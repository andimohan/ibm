<?php

class Downpayment extends BaseClass {
    
    function __construct() {
        
        parent::__construct();
        $this->tableName = 'downpayment';
        $this->tableStatus = 'transaction_status';
        $this->tableWarehouse = 'warehouse';
        $this->tableJobOrder = 'trucking_service_order_header';
        $this->tableSalesOrder = 'sales_order_header';
		$this->tableSalesOrderProperty = 'sales_order_property_header';
        $this->tablePayment = 'customer_downpayment_payment';
        $this->tablePaymentMethod = 'payment_method';
        $this->isTransaction = true;
        
        $arrPaymentDetail = array(); 
        $arrPaymentDetail['pkey'] = array('hidDetailPaymentKey');
        $arrPaymentDetail['refkey'] = array('pkey', 'ref');
        $arrPaymentDetail['amount'] = array('paymentMethodValue',array('datatype' => 'number','mandatory'=>true));
        $arrPaymentDetail['paymentkey'] = array('selPaymentMethod',array('mandatory'=>true));
         
    }
    
    function getQuery() {
        
        $sql = '
        select
                '.$this->tableName. '.*, 
                '.$this->tableStatus.'.status as statusname,
                '.$this->tableCustomer.'.name as customername, 
                '.$this->tableWarehouse.'.name as warehousename 
            from 
                '.$this->tableName . ',
                '.$this->tableStatus.',
                '.$this->tableCustomer.',
                '.$this->tableWarehouse.' 
            where  		 
                '.$this->tableName . '.statuskey = '.$this->tableStatus.'.pkey and
                '.$this->tableName . '.warehousekey = '.$this->tableWarehouse.'.pkey and 
                '.$this->tableName . '.customerkey = '.$this->tableCustomer.'.pkey
 		' .$this->criteria ; 
        
        $sql .= $this->getCompanyCriteria()	;
        return $sql;
    }  
     
    function validateCancel($rsHeader,$autoChangeStatus=false){
		parent::validateCancel($rsHeader,$autoChangeStatus);   
         
        // transaksi tetep tidak boleh dibatalkan jika sudah ad pembayaran (status DP <> open)  
        // meskipun transaksi manual atau transaksi dr sales order 
        if ( $rsHeader[0]['outstanding'] <> $rsHeader[0]['amount']) 
                $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' .$this->errorMsg['downpayment'][5]);     
          
	 } 	
    
    function cancelTrans($rsHeader,$copy){  
	    $id =  $rsHeader[0]['pkey'];
         
		if ($copy)
			$this->copyDataOnCancel($id);	  
       
        $this->cancelGLByRefkey($id,$this->tableName);
	}  
    
     function reCountGrandtotal($arrParam){
  
        $totalPayment = 0;   
        $payment = $arrParam['paymentMethodValue'];
        for($i=0;$i<count($payment);$i++){
            $totalPayment += $this->unFormatNumber($payment[$i]);
        }  
 
        $reCountResult['totalPayment'] = $totalPayment; 

        return $reCountResult;

    } 
    
    function getDownpaymentList($customerkey='',$salesOrderRef = array(), $availableOnly = false){
         
        $roderby = 'order by trdate desc';
        
        $criteria = ' and '.$this->tableName.'.statuskey in (2,3) '; 
        
        if(!empty($customerkey)) {  
            $customerField =   (isset($this->tableCustomer)) ? 'customerkey'  :  'supplierkey';  
            $criteria .= ' and '.$customerField.' = ' . $this->oDbCon->paramString($customerkey) ;
        }
        
        if(!empty($salesOrderRef)){  
            $criteria .= ' and reftabletype = ' . $this->oDbCon->paramString($salesOrderRef['reftabletype']) ; 
            if(!empty($salesOrderRef['refkey']))
                $criteria .= ' and refkey = ' . $this->oDbCon->paramString($salesOrderRef['refkey']) ;
        }
        
        
        if($availableOnly){ 
             $criteria .= ' and outstanding  > 0';
        }
         
        return  $this->searchData('','',true,$criteria,$roderby);
    }
	  
    function normalizeParameter($arrParam, $trim = false){
        
        $arrParam = parent::normalizeParameter($arrParam);  
     
        $arrParam['islinked'] = (!empty($arrParam['islinked'])) ? 1 : 0; 
        $arrParam['overwriteGL'] = (!empty($arrParam['overwriteGL'])) ? 1 : 0; 
  
        
        return $arrParam;
    }  
 
}

?>