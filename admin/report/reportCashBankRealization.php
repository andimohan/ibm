<?php
include '../../_config.php';
include '../../_include-v2.php';

includeClass(array('CashBankRealization.class.php','TruckingServiceOrder.class.php'));
$cashBankRealization = createObjAndAddToCol(new CashBankRealization());  
$customer = createObjAndAddToCol(new Customer());  
$arEmployee = createObjAndAddToCol(new AREmployee()); 
$truckingServiceOrder = createObjAndAddToCol(new TruckingServiceOrder());  
    
include '_global.php';

$obj = $cashBankRealization;
$securityObject = 'reportCashBankRealization'; // the value of security object is manually inserted to handle 
				               // some modules that have different security object from that of their class
								  
if(!$security->isAdminLogin($securityObject,10,true)); 
 
$arrFilterInformation = array(); 
$detailCriteria = '';  
$_POST['selStatus[]'] = array(2,3,4);
if(!isset($_POST) || empty($_POST['selDateType'])) $_POST['selDateType'] = 2;
$isShowDetail = (isset($_POST['isShowDetail']) && !empty($_POST['isShowDetail'])) ? true : false;
/*$arrDateType= array(
    '1' => $obj->lang['transactionDate'],
    '2' => $obj->lang['realizationDate'], 
);*/

// ===== FOR EXPORT SECTION
$dataToExport = array();

/* data structure */
$arrTemplate = array();

$arrDataStructure = array();
$arrDataStructure['rowNumber'] = array('title'=>'#', 'align'=>'right', 'width'=>"40px", 'autoNumber' => true, "sortable" => false);
$arrDataStructure['code'] = array('title'=>ucwords($obj->lang['code']),  'width'=>"100px", 'dbfield' => 'code');
$arrDataStructure['date'] = array('title'=>ucwords($obj->lang['date']),'dbfield' => 'trdate', 'width'=>"100px",'format'=>'date');
$arrDataStructure['CashOutCode'] = array('title'=>ucwords($obj->lang['refCode']).' 1',  'width'=>"120px", 'dbfield' => 'refcode');
$arrDataStructure['JOCode'] = array('title'=>ucwords($obj->lang['refCode']).' 2',  'width'=>"120px", 'dbfield' => 'refcode2');
$arrDataStructure['si'] = array('title'=>ucwords($obj->lang['si']),  'width'=>"120px", 'dbfield' => 'donumber');
$arrDataStructure['bookingNumber'] = array('title'=>ucwords($obj->lang['bookingNumber']),  'width'=>"120px", 'dbfield' => 'shipmentnumber');
$arrDataStructure['JOCode'] = array('title'=>ucwords($obj->lang['refCode']).' 2',  'width'=>"120px", 'dbfield' => 'refcode2');
$arrDataStructure['customer'] = array('title'=>ucwords($obj->lang['customer']),  'width'=>"200px", 'dbfield' => 'customername');
$arrDataStructure['driver'] = array('title'=>ucwords($obj->lang['driver']),'dbfield' => 'employeename', 'width'=>"150px");
//$arrDataStructure['realizationdate'] = array('title'=>ucwords($obj->lang['realizationDate']),'dbfield' => 'confirmedon', 'width'=>"100px",'format'=>'date');
$arrDataStructure['party'] = array('title'=>ucwords($obj->lang['party']),  'width'=>"150px", 'dbfield' => 'party');
$arrDataStructure['note'] = array('title'=>ucwords($obj->lang['note']),'dbfield' => 'trdesc', 'width'=>"200px",);
$arrDataStructure['total'] = array('title'=>ucwords($obj->lang['total']),'dbfield' => 'total', 'width'=>"100px" ,'format'=>'number','calculateTotal' => true, 'textColor' => '568203');
$arrDataStructure['totalRealization'] = array('title'=>ucwords($obj->lang['realization']),'dbfield' => 'totalrealization', 'width'=>"100px" ,'format'=>'number','calculateTotal' => true);
$arrDataStructure['totalDifference'] = array('title'=>ucwords($obj->lang['totalDifference']),'dbfield' => 'totalreceived', 'width'=>"100px" ,'format'=>'number','calculateTotal' => true);
$arrDataStructure['ar'] = array('title'=>ucwords($obj->lang['employeeAR']),'dbfield' => 'employeear', 'width'=>"120px" ,'format'=>'number','calculateTotal' => true);
$arrDataStructure['arOutstanding'] = array('title'=>ucwords($obj->lang['AROutstanding']),'dbfield' => 'aroutstanding', 'width'=>"120px" ,'format'=>'number', "sortable" => false,'calculateTotal' => true);
$arrDataStructure['balance'] = array('title'=>ucwords($obj->lang['balance']),'dbfield' => 'balance', 'width'=>"100px" ,'format'=>'number','calculateTotal' => true);
$arrDataStructure['status'] = array('title'=>ucwords($obj->lang['status']),'dbfield' => 'statusname', 'width'=>"100px");


