<?php
	 
include '../../_config.php';  
include '../../_include-v2.php'; 

includeClass('DisposalWorkOrder.class.php');

$disposalWorkOrder = createObjAndAddToCol(new DisposalWorkOrder());
$customer = createObjAndAddToCol(new Customer()); 
$service = createObjAndAddToCol(new Service());
$city = createObjAndAddToCol(new City());
$customer = createObjAndAddToCol( new Customer()); 
$service = createObjAndAddToCol( new Service()); 
$employee = createObjAndAddToCol( new Employee()); 
$car = createObjAndAddToCol( new Car()); 

include '_global.php';
$securityObject = 'ReportSPK'; // the value of security object is manually inserted to handle 
  

$obj= $disposalWorkOrder;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
								// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true));  
 $_POST['selStatus[]'] = array(2,3);
$arrStatus = $obj->getAllStatus();


$arrTemplate = array();
$arrDataStructure = array();

            $arrDataStructure['rowNumber'] = array('title'=>'#', 'align'=>'right', 'width'=>"40px", 'autoNumber' => true, "sortable" => false);
            $arrDataStructure['code'] = array('title'=>ucwords($obj->lang['code']),'dbfield' => 'code', 'width'=>"100px");
            $arrDataStructure['date'] = array('title'=>ucwords($obj->lang['date']),'dbfield' => 'trdate',  'width' => "100px", 'align' => 'center', 'format' => 'date');
            $arrDataStructure['workList'] = array('title'=>ucwords($obj->lang['reference']), 'dbfield' => 'wolistcode', 'default' => true, 'width' => "130px");
           
            $arrDataStructure['JOCode'] = array('title'=>ucwords($obj->lang['JOCode']),'dbfield' => 'jocode', 'width'=>"110px");
            $arrDataStructure['customer'] = array('title'=>ucwords($obj->lang['customer']),'dbfield' => 'customername', 'width'=>"250px");
            $arrDataStructure['city'] = array('title'=>ucwords($obj->lang['city']),'dbfield' => 'cityname', 'width'=>"100px");           
            $arrDataStructure['service'] = array('title'=>ucwords($obj->lang['service']),'dbfield' => 'servicename', 'width'=>"100px");
            
            $arrDataStructure['carRegistrationNumber'] = array('title'=>ucwords($obj->lang['carRegistrationNumber']),'dbfield' => 'policenumber', 'width'=>"100px");            $arrDataStructure['driver'] = array('title'=>ucwords($obj->lang['driver']),'dbfield' => 'drivername', 'width'=>"200px");            
            $arrDataStructure['disposalweight'] = array('title'=>'Muatan (Kg)','dbfield' => 'disposalweight',  'width' => "120px", 'format' => 'number');
            //maxWeight belum tampil
            $arrDataStructure['maximumweight'] = array('title'=>'Berat Maks. (Kg)','dbfield' => 'maximumweight',  'width' => "120px", 'format' => 'number');
           
            $arrDataStructure['status'] = array('title'=>ucwords($obj->lang['status']),'dbfield' => 'statusname', 'width'=>"100px");


$arrHeaderTemplate = array();  
$arrHeaderTemplate['reportTitle'] = $obj->lang['workOrderReport']; 
$arrHeaderTemplate['dataStructure'] = $arrDataStructure; 
$arrHeaderTemplate['total'] = array();
 
array_push($arrTemplate, $arrHeaderTemplate);

// ===== END FOR EXPORT SECTION
$arrStatus = $class->convertForCombobox($arrStatus,'pkey','status');  
$arrService = $service->generateComboboxOpt(null,array('criteria' => ' and  '.$service->tableName.'.statuskey = 1  and '.$service->tableName.'.itemtype = '.SERVICE.'  and '.$service->tableName.'.servicecost = 0 '));
$arrCustomer = $class->convertForCombobox($customer->searchData($customer->tableName.'.statuskey',2,true,'','order by name asc'),'pkey','name');
$arrDriver = $class->convertForCombobox($employee->searchData($employee->tableName.'.statuskey',2,true,'','order by name asc'),'pkey','name');
$arrCar = $class->convertForCombobox($car->searchData($car->tableName.'.statuskey',1,true,'','order by policenumber asc'),'pkey','policenumber');


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
 $arrTwigVar['inputSelDriver'] =  $class->inputSelect('selDriver[]', $arrDriver, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));  
 $arrTwigVar['inputSelCar'] =  $class->inputSelect('selCar[]', $arrCar, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));  
 $arrTwigVar['inputSelStatus'] =  $class->inputSelect('selStatus[]', $arrStatus, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox')); 
 //filter periode
$arrTwigVar['inputStartDate'] = $class->inputDate('trStartDate',array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputEndDate'] = $class->inputDate('trEndDate',array('etc' => 'style="text-align:center"')); $arrTwigVar['arrTemplate'] =  $arrHeaderTemplate;   
      
echo $twig->render('reportDisposalWorkOrder.html', $arrTwigVar);  

function queryNewReport($varCol = array(),$order){ 
    
    global $service;
    global $customer;
    global $car;
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

    array_push($criteriaArr, array('postVariable' => 'selDriver', 
                            'fieldName' => $obj->tableWOListHeader.'.driverkey', 
                            'label' => $obj->lang['driver'],
                        'useArrayKey' => array('obj' => $employee)));

   //filter periode
    array_push($criteriaArr, array('postVariable' => array('trStartDate', 'trEndDate'), 
								   'fieldName' => $obj->tableName.'.trdate', 
								   'label' =>  $obj->lang['date'],
								   'type' => 'daterange'));

    array_push($criteriaArr, array('postVariable' => 'selStatus',
								   'type' => 'status'));    

	$obj->createReportCriteria($criteria,$arrFilterInformation,$criteriaArr);
	 
	$order = 'order by '.$order['orderBy'].' ' .$order['orderType']; 
      
	$rs = $obj->searchData('','',true,$criteria,$order);


   //manipulasi sebeum di proses/tampilkan 
for( $i=0;$i<count($rs);$i++) {  




	 }
	return array(
		'arrFilterInformation' => $arrFilterInformation,
		'rs' => $rs
        
	);
}


?>