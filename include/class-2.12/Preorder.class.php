<?php
  
class Preorder extends BaseClass{ 
   
   function __construct(){
		
		parent::__construct(); 
         
		$this->tableName = 'preorder_header';
		$this->tableNameDetail = 'preorder_detail';
		$this->tableCustomer = 'customer';
		$this->tableEmployee = 'employee';
		$this->tableWarehouse = 'warehouse'; 
		$this->tableStatus = 'transaction_status';
		$this->tableMovement = 'item_movement'; 
		$this->tableHistory = 'history';
		$this->tablePayment= 'sales_order_payment'; 	
		$this->tableItem = 'item'; 	
		  
		$this->securityObject = 'Preorder';  
		 
   }
   
   function getQuery(){
	   
	   return '
			SELECT '.$this->tableName.'.* ,
			   '.$this->tableCustomer.'.name as customername,
			   '.$this->tableWarehouse.'.name as warehousename,
			   '.$this->tableStatus.'.status as statusname ,
			   '.$this->tableEmployee.'.name as salesname 
			FROM '.$this->tableStatus.', '.$this->tableCustomer.'  ,'.$this->tableWarehouse.', '.$this->tableName.' left join '.$this->tableEmployee.' on  
					 '.$this->tableName.'.saleskey = '.$this->tableEmployee.'.pkey 
			WHERE '.$this->tableName.'.customerkey = '.$this->tableCustomer.'.pkey and
					 '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey and
					 '.$this->tableName.'.warehousekey = '.$this->tableWarehouse.'.pkey 
 		' .$this->criteria ; 
		 
    }  
	
   function addData($arrParam){
	   
		$arrayToJs = array(); 
		try{						

			if(!$this->oDbCon->startTrans())
				throw new Exception($this->errorMsg[100]);
            
			$arrayToJs = $this->validateForm($arrParam);
			if (!empty($arrayToJs)) 
					return $arrayToJs;
		
		 	         
		  				
				$pkey = $this->getNextKey($this->tableName);
				$usecode = $this->useAutoCode($this->tableName); 
	 
	 			if($usecode == 1)  
					$arrParam['code'] =  $this->getNewCode($this->tableName); 
		
				$isPriceIncludeTax=0;
				if(!empty($arrParam['chkIncludeTax'])) 
					$isPriceIncludeTax=1;  
						
				if (empty($arrParam['recipientName']))
					$arrParam['recipientName'] = '';
				if (empty($arrParam['recipientPhone']))
					$arrParam['recipientPhone'] = '';
				if (empty($arrParam['recipientEmail']))
					$arrParam['recipientEmail'] = '';
				if (empty($arrParam['recipientAddress']))
					$arrParam['recipientAddress'] = '';
					
				// hitung ulang subtotal 
				$reCountResult = $this->reCountSubtotal($arrParam); 
			 	$arrParam['detailCOGS'] = $reCountResult['detailCOGS'];
			 		          
				
				$sql = '
						INSERT INTO		
						 '.$this->tableName .' (
                            pkey, 
							code,
							trdate,
							warehousekey,
							customerkey, 
							termofpaymentkey,
							trnotes, 
							subtotal,
							finaldiscounttype,
							finaldiscount,
							beforetaxtotal,
							ispriceincludetax,
							taxpercentage,                                                        
							taxvalue,
							grandtotal, 
							totalpayment,
							balance, 
							profit, 
							createdby,
							createdon,
                            statuskey,
							saleskey,
							recipientname,
							recipientphone,
							recipientemail,
							recipientaddress,
							pointvalue
						)
						VALUES	( 
							'.$pkey.', 
							'.$this->oDbCon->paramString($arrParam['code']).',
							'.$this->oDbCon->paramDate($arrParam['trDate'],' / ').',  
							'.$this->oDbCon->paramString($arrParam['selWarehouseKey']).',
							'.$this->oDbCon->paramString($arrParam['hidCustomerKey']).', 
							'.$this->oDbCon->paramString($arrParam['selTermOfPaymentKey']).',
							'.$this->oDbCon->paramString($arrParam['trNotes']).',
							'.$this->oDbCon->paramString($reCountResult['subtotal']).',
							'.$this->oDbCon->paramString($this->unFormatNumber($arrParam['selFinalDiscountType'])).',
							'.$this->oDbCon->paramString($this->unFormatNumber($arrParam['finalDiscount'])).',
							'.$this->oDbCon->paramString($reCountResult['beforeTaxTotal']).',
							'.$this->oDbCon->paramString($reCountResult['isPriceIncludeTax']).',
							'.$this->oDbCon->paramString($this->unFormatNumber($arrParam['taxPercentage'])).',
							'.$this->oDbCon->paramString($this->unFormatNumber($arrParam['taxValue'])).',
							'.$this->oDbCon->paramString($reCountResult['grandtotal']).', 
							'.$this->oDbCon->paramString($reCountResult['totalPayment']).', 
							'.$this->oDbCon->paramString($reCountResult['balance']).',
							'.$this->oDbCon->paramString($reCountResult['profit']).', 
							'.$this->oDbCon->paramString($arrParam['createdBy']).', 
							now(),
							1 ,
							'.$this->oDbCon->paramString($arrParam['selSalesKey']).',
							'.$this->oDbCon->paramString($arrParam['recipientName']).',
							'.$this->oDbCon->paramString($arrParam['recipientPhone']).',
							'.$this->oDbCon->paramString($arrParam['recipientEmail']).',
							'.$this->oDbCon->paramString($arrParam['recipientAddress']).',
							'.$this->oDbCon->paramString($this->unFormatNumber($arrParam['pointValue'])).'
						)
				';
			  
				$this->oDbCon->execute($sql);
				                                    
				$this->updateDetail($pkey, $arrParam);	
				$this->updatePayment($pkey, $arrParam);
				
				
                $this->setTransactionLog(INSERT_DATA,$pkey);
            	
				if (isset($arrParam['hidSendEmail']) && !empty($arrParam['hidSendEmail'])){
					$customer = new Customer();
					$rsCustomer = $customer->getDataRowById($arrParam['hidCustomerKey']); 
					
					$invoice =  $this->generateInvoice($pkey);
					$emailTemplate = $this->getEmailTemplate(); 
		
					$patterns = array();
					$patterns[count($patterns)] = '/({{CONTENT}})/'; 
					
					$replacement = array();
					$replacement[count($replacement)] = $invoice;  
					 
					$email = preg_replace($patterns, $replacement, $emailTemplate);  
					 
					$this->sendMail('','',$this->lang['invoice'] . ' '. $code,$email,$rsCustomer[0]['email']);	
					
					$sql = 'update ' .$this->tableName.' set invoiceSent = invoiceSent + 1 where pkey = ' . $pkey ;
					$this->oDbCon->execute($sql);
	 			}
                     
                                
				$this->oDbCon->endTrans();
				
				
				if (isset($arrParam['fromFE']) && !empty($arrParam['fromFE'])){
					$_SESSION[$this->loginSession]['POcart']  = array();
					$_SESSION[$this->loginSession]['POpointValue'] = 0;
				}
				
				$this->addErrorList($arrayToJs,true,$this->lang['dataHasBeenSuccessfullyUpdated']);   

		}catch(Exception $e){
			$this->oDbCon->rollback();
			$this->addErrorList($arrayToJs,false,$e->getMessage());    
		}		
		
		return $arrayToJs; 
			
	}
    
	
        
