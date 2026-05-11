<?php	 

$obj= $ar;
$arPayment = $obj->getPaymentObj();
$securityObject = 'reportAR'; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true)); 
  
$_POST['selStatus[]'] = array(1,2); 

$arrFilterInformation = array();   

// ====================== must be set before TWIG
if (!isset($_POST['trStartDate']) || empty($_POST['trStartDate'])){ 
	$_POST['trStartDate'] = date('d / m / Y');
	$_POST['trEndDate'] = date('d / m / Y');
}   

$orderCriteria = array(); 
$orderCriteria['orderBy'] =  (isset ($_POST) && !empty($_POST['hidOrderBy']) ) ?  $obj->oDbCon->paramOrder($_POST['hidOrderBy']) : 'pkey'; //$obj->tableName.'.
$orderCriteria['orderType'] = (isset ($_POST) && !empty($_POST['hidOrderType'])) ?   $_POST['hidOrderType'] : -1;
// ====================== must be set before TWIG


// ===== FOR EXPORT SECTION
$dataToExport = array();

/* data structure */
$arrTemplate = array();

$rsARType = $obj->getARType(); 
define('AR_IMPORT_TYPE',array_column($rsARType,'name','pkey')); 

$arrDataStructure = array();
$isShowDetail = (isset($_POST['isShowDetail']) && $_POST['isShowDetail'] == 1) ? true : false;
 
$rsCurrency = $currency->searchData($currency->tableName.'.statuskey',1,true,'','order by name asc');
 
$arrDataStructure['rowNumber'] = array('title'=>'#', 'align'=>'right', 'width'=>"40px", 'autoNumber' => true,"sortable" => false);
$arrDataStructure['code'] = array('title'=>ucwords($obj->lang['code']),  'width'=>"100px", 'dbfield' => 'code');
$arrDataStructure['refcode'] = array('title'=>ucwords($obj->lang['invoiceCode']),  'width'=>"150px", 'dbfield' => 'refcode');

 if( in_array(PLAN_TYPE['categorykey'], array(COMPANY_TYPE['trucking'],COMPANY_TYPE['forwarding'])) ) 
  $arrDataStructure['jocode'] = array('title'=>$obj->lang['JOCode'],'dbfield' => 'jocode', 'width'=>"150px", 'sortable' => false);


$arrDataStructure['trdate'] = array('title'=>ucwords($obj->lang['invoiceDate']),'dbfield' => 'invoicedate', 'width'=>"100px",'format'=>'date', 'sortable' => false);
$arrDataStructure['receiveddate'] = array('title'=>ucwords($obj->lang['invoiceReceivedDate']),'dbfield' => 'receiveddate', 'width'=>"140px", 'sortable' => false,'format'=>'date','returnOnEmpty' => array('returnOnEmpty' => true, 'value' => ''), 'sortable' => false);
$arrDataStructure['duedate'] = array('title'=>ucwords($obj->lang['duedate']),'dbfield' => 'duedate', 'width'=>"100px", 'sortable' => false,'format'=>'date');
$arrDataStructure['datediff'] = array('title'=>ucwords($obj->lang['aging']),'dbfield' => 'datediff', 'width'=>"70px", 'sortable' => false,'format'=>'number');
//$arrDataStructure['warehouse'] = array('title'=>ucwords($obj->lang['warehouse']),'dbfield' => 'warehousename', 'width'=>"150px");

//$arrDataStructure['customer'] = array('title'=>ucwords($obj->lang['customer']),'dbfield' => 'customername', 'width'=>"200px");

