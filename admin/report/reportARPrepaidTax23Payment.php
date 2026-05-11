<?php	 
include '../../_config.php';  
include '../../_include-v2.php';

includeClass(array('ARPayment.class.php','ARPrepaidTax23Payment.class.php','Warehouse.class.php','Customer.class.php'));
$arPrepaidTax23Payment = createObjAndAddToCol( new ARPrepaidTax23Payment()); 
$warehouse = createObjAndAddToCol( new Warehouse()); 
$customer = createObjAndAddToCol( new Customer()); 

include '_global.php';

$obj= $arPrepaidTax23Payment;
$ar = $obj->getARObj();
$securityObject = 'reportARPrepaidTax23'; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true)); 
 
$_POST['selStatus[]'] = array(2,3);

$arrDateType= array(
    '1' => $obj->lang['transactionDate'], 
    '2' => $obj->lang['taxPeriod'],
);

$arrFilterInformation = array();

// ===== FOR EXPORT SECTION
$dataToExport = array();

/* data structure */
$arrTemplate = array();

$arrDataStructure = array();
$arrDataStructure['rowNumber'] = array('title'=>'#', 'align'=>'right', 'width'=>"40px", 'autoNumber' => true,"sortable" => false);
$arrDataStructure['code'] = array('title'=>ucwords($obj->lang['code']),  'width'=>"100px", 'dbfield' => 'code');
$arrDataStructure['date'] = array('title'=>ucwords($obj->lang['date']),'dbfield' => 'trdate', 'width'=>"120px",'format'=>'date');
$arrDataStructure['taxPeriod'] = array('title' =>ucwords($obj->lang['taxPeriod']),  'width'=>"100px", 'dbfield' => 'taxperiod', 'align' => 'center');
$arrDataStructure['customer'] = array('title'=>ucwords($obj->lang['customer']),'dbfield' => 'customername', 'width'=>"170px", 'mergeExcelCell' => 2 );
$arrDataStructure['warehouse'] = array('title'=>ucwords($obj->lang['warehouse']),'dbfield' => 'warehousename', 'width'=>"100px" );
//$arrDataStructure['total'] = array('title'=>ucwords($obj->lang['total']),'dbfield' => 'grandtotal', 'width'=>"100px" ,'format'=>'number','calculateTotal' => true);
$arrDataStructure['totalPayment'] = array('title'=>ucwords($obj->lang['paymentAmount']),'dbfield' => 'totalreceived', 'width'=>"130px" ,'format'=>'number','calculateTotal' => true);
$arrDataStructure['note'] = array('title'=>ucwords($obj->lang['note']),'dbfield' => 'trnotes','width'=>"300px");
$arrDataStructure['status'] = array('title'=>ucwords($obj->lang['status']),'dbfield' => 'statusname', 'width'=>"100px" );
		  
$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $obj->lang['prepaidTax23PaymentReport'];
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();

array_push($arrTemplate, $arrHeaderTemplate);

$arrDataDetailStructure = array();
$arrDataDetailStructure['arcode'] = array('title'=>ucwords($obj->lang['arCode']),  'dbfield' => 'arcode', 'width'=>'100px', 'format' => 'string' ); 
$arrDataDetailStructure['refCode'] = array('title'=>ucwords($obj->lang['refCode']),  'dbfield' => 'refcode', 'width'=>'100px', 'format' => 'string' ); 
$arrDataDetailStructure['refDate'] = array('title'=>ucwords($obj->lang['refDate']),'dbfield' => 'refdate', 'width'=>"120px",'format'=>'date');
$arrDataDetailStructure['amount'] = array('title'=>ucwords($obj->lang['amount']),  'dbfield' => 'amountar', 'width'=>"120px", 'format' => 'number' ,'calculateTotal' => true);
$arrDataDetailStructure['outstanding'] = array('title'=>ucwords($obj->lang['outstanding']),  'dbfield' => 'outstanding', 'width'=>"120px", 'format' => 'number' ,'calculateTotal' => true);
$arrDataDetailStructure['payment'] = array('title'=>ucwords($obj->lang['payment']),  'dbfield' => 'amount', 'width'=>"120px", 'format' => 'number' ,'calculateTotal' => true);

$arrDetailTemplate = array();
$arrDetailTemplate['reportWidth'] = "680px";
$arrDetailTemplate['dataStructure'] = $arrDataDetailStructure;
$arrDetailTemplate['total'] = array();

array_push($arrTemplate, $arrDetailTemplate);   

