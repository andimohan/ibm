<?php
	 
include '../../_config.php';  
include '../../_include-v2.php';

includeClass('SalesOrderSubscription.class.php');
$salesOrderSubscription = createObjAndAddToCol( new SalesOrderSubscription()); 
$installationWorkOrder = createObjAndAddToCol( new InstallationWorkOrder()); 
$warehouse = createObjAndAddToCol( new Warehouse()); 
$customer = createObjAndAddToCol( new Customer());
$stagesProcess = createObjAndAddToCol( new StagesProcess());
$jobDetails = createObjAndAddToCol( new JobDetails());
$media = createObjAndAddToCol( new Media());
$location = createObjAndAddToCol( new Location());

include '_global.php';

$obj= $salesOrderSubscription;
$securityObject = 'reportSalesOrderSubscription'; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true)); 

$_POST['selStatus[]'] = array(2,3);
$isInvoiced = (isset($_POST['isInvoiced']) && $_POST['isInvoiced'] == 1) ? true : false;
$arrFilterInformation = array(); 
$detailCriteria = '';

$dataToExport = array();

/* data structure */
$arrTemplate = array();

$arrDataStructure = array();
$arrDataStructure['rowNumber'] = array('title'=>'#', 'align'=>'right', 'width'=>"40px", 'autoNumber' => true, "sortable" => false);
$arrDataStructure['code'] = array('title'=>ucwords($obj->lang['code']),  'width'=>"150px", 'dbfield' => 'code'); 
$arrDataStructure['date'] = array('title'=>ucwords($obj->lang['date']),'dbfield' => 'trdate', 'width'=>"120px",'format'=>'date');
$arrDataStructure['warehouse'] = array('title'=>ucwords($obj->lang['warehouse']),'dbfield' => 'warehousename', 'width'=>"90px");
$arrDataStructure['customer'] = array('title'=>ucwords($obj->lang['customer']),'dbfield' => 'customername', 'width'=>"250px");
//$arrDataStructure['pic'] = array('title'=>ucwords($obj->lang['PIC']),'dbfield' => 'employeename', 'width'=>"110px");
$arrDataStructure['media'] = array('title'=>ucwords($obj->lang['media']),'dbfield' => 'medianame', 'width'=>"110px");
$arrDataStructure['attention'] = array('title'=>ucwords($obj->lang['attention']),'dbfield' => 'attention', 'width'=>"110px");
$arrDataStructure['phone'] = array('title'=>ucwords($obj->lang['phone']),'dbfield' => 'phone', 'width'=>"110px");
$arrDataStructure['locationname'] = array('title'=>ucwords($obj->lang['location']),'dbfield' => 'locationname', 'width'=>"110px");
$arrDataStructure['address'] = array('title'=>ucwords($obj->lang['address']),'dbfield' => 'address', 'width'=>"250px");
$arrDataStructure['sales'] = array('title'=>ucwords($obj->lang['salesman']),'dbfield' => 'salesname', 'width'=>"110px");
$arrDataStructure['product'] = array('title'=>ucwords($obj->lang['products']),'dbfield' => 'product', 'width'=>"110px");
$arrDataStructure['jobDetails'] = array('title'=>ucwords($obj->lang['jobDetails']),'dbfield' => 'jobdetailname', 'width'=>"110px");
$arrDataStructure['stagesProcess'] = array('title'=>ucwords($obj->lang['stagesProcess']),'dbfield' => 'stagename', 'width'=>"110px");

$arrDataStructure['subtotal'] = array('title'=>ucwords($obj->lang['subtotal']),'dbfield' => 'subtotal','align'=>'right', 'width'=>"100px",'format'=>'number','calculateTotal' => true); 
$arrDataStructure['tax'] = array('title'=>ucwords($obj->lang['tax']),'dbfield' => 'taxvalue','align'=>'right', 'width'=>"100px",'format'=>'number','calculateTotal' => true); 
$arrDataStructure['total'] = array('title'=>ucwords($obj->lang['initialCost']),'dbfield' => 'grandtotal','align'=>'right', 'width'=>"100px",'format'=>'number','calculateTotal' => true); 

