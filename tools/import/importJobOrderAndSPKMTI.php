<?php

require_once '../../_config.php';
require_once '_include.php';
require_once 'function-v2.php';


require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/TruckingServiceOrder.class.php';
require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/TruckingServiceWorkOrder.class.php';
require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/Customer.class.php';
require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/Location.class.php';
require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/Car.class.php';
require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/Employee.class.php';
require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/Item.class.php';
require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/Service.class.php';
require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/TruckingServiceOrderCategory.class.php';
require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/Warehouse.class.php';
require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/Supplier.class.php';

$truckingServiceOrder = new TruckingServiceOrder();
$truckingServiceOrderCategory = new TruckingServiceOrderCategory();
$truckingServiceWorkOrder = new TruckingServiceWorkOrder();
$customer = new Customer();
$location = new Location();
$car = new Car();
$employee = new Employee();
$item = new Item();
$service = new Service();
$truckingCost = new Service(TRUCKING_SERVICE, 1);
$warehouse = new Warehouse();
$supplier = new Supplier();

$obj = $truckingServiceOrder;

$MODULE_NAME = 'truckingServiceOrder';
$TITLE = $obj->lang['jobOrder'];


$columnStart = 24;
$columnMultiDropCost = 22;
$columnMultiDropSelling = 23;

$code = '[auto code]';
$categoryName = 'KONTRAK';
$warehouseName = 'HO';
$cargoType = 'Dry';
$status = 2;

$arrCustomerName = array();
$arrDriverName = array();
$arrPoliceNumber = array();
$arrLocationName = array();
$arrServiceName = array();
$arrJobCategory = array();
$arrSupplierName = array();
$arrPlannerName = array();

$arrErrorMsg = array();


function removeSpaceAndLowerCase($value) {
    $result = strtolower($value);
    $result = str_replace(' ', '', $result);
    return $result;
}

function getTruckingCost($criteria = '')
{
    global $truckingCost;

    $arrColumns = [
       $truckingCost->tableName . '.pkey',
       $truckingCost->tableName . '.code',
        'lower(replace(' .$truckingCost->tableName . '.name," ","")) as name',
       $truckingCost->tableName . '.statuskey'
    ];

    $baseCondition = ' and ' .$truckingCost->tableName . '.statuskey = 1 and ' .$truckingCost->tableName . '.servicecost = 1';

    if (!empty($whereClause)) {
        $baseCondition .= ' and (' . $criteria . ')';
    }

    return $truckingCost->searchDataRow($arrColumns, $baseCondition);
}

$arrHeaderCol = array();
//ambil biaya
for ($col = $columnStart; $col <= $highestColumnIndex; ++$col) {
    $header = removeSpaceAndLowerCase($worksheet->getCellByColumnAndRow($col, 1)->getValue());
    $arrHeaderCol[$col] = $header;
}


//ambil selling col
$arrSellingCol = array_filter($arrHeaderCol, function ($value) {
    return stripos($value, 'jual') !== false;
});

//cost col
$arrCostCol = array_filter($arrHeaderCol, function ($value) {
    return stripos($value, 'jual') === false;
});

//multi drop column
$headerMultiDropCost = removeSpaceAndLowerCase($worksheet->getCellByColumnAndRow($columnMultiDropCost, 1)->getValue());
$headerMultiDropSelling = removeSpaceAndLowerCase($worksheet->getCellByColumnAndRow($columnMultiDropSelling, 1)->getValue());
$headerMultiDropSelling = str_replace('jual','', $headerMultiDropSelling);

$arrCostCol[] = $headerMultiDropCost;
$arrSellingCol[] = $headerMultiDropSelling;

$arrCostHeader = array_unique(array_merge($arrCostCol, $arrSellingCol));


//get all cost
$rsTruckingCost = getTruckingCost();

$costsCols = [];
foreach ($rsTruckingCost as $costRow) { 
    $costName = removeSpaceAndLowerCase($costRow['name']);
    if (in_array($costName, $arrCostCol)) {
        $index = array_search($costName, $arrCostCol);
        $costCols[$costName] = $index;
    }
    
}

$costCol = [];
$whereCost = [];
foreach ($costCols as $key => $value) {
    $costCol[$value] = $key;
    $whereCost[] = 'REPLACE(LOWER('.$truckingCost->tableName.'.name), " ","") LIKE "%' . $key .'%"';
}
ksort($costCol);

$whereCost= implode(' OR ', $whereCost);

$rsCost = getTruckingCost($whereCost);

$costIndex = array_flip($costCol);