$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $obj->lang['cashBankRealizationReport'];
$arrHeaderTemplate['reportWidth'] = "1100px;";
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();

array_push($arrTemplate, $arrHeaderTemplate);
if ($isShowDetail){ 
	$arrDataDetailStructure = array();
	$arrDataDetailStructure['cost'] = array('title'=>ucwords($obj->lang['cost']),  'dbfield' => 'costname', 'mergeExcelCell' => 2); 
	$arrDataDetailStructure['amount'] = array('title'=>ucwords($obj->lang['amount']),  'dbfield' => 'amount', 'width'=>"100px", 'format' => 'number','calculateTotal' => true , 'textColor' => '568203'); 
	$arrDataDetailStructure['amountRealization'] = array('title'=>ucwords($obj->lang['realization']),  'dbfield' => 'realamount', 'width'=>"100px", 'format' => 'number','calculateTotal' => true ); 
	$arrDataDetailStructure['balance'] = array('title'=>ucwords($obj->lang['balance']),  'dbfield' => 'balance', 'width'=>"100px", 'format' => 'number','calculateTotal' => true ); 
	$arrDataDetailStructure['cashOutCode'] = array('title'=>ucwords($obj->lang['reference']),  'dbfield' => 'cashoutcode', 'width'=>"100px");  
	$arrDataDetailStructure['trDesc'] = array('title'=>ucwords($obj->lang['note']), 'dbfield' => 'description', 'mergeExcelCell' => 4); 

	$arrDetailTemplate = array();
	$arrDetailTemplate['reportWidth'] = "800px";
	$arrDetailTemplate['dataStructure'] = $arrDataDetailStructure;
	$arrDetailTemplate['total'] = array();

	array_push($arrTemplate, $arrDetailTemplate);
}
if (isset($_POST) && !empty($_POST['hidAction'])){
	$criteria = '';
	if(isset($_POST) && !empty($_POST['code'])) {
		$criteria .= ' AND '.$obj->tableName.'.code LIKE ('.$class->oDbCon->paramString('%'.$_POST['code'].'%').')';
		array_push($arrFilterInformation,array("label" => 'Kode', 'filter' => $_POST['code']));
	}
    if(isset($_POST) && !empty($_POST['cashOutCode'])) {
		$criteria .= ' AND '.$obj->tableName.'.refcode LIKE ('.$class->oDbCon->paramString('%'.$_POST['cashOutCode'].'%').')';
		array_push($arrFilterInformation,array("label" => 'Kode SPK', 'filter' => $_POST['cashOutCode']));
	}
    if(isset($_POST) && !empty($_POST['joCode'])) {
		$criteria .= ' AND '.$obj->tableName.'.refcode2 LIKE ('.$class->oDbCon->paramString('%'.$_POST['joCode'].'%').')';
		array_push($arrFilterInformation,array("label" => 'Kode Job Order', 'filter' => $_POST['joCode']));
	}
    if(isset($_POST) && !empty($_POST['driver'])) {
		$criteria .= ' AND '.$obj->tableEmployee.'.name LIKE ('.$class->oDbCon->paramString('%'.$_POST['driver'].'%').')';
		array_push($arrFilterInformation,array("label" => 'Supir', 'filter' => $_POST['driver']));
	}
      
    if(isset($_POST) && !empty($_POST['selCustomer'])) {
        $key = implode(",", $class->oDbCon->paramString($_POST['selCustomer']));   
        
       	$criteria .= ' AND '.$obj->tableTruckingServiceOrder.'.customerkey in('.$key.')';  

        $rsCriteria = $customer->searchData('','',true, ' and '.$customer->tableName.'.pkey in ('.$key.')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$statusName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => $obj->lang['customer'], 'filter' => $statusName ));
	}
    
/*	if(isset($_POST) && !empty($_POST['trStartDate'])){
		$criteria .= ' and '.$obj->tableName.'.trdate between '.$class->oDbCon->paramDate( $_POST['trStartDate'],' / ').' AND '.$class->oDbCon->paramDate( $_POST['trEndDate'],' / '); 
		array_push($arrFilterInformation,array("label" => 'Tanggal', 'filter' => $_POST['trStartDate'] . ' - ' .$_POST['trEndDate'] ));
	}*/
    
     if(isset($_POST) && !empty($_POST['trStartDate'])){
        
       /* switch($_POST['selDateType']){
            case '1' : $fieldName = $obj->tableName.'.trdate';  break;
            case '2' : $fieldName = $obj->tableName.'.confirmedon'; break ;
            default : $fieldName = $obj->tableName.'.trdate';  break;
                
        } */
         
        $fieldName = $obj->tableName.'.trdate';
		$criteria .= ' and '.$fieldName.' between '.$class->oDbCon->paramDate( $_POST['trStartDate'],' / ').' AND '.$class->oDbCon->paramDate( $_POST['trEndDate'],' / ', 'Y-m-d 23:59:59');  
        array_push($arrFilterInformation,array("label" => $obj->lang['date'], 'filter' => $_POST['trStartDate'] . ' - ' .$_POST['trEndDate'] ));
		//array_push($arrFilterInformation,array("label" => $arrDateType[$_POST['selDateType']], 'filter' => $_POST['trStartDate'] . ' - ' .$_POST['trEndDate'] ));
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
	$rsARKey = $obj->getTableKeyAndObj($obj->tableName,array('key')); 
	 
	$rs = $obj->searchData('','',true,$criteria,$order);
	$arrKeys = array_column($rs,'pkey');
	$rsPaymentDetail = ($isShowDetail) ? $obj->getDetailCollections($rs,'refkey',$detailCriteria) : array();
    
    $rsARCol = array();
	if(!empty($arrKeys)){
		$rsARCol = $arEmployee->searchDataRow(array($arEmployee->tableName.'.pkey',$arEmployee->tableName.'.refkey', $arEmployee->tableName.'.code', $arEmployee->tableName.'.outstanding'),
										 ' and '.$arEmployee->tableName.'.refkey in ('.$obj->oDbCon->paramString($arrKeys,',').') and '.$arEmployee->tableName.'.reftabletype = '.$class->oDbCon->paramString($rsARKey['key']).' '
										);
	}
    
    $arrAR = (!empty($rsARCol)) ? array_column($rsARCol,null,'refkey') : array();
    $tempreport = ''; 

	$arrJOKey = array_column($rs,'jokey');
	$rsPartyCol = $truckingServiceOrder->getPartyDescription($arrJOKey);
	
	
	for( $i=0;$i<count($rs);$i++) {   
            
			$jokey = $rs[$i]['jokey'];
		
            $arrDetailStyle = array();
            $arrHeaderStyle = array();
			
            if($rs[$i]['statuskey']==1)
                $rs[$i]['totalrealization'] = 0;
            
			if(isset($rsPartyCol[$jokey]))  
				 $rs[$i]['party'] = $rsPartyCol[$jokey];
			   
			if ($rs[$i]['totalreceived'] > 0) { 
                $arrHeaderStyle['totalreceived']['textColor'] = '0093AF';  
                $arrHeaderStyle['totalrealization']['textColor'] = '0093AF';   
            }
           	
			if(!empty($arrAR[$rs[$i]['pkey']]['outstanding']))
				$rs[$i]['aroutstanding'] = $arrAR[$rs[$i]['pkey']]['outstanding'];
 
			if($isShowDetail){
				$rsDetail = $rsPaymentDetail[$rs[$i]['pkey']];
            	if (empty($rsDetail))
                	continue;
				
				for($j=0;$j<count($rsDetail);$j++){ 
					if ($rsDetail[$j]['balance'] > 0){ 
						$arrDetailStyle[$j]['realamount']['textColor'] = '0093AF';  
						$arrDetailStyle[$j]['balance']['textColor'] = '0093AF';  
					}
				} 
            
            	$rs[$i]['_detail_'] = array('arrTemplate'=>$arrDetailTemplate,'data' => $rsDetail, 'style' => $arrDetailStyle);
				
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
$arrCustomer = $class->convertForCombobox($customer->searchData($customer->tableName.'.statuskey',2,true,'','order by name asc'),'pkey','name');


$arrTwigVar['inputCode'] =  $class->inputText('code');
$arrTwigVar['inputStartDate'] = $class->inputDate('trStartDate', array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputEndDate'] = $class->inputDate('trEndDate', array('etc' => 'style="text-align:center"'));
//$arrTwigVar['inputSelDateType'] =  $class->inputSelect('selDateType', $arrDateType);  
$arrTwigVar['inputCashOutCode'] =  $class->inputText('cashOutCode');  
$arrTwigVar['inputJoCode'] =  $class->inputText('joCode'); 
$arrTwigVar['inputHidDriverKey'] = $class->inputHidden('hidDriverKey');
$arrTwigVar['inputDriver'] =  $class->inputText('driver'); 
$arrTwigVar['inputSelStatus'] =  $class->inputSelect('selStatus[]', $arrStatus, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox')); 
$arrTwigVar['inputIsShowDetail'] =  $class->inputCheckBox('isShowDetail');
$arrTwigVar['inputSelCustomer'] =  $class->inputSelect('selCustomer[]', $arrCustomer, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate;      

echo $twig->render('reportCashBankRealization.html', $arrTwigVar);   

?>
