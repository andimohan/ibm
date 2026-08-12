<?php
die('This script is disabled. Please contact the administrator for assistance.');
include_once '../../_config.php'; 
include_once '../../_include-v2.php';

includeClass(array('WarehouseLayout.class.php'));
 
 
// ====== UPDATE COA AMOUNT 

$warehouseLayout = new WarehouseLayout(); 
 
// echo str_pad($angka, 2, '0', STR_PAD_LEFT);

$arrGenerate = [
    // Dimensi 1: C
    'A' => [
        'kesamping' => 55,
        'kebawah' => 6,
        'tengah' => true
    ],
    'B' => [
        'kesamping' => 55,
        'kebawah' => 6,
        'tengah' => true
    ],
    'C' => [
        'kesamping' => 55,
        'kebawah' => 6,
        'tengah' => true
    ],
    'D' => [
        'kesamping' => 55,
        'kebawah' => 6,
        'tengah' => true
    ],
    'CH1' => [
        'kesamping' => 'N',
        'kebawah' => 10,
        'tengah' => false
    ],
    'CH3' => [
        'kesamping' => 40,
        'kebawah' => 8,
        'tengah' => true
    ],
    'GD2' => [
        'kesamping' => 'D',
        'kebawah' => 8,
        'tengah' => false
    ]
];

$arrDataCode = [];


foreach ($arrGenerate as $key => $data) {

    $initial = $key;
    if ($data['tengah']) {
        for ($angka = 1; $angka <= $data['kesamping']; $angka++) {
            $intialCenter = str_pad($angka, 2, '0', STR_PAD_LEFT);
            for ($i = 1; $i <= $data['kebawah']; $i++) { 
                $initialRight = $i ;
                $code = "$initial-$intialCenter-$initialRight";
                array_push($arrDataCode, $code);
            }
        }
    } else {
        $channels = range('A', $data['kesamping']);
        for ($angka = 0; $angka < count($channels); $angka++) {
            $intialCenter = $channels[$angka];
            for ($i = 1; $i <= $data['kebawah']; $i++) { 
                $initialRight = str_pad($i, 2, '0', STR_PAD_LEFT);
                $code = "$initial-$intialCenter-$initialRight";
                array_push($arrDataCode, $code);
            }
        }
    }
}

for ($angka = 41; $angka <= 72; $angka++) { 
   
    for ($i = 1; $i <= 7; $i++) { 
        $code = 'CH3' . '-' . $angka . '-' . $i;
        array_push($arrDataCode, $code);
    }
}

for ($i = 1; $i <= 24; $i++) { 
    $initialRight = str_pad($i, 2, '0', STR_PAD_LEFT);
    $code = 'GD2' . '-Z-' . $initialRight;
    array_push($arrDataCode, $code);
}

for ($i = 0; $i <= count($arrDataCode); $i++) { 

    $result = explode("-", $arrDataCode[$i]);

    switch ( $result[0]){  
        case 'A' :   $parentKey = '8157'; break;
        case 'B' :   $parentKey = '8158'; break;
        case 'C' :   $parentKey = '8159'; break;
        case 'D' :   $parentKey = '8160'; break;
        case 'CH1' :   $parentKey = '8161'; break;
        case 'CH3' :   $parentKey = '8162'; break;
        case 'GD2' :   $parentKey = '8163'; break;
    }

    $arrParam = array();	
    $arrParam['code'] = 'xxxxxx';
    $arrParam['name'] = $arrDataCode[$i];
    $arrParam['selCategory'] = $parentKey;
    $arrParam['selStatus'] = 1;
    $arrayToJs = $warehouseLayout->addData($arrParam); 
}
    
// $warehouseLayout->setLog($arrDataCode, true);

// $coa->setLog($data, true);

// print_r($data);

?>