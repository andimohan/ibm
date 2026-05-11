<?php
include '../../_config.php';
include '../../_include-v2.php';

includeClass('CashAdvanceRealization.class.php');
$cashAdvanceRealization = createObjAndAddToCol( new CashAdvanceRealization()); 
$cashAdvance = createObjAndAddToCol( new CashAdvance()); 
$employee = createObjAndAddToCol( new Employee()); 
$warehouse = createObjAndAddToCol( new Warehouse()); 
$chartOfAccount = createObjAndAddToCol( new ChartOfAccount());

include '_global.php';

$obj = $cashAdvanceRealization;
$securityObject = 'reportCashAdvanceRealization'; // the value of security object is manually inserted to handle 
								  // some modules that have different security object from that of their class
								  
if(!$security->isAdminLogin($securityObject,10,true)); 

$_POST['selStatus[]'] = array(2,3);
$arrFilterInformation = array();
// ===== FOR EXPORT SECTION
$dataToExport = array();
$detailCriteria = ''; 

/* data structure */
$arrTemplate = array();

$arrDataStructure = array();
$arrDataStructure['rowNumber'] = array('title'=>'#', 'align'=>'right', 'width'=>"40px", 'autoNumber' => true, "sortable" => false);
$arrDataStructure['code'] = array('title'=>ucwords($obj->lang['code']),  'width'=>"130px", 'dbfield' => 'code');
$arrDataStructure['date'] = array('title'=>ucwords($obj->lang['date']),'dbfield' => 'trdate', 'width'=>"100px",'format'=>'date');
$arrDataStructure['warehouse'] = array('title'=>ucwords($obj->lang['warehouse']),'dbfield' => 'warehousename', 'width'=>"200px");
$arrDataStructure['closingAccount'] = array('title'=>ucwords($obj->lang['settlementAccount']),'dbfield' => 'coacodename', 'width'=>"250px");
//$arrDataStructure['cashAdvance'] = array('title'=>ucwords($obj->lang['cashAdvance']),'dbfield' => 'cashadvancecode', 'width'=>"150px");
$arrDataStructure['recipient'] = array('title'=>ucwords($obj->lang['recipient']),'dbfield' => 'employeename', 'width'=>"250px" );
//$arrDataStructure['jocode'] = array('title'=>ucwords($obj->lang['JOCode']),  'width'=>"250px", 'dbfield' => 'jocode'); 
$arrDataStructure['recipientAccount'] = array('title'=>ucwords($obj->lang['recipientAccount']),'dbfield' => 'coaadvancecodename', 'width'=>"250px");
$arrDataStructure['amount'] = array('title'=>ucwords($obj->lang['amount']),'dbfield' => 'amount', 'width'=>"100px" ,'format'=>'number','calculateTotal' => true);
$arrDataStructure['settlement'] = array('title'=>ucwords($obj->lang['settlement']),'dbfield' => 'total', 'width'=>"100px" ,'format'=>'number','calculateTotal' => true);
$arrDataStructure['balance'] = array('title'=>ucwords($obj->lang['balance']),'dbfield' => 'balance', 'width'=>"100px" ,'format'=>'number','calculateTotal' => true);
$arrDataStructure['desc'] = array('title'=>ucwords($obj->lang['description']),'dbfield' => 'trdesc', 'width'=>"250px" );
$arrDataStructure['status'] = array('title'=>ucwords($obj->lang['status']),'dbfield' => 'statusname', 'width'=>"100px");
		  
$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $obj->lang['cashAdvanceRealizationReport'];
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();

array_push($arrTemplate, $arrHeaderTemplate);

$arrDataDetailStructure = array();
$arrDataDetailStructure['jobOrderCode'] = array('title'=>ucwords($obj->lang['jobOrderCode']),  'dbfield' => 'jocode', 'width'=>'150px', 'format' => 'string' );
$arrDataDetailStructure['containername'] = array('title'=>ucwords($obj->lang['container']),  'dbfield' => 'containername', 'width'=>'80px', 'format' => 'string' );
$arrDataDetailStructure['service'] = array('title'=>ucwords($obj->lang['service']),  'dbfield' => 'servicename', 'width'=>'100px', 'format' => 'string' );
$arrDataDetailStructure['invoiceReference'] = array('title'=>ucwords($obj->lang['invoiceReference']),  'dbfield' => 'invoicereference', 'width'=>'200px', 'format' => 'string' );
$arrDataDetailStructure['supplier'] = array('title'=>ucwords($obj->lang['supplier']),  'dbfield' => 'suppliername', 'width'=>'150px', 'format' => 'string' );
//$arrDataDetailStructure['cost'] = array('title'=>ucwords($obj->lang['cost']),  'dbfield' => 'coaname', 'width'=>'150px', 'format' => 'string' ); 
$arrDataDetailStructure['qty'] = array('title'=>ucwords($obj->lang['qty']),  'dbfield' => 'qty', 'width'=>"100px", 'format' => 'number');
$arrDataDetailStructure['beforeTax'] = array('title'=>ucwords($obj->lang['amount']),  'dbfield' => 'beforetaxtotalinunit', 'width'=>"100px", 'format' => 'number' ,'calculateTotal' => true);
$arrDataDetailStructure['taxValue'] = array('title'=>ucwords($obj->lang['PPN']),  'dbfield' => 'taxvalueinunit', 'width'=>"60px", 'format' => 'number' ,'calculateTotal' => true);
$arrDataDetailStructure['subtotal'] = array('title'=>ucwords($obj->lang['subtotal']),  'dbfield' => 'subtotal', 'width'=>"120px", 'format' => 'number' ,'calculateTotal' => true);
$arrDataDetailStructure['description'] = array('title'=>ucwords($obj->lang['description']),  'dbfield' => 'description', 'width'=>'200px', 'format' => 'string' );

