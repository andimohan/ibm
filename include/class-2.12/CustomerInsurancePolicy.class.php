<?php  
// data yang diambil dr polis adalah data yg tdk bisa berubah, seperti : nama, tgl lahir, 
// sisanya disimpan sebagai histori ditransaksi

class CustomerInsurancePolicy extends BaseClass{
 
   function __construct(){
		
		parent::__construct();
		
        $this->tableName = 'customer_insurance_policy';
		$this->tableStatus = 'master_status';
        $this->tableSupplier = 'supplier';
        $this->tableCustomer = 'customer';
        $this->tableCustomerCategory = 'customer_category'; 
        $this->tableCountry = 'country';
	   
		$this->securityObject = 'CustomerInsurancePolicy'; 

        $this->arrData = array();
        $this->arrData['pkey'] = array('pkey');
        $this->arrData['code'] = array('code');
        $this->arrData['refkey'] = array('hidRefKey');
        $this->arrData['name'] = array('name');
        $this->arrData['phone'] = array('phone');
        $this->arrData['mobile'] = array('mobile');
        $this->arrData['email'] = array('email');
        $this->arrData['idnumber'] = array('IDNumber');
        $this->arrData['supplierkey'] = array('selInsuranceCompany');
        $this->arrData['dateofbirth'] = array('dateOfBirth', 'date');
        $this->arrData['expireddate'] = array('expiredDate', 'date');
        $this->arrData['policynumber'] = array('policyNumber');
        $this->arrData['countrykey'] = array('selCountry');
        $this->arrData['trdesc'] = array('trDesc'); 
        $this->arrData['statuskey'] = array('selStatus');
        $this->arrData['islinked'] = array('isLinked');
        $this->arrData['coverage'] = array('coverage', 'number');
        $this->arrData['excessfee'] = array('excessFee', 'number');

        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'policyNumber','title' => 'policyNumber','dbfield' => 'policynumber','default'=>true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'name','title' => 'name','dbfield' => 'name','default'=>true, 'width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'company','title' => 'company','dbfield' => 'customername','default'=>true, 'width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'category','title' => 'category','dbfield' => 'categoryname','default'=>true, 'width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'insuranceCompany','title' => 'insuranceCompany','dbfield' => 'suppliername','default'=>true, 'width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'idnumber','title' => 'IDNumber','dbfield' => 'idnumber','default'=>true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
    

        $this->arrSearchColumn = array();
        array_push($this->arrSearchColumn, array('Kode', $this->tableName . '.code'));
        array_push($this->arrSearchColumn, array('name', $this->tableName . '.name'));
        array_push($this->arrSearchColumn, array('customer', $this->tableCustomer . '.name'));
        array_push($this->arrSearchColumn, array('supplier', $this->tableSupplier . '.name'));
        array_push($this->arrSearchColumn, array('status', $this->tableStatus . '.status'));
        array_push($this->arrSearchColumn, array('IDNumber', $this->tableName . '.idnumber'));
        array_push($this->arrSearchColumn, array('policyNumber', $this->tableName . '.policynumber'));
        array_push($this->arrSearchColumn, array('category', $this->tableCustomerCategory . '.name'));
        
        $this->includeClassDependencies(array(
            'Customer.class.php',
            'CustomerCategory.class.php',
            'Country.class.php',
            'Supplier.class.php'
        ));

		$this->overwriteConfig();
   }
	 
	 
	 
   function getQuery(){

       $sql = '
                select
                    ' . $this->tableName . '.*,
                    ' . $this->tableSupplier . '.name as suppliername,
                    ' . $this->tableCustomer . '.name as customername,
                    ' . $this->tableCustomer . '.categorykey as customercategorykey,
                    ' . $this->tableCustomerCategory . '.name as categoryname,
                    ' . $this->tableCountry . '.name as countryname,
                    ' . $this->tableStatus . '.status as statusname
                from 
                    ' . $this->tableName . '
                    left join ' . $this->tableSupplier . ' on ' . $this->tableName . '.supplierkey = ' . $this->tableSupplier . '.pkey  
                    left join ' . $this->tableCustomer . ' on ' . $this->tableName . '.refkey = ' . $this->tableCustomer . '.pkey
                    left join ' . $this->tableCustomerCategory . ' on ' . $this->tableCustomer . '.categorykey = ' . $this->tableCustomerCategory . '.pkey  
                    left join ' . $this->tableCountry . ' on ' . $this->tableName . '.countrykey = ' . $this->tableCountry . '.pkey,
                    ' . $this->tableStatus . '
                where  		
                    ' . $this->tableName . '.statuskey = ' . $this->tableStatus . '.pkey
         ' . $this->criteria;
       return $sql;
   }


