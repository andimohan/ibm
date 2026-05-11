<?php
	 
include '../../_config.php';  
include '../../_include-v2.php';

includeClass(array('TruckingServiceOrder.class.php'));
$truckingServiceOrder = createObjAndAddToCol(new TruckingServiceOrder()); 

$customer = createObjAndAddToCol(new Customer());    
$employee = createObjAndAddToCol(new Employee());    
$truckingServiceOrderCategory = createObjAndAddToCol(new TruckingServiceOrderCategory());    
$warehouse = createObjAndAddToCol(new Warehouse());   
$truckingService = createObjAndAddToCol(new Service());
//$truckingPurchaseRefund =  createObjAndAddToCol(new TruckingPurchaseRefund());

include '_global.php';

$obj= $truckingServiceOrder;
$securityObject = 'reportTruckingServiceOrder'; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true));  
  
$arrFilterInformation = array();  
$_POST['selStatus[]'] = array(2,3,4,5,6);

// isShowDetail sementara false saja dulu
if(!isset($_POST['isShowDetail']))  $_POST['isShowDetail'] = 0;  
$isShowDetail = (isset($_POST['isShowDetail']) && !empty($_POST['isShowDetail'])) ? true : false;  
if(!isset($_POST['selDateType']) || empty($_POST['selDateType']))
    $_POST['selDateType'] = 1;

$arrDateType= array(
    '1' => $obj->lang['transactionDate'],
    '2' => $obj->lang['stuffingAndDestuffingDateTime']
);
   
//$hasPurchaseRefundAccess = $security->hasSecurityAccess( $obj->userkey ,$security->getSecurityKey($truckingPurchaseRefund->securityObject),10);

// ===== FOR EXPORT SECTION
$dataToExport = array();

/* data structure */
$arrTemplate = array();

$arrDataStructure = array();
$arrDataStructure['rowNumber'] = array('title'=>'#', 'align'=>'right', 'width'=>"40px", 'autoNumber' => true, "sortable" => false);
$arrDataStructure['code'] = array('title'=>ucwords($obj->lang['code']),  'width'=>"150px", 'dbfield' => 'code'); 
$arrDataStructure['date'] = array('title'=>ucwords($obj->lang['date']),'dbfield' => 'trdate', 'width'=>"90px",'format'=>'date');
$arrDataStructure['stuffingDate'] = array('title'=>ucwords($obj->lang['stuffingDate']),'dbfield' => 'lastwodate', 'width'=>"90px",'format'=>'date','returnOnEmpty' => array('returnOnEmpty' => true, 'value' => ''));
$arrDataStructure['warehouse'] = array('title'=>ucwords($obj->lang['warehouse']),'dbfield' => 'warehousename', 'width'=>"100px");
$arrDataStructure['shipmentNumber'] = array('title'=>ucwords($obj->lang['bookingNumber']),'dbfield' => 'shipmentnumber', 'width'=>"150px");
$arrDataStructure['customerName'] = array('title'=>ucwords($obj->lang['customer']),'dbfield' => 'customername', 'width'=>"200px", 'mergeExcelCell' => 2);

