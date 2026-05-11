<?php
require_once '../../_config.php';  
require_once '_include.php';
 
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/LoginLog.class.php';    

$OBJ = $class; 
$loginLog =  new LoginLog() ; 

require_once '_global.php';

if($ACTION != 'POST') endForRequestMethodError();

$hasSuccessValue = false;
$arrFailed = array();
$ARR_RETURN_VALUE = array();

for($i=0;$i<$totalPostVars;$i++){
    $postVars = $arrPostVars[$i];
	
    $username = $postVars['username'];
    $password = $postVars['password'];

    if ($loginLog->isLockout($username,2)){ 
        $lockoutMinutes =  ceil($class->loadSetting('lockoutSecond') / 60); 
        $errorMsg = $class->errorMsg['login'][3];
    
        $patterns = array();
        $patterns[count($patterns)] = '/({{LOCKOUT_MINUTES}})/'; 
    
        $replacement = array();
        $replacement[count($replacement)] =$lockoutMinutes; 
    
        $errorMsg = preg_replace($patterns, $replacement, $errorMsg);  

        addFailedRows($arrFailed, array(
                'username' => $username,
                'message' => $errorMsg,
            )
        );
		
		// agar tetep ad error message
		$RETURN_VALUE['message'] =  $errorMsg; 
        
        continue;
    }
    
    $arrLoginLog = array('logintype' => 2, 'username' => $username, 'statuskey' => 2, 'userkey' => 0);
    
	$result = $security->adminLogin($username,$password);

	if ( $result['valid'] == false){  
		$RETURN_VALUE['response_code']  = 401;  
		$RETURN_VALUE['message'] =  $result['message'];  
	}else{ 
		$hasSuccessValue = true;
		
		$arrLoginLog['statuskey'] = 1 ;  
		$arrLoginLog['userkey'] = $result['data']['pkey'] ;  

		$RETURN_VALUE['response_code']  = 200;
		$RETURN_VALUE['message'] = '';  
		$RETURN_VALUE['data'] = array(
									'pkey' => $result['data']['pkey'], 
									'code' => $result['data']['code'], 
									'user_real_name' => $result['data']['name'],
									'username' => $result['data']['username'],
									'password' => $result['data']['password'],
									'is_driver' => $result['data']['isdriver'],
								);  
	}		

    
    $loginLog->addData($arrLoginLog);
}

$RETURN_VALUE['response_code'] = ($hasSuccessValue) ? 200 : 409;
$RETURN_VALUE['success_rows'] = count($ARR_RETURN_VALUE);
$RETURN_VALUE['success_data'] = $ARR_RETURN_VALUE;
$RETURN_VALUE['failed_rows'] = count($arrFailed);
$RETURN_VALUE['failed_data'] = $arrFailed;

http_response_code($RETURN_VALUE['response_code']); 
echo json_encode($RETURN_VALUE);
die;
?>