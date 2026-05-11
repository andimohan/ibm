<?php
	 
include '../../_config.php';  
include '../../_include-v2.php'; 

includeClass(array('DisposalJobOrder.class.php','DisposalWorkOrderDispatcher.class.php'); 
$disposalWorkOrderDispatcher = createObjAndAddToCol(new DisposalWorkOrderDispatcher());
$warehouse = createObjAndAddToCol(new Warehouse());
$car = createObjAndAddToCol(new Car());
$disposalJobOrder = createObjAndAddToCol(new DisposalJobOrder());

$obj = $disposalWorkOrderDispatcher;
include '_global.php';
$securityObject = 'reportWorkList'; // the value of security object is manually inserted to handle 
  
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
								// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true));  
  
$_POST['selStatus[]'] = array(2,3);
$arrStatus = $obj->getAllStatus();

$isDetail = (isset($_POST['isDetail']) && !empty($_POST['isDetail'])) ? true : false;



/* data structure */
$arrTemplate = array();

$arrDataStructure = array();
            $arrDataStructure['rowNumber'] = array('title'=>'#', 'align'=>'right', 'width'=>"40px", 'autoNumber' => true, "sortable" => false);
            $arrDataStructure['code'] = array('title'=>ucwords($obj->lang['code']),'dbfield' => 'code', 'width'=>"100px");
            $arrDataStructure['date'] = array('title'=>ucwords($obj->lang['date']),'dbfield' => 'trdate',  'width' => "100px", 'align' => 'center', 'format' => 'date');
            $arrDataStructure['car'] = array('title'=>ucwords($obj->lang['car']),'dbfield' => 'policenumber', 'width'=>"100px");
            $arrDataStructure['driver'] = array('title'=>ucwords($obj->lang['driver']),'dbfield' => 'drivername', 'width'=>"200px");
            $arrDataStructure['warehouse'] = array('title'=>ucwords($obj->lang['warehouse']),'dbfield' => 'warehousename', 'width'=>"100px"); 
           
            $arrDataStructure['note'] = array('title'=>ucwords($obj->lang['note']),'dbfield' => 'trdesc', 'width'=>"200px");                   
            $arrDataStructure['status'] = array('title'=>ucwords($obj->lang['status']),'dbfield' => 'statusname', 'width'=>"100px");

  
$arrHeaderTemplate = array();  
$arrHeaderTemplate['reportTitle'] = $obj->lang['workOrderListReport']; 
$arrHeaderTemplate['dataStructure'] = $arrDataStructure; 
$arrHeaderTemplate['total'] = array();
 
array_push($arrTemplate, $arrHeaderTemplate);

if ($isDetail){ 
    // detail ...
    $arrDataDetailStructure = array(); 
    $arrDataDetailStructure['jocode'] = array('title'=>ucwords($obj->lang['JOCode']),'dbfield' => 'jobordercode', 'width'=>"150px" );  
    $arrDataDetailStructure['contract'] = array('title'=>ucwords($obj->lang['contract']),'dbfield' => 'contractname', 'width'=>"150px" );  
    $arrDataDetailStructure['customer'] = array('title'=>ucwords($obj->lang['customer']),'dbfield' => 'customername', 'width'=>"150px" );  
    $arrDataDetailStructure['service'] = array('title'=>ucwords($obj->lang['service']),'dbfield' => 'servicename', 'width'=>"150px" );  
    $arrDataDetailStructure['quota'] = array('title'=>ucwords($obj->lang['maxWeight']),'dbfield' => 'quota', 'width'=>"170px",'format'=>'number');

    $arrDetailTemplate = array(); 
    $arrDetailTemplate['dataStructure'] = $arrDataDetailStructure;
    $arrDetailTemplate['total'] = array();

    array_push($arrTemplate, $arrDetailTemplate); 
}


// ===== END FOR EXPORT SECTION   
$arrStatus = $class->convertForCombobox($arrStatus,'pkey','status');  
$arrDriver = $class->convertForCombobox($employee->searchData($employee->tableName.'.statuskey',2,true,'','order by name asc'),'pkey','name');
$arrWarehouse = $class->convertForCombobox($warehouse->searchData($warehouse->tableName.'.statuskey',1,true,'','order by name asc'),'pkey','name');
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

 $arrTwigVar['inputSelDriver'] =  $class->inputSelect('selDriver[]', $arrDriver, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));  
 $arrTwigVar['inputSelWarehouse'] =  $class->inputSelect('selWarehouse[]', $arrWarehouse, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));  
 $arrTwigVar['inputSelCar'] =  $class->inputSelect('selCar[]', $arrCar, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));  
 $arrTwigVar['inputIsDetail'] =  $class->inputCheckBox('isDetail', array('value'=> 1));
$arrTwigVar['inputSelStatus'] =  $class->inputSelect('selStatus[]', $arrStatus, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox')); 
 //filter periode
$arrTwigVar['inputStartDate'] = $class->inputDate('trStartDate',array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputEndDate'] = $class->inputDate('trEndDate',array('etc' => 'style="text-align:center"')); $arrTwigVar['arrTemplate'] =  $arrHeaderTemplate;   
      
echo $twig->render('reportDisposalWorkOrderDispatcher.html', $arrTwigVar);  

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

                            
    array_push($criteriaArr, array('postVariable' => 'selDriver', 
                            'fieldName' => $obj->tableName.'.driverkey', 
                            'label' => $obj->lang['driver'],
                        'useArrayKey' => array('obj' => $employee)));

    array_push($criteriaArr, array('postVariable' => 'selWarehouse', 
                            'fieldName' => $obj->tableName.'.warehousekey', 
                            'label' => $obj->lang['warehouse'],
                        'useArrayKey' => array('obj' => $warehouse)));

   //filter periode
    array_push($criteriaArr, array('postVariable' => array('trStartDate', 'trEndDate'), 
								   'fieldName' => $obj->tableName.'.trdate', 
								   'label' =>  $obj->lang['date'],
								   'type' => 'daterange'));

    array_push($criteriaArr, array('postVariable' => 'selStatus',
								   'type' => 'status'));


    //filter secara manual/cara lama
	if(isset($_POST) && !empty($_POST['selCar'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selCar']));   
       	$criteria .= ' AND '.$obj->tableName.'.carkey in('.$key.')';  

        $rsCriteria = $car->searchData('','',true, ' and '.$car->tableName.'.pkey in ('.$key.')');
	 //buat nampilin hasil filter di bawah title
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['policenumber']);
			
		$customerName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" =>$obj->lang['carRegistrationNumber'], 'filter' => $customerName ));
        
	}


    //buat nampilin hasil filter di bawah title
    $obj->createReportCriteria($criteria,$arrFilterInformation,$criteriaArr); 
	$order = 'order by '.$order['orderBy'].' ' .$order['orderType'];  
	$rs = $obj->searchData('','',true,$criteria,$order);
    $rsDetailCol = ($isDetail) ? $obj->getDetailCollections($rs,'refkey') : array();
    
   //manipulasi sebeum di proses/tampilkan 
    for( $i=0;$i<count($rs);$i++) {  
        $pkey = $rs[$i]['pkey'];

        $rsDetail = $rsDetailCol[$pkey];  
        $totalRsDetail = count($rsDetail);
        if($isDetail){ 
            $rs[$i]['_detail_'] = array('arrTemplate'=>$arrDetailTemplate,'data' => $rsDetail); 
        }

	 }

	return array(
		'arrFilterInformation' => $arrFilterInformation,
		'rs' => $rs
	);
}

?>