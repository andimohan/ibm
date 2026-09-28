<?php

include '../../_config.php';
include '../../_include-v2.php';

includeClass(array(
    'ItemReceivingPlan.class.php',
    'Customer.class.php',
    'Supplier.class.php',
    'Warehouse.class.php',
    'WarehouseLayout.class.php',
    'DocumentType.class.php'
));

$itemReceivingPlan = new ItemReceivingPlan();
$customer = new Customer();
$supplier = new Supplier();
$warehouse = new Warehouse();
$warehouseLayout = new WarehouseLayout();
$documentType = new DocumentType();
$obj = $itemReceivingPlan;

include '_global.php';

$securityObject = 'ReportItemReceivingPlan';

if (!$security->isAdminLogin($securityObject, 10, true));

$arrFilterInformation = array();
$detailCriteria = '';
$dataToExport = array();
$arrTemplate = array();

$arrStatus = $obj->getAllStatus();
$arrDocumentType = $obj->convertForCombobox($documentType->searchData('', '', true, '', 'order by name asc'), 'pkey', 'name');

if (!isset($_POST['isGrouping'])) {
    $_POST['isGrouping'] = 0;
}


if (!isset($_POST['trStartDate']) || empty($_POST['trStartDate'])) {
    $_POST['trStartDate'] = date('d / m / Y');
    $_POST['trEndDate'] = date('d / m / Y');
}

$orderCriteria = array();
$orderCriteria['orderBy'] = (isset($_POST) && !empty($_POST['hidOrderBy'])) ? $obj->oDbCon->paramOrder($_POST['hidOrderBy']) : 'trdate';
$orderCriteria['orderType'] = (isset($_POST) && !empty($_POST['hidOrderType'])) ? $_POST['hidOrderType'] : -1;

$isGrouping = (isset($_POST['isGrouping']) && $_POST['isGrouping'] == 1) ? true : false;
$arrDataStructure = array();
$_POST['itemReceivingPlan'] = IMPORT_TEMPLATE['itemReceivingPlan'];

