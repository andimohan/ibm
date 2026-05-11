<?php
include '../../_config.php';
include '../../_include-v2.php';

includeClass('EMKLCommission.class.php');
$emklCommission = createObjAndAddToCol( new EMKLCommission()); 
$supplier = createObjAndAddToCol( new Supplier()); 
$warehouse = createObjAndAddToCol( new Warehouse()); 
$customer = createObjAndAddToCol( new Customer()); 
$currency = createObjAndAddToCol( new Currency()); 
$termOfPayment = createObjAndAddToCol( new TermOfPayment()); 

include '_global.php';

$obj = $emklCommission;
$securityObject = 'reportEMKLCommission'; // the value of security object is manually inserted to handle 
								  // some modules that have different security object from that of their class
								  
if(!$security->isAdminLogin($securityObject,10,true));  

$arrFilterInformation = array(); 
$detailCriteria = ''; 
$_POST['selStatus[]'] = array(2,3);
if(!isset($_POST['isGrouping']))  $_POST['isGrouping'] = 0; // sementara    
if(!isset($_POST['isShowDetail']))  $_POST['isShowDetail'] = 0;


// ====================== must be set before TWIG
if (!isset($_POST['trStartDate']) || empty($_POST['trStartDate'])){ 
	$_POST['trStartDate'] = date('d / m / Y');
	$_POST['trEndDate'] = date('d / m / Y');
}   
 
$orderCriteria = array(); 
$orderCriteria['orderBy'] =  (isset ($_POST) && !empty($_POST['hidOrderBy']) ) ?  $obj->oDbCon->paramOrder($_POST['hidOrderBy']) : 'trdate'; //$obj->tableName.'.
$orderCriteria['orderType'] = (isset ($_POST) && !empty($_POST['hidOrderType'])) ?   $_POST['hidOrderType'] : -1;

// ===== FOR EXPORT SECTION
$dataToExport = array();

/* data structure */
$arrTemplate = array();
$isGrouping = (isset($_POST['isGrouping']) && $_POST['isGrouping'] == 1) ? true : false;
$isShowDetail = (isset($_POST['isShowDetail']) && !empty($_POST['isShowDetail'])) ? true : false;

$arrDataStructure = array();
$arrDataStructure['rowNumber'] = array('title'=>'#', 'align'=>'right', 'width'=>"40px", 'autoNumber' => true, "sortable" => false);
$arrDataStructure['code'] = array('title'=>ucwords($obj->lang['code']),  'width'=>"150px", 'dbfield' => 'code');
$arrDataStructure['warehouse'] = array('title'=>ucwords($obj->lang['warehouse']),  'width'=>"120px", 'dbfield' => 'warehousename');
//$arrDataStructure['date'] = array('title'=>ucwords($obj->lang['date']),'dbfield' => 'trdate', 'width'=>"100px",'format'=>'date');
$arrDataStructure['jocode'] = array('title'=>ucwords($obj->lang['JOCode']),  'width'=>"150px", 'dbfield' => 'jocode');
$arrDataStructure['etd'] = array('title'=>'ETD',  'dbfield' => 'etdpol', 'width'=>"100px",'format' => 'date','align'=>'center'); 
$arrDataStructure['suppliername'] = array('title'=>ucwords($obj->lang['supplier']),  'dbfield' => 'suppliername', 'width'=>"200px"); 
$arrDataStructure['shippername'] = array('title'=>ucwords($obj->lang['shipper']),  'dbfield' => 'shippername', 'width'=>"200px"); 
$arrDataStructure['pod'] = array('title'=>'POD',  'dbfield' => 'podname', 'width'=>"130px"); 
//$arrDataStructure['eta'] = array('title'=>'ETA',  'dbfield' => 'etapod', 'width'=>"100px", 'format' => 'date', 'align'=>'center'); 
//$arrDataStructure['pol'] = array('title'=>'POL',  'dbfield' => 'polname', 'width'=>"130px"); 
if(!$isGrouping){
    $arrDataStructure['quantity'] = array('title'=>ucwords($obj->lang['qty']),'dbfield' => 'qty', 'width'=>"60px", 'format'=> 'decimal');
    $arrDataStructure['currency'] = array('title'=>ucwords($obj->lang['curr']),'dbfield' => 'currencyname', 'width'=>"60px");
    $arrDataStructure['priceinunit'] = array('title'=>ucwords($obj->lang['pricePerUnit']),'dbfield' => 'priceinunit','format'=>'autodecimal', 'width'=>"100px");
    $arrDataStructure['amount'] = array('title'=>ucwords($obj->lang['amount']),'dbfield' => 'subtotalcurrency', 'width'=>"90px" ,'format'=>'autodecimal');
    $arrDataStructure['rate'] = array('title'=>ucwords($obj->lang['rate']),'dbfield' => 'rate', 'width'=>"70px" ,'format'=>'number');
    $arrDataStructure['amountIDR'] = array('title'=>ucwords($obj->lang['amount']) . ' (IDR)','dbfield' => 'subtotalidr', 'width'=>"90px" ,'format'=>'number', 'calculateTotal' => true, 'sortable' => false);
    $arrDataStructure['note'] = array('title'=>ucwords($obj->lang['note']),'dbfield' => 'trdesc', 'width'=>"200px" );
    $arrDataStructure['detailDesc'] = array('title'=>ucwords($obj->lang['description']),'dbfield' => 'description', 'width'=>"200px" );
}else{ 
    $arrDataStructure['currency'] = array('title'=>ucwords($obj->lang['curr']),'dbfield' => 'currencyname', 'width'=>"60px");
    $arrDataStructure['total'] = array('title'=>ucwords($obj->lang['total']),'dbfield' => 'grandtotal', 'width'=>"90px" ,'format'=>'number');
    $arrDataStructure['note'] = array('title'=>ucwords($obj->lang['note']),'dbfield' => 'trdesc', 'width'=>"200px");
}

