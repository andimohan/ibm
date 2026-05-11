<?php
	 
include '../../_config.php';  
include '../../_include-v2.php';

includeClass('MedicalPurchaseOrder.class.php');
$medicalPurchaseOrder = createObjAndAddToCol(new MedicalPurchaseOrder());
$city = createObjAndAddToCol(new City());
$supplier = createObjAndAddToCol(new Supplier());
$customerCategory = createObjAndAddToCol(new CustomerCategory());
$medicalRequestClaim = createObjAndAddToCol(new MedicalRequestClaim());
$medicalJobOrder = createObjAndAddToCol(new MedicalJobOrder());


include '_global.php';

$obj= $medicalPurchaseOrder;
$securityObject = 'reportMedicalPurchaseOrder'; // the value of security object is manually inserted to handle 
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
$_POST['module'] = IMPORT_TEMPLATE['medicalPurchaseOrder'];

$isDetail = (isset($_POST['isDetail']) && !empty($_POST['isDetail'])) ? true : false;

switch($EXPORT_TYPE){
    case 2 :
        $arrDataStructure['rowNumber'] = array('title' => '#', 'align' => 'right', 'width' => "40px", 'autoNumber' => true, "sortable" => false);
        $arrDataStructure['code'] = array('title' => ucwords($obj->lang['code']),  'width' => "150px", 'dbfield' => 'code');
        $arrDataStructure['trDate'] = array('title' => ucwords($obj->lang['date']),  'width' => "150px", 'dbfield' => 'trdate');
        $arrDataStructure['JOCode'] = array('title' => ucwords($obj->lang['JOCode']),  'width' => "150px", 'dbfield' => 'jobordercode');
        $arrDataStructure['warehouse'] = array('title' => ucwords($obj->lang['warehouse']),  'width' => "150px", 'dbfield' => 'warehousename');
        $arrDataStructure['type'] = array('title' => ucwords($obj->lang['type']),  'width' => "150px", 'dbfield' => 'guaranteelettertype');
        $arrDataStructure['insuredName'] = array('title' => ucwords($obj->lang['insuredName']),  'width' => "150px", 'dbfield' => 'insuredname');
        $arrDataStructure['supplier'] = array('title' => ucwords($obj->lang['supplier']), 'dbfield' => 'suppliername', 'width' => "150px");
        $arrDataStructure['company'] = array('title' => ucwords($obj->lang['company']), 'dbfield' => 'companyname', 'width' => "200px");
        $arrDataStructure['insuranceCompany'] = array('title' => ucwords($obj->lang['insuranceCompany']), 'dbfield' => 'suppliername', 'width' => "200px");
        $arrDataStructure['diagnose'] = array('title' => ucwords($obj->lang['diagnose']), 'dbfield' => 'initialdiagnose', 'width' => "300px");
        $arrDataStructure['excess'] = array('title'=>ucwords($obj->lang['excess']),'dbfield' => 'excessfee','align'=>'right', 'width'=>"110px",'format'=>'number'); 
        $arrDataStructure['description'] = array('title' => ucwords($obj->lang['description']), 'dbfield' => 'trdesc', 'width' => "300px");
        $arrDataStructure['status'] = array('title'=>ucwords($obj->lang['status']),'dbfield' => 'statusname', 'width'=>"100px");
        break;

    default :
        $arrDataStructure['rowNumber'] = array('title' => '#', 'align' => 'right', 'width' => "40px", 'autoNumber' => true, "sortable" => false);
        $arrDataStructure['code'] = array('title' => ucwords($obj->lang['code']),  'width' => "150px", 'dbfield' => 'code');
        $arrDataStructure['trDate'] = array('title' => ucwords($obj->lang['date']),  'width' => "150px", 'dbfield' => 'trdate');
        $arrDataStructure['JOCode'] = array('title' => ucwords($obj->lang['JOCode']),  'width' => "150px", 'dbfield' => 'jobordercode');
        $arrDataStructure['warehouse'] = array('title' => ucwords($obj->lang['warehouse']),  'width' => "150px", 'dbfield' => 'warehousename');
        $arrDataStructure['type'] = array('title' => ucwords($obj->lang['type']),  'width' => "150px", 'dbfield' => 'guaranteelettertype');
        $arrDataStructure['insuredName'] = array('title' => ucwords($obj->lang['insuredName']),  'width' => "150px", 'dbfield' => 'insuredname');
        $arrDataStructure['supplier'] = array('title' => ucwords($obj->lang['supplier']), 'dbfield' => 'suppliername', 'width' => "150px");
        $arrDataStructure['company'] = array('title' => ucwords($obj->lang['company']), 'dbfield' => 'companyname', 'width' => "200px");
        $arrDataStructure['insuranceCompany'] = array('title' => ucwords($obj->lang['insuranceCompany']), 'dbfield' => 'suppliername', 'width' => "200px");
        $arrDataStructure['diagnose'] = array('title' => ucwords($obj->lang['diagnose']), 'dbfield' => 'initialdiagnose', 'width' => "300px");
        $arrDataStructure['excess'] = array('title'=>ucwords($obj->lang['excess']),'dbfield' => 'excessfee','align'=>'right', 'width'=>"110px",'format'=>'number'); 
        $arrDataStructure['description'] = array('title' => ucwords($obj->lang['description']), 'dbfield' => 'trdesc', 'width' => "300px");
        $arrDataStructure['status'] = array('title'=>ucwords($obj->lang['status']),'dbfield' => 'statusname', 'width'=>"100px");

    }

