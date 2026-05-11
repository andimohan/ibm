<?php
require_once '../../_config.php';
require_once '_include.php';
require_once 'function-v2.php';

require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/ItemReceiving.class.php';
require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/Warehouse.class.php';
require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/Customer.class.php';
require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/Supplier.class.php';
require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/Currency.class.php';
require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/WarehouseLayout.class.php';

$OBJ = new ItemReceiving();
$customer = new Customer();
$supplier = new Supplier();
$warehouse = new Warehouse();
$currency = new Currency();
$warehouseLayout = new WarehouseLayout();

$MODULE_NAME = 'itemReceiving';
$TITLE = $OBJ->lang['itemReceiving'];

// ===================== COMPILING DATA
$arrDisplayData = array();
$dateMap = array();
$policeNumberMap = array();

$code = '[auto code]';
$currCode = '';
$indexCtr = 0;


$headerRow = 2;
$detailRow = 5;

$arrHeaderData = array();
$arrDetailData = array();

$rsWarehouseCol = $warehouse->searchData('', '', true, ' and ' . $warehouse->tableName . '.statuskey = 1 ');
$rsWarehouseCol = array_column($rsWarehouseCol, null, 'name');

$rsCurrencyCol = $currency->searchData('', '', true, ' and ' . $currency->tableName . '.statuskey = 1 ');
$rsCurrencyCol = array_column($rsCurrencyCol, null, 'name');

$rsCustomerCol = $customer->searchData('', '', true, ' and ' . $customer->tableName . '.statuskey = 2 ');
$rsCustomerCol = array_column($rsCustomerCol, null, 'name');


$rsSupplierCol = $supplier->searchData('', '', true, ' and ' . $supplier->tableName . '.statuskey = 1 ');
$rsSupplierCol = array_column($rsSupplierCol, null, 'name');

$rsWarehouseLayoutCol = $warehouseLayout->searchData('', '', true, ' and ' . $warehouseLayout->tableName . '.statuskey = 1 ');
$rsWarehouseLayoutCol = array_column($rsWarehouseLayoutCol, null, 'name');

$arrErrorMsg = array();
$arrWarehouseName = array();
$arrCustomerName = array();
$arrSupplierName = array();
$arrShipperName = array();
$arrWarehouseLayoutName = array();



for ($row = $headerRow; $row <= $headerRow; ++$row) {

    $trdate = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
    $trdate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($trdate);
    $trdate = $trdate->getTimestamp();

    $warehouseId = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
    $warehouseLayoutId = $worksheet->getCellByColumnAndRow(3, $row)->getValue();

    $receivedDate = $worksheet->getCellByColumnAndRow(4, $row)->getValue();
    $receivedDate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($receivedDate);
    $receivedDate = $receivedDate->getTimestamp();

    $customerId = $worksheet->getCellByColumnAndRow(5, $row)->getValue();
    $supplierId = $worksheet->getCellByColumnAndRow(6, $row)->getValue();
    $sipperId = $worksheet->getCellByColumnAndRow(7, $row)->getValue();

    $documentType = $worksheet->getCellByColumnAndRow(8, $row)->getValue();

    $submissionNumber = $worksheet->getCellByColumnAndRow(9, $row)->getValue();
    $submissionDate = $worksheet->getCellByColumnAndRow(10, $row)->getValue();
    $submissionDate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($submissionDate);
    $submissionDate = $submissionDate->getTimestamp();

    $invoiceNumber = $worksheet->getCellByColumnAndRow(11, $row)->getValue();
    $invoiceDate = $worksheet->getCellByColumnAndRow(12, $row)->getValue();
    $invoiceDate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($invoiceDate);
    $invoiceDate = $invoiceDate->getTimestamp();

    $blNumber = $worksheet->getCellByColumnAndRow(13, $row)->getValue();
    $blDate = $worksheet->getCellByColumnAndRow(14, $row)->getValue();
    $blDate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($blDate);
    $blDate = $blDate->getTimestamp();

    $registerNumber = $worksheet->getCellByColumnAndRow(15, $row)->getValue();
    $registerDate = $worksheet->getCellByColumnAndRow(16, $row)->getValue();
    $registerDate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($registerDate);
    $registerDate = $registerDate->getTimestamp();

    $currencyId = $worksheet->getCellByColumnAndRow(17, $row)->getValue();

    $valueType = $worksheet->getCellByColumnAndRow(18, $row)->getValue();
    $notes = $worksheet->getCellByColumnAndRow(19, $row)->getValue();

    array_push($arrWarehouseName, $warehouseId);
    array_push($arrWarehouseLayoutName, $warehouseLayoutId);
    array_push($arrCustomerName, $customerId);
    array_push($arrSupplierName, $supplierId);
    array_push($arrShipperName, $supplierId);

    $warehouseId = $rsWarehouseCol[$warehouseId];
    $customerId = $rsCustomerCol[$customerId];
    $supplierId = $rsSupplierCol[$supplierId];
    $sipperId = $rsSupplierCol[$sipperId];
    $currencyId = $rsCurrencyCol[$currencyId];
    $warehouseLayoutId = $rsWarehouseLayoutCol[$warehouseLayoutId];
    
    array_push($arrHeaderData,array(
        'trdate' => $trdate,
        'warehouse_id' => $warehouseId['pkey'],
        'warehouse_layout_id' => $warehouseLayoutId['pkey'],
        'received_date' => $receivedDate,
        'customer_id' => $customerId['pkey'],
        'supplier_id' => $supplierId['pkey'],
        'shipper_id' => $supplierId['pkey'],
        'document_type' => $documentType,
        'submission_number' => $submissionNumber,
        'submission_date' => $submissionDate,
        'invoice_number' => $invoiceNumber,
        'invoice_date' => $invoiceDate,
        'bl_number' => $blNumber,
        'bl_date' => $blDate,
        'register_number' => $registerNumber,
        'register_date' => $registerDate,
        'currency_id' => $currencyId['pkey'],
        'value_type' => $valueType,
        'notes' => $notes
    ));

}