	function validateForm($arr,$pkey = ''){
		  
		$arrayToJs = parent::validateForm($arr,$pkey); 
	   
        $customer = new Customer() ;
        $customerkey = $arr['hidRefKey'];
		$id = $arr['hidRefKey'];
			
		if(!isset($arr['_mnv_auto_update'])){
			
			$rsInsuranceCompany = $customer->getInsuranceCompanyDetail($customerkey);
			$rsCustomer = $customer->getDataRowById($customerkey);
			
			// user individu tidak boleh ditambahkan manual
			if($rsCustomer[0]['categorykey'] == 1)
				$this->addErrorList($arrayToJs,false,$this->errorMsg['policyNumber'][6]);
			
			$arrInsuranceCompany = array_column($rsInsuranceCompany,'supplierkey'); 
			if(!in_array($arr['selInsuranceCompany'], $arrInsuranceCompany)) 
				$this->addErrorList($arrayToJs,false,$this->errorMsg['insuranceCompany'][3]);
			
	 	}
		
		// ggk mungkin kejadian, karena sudah divalidasi gk boleh add manual
//		if($arr['selCategory'] == 1){
//			if($this->isValueExisted($pkey,'refkey',$customerkey)){ 
//					$this->addErrorList($arrayToJs,false,$this->errorMsg['policyNumber'][5]);
//			}
//		}
		
		
		// utk insurance, nanti ganti selectbox
		// asuransi tidak wajib diisi untuk sementara karena bisa ditanggung perusahaan
		
		
//        $rsCustomer = $customer->searchDataRow(
//            array($customer->tableName . '.isinsured'),
//                ' and ' . $customer->tableName . '.pkey = ' . $this->oDbCon->paramString($customerkey)
//        );
//
//        if ($rsCustomer[0]['isinsured'] == 1){
//            if(empty($arr['hidSupplierKey'])){
//                $this->addErrorList($arrayToJs,false,$this->errorMsg['insuranceCompany'][2]);
//		    }
//            if(!empty($arr['hidSupplierKey'])){
//                $arrInsruanceCompany = $customer->getInsuranceCompany($customerkey, $arr['hidSupplierKey']);
//                if(empty($arrInsruanceCompany)){
//                    $this->addErrorList($arrayToJs,false,$this->errorMsg['insuranceCompany'][1] . ' ' . $arr['customerName']);
//                }
//		    }
//        } else {
//            if(!empty($arr['hidSupplierKey'])){
//                $this->addErrorList($arrayToJs,false,$this->errorMsg['insuranceCompany'][1] . ' ' . $arr['customerName']);
//		    }
//        }
        
        
		 return $arrayToJs;
	 }


//     function getCustomerPolicysAge($pkey){ 
//        $rs = $this->getDataRowById($pkey);
//        
//        $today = date('Y-m-d');
//        $dateOfBirth = $rs[0]['dateofbirth'];
//        $diff = date_diff(date_create($dateOfBirth), date_create($today));
//        $age = $diff->format('%y');
//
//        return $age; 
//    }
	 
    function generateDefaultQueryForAutoComplete($returnField){ 
        
        $sql = 'select
					'.$returnField['key'].',
					'.$returnField['value'].' as value,
					'.$this->tableName.'.name,
					'.$this->tableName.'.policynumber,
					'.$this->tableName.'.idnumber,
					'.$this->tableName.'.email,
					'.$this->tableName.'.phone,
					'.$this->tableName.'.excessfee,
					'.$this->tableName.'.mobile,
					'.$this->tableName.'.dateofbirth,
					'.$this->tableCountry.'.name as countryname,
					'.$this->tableSupplier.'.name as suppliername ,
					'.$this->tableCustomerCategory.'.name as categoryname,
					'.$this->tableCustomer.'.name as companyname 
				from 
					'.$this->tableName . ' 
						left join '.$this->tableCustomer.'  on '.$this->tableName.'.refkey = '.$this->tableCustomer.'.pkey 
						left join '.$this->tableCustomerCategory.'  on '.$this->tableCustomer.'.categorykey = '.$this->tableCustomerCategory.'.pkey 
						left join '.$this->tableCountry.'  on '.$this->tableName.'.countrykey = '.$this->tableCountry.'.pkey 
						left join '.$this->tableSupplier.'  on '.$this->tableName.'.supplierkey = '.$this->tableSupplier.'.pkey , 
                    '.$this->tableStatus.' 
				where  		 
					'.$this->tableName . '.statuskey = '.$this->tableStatus.'.pkey  
			';
           
		 return $sql;
     }
 
     function validateConfirm($rsHeader) {
       
     }
     
    function normalizeParameter($arrParam, $trim=false){ 
        
        $arrParam = parent::normalizeParameter($arrParam,true);
        return $arrParam;
    }
        
    
  }

?>
