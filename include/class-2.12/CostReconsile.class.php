<?php

class CostReconsile extends BaseClass{
  
   function __construct(){
		
		parent::__construct();
		
		$this->tableName = 'cost_reconsile_header';
		$this->tableNameDetail = 'cost_reconsile_detail';
        $this->tableJobOrder = 'emkl_job_order_header';
		$this->tableStatus = 'transaction_status';
		$this->tableInvoice = 'emkl_order_invoice_header';
		$this->tableWarehouse = 'warehouse'; 
		$this->tableCustomer = 'customer'; 
		$this->tableItem = 'item';
		$this->tablePayment= 'cost_reconsile_payment';
        $this->tableCurrency = 'currency';
        $this->tablePaymentMethod = 'payment_method';
        $this->tablePurchaseOrder = 'emkl_purchase_order_header';
        $this->tablePrepaidExpense =  'prepaid_expense';
        $this->isTransaction = true;
        $this->newLoad = true;
       
        $this->tableNeedToBeCopyOnCancel = array($this->tableNameDetail);
		 
		$this->securityObject = 'CostReconsile';
       
        $this->arrDataDetail = array(); 
        $this->arrDataDetail['pkey'] = array('hidDetailKey');
        $this->arrDataDetail['refkey'] = array('pkey','ref');
        $this->arrDataDetail['refreconsilekey'] = array('hidReconsileKey');
        $this->arrDataDetail['outstanding'] = array('outstanding','number');
        $this->arrDataDetail['amount'] = array('amount', array('datatype' => 'number','mandatory'=>true));
       
        $arrPaymentDetail = array(); 
        $arrPaymentDetail['pkey'] = array('hidDetailPaymentKey');
        $arrPaymentDetail['refkey'] = array('pkey', 'ref');
        $arrPaymentDetail['amount'] = array('paymentMethodValue',array('datatype' => 'number','mandatory'=>true));
        $arrPaymentDetail['paymentkey'] = array('selPaymentMethod'); // gk boleh mandatory, karena kadang pake payment kadang pake voucher, validasi di add saja

        $arrDetails = array();
        array_push($arrDetails, array('dataset' => $this->arrDataDetail, 'tableName' => $this->tableNameDetail));
//        array_push($arrDetails, array('dataset' => $arrPaymentDetail, 'tableName' => $this->tablePayment));
          
        $this->arrData = array(); 
        $this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails));
        $this->arrData['code'] = array('code');
        $this->arrData['trdate'] = array('trDate','date');
        $this->arrData['refcode'] = array('refHeaderCode');
        $this->arrData['reftabletype'] = array('reftabletype');
        $this->arrData['refkey'] = array('hidInvoiceKey');
        $this->arrData['islinked'] = array('islinked');
        $this->arrData['trdate'] = array('trDate','date');
        $this->arrData['currencykey'] = array('selCurrency');
        $this->arrData['warehousekey'] = array('selWarehouseKey');
        $this->arrData['trnotes'] = array('trDesc');
        $this->arrData['statuskey'] = array('selStatus');
        $this->arrData['grandtotal'] = array('total','number');
        $this->arrData['usedateperiod'] = array('chkDatePeriod');
        $this->arrData['startdateperiod'] = array('trStartDate','date');
        $this->arrData['enddateperiod'] = array('trEndDate','date');
        $this->arrData['rate'] = array('currencyRate','number');
        $this->arrData['overwriteGL'] = array('overwriteGL'); 
        $this->arrData['profitloss'] = array('profitLoss','number');
       
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'date','title' => 'date','dbfield' => 'trdate','default'=>true, 'width' => 100, 'align' =>'center', 'format' => 'date'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'warehouse','title' => 'warehouse','dbfield' => 'warehousename', 'default'=>true, 'width' => 120));
	    array_push($this->arrDataListAvailableColumn, array('code' => 'invoice','title' => 'invoiceCode','dbfield' => 'invoicecode', 'default'=>true, 'width' => 130));      
        array_push($this->arrDataListAvailableColumn, array('code' => 'customer','title' => 'customer','default'=>true, 'default'=>true, 'dbfield' => 'customername', 'width' => 130));     
        array_push($this->arrDataListAvailableColumn, array('code' => 'refCode','title' => 'JOCode','default'=>true, 'dbfield' => 'jocode', 'width' => 130));      
        array_push($this->arrDataListAvailableColumn, array('code' => 'currency','title' => 'curr','dbfield' => 'currencyname', 'default'=>true, 'width' => 60,  'align' =>'center'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'costReconsileAmount','title' => 'amount','dbfield' => 'grandtotal', 'default'=>true, 'width' => 120,  'align' =>'right',  'format' => 'number' ));
        //array_push($this->arrDataListAvailableColumn, array('code' => 'margin','title' => 'margin','dbfield' => 'profitloss',  'width' => 120,  'align' =>'right',  'format' => 'number' ));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
        array_push($this->arrDataListAvailableColumn, array('code' => 'desc','title' => 'note','dbfield' => 'trnotes',  'width' => 250)); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'jobOrderCode','title' => 'jobOrderCode','dbfield' => 'jocode', 'width' => 100));    
 
         
        $this->arrSearchColumn = array ();
        array_push($this->arrSearchColumn, array('Kode', $this->tableName . '.code')); 
        array_push($this->arrSearchColumn, array('Tanggal', $this->tableName . '.trdate')); 
        array_push($this->arrSearchColumn, array('Gudang', $this->tableWarehouse. '.name'));
        array_push($this->arrSearchColumn, array('Total', $this->tableName. '.grandtotal')); 
        array_push($this->arrSearchColumn, array('Faktur', $this->tableInvoice. '.code'));    
        array_push($this->arrSearchColumn, array('Kode JO', $this->tableInvoice. '.salesordercodecache'));    
        array_push($this->arrSearchColumn, array('Pelanggan', $this->tableCustomer. '.name'));    
 
       
        array_push($this->filterCriteria, array('title' => $this->lang['warehouse'], 'field' => 'warehousekey'));
       
        $this->printMenu = array();
        array_push($this->printMenu,array('code' => 'printTransaction', 'name' => $this->lang['printTransaction'],  'icon' => 'print', 'url' => 'print/costReconsile'));
          
        $this->includeClassDependencies(array(
                  'CostReconsileOutstanding.class.php', 
                  'ChartOfAccount.class.php', 
                  'COALink.class.php', 
                  'Customer.class.php', 
                  'Currency.class.php', 
                  'Item.class.php', 
                  'EMKLOrderInvoice.class.php', 
                  'PrepaidExpense.class.php', 
                  'EMKLJobOrder.class.php',  
                  'GeneralJournal.class.php', 
                  'CostCashOut.class.php', 
                  'Service.class.php', 
                  'Warehouse.class.php',
            ));  
       
        $this->overwriteConfig();
	}
	
	function getQuery(){
		
		$sql = '
			SELECT '.$this->tableName.'.* ,
			   '.$this->tableInvoice.'.code as invoicecode,
			   '.$this->tableInvoice.'.salesordercodecache as jocode,
			   '.$this->tableWarehouse.'.name as warehousename,
			   '.$this->tableStatus.'.status as statusname,
			   '.$this->tableCustomer.'.name as customername,
               '.$this->tableCurrency.'.name as currencyname
			FROM '.$this->tableStatus.', 
                  '.$this->tableName.', 
                  '.$this->tableWarehouse.',
                  '.$this->tableInvoice.' 
                        left join ' . $this->tableCustomer . ' on ' . $this->tableInvoice . '.customerkey = ' . $this->tableCustomer . '.pkey ,
                  '.$this->tableCurrency.'
			WHERE 
				  '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey and
				  '.$this->tableName.'.refkey = '.$this->tableInvoice.'.pkey and
                  '.$this->tableName.'.currencykey = '.$this->tableCurrency.'.pkey   and
				  '.$this->tableName.'.warehousekey = '.$this->tableWarehouse.'.pkey  
		' .$this->criteria ;
        
        $sql .=  $this->getWarehouseCriteria() ;
        
        return $sql;
	}
    

	 
	function validateForm($arr,$pkey = ''){
          
        $reconsileObj = $this->getPrepaidExpenseObj();
        
		$arrayToJs = parent::validateForm($arr,$pkey); 

        $currencykey = $arr['selCurrency'];  
        $invoicekey = $arr['hidInvoiceKey'];  
		$arrReconsilekey = $arr['hidReconsileKey']; 
		$arrAmount = $arr['amount'];
		$arrOutstanding= $arr['outstanding'];
        $trDate = $arr['trDate'];
		//$arrPick = $arr['chkPick'];  

        $arrDetailKey = array();
          
        if(empty($invoicekey))
            $this->addErrorList($arrayToJs,false, $this->errorMsg['invoice'][1]); 	

        $rsReconsile = (!empty($arrReconsilekey)) ? $reconsileObj->searchData('','',true, ' and '.$reconsileObj->tableName.'.pkey in ('.implode(',',$this->oDbCon->paramString($arrReconsilekey)).') ') : array(); 
        
        $arrReconsile = array_column($rsReconsile,null, 'pkey');
        $arrDate = array_column($arrReconsile, 'trdate', 'pkey');
            

		//validasi kalo status gk menunggu gk bisa edit 
		if (!empty($pkey)){
			$rs = $this->getDataRowById($pkey);
			if ($rs[0]['statuskey'] <> 1){
				$this->addErrorList($arrayToJs,false,$this->errorMsg[212]);
			}
		}  

	 
        $hasReconsile = false; 
        for($i=0;$i<count($arrReconsilekey);$i++) { 
            if (!empty($arrReconsilekey[$i]))  //  && !empty($arrPick[$i])
                $hasReconsile = true;  

            if (in_array($arrReconsilekey[$i],$arrDetailKey)){   
                $this->addErrorList($arrayToJs,false, $arrReconsile[$arrReconsilekey[$i]]['code'].'. '.$this->errorMsg[215]); 	 
            }else{ 
                array_push($arrDetailKey, $arrReconsilekey[$i]); 
            }

        }

        if (!$hasReconsile)
            $this->addErrorList($arrayToJs,false, $this->errorMsg['prepaidExpense'][1]); 	

        
		for($i=0;$i<count($arrReconsilekey);$i++) {  
            if(!empty($arrReconsilekey[$i])){
                
                
                $outstanding = $this->unFormatNumber($arrOutstanding[$i]);
                $amount = $this->unFormatNumber($arrAmount[$i]);
                
                if ($amount == 0 || 
                    ($outstanding > 0 && $amount < 0) || 
                    ($outstanding > 0 && ($amount) > $outstanding) || //overpay
                    ($outstanding < 0 && (($amount) < $outstanding ||  $amount > 0)) //overpay
                   ) 
                $this->addErrorList($arrayToJs,false,'<strong>'.$arrReconsile[$arrReconsilekey[$i]]['code']. '</strong>. ' . $this->errorMsg['costReconsile'][2]);
   
				if($currencykey<>$arrReconsile[$arrReconsilekey[$i]]['currencykey'])
                    $this->addErrorList($arrayToJs,false,'<strong>'.$arrReconsile[$arrReconsilekey[$i]]['code'].'</strong>. '.$this->errorMsg['costReconsile'][5]); 
  
            }
		}
     
         
		
		return $arrayToJs;
	 }

	
	function reCountGrandtotal($arrParam){

        $emklOrderInvoice = new EMKLOrderInvoice();
        $prepaidExpense = new PrepaidExpense();

        $grandtotal = 0;
        $totalCostIDR = 0; 
        $discount = 0;
        $pph = 0;

        $invoicekey = $arrParam['hidInvoiceKey'];
        $currencykey = $arrParam['selCurrency'];
				
        $arrConsilekey = $arrParam['hidReconsileKey'];
        $arrAmount = $arrParam['amount'];
        //$arrPick = $arrParam['chkPick']; 

        $rsReconsileCol = $prepaidExpense->searchDataRow(array($prepaidExpense->tableName.'.pkey',
                                                        $prepaidExpense->tableName.'.amountidr' 
                                                      ),
                              ' and '.$prepaidExpense->tableName.'.pkey in ('.$this->oDbCon->paramString($arrConsilekey,',').')'
                             );
        $rsReconsileCol = array_column($rsReconsileCol,null,'pkey');
     
        for ($i=0;$i<count($arrConsilekey);$i++){

            $arrAmount[$i] = $this->unFormatNumber($arrAmount[$i]);
            if ( empty($arrConsilekey[$i]) || empty($arrAmount[$i]))  //  || empty($arrPick[$i]) 
                continue; 

            $rsReconsile = $rsReconsileCol[$arrConsilekey[$i]]; 
            
            $grandtotal += $this->unFormatNumber($arrAmount[$i]);

            $totalCostIDR += $rsReconsile['amountidr'];   
        }  
   
        $totalInvoice = 0;
        $totalProfitLoss = 0;

        $rsInvoice = $emklOrderInvoice->searchDataRow(array($emklOrderInvoice->tableName.'.pkey',
                                                    $emklOrderInvoice->tableName.'.code',
                                                    $emklOrderInvoice->tableName.'.currencykey',
                                                    $emklOrderInvoice->tableName.'.rate',
                                                    $emklOrderInvoice->tableName.'.beforetaxtotal',
                                                    $emklOrderInvoice->tableName.'.grandtotal'), 
                                                     ' and ' . $emklOrderInvoice->tableName.'.pkey = '. $this->oDbCon->paramString($invoicekey));

        if (!empty($rsInvoice)) {
             
            $totalInvoice = $rsInvoice[0]['beforetaxtotal'];
            $currencyInvoice = $rsInvoice[0]['currencykey'];
            $rateInvoice = $rsInvoice[0]['rate'];
            
            // total invocie termasuk PPN
            $totalInvoiceInIDR = ( $currencyInvoice != CURRENCY['idr']) ?  $totalInvoice * $rateInvoice : $totalInvoice; 
  
            // profit loss dithitung dari DPP, diluar PPN
            // karena PPN di purchase di absorb, kadang tidak, tergantugn selling
            // mungkin nanti kedepanya baru diupdate lebih akurat, skarang hanya utk perbandingan
            $totalProfitLoss = $totalInvoiceInIDR - $totalCostIDR;

        }
        
        $reCountResult = array();
        $reCountResult['grandtotal'] = $grandtotal;
        $reCountResult['profitloss'] = $totalProfitLoss;

        return $reCountResult;
				
	}	
    
    function afterStatusChanged($rsHeader){ 
        
		$reconsileObj = $this->getPrepaidExpenseObj();
        

        $rsDetail = $this->getDetailById($rsHeader[0]['pkey']);
        
        for($i=0;$i<count($rsDetail); $i++){   
           $reconsileObj->updateOutstanding($rsDetail[$i]['refreconsilekey']); 
        }  
        
        $rsHeader = $this->getDataRowById($rsHeader[0]['pkey']);

         // retrieve latest status
        if ($rsHeader[0]['statuskey'] == 2)
            $this->changeStatus($rsHeader[0]['pkey'],3,'',false,true); // set jd autochange, agar gk perlu validasi session utk AR Payment yg jalan dr cron 
    }


    
	function validateConfirm($rsHeader){
		 
		$id = $rsHeader[0]['pkey'];
        $currencykey =  $rsHeader[0]['currencykey'];
        $rate = ($rsHeader[0]['rate'] > 0) ? $rsHeader[0]['rate'] : 1;
                
		$coaLink = new COALink();
        $warehouse = new Warehouse();
        $prepaidExpense = $this->getPrepaidExpenseObj();

          
//        $balance = $rsHeader[0]['grandtotal']; 
//        $balance *= $rate; 
//         
//        $thresholdDiscount = abs($this->loadSetting('roundedPaymentThreshold'));
//        if($balance < ($thresholdDiscount * -1)) 
//            $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg[502]);
//        else if ($balance > $thresholdDiscount)
//            $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg[509]); 

        if (USE_GL){ 

            $arrCOA = array();
            array_push($arrCOA,'prepaidexpense','othercost'); 
            
            // kalo ad lebih dr 1 currency
            $currency = new Currency();
            $rsCurrency = $currency->searchData($currency->tableName.'.statuskey',1);
                       
            for ($i=0;$i<count($arrCOA);$i++){
                $rsCOA = $coaLink->getCOALink ($arrCOA[$i], $warehouse->tableName,$rsHeader[0]['warehousekey'], 0); 
                if (empty($rsCOA))	
                    $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$arrCOA[$i]. ' ' .$this->errorMsg['coa'][3]);
            }   
 
           
        }    
 
        $rsDetail = $this->getDetailById($id);
        $arrKeys = array_column($rsDetail,'refreconsilekey');  
        $rsReconsile = $prepaidExpense->searchData('','',true,' and ' .$prepaidExpense->tableName.'.pkey in ('.$this->oDbCon->paramString($arrKeys,',').') ' );
      
        
        // cek status sudah diakui atau belum
        if (!empty($arrKeys)){ 
            $rsReconsilePaid = $prepaidExpense->searchData('','',true,' and ' .$prepaidExpense->tableName.'.pkey in ('.$this->oDbCon->paramString($arrKeys,',').') and ' .$prepaidExpense->tableName.'.statuskey in (3,4) ' );
            if (!empty($rsReconsilePaid)){
                $arrReconsile = array_column($rsReconsilePaid,'code');
                $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg[201].'<br><strong>'.implode(', ',$arrReconsile).'</strong>. '.$this->errorMsg['costReconsile'][6]); 
            }
        }
        
        // cek amount yg diisi lebih besar gk dr outstanding

        
        $trDate =  $this->formatDBDate($rsHeader[0]['trdate'],'d / m / Y');
        for($i=0;$i<count($rsReconsile);$i++){

			if($currencykey<>$rsReconsile[$i]['currencykey'])
                $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. <strong>'.$rsReconsile[$i]['code'].'</strong>. '.$this->errorMsg['costReconsile'][5]); 
			
			$arDate = $this->formatDBDate($rsReconsile[$i]['trdate'],'d / m / Y');
		 
			$dateDiff = $this->dateDiff($trDate,$arDate);
			
//			$this->setLog('NANTI DIAKTIFKAN LG',true); 
			if (!in_array(DOMAIN_NAME,array('thewhale.wintera.co.id', 'cif.wintera.co.id'))){  
                if($dateDiff > 0)
                    $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg['costReconsile'][4]); 
			}
           
        }
        
        
        // cek setiap detail ad g overpaid gk
        // harus ambil ulang outstanding reconsile
        $rsReconsile = array_column($rsReconsile,null,'pkey');

        foreach($rsDetail as $detailRow){
           $reconsileRow = $rsReconsile[$detailRow['refreconsilekey']];

           if ( ($reconsileRow['outstanding'] > 0 && $detailRow['amount'] > ($reconsileRow['outstanding']+1) )  || //overpay
               ($reconsileRow['outstanding']  < 0 && ($detailRow['amount'] < ($reconsileRow['outstanding']-1)  ||  $detailRow['amount'] > 0)) //overpay
              )   
            $this->addErrorLog(false,'<strong>'.$reconsileRow['code']. '</strong>. ' . $this->errorMsg['prepaidExpense'][2]); 
           
        }
         
        
    }
    
    function getDetailReconsile($reconsilekey,$criteria = ''){
		$sql = 'select 
					'. $this->tableNameDetail.'.* ,
                    '. $this->tableCurrency.'.name as currencyname
				from 
					'. $this->tableNameDetail.','. $this->tableCurrency.','. $this->tableName.'  
				where 
					'. $this->tableNameDetail.'.refkey = '. $this->tableName.'  .pkey and
					'. $this->tableNameDetail.'.refreconsilekey in(' .$this->oDbCon->paramString($reconsilekey,',').') and
                    '. $this->tableCurrency.'.pkey = '. $this->tableName.'  .currencykey and
				    ('. $this->tableName.'.statuskey = 2 or '. $this->tableName.'.statuskey = 3)';
     
        if(!empty($criteria))
            $sql .= $criteria;   
        
        $sql .= ' order by  pkey asc'; 
					  
		return $this->oDbCon->doQuery($sql);
	} 
	 
	function confirmTrans($rsHeader){ 
		$id = $rsHeader[0]['pkey'];   
        $this->updateGL($rsHeader); 
	}
 

    function validateCancel($rsHeader,$autoChangeStatus = false){ 
            

            if ( !$autoChangeStatus ) {
                if(isset($rsHeader[0]['islinked']) && !empty($rsHeader[0]['islinked']))
                    $this->addErrorLog(false, '<strong>'.$rsHeader[0]['code'].'.</strong> '.$this->errorMsg[900],true);  
            }  

            $id = $rsHeader[0]['pkey'];
        
            if(!$this->validateAutoReverseGL($id))
                $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' . $this->errorMsg[201].' ' .$this->errorMsg['generalJournal'][6],true);
    

    } 

    function cancelTrans($rsHeader,$copy){ 

        $id = $rsHeader[0]['pkey'];   

        if ($copy)
            $this->copyDataOnCancel($id);	  

        $this->cancelGLByRefkey($rsHeader[0]['pkey'],$this->tableName);
    }


	function updateGL($rs){  
		
        if (!USE_GL) return;
        if ($rs[0]['overwriteGL'] == 1) return;
        
        
		$warehouse = new Warehouse();
		$service = new Service(SERVICE);
        $coaLink = new COALink();
        $generalJournal = new GeneralJournal(); 
        $chartOfAccount = new ChartOfAccount();
        $EMKLOrderInvoice = new EMKLOrderInvoice();
        $multiCurrency = ($rs[0]['currencykey'] != CURRENCY['idr']) ? true : false; // khusus currency selain IDR
        $useCostReconsile = $this->loadSetting('usePrepaidExpense');

        
        // kalo dari invoice reimburse dan ad settingannya gk bentuk GL
        // jgn berdarsarkan jenis invice, tp berdasarkan jenis PE nya, dari reimburse atau bkn
        // 1. prioritas pertama dari jenis PE, apakah jenis reimburse atau bukan (utk CIF)
        // 2. lalu cek dari jenis charges apakah jenis reimburse atau bkn (utk thewhale) TIDAK BISA, karena reimbursenya semua gk diset = 1
        // jadi sementara utk omit GL, hanya berlaku utk tipe PE reimburse saja dulu
            
        //$tableKey = $EMKLOrderInvoice->getTableKeyAndObj($EMKLOrderInvoice->tableName,array('key'))['key']; 
        //if($rs[0]['reftabletype'] == $tableKey){ 
        //    $rsInvoice = $EMKLOrderInvoice->searchData($EMKLOrderInvoice->tableName.'.pkey', $rs[0]['refkey'],true); 
        //    if($rsInvoice[0]['isreimburse'] == 1){
        //        $omitGLFromCostReconsileReimburse = $this->loadSetting('omitGLFromCostReconsileReimburse');
        //        if ($omitGLFromCostReconsileReimburse == 1) return;  
        //    }
        //}
        
        
        $totalPayment = 0;
        
        $warehousekey = $rs[0]['warehousekey'];
        //$rate = (!empty($rs[0]['rate'])) ? $rs[0]['rate'] : 1;
        
            
        $rsKey = $generalJournal->getTableKeyAndObj($this->tableName); 
		$arr = array();
		$arr['pkey'] = $generalJournal->getNextKey($generalJournal->tableName);
		$arr['code'] = 'xxxxx';
		$arr['refkey'] = $rs[0]['pkey'];
		$arr['refTableType'] = $rsKey['key'];
		$arr['trDate'] =   $this->formatDBDate($rs[0]['trdate'],'d / m / Y'); 
		$arr['createdBy'] = 0; 
        $arr['selWarehouseKey'] = $rs[0]['warehousekey'];
        
        // desc
        $desc = array(); 
		array_push($desc,$rs[0]['refcode']);
        if(!empty($rs[0]['trnotes'])) array_push($desc,$rs[0]['trnotes']);
		$arr['trDesc'] = implode(chr(13),$desc);
		
		$temp = -1;
             
        $prepaidExpense = $this->getPrepaidExpenseObj();

        $rsDetail = $this->getDetailById($rs[0]['pkey']);
        
        
        $rsReconsileCol = $prepaidExpense->searchDataRow(array($prepaidExpense->tableName.'.pkey',
															   $prepaidExpense->tableName.'.amountidr', 
															   $prepaidExpense->tableName.'.amount', 
															   $prepaidExpense->tableName.'.rate',
															   $prepaidExpense->tableName.'.costkey', 
															   $prepaidExpense->tableName.'.reftabletype',
															   $prepaidExpense->tableName.'.salesorderkey',
															   $prepaidExpense->tableName.'.isreimburse' 
															  ),
                                      ' and '.$prepaidExpense->tableName.'.pkey in ('.$this->oDbCon->paramString(array_column($rsDetail,'refreconsilekey'),',').')'
                                     );
        
		$arrSalesOrderkey = array_column($rsReconsileCol,'salesorderkey');
        $rsReconsileCol = array_column($rsReconsileCol,null,'pkey');

        //$rsCOATemporaryCost = $coaLink->getCOALink ('outsourcecost', $warehouse->tableName, $warehousekey); 

		// kalo COA dipisah per kategori pekerjaan
		$costByJobCategory = $this->loadSetting('splitCOAByJobCategory'); 
 
		if($costByJobCategory == 1){
			$emklJobOrder = new EMKLJobOrder();
			$rsJOCol = $emklJobOrder->searchDataRow(array($emklJobOrder->tableName.'.pkey',$emklJobOrder->tableName.'.jobtypekey',$emklJobOrder->tableName.'.loadcontainertypekey'),
								  ' and '.$emklJobOrder->tableName.'.pkey in ('.$this->oDbCon->paramString($arrSalesOrderkey,',').')' 
								  ); 
			$rsJOCol = array_column($rsJOCol,null,'pkey');
		}
		
            
        
        foreach($rsDetail as $key=>$rowDetail){  
            $rsCostReconsile = $rsReconsileCol[$rowDetail['refreconsilekey']]; 
            
            // skip karena dari buyingnya reimburse
            if($rsCostReconsile['isreimburse'] == 1) continue;
			
			$eximkey = 0;
			$jobcategorykey = 0;
				
			if($costByJobCategory == 1){ 
				$rsJO = $rsJOCol[$rsCostReconsile['salesorderkey']];
				$eximkey = $rsJO['jobtypekey'];
				$jobcategorykey = $rsJO['loadcontainertypekey'];
			}
			
            $totalReconsile = ($rowDetail['amount'] * $rsCostReconsile['rate']);
            $temp++; 
            $arr['hidCOAKey'][$temp] =  $service->getCostCOAKey($rsCostReconsile['costkey'],$warehousekey,'outsourcecost',false,$eximkey,$jobcategorykey);  
            $arr['debit'][$temp] = $totalReconsile; 
            $arr['credit'][$temp] =  0; 
        }

        $arrItemCOA = array();
        foreach($rsDetail as $key=>$rowDetail ){   
			$rsCostReconsile = $rsReconsileCol[$rowDetail['refreconsilekey']]; 
			
            // skip karena dari buyingnya reimburse
            if($rsCostReconsile['isreimburse'] == 1) continue;
            
			$eximkey = 0;
			$jobcategorykey = 0;
			
			if($costByJobCategory == 1){ 
				$rsJO = $rsJOCol[$rsCostReconsile['salesorderkey']];
				$eximkey = $rsJO['jobtypekey'];
				$jobcategorykey = $rsJO['loadcontainertypekey'];
			}
			
			
			$itemCOAKey = $service->getCostCOAKey($rsCostReconsile['costkey'],$warehousekey,'prepaidexpense',$useCostReconsile,$eximkey,$jobcategorykey);  
			$costAmount = ($rowDetail['amount'] * $rsCostReconsile['rate']);
			$arrItemCOA[$itemCOAKey] = (!isset($arrItemCOA[$itemCOAKey])) ? $costAmount : $arrItemCOA[$itemCOAKey] + $costAmount;  
        }
         
        foreach ($arrItemCOA as $coakey => $coaValue){ 
            $temp++;
            $arr['hidCOAKey'][$temp] = $coakey;
            $arr['debit'][$temp] = 0; 
            $arr['credit'][$temp] = $coaValue;  
        }

        
        // nanti dicek kalo semua COA nya akunnya sama semua dan totalnya 0
        $flag = true;
        
        // biari saja dulu, nanti malah bingung kalo gk ad jurnal sama sekali
        // utk case CIF, pake settingan aj, kal odari invocie reimbruse jgn muncul jurnal
        
//        if (count(array_unique($arr['hidCOAKey'])) == 1){
//            
//             $amount = 0;
//             for ($i=0;$i<count($arr['hidCOAKey']); $i++) 
//                    $amount += ($arr['credit'][$i] > 0 ) ?  ($arr['credit'][$i] * -1) :  $arr['debit'][$i]; 
//
//            $this->setLog('debit ' . $arr['debit'][$i],true);
//            $this->setLog('credit ' . $arr['credit'][$i],true);
//            $this->setLog($amount,true);
//            if ($amount == 0) $flag = false;
//        }
         
            
        if ($flag){
            $arrayToJs = $generalJournal->addData($arr);

            if (!$arrayToJs[0]['valid'])
                    throw new Exception('<strong>'.$rs[0]['code'] . '</strong>. '.$this->errorMsg[504].' '.$arrayToJs[0]['message']); 
            
        }
        
        

    }
     
    
    function getPrepaidExpenseObj(){
        return new PrepaidExpense();
    }
    
    function normalizeParameter($arrParam, $trim = false){   
		
		$emklOrderInvoice = new EMKLOrderInvoice();
		
		// sementara tablekey tembak mati dr emkl dulu, nanti baru buat pilihan
		$rsObjKey = $this->getTableKeyAndObj($emklOrderInvoice->tableName, array('key')); 
        $arrParam['reftabletype'] = $rsObjKey['key'];
                   
		// remove uncheck 
        $this->removeUnCheckRows($arrParam,$this->arrDataDetail);
    
        $reCountResult = $this->reCountGrandTotal($arrParam); 
        $arrParam['total'] = $reCountResult['grandtotal'];
        $arrParam['profitLoss'] = $reCountResult['profitloss'];
        
		
		$arrParam = parent::normalizeParameter($arrParam,true);
		
        return $arrParam;
    }

	
    function getDetailWithRelatedInformation($pkey,$criteria=''){
      $reconsileObj = $this->getPrepaidExpenseObj();
        
      $sql = 'select
	   			'.$this->tableNameDetail .'.*,
                '.$reconsileObj->tableName.'.code as pecode , 
                '.$reconsileObj->tableName.'.trdate as podate ,
                '.$reconsileObj->tableName.'.refcode,
                '.$reconsileObj->tableName.'.amount as reconcilamount,
                '.$this->tableItem.'.name as servicename
				
			  from
			  	'. $this->tableNameDetail .',
                '.$reconsileObj->tableName.',
                '.$this->tableItem.'
			  where
			  	'. $this->tableNameDetail .'.refreconsilekey = '.$reconsileObj->tableName.'.pkey and
			  	'. $reconsileObj->tableName .'.costkey = '.$this->tableItem.'.pkey and
			  	'. $this->tableNameDetail .'.refkey in('.$this->oDbCon->paramString($pkey,',').') ';
         
       
        $sql .= $criteria; 
   
        return $this->oDbCon->doQuery($sql);
   } 

    function updateProfitLoss()
    {
        $sql = '
            select
                '. $this->tableName .'.pkey,
                '. $this->tableName .'.code,
                '. $this->tableName .'.refkey,
                '. $this->tableName .'.rate,
                '. $this->tableName .'.currencykey,
                '. $this->tableName .'.grandtotal,
                '. $this->tableInvoice .'.code as invoicecode,
                '. $this->tableInvoice .'.currencykey as invoicecurrencykey,
                '. $this->tableInvoice .'.rate as invoicerate,
                '. $this->tableInvoice .'.grandtotal as invoicegrandtotal,
                '. $this->tableName .'.statuskey
            from
                '. $this->tableName .',
                '. $this->tableInvoice .'
            where
                '. $this->tableName .'.refkey = '. $this->tableInvoice .'.pkey and
                (' . $this->tableName . '.profitloss is null or ' . $this->tableName . '.profitloss = 0) and
                '. $this->tableName .'.statuskey in (1,2,3)
        ';
        
        $rsData =  $this->oDbCon->doQuery($sql);

        if(empty($rsData)) return;

        foreach($rsData as $dataRow) {

            $pkey = $dataRow['pkey'];
            $totalProfitLoss = 0;   

            $totalInvoice = $dataRow['invoicegrandtotal'];
            $currInvoice = $dataRow['invoicecurrencykey'];
            $rateInvoice = $dataRow['invoicerate'];

            $totalCostRecon = $dataRow['grandtotal'];
            $currCostRecont = $dataRow['currencykey'];
            $rateCostRecont = $dataRow['rate'];

            if($currInvoice != CURRENCY['idr']) {
                $totalInvoice *= $rateInvoice;
            }

            if($currCostRecont != CURRENCY['idr']) {
                $totalCostRecon *= $rateCostRecont;
            }

            $totalProfitLoss = $totalInvoice - $totalCostRecon;
            
            $sql = '
                UPDATE
                    '. $this->tableName .'
                SET
                    profitloss = '. $this->oDbCon->paramString($totalProfitLoss) .'
                WHERE
                    '. $this->tableName.'.pkey = '. $this->oDbCon->paramString($pkey) .'
            ';

            $this->oDbCon->execute($sql);

        }   

    }
 function getJobInformation($arrPkey){
        // untuk laporan buku besar
     
        $sql = 'select distinct
                 '.$this->tableJobOrder.'.pkey as jokey,
                 '.$this->tableJobOrder.'.code as jocode,
                 '.$this->tableName.'.pkey as reftablekey 
                from  
                 '.$this->tableJobOrder.', 
                 '.$this->tableName.',
                 '.$this->tableNameDetail.' ,
                 '.$this->tablePrepaidExpense.' 
                where  
                    '.$this->tableName.'.pkey in ('.$this->oDbCon->paramString($arrPkey,',').') and
                    '.$this->tableName.'.pkey = '.$this->tableNameDetail.'.refkey and
                    '.$this->tableNameDetail.'.refreconsilekey = '.$this->tablePrepaidExpense.'.pkey and
                    '.$this->tablePrepaidExpense.'.salesorderkey = '.$this->tableJobOrder.'.pkey
              ';
          
        $rs = $this->oDbCon->doQuery($sql);
        
        return $rs;
    }
 
	
}
?>