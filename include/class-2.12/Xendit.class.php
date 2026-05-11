<?php

class Xendit extends BaseClass{
	
    function __construct(){ 
    	parent::__construct(); 
		$this->apiURL = 'https://api.xendit.co/v2/'; 
		
		
		$this->includeClassDependencies(array(
		   'MembershipSubscription.class.php' , 
		   'SalesOrder.class.php' 
		));  


		$this->overwriteConfig();
    }
	
	function getInvoice($arr){
		
		$url = $this->apiURL.'invoices';
		
		$payload = array();
		
	 	$payload['external_id'] = $arr['code'].'_'.time();
		$payload['currency'] =   $arr['currency'];
		$payload['amount'] =   $arr['amount'];
		$payload['payer_email'] = $arr['customerEmail']; 
		$payload['description'] = $arr['description'];
		
		$successURL = $this->loadSetting('PaymentGatewaySuccessURL');
		 
		 if(!empty($successURL)){
		        // kalo blm ad http:// abaikan
        		if (! (strpos($successURL,"https://")  !== false || strpos($successURL,"http://")  !== false)) 
        		    $successURL = HTTP_HOST . $successURL; 
        		
			 	$successURL .= '/'.$arr['pkey'].'/'.$arr['token']; 
        		$payload['success_redirect_url'] =  $successURL;
		 }
		
		
		
	//	$payload['failure_redirect_url'] = HTTP_HOST.'payment-pending';
		 
		return $this->execute($url,'POST',$payload);  
	}

	
	function execute($url,$method='GET',$payload=''){
		
		$apiKey = $this->loadSetting('PaymentGatewayServerKey'); // secretkey
		$auth = 'Basic '.base64_encode($apiKey.':');
		
		//$payload=json_encode($payload);
		return $this->executeCURL($url,$payload, array('auth' => $auth, 'method' => $method));

	}
	 
	function invoicePaid($data){
		
	    $transCode=explode('_',$data['external_id']);
	    $transCode = $transCode[0];
	     
	    $status=strtolower($data['status']);
	    if($status != 'paid'){
	        $this->setLog("Invoice not paid " .$transCode,true,'xendit');
            return false;
	        die;
	    }
	    
	    // update transaction
	    //$membershipSubscription = new MembershipSubscription();
	    //$rsMembership = $membershipSubscription->searchDataRow(array($membershipSubscription->tableName.'.pkey'),
	    //                                                        ' and '.$membershipSubscription->tableName.'.code = ' .$this->oDbCon->paramString($transCode).'
	    //                                                          and statuskey = 1 '
	    //                                                        );
	    //                                                        
     //  if (empty($rsMembership)){
     //       $this->setLog("Transaction not found " .$transCode,true,'xendit');
     //       return false;
	    //    die;
     //  }	                               
     //  
     //  
       //$membershipSubscription->changeStatus($rsMembership[0]['pkey'],2, '',false,true); 
	
		$salesOrder = new SalesOrder();
	    $rs = $salesOrder->searchDataRow(array($salesOrder->tableName.'.pkey'),
	                                                            ' and '.$salesOrder->tableName.'.code = ' .$this->oDbCon->paramString($transCode).'
	                                                              and statuskey = 1 '
	                                                            );
	                                                            
       if (empty($rs)){
            $this->setLog("Transaction not found " .$transCode,true,'xendit');
            return false;
	        die;
       }	                               
       
		
		try{

            if(!$this->oDbCon->startTrans())
                throw new Exception($this->errorMsg[100]);
          
			$salesOrder->updatePaidStatus($rs[0]['pkey'],3); 
		
	   // jgn otomatis, takutnya user gk tau
       //$salesOrder->changeStatus($salesOrder[0]['pkey'],2, '',false,true); 
		
			
            $this->oDbCon->endTrans();
       
        } catch(Exception $e){
            $this->oDbCon->rollback();
            $this->addErrorList($arrayToJs,false,$e->getMessage());
        }	
		
       return true;
       
	    
	}
    
}

?>