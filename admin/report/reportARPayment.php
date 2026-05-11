<?php	
include '../../_config.php'; 
include '../../_include-v2.php';

includeClass('ARPayment.class.php');
$arPayment = createObjAndAddToCol( new ARPayment()); 
$ar = createObjAndAddToCol( new AR()); 

$customer = createObjAndAddToCol( new Customer()); 
$warehouse = createObjAndAddToCol( new Warehouse()); 
$currency = createObjAndAddToCol( new Currency());
$employee = createObjAndAddToCol( new Employee());

include '_global.php';  

$obj= $arPayment;
$ar = $obj->getARObj();
$securityObject = 'reportARPayment'; // the value of security object is manually inserted to handle 
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
$arrDataStructure['rowNumber'] = array('title'=>'#', 'align'=>'right', 'width'=>"40px", 'autoNumber' => true,"sortable" => false);
$arrDataStructure['code'] = array('title'=>ucwords($obj->lang['code']),  'width'=>"130px", 'dbfield' => 'code');
$arrDataStructure['date'] = array('title'=>ucwords($obj->lang['date']),'dbfield' => 'trdate', 'width'=>"120px",'format'=>'date');
$arrDataStructure['customer'] = array('title'=>ucwords($obj->lang['customer']),'dbfield' => 'customername', 'width'=>"170px", 'mergeExcelCell' => 2 );

if (PLAN_TYPE['categorykey'] == COMPANY_TYPE['retail'])
	$arrDataStructure['salesman'] = array('title'=>ucwords($obj->lang['salesman']),'dbfield' => 'salesname', 'width'=>"150px","sortable" => false );

$arrDataStructure['warehouse'] = array('title'=>ucwords($obj->lang['warehouse']),'dbfield' => 'warehousename', 'width'=>"100px" );
$arrDataStructure['rate'] = array('title'=>ucwords($obj->lang['currencyRate']),'dbfield' => 'rate', 'width'=>"130px" ,'format'=>'number');
$arrDataStructure['currency'] = array('title'=>ucwords($obj->lang['curr']),'dbfield' => 'currencyname', 'width'=>"60px", "align"=>"center");
$arrDataStructure['paidtotal'] = array('title'=>ucwords($obj->lang['payingOffAmount']),'dbfield' => 'totalreceived', 'width'=>"130px" ,'format'=>'number','calculateTotal' => true);
$arrDataStructure['totaldiscount'] = array('title'=>ucwords($obj->lang['discount']),  'dbfield' => 'totaldiscount', 'width'=>"100px", 'format' => 'number' ,'calculateTotal' => true);
$arrDataStructure['downpayment'] = array('title'=>ucwords($obj->lang['downpayment']),'dbfield' => 'totaldownpayment', 'width'=>"130px" ,'format'=>'number','calculateTotal' => true);
$arrDataStructure['cost'] = array('title'=>ucwords($obj->lang['cost']),'dbfield' => 'totalcost', 'width'=>"130px" ,'format'=>'number','calculateTotal' => true);
$arrDataStructure['prepaidTax23'] = array('title'=>ucwords($obj->lang['tax23']),'dbfield' => 'prepaidtax23', 'width'=>"100px" ,'format'=>'number','calculateTotal' => true);
$arrDataStructure['totalPayment'] = array('title'=>ucwords($obj->lang['paymentAmount']),'dbfield' => 'totalpayment', 'width'=>"130px" ,'format'=>'number','calculateTotal' => true);
$arrDataStructure['note'] = array('title'=>ucwords($obj->lang['note']),'dbfield' => 'trnotes','width'=>"300px");
$arrDataStructure['status'] = array('title'=>ucwords($obj->lang['status']),'dbfield' => 'statusname', 'width'=>"100px" );
		  
$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $obj->lang['accountsReceivablePaymentReport'];
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;


$_POST['module'] = IMPORT_TEMPLATE['arPayment'];


$arrHeaderTemplate['total'] = array();

