<?php

class Employee extends BaseClass{
 
   function __construct(){
		
		parent::__construct();
       
		$this->tableName = 'employee';
		$this->tableCategory = 'employee_category'; 
		$this->tableStatus = 'employee_status';
		$this->tableCompany = 'company'; 
        $this->tableWarehouse = 'warehouse';
        $this->tableCustomer = 'customer';
        $this->tableCOA = 'chart_of_account';
        $this->tablePaymentMethod = 'payment_method';
		$this->tableEmployeeCompany = 'employee_detail_company';
		$this->tableEmployeeCustomer = 'employee_detail_customer';
		$this->tableEmployeeWarehouse = 'employee_detail_warehouse';
		$this->tableEmployeeCommission = 'employee_detail_commission';
		$this->tableEmployeeSales = 'employee_detail_sales';
        $this->tableEmployeePaymentMethod = 'employee_detail_payment_method';
		$this->tableCity = 'city';
		$this->tableImageID = 'employee_image'; // gk boleh pake tableImage, akan otomatis narik jadiny nanti di baseclass
		$this->tableCityCategory = 'city_category'; 
		$this->tableSecurityAccess = 'security_access';	  
        $this->tableContact = 'contact_person';
        $this->tableService = 'item';	  	  
        $this->tableCOAAccess = 'user_coa_access'; 
        $this->tableMaritalStatus = 'marital_status'; 
        $this->tableSex = '_sex'; 
        $this->uploadFolder = 'employee/';
        $this->uploadPhotoFolder = 'employee-photo/';
        $this->uploadSignatureFolder = 'employee-signature/';
		$this->securityObject = 'Employee';	    
		$this->securityPrivilegesObject = 'SecurityPrivileges';	   
        $this->arrCommission = array();  
        $this->arrCommission['pkey'] = array('hidDetailKey');
        $this->arrCommission['refkey'] = array('pkey', 'ref');  
        $this->arrCommission['servicekey'] = array('hidServiceKey', array('mandatory'=>true)); 
        $this->arrCommission['commission'] = array('employeeCommission','number');

        $arrDetails = array();
        array_push($arrDetails, array('dataset' => $this->arrCommission, 'tableName' => $this->tableEmployeeCommission));
       
        $this->arrData = array(); 
        $this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails)); 
        $this->arrData['code'] = array('code'); 
	   	$this->arrData['attendanceid'] = array('attendanceID');  
        $this->arrData['categorykey'] = array('selCategory');
        $this->arrData['warehousekey'] = array('selWarehouse');
        $this->arrData['name'] = array('memberName');
        $this->arrData['livingaddress1'] = array('livingAddress1');
        $this->arrData['livingaddress2'] = array('livingAddress2');
        $this->arrData['address1'] = array('memberAddress1');
        $this->arrData['address2'] = array('memberAddress2');
        $this->arrData['citykey'] = array('hidCityKey');
        $this->arrData['zipcode'] = array('memberZipCode');
        $this->arrData['phone'] = array('memberPhone');
        $this->arrData['mobile'] = array('memberMobile');
        $this->arrData['email'] = array('memberEmail');
        $this->arrData['isdriver'] = array('chkIsDriver');
        $this->arrData['issales'] = array('chkIsSales');
        $this->arrData['placeofbirth'] = array('hidPlaceOfBirthKey');
        $this->arrData['dateofbirth'] = array('dateOfBirth', 'date');
        $this->arrData['drivinglicense'] = array('drivingLicense');
        $this->arrData['drivinglicenseexpdate'] = array('drivingLicenseExpDate', 'date');
        $this->arrData['idnumber'] = array('IDNumber');
        $this->arrData['taxid'] = array('taxid');
        $this->arrData['religionkey'] = array('religion');
        $this->arrData['nationality'] = array('nationality');
        $this->arrData['maritalstatuskey'] = array('maritalStatus');
        $this->arrData['sexkey'] = array('sex');
        $this->arrData['statuskey'] = array('selStatus');
        $this->arrData['password'] = array('memberPassword');  
        $this->arrData['username'] = array('memberUserName');
        $this->arrData['secretAuth'] = array('secretAuth');
        $this->arrData['cashbankcoakey'] = array('hidCashBankCOAKey');
        $this->arrData['commissionapcoakey'] = array('hidCommissionAPCOAKey');
        $this->arrData['arcoakey'] = array('hidARCOAKey');
        $this->arrData['apcoakey'] = array('hidAPCOAKey');
        $this->arrData['photofile'] = array('photoFile');
        $this->arrData['signaturefile'] = array('signatureFile');
        $this->arrData['allwarehouseaccess'] = array('chkAllWarehouseAccess');
        $this->arrData['allcustomeraccess'] = array('chkAllCustomerAccess');
        $this->arrData['allcoaaccess'] = array('chkAllCOAAccess');
        $this->arrData['allsalesaccess'] = array('chkAllSalesAccess');
        $this->arrData['bankname'] = array('bankName');
        $this->arrData['bankaccountname'] = array('bankAccountName');
        $this->arrData['bankaccountnumber'] = array('bankAccountNumber');
        $this->arrData['position'] = array('position');
        $this->arrData['needrealization'] = array('chkNeedRealization');
        $this->arrData['commissionpercentage'] = array('commissionPercentage','number');
	    $this->arrData['targetprofit'] = array('targetProfit','number');
        $this->arrData['targetmonthperiod'] = array('targetMonthPeriod','number');
        $this->arrData['allpaymentmethodaccess'] = array('chkAllPaymentMethodAccess');       
        $this->allowedStatusForEdit = array(1,2);
		
        $this->arrLockedTable = array(); 
        array_push($this->arrLockedTable, array('table'=>'transaction_log','field'=>'createdby'));
        array_push($this->arrLockedTable, array('table'=>'sales_order_header','field'=>'saleskey'));
        array_push($this->arrLockedTable, array('table'=>'service_order_header','field'=>'saleskey')); 
        array_push($this->arrLockedTable, array('table'=>'service_work_order','field'=>'driverkey'));  
        array_push($this->arrLockedTable, array('table'=>'ap_employee_commission','field'=>'employeekey'));  
        array_push($this->arrLockedTable, array('table'=>'ap_employee_commission_payment_header','field'=>'employeekey'));  
        array_push($this->arrLockedTable, array('table'=>'ar_employee','field'=>'customerkey'));  
       
        $this->arrDeleteTable = array(); 
        array_push($this->arrDeleteTable, array('table'=>$this->tableContact,'field' => array('refkey'=>'{id}', 'reftable'=>$this->tableName)));   
        array_push($this->arrDeleteTable, array('table'=>$this->tableSecurityAccess,'field' => array('userkey'=>'{id}')));   
        array_push($this->arrDeleteTable, array('table'=>$this->tableImageID,'field' => array('refkey'=>'{id}')));   
        array_push($this->arrDeleteTable, array('table'=>$this->tableEmployeeCompany,'field' => array('refkey'=>'{id}')));   
        
        array_push($this->filterCriteria, array('title' => $this->lang['warehouse'], 'field' => 'warehousekey'));
       
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 70));
        array_push($this->arrDataListAvailableColumn, array('code' => 'name','title' => 'name','dbfield' => 'name','default'=>true,'width' => 250));
        array_push($this->arrDataListAvailableColumn, array('code' => 'attendance','title' => 'attendanceID','dbfield' => 'attendanceid', 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'division','title' => 'division','dbfield' => 'categoryname','default'=>true, 'width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'phone','title' => 'phone','dbfield' => 'phone', 'default'=>true, 'width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'email','title' => 'email','dbfield' => 'email', 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'username','title' => 'username','dbfield' => 'username', 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
    
        $this->includeClassDependencies(array( 
              'AR.class.php',      
              'Category.class.php',      
              'ChartOfAccount.class.php', 
              'City.class.php',      
              'Company.class.php',
              'Customer.class.php',      
              'EmployeeCategory.class.php',      
              'PaymentMethod.class.php', 
              'RoleTemplate.class.php', 
              'TermOfPayment.class.php', 
              'Location.class.php', 
              'Service.class.php', 
              'LoginLog.class.php'
        ));
       
		$this->overwriteConfig();
       
   }
    
	function getQuery(){
	   
	   $sql = '
				select
					'.$this->tableName. '.*,
					'.$this->tableCategory.'.name as categoryname, 
					'.$this->tableStatus.'.status as statusname	, 
					'.$this->tableCity.'.name as cityname, 
					'.$this->tableCityCategory.'.name as citycategoryname	, 
					'.$this->tableWarehouse.'.name as warehousename			
				from 
					'.$this->tableName . ' 
						 left join '.$this->tableWarehouse.' on '.$this->tableName . '.warehousekey = '.$this->tableWarehouse.'.pkey 
						 left join '.$this->tableCity.' on '.$this->tableName . '.citykey = '.$this->tableCity.'.pkey 
						 left join '.$this->tableCityCategory.' on '.$this->tableCity . '.categorykey = '.$this->tableCityCategory.'.pkey 
                         inner join '.$this->tableStatus.' on '.$this->tableName . '.statuskey = '.$this->tableStatus.'.pkey    
                         inner join '.$this->tableCategory.' on '.$this->tableName . '.categorykey = '.$this->tableCategory.'.pkey     
				where  		
					1=1
					
 		' .$this->criteria ; 
        
        $sql .= $this->getCompanyCriteria()	; 
        $sql .= $this->getWarehouseCriteria() ; 
        
        //khusus employee, tambahkan owner (atau yg login mungkin ??)
        //$sql .= ' or '.$this->tableName. '.pkey  = ' .  $this->oDbCon->paramString($this->userkey);
        //$this->setLog($sql); 
        return $sql;
    }
	
	function addData($arrParam){
		
		$arrayToJs =  array();
		
		try{ 
            
		 	if(!$this->oDbCon->startTrans())
				throw new Exception($this->errorMsg[100]); 
			 
			$code = $this->getNewCustomCode($arrParam);	
            
            $arrParam['code'] = (is_array($code)) ? $code[0] : $code;
                
            $pkey = $this->getNextKey($this->tableName);  
            $arrParam['pkey'] = $pkey;
			$arrParam['hidId'] = $pkey; // UTK UDPATE PRIVILEGES DETAIL
            
            $arrParam = $this->normalizeParameter($arrParam);
			$arrayToJs = $this->validateForm($arrParam);
			if (!empty($arrayToJs)) 
					return $arrayToJs;
            
			
            $arrParam['secretAuth'] = '';
            
            $arrParam['memberPassword'] = hash('sha256',md5($arrParam['memberPassword']));  
            
            if (!$this->hasSecurityPrivileges()){
               unset ($this->arrData['username']);
               unset ($this->arrData['password']); 
            }
			
            $this->updateData($arrParam,INSERT_DATA);   
            
            $this->updateImages($pkey, $arrParam['token-id-image-uploader'], $arrParam['id-image-uploader'], $this->tableImageID);  
            
            $this->updateDetail($pkey,$arrParam);   
            
			$this->oDbCon->endTrans();
					 
			$this->addErrorList($arrayToJs,true,$this->lang['dataHasBeenSuccessfullyUpdated']);   
            $rs = $this->searchData($this->tableName.'.pkey',$arrParam['pkey'],true);
            $arrayToJs[0]['data'] = $rs[0];
            
					 
		} catch(Exception $e){
			$this->oDbCon->rollback();
			$this->addErrorList($arrayToJs,false, $e->getMessage());   
		}
		
		return $arrayToJs; 	 	
		 
	}


	function editData($arrParam){  
		
		$arrayToJs =  array();
			
		try{ 
		
			if(!$this->oDbCon->startTrans())
				throw new Exception($this->errorMsg[100]);
            
			$arrParam['pkey'] = $arrParam['hidId'];	
			$code = $this->getNewCustomCode($arrParam);	 
            $arrParam['code'] = (is_array($code)) ? $code[0] : $code;
            
            
            $arrParam = $this->normalizeParameter($arrParam);
			$arrayToJs = $this->validateForm($arrParam,$arrParam['hidId']);
			if (!empty($arrayToJs)) 
					return $arrayToJs; 
            
            $updatePassword = '';
            $password = '';
			if (!empty($arrParam['memberPassword'])){
			    $password = hash('sha256',md5($arrParam['memberPassword'])); 
                $arrParam['memberPassword'] = $password;
			}else{
                unset($this->arrData['password']); 
            } 
             
             
            if (!empty($arrParam['updateProfile'])){ 
                 unset($this->arrData['statuskey']);
                 unset($this->arrData['categorykey']);
                 unset($this->arrData['warehousekey']);
                 unset($this->arrData['isdriver']);
                 unset($this->arrData['issales']);
                 unset($this->arrData['drivinglicense']);
                 unset($this->arrData['drivinglicenseexpdate']);
                 unset($this->arrData['placeofbirth']);
                 unset($this->arrData['dateofbirth']);
                 unset($this->arrData['drivingLicenseExpDate']);
                 unset($this->arrData['religionkey']);
                 unset($this->arrData['nationality']);
                 unset($this->arrData['maritalstatuskey']);
                 unset($this->arrData['sexkey']);
                 unset($this->arrData['idnumber']);
                 unset($this->arrData['taxid']);
                 unset($this->arrData['address1']);
                 unset($this->arrData['address2']);
                 unset($this->arrData['citykey']);
                 unset($this->arrData['zipcode']);
                 unset($this->arrData['photofile']); 
                 unset($this->arrData['signaturefile']); 
            }else{
                 unset($this->arrData['secretAuth']); 
            }
            
            if (!$this->hasSecurityPrivileges())
                 unset($this->arrData['username']); 
            
            $this->updateData($arrParam,UPDATE_DATA);
             
            if (empty($arrParam['updateProfile']) && !isset($arrParam['_isImport_'])){  
                $this->updateDetail($arrParam['hidId'],$arrParam); 
                $this->updateImages($arrParam['hidId'], $arrParam['token-id-image-uploader'], $arrParam['id-image-uploader'], $this->tableImageID);   
            }
          
                
            /*
            if (!empty($updatePassword))
                $this->syncPass($arrParam['memberUserName'], $password);
            */
            	 
            // update LANG
            if ($arrParam['updateProfile'] == 1 && isset($arrParam['selLang'])) { 
//                $lang = new Lang();
//                $rsLang = $lang->searchDataRow(array($lang->tableName.'.code'), ' and '.$lang->tableName.'.pkey = ' .$this->oDbCon->paramString($arrParam['selLang']) );
//                $langCode = (!empty($rsLang))? $rsLang[0]['code'] : 'id';
                $this->updateThemeSettings('lang',$arrParam['selLang']);
            }
            
            
			$this->oDbCon->endTrans();  
			$this->addErrorList($arrayToJs,true,$this->lang['dataHasBeenSuccessfullyUpdated']);   
					
				
		} catch(Exception $e){
			$this->oDbCon->rollback();
			$this->addErrorList($arrayToJs,false, $e->getMessage());  
		}		
				 
 		return $arrayToJs; 
	}
	 
    function updateDetail($pkey, $arrParam){  
                
        $this->updatePrivilegesDetail($arrParam); 
         
        $this->updateCOAAccess($pkey, $arrParam);
        $rsOwnedCompany =  $this->getOwnedCompany($pkey);
        if(empty($rsOwnedCompany)) 
            $this->updateCompany($pkey, $arrParam);
         
		         
        if($this->hasSecurityPrivileges()){  
			$this->updateWarehouseAccess($pkey, $arrParam);
			$this->updateCustomerAccess($pkey, $arrParam);
			$this->updateSalesAccess($pkey, $arrParam);
			$this->updatePaymentMethodAccess($pkey, $arrParam);		
		}
    }
    
    	  
    
  function updateCOAAccess($pkey,$arrParam){
        
	  	if(!USE_GL) return;
	  
        // filter hanya COA yg punya akses saja
        $arrCOA = $this->getCOAAccess($this->userkey);
        
		$sql = 'delete from '.$this->tableCOAAccess.' where refkey = '. $this->oDbCon->paramString($pkey);
		$this->oDbCon->execute($sql);
       
        $hasAllCOAAccess = ($arrParam['chkAllCOAAccess'] == 1) ? true : false;
                
        if ($hasAllCOAAccess)  return; 
        if (!isset($arrParam['selCOAAccess']))  return;
      
		$arrCOAAccess = $arrParam['selCOAAccess'];

        for ($i=0;$i<count($arrCOAAccess);$i++){ 
            if (!in_array( $arrCOAAccess[$i],$arrCOA))
                continue;

            $sql = 'insert into  '.$this->tableCOAAccess.' (refkey,coakey) values ('.$this->oDbCon->paramString($pkey).', '.$this->oDbCon->paramString($arrCOAAccess[$i]).' )';	
            $this->oDbCon->execute($sql);

        }
					 
	}
    
    function updateCustomerAccess($pkey,$arrParam){ 
        if(!$this->isActiveModule('Customer')) return;
		
        // filter hanya Customer yg punya akses saja
        $arrCustomer = $this->getCustomerAccess($this->userkey);
        
		$sql = 'delete from '.$this->tableEmployeeCustomer.' where refkey = '. $this->oDbCon->paramString($pkey);
		$this->oDbCon->execute($sql);
       
        $hasAllCustomerAccess = ($arrParam['chkAllCustomerAccess'] == 1) ? true : false;
        if ($hasAllCustomerAccess)  return; 
        if (!isset($arrParam['selCustomerAccess'])) return;

		$arrCustomerAccess = $arrParam['selCustomerAccess'];
        //$this->setLog($arrCustomerAccess,true);
        
        for ($i=0;$i<count($arrCustomerAccess);$i++){ 
            if (!in_array($arrCustomerAccess[$i],$arrCustomer))
                continue;

            $sql = 'insert into  '.$this->tableEmployeeCustomer.' (refkey,customerkey) values ('.$this->oDbCon->paramString($pkey).', '.$this->oDbCon->paramString($arrCustomerAccess[$i]).' )';
            $this->oDbCon->execute($sql);      

        }
					 
	}

     function updateSalesAccess($pkey,$arrParam){ 
        
        // filter hanya Customer yg punya akses saja
        $arrSales = $this->getSalesAccess($this->userkey);
        
		$sql = 'delete from '.$this->tableEmployeeSales.' where refkey = '. $this->oDbCon->paramString($pkey);
		$this->oDbCon->execute($sql);
       
        $hasAllSalesAccess = ($arrParam['chkAllSalesAccess'] == 1) ? true : false;
        if ($hasAllSalesAccess)  return; 
        if (!isset($arrParam['selSalesAccess'])) return;

		$arrSalesAccess = $arrParam['selSalesAccess'];
        //$this->setLog($arrCustomerAccess,true);
        
        for ($i=0;$i<count($arrSalesAccess);$i++){ 
            if (!in_array($arrSalesAccess[$i],$arrSales))
                continue;

            $sql = 'insert into  '.$this->tableEmployeeSales.' (refkey,saleskey) values ('.$this->oDbCon->paramString($pkey).', '.$this->oDbCon->paramString($arrSalesAccess[$i]).' )';
            $this->oDbCon->execute($sql);      

        }
					 
	}

    function updateWarehouseAccess($pkey, $arrParam){
         
        if (!isset($arrParam['selWarehouseAccess'])) return;
         
        $sql = 'delete from '.$this->tableEmployeeWarehouse.' where refkey = '. $this->oDbCon->paramString($pkey);
		$this->oDbCon->execute($sql); 
        
        $hasAllWarehouseAccess = ($arrParam['chkAllWarehouseAccess'] == 1) ? true : false;
        
        if ($hasAllWarehouseAccess) return;
        
        
        // filter hanya warehouse yg punya akses saja
        $arrWarehouse = $this->getWarehouseAccess($this->userkey);
        $arrWarehouseAccess = $arrParam['selWarehouseAccess'];   
        
        /*$this->setLog($arrWarehouse,true);
        $this->setLog($arrWarehouseAccess,true);*/
        
        for ($i=0;$i<count($arrWarehouseAccess);$i++){ 
            if (!in_array( $arrWarehouseAccess[$i],$arrWarehouse)) continue;
            
            $sql = 'insert into  '.$this->tableEmployeeWarehouse.' (refkey,warehousekey) values ('.$this->oDbCon->paramString($pkey).','.$this->oDbCon->paramString($arrWarehouseAccess[$i]).')';	
            $this->oDbCon->execute($sql); 
        }
        
    }
    
    function updateCompany($pkey, $arrParam) {
        $sql = 'delete from '.$this->tableEmployeeCompany.' where refkey = '. $this->oDbCon->paramString($pkey);
		$this->oDbCon->execute($sql); 
          
		$arrCompany = $arrParam['selCompany'];
        //kalo company kosong, ambil company pertama 
        
        if (empty($arrCompany)){ 
            $rsCompany = array_column($this->getAccessCompany($this->userkey),'companykey');
            $arrCompany[0] = $rsCompany[0];
        }
            
        for ($i=0;$i<count($arrCompany);$i++){ 
            $sql = 'insert into  '.$this->tableEmployeeCompany.' (refkey,companykey) values ('.$this->oDbCon->paramString($pkey).','.$this->oDbCon->paramString($arrCompany[$i]).')';	
            $this->oDbCon->execute($sql); 
        }
    }
	
    function updatePaymentMethodAccess($pkey, $arrParam){
        
        $sql = 'delete from '.$this->tableEmployeePaymentMethod.' where refkey = '. $this->oDbCon->paramString($pkey);
		$this->oDbCon->execute($sql);
        
        $hasAllPaymentMethodAccess = ($arrParam['chkAllPaymentMethodAccess'] == 1) ? true : false;
        
        if ($hasAllPaymentMethodAccess) return;
       
        if (!isset($arrParam['selPaymentMethodAccess'])) return;

        $arrPaymentMethod = $this->getPaymentMethodAccess($this->userkey);

        $arrPaymentMethodAccess = $arrParam['selPaymentMethodAccess']; 

        for ($i=0;$i<count($arrPaymentMethodAccess);$i++){ 
            
            if (!in_array( $arrPaymentMethodAccess[$i],$arrPaymentMethod)) continue;
            
            $sql = 'insert into  '.$this->tableEmployeePaymentMethod .' (refkey,paymentmethodkey) values ('.$this->oDbCon->paramString($pkey).','.$this->oDbCon->paramString($arrPaymentMethodAccess[$i]).')';	
            $this->oDbCon->execute($sql); 
        }

    }
	
	function updatePrivilegesDetail($arrParam){
         
        // update contacts
        $this->updateContactPerson($arrParam['hidId'],$arrParam); 
        
        $hasSecurityPrivileges = $this->hasSecurityPrivileges();
        
        if(!$hasSecurityPrivileges) return;
        
		$sql = 'delete from security_access where userkey = ' . $arrParam['hidId'];
		$this->oDbCon->execute($sql);
		 
		
		$security = new Security();
		$rsSecurityObject  = $security->generateSecurityObject(); 
		
		for ($i=0;$i<count($rsSecurityObject);$i++){
		 	
			if (!isset($arrParam['chkList' . $rsSecurityObject[$i]['pkey']]))
				continue;
		
			for($j=0;$j<count($arrParam['chkList' . $rsSecurityObject[$i]['pkey']]);$j++){
				$sql = '
					INSERT INTO		
					security_access ( 
						userkey,
						objectkey,
						statuskey 
						)
						VALUES	(
						'.$arrParam['hidId'].', 
						'.$this->oDbCon->paramString($rsSecurityObject[$i]['pkey']).', 
						'.$this->oDbCon->paramString($arrParam['chkList' . $rsSecurityObject[$i]['pkey']][$j]).' 
					)
				';
				
				$this->oDbCon->execute($sql);
			} 
		}
	} 

    
    function validateForm($arr,$pkey = ''){
		  
		$arrayToJs = parent::validateForm($arr,$pkey);  
	    
	    $name =  $arr['memberName'];  
		$email = $arr['memberEmail'];  
		$citykey = $arr['hidCityKey'];   
	   
        $rsData = $this->searchData();
        
        if($this->checkTotalItemLimitation($this->tableName,PLAN_TYPE['maxuser'],$pkey)){  
          $this->addErrorList($arrayToJs,false,$this->errorMsg['limit'][1]. ' ('.$this->lang['max'].' '. $this->formatNumber(PLAN_TYPE['maxuser']). ' '. strtolower($this->lang['employees']).')');  
        }
                 
		$rsEmployee = $this->isValueExisted($pkey,'name',$name);	 
        if(empty($name)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['employee'][1]);
		}else if(count($rsEmployee ) <> 0){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['employee'][2]);
		}
        
 
		$rsEmail = $this->isValueExisted($pkey,'email',$email);
		if(!empty($email)){ 
            if(!filter_var($email, FILTER_VALIDATE_EMAIL)) 
                $this->addErrorList($arrayToJs,false,$this->errorMsg['email'][3]);
            else if(count($rsEmail) <> 0) 	
                $this->addErrorList($arrayToJs,false,$this->errorMsg['email'][2]); 
        } 
        
        if (isset($arr['selCompany'])){
             $companyKey = $arr['selCompany'];
            for($i=0;$i<count($companyKey);$i++){ 
                $rsCompany = $this->getAccessCompany($this->userkey,$companyKey[$i]);
                if (empty($rsCompany)){
                    $this->addErrorList($arrayToJs,false,$this->errorMsg['company'][3]);  
                    break;
                }
            }  
        }
       
        
        // jika bkn dr profile 
        if (empty($arr['updateProfile'])){ 
            $hasSecurityPrivileges = $this->hasSecurityPrivileges();
            if ($hasSecurityPrivileges){ 
                $username = $arr['memberUserName'];  
                $pass = $arr['memberPassword'];  
                $passConfirmation = $arr['memberPasswordConfirmation'];  

                $rsUsername = $this->isValueExisted($pkey,'username',$username);	 

                if(!empty($username)) { 
                     if (strlen($username) <  5 || strlen($username) > 30) 
                         $this->addErrorList($arrayToJs,false,$this->errorMsg['username'][3]);

                     if (count($rsUsername) <> 0) 
                        $this->addErrorList($arrayToJs,false,$this->errorMsg['username'][2]); 
					
					 if ( !preg_match('/^[a-zA-Z0-9_.]+$/', $username)  )
                		$this->addErrorList($arrayToJs,false,$this->errorMsg['username'][4]);
                }


                if(!empty($pass)) {  
                    if($pass <> $passConfirmation) 
                        $this->addErrorList($arrayToJs,false,$this->errorMsg['password'][3]); 
                    
                    if(!$this->checkPasswordStrength($pass))
                        $this->addErrorList($arrayToJs,false,$this->errorMsg['password'][4]); 
                }
            }
        }
        
		return $arrayToJs;
	 }
	 
                   
	 function syncPass($username,$password){
	     return ;
         
	     // PERLU TAMBAH UTK SYNC KE DB LAIN YG MASI SATU USER 
	     
	    /* $dbCon = new Database(PS_DB['dbuser'],PS_DB['dbpass'],PS_DB['dbname'],'localhost'); 
         
	     try{
			   
		  	if(!$dbCon->startTrans())
				throw new Exception($this->errorMsg[100]);
		     
		    $sql = 'update customer set password = '. $this->oDbCon->paramString($password).' where username = ' . $this->oDbCon->paramString($username);
	        $dbCon->execute($sql);
	        
			$dbCon->endTrans();
	 				 
		} catch(Exception $e){
		    $dbCon->rollback(); 
		}*/
		
	 }
	  
     
    function normalizeParameter($arrParam, $trim = false){
         
            if (!isset($arrParam['chkIsDriver']) || $arrParam['chkIsDriver'] == 0){ 
                $arrParam['drivingLicenseExpDate'] =  date('d / m / Y');
            }
        
            if (empty($arrParam['updateProfile']) && !isset($arrParam['_isImport_'])){ 
                
				if($this->hasSecurityPrivileges()){
					if ($this->hasAllWarehouseAccess($this->userkey))
						$arrParam['chkAllWarehouseAccess'] = (isset($arrParam['chkAllWarehouseAccess'])) ? $arrParam['chkAllWarehouseAccess'] : 0;
					else
						$arrParam['chkAllWarehouseAccess']  = 0;

					if (!empty($this->hasAllCustomerAccess($this->userkey)))
						$arrParam['chkAllCustomerAccess'] = (isset($arrParam['chkAllCustomerAccess'])) ? $arrParam['chkAllCustomerAccess'] : 0;
					else
						$arrParam['chkAllCustomerAccess']  = 0;

					if ($this->hasAllCOAAccess($this->userkey))
						$arrParam['chkAllCOAAccess'] = (isset($arrParam['chkAllCOAAccess'])) ? $arrParam['chkAllCOAAccess'] : 0;
					else
						$arrParam['chkAllCOAAccess']  = 0; 


					if ($this->hasAllSalesAccess($this->userkey))
						$arrParam['chkAllSalesAccess'] = (isset($arrParam['chkAllSalesAccess'])) ? $arrParam['chkAllSalesAccess'] : 0;
					else
						$arrParam['chkAllSalesAccess']  = 0; 
      
                    if($this->hasAllpaymentMethodAccess($this->userkey)) {
                        $arrParam['chkAllpaymentMethodAccess'] = (isset($arrParam['chkAllpaymentMethodAccess'])) ? $arrParam['chkAllpaymentMethodAccess'] : 0;
                    } else {
                        $arrParam['chkAllpaymentMethodAccess'] = 0;
                    } 
				}
                
                $arrParam['photoFile'] = $this->updateImages($arrParam['pkey'], $arrParam['token-photo-image-uploader'], $arrParam['photo-image-uploader'],'',$this->uploadPhotoFolder);  
                $arrParam['signatureFile'] = $this->updateImages($arrParam['pkey'], $arrParam['token-signature-image-uploader'], $arrParam['signature-image-uploader'],'',$this->uploadSignatureFolder);  
                
            }
        
        
        
            if (!isset($arrParam['selWarehouse']) || empty($arrParam['selWarehouse'])){ 
                $warehouse = new Warehouse();
                $arrParam['selWarehouse'] = $warehouse->getDefaultData();
            }
        
		    $arrParam = parent::normalizeParameter($arrParam,true);   
            
        return $arrParam;
    }
    
    function hasSecurityPrivileges(){
		// ini hati2 karena berbeda fungsi dengan hasWarehousePrivileges di Baseclass
        $security = new Security();
        return $security->isAdminLogin($this->securityPrivilegesObject,10);   
    }
    
    
    function getCOAAccess($employeekey = ''){
        
	  	if(!USE_GL) return;
		
        $chartOfAccount = new ChartOfAccount();  
         
        $employeekey = (isset($employeekey) && !empty($employeekey)) ? $employeekey : $this->userkey ;
             
        if ($this->hasAllCOAAccess($employeekey)){
            $sql = 'select pkey as coakey from ' . $chartOfAccount->tableName;  
        }else{
            $sql = 'select 
                    '. $this->tableCOAAccess. '.*,
                    ' . $this->tableCOA .'.name as coaname
                from 
                    ' . $this->tableCOAAccess. ',
                    ' . $this->tableCOA. '
                where 
                    ' . $this->tableCOAAccess .'.coakey = ' . $this->tableCOA .'.pkey and
                    ' . $this->tableCOA .'.statuskey = 1 and
                    refkey = ' . $this->oDbCon->paramString($employeekey).'
                order by  ' . $this->tableCOA .'.pkey asc
                    ';
            }
            
        $rs = $this->oDbCon->doQuery($sql);
        $arrCOA = array_column($rs,'coakey'); 
          
        return $arrCOA;
    }
    
    function getWarehouseAccess($employeekey = ''){ 
        
        $employeekey = (isset($employeekey) && !empty($employeekey)) ? $employeekey : $this->userkey ; 
        
        if ($this->hasAllWarehouseAccess($employeekey)){
            // JGN PAKE SEARCH DATA, looping forever 
            $sql = 'select pkey as warehousekey from ' . $this->tableWarehouse;   
        } else{
            $sql = 'select 
                    ' . $this->tableEmployeeWarehouse .'.* ,
                    ' . $this->tableWarehouse .'.name as warehousename
                from 
                    ' . $this->tableEmployeeWarehouse .',
                    ' . $this->tableWarehouse .'
                where 
                    ' . $this->tableEmployeeWarehouse .'.warehousekey = ' . $this->tableWarehouse .'.pkey and
                    ' . $this->tableWarehouse .'.statuskey = 1 and
                    refkey = ' . $this->oDbCon->paramString($employeekey) ; // masalah gk kalo statuskeynya gk di masukin di criteria 
        } 
        

        $rs = $this->oDbCon->doQuery($sql);
        $arrWarehouse = array_column($rs,'warehousekey'); 

        return $arrWarehouse;
    }
    
    function getCustomerAccess($employeekey = ''){ 
            
        $employeekey = (isset($employeekey) && !empty($employeekey)) ? $employeekey : $this->userkey ;
         
        if ( $this->hasAllCustomerAccess($employeekey) ){ 
            $sql = 'select pkey as customerkey from ' . $this->tableCustomer;
        } else {
            $sql = 'select 
                    ' . $this->tableEmployeeCustomer .'.* ,
                    ' . $this->tableCustomer .'.name as customername
                from 
                    ' . $this->tableEmployeeCustomer .',
                    ' . $this->tableCustomer .'
                where 
                    ' . $this->tableEmployeeCustomer .'.customerkey = ' . $this->tableCustomer .'.pkey and
                    refkey = ' . $this->oDbCon->paramString($employeekey) ; 
        }
        
        $rs = $this->oDbCon->doQuery($sql);
        $arrCustomer = array_column($rs,'customerkey'); 
        
        return $arrCustomer;
         
    }

    
    function getSalesAccess($employeekey = ''){ 
            
        $employeekey = (isset($employeekey) && !empty($employeekey)) ? $employeekey : $this->userkey ;
         
        if ( $this->hasAllSalesAccess($employeekey) ){ 
            $sql = 'select pkey as saleskey from ' . $this->tableName;
        } else {
            $sql = 'select 
                    ' . $this->tableEmployeeSales .'.* ,
                    ' . $this->tableName .'.name as salesname
                from 
                    ' . $this->tableEmployeeSales .',
                    ' . $this->tableName .'
                where 
                    ' . $this->tableEmployeeSales .'.saleskey = ' . $this->tableName .'.pkey and
                    refkey = ' . $this->oDbCon->paramString($employeekey) ; 
        }
        
        $rs = $this->oDbCon->doQuery($sql);
        $arrSales = array_column($rs,'saleskey'); 
        
        return $arrSales;
         
    }


    function hasAllpaymentMethodAccess($employeekey) 
    {
        $sql = '
            select
                '. $this->tableName .'.pkey,
                '. $this->tableName .'.allpaymentmethodaccess
            from
                '. $this->tableName .'
            where
                '. $this->tableName .'.pkey = '. $this->oDbCon->paramString($employeekey) .'
        ';

        $rs = $this->oDbCon->doQuery($sql);

        $hasAllPaymentMethodAccess = $rs[0]['allpaymentmethodaccess'] == 1 ? true : false;

        return $hasAllPaymentMethodAccess;
    }

    function getPaymentMethodAccess($employeekey = '')
    {
        $employeekey = (isset($employeekey) && !empty($employeekey)) ? $employeekey : $this->userkey ;

        if ($this->hasAllPaymentMethodAccess($employeekey) ){ 
            $sql = 'select pkey as paymentmethodkey from ' . $this->tablePaymentMethod;
        } else {

            $sql = 'select 
                    ' . $this->tableEmployeePaymentMethod .'.* ,
                    ' . $this->tablePaymentMethod .'.name as paymentmethodname
                from 
                    ' . $this->tableEmployeePaymentMethod .',
                    ' . $this->tablePaymentMethod .'
                where 
                    ' . $this->tableEmployeePaymentMethod .'.paymentmethodkey = ' . $this->tablePaymentMethod .'.pkey and
                    refkey = ' . $this->oDbCon->paramString($employeekey) 
                ;
        }

        $rs = $this->oDbCon->doQuery($sql);
        $arrPayemntMethod = array_column($rs,'paymentmethodkey'); 
                    
        return $arrPayemntMethod;

    }

    function getAccessCompany($employeekey,$companykey = ''){
        
        $sql = 'select 
                    ' . $this->tableEmployeeCompany .'.* ,
                    ' . $this->tableCompany .'.name as companyname
                from 
                    ' . $this->tableEmployeeCompany .',
                    ' . $this->tableCompany .'
                where 
                    ' . $this->tableEmployeeCompany .'.companykey = ' . $this->tableCompany .'.pkey and
                    ' . $this->tableCompany .'.statuskey = 1 and
                    refkey = ' . $this->oDbCon->paramString($employeekey) ;
        
        if (!empty($companykey))
            $sql .= ' and companykey = ' . $this->oDbCon->paramString($companykey) ;
              
        return $this->oDbCon->doQuery($sql);
    }
    
    function getOwnedCompany($employeekey){
        $company = new Company();
        
        $rsCompany = $company->searchData($company->tableName.'.statuskey',1,true, ' and ' . $company->tableName.'.employeekey=' . $this->oDbCon->paramString($employeekey));
        return $rsCompany;
    }
    
    function checkIsUserFranchisee($employeekey){
        $sql = 'select * from ' . $this->tableCompany .' where employeekey = ' . $this->oDbCon->paramString($employeekey) ;
        $rs = $this->oDbCon->doQuery($sql);
        
        return (empty($rs)) ? false : true;
    }
    
    
    function delete($id,$forceDelete = false,$reason = ''){
		 
		$arrayToJs =  array(); 
         
		try{ 
		
	 		$arrayToJs = $this->validateDelete($id);
			if (!empty($arrayToJs)) 
				return $arrayToJs;
					 
			 if(!$this->oDbCon->startTrans())
				throw new Exception($this->errorMsg[100]);
				 
				$sql = 'delete from  '.$this->tableName.' where pkey = ' . $this->oDbCon->paramString($id);
				$this->oDbCon->execute($sql); 
            
                $this->deleteReference($id); 
            
                $this->deleteAll($this->defaultDocUploadPath.$this->uploadFolder.$id);
                $this->deleteAll($this->defaultDocUploadPath.$this->uploadPhotoFolder.$id);
                $this->deleteAll($this->defaultDocUploadPath.$this->uploadSignatureFolder.$id);
            
                $this->setTransactionLog(DELETE_DATA,$id);
            
				$this->oDbCon->endTrans(); 

				$this->addErrorList($arrayToJs,true,$this->lang['dataHasBeenSuccessfullyUpdated']); 
				 
		} catch(Exception $e){
			$this->oDbCon->rollback(); 
			$this->addErrorList($arrayToJs,false, $e->getMessage()); 
			
		}		 
			 	
 		return $arrayToJs; 
	}
    
    
    function updateAPCommissionOutstanding($employeekey){
        
    }
    
    function updateAROutstanding($customerkey){
         
		  $arrayToJs = array();
         
         try{	  
				if(!$this->oDbCon->startTrans())
					throw new Exception($this->errorMsg[100]); 
			   
                $ar = new AREmployee();
                $outstanding = $ar->getAROutstanding($customerkey);

                $sql = 'update ' . $this->tableName .' set aroutstanding = ' .  $this->oDbCon->paramString($outstanding) .' where pkey = ' .  $this->oDbCon->paramString($customerkey);
                $this->oDbCon->execute($sql);
				
                $this->oDbCon->endTrans();  
				$this->addErrorList($arrayToJs,true,$this->lang['dataHasBeenSuccessfullyUpdated']);  
			
			} catch(Exception $e){
				$this->oDbCon->rollback();
				$this->addErrorList($arrayToJs,false, $e->getMessage());  
			}	 
      
   } 
    
        
    function getAPCommissionCOAKey($employeekey,$warehousekey){ 
        $coaLink = new COALink();
        $warehouse = new Warehouse();
        
        $rsEmployee = $this->getDataRowById($employeekey);
        if (!empty($rsEmployee[0]['commissionapcoakey'])){  
             $coakey = $rsEmployee[0]['commissionapcoakey'];
        }else{ 
            $rsCOA = $coaLink->getCOALink ('commissionap', $warehouse->tableName,  $warehousekey);   
            $coakey = $rsCOA[0]['coakey'];
        }
        
        return $coakey;
    }
            
    function getARCOAKey($employeekey,$warehousekey){ 
        $coaLink = new COALink();
        $warehouse = new Warehouse();
        
        $rs = $this->getDataRowById($employeekey);
        if (!empty($rs[0]['arcoakey'])){  
             $coakey = $rs[0]['arcoakey'];
        }else{ 
            $rsCOA = $coaLink->getCOALink ('employeear', $warehouse->tableName,  $warehousekey);   
            $coakey = $rsCOA[0]['coakey'];
        }
        
        return $coakey;
    }
    
                
    function getCashAdvCOAKey($employeekey,$warehousekey){ 
        $coaLink = new COALink();
        $warehouse = new Warehouse();
        
        $rsEmployee = $this->getDataRowById($employeekey);
        if(!empty($rsEmployee[0]['cashbankcoakey'])){ 
            $coakey = $rsEmployee[0]['cashbankcoakey'];
        }else{ 
            $rsCOA = $coaLink->getCOALink ('cashbankdriver', $warehouse->tableName,$warehousekey, 0);  
            $coakey = $rsCOA[0]['coakey'];
        }
        
        return $coakey;
    }
	
	function generateDefaultQueryForAutoComplete($returnField){ 
            $sql = 'select
					'.$returnField['key'].',
					'.$returnField['value'].' as value,
					'.$this->tableName . '.code,
					'.$this->tableName . '.statuskey,
					'.$this->tableName . '.commissionpercentage
				from 
				    '.$this->tableName . ',
                    '.$this->tableStatus.'
				where  		
					'.$this->tableName . '.statuskey = '.$this->tableStatus.'.pkey 
			';
         
        return $sql;
    }
    
      
    function getExpiryLicense($licenseType = array(), $warehousekey = ''){
        $rs = array();
        
        $arrSQL = array();
        
        $basesql = 'select code, name, ';
        
		$arrType = array_column($licenseType,null,'dbfield');
		
		foreach($arrType as $row){ 
			$sql = $basesql . ' \''.$row['label'].'\' as typename, 
				  '.$row['dbfield'].' as expireddate
				  from ' . $this->tableName .' 
				  where
				  	' . $this->tableName .'.statuskey = 2 and
                    ' . $this->tableName .'.isdriver = 1 and
				  datediff('.$row['dbfield'].', now()) < ' . $this->oDbCon->paramString( $row['duedays'] ) ;
			
			
			if (!empty($warehousekey))
				$sql .= ' and '.$this->tableName.'.warehousekey in ('. $this->oDbCon->paramString($warehousekey,',').' )';
			
            array_push($arrSQL, $sql);
		}
	   
        $sql = implode(' UNION ALL ', $arrSQL);
        $sql = 'select *, datediff(expireddate,now()) as duedate from ('.$sql.' ) expired_license order by expireddate asc, name asc';
      
        $rs =  $this->oDbCon->doQuery($sql);
         
        return $rs;
    } 
    function getDetailCommission($pkey, $serviceKey = '', $criteria=''){
          
      $sql = 'select
          '.$this->tableEmployeeCommission .'.*,
          '.$this->tableService .'.name as servicename
      from
          '. $this->tableEmployeeCommission .' 
          left join ' . $this->tableService . ' on ' . $this->tableEmployeeCommission . '.servicekey = ' . $this->tableService . '.pkey 
      where  
          '.$this->tableEmployeeCommission .'.refkey = '.$this->oDbCon->paramString($pkey);
  
      if (!empty($serviceKey))  
          $criteria = ' and '. $this->tableEmployeeCommission.'.servicekey = '.$serviceKey; 
      
      $sql .= $criteria;
      
      return $this->oDbCon->doQuery($sql);
  }
 function getDataSecurityPrivilegesForReport($criteria = '', $order = '')
    {
        $sql = '
            select 
                ' . $this->tableSecurityAccess . '.*,
                ' . $this->tableEmployee . '.name as employeename,
                ' . $this->tableSecurityObject . '.modulecode,
                ' . $this->tableSecurityObject . '.modulename,
                ' . $this->tableSecurityObject . '.modulestatus,
                CONCAT( ' . $this->tableSecurityAccess . '.userkey, \'-\',  ' . $this->tableSecurityAccess . '.objectkey) as indexkey,
                IF(security_access.statuskey = 10, 1, 0) as viewdata,
                IF(security_access.statuskey = 11, 1, 0) as adddata,
                IF(security_access.statuskey = 12, 1, 0) as deletedata
            from
                ' . $this->tableSecurityAccess . '
                left join ' . $this->tableEmployee . ' on ' . $this->tableSecurityAccess . '.userkey = ' . $this->tableEmployee . '.pkey,
                ' . $this->tableUserSecurityObject . ',
                ' . $this->tableSecurityObject . '
            where
                ' . $this->tableSecurityAccess .'.objectkey = '. $this->tableSecurityObject .'.pkey and
                ' . $this->tableSecurityObject .'.pkey = '. $this->tableUserSecurityObject .'.security_object_key and
                ' . $this->tableUserSecurityObject .'.statuskey = 1 and
                ' . $this->tableEmployee .'.statuskey = 2
        ';

        if (!empty($criteria)) {
            $sql .= $criteria;
        }

        if (!empty($order)) {
            $sql .= $order;
        }

        $result = $this->oDbCon->doQuery($sql);

        return $result;
    }


    function getDetailForAPI($arrKey,$arrIndex = array()){
        $rsDetailsCol = array();
        
        if(in_array('contact_person_detail',$arrIndex)){  
            
            $keys = is_array($arrKey) ? $arrKey : [$arrKey];
            $rsDetails = [];
            foreach ($keys as $key) {
                $rows = $this->getContactPerson($key);

                if (!empty($rows)) {
                    foreach ($rows as $row) {
                        $rsDetails[] = $row;
                    }
                }
            } 
            $rsDetails = $this->reindexDetailCollections($rsDetails,'refkey');
            $rsDetailsCol['contact_person_detail'] = $rsDetails;
        }

        return $rsDetailsCol;

    }

    function getWarehouseAccessForAPI($employeekey) 
    { 
        if ($this->hasAllWarehouseAccess($employeekey)){
            $sql = 'select pkey as warehousekey,code from ' . $this->tableWarehouse;   
        } else{
            $sql = 'select  
                    ' . $this->tableWarehouse .'.pkey as warehousekey,
                    ' . $this->tableWarehouse .'.code
                from 
                    ' . $this->tableEmployeeWarehouse .',
                    ' . $this->tableWarehouse .'
                where 
                    ' . $this->tableEmployeeWarehouse .'.warehousekey = ' . $this->tableWarehouse .'.pkey and
                    ' . $this->tableWarehouse .'.statuskey = 1 and
                    refkey = ' . $this->oDbCon->paramString($employeekey) ; // masalah gk kalo statuskeynya gk di masukin di criteria 
        } 

        $rs = $this->oDbCon->doQuery($sql); 

        return $rs;
    }

    function getSalesAccessForAPI($employeekey) 
    {
        if ($this->hasAllSalesAccess($employeekey)){ 
            $sql = 'select pkey as saleskey, code from ' . $this->tableName;
        } else {
            $sql = 'select 
                    ' . $this->tableName .'.code,
                    ' . $this->tableName .'.pkey as saleskey
                from 
                    ' . $this->tableEmployeeSales .',
                    ' . $this->tableName .'
                where 
                    ' . $this->tableEmployeeSales .'.saleskey = ' . $this->tableName .'.pkey and
                    refkey = ' . $this->oDbCon->paramString($employeekey) ; 
        }

        $rs = $this->oDbCon->doQuery($sql); 

        return $rs;
    }

    function getPaymentMethodAccessForAPI($employeekey) 
    {
        if ($this->hasAllPaymentMethodAccess($employeekey)){ 
            $sql = 'select pkey as paymentmethodkey, code from ' . $this->tablePaymentMethod;
        } else {

            $sql = 'select 
                    ' . $this->tablePaymentMethod .'.code,
                    ' . $this->tablePaymentMethod .'.pkey as paymentmethodkey
                from 
                    ' . $this->tableEmployeePaymentMethod .',
                    ' . $this->tablePaymentMethod .'
                where 
                    ' . $this->tableEmployeePaymentMethod .'.paymentmethodkey = ' . $this->tablePaymentMethod .'.pkey and
                    refkey = ' . $this->oDbCon->paramString($employeekey) 
                ;
        }

        $rs = $this->oDbCon->doQuery($sql);

        return $rs;
    }

    function getCOAAccessForAPI($employeekey)
    {
        if(!USE_GL) return;
		
        $chartOfAccount = new ChartOfAccount();  
         
        $employeekey = (isset($employeekey) && !empty($employeekey)) ? $employeekey : $this->userkey ;
             
        if ($this->hasAllCOAAccess($employeekey)){
            $sql = 'select pkey as coakey, code from ' . $chartOfAccount->tableName;  
        }else{
            $sql = 'select 
                    ' . $this->tableCOA .'.pkey as coakey,
                    ' . $this->tableCOA .'.code
                from 
                    ' . $this->tableCOAAccess. ',
                    ' . $this->tableCOA. '
                where 
                    ' . $this->tableCOAAccess .'.coakey = ' . $this->tableCOA .'.pkey and
                    ' . $this->tableCOA .'.statuskey = 1 and
                    refkey = ' . $this->oDbCon->paramString($employeekey).'
                order by  ' . $this->tableCOA .'.pkey asc
                    ';
            }
            
        $rs = $this->oDbCon->doQuery($sql);

        return $rs;
    }

    function getUserPrivilegesForAPI($employeekey)
    {
        $sql = '
            select
                '. $this->tableSecurityAccess.'.userkey,
                '. $this->tableSecurityAccess.'.objectkey,
                '. $this->tableSecurityAccess.'.statuskey,
                '. $this->tableSecurityObject.'.categorykey,
                '. $this->tableSecurityObject.'.modulecode,
                '. $this->tableSecurityObject.'.modulename,
                '. $this->tableSecurityObject.'.modulestatus
            from
                '.$this->tableSecurityAccess.',
                '.$this->tableSecurityObject.',
                '.$this->tableUserSecurityObject.'
            where
                '.$this->tableSecurityAccess.'.objectkey = '. $this->tableSecurityObject.'.pkey and
                '.$this->tableSecurityAccess.'.objectkey = '.$this->tableUserSecurityObject.'.security_object_key and
                '.$this->tableUserSecurityObject.'.statuskey = 1 and
                '.$this->tableSecurityAccess.'.userkey = '.$this->oDbCon->paramString($employeekey).'
        ';

        $rs = $this->oDbCon->doQuery($sql);

        $result = [];

        foreach ($rs as $row) {
            if (isset($row[0])) {
                $row = array_filter($row, fn($k) => !is_int($k), ARRAY_FILTER_USE_KEY);
            }

            $objectkey = (string)$row['objectkey'];

            if (!isset($result[$objectkey])) {
                $result[$objectkey] = [
                    'objectkey'    => $objectkey,
                    'categorykey'  => (string)$row['categorykey'],
                    'modulecode'   => (string)$row['modulecode'],
                    'modulename'   => (string)$row['modulename'],
                    'modulestatus' => (string)$row['modulestatus'],
                    'statuskeys'   => []
                ];
            }

            $result[$objectkey]['statuskeys'][] = (int)$row['statuskey'];
        
        }

        $result = array_values($result);
        return $result;


    }    
     
  }

?>
