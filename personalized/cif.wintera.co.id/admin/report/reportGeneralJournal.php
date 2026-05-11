<?php


includeClass('GeneralJournal.class.php');
$generalJournal = createObjAndAddToCol(new GeneralJournal());
$warehouse = createObjAndAddToCol(new Warehouse());
$cashAdvanceRealization = createObjAndAddToCol(new CashAdvanceRealization());
$emklPurchaseOrder = createObjAndAddToCol(new EMKLPurchaseOrder());


$obj = $generalJournal;
$securityObject = 'reportGeneralJournal'; // the value of security object is manually inserted to handle 
// some modules that have different security object from that of their class

if (!$security->isAdminLogin($securityObject, 10, true));

$useCurrencyRevaluation = $obj->loadSetting('currencyRevaluation');
$useCurrencyRevaluation = ($useCurrencyRevaluation == 1) ? true : false;

$_POST['selStatus[]'] = array(2, 3);
if (!isset($_POST['isGrouping']))
    $_POST['isGrouping'] = 1;

if (!isset($_POST['trStartDate']) || empty($_POST['trStartDate'])) {
    $_POST['trStartDate'] = date('d / m / Y');
    $_POST['trEndDate'] = date('d / m / Y');
}

$isGrouping = (isset($_POST['isGrouping']) && $_POST['isGrouping'] == 1) ? true : false;

$arrFilterInformation = array();

// ===== FOR EXPORT SECTION
$dataToExport = array();

/* data structure */
$arrTemplate = array();

$arrDataStructure = array();
$arrDataStructure['rowNumber'] = array('title' => '#', 'align' => 'right', 'width' => "40px", 'autoNumber' => true, "sortable" => false);
$arrDataStructure['code'] = array('title' => ucwords($obj->lang['code']), 'width' => "150px", 'dbfield' => 'code');
$arrDataStructure['date'] = array('title' => ucwords($obj->lang['date']), 'width' => "100px", 'dbfield' => 'trdate', 'format' => 'date');
$arrDataStructure['warehouse'] = array('title' => ucwords($obj->lang['warehouse']), 'dbfield' => 'warehousename', 'width' => "100px");
$arrDataStructure['refcode'] = array('title' => ucwords($obj->lang['reference']), 'width' => "150px", 'dbfield' => 'refcode');

if ($isGrouping) {
    $arrDataStructure['amount'] = array('title' => ucwords($obj->lang['amount']), 'width' => "150px", 'dbfield' => 'totaldebit', 'format' => 'decimal');
    $arrDataStructure['note'] = array('title' => ucwords($obj->lang['note']), 'width' => ($useCurrencyRevaluation) ? "500px" : "250px", 'dbfield' => 'trdesc');

    $arrDataStructure['JOCode'] = array('title' => ucwords($obj->lang['JOCode']), 'width' => "150px", 'dbfield' => 'jocodecache');
    $arrDataStructure['POCode'] = array('title' => ucwords($obj->lang['poCode']), 'width' => "150px", 'dbfield' => 'pocodecache');

    $arrDataStructure['status'] = array('title' => ucwords($obj->lang['status']), 'dbfield' => 'statusname', 'width' => "100px");
} else {
    $arrDataStructure['accountCode'] = array('title' => ucwords($obj->lang['accountCode']), 'dbfield' => 'coacode', 'width' => "120px");
    $arrDataStructure['ccountName'] = array('title' => ucwords($obj->lang['accountName']), 'dbfield' => 'coaname', 'width' => "200px");

    if ($useCurrencyRevaluation) {
        $arrDataStructure['debitSource'] = array('title' => ucwords($obj->lang['debitSource']), 'dbfield' => 'debitsource', 'width' => "100px", 'format' => 'decimal');
        $arrDataStructure['creditSource'] = array('title' => ucwords($obj->lang['creditSource']), 'dbfield' => 'creditsource', 'width' => "100px", 'format' => 'decimal');
        $arrDataStructure['currency'] = array('title' => ucwords($obj->lang['curr']), 'dbfield' => 'currencyname', 'width' => "60px", "align" => "center");
        $arrDataStructure['rate'] = array('title' => ucwords($obj->lang['rate']), 'dbfield' => 'rate', 'width' => "80px", 'format' => 'decimal');
    }

    $arrDataStructure['debit'] = array('title' => ucwords($obj->lang['debit']), 'dbfield' => 'debit', 'width' => "100px", 'format' => 'decimal', 'textColor' => '568203');
    $arrDataStructure['credit'] = array('title' => ucwords($obj->lang['credit']), 'dbfield' => 'credit', 'width' => "100px", 'format' => 'decimal', 'textColor' => 'C41E3A');
    $arrDataStructure['note'] = array('title' => ucwords($obj->lang['note']), 'width' => "500px", 'dbfield' => 'trdesc');

    $arrDataStructure['JOCode'] = array('title' => ucwords($obj->lang['JOCode']), 'width' => "150px", 'dbfield' => 'jocodecache');
    $arrDataStructure['POCode'] = array('title' => ucwords($obj->lang['poCode']), 'width' => "150px", 'dbfield' => 'pocodecache');

} 

