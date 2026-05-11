<?php
	 
include '../../_config.php'; 
include '../../_include-v2.php';


includeClass('EMKLJobOrder.class.php');
$emklJobOrderExport = createObjAndAddToCol(new EMKLJobOrder());
$container = createObjAndAddToCol(new Container());
$currency = createObjAndAddToCol(new Currency());
$customer = createObjAndAddToCol(new Customer());
$warehouse = createObjAndAddToCol(new Warehouse());
include '_global.php';

$obj = $emklJobOrderExport;
$securityObject = 'reportSalesOrderExportFF'; // the value of security object is manually inserted to handle 
								  // some modules that have different security object from that of their class
								  
if(!$security->isAdminLogin($securityObject,10,true));  

$arrFilterInformation = array();
$detailCriteria = '';
$_POST['selStatus[]'] = array(2,3); 

if(!isset($_POST['selDateType']) || empty($_POST['selDateType']))  $_POST['selDateType'] = 2;
if(!isset($_POST['selInvoiceDateType']) || empty($_POST['selInvoiceDateType']))  $_POST['selInvoiceDateType'] = 2;

$arrDateType= array(
    '1' => $obj->lang['transactionDate'],
    '2' => 'ETD',
    '3' => 'ETA'
);

$arrInvoiceDateType = array(
    '1' => $obj->lang['confirmedDate'],
    '2' => $obj->lang['receiptDate'],
);

// ===== FOR EXPORT SECTION
$dataToExport = array();

/* data structure */
$arrTemplate = array();
$isShowDetail = (isset($_POST['isShowDetail']) && !empty($_POST['isShowDetail'])) ? true : false;

$arrDataStructure = array();
$arrDataStructure['rowNumber'] = array('title'=>'#', 'align'=>'right', 'width'=>"40px", 'autoNumber' => true, "sortable" => false);
$arrDataStructure['code'] = array('title'=>ucwords($obj->lang['code']), 'width'=>"150px", 'dbfield' => 'code'); 
$arrDataStructure['date'] = array('title'=>ucwords($obj->lang['date']), 'width'=>"90px", 'dbfield' => 'trdate','format'=>'date'); 
$arrDataStructure['customer'] = array('title'=>ucwords($obj->lang['shipper']),'dbfield' => 'customername', 'width'=>"250px");
$arrDataStructure['etd'] = array('title'=>ucwords($obj->lang['etd']),'dbfield' => 'etdpol', 'width'=>"90px",'format'=>'date');
$arrDataStructure['eta'] = array('title'=>ucwords($obj->lang['eta']),'dbfield' => 'etapod', 'width'=>"90px",'format'=>'date');
$arrDataStructure['invoicedCode'] = array('title'=>ucwords($obj->lang['invoiceCode']),'dbfield' => 'invoicecode', 'width'=>"150px", "sortable" => false); 
$arrDataStructure['invoicedDate'] = array('title'=>ucwords($obj->lang['confirmedDate']),'dbfield' => 'invoiceconfirmedon', 'width'=>"90px",'align'=>'center', "sortable" => false); 
$arrDataStructure['invoicedReceiptDate'] = array('title'=>ucwords($obj->lang['receiptDate']),'dbfield' => 'invoicereceiptdate', 'width'=>"90px",'align'=>'center', "sortable" => false); 
$arrDataStructure['datediff'] = array('title'=>ucwords($obj->lang['days']),'dbfield' => 'datediff', 'width'=>"70px",'align'=>'center', "sortable" => false); 

$arrDataStructure['status'] = array('title'=>ucwords($obj->lang['status']),'dbfield' => 'statusname', 'width'=>"100px");
		   
$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] =  $obj->lang['salesOrderToInvoiceReport']; 
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();

array_push($arrTemplate, $arrHeaderTemplate);
    
