<?php	 
include '../../_config.php';  
include '../../_include-v2.php';

includeClass(array('AR.class.php','ARPrepaidTax23.class.php'));
$arPrepaidTax23 = createObjAndAddToCol(new ARPrepaidTax23());
$arPayment = createObjAndAddToCol(new ARPayment());
$customer = createObjAndAddToCol(new Customer());
$warehouse = createObjAndAddToCol(new Warehouse());
$currency = createObjAndAddToCol(new Currency());

include '_global.php';

$obj= $arPrepaidTax23;
$arPayment = $obj->getPaymentObj();
$securityObject = 'reportARPrepaidTax23'; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
  
if(!$security->isAdminLogin($securityObject,10,true)); 
    
$rsCurrency = $currency->searchData($currency->tableName.'.statuskey',1,true);

$arrFilterInformation = array();    
 
// ===== FOR EXPORT SECTION
$dataToExport = array();

/* data structure */
$arrTemplate = array();
$isShowDetail = (isset($_POST['isShowDetail']) && !empty($_POST['isShowDetail'])) ? true : false;

$arrDataStructure = array();
$arrDataStructure['rowNumber'] = array('title'=>'#', 'align'=>'right', 'width'=>"40px", 'autoNumber' => true,"sortable" => false);
$arrDataStructure['customerName'] = array('title'=>ucwords($obj->lang['customer']),  'width'=>"300px", 'dbfield' => 'customername',"sortable" => false, 'mergeExcelCell' => 3);
$arrDataStructure['notduedays'] = array('title'=> ucwords($obj->lang['notDue']),  'width'=>"110px", 'dbfield' => 'notduedays',  "sortable" => false, "format" => 'number','calculateTotal' => true);
$arrDataStructure['30days'] = array('title'=>'0 - 30 '.ucwords($obj->lang['days']),  'width'=>"110px", 'dbfield' => '30days',  "sortable" => false, "format" => 'number','calculateTotal' => true);
$arrDataStructure['60days'] = array('title'=>'31 - 60 '.ucwords($obj->lang['days']),  'width'=>"110px",  'dbfield' => '60days',  "sortable" => false, "format" => 'number','calculateTotal' => true);
$arrDataStructure['90days'] = array('title'=>'61 - 90 '.ucwords($obj->lang['days']),  'width'=>"110px", 'dbfield' => '90days',  "sortable" => false, "format" => 'number','calculateTotal' => true);
$arrDataStructure['moreThan90days'] = array('title'=>'> 90 '.ucwords($obj->lang['days']),  'width'=>"110px",  'dbfield' => 'moreThan90days', "sortable" => false, "format" => 'number','calculateTotal' => true);
$arrDataStructure['amount'] = array('title'=>ucwords($obj->lang['total']),'dbfield' => 'totalamount', 'width'=>"130px" ,'format'=>'number','calculateTotal' => true);

$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $obj->lang['prepaidTax23AgingReport']; 
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();

array_push($arrTemplate, $arrHeaderTemplate);

if ($isShowDetail){ 
// detail ...
$arrDataDetailStructure = array();
$arrDataDetailStructure['arcode'] = array('title'=>ucwords($obj->lang['arCode']),  'dbfield' => 'arcode', 'width'=>'85px', 'format' => 'string' ); 
$arrDataDetailStructure['refCode'] = array('title'=>ucwords($obj->lang['refCode']),  'dbfield' => 'refcode', 'width'=>'120px', 'format' => 'string' ); 
$arrDataDetailStructure['refDate'] = array('title'=>ucwords($obj->lang['refDate']),'dbfield' => 'trdate', 'width'=>"80px",'format'=>'date');
$arrDataDetailStructure['notduedays'] = array('title'=> ucwords($obj->lang['notDue']),  'width'=>"110px", 'dbfield' => 'notduedays',  "sortable" => false, "format" => 'number','calculateTotal' => true);
$arrDataDetailStructure['30days'] = array('title'=>'0 - 30 '.ucwords($obj->lang['days']),  'width'=>"110px", 'dbfield' => '30days',  "sortable" => false, "format" => 'number','calculateTotal' => true);
$arrDataDetailStructure['60days'] = array('title'=>'31 - 60 '.ucwords($obj->lang['days']),  'width'=>"110px",  'dbfield' => '60days',  "sortable" => false, "format" => 'number','calculateTotal' => true);
$arrDataDetailStructure['90days'] = array('title'=>'61 - 90 '.ucwords($obj->lang['days']),  'width'=>"110px", 'dbfield' => '90days',  "sortable" => false, "format" => 'number','calculateTotal' => true);
$arrDataDetailStructure['moreThan90days'] = array('title'=>'> 90 '.ucwords($obj->lang['days']),  'width'=>"110px",  'dbfield' => 'moreThan90days', "sortable" => false, "format" => 'number','calculateTotal' => true);

$arrDetailTemplate = array();
$arrDetailTemplate['reportWidth'] = "680px";
$arrDetailTemplate['dataStructure'] = $arrDataDetailStructure;
$arrDetailTemplate['total'] = array();

array_push($arrTemplate, $arrDetailTemplate);  
}

