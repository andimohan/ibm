<?php
	 
include '../../_config.php';  
include '../../_include-v2.php';

includeClass('APILog.class.php');
$apiLog = createObjAndAddToCol( new APILog()); 

include '_global.php';

$obj= $apiLog;
$securityObject = 'reportServices'; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true));   

$arrFilterInformation = array();     
 

// ===== FOR EXPORT SECTION
$dataToExport = array();

/* data structure */
$arrTemplate = array();

$arrDataStructure = array();

$arrDataStructure['rowNumber'] = array('title'=>'#', 'align'=>'right', 'width'=>"40px", 'autoNumber' => true, "sortable" => false);
$arrDataStructure['ip'] = array('title'=>'IP','dbfield' => 'ip', 'width'=>"120px");
$arrDataStructure['actiontype'] = array('title'=>'Action','dbfield' => 'action', 'width'=>"120px");
$arrDataStructure['endpoint'] = array('title'=>'Endpoint','dbfield' => 'endpoint', 'width'=>"150px");
$arrDataStructure['payload'] = array('title'=>'Payload','dbfield' => 'payload', 'width'=>"500px");            
$arrDataStructure['responsecode'] = array('title'=>'Response Code','dbfield' => 'responsecode','align' =>'center','width'=>"130px");            
$arrDataStructure['responsemsg'] = array('title'=>'Response Message','dbfield' => 'responsemsg', 'width'=>"800px");            

$arrHeaderTemplate = array();  
$arrHeaderTemplate['reportTitle'] = $obj->lang['apiLogReport']; 
$arrHeaderTemplate['dataStructure'] = $arrDataStructure; 
$arrHeaderTemplate['total'] = array();
 
array_push($arrTemplate, $arrHeaderTemplate);

// ===== END FOR EXPORT SECTION

if (isset($_POST) && !empty($_POST['hidAction'])){  
		
	$criteria = '';
 	if(isset($_POST) && !empty($_POST['trStartDate'])){
		$criteria .= ' and createdon between '.$class->oDbCon->paramDate( $_POST['trStartDate'],' / ').' AND '.$class->oDbCon->paramDate( $_POST['trEndDate'],' / ','Y-m-d 23:59'); 
		array_push($arrFilterInformation,array("label" => 'Tanggal', 'filter' => $_POST['trStartDate'] . ' - ' .$_POST['trEndDate'] ));
	}
	
    
    if(isset($_POST) && !empty($_POST['selAction'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selAction']));    
        $criteria .= ' AND '.$obj->tableName.'.action in('.$key.')';   
	    array_push($arrFilterInformation,array("label" => 'Action', 'filter' => $key));
        
	} 
    
    if(isset($_POST) && !empty($_POST['responseCode'])) {
		$criteria .= ' AND '.$obj->tableName.'.responsecode = ('.$class->oDbCon->paramString($_POST['responseCode']).')';
		array_push($arrFilterInformation,array("label" => 'Response Code', 'filter' => $_POST['actionType']));
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
}else{ 
	$_POST['trStartDate'] = date('d / m / Y');
	$_POST['trEndDate'] = date('d / m / Y');
}


$arrAction = array(
	'POST' => 'POST',
	'PUT' => 'PUT',
	'GET' => 'GET'
);
    
$arrTwigVar['inputStartDate'] = $class->inputDate('trStartDate', array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputEndDate'] = $class->inputDate('trEndDate', array('etc' => 'style="text-align:center"'));  
$arrTwigVar['inputActionType'] =  $class->inputText('actionType');  
$arrTwigVar['inputResponseCode'] =  $class->inputText('responseCode');   
$arrTwigVar['inputSelAction'] =  $class->inputSelect('selAction[]', $arrAction, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox')); 

$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate;   
      
echo $twig->render('reportAPILog.html', $arrTwigVar);  
 
?>