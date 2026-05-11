<?php

include '../../_config.php';   
include '../../_include-v2.php';

includeClass('Assembly.class.php');
$assembly = createObjAndAddToCol(new Assembly());
$item = createObjAndAddToCol(new Item());
$billOfMaterials = createObjAndAddToCol(new BillOfMaterials());
$warehouse = createObjAndAddToCol(new Warehouse());

include '_global.php';

$obj= $assembly;
$securityObject = 'reportAssembly'; // the value of security object is manually inserted to handle 
											// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true)); 

$arrFilterInformation = array();    
$detailCriteria = '';
$dataToExport = array();
$_POST['selStatus[]'] = array(2,3);

/* data structure */
$arrTemplate = array();
$isShowDetail = (isset($_POST['isShowDetail']) && !empty($_POST['isShowDetail'])) ? true : false;

$arrDataStructure = array();
$arrDataStructure['rowNumber'] = array('title'=>'#', 'align'=>'right', 'width'=>"40px", 'autoNumber' => true, "sortable" => false);
$arrDataStructure['code'] = array('title'=>ucwords($obj->lang['code']), 'dbfield' => 'code', 'width'=>"100px");
$arrDataStructure['date'] = array('title'=>ucwords($obj->lang['date']),'dbfield' => 'trdate', 'width'=>"100px",'format'=>'date');
$arrDataStructure['billOfMaterials'] = array('title'=>ucwords($obj->lang['billOfMaterials']),'dbfield' => 'bomname', 'width'=>"150px");
$arrDataStructure['itemName'] = array('title'=>ucwords($obj->lang['item']),'dbfield' => 'itemname', 'width'=>"250px");
$arrDataStructure['qty'] = array('title'=>ucwords($obj->lang['qty']),'dbfield' => 'qty', 'width'=>"100px", 'format' => 'number');
$arrDataStructure['cost'] = array('title'=>ucwords($obj->lang['cost']),'dbfield' => 'cost', 'width'=>"100px", 'format' => 'number');
$arrDataStructure['sourceWarehouse'] = array('title'=>ucwords($obj->lang['sourceWarehouse']),'dbfield' => 'warehousefromname', 'width'=>"150px");
$arrDataStructure['destinationWarehouse'] = array('title'=>ucwords($obj->lang['destinationWarehouse']),'dbfield' => 'warehousetoname', 'width'=>"150px");
$arrDataStructure['description'] = array('title'=>ucwords($obj->lang['description']),'dbfield' => 'trdesc', 'width'=>"350px");
$arrDataStructure['status'] = array('title'=>ucwords($obj->lang['status']),'dbfield' => 'statusname', 'width'=>"100px");

$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $obj->lang['assemblyReport']; 
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();

array_push($arrTemplate, $arrHeaderTemplate);


if ($isShowDetail){ 
// detail ... 
$arrDataDetailStructure = array(); 
//$arrDataDetailStructure['itemCode'] = array('title'=>ucwords($obj->lang['itemCode']),  'dbfield' => 'itemcode', 'width'=>"100px", 'mergeExcelCell' => 2 );  
$arrDataDetailStructure['itemName'] = array('title'=>ucwords($obj->lang['itemName']),  'dbfield' => 'itemname', 'width'=>"200px");  
$arrDataDetailStructure['qtyBom'] = array('title'=>ucwords($obj->lang['qtyBom']),'dbfield' => 'qtybom', 'width'=>"100px",'calculateTotal' => true, 'format'=>'decimal');
$arrDataDetailStructure['qtyUsed'] = array('title'=>ucwords($obj->lang['qtyUsed']),'dbfield' => 'qtyused', 'width'=>"100px",'calculateTotal' => true, 'format'=>'decimal');

$arrDetailTemplate = array();
$arrDetailTemplate['reportWidth'] = "900px";
$arrDetailTemplate['dataStructure'] = $arrDataDetailStructure;
$arrDetailTemplate['total'] = array();

array_push($arrTemplate, $arrDetailTemplate); 
}

