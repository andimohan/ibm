<?php
	 
include '../../_config.php';  
include '../../_include.php';
include '_global.php';

$obj= $salesOrderCarService;
$securityObject = 'reportCarMaintenanceSalesHistory'; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true)); 
$hasCOGSAccess = $security->isAdminLogin($item->cogsSecurityObject,10);  
 
$arrFilterInformation = array(); 
$detailCriteria = '';
    
$dataToExport = array();


/* data structure */
$arrTemplate = array();

$arrDataStructure = array();
$arrDataStructure['rowNumber'] = array('title'=>'#', 'align'=>'right', 'width'=>"40px", 'autoNumber' => true, "sortable" => false);
$arrDataStructure['date'] = array('title'=>ucwords($obj->lang['date']),'dbfield' => 'salesdate', 'align'=>'center','width'=>"120px",'format'=>'date', "sortable" => false);
$arrDataStructure['mileage'] = array('title'=>ucwords($obj->lang['mileage']),  'width'=>"80px", 'dbfield' => 'km',"format" => 'number', "sortable" => false);  
$arrDataStructure['itemName'] = array('title'=>ucwords($obj->lang['itemName']),  'width'=>"240px", 'dbfield' => 'itemname', "sortable" => false); 
$arrDataStructure['qty'] = array('title'=>ucwords($obj->lang['qty']),'dbfield' => 'qty','align'=>'right', 'width'=>"60px",'format'=>'number', "sortable" => false); 
$arrDataStructure['unitname'] = array('title'=>'','dbfield' => 'unitname', 'width'=>"60px", "sortable" => false,'textColor' => '999999', 'style' => 'padding-left:0px;'); 
$arrDataStructure['code'] = array('title'=>ucwords($obj->lang['reference']),  'width'=>"100px", 'dbfield' => 'salescode', "sortable" => false); 

$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $obj->lang['carMaintenanceHistoryReport']; 
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();

array_push($arrTemplate, $arrHeaderTemplate);


if (isset($_POST) && !empty($_POST['hidAction'])){  
		
	$criteria = '';
	/*if(isset($_POST) && !empty($_POST['salesCode'])) {
		$criteria .= ' AND '.$obj->tableName.'.code LIKE ('.$class->oDbCon->paramString('%'.$_POST['salesCode'].'%').')';
		array_push($arrFilterInformation,array("label" => 'Kode', 'filter' => $_POST['salesCode']));
	}*/
	/*if(isset($_POST) && !empty($_POST['trStartDate'])){
		$criteria .= ' and trdate between '.$class->oDbCon->paramDate( $_POST['trStartDate'],' / ').' AND '.$class->oDbCon->paramDate( $_POST['trEndDate'],' / '); 
		array_push($arrFilterInformation,array("label" => 'Tanggal', 'filter' => $_POST['trStartDate'] . ' - ' .$_POST['trEndDate'] ));
	}*/
     
    
	if(isset($_POST) && !empty($_POST['carNumber'])) {
		$criteria .= ' AND '.$obj->tableCar.'.policenumber = '.$class->oDbCon->paramString($_POST['carNumber']); 
		array_push($arrFilterInformation,array("label" => 'No. Polisi', 'filter' =>  $_POST['carNumber']));
	}
     	 	 
  
    $orderBy = (!empty($_POST['hidOrderBy'])) ? $obj->oDbCon->paramOrder($_POST['hidOrderBy']) : 'pkey'; // order by harus dr kolom yg terdaftar saja
    $orderType = (isset($_POST['hidOrderType']) && !empty($_POST['hidOrderType']) && $_POST['hidOrderType'] == 1) ? 'desc' : 'asc';

		   
	$order = 'order by '.$orderBy.' ' .$orderType;  
	$rs = $obj->getCarMaintenanceHistory('',$criteria);
  
    $tempreport = '';
		
    if (empty($rs)) 
        $tempreport .= '<tr class="report-row rewrite-row"><td colspan="'.count($arrHeaderTemplate['dataStructure']).'"></td></tr>';

	  for( $i=0;$i<count($rs);$i++) {  
        $return = $obj->formatReportRows(array('data' => $rs[$i]),$arrTemplate); 

        // ===== FOR EXPORT SECTION 
        array_push($dataToExport, $return['data']);  
        // ===== END FOR EXPORT SECTION

        $tempreport .= $return['html'];

    }
    
	$obj->generateReport($_POST, $tempreport, $arrTemplate,$dataToExport,$arrFilterInformation);

}
else{
   	//$_POST['trStartDate'] = date('d / m / Y');
	//$_POST['trEndDate'] = date('d / m / Y'); 
} 
    
$arrTwigVar['inputCarNumber'] =  $class->inputText('carNumber');   
//$arrTwigVar['inputHidCarKey'] = $class->inputHidden('hidCarKey');
//$arrTwigVar['inputStartDate'] = $class->inputDate('trStartDate', array('etc' => 'style="text-align:center"')); 
//$arrTwigVar['inputEndDate'] = $class->inputDate('trEndDate', array('etc' => 'style="text-align:center"')); 
$arrTwigVar['autoLoad'] =  0; 
$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate;

echo $twig->render('reportCarMaintenanceHistory.html', $arrTwigVar);  
 
?>

