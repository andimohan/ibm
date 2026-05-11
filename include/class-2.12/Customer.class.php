<?php

class Customer extends BaseClass{
 
   function __construct(){
		
		parent::__construct();
       
		$this->tableName = 'customer'; 
		$this->tableStatus = 'customer_status';
		$this->tableCity = 'city';
		$this->tableCategory = 'customer_category'; 
		$this->tableCityCategory = 'city_category'; 
		$this->tableCustomerSocialMedia = 'customer_social_media'; 
		$this->tableSocialMedia = 'social_media'; 
		$this->tableTermOfPayment = 'term_of_payment';	  
		$this->tableCurrency = 'currency';
		$this->tableInvoicingType = 'disposal_invoicing_type';
        $this->tableAPCustomerCommission = 'ap_customer_commission';
       

        $this->tableContact = 'contact_person';	     		
	    $this->tableBusinessCategory = 'business_category';
	    $this->tableCustomerBusiness = 'customer_business_category_detail';
	    $this->tableMembershipSubscription = 'membership_subscription';
	    $this->tableDetailItem = 'customer_item_alias_detail';
	    $this->tableDetailAccount = 'customer_account_detail';
	    $this->tableItem = 'item';

	   	$this->tableCountry = 'country'; 
	    $this->tableSupplier = 'supplier';
        $this->tableInsuranceCompany = 'customer_insurance_company';	

        $this->tableMembershipLevel= 'membership_level';	  
        $this->tablePaymentMethod = 'payment_method';	  
        $this->tableMultipleAddress = 'multiple_address_detail';
	   	$this->tableEmployeeCustomer = 'employee_detail_customer';	   
       	$this->tableJobPosition = 'job_position';
       	$this->tableMedia = 'media';
       	$this->tableLocation = 'location';
        $this->tableSex = '_sex';	  
	    $this->tableFile = 'customer_file'; 
        $this->tableTinType = 'tin_type';
       
		$this->tablePersonInChargeHeader = 'person_in_charge_group_header';
		$this->tablePersonInChargeDetail = 'person_in_charge_group_detail';
		$this->tableCustomerPersonInCharge = 'customer_person_in_charge_detail';
	   
	   	$this->uploadFolder = 'customer/';
	    $this->uploadFileFolder = 'customer-file/';
		$this->securityObject = 'Customer';
        $this->creditLimitSecurityObject = 'creditLimitApproval';
		
        $this->importUrl = 'import/customer';
        $this->useStorage = $this->useStorage('S3');
	      
	    $this->activeModule = $this->isActiveModule(array('truckingServiceOrder', 'membershipSubscription', 'activityLog','CustomerInsurancePolicy','BuildingUnit'));
	    
	    	
        $this->arrPersonInCharge = array();
        $this->arrPersonInCharge['pkey'] = array('hidCustomerPersonInChargeKey');
        $this->arrPersonInCharge['refkey'] = array('pkey', 'ref');
        $this->arrPersonInCharge['personinchargekey'] = array('selPersonInCharge');
    
        $this->arrContactPerson = array(); 
        $this->arrContactPerson['pkey'] = array('hidContactPersonDetailKey'); 
        $this->arrContactPerson['refkey'] = array('pkey', 'ref');
        $this->arrContactPerson['reftable'] = array('reftable',array('mandatory'=>true));
        $this->arrContactPerson['name'] = array('cpName',array('mandatory'=>true));
        $this->arrContactPerson['position'] = array('cpPosition');
        $this->arrContactPerson['phone'] = array('cpPhone');

        $this->detailItem = array(); 
        $this->detailItem['pkey'] = array('hidDetailKey'); 
        $this->detailItem['refkey'] = array('pkey', 'ref');
        $this->detailItem['itemkey'] = array('hidItemKey',array('mandatory'=>true));
        $this->detailItem['alias'] = array('aliasItem');
                
	    $this->detailAccount = array(); 
        $this->detailAccount['pkey'] = array('hidAccountDetailKey'); 
        $this->detailAccount['refkey'] = array('pkey', 'ref');
        $this->detailAccount['rolekey'] = array('selRoleTypeKey');
        $this->detailAccount['username'] = array('userNameDetail',array('mandatory'=>true));
        $this->detailAccount['password'] = array('passwordDetail');
	   
		$this->arrBusinessCategory = array(); 
        $this->arrBusinessCategory['pkey'] = array('hidDetailKey'); 
        $this->arrBusinessCategory['refkey'] = array('pkey', 'ref'); 
        $this->arrBusinessCategory['refbusinesskey'] = array('selBusinessDetailKey',array('mandatory'=>true));
        
        $arrDetails = array(); 
        array_push($arrDetails, array('dataset' => $this->arrContactPerson, 'tableName' => $this->tableContact)); 
        array_push($arrDetails, array('dataset' => $this->arrBusinessCategory, 'tableName' => $this->tableCustomerBusiness));
	    array_push($arrDetails, array('dataset' => $this->detailAccount, 'tableName' => $this->tableDetailAccount));
	  
	   	if ( defined('PLAN_TYPE') && in_array(PLAN_TYPE['categorykey'], array(COMPANY_TYPE['trucking'],COMPANY_TYPE['forwarding'])) )
        	array_push($arrDetails, array('dataset' => $this->detailItem, 'tableName' => $this->tableDetailItem));
	   	
	   array_push($arrDetails, array('dataset' => $this->arrPersonInCharge, 'tableName' => $this->tableCustomerPersonInCharge));
	   	   
	   	   
        if( $this->activeModule['customerinsurancepolicy']){  
			$this->arrSupplierInsuranceCompany = array(); 
			$this->arrSupplierInsuranceCompany['pkey'] = array('hidDetaiSupplierKey'); 
			$this->arrSupplierInsuranceCompany['refkey'] = array('pkey', 'ref'); 
			$this->arrSupplierInsuranceCompany['supplierkey'] = array('selSupplierKey',array('mandatory'=>true)); 
        	array_push($arrDetails, array('dataset' => $this->arrSupplierInsuranceCompany, 'tableName' => $this->tableInsuranceCompany));
        }       
       
         if($this->useStorage){ 
            
            $this->arrDataFileDetail = array();  
            $this->arrDataFileDetail['pkey'] = array('hidDetailFileKey');
            $this->arrDataFileDetail['refkey'] = array('pkey','ref');
            $this->arrDataFileDetail['file'] = array('fileDetail',array('datatype' => 'file','uploadFolder' => $this->uploadFileFolder));
            
            array_push($arrDetails, array('dataset' => $this->arrDataFileDetail, 'tableName' => $this->tableFile));
        }

        
        $this->arrData = array();
        $this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails)); 
        $this->arrData['code'] = array('code'); 
        $this->arrData['sid'] = array('sid'); 
        $this->arrData['name'] = array('name');
        $this->arrData['alias'] = array('alias');
        $this->arrData['categorykey'] = array('selCategory');
        $this->arrData['username'] = array('userName');
        $this->arrData['password'] = array('password');
        $this->arrData['address'] = array('address');
        $this->arrData['sexkey'] = array('sex');
        $this->arrData['citykey'] = array('hidCityKey');
        $this->arrData['referralkey'] = array('hidReferralKey');
        $this->arrData['zipcode'] = array('zipCode');
        $this->arrData['phone'] = array('phone');
        $this->arrData['mobilecode'] = array('selMobileCode');
        $this->arrData['mobile'] = array('mobile');
        $this->arrData['email'] = array('email');
        $this->arrData['fax'] = array('fax'); 
        $this->arrData['idnumber'] = array('IDNumber');
        $this->arrData['dateofbirth'] = array('dob', 'date');
        $this->arrData['placeofbirth'] = array('hidPlaceOfBirthKey');
        $this->arrData['weight'] = array('weight', 'number');
        $this->arrData['height'] = array('height', 'number');
        $this->arrData['occupation'] = array('occupation');
        $this->arrData['fbaccount'] = array('FBAccount');
        $this->arrData['igaccount'] = array('IGAccount');
        $this->arrData['policynumber'] = array('policyNumber');
                     
        $this->arrData['taxid'] = array('taxid');
        $this->arrData['nib'] = array('nib'); 
        $this->arrData['taxregistrationname'] = array('taxRegistrationName');
        $this->arrData['taxregistrationaddress'] = array('taxRegistrationAddress');
        $this->arrData['description'] = array('description');
        $this->arrData['termofpaymentkey'] = array('selTermOfPayment');
        $this->arrData['companybankkey'] = array('selBank');
        $this->arrData['activationhashkey'] = array('activationhashkey');
        $this->arrData['creditlimit'] = array('creditlimit','number');
        
        $this->arrData['arcoakey'] = array('hidARCOAKey');
		$this->arrData['reimbursearcoakey'] = array('hidReimburseARCOAKey');
        $this->arrData['downpaymentcoakey'] = array('hidDownpaymentCOAKey'); 
        $this->arrData['currencypreference'] = array('selCurrencyPreference'); 
        $this->arrData['statuskey'] = array('selStatus'); 
        $this->arrData['saleskey'] = array('hidSalesKey'); 
        $this->arrData['mediakey'] = array('selMedia'); 
        $this->arrData['ismainaccount'] = array('chkIsMainAccount'); 
        $this->arrData['locationkey'] = array('hidLocationKey'); 
        $this->arrData['attention'] = array('attention');
        $this->arrData['parentkey'] = array('hidParentKey');
        $this->arrData['subscriptionstatuskey'] = array('selSubscriptionStatus');
        $this->arrData['activationdate'] = array('activationDate', 'date');
        $this->arrData['ssotypekey'] = array('hidSSOTypeKey'); 
        $this->arrData['latlng'] = array('hidLatLng'); 
        $this->arrData['mapaddress'] = array('mapAddress'); 
        $this->arrData['membershiplevel'] = array('selMembership'); 
        $this->arrData['companyname'] = array('companyName'); 
        $this->arrData['mainbusinesskey'] = array('selBusiness'); 
        $this->arrData['emailprivacykey'] = array('selEmailPrivacyKey'); 
        $this->arrData['mobileprivacykey'] = array('selMobilePrivacyKey'); 
        $this->arrData['offerdescription'] = array('offerDescription'); 
        $this->arrData['prospectdescription'] = array('prospectDescription'); 
        $this->arrData['supplierkey'] = array('supplierkey'); 

        $this->arrData['countrykey'] = array('selCountry'); 
        $this->arrData['nationalitykey'] = array('selNationality'); 
        $this->arrData['isinsured'] = array('isInsured'); 
        $this->arrData['excessfee'] = array('excessFee', 'number');
        $this->arrData['langkey'] = array('selLang');
        $this->arrData['gmt'] = array('selTimeZone');

        $this->arrData['jobpositionkey'] = array('selJobPosition'); 
        $this->arrData['bankname'] = array('bankName'); 
        $this->arrData['bankaccountname'] = array('bankAccountName'); 
        $this->arrData['bankaccountnumber'] = array('bankAccountNumber'); 
        $this->arrData['requestid'] = array('requestid');   
        $this->arrData['virtualaccount'] = array('virtualAccount');    
        $this->arrData['supplierlinkkey'] = array('hidSupplierLinkKey');    
       
        
        if($this->useStorage)
            $this->arrData['photofile'] = array('photoFile',array('datatype' => 'file','uploadFolder' => $this->uploadFolder)); 
        else // yg ini blm di test, harus test lg yg S3
	   	   $this->arrData['photofile'] = array('item-image-uploader',array('datatype' => 'image', 'uploadFolder' => $this->uploadFolder,  'token' => 'token-item-image-uploader', 'fileName' => 'item-image-uploader'));
       
       
		$this->arrData['billingemail'] = array('billingEmail');    
		$this->arrData['billingmobile'] = array('billingMobile');    
		$this->arrData['isica'] = array('chkICA');    
		$this->arrData['icacoakey'] = array('hidICACOAKey'); 
	   	$this->arrData['showgpslocation'] = array('chkShowGPSLocation');
	   	$this->arrData['invoicingtypekey'] = array('selInvoicingType');
	   	$this->arrData['islocked'] = array('islocked');
	   	$this->arrData['refbuildingunitkey'] = array('refbuildingunitkey'); //khusus building unit
        $this->arrData['islinked'] = array('islinked');
          
        $this->arrData['tku'] = array('tku');     
        $this->arrData['countrycode'] = array('countryCode');     
        $this->arrData['tintypekey'] = array('selTinType');      
        $this->arrData['otherdocuments'] = array('otherDocuments');  
        $this->arrData['nik'] = array('nik');
        $this->arrData['passport'] = array('passport');
        $this->arrData['displaytax23ininvoice'] = array('chkDisplayTax23InInvoice');
        $this->arrData['vendorcode'] = array('vendorCode');
		$this->arrData['invoicesignaturekey'] = array('hidInvoiceSignatureKey');
        
		   
