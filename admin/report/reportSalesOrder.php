<?php
	 
include '../../_config.php';  
include '../../_include-v2.php';

includeClass('SalesOrder.class.php');
$salesOrder = createObjAndAddToCol( new SalesOrder()); 
$item = createObjAndAddToCol( new Item()); 
$warehouse = createObjAndAddToCol( new Warehouse()); 
$customer = createObjAndAddToCol( new Customer());
$brand = createObjAndAddToCol( new Brand()); 
$city = createObjAndAddToCol( new City()); 

include '_global.php';

$obj= $salesOrder;
$securityObject = 'reportSalesOrder'; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true)); 

if(empty($_POST['hidAction'])){
    $_POST['trStartDate'] = date('d / m / Y');
	$_POST['trEndDate'] = date('d / m / Y');  
    $_POST['isGrouping'] = 1;
}
    
 
$isGrouping = ($_POST['isGrouping'] == 1) ? true : false;

$_POST['selStatus[]'] = array(2,3);

$arrFilterInformation = array(); 

$dataToExport = array();

/* data structure */
$arrTemplate = array();
$isShowDetail = (isset($_POST['isShowDetail']) && !empty($_POST['isShowDetail'])) ? true : false;

// kalo grouping, sudah pasti gk show detail
if(!$isGrouping) $isShowDetail = false;  
 
$arrDataStructure = array();
$arrDataStructure['rowNumber'] = array('title'=>'#', 'align'=>'right', 'width'=>"40px", 'autoNumber' => true, "sortable" => false);
$arrDataStructure['code'] = array('title'=>ucwords($obj->lang['code']),  'width'=>"150px", 'dbfield' => 'code'); 
$arrDataStructure['date'] = array('title'=>ucwords($obj->lang['date']),'dbfield' => 'trdate', 'width'=>"120px",'format'=>'date');
$arrDataStructure['refcode'] = array('title'=>ucwords($obj->lang['refCode']),  'width'=>"150px", 'dbfield' => 'refcode'); 
$arrDataStructure['warehouse'] = array('title'=>ucwords($obj->lang['warehouse']),'dbfield' => 'warehousename', 'width'=>"110px");
$arrDataStructure['customer'] = array('title'=>ucwords($obj->lang['customer']),'dbfield' => 'customername', 'width'=>"150px");
$arrDataStructure['sales'] = array('title'=>ucwords($obj->lang['salesman']),'dbfield' => 'salesname', 'width'=>"150px");