//short berdasarkan costIndex urutan rs cost
usort($rsCost, function ($a, $b) use ($costIndex) {
    return $costIndex[$a['name']] <=> $costIndex[$b['name']];
});

$rsCostAssoc = array_column(array_map(function ($cost) {
    $cost['name'] = strtolower(str_replace(' ', '', $cost['name']));
    return $cost;
}, $rsCost), null, 'name');


//search selling
$whereClause = array();
foreach($arrSellingCol as $keyword) {
    $keyword = str_replace('jual', '', $keyword);
    $whereClause[] = 'REPLACE(LOWER('.$truckingCost->tableName.'.name), " ","") LIKE "%' . strtolower(str_replace(' ', '', $keyword)) .'%"';
}
$whereClause = implode(' OR ', $whereClause);

$rsTruckingCostSelling = getTruckingCost($whereClause);

$sellingCols = array_map(function ($value) {
    return str_replace('jual', '', $value);
}, $arrSellingCol);

$sellingIndex = array_flip($sellingCols);

usort($rsTruckingCostSelling, function ($a, $b) use ($sellingIndex) {
    return $sellingIndex[$a['name']] <=> $sellingIndex[$b['name']];
});

$rsSellingCostAssoc = array_column(array_map(function ($cost) {
    $cost['name'] = strtolower(str_replace(' ', '', $cost['name']));
    return $cost;
}, $rsTruckingCostSelling), null, 'name');

for ($row = 2; $row <= $highestRow; ++$row) {

    $customerId = $worksheet->getCellByColumnAndRow(3, $row)->getValue();
    $shippingInstruction = $worksheet->getCellByColumnAndRow(5, $row)->getValue(); //PCL
    $customerIdLower = removeSpaceAndLowerCase($customerId);
    $supplierId = $worksheet->getCellByColumnAndRow(11, $row)->getValue(); //Supplier
    $supplierIdLower = removeSpaceAndLowerCase($supplierId); //Supplier

    $plannerId = $worksheet->getCellByColumnAndRow(6, $row)->getValue();
    $plannerIdLower = removeSpaceAndLowerCase($plannerId);

    $jobTypeId = removeSpaceAndLowerCase($worksheet->getCellByColumnAndRow(7, $row)->getValue()); //Job Type
    $serviceTypeId = removeSpaceAndLowerCase($worksheet->getCellByColumnAndRow(8, $row)->getValue());
    $driverName = removeSpaceAndLowerCase($worksheet->getCellByColumnAndRow(10, $row)->getValue()); //Driver
    $policeNumber = $worksheet->getCellByColumnAndRow(9, $row)->getValue(); //No Polisi
    $policeNumber = strtoupper($car->normalizePoliceNumber($policeNumber)); 
    $policeNumberVendor = strtoupper($worksheet->getCellByColumnAndRow(12, $row)->getValue()); //No Polisi Vendor
    $routeFromId = removeSpaceAndLowerCase($worksheet->getCellByColumnAndRow(13, $row)->getValue()); //Gudang Pick UP
    $routeToId = removeSpaceAndLowerCase($worksheet->getCellByColumnAndRow(14, $row)->getValue()); //Area Terjauh

    array_push($arrCustomerName, $customerIdLower);
    array_push($arrJobCategory, $jobTypeId); //Job Type
    array_push($arrDriverName, $driverName); 
    array_push($arrPoliceNumber, $policeNumber);
    array_push($arrServiceName, $serviceTypeId);
    array_push($arrLocationName, ...[$routeFromId, $routeToId]); //Location
    array_push($arrSupplierName, $supplierIdLower);
    array_push($arrPlannerName, $plannerIdLower);

    if(empty($policeNumber) && empty($supplierId)) {
        array_push($arrErrorMsg,$shippingInstruction. ', '. ''. $customerId .'. Vendor tidak boleh kosong.'); 
    }

    if(empty($policeNumber) && !empty($supplierId) && empty($policeNumberVendor)) {
        array_push($arrErrorMsg, ''. $supplierId .'. Mobil vendor tidak boleh kosong.'); 
    }

}

//get cargo
$arrTempCargo = $obj->getCargoType();
$arrTempCargo = array_column($arrTempCargo, 'pkey', 'name');

$arrCargoType = array();
foreach ($arrTempCargo as $key => $row) {
    $arrCargoType[strtolower($key)] = $row;
}

$rsCategory = $truckingServiceOrderCategory->searchDataRow(array($truckingServiceOrderCategory->tableName . '.pkey', $truckingServiceOrderCategory->tableName . '.code', $truckingServiceOrderCategory->tableName . '.name'), ' and ' . $truckingServiceOrderCategory->tableName . '.name = ' . $obj->oDbCon->paramString($categoryName));
$rsWarehouse = $warehouse->searchDataRow(array($warehouse->tableName . '.pkey', $warehouse->tableName . '.code', $warehouse->tableName . '.name'), ' and ' . $warehouse->tableName . '.name = ' . $obj->oDbCon->paramString($warehouseName));