//		$this->arrData['picgroupkey'] = array('selPersonInChargeGroup');
//		$this->arrData['apicacoakey'] = array('hidAPICACOAKey');  



        $this->allowedStatusForEdit = array(1,2);
       
        $this->arrLockedTable = array();
        $defaultFieldName = 'customerkey'; 
        array_push($this->arrLockedTable, array('table'=>'ar','field'=>$defaultFieldName)); 
        array_push($this->arrLockedTable, array('table'=>'ar_payment_header','field'=>$defaultFieldName)); 
        array_push($this->arrLockedTable, array('table'=>'billing_statement_header','field'=>$defaultFieldName)); 
        array_push($this->arrLockedTable, array('table'=>'email_blast','field'=>$defaultFieldName));  
        array_push($this->arrLockedTable, array('table'=>'gallery_header','field'=>$defaultFieldName)); 
        array_push($this->arrLockedTable, array('table'=>'preorder_header','field'=>$defaultFieldName)); 
        array_push($this->arrLockedTable, array('table'=>'rewards_point','field'=>$defaultFieldName)); 
        array_push($this->arrLockedTable, array('table'=>'sales_order_car_service_header','field'=>$defaultFieldName)); 
        array_push($this->arrLockedTable, array('table'=>'sales_order_header','field'=>$defaultFieldName)); 
        array_push($this->arrLockedTable, array('table'=>'sales_return_header','field'=>$defaultFieldName)); 
        array_push($this->arrLockedTable, array('table'=>'service_order_header','field'=>$defaultFieldName)); 
        array_push($this->arrLockedTable, array('table'=>'trucking_selling_rate_header','field'=>$defaultFieldName));  
        array_push($this->arrLockedTable, array('table'=>'trucking_service_order_header','field'=>$defaultFieldName));   
        array_push($this->arrLockedTable, array('table'=>'emkl_job_order_detail','field'=>$defaultFieldName));  
        array_push($this->arrLockedTable, array('table'=>'disposal_contract','field'=>$defaultFieldName));  
         
       
        $this->arrDeleteTable = array(); 
        array_push($this->arrDeleteTable, array('table'=>$this->tableContact,'field' => array('refkey'=>'{id}', 'reftable'=>$this->tableName)));  
        array_push($this->arrDeleteTable, array('table'=>$this->tableMultipleAddress,'field' => array('refkey'=>'{id}', 'reftable'=>$this->tableName))); 
	      
	    if( $this->activeModule['customerinsurancepolicy']){
			array_push($this->arrDeleteTable, array('table'=>$this->tableInsuranceCompany,'field' => array('refkey'=>'{id}', 'reftable'=>$this->tableName)));  
        }
        //array_push($this->arrDeleteTable, array('table'=>$this->tableCustomerSocialMedia,'field' => array('refkey'=>'{id}')));  
                      
       
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'name','title' => 'name','dbfield' => 'name','default'=>true, 'width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'category','title' => 'category','dbfield' => 'categoryname','default'=>true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'phone','title' => 'phone','dbfield' => 'phone','default'=>true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'mobilePhone','title' => 'mobilePhone','dbfield' => 'mobile','default'=>true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'email','title' => 'email','dbfield' => 'email', 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'address','title' => 'address','dbfield' => 'address', 'width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'arOutstanding','title' => 'outstanding','dbfield' => 'aroutstanding', 'width' => 80, 'align' => 'right', 'format' => 'integer'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
        array_push($this->arrDataListAvailableColumn, array('code' => 'dob','title' => 'dateOfBirth','dbfield' => 'dateofbirth',  'width' => 150, 'align' =>'center', 'format' => 'date'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'description','title' => 'note','dbfield' => 'description',  'width' => 200));
        
	    if ( defined('PLAN_TYPE') && in_array(PLAN_TYPE['categorykey'], array(COMPANY_TYPE['retail'])) )
	   		array_push($this->arrDataListAvailableColumn, array('code' => 'point','title' => 'point','dbfield' => 'point',  'width' => 80, 'align' => 'right', 'format' => 'integer'));
        
	   
        $this->includeClassDependencies(array(
              'ChartOfAccount.class.php',  
              'AR.class.php',  
              'PaymentMethod.class.php', 
              'TermOfPayment.class.php', 
              'Category.class.php', 
              'CustomerCategory.class.php',
              'City.class.php',  
              'Location.class.php',  
              'Currency.class.php',  
              'ItemMovement.class.php',  
              'Location.class.php',  
              'Media.class.php',  
              'MembershipSubscription.class.php',  
			  'BusinessCategory.class.php',
			  'CustomerFeatures.class.php',
			  'MembershipLevel.class.php',
              'Supplier.class.php',
              'Country.class.php', 
			  'Warehouse.class.php',
			  'PersonInChargeGroup.class.php',
			  'BuildingUnit.class.php'
        ));
       
	     if( $this->activeModule['truckingserviceorder']){  
           $this->includeClassDependencies(array(
                'TruckingServiceOrder.class.php',  
            ));
        }       
               
	    if( $this->activeModule['activitylog']){  
            $this->includeClassDependencies(array(
                'ActivityLog.class.php',  
            ));
        }       
	   
        if( $this->activeModule['customerinsurancepolicy']){  
           $this->includeClassDependencies(array(
                'CustomerInsurancePolicy.class.php',  
            ));
        }       
       
        $this->overwriteConfig();
   }
	
   function getQuery(){
	   
	   $sql = '
			select
					'.$this->tableName. '.*, 
                    '.$this->tableBusinessCategory . '.name as mainbusinessname,
                    '.$this->tableCategory.'.name as categoryname,
                    '.$this->tableCategory.'.code as categorycode,
                    '.$this->tableCountry.'.name as countryname,
                    '.$this->tableMedia.'.name as medianame,
                    '.$this->tableLocation.'.name as locationname,
					'.$this->tableStatus.'.status as statusname,	
                    IF(subscriptionstatuskey=1, "Aktif", "Tidak Aktif") as subscriptionstatus,
                    IF(ismainaccount=1, "<i class=\"fas fa-check text-green-avocado\"></i>", "") as mainaccounticon,
					'.$this->tableCity.'.name as cityname , 
					'.$this->tableCity.'.code as citycode , 
					'.$this->tableCurrency.'.name as currencyname,
                    '.$this->tableTermOfPayment.'.name as termofpayment, 
					'.$this->tableCityCategory.'.name as citycategoryname,
                    '.$this->tablePaymentMethod.'.name as paymentmethodname,
                    concat ('.$this->tableCity. '.name, ", ", '.$this->tableCityCategory.'.name) as cityandcategoryname,
                    '.$this->tableSex.'.name as sexname,
					'.$this->tableMembershipLevel.'.name as membershiplevelname,
					'.$this->tableJobPosition.'.name as jobpositionname,
                    '.$this->tableTinType.'.name as tintypename,
					invoicesignature.name as invoicesignaturename
				from 
					'.$this->tableName . ' 
                         left join ' . $this->tableBusinessCategory . ' on ' . $this->tableName . '.mainbusinesskey = ' . $this->tableBusinessCategory . '.pkey  
                         left join ' . $this->tableCountry . ' on ' . $this->tableName . '.countrykey = ' . $this->tableCountry . '.pkey  
						 left join '. $this->tableCategory.' on '.$this->tableName . '.categorykey = '.$this->tableCategory.'.pkey  
						 left join '.$this->tableCity.' on '.$this->tableName . '.citykey = '.$this->tableCity.'.pkey 
						 left join '.$this->tableLocation.' on '.$this->tableName . '.locationkey = '.$this->tableLocation.'.pkey 
						 left join '.$this->tableMedia.' on '.$this->tableName . '.mediakey = '.$this->tableMedia.'.pkey 
						 left join '.$this->tableCityCategory.' on '.$this->tableCity . '.categorykey = '.$this->tableCityCategory.'.pkey 
						 left join '.$this->tableCurrency.' on '.$this->tableName . '.currencypreference = '.$this->tableCurrency.'.pkey
                         left join '.$this->tableTermOfPayment.' on '.$this->tableName . '.termofpaymentkey = '.$this->tableTermOfPayment.'.pkey
                         left join '.$this->tablePaymentMethod.' on '.$this->tableName . '.companybankkey = '.$this->tablePaymentMethod.'.pkey 
                         left join '.$this->tableSex.' on '.$this->tableName . '.sexkey = '.$this->tableSex.'.pkey
						 left join '.$this->tableMembershipLevel.' on '.$this->tableName.'.membershiplevel = '.$this->tableMembershipLevel.'.pkey
						 left join '.$this->tableJobPosition.' on '.$this->tableName.'.jobpositionkey = '.$this->tableJobPosition.'.pkey
					     left join '.$this->tableTinType.' on '.$this->tableName.'.tintypekey = '.$this->tableTinType.'.pkey
						 left join '. $this->tableEmployee .' invoicesignature on '. $this->tableCustomer .'.invoicesignaturekey = invoicesignature.pkey,
					'.$this->tableStatus.' 
				where  		 
					'.$this->tableName . '.statuskey = '.$this->tableStatus.'.pkey   
					
 		' .$this->criteria ; 
        
        $sql .=  $this->getCustomerCriteria() ;
        return $sql;
    }
	
	
	
	function resetReferralKeyIfQuotaExceed($pkey){
		// $pkey => customerkey
		$customerFeatures = new CustomerFeatures();
		
		// hanya hapus jika ada quota dan melebih quota
		$rsCustomer = $this->searchDataRow( array($this->tableName.'.referralkey'),
										  	' and '.$this->tableName.'.pkey = ' . $this->oDbCon->paramString($pkey));
 		
		if(empty($rsCustomer) || $rsCustomer[0]['referralkey'] == 0) return false;
		
		$referralkey = $rsCustomer[0]['referralkey'] ;
			
		// cek batasan quota
		$arrReturn = $customerFeatures->getFeaturesQuota($referralkey, array('funckey' => 'referral'));
		if(empty($arrReturn)) return false;
		
		$featurekey = $arrReturn[0]['featurekey'];
		$quota = $arrReturn[0]['quota'];
		$quotaUsed = $arrReturn[0]['quotaused'];
		
		if($quotaUsed >= $quota){
			$sql = 'update '.$this->tableName.'  set '.$this->tableName.'.referralkey = 0 where '.$this->tableName.'.pkey = ' . $this->oDbCon->paramString($pkey);
			$this->oDbCon->execute($sql); 
			return true;
		}
		
		return false;
		
	}
	
    function afterUpdateData($arrParam, $action){ 
        
		parent::afterUpdateData($arrParam,$action);
		
        $pkey = $arrParam['pkey'];
        //$this->updateQuestionnair($pkey, $arrParam);
        //$this->updateCustomerMembership($pkey, $arrParam);
        //$this->updateCustomerSocialMedia($pkey, $arrParam);
        
        // kalo add user baru
        if ($action == INSERT_DATA && !empty($arrParam['fromFE'])){ 
            $this->sendActivationEmail($pkey);
			 
			// kalo ada keangggotaan 
			if( !empty($arrParam['_mnv-joined-membership']) && $arrParam['selMembership'] <> $arrParam['_mnv-joined-membership']){
				$newLevel = $arrParam['_mnv-joined-membership'];
				
				$membershipSubscription = new MembershipSubscription();
				$membershipLevel = new MembershipLevel();
				$warehouse = new Warehouse();
				$termOfPayment = new TermOfPayment();
				$rsLevel = $membershipLevel->getMembershipPrice($newLevel,$arrParam['hidCurrencyKey']);
				
				$arrMembership = array();
				
           		$arrMembership['code'] = 'xxxxx';
           		$arrMembership['trDate'] = date('d / m / Y');
           		$arrMembership['selWarehouseKey'] = $warehouse->getDefaultData();
           		$arrMembership['selTermOfPaymentKey'] = $termOfPayment->getDefaultData();
				$arrMembership['hidCustomerKey'] =$pkey;
				$arrMembership['subtotal'] = $rsLevel[0]['sellingprice'];
				$arrMembership['grandtotal'] = $rsLevel[0]['sellingprice'];
				$arrMembership['selStatus'] = 1;  
				$arrMembership['selMembershipLevel'] = $newLevel;  
				$arrMembership['hidCurrencyKey'] = $arrParam['hidCurrencyKey'];  
				 
				$membershipSubscription->addData($arrMembership);
			}
			
			// kalo ad referralkey dan quota
			// table customer_features_detail harusnya selalu ada  
			// sementara patokannya per bulan dulu 
			// counter sudah dihitung pada saat customer register
			
			if(!empty($arrParam['hidReferralKey'])){   
                // reset referral key kalo udah lebih
				$isReset = $this->resetReferralKeyIfQuotaExceed($pkey);
				
				// kalo masih ad quota
				if(!$isReset){ 
					$customerFeatures = new CustomerFeatures();
					$customerFeatures->updateMembershipFeaturesCounter($arrParam['hidReferralKey'],'referral'); 
				}
			}
			
		}
             
		// sementara khusus 119
		// khusus individu
		if($this->activeModule['customerinsurancepolicy'] && $arrParam['selCategory'] == 1){
			 
			// perlu ditambahkan, kalo customer ganti kategori, didelete
			
			$customerInsurancePolicy = new CustomerInsurancePolicy();
			
			$rsCust = $customerInsurancePolicy->searchDataRow(array($customerInsurancePolicy->tableName.'.*'),
															  ' and '.$customerInsurancePolicy->tableName.'.refkey = '.  $this->oDbCon->paramString($arrParam['pkey'])
															 );
			
			$arrPolis = array();
			$arrPolis['code'] =  'xxxxxx'; 
            $arrPolis['name'] = $arrParam['name'];
            $arrPolis['hidRefKey'] = $arrParam['pkey'];
            $arrPolis['phone'] = $arrParam['phone'];
            $arrPolis['mobile'] = $arrParam['mobile'];
            $arrPolis['email'] = $arrParam['email'];
            $arrPolis['trDate'] = date('d / m / Y');
            $arrPolis['dateOfBirth'] = $arrParam['dob'];
            $arrPolis['address'] = $arrParam['address'];
            $arrPolis['policyNumber'] = $arrParam['policyNumber'];
            $arrPolis['hidCityKey'] = $arrParam['hidCityKey'];
            $arrPolis['IDNumber'] = $arrParam['IDNumber'];
            $arrPolis['selCategory'] = $arrParam['selCategory'];
            $arrPolis['selCountry'] = $arrParam['selCountry'];
            $arrPolis['excessFee'] = $arrParam['excessFee'];
            $arrPolis['_mnv_auto_update'] = 1;//agar bisa edit islinked
             
			if ($arrParam['isInsured'] == 1)  
                $arrPolis['hidSupplierKey'] = $arrParam['selSupplierKey'][0]; 
			
            $arrPolis['selStatus'] = 1;
            $arrPolis['isLinked'] = 1;
			
			if(empty($rsCust)){
				$arrayToJs = $customerInsurancePolicy->addData($arrPolis); 
				
			}else{ 
                $arrPolis['hidId'] = $rsCust[0]['pkey']; 
				$arrPolis['code'] =  $rsCust[0]['code']; // biar gk berubah karena autocode
                $arrPolis['hidModifiedOn'] = $rsCust[0]['modifiedon'];	
				
                $arrayToJs = $customerInsurancePolicy->editData($arrPolis); 
			}
            
			if (!$arrayToJs[0]['valid'])
                throw new Exception('<strong>'. $arrayToJs[0]['message']);
  
		}
		
		
        $this->addContactsToThirdParty($arrParam['email']);
        
        if (!empty($arrParam['fromFE']) || isset($arrParam['_isImport_'])) return;
        
         
        $arrParam['maTypeKey'] = 1;
        $this->updateMultipleAddres($pkey, $arrParam);
        $this->updateCustomerAccess($pkey);

// versi lama
        if(!$this->useStorage){
            if(isset($arrParam['item-file-uploader'])) 
                $this->updateFile($arrParam['pkey'], $arrParam['token-item-file-uploader'], $arrParam['item-file-uploader']);   
        }
            
		
		// kalo neglink ke sistem lain 
		// kalo auto add ke TMS
		$this->syncPartnerCustomer($pkey);
        
        // update AP komisi kalo penerima referralnya beda 
        // hanya jika ad referral, kalo icom gk ad referal, 
        // defaultnya ke icommunity, maka tetep isi referalnya icomunity manual
        if(!empty($arrParam['hidReferralKey'])){
                $sql = 'update '. $this->tableAPCustomerCommission.' 
                        set '. $this->tableAPCustomerCommission.'.customerkey  = '.$this->oDbCon->paramString($arrParam['hidReferralKey']).'
                        where 
                            '.$this->tableAPCustomerCommission.'.aptype = '. $this->oDbCon->paramString(AP_TYPE['salesCommission']).' and
                            '.$this->tableAPCustomerCommission.'.statuskey = 1 and 
                            '.$this->tableAPCustomerCommission.'.refkey in (
                                select pkey from  '.$this->tableMembershipSubscription.' where customerkey = '.$this->oDbCon->paramString($pkey).' 
                            )
                    ';
 
                $this->oDbCon->execute($sql);
        }
    
    } 
	
	function syncPartnerCustomer($pkey){
		if(empty(PARTNER_ACCOUNT['TMS']))  return;
			  
        $rsHeader = $this->searchData( $this->tableName.'.pkey',$pkey,true); 
 
		// add to API
		$url = PARTNER_ACCOUNT['TMS']['partnerurl'].'/api/v3/customers';
 		
		// harus dipisah karena kalo ad request_id, ketika PUT, kodenya gk muncul
		$method = 'PUT'; // ( !empty($rsHeader[0]['partnerid']) ) ? 'PUT' : 'POST';
		
		$payload = array();
		$payload['code'] = $rsHeader[0]['code']; 
		$payload['request_id'] = $pkey;
		$payload['name'] = $rsHeader[0]['name'];
		$payload['category_id'] = $this->getPartnerId(new CustomerCategory(),$rsHeader[0]['categorykey']); 
		$payload['city_id'] = $this->getPartnerId(new City(),$rsHeader[0]['locationkey']);
		$payload['address'] = $rsHeader[0]['address'];
		$payload['zip_code'] = $rsHeader[0]['zipcode'];
		$payload['phone'] = $rsHeader[0]['phone'];
		$payload['mobile'] = $rsHeader[0]['mobile'];
		$payload['fax'] = $rsHeader[0]['fax'];
		$payload['email'] = $rsHeader[0]['email'];
		$payload['tax_id'] = $rsHeader[0]['taxid'];
		$payload['status_key'] = $rsHeader[0]['statuskey'];
		
		$this->executeAPIPartner(PARTNER_ACCOUNT['TMS'], $url,$method,$payload);
		
	}
    
    function addContactsToThirdParty($email){
       //nanti perlu cek ulang kalo sudah terdaftar lebih baik gk perlu nembak lg
        $smtpAgent = $this->loadSetting('SMTPAgent');
        $folderId = $this->loadSetting('sendInBlueDefaultFolderId');  
        switch($smtpAgent){
            case 'sendinblue' : 
                    $this->includeClassDependencies(array("SendInBlue.class.php"));
                    $sendinblue = new SendInblue();
                    $sendinblue->createContact($folderId,$email); 
                    break;
            default : break;
        }
    }
    
    function updateQuestionnair($pkey, $arrParam){
         
      /*  if(isset($arrParam['hidQuestionKey']) && !empty($arrParam['hidQuestionKey'])){
            
            $questionnaireResponse = new QuestionnaireResponse();
            
            $arrQuestionnaire = array();
            $arrQuestionnaire['code'] = 'xxxxxx';
            $arrQuestionnaire['userkey'] = $pkey;
            $arrQuestionnaire['hidRefKey'] = $arrParam['hidQuestionnaireKey'];
            $arrQuestionnaire['trDate'] = date('d / m / Y');
            $arrQuestionnaire['selStatus'] = 1;

            // response
            for($i=0;$i<count($arrParam['hidQuestionKey']);$i++){ 
                $questionkey = $arrParam['hidQuestionKey'][$i];
                $arrQuestionnaire['hidDetailKey'][$i] = 0;
                $arrQuestionnaire['hidQuestionKey'][$i] = $questionkey;
                $arrQuestionnaire['answer'][$i] = $arrParam['questionnaireAnswer'.$questionkey]; 
                $arrQuestionnaire['trDesc'][$i] = (isset($arrParam['questionnaireAnswerDescription'.$questionkey]) && !empty($arrParam['questionnaireAnswerDescription'.$questionkey])) ? $arrParam['questionnaireAnswerDescription'.$questionkey] : '';
            }

             $questionnaireResponse->addData($arrQuestionnaire);
        }
*/
    }
	
