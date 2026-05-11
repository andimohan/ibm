<?php
	 
include '../../_config.php';  
include '../../_include-v2.php';

includeClass('Marketplace.class.php'); 
$marketplace =  new Marketplace() ;

include '_global.php';

$obj = $marketplace;
$securityObject = 'reportMarketplaceLog'; // the value of security object is manually inserted to handle 
								  // some modules that have different security object from that of their class
								  
if(!$security->isAdminLogin($securityObject,10,true));  

$arrFilterInformation = array();

// ===== FOR EXPORT SECTION
$dataToExport = array();

/* data structure */
$arrTemplate = array();

$arrDataStructure = array();
$arrDataStructure['rowNumber'] = array('title'=>'#', 'align'=>'right', 'width'=>"40px", 'autoNumber' => true, "sortable" => false); 
$arrDataStructure['marketplace'] = array('title'=>ucwords($obj->lang['marketplace']),'dbfield' => 'marketplacename', 'width'=>"100px" );
$arrDataStructure['trdate'] = array('title'=>ucwords($obj->lang['actionTime']),'dbfield' => 'createdon', 'width'=>"150px", 'format' => 'datetime' );
$arrDataStructure['actionName'] = array('title'=>ucwords($obj->lang['action']),'dbfield' => 'actionname', 'width'=>"150px" );
$arrDataStructure['transaction'] = array('title'=>ucwords($obj->lang['reference']),'dbfield' => 'ref', 'width'=>"300px" );
$arrDataStructure['resultState'] = array('title'=>ucwords($obj->lang['status']),'dbfield' => 'resultstate', 'width'=>"100px" );
$arrDataStructure['message'] = array('title'=>ucwords($obj->lang['note']),'dbfield' => 'message', 'width'=>"1000px" ); 
		   
$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $obj->lang['marketplaceLogReport']; 
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();

$arrStatus = array();
$arrStatus[-1]= $class->lang['selectAll'];
$arrStatus[0]=  $class->lang['failed'];
$arrStatus[1]=  $class->lang['success'];

array_push($arrTemplate, $arrHeaderTemplate);

if (isset($_POST) && !empty($_POST['hidAction'])){  
		
	$criteria = '';
    
    if(isset($_POST) && !empty($_POST['trStartDate'])){
		$criteria .= ' and '.$obj->tableMarketplaceLog.'.createdon between '.$class->oDbCon->paramDate( $_POST['trStartDate'],' / ').' AND '.$class->oDbCon->paramDate( $_POST['trEndDate']. ' 23:59:59',' / '); 
		array_push($arrFilterInformation,array("label" => $obj->lang['date'], 'filter' => $_POST['trStartDate'] . ' - ' .$_POST['trEndDate'] ));
	}
	
	    
	if(isset($_POST) && !empty($_POST['reference'])) {
		$criteria .= ' AND '.$obj->tableMarketplaceLog.'.ref LIKE  ('.$class->oDbCon->paramString('%'.$_POST['reference'].'%').')'; 
		array_push($arrFilterInformation,array("label" => 'Referensi', 'filter' =>  $_POST['reference']));
	} 
    
    if(isset($_POST) && !empty($_POST['selMarketplace'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selMarketplace']));   
        
       	$criteria .= ' AND '.$obj->tableMarketplaceLog.'.marketplacekey in('.$key.')';  

        $rsCriteria = $marketplace->searchData('','',true, ' and '.$marketplace->tableName.'.pkey in ('.$key.')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$marketplaceName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => $class->lang['marketplace'] , 'filter' => $marketplaceName));
        
	}
     
    if(isset($_POST) && !empty($_POST['selMarketplaceAction'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selMarketplaceAction']));   
        
       	$criteria .= ' AND '.$obj->tableMarketplaceLog.'.actionkey in('.$key.')';  

        $rsCriteria = $marketplace->getActionType($_POST['selMarketplaceAction']);
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$marketplaceAction = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => $class->lang['action'] , 'filter' => $marketplaceAction));
        
	}
      
    
    if(isset($_POST) && $_POST['selStatus'] <> -1) {   
        $key =  $_POST['selStatus'];
        $criteria .= ' AND '.$obj->tableMarketplaceLog.'.issuccess = '. $class->oDbCon->paramString($key);  
        array_push($arrFilterInformation,array("label" => $class->lang['status'] , 'filter' => $arrStatus[$key]));    
	}
    
  
    $orderBy = (!empty($_POST['hidOrderBy'])) ? $obj->oDbCon->paramOrder($_POST['hidOrderBy']) : 'pkey'; // order by harus dr kolom yg terdaftar saja
    $orderType = (isset($_POST['hidOrderType']) && !empty($_POST['hidOrderType']) && $_POST['hidOrderType'] == 1) ? 'desc' : 'asc';
    

    $order = 'order by '.$orderBy.' ' .$orderType; 
    
    $tempreport = ''; 
    
    $rs = $obj->generateMarketplaceLog($criteria,$order);
   
    for($i=0;$i<count($rs);$i++) { 

        $arrHeaderStyle = array();  
        if ($rs[$i]['issuccess'])  
            $arrHeaderStyle['resultstate']['textColor'] = '568203'; 
        else 
            $arrHeaderStyle['resultstate']['textColor'] = 'C41E3A'; 
        
        
        $return = $obj->formatReportRows(array('data' => $rs[$i], 'style' => $arrHeaderStyle ),$arrTemplate); 

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

$arrMarketplace = $class->convertForCombobox($marketplace->searchData($marketplace->tableName.'.statuskey',1,true,'','order by name asc'),'pkey','name');
$arrMarketplaceAction = $class->convertForCombobox($marketplace->getActionType(),'pkey','name');
  
$arrTwigVar['inputReference'] =  $class->inputText('reference');   
$arrTwigVar['inputSelMarketplace'] =  $class->inputSelect('selMarketplace[]', $arrMarketplace, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox')); 
$arrTwigVar['inputSelStatus'] =  $class->inputSelect('selStatus', $arrStatus); 
$arrTwigVar['inputSelMarketplaceAction'] =  $class->inputSelect('selMarketplaceAction[]', $arrMarketplaceAction, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox')); 
$arrTwigVar['inputStartDate'] = $class->inputDate('trStartDate',array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputEndDate'] = $class->inputDate('trEndDate',array('etc' => 'style="text-align:center"')); 
$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate;    

echo $twig->render('reportMarketplaceLog.html', $arrTwigVar);  
 
?>