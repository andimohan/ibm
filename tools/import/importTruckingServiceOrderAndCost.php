<?php
require_once '../../_config.php';
require_once '_include.php';
require_once 'function-v2.php';

require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/TruckingServiceOrder.class.php';
require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/Warehouse.class.php';
require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/Customer.class.php';
require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/TruckingServiceOrderCategory.class.php';
require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/Item.class.php';
require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/Car.class.php';
require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/Employee.class.php';
require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/TruckingServiceWorkOrder.class.php';
require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/Location.class.php';

$OBJ = new TruckingServiceOrder();
$truckingServiceOrderCategory = new TruckingServiceOrderCategory();
$truckingServiceWorkOrder = new TruckingServiceWorkOrder();
$item = new Item();
$customer = new Customer();
$warehouse = new Warehouse();
$employee = new Employee();
$location = new Location();
$car = new Car();
$MODULE_NAME = 'truckingServiceOrder';
$TITLE = $OBJ->lang['jobOrder'];
$AJAX_FILE = 'ajax-api-trucking-service-order';

// ===================== COMPILING DATA
$arrDisplayData = array();
$dateMap = array();
$policeNumberMap = array();

$code = '[auto code]';
$currCode = '';
$indexCtr = 0;

//Define default value
$warehouseId = 'HO';
$cargoType = 'Dry';
$categoryName = 'KONTRAK';

$status = 2;
$qtyDetail = 1;
$arrServiceDetail = array('kontrak');
$totalDetail = 1;
$detailStatus = 1;


// search data 
$rsWarehouse = $warehouse->searchData($warehouse->tableName . '.name', $warehouseId);

//get cargo
$arrTempCargo = $OBJ->getCargoType();
$arrTempCargo = array_column($arrTempCargo, 'pkey', 'name');
$arrCargoType = array();
foreach ($arrTempCargo as $key => $row) {
    $arrCargoType[strtolower($key)] = $row;
}

//ambil semua nilai header / label dari excel
$headerCol = [];
for ($col = 0; $col <= $highestColumnIndex; ++$col) {
    $header = $worksheet->getCellByColumnAndRow($col, 1)->getValue();
    $header = strtolower(str_replace(' ', '', $header));
    array_push($headerCol, $header);
}

//ambil nilai header utk cost jual di SO
$sellingCol = array_filter($headerCol, function ($value) {
    return stripos($value, 'jual') !== false;
});

//get all item is drop point
$rsDataItem = $item->searchDataRow(array(
    $item->tableName . '.pkey',
    $item->tableName . '.code',
    $item->tableName . '.name',
    $item->tableName . '.isdroppointdetailprice',
    $item->tableName . '.ismultipliedbyqty',
), ' and ' . $item->tableName . '.statuskey = 1 and ' . $item->tableName . '.servicecost = 1');


//Get Cost Col
$costsCols = [];
$workOrderCol = [];
$workOrderName = 'shipment';
foreach ($rsDataItem as $itemData) {
    $itemName = strtolower(str_replace(' ', '', $itemData['name']));

    if (in_array($itemName, $headerCol)) {
        $index = array_search($itemName, $headerCol);
        $costCols[$itemName] = $index;
    }

    if (in_array($workOrderName, $headerCol)) {
        $index = array_search($workOrderName, $headerCol);
        $workOrderCol[$index] = $workOrderName;
    }

}
$costCol = [];
foreach ($costCols as $key => $value) {
    $costCol[$value] = $key;
}

//search item
$whereClause = array();
foreach ($sellingCol as $keyword) {
    $keyword = str_replace('jual', '', $keyword);
    $whereClause[] = 'REPLACE(LOWER(' . $item->tableName . '.name), " ", "") LIKE "%' . strtolower(str_replace(' ', '', $keyword)) . '%"';
}
$whereClause = implode(' OR ', $whereClause);
$rsItem = $item->searchDataRow(array($item->tableName . '.pkey', $item->tableName . '.code', $item->tableName . '.name', $item->tableName . '.isdroppointdetailprice', $item->tableName . '.ismultipliedbyqty', ), ' and (' . $whereClause . ') ');


