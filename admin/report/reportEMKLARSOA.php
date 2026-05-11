<?php	 
include '../../_config.php';  
include '../../_include-v2.php';

includeClass('AR.class.php');
$ar = createObjAndAddToCol(new AR()); 

$arPayment = createObjAndAddToCol(new ARPayment()); 
$currency = createObjAndAddToCol(new Currency()); 
$warehouse = createObjAndAddToCol(new Warehouse()); 
$customer = createObjAndAddToCol(new Customer()); 

// khusus trucking / emkl
$isActiveModule = $class->isActiveModule(array('TruckingServiceOrder','EMKLJobOrder'));

if(!empty($isActiveModule['emkljoborder'])){
	$emklOrderInvoice = createObjAndAddToCol(new EMKLOrderInvoice()); 
	$emklJobOrderExport = createObjAndAddToCol(new EMKLJobOrder(EMKL['jobType']['export']));
	$emklJobOrder =  createObjAndAddToCol(new EMKLJobOrder());  
}

if(!empty($isActiveModule['truckingserviceorder'])){ 
	$truckingServiceOrderInvoice = createObjAndAddToCol(new TruckingServiceOrderInvoice());  
}

//$port = createObjAndAddToCol(new Port()); 

include '_global.php';

$obj= $ar;
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


// ===== FOR EXPORT SECTION
$dataToExport = array();

/* data structure */
$arrTemplate = array();

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
//		$arrDataStructure['arType'] = array('title'=>ucwords($obj->lang['transactionType']),  'width'=>"150px", 'dbfield' => 'artypename');
		$arrDataStructure['refcode'] = array('title'=>ucwords($obj->lang['invoiceCode']),  'width'=>"150px", 'dbfield' => 'refcode');
  
		$arrDataStructure['trdate'] = array('title'=>ucwords($obj->lang['date']),'dbfield' => 'trdate', 'width'=>"100px",'format'=>'date'  ,'returnOnEmpty' => array('returnOnEmpty' => true, 'value' => ''));
		$arrDataStructure['duedate'] = array('title'=>ucwords($obj->lang['duedate']),'dbfield' => 'duedate', 'width'=>"100px",'format'=>'date','returnOnEmpty' => array('returnOnEmpty' => true, 'value' => ''));
//		$arrDataStructure['datediff'] = array('title'=>ucwords($obj->lang['aging']),'dbfield' => 'datediff', 'width'=>"70px",'format'=>'number');
		$arrDataStructure['warehouse'] = array('title'=>ucwords($obj->lang['warehouse']),'dbfield' => 'warehousename', 'width'=>"150px");

		$arrDataStructure['customer'] = array('title'=>ucwords($obj->lang['customer']),'dbfield' => 'customername', 'width'=>"200px");
 
//		$arrDataStructure['sales'] = array('title'=>ucwords($obj->lang['salesman']),'dbfield' => 'salesname', 'width'=>"150px");
 
		if(count($rsCurrency) == 1){
//			$arrDataStructure['ammount'] = array('title'=>ucwords($obj->lang['amount']),'dbfield' => 'amount', 'width'=>"130px" ,'format'=>'number','calculateTotal' => true);
			$arrDataStructure['outstanding'] = array('title'=>ucwords($obj->lang['outstanding']),'dbfield' => 'outstanding', 'width'=>"130px" ,'format'=>'number','calculateTotal' => true); 
		}else{ 
			foreach($rsCurrency as $currRow){
//				$arrDataStructure['ammount'.$currRow['pkey']] = array('title'=>ucwords($obj->lang['amount']). ' ' .$currRow['name'],'dbfield' => 'amount'.$currRow['pkey'],"sortable" => false, 'width'=>"130px" ,'format'=>'number','calculateTotal' => true);
				$arrDataStructure['outstanding'.$currRow['pkey']] = array('title'=>ucwords($obj->lang['outstanding']). ' '.$currRow['name'],'dbfield' => 'outstanding'.$currRow['pkey'],"sortable" => false, 'width'=>"130px" ,'format'=>'number','calculateTotal' => true);
			}
		}

