<?php
	 
include '../../_config.php';  
include '../../_include-v2.php';

includeClass(array('EMKLPurchaseOrder.class.php'));
$emklPurchaseOrderWarehouse = createObjAndAddToCol(new EMKLPurchaseOrder(EMKL['jobType']['warehouse']));
$currency = createObjAndAddToCol(new Currency()); 
$warehouse = createObjAndAddToCol(new Warehouse()); 
$supplier = createObjAndAddToCol(new Supplier()); 
$container = createObjAndAddToCol(new Container());
$service = createObjAndAddToCol(new Service(SERVICE));

include '_global.php';

$obj = $emklPurchaseOrderWarehouse;
$securityObject = 'reportPurchaseOrderWarehouseFF'; // the value of security object is manually inserted to handle 
								  // some modules that have different security object from that of their class
								  
if(!$security->isAdminLogin($securityObject,10,true));  

$arrFilterInformation = array();
$detailCriteria = '';
$_POST['selStatus[]'] = array(2,3);

if (!isset($_POST['trStartDate']) || empty($_POST['trStartDate'])) {
	$_POST['trStartDate'] = date('d / m / Y');
	$_POST['trEndDate'] = date('d / m / Y');
	$_POST['isGrouping'] = true;
}

$arrDateType= array(
    '1' => $obj->lang['transactionDate'],
    '2' => 'ETD',
    '3' => 'ETA'
);   
$defaultCurrencyKey = $currency->getDefaultData();
$rsCurrency = $currency->getDataRowById($defaultCurrencyKey);
// ===== FOR EXPORT SECTION
$dataToExport = array();

/* data structure */
$arrTemplate = array();
$isShowDetail = (isset($_POST['isShowDetail']) && !empty($_POST['isShowDetail'])) ? true : false;
$isGrouping = (isset($_POST['isGrouping']) && !empty($_POST['isGrouping'])) ? true : false;

// overwrite
if(!$isGrouping) $isShowDetail = false;
	
$arrDataStructure = array();
$arrDataStructure['rowNumber'] = array('title'=>'#', 'align'=>'right', 'width'=>"40px", 'autoNumber' => true, "sortable" => false);
$arrDataStructure['code'] = array('title'=>ucwords($obj->lang['code']),  'width'=>"150px", 'dbfield' => 'code'); 
$arrDataStructure['date'] = array('title'=>ucwords($obj->lang['date']),'dbfield' => 'trdate', 'width'=>"90px",'format'=>'date');
$arrDataStructure['warehouse'] = array('title'=>ucwords($obj->lang['warehouse']),'dbfield' => 'warehousename', 'width'=>"150px" );
$arrDataStructure['invoiceReference'] = array('title'=>ucwords($obj->lang['invoiceReference']),'dbfield' => 'refinvoicecode', 'width'=>"150px" );
$arrDataStructure['JOCode'] = array('title'=>ucwords($obj->lang['JOCode']),'dbfield' => 'jocode', 'width'=>"150px" );
$arrDataStructure['Shipper'] = array('title'=>ucwords($obj->lang['shipper']),'dbfield' => 'shippername', 'width'=>"150px" );
$arrDataStructure['containertype'] = array('title'=>ucwords($obj->lang['type']),'dbfield' => 'containertype', 'width'=>"60px" );
$arrDataStructure['etd'] = array('title'=>ucwords($obj->lang['etd']),'dbfield' => 'etdpol', 'width'=>"90px",'format'=>'date');
$arrDataStructure['eta'] = array('title'=>ucwords($obj->lang['eta']),'dbfield' => 'etapod', 'width'=>"90px",'format'=>'date');
$arrDataStructure['pol'] = array('title'=>ucwords($obj->lang['pol']),'dbfield' => 'polname', 'width'=>"150px" );
$arrDataStructure['pod'] = array('title'=>ucwords($obj->lang['pod']),'dbfield' => 'podname', 'width'=>"150px" );
$arrDataStructure['supplier'] = array('title'=>ucwords($obj->lang['supplier']),'dbfield' => 'suppliername', 'width'=>"300px" );

