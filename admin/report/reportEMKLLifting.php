<?php

include '../../_config.php';
include '../../_include-v2.php';

includeClass(array('EMKLJobOrder.class.php', 'Customer.class.php', 'Supplier.class.php', 'Port.class.php', 'Container.class.php', 'EMKLCommission.class.php', 'Currency.class.php', 'CurrencyRate.class.php', 'Continent.class.php'));
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


include '_global.php';


$obj = $emklJobOrder;
$securityObject = 'reportEMKLLifting'; // the value of security object is manually inserted to handle 
// some modules that have different security object from that of their class

if (!$security->isAdminLogin($securityObject, 10, true));

$arrFilterInformation = array();
$detailCriteria = '';
$_POST['selStatus[]'] = array(1, 2, 3);

if (!isset($_POST['trStartDate']) || empty($_POST['trStartDate'])) {
    $_POST['trStartDate'] = date('d / m / Y');
    $_POST['trEndDate'] = date('d / m / Y');
}

$arrDateType = array(
    '1' => $obj->lang['transactionDate'],
    '2' => 'ETD',
    '3' => 'ETA',
    '4' => 'Sail Date'
);

$arrGroupType = array(
    '1' => $obj->lang['customer'],
    '2' => $obj->lang['agent'],
    '3' => $obj->lang['continent']. ' ' . $obj->lang['pol'],
    '4' => $obj->lang['continent']. ' ' . $obj->lang['pod'],
    '5' => $obj->lang['shippingLine'],
    '6' => $obj->lang['placeOfDelivery']
);

function getTeus($value) 
{
    $result = round($value * 0.04 * 100) / 100;
    return $result;
}

$isGrouping = (isset($_POST['isGrouping']) && !empty($_POST['isGrouping'])) ? true : false;

if(!$isGrouping) $groupType = 0;
else $groupType = (isset($_POST['selGroupType']) && !empty($_POST['selGroupType'])) ? $_POST['selGroupType'] : 1;

// ===== FOR EXPORT SECTION
$dataToExport = array();

/* data structure */
$arrTemplate = array();
$arrDataStructure = array();
$arrDataStructure['rowNumber'] = array('title' => '#', 'align' => 'right', 'width' => "40px", 'autoNumber' => true, "sortable" => false);

if(!$isGrouping){
    $arrDataStructure['code'] = array('title' => ucwords($obj->lang['code']), 'width' => "150px", 'dbfield' => 'code');
    $arrDataStructure['eta'] = array('title'=>ucwords($obj->lang['eta']),'dbfield' => 'etapod', 'width'=>"100px",'align' => 'center','format'=>'date', 'returnOnEmpty' => array('returnOnEmpty' => true, 'value' => ''));
    $arrDataStructure['etd'] = array('title'=>ucwords($obj->lang['etd']),'dbfield' => 'etdpol', 'width'=>"100px",'align' => 'center','format'=>'date', 'returnOnEmpty' => array('returnOnEmpty' => true, 'value' => ''));
    $arrDataStructure['saildate'] = array('title'=>ucwords($obj->lang['sailDate']),'dbfield' => 'saildate', 'width'=>"100px",'align' => 'center','format'=>'date', 'returnOnEmpty' => array('returnOnEmpty' => true, 'value' => ''));
    $arrDataStructure['hblNumber'] = array('title' => ucwords($obj->lang['hblNumber']), 'width' => "150px", 'dbfield' => 'hblcode');    
}

if(!$isGrouping || $groupType == 1)
    $arrDataStructure['shipper'] = array('title' => ucwords($obj->lang['customer']), 'width' => "250px", 'dbfield' => 'customername');

if(!$isGrouping || $groupType == 2)
    $arrDataStructure['agent'] = array('title' => ucwords($obj->lang['agent']), 'width' => "250px", 'dbfield' => 'agentname');

if(!$isGrouping) 
    $arrDataStructure['pol'] = array('title' => ucwords($obj->lang['pol']), 'width' => "150px", 'dbfield' => 'polname');

