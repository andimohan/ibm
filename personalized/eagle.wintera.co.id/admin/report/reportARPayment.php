<?php	  
$obj= $arPayment;
$ar = $obj->getARObj();
$securityObject = 'reportARPayment'; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true)); 
 
$_POST['selStatus[]'] = array(2,3);

$arrFilterInformation = array();
$detailCriteria = '';

// ===== FOR EXPORT SECTION
$dataToExport = array();

/* data structure */
$arrTemplate = array();
$isShowDetail = (isset($_POST['isShowDetail']) && !empty($_POST['isShowDetail'])) ? true : false;

$arrDataStructure = array();
$arrDataStructure['rowNumber'] = array('title'=>'#', 'align'=>'right', 'width'=>"40px", 'autoNumber' => true,"sortable" => false);
$arrDataStructure['code'] = array('title'=>ucwords($obj->lang['code']),  'width'=>"130px", 'dbfield' => 'code');
$arrDataStructure['date'] = array('title'=>ucwords($obj->lang['date']),'dbfield' => 'trdate', 'width'=>"120px",'format'=>'date');
$arrDataStructure['customer'] = array('title'=>ucwords($obj->lang['customer']),'dbfield' => 'customername', 'width'=>"170px", 'mergeExcelCell' => 2 );
$arrDataStructure['warehouse'] = array('title'=>ucwords($obj->lang['warehouse']),'dbfield' => 'warehousename', 'width'=>"100px" );
$arrDataStructure['rate'] = array('title'=>ucwords($obj->lang['rate']),'dbfield' => 'rate', 'width'=>"130px" ,'format'=>'number');
$arrDataStructure['currency'] = array('title'=>ucwords($obj->lang['curr']),'dbfield' => 'currencyname', 'width'=>"60px", "align"=>"center");
$arrDataStructure['paidtotal'] = array('title'=>ucwords($obj->lang['payingOffAmount']),'dbfield' => 'totalreceived', 'width'=>"130px" ,'format'=>'number','calculateTotal' => true);
$arrDataStructure['totaldiscount'] = array('title'=>ucwords($obj->lang['discount']),  'dbfield' => 'totaldiscount', 'width'=>"100px", 'format' => 'number' ,'calculateTotal' => true);
$arrDataStructure['downpayment'] = array('title'=>ucwords($obj->lang['downpayment']),'dbfield' => 'totaldownpayment', 'width'=>"130px" ,'format'=>'number','calculateTotal' => true);
$arrDataStructure['cost'] = array('title'=>ucwords($obj->lang['cost']),'dbfield' => 'totalcost', 'width'=>"130px" ,'format'=>'number','calculateTotal' => true);
$arrDataStructure['prepaidTax23'] = array('title'=>ucwords($obj->lang['tax23']),'dbfield' => 'prepaidtax23', 'width'=>"100px" ,'format'=>'number','calculateTotal' => true);
$arrDataStructure['totalPayment'] = array('title'=>ucwords($obj->lang['paymentAmount']),'dbfield' => 'totalpayment', 'width'=>"130px" ,'format'=>'number','calculateTotal' => true);
$arrDataStructure['paymentMethod'] = array('title'=>ucwords($obj->lang['paymentMethod']),'dbfield' => 'paymentmethodname','width'=>"200px","sortable" => false);
$arrDataStructure['note'] = array('title'=>ucwords($obj->lang['note']),'dbfield' => 'trnotes','width'=>"300px");
$arrDataStructure['status'] = array('title'=>ucwords($obj->lang['status']),'dbfield' => 'statusname', 'width'=>"100px" );
		  
$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $obj->lang['accountsReceivablePaymentReport'];
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();