if(in_array(PLAN_TYPE['categorykey'], array(COMPANY_TYPE['trucking'],COMPANY_TYPE['forwarding']))){ 
  $arrDataStructure['consignee'] = array('title'=>ucwords($obj->lang['consignee']),'dbfield' => 'consigneename', 'width'=>"200px", 'sortable' => false);
  $arrDataStructure['si'] = array('title'=>ucwords($obj->lang['si']),'dbfield' => 'refcode2', 'width'=>"150px");
//  $arrDataStructure['containernumber'] = array('title'=>ucwords($obj->lang['containerNumber']),'dbfield' => 'containernumber', 'width'=>"200px", 'sortable' => false);
  $arrDataStructure['docNumber'] = array('title'=>ucwords($obj->lang['documentNo']),'dbfield' => 'documentnumber', 'sortable' => false, 'width'=>"120px");
  $arrDataStructure['refcode2'] = array('title'=>ucwords($obj->lang['refCode']),'dbfield' => 'poreference', 'sortable' => false, 'width'=>"150px");
  $arrDataStructure['currency'] = array('title'=>ucwords($obj->lang['curr']),'dbfield' => 'currencyname',  'align' => 'center', 'width'=>"60px");
} else {             
  $arrDataStructure['sales'] = array('title'=>ucwords($obj->lang['salesman']),'dbfield' => 'salesname', 'width'=>"150px");
}

if(count($rsCurrency) == 1){
    $arrDataStructure['ammount'] = array('title'=>ucwords($obj->lang['amount']),'dbfield' => 'amount', 'width'=>"130px" ,'format'=>'number' , 'sortable' => false,'calculateTotal' => true);
    $arrDataStructure['taxvalue'] = array('title'=>'PPN','dbfield' => 'taxvalue', 'width'=>"120px" ,'format'=>'number','sortable' => false,'calculateTotal' => true);
//    $arrDataStructure['tax23value'] = array('title'=>'PPH','dbfield' => 'tax23value', 'width'=>"120px" ,'format'=>'number','sortable' => false,'calculateTotal' => true);
    $arrDataStructure['outstanding'] = array('title'=>ucwords($obj->lang['outstanding']),'dbfield' => 'outstanding', 'width'=>"130px" ,'format'=>'number','calculateTotal' => true); 
}else{ 
    // nanti baru diupdate
//    foreach($rsCurrency as $currRow){
//        $arrDataStructure['ammount'.$currRow['pkey']] = array('title'=>ucwords($obj->lang['amount']). ' ' .$currRow['name'],'dbfield' => 'amount'.$currRow['pkey'],"sortable" => false, 'width'=>"130px" ,'format'=>'number','calculateTotal' => true);
//        $arrDataStructure['outstanding'.$currRow['pkey']] = array('title'=>ucwords($obj->lang['outstanding']). ' '.$currRow['name'],'dbfield' => 'outstanding'.$currRow['pkey'],"sortable" => false, 'width'=>"130px" ,'format'=>'number','calculateTotal' => true);
//    }
}


$arrDataStructure['note'] = array('title'=>ucwords($obj->lang['note']),'dbfield' => 'trdesc','width'=>"300px" );
$arrDataStructure['status'] = array('title'=>ucwords($obj->lang['status']),'dbfield' => 'statusname', 'width'=>"100px");

$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $obj->lang['ARSOAReport']; 
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();

array_push($arrTemplate, $arrHeaderTemplate);

// detail ...


$arrStatus = $class->convertForCombobox($obj->getAllStatus(),'pkey','status');
$arrWarehouse = $class->convertForCombobox($warehouse->searchData($warehouse->tableName.'.statuskey',1,true,'','order by name asc'),'pkey','name');
$arrCustomer = $class->convertForCombobox($customer->searchData($customer->tableName.'.statuskey',2,true,'','order by name asc'),'pkey','name');
$arrCurrency = $class->convertForCombobox($rsCurrency,'pkey','name');
$arrType = $class->convertForCombobox($rsARType,'pkey','name');
$arrEmployee = $class->convertForCombobox($employee->searchData($employee->tableName.'.statuskey',2,true, ' and '.$employee->tableName.'.issales = 1 ' ),'pkey','name');   

