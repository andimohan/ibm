<?php

class ARPrepaidTax23Payment extends ARPayment{
	
function __construct(){
		
		parent::__construct();
		
		$this->tableName = 'ar_prepaid_23_payment_header';
		$this->tableNameDetail = 'ar_prepaid_23_payment_detail';
		$this->tableCustomer = 'customer'; 
		$this->tablePayment= 'ar_prepaid_23_payment';
		$this->tableAR = 'ar_prepaid_23';
		 
        $this->tableDownpaymentDetail = ''; // harusnya gk kepake karena struktur data sudah dioverwrite
        $this->uploadFileFolder = 'ar-prepaid-tax23-payment/';  
        $this->tableFile = '';
    
		$this->securityObject = 'ARPrepaidTax23Payment';
    
        $this->tableNeedToBeCopyOnCancel = array($this->tableNameDetail);
     
        // perlu define ulang agar gk nabrak table dari class turunan  
        $arrDetails = array();
        array_push($arrDetails, array('dataset' => $this->arrDataDetail));
        
        $this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails));
    
    
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'refCode','title' => 'withholdingNo','dbfield' => 'refcode','default'=>true, 'width' => 150));  
        array_push($this->arrDataListAvailableColumn, array('code' => 'ntpn','title' => 'ntpn','dbfield' => 'ntpn','default'=>true, 'width' => 150));      
        array_push($this->arrDataListAvailableColumn, array('code' => 'date','title' => 'date','dbfield' => 'trdate','default'=>true, 'width' => 100, 'align' =>'center', 'format' => 'date'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'taxPeriod','title' => 'taxPeriod','dbfield' => 'taxperiod','default'=>true, 'width' => 100, 'align' =>'center', 'format' => 'monthPeriod'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'customer','title' => 'customer','dbfield' => 'customername','default'=>true, 'width' => 200 ));
        array_push($this->arrDataListAvailableColumn, array('code' => 'total','title' => 'total','dbfield' => 'grandtotal','default'=>true, 'width' => 100, 'align' => 'right', 'format' => 'number'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
        array_push($this->arrDataListAvailableColumn, array('code' => 'warehouse','title' => 'warehouse','dbfield' => 'warehousename',  'width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'description','title' => 'note','dbfield' => 'trnotes',  'width' => 200));
        
        $this->includeClassDependencies(array( 
            'AR.class.php',
            'ARPayment.class.php',
            'ARPrepaidTax23.class.php',  
            'Warehouse.class.php',
            'PaymentMethod.class.php',
            'Customer.class.php'
        ));
             
        $this->printMenu = array();  
        array_push($this->printMenu,array('code' => 'printTransaction', 'name' => $this->lang['printTransaction'],  'icon' => 'print', 'url' => 'print/arPrepaidTax23Payment'));
 
        $this->overwriteConfig();
	}
    
    function validateForm($arr,$pkey = ''){
        
		$ARObj = $this->getARObj(); 
        $arrayToJs = array(); 
        
        // dr baseclass
        
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
                $this->addErrorList($arrayToJs,false,'<strong>'.$code.'</strong>. '.$this->errorMsg['code'][2]);
            }
        }
		  
        // dr baseclass
        
		$customerkey = $arr['hidCustomerKey'];  
		$arrARkey = $arr['hidARKey']; 
		$arrAmount = $arr['amount'];
		$arrOutstanding= $arr['outstanding'];
		$arrDiscount = $arr['discount'];
		$arrDownpaymentKey = $arr['hidDownpaymentKey'];
		$arrDownpaymentAmount = $arr['downpaymentAmount'];
		$arrDownpaymentCode = $arr['downpaymentCode'];
        $trDate = $arr['trDate'];
		//$arrPick = $arr['chkPick'];  

        $arrDetailKey = array();
          
        $arrAR = array();
        $rsAR = $ARObj->searchData('','',true, ' and '.$ARObj->tableName.'.pkey in ('.implode(',',$this->oDbCon->paramString($arrARkey)).') '); 
        $arrAR = array_column($rsAR, 'code', 'pkey');
        $arrARCustomer = array_column($rsAR, 'customerkey', 'pkey'); 
        $arrARWarehouse = array_column($rsAR, 'warehousekey', 'pkey');
        $arrDate = array_column($rsAR, 'trdate', 'pkey');
            
         
		//validasi kalo status gk menunggu gk bisa edit 
		if (!empty($pkey)){
			$rs = $this->getDataRowById($pkey);
			if ($rs[0]['statuskey'] <> 1){
				$this->addErrorList($arrayToJs,false,$this->errorMsg[212]);
			}
		}  
			
		if(empty($customerkey)) 
			$this->addErrorList($arrayToJs,false,$this->errorMsg['customer'][1]);
	 
        $hasAR = false; 
        for($i=0;$i<count($arrARkey);$i++) { 
            if (!empty($arrARkey[$i]))  //  && !empty($arrPick[$i])
                $hasAR = true;  

            if (in_array($arrARkey[$i],$arrDetailKey)){   
                $this->addErrorList($arrayToJs,false, $arrAR[$arrARkey[$i]].'. '.$this->errorMsg[215]); 	 
            }else{ 
                array_push($arrDetailKey, $arrARkey[$i]); 
            }

        }

        if (!$hasAR)
            $this->addErrorList($arrayToJs,false, $this->errorMsg['ar'][1]); 	

        
		for($i=0;$i<count($arrARkey);$i++) {  
            if(!empty($arrARkey[$i])){
                
                $outstanding = $this->unFormatNumber($arrOutstanding[$i]);
                $amount = $this->unFormatNumber($arrAmount[$i]);

                if ($amount == 0 || ($outstanding > 0 && $amount < 0) || ($outstanding < 0 && ($amount < $outstanding ||  $amount > 0)))  
                        $this->addErrorList($arrayToJs,false,'<strong>'.$arrAR[$arrARkey[$i]]. '</stong>. ' . $this->errorMsg['arPayment'][2]);
                

                if ($arrARCustomer[$arrARkey[$i]] <> $customerkey) 
                    $this->addErrorList($arrayToJs,false,'<strong>'.$arrAR[$arrARkey[$i]]. '</stong>. ' . $this->errorMsg['ar'][5]); 
                
				
				if ($arrARWarehouse[$arrARkey[$i]] <> $arr['selWarehouseKey'])
                    $this->addErrorList($arrayToJs,false,'<strong>'.$arrAR[$arrARkey[$i]]. '</stong>. ' . $this->errorMsg[905]); 
            }
		}
		   
		return $arrayToJs;
	 }
	     
	  	  
    function validateConfirm($rsHeader){
		
		$id = $rsHeader[0]['pkey']; 

        $ARObj = $this->getARObj();  
        $rsDetail = $this->getDetailById($id);
        $arrKeys = array_column($rsDetail,'arkey');
 

        if (!empty($arrKeys)){
            $arrKeys = implode(',',$arrKeys);
            $rsAR = $ARObj->searchData('','',true,' and ' .$ARObj->tableName.'.pkey in ('.$arrKeys.') and ' .$ARObj->tableName.'.statuskey in (3,4) ' );
            if (!empty($rsAR)){
                $arrAR = array_column($rsAR,'code');
                $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg[201].'<br>'.implode(', ',$arrAR).'. '.$this->errorMsg['arTax23'][6]); 
            }
        }
       
	 }
    
    function updateAPPrepaid($rsHeader,$rsDetail){
        //overwrite biar gk delete table sendiri
    }
    function deleteAPPrepaidTax($id){ 
        //overwrite biar gk delete table sendiri
    }
    
    function updateGL($rs, $rsPayment){
       // ar prepaid gk perlu jurnal, jurnalnya nanti manual adjustment pas akhir tahun
    }
    
    function getARObj(){
        return  new ARPrepaidTax23();
    }
	
    
     function afterStatusChanged($rsHeader){ 
           
        $ARObj = $this->getARObj();
        $rsDetail = $this->getDetailById($rsHeader[0]['pkey']);
        for($i=0;$i<count($rsDetail); $i++){  
           $ARObj->updateAROutstanding($rsDetail[$i]['arkey']); 
        }
         
     }
    
    function cancelTrans($rsHeader,$copy){ 

        $id = $rsHeader[0]['pkey']; 
        
		if ($copy)
			$this->copyDataOnCancel($id);	  
		   
	}
      
    function afterAddDataOnCopy($pkey, $oldkey){  
      
    }
    
 
}

?>