if (isset($_POST) && !empty($_POST['hidAction'])){  

	$criteria = '';
	$criteriaArr = array();
	
	array_push($criteriaArr, array('postVariable' => 'assemblyCode', 
								   'fieldName' => $obj->tableName.'.code', 
								   'label' => $obj->lang['code']));
	
	array_push($criteriaArr, array('postVariable' => array('trStartDate', 'trEndDate'), 
								   'fieldName' => $obj->tableName.'.trdate', 
								   'label' =>  $obj->lang['date'], 
								   'type' => 'daterange'));
	
	array_push($criteriaArr, array('postVariable' => 'selBillOfMaterials', 
								   'fieldName' => $obj->tableName.'.bomkey', 
								   'label' => $obj->lang['billOfMaterials'], 
								   'useArrayKey' => array('obj' => $billOfMaterials) ));		
	
	array_push($criteriaArr, array('postVariable' => 'selItem', 
								   'fieldName' => $obj->tableName.'.itemkey', 
								   'label' => $obj->lang['item'], 
								   'useArrayKey' => array('obj' => $item) ));	

	array_push($criteriaArr, array('postVariable' => 'selWarehouse', 
								   'fieldName' => $obj->tableName.'.warehousefromkey', 
								   'label' => $obj->lang['rawItemWarehouse'], 
								   'useArrayKey' => array('obj' => $warehouse) ));
	
	array_push($criteriaArr, array('postVariable' => 'selWarehouseTo', 
								   'fieldName' => $obj->tableName.'.warehousetokey', 
								   'label' => $obj->lang['resultItemWarehouse'], 
								   'useArrayKey' => array('obj' => $warehouse) ));	
	
	array_push($criteriaArr, array('postVariable' => 'selStatus',
								   'type' => 'status'));

	
	$obj->createReportCriteria($criteria,$arrFilterInformation,$criteriaArr);		
	
    //search detail
	/*if(isset($_POST) && !empty($_POST['itemName'])) { 
        $detailCriteria .= ' AND '.$obj->tableItem.'.name LIKE ('.$class->oDbCon->paramString('%'.$_POST['itemName'].'%').')';
	    array_push($arrFilterInformation,array("label" => $obj->lang['item'], 'filter' => $_POST['itemName']));
	}*/
		

	$orderBy = (!empty($_POST['hidOrderBy'])) ? $obj->oDbCon->paramOrder($_POST['hidOrderBy']) : 'pkey'; // order by harus dr kolom yg terdaftar saja
	$orderType = (isset($_POST['hidOrderType']) && !empty($_POST['hidOrderType']) && $_POST['hidOrderType'] == 1) ? 'desc' : 'asc';

	$order = 'order by '.$orderBy.' ' .$orderType;
	$rs = $obj->searchData('','',true,$criteria,$order);

	$temp = 1;
	$tempreport = '';

	if (empty($rs))
		$tempreport .= '<tr class="report-row rewrite-row"><td colspan="'.count($arrHeaderTemplate['dataStructure']).'"></td></tr>';


	$rsDetailCol = ($isShowDetail) ? $obj->getDetailCollections($rs,'refkey',$detailCriteria) : array();

	$totalRs = count($rs);
	for( $i=0;$i<$totalRs;$i++) {   



		if($isShowDetail){ 

			if (!isset($rsDetailCol[$rs[$i]['pkey']])) continue;

			$rsDetail = $rsDetailCol[$rs[$i]['pkey']]; 


			$rs[$i]['_detail_'] = array('arrTemplate'=>$arrDetailTemplate,'data' => $rsDetail); 

		}


		$return = $obj->formatReportRows(array('data' => $rs[$i]),$arrTemplate); 

				// ===== FOR EXPORT SECTION 
		array_push($dataToExport, $return['data']);  
		// ===== END FOR EXPORT SECTION

		$tempreport .= $return['html'];
		$arrTemplate[0]['total'] = $obj->arraySum($arrTemplate[0]['total'], $return['subtotal'][0]);

	}

	$obj->generateReport($_POST, $tempreport, $arrTemplate,$dataToExport,$arrFilterInformation);
	
	}else{ 
		
	$_POST['trStartDate'] = date('d / m / Y');	
	$_POST['trEndDate'] = date('d / m / Y');
} 

$arrWarehouse = $class->convertForCombobox($warehouse->searchData($warehouse->tableName.'.statuskey',1,true,'','order by name asc'),'pkey','name');
$arrItem = $class->convertForCombobox($item->searchData($item->tableName.'.statuskey',1,true,'','order by name asc'),'pkey','name');
$arrBOM = $class->convertForCombobox($billOfMaterials->searchData($billOfMaterials->tableName.'.statuskey',1,true,'','order by name asc'),'pkey','name');
$arrStatus = $class->convertForCombobox($obj->getAllStatus(),'pkey','status');   


$arrTwigVar['inputAssemblyCode'] =  $class->inputText('assemblyCode');
$arrTwigVar['inputSelWarehouse'] =  $class->inputSelect('selWarehouse[]', $arrWarehouse, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox')); 
$arrTwigVar['inputSelWarehouseTo'] =  $class->inputSelect('selWarehouseTo[]', $arrWarehouse, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));  
/*$arrTwigVar['inputHidItemKey'] = $class->inputHidden('hidItemKey');
$arrTwigVar['inputItemName'] =  $class->inputText('itemName'); */
$arrTwigVar['inputSelBOM'] =  $class->inputSelect('selBillOfMaterials[]', $arrBOM, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputSelItem'] =  $class->inputSelect('selItem[]', $arrItem, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputSelStatus'] =  $class->inputSelect('selStatus[]', $arrStatus, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputStartDate'] = $class->inputDate('trStartDate', array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputEndDate'] = $class->inputDate('trEndDate', array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputIsShowDetail'] =  $class->inputCheckBox('isShowDetail');
$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate;      
echo $twig->render('reportAssembly.html', $arrTwigVar);  

?>