if($isGrouping){
    $arrDataStructure['subtotal'] = array('title'=>ucwords($obj->lang['subtotal']),'dbfield' => 'subtotal','align'=>'right', 'width'=>"100px",'format'=>'integer','calculateTotal' => true); 
    $arrDataStructure['finalDiscount'] = array('title'=>ucwords($obj->lang['finalDiscount']),'dbfield' => 'finaldiscount','align'=>'right', 'width'=>"100px",'format'=>'integer','calculateTotal' => true); 
    if($obj->multiLevelDiscount == 1)
        $arrDataStructure['finalDiscount2'] = array('title'=>ucwords($obj->lang['finalDiscount']),'dbfield' => 'finaldiscount2','align'=>'right', 'width'=>"100px",'format'=>'integer','calculateTotal' => true); 

    $arrDataStructure['point'] = array('title'=>ucwords($obj->lang['point']),'dbfield' => 'pointvalue','align'=>'right', 'width'=>"100px",'format'=>'integer','calculateTotal' => true); 
    $arrDataStructure['tax'] = array('title'=>ucwords($obj->lang['tax']),'dbfield' => 'taxvalue','align'=>'right', 'width'=>"100px",'format'=>'integer','calculateTotal' => true); 
    $arrDataStructure['shipmentFee'] = array('title'=>ucwords($obj->lang['shippingFee']),'dbfield' => 'shipmentfee','align'=>'right', 'width'=>"100px",'format'=>'integer','calculateTotal' => true); 
    $arrDataStructure['etccost'] = array('title'=>ucwords($obj->lang['etccost']),'dbfield' => 'etccost','align'=>'right', 'width'=>"100px",'format'=>'integer','calculateTotal' => true); 
    $arrDataStructure['total'] = array('title'=>ucwords($obj->lang['total']),'dbfield' => 'grandtotal','align'=>'right', 'width'=>"100px",'format'=>'integer','calculateTotal' => true); 
    $arrDataStructure['hpp'] = array('title'=>ucwords($obj->lang['cogs']),'dbfield' => 'totalcogs','align'=>'right', 'width'=>"100px",'format'=>'integer','calculateTotal' => true); 
    $arrDataStructure['profit'] = array('title'=>ucwords($obj->lang['profit']),'dbfield' => 'profit','align'=>'right', 'width'=>"100px",'format'=>'integer','calculateTotal' => true); 
    $arrDataStructure['paymentInformation'] = array('title'=>ucwords($obj->lang['paymentInformation']),'dbfield' => 'paymentinformation','width'=>"150px"); 
    $arrDataStructure['shipment'] = array('title'=>ucwords($obj->lang['shipment']),'dbfield' => 'shipmentname','width'=>"150px"); 
    $arrDataStructure['desc'] = array('title'=>ucwords($obj->lang['note']),'dbfield' => 'trdesc','width'=>"250px"); 
}else{
    $arrDataStructure['itemcode'] = array('title'=>ucwords($obj->lang['itemCode']),'dbfield' => 'itemcode', 'width'=>"100px"); 
    $arrDataStructure['itemname'] = array('title'=>ucwords($obj->lang['itemName']),'dbfield' => 'itemname', 'width'=>"250px"); 
    $arrDataStructure['brandname'] = array('title'=>ucwords($obj->lang['brand']),'dbfield' => 'brandname', 'width'=>"130px"); 
    $arrDataStructure['qty'] = array('title'=>ucwords($obj->lang['qty']),'dbfield' => 'qty','align'=>'right', 'width'=>"100px",'format'=>'integer','calculateTotal' => true); 
    $arrDataStructure['priceinunit'] = array('title'=>ucwords($obj->lang['price']),'dbfield' => 'priceinunit','align'=>'right', 'width'=>"100px",'format'=>'integer'); 
    $arrDataStructure['hpp'] = array('title'=>ucwords($obj->lang['cogs']),'dbfield' => 'costinbaseunit','align'=>'right', 'width'=>"100px",'format'=>'integer'); 
    $arrDataStructure['totalsales'] = array('title'=>ucwords($obj->lang['totalselling']),'dbfield' => 'totalselling','align'=>'right', 'width'=>"100px",'format'=>'integer','calculateTotal' => true); 
    $arrDataStructure['totalcogs'] = array('title'=>ucwords($obj->lang['totalcogs']),'dbfield' => 'totalcogs','align'=>'right', 'width'=>"100px",'format'=>'integer','calculateTotal' => true); 
    $arrDataStructure['profit'] = array('title'=>ucwords($obj->lang['profit']),'dbfield' => 'profit','align'=>'right', 'width'=>"100px",'format'=>'integer','calculateTotal' => true); 
}
    
$arrDataStructure['status'] = array('title'=>ucwords($obj->lang['status']),'dbfield' => 'statusname', 'width'=>"80px");

$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $obj->lang['salesOrderReport']; 
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;

$_POST['module'] = IMPORT_TEMPLATE['salesOrder'];

$arrHeaderTemplate['total'] = array();

array_push($arrTemplate, $arrHeaderTemplate);
if ($isShowDetail){ 
    // detail ...
    $arrDataDetailStructure = array(); 
    $arrDataDetailStructure['itemCode'] = array('title'=>ucwords($obj->lang['itemCode']),  'dbfield' => 'itemcode', 'width'=>"100px" );  
    $arrDataDetailStructure['itemName'] = array('title'=>ucwords($obj->lang['itemName']),  'dbfield' => 'itemname', 'width'=>"240px" );  
    if (PLAN_TYPE['categorykey'] == COMPANY_TYPE['jewelry'])
        $arrDataDetailStructure['serialNumber'] = array('title'=>ucwords($obj->lang['serialNumber']),'dbfield' => 'serialnumber', 'width'=>"130px"); 
    $arrDataDetailStructure['brandname'] = array('title'=>ucwords($obj->lang['brand']),'dbfield' => 'brandname', 'width'=>"130px"); 
    $arrDataDetailStructure['qty'] = array('title'=>ucwords($obj->lang['qty']),  'dbfield' => 'qty', 'width'=>"60px" , 'format' => 'number'); 
    $arrDataDetailStructure['unit'] = array('title'=>ucwords($obj->lang['unit']),  'dbfield' => 'unitname', 'width'=>"100px" ); 
    //$arrDataDetailStructure['deliveredQty'] = array('title'=>ucwords($obj->lang['deliveredQty']),  'dbfield' => 'deliveredqty', 'width'=>"180px");
    $arrDataDetailStructure['priceInUnit'] = array('title'=>ucwords($obj->lang['price']),'dbfield' => 'priceinunit', 'width'=>"100px",'format'=>'number');
    $arrDataDetailStructure['discount'] = array('title'=>ucwords($obj->lang['discount']),'dbfield' => 'discount', 'width'=>"100px",'format'=>'number');
    $arrDataDetailStructure['total'] = array('title'=>ucwords($obj->lang['total']),'dbfield' => 'total', 'width'=>"100px",'format'=>'number');
    $arrDataDetailStructure['hpp'] = array('title'=>"HPP",'dbfield' => 'costinbaseunit', 'width'=>"100px",'format'=>'number');
    $arrDataDetailStructure['profit'] = array('title'=>ucwords($obj->lang['profit'] .' @'),'dbfield' => 'profit', 'width'=>"100px",'format'=>'number');
    $arrDataDetailStructure['profitTotal'] = array('title'=>ucwords($obj->lang['subtotal']) . ' '.ucwords($obj->lang['profit']),'dbfield' => 'profittotal', 'width'=>"100px",'format'=>'number');

    $arrDetailTemplate = array(); 
    $arrDetailTemplate['dataStructure'] = $arrDataDetailStructure;
    $arrDetailTemplate['total'] = array();

    array_push($arrTemplate, $arrDetailTemplate); 
}