    function editData($arrParam){
		 
		$arrayToJs = array();  
					
		try{ 
				$arrayToJs = $this->validateForm($arrParam,$arrParam['hidId']);
				if (!empty($arrayToJs)) 
						return $arrayToJs;
						 
			
				if(!$this->oDbCon->startTrans())
					throw new Exception($this->errorMsg[100]);
				
			
				$isPriceIncludeTax=0;
				if(!empty($arrParam['chkIncludeTax'])) 
					$isPriceIncludeTax=1; 
				
					
				if (empty($arrParam['recipientName']))
						$arrParam['recipientName'] = '';
				if (empty($arrParam['recipientPhone']))
						$arrParam['recipientPhone'] = '';
				if (empty($arrParam['recipientEmail']))
						$arrParam['recipientEmail'] = '';
				if (empty($arrParam['recipientAddress']))
						$arrParam['recipientAddress'] = '';
						
								
				// hitung ulang subtotal 
				$reCountResult = $this->reCountSubtotal($arrParam);
			 	$arrParam['detailCOGS'] = $reCountResult['detailCOGS']; 
				
				$sql = '
						UPDATE	
						 '.$this->tableName .'
						SET	  
							trdate = '.$this->oDbCon->paramDate($arrParam['trDate'],' / ').', 
							warehousekey = '.$this->oDbCon->paramString($arrParam['selWarehouseKey']).', 
							customerkey = '.$this->oDbCon->paramString($arrParam['hidCustomerKey']).', 
							termofpaymentkey ='.$this->oDbCon->paramString($arrParam['selTermOfPaymentKey']).',
							trnotes = 	'.$this->oDbCon->paramString($arrParam['trNotes']).',
							subtotal = '.$this->oDbCon->paramString($reCountResult['subtotal']).',
							finaldiscounttype ='.$this->oDbCon->paramString($this->unFormatNumber($arrParam['selFinalDiscountType'])).' ,
							finaldiscount = '.$this->oDbCon->paramString($this->unFormatNumber($arrParam['finalDiscount'])).',
							beforetaxtotal = '.$this->oDbCon->paramString($reCountResult['beforeTaxTotal']).',
							ispriceincludetax = '.$this->oDbCon->paramString($reCountResult['isPriceIncludeTax']).',
							taxpercentage = '.$this->oDbCon->paramString($this->unFormatNumber($arrParam['taxPercentage'])).',                                                      
							taxvalue = '.$this->oDbCon->paramString($this->unFormatNumber($arrParam['taxValue'])).',
							grandtotal = '.$this->oDbCon->paramString($reCountResult['grandtotal']).',  
							totalpayment = '.$this->oDbCon->paramString($reCountResult['totalPayment']).',  
							balance = '.$this->oDbCon->paramString($reCountResult['balance']).',  
							profit = '.$this->oDbCon->paramString($reCountResult['profit']).',  
							modifiedby = '.$this->oDbCon->paramString($arrParam['modifiedBy']).',
							modifiedon = now() ,
							saleskey = '.$this->oDbCon->paramString($arrParam['selSalesKey']).',
							recipientname = '.$this->oDbCon->paramString($arrParam['recipientName']).',
							recipientphone = '.$this->oDbCon->paramString($arrParam['recipientPhone']).',
							recipientemail = '.$this->oDbCon->paramString($arrParam['recipientEmail']).',
							recipientaddress = '.$this->oDbCon->paramString($arrParam['recipientAddress']).',
							pointvalue = '.$this->oDbCon->paramString($this->unFormatNumber($arrParam['pointValue'])).'
						WHERE	
						 pkey = '.$this->oDbCon->paramString($arrParam['hidId']).'
				';
						 				 					   
				$this->oDbCon->execute($sql);
				$this->updateDetail($arrParam['hidId'], $arrParam);  
				$this->updatePayment($arrParam['hidId'], $arrParam); 
                $this->setTransactionLog(UPDATE_DATA,$arrParam['hidId']);
					 
				if (isset($arrParam['hidSendEmail']) && !empty($arrParam['hidSendEmail'])){
					
					$customer = new Customer();
					$rsCustomer = $customer->getDataRowById($arrParam['hidCustomerKey']); 
					
					$invoice =  $this->generateInvoice($arrParam['hidId']);
					$emailTemplate = $this->getEmailTemplate(); 
		
					$patterns = array();
					$patterns[count($patterns)] = '/({{CONTENT}})/'; 
					
					$replacement = array();
					$replacement[count($replacement)] = $invoice;  
					 
					$email = preg_replace($patterns, $replacement, $emailTemplate);  
					 
					$this->sendMail('','',$this->lang['invoice'] . ' '. $arrParam['code'],$email,$rsCustomer[0]['email']);	 
					
					$sql = 'update ' .$this->tableName.' set invoiceSent = invoiceSent + 1 where pkey = ' .$arrParam['hidId'] ;
					$this->oDbCon->execute($sql);
	 			}
                     
					 		
				$this->oDbCon->endTrans();
				$this->addErrorList($arrayToJs,true,$this->lang['dataHasBeenSuccessfullyUpdated']);   

		}catch(Exception $e){
			$this->oDbCon->rollback();
			$this->addErrorList($arrayToJs,false,$e->getMessage());    
		}		
		
		return $arrayToJs; 
			 

	}
	
