<?php

$obj= $ar;
$port = new Port();
$arPayment = $obj->getPaymentObj();
$securityObject = 'reportAR'; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true)); 
  
$_POST['selStatus[]'] = array(1,2); 

$arrFilterInformation = array();   

$detailCriteria = '';

$arrGroupBy = array('1' => $obj->lang['customer'], '2' => $obj->lang['salesman']);



$orderCriteria = array(); 
$orderCriteria['orderBy'] =  (isset ($_POST) && !empty($_POST['hidOrderBy']) ) ?  $obj->oDbCon->paramOrder($_POST['hidOrderBy']) : 'pkey'; //$obj->tableName.'.
$orderCriteria['orderType'] = (isset ($_POST) && !empty($_POST['hidOrderType'])) ?   $_POST['hidOrderType'] : -1;
// ====================== must be set before TWIG


$EMKLInvObj = $class->getTableKeyAndObj($emklOrderInvoice->tableName);
$arrEMKLObj = array(); 
$arrEMKLObj[$EMKLInvObj['key']] = $EMKLInvObj['obj'];
$arrEMKLObjKey = array_keys($arrEMKLObj);

if (!isset($_POST['trStartDate']) || empty($_POST['trStartDate'])){ 
	$_POST['trStartDate'] = date('d / m / Y');
	$_POST['trEndDate'] = date('d / m / Y');
}   

// ===== FOR EXPORT SECTION
$dataToExport = array();

/* data structure */
$arrTemplate = array();

// PRELOAD DATA
$rsJobType = $emklJobOrderExport->getJobType();
$rsJobType = array_column($rsJobType,'name','pkey');
    
$rsPort = $port->searchData();
$rsPort = array_column($rsPort,'name','pkey');

$rsARType = $obj->getARType(); 
define('AR_IMPORT_TYPE',array_column($rsARType,'name','pkey')); 

$arrDataStructure = array();
//$isGrouping = (isset($_POST['isGrouping']) && $_POST['isGrouping'] == 1) ? true : false;
$isShowDetail = (isset($_POST['isShowDetail']) && $_POST['isShowDetail'] == 1) ? true : false;
$_POST['module'] = IMPORT_TEMPLATE['ar'];

$rsCurrency = $currency->searchData($currency->tableName.'.statuskey',1,true,'','order by name asc');
 
