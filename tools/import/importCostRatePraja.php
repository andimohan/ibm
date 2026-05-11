<?php

require_once '../../_config.php';
require_once '_include.php';
require_once 'function-v2.php';

require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/CostRate.class.php';
require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/Service.class.php';
require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/Consignee.class.php';
require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/Warehouse.class.php';
require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/Location.class.php';
require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/TruckingJob.class.php';
require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/CarCategory.class.php';

$costRate = new CostRate();
// $service = new Service();
$truckingService = new Service();
$truckingCost = new Service(TRUCKING_SERVICE, 1);
$consignee = new Consignee();
$warehouse = new Warehouse();
$location = new Location();
$truckingJob = new TruckingJob();
$carCategory = new CarCategory();
$security = new Security();

$obj = $costRate; 
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,11,true));


$MODULE_NAME = 'costRate';
$TITLE = $obj->lang['costRate'];


$code = '[auto code]';
$categoryName = 'KONTRAK';
$warehouseName = 'HO';
$cargoType = 'Dry';
$status = 1;

$arrCustomerName = array();
$arrDriverName = array();
$arrPoliceNumber = array();


$arrJobCategory = array();
$arrSupplierName = array();
$arrPlannerName = array();

$arrErrorMsg = array();


function removeSpaceAndLowerCase($value) {
    $result = strtolower($value);
    $result = str_replace(' ', '', $result);
    return $result;
}

$arrCommission = $obj->rsDriverCommission;
for ($i = 0; $i < count($arrCommission); $i++) {
    $arrCommission[$i]['name'] = removeSpaceAndLowerCase($arrCommission[$i]['name']);
    $arrCommission[$i]['nameoriginal'] = $arrCommission[$i]['name'];
}

$rsWarehouse = $warehouse->searchDataRow(array($warehouse->tableName . '.pkey', $warehouse->tableName . '.code', $warehouse->tableName . '.name'), ' and ' . $warehouse->tableName . '.name = ' . $obj->oDbCon->paramString($warehouseName));
//$arrService = $truckingService->searchData('', '', true, ' and '.$truckingService->tableName.'.statuskey = 1 order by '.$truckingService->tableName.'.name asc');
$arrCarCategory = $carCategory->searchData('', '', true, ' and '.$carCategory->tableName.'.statuskey = 1 order by '.$carCategory->tableName.'.name asc');


$arrCost = array();

$arrCarCategoryName = array();
$arrConsignee = array();
$arrLocationName = array();
$arrCostRate = array() ;
$arrJobType = array() ;
$arrCargoType = array();

$arrCarCategoryNameOriginal = array();
$arrConsigneeOriginal = array();
$arrLocationNameOriginal = array();
$arrJobTypeOriginal = array() ;
$arrCargoTypeOriginal = array();


$columnCostStart = 7;

$arrCostHeaderCol = array();
$arrCostHeaderOriginalCol = array();
//ambil biaya
for ($col = $columnCostStart; $col <= $highestColumnIndex; ++$col) {
    $headerOriginal = $worksheet->getCellByColumnAndRow($col, 1)->getValue();
    $header = removeSpaceAndLowerCase($headerOriginal);
    
    $arrCostHeaderCol[$col] = $header;
    $arrCostHeaderOriginalCol[$col] = $headerOriginal;
}



