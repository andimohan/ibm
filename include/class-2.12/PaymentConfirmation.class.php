<?php

class PaymentConfirmation extends BaseClass{
	 
 
   function __construct(){
		
		parent::__construct(); 
		
		$this->tableName = 'payment_confirmation';
		$this->tableSales = 'sales_order_header';
		$this->tableCustomer = 'customer';
		$this->tableStatus = 'transaction_status';
		$this->isTransaction = true;
		$this->securityObject = 'PaymentConfirmation';  
       
        $this->arrData = array();
        $this->arrData['pkey'] = array('pkey'); 
        $this->arrData['code'] = array('code'); 
        $this->arrData['trdate'] = array('trDate','date');
        $this->arrData['refkey'] = array('hidSalesKey');
        $this->arrData['customerkey'] = array('hidCustomerKey');
        $this->arrData['amount'] = array('amount','number');
       
        $this->arrData['paymentamount'] = array('paymentAmount','number');
        $this->arrData['paymentmethodkey'] = array('selPaymentMethod');
        $this->arrData['bankname'] = array('bankName');
        $this->arrData['bankaccountname'] = array('bankAccountName');
        $this->arrData['bankaccountnumber'] = array('bankAccountNumber');
        $this->arrData['branch'] = array('bankBranch');
        $this->arrData['paymentdate'] = array('paymentDate','date');
        $this->arrData['transdesc'] = array('trDesc');
       
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'date','title' => 'date','dbfield' => 'trdate','format'=>'date','default'=>true, 'width' => 80, 'align'=>'center'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'paymentdate','title' => 'paymentDate','dbfield' => 'paymentdate','format'=>'date','default'=>true, 'width' => 130, 'align'=>'center'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'invoice','title' => 'invoice','dbfield' => 'invoiceid','default'=>true, 'width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'customer','title' => 'customer','dbfield' => 'customername','default'=>true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'payment','title' => 'payment','dbfield' => 'amount','default'=>true, 'width' => 100, 'format' => 'integer'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'bank','title' => 'bankName','dbfield' => 'bankname','default'=>true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'bankaccountnumber','title' => 'bankAccountNumber','dbfield' => 'bankaccountnumber','default'=>true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'bankaccountname','title' => 'bankAccountName','dbfield' => 'bankaccountname', 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
       
        $this->overwriteConfig();
		
	}
	
	function getQuery(){
	   
	   return '
			SELECT 
                 '.$this->tableName.'.* , 
                 '.$this->tableSales.'.code as invoiceid,
			     '.$this->tableCustomer.'.name as customername, 
			     '.$this->tableStatus.'.status as statusname 
			FROM 
                 '.$this->tableStatus.', 
                 '.$this->tableSales.',
                  '.$this->tableName.'
                    left join '.$this->tableCustomer.' on '.$this->tableName.'.customerkey = '.$this->tableCustomer.'.pkey 
                  
			WHERE 
                '.$this->tableName.'.refkey = '.$this->tableSales.'.pkey and 
                '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey 
			
 		' .$this->criteria ; 
		 
    } 
    
  
    
    function afterStatusChanged($rsHeader){ 
        // retrieve latest status
        $rsHeader = $this->getDataRowById($rsHeader[0]['pkey']);
        $salesOrder = new SalesOrder();
        
        $salesOrder->updateStatusFromPaymentConfirmation($rsHeader[0]['refkey']); 
    }
    
	
	function validateForm($arr,$pkey = ''){ 
        
		$arrayToJs = parent::validateForm($arr,$pkey); 
		
		$invoiceId = $arr['hidSalesKey']; 
		$paymentMethodKey = $arr['selPaymentMethod']; 
		$paymentDate = $arr['paymentDate'];  
		$paymentAmount = $this->unFormatNumber($arr['paymentAmount']); 
		$amount = $this->unFormatNumber($arr['amount']); 
        
		
		if(empty($invoiceId)){
				$this->addErrorList($arrayToJs,false,$this->errorMsg['invoice'][1]);
		}
		
		/* utk handle edit bagian UI frontend  */
		if (isset($arr['fromFE']) && !empty($arr['fromFE'])){
		 
			$captchaResponse = $arr['g-recaptcha-response'];  
			$request = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$this->loadSetting('reCaptchaSecretKey')."&response=".$captchaResponse);
			$captchaResult = json_decode($request);
			
			$errorCaptcha= $captchaResult->{'error-codes'}; 
			  
		 
			/*if (empty($captchaResponse)){
				$this->addErrorList($arrayToJs,false,$this->errorMsg['captcha'][1]);
			} else if(!$captchaResult->{'success'}){
				$this->addErrorList($arrayToJs,false,$this->errorMsg['captcha'][1]);
			} */ 
			
		}
         
