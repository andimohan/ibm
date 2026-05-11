<?php

class DebitNote extends BaseClass{
	
    function __construct(){

    parent::__construct();

 
    $this->tableName = 'debit_note_header';
    $this->tableNameDetail = 'debit_note_detail';
    $this->tablePayment= 'debit_note_payment';
    $this->tableNameDetailJO = 'debit_note_detail_job_order';

    $this->tablePurchaseHeader = 'emkl_purchase_order_header';
    $this->tablePurchaseDetail = 'emkl_purchase_order_detail';
    $this->tableCommissionHeader = 'emkl_commission_header';
    $this->tableCommissionDetail = 'emkl_commission_detail'; 
    $this->tableEMKLJobOrderHeader= 'emkl_job_order_header';  
    $this->tableEMKLJobOrderDetail= 'emkl_job_order_detail'; 
    $this->tableAPCommission = 'ap_commission';   
    $this->tableSupplier = 'supplier';
    $this->tableWarehouse = 'warehouse';   
    $this->tableCurrency = 'currency';   
    $this->tableCost = 'item';
    $this->tableStatus = 'transaction_status';
    $this->isTransaction = true; 
    $this->newLoad = true;  
    $this->tableAP = 'ap';  
    $this->securityObject = 'DebitNote';

    $this->activeModule = $this->isActiveModule(array('emkljoborder'));
        
    $this->arrPaymentDetail = array(); 
    $this->arrPaymentDetail['pkey'] = array('hidDetailPaymentKey');
    $this->arrPaymentDetail['refkey'] = array('pkey', 'ref');
    $this->arrPaymentDetail['amount'] = array('paymentMethodValue',array('datatype' => 'number','mandatory'=>true));
    $this->arrPaymentDetail['paymentkey'] = array('selPaymentMethod'); 
    $this->arrPaymentDetail['cashbankvoucherkey'] = array('selVoucher');


    $this->arrDataDetail = array(); 
    $this->arrDataDetail['pkey'] = array('hidDetailKey');
    $this->arrDataDetail['refkey'] = array('pkey','ref'); 
// jd gk wajib AP
	//$this->arrDataDetail['refapkey'] = array('hidRefAPKey',array('mandatory'=>true));
    $this->arrDataDetail['refapkey'] = array('hidRefAPKey');
	$this->arrDataDetail['refpurchasekey'] = array('hidRefPurchaseKey');
	$this->arrDataDetail['refpurchasetabletype'] = array('hidRefPurchaseTableType');
	$this->arrDataDetail['totaldebit'] = array('debitTotal',array('datatype'=>'number','mandatory'=>true));
//    $this->arrDataDetail['invoicekey'] = array('hidInvoiceKey'); // bisa ke Refund atau Purchase
    $this->arrDataDetail['reftabletype'] = array('hidRefTableType');
    $this->arrDataDetail['refcode'] = array('refCode');
    $this->arrDataDetail['refpurchasecode'] = array('refPurchaseCode');
    $this->arrDataDetail['rate'] = array('rate','number');
    $this->arrDataDetail['refapdate'] = array('refAPDate','date');
    $this->arrDataDetail['aptotal'] = array('amount','number');
    $this->arrDataDetail['costkey'] = array('hidCostKey');


    $arrDetails = array(); 
    array_push($arrDetails, array('dataset' => $this->arrDataDetail, 'tableName' => $this->tableNameDetail));
    array_push($arrDetails, array('dataset' => $this->arrPaymentDetail, 'tableName' => $this->tablePayment));
        
    $this->arrData = array(); 
    $this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails));  
    $this->arrData['code'] = array('code');
    $this->arrData['trdate'] = array('trDate','date');
    $this->arrData['supplierkey'] = array('hidSupplierKey');
    $this->arrData['warehousekey'] = array('selWarehouseKey');
    $this->arrData['trdesc'] = array('trDesc');
    $this->arrData['statuskey'] = array('selStatus');
    $this->arrData['grandtotal'] = array('grandTotal','number');
    $this->arrData['totalpayment'] = array('totalPayment','number');
	$this->arrData['currencykey'] = array('selCurrency');
	$this->arrData['dntype'] = array('selDNType');
    $this->arrData['rate'] = array('currencyRate','number');
    $this->arrData['joborderkey'] = array('hidJobOrderKey');
    $this->arrData['termofpaymentkey'] = array('selTermOfPayment');
        
        
            
    array_push($this->filterCriteria, array('title' => $this->lang['warehouse'], 'field' => 'warehousekey'));
        
    $this->arrDataListAvailableColumn = array(); 
    array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 120));
    array_push($this->arrDataListAvailableColumn, array('code' => 'date','title' => 'date','dbfield' => 'trdate','default'=>true, 'width' => 100, 'align' =>'center', 'format' => 'date'));
    array_push($this->arrDataListAvailableColumn, array('code' => 'supplier','title' => 'supplier','dbfield' => 'suppliername','default'=>true,'width' => 150));
    array_push($this->arrDataListAvailableColumn, array('code' => 'warehouse','title' => 'warehouse','default'=>true, 'dbfield' => 'warehousename', 'width' => 100));
    array_push($this->arrDataListAvailableColumn, array('code' => 'currency','title' => 'curr','dbfield' => 'currencyname','default'=>true ,'width' => 80, 'align' => 'center'));
    array_push($this->arrDataListAvailableColumn, array('code' => 'total','title' => 'total','dbfield' => 'grandtotal','default'=>true ,'width' => 100, 'align' => 'right', 'format' => 'number'));
    array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
    array_push($this->arrDataListAvailableColumn, array('code' => 'desc','title' => 'note','dbfield' => 'trdesc', 'width' => 200));
    
    $this->printMenu = array();
    array_push($this->printMenu,array('code' => 'printTransaction', 'name' => $this->lang['printTransaction'],  'icon' => 'print', 'url' => 'print/debitNote'));
          
    $this->includeClassDependencies(array( 
        'Currency.class.php', 
        'Supplier.class.php',  
        'AP.class.php',  
        'APCommission.class.php',  
        'Warehouse.class.php',					  
		'ChartOfAccount.class.php', 
        'COALink.class.php',  
        'EMKLJobOrder.class.php',  
        'EMKLPurchaseOrder.class.php',  
        'EMKLCommission.class.php',  
        'GeneralJournal.class.php',
        'CashBank.class.php',   
        'Item.class.php',
        'Service.class.php',
        'TermOfPayment.class.php'
    ));  
    
    }

    function getQuery(){

        $sql = '
            SELECT
                '.$this->tableName.'.* , 
                '.$this->tableWarehouse.'.name as warehousename,
                 '.$this->tableSupplier.'.name as suppliername, 
                '.$this->tableStatus.'.status as statusname,
                '.$this->tableCurrency.'.name as currencyname 
            FROM '.$this->tableStatus.',
                '.$this->tableWarehouse.',
                 '.$this->tableName.' 
                 left join '.$this->tableSupplier.' on '.$this->tableName.'.supplierkey = '.$this->tableSupplier.'.pkey 
                 left join '.$this->tableCurrency.' on '.$this->tableName.'.currencykey = '.$this->tableCurrency.'.pkey
            WHERE 
                  '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey and 
                  '.$this->tableName.'.warehousekey = '.$this->tableWarehouse.'.pkey
                ' .$this->criteria ;
            
        $sql .=  $this->getWarehouseCriteria() ;
         
        $sql .= ' group by '.$this->tableName.'.pkey ';
                    
        return $sql;
    }
  

    function reCountGrandTotal($arrParam){
           
        $transactionObj = $this->getTransactionObject();
            
        $grandTotal = 0; 
 

        if($arrParam['selDNType'] == DN_TYPE['debitMemo'])
            $arrApkey = $arrParam['hidCostKey'];  
        else
            $arrApkey = $arrParam['hidRefAPKey']; 

        $arrAmount = $arrParam['debitTotal'];  
         
        for ($i=0;$i<count($arrApkey);$i++){
            $arrAmount[$i] = $this->unFormatNumber($arrAmount[$i]);
            if (empty($arrApkey[$i]) || empty($arrAmount[$i]))  continue;
             
            $grandTotal += $this->unFormatNumber($arrAmount[$i]); 
        }   

        $payment = $arrParam['paymentMethodValue'];
        $totalPayment = 0;
        for($i=0;$i<count($payment);$i++){
            $totalPayment += $this->unFormatNumber($payment[$i]);
        } 


        $reCountResult = array();                       
        $reCountResult['grandTotal'] = $grandTotal;  
		$reCountResult['totalPayment'] = $totalPayment;

        return $reCountResult;
                  

    } 

   function validateForm($arr,$pkey = ''){ 
        
        $transactionObj = $this->getTransactionObject();
        
        $arrayToJs = parent::validateForm($arr,$pkey); 
 

        $dnType = $arr['selDNType'];
        $supplierkey = $arr['hidSupplierKey'];  
        $currencykey = $arr['selCurrency']; 
        $arrPick = $arr['chkPick'];  
        $arrDebit = $arr['debitTotal'];
        $arrAmount = $arr['amount'];
        $arrRefTableTypeKey = $arr['hidRefTableType'];
        $joborderkey = $arr['hidJobOrderKey'];
          
        $arrAPKey = $arr['hidRefAPKey']; 
        $arrCostKey = $arr['hidCostKey'];
        $arrCostName = $arr['costName'];
 
           
        if(empty($supplierkey)) 
            $this->addErrorList($arrayToJs,false,$this->errorMsg['supplier'][1]); 


        if($dnType == DN_TYPE['debitMemo']){
                if(empty($joborderkey)) 
                    $this->addErrorList($arrayToJs,false,$this->errorMsg['jobOrder'][1]);
                
                if(empty($arrCostKey[0]))
                    $this->addErrorList($arrayToJs,false,$this->errorMsg['cost'][1]);
                
                $arrDetailKey = array();
                for($i=0;$i<count($arrCostKey);$i++) {   

                    if ((!empty($arrCostKey[$i]) ) && !empty($arrPick[$i]))  { 

                        if (in_array($arrCostKey[$i],$arrDetailKey)){  
                            $this->addErrorList($arrayToJs,false, $arrCostName[$i].'. '.$this->errorMsg[215]); 	 
                        }else{ 
                            if (!empty($arrCostKey[$i]))  
                                array_push($arrDetailKey, $arrCostKey[$i]);
                        }

                        if($arrDebit[$i] <= 0) 
                            $this->addErrorList($arrayToJs,false,'<strong>'.$arrCostName[$i].'</strong>. ' .$this->errorMsg['debitNote'][1]); 

                    }

                }
        } else{
            // model normal diawal
              if(count($arrAPKey)<=0)  
                $this->addErrorList($arrayToJs,false,$this->errorMsg['debitNote'][4]);

                $arrDetailKey = array();
                // cek ad duplikasi gk, dan cek suppliernya sesuai gk
                for($i=0;$i<count($arrAPKey);$i++) {   

                    if ( (!empty($arrAPKey[$i]) ) && !empty($arrPick[$i]) )  { 

                        $refObj=$this->getObjMapping('',$arrRefTableTypeKey[$i]);
                        $rsSOI = $refObj->getDataRowById($arrAPKey[$i]);
                        $arrDebit[$i] = $this->unFormatNumber($arrDebit[$i]);
                        $arrAmount[$i] = $this->unFormatNumber($arrAmount[$i]);
                        if($currencykey <> $rsSOI[0]['currencykey']){
                            $this->addErrorList($arrayToJs,false, $rsSOI[0]['code'].'. '.$this->errorMsg['creditNote'][4]); 
                        }

                        if (in_array($arrAPKey[$i],$arrDetailKey)){  
                            $this->addErrorList($arrayToJs,false, $rsSOI[0]['code'].'. '.$this->errorMsg[215]); 	 
                        }else{ 
                            if (!empty($arrAPKey[$i]))  
                                array_push($arrDetailKey, $arrAPKey[$i]);
                        }

                        if ($rsSOI[0]['supplierkey'] <> $supplierkey)
                            $this->addErrorList($arrayToJs,false, $rsSOI[0]['code'].'. '.$this->errorMsg['ap'][5]);

                        if($dnType != DN_TYPE['cash']){
                             // kalo cash, gk perlu validasi
                             if ($arrDebit[$i] > $arrAmount[$i]) 
                                $this->addErrorList($arrayToJs,false,$this->errorMsg['debitNote'][2]); 
                        }

                        // harusnya selalu gk boleh minus
                         if($arrDebit[$i] <= 0) 
                                $this->addErrorList($arrayToJs,false,$this->errorMsg['debitNote'][1]); 

                    }

                }
        }


      
        return $arrayToJs;
    }


    function getSourceTransaction($jokey,$debitNoteStatus=array(2,3)){
        
        if (!is_array($jokey))
            $jokey = array($jokey);
        
        /*
            Fungsi untuk mencari detail debit note untuk JO tertentu dari Purchase mana saja
            
            EMKL ad PO dan Commision
            Trucking ad PO
            
            dilempar ke setiap modul yg terdaftar berkaitan dengan debit note ini, masiang2 akan memberikan return yg sama sbb
            
            jokey => pkey jo
            reftabletype => buat nunjukin dari PO EMKL, atau trucking atau commision, dst
            pokey => pkey po
            amount => nilai debit note, utk PO trucking harussny prorate JO nya, PO EMKL semetnara masih di header
        
            
        */
         
    
        // distinct dulu ad berapa jenis reftaletype
        $sql = 'select distinct(refpurchasetabletype) as refpurchasetabletype from ' .$this->tableNameDetail;
        $rs = $this->oDbCon->doQuery($sql);
        
        $arrTablePurchaseType = array_unique(array_column($rs,'refpurchasetabletype'));
          
        $rsDN = array();
        
        foreach($arrTablePurchaseType as $typekey){
            // buat jaga2 saja
            if(empty($typekey)) continue;
            
            $poObj = $this->getObjMapping('',$typekey);  
            $arrPODN = $poObj->getDebitNote($jokey,$debitNoteStatus); // setiap class yg berhubungan dengan debitNote harus ad fungsi ini
             
            $rsDN = array_merge($rsDN, $arrPODN); 
             
        }
        
        $sql = 'select   
                   '.$this->tableName.'.pkey as debitnotekey,
                   '.$this->tableName.'.code debitnotecode,
                   '.$this->tableName.'.statuskey  as debitnotestatuskey,
                   '.$this->tableName.'.currencykey,
                   '.$this->tableName.'.rate,
                   '.$this->tableNameDetail.'.totaldebit,
                   '.$this->tableEMKLJobOrderHeader.'.pkey as sokey,
                   '.$this->tableEMKLJobOrderHeader.'.code as socode,
                   '.$this->tableSupplier.'.name as suppliername 
                from
                   '.$this->tableName.',
                   '.$this->tableNameDetail.',
                   '.$this->tableEMKLJobOrderHeader.',
                   '.$this->tableSupplier.'
                where
                    '.$this->tableName.'.pkey = '.$this->tableNameDetail.'.refkey and
                    '.$this->tableName.'.joborderkey = '.$this->tableEMKLJobOrderHeader.'.pkey and
                    '.$this->tableName.'.supplierkey = '.$this->tableSupplier.'.pkey and
                    '.$this->tableName.'.statuskey in ('.$this->oDbCon->paramString($debitNoteStatus,',') .') and
                    '.$this->tableName.'.joborderkey  in ('.$this->oDbCon->paramString($jokey,',').')
                ';
        
        //$this->setLog($sql,true);
        $rsDebitMemo = $this->oDbCon->doQuery($sql);
        
        $rsDN = array_merge($rsDN, $rsDebitMemo); 
        
            
        // gabungin yg dari debitMemo (gk ad informasi PO)
        //
        // '.$this->tableName.'.pkey as pokey,
        //           '.$this->tableName.'.code as pocode,
        //           '.$this->tableDebitNoteDetail.'.refpurchasetabletype as purchasetabletype,
        //           '.$this->tableJobOrder.'.pkey as sokey,
        //           '.$this->tableJobOrder.'.code as socode,
        //           '.$this->tableDebitNoteHeader.'.pkey as debitnotekey,
        //           '.$this->tableDebitNoteHeader.'.code as debitnotecode,
        //           '.$this->tableDebitNoteHeader.'.statuskey as debitnotestatuskey,
        //           '.$this->tableDebitNoteDetail.'.totaldebit,
        //           '.$this->tableDebitNoteHeader.'.currencykey,
        //           '.$this->tableDebitNoteDetail.'.rate,
        //           '.$this->tableSupplier.'.name as suppliername 
        //               
        
        return $rsDN;

    } 
    
    
    
    
 
     function getDetailWithRelatedInformation($pkey,$criteria=''){
         
         $apTableKey = $this->getTableKeyAndObj($this->tableAP)['key'];
         $apCommissionTableKey = $this->getTableKeyAndObj($this->tableAPCommission)['key'];
         
         
         $sql = 'select
            '.$this->tableNameDetail.'.*,  
            '.$this->tableCost.'.code as costcode,
            '.$this->tableCost.'.name as costname,
            
             CONCAT_WS(\'\','.$this->tableAP.'.refcode, '.$this->tableAPCommission.'.refcode) as refcode,
             CONCAT_WS(\'\','.$this->tableAP.'.code, '.$this->tableAPCommission.'.code) as apcode,
             CONCAT_WS(\'\','.$this->tableAP.'.trdate, '.$this->tableAPCommission.'.trdate) as apdate,
             CONCAT_WS(\'\','.$this->tableAP.'.rate, '.$this->tableAPCommission.'.rate) as rate,
             CONCAT_WS(\'\','.$this->tableAP.'.outstanding, '.$this->tableAPCommission.'.outstanding) as aptotal 
          from
            '.$this->tableNameDetail.'
                left join '.$this->tableCost.' on '.$this->tableNameDetail.'.costkey = '.$this->tableCost.'.pkey
                left join  '.$this->tableAP.' on '.$this->tableNameDetail.'.reftabletype =  '.$this->oDbCon->paramString($apTableKey) . ' 
                        and '. $this->tableNameDetail.'.refapkey = '.$this->tableAP.'.pkey  
                
                left join  '.$this->tableAPCommission.' on '.$this->tableNameDetail.'.reftabletype =  '.$this->oDbCon->paramString($apCommissionTableKey) . ' 
                        and '. $this->tableNameDetail.'.refapkey = '.$this->tableAPCommission.'.pkey  
                 
                
          where  
            '. $this->tableNameDetail.'.refkey  = '.$this->oDbCon->paramString($pkey);

        $sql .= $criteria;
 
        return $this->oDbCon->doQuery($sql);

    } 
    
    
    function getAPDetail($rs){ 
        //format minimal. pkey dan reftabletype
        
        // grouping berdsarakan tabletype
        
        $rsAPCol = array();
        
        $rs = $this->reindexDetailCollections($rs,'reftabletype');
        foreach($rs as $tablekey=>$refTableRow){
            
            $refObj=$this->getObjMapping('',$tablekey);
            if($refObj == null) continue;
            
            $apkey = array_column($refTableRow,'refapkey');
                  
            //tablekey dan reftabletype berbeda
            //tablekey =>  tablekey utk cari dari AP atau AP Commission
            //reftabletype => tabletype dari table AP
            
            $rsAP = $refObj->searchDataRow( array($refObj->tableName.'.pkey',
                                                  $refObj->tableName.'.code',
                                                  $refObj->tableName.'.outstanding',
                                                  $refObj->tableName.'.refheaderkey',
                                                  $refObj->tableName.'.currencykey',
                                                  $refObj->tableName.'.rate',
                                                  $refObj->tableName.'.statuskey',
                                                  $tablekey.' as tablekey',
                                                  $refObj->tableName.'.reftabletype',
                                                  'concat('.$refObj->tableName.'.pkey,\'-'.$tablekey.'\') as indexkey'),
								    ' and ' .$refObj->tableName.'.pkey in ('.$this->oDbCon->paramString($apkey,',').')'
								   );
            
            $rsAPCol = array_merge($rsAPCol, $rsAP);
        }
         
        return $rsAPCol;
    }
    
    function normalizeParameter($arrParam, $trim=false){
         
        // remove uncheck 
        $this->removeUnCheckRows($arrParam,$this->arrDataDetail);
        
        //kalo AP, hapus semua payment
        if($arrParam['selDNType'] == DN_TYPE['ap']){
            $arrParam['hidDetailPaymentKey'] = array();
            $arrParam['paymentMethodValue'] = array();
            $arrParam['currencyRate'] = 0;
        }
        
    	$reCountResult = $this->reCountGrandtotal($arrParam); 
        $arrParam['grandTotal'] = $reCountResult['grandTotal'];
        $arrParam['totalPayment'] = $reCountResult['totalPayment'];
		
		//update ulang informasi invoicekey, agar join lebih mudah
		// $ap = new AP();
        if($arrParam['selDNType'] == DN_TYPE['debitMemo']){
            // update refpurchasekey di setial detail
            // gk ad informasi PO di debit memo 
            //$emklPUrchaseOrder = new EMKLPurchaseOrder();
            //$rsPO = $emklPUrchaseOrder->searchDataRow(array($emklPUrchaseOrder->tableName.'.pkey',
            //                                             $emklPUrchaseOrder->tableName.'.reftabletype'
            //                                            ),
            //                                        ' and '.$emklPUrchaseOrder->tableName.'.pkey = '
            //                                         );
            //
            //$totalDetail = count($arrParam['hidCostKey'][$i]);
            //
            //for($i=0;$i<$totalDetail;$i++) {   
            //    $arrParam['rate'][$i] = $this->unFormatNumber($arrParam['currencyRate']); 
            //    $arrParam['hidRefPurchaseKey'][$i] = $rsAP[$indexkey]['refheaderkey'];
            //    $arrParam['hidRefPurchaseTableType'][$i] = $rsAP[$indexkey]['reftabletype']; 
            //}
            
        }else{
            $arrAPKey = $arrParam['hidRefAPKey'];
            $arrRefTableType = $arrParam['hidRefTableType'];

            $arrAP = array();
            for($i=0;$i<count( $arrAPKey );$i++) 
                array_push($arrAP, array('refapkey' => $arrAPKey[$i], 'reftabletype' => $arrRefTableType[$i]));

            $rsAP = $this->getAPDetail($arrAP);
            $rsAP = array_column($rsAP,null, 'indexkey');

            for($i=0;$i<count( $arrAPKey );$i++) {  
                $indexkey = $arrAPKey[$i].'-'.$arrRefTableType[$i]; 
                $arrParam['rate'][$i] = $rsAP[$indexkey]['rate'];
                $arrParam['hidRefPurchaseKey'][$i] = $rsAP[$indexkey]['refheaderkey'];
                $arrParam['hidRefPurchaseTableType'][$i] = $rsAP[$indexkey]['reftabletype']; 
            }

        }

        $arrParam = parent::normalizeParameter($arrParam,true); 
         
        return $arrParam;
    }


    function validateConfirm($rsHeader){  
        $id = $rsHeader[0]['pkey']; 
        
        $obj = $this->getTransactionObject();
        
        $rsDetail = $this->getDetailById($id);
        $currencykey = $rsHeader[0]['currencykey'];
         
        if($rsHeader[0]['dntype'] == DN_TYPE['debitMemo']){

        }else{
            $rsAPCol = $this->getAPDetail($rsDetail);
            $rsAPReindexTableKey = $this->reindexDetailCollections($rsAPCol,'tablekey');         
            $rsAPIndex = array_column($rsAPCol,null,'indexkey');

            //        $this->setLog('$rsAPIndex >>',true);
            //        $this->setLog($rsAPIndex,true);

            $rsExistingDNCol = array();

            foreach($rsAPReindexTableKey as $tablekey => $rsAP){
                $apKeys = array_column($rsAP,'pkey');

                $rsExistingDN = $this->getDebitNoteDetailByAP($apKeys,$tablekey, true,array(DN_TYPE['ap']));
                $rsExistingDNCol = array_merge($rsExistingDNCol, $rsExistingDN); 
            }

            $rsExistingDN = array_column($rsExistingDNCol, null,'indexkey');


             foreach($rsDetail as $detailRow){
                    $apkey = $detailRow['refapkey'];  
                    $indexkey =  $apkey.'-'.$detailRow['reftabletype'];

                    $apRow = $rsAPIndex[$indexkey]; 

                    if($currencykey<>$apRow['currencykey'])
                        $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].' - '.$apRow['code'].'</strong>. ' . $this->errorMsg['creditNote'][4]);

                    // khusus kalo potong AP
                    if($rsHeader[0]['dntype'] == DN_TYPE['ap']){

                        // credit note kalo konfirmasi, invoice nya harus konfirmasi atau selesai
                        if($apRow['statuskey'] > AP_STATUS['partial'])
                            $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].' - '.$apRow['code'].'</strong>. ' . $this->errorMsg['ap'][2]);

                        $totalExistingDN = (isset($rsExistingDN[$indexkey])) ? $rsExistingDN[$indexkey]['totaldebit'] : 0;

                        if ( ($detailRow['totaldebit'] + $totalExistingDN ) > $apRow['outstanding']) 
                            $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].' - '.$apRow['code'].'</strong>.  '.$this->errorMsg['debitNote'][2]);  
                    }

                     // harusnya selalu gk boleh negatif
                    if($detailRow['totaldebit'] <= 0) 
                        $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>.  '.$this->errorMsg['debitNote'][1]); 


             }

        }

        
    

         if($rsHeader[0]['dntype'] == DN_TYPE['cash']){
            // hitung balance
            // harus sama mirip, gk boelh ad seslisih, karena agak sulit menentukaan rate

            $rsPayment = (ADV_FINANCE && TEST_VOUCHER) ?  $this->getPaymentVoucherDetail($id,'',2) : $this->getPaymentMethodDetail($id); 
            $totalPayment = 0; 
            for($i=0;$i<count($rsPayment); $i++)
                $totalPayment += $rsPayment[$i]['amount'];

            $balance = $totalPayment - $rsHeader[0]['grandtotal'];  

            $thresholdDiscount = 0 ; //abs($this->loadSetting('roundedPaymentThreshold'));
            if($balance < ($thresholdDiscount * -1)) 
                $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg[502]);
            else if ($balance > $thresholdDiscount)
                $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg[509]); 

         }
 
       
        
        if (USE_GL){
            $coaLink = new COALink();
            $warehouse = new Warehouse();
            $arrCOA = array();
            array_push($arrCOA, 'ap'); 
            for ($i=0;$i<count($arrCOA);$i++){
                $rsCOA = $coaLink->getCOALink ($arrCOA[$i], $warehouse->tableName,$rsHeader[0]['warehousekey'], 0); 
                if (empty($rsCOA))	
                    $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$arrCOA[$i]. ' ' .$this->errorMsg['coa'][3]);
            }     
         } 
        
