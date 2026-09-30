<?php

include '../../_config.php';
include '../../_include-v2.php';

includeClass(array(
    'GoodsOut.class.php',
    'Customer.class.php',
    'DocumentType.class.php'
));

$goodsOut = new GoodsOut();
$customer = new Customer();
$documentType = new DocumentType();
$obj = $goodsOut;

include '_global.php';

$securityObject = 'reportGoodsOut';

if (!$security->isAdminLogin($securityObject, 10, true));

$arrFilterInformation = array();
$detailCriteria = '';
$dataToExport = array();
$arrTemplate = array();

$arrDocumentType = $class->convertForCombobox($documentType->searchData('', '', true, '', 'order by name asc'), 'pkey', 'name');
$arrCustomer = $class->convertForCombobox($customer->searchData($customer->tableName . '.statuskey', 2, true, '', 'order by name asc'), 'pkey', 'name');

$rsGoodsOutOption = $obj->searchData('', '', true, ' and ' . $obj->tableName . '.statuskey in (1,2,3)', 'order by ' . $obj->tableName . '.recipient asc');
$arrRecipient = array();
$arrCar = array();
$arrDriver = array();

for ($i = 0; $i < count($rsGoodsOutOption); $i++) {
    $recipient = trim($rsGoodsOutOption[$i]['recipient']);
    if (!empty($recipient) && !isset($arrRecipient[$recipient])) {
        $arrRecipient[$recipient] = array('pkey' => $recipient, 'name' => $recipient);
    }

    $car = trim($rsGoodsOutOption[$i]['car']);
    if (!empty($car) && !isset($arrCar[$car])) {
        $arrCar[$car] = array('pkey' => $car, 'name' => $car);
    }

    $driver = trim($rsGoodsOutOption[$i]['driver']);
    if (!empty($driver) && !isset($arrDriver[$driver])) {
        $arrDriver[$driver] = array('pkey' => $driver, 'name' => $driver);
    }
}



if (!isset($_POST['isGrouping'])) {
    $_POST['isGrouping'] = 0;
}

$isGrouping = (isset($_POST['isGrouping']) && $_POST['isGrouping'] == 1) ? true : false;

if (!isset($_POST['trStartDate']) || empty($_POST['trStartDate'])) {
    $_POST['trStartDate'] = date('d / m / Y');
    $_POST['trEndDate'] = date('d / m / Y');
}

$orderCriteria = array();
$orderCriteria['orderBy'] = (isset($_POST) && !empty($_POST['hidOrderBy'])) ? $obj->oDbCon->paramOrder($_POST['hidOrderBy']) : 'trdate';
$orderCriteria['orderType'] = (isset($_POST) && !empty($_POST['hidOrderType'])) ? $_POST['hidOrderType'] : -1;

