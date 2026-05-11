<?php
	 
include '../../_config.php';  
include '../../_include-v2.php'; 

includeClass('DisposalContract.class.php');

$disposalContract = createObjAndAddToCol(new DisposalContract()); 
$customer = createObjAndAddToCol( new Customer()); 
$service = createObjAndAddToCol( new Service()); 


include '_global.php';
$securityObject = 'reportContract'; // the value of security object is manually inserted to handle 
  

$obj= $disposalContract;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
								// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true));  
 
$arrStatus = $obj->getAllStatus();

$_POST['selStatus[]'] = array(2,3);
 
// ===== FOR EXPORT SECTION
$dataToExport = array();

/* data structure */
$arrTemplate = array();

$arrDataStructure = array();
$_POST['module'] = IMPORT_TEMPLATE['contract'];
switch($EXPORT_TYPE){
    case 2 :
            $arrDataStructure['rowNumber'] = array('title'=>'#', 'align'=>'right', 'width'=>"40px", 'autoNumber' => true, "sortable" => false);
            $arrDataStructure['code'] = array('title'=>ucwords($obj->lang['code']),'dbfield' => 'code', 'width'=>"100px");
            $arrDataStructure['date'] = array('title'=>ucwords($obj->lang['date']),'dbfield' => 'trdate', 'width'=>"100px", 'align'=>'center');
            $arrDataStructure['customer'] = array('title'=>ucwords($obj->lang['customer']),'dbfield' => 'customername', 'width'=>"300px");
            $arrDataStructure['service'] = array('title'=>ucwords($obj->lang['service']),'dbfield' => 'servicename', 'width'=>"100px");
            $arrDataStructure['city'] = array('title'=>ucwords($obj->lang['city']),'dbfield' => 'cityname', 'width'=>"100px");
            $arrDataStructure['contractDuration'] = array('title'=>ucwords($obj->lang['contractDuration']),'dbfield' => 'durationname', 'width'=>"100px");
        
            //infolayanan
            $arrDataStructure['duration'] = array('title'=>ucwords($obj->lang['duration']),'dbfield' => 'duration', 'width'=>"100px", 'format'=>'number','align'=>'right');
            $arrDataStructure['totalVisit'] = array('title'=>ucwords($obj->lang['totalVisit']),'dbfield' => 'qtyservice', 'width'=>"100px",'format'=>'number','align'=>'right');
            $arrDataStructure['sellingPrice'] = array('title'=>ucwords($obj->lang['sellingPrice']),'dbfield' => 'sellingprice', 'width'=>"100px",'format'=>'number','align'=>'right');
            $arrDataStructure['additional'] = array('title'=>ucwords($obj->lang['additional']),'dbfield' => 'exceedprice', 'width'=>"100px",'format'=>'number','align'=>'right');
            $arrDataStructure['additional'] = array('title'=>ucwords($obj->lang['additional']),'dbfield' => 'extraprice', 'width'=>"100px",'format'=>'number','align'=>'right');

            $arrDataStructure['note'] = array('title'=>ucwords($obj->lang['note']),'dbfield' => 'trdesc', 'width'=>"250px");
            $arrDataStructure['status'] = array('title'=>ucwords($obj->lang['status']),'dbfield' => 'statusname', 'width'=>"100px");

            break;
        
    default :
            $arrDataStructure['rowNumber'] = array('title'=>'#', 'align'=>'right', 'width'=>"40px", 'autoNumber' => true, "sortable" => false);
            $arrDataStructure['code'] = array('title'=>ucwords($obj->lang['code']),'dbfield' => 'code', 'width'=>"100px");
            $arrDataStructure['date'] = array('title'=>ucwords($obj->lang['date']),'dbfield' => 'trdate', 'width'=>"100px", 'align'=>'center');
            $arrDataStructure['customer'] = array('title'=>ucwords($obj->lang['customer']),'dbfield' => 'customername', 'width'=>"300px");
            $arrDataStructure['service'] = array('title'=>ucwords($obj->lang['service']),'dbfield' => 'servicename', 'width'=>"100px");
            $arrDataStructure['city'] = array('title'=>ucwords($obj->lang['city']),'dbfield' => 'cityname', 'width'=>"100px");
            $arrDataStructure['contractDuration'] = array('title'=>ucwords($obj->lang['contractDuration']),'dbfield' => 'durationname', 'width'=>"100px");
         
            //infolayanan
            $arrDataStructure['duration'] = array('title'=>ucwords($obj->lang['duration']),'dbfield' => 'duration', 'width'=>"100px", 'format'=>'number','align'=>'right');
            $arrDataStructure['totalVisit'] = array('title'=>ucwords($obj->lang['totalVisit']),'dbfield' => 'qtyservice', 'width'=>"100px",'format'=>'number','align'=>'right');
            $arrDataStructure['sellingPrice'] = array('title'=>ucwords($obj->lang['sellingPrice']),'dbfield' => 'sellingprice', 'width'=>"100px",'format'=>'number','align'=>'right');
            $arrDataStructure['additional'] = array('title'=>ucwords($obj->lang['additional']),'dbfield' => 'exceedprice', 'width'=>"100px",'format'=>'number','align'=>'right');
            $arrDataStructure['additional'] = array('title'=>ucwords($obj->lang['additional']),'dbfield' => 'extraprice', 'width'=>"100px",'format'=>'number','align'=>'right');

            $arrDataStructure['note'] = array('title'=>ucwords($obj->lang['note']),'dbfield' => 'trdesc', 'width'=>"250px");
            $arrDataStructure['status'] = array('title'=>ucwords($obj->lang['status']),'dbfield' => 'statusname', 'width'=>"100px");
}
  
       
$arrHeaderTemplate = array();  
$arrHeaderTemplate['reportTitle'] = $obj->lang['contractReport']; 
$arrHeaderTemplate['dataStructure'] = $arrDataStructure; 
$arrHeaderTemplate['total'] = array();
 