$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $obj->lang['generalJournalReport'];
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();

array_push($arrTemplate, $arrHeaderTemplate);


if ($isGrouping) {
    // detail ...
    $arrDataDetailStructure = array();
    $arrDataDetailStructure['accountCode'] = array('title' => ucwords($obj->lang['accountCode']), 'dbfield' => 'coacode', 'width' => "120px");
    $arrDataDetailStructure['accountName'] = array('title' => ucwords($obj->lang['accountName']), 'dbfield' => 'coaname', 'width' => "200px");

    if ($useCurrencyRevaluation) {
        $arrDataDetailStructure['debitSource'] = array('title' => ucwords($obj->lang['debitSource']), 'dbfield' => 'debitsource', 'width' => "100px", 'format' => 'decimal');
        $arrDataDetailStructure['creditSource'] = array('title' => ucwords($obj->lang['creditSource']), 'dbfield' => 'creditsource', 'width' => "100px", 'format' => 'decimal');
        $arrDataDetailStructure['currency'] = array('title' => ucwords($obj->lang['curr']), 'dbfield' => 'currencyname', 'width' => "60px", "align" => "center");
        $arrDataDetailStructure['rate'] = array('title' => ucwords($obj->lang['rate']), 'dbfield' => 'rate', 'width' => "80px", 'format' => 'decimal');
    }

    $arrDataDetailStructure['debit'] = array('title' => ucwords($obj->lang['debit']), 'dbfield' => 'debit', 'width' => "100px", 'format' => 'decimal', 'textColor' => '568203', 'calculateTotal' => true);
    $arrDataDetailStructure['credit'] = array('title' => ucwords($obj->lang['credit']), 'dbfield' => 'credit', 'width' => "100px", 'format' => 'decimal', 'textColor' => 'C41E3A', 'calculateTotal' => true);
    $arrDataDetailStructure['reference'] = array('title' => ucwords($obj->lang['reference']), 'dbfield' => 'refcode', 'width' => "100px");
    $arrDataDetailStructure['note'] = array('title' => ucwords($obj->lang['note']), 'dbfield' => 'trdesc', 'width' => "150px");

    $arrDetailTemplate = array();
    $arrDetailTemplate['dataStructure'] = $arrDataDetailStructure;
    $arrDetailTemplate['total'] = array();

    array_push($arrTemplate, $arrDetailTemplate);
}
$arrWarehouse = $class->convertForCombobox($warehouse->searchData($warehouse->tableName . '.statuskey', 1, true, '', 'order by name asc'), 'pkey', 'name');
$arrStatus = $class->convertForCombobox($obj->getAllStatus(), 'pkey', 'status');


$arrTwigVar['inputStartDate'] = $class->inputDate('trStartDate', array('etc' => ' style="text-align:center"'));
$arrTwigVar['inputEndDate'] = $class->inputDate('trEndDate', array('etc' => ' style="text-align:center"'));
$arrTwigVar['inputSelWarehouse'] = $class->inputSelect('selWarehouse[]', $arrWarehouse, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputSelStatus'] = $class->inputSelect('selStatus[]', $arrStatus, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputGeneralJurnalCode'] = $class->inputText('generalJurnalCode');
$arrTwigVar['inputRefCode'] = $class->inputText('refCode');
$arrTwigVar['inputHidCOAKey'] = $class->inputHidden('hidCOAKey');
$arrTwigVar['inputCOAName'] = $class->inputText('coaName');
$arrTwigVar['inputIsGrouping'] = $class->inputCheckBox('isGrouping');
$arrTwigVar['arrTemplate'] = $arrHeaderTemplate;