switch($EXPORT_TYPE){

	case 2 :
		$arrDataStructure['code'] = array('title'=>ucwords($obj->lang['code']),  'width'=>"100px", 'dbfield' => 'code');
		$arrDataStructure['arType'] = array('title'=>ucwords($obj->lang['type']),  'width'=>"250px", 'dbfield' => 'artypename');
		$arrDataStructure['customer'] = array('title'=>ucwords($obj->lang['customer']),'dbfield' => 'customername', 'width'=>"170px" );
		$arrDataStructure['date'] = array('title'=>ucwords($obj->lang['date']),'dbfield' => 'trdate', 'width'=>"120px",'format'=>'date');
//		$arrDataStructure['ammount'] = array('title'=>ucwords($obj->lang['amount']),'dbfield' => 'amount', 'width'=>"130px" ,'format'=>'number');

		break;

	default :
 
  			$arrDataStructure['rowNumber'] = array('title'=>'#', 'align'=>'right', 'width'=>"40px", 'autoNumber' => true,"sortable" => false);
            $arrDataStructure['code'] = array('title'=>ucwords($obj->lang['code']),  'width'=>"100px", 'dbfield' => 'code');
    	    $arrDataStructure['customer'] = array('title'=>ucwords($obj->lang['customer']),'dbfield' => 'customername', 'width'=>"200px");
        	$arrDataStructure['undername'] = array('title'=>ucwords($obj->lang['undername']),'dbfield' => 'undername', 'width'=>"200px");
        	//$arrDataStructure['arType'] = array('title'=>ucwords($obj->lang['transactionType']),  'width'=>"150px", 'dbfield' => 'artypename');
            //$arrDataStructure['refcode'] = array('title'=>ucwords($obj->lang['refCode']),  'width'=>"130px", 'dbfield' => 'refcode');
            $arrDataStructure['trdate'] = array('title'=>ucwords($obj->lang['date']),'dbfield' => 'trdate', 'width'=>"100px",'format'=>'date','returnOnEmpty' => array('returnOnEmpty' => true, 'value' => ''));
            $arrDataStructure['duedate'] = array('title'=>ucwords($obj->lang['duedate']),'dbfield' => 'duedate', 'width'=>"100px",'format'=>'date','returnOnEmpty' => array('returnOnEmpty' => true, 'value' => ''));
            $arrDataStructure['datediff'] = array('title'=>ucwords($obj->lang['aging']),'dbfield' => 'datediff', 'width'=>"70px",'format'=>'number');
            //$arrDataStructure['warehouse'] = array('title'=>ucwords($obj->lang['warehouse']),'dbfield' => 'warehousename', 'width'=>"150px");
            
            $arrDataStructure['invoicenumber'] = array('title'=>ucwords($obj->lang['invoiceNumber']),  'width'=>"130px", 'dbfield' => 'invoicenumber',"sortable" => false);
            $arrDataStructure['soCode'] = array('title'=>ucwords($obj->lang['soCode']),  'width'=>"130px", 'dbfield' => 'jocode',"sortable" => false);
            $arrDataStructure['poCode'] = array('title'=>ucwords($obj->lang['poReference']),  'width'=>"130px", 'dbfield' => 'referencepo',"sortable" => false);
            $arrDataStructure['mblnumber'] = array('title'=>ucwords($obj->lang['mblNumber']),'dbfield' => 'mblnumber', 'width'=>"170px" , "sortable" => false);
            $arrDataStructure['pod'] = array('title'=> 'POD','dbfield' => 'pod', 'width'=>"150px" , "sortable" => false);
            $arrDataStructure['containernumber'] = array('title'=>ucwords($obj->lang['containerNumber']),'dbfield' => 'containernumber', 'width'=>"170px" , "sortable" => false);
            $arrDataStructure['etddate'] = array('title'=>ucwords($obj->lang['etd']),'dbfield' => 'etd', 'width'=>"100px", 'align'=> 'center', "sortable" => false );
            $arrDataStructure['invoicedate'] = array('title'=>ucwords($obj->lang['invoiceDate']),'dbfield' => 'invoicedate', 'width'=>"100px", 'align'=> 'center', "sortable" => false );
            $arrDataStructure['receiptdate'] = array('title'=>ucwords($obj->lang['receiptDate']),'dbfield' => 'receiptdt', 'width'=>"100px", 'align'=> 'center', "sortable" => false) ;
            $arrDataStructure['shipment'] = array('title'=>ucwords($obj->lang['shipment']),'dbfield' => 'shipmenttype', 'width'=>"100px", "sortable" => false );
            $arrDataStructure['beforetaxtotal'] = array('title'=>ucwords($obj->lang['beforeTax']),  'width'=>"130px", 'dbfield' => 'beforetaxtotal','format'=>'number',"sortable" => false);
            $arrDataStructure['taxvalue'] = array('title'=>ucwords($obj->lang['tax']),  'width'=>"130px", 'dbfield' => 'taxvalue','format'=>'number',"sortable" => false);
            //$arrDataStructure['amount'] = array('title'=>ucwords($obj->lang['amount']),  'width'=>"130px", 'dbfield' => 'amountidr','format'=>'number','calculateTotal' => true);
            //$arrDataStructure['aftertax'] = array('title'=>ucwords($obj->lang['total']),  'width'=>"130px", 'dbfield' => 'aftertax','format'=>'number',"sortable" => false);
             
            //$arrDataStructure['si'] = array('title'=>ucwords($obj->lang['si']),'dbfield' => 'refcode2', 'width'=>"300px");
           
            if(count($rsCurrency) == 1){
                $arrDataStructure['ammount'] = array('title'=>ucwords($obj->lang['amount']),'dbfield' => 'amount', 'width'=>"130px" ,'format'=>'number','calculateTotal' => true);
                $arrDataStructure['outstanding'] = array('title'=>ucwords($obj->lang['outstanding']),'dbfield' => 'outstanding', 'width'=>"130px" ,'format'=>'number','calculateTotal' => true); 
            }else{ 
                foreach($rsCurrency as $currRow){
                   // $arrDataStructure['ammount'.$currRow['pkey']] = array('title'=>ucwords($obj->lang['amount']). ' ' .$currRow['name'],'dbfield' => 'amount'.$currRow['pkey'],"sortable" => false, 'width'=>"130px" ,'format'=>'number','calculateTotal' => true);
                    $arrDataStructure['outstanding'.$currRow['pkey']] = array('title'=>ucwords($obj->lang['outstanding']). ' '.$currRow['name'],'dbfield' => 'outstanding'.$currRow['pkey'],"sortable" => false, 'width'=>"130px" ,'format'=>'number','calculateTotal' => true);
                }
            }
            
                 
            $arrDataStructure['tax23'] = array('title'=>ucwords($obj->lang['tax23']),'dbfield' => 'tax23value', 'width'=>"100px" ,'format'=>'number');
            $arrDataStructure['paidAmount'] = array('title'=>ucwords($obj->lang['paidAmount']),'dbfield' => 'paidamount', 'width'=>"130px" ,'format'=>'number');
        
            //$arrDataStructure['note'] = array('title'=>ucwords($obj->lang['note']),'dbfield' => 'trdesc','width'=>"300px" );
            $arrDataStructure['status'] = array('title'=>ucwords($obj->lang['status']),'dbfield' => 'statusname', 'width'=>"100px");

}