	function reCountSubtotal($arrParam){

				$isPriceIncludeTax=0;
				if(!empty($arrParam['chkIncludeTax'])) 
					$isPriceIncludeTax=1; 
			 
			
				$subtotal = 0 ;
				$grandtotal = 0;
				
				$itemkey = $arrParam['hidItemKey'];
				$taxValue = $this->unFormatNumber($arrParam['taxValue']); 
				
				$pointValue = 0;
				if (isset($arrParam['pointValue']) && !empty($arrParam['pointValue']))
					$pointValue = $this->unFormatNumber($arrParam['pointValue']); 
				
				$finalDiscount = $this->unFormatNumber($arrParam['finalDiscount']); 
				$finalDiscountType = $arrParam['selFinalDiscountType']; 
				$taxPercentage = $this->unFormatNumber($arrParam['taxPercentage']); 
				$taxValue = $this->unFormatNumber($arrParam['taxValue']); 
				$pointValue =$this->unFormatNumber($arrParam['pointValue']);  
				
				$arrItemkey = $arrParam['hidItemKey']; 
				$arrQty = $arrParam['qty']; 
				$arrPriceinunit = $arrParam['priceInUnit']; 
				$arrDiscountValueInUnit = $arrParam['discountValueInUnit']; 
				$arrDiscountType = $arrParam['selDiscountType']; 
				 
				$arrItemDetail = array();
				$item = new Item();
				$subtotalProfit = 0;
				
				for ($i=0;$i<count($itemkey);$i++){
					
					if (empty($itemkey[$i]))  
						continue;
					 
						$qty =  $this->unFormatNumber($arrQty[$i]);
						$priceInUnit = $this->unFormatNumber($arrPriceinunit[$i]);
						$discount = $this->unFormatNumber($arrDiscountValueInUnit[$i]);
						$discountType =  $this->unFormatNumber($arrDiscountType[$i]);
					 
						$discountValue = $discount;
					 
						if ($discount != 0){
							if ($discountType == 2)
								$discountValue = $discount/100 * $priceInUnit;
						}
						
						$detailSubtotal = $qty * ($priceInUnit - $discountValue);
						
						$arrItemDetail[$itemkey[$i]]['subtotal'] = $detailSubtotal;
						
						$subtotal += $detailSubtotal ;
						
						$priceInUnitBeforeTax = $priceInUnit - $discountValue;
					
						if ($isPriceIncludeTax == true) { 
								$taxValue = ($taxPercentage/(100 + $taxPercentage)) * $priceInUnitBeforeTax;   
								$priceInUnitBeforeTax = $priceInUnitBeforeTax - $taxValue ;
						}  
						
						$rsItem = $item->getDataRowById($itemkey[$i]);
						$arrItemDetail[$itemkey[$i]]['cogs'] = $rsItem[0]['cogs'];	
						$arrItemDetail[$itemkey[$i]]['profit'] = $priceInUnitBeforeTax - $rsItem[0]['cogs'];
					    $subtotalProfit += $qty * $arrItemDetail[$itemkey[$i]]['profit'];
				} 
				
				$grandtotal = $subtotal;
				
				if ($finalDiscount != 0){
					if ($finalDiscountType == 2)
						$finalDiscount = $finalDiscount/100 * $grandtotal;
				} 
				
				$beforeTaxTotal = $subtotal - $finalDiscount - $pointValue;
				$grandtotal = $beforeTaxTotal;
					 
 				if ($isPriceIncludeTax == false) {
						$taxValue = $beforeTaxTotal * $taxPercentage / 100;
						$grandtotal += $taxValue;
				}else{
						$taxValue = ($taxPercentage/(100 + $taxPercentage)) * $grandtotal;   
				 		$beforeTaxTotal = $grandtotal - $taxValue ;
				}
			 
				$balance = 0;
				$totalPayment = 0; 
				$payment = $arrParam['paymentMethodValue'];
				for($i=0;$i<count($payment);$i++){
					$totalPayment += $this->unFormatNumber($payment[$i]);
				} 
				$balance = $totalPayment - $grandtotal;
				  
				
				$profit = $subtotalProfit - $finalDiscount - $pointValue ;
				
				$reCountResult = array();
				$reCountResult['subtotal'] = $subtotal;
				$reCountResult['beforeTaxTotal'] = $beforeTaxTotal;
				$reCountResult['isPriceIncludeTax'] = $isPriceIncludeTax;
				$reCountResult['grandtotal'] = $grandtotal;
				$reCountResult['totalPayment'] = $totalPayment;
				$reCountResult['balance'] = $balance;
				$reCountResult['profit'] = $profit;
				$reCountResult['detailCOGS'] = $arrItemDetail;
				
				return $reCountResult;
				
	}
	
	
    function updateDetail($pkey,$arrParam){
		
	 	$sql = 'delete from '.$this->tableNameDetail.' where refkey = '. $this->oDbCon->paramString($pkey);
		$this->oDbCon->execute($sql);
		 
		$arrItemkey = $arrParam['hidItemKey']; 
		$arrQty = $arrParam['qty']; 
		$arrPriceinunit = $arrParam['priceInUnit']; 
		$arrDiscountValueInUnit = $arrParam['discountValueInUnit']; 
		$arrDiscountType = $arrParam['selDiscountType']; 
		
        $item = new Item();
		        
     	for ($i=0;$i<count($arrItemkey);$i++){
			
			$rsItem = $item->getDataRowById($arrItemkey[$i]); 
			$baseunitkey = $rsItem[0]['baseunitkey'];
			$unitconvmultiplier = 1;
			
			 
		 	$qty =  $this->unFormatNumber($arrQty[$i]);
			$priceInUnit = $this->unFormatNumber($arrPriceinunit[$i]);
		 	$discount = $this->unFormatNumber($arrDiscountValueInUnit[$i]);
			$discountType =  $this->unFormatNumber($arrDiscountType[$i]);
		 
		 	$discountValue = $discount;
		 
			if ($discount != 0){
				if ($discountType == 2)
					$discountValue = $discount/100 * $priceInUnit;
			}
			
			$subtotal = $qty * ($priceInUnit - $discountValue);  
			 
			$sql = 'insert into '.$this->tableNameDetail.' (
						refkey,
						itemkey,
						qty,  
						qtyinbaseunit,  
						unitkey,
						priceinunit, 
						priceinbaseunit, 
						unitconvmultiplier, 
						discounttype,
						discount,
						total,
						costinbaseunit,
						profit
					 ) values (
						'.$this->oDbCon->paramString($pkey).',
						'.$this->oDbCon->paramString($arrItemkey[$i]).',
						'.$this->oDbCon->paramString($qty).',
						'.$this->oDbCon->paramString($qty).',
						'.$this->oDbCon->paramString($baseunitkey).',
						'.$this->oDbCon->paramString($priceInUnit).',
						'.$this->oDbCon->paramString($priceInUnit).',
						1, 
						'.$this->oDbCon->paramString($discountType).',
						'.$this->oDbCon->paramString($discount).', 
						'.$this->oDbCon->paramString($subtotal).', 
						'.$this->oDbCon->paramString($arrParam['detailCOGS'][$arrItemkey[$i]]['cogs']).', 
						'.$this->oDbCon->paramString($arrParam['detailCOGS'][$arrItemkey[$i]]['profit']).'
					)';	 
			$this->oDbCon->execute($sql);
                                        
		}
		 
					
	}
        
    function updatePayment($pkey,$arrParam){
	 
		$sql = 'delete from '.$this->tablePayment.' where refkey = '. $this->oDbCon->paramString($pkey);
		$this->oDbCon->execute($sql);
		 
		$payment = $arrParam['paymentMethodValue'];
		$paymentMethodKey = $arrParam['paymentMethodKey'];
		
		for ($i=0;$i<count($payment);$i++){
			
			if ($payment[$i] <= 0)
				continue;
			         
			$sql = 'insert into '.$this->tablePayment.'(
						refkey ,
						paymentkey , 
						amount ) 
					values (
						'.$this->oDbCon->paramString($pkey).',
						'.$this->oDbCon->paramString($paymentMethodKey[$i]).', 
						'.$this->oDbCon->paramString($this->unFormatNumber($payment[$i])).' 
					)';
			 	
			$this->oDbCon->execute($sql);                                        
		}
			 
	}
 
     function validateForm($arr,$pkey = ''){
		$item = new Item();   
		$arrayToJs = array();
		
		$code = $arr['code'];
		$customerkey = $arr['hidCustomerKey'];  
		$arrItemkey = $arr['hidItemKey']; 
		$arrQty = $arr['qty']; 
		$arrPriceinunit = $arr['priceInUnit'];
		
		
 

		//validasi kalo status gk menunggu gk bisa edit 
		if (!empty($pkey)){
			$rs = $this->getDataRowById($pkey);
			if ($rs[0]['statuskey'] <> 1){
				$this->addErrorList($arrayToJs,false,$this->errorMsg[212]);
			}
		} 
					
		$rs = $this->isValueExisted($pkey,'code',$code);	 
		if(empty($code)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['code'][1]);
		}else if(count($rs) <> 0){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['code'][2]);
		}
			
			
		if(empty($customerkey)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['customer'][1]);
		}
		
		 for($i=0;$i<count($arrItemkey);$i++) { 
			if (empty($arrItemkey[$i]) ){ 
				$this->addErrorList($arrayToJs,false, $this->errorMsg['item'][1]); 	
			}
			
			if (!empty($arrItemkey[$i]) && ($this->unFormatNumber($arrQty[$i]) <= 0 || $this->unFormatNumber($arrPriceinunit[$i]) <= 0)){
				$rsItem = $item->getDataRowById($arrItemkey[$i]);
				$this->addErrorList($arrayToJs,false,$rsItem[0]['name']. '. ' . $this->errorMsg[500]); 
			}
		}
		
		if (isset($arr['fromFE']) && !empty($arr['fromFE'])){ 
				
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
		
		return $arrayToJs;
	 }
	  
	 
	  
	function changeStatus($id,$status,$reason='',$copy=false){
		
		$arrayToJs = array();
		  
		try{
		  
		  	switch ($status){
				case 1 :  $arrayToJs = $this->validateInput($id);
						 if (!empty($arrayToJs)) 
								return $arrayToJs;
						
						  break;
				case 2 : $arrayToJs = $this->validateConfirm($id);
						 if (!empty($arrayToJs)) 
								return $arrayToJs; 
						  break;
				case 3 : $arrayToJs = $this->validateClose($id);
						 if (!empty($arrayToJs)) 
								return $arrayToJs; 
						  break;
				case 4 : $arrayToJs = $this->validateCancel($id);
						 if (!empty($arrayToJs)) 
								return $arrayToJs; ; 
						  break; 
			}
			
			
			if(!$this->oDbCon->startTrans())
				throw new Exception($this->errorMsg[100]);
					 
			switch ($status){ 
				case 2 : $this->confirmTrans($id); break; 
				case 4 : $this->cancelTrans($id,$copy);
                          $this->afterCancelTrans($id);
                          break;  
			}
			
			$sql = 'update '.$this->tableName.' set statuskey = '.$this->oDbCon->paramString($status).' where pkey = ' . $this->oDbCon->paramString($id);
			$this->oDbCon->execute($sql);
			
            $rsStatus = $this->getStatusById ($status); 
            $this->setTransactionLog($rsStatus[0]['pkey'],$id);
            
			$this->oDbCon->endTrans();
			$this->addErrorList($arrayToJs,true,$this->lang['dataHasBeenSuccessfullyUpdated']);   
		
	    } catch(Exception $e){
			$this->oDbCon->rollback();
			$this->addErrorList($arrayToJs,false,$e->getMessage());
		}		
				 
 		return $arrayToJs; 
 	}
	 
	
	 
	function validateConfirm($id){
        $rewardsPoint = new RewardsPoint();
        $warehouse = new Warehouse();  
        $coaLink = new COALink();
		
		$rs = $this->getDataRowById($id);
		  
		$arrayToJs = array();
		
		if($rs[0]['statuskey'] <> 1){  
			$this->addErrorList($arrayToJs,false,'<strong>'.$rs[0]['code'].'</strong>. ' . $this->errorMsg[203]);
		}else{
			if($rs[0]['balance'] < 0 ){
				$this->addErrorList($arrayToJs,false,'<strong>'.$rs[0]['code'].'</strong>. '.$this->errorMsg[502]);
			} 
			
			$point = $rs[0]['pointvalue'];
			$currentPoint = $rewardsPoint->getSumTotalRewards($rs[0]['customerkey']) * $this->loadSetting('rewardsPointUnitValue');
			
		 	if ($point > $currentPoint)
				$this->addErrorList($arrayToJs,false,$this->errorMsg['point'][3]);
			 
			 
                $rsPayment = $this->getPaymentMethodDetail($id); 

                for($i=0;$i<count($rsPayment); $i++){ 
                    if ($rsPayment[$i]['amount'] > 0 ){ 
                        $rsPaymentCOA = $coaLink->getCOALink ('payment', $warehouse->tableName,$rs[0]['warehousekey'], $rsPayment[$i]['paymentkey']);
                        
                        if (empty($rsPaymentCOA))	
                            $this->addErrorList($arrayToJs,false,'<strong>'.$rs[0]['code'].'</strong>. '.$this->errorMsg['coa'][3]);
                    }
                }      
 
				
		}
		
		
		
	 	return $arrayToJs;
	 }
	 
	 
	function confirmTrans($id){
		$rsHeader = $this->getDataRowById($id);
		  
		$item = new Item();
		$customer = new Customer();
		$rsCustomer = $customer->getDataRowById($rsHeader[0]['customerkey']);
		$note = $rsHeader[0]['code'].'. Jual ke '.$rsCustomer[0]['name'];
		$warehouse = new Warehouse();
		$rsWarehouse = $warehouse->getDataRowById($rsHeader[0]['warehousekey']);
		$notecash = $rsHeader[0]['code'].'. Kas Masuk dari '.$rsWarehouse[0]['name'].' untuk penjualan barang dari '.$rsCustomer[0]['name'];
		$rsDetail = $this->getDetailById($rsHeader[0]['pkey']);
		 
		// MENGHITUNG PAYMENT
		$rsPayment = $this->getPaymentMethodDetail($id);  
		
		$cashMovement = new CashMovement();  
		$itemMovementPO = new ItemMovementPO();  
        $coaLink = new COALink();
        
		for($i=0;$i<count($rsPayment); $i++){  
            $rsPaymentCOA = $coaLink->getCOALink ('payment', $warehouse->tableName,$rsHeader[0]['warehousekey'], $rsPayment[$i]['paymentkey']);
                        
		   $cashMovement->updateCashMovement($id, $rsPaymentCOA[0]['coakey'],$rsPayment[$i]['amount'],$this->tableName, $rsHeader[0]['warehousekey'], $notecash,$rsHeader[0]['trdate']);
		}                        
		// END           
		
		 
		
		for($i=0;$i<count($rsDetail); $i++){		
		   $rsItem = $item->getDataRowById($rsDetail[$i]['itemkey']);  
		   $itemMovementPO->updateItemMovement($id,$rsDetail[$i]['itemkey'],-$rsDetail[$i]['qtyinbaseunit'], $rsItem[0]['cogs'] ,$this->tableName,  $note);
		}	 
		 
		// potong point jika digunakan
		if ($rsHeader[0]['pointvalue'] > 0){ 
			$totalPoint = ceil($rsHeader[0]['pointvalue'] / $this->loadSetting('rewardsPointUnitValue')); 
			
			$rewardsPoint = new RewardsPoint();
			$arr = array();
			$arr['pkey']  = $rewardsPoint->getNextKey($rewardsPoint->tableName);
			$arr['code'] = 'xxxxx';
			$arr['trDate'] =  date('d / m / Y');
			$arr['hidCustomerKey'] = $rsHeader[0]['customerkey'];
			$arr['hidSalesOrderKey'] = $rsHeader[0]['pkey'];
			$arr['point'] = $totalPoint*-1;
			$arr['createdBy'] = 0;
			$arr['notes'] = 'Redeem';
			
			$rewardsPoint->addData($arr);  
			$rewardsPoint->changeStatus($arr['pkey'] ,2);
		}
		 
		//update point jika ada
		$pointMultiple = $this->loadSetting('rewardsPointMultiple');
		if ($pointMultiple > 0){ 
			$beforeTaxTotal = $rsHeader[0]['beforetaxtotal']; 
			$totalPoint = floor($beforeTaxTotal/$pointMultiple); 
			
			$rewardsPoint = new RewardsPoint();
			$arr = array();
			$arr['pkey']  = $rewardsPoint->getNextKey($rewardsPoint->tableName);
			$arr['code'] = 'xxxxx';
			$arr['trDate'] =  date('d / m / Y');
			$arr['hidCustomerKey'] = $rsHeader[0]['customerkey'];
			$arr['hidSalesOrderKey'] = $rsHeader[0]['pkey'];
			$arr['point'] = $totalPoint ;
			$arr['createdBy'] = 0;
			$arr['notes'] = '';
			
			$rewardsPoint->addData($arr);  
			$rewardsPoint->changeStatus($arr['pkey'] ,2);
		}
		
	} 
	 
	function cancelTrans($id,$copy){ 
		
		$rsHeader = $this->getDataRowById($id); 
		
		if ($rsHeader[0]['statuskey'] == 1)
			return;
			
		$cashMovement = new CashMovement();  
		$itemMovementPO = new ItemMovementPO();  
		
		$cashMovement->cancelMovement($id,$this->tableName);
		$itemMovementPO->cancelMovement($id,$this->tableName); 
		
		if ($copy)
			$this->copyDataOnCancel($id);	  
		  
	 
	} 
	
	
	function addToCartSession($arr){    
	   	$arr['POOrderQty'] = $this->unFormatNumber($arr['POOrderQty']);
			
	  	 if ($arr['POOrderQty'] <= 0 || !is_numeric($arr['POOrderQty']))
				$arr['POOrderQty'] = 1;
		
		 if(isset($_SESSION[$this->loginSession]['POcart']))			
			$ctr = count($_SESSION[$this->loginSession]['POcart']);
		 else
			$ctr = 0;
			
		//cari apakah ad item yg sama
		for($i=0;$i<$ctr;$i++){ 
			if ($_SESSION[$this->loginSession]['POcart'][$i]['itemkey'] == $arr['POHiditemkey']){
				 $_SESSION[$this->loginSession]['POcart'][$i]['qty'] += $arr['POOrderQty'];
				 return true;
			 } 
		} 
			 
		$_SESSION[$this->loginSession]['POcart'][$ctr]['itemkey'] = $arr['POHiditemkey'];
		$_SESSION[$this->loginSession]['POcart'][$ctr]['pokey'] = $arr['POHidkey'];
		$_SESSION[$this->loginSession]['POcart'][$ctr]['qty'] = $arr['POOrderQty'];
		   
		return true; 
   } 
   
 
   function getlatestSellingPrice($itemkey,$customerkey){
		$sql = 'select 
					coalesce(priceInUnit,0) as price
				from 
					'.$this->tableName.',' . $this->tableNameDetail .'
				where 
					'.$this->tableName.'.pkey ='.$this->tableNameDetail.'.refkey and
					'.$this->tableName.'.customerkey = '.$this->oDbCon->paramString($customerkey).' and
					'.$this->tableNameDetail.'.itemkey = '.$this->oDbCon->paramString($itemkey);
					 
			
		$sql .= ' order by trdate desc limit 1';
		 
		$rs =  $this->oDbCon->doQuery($sql);
	 	
		if (empty($rs))
			return 0;
		else 
	   		return $rs[0]['price'];
		   
   }
   
   function getDetailWithRelatedInformation($pkey){
	   $sql = 'select
	   			' . $this->tableNameDetail .'.*, '.$this->tableItem.'.name as itemname, '.$this->tableItem.'.code as itemcode
			  from
			  	' . $this->tableNameDetail .','.$this->tableItem.'
			  where
			  	' . $this->tableNameDetail .'.itemkey = '.$this->tableItem.'.pkey and
			  	refkey = '.$this->oDbCon->paramString($pkey);
		return $this->oDbCon->doQuery($sql);
	
   }
   
     
   
      
   function generateInvoice($pkey){
	    
		global $defaultPath;
		$rsHeader = $this->getDataRowById($pkey);  
		$rsDetail = $this->getDetailById($rsHeader[0]['pkey']);
	 
		$content = '
			<div style="width:700px; color:#000; margin:auto;  font-family:Arial, Helvetica, sans-serif;  border:1px solid #999; padding:20px;">
			<div>
				<div style="float:left;"><img src="'.$this->defaultURLUploadPath.'setting/emailLogo/'.$this->loadSetting('emailLogo').'" /></div>
				<div style="float:right; margin-top:15px; text-align:center;"><span style="font-size:22px;">'.$rsHeader[0]['code'].'</span><br>'.$this->formatDBDate($rsHeader[0]['trdate']).'</div> 
				<div style="clear:both"></div>
			</div>	
			<div style="clear:both; height:10px;"></div>
			<table style=" width:100%;text-align:left;">   
				<tr>
					<td style="padding: 5px; vertical-align:top;border-top:1px solid #666;border-bottom:1px solid #666;font-weight:bold;width:30px; text-align:right;">#</td>  
					<td style="padding: 5px; vertical-align:top;border-top:1px solid #666;border-bottom:1px solid #666;font-weight:bold;width:350px">'.$this->lang['itemName'].'</td> 
					<td style="padding: 5px; vertical-align:top;border-top:1px solid #666;border-bottom:1px solid #666;font-weight:bold;width:80px; text-align:right;">@ '.$this->lang['price'].'</td>   
					<td style="padding: 5px; vertical-align:top;border-top:1px solid #666;border-bottom:1px solid #666;font-weight:bold;width:70px; text-align:right;">'.$this->lang['qty'].'</td>   
					<td style="padding: 5px; vertical-align:top;border-top:1px solid #666;border-bottom:1px solid #666;font-weight:bold;text-align:right;">'.$this->lang['subtotal'].' (IDR)</td>  
				</tr>
		';
	
		 
		
		if (!empty($rsDetail)) {
				$totalprice = 0;
				$totalqty = 0;
				
				$item = new Item();
				for($i=0;$i<count($rsDetail); $i++){
					 
					 $rsItem = $item->getDataRowById($rsDetail[$i]['itemkey']);
					 $rsImage = $item->getItemImage($rsDetail[$i]['itemkey']); 
					 
					 $totalqty += $rsDetail[$i]['qty'] ;
					
					 $discount = $rsDetail[$i]['discount'];  
					 if ($discount != 0){
						if ($rsDetail[$i]['discounttype'] == 2)
							$discount = $discount/100 *  $rsDetail[$i]['priceinunit'];
					 }
					
					 $unitprice = 	$rsDetail[$i]['priceinunit'] - $discount;
					 $totalprice += $rsDetail[$i]['qty'] * $unitprice ;
				
					 
					 $content .= '   
									<tr>
										<td style="padding: 5px; vertical-align:top;border-top:1px solid #e9e9e9; text-align:right;" >'.($i+1).'.</td>  
										<td style="padding: 5px; vertical-align:top;border-top:1px solid #e9e9e9;" class="text-cobalt-blue">
										<a href="'.$this->loadSetting('sitesName').'/products-detail/'.$rsItem[0]['pkey'].'/'.str_replace($this->arrSearch,$this->arrReplace,$rsItem[0]['name']).'" target="_blank">'.$rsItem[0]['name'].'</a>
										</td>
										<td style="padding: 5px; vertical-align:top;border-top:1px solid #e9e9e9;text-align:right;" >'.$this->formatNumber($unitprice,0,'.',',').'</td>   
										<td style="padding: 5px; vertical-align:top;border-top:1px solid #e9e9e9;text-align:right;" >'.$this->formatNumber($rsDetail[$i]['qty'],0,'.',',').'</td>   
										<td style="padding: 5px; vertical-align:top;border-top:1px solid #e9e9e9;text-align:right;" >'.$this->formatNumber($rsDetail[$i]['qty'] * $unitprice,0,'.',',').'</td>    
									</tr>  
							';
							
				}
				
				$discount = $rsHeader[0]['finaldiscount'];
				$point = $rsHeader[0]['pointvalue'];
				
				if ($discount != 0){
					if ($rsHeader[0]['finaldiscounttype'] == 2)
						$discount = $discount/100 * $totalprice;
				}
				
				 $content .= '   
					<tr > 
						<td colspan="3" style="padding: 5px; vertical-align:top;border-top:1px solid #000;font-weight:bold;text-align:right; ">'.$this->lang['total'].'</td>   
						<td style="padding: 5px; vertical-align:top;border-top:1px solid #000;font-weight:bold;text-align:right; " >'.$this->formatNumber($totalqty,0,'.',',').'</td>   
						<td style="padding: 5px; vertical-align:top;border-top:1px solid #000;font-weight:bold;text-align:right; " >'.$this->formatNumber($totalprice,0,'.',',').'</td>   
					</tr>';
				
				$addSubtotal = '';
				if ($discount > 0 )	
				 $addSubtotal .= ' 	
					<tr>
						<td colspan="3" style="padding: 5px; vertical-align:top; font-weight:bold;text-align:right; ">'.$this->lang['discount'].'</td>   
						<td style="padding: 5px; vertical-align:top;font-weight:bold;text-align:right; " ></td>   
						<td style="padding: 5px; vertical-align:top;font-weight:bold;text-align:right; " >- '. $this->formatNumber($discount,0,'.',',').'</td>   
					</tr>';
				
				if ($point > 0 )		
				 $addSubtotal .= ' 	<tr>
						<td colspan="3" style="padding: 5px; vertical-align:top; font-weight:bold;text-align:right; ">'.$this->lang['point'].'</td>   
						<td style="padding: 5px; vertical-align:top;font-weight:bold;text-align:right; " ></td>   
						<td style="padding: 5px; vertical-align:top;font-weight:bold;text-align:right; " >- '. $this->formatNumber($point,0,'.',',').'</td>   
					</tr>';

				if (!empty($addSubtotal)){	
				 $content .= $addSubtotal;	
				 $content .= '	<tr>
						<td  colspan="3" style="padding: 5px; vertical-align:top;font-weight:bold;text-align:right;">Grand Total</td>   
						<td style="padding: 5px; vertical-align:top;font-weight:bold;text-align:right;"></td>   
						<td style="padding: 5px; vertical-align:top;font-weight:bold;text-align:right;">'.$this->formatNumber($rsHeader[0]['grandtotal'],0,'.',',').'</td>   
					</tr> 
				';
				}
			}
				
			$content .='</table>
				<div style="clear:both; height:30px;"></div>
				<div style="font-size:12px">'.str_replace(chr(13),'<br>',$this->loadSetting('emailPOInvoiceFooter')).'</div>
			</div> 
			'; 
						
			 return $content;
	}
}
?>