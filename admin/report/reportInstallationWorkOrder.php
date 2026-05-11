<?php
	 
include '../../_config.php';  
include '../../_include-v2.php';

includeClass('InstallationWorkOrder.class.php');
$installationWorkOrder = createObjAndAddToCol( new InstallationWorkOrder()); 
$warehouse = createObjAndAddToCol( new Warehouse()); 
$customer = createObjAndAddToCol( new Customer());
$employee = createObjAndAddToCol( new Employee());
$stagesProcess = createObjAndAddToCol( new StagesProcess());
$jobDetails = createObjAndAddToCol( new JobDetails());
$media = createObjAndAddToCol( new Media());
$location = createObjAndAddToCol( new Location());

include '_global.php';

$obj= $installationWorkOrder;
$securityObject = 'reportInstallationWorkOrder'; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true)); 

$_POST['selStatus[]'] = array(2,3);

$arrFilterInformation = array(); 
$detailCriteria = '';

$dataToExport = array();

/* data structure */
$arrTemplate = array();

$arrDataStructure = array();
$arrDataStructure['rowNumber'] = array('title'=>'#', 'align'=>'right', 'width'=>"40px", 'autoNumber' => true, "sortable" => false);
$arrDataStructure['code'] = array('title'=>ucwords($obj->lang['code']),  'width'=>"180px", 'dbfield' => 'code'); 
$arrDataStructure['refcode'] = array('title'=>ucwords($obj->lang['refCode']),  'width'=>"110px", 'dbfield' => 'socode'); 
$arrDataStructure['date'] = array('title'=>ucwords($obj->lang['date']),'dbfield' => 'trdate', 'width'=>"120px",'format'=>'date');
$arrDataStructure['warehouse'] = array('title'=>ucwords($obj->lang['warehouse']),'dbfield' => 'warehousename', 'width'=>"90px");
$arrDataStructure['jobDetails'] = array('title'=>ucwords($obj->lang['jobDetails']),'dbfield' => 'jobname', 'width'=>"110px",);
$arrDataStructure['startTime'] = array('title'=>ucwords($obj->lang['startTime']),'dbfield' => 'starttime', 'width'=>"150px",'format'=>'datetime');
$arrDataStructure['endTime'] = array('title'=>ucwords($obj->lang['endTime']),'dbfield' => 'endtime', 'width'=>"150px",'format'=>'datetime');
$arrDataStructure['sales'] = array('title'=>ucwords($obj->lang['salesman']),'dbfield' => 'employeename', 'width'=>"130px");
$arrDataStructure['stagesProcess'] = array('title'=>ucwords($obj->lang['stagesProcess']),'dbfield' => 'stagename', 'width'=>"110px");
$arrDataStructure['customer'] = array('title'=>ucwords($obj->lang['customer']),'dbfield' => 'customername', 'width'=>"250px");
$arrDataStructure['media'] = array('title'=>ucwords($obj->lang['media']),'dbfield' => 'medianame', 'width'=>"110px");
$arrDataStructure['phone'] = array('title'=>ucwords($obj->lang['phone']),'dbfield' => 'phone', 'width'=>"150px");
$arrDataStructure['locationname'] = array('title'=>ucwords($obj->lang['location']),'dbfield' => 'locationname', 'width'=>"110px");
$arrDataStructure['technicianName'] = array('title'=>ucwords($obj->lang['technician']),'dbfield' => 'technicianname', 'width'=>"110px", "sortable" => false);
$arrDataStructure['address'] = array('title'=>ucwords($obj->lang['address']),'dbfield' => 'address', 'width'=>"250px");

$arrDataStructure['status'] = array('title'=>ucwords($obj->lang['status']),'dbfield' => 'statusname', 'width'=>"80px");

$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $obj->lang['installationWorkOrderReport']; 
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();

array_push($arrTemplate, $arrHeaderTemplate);

// detail ...
$arrDataDetailStructure = array(); 
$arrDataDetailStructure['itemCode'] = array('title'=>ucwords($obj->lang['itemCode']),  'dbfield' => 'itemcode', 'width'=>"100px" );  
$arrDataDetailStructure['itemName'] = array('title'=>ucwords($obj->lang['itemName']),  'dbfield' => 'itemname', 'width'=>"240px" );  
$arrDataDetailStructure['qty'] = array('title'=>ucwords($obj->lang['qty']),  'dbfield' => 'qty', 'width'=>"60px" , 'format' => 'number'); 
$arrDataDetailStructure['unit'] = array('title'=>ucwords($obj->lang['unit']),  'dbfield' => 'unitname', 'width'=>"100px" ); 

$arrDetailTemplate = array(); 
$arrDetailTemplate['dataStructure'] = $arrDataDetailStructure;
$arrDetailTemplate['total'] = array();