$arrDataStructure['subtotalMonthly'] = array('title'=>ucwords($obj->lang['subtotal']),'dbfield' => 'subtotalmonthly','align'=>'right', 'width'=>"120px",'format'=>'number','calculateTotal' => true); 
$arrDataStructure['taxMonthly'] = array('title'=>ucwords($obj->lang['tax']),'dbfield' => 'taxvaluemonthly','align'=>'right', 'width'=>"100px",'format'=>'number','calculateTotal' => true); 
$arrDataStructure['grandtotalmonthly'] = array('title'=>ucwords($obj->lang['monthlyCost']),'dbfield' => 'grandtotalmonthly','align'=>'right', 'width'=>"100px",'format'=>'number','calculateTotal' => true); 

$arrDataStructure['invoiceDate'] = array('title'=>ucwords($obj->lang['invoiceDate']),'dbfield' => 'invoiceduedate', 'width'=>"120px",'format'=>'date');
$arrDataStructure['invoiced'] = array('title'=>ucwords($obj->lang['invoiced']),'dbfield' => 'isinvoiced', 'width'=>"100px");
$arrDataStructure['status'] = array('title'=>ucwords($obj->lang['status']),'dbfield' => 'statusname', 'width'=>"80px");

$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $obj->lang['salesOrderSubscriptionReport']; 
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();

array_push($arrTemplate, $arrHeaderTemplate);

// detail ...
$arrDataDetailStructure = array(); 
$arrDataDetailStructure['type'] = array('title'=>ucwords($obj->lang['type']),  'dbfield' => 'type', 'width'=>"100px" );  
$arrDataDetailStructure['itemName'] = array('title'=>ucwords($obj->lang['service']),  'dbfield' => 'itemname', 'width'=>"300px" );  
$arrDataDetailStructure['qty'] = array('title'=>ucwords($obj->lang['qty']),  'dbfield' => 'qty', 'width'=>"60px" , 'format' => 'number'); 
$arrDataDetailStructure['priceInUnit'] = array('title'=>ucwords($obj->lang['price']),'dbfield' => 'priceinunit', 'width'=>"100px",'format'=>'number');
$arrDataDetailStructure['total'] = array('title'=>ucwords($obj->lang['total']),'dbfield' => 'total', 'width'=>"100px",'format'=>'number');

$arrDetailTemplate = array(); 
$arrDetailTemplate['dataStructure'] = $arrDataDetailStructure;
$arrDetailTemplate['total'] = array();

array_push($arrTemplate, $arrDetailTemplate); 