//SHORTING
$sellingCols = array_map(function ($value) {
    return str_replace('JUAL ', '', $value);
}, $sellingCol);
$sellingIndex = array_flip($sellingCols);
usort($rsItem, function ($a, $b) use ($sellingIndex) {
    return $sellingIndex[$a['name']] <=> $sellingIndex[$b['name']];
});
$rsItemAssoc = array_column(array_map(function ($items) {
    $items['name'] = strtolower(str_replace(' ', '', $items['name']));
    return $items;
}, $rsItem), null, 'name');

$arrExcelData = array();
$arrSellingData = array();
$arrWorkOrderData = array();
$arrWorkOrderCargo = array();
$arrWorkOrderCargoCost = array();


$arrErrorMsg = array();

// ambil data sopir dan mobil dulu

$arrCustomerName = array();
$arrDriverName = array();
$arrPoliceNumber = array();
$arrDestination = array();
$arrCategoryName = array();

for ($row = 2; $row <= $highestRow; ++$row) {

    $categoryId = strtolower($worksheet->getCellByColumnAndRow(3, $row)->getValue());
    $categoryId = str_replace(' ', '', $categoryId);
    $customerId = $worksheet->getCellByColumnAndRow(4, $row)->getValue();

    $destination = strtolower($worksheet->getCellByColumnAndRow(5, $row)->getValue());
    $driverName = strtolower($worksheet->getCellByColumnAndRow(7, $row)->getValue());
    $policeNumber = $worksheet->getCellByColumnAndRow(8, $row)->getValue();
    $policeNumber = strtoupper($car->normalizePoliceNumber($policeNumber));

    array_push($arrCustomerName, $customerId);
    array_push($arrDriverName, $driverName);
    array_push($arrPoliceNumber, $policeNumber);
    array_push($arrDestination, $destination);
    array_push($arrCategoryName, $categoryId);
}


$rsCategoryJobOrderCol = $truckingServiceOrderCategory->searchDataRow(array($truckingServiceOrderCategory->tableName . '.pkey', $truckingServiceOrderCategory->tableName . '.code', 'replace(lower(' . $truckingServiceOrderCategory->tableName . '.name)," ","") as name'), ' and ' . $truckingServiceOrderCategory->tableName . '.statuskey = 1 and ' . $truckingServiceOrderCategory->tableName . '.name in (' . $OBJ->oDbCon->paramString($arrCategoryName, ',') . ') ');

$arrCategoryNameCol = array_column($rsCategoryJobOrderCol, 'name');
$rsCategoryJobOrderCol = array_column($rsCategoryJobOrderCol, null, 'name');


$rsCustomerCol = $customer->searchData('', '', true, ' and ' . $customer->tableName . '.statuskey = 2  and ' . $customer->tableName . '.name in (' . $OBJ->oDbCon->paramString($arrCustomerName, ',') . ') ');
$rsCustomerCol = array_column($rsCustomerCol, null, 'name');

$rsDriverCol = $employee->searchDataRow(
    array($employee->tableName . '.pkey', 'lower(' . $employee->tableName . '.name) as name', $employee->tableName . '.code', $employee->tableName . '.isdriver'),
    ' and ' . $employee->tableName . '.name in (' . $OBJ->oDbCon->paramString($arrDriverName, ',') . ')   
									   and ' . $employee->tableName . '.isdriver = 1 and ' . $employee->tableName . '.statuskey = 2 '
);
$arrDriverNameCol = array_column($rsDriverCol, 'name');
$rsDriverCol = array_column($rsDriverCol, null, 'name');

$rsDestinationCol = $location->searchDataRow(
    array($location->tableName . '.pkey', 'lower(' . $location->tableName . '.name) as name', $location->tableName . '.code'),
    ' and ' . $location->tableName . '.name in (' . $OBJ->oDbCon->paramString($arrDestination, ',') . ') and '. $location->tableName . '.statuskey = 1 '
);

