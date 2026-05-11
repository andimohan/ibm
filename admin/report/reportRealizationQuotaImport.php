<?php
include '../../_config.php';
include '../../_include-v2.php';

includeClass('EMKLQuotationOrder.class.php');
$emklQuotationOrderImport = createObjAndAddToCol(new EMKLQuotationOrder(EMKL['jobType']['import']));
$customer = createObjAndAddToCol(new Customer());
$employee = createObjAndAddToCol(new Employee());

include '_global.php';

$obj = $emklQuotationOrderImport;

$securityObject = 'ReportImportQuotaRealization';// the value of security object is manually inserted to handle 
// some modules that have different security object from that of their class

if (!$security->isAdminLogin($securityObject, 10, true));

$arrFilterInformation = array();
$detailCriteria = '';
$dataToExport = array();
$_POST['selStatus[]'] = array(2, 3);

/* data structure */
$arrTemplate = array();
$arrDataStructure = array();
$arrDataStructure['rowNumber'] = array('title' => '#', 'align' => 'right', 'width' => "40px", 'autoNumber' => true, "sortable" => false);
$arrDataStructure['code'] = array('title' => ucwords($obj->lang['code']), 'width' => "130px", 'dbfield' => 'code');
$arrDataStructure['customer'] = array('title' => ucwords($obj->lang['customer']), 'width' => "200px", 'dbfield' => 'customername');
$arrDataStructure['location'] = array('title' => ucwords($obj->lang['location']), 'width' => "300px", 'dbfield' => 'locationcache');
$arrDataStructure['salesman'] = array('title' => ucwords($obj->lang['salesman']), 'width' => "150px", 'dbfield' => 'salesname');
$arrDataStructure['quotaOrTarget'] = array('title' => ucwords($obj->lang['quotaOrTarget']), 'width' => "150px", 'dbfield' => 'quota', 'align' => 'right', 'format' => 'number');
$arrDataStructure['unit'] = array('title' => ucwords($obj->lang['unit']), 'width' => "100px", 'dbfield' => 'unitname');
$arrDataStructure['status'] = array('title' => ucwords($obj->lang['status']), 'width' => "150px", 'dbfield' => 'statusname');

$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $obj->lang['exportQuotaRealizationReport'];
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();

array_push($arrTemplate, $arrHeaderTemplate);

$arrDataDetailStructure = array();

$arrDataDetailStructure['jobOrderCode'] = array('title' => ucwords($obj->lang['jobOrderCode']), 'dbfield' => 'jobordercode', 'width' => "120px");
$arrDataDetailStructure['realization'] = array('title' => ucwords($obj->lang['realization']), 'dbfield' => 'realization', 'width' => "150px", 'align' => 'right', 'format' => 'number', 'calculateTotal' => true);
$arrDataDetailStructure['realizationUnit'] = array('title' => ucwords($obj->lang['unit']), 'dbfield' => 'realizationunit', 'width' => "100px");

$arrDetailTemplate = array();
$arrDetailTemplate['reportWidth'] = "960px";
$arrDetailTemplate['dataStructure'] = $arrDataDetailStructure;
$arrDetailTemplate['total'] = array();

array_push($arrTemplate, $arrDetailTemplate);