$arrDataStructure['status'] = array('title'=>ucwords($obj->lang['status']),'dbfield' => 'statusname', 'width'=>"100px" );
		  
$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $obj->lang['purchaseRefundReport'];
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();

array_push($arrTemplate, $arrHeaderTemplate);

if ($isGrouping && $isShowDetail){ 
    // detail ...
    $arrDataDetailStructure = array();
    $arrDataDetailStructure['qty'] = array('title'=>ucwords($obj->lang['qty']),  'dbfield' => 'qty', 'width'=>"60px", 'format' => 'decimal' ); 
    $arrDataDetailStructure['description'] = array('title'=>ucwords($obj->lang['description']),  'dbfield' => 'description', 'width'=>"150px", 'mergeExcelCell' => 2 ); 
    $arrDataDetailStructure['currency'] = array('title'=>$obj->lang['curr'],  'dbfield' => 'currencyname', 'align'=>'center', 'width'=>"60px" ); 
    $arrDataDetailStructure['rate'] = array('title'=>$obj->lang['rate'],  'dbfield' => 'rate', 'width'=>"60px" , 'format' => 'number' ); 
    $arrDataDetailStructure['price'] = array('title'=>ucwords($obj->lang['price']),  'dbfield' => 'priceinunit', 'width'=>"100px", 'format' => 'number' );  
    $arrDataDetailStructure['subtotal'] = array('title'=>ucwords($obj->lang['total']),  'dbfield' => 'subtotalcurrency', 'width'=>"100px", 'format' => 'number' ); 
    $arrDataDetailStructure['total'] = array('title'=>ucwords($obj->lang['total']),  'dbfield' => 'subtotal', 'width'=>"80px", 'format' => 'number'); 


    $arrDetailTemplate = array();
    $arrDetailTemplate['reportWidth'] = "700px";
    $arrDetailTemplate['dataStructure'] = $arrDataDetailStructure;
    $arrDetailTemplate['total'] = array();

    array_push($arrTemplate, $arrDetailTemplate);
}

