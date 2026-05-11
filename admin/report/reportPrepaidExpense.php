<?php	 
include '../../_config.php';  
include '../../_include-v2.php';

includeClass(array('PrepaidExpense.class.php'));
$prepaidExpense = createObjAndAddToCol(new PrepaidExpense()); 
$EMKLJobOrder = createObjAndAddToCol(new EMKLJobOrder()); 
$currency = createObjAndAddToCol(new Currency()); 
$service = createObjAndAddToCol(new Service(SERVICE)); 
$warehouse = createObjAndAddToCol(new Warehouse());  


include '_global.php';

$obj= $prepaidExpense;
$securityObject = 'ReportPrepaidExpense'; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true)); 
  
$_POST['selStatus[]'] = array(1,2);

$arrFilterInformation = array();    


// ===== FOR EXPORT SECTION
$dataToExport = array();

/* data structure */
$arrTemplate = array();

$arrDataStructure = array();
$arrDataStructure['rowNumber'] = array('title'=>'#', 'align'=>'right', 'width'=>"40px", 'autoNumber' => true,"sortable" => false);
$arrDataStructure['code'] = array('title'=>ucwords($obj->lang['code']),  'width'=>"100px", 'dbfield' => 'code');
$arrDataStructure['date'] = array('title'=>ucwords($obj->lang['date']),'dbfield' => 'trdate', 'width'=>"120px",'format'=>'date');
$arrDataStructure['refcode'] = array('title'=>ucwords($obj->lang['refCode']),  'width'=>"150px", 'dbfield' => 'refcode'); 
$arrDataStructure['socode'] = array('title'=>ucwords($obj->lang['soCode']),  'width'=>"150px", 'dbfield' => 'jocode'); 
$arrDataStructure['warehouse'] = array('title'=>ucwords($obj->lang['warehouse']),'dbfield' => 'warehousename', 'width'=>"100px" );
$arrDataStructure['service'] = array('title'=>ucwords($obj->lang['service']),  'width'=>"300px", 'dbfield' => 'servicename'); 
$arrDataStructure['currencyshort'] = array('title'=>ucwords($obj->lang['currencyShort']),'dbfield' => 'currencyname', 'width'=>"50px" );
$arrDataStructure['amount'] = array('title'=>ucwords($obj->lang['amount']),'dbfield' => 'amount', 'width'=>"100px" ,'format'=>'autodecimal');
$arrDataStructure['outstanding'] = array('title'=>ucwords($obj->lang['outstanding']),'dbfield' => 'outstanding', 'width'=>"100px" ,'format'=>'autodecimal');
$arrDataStructure['note'] = array('title'=>ucwords($obj->lang['note']),'dbfield' => 'trdesc','width'=>"300px");
$arrDataStructure['status'] = array('title'=>ucwords($obj->lang['status']),'dbfield' => 'statusname', 'width'=>"100px" );
		  
$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $obj->lang['prepaidCostReport'];
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();

array_push($arrTemplate, $arrHeaderTemplate);


if (isset($_POST) && !empty($_POST['hidAction'])){   
	
	$criteria = '';
    $criteriaArr = array();

    array_push($criteriaArr, array('postVariable' => array('trStartDate', 'trEndDate'), 
								   'fieldName' => $obj->tableName.'.trdate', 
								   'label' =>  $obj->lang['period'], 
								   'type' => 'daterange'));

    array_push($criteriaArr, array('postVariable' => 'code', 
								   'fieldName' => $obj->tableName.'.code', 
								   'label' => $obj->lang['code']));

    array_push($criteriaArr, array('postVariable' => 'refCode', 
								   'fieldName' => $obj->tableName.'.refcode', 
								   'label' => $obj->lang['refCode']));

    array_push($criteriaArr, array('postVariable' => 'soCode', 
								   'fieldName' => $EMKLJobOrder->tableName.'.code', 
								   'label' => $obj->lang['soCode']));

    array_push($criteriaArr, array('postVariable' => 'selWarehouse', 
								   'fieldName' => $obj->tableName.'.warehousekey', 
								   'label' => $obj->lang['warehouse'], 
								   'useArrayKey' => array('obj' => $warehouse) ));

    array_push($criteriaArr, array('postVariable' => 'selCurrency', 
								   'fieldName' => $obj->tableName.'.currencykey', 
								   'label' => $obj->lang['currency'], 
								   'useArrayKey' => array('obj' => $currency) ));
    
    array_push($criteriaArr, array('postVariable' => 'selService', 
								   'fieldName' => $obj->tableName.'.costkey', 
								   'label' => $obj->lang['service'], 
								   'useArrayKey' => array('obj' => $service) ));
    
    array_push($criteriaArr, array('postVariable' => 'selStatus',
								   'type' => 'status'));

    $obj->createReportCriteria($criteria,$arrFilterInformation,$criteriaArr);
    
    $orderBy = (!empty($_POST['hidOrderBy'])) ? $obj->oDbCon->paramOrder($_POST['hidOrderBy']) : 'pkey'; // order by harus dr kolom yg terdaftar saja
    $orderType = (isset($_POST['hidOrderType']) && !empty($_POST['hidOrderType']) && $_POST['hidOrderType'] == 1) ? 'desc' : 'asc';

	 
	$order = 'order by '.$orderBy.' ' .$orderType; 
	 
	$rs = $obj->searchData('','',true,$criteria,$order);
    $totalCurr = count(array_unique(array_column($rs,'currencykey')));     
    $calculateTotal = ($totalCurr <= 1) ? true : false;
    
    
    $tempreport = '';  
	 
    for( $i=0;$i<count($rs);$i++) {   
        $arrTemplate[0]['dataStructure']['amount']['calculateTotal'] = $calculateTotal;
        $arrTemplate[0]['dataStructure']['outstanding']['calculateTotal'] = $calculateTotal;

        $return = $obj->formatReportRows(array('data' => $rs[$i]),$arrTemplate); 

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

$arrStatus = $class->convertForCombobox($obj->getAllStatus(),'pkey','status');
$arrWarehouse = $class->convertForCombobox($warehouse->searchData($warehouse->tableName.'.statuskey',1,true,'','order by name asc'),'pkey','name');
$arrCurrency = $class->convertForCombobox($currency->searchData($currency->tableName.'.statuskey',1,true,'','order by name asc'),'pkey','name');
$arrService = $class->convertForCombobox($service->searchData($service->tableName.'.statuskey',1,true,'','order by name asc'),'pkey','name');

$arrTwigVar['inputCode'] =  $class->inputText('code');
$arrTwigVar['inputRefCode'] =  $class->inputText('refCode'); 
$arrTwigVar['inputSoCode'] =  $class->inputText('soCode'); 
$arrTwigVar['inputStartDate'] = $class->inputDate('trStartDate',array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputEndDate'] = $class->inputDate('trEndDate',array('etc' => 'style="text-align:center"'));  
$arrTwigVar['inputSelStatus'] =  $class->inputSelect('selStatus[]', $arrStatus, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox')); 
$arrTwigVar['inputSelWarehouse'] =  $class->inputSelect('selWarehouse[]', $arrWarehouse, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputSelService'] =  $class->inputSelect('selService[]', $arrService, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputSelCurrency'] =  $class->inputSelect('selCurrency[]', $arrCurrency,  array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
      

$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate; 
       
echo $twig->render('reportPrepaidExpense.html', $arrTwigVar);   
?>
