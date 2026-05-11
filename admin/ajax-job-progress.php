<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php';  

includeClass('JobProgress.class.php');
$jobProgress = createObjAndAddToCol(new JobProgress());

$obj = $jobProgress;   

$arrCriteria = array();  
array_push ($arrCriteria, $obj->tableName.'.statuskey = 1'); 

include 'ajax-general.php';

if (isset($_GET) && !empty($_GET['action'])) {
    switch ($_GET['action']) {
        case 'getJobProgressForWorkOrder' :

            if(!isset($_GET['categorykey']) || empty($_GET['categorykey'])) {
                die;
            }

            $result = $obj->getJobProgressByCategory($_GET['categorykey']);
           
            echo json_encode($result); 
        break; 
    }
}

die;

?>