$arrDataStructure['category'] = array('title'=>ucwords($obj->lang['category']),'dbfield' => 'categoryname', 'width'=>"150px");
$arrDataStructure['volume20'] = array('title'=>'20\'','dbfield' => 'volume20', 'width'=>"60px",'format'=>'number','calculateTotal' => true, "sortable" => false);
$arrDataStructure['volume40'] = array('title'=>'40\'','dbfield' => 'volume40', 'width'=>"60px",'format'=>'number','calculateTotal' => true, "sortable" => false);
$arrDataStructure['volume45'] = array('title'=>'45\'','dbfield' => 'volume45', 'width'=>"60px",'format'=>'number','calculateTotal' => true, "sortable" => false);
$arrDataStructure['consigneeName'] = array('title'=>ucwords($obj->lang['consignee']),'dbfield' => 'consigneename', 'width'=>"200px", 'mergeExcelCell' => 2);
$arrDataStructure['totalTrucking'] = array('title'=>ucwords($obj->lang['trucking']),'dbfield' => 'subtotal','align'=>'right', 'width'=>"110px",'format'=>'number', 'textColor' => '568203','calculateTotal' => true); 
// $arrDataStructure['purchaseRefund'] = array('title'=>ucwords($obj->lang['purchaseRefund']),'dbfield' => 'totalrefund','align'=>'right', 'width'=>"160px",'format'=>'number','calculateTotal' => true); 
//$arrDataStructure['total'] = array('title'=>ucwords($obj->lang['totalSales']),'dbfield' => 'grandtotal','align'=>'right', 'width'=>"110px",'format'=>'number','calculateTotal' => true); 
//$arrDataStructure['totalCost'] = array('title'=>ucwords($obj->lang['totalCost']),'dbfield' => 'totalcost','align'=>'right', 'width'=>"110px",'format'=>'number','calculateTotal' => true); 
$arrDataStructure['grossProfit'] = array('title'=>ucwords($obj->lang['grossProfit']),'dbfield' => 'grossprofit','align'=>'right', 'width'=>"110px",'format'=>'number','calculateTotal' => true); 
$arrDataStructure['invoiceNumber'] = array('title'=>ucwords($obj->lang['invoiceNumber']),'dbfield' => 'invoicenumber', 'width'=>"150px", "sortable" => false);
$arrDataStructure['note'] = array('title'=>ucwords($obj->lang['note']), 'width'=>"300px",'dbfield' => 'trdesc');
$arrDataStructure['status'] = array('title'=>ucwords($obj->lang['status']),'dbfield' => 'statusname', 'width'=>"100px");
		   
$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $obj->lang['grossPLReport']; 
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();

array_push($arrTemplate, $arrHeaderTemplate);
if ($isShowDetail){
// detail ...
$arrDataDetailStructure = array(); 
$arrDataDetailStructure['party'] = array('title'=>ucwords($obj->lang['party']),  'dbfield' => 'qtyinbaseunit', 'width'=>"40px" , 'format' => 'number' ,'calculateTotal' => true ); 
$arrDataDetailStructure['itemName'] = array('title'=>ucwords($obj->lang['service']),  'dbfield' => 'itemname', 'width'=>"100px" );  
$arrDataDetailStructure['date'] = array('title'=>ucwords($obj->lang['date']),'dbfield' => 'trdate', 'width'=>"120px",'format'=>'datetime');
$arrDataDetailStructure['priceInUnit'] = array('title'=>ucwords($obj->lang['price']),'dbfield' => 'priceinunit', 'width'=>"80px",'format'=>'number');
$arrDataDetailStructure['total'] = array('title'=>ucwords($obj->lang['total']),'dbfield' => 'total', 'width'=>"80px",'format'=>'number');
$arrDataDetailStructure['statusName'] = array('title'=>ucwords($obj->lang['status']),'dbfield' => 'statusname', 'width'=>"100px");
$arrDataDetailStructure['note'] = array('title'=>ucwords($obj->lang['note']),'dbfield' => 'trdesc', 'width'=>"200px", 'mergeExcelCell' => 3);
  
$arrDetailTemplate = array();
$arrDetailTemplate['reportWidth'] = "1000px";
$arrDetailTemplate['dataStructure'] = $arrDataDetailStructure;
$arrDetailTemplate['total'] = array();

array_push($arrTemplate, $arrDetailTemplate); 
}
  

