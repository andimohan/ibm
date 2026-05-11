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
//$serviceDetail = 'KONTRAK';
$totalDetail = 1;
$detailStatus = 1;

// search data 
$rsWarehouse = $warehouse->searchData($warehouse->tableName . '.name', $warehouseId);
$rsCategory = $truckingServiceOrderCategory->searchDataRow(array($truckingServiceOrderCategory->tableName . '.pkey', $truckingServiceOrderCategory->tableName . '.code', $truckingServiceOrderCategory->tableName . '.name'), ' and ' . $truckingServiceOrderCategory->tableName . '.name = ' . $OBJ->oDbCon->paramString($categoryName));
//$rsItemDetail = $item->searchDataRow(array($item->tableName . '.pkey', $item->tableName . '.code', $item->tableName . '.name'), ' and ' . $item->tableName . '.name = ' . $OBJ->oDbCon->paramString($serviceDetail));


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

for ($row = 2; $row <= $highestRow; ++$row) {
    $customerId = $worksheet->getCellByColumnAndRow(4, $row)->getValue();
    $destination = strtolower($worksheet->getCellByColumnAndRow(5, $row)->getValue());
    $driverName = strtolower($worksheet->getCellByColumnAndRow(7, $row)->getValue());
    $policeNumber = $worksheet->getCellByColumnAndRow(8, $row)->getValue();
    $policeNumber = strtoupper($car->normalizePoliceNumber($policeNumber));

    array_push($arrCustomerName, $customerId);
    array_push($arrDriverName, $driverName);
    array_push($arrPoliceNumber, $policeNumber);
    array_push($arrDestination, $destination);
}


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

    $arrCriteria = array(); 
    $rsCargoDetail = array() ;
    $trdate = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
    $trdate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($trdate);
    $trdate = $trdate->getTimestamp();

    $customerId = $worksheet->getCellByColumnAndRow(4, $row)->getValue();
    $destination = $worksheet->getCellByColumnAndRow(5, $row)->getValue();
    $type = $worksheet->getCellByColumnAndRow(6, $row)->getValue();
    $driverName = $worksheet->getCellByColumnAndRow(7, $row)->getValue();
    $driverName = strtolower($driverName);
    $destination = strtolower($destination);

    $policeNumber = $worksheet->getCellByColumnAndRow(8, $row)->getValue();
    $policeNumber = strtoupper($car->normalizePoliceNumber($policeNumber));

    $qty = $worksheet->getCellByColumnAndRow(9, $row)->getValue(); //MUAT

    $rsCustomer = $rsCustomerCol[$customerId]; // $customer->searchData($customer->tableName . '.name', $customerId);

    $rsDriver = $rsDriverCol[$driverName];
    $rsCar = $rsCarCol[$policeNumber];
    $shipmentValue = $worksheet->getCellByColumnAndRow($shipmentColumnIndex, $row)->getValue();

    $destinationkey = $rsDestinationCol[$destination]['pkey'];

    $date = date('Y-m-d', $trdate);

    if (!empty($shipmentValue) && !empty($destinationkey) && !empty($policeNumber)) { 
        array_push ($arrCriteria, $truckingServiceWorkOrder->tableName.'.stuffingdatetime = '. $OBJ->oDbCon->paramString($date)); 
        array_push ($arrCriteria, $truckingServiceWorkOrder->tableName.'.carkey = '. $OBJ->oDbCon->paramString($rsCar['pkey'])); 
        array_push ($arrCriteria, $truckingServiceWorkOrder->tableWorkOrderCargoDetail.'.destinationkey = '.  $OBJ->oDbCon->paramString($destinationkey)); 
        array_push ($arrCriteria, $truckingServiceWorkOrder->tableWorkOrderCargoDetail.'.workorder = '.  $OBJ->oDbCon->paramString($shipmentValue)); 
        $criteria = implode(' and ', $arrCriteria);  
        $criteria = (!empty($criteria)) ? ' and ' . $criteria : ''; 
        $rsCargoDetail = $truckingServiceWorkOrder->getDataForImport($criteria);
        
        $rsCargoDetailCol = array_column($rsCargoDetail, null, 'costkey');
    }


    array_push($arrExcelData, array(
        'trdate' => $trdate,
        'warehouse_name' => $rsWarehouse[0]['name'],
        'warehouse_id' => $rsWarehouse[0]['pkey'],
        'warehouse_name_excel' => $warehouseId,
        'customer_id' => $rsCustomer['pkey'],
        'customer_name' => $customerId,
        'destination' => $destination,
        'type' => $type,
        'cargo_id' => $arrCargoType[strtolower($cargoType)],
        'cargo_type' => strtolower($cargoType),
        'category_name' => $categoryName,
        'category_id' => $rsCategory[0]['pkey'],
        'driver_id' => $rsDriver['pkey'],
        'driver_name' => $driverName,
        'car_id' => $rsCar['pkey'],
        'police_number' => $policeNumber,
        'qty' => $qty,
        'status' => $status
    ));

    //selling cost sales order
    $totalSelling = 0;
    $WODetailKey = '';
    
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
                $rsCargo = $rsCargoDetailCol[$rsItem['pkey']];
                $OBJ->oDbCon->startTrans();
                $totalSelling += $totalCost ;
                if (!empty($rsCargo)) {
                    // $WODetailKey = $rsCargo['workorderdetailkey'];
                   
                    $sql = ' update '.$truckingServiceWorkOrder->tableWorkOrderCostCargoDetail.' set sellingprice = '.$OBJ->oDbCon->paramString($sellingValue).'
                             where '.$truckingServiceWorkOrder->tableWorkOrderCostCargoDetail.'.pkey = '.$OBJ->oDbCon->paramString($rsCargo['workordercostcargokey']);
                    $OBJ->oDbCon->execute($sql);
                    
                    
                } else if (empty($rsCargo) && !empty($rsCargoDetail)) {
                    $sql = 'insert into '.$truckingServiceWorkOrder->tableWorkOrderCostCargoDetail.' (refkey,refheaderkey ,costkey, price, sellingprice, ismultipliedqty) 
                            values  ('. $OBJ->oDbCon->paramString($rsCargoDetail[0]['workorderdetailkey']).',
                                     '. $OBJ->oDbCon->paramString($rsCargoDetail[0]['woheaderkey']).',
                                     '. $OBJ->oDbCon->paramString($rsItem['pkey']).',
                                     0,
                                     '. $OBJ->oDbCon->paramString($sellingValue).',
                                     '. $OBJ->oDbCon->paramString($rsItem['ismultipliedbyqty']).'
                                     ) ';
                    $OBJ->oDbCon->execute($sql);  
                }
                
                $OBJ->oDbCon->endTrans();
            }

        }
    }
    if (!empty($rsCargoDetail) && $totalSelling > 0) {
        if ($totalSelling > 0) {
            $OBJ->oDbCon->startTrans();
            $sql = 'update 
                            '.$truckingServiceWorkOrder->tableWorkOrderCargoDetail.'
                            set sellingamount = ' . $truckingServiceWorkOrder->oDbCon->paramString($totalSelling) .' 
                            where pkey = ' . $truckingServiceWorkOrder->oDbCon->paramString($rsCargoDetail[0]['workorderdetailkey']);
            $OBJ->oDbCon->execute($sql);
            $OBJ->oDbCon->endTrans();
            echo '<span style="color:green;margin:4px">Import data berhasil.  <br> - ' . $rsCargoDetail[0]['wocode'] . ' - '. $rsCargoDetail[0]['destinationlocation'].'<br></span>';
        }
    } else {
        echo '<span style="margin:4px;color:red">- data baris ke -'.$row .', '. $OBJ->errorMsg[213] . '</span><br>';
    }

}





?>
