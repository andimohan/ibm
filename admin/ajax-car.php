<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php';  

includeClass('Car.class.php');
$car = createObjAndAddToCol(new Car());

$obj = $car;  

$fieldValue = $obj->tableName.'.policenumber';
 
$arrCriteria = array();  
array_push ($arrCriteria, $obj->tableName.'.statuskey = 1');  

include 'ajax-general.php';
 
if (isset($_GET) && !empty($_GET['action'])) {
    switch ($_GET['action']) {
        case 'getCarItemLastSNForMaintenance':

            if (empty($_GET['itemkey']) || empty($_GET['carkey']) || empty($_GET['positionkey']))
                die;

            $carkey = $_GET['carkey'];
            $itemkey = $_GET['itemkey'];
            $positionkey = $_GET['positionkey'];

            $rsResult = $obj->getCarItemLastSN($carkey, $itemkey, $positionkey);
            $obj->setLog($rsResult, true);
            echo json_encode($rsResult);
            break;
    }
}

if (isset($_POST) && !empty($_POST['action'])) {
	switch ($_POST['action']){
       
        // gk kepake, karena kalo gk ad pekerjana, history GPS nya kosong utk tgl tersebut
        //case 'getGPSMileage' : 
        //           
        //        //if (empty($_POST['registrationNumber'])) die;
        //        
        //        // harus panggil update sql jg
        //        // kedepan dicoba bisa tdk bebrapa element sekaligus
        //        $arrRegistrationNumber = $_POST['registrationNumber'];
        //        //if(!is_array($arrRegistrationNumber))
        //        //  $arrRegistrationNumber = array($arrRegistrationNumber);
        //
        //        $rsResult = $obj->getGPSMileage(array('startDate'=>$_POST['startDate'], 'endDate' =>$_POST['endDate'] , 'registrationNumber' => $arrRegistrationNumber )); 
        //        echo json_encode($rsResult); 
        //        break;
                    
            case 'getMileage':
                if (empty($_POST['carkey'])) die; 
                $carkey = $_POST['carkey']; 
        
                $rsResult = $obj->getMileage($_POST['trDate'], $carkey); 
                echo json_encode($rsResult); 
                break;
                    
    }
}

die;
  
?>