$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $obj->lang['guaranteeLetterReport'];
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

    if(isset($_POST) && !empty($_POST['trStartDate'])){
		$criteria .= ' and '.$obj->tableName.'.trdate'.' between '.$class->oDbCon->paramDate( $_POST['trStartDate'],' / ').' AND '.$class->oDbCon->paramDate( $_POST['trEndDate'],' / '); 
		array_push($arrFilterInformation,array("label" => $obj->lang['period'], 'filter' => $_POST['trStartDate'] . ' - ' .$_POST['trEndDate'] ));
	}
    
	if(isset($_POST) && !empty($_POST['code'])) {
		$criteria .= ' AND '.$obj->tableName.'.code LIKE ('.$class->oDbCon->paramString('%'.$_POST['code'].'%').')';
		array_push($arrFilterInformation,array("label" => 'Kode', 'filter' => $_POST['code']));
	}
	if(isset($_POST) && !empty($_POST['JOCode'])) {
		$criteria .= ' AND '.$medicalJobOrder->tableName.'.code LIKE ('.$class->oDbCon->paramString('%'.$_POST['JOCode'].'%').')';
		array_push($arrFilterInformation,array("label" => 'Kode', 'filter' => $_POST['JOCode']));
	}
	if(isset($_POST) && !empty($_POST['insuredName'])) {
		$criteria .= ' AND '.$medicalRequestClaim->tableName.'.insuredName LIKE  ('.$class->oDbCon->paramString('%'.$_POST['insuredName'].'%').')'; 
		array_push($arrFilterInformation,array("label" => $obj->lang['insuredName'], 'filter' =>  $_POST['insuredName']));
	}
    
    if(isset($_POST) && !empty($_POST['selGuaranteeType'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selGuaranteeType']));   
        
       	$criteria .= ' AND '.$obj->tableName.'.guaranteetype in('.$key.')';  

        $arrGuaranteeType = array();
        $arrGuaranteeType[1] = 'Initial Guarantee';
        $arrGuaranteeType[2] = 'Final Guarantee';
	 
        $arrTempGuaranteeType = array();
		for ($k=0;$k<count($_POST['selGuaranteeType']);$k++)
		 	array_push($arrTempGuaranteeType,$arrGuaranteeType[$k+1]);
			
		$arrGuaranteeTypeName = implode(", ",$arrTempGuaranteeType); 
	    array_push($arrFilterInformation,array("label" => $obj->lang['type'], 'filter' => $arrGuaranteeTypeName ));
        
	}

    if(isset($_POST) && !empty($_POST['selSupplier'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selSupplier']));   
        
       	$criteria .= ' AND '.$obj->tableName.'.supplierkey in('.$key.')';  

        $rsCriteria = $supplier->searchData('','',true, ' and '.$supplier->tableName.'.pkey in ('.$key.')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$statusName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => $obj->lang['supplier'], 'filter' => $statusName ));
        
	}
    if(isset($_POST) && !empty($_POST['selInsuranceCompany'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selInsuranceCompany']));   
        
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
    
    $orderBy = (!empty($_POST['hidOrderBy'])) ? $obj->oDbCon->paramOrder($_POST['hidOrderBy']) : 'pkey'; // order by harus dr kolom yg terdaftar saja
    $orderType = (isset($_POST['hidOrderType']) && !empty($_POST['hidOrderType']) && $_POST['hidOrderType'] == 1) ? 'desc' : 'asc';
    
	 
	$order = 'order by '.$orderBy.' ' .$orderType; 
      
	$rs = $obj->searchData('','',true,$criteria,$order);
    $rsReIndex = array_column($rs, null, 'pkey');
    $rsDetailCol = ($isDetail) ? $obj->getDetailCollections($rs,'refkey') : array();
    $tempreport = ''; 

    foreach ($rsReIndex as $index) {
        $rsInitialDiagnoseDetail = $medicalJobOrder->getDetailDiagnose($index['refkey']);
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
   
        $rs[$i]['guaranteelettertype'] = ($rs[$i]['guaranteetype']==1) ? 'Initial Guarantee' : 'Final Guarantee';
         
        $rsDetail = $rsDetailCol[$pkey]; 
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

$arrGuaranteeType = array();
$arrGuaranteeType[1] = 'Initial Guarantee';
$arrGuaranteeType[2] = 'Final Guarantee';


$arrTwigVar['inputStartDate'] = $class->inputDate('trStartDate', array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputEndDate'] = $class->inputDate('trEndDate', array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputSelStatus'] =  $class->inputSelect('selStatus[]', $arrStatus, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox')); 
$arrTwigVar['inputSelGuaranteeType'] =  $class->inputSelect('selGuaranteeType[]', $arrGuaranteeType, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox')); 
$arrTwigVar['inputCode'] =  $class->inputText('code');  
$arrTwigVar['inputJOCode'] =  $class->inputText('JOCode');  
$arrTwigVar['inputIsDetail'] =  $class->inputCheckBox('isDetail', array('value'=> 1));
$arrTwigVar['inputInsuredName'] =  $class->inputText('insuredName');   
$arrTwigVar['inputSelSupplier'] =  $class->inputSelect('selSupplier[]', $arrSupplier, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputselInsuranceCompany'] =  $class->inputSelect('selInsuranceCompany[]', $arrSupplier, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate;   
      
echo $twig->render('reportMedicalPurchaseOrder.html', $arrTwigVar);  
 
?>