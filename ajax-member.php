<?php
require_once '_config.php'; 
require_once '_include-fe-v2.php';
require_once '_global.php';  
	 
includeClass(array('Customer.class.php', 'LoginLog.class.php'));
$customer = new Customer();
$loginLog = new LoginLog();
 
	if (isset($_POST) && !empty($_POST['type'])) {
		
		 $isAvailable = true;
		if ( $_POST['type'] == 'check' ){
			switch ($_POST['fieldtype']) {
				case 'email':
					$email = $_POST['email'];
                    $isEdit = (isset($_POST['edit']) && $_POST['edit'] == 1) ? true : false;
                        
                    $userkey = ($isEdit) ? base64_decode($_SESSION[$customer->loginSession]['id']) : '';    
					$rsEmail = $customer->isValueExisted($userkey,'email',$email);
					// Check the email existence ...
					if(count ($rsEmail) <> 0)
						$isAvailable = false;
					break;
		
				case 'email-negation':
					$email = $_POST['email'];
					$rsEmail = $customer->isValueExisted('','email',$email);
					// Check the email existence ...
					if(count ($rsEmail) == 0)
						$isAvailable = false;
						
					break;
		
				case 'username': 
					$userName = $_POST['userName'];
					$rsUserName = $customer->isValueExisted('','username',$userName);
					// Check the username existence ...
					if(count ($rsUserName) <> 0)
						$isAvailable = false;
					break;
					
				case 'checkPassword':  
					$username = $_SESSION[$class->loginSession]['username'];
					$password = $_POST['currentPassword'];
					 
					$isAvailable  = $customer->checkPassword(USERKEY,$username,$password);
				 
					break;
			}
		
			echo json_encode(array(
				'valid' => $isAvailable,
			)); 
			die; 
		} 
		
	}	
	
	 
	if (isset($_POST) && !empty($_POST['action'])) {
		
			foreach ($_POST as $k => $v) { 
				if (!is_array($v))
					 $v = trim($v);  
				
				$arr[$k] = $v;     
			}  
			 
			$arrReturn = array();  
			
			switch ($_POST['action']) {
				case 'add':
						
								$useEmailActivation = $customer->loadSetting('useEmailActivation');
					
								$arr['code'] = 'XXXXX';
								$arr['createdBy'] = 0;
								$arr['selStatus'] = ($useEmailActivation == 1) ? 1 : 2;   
								$arr['hidCityKey'] = 0; 
								$arr['selTermOfPayment'] = 0; 
								$arr['frontendRegistration'] = 1;  
								$arr['fromFE'] = 1;
                    
                                // kalo ad contact darurat, sementatra masukin aj ke contact person
                                if(isset($arr['emergencyContactName']) && !empty($arr['emergencyContactName']) && isset($arr['emergencyContactPhone']) && !empty($arr['emergencyContactPhone'])){
                                    $arr['hidContactPersonDetailKey'][0] = 0;
                                    $arr['cpPosition'][0] = $class->lang['emergencyContact'];
                                    $arr['cpName'][0] = $arr['emergencyContactName'];
                                    $arr['cpPhone'][0] = $arr['emergencyContactPhone']; 
                                }
                     
					
								if($class->isActiveModule('MembershipSubscription')){ 

									includeClass(array('MembershipLevel.class.php','MembershipSubscription.class.php'));
									$membershipLevel = new MembershipLevel();
									$membershipSubscription = new MembershipSubscription();

									$basicMembership = $membershipLevel->getDefaultData();
									$membershipLevelJoined = $arr['selMembership'] ;
									
									// kalo ad membership, selalu aktifkan yg basic dulu
									$arr['selMembership'] = $basicMembership;
									$arr['_mnv-joined-membership'] = $membershipLevelJoined;	  
								}
					
								$arrReturn = $customer->addData($arr); 
								break;
				
				case 'edit' :	 
					
								if(empty(USERKEY)) die;
					
								$username = $_SESSION[$class->loginSession]['username'];
								$password = $_POST['currentPassword'];
								 
								$rsCust = $customer->getDataRowById(USERKEY); 
					
								$arr['code'] = $rsCust[0]['code'];
								$arr['fromFE'] = 1;
								$arr['chkAgree'] = 1;
								$arr['modifiedBy'] = 0;
								$arr['mnv-OAuth'] = ($rsCust[0]['ssotypekey'] > 0) ? 1 : 0; // kalo mau nilai ssokey, nanti kirim beda variable saja
                                   
                                // kalo ad contact darurat, sementatra masukin aj ke contact person
                                if(isset($arr['emergencyContactName']) && !empty($arr['emergencyContactName']) && isset($arr['emergencyContactPhone']) && !empty($arr['emergencyContactPhone'])){
                                    $arr['hidContactPersonDetailKey'][0] = 0;
                                    $arr['cpPosition'][0] = $class->lang['emergencyContact'];
                                    $arr['cpName'][0] = $arr['emergencyContactName'];
                                    $arr['cpPhone'][0] = $arr['emergencyContactPhone']; 
                                }
                     
								if($arr['mnv-OAuth'] == 1 || $customer->checkPassword(USERKEY,$username,$password)){ 
									$arrReturn = $customer->editData($arr);
									  
									if($class->isActiveModule('activityLog')){  
										includeClass(array('ActivityLog.class.php'));
										
										$arrActivityLog = array();
										array_push($arrActivityLog, 
																array( 
																		'modulekey' => 4,
																		'templatekey' => 17, 
																		'refkey' => USERKEY,  
																	) 
														); 

										$activityLog = new ActivityLog();
										$activityLog->addNewLog($arrActivityLog);  
									}

								}else{ 
									$class->addErrorList($arrReturn,false,$class->errorMsg[302]);
								}
								break; 
								
				case 'login' : 
                    
                                $tempCart = $_SESSION[$class->loginSession]['cart'];
                    
								$userName=$_POST['loginID'];
								$password=$_POST['loginPassword']; 
								 
								if ($loginLog->isLockout($userName,1)){ 
										$lockoutMinutes =  ceil($class->loadSetting('lockoutSecond') / 60); 
										$errorMsg = $class->errorMsg['login'][3];
										
										$patterns = array();
										$patterns[count($patterns)] = '/({{LOCKOUT_MINUTES}})/'; 
										
										$replacement = array();
										$replacement[count($replacement)] =$lockoutMinutes; 
										 
										$errorMsg = preg_replace($patterns, $replacement, $errorMsg); 
										
										$class->addErrorList($arrReturn,false,$errorMsg);
								 	    break;
							 	} 
								
								$arrLoginLog = array();
								$arrLoginLog['logintype'] = 1;
                                $arrLoginLog['userkey'] =  ''; 
								$arrLoginLog['username'] = $userName;
								$arrLoginLog['statuskey'] = 2; 
								
									  
								$result = $customer->memberLogin($userName,$password); 
								
								if (count ($result) == 0){ 
									if (isset ($_SESSION[$class->loginSession]))
										session_unset($_SESSION[$class->loginSession]);  
									$class->addErrorList($arrReturn,false,$class->errorMsg[300]);
								 
								}
								
								else if ($result[0]['statuskey'] == 1){
									$class->addErrorList($arrReturn,false,$class->errorMsg['login'][1]);
								}
								else if ($result[0]['statuskey'] == 3){
									$class->addErrorList($arrReturn,false,$class->errorMsg['login'][2]);
								}
								else if ($result[0]['statuskey'] == 2){
									 
										$_SESSION[$class->loginSession]['id'] = base64_encode($result[0]['pkey']);
										$_SESSION[$class->loginSession]['name'] = $result[0]['name']; 
										$_SESSION[$class->loginSession]['username'] = $result[0]['username']; 
										$_SESSION[$class->loginSession]['pass'] = $result[0]['password']; 
										$_SESSION[$class->loginSession]['email'] = $result[0]['email'];  
                                        $_SESSION[$class->loginSession]['gmt'] = $result[0]['gmt'];  
                                        $_SESSION[$class->loginSession]['logintype'] = 1;  
										 
										$class->addErrorList($arrReturn,true,$class->lang['loginSuccessful']); 
										
                                    
                                        if($class->isActiveModule('SalesOrder')){
                                            includeClass(array('SalesOrder.class.php'));  
                                            $salesOrder = new SalesOrder();
                                            $salesOrder->retrieveAbandonedCart(); 
                                        }
                                            
                                        $arrLoginLog['userkey'] =  $result[0]['pkey']; 
										$arrLoginLog['statuskey'] = 1;  
                                     
								}	 
                    
								$loginLog->addData($arrLoginLog); 
								
								break;
								
				case 'recover-account' :	  
								$arrReturn = $customer->requestRecoverAccount($arr);
								break;
								
				case 'resend-activation' :	 
								$arrReturn = $customer->resendActivationEmail($arr);
								break;
                    
                case 'update-password':
								if(empty(USERKEY)) die;
								$arr['hidUserKey'] = USERKEY;
								$arrReturn = $customer->updatePassword($arr);
								break;
			
			     case 'get-point': 
								if(empty(USERKEY)) die;
								$rs = $customer->searchDataRow(array($customer->tableName.'.point'),
																' and '.$customer->tableName.'.pkey = '. $class->oDbCon->paramString(USERKEY) 
															  );
					
								$pointValue = $class->loadSetting('rewardsPointUnitValue');
								$arrReturn = array(
										'eligiblePoint' => $rs[0]['point'],
										'pointUnitValue' => $pointValue
								);
						
								break;
			
				case 'upgrade-membership' :
								if(empty(USERKEY)) die;
					
								includeClass(array('Warehouse.class.php','MembershipSubscription.class.php','MembershipLevel.class.php'));
								$warehouse = new Warehouse();
								$membershipSubscription = new MembershipSubscription();
								$membershipLevel = new MembershipLevel();
					
								$arr = array(); 

								foreach ($_POST as $k => $v) {
									$arr[$k] = $v; 
								}

								$customerkey = USERKEY; 
								//$customerkey = (!empty(USERKEY)) ? USERKEY : 1;
								
								$rsCustomer = $customer->searchDataRow(array($customer->tableName.'.point',$customer->tableName.'.membershiplevel',$customer->tableName.'.canusepoint'),' and '.$customer->tableName.'.pkey = ' . $customer->oDbCon->paramString($customerkey));
								
								// sementara urutkan berdasarkan pkey saja dulu 
								$rsMembershipLevel = $membershipLevel->searchDataRow(array($membershipLevel->tableName.'.pkey', $membershipLevel->tableName.'.sellingprice'),' and '.$membershipLevel->tableName.'.statuskey = 1',' order by '.$membershipLevel->tableName.'.pkey asc');
								
								$nextMembershipLevel = array();
								for($i=0;$i<count($rsMembershipLevel);$i++){ 
									if($rsMembershipLevel[$i]['pkey'] == $rsCustomer[0]['membershiplevel']){
										$nextMembershipLevel = (!empty($rsMembershipLevel[$i+1])) ? $rsMembershipLevel[$i+1] : array(); 
										break;
									}
								}
					  
								if (empty($nextMembershipLevel)) break; // sudah mentok membershipnya
									
								$rewardPointValue = $customer->loadSetting('rewardsPointUnitValue');
						 
								$membershipPrice = $nextMembershipLevel['sellingprice'];
								$arr['hidCustomerKey'] = $customerkey;

								$arr['code'] = 'xxxxxx';    
								$arr['trDate'] = date('d / m / Y');

								$rsWarehouse = $warehouse->searchData($warehouse->tableName.'.statuskey',1,true,' order by pkey asc'); 
								$arr['selWarehouseKey'] =  $rsWarehouse[0]['pkey']; 
  
								$arr['isPriceIncludeTax'] = 0;
								$arr['taxPercentage'] = 0;
								$arr['taxValue'] = 0;  
								$arr['etcCost'] = 0;
								$arr['createdBy'] = 0; 
								$arr['selTermOfPaymentKey'] = 1; 
								$arr['paymentMethodValue'] = 1;
								$arr['paymentMethodKey'] = 1;   
								$arr['point'] = ($arr['hidUsePoint'] == 1) ? ceil($membershipPrice/$rewardPointValue) : 0;  
								$arr['selMembershipLevel'] = $nextMembershipLevel['pkey']; 
								$arr['fromFE'] = 1; 			
									
								// lupa utk apa
								//$arrayToJs = array(); 
								//$class->addErrorList($arrayToJs,false, $class->errorMsg['cart'][1]); 
   	 
								$arrReturn = $membershipSubscription->addData($arr);
					
								// kalo pake point langsung konfirmasi
								// kalo kurang gk masalah, nanti akaan divalidasi oleh jumlah payment
								if($arr['point'] > 0)
									$membershipSubscription->changeStatus($arrReturn[0]['data']['pkey'], 2, '', false, true);
								
					
								break;
//					
//				case 'privacy-settings' :
//								  
//								if(empty(USERKEY)) die;
//					
//								$arr = array(); 
//
//								foreach ($_POST as $k => $v) {
//									$arr[$k] = $v; 
//								}
//								 
//								$arr['hidId'] = USERKEY; 
//								$arrReturn = $customer->updatePrivacy($arr);  
//								break;
						
				case 'update-settings' :
								
                                // gk bis acek login karen gk ad komponen password
                    
								if(empty(USERKEY)) die;
					           
                                $arr = array();  
                                foreach ($_POST as $k => $v)   $arr[$k] = $v;  

                                $arr['hidId'] = USERKEY; 
                                $arrReturn = $customer->updateSettings($arr);  // utk lang, timezone, dsb 
                    
								break;
			 
				case 'get-pending-subscription' :
						
							if(empty($_POST['userkey'])) die;
							
							$arrReturn = $customer->getPendingSubscription();
							
							break;
					
				case 'update-bank-information' :
							if(empty(USERKEY)) die;
							 
							$username = $_SESSION[$class->loginSession]['username'];
							$password = $_POST['currentPassword'];
					
							if($arr['mnv-OAuth'] == 1 || $customer->checkPassword(USERKEY,$username,$password)){  
								$arr['pkey'] = USERKEY;
								$arrReturn = $customer->updateBankInformation($arr);

							}else{ 
								$class->addErrorList($arrReturn,false,$class->errorMsg[302]);
							}

							break;
						
                case 'searchMember' :
                        // sementara khusus icommunity
                        $keyword = $_POST['keyword'];
                        $criteria = array();
                    
                        array_push($criteria, $customer->tableName.'.name like ' .$customer->oDbCon->paramString('%'.$keyword.'%'));
                        array_push($criteria, $customer->tableName.'.companyname like ' .$customer->oDbCon->paramString('%'.$keyword.'%'));
                        array_push($criteria, $customer->tableJobPosition.'.name like ' .$customer->oDbCon->paramString('%'.$keyword.'%'));

                        $limit = ' limit 0,6 ';
                        $rs = $customer->searchData('','',true,' and '.$customer->tableName.'.statuskey = 2 and (' . implode(' OR ', $criteria).')','  ORDER BY RAND() ',$limit );
                    
                        $arrReturn = array(); 
                        for($i=0;$i<count($rs);$i++) { 
                            $arrReturn[$i]['pkey'] = $rs[$i]['pkey'];
                            $arrReturn[$i]['name'] = $rs[$i]['name'];
                            $arrReturn[$i]['code'] = $rs[$i]['code'];
                            $arrReturn[$i]['photofile'] = (!empty($rs[$i]['photofile'])) ? $rs[$i]['photofile'] : '';
                            $arrReturn[$i]['jobpositionname'] = (!empty($rs[$i]['jobpositionname'])) ? $rs[$i]['jobpositionname'] : '';
                            $arrReturn[$i]['companyname'] = (!empty($rs[$i]['companyname'])) ? $rs[$i]['companyname'] : '';
                            $arrReturn[$i]['photohash'] = getPHPThumbHash($rs[$i]['photofile']); 
                        }
                     
                    
                        break;

				case 'edit-multi-address' : 	
					if(empty(USERKEY)) die;
					
					$arr = array();  
					$arrParam = array();  
					foreach ($_POST as $k => $v)   $arr[$k] = $v;  

					$arrParam['pkey'] = $arr['hidDetailKey'][0];
					$arrParam['name'] = $arr['maName'][0];
					$arrParam['address'] = $arr['maAddress'][0];
					$arrParam['trDesc'] = $arr['maTrDesc'][0];
					$arrParam['zipcode'] = $arr['maZipCode'][0];
					$arrParam['pic'] = $arr['maPIC'][0];
					$arrParam['phone'] = $arr['maPhone'][0];
					$arrParam['hidUserKey'] = USERKEY;
					$arrParam['hidLatLng'] = $arr['hidLatLngEdit'][0];
					$arrParam['chkIsPrimary'] = $arr['maPrimary'][0];
					 
					$arrReturn = $customer->editMultiAddress($arrParam);  
					
					break;

				case 'add-multi-address' :
							
					if(empty(USERKEY)) die;
					
					$arr = array();  
					$arrParam = array();  
					foreach ($_POST as $k => $v)   $arr[$k] = $v;  
  
					$arrParam['name'] = $arr['maName'][0];
					$arrParam['address'] = $arr['maAddress'][0];
					$arrParam['zipcode'] = $arr['maZipCode'][0];
					$arrParam['trDesc'] = $arr['maTrDesc'][0];
					$arrParam['pic'] = $arr['maPIC'][0];
					$arrParam['phone'] = $arr['maPhone'][0];
					$arrParam['hidUserKey'] = USERKEY;
					$arrParam['hidLatLng'] = $arr['hidLatLngAdd'][0];
					$arrParam['chkIsPrimary'] = $arr['maPrimary'][0];
					 
					$arrReturn = $customer->addMultiAddress($arrParam); 
		
					break;

				case 'delete-multi-address' :
					if (!isset($_POST['pkey']) || empty(USERKEY))
						die;

					$arrReturn = $customer->delMultiAddress($_POST['pkey']);  
		
					break;
                    
				case 'get-shipment-address' :
					if (!isset($_POST['pkey']) || empty(USERKEY)) die; 
					$arrReturn = $customer->getMultipleAddress(USERKEY,1,'',' and '.$customer->tableMultipleAddress.'.pkey = '.$customer->oDbCon->paramString($_POST['pkey']));  
		
					// tambah informasi nama, telp dan email member sementara
					$rsCust = $customer->getDataRowById(USERKEY);
					//$arrReturn[0]['pic'] = $rsCust[0]['name'];
					//$arrReturn[0]['phone'] = $rsCust[0]['mobile'];
					$arrReturn[0]['email'] = $rsCust[0]['email'];
					
					break;
                    
			    /* case 'checkin': 
                                if(!$security->isMemberLogin(false)){
                                    $userName=$_POST['userId'];
                                    $password=$_POST['userPassword']; 

                                    $arrReturn = array();

                                    $rsCust = $customer->memberLogin($userName,$password);  
                                    if (count ($rsCust) == 0) {
                                        $class->addErrorList($arrReturn,false,$class->errorMsg['checkIn'][1]);
                                        break;
                                    }
                                    
                                    $userkey = $rsCust[0]['pkey'];
                                }else{
                                    $userkey = USERKEY;
                                }
                    
                                $rsMember = $customerMembership->searchData('','',true,'and '.$customerMembership->tableName.'.customerkey ='.$customerMembership->oDbCon->paramString($userkey).' and '.$customerMembership->tableName.'.statuskey = 2', 'order by '.$customerMembership->tableName.'.pkey asc');

                                $arr['code'] = 'xxxxx'; 
                                $arr['hidCustomerKey'] = $userkey;
                                $arr['selCustomerMembership'] = $rsMember[0]['pkey']; 
                                $arr['hidSaveAndProceed'] = 1;
                                $arr['trDate'] = date('d / m / Y H:i');
                    
                                $arr['selStatus'] = 1;

                                $arrReturn = $membershipAttendance->addData($arr);
                    
                                if($arrReturn[0]['valid']){
                                    // reset ulang, karena kalo konfirmasi otomatis ad keluar beberapa messge
                                    $arrReturn = array();
                                    $arrReturn[0]['valid'] = true;
                                    $arrReturn[0]['message'] = $class->lang['checkInSuccessful'];
                                } 
                                 
                               
			                    break;*/
			}; 
			
			echo json_encode($arrReturn);  
			die;  
	}
	
	 
?>