$arrTwigVar['inputCode'] =  $class->inputText('code');
$arrTwigVar['inputShowDetail'] =  $class->inputCheckBox('isShowDetail');
$arrTwigVar['inputStartDate'] = $class->inputDate('trStartDate',array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputEndDate'] = $class->inputDate('trEndDate',array('etc' => 'style="text-align:center"'));  
$arrTwigVar['inputSelStatus'] =  $class->inputSelect('selStatus[]', $arrStatus, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox')); 
$arrTwigVar['inputSelCurrency'] =  $class->inputSelect('selCurrency[]', $arrCurrency, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox')); 
$arrTwigVar['inputChkDueDate'] =  $class->inputCheckBox('chkDueDate',array('overwritePost' => false, 'value' => 0, 'class' => 'no-class'));  
$arrTwigVar['inputSelWarehouse'] =  $class->inputSelect('selWarehouse[]', $arrWarehouse, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputSelCustomer'] =  $class->inputSelect('selCustomer[]', $arrCustomer, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
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
$arrTwigVar['inputSelARType'] =  $class->inputSelect('selARType[]', $arrType, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['order'] =  $orderCriteria; 
$arrTwigVar['PLAN_TYPE'] = PLAN_TYPE;
$arrTwigVar['COMPANY_TYPE'] = COMPANY_TYPE;
$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate; 


if (isset($_POST) && !empty($_POST['hidAction'])){   
	
	$criteria = '';
	
	if(isset($_POST) && !empty($_POST['code'])) {
		$criteria .= ' AND '.$obj->tableName.'.code LIKE ('.$class->oDbCon->paramString('%'.$_POST['code'].'%').')';
		array_push($arrFilterInformation,array("label" => 'Kode', 'filter' => $_POST['code']));
	}
    
	if(isset($_POST) && !empty($_POST['trStartDate'])){
		$criteria .= ' AND '.$obj->tableName.'.trdate between '.$class->oDbCon->paramDate( $_POST['trStartDate'],' / ').' AND '.$class->oDbCon->paramDate( $_POST['trEndDate'],' / ','Y-m-d 23:59'); 
		array_push($arrFilterInformation,array("label" => 'Tanggal', 'filter' => $_POST['trStartDate'] . ' - ' .$_POST['trEndDate'] ));
	} 
  
    
    if(isset($_POST) && !empty($_POST['selCustomer'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selCustomer']));   
        
       	$criteria .= ' AND '.$obj->tableName.'.customerkey in('.$key.')';  

        $rsCriteria = $customer->searchData('','',true, ' and '.$customer->tableName.'.pkey in ('.$key.')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$statusName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => 'Pelangan', 'filter' => $statusName ));
        
	}	
  
    if(isset($_POST) && !empty($_POST['selCurrency'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selCurrency']));   
        
       	$criteria .= ' AND '.$obj->tableName.'.currencykey in('.$key.')';  

        $rsCriteria = $currency->searchData('','',true, ' and '.$currency->tableName.'.pkey in ('.$key.')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$statusName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => 'Mata Uang', 'filter' => $statusName ));
        
	}	
    

    if(isset($_POST) && !empty($_POST['chkDueDate'])){  
			$criteria .= ' having datediff > 0';  
			array_push($arrFilterInformation,array("label" => 'Aging', 'filter' => 'Tampilkan hanya yang jatuh tempo'));
    }

	if(isset($_POST) && !empty($_POST['selWarehouse'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selWarehouse']));   
        
       	$criteria .= ' AND '.$obj->tableName.'.warehousekey in('.$key.')';  

        $rsCriteria = $warehouse->searchData('','',true, ' and '.$warehouse->tableName.'.pkey in ('.$key.')');
	   
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$statusName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => 'Gudang', 'filter' => $statusName ));
        
	}
    
    if(isset($_POST) && !empty($_POST['selARType'])) { 
         
       	$criteria .= ' AND '.$obj->tableName.'.artype in('.$class->oDbCon->paramString($_POST['selARType'],',').')';  
        
        $rsCriteria = $obj->getARTypeName($_POST['selARType']);
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$statusName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => $obj->lang['transactionType'], 'filter' => $statusName ));
        
	}
    

	if(isset($_POST) && !empty($_POST['selStatus'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selStatus']));   
        
       	$criteria .= ' AND '.$obj->tableName.'.statuskey in('.$key.')';  

        $rsCriteria =  $obj->getStatusById($key);
	 
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
      
    
    $arrConsignee = array();
    $arrJobOrder = array();
 
	if(!empty($isActiveModule['truckingserviceorder'])){ 
 

        // kalo refkeynya dari invoice baru di push, karena bisa dr UM dan sales order barang jg
        $arrInvoiceKey = array();
        foreach($rs as $row) 
            if($row['reftabletype'] == $truckingType)
                array_push($arrInvoiceKey, $row['refkey']);      

        
        // invoice
        $rsInvoice = $truckingServiceOrderInvoice->searchDataRow(array($truckingServiceOrderInvoice->tableName.'.pkey',
                                                                      $truckingServiceOrderInvoice->tableName.'.trdate',
                                                                      $truckingServiceOrderInvoice->tableName.'.receiptdt',
                                                                      $truckingServiceOrderInvoice->tableName.'.receiveddate',
                                                                      $truckingServiceOrderInvoice->tableName.'.taxvalue',
                                                                      $truckingServiceOrderInvoice->tableName.'.tax23value'
                                                                      ), 
                                                                 ' and '.$truckingServiceOrderInvoice->tableName.'.statuskey in (2,3) 
                                                                  and '.$truckingServiceOrderInvoice->tableName.'.pkey in ('. $class->oDbCon->paramString($arrInvoiceKey,',').')'
                                                                 );
        
		$rsInvoice = array_column($rsInvoice,null,'pkey');
        
		// consignee
		$rsConsignee = $truckingServiceOrderInvoice->getConsigneeInformation($arrInvoiceKey);
		$arrConsignee[$truckingType] = $obj->reindexDetailCollections($rsConsignee,'invoicekey');

		// no job
		$rsJobOrder = $truckingServiceOrderInvoice->getJODetail($arrInvoiceKey);
		$arrJobOrder[$truckingType] = $obj->reindexDetailCollections($rsJobOrder,'refkey');
        
            

        // update kolom charges 
        $rsDetailInvoiceItem = $truckingServiceOrderInvoice->getDetailItemForSOA($arrInvoiceKey,2);
        $rsDetailInvoiceItemName = array_unique(array_column($rsDetailInvoiceItem,'itemname'));
             
        $rsDetailInvoiceItem = $obj->reindexDetailCollections($rsDetailInvoiceItem,'invoicekey');
        
        $arrTempStructure = array();
        foreach($rsDetailInvoiceItemName as $row)
            $arrTempStructure['itemamount-'.strtolower($row)] = array('title'=> $row,'dbfield' => 'itemamount-'.strtolower($row), 'width'=>"150px",'format'=>'number','sortable' => false,'calculateTotal' => true, 'textColor' => '568203');  
           
        $arrReturn = $obj->insertReportColumns(13, $arrDataStructure, $arrTempStructure,$twig,$arrTwigVar,  $arrHeaderTemplate);
        $arrTemplate = $arrReturn['tableTemplate']; 
 
	}

	
	$tempreport = '';  
    $totalRs = count($rs);
	for( $i=0;$i<$totalRs;$i++) {   
            $arrHeaderStyle = array();
			 
            $arPkey = explode (",",$rs[$i]['pkey']);
        
            $arrDetailStyle = array();
            
        // nanti baru diupdate
//            if(count($rsCurrency) >= 1){
//                foreach($rsCurrency as $currRow){
//                    $rs[$i]['totalamount'.$currRow['pkey']] = 0;
//                    $rs[$i]['totaloutstanding'.$currRow['pkey']] = 0;
//                    $rs[$i]['totaltaxvalue'.$currRow['pkey']] = 0;
//                    $rs[$i]['totaltax23value'.$currRow['pkey']] = 0;
//
//                    $currencykey = $rs[$i]['currencykey'];
//                    $rs[$i]['totalamount'.$currencykey] = $rs[$i]['totalamount'];
//                    $rs[$i]['totaloutstanding'.$currencykey] = $rs[$i]['totaloutstanding'];
//                    $rs[$i]['totaltaxvalue'.$currencykey] = $rs[$i]['totaltaxvalue'];
//                    $rs[$i]['totaltax23value'.$currencykey] = $rs[$i]['totaltax23value'];   
//                }
//            }
        
                if(in_array(PLAN_TYPE['categorykey'], array(COMPANY_TYPE['trucking'],COMPANY_TYPE['forwarding']))){
                    $refTableTpye = $rs[$i]['reftabletype'];
                    
                    // kalo jenis AR nya bukan dr invoice, harusnya diskip...
                    if($refTableTpye <> $truckingType) continue;
                        
                    $invoicekey = $rs[$i]['refkey'];

					// consignee
                    $consigneeInformation = (isset($arrConsignee[$refTableTpye][$invoicekey])) ? $arrConsignee[$refTableTpye][$invoicekey] : array();

                    $consigneeName=array();
                    foreach($consigneeInformation as $consigneeRow)
                        if(!in_array($consigneeRow['name'],$consigneeName))    
                           array_push($consigneeName,$consigneeRow['name']);

                    $rs[$i]['consigneename'] =  implode('<br>',$consigneeName);
                  
					
					// job
					$jobInformation = (isset($arrJobOrder[$refTableTpye][$invoicekey])) ? $arrJobOrder[$refTableTpye][$invoicekey] : array();
                     
 					$joCode =array();
                    $joShipmentNumber =array();
                    $joPOReference =array();
                    $joBookingCode = array();
                    foreach($jobInformation as $jobRow){ 
                        if(!in_array($jobRow['jocode'],$joCode))   array_push($joCode,$jobRow['jocode']);
                        if(!in_array($jobRow['shipmentnumber'],$joShipmentNumber))   array_push($joShipmentNumber,$jobRow['shipmentnumber']);
                        if(!in_array($jobRow['poreference'],$joPOReference))   array_push($joPOReference,$jobRow['poreference']); 

                        if(!empty($jobRow['donumber'])) array_push($joBookingCode, $jobRow['donumber']);
                        if(!empty($jobRow['shipmentnumber'])) array_push($joBookingCode, $jobRow['shipmentnumber']); 
                    }
                    
                    $rs[$i]['jocode'] =  implode('<br>',$joCode); 
                    $rs[$i]['poreference'] =  implode('<br>',$joPOReference);
                    $rs[$i]['shipmentnumber'] =  implode('<br>',$joShipmentNumber);
                    $rs[$i]['documentnumber'] = implode('<br>',$joBookingCode); 
 
                    
                    $arrInvoice = $rsInvoice[$rs[$i]['refkey']]; // KALO refkeyny dari UM gk boleh
                    $rs[$i]['invoicedate'] = $arrInvoice['trdate'];
                    $rs[$i]['receiptdate'] = $arrInvoice['receiptdt'];
                    $rs[$i]['receiveddate'] = $arrInvoice['receiveddate'];
                    $rs[$i]['taxvalue'] = $arrInvoice['taxvalue'];
//                    $rs[$i]['tax23value'] = $arrInvoice['tax23value'];
                      
                    $rs[$i]['amount'] += $arrInvoice['tax23value'];
 
                    // isi nilai charges
                    $rsDetailInvoice = $rsDetailInvoiceItem[$rs[$i]['refkey']];// KALO refkeyny dari UM gk boleh
                        
                    foreach($rsDetailInvoice as $row){ 
                         $indexItem = 'itemamount-'.strtolower($row['itemname']); 
                         if(!isset($rs[$i][$indexItem])) $rs[$i][$indexItem]= 0; 
                            $rs[$i][$indexItem] += $row['aftertaxdetailvalue'];  
                    }
      
                }
                
                $rs[$i]['datediff'] = ($rs[$i]['datediff'] > 0) ? $rs[$i]['datediff'] : 0;  
                if ($rs[$i]['datediff']  > 0 ){
                    foreach($arrTemplate[0]['dataStructure'] as $key=>$el) 
                        if (isset($el['dbfield']))
                            $arrHeaderStyle[$el['dbfield']]['textColor'] = 'C41E3A';   
                }else{
                    $arrHeaderStyle['outstanding']['textColor'] = '0093AF';  
                    
                     // nanti baru diupdate
//                    if(count($rsCurrency) > 1){ 
//                        foreach($rsCurrency as $currRow) 
//                             $arrHeaderStyle['outstanding'.$currRow['pkey']]['textColor'] = '0093AF';   
//                    }
                }
         
 
        

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
       
echo $twig->render('reportARSOA.html', $arrTwigVar);   
?>