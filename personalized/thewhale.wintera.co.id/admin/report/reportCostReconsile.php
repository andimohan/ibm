<?php	  
 
$obj= $costReconsile;
$securityObject = 'ReportCostReconsile'; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true)); 
  
$_POST['selStatus[]'] = array(2,3);

$isDetail = (isset($_POST['isDetail']) && !empty($_POST['isDetail'])) ? true : false;

$arrFilterInformation = array();    


// ===== FOR EXPORT SECTION
$dataToExport = array();

/* data structure */
$arrTemplate = array();

$arrDataStructure = array();
$arrDataStructure['rowNumber'] = array('title'=>'#', 'align'=>'right', 'width'=>"40px", 'autoNumber' => true,"sortable" => false);
$arrDataStructure['code'] = array('title'=>ucwords($obj->lang['code']),  'width'=>"100px", 'dbfield' => 'code');
$arrDataStructure['date'] = array('title'=>ucwords($obj->lang['date']),'dbfield' => 'trdate', 'width'=>"120px",'format'=>'date');
$arrDataStructure['invoicecode'] = array('title'=>ucwords($obj->lang['invoiceCode']),  'width'=>"150px", 'dbfield' => 'invoicecode'); 
$arrDataStructure['jocode'] = array('title'=>ucwords($obj->lang['JOCode']),  'width'=>"150px", 'dbfield' => 'jocode');  
$arrDataStructure['refcode'] = array('title'=>ucwords($obj->lang['poReference']),  'width'=>"150px", 'dbfield' => 'refpocode'); 
$arrDataStructure['warehouse'] = array('title'=>ucwords($obj->lang['warehouse']),'dbfield' => 'warehousename', 'width'=>"100px" );
$arrDataStructure['currencyshort'] = array('title'=>ucwords($obj->lang['currencyShort']),'dbfield' => 'currencyname', 'width'=>"50px" );
$arrDataStructure['ammount'] = array('title'=>ucwords($obj->lang['amount']),'dbfield' => 'grandtotal', 'width'=>"100px" ,'format'=>'autodecimal');
$arrDataStructure['note'] = array('title'=>ucwords($obj->lang['note']),'dbfield' => 'trdesc','width'=>"300px");
$arrDataStructure['status'] = array('title'=>ucwords($obj->lang['status']),'dbfield' => 'statusname', 'width'=>"100px" );
		  
$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $obj->lang['costReconsiliationReport'];
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();

array_push($arrTemplate, $arrHeaderTemplate);


if ($isDetail){ 
    // detail ...
    $arrDataDetailStructure = array(); 
    $arrDataDetailStructure['headercode'] = array('title'=>ucwords($obj->lang['reference']),  'dbfield' => 'headercode', 'width'=>"100px" );  
    $arrDataDetailStructure['headerdate'] = array('title'=>ucwords($obj->lang['date']),  'dbfield' => 'headerdate', 'width'=>"100px",'format'=>'date');
    $arrDataDetailStructure['invoiceCode'] = array('title'=>ucwords($obj->lang['invoice']),  'dbfield' => 'headerinvoicecode', 'width'=>"150px" );   
    $arrDataDetailStructure['JOCode'] = array('title'=>ucwords($obj->lang['JOCode']),  'dbfield' => 'headerjocode', 'width'=>"150px" );   
    $arrDataDetailStructure['reference'] = array('title'=>ucwords($obj->lang['reference']),  'dbfield' => 'refcode', 'width'=>"150px" );   
    $arrDataDetailStructure['service'] = array('title'=>ucwords($obj->lang['service']),  'dbfield' => 'servicename', 'width'=>"200px" );  
    $arrDataDetailStructure['amount'] = array('title'=>ucwords($obj->lang['amount']),'dbfield' => 'reconcilamount', 'width'=>"100px",'format'=>'number','calculateTotal' => true);
    $arrDataDetailStructure['outstanding'] = array('title'=>ucwords($obj->lang['outstanding']),'dbfield' => 'outstanding', 'width'=>"100px",'format'=>'number','calculateTotal' => true);
    $arrDataDetailStructure['reconsiliation'] = array('title'=>ucwords($obj->lang['reconsiliation']),'dbfield' => 'amount', 'width'=>"100px",'format'=>'number','calculateTotal' => true);

    $arrDetailTemplate = array(); 
    $arrDetailTemplate['dataStructure'] = $arrDataDetailStructure;
    $arrDetailTemplate['total'] = array();

    array_push($arrTemplate, $arrDetailTemplate); 
}


