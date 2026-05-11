<?php

// kosongkan array jika tdk ad block
$arrAllowedIP = array();
 
// 
//if(DOMAIN_NAME == 'logol.wintera.co.id')
//    $arrAllowedIP = (!IS_DEVELOPMENT) ? array('103.234.195.154','34.87.19.200','118.96.17.143', '103.29.187.27','10.1.19.27') : array();

if(!empty($arrAllowedIP) && !in_array($_SERVER['REMOTE_ADDR'],$arrAllowedIP)){
    $RETURN_VALUE = array();
    $RETURN_VALUE['response_code']  = 401;  
    $RETURN_VALUE['message'] = 'IP Not Allowed';
    http_response_code($RETURN_VALUE['response_code']); 
    echo json_encode($RETURN_VALUE); 
    die;   
}
 

$FILE_NAME = basename (  $_SERVER['PHP_SELF'] ,".php"); 

require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/BaseClass.class.php';  
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/AutoCode.class.php';     
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/CustomCode.class.php';   
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Employee.class.php';   
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/APILog.class.php';   
require_once DOC_ROOT. 'connections/_connection.php';     
 
$GLOBALS['oDbCon'] = new Database($rs[0]['dbusername'],$rs[0]['dbpass'],$rs[0]['dbname'],$host);
  
$class = new Baseclass();

//$class->setLog($_SERVER['REMOTE_ADDR'],true,'ip-log');

// load settings
$TABLEKEY_SETTINGS = $class->loadTableKeySettings();
define('TABLENAME_SETTINGS', array_column($TABLEKEY_SETTINGS,null,'tablename'));
define('TABLEKEY_SETTINGS', array_column($TABLEKEY_SETTINGS,null,'pkey'));

parse_str(file_get_contents("php://input"),$postVars);

// default login id 
// sementara pake ini utk lihat siapa yg update ketika import file
// nanti harus diganti, kirim user credential
if (isset($postVars['_userkey']) && !empty($postVars['_userkey']) ){ 
    //$_SESSION[$class->loginAdminSession]['id'] = $postVars['createdby'];
    $userkey = $postVars['_userkey']; // utk dilanjutkan ke _process.php utk auth
    unset($postVars['_userkey']);
}

$security = new Security();  
$employee = new Employee();  

// define plan configuration  
$PLAN_TYPE = $security->getUserPlanType();
$PLAN_TYPE = $PLAN_TYPE[0];   
define('PLAN_TYPE', $PLAN_TYPE);

$useGL = $class->loadSetting('useGL');
$useGL = ($useGL == 1) ? true : false;  
define('USE_GL', $useGL);

// default conversion 
// field ap saja yang boleh diakses oleh API
$API_FIELDS = array(
                     // tablefield =>                        
                        'pkey' => array('paramName' => 'key', 'updatable' => false),   
                        'code' => array('paramName' => 'code' ), // kalo ad nilai Kode di excel, dan gk ketemu, maka boleh diproses sebagai data baru, karena mungkin saja menggunakan data manual
                        // cek keoverwrite gk di master
                        // master jd gk bisa update, karena merge ny kita kebalik
                        //'statuskey' => array('paramName' => 'status_key', 'updatable' => false ),  // hanya master yg boleh diupdate status dari API,
                        'statusname' => array('paramName' => 'status_name', 'updatable' => false ), 
                        'createdon' =>  array('paramName' => 'created_on', 'updatable' => false , 'return' => array('format' => 'mktime')), 
                        'createdby' => array('paramName' => 'created_by', 'updatable' => false ), 
                        'modifiedon' => array('paramName' => 'modified_on', 'updatable' => false, 'return' => array('format' => 'mktime')), 
                        'modifiedby' => array('paramName' => 'modified_by', 'updatable' => false ),  
                    ); 

// konversi dari pkey ke code ketika GET
$RETURN_IN_CODE = array( 
               'createdby' =>  array('obj' => $employee), 
               'modifiedby' =>  array('obj' => $employee), 
            );

?>