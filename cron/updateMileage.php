<?php

require_once '../_config.php';
require_once "../_include-v2.php"; 

includeClass(array('Car.class.php')); 
$car = new Car();

// format date Y-m-d
$date = (isset($_GET['date']) && !empty($_GET['date'])) ? $_GET['date'] : date('Y-m-d'); 
$init = (isset($_GET['init']) && !empty($_GET['init'])) ? $_GET['init'] : 0; 

$rsCar = $car->searchDataRow(array($car->tableName.'.policenumber'), ' and '.$car->tableName.'.statuskey in (1)' );
$arrRegistrationNumber = array_column($rsCar,'policenumber');

$startDate = $date;
$endDate = $date;

$startDate = ($init == 1) ? '2010-01-01' : $date;
$endDate =  $date;


//$arrRegistrationNumber = array('B 9152 BXU');
        
// convert ke format d / m / Y  agar kedepannya kalo perlu bisa compatible dengan form
$startDate =   $car->formatDBDate($startDate);
$endDate =   $car->formatDBDate($endDate);
$car->updateMileage(array('startDate'=> $startDate, 'endDate' =>$endDate, 'registrationNumber' => $arrRegistrationNumber));

echo 'done';
?>