for ($row = 2; $row <= $highestRow; ++$row) {

    $name = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
    $consigneeNameOriginal = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
    $locationNameOriginal = $worksheet->getCellByColumnAndRow(3, $row)->getValue();
    $cargoTypeOriginal = $worksheet->getCellByColumnAndRow(4, $row)->getValue();
    $carCategoryOriginal = $worksheet->getCellByColumnAndRow(5, $row)->getValue();
    $jobTypeOriginal = $worksheet->getCellByColumnAndRow(6, $row)->getValue();

    $consigneeName = removeSpaceAndLowerCase($consigneeNameOriginal);
    $locationName = removeSpaceAndLowerCase($locationNameOriginal);
    $cargoType = removeSpaceAndLowerCase($cargoTypeOriginal);
    $carCategoryRow = removeSpaceAndLowerCase($carCategoryOriginal);
    $jobType = removeSpaceAndLowerCase($jobTypeOriginal);

    $amount = removeSpaceAndLowerCase($worksheet->getCellByColumnAndRow(7, $row)->getValue()); //amount
 
    array_push($arrCarCategoryName, $carCategoryRow);
    array_push($arrConsignee, $consigneeName);
    array_push($arrLocationName, $locationName);
    array_push($arrCargoType, $cargoType);
    array_push($arrJobType, $jobType);
   
    array_push($arrCarCategoryNameOriginal, $carCategoryOriginal);
    array_push($arrConsigneeOriginal, $consigneeNameOriginal);
    array_push($arrLocationNameOriginal, $locationNameOriginal);
    array_push($arrCargoTypeOriginal, $cargoTypeOriginal);
    array_push($arrJobTypeOriginal, $jobTypeOriginal);

    // $obj->setLog($arrLocationName,true);
    foreach ($arrCostHeaderCol as $colIndex => $header) {
        
        $costAmount = $worksheet->getCellByColumnAndRow($colIndex, $row)->getValue();
        if ($costAmount === null || $costAmount === '' || trim($costAmount) === '') {
            $costAmount = 0;
        }
        
        $costName = $header;
        
        array_push($arrCostRate, array(
            'name' => $name,
            'location' => $locationName,
            'consignee' => $consigneeName,
            'cargotype' => $cargoType,
            'carcategory' => $carCategoryRow,
            'jobtype' => $jobType,
            'cost' => $costName,
            'amount' => $costAmount
        ));
    }

}

$rsCargoType = $obj->getCargoType();
$rsCargoType = array_map(function ($data) {
    foreach ($data as $key => $value) {
        if (strtolower($key) === 'name' || $key === 1) {
            $data[$key] = strtolower(str_replace(' ', '', $value));
        }
    }
    return $data;
}, $rsCargoType);
$arrCargoTypeCol = array_column($rsCargoType, 'name');
$rsCargoTypeCols = $obj->reindexDetailCollections($rsCargoType, 'name');


$rsTruckingJob = $truckingJob->searchDataRow(array($truckingJob->tableName.'.pkey',
                                                $truckingJob->tableName.'.code',
                                                'lower(replace('.$truckingJob->tableName.'.name," ","")) as name',
                                                $truckingJob->tableName.'.statuskey'
                                            ), ' and ' . $truckingJob->tableName.'.statuskey = 1 and lower(replace(' . $truckingJob->tableName . '.name," ","")) in ('. $obj->oDbCon->paramString($arrJobType, ',') .')  ');
$arrJobTypeCol = array_column($rsTruckingJob, 'name');
$rsTruckingJobCols = $obj->reindexDetailCollections($rsTruckingJob, 'name');


$rsLocation = $location->searchDataRow(array($location->tableName.'.pkey',
                                                $location->tableName.'.code',
                                                'lower(replace(' . $location->tableName . '.name," ","")) as name',
                                                $location->tableName.'.statuskey'
                                            ), ' and ' . $location->tableName.'.statuskey = 1');
$obj->htmlEntityDecodeArray($rsLocation, array('name'));
$arrLocationCol = array_column($rsLocation, 'name');
$rsLocationCols = $obj->reindexDetailCollections($rsLocation, 'name');

$rsConsignee = $consignee->searchDataRow(array($consignee->tableName.'.pkey',
                                                $consignee->tableName.'.code',
                                                'lower(replace('.$consignee->tableName.'.name," ","")) as name',
                                                $consignee->tableName.'.statuskey'
                                            ), ' and ' . $consignee->tableName.'.statuskey = 1 and lower(replace(' . $consignee->tableName . '.name," ","")) in ('. $obj->oDbCon->paramString($arrConsignee, ',') .')  ');
$arrConsigneeCol = array_column($rsConsignee, 'name');
$rsConsigneeCols = $obj->reindexDetailCollections($rsConsignee, 'name');

// $rsService = $truckingService->searchDataRow(array($truckingService->tableName.'.pkey',
//                                                 $truckingService->tableName.'.code',
//                                                 'lower(replace('.$truckingService->tableName.'.name," ","")) as name',
//                                                 $truckingService->tableName.'.statuskey'
//                                             ), ' and ' . $truckingService->tableName.'.statuskey = 1 and lower(replace(' . $truckingService->tableName . '.name," ","")) in ('. $obj->oDbCon->paramString($arrServiceName, ',') .')  ');
// $arrServiceNameCol = array_column($rsService, 'name');
// $rsServiceCols = $obj->reindexDetailCollections($rsService, 'name');

