<?php
	 
include '../../_config.php';  
include '../../_include-v2.php';

includeClass('LoginLog.class.php');
$loginLog = createObjAndAddToCol(new LoginLog());

include '_global.php';

$obj = $loginLog;
$securityObject = 'reportLoginLog'; // the value of security object is manually inserted to handle 
								  // some modules that have different security object from that of their class
								  
if(!$security->isAdminLogin($securityObject,10,true));  

$arrFilterInformation = array();

// ===== FOR EXPORT SECTION
$dataToExport = array();

/* data structure */
$arrTemplate = array();

$arrDataStructure = array();
$arrDataStructure['rowNumber'] = array('title'=>'#', 'align'=>'right', 'width'=>"40px", 'autoNumber' => true, "sortable" => false);
$arrDataStructure['username'] = array('title'=>ucwords($obj->lang['username']),'dbfield' => 'username', 'width'=>"150px" );
$arrDataStructure['employee'] = array('title'=>ucwords($obj->lang['employee']),'dbfield' => 'employeename', 'width'=>"150px" );
$arrDataStructure['ipaddress'] = array('title'=>ucwords($obj->lang['ipaddress']),'dbfield' => 'ipaddress', 'width'=>"150px" );
$arrDataStructure['createdon'] = array('title'=>ucwords($obj->lang['date']),'dbfield' => 'createdon', 'width'=>"150px" ,'format'=>'datetime');
$arrDataStructure['statusname'] = array('title'=>ucwords($obj->lang['status']),'dbfield' => 'statusname', 'width'=>"150px" );
		   
$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $obj->lang['loginLogReport']; 
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();

array_push($arrTemplate, $arrHeaderTemplate);
    
if (isset($_POST) && !empty($_POST['hidAction'])){  
		
	$criteria = '';
     
    if(isset($_POST) && !empty($_POST['trStartDate'])){
		$criteria .= ' and '.$obj->tableName.'.createdon between '.$class->oDbCon->paramDate( $_POST['trStartDate'],' / ').' AND '.$class->oDbCon->paramDate( $_POST['trEndDate'] . ' 23:59:59',' / '); 
		array_push($arrFilterInformation,array("label" => $class->lang['date'], 'filter' => $_POST['trStartDate'] . ' - ' .$_POST['trEndDate'] ));
	}
     
	if(isset($_POST) && !empty($_POST['employeeName'])) {
		$criteria .= ' AND '.$obj->tableEmployee.'.name LIKE ('.$class->oDbCon->paramString('%'.$_POST['employeeName'].'%').')';
	 	array_push($arrFilterInformation,array("label" => $class->lang['employee'] , 'filter' =>  $_POST['employeeName']));
	} 
	 
    
    if(isset($_POST) && !empty($_POST['username'])) { 
        $criteria .= ' AND '.$obj->tableName.'.username LIKE ('.$class->oDbCon->paramString('%'.$_POST['username'].'%').')';
	    array_push($arrFilterInformation,array("label" => $class->lang['username'], 'filter' => $_POST['username']));
	}
    
    
    if(isset($_POST) && !empty($_POST['selStatus'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selStatus']));   
        
       	$criteria .= ' AND '.$obj->tableName.'.statuskey in('.$key.')';  

        $rsCriteria =  $obj->getStatusById ($key);
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['status']);
			
		$statusName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => $class->lang['status'], 'filter' => $statusName));
        
	}  
		         
  
    $orderBy = (!empty($_POST['hidOrderBy'])) ? $obj->oDbCon->paramOrder($_POST['hidOrderBy']) : 'pkey'; // order by harus dr kolom yg terdaftar saja
    $orderType = (isset($_POST['hidOrderType']) && !empty($_POST['hidOrderType']) && $_POST['hidOrderType'] == 1) ? 'desc' : 'asc';


    $order = 'order by '.$orderBy.' ' .$orderType; 
    $rs = $obj->searchData('','',true,$criteria,$order);
    $tempreport = ''; 

    for($i=0;$i<count($rs);$i++) { 
 
        $arrHeaderStyle = array(); 
        if ($rs[$i]['statuskey'] == 2){  
            foreach($arrDataStructure as $key=>$row) 
                $arrHeaderStyle[$key]['textColor'] = 'C41E3A';    
        }


        $return = $obj->formatReportRows(array('data' => $rs[$i], 'style' => $arrHeaderStyle),$arrTemplate); 

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
//$arrWarehouse = $class->convertForCombobox($warehouse->searchData($warehouse->tableName.'.statuskey',1,true,'','order by name asc'),'pkey','name');
 
$arrTwigVar['inputEmployeeName'] =  $class->inputText('employeeName');  
$arrTwigVar['inputUsername'] =  $class->inputText('username');
$arrTwigVar['inputStartDate'] = $class->inputDate('trStartDate',array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputEndDate'] = $class->inputDate('trEndDate',array('etc' => 'style="text-align:center"')); 
//$arrTwigVar['inputSelWarehouse'] =  $class->inputSelect('selWarehouse[]', $arrWarehouse, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));  
$arrTwigVar['inputSelStatus'] =  $class->inputSelect('selStatus[]', $arrStatus, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox')); 
      
$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate;    

echo $twig->render('reportLoginLog.html', $arrTwigVar);  
 
?>