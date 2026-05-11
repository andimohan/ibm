<?php	 
include '../../_config.php';  
include '../../_include-v2.php'; 

includeClass('ARPrepaidTax23.class.php');
$arPrepaidTax23 = createObjAndAddToCol(new ARPrepaidTax23());
$arPayment = createObjAndAddToCol(new ARPayment());
$customer = createObjAndAddToCol(new Customer());
$warehouse = createObjAndAddToCol(new Warehouse());

include '_global.php';

$obj= $arPrepaidTax23;
$arPayment = $obj->getPaymentObj();
$securityObject = 'reportARPrepaidTax23'; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true)); 

if (!isset($_POST['trStartDate']) || empty($_POST['trStartDate'])){ 
	$_POST['trStartDate'] = date('d / m / Y');
	$_POST['trEndDate'] = date('d / m / Y');
} 

$_POST['selStatus[]'] = array(1,2,3);

$arrFilterInformation = array();     

// ===== FOR EXPORT SECTION
$dataToExport = array();

/* data structure */
$arrTemplate = array();
$isShowDetail = (isset($_POST['isShowDetail']) && $_POST['isShowDetail'] == 1) ? true : false;
$isDetailInHeader = (isset($_POST['isDetailInHeader']) && $_POST['isDetailInHeader'] == 1) ? true : false;

$arrDataStructure = array();
$arrDataStructure['rowNumber'] = array('title'=>'#', 'align'=>'right', 'width'=>"40px", 'autoNumber' => true,"sortable" => false);
$arrDataStructure['code'] = array('title'=>ucwords($obj->lang['code']),  'width'=>"100px", 'dbfield' => 'code');
$arrDataStructure['trdate'] = array('title'=>ucwords($obj->lang['date']),'dbfield' => 'trdate', 'width'=>"100px",'format'=>'date');
$arrDataStructure['refcode'] = array('title'=>ucwords($obj->lang['invoiceCode']),  'width'=>"150px", 'dbfield' => 'refcode');
$arrDataStructure['refdate'] = array('title'=>ucwords($obj->lang['invoiceDate']),  'width'=>"100px", 'dbfield' => 'refdate','format'=>'date');
//$arrDataStructure['duedate'] = array('title'=>ucwords($obj->lang['duedate']),'dbfield' => 'duedate', 'width'=>"100px",'format'=>'date');
//$arrDataStructure['datediff'] = array('title'=>ucwords($obj->lang['aging']),'dbfield' => 'datediff', 'width'=>"70px",'format'=>'number');
if($isDetailInHeader){ 
	//$arrDataStructure['receiptCode'] = array('title'=>ucwords($obj->lang['receiptCode']),'dbfield' => 'receiptcode', 'width'=>"150px","sortable" => false, 'textColor' => 'F58025');
	$arrDataStructure['prepaidReceiptCode'] = array('title'=>ucwords($obj->lang['withholdingNo']),'dbfield' => 'prepaidreceiptcode', 'width'=>"150px","sortable" => false, 'textColor' => 'F58025');
	$arrDataStructure['prepaidReceiptNTPN'] = array('title'=>ucwords($obj->lang['ntpn']),'dbfield' => 'prepaidreceiptntpn', 'width'=>"150px","sortable" => false, 'textColor' => 'F58025');
	$arrDataStructure['prepaidReceiptDate'] = array('title'=>ucwords($obj->lang['prepaidTaxReceiptDate']),'dbfield' => 'prepaidreceiptdate', 'width'=>"100px","sortable" => false, 'textColor' => 'F58025');
	$arrDataStructure['prepaidReceiptAmount'] = array('title'=>ucwords($obj->lang['prepaidTaxReceiptAmount']),'dbfield' => 'prepaidreceiptamount', 'width'=>"100px","sortable" => false, 'align' =>'right', 'textColor' => 'F58025');
}

$arrDataStructure['warehouse'] = array('title'=>ucwords($obj->lang['warehouse']),'dbfield' => 'warehousename', 'width'=>"150px");
$arrDataStructure['customer'] = array('title'=>ucwords($obj->lang['customer']),'dbfield' => 'customername', 'width'=>"200px");
$arrDataStructure['ammount'] = array('title'=>ucwords($obj->lang['amount']),'dbfield' => 'amount', 'width'=>"100px" ,'format'=>'number','calculateTotal' => true);
$arrDataStructure['outstanding'] = array('title'=>ucwords($obj->lang['outstanding']),'dbfield' => 'outstanding', 'width'=>"100px" ,'format'=>'number','calculateTotal' => true);
$arrDataStructure['note'] = array('title'=>ucwords($obj->lang['note']),'dbfield' => 'trdesc','width'=>"300px" );
$arrDataStructure['status'] = array('title'=>ucwords($obj->lang['status']),'dbfield' => 'statusname', 'width'=>"100px");
		  