switch ($EXPORT_TYPE) {
    case 2:
        $arrDataStructure = array();
        $arrDataStructure['code'] = array('title' => ucwords($obj->lang['code']), 'dbfield' => 'code', 'width' => '110px');
        $arrDataStructure['trdate'] = array('title' => ucwords($obj->lang['date']), 'dbfield' => 'date', 'width' => '120px', 'format' => 'date', 'align' => 'center');
        if(!$isGrouping) {
            $arrDataStructure['itemcode'] = array('title' => ucwords($obj->lang['itemCode']), 'dbfield' => 'itemcode', 'width' => '180px');
            $arrDataStructure['itemname'] = array('title' => ucwords($obj->lang['itemName']), 'dbfield' => 'itemlabel', 'width' => '180px');
            $arrDataStructure['hsnumber'] = array('title' => ucwords($obj->lang['hs']), 'dbfield' => 'hs', 'width' => '150px');
            $arrDataStructure['containernumber'] = array('title' => ucwords($obj->lang['containerNumber']), 'dbfield' => 'detailcontainernumber', 'width' => '150px');
            $arrDataStructure['amount'] = array('title' => ucwords($obj->lang['amount']), 'dbfield' => 'qty', 'width' => '150px');
            $arrDataStructure['unit'] = array('title' => ucwords($obj->lang['unit']), 'dbfield' => 'unitname', 'width' => '180px');
            $arrDataStructure['currency'] = array('title' => ucwords($obj->lang['currency']), 'dbfield' => 'currencyname', 'width' => '180px');
            $arrDataStructure['value'] = array('title' => ucwords($obj->lang['value']), 'dbfield' => 'amount', 'width' => '150px');

        }
        $arrDataStructure['documenttype'] = array('title' => ucwords($obj->lang['documentType']), 'dbfield' => 'documenttype', 'width' => '140px');
        $arrDataStructure['submissionnumber'] = array('title' => ucwords($obj->lang['submissionNumber']), 'dbfield' => 'submissionnumber', 'width' => '140px');
        $arrDataStructure['submissiondate'] = array('title' => ucwords($obj->lang['submissionDate']), 'dbfield' => 'submissiondate', 'width' => '120px', 'format' => 'date', 'align' => 'center');
        $arrDataStructure['registrationnumber'] = array('title' => ucwords($obj->lang['registerNumber']), 'dbfield' => 'registrationnumber', 'width' => '150px');
        $arrDataStructure['registrationdate'] = array('title' => ucwords($obj->lang['registerDate']), 'dbfield' => 'registrationdate', 'width' => '120px', 'format' => 'date', 'align' => 'center');
        $arrDataStructure['invoicenumber'] = array('title' => ucwords($obj->lang['invoiceNumber']), 'dbfield' => 'invoicenumber', 'width' => '150px');
        $arrDataStructure['blnumber'] = array('title' => ucwords($obj->lang['blNumber']), 'dbfield' => 'blnumber', 'width' => '150px');
        $arrDataStructure['customer'] = array('title' => ucwords($obj->lang['customer']), 'dbfield' => 'customername', 'width' => '180px');
        $arrDataStructure['recipient'] = array('title' => ucwords($obj->lang['recipient']), 'dbfield' => 'recipient', 'width' => '180px');
        $arrDataStructure['recipientaddress'] = array('title' => ucwords($obj->lang['recipientAddress']), 'dbfield' => 'recipientaddress', 'width' => '220px');
        $arrDataStructure['car'] = array('title' => ucwords($obj->lang['car']), 'dbfield' => 'car', 'width' => '140px');
        $arrDataStructure['driver'] = array('title' => ucwords($obj->lang['driver']), 'dbfield' => 'driver', 'width' => '140px');
        $arrDataStructure['status'] = array('title' => ucwords($obj->lang['status']), 'dbfield' => 'statusname', 'width' => '80px');
        break;

    default:
        $arrDataStructure = array();
        $arrDataStructure['rowNumber'] = array('title' => '#', 'align' => 'right', 'width' => '40px', 'autoNumber' => true, 'sortable' => false);
        $arrDataStructure['code'] = array('title' => ucwords($obj->lang['code']), 'dbfield' => 'code', 'width' => '110px');
        $arrDataStructure['trdate'] = array('title' => ucwords($obj->lang['date']), 'dbfield' => 'date', 'width' => '120px', 'format' => 'date', 'align' => 'center');
        if(!$isGrouping) {
            $arrDataStructure['itemcode'] = array('title' => ucwords($obj->lang['itemCode']), 'dbfield' => 'itemcode', 'width' => '180px');
            $arrDataStructure['itemname'] = array('title' => ucwords($obj->lang['itemName']), 'dbfield' => 'itemlabel', 'width' => '180px');
            $arrDataStructure['hsnumber'] = array('title' => ucwords($obj->lang['hs']), 'dbfield' => 'hs', 'width' => '150px');
            $arrDataStructure['containernumber'] = array('title' => ucwords($obj->lang['containerNumber']), 'dbfield' => 'detailcontainernumber', 'width' => '150px');
            $arrDataStructure['amount'] = array('title' => ucwords($obj->lang['amount']), 'dbfield' => 'qty', 'width' => '150px');
            $arrDataStructure['unit'] = array('title' => ucwords($obj->lang['unit']), 'dbfield' => 'unitname', 'width' => '180px');
            $arrDataStructure['currency'] = array('title' => ucwords($obj->lang['currency']), 'dbfield' => 'currencyname', 'width' => '180px');
            $arrDataStructure['value'] = array('title' => ucwords($obj->lang['value']), 'dbfield' => 'amount', 'width' => '150px');

        }
        $arrDataStructure['documenttype'] = array('title' => ucwords($obj->lang['documentType']), 'dbfield' => 'documenttype', 'width' => '140px');
        $arrDataStructure['submissionnumber'] = array('title' => ucwords($obj->lang['submissionNumber']), 'dbfield' => 'submissionnumber', 'width' => '140px');
        $arrDataStructure['submissiondate'] = array('title' => ucwords($obj->lang['submissionDate']), 'dbfield' => 'submissiondate', 'width' => '120px', 'format' => 'date', 'align' => 'center');
        $arrDataStructure['registrationnumber'] = array('title' => ucwords($obj->lang['registerNumber']), 'dbfield' => 'registrationnumber', 'width' => '150px');
        $arrDataStructure['registrationdate'] = array('title' => ucwords($obj->lang['registerDate']), 'dbfield' => 'registrationdate', 'width' => '120px', 'format' => 'date', 'align' => 'center');
        $arrDataStructure['invoicenumber'] = array('title' => ucwords($obj->lang['invoiceNumber']), 'dbfield' => 'invoicenumber', 'width' => '150px');
        $arrDataStructure['blnumber'] = array('title' => ucwords($obj->lang['blNumber']), 'dbfield' => 'blnumber', 'width' => '150px');
        $arrDataStructure['customer'] = array('title' => ucwords($obj->lang['customer']), 'dbfield' => 'customername', 'width' => '180px');
        $arrDataStructure['recipient'] = array('title' => ucwords($obj->lang['recipient']), 'dbfield' => 'recipient', 'width' => '180px');
        $arrDataStructure['recipientaddress'] = array('title' => ucwords($obj->lang['recipientAddress']), 'dbfield' => 'recipientaddress', 'width' => '220px');
        $arrDataStructure['car'] = array('title' => ucwords($obj->lang['car']), 'dbfield' => 'car', 'width' => '140px');
        $arrDataStructure['driver'] = array('title' => ucwords($obj->lang['driver']), 'dbfield' => 'driver', 'width' => '140px');
        $arrDataStructure['status'] = array('title' => ucwords($obj->lang['status']), 'dbfield' => 'statusname', 'width' => '80px');
        break;
}

