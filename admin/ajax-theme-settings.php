<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php';

if (isset($_POST) && !empty($_POST['action'])) {
        switch ( $_POST['action']){ 
            case 'update' :

                $settingkey = $_POST['settingkey'];
                $value = $_POST['value'];
                $class->updateThemeSettings($settingkey,$value);  
                break;


//				case 'getPropertiesValue':
//					if(!isset($_POST['widgetkey']) || empty($_POST['widgetkey'])) die; 
//                    $return = json_encode($widgetSetting->getPropertiesValue($_POST['widgetkey']));  
//					echo $return;
//					break;
        } 
}
die; 
?>