foreach ($arrWarehouseName as $row) {
    if (empty($row))
        continue;

    if (!array_key_exists($row, $rsWarehouseCol))
        array_push($arrErrorMsg, $row . ' tidak terdaftar');
}

foreach ($arrWarehouseLayoutName as $row) {
    if (empty($row))
        continue;

    if (!array_key_exists($row, $rsWarehouseLayoutCol))
        array_push($arrErrorMsg, $row . ' tidak terdaftar');
}

foreach ($arrCustomerName as $row) {
    
    if (empty($row))
        continue;

    if (!array_key_exists($row, $rsCustomerCol))
        array_push($arrErrorMsg, $row . ' tidak terdaftar');
}

foreach ($arrSupplierName as $row) {
    if (empty($row))
        continue;

    if (!array_key_exists($row, $rsSupplierCol))
        array_push($arrErrorMsg, $row . ' tidak terdaftar');
}

foreach ($arrShipperName as $row) {
    if (empty($row))
        continue;

    if (!array_key_exists($row, $rsSupplierCol))
        array_push($arrErrorMsg, $row . ' tidak terdaftar');
}

if (!empty($arrErrorMsg)) {
    echo '<table>';
    foreach ($arrErrorMsg as $row) {
        echo '<tr>';
        echo '<td style="padding:0.1em"><div style="color:red;"> ' . $row . ' </div></td>';
        echo '</tr>';
    }
    echo '</table>';
    die;
}

for ($row = $detailRow; $row <= $highestRow; ++$row) {

    $itemBarcode = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
    $itemCode = $worksheet->getCellByColumnAndRow(3, $row)->getValue();
    $itemName = $worksheet->getCellByColumnAndRow(4, $row)->getValue();
    $hs = $worksheet->getCellByColumnAndRow(5, $row)->getValue();
    $countryOfOriginId = $worksheet->getCellByColumnAndRow(6, $row)->getValue();
    $itemCategory = $worksheet->getCellByColumnAndRow(7, $row)->getValue();
    $facility = $worksheet->getCellByColumnAndRow(8, $row)->getValue();
    $orderList = $worksheet->getCellByColumnAndRow(9, $row)->getValue();
    $qty = $worksheet->getCellByColumnAndRow(10, $row)->getValue();
    $unit = $worksheet->getCellByColumnAndRow(11, $row)->getValue();
    $category = $worksheet->getCellByColumnAndRow(12, $row)->getValue();
    $alcoholContent = $worksheet->getCellByColumnAndRow(13, $row)->getValue();
    $mililiter = $worksheet->getCellByColumnAndRow(14, $row)->getValue();
    $qtyCarton = $worksheet->getCellByColumnAndRow(15, $row)->getValue();
    $qtyPackage = $worksheet->getCellByColumnAndRow(16, $row)->getValue();
    $packaging = $worksheet->getCellByColumnAndRow(17, $row)->getValue();
    $amount = $worksheet->getCellByColumnAndRow(18, $row)->getValue();

    if(empty($itemName)) {
        continue;
    }

    array_push($arrDetailData, array(
        'item_barcode' => $itemBarcode,
        'item_code' => $itemCode,
        'item_name' => $itemName,
        'hs' => $hs,
        'country_of_origin_id' => $countryOfOriginId,
        'item_category' => $itemCategory,
        'facility' => $facility,
        'order_list' => $orderList,
        'qty' => $qty,
        'unit' => $unit,
        'category' => $category,
        'alcohol_content' => $alcoholContent,
        'mililiter' => $mililiter,
        'qty_carton' => $qtyCarton,
        'qty_package' => $qtyPackage,
        'packaging' => $packaging,
        'amount' => $amount
    ));

}

