<?php
	 
include '../../_config.php';  
include '../../_include.php';
include '_global.php';

$obj= $cityCategory;
$securityObject = 'reportCityCategory'; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true));   

$arrFilterInformation = array();     
 
// ===== FOR EXPORT SECTION
$dataToExport = array();

/* data structure */
$arrTemplate = array();

$arrDataStructure = array();
$_POST['module'] = IMPORT_TEMPLATE['cityCategory'];

$arrStatus = $obj->getAllStatus();

switch($EXPORT_TYPE){
    case 2 :
            $arrDataStructure['code'] = array('title'=>ucwords($obj->lang['code']),'dbfield' => 'code', 'width'=>"100px");
            $arrDataStructure['name'] = array('title'=>ucwords($obj->lang['name']),'dbfield' => 'name', 'width'=>"200px");
            $arrDataStructure['status'] = array('title'=>ucwords($obj->lang['status']),'dbfield' => 'statusname', 'width'=>"100px", 'validation' => array_column($arrStatus,'status'));
            break;
        
    default :
            $arrDataStructure['rowNumber'] = array('title'=>'#', 'align'=>'right', 'width'=>"40px", 'autoNumber' => true, "sortable" => false);
            $arrDataStructure['code'] = array('title'=>ucwords($obj->lang['code']),'dbfield' => 'code', 'width'=>"100px");
            $arrDataStructure['name'] = array('title'=>ucwords($obj->lang['name']),'dbfield' => 'name', 'width'=>"200px");
            $arrDataStructure['status'] = array('title'=>ucwords($obj->lang['status']),'dbfield' => 'statusname', 'width'=>"100px");

}
  
$arrHeaderTemplate = array();  
$arrHeaderTemplate['reportTitle'] = $obj->lang['cityCategoryReport']; 
$arrHeaderTemplate['dataStructure'] = $arrDataStructure; 
$arrHeaderTemplate['total'] = array();
 
array_push($arrTemplate, $arrHeaderTemplate);

// ===== END FOR EXPORT SECTION

if (isset($_POST) && !empty($_POST['hidAction'])){  
		
	$criteria = '';
	if(isset($_POST) && !empty($_POST['cityCategoryCode'])) {
		$criteria .= ' AND '.$obj->tableName.'.code LIKE ('.$class->oDbCon->paramString('%'.$_POST['cityCategoryCode'].'%').')';
		array_push($arrFilterInformation,array("label" => 'Kode', 'filter' => $_POST['cityCategoryCode']));
	}
    
	if(isset($_POST) && !empty($_POST['cityCategoryName'])) {
		$criteria .= ' AND '.$obj->tableName.'.name LIKE  ('.$class->oDbCon->paramString('%'.$_POST['cityCategoryName'].'%').')'; 
		array_push($arrFilterInformation,array("label" => 'Nama', 'filter' =>  $_POST['cityCategoryName']));
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
 
$arrTwigVar['importUrl'] = $obj->importUrl; 


$arrTwigVar['inputCityCategoryCode'] =  $class->inputText('cityCategoryCode');  
$arrTwigVar['inputCityCategoryName'] =  $class->inputText('cityCategoryName');   
$arrTwigVar['inputSelStatus'] =  $class->inputSelect('selStatus[]', $arrStatus, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox')); 
$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate;   
      
echo $twig->render('reportCityCategory.html', $arrTwigVar);  
 
?>