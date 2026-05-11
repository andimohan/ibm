<?php

includeClass(array('EMKLJobOrder.class.php','Customer.class.php', 'Port.class.php', 'Container.class.php', 'EMKLCommission.class.php', 'Currency.class.php', 'CurrencyRate.class.php', 'Continent.class.php','EMKLOrderInvoice.class.php'));
$emklJobOrder = createObjAndAddToCol(new EMKLJobOrder());
$customer = createObjAndAddToCol(new Customer());
$port = createObjAndAddToCol(new Port());
$container = createObjAndAddToCol(new Container());
$emklCommission = createObjAndAddToCol(new EMKLCommission());
$currency = createObjAndAddToCol(new Currency());
$currencyRate = createObjAndAddToCol(new CurrencyRate());
$continent = createObjAndAddToCol(new Continent());
$employee = createObjAndAddToCol(new Employee());
$supplier = createObjAndAddToCol(new Supplier());
$debitNote = createObjAndAddToCol(new DebitNote());
$creditNote = createObjAndAddToCol(new CreditNote());
$employeeCommission = createObjAndAddToCol(new ApEmployeeCommission());
$emklOrderInvoice = createObjAndAddToCol(new EMKLOrderInvoice());
    
$obj = $emklJobOrder;
$securityObject = 'reportEMKLJobOrderSummary'; // the value of security object is manually inserted to handle 
// some modules that have different security object from that of their class

if (!$security->isAdminLogin($securityObject, 10, true));

$arrFilterInformation = array();
$detailCriteria = '';
$_POST['selStatus[]'] = array(2, 3);

if (!isset($_POST['trStartDate']) || empty($_POST['trStartDate'])) {
    $_POST['trStartDate'] = date('d / m / Y');
    $_POST['trEndDate'] = date('d / m / Y');
}

if(!isset($_POST['isShowFunctionalValue'])) {
    $_POST['isShowFunctionalValue'] = true;
}

$isShowFunctionalValue = (isset($_POST['isShowFunctionalValue']) && !empty($_POST['isShowFunctionalValue'])) ? true : false;
$isSellingIncludeTax = (isset($_POST) && $_POST['chkSellingIncludeTax'] == 1) ? true : false;

if (!isset($_POST['selCurrency']) || empty($_POST['selCurrency'])) {
    $_POST['selCurrency'] = CURRENCY['idr'];
}


$rsCurrency = $currency->searchData('','',true, '', 'order by pkey desc');

$arrDateType = array(
    '1' => $obj->lang['transactionDate'],
    '2' => 'ETD',
    '3' => 'ETA',
    '4' => 'Sail Date'
);

$arrHasCurrencyAmount = array('selling' => array(),
                            'buying' => array(),
                            'taxvalue' => array(),
                            'refund' => array(),
                            'grossProfit' => array(),
                             );

// ===== FOR EXPORT SECTION
$dataToExport = array();

/* data structure */
$arrTemplate = array();
$arrDataStructure = array();
$arrDataStructure['rowNumber'] = array('title' => '#', 'align' => 'right', 'width' => "40px", 'autoNumber' => true, "sortable" => false);
$arrDataStructure['code'] = array('title' => ucwords($obj->lang['code']), 'width' => "150px", 'dbfield' => 'code');
$arrDataStructure['trdate'] = array('title' => ucwords($obj->lang['date']), 'width' => "100px", 'dbfield' => 'trdate','align' => 'center','format'=>'date');
$arrDataStructure['eta'] = array('title'=>ucwords($obj->lang['eta']),'dbfield' => 'etapod', 'width'=>"100px",'align' => 'center','format'=>'date', 'returnOnEmpty' => array('returnOnEmpty' => true, 'value' => ''));
$arrDataStructure['etd'] = array('title'=>ucwords($obj->lang['etd']),'dbfield' => 'etdpol', 'width'=>"100px",'align' => 'center','format'=>'date', 'returnOnEmpty' => array('returnOnEmpty' => true, 'value' => ''));
$arrDataStructure['saildate'] = array('title'=>ucwords($obj->lang['sailDate']),'dbfield' => 'saildate', 'width'=>"100px",'align' => 'center','format'=>'date', 'returnOnEmpty' => array('returnOnEmpty' => true, 'value' => ''));
$arrDataStructure['hblNumber'] = array('title' => ucwords($obj->lang['hblNumber']), 'width' => "150px", 'dbfield' => 'hblcode');
$arrDataStructure['mblNumber'] = array('title' => ucwords($obj->lang['mblNumber']), 'width' => "150px", 'dbfield' => 'mblnumber');
$arrDataStructure['customer'] = array('title' => ucwords($obj->lang['customer']), 'width' => "250px", 'dbfield' => 'customername');
$arrDataStructure['pol'] = array('title' => ucwords($obj->lang['pol']), 'width' => "150px", 'dbfield' => 'polname');
$arrDataStructure['pod'] = array('title' => ucwords($obj->lang['pod']), 'width' => "150px", 'dbfield' => 'podname');
$arrDataStructure['lclwgt'] = array('title' => 'LCL / WGT', 'width' => "80px", 'align' => 'right', 'dbfield' => 'lclwgt', 'format' => 'decimal', 'calculateTotal' => true, "sortable" => false);
$arrDataStructure['20'] = array('title' => '20\'', 'dbfield' => 'volume20', 'align' => 'right', 'width' => "60px", 'format' => 'number', 'calculateTotal' => true, "sortable" => false);
$arrDataStructure['40'] = array('title' => '40\'', 'dbfield' => 'volume40', 'align' => 'right', 'width' => "60px", 'format' => 'number', 'calculateTotal' => true, "sortable" => false);
$arrDataStructure['hq'] = array('title' => '40HQ', 'dbfield' => 'volumeHQ', 'align' => 'right', 'width' => "60px", 'format' => 'number', 'calculateTotal' => true, "sortable" => false);
$arrDataStructure['45'] = array('title' => '45\'', 'dbfield' => 'volume45', 'align' => 'right', 'width' => "60px", 'format' => 'number', 'calculateTotal' => true, "sortable" => false);
$arrDataStructure['teus'] = array(  'title' => 'Teus', 'width' => "100px", 'align' => 'right', 'dbfield' => 'teus','format' => 'decimal','calculateTotal' => true, "sortable" => false);


