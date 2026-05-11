<?php
include '../../_config.php';
include '../../_include-v2.php'; 

includeClass('CashIn.class.php');
$cashIn= createObjAndAddToCol( new CashIn()); 
include '_global.php';

$obj = $cashIn;
$securityObject = 'reportCashIn'; // the value of security object is manually inserted to handle 
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
$arrDataStructure['rowNumber'] = array('title'=>'#', 'align'=>'right', 'width'=>"40px", 'autoNumber' => true, "sortable" => false);
$arrDataStructure['code'] = array('title'=>ucwords($obj->lang['code']),  'width'=>"100px", 'dbfield' => 'code');
$arrDataStructure['coalink'] = array('title'=>ucwords($obj->lang['account']),'dbfield' => 'codename', 'width'=>"150px");
$arrDataStructure['paidFrom'] = array('title'=>ucwords($obj->lang['from']),'dbfield' => 'recipientname');
$arrDataStructure['date'] = array('title'=>ucwords($obj->lang['date']),'dbfield' => 'trdate', 'width'=>"100px",'format'=>'date');
$arrDataStructure['note'] = array('title'=>ucwords($obj->lang['note']),'dbfield' => 'trdesc', 'width'=>"350px");
$arrDataStructure['total'] = array('title'=>ucwords($obj->lang['total']),'dbfield' => 'grandtotal', 'width'=>"120px" ,'format'=>'number','calculateTotal' => true);
$arrDataStructure['status'] = array('title'=>ucwords($obj->lang['status']),'dbfield' => 'statusname', 'width'=>"100px");
		  
$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $obj->lang['cashInReport'];
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();

array_push($arrTemplate, $arrHeaderTemplate);

$arrDataDetailStructure = array(); 
$arrDataDetailStructure['revenueAccount'] = array('title'=>ucwords($obj->lang['revenueAccount']),  'dbfield' => 'codename', 'mergeExcelCell' => 2);  
$arrDataDetailStructure['amount'] = array('title'=>ucwords($obj->lang['amount']),  'dbfield' => 'amount', 'width'=>"120px", 'format' => 'number'); 
$arrDataDetailStructure['trDesc'] = array('title'=>ucwords($obj->lang['note']), 'dbfield' => 'trdesc', 'mergeExcelCell' => 3); 

$arrDetailTemplate = array();
$arrDetailTemplate['reportWidth'] = "700px";
$arrDetailTemplate['dataStructure'] = $arrDataDetailStructure;
$arrDetailTemplate['total'] = array();

array_push($arrTemplate, $arrDetailTemplate);



if (isset($_POST) && !empty($_POST['hidAction'])){
	
	$criteria = '';

	if(isset($_POST) && !empty($_POST['cashInCode'])) {
		$criteria .= ' AND '.$obj->tableName.'.code LIKE ('.$class->oDbCon->paramString('%'.$_POST['cashInCode'].'%').')';
		array_push($arrFilterInformation,array("label" => 'Kode', 'filter' => $_POST['cashInCode']));
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
	 
	$rs = $obj->searchData('','',true,$criteria,$order);
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
            $arrTemplate[0]['total'] = $obj->arraySum($arrTemplate[0]['total'], $return['subtotal'][0]);
            
		} 
    
        $obj->generateReport($_POST, $tempreport, $arrTemplate,$dataToExport,$arrFilterInformation);
}
else{ 
	$_POST['trStartDate'] = date('d / m / Y');
	$_POST['trEndDate'] = date('d / m / Y');
}  

$arrStatus = $class->convertForCombobox($obj->getAllStatus(),'pkey','status'); 

 
$arrTwigVar['inputStartDate'] = $class->inputDate('trStartDate', array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputEndDate'] = $class->inputDate('trEndDate', array('etc' => 'style="text-align:center"'));  
$arrTwigVar['inputSelStatus'] =  $class->inputSelect('selStatus[]', $arrStatus, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputCashInCode'] =  $class->inputText('cashInCode'); 
$arrTwigVar['inputHidCOAKey'] =  $class->inputHidden('hidCOAKey');  
$arrTwigVar['inputCOAName'] =  $class->inputText('coaName');
$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate; 
echo $twig->render('reportCashIn.html', $arrTwigVar);   

?>
