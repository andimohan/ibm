<?php
	 
includeClass(array('EMKLJobOrder.class.php','Continent.class.php'));
$emklJobOrder = createObjAndAddToCol(new EMKLJobOrder());
$customer = createObjAndAddToCol(new Customer());
$employee = createObjAndAddToCol(new Employee());
$currency = createObjAndAddToCol(new Currency());
$creditNote = createObjAndAddToCol(new CreditNote());
$debitNote = createObjAndAddToCol(new DebitNote());

$port = createObjAndAddToCol(new Port());
$container = createObjAndAddToCol(new Container());
$emklCommission = createObjAndAddToCol(new EMKLCommission());
$currencyRate = createObjAndAddToCol(new CurrencyRate());
$continent = createObjAndAddToCol(new Continent());
$supplier = createObjAndAddToCol(new Supplier());
    
 

$obj = $emklJobOrder;
$securityObject = 'reportGrossPNLFF'; // the value of security object is manually inserted to handle 
// some modules that have different security object from that of their class

if (!$security->isAdminLogin($securityObject, 10, true));

$arrFilterInformation = array();
$detailCriteria = '';

$_POST['selStatus[]'] = array(2, 3);

if (!isset($_POST['trStartDate']) || empty($_POST['trStartDate'])) {
    $_POST['trStartDate'] = date('d / m / Y');
    $_POST['trEndDate'] = date('d / m / Y');
}

$isSellingIncludeTax = (isset($_POST) && $_POST['chkSellingIncludeTax'] == 1) ? true : false;

$arrDateType = array(
    '1' => $obj->lang['transactionDate'],
    '2' => 'ETD',
    '3' => 'ETA'
);

if (!isset($_POST['selCurrency']) || empty($_POST['selCurrency'])) {
    $_POST['selCurrency'] = CURRENCY['idr'];
}

// buat order urutan curr
$rsCurrency = $currency->searchData('', '', true, '', 'order by pkey desc');
$rsCurrencyCol = array_column($rsCurrency, null,'pkey'); 

// ===== FOR EXPORT SECTION
$dataToExport = array();

/* data structure */
$arrTemplate = array();
$arrDataStructure = array();
$arrDataStructure['rowNumber'] = array('title' => '#', 'align' => 'right', 'width' => "40px", 'autoNumber' => true, "sortable" => false);
$arrDataStructure['code'] = array('title' => ucwords($obj->lang['code']), 'width' => "150px", 'dbfield' => 'code');
$arrDataStructure['customer'] = array('title' => ucwords($obj->lang['customer']), 'width' => "250px", 'dbfield' => 'customername');
$arrDataStructure['date'] = array('title' => ucwords($obj->lang['date']), 'dbfield' => 'trdate', 'width' => "90px", 'format' => 'date');
$arrDataStructure['etd'] = array('title' => ucwords($obj->lang['etd']), 'dbfield' => 'etdpol', 'width' => "90px", 'format' => 'date', 'returnOnEmpty' => array('returnOnEmpty' => true, 'value' => ''));
$arrDataStructure['eta'] = array('title' => ucwords($obj->lang['eta']), 'dbfield' => 'etapod', 'width' => "90px", 'format' => 'date', 'returnOnEmpty' => array('returnOnEmpty' => true, 'value' => ''));
//
//foreach ($rsCurrency as $currRow) {
//    $arrDataStructure['invoice'.$currRow['pkey']] = array('title' => ucwords($currRow['name']), 'group' => ucwords($obj->lang['invoice']), 'dbfield' => 'totalinvoice'. $currRow['pkey'], 'width' => "90px", "format" => 'decimal', 'calculateTotal' => true, "sortable" => false);
//}