if(!$isShowFunctionalValue) {

    foreach($rsCurrency as $currRow) {     
        //selling
        $arrDataStructure['selling'.$currRow['pkey']] = array('title' => strtoupper($currRow['name']), 'group' => ucwords($obj->lang['revenue']), 'dbfield' => 'totalselling'. $currRow['pkey'], 'align' => 'right', 'width' => "120px", 'format' => 'decimal', 'calculateTotal' => true, "sortable" => false);
    }

    foreach($rsCurrency as $currRow) {     
        //buying
        $arrDataStructure['buying'.$currRow['pkey']] = array('title' => strtoupper($currRow['name']), 'group' => ucwords($obj->lang['cost']), 'dbfield' => 'totalbuying'. $currRow['pkey'], 'align' => 'right', 'width' => "120px", 'format' => 'decimal', 'calculateTotal' => true, "sortable" => false);    
    }
    
    foreach($rsCurrency as $currRow) {     
        //buying tax
        $arrDataStructure['taxvalue'.$currRow['pkey']] = array('title' => strtoupper($currRow['tax']), 'group' => ucwords($obj->lang['tax']), 'dbfield' => 'taxvalue'. $currRow['pkey'], 'align' => 'right', 'width' => "120px", 'format' => 'decimal', 'calculateTotal' => true, "sortable" => false);    
    }
   
    foreach($rsCurrency as $currRow) {     
        //refund
        $arrDataStructure['refund'.$currRow['pkey']] = array('title' => strtoupper($currRow['name']), 'group' => ucwords($obj->lang['refund']), 'dbfield' => 'refund' . $currRow['pkey'], 'align' => 'right' , 'width' => "120px", 'format' => 'decimal', 'calculateTotal' => true, "sortable" => false);
    }

    foreach($rsCurrency as $currRow) {     
        //refund
        $arrDataStructure['grossProfit'.$currRow['pkey']] = array('title' => strtoupper($currRow['name']), 'group' => ucwords($obj->lang['grossProfit']), 'dbfield' => 'grossprofit' . $currRow['pkey'], 'align' => 'right' , 'width' => "120px", 'format' => 'decimal', 'calculateTotal' => true, "sortable" => false);
    }

} else {
    $arrDataStructure['selling'] = array('title' => ucwords($obj->lang['revenue']), 'dbfield' => 'totalselling', 'align' => 'right', 'width' => "120px", 'format' => 'decimal', 'calculateTotal' => true, "sortable" => false);
    $arrDataStructure['buying'] = array('title' => ucwords($obj->lang['cost']), 'dbfield' => 'totalbuying', 'align' => 'right', 'width' => "120px", 'format' => 'decimal', 'calculateTotal' => true, "sortable" => false);
    $arrDataStructure['taxvalue'] = array('title' => ucwords($obj->lang['tax']), 'dbfield' => 'taxvalue', 'align' => 'right', 'width' => "120px", 'format' => 'decimal', 'calculateTotal' => true, "sortable" => false);
    $arrDataStructure['refund'] = array('title' => ucwords($obj->lang['refund']), 'dbfield' => 'refund', 'align' => 'right', 'width' => "120px", 'format' => 'decimal', 'calculateTotal' => true, "sortable" => false);
    $arrDataStructure['grossProfit'] = array('title' => ucwords($obj->lang['grossProfit']), 'dbfield' => 'grossprofit', 'align' => 'right', 'width' => "120px", 'format' => 'decimal', 'calculateTotal' => true, "sortable" => false);
}
//$arrDataStructure['debitNote'] = array('title' => ucwords($obj->lang['debitNote']), 'dbfield' => 'totaldebitnote', 'align' => 'right', 'width' => "120px", 'format' => 'decimal', 'calculateTotal' => true, "sortable" => false);
//$arrDataStructure['handling'] = array('title' => ucwords($obj->lang['handling']), 'dbfield' => 'handling', 'align' => 'right', 'width' => "120px", 'format' => 'decimal', 'calculateTotal' => true, "sortable" => false);
//$arrDataStructure['netProfit'] = array('title' => 'Net Profit', 'dbfield' => 'profit', 'align' => 'right', 'width' => "120px", 'format' => 'decimal', 'calculateTotal' => true, "sortable" => false);

$arrDataStructure['revenue'] = array('title' => ucwords($obj->lang['invoiced']), 'dbfield' => 'totalrevenue', 'align' => 'right', 'width' => "120px", 'textColor' => '568203', 'format' => 'decimal', 'calculateTotal' => true, "sortable" => false);
$arrDataStructure['cost'] = array('title' => ucwords($obj->lang['costReconsiliation']), 'dbfield' => 'totalcost', 'align' => 'right', 'width' => "120px", 'textColor' => 'C41E3A', 'format' => 'decimal', 'calculateTotal' => true, "sortable" => false);

//if($isShowFunctionalValue)
$arrDataStructure['costDiff'] = array('title' => ucwords($obj->lang['costDifference']), 'dbfield' => 'costdifference', 'align' => 'right', 'width' => "120px", 'textColor' => 'C41E3A', 'format' => 'decimal', 'calculateTotal' => true, "sortable" => false);
 

