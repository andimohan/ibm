<?php

class APCommission extends AP{
  
   function __construct(){
		 
		parent::__construct();
		 
		$this->tableName = 'ap_commission'; 
		$this->tablePaymentHeader = 'ap_commission_payment_header';   
		$this->tablePaymentDetail = 'ap_commission_payment_detail'; 
	   	$this->tableSupplier = 'supplier';      
		$this->tableCustomer = 'customer';
		$this->tableJobCommission = 'emkl_commission_header';   
		$this->tableJobOrder = 'emkl_job_order_header';  
	   	$this->tablePort = 'port'; 
	   	$this->tableCurrency = 'currency'; 
		
		$this->securityObject = 'APCommission'; 
	   
	   	$this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'date','title' => 'date','dbfield' => 'trdate','default'=>true, 'width' => 100, 'align' =>'center', 'format' => 'date'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'warehouse','title' => 'warehouse','dbfield' => 'warehousename',  'width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'duedate','title' => 'duedate','dbfield' => 'duedate', 'width' => 100, 'align' =>'center', 'format' => 'date'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'reference','title' => 'reference','dbfield' => 'refcode','default'=>true, 'width' => 120 ));
        array_push($this->arrDataListAvailableColumn, array('code' => 'customer','title' => 'customer','dbfield' => 'customername', 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'jocode','title' => 'JOCode','dbfield' => 'reftranscode', 'width' => 100)); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'supplier','title' => 'supplier','dbfield' => 'suppliername','default'=>true, 'width' => 200 ));
                
         if ( in_array(PLAN_TYPE['categorykey'], array(COMPANY_TYPE['trucking'],COMPANY_TYPE['forwarding'])) )
            array_push($this->arrDataListAvailableColumn, array('code' => 'currencyShort','title' => 'currencyShort','dbfield' => 'currencyname', 'width' => 60, 'align' =>'center' ));

        array_push($this->arrDataListAvailableColumn, array('code' => 'amount','title' => 'amount','dbfield' => 'amount','default'=>true, 'width' => 100, 'align' => 'right', 'format' => 'number'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'outstanding','title' => 'outstanding','dbfield' => 'outstanding','default'=>true, 'width' => 100, 'align' => 'right', 'format' => 'number'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70)); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'description','title' => 'note','dbfield' => 'trdesc',  'width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'aptype','title' => 'transactionType','dbfield' => 'aptypename',  'width' => 120 ));
	   
       

        $this->arrSearchColumn = array ();
        array_push($this->arrSearchColumn, array('Kode', $this->tableName . '.code'));
        array_push($this->arrSearchColumn, array('Tgl. Transaksi', $this->tableName . '.trdate')); 
        array_push($this->arrSearchColumn, array('Referensi', $this->tableName. '.refcode'));
        array_push($this->arrSearchColumn, array('Job Order', $this->tableJobOrder. '.code'));
        array_push($this->arrSearchColumn, array('Supplier', $this->tableSupplier. '.name'));
        array_push($this->arrSearchColumn, array('Pelanggan', $this->tableCustomer. '.name'));
        array_push($this->arrSearchColumn, array('Jumlah', $this->tableName. '.amount'));
        array_push($this->arrSearchColumn, array('Mata Uang', $this->tableCurrency. '.name')); 
        array_push($this->arrSearchColumn, array('Catatan', $this->tableName. '.trdesc'));
	    array_push($this->arrSearchColumn, array('JO Code', $this->tableName. '.salesordercodecache')); 



	   	$this->printMenu = array();    
        array_push($this->printMenu,array('code' => 'printTransaction', 'name' => $this->lang['printTransaction'],  'icon' => 'print', 'url' => 'print/apCommission'));
	  
	   
	   	$this->includeClassDependencies(array( 
            'AP.class.php',
            'APPayment.class.php',
            'APCommissionPayment.class.php',
            'Supplier.class.php',
			'PaymentMethod.class.php',
			'EMKLCommission.class.php',
			'EMKLJobOrder.class.php',
			'Currency.class.php', 
            'Warehouse.class.php'
        ));
	}  
  
    function getQuery(){
	    // sementara nama customer ambil dr JO
        
        // kalo dr realisasi
        //$rsRrealizationKey = $this->getTableKeyAndObj($this->tableJobCommission, array('key'));
        //  '.$this->tableName . '.reftabletype = '.$this->oDbCon->paramString($rsRrealizationKey['key']).' and
        
        // left join '.$this->tableJobCommission.' on '.$this->tableJobCommission.'.pkey ='.$this->tableName . '.refkey 
        
		$sql=  '
				select
					'.$this->tableName. '.*,
                    if('.$this->tableName. '.statuskey = 1 or '.$this->tableName. '.statuskey = 2, datediff(now(),duedate) , 0)  as datediff,
					'.$this->tableSupplier.'.name as suppliername,
                    '.$this->tableJobOrder.'.code as reftranscode,
                    '.$this->tableJobOrder.'.bookingnumber as bookingnumber,
                    '.$this->tableJobOrder.'.mblnumber as mblnumber,
                    '.$this->tableJobOrder.'.etdpol,
					'.$this->tableCustomer.'.name as customername, 
					'.$this->tableStatus.'.status as statusname,
					'.$this->tableWarehouse.'.name as warehousename ,
                    '.$this->tableType .'.name as aptypename,
					'.$this->tableCurrency.'.name as currencyname,
                    pol.name as polname,
                    pod.name as podname 
				from 
					'.$this->tableName . ' 
                    left join '.$this->tableJobOrder.' on  '.$this->tableName . '.refkey2 =  '.$this->tableJobOrder . '.pkey
                    left join '.$this->tableCustomer.' on  '.$this->tableJobOrder . '.customerkey =  '.$this->tableCustomer . '.pkey
                    left join '.$this->tablePort.' pol on  '.$this->tableJobOrder.'.polkey = pol.pkey 
                    left join '.$this->tablePort.' pod on  '.$this->tableJobOrder.'.podkey = pod.pkey 

                    left join '.$this->tableCurrency .' on  '.$this->tableName.'.currencykey = ' . $this->tableCurrency .'.pkey
                    left join '.$this->tableType .' on  '.$this->tableName.'.aptype = ' . $this->tableType .'.pkey, 
                    '.$this->tableStatus.',
                    '.$this->tableSupplier.',
                    '.$this->tableWarehouse.' 
				where  		
					'.$this->tableName . '.statuskey = '.$this->tableStatus.'.pkey and 
					'.$this->tableName . '.warehousekey = '.$this->tableWarehouse.'.pkey and 
					'.$this->tableName.'.supplierkey = '.$this->tableSupplier.'.pkey
		' .$this->criteria ; 
        
        return $sql;
	}  
    
    function validateForm($arr,$pkey = ''){
		   
        // gk bisa inherit dr parentnya parent
         
        $arrayToJs = array();
         
        if(!empty($pkey)){
            $latestModifiedOn = $arr['hidModifiedOn'];
            
            $rs = $this->getDataRowById($pkey);
            if ($rs[0]['modifiedon'] <> $latestModifiedOn)
			     $this->addErrorList($arrayToJs,false,$this->errorMsg[214]);
            
            // jika linked dr data lain, tdk boleh edit
            if (isset($rs[0]['islinked']) && $rs[0]['islinked'] == 1)
                 $this->addErrorList($arrayToJs,false,$this->errorMsg[900]);
        }
        
        if(isset($arr['code']) && !empty($arr['code'])){ 
            $code = $arr['code'];   
            $rs = $this->isValueExisted($pkey,'code',$code);	 
            if(empty($code)){
                $this->addErrorList($arrayToJs,false,$this->errorMsg['code'][1]);
            }else if(count($rs) <> 0){
                //$this->setLog($rs);
                $this->addErrorList($arrayToJs,false,$this->errorMsg['code'][2]);
            }
        }
		 
		$supplierkey = $arr['hidSupplierKey']; 
		$amount = $this->unFormatNumber($arr['amount']);
		 
        //validasi kalo status gk menunggu gk bisa edit 
		if (!empty($pkey)){
			$rs = $this->getDataRowById($pkey);
			if ($rs[0]['statuskey'] <> 1){
				$this->addErrorList($arrayToJs,false,$this->errorMsg[212]);
			}
		}  
        
		if(empty($supplierkey)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['supplier'][1]);
		}
		if (!is_numeric($amount) || $amount == 0){  
			$this->addErrorList($arrayToJs,false,$this->errorMsg['amount'][2]);
		}
		 
		return $arrayToJs;
         
	 } 
    
    function getPaymentObj(){
        return  new APCommissionPayment();
    }
	
	function normalizeParameter($arrParam, $trim = false){   
        $arrParam = parent::normalizeParameter($arrParam,true);   
        return $arrParam;
    }
    
     function updateGL($rs){
        if (!USE_GL) return;
         
        if ($rs[0]['overwriteGL'] == 1)
            return;
         
        //kalo amount sama gk perlu cancel
        $this->cancelGLByRefkey($rs[0]['pkey'],$this->tableName); 
        
        $coaLink = new COALink(); 
        $warehouse = new Warehouse();  
        $generalJournal = new GeneralJournal();
        $supplier = new Supplier();
		
        $warehousekey = $rs[0]['warehousekey']; 
            
        $rsKey = $generalJournal->getTableKeyAndObj($this->tableName);
		$arr = array();
		$arr['pkey'] = $generalJournal->getNextKey($generalJournal->tableName);
		$arr['code'] = 'xxxxx';
		$arr['refkey'] = $rs[0]['pkey'];
		$arr['refTableType'] = $rsKey['key'];
		$arr['trDate'] =  $this->formatDBDate($rs[0]['trdate'],'d / m / Y');  
		$arr['refCode'] = $rs[0]['code'];
		
		$temp = -1; 
		   
        switch ($rs[0]['aptype']){ 
             
            // commission
            default : 
                    $rsCOA = $coaLink->getCOALink ('purchaserefundcost', $warehouse->tableName, $warehousekey);   
                    $temp++;
                    $arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];
                    $arr['debit'][$temp] = $rs[0]['amount']; 
                    $arr['credit'][$temp] = 0;
                
                    break;
          
        }
        
        $coakey = $supplier->getCommissionCOAKey($rs[0]['supplierkey'],$warehousekey);
        
        $temp++; 
        $arr['hidCOAKey'][$temp] = $coakey;
        $arr['debit'][$temp] = 0; 
        $arr['credit'][$temp] = $rs[0]['amount'];  

        
		$arrayToJs = $generalJournal->addData($arr); 
        
		if (!$arrayToJs[0]['valid'])
                throw new Exception('<strong>'.$rs[0]['code'] . '</strong>. '.$this->errorMsg[504].' '.$arrayToJs[0]['message']);    
 
    }
    
    
    function getAPObj(){
        return new APCommission();
    }
	 function searchDataForAutoComplete($fieldname='',$searchkey='',$mustmatch=false,$searchCriteria='',$orderCriteria='', $limit=''){
         
		$sql = 'select
					'.$this->tableName. '.pkey,     
                    concat('.$this->tableName.'.code ,  IFNULL(concat(\'-\','.$this->tableName. '.refcode), \'\') ) as value , 
                    '.$this->tableName. '.code as code , 
                    '.$this->tableName.'.refcode, 
                    '.$this->tableName.'.duedate, 
                    '.$this->tableName.'.refcode2,
                    '.$this->tableName.'.refkey,
                    '.$this->tableName.'.refdate, 
                    '.$this->tableName. '.amount,  
                    '.$this->tableName. '.currencykey, 
                    '.$this->tableName. '.outstanding, 
                    '.$this->tableName. '.rate
				from 
					'.$this->tableName . ', 
                    '.$this->tableStatus.'
				where  		
					'.$this->tableName . '.statuskey = '.$this->tableStatus.'.pkey 
			';
	
		if(!empty($fieldname)){
			
			$sql .= ' and ' ;
			
			if($mustmatch)
				$sql .=  $fieldname .' = '. $this->oDbCon->paramString($searchkey);
			else
				$sql .=  '('.$fieldname .' like '. $this->oDbCon->paramString('%'.$searchkey.'%') .' || '. $this->tableName .'.refcode like '. $this->oDbCon->paramString('%'.$searchkey.'%').')';
		}
				
		if($searchCriteria <> '')
			$sql .= ' ' .$searchCriteria;
	
		if($orderCriteria <> ''){
			$sql .= ' ' .$orderCriteria;
	 
	 	}
			
		if($limit <> '')
			$sql .= ' ' .$limit;
		     
		return $this->oDbCon->doQuery($sql);	
	}

    function updateJobOrderCommissionIsPaid($pkey, $statuskey)
    {

        $rsAP = $this->searchDataRow(array(
                                    $this->tableName.'.pkey',
                                    $this->tableName.'.code',
                                    $this->tableName.'.refcode2',
                                    $this->tableName.'.refkey2'
                                ), ' and ' . $this->tableName.'.pkey = '.$this->oDbCon->paramString($pkey).' 
                            ');

        if(empty($rsAP)) return; 

        $jokey = $rsAP[0]['refkey2'];
        if($statuskey == TRANSACTION_STATUS['konfirmasi']) {
            //status konfirmasi update ke 1
            $this->setJobOrderCommissionPaid($jokey,1);	
        } else if($statuskey == TRANSACTION_STATUS['batal']) {

            //kalau cancel, cek apakah ada job order yang sama yang sudah di bayar,

            $sql = '
                select
                    ' . $this->tableName . '.pkey,
                    ' . $this->tableName . '.refkey2
                from
                    ' . $this->tableName . '
                where
                    ' . $this->tableName . '.refkey2 = ' . $this->oDbCon->paramString($jokey) . ' and
                    '.$this->tableName.'.pkey <> '.$this->oDbCon->paramString($pkey).' and
                    ' . $this->tableName . '.statuskey in (2,3) 
            ';

            $rsOther = $this->oDbCon->doQuery($sql);

            if(empty($rsOther)) {
                //kalau tidak ada set iscommissionpaid jadi 0, 
                //karena sudah tidak ada komisis yang sudah di bayarkan
                $this->setJobOrderCommissionPaid($jokey, 0);
            } else {
                //kalau ada
                $this->setJobOrderCommissionPaid($jokey, 1);
                return; 
            }

        }
    }

    function setJobOrderCommissionPaid($jobOrderKey, $isPaid)
    {
        $sql = ' update   ' . $this->tableJobOrder . ' 
                set iscommissionpaid = ' . $this->oDbCon->paramString($isPaid) . '
                where pkey = ' . $this->oDbCon->paramString($jobOrderKey) . '
        ';

        $this->oDbCon->execute($sql);
    }
    
}
?>