if(!$isGrouping || $groupType == 3)
    $arrDataStructure['continentpol'] = array('title' => ucwords($obj->lang['continent']. ' ' .$obj->lang['pol']), 'width' => "150px", 'dbfield' => 'polcontinentname');

if(!$isGrouping) 
    $arrDataStructure['pod'] = array('title' => ucwords($obj->lang['pod']), 'width' => "150px", 'dbfield' => 'podname');
  
if(!$isGrouping || $groupType == 4)
    $arrDataStructure['continentpod'] = array('title' => ucwords($obj->lang['continent']. ' ' .$obj->lang['pod']), 'width' => "150px", 'dbfield' => 'podcontinentname');

if(!$isGrouping || $groupType == 5)
    $arrDataStructure['shippingline'] = array('title' => ucwords($obj->lang['shippingLine']), 'width' => "250px", 'dbfield' => 'carriername');

if(!$isGrouping || $groupType == 6)
    $arrDataStructure['placeOfDelivery'] = array('title' => ucwords($obj->lang['placeOfDelivery']), 'width' => "250px", 'dbfield' => 'placeofdeliveryname');


$arrDataStructure['lclwgt'] = array('title' => 'LCL / WGT', 'width' => "80px", 'align' => 'right', 'dbfield' => 'lclwgt', 'format' => 'decimal', 'calculateTotal' => true, "sortable" => false);
$arrDataStructure['airweight'] = array('title' => 'Air Weight ', 'width' => "80px", 'align' => 'right', 'dbfield' => 'airweight', 'format' => 'decimal', 'calculateTotal' => true, "sortable" => false);
$arrDataStructure['20'] = array('title' => '20\'', 'dbfield' => 'volume20', 'align' => 'right', 'width' => "60px", 'format' => 'number', 'calculateTotal' => true, "sortable" => false);
$arrDataStructure['40'] = array('title' => '40\'', 'dbfield' => 'volume40', 'align' => 'right', 'width' => "60px", 'format' => 'number', 'calculateTotal' => true, "sortable" => false);
$arrDataStructure['hq'] = array('title' => '40HQ', 'dbfield' => 'volumeHQ', 'align' => 'right', 'width' => "60px", 'format' => 'number', 'calculateTotal' => true, "sortable" => false);
$arrDataStructure['45'] = array('title' => '45\'', 'dbfield' => 'volume45', 'align' => 'right', 'width' => "60px", 'format' => 'number', 'calculateTotal' => true, "sortable" => false);
$arrDataStructure['teus'] = array('title' => 'Teus', 'width' => "100px", 'align' => 'right', 'dbfield' => 'teus', 'format' => 'decimal', 'calculateTotal' => true, "sortable" => false);

if($isGrouping) {
    $arrDataStructure['totalJobOrder'] = array('title' => ucwords($obj->lang['totalJO']), 'width' => "100px", 'align' => 'right', 'dbfield' => 'totaljo', 'format' => 'number', 'calculateTotal' => true, "sortable" => false);
}

if(!$isGrouping)
    $arrDataStructure['status'] = array('title' => ucwords($obj->lang['status']), 'dbfield' => 'statusname', 'width' => "100px");

$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $obj->lang['liftingReport'];
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();

array_push($arrTemplate, $arrHeaderTemplate);