$rsCarCategory = $carCategory->searchDataRow(array($carCategory->tableName.'.pkey',
                                                    $carCategory->tableName.'.code',
                                                    'lower(replace('.$carCategory->tableName.'.name," ","")) as name',
                                                    $carCategory->tableName.'.statuskey'
                                                ), ' and ' . $carCategory->tableName.'.statuskey = 1 and lower(replace(' . $carCategory->tableName . '.name," ","")) in ('. $obj->oDbCon->paramString($arrCarCategoryName, ',') .')  ');
$arrCarCategoryNameCol = array_column($rsCarCategory, 'name');
$rsCarCategoryCols = $obj->reindexDetailCollections($rsCarCategory, 'name');

$rsCost = $truckingCost->searchDataRow(array($truckingCost->tableName.'.pkey',
                                                $truckingCost->tableName.'.code',
                                                'lower(replace('.$truckingCost->tableName.'.name," ","")) as name',
                                                $truckingCost->tableName.'.statuskey'
                                            ), ' and ' . $truckingCost->tableName.'.statuskey = 1 and lower(replace(' . $truckingService->tableName . '.name," ","")) in ('. $obj->oDbCon->paramString($arrCostHeaderCol, ',') .') ');

$rsCost = array_merge($arrCommission, $rsCost);
$arrCostCol = array_column($rsCost, 'name');
$rsCostCols = $obj->reindexDetailCollections($rsCost, 'name');


//cari apakah Consignee terdaftar di database atau tidak
// foreach ($arrConsignee  as $i => $row) {
//     if (empty($row)) {
//         continue;
//     }

//     if (!in_array($row, $arrConsigneeCol)) {
//         array_push($arrErrorMsg,  '<b>'.$obj->lang['consignee'].' : </b>' . $arrConsigneeOriginal[$i]. '. Data tidak terdaftar.');
//     }
// }

//cari apakah CargoType terdaftar di database atau tidak
foreach ($arrCargoType  as $i => $row) {
    if (empty($row)) {
        continue;
    }

    if (!in_array($row, $arrCargoTypeCol)) {
        array_push($arrErrorMsg,  '<b>'.$obj->lang['cargoType'].' : </b>' .$arrCargoTypeOriginal[$i] . '. Data tidak terdaftar.');
    }
}

//cari apakah JobType terdaftar di database atau tidak
foreach ($arrJobType  as $i =>$row) {
    if (empty($row)) {
        continue;
    }

    if (!in_array($row, $arrJobTypeCol)) {
        array_push($arrErrorMsg,  '<b>'.$obj->lang['jobType'].' : </b>' .$arrJobTypeOriginal[$i] . '. Data tidak terdaftar.');
    }
}

//cari apakah cost terdaftar di database atau tidak
foreach ($arrCostHeaderCol  as $i => $row) {
    if (empty($row)) {
        continue;
    }

    if (!in_array($row, $arrCostCol)) {
        array_push($arrErrorMsg,  '<b>'.$obj->lang['cost'].' : </b>' .$arrCostHeaderOriginalCol[$i] . '. Data tidak terdaftar.');
    }
}

//cari apakah trucking cost terdaftar di database atau tidak
foreach ($arrCarCategoryName  as $i => $row) {
    if (empty($row)) {
        continue;
    }

    if (!in_array($row, $arrCarCategoryNameCol)) {
        array_push($arrErrorMsg,  '<b>'.$obj->lang['carCategory'].' : </b>'.$arrCarCategoryNameOriginal[$i] . '. Data tidak terdaftar.');
    }
}
// $obj->setLog($arrLocationCol, true);
//cari apakah location terdaftar di database atau tidak
foreach ($arrLocationName  as $i => $row ) {
    if (empty($row)) {
        continue;
    }
    // $obj->setLog($row,true);
    if (!in_array($row, $arrLocationCol)) {
        array_push($arrErrorMsg,  '<b>'.$obj->lang['location'].' : </b>  '. $arrLocationNameOriginal[$i] . '. Data tidak terdaftar.');
    }
}

if (!empty($arrErrorMsg)) {
    echo '<table style="margin-left:5px; margin-top:5px;"> ';
    foreach ($arrErrorMsg as $row) {
        echo '<tr>';
        echo '<td style="padding:0.1em"><div style="color:red;"> ' . $row . ' </div></td>';
        echo '</tr>';
    }
    echo '</table>';
    die;
}