$arrStatus = $class->convertForCombobox($obj->getAllStatus(),'pkey','status');
$arrSupplier = $class->convertForCombobox($supplier->searchData($supplier->tableName.'.statuskey',1,true,'','order by name asc'),'pkey','name');
$arrWarehouse = $class->convertForCombobox($warehouse->searchData($warehouse->tableName.'.statuskey',1,true,'','order by name asc'),'pkey','name');
$arrShipper = $class->convertForCombobox($customer->searchData($customer->tableName.'.statuskey',2,true,'','order by name asc'),'pkey','name');
$arrDateType = array('1' =>  $obj->lang['transactionDate'],'2' =>  $obj->lang['etd'], '3' =>  $obj->lang['eta']);
$arrCurrency = $class->convertForCombobox($currency->searchData($currency->tableName.'.statuskey',1,true,'','order by name asc'),'pkey','name');

$arrTwigVar['inputCode'] =  $class->input('code'); 
$arrTwigVar['inputStartDate'] = $class->inputDate('trStartDate', array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputEndDate'] = $class->inputDate('trEndDate', array('etc' => 'style="text-align:center"'));  
$arrTwigVar['inputJobCode'] = $class->inputText('JOCode');
$arrTwigVar['inputIsGrouping'] =  $class->inputCheckBox('isGrouping'); 
$arrTwigVar['order'] =  $orderCriteria;
$arrTwigVar['inputShowDetail'] =  $class->inputCheckBox('isShowDetail'); 
$arrTwigVar['inputSelDateType'] =  $class->inputSelect('selDateType', $arrDateType); 
$arrTwigVar['inputSelSupplier'] =  $class->inputSelect('selSupplier[]', $arrSupplier, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputSelShipper'] =  $class->inputSelect('selShipper[]', $arrShipper, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
//$arrTwigVar['inputselSupplier'] = $class->inputText('selSupplier');  
$arrTwigVar['inputSelWarehouse'] =  $class->inputSelect('selWarehouse[]', $arrWarehouse, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));  
//$arrTwigVar['inputHidItemKey'] = $class->inputHidden('hidItemKey');
//$arrTwigVar['inputItemName'] =  $class->inputText('itemName'); 
$arrTwigVar['inputSelStatus'] =  $class->inputSelect('selStatus[]', $arrStatus, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));  
$arrTwigVar['inputSelCurrency'] =  $class->inputSelect('selCurrency[]', $arrCurrency, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox')); 
//$arrTwigVar['inputChkSN'] =  $class->inputCheckBox('chkSN',array('overwritePost' => false, 'value' => 0, 'class' => 'no-class'));  
$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate;  

if (isset($_POST) && !empty($_POST['hidAction'])){
	
	$criteria = '';
	if(isset($_POST) && !empty($_POST['code'])) {
		$criteria .= ' AND '.$obj->tableName.'.code LIKE ('.$class->oDbCon->paramString('%'.$_POST['code'].'%').')';
		array_push($arrFilterInformation,array("label" => $obj->lang['code'], 'filter' => $_POST['code']));
	}
     
    
     if(isset($_POST) && !empty($_POST['trStartDate'])){
        
        switch($_POST['selDateType']){
            case '1' : $fieldName = $obj->tableName.'.trdate';  break;
            case '2' : $fieldName = $obj->tableJobOrder.'.etdpol'; break;
            case '3' : $fieldName = $obj->tableJobOrder.'.etapod'; break;
            default : $fieldName = $obj->tableName.'.trdate';  break;
                
        }
		$criteria .= ' and '.$fieldName.' between '.$class->oDbCon->paramDate( $_POST['trStartDate'],' / ').' AND '.$class->oDbCon->paramDate( $_POST['trEndDate'],' / ','Y-m-d 23:59'); 
    		array_push($arrFilterInformation,array("label" => $arrDateType[$_POST['selDateType']], 'filter' => $_POST['trStartDate'] . ' - ' .$_POST['trEndDate'] ));
	}
    
    
   /* if(isset($_POST) && !empty($_POST['trStartDate'])){
		$criteria .= ' AND '.$obj->tableName.'.trdate between '.$class->oDbCon->paramDate( $_POST['trStartDate'],' / ').' AND '.$class->oDbCon->paramDate( $_POST['trEndDate'],' / ','Y-m-d 23:59'); 
		array_push($arrFilterInformation,array("label" => 'Tanggal', 'filter' => $_POST['trStartDate'] . ' - ' .$_POST['trEndDate'] ));
	} */
    
    if(isset($_POST) && !empty($_POST['JOCode'])) { 
        $criteria .= ' AND '.$obj->tableJobOrder.'.code LIKE ('.$class->oDbCon->paramString('%'.$_POST['JOCode'].'%').')';
	    array_push($arrFilterInformation,array("label" => $obj->lang['JOCode'], 'filter' => $_POST['JOCode']));
	} 
    
    if(isset($_POST) && !empty($_POST['selSupplier'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selSupplier']));   
        
       	$criteria .= ' AND '.$obj->tableName.'.supplierkey in('.$key.')';  

        $rsCriteria = $supplier->searchData('','',true, ' and '.$supplier->tableName.'.pkey in ('.$key.')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$statusName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => $obj->lang['supplier'], 'filter' => $statusName ));
        
	}
    
    if(isset($_POST) && !empty($_POST['selShipper'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selShipper']));   
        
       	$criteria .= ' AND '.$obj->tableJobOrder.'.customerkey in('.$key.')';  

        $rsCriteria = $customer->searchData('','',true, ' and '.$customer->tableName.'.pkey in ('.$key.')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$shipperName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => $obj->lang['shipper'], 'filter' => $shipperName ));
        
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
    
	if(isset($_POST) && !empty($_POST['selCurrency'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selCurrency']));   
        
       	$criteria .= ' AND '.$obj->tableName.'.currencykey in('.$key.')';  

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
	    array_push($arrFilterInformation,array("label" => $obj->lang['status'], 'filter' => $statusName));
        
	} 
 
	$order = 'order by '.$orderCriteria['orderBy'].' ' . (($orderCriteria['orderType'] == 1) ? 'desc' : 'asc'); 
	 
	$rs = ($isGrouping) ? $obj->searchData('','',true,$criteria,$order) : $obj->generateCommissionReport($criteria,$order); 
    $rsDetailCol = ($isShowDetail) ? $obj->getDetailCollections($rs,'refkey',$detailCriteria) : array();

    $rsTOPCol = $termOfPayment->searchData();
    $rsTOPCol = array_column($rsTOPCol,null,'pkey');
    
	$tempreport = ''; 

    $totalRs = count($rs);

    for( $i=0;$i<$totalRs;$i++) {    

        $rsTOP = $rsTOPCol[$rs[$i]['termofpaymentkey']];  
        $rs[$i]['topsaid'] = ($rsTOP['duedays'] > 0 ) ? $rsTOP['duedays'] . ' ' . $obj->lang['day(s)'] : $obj->lang['cash'];
        
        if($isGrouping && $isShowDetail){

            $rsBuyDetail = $rsDetailCol[$rs[$i]['pkey']];
            if (empty($rsBuyDetail)) continue;

            for($j=0;$j<count($rsBuyDetail);$j++){
                if ($rsBuyDetail[$j]['currencykey'] == $defaultCurrencyKey)
                    $rsBuyDetail[$j]['rate'] = 1; 
            }

            // has detail
            $rs[$i]['_detail_'] = array('arrTemplate'=>$arrDetailTemplate,'data' => $rsBuyDetail); 
        }

        if(!$isGrouping){
			$rate = ($rs[$i]['currencykey'] == CURRENCY['idr']) ? 1 : $rs[$i]['rate'];
            $rs[$i]['rate'] = ($rs[$i]['currencykey'] == CURRENCY['idr']) ? 1 :  $rs[$i]['rate'];
			$rs[$i]['subtotalidr'] = $rate * $rs[$i]['subtotalcurrency'];
		}        
        
        $return = $obj->formatReportRows(array('data' => $rs[$i]),$arrTemplate); 

        // ===== FOR EXPORT SECTION 
        array_push($dataToExport, $return['data']);  
        // ===== END FOR EXPORT SECTION

        $tempreport .= $return['html'];
        $arrTemplate[0]['total'] = $obj->arraySum($arrTemplate[0]['total'], $return['subtotal'][0]);

    }  

    $tableHeader = $twig->render('template-header.html', $arrTwigVar);
    $obj->generateReport($_POST, $tempreport, $arrTemplate,$dataToExport,$arrFilterInformation,$tableHeader);

}


echo $twig->render('reportEMKLCommission.html', $arrTwigVar);   

?>