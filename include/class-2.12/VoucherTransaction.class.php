<?php

class VoucherTransaction extends BaseClass{
 
   function __construct(){
		
		parent::__construct();
       
		$this->tableName = 'voucher_transaction'; 
		$this->tableVoucher = 'voucher'; 
		$this->tableStatus = 'transaction_status';  
		$this->tableWarehouse = 'warehouse'; 
		$this->tableCustomer = 'customer'; 
        $this->tableCustomerMembership = 'customer_membership';
		$this->securityObject = 'VoucherTransaction';
	    $this->isTransaction = true; 
		
        $this->arrData = array();
        $this->arrData['pkey'] = array('pkey'); 
        $this->arrData['code'] = array('code'); 
        $this->arrData['trdate'] = array('trDate','date');
        $this->arrData['expdate'] = array('expDate','date');
        $this->arrData['warehousekey'] = array('selWarehouse');
        $this->arrData['refkey'] = array('hidRefKey'); // pkey transaksi pembelian voucher, BUKAN ref transaksi sales order
        $this->arrData['reftabletype'] = array('refTableType');
        $this->arrData['refvoucherkey'] = array('hidVoucherKey');
        $this->arrData['customerkey'] = array('hidCustomerKey');
        $this->arrData['refcustomerkey'] = array('hidRefCustomerKey'); 
        $this->arrData['refcode'] = array('refCode'); 
        $this->arrData['trdesc'] = array('trDesc');
        $this->arrData['statuskey'] = array('selStatus'); 
        $this->arrData['discounttype'] = array('selDiscountType'); 
        $this->arrData['minamount'] = array('minAmount','number');
        $this->arrData['maxdiscount'] = array('maxDiscount','number');
        $this->arrData['value'] = array('value','number'); 
       
        $this->arrDataListAvailableColumn = array(); 
       
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'trdate','title' => 'date','dbfield' => 'trdate','default'=>true, 'width' => 80, 'align'=>'center', 'format' => 'date'));    
        array_push($this->arrDataListAvailableColumn, array('code' => 'name','title' => 'name','dbfield' => 'voucherlabel','default'=>true, 'width' => 150));    
        array_push($this->arrDataListAvailableColumn, array('code' => 'expdate','title' => 'expiredOn','dbfield' => 'expdate','default'=>true, 'width' => 100, 'align'=>'center', 'format' => 'date'));    
        array_push($this->arrDataListAvailableColumn, array('code' => 'useddate','title' => 'usedOn','dbfield' => 'useddate','default'=>true, 'width' => 100, 'align'=>'center', 'format' => 'date'));    
        array_push($this->arrDataListAvailableColumn, array('code' => 'customer','title' => 'customer','dbfield' => 'customername','default'=>true, 'width' => 160));    
        array_push($this->arrDataListAvailableColumn, array('code' => 'refcode','title' => 'reference','dbfield' => 'refcode',  'width' => 150));    
        //array_push($this->arrDataListAvailableColumn, array('code' => 'value','title' => 'value','dbfield' => 'value','default'=>true, 'width' => 100, 'align' => 'right', 'format'=>'number'));   
        array_push($this->arrDataListAvailableColumn, array('code' => 'note','title' => 'note','dbfield' => 'trdesc', 'width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
        
        $this->printMenu = array();  
        array_push($this->printMenu,array('code' => 'printVoucher', 'name' => $this->lang['print'] . ' ' .$this->lang['voucher'],  'icon' => 'print', 'url' => 'print/voucher'));
                    
	   $this->includeClassDependencies(array(
            'Voucher.class.php' 
        ));   

        $this->overwriteConfig();
               
   }
    
	function getQuery(){
	   
	   $sql = '
			select
					'.$this->tableName. '.*,  
                    '.$this->tableWarehouse.'.name as warehousename,
                    '.$this->tableCustomer.'.code as customercode,
                    '.$this->tableCustomer.'.name as customername,
                    '.$this->tableStatus.'.status as statusname,
					'.$this->tableVoucher.'.pkey as voucherkey,
					'.$this->tableVoucher.'.name as voucherlabel,
					'.$this->tableVoucher.'.shortdesc as vouchershortdesc,
					'.$this->tableVoucher.'.trdesc as voucherdesc,
					'.$this->tableVoucher.'.typekey,
					'.$this->tableVoucher.'.onetimeuse,
					'.$this->tableVoucher.'.categorykey
				from 
					'.$this->tableName.', 
                    '.$this->tableWarehouse.',
                    '.$this->tableCustomer.',
					'.$this->tableStatus.',
					'.$this->tableVoucher.'
				where  		 
					'.$this->tableName . '.statuskey = '.$this->tableStatus.'.pkey and 
					'.$this->tableName . '.customerkey = '.$this->tableCustomer.'.pkey and
					'.$this->tableName . '.refvoucherkey = '.$this->tableVoucher.'.pkey and
					'.$this->tableName . '.warehousekey = '.$this->tableWarehouse.'.pkey 
        
        ' .$this->criteria ;
        
        $sql .=  $this->getWarehouseCriteria() ;
         
        return $sql;
    }
    
	function validateForm($arr,$pkey = ''){ 
                 
        $arrayToJs = parent::validateForm($arr,$pkey);
    
        $voucherkey = $arr['hidVoucherKey'];
        $voucher = new Voucher();
        $rsVoucher = $voucher->getDataRowById($voucherkey);

        if(empty($voucherkey))
           $this->addErrorList($arrayToJs,false,$this->errorMsg['voucher'][1]); 
        
        // kalo status sudah selesai
        if($rsVoucher[0]['statuskey'] <> 2)
            $this->addErrorLog(false,'<strong>'.$rsVoucher[0]['code'].'</strong>. '.$this->errorMsg['voucher'][3]);
 
		// kalo qty sudah melebihi
		$maxqty =  $rsVoucher[0]['qty'];
		$qtyused = $rsVoucher[0]['qtyused'];
		if($maxqty > 0){
			if($qtyused > $maxqty) 
				$this->addErrorLog(false, $this->errorMsg['voucher'][4]); 
		}   

		// jika tgl diisi
		if(!empty($rsVoucher[0]['startdate']) && !empty($rsVoucher[0]['enddate'])){
			$date1 = str_replace('\'','',$this->oDbCon->paramDate($arr['trDate'],' / ','Y-m-d'));
			$trDate = strtotime($date1);
			$stardDate = strtotime($rsVoucher[0]['startdate']);
			$endDate = strtotime($rsVoucher[0]['enddate']);
   
			if($trDate > $endDate || $trDate < $stardDate)
				$this->addErrorList($arrayToJs,false, $this->errorMsg['voucher'][3]);
		}
	     
		return $arrayToJs;
	 }
    
    
//    function afterStatusChanged($rsHeader){ 
//        $voucher = new Voucher();
//        $voucher->updateQtyUsed($rsHeader[0]['refvoucherkey']); 
//    }
//    
     
    function validateConfirm($rsHeader){
        
        $voucher = new Voucher(); 
        $rsVoucher = $voucher->getDataRowById($rsHeader[0]['refvoucherkey']);
         
        // kalo status sudah selesai
    	if($rsVoucher[0]['statuskey'] <> 2)
           $this->addErrorLog(false,'<strong>'.$rsVoucher[0]['code'].'</strong>. '.$this->errorMsg['voucher'][3]);
        
        // kalo qty sudah melebihi
        $maxqty =  $rsVoucher[0]['qty'];
        $qtyused = $rsVoucher[0]['qtyused'];
        
        if($maxqty > 0){
            if($qtyused > $maxqty) 
                $this->addErrorLog(false,  $this->errorMsg['voucher'][4]); 
        }        
		
		if(!empty($rsVoucher[0]['startdate']) && !empty($rsVoucher[0]['enddate'])){
			// kalo tgl expired sdh melebih batas waktu
			$trDate = strtotime($rsHeader[0]['trdate']);
			$startDate = strtotime($rsVoucher[0]['startdate']);
			$endDate = strtotime($rsVoucher[0]['enddate']);
			if($trDate > $endDate || $trDate < $startDate)
				$this->addErrorLog(false,  $this->errorMsg['voucher'][3]);
		} 
        
    }
    
    function confirmTrans($rsHeader){
        
        //$voucher = new Voucher();     
        
    }
    
    function validateCancel($rsHeader,$autoChangeStatus=false){
        $id = $rsHeader[0]['pkey'];
    } 
    
    
    function normalizeParameter($arrParam, $trim = false){  
        $arrParam = parent::normalizeParameter($arrParam,true); 

        
        return $arrParam;
        
    }
    
    function getAvailableVoucher($customerkey){
         
        $sql = 'select
                    '.$this->tableName.'.*,
                    '.$this->tableVoucher.'.name as voucherlabel,
                    '.$this->tableVoucher.'.shortdesc ,
                    '.$this->tableVoucher.'.trdesc,
                    '.$this->tableVoucher.'.typekey,
                    '.$this->tableVoucher.'.categorykey
                from
                    '.$this->tableName.', 
                    '.$this->tableVoucher.' 
                where 
                    '.$this->tableName.'.customerkey = '.$this->oDbCon->paramString($customerkey).' and 
                    '.$this->tableName.'.refvoucherkey = '.$this->tableVoucher.'.pkey and 
                    '.$this->tableName.'.statuskey = 2' 
            ;
        
        return $this->oDbCon->doQuery($sql);
         
        
    }
    
    
 function updateVoucherAvailability($voucherkey){
        
        $arrTable = array();
        array_push($arrTable, $this->tableCustomerMembership);
        //array_push($arrTable, $this->tableSalesOrderHeader);
         
        
		try{ 
            
		 	if(!$this->oDbCon->startTrans())
				throw new Exception($this->errorMsg[100]); 
			 
			
            foreach($arrTable as $table){

                    $sql = 'select * from '.$table.'  where '.$table.'.voucherkey = '.$this->oDbCon->paramString($voucherkey).' and '.$table.'.statuskey in (1,2,3)';
                    $rs = $this->oDbCon->doQuery($sql);
                    $rs[0]['tablename'] = $table;
                    $rsTableKey = $this->getTableKeyAndObj($rs[0]['tablename']); 

                
                    if(empty($rs) || empty($rs[0]['pkey'])){
                        //not used
                        $reftranskey = 0;   
                        $reftranstablekey = 0;
                        $statuskey = 2;
                        $useddate = DEFAULT_EMPTY_DATE;
                    }else{
                        //used
                        $reftranskey = $rs[0]['pkey'];
                        $reftranstablekey = $rsTableKey['key'];
                        $statuskey = 3;
                        $useddate = date('d / m / Y');  
                    }
                 
                    $sql = 'update 
                                    '.$this->tableName.' 
                            set 
                                statuskey = '.$statuskey.',
                                reftranskey = '.$reftranskey.',
                                reftranstablekey = '.$reftranstablekey.',
                                useddate = '.$this->oDbCon->paramDate($useddate, ' / ').' 
                            
                            where '.$this->tableName.'.statuskey in (2,3) and '.$this->tableName.'.pkey = '.$this->oDbCon->paramString($voucherkey);
                    //$this->setLog($rs,true);
                    $this->oDbCon->execute($sql); 

            }
            
		    $this->oDbCon->endTrans();
					  
					 
		} catch(Exception $e){
			$this->oDbCon->rollback();
			$this->addErrorList($arrayToJs,false, $e->getMessage());   
		}
        

        
    }
	
	function generateDefaultQueryForAutoComplete($returnField){ 
        $sql = 'select
                '.$returnField['key'].',
                '.$returnField['value'].' as value,
                '.$this->tableName . '.code,
                '.$this->tableName . '.value as vouchervalue,
                '.$this->tableName . '.discounttype 
            from 
                '.$this->tableName.', 
                '.$this->tableStatus.'  
            where  		 
                '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey  
        ';
        
        $sql .=  $this->getCompanyCriteria() ;
        return $sql; 
    }
        
           
     function useVoucher($arrVoucherKey, $refkey,$reftabletype){
         
		try{ 
            
		 	if(!$this->oDbCon->startTrans())
				throw new Exception($this->errorMsg[100]); 
			 
			 // update semua voucher yg berhubungan dengan refkey ini
			 $sql = 'update  
			 			'.$this->tableName.' 
					set reftranstablekey = 0, statuskey = 2, reftranskey = 0,useddate = \'0000-00-00\' 
					where  
						'.$this->tableName.'.reftranskey = '.$this->oDbCon->paramString($refkey). ' 
						and '.$this->tableName.'.reftranstablekey = '.$this->oDbCon->paramString($reftabletype);
             
			$this->oDbCon->execute($sql); 
			
			// back confirmed, status langsung proses saja diatas
//			foreach($arrVoucherKey as $voucherkey)
//            	$this->changeStatus($voucherkey ,2);
   
            
            if(!empty($arrVoucherKey)){
                $sql = 'update 
                            '.$this->tableName.' 
                        set 
                            reftranstablekey = '.$this->oDbCon->paramString($reftabletype).',
                            reftranskey = '.$this->oDbCon->paramString($refkey).',
                            useddate = now() ,
                            statuskey = 3
                            where '.$this->tableName.'.pkey  in ('.$this->oDbCon->paramString($arrVoucherKey,',').')';
 
                $this->oDbCon->execute($sql); 

                // status langsung proses saja diatas			
    //			foreach($arrVoucherKey as $voucherkey)
    //            	$this->changeStatus($voucherkey ,3);

                // hitung ulang qtyused
                $this->updateVoucherQty($arrVoucherKey); 

            }
			
		    $this->oDbCon->endTrans();
					  
					 
		} catch(Exception $e){
			$this->oDbCon->rollback();
			$this->addErrorList($arrayToJs,false, $e->getMessage());   
		}
        
    }
	
    function updateVoucherQty($arrVoucherTransactionKey){ 
        $voucher = new Voucher();
        $rs = $this->searchDataRow(array($this->tableName.'.pkey',$this->tableName.'.refvoucherkey'),
                                    ' and '.$this->tableName.'.pkey in  ('.$this->oDbCon->paramString($arrVoucherTransactionKey,',').')'
                                   );
        $arrVoucherKey = array_column($rs,'refvoucherkey');
        foreach($arrVoucherKey as $voucherkey)
            $voucher->updateQtyUsed($voucherkey); 
            
    }
    
	function calculateVoucherValue($voucherInformation,$salesInformation){
        $voucher = new Voucher();        
        
        $voucherkey = $voucherInformation['voucherkey'];
        $vouchertype = $voucherInformation['vouchertype'];
        
        $totalSales = $salesInformation['totalsales'];
        $totalShipment = $salesInformation['totalshipment'];
        

        // category search ulang
        
		$totalSales = $this->unFormatNumber($totalSales);
		
        $returnValue = 0;
        
        // type 1 = reguler
        
        if (in_array($vouchertype, array(VOUCHER_TYPE['regular'], VOUCHER_TYPE['claim']))){

                                                //$this->tableName.'.name',
                                                //$this->tableName.'.shortdesc',
            
            // status gk bisa dimasukkan dlm criteria ( atu cair yg konfirmasi dan selesai), karena di admin form, perlu utk hitung ulang ketika edit
            $rsVoucherTransaction = $this->searchDataRow(array($this->tableName.'.pkey', $this->tableName.'.expdate',
                                                $this->tableName.'.trdesc',
												$this->tableName.'.minamount',$this->tableName.'.maxdiscount',
												$this->tableName.'.discounttype',$this->tableName.'.value',$this->tableName.'.refvoucherkey'),
										  ' and '.$this->tableName.'.pkey = ' . $this->oDbCon->paramString($voucherkey).'
                                            and '.$this->tableName.'.expdate >= curdate()
                                            and '.$this->tableName.'.statuskey in(2,3) 
                                          ');
		    
            if(!empty($rsVoucherTransaction)){
                $rsVoucher = $voucher->searchDataRow(array($voucher->tableName.'.pkey', $voucher->tableName.'.categorykey', $voucher->tableName.'.name', $voucher->tableName.'.trdesc', $voucher->tableName.'.shortdesc', $voucher->tableName.'.qtyused', $voucher->tableName.'.qty'),
                                                    ' and '.$voucher->tableName.'.pkey = ' . $this->oDbCon->paramString( $rsVoucherTransaction[0]['refvoucherkey'])
                                                    );
                $rsVoucherTransaction[0]['categorykey'] = $rsVoucher[0]['categorykey'];
                $rsVoucherTransaction[0]['name'] = $rsVoucher[0]['name'];
                $rsVoucherTransaction[0]['pkey'] = $rsVoucher[0]['pkey'];
                $rsVoucherTransaction[0]['shortdesc'] = $rsVoucher[0]['shortdesc'];
                $rsVoucherTransaction[0]['trdesc'] = $rsVoucher[0]['trdesc'];
                $rsVoucherTransaction[0]['qtyused'] = $rsVoucher[0]['qtyused'];
                $rsVoucherTransaction[0]['qty'] = $rsVoucher[0]['qty'];
            }
            
        }else{
             // kalo dr collectible
             $rsVoucherTransaction = $voucher->searchDataRow(array($voucher->tableName.'.pkey',
                                                $voucher->tableName.'.name',
                                                $voucher->tableName.'.shortdesc',
                                                $voucher->tableName.'.enddate as expdate',
                                                $voucher->tableName.'.trdesc',
                                                $voucher->tableName.'.qtyused',
				                                $voucher->tableName.'.qty',
												$voucher->tableName.'.minamount',$voucher->tableName.'.maxdiscount',
												$voucher->tableName.'.discounttype',$voucher->tableName.'.value',$voucher->tableName.'.categorykey'),
                                                ' and '.$voucher->tableName.'.pkey = ' . $voucher->oDbCon->paramString($voucherkey).'
                                                  and '.$voucher->tableName.'.typekey = ' . $voucher->oDbCon->paramString($vouchertype).'
                                                  and '.$voucher->tableName.'.enddate >= curdate() and startdate <= curdate()
                                                  and '.$voucher->tableName.'.statuskey = 2'
                                              ); 
        }
        
                       
	    $pkey = $rsVoucherTransaction[0]['pkey'];
            $name = $rsVoucherTransaction[0]['name'];
            $shortdesc = $rsVoucherTransaction[0]['shortdesc'];
            $trdesc = $rsVoucherTransaction[0]['trdesc'];
        
            $expDate = $rsVoucherTransaction[0]['expdate'];
            $minAmount = $rsVoucherTransaction[0]['minamount'];
            $maxDiscount = $rsVoucherTransaction[0]['maxdiscount'];
            $discountType = $rsVoucherTransaction[0]['discounttype'];
            $value = $rsVoucherTransaction[0]['value'];
            $categorykey = $rsVoucherTransaction[0]['categorykey'];
            $percentageused = ($rsVoucherTransaction[0]['qty'] == 0) ? 0 : $rsVoucherTransaction[0]['qtyused'] / $rsVoucherTransaction[0]['qty'] * 100; // gk bisa 100% karena kalo voucher unlimited, disiinya 0 oleh user
            $refvoucherkey = $rsVoucherTransaction[0]['refvoucherkey'];

            // jika nilai pemjualan (HANYA PENJUALAN)  minimum tidak tercapai
            if($minAmount > 0 && $totalSales < $minAmount ) { 
                $value = 0;
            }else{
                 // kalo sales  
                // nanti dipilih mau sales atau shipment. utk registrasai nanti menyusul
                switch($categorykey){
                    case 1 : $totalAmount = 0; break;
                    case 2 : $totalAmount = $totalSales; break;
                    case 3 : $totalAmount = $totalShipment; break;
                    default : $totalAmount = 0; break;
                }  
                
                if($discountType == 2) 
                    $value = $value / 100 *  $totalAmount; 

                if($maxDiscount > 0 && $value > $maxDiscount) $value = $maxDiscount;

                $value = ($value > $totalAmount) ? $totalAmount : $value; // harusnya gk ad max discount kalo jenisnya nilai voucher  

            }


            // kalo shipment
            $returnValue = array('pkey' => $pkey ,'name' => $name, 'shortdesc' => $shortdesc, 'expdate' => $expDate, 'amount' => $value, 'categorykey' => $categorykey, 'typekey' => $vouchertype, 'trdesc' => $trdesc, 'percentageused' => $percentageused, 'refvoucherkey' => $refvoucherkey);
		    return $returnValue; // harusnya gk ad max discount kalo jenisnya nilai voucher
		
	}
    
    function eligibleForOneTimeUse($userkey,$vouchertransactionkey){
         
        // gk perlu cek status
        $rsVoucher = $this->searchData('','',true,' and '.$this->tableName.'.pkey in (' .$this->oDbCon->paramString($vouchertransactionkey,',').')' ); 
         
        $arrReturn = array();
        foreach($rsVoucher as $row){
            $voucherkey = $row['pkey'];
            
            // kalo gk ad rules ny, boleh
             if ($row['onetimeuse'] == 0){
                 $arrReturn[$voucherkey] = true;
                 continue;
             }
             
             // count total yg kepake utk voucher ini, kecualikan voucher itu sendiri
             $sql  = 'select coalesce(count(pkey),0) as total from '.$this->tableName.' 
                      where 
                        '.$this->tableName.'.customerkey = '.$this->oDbCon->paramString($userkey).' and 
                        '.$this->tableName.'.statuskey <> 4 and 
                        '.$this->tableName.'.pkey <> '.$this->oDbCon->paramString($voucherkey).' and 
                        '.$this->tableName.'.refvoucherkey = '.$this->oDbCon->paramString($row['refvoucherkey']);
             
             $rs = $this->oDbCon->doQuery($sql);
             $arrReturn[$voucherkey] = ($rs[0]['total'] == 0) ? true : false;
        }
     
        return $arrReturn;
    }
    
        
    function removeOneTimeUse($rs){
        if (empty(USERKEY)) return $rs;
        
        $uniqueRules = $this->eligibleForOneTimeUse(USERKEY,array_column($rs,'pkey'));

        foreach($rs as $key=>$row){
            if(!$uniqueRules[$row['pkey']])
                unset($rs[$key]);
        }    

        return array_values($rs);
    }
	
  }

?>
