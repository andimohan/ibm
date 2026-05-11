<?php
require_once '_config.php'; 
require_once '_include-fe-v2.php';
require_once '_global.php';

includeClass(array('Recruitment.class.php'));
$recruitment = new Recruitment();

if (isset($_POST) && !empty($_POST['action'])) {

        foreach ($_POST as $k => $v) { 
            if (!is_array($v))
                 $v = trim($v);  

            $arr[$k] = $v;     
        }  

        $arrReturn = array();  

        switch ($_POST['action']) { 
            case 'apply':
 
                $arr['fromFE'] = '1';
                $arr['code'] = 'xxxxx'; 
                $arr['hidJobKey'] = $_POST['inputHidJobKey'];
                $arr['hidSaveAndProceed'] = 1;
                $arr['trDate'] = date('d / m / Y H:i');
                $arr['selStatus'] = 1;

                $arrReturn = $recruitment->addData($arr);
        

                break;
        } 
 
    
    echo json_encode($arrReturn);  
    die;  
}

	 
?>