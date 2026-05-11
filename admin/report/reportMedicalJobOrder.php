<?php
	 
include '../../_config.php';  
include '../../_include-v2.php';

includeClass('MedicalJobOrder.class.php');
$medicalJobOrder = createObjAndAddToCol(new MedicalJobOrder());
$city = createObjAndAddToCol(new City());
$supplier = createObjAndAddToCol(new Supplier());
$customerCategory = createObjAndAddToCol(new CustomerCategory());
$customer = createObjAndAddToCol(new Customer());
$medicalRequestClaim = createObjAndAddToCol(new MedicalRequestClaim());
$customerInsurancePolicy = createObjAndAddToCol(new CustomerInsurancePolicy());


include '_global.php';

$obj= $medicalJobOrder;
$securityObject = 'ReportMedicalJobOrder'; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true));  

$arrFilterInformation = array();     
 
// ===== FOR EXPORT SECTION
$dataToExport = array();

// ====================== must be set before TWIG
if (!isset($_POST['trStartDate']) || empty($_POST['trStartDate'])){ 
	$_POST['trStartDate'] = date('d / m / Y');
	$_POST['trEndDate'] = date('d / m / Y');
}   

$_POST['selStatus[]'] = array(2,3);

/* data structure */
$arrTemplate = array();

$arrDataStructure = array();
$_POST['module'] = IMPORT_TEMPLATE['medicalJobOrder'];

$isDetail = (isset($_POST['isDetail']) && !empty($_POST['isDetail'])) ? true : false;

switch($EXPORT_TYPE){
    case 2 :
        $arrDataStructure['rowNumber'] = array('title' => '#', 'align' => 'right', 'width' => "40px", 'autoNumber' => true, "sortable" => false);
        $arrDataStructure['code'] = array('title' => ucwords($obj->lang['code']),  'width' => "150px", 'dbfield' => 'code');
        $arrDataStructure['date'] = array('title'=>ucwords($obj->lang['date']),'dbfield' => 'trdate', 'width'=>"90px",'format'=>'date');
        $arrDataStructure['log'] = array('title' => ucwords($obj->lang['log']),  'width' => "150px", 'dbfield' => 'codelog');
        $arrDataStructure['insuredName'] = array('title' => ucwords($obj->lang['insuredName']),  'width' => "150px", 'dbfield' => 'insuredname');
        $arrDataStructure['policyNumber'] = array('title' => ucwords($obj->lang['policyNumber']), 'dbfield' => 'policynumber', 'width' => "110px");
        $arrDataStructure['category'] = array('title' => ucwords($obj->lang['category']), 'dbfield' => 'categoryname', 'width' => "150px");
        $arrDataStructure['company'] = array('title' => ucwords($obj->lang['company']), 'dbfield' => 'customername', 'width' => "200px");
        $arrDataStructure['insuranceCompany'] = array('title' => ucwords($obj->lang['insuranceCompany']), 'dbfield' => 'suppliername', 'width' => "200px");
        $arrDataStructure['diagnose'] = array('title' => ucwords($obj->lang['diagnose']), 'dbfield' => 'initialdiagnose', 'width' => "300px");
        $arrDataStructure['email'] = array('title' => ucwords($obj->lang['email']), 'dbfield' => 'insuredemail', 'width' => "150px");
        $arrDataStructure['status'] = array('title'=>ucwords($obj->lang['status']),'dbfield' => 'statusname', 'width'=>"100px");
        break;

    default :
        $arrDataStructure['rowNumber'] = array('title' => '#', 'align' => 'right', 'width' => "40px", 'autoNumber' => true, "sortable" => false);
        $arrDataStructure['code'] = array('title' => ucwords($obj->lang['code']),  'width' => "150px", 'dbfield' => 'code');
        $arrDataStructure['date'] = array('title'=>ucwords($obj->lang['date']),'dbfield' => 'trdate', 'width'=>"90px",'format'=>'date');
        $arrDataStructure['log'] = array('title' => ucwords($obj->lang['log']),  'width' => "150px", 'dbfield' => 'codelog');
        $arrDataStructure['insuredName'] = array('title' => ucwords($obj->lang['insuredName']),  'width' => "150px", 'dbfield' => 'insuredname');
        $arrDataStructure['policyNumber'] = array('title' => ucwords($obj->lang['policyNumber']), 'dbfield' => 'policynumber', 'width' => "110px");
        $arrDataStructure['category'] = array('title' => ucwords($obj->lang['category']), 'dbfield' => 'categoryname', 'width' => "150px");
        $arrDataStructure['company'] = array('title' => ucwords($obj->lang['company']), 'dbfield' => 'customername', 'width' => "200px");
        $arrDataStructure['insuranceCompany'] = array('title' => ucwords($obj->lang['insuranceCompany']), 'dbfield' => 'suppliername', 'width' => "200px");
        $arrDataStructure['diagnose'] = array('title' => ucwords($obj->lang['diagnose']), 'dbfield' => 'initialdiagnose', 'width' => "300px");
        $arrDataStructure['email'] = array('title' => ucwords($obj->lang['email']), 'dbfield' => 'insuredemail', 'width' => "150px");
        $arrDataStructure['status'] = array('title'=>ucwords($obj->lang['status']),'dbfield' => 'statusname', 'width'=>"100px");

    }