//
//foreach ($rsCurrency as $currRow) {
//    $arrDataStructure['cost'.$currRow['pkey']] = array('title' => ucwords($currRow['name']), 'group' => ucwords($obj->lang['cost']), 'dbfield' => 'totalcost'. $currRow['pkey'], 'width' => "90px", "format" => 'decimal', 'calculateTotal' => true, "sortable" => false);
//}
//  
$arrDataStructure['profit'] = array('title' => ucwords($currRow['name']), 'group' => ucwords($obj->lang['profit']), 'dbfield' => 'totalprofit', 'width' => "90px", "format" => 'decimal', 'calculateTotal' => true, "sortable" => false);

$arrDataStructure['sales'] = array('title' => ucwords($obj->lang['sales']), 'width' => "150px", 'dbfield' => 'salesname');
$arrDataStructure['status'] = array('title' => ucwords($obj->lang['status']), 'dbfield' => 'statusname', 'width' => "100px");

$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $obj->lang['grossPLReport'];
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();

array_push($arrTemplate, $arrHeaderTemplate);

$arrStatus = $class->convertForCombobox($obj->getAllStatus(), 'pkey', 'status');
$arrCustomer = $class->convertForCombobox($customer->searchData($customer->tableName . '.statuskey', 2, true, '', 'order by name asc'), 'pkey', 'name');
$arrSales = $class->convertForCombobox($employee->searchData($employee->tableName . '.statuskey', 2, true, ' and ' . $employee->tableName . '.issales = 1 ', 'order by name asc'), 'pkey', 'name');
$arrCurrency = $class->convertForCombobox($currency->searchData($currency->tableName . '.statuskey', 1, true, '', 'order by name asc'), 'pkey', 'name');
$arrTypeOfJob =  $obj->generateComboboxOpt(array('data' => $obj->getJobType()));
$arrTransportationType =  $obj->generateComboboxOpt(array('data' => $obj->getTransportationType()));
$arrContainer = $class->convertForCombobox($obj->getLoadContainer(),'pkey','name');
$arrShipmentType = $obj->generateComboboxOpt(array('data' => $obj->getShipmentType()));
$arrPort = $class->convertForCombobox($port->searchData($port->tableName.'.statuskey', 1, true, '', 'order by name asc'), 'pkey', 'name');
$arrCreated = $class->convertForCombobox($employee->searchData($employee->tableName . '.statuskey', 2, true, '', 'order by name asc'), 'pkey', 'name');
$arrContinent = $class->convertForCombobox($continent->searchData($continent->tableName . '.statuskey', 1, true, '', 'order by name asc'), 'pkey', 'name');

$arrTwigVar['inputJOCode'] = $class->inputText('joCode');
$arrTwigVar['inputSelDateType'] = $class->inputSelect('selDateType', $arrDateType);
$arrTwigVar['inputStartDate'] = $class->inputDate('trStartDate', array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputEndDate'] = $class->inputDate('trEndDate', array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputSelCustomer'] = $class->inputSelect('selCustomer[]', $arrCustomer, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputSelSales'] = $class->inputSelect('selSales[]', $arrSales, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputSelAgent'] = $class->inputSelect('selAgent[]', $arrCustomer, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputSelPol'] = $class->inputSelect('selPol[]', $arrPort, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputMBLNumber'] = $class->inputText('mblNumber');
$arrTwigVar['inputSelCurrency'] = $class->inputSelect('selCurrency', $arrCurrency);
$arrTwigVar['inputSelTypeOfJob'] =  $class->inputSelect('selTypeOfJob[]', $arrTypeOfJob, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));   
$arrTwigVar['inputSelTransportationType'] =  $class->inputSelect('selTransportationType[]', $arrTransportationType, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));   
$arrTwigVar['inputSelShipmentType'] =  $class->inputSelect('selShipmentType[]', $arrShipmentType, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));   
$arrTwigVar['inputSelContainerType'] = $class->inputSelect('selContainerType[]', $arrContainer, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputSelCreated'] = $class->inputSelect('selCreated[]', $arrCreated, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputSelContinentPOL'] = $class->inputSelect('selContinentPOL[]', $arrContinent, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));

