<?php
include '../../_config.php';
include '../../_include-v2.php';

includeClass('CashBankOut.class.php');
$cashBankOut = createObjAndAddToCol( new CashBankOut()); 
$cashBank = createObjAndAddToCol( new CashBank()); 

include '_global.php';

$obj = $cashBankOut;
$securityObject = 'reportCashBankOut'; // the value of security object is manually inserted to handle 
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
$_POST['module'] = IMPORT_TEMPLATE['cashBankOut'];
switch($EXPORT_TYPE){
    case 2 :
            $arrDataStructure['code'] = array('title'=>ucwords($obj->lang['code']),  'width'=>"150px", 'dbfield' => 'code');
			$arrDataStructure['warehouse'] = array('title'=>ucwords($obj->lang['warehouse']),'dbfield' => 'warehousename', 'width'=>"150px" );
			$arrDataStructure['date'] = array('title'=>ucwords($obj->lang['date']),'dbfield' => 'trdate', 'width'=>"100px",'format'=>'date');
			$arrDataStructure['coalink'] = array('title'=>ucwords($obj->lang['cash/bank']),'dbfield' => 'codename', 'width'=>"200px");
			$arrDataStructure['note'] = array('title'=>ucwords($obj->lang['note']),'dbfield' => 'trdesc', 'width'=>"350px");
			$arrDataStructure['customer'] = array('title'=>ucwords($obj->lang['customer']),'dbfield' => 'customername', 'width'=>"150px");
			$arrDataStructure['description'] = array('title'=>ucwords($obj->lang['detailsNote']),'dbfield' => 'description', 'width'=>"250px");
			$arrDataStructure['amount'] = array('title'=>ucwords($obj->lang['amount']),  'dbfield' => 'amount', 'width'=>"60px", 'format' => 'number' ); 
			$arrDataStructure['transactionType'] = array('title'=>ucwords($obj->lang['transactionType']),'dbfield' => 'transactionyype', 'width'=>"100px");

            break;
        
    default :
            $arrDataStructure['rowNumber'] = array('title'=>'#', 'align'=>'right', 'width'=>"40px", 'autoNumber' => true, "sortable" => false);
			$arrDataStructure['code'] = array('title'=>ucwords($obj->lang['code']),  'width'=>"100px", 'dbfield' => 'code');
			$arrDataStructure['date'] = array('title'=>ucwords($obj->lang['date']),'dbfield' => 'trdate', 'width'=>"100px",'format'=>'date');
			$arrDataStructure['cashbank'] = array('title'=>ucwords($obj->lang['cashBank']),  'dbfield' => 'codename');  
			$arrDataStructure['grandtotal'] = array('title'=>ucwords($obj->lang['amount']),  'width'=>"120px", 'dbfield' => 'grandtotal', 'format' => 'number', 'calculateTotal' => true);  
			$arrDataStructure['note'] = array('title'=>ucwords($obj->lang['note']),'dbfield' => 'trdesc',  'width'=>"300px");
			$arrDataStructure['status'] = array('title'=>ucwords($obj->lang['status']),'dbfield' => 'statusname', 'width'=>"100px");
}		  
$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = str_replace('/','',$obj->lang['cashBankOutReport']);
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();

