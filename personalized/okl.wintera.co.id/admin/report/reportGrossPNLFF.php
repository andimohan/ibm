<?php

includeClass(array('EMKLJobOrder.class.php'));
$emklJobOrder = createObjAndAddToCol(new EMKLJobOrder());
$customer = createObjAndAddToCol(new Customer());
$employee = createObjAndAddToCol(new Employee());
$currency = createObjAndAddToCol(new Currency());
$creditNote = createObjAndAddToCol(new CreditNote());
$debitNote = createObjAndAddToCol(new DebitNote());
$serviceCategory = createObjAndAddToCol(new ServiceCategory());
// $item = createObjAndAddToCol(new Item());
$emklPurchaseOrder = createObjAndAddToCol(new EmklPurchaseOrder());
$service = createObjAndAddToCol(new Service(SERVICE));
$container = createObjAndAddToCol(new Container());

$obj = $emklJobOrder;
$securityObject = 'reportGrossPNLFF'; // the value of security object is manually inserted to handle 
// some modules that have different security object from that of their class

if (!$security->isAdminLogin($securityObject, 10, true))
    ;

$arrFilterInformation = array();
$detailCriteria = '';
$_POST['selStatus[]'] = array(2, 3);

if (!isset($_POST['trStartDate']) || empty($_POST['trStartDate'])) {
    $_POST['trStartDate'] = date('d / m / Y');
    $_POST['trEndDate'] = date('d / m / Y');
}

$arrDateType = array(
    '1' => $obj->lang['transactionDate'],
    '2' => 'ETD',
    '3' => 'ETA',
    '4' => $obj->lang['sailDate']
);

$arrFilterBy = array(
    '1' => $obj->lang['category'],
    '2' => $obj->lang['service']
);

// buat order urutan curr
$rsCurrency = $currency->searchData('', '', true, '', 'order by pkey desc');
$rsCurrencyCol = array_column($rsCurrency, null, 'pkey');

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

$arrDataStructure['volume20'] = array('title' => '20\'', 'dbfield' => 'volume20', 'width' => "60px", 'format' => 'number', 'calculateTotal' => true, "sortable" => false);
$arrDataStructure['volume40'] = array('title' => '40\'', 'dbfield' => 'volume40', 'width' => "60px", 'format' => 'number', 'calculateTotal' => true, "sortable" => false);
$arrDataStructure['volume45'] = array('title' => '45\'', 'dbfield' => 'volume45', 'width' => "60px", 'format' => 'number', 'calculateTotal' => true, "sortable" => false);
$arrDataStructure['cbm'] = array('title'=>'CBM','dbfield' => 'volume', 'width'=>"100px", 'align' =>'right', 'format' => 'decimal','calculateTotal' => true );



$arrDataStructure['grossProfit'] = array('title' => ucwords($obj->lang['grossProfit']), 'dbfield' => 'totalprofit', 'align' => 'right', 'width' => "110px", 'format' => 'number', 'calculateTotal' => true);

// $arrDataStructure['sales'] = array('title' => ucwords($obj->lang['sales']), 'width' => "150px", 'dbfield' => 'salesname');
$arrDataStructure['status'] = array('title' => ucwords($obj->lang['status']), 'dbfield' => 'statusname', 'width' => "100px");

$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $obj->lang['grossPLReport'];
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();

array_push($arrTemplate, $arrHeaderTemplate);

$arrStatus = $class->convertForCombobox($obj->getAllStatus(), 'pkey', 'status');
$arrCustomer = $class->convertForCombobox($customer->searchData($customer->tableName . '.statuskey', 2, true, '', 'order by name asc'), 'pkey', 'name');
$arrSales = $class->convertForCombobox($employee->searchData($employee->tableName . '.statuskey', 2, true, ' and ' . $employee->tableName . '.issales = 1 ', 'order by name asc'), 'pkey', 'name');
$arrService = $class->convertForCombobox($service->searchData($service->tableName . '.statuskey', 1, true, ' and ' . $service->tableName . '.itemtype = 3 ', 'order by name asc'), 'pkey', 'name');
$arrServiceCategory = $class->convertForCombobox($serviceCategory->searchData($serviceCategory->tableName . '.statuskey', 1, true, 'order by name asc'), 'pkey', 'name');

