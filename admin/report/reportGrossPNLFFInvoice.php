<?php

include '../../_config.php'; 
include '../../_include-v2.php';

includeClass(array('EMKLOrderInvoice.class.php','Continent.class.php'));
$emklOrderInvoice = createObjAndAddToCol(new EMKLOrderInvoice());
$emklJobOrder = createObjAndAddToCol(new EMKLJobOrder());
$customer = createObjAndAddToCol(new Customer());
$emklJobOrder = createObjAndAddToCol(new EMKLJobOrder());
$employee = createObjAndAddToCol(new Employee());
$currency = createObjAndAddToCol(new Currency());
$creditNote = createObjAndAddToCol(new CreditNote());

$supplier = createObjAndAddToCol(new Supplier());

include '_global.php';


$obj = $emklOrderInvoice;
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
$arrDataStructure['code'] = array('title' => ucwords($obj->lang['invoiceCode']), 'width' => "150px", 'dbfield' => 'code');
$arrDataStructure['date'] = array('title' => ucwords($obj->lang['date']), 'dbfield' => 'trdate', 'width' => "90px", 'format' => 'date');
$arrDataStructure['jocode'] = array('title' => ucwords($obj->lang['JOCode']), 'width' => "150px", 'dbfield' => 'salesordercodecache');
$arrDataStructure['customer'] = array('title' => ucwords($obj->lang['customer']), 'width' => "250px", 'dbfield' => 'customername');
$arrDataStructure['profit'] = array('title' => ucwords($currRow['name']), 'group' => ucwords($obj->lang['profit'] . ' IDR'), 'dbfield' => 'totalprofit', 'width' => "90px", "format" => 'decimal', 'calculateTotal' => true, "sortable" => false);
//$arrDataStructure['sales'] = array('title' => ucwords($obj->lang['sales']), 'width' => "150px", 'dbfield' => 'salesname');
$arrDataStructure['status'] = array('title' => ucwords($obj->lang['status']), 'dbfield' => 'statusname', 'width' => "100px");

$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $obj->lang['grossPLReport']. ' (Invoice)';
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();

array_push($arrTemplate, $arrHeaderTemplate);

$arrStatus = $class->convertForCombobox($obj->getAllStatus(), 'pkey', 'status');
$arrCustomer = $class->convertForCombobox($customer->searchData($customer->tableName . '.statuskey', 2, true, '', 'order by name asc'), 'pkey', 'name');
$arrCreated = $class->convertForCombobox($employee->searchData($employee->tableName . '.statuskey', 2, true, '', 'order by name asc'), 'pkey', 'name');
$arrSales = $class->convertForCombobox($employee->searchData($employee->tableName . '.statuskey', 2, true, ' and ' . $employee->tableName . '.issales = 1 ', 'order by name asc'), 'pkey', 'name');

$arrTwigVar['inputInvoiceCode'] = $class->inputText('invoiceCode');
$arrTwigVar['inputStartDate'] = $class->inputDate('trStartDate', array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputEndDate'] = $class->inputDate('trEndDate', array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputSelCustomer'] = $class->inputSelect('selCustomer[]', $arrCustomer, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputSelSales'] = $class->inputSelect('selSales[]', $arrSales, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));

$arrTwigVar['inputSelCreated'] = $class->inputSelect('selCreated[]', $arrCreated, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));