if (isset($_POST) && !empty($_POST['hidAction'])) {

    $criteria = '';
    if (isset($_POST) && !empty($_POST['quotationCode'])) {
        $criteria .= ' AND ' . $obj->tableName . '.code LIKE (' . $class->oDbCon->paramString('%' . $_POST['quotationCode'] . '%') . ')';
        array_push($arrFilterInformation, array("label" => 'Kode', 'filter' => $_POST['quotationCode']));
    }
    if (isset($_POST) && !empty($_POST['trStartDate'])) {
        $criteria .= ' and ' . $obj->tableName . '.trdate between ' . $class->oDbCon->paramDate($_POST['trStartDate'], ' / ') . ' AND ' . $class->oDbCon->paramDate($_POST['trEndDate'], ' / ');
        array_push($arrFilterInformation, array("label" => 'Tanggal', 'filter' => $_POST['trStartDate'] . ' - ' . $_POST['trEndDate']));
    }

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

    if (isset($_POST) && !empty($_POST['selCustomer'])) {

        $key = implode(",", $class->oDbCon->paramString($_POST['selCustomer']));

        $criteria .= ' AND ' . $obj->tableName . '.customerkey in(' . $key . ')';

        $rsCriteria = $customer->searchData('', '', true, ' and ' . $customer->tableName . '.pkey in (' . $key . ')');

        $arrTempStatus = array();
        for ($k = 0; $k < count($rsCriteria); $k++)
            array_push($arrTempStatus, $rsCriteria[$k]['name']);

        $customerName = implode(", ", $arrTempStatus);
        array_push($arrFilterInformation, array("label" => $obj->lang['customer'], 'filter' => $customerName));

    }

    if (isset($_POST) && !empty($_POST['selSalesman'])) {

        $key = implode(",", $class->oDbCon->paramString($_POST['selSalesman']));

        $criteria .= ' AND ' . $obj->tableName . '.saleskey in(' . $key . ')';

        $rsCriteria = $employee->searchData('', '', true, ' and ' . $employee->tableName . '.pkey in (' . $key . ')');

        $arrTempStatus = array();
        for ($k = 0; $k < count($rsCriteria); $k++)
            array_push($arrTempStatus, $rsCriteria[$k]['name']);

        $employeeName = implode(", ", $arrTempStatus);
        array_push($arrFilterInformation, array("label" => $obj->lang['salesman'], 'filter' => $employeeName));

    }

    $orderBy = (!empty($_POST['hidOrderBy'])) ? $obj->oDbCon->paramOrder($_POST['hidOrderBy']) : 'pkey'; // order by harus dr kolom yg terdaftar saja
    $orderType = (isset($_POST['hidOrderType']) && !empty($_POST['hidOrderType']) && $_POST['hidOrderType'] == 1) ? 'desc' : 'asc';

    $order = 'order by ' . $orderBy . ' ' . $orderType;


    $tempreport = '';

    $rs = $obj->generateDataForRealizationQuotaReport($criteria, $order);

    if (empty($rs))
        $tempreport .= '<tr class="report-row rewrite-row"><td colspan="' . count($arrHeaderTemplate['dataStructure']) . '"></td></tr>';

    $arrPkey = array_column($rs, 'pkey');
    $rsDetail = $obj->getRealizationQuotationJobOrderReport($arrPkey);
    $rsDetail = $obj->reindexDetailCollections($rsDetail, 'indexkey');

    $totalRs = count($rs);
    for ($i = 0; $i < $totalRs; $i++) {

        $indexkey = $rs[$i]['pkey'] . '-' . (empty($rs[$i]['itemkey']) ? 0 : $rs[$i]['itemkey']);

        if ($rs[$i]['quotacbm'] > 0) {
            // $rs[$i]['quota'] = $obj->formatNumber($rs[$i]['quotacbm'], 2) . ' CBM';
            $rs[$i]['quota'] = $rs[$i]['quotacbm'];
            $rs[$i]['unitname'] = 'CBM';
        } else {
            // $rs[$i]['quota'] = $obj->formatNumber($rs[$i]['quotavolume'], 2) . ' ' . $rs[$i]['containername'];
            $rs[$i]['quota'] = $rs[$i]['quotavolume'];
            $rs[$i]['unitname'] = $rs[$i]['containername'];
        }

        $rsDetailCol = $rsDetail[$indexkey];

        for ($j = 0; $j < count($rsDetailCol); $j++) {
            $rsDetailCol[$j]['jobordercode'] = $rsDetailCol[$j]['code'];

            if ($rsDetailCol[$j]['totalrealizationcbm'] > 0) {
                // $rsDetailCol[$j]['realization'] = $obj->formatNumber($rsDetailCol[$j]['totalrealizationcbm'], 2) . ' CBM';
                $rsDetailCol[$j]['realization'] = $rsDetailCol[$j]['totalrealizationcbm'];
                $rsDetailCol[$j]['realizationunit'] = 'CBM';
            } else {
                // $rsDetailCol[$j]['realization'] = $obj->formatNumber($rsDetailCol[$j]['totalrealizationvolume'], 2) . ' ' . $rsDetailCol[$j]['containername'];
                $rsDetailCol[$j]['realization'] = $rsDetailCol[$j]['totalrealizationvolume'];
                $rsDetailCol[$j]['realizationunit'] = $rsDetailCol[$j]['containername'];
            }

        }

        $rs[$i]['_detail_'] = array('arrTemplate' => $arrDetailTemplate, 'data' => $rsDetailCol);

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

$arrCustomer = $class->convertForCombobox($customer->searchData($customer->tableName . '.statuskey', 2, true), 'pkey', 'name');
$arrEmployee = $class->convertForCombobox($employee->searchData($employee->tableName . '.statuskey', 2, true, ' and ' . $employee->tableName . '.issales = 1'), 'pkey', 'name');
$arrStatus = $class->convertForCombobox($obj->getAllStatus(), 'pkey', 'status');


$arrTwigVar['inputQuotationCode'] = $class->inputText('quotationCode');
$arrTwigVar['inputSelStatus'] = $class->inputSelect('selStatus[]', $arrStatus, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputSelCustomer'] = $class->inputSelect('selCustomer[]', $arrCustomer, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputSelSalesman'] = $class->inputSelect('selSalesman[]', $arrEmployee, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputStartDate'] = $class->inputDate('trStartDate', array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputEndDate'] = $class->inputDate('trEndDate', array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputSelStatus'] = $class->inputSelect('selStatus[]', $arrStatus, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));

$arrTwigVar['arrTemplate'] = $arrHeaderTemplate;
echo $twig->render('reportRealizationQuotaImport.html', $arrTwigVar);

?>