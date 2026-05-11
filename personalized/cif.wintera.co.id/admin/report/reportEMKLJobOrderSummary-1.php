<?php

includeClass(array('EMKLJobOrder.class.php','Customer.class.php', 'Port.class.php', 'Container.class.php', 'EMKLCommission.class.php', 'Currency.class.php', 'CurrencyRate.class.php', 'Continent.class.php'));
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
    
$obj = $emklJobOrder;
$securityObject = 'reportEMKLJobOrderSummary'; // the value of security object is manually inserted to handle 
// some modules that have different security object from that of their class

if (!$security->isAdminLogin($securityObject, 10, true));

$arrFilterInformation = array();
$detailCriteria = '';
$_POST['selStatus[]'] = array(1, 2, 3);

if (!isset($_POST['selCurrency']) || empty($_POST['selCurrency'])) {
    $_POST['selCurrency'] = CURRENCY['idr'];
}

$arrDateType = array(
    '1' => $obj->lang['transactionDate'],
    '2' => 'ETD',
    '3' => 'ETA',
    '4' => 'Sail Date'
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
$arrDataStructure['teus'] = array('title' => 'Teus', 'width' => "100px", 'align' => 'right', 'dbfield' => 'teus','format' => 'decimal','calculateTotal' => true, "sortable" => false);
$arrDataStructure['selling'] = array('title' => ucwords($obj->lang['selling']), 'dbfield' => 'totalselling', 'align' => 'right', 'width' => "120px", 'format' => 'number', 'calculateTotal' => true, "sortable" => false);
$arrDataStructure['buying'] = array('title' => ucwords($obj->lang['buying']), 'dbfield' => 'totalbuying', 'align' => 'right', 'width' => "120px", 'format' => 'number', 'calculateTotal' => true, "sortable" => false);
$arrDataStructure['refund'] = array('title' => ucwords($obj->lang['refund']), 'dbfield' => 'refund', 'align' => 'right', 'width' => "120px", 'format' => 'number', 'calculateTotal' => true, "sortable" => false);
$arrDataStructure['grossProfit'] = array('title' => ucwords($obj->lang['grossProfit']), 'dbfield' => 'grossprofit', 'align' => 'right', 'width' => "120px", 'format' => 'number', 'calculateTotal' => true, "sortable" => false);
//$arrDataStructure['debitNote'] = array('title' => ucwords($obj->lang['debitNote']), 'dbfield' => 'totaldebitnote', 'align' => 'right', 'width' => "120px", 'format' => 'number', 'calculateTotal' => true, "sortable" => false);
//$arrDataStructure['handling'] = array('title' => ucwords($obj->lang['handling']), 'dbfield' => 'handling', 'align' => 'right', 'width' => "120px", 'format' => 'number', 'calculateTotal' => true, "sortable" => false);
//$arrDataStructure['netProfit'] = array('title' => 'Net Profit', 'dbfield' => 'profit', 'align' => 'right', 'width' => "120px", 'format' => 'number', 'calculateTotal' => true, "sortable" => false);
$arrDataStructure['commodity'] = array('title' => ucwords($obj->lang['commodity']), 'width' => "100px", 'dbfield' => 'commodityname');
$arrDataStructure['fn'] = array('title' => 'F / N', 'width' => "60px", 'align' => 'center', 'dbfield' => 'fn');
//$arrDataStructure['cs'] = array('title' => 'C / S', 'width' => "60px", 'align' => 'center', 'dbfield' => 'cs');
$arrDataStructure['sales'] = array('title' => ucwords($obj->lang['sales']), 'width' => "100px", 'dbfield' => 'salesname');
$arrDataStructure['agent'] = array('title' => ucwords($obj->lang['agent']), 'width' => "150px", 'dbfield' => 'agentname');
$arrDataStructure['shippingLine'] = array('title' => ucwords($obj->lang['shippingLine']), 'width' => "200px", 'dbfield' => 'carriername');
$arrDataStructure['created'] = array('title' => ucwords($obj->lang['createdBy']), 'dbfield' => 'createdname', 'width' => "100px");
$arrDataStructure['status'] = array('title' => ucwords($obj->lang['status']), 'dbfield' => 'statusname', 'width' => "100px");

$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $obj->lang['jobOrderSummaryReport'];
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();

array_push($arrTemplate, $arrHeaderTemplate);

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
    $rsPurchaseRefund = $obj->reindexDetailCollections($rsPurchaseRefund, 'refkey');

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

    $currencykey = $_POST['selCurrency'];
    $date = $_POST['trEndDate'];
    $arrCurrencyRate = $currencyRate->getCurrencyLastRate($currencykey, $date);
    $rate = $arrCurrencyRate[0]['rate'];

    $totalRs = count($rs);
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
        $rsPurchaseRefundCol = (isset($rsPurchaseRefund[$sokey])) ? $rsPurchaseRefund[$sokey] : array();

        // if (empty($rsDetailCol))
        //     return;
 
        
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
            // $teus = $obj->formatNumber($totalTeus, 2);
            // array_push($arrTeus, $teus);
        }

        $totalRefund = 0;
        
        if(!empty($rsPurchaseRefundCol)){ 
            $totalRefund = $rsPurchaseRefundCol[0]['grandtotal'];
            if($rsPurchaseRefundCol[0]['currencykey'] <> CURRENCY['idr']) {
                $totalRefund *= $rsPurchaseRefundCol[0]['rate'];
            }
        }


        $rs[$i]['teus'] = $sumTeus; 
        $rs[$i]['lclwgt'] =  $rs[$i]['volume'];
        $rs[$i]['fn'] = $FN;
        $rs[$i]['hblcode'] = implode('<br> ', $arrHBLCode); 
        $rs[$i]['commodityname'] = implode('<br> ', $arrCommodity);
        // $rs[$i]['refund'] = $totalRefund;
        $rs[$i]['refund'] = ($totalRefund / $rate);

        $rs[$i]['totalselling'] = ($rs[$i]['totalselling'] / $rate);
        $rs[$i]['totalbuying'] = ($rs[$i]['totalbuying'] / $rate);
        $rs[$i]['grossprofit'] = ($rs[$i]['grossprofit'] / $rate);
        $rs[$i]['totaldebitnote'] = ($rs[$i]['totaldebitnote'] / $rate);

        $netProfit = ($rs[$i]['grossprofit'] + $rs[$i]['totaldebitnote']) - $totalRefund;
        $rs[$i]['netprofit'] = ($netProfit / $rate);

        $return = $obj->formatReportRows(array('data' => $rs[$i]), $arrTemplate);

        // ===== FOR EXPORT SECTION 
        array_push($dataToExport, $return['data']);
        // ===== END FOR EXPORT SECTION

        $tempreport .= $return['html'];
        $arrTemplate[0]['total'] = $obj->arraySum($arrTemplate[0]['total'], $return['subtotal'][0]);
    
    }

    $obj->generateReport($_POST, $tempreport, $arrTemplate, $dataToExport, $arrFilterInformation);

} else {
    $_POST['trStartDate'] = date('d / m / Y');
    $_POST['trEndDate'] = date('d / m / Y');
}

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

$arrTwigVar['arrTemplate'] = $arrHeaderTemplate;


echo $twig->render('reportEMKLJobOrderSummary.html', $arrTwigVar);


?>