<?php
  
class CashBank extends BaseClass{ 
 
   function __construct(){
		
		parent::__construct();
       
		$this->tableName = 'cash_bank'; 
		$this->tableNameTransaction = 'cash_bank_transaction'; 
		$this->tableStatus = 'transaction_status';
        $this->tableCOA = 'chart_of_account';
        $this->tableWarehouse = 'warehouse';   
        $this->tableCustomer = 'customer';    
        $this->tableSupplier = 'supplier'; 
        $this->tableCurrency = 'currency';       
        $this->tableEmployee = 'employee';   
        $this->tablekey = 'tablekey';   
        $this->isTransaction = true; 
        $this->tableCashBankTransactionType = 'cash_bank_transaction_type';
        $this->tableBusinessUnit = 'business_unit';
		
        $this->securityObject = 'CashBank'; 
 
        $this->arrData = array();  
        $this->arrData['pkey'] = array('pkey');
        $this->arrData['code'] = array('code');
        $this->arrData['refkey'] = array('hidRefKey');
        $this->arrData['detailkey'] = array('detailKey');
        $this->arrData['refcode'] = array('refCode');
        $this->arrData['reftabletype'] = array('reftabletype');
        $this->arrData['recipienttypekey'] = array('selRecipientType');

        $this->arrData['warehousekey'] = array('selWarehouseKey');
        $this->arrData['transactiontypekey'] = array('selTransactionTypeKey');
        $this->arrData['currencykey'] = array('selCurrency');
        $this->arrData['rate'] = array('currencyRate','number');
        $this->arrData['trdate'] = array('trDate','date');
        $this->arrData['recipientname'] = array('recipientName');
        $this->arrData['attnname'] = array('attnName');
        $this->arrData['coakey'] = array('hidCOAHeaderKey');
        $this->arrData['trdesc'] = array('trDesc');
        $this->arrData['amount'] = array('amount','number');
        $this->arrData['outstanding'] = array('outstanding','number');
        $this->arrData['islinked'] = array('islinked'); 
        $this->arrData['credittype'] = array('creditType');
        $this->arrData['statuskey'] = array('selStatus');   
        $this->arrData['overwriteGL'] = array('overwriteGL'); 
        $this->arrData['supplierkey'] = array('hidSupplierKey');
        $this->arrData['customerkey'] = array('hidCustomerkey');
        $this->arrData['employeekey'] = array('hidEmployeekey');
        $this->arrData['revenuekey'] = array('hidRevenueKey');
        $this->arrData['costkey'] = array('hidCostKey');
        $this->arrData['countercoakey'] = array('hidCounterCOAKey'); 
        $this->arrData['ppnvalue'] = array('PPnValue');
        $this->arrData['ppnpercentage'] = array('PPnPercentage');
        $this->arrData['pphvalue'] = array('PPhValue');
        $this->arrData['pphtypekey'] = array('PPhTypeKey');
        $this->arrData['ispriceincludetax'] = array('isPriceIncludeTax');
       
           
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 130));
        array_push($this->arrDataListAvailableColumn, array('code' => 'date','title' => 'date','dbfield' => 'trdate','default'=>true, 'width' => 100, 'align' =>'center', 'format' => 'date'));
   	    array_push($this->arrDataListAvailableColumn, array('code' => 'reconsiliationDate','title' => 'reconsiliationDate','dbfield' => 'reconsiledate','default'=>true, 'width' => 130, 'align' =>'center', 'format' => 'date'));
             // utk transfer kas bank, gk ad gudang 
        //array_push($this->arrDataListAvailableColumn, array('code' => 'warehouse','title' => 'warehouse','dbfield' => 'warehousename','default'=>true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'businesunit','title' => 'businessUnit','dbfield' => 'businessunitname','default'=>false, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'refcode','title' => 'refCode','dbfield' => 'refcode','default'=>true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'from','title' => 'senderOrRecipient','dbfield' => 'recipientname', 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'account','title' => 'account','dbfield' => 'codename',   'width' => 180));
        array_push($this->arrDataListAvailableColumn, array('code' => 'currency','title' => 'curr','dbfield' => 'currencyname','default'=>true, 'width' => 60,'align' =>'center'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'amount','title' => 'amount','dbfield' => 'amountcredit','default'=>true, 'width' => 100,'align' =>'right','format' => 'number'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'outstanding','title' => 'outstanding','default'=>true,'dbfield' => 'outstandingcredit',  'width' => 100,'align' =>'right','format' => 'number'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'transactionType','title' => 'transactionType','dbfield' => 'transactiontypename',   'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'desc','title' => 'note','dbfield' => 'trdesc', 'default'=>true, 'width' => 250));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
        
        $this->printMenu = array();
        array_push($this->printMenu,array('code' => 'print', 'name' => $this->lang['printTransaction'],  'icon' => 'print', 'url' => 'print/cashBankVoucher'));
            

        $this->arrSearchColumn = array ();
        array_push($this->arrSearchColumn , array('Kode', $this->tableName . '.code'));
        array_push($this->arrSearchColumn , array('COA', $this->tableCOA . '.code')); 
        array_push($this->arrSearchColumn , array('COA', $this->tableCOA . '.name')); 
        array_push($this->arrSearchColumn , array('Dari', $this->tableName . '.recipientname'));
        array_push($this->arrSearchColumn , array('Pelanggan', $this->tableCustomer . '.name'));
        array_push($this->arrSearchColumn , array('Karyawan', $this->tableEmployee . '.name'));
        array_push($this->arrSearchColumn , array('Pemasok', $this->tableSupplier . '.name'));
        array_push($this->arrSearchColumn , array('Kode Ref', $this->tableName . '.refcode'));
        array_push($this->arrSearchColumn , array('Tanggal', $this->tableName . '.trdate'));
        array_push($this->arrSearchColumn , array('Catatan', $this->tableName . '.trdesc') );
        array_push($this->arrSearchColumn , array('Jumlah', $this->tableName . '.amount') );

        //array_push($this->filterCriteria, array('title' => $this->lang['warehouse'], 'field' => 'warehousekey'));
      
        $this->includeClassDependencies(array(
            'ChartOfAccount.class.php',
            'Customer.class.php',   
            'Supplier.class.php',  
            'Warehouse.class.php',  
            'Downpayment.class.php',  // kebawah, perlu utk report // getRelatedDataForCashBankReport
            'CashBankIn.class.php',
            'CashBankOut.class.php',  
            'RevenueCashIn.class.php',  
            'CostCashOut.class.php', 
            'CashBankTransfer.class.php',  
            'CashIn.class.php',  
            'CashOut.class.php',  
            'SupplierDownpayment.class.php',  
            'CustomerDownpayment.class.php',  
            'TruckingCostCashOut.class.php',  
            'APPayment.class.php',  
            'CarServiceMaintenance.class.php',  
            'PurchaseOrder.class.php',  
            'Tax.class.php',
            'GeneralJournal.class.php',
            'BusinessUnit.class.php'.
            'Currency.class.php',
            'COALink.class.php'
        ));  
        
       
        $this->privilegesCriteria = true;
        $this->overwriteConfig();
   }
   
   function getQuery(){
	   
       $sql = '
			SELECT '.$this->tableName.'.* , 
                '.$this->tableName.'.amount * '.$this->tableName.'.credittype as amountcredit ,  
                '.$this->tableName.'.outstanding * '.$this->tableName.'.credittype as outstandingcredit,
               ' . $this->tableCOA .'.name as coaname,
               ' . $this->tableCOA .'.code as coacode,
               '. $this->tableBusinessUnit .'.name as businessunitname,
               '.$this->tableWarehouse.'.name as warehousename,
               ' . $this->tableCurrency .'.name as currencyname,
               '.$this->tableCashBankTransactionType.'.name as transactiontypename, 
               concat(' . $this->tableCOA .'.code, " - " , ' . $this->tableCOA .'.name ) as codename,
			   '.$this->tableStatus.'.status as statusname,
                IF('.$this->tableName.'.amount > 0, '.$this->tableName.'.amount, "0") as debit,
                IF('.$this->tableName.'.amount < 0, '.$this->tableName.'.amount, "0") as credit,
                concat_ws(\'\',' . $this->tableCustomer .'.name, ' . $this->tableSupplier .'.name, ' . $this->tableEmployee .'.name) as recipientname
			FROM '.$this->tableStatus.', '.$this->tableName.' 
                left join '.$this->tableCOA.' on ' . $this->tableCOA .'.pkey = ' . $this->tableName .'.coakey 
                left join '.$this->tableWarehouse.' on ' . $this->tableWarehouse .'.pkey = ' . $this->tableName .'.warehousekey
                left join '.$this->tablekey.' on ' . $this->tablekey .'.pkey = ' . $this->tableName .'.reftabletype
                left join '.$this->tableCashBankTransactionType.' on ' . $this->tablekey .'.tablename = ' . $this->tableCashBankTransactionType .'.tablename
                left join '.$this->tableCustomer.' on ' . $this->tableName .'.customerkey = ' . $this->tableCustomer .'.pkey
                left join '.$this->tableSupplier.' on ' . $this->tableName .'.supplierkey = ' . $this->tableSupplier .'.pkey
                left join '.$this->tableEmployee.' on ' . $this->tableName .'.employeekey = ' . $this->tableEmployee .'.pkey 
                left join '.$this->tableCurrency.' on ' . $this->tableName .'.currencykey = ' . $this->tableCurrency .'.pkey  
                left join '.$this->tableBusinessUnit.' on ' . $this->tableName .'.businessunitkey = ' . $this->tableBusinessUnit .'.pkey
			WHERE '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey
 	  ' .$this->criteria ; 
       
        if($this->privilegesCriteria)
            $sql .=  $this->getCOACriteria() ;
         
       return $sql;
		 
    }
        

    function generateDefaultQueryForAutoComplete($returnField)
    {
        $sql = 'select
                    '. $returnField['key'] .',
                    '. $returnField['value'] .' as value, 
                    '. $this->tableName .'.code,
                    '. $this->tableName .'.refcode,
                    '. $this->tableName .'.trdate,
                    '. $this->tableName .'.trdesc,
                    '. $this->tableName . '.amount,
                    '. $this->tableName . '.credittype,
                    '. $this->tableCurrency .'.name as currencyname
                    from
                        ' . $this->tableStatus . ',
                        '. $this->tableName . '
                        left join ' . $this->tableCOA . ' on ' . $this->tableCOA . '.pkey = ' . $this->tableName . '.coakey 
                        left join ' . $this->tableWarehouse . ' on ' . $this->tableWarehouse . '.pkey = ' . $this->tableName . '.warehousekey
                        left join ' . $this->tableCurrency . ' on ' . $this->tableCurrency . '.pkey = ' . $this->tableName . '.currencykey
                    where ' . $this->tableName . '.statuskey = ' . $this->tableStatus . '.pkey 
                ';
        
        return $sql;
    }
    
    function afterStatusChanged($rsHeader){   
 
    }
    
    function sumCashMovement($coakey, $endDate=''){
		  
		$criteria = '';
	    if (!is_array($coakey))
            $coakey = array($coakey);
        
           // harusnya udah pasti trdate  
       	if (!empty($endDate)) 
           $criteria .= ' and '.$this->tableName.'.trdate < '.$this->oDbCon->paramDate($endDate,' / ', 'Y-m-d 00:00:00'); 
	 
        
		$sql = 'select coalesce(sum(amount),0) as "amount" from '.$this->tableName.'  where statuskey in (2,3) and coakey in ('.$this->oDbCon->paramString($coakey,',') .') '. $criteria;		 
        
		$rs =  $this->oDbCon->doQuery($sql);		 
	 	return $rs[0]['amount'];
	}
    
    function validateForm($arr,$pkey = ''){ 
		   
		$arrayToJs = parent::validateForm($arr,$pkey); 
		
		$chartOfAccount = new ChartOfAccount();   
		//$arrCOAkey = $arr['hidCOAKey']; 
		$arrAmount = $arr['amount']; 
		$arrCOAHeaderKey = $arr['hidCOAHeaderKey'];
		
		//validasi kalo status gk menunggu gk bisa edit 
		if (!empty($pkey)){
			$rs = $this->getDataRowById($pkey);
			if ($rs[0]['statuskey'] <> 1){
				$this->addErrorList($arrayToJs,false,$this->errorMsg[212]);
			}
		}   
		
		if(empty($arrCOAHeaderKey)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['coa'][1]); 
		}	
         
		/*for($i=0;$i<count($arrCOAkey);$i++) { 
			
			if (empty($arrCOAkey[$i])){ 
				$this->addErrorList($arrayToJs,false, $this->errorMsg['coa'][1]); 	
			}
			if (!empty($arrCOAkey[$i]) && $this->unFormatNumber($arrAmount[$i]) <= 0){
				$rsCOA = $chartOfAccount->getDataRowById($arrCOAkey[$i]);
				$this->addErrorList($arrayToJs,false,$rsCOA[0]['code'] . ' - ' .$rsCOA[0]['name']. '. ' . $this->errorMsg[503]); 
			}
		}*/
		
		return $arrayToJs;
	 }  

    function validateCancel($rsHeader,$autoChangeStatus=false){ 
          
        
        $id = $rsHeader[0]['pkey'];
        
        parent::validateCancel($rsHeader,$autoChangeStatus); 
        
        if($rsHeader[0]['isreconsile'] == 1) 
            $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg['cashBank'][6]);
 
    }
    
    
	function validateConfirm($rsHeader){
        
	 }		

	function confirmTrans($rsHeader){ 
		$id = $rsHeader[0]['pkey'];
	  
		//update jurnal umum 
        $this->updateGL($rsHeader);
	} 
     
	function updateGL($rs){
        if (!USE_GL) return;
        
        if ($rs[0]['overwriteGL'] == 1) return; 
        
        //$this->setLog("in",true);
         
        $warehouse = new Warehouse();
        $generalJournal = new GeneralJournal(); 
        $chartOfAccount = new ChartOfAccount();
        $revenueCashIn = new RevenueCashIn();
        $costCashOut = new CostCashOut();
        $tax = new Tax();
        
        $rsKey = $generalJournal->getTableKeyAndObj($this->tableName);
        
		$arr = array();
		$arr['pkey'] = $generalJournal->getNextKey($generalJournal->tableName);
		$arr['code'] = 'xxxxx';
		$arr['refkey'] = $rs[0]['pkey'];
		$arr['refTableType'] = $rsKey['key'];
		$arr['trDate'] =  $this->formatDBDate($rs[0]['trdate'],'d / m / Y'); 
		$arr['trDesc'] = $rs[0]['trdesc'];
		$arr['createdBy'] = 0;  
		$arr['selWarehouseKey'] = $rs[0]['warehousekey'];
		
        $temp = -1;
        //$rsDetail = $this->getDetailById($rs[0]['pkey']); 
        

        $amount =  $rs[0]['amount'] * $rs[0]['credittype']  ;
        $rs[0]['ppnvalue'] *= $rs[0]['credittype'] ;
        $rs[0]['pphvalue'] *= $rs[0]['credittype'] ;
        
        $coaKey =  $rs[0]['coakey'];

        if(!empty($rs[0]['revenuekey'])) 
            $coaCounterKey = $revenueCashIn->getDataRowById($rs[0]['revenuekey'])[0]['coakey'] ;
        else if (!empty($rs[0]['costkey'])) 
            $coaCounterKey = $costCashOut->getDataRowById($rs[0]['costkey'])[0]['coakey'] ;
        else
            $coaCounterKey = $chartOfAccount->getDataRowById($rs[0]['coakey'])[0]['countercoakey'];
  
        // kalo negatif, otomatis nanti aka ndibalik di GL nya
        $temp++;
        $arr['hidCOAKey'][$temp] = $coaKey; 
        $arr['debit'][$temp] = $amount;  // kalo ayat silang harusnya aman karena tax nya pasti direset jd 0;
        $arr['credit'][$temp] = 0;  
        
        $temp++;
        $arr['hidCOAKey'][$temp] = $coaCounterKey; 
        $arr['debit'][$temp] = 0; 
        $arr['credit'][$temp] = $amount - $rs[0]['ppnvalue'] + $rs[0]['pphvalue'];  

 
        if($rs[0]['ppnvalue'] != 0){
            
            // format dulu agar bisa sama denga string
            $taxFormatted = (float)$rs[0]['ppnpercentage'];
            
            $rsTax = $tax->searchDataRow(array('taxincoakey', 'taxoutcoakey'), ' and '.$tax->tableName.'.typekey = '. $this->oDbCon->paramString(TAX_TYPE['PPN']).' and '.$tax->tableName.'.name = ' . $this->oDbCon->paramString($taxFormatted));
            $taxCOAKey = ($amount<0)  ? $rsTax[0]['taxincoakey'] : $rsTax[0]['taxoutcoakey'] ;
                    
            $temp++;
            $arr['hidCOAKey'][$temp] = $taxCOAKey; 
            $arr['debit'][$temp] = 0; 
            $arr['credit'][$temp] = $rs[0]['ppnvalue'];  
                    
        }
        
 
        if($rs[0]['pphvalue'] != 0){
              
            $rsTax = $tax->searchDataRow(array('taxincoakey', 'taxoutcoakey'), ' and '.$tax->tableName.'.typekey = '. $this->oDbCon->paramString(TAX_TYPE['PPH']).' and '.$tax->tableName.'.pkey = ' . $this->oDbCon->paramString($rs[0]['pphtypekey']));
//            $this->setLog(  ' and '.$tax->tableName.'.pkey = ' . $this->oDbCon->paramString($rs[0]['pphtypekey']), true);
            
            $taxCOAKey = ($amount>0)  ? $rsTax[0]['taxincoakey'] : $rsTax[0]['taxoutcoakey'] ;
                    
            $temp++;
            $arr['hidCOAKey'][$temp] = $taxCOAKey; 
            $arr['debit'][$temp] = $rs[0]['pphvalue']; 
            $arr['credit'][$temp] = 0;  
                    
        }
        
		$arrayToJs = $generalJournal->addData($arr);
        
		if (!$arrayToJs[0]['valid'])
                throw new Exception('<strong>'.$rs[0]['code'] . '</strong>. '.$this->errorMsg[504].' '.$arrayToJs[0]['message']);    
    }

    
	 
	function cancelTrans($rsHeader,$copy){ 
		$id = $rsHeader[0]['pkey']; 
	 
		if ($copy)
			$this->copyDataOnCancel($id);	 
        
        $this->cancelGLByRefkey($rsHeader[0]['pkey'],$this->tableName);
	} 
    
    function getTransactionType($pkey=''){ 
       
        // gk boleh pake pkey tableCashBankTransactionType, karena tablekey di setiap user bisa beda2.
        // sistem auto add tablekey, jd kita gk bisa kontrol pkey tablekey
        
	   $sql = 'select
	   			'.$this->tablekey .'.pkey, 
	   			'.$this->tableCashBankTransactionType .'.name 
              from
			  	'.$this->tableCashBankTransactionType .',
                '.$this->tablekey .'
			  where
			  	'.$this->tableCashBankTransactionType .'.tablename =   '.$this->tablekey .'.tablename and
			  	'.$this->tableCashBankTransactionType .'.statuskey = 1';
        
       if(!empty($pkey))
            $sql .= ' and '.$this->tableCashBankTransactionType .'.pkey = '.$this->oDbCon->paramString($pkey);
        
       $sql .=' order by name asc';
         
       return $this->oDbCon->doQuery($sql);
	
   }
  
   function generateCashBankCode($arrParam){
          
        $coaLink = new COALink();
        $charOfAccount = new ChartOfAccount();
        $coakey = $arrParam['hidCOAHeaderKey'];
        $amount = $arrParam['amount'];
		$theCode = '';
//		$counterUpdate = '';
        
		$sql = 'select * from  '.$this->tableCOA.' where pkey ='.$this->oDbCon->paramString($coakey);
        $rs = $this->oDbCon->doQuery($sql);
         
        if(empty($rs))
            return $theCode;
       
        $counterValue = 0; 
        if($amount<0 || $arrParam['creditType']<0){
//           $counterValue = (!empty($rs[0]['outcounter'])) ? $rs[0]['outcounter']: 1 ; 
           $codeFormat = $rs[0]['outcode'];
//           $counterUpdate = 'outcounter';
        }else{
//            $counterValue = (!empty($rs[0]['incounter'])) ? $rs[0]['incounter']: 1 ;
            $codeFormat = $rs[0]['incode'];
//            $counterUpdate = 'incounter';
        }
        
        //cek jenis counter, ambil counter sesuai jenisnya 
        $arrFormat = array();      
         
        $arr = array();
        $arr['selResetType'] =  $rs[0]['resettypekey'] ;
          
        if (isset($arrParam['trDate'])){ 
            $arrFormatType = array('m','Y','y','d','mr'); 
            foreach($arrFormatType as $format){  
                if($format == 'mr'){
                    $arrFormat[$format] = $this->numberToRoman($this->formatDBDate(str_replace('\'','',$this->oDbCon->paramDate($arrParam['trDate'],' / ')),'m'));    
                }else{ 
                    $arrFormat[$format] = $this->formatDBDate(str_replace('\'','',$this->oDbCon->paramDate($arrParam['trDate'],' / ')),$format); 
                }
            }
            
            $arr['trDate'] = $arrParam['trDate']; 
            switch ($arr['selResetType']){
                case 3: $arr['trDate'] = $this->oDbCon->paramDate($arr['trDate'],' / ', '01 / m / Y');
                        break;
                case 4: $arr['trDate'] = $this->oDbCon->paramDate($arr['trDate'],' / ', '01 / 01 / Y');
                        break;
            }
            
            $arr['trDate'] = str_replace('\'','',$arr['trDate']);

        }
        
        $arr['pkey'] = $coakey;
        $arr['amount'] = $amount; 
        $arr['creditType'] = $arrParam['creditType']; 
           
        $counterValue = $charOfAccount->updateCounter($arr);
//        $this->setLog($counterValue,true);
       
        $format = '%s%0' . $rs[0]['digit'] . 'd';
        $counterFormated = sprintf($format,'', $counterValue);  
        	
        $patterns = array(); 
		$replacement = array();
        
        array_push($patterns, '/({{NO}})/'); 
        array_push($replacement, $counterFormated);  
         
        foreach($arrFormat as $key=>$value){
            array_push($patterns, '/({{'.$key.'}})/');  
            array_push($replacement, $value);   
        }
              
		$theCode = preg_replace($patterns, $replacement, $codeFormat);   
           
        return $theCode;
       
	}
    
    
     
    function addCashBank($rs,$tableName,$arrParam = array(), $isSupplier=false){ 
            
        if(!ADV_FINANCE) return; 
		
		
        
        // harus ad pengecualian, kalo dr SPK ad tambahan informasi pengakuan tgl jurnal
        
        /*$timestampArr = $this->getDateUsedForTimestamp($this->tableName, $rsHeader);
        
        $changeCashOutTimestamp = $security->isAdminLogin($this->changeTimeStampSecurityObject,10);         
  
        if(!$changeCashOutTimestamp){
            $trDate = $timestampArr['timestamp'];
        }else{
            switch($rsHeader[0]['timestamptype']){ 
                case '1' : $trDate = $rsHeader[0]['trdate'];  break;
                case '2' : $trDate = date('Y-m-d');  break;
                default : $trDate = $timestampArr['timestamp'];  break; 
            }     
        }*/
        
        
        if(!isset($arrParam['timestamp'])){
             $timestamp = $this->getDateUsedForTimestamp($tableName, $rs);
             $timestamp = $timestamp['timestamp'];
        }else{ 
             $timestamp = $arrParam['timestamp']; 
        }
 
        $tablekey = $this->getTableKeyAndObj($tableName,array(('key')))['key'];
        $isSupplierCredit = ($isSupplier) ? -1 : 1; 
        
        $pkey = $rs[0]['pkey'];
        $code = $rs[0]['code'];
        $warehousekey = (isset($rs[0]['warehousekey'])) ? $rs[0]['warehousekey'] : 0;
        $currencykey = (isset($rs[0]['currencykey'])) ? $rs[0]['currencykey'] : 1;
        $rate = (isset($rs[0]['rate'])) ? $rs[0]['rate'] : 1;
        $recipienttypekey = (isset($rs[0]['recipienttypekey'])) ? $rs[0]['recipienttypekey'] : '';
        $coakey = (!isset($arrParam['coakey'])) ? $rs[0]['coakey'] : $arrParam['coakey'];
        $amount = (!isset($arrParam['amount'])) ? $rs[0]['amount'] : $arrParam['amount'];
        $trdesc = (!isset($arrParam['desc'])) ? '' : $arrParam['desc'];
        $detailkey = (!isset($arrParam['detailkey'])) ? $pkey : $arrParam['detailkey'];
        $supplierkey = (!isset($arrParam['supplierkey'])) ? '' : $arrParam['supplierkey'];
        $customerkey = (!isset($arrParam['customerkey'])) ? '' : $arrParam['customerkey'];
        $employeekey = (!isset($arrParam['employeekey'])) ? '' : $arrParam['employeekey'];
        $outstanding = (!isset($arrParam['outstanding'])) ? 0 : $arrParam['outstanding'];
        $overwriteGL = (!isset($arrParam['overwriteGL'])) ? 1 : $arrParam['overwriteGL'];
        $countercoakey = (!isset($arrParam['countercoakey'])) ? 0 : $arrParam['countercoakey'];
        $revenuekey = (!isset($arrParam['revenuekey'])) ? 0 : $arrParam['revenuekey'];
        $costkey = (!isset($arrParam['costkey'])) ? 0 : $arrParam['costkey'];
        
        $PPnValue = (!isset($arrParam['PPnValue'])) ? 0 : $arrParam['PPnValue'];
        $PPhValue = (!isset($arrParam['PPhValue'])) ? 0 : $arrParam['PPhValue'];
        $PPhTypeKey = (!isset($arrParam['PPhTypeKey'])) ? 0 : $arrParam['PPhTypeKey'];
        $PPnPercentage = (!isset($arrParam['PPnPercentage'])) ? 0 : $arrParam['PPnPercentage'];
        $isPriceIncludeTax = (!isset($arrParam['isPriceIncludeTax'])) ? 0 : $arrParam['isPriceIncludeTax'];
        $attnName =  (!isset($arrParam['attnName'])) ? '' : $arrParam['attnName'];
			
        if($amount == 0) return;
            
		
		// kalo master coa nya gk terbit voucher (biasanya utk kas kasbon), return langsung
		$chartOfAccount = new ChartOfAccount();
		$rsCOA = $chartOfAccount->searchDataRow(array($chartOfAccount->tableName.'.isusevoucher'),
											   ' and '. $chartOfAccount->tableName .'.pkey = ' . $this->oDbCon->paramString($coakey)
												);
		
		if ($rsCOA[0]['isusevoucher'] != 1) return;
		
		
        $arrParam = array();
        $arrParam['code'] = 'xxxxxx';
        $arrParam['hidRefKey'] = $pkey;
        $arrParam['refCode'] = $code;
        $arrParam['trDate'] = $this->formatDBDate($timestamp,'d / m / Y'); 
        $arrParam['trDesc'] = $trdesc;
        $arrParam['selWarehouseKey'] = $warehousekey; 
        $arrParam['attnName'] = $attnName;
        $arrParam['reftabletype'] = $tablekey;
        $arrParam['selRecipientType'] = $recipienttypekey;
        $arrParam['selTransactionTypeKey'] = $tablekey;
        $arrParam['selCurrency'] = $currencykey;
        $arrParam['currencyRate'] = $rate;
        $arrParam['hidCOAHeaderKey'] = $coakey;
        $arrParam['amount'] =  $amount;
        $arrParam['outstanding'] = $outstanding;
        $arrParam['detailKey'] = $detailkey;
        $arrParam['hidSupplierKey'] = $supplierkey;
        $arrParam['hidCustomerkey'] = $customerkey;
        $arrParam['hidEmployeekey'] = $employeekey;
        $arrParam['overwriteGL'] = $overwriteGL;
        $arrParam['hidCounterCOAKey'] = $countercoakey;
        $arrParam['hidRevenueKey'] = $revenuekey;
        $arrParam['hidCostKey'] = $costkey;
        $arrParam['islinked'] = 1;
        $arrParam['creditType'] = $isSupplierCredit;
        
        $arrParam['PPnValue'] = $PPnValue;
        $arrParam['PPnPercentage'] = $PPnPercentage;
        $arrParam['isPriceIncludeTax'] = $isPriceIncludeTax;
        $arrParam['PPhValue'] = $PPhValue;
        $arrParam['PPhTypeKey'] = $PPhTypeKey; 

        $arrayToJs = $this->addData($arrParam);

        if (!$arrayToJs[0]['valid'])
            $this->addErrorLog(false, '<strong>'.$code . '</strong>. '.$this->errorMsg[201].' '.$arrayToJs[0]['message'], true); 
  
      	$this->changeStatus($arrayToJs[0]['data']['pkey'],TRANSACTION_STATUS['konfirmasi'],'',false,true); 
		  
        // utk yg jelas uang masuk utk ap dan utk uang keluar
		// gk boleh digabung dengan yg atas karena memang update status 2x
        // hrusnya gk masalah karena gk mungkin costkey dan revenuekey sama2 <> kosong
        if(!empty($revenuekey) || !empty($costkey) || $outstanding == 0) 
           $this->changeStatus($arrayToJs[0]['data']['pkey'],TRANSACTION_STATUS['selesai'],'',false,true);  
	 
         
        return $arrayToJs[0]['data'];
 
    }
    
    function cancelCashBank($rsHeader,$tableName){
        if (!ADV_FINANCE) return;
           
        $id = $rsHeader[0]['pkey'];

        // utk pengecualian, karena bisa saja kas na diisi oleh divisi yg berbeda
        $this->privilegesCriteria = false;

        $rsCashOutBankKey = $this->getTableKeyAndObj($tableName,array('key'));
        $rsCashBank = $this->searchData('','',true,' and '.$this->tableName.'.reftabletype = ' . $this->oDbCon->paramString($rsCashOutBankKey['key']) .' and '.$this->tableName.'.refkey = ' . $this->oDbCon->paramString($id) .' and '.$this->tableName.'.statuskey in (1,2,3) ');
        for($i=0;$i<count($rsCashBank);$i++) {
            $arrayToJs = $this->changeStatus($rsCashBank[$i]['pkey'],TRANSACTION_STATUS['batal'],'',false,true);
            if (!$arrayToJs[0]['valid'])
                throw new Exception('<strong>'.$rsHeader[0]['code'] . '</strong>. '.  $arrayToJs[0]['message']);    
        }  
        
    }
    
    
    function reUpdateCashBankDetails($arrDetails){
		
		// lupa utk ap
         
        $headerkey = $arrDetails[0]['headerkey'];
        $tablekey = $arrDetails[0]['tablekey'];
        
        // and '. $this->tableName.'.statuskey in (2,3) 
        // gk perlu cek status, takutnya ad yg sudah dicancel terus di duplicate, utk jaga2 saja
        $rsCashBank = $this->searchDataRow( array($this->tableName.'.pkey',
                                                  $this->tableName.'.code',
                                                  $this->tableName.'.detailkey',
                                                  $this->tableName.'.amount',
                                                  $this->tableName.'.outstanding',
                                                  $this->tableName.'.customerkey',
                                                  $this->tableName.'.supplierkey',
                                                  $this->tableName.'.trdesc',
                                                 ),
                                            ' and '.$this->tableName.'.refkey = '.$this->oDbCon->paramString($headerkey).'  
                                              and '.$this->tableName.'.reftabletype = ' . $this->oDbCon->paramString($tablekey) .' '
                                            );  

        $rsCashBank = array_column($rsCashBank,null,'detailkey');
        
        foreach($arrDetails as $arr){
             $cashBankRow = $rsCashBank[$arr['detailkey']];
            
             // ini nanti kalo sudah ad transksi, gk berubah jadinya ?
             // harus tambaha validasi
             if ($cashBankRow['amount'] <> $cashBankRow['outstanding']) continue;
             
             $forCustomer = (isset($arr['customerkey'])) ? true : false;
            
            if($forCustomer){
                  if ($cashBankRow['customerkey'] == $arr['customerkey'] && $cashBankRow['trdesc'] == $arr['description']) continue;
                  $sql = ' update '.$this->tableName.' set 
                                customerkey = '.$this->oDbCon->paramString($arr['customerkey']).',
                                trdesc = '.$this->oDbCon->paramString($arr['description']).'
                         where
                               '.$this->tableName.'.pkey = '.$this->oDbCon->paramString($cashBankRow['pkey']);
                
            }else{
                  if ($cashBankRow['supplierkey'] == $arr['supplierkey'] && $cashBankRow['trdesc'] == $arr['description']) continue;
                  $sql = ' update '.$this->tableName.' set 
                                supplierkey = '.$this->oDbCon->paramString($arr['supplierkey']).',
                                trdesc = '.$this->oDbCon->paramString($arr['description']).'
                         where
                               '.$this->tableName.'.pkey = '.$this->oDbCon->paramString($cashBankRow['pkey']);
                
            }
                
            $this->oDbCon->execute($sql);
           
        }
       
        
        /*$cashBankIn = new CashBankIn();
        $rsCashOutBankKey = $this->getTableKeyAndObj($tableName,array('key'));
        $rsDetailCash = $cashBankIn->getDetailById($rsCashBankIn[0]['pkey']);
        for($i=0;$i<count($rsDetailCash);$i++){
            if(!empty($rsDetailCash[$i]['revenuekey']))
                continue;
            
            $rsCashBank = $this->searchDataRow( array($this->tableName.'.pkey',$this->tableName.'.code'),
                                                        ' and '.$this->tableName.'.refkey = '.$this->oDbCon->paramString($rsCashBankIn[0]['pkey']).' 
                                                          and '. $this->tableName.'.detailKey = '.$this->oDbCon->paramString($rsDetailCash[$i]['pkey']).' 
                                                          and '. $this->tableName.'.statuskey in (2,3) 
                                                          and '.$this->tableName.'.reftabletype = ' . $this->oDbCon->paramString($rsCashOutBankKey['key']) .' '
                                                        );  
            
            if(!empty($rsCashBank) && ($rsDetailCash[$i]['customerkey']<>$rsCashBank[0]['customerkey'] || $rsDetailCash[$i]['trdesc'] <> $rsCashBank[0]['trdesc'] )){
                $sql = ' update '.$this->tableName.' set 
                                customerkey = '.$this->oDbCon->paramString($rsDetailCash[$i]['customerkey']).',
                                trdesc = '.$this->oDbCon->paramString($rsDetailCash[$i]['trdesc']).'
                         where
                               '.$this->tableName.'.refkey = '.$this->oDbCon->paramString($rsCashBankIn[0]['pkey']).' and 
                               '. $this->tableName.'.detailKey = '.$this->oDbCon->paramString($rsDetailCash[$i]['pkey']).' and 
                               '. $this->tableName.'.statuskey in (2,3) and 
                               '.$this->tableName.'.reftabletype = ' . $this->oDbCon->paramString($rsCashOutBankKey['key']) .' ';
                $this->oDbCon->execute($sql);
                
            }
            
        }  */
        
    }

    function normalizeParameter($arrParam, $trim = false){ 
		
        $newCode = $this->generateCashBankCode($arrParam);
        if(!empty($newCode))  $arrParam['code'] = $newCode;
 
        $arrParam = parent::normalizeParameter($arrParam,true); 
  
        return $arrParam;
    }
    
        
    function getCashBankRef($id,$tableName,$coakey = '',$detailkey = ''){
        
        $rsCashOutBankKey = $this->getTableKeyAndObj($tableName,array('key'));
        
        $criteria = ' and '.$this->tableName.'.reftabletype = ' . $this->oDbCon->paramString($rsCashOutBankKey['key']) .' and '.$this->tableName.'.statuskey in (1,2,3)';
        
        if (!empty($coakey))
            $criteria .= ' and coakey = ' .  $this->oDbCon->paramString($coakey);
        
        if (!empty($detailkey))
            $criteria .= ' and detailkey = ' .  $this->oDbCon->paramString($detailkey);
        
        $rs = $this->searchData($this->tableName.'.refkey',$id,true, $criteria);
        
        return (empty($rs)) ? array() : $rs[0];
        
    }
    
    function getAvailableVoucher($customerkey,$criteria='',$includeUnKnownSource=false,$recipientType=1){
			 
//		$recipientType = 1; => customer
//		$recipientType = 2; => supplier
//		$recipientType = 3; => employee	

//        $this->setLog($criteria,true); 
//        $this->setLog($recipientType,true);
//        $this->setLog('.....',true);
        
		 // $credittype sepertinya sudah tidak perlu karena di DP settlement, bisa terbalik. contoh harusnya customer > 0. di DP Settlement bisa jd < 0
		 // tapi nanti direview lg. mungkin harus ditambahkan criteria (ketika lempar via ajax), jika jenis DP Settlement harus ambil yg creditype < 0, dilempar dr ajax nya
		  switch($recipientType){
			 case 1 : 
				 		$isSupplierOrCustomer = 'customerkey'; 
				 		break;
				 
			 case 2:  
				 		$isSupplierOrCustomer = 'supplierkey'; 
				 		break;
				 
			 case 3:  
				 		$isSupplierOrCustomer = 'employeekey'; 
				 		break; 
				 
		 }
        
		 $unknownCriteria = ($includeUnKnownSource) ? ' or ('.$this->tableName.'.supplierkey = 0 and '.$this->tableName.'.customerkey = 0 and '.$this->tableName.'.employeekey = 0)'  : '';

/*
		if ($isSupplier){
			$isSupplierOrCustomer = 'supplierkey';
			$unknownCriteria = ($includeUnKnownSource) ? ' or ('.$this->tableName.'.'.$isSupplierOrCustomer.' = 0 and '.$this->tableName.'.amount *  '.$this->tableName.'.credittype  < 0)'  : '';
		}else{ 
			$isSupplierOrCustomer = 'customerkey';
			$unknownCriteria = ($includeUnKnownSource) ? ' or ('.$this->tableName.'.'.$isSupplierOrCustomer.' = 0 and '.$this->tableName.'.amount *  '.$this->tableName.'.credittype  > 0)'  : '';
		}
*/
		
//        $isSupplierOrCustomer = ($isSupplier) ? 'supplierkey' : 'customerkey';

		 
        // harusnya gk perlu warehouse, bisa saja 1 bank dipake utk beberapa cabang
      /*  $rs = $this->searchDataRow( array($this->tableName.'.pkey', $this->tableName.'.code', $this->tableName.'.trdate',$this->tableName.'.amount',$this->tableName.'.outstanding'),
                                   ' and ( '.$this->tableName.'.'.$isSupplierOrCustomer.' '.'in ('. $this->oDbCon->paramString($customerkey,',').') '.$unknownCriteria.' )  
                                     and '.$this->tableName.'.outstanding > 0 
                                     and '.$this->tableName.'.statuskey = ' .TRANSACTION_STATUS['konfirmasi']
                                   .$criteria
                ); */

        // harusnya gk perlu warehouse, bisa saja 1 bank dipake utk beberapa cabang
        $rs = $this->searchDataRow( array($this->tableName.'.pkey', $this->tableName.'.code', $this->tableName.'.trdate',$this->tableName.'.credittype',$this->tableName.'.amount',$this->tableName.'.outstanding'),
                                   ' and ('.$this->tableName.'.'.$isSupplierOrCustomer.' '.'in ('. $this->oDbCon->paramString($customerkey,',').')'.$unknownCriteria.' )
                                     and '.$this->tableName.'.outstanding > 0 
                                     and '.$this->tableName.'.statuskey = ' .TRANSACTION_STATUS['konfirmasi']
                                   .$criteria
                ); 
 
        $totalRs = count($rs); 
        for($i=0;$i<$totalRs;$i++)
            $rs[$i]['voucherlabel'] = $this->formatNumber($rs[$i]['outstanding']). ' - ['.$this->formatDBDate($rs[$i]['trdate']).']';
        
        return $rs;
    }
    
    function updateOutstanding($pkey){ 
                                                                                                                                             
        $sql = 'update '.$this->tableName.' set outstanding = amount - (
                    select coalesce(sum(amount),0) as amount from '.$this->tableNameTransaction.' where refkey = ' . $this->oDbCon->paramString($pkey).' 
                )  
                where pkey = ' . $this->oDbCon->paramString($pkey);
        
        $this->oDbCon->execute($sql);      
         
        
        $rs = $this->getDataRowById($pkey);
        $outstanding = $rs[0]['outstanding'];
        
		$statuskey = ($outstanding <= 0) ? TRANSACTION_STATUS['selesai'] : TRANSACTION_STATUS['konfirmasi'];
       
        if($rs[0]['statuskey'] <> $statuskey)
            $this->changeStatus($pkey,$statuskey, '', false, true,true);
         
    }
    
    function insertTransaction($arr){
        $sql ='insert into '.$this->tableNameTransaction.' (refkey,reftabletype,reftranskey,refcode,refdate,amount) 
               values (
                    '.$this->oDbCon->paramString($arr['refkey']).',
                    '.$this->oDbCon->paramString($arr['reftablekey']).',
                    '.$this->oDbCon->paramString($arr['reftranskey']).',
                    '.$this->oDbCon->paramString($arr['refcode']).',
                    '.$this->oDbCon->paramString($arr['refdate']).',
                    '.$this->oDbCon->paramString($arr['amount']).'
                ) ';
             
        $this->oDbCon->execute($sql);	
            
        $this->updateOutstanding($arr['refkey']);    
    }
    
    
    function removeTransaction($reftranskey,$reftabletype){
        
        $sql = 'select refkey from '.$this->tableNameTransaction.' 
                where reftranskey = ' . $this->oDbCon->paramString($reftranskey) .' and reftabletype = '.$this->oDbCon->paramString($reftabletype);
         
        $rs = $this->oDbCon->doQuery($sql);	
        
        if (empty($rs)) return;
            
        $sql ='delete from '.$this->tableNameTransaction.' 
               where reftranskey = ' . $this->oDbCon->paramString($reftranskey) .' and reftabletype = '.$this->oDbCon->paramString($reftabletype); 
        $this->oDbCon->execute($sql);	
        
        foreach($rs as $row)
            $this->updateOutstanding($row['refkey']);    
    }
	
	function getTransactionDetail($pkey){
		if (empty($pkey)) return;
		
		$sql = 'select * from '.$this->tableNameTransaction.' where refkey in ('. $this->oDbCon->paramString($pkey,',').')';
		return   $this->oDbCon->doQuery($sql);
		
	}
	
	function getCashBankTransaction($pkey)
    {
        $sql = '
            select
                '. $this->tableNameTransaction .'.*,
                '. $this->tableName .'.code as cashbankcode
            from
                '. $this->tableNameTransaction .'
                left join '. $this->tableName .' on '. $this->tableNameTransaction .'.refkey = '. $this->tableName .'.pkey
            where
                '. $this->tableNameTransaction .'.refkey in ('. $this->oDbCon->paramString($pkey,',') .')
        ';

        $result = $this->oDbCon->doQuery($sql);

        return $result;
    } 
        
}
?>
