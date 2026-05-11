<?php
include '../../_config.php';
include '../../_include-v2.php';

includeClass( array('TruckingServiceOrder.class.php','TruckingServiceWorkOrder.class.php','TruckingServiceOrderInvoice.class.php')); 

$arrInterval = array(
 'd' => $class->lang['day'],
 'h' => $class->lang['hour'],
);

$arrModule = array(
 'jobOrder' => $class->lang['jobOrder'],
 'truckingServiceWorkOrder' => $class->lang['truckingServiceWorkOrder'],
 'truckingServiceOrderInvoice' => $class->lang['salesInvoice'],
);

if(isset($_POST['selModule'])){ 
    switch($_POST['selModule']){
        case 'jobOrder' :  $obj = createObjAndAddToCol(new TruckingServiceOrder());
                         break;
        case 'truckingServiceWorkOrder' :  $obj = createObjAndAddToCol(new TruckingServiceWorkOrder());
                         break;
        case 'truckingServiceOrderInvoice' :  $obj = createObjAndAddToCol(new TruckingServiceOrderInvoice());
                         break;
    }
}else{
   $obj = createObjAndAddToCol(new TruckingServiceOrder());
}


include '_global.php';
 
$securityObject = 'reportTimespan'; // the value of security object is manually inserted to handle 
								  // some modules that have different security object from that of their class

 
if(!$security->isAdminLogin($securityObject,10,true)); 
 
$arrFilterInformation = array();
 
$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $class->lang['timespanReport'];

// ====================== must be set before TWIG
if (!isset($_POST['trStartDate']) || empty($_POST['trStartDate'])){ 
	$_POST['trStartDate'] = date('d / m / Y');
	$_POST['trEndDate'] = date('d / m / Y');
}   

if (isset($_POST) && !empty($_POST['hidAction'])){ 
   
    $criteria = '';
    	
	if(isset($_POST) && !empty($_POST['selModule'])){  
        array_push($arrFilterInformation,array("label" => $obj->lang['module'], 'filter' => $arrModule[$_POST['selModule']]));
	}
     
	if(isset($_POST) && !empty($_POST['trStartDate'])){
		$criteria .= ' AND '.$obj->tableName.'.trdate between '.$class->oDbCon->paramDate( $_POST['trStartDate'],' / ').' AND '.$class->oDbCon->paramDate( $_POST['trEndDate'],' / ','Y-m-d 23:59'); 
		array_push($arrFilterInformation,array("label" => $obj->lang['period'], 'filter' => $_POST['trStartDate'] . ' - ' .$_POST['trEndDate'] ));
	}
    
    if(isset($_POST) && !empty($_POST['transCode'])) {
		$criteria .= ' AND '.$obj->tableName.'.code LIKE ('.$class->oDbCon->paramString('%'.$_POST['transCode'].'%').')';
		array_push($arrFilterInformation,array("label" => $obj->lang['transactionCode'], 'filter' => $_POST['transCode']));
	}
    
     
    $dateInterval = 'h';
     
	if(isset($_POST) && !empty($_POST['selInterval'])){
		//$criteria .= ' AND '.$obj->tableName.'.trdate between '.$class->oDbCon->paramDate( $_POST['trStartDate'],' / ').' AND '.$class->oDbCon->paramDate( $_POST['trEndDate'],' / ','Y-m-d 23:59'); 
		$dateInterval = $_POST['selInterval'];
         
        array_push($arrFilterInformation,array("label" => $obj->lang['interval'], 'filter' => $arrInterval[$_POST['selInterval']]));
	}
     
    //$criteria .= ' and code in (\'11-21070003\',\'20-2101000001\') ' ;
    
    $rsStatus = $obj->getAllStatus(); 
   
    // ==== hitung total waktu 
    $result = $obj->calculateDateDiffPerStatus($criteria,array('interval' => $dateInterval));
    
    $totalPerStatus = $result['totalPerStatus'];
    $rsHistory = $result['transactionRows'];
     
    $tempreport = '<table>';
    $tempreport .= '<thead><tr class="table-header"><th style="width: 120px">'.$obj->lang['code'].'</th><th style="text-align:center;">'.$obj->lang['date'].'</th>';
    
    $totalStatus = count($rsStatus) -2;
    for($i=0;$i<$totalStatus ;$i++){ 
        $statuskey = $rsStatus[$i]['pkey']; 
        $avgDaysLabel = $totalPerStatus[$statuskey]['label'];
        
        $tempreport .= '<th style="width: 100px; text-align:center">'.$rsStatus[$i]['status'].'<div style=" font-size:0.9em;">'.$avgDaysLabel.'</div></th>';
    }
    
    $tempreport .= '</tr></thead>';
  
    foreach($rsHistory as $key=>$row){
        
        $tempreport .= '<tr><td>'.$row['code'].'</td><td>'.$obj->formatDBDate($row['trdate']).'</td>';
        
        for($i=0;$i<$totalStatus;$i++) { 
            $diff = (isset($row['statusTimeline'][$rsStatus[$i]['pkey']])) ? $row['statusTimeline'][$rsStatus[$i]['pkey']] : 0;
            $tempreport .= '<td style="text-align:center">'.$obj->formatNumber($diff,2).'</td>';
        }
        
        $tempreport .= '</tr>'; 
        $tempreport .= '<tr class="detail-row rewrite-row"><td colspan="'.($totalStatus+2).'"></td></tr>'; 
    }
    
    $tempreport .= '</table>'; 
     
	$reportResult = array(); 
    $reportResult['filterInformation'] = $arrFilterInformation;  
 	$reportResult['content'] = $tempreport;
     	 
    if ((isset($_POST['hidExportExcel']) && $_POST['hidExportExcel'] == 1)){  
        $arrTemplate = array();
        $arrTemplate[0]['dataToExport'] = array();
        $arrTemplate[0]['filterInformation'] = $arrFilterInformation;
         
        //exportToExcel($arrHeaderTemplate['reportTitle'],$arrTemplate, $arrContent);  
    }else{ 
        echo json_encode($reportResult);
        die;
    }
    
}else{ 
	$_POST['trEndDate'] = date('d / m / Y'); 
}
  
//$_POST['trEndDate'] = '30 / 09 / 2017';
 

$arrTwigVar['inputTransCode'] =  $class->inputText('transCode');  
$arrTwigVar['inputStartDate'] = $class->inputDate('trStartDate',array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputEndDate'] = $class->inputDate('trEndDate',array('etc' => 'style="text-align:center"'));  
$arrTwigVar['inputSelInterval'] =  $class->inputSelect('selInterval', $arrInterval); 
$arrTwigVar['inputSelModule'] =  $class->inputSelect('selModule', $arrModule); 
$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate;  


echo $twig->render('reportTimespan.html', $arrTwigVar);   

?>