$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $obj->lang['prepaidTax23Report']; 
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();

array_push($arrTemplate, $arrHeaderTemplate);

if($isShowDetail && !$isDetailInHeader){
	$arrDataDetailStructure = array();
	$arrDataDetailStructure['arcode'] = array('title'=>ucwords($obj->lang['prepaidTaxReceiptCode']),  'dbfield' => 'code', 'width'=>'140px', 'format' => 'string' ); 
	$arrDataDetailStructure['arpaymentdate'] = array('title'=>ucwords($obj->lang['prepaidTaxReceiptDate']),  'dbfield' => 'trdate', 'format' => 'date', 'width'=>'100px'); 
	$arrDataDetailStructure['amount'] = array('title'=>ucwords($obj->lang['prepaidTaxReceiptAmount']),  'dbfield' => 'amount', 'width'=>"100px", 'format' => 'number','calculateTotal' => true);

	$arrDetailTemplate = array();
	$arrDetailTemplate['reportWidth'] = "400px";
	$arrDetailTemplate['dataStructure'] = $arrDataDetailStructure;
	$arrDetailTemplate['total'] = array();

	array_push($arrTemplate, $arrDetailTemplate);
}

$arrStatus = $class->convertForCombobox($obj->getAllStatus(),'pkey','status');
$arrWarehouse = $class->convertForCombobox($warehouse->searchData($warehouse->tableName.'.statuskey',1,true,'','order by name asc'),'pkey','name');
$arrCustomer = $class->convertForCombobox($customer->searchData($customer->tableName.'.statuskey',2,true,'','order by name asc'),'pkey','name');