$OBJ->htmlEntityDecodeArray($rsDestinationCol, array('name'));

$rsDestinationNameCol = array_column($rsDestinationCol, 'name');
$rsDestinationCol = array_column($rsDestinationCol, null, 'name');

$rsCarCol = $car->searchDataRow(
    array($car->tableName . '.pkey', $car->tableName . '.code', 'upper(' . $car->tableName . '.policenumber) as policenumber'),
    ' and ' . $car->tableName . '.policenumber in (' . $OBJ->oDbCon->paramString($arrPoliceNumber, ',') . ')'
);

$arrPoliceNumberCol = array_column($rsCarCol, 'policenumber');
$rsCarCol = array_column($rsCarCol, null, 'policenumber');


// cari data mobil, sopir yang tdk terdaftar di database
foreach ($arrDriverName as $row) {
    if (empty($row))
        continue;
    if (!in_array($row, $arrDriverNameCol))
        array_push($arrErrorMsg, $row . ' tidak terdaftar');
}

// cari data lokasi yang tdk terdaftar di database

foreach ($arrDestination as $row) {
    if (empty($row))
        continue;
    if (!in_array($row, $rsDestinationNameCol))
        array_push($arrErrorMsg, $row . ' tidak terdaftar');
}

// cari index colomn shipment by name
$shipmentColumnIndex = null;
foreach ($workOrderCol as $colIndex => $header) {
    if (stripos($header, 'shipment') !== false) {
        $shipmentColumnIndex = $colIndex;
        break;
    }
}


//caridata category tidak terdaftar di database
foreach ($arrCategoryName as $row) {
    if (empty($row))
        continue;

    if (!in_array($row, $arrCategoryNameCol))
        array_push($arrErrorMsg, $row . ' tidak terdaftar');
}


