<?php
  
class CashBankRealization extends BaseClass{ 
   
   function __construct(){
		
		parent::__construct();
       
		$this->tableName = 'cash_bank_realization_header';
		$this->tableNameDetail = 'cash_bank_realization_detail';  
        $this->tableStatus = 'cash_bank_realization_status'; 
        $this->tableTruckingCashOut = 'trucking_cost_cash_out_header'; 
        $this->tableEmployee = 'employee';
        $this->tableItem = 'item';
        $this->tableConsignee = 'consignee';
		$this->tablePayment= 'cash_bank_realization_payment';
        $this->tableTruckingServiceOrder = 'trucking_service_order_header';
        $this->securityObject = 'CashBankRealization';
        $this->isTransaction = true;
        $this->allowedStatusForEdit = array(1);
       
        $this->arrDataDetail = array(); 
        $this->arrDataDetail['pkey'] = array('hidDetailKey');
        $this->arrDataDetail['refkey'] = array('pkey','ref');
        $this->arrDataDetail['costkey'] = array('hidCostKey');
        $this->arrDataDetail['refkey2'] = array('refheadercostkey');
        $this->arrDataDetail['qty'] = array('qty','number');
        $this->arrDataDetail['costvalue'] = array('costValue','number');
        $this->arrDataDetail['realcostvalue'] = array('realCostValue','number');
        $this->arrDataDetail['amount'] = array('amount','number');
        $this->arrDataDetail['description'] = array('detailDesc'); 
        $this->arrDataDetail['settlementtypekey'] = array('hidSettlementType'); 
        
       
        $arrPaymentDetail = array(); 
        $arrPaymentDetail['pkey'] = array('hidDetailPaymentKey');
        $arrPaymentDetail['refkey'] = array('pkey', 'ref');
        $arrPaymentDetail['amount'] = array('paymentMethodValue',array('datatype' => 'number','mandatory'=>true));
        $arrPaymentDetail['paymentkey'] = array('selPaymentMethod',array('mandatory'=>true)); 
        
        $arrDetails = array();
        array_push($arrDetails, array('dataset' => $this->arrDataDetail));
        array_push($arrDetails, array('dataset' => $arrPaymentDetail, 'tableName' => $this->tablePayment)); 
      
        $this->arrData = array(); 
        $this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails));
        $this->arrData['code'] = array('code');
        $this->arrData['refkey'] = array('hidRefKey');
        $this->arrData['refkey2'] = array('hidRefKey2');
        $this->arrData['refkey3'] = array('hidRefKey3');
        $this->arrData['refcode'] = array('refCode');
        $this->arrData['refcode2'] = array('refCode2');
        $this->arrData['refcode3'] = array('refCode3');
        $this->arrData['trdate'] = array('trDate','date');
        $this->arrData['employeekey'] = array('hidEmployeeKey');
        $this->arrData['trdesc'] = array('trDesc');
        $this->arrData['total'] = array('total','number'); 
        $this->arrData['totalrealization'] = array('totalRealization','number'); 
        $this->arrData['reftabletype'] = array('hidRefTable'); 
        $this->arrData['reftabletype2'] = array('hidRefTable2'); 
        //$this->arrData['islinked'] = array('islinked'); 
        $this->arrData['statuskey'] = array('selStatus');    
        $this->arrData['warehousekey'] = array('selWarehouse');
        $this->arrData['customerkey'] = array('hidCustomerKey');
        $this->arrData['consigneekey'] = array('hidConsigneeKey');
        $this->arrData['settlementcoakey'] = array('hidCOASettlementKey');
       
        $this->arrData['totalreceived'] = array('totalReceived','number');
        $this->arrData['totalpayment'] = array('totalPayment','number'); 
        $this->arrData['employeear'] = array('employeeAR','number'); 
        $this->arrData['balance'] = array('balance','number'); 
       
        $this->arrData['jokey'] = array('hidJOKey');
        $this->arrData['wokey'] = array('hidWOKey');
        
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'date','title' => 'date','dbfield' => 'trdate','default'=>true, 'width' => 80, 'align' =>'center', 'format' => 'date'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'refCode1','title' => 'cashOutRef','dbfield' => 'refcode','default'=>true, 'width' => 100 ));
        array_push($this->arrDataListAvailableColumn, array('code' => 'refCode2','title' => 'reference','dbfield' => 'refcode2','default'=>true, 'width' => 100 ));
        array_push($this->arrDataListAvailableColumn, array('code' => 'refCode3','title' => 'reference','dbfield' => 'refcode3','default'=>true, 'width' => 100 ));
        array_push($this->arrDataListAvailableColumn, array('code' => 'employee','title' => 'employee','dbfield' => 'employeename','default'=>true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'amount','title' => 'amount','dbfield' => 'total','default'=>true, 'width' => 100, 'align' => 'right', 'format' => 'number'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'realization','title' => 'realization','dbfield' => 'totalrealization','default'=>true, 'width' => 100, 'align' => 'right', 'format' => 'number'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 90));
        array_push($this->arrDataListAvailableColumn, array('code' => 'si','title' => 'si','dbfield' => 'donumber','width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'bookingNumber','title' => 'bookingNumber','dbfield' => 'shipmentnumber','width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'customer','title' => 'customer','dbfield' => 'customername','width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'consignee','title' => 'consignee','dbfield' => 'consigneename','width' => 150));
        
        $this->printMenu = array();  
        array_push($this->printMenu,array('code' => 'printTransaction', 'name' => $this->lang['printTransaction'],  'icon' => 'print', 'url' => 'print/cashBankRealization'));
        
       
        //$this->arrLinkedTable = array();   
        
        $this->includeClassDependencies(array(
            'ChartOfAccount.class.php',
            'Warehouse.class.php',  
            'TruckingServiceOrder.class.php',
            'TruckingServiceWorkOrder.class.php',
            'Category.class.php',
            'Item.class.php',
            'COALink.class.php',
            'TruckingCostCashOut.class.php',
            'GeneralJournal.class.php',
            'Service.class.php',
            'CashBank.class.php',
            'AR.class.php',
            'AP.class.php',
            'AREmployee.class.php',
            'APEmployee.class.php',
            'PaymentMethod.class.php'
        ));  
       
       
        $this->overwriteConfig();
   }
   
 
   function getQuery(){
	   
	   $sql =  '
			SELECT '.$this->tableName.'.*, 
			   '.$this->tableStatus.'.status as statusname ,   
			   '.$this->tableEmployee.'.name as employeename,
               '.$this->tableCustomer.'.name as customername,
               '.$this->tableConsignee.'.name as consigneename,
               '.$this->tableTruckingServiceOrder.'.donumber,
               '.$this->tableTruckingServiceOrder.'.shipmentnumber
			FROM 
                '.$this->tableStatus.',  
                '.$this->tableName.'    
                    left join '.$this->tableEmployee.' on '.$this->tableName.'.employeekey = '.$this->tableEmployee.'.pkey  
                    left join '.$this->tableTruckingCashOut.' on  '.$this->tableName.'.refkey = '.$this->tableTruckingCashOut.'.pkey  
                    left join '.$this->tableTruckingServiceOrder.' on  '.$this->tableTruckingCashOut.'.jokey = '.$this->tableTruckingServiceOrder.'.pkey  
                    left join '.$this->tableCustomer.' on  '.$this->tableTruckingServiceOrder.'.customerkey = '.$this->tableCustomer.'.pkey   
                    left join '.$this->tableConsignee.' on  '.$this->tableTruckingServiceOrder.'.consigneekey = '.$this->tableConsignee.'.pkey  
			WHERE     
                '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey 
 		' .$this->criteria ; 
       
       return $sql;
		 
    }  

	
        
     function validateForm($arr,$pkey = ''){ 
		  
		$arrayToJs = parent::validateForm($arr,$pkey); 
		  
        $truckingServiceWorkOrder = new TruckingServiceWorkOrder();
		$item = new Item();   
		$arrCostKey = $arr['hidCostKey']; 
		$arrAmount = $arr['amount'];   
		$employeeAR =  $this->unFormatNumber($arr['employeeAR']);   
		$balance =  $this->unFormatNumber($arr['balance']);   
		$closingCOA = $arr['hidCOASettlementKey'];
           
        if(empty($arr['hidRefKey'])){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['reference'][1]);
		} 
         
        // gk boleh ad 2 realisasi
        /*$rsRealizationKey = $this->getTransactionType($truckingServiceWorkOrder->tableName); 
        $pkeyCriteria = (!empty($pkey)) ?  ' and '.$this->tableName.'.pkey <> ' .$this->oDbCon->paramString($pkey) : '';
         
        $rs = $this->searchData($this->tableName.'.refkey',$arr['refkey'],true,$pkeyCriteria. ' and reftabletype = '.$this->oDbCon->paramString($rsRealizationKey['key']).' and '.$this->tableName.'.statuskey in (1,2,3)' );
        if (!empty($rs)){ 
            $this->addErrorList($arrayToJs,false,'<strong>'.$rs[0]['code'].'</strong>. ' . $this->errorMsg[212].' '. $this->errorMsg[215]); 
        }*/

        // jumlah AR Employee gk boleh negatif
        if($employeeAR < 0)
            $this->addErrorList($arrayToJs,false,$this->errorMsg['arEmployee'][3]); 
	   
        // closing COA cuma kalo ad duit balik
        if($balance > 0 && empty($closingCOA)) 
			$this->addErrorList($arrayToJs,false,$this->errorMsg['coa'][1]); 
	  
        if(empty($arrCostKey)) 
			$this->addErrorList($arrayToJs,false,$this->errorMsg[501]); 
	 
		  
		for($i=0;$i<count($arrCostKey);$i++) { 
			$costkey = $arrCostKey[$i];
            $rsItem = $item->getDataRowById($costkey); 
            
			if (empty($costkey)){ 
				$this->addErrorList($arrayToJs,false, $this->errorMsg['cost'][1]); 	
			}else{
                $amount = $this->unFormatNumber($arrAmount[$i]);  
                
                if ($amount < 0){
                    $this->addErrorList($arrayToJs,false,$rsItem[0]['code'] . ' - ' .$rsItem[0]['name']. '. ' . $this->errorMsg[503]); 
                }  
            }
		
		}
		    if ($arr['totalReceived'] <= 0 && $employeeAR <> 0) {
            $this->addErrorList($arrayToJs,false,$this->errorMsg['arEmployee'][5]); 
        } else if ($arr['totalReceived'] > 0 && $employeeAR > $arr['totalReceived']) {
            $this->addErrorList($arrayToJs,false,$this->errorMsg['arEmployee'][4]); 
        }

		return $arrayToJs;
	 }
	  
    function getRefTableDetails($transactionType){
        //$truckingServiceWorkOrder = new TruckingServiceWorkOrder();
        //$truckingServiceOrder = new TruckingServiceOrder();
        
        $rsObj = $this->getTableNameAndObjById($transactionType);
        $obj = $rsObj['obj'];
        
        //update amount sesuai dengan object transaksinya
        // masing2 punya detail yg berbeda
        /*$amountField = 'amount'; 
        $requestAmountField = 'requestamount';
        $qtyField = '';
        
        switch($obj->tableName){
            case $truckingServiceWorkOrder->tableName : 
                    $costTable = $truckingServiceWorkOrder->tableCost; 
                    break;
            case $truckingServiceOrder->tableName : 
                    $costTable = $truckingServiceOrder->tableHeaderCost; 
                    $qtyField = 'qty';
                    break;
            default : $costTable = '';    
        }
         */
        
        return array(
                    'obj' => $obj,
                   /* 'costTable' => $costTable,
                    'amountField' => $amountField,
                    'requestAmountField' => $requestAmountField,
                    'qtyField' => $qtyField*/
                );
    }
    
    
  function changeTransactionStatus($id,$status,$reason='',$copy=false, $autoChangeStatus = false, $ignoreValidation = false){
          
             
        if (empty($_SESSION[$this->loginAdminSession]['id']))
            die;
          
        $rsHeader = $this->getDataRowById($id); 
         
      	try{ 
            // jika status bkn status sendiri dan bukan status terakhir (status cancel)
            //$this->setLog($this->tableName.' -> ' . $rsHeader[0]['statuskey'] .'=='. $status);
              
            if(!$autoChangeStatus){  
                $security = new Security();
                if(!$security->isAdminLogin($this->securityObject,$status,false))  
                    $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'.</strong> '.$this->errorMsg[252],true);   
            }

            if ($rsHeader[0]['statuskey'] == count($this->getAllStatus())) 
                $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'.</strong> '.$this->errorMsg[221],true);   
    
            if ($rsHeader[0]['statuskey'] == $status) 
                $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'.</strong> '.$this->errorMsg[224],true);   
   
        }catch(Exception $e){ 
 		     return $this->getErrorLog(); 
			//$this->addErrorList($arrayToJs,false,$e->getMessage());
		}		
				 

		try{ 
            
            // ================== VALIDATION
             
		  	switch ($status){
				case 1 : $this->validateInput($rsHeader); 
						  break;
                case 2 : if ($rsHeader[0]['statuskey'] < $status )
                            $this->validateValidation($rsHeader); 
						 else
							 $this->validateBackValidation($rsHeader);
						  break;
                case 3 : if ($rsHeader[0]['statuskey'] < $status )
                            $this->validateConfirm($rsHeader);
                         else
                            $this->validateBackConfirm($rsHeader); 
						  break;
				case 4 : $this->validateClose($rsHeader); 
						  break;
				case 5 : $this->validateCancel($rsHeader, $autoChangeStatus);
						  break; 
			} 
             
             
            //make sure we throw error 
            $this->throwIfHasErrorLog();  
             
            
            // ================== VALIDATION OK !
            
			if(!$this->oDbCon->startTrans())
				throw new Exception($this->errorMsg[100]);
					  
            
			switch ($status){ 
				case 2 : if ($rsHeader[0]['statuskey'] < $status ){ 
                            
                        }else{ 
                            $this->backValidationTrans($rsHeader);  
                        }
                         break; 
				case 3 : if ($rsHeader[0]['statuskey'] < $status ){ 
                            $this->confirmTrans($rsHeader); 
                            $this->afterConfirmTrans($rsHeader);
                        }else{ 
                            $this->backConfirmTrans($rsHeader); 
                            $this->afterBackConfirmTrans($rsHeader);
                        }
                         break; 
				case 4 : $this->closeTrans($rsHeader); 
                         $this->afterCloseTrans($rsHeader); 
                         break; 
				case 5 : $this->cancelTrans($rsHeader,$copy);
                         $this->afterCancelTrans($rsHeader);
                         break;  
			}
			
			$sql = 'update '.$this->tableName.' set statuskey = '.$this->oDbCon->paramString($status).' where pkey = ' . $this->oDbCon->paramString($id); 
            $this->oDbCon->execute($sql);  
             
            $this->setTransactionLog($status,$id,'',$reason);
            
            $this->afterStatusChanged($rsHeader);
                
			$this->oDbCon->endTrans();  
			$this->addErrorLog(true,$this->lang['dataHasBeenSuccessfullyUpdated']);   
		
	    } catch(Exception $e){ 
             
            $this->oDbCon->rollback(); 
            
            if (!empty($e->getMessage()))
                $this->addErrorLog(false,$e->getMessage());
			//$this->addErrorList($arrayToJs,false,$e->getMessage());
		}		
				 
        return $this->getErrorLog(); 
  }   
    
 	function validateBackValidation($rsHeader){
		$id = $rsHeader[0]['pkey'];
      
        $rsARKey = $this->getTableKeyAndObj($this->tableName, array('key')); 

        $arEmployee = new AREmployee();
        $rsAREmployee = $arEmployee->searchData($arEmployee->tableName.'.refkey',$rsHeader[0]['pkey'],true,' and '.$arEmployee->tableName.'.reftabletype = '.$this->oDbCon->paramString($rsARKey['key']).' and '.$arEmployee->tableName.'.statuskey in (2,3)');
        if(!empty($rsAREmployee))
            $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' . $this->errorMsg[201].' ' .$this->errorMsg['arEmployee'][2]);


        $apEmployee = new APEmployee();
        $rsAPEmployee = $apEmployee->searchData($apEmployee->tableName.'.refkey',$rsHeader[0]['pkey'],true,' and '.$apEmployee->tableName.'.reftabletype = '.$this->oDbCon->paramString($rsARKey['key']).' and '.$apEmployee->tableName.'.statuskey in (2,3)');
        if(!empty($rsAPEmployee))
            $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' . $this->errorMsg[201].' ' .$this->errorMsg['apEmployee'][2]);

        // gk boleh dipisah, untuk jaga2
        
       /* if($rsHeader[0]['employeear'] > 0){ 

        }else if ($rsHeader[0]['balance'] < 0){ 
          
        }  */
    } 

    function validateClose($rsHeader){
        if($rsHeader[0]['statuskey'] <> 3) 
            $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'.</strong> '.$this->errorMsg[204],true);   
    }
    
    function confirmTrans($rsHeader){ 
        
        // konfirmasi realisasi,
        // update SPK / JO atau Kas Keluar nilai realisasi
        // buat jurnal realisasi
        
        $employee = new Employee();
        $coaLink = new COALink();
        $warehouse = new Warehouse();
		$truckingCostCashOut = new TruckingCostCashOut();
         
        $id = $rsHeader[0]['pkey'];
        $refTransactionHeaderKey =  $rsHeader[0]['refkey2'];
        
         
        // cari dulu jenis transaksinya SPK atau JO, atau dari yg lain 
        $objDetails = $this->getRefTableDetails($rsHeader[0]['reftabletype2']);
        $obj = $objDetails['obj'];
        //$costTable = $objDetails['costTable'];
        //$amountField = $objDetails['amountField']; 
        
        $employeCOAKey = 0;
        if(!empty($rsHeader[0]['employeekey'])){ 
            $rsEmployee = $employee->getDataRowById($rsHeader[0]['employeekey']); 
            $employeCOAKey = $rsEmployee[0]['cashbankcoakey']; 
        } 
        
        $rsCOACashBank = $coaLink->getCOALink ('cashbankdriver', $warehouse->tableName, $rsHeader[0]['warehousekey'], 0);
        $coakey = (!empty($employeCOAKey)) ? $employeCOAKey : $rsCOACashBank[0]['coakey'];    
        $rsDetail = $this->getDetailById($id);
        
        foreach($rsDetail as $key=>$row){
            
            // utk update jurnal
            $rsDetail[$key]['coakey'] =  $coakey;
            
           /* if ($row['settlementtypekey'] == 1) 
                // dari kas keluar 
                $sql = 'update '.$costTable.' 
                        set   
                            '.$amountField.' = '.$this->oDbCon->paramString($row['realcostvalue']).', isrealization = 1
                        where 
                            '.$costTable.'.pkey = ' . $this->oDbCon->paramString($row['refkey2']);  

                $this->oDbCon->execute($sql);  
            else{
                // kalo ad biaya tambahan, insert ke biaya kas keluar 
                
            }*/
       
        } 
       
        // update tgl 
        //$timestampArr = $this->getDateUsedForTimestamp($this->tableName, $rsHeader); 
		//$rsHeader[0]['trdate'] = $timestampArr['timestamp']; 
         
        
        // update status kas keluar   
        $arrayToJs = $this->updateCashBankStatus($rsHeader,true); 
        if (!$arrayToJs[0]['valid'])
            throw new Exception('<strong>'.$rsHeader[0]['code'] . '</strong>. '.  $arrayToJs[0]['message']);    
        
        // update lainnya.... 
        $obj->updateDataAfterRealization($rsHeader,$rsDetail,1);   
        $this->addCashBank($rsHeader,$rsDetail); 
         
        if ($rsHeader[0]['balance'] < 0 ) 
            $this->addARAPEmployee($rsHeader,false);
        else if ($rsHeader[0]['employeear'] > 0 )  
            $this->addARAPEmployee($rsHeader,true);
        
        //update jurnal umum 
        //$this->setLog($rsHeader,true);
        $this->updateGL($rsHeader);
	} 
    
    function updateCashBankStatus($rsHeader,$isClosed){
        $arrayToJs = array();
        
        // update status kas keluar
        $cashOutObj = $this->getTableNameAndObjById($rsHeader[0]['reftabletype']);
        $cashOutObj = $cashOutObj['obj']; 
        
        $rsCashOut = $cashOutObj->getDataRowById($rsHeader[0]['refkey']);
//        $status = ($isClosed) ? TRANSACTION_STATUS['selesai'] : TRANSACTION_STATUS['konfirmasi'];
          $status = ($isClosed) ? 4 : 3;
        
        if ($rsCashOut[0]['statuskey'] != $status )
            $arrayToJs = $cashOutObj->changeStatus($rsHeader[0]['refkey'],$status,'',false,true,true);
        else
            $arrayToJs[0]['valid'] = true;
          
        return $arrayToJs;
      
    }
    
    function validateValidation($rsHeader){
        $id = $rsHeader[0]['pkey']; 
		$truckingCostCashOut = new TruckingCostCashOut();
        
               

        $rsCash = $this->searchData('','',false,' and '.$this->tableName.'.pkey <> '.$this->oDbCon->paramString($id).' and '.$this->tableName.'.statuskey in (2,3,4) and '.$this->tableName.'.reftabletype = '.$rsHeader[0]['reftabletype'].' and '.$this->tableName.'.refkey = '.$rsHeader[0]['refkey']);
        
        if(!empty($rsCash))
            $this->addErrorLog(false,'<strong>'.$rsCash[0]['code'].'</strong>. '.$this->errorMsg['cashBankRealization'][2]);
        

        // Cash Bank statusnya harus masih konfirmasi
        $rsTruckingCostCashOut = $truckingCostCashOut->getDataRowById($rsHeader[0]['refkey']);
        if (empty($rsTruckingCostCashOut) || $rsTruckingCostCashOut[0]['statuskey'] != 3)
            $this->addErrorLog(false,'<strong>'.$rsTruckingCostCashOut[0]['code'].'</strong>. '.$this->errorMsg[204]);
            	
        // coa settlement berdasarkan duit balik
		if ($rsHeader[0]['balance'] > 0 && empty($rsHeader[0]['settlementcoakey']))
            $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg['coa'][1]);
             
        
        $rsPayment = $this->getPaymentMethodDetail($id);
        
        $totalPayment = 0; 
        for($i=0;$i<count($rsPayment); $i++)
            $totalPayment += $rsPayment[$i]['amount'];
         
        $balance =  $rsHeader[0]['balance']; 
          
        /*$thresholdDiscount = abs($this->loadSetting('roundedPaymentThreshold'));
        if($balance < ($thresholdDiscount * -1)) 
            $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg[502]);
        else if ($balance > $thresholdDiscount)
            $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg[509]); 

        $rsCash = $this->searchData('','',false,'  and '.$this->tableName.'.pkey <> '.$this->oDbCon->paramString($id).' and '.$this->tableName.'.statuskey in (2,3,4) and reftabletype = '.$rsHeader[0]['reftabletype'].' and '.$this->tableName.'.refkey = '.$rsHeader[0]['refkey']);
        if(!empty($rsCash))
            $this->addErrorLog(false,'<strong>'.$rsCash[0]['code'].'</strong>. '.$this->errorMsg['cashBankRealization'][2]);
        */    
    }
    
	function validateConfirm($rsHeader){
        
        // cuma boleh satu realisasi untuk setiap kas keluar !! 
        $id = $rsHeader[0]['pkey']; 
		$truckingCostCashOut = new TruckingCostCashOut();
        
        $rsCash = $this->searchData('','',false,' and '.$this->tableName.'.pkey <> '.$this->oDbCon->paramString($id).' and '.$this->tableName.'.statuskey in (2,3,4) and '.$this->tableName.'.reftabletype = '.$rsHeader[0]['reftabletype'].' and '.$this->tableName.'.refkey = '.$rsHeader[0]['refkey']);
        
        if(!empty($rsCash))
            $this->addErrorLog(false,'<strong>'.$rsCash[0]['code'].'</strong>. '.$this->errorMsg['cashBankRealization'][2]);
  
        // Cash Bank statusnya harus masih konfirmasi
        $rsTruckingCostCashOut = $truckingCostCashOut->getDataRowById($rsHeader[0]['refkey']);
        if (empty($rsTruckingCostCashOut) || $rsTruckingCostCashOut[0]['statuskey'] != 3)
            $this->addErrorLog(false,'<strong>'.$rsTruckingCostCashOut[0]['code'].'</strong>. '.$this->errorMsg[204]);
            
        // coa settlement berdasarkan duit balik
		if ($rsHeader[0]['balance'] > 0 && empty($rsHeader[0]['settlementcoakey']))
            $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg['coa'][1]);
             
        
        
        $rsPayment = $this->getPaymentMethodDetail($id);
        
        $totalPayment = 0; 
        for($i=0;$i<count($rsPayment); $i++)
            $totalPayment += $rsPayment[$i]['amount'];
        
        $balance = $rsHeader[0]['balance']; 
         

        if ($rsHeader[0]['totalreceived'] <= 0 && $rsHeader[0]['employeear'] <> 0) {
            $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg['arEmployee'][5]);
        } else if ($rsHeader[0]['totalreceived'] > 0 && $rsHeader[0]['employeear'] > $rsHeader[0]['totalreceived']) {
            $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg['arEmployee'][4]);
        }

    }		
     
     
    function updateGL($rs){
        
        if (!USE_GL) return; 
        
        $warehouse = new Warehouse();
        $employee = new Employee();
        $generalJournal = new GeneralJournal();
        $coaLink = new COALink();
        $truckingServiceOrder = new TruckingServiceOrder();
        $cost = new Service(TRUCKING_SERVICE,1);
        
        $warehousekey = $rs[0]['warehousekey'];
          
        $desc = array();
        $employeCOAKey = 0;
        $employeARCOAKey = 0;
         
        array_push($desc,$this->ucFirst($this->lang['cashBankRealization'])); 
        array_push($desc,$rs[0]['refcode']);
        array_push($desc,$rs[0]['refcode2']);
        
        if(!empty($rs[0]['refcode3']))
            array_push($desc,$rs[0]['refcode3']);
        
        if(!empty($rs[0]['employeekey'])){ 
            $rsEmployee = $employee->getDataRowById($rs[0]['employeekey']); 
            $employeCOAKey = $rsEmployee[0]['cashbankcoakey'];
            $employeARCOAKey = $rsEmployee[0]['arcoakey']; 
        }
         
        if (!empty($rs[0]['trdesc']))
            array_push($desc, $rs[0]['trdesc']);
        
        $desc = implode(', ',$desc);
        $desc .= '.';
        
        // sudah diupdate dr confirm
        //$timestampArr = $this->getDateUsedForTimestamp($this->tableName, $rs);
        
        $rsKey = $generalJournal->getTableKeyAndObj($this->tableName);
		$arr = array();
		$arr['pkey'] = $generalJournal->getNextKey($generalJournal->tableName);
		$arr['code'] = 'xxxxx';
		$arr['refkey'] = $rs[0]['pkey'];
		$arr['refTableType'] = $rsKey['key'];
		$arr['trDate'] = $this->formatDBDate($rs[0]['trdate'],'d / m / Y');  
		$arr['trDesc'] = $desc; 
		$arr['selWarehouseKey'] = $rs[0]['warehousekey'];
		
        $temp = -1;
          
        $rsDetail = $this->getDetailById($rs[0]['pkey']); 
         
        //$rsCOAOperationalCost = $coaLink->getCOALink ('operationalcost', $warehouse->tableName, $warehousekey); 
        $rsCOACashBank = $coaLink->getCOALink ('cashbankdriver', $warehouse->tableName, $rs[0]['warehousekey'], 0);
        
        $employeCOAKey = (empty($employeCOAKey)) ?  $rsCOACashBank[0]['coakey'] : $employeCOAKey; 
 
        $rsJO = $truckingServiceOrder->getDataRowById($rs[0]['jokey']);
        
		for($i=0;$i<count($rsDetail);$i++){  
  
            $coakey =  $cost->getCostCOAKeyByJobCategory($rsDetail[$i]['costkey'],$rsJO[0]['categorykey'],$warehousekey) ;  //(!empty($rsItem[0]['costcoakey'])) ? $rsItem[0]['costcoakey'] : $rsCOAOperationalCost[0]['coakey']; 
            
            $temp++; 
            $arr['hidCOAKey'][$temp] = $coakey; 
            $arr['debit'][$temp] = $rsDetail[$i]['amount']; 
            $arr['credit'][$temp] = 0;
            $arr['trdescDetail'][$temp] = $rsDetail[$i]['description'];
             
        }
 	
        // kalo ad AP Employee
        if ($rs[0]['balance'] < 0){ 
            $rsCOAEmployeeAP = $coaLink->getCOALink ('employeeap', $warehouse->tableName, $warehousekey); 
            $employeAPCOAKey = (empty($employeAPCOAKey)) ?  $rsCOAEmployeeAP[0]['coakey'] : $employeAPCOAKey; 

            $temp++;  
            $arr['hidCOAKey'][$temp] = $employeAPCOAKey;
            $arr['debit'][$temp] = 0; 
            $arr['credit'][$temp] = abs($rs[0]['balance']);
            $arr['trdescDetail'][$temp] = $rs[0]['code']; 
        }
        
 
        // hutang / piutang karyawan
        if( $rs[0]['employeear'] > 0){ 
            $rsCOAEmployeeAR = $coaLink->getCOALink ('employeear', $warehouse->tableName, $warehousekey); 
            $employeARCOAKey = (empty($employeARCOAKey)) ?  $rsCOAEmployeeAR[0]['coakey'] : $employeARCOAKey; 

            $temp++; 
            $arr['hidCOAKey'][$temp] = $employeARCOAKey;
            $arr['debit'][$temp] = $rs[0]['employeear'];  
            $arr['credit'][$temp] = 0; 
            $arr['trdescDetail'][$temp] = $rs[0]['code']; 
        } 
 
		
		if( $rs[0]['balance'] > 0  ){ 

            $temp++; 
            $arr['hidCOAKey'][$temp] = $rs[0]['settlementcoakey'];
            $arr['debit'][$temp] = $rs[0]['balance'];  
            $arr['credit'][$temp] = 0; 
            $arr['trdescDetail'][$temp] = $rs[0]['code']; 
        } 
         
         // jika ad uang yg dikembalkan
        // pada kenyataannya, gk langsung balik duitnya di LOGOL. mungkin nanti bisa nyusul updatenya
        
        /*$temp++; 
        if ($rs[0]['balance'] < 0){ 
            $rsCOA = $coaLink->getCOALink ('othercost', $warehouse->tableName,$warehousekey, 0); 
            $arr['debit'][$temp] = abs($rs[0]['balance']); 
            $arr['credit'][$temp] = 0; 
        }else{  
            $rsCOA = $coaLink->getCOALink ('otherrevenue', $warehouse->tableName,$warehousekey, 0); 
            $arr['debit'][$temp] = 0; 
            $arr['credit'][$temp] = abs($rs[0]['balance']); 
        } 
        $arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];
        $arr['trdescDetail'][$temp] = $rs[0]['code'];  */
        
                                        
        $rsPayment = $this->getPaymentMethodDetail($rs[0]['pkey']);  
        for($i=0;$i<count($rsPayment); $i++){ 
             $rsCOA = $coaLink->getCOALink ('payment', $warehouse->tableName,$warehousekey,$rsPayment[$i]['paymentkey']); 
             $temp++;
             $arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];
             $arr['debit'][$temp] = $rsPayment[$i]['amount']; 
             $arr['credit'][$temp] = 0;  
             $arr['trdescDetail'][$temp] = $rs[0]['code'];  
        }
        		 
        // kas gantung
        $temp++; 
        $arr['hidCOAKey'][$temp] = $employeCOAKey;
        $arr['debit'][$temp] = 0;  
        $arr['credit'][$temp] = $rs[0]['total'];  
        $arr['trdescDetail'][$temp] = $rs[0]['code'];
		
        $arrayToJs = $generalJournal->addData($arr);
        
		if (!$arrayToJs[0]['valid'])
          throw new Exception('<strong>'.$rs[0]['code'] . '</strong>. '.$this->errorMsg[504].' '.$arrayToJs[0]['message']);    
 
    }
    
    /*function updateGL($rs){
        
        if (!USE_GL) return;
        
        $warehouse = new Warehouse();
        $employee = new Employee();
        $generalJournal = new GeneralJournal();
        $coaLink = new COALink();
        $cost = new Service(TRUCKING_SERVICE,1);
        
        $warehousekey = $rs[0]['warehousekey'];
          
        $desc = array();
        $employeCOAKey = 0;
        $employeARCOAKey = 0;
         
        array_push($desc,$this->ucFirst($this->lang['cashBankRealization'])); 
        array_push($desc,$rs[0]['refcode']);
        array_push($desc,$rs[0]['refcode2']);
        
        if(!empty($rs[0]['refcode3']))
            array_push($desc,$rs[0]['refcode3']);
        
        if(!empty($rs[0]['employeekey'])){ 
            $rsEmployee = $employee->getDataRowById($rs[0]['employeekey']); 
            $employeCOAKey = $rsEmployee[0]['cashbankcoakey'];
            $employeARCOAKey = $rsEmployee[0]['arcoakey']; 
        }
         
        if (!empty($rs[0]['trdesc']))
            array_push($desc, $rs[0]['trdesc']);
        
        $desc = implode(', ',$desc);
        $desc .= '.';
        
        // sudah diupdate dr confirm
        //$timestampArr = $this->getDateUsedForTimestamp($this->tableName, $rs);
        
        $rsKey = $generalJournal->getTableKeyAndObj($this->tableName);
		$arr = array();
		$arr['pkey'] = $generalJournal->getNextKey($generalJournal->tableName);
		$arr['code'] = 'xxxxx';
		$arr['refkey'] = $rs[0]['pkey'];
		$arr['refTableType'] = $rsKey['key'];
		$arr['trDate'] = $this->formatDBDate($rs[0]['trdate'],'d / m / Y');  
		$arr['trDesc'] = $desc; 
		
        $temp = -1;
          
        $rsDetail = $this->getDetailById($rs[0]['pkey']); 
         
        $rsCOAOperationalCost = $coaLink->getCOALink ('operationalcost', $warehouse->tableName, $warehousekey); 
        $rsCOACashBank = $coaLink->getCOALink ('cashbankdriver', $warehouse->tableName, $rs[0]['warehousekey'], 0);  
            
        //$this->setLog($employeCOAKey,true);
        //$this->setLog($rsCOACashBank[0]['coakey'],true);
        
        $employeCOAKey = (empty($employeCOAKey)) ?  $rsCOACashBank[0]['coakey'] : $employeCOAKey; 
 
		for($i=0;$i<count($rsDetail);$i++){  

            $rsItem = $cost->getDataRowById($rsDetail[$i]['costkey']);  
            $coakey = (!empty($rsItem[0]['costcoakey'])) ? $rsItem[0]['costcoakey'] : $rsCOAOperationalCost[0]['coakey']; 
            
            $temp++; 
            $arr['hidCOAKey'][$temp] = $coakey; 
            $arr['debit'][$temp] = $rsDetail[$i]['amount']; 
            $arr['credit'][$temp] = 0;
            $arr['trdescDetail'][$temp] = $rsDetail[$i]['description'];
             
        }
 	
        if ($rs[0]['balance'] <> 0){ 

            $rsCOACashBank = $coaLink->getCOALink ('cashbankops', $warehouse->tableName, $rs[0]['warehousekey'], 0);  
            $coaOpsCashKey = $rsCOACashBank[0]['coakey']; 
            
            $temp++;  
            $arr['hidCOAKey'][$temp] = $coaOpsCashKey;
            $arr['debit'][$temp] =$rs[0]['balance']; 
            $arr['credit'][$temp] =  0;  
            $arr['trdescDetail'][$temp] = $rs[0]['code']; 
        }
         
        $rsPayment = $this->getPaymentMethodDetail($rs[0]['pkey']);  
        for($i=0;$i<count($rsPayment); $i++){ 
             $rsCOA = $coaLink->getCOALink ('payment', $warehouse->tableName,$warehousekey,$rsPayment[$i]['paymentkey']); 
             $temp++;
             $arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];
             $arr['debit'][$temp] = $rsPayment[$i]['amount']; 
             $arr['credit'][$temp] = 0;  
             $arr['trdescDetail'][$temp] = $rs[0]['code'];  
        }
        		
        
 
        // piutang karyawan
        if( $rs[0]['employeear'] <> 0){ 
            $rsCOAEmployeeAR = $coaLink->getCOALink ('employeear', $warehouse->tableName, $warehousekey); 
            $employeARCOAKey = (empty($employeARCOAKey)) ?  $rsCOAEmployeeAR[0]['coakey'] : $employeARCOAKey; 

            $temp++; 
            $arr['hidCOAKey'][$temp] = $employeARCOAKey;
            $arr['debit'][$temp] = $rs[0]['employeear'];  
            $arr['credit'][$temp] = 0; 
            $arr['trdescDetail'][$temp] = $rs[0]['code']; 
        } 
         
         
        // kas gantung
        $temp++; 
        $arr['hidCOAKey'][$temp] = $employeCOAKey;
        $arr['debit'][$temp] = 0;  
        $arr['credit'][$temp] = $rs[0]['total'];  
        $arr['trdescDetail'][$temp] = $rs[0]['code'];  
         
        $arrayToJs = $generalJournal->addData($arr);
        
		if (!$arrayToJs[0]['valid'])
          throw new Exception('<strong>'.$rs[0]['code'] . '</strong>. '.$this->errorMsg[504].' '.$arrayToJs[0]['message']);    
 
    }*/
 
    function validateCancel($rsHeader,$autoChangeStatus=false){ 
        $truckingCostCashOut = new TruckingCostCashOut();
        //$truckingCostCashIn = new TruckingCostCashIn();
      
        $id = $rsHeader[0]['pkey'];
      
        $rsARKey = $this->getTableKeyAndObj($this->tableName); 
       
        $arEmployee = new AREmployee();
        $rsAREmployee = $arEmployee->searchData($arEmployee->tableName.'.refkey',$rsHeader[0]['pkey'],true,' and '.$arEmployee->tableName.'.reftabletype = '.$this->oDbCon->paramString($rsARKey['key']).' and '.$arEmployee->tableName.'.statuskey in (2,3)');
        if(!empty($rsAREmployee))
            $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' . $this->errorMsg[201].' ' .$this->errorMsg['arEmployee'][2]);


        $apEmployee = new APEmployee();
        $rsAPEmployee = $apEmployee->searchData($apEmployee->tableName.'.refkey',$rsHeader[0]['pkey'],true,' and '.$apEmployee->tableName.'.reftabletype = '.$this->oDbCon->paramString($rsARKey['key']).' and '.$apEmployee->tableName.'.statuskey in (2,3)');
        if(!empty($rsAPEmployee))
            $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' . $this->errorMsg[201].' ' .$this->errorMsg['apEmployee'][2]);

        // gk boleh dicancel utk jaga2
        /*if($rsHeader[0]['employeear'] > 0){ 

        }else if ($rsHeader[0]['balance'] < 0){ 
         
        }*/
      
    }
    
      
    function cancelTrans($rsHeader,$copy){  
        
		$id = $rsHeader[0]['pkey'];
		  	
		if ($rsHeader[0]['statuskey'] == 1) return; 
  
        $this->reverseConfirm($rsHeader);
        
		if ($copy)
			$this->copyDataOnCancel($id);	
        
	} 
    
    function cancelCashBank($id){
        if(!ADV_FINANCE) return;
        
        $cashBank = new CashBank();
        $rsCashOutBankKey = $this->getTableKeyAndObj($this->tableName);
        $rsCashBank = $cashBank->searchData('','',true,' and '.$cashBank->tableName.'.reftabletype = ' . $this->oDbCon->paramString($rsCashOutBankKey['key']) .' and '.$cashBank->tableName.'.refkey = ' . $this->oDbCon->paramString($id) .' and '.$cashBank->tableName.'.statuskey in (1,2,3) ');
        for($i=0;$i<count($rsCashBank);$i++) {
            $arrayToJs = $cashBank->changeStatus($rsCashBank[$i]['pkey'],TRANSACTION_STATUS['batal'],'',false,true);
            if (!$arrayToJs[0]['valid'])
                throw new Exception('<strong>'.$rsHeader[0]['code'] . '</strong>. '.  $arrayToJs[0]['message']);    
        }
    }
    
    function backValidationTrans($rsHeader){    
        $this->reverseConfirm($rsHeader); 
	} 
     
    
    function reverseConfirm($rsHeader){
		$id = $rsHeader[0]['pkey'];
        $refTransactionHeaderKey =  $rsHeader[0]['refkey2'];
        
        $rsARKey = $this->getTableKeyAndObj($this->tableName);  
        
        // gk boleh dipisah validasi, karena bisa saja dr minus ke plus atau sebaliknya
        $arEmployee = new AREmployee();
        $rsAR = $arEmployee->searchData('','',true,' and '.$arEmployee->tableName.'.reftabletype = '.$this->oDbCon->paramString($rsARKey['key']).' and '.$arEmployee->tableName.'.refkey = '.$this->oDbCon->paramString($id).' and '.$arEmployee->tableName.'.statuskey = 1');
        for($i=0;$i<count($rsAR);$i++) {
            $arrayToJs = $arEmployee->changeStatus($rsAR[$i]['pkey'],4,'',false,true);
            if (!$arrayToJs[0]['valid'])
                throw new Exception('<strong>'.$rsHeader[0]['code'] . '</strong>. '.  $arrayToJs[0]['message']);    
        }
        
        $apEmployee = new APEmployee(); 
        $rsAP = $apEmployee->searchData('','',true,' and '.$apEmployee->tableName.'.reftabletype = '.$this->oDbCon->paramString($rsARKey['key']).' and '.$apEmployee->tableName.'.refkey = '.$this->oDbCon->paramString($id).' and '.$apEmployee->tableName.'.statuskey = 1');
        for($i=0;$i<count($rsAP);$i++) {
            $arrayToJs = $apEmployee->changeStatus($rsAP[$i]['pkey'],4,'',false,true);
            if (!$arrayToJs[0]['valid'])
                throw new Exception('<strong>'.$rsHeader[0]['code'] . '</strong>. '.  $arrayToJs[0]['message']);    
        }
 
        
        // update status kas keluar
        // hanya jika statusnya SELESAI 
        $cashOutObj =  $this->getTableNameAndObjById($rsHeader[0]['reftabletype']);
        $cashOutObj = $cashOutObj['obj']; 
        $rsCashOut = $cashOutObj->getDataRowById($rsHeader[0]['refkey']);
        if (!empty($rsCashOut) && $rsCashOut[0]['statuskey'] == 4){  
            $arrayToJs = $cashOutObj->changeStatus($rsHeader[0]['refkey'],3,'',false,true,true);
            if (!$arrayToJs[0]['valid'])
                throw new Exception('<strong>'.$rsHeader[0]['code'] . '</strong>. '.  $arrayToJs[0]['message']); 
        }
         
        // cari dulu jenis transaksinya SPK atau JO, atau dari yg lain
        $objDetails = $this->getRefTableDetails($rsHeader[0]['reftabletype2']);
        $obj = $objDetails['obj'];
        /*$costTable = $objDetails['costTable'];
        $amountField = $objDetails['amountField'];
        $requestAmountField = $objDetails['requestAmountField'];
        $qtyField = $objDetails['qtyField'];
        */
        $rsDetail = $this->getDetailById($id);
        
        /*foreach($rsDetail as $row){
            if(empty($row['refkey2'])) continue;
     
            $sql = 'update 
                        '.$costTable.' 
                    set 
                        '.$amountField.' = 0,  isrealization = 0
                    where 
                        '.$costTable.'.pkey = ' . $this->oDbCon->paramString($row['refkey2']);
            $this->oDbCon->execute($sql); 
        }*/
         
        $arrayToJs = $this->updateCashBankStatus($rsHeader,false); 
        if (!$arrayToJs[0]['valid'])
            throw new Exception('<strong>'.$rsHeader[0]['code'] . '</strong>. '.  $arrayToJs[0]['message']);  
        
        
        $this->cancelCashBank($id); 
        $obj->updateDataAfterRealization($rsHeader,$rsDetail,2);    
        $this->cancelGLByRefkey($rsHeader[0]['pkey'],$this->tableName);

    }
    
    function reCountGrandtotal($arrParam){

				$grandtotal = 0;
				$costValue = 0;
				$realCostValue = 0;
				
				$arrCostKey = $arrParam['hidCostKey'];
				$arrCostValue = $arrParam['costValue']; 
				$arrRealCostValue = $arrParam['realCostValue']; 
				$arrAmount = $arrParam['amount']; 
				$qty = $arrParam['qty']; 
				
				$arrARDetail = array();
               			$arrItemDetail = array();
				$aR = new AR();
				
				for ($i=0;$i<count($arrCostKey);$i++){
					
				    $arrCostValue[$i] = $this->unFormatNumber($arrCostValue[$i]);
				    $arrRealCostValue[$i] = $this->unFormatNumber($arrRealCostValue[$i]);
				    $arrAmount[$i] = $this->unFormatNumber($arrAmount[$i]);
				    $qty[$i] = $this->unFormatNumber($qty[$i]);
                    
					if ( empty($arrCostKey[$i]) )  continue;
					

                    
                    			$arrItemDetail[$i]['realCostValue'] =($arrAmount[$i] / $qty[$i]);
					//$realCostValue +=  ($arrRealCostValue[$i] * $qty[$i]);
					$realCostValue +=  ($arrAmount[$i]);
					$costValue +=  ($arrCostValue[$i] * $qty[$i]);
				} 
        
                $totalReceived = $costValue - $realCostValue;
                    
                $totalPayment = 0; 
				$payment = $arrParam['paymentMethodValue'];
				for($i=0;$i<count($payment);$i++){
					$totalPayment += $this->unFormatNumber($payment[$i]);
				} 
        
        
				$employeeAR = $this->unFormatNumber($arrParam['employeeAR']); 
				$balance = $totalPayment - $totalReceived + $employeeAR;
				$balance *= -1;
        
                $reCountResult = array();
                $reCountResult['total'] = $costValue; 
                $reCountResult['totalrealization'] = $realCostValue; 
                $reCountResult['totalreceived'] = $totalReceived;
                $reCountResult['totalpayment'] = $totalPayment;
                $reCountResult['detailAmount'] = $arrItemDetail;
                $reCountResult['balance'] = $balance;

				
				return $reCountResult;
				
	}
     
    function normalizeParameter($arrParam, $trim=false){
        $arrParam['islinked'] = (isset($arrParam['islinked'])) ? $arrParam['islinked'] : 0; 
        $arrParam['trDesc'] = (isset($arrParam['trDesc'])) ? $arrParam['trDesc'] : '';  
         
        $arrParam['employeeAR'] = (isset($arrParam['employeeAR'])) ? $arrParam['employeeAR'] : 0;  // utk perhitungan reCount
        $arrParam['paymentMethodValue'] = (isset($arrParam['paymentMethodValue'])) ? $arrParam['paymentMethodValue'] : array();  // utk perhitungan reCount
            
        // TODO : data dibawah ini harus narik ulang dr data transaksi
        $arrParam['hidEmployeeKey'] = (isset($arrParam['hidEmployeeKey'])) ? $arrParam['hidEmployeeKey'] : '';  
        
        //kalau balance tidak sama dengan 0 maka coakey di set menjadi 0
        $balance = $this->unFormatNumber($arrParam['balance']);
        $arrParam['hidCOASettlementKey'] = ($balance > 0) ? $arrParam['hidCOASettlementKey'] : 0;  
        // dari kas bank    
        //$arrObj = array();
        //array_push($arrObj,new CashOut()); 
        // belum tentu kepake skrg..., hati2 kalo nambahin class baru, harus nambah di beberapa tmp jg utk require_once nya
        // kemungkinan gk kepake lg, diset di cash advance saja
        
        //array_push($arrObj,new TruckingCostCashOut());
          
        $truckingCostCashOut = new TruckingCostCashOut();
        
        $rs = $truckingCostCashOut->searchDataRow(array( $truckingCostCashOut->tableName.'.reftabletype', $truckingCostCashOut->tableName.'.jokey', $truckingCostCashOut->tableName.'.wokey', $truckingCostCashOut->tableName.'.customerkey', $truckingCostCashOut->tableName.'.consigneekey'),
                                  ' and '.$truckingCostCashOut->tableName.'.code = ' . $this->oDbCon->paramString($arrParam['refCode'])
                                 );
 
        if (!empty($rs)){
 
            // harus normalize, karena user bisa narik ulang utk realisasi

            $arrParam['hidCustomerKey'] = $rs[0]['customerkey'];
            $arrParam['hidConsigneeKey'] = $rs[0]['consigneekey'];
            $arrParam['hidJOKey'] = $rs[0]['jokey']; 
            $arrParam['hidWOKey'] = $rs[0]['wokey'];

            $rsCashOutKey = $this->getTableKeyAndObj($truckingCostCashOut->tableName,array('key')); 
            $arrParam['hidRefTable'] = $rsCashOutKey['key'];

            if(isset($rs[0]['reftabletype']))
                $arrParam['hidRefTable2'] = $rs[0]['reftabletype'];
 
        }

        /*foreach($arrObj as $obj){
            
            $rs = $obj->searchData($obj->tableName.'.code', $arrParam['refCode']);
            
            // kalo for nya ud lebih dr 1, harus ad validasi
            $arrParam['hidCustomerKey'] = $rs[0]['customerkey'];
            
            if (!empty($rs)){
            
                // harus normalize, karena user bisa narik ulang utk realisasi
          
                $arrParam['hidJOKey'] = $arrParam['hidRefKey2']; 
                $arrParam['hidWOKey'] = $arrParam['hidRefKey']; 

                $rsARKey = $this->getTableKeyAndObj($obj->tableName); 
                $arrParam['hidRefTable'] = $rsARKey['key'];
                
                if(isset($rs[0]['reftabletype']))
                    $arrParam['hidRefTable2'] = $rs[0]['reftabletype'];
                
                break;
            }
        }*/
        
        $reCountResult = $this->reCountGrandtotal($arrParam);   
        $arrParam['total'] = $reCountResult['total'];
        $arrParam['totalRealization'] = $reCountResult['totalrealization'];
        
        // total yg harus dibayarkan
        $arrParam['totalReceived'] = $reCountResult['totalreceived'];
        $arrParam['totalPayment'] = $reCountResult['totalpayment'];
        $arrParam['balance'] = $reCountResult['balance'];
        $arrParam['detailAmount'] = $reCountResult['detailAmount'];
         
        
        for($i=0;$i<count($arrParam['hidCostKey']);$i++){
           $arrParam['realCostValue'][$i] = $arrParam['detailAmount'][$i]['realCostValue'];
           $arrParam['selSettlement'][$i] =  (isset($arrParam['selSettlement'][$i])) ? $arrParam['selSettlement'][$i] : 1; 
           $arrParam['detailDesc'][$i] =  (isset($arrParam['detailDesc'][$i])) ? $arrParam['detailDesc'][$i] : ''; 
        } 
        
        return $arrParam;
    }
 
        
	function editData($arrParam){ 
        /*// biar gk berubah tipenya  
          // gk perlu unset lg karena sudah dicek di normalize
          
		unset( $this->arrData['reftabletype']);  
		unset( $this->arrData['reftabletype2']);  */
        
        return parent::editData($arrParam);
	}
     
        
    function getDetailWithRelatedInformation($pkey,$criteria=''){
   
      $sql = 'select
	   			'.$this->tableNameDetail .'.*,
                ('.$this->tableNameDetail .'.costvalue - '.$this->tableNameDetail .'.realcostvalue) as balance,
                '.$this->tableItem.'.name as costname 
			  from
			  	'. $this->tableNameDetail .',
                '.$this->tableItem.' 
			  where
			  	' . $this->tableNameDetail .'.costkey = '.$this->tableItem.'.pkey and
			  	refkey in('.$this->oDbCon->paramString($pkey,',').')'; 
       
        $sql .= $criteria; 
   
        return $this->oDbCon->doQuery($sql);
   }
     

   function addARAPEmployee($rsHeader, $isAR){
            $arEmployee = new AREmployee();  
            $apEmployee = new APEmployee();  
            $employee = new Employee();   
            $truckingCostCashOut = new TruckingCostCashOut();
            $truckingServiceOrder = new TruckingServiceOrder();
            $truckingServiceWorkOrder = new TruckingServiceWorkOrder();
            $totalAP = 0;
            $warehousekey =  $rsHeader[0]['warehousekey'];

            $amount = ($isAR) ? $rsHeader[0]['employeear'] :  $rsHeader[0]['balance'];
            //if ($amount <= 0 ) return;
        
            $desc = array();
            array_push($desc, $this->ucFirst($this->lang['cashBankRealization']) .' '. $rsHeader[0]['code']. ' ' .strtolower($this->lang['for']) .' ' .$rsHeader[0]['refcode']); 
            $desc = implode('. ', $desc);
            $desc .= '.';
            $rsSOKey = $truckingServiceOrder->getTableKeyAndObj($truckingServiceOrder->tableName,array('key')); 
            $rsCashOut = $truckingCostCashOut->getDataRowById($rsHeader[0]['refkey']); 
            $refCashOutKey = $rsCashOut[0]['pkey'];
            $refWOKey ='';
            $refSOKey ='';
            $refCustomerKey ='';
            if($rsCashOut[0]['reftabletype']==$rsSOKey['key']){
                $rsSO = $truckingServiceOrder->getDataRowById($rsCashOut[0]['refkey']);
                $refSOKey = $rsSO[0]['pkey'];
                $refCustomerKey = $rsSO[0]['customerkey'];
            }else{
                $rsWO = $truckingServiceWorkOrder->getDataRowById($rsCashOut[0]['refkey']);
                $rsSO = $truckingServiceOrder->getDataRowById($rsWO[0]['refkey']);
                $refSOKey = $rsSO[0]['pkey'];
                $refWOKey = $rsWO[0]['pkey'];
                $refCustomerKey = $rsSO[0]['customerkey']; 
            }
       
            $top = 0; 
            $rsARKey = $this->getTableKeyAndObj($this->tableName,array('key')); 
            $arrParam = array();	

            $arrParam['code'] = 'xxxxxx';
            $arrParam['hidEmployeeKey'] = $rsHeader[0]['employeekey']; 
            $arrParam['hidRefKey'] = $rsHeader[0]['pkey']; // refkey realisasi
            $arrParam['hidRefCode'] =  $rsHeader[0]['code']; // kode realisasi
            $arrParam['hidRefKey2'] = $rsHeader[0]['refkey'];  // refkey kas keluar
            $arrParam['hidRefCode2'] = $rsHeader[0]['refcode']; // kode kas keluar
            $arrParam['hidRefHeaderKey'] = $rsHeader[0]['pkey'];
            $arrParam['hidRefDate'] =   $this->formatDBDate($rsHeader[0]['trdate'],'d / m / Y'); 
            $arrParam['hidRefTable'] = $rsARKey['key'];
            $arrParam['amount'] =  abs($amount);
            $arrParam['trDesc'] = $desc;
            $arrParam['trDate'] =  $this->formatDBDate($rsHeader[0]['trdate'],'d / m / Y');  
            $date = new DateTime($rsHeader[0]['trdate']);
            $date->add(new DateInterval('P'.$top.'D'));
            $arrParam['dueDate'] = $date->format('d / m / Y'); 
            $arrParam['overwriteGL'] = 1;
            $arrParam['islinked'] = 1; 
            $arrParam['selWarehouse'] = $warehousekey;
            $arrParam['hidRefCustomerKey'] = $refCustomerKey;
            $arrParam['hidRefCashOutKey'] = $refCashOutKey;
            $arrParam['hidRefSOKey'] = $refSOKey;
            $arrParam['hidRefWOKey'] = $refWOKey;

            if(($isAR)){ 
                $arrParam['selARType'] = AR_EMPLOYEE_TYPE['cashBankRealization'];
                $arrayToJs = $arEmployee->addData($arrParam);
            }else{ 
                $arrParam['selAPType'] = AR_EMPLOYEE_TYPE['cashBankRealization'];
                $arrayToJs = $apEmployee->addData($arrParam);
            }
       
            if (!$arrayToJs[0]['valid'])
                throw new Exception('<strong>'.$rsHeader[0]['code'] . '</strong>. '.$this->errorMsg[201].' '.$arrayToJs[0]['message']);
 
    }
     
    function addCashBank($rsHeader,$rsDetail){
        if(!ADV_FINANCE) return;
        //if(empty($rsDetail)) return;
        
        $cashBank = new CashBank();
        $truckingCostCashOut = new TruckingCostCashOut(); 
         
        $warehouse = new Warehouse();
        $coaLink = new COALink();
	    $employee = new Employee();
        
        $rsEmployee = $employee->getDataRowById($rsHeader[0]['employeekey']);
        $employeCOAKey = $rsEmployee[0]['cashbankcoakey']; 
        
        $rsCOACashBank = $coaLink->getCOALink ('cashbankdriver', $warehouse->tableName, $rsHeader[0]['warehousekey'], 0);   
        $employeCOAKey = (empty($employeCOAKey)) ?  $rsCOACashBank[0]['coakey'] : $employeCOAKey; 
         
        $rsTableKey = $this->getTableKeyAndObj($this->tableName,array('key'));
          
        //$arrCoa = $truckingCostCashOut->sumArrayColumnGroup($rsDetail,'coakey',array('amount'));
       
        $arrParam = array();
        $arrParam['code'] = 'xxxxxx';
        $arrParam['hidRefKey'] = $rsHeader[0]['pkey'];
        $arrParam['refCode'] = $rsHeader[0]['code'];
        $arrParam['trDate'] = $this->formatDBDate($rsHeader[0]['trdate'],'d / m / Y');
        $arrParam['trDesc'] = '';
        $arrParam['selWarehouseKey'] = $rsHeader[0]['warehousekey'];
        $arrParam['recipientName'] = $rsEmployee[0]['name'];
        $arrParam['reftabletype'] = $rsTableKey['key'];
        $arrParam['selTransactionTypeKey'] = $rsTableKey['key'];
        $arrParam['hidCOAHeaderKey'] = $employeCOAKey;
        $arrParam['amount'] =  -$rsHeader[0]['total'];
        $arrParam['outstanding'] =  0 ; 
        $arrParam['overwriteGL'] = 1;
        $arrParam['islinked'] = 1;

        $arrayToJs = $cashBank->addData($arrParam);
        if (!$arrayToJs[0]['valid'])
            $this->addErrorLog(false, '<strong>'.$rsHeader[0]['code'] . '</strong>. '.$this->errorMsg[201].' '.$arrayToJs[0]['message'], true); 

        $cashBank->changeStatus($arrayToJs[0]['data']['pkey'],TRANSACTION_STATUS['konfirmasi'],'',false,true);

            
         
        // kalo masih ad sisa balance
        if($rsHeader[0]['balance'] <> 0){ 
            
            //$coaOpsCashKey by default diambil dr coa cash bank operasional
            
            //$rsEmployee = $employee->getDataRowById($rsHeader[0]['employeekey']);
            //$employeCOAKey = $rsEmployee[0]['cashbankcoakey'];
            $rsCOACashBank = $coaLink->getCOALink ('cashbankops', $warehouse->tableName, $rsHeader[0]['warehousekey'], 0);  
            $coaOpsCashKey = $rsCOACashBank[0]['coakey']; 
 
            
            $arrParam = array();
            $arrParam['code'] = 'xxxxxx';
            $arrParam['hidRefKey'] = $rsHeader[0]['pkey'];
            $arrParam['refCode'] = $rsHeader[0]['code'];
            $arrParam['trDate'] = $this->formatDBDate($rsHeader[0]['trdate'],'d / m / Y');
            $arrParam['trDesc'] = '';
            $arrParam['selWarehouseKey'] = $rsHeader[0]['warehousekey'];
            $arrParam['recipientName'] = $rsEmployee[0]['name'];
            $arrParam['reftabletype'] = $rsTableKey['key'];
            $arrParam['selTransactionTypeKey'] = $rsTableKey['key'];
            $arrParam['hidCOAHeaderKey'] = $coaOpsCashKey;
            $arrParam['amount'] =  $rsHeader[0]['balance'] ;
            $arrParam['outstanding'] = 0;
            $arrParam['overwriteGL'] = 1; 
            $arrParam['islinked'] = 1;
             
            $arrayToJs = $cashBank->addData($arrParam);
            
            if (!$arrayToJs[0]['valid'])
                $this->addErrorLog(false, '<strong>'.$rsHeader[0]['code'] . '</strong>. '.$this->errorMsg[201].' '.$arrayToJs[0]['message'], true); 
 
            $cashBank->changeStatus($arrayToJs[0]['data']['pkey'],TRANSACTION_STATUS['konfirmasi'],'',false,true); 
 
        }

    }
        
	function delete($id,$forceDelete = false,$reason = ''){ 
         $arrayToJs = $this->changeStatus($id, 5,'',false,$forceDelete);  
         return $arrayToJs; 
	}
    
    
    function getRealizationDashboardSummary(){
        $arrReturn = array();
        $arEmployee = new AREmployee();
        $apEmployee = new APEmployee();
        
        // tarik total ar employee
        $sql = 'select coalesce(sum('.$arEmployee->tableName.'.outstanding),0) as aroutstanding from '.$arEmployee->tableName.' where statuskey in (1,2) ';
        $rsAR = $this->oDbCon->doQuery($sql); 
        array_push($arrReturn, array('label' => $this->lang['employeeAR'], 'amount' => $rsAR[0]['aroutstanding']));
        
        // tarik total ap employee
        $sql = 'select coalesce(sum('.$apEmployee->tableName.'.outstanding),0) as apoutstanding from '.$apEmployee->tableName.' where statuskey in (1,2) ';
        $rsAP = $this->oDbCon->doQuery($sql); 
        array_push($arrReturn, array('label' => $this->lang['employeeAP'], 'amount' => $rsAP[0]['apoutstanding'] * -1));

        // tarik sisa yg blm direalisasi
        $sql = 'select coalesce(sum('.$this->tableName.'.total),0) as realizationoutstanding from '.$this->tableName.' where statuskey in (1,2) ';
        $rsRealization = $this->oDbCon->doQuery($sql); 
        array_push($arrReturn, array('label' => $this->lang['cashAdvance'], 'amount' => $rsRealization[0]['realizationoutstanding']));
 
        return $arrReturn;
    }
    
}
?>