$arrTwigVar['inputCode'] =  $class->inputText('code');
//$arrTwigVar['inputHidCustomerKey'] = $class->inputHidden('hidCustomerKey');
//$arrTwigVar['inputCustomerName'] =  $class->inputText('customerName');
$arrTwigVar['inputIsDetailInHeader'] =  $class->inputCheckBox('isDetailInHeader');
$arrTwigVar['inputShowDetail'] =  $class->inputCheckBox('isShowDetail');
$arrTwigVar['inputSelCustomer'] =  $class->inputSelect('selCustomer[]', $arrCustomer, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputRefCode'] =  $class->inputText('refCode');
$arrTwigVar['inputStartDate'] = $class->inputDate('trStartDate',array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputEndDate'] = $class->inputDate('trEndDate',array('etc' => 'style="text-align:center"'));  
$arrTwigVar['inputSelStatus'] =  $class->inputSelect('selStatus[]', $arrStatus, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox')); 
$arrTwigVar['inputChkDueDate'] =  $class->inputCheckBox('chkDueDate',array('overwritePost' => false, 'value' => 0, 'class' => 'no-class'));  
$arrTwigVar['inputSelWarehouse'] =  $class->inputSelect('selWarehouse[]', $arrWarehouse, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate; 

if (isset($_POST) && !empty($_POST['hidAction'])){   
	
	$criteria = '';
	
	if(isset($_POST) && !empty($_POST['code'])) {
		$criteria .= ' AND '.$obj->tableName.'.code LIKE ('.$class->oDbCon->paramString('%'.$_POST['code'].'%').')';
		array_push($arrFilterInformation,array("label" => 'Kode', 'filter' => $_POST['code']));
	}
    
    if(isset($_POST) && !empty($_POST['refCode'])) {
		$criteria .= ' AND '.$obj->tableName.'.refcode LIKE ('.$class->oDbCon->paramString('%'.$_POST['refCode'].'%').')';
		array_push($arrFilterInformation,array("label" => 'Kode Ref', 'filter' => $_POST['refCode']));
	}
    
	if(isset($_POST) && !empty($_POST['trStartDate'])){
		$criteria .= ' AND '.$obj->tableName.'.trdate between '.$class->oDbCon->paramDate( $_POST['trStartDate'],' / ').' AND '.$class->oDbCon->paramDate( $_POST['trEndDate'],' / ','Y-m-d 23:59'); 
		array_push($arrFilterInformation,array("label" => 'Tanggal', 'filter' => $_POST['trStartDate'] . ' - ' .$_POST['trEndDate'] ));
	} 

	/*if(isset($_POST) && !empty($_POST['customerName'])) {
		$criteria .= ' AND '.$obj->tableCustomer.'.name LIKE ('.$class->oDbCon->paramString('%'.$_POST['customerName'].'%').')';
		array_push($arrFilterInformation,array("label" => 'Nama Pelanggan', 'filter' =>  $_POST['customerName']));
	}*/
    
    if(isset($_POST) && !empty($_POST['selCustomer'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selCustomer']));   
        
       	$criteria .= ' AND customerkey in('.$key.')';  

        $rsCriteria = $customer->searchData('','',true, ' and '.$customer->tableName.'.pkey in ('.$key.')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$statusName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" =>  $obj->lang['customer'], 'filter' => $statusName ));
        
	}
    
    /*if(isset($_POST) && !empty($_POST['chkDueDate'])){ 
        $criteria .= ' having datediff > 0';
        array_push($arrFilterInformation,array("label" => 'Aging', 'filter' => 'Tampilkan hanya yang jatuh tempo'));
    }*/
    
        
	if(isset($_POST) && !empty($_POST['selWarehouse'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selWarehouse']));   
        
       	$criteria .= ' AND warehousekey in('.$key.')';  

        $rsCriteria = $warehouse->searchData('','',true, ' and '.$warehouse->tableName.'.pkey in ('.$key.')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$statusName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => 'Gudang', 'filter' => $statusName ));
        
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
	$arrPayment = array();
	if($isShowDetail || $isDetailInHeader) 
		$arrPayment = $arPayment->getDetailPaymentCollections($rs,'arkey');
	 
	$tempreport = '';  
	  
    $totalRs = count($rs);
    for( $i=0;$i<$totalRs;$i++) {   
        $arrHeaderStyle = array();

        // kalo ad detail
        $rsPayment = $arrPayment[$rs[$i]['pkey']]; 
        $totalRsPayment = count($rsPayment);
        if($isShowDetail && !$isDetailInHeader){
 
            $rsDetail = array();
            for ($j=0;$j<$totalRsPayment;$j++){
 
                $arrTemp = array();
                $arrTemp['code'] = $rsPayment[$j]['code'];
                $arrTemp['refcode'] = $rsPayment[$j]['refcode'];
                $arrTemp['trdate'] = $rsPayment[$j]['trdate'];
                $arrTemp['amount'] = $rsPayment[$j]['amount']; 
                array_push($rsDetail, $arrTemp);

            } 
            // has detail
            $rs[$i]['_detail_'] = array('arrTemplate'=>$arrDetailTemplate,'data' => $rsDetail);  
            
        }else if($isDetailInHeader){ 
           // $rsDetailCode = array();
            $rsDetailPrepaidReceiptCode = array();
            $rsDetailNTPN = array();
            $rsDetailDate = array();
            $rsDetailAmount = array();
             for ($j=0;$j<$totalRsPayment;$j++){  
                //array_push($rsDetailCode, $rsPayment[$j]['code']);
                array_push($rsDetailPrepaidReceiptCode, $rsPayment[$j]['refcode']);
                array_push($rsDetailNTPN, $rsPayment[$j]['ntpn']);
                array_push($rsDetailDate, $obj->formatDBDate($rsPayment[$j]['trdate']));
                array_push($rsDetailAmount, $obj->formatNumber($rsPayment[$j]['amount']));
             }
            
             //$rs[$i]['receiptcode'] = implode('<br>',$rsDetailCode);
             $rs[$i]['prepaidreceiptcode'] = implode('<br>',$rsDetailPrepaidReceiptCode);
             $rs[$i]['prepaidreceiptntpn'] = implode('<br>',$rsDetailNTPN);
             $rs[$i]['prepaidreceiptdate'] = implode('<br>',$rsDetailDate);
             $rs[$i]['prepaidreceiptamount'] = implode('<br>',$rsDetailAmount);
        } 

        $return = $obj->formatReportRows(array('data' => $rs[$i], 'style' => $arrHeaderStyle),$arrTemplate);  

        // ===== FOR EXPORT SECTION 
        array_push($dataToExport, $return['data']);  
        // ===== END FOR EXPORT SECTION

        $tempreport .= $return['html'];  
        $arrTemplate[0]['total'] = $obj->arraySum($arrTemplate[0]['total'], $return['subtotal'][0]);

    }
	
	$tableHeader = $twig->render('template-header.html', $arrTwigVar);
    $obj->generateReport($_POST, $tempreport, $arrTemplate,$dataToExport,$arrFilterInformation,$tableHeader);
    
}

  
echo $twig->render('reportARPrepaidTax23.html', $arrTwigVar);   
?>