$arrWarehouse = $class->convertForCombobox($warehouse->searchData($warehouse->tableName.'.statuskey',1,true,'','order by name asc'),'pkey','name');
$arrEmployee = $class->convertForCombobox($employee->searchData($employee->tableName.'.statuskey',2,true, ' and '.$employee->tableName.'.issales = 1 '),'pkey','name');   
$arrCustomer = $class->convertForCombobox($customer->searchData($customer->tableName.'.statuskey',2,true,'','order by name asc'),'pkey','name');
$arrBrand = $class->convertForCombobox($brand->searchData($brand->tableName.'.statuskey',1,true),'pkey','name');      
$arrCity = $class->convertForCombobox($city->searchData($city->tableName.'.statuskey',1,true),'pkey','name');   
$arrStatus = $class->convertForCombobox($obj->getAllStatus(),'pkey','status');   
  
 
$arrTwigVar['inputSalesCode'] =  $class->inputText('salesCode');  
$arrTwigVar['inputSalesRefCode'] =  $class->inputText('salesRefCode');   
$arrTwigVar['inputSelCustomer'] =  $class->inputSelect('selCustomer[]', $arrCustomer, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputSelStatus'] =  $class->inputSelect('selStatus[]', $arrStatus, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox')); 
$arrTwigVar['inputSelBrand'] =  $class->inputSelect('selBrand[]', $arrBrand, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox')); 
$arrTwigVar['inputItemName'] =  $class->inputText('itemName'); 
$arrTwigVar['inputSalesName'] =  $class->inputSelect('selSales[]', $arrEmployee, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox')); 
$arrTwigVar['inputStartDate'] = $class->inputDate('trStartDate', array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputEndDate'] = $class->inputDate('trEndDate', array('etc' => 'style="text-align:center"')); 
$arrTwigVar['inputSelWarehouse'] =  $class->inputSelect('selWarehouse[]', $arrWarehouse, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));  
$arrTwigVar['inputSelCity'] =  $class->inputSelect('selCity[]', $arrCity, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox')); 
$arrTwigVar['inputTemplateCustomer'] = $class->inputAutoComplete(array(   
                                                                        'element' => array('value' => 'selTemplateCustomer',
                                                                                           'key' => 'hidTemplateCustomerKey'),
                                                                        'source' => array(
                                                                                            'url' => '../ajax-template-customer.php',
                                                                                            'data' => array(  'action' =>'searchData')
                                                                                        ), 
                                                                        'placeholder' => $obj->lang['searchTemplate'].'...',
                                                                        'callbackFunction' => 'updateCustomer(this)' 
                                                                      )); 
$arrTwigVar['inputIsShowDetail'] =  $class->inputCheckBox('isShowDetail');
$arrTwigVar['inputIsGrouping'] =  $class->inputCheckBox('isGrouping');
$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate;


if (isset($_POST) && !empty($_POST['hidAction'])){  
		
    $orderBy = (!empty($_POST['hidOrderBy'])) ? $obj->oDbCon->paramOrder($_POST['hidOrderBy']) : 'pkey'; // order by harus dr kolom yg terdaftar saja
    $orderType = (isset($_POST['hidOrderType']) && !empty($_POST['hidOrderType']) && $_POST['hidOrderType'] == 1) ? 'desc' : 'asc';
	
	if(empty($_POST['hidRs'])){ 
    	$result = queryNewReport(get_defined_vars(), array('orderBy' => $orderBy, 'orderType' => $orderType));
		$rs = $result['rs']; 
		$arrFilterInformation = $result['arrFilterInformation'];
	}else{
		$hidRs = json_decode($_POST['hidRs'],true);  
		foreach($hidRs as $key=>$row) $$key = $hidRs[$key];
		$obj->mknatsort ($rs, $orderBy, ($orderType=='asc')?false:true ,true);
	}
    
    $tempreport = ''; 
    $totalRs = count($rs);
    for( $i=0;$i<$totalRs;$i++) {  
        $arrHeaderStyle = array();  
 
        if ($rs[$i]['profit'] < 0)  
            $arrHeaderStyle['profit']['textColor'] = 'C41E3A'; 
        else if ($rs[$i]['profit'] > 0) 
            $arrHeaderStyle['profit']['textColor'] = '568203'; 
        
		$arrHeaderStyle['totalcogs']['textColor'] = '0093AF';
		
        $return = $obj->formatReportRows(array('data' => $rs[$i], 'style' => $arrHeaderStyle ),$arrTemplate); 

        // ===== FOR EXPORT SECTION 
        array_push($dataToExport, $return['data']);  
        // ===== END FOR EXPORT SECTION

        $tempreport .= $return['html'];
        $arrTemplate[0]['total'] = $obj->arraySum($arrTemplate[0]['total'], $return['subtotal'][0]);

    }
    
    $arrTemplate[0]['dataStructure'] = $arrDataStructure;
    $arrHeaderTemplate['dataStructure'] = $arrDataStructure;
     
    $tableHeader = $twig->render('template-header.html', $arrTwigVar);
    
	$obj->generateReport($_POST, $tempreport, $arrTemplate,array('dataToExport' => $dataToExport,'rs' => $rs),$arrFilterInformation,$tableHeader);

}
 
echo $twig->render('reportSalesOrder.html', $arrTwigVar);  

function queryNewReport($varCol = array(),$order){ 
	foreach($varCol as $key=>$row) $$key = $varCol[$key];
		 
	$arrFilterInformation = array();
	
	$criteria = '';
	$detailCriteria = '';
    
	$criteriaArr = array();
    array_push($criteriaArr, array('postVariable' => 'salesCode', 
                       'fieldName' => $obj->tableName.'.code', 
                       'label' =>  $obj->lang['code']
                    )
    );
    array_push($criteriaArr, array('postVariable' => 'salesRefCode', 
                       'fieldName' => $obj->tableName.'.code', 
                       'label' =>  $obj->lang['refCode']
                    )
    );
 
	array_push($criteriaArr, array('postVariable' => array('trStartDate', 'trEndDate'), 
							   'fieldName' => $obj->tableName.'.trdate', 
							   'label' =>  $obj->lang['date'], 
							   'type' => 'daterange'));
	  
    
    array_push($criteriaArr, array('postVariable' => 'selCustomer', 
                       'fieldName' => $obj->tableName.'.customerkey', 
                       'label' =>  $obj->lang['customer'],
				    'useArrayKey' => array('obj' => $customer)  
                    ));
    
    array_push($criteriaArr, array('postVariable' => 'selCity', 
                       'fieldName' => $obj->tableName.'.citykey', 
                       'label' =>  $obj->lang['location'],
				        'useArrayKey' => array('obj' => $city) 
                    ));
    
    array_push($criteriaArr, array('postVariable' => 'selSales', 
                       'fieldName' => $obj->tableName.'.saleskey', 
                       'label' =>  $obj->lang['sales'],
				        'useArrayKey' => array('obj' => $employee) 
                    ));
    
   
	array_push($criteriaArr, array('postVariable' => 'selWarehouse', 
								   'fieldName' => $obj->tableName.'.warehousekey', 
								   'label' => $obj->lang['warehouse'], 
								   'useArrayKey' => array('obj' => $warehouse) ));

    // hanya jika non grouping
    if(!$isGrouping){

        array_push($criteriaArr, array('postVariable' => 'selBrand', 
                                       'fieldName' => $obj->tableItem.'.brandkey', 
                                       'label' => $obj->lang['brand'], 
                                       'useArrayKey' => array('obj' => $brand) ));
 
    }
    
	array_push($criteriaArr, array('postVariable' => 'selStatus',
							   'type' => 'status'));
    
	$obj->createReportCriteria($criteria,$arrFilterInformation,$criteriaArr);
        

    
    
	$order = 'order by '.$order['orderBy'].' ' .$order['orderType']; 
     
	$rs = ($isGrouping) ? $obj->searchData('','',true,$criteria,$order) :  $obj->getReportPerItem($criteria,$order) ;
	$rsDetailCol = ($isShowDetail) ? $obj->getDetailCollections($rs,'refkey') : array();
    $arrDetailKey = ($isShowDetail) ? array_column($rsDetailCol, 'pkey') : array();
	
	$hasCOGSAccess = $security->isAdminLogin($item->cogsSecurityObject,10);  
	 
	// informasi payment
	$rsPaymentCol =  $obj->getPaymentDetailCollections($rs,'refkey', ' and '.$obj->tablePayment.'.refkey in ('.$class->oDbCon->paramString(array_column($rs,'pkey'),',').')' );
	
    $totalRs = count($rs);
    for( $i=0;$i<$totalRs;$i++) {  
        $arrHeaderStyle = array();  
		
		$pkey = $rs[$i]['pkey'];
			
        if(!$hasCOGSAccess) {
			$rs[$i]['totalcogs'] = 0;
			$rs[$i]['costinbaseunit'] = 0;
			$rs[$i]['profit'] = 0;
		}
    
        $discount = $rs[$i]['finaldiscount'];
        $discount2 = $rs[$i]['finaldiscount2'];
        $discountType = $rs[$i]['finaldiscounttype'];
        $discountType2 = $rs[$i]['finaldiscounttype2'];
        $subtotal =  $rs[$i]['subtotal'];

		$discountValue = $obj->getDiscountValue($subtotal,$discount,$discountType);
		$discountValue2 = $obj->getDiscountValue($subtotal-$discountValue,$discount2,$discountType2);
		
        $rs[$i]['finaldiscount']= $discountValue;
        $rs[$i]['finaldiscount2']= $discountValue2;
  
		if($rs[$i]['termofpaymentduedays'] > 0){ 
			$rs[$i]['paymentinformation'] = $rs[$i]['termofpaymentname'];
		}else{ 
			// paymentmethod
			$tempPaymentMethod = $rsPaymentCol[$pkey];   
			$rs[$i]['paymentinformation'] = implode(', ',array_column($tempPaymentMethod,'paymentmethodname')); 
		}
		
        if($isShowDetail){ 
         
            if (!isset($rsDetailCol[$pkey]))  continue;
            $rsDetail = $rsDetailCol[$pkey]; 

			
            $arrDetailStyle = array();
            for($j=0;$j<count($rsDetail);$j++){

                if (PLAN_TYPE['categorykey'] == COMPANY_TYPE['jewelry']) {
                    $rsSN = $obj->getSerialNumber($rsDetail[$j]['pkey']);
                    $sn = (!empty($rsSN)) ? implode(', ',array_column($rsSN,'serialnumber')) : '';
                    $rsDetail[$j]['serialnumber'] = $sn;
                }
 
 
                if(!$hasCOGSAccess){
                    $rsDetail[$j]['costinbaseunit'] =0;
                    $rsDetail[$j]['profit'] = 0;
                } 
                
                $rsDetail[$j]['profittotal'] = $rsDetail[$j]['qtyinbaseunit'] * $rsDetail[$j]['profit'];
              
                $discount = $rsDetail[$j]['discount'];
                $discountType = $rsDetail[$j]['discounttype'];
                $priceInUnit = $rsDetail[$j]['priceinunit'];

				$discountValue = $obj->getDiscountValue($priceInUnit,$discount,$discountType);
                $rsDetail[$j]['discount'] = $discountValue;
				
				if ($rsDetail[$j]['profit'] < 0) { 
                    $arrDetailStyle[$j]['profit']['textColor'] = 'C41E3A';
                    $arrDetailStyle[$j]['profittotal']['textColor'] = 'C41E3A';
                }else if ($rsDetail[$j]['profit'] > 0){ 
                    $arrDetailStyle[$j]['profit']['textColor'] = '568203'; 
                    $arrDetailStyle[$j]['profittotal']['textColor'] = '568203';
                } 
				
            }
			    
            $rs[$i]['_detail_'] = array('arrTemplate'=>$arrDetailTemplate,'data' => $rsDetail, 'style' => $arrDetailStyle); 
        }
 
    }
	
	
	return array(
		'arrFilterInformation' => $arrFilterInformation, 
		'rs' => $rs
	);
}
?>
