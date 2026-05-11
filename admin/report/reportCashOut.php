<?php
include '../../_config.php';
include '../../_include-v2.php'; 

includeClass('CashOut.class.php');
$cashOut= createObjAndAddToCol( new CashOut()); 
include '_global.php';

$obj = $cashOut;
$securityObject = 'reportCashOut'; // the value of security object is manually inserted to handle 
								  // some modules that have different security object from that of their class
								  
if(!$security->isAdminLogin($securityObject,10,true)); 

$_POST['selStatus[]'] = array(2,3);

$arrFilterInformation = array();
$detailCriteria = ''; 
// ===== FOR EXPORT SECTION
$dataToExport = array();

/* data structure */
$arrTemplate = array();

$arrDataStructure = array();
$_POST['module'] = IMPORT_TEMPLATE['cashOut'];

switch($EXPORT_TYPE){
    case 2 :
            $arrDataStructure['code'] = array('title'=>ucwords($obj->lang['code']),  'width'=>"150px", 'dbfield' => 'code');
			$arrDataStructure['date'] = array('title'=>ucwords($obj->lang['date']),'dbfield' => 'trdate', 'width'=>"100px",'format'=>'date');
			$arrDataStructure['paidTo'] = array('title'=>ucwords($obj->lang['paidTo']),'dbfield' => 'recipientname', 'width'=>"200px");
			$arrDataStructure['coalink'] = array('title'=>ucwords($obj->lang['account']),'dbfield' => 'codename', 'width'=>"200px");
			$arrDataStructure['note'] = array('title'=>ucwords($obj->lang['note']),'dbfield' => 'trdesc', 'width'=>"350px");
			$arrDataStructure['cost'] = array('title'=>ucwords($obj->lang['cost']),'dbfield' => 'costname', 'width'=>"150px");
			$arrDataStructure['description'] = array('title'=>ucwords($obj->lang['detailsNote']),'dbfield' => 'description', 'width'=>"250px");
			$arrDataStructure['amount'] = array('title'=>ucwords($obj->lang['amount']),  'dbfield' => 'qty', 'width'=>"60px", 'format' => 'number' ); 

            break;
        
    default :
            $arrDataStructure['rowNumber'] = array('title'=>'#', 'align'=>'right', 'width'=>"40px", 'autoNumber' => true, "sortable" => false);
			$arrDataStructure['code'] = array('title'=>ucwords($obj->lang['code']),  'width'=>"150px", 'dbfield' => 'code');
			$arrDataStructure['coalink'] = array('title'=>ucwords($obj->lang['account']),'dbfield' => 'codename', 'width'=>"200px");
			$arrDataStructure['paidTo'] = array('title'=>ucwords($obj->lang['paidTo']),'dbfield' => 'recipientname', 'width'=>"200px");
			$arrDataStructure['date'] = array('title'=>ucwords($obj->lang['date']),'dbfield' => 'trdate', 'width'=>"100px",'format'=>'date');
			$arrDataStructure['note'] = array('title'=>ucwords($obj->lang['note']),'dbfield' => 'trdesc', 'width'=>"350px");
			$arrDataStructure['total'] = array('title'=>ucwords($obj->lang['total']),'dbfield' => 'grandtotal', 'width'=>"120px" ,'format'=>'number', 'calculateTotal' => true);
			$arrDataStructure['status'] = array('title'=>ucwords($obj->lang['status']),'dbfield' => 'statusname', 'width'=>"100px");
}

		  
$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $obj->lang['cashOutReport'];
$arrHeaderTemplate['reportWidth'] = "1000px;";
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();

array_push($arrTemplate, $arrHeaderTemplate);
if ($EXPORT_TYPE != 2){

$arrDataDetailStructure = array(); 

if($obj->useMasterCost)
    $arrDataDetailStructure['cost'] = array('title'=>ucwords($obj->lang['cost']),  'dbfield' => 'costname','width'=>"200px", 'mergeExcelCell' => 1);

$arrDataDetailStructure['revenueAccount'] = array('title'=>ucwords($obj->lang['cost']),  'dbfield' => 'coaname','width'=>"250px", 'mergeExcelCell' => 2);  
$arrDataDetailStructure['amount'] = array('title'=>ucwords($obj->lang['amount']),  'dbfield' => 'amount', 'width'=>"100px", 'format' => 'number'); 
$arrDataDetailStructure['trDesc'] = array('title'=>ucwords($obj->lang['note']), 'dbfield' => 'trdesc', 'mergeExcelCell' => 3); 

$arrDetailTemplate = array();
$arrDetailTemplate['reportWidth'] = "900px";
$arrDetailTemplate['dataStructure'] = $arrDataDetailStructure;
$arrDetailTemplate['total'] = array();
}

array_push($arrTemplate, $arrDetailTemplate);
if (isset($_POST) && !empty($_POST['hidAction'])){
	
	$criteria = '';

	if(isset($_POST) && !empty($_POST['cashOutCode'])) {
		$criteria .= ' AND '.$obj->tableName.'.code LIKE ('.$class->oDbCon->paramString('%'.$_POST['cashOutCode'].'%').')';
		array_push($arrFilterInformation,array("label" => 'Kode', 'filter' => $_POST['cashOutCode']));
	}
	
	if(isset($_POST) && !empty($_POST['trStartDate'])){
		$criteria .= ' and trdate between '.$class->oDbCon->paramDate( $_POST['trStartDate'],' / ').' AND '.$class->oDbCon->paramDate( $_POST['trEndDate'],' / '); 
		array_push($arrFilterInformation,array("label" => 'Tanggal', 'filter' => $_POST['trStartDate'] . ' - ' .$_POST['trEndDate'] ));
	}
	
	if(isset($_POST) && !empty($_POST['coaName'])) { 
        $criteria .= ' AND ( concat('.$obj->tableCOA.'.code," - ", '.$obj->tableCOA.'.name) LIKE '.$class->oDbCon->paramString('%'.$_POST['coaName'].'%').' )';
	    array_push($arrFilterInformation,array("label" => 'COA', 'filter' => $_POST['coaName']));
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
	 
	$rs = ($EXPORT_TYPE != 2) ? $obj->searchData('','',true,$criteria,$order) : array();
	
	$tempreport = ''; 
	// ============================= GENERATE DATA ============================= 
    
    
        for( $i=0;$i<count($rs);$i++) {   
		
			$rsDetail = $obj->getDetailWithRelatedInformation($rs[$i]['pkey'],$detailCriteria); 
            if (empty($rsDetail))
                continue;
            
            // has detail
            $rs[$i]['_detail_'] = array('arrTemplate'=>$arrDetailTemplate,'data' => $rsDetail);
                  
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
else{ 
	$_POST['trStartDate'] = date('d / m / Y');
	$_POST['trEndDate'] = date('d / m / Y');
}  

$arrStatus = $class->convertForCombobox($obj->getAllStatus(),'pkey','status'); 

$arrTwigVar['importUrl'] = $obj->importUrl;

$arrTwigVar['inputStartDate'] = $class->inputDate('trStartDate', array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputEndDate'] = $class->inputDate('trEndDate', array('etc' => 'style="text-align:center"'));  
$arrTwigVar['inputSelStatus'] =  $class->inputSelect('selStatus[]', $arrStatus, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputCashOutCode'] =  $class->inputText('cashOutCode'); 
$arrTwigVar['inputHidCOAKey'] =  $class->inputHidden('hidCOAKey');  
$arrTwigVar['inputCOAName'] =  $class->inputText('coaName'); 
$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate;      
echo $twig->render('reportCashOut.html', $arrTwigVar);   

?>