$rsJobCategory = $truckingServiceOrderCategory->searchDataRow(array($truckingServiceOrderCategory->tableName . '.pkey', $truckingServiceOrderCategory->tableName . '.code', 'lower(replace(' . $truckingServiceOrderCategory->tableName . '.name," ", "")) as name',), ' and lower(replace(' . $truckingServiceOrderCategory->tableName . '.name," ","")) in (' . $obj->oDbCon->paramString($arrJobCategory, ',') . ')  ');
$arrJobCategoryNameCol = array_column($rsJobCategory, 'name');
$rsJobCategoryCols = $obj->reindexDetailCollections($rsJobCategory, 'name');

$arrTruckingCostNameCol = array_column($rsTruckingCost, 'name');
$rsTruckingCostCols = $obj->reindexDetailCollections($rsTruckingCost, 'name');

$rsCustomer = $customer->searchDataRow(array($customer->tableName.'.pkey',
                                                $customer->tableName.'.code',
                                                'lower(replace('.$customer->tableName.'.name," ", "")) as name',
                                                $customer->tableName.'.statuskey'
                                            ), ' and ' . $customer->tableName.'.statuskey = 2 and lower(replace(' . $customer->tableName . '.name," ","")) in ('. $obj->oDbCon->paramString($arrCustomerName, ',') .')  ');
$arrCustomerNameCol = array_column($rsCustomer, 'name');
$rsCustomerCols = $obj->reindexDetailCollections($rsCustomer, 'name');


$rsDriver = $employee->searchDataRow(array($employee->tableName.'.pkey',
                                                $employee->tableName.'.code',
                                                'lower(replace('.$employee->tableName.'.name," ","")) as name',
                                                $employee->tableName.'.statuskey',
                                                $employee->tableName.'.isdriver'
                                            ), ' and ' . $employee->tableName.'.statuskey = 2 and '. $employee->tableName.'.isdriver = 1 and lower(replace(' . $employee->tableName . '.name," ","")) in ('. $obj->oDbCon->paramString($arrDriverName, ',') .')  ');
$arrDriverNameCol = array_column($rsDriver,  'name');
$rsDriverCols = $obj->reindexDetailCollections($rsDriver, 'name');

$rsCar = $car->searchDataRow(array(
                                $car->tableName.'.pkey',
                                $car->tableName.'.code',
                                'upper('.$car->tableName.'.policenumber) as policenumber',
                                $car->tableName.'.statuskey'
                            ), ' and ' . $car->tableName.'.statuskey = 1 and ' . $car->tableName.'.policenumber in ('. $obj->oDbCon->paramString($arrPoliceNumber, ',') .')  ');    
$arrPoliceNumberCol = array_column($rsCar,  'policenumber');
$rsCarCols = $obj->reindexDetailCollections($rsCar, 'policenumber');

$rsService = $service->searchDataRow(array($service->tableName.'.pkey',
                                                $service->tableName.'.code',
                                                'lower(replace('.$service->tableName.'.name," ","")) as name',
                                                $service->tableName.'.statuskey'
                                            ), ' and ' . $service->tableName.'.statuskey = 1 and lower(replace(' . $service->tableName . '.name," ","")) in ('. $obj->oDbCon->paramString($arrServiceName, ',') .')  ');
$arrServiceNameCol = array_column($rsService, 'name');
$rsServiceCols = $obj->reindexDetailCollections($rsService, 'name');

$rsLocation = $location->searchDataRow(array($location->tableName.'.pkey',
                                                $location->tableName.'.code',
                                                'lower(replace('.$location->tableName.'.name," ","")) as name',
                                                $location->tableName.'.statuskey'
                                            ), ' and ' . $location->tableName.'.statuskey = 1 and lower(replace(' . $location->tableName . '.name," ","")) in ('. $obj->oDbCon->paramString($arrLocationName, ',') .')  ');
$arrLocationNameCol = array_column($rsLocation, 'name');
$rsLocationCols = $obj->reindexDetailCollections($rsLocation, 'name');

$rsSupplier = $supplier->searchDataRow(array($supplier->tableName.'.pkey',
                                                $supplier->tableName.'.code',
                                                'lower(replace('.$supplier->tableName.'.name," ","")) as name',
                                                $supplier->tableName.'.statuskey'
                                            ), ' and ' . $supplier->tableName.'.statuskey = 1 and lower(replace(' . $supplier->tableName . '.name," ","")) in ('. $obj->oDbCon->paramString($arrSupplierName, ',') .')  ');