$arrTwigVar['inputSelStatus'] = $class->inputSelect('selStatus[]', $arrStatus, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputChkSellingIncludeTax'] = $class->inputCheckBox('chkSellingIncludeTax', array('overwritePost' => false, 'value' => 0, 'class' => 'no-class'));

$arrTwigVar['arrTemplate'] = $arrHeaderTemplate;

if (isset($_POST) && !empty($_POST['hidAction'])) {

    $criteria = '';
    $criteriaArr = array();

    // untuk pencarian berdasarkan kode
    array_push($criteriaArr, array(
        'postVariable' => 'invoiceCode',
        'fieldName' => $obj->tableName . '.code',
        'label' => $obj->lang['code']
    ));

    array_push($criteriaArr, array(
        'postVariable' => array('trStartDate', 'trEndDate'),
        'fieldName' => $obj->tableName . '.trdate',
        'label' => $obj->lang['period'],
        'type' => 'daterange'
    ));


    array_push($criteriaArr, array(
        'postVariable' => 'selCustomer',
        'fieldName' => $obj->tableName . '.customerkey',
        'label' => $obj->lang['shipper'],
        'useArrayKey' => array('obj' => $customer)
    ));

    array_push($criteriaArr, array(
        'postVariable' => 'selCreated',
        'fieldName' => $obj->tableName . '.createdby',
        'label' => $obj->lang['createdBy'],
        'useArrayKey' => array('obj' => $employee)
    ));

 array_push($criteriaArr, array(
            'postVariable' => 'selSales',
            'fieldName' => $obj->tableName . '.saleskey',
            'label' => $obj->lang['salesman'],
            'useArrayKey' => array('obj' => $employee)
        ));

    

    array_push($criteriaArr, array(
        'postVariable' => 'selStatus',
        'type' => 'status'
    ));




    $obj->createReportCriteria($criteria, $arrFilterInformation, $criteriaArr);

    $orderBy = (!empty($_POST['hidOrderBy'])) ? $obj->oDbCon->paramOrder($_POST['hidOrderBy']) : 'trdate'; // order by harus dr kolom yg terdaftar saja
    $orderType = (isset($_POST['hidOrderType']) && !empty($_POST['hidOrderType']) && $_POST['hidOrderType'] == 1) ? 'desc' : 'asc';

    $order = 'order by ' . $orderBy . ' ' . $orderType;

    $rs = $obj->getDataForGrossPNLFFInvoiceReport($criteria, $order);
    $arrPkey = array_column($rs, 'pkey');
    
    $rsCost = $obj->getTotalCostCIPByInvoice($arrPkey, false);
    $arrActiveCurrCost = filterAvailableCurrency($rsCost);
    
    $rsCostIDR = $obj->getTotalCostCIPByInvoice($arrPkey);
    $rsCostIDR = $obj->reindexDetailCollections($rsCostIDR, 'refkey');
    
    $rsCN = $creditNote->getCreditNoteByEMKLInvoice($arrPkey, false);
    $arrActiveCurrCN = filterAvailableCurrency($rsCN);

    $rsCNIDR = $creditNote->getCreditNoteByEMKLInvoice($arrPkey);
    $rsCNIDR = $obj->reindexDetailCollections($rsCNIDR, 'invoicekey');
    
    $rsItemDetail = $obj->getItemDetail($arrPkey, 'refheaderkey');

    //selling
    $groupedItemDetail = [];
    $groupedItemDetailIDR = [];
    foreach ($rsItemDetail as $row) {
        $refkey   = $row['refheaderkey'];
        $currency = $row['headercurrencykey'];

        $beforeTax = $row['beforetaxdetailvalue'];
        $taxValue  = $row['taxdetailvalue'];
        $afterTax  = $row['aftertaxdetailvalue'];
        $rate  = $row['rate'];
        $currencydetailkey = $row['currencykey'];


        if (!isset($groupedItemDetail[$refkey][$currency])) {
            $groupedItemDetail[$refkey][$currency] = [
                'invoicekey'   => $refkey,
                'currencykey'    => $currency,
                'beforetaxtotal' => 0,
                'taxvaluetotal'  => 0,
                'total'  => 0
            ];
        }

        //IDR
        $groupedItemDetail[$refkey][$currency]['beforetaxtotal'] += $beforeTax;
        $groupedItemDetail[$refkey][$currency]['taxvaluetotal'] += $taxValue;
        $groupedItemDetail[$refkey][$currency]['total'] += $afterTax;

        if (!isset($groupedItemDetailIDR[$refkey])) {
            $groupedItemDetailIDR[$refkey] = [
                'invoicekey'     => $refkey,
                'currencykey'    => $currency,
                'beforetaxtotal' => 0,
                'taxvaluetotal'  => 0,
                'total'          => 0
            ];
        }

        $rate = ($currency != CURRENCY['idr'] && $currencydetailkey != CURRENCY['idr']) ? $rate : 1;

        $groupedItemDetailIDR[$refkey]['beforetaxtotal'] += $beforeTax * $rate;
        $groupedItemDetailIDR[$refkey]['taxvaluetotal']  += $taxValue * $rate;
        $groupedItemDetailIDR[$refkey]['total']          += $afterTax * $rate;
    }

    $rsSelling = array_merge(...array_map('array_values', $groupedItemDetail));
    
    $rsSellingInIDR   = array_values($groupedItemDetailIDR);
    $rsSellingInIDRCols   = $obj->reindexDetailCollections($rsSellingInIDR, 'invoicekey');

    $arrActiveCurrSelling = filterAvailableCurrency($rsSelling);
   

    foreach ($rsCost as $data) {
        $invoicekey = $data['refkey'];
        $currencyKey = $data['currencykey'];

        if (!isset($reindexCost[$invoicekey])) {
            $reindexCost[$invoicekey] = [];
        }
        if (!isset($reindexCost[$invoicekey][$currencyKey])) {
            $reindexCost[$invoicekey][$currencyKey] = [];
        }
        $reindexCost[$invoicekey][$currencyKey][] = $data;
    }

    $reindexSelling= array();
    foreach ($rsSelling as $data) {
        $invoicekey = $data['invoicekey'];
        $currencyKey = $data['currencykey'];

        if (!isset($reindexSelling[$invoicekey])) {
            $reindexSelling[$invoicekey] = [];
        }
        if (!isset($reindexSelling[$invoicekey][$currencyKey])) {
            $reindexSelling[$invoicekey][$currencyKey] = [];
        }
        $reindexSelling[$invoicekey][$currencyKey][] = $data;
    }

    $reindexCN = array();
    foreach ($rsCN as $data) {
        $invoicekey = $data['invoicekey'];
        $currencyKey = $data['currencykey'];

        if (!isset($reindexCN[$invoicekey])) {
            $reindexCN[$invoicekey] = [];
        }
        if (!isset($reindexCN[$invoicekey][$currencyKey])) {
            $reindexCN[$invoicekey][$currencyKey] = [];
        }
        $reindexCN[$invoicekey][$currencyKey][] = $data;
    }

    //$obj->setLog($rsCost, true);
    $tempreport = '';
    if (empty($rs))
        $tempreport .= '<tr class="report-row rewrite-row"><td colspan="' . count($arrHeaderTemplate['dataStructure']) . '"></td></tr>';

    $arrTempStructure = array();

    $arrActiveCurrProfit = getActiveProfitCurrency($arrActiveCurrSelling, $arrActiveCurrCost, $arrActiveCurrCN);

    foreach ($arrActiveCurrSelling as $currRow)
        $arrTempStructure['selling' . $currRow['pkey']] = array('title' => strtoupper($currRow['name']), 'group' => ucwords($obj->lang['selling']), 'dbfield' => 'totalselling' . $currRow['pkey'], 'width' => "90px", "format" => 'decimal', 'calculateTotal' => true, "sortable" => false);
    
    foreach ($arrActiveCurrCost as $currRow)
        $arrTempStructure['cost' . $currRow['pkey']] = array('title' => strtoupper($currRow['name']), 'group' => ucwords($obj->lang['cost']), 'dbfield' => 'totalcost' . $currRow['pkey'], 'width' => "90px", "format" => 'decimal', 'calculateTotal' => true, "sortable" => false);
    
    foreach ($arrActiveCurrCN as $currRow)
        $arrTempStructure['creditNote' . $currRow['pkey']] = array('title' => strtoupper($currRow['name']), 'group' => ucwords($obj->lang['creditNote']), 'dbfield' => 'totalcreditnote' . $currRow['pkey'], 'width' => "90px", "format" => 'decimal', 'calculateTotal' => true, "sortable" => false);

    foreach ($arrActiveCurrProfit as $currRow) {
         $arrTempStructure['totalprofitcurr'.$currRow['pkey']] = array('title' => ucwords($currRow['name']), 'group' => ucwords($obj->lang['profit']), 'dbfield' => 'totalprofitcurr'. $currRow['pkey'], 'width' => "90px", "format" => 'decimal', 'calculateTotal' => true, "sortable" => false);
    }

    $arrReturn = $obj->insertReportColumns(5, $arrDataStructure, $arrTempStructure,$twig,$arrTwigVar,  $arrHeaderTemplate);
    $arrTemplate = $arrReturn['tableTemplate']; 

    $totalRs = count($rs);
    for ($i = 0; $i < $totalRs; $i++) {

        $invoicekey = $rs[$i]['pkey'];

        $rsCostCol = $reindexCost[$invoicekey];
        $rsCNCol = $reindexCN[$invoicekey];
        $rsSellingCol = $reindexSelling[$invoicekey];

        $rsSellingIDRCol =  $rsSellingInIDRCols[$invoicekey];
        
        $rsCostIDRCol = $rsCostIDR[$invoicekey];
        $rsCNIDRCol = $rsCNIDR[$invoicekey];

        $sellingIDR = ($isSellingIncludeTax) ? $rsSellingIDRCol[0]['total'] :  $rsSellingIDRCol[0]['beforetaxtotal'];
        $costIDR = $rsCostIDRCol[0]['amount'];
        $creditNoteIDR = $rsCNIdrCol[0]['totalcredit'];

        $profit = $sellingIDR - $costIDR - $creditNoteIDR;

        foreach ($rsCurrency as $currRow) { 
            $cost = (isset($rsCostCol[$currRow['pkey']])) ? $rsCostCol[$currRow['pkey']][0]['amount'] : 0;
            $selling = (isset($rsSellingCol[$currRow['pkey']])) ? (($isSellingIncludeTax) ? $rsSellingCol[$currRow['pkey']][0]['total'] : $rsSellingCol[$currRow['pkey']][0]['beforetaxtotal']) : 0;
            $creditNote = (isset($rsCNCol[$currRow['pkey']])) ? $rsCNCol[$currRow['pkey']][0]['totalcredit'] : 0;

            $rs[$i]['totalcost' . $currRow['pkey']] = $cost;
            $rs[$i]['totalselling' . $currRow['pkey']] = $selling;
            $rs[$i]['totalcreditnote' . $currRow['pkey']] = $creditNote;

            $profitCurr = $selling - $cost - $creditNote;
            $rs[$i]['totalprofitcurr' . $currRow['pkey']] = $profitCurr;
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

function filterAvailableCurrency($rs)
{
    global $rsCurrency;

    $rsActiveCurrInvoiced = array_unique(array_column($rs, 'currencykey'));
    $arrActiveCurrInvoiced = array();
    // urutkan ulang
    foreach ($rsCurrency as $currRow) {
        if (in_array($currRow['pkey'], $rsActiveCurrInvoiced))
            array_push($arrActiveCurrInvoiced, $currRow);
    }

    return $arrActiveCurrInvoiced;
}

function getActiveProfitCurrency($arrInvoiced, $arrCost, $arrCN) {
    $temp = [];

    foreach ([$arrInvoiced, $arrCost, $arrCN] as $arr) {
        foreach ($arr as $row) {
            $temp[$row['pkey']] = $row;
        }
    }

    return array_values($temp); 
}


echo $twig->render('reportGrossPNLFFInvoice.html', $arrTwigVar);


?>
