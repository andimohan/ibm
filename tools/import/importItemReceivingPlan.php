<?php
require_once '../../_config.php';
require_once '_include.php';
require_once 'function-v2.php';

require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/ItemReceivingPlan.class.php';
require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/Warehouse.class.php';
require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/Customer.class.php';
require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/Supplier.class.php';
require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/Currency.class.php';
require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/WarehouseLayout.class.php';
require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/Brand.class.php';
require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/ItemCategory.class.php';
require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/TransactionType.class.php';
require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/Country.class.php';
require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/ItemUnit.class.php';


$OBJ = new ItemReceivingPlan();
$customer = new Customer();
$supplier = new Supplier();
$warehouse = new Warehouse();
$currency = new Currency();
$warehouseLayout = new WarehouseLayout();
$brand = new Brand();
$transactionType = new TransactionType();
$country = new Country();
$itemUnit = new ItemUnit();
$itemCategory = new ItemCategory();

$MODULE_NAME = 'itemReceiving';
$TITLE = $OBJ->lang['itemReceiving'];

// ===================== COMPILING DATA
$arrDisplayData = array();
$dateMap = array();

function removeSpaceAndLowerCase($value)
{
    $result = strtolower($value);
    $result = str_replace(' ', '', $result);
    return $result;
}

$code = '[auto code]';
$currCode = '';
$indexCtr = 0;


$headerRow = 2;
$detailRow = 5;

$arrHeaderData = array();
$arrDetailData = array();

$rsWarehouse = $warehouse->searchDataRow(
    array(
        $warehouse->tableName . '.pkey',
        $warehouse->tableName . '.code',
        $warehouse->tableName . '.name as originalname',
        'lower(replace(' . $warehouse->tableName . '.name," ","")) as name',
        $warehouse->tableName . '.statuskey'
    ),
    ' and ' . $warehouse->tableName . '.statuskey = 1'
);
$arrWarehouseNameCol = array_column($rsWarehouse, 'name');
$rsWarehouseCols = array_column($rsWarehouse, null, 'name');

$rsCurrency = $currency->searchDataRow(
    array(
        $currency->tableName . '.pkey',
        $currency->tableName . '.code',
        $currency->tableName . '.name as originalname',
        'lower(replace(' . $currency->tableName . '.name," ","")) as name',
        $currency->tableName . '.statuskey'
    ),
    ' and ' . $currency->tableName . '.statuskey = 1'
);
$arrCurrencyNameCol = array_column($rsCurrency, 'name');
$rsCurrencyCols = array_column($rsCurrency, null, 'name');

$rsCustomer = $customer->searchDataRow(
    array(
        $customer->tableName . '.pkey',
        $customer->tableName . '.code',
        $customer->tableName . '.name as originalname',
        'lower(replace(' . $customer->tableName . '.name," ","")) as name',
        $customer->tableName . '.statuskey'
    ),
    ' and ' . $customer->tableName . '.statuskey = 2'
);
$arrCustomerNameCol = array_column($rsCustomer, 'name');
$rsCustomerCols = array_column($rsCustomer, null, 'name');

$rsSupplier = $supplier->searchDataRow(
    array(
        $supplier->tableName . '.pkey',
        $supplier->tableName . '.code',
        $supplier->tableName . '.name as originalname',
        'lower(replace(' . $supplier->tableName . '.name," ","")) as name',
        $supplier->tableName . '.statuskey'
    ),
    ' and ' . $supplier->tableName . '.statuskey = 1'
);
$arrSupplierNameCol = array_column($rsSupplier, 'name');
$rsSupplierCols = array_column($rsSupplier, null, 'name');

$rsWarehouseLayout = $warehouseLayout->searchDataRow(
    array(
        $warehouseLayout->tableName . '.pkey',
        $warehouseLayout->tableName . '.code',
        $warehouseLayout->tableName . '.name as originalname',
        'lower(replace(' . $warehouseLayout->tableName . '.name," ","")) as name',
        $warehouseLayout->tableName . '.statuskey'
    ),
    ' and ' . $warehouseLayout->tableName . '.statuskey = 1'
);
$arrWarehouseLayoutNameCol = array_column($rsWarehouseLayout, 'name');
$rsWarehouseLayoutCols = array_column($rsWarehouseLayout, null, 'name');