$arrDataStructure['commodity'] = array('title' => ucwords($obj->lang['commodity']), 'width' => "100px", 'dbfield' => 'commodityname');
$arrDataStructure['fn'] = array('title' => 'F / N', 'width' => "60px", 'align' => 'center', 'dbfield' => 'fn');
//$arrDataStructure['cs'] = array('title' => 'C / S', 'width' => "60px", 'align' => 'center', 'dbfield' => 'cs');
$arrDataStructure['sales'] = array('title' => ucwords($obj->lang['salesman']), 'width' => "100px", 'dbfield' => 'salesname');
$arrDataStructure['agent'] = array('title' => ucwords($obj->lang['agent']), 'width' => "150px", 'dbfield' => 'agentname');
$arrDataStructure['shippingLine'] = array('title' => ucwords($obj->lang['shippingLine']), 'width' => "200px", 'dbfield' => 'carriername');
$arrDataStructure['created'] = array('title' => ucwords($obj->lang['createdBy']), 'dbfield' => 'createdname', 'width' => "100px");
$arrDataStructure['status'] = array('title' => ucwords($obj->lang['status']), 'dbfield' => 'statusname', 'width' => "100px");

$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $obj->lang['jobOrderSummaryReport'];
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();

array_push($arrTemplate, $arrHeaderTemplate);


$arrStatus = $class->convertForCombobox($obj->getAllStatus(), 'pkey', 'status');
$arrCustomer = $class->convertForCombobox($customer->searchData($customer->tableName . '.statuskey', 2, true, '', 'order by name asc'), 'pkey', 'name');
$arrPort = $class->convertForCombobox($port->searchData($port->tableName.'.statuskey', 1, true, '', 'order by name asc'), 'pkey', 'name');
$arrCurrency = $class->convertForCombobox($currency->searchData($currency->tableName . '.statuskey', 1, true, '', 'order by name asc'), 'pkey', 'name');
$arrContinent = $class->convertForCombobox($continent->searchData($continent->tableName . '.statuskey', 1, true, '', 'order by name asc'), 'pkey', 'name');
$arrSales = $class->convertForCombobox($employee->searchData($employee->tableName . '.statuskey', 2, true, ' and '. $employee->tableName .'.issales = 1 ', 'order by name asc'), 'pkey', 'name');
$arrCreated = $class->convertForCombobox($employee->searchData($employee->tableName . '.statuskey', 2, true, '', 'order by name asc'), 'pkey', 'name');
$arrTypeOfJob =  $obj->generateComboboxOpt(array('data' => $obj->getJobType()));
$arrTransportationType =  $obj->generateComboboxOpt(array('data' => $obj->getTransportationType()));
$arrContainer = $class->convertForCombobox($obj->getLoadContainer(),'pkey','name');
$arrShipmentType = $obj->generateComboboxOpt(array('data' => $obj->getShipmentType()));


$arrTwigVar['inputJOCode'] = $class->inputText('joCode');
$arrTwigVar['inputHBLNumber'] = $class->inputText('hblNumber');
$arrTwigVar['inputMBLNumber'] = $class->inputText('mblNumber');
$arrTwigVar['inputSelDateType'] = $class->inputSelect('selDateType', $arrDateType);
$arrTwigVar['inputSelStatus'] = $class->inputSelect('selStatus[]', $arrStatus, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputStartDate'] = $class->inputDate('trStartDate', array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputEndDate'] = $class->inputDate('trEndDate', array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputSelPol'] = $class->inputSelect('selPol[]', $arrPort, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
//$arrTwigVar['inputSelPod'] = $class->inputSelect('selPod[]', $arrPort, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputSelCustomer'] = $class->inputSelect('selCustomer[]', $arrCustomer, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputSelAgent'] = $class->inputSelect('selAgent[]', $arrCustomer, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputSelContainerType'] = $class->inputSelect('selContainerType[]', $arrContainer, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputSelSales'] = $class->inputSelect('selSales[]', $arrSales, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputSelContinentPOL'] = $class->inputSelect('selContinentPOL[]', $arrContinent, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
//$arrTwigVar['inputSelContinentPOD'] = $class->inputSelect('selContinentPOD[]', $arrContinent, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputSelCurrency'] = $class->inputSelect('selCurrency', $arrCurrency);
$arrTwigVar['inputSelCreated'] = $class->inputSelect('selCreated[]', $arrCreated, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputSelTypeOfJob'] =  $class->inputSelect('selTypeOfJob[]', $arrTypeOfJob, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));   
$arrTwigVar['inputSelTransportationType'] =  $class->inputSelect('selTransportationType[]', $arrTransportationType, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));   
$arrTwigVar['inputSelShipmentType'] =  $class->inputSelect('selShipmentType[]', $arrShipmentType, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));   
$arrTwigVar['inputIsShowFunctionalValue'] = $class->inputCheckBox('isShowFunctionalValue', array('add-class' => 'choose-one-opt'));
$arrTwigVar['inputChkSellingIncludeTax'] =  $class->inputCheckBox('chkSellingIncludeTax',array('overwritePost' => false, 'value' => 0, 'class' => 'no-class'));  
$arrTwigVar['arrTemplate'] = $arrHeaderTemplate;


