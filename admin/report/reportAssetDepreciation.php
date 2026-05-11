<?php

include '../../_config.php';   
include '../../_include-v2.php';

includeClass('AssetDepreciation.class.php');
$assetDepreciation = createObjAndAddToCol(new AssetDepreciation());
$asset = createObjAndAddToCol(new Asset());
$warehouse = createObjAndAddToCol(new Warehouse());

include '_global.php';

$obj= $assetDepreciation;
$securityObject = 'reportAssetDepreciation'; // the value of security object is manually inserted to handle 
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
$arrDataStructure['code'] = array('title'=>ucwords($obj->lang['code']),  'width'=>"130px", 'dbfield' => 'code'); 
$arrDataStructure['date'] = array('title'=>ucwords($obj->lang['date']),'dbfield' => 'trdate', 'width'=>"120px",'format'=>'date');
$arrDataStructure['warehouse'] = array('title'=>ucwords($obj->lang['warehouse']),'dbfield' => 'warehousename', 'width'=>"150px");
$arrDataStructure['total'] = array('title'=>ucwords($obj->lang['depreciation']),'dbfield' => 'grandtotal','align'=>'right', 'width'=>"130px",'format'=>'number','calculateTotal' => true); 
$arrDataStructure['status'] = array('title'=>ucwords($obj->lang['status']),'dbfield' => 'statusname', 'width'=>"80px");

$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $obj->lang['assetDepreciationReport']; 
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();

array_push($arrTemplate, $arrHeaderTemplate);
if ($isShowDetail){ 
	// detail ... 
$arrDataDetailStructure = array(); 
$arrDataDetailStructure['assetCode'] = array('title'=>ucwords($obj->lang['code']),  'dbfield' => 'assetcode', 'width'=>"100px", 'mergeExcelCell' => 2 );  
$arrDataDetailStructure['assetName'] = array('title'=>ucwords($obj->lang['name']),  'dbfield' => 'assetname', 'width'=>"200px");  
$arrDataDetailStructure['amount'] = array('title'=>ucwords($obj->lang['depreciation']),'dbfield' => 'value', 'width'=>"130px",'calculateTotal' => true, 'format'=>'decimal');

$arrDetailTemplate = array();
$arrDetailTemplate['reportWidth'] = "1040px";
$arrDetailTemplate['dataStructure'] = $arrDataDetailStructure;
$arrDetailTemplate['total'] = array();

array_push($arrTemplate, $arrDetailTemplate); 
}

if (isset($_POST) && !empty($_POST['hidAction'])){  

	$criteria = '';
	$criteriaArr = array();

	array_push($criteriaArr, array('postVariable' => 'assetDepreciationCode', 
								   'fieldName' => $obj->tableName.'.code', 
								   'label' => $obj->lang['code']));
	
	
	array_push($criteriaArr, array('postVariable' => array('trStartDate', 'trEndDate'), 
								   'fieldName' => $obj->tableName.'.trdate', 
								   'label' =>  $obj->lang['date'], 
								   'type' => 'daterange'));


	array_push($criteriaArr, array('postVariable' => 'selWarehouse', 
								   'fieldName' => $obj->tableName.'.warehousekey', 
								   'label' => $obj->lang['warehouse'], 
								   'useArrayKey' => array('obj' => $warehouse) ));
	

	array_push($criteriaArr, array('postVariable' => 'selStatus',
								   'type' => 'status'));
	
	
	$obj->createReportCriteria($criteria,$arrFilterInformation,$criteriaArr);
	

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

$arrWarehouse = $class->convertForCombobox($warehouse->searchData($warehouse->tableName.'.statuskey',1,true,'','order by name asc'),'pkey','name');$arrAsset = $class->convertForCombobox($asset->searchData($asset->tableName.'.statuskey',1,true, ' order by name asc'),'pkey','name');   $arrStatus = $class->convertForCombobox($obj->getAllStatus(),'pkey','status');   


$arrTwigVar['inputAssetDepreciationCode'] =  $class->inputText('assetDepreciationCode');
$arrTwigVar['inputSelStatus'] =  $class->inputSelect('selStatus[]', $arrStatus, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputStartDate'] = $class->inputDate('trStartDate', array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputEndDate'] = $class->inputDate('trEndDate', array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputIsShowDetail'] =  $class->inputCheckBox('isShowDetail');
$arrTwigVar['inputSelWarehouse'] =  $class->inputSelect('selWarehouse[]', $arrWarehouse, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox')); 
$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate;      
echo $twig->render('reportAssetDepreciation.html', $arrTwigVar);  

?>
