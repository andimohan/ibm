<?php
	 
include '../../_config.php';  
include '../../_include-v2.php';

includeClass('NewsletterSubscription.class.php');
$newsletterSubscription = createObjAndAddToCol(new NewsletterSubscription());

include '_global.php';

$obj= $newsletterSubscription;
$securityObject = 'ReportNewsletterSubscription'; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true));  
$hasARAccess = $security->isAdminLogin($ar->securityObject,10);  
 

$arrFilterInformation = array();     
 
// ===== FOR EXPORT SECTION
$dataToExport = array();

/* data structure */
$arrTemplate = array();

$arrDataStructure = array();

$arrDataStructure['rowNumber'] = array('title'=>'#', 'align'=>'right', 'width'=>"40px", 'autoNumber' => true, "sortable" => false);
$arrDataStructure['code'] = array('title'=>ucwords($obj->lang['code']),'dbfield' => 'code', 'width'=>"100px");
$arrDataStructure['email'] = array('title'=>ucwords($obj->lang['email']),'dbfield' => 'email', 'width'=>"200px");
$arrDataStructure['phone'] = array('title'=>ucwords($obj->lang['phone']),'dbfield' => 'phone', 'width'=>"100px");
$arrDataStructure['status'] = array('title'=>ucwords($obj->lang['status']), 'width'=>"100px", 'dbfield'=>'statusname');
  
$arrHeaderTemplate = array();  
$arrHeaderTemplate['reportTitle'] = $obj->lang['newsletterSubscriptionReport']; 
$arrHeaderTemplate['dataStructure'] = $arrDataStructure; 
$arrHeaderTemplate['total'] = array();
 
array_push($arrTemplate, $arrHeaderTemplate);

// ===== END FOR EXPORT SECTION

if (isset($_POST) && !empty($_POST['hidAction'])){  
		
	$criteria = '';

    
	if(isset($_POST) && !empty($_POST['subscriptionCode'])) {
		$criteria .= ' AND '.$obj->tableName.'.code LIKE ('.$class->oDbCon->paramString('%'.$_POST['subscriptionCode'].'%').')';
		array_push($arrFilterInformation,array("label" => ucwords($obj->lang['code']), 'filter' => $_POST['subscriptionCode']));
	}
    
	if(isset($_POST) && !empty($_POST['subscriptionEmail'])) {
		$criteria .= ' AND '.$obj->tableName.'.email LIKE  ('.$class->oDbCon->paramString('%'.$_POST['subscriptionEmail'].'%').')'; 
		array_push($arrFilterInformation,array("label" => ucwords($obj->lang['email']), 'filter' =>  $_POST['subscriptionEmail']));
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
    
    // ============================= GENERATE DATA ============================= 
 
    for( $i=0;$i<count($rs);$i++) {      


        
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


$arrTwigVar['inputSubscriptiontEmail'] =  $class->inputText('subscriptionEmail');   
$arrTwigVar['inputSubscriptionCode'] =  $class->inputText('subscriptionCode');   
$arrTwigVar['inputSelStatus'] =  $class->inputSelect('selStatus[]', $arrStatus, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox')); 
$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate;   
      
echo $twig->render('reportNewsletterSubscription.html', $arrTwigVar);  
 
?>