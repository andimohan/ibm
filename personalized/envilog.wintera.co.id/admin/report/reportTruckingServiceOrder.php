<?php

$obj= $truckingServiceOrder;
$securityObject = 'reportTruckingServiceOrder'; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true));  
$hasSellingPriceAccess = $security->isAdminLogin($truckingServiceOrder->sellingPriceSecurityObject,10);  

  
$arrFilterInformation = array();  
$_POST['selStatus[]'] = array(2,3,4,5,6);
if(!isset($_POST['isShowDetail']))  $_POST['isShowDetail'] = 0;  
$isShowDetail = (isset($_POST['isShowDetail']) && !empty($_POST['isShowDetail'])) ? true : false;
if(!isset($_POST['selDateType']) || empty($_POST['selDateType']))
    $_POST['selDateType'] = 1;

$arrDateType= array(
    '1' => $obj->lang['transactionDate'],
    '2' => $obj->lang['stuffingAndDestuffingDateTime']
);

// ===== FOR EXPORT SECTION
$dataToExport = array();

/* data structure */
$arrTemplate = array();

$arrDataStructure = array();
$arrDataStructure['rowNumber'] = array('title'=>'#', 'align'=>'right', 'width'=>"40px", 'autoNumber' => true, "sortable" => false);
$arrDataStructure['code'] = array('title'=>ucwords($obj->lang['code']),  'width'=>"150px", 'dbfield' => 'code'); 
$arrDataStructure['date'] = array('title'=>ucwords($obj->lang['date']),'dbfield' => 'trdate', 'width'=>"90px",'format'=>'date');
$arrDataStructure['doNumber'] = array('title'=>ucwords($obj->lang['si']),'dbfield' => 'donumber', 'width'=>"200px");
$arrDataStructure['shipmentNumber'] = array('title'=>ucwords($obj->lang['bookingNumber']),'dbfield' => 'shipmentnumber', 'width'=>"150px");
//$arrDataStructure['salesName'] = array('title'=>ucwords($obj->lang['salesman']),'dbfield' => 'salesname', 'width'=>"200px", 'mergeExcelCell' => 2);
$arrDataStructure['kraniName'] = array('title'=>'Krani','dbfield' => 'kraniname', 'width'=>"200px", "sortable" => false, 'mergeExcelCell' => 2);
$arrDataStructure['customerName'] = array('title'=>ucwords($obj->lang['customer']),'dbfield' => 'customername', 'width'=>"200px", 'mergeExcelCell' => 2);
$arrDataStructure['stuffingDate'] = array('title'=>ucwords($obj->lang['stuffingDate']),'dbfield' => 'lastwodate', 'width'=>"90px",'format'=>'date','returnOnEmpty' => array('returnOnEmpty' => true, 'value' => ''));
$arrDataStructure['category'] = array('title'=>ucwords($obj->lang['category']),'dbfield' => 'categoryname', 'width'=>"150px");
$arrDataStructure['cargoType'] = array('title'=>ucwords($obj->lang['cargoType']),'dbfield' => 'cargotype', 'width'=>"90px");
$arrDataStructure['party'] = array('title'=>ucwords($obj->lang['party']),'dbfield' => 'party', 'width'=>"100px", "sortable" => false); 

$arrDataStructure['20'] = array('title'=>'20\'','dbfield' => 'volume20', 'align'=>'right', 'width'=>"60px", 'format'=>'number', 'calculateTotal' => true, "sortable" => false);
$arrDataStructure['40'] = array('title'=>'40\'','dbfield' => 'volume40', 'align'=>'right',  'width'=>"60px" ,'format'=>'number','calculateTotal' => true, "sortable" => false);
$arrDataStructure['45'] = array('title'=>'45\'','dbfield' => 'volume45', 'align'=>'right',  'width'=>"60px" ,'format'=>'number','calculateTotal' => true, "sortable" => false);