if (isset($_POST) && !empty($_POST['hidAction'])){   
	
	$criteria = '';

    if(isset($_POST) && !empty($_POST['code'])) {
		$criteria .= ' AND '.$obj->tableName.'.code LIKE ('.$class->oDbCon->paramString('%'.$_POST['code'].'%').')';
		array_push($arrFilterInformation,array("label" => 'Kode', 'filter' => $_POST['code']));
	}

    if(isset($_POST) && !empty($_POST['selDateType'])){
        
        if($_POST['selDateType'] == 1){
            $criteria .= ' and '.$obj->tableName.'.trdate between '.$class->oDbCon->paramDate( $_POST['trStartDate'],' / ').' AND '.$class->oDbCon->paramDate( $_POST['trEndDate'],' / '); 
            array_push($arrFilterInformation,array("label" => 'Tanggal', 'filter' => $_POST['trStartDate'] . ' - ' .$_POST['trEndDate'] ));
        }else if($_POST['selDateType'] == 2){
            $startPeriod = date('01 / m / Y',strtotime($_POST['startTaxPeriod']));  
            $endPeriod = date('01 / m / Y',strtotime($_POST['endTaxPeriod']));  
            $criteria .= ' and '.$obj->tableName.'.taxperiod between '.$class->oDbCon->paramDate($startPeriod,' / ').' AND '.$class->oDbCon->paramDate( $endPeriod,' / '); 
            array_push($arrFilterInformation,array("label" => 'Masa Pajak', 'filter' => $_POST['startTaxPeriod'] . ' - ' .$_POST['endTaxPeriod'] ));
        }
        
    }

    if(isset($_POST) && !empty($_POST['selCustomer'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selCustomer']));   
        
       	$criteria .= ' AND customerkey in('.$key.')';  

        $rsCriteria = $customer->searchData('','',true, ' and '.$customer->tableName.'.pkey in ('.$key.')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$statusName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => 'Pelangan', 'filter' => $statusName ));
        
	}
    
	if(isset($_POST) && !empty($_POST['selWarehouse'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selWarehouse']));   
        
       	$criteria .= ' AND warehousekey in('.$key.')';  

        $rsCriteria = $warehouse->searchData('','',true, ' and '.$warehouse->tableName.'.pkey in ('.$key.')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$warehouseName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => 'Gudang', 'filter' => $warehouseName ));
        
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
	 
		if (empty($rs)) 
			$tempreport .= '<tr class="report-row rewrite-row"><td colspan="'.count($arrHeaderTemplate['dataStructure']).'"></td></tr>';
		 
    for( $i=0;$i<count($rs);$i++) {   

        $rsDetail = $obj->getDetailById($rs[$i]['pkey']); 
		$rs[$i]['taxperiod'] = (!empty($rs[$i]['taxperiod'])) ? date('F Y', strtotime($rs[$i]['taxperiod'])) : '';  
            if (empty($rsDetail))
            continue;
        
        for ($j=0;$j<count($rsDetail);$j++){   
            $rsAR = $ar->getDataRowById($rsDetail[$j]['arkey']);
            $rsDetail[$j]['arcode'] =  $rsAR[0]['code'];
            $rsDetail[$j]['refcode'] =  $rsAR[0]['refcode'];   
            $rsDetail[$j]['refdate'] =  $rsAR[0]['trdate'];   
            $rsDetail[$j]['amountar'] =  $rsAR[0]['amount'];   
        }
        
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

	$_POST['startTaxPeriod'] = date('F Y',mktime(0, 0, 0, 1, 1, date('Y')));
    $_POST['endTaxPeriod'] = date('F Y'); 
}

$arrWarehouse = $class->convertForCombobox($warehouse->searchData($warehouse->tableName.'.statuskey',1,true,'','order by name asc'),'pkey','name');
$arrCustomer = $class->convertForCombobox($customer->searchData($customer->tableName.'.statuskey',2,true,'','order by name asc'),'pkey','name');
$arrStatus = $class->convertForCombobox($obj->getAllStatus(),'pkey','status');

$arrTwigVar['inputSelDateType'] =  $class->inputSelect('selDateType', $arrDateType);  
$arrTwigVar['inputCode'] =  $class->inputText('code');
$arrTwigVar['inputStartTaxPeriod'] = $class->inputMonth('startTaxPeriod', array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputEndTaxPeriod'] = $class->inputMonth('endTaxPeriod', array('etc' => 'style="text-align:center"'));   
//$arrTwigVar['inputHidCustomerKey'] = $class->inputHidden('hidCustomerKey');
//$arrTwigVar['inputCustomerName'] =  $class->inputText('customerName');
$arrTwigVar['inputSelCustomer'] =  $class->inputSelect('selCustomer[]', $arrCustomer, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputStartDate'] = $class->inputDate('trStartDate',array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputEndDate'] = $class->inputDate('trEndDate',array('etc' => 'style="text-align:center"'));  
$arrTwigVar['inputSelStatus'] =  $class->inputSelect('selStatus[]', $arrStatus, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox')); 
$arrTwigVar['inputSelWarehouse'] =  $class->inputSelect('selWarehouse[]', $arrWarehouse, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate;       
echo $twig->render('reportARPrepaidTax23Payment.html', $arrTwigVar);   
?>