$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $obj->lang['ARSOAReport']; 
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();

array_push($arrTemplate, $arrHeaderTemplate);


$arrStatus = $class->convertForCombobox($obj->getAllStatus(),'pkey','status');
$arrWarehouse = $class->convertForCombobox($warehouse->searchData($warehouse->tableName.'.statuskey',1,true,'','order by name asc'),'pkey','name');
$arrCustomer = $class->convertForCombobox($customer->searchData($customer->tableName.'.statuskey',2,true,'','order by name asc'),'pkey','name');
$arrCurrency = $class->convertForCombobox($rsCurrency,'pkey','name');
//$arrTemplateCustomer = $class->convertForCombobox($templateCustomer->searchData($templateCustomer->tableName.'.statuskey',1,true,'','order by name asc'),'pkey','name','- ' .$obj->lang['chooseTemplate'].' -');
//$arrType = $class->convertForCombobox($rsARType,'pkey','name');
//$arrEmployee = $class->convertForCombobox($employee->searchData($employee->tableName.'.statuskey',2,true, ' and '.$employee->tableName.'.issales = 1 ' ),'pkey','name');   

//$arrTwigVar['inputCode'] =  $class->inputText('code');
//$arrTwigVar['inputShowDetail'] =  $class->inputCheckBox('isShowDetail');
//$arrTwigVar['inputHidCustomerKey'] = $class->inputHidden('hidCustomerKey');
//$arrTwigVar['inputCustomerName'] =  $class->inputText('customerName');
$arrTwigVar['inputStartDate'] = $class->inputDate('trStartDate',array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputEndDate'] = $class->inputDate('trEndDate',array('etc' => 'style="text-align:center"'));  
$arrTwigVar['inputSelStatus'] =  $class->inputSelect('selStatus[]', $arrStatus, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox')); 
$arrTwigVar['inputSelCurrency'] =  $class->inputSelect('selCurrency[]', $arrCurrency, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox')); 
//$arrTwigVar['inputChkDueDate'] =  $class->inputCheckBox('chkDueDate',array('overwritePost' => false, 'value' => 0, 'class' => 'no-class'));  
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
//$arrTwigVar['inputSelARType'] =  $class->inputSelect('selARType[]', $arrType, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
//$arrTwigVar['inputIsGrouping'] =  $class->inputCheckBox('isGrouping'); 
//$arrTwigVar['inputGroupBy'] =  $class->inputSelect('selGroupBy',$arrGroupBy); 
//$arrTwigVar['inputSelTemplateCustomer'] =  $class->inputSelect('selTemplateCustomer',$arrTemplateCustomer);	
//$arrTwigVar['inputSalesName'] =  $class->inputSelect('selSales[]', $arrEmployee, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['order'] =  $orderCriteria;
//$arrTwigVar['inputSI'] = $class->inputText('si'); 
$arrTwigVar['PLAN_TYPE'] = PLAN_TYPE;
$arrTwigVar['COMPANY_TYPE'] = COMPANY_TYPE;
$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate; 


if (isset($_POST) && !empty($_POST['hidAction'])){   
	
	$criteria = '';
	
//	if(isset($_POST) && !empty($_POST['code'])) {
//		$criteria .= ' AND '.$obj->tableName.'.code LIKE ('.$class->oDbCon->paramString('%'.$_POST['code'].'%').')';
//		array_push($arrFilterInformation,array("label" => 'Kode', 'filter' => $_POST['code']));
//	}

	if(isset($_POST) && !empty($_POST['trStartDate'])){
		
		$criteria .= ' AND '.$obj->tableEMKLOrderInvoice.'.trdate between '.$class->oDbCon->paramDate( $_POST['trStartDate'],' / ').' AND '.$class->oDbCon->paramDate( $_POST['trEndDate'],' / ','Y-m-d 23:59'); 
		array_push($arrFilterInformation,array("label" => 'Tanggal', 'filter' => $_POST['trStartDate'] . ' - ' .$_POST['trEndDate'] ));
	} 

	/*if(isset($_POST) && !empty($_POST['customerName'])) {
		$criteria .= ' AND '.$obj->tableCustomer.'.name LIKE ('.$class->oDbCon->paramString('%'.$_POST['customerName'].'%').')';
		array_push($arrFilterInformation,array("label" => 'Nama Pelanggan', 'filter' =>  $_POST['customerName']));
	}*/
    
    
    if(isset($_POST) && !empty($_POST['selCustomer'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selCustomer']));   
        
       	$criteria .= ' AND '.$obj->tableEMKLOrderInvoice.'.customerkey in('.$key.')';  

        $rsCriteria = $customer->searchData('','',true, ' and '.$customer->tableName.'.pkey in ('.$key.')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$statusName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => 'Pelangan', 'filter' => $statusName ));
        
	}	
    
//     if(isset($_POST) && !empty($_POST['selSales'])) { 
//        
//        $key = implode(",", $class->oDbCon->paramString($_POST['selSales']));   
//        
//       	$criteria .= ' AND '.$obj->tableEMKLOrderInvoice.'.saleskey in('.$key.')';  
//
//        $rsCriteria =  $employee->searchData('','',true, ' and '.$employee->tableName.'.pkey in ('.$key.')');
//	 
//        $arrTempStatus = array();
//		for ($k=0;$k<count($rsCriteria);$k++)
//		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
//			
//		$salesName = implode(", ",$arrTempStatus); 
//	    array_push($arrFilterInformation,array("label" => 'Sales', 'filter' => $salesName));
//        
//	}

    
    if(isset($_POST) && !empty($_POST['selCurrency'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selCurrency']));   
        
       	$criteria .= ' AND '.$obj->tableEMKLOrderInvoice.'.currencykey in('.$key.')';  

        $rsCriteria = $currency->searchData('','',true, ' and '.$currency->tableName.'.pkey in ('.$key.')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$statusName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => 'Mata Uang', 'filter' => $statusName ));
        
	}	
    

//    if(isset($_POST) && !empty($_POST['chkDueDate'])){ 
//			if($isGrouping){ 
//				$detailCriteria .= ' and DATEDIFF(NOW(),duedate) > 0';
//				$criteria .= ' and DATEDIFF(NOW(),duedate) > 0';
//            }else{
//				$criteria .= ' having datediff > 0';
//			} 
//            
//			array_push($arrFilterInformation,array("label" => 'Aging', 'filter' => 'Tampilkan hanya yang jatuh tempo'));
//    }

//	if(isset($_POST) && !empty($_POST['selWarehouse'])) { 
//        
//        $key = implode(",", $class->oDbCon->paramString($_POST['selWarehouse']));   
//        
//       	$criteria .= ' AND '.$obj->tableEMKLOrderInvoice.'.warehousekey in('.$key.')';  
//
//        $rsCriteria = $warehouse->searchData('','',true, ' and '.$warehouse->tableName.'.pkey in ('.$key.')');
//	   
//        $arrTempStatus = array();
//		for ($k=0;$k<count($rsCriteria);$k++)
//		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
//			
//		$statusName = implode(", ",$arrTempStatus); 
//	    array_push($arrFilterInformation,array("label" => 'Gudang', 'filter' => $statusName ));
//        
//	}
//    
//    if(isset($_POST) && !empty($_POST['selARType'])) { 
//         
//       	$criteria .= ' AND '.$obj->tableName.'.artype in('.$class->oDbCon->paramString($_POST['selARType'],',').')';  
//        
//        $rsCriteria = $obj->getARTypeName($_POST['selARType']);
//	 
//        $arrTempStatus = array();
//		for ($k=0;$k<count($rsCriteria);$k++)
//		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
//			
//		$statusName = implode(", ",$arrTempStatus); 
//	    array_push($arrFilterInformation,array("label" => $obj->lang['transactionType'], 'filter' => $statusName ));
//        
//	}
    

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
        if(isset($_POST) && !empty($_POST['si'])) { 
        $criteria .= ' AND '.$obj->tableName.'.refcode2  LIKE ('.$class->oDbCon->paramString('%'.$_POST['si'].'%').')';
	    array_push($arrFilterInformation,array("label" => 'S/I', 'filter' => $_POST['si']));
	}

    $orderBy = (!empty($_POST['hidOrderBy'])) ? $obj->oDbCon->paramOrder($_POST['hidOrderBy']) : 'pkey'; // order by harus dr kolom yg terdaftar saja
    $orderType = (isset($_POST['hidOrderType']) && !empty($_POST['hidOrderType']) && $_POST['hidOrderType'] == 1) ? 'desc' : 'asc';

    $order = 'order by '.$orderBy.' ' .$orderType;  
 
	$rs = $obj->getEMKLARSOAReport($criteria,$order);
	  
    $arrConsignee = array();
    $arrJobOrder = array();
    
	 
	$tempreport = '';  
    $totalRs = count($rs);
	 
	// kumpulin dulu yang jenisnya emklObj
	$arrEMKLSalesOrderkey = array();
	for( $i=0;$i<$totalRs;$i++) {
	   if(in_array($rs[$i]['reftabletype'],$arrEMKLObjKey)){
		   	array_push($arrEMKLSalesOrderkey, $rs[$i]['refheaderkey'] );
	   }
	}
	
	$rsInvoiceCol = $emklOrderInvoice->searchDataRow(array($emklOrderInvoice->tableName.'.pkey',
												   $emklOrderInvoice->tableName.'.code',
												   $emklOrderInvoice->tableName.'.undername',
												   $emklOrderInvoice->tableName.'.trdate',
												   $emklOrderInvoice->tableName.'.receiptdt',
												   $emklOrderInvoice->tableName.'.beforetaxtotal',
												   $emklOrderInvoice->tableName.'.taxvalue',
												   $emklOrderInvoice->tableName.'.tax23value' 
												  ),
											 	' and '.$emklOrderInvoice->tableName.'.pkey in ('.$class->oDbCon->paramString( $arrEMKLSalesOrderkey,',' ).')'
											);  
	
	
	$rsInvoiceCol = array_column($rsInvoiceCol,null,'pkey');
	
	for( $i=0;$i<$totalRs;$i++) {   
            $arrHeaderStyle = array();
   
 
                if(count($rsCurrency) >= 1){
                    foreach($rsCurrency as $currRow){
                        $rs[$i]['amount'.$currRow['pkey']] = 0;
                        $rs[$i]['outstanding'.$currRow['pkey']] = 0;
                        
                        $currencykey = $rs[$i]['currencykey'];
                        $rs[$i]['amount'.$currencykey] = $rs[$i]['amount'];
                        $rs[$i]['outstanding'.$currencykey] = $rs[$i]['outstanding'];
                    }
                }
                
                 
		  
                if(in_array($rs[$i]['reftabletype'],$arrEMKLObjKey)){
                 
                   $reftabletype = $rs[$i]['reftabletype'];
                   $refheaderkey = $rs[$i]['refheaderkey'];
                    
                   $refObj = $arrEMKLObj[$reftabletype];
                   
                   // ambil data dari PO / Refund utk dapetin refkey agar bisa link ke JO
                   $rsObj =  $rsInvoiceCol[$refheaderkey];
				   // $refObj->getDataRowById($refheaderkey);  
                   $rsInvoiceDetail = $emklOrderInvoice->getDetailWithRelatedInformation($rsObj['pkey']);
                    
                   $arrJO = array();   
                   $arrJO['code'] = array();  
                   $arrJO['container'] = array();  
                   $arrJO['mbl'] = array();  
                   $arrJO['etd'] = array(); 
                   $arrJO['type'] = array();
                   $arrJO['shipmenttype'] = array();
                   $arrJO['pod'] = array();
                   $arrJO['ponumber'] = array();
                 
                    for($j=0;$j<count($rsInvoiceDetail);$j++){
                        $rsJO = $emklJobOrder->getDataRowById($rsInvoiceDetail[$j]['refsalesorderheaderkey']);
                            
                        //$rsJODetail = $emklJobOrder->getDetailById($rsInvoiceDetail[$j]['refsalesorderheaderkey']);
                        array_push($arrJO['code'],$rsJO[0]['code']); 
                        array_push($arrJO['container'],$rsJO[0]['containernumber']);
                        array_push($arrJO['mbl'],$rsJO[0]['mblnumber']);
                        array_push($arrJO['ponumber'],$rsJO[0]['ponumber']);
                        
                        if(isset($rsPort[$rsJO[0]['podkey']]))
                            array_push($arrJO['pod'],$rsPort[$rsJO[0]['podkey']]);
                        
                        if(isset($rsJobType[$rsJO[0]['jobtypekey']])) 
                            array_push($arrJO['shipmenttype'], $rsJobType[$rsJO[0]['jobtypekey']]);
                        
                        array_push($arrJO['etd'],$emklJobOrder->formatDBDate($rsJO[0]['etdpol']));

                    }
                    
                   $grandtotal = 0;
                   $rs[$i]['invoicenumber'] = $rsObj['code'];
                   $rs[$i]['undername'] = (isset( $rsObj['undername'])) ? $rsObj['undername'] : '' ;
                   $rs[$i]['invoicedate'] = $obj->formatDBDate($rsObj['trdate']);
                   $rs[$i]['receiptdt'] = (empty($rsObj['receiptdt']) || in_array($rsObj['receiptdt'], array('1970-01-01','0000-00-00'))) ? '' : $obj->formatDBDate($rsObj['receiptdt']);
                   $rs[$i]['beforetaxtotal'] = $rsObj['beforetaxtotal'];
                   $rs[$i]['taxvalue'] = $rsObj['taxvalue'];
                    
                   $tax23Value = $rsObj['tax23value'] - $obj->getARPrepaidTaxAmount($rs[$i]['pkey']);
                   $rs[$i]['tax23value'] = $tax23Value;
                    
                   $rs[$i]['jocode'] = implode('<br>',$arrJO['code']);
                   $rs[$i]['etd'] =  implode('<br>',$arrJO['etd']);
                   $rs[$i]['shipmenttype'] =  implode('<br>',$arrJO['shipmenttype']);
                   $rs[$i]['containernumber'] = implode('<br>',$arrJO['container']);
                   $rs[$i]['mblnumber'] = implode('<br>',$arrJO['mbl']);
                   $rs[$i]['pod'] = implode('<br>',$arrJO['pod']);
                   $rs[$i]['referencepo'] = implode('<br>',$arrJO['ponumber']); 
              

					 if ($rs[$i]['datediff']  > 0 ){
						foreach($arrTemplate[0]['dataStructure'] as $key=>$el) 
							if (isset($el['dbfield']))
								$arrHeaderStyle[$el['dbfield']]['textColor'] = 'C41E3A';   
					}else{
						$arrHeaderStyle['outstanding']['textColor'] = '0093AF';  

						if(count($rsCurrency) > 1){ 
							foreach($rsCurrency as $currRow) 
								 $arrHeaderStyle['outstanding'.$currRow['pkey']]['textColor'] = '0093AF';   
						}
					}

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
       
echo $twig->render('@custom/reportEMKLARSOA.html', $arrTwigVar);   
?>