$arrSupplierNameCol = array_column($rsSupplier, 'name');
$rsSupplierCols = $obj->reindexDetailCollections($rsSupplier, 'name');

$rsPlanner = $employee->searchDataRow(array(
    $employee->tableName . '.pkey',
    $employee->tableName . '.code',
    'lower(replace(' . $employee->tableName . '.name," ","")) as name',
    $employee->tableName . '.statuskey',
), ' and ' . $employee->tableName . '.statuskey = 2  and lower(replace(' . $employee->tableName . '.name," ","")) in (' . $obj->oDbCon->paramString($arrPlannerName, ',') . ')  ');
$arrPlannerNameCol = array_column($rsPlanner, 'name');
$rsPlannerCols = $obj->reindexDetailCollections($rsPlanner, 'name');

//cari apakah trucking cost terdaftar di database atau tidak
foreach ($arrCostCol  as $row) {
    if (empty($row)) {
        continue;
    }

    if (!in_array($row, $arrTruckingCostNameCol)) {
        array_push($arrErrorMsg, $row . ' tidak terdaftar.');
    }
}

//cari apakah customer terdaftar di database atau tidak
foreach($arrCustomerName as $row) {
    if(empty($row)) {
        continue;
    }

    if(!in_array($row, $arrCustomerNameCol)) {
        array_push($arrErrorMsg, $row . ' tidak terdaftar.');
    }
}

//cari apakah driver terdaftar di database atau tidak
foreach ($arrDriverName as $row) {
    if (empty($row)) {
        continue;
    }

    if (!in_array($row, $arrDriverNameCol)) {
        array_push($arrErrorMsg, $row . ' tidak terdaftar.');
    }
}

//cari apakah no polisi terdaftar di database atau tidak
foreach ($arrPoliceNumber as $row) {
    if (empty($row)) {
        continue;
    }

    if (!in_array($row, $arrPoliceNumberCol)) {
        array_push($arrErrorMsg, $row . ' tidak terdaftar.');
    }
}

//cari apakah service terdaftar di database atau tidak
foreach ($arrServiceName as $row) {
    if (empty($row)) {
        continue;
    }

    if (!in_array($row, $arrServiceNameCol)) {
        array_push($arrErrorMsg, $row . ' tidak terdaftar.');
    }
}

//cari apakah location terdaftar di database atau tidak
foreach ($arrLocationName as $row) {
    if (empty($row)) {
        continue;
    }

    if (!in_array($row, $arrLocationNameCol)) {
        array_push($arrErrorMsg, $row . ' tidak terdaftar.');
    }
}

//cek job category
foreach($arrJobCategory as $row) {
    if (empty($row)) {
        continue;
    }

    if (!in_array($row, $arrJobCategoryNameCol)) {
        array_push($arrErrorMsg, $row . ' tidak terdaftar.');
    }
}


//cari apakah supplier terdaftar di database atau tidak
foreach ($arrSupplierName as $row) {
    if (empty($row)) {
        continue;
    }

    if (!in_array($row, $arrSupplierNameCol)) {
        array_push($arrErrorMsg, $row . ' tidak terdaftar.');
    }
}