$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $obj->lang['jobOrderReport'];
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();
 
array_push($arrTemplate, $arrHeaderTemplate);

if ($isDetail){ 
    // detail ...
    $arrDataDetailStructure = array(); 
    $arrDataDetailStructure['service'] = array('title'=>ucwords($obj->lang['service']),  'dbfield' => 'itemname', 'width'=>"150px" );  
    $arrDataDetailStructure['amount'] = array('title'=>ucwords($obj->lang['amount']),'dbfield' => 'qty', 'width'=>"80px",'format'=>'number','calculateTotal' => true);
    $arrDataDetailStructure['price'] = array('title'=>ucwords($obj->lang['price']),'dbfield' => 'priceinunit', 'width'=>"170px",'format'=>'number','calculateTotal' => true);
    $arrDataDetailStructure['price'] = array('title'=>ucwords($obj->lang['price']),'dbfield' => 'priceinunit', 'width'=>"170px",'format'=>'number','calculateTotal' => true);
    $arrDataDetailStructure['subtotal'] = array('title'=>ucwords($obj->lang['subtotal']),'dbfield' => 'total', 'width'=>"170px",'format'=>'number','calculateTotal' => true);
    $arrDataDetailStructure['description'] = array('title'=>ucwords($obj->lang['description']),  'dbfield' => 'trdesc', 'width'=>"300px" );

    $arrDetailTemplate = array(); 
    $arrDetailTemplate['dataStructure'] = $arrDataDetailStructure;
    $arrDetailTemplate['total'] = array();

    array_push($arrTemplate, $arrDetailTemplate); 
}

// ===== END FOR EXPORT SECTION

