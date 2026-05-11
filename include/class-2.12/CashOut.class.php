<?php
  
class CashOut extends BaseClass{ 
 
   function __construct(){
		
		parent::__construct();
         
		$this->tableName = 'cash_out_header';
		$this->tableNameDetail = 'cash_out_detail'; 
		$this->tableStatus = 'transaction_status';
        $this->tableCost = 'cost_cash_out';
        $this->tableCOA = 'chart_of_account';
        $this->tableWarehouse = 'warehouse';
        $this->tableCurrency = 'currency';
        $this->tableSupplier = 'supplier';
        $this->isTransaction = true; 
		$this->securityObject = 'CashOut';  
        $this->tableFile = 'cash_out_file';
		$this->uploadFileFolder = 'cash-out/'; 
        $this->useMasterCost = $this->loadSetting('useMasterCost');
       
        $this->useStorage = $this->useStorage('S3');
       
        $this->newLoad = true;
	   
        $this->arrDataDetail = array(); 
        $this->arrDataDetail['pkey'] = array('hidDetailKey');
        $this->arrDataDetail['refkey'] = array('pkey','ref');
        $this->arrDataDetail['coakey'] = array('hidCOAKey');
        $this->arrDataDetail['costkey'] = array('hidCostKey');
        $this->arrDataDetail['amount'] = array('amount','number');
        $this->arrDataDetail['trdesc'] = array('trdesc');
        $this->arrDataDetail['pphtype'] = array('selPPhType'); 
        $this->arrDataDetail['pphvalue'] = array('PPhValue','number'); 
        $this->arrDataDetail['total'] = array('detailTotal','number'); 
        $this->arrDataDetail['taxpercentage'] = array('detailTaxPercentage','number'); 
        $this->arrDataDetail['taxvalue'] = array('detailTaxValue','number'); 
        $this->arrDataDetail['beforetax'] = array('detailBeforeTax','number'); 
        $this->arrDataDetail['ispriceincludetax'] = array('chkDetailIncludeTax');
       
        $arrDetails = array();
        array_push($arrDetails, array('dataset' => $this->arrDataDetail));
        
         if($this->useStorage){ 
            
            $this->arrDataFileDetail = array();  
            $this->arrDataFileDetail['pkey'] = array('hidDetailFileKey');
            $this->arrDataFileDetail['refkey'] = array('pkey','ref');
            $this->arrDataFileDetail['file'] = array('fileDetail',array('datatype' => 'file','uploadFolder' => $this->uploadFileFolder));
            
            array_push($arrDetails, array('dataset' => $this->arrDataFileDetail, 'tableName' => $this->tableFile));
        }
     
        $this->arrData = array();
        $this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails)); 
        $this->arrData['code'] = array('code');
        $this->arrData['refkey'] = array('refkey');
        $this->arrData['reftable'] = array('reftable');
        $this->arrData['trdate'] = array('trDate','date');
        $this->arrData['recipientname'] = array('recipientName');
        $this->arrData['coakey'] = array('hidCOAHeaderKey');
        $this->arrData['trdesc'] = array('trDesc');
        $this->arrData['grandtotal'] = array('total','number');
        $this->arrData['islinked'] = array('islinked'); 
        $this->arrData['statuskey'] = array('selStatus');
        $this->arrData['warehousekey'] = array('selWarehouseKey');
        $this->arrData['bankrefcode'] = array('bankRefCode');
        $this->arrData['currencykey'] = array('hidCurrencyKey');
        $this->arrData['rate'] = array('currencyRate', 'number');
        $this->arrData['totalpph'] = array('totalPPh', 'number');
        $this->arrData['totalcost'] = array('totalCost', 'number');
        $this->arrData['totalppn'] = array('totalPPN', 'number');
        $this->arrData['supplierkey'] = array('hidSupplierKey');
       
        if(!$this->useStorage)
	       $this->arrData['file'] = array('item-file-uploader',array('datatype' => 'file', 'uploadFolder' => $this->uploadFileFolder,  'token' => 'token-item-file-uploader', 'fileName' => 'item-file-uploader'));
      
       
        $this->importUrl = 'import/cashOut';
       
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'date','title' => 'date','dbfield' => 'trdate','default'=>true, 'width' => 100, 'align' =>'center', 'format' => 'date'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'warehouseName','title' => 'warehouse','dbfield' => 'warehousename',  'width' => 100));
		array_push($this->arrDataListAvailableColumn, array('code' => 'bankrefcode','title' => 'bankRef','dbfield' => 'bankrefcode','default'=>false, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'paidTo','title' => 'paidTo','dbfield' => 'recipientname', 'default'=>true, 'width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'currency','title' => 'curr','dbfield' => 'currencyname', 'default'=>true, 'width' => 60, 'align' =>'center'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'account','title' => 'account','dbfield' => 'codename', 'default'=>true, 'width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'total','title' => 'total','dbfield' => 'grandtotal', 'default'=>true, 'width' => 120, 'align' =>'right',  'format' => 'number'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
        array_push($this->arrDataListAvailableColumn, array('code' => 'desc','title' => 'note','dbfield' => 'trdesc',  'width' => 250));
        //array_push($this->arrDataListAvailableColumn, array('code' => 'reference','title' => 'reference','dbfield' => 'refcode',  'width' => 100)); 

		$this->arrSearchColumn = array ();
		array_push($this->arrSearchColumn, array('Kode', $this->tableName . '.code'));
		array_push($this->arrSearchColumn, array('Tanggal', $this->tableName . '.trdate'));
		array_push($this->arrSearchColumn, array('Penerima', $this->tableName . '.recipientname'));
		array_push($this->arrSearchColumn, array('COA', $this->tableCOA . '.code')); 
		array_push($this->arrSearchColumn, array('COA', $this->tableCOA . '.name')); 
		array_push($this->arrSearchColumn, array('Catatan', $this->tableName . '.trdesc') );
		array_push($this->arrSearchColumn, array('Gudang', $this->tableWarehouse . '.name') ); 
		array_push($this->arrSearchColumn, array('Ref. Bank', $this->tableName . '.bankrefcode') );
        array_push($this->arrSearchColumn, array('Pemasok', $this->tableSupplier . '.name') );  

       $this->activeModule = $this->isActiveModule(array('APPayableTax23', 'CashBank'));
        
       
        $this->printMenu = array();
        array_push($this->printMenu,array('code' => 'print', 'name' => $this->lang['printTransaction'],  'icon' => 'print', 'url' => 'print/cashOut'));
        array_push($this->printMenu,array('code' => 'printVoucher', 'name' => $this->lang['printVoucher'],  'icon' => 'print', 'url' => 'print/cashBankVoucherFromBankOut'));
       
        array_push($this->filterCriteria, array('title' => $this->lang['warehouse'], 'field' => 'warehousekey'));
         
        $this->includeClassDependencies(array(
            'ChartOfAccount.class.php',
            'CostCashOut.class.php',
            'CashBank.class.php',
            'COALink.class.php',
            'GeneralJournal.class.php',
            'Tax.class.php',
            'Supplier.class.php',
            'AP.class.php',
            'APPayableTax23.class.php'
        ));  
        
        
        $this->overwriteConfig();
   }
   
   function getQuery(){
	  
	   $sql = '
			SELECT '.$this->tableName.'.* , 
               ' . $this->tableCOA .'.name as coaname,
               ' . $this->tableCOA .'.code as coacode, 
               concat(' . $this->tableCOA .'.code, " - " , ' . $this->tableCOA .'.name ) as codename,
			   '.$this->tableStatus.'.status as statusname,
               '.$this->tableWarehouse.'.name as warehousename,
               '.$this->tableCurrency.'.name as currencyname,
               '.$this->tableSupplier.'.name as suppliername
			FROM '.$this->tableStatus.', '.$this->tableName.' 
                left join '. $this->tableCOA.' on ' . $this->tableCOA .'.pkey = ' . $this->tableName .'.coakey 
                left join '.$this->tableWarehouse.' on '.$this->tableName.'.warehousekey = '.$this->tableWarehouse.'.pkey
                left join '.$this->tableCurrency.' on '.$this->tableName.'.currencykey = '.$this->tableCurrency.'.pkey
                left join '.$this->tableSupplier.' on '.$this->tableName.'.supplierkey = '.$this->tableSupplier.'.pkey
			WHERE '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey 
 	  ' .$this->criteria ; 
	 	 
        $sql .=  $this->getCOACriteria() ;
        $sql .=  $this->getWarehouseCriteria() ;
        
       return $sql;
    }    
    
    function afterStatusChanged($rsHeader){   
        // retrieve latest status
        
        if (TABLENAME_SETTINGS[$this->tableName]['isautoclose'] == 1){
            $rsHeader = $this->getDataRowById($rsHeader[0]['pkey']);
            if ($rsHeader[0]['statuskey'] == 2)
                $this->changeStatus($rsHeader[0]['pkey'],3);     
        }
    }
    
        
     function validateForm($arr,$pkey = ''){ 
		  
		$arrayToJs = parent::validateForm($arr,$pkey); 
		  
		$chartOfAccount = new ChartOfAccount();   
        $costCashOut = new CostCashOut();
           
        $arrCCOkey = $arr['hidCostKey'];
		$arrCOAkey = $arr['hidCOAKey']; 
		$arrAmount = $arr['amount']; 
		$arrCOAHeaderKey = $arr['hidCOAHeaderKey'];
        $rate = $this->unFormatNumber($arr['currencyRate']);
        $supplierkey = $arr['hidSupplierKey'];
        $totalPPh = $this->unFormatNumber($arr['totalPPh']);
		
		
		//validasi kalo status gk menunggu gk bisa edit 
		if (!empty($pkey)){
			$rs = $this->getDataRowById($pkey);
			if ($rs[0]['statuskey'] <> 1){
				$this->addErrorList($arrayToJs,false,$this->errorMsg[212]);
			}
		} 
 
        if(empty($arrCOAHeaderKey)) 
            $this->addErrorList($arrayToJs,false,$this->errorMsg['coa'][1]); 

         

        //wajib di isi kalau pph > 0
        if($totalPPh > 0 && empty($supplierkey)) {
            $this->addErrorList($arrayToJs,false,$this->errorMsg['supplier'][1]);
        }
        // untuk batas pembulatan
        $thresholdDiscount = abs($this->loadSetting('roundedPaymentThreshold'));
    
		if($this->useMasterCost){ 

            for($i=0;$i<count($arrCCOkey);$i++) { 

                if (empty($arrCCOkey[$i]) )  
                    $this->addErrorList($arrayToJs,false, $this->errorMsg['cost'][1]); 	
                
                // untuk pembulatan
                $detailAmount = $this->unFormatNumber($arrAmount[$i]) * $rate;
                
                // hanya jika negatif utk pembulatan
                if (!empty($arrCCOkey[$i]) && $detailAmount < 0 && abs($detailAmount) > $thresholdDiscount){
                    $rsCCO = $costCashOut->getDataRowById($arrCCOkey[$i]); 
                    $this->addErrorList($arrayToJs,false,$rsCCO[0]['name'] .'. '. $this->errorMsg[513]); 
                }
            }

        }else{
         
            for($i=0;$i<count($arrCOAkey);$i++) { 

                if (empty($arrCOAkey[$i]) )  
                    $this->addErrorList($arrayToJs,false, $this->errorMsg['coa'][1]); 	
             
                // untuk pembulatan 
                $detailAmount = $this->unFormatNumber($arrAmount[$i]) * $rate;
                
                if (!empty($arrCOAkey[$i]) &&  $detailAmount < 0 && abs($detailAmount) > $thresholdDiscount){
                    $rsCOA = $chartOfAccount->getDataRowById($arrCOAkey[$i]); 
                    $this->addErrorList($arrayToJs,false,$rsCOA[0]['code'] . ' - ' .$rsCOA[0]['name']. '. ' . $this->errorMsg[513]); 
                }
            }
        }
         
		
		return $arrayToJs;
	 }

	function validateConfirm($rsHeader){
        
	    $costCashOut = new CostCashOut();
        
        parent::validateConfirm($rsHeader);
           
        $id = $rsHeader[0]['pkey']; 
        $rsDetail = $this->getDetailById($id);
           
        if(empty($rsDetail))
            $this->addErrorLog(false, $this->errorMsg['coa'][1]); 	
            
            
        if($this->useMasterCost){ 

            for($i=0;$i<count($rsDetail);$i++) {  
                if (empty($rsDetail[$i]['costkey']) )  
                    $this->addErrorLog(false, $this->errorMsg['cost'][1]); 
                
                $rsCostCashOut = $costCashOut->getDataRowById($rsDetail[$i]['costkey']); 
                
                if (empty($rsCostCashOut[0]['coakey']) )  
                    $this->addErrorLog(false, $rsCostCashOut[0]['name'] . $this->errorMsg['coa'][1]); 
            }

        }else{
         
            for($i=0;$i<count($rsDetail);$i++) {  
                if (empty($rsDetail[$i]['coakey']) )  
                    $this->addErrorLog(false, $this->errorMsg['coa'][1]); 	
              
            }
        }
        
        
    }		

	function confirmTrans($rsHeader){
        $id = $rsHeader[0]['pkey'];
		//$cashMovement = new CashMovement();  
	    $costCashOut = new CostCashOut();
		
		$note = $rsHeader[0]['code'] .'. Kas Keluar. '; 
		$rsDetail = $this->getDetailById($rsHeader[0]['pkey']); 
		 
        
		for($i=0;$i<count($rsDetail); $i++){		 
		    //$cashMovement->updateCashMovement($id,$rsHeader[0]['coakey'],-$rsDetail[$i]['amount'],$this->tableName, 0, $note.$rsDetail[$i]['trdesc'],$rsHeader[0]['trdate']);

            if($this->useMasterCost){
                $rsCostCashOut = $costCashOut->getDataRowById($rsDetail[$i]['costkey']); 
                $coakey = $rsCostCashOut[0]['coakey'];
                $sql = 'update '.$this->tableNameDetail.'  
                        set coakey = '.$this->oDbCon->paramString($coakey).'
                        where pkey = ' .$rsDetail[$i]['pkey'];
                $this->oDbCon->execute($sql);
            }
            
		}	 
        
		$arrCashBank = array();
		if($this->activeModule['cashbank']){ 
			$cashBank = new CashBank(); 
			$rsCashBank = $cashBank->addCashBank($rsHeader,$this->tableName, array('desc' => $rsHeader[0]['trdesc'], 'amount' => -$rsHeader[0]['grandtotal'])); 
			$arrCashBank['cashFromKey'] = $rsCashBank['pkey']; 
		}
	
		if($this->activeModule['appayabletax23']){ 
            $this->updateAPPrepaid($rsHeader,$rsDetail);
        }	
		//update jurnal umum 
        $this->updateGL($rsHeader,$arrCashBank);
        
	} 


    function updateAPPrepaid($rsHeader,$rsDetail){
            $apPayableTax23 = new APPayableTax23();  
            
            $rate = (isset($rsHeader[0]['rate']) && $rsHeader[0]['rate'] > 0) ? $rsHeader[0]['rate'] : 1;
            
            $tax = new Tax();
        
            $rsTax = $tax->searchDataRow(array( $tax->tableName.'.pkey', $tax->tableName. '.name', $tax->tableName. '.haswithholding' ), 
                                        ' and ' . $tax->tableName.'.typekey = '. $this->oDbCon->paramString(TAX_TYPE['PPH']) .' and '. $tax->tableName .'.statuskey = 1');
            $rsTax = $this->reindexDetailCollections($rsTax, 'pkey');
        
            if($rsHeader[0]['totalpph'] == 0 || empty($rsHeader[0]['supplierkey'])) return;
        
            for ($i=0;$i<count($rsDetail);$i++){ 
                $pphTypeKey = 0; // reset ulang
                
                
                // hanya jika detail pph ada isinya (backcompability)
                if(!empty($rsDetail[$i]['pphtype'])){ 
                    $pphTypeKey = $rsDetail[$i]['pphtype']; 
                    $hasWithholding = $rsTax[$pphTypeKey][0]['haswithholding']; 
                    if($hasWithholding != 1) continue;
                }
                
                if ($rsDetail[$i]['pphvalue'] == 0) continue;
                    
                $arrParam = array();
                
                
                $rsKey =  $this->getTableKeyAndObj($this->tableName);                  
                $arrParam['code'] = 'xxxxxx';
                $arrParam['hidSupplierKey'] = $rsHeader[0]['supplierkey']; 
                $arrParam['hidRefKey'] = $rsDetail[$i]['pkey']; 
                $arrParam['hidRefHeaderKey'] = $rsHeader[0]['pkey'];
                $arrParam['hidRefCode'] =  $rsHeader[0]['code'];
                $arrParam['hidRefDate'] =  $this->formatDBDate($rsHeader[0]['trdate'],'d / m / Y');  
                $arrParam['hidRefTable'] = $rsKey['key'];
                $arrParam['amount'] = $rsDetail[$i]['pphvalue'] * $rate;
                $arrParam['trDesc'] = '';
                $arrParam['trDate'] =  $this->formatDBDate($rsHeader[0]['trdate'],'d / m / Y');  
                $arrParam['dueDate'] =  $this->formatDBDate($rsHeader[0]['trdate'],'d / m / Y');  
                $arrParam['createdBy'] = 0; 
                $arrParam['islinked'] = 1;
                $arrParam['selAPType'] = 1;
                $arrParam['overwriteGL'] = 1;
                $arrParam['selWarehouse'] = $rsHeader[0]['warehousekey'];
                $arrParam['selPPhType'] = $pphTypeKey;
                
                $returnVal = $apPayableTax23->addData($arrParam,false);  

                if (!$returnVal[0]['valid'])
                    throw new Exception('<strong>'.$rsHeader[0]['code'] . '</strong>. '.$this->errorMsg[201].' '.$returnVal[0]['message']);    
                
 
            }  
    }

    function deleteAPPrepaidTax($id){ 
          
        $apPayableTax23 = new APPayableTax23(); 
		
        $rsHeader = $this->getDataRowById($id);

        $rsKey = $this->getTableKeyAndObj($this->tableName, array('key')); 
        $rsAP = $apPayableTax23->searchData('', '', true, ' and refheaderkey = ' . $this->oDbCon->paramString($id) . ' and ' . $apPayableTax23->tableName . '.reftabletype = ' . $rsKey['key'] . ' and ' . $apPayableTax23->tableName . '.statuskey = 1');
    
        for($i=0;$i<count($rsAP);$i++) { 
            $arrayToJs = $apPayableTax23->changeStatus($rsAP[$i]['pkey'],4,'',false, true);
            if (!$arrayToJs[0]['valid'])
                throw new Exception('<strong>'.$rsHeader[0]['code'] . '</strong>. '.  $arrayToJs[0]['message']);    
        }  
          
      }
	
	
	  
	function updateGL($rs,$arrCashBank){
        if (!USE_GL) return;
        
        $warehouse = new Warehouse();
        $generalJournal = new GeneralJournal();
        $chartOfAccount = new ChartOfAccount();
        $coaLink = new COALink(); 
        $tax = new Tax();
        
        
        $rsCOA = $chartOfAccount->getDataRowById($rs[0]['coakey']);
        $currencykey = $rsCOA[0]['currencykey'];
        $rate = (!empty($rs[0]['rate'])) ? $rs[0]['rate'] : 1;
		
        $desc = array();
        if (!empty($rs[0]['recipientname']))
            array_push($desc, $rs[0]['recipientname']);
        
        if (!empty($rs[0]['trdesc']))
            array_push($desc, $rs[0]['trdesc']);
        
        $desc = html_entity_decode(implode(chr(13),$desc));
        $rsKey = $generalJournal->getTableKeyAndObj($this->tableName);
        
        $timestampArr = $this->getDateUsedForTimestamp($this->tableName, $rs);
        
		$arr = array();
		$arr['pkey'] = $generalJournal->getNextKey($generalJournal->tableName);
		$arr['code'] = 'xxxxx';
		$arr['refkey'] = $rs[0]['pkey'];
		$arr['refTableType'] = $rsKey['key'];
		$arr['trDate'] = $this->formatDBDate($timestampArr['timestamp'],'d / m / Y'); 
		$arr['trDesc'] = $desc;
		$arr['selWarehouseKey'] = $rs[0]['warehousekey'];
		$arr['createdBy'] = 0; 
		
        $temp = -1;
        
        $totalAmount = 0 ;
        $taxValue = 0;
        $arrPPHTypeKey = array();
        
        $rsDetail = $this->getDetailById($rs[0]['pkey']); 
        for($i=0;$i<count($rsDetail);$i++){
            array_push($arrPPHTypeKey, $rsDetail[$i]['pphtype']);
            $totalAmount += $rsDetail[$i]['amount']; 
            $taxValue += $rsDetail[$i]['taxvalue']; 
        } 
  
		for($i=0;$i<count($rsDetail);$i++){
            $temp++;
            $arr['hidCOAKey'][$temp] = $rsDetail[$i]['coakey'];
            //$arr['debit'][$temp] =  $rsDetail[$i]['amount']*$rate; 
            $arr['debit'][$temp] = ($rsDetail[$i]['total'] - $rsDetail[$i]['taxvalue']) * $rate ; 
            $arr['credit'][$temp] = 0;
            $arr['selCurrencyKey'][$temp] = $currencykey ; 
            // $arr['debitSource'][$temp] = $rsDetail[$i]['amount']; 
            $arr['debitSource'][$temp] = $rsDetail[$i]['total'] - $rsDetail[$i]['taxvalue'];
            $arr['creditSource'][$temp] = 0; 
            $arr['rate'][$temp] = $rate ;   
            $arr['trdescDetail'][$temp] = html_entity_decode($rsDetail[$i]['trdesc']);
			
			
			if(!empty($arrCashBank))
            	$arr['refCashBankKey'][$temp]  = '';
        }
		
        $temp++;
        $arr['hidCOAKey'][$temp] = $rs[0]['coakey'];
        $arr['debit'][$temp] = 0;  
        $arr['credit'][$temp] = $totalAmount*$rate; 
        $arr['selCurrencyKey'][$temp] = $currencykey ; 
        $arr['debitSource'][$temp] = 0; 
        $arr['creditSource'][$temp] = $totalAmount; 
        $arr['rate'][$temp] = $rate ;   
        $arr['trdescDetail'][$temp] = html_entity_decode($rs[0]['trdesc']);	
		
		
		if(!empty($arrCashBank))
        	$arr['refCashBankKey'][$temp] =  $arrCashBank['cashFromKey']; 

         
        //PPN
        // gk bisa pake jenis ppn karena gk simpen key nya
        if($taxValue != 0) {
 
            $taxCOAKey = $coaLink->getCOALink('taxin', $warehouse->tableName, $rs[0]['warehousekey'], 0)[0]['coakey'];
        
            $totalVatIn = 0;
            for($i=0; $i<count($rsDetail);$i++) 
                $totalVatIn += $rsDetail[$i]['taxvalue'] ;   
            

            $temp++;
            $arr['hidCOAKey'][$temp] = $taxCOAKey;
            $arr['debit'][$temp] = $totalVatIn * $rate;
            $arr['credit'][$temp] = 0;
            $arr['debitSource'][$temp] = $totalVatIn ; 
            $arr['creditSource'][$temp] =  0; 
            $arr['selCurrencyKey'][$temp] = $currencykey ; 
            $arr['rate'][$temp] = $rate ; 
            $arr['trdescDetail'][$temp] = ''; 


        }
        
        //PPH
        $rsTaxCol = $tax->searchDataRow(array('pkey','taxincoakey', 'taxoutcoakey'), ' and '.$tax->tableName.'.typekey = '. $this->oDbCon->paramString(TAX_TYPE['PPH']).' and '.$tax->tableName.'.pkey in (' . $this->oDbCon->paramString($arrPPHTypeKey,',').')');
        $rsTaxCol = array_column($rsTaxCol,null,'pkey');

        if($rs[0]['totalpph'] != 0){

            for($i=0;$i<count($rsDetail);$i++){

                $rsTax = $rsTaxCol[$rsDetail[$i]['pphtype']];

                $taxCOAKey = $rsTax['taxincoakey'];

                $temp++;
                $arr['hidCOAKey'][$temp] = $taxCOAKey; 
                $arr['selBusinessUnitKey'][$temp] = 0; // $rsDetail[$i]['businessunitkey']; 
                $arr['debit'][$temp] = 0; 
                $arr['credit'][$temp] = $rsDetail[$i]['pphvalue'] * $rate;  
                $arr['debitSource'][$temp] = 0; 
                $arr['creditSource'][$temp] =  $rsDetail[$i]['pphvalue'] ; 
                $arr['selCurrencyKey'][$temp] =$currencykey; 
                $arr['rate'][$temp] = $rate ; 
                $arr['trdescDetail'][$temp] = '';

            }

        }
         
		$arrayToJs = $generalJournal->addData($arr);
        
		if (!$arrayToJs[0]['valid'])
                throw new Exception('<strong>'.$rs[0]['code'] . '</strong>. '.$this->errorMsg[504].' '.$arrayToJs[0]['message']);    
    }

    function validateCancel($rsHeader, $autoChangeStatus = false){ 
    
        $id = $rsHeader[0]['pkey'];

        
        if($this->activeModule['appayabletax23']){ 
            //cek ad Prepaid yg ad bukti potongnya blm 
            $apPayableTax = new APPayableTax23();
            $rsKey = $this->getTableKeyAndObj($this->tableName,array('key'));                  
            $rsAP = $apPayableTax->searchData('','',true,' and refheaderkey = '.$this->oDbCon->paramString($id).' and '.$apPayableTax->tableName.'.reftabletype = '.$rsKey['key'].' and ('.$apPayableTax->tableName.'.statuskey in (2,3) )');
        
            if(!empty($rsAP)) {
                $arrAP = array_column($rsAP,'code');
                $this->addErrorLog( false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' . $this->errorMsg[201].' Bukti bayar sudah diinput.<br>' . implode(', ', $arrAP ).'.');
            }
        }
    }
	 
	function cancelTrans($rsHeader,$copy){  
        
		$id = $rsHeader[0]['pkey'];
		  	 
		/*$cashMovement = new CashMovement();  
		$cashMovement->cancelMovement($id,$this->tableName);*/
		  
		if($this->activeModule['cashbank']){ 
			$cashBank = new CashBank();
			$cashBank->cancelCashBank($rsHeader,$this->tableName);
		}
        if($this->activeModule['appayabletax23']){ 
            $this->deleteAPPrepaidTax($id);
        }
		
		if ($copy)
			$this->copyDataOnCancel($id);	
         
        $this->cancelGLByRefkey($rsHeader[0]['pkey'],$this->tableName);
	} 
    
 	function reCountGrandtotal($arrParam){

				$grandtotal = 0;
				$amount = 0;
                
                $totalPPh = 0;
                $totalCost = 0;
                $totalCashOutAmount = 0;
				
				$arrCOAKey = $arrParam['hidCOAKey'];
				$arrAmount = $arrParam['amount']; 
				
                $arrAmount = $arrParam['amount']; 
                $arrPPh = $arrParam['PPhValue'];  
                $arrTaxDetailValue = $arrParam['detailTaxValue'];  
                $arrDetailTaxPercentage = $arrParam['detailTaxPercentage'];  
                $arrDetailIncludeTax = $arrParam['chkDetailIncludeTax'];

				$arrARDetail = array();
				//$aR = new AR();
                
                
                $arrItemDetail = array();
				
                $totalPPN = 0;
        
				for ($i=0;$i<count($arrCOAKey);$i++){
					
				    $arrAmount[$i] = $this->unFormatNumber($arrAmount[$i]);
					
                    $arrPPh[$i] = $this->unFormatNumber($arrPPh[$i]);
                    $arrDetailTaxPercentage[$i] = $this->unFormatNumber($arrDetailTaxPercentage[$i]);

                    if (empty($arrCOAKey[$i]) || empty($arrAmount[$i]) )  // gk perlu cek costkey, karena coakey selalu ada
						continue;

					//$amount += $this->unFormatNumber($arrAmount[$i]);

                      
                    $DPP =  $arrAmount[$i] + $arrPPh[$i];
                    
                    // duit keluar sudah pasti termasuk pajak,
                    $DPP = $DPP / (1 + $arrDetailTaxPercentage[$i]/100);
                    
                    
//                    if($arrDetailIncludeTax[$i] == 1)
//                        $DPP = $DPP / (1 + $arrDetailTaxPercentage[$i]/100);
                    
//                    $DPP = ($arrAmount[$i] + $arrPPh[$i]) / (1 + ($arrDetailTaxPercentage[$i] / 100));
                    
                    $taxValue = $DPP * ($arrDetailTaxPercentage[$i] / 100); 
                        
                    $afterTax = $DPP + $taxValue;
                    $cashOutAmount = $afterTax - $arrPPh[$i];
                    $totalAmount = $afterTax;

                    $arrItemDetail[$i]['detailBeforeTax'] = $DPP; 
                    $arrItemDetail[$i]['detailTaxValue'] = $taxValue; 
                    $arrItemDetail[$i]['detailTotal'] = $totalAmount; 


                    $totalPPh += $arrPPh[$i];
                    $totalCost += $totalAmount ; 
                    $totalCashOutAmount += $cashOutAmount ; 
                    $totalPPN += $taxValue;
				
                } 
				
				//$grandtotal = $amount; 

				$reCountResult = array();
				//$reCountResult['grandtotal'] = $grandtotal;
                $reCountResult['grandtotal'] = $totalCashOutAmount; 
                $reCountResult['totalPPh'] = $totalPPh; 
                $reCountResult['totalPPN'] = $totalPPN; 
                $reCountResult['totalCost'] = $totalCost; 
                $reCountResult['itemDetail'] = $arrItemDetail; 
				
				return $reCountResult;
				
	}

    function getDetailWithRelatedInformation($pkey,$criteria=''){
        
            $sql = 'select
	   			'.$this->tableNameDetail .'.*,
                '.$this->tableCost.'.name as costname, 
                '.$this->tableCOA.'.name as coaname,
                '.$this->tableCOA.'.code as coacode,
                concat(' . $this->tableCOA .'.code, " - " , ' . $this->tableCOA .'.name ) as coacodename 
			  from
			  	'. $this->tableNameDetail .' 
                    left join  '.$this->tableCost.' on '. $this->tableNameDetail .'.costkey = '.$this->tableCost.'.pkey
                    left join  '.$this->tableCOA.' on '. $this->tableNameDetail .'.coakey = '.$this->tableCOA.'.pkey 
			  where  
			  	'.$this->tableNameDetail .'.refkey = '.$this->oDbCon->paramString($pkey);
         

        $sql .= $criteria;
		return $this->oDbCon->doQuery($sql);
    }
     
    
    function normalizeParameter($arrParam, $trim = false){ 
        
            $costCashOut = new CostCashOut(); 
         
            if($this->useMasterCost){ 
                $arrCostkey = $arrParam['hidCostKey']; 
                for($i=0;$i<count($arrParam['hidDetailKey']);$i++){ 
                    $rsCost = $costCashOut->getDataRowById($arrCostkey[$i]); 
                    $arrParam['hidCOAKey'][$i] = $rsCost[0]['coakey'];
                }
            }
            
            $reCountResult = $this->reCountGrandtotal($arrParam);  
            $arrParam['total'] = $reCountResult['grandtotal'];
  
            $arrParam['totalPPh'] = $reCountResult['totalPPh'];
            $arrParam['totalPPN'] = $reCountResult['totalPPN'];
            $arrParam['totalCost'] = $reCountResult['totalCost'];
            
            foreach($reCountResult['itemDetail'] as $key=>$row){
                $arrParam['detailTotal'][$key] = $row['detailTotal'];
                $arrParam['detailBeforeTax'][$key] = $row['detailBeforeTax']; 
                $arrParam['detailTaxValue'][$key] = $row['detailTaxValue']; 
            }
  
                

                
            //kalau idr currency rate nya 1
            if($arrParam['hidCurrencyKey'] == CURRENCY['idr'])
                $arrParam['currencyRate'] = 1;


            $arrParam = parent::normalizeParameter($arrParam,true); 
            
            return $arrParam;
    }
    
      
    function getRelatedDataForCashBankReport($pkey){
        $arrReturn = array();
        
        $sql = 'select 
                    '. $this->tableName.'.pkey, 
                    '. $this->tableName.'.code as refcode , 
                    '. $this->tableName.'.recipientname as recipient 
                from 
                    '. $this->tableName.' 
                where 
                    pkey in ('.$this->oDbCon->paramString($pkey,',').') ';
        
        $rs = $this->oDbCon->doQuery($sql); 
        $rs = array_column($rs, null,'pkey');
          
        return $rs;
    }
    
	function getTransactionDescription($arrKey,$userkey= ''){
                   
        // yg boleh diakses
//        $arrAvailableField = array(   
//                                    array('code' => 'recipient', 'param' => 'RECIPIENT_NAME', 'field' => $this->tableName.'.recipientname'),  
//                                    array('code' => 'trdesc', 'param' => 'DESCRIPTION', 'field' => $this->tableName.'.trdesc'),  
//                                    array('code' => 'detaildesc', 'param' => 'TRANSACTION_DESCRIPTION', 'tableDetail' => array('tableName' => $this->tableNameDetail, 
//																															   'refField' =>  'refkey' , 
//																															   'field' => $this->tableNameDetail.'.trdesc' 
//																															  )), 
//        );
        
        $arrAvailableField = array(   
                                    array('tableName' => $this->tableName, 'param' => 'RECIPIENT_NAME', 'field' => $this->tableName.'.recipientname'),  
                                    array('tableName' => $this->tableName, 'param' => 'DESCRIPTION', 'field' => $this->tableName.'.trdesc'),   
                                    array('tableName' =>  $this->tableNameDetail, 'param' => 'TRANSACTION_DESCRIPTION', 'field' => $this->tableNameDetail.'.trdesc' ),  
        );
		   
        return $this->stitchDescriptionV2(array('field' => $arrAvailableField, 'pkey' => $arrKey, 'userkey' => $userkey ));
	 }
	 
    
}
?>
