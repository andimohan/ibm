<?php
	 
include '../../_config.php';  
include '../../_include-v2.php'; 

includeClass(array('CustomerIssue.class.php','SalesOrder.class.php','Customer.class.php'));
$customerIssue = createObjAndAddToCol( new CustomerIssue()); 
$salesOrder = createObjAndAddToCol( new SalesOrder()); 
$customer = createObjAndAddToCol( new Customer()); 

include '_global.php';

$obj= $customerIssue;
$securityObject = 'customerIssueReport'; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true));  
 
$arrStatus = $obj->getAllStatus();

$arrFilterInformation = array();     
 
// ===== FOR EXPORT SECTION
$dataToExport = array();

/* data structure */
$arrTemplate = array();

$arrDataStructure = array();
$_POST['module'] = IMPORT_TEMPLATE['customer'];


if(empty($_POST['hidAction'])){
    $_POST['trStartDate'] = date('d / m / Y');
	$_POST['trEndDate'] = date('d / m / Y');  
    $_POST['isShowDetail'] = true;
}

$isShowDetail = (isset($_POST['isShowDetail']) && !empty($_POST['isShowDetail'])) ? true : false;

switch($EXPORT_TYPE){
    case 2 :
            $arrDataStructure['code'] = array('title'=>ucwords($obj->lang['code']),'dbfield' => 'code', 'width'=>"100px");
            $arrDataStructure['name'] = array('title'=>ucwords($obj->lang['name']),'dbfield' => 'name', 'width'=>"200px");

            break;
        
    default :
            $arrDataStructure['rowNumber'] = array('title'=>'#', 'align'=>'right', 'width'=>"40px", 'autoNumber' => true, "sortable" => false);
            $arrDataStructure['code'] = array('title'=>ucwords($obj->lang['code']),'dbfield' => 'code', 'width'=>"100px");
            $arrDataStructure['date'] = array('title'=>ucwords($obj->lang['date']),'dbfield' => 'createdon', 'width'=>"120px",'format'=>'date');
            $arrDataStructure['customer'] = array('title'=>ucwords($obj->lang['customer']),'dbfield' => 'customername', 'width'=>"200px");
            $arrDataStructure['invoiceCode'] = array('title'=>ucwords($obj->lang['invoiceCode']),'dbfield' => 'salesordercode', 'width'=>"100px");
            $arrDataStructure['subject'] = array('title'=>ucwords($obj->lang['subject']),'dbfield' => 'subject', 'width'=>"150px");
            $arrDataStructure['issue'] = array('title'=>ucwords($obj->lang['issue']),'dbfield' => 'issue', 'width'=>"200px");
            $arrDataStructure['status'] = array('title'=>ucwords($obj->lang['status']),'dbfield' => 'statusname', 'width'=>"100px");
}
  
$arrHeaderTemplate = array();  
$arrHeaderTemplate['reportTitle'] = $obj->lang['customerIssueReport']; 
$arrHeaderTemplate['dataStructure'] = $arrDataStructure; 
$arrHeaderTemplate['total'] = array();
 
array_push($arrTemplate, $arrHeaderTemplate);

if ($isShowDetail){ 
    // detail ...
    $arrDataDetailStructure = array(); 
    $arrDataDetailStructure['itemCode'] = array('title'=>ucwords($obj->lang['itemCode']),  'dbfield' => 'itemcode', 'width'=>"100px" );  
    $arrDataDetailStructure['itemName'] = array('title'=>ucwords($obj->lang['itemName']),  'dbfield' => 'itemname', 'width'=>"240px" );  
    if (PLAN_TYPE['categorykey'] == COMPANY_TYPE['jewelry'])
        $arrDataDetailStructure['serialNumber'] = array('title'=>ucwords($obj->lang['serialNumber']),'dbfield' => 'serialnumber', 'width'=>"130px"); 
    $arrDataDetailStructure['brandname'] = array('title'=>ucwords($obj->lang['brand']),'dbfield' => 'brandname', 'width'=>"130px"); 
    $arrDataDetailStructure['qty'] = array('title'=>ucwords($obj->lang['qty']),  'dbfield' => 'qty', 'width'=>"60px" , 'format' => 'number'); 
    $arrDataDetailStructure['unit'] = array('title'=>ucwords($obj->lang['unit']),  'dbfield' => 'unitname', 'width'=>"100px" ); 
    //$arrDataDetailStructure['deliveredQty'] = array('title'=>ucwords($obj->lang['deliveredQty']),  'dbfield' => 'deliveredqty', 'width'=>"180px");
    $arrDataDetailStructure['priceInUnit'] = array('title'=>ucwords($obj->lang['price']),'dbfield' => 'priceinunit', 'width'=>"100px",'format'=>'number');
    $arrDataDetailStructure['discount'] = array('title'=>ucwords($obj->lang['discount']),'dbfield' => 'discount', 'width'=>"100px",'format'=>'number');
    $arrDataDetailStructure['total'] = array('title'=>ucwords($obj->lang['total']),'dbfield' => 'total', 'width'=>"100px",'format'=>'number');

    $arrDetailTemplate = array(); 
    $arrDetailTemplate['dataStructure'] = $arrDataDetailStructure;
    $arrDetailTemplate['total'] = array();

    array_push($arrTemplate, $arrDetailTemplate); 
}

