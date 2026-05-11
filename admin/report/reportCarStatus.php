<?php
	 
include '../../_config.php';  
include '../../_include.php';
include '_global.php';

$obj= $truckingServiceWorkOrder;
$securityObject = 'reportSupplier'; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true)); 
 
$hasAPAccess = $security->isAdminLogin($ap->securityObject,10);  

$arrFilterInformation = array();    
	
if (isset($_POST) && !empty($_POST['hidAction'])){  
		
	$criteria = '';
	
    if(isset($_POST) && !empty($_POST['carNumber'])) {
		$criteria .= ' AND '.$obj->tableCar.'.policenumber LIKE  ('.$class->oDbCon->paramString('%'.$_POST['carNumber'].'%').')'; 
		array_push($arrFilterInformation,array("label" => 'No. Polisi', 'filter' =>  $_POST['carNumber']));
	} 
    
    if(isset($_POST) && !empty($_POST['workOrderCode'])) {
		$criteria .= ' AND '.$obj->tableName.'.code LIKE ('.$class->oDbCon->paramString('%'.$_POST['workOrderCode'].'%').')';
		array_push($arrFilterInformation,array("label" => 'Kode', 'filter' => $_POST['workOrderCode']));
	}
    
	if(isset($_POST) && !empty($_POST['driverName'])) {
		$criteria .= ' AND '.$obj->tableEmployee.'.name LIKE  ('.$class->oDbCon->paramString('%'.$_POST['driverName'].'%').')'; 
		array_push($arrFilterInformation,array("label" => 'Nama', 'filter' =>  $_POST['driverName']));
	}
    
    if(isset($_POST) && !empty($_POST['trStartDateTime'])){
		$criteria .= ' and '.$obj->tableName.'.trdate between '.$class->oDbCon->paramDate( $_POST['trStartDateTime'],' / ').' AND '.$class->oDbCon->paramDate( $_POST['trEndDateTime'] . ' 23:59:00',' / '); 
		array_push($arrFilterInformation,array("label" => 'Tanggal SPK', 'filter' => $_POST['trStartDateTime'] . ' - ' .$_POST['trEndDateTime'] ));
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
     
		$temp = 1;
		$tempreport = ''; 
    
		if (empty($rs)){
			$tempreport .= '<tr class="report-row rewrite-row"><td colspan="8"></td></tr>';	
		}
		for( $i=0;$i<count($rs);$i++) {    
			 
			$temptablerow = ''; 
	    
	        
            
	     	$temptablerow  .= '<tr class="rewrite-row"> ';
			$temptablerow  .= '<td style="text-align:right;">'.$temp.'.</td>';  
			$temptablerow  .= '<td>'.$rs[$i]['policenumber'].'</td>'; 
			$temptablerow  .= '<td>'.$rs[$i]['code'].'</td>'; 
			$temptablerow  .= '<td style="text-align:center;">'.$obj->formatDBDate($rs[$i]['trdate'],'d / m / Y').'</td>';
			$temptablerow  .= '<td>'.$rs[$i]['drivername'].'</td>'; 
			$temptablerow  .= '<td>'.$rs[$i]['statusname'].'</td>';
			$temptablerow  .= '</tr>';
			$temptablerow  .= '<tr class="detail-row rewrite-row">';
			$temptablerow  .= '<td colspan="8"">';
			$temptablerow  .= '';
			$temptablerow  .= '</td>';
			$temptablerow  .= '</tr>';
			
			
			$tempreport .= $temptablerow; 
			  
			$temp++; 
		}
		 
	
	$reportResult = array(); 
	 
	$reportResult['filterInformation'] = $arrFilterInformation;  
 	$reportResult['content'] = $tempreport;  
 	echo json_encode($reportResult);
	die;
}

$arrStatus = $class->convertForCombobox($obj->getAllStatus(),'pkey','status');  
   
 


$arrTwigVar['inputCarNumber'] =  $class->inputText('carNumber'); 
$arrTwigVar['inputHidCarKey'] =  $class->inputHidden('hidCarKey'); 
$arrTwigVar['inputWorkOrderCode'] =  $class->inputText('workOrderCode');
$arrTwigVar['inputHidDriverKey'] =  $class->inputHidden('driverKey');
$arrTwigVar['inputDriverName'] =  $class->inputText('driverName');
$arrTwigVar['inputStartDateTime'] = $class->inputDate('trStartDateTime');
$arrTwigVar['inputEndDateTime'] = $class->inputDate('trEndDateTime'); 
$arrTwigVar['inputSelStatus'] =  $class->inputSelect('selStatus[]', $arrStatus, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox')); 
      
echo $twig->render('reportCarStatus.html', $arrTwigVar);  
 
?>