$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = ucwords($obj->lang['itemOutReport']);
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();
array_push($arrTemplate, $arrHeaderTemplate);

if ($isGrouping) {
    $arrDataDetailStructure = array();

    $arrDataDetailStructure['itemcode'] = array('title' => ucwords($obj->lang['itemCode']), 'dbfield' => 'itemcode', 'width' => '180px');
    $arrDataDetailStructure['itemname'] = array('title' => ucwords($obj->lang['itemName']), 'dbfield' => 'itemlabel', 'width' => '180px');
    $arrDataDetailStructure['hsnumber'] = array('title' => ucwords($obj->lang['hs']), 'dbfield' => 'hs', 'width' => '150px');
    $arrDataDetailStructure['containernumber'] = array('title' => ucwords($obj->lang['containerNumber']), 'dbfield' => 'detailcontainernumber', 'width' => '150px');
    $arrDataDetailStructure['amount'] = array('title' => ucwords($obj->lang['amount']), 'dbfield' => 'qty', 'width' => '150px');
    $arrDataDetailStructure['unit'] = array('title' => ucwords($obj->lang['unit']), 'dbfield' => 'unitname', 'width' => '180px');
    $arrDataDetailStructure['currency'] = array('title' => ucwords($obj->lang['currency']), 'dbfield' => 'currencyname', 'width' => '180px');
    $arrDataDetailStructure['value'] = array('title' => ucwords($obj->lang['value']), 'dbfield' => 'amount', 'width' => '150px');


    $arrDetailTemplate = array();
    $arrDetailTemplate['reportWidth'] = '1100px';
    $arrDetailTemplate['dataStructure'] = $arrDataDetailStructure;
    $arrDetailTemplate['total'] = array();

    array_push($arrTemplate, $arrDetailTemplate);
}