switch ($EXPORT_TYPE) {
    case 2:
        $arrDataStructure['code'] = array('title' => ucwords($obj->lang['code']), 'dbfield' => 'code', 'width' => '110px');
        $arrDataStructure['trdate'] = array('title' => ucwords($obj->lang['date']), 'dbfield' => 'date', 'width' => '120px', 'format' => 'date', 'align' => 'center');
        if (!$isGrouping) {
            $arrDataStructure['itemcode'] = array('title' => ucwords($obj->lang['itemCode']), 'dbfield' => 'itemcode', 'width' => '150px', 'format' => 'string');
            $arrDataStructure['itemname'] = array('title' => ucwords($obj->lang['itemName']), 'dbfield' => 'label', 'width' => '600px');
            $arrDataStructure['containernumber'] = array('title' => ucwords($obj->lang['containerNumber']), 'dbfield' => 'containernumber', 'width' => '220px');
            $arrDataStructure['qtycarton'] = array('title' => ucwords($obj->lang['qtyCarton']), 'dbfield' => 'qtycarton', 'width' => '70px', 'format' => 'number');
            $arrDataStructure['qtypackage'] = array('title' => ucwords($obj->lang['qtyPackage']), 'dbfield' => 'qtypackage', 'width' => '120px', 'format' => 'number');
            $arrDataStructure['qty'] = array('title' => ucwords($obj->lang['qty']), 'dbfield' => 'qty', 'width' => '70px', 'format' => 'number');
        }
        $arrDataStructure['documenttype'] = array('title' => ucwords($obj->lang['documentType']), 'dbfield' => 'documenttype', 'width' => '120px');
        $arrDataStructure['submissionnumber'] = array('title' => ucwords($obj->lang['submissionNumber']), 'dbfield' => 'submissionnumber', 'width' => '140px');
        $arrDataStructure['submissiondate'] = array('title' => ucwords($obj->lang['submissionDate']), 'dbfield' => 'submissiondate', 'width' => '120px', 'format' => 'date', 'align' => 'center');
        $arrDataStructure['invoicenumber'] = array('title' => ucwords($obj->lang['invoiceNumber']), 'dbfield' => 'invoicenumber', 'width' => '140px');
        $arrDataStructure['invoicedate'] = array('title' => ucwords($obj->lang['invoiceDate']), 'dbfield' => 'invoicedate', 'width' => '120px', 'format' => 'date', 'align' => 'center');
        $arrDataStructure['blnumber'] = array('title' => ucwords($obj->lang['blNumber']), 'dbfield' => 'blnumber', 'width' => '140px');
        $arrDataStructure['bldate'] = array('title' => ucwords($obj->lang['blDate']), 'dbfield' => 'bldate', 'width' => '120px', 'format' => 'date', 'align' => 'center');
        $arrDataStructure['registrationnumber'] = array('title' => ucwords($obj->lang['registerNumber']), 'dbfield' => 'registrationnumber', 'width' => '150px');
        $arrDataStructure['registrationdate'] = array('title' => ucwords($obj->lang['registerDate']), 'dbfield' => 'registrationdate', 'width' => '120px', 'format' => 'date', 'align' => 'center');
        $arrDataStructure['warehouse'] = array('title' => ucwords($obj->lang['warehouse']), 'dbfield' => 'warehousename', 'width' => '140px');
        $arrDataStructure['warehouseLayout'] = array('title' => ucwords($obj->lang['warehouseLayout']), 'dbfield' => 'warehouselayoutname', 'width' => '150px');
        $arrDataStructure['customer'] = array('title' => ucwords($obj->lang['customer']), 'dbfield' => 'customername', 'width' => '180px');
        $arrDataStructure['supplier'] = array('title' => ucwords($obj->lang['supplier']), 'dbfield' => 'suppliername', 'width' => '180px');
        $arrDataStructure['shipper'] = array('title' => ucwords($obj->lang['shipper']), 'dbfield' => 'shippername', 'width' => '180px');
        $arrDataStructure['status'] = array('title' => ucwords($obj->lang['status']), 'dbfield' => 'statusname', 'width' => '80px');
        

        
        break;

    default:
        $arrDataStructure['rowNumber'] = array('title' => '#', 'align' => 'right', 'width' => '40px', 'autoNumber' => true, 'sortable' => false);
        $arrDataStructure['code'] = array('title' => ucwords($obj->lang['code']), 'dbfield' => 'code', 'width' => '110px');
        $arrDataStructure['trdate'] = array('title' => ucwords($obj->lang['date']), 'dbfield' => 'date', 'width' => '120px', 'format' => 'date', 'align' => 'center');
        if (!$isGrouping) {
            $arrDataStructure['itemcode'] = array('title' => ucwords($obj->lang['itemCode']), 'dbfield' => 'itemcode', 'width' => '150px', 'format' => 'string');
            $arrDataStructure['itemname'] = array('title' => ucwords($obj->lang['itemName']), 'dbfield' => 'label', 'width' => '600px');
            $arrDataStructure['containernumber'] = array('title' => ucwords($obj->lang['containerNumber']), 'dbfield' => 'containernumber', 'width' => '220px');
            $arrDataStructure['qtycarton'] = array('title' => ucwords($obj->lang['qtyCarton']), 'dbfield' => 'qtycarton', 'width' => '70px', 'format' => 'number');
            $arrDataStructure['qtypackage'] = array('title' => ucwords($obj->lang['qtyPackage']), 'dbfield' => 'qtypackage', 'width' => '120px', 'format' => 'number');
            $arrDataStructure['qty'] = array('title' => ucwords($obj->lang['qty']), 'dbfield' => 'qty', 'width' => '70px', 'format' => 'number');
        }
        $arrDataStructure['documenttype'] = array('title' => ucwords($obj->lang['documentType']), 'dbfield' => 'documenttype', 'width' => '120px');
        $arrDataStructure['submissionnumber'] = array('title' => ucwords($obj->lang['submissionNumber']), 'dbfield' => 'submissionnumber', 'width' => '140px');
        $arrDataStructure['submissiondate'] = array('title' => ucwords($obj->lang['submissionDate']), 'dbfield' => 'submissiondate', 'width' => '120px', 'format' => 'date', 'align' => 'center');
        $arrDataStructure['invoicenumber'] = array('title' => ucwords($obj->lang['invoiceNumber']), 'dbfield' => 'invoicenumber', 'width' => '140px');
        $arrDataStructure['invoicedate'] = array('title' => ucwords($obj->lang['invoiceDate']), 'dbfield' => 'invoicedate', 'width' => '120px', 'format' => 'date', 'align' => 'center');
        $arrDataStructure['blnumber'] = array('title' => ucwords($obj->lang['blNumber']), 'dbfield' => 'blnumber', 'width' => '140px');
        $arrDataStructure['bldate'] = array('title' => ucwords($obj->lang['blDate']), 'dbfield' => 'bldate', 'width' => '120px', 'format' => 'date', 'align' => 'center');
        $arrDataStructure['registrationnumber'] = array('title' => ucwords($obj->lang['registerNumber']), 'dbfield' => 'registrationnumber', 'width' => '150px');
        $arrDataStructure['registrationdate'] = array('title' => ucwords($obj->lang['registerDate']), 'dbfield' => 'registrationdate', 'width' => '120px', 'format' => 'date', 'align' => 'center');
        $arrDataStructure['warehouse'] = array('title' => ucwords($obj->lang['warehouse']), 'dbfield' => 'warehousename', 'width' => '140px');
        $arrDataStructure['warehouseLayout'] = array('title' => ucwords($obj->lang['warehouseLayout']), 'dbfield' => 'warehouselayoutname', 'width' => '150px');
        $arrDataStructure['customer'] = array('title' => ucwords($obj->lang['customer']), 'dbfield' => 'customername', 'width' => '180px');
        $arrDataStructure['supplier'] = array('title' => ucwords($obj->lang['supplier']), 'dbfield' => 'suppliername', 'width' => '180px');
        $arrDataStructure['shipper'] = array('title' => ucwords($obj->lang['shipper']), 'dbfield' => 'shippername', 'width' => '180px');
        $arrDataStructure['status'] = array('title' => ucwords($obj->lang['status']), 'dbfield' => 'statusname', 'width' => '80px');
        


        break;
}