if (isset($_POST) && !empty($_POST['hidAction'])){  
		
	$criteria = '';
	if(isset($_POST) && !empty($_POST['code'])) {
		$criteria .= ' AND '.$obj->tableName.'.code LIKE ('.$class->oDbCon->paramString('%'.$_POST['code'].'%').')';
		array_push($arrFilterInformation,array("label" => 'Kode', 'filter' => $_POST['code']));
	}
	
    if(isset($_POST) && !empty($_POST['trStartDate'])){
        switch($_POST['selDateType']){
            case '1' : $fieldName = $obj->tableName.'.trdate';  break;
            case '2' : $fieldName = $obj->tableName.'.etdpol'; break;
            case '3' : $fieldName = $obj->tableName.'.etapod'; break;
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
    
	
	if(isset($_POST) && !empty($_POST['selEmployee'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selEmployee']));   
        
       	$criteria .= ' AND '.$obj->tableName.'.saleskey in('.$key.')';  

        $rsCriteria = $employee->searchData('','',true, ' and '.$employee->tableName.'.pkey in ('.$key.')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$statusName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => $obj->lang['salesman'], 'filter' => $statusName ));
        
	}

      
    if(isset($_POST) && !empty($_POST['selCustomer'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selCustomer']));   
       	$criteria .= ' AND '.$obj->tableName.'.customerkey in('.$key.')';  

        $rsCriteria = $customer->searchData('','',true, ' and '.$customer->tableName.'.pkey in ('.$key.')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$customerName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" =>$obj->lang['customer'], 'filter' => $customerName ));
        
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

    if(isset($_POST) && !empty($_POST['selContainer'])) { 
        
        $key = $_POST['selContainer'];  
       	$criteria .= ' AND '.$obj->tableName.'.containertypekey in ('.$class->oDbCon->paramString($key,',').')';  

        $rsCriteria = $container->getContainerType($key);
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$statusName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" =>$obj->lang['containerType'], 'filter' => $statusName ));
        
	} 

    $orderBy = (!empty($_POST['hidOrderBy'])) ? $obj->oDbCon->paramOrder($_POST['hidOrderBy']) : 'etdpol'; // order by harus dr kolom yg terdaftar saja
    $orderType = (isset($_POST['hidOrderType']) && !empty($_POST['hidOrderType']) && $_POST['hidOrderType'] == 1) ? 'desc' : 'asc';
      
	$order = 'order by '.$orderBy.' ' .$orderType; 
	$rs = $obj->searchData('','',true,$criteria,$order);
        
    //$totalCurr = count(array_unique(array_column($rs,'currencykey'))); 
    //$calculateTotal = ($totalCurr <= 1) ? true : false;
        
    $arrKeys = array_column($rs,'pkey');
        
    // total invvoiced
    $rsInvoiceInformation = $obj->getAmountInvoiced($arrKeys);
    $rsInvoiceInformation = $obj->reindexDetailCollections($rsInvoiceInformation,'refsalesorderheaderkey');    
  
    $tempreport = '';

    
    $totalRs = count($rs);
    
    // tentuin patokan tgl
     switch($_POST['selDateType']){
        case '1' : $dateFromTrans ='trdate';  break;
        case '2' : $dateFromTrans = 'etdpol'; break;
        case '3' : $dateFromTrans = 'etapod'; break;
        default : $dateFromTrans = 'trdate';  break; 
    }
    
    $deadline = (isset($_POST['inputDeadline'])) ? $_POST['inputDeadline'] : 0;
    
    for( $i=0;$i<$totalRs;$i++) {   
        
        $sokey = $rs[$i]['pkey'];
        
        $arrHeaderStyle = array();
           
        $rs[$i]['code'] = '<a href="/admin/print/emklJobOrderExport/'.$rs[$i]['pkey'].'" target="_blank">'.$rs[$i]['code'].'</a>';
        $rs[$i]['totalinvoiced'] = 0;
        $rs[$i]['totalbeforetaxinvoiced'] = 0;
        $rs[$i]['totaltax23value'] = 0;
        $arrStatus = array();  
        $arrInvoiceCode = array();
        $arrInvoiceDate = array();
        $arrReceiptDate = array();
        $arrDateDiff = array();
        
        $hasOutstanding = false;
        
        $dateFrom = $obj->formatDBDate($rs[$i][$dateFromTrans],'d / m / Y');
        
        $hasDaedline = false;
        
        if(isset($rsInvoiceInformation[$sokey])){
            foreach($rsInvoiceInformation[$sokey] as $invoiceRow){ 
                 array_push($arrInvoiceCode, $invoiceRow['code']);  
                 
                 array_push($arrInvoiceDate, $obj->formatDBDate($invoiceRow['confirmedon'])); 
                 array_push($arrReceiptDate, $obj->formatDBDate($invoiceRow['receiptdt'],'d / m / Y', array('returnOnEmpty'=>true, 'value' => ''))); 
                 
                 $dateTo = ($_POST['selInvoiceDateType'] == 1) ? $invoiceRow['confirmedon'] : $invoiceRow['receiptdt'] ;
                 if ($dateTo == '0000-00-00' || $dateTo == '1970-01-01' || empty($dateTo))
                     $dateTo = date('Y-m-d');
                     
                 $dateTo =  $obj->formatDBDate($dateTo,'d / m / Y');
                 $dateDiff = $obj->dateDiff($dateFrom,$dateTo); 
                 $dateDiff /= 86400;
                 array_push($arrDateDiff,$dateDiff);
                
                if($dateDiff > $deadline) $hasDaedline = true;
            } 
        }else{
             // gkbisa digabung dengan atas karena diatas looping
             $dateTo = date('d / m / Y');
             $dateDiff = $obj->dateDiff($dateFrom,$dateTo); 
             $dateDiff /= 86400;
             array_push($arrDateDiff,$dateDiff);
            
            if($dateDiff > $deadline) $hasDaedline = true;
        }

        if ($hasDaedline){
            foreach($arrTemplate[0]['dataStructure'] as $key=>$el) 
                if (isset($el['dbfield']))
                    $arrHeaderStyle[$el['dbfield']]['textColor'] = 'C41E3A';   
        }
        
        
        $rs[$i]['invoicecode'] = implode('<br>',$arrInvoiceCode);
        $rs[$i]['invoiceconfirmedon'] = implode('<br>',$arrInvoiceDate);
        $rs[$i]['invoicereceiptdate'] = implode('<br>',$arrReceiptDate);
        $rs[$i]['datediff'] = implode('<br>',$arrDateDiff);
       
        // has detail
        // sementara, nanti kal osudah pake shipperkey, gk perlu lg, krena gk ad pencarian didetail lg
        if($isShowDetail || !empty($detailCriteria)){  
            $rsDetail = $rsDetailCol[$rs[$i]['pkey']]; 
            
            if(!empty($detailCriteria) && empty($rsDetail))  continue;
              
            if($isShowDetail) 
                $rs[$i]['_detail_'] = array('arrTemplate'=>$arrDetailTemplate,'data' => $rsDetail); 
        }
          
        //$arrTemplate[0]['dataStructure']['total']['calculateTotal'] = $calculateTotal;
        
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
 
$arrWarehouse = $class->convertForCombobox($warehouse->searchData($warehouse->tableName.'.statuskey',1,true,'','order by name asc'),'pkey','name');
$arrCurrency = $class->convertForCombobox($currency->searchData($currency->tableName.'.statuskey',1,true,'','order by name asc'),'pkey','name');
$arrStatus = $class->convertForCombobox($obj->getAllStatus(),'pkey','status');  
$arrContainer = $class->convertForCombobox($container->getContainerType(),'pkey','name');   
$arrEmployee = $class->convertForCombobox($employee->searchData($employee->tableName.'.statuskey',2,true, ' and issales = 1'),'pkey','name');
$arrCustomer = $class->convertForCombobox($customer->searchData($customer->tableName.'.statuskey',2,true,'','order by name asc'),'pkey','name');
      
$arrTwigVar['inputCode'] =  $class->inputText('code');
$arrTwigVar['inputSelWarehouse'] =  $class->inputSelect('selWarehouse[]', $arrWarehouse, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));  
$arrTwigVar['inputSelCustomer'] =  $class->inputSelect('selCustomer[]', $arrCustomer, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));  

$arrTwigVar['inputSelDateType'] =  $class->inputSelect('selDateType', $arrDateType);  
$arrTwigVar['inputSelInvoiceDateType'] =  $class->inputSelect('selInvoiceDateType', $arrInvoiceDateType);  
$arrTwigVar['inputSelStatus'] =  $class->inputSelect('selStatus[]', $arrStatus, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputStartDate'] = $class->inputDate('trStartDate',array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputEndDate'] = $class->inputDate('trEndDate',array('etc' => 'style="text-align:center"'));   
$arrTwigVar['inputBookingNumber'] =  $class->inputText('bookingNumber');
$arrTwigVar['inputMblNumber'] =  $class->inputText('mblNumber');
$arrTwigVar['inputDeadline'] =  $class->inputNumber('inputDeadline',array('value' => 5));
$arrTwigVar['inputSelContainer'] =  $class->inputSelect('selContainer[]', $arrContainer, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));  
$arrTwigVar['inputSelEmployee'] =  $class->inputSelect('selEmployee[]', $arrEmployee, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));  
$arrTwigVar['inputIsShowDetail'] =  $class->inputCheckBox('isShowDetail');
$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate;    

echo $twig->render('reportEMKLJobOrderToInvoice.html', $arrTwigVar);  
 
?>