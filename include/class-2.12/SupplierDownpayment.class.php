<?php
class SupplierDownpayment extends Downpayment{
  
   function __construct(){
		
		parent::__construct();
		
		$this->tableName = 'supplier_downpayment'; 
		$this->tableSupplier = 'supplier';  
		$this->tableCurrency = 'currency';  
        $this->tablePayment = 'supplier_downpayment_payment'; 
        $this->tableCashBank = 'cash_bank';
        $this->tablePurchaseOrder = 'purchase_order_header'; 
	    $this->securityObject = 'SupplierDownpayment'; 
        $this->tableCashAdvanceRealization = 'cash_advance_realization_header';
        $this->isTransaction = true;
        
        $arrPaymentDetail = array(); 
        $arrPaymentDetail['pkey'] = array('hidDetailPaymentKey');
        $arrPaymentDetail['refkey'] = array('pkey', 'ref');
        $arrPaymentDetail['amount'] = array('paymentMethodValue',array('datatype' => 'number','mandatory'=>true));
        $arrPaymentDetail['paymentkey'] = array('selPaymentMethod');   // gk boleh mandatory, karena kadang pake payment kadang pake voucher, validasi di add saja
		$arrPaymentDetail['cashbankvoucherkey'] = array('selVoucher');  // gk boleh mandatory, karena kadang pake payment kadang pake voucher, validasi di add saja
       
        $arrDetails = array();
        array_push($arrDetails, array('dataset' => $arrPaymentDetail, 'tableName' => $this->tablePayment));
        
        $this->arrData = array(); 
        $this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails));
        $this->arrData['code'] = array('code'); 
        $this->arrData['trdate'] = array('trDate', 'date');
        $this->arrData['refkey'] = array('hidRefKey');
        $this->arrData['refcode'] = array('refCode'); 
        $this->arrData['reftabletype'] = array('selDPType');
        $this->arrData['trdesc'] = array('trDesc');
        $this->arrData['amount'] = array('amount', 'number');
        $this->arrData['outstanding'] = array('amount','number');
        $this->arrData['payment'] = array('payment','number');
        $this->arrData['islinked'] = array('islinked');
        $this->arrData['statuskey'] = array('selStatus'); 
        $this->arrData['overwriteGL'] = array('overwriteGL');
        $this->arrData['warehousekey'] = array('selWarehouse');
        $this->arrData['supplierkey'] = array('hidSupplierKey');
        $this->arrData['refcashadvancekey'] = array('refCashAdvanceKey');
        $this->arrData['refcashadvancedetailkey'] = array('refCashAdvanceDetailKey');
        $this->arrData['cashadvancecoakey'] = array('cashAdvanceCOAKey');
        $this->arrData['currencykey'] = array('selCurrency');
        $this->arrData['termofpaymentkey'] = array('selTermOfPaymentKey');
        $this->arrData['rate'] = array('currencyRate','number');
                
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'date','title' => 'date','dbfield' => 'trdate','default'=>true, 'width' => 80, 'align' =>'center', 'format' => 'date'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'refCode','title' => 'reference','dbfield' => 'refcode','default'=>true, 'width' => 120 ));
        array_push($this->arrDataListAvailableColumn, array('code' => 'supplier','title' => 'supplier','dbfield' => 'suppliername','default'=>true));
        array_push($this->arrDataListAvailableColumn, array('code' => 'amount','title' => 'amount','dbfield' => 'amount','default'=>true, 'width' => 80, 'align' => 'right', 'format' => 'number'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'outstanding','title' => 'outstanding','dbfield' => 'outstanding','default'=>true, 'width' => 80, 'align' => 'right', 'format' => 'number'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'currency','title' => 'curr','dbfield' => 'currencyname','default'=>true, 'align' => 'center', 'width' => 60));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
        array_push($this->arrDataListAvailableColumn, array('code' => 'warehouse','title' => 'warehouse','dbfield' => 'warehousename',  'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'cadr','title' => 'realization','dbfield' => 'cashadvancerealizationcode','default'=>true, 'width' =>150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'desc','title' => 'note','dbfield' => 'trdesc',  'width' => 200));
 
        $this->printMenu = array();  
        array_push($this->printMenu,array('code' => 'printTransaction', 'name' => $this->lang['printTransaction'],  'icon' => 'print', 'url' => 'print/supplierDownpayment')); 
        
        $this->useCashbankVoucher = (TABLENAME_SETTINGS[$this->tableName]['usecashbankvoucher'] == 1 && ADV_FINANCE && TEST_VOUCHER) ? true : false;
            
        $this->includeClassDependencies(array(  
            'Supplier.class.php',
            'TruckingServiceOrder.class.php',
            'COALink.class.php',
            'GeneralJournal.class.php',
            'Warehouse.class.php',
            'PaymentMethod.class.php',
            'AP.class.php',
            'APPayment.class.php',
            'CashBank.class.php',
            'Currency.class.php', 
            'SupplierDownpaymentSettlement.class.php',
            'TruckingServiceWorkOrder.class.php', 
            'TermOfPayment.class.php',
            'PurchaseOrder.class.php',
            'CashAdvanceRealization.class.php',
			'APCommissionPayment.class.php'
        ));  
       
        $this->overwriteConfig();

	}
		
   function getQuery(){
	   
		$sql = '
				select
					'.$this->tableName. '.*,
					'.$this->tableSupplier.'.name as suppliername,
					'.$this->tableStatus.'.status as statusname,
					'.$this->tableWarehouse.'.name as warehousename,
                    '.$this->tableCurrency.'.name as currencyname,
			  		'.$this->tableCashAdvanceRealization.'.code as cashadvancerealizationcode
				from 
					'.$this->tableName . '
                        left join '.$this->tableCurrency.' on '.$this->tableName. '.currencykey = '.$this->tableCurrency.'.pkey
						left join '.$this->tableCashAdvanceRealization.' on  '.$this->tableName.'.refcashadvancekey = '.$this->tableCashAdvanceRealization.'.pkey 
                    	left join '.$this->tableSupplier.' on  '.$this->tableName.'.supplierkey = '.$this->tableSupplier.'.pkey,
                    '.$this->tableStatus.' , 
                    '.$this->tableWarehouse.' 
				where  		
					'.$this->tableName . '.statuskey = '.$this->tableStatus.'.pkey and 
					'.$this->tableName . '.warehousekey = '.$this->tableWarehouse.'.pkey 
		' .$this->criteria ; 
       
       return $sql;
	}  
    
	function validateForm($arr,$pkey = ''){
		  
		$arrayToJs = parent::validateForm($arr,$pkey); 
        
        $supplier = new Supplier(); 
        
		$supplierkey = $arr['hidSupplierKey'];  
        $transactionType = $arr['selDPType'];
        $hidRefKey = $arr['hidRefKey'];
        
        //validasi kalo status gk menunggu gk bisa edit 
		if (!empty($pkey)){
			$rs = $this->getDataRowById($pkey);
			if ($rs[0]['statuskey'] <> 1){
				$this->addErrorList($arrayToJs,false,$this->errorMsg[212]);
			}
		}  
        
        $rsSupplier = $supplier->getDataRowById($supplierkey);
		if(empty($rsSupplier)) 
			$this->addErrorList($arrayToJs,false,$this->errorMsg['supplier'][1]);
	 
         // cek transaksi sesuai gk dengan supplier
        if(!empty($hidRefKey)){ 
            $rsObj = $this->getTableNameAndObjById($transactionType); 
            $obj = $rsObj['obj'];   
            $rsTSO = $obj->getDataRowById($hidRefKey); 
            
            //cara cek klo benar ada outsource atau hutang sesuai dengan supplier gimana ?
            if($rsTSO[0]['supplierkey'] <> $supplierkey){
                $this->addErrorList($arrayToJs,false, $this->errorMsg['downpayment'][4]);  
            } 
        } 
          
		return $arrayToJs;
	 } 
    
   
        
	 function validateClose($rsHeader){ 
        
        $id = $rsHeader[0]['pkey'];
         
        $arrayToJs = array(); 
		$rs  = $this->getDataRowById($id); 
         
        if($rs[0]['outstanding'] <=0) return; // boleh closed kalo sudah habis
            
        $this->addErrorLog(false, '<strong>' . $rsHeader[0]['code'] . '</strong>. ' . $this->errorMsg[201]);
		return $arrayToJs;
          
	 } 	 
    
    function validateConfirm($rsHeader){
		
	    $id = $rsHeader[0]['pkey'];
        $coaLink = new COALink();
        $warehouse = new Warehouse();
        $supplierkey = $rsHeader[0]['supplierkey'];
	

        // cek transaksi sesuai gk dengan supplier
        if(!empty($rsHeader[0]['refkey'])){
            $rsObj = $this->getTableNameAndObjById($rsHeader[0]['reftabletype']); 
            $obj = $rsObj['obj'];   
            $rs = $obj->getDataRowById($rsHeader[0]['refkey']);
            
            if($rs[0]['supplierkey'] <> $rsHeader[0]['supplierkey'])
                $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg['downpayment'][4]);     
        } 
         		
        $rsPayment = ($this->useCashbankVoucher) ?  $this->getPaymentVoucherDetail($id,'',2) : $this->getPaymentMethodDetail($id); 
        
        $termOfPayment = new TermOfPayment();
 		$rsTOP = $termOfPayment->getDataRowById($rsHeader[0]['termofpaymentkey']); 
		$isCash = ($rsTOP[0]['duedays'] == 0) ? true : false; 
        
        
        $totalPayment = 0; 
        for($i=0;$i<count($rsPayment); $i++)
            $totalPayment += $rsPayment[$i]['amount'];

        $balance = $totalPayment - $rsHeader[0]['amount'];   
  
        if ($isCash){ 
            $thresholdDiscount = 0;
            if(empty($rsHeader[0]['refcashadvancekey'])){
                if($balance < ($thresholdDiscount * -1)) 
                    $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg[502]);
                else if ($balance > $thresholdDiscount)
                    $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg[509]); 
           }
        }
              
        if (USE_GL){  
            $arrCOA = array();
            array_push($arrCOA, 'supplierdownpayment'); 
            for ($i=0;$i<count($arrCOA);$i++){
                $rsCOA = $coaLink->getCOALink ($arrCOA[$i], $warehouse->tableName,$rsHeader[0]['warehousekey'], 0); 
                if (empty($rsCOA))	
                    $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$arrCOA[$i]. ' ' .$this->errorMsg['coa'][3]);
            }   

            if ($isCash){ 
                
                 if ($this->useCashbankVoucher){
						for($i=0;$i<count($rsPayment); $i++){ 
							// cek kalo customerkey sudah beda
							if ($rsPayment[$i]['vouchersupplierkey'] <> $supplierkey)
								 $this->addErrorLog(false,'<b>'.$rsPayment[$i]['vouchercode']. '</b>. ' . $this->errorMsg['cashBank'][3]); 
							else if ($rsPayment[$i]['voucheroutstanding'] < $rsPayment[$i]['amount'])
								// cek kalo outstanding masih cukup
								 $this->addErrorLog(false,'<b>'.$rsPayment[$i]['vouchercode']. '</b>. ' . $this->errorMsg['cashBank'][4]); 

							else if ($rsPayment[$i]['voucherstatuskey'] <> TRANSACTION_STATUS['konfirmasi'])
								 $this->addErrorLog(false,'<b>'.$rsPayment[$i]['vouchercode']. '</b>. ' . $this->errorMsg['cashBank'][5]); 

						}  
				}else{ 
                    for($i=0;$i<count($rsPayment); $i++){ 
                        if ($rsPayment[$i]['amount'] > 0 ){ 
                            $rsCOA = $coaLink->getCOALink ('payment', $warehouse->tableName,$rsHeader[0]['warehousekey'], $rsPayment[$i]['paymentkey']); 
                            if (empty($rsCOA))	
                                $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg['coa'][3]); 
                        }
                    } 
                 } 
                
            }
 
         } 

	 }
       
      function confirmTrans($rsHeader){ 
        $id = $rsHeader[0]['pkey'];
         
        $coaLink = new COALink(); 
		$warehouse = new Warehouse(); 
        $cashBank = new CashBank(); 
        $supplier = new Supplier();
        $rsSupplier = $supplier->getDataRowById($rsHeader[0]['supplierkey']);
 
	    $termOfPayment = new TermOfPayment();
		$rsTOP = $termOfPayment->getDataRowById($rsHeader[0]['termofpaymentkey']);  
		$isCash = ($rsTOP[0]['duedays'] == 0) ? true : false;           
                      
        $rate = ($rsHeader[0]['currencykey']==CURRENCY['idr']) ? 1 : $rsHeader[0]['rate']; 
		$rsPayment = $this->getPaymentMethodDetail($id);   
		

        if ($isCash){
            
            
			if ($this->useCashbankVoucher){ 
				$rsPayment = $this->getPaymentVoucherDetail($id,'',2);

				$rsAPKey = $this->getTableKeyAndObj($this->tableName,array('key'));    

				// update outstanding voucher  
				foreach($rsPayment as $voucherlist){ 
					$cashBank->insertTransaction(
						array('refkey' => $voucherlist['cashbankvoucherkey'],
							  'reftablekey' => $rsAPKey['key'],
							  'reftranskey' => $rsHeader[0]['pkey'],
							  'refcode' => $rsHeader[0]['code'],
							  'refdate' => $rsHeader[0]['trdate'],
							  'amount' => $voucherlist['amount'],
							 )
					); 
				}

			}else{
                
                for($i=0;$i<count($rsPayment); $i++){   
                    if (USE_GL) {
                       $rsPaymentCOA = $coaLink->getCOALink ('payment', $warehouse->tableName,$rsHeader[0]['warehousekey'], $rsPayment[$i]['paymentkey']); 
                       $coakey = $rsPaymentCOA[0]['coakey']; 
                   }else{
                       $coakey = $rsPayment[$i]['paymentkey'];
                   }

                   $rsCashBank = $cashBank->addCashBank($rsHeader,$this->tableName, array('supplierkey' => $rsHeader[0]['supplierkey'],'coakey' => $coakey, 'desc' => $this->lang['supplierDownpayment'].', ' . $rsSupplier[0]['name'],  'amount' => -$rsPayment[$i]['amount'])); 
                   $rsPayment[$i]['cashBankKey'] = $rsCashBank['pkey'];
                } 
                
            }
		}else{
                $ap = new AP();
				
				$arrParam = array();	
                 
                $refTableKey = $ap->getTableKeyAndObj($this->tableName, array('key'))['key'];
			
                $arrParam['code'] = 'xxxxxx';
				$arrParam['hidSupplierKey'] = $rsHeader[0]['supplierkey'];
				$arrParam['hidRefKey'] = $id;
				$arrParam['hidRefHeaderKey'] = $id;
                $arrParam['hidRefCode'] =  $rsHeader[0]['code'];
                //$arrParam['hidRefCode2'] =  $rsHeader[0]['refcode'];
                $arrParam['hidRefTable'] = $refTableKey;
                $arrParam['hidRefDate'] =   $this->formatDBDate($rsHeader[0]['trdate'],'d / m / Y'); 
				$arrParam['amount'] = abs($rsHeader[0]['amount']);
				$arrParam['amountIDR'] = abs($rsHeader[0]['amount']) * $rate; 
				$arrParam['trDesc'] = '';
                $arrParam['trDate'] =  $this->formatDBDate($rsHeader[0]['trdate'],'d / m / Y');  
                $date = new DateTime($rsHeader[0]['trdate']);
                $date->add(new DateInterval('P'.$rsTOP[0]['duedays'].'D'));
                $arrParam['dueDate'] = $date->format('d / m / Y');// date ('d / m / Y', mktime(0, 0, 0, date("m")  , date("d")+$rsTOP[0]['duedays'], date("Y")));
				$arrParam['createdBy'] = 0;
                $arrParam['selWarehouse'] = $rsHeader[0]['warehousekey'];
                $arrParam['islinked'] = 1;
                $arrParam['overwriteGL'] = 1;
                $arrParam['selAPType'] = 7;
                $arrParam['selCurrency'] =  $rsHeader[0]['currencykey']; 
				$arrParam['currencyRate'] = $rate;
            
            
				$arrayToJs = $ap->addData($arrParam); 
            
                if (!$arrayToJs[0]['valid'])
                    throw new Exception('<strong>'.$rsHeader[0]['code'] . '</strong>. '.$this->errorMsg[201].' '.$arrayToJs[0]['message']);    
    
            
        }
		//update jurnal umum 
        // biar gk usah query ulang
        $rsHeader[0]['suppliername'] = $rsSupplier[0]['name'];
        $this->updateGL($rsHeader,$rsPayment);
	} 
    
    function cancelTrans($rsHeader,$copy){ 

        $id = $rsHeader[0]['pkey'];
  
        $rsKey = $this->getTableKeyAndObj($this->tableName,array('key')); 
		
		if( $this->isActiveModule('CashBank') ){
			$cashBank = new CashBank();
			if ($this->useCashbankVoucher){ 
				$cashBank->removeTransaction($id,$rsKey['key']);
			}else{ 
				$cashBank->cancelCashBank($rsHeader,$this->tableName);
			}
		}
        
   
        $ap = new AP();
        $rsAPKey = $ap->getTableKeyAndObj($this->tableName); 
		$rsAP = $ap->searchData('','',true,' and '.$ap->tableName.'.reftabletype = '.$this->oDbCon->paramString($rsAPKey['key']).' and '.$ap->tableName.'.refkey = '.$this->oDbCon->paramString($id).' and '.$ap->tableName.'.statuskey = 1');
		for($i=0;$i<count($rsAP);$i++) {
            $arrayToJs = $ap->changeStatus($rsAP[$i]['pkey'],4,'',false,true);
            if (!$arrayToJs[0]['valid'])
                throw new Exception('<strong>'.$rsHeader[0]['code'] . '</strong>. '.  $arrayToJs[0]['message']);    
        }	

		if ($copy)
			$this->copyDataOnCancel($id);
        
        $this->cancelGLByRefkey($id,$this->tableName);

		  
	}

    function updateGL($rs,$rsPayment){
        if (!USE_GL) return;
        
        $isActiveCashBank = $this->isActiveModule('CashBank');
        
        // kalo dr invoice, jgn buat jurnal DP lg.
        
        $warehouse = new Warehouse();
        $coaLink = new COALink();
        $supplier = new Supplier();
        $cashBank = new CashBank(); 
        $generalJournal = new GeneralJournal();
        $chartOfAccount = new ChartOfAccount();
        
		
        $termOfPayment = new TermOfPayment();
        $rsTOP = $termOfPayment->getDataRowById($rs[0]['termofpaymentkey']);
        $isCash = ($rsTOP[0]['duedays'] == 0) ? true : false;
		
         $warehousekey = $rs[0]['warehousekey'] ; 
                 
         $rate = ($rs[0]['rate'] > 0) ? $rs[0]['rate'] : 1 ;
         $currencykey = $rs[0]['currencykey'];
         
         //$hasPrepaidTax = (!empty($rs[0]['prepaidtax23'])) ? true : false;
         //$hasTax = (!empty($rs[0]['prepaidtax23'])) ? true : false;
         
         $desc = array();
         if (!empty($rs[0]['suppliername']))
            array_push($desc,$this->lang['supplierDownpayment'] .', '.$rs[0]['suppliername']);
        
         if (!empty($rs[0]['trdesc']))
            array_push($desc, $rs[0]['trdesc']);
        
         $desc = implode(chr(13),$desc);
        
        
         $rsKey = $generalJournal->getTableKeyAndObj($this->tableName);
		 $arr = array();
		 $arr['pkey'] = $generalJournal->getNextKey($generalJournal->tableName);
		 $arr['code'] = 'xxxxx';
		 $arr['refkey'] = $rs[0]['pkey'];
		 $arr['refTableType'] = $rsKey['key'];
		 $arr['trDate'] =  $this->formatDBDate($rs[0]['trdate'],'d / m / Y'); 
		 $arr['trDesc'] = $desc;
		 $arr['createdBy'] = 0; 
		 $arr['selWarehouseKey'] = $rs[0]['warehousekey'];
         $temp = -1; 
        
        $temp++;
        $arr['hidCOAKey'][$temp] = $supplier->getDownpaymentCOAKey($rs[0]['supplierkey'],$warehousekey) ;
        $arr['debit'][$temp] = $rs[0]['amount'] * $rate; 
        $arr['credit'][$temp] = 0 ;    
        $arr['selCurrencyKey'][$temp] = $currencykey ; 
        $arr['debitSource'][$temp] =$rs[0]['amount']; 
        $arr['creditSource'][$temp] = 0; 
        $arr['rate'][$temp] = $rate ;  
        $arr['refCashBankKey'][$temp] = '';  
        
        
        if(!empty($rs[0]['refcashadvancekey'])){ 
            $temp++; 
            $arr['hidCOAKey'][$temp] = $rs[0]['cashadvancecoakey'];
            $arr['debit'][$temp] = 0;
            $arr['credit'][$temp] =  $rs[0]['amount'] * $rate; 
            $arr['selCurrencyKey'][$temp] = $currencykey ; 
            $arr['debitSource'][$temp] = 0; 
            $arr['creditSource'][$temp] = $rs[0]['amount']; 
            $arr['rate'][$temp] = $rate ;
            $arr['refCashBankKey'][$temp] = '';  
        }else{
	 		if ($isCash) {
                
                
                if($this->useCashbankVoucher) 
                    $rsPayment = $this->getPaymentVoucherDetail($rs[0]['pkey'],'',2);
                
				for($i=0;$i<count($rsPayment); $i++){ 
//					 $rsCOA = $coaLink->getCOALink ('payment', $warehouse->tableName,$warehousekey,$rsPayment[$i]['paymentkey']); 
                     
                    if($this->useCashbankVoucher){ 
                        $rsCashBank = $cashBank->getDataRowById($rsPayment[$i]['cashbankvoucherkey']); 
                        
//                        $this->setLog($rsCashBank,true);
                        
                        $rsCOA = $chartOfAccount->getDataRowById($rsCashBank[0]['coakey']); 
                        $paymentcoakey = $rsCOA[0]['countercoakey'];
                    }else{
                        $rsCOA = $coaLink->getCOALink ('payment', $warehouse->tableName,$warehousekey,$rsPayment[$i]['paymentkey']); 
                        $paymentcoakey = $rsCOA[0]['coakey'];
                    }
 

					 $temp++;
					 $arr['hidCOAKey'][$temp] = $paymentcoakey;
					 $arr['debit'][$temp] = 0; 
					 $arr['credit'][$temp] = $rsPayment[$i]['amount']* $rate;   
                     $arr['selCurrencyKey'][$temp] = $currencykey ; 
                     $arr['debitSource'][$temp] = 0; 
                     $arr['creditSource'][$temp] = $rsPayment[$i]['amount']; 
                     $arr['rate'][$temp] = $rate ;   
					 $arr['refCashBankKey'][$temp] = $rsPayment[$i]['cashBankKey'];  
				}
                
                
			}else{
					$temp++;
					$arr['hidCOAKey'][$temp] = $supplier->getAPCOAKey($rs[0]['supplierkey'],$warehousekey);
					$arr['debit'][$temp] = 0; 
					$arr['credit'][$temp] =  $rs[0]['amount'] * $rate;   
                    $arr['selCurrencyKey'][$temp] = $currencykey ; 
                    $arr['debitSource'][$temp] = 0; 
                    $arr['creditSource'][$temp] = $rs[0]['amount']; 
                    $arr['rate'][$temp] = $rate ;   
                    $arr['refCashBankKey'][$temp] = '';  
				}
        }
        
                  
/*        if($hasPrepaidTax){ 
         $rsCOA = $coaLink->getCOALink ('prepaidtax23', $warehouse->tableName,$warehousekey,0); 
         $temp++;
         $arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];
         $arr['debit'][$temp] = $rs[0]['prepaidtax23']; 
         $arr['credit'][$temp] = 0;  
        }

        $rsCOA = $coaLink->getCOALink ('taxout', $warehouse->tableName,$warehousekey, 0);
        $temp++;
        $arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];
        $arr['debit'][$temp] = 0;
        $arr['credit'][$temp] = $rs[0]['taxvalue'];   
           */ 

		$arrayToJs = $generalJournal->addData($arr);
        
		if (!$arrayToJs[0]['valid'])
                throw new Exception('<strong>'.$rs[0]['code'] . '</strong>. '.$this->errorMsg[504].' '.$arrayToJs[0]['message']); 
	 }
    
    
    function getTotalDownpayment($refkey,$supplierkey){
        $sql = 'select 
                    coalesce(sum(amount),0) as totaldownpayment
                from 
                    '.$this->tableName.' 
                where statuskey in (2,3,4) ';
        
        if(!empty($refkey))
            $sql .= ' and refkey = '.$this->oDbCon->paramString($refkey);
        
        if(!empty($supplierkey))
            $sql .= ' and supplierkey = '.$this->oDbCon->paramString($supplierkey);
            
            

        $rs = $this->oDbCon->doQuery($sql);	
        
        return $rs;
    }
   

    function getTotalOutstanding($supplierkey, $currencykey = CURRENCY['idr'], $refkey = ''){
        $sql = 'select 
                    coalesce(sum(outstanding),0) as totaldownpayment,
                    ' . $this->tableCurrency . '.name as currencyname
                from 
                    '.$this->tableName.' 
                    left join ' . $this->tableCurrency . ' on ' . $this->tableName . '.currencykey = ' . $this->tableCurrency . '.pkey
                where 
                    '.$this->tableName . '.supplierkey = '.$this->oDbCon->paramString($supplierkey) .' and
                    '.$this->tableName . '.currencykey = '.$this->oDbCon->paramString($currencykey).' and
                    '.$this->tableName . '.outstanding > 0 and '.$this->tableName . '.statuskey in (1,2) ';
        
 
        if(!empty($refkey))
            $sql .= ' and refkey = '.$this->oDbCon->paramString($refkey);
        
            
        $rs = $this->oDbCon->doQuery($sql);	
        
        return $rs;
    }

    function getPurchaseOrder($criteria='', $orderBy = ''){ 
        
        $arrTable = array();
        array_push($arrTable,array('tableName' => $this->tableJobOrder,'statuskey' => '(1,2,3,4,5,6)' ));
        array_push($arrTable,array('tableName' => $this->tablePurchaseOrder,'statuskey' => '(1,2)' ));
        $arrSQL = array();
        foreach($arrTable as $table){  
            $rsKey = $this->getTableKeyAndObj($table['tableName']);
            
            $sql = 'select pkey, code as value, '.$rsKey['key'].' as  tabletypekey from ' . $table['tableName'] .' where statuskey in ' . $table['statuskey'];    
            array_push($arrSQL, $sql);
        }
        
        $sql = 'select * from ('.implode(' union ',$arrSQL ).') as purchase_order where 1=1 ';
        
        if (!empty($criteria))
            $sql .= ' ' . $criteria;
        
        if (!empty($orderBy))
            $sql .= ' ' . $orderBy;
        
        //$this->setLog($sql,true);
        $rs =  $this->oDbCon->doQuery($sql);	
        return $rs;
        
    }
    
    
    function generateDefaultQueryForAutoComplete($returnField){ 
        $sql = 'select
                '.$returnField['key'].',
                '.$returnField['value'].' as value,
                '.$this->tableName . '.refcode,
                '.$this->tableName . '.amount,
                '.$this->tableName . '.outstanding
            from 
                '.$this->tableName . ',
                '.$this->tableStatus.'  
            where  		
                '.$this->tableName . '.statuskey = '.$this->tableStatus.'.pkey  
        ';
        
        $sql .=  $this->getCompanyCriteria() ;
        return $sql;
        
    }
    
    function normalizeParameter($arrParam, $trim = false){
        
		$termOfPayment = new TermOfPayment();
		   
        $transactionType = $arrParam['selDPType'];
        $hidRefKey = $arrParam['hidRefKey']; 
        $arrParam['paymentMethodValue'] = (isset($arrParam['paymentMethodValue'])) ? $arrParam['paymentMethodValue'] : array();  
        
        $arrParam['currencyRate'] =  (isset($arrParam['currencyRate'])) ? $arrParam['currencyRate'] : 1;  
        $arrParam['selCurrency'] =  (isset($arrParam['selCurrency'])) ? $arrParam['selCurrency'] : CURRENCY['idr'];   
        $arrParam['currencyRate'] =   ($arrParam['selCurrency'] == CURRENCY['idr']) ? 1 : $arrParam['currencyRate'];
   	
		
		$rsTOP = $termOfPayment->getDataRowById($arrParam['selTermOfPaymentKey']);  
		if ($rsTOP[0]['duedays'] != 0){   
			for($i=0;$i<count( $arrParam['paymentMethodValue']);$i++){ 
				$arrParam['paymentMethodValue'][$i] = 0; 
				$arrParam['hidDetailPaymentKey'][$i] = 0;
			}
		}
		   
		$isCashAdvance = (isset($arrParam['refCashAdvanceKey']) && !empty($arrParam['refCashAdvanceKey'])) ? true : false;
 
        if( !$isCashAdvance ){  
            foreach($arrParam['paymentMethodValue'] as $key=>$row){ 
                if ($this->unFormatNumber($row) == 0){ 
                    unset($arrParam['selPaymentMethod'][$key]);
                    unset($arrParam['paymentMethodValue'][$key]); 
                    unset($arrParam['hidDetailPaymentKey'][$key]); 
                }
            }
            
            $arrParam['selPaymentMethod'] =  array_values($arrParam['selPaymentMethod']);
            $arrParam['paymentMethodValue'] = array_values($arrParam['paymentMethodValue']); 
            $arrParam['hidDetailPaymentKey'] = array_values($arrParam['hidDetailPaymentKey']); 
        }
        
     $reCountResult = $this->reCountGrandtotal($arrParam);  

        $arrParam['payment'] = $reCountResult['payment'];
        
        if( $isCashAdvance ){  
            $arrParam['selPaymentMethod'] = array('0' => -1);
            $arrParam['paymentMethodValue'] = array('0' => $arrParam['amount']);
            $arrParam['totalPayment'] = $arrParam['amount'];
            $arrParam['hidDetailPaymentKey'] = array('0' => 0);
            $arrParam['balance'] = 0; 
            $arrParam['selTermOfPaymentKey'] = 1; 
            $arrParam['payment'] = $arrParam['amount'];
        } 
        
        $arrParam = parent::normalizeParameter($arrParam,true);
        
        return $arrParam;
    }
    
    
    function updateOutstanding($id){ 
        
         $rs = $this->getDataRowById($id);
        
         try{  

            if(!$this->oDbCon->startTrans())
                throw new Exception($this->errorMsg[100]); 

            $arrObj = array();
            array_push($arrObj, new APPayment());
            //array_push($arrObj, new TruckingServiceOrderInvoice());
              
            //$sqlArr = array();
             
            $totalUsedAmount = $this->getUsedDPList($id);
            $totalUsedAmount = $totalUsedAmount['usedamount'];
             
            $totalDPUsedAmount = $this->getDPSettlementList($rs[0]['pkey']);
            $totalDPUsedAmount = $totalDPUsedAmount['usedamount'];
//          
            $totalAmount = $totalUsedAmount + $totalDPUsedAmount;
               
            $statuskey = ($totalAmount >= $rs[0]['amount']) ? 3 : 2; 
	  
            $sql  = 'update '.$this->tableName.' set outstanding = amount - ' . $totalAmount .' where statuskey in (2,3) and pkey = ' . $this->oDbCon->paramString($id) ;	 
            $this->oDbCon->execute($sql);  
		
            if($rs[0]['statuskey'] <> $statuskey)
                $this->changeStatus($id,$statuskey, '', false, true,true); 
              
            $this->oDbCon->endTrans();   

        } catch(Exception $e){
            $this->oDbCon->rollback();  
        }	
    }
    
    function getDPSettlementList($id){
            // cari DP yg disettle tanpa transaksi
         
            $arrObj = array();
            array_push($arrObj, $this->getPaymentObj());
              
            $sqlArr = array();
              
            $totalUsedAmount = 0; 
            foreach($arrObj as $obj){
                $sql = ' select 
                                '.$obj->tableName.'.code,
                                '.$obj->tableName.'.trdate,
                                '.$obj->tableNameDetail.'.downpaymentkey,
                                '.$obj->tableNameDetail.'.amount
                            from 
                                '.$obj->tableName.',
                                '.$obj->tableNameDetail.'
                            where 
                                '.$obj->tableName.'.statuskey in (2,3) and
                                '.$obj->tableName.'.pkey = '.$obj->tableNameDetail.'.refkey and
                                downpaymentkey = ' . $this->oDbCon->paramString($id); 
           
                array_push($sqlArr, $sql); 
            } 
        
            $sql = 'select * from ('.implode(' UNION ', $sqlArr).') downpayment';
            $rs = $this->oDbCon->doQuery($sql);
        
            foreach($rs as $row)
                $totalUsedAmount += $row['amount'];
            
            $arrReturn = array(); 
            $arrReturn['usedamount'] = $totalUsedAmount; 
            $arrReturn['history'] = $rs; 
            
            return $arrReturn;
    }
    
      
    function getDownpaymentForAP($apKey,$currencykey=''){
        $ap = new AP();
        $apPayment = new APPayment();
        $arrAvailableDP = array();
        
        if(!is_array($apKey))
            $apKey[0] = $apKey;
        
//        for($i=0;$i<count($apKey);$i++){
//            if(empty($apKey))
//        }
 
        
        $rsTemp =   $this->getTableKeyAndObj($apPayment->tableName); 
		$arrDP = array('refkey' => $apKey, 'reftabletype' => $rsTemp['key'], 'currencykey' => $currencykey);
			
        $rsDP =  $this->getAvailableDownpayment($arrDP);
        /*foreach($rsDP as $dpRow)  
           array_push($arrAvailableDP, $dpRow); 

        return $arrAvailableDP;*/
        
        return $rsDP;
        
    }
    
    function getAvailableDownpayment($apKey = array()){ 
        if(empty($apKey['refkey']))
         return array();        
         
         $criteria = ' and '.$this->tableName.'.statuskey = 2 ';
        
        if(!is_array($apKey['refkey']))
           $apKey[0] = $apKey['refkey'];
              
        $refkey = implode(',',$this->oDbCon->paramString($apKey['refkey']));
      
        $criteria .= ' and '.$this->tableName.'.reftabletype = ' . $this->oDbCon->paramString($apKey['reftabletype']); 
        if(!empty($refkey))
            $criteria .= ' and '.$this->tableName.'.refkey in('.$refkey.')';
		
		if(!empty($apKey['currencykey']))
            $criteria .= ' and '.$this->tableName.'.currencykey = '.$this->oDbCon->paramString($apKey['currencykey']).' ';
           
        $rs = $this->searchData('','',true, $criteria);

        return $rs;
    }
    
    
    function getUsedDPList($id){
         
            $arrObj = array();
            array_push($arrObj, new APPayment());
            array_push($arrObj, new APCommissionPayment());
            //array_push($arrObj, new TruckingServiceOrderInvoice());
              
             $sqlArr = array();
              
            $totalUsedAmount = 0; 
            foreach($arrObj as $obj){
                $sql = ' select 
                                '.$obj->tableName.'.code,
                                '.$obj->tableName.'.trdate,
                                '.$obj->tableDownpaymentDetail.'.downpaymentkey,
                                '.$obj->tableDownpaymentDetail.'.amount
                            from 
                                '.$obj->tableName.',
                                '.$obj->tableDownpaymentDetail.'
                            where 
                                '.$obj->tableName.'.statuskey in (2,3) and
                                '.$obj->tableName.'.pkey = '.$obj->tableDownpaymentDetail.'.refkey and
                                downpaymentkey = ' . $this->oDbCon->paramString($id); 
           
                array_push($sqlArr, $sql); 
            } 
        
            $sql = 'select * from ('.implode(' UNION ', $sqlArr).') downpayment';
            $rs = $this->oDbCon->doQuery($sql);
        
            foreach($rs as $row)
                $totalUsedAmount += $row['amount'];
            
            $arrReturn = array(); 
            $arrReturn['usedamount'] = $totalUsedAmount; 
            $arrReturn['history'] = $rs; 
            
            return $arrReturn;
    }
    
      
    function reCountGrandtotal($arrParam){
          
//        $isPriceIncludeTax =  $arrParam['chkIncludeTax']; 
//        $taxPercentage =  $this->unFormatNumber($arrParam['taxPercentage']);
//        $prepaidTax23Percentage =  $this->unFormatNumber($arrParam['prepaidTax23Percentage']);
        
//          $subtotal = $this->unFormatNumber($arrParam['amount']); 
        
/*      
        
        $beforeTaxTotal = $subtotal;
        
        if (!$isPriceIncludeTax){ 
            $taxValue = $subtotal * $taxPercentage / 100;
            $subtotal += $taxValue;
        } { 
            $taxValue = ($taxPercentage/(100 + $taxPercentage)) * $subtotal;   
            $beforeTaxTotal = $subtotal - $taxValue ;
        }
  
        $prepaidTax23 = $beforeTaxTotal * $prepaidTax23Percentage / 100; */
        
        //$payment = $beforeTaxTotal;
        
        //$payment = $beforeTaxTotal + $taxValue - $prepaidTax23;

        $totalPayment = 0;  
        $paymentDetail = $arrParam['paymentMethodValue'];
        for($i=0;$i<count($paymentDetail);$i++){
            $totalPayment += $this->unFormatNumber($paymentDetail[$i]);
        } 

        $reCountResult = array(); 
/*        $reCountResult['beforeTaxTotal'] = $beforeTaxTotal; 
        $reCountResult['taxValue'] = $taxValue;  
        $reCountResult['subtotal'] = $subtotal;
        $reCountResult['prepaidTax23'] = $prepaidTax23;  */
        $reCountResult['payment'] = $totalPayment;
          

        return $reCountResult;

    }
    
    function validateCancel($rsHeader,$autoChangeStatus=false){ 
        $rsDP = $this->getUsedDPList($rsHeader[0]['pkey']);   
        $rsDP = $rsDP['history'];
        if(!empty($rsDP))
            $this->addErrorLog( false, '<strong>'.$rsHeader[0]['code'].'</strong> ' .$this->errorMsg[201].'<br><strong>'.$rsDP[0]['code'].'</strong>, ' .$this->errorMsg[225] );
        
        //cek downpayment yang sudah di lakukan pelunasan
        $rsDPSettlement = $this->getDPSettlementList($rsHeader[0]['pkey']);   
        $rsDPSettlement = $rsDPSettlement['history'];
        
        if(!empty($rsDPSettlement)) 
            $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '. $this->errorMsg[201].'<br><strong>'.$rsDPSettlement[0]['code'].'</strong> ' .$this->errorMsg[225] );
      
      //cek ad AP terbayar
		$ap = new AP(); 
        $rsAPKey = $ap->getTableKeyAndObj($this->tableName,array('key'));  
		$rsAP = $ap->searchData('','',true,' and '.$ap->tableName.'.refkey = '.$this->oDbCon->paramString($rsHeader[0]['pkey']).' and '.$ap->tableName.'.reftabletype = '.$rsAPKey['key'].' and ('.$ap->tableName.'.statuskey in (2,3))');
		
		if(!empty($rsAP))     
			$this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' . $this->errorMsg[201].' ' .$this->errorMsg['ap'][2]);
    	    }  
    
  
     function afterStatusChanged($rsHeader){    
        // retrieve latest status
        $rsHeader = $this->getDataRowById($rsHeader[0]['pkey']);
         
        $cashAdvanceRealization = new CashAdvanceRealization();
          
        if($rsHeader[0]['statuskey'] == TRANSACTION_STATUS['batal'] && !empty($rsHeader[0]['refcashadvancedetailkey'])){ 
            // utk Cash Advance
            // kalo cancel, update transkey cash advance   
            $cashAdvanceRealization->removeTransactionLink(explode(',',$rsHeader[0]['refcashadvancedetailkey']),$rsHeader[0]['refcashadvancekey'],$rsHeader[0]['pkey']);
            
        }
        
    }
    
    function getPaymentObj(){
        return  new SupplierDownpaymentSettlement();
    }


    function afterAddDataOnCopy($pkey, $oldkey){ 
        
        // masalahnya add data baru dulu baru updatestatuschanged
        
        $rsHeader = $this->getDataRowById($pkey);
        if(!empty($rsHeader[0]['refcashadvancedetailkey'])){ 
            $cashAdvanceRealization = new CashAdvanceRealization(); 
            $cashAdvanceRealization->updateTransactionLink(explode(',',$rsHeader[0]['refcashadvancedetailkey']),$rsHeader[0]['pkey']);
        } 
    }
    
}
		
?>