array_push($arrTemplate, $arrHeaderTemplate);
if ($isShowDetail){ 
// detail ...
$arrDataDetailStructure = array();
$arrDataDetailStructure['arcode'] = array('title'=>ucwords($obj->lang['arCode']),  'dbfield' => 'arcode', 'width'=>'100px', 'format' => 'string' ); 
$arrDataDetailStructure['refCode'] = array('title'=>ucwords($obj->lang['refCode']),  'dbfield' => 'refcode', 'width'=>'100px', 'format' => 'string' ); 
$arrDataDetailStructure['refDate'] = array('title'=>ucwords($obj->lang['refDate']),'dbfield' => 'refdate', 'width'=>"120px",'format'=>'date');
$arrDataDetailStructure['rate'] = array('title'=>ucwords($obj->lang['rate']),  'dbfield' => 'rate', 'width'=>"120px", 'format' => 'number');
$arrDataDetailStructure['currency'] = array('title'=>ucwords($obj->lang['curr']),'dbfield' => 'currencyname', 'width'=>"60px", "align"=>"center");    
$arrDataDetailStructure['amount'] = array('title'=>ucwords($obj->lang['amount']),  'dbfield' => 'amountar', 'width'=>"120px", 'format' => 'number' ,'calculateTotal' => true);
$arrDataDetailStructure['outstanding'] = array('title'=>ucwords($obj->lang['outstanding']),  'dbfield' => 'outstanding', 'width'=>"120px", 'format' => 'number' ,'calculateTotal' => true);
$arrDataDetailStructure['discount'] = array('title'=>ucwords($obj->lang['discount']),  'dbfield' => 'discount', 'width'=>"100px", 'format' => 'number' ,'calculateTotal' => true);
$arrDataDetailStructure['payment'] = array('title'=>ucwords($obj->lang['payingSettlement']),  'dbfield' => 'paymentamount', 'width'=>"120px", 'format' => 'number' ,'calculateTotal' => true);
$arrDataDetailStructure['pph23'] = array('title'=>ucwords($obj->lang['tax23']),  'dbfield' => 'taxamount', 'width'=>"120px", 'format' => 'number' ,'calculateTotal' => true);

$arrDetailTemplate = array();
$arrDetailTemplate['reportWidth'] = "680px";
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
	if(isset($_POST) && !empty($_POST['trStartDate'])){
		$criteria .= ' and '.$obj->tableName.'.trdate between '.$class->oDbCon->paramDate( $_POST['trStartDate'],' / ').' AND '.$class->oDbCon->paramDate( $_POST['trEndDate'],' / '); 
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
	    array_push($arrFilterInformation,array("label" => 'Pelangan', 'filter' => $statusName ));
        
	}
    
	if(isset($_POST) && !empty($_POST['selWarehouse'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selWarehouse']));   
        
       	$criteria .= ' AND '.$obj->tableName.'.warehousekey in('.$key.')';  

        $rsCriteria = $warehouse->searchData('','',true, ' and '.$warehouse->tableName.'.pkey in ('.$key.')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$warehouseName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => 'Gudang', 'filter' => $warehouseName ));
        
	}		
    
    if(isset($_POST) && !empty($_POST['selCurrency'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selCurrency']));   
        
       	$criteria .= ' AND currencykey in('.$key.')';  

        $rsCriteria = $currency->searchData('','',true, ' and '.$currency->tableName.'.pkey in ('.$key.')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$statusName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => 'Mata Uang', 'filter' => $statusName ));
        
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
	$arrCurrencyCalculate = array_unique(array_column($rs,'currencykey'));
	$arrUnsetTotal = array('paidtotal','totaldiscount','downpayment','cost','prepaidTax23','totalPayment');
	if(count($arrCurrencyCalculate)>1){
		foreach($arrUnsetTotal as $row)
		unset($arrTemplate[0]['dataStructure'][$row]['calculateTotal']);
	}
    $tempreport = '';  

    if (empty($rs)) 
        $tempreport .= '<tr class="report-row rewrite-row"><td colspan="'.count($arrHeaderTemplate['dataStructure']).'"></td></tr>';
	
    $rsDetailCol = ($isShowDetail) ? $obj->getDetailCollections($rs,'refkey',$detailCriteria) : array();
    
    // currency master
    $rsCurrency = $currency->searchData();
    $rsCurrency = array_column($rsCurrency,null,'pkey');
    
    // payment method 
    $rsPaymentCol =  $obj->getPaymentDetailCollections($rs,'refkey', ' and '.$obj->tablePayment.'.refkey in ('.$class->oDbCon->paramString(array_column($rs,'pkey'),',').')' );
     
    // ambil semua detail AR dulu
    $arrDetailARKey = array(); 
    foreach($rsDetailCol as $detailColRow) 
        $arrDetailARKey = array_merge($arrDetailARKey, array_column($detailColRow,'arkey')); 
      
    $arrDetailARKey = array_values(array_unique($arrDetailARKey));
        
    $rsARCol = $ar->searchDataRow( array($ar->tableName.'.pkey', 
                                      $ar->tableName.'.code', 
                                      $ar->tableName.'.refcode', 
                                      $ar->tableName.'.trdate', 
                                      $ar->tableName.'.amount',  
                                      $ar->tableName.'.rate',  
                                      $ar->tableName.'.currencykey'), 
                                   ' and ' . $ar->tableName.'.pkey in ('.$class->oDbCon->paramString($arrDetailARKey,',').')' 
                              ); 
    $rsARCol = array_column($rsARCol,null,'pkey'); 
    
    $totalRs = count($rs);
    for( $i=0;$i<$totalRs;$i++) {   
        
        $pkey = $rs[$i]['pkey'];
         
        // paymentmethod
        $tempPaymentMethod = $rsPaymentCol[$pkey];  
        $rs[$i]['paymentmethodname'] = implode('<br>',array_column($tempPaymentMethod,'paymentmethodname'));
        
        if($isShowDetail){ 
            if (!isset($rsDetailCol[$pkey]))  continue;
                $rsDetail = $rsDetailCol[$pkey]; 

            $totalDetail = count($rsDetail);
            for ($j=0;$j<$totalDetail;$j++){   
                
                $rsAR = $rsARCol[$rsDetail[$j]['arkey']];
                
                $rsDetail[$j]['arcode'] =  $rsAR['code'];
                $rsDetail[$j]['refcode'] =  $rsAR['refcode'];   
                $rsDetail[$j]['refdate'] =  $rsAR['trdate'];   
                $rsDetail[$j]['amountar'] =  $rsAR['amount'];   
                $rsDetail[$j]['rate'] =  $rsAR['rate'];   
                $rsDetail[$j]['currencyname'] =  $rsCurrency[$rsAR['currencykey']]['name']; 
                $rsDetail[$j]['paymentamount'] =  $rsDetail[$j]['amount'] - $rsDetail[$j]['taxamount']; 
            }

            // has detail
            $rs[$i]['_detail_'] = array('arrTemplate'=>$arrDetailTemplate,'data' => $rsDetail);

        }
        
        
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

$arrWarehouse = $class->convertForCombobox($warehouse->searchData($warehouse->tableName.'.statuskey',1,true,'','order by name asc'),'pkey','name');
$arrCustomer = $class->convertForCombobox($customer->searchData($customer->tableName.'.statuskey',2,true,'','order by name asc'),'pkey','name');
$arrCurrency = $class->convertForCombobox($currency->searchData($currency->tableName.'.statuskey',1,true,'','order by name asc'),'pkey','name');
$arrStatus = $class->convertForCombobox($obj->getAllStatus(),'pkey','status');

$arrTwigVar['inputCode'] =  $class->inputText('code');
//$arrTwigVar['inputHidCustomerKey'] = $class->inputHidden('hidCustomerKey');
//$arrTwigVar['inputCustomerName'] =  $class->inputText('customerName');
$arrTwigVar['inputSelCustomer'] =  $class->inputSelect('selCustomer[]', $arrCustomer, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputSelCurrency'] =  $class->inputSelect('selCurrency[]', $arrCurrency, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox')); 
$arrTwigVar['inputStartDate'] = $class->inputDate('trStartDate',array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputEndDate'] = $class->inputDate('trEndDate',array('etc' => 'style="text-align:center"'));  
$arrTwigVar['inputSelStatus'] =  $class->inputSelect('selStatus[]', $arrStatus, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox')); 
$arrTwigVar['inputSelWarehouse'] =  $class->inputSelect('selWarehouse[]', $arrWarehouse, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputIsShowDetail'] =  $class->inputCheckBox('isShowDetail');
$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate;       
echo $twig->render('reportARPayment.html', $arrTwigVar);   
?> 
