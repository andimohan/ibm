<?php  
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass(array('CarServiceMaintenance.class.php'));
$carServiceMaintenance = createObjAndAddToCol(new CarServiceMaintenance()); 
  
$obj = $carServiceMaintenance;    

$fieldValue = $obj->tableName.'.code';

include 'ajax-general.php';

if (isset($_POST) && !empty($_POST['action'])) {
			switch ( $_POST['action']){ 
     
                case 'updateExecuteDate' :  

                    if (!isset($_POST) ||  empty($_POST['pkey']))  
                        die;

                    $result = $obj->updateExecuteDate($_POST['pkey'], $_POST['executeDate']);
 
                    echo json_encode($result);   
                    break;
                    
            }
}

die;
  
?>
