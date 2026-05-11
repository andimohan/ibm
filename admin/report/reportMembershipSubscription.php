<?php
	 
include '../../_config.php';  
include '../../_include-v2.php';

includeClass(array('Warehouse.class.php','Customer.class.php','MembershipSubscription.class.php'));
$membershipSubscription = createObjAndAddToCol( new MembershipSubscription());  
$warehouse = createObjAndAddToCol( new Warehouse());  
$customer = createObjAndAddToCol( new Customer());  

include '_global.php';

$obj= $membershipSubscription;
$securityObject = 'reportMembershipSubscription'; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true)); 


$_POST['selStatus[]'] = array(2,3);

$arrFilterInformation = array(); 

$dataToExport = array();

/* data structure */
$arrTemplate = array();

$arrDataStructure = array();
$arrDataStructure['rowNumber'] = array('title'=>'#', 'align'=>'right', 'width'=>"40px", 'autoNumber' => true, "sortable" => false);
$arrDataStructure['code'] = array('title'=>ucwords($obj->lang['code']),  'width'=>"150px", 'dbfield' => 'code'); 
$arrDataStructure['date'] = array('title'=>ucwords($obj->lang['date']),'dbfield' => 'trdate', 'width'=>"120px",'format'=>'date');
//$arrDataStructure['warehouse'] = array('title'=>ucwords($obj->lang['warehouse']),'dbfield' => 'warehousename', 'width'=>"110px");
$arrDataStructure['customer'] = array('title'=>ucwords($obj->lang['customer']),'dbfield' => 'customername', 'width'=>"200px");
$arrDataStructure['total'] = array('title'=>ucwords($obj->lang['total']),'dbfield' => 'grandtotal','align'=>'right', 'width'=>"100px",'format'=>'integer','calculateTotal' => true); 
$arrDataStructure['membershiplevel'] = array('title'=>ucwords($obj->lang['membership']),'dbfield' => 'membershiplevel',  'width'=>"100px" ); 
//$arrDataStructure['paymentInformation'] = array('title'=>ucwords($obj->lang['paymentInformation']),'dbfield' => 'paymentinformation','width'=>"200px"); 
$arrDataStructure['status'] = array('title'=>ucwords($obj->lang['status']),'dbfield' => 'statusname', 'width'=>"80px");

$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $obj->lang['membershipSubscriptionnReport']; 
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();

array_push($arrTemplate, $arrHeaderTemplate);


if (isset($_POST) && !empty($_POST['hidAction'])){  
		
	
	$criteriaArr = array();
	
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
    
    $tempreport = ''; 
    $totalRs = count($rs);
    for( $i=0;$i<$totalRs;$i++) {  
        $arrHeaderStyle = array();  
  
        $return = $obj->formatReportRows(array('data' => $rs[$i], 'style' => $arrHeaderStyle ),$arrTemplate); 

        // ===== FOR EXPORT SECTION 
        array_push($dataToExport, $return['data']);  
        // ===== END FOR EXPORT SECTION

        $tempreport .= $return['html'];
        $arrTemplate[0]['total'] = $obj->arraySum($arrTemplate[0]['total'], $return['subtotal'][0]);

    }
    
	$obj->generateReport($_POST, $tempreport, $arrTemplate,array('dataToExport' => $dataToExport,'rs' => $rs),$arrFilterInformation);

}
else{
   	$_POST['trStartDate'] = date('d / m / Y');
	$_POST['trEndDate'] = date('d / m / Y'); 
}

$arrWarehouse = $class->convertForCombobox($warehouse->searchData($warehouse->tableName.'.statuskey',1,true,'','order by name asc'),'pkey','name');
$arrCustomer = $class->convertForCombobox($customer->searchData($customer->tableName.'.statuskey',2,true,'','order by name asc'),'pkey','name');
$arrStatus = $class->convertForCombobox($obj->getAllStatus(),'pkey','status');   
  
 
$arrTwigVar['inputSalesCode'] =  $class->inputText('salesCode');  
$arrTwigVar['inputSelCustomer'] =  $class->inputSelect('selCustomer[]', $arrCustomer, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputSelStatus'] =  $class->inputSelect('selStatus[]', $arrStatus, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox')); 
$arrTwigVar['inputStartDate'] = $class->inputDate('trStartDate', array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputEndDate'] = $class->inputDate('trEndDate', array('etc' => 'style="text-align:center"')); 
$arrTwigVar['inputSelWarehouse'] =  $class->inputSelect('selWarehouse[]', $arrWarehouse, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));  
$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate;
echo $twig->render('reportMembershipSubscription.html', $arrTwigVar);  
 

function queryNewReport($varCol = array(),$order){ 
	foreach($varCol as $key=>$row) $$key = $varCol[$key];
		 
	$arrFilterInformation = array();
	
	$criteria = ''; 
	  
	array_push($criteriaArr, array('postVariable' => array('trStartDate', 'trEndDate'), 
								   'fieldName' => $obj->tableName.'.trdate', 
								   'label' =>  $obj->lang['period'], 
								   'type' => 'daterange'));
	
	array_push($criteriaArr, array('postVariable' => 'salesCode', 
								   'fieldName' => $obj->tableName.'.code', 
								   'label' => $obj->lang['code']
								  ));
	
 
	array_push($criteriaArr, array('postVariable' => 'selWarehouse', 
								   'fieldName' => $obj->tableName.'.warehousekey', 
								   'label' => $obj->lang['warehouse'], 
								   'useArrayKey' => array('obj' => $warehouse) ));
	
	array_push($criteriaArr, array('postVariable' => 'selCustomer', 
							   'fieldName' => $obj->tableName.'.customerkey', 
							   'label' => $obj->lang['customer'], 
							   'useArrayKey' => array('obj' => $customer) ));
	  
	array_push($criteriaArr, array('postVariable' => 'selStatus',
								   'type' => 'status'));
	    
	$obj->createReportCriteria($criteria,$arrFilterInformation,$criteriaArr);
	
	$order = 'order by '.$order['orderBy'].' ' .$order['orderType'];  
	$rs = $obj->searchData('','',true,$criteria,$order);
	 
	  
//    $totalRs = count($rs);
//    for( $i=0;$i<$totalRs;$i++) {     
//		$pkey = $rs[$i]['pkey']; 
//    }
	 
	return array(
		'arrFilterInformation' => $arrFilterInformation, 
		'rs' => $rs
	);
}
?>