//          $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>'); 
        
    } 

    function confirmTrans($rsHeader){ 

        $id = $rsHeader[0]['pkey'];

        $warehouse = new Warehouse();
        $rsDetail = $this->getDetailWithRelatedInformation($id);
        $rsPayment = array();
        
         
        if($rsHeader[0]['dntype'] == DN_TYPE['ap']){
            $ap = new AP();  
            
            $rsDetailReindex = $this->reindexDetailCollections($rsDetail,'reftabletype');
 
            $rate = 1;
            if($rsHeader[0]['currencykey'] <> CURRENCY['idr']){
                //cari rate yg bukan 1
                foreach($rsDetail as $detailRow)
                    if ($detailRow['rate']<>1){
                        $rate = $detailRow['rate'];
                        break;
                    }
            }
            

            $rsAPKey = $ap->getTableKeyAndObj($this->tableName);  
            $arrParam = array();	

            $arrParam['code'] = 'xxxxxx';
            $arrParam['hidSupplierKey'] = $rsHeader[0]['supplierkey']; 
            $arrParam['hidRefKey'] = $rsHeader[0]['pkey'];
            $arrParam['hidRefHeaderKey'] = $rsHeader[0]['pkey'];
            $arrParam['hidRefCode'] =  $rsHeader[0]['code']; 
            
            
//            $arrParam['hidRefCode2'] =  $rsHeader[0]['donumber'];
            $arrParam['hidRefDate'] =   $this->formatDBDate($rsHeader[0]['trdate'],'d / m / Y'); 
            $arrParam['hidRefTable'] = $rsAPKey['key'];
            //$arrParam['amount'] = -$rsHeader[0]['grandtotal'];
            $arrParam['trDesc'] = $rsHeader[0]['code'];
            $arrParam['trDate'] =  $this->formatDBDate($rsHeader[0]['trdate'],'d / m / Y');  
            $date = new DateTime($rsHeader[0]['trdate']);
            $arrParam['dueDate'] = $date->format('d / m / Y');// date ('d / m / Y', mktime(0, 0, 0, date("m")  , date("d")+$rsTOP[0]['duedays'], date("Y")));
            $arrParam['createdBy'] = 0;
            $arrParam['overwriteGL'] = 1;
            $arrParam['islinked'] = 1;
            $arrParam['selAPType'] = AP_TYPE['debitNote'];
            $arrParam['selWarehouse'] = $rsHeader[0]['warehousekey'];   
            $arrParam['selCurrency'] = $rsHeader[0]['currencykey'];
            $arrParam['currencyRate'] = $rate;  // sementara ambil rate pertama aj dulu

             
            // ambil semua jokey dn code dulu
//            $rsJo = $this->getJobOrderDetail($rsHeader[0]['pkey']);
//            $this->setLog($rsJo,true);
            
//            $arrJOKey = array();
//            foreach($arrJOKey as $joIndexRow) 
//                foreach($joIndexRow as $joRow)
//                    array_push($arrJOKey, $joRow['sokey']);
            
//            $emklJobOrder = new EMKLJobOrder();
//            $rsJO = $emklJobOrder->searchDataRow(array($emklJobOrder->tableName.'.pkey',$emklJobOrder->tableName.'.code'),
//                                                 ' and ' . $emklJobOrder->tableName.'.pkey in ('.$this->oDbCon->paramString($arrJOKey,',').')');
//            $rsJO = array_column($rsJO,'pkey');
            
            foreach($rsDetailReindex as $key=>$detailRow) {
                $amount = 0;
                $refObj=$this->getObjMapping('',$key);
                for ($i=0; $i<count($detailRow); $i++) {
                    $amount += $this->unFormatNumber($detailRow[$i]['totaldebit']); 
                }
                $arrParam['amount'] = -$amount;
                 

                $returnVal = $refObj->addData($arrParam,false); 
            }
            
        }else if($rsHeader[0]['dntype'] == DN_TYPE['debitMemo'] || $rsHeader[0]['dntype'] == DN_TYPE['cash']) {
            $termOfPayment = new TermOfPayment();
            $rsTOP = $termOfPayment->getDataRowById($rsHeader[0]['termofpaymentkey']); 
            $isCash = ($rsTOP[0]['duedays'] == 0) ? true : false; 
            $top = (empty($rsTOP)) ? 0 : $rsTOP[0]['duedays'];
            //without ap

            if(!$isCash) {

                $ap = new AP();  
                $emklJobOrder = new EMKLJobOrder();

                $rsKey = $this->getTableKeyAndObj($this->tableName);  

                $rate = (!empty($rsHeader[0]['rate'])) ? $rsHeader[0]['rate'] : 1;
                $amount = $rsHeader[0]['grandtotal'];
                $amount *= -1;
                
                $rsJO = $emklJobOrder->getDataRowById($rsHeader[0]['joborderkey']);

                $arrParam = array();	

                $arrParam['code'] = 'xxxxxx';
                $arrParam['hidSupplierKey'] = $rsHeader[0]['supplierkey']; 
                $arrParam['hidRefKey'] = $rsHeader[0]['pkey'];
                $arrParam['hidRefKey2'] = $rsJO[0]['pkey']; 
                $arrParam['hidRefHeaderKey'] = $rsHeader[0]['pkey'];
                $arrParam['hidRefCode'] =  $rsHeader[0]['code']; 
                $arrParam['hidRefCode2'] =  $rsJO[0]['code'];
                $arrParam['hidRefDate'] =   $this->formatDBDate($rsHeader[0]['trdate'],'d / m / Y'); 
                $arrParam['hidRefTable'] = $rsKey['key'];
                $arrParam['trDesc'] = $rsHeader[0]['code'];
                $arrParam['trDate'] =  $this->formatDBDate($rsHeader[0]['trdate'],'d / m / Y');  
                $date = new DateTime($rsHeader[0]['trdate']);
                $date->add(new DateInterval('P'.$rsTOP[0]['duedays'].'D'));
                $arrParam['dueDate'] = $date->format('d / m / Y');
                $arrParam['amount'] =  $amount; 
				$arrParam['amountIDR'] =  $amount * $rate; 
                $arrParam['createdBy'] = 0;
                $arrParam['overwriteGL'] = 1;
                $arrParam['islinked'] = 1;
                $arrParam['selAPType'] = AP_TYPE['debitNote'];
                $arrParam['selWarehouse'] = $rsHeader[0]['warehousekey'];   
                $arrParam['selCurrency'] = $rsHeader[0]['currencykey'];
                $arrParam['currencyRate'] = $rate;

                $arrayToJs = $ap->addData($arrParam,false); 

                if (!$arrayToJs[0]['valid'])
                    throw new Exception('<strong>'.$rsHeader[0]['code'] . '</strong>.<br>'.$this->lang['ap'].'. '.$arrayToJs[0]['message']);    

            }else{
                 
                 $coaLink = new COALink(); 

                 $rsPayment = $this->getPaymentMethodDetail($id);
                 for($i=0;$i<count($rsPayment); $i++){   
                    if (USE_GL) {
                       $rsPaymentCOA = $coaLink->getCOALink ('payment', $warehouse->tableName,$rsHeader[0]['warehousekey'], $rsPayment[$i]['paymentkey']); 
                       $coakey = $rsPaymentCOA[0]['coakey']; 
                   }else{
                       $coakey = $rsPayment[$i]['paymentkey'];
                   }

                   if( $this->isActiveModule('CashBank') ){
                            $cashBank = new CashBank(); 
                             if (USE_GL) {
                               $rsPaymentCOA = $coaLink->getCOALink ('payment', $warehouse->tableName,$rsHeader[0]['warehousekey'], $rsPayment[$i]['paymentkey']); 
                               $coakey = $rsPaymentCOA[0]['coakey']; 
                             }else{
                               $coakey = $rsPayment[$i]['paymentkey'];
                             }


                            //$cashMovement->updateCashMovement($id, $coakey,-$rsPayment[$i]['amount'], $this->tableName, $rsHeader[0]['warehousekey'], $notecash,$rsHeader[0]['trdate']);
                            $rsCashBank = $cashBank->addCashBank($rsHeader,$this->tableName, array('supplierkey' => $rsHeader[0]['supplierkey'],'coakey' => $coakey, 'amount' => $rsPayment[$i]['amount'] )); 
                            $rsPayment[$i]['cashBankKey'] = $rsCashBank['pkey']; 
                        }

                } 
            }

        }

        	
        $this->updateGL($rsHeader, $rsDetail, $rsPayment);
        
    } 
    
     function updateGL($rs,$rsDetail,$rsPayment){
        if (!USE_GL) return;
         
        if ($rs[0]['overwriteGL'] == 1) return;
         
        $coaLink = new COALink(); 
        $warehouse = new Warehouse();  
        $generalJournal = new GeneralJournal();
        $supplier = new Supplier();
        $ap = new AP();
        $emklJobOrder = new EMKLJobOrder();
        $service = new Service(SERVICE);
        
        $multiCurrency = ($rs[0]['currencykey'] != CURRENCY['idr']) ? true : false; // khusus currency selain IDR
         
        $warehousekey = $rs[0]['warehousekey']; 
        $rsSupplier = $supplier->getDataRowById($rs[0]['supplierkey']);
	
        $rsJO = array();
        $trDesc = array();
        array_push($trDesc,$rsSupplier[0]['name']);
        if($rs[0]['dntype'] == DN_TYPE['debitMemo']){
            $rsJO = $emklJobOrder->getDataRowById($rs[0]['joborderkey']);
            
           array_push($trDesc,$rsJO[0]['code']);
        }
         
		 
        $apCommissionTableKey = $this->getTableKeyAndObj($this->tableAPCommission, array('key'))['key'];
         
        $rsKey = $generalJournal->getTableKeyAndObj($this->tableName);
		$arr = array();
		$arr['pkey'] = $generalJournal->getNextKey($generalJournal->tableName);
		$arr['code'] = 'xxxxx';
		$arr['refkey'] = $rs[0]['pkey'];
		$arr['refTableType'] = $rsKey['key'];
		$arr['trDate'] =  $this->formatDBDate($rs[0]['trdate'],'d / m / Y'); 
		$arr['createdBy'] = 0;
		$arr['refCode'] = $rs[0]['code'];
		//$arr['trDesc'] = $rsSupplier[0]['name'];
        	$arr['trDesc'] = implode('<br>',$trDesc);
		$arr['selWarehouseKey'] = $rs[0]['warehousekey'];
		
		$temp = -1; 
		$totalAP = 0;
        

        $apCOAKey = $supplier->getAPCOAKey($rs[0]['supplierkey'],$warehousekey);
        $apCommissionCOAKey = $supplier->getCommissionCOAKey($rs[0]['supplierkey'],$warehousekey);

         
        // cari informasi AP dulu
         
        // test 
        $rsAP = $this->getAPDetail($rsDetail);
        $rsAP = array_column($rsAP,null, 'indexkey');
            
         // jika potong AP
         if ($rs[0]['dntype'] == DN_TYPE['ap']){
                
                 // DN detailny sudah dimodif 2 level
                foreach($rsDetail as $row){  

                        $indexkey = $row['refapkey'].'-'.$row['reftabletype'];
                        
                        $rate = ($rs[0]['currencykey'] == CURRENCY['idr']) ? 1 : $rsAP[$indexkey]['rate']; 
                        $dnAmount = $row['totaldebit'] * $rate; 

						switch($row['reftabletype']){
							case $apCommissionTableKey : $coakey = $apCommissionCOAKey; break;
							default :$coakey = $apCOAKey; break; 
						}
					
					
                        $temp++; 
                        $arr['hidCOAKey'][$temp] = $coakey;
                        $arr['debit'][$temp] = $dnAmount; 
                        $arr['credit'][$temp] = 0;  
                        $arr['refCashBankKey'][$temp] = 0;
        
                        $totalAP += $dnAmount; 
                }

         }else if($rs[0]['dntype'] == DN_TYPE['cash']){
            // jika terima cash
             
             if(ADV_FINANCE && TEST_VOUCHER) 
                $rsPayment = $this->getPaymentVoucherDetail($rs[0]['pkey'],'',2);

            $totalPaymentAmount = 0;
            $rate = (!empty($rs[0]['rate'])) ? $rs[0]['rate'] : 1;
             
            // hitung total payment 
            for($i=0;$i<count($rsPayment); $i++){ 

                if(ADV_FINANCE && TEST_VOUCHER){ 
                    $rsPayment = $this->getPaymentVoucherDetail($rs[0]['pkey'],'',2);
                    $rsCashBank = $cashBank->getDataRowById($rsPayment[$i]['cashbankvoucherkey']);
                    $rsCOA = $chartOfAccount->getDataRowById($rsCashBank[0]['coakey']);

                    $paymentcoakey = $rsCOA[0]['countercoakey'];
                }else{
                    $rsCOA = $coaLink->getCOALink ('payment', $warehouse->tableName,$warehousekey,$rsPayment[$i]['paymentkey']); 
                    $paymentcoakey = $rsCOA[0]['coakey'];
                }
                 
                 $paymentAmount = $rsPayment[$i]['amount'] * $rate; 

                 $temp++;
                 $arr['hidCOAKey'][$temp] = $paymentcoakey;
                 $arr['debit'][$temp] =$paymentAmount; 
                 $arr['credit'][$temp] = 0 ; 
                 $arr['refCashBankKey'][$temp] = $rsPayment[$i]['cashBankKey'];  
                 $totalPaymentAmount += $paymentAmount;
            }
  
            // hitung total AP dalam rupiah 
            foreach($rsDetail as $row){   
                    $indexkey = $row['refapkey'].'-'.$row['reftabletype'];
                    
                    $rate = ($rs[0]['currencykey'] == CURRENCY['idr']) ? 1 : $rsAP[$indexkey]['rate']; 
                    $totalAP += ($row['totaldebit'] * $rate);  
            }

             
            $totalDifference = $totalAP - $totalPaymentAmount;

            if($totalDifference <> 0){ 
             $rsCOA = $coaLink->getCOALink ('lossprofitrate', $warehouse->tableName,$warehousekey, 0); 
             $temp++;
             $arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];     
             $arr['debit'][$temp] = $totalDifference; 
             $arr['credit'][$temp] = 0;
             $arr['refCashBankKey'][$temp] = 0;  
            }


        } else {

            $termOfPayment = new TermOfPayment();
            $rsTOP = $termOfPayment->getDataRowById($rs[0]['termofpaymentkey']); 
            $isCash = ($rsTOP[0]['duedays'] == 0) ? true : false; 

            //without ap
            $rate = (!empty($rs[0]['rate'])) ? $rs[0]['rate'] : 1;


            $usePrepaidExpense = $this->loadSetting('usePrepaidExpense');
            $usePrepaidExpense = ($usePrepaidExpense == 1) ? true : false;

            $eximTypeKey = $rsJO[0]['jobtypekey'];
            $jobCategoryKey = $rsJO[0]['loadcontainertypekey'];

            if($isCash) {

                if(ADV_FINANCE && TEST_VOUCHER) 
                $rsPayment = $this->getPaymentVoucherDetail($rs[0]['pkey'],'',2);

                $totalPaymentAmount = 0;
             
                // hitung total payment 
                for($i=0;$i<count($rsPayment); $i++){ 

                    if(ADV_FINANCE && TEST_VOUCHER){ 
                        $rsPayment = $this->getPaymentVoucherDetail($rs[0]['pkey'],'',2);
                        $rsCashBank = $cashBank->getDataRowById($rsPayment[$i]['cashbankvoucherkey']);
                        $rsCOA = $chartOfAccount->getDataRowById($rsCashBank[0]['coakey']);

                        $paymentcoakey = $rsCOA[0]['countercoakey'];
                    }else{
                        $rsCOA = $coaLink->getCOALink ('payment', $warehouse->tableName,$warehousekey,$rsPayment[$i]['paymentkey']); 
                        $paymentcoakey = $rsCOA[0]['coakey'];
                    }
                    $paymentAmount = $rsPayment[$i]['amount'] * $rate; 

                    $temp++;
                    $arr['hidCOAKey'][$temp] = $paymentcoakey;
                    $arr['debit'][$temp] =$paymentAmount; 
                    $arr['credit'][$temp] = 0 ; 
                    $arr['refCashBankKey'][$temp] = 0;  
                    $totalPaymentAmount += $paymentAmount;
                }
    
                foreach($rsDetail as $row){         

                    $costkey = $row['costkey'];
                    //$coakey = $service->getCostCOAKey($costkey,$warehousekey,'costcoakey');
                    $coakey = $service->getCostCOAKey($costkey,$warehousekey,'outsourcecost',$usePrepaidExpense,$eximTypeKey,$jobCategoryKey); 
                 
                    $dnAmount = $row['totaldebit'] * $rate; 

                    $temp++;
                    $arr['hidCOAKey'][$temp] = $coakey;
                    $arr['debit'][$temp] = 0; 
                    $arr['credit'][$temp] = $dnAmount ; 
                    $arr['refCashBankKey'][$temp] = 0;  

                    $totalAP += ($row['totaldebit'] * $rate);  
                }

                $totalDifference = $totalAP - $totalPaymentAmount;

                if($totalDifference <> 0){ 
                    $rsCOA = $coaLink->getCOALink ('lossprofitrate', $warehouse->tableName,$warehousekey, 0); 
                    $temp++;
                    $arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];     
                    $arr['debit'][$temp] = $totalDifference; 
                    $arr['credit'][$temp] = 0;
                    $arr['refCashBankKey'][$temp] = 0;  
                }
                
            } else {
              
                 
                foreach($rsDetail as $row){ 
                    $costkey = $row['costkey'];
                    //$coakey = $service->getCostCOAKey($costkey,$warehousekey,'costcoakey');
                    $coakey = $service->getCostCOAKey($costkey,$warehousekey,'outsourcecost',$usePrepaidExpense,$eximTypeKey,$jobCategoryKey); 
                    $dnAmount = $row['totaldebit'] * $rate; 

                    $temp++;
                    $arr['hidCOAKey'][$temp] = $coakey;
                    $arr['debit'][$temp] = $dnAmount; 
                    $arr['credit'][$temp] = 0 ; 
                    $arr['refCashBankKey'][$temp] = 0;  
                    $totalAP += $dnAmount; 
                } 
                
                
                $rsCOA = $coaLink->getCOALink ('ap', $warehouse->tableName,$warehousekey, 0);
                $temp++; 
                $arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey']; 
                $arr['debit'][$temp] = 0; 
                $arr['credit'][$temp] = $totalAP; 
                $arr['refCashBankKey'][$temp] = 0;

            }

    
        }
              

          if($rs[0]['dntype'] == DN_TYPE['debitMemo']){ 
          }else{
                     //akun potongan pembelian  
                    $rsCOA = $coaLink->getCOALink ('purchaseretaildiscount', $warehouse->tableName,$warehousekey, 0);
                    $temp++; 
                    $arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey']; 
                    $arr['debit'][$temp] = 0; 
                    $arr['credit'][$temp] = $totalAP; 
                    $arr['refCashBankKey'][$temp] = 0;
          }
 
 
         //$this->setLog($arr,true);
		$arrayToJs = $generalJournal->addData($arr); 
         
		if (!$arrayToJs[0]['valid'])
                throw new Exception('<strong>'.$rs[0]['code'] . '</strong>. '.$this->errorMsg[504].' '.$arrayToJs[0]['message']);    
 
    }
       
     function validateCancel($rsHeader, $autoChangeStatus = false){
         
        // debit note hanya bisa cancel jika ar statusnya open / cancel
        // dengan kata lain kalo AP statusnya paid / partial, gagal cancel
        
        $id = $rsHeader[0]['pkey'];
  
        $rsDetail = $this->getDetailWithRelatedInformation($rsHeader[0]['pkey']);
        $rsDetail = $this->reindexDetailCollections($rsDetail,'reftabletype');
        $refTableKey = $this->getTableKeyAndObj($this->tableName,array('key')); // harus cari AP yang berasal dari DN ini
          
         

        if($rsHeader[0]['dntype'] == DN_TYPE['debitMemo']) { 
            $ap = new AP();
            $rsAP = $ap->searchData('','',true,' and '.$ap->tableName.'.reftabletype = '.$this->oDbCon->paramString($refTableKey['key']).' and '.$ap->tableName.'.refkey = '.$this->oDbCon->paramString($id).' and ('.$ap->tableName.'.statuskey in (2,3))');
            if(!empty($rsAP)) 
                $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' . $this->errorMsg[201].' ' .$this->errorMsg['ap'][2],true); 

        }else{
          foreach ($rsDetail as $key => $detailRow) {
                    $refObj = $this->getObjMapping('',$key);
                    $rsAP = $refObj->searchData('','',true,' and '.$refObj->tableName.'.reftabletype = '.$this->oDbCon->paramString($refTableKey['key']).' and '.$refObj->tableName.'.refkey = '.$this->oDbCon->paramString($id).' and ('.$refObj->tableName.'.statuskey in (2,3))');

                    if(!empty($rsAP)) 
                        $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' . $this->errorMsg[201].' ' .$this->errorMsg['ap'][2],true);
                } 
        }
      
     }      
    
    function cancelTrans($rsHeader,$copy){  
         
        // kalo cancel debit note, semua AP yg berhubungan harus dicancel jg
        $ap = new AP();
        
		$id = $rsHeader[0]['pkey'];
        $refTableKey = $this->getTableKeyAndObj($this->tableName,array('key')); // harus cari AP yang berasal dari DN ini
 

        if($rsHeader[0]['dntype'] == DN_TYPE['debitMemo']) {
            //cancel AP, DN type without AP
            $rsAP = $ap->searchData('','',true,' and '.$ap->tableName.'.reftabletype = '.$this->oDbCon->paramString($refTableKey['key']).' and '.$ap->tableName.'.refkey = '.$this->oDbCon->paramString($id).' and '.$ap->tableName.'.statuskey = 1');

            for($i= 0;$i<count($rsAP);$i++){
                $arrayToJs = $ap->changeStatus($rsAP[$i]['pkey'],4,'',false, true);
                if (!$arrayToJs[0]['valid'])
                    throw new Exception('<strong>'.$rsHeader[0]['code'] . '</strong>. '.  $arrayToJs[0]['message']);    
            }

        }else{

                $rsDetail = $this->getDetailWithRelatedInformation($rsHeader[0]['pkey']);
                $rsDetail = $this->reindexDetailCollections($rsDetail,'reftabletype');

                foreach ($rsDetail as $key => $detailRow) {
                    $refObj = $this->getObjMapping('',$key);
                    $rsAP = $refObj->searchData('','',true,' and '.$refObj->tableName.'.reftabletype = '.$this->oDbCon->paramString($refTableKey['key']).' and '.$refObj->tableName.'.refkey = '.$this->oDbCon->paramString($id).' and '.$refObj->tableName.'.statuskey = 1');
                    for($i=0;$i<count($rsAP);$i++) {   
                        $arrayToJs = $refObj->changeStatus($rsAP[$i]['pkey'],4,'',false, true);
                        if (!$arrayToJs[0]['valid'])
                            throw new Exception('<strong>'.$rsHeader[0]['code'] . '</strong>. '.  $arrayToJs[0]['message']);    
                    }
                }


                if( $this->isActiveModule('CashBank') ){
                    $cashBank = new CashBank();
                    $cashBank->cancelCashBank($rsHeader,$this->tableName);
                }
        }
       
        
		if ($copy)
			$this->copyDataOnCancel($id);	  
         
        $this->cancelGLByRefkey($rsHeader[0]['pkey'],$this->tableName);
	} 
    
    function getDebitNoteDetailByAP($apkeys, $tablekey,$summary = false, $dntype = array() ){
         
        
        if(!is_array($apkeys)) $apkeys = array($apkeys);
     
        // hitung semua CN yagn sudah diproses,
        // harus cari yg headerny 2 dan 3
        
        $sqlSelect = $this->tableNameDetail.'.totaldebit';
        $sqlGroup = '';
        
        if ($summary) { 
            $sqlSelect = 'coalesce( sum('.$this->tableNameDetail.'.totaldebit) ,0) as totaldebit';
            $sqlGroup = ' group by '.$this->tableNameDetail.'.refapkey';
        }
        
        $sql = 'select 
                        '.$sqlSelect.', 
                        '.$this->tableName.'.code,
                        '.$this->tableNameDetail.'.refapkey,
                        '.$tablekey.' as tablekey, 
                         concat('.$this->tableNameDetail.'.refapkey,\'-'.$tablekey.'\') as indexkey 
                from  '.$this->tableName.', '.$this->tableNameDetail.' 
                where '.$this->tableName.'.pkey =  '.$this->tableNameDetail.'.refkey and
                      '.$this->tableName.'.dntype = '.$this->oDbCon->paramString($dntype,',').' and
                      '.$this->tableName.'.statuskey in (2,3) and
                      '.$this->tableNameDetail.'.refapkey in ('.$this->oDbCon->paramString($apkeys,',').') and
                      '.$this->tableNameDetail.'.reftabletype = '.$this->oDbCon->paramString($tablekey).' 
                ';     
        
        $sql .= $sqlGroup;
        
        $rs = $this->oDbCon->doQuery($sql);
        
//        $this->setLog($rs,true);
        
        return $rs;
    }
      
//    function getDebitNoteByAP($apkey,$criteria=''){
//        
//        $this->setLog('harus cari berdasarkan tabletypekey jg',true);
//        
//        $sql = 'select   
//            '.$this->tableName.'.pkey ,
//            '.$this->tableName.'.code 
//          from
//            '.$this->tableNameDetail.',
//            '.$this->tableName.' 
//          where  
//            '. $this->tableNameDetail.'.refkey = '.$this->tableName.'.pkey and  
//            '. $this->tableNameDetail.'.apkey  = '.$this->oDbCon->paramString($apkey);
//
//        $sql .= $criteria;
// 
//        return $this->oDbCon->doQuery($sql);
//
//    } 
    
	function getJobOrderDetail($rsHeader){
         
        $pkey = $rsHeader[0]['pkey'];
         
        $arrJOKey = array();  
        
        if($rsHeader[0]['dntype'] == DN_TYPE['debitMemo']){ 
            //$rsJobKey = array();
            //$rsJobKey[0]['pkey'] = 0;
            //$rsJobKey[0]['reftabletype'] = 0;
            //$rsJobKey[0]['sokey'] = $rsHeader[0]['joborderkey'];
            //      
            //$arrJOKey = array_merge($arrJOKey,$rsJobKey);
        }else{
            $rsDetail = $this->getDetailById($pkey);
        
            // group dulu berdasarkan jenis table ny
            // pisah apakah dr EMKL Purchase Order, Commission, Trucking Purchase Order, dsb
            $rsDetail = $this->reindexDetailCollections($rsDetail,'refpurchasetabletype');  

             // harus diselect terpisah tergantung tabletypenyaa
             foreach($rsDetail as $tableTypeKey=>$rowTableType){

                 // kalo dari yg add AP sendiri, gk ad obj nya
                 if (empty($tableTypeKey)) continue;

                 // setiap table ad kemungkinan beda ref jo key nya
                 // cari jokey di setiap table, terus di merge 

                 $purchaseObj = $this->getObjMapping('',$tableTypeKey);

                 // harus return min ad reftabletype dan sokey
                 $rsJobKey = $purchaseObj->getDetailJobOrder(array_column($rowTableType,'refpurchasekey')) ?? [];

                 $arrJOKey = array_merge($arrJOKey,$rsJobKey);

             }   
        }
    
        $arrJOKey = $this->reindexDetailCollections($arrJOKey,'reftabletype');  
          
        return $arrJOKey;
    } 
    
    function afterStatusChanged($rsHeader){   
		  
        $arrJOKey = $this->getJobOrderDetail($rsHeader);
        
        foreach($arrJOKey as $tableTypeKey=>$row){ 
            if(empty($tableTypeKey)) continue; // utk jaga2 TMS yg blm ada reftabletypenya , kalo 0, gk dapat Purchaes Obj nya
            $transObj = $this->getObjMapping('',$tableTypeKey);
            $transObj->updateTotalDebitNote(array_column($row,'sokey'));
        }
         
        // update yg khusus debit Memo, Job Order dulu, header belakangan,di DN jg blm bisa pilih Header
        if(!empty($rsHeader[0]['joborderkey'])){
            $emklJobOrder = new EMKLJobOrder();
            $emklJobOrder->updateTotalDebitNote(array($rsHeader[0]['joborderkey'])); 
        } 
         
	}
 
    
    function getTransactionObject(){
        return new AP();
    }
    
    function getDebitNoteType(){
        $arr = array();
          
        array_push($arr, array('pkey' => DN_TYPE['ap'] , 'name' => $this->lang['debitAP']));
        array_push($arr, array('pkey' => DN_TYPE['cash'], 'name' => $this->lang['receiveCashBank']));
        
        // khusus TEL ddan CIF dulu
        if (in_array(DOMAIN_NAME, array('trioeaglelogistic.wintera.co.id','cif.wintera.co.id')))
            array_push($arr, array('pkey' => DN_TYPE['debitMemo'], 'name' => $this->lang['debitMemo']));
        
        return $arr;
    }
    
    function searchAPForDebitNote($apCriteria='',$commissionCriteria = '', $limit=''){
        
        $ap = new AP();
        $APCommission = new APCommission();
        $APKey = $this->getTableKeyAndObj($ap->tableName,array('key')); 
        $APCommissionKey = $APCommission->getTableKeyAndObj($APCommission->tableName,array('key')); 
         
		$sql = 'select
					'.$ap->tableName. '.pkey,     
                    concat('.$ap->tableName.'.code ,  IFNULL(concat(\'-\','.$ap->tableName. '.refcode), \'\') ) as value , 
                    '.$ap->tableName. '.code as code , 
                    '.$ap->tableName.'.refcode, 
                    '.$ap->tableName.'.duedate, 
                    '.$ap->tableName.'.refcode2,
                    '.$ap->tableName.'.refinvoicecode,
                    '.$ap->tableName.'.refkey,
                    '.$ap->tableName.'.refdate, 
                    '.$ap->tableName. '.amount,  
                    '.$ap->tableName. '.currencykey,  
                    '.$APKey['key'].' as reftabletype,
                    '.$ap->tableName. '.outstanding
				from 
					'.$ap->tableName . ', 
                    '.$this->tableStatus.'
				where  		
					'.$ap->tableName . '.statuskey = '.$this->tableStatus.'.pkey 
			';

        if($apCriteria <> '')
			$sql .= ' ' .$apCriteria;

    $sql .= 'union all 
            select
            '.$APCommission->tableName. '.pkey,     
            concat('.$APCommission->tableName.'.code ,  IFNULL(concat(\'-\','.$APCommission->tableName. '.refcode), \'\') ) as value , 
            '.$APCommission->tableName. '.code as code , 
            '.$APCommission->tableName.'.refcode, 
            '.$APCommission->tableName.'.trdate, 
            '.$APCommission->tableName.'.refcode2,
            '.$APCommission->tableName.'.refinvoicecode,
            '.$APCommission->tableName.'.refkey,
            '.$APCommission->tableName.'.trdate, 
            '.$APCommission->tableName. '.amount,  
            '.$APCommission->tableName. '.currencykey,  
            '.$APCommissionKey['key'].' as reftabletype,
            '.$APCommission->tableName. '.outstanding
        from 
            '.$APCommission->tableName . ', 
            '.$APCommission->tableStatus.'
        where  		
            '.$APCommission->tableName . '.statuskey = '.$APCommission->tableStatus.'.pkey 
    ';
				
		if($commissionCriteria <> '')
			$sql .= ' ' .$commissionCriteria;

			
		if($limit <> '')
			$sql .= ' ' .$limit;
         
//        $this->setLog($sql,true);
		return $this->oDbCon->doQuery($sql);	
	}
     
}

?>