//		$arrDataStructure['note'] = array('title'=>ucwords($obj->lang['note']),'dbfield' => 'trdesc','width'=>"300px" );
//		$arrDataStructure['status'] = array('title'=>ucwords($obj->lang['status']),'dbfield' => 'statusname', 'width'=>"100px");

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
//$arrTwigVar['inputStartDate'] = $class->inputDate('trStartDate',array('etc' => 'style="text-align:center"'));
//$arrTwigVar['inputEndDate'] = $class->inputDate('trEndDate',array('etc' => 'style="text-align:center"'));  
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
	
	if(isset($_POST) && !empty($_POST['code'])) {
		$criteria .= ' AND '.$obj->tableName.'.code LIKE ('.$class->oDbCon->paramString('%'.$_POST['code'].'%').')';
		array_push($arrFilterInformation,array("label" => 'Kode', 'filter' => $_POST['code']));
	}
//    
//	if(isset($_POST) && !empty($_POST['trStartDate'])){
//		$criteria .= ' AND '.$obj->tableName.'.trdate between '.$class->oDbCon->paramDate( $_POST['trStartDate'],' / ').' AND '.$class->oDbCon->paramDate( $_POST['trEndDate'],' / ','Y-m-d 23:59'); 
//		array_push($arrFilterInformation,array("label" => 'Tanggal', 'filter' => $_POST['trStartDate'] . ' - ' .$_POST['trEndDate'] ));
//	} 

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

	if(isset($_POST) && !empty($_POST['selWarehouse'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selWarehouse']));   
        
       	$criteria .= ' AND '.$obj->tableEMKLOrderInvoice.'.warehousekey in('.$key.')';  

        $rsCriteria = $warehouse->searchData('','',true, ' and '.$warehouse->tableName.'.pkey in ('.$key.')');
	   
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$statusName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => 'Gudang', 'filter' => $statusName ));
        
	}
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
                
                
//                $rs[$i]['datediff'] = ($rs[$i]['datediff'] > 0) ? $rs[$i]['datediff'] : 0;  
//                if ($rs[$i]['datediff']  > 0 ){
//                    foreach($arrTemplate[0]['dataStructure'] as $key=>$el) 
//                        if (isset($el['dbfield']))
//                            $arrHeaderStyle[$el['dbfield']]['textColor'] = 'C41E3A';   
//                }else{
//                    $arrHeaderStyle['outstanding']['textColor'] = '0093AF';  
//                    
//                    if(count($rsCurrency) > 1){ 
//                        foreach($rsCurrency as $currRow) 
//                             $arrHeaderStyle['outstanding'.$currRow['pkey']]['textColor'] = '0093AF';   
//                    }
//                }
	       
//                if(in_array(PLAN_TYPE['categorykey'], array(COMPANY_TYPE['trucking'],COMPANY_TYPE['forwarding']))){
//                    $refTableTpye = $rs[$i]['reftabletype'];
//                    $salesOrderKey = $rs[$i]['refkey'];
//
//					// consignee
//                    $consigneeInformation = (isset($arrConsignee[$refTableTpye][$salesOrderKey])) ? $arrConsignee[$refTableTpye][$salesOrderKey] : array();
//
//                    $consigneeName=array();
//                    foreach($consigneeInformation as $consigneeRow)
//                        if(!in_array($consigneeRow['name'],$consigneeName))    
//                           array_push($consigneeName,$consigneeRow['name']);
//
//                    $rs[$i]['consigneename'] =  implode('<br>',$consigneeName);
//					
//					// job
//					$jobInformation = (isset($arrJobOrder[$refTableTpye][$salesOrderKey])) ? $arrJobOrder[$refTableTpye][$salesOrderKey] : array();
// 					$joCode =array();
//                    foreach($jobInformation as $jobRow)
//                        if(!in_array($jobRow['jocode'],$joCode))    
//                           array_push($joCode,$jobRow['jocode']);
//
//                    $rs[$i]['jocode'] =  implode('<br>',$joCode);
//                    
//					$containerNumber =array();
//                    foreach($jobInformation as $jobRow)
//                        if(!in_array($jobRow['containernumber'],$containerNumber))    
//                           array_push($containerNumber,$jobRow['containernumber']);
//
//                	$rs[$i]['containernumber'] =  implode('<br>',$containerNumber);
//					
//                }
                
                
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
       
echo $twig->render('reportEMKLARSOA.html', $arrTwigVar);   
?>