if (isset($_POST) && !empty($_POST['hidAction'])){  
		
	$criteria = '';
	$criteriaArr = array();
	 
	array_push($criteriaArr, array('postVariable' => 'salesCode', 
								   'fieldName' => $obj->tableName.'.code', 
								   'label' => $obj->lang['code']));
	

	switch($_POST['selDateType']){
            case '1' : $fieldName = $obj->tableName.'.trdate';  break;
            case '2' : $fieldName = $obj->tableName.'.lastwodate'; break;
            default : $fieldName = $obj->tableName.'.trdate';  break; 
    }
	
	array_push($criteriaArr, array('postVariable' => array('trStartDate', 'trEndDate'), 
								   'fieldName' => $fieldName, 
								   'label' =>  $arrDateType[$_POST['selDateType']], 
								   'type' => 'daterange'));
	
	array_push($criteriaArr, array('postVariable' => 'selWarehouse', 
								   'fieldName' => $obj->tableName.'.warehousekey', 
								   'label' => $obj->lang['warehouse'], 
								   'useArrayKey' => array('obj' => $warehouse) ));
	
	array_push($criteriaArr, array('postVariable' => 'selCustomer', 
							   'fieldName' => $obj->tableName.'.customerkey', 
							   'label' => $obj->lang['customer'], 
							   'useArrayKey' => array('obj' => $customer) ));
	
	array_push($criteriaArr, array('postVariable' => 'shipmentNumber', 
							   'fieldName' => $obj->tableName.'.shipmentnumber', 
							   'label' => 'No. Booking'));
	
	array_push($criteriaArr, array('postVariable' => 'consigneeName', 
							   'fieldName' => $obj->tableConsignee.'.name', 
							   'label' => $obj->lang['consignee']));
	
	array_push($criteriaArr, array('postVariable' => 'selCategory', 
							   'fieldName' => $obj->tableName.'.categorykey', 
							   'label' => $obj->lang['category'],
							   'useArrayKey' => array('obj' => $truckingServiceOrderCategory)));
	
	
	array_push($criteriaArr, array('postVariable' => 'selStatus',
								   'type' => 'status'));
	
	
	$obj->createReportCriteria($criteria,$arrFilterInformation,$criteriaArr);

	
		 
    $orderBy = (!empty($_POST['hidOrderBy'])) ? $obj->oDbCon->paramOrder($_POST['hidOrderBy']) : 'pkey'; // order by harus dr kolom yg terdaftar saja
    $orderType = (isset($_POST['hidOrderType']) && !empty($_POST['hidOrderType']) && $_POST['hidOrderType'] == 1) ? 'desc' : 'asc';
    
	$rsService = $truckingService->searchDataRow(array($truckingService->tableName.'.pkey' ,$truckingService->tableName.'.volume',$truckingService->tableName.'.qty'),
                                                 ' and '.$truckingService->tableName.'.servicecost = 0 
                                                   and '.$truckingService->tableName.'.itemtype = 2'
                                                );
    
    $rsService = array_column($rsService,null,'pkey');	
     
	$order = 'order by '.$orderBy.' ' .$orderType; 
	$rs = $obj->searchData('','',true,$criteria,$order);
	
	
    $tempreport = '';
	 
    if (empty($rs)) 
		$tempreport .= '<tr class="report-row rewrite-row"><td colspan="'.count($arrHeaderTemplate['dataStructure']).'"></td></tr>';
 
    $rsDetailCol = $obj->getDetailCollections($rs,'refkey');
	
	$arrPkey = array_column($rs,'pkey');
    
    // party
    $rsPartyCol = $obj->getPartyDescription($arrPkey);
    
    // invoice
    $rsInvoiceCol = $obj->getAmountInvoiced($arrPkey);
    $rsInvoiceCol = $obj->reindexDetailCollections($rsInvoiceCol,'salesorderkey');
 
	// sales by category 
	$rsSalesByCategory = $obj->getSellingCostDetail($arrPkey,'','order by categoryname asc');  
	$rsSalesByCategoryCol = $obj->reindexDetailCollections($rsSalesByCategory,'refkey');
	$arrItemCategory = array_unique(array_column($rsSalesByCategory,'categoryname')); // utk dpt unique index aj
	
	$totalSellingItemCategory = count($arrItemCategory); // biar tau dibawwah mau ditaro di kolom ke berapa utk cost nya
	
	$arrTempStructure = array(); 
    foreach($arrItemCategory as $row) {   
		$arrStructureIndex = 'mnv-selling-'.$row;
		$arrTempStructure[$arrStructureIndex] = array('title'=> $row,'dbfield' => $arrStructureIndex, 'width'=>"100px",'format'=>'number','sortable' => false,'calculateTotal' => true, 'textColor' => '568203');  
	}
	$arrTempStructure['total'] = array('title'=>ucwords($obj->lang['totalSales']),'dbfield' => 'grandtotal','align'=>'right', 'width'=>"110px",'sortable' => false,'format'=>'number','calculateTotal' => true); 
	
	
	// cost outource
	$rsOutsourceCost = $obj->getWorkOrderOutsourceCost($arrPkey);
	$rsOutsourceCostCol = $obj->reindexDetailCollections($rsOutsourceCost,'refkey'); 
	
	// cost by category
	$rsCostByCategory = $obj->getHeaderCost($arrPkey,'','order by categoryname asc');   
	$rsCostByCategoryCol = $obj->reindexDetailCollections($rsCostByCategory,'refkey');
	$arrCostCategory = array_unique(array_column($rsCostByCategory,'categoryname')); // utk dpt unique index aj
	
	// cost by spk 
	// harus digabung dengan cost di header agar urut namany dan totalnya
	$rsSPKCostByCategory = $obj->getWorkOrderCost($arrPkey,'','order by categoryname asc');
	$rsSPKCostCol = $obj->reindexDetailCollections($rsSPKCostByCategory,'refkey');
	$arrSPKCostCategory = array_unique(array_column($rsSPKCostByCategory,'categoryname')); // utk dpt unique index aj
	 
	$arrCostCategory =  array_merge($arrCostCategory,$arrSPKCostCategory);
	sort($arrCostCategory);
	
	$arrTempStructure['totalOutsourceTrucking'] = array('title'=>ucwords($obj->lang['trucking']),'dbfield' => 'totaloutsourcetrucking','align'=>'right', 'width'=>"110px",'sortable' => false,'format'=>'number','calculateTotal' => true,'textColor' => '0093AF'); 
	
    foreach($arrCostCategory as $row) {   
		$arrStructureIndex = 'mnv-cost-'.$row;
		$arrTempStructure[$arrStructureIndex] = array('title'=> $row,'dbfield' => $arrStructureIndex, 'width'=>"100px",'format'=>'number','sortable' => false,'calculateTotal' => true, 'textColor' => '0093AF');  
	}
	
	// asumsi kalo boleh lihat PnL sudah pasti boleh lihat komisi, kal ogk nanti total biayanya akan beda
	$arrTempStructure['purchaseRefund'] = array('title'=>ucwords($obj->lang['purchaseRefund']),'dbfield' => 'totalrefund','align'=>'right', 'width'=>"160px",'format'=>'number','calculateTotal' => true);

	$arrTempStructure['totalCost'] = array('title'=>ucwords($obj->lang['totalCost']),'dbfield' => 'totalcost','align'=>'right', 'width'=>"110px",'sortable' => false,'format'=>'number','calculateTotal' => true); 
	
		
	$arrReturn = $obj->insertReportColumns(13, $arrDataStructure, $arrTempStructure,$twig,$arrTwigVar,  $arrHeaderTemplate);
	$arrTemplate = $arrReturn['tableTemplate'];
     
	
	$totalRs = count($rs);
    for( $i=0;$i<$totalRs;$i++){
 		$rs[$i]['totalinvoiced'] = 0;
        $arrHeaderStyle = array(); 
		$arrStatus = array();  
        $arrInvoiceCode = array();
    
        $rsDetail = (!empty($rsDetailCol[$rs[$i]['pkey']])) ? $rsDetailCol[$rs[$i]['pkey']] : array();
		
		// selling cost
 		$rsCost = isset($rsSalesByCategoryCol[$rs[$i]['pkey']]) ? $rsSalesByCategoryCol[$rs[$i]['pkey']] : array(); 
		foreach($rsCost as $costRow){
			$arrStructureIndex = 'mnv-selling-'.$costRow['categoryname'];
			if(!isset($rs[$i][$arrStructureIndex])) $rs[$i][$arrStructureIndex] = 0; 
			$rs[$i][$arrStructureIndex] += $costRow['subtotal'];
		}
		
		// header cost
		$rsCost = isset($rsCostByCategoryCol[$rs[$i]['pkey']]) ? $rsCostByCategoryCol[$rs[$i]['pkey']] : array();
		foreach($rsCost as $costRow){
			$arrStructureIndex = 'mnv-cost-'.$costRow['categoryname'];
			if(!isset($rs[$i][$arrStructureIndex])) $rs[$i][$arrStructureIndex] = 0; 
			$rs[$i][$arrStructureIndex] += $costRow['subtotal'];
		}
		
		// SPK outsource cost
		$rsCost = isset($rsOutsourceCostCol[$rs[$i]['pkey']]) ? $rsOutsourceCostCol[$rs[$i]['pkey']] : array();
		foreach($rsCost as $costRow){
			$arrStructureIndex = 'totaloutsourcetrucking';
			if(!isset($rs[$i][$arrStructureIndex])) $rs[$i][$arrStructureIndex] = 0; 
			$rs[$i][$arrStructureIndex] += $costRow['outsourcecost'];
		}
		
		// SPK cost
		$rsCost = isset($rsSPKCostCol[$rs[$i]['pkey']]) ? $rsSPKCostCol[$rs[$i]['pkey']] : array();
		foreach($rsCost as $costRow){
			$arrStructureIndex = 'mnv-cost-'.$costRow['categoryname'];
			if(!isset($rs[$i][$arrStructureIndex])) $rs[$i][$arrStructureIndex] = 0; 
			$rs[$i][$arrStructureIndex] += $costRow['subtotal'];
		}
		
		
		// invoice
		if(isset($rsInvoiceCol[$rs[$i]['pkey']])){
            foreach($rsInvoiceCol[$rs[$i]['pkey']] as $invoiceRow){  
				$invoiceCode = ($invoiceRow['statuskey']==1) ? $invoiceRow['code'].'*':$invoiceRow['code']; 
				array_push($arrInvoiceCode, $invoiceCode);
            } 
        }
 
		$rs[$i]['invoicenumber'] = implode('<br>',$arrInvoiceCode); 
 
        // gk boleh continue, karena ad JO yg gk ad SPK
        /*if (empty($rsDetail)) continue;*/
 
        if ($rs[$i]['grossprofit'] < 0) { 
            $arrHeaderStyle['grossprofit']['textColor'] = 'C41E3A'; 
        }else if ($rs[$i]['grossprofit'] > 0){ 
            $arrHeaderStyle['grossprofit']['textColor'] = '568203';  
        }

		$rs[$i]['volume20'] = 0;
        $rs[$i]['volume40'] = 0;
        $rs[$i]['volume45'] = 0;
        
        for ($j=0;$j<count($rsDetail);$j++){  
            $vol = strval(intval($rsService[$rsDetail[$j]['itemkey']]['volume'])); 
            $rs[$i]['volume'.$vol] += $rsDetail[$j]['qtyinbaseunit'] * $rsService[$rsDetail[$j]['itemkey']]['qty']; 
        }
            
        // has detail

		if($isShowDetail)
        $rs[$i]['_detail_'] = array('arrTemplate'=>$arrDetailTemplate,'data' => $rsDetail); 

        
        $return = $obj->formatReportRows(array('data' => $rs[$i], 'style' => $arrHeaderStyle),$arrTemplate); 

        // ===== FOR EXPORT SECTION 
        array_push($dataToExport, $return['data']);  
        // ===== END FOR EXPORT SECTION

        $tempreport .= $return['html'];
        $arrTemplate[0]['total'] = $obj->arraySum($arrTemplate[0]['total'], $return['subtotal'][0]);
    }  
 
    $tableHeader = $twig->render('template-header.html', $arrTwigVar);
    $obj->generateReport($_POST, $tempreport, $arrTemplate,$dataToExport,$arrFilterInformation,$tableHeader);

}
else{
   	$_POST['trStartDate'] = date('d / m / Y');
	$_POST['trEndDate'] = date('d / m / Y'); 
}