array_push($arrTemplate, $arrDetailTemplate); 





if (isset($_POST) && !empty($_POST['hidAction'])){  
		
	$criteria = '';
	if(isset($_POST) && !empty($_POST['salesCode'])) {
		$criteria .= ' AND '.$obj->tableName.'.code LIKE ('.$class->oDbCon->paramString('%'.$_POST['salesCode'].'%').')';
		array_push($arrFilterInformation,array("label" => ucwords($obj->lang['code']), 'filter' => $_POST['salesCode']));
	}
    
    if(isset($_POST) && !empty($_POST['salesRefCode'])) {
		$criteria .= ' AND '.$obj->tableSalesOrder.'.code LIKE ('.$class->oDbCon->paramString('%'.$_POST['salesRefCode'].'%').')';
		array_push($arrFilterInformation,array("label" => ucwords($obj->lang['refCode']), 'filter' => $_POST['salesRefCode']));
	}

	if(isset($_POST) && !empty($_POST['trStartDate'])){
		$criteria .= ' and '.$obj->tableName.'.trdate between '.$class->oDbCon->paramDate( $_POST['trStartDate'],' / ').' AND '.$class->oDbCon->paramDate( $_POST['trEndDate'],' / '); 
		array_push($arrFilterInformation,array("label" => ucwords($obj->lang['date']), 'filter' => $_POST['trStartDate'] . ' - ' .$_POST['trEndDate'] ));
	}
    
//    if(isset($_POST) && !empty($_POST['customerName'])) {
//		$criteria .= ' AND '.$obj->tableCustomer.'.name LIKE ('.$class->oDbCon->paramString('%'.$_POST['customerName'].'%').')';
//	 	array_push($arrFilterInformation,array("label" => ucwords($obj->lang['customer']), 'filter' =>  $_POST['customerName']));
//	} 
    
    if(isset($_POST) && !empty($_POST['selCustomer'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selCustomer']));   
        
       	$criteria .= ' AND '.$obj->tableName.'.customerkey in('.$key.')';  

	   $rsCriteria = $customer->searchData('','',true, ' and '.$customer->tableName.'.pkey in ('.$key.')');

        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$customerName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => ucwords($obj->lang['customer']), 'filter' => $customerName ));
        
	}
    
    if(isset($_POST) && !empty($_POST['selMedia'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selMedia']));   
        
       	$criteria .= ' AND '.$obj->tableCustomer.'.mediakey in('.$key.')';  

        $rsCriteria = $media->searchData('','',true, ' and '.$media->tableName.'.pkey in ('.$key.')');
	 
        $arrTempMedia = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempMedia,$rsCriteria[$k]['name']);
			
		$mediaName = implode(", ",$arrTempMedia); 
	    array_push($arrFilterInformation,array("label" => ucwords($obj->lang['media']), 'filter' => $mediaName ));
        
	}	
    
    if(isset($_POST) && !empty($_POST['selJobDetails'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selJobDetails']));   
        
       	$criteria .= ' AND '.$obj->tableSalesOrder.'.jobdetailskey in('.$key.')';  

        $rsCriteria = $jobDetails->searchData('','',true, ' and '.$jobDetails->tableName.'.pkey in ('.$key.')');
	 
        $arrTempJobDetail = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempJobDetail,$rsCriteria[$k]['name']);
			
		$jobDetailsName = implode(", ",$arrTempJobDetail); 
	    array_push($arrFilterInformation,array("label" => ucwords($obj->lang['jobDetails']), 'filter' => $jobDetailsName ));
        
	}	
    
    if(isset($_POST) && !empty($_POST['selStageProcess'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selStageProcess']));   
        
       	$criteria .= ' AND '.$obj->tableName.'.stagekey in('.$key.')';  

        $rsCriteria = $stagesProcess->searchData('','',true, ' and '.$stagesProcess->tableName.'.pkey in ('.$key.')');

        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$stageProcessName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" =>  ucwords($obj->lang['stagesProcess']), 'filter' => $stageProcessName ));
        
	}	

	if(isset($_POST) && !empty($_POST['selLocation'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selLocation']));   
        
       	$criteria .= ' AND '.$obj->tableCustomer.'.locationkey in('.$key.')';  

        $rsCriteria = $location->searchData('','',true, ' and '.$location->tableName.'.pkey in ('.$key.')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$locationName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => ucwords($obj->lang['location']), 'filter' => $locationName ));
        
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
	    array_push($arrFilterInformation,array("label" => ucwords($obj->lang['salesman']), 'filter' => $salesName));
        
	}

 
	if(isset($_POST) && !empty($_POST['selWarehouse'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selWarehouse']));   
        
       	$criteria .= ' AND '.$obj->tableName.'.warehousekey in('.$key.')';  

        $rsCriteria = $warehouse->searchData('','',true, ' and '.$warehouse->tableName.'.pkey in ('.$key.')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$warehouseName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => ucwords($obj->lang['warehouse']), 'filter' => $warehouseName ));
        
	}
	
	if(isset($_POST) && !empty($_POST['selStatus'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selStatus']));   
        
       	$criteria .= ' AND '.$obj->tableName.'.statuskey in('.$key.')';  

        $rsCriteria =  $obj->getStatusById ($key);
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['status']);
			
		$statusName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => ucwords($obj->lang['status']), 'filter' => $statusName));
        
	}

    $orderBy = (!empty($_POST['hidOrderBy'])) ? $obj->oDbCon->paramOrder($_POST['hidOrderBy']) : 'pkey'; // order by harus dr kolom yg terdaftar saja
    $orderType = (isset($_POST['hidOrderType']) && !empty($_POST['hidOrderType']) && $_POST['hidOrderType'] == 1) ? 'desc' : 'asc';
    
		   
	$order = 'order by '.$orderBy.' ' .$orderType; 
	$rs = $obj->searchData('','',true,$criteria,$order);
    
    $tempreport = '';

    if (empty($rs)) 
        $tempreport .= '<tr class="report-row rewrite-row"><td colspan="'.count($arrHeaderTemplate['dataStructure']).'"></td></tr>';

    $rsDetailCol =$obj->getDetailCollections($rs,'refkey',$detailCriteria) ;
    
    $arrPkey = array_column($rs,'pkey');


    $rsDetailTechnicianCol = $obj->getDetailTechnicianWithRelatedInformation($arrPkey);
    $rsDetailTechnicianCol = $obj->reindexDetailCollections($rsDetailTechnicianCol,'refkey');
    
    $totalRs = count($rs);
    for( $i=0;$i<$totalRs;$i++) {  
        $arrHeaderStyle = array();  
            
        if (!isset($rsDetailCol[$rs[$i]['pkey']]))  continue;
        $rsDetail = $rsDetailCol[$rs[$i]['pkey']];
        
        $rs[$i]['technicianname'] = (isset($rsDetailTechnicianCol[$rs[$i]['pkey']])) ? implode('<br>',array_column($rsDetailTechnicianCol[$rs[$i]['pkey']],'technicianname')) : '';

                
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
//    $_POST['trStartTime'] = date('d / m / Y 00:00');
//	$_POST['trEndTime'] = date('d / m / Y 00:00'); 
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
$arrTwigVar['inputSalesRefCode'] =  $class->inputText('salesRefCode');   
$arrTwigVar['inputHidCustomerKey'] =  $class->inputHidden('hidCustomerKey');
$arrTwigVar['inputCustomerName'] =  $class->inputText('customerName');
$arrTwigVar['inputSelCustomer'] =  $class->inputSelect('selCustomer[]', $arrCustomer, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputSelStatus'] =  $class->inputSelect('selStatus[]', $arrStatus, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox')); 
$arrTwigVar['inputItemName'] =  $class->inputText('itemName'); 
$arrTwigVar['inputSalesName'] =  $class->inputSelect('selSales[]', $arrEmployee, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputMediaName'] =  $class->inputSelect('selMedia[]', $arrMedia, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputStageProcess'] =  $class->inputSelect('selStageProcess[]', $arrStageProcess, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputJobDetails'] =  $class->inputSelect('selJobDetails[]', $arrJobDetails, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputLocationName'] =  $class->inputSelect('selLocation[]', $arrLocation, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
//$arrTwigVar['inputStageProcess'] =  $class->inputSelect('selStageProcess[]', $arrStageProcess);
//$arrTwigVar['inputJobDetails'] =  $class->inputSelect('selJobDetails[]', $arrJobDetails);
//$arrTwigVar['inputMedia'] =  $class->inputSelect('selMedia[]', $arrMedia);
$arrTwigVar['inputStartDate'] = $class->inputDate('trStartDate', array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputEndDate'] = $class->inputDate('trEndDate', array('etc' => 'style="text-align:center"')); 
//$arrTwigVar['inputStartTime'] = $class->inputText('trStartTime', array('class'=> 'form-control input-datetime hasDatepicker'));
//$arrTwigVar['inputEndTime'] = $class->inputDate('trEndTime'); 

$arrTwigVar['inputSelWarehouse'] =  $class->inputSelect('selWarehouse[]', $arrWarehouse, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));  

$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate;
echo $twig->render('reportInstallationWorkOrder.html', $arrTwigVar);  
 
?>