//	function updateCustomerMembership($pkey, $arrParam){
//         
//        if(isset($arrParam['membership']) && !empty($arrParam['membership'])){
//            
//            $customerMembership = new CustomerMembership();
//            $arrMembership = array();
//            $arrMembership['code'] = 'xxxxxx';
//            $arrMembership['hidCustomerKey'] = $pkey;
//            $arrMembership['hidReferralKey'] = $arrParam['hidReferralKey'];
//            $arrMembership['selMembership'] = $arrParam['membership'];
//            $arrMembership['trDate'] = date('d / m / Y'); 
//            $arrMembership['selStatus'] = 1;
//
//            $customerMembership->addData($arrMembership);
//        }
//
//    }

    
//    function updateCustomerSocialMedia($pkey, $arrParam){
//        if(!isset($arrParam['hidSocialKey'])) return;
//        
//        $socialMedia = new SocialMedia();
//         
//        $sql = 'delete from '.$this->tableCustomerSocialMedia.' where refkey = '. $this->oDbCon->paramString($pkey);
//		$this->oDbCon->execute($sql); 
//         
//         
//        foreach($arrParam['hidSocialKey'] as $row){  
//            $sql = 'insert into '.$this->tableCustomerSocialMedia.' (refkey,socialkey,value) values ('.$this->oDbCon->paramString($pkey).','.$this->oDbCon->paramString($row).','.$this->oDbCon->paramString($arrParam['socialMedia'.$row]).')';	
//            $this->oDbCon->execute($sql); 
//        }
//                                     
//    }

	function updateCustomerAccess($pkey){
		$userkey = $this->userkey;
		$sql = 'insert into  '.$this->tableEmployeeCustomer.' (refkey,customerkey) values ('.$this->oDbCon->paramString($userkey).', '.$this->oDbCon->paramString($pkey).' )';
        $this->oDbCon->execute($sql);
	}
  
    function delete($id,$forceDelete = false, $reason = ''){
		
        //$questionnaireResponse = new QuestionnaireResponse();
        
		$arrayToJs =  array();
	   
		try{ 
		
	 		$arrayToJs = $this->validateDelete($id);
			if (!empty($arrayToJs)) 
				return $arrayToJs;
					 
			 if(!$this->oDbCon->startTrans())
				throw new Exception($this->errorMsg[100]);
				 
                $rs = $this->getDataRowById($id);
            
				$sql = 'delete from  '.$this->tableName.' where pkey = ' . $this->oDbCon->paramString($id);
				$this->oDbCon->execute($sql);
				$this->deleteAll($this->defaultDocUploadPath.$this->uploadFolder.$id);
			
         
                // delete quittionare 
                /*$rsQuiz = $questionnaireResponse->searchData($questionnaireResponse->tableName.'.userkey',$id);
            
            	$sql = 'delete from  '.$questionnaireResponse->tableNameDetail.' where refkey = ' . $this->oDbCon->paramString($rsQuiz[0]['pkey']);
				$this->oDbCon->execute($sql);
             
            	$sql = 'delete from  '.$questionnaireResponse->tableName.' where pkey = ' . $this->oDbCon->paramString($rsQuiz[0]['pkey']);
				$this->oDbCon->execute($sql); */
            
            
                $this->deleteReference($id);
            
                $this->setTransactionLog(DELETE_DATA,$id);
            
				$this->oDbCon->endTrans(); 

				$this->addErrorList($arrayToJs,true,$this->lang['dataHasBeenSuccessfullyDeleted']); 
				 
                $this->afterCommitDelete($rs); 
            
		} catch(Exception $e){
			$this->oDbCon->rollback(); 
			$this->addErrorList($arrayToJs,false, $e->getMessage()); 
			
		}		 
			 	
 		return $arrayToJs; 
	}
	
	    
    function afterStatusChanged($rsHeader){   
        // retrieve latest status
        $rsHeader = $this->getDataRowById($rsHeader[0]['pkey']);
		
        if ($rsHeader[0]['statuskey'] == 2 && in_array($rsHeader[0]['activationdate'],ARR_DB_EMPTY_DATE)){ 
         	// sementara gk pake email
			$sql = 'update ' . $this->tableName .' set activationdate = now() where pkey = ' . $this->oDbCon->paramString($rsHeader[0]['pkey']);
			$this->oDbCon->execute($sql);
		}
    }
    
    
	function sendActivationEmail($userkey){
		
		$useEmailActivation = $this->loadSetting('useEmailActivation');
		if($useEmailActivation <> 1) return;
		
        global $twig; 
		
		// kirim email
		
        $rsCust = $this->getDataRowById($userkey);
        $activationLink =  HTTP_HOST.'activation/'.$rsCust[0]['pkey'].'/'.$rsCust[0]['activationhashkey'];
	 
        $arrTwigVar = array();
        $arrTwigVar = $this->getDefaultEmailVariable();
         
        $arrTwigVar['CUSTOMER_NAME'] = $rsCust[0]['name']; 
        $arrTwigVar['ACTIVATION_LINK'] = $activationLink;
          
        
        $lang = new Lang();
        $rsLang = $lang->searchDataRow(array($lang->tableName.'.code'),
                                        ' and '.$lang->tableName.'.pkey = '.$this->oDbCon->paramString($rsCust[0]['langkey'])
                                      );
        
        $content = $twig->render($this->getLangTemplatePath('email-activation.html',true,$rsLang[0]['code']), $arrTwigVar);
        $this->sendMail(array(), $this->lang['activationEmail'] . ' - ' . DOMAIN_NAME,$content,array('name' => $rsCust[0]['name'], 'email'=>$rsCust[0]['email'])); 
        
        // kirim WA 
		// content WA harus bisa disetting per user
		if(!empty($this->loadSetting('WAGatewayAPIKey'))){ 
			$content = $twig->render($this->getLangTemplatePath('wa-activation.html',true,$rsLang[0]['code']), $arrTwigVar);
			$content = html_entity_decode(strip_tags($content)); 
            
            if(!empty($rsCust[0]['mobilecode'])) $rsCust[0]['mobile'] = $rsCust[0]['mobilecode'] . $rsCust[0]['mobile'];
			$this->sendWA($rsCust[0]['mobile'],$content,true);
		}
		 
	}
	
	function requestRecoverAccount($arr){
        
		global $twig;
        
		$arrayToJs =  array();
		 
		$arrayToJs = $this->validateRequestRecoverAccount($arr);
        
        if (!empty($arrayToJs))  return $arrayToJs;
				 
        
        $rsCust = $this->searchDataRow(array($this->tableName.'.pkey', $this->tableName.'.activationhashkey', $this->tableName.'.name',$this->tableName.'.langkey', $this->tableName.'.email'),
                           ' and '.$this->tableName.'.email = '.$this->oDbCon->paramString($arr['email']).'
                             and '.$this->tableName.'.statuskey = 2'); // cuma yg aktif yg boleh reset password
        
		//$rsCust = $this->searchData('','',true,' and '.$this->tableName.'.email = ' . $this->oDbCon->paramString($arr['email']) );
				  
         
		$resetLink =  HTTP_HOST.'account-recovery/'.$rsCust[0]['pkey'].'/'.$rsCust[0]['activationhashkey'];
	 
        $arrTwigVar = array();
        $arrTwigVar = $this->getDefaultEmailVariable();
           
        $arrTwigVar['CUSTOMER_NAME'] = $rsCust[0]['name'];
        $arrTwigVar['RESET_PASSWORD_LINK'] = $resetLink;
        				 
        $lang = new Lang();
        $rsLang = $lang->searchDataRow(array($lang->tableName.'.code'),
                                        ' and '.$lang->tableName.'.pkey = '.$this->oDbCon->paramString($rsCust[0]['langkey'])
                                      );
        $content = $twig->render($this->getLangTemplatePath('email-reset-password.html',true,$rsLang[0]['code']), $arrTwigVar); 
        
        $this->sendMail(array(), $this->lang['accountRecovery'] . ' - ' . DOMAIN_NAME,$content,array('email'=>$rsCust[0]['email'])); 
		
		$this->addErrorList($arrayToJs,true,$this->lang['emailSentSuccessful']);  
		return 	$arrayToJs; 
		
	}
	 
	function resendActivationEmail($arr){ 
		 	
		$arrayToJs =  array();
		$arrayToJs = $this->validateResendActivation($arr);
		if (!empty($arrayToJs)) 
				return $arrayToJs;
					
		$rsCust = $this->searchDataRow(array($this->tableName.'.pkey'),
                                       ' and '.$this->tableName.'.email = '.$this->oDbCon->paramString($arr['email']).'
                                         and '.$this->tableName.'.statuskey = 1');
        
        
		$this->sendActivationEmail($rsCust[0]['pkey']);
		$this->addErrorList($arrayToJs,true,$this->lang['emailSentSuccessful']);
		return 	$arrayToJs;
			
	}
	
	function activateMember($userkey,$activationhashkey){
		
		$arrayToJs =  array();
				
        $rsCust = $this->searchDataRow(array($this->tableName.'.pkey',$this->tableName.'.statuskey'),
                           ' and '.$this->tableName.'.pkey = '.$this->oDbCon->paramString($userkey).'
                             and '.$this->tableName.'.activationhashkey = '.$this->oDbCon->paramString($activationhashkey));
		
		// and '.$this->tableName.'.statuskey = 1

		//$rsCust = $this->searchData('','',true,' and '.$this->tableName.'.pkey = '.$this->oDbCon->paramString($userkey).' and '.$this->tableName.'.activationhashkey = '.$this->oDbCon->paramString($activationhashkey).' and '.$this->tableName.'.statuskey = 1');
		
        if (empty($rsCust))	{
  			$this->addErrorList($arrayToJs,false, $this->errorMsg[302]); 
	 	}else{
			if ($rsCust[0]['statuskey'] == 2){
				// account sudah aktif 
				$this->addErrorList($arrayToJs,false, $this->errorMsg[306]);
				
			}else if ($rsCust[0]['statuskey'] == 1){
				// account baru mau diaktifkan
				try{	 

					if(!$this->oDbCon->startTrans())
						throw new Exception($this->errorMsg[100]);

					$sql = 'update ' . $this->tableName .' set statuskey = 2, activationdate = now() where pkey = ' . $this->oDbCon->paramString($userkey);
					$this->oDbCon->execute($sql);

					$this->oDbCon->endTrans();  
					$this->addErrorList($arrayToJs,true,$this->lang['dataHasBeenSuccessfullyUpdated']);  

				} catch(Exception $e){
					$this->oDbCon->rollback();
					$this->addErrorList($arrayToJs,false, $e->getMessage());  
				}
			}else{
				$this->addErrorList($arrayToJs,false, $this->errorMsg[302]); 
			}
		}  	   
		
 		return $arrayToJs; 
			
	}
	
	function resetPassword($userkey,$activationhashkey){
		
	 	global $twig;
        
		$arrayToJs =  array();
		 
        $rsCust = $this->searchDataRow(array($this->tableName.'.pkey',$this->tableName.'.name',$this->tableName.'.email',$this->tableName.'.langkey',$this->tableName.'.mobilecode',$this->tableName.'.mobile'),
                           ' and '.$this->tableName.'.pkey = '.$this->oDbCon->paramString($userkey).'
                             and '.$this->tableName.'.activationhashkey = '.$this->oDbCon->paramString($activationhashkey).' 
                             and '.$this->tableName.'.statuskey = 2'); 
         
        
		if (empty($rsCust))	{
				$this->addErrorList($arrayToJs,false, $this->errorMsg[303]); 
		}else{
			try{	  
				
				$newPassword = $this->generateStrongPassword();
				
				if(!$this->oDbCon->startTrans())
					throw new Exception($this->errorMsg[100]);
			  
				//randon hashkey baru untuk mencegah reset password 2x
                
                    
				$sql = 'update ' . $this->tableName .' set password ='.$this->oDbCon->paramString(hash('sha256',md5($newPassword))).', activationhashkey = md5(now() + md5(email)) where pkey = ' . $this->oDbCon->paramString($userkey);
				$this->oDbCon->execute($sql);
				 
                $arrTemplate = array();

                // nanti jadikan default variable
                $companyName = $this->loadSetting('companyName');
                $arrTemplate['COMPANY_NAME'] = $companyName;
                $arrTemplate['HTTP_HOST'] = HTTP_HOST;

                $arrTwigVar = array();
                $arrTwigVar['CUSTOMER_NAME'] = $rsCust[0]['name'];
                $arrTwigVar['COMPANY_NAME'] = $companyName;
                $arrTwigVar['NEW_PASSWORD'] = $newPassword;

                $lang = new Lang();
                $rsLang = $lang->searchDataRow(array($lang->tableName.'.code'),
                    ' and '.$lang->tableName.'.pkey = '.$this->oDbCon->paramString($rsCust[0]['langkey'])
                  );
                 
                $content = $twig->render($this->getLangTemplatePath('email-new-password.html',true,$rsLang[0]['code']), $arrTwigVar);

                $this->sendMail(array(), $this->lang['resetPassword'] . ' - ' . DOMAIN_NAME,$content,array('email'=>$rsCust[0]['email'])); 
  
				$this->oDbCon->endTrans();  
				$this->addErrorList($arrayToJs,true,$this->lang['dataHasBeenSuccessfullyUpdated']);  
			
			} catch(Exception $e){
				$this->oDbCon->rollback();
				$this->addErrorList($arrayToJs,false, $e->getMessage());  
			}	 	  
		} 
			  	    
 		return $arrayToJs;  
				
	}
	
	
	function validateRequestRecoverAccount($arr){
		  
		$arrayToJs = array();
	     
        if (!IS_DEVELOPMENT){ 
            if(!isset($arr['_mnv-api'])){ 
                $captchaResponse = $arr['g-recaptcha-response'];  
                $request = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$this->loadSetting('reCaptchaSecretKey')."&response=".$captchaResponse);
                $captchaResult = json_decode($request);

                $errorCaptcha= $captchaResult->{'error-codes'};  

                if (empty($captchaResponse)){
                    $this->addErrorList($arrayToJs,false,$this->errorMsg['captcha'][1]);
                } else if(!$captchaResult->{'success'}){
                    $this->addErrorList($arrayToJs,false,$this->errorMsg['captcha'][1]);
                }  
            }
        }
				
        $rsCust = $this->searchDataRow(array($this->tableName.'.pkey'),
                               ' and '.$this->tableName.'.email = '.$this->oDbCon->paramString($arr['email']).'
                                 and '.$this->tableName.'.statuskey = 2'); 
         
		if (empty($rsCust)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['email'][4]); 
			return $arrayToJs; 
		} 
		
		return $arrayToJs;
	 }  
	 
	
	function validateResendActivation($arr){
		  
		  
		$arrayToJs = array();
	     
        if (!IS_DEVELOPMENT){ 
            if(!isset($arr['_mnv-api'])){ 
                $captchaResponse = $arr['g-recaptcha-response'];  
                $request = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$this->loadSetting('reCaptchaSecretKey')."&response=".$captchaResponse);
                $captchaResult = json_decode($request);

                $errorCaptcha= $captchaResult->{'error-codes'};  

                if (empty($captchaResponse)){
                    $this->addErrorList($arrayToJs,false,$this->errorMsg['captcha'][1]);
                } else if(!$captchaResult->{'success'}){
                    $this->addErrorList($arrayToJs,false,$this->errorMsg['captcha'][1]);
                }  
            }
        }
        
        
        $rsCust = $this->searchDataRow(array($this->tableName.'.pkey'),
                               ' and '.$this->tableName.'.email = '.$this->oDbCon->paramString($arr['email']).'
                                 and '.$this->tableName.'.statuskey = 1'); 
		if (empty($rsCust))   
			$this->addErrorList($arrayToJs,false,$this->errorMsg['email'][4]);   	
		 
		return $arrayToJs;
	 }  
	 
	function validateForm($arr,$pkey = ''){ 
        
        $arrayToJs = parent::validateForm($arr,$pkey);  
	    
	 	$username = isset($arr['userName']) ? $arr['userName'] : '';    
        $arrUserName = (!empty($arr['userNameDetail'])) ? array_filter($arr['userNameDetail']) : [];

	 	$isInsured = $arr['isInsured'];
	 	$supplierKey = $arr['selSupplierKey'];
         if(($isInsured == 1) &&  empty($supplierKey)){
             $this->addErrorList($arrayToJs,false,$this->errorMsg['insuranceCompany'][1]);
         }
 
        if (!IS_DEVELOPMENT){ 
			
			// hanya jika add data
            if ($pkey == '' && isset($arr['fromFE']) && !empty($arr['fromFE'])){ 
                // kalo bukan dr login SSO gk perlu validasi ini
                if(!isset($arr['mnv-OAuth']) || $arr['mnv-OAuth'] == 0){ 

                    if(empty($username)){
                        $this->addErrorList($arrayToJs,false,$this->errorMsg['username'][1]);
                    } 

                    // khusus edit
                    if(!empty($pkey) && empty($arr['chkAgree'])){
                        $this->addErrorList($arrayToJs,false,$this->errorMsg['registration'][1]);
                    }

                     if(!isset($arr['_mnv-api'])){  
                        $captchaResponse = $arr['g-recaptcha-response'];  
                        $request = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$this->loadSetting('reCaptchaSecretKey')."&response=".$captchaResponse);
                        $captchaResult = json_decode($request);

                        $errorCaptcha= $captchaResult->{'error-codes'};  
                        if (empty($captchaResponse)){
                            $this->addErrorList($arrayToJs,false,$this->errorMsg['captcha'][1]);
                        } else if(!$captchaResult->{'success'}){
                            $this->addErrorList($arrayToJs,false,$this->errorMsg['captcha'][1]);
                        } 
                     }
                }
            } 
        } 
	 
        if(isset($arr['item-file-uploader']) && !empty($arr['item-file-uploader'])){ 
            $arrFile = explode(",",$arr['item-file-uploader']);
            if(count($arrFile) > PLAN_TYPE['maxproductfile'])
                $this->addErrorList($arrayToJs,false,$this->errorMsg['limit'][3] .' ('.$this->lang['max'].' '. $this->formatNumber(PLAN_TYPE['maxproductfile']). ' '. strtolower($this->lang['files']).')' );

            for($i=0;$i<count($arrFile);$i++){
                if (empty($arrFile[$i]))
                    continue;

                $path = $this->uploadTempDoc.$this->uploadFileFolder.$arr['token-item-file-uploader']; 
                if (filesize($path.'/'.$arrFile[$i]) > (pow(1024,2) * PLAN_TYPE['maxfilesize']) )
                    $this->addErrorList($arrayToJs,false,$this->errorMsg['limit'][5] .' ('.$this->lang['max'].' '. $this->formatNumber(PLAN_TYPE['maxfilesize']). ' MB)' );
            }
        }	  
        
        $name = $arr['name'];  
        
        if(empty($name)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['customer'][1]);
		}else{
			 
		 if( $this->loadSetting('uniqueCustomerName') == 1){    
			$rsCustomer = $this->isValueExisted($pkey,'name',$name);	
			if(count($rsCustomer) <> 0) 
				$this->addErrorList($arrayToJs,false,$this->errorMsg['customer'][2]);
		 }
			
		}
        
	 	if(isset($arr['email']) && !empty($arr['email'])){
            $email = $arr['email'];
  
			if(!filter_var($email, FILTER_VALIDATE_EMAIL)) 
				$this->addErrorList($arrayToJs,false,$this->errorMsg['email'][3]);
			
			 if( $this->loadSetting('uniqueCustomerEmail') == 1){    
				$rsCust = $this->isValueExisted($pkey,'email',$email);	
				if(count($rsCust) <> 0) 
					$this->addErrorList($arrayToJs,false,$this->errorMsg['email'][2]);
			 }
		}  
		
		if(!empty($username)){
            
            // kalo dr SSO baru validasi
            // sementara nilainya ambil dr ajax aj dulu
            if(!isset($arr['mnv-OAuth'])){ 
                $rsCust = $this->isValueExisted($pkey,'username',$username);	 
                if(count($rsCust) <> 0){
                    $this->addErrorList($arrayToJs,false,'"'.$username.'", '.$this->errorMsg['username'][2]);
                }

                if (!empty($arr['fromFE'])){
                    $strlen = strlen($username);
                    if ($strlen < 5 || $strlen > 30){
                        $this->addErrorList($arrayToJs,false,$this->errorMsg['username'][3]);
                    }
                }

                if ( !preg_match('/^[a-zA-Z0-9_\.]+$/', $username) ){
                    $this->addErrorList($arrayToJs,false,'"'.$username.'", '.$this->errorMsg['username'][4]); 
                }
            }
		}
        
		foreach($arrUserName as $row){ 
			if ( !preg_match('/^[a-zA-Z0-9_\.]+$/', $row) ) 
				$this->addErrorList($arrayToJs,false,'"'.$row.'", '.$this->errorMsg['username'][4]);   
		}
		
		//validasi username header tidak boleh sama dengan username detail di satu customer
		// kecuali kosong
		$arrAllUserName = array_merge($arrUserName, array($username));
		$arrAllUserName = array_filter($arrAllUserName);
		  
		if(count($arrAllUserName) != count(array_unique($arrAllUserName)))
		  $this->addErrorList($arrayToJs,false,$this->errorMsg['username'][2]);
		             
		//validasi check username header apakah ada username didetail lainnya
		// bisa header ke detail atau detail ke header
        $arrExistingUserName = $this->checkExistingUserName($pkey,$arrAllUserName);
		foreach($arrExistingUserName as $row)
             $this->addErrorList($arrayToJs,false,'"'.$row.'", '.$this->errorMsg['username'][2]);
		
		
        // kalo ad kuisioner
        if(isset($arr['hidQuestionKey']) && !empty($arr['hidQuestionKey'])){
            $hasBlankAnswer = false;
            // harus kasi paramter baru, mandatory atau tdk
            foreach($arr['hidQuestionKey'] as $questionkey){ 
                if (empty($arr['questionnaireAnswer'.$questionkey])){
                    $hasBlankAnswer = true;
                    break;
                }
            }
            
            if($hasBlankAnswer)
                 $this->addErrorList($arrayToJs,false,$this->errorMsg['questionnaire'][2]); 
        }
        
		 if (isset($arr['offerDescription'])){
			 if(strlen($arr['offerDescription']) > 150)
                 $this->addErrorList($arrayToJs,false,$this->errorMsg['profile'][3]);  
		 }
		
		 if (isset($arr['prospectDescription'])){
			 if(strlen($arr['prospectDescription']) > 150)
                 $this->addErrorList($arrayToJs,false,$this->errorMsg['profile'][4]);  
		 }
		
        // agreement 
        if (isset($arr['chkAgree'])){
            if(empty($arr['chkAgree']))
                 $this->addErrorList($arrayToJs,false,$this->errorMsg['registration'][1]); 
        }
		
		//khusus TPA
		if($this->activeModule['customerinsurancepolicy']){
			
			if($arr['selCategory'] == 1) {
                if(empty($arr['policyNumber']))  
                    $this->addErrorList($arrayToJs,false,$this->errorMsg['policyNumber'][1]);
                
                 // kalo individu, asuransi gk boleh lebih dr satu
                if( count($arr['selSupplierKey']) > 1) 
                    $this->addErrorList($arrayToJs,false,$this->errorMsg['policyNumber'][4]); 
            }		
		}
		
			$arrPersonInCharge = $arr['selPersonInCharge'];
			if(!empty($arrPersonInCharge)) {
				//validasi dupliasasi data
				$personInCharge = new PersonInChargeGroup();
				$arrErr = array();
				$arrPICKeys = array();
				for($i=0; $i<count($arrPersonInCharge); $i++) {
					//chek duplikasi
					if (in_array($arrPersonInCharge[$i], $arrPICKeys)) {
						$rsPIC = $personInCharge->getDataRowById($arrPersonInCharge[$i]);
						array_push($arrErr, '<b>' . $rsPIC[0]['name'] . '. </b> ' . $this->errorMsg[215]);
						
					} else {
						array_push($arrPICKeys, $arrPersonInCharge[$i]);
					}
				}

				if(!empty($arrErr)) {
					$this->addErrorList($arrayToJs, false, '<strong>'. $this->lang['personInCharge'] .'. <br></strong>' . implode('<br>', $arrErr));
				}

			}		
		return $arrayToJs;
	 }  
	  
	 function checkExistingUserName($pkey,$arrUserName){
         		 
		 $sql = 'select '.$this->tableName.'.username 
                from '.$this->tableName.'
                where    
					'.$this->tableName.'.username in ('.$this->oDbCon->paramString($arrUserName,',').') and 
					'.$this->tableName.'.pkey <> '.$this->oDbCon->paramString($pkey);
            
		 $rs = $this->oDbCon->doQuery($sql);
		 
		 
        $sql = 'select '.$this->tableDetailAccount.'.username 
                from '.$this->tableDetailAccount.'
                where 
					'.$this->tableDetailAccount.'.username in ('.$this->oDbCon->paramString($arrUserName,',').') and 
					'.$this->tableDetailAccount.'.refkey <> '.$this->oDbCon->paramString($pkey);
            
        $rsDetail = $this->oDbCon->doQuery($sql); 
 
		$arrReturn = array_merge($rs,$rsDetail);
		$arrReturn = array_filter(array_column($arrReturn,'username'));
		  
        return $arrReturn ;
        
    }
	
    function generateDefaultQueryForAutoComplete($returnField){  
        
            $sql = 'select
					'.$returnField['key']. ',  
                    '.$returnField['value']. ' as value,
					concat('.$this->tableName. '.code,\' - \','.$this->tableName. '.name) as codename,
                    '.$this->tableName. '.name as name,
                    '.$this->tableName. '.taxregistrationname,
                    '.$this->tableName. '.address, 
                    '.$this->tableName. '.phone,
                    '.$this->tableName. '.email,
                    '.$this->tableName. '.creditlimit ,
                    '.$this->tableName. '.taxid ,
                    '.$this->tableName. '.termofpaymentkey,
                    '.$this->tableName. '.currencypreference,
                    '.$this->tableName. '.companybankkey,
                    ' . $this->tableBusinessCategory . '.name as mainbusinessname
                    
				from 
					'.$this->tableName . ' 
						left join ' . $this->tableBusinessCategory . ' on ' . $this->tableName . '.mainbusinesskey = ' . $this->tableBusinessCategory . '.pkey  
					,'.$this->tableStatus.' 
				where  		 
					'.$this->tableName . '.statuskey = '.$this->tableStatus.'.pkey  
			'; 
        
            $sql .=  $this->getCustomerCriteria() ; 
        
            return $sql;
        
    } 
       
    function updateAROutstanding($customerkey){
         
		  $arrayToJs = array();
         
         try{	  
				if(!$this->oDbCon->startTrans())
					throw new Exception($this->errorMsg[100]); 
			   
                $ar = new AR();
                $outstanding = $ar->getAROutstanding($customerkey);

                $salesAsAROutstanding = $this->loadSetting('salesAsAROutstanding');
                if($salesAsAROutstanding == 1){ 
                    // kalo trucking harus tambah JO blm diinvoie
                    // sum selisih total JO dengan total invoiced
                    
                    // utk sementara TMS pasti IDR
                    if( $this->activeModule['truckingserviceorder']){ 
                        $truckingServiceOrder = new TruckingServiceOrder();
                        $totalUninvoiced = $truckingServiceOrder->getCustomerUninvoicedAmount($customerkey);
                        $outstanding += $totalUninvoiced;
                    }
                    
                    // nanti tambahkan juga utk sales order
                }
             
                $sql = 'update ' . $this->tableName .' set aroutstanding = ' .  $this->oDbCon->paramString($outstanding) .' where pkey = ' .  $this->oDbCon->paramString($customerkey);
                $this->oDbCon->execute($sql);
				
             //update aroutstanding buildingunit unit
                if( $this->activeModule['buildingunit']){ 
                    $buildingUnit = new BuildingUnit();
                    $buildingUnit->updateAROutstanding($customerkey);
                }
                               $this->oDbCon->endTrans();  
				$this->addErrorList($arrayToJs,true,$this->lang['dataHasBeenSuccessfullyUpdated']);  
			
			} catch(Exception $e){
				$this->oDbCon->rollback();
				$this->addErrorList($arrayToJs,false, $e->getMessage());  
			}	 
      
     } 
    
    function getCustomerCreditLimitSummary($customerkey,$overdue = false, $orderBy=''){
        
        // status non atkif tetpe harus dimunculin
        //$criteria = ' and creditlimit > 0 '; // jgn ditambahin biar keliatan kalo ad yg lupa diset
        
        $criteria = '';
        
        if($overdue)
            $criteria .= ' and '.$this->tableName.'.creditlimit < '.$this->tableName.'.aroutstanding';
            
        $rsCustomer = $this->searchDataRow(
            array($this->tableName.'.pkey',$this->tableName.'.code',$this->tableName.'.name',$this->tableName.'.creditlimit',$this->tableName.'.aroutstanding'),
            $criteria,
            $orderBy
        );
        
        return $rsCustomer;
    }
    
    function willExceedCreditLimit($customerkey,$amount){
        
        $amount = $this->unFormatNumber($amount);
        
        $rs = $this->getDataRowById($customerkey);
        if ($rs[0]['creditlimit'] <= 0 )
            return false;
        
        if ($rs[0]['aroutstanding'] + $amount  > $rs[0]['creditlimit'])
            return true;
        else
            return false;
            
    }
    
//    function getMembershipLevel($pkey = array()){
//
//		$sql = 'select 
//					'.$this->tableMembershipLevel.'.*
//				from 
//					'.$this->tableMembershipLevel.'
//				where
//				   1 =1
//				';
//
//		if(!empty($pkey))
//			$sql .=  ' and '.$this->tableMembershipLevel.'.pkey in ('.$this->oDbCon->paramString($pkey,',').')';
//		 
//        return  $this->oDbCon->doQuery($sql); 
//          
//    }
    
     function updatePassword($arrParam){
        
		$arrayToJs =  array();
			
		try{
			
            if (empty($arrParam['hidUserKey']))
                return;
            
			$id = $arrParam['hidUserKey'];
            
            $rs = $this->getDataRowById($id);
            
			$username = $rs[0]['username'];
            $currentPassword = $arrParam['currentPassword'];
            $password = $arrParam['password'];
            
            if(!$this->checkPassword($id,$username,$currentPassword)){
                $this->addErrorList($arrayToJs,false,$this->errorMsg['username'][5]);  
                return $arrayToJs;
            }
            
            if(!$this->oDbCon->startTrans())
				throw new Exception($this->errorMsg[100]); 
            
            
            $sql = 'update customer set password = \''.hash('sha256',md5($password)).'\' where pkey = ' . $this->oDbCon->paramString($id);
            $this->oDbCon->execute($sql);
      
            $this->setTransactionLog(UPDATE_DATA,$id);
            
			$this->oDbCon->endTrans();  
            
            //send email
            /*
            $companyName = $this->loadSetting('companyName');

            $content =  $this->lang['updatePasswordContent'];
            $emailTemplate = $this->getEmailTemplate(); 

            $patterns = array();
            $patterns[0] = '/({{CONTENT}})/';
            $patterns[1] = '/({{CUSTOMER_NAME}})/'; 
            $patterns[2] = '/({{COMPANY_NAME}})/';

            $replacement = array();
            $replacement[0] = $content;
            $replacement[1] = $rs[0]['name']; 
            $replacement[2] = $companyName;  

            $email = preg_replace($patterns, $replacement, $emailTemplate); 

            $this->sendMail(array(),$this->lang['updatePassword'] .' - ' . $companyName,$email,array('email'=>$rs[0]['email']));	 
            */
            
			$this->addErrorList($arrayToJs,true,$this->lang['dataHasBeenSuccessfullyUpdated']); 
		
	    } catch(Exception $e){
			$this->oDbCon->rollback(); 
			$this->addErrorList($arrayToJs,false,$e->getMessage()); 
		}		
				 
 		return $arrayToJs;  
	   
    }
    
//        
//    function getARCOAKey($customerkey,$warehousekey){ 
//        $coaLink = new COALink();
//        $warehouse = new Warehouse();
//        
//        $rsCustomer = $this->getDataRowById($customerkey);
//        if (!empty($rsCustomer[0]['arcoakey'])){  
//             $coakey = $rsCustomer[0]['arcoakey'];
//        }else{ 
//            $rsCOA = $coaLink->getCOALink ('ar', $warehouse->tableName,  $warehousekey);   
//            $coakey = $rsCOA[0]['coakey'];
//        }
//        
//        return $coakey;
//    }

    function getARCOAKey($customerkey, $warehousekey, $isReimburse = false){ 
        $coaLink = new COALink();
        $warehouse = new Warehouse();
        
        $rsCustomer = $this->getDataRowById($customerkey);

		if ($isReimburse) { 
			// ambil dr reimburse cust
			if (!empty($rsCustomer[0]['reimbursearcoakey']))   return $rsCustomer[0]['reimbursearcoakey']; 
			
			// kalo gk ad ambil dari reimburse warehouse 
			$rsCOA = $coaLink->getCOALink('arreimbursement', $warehouse->tableName, $warehousekey);
			if (!empty($rsCOA[0]['coakey']))   return $rsCOA[0]['coakey'];
  
			// kalo gk ad jg, narik dr AR biasa 
			return $this->getARCOAKey($customerkey, $warehousekey, false);
		}else{ 
			// kalo ad dari AR Customer
			if(!empty($rsCustomer[0]['arcoakey']))   return $rsCustomer[0]['arcoakey'];
		 
			// kalo gk ad, ambil dari AR warehouse
			$rsCOA = $coaLink->getCOALink('ar', $warehouse->tableName, $warehousekey);
			return $rsCOA[0]['coakey']; // ad gk ad, return
		}
 
 
    }
    
    
    
    function getDownpaymentCOAKey($customerkey,$warehousekey){ 
        $coaLink = new COALink();
        $warehouse = new Warehouse();
        
        $rsCustomer = $this->getDataRowById($customerkey);
        if (!empty($rsCustomer[0]['downpaymentcoakey'])){  
             $coakey = $rsCustomer[0]['downpaymentcoakey'];
        }else{ 
            $rsCOA = $coaLink->getCOALink ('customerdownpayment', $warehouse->tableName,  $warehousekey);   
            $coakey = $rsCOA[0]['coakey'];
        }
        
        return $coakey;
    }
    
    function normalizeParameter($arrParam, $trim = false){  

      
		// karena ad function refill data kalo dari API. berarti ini masalah gk kalo dia gk import semua ?
		
        // kalo ad mobile code, omit angka 0
        if(isset($arrParam['selMobileCode']) && !empty($arrParam['selMobileCode']))
            $arrParam['mobile'] = ltrim($arrParam['mobile'],0);
        
        
        // kalo dr fronetend, gk boleh revisi username, kadang kebawa dari autocomplete
        if ($arrParam['fromFE'] && !empty($arrParam['hidId'])){
            unset($arrParam['userName']);
        }
        
		// jggn pake 'pkey', pakenya 'hidId' utk bedain edit atau add
        if(!empty($arrParam['_mnv-api']) && !empty($arrParam['hidId']) ){
            unset($arrParam['password']);  // klo gk di unset, keupdate terus md5nya
        }
		
		// 119 perlu ad overrite kalo memungkinkan
		if( $this->activeModule['customerinsurancepolicy'] ){
		
            if($arrParam['fromFE']){ 
                $rs = $this->searchDataRow(array($this->tableName.'.pkey',
												 $this->tableName.'.name',
												 $this->tableName.'.isinsured',
												 $this->tableName.'.categorykey'
												),
                                            ' and '.$this->tableName.'.pkey = '.$this->oDbCon->paramString($arrParam['pkey'])
                                          );
                
                $rsDetail = $this->getInsuranceCompanyDetail($rs[0]['pkey']);
                $arrSupplierKey =  array_column($rsDetail,'supplierkey');

                $arrParam['name']  = $rs[0]['name'];
                $arrParam['isInsured'] = $rs[0]['isinsured'];
                $arrParam['selCategory'] = $rs[0]['categorykey'];
                $arrParam['selSupplierKey'] = $arrSupplierKey;
            }
			
			if (in_array($arrParam['selCategory'], array(3))) $arrParam['isInsured'] =0; 
			if($arrParam['isInsured'] == 0 )   $arrParam['selSupplierKey'] = array();
            
		}
         
        
        // sementara sampe baseclass bisa mengakomodir
        /* if (!empty($arrParam['fromFE']) || isset($arrParam['_isImport_'])){   
            $this->arrData['pkey'] = array('pkey'); 
        }*/
        
        if (empty($arrParam['password'])){
            unset($this->arrData['password']);
        }else{
            $arrParam['password'] = hash('sha256',md5($arrParam['password']));
        }
        
        // selalu timpa
        $arrParam['activationhashkey'] =  md5($arrParam['pkey'].$arrParam['code'].time().$this->secretKey) ;
            
        
        if(!empty($arrParam['referralCode'])){ 
            $rsCustomer = $this->searchDataRow(array($this->tableName.'.pkey '),
											   'and '.$this->tableName.'.code = ' .$this->oDbCon->paramString( $arrParam['referralCode'])
											  ); 
            $arrParam['hidReferralKey'] = (!empty($rsCustomer)) ? $rsCustomer[0]['pkey'] : 0; 
        }
        
        if (isset($arrParam['chkIsMainAccount']) && $arrParam['chkIsMainAccount'] == 1)
            $arrParam['hidParentKey'] = 0;

        if(isset($arrParam['hidContactPersonDetailKey'])){ 
            for($i=0;$i<count($arrParam['hidContactPersonDetailKey']);$i++) 
                    $arrParam['reftable'][$i] = $this->tableName; 
        }
        
		// overwrite ulang level membership terakhir, kalo edit saja
		if($this->activeModule['membershipsubscription']){ 
			
			$membershipLevel = new MembershipLevel();
			
			if (empty($arrParam['hidId'])){
				$lastLevel = $membershipLevel->getDefaultData();
			}else{
				$arrActivePeriod = $this->calculateActiveSubscriptionPeriod($arrParam['hidId']);   
				$lastLevel = ($arrActivePeriod['level'] > 0) ? $arrActivePeriod['level'] : $membershipLevel->getDefaultData();
				
				if(in_array($arrParam['hidId'], array(8015, 8014,8167, 8171,8184)))
					$lastLevel = 3;
			} 
			
			
			// kalo kosong sama sekali, paling gk select level paling basic
			$arrParam['selMembership'] = $lastLevel;

		}
		
		if(isset($arrParam['selBusinessDetailKey'])){
            for($i=0;$i<count($arrParam['selBusinessDetailKey']);$i++) 
                    $arrParam['hidDetailKey'][$i] = '';  // harus di set agar kesave 
        }
		   
		if(isset($arrParam['selSupplierKey'])){
            for($i=0;$i<count($arrParam['selSupplierKey']);$i++) 
                    $arrParam['hidDetaiSupplierKey'][$i] = '';  // harus di set agar kesave

        }
		
		
		// ICA
		if ($arrParam['chkICA'] <> 1){
			$arrParam['hidICACOAKey'] = 0;
			$arrParam['hidAPICACOAKey'] = 0;
		}
		 
		
        $arrParam = parent::normalizeParameter($arrParam,true); 
        
        return $arrParam;
        
    }
    function getCustomerFile($pkey){
		$sql = 'select * from '.$this->tableFile.' where refkey = '.$this->oDbCon->paramString($pkey).' order by pkey asc';	
		return $this->oDbCon->doQuery($sql);
    } 
	
	function updateFile($pkey,$token,$arrFile){		
	 /*   if (isset($arrParam['_ignore_']) && $arrParam['_ignore_']['itemFile'])
            return;*/
        
        if(!empty($arrFile)) 
            $this->validateDiskUsage(); 
        
		$sourcePath = $this->uploadTempDoc.$this->uploadFileFolder.$token;
		$destinationPath = $this->defaultDocUploadPath.$this->uploadFileFolder;
		
			
		if(!is_dir($destinationPath)) 
			mkdir ($destinationPath,  0755, true);
			
		$destinationPath .= $pkey;  
		 
		
		//delete previous files	    
		$this->deleteAll($destinationPath);  
		$sql = 'delete from '.$this->tableFile.' where refkey = '. $this->oDbCon->paramString($pkey);
		$this->oDbCon->execute($sql);
		
		if(!is_dir($sourcePath))  return;
		
		if (!empty($arrFile))	{
			$arrFile = explode(",",$arrFile);
			for ($i=0;$i<count($arrFile);$i++){   
				$this->uploadImage($sourcePath, $destinationPath,$arrFile[$i]);
				
				$imagekey = $this->getNextKey($this->tableFile);  
				
				$sql = 'insert into '.$this->tableFile.' (pkey,refkey,file) values ('.$this->oDbCon->paramString($imagekey).','.$this->oDbCon->paramString($pkey).','.$this->oDbCon->paramString($arrFile[$i]).')';	
				$this->oDbCon->execute($sql);	 
				 
			}		
		} 
					
	} 
        
    function getTaxInformation($pkey){
        
        $rs = $this->getDataRowById($pkey);
        
        if (empty($rs) || $rs[0]['autotax'] == 0)
            $rs[0]['taxpercentage'] = 0;
        else
            $rs[0]['taxpercentage'] = (!empty($rs[0]['taxid'])) ? 2 : 4;
         
             
        return $rs[0];
        
    }
    
//    function getCustomerSocialMedia($pkey){
//        $sql = 'select
//                    '.$this->tableCustomerSocialMedia.'.*,
//                    '.$this->tableSocialMedia.'.name as socialmedianame 
//                from
//                    '.$this->tableCustomerSocialMedia.'
//				        join '.$this->tableSocialMedia.' on '.$this->tableCustomerSocialMedia . '.socialkey = '.$this->tableSocialMedia.'.pkey 
//                where
//                    '.$this->tableCustomerSocialMedia.'.refkey = '.$this->oDbCon->paramString($pkey);
//        
//        
//        $rs = $this->oDbCon->doQuery($sql);
//        
//        return $rs;
//    }
    
    function getCustomersAge($pkey){ 
        $rs = $this->getDataRowById($pkey);
        
        $today = date('Y-m-d');
        $dateOfBirth = $rs[0]['dateofbirth'];
        $diff = date_diff(date_create($dateOfBirth), date_create($today));
        
        $age = $diff->format('%y');

        return $age;
        
    }

    function getLocationInformation($pkey){ 
         $sql = 'select
                   '.$this->tableName.'.citykey,
                   '.$this->tableCity.'.categorykey as citycategorykey,
                   concat ('.$this->tableCity. '.name, ", ", '.$this->tableCityCategory.'.name) as cityandcategoryname
               from
                   '.$this->tableName.'
				        left join '.$this->tableCity.' on '.$this->tableName . '.citykey = '.$this->tableCity.'.pkey 
				        left join '.$this->tableCityCategory.' on '.$this->tableCity . '.categorykey = '.$this->tableCityCategory.'.pkey 
               where
                   '.$this->tableName.'.pkey = '.$this->oDbCon->paramString($pkey);
       
       
       $rs = $this->oDbCon->doQuery($sql);
       
       return $rs;
        
    }
    
    function getSalesman($pkey){
		$employee = new Employee();
		
		if (!is_array($pkey)){ 
			$rs = $this->getDataRowById($pkey); 
            if(empty($rs[0]['saleskey'])) return array();
             
			$rs = $employee->getDataRowById($rs[0]['saleskey']); 
            
			return (empty($rs)) ? array() : $rs[0];
		}else{
			$sql = 'select '.$this->tableName.'.pkey as customerkey, '.$employee->tableName.'.* from 
						'.$employee->tableName.','.$this->tableName.'
					where
						'.$this->tableName.'.saleskey = '.$employee->tableName.'.pkey and
						'.$this->tableName.'.pkey in ('.$this->oDbCon->paramString($pkey,',').')
					';
			
			return $this->oDbCon->doQuery($sql);
		}
        
    }
	 
	function calculateActiveSubscriptionPeriod($pkey){
		$membershipSubscription = new MembershipSubscription();
		
		// utk data membership level harus query terpisah
		$rs = $membershipSubscription->searchDataRow(array($this->tableMembershipSubscription.'.membershiplevelkey'),
								  ' and '.$this->tableMembershipSubscription.'.customerkey =  ' .$this->oDbCon->paramString($pkey).' 
								    and '.$this->tableMembershipSubscription.'.statuskey in (2,3)',
								  ' order by '.$this->tableMembershipSubscription.'.trdate desc, '.$this->tableMembershipSubscription.'.pkey desc limit 1'
								  ); 
		$membershipLevelkey = (!empty($rs)) ? $rs[0]['membershiplevelkey'] : 0;
		
		$sql = 'select 
					coalesce( sum('.$this->tableMembershipSubscription.'.activeperiodmonth) ,0) as totalactiveperiod, 
					count('.$this->tableMembershipSubscription.'.pkey) as totalsubscription,
					min('.$this->tableMembershipSubscription.'.trdate) as firstsubscriptiondate
				from '.$this->tableMembershipSubscription.' 
				where 
					'.$this->tableMembershipSubscription.'.customerkey =  ' .$this->oDbCon->paramString($pkey) .' and
					'.$this->tableMembershipSubscription.'.statuskey in (2,3)
				order by trdate desc';
		 
		$rs = $this->oDbCon->doQuery($sql);
		
		return array('activeperiodmonth' => $rs[0]['totalactiveperiod'],
					 'level' => $membershipLevelkey,
					 'totalsubscription' => $rs[0]['totalsubscription'],
					 'firstsubscriptiondate' => $rs[0]['firstsubscriptiondate']
					);
	}
	
	function updateExpDate($pkey){
	
		try{ 
		 
			 if(!$this->oDbCon->startTrans())
				throw new Exception($this->errorMsg[100]);
 
				$membershipSubscription = new MembershipSubscription();
				$membershipLevel = new MembershipLevel();
			
				$rsCustomer = $this->getDataRowById($pkey);
			
				// ambil semua transaksi karena perlu hitung ulang total brp thn user perpanjang ?
			 	$arrActivePeriod = $this->calculateActiveSubscriptionPeriod($pkey);
 				//$this->setLog($arrActivePeriod,true);
			
				$totalSubscription = $arrActivePeriod['totalsubscription'];
				$totalActivePeriod = $arrActivePeriod['activeperiodmonth'];
				$lastLevel = $arrActivePeriod['level'];
				$firstSubsDate  = $arrActivePeriod['firstsubscriptiondate'];
					
				// cari pernah ad subscription tdk, atau semua dicancel
				if($totalSubscription <=0 ){
 					$expDate = '\'0000-00-00\'';
 					$firstSubsDate = '\'0000-00-00\'';
					$lastLevel = $membershipLevel->getDefaultData(); //reset jd awal
				}else{  
					$date = new DateTime($firstSubsDate); 
					$date->add(new DateInterval('P'.$totalActivePeriod.'M'));    
					$expDate = $this->oDbCon->paramString($date->format('Y-m-d')); 
					$firstSubsDate = $this->oDbCon->paramString($firstSubsDate);
				}
			
				$sql = 'update 
					'.$this->tableName.' 
				set  
					'.$this->tableName.'.expdate = '.$expDate.',
					'.$this->tableName.'.subscriptionactivationdate = '.$firstSubsDate.', 
					'.$this->tableName.'.membershiplevel = '.$this->oDbCon->paramString($lastLevel).'
				where  
					'.$this->tableName.'.pkey = ' . $this->oDbCon->paramString($pkey);  
 				//$this->setLog($sql,true);
				$this->oDbCon->execute($sql);
 
				$this->oDbCon->endTrans();  
            
		} catch(Exception $e){
			$this->oDbCon->rollback(); 
			$this->addErrorList($arrayToJs,false, $e->getMessage()); 
			
		}		 
		
	}
	    
   
    function getCustomerBusinessDetail($pkey){
        $sql = 'select
                    '.$this->tableCustomerBusiness.'.*,
                    '.$this->tableBusinessCategory.'.name as businessname
                from
                    '.$this->tableCustomerBusiness.',
                    '.$this->tableBusinessCategory.'
                where
                    '.$this->tableCustomerBusiness.'.refbusinesskey = '.$this->tableBusinessCategory.'.pkey and
                    '.$this->tableCustomerBusiness.'.refkey = '.$this->oDbCon->paramString($pkey);
         
        $rs = $this->oDbCon->doQuery($sql);
        
        return $rs;
    }
	
    function getItemAliasDetail($pkey, $arrItemKey=array()){
        $sql = 'select
                    '.$this->tableDetailItem.'.*,
                    ' . $this->tableItem . '.name as itemname
                from
                    '.$this->tableDetailItem.',
                    '.$this->tableItem.'
                where
                    '.$this->tableDetailItem . '.itemkey = '.$this->tableItem.'.pkey and
                    '.$this->tableDetailItem.'.refkey = '.$this->oDbCon->paramString($pkey);
         
		if(!empty($arrItemKey))
			$sql .= ' and '.$this->tableDetailItem . '.itemkey in ('.$this->oDbCon->paramString($arrItemKey,',').')' ;
		
        $rs = $this->oDbCon->doQuery($sql);
        
        return $rs;
    }
	  
	 // overwrite karena ad warehouse Criteria
	function generateComboboxOpt($opt = array(),$queryOpt = array(),$preselected='',$relOpt = array()){
		// nanti dilihat perlu isset gk, atau selalu ditambahkan saja
		if(isset($queryOpt['criteria'])) $queryOpt['criteria'] .=  $this->getCustomerCriteria() ; 
		return parent::generateComboboxOpt($opt,$queryOpt,$preselected ,$relOpt );
	}
	
    function updateSettings($arr){
		//biar gk kena validasi validateForm
		
		$arrayToJs = array();		
		
		if(empty($arr['hidId'])) return;

		$arrUpdate = array(); 
		
		if (isset($arr['selLang'])) array_push($arrUpdate , $this->tableName.'.langkey = '.$this->oDbCon->paramString($arr['selLang']) );
		if (isset($arr['selTimeZone'])) array_push($arrUpdate , $this->tableName.'.gmt = '.$this->oDbCon->paramString($arr['selTimeZone']) );
        
        
		if (isset($arr['selEmailPrivacyKey'])) array_push($arrUpdate , $this->tableName.'.emailprivacykey = '.$this->oDbCon->paramString($arr['selEmailPrivacyKey']) );
		if (isset($arr['selMobilePrivacyKey'])) array_push($arrUpdate , $this->tableName.'.mobileprivacykey = '.$this->oDbCon->paramString($arr['selMobilePrivacyKey']) );
			
		
		if(empty($arrUpdate)) return;

		try{ 

		 if(!$this->oDbCon->startTrans())
			throw new Exception($this->errorMsg[100]);

				
			$sql = 'update '.$this->tableName.'
					set '.implode(',',$arrUpdate).'
					where  '.$this->tableName.'.pkey = '.$this->oDbCon->paramString($arr['hidId']).' 
			';

			$this->oDbCon->execute($sql);
			 
            if($this->activeModule['activitylog']){ 
				$arrActivityLog = array();
				array_push($arrActivityLog, 
										array( 
												'modulekey' => 3, 
												'templatekey' => 17, 
												'refkey' => $arr['hidId'],  
                                                'userkey' => $arr['hidId']
											) 
								); 

				$activityLog = new ActivityLog();
				$activityLog->addNewLog($arrActivityLog);  
			}
				
            // kalo ad session
            if(isset($_SESSION[$this->loginSession]) && isset($arr['selTimeZone']))  $_SESSION[$this->loginSession]['gmt'] = $arr['selTimeZone'];  
            
			$this->oDbCon->endTrans();  
			$this->addErrorList($arrayToJs,true,$this->lang['dataHasBeenSuccessfullyUpdated']);  

		} catch(Exception $e){
			$this->oDbCon->rollback(); 
			$this->addErrorList($arrayToJs,false, $e->getMessage());  
		}		
 	
		return $arrayToJs;
	}
	
    
//	function updatePrivacy($arr){
//		//biar gk kena validasi validateForm
//		
//		$arrayToJs = array();		
//		
//		if(empty($arr['hidId'])) return;
//
//		$arrUpdate = array(); 
//		
//			
//		
//		if(empty($arrUpdate)) return;
//
//		try{ 
//
//		 if(!$this->oDbCon->startTrans())
//			throw new Exception($this->errorMsg[100]);
//
//				
//			$sql = 'update '.$this->tableName.'
//					set '.implode(',',$arrUpdate).'
//					where  '.$this->tableName.'.pkey = '.$this->oDbCon->paramString($arr['hidId']).' 
//			';
//
//			$this->oDbCon->execute($sql);
//			 
//			if($this->activeModule['activitylog']){ 
//				$arrActivityLog = array();
//				array_push($arrActivityLog, 
//										array( 
//												'modulekey' => 3, 
//												'templatekey' => 17, 
//												'refkey' => $arr['hidId'],  
//                                                'userkey' => $arr['hidId']
//											) 
//								); 
//
//				$activityLog = new ActivityLog();
//				$activityLog->addNewLog($arrActivityLog);  
//			}
//				
//			$this->oDbCon->endTrans();  
//			$this->addErrorList($arrayToJs,true,$this->lang['dataHasBeenSuccessfullyUpdated']);  
//
//		} catch(Exception $e){
//			$this->oDbCon->rollback(); 
//			$this->addErrorList($arrayToJs,false, $e->getMessage());  
//		}		
// 	
//		return $arrayToJs;
//	}
	
	function getGamificationStat($pkey){
		
		$arrReturn = array();
		
		$customer = new Customer();
		$rsCustomer = $customer->searchDataRow(array($customer->tableName.'.pkey',$customer->tableName.'.membershiplevel',$customer->tableName.'.hostlevelkey',$customer->tableName.'.totaljoin',$customer->tableName.'.totalhost'),
											  ' and ' . $customer->tableName.'.pkey = ' . $this->oDbCon->paramString($pkey));
		
		$toNextLevel = 10;
		$maxLevel = 50;
		
		$exp = 0 ;
		$exp = $rsCustomer[0]['membershiplevel'] * $toNextLevel;
		 
		// utk hostmeeting
		$exp += ($rsCustomer[0]['hostlevelkey'] * $toNextLevel);
		
		if($exp > $maxLevel) $exp = $maxLevel;
		
        
		$arrReturn['totalMeeting'] = ($rsCustomer[0]['totalhost'] + $rsCustomer[0]['totaljoin']);
        
        $nextMeetingNeeded = $toNextLevel - $arrReturn['totalMeeting'];
        if ($nextMeetingNeeded < 0) $nextMeetingNeeded = 0; 
		$arrReturn['nextMeetingNeeded'] = $nextMeetingNeeded;
		
		$arrReturn['hostLevelKey'] =  $rsCustomer[0]['hostlevelkey'];
		
		$nextHostLevelName = '';
		 
		if ($rsCustomer[0]['hostlevelkey'] == 0)
			$nextHostLevelName = 'Host';
		else if ($rsCustomer[0]['hostlevelkey'] == 1)
			$nextHostLevelName = 'Master Host';
			
		$arrReturn['nextHostLevelName'] = $nextHostLevelName;
		
		$arrReturn['expPercentage'] = $exp/$maxLevel * 100;
			
		return $arrReturn;
	}
	
	function updateExpiredMemberStatus($defaultLevel = 1){
		// tdk ubah status member, hanya membership level saja
		// nanti tambahin opsi kalo perlu ganti status jg
		
		try{ 

		 if(!$this->oDbCon->startTrans())
			throw new Exception($this->errorMsg[100]);

				
			$sql = 'update '.$this->tableName.'
					set '.$this->tableName.'.membershiplevel = '.$this->oDbCon->paramString($defaultLevel).',
					'.$this->tableName.'.expdate = \'0000-00-00\' 
					where 
						'.$this->tableName.'.expdate < now() and  
						'.$this->tableName.'.membershiplevel > '.$this->oDbCon->paramString($defaultLevel).'  
						
			';
            
//            $sql .= ' and '.$this->tableName.'.pkey = 8153';
            
			//and  '.$this->tableName.'.statuskey = 2 
 
			$this->oDbCon->execute($sql);
				
			$this->oDbCon->endTrans();  
			$this->addErrorList($arrayToJs,true,$this->lang['dataHasBeenSuccessfullyUpdated']);  

		} catch(Exception $e){
			$this->oDbCon->rollback(); 
			$this->addErrorList($arrayToJs,false, $e->getMessage());  
		}		
 	
	}
	
 	function updateBankInformation($arr){
		
		$arrayToJs = array();
		
		try{ 

		 if(!$this->oDbCon->startTrans())
			throw new Exception($this->errorMsg[100]);

				
			$pkey = $arr['pkey'];
			$bankName = $arr['bankName'];
			$bankAccountName = $arr['bankAccountName'];
			$bankAccountNumber = $arr['bankAccountNumber'];
			$taxId = $arr['taxid'];

			$sql = 'update '.$this->tableName.' set
						bankname = '.$this->oDbCon->paramString($bankName).', 	
						bankaccountname = '.$this->oDbCon->paramString($bankAccountName).', 	
						bankaccountnumber = '.$this->oDbCon->paramString($bankAccountNumber).', 	
						taxid = '.$this->oDbCon->paramString($taxId).' 
					where 
						'.$this->tableName.'.pkey = '.$this->oDbCon->paramString($pkey).' 
					';

			$this->oDbCon->execute($sql);
				
			if($this->activeModule['activitylog']){ 
				$arrActivityLog = array();
				array_push($arrActivityLog, 
										array( 
												'modulekey' => 6, 
												'templatekey' => 17, 
												'refkey' =>$pkey,  
                                                'userkey' => $arr['pkey']
											) 
								); 

				$activityLog = new ActivityLog();
				$activityLog->addNewLog($arrActivityLog);  
			}
			
			$this->oDbCon->endTrans();  
			$this->addErrorList($arrayToJs,true,$this->lang['dataHasBeenSuccessfullyUpdated']);  

		} catch(Exception $e){
			$this->oDbCon->rollback(); 
			$this->addErrorList($arrayToJs,false, $e->getMessage());  
		}		
		
		return $arrayToJs;
		
	} 
	
	function autoUpdateData($arrKey,$arrParam){
		 
		$returnRs = array();
		
		$rs = $this->searchDataRow( array($this->tableName.'.pkey',$this->tableName.'.code',$this->tableName.'.modifiedon') ,
								   ' and '.$this->tableName.'.'.$arrKey['field']. '=' .$this->oDbCon->paramString($arrKey['value'])
								  );
		 
		try{ 
		 
			 if(!$this->oDbCon->startTrans())
				throw new Exception($this->errorMsg[100]);
				 
               
				$arr = array();
				$arr['name'] = $arrParam['name'];
				$arr['mobile'] = $arrParam['mobile'];	
				$arr['phone'] = $arrParam['phone'];	
				$arr['address'] = $arrParam['address'];	
				$arr['selStatus'] = $arrParam['statuskey'];	
				$arr['hidCityKey'] = $arrParam['citykey'];	

				if (!empty($rs)){  
					$arr['hidId'] = $rs[0]['pkey'];	
					$arr['hidModifiedOn'] = $rs[0]['modifiedon'];	
					$arr['code'] =  $rs[0]['code'];	
					 
					$returnRs = $this->editData($arr);

				}else{ 

					$arr['code'] = 'xxxx';	   
					$returnRs = $this->addData($arr);
				}
 
				$this->oDbCon->endTrans(); 
 
		} catch(Exception $e){
			$this->oDbCon->rollback();   
		}		 
		
		// select ulang
		
		return $returnRs[0]['data'];
	}
	
	 function getInsuranceCompanyDetail($pkey, $supplierkey = ''){
        $sql = 'select
                    '.$this->tableInsuranceCompany.'.*,
                    '.$this->tableSupplier.'.name as suppliername
                from
                    '.$this->tableInsuranceCompany.',
                    '.$this->tableSupplier.'
                where
                    '.$this->tableInsuranceCompany.'.supplierkey = '.$this->tableSupplier.'.pkey and
                    '.$this->tableInsuranceCompany.'.refkey = '.$this->oDbCon->paramString($pkey);
         
        if(!empty($supplierkey)) {
            $sql .= ' and '.$this->tableInsuranceCompany.'.supplierkey = '.$this->oDbCon->paramString($supplierkey);
        }
		 
		 
        $rs = $this->oDbCon->doQuery($sql);
        return $rs;
    }
	
	function getAddressForInvoice($pkey){
		
		$rs = $this->searchDataRow(array($this->tableName.'.address',$this->tableName.'.taxregistrationaddress'),
								   ' and '.$this->tableName.'.pkey = ' .$this->oDbCon->paramString($pkey)
								  );
		
		$arrAddress = array();
		
		// default address
		array_push($arrAddress, array('pkey' => -1, 'name' => $this->lang['companyAddress'], 'value' => $rs[0]['address'], 'longdescription' => $this->lang['companyAddress'].', '.$rs[0]['address']));
		array_push($arrAddress, array('pkey' => -2, 'name' => $this->lang['taxRegistrationAddress'], 'value' => $rs[0]['taxregistrationaddress'], 'longdescription' => $this->lang['taxRegistrationAddress'].', '.$rs[0]['taxregistrationaddress'] ));
		
        // pake -999 biar urutannya rapi pas saat reiinsert selectbox
        array_push($arrAddress, array('pkey' => -999, 'name' => $this->lang['others'], 'value' => '', 'longdescription' => '' ));
		
		$rsShippingAddress = $this->getMultipleAddress($pkey,1);
    	foreach($rsShippingAddress as $row)
			array_push($arrAddress, array('pkey' => $row['pkey'], 'name' => $row['name'], 'value' => $row['address'], 'longdescription' =>  $row['name'].', '.$row['address']));
		
		//$this->setLog($arrAddress,true);
		return $arrAddress;
	}
	
	function getSupplierLink($arrPkey){
		// jgn pake left join karena diajax perlu return empty row kalo gk nemu
		
		if(!is_array($arrPkey)) $arrPkey = array($arrPkey);
		
		$sql = 'select 	
					'.$this->tableName.'.pkey,
					'.$this->tableName.'.supplierlinkkey as supplierkey,
					'.$this->tableSupplier.'.name as suppliername
				from '.$this->tableName.', '.$this->tableSupplier.'
				where  '.$this->tableName.'.supplierlinkkey =  '.$this->tableSupplier.'.pkey
					and  '.$this->tableSupplier.'.statuskey = 1
					and  '.$this->tableName.'.pkey in ('.$this->oDbCon->paramString($arrPkey,',').')
				';
		
		return $this->oDbCon->doQuery($sql);
	}
	

	function getAccountDetail($pkey){
        
        $sql = 'select
                    '.$this->tableDetailAccount.'.*
                from
                    '.$this->tableDetailAccount.'
                where
                    '.$this->tableDetailAccount.'.refkey = '.$this->oDbCon->paramString($pkey);
         
        $rs = $this->oDbCon->doQuery($sql);
        
        return $rs;
    }
	
	function loginAccountDetail($username,$password){
		// sementara pass gk dienkrip
		
		$sql = 'select 
						'.$this->tableName.'.pkey,
						'.$this->tableName.'.name,
						'.$this->tableName.'.statuskey,
						'.$this->tableDetailAccount.'.rolekey,
						'.$this->tableDetailAccount.'.username,
						'.$this->tableDetailAccount.'.password
				from '.$this->tableName.' ,'.$this->tableDetailAccount.' 
				where 
					'.$this->tableName.'.pkey = '.$this->tableDetailAccount.'.refkey and
					'.$this->tableDetailAccount.'.username = '.$this->oDbCon->paramString($username).' and 
					'.$this->tableDetailAccount.'.password = '.$this->oDbCon->paramString($password).' and
					'.$this->tableName.'.statuskey in (2) 
				';
		
        return $this->oDbCon->doQuery($sql);
		
	}
	
	function getPortalMenu($loginType){
		
		$arrMenu = array();
		$arrMenu['dashboard'] = array('url' => '/dashboard', 'title' => $this->lang['dashboard'] );
		$arrMenu['job-order-list'] = array('url' => '/job-order-list', 'title' => $this->lang['jobOrder'],  'icon' => 'icon-job-order.png');
		$arrMenu['work-order-map'] = array('url' => '/work-order-map');
		$arrMenu['ajax-marker'] = array('url' => '/ajax-marker');
		$arrMenu['ajax-work-order-marker'] = array('url' => '/ajax-work-order-marker');
		$arrMenu['invoice-trucking-list'] = array('url' => '/invoice-trucking-list', 'title' => $this->lang['invoice'],  'icon' => 'icon-invoice.png');
		$arrMenu['ar-outstanding-list'] = array('url' => '/ar-outstanding-list', 'title' => $this->lang['accountsReceivable'],  'icon' => 'icon-ar.png');
		$arrMenu['ar-payment-list'] = array('url' => '/ar-payment-list', 'title' => $this->lang['arPayment'],  'icon' => 'icon-ar-payment.png');
		$arrMenu['prepaid-tax23-list'] = array('url' => '/prepaid-tax23-list', 'title' => $this->lang['prepaidTax23'],  'icon' => 'icon-prepaid-tax23.png');
		$arrMenu['ap-outstanding-list'] = array('url' => '/ap-outstanding-list', 'title' => $this->lang['accountsPayable'],  'icon' => 'icon-ap.png');
		$arrMenu['ap-payment-list'] = array('url' => '/ap-payment-list', 'title' => $this->lang['apPayment'],  'icon' => 'icon-ap-payment.png');
//		$arrMenu['payable-tax23-list'] = array('url' => '/payable-tax23-list', 'title' => $this->lang['payableTax23'],  'icon' => 'icon-payable-tax23.png');
		$arrMenu['payable-tax23-payment-list'] = array('url' => '/payable-tax23-payment-list', 'title' => $this->lang['withholdingCodeArt23'],  'icon' => 'icon-payable-tax23-payment.png');
		$arrMenu['news'] = array('url' => '/customer-news', 'title' => $this->lang['news']);
		$arrMenu['logout'] = array('url' => '/logout', 'title' => $this->lang['logout']);
		
 
 		$arrReturn = array();
		array_push($arrReturn,$arrMenu['dashboard'] );
					
		switch($loginType){
			case 1 :  array_push($arrReturn,$arrMenu['job-order-list'] );
					  array_push($arrReturn,$arrMenu['work-order-map'] );
					  array_push($arrReturn,$arrMenu['ajax-marker'] );
					  array_push($arrReturn,$arrMenu['ajax-work-order-marker'] );
					  array_push($arrReturn,$arrMenu['invoice-trucking-list'] );
					  array_push($arrReturn,$arrMenu['ar-outstanding-list'] );
					  array_push($arrReturn,$arrMenu['ar-payment-list'] );
					  array_push($arrReturn,$arrMenu['prepaid-tax23-list'] ); 
					  array_push($arrReturn,$arrMenu['ap-outstanding-list'] );
					  array_push($arrReturn,$arrMenu['ap-payment-list'] ); 
					  array_push($arrReturn,$arrMenu['payable-tax23-payment-list'] ); 
//					  array_push($arrReturn,$arrMenu['payable-tax23-list'] ); 
					break;
			case 8001 : 
					 array_push($arrReturn,$arrMenu['invoice-trucking-list'] );
					 array_push($arrReturn,$arrMenu['ar-outstanding-list'] );
					 array_push($arrReturn,$arrMenu['ar-payment-list'] );
					 array_push($arrReturn,$arrMenu['prepaid-tax23-list'] ); 
					break;	
			case 8002 : 
					 array_push($arrReturn,$arrMenu['job-order-list'] ); 
					 array_push($arrReturn,$arrMenu['work-order-map'] );
					 array_push($arrReturn,$arrMenu['ajax-marker'] );
					  array_push($arrReturn,$arrMenu['ajax-work-order-marker'] );
					 break;	
			case 8003 : 
					 array_push($arrReturn,$arrMenu['ap-outstanding-list'] );
					 array_push($arrReturn,$arrMenu['ap-payment-list'] ); 
//					 array_push($arrReturn,$arrMenu['payable-tax23-list'] ); 
					 array_push($arrReturn,$arrMenu['payable-tax23-payment-list'] ); 
					 break;	
				
		}
		
		
		array_push($arrReturn,$arrMenu['news'] );
		array_push($arrReturn,$arrMenu['logout'] );
		
		return $arrReturn;
	}
	
	function getInvoicingType()
    {
        $sql = '
            select
                '. $this->tableInvoicingType  .'.*
            from 
                '. $this->tableInvoicingType  .'
        ';

        $rs = $this->oDbCon->doQuery($sql);
        return $rs;
    }

	
	// buat import default payment methodnya BOS
	function getDefaultPaymentBank($arrId = array() ){
		if(!is_array($arrId)) $arrId = array($arrId);
		
 		$paymentMethod = new PaymentMethod();
		$sql = 'select 
					'.$this->tableName.'.pkey, 
					'.$this->tableName.'.companybankkey,
					'.$paymentMethod->tableName.'.code,
					'.$paymentMethod->tableName.'.name
				from
					'.$this->tableName.', '.$paymentMethod->tableName.'
				where
					'.$this->tableName.'.pkey  in ('. $this->oDbCon->paramString($arrId,',').') and 
					'.$this->tableName.'.companybankkey =  '.$paymentMethod->tableName.'.pkey and 
					'.$paymentMethod->tableName.'.statuskey = 1
				';
		
		$rsPaymentMethod = $this->oDbCon->doQuery($sql);
		
		return $rsPaymentMethod;
	}

	public function getPICGroupDetail($pkey) 
	{
		$sql = '
			select
				'. $this->tableCustomerPersonInCharge .'.*,
				'. $this->tablePersonInChargeHeader.'.name as personinchergename
			from
				'. $this->tableCustomerPersonInCharge .'
				left join '. $this->tablePersonInChargeHeader .' on '. $this->tableCustomerPersonInCharge .'.personinchargekey = '. $this->tablePersonInChargeHeader .'.pkey
			where
				refkey in ('.$this->oDbCon->paramString($pkey,',') . ')
				order by '.$this->tableCustomerPersonInCharge .'.pkey asc
		';
 
		return $this->oDbCon->doQuery($sql);
	}

	public function getPersonInChargeByCustomer($pkey = '') {
		$rsData = $this->getPICGroupDetail($pkey);

		$arrpersoninchargekey = (empty($rsData) ? 0 : array_column($rsData, 'personinchargekey'));

		$sql = '
			select 
				'. $this->tablePersonInChargeDetail .'.*,
				'. $this->tableSupplier .'.name as value
			from
				'. $this->tablePersonInChargeDetail .'
				left join '. $this->tableSupplier .' on '. $this->tablePersonInChargeDetail .'.supplierkey = '. $this->tableSupplier .'.pkey,
				'. $this->tablePersonInChargeHeader .'
			where
				'. $this->tablePersonInChargeDetail .'.refkey in ('.$this->oDbCon->paramString($arrpersoninchargekey,',') . ') and
				'. $this->tablePersonInChargeDetail .'.refkey = '. $this->tablePersonInChargeHeader .'.pkey and
				'. $this->tablePersonInChargeHeader .'.statuskey = 1
		';

		$rs = $this->oDbCon->doQuery($sql);
      return $rs;

	}
    
   function getTinType(){
        $sql = 'select * from '.$this->tableTinType.' where statuskey = 1';
        return $this->oDbCon->doQuery($sql);
    }


	function getShippingMultiLocation($pkey, $criteria){

        $sql = 'select
                    '.$this->tableMultipleAddress.'.*,
					concat ('.$this->tableCity. '.name, ", ", '.$this->tableCityCategory.'.name) as cityandcategoryname
                from
                    '.$this->tableMultipleAddress.'
						left join '.$this->tableCity.' on '.$this->tableMultipleAddress . '.citykey = '.$this->tableCity.'.pkey
						left join '.$this->tableCityCategory.' on '.$this->tableCity . '.categorykey = '.$this->tableCityCategory.'.pkey
                where
                    '.$this->tableMultipleAddress.'.refkey = '.$this->oDbCon->paramString($pkey);
    
		if (!empty($criteria)) {
			$sql .= $criteria;
		}

        $rs = $this->oDbCon->doQuery($sql);

        return $rs;
    }

	function editMultiAddress($arr){
 
		$arrayToJs =  array();
			
		try{
			
			if (empty($arr['hidUserKey']))
				return;
			
			if(!$this->oDbCon->startTrans())
				throw new Exception($this->errorMsg[100]); 
	
			$sql = 'update 
						' .$this->tableMultipleAddress. ' 
					set 
						name = '.$this->oDbCon->paramString($arr['name']).',
						pic = '.$this->oDbCon->paramString($arr['pic']).',
						phone = '.$this->oDbCon->paramString($arr['phone']).',
						address = '.$this->oDbCon->paramString($arr['address']).',
						zipcode = '.$this->oDbCon->paramString($arr['zipcode']).',
						latlng = '.$this->oDbCon->paramString($arr['hidLatLng']).',
						trdesc = '.$this->oDbCon->paramString($arr['trDesc']).',
						isprimary = '.$this->oDbCon->paramString($arr['chkIsPrimary']).'
					where 
						pkey = ' . $this->oDbCon->paramString($arr['pkey']) ; //detail pkey
						
			$this->oDbCon->execute($sql);
			
			// kalo primary, udpate yg lain jg non primary
			if($arr['chkIsPrimary'] == 1){
					$sql = 'update  ' .$this->tableMultipleAddress. ' 
							set  isprimary = 0
							where 
								refkey = ' . $this->oDbCon->paramString($arr['hidUserKey']) .' and  
								pkey != ' . $this->oDbCon->paramString($arr['pkey']) ;  
					$this->oDbCon->execute($sql);
			}
			
			// $this->setLog($sql,true);
	  
			$this->setTransactionLog(UPDATE_DATA,$id);
			
			$this->oDbCon->endTrans();  
			
			$this->addErrorList($arrayToJs,true,$this->lang['dataHasBeenSuccessfullyUpdated']); 
		
		} catch(Exception $e){
			$this->oDbCon->rollback(); 
			$this->addErrorList($arrayToJs,false,$e->getMessage()); 
		}		
				 
		 return $arrayToJs;  
	}

	function addMultiAddress($arr){

		$arrayToJs =  array();
			
		try{
			
            if (empty($arr['hidUserKey']))  return;
            
            if(!$this->oDbCon->startTrans())
				throw new Exception($this->errorMsg[100]); 
            
			
			// kalo primary, udpate yg lain jg non primary, harus diatas sebelum query
			if($arr['chkIsPrimary'] == 1){
					$sql = 'update  ' .$this->tableMultipleAddress. ' 
							set  isprimary = 0
							where  refkey = ' . $this->oDbCon->paramString($arr['hidUserKey']);  
					$this->oDbCon->execute($sql);
			}
			
			
            $sql = 'insert into  '.$this->tableMultipleAddress.' (refkey,reftable,name,address,zipcode,typekey, pic,phone,latlng,trdesc, isprimary) values ('.$this->oDbCon->paramString($arr['hidUserKey']).', '.$this->oDbCon->paramString($this->tableName).', '.$this->oDbCon->paramString($arr['name']).', '.$this->oDbCon->paramString($arr['address']).', '.$this->oDbCon->paramString($arr['zipcode']).', 1, '.$this->oDbCon->paramString($arr['pic']).', '.$this->oDbCon->paramString($arr['phone']).', '.$this->oDbCon->paramString($arr['hidLatLng']).', '.$this->oDbCon->paramString($arr['trDesc']).', '.$this->oDbCon->paramString($arr['chkIsPrimary']).' )';
			$this->oDbCon->execute($sql);
      
            $this->setTransactionLog(UPDATE_DATA,$id);
            
			$this->oDbCon->endTrans();
			
			$this->addErrorList($arrayToJs,true,$this->lang['dataHasBeenSuccessfullyUpdated']); 
		
	    } catch(Exception $e){
			$this->oDbCon->rollback(); 
			$this->addErrorList($arrayToJs,false,$e->getMessage()); 
		}		
				 
 		return $arrayToJs;  
	}
	
 
function delMultiaddress($arr){
		// $this->setLog($arr,true);
		
		$arrayToJs =  array();
		
		try{
			if (empty($arr)) return; 
            
            if(!$this->oDbCon->startTrans())
				throw new Exception($this->errorMsg[100]); 


			$sql = 'delete from '.$this->tableMultipleAddress.' where pkey = '. $this->oDbCon->paramString($arr);
						
			$this->oDbCon->execute($sql);
      
            $this->setTransactionLog(UPDATE_DATA,$id);
            
			$this->oDbCon->endTrans();  
            
			$this->addErrorList($arrayToJs,true,$this->lang['dataHasBeenSuccessfullyUpdated']); 
		
	    } catch(Exception $e){
			$this->oDbCon->rollback(); 
			$this->addErrorList($arrayToJs,false,$e->getMessage()); 
		}		
				 
 		return $arrayToJs;  
	}
	
  }

?>