$rsCategory = $truckingServiceOrderCategory->searchData($truckingServiceOrderCategory->tableName.'.statuskey',1,true); 
$arrWarehouse = $class->convertForCombobox($warehouse->searchData($warehouse->tableName.'.statuskey',1,true,'','order by name asc'),'pkey','name');
$arrStatus = $class->convertForCombobox($obj->getAllStatus(),'pkey','status');   
$arrCustomer = $class->convertForCombobox($customer->searchData($customer->tableName.'.statuskey',2,true,'','order by name asc'),'pkey','name');
$arrSales = $class->convertForCombobox($employee->searchData($employee->tableName.'.statuskey',2,true,' and issales = 1','order by name asc'),'pkey','name');
$arrCategory = $class->convertForCombobox($rsCategory,'pkey','name'); 

//$arrJobType = $obj->convertForCombobox($obj->getCargoType(),'pkey','name');    
    
$arrTwigVar['inputHidCityKey'] =  $class->inputHidden('hidCityKey');
$arrTwigVar['inputSalesCode'] =  $class->inputText('salesCode');
$arrTwigVar['inputSelWarehouse'] =  $class->inputSelect('selWarehouse[]', $arrWarehouse, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));  
$arrTwigVar['inputSelCustomer'] =  $class->inputSelect('selCustomer[]', $arrCustomer, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
//$arrTwigVar['inputSelSales'] =  $class->inputSelect('selSales[]', $arrSales, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputTemplateCustomer'] = $class->inputAutoComplete(array(   
                                                                        'element' => array('value' => 'selTemplateCustomer',
                                                                                           'key' => 'hidTemplateCustomerKey'),
                                                                        'source' => array(
                                                                                            'url' => '../ajax-template-customer.php',
                                                                                            'data' => array(  'action' =>'searchData')
                                                                                        ), 
                                                                        'placeholder' => $obj->lang['searchTemplate'].'...',
                                                                        'callbackFunction' => 'updateCustomer(this)' 
                                                                      ));  
