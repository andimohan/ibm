<?php
	 
include '../../_config.php';  
include '../../_include-v2.php'; // kalo pake v2, ubah status ke status ap gk kebaca

include '_global.php';

$obj = $class;
$securityObject = 'reportTransactionLog'; // the value of security object is manually inserted to handle 
								  // some modules that have different security object from that of their class
								  
if(!$security->isAdminLogin($securityObject,10,true));  

$arrFilterInformation = array();

// ===== FOR EXPORT SECTION
$dataToExport = array();

/* data structure */
$arrTemplate = array();

$arrDataStructure = array();
$arrDataStructure['rowNumber'] = array('title'=>'#', 'align'=>'right', 'width'=>"40px", 'autoNumber' => true, "sortable" => false); 
$arrDataStructure['code'] = array('title'=>ucwords($obj->lang['transactionCode']),'dbfield' => 'code', 'width'=>"200px" ); 
$arrDataStructure['employee'] = array('title'=>ucwords($obj->lang['employee']),'dbfield' => 'employeename', 'width'=>"200px" );
$arrDataStructure['actionName'] = array('title'=>ucwords($obj->lang['activity']),'dbfield' => 'actionname', 'width'=>"150px" );
$arrDataStructure['description'] = array('title'=>ucwords($obj->lang['note']),'dbfield' => 'trdesc', 'width'=>"300px" ); 
$arrDataStructure['trdate'] = array('title'=>ucwords($obj->lang['actionTime']),'dbfield' => 'createdon', 'width'=>"150px", 'format' => 'datetime' );
		   
$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $obj->lang['transactionLogReport']; 
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();

array_push($arrTemplate, $arrHeaderTemplate);

if (isset($_POST) && !empty($_POST['hidAction'])){  
		
	$criteria = '';
    
    if(isset($_POST) && !empty($_POST['trStartDate'])){
		$criteria .= ' and '.$obj->transactionLog.'.createdon between '.$class->oDbCon->paramDate( $_POST['trStartDate'],' / ').' AND '.$class->oDbCon->paramDate( $_POST['trEndDate']. ' 23:59:59',' / '); 
		array_push($arrFilterInformation,array("label" => $obj->lang['date'], 'filter' => $_POST['trStartDate'] . ' - ' .$_POST['trEndDate'] ));
	}
    
    if(isset($_POST) && !empty($_POST['employeeName'])) { 
        $criteria .= ' AND '.$obj->tableEmployee.'.name LIKE ('.$class->oDbCon->paramString('%'.$_POST['employeeName'].'%').')';
	    array_push($arrFilterInformation,array("label" => $obj->lang['employee'], 'filter' => $_POST['employeeName']));
	}

  
    $orderBy = (!empty($_POST['hidOrderBy'])) ? $obj->oDbCon->paramOrder($_POST['hidOrderBy']) : 'pkey'; // order by harus dr kolom yg terdaftar saja
    $orderType = (isset($_POST['hidOrderType']) && !empty($_POST['hidOrderType']) && $_POST['hidOrderType'] == 1) ? 'desc' : 'asc';
    

    $order = 'order by '.$orderBy.' ' .$orderType; 
    
    $tempreport = ''; 
    
    $rs = $obj->generateTransactionLogReport($criteria,$order);

    // 1. cek ad berapa table DISTINCT
    $rsDetailInformation = $obj->getTransactionLogDetailReportByTableKey($rs);
    
    for($i=0;$i<count($rs);$i++) { 
        
        $tablekey = $rs[$i]['tablename'];
        $transactionkey = $rs[$i]['refkey'];
 
        // lewatin utk table2 tertentu, seperti car_turnover, movement dsb
        if(!isset( $rsDetailInformation[$tablekey])) continue;
        
        
        $rowInformation = $rsDetailInformation[$tablekey][$transactionkey] ;
        $arrStatus = $rsDetailInformation[$tablekey]['arrStatus'] ;
		
        $rs[$i]['code'] = $rowInformation['code'] ;
        $rs[$i]['actionname'] = ucwords($obj->lang[$rs[$i]['actionname']]); 
        if($rs[$i]['actionkey'] <= 10)
            $rs[$i]['actionname'] .= ': ' . $arrStatus[$rs[$i]['actionkey']]['status'];
         
        if(!empty($rs[$i]['reason'])) 
            $rs[$i]['actionname'] .= '<br><span class="text-muted"><i>'.$obj->replaceNewLine($rs[$i]['reason']).'</i></span>' ;


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
     
    
$arrTwigVar['inputHidEmployeeKey'] =  $class->inputHidden('hidEmployeeKey');
$arrTwigVar['inputEmployeeName'] =  $class->inputText('employeeName');
$arrTwigVar['inputStartDate'] = $class->inputDate('trStartDate',array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputEndDate'] = $class->inputDate('trEndDate',array('etc' => 'style="text-align:center"')); 
$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate;    

echo $twig->render('reportTransactionLog.html', $arrTwigVar);  

?>