if (isset($_POST) && !empty($_POST['hidAction'])) {

    $criteria = '';
	$criteriaArr = array();
	
    // untuk pencarian berdasarkan kode
	array_push($criteriaArr, array('postVariable' => 'joCode', 
								   'fieldName' => $obj->tableName.'.code', 
								   'label' => $obj->lang['code']));
	

     if (isset($_POST) && !empty($_POST['trStartDate'])) {
             
        if($_POST['selDateType'] == 4){
				$tempCriteria = ' and if ('.$obj->tableName. '.jobtypekey = 1, '.$obj->tableName. '.etapod , '.$obj->tableName. '.etdpol ) between '.$class->oDbCon->paramDate( $_POST['trStartDate'],' / ').' AND '.$class->oDbCon->paramDate( $_POST['trEndDate'],' / '); 
			
		}else{
			switch($_POST['selDateType']){
				case '1' : $fieldName = $obj->tableName.'.trdate';  break;
				case '2' : $fieldName = $obj->tableName.'.etdpol'; break;
				case '3' : $fieldName = $obj->tableName.'.etapod'; break;
				default : $fieldName = $obj->tableName.'.trdate';  break; 
			}

			$tempCriteria = ' and '.$fieldName.' between '.$class->oDbCon->paramDate( $_POST['trStartDate'],' / ').' AND '.$class->oDbCon->paramDate( $_POST['trEndDate'],' / '); 
		}
         
        array_push($criteriaArr, array('postVariable' => 'trStartDate', 
								       'criteria' => $tempCriteria,
                                       'label' =>  $arrDateType[$_POST['selDateType']],
                                       'filter' =>  $_POST['trStartDate'] . ' - ' . $_POST['trEndDate'],
                                       'type' => 'criteria'));
    }

    
    // untuk pencarian berdasarkan nama
	array_push($criteriaArr, array('postVariable' => 'mblNumber', 
								   'fieldName' => $obj->tableName.'.mblnumber', 
								   'label' => $obj->lang['mbl']));
 
    array_push($criteriaArr, array('postVariable' => 'selCustomer', 
                               'fieldName' => $obj->tableName.'.customerkey', 
                               'label' => $obj->lang['shipper'], 
                               'useArrayKey' => array('obj' => $customer) ));

    array_push($criteriaArr, array('postVariable' => 'selSales', 
                               'fieldName' => $obj->tableName.'.saleskey', 
                               'label' => $obj->lang['salesman'], 
                               'useArrayKey' => array('obj' => $employee) ));

    array_push($criteriaArr, array('postVariable' => 'selAgent', 
                               'fieldName' => $obj->tableName.'.agentkey', 
                               'label' => $obj->lang['agent'], 
                               'useArrayKey' => array('obj' => $customer) ));
    
     if (isset($_POST) && !empty($_POST['selCurrency']) && $isShowFunctionalValue) {
  
        $key = $class->oDbCon->paramString($_POST['selCurrency']);

        $rsCriteria = $currency->searchData('', '', true, ' and ' . $currency->tableName . '.pkey = ' . $key . ' ');;

        $arrTempStatus = array();
        for ($k = 0; $k < count($rsCriteria); $k++) {
            array_push($arrTempStatus, $rsCriteria[$k]['name']);
        }

        $arrCurrencyRate = $currencyRate->getCurrencyLastRate($_POST['selCurrency'], $_POST['trEndDate']);
        $rateAmount = $class->formatNumber($arrCurrencyRate[0]['rate']);

        $currencyName = implode(", ", $arrTempStatus);
         
        array_push($criteriaArr, array('postVariable' => 'selCurrency', 
								       'criteria' => '',
                                       'label' =>  $obj->lang['rate'],
                                       'filter' => $rateAmount,
                                       'type' => 'criteria'));
         
    }

 
      
    if(isset($_POST) && !empty($_POST['selPol'])) { 
		
        
       	$criteria .= ' AND (
                                '.$obj->tableName.'.polkey in ('.$class->oDbCon->paramString($_POST['selPol'],',').') or
                                '.$obj->tableName.'.podkey in ('.$class->oDbCon->paramString($_POST['selPol'],',').')  
                         )';  
        
        $arrPort = array();
        $arrPort = array_merge($arrPort,$_POST['selPol']); 
         
        $rsCriteria = $port->searchDataRow(array($port->tableName.'.name'),
                                                ' and '.$port->tableName.'.pkey in ('.$class->oDbCon->paramString($arrPort,',').')');
        
         
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$statusName = implode(", ",$arrTempStatus); 
       
	    array_push($arrFilterInformation,array("label" => ucwords($class->lang['port']), 'filter' => $statusName));
          
	}
    
      if(isset($_POST) && (!empty($_POST['selContinentPOL']))) { 
		
        
       	$criteria .= ' AND (
                                pol_continent.pkey in ('.$class->oDbCon->paramString($_POST['selContinentPOL'],',').') or
                                pod_continent.pkey in ('.$class->oDbCon->paramString($_POST['selContinentPOL'],',').')  
                         )';  
        
        $arrPort = array();
        $arrPort = array_merge($arrPort,$_POST['selContinentPOL']);
  
        $rsCriteria = $continent->searchDataRow(array($continent->tableName.'.name'),
                                                ' and '.$continent->tableName.'.pkey in ('.$class->oDbCon->paramString($arrPort,',').')');
        
         
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$statusName = implode(", ",$arrTempStatus); 
       
	    array_push($arrFilterInformation,array("label" => ucwords($class->lang['continent']), 'filter' => $statusName));
          
	}
    
    
    
    
    if(isset($_POST) && !empty($_POST['selTypeOfJob'])) { 
		
       	$criteria .= ' AND '.$obj->tableName.'.jobtypekey in ('.$class->oDbCon->paramString($_POST['selTypeOfJob'],',').')';  

        $rsCriteria = $obj->getJobType($_POST['selTypeOfJob']);
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$statusName = implode(", ",$arrTempStatus); 
      
        array_push($criteriaArr, array('postVariable' => 'selTypeOfJob', 
								       'criteria' => $criteria,
                                       'label' =>  $obj->lang['typeOfJob'],
                                       'filter' => $statusName,
                                       'type' => 'criteria'));
          
	}
    
    

    if(isset($_POST) && !empty($_POST['selTransportationType'])) { 
		
       	$criteria .= ' AND '.$obj->tableName.'.transportationtypekey in('.$class->oDbCon->paramString($_POST['selTransportationType'],',').')';  

        $rsCriteria = $obj->getTransportationType($_POST['selTransportationType']);
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$statusName = implode(", ",$arrTempStatus); 
	  
        array_push($criteriaArr, array('postVariable' => 'selTransportationType', 
								       'criteria' => $criteria,
                                       'label' =>  $obj->lang['transportation'],
                                       'filter' => $statusName,
                                       'type' => 'criteria'));
         
        
        
	}
    
    
    if(isset($_POST) && !empty($_POST['selContainerType'])) { 
		
        
       	$criteria .= ' AND '.$obj->tableName.'.loadcontainertypekey in ('.$class->oDbCon->paramString($_POST['selContainerType'],',').')';  

        $rsCriteria = $obj->getLoadContainer($_POST['selContainerType']);
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$statusName = implode(", ",$arrTempStatus);  
        
        array_push($criteriaArr, array('postVariable' => 'selContainerType', 
								       'criteria' => $criteria,
                                       'label' =>  $obj->lang['containerType'],
                                       'filter' => $statusName,
                                       'type' => 'criteria'));
         
        
	}
    
        
    if(isset($_POST) && !empty($_POST['selShipmentType'])) { 
		
        
       	$criteria .= ' AND '.$obj->tableName.'.shipmenttypekey in ('.$class->oDbCon->paramString($_POST['selShipmentType'],',').')';  

        $rsCriteria = $obj->getShipmentType($_POST['selShipmentType']);
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$shipmentTypeName = implode(", ",$arrTempStatus);  
        
        array_push($criteriaArr, array('postVariable' => 'selShipmentType', 
								       'criteria' => $criteria,
                                       'label' =>  $obj->lang['containerType'],
                                       'filter' => $shipmentTypeName,
                                       'type' => 'criteria'));
         
        
	}
     
    
    array_push($criteriaArr, array('postVariable' => 'selCreated', 
                           'fieldName' => $obj->tableName . '.createdby', 
                           'label' => $obj->lang['createdBy'], 
                           'useArrayKey' => array('obj' => $employee) ));
    
    array_push($criteriaArr, array('postVariable' => 'selStatus',
                               'type' => 'status'));
    
    
    $obj->createReportCriteria($criteria,$arrFilterInformation,$criteriaArr); 
    
    $orderBy = (!empty($_POST['hidOrderBy'])) ? $obj->oDbCon->paramOrder($_POST['hidOrderBy']) : 'etdpol'; // order by harus dr kolom yg terdaftar saja
    $orderType = (isset($_POST['hidOrderType']) && !empty($_POST['hidOrderType']) && $_POST['hidOrderType'] == 1) ? 'desc' : 'asc';

    $order = 'order by ' . $orderBy . ' ' . $orderType;

    $rs = $obj->getDataForJobOrderSummaryReport($criteria, $order);

    $arrPkey = array_column($rs,'pkey');
    $rsDetail = $obj->getDetailWithRelatedInformation($arrPkey);
    $rsDetail = $obj->reindexDetailCollections($rsDetail, 'refkey');
 
    $rsContainerQty = $obj->getDetailVolume($arrPkey);
    
    $rsContainerCols = $obj->reindexDetailCollections($rsContainerQty, 'refkey');
    
    $arrContainerKey = array_column($rsContainerQty, 'itemkey');
    $rsContainer = $container->searchData('','', ' and ' . $container->tableName.'.pkey in ('.$obj->oDbCon->paramString($arrContainerKey,',').') ');
    $rsContainers =  $obj->reindexDetailCollections($rsContainer, 'pkey');

    $rsCommodity = $obj->getDetailCommodity($arrPkey);
    $rsCommodity = $obj->reindexDetailCollections($rsCommodity, 'refkey');

    $rsPurchaseRefund = $emklCommission->searchData('','',true, ' and ' . $emklCommission->tableName.'.statuskey in (2,3) and ' . $emklCommission->tableName.'.refkey in ('. $obj->oDbCon->paramString($arrPkey, ',') .') ');
    $rsPurchaseRefundCols = $obj->reindexDetailCollections($rsPurchaseRefund, 'refkey');

    $rsCostCIP = $obj->getTotalCostCIP($arrPkey);
    $rsCostCIPCols = $obj->reindexDetailCollections($rsCostCIP, 'joborderkey');

    $rsRevenue = $emklOrderInvoice->getRevenueByJO($arrPkey);
    $rsRevenueCols = $obj->reindexDetailCollections($rsRevenue, 'salesorderkey');

    $arrContainerCol = array();
    
    $totalContainerRows = count($rsContainerQty);
    for ($i = 0; $i < $totalContainerRows; $i++) {
        $sokey = $rsContainerQty[$i]['refkey'];
        $vol = $rsContainerQty[$i]['groupvolume'];
        $qty = $rsContainerQty[$i]['qty'];
        if (!isset($arrContainerCol[$sokey]))
            $arrContainerCol[$sokey] = array();

        if(!isset($arrContainerCol[$sokey][$vol])) $arrContainerCol[$sokey][$vol] = 0;
        
        $arrContainerCol[$sokey][$vol] += $qty;

    }

    $asIDR = ($isShowFunctionalValue) ? true : false ;
    $rsTotalSelling = $obj->getTotalSelling($arrPkey, $asIDR);
    $rsTotalSellingCols = $obj->reindexDetailCollections($rsTotalSelling, 'jokey');

    $rsTotalBuying = $obj->getAmountCost($arrPkey, $asIDR, false);
    $rsTotalBuyingCols = $obj->reindexDetailCollections($rsTotalBuying, 'jokey');

    $rsEmployeeCommission = $employeeCommission->searchData('', '', true, ' and ' . $employeeCommission->tableName . '.refkey in (' . $obj->oDbCon->paramString($arrPkey, ',') . ') and ' . $employeeCommission->tableName . '.statuskey in (1,2,3) ');
    $rsEmployeeCommissionCols = $obj->reindexDetailCollections($rsEmployeeCommission, 'refkey');

    if(!$isShowFunctionalValue) {
        $reindexSelling = array();
        foreach ($rsTotalSelling as $data) {
            $jokey = $data['jokey'];
            $currencyKey = $data['currencykey'];

            if (!isset($reindexSelling[$jokey])) {
                $reindexSelling[$jokey] = [];
            }
            if (!isset($reindexSelling[$jokey][$currencyKey])) {
                $reindexSelling[$jokey][$currencyKey] = [];
            }
            $reindexSelling[$jokey][$currencyKey][] = $data;
        }
 
        $reindexBuying = array();
        foreach ($rsTotalBuying as $data) {
            $jokey = $data['jokey'];
            $currencyKey = $data['currencykey'];

            if (!isset($reindexBuying[$jokey])) {
                $reindexBuying[$jokey] = [];
            }
            if (!isset($reindexBuying[$jokey][$currencyKey])) {
                $reindexBuying[$jokey][$currencyKey] = [];
            }
            $reindexBuying[$jokey][$currencyKey][] = $data;
        }


        $reindexRefund = array();
        foreach ($rsPurchaseRefund as $data) {
            $jokey = $data['refkey'];
            $currencyKey = $data['currencykey'];

            if (!isset($reindexRefund[$jokey])) {
                $reindexRefund[$jokey] = [];
            }
            if (!isset($reindexRefund[$jokey][$currencyKey])) {
                $reindexRefund[$jokey][$currencyKey] = [];
            }
            $reindexRefund[$jokey][$currencyKey][] = $data;
        }

        $rsDebitNoteCol = $debitNote->getSourceTransaction($arrPkey, array(2, 3));


        $reindexDebitNote = array();
        foreach ($rsDebitNoteCol as $data) {
            $jokey = $data['sokey'];
            $currencyKey = $data['currencykey'];

            if (!isset($reindexDebitNote[$jokey])) {
                $reindexDebitNote[$jokey] = [];
            }
            if (!isset($reindexDebitNote[$jokey][$currencyKey])) {
                $reindexDebitNote[$jokey][$currencyKey] = [];
            }
            $reindexDebitNote[$jokey][$currencyKey][] = $data;
        }

        // $rsCNCol =  $creditNote->getCreditNoteByEMKLJO($arrPkey);
		// foreach($rsCNCol as $row)  {
		// 	$arrJO = array_merge($arrJO, array_column($row,'jokey'));  
        // }
		// $arrJO = array_unique($arrJO);
        
        $rsCreditNoteCol = $creditNote->getCreditNoteByEMKLJO($arrPkey, '	and ' . $creditNote->tableName . '.statuskey in (2,3)');
        
        $reindexCreditNote = [];

        foreach($rsCreditNoteCol as $pkey => $rows) {
            foreach ($rows as $data) {
                $jokey = $data['jokey'];
                $currencyKey = $data['currencykey'];

                if (!isset($reindexCreditNote[$jokey])) {
                    $reindexCreditNote[$jokey] = [];
                }
                if (!isset($reindexCreditNote[$jokey][$currencyKey])) {
                    $reindexCreditNote[$jokey][$currencyKey] = [];
                }
                $reindexCreditNote[$jokey][$currencyKey][] = $data;
            }
        }
        
        $reindexEmployeeCommission = [];
        foreach ($rsEmployeeCommission as $data) {
            $jokey = $data['refkey'];
            $currencyKey = $data['currencykey'];

            if (!isset($reindexEmployeeCommission[$jokey])) {
                $reindexEmployeeCommission[$jokey] = [];
            }
            if (!isset($reindexEmployeeCommission[$jokey][$currencyKey])) {
                $reindexEmployeeCommission[$jokey][$currencyKey] = [];
            }
            $reindexEmployeeCommission[$jokey][$currencyKey][] = $data;
        }
    }

    $tempreport = '';

    if (empty($rs))
        $tempreport .= '<tr class="report-row rewrite-row"><td colspan="' . count($arrHeaderTemplate['dataStructure']) . '"></td></tr>';

    $currencykey = $_POST['selCurrency'];
    $date = $_POST['trEndDate'];
    $arrCurrencyRate = $currencyRate->getCurrencyLastRate($currencykey, $date);
    $rate = $arrCurrencyRate[0]['rate'];

    $totalRs = count($rs);
     
     if(!$isShowFunctionalValue) {
        for ($i = 0; $i < $totalRs; $i++) {   
            
            $sokey = $rs[$i]['pkey'];

            $rsSellingCol = $reindexSelling[$sokey];
            $rsBuyingCol = $reindexBuying[$sokey];
            $rsRefundCol = $reindexRefund[$sokey];
            $rsDNCol = $reindexDebitNote[$sokey];
            $rsCNCol =  $reindexCreditNote[$sokey];
            $rsEmployeeCommissionCol = $reindexEmployeeCommission[$sokey];

            foreach($rsCurrency as $currRow) {
                $currkey = $currRow['pkey'];
                $totalSelling = (isset($rsSellingCol[$currkey])) ? (($isSellingIncludeTax) ? $rsSellingCol[$currkey][0]['total'] : $rsSellingCol[$currkey][0]['beforetaxtotal']) : 0;
                $totalBuying = (isset($rsBuyingCol[$currkey])) ? $rsBuyingCol[$currkey][0]['amount'] : 0;
                $totalBuyingTax = (isset($rsBuyingCol[$currkey])) ? $rsBuyingCol[$currkey][0]['taxvalue'] : 0;
                $totalRefund = (isset($rsRefundCol[$currkey])) ? $rsRefundCol[$currkey][0]['grandtotal'] : 0;
                $totalDebitNote = 0; //$totalDN;
                $totalCreditNote = 0; //$totalCN; 
                $employeeCommission = (isset($rsEmployeeCommissionCol[$currkey])) ?$rsEmployeeCommissionCol[$currkey][0]['amount'] : 0;

                $grossProfit = $totalSelling - ($totalBuying+$totalBuyingTax) - $employeeCommission - $totalRefund - $totalCreditNote + $totalDebitNote;
            
                if($totalSelling <> 0)  $arrHasCurrencyAmount['selling'][$currkey] = true;
                if($totalBuying <> 0)  $arrHasCurrencyAmount['buying'][$currkey] = true;  
                if($totalBuyingTax <> 0)  $arrHasCurrencyAmount['taxvalue'][$currkey] = true;  
                if($totalRefund <> 0)   $arrHasCurrencyAmount['refund'][$currkey] = true;
                if($grossProfit <> 0)   $arrHasCurrencyAmount['grossProfit'][$currkey] = true;
                
                // biar gk 2x hitung
                $rs[$i]['totalselling' . $currkey] = $totalSelling;
                $rs[$i]['totalbuying' . $currkey] = $totalBuying;
                $rs[$i]['taxvalue' . $currkey] = $totalBuyingTax;
                $rs[$i]['refund' . $currkey] = $totalRefund;
                $rs[$i]['grossprofit' . $currkey] = $grossProfit; 
            }
        }
            
        foreach($rsCurrency as $currRow){
            $currkey = $currRow['pkey'];
            if(!isset($arrHasCurrencyAmount['selling'][$currkey])) unset($arrDataStructure['selling'.$currkey]);  
            if(!isset($arrHasCurrencyAmount['buying'][$currkey])) unset($arrDataStructure['buying'.$currkey]);   
            if(!isset($arrHasCurrencyAmount['taxvalue'][$currkey])) unset($arrDataStructure['taxvalue'.$currkey]);   
            if(!isset($arrHasCurrencyAmount['refund'][$currkey])) unset($arrDataStructure['refund'.$currkey]);   
            if(!isset($arrHasCurrencyAmount['grossProfit'][$currkey])) unset($arrDataStructure['grossProfit'.$currkey]);   
        }
        $arrTemplate = array(); 
        $arrHeaderTemplate['dataStructure'] = $arrDataStructure; 
        array_push($arrTemplate, $arrHeaderTemplate);
        $arrTwigVar['arrTemplate'] = $arrHeaderTemplate; 
     }
    
    // >>>>> coba hapus kolom kosong 
    
    for ($i = 0; $i < $totalRs; $i++) {

        $sokey = $rs[$i]['pkey'];
 
        switch($rs[$i]['jobtypekey']){
            case EMKL['jobType']['import'] :  $printFile = 'emklJobOrderImport'; break;
            case EMKL['jobType']['export'] :  $printFile =  'emklJobOrderExport'; break;
            default :  $printFile = 'emklJobOrderExport';
        }
        $rs[$i]['code'] = '<a href="/admin/print/'.$printFile.'/'.$rs[$i]['pkey'].'" target="_blank">'.$rs[$i]['code'].'</a>';
        
        $rsDetailCol = $rsDetail[$sokey];
        $rsCommodityCol = (isset($rsCommodity[$sokey])) ? $rsCommodity[$sokey] : array(); 
        $rsContainerCol = (isset($rsContainerCols[$sokey])) ? $rsContainerCols[$sokey]: array(); 
        $rsPurchaseRefundCols = (isset($rsPurchaseRefundCols[$sokey])) ? $rsPurchaseRefundCols[$sokey] : array();
        $rsCostCIPCol = (isset($rsCostCIPCols[$sokey])) ? $rsCostCIPCols[$sokey] : array();
        $rsRevenueCol = (isset($rsRevenueCols[$sokey])) ? $rsRevenueCols[$sokey] : array();
        $rsTotalSellingCol = (isset($rsTotalSellingCols[$sokey])) ? $rsTotalSellingCols[$sokey] : array();
        $rsTotalBuyingCol = (isset($rsTotalBuyingCols[$sokey])) ? $rsTotalBuyingCols[$sokey] : array();
        $employeeCommissionCol = (isset($rsTotalEmoloyeeCommission[$sokey])) ? $rsTotalEmoloyeeCommission[$sokey] : array();
 
        $arrHBLCode = array_column($rsDetailCol,'hbl');       
 
        if (in_array($rs[$i]['loadcontainertypekey'], array(EMKL['emklType']['lcl'], EMKL['emklType']['lclnc']))) {
            $rs[$i]['volume20'] =  0;
            $rs[$i]['volume40'] =  0;
            $rs[$i]['volume45'] =  0;
            $rs[$i]['volumeHQ'] =  0;
        } else {
            $rs[$i]['volume20'] = (!empty($arrContainerCol[$sokey]['20\''])) ? $arrContainerCol[$sokey]['20\''] : 0;
            $rs[$i]['volume40'] = (!empty($arrContainerCol[$sokey]['40\''])) ?  $arrContainerCol[$sokey]['40\''] : 0;
            $rs[$i]['volume45'] = (!empty($arrContainerCol[$sokey]['45\''])) ?  $arrContainerCol[$sokey]['45\''] : 0;
            $rs[$i]['volumeHQ'] = (!empty($arrContainerCol[$sokey]['40HQ'])) ?  $arrContainerCol[$sokey]['40HQ'] : 0;
        }

        $arrCommodity = array();
        for($k=0; $k<count($rsCommodityCol); $k++) {
            array_push($arrCommodity, $rsCommodityCol[$k]['commodityname']);
        }
        
        $FN = ($rs[$i]['shipmenttypekey'] == 1) ? 'F' : 'N';
        
        $arrTeus = array();
        $sumTeus = 0;
        for ($c = 0; $c < count($rsContainerCol); $c++) {
            //$teus = 0;
            $rsContainerData = $rsContainers[$rsContainerCol[$c]['itemkey']]; 
            $totalTeus = $rsContainerCol[$c]['qty'] * $rsContainerData[0]['teus'];
            $sumTeus += $totalTeus; 
        }
        
        $rs[$i]['teus'] = $sumTeus; 
        $rs[$i]['lclwgt'] = (($rs[$i]['transportationtypekey'] == EMKL['shipping']['sea']) ?  $rs[$i]['volume'] : $rs[$i]['weight']);
        $rs[$i]['fn'] = $FN;
        $rs[$i]['hblcode'] = implode('<br> ', $arrHBLCode); 
        $rs[$i]['commodityname'] = implode('<br> ', $arrCommodity);
        // $rs[$i]['refund'] = $totalRefund;
        
        if($isShowFunctionalValue) {
  
            $totalRefund = 0;
            $totalSelling = 0;
            $totalBuying = 0;
            $totalBuyingTax = 0;
            $totalEmployeeCommission = 0;
            $totalDebitNote = 0;
            $totalCreditNote = 0;
            
            if(!empty($rsPurchaseRefundCols)){ 
                $totalRefund = $rsPurchaseRefundCols[0]['grandtotal'];
                if($rsPurchaseRefundCols[0]['currencykey'] <> CURRENCY['idr']) {
                    $totalRefund *= $rsPurchaseRefundCols[0]['rate'];
                }
            }


            if(!empty($rsTotalSellingCol)) {
                $totalSelling = (($isSellingIncludeTax) ? $rsTotalSellingCol[0]['total'] : $rsTotalSellingCol[0]['beforetaxtotal']);
                if($rsTotalSellingCol[0]['currencykey'] <> CURRENCY['idr']) {
                    $totalSelling *= $rsTotalSellingCol[0]['rate'];
                }
            }

            if(!empty($rsTotalBuyingCol)) {
                $totalBuying = $rsTotalBuyingCol[0]['amount'];
                $totalBuyingTax = $rsTotalBuyingCol[0]['taxvalue'];
                if($rsTotalBuyingCol[0]['currencykey'] <> CURRENCY['idr']) {
                    $totalBuying *= $rsTotalBuyingCol[0]['rate'];
                    $totalBuyingTax *= $rsTotalBuyingCol[0]['rate'];
                }
            }

            if(!empty($employeeCommissionCol)) {
                $totalEmployeeCommission = $employeeCommissionCol[0]['amount'];
                if($employeeCommissionCol[0]['currencykey'] <> CURRENCY['idr']) {
                    $totalEmployeeCommission *= $employeeCommissionCol[0]['rate'];
                }
            }
            
            $grossProfit = $totalSelling - ($totalBuying+$totalBuyingTax) - $totalEmployeeCommission - $totalRefund - $totalCreditNote + $totalDebitNote;
            $rs[$i]['refund'] = ($totalRefund / $rate);
            $rs[$i]['totalselling'] = ($totalSelling / $rate);
            $rs[$i]['totalbuying'] = ($totalBuying / $rate);
            $rs[$i]['taxvalue'] = ($totalBuyingTax / $rate);
            $rs[$i]['grossprofit'] = ($grossProfit / $rate);
            $rs[$i]['totaldebitnote'] = ($totalDebitNote / $rate);

            //$netProfit = ($rs[$i]['grossprofit'] + $rs[$i]['totaldebitnote']) - $totalRefund;
            $netProfit = ($grossProfit + $totalDebitNote) - $totalRefund;
        } else {
 
            // udah dihitung diatas 

        }


        $rs[$i]['netprofit'] = ($netProfit / $rate);
        $rs[$i]['totalcost'] = $rsCostCIPCol[0]['amount'];
        $rs[$i]['totalrevenue'] = $rsRevenueCol[0]['amount'];
        
        //if($isShowFunctionalValue)
            
        $rs[$i]['costdifference'] =  $rs[$i]['totalcost'] - $rs[$i]['totalbuying'];
        
        if (intval($rs[$i]['costdifference']) == 0)  
            $arrHeaderStyle['costdifference']['textColor'] = '000'; 
        else if ($rs[$i]['costdifference'] < 0)  
            $arrHeaderStyle['costdifference']['textColor'] = 'C41E3A'; 
        else
            $arrHeaderStyle['costdifference']['textColor'] = '568203'; 
            
        $return = $obj->formatReportRows(array('data' => $rs[$i], 'style' => $arrHeaderStyle), $arrTemplate);

        // ===== FOR EXPORT SECTION 
        array_push($dataToExport, $return['data']);
        // ===== END FOR EXPORT SECTION

        $tempreport .= $return['html'];
        $arrTemplate[0]['total'] = $obj->arraySum($arrTemplate[0]['total'], $return['subtotal'][0]);
    
    }

    $tableHeader = $twig->render('template-header.html', $arrTwigVar);
    $obj->generateReport($_POST, $tempreport, $arrTemplate, $dataToExport, $arrFilterInformation, $tableHeader);

} 


echo $twig->render('@custom/reportEMKLJobOrderSummary.html', $arrTwigVar);


?>
