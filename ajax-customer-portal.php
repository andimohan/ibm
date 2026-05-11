<?php
require_once '_config.php'; 
require_once '_include-fe-v2.php';
require_once '_global.php';  

	 
includeClass(array('Customer.class.php', 'LoginLog.class.php'));
$customer = new Customer();
$loginLog = new LoginLog();

if (isset($_POST) && !empty($_POST['action'])) {

		foreach ($_POST as $k => $v) { 
			if (!is_array($v))
				 $v = trim($v);  

			$arr[$k] = $v;     
		}  

		$arrReturn = array();  

		switch ($_POST['action']) {


			case 'login' : 

							$companyCode=$_POST['companyCode'];

							//check dulu company code nya
							$psCon = $class->masterConn(); 
							$sql = 'select * from customer_company where statuskey = 2 and code ='.$class->oDbCon->paramString($companyCode);  
							$rsCompany = $psCon->doQuery($sql);  

							if(empty($rsCompany)) { 
								$class->addErrorList($arrReturn,false,$class->errorMsg[300]);
								break;
							}
				 
							$domainName = $rsCompany[0]['name'];
 				
							$customerConnection = newConnection($domainName); 
							$customer->oDbCon = $customerConnection;
							$loginLog->oDbCon = $customerConnection;
				

							$userName = $_POST['loginID'];
							$password = hash('sha256',md5($_POST['loginPassword']));

							// cari dari akun utama
							$resultMainAcc = $customer->searchdata('','',true,' and '.$customer->tableName.'.username = '.$customer->oDbCon->paramString($userName).' and '.$customer->tableName.'.password = '.$customer->oDbCon->paramString($password));
							$loginType = 1; // nanti diganti sesuai tipe login
						 
							// cari dari akun lainnya
							if(empty($resultMainAcc)){
								$resultMainAcc = $customer->loginAccountDetail($userName ,$_POST['loginPassword']);
								$loginType = $resultMainAcc[0]['rolekey'];
							}
							
							$arrReturn =  validateLogin($resultMainAcc,$loginType,$userName,$rsCompany);
 
							break;


		}; 

		echo json_encode($arrReturn);  
		die;  
}


function validateLogin($result,$loginType,$userName,$rsCompany){
        global $class;
		global $customer;
		global $loginLog;

		$arrLoginLog = array();
		$arrLoginLog['logintype'] = $loginType;
		$arrLoginLog['username'] = $userName;
		$arrLoginLog['statuskey'] = 2;  
		$arrLoginLog['userkey'] = 0 ;  


        $arrayToJs = array();
         
            if (count ($result) == 0 ){ 
                if (isset ($_SESSION[$class->loginSession]))
                    session_unset($_SESSION[$class->loginSession]);  
                $class->addErrorList($arrayToJs,false,$class->errorMsg[300]);

            }
    
            else if ($result[0]['statuskey'] == 1){
                $class->addErrorList($arrayToJs,false,$class->errorMsg['login'][1]);
            }
            else if ($result[0]['statuskey'] == 3 ){
                $class->addErrorList($arrayToJs,false,$class->errorMsg['login'][2]);
            }
            else if ($result[0]['statuskey'] == 2){
				
					// ambil informasi nama dan logo perusahaan

					// gk bisa pake load setting karena sudah ke cache
					$sql = 'select code,value from _setting,_user_setting where _setting.pkey = _user_setting.settingkey and  code in (\'companyName\',\'companyLogo\');';
					$rsCustomerSettings = $customer->oDbCon->doQuery($sql);
					$rsCustomerSettings = array_column($rsCustomerSettings,'value','code');
						
					$arrCompany = array();
					$arrCompany['companykey'] = $rsCompany[0]['pkey'];
					$arrCompany['domain'] = $rsCompany[0]['name'];
					$arrCompany['companyName'] = $rsCustomerSettings['companyName'];
					$arrCompany['companyLogo'] = $rsCustomerSettings['companyLogo'];
					
					$_SESSION[$class->loginSession]['customerCompany'] = $arrCompany;
				

                    $_SESSION[$class->loginSession]['id'] = base64_encode($result[0]['pkey']);
                    $_SESSION[$class->loginSession]['name'] = $result[0]['name']; 
                    $_SESSION[$class->loginSession]['username'] = $result[0]['username']; 
                    $_SESSION[$class->loginSession]['pass'] = $result[0]['password']; 
//                    $_SESSION[$class->loginSession]['email'] = $result[0]['email'];  
//                    $_SESSION[$class->loginSession]['gmt'] = $result[0]['gmt']; 
                    $_SESSION[$class->loginSession]['logintype'] = $loginType; 

					$class->addErrorList($arrayToJs,true,$class->lang['loginSuccessful']); 

					$arrLoginLog['statuskey'] = 1;  
					$arrLoginLog['userkey'] = $result[0]['pkey'] ;  
				
            }
    
    
	$loginLog->addData($arrLoginLog); 
	
    return $arrayToJs;
    
}



?>