		if(empty($paymentMethodKey)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['paymentMethod'][1]);
		} 
        
        if($paymentAmount <= 0)
			$this->addErrorList($arrayToJs,false,$this->errorMsg[510]);
        
        if(empty($arr['bankName']))
			$this->addErrorList($arrayToJs,false,$this->errorMsg['bank'][1]);
		
        if(empty($arr['bankAccountName']))
			$this->addErrorList($arrayToJs,false,$this->errorMsg['bankaccountname'][1]);
		 
        if(empty($arr['bankAccountNumber']))
			$this->addErrorList($arrayToJs,false,$this->errorMsg['bankaccountnumber'][1]); 
    
        // amount tdk eprlu divalidasi, perlakukannya sama kaya ar ap saja, dihitugn ulang total konfirmasi pembyran
        
		return $arrayToJs;
	 }
	  
	
	function validateConfirm($rsHeader){
		$id = $rsHeader[0]['pkey'];
		
		$salesOrder = new SalesOrder();
		$rsSalesOrder = $salesOrder->getDataRowById($rsHeader[0]['refkey']);
		 
        if(empty($rsSalesOrder) || $rsSalesOrder[0]['statuskey']<>1)  
             $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' . $this->errorMsg['paymentConfirmation'][1]);  
        
        if($rsHeader[0]['paymentamount']<=0)
             $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' . $this->errorMsg[510]);  

	 }
	 
	 function confirmTrans($rsHeader){
		 /*$rs = $this->getDataRowById($id);
		 
		 $salesOrder = new SalesOrder();
		 $rsSalesOrder = $salesOrder->getDataRowById($rs[0]['refkey']);
		 
         $termOfPayment = new TermOfPayment();
         $rsTOP = $termOfPayment->searchData('systemVariable',1);
             
		 $arrParam['selTermOfPaymentKey'] = $rsTOP[0]['pkey'];
		 $arrParam['paymentMethodValue'][0] = $rs[0]['amount'];
		 $arrParam['paymentMethodKey'][0] = $rs[0]['paymentmethodkey'];
		 
		 $salesOrder->updatePayment($rsSalesOrder[0]['pkey'],$arrParam);
		 
		 $balance = $rsSalesOrder[0]['balance'] + $rs[0]['amount'];
		 
		 $sql = 'update '.$salesOrder->tableName.' set balance = '.$this->oDbCon->paramString($balance).' where pkey = ' . $this->oDbCon->paramString($rsSalesOrder[0]['pkey']);
		 $this->oDbCon->execute($sql);
		  
         $arrayToJs =  $salesOrder->changeStatus($rsSalesOrder[0]['pkey'],2);
         
         if ($arrayToJs[0]['valid'] == false){ 
             throw new Exception($arrayToJs[0]['message'] );
         }*/
	 }
    
	 function validateCancel($rsHeader,$autoChangeStatus=false){
         
        // kalo gk boleh cancel ketika SO sudah konfirmasi,
        // akan ada issue, kalo SO dikonfirmasi, payment jg harus di cancel otomatis..
         
		/*$id = $rsHeader[0]['pkey'];
		
		$salesOrder = new SalesOrder();
		$rsSalesOrder = $salesOrder->getDataRowById($rsHeader[0]['refkey']);
		  */
//	    if($rsSalesOrder[0]['statuskey'] != 1 && $rsSalesOrder[0]['statuskey'] != 4 )
//            $this->addErrorLog(false,'<strong>'.$rsSalesOrder[0]['code'].'</strong>. ' . $this->errorMsg[201]); 
         
/*         $rsPayment = $this->getPaymentSO($rsHeader[0]['refkey']);
          if(($rsPayment[0]['totalpayment'] - $rsHeader[0]['paymentamount']) < $rsPayment[0]['grandtotal'] && $rsSalesOrder[0]['statuskey'] != 1)
              $this->addErrorLog(false,'<strong>'.$rsSalesOrder[0]['code'].'</strong>. ' . $this->errorMsg[502]); 
         */
	 }
    
	 function cancelTrans($rsHeader,$copy){ 
		$id = $rsHeader[0]['pkey'];
         
		if ($copy)
			$this->copyDataOnCancel($id);	  
        
//        $this->cancelGLByRefkey($rsHeader[0]['pkey'],$this->tableName);
	 }
    
    function normalizeParameter($arrParam, $trim = false){
          
        if(isset($arrParam['hidSalesKey']) && !empty($arrParam['hidSalesKey'])){
            $salesOrder = new SalesOrder();
            $rsSalesOrder = $salesOrder->getDataRowById($arrParam['hidSalesKey']);
            
            $arrParam['hidCustomerKey'] = $rsSalesOrder[0]['customerkey'];
        }
       
        $arrParam = parent::normalizeParameter($arrParam,true); 
       
        return $arrParam;
    }
    
	 
}
?>