$arrParam = array();
$indexHeaderBefore = '';

for ($i = 0; $i < count($arrCostRate); $i++) {
    
    $name = trim($arrCostRate[$i]['name']);
    $consignee = trim($arrCostRate[$i]['consignee']);
    $location = trim($arrCostRate[$i]['location']);
    $cargoType = trim($arrCostRate[$i]['cargotype']);
    $jobTypeName = trim($arrCostRate[$i]['jobtype']);
    $truckingCostName = trim($arrCostRate[$i]['cost']);
    $carCategoryName = trim($arrCostRate[$i]['carcategory']);
    $amount = floatval($arrCostRate[$i]['amount']);

    if (empty($name)) $name = $prevName;
    if (empty($consignee)) $consignee = $prevConsignee;
    if (empty($location)) $location = $prevLocation;
    if(empty($cargoType)) $cargoType = $prevCargoType;

    $prevName = $name;
    $prevConsignee = $consignee;
    $prevLocation = $location;
    $prevCargoType = $cargoType;


    //$indexHeader = $name . '-' . $consignee . '-' . $location .'-'.$cargoType;
    $indexHeader = $name.'-'.$location.'-'.$cargoType;

    //if (empty($amount) || $amount <= 0) continue;

    if (!isset($rsCostCols[$truckingCostName][0]['pkey'])) continue;
    if (!isset($rsCarCategoryCols[$carCategoryName][0]['pkey'])) continue;
    if (!isset($rsTruckingJobCols[$jobTypeName][0]['pkey'])) continue;

    $truckingCostKey = $rsCostCols[$truckingCostName][0]['pkey'];
    $carCategoryKey = $rsCarCategoryCols[$carCategoryName][0]['pkey'];
    $jobTypeKey = $rsTruckingJobCols[$jobTypeName][0]['pkey'];


    $arrTemp = array();
    if (!isset($arrParam[$indexHeader])) {
        $arrTemp = array();
        $arrTemp['code'] = 'xxxxx';
        $arrTemp['indexHeader'] = $indexHeader;
        $arrTemp['selStatus'] = $status;
        $arrTemp['name'] = $name;

        $arrTemp['selWarehouseKey'] = $rsWarehouse[0]['pkey'];
        $arrTemp['hidCargoTypeKey'] = $rsCargoTypeCols[$cargoType][0]['pkey'];
        $arrTemp['hidLocationKey'] = $rsLocationCols[$location][0]['pkey'];
        $arrTemp['hidConsigneeKey'] = $rsConsigneeCols[$consignee][0]['pkey'];

        $arrTemp['hidJobTypeKey'] = array();
        $arrTemp['hidItemKey'] = array();
        $arrTemp['cost_' . $truckingCostKey . '_' . $carCategoryKey] = array();

        array_push($arrTemp['hidJobTypeKey'], $jobTypeKey);
        array_push($arrTemp['cost_' . $truckingCostKey . '_' . $carCategoryKey], $amount);

        foreach ($arrCarCategory as $data) {
            array_push($arrTemp['hidItemKey'], $data['pkey']);
        }

        $arrParam[$indexHeader] = $arrTemp;
        $indexHeaderBefore = $indexHeader;
    } else {
        if (!in_array($jobTypeKey, $arrParam[$indexHeader]['hidJobTypeKey'])) {
            array_push($arrParam[$indexHeader]['hidJobTypeKey'], $jobTypeKey);
        }

        $jobTypeIndex = array_search($jobTypeKey, $arrParam[$indexHeader]['hidJobTypeKey']);
        $costKeyName = 'cost_' . $truckingCostKey . '_' . $carCategoryKey;

        if (!isset($arrParam[$indexHeader][$costKeyName])) {
            $arrParam[$indexHeader][$costKeyName] = [];
        }

        $arrParam[$indexHeader][$costKeyName][$jobTypeIndex] = $amount;
    }
}

$rsCostRate = $obj->searchDataRow(array(
                $obj->tableName.'.pkey',
                $obj->tableName.'.code',
                'lower(replace(' . $obj->tableName . '.name," ","")) as name'
            ));

$rsCostRateCol = $obj->reindexDetailCollections($rsCostRate, 'name');
foreach ($arrParam as $arrData) { 
    $name = removeSpaceAndLowerCase($arrData['name']);
    if(isset($rsCostRateCol[$name])) {
        $rs = $rsCostRateCol[$name];
        //update detail
        updateCostRate($rs,$arrData);
    } else {
        $obj->addData($arrData);
    }
    
}