// ===== END FOR EXPORT SECTION

if (isset($_POST) && !empty($_POST['hidAction'])){  
		
    $orderBy = (!empty($_POST['hidOrderBy'])) ? $obj->oDbCon->paramOrder($_POST['hidOrderBy']) : 'pkey'; // order by harus dr kolom yg terdaftar saja
    $orderType = (isset($_POST['hidOrderType']) && !empty($_POST['hidOrderType']) && $_POST['hidOrderType'] == 1) ? 'desc' : 'asc';
 
	if(empty($_POST['hidRs'])){ 
		$result = queryNewReport(get_defined_vars(), array('orderBy' => $orderBy, 'orderType' => $orderType));
		$rs = $result['rs'];
		$rsDetailCol = $result['rsdetail'];
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

        $soKey = $rs[$i]['sokey'];
        if($isShowDetail){ 
         
            if (!isset($rsDetailCol[$soKey]))  continue;
            $rsDetail = $rsDetailCol[$soKey]; 

			    
            $rs[$i]['_detail_'] = array('arrTemplate'=>$arrDetailTemplate,'data' => $rsDetail, 'style' => $arrDetailStyle); 
        }
    
        $return = $obj->formatReportRows(array('data' => $rs[$i], 'totalFreezeCol' => $_POST['hidTotalFreezeCol']),$arrTemplate);
        
        // ===== FOR EXPORT SECTION 
        array_push($dataToExport, $return['data']); 
        // ===== END FOR EXPORT SECTION
        
        $tempreport .= $return['html']; 
         
        // count subtotal for each col
        $arrTemplate[0]['total'] = $obj->arraySum($arrTemplate[0]['total'], $return['subtotal'][0]); 
         
    }
		 
    $obj->generateReport($_POST, $tempreport, $arrTemplate,array('dataToExport' => $dataToExport,'rs' => $rs),$arrFilterInformation);
}

$arrStatus = $class->convertForCombobox($arrStatus,'pkey','status');  
$arrCustomer = $class->convertForCombobox($customer->searchData($customer->tableName.'.statuskey',2,true,'','order by name asc'),'pkey','name');
   
$arrTwigVar['importUrl'] = $obj->importUrl; 
$arrTwigVar['inputCustomerIssueCode'] =  $class->inputText('customerIssueCode');  
$arrTwigVar['inputSelStatus'] =  $class->inputSelect('selStatus[]', $arrStatus, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox')); 
$arrTwigVar['inputSelCustomer'] =  $class->inputSelect('selCustomer[]', $arrCustomer, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputStartDate'] = $class->inputDate('trStartDate', array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputEndDate'] = $class->inputDate('trEndDate', array('etc' => 'style="text-align:center"')); 
$arrTwigVar['inputIsShowDetail'] =  $class->inputCheckBox('isShowDetail');
$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate;   


echo $twig->render('reportCustomerIssue.html', $arrTwigVar);  

function queryNewReport($varCol = array(),$order){ 
	foreach($varCol as $key=>$row) $$key = $varCol[$key];
		 
	$arrFilterInformation = array();
	
	$criteria = '';
	if(isset($_POST) && !empty($_POST['customerIssueCode'])) {
		$criteria .= ' AND '.$obj->tableName.'.code LIKE ('.$class->oDbCon->paramString('%'.$_POST['customerIssueCode'].'%').')';
		array_push($arrFilterInformation,array("label" => 'Kode', 'filter' => $_POST['customerIssueCode']));
	}

    if(isset($_POST) && !empty($_POST['selCustomer'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selCustomer']));   
        
       	$criteria .= ' AND customerkey in('.$key.')';  

        $rsCriteria = $customer->searchData('','',true, ' and '.$customer->tableName.'.pkey in ('.$key.')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$statusName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => 'Pelangan', 'filter' => $statusName ));
        
	}	

    if(isset($_POST) && !empty($_POST['trStartDate'])){
		$criteria .= ' and '.$obj->tableName.'.createdon between '.$class->oDbCon->paramDate( $_POST['trStartDate'],' / ').' AND '.$class->oDbCon->paramDate( $_POST['trEndDate'],' / '); 
		array_push($arrFilterInformation,array("label" => 'Tanggal', 'filter' => $_POST['trStartDate'] . ' - ' .$_POST['trEndDate'] ));
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
	  
	 
	$order = 'order by '.$order['orderBy'].' ' .$order['orderType']; 
      
	$rs = $obj->searchData('','',true,$criteria,$order);
    $arrSO = array_column($rs,'sokey');
    $rsDetail = $salesOrder->getDetailWithRelatedInformation($arrSO);
    $rsDetail = $obj->reindexDetailCollections($rsDetail, 'refkey');
	 
	
	return array(
		'arrFilterInformation' => $arrFilterInformation,
		'rs' => $rs,
		'rsdetail' => $rsDetail
	);
}

?>