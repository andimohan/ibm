<?php
require_once '../../_config.php';  
require_once '../../_include-fe-v2.php';
require_once '../../_global.php';  // perlu utk obj $twig utk kirim email

includeClass(array('Customer.class.php','LoginLog.class.php'));
$customer = new Customer();
$loginLog = new LoginLog();
 
require_once '../../assets/vendor/autoload.php';

$arrSSO = array();
$arrSSO['google'] = array( 'prefix' => 'G', 'typekey' => 1);
$arrSSO['facebook'] = array( 'prefix' => 'F', 'typekey' => 2);
            

switch(SSO_DOMAIN){
    case 'google' : $appId = $class->loadSetting('googleOAuthId');  
                    $authResponse = attemptAuth($appId);
                    break; 
    case 'facebook' : $appId = $class->loadSetting('FBAppID');
                    $secretKey = $class->loadSetting('FBSecretKey');
                    $authResponse = attemptAuth($appId,$secretKey);
                    break; 
}

$authResponse = json_decode($authResponse,true);

if($authResponse['valid']){
    $email = $authResponse['data']['email'];
    $sub = $authResponse['data']['sub']; // utk id / username
    $name = $authResponse['data']['name'];

    $rsCustomer = $customer->searchDataRow(array($customer->tableName.'.pkey',$customer->tableName.'.name',$customer->tableName.'.username',$customer->tableName.'.password',$customer->tableName.'.email'),
                                           ' and '.$customer->tableName.'.email='.$class->oDbCon->paramString($email));

    if(empty($rsCustomer)){
        // auto register
        
        $pasword = md5($sub.$email);
        
        $arrParam = array();
        $arrParam['fromFE'] = 1;
        $arrParam['mnv-OAuth'] = 1; // agar bypass agreement dan captcha
        $arrParam['code'] = $sub; // nanti akan dioverwrite jg kalo pake autocode
        $arrParam['userName'] = $arrSSO[SSO_DOMAIN]['prefix'].$sub;
        $arrParam['password'] = $pasword; // gk perlu tau jg
        $arrParam['passwordConfirmation'] = $pasword;
        $arrParam['name'] = $name;
        $arrParam['email'] = $email; 
        $arrParam['hidSSOTypeKey'] =$arrSSO[SSO_DOMAIN]['typekey'];

        $response  = $customer->addData($arrParam); 
        $response = $response[0];
        
        // kalo berhasil, otomatis aktivasi 
        if(!$response['valid'])
            die ($response['message']); 
         
        $rsCustomer = array();
        $rsCustomer[0] = $response['data']; 
        $response = $customer->changeStatus($rsCustomer[0]['pkey'],2,'','',true); 
    } 

    // fake login 
    $_SESSION[$class->loginSession]['id'] = base64_encode($rsCustomer[0]['pkey']);
    $_SESSION[$class->loginSession]['name'] = $rsCustomer[0]['name']; 
    $_SESSION[$class->loginSession]['username'] = $rsCustomer[0]['username']; 
    $_SESSION[$class->loginSession]['pass'] = $rsCustomer[0]['password']; 
    $_SESSION[$class->loginSession]['email'] = $rsCustomer[0]['email'];  
    $_SESSION[$class->loginSession]['gmt'] = $rsCustomer[0]['gmt'];  
    $_SESSION[$class->loginSession]['logintype'] = 1;
    
    $arrLoginLog = array();
    $arrLoginLog['logintype'] = 1;
    $arrLoginLog['userkey'] =  $rsCustomer[0]['pkey']; 
    $arrLoginLog['username'] =  $rsCustomer[0]['username']; 
    $arrLoginLog['statuskey'] = 1;  
 
    $loginLog->addData($arrLoginLog); 
    header('Location: /index');
 
    // cek kalo blm terdaftar emailnya, auto register, pake sub sebagai password, kalo blm terdaftar emailnya.
    // kalo sudah terdaftar, fake login pake id sebenarnya

}
?>