foreach ($arrPoliceNumber as $row) {
    if (empty($row))
        continue;

    if (!in_array($row, $arrPoliceNumberCol))
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


for ($row = 2; $row <= $highestRow; ++$row) {

    $trdate = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
    $trdate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($trdate);
    $trdate = $trdate->getTimestamp();
    $categoryId = strtolower($worksheet->getCellByColumnAndRow(3, $row)->getValue());
    $categoryId = str_replace(' ', '', $categoryId);


    $customerId = $worksheet->getCellByColumnAndRow(4, $row)->getValue();
    $destination = $worksheet->getCellByColumnAndRow(5, $row)->getValue();
    $type = $worksheet->getCellByColumnAndRow(6, $row)->getValue();
    $driverName = $worksheet->getCellByColumnAndRow(7, $row)->getValue();
    $driverName = strtolower($driverName);

    $policeNumber = $worksheet->getCellByColumnAndRow(8, $row)->getValue();
    $policeNumber = strtoupper($car->normalizePoliceNumber($policeNumber));

    $qty = $worksheet->getCellByColumnAndRow(9, $row)->getValue(); //MUAT
    $load = $worksheet->getCellByColumnAndRow(9, $row)->getValue();
   // $total = $worksheet->getCellByColumnAndRow(20, $row)->getCalculatedValue();

    $rsCustomer = $rsCustomerCol[$customerId]; // $customer->searchData($customer->tableName . '.name', $customerId);

    $rsDriver = $rsDriverCol[$driverName];
    $rsCar = $rsCarCol[$policeNumber];
    $rsCategory = $rsCategoryJobOrderCol[$categoryId];



    array_push($arrExcelData, array(
        'indexrow' => $row,
        'trdate' => $trdate,
        'warehouse_name' => $rsWarehouse[0]['name'],
        'warehouse_id' => $rsWarehouse[0]['pkey'],
        'warehouse_name_excel' => $warehouseId,
        'customer_id' => $rsCustomer['pkey'],
        'customer_name' => $customerId,
        'destination' => $destination,
        'type' => strtolower($type),
        'cargo_id' => $arrCargoType[strtolower($cargoType)],
        'cargo_type' => strtolower($cargoType),
        'category_name' => $categoryId,
        'category_id' => $rsCategory['pkey'],
        'driver_id' => $rsDriver['pkey'],
        'driver_name' => $driverName,
        'car_id' => $rsCar['pkey'],
        'police_number' => $policeNumber,
        'qty' => $qty,
      //  'total' => $total,
        'status' => $status
    ));

    //selling cost sales order
    foreach ($sellingCol as $colIndex => $header) {

        $sellingValue = $worksheet->getCellByColumnAndRow($colIndex, $row)->getValue();
        $costName = $header;
        $costName = str_replace('jual', '', $costName);
        $costName = trim($costName);
        $costNameLower = strtolower($costName);

        $shipmentValue = $worksheet->getCellByColumnAndRow($shipmentColumnIndex, $row)->getValue();

        if ($rsItemAssoc[$costNameLower]) {
            $rsItem = $rsItemAssoc[$costNameLower];
            if ($rsItem['name'] == $costNameLower && !empty($sellingValue)) {
                $qtyCost = 1;
                if ($rsItem['ismultipliedbyqty'] == 1) {
                    $qtyCost = $qty;
                }
                $totalCost = $qtyCost * $sellingValue;
                array_push($arrSellingData, array(
                    'indexrow' => $row,
                    'indexkey' => $trdate . '-' . $customerId . '-' . $destination . '-' . $policeNumber . '-' . $rsItem['name'].'-'. $shipmentValue,//indexkey di tambah shipment, karena ada kemingkinan destinantion sama
                    'trdate' => $trdate,
                    'customer_id' => $rsCustomer['pkey'],
                    'customer_name' => $customerId,
                    'car_id' => $rsCar['pkey'],
                    'police_number' => $policeNumber,
                    'destination' => $destination,
                    'multiplied_qty' => $rsItem['ismultipliedbyqty'],
                    'is_droppoint_price' => $rsItem['isdroppointdetailprice'],
                    'selling_cost_name' => $rsItem['name'],
                    'selling_cost_id' => $rsItem['pkey'],
                    'selling_qty' => $qtyCost,
                    'selling_price_in_unit' => $sellingValue,
                    'selling_total_cost' => $totalCost
                ));
            }
        }
    }

    //SPK
    array_push($arrWorkOrderData, array(
        'indexrow' => $row,
        'customer_id' => $rsCustomer['pkey'],
        'customer_name' => $customerId,
        'warehouse_id' => $rsWarehouse[0]['pkey'],
        'trdate' => $trdate,
        'destination' => $destination,
        'qty' => $qty,
        'driver_id' => $rsDriver['pkey'],
        'driver_name' => $driverName,
        'police_number' => $policeNumber,
        'car_id' => $rsCar['pkey'],
        'category_name' => strtolower($categoryId),
        'category_id' => $rsCategory['pkey']
    ));

    foreach ($workOrderCol as $colIndex => $header) {
        $valueWONumber = $worksheet->getCellByColumnAndRow($colIndex, $row)->getValue();

        //destination list
        array_push($arrWorkOrderCargo, array(
            'indexrow' => $row,
            'trdate' => $trdate,
            'customer_id' => $rsCustomer['pkey'],
            'customer_name' => $customerId,
            'destination' => $destination,
            'work_order' => $valueWONumber,
            'qty' => $qty,
            'police_number' => $policeNumber,
            'car_id' => $rsCar['pkey'],
            'category_name' => strtolower($categoryId),
            'category_id' => $rsCategory['pkey']
        ));

    }

    //cost list
    foreach ($costCol as $colIndex => $header) {
        //$costValue = $worksheet->getCellByColumnAndRow($colIndex, $row)->getValue();
        $costValue = $worksheet->getCellByColumnAndRow($colIndex, $row)->getCalculatedValue();
        $costName = $header;
        $costNameLower = strtolower(str_replace(' ', '', $costName));

        $shipmentValue = $worksheet->getCellByColumnAndRow($shipmentColumnIndex, $row)->getValue();

        $rsItemCost = $item->searchDataRow(array(
            $item->tableName . '.pkey',
            $item->tableName . '.code',
            $item->tableName . '.name',
            $item->tableName . '.isdroppointdetailprice',
            $item->tableName . '.ismultipliedbyqty',
        ), ' and REPLACE(LOWER(' . $item->tableName . '.name), " ", "") = (' . $OBJ->oDbCon->paramString($costNameLower) . ') ');

        if (!empty($rsItemCost)) {
            array_push($arrWorkOrderCargoCost, array(
                'indexkey' => $trdate . '-' . $customerId . '-' . $destination . '-' . $policeNumber . '-' . $costName.'-'.$shipmentValue,
                'trdate' => $trdate,
                'customer_id' => $rsCustomer['pkey'],
                'customer_name' => $rsCustomer['name'],
                'car_id' => $rsCar['pkey'],
                'police_number' => $policeNumber,
                'destination' => $destination,
                'multiplied_qty' => $rsItemCost[0]['ismultipliedbyqty'],
                'is_droppoint_price' => $rsItemCost[0]['isdroppointdetailprice'],
                'price_in_unit' => (empty($costValue) ? 0 : $costValue),
                'cost_name' => $costName,
                'cost_id' => $rsItemCost[0]['pkey'],
                'category_name' => strtolower($categoryId),
                'category_id' => $rsCategory['pkey'],
                'selling_cost_id' => '',
                'selling_cost_name' => '',
                'selling_price_in_unit' => 0,
                'selling_total_cost' => 0,
            ));
        }
    }
}


$arrType = array_column($arrExcelData,'type');
array_push($arrType,'kontrak');
$arrType = array_unique($arrType);

//$item->setLog($arrType,true);

$rsItemDetail = $item->searchDataRow(array($item->tableName . '.pkey', $item->tableName . '.code', 'lower('.$item->tableName . '.name) as name' ),
                                            ' and ' . $item->tableName . '.name in ('.$item->oDbCon->paramString($arrType,',').')'
                                    );
$rsItemDetail = array_column($rsItemDetail,null,'name');

//$item->setLog($rsItemDetail,true);



//merge data selling cost
$sellingDataLookup = [];
foreach ($arrSellingData as $selling) {
    $sellingDataLookup[$selling['indexkey']] = $selling;
}
foreach ($arrWorkOrderCargoCost as &$cost) {
    $key = $cost['indexkey'];
    if (isset($sellingDataLookup[$key])) {
        $selling = $sellingDataLookup[$key];

        $cost['selling_index_row'] = $selling['indexrow'];
        $cost['selling_cost_name'] = $selling['selling_cost_name'];
        $cost['selling_cost_id'] = $selling['selling_cost_id'];
        $cost['selling_price_in_unit'] = $selling['selling_price_in_unit'];
        $cost['selling_total_cost'] = $selling['selling_total_cost'];
    }

}

$groupDateCustomer = array();
for ($i = 0; $i < count($arrExcelData); $i++) {

    $data = $arrExcelData[$i];
    $date = date('Y-m-d H:i:s', $data['trdate']);

   // $dateCustomerKey = $data['trdate'] . '-' . $data['customer_name'];
    $categoryName = strtolower($data['category_name']);

    if ($categoryName == 'oncall') {
        $dateCustomerKey = $data['trdate'] . '-' . $data['customer_name'] . '-' . $data['indexrow'];
    } else {
        $dateCustomerKey = $data['trdate'] . '-' . $data['customer_name'];
    }



    if (!isset($groupDateCustomer[$dateCustomerKey])) {
        $arrParam = array();
        $arrParam['code'] = 'xxxxx';
        $arrParam['trDate'] = $OBJ->formatDBDate($date, 'd / m / Y');
        $arrParam['selWarehouseKey'] = $data['warehouse_id'];
        $arrParam['hidCustomerKey'] = $data['customer_id'];
        $arrParam['hidCargoType'] = $data['cargo_id'];
        $arrParam['hidCategoryKey'] = $data['category_id'];
        $arrParam['selStatus'] = $data['status'];

        $arrParam['hidDetailKey'] = array();
        $arrParam['hidItemKey'] = array();
        $arrParam['qty'] = array();
        $arrParam['trShipmentDate'] = array();
        $arrParam['price'] = array();

//        $item->setLog($data['type'],true);
//        $item->setLog($rsItemDetail[$data['type']]['pkey'],true);
        
        $itemkey = ($categoryName == 'kontrak') ? $rsItemDetail['kontrak']['pkey'] : $rsItemDetail[$data['type']]['pkey'];
        array_push($arrParam['hidDetailKey'], 0);
        array_push($arrParam['hidItemKey'], $itemkey);
        array_push($arrParam['qty'], 1);
        array_push($arrParam['trShipmentDate'], $OBJ->formatDBDate($date, 'd / m / Y H:i:s'));
        array_push($arrParam['price'], 0);
 

        $groupDateCustomer[$dateCustomerKey] = true;

        //Save Sales Order
        $result = $OBJ->addData($arrParam);

        if ($result[0]['valid']) {

            //            $arrNotFoundDriverAndCar = array();
            $groupPoliceNumber = array(); // reset per SPK

            $rsSO = $result[0]['data'];

            // change status
            $OBJ->changeStatus($rsSO['pkey'], 2, '', false, true);

            $salesOrderKey = $rsSO['pkey'];
            $trDate = $rsSO['trdate'];

            $cargotypekey = $rsSO['cargotypekey'];
            $categorykey = $rsSO['categorykey'];

            $rsSODetail = $OBJ->getDetailWithRelatedInformation($salesOrderKey);

            //ambil index pertama, karena dari import detail JO hanya 1
            $sodetailkey = $rsSODetail[0]['pkey'];
            $itemkey = $rsSODetail[0]['itemkey'];


            foreach ($arrWorkOrderData as $key => $workOrder) {
 
                if ($workOrder['category_name'] == 'oncall') {
                    $conditionalStatement = ($data['customer_name'] == $workOrder['customer_name']) && ($data['trdate'] == $workOrder['trdate']) && ($data['category_name'] == $workOrder['category_name']) && ($data['indexrow'] == $workOrder['indexrow']);
//                    $conditionalStatement = true; // karena satu baris selalu jadi 1 JO
                    $carAndNumber = $workOrder['police_number'] . '-' . $workOrder['driver_name'] . '-' . $workOrder['indexrow'];
                } else {
                    $conditionalStatement = ($data['customer_name'] == $workOrder['customer_name']) && ($data['trdate'] == $workOrder['trdate']) && ($data['category_name'] == $workOrder['category_name']);
                    $carAndNumber = $workOrder['police_number'] . '-' . $workOrder['driver_name'];
                }

                //if ($data['customer_name'] == $workOrder['customer_name'] && $data['trdate'] == $workOrder['trdate']) {
                if ($conditionalStatement) {
                    if (!isset($groupPoliceNumber[$carAndNumber])) {

                        $groupPoliceNumber[$carAndNumber] = true;
 
                        $arrParamWO = array();
                        $arrParamWO['code'] = 'xxxxxx';
                        $arrParamWO['hidSOKey'] = $salesOrderKey;
                        $arrParamWO['hidSODetailKey'] = $sodetailkey;
                        $arrParamWO['hidItemKey'] = $itemkey;
                        $arrParamWO['trDate'] = date('d / m / Y');
                        $arrParamWO['trDateStuffing'] = $OBJ->formatDBDate($trDate, 'd / m / Y H:i');
                        $arrParamWO['hidCargoTypeKey'] = $cargotypekey;
                        $arrParamWO['hidCategoryKey'] = $categorykey;
                        $arrParamWO['selJobType'] = 8001; // tembak mati dulu
                        $arrParamWO['selWarehouseKey'] = $workOrder['warehouse_id'];
                        $arrParamWO['stuffingAddress'] = '';
                        $arrParamWO['hidDriverKey'] = $workOrder['driver_id'];
                        $arrParamWO['hidCarKey'] = $workOrder['car_id'];
                        $arrParamWO['hidCustomerKey'] = $workOrder['customer_id'];
                        $arrParamWO['selStatus'] = 1;
                        $arrParamWO['islinked'] = true;
                        $arrParamWO['createdBy'] = 0;
                        $arrParamWO['chkIsOutsource'] = 0;

                        //$arrParamWO['_mnv'] = true;


                        $arrParamWO['hidDetailKey'] = array();
                        $arrParamWO['qtyCostDetail'] = array();
                        $arrParamWO['hidCostKey'] = array();
                        $arrParamWO['requestAmount'] = array();
                        $arrParamWO['amount'] = array();
                        $arrParamWO['isReimburse'] = array();
                        $arrParamWO['hidCargoDetailKey'] = array();
                        $arrParamWO['destinationCargo'] = array();
                        $arrParamWO['workOrderCargo'] = array();
                        $arrParamWO['qtyDetailCargo'] = array();
                        $arrParamWO['selUnitCargo'] = array();
                        $arrParamWO['amountCargo'] = array();
                        $arrParamWO['sellingAmountCargo'] = array();
                        $arrParamWO['hidDestinationDetailKey'] = array();
                        
                        for ($c = 0; $c < count($arrWorkOrderCargo); $c++) {

                     
                            if($arrWorkOrderCargo[$c]['category_name'] == 'oncall') {
                                $conditionalStatementWOCargo = ($arrWorkOrderCargo[$c]['police_number'] == $workOrder['police_number']) &&
                                                                ($arrWorkOrderCargo[$c]['customer_name'] == $workOrder['customer_name']) &&
                                                                ($arrWorkOrderCargo[$c]['trdate'] == $workOrder['trdate']) && 
                                                                ($arrWorkOrderCargo[$c]['indexrow'] == $workOrder['indexrow']);
                            } else {
                                $conditionalStatementWOCargo = ($arrWorkOrderCargo[$c]['police_number'] == $workOrder['police_number']) &&
                                                                ($arrWorkOrderCargo[$c]['customer_name'] == $workOrder['customer_name']) &&
                                                                ($arrWorkOrderCargo[$c]['trdate'] == $workOrder['trdate']);
                            }

                            if ($conditionalStatementWOCargo) {

                                $qty = (empty($arrWorkOrderCargo[$c]['qty']) ? 0 : $arrWorkOrderCargo[$c]['qty']);
                                $destination = strtolower($arrWorkOrderCargo[$c]['destination']);
                                $destinationKey = $rsDestinationCol[$destination]['pkey'];

                                array_push($arrParamWO['hidCargoDetailKey'], 0);
                                array_push($arrParamWO['destinationCargo'], $arrWorkOrderCargo[$c]['destination']);
                                array_push($arrParamWO['workOrderCargo'], $arrWorkOrderCargo[$c]['work_order']);
                                array_push($arrParamWO['qtyDetailCargo'], $qty);
                                array_push($arrParamWO['hidDestinationDetailKey'], $destinationKey);
                                array_push($arrParamWO['selUnitCargo'], 1);
                                array_push($arrParamWO['amountCargo'], 1);
                                array_push($arrParamWO['sellingAmountCargo'], 1);

                            }
                        }

                        $arrParamWO['hidCostDetailCargoKey'] = array();
                        $arrParamWO['hidCargoCostKey'] = array();

                        //inisialisasi filed dengan key cost.
                        for ($s = 0; $s < count($arrWorkOrderCargoCost); $s++) {
                            $arrParamWO['costCargoDetail_' . $arrWorkOrderCargoCost[$s]['cost_id']] = array();
                            // $arrParamWO['sellingCostCargoDetail_' . $arrWorkOrderCargoCost[$s]['cost_id']] = array();
                            $arrParamWO['hidIsMultipliedQty_' . $arrWorkOrderCargoCost[$s]['cost_id']] = array();
                        }


                        for ($l = 0; $l < count($arrWorkOrderCargoCost); $l++) {

                            if ($arrWorkOrderCargoCost[$l]['category_name'] == 'oncall') {
                                $conditionalStatementWOCargoCost = ($arrWorkOrderCargoCost[$l]['police_number'] == $workOrder['police_number']) &&
                                    ($arrWorkOrderCargoCost[$l]['customer_name'] == $workOrder['customer_name']) &&
                                    ($arrWorkOrderCargoCost[$l]['trdate'] == $workOrder['trdate']) &&
                                    (!empty($arrWorkOrderCargoCost[$l]['destination'])) ;
                                    // && ($arrWorkOrderCargoCost[$l]['selling_index_row'] == $workOrder['indexrow']);
                            } else {
                                $conditionalStatementWOCargoCost = ($arrWorkOrderCargoCost[$l]['police_number'] == $workOrder['police_number']) &&
                                    ($arrWorkOrderCargoCost[$l]['customer_name'] == $workOrder['customer_name']) &&
                                    ($arrWorkOrderCargoCost[$l]['trdate'] == $workOrder['trdate']) &&
                                    (!empty($arrWorkOrderCargoCost[$l]['destination']));
                            }
                            
                            if ($conditionalStatementWOCargoCost) {
                                if (($arrWorkOrderCargoCost[$l]['is_droppoint_price'] == 1)) {
                                    array_push($arrParamWO['hidCostDetailCargoKey'], 0);
                                    array_push($arrParamWO['hidCargoCostKey'], $arrWorkOrderCargoCost[$l]['cost_id']);
                                    array_push($arrParamWO['costCargoDetail_' . $arrWorkOrderCargoCost[$l]['cost_id']], $arrWorkOrderCargoCost[$l]['price_in_unit']);
                                    // array_push($arrParamWO['sellingCostCargoDetail_' . $arrWorkOrderCargoCost[$l]['cost_id']], $arrWorkOrderCargoCost[$l]['selling_price_in_unit']);
                                    array_push($arrParamWO['hidIsMultipliedQty_' . $arrWorkOrderCargoCost[$l]['cost_id']], $arrWorkOrderCargoCost[$l]['multiplied_qty']);
                                } else {
                                    if (!empty($arrWorkOrderCargoCost[$l]['price_in_unit'])) {
                                        array_push($arrParamWO['hidDetailKey'], 0);
                                        array_push($arrParamWO['qtyCostDetail'], $qty);
                                        array_push($arrParamWO['hidCostKey'], $arrWorkOrderCargoCost[$l]['cost_id']);
                                        array_push($arrParamWO['requestAmount'], $arrWorkOrderCargoCost[$l]['price_in_unit']);
                                    }
                                }
                            }
                        }



                        $rsWO = $truckingServiceWorkOrder->addData($arrParamWO);


                        if (!$rsWO[0]['valid']) {
                            echo '<span style="font-weight:bold;color:red;font-size:12px;margin:4px">ERROR SPK:</span><br>';
                            foreach ($rsWO as $rswo) {
                                echo '<span style="margin:4px;color:red">- ' . $rswo['message'] . '</span><br>';
                            }
                        }
                    }

                }
            }

            echo '<span style="color:green;margin:4px">Import data berhasil.  <br> - ' . $result[0]['data']['code'] . '<br></span>';



        } else {
            echo '<span style="font-weight:bold;color:red;font-size:12px;margin:4px">ERROR :</span><br>';
            foreach ($result as $rs) {
                echo '<span style="margin:4px;color:red">- ' . $rs['message'] . '</span><br>';
            }
        }
    }

}


?>