for ($i = 0; $i < count($arrHeaderData); $i++) {

    $data = $arrHeaderData[$i];

    $date = date('Y-m-d H:i:s', $data['trdate']);
    $receivedDate = date('Y-m-d H:i:s', $data['received_date']);
    $submissionDate = date('Y-m-d H:i:s', $data['submission_date']);
    $invoiceDate = date('Y-m-d H:i:s', $data['invoice_date']);
    $blDate = date('Y-m-d H:i:s', $data['bl_date']);
    $registerDate = date('Y-m-d H:i:s', $data['register_date']);

    $arrParam = array();
    $arrParam['code'] = 'xxxxx';
    $arrParam['trDate'] = $OBJ->formatDBDate($date, 'd / m / Y');
    $arrParam['selWarehouseKey'] = $data['warehouse_id'];
    $arrParam['selWarehouseLayoutKey'] = $data['warehouse_layout_id'];
    $arrParam['hidCustomerKey'] = $data['customer_id'];
    $arrParam['hidSupplierKey'] = $data['supplier_id'];
    $arrParam['hidShipperKey'] = $data['shipper_id'];
    $arrParam['documentType'] = $data['document_type'];
    $arrParam['submissionNumber'] = $data['submission_number'];
    $arrParam['submissionDate'] = $OBJ->formatDBDate($submissionDate, 'd / m / Y');
    $arrParam['invoiceNumber'] = $data['invoice_number'];
    $arrParam['invoiceDate'] = $OBJ->formatDBDate($invoiceDate, 'd / m / Y');
    $arrParam['blNumber'] = $data['bl_number'];
    $arrParam['blDate'] = $OBJ->formatDBDate($blDate, 'd / m / Y');
    $arrParam['registrationNumber'] = $data['register_number'];
    $arrParam['registrationDate'] = $OBJ->formatDBDate($registerDate, 'd / m / Y');
    $arrParam['selCurrencyKey'] = $data['currency_id'];
    $arrParam['valueType'] = $data['value_type'];
    $arrParam['trDesc'] = $data['notes'];

    $arrParam['hidDetailKey'] = array();
    $arrParam['itemDetailBarcode'] = array();
    $arrParam['itemDetailCode'] = array();
    $arrParam['itemDetailName'] = array();
    $arrParam['hs'] = array();
    $arrParam['countryOfOriginId'] = array();
    $arrParam['itemCategoryName'] = array();
    $arrParam['facility'] = array();
    $arrParam['orderList'] = array();
    $arrParam['qty'] = array();
    $arrParam['unitName'] = array();
    $arrParam['category'] = array();
    $arrParam['alcoholContent'] = array();
    $arrParam['mililiter'] = array();
    $arrParam['qtyCarton'] = array();
    $arrParam['qtyPackage'] = array();
    $arrParam['packagingName'] = array();
    $arrParam['amount'] = array();


    foreach ($arrDetailData as $detail) {
        array_push($arrParam['hidDetailKey'], 0);
        array_push($arrParam['itemDetailBarcode'], $detail['item_barcode']);
        array_push($arrParam['itemDetailCode'], $detail['item_code']);
        array_push($arrParam['itemDetailName'], $detail['item_name']);
        array_push($arrParam['hs'], $detail['hs']);
        array_push($arrParam['countryOfOriginId'], $detail['country_of_origin_id']);
        array_push($arrParam['itemCategoryName'], $detail['item_category']);
        array_push($arrParam['facility'], $detail['facility']);
        array_push($arrParam['orderList'], $detail['order_list']);
        array_push($arrParam['qty'], $detail['qty']);
        array_push($arrParam['unitName'], $detail['unit']);
        array_push($arrParam['category'], $detail['category']);
        array_push($arrParam['alcoholContent'], $detail['alcohol_content']);
        array_push($arrParam['mililiter'], $detail['mililiter']);
        array_push($arrParam['qtyCarton'], $detail['qty_carton']);
        array_push($arrParam['qtyPackage'], $detail['qty_package']);
        array_push($arrParam['packagingName'], $detail['packaging']);
        array_push($arrParam['amount'], $detail['amount']);
    }
    

    $result = $OBJ->addData($arrParam);

    if (!$result[0]['valid']) {
        echo '<span style="font-weight:bold;color:red;font-size:12px;margin:4px">ERROR :</span><br>';
            foreach ($result as $rs) {
                echo '<span style="margin:4px;color:red">- ' . $rs['message'] . '</span><br>';
            }
    } else {
        echo '<span style="color:green;margin:4px">Import data berhasil.  <br> - ' . $result[0]['data']['code'] . '<br></span>';
    }

}

?>