<?php
	 
include '../../_config.php';  
include '../../_include-v2.php'; 

includeClass(array('Service.class.php','EMKLJobOrder.class.php'));
$service = createObjAndAddToCol( new Service(SERVICE));  
$emklJobOrder = new EMKLJobOrder();
$chartOfAccount = new ChartOfAccount();

include '_global.php';

$obj= $service;
$securityObject = 'ChartOfAccount'; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true));
 
//$arrStatus = $obj->getAllStatus();
$arrFilterInformation = array();     
 
// ===== FOR EXPORT SECTION
$dataToExport = array();

/* data structure */
$arrTemplate = array();

$arrDataStructure = array();
//$_POST['module'] = IMPORT_TEMPLATE['voucher'];

$arrJobType = array();
$arrJobType[1] = 'Import';
$arrJobType[2] = 'Export';

$arrCostType = array(
					'1' => $obj->lang['revenueAccount'],
					'2' => $obj->lang['prepaidCost'], 
					'3' => $obj->lang['costAccount'], 
					'4' => $obj->lang['ARAPReimburse'], 
				);

$rsContainer = $emklJobOrder->getLoadContainer();
  
$arrDataStructure['rowNumber'] = array('title'=>'#', 'align'=>'right', 'width'=>"40px", 'autoNumber' => true, "sortable" => false);
$arrDataStructure['code'] = array('title'=>ucwords($obj->lang['code']),'dbfield' => 'code', 'width'=>"100px");
$arrDataStructure['name'] = array('title'=>ucwords($obj->lang['name']),'dbfield' => 'name', 'width'=>"300px");

foreach($arrJobType as $jobTypeKey=>$jobTypeRow){  
	foreach($rsContainer as $containerRow){ 
		foreach($arrCostType as $costTypeKey=>$costTypeRow){
			$index = $jobTypeKey.'-'.$containerRow['pkey'].'-'.$costTypeKey;
			$arrDataStructure[$index] = array('title'=>ucwords( $containerRow['name'] . ' (' .$jobTypeRow.' - '.$costTypeRow.')'),'dbfield' => $index, 'width'=>"300px");
		} 
	}
}

$arrHeaderTemplate = array();  
$arrHeaderTemplate['reportTitle'] = $obj->lang['servicesCOALink']; 
$arrHeaderTemplate['dataStructure'] = $arrDataStructure; 

array_push($arrTemplate, $arrHeaderTemplate);

// ===== END FOR EXPORT SECTION

if (isset($_POST) && !empty($_POST['hidAction'])){  
	$criteria = '';
	
    $orderBy = (!empty($_POST['hidOrderBy'])) ? $obj->oDbCon->paramOrder($_POST['hidOrderBy']) : 'pkey'; // order by harus dr kolom yg terdaftar saja
    $orderType = (isset($_POST['hidOrderType']) && !empty($_POST['hidOrderType']) && $_POST['hidOrderType'] == 1) ? 'desc' : 'asc';
  
	$order = 'order by '.$orderBy.' ' .$orderType; 
      
	$rs = $obj->searchData('','',true,$criteria,$order);
     
    $tempreport = ''; 
    
    // ============================= GENERATE DATA ============================= 
 
    for( $i=0;$i<count($rs);$i++) {      
         
		$rsCostCOAKey = $obj->getCostCOADetail($rs[$i]['pkey']);
		
		if(!empty($rsCostCOAKey)){
			$costCOAByType = $obj->reindexDetailCollections($rsCostCOAKey,'eximkey'); 
			$arrTempCOA = $chartOfAccount->searchDataRow(array('pkey','code','name'),' and '.$chartOfAccount->tableName.'.pkey in ('.$obj->oDbCon->paramString(array_column($rsCostCOAKey,'coakey'),',').') ');
			$arrTempCOA = array_column($arrTempCOA,null,'pkey');
			
			
			foreach($arrJobType as $jobTypeKey=>$jobTypeRow){ 
				
				foreach($rsContainer as $containerRow){ 
					$containerkey = $containerRow['pkey']; 
					
					foreach($arrCostType as $costTypeKey => $typeRow){
						
						$COAIndexKey = $containerkey.'-'.$costTypeKey; 
						
						$arrCostCOAKey = array(); 
						$arrCostCOAKey = $costCOAByType[$jobTypeKey];
						$arrCostCOAKey = array_column($arrCostCOAKey,null,'categoryandtypekey');

						$coaName = (!empty($arrTempCOA[$arrCostCOAKey[$COAIndexKey]['coakey']])) ? $arrTempCOA[$arrCostCOAKey[$COAIndexKey]['coakey']]['code'] .' - '. $arrTempCOA[$arrCostCOAKey[$COAIndexKey]['coakey']]['name'] : '' ;

						
						$index = $jobTypeKey.'-'.$containerRow['pkey'].'-'.$costTypeKey;
						
						$rs[$i][$index] = $coaName;

					}
				} 
			}
		} 
		
        $return = $obj->formatReportRows(array('data' => $rs[$i]),$arrTemplate);
        
        // ===== FOR EXPORT SECTION 
        array_push($dataToExport, $return['data']); 
        // ===== END FOR EXPORT SECTION
        
        $tempreport .= $return['html']; 
         
    }
		 
    $obj->generateReport($_POST, $tempreport, $arrTemplate,$dataToExport,$arrFilterInformation);
}else{
   	$_POST['trStartDate'] = date('d / m / Y');
	$_POST['trEndDate'] = date('d / m / Y'); 
}

$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate;   
      
echo $twig->render('reportServicesCoaLink.html', $arrTwigVar);  
 
?>