//cari apkah planner terdaftar di database
foreach($arrPlannerName as $row) {
    if (empty($row)) {
        continue;
    }

    if (!in_array($row, $arrPlannerNameCol)) {
        array_push($arrErrorMsg, $row . ' tidak terdaftar.');
    }
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

$arrJobOrderData = array();
$arrWorkOrderData = array();

$jobOrderParty = 1;
$qtyCost = 1;
for ($row = 2; $row <= $highestRow; ++$row) {

    $trdate = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
    $trdate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($trdate);
    $trdate = $trdate->getTimestamp(); //Date

    $spkDate = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
    $spkDate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($spkDate);
    $spkDate = $spkDate->getTimestamp(); //Date

    $customerId = removeSpaceAndLowerCase($worksheet->getCellByColumnAndRow(3, $row)->getValue()); //Customer
    $purchaseOrderReference = $worksheet->getCellByColumnAndRow(4, $row)->getValue(); //Purchase Order Reference
    $shippingInstruction = $worksheet->getCellByColumnAndRow(5, $row)->getValue(); //PCL
    $plannerId = removeSpaceAndLowerCase($worksheet->getCellByColumnAndRow(6, $row)->getValue()); //Planner
    $jobCategoryId = removeSpaceAndLowerCase($worksheet->getCellByColumnAndRow(7, $row)->getValue()); //Job Type
    $serviceTypeId = removeSpaceAndLowerCase($worksheet->getCellByColumnAndRow(8, $row)->getValue()); //Service Type
    $policeNumber = $worksheet->getCellByColumnAndRow(9, $row)->getValue(); //No Polisi
    $policeNumber = strtoupper($car->normalizePoliceNumber($policeNumber));
    $driverName = removeSpaceAndLowerCase($worksheet->getCellByColumnAndRow(10, $row)->getValue()); //Driver
    $supplierId = removeSpaceAndLowerCase($worksheet->getCellByColumnAndRow(11, $row)->getValue()); //Supplier
    $policeNumberVendor = strtoupper($worksheet->getCellByColumnAndRow(12, $row)->getValue()); //No Polisi Vendor
    $routeFromId = removeSpaceAndLowerCase($worksheet->getCellByColumnAndRow(13, $row)->getValue()); //Route From
    $routeToId = removeSpaceAndLowerCase($worksheet->getCellByColumnAndRow(14, $row)->getValue()); //Route To
    $trDesc = $worksheet->getCellByColumnAndRow(15, $row)->getValue(); //Description Header JO
    $descriptionDetail = $worksheet->getCellByColumnAndRow(16, $row)->getValue(); //Description (STORE Column) //detail JO
    $price = $worksheet->getCellByColumnAndRow(17, $row)->getValue(); //Harga Mobil


    $vendorCost = $worksheet->getCellByColumnAndRow(18, $row)->getValue(); //biaya vendor
    $taxValueVendor = $worksheet->getCellByColumnAndRow(19, $row)->getValue(); //ppn vendor persen
    $chkIncludeTax = $worksheet->getCellByColumnAndRow(20, $row)->getValue(); // inc/exc ppn vendor
    $qtyMultiDrop = $worksheet->getCellByColumnAndRow(21, $row)->getValue(); //QTY Multi
    $priceMultiDropName = removeSpaceAndLowerCase($worksheet->getCellByColumnAndRow($columnMultiDropCost, 1)->getValue()); //Column multi tetap
    $priceSellingMultiDropName = removeSpaceAndLowerCase($worksheet->getCellByColumnAndRow($columnMultiDropSelling, 1)->getValue()); //Column selling multi tetap
    $priceSellingMultiDropName = str_replace('jual', '', $priceSellingMultiDropName);

    $priceMultiDrop = $worksheet->getCellByColumnAndRow($columnMultiDropCost, $row)->getValue(); //Column multi tetap
    $priceSellingMultiDrop = $worksheet->getCellByColumnAndRow($columnMultiDropSelling, $row)->getValue(); //Column selling multi tetap


    
    $rsCustomerCol = $rsCustomerCols[$customerId];
    $rsSupplierCol = $rsSupplierCols[$supplierId];
    $rsLocationFromCol = $rsLocationCols[$routeFromId];
    $rsLocationToCol = $rsLocationCols[$routeToId];
    $rsDriverCol = $rsDriverCols[$driverName];
    $rsCarCol = $rsCarCols[$policeNumber];
    $rsServiceCol = $rsServiceCols[$serviceTypeId];
    $rsJobCategoryCol = $rsJobCategoryCols[$jobCategoryId];
    $rsPlannerCol = $rsPlannerCols[$plannerId];


    $arrDetail = array();
    $arrSellingPrice = array();
    
    $finalStores = '';
    $finalSI = '';
    //multi drop
    if (!empty($priceSellingMultiDrop) && isset($rsSellingCostAssoc[$priceSellingMultiDropName])) {
        $rsSelling = $rsSellingCostAssoc[$priceSellingMultiDropName];

        $priceSellingMultiDrop = $priceSellingMultiDrop / $qtyMultiDrop;//dibagi dengan qty

        $store = explode('+', $descriptionDetail);
        $store = array_reverse($store);

        $sellingDesc = explode(',', $shippingInstruction);
        $sellingDesc = array_reverse($sellingDesc);
        $arrPushedSotore = array();
        for ($i = 0; $i < $qtyMultiDrop; $i++) {
            $storeName = isset($store[$i]) ? $store[$i] : end($store);
            $sellingDescName = isset($sellingDesc[$i]) ? $sellingDesc[$i] : end($sellingDesc);
            
            array_push($arrSellingPrice, array(
                'qty' => 1, //tembak 1
                'service_id' => $rsSelling['pkey'],
                'service_name' => $rsSelling['name'],
                'price' => $priceSellingMultiDrop,
                'store' => $storeName,
                'notes' => $sellingDescName
            ));
        }

        $remainingStores = array_slice($store, $qtyMultiDrop, null, true);
        $remainingSellingDesc = array_slice($sellingDesc, $qtyMultiDrop, null, true);

        $finalStores = !empty($remainingStores) ? $remainingStores : [end($store)];
        $finalStores = implode('+', $finalStores);

        $descriptionDetail = !empty($remainingSellingDesc) ? $remainingSellingDesc : [end($sellingDesc)];
        $finalSI = implode(',', $descriptionDetail);
    } 

    array_push($arrDetail, array(
        'qty' => $jobOrderParty,
        'item_id' => $rsServiceCol[0]['pkey'],
        'item_name' => $rsServiceCol[0]['name'],
        'price' => (empty($price) ? 0 : $price),
        'note' => empty($finalStores) ? $descriptionDetail : $finalStores
    ));


    foreach($arrSellingCol as $colIndex => $header) {
        
        $sellingValue = $worksheet->getCellByColumnAndRow($colIndex, $row)->getValue();
     
        $sellingCostName = $header;
        $sellingCostName = str_replace('jual', '', $sellingCostName);
        $sellingCostNameLower = strtolower($sellingCostName);
        
        if ($rsSellingCostAssoc[$sellingCostNameLower]) {

            $rsSellingCost = $rsSellingCostAssoc[$sellingCostNameLower];
        
            if ($rsSellingCost['name'] == $sellingCostNameLower && !empty($sellingValue)) {
                array_push(
                    $arrSellingPrice,
                    array(
                        'qty' => $qtyCost, 
                        'service_id' =>  $rsSellingCost['pkey'],
                        'service_name' =>  $rsSellingCost['name'],
                        'price' => $sellingValue
                    )
                );
            }

        }
    }

    $arrCostPrice = array();

    if (!empty($priceMultiDrop) && isset($rsCostAssoc[$priceMultiDropName])) {
        $rsCost = $rsCostAssoc[$priceMultiDropName];
        array_push($arrCostPrice, array(
            'qty' => $qtyMultiDrop, 
            'cost_id' => $rsCost['pkey'],
            'cost_name' => $rsCost['name'],
            'price' => $priceMultiDrop
        ));
    }

    foreach($costCol as $colIndex => $header) {
        
        $costValue = $worksheet->getCellByColumnAndRow($colIndex, $row)->getValue();

        $costName = $header;
        $costNameLower = removeSpaceAndLowerCase($costName);
       
        if ($rsCostAssoc[$costNameLower]) {
            
            $rsCost = $rsCostAssoc[$costNameLower];
            if ($rsCost['name'] == $costNameLower && !empty($costValue)) {
                
                array_push(
                $arrCostPrice,
                array(
                        'qty' => $qtyCost,
                        'cost_id' => $rsCost['pkey'],
                        'cost_name' => $rsCost['name'],
                        'price' => $costValue
                    )
                );
            }

        }
    }

    //Job Order
    array_push($arrJobOrderData, array(
        'indexrow' => $row,
        'status' => $status,
        'trdate' => $trdate,
        'spkdate' => $spkDate,
        'warehouse_id' => $rsWarehouse[0]['pkey'],
        'do_number' => (empty($finalSI) ? $shippingInstruction : $finalSI),
        'po_reference' => $purchaseOrderReference,  
        'customer_id' => $rsCustomerCol[0]['pkey'],
        'customer_name' => $rsCustomerCol[0]['name'],
        'planner_id' => $rsPlannerCol[0]['pkey'],
        'planner_name' => $rsPlannerCol[0]['name'],
        'cargo_id' => $arrCargoType[strtolower($cargoType)],
        'category_id' => $rsJobCategoryCol[0]['pkey'],
        'category_name' => $rsJobCategoryCol[0]['name'],
        'route_from_id' => $rsLocationFromCol[0]['pkey'],
        'route_from_name' => $rsLocationFromCol[0]['name'],
        'route_to_id' => $rsLocationToCol[0]['pkey'],
        'route_to_name' => $rsLocationToCol[0]['name'],
        'detail' => $arrDetail,
        'selling_price' => $arrSellingPrice,
        'route_from' => $routeFrom,
        'route_to' => $routeTo,
        'trdesc' => $trDesc
    ));

    //Work Order
    array_push($arrWorkOrderData, array(
        'indexrow' => $row,
        'warehouse_id' => $rsWarehouse[0]['pkey'],
        'customer_id' => $rsCustomerCol[0]['pkey'],
        'customer_name' => $rsCustomerCol[0]['name'],
        'trdate' => $spkDate,
        'driver_id' => (empty($rsCarCol[0]['pkey']) ? '' : $rsDriverCol[0]['pkey']),
        'driver_name' => $driverName,
        'police_number' => $policeNumber,
        'car_id' => $rsCarCol[0]['pkey'], 
        'category_id' => $rsCategory[0]['pkey'],
        'category_name' => $rsCategory[0]['name'],
        'supplier_id' => (empty($rsCarCol[0]['pkey']) ? $rsSupplierCol[0]['pkey'] : ''), //abaikan kalau ada mobil
        'supplier_name' => (empty($rsCarCol[0]['pkey']) ? $rsSupplierCol[0]['name'] : ''), //abaikan kalau ada mobil
        'car_vendor' => (empty($rsCarCol[0]['pkey']) ? $policeNumberVendor : ''), //abaikan kalau ada mobil
        'is_outsource' => (empty($rsCarCol[0]['pkey']) ? 1 : 0), //kalau nggak ada mobil is outsource
        'vendor_cost' => (empty($rsCarCol[0]['pkey']) ? $vendorCost : 0), //kalau nggak ada mobil 
        'tax_percentage' => (empty($rsCarCol[0]['pkey']) ? $taxValueVendor : 0), //kalau nggak ada mobil 
        'include_tax' => (empty($rsCarCol[0]['pkey']) ? $chkIncludeTax : 0), //kalau nggak ada mobil 
        'detail' => $arrCostPrice
    ));


}
// die;

for ($i = 0; $i < count($arrJobOrderData); $i++) {

    $data = $arrJobOrderData[$i];
    
    $detail = $data['detail'];
    $sellingPrice = $data['selling_price'];

    $date = date('Y-m-d H:i:s', $data['trdate']);
    $spkDate = date('Y-m-d H:i:s', $data['spkdate']);

    $arrParam = array();
    $arrParam['code'] = 'xxxxx';
    $arrParam['selStatus'] = $data['status'];
    $arrParam['trDate'] = $obj->formatDBDate($date, 'd / m / Y');
    $arrParam['selWarehouseKey'] = $data['warehouse_id'];
    $arrParam['doNumber'] = $data['do_number'];
    $arrParam['hidCustomerKey'] = $data['customer_id'];
    $arrParam['hidPlannerKey'] = $data['planner_id'];
    $arrParam['hidCategoryKey'] = $data['category_id'];
    $arrParam['hidCargoType'] = $data['cargo_id'];
    $arrParam['hidStuffingLocationFromKey'] = $data['route_from_id'];
    $arrParam['hidStuffingLocationKey'] = $data['route_to_id'];
    $arrParam['trDesc'] = $data['trdesc'];
    $arrParam['poReference'] = $data['po_reference'];

    //detail
    $arrParam['hidDetailKey'] = array();
    $arrParam['qty'] = array();
    $arrParam['hidItemKey'] = array();
    $arrParam['trShipmentDate'] = array();
    $arrParam['price'] = array();
    $arrParam['detailNotes'] = array();

    foreach ($detail as $key => $detailRow) {

        array_push($arrParam['hidDetailKey'], 0);
        array_push($arrParam['qty'], $detailRow['qty']);
        array_push($arrParam['hidItemKey'], $detailRow['item_id']);
        array_push($arrParam['trShipmentDate'], $obj->formatDBDate($spkDate, 'd / m / Y H:i:s'));
        array_push($arrParam['price'], $detailRow['price']);
        array_push($arrParam['detailNotes'], $detailRow['note']);
    }

    //selling price
    $arrParam['hidDetailCostKey'] = array();
    $arrParam['qtyCost'] = array();
    $arrParam['hidItemKeyCost'] = array();
    $arrParam['store'] = array();
    $arrParam['sellingDesc'] = array();
    $arrParam['priceCost'] = array();

    foreach ($sellingPrice as $key => $sellingPriceRow) {
        array_push($arrParam['hidDetailCostKey'], 0);
        array_push($arrParam['qtyCost'], $sellingPriceRow['qty']);
        array_push($arrParam['hidItemKeyCost'], $sellingPriceRow['service_id']);
        array_push($arrParam['priceCost'], $sellingPriceRow['price']);
        array_push($arrParam['store'], $sellingPriceRow['store']);
        array_push($arrParam['sellingDesc'], $sellingPriceRow['notes']);
    }

    //Save Job Order
    $result = $obj->addData($arrParam);

    //Add Work Order
    if($result[0]['valid']) {

        $rsSO = $result[0]['data'];

        $obj->changeStatus($rsSO['pkey'], 2, '', false, true);

        $salesOrderKey = $rsSO['pkey'];
        $trDate = $rsSO['trdate'];
        $cargotypekey = $rsSO['cargotypekey'];
        $categorykey = $rsSO['categorykey'];
        $plannerKey = $rsSO['plannerkey'];

        $rsSODetail = $obj->getDetailWithRelatedInformation($salesOrderKey);

        //ambil index pertama, karena dari detail JO hanya 1
        $sodetailkey = $rsSODetail[0]['pkey'];
        $itemkey = $rsSODetail[0]['itemkey'];

        $routeFrom = $rsSO['stuffinglocationfromname'];
        $routeTo = $rsSO['locationname'];


        foreach($arrWorkOrderData as $key => $WOData) {
            
            $detailWo = $WOData['detail'];
            $spkDate = date('Y-m-d H:i:s', $WOData['trdate']);
            
            if($data['indexrow'] === $WOData['indexrow']) {

                $arrParamWO = array();
                $arrParamWO['code'] = 'xxxxxx';
                $arrParamWO['hidSOKey'] = $salesOrderKey;
                $arrParamWO['hidSODetailKey'] = $sodetailkey;
                $arrParamWO['hidItemKey'] = $itemkey;
                $arrParamWO['trDate'] = $obj->formatDBDate($spkDate, 'd / m / Y');
                $arrParamWO['trDateStuffing'] = $obj->formatDBDate($spkDate, 'd / m / Y H:i');
                $arrParamWO['hidCargoTypeKey'] = $cargotypekey;
                $arrParamWO['hidCategoryKey'] = $categorykey;
                $arrParamWO['selJobType'] = 1; // tembak mati dulu
                $arrParamWO['selWarehouseKey'] = $WOData['warehouse_id'];
                $arrParamWO['stuffingAddress'] = '';
                $arrParamWO['hidDriverKey'] = $WOData['driver_id'];
                $arrParamWO['hidCarKey'] = $WOData['car_id'];
                $arrParamWO['hidCustomerKey'] = $WOData['customer_id'];
                $arrParamWO['routeFrom'] = $routeFrom;
                $arrParamWO['routeTo'] = $routeTo;
                $arrParamWO['chkIsOutsource'] = $WOData['is_outsource'];
                $arrParamWO['outsourceCarRegistrationNumber'] = $WOData['car_vendor'];
                $arrParamWO['hidSupplierKey'] = $WOData['supplier_id'];
                $arrParamWO['selStatus'] = 1;
                $arrParamWO['islinked'] = true;
                $arrParamWO['createdBy'] = 0;
                $arrParamWO['outsourceCost'] = $WOData['vendor_cost'];
                $arrParamWO['taxPercentage'] = $WOData['tax_percentage'];
                $arrParamWO['chkIncludeTax'] = $WOData['include_tax'];
                $arrParamWO['taxValue'] = 0; //kalau tidak di set tidak masuk database
                $employeekey = ($WOData['is_outsource'] == 1) ? $plannerKey : '' ; // penerima biaya jika outsource planner, jika non-outsource ada sopir default dinormalize


                //detail SPK
                $arrParamWO['hidDetailKey'] = array();
                $arrParamWO['qtyCostDetail'] = array();
                $arrParamWO['hidCostKey'] = array();
                $arrParamWO['requestAmount'] = array();
                $arrParamWO['hidEmployeeDetailKey'] = array();
                foreach($detailWo as $key => $detailRow) {
                    array_push($arrParamWO['hidDetailKey'], 0);
                    array_push($arrParamWO['qtyCostDetail'], $detailRow['qty']);
                    array_push($arrParamWO['hidCostKey'], $detailRow['cost_id']);
                    array_push($arrParamWO['requestAmount'], $detailRow['price']);
                    array_push($arrParamWO['hidEmployeeDetailKey'], $employeekey);
                }
        
                //Save Work Order
                $resultWO = $truckingServiceWorkOrder->addData($arrParamWO);
                if (!$resultWO[0]['valid']) {
                    echo '<span style="font-weight:bold;color:red;font-size:12px;margin:4px">ERROR SPK:</span><br>';
                    foreach ($resultWO as $rswo) {
                        echo '<span style="margin:4px;color:red">- ' . $rswo['message'] . '</span><br>';
                    }
                } 
            
            }

        }

        //echo '<span style="color:green;margin:4px">Import data berhasil.  <br> - ' . $result[0]['data']['code'] . '<br></span>';

    } else {
        echo '<span style="font-weight:bold;color:red;font-size:12px;margin:4px">ERROR :</span><br>';
        foreach ($result as $rs) {
            echo '<span style="margin:4px;color:red">- ' . $rs['message'] . '</span><br>';
        }
    }
    
}

echo 'done';

?>