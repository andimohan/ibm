<?php
	 
include '../../_config.php';  
include '../../_include.php';
include '_global.php';

$obj= $service;
$securityObject = 'reportServices'; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true));   

$arrFilterInformation = array();     
 
// ===== FOR EXPORT SECTION
$dataToExport = array();

/* data structure */
$arrTemplate = array();

$arrDataStructure = array();
$_POST['module'] = IMPORT_TEMPLATE['service'];

$arrStatus = $obj->getAllStatus();
$arrCategory = $serviceCategory->searchData($serviceCategory->tableName.'.statuskey',1,true, ' and '.$serviceCategory->tableName.'.isleaf = 1', ' order by name asc');

switch($EXPORT_TYPE){
    case 2 :
            $arrDataStructure['code'] = array('title'=>ucwords($obj->lang['code']),'dbfield' => 'code', 'width'=>"100px");
            $arrDataStructure['name'] = array('title'=>ucwords($obj->lang['name']),'dbfield' => 'name', 'width'=>"200px");
            //$arrDataStructure['aliasname'] = array('title'=>ucwords($obj->lang['alias']),'dbfield' => 'aliasname', 'width'=>"200px");    
            $arrDataStructure['category'] = array('title'=>ucwords($obj->lang['category']),'dbfield' => 'categoryname', 'width'=>"100px",'validation' => array_column($arrCategory,'name'));
            $arrDataStructure['sellingPrice'] = array('title'=>ucwords($obj->lang['sellingPrice']),'dbfield' => 'sellingprice', 'width'=>"100px",'format'=>"number");        
            $arrDataStructure['revenueAccount'] = array('title'=>ucwords($obj->lang['revenueAccount']),'dbfield' => 'revenuecoaname', 'width'=>"200px");            
            $arrDataStructure['prepaidExpenseAccount'] = array('title'=>ucwords($obj->lang['prepaidExpense']),'dbfield' => 'prepaidexpensecoaname', 'width'=>"200px");            
            $arrDataStructure['costAccount'] = array('title'=>ucwords($obj->lang['costAccount']),'dbfield' => 'costcoaname', 'width'=>"200px");            
            $arrDataStructure['shortDescription'] = array('title'=>ucwords($obj->lang['shortDescription']),'dbfield' => 'shortdescription', 'width'=>"200px",);            
            $arrDataStructure['status'] = array('title'=>ucwords($obj->lang['status']),'dbfield' => 'statusname', 'width'=>"100px", 'validation' => array_column($arrStatus,'status'));
            break;
        
    default :
            $arrDataStructure['rowNumber'] = array('title'=>'#', 'align'=>'right', 'width'=>"40px", 'autoNumber' => true, "sortable" => false);
            $arrDataStructure['code'] = array('title'=>ucwords($obj->lang['code']),'dbfield' => 'code', 'width'=>"100px");
            $arrDataStructure['name'] = array('title'=>ucwords($obj->lang['name']),'dbfield' => 'name', 'width'=>"200px");
            $arrDataStructure['aliasname'] = array('title'=>ucwords($obj->lang['alias']),'dbfield' => 'aliasname', 'width'=>"200px");
            $arrDataStructure['category'] = array('title'=>ucwords($obj->lang['category']),'dbfield' => 'categoryname', 'width'=>"100px");            
            $arrDataStructure['sellingPrice'] = array('title'=>ucwords($obj->lang['sellingPrice']),'dbfield' => 'sellingprice', 'width'=>"100px",'format'=>"number");            
            $arrDataStructure['revenueAccount'] = array('title'=>ucwords($obj->lang['revenueAccount']),'dbfield' => 'revenuecoaname', 'width'=>"200px");            
            $arrDataStructure['prepaidExpenseAccount'] = array('title'=>ucwords($obj->lang['prepaidExpense']),'dbfield' => 'prepaidexpensecoaname', 'width'=>"200px"); 
            $arrDataStructure['costAccount'] = array('title'=>ucwords($obj->lang['costAccount']),'dbfield' => 'costcoaname', 'width'=>"200px");                    
            $arrDataStructure['shortDescription'] = array('title'=>ucwords($obj->lang['shortDescription']),'dbfield' => 'shortdescription', 'width'=>"200px");            
            $arrDataStructure['status'] = array('title'=>ucwords($obj->lang['status']),'dbfield' => 'statusname', 'width'=>"100px");

}
  
$arrHeaderTemplate = array();  
$arrHeaderTemplate['reportTitle'] = $obj->lang['serviceReport']; 
$arrHeaderTemplate['dataStructure'] = $arrDataStructure; 
$arrHeaderTemplate['total'] = array();
 
array_push($arrTemplate, $arrHeaderTemplate);

// ===== END FOR EXPORT SECTION

if (isset($_POST) && !empty($_POST['hidAction'])){  
		
	$criteria = '';
	if(isset($_POST) && !empty($_POST['serviceCode'])) {
		$criteria .= ' AND '.$obj->tableName.'.code LIKE ('.$class->oDbCon->paramString('%'.$_POST['serviceCode'].'%').')';
		array_push($arrFilterInformation,array("label" => 'Kode', 'filter' => $_POST['serviceCode']));
	}
    
	if(isset($_POST) && !empty($_POST['serviceName'])) {
		$criteria .= ' AND '.$obj->tableName.'.name LIKE  ('.$class->oDbCon->paramString('%'.$_POST['serviceName'].'%').')'; 
		array_push($arrFilterInformation,array("label" => 'Nama', 'filter' =>  $_POST['serviceName']));
	} 
    
     if(isset($_POST) && !empty($_POST['selCategory'])) { 
         
        $key = implode(",", $class->oDbCon->paramString($_POST['selCategory']));   
        
        $criteria .= ' AND categorykey in('.$key.')';  

        $rsCriteria = $serviceCategory->searchData('','',true, ' and '.$serviceCategory->tableName.'.pkey in ('.$key.')');
	 
        $arrTempCategory = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempCategory,$rsCriteria[$k]['name']);
			
		$categoryName = implode(", ",$arrTempCategory); 
	    array_push($arrFilterInformation,array("label" => 'Kategori', 'filter' => $categoryName));
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

        switch($EXPORT_TYPE){
            case 2 : 
                $rsPath = $serviceCategory->getPath($rs[$i]['categorykey']);
                $rs[$i]['categoryname']  = $rsPath[0]['path'];
                break;
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
 $arrCategory = $class->convertForCombobox($arrCategory,'pkey','name');   

$arrTwigVar['importUrl'] = $obj->importUrl; 


$arrTwigVar['inputServiceCode'] =  $class->inputText('serviceCode');  
$arrTwigVar['inputServiceName'] =  $class->inputText('serviceName');   
$arrTwigVar['inputSelCategory'] =  $class->inputSelect('selCategory[]', $arrCategory, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox')); 
$arrTwigVar['inputSelStatus'] =  $class->inputSelect('selStatus[]', $arrStatus, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox')); 
$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate;   
      
echo $twig->render('reportServices.html', $arrTwigVar);  
 
?>