$rsBrand = $brand->searchDataRow(
    array(
        $brand->tableName . '.pkey',
        $brand->tableName . '.code',
        $brand->tableName . '.name as originalname',
        'lower(replace(' . $brand->tableName . '.name," ","")) as name',
        $brand->tableName . '.statuskey'
    ),
    ' and ' . $brand->tableName . '.statuskey = 1'
);
$arrBrandNameCol = array_column($rsBrand, 'name');
$rsBrandCols = array_column($rsBrand, null, 'name');

$rsItemCategory = $itemCategory->searchDataRow(
    array(
        $itemCategory->tableName . '.pkey',
        $itemCategory->tableName . '.code',
        $itemCategory->tableName . '.name as originalname',
        'lower(replace(' . $itemCategory->tableName . '.name," ","")) as name',
        $itemCategory->tableName . '.statuskey'
    ),
    ' and ' . $itemCategory->tableName . '.statuskey = 1'
);
$arrItemCategoryNameCol = array_column($rsItemCategory, 'name');
$rsItemCategoryCols = array_column($rsItemCategory, null, 'name');


$rsTransactionType = $transactionType->searchDataRow(
    array(
        $transactionType->tableName . '.pkey',
        $transactionType->tableName . '.code',
        $transactionType->tableName . '.name as originalname',
        'lower(replace(' . $transactionType->tableName . '.name," ","")) as name',
        $transactionType->tableName . '.statuskey'
    ),
    ' and ' . $transactionType->tableName . '.statuskey = 1'
);
$arrTransactionTypeNameCol = array_column($rsTransactionType, 'name');
$rsTransactionTypeCols = array_column($rsTransactionType, null, 'name');


$rsCountry = $country->searchDataRow(
    array(
        $country->tableName . '.pkey',
        $country->tableName . '.code',
        $country->tableName . '.name as originalname',
        'lower(replace(' . $country->tableName . '.name," ","")) as name',
        $country->tableName . '.statuskey'
    ),
    ' and ' . $country->tableName . '.statuskey = 1'
);
$arrCountryNameCol = array_column($rsCountry, 'name');
$rsCountryCols = array_column($rsCountry, null, 'name');

$rsItemUnit = $itemUnit->searchDataRow(
    array(
        $itemUnit->tableName . '.pkey',
        $itemUnit->tableName . '.code',
        $itemUnit->tableName . '.name as originalname',
        'lower(replace(' . $itemUnit->tableName . '.name," ","")) as name',
        $itemUnit->tableName . '.statuskey'
    ),
    ' and ' . $itemUnit->tableName . '.statuskey = 1'
);
$arrItemUnitNameCol = array_column($rsItemUnit, 'name');
$rsItemUnitCols = array_column($rsItemUnit, null, 'name');

$arrErrorMsg = array();
$arrWarehouseName = array();
$arrCustomerName = array();
$arrSupplierName = array();
$arrShipperName = array();
$arrWarehouseLayoutName = array();

$arrBrandName = array();
$arrTypename = array();
$arrTransactionTypeName = array();
$arrUnitName = array();
$arrCountryName = array();


