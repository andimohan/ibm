<?php

class APPayableTax23Payment extends APPayment{
	
function __construct(){
		
		parent::__construct();
		
		$this->tableName = 'ap_payable_23_payment_header';
		$this->tableNameDetail = 'ap_payable_23_payment_detail';
		$this->tableSupplier = 'supplier'; 
		$this->tablePayment= 'ap_payable_23_payment';
		$this->tableAP = 'ap_payable_23'; 
    
        $this->tableDownpaymentDetail = ''; // harusnya gk kepake karena struktur data sudah dioverwrite
        $this->tableFile = '';
    
    
		$this->uploadFileFolder = 'ap-payable-tax23-payment/';  
        $this->tableFile = 'ap_payable_tax23_file';

        $this->useStorage = $this->useStorage('S3');		  
        
        $this->tableNeedToBeCopyOnCancel = array($this->tableNameDetail);
        $this->apPayable23GL = ($this->loadSetting('apPayable23GL') == 1) ? true : false;
		$this->securityObject = 'APPayableTax23Payment';

        $arrPaymentDetail = array(); 
        $arrPaymentDetail['pkey'] = array('hidDetailPaymentKey');
        $arrPaymentDetail['refkey'] = array('pkey', 'ref');
        $arrPaymentDetail['amount'] = array('paymentMethodValue',array('datatype' => 'number','mandatory'=>true));
        $arrPaymentDetail['paymentkey'] = array('selPaymentMethod'); 
        $arrPaymentDetail['cashbankvoucherkey'] = array('selVoucher');
       
  
		// harus di overwrite  ualng agar keupdate paramternya
    
        $arrDetails = array();
        array_push($arrDetails, array('dataset' => $this->arrDataDetail));
                array_push($arrDetails, array('dataset' => $arrPaymentDetail, 'tableName' => $this->tablePayment));
        
       if($this->useStorage){ 
            
            $this->arrDataFileDetail = array();  
            $this->arrDataFileDetail['pkey'] = array('hidDetailFileKey');
            $this->arrDataFileDetail['refkey'] = array('pkey','ref');
            $this->arrDataFileDetail['file'] = array('fileDetail',array('datatype' => 'file','uploadFolder' => $this->uploadFileFolder));
            
            array_push($arrDetails, array('dataset' => $this->arrDataFileDetail, 'tableName' => $this->tableFile));
        }else{ 
            array_push($arrDetails, array('dataset' => $this->arrDataFile, 'tableName' => $this->tableFile, 
                                          'datatype' => 'file', 'uploadFolder' => $this->uploadFileFolder,
                                          'token' => 'token-item-file-uploader', 'fileName' => 'item-file-uploader')); 
        }
          
     	
		// harus di overwrite  ualng agar keupdate paramternya 
        $this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails));
	
	
	
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'date','title' => 'date','dbfield' => 'trdate','default'=>true, 'width' => 100, 'align' =>'center', 'format' => 'date'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'supplier','title' => 'supplier','dbfield' => 'suppliername','default'=>true, 'width' => 200 ));
        array_push($this->arrDataListAvailableColumn, array('code' => 'total','title' => 'total','dbfield' => 'grandtotal','default'=>true, 'width' => 100, 'align' => 'right', 'format' => 'number'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
        array_push($this->arrDataListAvailableColumn, array('code' => 'warehouse','title' => 'warehouse','dbfield' => 'warehousename',  'width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'description','title' => 'note','dbfield' => 'trnotes',  'width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'refCode','title' => 'refCode','dbfield' => 'refcode', 'width' => 150));    
       
    
        $this->includeClassDependencies(array( 
            'AP.class.php',
            'APPayment.class.php',
            'APPayableTax23.class.php',
            'TruckingServiceOrder.class.php',
            'SalesOrder.class.php',
            'Warehouse.class.php',
            'PaymentMethod.class.php',
            'Supplier.class.php',
            'GeneralJournal.class.php',
            'Tax.class.php',
            'CashBank.class.php',
            'COALink.class.php',
            'ChartOfAccount.class.php'
        ));

    
//        $printTransactionFunction = $class->generatePrintContextMenu('print','printARPayment');  
//        $overwriteContextMenu["printSeparator"] = "-";
//        $overwriteContextMenu["print"] = array("name" => $obj->lang['printTransaction'],"icon" =>"print","callbackFunction" => $printTransactionFunction); 
        $this->printMenu = array();       
        array_push($this->printMenu,array('code' => 'printTransaction', 'name' => $this->lang['printTransaction'],  'icon' => 'print', 'url' => 'print/apPayableTax23Payment'));

        $this->overwriteConfig();
	}
	  
    function validateForm($arr,$pkey = ''){
      
	   // nanti dicek lg
        // perlu cek settingan boleh cross supplier tdk    
        return $arrayToJs;
	 }
    
    function validateConfirm($rsHeader){
		$id = $rsHeader[0]['pkey']; 
        $supplierkey =  $rsHeader[0]['supplierkey'];
		$currencykey =  $rsHeader[0]['currencykey'];
        $rate = ($rsHeader[0]['rate'] > 0) ? $rsHeader[0]['rate'] : 1;

        $APObj = $this->getAPObj();  
        $warehouse = new Warehouse();  

        if($this->apPayable23GL){
            $coaLink = new COALink();

            $rsPayment = (ADV_FINANCE && TEST_VOUCHER) ?  $this->getPaymentVoucherDetail($id,'',2) : $this->getPaymentMethodDetail($id);  

            $totalPayment = 0; 
            for($i=0;$i<count($rsPayment); $i++) {
                $totalPayment += $rsPayment[$i]['amount'];
            }

            $balance = $totalPayment - $rsHeader[0]['grandtotal']; 
            $balance *= $rate;

            $thresholdDiscount = abs($this->loadSetting('roundedPaymentThreshold'));
            if($balance < ($thresholdDiscount * -1)) {
                $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg[502]);
            } else if ($balance > $thresholdDiscount){
                $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg[509]); 
            }


            if (USE_GL){  
                $arrCOA = array();
                array_push($arrCOA, 'ap' , 'otherrevenue', 'othercost','payabletax23','supplierdownpayment'); 
                // kalo ad lebih dr 1 currency
                $currency = new Currency();
                $rsCurrency = $currency->searchData($currency->tableName.'.statuskey',1);
                if (!count($rsCurrency) > 1)
                    array_push($arrCOA, 'lossprofitrate');

                for ($i=0;$i<count($arrCOA);$i++){
                    $rsCOA = $coaLink->getCOALink ($arrCOA[$i], $warehouse->tableName,$rsHeader[0]['warehousekey'], 0); 
                    if (empty($rsCOA))	
                        $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$arrCOA[$i]. ' ' .$this->errorMsg['coa'][3]);
                }    


                if (ADV_FINANCE && TEST_VOUCHER){
                    for($i=0;$i<count($rsPayment); $i++){ 
                        // cek kalo supplierkey sudah beda
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
        

        $rsDetail = $this->getDetailById($id);
        $arrKeys = array_column($rsDetail,'apkey');
  
        if (!empty($arrKeys)){
            $arrKeys = implode(',',$arrKeys);
            $rsAP = $APObj->searchData('','',true,' and ' .$APObj->tableName.'.pkey in ('.$arrKeys.') and ' .$APObj->tableName.'.statuskey in (3,4) ' );
            if (!empty($rsAP)){
                $arrAP = array_column($rsAP,'code');
                $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg[201].'<br>'.implode(', ',$arrAP).'. '.$this->errorMsg['apTax23'][6]); 
            }
        }
 
        
	 }
    
    function deleteAPPrepaidTax($id){   
    }
	 

    function updateGL($rs, $rsPayment){

        if (!USE_GL) return;
        
        if(!$this->apPayable23GL) return;

        // ap prepaid gk perlu jurnal, jurnalnya nanti manual adjustment pas akhir tahun
        $generalJournal = new GeneralJournal(); 
        $tax = new Tax();
        $ap = new APPayableTax23();
        $supplier = new Supplier();
        $cashBank = new CashBank();
        $chartOfAccount = new ChartOfAccount();
        $warehouse = new Warehouse();
        $coaLink = new COALink();

        $warehousekey = $rs[0]['warehousekey'];
        $paymentCurrencyKey = $rs[0]['currencykey'];

        $rsKey = $generalJournal->getTableKeyAndObj($this->tableName); 
		$arr = array();
		$arr['pkey'] = $generalJournal->getNextKey($generalJournal->tableName);
		$arr['code'] = 'xxxxx';
		$arr['refkey'] = $rs[0]['pkey'];
		$arr['refTableType'] = $rsKey['key'];
		$arr['trDate'] =   $this->formatDBDate($rs[0]['trdate'],'d / m / Y'); 
		$arr['createdBy'] = 0; 
        $arr['selWarehouseKey'] = $rs[0]['warehousekey'];
        $arr['trDesc'] = $rs[0]['trnotes'];


        $rsDetail = $this->getDetailById($rs[0]['pkey']);
        $arrAPKey  = array_column($rsDetail, 'apkey');

        $rsAP = $ap->searchData('','',true,' and ' . $ap->tableName.'.pkey in ('.$this->oDbCon->paramString($arrAPKey,',').') ');
        
        $arrPPHType = array_column($rsAP, 'pphtype', 'pkey');
        $arrPPhTypeKey = array_values(array_unique($arrPPHType));

        $rsCOA = $tax->getPPhCOA($arrPPhTypeKey, $rs[0]['warehousekey'],false);
        $rsCOACol = $this->reindexDetailCollections($rsCOA, 'pkey');


        $rate = (!empty($rs[0]['rate'])) ? $rs[0]['rate'] : 1;

        $arrGroupedDebit = [];
        //$arrGroupedCredit = [];
        foreach ($rsDetail as $rowDetail) {

            $pphType = $arrPPHType[$rowDetail['apkey']];
            $COAKey  = $rsCOACol[$pphType][0]['coakey'];
            //$paidCOAKey = $rsCOACol[$pphType][0]['paidcoakey'];
            if (!$COAKey) continue;
            
            $amount = ($rowDetail['amount'] * $rate);
            
            if (!isset($arrGroupedDebit[$COAKey])) {
                $arrGroupedDebit[$COAKey] = 0;
            }

            $arrGroupedDebit[$COAKey] += $amount;
        }

        // desc
        $desc = array(); 
        $rsSupplier = $supplier->getDataRowById($rs[0]['supplierkey']);
        array_push($desc,html_entity_decode($rsSupplier[0]['name'])); 
        if(!empty($rs[0]['trnotes'])) array_push($desc,$rs[0]['trnotes']);
		$arr['trDesc'] = implode(chr(13),$desc);  

        $multiCurrency = ($rs[0]['currencykey'] != CURRENCY['idr']) ? true : false;

        $totalPaymentAmount = 0;
        $totalAP =0;

        $temp = -1;

        foreach ($arrGroupedDebit as $COAKey => $amount) {
            $temp++;
            $arr['hidCOAKey'][$temp] = $COAKey;
            $arr['debit'][$temp]    = $amount;
            $arr['credit'][$temp]   = 0;
            $arr['selCurrencyKey'][$temp] = $rate; 
            $arr['debitSource'][$temp] = 0; 
            $arr['creditSource'][$temp] = $amount; 
            $arr['rate'][$temp] = $rate ; 
            $arr['refCashBankKey'][$temp] = 0; 

            $totalAP += $amount;
        }

        // foreach ($arrGroupedCredit as $COAKey => $amount) {
        //     $temp++;
        //     $arr['hidCOAKey'][$temp] = $COAKey;
        //     $arr['debit'][$temp]    = 0;
        //     $arr['credit'][$temp]   = $amount;
        // }

		if(ADV_FINANCE && TEST_VOUCHER) 
			$rsPayment = $this->getPaymentVoucherDetail($rs[0]['pkey'],'',2);

        for($i=0;$i<count($rsPayment); $i++){ 
            
            if(ADV_FINANCE && TEST_VOUCHER){ 
				$rsCashBank = $cashBank->getDataRowById($rsPayment[$i]['cashbankvoucherkey']);
				$rsCOA = $chartOfAccount->getDataRowById($rsCashBank[0]['coakey']);
                
				$paymentcoakey = $rsCOA[0]['countercoakey'];
                $paymentRate = $rsCashBank[0]['rate'];  
			}else{
				$rsCOA = $coaLink->getCOALink ('payment', $warehouse->tableName,$warehousekey,$rsPayment[$i]['paymentkey']); 
				$paymentcoakey = $rsCOA[0]['coakey'];
                $paymentRate = $rs[0]['rate']; 
			}
            
			 $paymentAmount = $rsPayment[$i]['amount'] * $paymentRate; 
             
             $temp++;
             $arr['hidCOAKey'][$temp] = $paymentcoakey;
             $arr['debit'][$temp] = 0; 
             $arr['credit'][$temp] = $paymentAmount; 
             $arr['selCurrencyKey'][$temp] = $paymentCurrencyKey ; 
             $arr['debitSource'][$temp] = 0; 
             $arr['creditSource'][$temp] = $rsPayment[$i]['amount']; 
             $arr['rate'][$temp] = $paymentRate ; 
             $arr['refCashBankKey'][$temp] = $rsPayment[$i]['cashBankKey'];  
			 $totalPaymentAmount += $paymentAmount;
        }        


        //selisih pembayaran   
        $temp++; 
        if ($rs[0]['balance'] < 0){ 
            $rsCOA = $coaLink->getCOALink ('otherrevenue', $warehouse->tableName,$warehousekey, 0); 
            $arr['debit'][$temp] = 0; 
            $arr['credit'][$temp] = abs($rs[0]['balance'])  * $rate; 
            $arr['debitSource'][$temp] = 0; 
            $arr['creditSource'][$temp] =   abs($rs[0]['balance']) ; 
            $arr['refCashBankKey'][$temp] = '';  
        }else{ 
            $rsCOA = $coaLink->getCOALink ('othercost', $warehouse->tableName,$warehousekey, 0); 
            $arr['debit'][$temp] = abs($rs[0]['balance'])  * $rate; 
            $arr['credit'][$temp] = 0; 
            $arr['debitSource'][$temp] = abs($rs[0]['balance']); 
            $arr['creditSource'][$temp] =   0 ; 
            $arr['refCashBankKey'][$temp] = '';  
        }

        $arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];
        $arr['selCurrencyKey'][$temp] = $paymentCurrencyKey ; 
        $arr['rate'][$temp] = $rate ; 

        $totalDifference = $totalPaymentAmount - $totalAP;

        if($multiCurrency){
			
			$balance = $rs[0]['balance'] * $rate;
			
			$totalDifference -= $balance;  
			
			if($totalDifference <> 0){ 
             $rsCOA = $coaLink->getCOALink ('lossprofitrate', $warehouse->tableName,$warehousekey, 0); 
             $temp++;
             $arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];     
             $arr['debit'][$temp] = $totalDifference; 
             $arr['credit'][$temp] = 0;
             $arr['selCurrencyKey'][$temp] = CURRENCY['idr'] ; 
             $arr['debitSource'][$temp] = $totalDifference; 
             $arr['creditSource'][$temp] =  0; 
             $arr['rate'][$temp] = 1 ;
             $arr['refCashBankKey'][$temp] = '';  
			}
        }



        $arrayToJs = $generalJournal->addData($arr);

        if (!$arrayToJs[0]['valid'])
            throw new Exception('<strong>'.$rs[0]['code'] . '</strong>. '.$this->errorMsg[504].' '.$arrayToJs[0]['message']); 

    }

    function updateNTPN($pkey, $ntpn){
        
        try{ 
            
		 	if(!$this->oDbCon->startTrans())
				throw new Exception($this->errorMsg[100]);
            
            $sql = 'update  '.$this->tableName.' set  ntpn = '.$this->oDbCon->paramString($ntpn).' where '.$this->tableName.'.pkey = '.$this->oDbCon->paramString($pkey);
            $this->oDbCon->execute($sql); 
             
            $this->oDbCon->endTrans();
					   
		} catch(Exception $e){
			$this->oDbCon->rollback();
			$this->addErrorList($arrayToJs,false, $e->getMessage());   
		}
        
        
    }   
	
	 
	
    function getAPObj(){
        return  new APPayableTax23();
    }
    
     function afterStatusChanged($rsHeader){ 
        
		$APObj = $this->getAPObj();
         
        $rsDetail = $this->getDetailById($rsHeader[0]['pkey']); 
        for($i=0;$i<count($rsDetail); $i++){   
           $APObj->updateAPOutstanding($rsDetail[$i]['apkey']); 
        }   
            
     }
    
    function cancelTrans($rsHeader,$copy){ 

        $id = $rsHeader[0]['pkey']; 

        $rsAPKey = $this->getTableKeyAndObj($this->tableName,array('key')); 
        
		if( $this->isActiveModule('CashBank') ){
			$cashBank = new CashBank();
			if (ADV_FINANCE && TEST_VOUCHER){ 
				$cashBank->removeTransaction($id,$rsAPKey['key']);
			}else{ 
				$cashBank->cancelCashBank($rsHeader,$this->tableName);
			}
		}

        
		if ($copy)
			$this->copyDataOnCancel($id);	  

        $this->cancelGLByRefkey($rsHeader[0]['pkey'],$this->tableName);
		   
	}
	    
    function afterAddDataOnCopy($pkey, $oldkey){  
      
    }
    
}

?>