array_push($arrTemplate, $arrHeaderTemplate);
if ($EXPORT_TYPE != 2){

$arrDataDetailStructure = array();
$arrDataDetailStructure['customername'] = array('title'=>ucwords($obj->lang['customer']),  'dbfield' => 'customername');  
$arrDataDetailStructure['trdescdetail'] = array('title'=>ucwords($obj->lang['note']),  'dbfield' => 'trdesc');  
$arrDataDetailStructure['amount'] = array('title'=>ucwords($obj->lang['amount']),  'dbfield' => 'amount', 'width'=>"120px", 'format' => 'number'); 
$arrDataDetailStructure['transactiontype'] = array('title'=>ucwords($obj->lang['transactionType']),  'dbfield' => 'transactiontype', 'width'=>"120px"); 
$arrDataDetailStructure['cashbankrefcode'] = array('title'=>ucwords($obj->lang['cashBankNumber']), 'dbfield' => 'cashbankrefcode'); 

$arrDetailTemplate = array();
$arrDetailTemplate['reportWidth'] = "1000px";
$arrDetailTemplate['dataStructure'] = $arrDataDetailStructure;
$arrDetailTemplate['total'] = array();

array_push($arrTemplate, $arrDetailTemplate);
}
if (isset($_POST) && !empty($_POST['hidAction'])){
	
	$criteria = '';

	if(isset($_POST) && !empty($_POST['cashBankOutCode'])) {
		$criteria .= ' AND '.$obj->tableName.'.code LIKE ('.$class->oDbCon->paramString('%'.$_POST['cashBankOutCode'].'%').')';
		array_push($arrFilterInformation,array("label" => $obj->lang['code'], 'filter' => $_POST['cashBankOutCode']));
	}
	
	if(isset($_POST) && !empty($_POST['trStartDate'])){
		$criteria .= ' and trdate between '.$class->oDbCon->paramDate( $_POST['trStartDate'],' / ').' AND '.$class->oDbCon->paramDate( $_POST['trEndDate'],' / '); 
		array_push($arrFilterInformation,array("label" => $obj->lang['date'], 'filter' => $_POST['trStartDate'] . ' - ' .$_POST['trEndDate'] ));
	}

	
	if(isset($_POST) && !empty($_POST['selStatus'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selStatus']));   
        
       	$criteria .= ' AND '.$obj->tableName.'.statuskey in('.$key.')';  

        $rsCriteria =  $obj->getStatusById ($key);
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['status']);
			
		$statusName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => $obj->lang['status'], 'filter' => $statusName));
        
	}
	
    $orderBy = (!empty($_POST['hidOrderBy'])) ? $obj->oDbCon->paramOrder($_POST['hidOrderBy']) : 'pkey'; // order by harus dr kolom yg terdaftar saja
    $orderType = (isset($_POST['hidOrderType']) && !empty($_POST['hidOrderType']) && $_POST['hidOrderType'] == 1) ? 'desc' : 'asc';
    
	 
	$order = 'order by '.$orderBy.' ' .$orderType; 
	  
	$rs = ($EXPORT_TYPE != 2) ? $obj->searchData('','',true,$criteria,$order) : array();
		
    $tempreport = ''; 
    // ============================= GENERATE DATA ============================= 
    $rsDetailCol = $obj->getDetailCollections($rs,'refkey',$detailCriteria);
	
    $totalRs = count($rs);
    for($i=0;$i<$totalRs;$i++) {   

            if (!isset($rsDetailCol[$rs[$i]['pkey']]))  continue;
        
            $rsDetail = $rsDetailCol[$rs[$i]['pkey']]; 

            $totalDetail = count($rsDetail);
            for ($j=0;$j<$totalDetail;$j++){ 

                $rsCashBank = $cashBank->getCashBankRef($rs[$i]['pkey'],$obj->tableName,$rs[$i]['coakey'],$rsDetail[$j]['pkey']);

                $rsDetail[$j]['cashbankrefcode'] = $rsCashBank['code'];
                
                $rsDetail[$j]['transactiontype'] = ((!empty($rsDetail[$j]['revenuename'])) ? $rsDetail[$j]['revenuename'] : $obj->lang['temporaryAccount']);
            }
        
        
            // has detail
            $rs[$i]['_detail_'] = array('arrTemplate'=>$arrDetailTemplate,'data' => $rsDetail);
                  
            $return = $obj->formatReportRows(array('data'=>$rs[$i]),$arrTemplate); 
            
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
$arrTwigVar['importUrl'] = $obj->importUrl;
$arrTwigVar['inputStartDate'] = $class->inputDate('trStartDate', array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputEndDate'] = $class->inputDate('trEndDate', array('etc' => 'style="text-align:center"'));  
$arrTwigVar['inputSelStatus'] =  $class->inputSelect('selStatus[]', $arrStatus, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputCashBankOutCode'] =  $class->inputText('cashBankOutCode');
$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate;      
echo $twig->render('reportCashBankOut.html', $arrTwigVar);   

?>