$arrTwigVar['inputSelStatus'] = $class->inputSelect('selStatus[]', $arrStatus, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputChkSellingIncludeTax'] =  $class->inputCheckBox('chkSellingIncludeTax',array('overwritePost' => false, 'value' => 0, 'class' => 'no-class'));  

$arrTwigVar['arrTemplate'] = $arrHeaderTemplate;


if (isset($_POST) && !empty($_POST['hidAction'])) {

    $criteria = '';
    $criteriaArr = array();

    // untuk pencarian berdasarkan kode
    array_push($criteriaArr, array(
        'postVariable' => 'joCode',
        'fieldName' => $obj->tableName . '.code',
        'label' => $obj->lang['code']
    ));


    if (isset($_POST) && !empty($_POST['trStartDate'])) {

            switch ($_POST['selDateType']) {
                case '1':
                    $fieldName = $obj->tableName . '.trdate';
                    break;
                case '2':
                    $fieldName = $obj->tableName . '.etdpol';
                    break;
                case '3':
                    $fieldName = $obj->tableName . '.etapod';
                    break;
                default:
                    $fieldName = $obj->tableName . '.trdate';
                    break;
            }

            $tempCriteria = ' and ' . $fieldName . ' between ' . $class->oDbCon->paramDate($_POST['trStartDate'], ' / ') . ' AND ' . $class->oDbCon->paramDate($_POST['trEndDate'], ' / ');
        


        array_push($criteriaArr, array(
            'postVariable' => 'trStartDate',
            'criteria' => $tempCriteria,
            'label' => $arrDateType[$_POST['selDateType']],
            'filter' => $_POST['trStartDate'] . ' - ' . $_POST['trEndDate'],
            'type' => 'criteria'
        ));
    }


    array_push($criteriaArr, array(
        'postVariable' => 'selCustomer',
        'fieldName' => $obj->tableName . '.customerkey',
        'label' => $obj->lang['shipper'],
        'useArrayKey' => array('obj' => $customer)
    ));

    array_push($criteriaArr, array(
        'postVariable' => 'selSales',
        'fieldName' => $obj->tableName . '.saleskey',
        'label' => $obj->lang['salesman'],
        'useArrayKey' => array('obj' => $employee)
    ));

    array_push($criteriaArr, array('postVariable' => 'selAgent', 
        'fieldName' => $obj->tableName.'.agentkey', 
        'label' => $obj->lang['agent'], 
        'useArrayKey' => array('obj' => $customer) 
    ));

    // untuk pencarian berdasarkan nama
    array_push($criteriaArr, array(
        'postVariable' => 'mblNumber',
        'fieldName' => $obj->tableName . '.mblnumber',
        'label' => $obj->lang['mbl']
    ));

    if (isset($_POST) && !empty($_POST['selCurrency'])) {

        $key = $class->oDbCon->paramString($_POST['selCurrency']);

        $rsCriteria = $currency->searchData('', '', true, ' and ' . $currency->tableName . '.pkey = ' . $key . ' ');
        ;

        $arrTempStatus = array();
        for ($k = 0; $k < count($rsCriteria); $k++) {
            array_push($arrTempStatus, $rsCriteria[$k]['name']);
        }

        $arrCurrencyRate = $currencyRate->getCurrencyLastRate($_POST['selCurrency'], $_POST['trEndDate']);
        $rateAmount = $class->formatNumber($arrCurrencyRate[0]['rate']);

        $currencyName = implode(", ", $arrTempStatus);

        array_push($criteriaArr, array(
            'postVariable' => 'selCurrency',
            'criteria' => '',
            'label' => $obj->lang['rate'],
            'filter' => $rateAmount,
            'type' => 'criteria'
        ));

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

    if (isset($_POST) && !empty($_POST['selPol'])) {


        $criteria .= ' AND (
                             ' . $obj->tableName . '.polkey in (' . $class->oDbCon->paramString($_POST['selPol'], ',') . ') or
                             ' . $obj->tableName . '.podkey in (' . $class->oDbCon->paramString($_POST['selPol'], ',') . ')  
                      )';

        $arrPort = array();
        $arrPort = array_merge($arrPort, $_POST['selPol']);

        $rsCriteria = $port->searchDataRow(
            array($port->tableName . '.name'),
            ' and ' . $port->tableName . '.pkey in (' . $class->oDbCon->paramString($arrPort, ',') . ')'
        );


        $arrTempStatus = array();
        for ($k = 0; $k < count($rsCriteria); $k++)
            array_push($arrTempStatus, $rsCriteria[$k]['name']);

        $statusName = implode(", ", $arrTempStatus);

        array_push($arrFilterInformation, array("label" => ucwords($class->lang['port']), 'filter' => $statusName));

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

    array_push($criteriaArr, array(
        'postVariable' => 'selCreated',
        'fieldName' => $obj->tableName . '.createdby',
        'label' => $obj->lang['createdBy'],
        'useArrayKey' => array('obj' => $employee)
    ));

    array_push($criteriaArr, array(
        'postVariable' => 'selStatus',
        'type' => 'status'
    ));


    
    $obj->createReportCriteria($criteria, $arrFilterInformation, $criteriaArr);
    
    $orderBy = (!empty($_POST['hidOrderBy'])) ? $obj->oDbCon->paramOrder($_POST['hidOrderBy']) : 'etdpol'; // order by harus dr kolom yg terdaftar saja
    $orderType = (isset($_POST['hidOrderType']) && !empty($_POST['hidOrderType']) && $_POST['hidOrderType'] == 1) ? 'desc' : 'asc';

    $order = 'order by ' . $orderBy . ' ' . $orderType;
    //$obj->setLog($criteria, true);
    //$rs = $obj->searchData('', '', true, $criteria, $order);
    $rs = $obj->getDataForGrossPNLFFReport($criteria, $order);

    $arrPkey = array_column($rs, 'pkey');

    // selling
    $rsJobOrderSelling = $obj->getTotalSelling($arrPkey, false);
    $arrActiveCurrInvoiced = filterAvailableCurrency($rsJobOrderSelling);
         
    // buying
    $rsBuying = $obj->getAmountCost($arrPkey, false);
    $arrActiveCurrCost = filterAvailableCurrency($rsBuying);
    
    
    $rsJobOrderSellingIDR = $obj->getTotalSelling($arrPkey);
//    $rsJobOrderSellingIDR = array_column($rsJobOrderSellingIDR,null,'jokey');
    $rsJobOrderSellingIDR = $obj->reindexDetailCollections($rsJobOrderSellingIDR, 'jokey'); // biar konsisten saja
    
    $rsBuyingIDR = $obj->getAmountCost($arrPkey);
    $rsBuyingIDR = $obj->reindexDetailCollections($rsBuyingIDR, 'jokey');
    
    // DN
    $rsDN = $debitNote->getSourceTransaction($arrPkey, array(2,3)); 
    
    $arrActiveCurrDN = filterAvailableCurrency($rsDN);
    $rsDebitNote = $obj->reindexDetailCollections($rsDN, 'sokey');
    
    // CN
    $rsTempCN = $creditNote->getCreditNoteByEMKLJO($arrPkey, '	and ' . $creditNote->tableName . '.statuskey in (2,3)');
    
    // normalin dulu strukturnya
    $rsCN = array(); 
    $arrActiveCurrCN = array();
    foreach($rsTempCN as $row){
        foreach($row as $datarow){
            array_push($arrActiveCurrCN,array('pkey'=>$datarow['currencykey'],'name' =>$rsCurrencyCol[$datarow['currencykey']]['name']));
            array_push($rsCN, $datarow);
        }
    }
    $arrActiveCurrCN = array_unique($arrActiveCurrCN);
    
    
    $rsCreditNote = $obj->reindexDetailCollections($rsCN, 'jokey');


//    $reindexSelling = $rsJobOrderSellingIDR;
    // biar stardar tetep break per currency dan JO
    $reindexSelling = array();
    foreach ($rsJobOrderSelling as $data) {
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
    

    $reindexDN = array();
    foreach ($rsDN as $data) {
        $jokey = $data['sokey'];
        $currencyKey = $data['currencykey'];

        if (!isset($reindexDN[$jokey])) {
            $reindexDN[$jokey] = [];
        }
        if (!isset($reindexDN[$jokey][$currencyKey])) {
            $reindexDN[$jokey][$currencyKey] = [];
        }
        $reindexDN[$jokey][$currencyKey][] = $data;
    }

    $reindexCN = array();
    foreach ($rsCN as $data) {
        $jokey = $data['jokey'];
        $currencyKey = $data['currencykey'];

        if (!isset($reindexCN[$jokey])) {
            $reindexCN[$jokey] = [];
        }
        if (!isset($reindexCN[$jokey][$currencyKey])) {
            $reindexCN[$jokey][$currencyKey] = [];
        }
        $reindexCN[$jokey][$currencyKey][] = $data;
    }

    $reindexBuying = array();
    foreach ($rsBuying as $data) {
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
    


    $tempreport = ''; 
    if (empty($rs))
        $tempreport .= '<tr class="report-row rewrite-row"><td colspan="' . count($arrHeaderTemplate['dataStructure']) . '"></td></tr>';
  
    $arrTempStructure = array(); 
    foreach ($arrActiveCurrInvoiced as $currRow) 
        $arrTempStructure['selling'.$currRow['pkey']] = array('title' => strtoupper($currRow['name']), 'group' => ucwords($obj->lang['selling']), 'dbfield' => 'totalselling'. $currRow['pkey'], 'width' => "90px", "format" => 'decimal', 'calculateTotal' => true, "sortable" => false);

//        foreach ($arrActiveCurrInvoiced as $currRow) 
//        $arrTempStructure['invoice'.$currRow['pkey']] = array('title' => strtoupper($currRow['name']), 'group' => ucwords($obj->lang['invoice']), 'dbfield' => 'totalinvoice'. $currRow['pkey'], 'width' => "90px", "format" => 'decimal', 'calculateTotal' => true, "sortable" => false);
  
    foreach ($arrActiveCurrCost as $currRow) 
        $arrTempStructure['cost'.$currRow['pkey']] = array('title' => strtoupper($currRow['name']), 'group' => ucwords($obj->lang['cost']), 'dbfield' => 'totalcost'. $currRow['pkey'], 'width' => "90px", "format" => 'decimal', 'calculateTotal' => true, "sortable" => false);
 
    foreach ($arrActiveCurrDN as $currRow) 
        $arrTempStructure['debitnote'.$currRow['pkey']] = array('title' => ucwords($currRow['name']), 'group' => ucwords($obj->lang['debitNote']), 'dbfield' => 'totaldebitnote'. $currRow['pkey'], 'width' => "90px", "format" => 'decimal', 'calculateTotal' => true, "sortable" => false);
 

    foreach ($arrActiveCurrCN as $currRow) 
        $arrTempStructure['creditnote'.$currRow['pkey']] = array('title' => ucwords($currRow['name']), 'group' => ucwords($obj->lang['creditNote']), 'dbfield' => 'totalcreditnote'. $currRow['pkey'], 'width' => "90px", "format" => 'decimal', 'calculateTotal' => true, "sortable" => false);
 
        
    $arrReturn = $obj->insertReportColumns(6, $arrDataStructure, $arrTempStructure,$twig,$arrTwigVar,  $arrHeaderTemplate);
    $arrTemplate = $arrReturn['tableTemplate']; 
    
    
    $totalRs = count($rs);
    for ($i = 0; $i < $totalRs; $i++) {
        
        $sokey = $rs[$i]['pkey'];
  
        $rs[$i]['code'] = '<a href="/admin/print/'.(($rs[$i]['jobtypekey'] == EMKL['jobType']['import']) ? 'emklJobOrderImport' : 'emklJobOrderExport').'/'.$rs[$i]['pkey'].'" target="_blank">'.$rs[$i]['code'].'</a>';
         
        
        $rsDNCol = $reindexDN[$sokey]; 
        $rsCNCol = $reindexCN[$sokey];
        $rsSellingCol = $reindexSelling[$sokey];
        $rsBuyingCol = $reindexBuying[$sokey];
   
        $rsSellingIDRCol =  $rsJobOrderSellingIDR[$sokey];
        
        $rsBuyingIDRCol = $rsBuyingIDR[$sokey];
        $rsCreditNoteCol = $rsCreditNote[$sokey];
        $rsDebitNoteCol = $rsDebitNote[$sokey];
 
        $sellingIDR = ($isSellingIncludeTax) ? $rsSellingIDRCol[0]['total'] :  $rsSellingIDRCol[0]['beforetaxtotal'];
        $buyingIDR = $rsBuyingIDRCol[0]['amount'];
        
        $amountCreditNote = 0 ;
        foreach($rsCreditNoteCol as $cnRow)
            $amountCreditNote += $cnRow['totalcredit'] * $cnRow['rate'];
        
        $amountDebitNote = 0;
        foreach($rsDebitNoteCol as $dnRow)
            $amountDebitNote += $dnRow['totaldebit'] * $dnRow['rate'];

        //profit
        $profit = $sellingIDR - $buyingIDR - $amountCreditNote + $amountDebitNote;
        
        foreach ($rsCurrency as $currRow) { 
            // loop setiap currency di DN CN
            
            $dnAmount = 0;
            foreach($rsDNCol[$currRow['pkey']] as $dnRow) 
                $dnAmount += $dnRow['totaldebit']; 
            
            $cnAmount = 0;
            foreach($rsCNCol[$currRow['pkey']] as $cnRow) 
                $cnAmount += $cnRow['totalcredit']; 
            
            $rs[$i]['totalselling' . $currRow['pkey']] = (isset($rsSellingCol[$currRow['pkey']])) ? ( ($isSellingIncludeTax) ? $rsSellingCol[$currRow['pkey']][0]['total'] : $rsSellingCol[$currRow['pkey']][0]['beforetaxtotal']) : 0;
            $rs[$i]['totaldebitnote' . $currRow['pkey']] = $dnAmount;
            $rs[$i]['totalcreditnote' . $currRow['pkey']] = $cnAmount;
            $rs[$i]['totalcost' . $currRow['pkey']] = (isset($rsBuyingCol[$currRow['pkey']])) ?  $rsBuyingCol[$currRow['pkey']][0]['amount'] : 0;
 
        }
         
        $rs[$i]['totalprofit' ] = $profit;

        $return = $obj->formatReportRows(array('data' => $rs[$i]), $arrTemplate);

        // ===== FOR EXPORT SECTION 
        array_push($dataToExport, $return['data']);
        // ===== END FOR EXPORT SECTION

        $tempreport .= $return['html'];
        $arrTemplate[0]['total'] = $obj->arraySum($arrTemplate[0]['total'], $return['subtotal'][0]);

    }

    $tableHeader = $twig->render('template-header.html', $arrTwigVar);
    $obj->generateReport($_POST, $tempreport, $arrTemplate, $dataToExport, $arrFilterInformation, $tableHeader);

}


function filterAvailableCurrency($rs){
    global $rsCurrency;
    
    $rsActiveCurrInvoiced = array_unique(array_column($rs,'currencykey'));
    $arrActiveCurrInvoiced = array();
    // urutkan ulang
    foreach($rsCurrency as $currRow){
        if (in_array($currRow['pkey'],$rsActiveCurrInvoiced ))
            array_push($arrActiveCurrInvoiced,$currRow );
    }
    
    return $arrActiveCurrInvoiced;
}

echo $twig->render('@custom/reportGrossPNLFF.html', $arrTwigVar);

?>
