<?php
include '../../_config.php';
include '../../_include-v2.php';

includeClass('CashAdvance.class.php');
$cashAdvance = createObjAndAddToCol( new CashAdvance());  
$employee = createObjAndAddToCol( new Employee()); 
$warehouse = createObjAndAddToCol( new Warehouse()); 
$chartOfAccount = createObjAndAddToCol( new ChartOfAccount());
$cashBank = createObjAndAddToCol( new CashBank());
$cashAdvanceRealization = createObjAndAddToCol( new CashAdvanceRealization()); 

include '_global.php';

$obj = $cashAdvance;
$securityObject = 'reportCashAdvance'; // the value of security object is manually inserted to handle 
								  // some modules that have different security object from that of their class
								  
if(!$security->isAdminLogin($securityObject,10,true)); 

$_POST['selStatus[]'] = array(2,3);
$arrFilterInformation = array();


// ===== FOR EXPORT SECTION
$dataToExport = array();

/* data structure */
$arrTemplate = array();
$isShowDetail = (isset($_POST['isShowDetail']) && !empty($_POST['isShowDetail'])) ? true : false;

$arrDataStructure = array();
$arrDataStructure['rowNumber'] = array('title'=>'#', 'align'=>'right', 'width'=>"40px", 'autoNumber' => true, "sortable" => false);
$arrDataStructure['code'] = array('title'=>ucwords($obj->lang['code']),  'width'=>"130px", 'dbfield' => 'code');
$arrDataStructure['date'] = array('title'=>ucwords($obj->lang['date']),'dbfield' => 'trdate', 'width'=>"100px",'format'=>'date');
$arrDataStructure['warehouse'] = array('title'=>ucwords($obj->lang['warehouse']),'dbfield' => 'warehousename', 'width'=>"120px");
$arrDataStructure['cashBankAccount'] = array('title'=>ucwords($obj->lang['cashBankAccount']),'dbfield' => 'coacodename', 'width'=>"300px" );
$arrDataStructure['recipient'] = array('title'=>ucwords($obj->lang['recipient']),'dbfield' => 'employeename', 'width'=>"180px" );
$arrDataStructure['recipientAccount'] = array('title'=>ucwords($obj->lang['recipientAccount']),'dbfield' => 'coaadvancecodename', 'width'=>"250px");
$arrDataStructure['amount'] = array('title'=>ucwords($obj->lang['amount']),'dbfield' => 'amount', 'width'=>"120px" ,'format'=>'number', 'calculateTotal' => true );

$arrDataStructure['ashAdvanceRealizationCode'] = array('title'=>ucwords($obj->lang['cashAdvanceRealization']),'dbfield' => 'realizationadnvacecode', 'width'=>"150px");

$arrDataStructure['status'] = array('title'=>ucwords($obj->lang['status']),'dbfield' => 'statusname', 'width'=>"100px");
		  
$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $obj->lang['cashAdvanceReport'];
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();

array_push($arrTemplate, $arrHeaderTemplate);

$arrDataDetailStructure = array();
$arrDataDetailStructure['realizationcode'] = array('title'=>ucwords($obj->lang['realizationCode']),  'dbfield' => 'realizationcode', 'width'=>'120px', 'format' => 'string' );
$arrDataDetailStructure['realizationdate'] = array('title'=>ucwords($obj->lang['realizationDate']),  'dbfield' => 'realizationdate',  'width'=>"120px",'format'=>'date', 'class' => 'text-center');
$arrDataDetailStructure['jobOrderCode'] = array('title'=>ucwords($obj->lang['jobOrderCode']),  'dbfield' => 'jocode', 'width'=>'120px', 'format' => 'string' );
$arrDataDetailStructure['purchaseOrderCode'] = array('title'=>ucwords($obj->lang['poCode']),  'dbfield' => 'pocode', 'width'=>'120px', 'format' => 'string' );
$arrDataDetailStructure['container'] = array('title'=>ucwords($obj->lang['container']),  'dbfield' => 'containername', 'width'=>'80px', 'format' => 'string' );
$arrDataDetailStructure['service'] = array('title'=>ucwords($obj->lang['service']),  'dbfield' => 'servicename', 'width'=>'150px', 'format' => 'string' );
$arrDataDetailStructure['subtotal'] = array('title'=>ucwords($obj->lang['subtotal']),  'dbfield' => 'subtotal', 'width'=>"120px", 'format' => 'number' ,'calculateTotal' => true);
$arrDataDetailStructure['description'] = array('title'=>ucwords($obj->lang['description']),  'dbfield' => 'description', 'width'=>'200px', 'format' => 'string' );

$arrDetailTemplate = array();
$arrDetailTemplate['reportWidth'] = "1380px";
$arrDetailTemplate['dataStructure'] = $arrDataDetailStructure;
$arrDetailTemplate['total'] = array();  

array_push($arrTemplate, $arrDetailTemplate);