$arrTwigVar['inputCode'] = $class->inputText('code');
$arrTwigVar['inputStartDate'] = $class->inputDate('trStartDate', array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputEndDate'] = $class->inputDate('trEndDate', array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputSelCustomer'] = $class->inputSelect('selCustomer[]', $arrCustomer, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputSelDocumentType'] = $class->inputSelect('selDocumentType[]', $arrDocumentType, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));

$arrTwigVar['inputIsGrouping'] = $class->inputCheckBox('isGrouping');
$arrTwigVar['arrTemplate'] = $arrHeaderTemplate;

$arrTwigVar['order'] = $orderCriteria;

if (isset($_POST) && !empty($_POST['hidAction'])) {
    $criteria = '';

    if (isset($_POST['code']) && !empty($_POST['code'])) {
        $criteria .= ' AND ' . $obj->tableName . '.code LIKE (' . $class->oDbCon->paramString('%' . $_POST['code'] . '%') . ')';
        array_push($arrFilterInformation, array('label' => $obj->lang['code'], 'filter' => $_POST['code']));
    }

    if (isset($_POST['trStartDate']) && !empty($_POST['trStartDate'])) {
        $criteria .= ' AND ' . $obj->tableName . '.trdate between ' . $class->oDbCon->paramDate($_POST['trStartDate'], ' / ') . ' AND ' . $class->oDbCon->paramDate($_POST['trEndDate'], ' / ', 'Y-m-d 23:59:59');
        array_push($arrFilterInformation, array('label' => $obj->lang['date'], 'filter' => $_POST['trStartDate'] . ' - ' . $_POST['trEndDate']));
    }

    if (isset($_POST['selCustomer']) && !empty($_POST['selCustomer'])) {
        $key = implode(',', $class->oDbCon->paramString($_POST['selCustomer']));
        $criteria .= ' AND ' . $obj->tableName . '.customerkey in(' . $key . ')';

        $rsCriteria = $customer->searchData('', '', true, ' and ' . $customer->tableName . '.pkey in (' . $key . ')');
        $arrTemp = array();
        for ($k = 0; $k < count($rsCriteria); $k++) {
            array_push($arrTemp, $rsCriteria[$k]['name']);
        }
        array_push($arrFilterInformation, array('label' => $obj->lang['customer'], 'filter' => implode(', ', $arrTemp)));
    }

    if (isset($_POST['selDocumentType']) && !empty($_POST['selDocumentType'])) {
        $key = implode(',', $class->oDbCon->paramString($_POST['selDocumentType']));
        $criteria .= ' AND ' . $obj->tableName . '.documenttypekey in(' . $key . ')';

        $rsCriteria = $documentType->searchData('', '', true, ' and ' . $documentType->tableName . '.pkey in (' . $key . ')');
        $arrTemp = array();
        for ($k = 0; $k < count($rsCriteria); $k++) {
            array_push($arrTemp, $rsCriteria[$k]['name']);
        }
        array_push($arrFilterInformation, array('label' => $obj->lang['documentType'], 'filter' => implode(', ', $arrTemp)));
    }

    // if (isset($_POST['selRecipient']) && !empty($_POST['selRecipient'])) {
    //     $key = implode(',', $class->oDbCon->paramString($_POST['selRecipient']));
    //     $criteria .= ' AND ' . $obj->tableName . '.recipient in(' . $key . ')';
    //     array_push($arrFilterInformation, array('label' => $obj->lang['recipient'], 'filter' => implode(', ', $_POST['selRecipient'])));
    // }

    // if (isset($_POST['selCar']) && !empty($_POST['selCar'])) {
    //     $key = implode(',', $class->oDbCon->paramString($_POST['selCar']));
    //     $criteria .= ' AND ' . $obj->tableName . '.car in(' . $key . ')';
    //     array_push($arrFilterInformation, array('label' => $obj->lang['car'], 'filter' => implode(', ', $_POST['selCar'])));
    // }

    // if (isset($_POST['selDriver']) && !empty($_POST['selDriver'])) {
    //     $key = implode(',', $class->oDbCon->paramString($_POST['selDriver']));
    //     $criteria .= ' AND ' . $obj->tableName . '.driver in(' . $key . ')';
    //     array_push($arrFilterInformation, array('label' => $obj->lang['driver'], 'filter' => implode(', ', $_POST['selDriver'])));
    // }

    if (isset($_POST['selStatus']) && !empty($_POST['selStatus'])) {
        $key = implode(',', $class->oDbCon->paramString($_POST['selStatus']));
        $criteria .= ' AND ' . $obj->tableName . '.statuskey in(' . $key . ')';

        $rsCriteria = $obj->getStatusById($key);
        $arrTemp = array();
        for ($k = 0; $k < count($rsCriteria); $k++) {
            array_push($arrTemp, $rsCriteria[$k]['status']);
        }
        array_push($arrFilterInformation, array('label' => $obj->lang['status'], 'filter' => implode(', ', $arrTemp)));
    } else {
        $criteria .= ' AND ' . $obj->tableName . '.statuskey in (1,2,3)';
    }

    $orderByMap = array(
        'code' => 'code',
        'date' => $obj->tableName . '.trdate',
        'trdate' => $obj->tableName . '.trdate',
        'documenttype' => $obj->tableName . '.documenttypekey',
        'customer' => 'customername',
        'customername' => 'customername',
        'recipient' => 'recipient',
        'car' => 'car',
        'driver' => 'driver',
        'status' => 'statusname',
        'statusname' => 'statusname'
    );

    $orderByKey = (!empty($_POST['hidOrderBy'])) ? $obj->oDbCon->paramOrder($_POST['hidOrderBy']) : 'date';
    $orderBy = isset($orderByMap[$orderByKey]) ? $orderByMap[$orderByKey] : $obj->tableName . '.trdate';
    $orderType = (isset($_POST['hidOrderType']) && !empty($_POST['hidOrderType']) && $_POST['hidOrderType'] == 1) ? 'desc' : 'asc';
    $order = 'order by ' . $orderBy . ' ' . $orderType;

    $rs = $obj->searchData('', '', true, $criteria, $order);
    
    $tempreport = '';

    if (!$isGrouping) {
        $arrFlatData = array();

        for ($i = 0; $i < count($rs); $i++) {
            $documentTypeId = (int)$rs[$i]['documenttypekey'];
            $rs[$i]['documenttype'] = isset($arrDocumentType[$documentTypeId]) ? $arrDocumentType[$documentTypeId]['label'] : $rs[$i]['documenttypekey'];
            $rsDetail = $obj->getDetailWithRelatedInformation($rs[$i]['pkey'], $detailCriteria);

            if (empty($rsDetail)) {
                continue;
            }

            for ($j = 0; $j < count($rsDetail); $j++) {
                $flatRow = array();
                $flatRow['code'] = $rs[$i]['code'];
                $flatRow['trdate'] = $rs[$i]['trdate'];
                $flatRow['date'] = $rs[$i]['trdate'];
                $flatRow['documenttype'] = $rs[$i]['documenttype'];
                $flatRow['submissionnumber'] = $rs[$i]['submissionnumber'];
                $flatRow['submissiondate'] = $rs[$i]['submissiondate'];
                $flatRow['registrationnumber'] = $rs[$i]['registrationnumber'];
                $flatRow['registrationdate'] = $rs[$i]['registrationdate'];
                $flatRow['invoicenumber'] = $rsDetail[$j]['invoicenumber'];
                $flatRow['blnumber'] = $rsDetail[$j]['blnumber'];
                $flatRow['customername'] = $rs[$i]['customername'];
                $flatRow['recipient'] = $rs[$i]['recipient'];
                $flatRow['recipientaddress'] = $rs[$i]['recipientaddress'];
                $flatRow['car'] = $rs[$i]['car'];
                $flatRow['driver'] = $rs[$i]['driver'];
                $flatRow['statusname'] = $rs[$i]['statusname'];
                $flatRow['receivingcode'] = $rsDetail[$j]['receivingcode'];
                $flatRow['warehouselayoutname'] = $rsDetail[$j]['warehouselayoutname'];
                $flatRow['itemcode'] = $rsDetail[$j]['itemcode'];
                $flatRow['itemname'] = $rsDetail[$j]['itemname'];
                $flatRow['itemlabel'] = $rsDetail[$j]['itemlabel'];
                $flatRow['hs'] = $rsDetail[$j]['hs'];
                $flatRow['detailcontainernumber'] = $rsDetail[$j]['detailcontainernumber'];
                $flatRow['unitname'] = $rsDetail[$j]['unitname'];
                $flatRow['currencyname'] = $rsDetail[$j]['currencyname'];
                $flatRow['qty'] = $rsDetail[$j]['qty'];
                $flatRow['issuedqty'] = $rsDetail[$j]['issuedqty'];
                $flatRow['amount'] = $rsDetail[$j]['amount'];
                array_push($arrFlatData, $flatRow);
            }
        }

        $rs = $arrFlatData;
    }

    for ($i = 0; $i < count($rs); $i++) {
        $rs[$i]['date'] = $rs[$i]['trdate'];
        if (isset($rs[$i]['documenttypekey'])) {
            $documentTypeId = (int)$rs[$i]['documenttypekey'];
            $rs[$i]['documenttype'] = isset($arrDocumentType[$documentTypeId]) ? $arrDocumentType[$documentTypeId]['label'] : $rs[$i]['documenttypekey'];
        }

        if ($isGrouping) {
            $rsDetail = $obj->getDetailWithRelatedInformation($rs[$i]['pkey'], $detailCriteria);
            if (!empty($rsDetail)) {
                $rs[$i]['invoicenumber'] = $rsDetail[0]['invoicenumber'];
                $rs[$i]['blnumber'] = $rsDetail[0]['blnumber'];
                $rs[$i]['_detail_'] = array('arrTemplate' => $arrDetailTemplate, 'data' => $rsDetail);
            }
        }

        $return = $obj->formatReportRows(array('data' => $rs[$i]), $arrTemplate);

        array_push($dataToExport, $return['data']);
        $tempreport .= $return['html'];
        $arrTemplate[0]['total'] = $obj->arraySum($arrTemplate[0]['total'], $return['subtotal'][0]);
    }

    $tableHeader = $twig->render('template-header.html', $arrTwigVar);
    $obj->generateReport($_POST, $tempreport, $arrTemplate, $dataToExport, $arrFilterInformation, $tableHeader);
}
$arrRecipient = $class->convertForCombobox(array_values($arrRecipient), 'pkey', 'name');
$arrCar = $class->convertForCombobox(array_values($arrCar), 'pkey', 'name');
$arrDriver = $class->convertForCombobox(array_values($arrDriver), 'pkey', 'name');

$arrStatus = $obj->getAllStatus();
$arrStatus = $class->convertForCombobox($arrStatus, 'pkey', 'status');
// $arrTwigVar['inputRecipient'] = $class->inputSelect('selRecipient[]', $arrRecipient, array('value' => array(),'etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
// $arrTwigVar['inputCar'] = $class->inputSelect('selCar[]', $arrCar, array('value' => array(),'etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
// $arrTwigVar['inputDriver'] = $class->inputSelect('selDriver[]', $arrDriver, array('value' => null,'etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));

$arrTwigVar['inputSelStatus'] = $class->inputSelect('selStatus[]', $arrStatus, array('value' => array(), 'etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['arrTemplate'] = $arrHeaderTemplate;

echo $twig->render('reportGoodsOut.html', $arrTwigVar);

?>