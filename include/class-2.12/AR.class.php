<?php
class AR extends BaseClass{
  
   function __construct(){
		
		parent::__construct();
		
		$this->tableName = 'ar';   
		$this->tablePaymentHeader = 'ar_payment_header';  
		$this->tablePaymentDetail = 'ar_payment_detail'; 
        $this->tableARPrepaid = 'ar_prepaid_23'; 
		$this->tableStatus = 'ar_status';
		$this->tableCustomer = 'customer'; 
		$this->tableEMKLOrderInvoice = 'emkl_order_invoice_header'; 
        $this->tableType = 'ar_type'; 
        $this->tableWarehouse = 'warehouse';
        $this->tableCurrency = 'currency';
        $this->tablePayment = 'ar_voucher_detail';
        $this->tableCashBank = 'cash_bank';
		$this->securityObject = 'AR'; 
        $this->isTransaction = true;
       
        $this->arrData = array(); 
        $this->arrData['pkey'] = array('pkey');
        $this->arrData['code'] = array('code');
        $this->arrData['trdate'] = array('trDate','date');
        $this->arrData['agingdate'] = array('trAgingDate','date');
        $this->arrData['customerkey'] = array('hidCustomerKey');
        $this->arrData['saleskey'] = array('hidSalesKey');
        $this->arrData['refheaderkey'] = array('hidRefHeaderKey');
        $this->arrData['warehousekey'] = array('selWarehouse');
        $this->arrData['refkey'] = array('hidRefKey');
        $this->arrData['refcode'] = array('hidRefCode');
        $this->arrData['refcode2'] = array('hidRefCode2');
        $this->arrData['refdate'] = array('hidRefDate','date');
        $this->arrData['reftabletype'] = array('hidRefTable');
        $this->arrData['amount'] = array('amount','number');
        $this->arrData['outstanding'] = array('amount','number');
        $this->arrData['trdesc'] = array('trDesc');
        $this->arrData['statuskey'] = array('selStatus');
        $this->arrData['duedate'] = array('dueDate','date');
        $this->arrData['duedays'] = array('dueDays','number');
        $this->arrData['artype'] = array('selARType');
        $this->arrData['islinked'] = array('islinked');
        $this->arrData['overwriteGL'] = array('overwriteGL'); 
        $this->arrData['paymentmethodkey'] = array('selPaymentMethod'); 
        $this->arrData['currencykey'] = array('selCurrency'); 
        $this->arrData['rate'] = array('currencyRate','number'); 
        $this->arrData['amountidr'] = array('amountIDR','number');
        $this->arrData['refcustomerkey'] = array('hidRefCustomerKey'); 
        $this->arrData['refcashoutkey'] = array('hidRefCashOutKey'); 
        $this->arrData['refwokey'] = array('hidRefWOKey'); 
        $this->arrData['refsokey'] = array('hidRefSOKey'); 
        $this->arrData['tax23value'] = array('tax23value');  
        $this->arrData['tax23outstanding'] = array('tax23outstanding');  
        $this->arrData['vanumber'] = array('vaNumber');  
        $this->arrData['totalvoucher'] = array('totalPayment', 'number'); 
        $this->arrData['pphtype'] = array('selPPhType');  
        $this->arrData['salesordercodecache'] = array('salesordercodecache');  
       
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'date','title' => 'date','dbfield' => 'trdate','default'=>true, 'width' => 100, 'align' =>'center', 'format' => 'date'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'duedate','title' => 'duedate','dbfield' => 'duedate','default'=>true, 'width' => 100, 'align' =>'center', 'format' => 'date'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'refCode','title' => 'reference','dbfield' => 'refcode','default'=>true, 'width' => 120 ));
        array_push($this->arrDataListAvailableColumn, array('code' => 'customer','title' => 'customer','dbfield' => 'customername','default'=>true, 'width' => 200 ));
        array_push($this->arrDataListAvailableColumn, array('code' => 'currencyShort','title' => 'currencyShort','dbfield' => 'currencyname', 'width' => 60, 'align' =>'center' ));
        array_push($this->arrDataListAvailableColumn, array('code' => 'rate','title' => 'currencyRate','dbfield' => 'rate', 'width' => 80, 'align' =>'right' , 'format' => 'number'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'amount','title' => 'amount','dbfield' => 'amount','default'=>true, 'width' => 100, 'align' => 'right', 'format' => 'number'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'outstanding','title' => 'outstanding','dbfield' => 'outstanding','default'=>true, 'width' => 100, 'align' => 'right', 'format' => 'number'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
        array_push($this->arrDataListAvailableColumn, array('code' => 'warehouse','title' => 'warehouse','dbfield' => 'warehousename',  'width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'description','title' => 'note','dbfield' => 'trdesc',  'width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'refCode2','title' => 'reference','dbfield' => 'refcode2',  'width' => 120 ));
        array_push($this->arrDataListAvailableColumn, array('code' => 'artype','title' => 'transactionType','dbfield' => 'artypename',  'width' => 120 ));
       
        array_push($this->filterCriteria, array('title' => $this->lang['warehouse'], 'field' => 'warehousekey'));
       
        $this->printMenu = array();
        array_push($this->printMenu,array('code' => 'printTransaction', 'name' => $this->lang['printTransaction'],  'icon' => 'print', 'url' => 'print/ar'));

	    $this->rounding =  1;
	    $this->activeModule = $this->isActiveModule(array('SalesOrder'));
	   
        $this->includeClassDependencies(array( 
                  'Currency.class.php', 
                  'Customer.class.php',  
                  'ARPayment.class.php',  
                  'Warehouse.class.php',
                  'EMKLOrderInvoice.class.php',
                  'EMKLJobOrder.class.php',
                  'Port.class.php',
                  'TruckingServiceOrderInvoice.class.php',
                  'COALink.class.php',
                  'GeneralJournal.class.php',
                  'CreditNote.class.php', 
                  'SalesOrder.class.php',
                   'CashBank.class.php'
         ));  
       
        $this->overwriteConfig();
	}
		
    function getQuery(){
	   
		$sql = '
				select
					'.$this->tableName. '.*,
                    ('.$this->tableName. '.amountidr - '.$this->tableName. '.tax23outstanding) as tax23balance,
                    if('.$this->tableName. '.statuskey = 1 or '.$this->tableName. '.statuskey = 2, datediff(now(),duedate) , 0)  as datediff,
					'.$this->tableCustomer.'.name as customername,
					'.$this->tableStatus.'.status as statusname,
					'.$this->tableWarehouse.'.name as warehousename , 
					'.$this->tableCurrency.'.name as currencyname,
					'.$this->tableEmployee.'.name as salesname , 
                    '.$this->tableType .'.name as artypename
				from 
					'.$this->tableName . '
                        left join ' . $this->tableCurrency .' on  '.$this->tableName.'.currencykey = ' . $this->tableCurrency .'.pkey 
                        left join ' . $this->tableEmployee .' on  '.$this->tableName.'.saleskey = ' . $this->tableEmployee .'.pkey 
                        left join ' .  $this->tableType .' on  '.$this->tableName.'.artype = ' . $this->tableType .'.pkey,
                    '.$this->tableStatus.' ,
                    '.$this->tableCustomer.' ,
                    '.$this->tableWarehouse.' 
				where  		
					'.$this->tableName . '.statuskey = '.$this->tableStatus.'.pkey and 
					'.$this->tableName . '.warehousekey = '.$this->tableWarehouse.'.pkey and 
					'.$this->tableName.'.customerkey = '.$this->tableCustomer.'.pkey
		' .$this->criteria ; 
        
        $sql .=  $this->getWarehouseCriteria() ;
        $sql .=  $this->getCustomerCriteria() ;
          
        return $sql;
	}
	
    function afterDuplicateData($rsHeader){
        $arrParam = array();
        $arrParam['pkey'] = $rsHeader[0]['pkey'];
        $arrParam['oldRs'] = '';  
 
        $this->afterUpdateData($arrParam,INSERT_DATA);   
    }
    
    function afterUpdateData($arrParam, $action){ 
		
        $generalJournal = new GeneralJournal(); 
        
        $customerObj = $this->getCustomerObj();
    
        //$rsKey = $generalJournal->getTableKeyAndObj($this->tableName);
        $rs = $this->getDataRowById($arrParam['pkey']);
        $oldRs = $arrParam['oldRs']; 
        
        
        $id = $arrParam['pkey'];
       
        $arr1 =array();
        array_push($arr1,$rs[0]['artype']); 
        array_push($arr1,$rs[0]['warehousekey']); 
        array_push($arr1,$rs[0]['customerkey']); 
        array_push($arr1,$rs[0]['amount']); 
        array_push($arr1,$rs[0]['trdate']); 
        
        // cek pake data yg dikirim, gk bisa pake modul karena gk semua AR pake voucher
        $useVoucher = (isset($arrParam['selVoucher'])) ? : false;
            
        if ($useVoucher)  { 
            // cek dan gabungin semua voucher detail dan amountnya
            $rsPayment = $this->getPaymentVoucherDetail($id);
            
            $voucherDetail = '';
            foreach($rsPayment as $row) 
                $voucherDetail .= $row['cashbankvoucherkey'].'*'.$row['amount'].'*';
            
            array_push($arr1,$voucherDetail); 
            
        }else{
            array_push($arr1,$rs[0]['paymentmethodkey']);
        }
        
        array_push($arr1,$rs[0]['rate']); 
        $arr1 = md5(json_encode($arr1));
         
        $arr2 = array();
        if(!empty($oldRs)){ 
            array_push($arr2,$oldRs[0]['artype']); 
            array_push($arr2,$oldRs[0]['warehousekey']); 
            array_push($arr2,$oldRs[0]['customerkey']); 
            array_push($arr2,$oldRs[0]['amount']); 
            array_push($arr2,$oldRs[0]['trdate']); 
            
            if ($useVoucher)  { 
                // cek dan gabungin semua voucher detail dan amountnya
                $rsPayment = $this->getPaymentVoucherDetail($oldRs[0]['pkey']);

                $voucherDetail = '';
                foreach($rsPayment as $row) 
                    $voucherDetail .= $row['cashbankvoucherkey'].'*'.$row['amount'].'*';

                array_push($arr2,$voucherDetail); 

            }else{
                array_push($arr2,$oldRs[0]['paymentmethodkey']); 
            }


            array_push($arr2,$oldRs[0]['rate']); 
        }
        $arr2 = md5(json_encode($arr2));
        
        $same = ($arr1 == $arr2) ? true : false;
	               
        // kalo blm ad jurnal, add
        if (empty($oldRs)){  
            
            if ($useVoucher) {
                $rsPayment = $this->getPaymentVoucherDetail($id); 
                $this->addVoucherTransaction($rs, $rsPayment);
            }

            
            $this->updateGL($rs);
        }else{ 
            if (!$same){
                
                if ($useVoucher) {
//                    $this->updateVoucherTransaction($rsHeader);
                    // langsung cancel aj terus add ulang
                    $cashBank = new CashBank();
                    
                    $rsPayment = $this->getPaymentVoucherDetail($id);  
                    
                    $rsARKey = $this->getTableKeyAndObj($this->tableName, array('key'));
                     
                    $cashBank->removeTransaction($id, $rsARKey['key']);
                    $this->addVoucherTransaction($rs, $rsPayment);
                }

                //kalo ud ad cek perlu add ulang atau tidak
                $this->cancelGLByRefkey($arrParam['pkey'],$this->tableName);
                $customerObj->updateAROutstanding($oldRs[0]['customerkey']);
                
                $this->updateGL($rs);
            } 
        }    
            
        $customerObj->updateAROutstanding($rs[0]['customerkey']);
}
    
//    function updateVoucherTransaction($rsHeader)
//    {
//        $cashBank = new CashBank();
//
//        $id = $rsHeader[0]['pkey'];
//
//        $rsARKey = $this->getTableKeyAndObj($this->tableName, array('key'));
//        $rsPayment = $this->getPaymentVoucherDetail($id);
//        $rsTransaction = $cashBank->getTransaction($id, $rsARKey['key']);
//
//        //jika cash bank transaction tidak kosong dan
//        //ar employee voucher tidak kosong 
//        //maka cek, ada perubahan voucher tidak dari ar employee
//        if (!empty($rsTransaction) && !empty($rsPayment)) {
//            $this->updateOrRemoveVoucherTransaction($rsHeader, $rsTransaction, $rsPayment);
//            //ar payment tidak ada voucher,
//            //maka remove all voucher yang ada di transasction
//        } else if (!empty($rsTransaction) && empty($rsPayment)) {
//            $cashBank->removeTransaction($id, $rsARKey['key']);
//        } else if (empty($rsTransaction) && !empty($rsPayment)) {
//            //jika belum ada di transaction maka insert transasction
//            //tapi ada voucher di ar employee
//            $this->addVoucherTransaction($rsHeader, $rsPayment);
//        }
//    }

    function addVoucherTransaction($rsHeader, $rsPayment)
    {
        $cashBank = new CashBank();
        $rsARKey = $this->getTableKeyAndObj($this->tableName, array('key'));
        foreach ($rsPayment as $voucherlist) {            
            // $refkey = $voucherlist['cashbankvoucherkey'];
            $cashBank->insertTransaction(
                array(
                    'refkey' => $voucherlist['cashbankvoucherkey'],
                    'reftablekey' => $rsARKey['key'],
                    'reftranskey' => $rsHeader[0]['pkey'],
                    'refcode' => $rsHeader[0]['code'],
                    'refdate' => $rsHeader[0]['trdate'],
                    'amount' => $voucherlist['amount'],
                )
            );
        }

    }

    function updateOrRemoveVoucherTransaction($rsHeader, $rsTransaction, $rsPayment) 
    {

        $cashBank = new CashBank();

        $id = $rsHeader[0]['pkey'];
        $rsARKey = $this->getTableKeyAndObj($this->tableName, array('key'));

        $rsPaymentCols = $this->reindexDetailCollections($rsPayment, 'cashbankvoucherkey');

        $unAvailableVoucherKey = [];
        foreach ($rsTransaction as $transaction) {
            $refkey = $transaction['refkey'];
            //cek apakah ada di ar payment 
            if (isset($rsPaymentCols[$refkey])) {
                $paymentCol = $rsPaymentCols[$refkey];
        
                 //cek apakah amount voucher berubah atau voucher telah di ganti

                if (($transaction['amount'] <> $paymentCol[0]['amount']) || ($transaction['refkey'] <> $paymentCol[0]['cashbankvoucherkey'])) {

                    $cashBank->removeTransaction($id, $rsARKey['key'], $transaction['refkey']);

                    //add voucher yang di rubah
                    $cashBank->insertTransaction(
                        array(
                            'refkey' => $paymentCol[0]['cashbankvoucherkey'],
                            'reftablekey' => $rsARKey['key'],
                            'reftranskey' => $rsHeader[0]['pkey'],
                            'refcode' => $rsHeader[0]['code'],
                            'refdate' => $rsHeader[0]['trdate'],
                            'amount' => $paymentCol[0]['amount'],
                        )
                    );
                } 
                
                $unAvailableVoucherKey[] = $refkey;
            } else {
                //jika tidak ada voucher payment maka removed 
                $cashBank->removeTransaction($id, $rsARKey['key'], $transaction['refkey']);
            }

            //add available voucher to transaction
            foreach ($rsPayment as $voucherlist) {
                $refkey = $voucherlist['cashbankvoucherkey'];
                        
                if (!in_array($refkey, $unAvailableVoucherKey)) {
                    $cashBank->insertTransaction([
                        'refkey' => $voucherlist['cashbankvoucherkey'],
                        'reftablekey' => $rsARKey['key'],
                        'reftranskey' => $rsHeader[0]['pkey'],
                        'refcode' => $rsHeader[0]['code'],
                        'refdate' => $rsHeader[0]['trdate'],
                        'amount' => $voucherlist['amount'],
                    ]);
                }
            }

        }

    }
    
		
    function afterAddDataOnCopy($pkey, $oldkey){   
//        $rs = $this->getDataRowById($pkey);   
//        $customerObj = $this->getCustomerObj();
//        $customerObj->updateAROutstanding($rs[0]['customerkey']);
		
		$rsHeader = $this->getDataRowById($pkey);
        $arrParam = array();
        $arrParam['pkey'] = $rsHeader[0]['pkey'];
        $arrParam['oldRs'] = '';  
 
        $this->afterUpdateData($arrParam,INSERT_DATA);  
		
    }
    
    function updateGL($rs){
			
        if (!USE_GL) return;
         
        if ($rs[0]['overwriteGL'] == 1) return;
         
        $this->cancelGLByRefkey($rs[0]['pkey'],$this->tableName);
         
        $coaLink = new COALink(); 
        $warehouse = new Warehouse();  
        $generalJournal = new GeneralJournal();
        $customerObj = $this->getCustomerObj();
		 
        $warehousekey = $rs[0]['warehousekey']; 

        $rsKey = $generalJournal->getTableKeyAndObj($this->tableName,array('key'));
		$arr = array();
		$arr['pkey'] = $generalJournal->getNextKey($generalJournal->tableName);
		$arr['code'] = 'xxxxx';
		$arr['refkey'] = $rs[0]['pkey'];
		$arr['refTableType'] = $rsKey['key'];
		$arr['trDate'] =  $this->formatDBDate($rs[0]['trdate'],'d / m / Y'); 
		$arr['createdBy'] = 0;
		$arr['refCode'] = $rs[0]['code'];
		$arr['selWarehouseKey'] = $rs[0]['warehousekey'];
        
		$temp = -1; 
		  
        //akun piutang  
        $temp++; 
        $arr['hidCOAKey'][$temp] = $customerObj->getARCOAKey($rs[0]['customerkey'],$warehousekey);
        $arr['debit'][$temp] = $rs[0]['amountidr']; 
        $arr['credit'][$temp] = 0;  
        
		$arr['debitSource'][$temp] = $rs[0]['amount'];  
		$arr['creditSource'][$temp] = 0 ; 
		$arr['selCurrencyKey'][$temp] = $rs[0]['currencykey']; 
		$arr['rate'][$temp] = $rs[0]['rate']; 
        switch ($rs[0]['artype']){ 
           
            // Service / Trucking  
			// Reimburse sementara disamakan, nanti baru tambah opsi kalo pake pengecualian baru narik dari AR Reimburse
            case AR_TYPE['reimburse'] :
            case AR_TYPE['serviceOrder'] :
                    $rsCOA = $coaLink->getCOALink ('salesservice', $warehouse->tableName, $warehousekey);    
                    break;
               
            // sales  
            default :
                    $rsCOA = $coaLink->getCOALink ('salesretail', $warehouse->tableName, $warehousekey);   
                    break;
          
        }
 	
        $temp++;
		$arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];
		$arr['debit'][$temp] = 0; 
		$arr['credit'][$temp] = $rs[0]['amountidr']; 

		$arr['debitSource'][$temp] = 0;  
		$arr['creditSource'][$temp] =  $rs[0]['amount']; 
		$arr['selCurrencyKey'][$temp] = $rs[0]['currencykey']; 
		$arr['rate'][$temp] = $rs[0]['rate']; 
        
		$arrayToJs = $generalJournal->addData($arr);
        
        if (!$arrayToJs[0]['valid'])
                throw new Exception('<strong>'.$rs[0]['code'] . '</strong>. '.$this->errorMsg[504].' '.$arrayToJs[0]['message']);    
 
    }
    
	function validateForm($arr,$pkey = ''){
        $security = new Security();		  
        
		$arrayToJs = parent::validateForm($arr,$pkey); 
		 
		$customerkey = $arr['hidCustomerKey']; 
		$amount = $this->unFormatNumber($arr['amount']);
		 
        //validasi kalo status gk menunggu gk bisa edit 
		if (!empty($pkey)){
			$rs = $this->getDataRowById($pkey);
			if ($rs[0]['statuskey'] <> 1){
				$this->addErrorList($arrayToJs,false,$this->errorMsg[212]);
			}
		}  
        
		if(empty($customerkey)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['customer'][1]);
		}
		if (!is_numeric($amount) || $amount == 0){  // positif negative sdh di normalize
			$this->addErrorList($arrayToJs,false,$this->errorMsg['amount'][1]);
		}
 
        
        $customerObj = $this->getCustomerObj();
        $hasCreditLimitAccess = $security->isAdminLogin($customerObj->creditLimitSecurityObject,10);  
        
        if (!$hasCreditLimitAccess && $customerObj->willExceedCreditLimit($customerkey,$amount)){
            $this->addErrorList($arrayToJs,false,$this->errorMsg['creditlimit'][1]);
        }
        
		return $arrayToJs;
	 } 
		 
	 
	 function searchDataForAutoComplete($fieldname='',$searchkey='',$mustmatch=false,$searchCriteria='',$orderCriteria='', $limit=''){
		
         $sql = 'select
					'.$this->tableName. '.pkey, 
                    concat('.$this->tableName.'.code ,  IFNULL(concat(\'-\','.$this->tableName. '.refcode), \'\') ) as value , 
                    '.$this->tableName. '.code as code , 
                    '.$this->tableName.'.refcode,
                    '.$this->tableName.'.refcode2,
                    '.$this->tableName.'.refkey,
                    '.$this->tableName.'.refdate, 
                    '.$this->tableName. '.amount,  
                    '.$this->tableName. '.tax23value,  
                    '.$this->tableName. '.outstanding
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
		
	 
    function changeStatus($id,$status,$reason='',$copy=false,$autoChangeStatus=false, $dontValidate = false){
		
		$arrayToJs = array();
		  
		  try{ 
                if(!$dontValidate){
                   switch ($status){
                               case 1 : $arrayToJs = $this->validateOpen($id);
                                         if (!empty($arrayToJs)) 
                                                return $arrayToJs;  
                                          break; 
                                case 2 : $arrayToJs = $this->validatePartial($id);
                                         if (!empty($arrayToJs)) 
                                                return $arrayToJs;  
                                          break; 
                                 case 3 : $arrayToJs = $this->validateClosed($id);
                                             if (!empty($arrayToJs)) 
                                                    return $arrayToJs;  
                                              break; 
                                case 4 : $arrayToJs = $this->validateCancel($id, $autoChangeStatus);
                                         if (!empty($arrayToJs)) 
                                                return $arrayToJs;  
                                          break; 

                    } 
                }
         
			
			if(!$this->oDbCon->startTrans())
				throw new Exception($this->errorMsg[100]);
		 				 
			switch ($status){  
				case 4 : $this->cancelTrans($id,$copy);
                          $this->afterCancelTrans($id);
                          break;  
			}
            
			$sql = 'update '.$this->tableName.' set statuskey = '.$this->oDbCon->paramString($status).' where pkey = ' . $this->oDbCon->paramString($id);
			$this->oDbCon->execute($sql);
			
            $rsAR = $this->getDataRowById($id);
            // dipindahkan ke updateAROutstanding
            //$customerObj = $this->getCustomerObj();
            //$customerObj->updateAROutstanding($rsAR[0]['customerkey']);
            
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
	  
    
	function delete($id, $forceDelete = false,$reason = ''){ 
		
		 $arrayToJs =  array();  
		 $arrayToJs = $this->changeStatus($id, 4);  
		 return $arrayToJs; 

	}
     
   function cancelTrans($id,$copy){  
        $rsHeader = $this->getDataRowById($id);
       
       
        $autoCancel = $this->loadSetting('autoCancelARPayment');
       
        if($autoCancel == 1){
            $paymentObj = $this->getPaymentObj();
       
            $sql = 'select 
                        '.$paymentObj->tableName.'.pkey
                    from
                        '.$paymentObj->tableName.','.$paymentObj->tableNameDetail .'
                    where
                        '.$paymentObj->tableName.'.pkey = '.$paymentObj->tableNameDetail.'.refkey and
                        '.$paymentObj->tableName.'.statuskey = 1 and
                        '.$paymentObj->tableNameDetail.'.arkey = '.$paymentObj->oDbCon->paramString($id).' 
                    ';

            $rs = $paymentObj->oDbCon->doQuery($sql);

            for($i=0;$i<count($rs);$i++) 
                $paymentObj->changeStatus($rs[$i]['pkey'],4,'',false,true);
       }
        
		 
         // kalo pake module credit note
		if($this->isActiveModule('CreditNote')){  
			$rsARKey = $this->getTableKeyAndObj('ar',array('key'));
			$rsKey = $this->getTableKeyAndObj($this->tableName,array('key'));
			if($rsARKey['key'] == $rsKey['key']){  

					$creditNote = new CreditNote(); 
					$rsCN = $creditNote->getCreditNoteByAR($rsHeader[0]['pkey'],'  and '.$creditNote->tableName.'.statuskey = 1 ');
					for($i=0;$i<count($rsCN);$i++) { 
						$arrayToJs = $creditNote->changeStatus($rsCN[$i]['pkey'],4,'',false, true);
						if (!$arrayToJs[0]['valid'])
							throw new Exception('<strong>'.$rsHeader[0]['code'] . '</strong>. '.  $arrayToJs[0]['message']);    
					}  

			}
		}
       
       if( $this->isActiveModule('CashBank') ){
         if (ADV_FINANCE && TEST_VOUCHER) { 
            $cashBank = new CashBank(); 
            $rsARKey = $this->getTableKeyAndObj($this->tableName, array('key'));
            $cashBank->removeTransaction($id, $rsARKey['key']); 
        }
       }
       
	   
		if ($copy)
			$this->copyDataOnCancel($id);	  
       
        $this->cancelGLByRefkey($rsHeader[0]['pkey'],$this->tableName);
	}  
    
	 function validateOpen($id){ 
		$arrayToJs = array(); 
		$rs  = $this->getDataRowById($id);
		$this->addErrorList($arrayToJs,false,'<strong>'.$rs[0]['code'].'</strong>. ' . $this->errorMsg[201]);
		return $arrayToJs;
	 } 	
    
	 function validatePartial($id){
		$arrayToJs = array(); 
		$rs  = $this->getDataRowById($id);
		$this->addErrorList($arrayToJs,false,'<strong>'.$rs[0]['code'].'</strong>. ' . $this->errorMsg['ar'][3]);
          
		return $arrayToJs;
	 } 	
    
	 function validateClosed($id){ 
         
        $arrayToJs = array(); 
		$rs  = $this->getDataRowById($id);
		$this->addErrorList($arrayToJs,false,'<strong>'.$rs[0]['code'].'</strong>. ' . $this->errorMsg[201]);
		return $arrayToJs;
          
	 } 	 
    
    function validateCancel($id,$autoChangeStatus=false){
		// perlu cek validasi lg kalo ad payment yg sudah dikonfirmasi bagaimana ?
        // atau gk perlu selama statusnya tdk open 
          
		$arrayToJs = array(); 
		$rs  = $this->getDataRowById($id);
           
        if ( !$autoChangeStatus ) {
            if(!empty($rs[0]['islinked'])) 
                $this->addErrorList($arrayToJs,false,'<strong>'.$rs[0]['code'].'</strong>. ' . $this->errorMsg['ar'][4]);    
        } 
         
        // transaksi tetep tidak boleh dibatalkan jika sudah ad pembayaran (status AR <> open)  
        // meskipun transaksi manual atau transaksi dr sales order 
        if ( $rs[0]['statuskey'] <> 1) 
                $this->addErrorList($arrayToJs,false,'<strong>'.$rs[0]['code'].'</strong>. ' .$this->errorMsg[201]);     
              
    
        // khusus AR saja, turunan AR gk perlu validasi 
		
		// kalo pake Credit Note
		if($this->isActiveModule('CreditNote')){  
			$rsARKey = $this->getTableKeyAndObj('ar',array('key'));
			$rsKey = $this->getTableKeyAndObj($this->tableName,array('key'));
			if($rsARKey['key'] == $rsKey['key']){ 
				$creditNote = new CreditNote(); 
				$rsCN = $creditNote->getCreditNoteByAR($id,' and '.$creditNote->tableName.'.statuskey in (2,3) ');
				if(!empty($rsCN)) 
					$this->addErrorList($arrayToJs,false,'<strong>'.$rs[0]['code'].'</strong>. '. $this->errorMsg[201].'<br><strong>'.$rsCN[0]['code'].'</strong> ' .$this->errorMsg['creditNote'][3],true);

			}
		}
        
		return $arrayToJs;
	 } 	
		
    function getAROutstanding($customerkey){
        $sql = 'select coalesce(sum(outstanding*rate),0) as outstanding from ' . $this->tableName .' where customerkey = ' . $this->oDbCon->paramString($customerkey) .' and statuskey in (1,2)' ;
        $rs = $this->oDbCon->doQuery($sql);
        return $rs[0]['outstanding'];
    }
    
    
    function getPaymentObj(){
        return  new ARPayment();
    }
    
    function getCustomerObj(){
        return  new Customer();
    }
     
  
    function getARType($additionalType = array(),$overwriteType = false){
         
        $typekey = array();
        if(!$overwriteType){  
            array_push($typekey, AR_TYPE['salesOrder']);
            array_push($typekey, AR_TYPE['downPayment']);
            if(in_array(PLAN_TYPE['categorykey'], array( COMPANY_TYPE['trucking'], COMPANY_TYPE['forwarding'], COMPANY_TYPE['logistics']))){
				array_push($typekey,AR_TYPE['serviceOrder']);
				
				if ($this->loadSetting('splitARReimbursement') == 1)
						array_push($typekey,AR_TYPE['reimburse']); 
			}
               
        }
         
        $typekey = array_merge($typekey, $additionalType);
        
        $sql = 'select * from '.$this->tableType.' where pkey in ('.$this->oDbCon->paramString($typekey,',').')  and statuskey = 1 ';
        $rs = $this->oDbCon->doQuery($sql);	 
        
        return $rs;
    }
 
    /*function getDoNumber($refHeaderKey){
        $truckingServiceOrderInvoice = new TruckingServiceOrderInvoice();
        $truckingServiceOrder= new TruckingServiceOrder();
        $rsDetailInvoice = $truckingServiceOrderInvoice->getDetailWithRelatedInformation($refHeaderKey);
        $doNumber = array();
        if(!empty($rsDetailInvoice)){
            for($i=0;$i<count($rsDetailInvoice);$i++){
                if($rsDetailInvoice[$i]['invoicetype']<>1 || empty($rsDetailInvoice[$i]['salesorderkey']))
                    continue;
            
            $rsJO = $truckingServiceOrder->getDataRowById($rsDetailInvoice[$i]['salesorderkey']);
            if(!empty($rsJO))
                array_push($doNumber, $rsJO[0]['donumber']);
                //$this->setLog($rsDetailInvoice[$i]['salesorderkey'].' --- '.$rsJO[0]['donumber'].' ----- '.$rsJO[0]['pkey'].' ----- '.$rsDetailInvoice[$i]['refkey']);
                
            }
            
        }
        
        //$this->setLog(implode(", ",$doNumber));
        
        return $doNumber;
        
    }*/


    function normalizeParameter($arrParam, $trim = false){
        
        
        $arrParam['selCurrency'] = (!empty($arrParam['selCurrency'])) ? $arrParam['selCurrency'] : CURRENCY['idr'];
        
        $arrParam['amount'] = abs($this->unFormatNumber($arrParam['amount'])); 
        $rsARType = $this->getARType(array($arrParam['selARType']),true); 
        $arrParam['amount'] *= $rsARType[0]['contra'];
             
        $rate = (isset($arrParam['currencyRate']) && !empty($arrParam['currencyRate'])) ? $this->unFormatNumber($arrParam['currencyRate']) : 1;
        $arrParam['amountIDR'] =  $arrParam['amount'] * $rate;
        
        $arrParam = parent::normalizeParameter($arrParam,true);  
        
        // old rs
        $oldRs = $this->getDataRowById($arrParam['pkey']);
        $arrParam['oldRs'] = $oldRs;
		
        return $arrParam;
    }
	
        
    function updateAROutstanding($arkey){
	    $arPaymentObj = $this->getPaymentObj(); 
		$rsAR = $this->getDataRowById($arkey);
        
        
		// kalo statusnya sudah batal tdk boleh dibalikin lg.
        // case karena terakhir update AR dicancel gk otomatis cancel payment 
        
        // case : PO kebentuk AP
        //        AP dibuat Payment, blm dikonfirmasi (status menunggu)
        //        PO dicancel, otomatis AP kecancel
        //        ketika payment dicancel, AP nya balik lg ke menunggu
        
        if($rsAR[0]['statuskey'] == 4) return;
        
		$sql = 'select 
						coalesce(sum('.$arPaymentObj->tableNameDetail.'.amount + '.$arPaymentObj->tableNameDetail.'.discount),0) as totalPaidAmount
				 from 
				 	' . $arPaymentObj->tableName.','.$arPaymentObj->tableNameDetail. '
				 where ' . $arPaymentObj->tableNameDetail.'.refkey = '.$arPaymentObj->tableName .'.pkey and 
				 	  ('.$arPaymentObj->tableName .'.statuskey = 2 or '.$arPaymentObj->tableName .'.statuskey = 3 )and
					  '.$arPaymentObj->tableNameDetail.'.arkey = '.$arPaymentObj->oDbCon->paramString($arkey).'
				'  ;
		$rsAmount =  $this->oDbCon->doQuery($sql); 
		$totalPaidAmount = $rsAmount[0]['totalPaidAmount'];    
	        
        //cari balancenya saja
        $balance  = $rsAR[0]['amount'] - $totalPaidAmount;
      
        $balanceRounding = ($rsAR[0]['currencykey'] == CURRENCY['idr']) ? ARAP_BALANCE_ROUNDING['idr'] : ARAP_BALANCE_ROUNDING['currency'];
        $tempBalance = round($balance * 100)/100; // buat buletin yg 0.00999999999 semoga saja bisa
        if($tempBalance < $balanceRounding && $tempBalance >  $balanceRounding * -1)  
            $balance = 0;  
            
            
        // haruss diatas, sebelum balancenya dikali -1
//	    $sql  = 'update '.$this->tableName.' set outstanding = amount - ' . $totalPaidAmount .' where statuskey <> 4 and pkey = ' .$arkey ;	 
	    $sql  = 'update '.$this->tableName.' set outstanding =' .$this->oDbCon->paramString($balance) .' where statuskey <> 4 and pkey = ' .  $this->oDbCon->paramString($arkey)  ;	 
	    $this->oDbCon->execute($sql);  
        
        
        if ($rsAR[0]['artype'] == AR_TYPE['creditNote']) $balance *= -1;
        
        $statuskey = AP_STATUS['open']; 
        
        if ($balance <= 0)  // lunas
			$statuskey = AP_STATUS['lunas'];
        else if ($balance > 0 && $balance < abs($rsAR[0]['amount'])) // partial, pake abs utk positifin CN
		    $statuskey =  AP_STATUS['partial'];
       
    
         if($rsAR[0]['statuskey'] <> $statuskey) {
                $this->changeStatus($arkey,$statuskey, '', false, true,true);

                if(!empty($rsAR[0]['reftabletype'])){
                    $refObj=$this->getObjMapping('',$rsAR[0]['reftabletype']);

                    if(!empty($refObj))
                        $refObj->afterARStatusChanged($rsAR[0]['refkey']);
                }
            }        

       
       
        // update customer outstanding 
        $customerObj = $this->getCustomerObj();
        $customerObj->updateAROutstanding($rsAR[0]['customerkey']);
        
        //update paid status
        // jgn join langsung di sales order, takut berat
        if ($this->activeModule['salesorder'] && !empty($rsAR[0]['refheaderkey'])){ 
           $salesOrder = new SalesOrder();
           $salesOrder->updatePaidStatus($rsAR[0]['refheaderkey'], $statuskey);
        }

	}    
        
    function getARTypeName($arrTypeKey){ 
        if (!is_array($arrTypeKey))  
            $arrTypeKey = array($arrTypeKey); 
            
        $sql = 'select * from '.$this->tableType.' where pkey in ('.$this->oDbCon->paramString($arrTypeKey,',').') and statuskey = 1 '; 
        return $this->oDbCon->doQuery($sql); 
    }
    
     
    function groupARByCustomer($rs, $rsCurrency = array(), $groupBy = ''){
        
        $totalCurrency = count($rsCurrency);
        
	   // bagi per customer
        $arrARCustomer = array();
        foreach($rs as $row){
            $indexkey = (!empty($groupBy)) ? $row[$groupBy['field']] : $row['customerkey'];
            $indexname = (!empty($groupBy)) ? $groupBy['label'] : 'customername';
            // init
            if (!isset($arrARCustomer[$indexkey])){
            
                $arrARCustomer[$indexkey] = array($indexname => $row[$indexname], 'detail' => array());
                
                if($totalCurrency <= 1){
                    $arrARCustomer[$indexkey]['totalamount'] = 0;
                    $arrARCustomer[$indexkey]['totaloutstanding'] = 0;
                }else{
                    foreach($rsCurrency as $currencyRow){ 
                        $arrARCustomer[$indexkey]['totalamount'.$currencyRow['pkey']] = 0;
                        $arrARCustomer[$indexkey]['totaloutstanding'.$currencyRow['pkey']] = 0;
                    }
                }
              
            } 
            
             
            if($totalCurrency <= 1){
                $arrARCustomer[$indexkey]['totalamount'] += $row['amount'];
                $arrARCustomer[$indexkey]['totaloutstanding'] += $row['outstanding'];
            }else{ 
                $arrARCustomer[$indexkey]['totalamount'.$row['currencykey']] += $row['amount'];
                $arrARCustomer[$indexkey]['totaloutstanding'.$row['currencykey']] += $row['outstanding'];
            }

            array_push($arrARCustomer[$indexkey]['detail'], $row); 
            
        }

        // agar bisa pake for i di report
        
        return array_values($arrARCustomer);
    } 
    
    
   	function generateARReport($criteria='', $order = '', $groupBy = ''){
        
	   $sql =  '
			SELECT 
					GROUP_CONCAT('.$this->tableName.'.pkey) as pkey,
					GROUP_CONCAT('.$this->tableName.'.refkey) as refkey,
					coalesce(sum(outstanding),0) as totaloutstanding,
					coalesce(sum(tax23value),0) as totaltax23value,
					coalesce(sum(amount),0) as totalamount,
                   '.$this->tableCustomer.'.name as customername,
                   '.$this->tableCurrency.'.pkey as currencykey,
                   '.$this->tableCurrency.'.name as currencyname ,
				   '.$this->tableEmployee .'.name as salesman,
                   '.$this->tableName.'.reftabletype
			FROM 
                '.$this->tableStatus.',  
                '.$this->tableName.' 
				  	left join ' . $this->tableEmployee .' on  '.$this->tableName.'.saleskey = ' . $this->tableEmployee .'.pkey ,
                '.$this->tableCustomer.',
                '.$this->tableCurrency.',
				'.$this->tableWarehouse.'
			WHERE     
                '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey and 
                '.$this->tableName.'.customerkey = '.$this->tableCustomer.'.pkey and 
                '.$this->tableName.'.warehousekey = '.$this->tableWarehouse.'.pkey and 
                '.$this->tableName.'.currencykey = '.$this->tableCurrency.'.pkey
 		'; 
        
		$sql .=  $this->getWarehouseCriteria() ;
        $sql .=  $this->getCustomerCriteria() ;
		
        if (!empty($criteria))  
            $sql .=  ' ' .$criteria; 
		$sql .= (empty($groupBy)) ? ' group by customerkey, currencykey' : $groupBy;
         
         
        if (!empty($order))  
            $sql .=  ' ' .$order; 
		
       //$this->setLog($sql,true);
       return $this->oDbCon->doQuery($sql);
		 
    } 
    
    function searchARCard($datePeriod, $criteria = '', $order = ''){
        
        $datePeriod = $this->oDbCon->paramDate($datePeriod,' / ','Y-m-d 23:59');
        
        $sql = 'select 

                    '.$this->tableName.'.pkey,
                    '.$this->tableName.'.code,
                    datediff('.$datePeriod.','.$this->tableName.'.duedate)  as datediff,
                    '.$this->tableName.'.artype, 
                    '.$this->tableType.'.name as artypename,
                    '.$this->tableWarehouse.'.name as warehousename,
                    '.$this->tableName.'.refcode, 
                    '.$this->tableName.'.trdate,
                    '.$this->tableName.'.amount, 
                    '.$this->tableName.'.amountidr, 
                    '.$this->tableName.'.currencykey,
                    '.$this->tableName.'.rate, 
                    '.$this->tableName.'.trdesc,
                    '.$this->tableName.'.customerkey,
                    '.$this->tableCustomer.'.name as customername,
                    coalesce(sum(ar_payment.amount),0) as paidamount,
                    '.$this->tableName.'.amount - coalesce(sum(ar_payment.amount + ar_payment.discount),0)  as outstanding
                from 
                    '.$this->tableName.'
                        left join (
                             select '.$this->tablePaymentDetail.'.amount, '.$this->tablePaymentDetail.'.discount,  '.$this->tablePaymentDetail.'.arkey
                    		 from '.$this->tablePaymentHeader.',  '.$this->tablePaymentDetail.'  
                    		 where 
                                '.$this->tablePaymentHeader.'.pkey =  '.$this->tablePaymentDetail.'.refkey and
                                '.$this->tablePaymentHeader.'.statuskey in ('.TRANSACTION_STATUS['konfirmasi'].','.TRANSACTION_STATUS['selesai'].') and 
                                '.$this->tablePaymentHeader.'.trdate <= '.$datePeriod.'
                        ) ar_payment on  '.$this->tableName.'.pkey = ar_payment.arkey,
                    '.$this->tableType.',
                    '.$this->tableWarehouse.',
                    '.$this->tableCustomer.'  
                where
                    '.$this->tableName.'.customerkey =  '.$this->tableCustomer.'.pkey and
                    '.$this->tableName.'.artype =  '.$this->tableType.'.pkey and
                    '.$this->tableName.'.warehousekey =  '.$this->tableWarehouse.'.pkey and
                    '.$this->tableName.'.trdate <= '.$datePeriod.' and
                    '.$this->tableName.'.statuskey <> '. TRANSACTION_STATUS['batal'];
          
		$sql .=  $this->getWarehouseCriteria() ;
        $sql .=  $this->getCustomerCriteria() ;
		
        if (!empty($criteria))  
            $sql .=  ' ' .$criteria; 
        
		// gk bisa, jadinya ngaco groupnya, karena ad 2 level sum
        // $sql .= (empty($groupBy)) ? ' group by '.$this->tableName.'.code  ' : $groupBy ;
		
		$sql .= ' group by '.$this->tableName.'.code  ';
//        $sql .= ' having ( outstanding > '.$this->rounding.' or outstanding <  '. ($this->rounding * -1).' ) '; // CN sepertinya outstandingnya minus, jd gk bisa pake outstanding > 0
         $sql .= ' having ( outstanding <> 0 ) '; // CN sepertinya outstandingnya minus, jd gk bisa pake outstanding > 0
       
        
        if (!empty($order))  
            $sql .=  ' ' .$order; 
        
        
		//$this->setLog($sql,true);
        $rsAR = $this->oDbCon->doQuery($sql);
        
         // hilangin semua AP yg dibawah rounding
         // utk model baru harusya sudah gk perlu, karena semua outstanding sudah di nol kan diawal
         $total = count($rsAR);
         for($i=0;$i<$total;$i++){ 
             $balanceRounding = ($rsAR[$i]['currencykey'] == CURRENCY['idr']) ? ARAP_BALANCE_ROUNDING['idr'] : ARAP_BALANCE_ROUNDING['currency'];
             
             if($rsAR[$i]['outstanding'] < $balanceRounding && $rsAR[$i]['outstanding'] >  $balanceRounding * -1)
                 unset($rsAR[$i]);
         }
        
        return $rsAR;
        
    }
	
	function getCustomerARCard($arrCustomerKey = array(),$criteria=''){
		 
		$customerCriteria = '';
		if(!empty($arrCustomerKey)){ 
			if(!is_array($arrCustomerKey))
				$arrCustomerKey = array($arrCustomerKey);
			
			$customerCriteria = ' and '.$this->tableName.'.customerkey in('.$this->oDbCon->paramString($arrCustomerKey,',').')'; 
		}
		
		$sql = '
			select '.$this->tableName.'.*,'.$this->tableCurrency.'.name as currencyname  from (
				select
						'.$this->tableName.'.pkey,
						'.$this->tableName.'.code,
						'.$this->tableName.'.trdate, 
						'.$this->tableName.'.refcode,
						'.$this->tableName.'.amount, 
						'.$this->tableName.'.currencykey,
						'.$this->tableName.'.createdon ,
						'.$this->tableName.'.customerkey,
						1 as tabletype
				from 
						'.$this->tableName.' '.$this->tableName.'
				where '.$this->tableName.'.statuskey in (1,2,3)
						'.$customerCriteria.'
				
				union all
				
				select 
					'.$this->tableName.'.pkey,
					'.$this->tableName.'.code as code, 
					'.$this->tableName.'.trdate, 
					'.$this->tableName.'.refcode, 
					'.$this->tableName.'.totalreceived * -1,
					'.$this->tableName.'.currencykey,
					'.$this->tableName.'.createdon,
					'.$this->tableName.'.customerkey ,
					2 as tabletype
				from 
					'.$this->tablePaymentHeader.' '.$this->tableName.'
				where 
					'.$this->tableName.'.statuskey in (2,3)
					'.$customerCriteria.'
			) '.$this->tableName.' left join '.$this->tableCurrency.'  on '.$this->tableName.'.currencykey = '.$this->tableCurrency.'.pkey 
			
			where 1=1
		';
		
		$sql .=  $this->getWarehouseCriteria() ;
        $sql .=  $this->getCustomerCriteria() ;
		
		$sql .=  ' ' .$criteria; 
		$sql .= 'order by trdate asc, createdon asc';
		
		//$this->setLog($sql,true);
		return $this->oDbCon->doQuery($sql);
	}
		
//	 function searchARCard($datePeriod, $criteria, $order, $groupBy = ''){
//        
//		// utk compability 
//		 
//        $startDate = $this->oDbCon->paramDate($datePeriod['trStartDate'],' / ','Y-m-d');
//        $endDate = $this->oDbCon->paramDate($datePeriod['trEndDate'],' / ','Y-m-d 23:59');
//        
//        $sql = 'select 
//
//                    '.$this->tableName.'.pkey,
//                    '.$this->tableName.'.code,
//                    '.$this->tableType.'.name as artypename,
//                    '.$this->tableWarehouse.'.name as warehousename,
//                    '.$this->tableName.'.refcode, 
//                    '.$this->tableName.'.trdate,
//                    '.$this->tableName.'.amount, 
//                    '.$this->tableName.'.currencykey,
//                    '.$this->tableName.'.trdesc,
//                    '.$this->tableCustomer.'.name as customername,
//                    coalesce(sum(ar_payment.amount),0) as paidamount,
//                    '.$this->tableName.'.amount - coalesce(sum(ar_payment.amount + ar_payment.discount),0)  as outstanding
//                from 
//                    '.$this->tableName.'
//                        left join (
//                             select '.$this->tablePaymentDetail.'.amount, '.$this->tablePaymentDetail.'.discount,  '.$this->tablePaymentDetail.'.arkey
//                    		 from '.$this->tablePaymentHeader.',  '.$this->tablePaymentDetail.'  
//                    		 where 
//                                '.$this->tablePaymentHeader.'.pkey =  '.$this->tablePaymentDetail.'.refkey and
//                                '.$this->tablePaymentHeader.'.statuskey in ('.TRANSACTION_STATUS['konfirmasi'].','.TRANSACTION_STATUS['selesai'].') and 
//                                '.$this->tablePaymentHeader.'.trdate <= '.$endDate;
//
//								// jika ada tgl mulai
//								if(!empty($datePeriod['trStartDate']))
//									$sql .= ' and '. $this->tablePaymentHeader.'.trdate >= '.$startDate;
//
//		$sql .=  ') ar_payment on  '.$this->tableName.'.pkey = ar_payment.arkey,
//                    '.$this->tableType.',
//                    '.$this->tableWarehouse.',
//                    '.$this->tableCustomer.'  
//                where
//                    '.$this->tableName.'.customerkey =  '.$this->tableCustomer.'.pkey and
//                    '.$this->tableName.'.artype =  '.$this->tableType.'.pkey and
//                    '.$this->tableName.'.warehousekey =  '.$this->tableWarehouse.'.pkey and
//                    '.$this->tableName.'.trdate <= '.$endDate
//					
//		// jika ada tgl mulai
//		if(!empty($datePeriod['trStartDate']))
//			$sql .= ' and '.$this->tablePaymentHeader.'.trdate >= '.$startDate;
//			
//        $sql .=  ' and '.$this->tableName.'.statuskey <> '. TRANSACTION_STATUS['batal'];
//          
//		
//        if (!empty($criteria))  
//            $sql .=  ' ' .$criteria; 
//        
//        $sql .= (empty($groupBy)) ? ' group by '.$this->tableName.'.code  ' : $groupBy ;
//		
//		
//        $sql .= ' having outstanding > 0 ';
//        
//        if (!empty($order))  
//            $sql .=  ' ' .$order; 
//        
//         $this->setLog($sql,true);
//         return $this->oDbCon->doQuery($sql);
//        
//    }
    
    function getARPrepaidTaxAmount($arkey){
            
        $sql = 'SELECT 
                    coalesce(SUM('.$this->tableARPrepaid.'.amount-'.$this->tableARPrepaid.'.outstanding),0) AS taxamount 
                FROM 
                    '.$this->tableName.',
                    '.$this->tableARPrepaid.',
                    '.$this->tablePaymentDetail.'
                WHERE 
                    '.$this->tablePaymentDetail.'.arkey = '.$this->tableName.'.pkey AND 
                    '.$this->tableARPrepaid.'.refkey = '.$this->tablePaymentDetail.'.pkey AND 
                    '.$this->tableARPrepaid.'.statuskey IN (2,3)
                    AND '.$this->tableName.'.pkey = '.$this->oDbCon->paramString($arkey).' ';
           
         $rs =  $this->oDbCon->doQuery($sql); 
         return $rs[0]['taxamount'];
        
    }
    
    
    function generateCashflowReport($criteria=''){
		$rsTrans = array(); 
		$arrSQL = array(); 
					
		$sql = 'SELECT  
					warehousekey,
					currencykey,  
					Year(trdate) as tryear,
					month(trdate) as trmonth,
					CONCAT(MONTHNAME(trdate), \' \' ,YEAR(trdate)) AS trmonthyear,
					CONCAT(YEAR(trdate),\'-\',MONTH(trdate)) AS timeindex,
					SUM(amountidr)AS totalidr, 
					'.$this->tableCurrency.'.name as currencyname
				FROM '.$this->tableName.' 
					 left join ' . $this->tableCurrency .' on  '.$this->tableName.'.currencykey = ' . $this->tableCurrency .'.pkey 
				where 
					'.$this->tableName.'.statuskey <> 4 ';
		
		if (!empty($criteria)) $sql .=  ' ' .$criteria;   
		 
		$sql .= ' GROUP BY warehousekey,currencykey,timeindex'; 
		$sql .= ' ORDER BY trdate asc';
        
          
       //$this->setLog($sql,true);
       return $this->oDbCon->doQuery($sql);
		 
    }	
	
	function generateHeaderARAPBalanceReport($criteria=''){
		$rsTrans = array(); 
		$arrSQL = array(); 
		
		$sql = 'SELECT 
					GROUP_CONCAT('.$this->tableName.'.pkey) AS arrpkey,
					MONTHNAME(trdate) AS trmonthname,
					MONTH(trdate) AS trmonth,
					YEAR(trdate) AS tryear,
					CONCAT(YEAR(trdate),\'-\',MONTH(trdate)) AS trindex,
					SUM(amount * rate) AS grandtotalamount,
					SUM(amount) AS totalamount,
					SUM(amountidr)AS totalidr,
					SUM(outstanding) AS totaloutstanding
				FROM '.$this->tableName.' 
				where 
					'.$this->tableName.'.statuskey in(2,3) ';
		
		if (!empty($criteria)) $sql .=  ' ' .$criteria;  
        array_push($arrSQL,$sql);
		
		
		$sql = 'SELECT 
					GROUP_CONCAT('.$this->tableAP.'.pkey) AS arrpkey, 
					MONTHNAME(trdate) AS trmonthname,
					MONTH(trdate) AS trmonth,
					YEAR(trdate) AS tryear,
					CONCAT(YEAR(trdate),\'-\',MONTH(trdate)) AS trindex,
					SUM(-amount * rate) AS grandtotalamount,
					SUM(-amount) AS totalamount,
					SUM(-amountidr) AS totalidr,
					SUM(-outstanding) AS totaloutstanding
				FROM '.$this->tableAP.' 
				where 
					'.$this->tableAP.'.statuskey in(2,3) ';
		
        if (!empty($criteria)) $sql .=  ' ' .$criteria;  
        array_push($arrSQL,$sql);
       
        $sql = implode ( ' UNION ALL ' , $arrSQL);
		
		$sql .= ' Group BY trindex,warehousekey';
		$sql .= ' ORDER BY tryear desc,trmonth desc';
        
          
//       $this->setLog($sql,true);
       return $this->oDbCon->doQuery($sql);
		 
    }
	
	function getEMKLARSOAReport($criteria='',$orderBy=''){
		
		
		$EMKLInvTableKey = $this->getTableKeyAndObj($this->tableEMKLOrderInvoice, array('key'))['key'];
		
		
		$sql = 'select  
					'.$this->tableEMKLOrderInvoice.'.statuskey, '.$this->tableEMKLOrderInvoice.'.currencykey, '.$this->tableEMKLOrderInvoice.'.outstanding, 
					'.$this->tableWarehouse.'.name as warehousename,
					'.$this->tableCustomer.'.name as customername, 
					'.$this->tableName.'.*, 
					'.$this->tableStatus.'.status as statusname, 
					COALESCE(NULLIF( '.$this->tableName.'.refcode,\'\'), '.$this->tableEMKLOrderInvoice.'.code) as refcode,
					COALESCE(NULLIF( '.$this->tableName.'.currencykey,\'\'), '.$this->tableEMKLOrderInvoice.'.currencykey) as currencykey,
					COALESCE(NULLIF( '.$this->tableName.'.outstanding,\'\'), '.$this->tableEMKLOrderInvoice.'.outstanding) as outstanding,
					COALESCE(NULLIF( '.$this->tableName.'.reftabletype,\'\'), '.$EMKLInvTableKey.') as reftabletype,
					COALESCE(NULLIF( '.$this->tableName.'.refheaderkey,\'\'), '.$this->tableEMKLOrderInvoice.'.pkey) as refheaderkey  ,
					COALESCE(NULLIF( '.$this->tableStatus.'.status,\'\'), \'Unconfirmed\') as statusname,
					COALESCE(NULLIF( if('.$this->tableName. '.statuskey = 1 or '.$this->tableName. '.statuskey = 2, datediff(now(),duedate) , 0) ,\'\'), 0) as datediff 
				from 
				'.$this->tableEMKLOrderInvoice.' 
					left join '.$this->tableName.' 	on   '.$this->tableName.'.refheaderkey = '.$this->tableEMKLOrderInvoice.'.pkey
					left join '.$this->tableStatus.' 	on   '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey
					left join '.$this->tableWarehouse.' on  '.$this->tableEMKLOrderInvoice.'.warehousekey = '.$this->tableWarehouse.'.pkey 
					left join '.$this->tableCustomer.' on  '.$this->tableEMKLOrderInvoice.'.customerkey = '.$this->tableCustomer.'.pkey  
				where ('.$this->tableName.'.statuskey in (1,2) or '.$this->tableEMKLOrderInvoice.'.statuskey = 1)
		';
		
		$sql .= ' ' .$criteria;
		$sql .= ' ' .$orderBy;
		
		return $this->oDbCon->doQuery($sql);
	}
    
    function getOverdueOutstanding($month = 1) {
        $sql = '
            SELECT
                '. $this->tableName .'.*,  
					'.$this->tableCustomer.'.name as customername,
					'.$this->tableStatus.'.status as statusname,
					'.$this->tableWarehouse.'.name as warehousename , 
					'.$this->tableCurrency.'.name as currencyname
            FROM
                '. $this->tableName .'
                    left join ' . $this->tableCurrency .' on  '.$this->tableName.'.currencykey = ' . $this->tableCurrency .'.pkey,
                '.$this->tableStatus.' ,
                '.$this->tableCustomer.' ,
                '.$this->tableWarehouse.' 
            WHERE
                '.$this->tableName . '.statuskey = '.$this->tableStatus.'.pkey and 
				'.$this->tableName . '.warehousekey = '.$this->tableWarehouse.'.pkey and 
				'.$this->tableName.'.customerkey = '.$this->tableCustomer.'.pkey and
                '. $this->tableName .'.trdate < DATE_SUB(CURDATE(), INTERVAL '. $month .' MONTH) and 
                '. $this->tableName .'.statuskey in (1,2)
        ';

        $rs  = $this->oDbCon->doQuery($sql);

        return $rs;
    }

    
	
	/*function getInvoiceType($tableName){ 
	
        
        $salesOrder = new SalesOrder();
        $truckingServiceOrderInvoice = new TruckingServiceOrderInvoice();
        $arPayment = new ARPayment();
        
        $arr = array();
        
        switch ($tableName){ 
            case $truckingServiceOrderInvoice->tableName : $arr = array('key' => 2,  
                                                                       'obj' => $truckingServiceOrderInvoice 
                                                                      );
                                                          break; 
            case $arPayment->tableName : $arr = array('key' => 3,  
                                                       'obj' => $arPayment 
                                                      );
                                                break; 
 
            default : $arr = array('key' => 1,  
                                   'obj' => $salesOrder 
                                  );
        }
        
        return $arr;
        
    }*/
      
}
		
?>