$arrDetailTemplate = array();
$arrDetailTemplate['reportWidth'] = "1380px";
$arrDetailTemplate['dataStructure'] = $arrDataDetailStructure;
$arrDetailTemplate['total'] = array();  

array_push($arrTemplate, $arrDetailTemplate);

if (isset($_POST) && !empty($_POST['hidAction'])){
	
	$criteria = '';

	if(isset($_POST) && !empty($_POST['codeAdvance'])) {
		$criteria .= ' AND '.$obj->tableName.'.code LIKE ('.$class->oDbCon->paramString('%'.$_POST['codeAdvance'].'%').')';
		array_push($arrFilterInformation,array("label" => $obj->lang['code'], 'filter' => $_POST['codeAdvance']));
	}
	
	if(isset($_POST) && !empty($_POST['trStartDate'])){
		$criteria .= ' and '.$obj->tableName.'.trdate between '.$class->oDbCon->paramDate( $_POST['trStartDate'],' / ').' AND '.$class->oDbCon->paramDate( $_POST['trEndDate'],' / '); 
		array_push($arrFilterInformation,array("label" => $obj->lang['date'], 'filter' => $_POST['trStartDate'] . ' - ' .$_POST['trEndDate'] ));
	}
    
    if(isset($_POST) && !empty($_POST['selEmployee'])) { 
        $key = implode(",", $class->oDbCon->paramString($_POST['selEmployee'])); 
       	$criteria .= ' AND employeekey in('.$key.')';  
        $rsCriteria = $employee->searchData('','',true, ' and '.$employee->tableName.'.pkey in ('.$key.')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$employeeName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => $obj->lang['recipient'], 'filter' => $employeeName ));
        
	}
	
	if(isset($_POST) && !empty($_POST['selCOA'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selCOA']));   
        
       	$criteria .= ' AND '.$obj->tableName.'.coakey in('.$key.')';  
        
        $rsCriteria = $chartOfAccount->searchData('','',true, ' and '.$chartOfAccount->tableName.'.pkey in ('.$key.')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['coaname']);
			
		$coaName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => $obj->lang['cashBankAccount'], 'filter' => $coaName ));
        
	}
    
//    if(isset($_POST) && !empty($_POST['selCash'])) { 
//        
//        $key = implode(",", $class->oDbCon->paramString($_POST['selCash']));   
//        
//        $criteria .= ' AND '.$obj->tableName.'.refkey in('.$key.')';  
//
//        $rsCriteria = $cashAdvance->searchData('','',true, ' and '.$cashAdvance->tableName.'.pkey in ('.$key.')');
//
//        $arrTempStatus = array();
//		  for ($k=0;$k<count($rsCriteria);$k++)
//		  array_push($arrTempStatus,$rsCriteria[$k]['code']);
//			
//		 $cashAdvanceCode = implode(", ",$arrTempStatus); 
//	     array_push($arrFilterInformation,array("label" => $obj->lang['cashAdvance'], 'filter' => $cashAdvanceCode ));
//        
//	} 
	
	if(isset($_POST) && !empty($_POST['selWarehouse'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selWarehouse']));   
        
        $criteria .= ' AND '.$obj->tableName.'.warehousekey in('.$key.')';  

        $rsCriteria = $warehouse->searchData('','',true, ' and '.$warehouse->tableName.'.pkey in ('.$key.')');

        $arrTempStatus = array();
		  for ($k=0;$k<count($rsCriteria);$k++)
		  array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		 $warehouseName = implode(", ",$arrTempStatus); 
	     array_push($arrFilterInformation,array("label" => $obj->lang['warehouse'], 'filter' => $warehouseName ));
        
	}  
	
	if(isset($_POST) && !empty($_POST['selStatus'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selStatus']));   
        
       	$criteria .= ' AND '.$obj->tableName.'.statuskey in('.$key.')';  

        $rsCriteria =  $obj->getStatusById($key);
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['status']);
			
		$statusName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => $obj->lang['status'], 'filter' => $statusName));
        
	}
	
    $orderBy = (!empty($_POST['hidOrderBy'])) ? $obj->oDbCon->paramOrder($_POST['hidOrderBy']) : 'pkey'; // order by harus dr kolom yg terdaftar saja
    $orderType = (isset($_POST['hidOrderType']) && !empty($_POST['hidOrderType']) && $_POST['hidOrderType'] == 1) ? 'desc' : 'asc';
    
	$order = 'order by '.$orderBy.' ' .$orderType; 
	 
	$rs = $obj->searchData('','',true,$criteria,$order);
	
	$rsDetailCol = $obj->getDetailCollections($rs,'refkey',$detailCriteria);
    $rsReIndex = array_column($rs, null, 'pkey');

    $tempreport = ''; 
//    foreach ($rsReIndex as $index) {
//        $pkey = $index['pkey'];
//        $arrJO = array(); 
//        foreach($rsDetailCol[$pkey] as $indexDetail) {
//                array_push($arrJO, $indexDetail['jobordercode']);
//            }
//        
//        $arrPO = implode(", ",$arrJO);
//        $rsReIndex[$pkey]['jobordercode'] = $arrPO;
//        
//    }

    for($i=0;$i<count($rs);$i++){
		
        //$rs[$i]['jocode'] = $rsReIndex[$rs[$i]['pkey']]['jobordercode'];
		if (!isset($rsDetailCol[$rs[$i]['pkey']]))  continue;
        $rsDetail = $rsDetailCol[$rs[$i]['pkey']]; 
        
        for($j=0;$j<count($rsDetail);$j++){
            
            $detailDesc ='';
			$invoiceReference ='';
			$serviceName = (!empty($rsDetail[$j]['servicename'])) ? $rsDetail[$j]['servicename']:'';
			$supplierName = (!empty($rsDetail[$j]['suppliername'])) ? $rsDetail[$j]['suppliername']:'';
			
			if($rsDetail[$j]['cashtypekey']==1){
				$detailDesc = $rsDetail[$j]['jobordercode'];
				$invoiceReference = (!empty($rsDetail[$j]['refcode'])) ? $rsDetail[$j]['refcode']:'';
			}else if($rsDetail[$j]['cashtypekey']==2) {
				$detailDesc = $obj->lang['downpayment'];  
			}else if($rsDetail[$j]['cashtypekey']==3){
				$detailDesc = $rsDetail[$j]['coaname'];  
			}else if($rsDetail[$j]['cashtypekey']==4){
				$detailDesc = $rsDetail[$j]['jobheadercode'];
				$invoiceReference = (!empty($rsDetail[$j]['refcode'])) ? $rsDetail[$j]['refcode']:'';
			}
                        
            $rsDetail[$j]['jocode'] = $detailDesc;
            $rsDetail[$j]['invoicereference'] = $invoiceReference;
            $rsDetail[$j]['suppliername'] = $supplierName;
            $rsDetail[$j]['servicename'] = $serviceName;
            
        }
        

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

//$arrTransaction = $class->convertForCombobox($obj->getTransactionType(),'pkey','name');
$arrWarehouse = $class->convertForCombobox($warehouse->searchData($warehouse->tableName.'.statuskey',1,true,'','order by name asc'),'pkey','name');
$arrEmployee = $class->convertForCombobox($employee->searchData($employee->tableName.'.statuskey',2,true,'','order by name asc'),'pkey','name');
$arrCash = $class->convertForCombobox($cashAdvance->searchData($cashAdvance->tableName.'.statuskey',2,true,'','order by pkey asc'),'pkey','code');
$arrCOA = $class->convertForCombobox($chartOfAccount->searchData($chartOfAccount->tableName.'.statuskey',1,true,' and iscashbank = 1','order by coaname asc'),'pkey','coaname');
$arrStatus = $class->convertForCombobox($obj->getAllStatus(),'pkey','status');

$arrTwigVar['inputStartDate'] = $class->inputDate('trStartDate', array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputEndDate'] = $class->inputDate('trEndDate', array('etc' => 'style="text-align:center"'));  
$arrTwigVar['inputSelStatus'] =  $class->inputSelect('selStatus[]', $arrStatus, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputSelWarehouse'] =  $class->inputSelect('selWarehouse[]', $arrWarehouse, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputSelCOA'] =  $class->inputSelect('selCOA[]', $arrCOA, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputSelEmployee'] =  $class->inputSelect('selEmployee[]', $arrEmployee, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputSelCash'] =  $class->inputSelect('selCash[]', $arrCash ,array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputCodeAdvance'] =  $class->inputText('codeAdvance');
$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate;      
echo $twig->render('reportCashAdvanceRealization.html', $arrTwigVar);   

?>