if (isset($_POST) && !empty($_POST['hidAction'])){   
	
	$criteria = '';
    $criteriaArr = array();

    array_push($criteriaArr, array('postVariable' => array('trStartDate', 'trEndDate'), 
								   'fieldName' => $obj->tableName.'.trdate', 
								   'label' =>  $obj->lang['period'], 
								   'type' => 'daterange'));

    array_push($criteriaArr, array('postVariable' => 'code', 
								   'fieldName' => $obj->tableName.'.code', 
								   'label' => $obj->lang['code']));

    array_push($criteriaArr, array('postVariable' => 'invoiceCode', 
								   'fieldName' => $EMKLOrderInvoice->tableName.'.code', 
								   'label' => $obj->lang['invoiceCode']));

    array_push($criteriaArr, array('postVariable' => 'soCode', 
								   'fieldName' => $EMKLOrderInvoice->tableName.'.salesordercodecache', 
								   'label' => $obj->lang['soCode']));

    array_push($criteriaArr, array('postVariable' => 'selWarehouse', 
								   'fieldName' => $obj->tableName.'.warehousekey', 
								   'label' => $obj->lang['warehouse'], 
								   'useArrayKey' => array('obj' => $warehouse) ));

    array_push($criteriaArr, array('postVariable' => 'selCurrency', 
								   'fieldName' => $obj->tableName.'.currencykey', 
								   'label' => $obj->lang['currency'], 
								   'useArrayKey' => array('obj' => $currency) ));
    
    array_push($criteriaArr, array('postVariable' => 'selStatus',
								   'type' => 'status'));

    $obj->createReportCriteria($criteria,$arrFilterInformation,$criteriaArr);
    
    $orderBy = (!empty($_POST['hidOrderBy'])) ? $obj->oDbCon->paramOrder($_POST['hidOrderBy']) : 'pkey'; // order by harus dr kolom yg terdaftar saja
    $orderType = (isset($_POST['hidOrderType']) && !empty($_POST['hidOrderType']) && $_POST['hidOrderType'] == 1) ? 'desc' : 'asc';

	 
	$order = 'order by '.$orderBy.' ' .$orderType; 
	 
	$rs = $obj->searchData('','',true,$criteria,$order);
    
    $totalCurr = count(array_unique(array_column($rs,'currencykey')));     
    $calculateTotal = ($totalCurr <= 1) ? true : false;    
    
    $rsDetailCol = $obj->getDetailCollections($rs,'refkey');    
    $rsReIndex = array_column($rs, null, 'pkey');
    $tempreport = '';  
	 
    foreach ($rsReIndex as $index) {
        $pkey = $index['pkey'];
        $arrPO = array(); 
        $arrRefPO = array(); 
        foreach($rsDetailCol[$pkey] as $indexDetail) {
                array_push($arrPO, $indexDetail['pecode']);
                array_push($arrRefPO, $indexDetail['refcode']);
            }
        $arrPO = implode(", ",$arrPO);
        $arrRefPO = implode(", ",$arrRefPO);
        $rsReIndex[$pkey]['pecode'] = $arrPO;
        $rsReIndex[$pkey]['refcode'] = $arrRefPO;
    }

    for( $i=0;$i<count($rs);$i++) {   
        $pkey = $rs[$i]['pkey'];
        $arrTemplate[0]['dataStructure']['ammount']['calculateTotal'] = $calculateTotal;
        
        
        $return = $obj->formatReportRows(array('data' => $rs[$i]),$arrTemplate); 

        $rs[$i]['pecode'] = $rsReIndex[$pkey]['pecode'];
        $rs[$i]['refcode'] = $rsReIndex[$pkey]['refcode'];

        $rsDetail = $rsDetailCol[$pkey];  
        
        $totalDetail = count($rsDetail);
        for( $j=0;$j<$totalDetail;$j++) {  
            $rsDetail[$j]['headercode'] = $rs[$i]['code'];
            $rsDetail[$j]['headerdate'] = $rs[$i]['trdate'];
            $rsDetail[$j]['headerinvoicecode'] = $rs[$i]['invoicecode'];
            $rsDetail[$j]['headerjocode'] = $rs[$i]['jocode'];
        }
        
        if($isDetail){ 
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

$arrStatus = $class->convertForCombobox($obj->getAllStatus(),'pkey','status');
$arrWarehouse = $class->convertForCombobox($warehouse->searchData($warehouse->tableName.'.statuskey',1,true,'','order by name asc'),'pkey','name');
$arrCurrency = $class->convertForCombobox($currency->searchData($currency->tableName.'.statuskey',1,true,'','order by name asc'),'pkey','name');

$arrTwigVar['inputCode'] =  $class->inputText('code');
$arrTwigVar['inputInvoiceCode'] =  $class->inputText('invoiceCode'); 
$arrTwigVar['inputSoCode'] =  $class->inputText('soCode'); 
$arrTwigVar['inputStartDate'] = $class->inputDate('trStartDate',array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputEndDate'] = $class->inputDate('trEndDate',array('etc' => 'style="text-align:center"'));  
$arrTwigVar['inputSelStatus'] =  $class->inputSelect('selStatus[]', $arrStatus, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox')); 
$arrTwigVar['inputIsDetail'] =  $class->inputCheckBox('isDetail');
$arrTwigVar['inputSelWarehouse'] =  $class->inputSelect('selWarehouse[]', $arrWarehouse, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputSelCurrency'] =  $class->inputSelect('selCurrency[]', $arrCurrency, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
      

$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate; 
       
echo $twig->render('reportCostReconsile.html', $arrTwigVar);   
?>