if (isset($_POST) && !empty($_POST['hidAction'])) {

    $criteria = '';

    if (isset($_POST) && !empty($_POST['generalJurnalCode'])) {
        $criteria .= ' AND ' . $obj->tableName . '.code LIKE (' . $class->oDbCon->paramString('%' . $_POST['generalJurnalCode'] . '%') . ')';
        array_push($arrFilterInformation, array("label" => 'Kode', 'filter' => $_POST['generalJurnalCode']));
    }

    if (isset($_POST) && !empty($_POST['refCode'])) {
        $criteria .= ' AND ' . $obj->tableName . '.refcode LIKE (' . $class->oDbCon->paramString('%' . $_POST['refCode'] . '%') . ')';
        array_push($arrFilterInformation, array("label" => 'Kode Referensi', 'filter' => $_POST['refCode']));
    }

    if (isset($_POST) && !empty($_POST['trStartDate'])) {
        $criteria .= ' and trdate between ' . $class->oDbCon->paramDate($_POST['trStartDate'], ' / ') . ' AND ' . $class->oDbCon->paramDate($_POST['trEndDate'], ' / ');
        array_push($arrFilterInformation, array("label" => 'Tanggal', 'filter' => $_POST['trStartDate'] . ' - ' . $_POST['trEndDate']));
    }

    if (isset($_POST) && !empty($_POST['selWarehouse'])) {

        $key = implode(",", $class->oDbCon->paramString($_POST['selWarehouse']));

        $criteria .= ' AND ' . $obj->tableName . '.warehousekey in(' . $key . ')';

        $rsCriteria = $warehouse->searchData('', '', true, ' and ' . $warehouse->tableName . '.pkey in (' . $key . ')');

        $arrTempStatus = array();
        for ($k = 0; $k < count($rsCriteria); $k++)
            array_push($arrTempStatus, $rsCriteria[$k]['name']);

        $warehouseName = implode(", ", $arrTempStatus);
        array_push($arrFilterInformation, array("label" => 'Gudang', 'filter' => $warehouseName));

    }

    /*
    if(isset($_POST) && !empty($_POST['coaName'])) { 
        $criteria .= ' AND ( '.$obj->tableCOA.'.name LIKE ('.$class->oDbCon->paramString('%'.$_POST['coaName'].'%').' ) or '.$obj->tableCOA.'.code LIKE ('.$class->oDbCon->paramString('%'.$_POST['coaName'].'%').')  )';
        array_push($arrFilterInformation,array("label" => 'COA', 'filter' => $_POST['coaName']));
    }*/

    if (isset($_POST) && !empty($_POST['selStatus'])) {

        $key = implode(",", $class->oDbCon->paramString($_POST['selStatus']));

        $criteria .= ' AND ' . $obj->tableName . '.statuskey in(' . $key . ')';

        $rsCriteria = $obj->getStatusById($key);

        $arrTempStatus = array();
        for ($k = 0; $k < count($rsCriteria); $k++)
            array_push($arrTempStatus, $rsCriteria[$k]['status']);

        $statusName = implode(", ", $arrTempStatus);
        array_push($arrFilterInformation, array("label" => 'Status', 'filter' => $statusName));

    }

    $orderBy = (!empty($_POST['hidOrderBy'])) ? $obj->oDbCon->paramOrder($_POST['hidOrderBy']) : 'pkey'; // order by harus dr kolom yg terdaftar saja
    $orderType = (isset($_POST['hidOrderType']) && !empty($_POST['hidOrderType']) && $_POST['hidOrderType'] == 1) ? 'desc' : 'asc';


    $order = 'order by ' . $orderBy . ' ' . $orderType;

    $rs = ($isGrouping) ? $obj->searchData('', '', true, $criteria, $order) : $obj->generateGeneralJournalDetailReport($criteria, $order);

    $cashAdvanceRealizationTableKey = $cashAdvanceRealization->getTableKeyAndObj($cashAdvanceRealization->tableName,array('key'))['key'];
    $refCashAdvanceRealization = array_filter($rs, function ($row) use ($cashAdvanceRealizationTableKey) {
        return $row['reftabletype'] == $cashAdvanceRealizationTableKey;
    });
    
    $reindexedCashAdvance = array();
    if (!empty($refCashAdvanceRealization)) {
        $cashAdvanceKeys = array_column($refCashAdvanceRealization, 'refkey');
        $rsCashAdvanceRealizationDetail = $cashAdvanceRealization->getDetailWithRelatedInformation($cashAdvanceKeys);
        $arrJobOrderKey = array_column($rsCashAdvanceRealizationDetail, 'joborderkey');
        $rsCashAdvanceRealizationDetailCols = $obj->reindexDetailCollections($rsCashAdvanceRealizationDetail, 'refkey');

        $rsEMKLPurchaseOrder = $emklPurchaseOrder->searchDataRow(array(
                                                        $emklPurchaseOrder->tableName.'.pkey',
                                                        $emklPurchaseOrder->tableName.'.code',
                                                        $emklPurchaseOrder->tableName.'.refkey',
                                                        $emklPurchaseOrder->tableName.'.statuskey'
                                                ), ' and ' . $emklPurchaseOrder->tableName.'.refkey in ('.$obj->oDbCon->paramString($arrJobOrderKey,',').') and '.$emklPurchaseOrder->tableName.'.statuskey in (2,3) ');
        $rsEMKLPurchaseOrderCols = $obj->reindexDetailCollections($rsEMKLPurchaseOrder, 'refkey');
                                            

        foreach ($rsCashAdvanceRealizationDetailCols as $refkey => $rows) {

            $jobOrderKeys = array_column($rows, 'joborderkey');
            $poCodes = [];
            foreach ($jobOrderKeys as $joKey) {
                if (isset($rsEMKLPurchaseOrderCols[$joKey])) {
                    $poCodes = array_merge($poCodes, array_column($rsEMKLPurchaseOrderCols[$joKey], 'code'));
                }
            }

            $reindexedCashAdvance[$cashAdvanceRealizationTableKey][$refkey] = [
                'jocodecache' => implode(', ', array_unique(array_column($rows, 'jobordercode'))),
                'pocodecache' => implode(', ', array_unique($poCodes))
            ];
        }

    }

    // $obj->setLog($reindexedCashAdvance, true);

    $tableReport = '';
    $tempreport = '';

    if (empty($rs))
        $tempreport .= '<tr class="report-row rewrite-row"><td colspan="' . count($arrHeaderTemplate['dataStructure']) . '"></td></tr>';

    if ($isGrouping)
        $rsDetailCol = $obj->getDetailCollections($rs, 'refkey');

    $arrShowKeys = [];
    foreach ($rs as $headerRow) {

        $indexKey = $headerRow['reftabletype'] . '_' . $headerRow['refkey'];
        if(isset($reindexedCashAdvance[$headerRow['reftabletype']][$headerRow['refkey']])){
            $reindexCode = $reindexedCashAdvance[$headerRow['reftabletype']][$headerRow['refkey']];

            $JOCode = $reindexCode['jocodecache'];
            $POCode = $reindexCode['pocodecache'];

            if (!$isGrouping) {
                if (!in_array($indexKey, $arrShowKeys)) {
                    $headerRow['jocodecache'] = $JOCode;
                    $headerRow['pocodecache'] = $POCode;
                    $arrShowKeys[] = $indexKey;
                } else {
                    $headerRow['jocodecache'] = '';
                    $headerRow['pocodecache'] = '';
                }
            } else {
                $headerRow['jocodecache'] = $JOCode;
                $headerRow['pocodecache'] = $POCode;
            }



        }

        if ($isGrouping) {
            $rsDetail = $rsDetailCol[$headerRow['pkey']];
            $headerRow['_detail_'] = array('arrTemplate' => $arrDetailTemplate, 'data' => $rsDetail);
        }
        

        $return = $obj->formatReportRows(array('data' => $headerRow), $arrTemplate);

        // ===== FOR EXPORT SECTION 
        array_push($dataToExport, $return['data']);
        // ===== END FOR EXPORT SECTION

        $tempreport .= $return['html'];

        $arrTemplate[0]['total'] = $obj->arraySum($arrTemplate[0]['total'], $return['subtotal'][0]);
    }

    $tableHeader = $twig->render('template-header.html', $arrTwigVar);
    $obj->generateReport($_POST, $tempreport, $arrTemplate, $dataToExport, $arrFilterInformation, $tableHeader);

}

echo $twig->render('reportGeneralJournal.html', $arrTwigVar);
?>