$arrTwigVar['inputJOCode'] = $class->inputText('joCode');
$arrTwigVar['inputSelDateType'] = $class->inputSelect('selDateType', $arrDateType);
$arrTwigVar['inputSelFilterBy'] = $class->inputSelect('selFilterBy', $arrFilterBy);
$arrTwigVar['inputStartDate'] = $class->inputDate('trStartDate', array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputEndDate'] = $class->inputDate('trEndDate', array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputSelCustomer'] = $class->inputSelect('selCustomer[]', $arrCustomer, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputSelSales'] = $class->inputSelect('selSales[]', $arrSales, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputSelStatus'] = $class->inputSelect('selStatus[]', $arrStatus, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputSelCategoryServiceSelling'] = $class->inputSelect('selCategoryServiceSelling[]', $arrServiceCategory, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputSelCategoryServiceBuying'] = $class->inputSelect('selCategoryServiceBuying[]', $arrServiceCategory, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputSelServiceSelling'] = $class->inputSelect('selServiceSelling[]', $arrService, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputSelServiceBuying'] = $class->inputSelect('selServiceBuying[]', $arrService, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));

$arrTwigVar['arrTemplate'] = $arrHeaderTemplate;


if (isset($_POST) && !empty($_POST['hidAction'])) {

    $criteria = '';
    $criteriaArr = array();
    $arrFilterCriteriaBuying = '';
    $arrFilterCriteriaSelling = '';

    // untuk pencarian berdasarkan kode
    array_push($criteriaArr, array(
        'postVariable' => 'joCode',
        'fieldName' => $obj->tableName . '.code',
        'label' => $obj->lang['code']
    ));


    if (isset($_POST) && !empty($_POST['trStartDate'])) {

        if ($_POST['selDateType'] == 4) {
            $criteria = ' and if (' . $obj->tableName . '.jobtypekey = 1, ' . $obj->tableName . '.etapod , ' . $obj->tableName . '.etdpol ) between ' . $class->oDbCon->paramDate($_POST['trStartDate'], ' / ') . ' AND ' . $class->oDbCon->paramDate($_POST['trEndDate'], ' / ');
        } else {
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

            $criteria = ' and ' . $fieldName . ' between ' . $class->oDbCon->paramDate($_POST['trStartDate'], ' / ') . ' AND ' . $class->oDbCon->paramDate($_POST['trEndDate'], ' / ');
        }



        array_push($criteriaArr, array(
            'postVariable' => 'trStartDate',
            'criteria' => $criteria,
            'label' => $arrDateType[$_POST['selDateType']],
            'filter' => $_POST['trStartDate'] . ' - ' . $_POST['trEndDate'],
            'type' => 'criteria'
        ));
    }

    if (isset($_POST) && !empty($_POST['selCategoryServiceSelling']) && $_POST['selFilterBy'] == 1) {

        $key = implode(",", $class->oDbCon->paramString($_POST['selCategoryServiceSelling']));

        $arrFilterCriteriaSelling .= ' AND ' . $obj->tableItem . '.categorykey in(' . $key . ')';

        $rsCriteria = $serviceCategory->searchData('', '', true, ' and ' . $serviceCategory->tableName . '.pkey in (' . $key . ')');

        $arrTempStatus = array();
        for ($k = 0; $k < count($rsCriteria); $k++)
            array_push($arrTempStatus, $rsCriteria[$k]['name']);

        $categoryName = implode(", ", $arrTempStatus);
        array_push($arrFilterInformation, array("label" => $obj->lang['serviceOrderCategory'], 'filter' => $categoryName));

    }

    if (isset($_POST) && !empty($_POST['selCategoryServiceBuying']) && $_POST['selFilterBy'] == 1) {

        $key = implode(",", $class->oDbCon->paramString($_POST['selCategoryServiceBuying']));

        $arrFilterCriteriaBuying .= ' AND ' . $emklPurchaseOrder->tableItem . '.categorykey in(' . $key . ')';

        $rsCriteria = $serviceCategory->searchData('', '', true, ' and ' . $serviceCategory->tableName . '.pkey in (' . $key . ')');

        $arrTempStatus = array();
        for ($k = 0; $k < count($rsCriteria); $k++)
            array_push($arrTempStatus, $rsCriteria[$k]['name']);

        $categoryName = implode(", ", $arrTempStatus);
        array_push($arrFilterInformation, array("label" => $obj->lang['purchaseCategory'], 'filter' => $categoryName));

    }

    if (isset($_POST) && !empty($_POST['selServiceSelling']) && $_POST['selFilterBy'] == 2) {

        $key = implode(",", $class->oDbCon->paramString($_POST['selServiceSelling']));

        $arrFilterCriteriaSelling .= ' AND ' . $obj->tableNameDetailItem . '.servicekey in(' . $key . ')';

        $rsCriteria = $service->searchData('', '', true, ' and ' . $service->tableName . '.pkey in (' . $key . ')');

        $arrTempStatus = array();
        for ($k = 0; $k < count($rsCriteria); $k++)
            array_push($arrTempStatus, $rsCriteria[$k]['name']);

        $serviceName = implode(", ", $arrTempStatus);
        array_push($arrFilterInformation, array("label" => $obj->lang['salesService'], 'filter' => $serviceName));

    }

    if (isset($_POST) && !empty($_POST['selServiceBuying']) && $_POST['selFilterBy'] == 2) {

        $key = implode(",", $class->oDbCon->paramString($_POST['selServiceBuying']));

        $arrFilterCriteriaBuying .= ' AND ' . $emklPurchaseOrder->tableNameDetail . '.servicekey in(' . $key . ')';

        $rsCriteria = $service->searchData('', '', true, ' and ' . $service->tableName . '.pkey in (' . $key . ')');

        $arrTempStatus = array();
        for ($k = 0; $k < count($rsCriteria); $k++)
            array_push($arrTempStatus, $rsCriteria[$k]['name']);

        $serviceName = implode(", ", $arrTempStatus);
        array_push($arrFilterInformation, array("label" => $obj->lang['purchasingCost'], 'filter' => $serviceName));

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

    array_push($criteriaArr, array(
        'postVariable' => 'selStatus',
        'type' => 'status'
    ));

    $obj->createReportCriteria($criteria, $arrFilterInformation, $criteriaArr);

    $orderBy = (!empty($_POST['hidOrderBy'])) ? $obj->oDbCon->paramOrder($_POST['hidOrderBy']) : 'etdpol'; // order by harus dr kolom yg terdaftar saja
    $orderType = (isset($_POST['hidOrderType']) && !empty($_POST['hidOrderType']) && $_POST['hidOrderType'] == 1) ? 'desc' : 'asc';

    $order = 'order by ' . $orderBy . ' ' . $orderType;

    //$rs = $obj->searchData('', '', true, $criteria, $order);
    $rs = $obj->getDataForJobOrderSummaryReport($criteria, $order);

    if ($_POST['selFilterBy'] == 1) {
        $itemFilter = 'categoryname';
    } else {
        $itemFilter = 'servicename';
    }

    $arrPkey = array_column($rs, 'pkey');

    // selling
    // $rsJobOrderSelling = $obj->getTotalSelling($arrPkey, false);
    $rsJobOrderSelling = $obj->getItemDetail('', '', $arrPkey, $arrFilterCriteriaSelling);
    $rsJobOrderSellingCol = $obj->reindexDetailCollections($rsJobOrderSelling, 'refheaderkey');
    $rsJobOrderSelling = array_unique(array_column($rsJobOrderSelling, $itemFilter)); // utk dpt unique index aj

    // buying
    $rsCostByCategory = $emklPurchaseOrder->getPurchaseService($arrPkey, $arrFilterCriteriaBuying);
    $rsCostByCategoryCol = $obj->reindexDetailCollections($rsCostByCategory, 'jokey');
    $arrCostCategory = array_unique(array_column($rsCostByCategory, $itemFilter)); // utk dpt unique index aj


    $arrTempStructure = array();

    foreach ($rsJobOrderSelling as $row) {
        $arrStructureIndex = 'mnv-selling-' . $row;
        $arrTempStructure[$arrStructureIndex] = array('title' => $row, 'dbfield' => $arrStructureIndex, 'width' => "100px", 'format' => 'number', 'sortable' => false, 'calculateTotal' => true, 'textColor' => '568203');
    }
    $arrTempStructure['total'] = array('title' => ucwords($obj->lang['totalSales']), 'dbfield' => 'totalselling', 'align' => 'right', 'width' => "110px", 'sortable' => false, 'format' => 'number', 'calculateTotal' => true);

    foreach ($arrCostCategory as $row) {
        $arrStructureIndex = 'mnv-cost-' . $row;
        $arrTempStructure[$arrStructureIndex] = array('title' => $row, 'dbfield' => $arrStructureIndex, 'width' => "100px", 'format' => 'number', 'sortable' => false, 'calculateTotal' => true, 'textColor' => '0093AF');
    }

    $arrTempStructure['totalCost'] = array('title' => ucwords($obj->lang['totalCost']), 'dbfield' => 'totalbuying', 'align' => 'right', 'width' => "110px", 'sortable' => false, 'format' => 'number', 'calculateTotal' => true);




    $arrReturn = $obj->insertReportColumns(10, $arrDataStructure, $arrTempStructure, $twig, $arrTwigVar, $arrHeaderTemplate);
    $arrTemplate = $arrReturn['tableTemplate'];

    $arrKeys = array_column($rs, 'pkey');
    $rsContainerQty = $obj->getDetailVolume($arrKeys);
    $arrContainerCol = array();
    $totalContainerRows = count($rsContainerQty);
    for ($i = 0; $i < $totalContainerRows; $i++) {
        $sokey = $rsContainerQty[$i]['refkey'];
        $vol = $rsContainerQty[$i]['volume'];
        $qty = $rsContainerQty[$i]['qty'];
        if (!isset($arrContainerCol[$sokey]))
            $arrContainerCol[$sokey] = array();

        $arrContainerCol[$sokey][strval(intval($vol))] += $qty;
    }

    // utk LCL
    $containerLCLKey = array_unique(array_column($rs, 'itemkey'));
    $rsContainerCol = $container->searchDataRow(
        array($container->tableName . '.pkey', $container->tableName . '.volume'),
        ' and ' . $container->tableName . '.pkey in (' . $class->oDbCon->paramString($containerLCLKey, ',') . ')'
    );
    $rsContainerCol = array_column($rsContainerCol, null, 'pkey');



    $tempreport = '';
    if (empty($rs))
        $tempreport .= '<tr class="report-row rewrite-row"><td colspan="' . count($arrHeaderTemplate['dataStructure']) . '"></td></tr>';




    $totalRs = count($rs);
    for ($i = 0; $i < $totalRs; $i++) {
        $totalcost = 0;
        $totalselling = 0;

        $sokey = $rs[$i]['pkey'];

        // selling cost
        $rsSelling = isset($rsJobOrderSellingCol[$rs[$i]['pkey']]) ? $rsJobOrderSellingCol[$rs[$i]['pkey']] : array();
        foreach ($rsSelling as $sellingRow) {
            $arrStructureIndex = 'mnv-selling-' . $sellingRow[$itemFilter];
            if (!isset($rs[$i][$arrStructureIndex]))
                $rs[$i][$arrStructureIndex] = 0;
            $rs[$i][$arrStructureIndex] += $sellingRow['subtotalcurrency'];
            $totalselling += $sellingRow['subtotalcurrency'];
        }
        $rs[$i]['totalselling'] = $totalselling;


        // cost
        $rsCost = isset($rsCostByCategoryCol[$rs[$i]['pkey']]) ? $rsCostByCategoryCol[$rs[$i]['pkey']] : array();
        foreach ($rsCost as $costRow) {
            $arrStructureIndex = 'mnv-cost-' . $costRow[$itemFilter];
            if (!isset($rs[$i][$arrStructureIndex]))
                $rs[$i][$arrStructureIndex] = 0;
            $rs[$i][$arrStructureIndex] += $costRow['subtotalcurrency'];
            $totalcost += $costRow['subtotalcurrency'];
        }


        // kalo lcl dan bukan master
        if (in_array($rs[$i]['loadcontainertypekey'], array(EMKL['emklType']['lcl'], EMKL['emklType']['lclnc']))) {
            if ($rs[$i]['ismaster']) {
                $volLCL = strval(intval($rsContainerCol[$rs[$i]['itemkey']]['volume']));
                $rs[$i]['volume' . $volLCL] = 1;
            } else {
                $rs[$i]['volume20'] = 0;
                $rs[$i]['volume40'] = 0;
                $rs[$i]['volume45'] = 0;
            }
        } else {
            $rs[$i]['volume20'] = $arrContainerCol[$sokey]['20'];
            $rs[$i]['volume40'] = $arrContainerCol[$sokey]['40'];
            $rs[$i]['volume45'] = $arrContainerCol[$sokey]['45'];
        }

        $rs[$i]['totalbuying'] = $totalcost;

        $rs[$i]['totalprofit'] = $totalselling - $totalcost;

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


echo $twig->render('reportGrossPNLFF.html', $arrTwigVar);

?>