if (isset($_POST) && !empty($_POST['hidAction'])){   
	
	$criteria = ' and '.$obj->tableName.'.statuskey in (1,2)'; 
    array_push($arrFilterInformation,array("label" => $obj->lang['date'], 'filter' =>  date('d / m / Y') )); 
    
    if(isset($_POST) && !empty($_POST['selCustomer'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selCustomer']));   
        
       	$criteria .= ' AND '.$obj->tableName.'.customerkey in('.$key.')';  

        $rsCriteria = $customer->searchData('','',true, ' and '.$customer->tableName.'.pkey in ('.$key.')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$statusName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => $obj->lang['customer'], 'filter' => $statusName ));
        
	}
     
    
	if(isset($_POST) && !empty($_POST['selWarehouse'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selWarehouse']));   
        
       	$criteria .= ' AND '.$obj->tableName.'.warehousekey in('.$key.')';  

        $rsCriteria = $warehouse->searchData('','',true, ' and '.$warehouse->tableName.'.pkey in ('.$key.')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$statusName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => $obj->lang['warehouse'], 'filter' => $statusName ));
        
	}
 
	$order = 'order by customername asc'; 
	 
	$rs = $obj->searchData('','',true,$criteria,$order);

    $rARByCustomer = array();
    
    // susun ulang list AR group by customer
    foreach($rs as $row){
         $customerkey = $row['customerkey'];
         $datediff = $row['datediff']; 
         $outstanding = $row['outstanding'];

        if (!isset($rARByCustomer[$customerkey])) { 
            
			$rARByCustomer[$customerkey] = array('customername' => $row['customername'], 'detail' => array());

			$rARByCustomer[$customerkey] = array_merge($rARByCustomer[$customerkey], array( 
											  'notduedays' => 0,
											  '30days' => 0,
											  '60days' => 0,
											  '90days' => 0,
											  'moreThan90days' => 0,
											  'totalamount' => 0 
											));
            
        }
 
        $arrAging = array();
        
            if ($datediff <= 0) 
                $arrAging['notduedays'] = $outstanding;
            else if ($datediff <= 30) 
                $arrAging['30days'] = $outstanding;
            else if ($datediff <= 60) 
                $arrAging['60days'] = $outstanding;
            else if ($datediff <= 90) 
                $arrAging['90days'] = $outstanding;
            else  
                $arrAging['moreThan90days'] = $outstanding;   

            $rARByCustomer[$customerkey]['totalamount'] += $outstanding;  

            $arrDetail = array(); 
            foreach($arrAging as $agingkey => $agingValue) { 
                $rARByCustomer[$customerkey][$agingkey] += $agingValue;
                $arrDetail[$agingkey] = $agingValue;
            }

            $arrDetail['arcode'] =  $row['code'];
            $arrDetail['refcode'] =  $row['refcode'];
            $arrDetail['trdate'] =  $row['trdate'];

            array_push($rARByCustomer[$customerkey]['detail'], $arrDetail);
    }
     
    
    $tempreport = '';  
    if (empty($rARByCustomer)) 
        $tempreport .= '<tr class="report-row rewrite-row"><td colspan="'.count($arrHeaderTemplate['dataStructure']).'"></td></tr>';

    
    // disini generate report seperti biasa
    
    foreach($rARByCustomer as $customerkey => $row){
 
        if ($isShowDetail){
            // has detail 
            $row['_detail_'] = array('arrTemplate'=>$arrDetailTemplate,'data' => $row['detail']);
        }
        
        $return = $obj->formatReportRows(array('data' => $row),$arrTemplate); 
        
        // ===== FOR EXPORT SECTION 
        array_push($dataToExport, $return['data']);  
        // ===== END FOR EXPORT SECTION

        $tempreport .= $return['html'];  
        $arrTemplate[0]['total'] = $obj->arraySum($arrTemplate[0]['total'], $return['subtotal'][0]);
        
    }
    
    unset($row);
 
    $obj->generateReport($_POST, $tempreport, $arrTemplate,$dataToExport,$arrFilterInformation);
 
}
 
 
$arrWarehouse = $class->convertForCombobox($warehouse->searchData($warehouse->tableName.'.statuskey',1,true,'','order by name asc'),'pkey','name');
$arrCustomer = $class->convertForCombobox($customer->searchData($customer->tableName.'.statuskey',2,true,'','order by name asc'),'pkey','name'); 
$arrTwigVar['inputSelCustomer'] =  $class->inputSelect('selCustomer[]', $arrCustomer, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));   
$arrTwigVar['inputSelWarehouse'] =  $class->inputSelect('selWarehouse[]', $arrWarehouse, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));       
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
$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate;  
echo $twig->render('reportARPrepaidTax23Aging.html', $arrTwigVar);   
?>