$arrDataStructure['consigneeName'] = array('title'=>ucwords($obj->lang['consignee']),'dbfield' => 'consigneename', 'width'=>"200px", 'mergeExcelCell' => 2);
$arrDataStructure['warehouse'] = array('title'=>ucwords($obj->lang['warehouse']),'dbfield' => 'consigneewarehousename', 'width'=>"100px");
$arrDataStructure['depot'] = array('title'=>ucwords($obj->lang['depot']),'dbfield' => 'depotname', 'width'=>"100px");
$arrDataStructure['terminal'] = array('title'=>ucwords($obj->lang['terminal']),'dbfield' => 'terminalname', 'width'=>"100px");
$arrDataStructure['locationname'] = array('title'=>ucwords($obj->lang['location']),'dbfield' => 'locationname', 'width'=>"100px");
$arrDataStructure['routefrom'] = array('title'=>ucwords($obj->lang['from']),'dbfield' => 'routefrom', 'width'=>"120px");
$arrDataStructure['routeto'] = array('title'=>ucwords($obj->lang['destination']),'dbfield' => 'routeto', 'width'=>"120px");
$arrDataStructure['total'] = array('title'=>ucwords($obj->lang['totalSales']),'dbfield' => 'grandtotal','align'=>'right', 'width'=>"110px",'format'=>'number','calculateTotal' => true); 
$arrDataStructure['totalInvoiced'] = array('title'=>ucwords($obj->lang['invoiceIssued']),'dbfield' => 'totalinvoiced','align'=>'right', 'width'=>"110px",'format'=>'number','calculateTotal' => true); 
$arrDataStructure['totalCost'] = array('title'=>ucwords($obj->lang['totalCost']),'dbfield' => 'totalcost','align'=>'right', 'width'=>"110px",'format'=>'number','calculateTotal' => true); 
$arrDataStructure['totalSharedProfit'] = array('title'=>ucwords($obj->lang['shareProfit']),'dbfield' => 'totalsharedprofit','align'=>'right', 'width'=>"110px",'format'=>'number','calculateTotal' => true); 
$arrDataStructure['grossProfit'] = array('title'=>ucwords($obj->lang['grossProfit']),'dbfield' => 'grossprofit','align'=>'right', 'width'=>"110px",'format'=>'number','calculateTotal' => true); 
$arrDataStructure['invoiceNumber'] = array('title'=>ucwords($obj->lang['invoiceNumber']),'dbfield' => 'invoicenumber', 'width'=>"150px", "sortable" => false);
$arrDataStructure['arStatus'] = array('title'=>ucwords($obj->lang['ar']),'dbfield' => 'arstatusname', 'width'=>"100px"); 
$arrDataStructure['note'] = array('title'=>ucwords($obj->lang['note']), 'width'=>"300px",'dbfield' => 'trdesc');
$arrDataStructure['status'] = array('title'=>ucwords($obj->lang['status']),'dbfield' => 'statusname', 'width'=>"100px");
		   