$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $obj->lang['itemReceivingPlanReport'];
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();
array_push($arrTemplate, $arrHeaderTemplate);

if ($isGrouping) {
    $arrDataDetailStructure = array();
    $arrDataDetailStructure['itemcode'] = array('title' => ucwords($obj->lang['itemCode']), 'dbfield' => 'itemcode', 'width' => '150px', 'format' => 'string');
    $arrDataDetailStructure['itemname'] = array('title' => ucwords($obj->lang['itemName']), 'dbfield' => 'label', 'width' => '600px');
    $arrDataDetailStructure['containernumber'] = array('title' => ucwords($obj->lang['containerNumber']), 'dbfield' => 'containernumber', 'width' => '220px');
    $arrDataDetailStructure['qtycarton'] = array('title' => ucwords($obj->lang['qtyCarton']), 'dbfield' => 'qtycarton', 'width' => '70px', 'format' => 'number');
    $arrDataDetailStructure['qtypackage'] = array('title' => ucwords($obj->lang['qtyPackage']), 'dbfield' => 'qtypackage', 'width' => '120px', 'format' => 'number');
    $arrDataDetailStructure['qty'] = array('title' => ucwords($obj->lang['qty']), 'dbfield' => 'qty', 'width' => '70px', 'format' => 'number');
    // $arrDataDetailStructure['amount'] = array('title' => ucwords($obj->lang['value']), 'dbfield' => 'amount', 'width' => '90px', 'format' => 'number', 'calculateTotal' => true);

    $arrDetailTemplate = array();
    $arrDetailTemplate['reportWidth'] = '100%';
    $arrDetailTemplate['dataStructure'] = $arrDataDetailStructure;
    $arrDetailTemplate['total'] = array();

    array_push($arrTemplate, $arrDetailTemplate);
}

$arrWarehouse = $class->convertForCombobox($warehouse->searchData($warehouse->tableName . '.statuskey', 1, true, '', 'order by name asc'), 'pkey', 'name');
// $arrWarehouseLayout = $class->convertForCombobox($warehouseLayout->searchData('', '', true, '', 'order by name asc'), 'pkey', 'name');
$arrWarehouseLayout = $class->convertForCombobox($warehouseLayout->searchData($warehouseLayout->tableName . '.statuskey', 1, true, ' and '. $warehouseLayout->tableName.'.pkey <> 0', 'order by name asc'), 'pkey', 'name');
$arrCustomer = $class->convertForCombobox($customer->searchData($customer->tableName . '.statuskey', 2, true, '', 'order by name asc'), 'pkey', 'name');
$arrSupplier = $class->convertForCombobox($supplier->searchData($supplier->tableName . '.statuskey', 1, true, '', 'order by name asc'), 'pkey', 'name');