$arrStatus = $class->convertForCombobox($obj->getAllStatus(), 'pkey', 'status');
$arrCustomer = $class->convertForCombobox($customer->searchData($customer->tableName . '.statuskey', 2, true, '', 'order by name asc'), 'pkey', 'name');
$arrPort = $class->convertForCombobox($port->searchData($port->tableName . '.statuskey', 1, true, '', 'order by name asc'), 'pkey', 'name');
$arrCurrency = $class->convertForCombobox($currency->searchData($currency->tableName . '.statuskey', 1, true, '', 'order by name asc'), 'pkey', 'name');
$arrContinent = $class->convertForCombobox($continent->searchData($continent->tableName . '.statuskey', 1, true, '', 'order by name asc'), 'pkey', 'name');
$arrSales = $class->convertForCombobox($employee->searchData($employee->tableName . '.statuskey', 2, true, ' and ' . $employee->tableName . '.issales = 1 ', 'order by name asc'), 'pkey', 'name');
$arrCreated = $class->convertForCombobox($employee->searchData($employee->tableName . '.statuskey', 2, true, '', 'order by name asc'), 'pkey', 'name');
$arrTypeOfJob = $obj->generateComboboxOpt(array('data' => $obj->getJobType()));
$arrTransportationType = $obj->generateComboboxOpt(array('data' => $obj->getTransportationType()));
$arrContainer = $class->convertForCombobox($obj->getLoadContainer(), 'pkey', 'name');
$arrSupplier = $class->convertForCombobox($supplier->searchData($supplier->tableName . '.statuskey', 1, true, '', 'order by name asc'), 'pkey', 'name');

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
$arrTwigVar['inputSelContainerType'] = $class->inputSelect('selContainerType[]', $arrContainer, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputSelSales'] = $class->inputSelect('selSales[]', $arrSales, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputSelContinentPOL'] = $class->inputSelect('selContinentPOL[]', $arrContinent, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
//$arrTwigVar['inputSelContinentPOD'] = $class->inputSelect('selContinentPOD[]', $arrContinent, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputSelCurrency'] = $class->inputSelect('selCurrency', $arrCurrency);
$arrTwigVar['inputSelCreated'] = $class->inputSelect('selCreated[]', $arrCreated, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputSelTypeOfJob'] = $class->inputSelect('selTypeOfJob[]', $arrTypeOfJob, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputSelTransportationType'] = $class->inputSelect('selTransportationType[]', $arrTransportationType, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputSelCustomer'] = $class->inputSelect('selCustomer[]', $arrCustomer, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputSelAgent'] = $class->inputSelect('selAgent[]', $arrCustomer, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputSelShippingLine'] = $class->inputSelect('selShippingLine[]', $arrSupplier, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputIsGrouping'] = $class->inputCheckBox('isGrouping');
$arrTwigVar['inputSelGroupType'] = $class->inputSelect('selGroupType', $arrGroupType);
$arrTwigVar['inputSelPlaceOfDelivery'] = $class->inputSelect('selPlaceOfDelivery[]', $arrPort, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));

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
    array_push($criteriaArr, array('postVariable' => 'selAgent', 
                               'fieldName' => $obj->tableName.'.agentkey', 
                               'label' => $obj->lang['agent'], 
                               'useArrayKey' => array('obj' => $customer) ));

    array_push($criteriaArr, array('postVariable' => 'selSales', 
                               'fieldName' => $obj->tableName.'.saleskey', 
                               'label' => $obj->lang['salesman'], 
                               'useArrayKey' => array('obj' => $employee) ));

    array_push($criteriaArr, array('postVariable' => 'selShippingLine', 
                            'fieldName' => $obj->tableName.'.carrierkey', 
                            'label' => $obj->lang['shippingLine'], 
                            'useArrayKey' => array('obj' => $supplier) ));

    array_push($criteriaArr, array(
                        'postVariable' => 'selPlaceOfDelivery',
                        'fieldName' => $obj->tableName . '.placeofdeliverykey',
                        'label' => $obj->lang['placeOfDelivery'],
                        'useArrayKey' => array('obj' => $port)
                    ));

    if (isset($_POST) && !empty($_POST['selCurrency'])) {
  
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
                                       'label' =>  $obj->lang['currency'],
                                       'filter' => $currencyName,
                                       'type' => 'criteria'));
         
         
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
	  
        array_push($criteriaArr, array('postVariable' => 'selTypeOfJob', 
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
        
        array_push($criteriaArr, array('postVariable' => 'selTypeOfJob', 
								       'criteria' => $criteria,
                                       'label' =>  $obj->lang['containerType'],
                                       'filter' => $statusName,
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

    $arrPkey = array_column($rs, 'pkey');
    $rsDetail = $obj->getDetailWithRelatedInformation($arrPkey);
    $rsDetail = $obj->reindexDetailCollections($rsDetail, 'refkey');

    $rsContainerQty = $obj->getDetailVolume($arrPkey);
    $rsContainerCols = $obj->reindexDetailCollections($rsContainerQty, 'refkey');

    $arrContainerKey = array_column($rsContainerQty, 'itemkey');
    $rsContainer = $container->searchData('', '', ' and ' . $container->tableName . '.pkey in (' . $obj->oDbCon->paramString($arrContainerKey, ',') . ') ');
    $rsContainers = $obj->reindexDetailCollections($rsContainer, 'pkey');

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
    
    $tempreport = '';

    if (empty($rs))
        $tempreport .= '<tr class="report-row rewrite-row"><td colspan="' . count($arrHeaderTemplate['dataStructure']) . '"></td></tr>';
    
    switch($groupType){
        case 1 :    
            $indexkey = 'customerkey';
            $groupby = $obj->tableName . '.customerkey';
                    break;
        case 2 :
            $indexkey = 'agentkey';
            $groupby = $obj->tableName . '.agentkey';
                    break;
        case 3 :
            $indexkey = 'polcontinentkey';
            $groupby = 'pol_continent.pkey';
                    break;
        case 4 :
            $indexkey = 'podcontinentkey';
            $groupby = 'pod_continent.pkey';
                    break;
        case 5 :
            $indexkey = 'carrierkey';
            $groupby = $obj->tableName . '.carrierkey';
                break;
        case 6 :
            $indexkey = 'placeofdeliverykey';
            $groupby = $obj->tableName . '.placeofdeliverykey';
                break;
        default :
            $indexkey = 'pkey';
            $groupby = $obj->tableName . '.pkey';
    }
    //get total JO
    $rsTotalJO = $obj->getTotalEMKLJobOrder($criteria,$groupby);
    $rsTotalJOCols = $obj->reindexDetailCollections($rsTotalJO, 'indexkey');
    
    if($groupType == 0){
        
        foreach ($rs as $key=>$data) {

            $sokey = $data['pkey'];

            $transportationtype = $data['transportationtypekey'];

            $rsDetailCol = $rsDetail[$sokey];
            $rsContainerCol =(isset($rsContainerCols[$sokey])) ? $rsContainerCols[$sokey]: array();

            $arrHBLCode = array_column($rsDetailCol,'hbl');   
            
            if (in_array($data['loadcontainertypekey'], array(EMKL['emklType']['lcl'], EMKL['emklType']['lclnc']))) {
                $rs[$key]['volume20'] = 0;
                $rs[$key]['volume40'] = 0;
                $rs[$key]['volume45'] = 0;
                $rs[$key]['volumeHQ'] = 0;
//                $rs[$key]['lclwgt'] = $data['volume'];
            } else {
                $rs[$key]['volume20'] = (!empty($arrContainerCol[$sokey]['20\''])) ? $arrContainerCol[$sokey]['20\''] : 0;
                $rs[$key]['volume40'] = (!empty($arrContainerCol[$sokey]['40\''])) ?  $arrContainerCol[$sokey]['40\''] : 0;
                $rs[$key]['volume45'] = (!empty($arrContainerCol[$sokey]['45\''])) ?  $arrContainerCol[$sokey]['45\''] : 0;
                $rs[$key]['volumeHQ'] = (!empty($arrContainerCol[$sokey]['40HQ'])) ?  $arrContainerCol[$sokey]['40HQ'] : 0;
//                $rs[$key]['lclwgt'] = 0;
            }

            $teusCBM = 0;
            if($transportationtype == EMKL['shipping']['sea']) {
                // gk perlu validasi ualng, validasi harusnya ketika normalize
                $rs[$key]['lclwgt'] = $data['volume'];
                $rs[$key]['airweight'] = 0;

                $teusCBM = getTeus($data['volume']);
                // $rs[$key]['teus'] = $teus;

            } else {
                $rs[$key]['airweight'] = $data['weightqty'];
            }

            $rs[$key]['teuscbm'] = array($teusCBM);
            $rs[$key]['sokey'] = array($sokey);
            $rs[$key]['hblcode'] = implode('<br> ', $arrHBLCode);
    
        } 

    }else{
        // hitung ulang
        $tempRs = $rs;
        $rs = array();
        foreach ($tempRs as $key=>$data) {
            
            $sokey = $data['pkey'];
            $transportationtype = $data['transportationtypekey'];
            $index = $data[$indexkey];

            $rsTotalJO = (isset($rsTotalJOCols[$index]) ? $rsTotalJOCols[$index][0]['total'] : 0);

            if(!isset($rs[$index])){
                $rs[$index] = $data; 
                
                $rs[$index]['volume20'] = $arrContainerCol[$sokey]['20\''];
                $rs[$index]['volume40'] = $arrContainerCol[$sokey]['40\''];
                $rs[$index]['volume45'] = $arrContainerCol[$sokey]['45\''];
                $rs[$index]['volumeHQ'] = $arrContainerCol[$sokey]['40HQ'];
                $rs[$index]['totaljo']  = $rsTotalJO;

                $teusCBM = 0;
                if($transportationtype == EMKL['shipping']['sea']) {
                    $rs[$index]['lclwgt'] = $data['volume'];
                    $rs[$index]['airweight'] = 0;

                    $teusCBM = getTeus($data['volume']); 

                } else {
                    $rs[$index]['lclwgt'] = 0;
                    $rs[$index]['airweight'] = $data['weightqty'];
                }

                $rs[$index]['teuscbm'] = array($teusCBM);
                $rs[$index]['sokey'] = array($sokey);
                continue;
            }
            

            $rs[$index]['volume20'] += $arrContainerCol[$sokey]['20\''];
            $rs[$index]['volume40'] += $arrContainerCol[$sokey]['40\''];
            $rs[$index]['volume45'] += $arrContainerCol[$sokey]['45\''];
            $rs[$index]['volumeHQ'] += $arrContainerCol[$sokey]['40HQ'];
            //$rs[$index]['totaljo']  += $rsTotalJO;
            
            $teusCBM = 0;
            if($transportationtype == EMKL['shipping']['sea']) {
                $rs[$index]['lclwgt'] += $data['volume'];
                $teusCBM = getTeus($data['volume']);
            } else {
                $rs[$index]['airweight'] += $data['weightqty'];
            }
            
            array_push($rs[$index]['teuscbm'], $teusCBM);
            array_push($rs[$index]['sokey'], $sokey);

        }
        
        // reset key
        $rs = array_values($rs);
    
    }

    foreach ($rs as $data) { 
        
            $sokey = $data['sokey'];
            $teusCbmRow = $data['teuscbm'];


            $sumTeusCBM = 0;
            $sumTeus = 0;
            foreach($sokey as $key => $sokeyRow){ 

                $teusCbm = $teusCbmRow[$key];
                $sumTeusCBM += $teusCbm;
                
                $rsContainerCol = (isset($rsContainerCols[$sokeyRow])) ? $rsContainerCols[$sokeyRow] : array();
                for ($c = 0; $c < count($rsContainerCol); $c++) { 
                    $rsContainerData = $rsContainers[$rsContainerCol[$c]['itemkey']]; 
                    $totalTeus = $rsContainerCol[$c]['qty'] * $rsContainerData[0]['teus'];
                    $sumTeus += $totalTeus;
                }
            }
        
        $totalTeus = $sumTeusCBM + $sumTeus;
        $data['teus'] = $totalTeus;


        $return = $obj->formatReportRows(array('data' => $data), $arrTemplate);

        // ===== FOR EXPORT SECTION 
        array_push($dataToExport, $return['data']);
        // ===== END FOR EXPORT SECTION

        $tempreport .= $return['html'];
        $arrTemplate[0]['total'] = $obj->arraySum($arrTemplate[0]['total'], $return['subtotal'][0]);

    }

    $tableHeader = $twig->render('template-header.html', $arrTwigVar);
    $obj->generateReport($_POST, $tempreport, $arrTemplate, $dataToExport, $arrFilterInformation, $tableHeader);


} 


echo $twig->render('reportEMKLLifting.html', $arrTwigVar);


?>