array_push($arrTemplate, $arrHeaderTemplate);
if ($isShowDetail){ 
	
switch(PLAN_TYPE['categorykey']){
		case COMPANY_TYPE['trucking'] : $refCodeLabel = $obj->lang['si']; break;
		case COMPANY_TYPE['forwarding'] : $refCodeLabel =  $obj->lang['si']; break;
		default : $refCodeLabel = $obj->lang['refCode']; break;
}	
// detail ...
$arrDataDetailStructure = array();
$arrDataDetailStructure['arcode'] = array('title'=>ucwords($obj->lang['arCode']),  'dbfield' => 'arcode', 'width'=>'100px', 'format' => 'string' ); 
$arrDataDetailStructure['refCode'] = array('title'=>ucwords($obj->lang['refCode']),  'dbfield' => 'refcode', 'width'=>'120px', 'format' => 'string' ); 
$arrDataDetailStructure['refCode2'] = array('title'=>ucwords( $refCodeLabel ),  'dbfield' => 'refcode2', 'width'=>'100px', 'format' => 'string' );     
$arrDataDetailStructure['refDate'] = array('title'=>ucwords($obj->lang['refDate']),'dbfield' => 'refdate', 'width'=>"120px",'format'=>'date');
$arrDataDetailStructure['salessman'] = array('title'=>ucwords($obj->lang['salesman']),  'dbfield' => 'salesname', 'width'=>'150px'); 
$arrDataDetailStructure['rate'] = array('title'=>ucwords($obj->lang['currencyRate']),  'dbfield' => 'rate', 'width'=>"120px", 'format' => 'number');
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
	$detailCriteria = '';
	
	$criteriaArr = array();
 
	array_push($criteriaArr, array('postVariable' => 'code', 
								   'fieldName' => $obj->tableName.'.code', 
								   'label' =>  $obj->lang['code']
								)
			   );
	
	array_push($criteriaArr, array('postVariable' => array('trStartDate', 'trEndDate'), 
							   'fieldName' => $obj->tableName.'.trdate', 
							   'label' =>  $obj->lang['date'], 
							   'type' => 'daterange'));
	  
	array_push($criteriaArr, array('postVariable' => 'selCustomer', 
							   'fieldName' => $obj->tableName.'.customerkey', 
							   'label' => $obj->lang['customer'], 
							   'useArrayKey' => array('obj' => $customer) ));

	array_push($criteriaArr, array('postVariable' => 'selWarehouse', 
								   'fieldName' => $obj->tableName.'.warehousekey', 
								   'label' => $obj->lang['warehouse'], 
								   'useArrayKey' => array('obj' => $warehouse) ));
    
 
    array_push($criteriaArr, array('postVariable' => 'selCurrency', 
							   'fieldName' => $obj->tableName.'.currencykey', 
							   'label' => $obj->lang['currency'] ,
							   'useArrayKey' => array('obj' => $currency)));

 
	array_push($criteriaArr, array('postVariable' => 'selStatus',
							   'type' => 'status'));

	$obj->createReportCriteria($criteria,$arrFilterInformation,$criteriaArr);
	
	// sales di detail
	
	        
    if(isset($_POST) && !empty($_POST['selSales'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selSales']));   
        
       	$detailCriteria = ' AND '.$obj->tableAR.'.saleskey in('.$key.')';  

        $rsCriteria = $employee->searchData('','',true, ' and '.$employee->tableName.'.pkey in ('.$key.')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$salesName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => $obj->lang['salesman'], 'filter' => $salesName ));
        
	}
	
  
    $orderBy = (!empty($_POST['hidOrderBy'])) ? $obj->oDbCon->paramOrder($_POST['hidOrderBy']) : 'pkey'; // order by harus dr kolom yg terdaftar saja
    $orderType = (isset($_POST['hidOrderType']) && !empty($_POST['hidOrderType']) && $_POST['hidOrderType'] == 1) ? 'desc' : 'asc';

	 
	$order = 'order by '.$orderBy.' ' .$orderType; 
	 
	$rs = $obj->searchData('','',true,$criteria,$order);

    $tempreport = '';   
    if (empty($rs)) 
        $tempreport .= '<tr class="report-row rewrite-row"><td colspan="'.count($arrHeaderTemplate['dataStructure']).'"></td></tr>';
	
    // $rsDetailCol = ($isShowDetail) ? $obj->getDetailCollections($rs,'refkey',$detailCriteria) : array();
	
	// perlu utk compile nama sales
	$rsDetailCol = $obj->getDetailCollections($rs,'refkey',$detailCriteria);
		
    // currency master
    $rsCurrency = $currency->searchData();
    $rsCurrency = array_column($rsCurrency,null,'pkey');
    
    // ambil semua detail AR dulu
    $arrDetailARKey = array(); 
    foreach($rsDetailCol as $detailColRow) 
        $arrDetailARKey = array_merge($arrDetailARKey, array_column($detailColRow,'arkey')); 
      
    $arrDetailARKey = array_values(array_unique($arrDetailARKey));
        
    $rsARCol = $ar->searchDataRow( array($ar->tableName.'.pkey', 
                                      $ar->tableName.'.code', 
                                      $ar->tableName.'.refcode', 
                                      $ar->tableName.'.refcode2', 
                                      $ar->tableName.'.trdate', 
                                      $ar->tableName.'.amount',  
                                      $ar->tableName.'.rate',   
                                      $ar->tableName.'.saleskey',   
                                      $ar->tableName.'.currencykey'), 
                                   ' and ' . $ar->tableName.'.pkey in ('.$class->oDbCon->paramString($arrDetailARKey,',').')'  
                              ); 
	 
	
	// master sales
	$arrSalesKey = array_unique(array_column($rsARCol,'saleskey'));
	$rsEmployee = $employee->searchDataRow(array($employee->tableName.'.pkey',$employee->tableName.'.name'), ' and '.$employee->tableName.'.pkey in ('.$class->oDbCon->paramString($arrSalesKey,',').')');
	$rsEmployee = array_column($rsEmployee,null,'pkey');
	
    $rsARCol = array_column($rsARCol,null,'pkey'); 
    
    $totalRs = count($rs);
    for( $i=0;$i<$totalRs;$i++) {   
 
            if (!isset($rsDetailCol[$rs[$i]['pkey']]))  continue;
                
		
			// perlu utk compile nama sales
		
			$rsDetail = $rsDetailCol[$rs[$i]['pkey']]; 

            $totalDetail = count($rsDetail);
			
			$arrSalesName = array();
            for ($j=0;$j<$totalDetail;$j++){   
                
                $rsAR = $rsARCol[$rsDetail[$j]['arkey']];
		 
                $rsDetail[$j]['arcode'] =  $rsAR['code'];
                $rsDetail[$j]['refcode'] =  $rsAR['refcode']; 
                $rsDetail[$j]['refcode2'] =  $rsAR['refcode2'];   
                $rsDetail[$j]['refdate'] =  $rsAR['trdate'];   
                $rsDetail[$j]['amountar'] =  $rsAR['amount'];   
                $rsDetail[$j]['salesname'] = (isset( $rsEmployee[$rsAR['saleskey']])) ? $rsEmployee[$rsAR['saleskey']]['name'] : ''; 
                $rsDetail[$j]['rate'] =  $rsAR['rate'];   
                $rsDetail[$j]['currencyname'] =  $rsCurrency[$rsAR['currencykey']]['name']; 
                $rsDetail[$j]['paymentamount'] =  $rsDetail[$j]['amount'] - $rsDetail[$j]['taxamount']; 
				
				if($rsDetail[$j]['salesname'] <> '' && !in_array($rsDetail[$j]['salesname'],$arrSalesName))
					array_push($arrSalesName, $rsDetail[$j]['salesname']);
            }
			
			$rs[$i]['salesname'] = implode(', ',$arrSalesName );

		
        if($isShowDetail){ 
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
$arrSales =  (PLAN_TYPE['categorykey'] == COMPANY_TYPE['retail']) ? $employee->generateComboboxOpt(null,array('criteria' =>' and  '.$employee->tableName.'.statuskey = 2 and '.$employee->tableName.'.issales = 1 ')) : array();  
$arrStatus = $class->convertForCombobox($obj->getAllStatus(),'pkey','status');

$arrTwigVar['inputCode'] =  $class->inputText('code');
//$arrTwigVar['inputHidCustomerKey'] = $class->inputHidden('hidCustomerKey');
//$arrTwigVar['inputCustomerName'] =  $class->inputText('customerName');
$arrTwigVar['inputSelCustomer'] =  $class->inputSelect('selCustomer[]', $arrCustomer, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputSelCurrency'] =  $class->inputSelect('selCurrency[]', $arrCurrency, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox')); 
$arrTwigVar['inputSelSales'] =  $class->inputSelect('selSales[]', $arrSales, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputStartDate'] = $class->inputDate('trStartDate',array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputEndDate'] = $class->inputDate('trEndDate',array('etc' => 'style="text-align:center"'));  
$arrTwigVar['inputSelStatus'] =  $class->inputSelect('selStatus[]', $arrStatus, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox')); 
$arrTwigVar['inputSelWarehouse'] =  $class->inputSelect('selWarehouse[]', $arrWarehouse, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputIsShowDetail'] =  $class->inputCheckBox('isShowDetail'); 
$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate;       
echo $twig->render('reportARPayment.html', $arrTwigVar);   
?> 