if (isset($_POST) && !empty($_POST['hidAction'])){  
		
	$criteria = '';
    
	if(isset($_POST) && !empty($_POST['code'])) {
		$criteria .= ' AND '.$obj->tableName.'.code LIKE ('.$class->oDbCon->paramString('%'.$_POST['code'].'%').')';
		array_push($arrFilterInformation,array("label" => 'Kode', 'filter' => $_POST['code']));
	}
	if(isset($_POST) && !empty($_POST['codeLog'])) {
		$criteria .= ' AND '.$medicalRequestClaim->tableName.'.code LIKE ('.$class->oDbCon->paramString('%'.$_POST['codeLog'].'%').')';
		array_push($arrFilterInformation,array("label" => $obj->lang['log'], 'filter' => $_POST['codeLog']));
	}
	if(isset($_POST) && !empty($_POST['insuredName'])) {
		$criteria .= ' AND '.$medicalRequestClaim->tableName.'.insuredName LIKE  ('.$class->oDbCon->paramString('%'.$_POST['insuredName'].'%').')'; 
		array_push($arrFilterInformation,array("label" => $obj->lang['insuredName'], 'filter' =>  $_POST['insuredName']));
	}
    if(isset($_POST) && !empty($_POST['policyNumber'])) {
		$criteria .= ' AND '.$customerInsurancePolicy->tableName.'.policynumber LIKE  ('.$class->oDbCon->paramString('%'.$_POST['policyNumber'].'%').')'; 
		array_push($arrFilterInformation,array("label" => $obj->lang['policyNumber'], 'filter' =>  $_POST['policyNumber']));
	}
	if(isset($_POST) && !empty($_POST['company'])) {
		$criteria .= ' AND '.$customer->tableName.'.name LIKE  ('.$class->oDbCon->paramString('%'.$_POST['company'].'%').')'; 
		array_push($arrFilterInformation,array("label" => $obj->lang['company'], 'filter' =>  $_POST['company']));
	}

    if(isset($_POST) && !empty($_POST['trStartDate'])){
		$criteria .= ' and '.$obj->tableName.'.trdate'.' between '.$class->oDbCon->paramDate( $_POST['trStartDate'],' / ').' AND '.$class->oDbCon->paramDate( $_POST['trEndDate'],' / '); 
		array_push($arrFilterInformation,array("label" => $obj->lang['period'], 'filter' => $_POST['trStartDate'] . ' - ' .$_POST['trEndDate'] ));
	}
    
    if(isset($_POST) && !empty($_POST['selSupplier'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selSupplier']));   
        
       	$criteria .= ' AND '.$medicalRequestClaim->tableName.'.supplierkey in('.$key.')';  

        $rsCriteria = $supplier->searchData('','',true, ' and '.$supplier->tableName.'.pkey in ('.$key.')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$statusName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => $obj->lang['insuranceCompany'], 'filter' => $statusName ));
        
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
    if(isset($_POST) && !empty($_POST['selCategory'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selCategory']));
        
       	$criteria .= ' AND '.$medicalRequestClaim->tableName.'.customercategorykey in('.$key.')';  

        $rsCriteria = $customerCategory->searchData('','',true, ' and '.$customerCategory->tableName.'.pkey in ('.$key.')');
	 
        $arrTempCategory = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempCategory,$rsCriteria[$k]['name']);
			
		$arrCategory = implode(", ",$arrTempCategory); 
	    array_push($arrFilterInformation,array("label" => 'Category', 'filter' => $arrCategory));
        
	}  
    
    $orderBy = (!empty($_POST['hidOrderBy'])) ? $obj->oDbCon->paramOrder($_POST['hidOrderBy']) : 'pkey'; // order by harus dr kolom yg terdaftar saja
    $orderType = (isset($_POST['hidOrderType']) && !empty($_POST['hidOrderType']) && $_POST['hidOrderType'] == 1) ? 'desc' : 'asc';
    
	 
	$order = 'order by '.$orderBy.' ' .$orderType; 
      
	$rs = $obj->searchData('','',true,$criteria,$order);
    $rsReIndex = array_column($rs, null, 'pkey');
    $rsDetailCol = ($isDetail) ? $obj->getDetailCollections($rs,'refkey') : array();
    $tempreport = ''; 

    foreach ($rsReIndex as $index) {
        $rsInitialDiagnoseDetail = $obj->getDetailDiagnose($index['pkey']);
        $arrInitialDiagnose = array(); 
        foreach($rsInitialDiagnoseDetail as $initialDiagnoseIndex) {
                array_push($arrInitialDiagnose, $initialDiagnoseIndex['codenameinitialdiagnose']);
            }
        $arrInitialDiagnose = implode("<br>",$arrInitialDiagnose);
        $rsReIndex[$index['pkey']]['data'] = $arrInitialDiagnose;
    }
    
    
    // ============================= GENERATE DATA ============================= 
 
    for( $i=0;$i<count($rs);$i++) {      
        $pkey = $rs[$i]['pkey'];
        $rs[$i]['initialdiagnose'] = $rsReIndex[$pkey]['data'];

        $rsDetail = $rsDetailCol[$pkey];  
        $totalRsDetail = count($rsDetail);
        if($isDetail){ 
            $rs[$i]['_detail_'] = array('arrTemplate'=>$arrDetailTemplate,'data' => $rsDetail); 
        }

        
        $return = $obj->formatReportRows(array('data' => $rs[$i]),$arrTemplate);
        
        // ===== FOR EXPORT SECTION 
        array_push($dataToExport, $return['data']); 
        // ===== END FOR EXPORT SECTION
        
        $tempreport .= $return['html']; 
         
        // count subtotal for each col
        $arrTemplate[0]['total'] = $obj->arraySum($arrTemplate[0]['total'], $return['subtotal'][0]); 
         
    }
		 
    $obj->generateReport($_POST, $tempreport, $arrTemplate,$dataToExport,$arrFilterInformation);
}
 
$arrStatus = $class->convertForCombobox($obj->getAllStatus(),'pkey','status');  
$arrSupplier = $class->convertForCombobox($supplier->searchData($supplier->tableName.'.statuskey',1,true,'','order by name asc'),'pkey','name');
$arrCategory = $class->convertForCombobox($customerCategory->searchData($customerCategory->tableName.'.statuskey',1,true,'','order by pkey asc'),'pkey','name');

$arrTwigVar['inputStartDate'] = $class->inputDate('trStartDate', array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputEndDate'] = $class->inputDate('trEndDate', array('etc' => 'style="text-align:center"'));   
$arrTwigVar['inputSelStatus'] =  $class->inputSelect('selStatus[]', $arrStatus, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox')); 
$arrTwigVar['inputSelCategory'] =  $class->inputSelect('selCategory[]', $arrCategory, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox')); 
$arrTwigVar['inputPolicyNumber'] =  $class->inputText('policyNumber');   
$arrTwigVar['inputCompany'] =  $class->inputText('company');   
$arrTwigVar['inputSalesCode'] =  $class->inputText('code'); 
$arrTwigVar['inputIsDetail'] =  $class->inputCheckBox('isDetail', array('value'=> 1));
$arrTwigVar['inputcodeLog'] =  $class->inputText('codeLog'); 
$arrTwigVar['inputInsuredName'] =  $class->inputText('insuredName');   
$arrTwigVar['inputSelSupplier'] =  $class->inputSelect('selSupplier[]', $arrSupplier, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate;   
      
echo $twig->render('reportMedicalJobOrder.html', $arrTwigVar);  
 
?>