function updateCostRate($rs, $arrParam)
{
    global $obj, $rsCost;
    $obj->oDbCon->startTrans();
    $pkey = $rs[0]['pkey'];

    // Ambil detail existing per jobtype
    $sql = '
        select * from '.$obj->tableNameDetail.'
        where refkey = '.$obj->oDbCon->paramString($pkey).'
    ';
    $rsDetail = $obj->oDbCon->doQuery($sql);
    $detailMap = $obj->reindexDetailCollections($rsDetail, 'jobtypekey'); // jobtypekey → [row]

    // Ambil semua cost existing (1x fetch)
    $sql = '
        select * from '.$obj->tableCostDetail.'
        where refheaderkey = '.$obj->oDbCon->paramString($pkey).'
    ';
    $rsCostDetail = $obj->oDbCon->doQuery($sql);

    // Map existing cost
    $costMap = [];
    foreach ($rsCostDetail as $row) {
        $costMap[
            $row['refkey'] // detailkey
        ][
            $row['costkey']
        ][
            $row['carcategorykey']
        ] = $row['pkey'];
    }

    //update header
    $locationkey = $arrParam['hidLocationKey'];
    $cargotypekey = $arrParam['hidCargoTypeKey'];
    $consigneekey = $arrParam['hidConsigneeKey'];

    if (
        $locationkey != $rs[0]['locationkey'] ||
        $cargotypekey != $rs[0]['cargotypekey'] ||
        $consigneekey != $rs[0]['consigneekey']
    ) {
    
        $sqlUpdateHeader = '
            update
                '.$obj->tableName.'
            set
                '.$obj->tableName.'.locationkey = '.$obj->oDbCon->paramString($locationkey).',
                '.$obj->tableName.'.cargotypekey = '.$obj->oDbCon->paramString($cargotypekey).',
                '.$obj->tableName.'.consigneekey = '.$obj->oDbCon->paramString($consigneekey).'
            where
                '.$obj->tableName.'.pkey = '.$obj->oDbCon->paramString($pkey).'
        ';

        $obj->oDbCon->execute($sqlUpdateHeader);

    }

    $arrJobTypeKey = $arrParam['hidJobTypeKey'];
    $arrItemKey    = $arrParam['hidItemKey'];

    foreach ($arrJobTypeKey as $i => $jobTypeKey) {

        if (!isset($detailMap[$jobTypeKey])) {
            $detailKey = $obj->getNextKey($obj->tableNameDetail);

            $sql = '
                insert into '.$obj->tableNameDetail.' (pkey, refkey, jobtypekey)
                values (
                    '.$obj->oDbCon->paramString($detailKey).',
                    '.$obj->oDbCon->paramString($pkey).',
                    '.$obj->oDbCon->paramString($jobTypeKey).'
                )
            ';
            $obj->oDbCon->execute($sql);
        } else {
            $detailKey = $detailMap[$jobTypeKey][0]['pkey'];
        }

        foreach ($rsCost as $cost) {
            $costKey = $cost['pkey'];

            foreach ($arrItemKey as $carCategoryKey) {
                $price = $arrParam['cost_'.$costKey.'_'.$carCategoryKey][$i] ?? null;
                if ($price === '' || $price === null) continue;

                $exists = $costMap[$detailKey][$costKey][$carCategoryKey] ?? null;

                if ($exists) {
                    // UPDATE
                    $sql = '
                        update '.$obj->tableCostDetail.'
                        set price = '.$obj->oDbCon->paramString($price).'
                        where pkey = '.$obj->oDbCon->paramString($exists).'
                    ';
                } else {
                    // INSERT
                    $sql = '
                        insert into '.$obj->tableCostDetail.'
                        (refkey, refheaderkey, costkey, carcategorykey, price)
                        values (
                            '.$obj->oDbCon->paramString($detailKey).',
                            '.$obj->oDbCon->paramString($pkey).',
                            '.$obj->oDbCon->paramString($costKey).',
                            '.$obj->oDbCon->paramString($carCategoryKey).',
                            '.$obj->oDbCon->paramString($price).'
                        )
                    ';
                }

                $obj->oDbCon->execute($sql);
            }
        }
    }

    $obj->oDbCon->endTrans();
}




echo 'done';

?>