array_push($arrTemplate, $arrHeaderTemplate);

// ===== END FOR EXPORT SECTION
$arrStatus = $class->convertForCombobox($obj->getAllStatus(),'pkey','status');   

$arrCustomer = $class->convertForCombobox($customer->searchData($customer->tableName.'.statuskey',2,true,'','order by name asc'),'pkey','name');

$arrService = $service->generateComboboxOpt(null,array('criteria' => ' and  '.$service->tableName.'.statuskey = 1  and '.$service->tableName.'.itemtype = '.SERVICE.'  and '.$service->tableName.'.servicecost = 0 '));

 

if (isset($_POST) && !empty($_POST['hidAction'])){  
		
    $orderBy = (!empty($_POST['hidOrderBy'])) ? $obj->oDbCon->paramOrder($_POST['hidOrderBy']) : 'pkey'; // order by harus dr kolom yg terdaftar saja
    $orderType = (isset($_POST['hidOrderType']) && !empty($_POST['hidOrderType']) && $_POST['hidOrderType'] == 1) ? 'desc' : 'asc';
 
	if(empty($_POST['hidRs'])){ 
		$result = queryNewReport(get_defined_vars(), array('orderBy' => $orderBy, 'orderType' => $orderType));
		$rs = $result['rs'];
		$arrFilterInformation = $result['arrFilterInformation'];
	}else{ 
		$hidRs = json_decode($_POST['hidRs'],true);  
		foreach($hidRs as $key=>$row) $$key = $hidRs[$key];
		
		//$arrFilterInformation = $hidRs['arrFilterInformation']; 
		$obj->mknatsort ($rs, $orderBy, ($orderType=='asc')?false:true ,true);
	}
    
    // ============================= GENERATE DATA ============================= 
 
    $tempreport = ''; 
    for( $i=0;$i<count($rs);$i++) {      
    
        $return = $obj->formatReportRows(array('data' => $rs[$i]),$arrTemplate);
        
        // ===== FOR EXPORT SECTION 
        array_push($dataToExport, $return['data']); 
        // ===== END FOR EXPORT SECTION
        
        $tempreport .= $return['html']; 
         
        // count subtotal for each col
        $arrTemplate[0]['total'] = $obj->arraySum($arrTemplate[0]['total'], $return['subtotal'][0]); 
         
    }
		 
    $obj->generateReport($_POST, $tempreport, $arrTemplate,array('dataToExport' => $dataToExport,'rs' => $rs),$arrFilterInformation);
}else{
   	$_POST['trStartDate'] = date('d / m / Y');
	$_POST['trEndDate'] = date('d / m / Y'); 
}


 
$arrTwigVar['importUrl'] = $obj->importUrl; 

 $arrTwigVar['inputContractCode'] =  $class->inputText('contrCode');    
 $arrTwigVar['inputSelCustomer'] =  $class->inputSelect('selCustomer[]', $arrCustomer, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));     
 $arrTwigVar['inputSelService'] =  $class->inputSelect('selService[]', $arrService, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));  

$arrTwigVar['inputSelStatus'] =  $class->inputSelect('selStatus[]', $arrStatus, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox')); 
 //filter periode
$arrTwigVar['inputStartDate'] = $class->inputDate('trStartDate',array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputEndDate'] = $class->inputDate('trEndDate',array('etc' => 'style="text-align:center"')); $arrTwigVar['arrTemplate'] =  $arrHeaderTemplate;   
      
echo $twig->render('reportDisposalContract.html', $arrTwigVar);  

function queryNewReport($varCol = array(),$order){ 
    
    global $service;
    global $customer;
	foreach($varCol as $key=>$row) $$key = $varCol[$key];
		  
	$criteria = ''; 
    $arrFilterInformation = array();
	$criteriaArr = array();
  
    array_push($criteriaArr, array('postVariable' => 'contrCode', 
                            'fieldName' => $obj->tableName.'.code', 
                            'label' => $obj->lang['code']));

    array_push($criteriaArr, array('postVariable' => 'selCustomer', 
                            'fieldName' => $obj->tableName.'.customerkey', 
                            'label' => $obj->lang['customer'],
                        'useArrayKey' => array('obj' => $customer)));

    array_push($criteriaArr, array('postVariable' => 'selService', 
                            'fieldName' => $obj->tableName.'.servicekey', 
                            'label' => $obj->lang['service'],
                        'useArrayKey' => array('obj' => $service)));

    array_push($criteriaArr, array('postVariable' => 'selStatus',
								   'type' => 'status'));
	                                   
//filter periode
        array_push($criteriaArr, array('postVariable' => array('trStartDate', 'trEndDate'), 
								   'fieldName' => $obj->tableName.'.trdate', 
								   'label' =>  $obj->lang['date'],
								   'type' => 'daterange'));

   
//fungsi untuk nampili kriteria sms tulisan yang di bwah title setekah filter
	$obj->createReportCriteria($criteria,$arrFilterInformation,$criteriaArr);
	 
	$order = 'order by '.$order['orderBy'].' ' .$order['orderType']; 
      
	$rs = $obj->searchData('','',true,$criteria,$order);


//untuk manipulai jika di perlukan( sebelum di lempar untuk di proses / export)
 for( $i=0;$i<count($rs);$i++) {  

         $rs[$i]['durationname'] = ($obj->formatNumber ($rs[$i]['contractduration'])).' '.$obj->lang['month'];
        
  

	 }
	return array(
		'arrFilterInformation' => $arrFilterInformation,
		'rs' => $rs
	);
}

?>