$arrTwigVar['inputHidConsigneeKey'] =  $class->inputHidden('hidConsigneeKey');
$arrTwigVar['inputConsigneeName'] =  $class->inputText('consigneeName');
$arrTwigVar['inputSelStatus'] =  $class->inputSelect('selStatus[]', $arrStatus, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputStartDate'] = $class->inputDate('trStartDate',array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputEndDate'] = $class->inputDate('trEndDate',array('etc' => 'style="text-align:center"')); 
$arrTwigVar['inputSelDateType'] =  $class->inputSelect('selDateType', $arrDateType);  
//$arrTwigVar['inputDONumber'] =  $class->inputText('doNumber');
$arrTwigVar['inputShipmentNumber'] =  $class->inputText('shipmentNumber');
$arrTwigVar['inputSelCategory'] =  $class->inputSelect('selCategory[]', $arrCategory, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox')); 
//$arrTwigVar['inputSelCargoType'] =  $class->inputSelect('selCargoType[]', $arrJobType, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));  
//$arrTwigVar['inputShowDetail'] =  $class->inputCheckBox('isShowDetail'); 
//$arrTwigVar['inputChkSPKDetail'] =  $class->inputCheckBox('chkSPKDetail',array('overwritePost' => false, 'value' => 1, 'class' => 'no-class'));   
$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate;    

echo $twig->render('reportGrossPNLTrucking.html', $arrTwigVar);  
 
?>