if (!$isGrouping){
	$arrDataStructure['container'] = array('title' => ucwords($obj->lang['container']), 'dbfield' => 'containername', 'width' => "100px");
	$arrDataStructure['serviceName'] = array('title' => ucwords($obj->lang['serviceName']), 'dbfield' => 'servicename', 'width' => "150px", 'mergeExcelCell' => 2);
	$arrDataStructure['qty'] = array('title' => ucwords($obj->lang['qty']), 'dbfield' => 'qty', 'width' => "80px", 'format' => 'autodecimal');
	$arrDataStructure['priceInUnit'] = array('title' => ucwords($obj->lang['price']), 'dbfield' => 'priceinunit', 'width' => "100px", 'format' => 'autodecimal');
	$arrDataStructure['currency'] = array('title' => $obj->lang['curr'], 'dbfield' => 'currencyname', 'align' => 'center', 'width' => "60px");
	$arrDataStructure['rate'] = array('title' => $obj->lang['rate'], 'dbfield' => 'rate', 'width' => "60px", 'format' => 'autodecimal');
	$arrDataStructure['subtotal'] = array('title' => ucwords($obj->lang['total']), 'dbfield' => 'subtotalcurrency', 'width' => "120px", 'format' => 'autodecimal');
	$arrDataStructure['total'] = array('title' => ucwords($obj->lang['total']) . ' ' . $rsCurrency[0]['name'], 'dbfield' => 'subtotal', 'width' => "120px", 'format' => 'number', 'calculateTotal' => true);
} else {
	$arrDataStructure['currency'] = array('title' => ucwords($obj->lang['curr']), 'dbfield' => 'currencyname', 'width' => "60px");
	$arrDataStructure['subtotal'] = array('title' => ucwords($obj->lang['subtotal']), 'dbfield' => 'subtotal', 'align' => 'right', 'width' => "100px", 'format' => 'autodecimal');
	$arrDataStructure['tax'] = array('title' => ucwords($obj->lang['tax']), 'dbfield' => 'taxvalue', 'align' => 'right', 'width' => "100px", 'format' => 'autodecimal');
	$arrDataStructure['total'] = array('title' => ucwords($obj->lang['total']), 'dbfield' => 'grandtotal', 'align' => 'right', 'width' => "100px", 'format' => 'autodecimal');
}
$arrDataStructure['status'] = array('title' => ucwords($obj->lang['status']), 'dbfield' => 'statusname', 'width' => "100px");

$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $obj->lang['purchaseOrderWarehouseReport']; 
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();

array_push($arrTemplate, $arrHeaderTemplate);

if ($isShowDetail){ 
// detail ...
$arrDataDetailStructure = array(); 
$arrDataDetailStructure['container'] = array('title'=>ucwords($obj->lang['container']),  'dbfield' => 'containername', 'width'=>"100px" );  
$arrDataDetailStructure['qty'] = array('title'=>ucwords($obj->lang['qty']),  'dbfield' => 'qty', 'width'=>"100px" , 'format' => 'autodecimal'); 
$arrDataDetailStructure['serviceName'] = array('title'=>ucwords($obj->lang['serviceName']),  'dbfield' => 'servicename', 'width'=>"200px", 'mergeExcelCell' => 2 );  
$arrDataDetailStructure['priceInUnit'] = array('title'=>ucwords($obj->lang['price']),'dbfield' => 'priceinunit', 'width'=>"80px",'format'=>'autodecimal');
$arrDataDetailStructure['currency'] = array('title'=>$obj->lang['curr'],  'dbfield' => 'currencyname', 'align'=>'center', 'width'=>"60px" ); 
$arrDataDetailStructure['rate'] = array('title'=>$obj->lang['rate'],  'dbfield' => 'rate', 'width'=>"60px" , 'format' => 'autodecimal' ); 
$arrDataDetailStructure['subtotal'] = array('title'=>ucwords($obj->lang['total']),'dbfield' => 'subtotalcurrency', 'width'=>"100px",'format'=>'autodecimal');
$arrDataDetailStructure['total'] = array('title'=>ucwords($obj->lang['total']). ' '. $rsCurrency[0]['name'], 'dbfield' => 'subtotal', 'width'=>"100px",'format'=>'number','calculateTotal' => true);
  
$arrDetailTemplate = array();
$arrDetailTemplate['reportWidth'] = "750px";
$arrDetailTemplate['dataStructure'] = $arrDataDetailStructure;
$arrDetailTemplate['total'] = array();

array_push($arrTemplate, $arrDetailTemplate); 
}

$arrWarehouse = $class->convertForCombobox($warehouse->searchData($warehouse->tableName . '.statuskey', 1, true, '', 'order by name asc'), 'pkey', 'name');
$arrStatus = $class->convertForCombobox($obj->getAllStatus(), 'pkey', 'status');
$arrCurrency = $class->convertForCombobox($currency->searchData($currency->tableName . '.statuskey', 1, true, '', 'order by name asc'), 'pkey', 'name');
$arrSupplier = $class->convertForCombobox($supplier->searchData($supplier->tableName . '.statuskey', 1, true, '', 'order by name asc'), 'pkey', 'name');
$arrContainer = $class->convertForCombobox($container->getContainerType(), 'pkey', 'name');
$arrService = $class->convertForCombobox($service->searchData('', '', true, ' and ' . $service->tableName . '.statuskey = 1 and ' . $service->tableName . '.itemtype = 3 order by name asc'), 'pkey', 'name');