if (isset($_POST) && !empty($_POST['hidAction'])){  
		
	$criteria = '';
	$stageCriteria = '';
	if(isset($_POST) && !empty($_POST['salesCode'])) {
		$criteria .= ' AND '.$obj->tableName.'.code LIKE ('.$class->oDbCon->paramString('%'.$_POST['salesCode'].'%').')';
		array_push($arrFilterInformation,array("label" => 'Kode', 'filter' => $_POST['salesCode']));
	}
	
	
	if(isset($_POST) && !empty($_POST['isInvoiced'])) {
		$isInvoice = "Y";
		$criteria .= ' AND '.$obj->tableName.'.invoiceduedate is not null and '.$obj->tableName.'.invoiceduedate  <> '.$class->oDbCon->paramDate(DEFAULT_EMPTY_DATE,' / ');
		array_push($arrFilterInformation,array("label" => 'Sudah Difaktur ', 'filter' => "Ya"));
	}

	if(isset($_POST) && !empty($_POST['trStartDate'])){
		$criteria .= ' and trdate between '.$class->oDbCon->paramDate( $_POST['trStartDate'],' / ').' AND '.$class->oDbCon->paramDate( $_POST['trEndDate'],' / '); 
		array_push($arrFilterInformation,array("label" => 'Tanggal', 'filter' => $_POST['trStartDate'] . ' - ' .$_POST['trEndDate'] ));
	}
    
    if(isset($_POST) && !empty($_POST['customerName'])) {
		$criteria .= ' AND '.$obj->tableCustomer.'.name LIKE ('.$class->oDbCon->paramString('%'.$_POST['customerName'].'%').')';
	 	array_push($arrFilterInformation,array("label" => 'Pelanggan', 'filter' =>  $_POST['customerName']));
	} 
    
	 if(isset($_POST) && !empty($_POST['selCustomer'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selCustomer']));   
        
       	$criteria .= ' AND '.$obj->tableName.'.customerkey in('.$key.')';  

        $rsCriteria = $customer->searchData('','',true, ' and '.$customer->tableName.'.pkey in ('.$key.')');
	 
        $arrTempCustomer = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempCustomer,$rsCriteria[$k]['name']);
			
		$customerName = implode(", ",$arrTempCustomer); 
	    array_push($arrFilterInformation,array("label" => 'Pelangan', 'filter' => $customerName )); 
        
	}	
    
    if(isset($_POST) && !empty($_POST['selMedia'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selMedia']));   
        
       	$criteria .= ' AND '.$obj->tableCustomer.'.mediakey in('.$key.')';  

        $rsCriteria = $media->searchData('','',true, ' and '.$media->tableName.'.pkey in ('.$key.')');
	 
        $arrTempMedia = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempMedia,$rsCriteria[$k]['name']);
			
		$mediaName = implode(", ",$arrTempMedia); 
	    array_push($arrFilterInformation,array("label" => 'Media', 'filter' => $mediaName ));
        
	}	
    
    if(isset($_POST) && !empty($_POST['selJobDetails'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selJobDetails']));   
        
       	$criteria .= ' AND '.$obj->tableName.'.jobdetailskey in('.$key.')';  

        $rsCriteria = $jobDetails->searchData('','',true, ' and '.$jobDetails->tableName.'.pkey in ('.$key.')');
	 
        $arrTempJobDetail = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempJobDetail,$rsCriteria[$k]['name']);
			
		$jobDetailsName = implode(", ",$arrTempJobDetail); 
	    array_push($arrFilterInformation,array("label" => 'Detail Perkerjaan', 'filter' => $jobDetailsName ));
        
	}	
    
//    if(isset($_POST) && !empty($_POST['selStageProcess'])) { 
//        
//        $key = implode(",", $class->oDbCon->paramString($_POST['selStageProcess']));   
//        
//       	$stageCriteria .= ' AND '.$installationWorkOrder->tableName.'.stagekey in('.$key.')';  
//
//        $rsCriteria = $installationWorkOrder->searchData('','',true, ' and '.$installationWorkOrder->tableName.'.pkey in ('.$key.')');
//	 
//        $arrTempStatus = array();
//		for ($k=0;$k<count($rsCriteria);$k++)
//		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
//			
//		$stageProcessName = implode(", ",$arrTempStatus); 
//	    array_push($arrFilterInformation,array("label" => 'Tahapan Pengerjaan', 'filter' => $stageProcessName ));
//        
//	}	

	if(isset($_POST) && !empty($_POST['selLocation'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selLocation']));   
        
       	$criteria .= ' AND '.$obj->tableCustomer.'.locationkey in('.$key.')';  

        $rsCriteria = $location->searchData('','',true, ' and '.$location->tableName.'.pkey in ('.$key.')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$locationName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => 'Lokasi', 'filter' => $locationName ));
        
	}
	
	 
//	if(isset($_POST) && !empty($_POST['itemName'])) { 
//        $detailCriteria .= ' AND '.$obj->tableItem.'.name  LIKE ('.$class->oDbCon->paramString('%'.$_POST['itemName'].'%').')';
//	    array_push($arrFilterInformation,array("label" => 'Item', 'filter' => $_POST['itemName']));
//	}
//     
    
    if(isset($_POST) && !empty($_POST['selSales'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selSales']));   
        
       	$criteria .= ' AND '.$obj->tableCustomer.'.saleskey in('.$key.')';  

        $rsCriteria =  $employee->searchData('','',true, ' and '.$employee->tableName.'.pkey in ('.$key.')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$salesName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => 'Sales', 'filter' => $salesName));
        
	}

 
	if(isset($_POST) && !empty($_POST['selWarehouse'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selWarehouse']));   
        
       	$criteria .= ' AND '.$obj->tableName.'.warehousekey in('.$key.')';  

        $rsCriteria = $warehouse->searchData('','',true, ' and '.$warehouse->tableName.'.pkey in ('.$key.')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$warehouseName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => 'Gudang', 'filter' => $warehouseName ));
        
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

    $arrPkey = array_column($rs,'pkey');

    $rsDetailCol = $obj->getAllDetailRelatedInformation($arrPkey);
    $rsDetailCol = $obj->reindexDetailCollections($rsDetailCol,'refkey');
    
    for( $i=0;$i<count($rs);$i++) {  
        $arrHeaderStyle = array();  
                
        $rsDetail = $rsDetailCol[$rs[$i]['pkey']];

      	$rsWOExternal = $installationWorkOrder->searchData ('','',true,' and '.$installationWorkOrder->tableName.'.statuskey in (1,2,3)',' order by '.$installationWorkOrder->tableName.'.stagekey desc limit 1');
        if(!empty($rsWOExternal))
            $rsStage = $stagesProcess->getDataRowById($rsWOExternal[0]['stagekey']);
        
        $rs[$i]['stagename'] = $rsStage[0]['name'];

        $rs[$i]['_detail_'] = array('arrTemplate'=>$arrDetailTemplate,'data' => $rsDetail); 

        $return = $obj->formatReportRows(array('data' => $rs[$i], 'style' => $arrHeaderStyle ),$arrTemplate); 

        // ===== FOR EXPORT SECTION 
        array_push($dataToExport, $return['data']);  
        // ===== END FOR EXPORT SECTION

        $tempreport .= $return['html'];
        $arrTemplate[0]['total'] = $obj->arraySum($arrTemplate[0]['total'], $return['subtotal'][0]);

    }
    
    
    $footnote = '';
    
	$obj->generateReport($_POST, $tempreport, $arrTemplate,$dataToExport,$arrFilterInformation,'', $footnote);

}
else{
   	$_POST['trStartDate'] = date('d / m / Y');
	$_POST['trEndDate'] = date('d / m / Y'); 
}

$arrWarehouse = $class->convertForCombobox($warehouse->searchData($warehouse->tableName.'.statuskey',1,true,'','order by name asc'),'pkey','name');
$arrEmployee = $class->convertForCombobox($employee->searchData($employee->tableName.'.statuskey',2,true),'pkey','name');   
$arrCustomer = $class->convertForCombobox($customer->searchData($customer->tableName.'.statuskey',2,true,'','order by name asc'),'pkey','name');
$arrStatus = $class->convertForCombobox($obj->getAllStatus(),'pkey','status');   
$arrStageProcess = $class->convertForCombobox($stagesProcess->searchData ('','',true,' and ('.$stagesProcess->tableName.'.statuskey = 1 )',' order by '.$stagesProcess->tableName.'.orderlist asc'),'pkey','name'); 
$arrJobDetails = $class->convertForCombobox($jobDetails->searchData('','',true,' and ('.$jobDetails->tableName.'.statuskey = 1)',' order by '.$jobDetails->tableName.'.name asc'),'pkey','name');  
$arrMedia = $class->convertForCombobox($media->searchData($media->tableName.'.statuskey',1,true,'','order by name asc'),'pkey','name');    
$arrLocation = $class->convertForCombobox($location->searchData($location->tableName.'.statuskey',1,true,'','order by name asc'),'pkey','name');    

 
$arrTwigVar['inputSalesCode'] =  $class->inputText('salesCode');  
$arrTwigVar['inputHidCustomerKey'] =  $class->inputHidden('hidCustomerKey');
$arrTwigVar['inputCustomerName'] =  $class->inputText('customerName');
$arrTwigVar['inputSelCustomer'] =  $class->inputSelect('selCustomer[]', $arrCustomer, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputSelStatus'] =  $class->inputSelect('selStatus[]', $arrStatus, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox')); 
$arrTwigVar['inputItemName'] =  $class->inputText('itemName'); 
$arrTwigVar['inputSalesName'] =  $class->inputSelect('selSales[]', $arrEmployee, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputMediaName'] =  $class->inputSelect('selMedia[]', $arrMedia, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
//$arrTwigVar['inputStageProcess'] =  $class->inputSelect('selStageProcess[]', $arrStageProcess, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputJobDetails'] =  $class->inputSelect('selJobDetails[]', $arrJobDetails, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputLocationName'] =  $class->inputSelect('selLocation[]', $arrLocation, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
//$arrTwigVar['inputStageProcess'] =  $class->inputSelect('selStageProcess[]', $arrStageProcess);
//$arrTwigVar['inputJobDetails'] =  $class->inputSelect('selJobDetails[]', $arrJobDetails);
//$arrTwigVar['inputMedia'] =  $class->inputSelect('selMedia[]', $arrMedia);
$arrTwigVar['inputStartDate'] = $class->inputDate('trStartDate', array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputEndDate'] = $class->inputDate('trEndDate', array('etc' => 'style="text-align:center"')); 
$arrTwigVar['inputSelWarehouse'] =  $class->inputSelect('selWarehouse[]', $arrWarehouse, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));  
$arrTwigVar['inputIsInvoiced'] =  $class->inputCheckBox('isInvoiced'); 

$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate;
echo $twig->render('reportSalesOrderSubscription.html', $arrTwigVar);  
 
?>