for ($row = $headerRow; $row <= $headerRow; ++$row) {

    $trdate = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
    $trdate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($trdate);
    $trdate = $trdate->getTimestamp();

    $warehouse = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
    $warehouseId = removeSpaceAndLowerCase($warehouse);

    $warehouseLayout = $worksheet->getCellByColumnAndRow(3, $row)->getValue();
    $warehouseLayoutId = removeSpaceAndLowerCase($warehouseLayout);

    $customer = $worksheet->getCellByColumnAndRow(4, $row)->getValue();
    $customerId = removeSpaceAndLowerCase($customer);

    $supplier = $worksheet->getCellByColumnAndRow(5, $row)->getValue();
    $supplierId = removeSpaceAndLowerCase($supplier);

    $sipper = $worksheet->getCellByColumnAndRow(6, $row)->getValue();
    $sipperId = removeSpaceAndLowerCase($sipper);

    $documentType = $worksheet->getCellByColumnAndRow(7, $row)->getValue();

    $submissionNumber = $worksheet->getCellByColumnAndRow(8, $row)->getValue();
    $submissionDate = $worksheet->getCellByColumnAndRow(9, $row)->getValue();
    $submissionDate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($submissionDate);
    $submissionDate = $submissionDate->getTimestamp();

    $invoiceNumber = $worksheet->getCellByColumnAndRow(10, $row)->getValue();
    $invoiceDate = $worksheet->getCellByColumnAndRow(11, $row)->getValue();
    $invoiceDate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($invoiceDate);
    $invoiceDate = $invoiceDate->getTimestamp();

    $blNumber = $worksheet->getCellByColumnAndRow(12, $row)->getValue();
    $blDate = $worksheet->getCellByColumnAndRow(13, $row)->getValue();
    $blDate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($blDate);
    $blDate = $blDate->getTimestamp();

    $registerNumber = $worksheet->getCellByColumnAndRow(14, $row)->getValue();
    $registerDate = $worksheet->getCellByColumnAndRow(15, $row)->getValue();
    $registerDate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($registerDate);
    $registerDate = $registerDate->getTimestamp();

    $currency = $worksheet->getCellByColumnAndRow(16, $row)->getValue();
    $currencyId = removeSpaceAndLowerCase($currency);

    $valueType = $worksheet->getCellByColumnAndRow(17, $row)->getValue();
    $notes = $worksheet->getCellByColumnAndRow(18, $row)->getValue();

    array_push($arrWarehouseName, array('original' => $warehouse, 'id' => $warehouseId));
    array_push($arrWarehouseLayoutName, array('original' => $warehouseLayout, 'id' => $warehouseLayoutId));
    array_push($arrCustomerName, array('original' => $customer, 'id' => $customerId));
    array_push($arrSupplierName, array('original' => $supplier, 'id' => $supplierId));
    array_push($arrShipperName, array('original' => $sipper, 'id' => $sipperId));

    $warehouseId = $rsWarehouseCols[$warehouseId];
    $customerId = $rsCustomerCols[$customerId];
    $supplierId = $rsSupplierCols[$supplierId];
    $sipperId = $rsSupplierCols[$sipperId];
    $currencyId = $rsCurrencyCols[$currencyId];
    $warehouseLayoutId = $rsWarehouseLayoutCols[$warehouseLayoutId];


    array_push($arrHeaderData, array(
        'trdate' => $trdate,
        'warehouse_id' => $warehouseId['pkey'],
        'warehouse_layout_id' => $warehouseLayoutId['pkey'],
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
    if (empty($row['original']))
        continue;

    if (!in_array($row['id'], $arrWarehouseNameCol))
        array_push($arrErrorMsg, 'Gudang : "' . $row['original'] . '" tidak terdaftar');
}

foreach ($arrWarehouseLayoutName as $row) {
    if (empty($row['original']))
        continue;

    if (!in_array($row['id'], $arrWarehouseLayoutNameCol))
        array_push($arrErrorMsg, 'Tata Letak Gudang : "' . $row['original'] . '" tidak terdaftar');
}

foreach ($arrCustomerName as $row) {
    if (empty($row['original']))
        continue;

    if (!in_array($row['id'], $arrCustomerNameCol))
        array_push($arrErrorMsg, 'Pelanggan : "' . $row['original'] . '" tidak terdaftar');
}

foreach ($arrSupplierName as $row) {
    if (empty($row['original']))
        continue;

    if (!in_array($row['id'], $arrSupplierNameCol))
        array_push($arrErrorMsg, 'Pemasok : "' . $row['original'] . '" tidak terdaftar');
}

foreach ($arrShipperName as $row) {
    if (empty($row['original']))
        continue;

    if (!in_array($row['id'], $arrSupplierNameCol))
        array_push($arrErrorMsg, 'Shipper : "' . $row['original'] . '" tidak terdaftar');
}



for ($row = $detailRow; $row <= $highestRow; ++$row) {

    $itemCode = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
    $itemName = $worksheet->getCellByColumnAndRow(3, $row)->getValue();
    $mililiter = $worksheet->getCellByColumnAndRow(4, $row)->getValue();

    $brand = $worksheet->getCellByColumnAndRow(5, $row)->getValue();
    $brandId = removeSpaceAndLowerCase($brand);

    $type = $worksheet->getCellByColumnAndRow(6, $row)->getValue();
    $typeId = removeSpaceAndLowerCase($type);

    $qtyCarton = $worksheet->getCellByColumnAndRow(7, $row)->getValue();
    $qtyPackage = $worksheet->getCellByColumnAndRow(8, $row)->getValue();
    $alcoholContent = $worksheet->getCellByColumnAndRow(9, $row)->getValue();
    $amount = $worksheet->getCellByColumnAndRow(10, $row)->getValue();
    $hs = $worksheet->getCellByColumnAndRow(11, $row)->getValue();
    $transactionType = $worksheet->getCellByColumnAndRow(12, $row)->getValue();
    $transactionTypeId = removeSpaceAndLowerCase($transactionType);

    $category = $worksheet->getCellByColumnAndRow(13, $row)->getValue();

    $unit = $worksheet->getCellByColumnAndRow(14, $row)->getValue();
    $unitId = removeSpaceAndLowerCase($unit);

    $packaging = $worksheet->getCellByColumnAndRow(15, $row)->getValue();

    $country = $worksheet->getCellByColumnAndRow(16, $row)->getValue();
    $countryId = removeSpaceAndLowerCase($country);

    $containerNumber = $worksheet->getCellByColumnAndRow(17, $row)->getValue();
    $containerSize = $worksheet->getCellByColumnAndRow(18, $row)->getValue();
    $containerType = $worksheet->getCellByColumnAndRow(19, $row)->getValue();

    $rsBrandCol = $rsBrandCols[$brandId];
    $rsItemCategoryCol = $rsItemCategoryCols[$typeId];
    $rsTransactionTypeCol = $rsTransactionTypeCols[$transactionTypeId];
    $rsCountryCol = $rsCountryCols[$countryId];
    $rsUnitCol = $rsItemUnitCols[$unitId];


    array_push($arrBrandName, array('original' => $brand, 'id' => $brandId, 'row' => $row));
    array_push($arrTypename, array('original' => $type, 'id' => $typeId, 'row' => $row));
    array_push($arrTransactionTypeName, array('original' => $transactionType, 'id' => $transactionTypeId, 'row' => $row));
    array_push($arrUnitName, array('original' => $unit, 'id' => $unitId, 'row' => $row));
    array_push($arrCountryName, array('original' => $country, 'id' => $countryId, 'row' => $row));



    if (empty($itemName)) {
        continue;
    }

    $qty = 0;
    $label = '';

    $brandLabel = '';
    $itemTypeLabel = '';
    $mililiterLabel = '';
    $sizeInfoLabel = '';
    $alcoholContentLabel = '';

    if ($brandId != "") {
        $brandLabel = ', Merk : ' . $rsBrandCol['originalname'];
    }

    if ($typeId != "") {
        $itemTypeLabel = ', Tipe : ' . $rsItemCategoryCol['originalname'];
    }

    if ($mililiter != 0) {
        $mililiterLabel = ', ' . $mililiter . ' ML';
    }

    if ($mililiter != 0 && $qtyCarton != 0) {
        $sizeInfoLabel = ', Ukuran : ' . $qtyCarton . ' X ' . $mililiter;
    }

    if ($alcoholContent != 0) {
        $alcoholContentLabel = ', Spesifikasi lain : ' . $alcoholContent . '%';
    }

    $qty = $qtyPackage * $qtyCarton;
    $label = $itemName . $mililiterLabel . $brandLabel . $itemTypeLabel . $sizeInfoLabel . $alcoholContentLabel;

    array_push($arrDetailData, array(
        'item_code' => $itemCode,
        'item_name' => $itemName,
        'mililiter' => $mililiter,
        'brand_id' => $rsBrandCol['pkey'],
        'type_id' => $rsItemCategoryCol['pkey'],
        'qty_carton' => $qtyCarton,
        'qty_package' => $qtyPackage,
        'alcohol_content' => $alcoholContent,
        'amount' => $amount,
        'hs' => $hs,
        'transaction_type_id' => $rsTransactionTypeCol['pkey'],
        'category' => $category,
        'unit_id' => $rsUnitCol['pkey'],
        'packaging' => $packaging,
        'country_id' => $rsCountryCol['pkey'],
        'country_origin_id' => $rsCountryCol['originalname'],
        'container_number' => $containerNumber,
        'container_size' => $containerSize,
        'container_type' => $containerType,
        'label' => $label,
        'qty' => $qty
    ));

}

//ERROR HANDLE
foreach ($arrBrandName as $row) {
    if (empty($row['original']))
        continue;

    if (!isset($rsBrandCols[$row['id']]))
        array_push($arrErrorMsg, 'Merk : "' . $row['original'] . '" tidak terdaftar');
}

foreach ($arrTypename as $row) {
    if (empty($row['original']))
        continue;

    if (!isset($rsItemCategoryCols[$row['id']]))
        array_push($arrErrorMsg, 'Tipe : "' . $row['original'] . '" tidak terdaftar');
}

foreach ($arrTransactionTypeName as $row) {
    if (empty($row['original']))
        continue;

    if (!isset($rsTransactionTypeCols[$row['id']]))
        array_push($arrErrorMsg, 'Jenis Transaksi : "' . $row['original'] . '" tidak terdaftar');
}

foreach ($arrUnitName as $row) {
    if (empty($row['original']))
        continue;

    if (!isset($rsItemUnitCols[$row['id']]))
        array_push($arrErrorMsg, 'Unit : "' . $row['original'] . '" tidak terdaftar');
}

foreach ($arrCountryName as $row) {
    if (empty($row['original']))
        continue;

    if (!isset($rsCountryCols[$row['id']]))
        array_push($arrErrorMsg, 'Negara : "' . $row['original'] . '" tidak terdaftar');
}


if (!empty($arrErrorMsg)) {
    echo '<table>';
    echo '<tr>';
    echo '<td style="padding:0.1em"><div style="color:red;"> ERROR : </div></td>';
    echo '</tr>';
    foreach ($arrErrorMsg as $row) {
        echo '<tr>';
        echo '<td style="padding:0.1em"><div style="color:red;"> ' . $row . ' </div></td>';
        echo '</tr>';
    }
    echo '</table>';
    die;
}

for ($i = 0; $i < count($arrHeaderData); $i++) {

    $data = $arrHeaderData[$i];

    $date = date('Y-m-d H:i:s', $data['trdate']);
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
    $arrParam['itemDetailCode'] = array();
    $arrParam['itemDetailName'] = array();
    $arrParam['hidDetailBrandKey'] = array();
    $arrParam['hidDetailTypeKey'] = array();
    $arrParam['mililiter'] = array();
    $arrParam['qtyCarton'] = array();
    $arrParam['qtyPackage'] = array();
    $arrParam['alcoholContent'] = array();
    $arrParam['selTransactionType'] = array();
    $arrParam['amount'] = array();
    $arrParam['label'] = array();
    $arrParam['packagingName'] = array();
    $arrParam['hs'] = array();
    $arrParam['category'] = array();
    $arrParam['selUnit'] = array();
    $arrParam['hidDetailCountryKey'] = array();
    $arrParam['containerNumber'] = array();
    $arrParam['containerType'] = array();
    $arrParam['containerSize'] = array();
    $arrParam['qty'] = array();
    $arrParam['countryOfOriginId'] = array();

    foreach ($arrDetailData as $detail) {
        array_push($arrParam['hidDetailKey'], 0);
        array_push($arrParam['itemDetailCode'], $detail['item_code']);
        array_push($arrParam['itemDetailName'], $detail['item_name']);
        array_push($arrParam['mililiter'], $detail['mililiter']);
        array_push($arrParam['hidDetailBrandKey'], $detail['brand_id']);
        array_push($arrParam['hidDetailTypeKey'], $detail['type_id']);
        array_push($arrParam['qtyCarton'], $detail['qty_carton']);
        array_push($arrParam['qtyPackage'], $detail['qty_package']);
        array_push($arrParam['alcoholContent'], $detail['alcohol_content']);
        array_push($arrParam['amount'], $detail['amount']);
        array_push($arrParam['label'], $detail['label']);
        array_push($arrParam['selTransactionType'], $detail['transaction_type_id']);
        array_push($arrParam['hs'], $detail['hs']);
        array_push($arrParam['category'], $detail['category']);
        array_push($arrParam['selUnit'], $detail['unit_id']);
        array_push($arrParam['packagingName'], $detail['packaging']);
        array_push($arrParam['hidDetailCountryKey'], $detail['country_id']);
        array_push($arrParam['countryOfOriginId'], $detail['country_origin_id']);
        array_push($arrParam['containerNumber'], $detail['container_number']);
        array_push($arrParam['containerType'], $detail['container_type']);
        array_push($arrParam['containerSize'], $detail['container_size']);
        array_push($arrParam['qty'], $detail['qty']);
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