$arrTwigVar['inputSalesCode'] = $class->inputText('salesCode');
$arrTwigVar['inputSelWarehouse'] = $class->inputSelect('selWarehouse[]', $arrWarehouse, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputSelSupplier'] = $class->inputSelect('selSupplier[]', $arrSupplier, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputSelContainer'] = $class->inputSelect('selContainer[]', $arrContainer, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
//$arrTwigVar['inputSupplierKey'] =  $class->inputHidden('hidSupplierKey');
//$arrTwigVar['inputSupplierName'] =  $class->inputText('supplierName');
$arrTwigVar['inputSelDateType'] = $class->inputSelect('selDateType', $arrDateType);
$arrTwigVar['inputSelCurrency'] = $class->inputSelect('selCurrency[]', $arrCurrency, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputSelStatus'] = $class->inputSelect('selStatus[]', $arrStatus, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputStartDate'] = $class->inputDate('trStartDate', array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputEndDate'] = $class->inputDate('trEndDate', array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputIsShowDetail'] = $class->inputCheckBox('isShowDetail', array('add-class' => 'choose-one-opt'));
$arrTwigVar['inputIsGrouping'] = $class->inputCheckBox('isGrouping', array('add-class' => 'choose-one-opt'));
$arrTwigVar['inputSelService'] = $class->inputSelect('selService[]', $arrService, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['arrTemplate'] = $arrHeaderTemplate;

if (isset($_POST) && !empty($_POST['hidAction'])){  
		
	$criteria = '';
	if(isset($_POST) && !empty($_POST['salesCode'])) {
		$criteria .= ' AND '.$obj->tableName.'.code LIKE ('.$class->oDbCon->paramString('%'.$_POST['salesCode'].'%').')';
		array_push($arrFilterInformation,array("label" => 'Kode', 'filter' => $_POST['salesCode']));
	}
    if(isset($_POST) && !empty($_POST['trStartDate'])){
        
        switch($_POST['selDateType']){
            case '1' : $fieldName = $obj->tableName.'.trdate';  break;
            case '2' : $fieldName = $obj->tableJobOrder.'.etdpol'; break;
            case '3' : $fieldName = $obj->tableJobOrder.'.etapod'; break;
            default : $fieldName = $obj->tableName.'.trdate';  break;
                
        }
        
		$criteria .= ' and '.$fieldName.' between '.$class->oDbCon->paramDate( $_POST['trStartDate'],' / ').' AND '.$class->oDbCon->paramDate( $_POST['trEndDate'],' / '); 
		array_push($arrFilterInformation,array("label" => $arrDateType[$_POST['selDateType']], 'filter' => $_POST['trStartDate'] . ' - ' .$_POST['trEndDate'] ));
	}
    
 if(isset($_POST) && !empty($_POST['selWarehouse'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selWarehouse']));   
        
       	$criteria .= ' AND '.$obj->tableName.'.warehousekey in('.$key.')';  

        $rsCriteria = $warehouse->searchData('','',true, ' and '.$warehouse->tableName.'.pkey in ('.$key.')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$statusName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => $obj->lang['warehouse'], 'filter' => $statusName ));
        
	}
    
    if(isset($_POST) && !empty($_POST['selSupplier'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selSupplier']));   
       	$criteria .= ' AND '.$obj->tableName.'.supplierkey in('.$key.')';  

        $rsCriteria = $supplier->searchData('','',true, ' and '.$supplier->tableName.'.pkey in ('.$key.')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$supplierName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" =>$obj->lang['supplier'], 'filter' => $supplierName ));
        
	}
    
//    if(isset($_POST) && !empty($_POST['supplierName'])) { 
//        $criteria .= ' AND '.$obj->tableSupplier.'.name LIKE ('.$class->oDbCon->paramString('%'.$_POST['supplierName'].'%').')';
//	    array_push($arrFilterInformation,array("label" => 'Supplier', 'filter' => $_POST['supplierName']));
//	}      
    
    if(isset($_POST) && !empty($_POST['selCurrency'])) { 
        
            
        $key = implode(",", $class->oDbCon->paramString($_POST['selCurrency']));   

       	$criteria .= ' AND '.$obj->tableName.'.currencykey in ('.$key.')';  

        $rsCriteria = $currency->searchData('','',true, ' and '.$currency->tableName.'.pkey in ('.$key.')');;
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$currencyName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" =>$obj->lang['currency'], 'filter' => $currencyName ));
        
	}

	if(!$isGrouping) {
		if (isset($_POST) && !empty($_POST['selService'])) {

			$key = implode(",", $class->oDbCon->paramString($_POST['selService']));
			$criteria .= ' AND ' . $obj->tableNameDetail . '.servicekey in(' . $key . ')';

			$rsCriteria = $service->searchData('', '', true, ' and ' . $service->tableName . '.pkey in (' . $key . ')');

			$arrTempStatus = array();
			for ($k = 0; $k < count($rsCriteria); $k++)
				array_push($arrTempStatus, $rsCriteria[$k]['name']);

			$serviceName = implode(", ", $arrTempStatus);
			array_push($arrFilterInformation, array("label" => $obj->lang['service'], 'filter' => $serviceName));

		}
	}

    if(isset($_POST) && !empty($_POST['selContainer'])) { 
        
        $key = $_POST['selContainer'];  
         
         //karena jo ada 2 macam jo header dan jo biasa
       	$criteria .= ' AND IF('.$obj->tableJobOrder.'.containertypekey IS NULL OR  '.$obj->tableJobOrder.'.containertypekey = \'\' ,'.$obj->tableJobOrderHeader.'.containertypekey ,'.$obj->tableJobOrder.'.containertypekey  ) in ('.$class->oDbCon->paramString($key,',').')';  

        $rsCriteria = $container->getContainerType($key);
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$statusName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" =>$obj->lang['containerType'], 'filter' => $statusName ));
        
	} 

	if(isset($_POST) && !empty($_POST['selStatus'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selStatus']));   
        
       	$criteria .= ' AND '.$obj->tableName.'.statuskey in('.$key.')';  

        $rsCriteria =  $obj->getStatusById ($key);
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['status']);
			
		$statusName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => 'Status', 'filter' => $statusName));
        
	}
		 
		 
    $orderBy = (!empty($_POST['hidOrderBy'])) ? $obj->oDbCon->paramOrder($_POST['hidOrderBy']) : 'pkey'; // order by harus dr kolom yg terdaftar saja
    $orderType = (isset($_POST['hidOrderType']) && !empty($_POST['hidOrderType']) && $_POST['hidOrderType'] == 1) ? 'desc' : 'asc';
      
		   
	$order = 'order by '.$orderBy.' ' .$orderType; 
	//$rs = $obj->searchData('','',true,$criteria,$order);

	$rs = ($isGrouping) ? $obj->searchData('', '', true, $criteria, $order) : $obj->generateDataForReportPurchaseOrder($criteria, $order);

	$totalCurr = count(array_unique(array_column($rs,'currencykey'))); 
    $calculateTotal = ($totalCurr <= 1) ? true : false;
    
    $tempreport = '';
    
    $rsDetailCol = ($isShowDetail) ? $obj->getDetailCollections($rs,'refkey',$detailCriteria) : array();
    
    $totalRs = count($rs);
    for( $i=0;$i<$totalRs;$i++) { 
		
        if($isShowDetail){ 
			$rsDetail = (isset($rsDetailCol[$rs[$i]['pkey']])) ? $rsDetailCol[$rs[$i]['pkey']] : array(); 

			for($j=0;$j<count($rsDetail);$j++){
				if ($rsDetail[$j]['currencykey'] == $defaultCurrencyKey)
					$rsDetail[$j]['rate'] = 1; 

				 $rsDetail[$j]['subtotal'] =  $rsDetail[$j]['subtotalcurrency'] * $rsDetail[$j]['rate'];  
			}


			// has detail
			$rs[$i]['_detail_'] = array('arrTemplate'=>$arrDetailTemplate,'data' => $rsDetail);
	    }
		
        $arrTemplate[0]['dataStructure']['total']['calculateTotal'] = $calculateTotal;

        $return = $obj->formatReportRows(array('data' => $rs[$i]),$arrTemplate); 

        // ===== FOR EXPORT SECTION 
        array_push($dataToExport, $return['data']);  
        // ===== END FOR EXPORT SECTION

        $tempreport .= $return['html'];  
        $arrTemplate[0]['total'] = $obj->arraySum($arrTemplate[0]['total'], $return['subtotal'][0]);
    }

	$tableHeader = $twig->render('template-header.html', $arrTwigVar);

    $obj->generateReport($_POST, $tempreport, $arrTemplate,$dataToExport,$arrFilterInformation, $tableHeader);
}

echo $twig->render('reportEMKLPurchaseOrder.html', $arrTwigVar); 

?>