$arrTwigVar['inputCode'] = $class->inputText('code');
$arrTwigVar['inputStartDate'] = $class->inputDate('trStartDate', array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputEndDate'] = $class->inputDate('trEndDate', array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputSelWarehouse'] = $class->inputSelect('selWarehouse[]', $arrWarehouse, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputSelWarehouseLayout'] = $class->inputSelect('selWarehouseLayout[]', $arrWarehouseLayout, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputSelCustomer'] = $class->inputSelect('selCustomer[]', $arrCustomer, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputSelSupplier'] = $class->inputSelect('selSupplier[]', $arrSupplier, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputSelShipper'] = $class->inputSelect('selShipper[]', $arrSupplier, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputIsGrouping'] = $class->inputCheckBox('isGrouping');
$arrTwigVar['arrTemplate'] = $arrHeaderTemplate;
// $arrTwigVar['isGrouping'] = $isGrouping;
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

    if (isset($_POST['selWarehouse']) && !empty($_POST['selWarehouse'])) {
        $key = implode(',', $class->oDbCon->paramString($_POST['selWarehouse']));
        $criteria .= ' AND ' . $obj->tableName . '.warehousekey in(' . $key . ')';

        $rsCriteria = $warehouse->searchData('', '', true, ' and ' . $warehouse->tableName . '.pkey in (' . $key . ')');
        $arrTemp = array();
        for ($k = 0; $k < count($rsCriteria); $k++) {
            array_push($arrTemp, $rsCriteria[$k]['name']);
        }
        array_push($arrFilterInformation, array('label' => $obj->lang['warehouse'], 'filter' => implode(', ', $arrTemp)));
    }

    if (isset($_POST['selWarehouseLayout']) && !empty($_POST['selWarehouseLayout'])) {
        $key = implode(',', $class->oDbCon->paramString($_POST['selWarehouseLayout']));
        $criteria .= ' AND ' . $obj->tableName . '.warehouselayoutkey in(' . $key . ')';

        $rsCriteria = $warehouseLayout->searchData('', '', true, ' and ' . $warehouseLayout->tableName . '.pkey in (' . $key . ')');
        $arrTemp = array();
        for ($k = 0; $k < count($rsCriteria); $k++) {
            array_push($arrTemp, $rsCriteria[$k]['name']);
        }
        array_push($arrFilterInformation, array('label' => $obj->lang['warehouseLayout'], 'filter' => implode(', ', $arrTemp)));
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

    if (isset($_POST['selSupplier']) && !empty($_POST['selSupplier'])) {
        $key = implode(',', $class->oDbCon->paramString($_POST['selSupplier']));
        $criteria .= ' AND ' . $obj->tableName . '.supplierkey in(' . $key . ')';

        $rsCriteria = $supplier->searchData('', '', true, ' and ' . $supplier->tableName . '.pkey in (' . $key . ')');
        $arrTemp = array();
        for ($k = 0; $k < count($rsCriteria); $k++) {
            array_push($arrTemp, $rsCriteria[$k]['name']);
        }
        array_push($arrFilterInformation, array('label' => $obj->lang['supplier'], 'filter' => implode(', ', $arrTemp)));
    }

    if (isset($_POST['selShipper']) && !empty($_POST['selShipper'])) {
        $key = implode(',', $class->oDbCon->paramString($_POST['selShipper']));
        $criteria .= ' AND ' . $obj->tableName . '.shipperkey in(' . $key . ')';

        $rsCriteria = $supplier->searchData('', '', true, ' and ' . $supplier->tableName . '.pkey in (' . $key . ')');
        $arrTemp = array();
        for ($k = 0; $k < count($rsCriteria); $k++) {
            array_push($arrTemp, $rsCriteria[$k]['name']);
        }
        array_push($arrFilterInformation, array('label' => $obj->lang['shipper'], 'filter' => implode(', ', $arrTemp)));
    }

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
        'date' => 'trdate',
        'trdate' => 'trdate',
        'warehouse' => 'warehousename',
        'warehousename' => 'warehousename',
        'warehouseLayout' => 'warehouselayoutname',
        'warehouselayoutname' => 'warehouselayoutname',
        'customer' => 'customername',
        'customername' => 'customername',
        'supplier' => 'suppliername',
        'suppliername' => 'suppliername',
        'shipper' => 'shippername',
        'shippername' => 'shippername',
        'status' => 'statusname'
    );

    $orderByKey = (!empty($_POST['hidOrderBy'])) ? $obj->oDbCon->paramOrder($_POST['hidOrderBy']) : 'date';
    $orderBy = isset($orderByMap[$orderByKey]) ? $orderByMap[$orderByKey] : 'trdate';
    $orderType = (isset($_POST['hidOrderType']) && !empty($_POST['hidOrderType']) && $_POST['hidOrderType'] == 1) ? 'desc' : 'asc';
    $order = 'order by ' . $orderBy . ' ' . $orderType;

    $rs = $obj->searchData('', '', true, $criteria, $order);
    $tempreport = '';

    if (!$isGrouping) {
        $arrFlatData = array();

        for ($i = 0; $i < count($rs); $i++) {
            $documentType = (int)$rs[$i]['documenttype'];
            $rs[$i]['documentname'] = isset($arrDocumentType[$documentType]) ? $arrDocumentType[$documentType]['label'] : $rs[$i]['documenttype'];
            $rsDetail = $obj->getDetailWithRelatedInformation($rs[$i]['pkey'], $detailCriteria);

            if (empty($rsDetail)) {
                continue;
            }

            for ($j = 0; $j < count($rsDetail); $j++) {
                $flatRow = array();
                $flatRow['code'] = $rs[$i]['code'];
                $flatRow['trdate'] = $rs[$i]['trdate'];
                // $flatRow['itemreceivingplanheadercode'] = $rs[$i]['itemreceivingplanheadercode'];
                $flatRow['documenttype'] = $rs[$i]['documentname'];
                $flatRow['submissionnumber'] = $rs[$i]['submissionnumber'];
                $flatRow['submissiondate'] = $rs[$i]['submissiondate'];
                $flatRow['invoicenumber'] = $rs[$i]['invoicenumber'];
                $flatRow['invoicedate'] = $rs[$i]['invoicedate'];
                $flatRow['blnumber'] = $rs[$i]['blnumber'];
                $flatRow['bldate'] = $rs[$i]['bldate'];
                $flatRow['registrationnumber'] = $rs[$i]['registrationnumber'];
                $flatRow['registrationdate'] = $rs[$i]['registrationdate'];
                $flatRow['warehousename'] = $rs[$i]['warehousename'];
                $flatRow['warehouselayoutname'] = $rs[$i]['warehouselayoutname'];
                $flatRow['customername'] = $rs[$i]['customername'];
                $flatRow['suppliername'] = $rs[$i]['suppliername'];
                $flatRow['shippername'] = $rs[$i]['shippername'];
                $flatRow['statusname'] = $rs[$i]['statusname'];
                $flatRow['itemcode'] = $rsDetail[$j]['itemcode'];
                $flatRow['label'] = $rsDetail[$j]['label'];
                $flatRow['containernumber'] = $rsDetail[$j]['containernumber'];
                $flatRow['qtycarton'] = $rsDetail[$j]['qtycarton'];
                $flatRow['qtypackage'] = $rsDetail[$j]['qtypackage'];
                $flatRow['qty'] = $rsDetail[$j]['qty'];
                array_push($arrFlatData, $flatRow);
            }
        }

        $rs = $arrFlatData;
    }

    for ($i = 0; $i < count($rs); $i++) {
        $rs[$i]['date'] = $rs[$i]['trdate'];
        $documentType = (int)$rs[$i]['documenttype'];
        $rs[$i]['documenttype'] = isset($arrDocumentType[$documentType]) ? $arrDocumentType[$documentType]['label'] : $rs[$i]['documenttype'];

        if ($isGrouping) {
            $rsDetail = $obj->getDetailWithRelatedInformation($rs[$i]['pkey'], $detailCriteria);
            if (!empty($rsDetail)) {
                $rs[$i]['_detail_'] = array('arrTemplate' => $arrDetailTemplate, 'data' => $rsDetail);
            }
        }

        $return = $obj->formatReportRows(array('data' => $rs[$i]), $arrTemplate);

        array_push($dataToExport, $return['data']);
        $tempreport .= $return['html'];
        $arrTemplate[0]['total'] = $obj->arraySum($arrTemplate[0]['total'], $return['subtotal'][0]);
    }

    // $tableHeader = $twig->render('template-header.html', $arrTwigVar);
    // $tempreport = $tableHeader . $tempreport;

    $obj->generateReport($_POST, $tempreport, $arrTemplate, $dataToExport, $arrFilterInformation, $tableHeader);
    // $obj->generateReport($_POST, $tempreport, $arrTemplate,$dataToExport,$arrFilterInformation);
}


$arrStatus = $class->convertForCombobox($arrStatus, 'pkey', 'status');
$arrTwigVar['inputSelStatus'] = $class->inputSelect('selStatus[]', $arrStatus, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['arrTemplate'] = $arrHeaderTemplate;

echo $twig->render('reportItemReceivingPlan.html', $arrTwigVar);

?>