$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $obj->lang['truckingServiceOrderReport']; 
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
	if(isset($_POST) && !empty($_POST['salesCode'])) {
		$criteria .= ' AND '.$obj->tableName.'.code LIKE ('.$class->oDbCon->paramString('%'.$_POST['salesCode'].'%').')';
		array_push($arrFilterInformation,array("label" => 'Kode', 'filter' => $_POST['salesCode']));
	}

	if(isset($_POST) && !empty($_POST['trStartDate'])){
      switch($_POST['selDateType']){
            case '1' : $fieldName = $obj->tableName.'.trdate';  break;
            case '2' : $fieldName = $obj->tableName.'.lastwodate'; break;
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
    
//	if(isset($_POST) && !empty($_POST['customerName'])) {
//		$criteria .= ' AND '.$obj->tableCustomer.'.name LIKE ('.$class->oDbCon->paramString('%'.$_POST['customerName'].'%').')';
//	 	array_push($arrFilterInformation,array("label" => 'Pelanggan', 'filter' =>  $_POST['customerName']));
//	} 
    
     if(isset($_POST) && !empty($_POST['selCustomer'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selCustomer']));   
        
       	$criteria .= ' AND customerkey in('.$key.')';  

        $rsCriteria = $customer->searchData('','',true, ' and '.$customer->tableName.'.pkey in ('.$key.')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$statusName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => 'Pelangan', 'filter' => $statusName ));
        
	}	
    
    if(isset($_POST) && !empty($_POST['selSales'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selSales']));   
        
       	$criteria .= ' AND '.$obj->tableName.'.saleskey in('.$key.')';  

        $rsCriteria = $employee->searchData('','',true, ' and '.$employee->tableName.'.pkey in ('.$key.')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$salesName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => 'Sales', 'filter' => $salesName ));
        
	}	
    	   
	if(isset($_POST) && !empty($_POST['doNumber'])) {
		$criteria .= ' AND '.$obj->tableName.'.donumber LIKE ('.$class->oDbCon->paramString('%'.$_POST['doNumber'].'%').')';
	 	array_push($arrFilterInformation,array("label" => 'DO Pelanggan', 'filter' =>  $_POST['doNumber']));
	} 
	if(isset($_POST) && !empty($_POST['shipmentNumber'])) {
		$criteria .= ' AND '.$obj->tableName.'.shipmentnumber LIKE ('.$class->oDbCon->paramString('%'.$_POST['shipmentNumber'].'%').')';
	 	array_push($arrFilterInformation,array("label" => 'Booking Pelayaran', 'filter' =>  $_POST['shipmentNumber']));
	} 
    	   
	if(isset($_POST) && !empty($_POST['consigneeName'])) {
		$criteria .= ' AND '.$obj->tableConsignee.'.name LIKE ('.$class->oDbCon->paramString('%'.$_POST['consigneeName'].'%').')';
	 	array_push($arrFilterInformation,array("label" => 'Consignee', 'filter' =>  $_POST['consigneeName']));
	}  
    
	if(isset($_POST) && !empty($_POST['selCategory'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selCategory']));   
        
       	$criteria .= ' AND '.$obj->tableName.'.categorykey in('.$key.')';  

        $rsCriteria =  $truckingServiceOrderCategory->searchData('','',true, ' and '.$truckingServiceOrderCategory->tableName.'.pkey in ('.$key.')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$statusName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => 'Kategori', 'filter' => $statusName));
        
	}
    

	if(isset($_POST) && !empty($_POST['selCargoType'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selCargoType']));   
        
       	$criteria .= ' AND '.$obj->tableName.'.cargotypekey in('.$key.')';  

        $rsCriteria =  $obj->getCargoType($_POST['selCargoType']);
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$statusName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => 'Jenis Kargo', 'filter' => $statusName));
        
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
	$rs = $obj->searchData('','',true,$criteria,$order);
    $tempreport = '';
	 
    if (empty($rs)) 
			$tempreport .= '<tr class="report-row rewrite-row"><td colspan="'.count($arrHeaderTemplate['dataStructure']).'"></td></tr>';
 
	$rsDetailCol = $obj->getDetailCollections($rs,'refkey');
	$arrKeys = array_column($rs,'pkey');
	
	$rsService = $truckingService->searchDataRow(array($truckingService->tableName.'.pkey' ,$truckingService->tableName.'.volume',$truckingService->tableName.'.qty'),
											 ' and '.$truckingService->tableName.'.servicecost = 0 
											   and '.$truckingService->tableName.'.itemtype = 2'
											);

	$rsService = array_column($rsService,null,'pkey');	

    // tarik semua biaya
    $rsHeaderCostCol = $obj->getHeaderCost($arrKeys);
    $rsHeaderCostCol = $obj->reindexDetailCollections($rsHeaderCostCol,'refkey');
    
    //$rsHeaderInvoicedCol = $obj->getInvoiceInformation($arrKeys);
    //$rsHeaderInvoicedCol = $obj->reindexDetailCollections($rsHeaderInvoicedCol,'salesorderkey');

    $rsInvoiceCol = $obj->getAmountInvoiced($arrKeys);
    $rsInvoiceCol = $obj->reindexDetailCollections($rsInvoiceCol,'salesorderkey');
 
    for( $i=0;$i<count($rs);$i++) {    
 		$rs[$i]['totalinvoiced'] = 0;
        $arrHeaderStyle = array(); 
		$arrStatus = array();  
        $arrInvoiceCode = array();
  
        $rsDetail = (!empty($rsDetailCol[$rs[$i]['pkey']])) ? $rsDetailCol[$rs[$i]['pkey']] : array();
		
        $rsHeaderCost = (!empty($rsHeaderCostCol[$rs[$i]['pkey']])) ? $rsHeaderCostCol[$rs[$i]['pkey']] : array();
 
        $hasOutstanding = false;
        
        $pendingInvoice = 0;
		if(isset($rsInvoiceCol[$rs[$i]['pkey']])){
            foreach($rsInvoiceCol[$rs[$i]['pkey']] as $invoiceRow){ 
                
                // kalo statusnya masih menunggu jgn dihitung
                //if( in_array($invoiceRow['statuskey'],array(2,3)))
                $rs[$i]['totalinvoiced'] += $invoiceRow['amount'];
                if( $invoiceRow['statuskey'] == 1 )
                    $pendingInvoice += $invoiceRow['amount'];
                
                array_push($arrStatus, $invoiceRow['arstatusname']);
				$invoiceCode = ($invoiceRow['statuskey']==1) ? $invoiceRow['code'].'*':$invoiceRow['code'];
				//array_push($arrInvoiceCode, $invoiceRow['code']); 
				array_push($arrInvoiceCode, $invoiceCode); 
                
                if(in_array($invoiceRow['arstatuskey'],array(1,2)))
                    $hasOutstanding = true; 
                
            }
   
        }
		
		$arrHeaderStyle['arstatusname']['textColor'] = ($hasOutstanding) ? 'C41E3A' : '568203';
		$rs[$i]['invoicenumber'] = implode('<br>',$arrInvoiceCode);
        $rs[$i]['arstatusname'] = implode('<br>',$arrStatus);
        
        if ( ($rs[$i]['totalinvoiced']-$pendingInvoice) < $rs[$i]['grandtotal']){ // dipotong dengan yg masi pending
            $arrHeaderStyle['totalinvoiced']['textColor'] = 'FFFFFF';
            $arrHeaderStyle['totalinvoiced']['backgroundColor'] = 'C41E3A';
        }
		

        // gk boleh continue, karena ad JO yg gk ad SPK
        /*if (empty($rsDetail)) continue;*/
        
//        $rsHeaderInvoiced = (!empty($rsHeaderInvoicedCol[$rs[$i]['pkey']])) ? $rsHeaderInvoicedCol[$rs[$i]['pkey']] : array();
        
        $rs[$i]['kraniname'] = implode(', ',array_unique(array_column($rsHeaderCost,'recipientname'))); 
      /*  
        $totalInvoice = 0;
        foreach($rsHeaderInvoiced as $invoice) 
            $totalInvoice += $invoice['amount']; 
        
        $rs[$i]['totalinvoiced'] = $totalInvoice;*/

        if (!$hasSellingPriceAccess) { 
            $rs[$i]['grandtotal'] = 0; 
            $rs[$i]['grossprofit'] = 0; 
        }
            
        if ($rs[$i]['grossprofit'] < 0) { 
            $arrHeaderStyle['grossprofit']['textColor'] = 'C41E3A'; 
        }else if ($rs[$i]['grossprofit'] > 0){ 
            $arrHeaderStyle['grossprofit']['textColor'] = '568203';  
        }

		$rs[$i]['volume20'] = 0;
        $rs[$i]['volume40'] = 0;
        $rs[$i]['volume45'] = 0;
		
        $arrParty = array(); 
        for ($j=0;$j<count($rsDetail);$j++){ 
            if (!$hasSellingPriceAccess){ 
                $rsDetail[$j]['priceinunit'] = 0;
                $rsDetail[$j]['total'] = 0;
            }            
         	$vol = strval(intval($rsService[$rsDetail[$j]['itemkey']]['volume'])); 
            $rs[$i]['volume'.$vol] += $rsDetail[$j]['qtyinbaseunit'] * $rsService[$rsDetail[$j]['itemkey']]['qty'];
            $arrParty[$rsDetail[$j]['itemname']] = (!isset($arrParty[$rsDetail[$j]['itemname']])) ? $rsDetail[$j]['qtyinbaseunit'] : $arrParty[$rsDetail[$j]['itemname']] + $rsDetail[$j]['qtyinbaseunit'] ;
                  
        }
           
        $arrPartyKeys = array_keys($arrParty);
        $party = array();
        for($k=0;$k<count($arrPartyKeys); $k++)
            array_push($party, $arrParty[$arrPartyKeys[$k]] .'x '. $arrPartyKeys[$k]);

        $rs[$i]['party'] = implode('<br>',$party);
        
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
 
    $obj->generateReport($_POST, $tempreport, $arrTemplate,$dataToExport,$arrFilterInformation);

}
else{
   	$_POST['trStartDate'] = date('d / m / Y');
	$_POST['trEndDate'] = date('d / m / Y'); 
}

    
//$arrWarehouse = $class->convertForCombobox($warehouse->searchData($warehouse->tableName.'.statuskey',1,true,'order by name asc'),'pkey','name');
//$arrCity = $class->convertForCombobox($city->searchData($city->tableName.'.statuskey',1,true),'pkey','name');   

$rsCategory = $truckingServiceOrderCategory->searchData($truckingServiceOrderCategory->tableName.'.statuskey',1,true); 
$arrWarehouse = $class->convertForCombobox($warehouse->searchData($warehouse->tableName.'.statuskey',1,true,'','order by name asc'),'pkey','name');
$arrStatus = $class->convertForCombobox($obj->getAllStatus(),'pkey','status');   
$arrCustomer = $class->convertForCombobox($customer->searchData($customer->tableName.'.statuskey',2,true,'','order by name asc'),'pkey','name');
$arrSales = $class->convertForCombobox($employee->searchData($employee->tableName.'.statuskey',2,true,' and issales = 1','order by name asc'),'pkey','name');
$arrCategory = $class->convertForCombobox($rsCategory,'pkey','name'); 
$arrJobType = $obj->convertForCombobox($obj->getCargoType(),'pkey','name');    
    

$arrTwigVar['inputHidCityKey'] =  $class->inputHidden('hidCityKey');
$arrTwigVar['inputSalesCode'] =  $class->inputText('salesCode');
$arrTwigVar['inputSelWarehouse'] =  $class->inputSelect('selWarehouse[]', $arrWarehouse, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));  
//$arrTwigVar['inputHidCustomerKey'] =  $class->inputHidden('hidCustomerKey');
//$arrTwigVar['inputCustomerName'] =  $class->inputText('customerName');
$arrTwigVar['inputSelCustomer'] =  $class->inputSelect('selCustomer[]', $arrCustomer, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputSelSales'] =  $class->inputSelect('selSales[]', $arrSales, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
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
$arrTwigVar['inputDONumber'] =  $class->inputText('doNumber');
$arrTwigVar['inputShipmentNumber'] =  $class->inputText('shipmentNumber');
$arrTwigVar['inputSelCategory'] =  $class->inputSelect('selCategory[]', $arrCategory, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox')); 
$arrTwigVar['inputSelCargoType'] =  $class->inputSelect('selCargoType[]', $arrJobType, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));  
$arrTwigVar['inputShowDetail'] =  $class->inputCheckBox('isShowDetail'); 
//$arrTwigVar['inputChkSPKDetail'] =  $class->inputCheckBox('chkSPKDetail',array('overwritePost' => false, 'value' => 1, 'class' => 'no-class'));   
$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate;    

echo $twig->render('@custom/reportTruckingServiceOrder.html', $arrTwigVar);  
 
?>
