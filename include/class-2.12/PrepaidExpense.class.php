<?php
  
class PrepaidExpense extends BaseClass{ 
 
   function __construct(){
		
		parent::__construct();
       
		$this->tableName = 'prepaid_expense'; 
		$this->tableJobOrder = 'emkl_job_order_header'; 
		$this->tableStatus = 'ar_status';
        $this->tableCurrency = 'currency';
        $this->tableItem = 'item';
        $this->tableService = 'item';
        $this->tableCOA = 'chart_of_account';
        $this->tableWarehouse = 'warehouse';   
        $this->isTransaction = true; 
		
        $this->securityObject = 'PrepaidExpense'; 
 
        $this->arrData = array();  
        $this->arrData['pkey'] = array('pkey');
        $this->arrData['code'] = array('code');
        $this->arrData['refkey'] = array('hidRefKey');
        $this->arrData['refcode'] = array('hidRefCode');
        $this->arrData['reftabletype'] = array('reftabletype');
        $this->arrData['warehousekey'] = array('selWarehouseKey');
        $this->arrData['salesorderkey'] = array('hidJobOrderKey');
        $this->arrData['salesorderheaderkey'] = array('hidJobOrderHeaderKey');
        $this->arrData['refsalesordertabletype'] = array('refsalesordertabletype');
        $this->arrData['trdate'] = array('trDate','date');
        $this->arrData['costkey'] = array('hidCostKey');
        $this->arrData['trdesc'] = array('trDesc');
        $this->arrData['amount'] = array('amount','number');
        $this->arrData['amountidr'] = array('amountIDR','number');
        $this->arrData['outstanding'] = array('outstanding','number');
        $this->arrData['islinked'] = array('islinked'); 
        $this->arrData['overwriteGL'] = array('overwriteGL'); 
        $this->arrData['currencykey'] = array('selCurrency'); 
        $this->arrData['statuskey'] = array('selStatus');   
        $this->arrData['rate'] = array('currencyRate','number'); 
        $this->arrData['amortizationvalue'] = array('amortizationValue','number');  
        $this->arrData['amortizationaging'] = array('amortizationAging','number');  
        $this->arrData['isreimburse'] = array('chkIsReimburse');
           
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'date','title' => 'date','dbfield' => 'trdate','default'=>true, 'width' => 100, 'align' =>'center', 'format' => 'date'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'refcode2','title' => 'reference','dbfield' => 'refcode','default'=>true, 'width' => 100,));
        array_push($this->arrDataListAvailableColumn, array('code' => 'refcode','title' => 'soCode','dbfield' => 'jocode','default'=>true, 'width' => 100,));
        array_push($this->arrDataListAvailableColumn, array('code' => 'servicename','title' => 'service','dbfield' => 'servicename','default'=>true, 'width' => 150,));
        array_push($this->arrDataListAvailableColumn, array('code' => 'currencyShort','title' => 'currencyShort','dbfield' => 'currencyname', 'width' => 60, 'align' =>'center' ));        
        array_push($this->arrDataListAvailableColumn, array('code' => 'rate','title' => 'currencyRate','dbfield' => 'rate', 'width' => 80, 'align' =>'right' , 'format' => 'number'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'amount','title' => 'amount','dbfield' => 'amount','default'=>true, 'width' => 100,'align' =>'right','format' => 'number'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'outstanding','title' => 'outstanding','dbfield' => 'outstanding','default'=>true,  'width' => 100,'align' =>'right','format' => 'number'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'warehouse','title' => 'warehouse','dbfield' => 'warehousename',  'width' => 120));        
        array_push($this->arrDataListAvailableColumn, array('code' => 'desc','title' => 'note','dbfield' => 'trdesc', 'width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
        
        $this->printMenu = array();
//        array_push($this->printMenu,array('code' => 'print', 'name' => $this->lang['printTransaction'],  'icon' => 'print', 'url' => 'print/cashBankVoucher'));
            
      
        $this->includeClassDependencies(array(
            'ChartOfAccount.class.php',
            'Warehouse.class.php',  
            'Service.class.php',  
            'Currency.class.php',  
            'CostReconsile.class.php',  
            'EMKLPurchaseOrder.class.php',  
            'EMKLJobOrder.class.php',  
            'EMKLOrderInvoice.class.php',  
            'Amortization.class.php',
            'PurchaseOrder.class.php'
        ));  
        
       
        $this->overwriteConfig();
   }
   
   function getQuery(){
	   
       $sql = '
			SELECT '.$this->tableName.'.* , 
               '.$this->tableJobOrder.'.code as jocode,
               '.$this->tableWarehouse.'.name as warehousename,
               '.$this->tableCurrency.'.name as currencyname,
               '.$this->tableService.'.name as servicename,
			   '.$this->tableStatus.'.status as statusname
			FROM '.$this->tableStatus.','.$this->tableWarehouse.', '.$this->tableName.' 
                left join '.$this->tableJobOrder.' on ' . $this->tableJobOrder .'.pkey = ' . $this->tableName .'.salesorderkey 
                left join '.$this->tableCurrency.' on ' . $this->tableCurrency .'.pkey = ' . $this->tableName .'.currencykey 
                left join '.$this->tableService.' on ' . $this->tableService .'.pkey = ' . $this->tableName .'.costkey 
			WHERE 
                '.$this->tableName . '.warehousekey = '.$this->tableWarehouse.'.pkey and 
                '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey  
 	  ' .$this->criteria ; 
       
 
       return $sql;
		 
    }
        
    function afterStatusChanged($rsHeader){   

        
      
        
    }
    
    function changeStatus($id,$status,$reason='',$copy=false,$autoChangeStatus=false, $dontValidate = false){
		
		$arrayToJs = array();
		  
		  try{ 
			     $rs = $this->getDataRowById($id);
              
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
 
            $rsStatus = $this->getStatusById ($status); 
            $this->setTransactionLog($rsStatus[0]['pkey'],$id);	
               
            $this->afterStatusChanged($rs);
            
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
		 $arrayToJs = $this->changeStatus($id, 4);   // harus ad validasi kalo islinked, gk boleh dihapus
		 return $arrayToJs; 

	}
    
    
    // ============= MAKE SURE USER CANNOT MANUAL UPDATE STATUS
	 function validateOpen($id){ 
		$arrayToJs = array(); 
		$rs  = $this->getDataRowById($id);
		$this->addErrorList($arrayToJs,false,'<strong>'.$rs[0]['code'].'</strong>. ' . $this->errorMsg[201]);
		return $arrayToJs;
	 } 	
    
    
	 function validatePartial($id){ 
		$arrayToJs = array(); 
		$rs  = $this->getDataRowById($id);
		$this->addErrorList($arrayToJs,false,'<strong>'.$rs[0]['code'].'</strong>. ' . $this->errorMsg['prepaidExpense'][3]);
     	return $arrayToJs;
	 } 	
    
    function validateClosed($id){ 
         
        $arrayToJs = array(); 
		$rs  = $this->getDataRowById($id);
		$this->addErrorList($arrayToJs,false,'<strong>'.$rs[0]['code'].'</strong>. ' . $this->errorMsg[201]);
		return $arrayToJs;
      
	 } 	 
    // ============= MAKE SURE USER CANNOT MANUAL UPDATE STATUS
    
    function validateCancel($id,$autoChangeStatus=false){ 
         // perlu cek validasi lg kalo ad payment yg sudah dikonfirmasi bagaimana ?
        // atau gk perlu selama statusnya tdk open 
          
		$arrayToJs = array(); 
		$rs  = $this->getDataRowById($id);
           
        if ( !$autoChangeStatus ) {
            if(!empty($rs[0]['islinked'])) 
                $this->addErrorList($arrayToJs,false,'<strong>'.$rs[0]['code'].'</strong>. ' . $this->errorMsg['prepaidExpense'][4]);    
        } 
         
        // transaksi tetep tidak boleh dibatalkan jika sudah ad pembayaran (status AR <> open)  
        // meskipun transaksi manual atau transaksi dr sales order 
        if ( $rs[0]['statuskey'] <> 1) 
                $this->addErrorList($arrayToJs,false,'<strong>'.$rs[0]['code'].'</strong>. ' .$this->errorMsg[201]);     
              
		return $arrayToJs;
	 } 	
    
     function searchDataForAutoComplete($fieldname='',$searchkey='',$mustmatch=false,$searchCriteria='',$orderCriteria='', $limit=''){
         
		$sql = 'select
					'.$this->tableName. '.pkey,     
                    '.$this->tableName.'.code as value , 
                    '.$this->tableName.'.code as code , 
                    '.$this->tableName.'.refcode, 
                    '.$this->tableName.'.refkey,
                    '.$this->tableName.'.costkey,
                    '.$this->tableService.'.name as servicename,
                    '.$this->tableName. '.amount,  
                    '.$this->tableName. '.currencykey,  
                    '.$this->tableName. '.outstanding
				from 
					'.$this->tableName . '
                        left join '.$this->tableService.' on ' . $this->tableName .'.costkey = ' . $this->tableService .'.pkey,
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
    
    function updateRefkey($pkey,$refheaderkey){

        if(empty($refheaderkey)) return;
        $emklPurchaseOrder = new EMKLPurchaseOrder();
        $emklJobOrder = new EMKLJobOrder();
         
        $rsJO = $emklJobOrder->searchDataRow( array($emklJobOrder->tableName.'.pkey'), 
                                              ' and  '. $emklJobOrder->tableName.'.headerorderkey = '. $this->oDbCon->paramString($refheaderkey).' and 
                                                     '. $emklJobOrder->tableName.'.statuskey in ('.TRANSACTION_STATUS['menunggu'].','.TRANSACTION_STATUS['konfirmasi'].','.TRANSACTION_STATUS['selesai'].')'    
                                );
         
        if(empty($rsJO)) return;
        
                
        $rsPO = $emklPurchaseOrder->searchDataRow( array($emklPurchaseOrder->tableName.'.pkey'), 
                                              ' and  '. $emklPurchaseOrder->tableName.'.pkey = '. $this->oDbCon->paramString($pkey).' and 
                                                     '. $emklPurchaseOrder->tableName.'.statuskey in ('.TRANSACTION_STATUS['menunggu'].','.TRANSACTION_STATUS['konfirmasi'].','.TRANSACTION_STATUS['selesai'].')'    
                                ); 
        if(empty($rsPO)) return;
 
        $rsDetail = $emklPurchaseOrder->getDetailById($rsPO[0]['pkey']);
        for($i=0;$i<count($rsDetail);$i++){
            
            $sql = 'update '.$this->tableName.' 
                    set  '.$this->tableName.'.salesorderkey = '.$rsJO[0]['pkey'].' 
                    where 
                        '.$this->tableName.'.refkey = '.$this->oDbCon->paramString($rsDetail[$i]['refkey']).' and 
                        '.$this->tableName.'.costkey = '.$this->oDbCon->paramString($rsDetail[$i]['servicekey']).'   
                    ';

            $this->oDbCon->execute($sql);
        }
         
    }
    
	function updateOutstanding($reconsilekey){
	    $reconsileObj = $this->getCostReconsileObj(); 
		$rsReconsile = $this->getDataRowById($reconsilekey);
        
        // kalo statusnya sudah batal tdk boleh dibalikin lg.
        // case karena terakhir update AP dicancel gk otomatis cancel payment
        if($rsReconsile[0]['statuskey'] == 4) return;
        
		$sql = 'select 
						coalesce(sum('.$reconsileObj->tableNameDetail.'.amount),0) as totalPaidAmount
				 from 
				 	' . $reconsileObj->tableName.','.$reconsileObj->tableNameDetail. '
				 where ' . $reconsileObj->tableNameDetail.'.refkey = '.$reconsileObj->tableName .'.pkey and 
				 	  ('.$reconsileObj->tableName .'.statuskey = 2 or '.$reconsileObj->tableName .'.statuskey = 3 )and
					  '.$reconsileObj->tableNameDetail.'.refreconsilekey = '.$reconsileObj->oDbCon->paramString($reconsilekey).'
				'  ;
         
		$rsAmount =  $this->oDbCon->doQuery($sql); 
		$totalPaidAmount = $rsAmount[0]['totalPaidAmount'];    

        
            
        //cari balancenya saja
        $balance  = $rsReconsile[0]['amount'] - $totalPaidAmount;

        $statuskey = AP_STATUS['open']; 
        
        if ($balance <= 0)  // lunas
			$statuskey = AP_STATUS['lunas'];
        else if ($balance > 0 && $balance < abs($rsReconsile[0]['amount'])) // partial, pake abs utk positifin CN
		    $statuskey =  AP_STATUS['partial'];
       
        
	    $sql  = 'update '.$this->tableName.' set outstanding = amount - ' . $totalPaidAmount .' where statuskey <> 4 and pkey = ' . $this->oDbCon->paramString($reconsilekey) ;	 
	    $this->oDbCon->execute($sql);  
		
        if($rsReconsile[0]['statuskey'] <> $statuskey)
            $this->changeStatus($reconsilekey,$statuskey, '', false, true,true);
        
	}
    
    function validateForm($arr,$pkey = ''){ 
		   
		$arrayToJs = parent::validateForm($arr,$pkey); 

		  
		
		return $arrayToJs;
	 }  

	function validateConfirm($rsHeader){
        
	 }		

	function confirmTrans($rsHeader){ 
		$id = $rsHeader[0]['pkey'];
	  
		//update jurnal umum 
//        $this->updateGL($rsHeader);   
	} 
 
	 
	function cancelTrans($rsHeader,$copy){ 
		$id = $rsHeader[0]['pkey']; 
            

        // RS nya kosong, blm tau harusnya apa
        //$costReconsieleObj = $this->getCostReconsileObj();
	 	//for($i=0;$i<count($rs);$i++) 
			//$paymentObj->changeStatus($rs[$i]['pkey'],4,'',false,true);
        
		if ($copy)
			$this->copyDataOnCancel($id);	 
        
	} 
    
  
   
    
    function getCostReconsileObj(){
        return  new CostReconsile();
    }

    function normalizeParameter($arrParam, $trim = false){ 
 
        $arrParam = parent::normalizeParameter($arrParam,true); 
  
        return $arrParam;
    }


    
    function addAmortization($trDate='')
    {

		if(empty($trDate)) $trDate = date('Y-m-d');
        
        $amortization = new Amortization();
        $purchaseOrder = new PurchaseOrder();
        
        $tablekey = $purchaseOrder->getTableKeyAndObj($purchaseOrder->tableName,array('key'))['key'];

        $sql = 'select ' . $this->tableName . '.*
                from
                    ' . $this->tableName . '
                where
                    ' . $this->tableName . '.statuskey in (1,2) and
					'. $this->tableName.'.trdate <=  '.$this->oDbCon->paramString($trDate).' and  
                    ' . $this->tableName . '.reftabletype =  '.$this->oDbCon->paramString($tablekey).' and
                    ' . $this->tableName . '.outstanding > 0';
 
        $rs = $this->oDbCon->doQuery($sql);

        $arrExpenseAccrual = array();
        foreach ($rs as $row) {

            $outstanding = $row['outstanding'];
            $warehousekey = $row['warehousekey'];

            if ($outstanding <= 0) continue; 
        
            if(!isset($arrExpenseAccrual[$warehousekey]))
                $arrExpenseAccrual[$warehousekey] = array();


            array_push($arrExpenseAccrual[$warehousekey], array(
                                            'pkey' => $row['pkey'],
                                            'warehousekey' => $row['warehousekey'],
                                            'outstanding' => $row['outstanding'], 
                                            'amortizationvalue' => $row['amortizationvalue'],
                                            'costkey' => $row['costkey'],
                                        ));

        }

        $sqlCheck = 
                'select 
                    warehousekey,
                    trdate
                from 
                    '.$amortization->tableName.'
                where 
                    '.$amortization->tableName.'.trdate = ' . $this->oDbCon->paramString($trDate) . '
                and 
                    '.$amortization->tableName.'.statuskey in (1,2,3)
                ';
        
        $rsCheck = $this->oDbCon->doQuery($sqlCheck);


        $existData = array_column($rsCheck, 'warehousekey');
    
        foreach($arrExpenseAccrual as $warehousekey => $arrData) {
 
            if (in_array($warehousekey, $existData)) continue; 
            
            $arrParam = array();
    
            $arrParam['code'] = 'xxxxx';
            $arrParam['selStatus'] = 1;
            $arrParam['selWarehouseKey'] = $warehousekey;
            $arrParam['trDate'] = (!empty($trDate)) ? $this->formatDBDate($trDate,'d / m / Y')  : date(' d / m / Y'); 
            $arrParam['createdBy'] = 0;

            $arrParam['hidDetailKey'] = array();
            $arrParam['hidPrepaidExpenseKey'] = array();
            $arrParam['hidItemKey'] = array();
            $arrParam['amount'] = array();

            $total = 0;
            foreach($arrData as $row) {
                array_push($arrParam['hidDetailKey'], 0);
                array_push($arrParam['hidPrepaidExpenseKey'], $row['pkey']);
                array_push($arrParam['hidItemKey'], $row['costkey']);
                array_push($arrParam['amount'], $row['amortizationvalue']);
                $total += $row['amortizationvalue'];
            }

            $arrParam['total'] = $total;
 
            $arrayToJs = $amortization->addData($arrParam);

            if (!$arrayToJs[0]['valid'])
                throw new Exception($this->errorMsg[201] . ' ' . $arrayToJs[0]['message']);

            $amortization->changeStatus($arrayToJs[0]['data']['pkey'], 2, '', false, true);

        }
    
    }
    
    function updateOutstandingAmortization($pkey, $refkey){ 
        
        $amortization = new Amortization();

        $sql = 'select 
                        '. $amortization->tableName .'.statuskey,
                        coalesce(sum('.$amortization->tableNameDetail.'.amount),0) as totalamount 
                    from 
                        ' . $amortization->tableNameDetail . ',
                        '. $amortization->tableName .' 
                    where 
                    '. $amortization->tableNameDetail .'.refkey = '. $amortization->tableName .'.pkey and 
                    '. $amortization->tableNameDetail.'.refprepaidexpensekey = ' . $this->oDbCon->paramString($pkey) . ' and
                    '.$amortization->tableName.'.statuskey in (2,3) 
        ';

        $rsAmount = $this->oDbCon->doQuery($sql);
        $totalAmount = $rsAmount[0]['totalamount'];
        
        $rs = $this->getDataRowById($pkey);
        $outstanding = $rs[0]['amount'] - $totalAmount;

        $sql = 'update ' . $this->tableName . ' set outstanding =' . $this->oDbCon->paramString($outstanding) . ' where statuskey <> 4 and pkey = ' . $this->oDbCon->paramString($pkey);
        $this->oDbCon->execute($sql);

        if ($outstanding == $rs[0]['amount']) {
            $statuskey = AP_STATUS['open'];
        } else if ($outstanding == 0) {
            $statuskey = AP_STATUS['lunas'];
        } else {
            $statuskey = AP_STATUS['partial'];
        }

        if ($rs[0]['statuskey'] <> $statuskey)
            $this->changeStatus($pkey, $statuskey, '', false, true, true);
    }
        
}
?>