if (isset($_POST) && !empty($_POST['hidAction'])){
	
	$criteria = '';

	if(isset($_POST) && !empty($_POST['codeCash'])) {
		$criteria .= ' AND '.$obj->tableName.'.code LIKE ('.$class->oDbCon->paramString('%'.$_POST['codeCash'].'%').')';
		array_push($arrFilterInformation,array("label" => $obj->lang['code'], 'filter' => $_POST['codeCash']));
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

    $arrPkey = array_column($rs,'pkey');
    $rsCashAdvanceRealizationDetail = $obj->getCashAdvanceRealizationDetail($arrPkey);
    
    $arrRealizationKeys = array_column($rsCashAdvanceRealizationDetail, 'refkey');
    $rsCashAdvanceRealizationDetail = $obj->reindexDetailCollections($rsCashAdvanceRealizationDetail,'cashadvancekey');

    $rsCashAdvanceDetail = $cashAdvanceRealization->getDetailCashAdvance($arrRealizationKeys);
    $rsCashAdvanceDetail = $obj->reindexDetailCollections($rsCashAdvanceDetail, 'refkey');

    $tempreport = '';
    for($i=0;$i<count($rs);$i++){ 
        
        $rs[$i]['realizationadnvacecode'] = '-';
        if($isShowDetail){
            
            if(!isset($rsCashAdvanceRealizationDetail[$rs[$i]['pkey']])) continue;

            $rsDetail = $rsCashAdvanceRealizationDetail[$rs[$i]['pkey']];
        
            for($j=0;$j<count($rsDetail);$j++){

                if(isset($rsCashAdvanceDetail[$rsDetail[$j]['refkey']])) {
                    $rsCashAdvanceDetailCol = $rsCashAdvanceDetail[$rsDetail[$j]['refkey']];
                    $advanceCode = array_column($rsCashAdvanceDetailCol,'cashadvancecode');

                    $rs[$i]['realizationadnvacecode'] = implode(',  <br>', $advanceCode);
                }

                if($rsDetail[$j]['cashtypekey']==1){
                    $detailDesc = $rsDetail[$j]['jobordercode'];
                    $invoiceReference = (!empty($rsDetail[$j]['refcode'])) ? $rsDetail[$j]['refcode']:'';
                }else if($rsDetail[$j]['cashtypekey']==2) {
                    $detailDesc = $obj->lang['downpayment'];  
                }else if($rsDetail[$j]['cashtypekey']==3){
                    $detailDesc = $rsDetail[$j]['coaname'];  
                }else if($rsDetail[$j]['cashtypekey']==4){
					// ini lupa utk ap
                    //$detailDesc = $rsDetail[$j]['jobheadercode'];
                    $invoiceReference = (!empty($rsDetail[$j]['refcode'])) ? $rsDetail[$j]['refcode']:'';
                }

                $rsDetail[$j]['jocode'] = $detailDesc;
            
            }

            $rs[$i]['_detail_'] = array('arrTemplate'=>$arrDetailTemplate,'data' => $rsDetail);

        }
        
        $return = $obj->formatReportRows(array('data'=>$rs[$i]),$arrTemplate); 

        // ===== FOR EXPORT SECTION 
        array_push($dataToExport, $return['data']);  
        // ===== END FOR EXPORT SECTION

        $tempreport .= $return['html'];  
        $arrTemplate[0]['total'] = $obj->arraySum($arrTemplate[0]['total'], $return['subtotal'][0]);

    } 

    $obj->generateReport($_POST, $tempreport, $arrTemplate,$dataToExport,$arrFilterInformation);
} else {
    $_POST['trStartDate'] = date('d / m / Y');
	$_POST['trEndDate'] = date('d / m / Y');
}


$arrWarehouse = $class->convertForCombobox($warehouse->searchData($warehouse->tableName.'.statuskey',1,true,'','order by name asc'),'pkey','name');
$arrEmployee = $class->convertForCombobox($employee->searchData($employee->tableName.'.statuskey',2,true,'','order by name asc'),'pkey','name');
$arrCOA = $class->convertForCombobox($chartOfAccount->searchData($chartOfAccount->tableName.'.statuskey',1,true,' and iscashbank = 1','order by coaname asc'),'pkey','coaname');
$arrStatus = $class->convertForCombobox($obj->getAllStatus(),'pkey','status');

$arrTwigVar['inputStartDate'] = $class->inputDate('trStartDate', array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputEndDate'] = $class->inputDate('trEndDate', array('etc' => 'style="text-align:center"'));  
$arrTwigVar['inputSelStatus'] =  $class->inputSelect('selStatus[]', $arrStatus, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputSelWarehouse'] =  $class->inputSelect('selWarehouse[]', $arrWarehouse, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputSelCOA'] =  $class->inputSelect('selCOA[]', $arrCOA, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputSelEmployee'] =  $class->inputSelect('selEmployee[]', $arrEmployee, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputCodeCash'] =  $class->inputText('codeCash');
$arrTwigVar['inputIsShowDetail'] =  $class->inputCheckBox('isShowDetail');
//$arrTwigVar['order'] =  $orderCriteria;
$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate;  

echo $twig->render('reportCashAdvance.html', $arrTwigVar); 

?>