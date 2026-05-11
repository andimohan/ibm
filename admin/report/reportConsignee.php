<?php
	 
include '../../_config.php';  
include '../../_include-v2.php';

includeClass('Consignee.class.php');
$consignee = createObjAndAddToCol(new Consignee());
include '_global.php';

$obj= $consignee;
$securityObject = 'reportConsignee'; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true)); 
  
$arrFilterInformation = array();    
	
// ===== FOR EXPORT SECTION
$dataToExport = array();

/* data structure */
$arrTemplate = array();

$arrDataStructure = array();

$_POST['module'] = IMPORT_TEMPLATE['consignee'];

switch($EXPORT_TYPE){
    case 2 :
        $arrDataStructure['code'] = array('title'=>ucwords($obj->lang['code']),'dbfield' => 'code', 'width'=>"100px");
        $arrDataStructure['name'] = array('title'=>ucwords($obj->lang['name']),'dbfield' => 'name', 'width'=>"300px");
        $arrDataStructure['address'] = array('title'=>ucwords($obj->lang['address']),'dbfield' => 'address', 'width'=>"300px");
        $arrDataStructure['location'] = array('title'=>ucwords($obj->lang['location']),'dbfield' => 'locationname', 'width'=>"150px");
    break;
        
        
    default : 
        $arrDataStructure['rowNumber'] = array('title'=>'#', 'align'=>'right', 'width'=>"40px", 'autoNumber' => true, "sortable" => false);
        $arrDataStructure['code'] = array('title'=>ucwords($obj->lang['code']),'dbfield' => 'code', 'width'=>"100px");
        $arrDataStructure['name'] = array('title'=>ucwords($obj->lang['name']),'dbfield' => 'name', 'width'=>"300px");
        $arrDataStructure['address'] = array('title'=>ucwords($obj->lang['address']),'dbfield' => 'address', 'width'=>"300px");
        $arrDataStructure['warehouse'] = array('title'=>ucwords($obj->lang['warehouse']),'dbfield' => 'warehousename', 'width'=>"150px");
        $arrDataStructure['location'] = array('title'=>ucwords($obj->lang['location']),'dbfield' => 'locationname', 'width'=>"150px");
        $arrDataStructure['status'] = array('title'=>ucwords($obj->lang['status']),'dbfield' => 'statusname', 'width'=>"100px");
}
 
$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $obj->lang['consigneeReport']; 
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();

array_push($arrTemplate, $arrHeaderTemplate);

if (isset($_POST) && !empty($_POST['hidAction'])){  
		
	$criteria = '';
    
    // untuk pencarian berdasarkan kode
	if(isset($_POST) && !empty($_POST['consigneeCode'])) {
		$criteria .= ' AND '.$obj->tableName.'.code LIKE ('.$class->oDbCon->paramString('%'.$_POST['consigneeCode'].'%').')';
		array_push($arrFilterInformation,array("label" => 'Kode', 'filter' => $_POST['consigneeCode']));
	}

    // untuk pencarian berdasarkan nama
    if(isset($_POST) && !empty($_POST['consigneeName'])) {
		$criteria .= ' AND '.$obj->tableName.'.name LIKE  ('.$class->oDbCon->paramString('%'.$_POST['consigneeName'].'%').')'; 
		array_push($arrFilterInformation,array("label" => 'Nama', 'filter' =>  $_POST['consigneeName']));
	} 
	 
    
    // untuk pencarian berdasarkan status
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
   
    for( $i=0;$i<count($rs);$i++) {    
   
        $return = $obj->formatReportRows(array('data' => $rs[$i]),$arrTemplate);
        // ===== FOR EXPORT SECTION 
        array_push($dataToExport, $return['data']);  
        // ===== END FOR EXPORT SECTION
        $tempreport .= $return['html']; 
         
    }
    
    $obj->generateReport($_POST, $tempreport, $arrTemplate,$dataToExport,$arrFilterInformation);
}

$arrStatus = $class->convertForCombobox($obj->getAllStatus(),'pkey','status');  
   
$arrTwigVar['importUrl'] = $obj->importUrl; 
$arrTwigVar['inputConsigneeCode'] =  $class->inputText('consigneeCode');  
$arrTwigVar['inputConsigneeName'] =  $class->inputText('consigneeName');   
$arrTwigVar['inputSelStatus'] =  $class->inputSelect('selStatus[]', $arrStatus, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));    
$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate;       
echo $twig->render('reportConsignee.html', $arrTwigVar);  
  
?>
