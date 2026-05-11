<?php

class CashBankOut extends BaseClass{
	
    function __construct(){

    parent::__construct();

    $this->tableName = 'cash_bank_out_header';
    $this->tableNameDetail = 'cash_bank_out_detail';
    $this->tableWarehouse = 'warehouse';   
    $this->tableSupplier = 'supplier'; 
    $this->tableCOA = 'chart_of_account';  
    $this->tableCostCashOut = 'cost_cash_out';
    $this->tableCurrency = 'currency';
    $this->tableEmployee = 'employee'; 
    $this->tableCustomer = 'customer'; 
    $this->tableStatus = 'transaction_status';
    $this->tableFile = 'cash_bank_out_file';
    $this->tableRecipientType = 'recipient_type';
    $this->uploadFileFolder = 'cash-bank-out/';
    $this->isTransaction = true;    
    $this->newLoad = true;    
        
    $this->securityObject = 'CashBankOut';

    $this->arrDataDetail = array(); 
    $this->arrDataDetail['pkey'] = array('hidDetailKey');
    $this->arrDataDetail['refkey'] = array('pkey','ref'); 
    $this->arrDataDetail['supplierkey'] = array('hidSupplierKey'); 
    $this->arrDataDetail['costkey'] = array('hidCostKey');
    $this->arrDataDetail['trdesc'] = array('trdescDetail'); 
    $this->arrDataDetail['amount'] = array('amount','number'); 
    $this->arrDataDetail['pphtype'] = array('selPPhType'); 
    $this->arrDataDetail['pphvalue'] = array('PPhValue','number'); 
    $this->arrDataDetail['total'] = array('detailTotal','number'); 
    $this->arrDataDetail['taxpercentage'] = array('detailTaxPercentage','number'); 
    $this->arrDataDetail['taxvalue'] = array('detailTaxValue','number'); 
    $this->arrDataDetail['beforetax'] = array('detailBeforeTax','number'); 
    $this->arrDataDetail['ispriceincludetax'] = array('chkDetailIncludeTax'); 
    
    
    $this->arrDataFileDetail = array();  
    $this->arrDataFileDetail['pkey'] = array('hidDetailFileKey');
    $this->arrDataFileDetail['refkey'] = array('pkey','ref');
    $this->arrDataFileDetail['file'] = array('fileDetail',array('datatype' => 'file','uploadFolder' => $this->uploadFileFolder));

    $this->arrDetails = array();
    array_push($this->arrDetails, array('dataset' => $this->arrDataDetail, 'tableName' => $this->tableNameDetail)); 
    array_push($this->arrDetails, array('dataset' => $this->arrDataFileDetail, 'tableName' => $this->tableFile));

    $this->arrData = array(); 
    $this->arrData['pkey'] = array('pkey', array('dataDetail' => $this->arrDetails));  
    $this->arrData['code'] = array('code');
    $this->arrData['warehousekey'] = array('selWarehouseKey');
    $this->arrData['isunknownresource'] = array('chkUnknownSource');

    $this->arrData['employeekey'] = array('hidEmployeeKey');
    $this->arrData['recipienttypekey'] = array('selRecipientType');
    $this->arrData['coakey'] = array('hidCOAHeaderKey');
    $this->arrData['currencykey'] = array('hidCurrencyKey');
    $this->arrData['rate'] = array('currencyRate', 'number');
    $this->arrData['supplierkey'] = array('hidSupplierKey');
    $this->arrData['customerkey'] = array('hidCustomerKey');
    $this->arrData['trdate'] = array('trDate','date');
    $this->arrData['trdesc'] = array('note');
    $this->arrData['statuskey'] = array('selStatus');
    $this->arrData['grandtotal'] = array('total','number');
    $this->arrData['totalpph'] = array('totalPPh','number');
    $this->arrData['totalcost'] = array('totalCost','number');
	$this->arrData['attnname'] = array('attnName');
//    $this->arrData['file'] = array('fileDetail',array('datatype' => 'file','uploadFolder' => $this->uploadFileFolder));
 
    $this->importUrl = 'import/cashBankOut';
	$this->autoPrintURL = 'print/cashBankVoucherFromCashBankOut';

    $this->arrDataListAvailableColumn = array(); 
	array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 120));
	array_push($this->arrDataListAvailableColumn, array('code' => 'date','title' => 'date','dbfield' => 'trdate','default'=>true, 'width' => 100, 'align' =>'center','format' => 'date'));
	array_push($this->arrDataListAvailableColumn, array('code' => 'warehouse','title' => 'warehouse','dbfield' => 'warehousename','default'=>true, 'width' => 100));    
	array_push($this->arrDataListAvailableColumn, array('code' => 'recipientType','title' => 'recipientType','dbfield' => 'recipienttypename','default'=>true, 'width' => 100));    
	array_push($this->arrDataListAvailableColumn, array('code' => 'sender','title' => 'recipient','dbfield' => 'sendername','default'=>true, 'width' => 200));    
	array_push($this->arrDataListAvailableColumn, array('code' => 'attention','title' => 'attention','dbfield' => 'attnname', 'width' => 150));    
    array_push($this->arrDataListAvailableColumn, array('code' => 'account','title' => 'account','dbfield' => 'codename', 'default'=>true, 'width' => 200));
	array_push($this->arrDataListAvailableColumn, array('code' => 'currency','title' => 'currency','dbfield' => 'currencyname', 'width' => 100));    
	array_push($this->arrDataListAvailableColumn, array('code' => 'currencyrate','title' => 'currencyRate','dbfield' => 'rate', 'width' => 100, 'format' => 'number'));  
	array_push($this->arrDataListAvailableColumn, array('code' => 'grandtotal','title' => 'amount','dbfield' => 'grandtotal','default'=>true,'align' =>'right',  'format' => 'number' ,'width' => 100));   
	array_push($this->arrDataListAvailableColumn, array('code' => 'desc','title' => 'note','dbfield' => 'trdesc','default'=>true, 'width' => 250));     
	array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
         
        

    $this->arrSearchColumn = array ();
    array_push($this->arrSearchColumn, array($this->lang['code'], $this->tableName . '.code'));  
    array_push($this->arrSearchColumn, array($this->lang['warehouse'], $this->tableWarehouse . '.name')); 
    array_push($this->arrSearchColumn, array($this->lang['supplier'], $this->tableSupplier . '.name')); 
    array_push($this->arrSearchColumn, array($this->lang['employee'], $this->tableEmployee . '.name')); 
    array_push($this->arrSearchColumn, array($this->lang['attention'], $this->tableName . '.attnname')); 
    array_push($this->arrSearchColumn, array($this->lang['description'], $this->tableName . '.trdesc'));

    array_push($this->filterCriteria, array('title' => $this->lang['warehouse'], 'field' => 'warehousekey'));
    
	$this->printMenu = array();
    array_push($this->printMenu,array('code' => 'print', 'name' => $this->lang['printTransaction'],  'icon' => 'print', 'url' => 'print/cashBankOut'));
    array_push($this->printMenu,array('code' => 'printVoucher', 'name' => $this->lang['printVoucher'],  'icon' => 'print', 'url' => 'print/cashBankVoucherFromCashBankOut'));
    
	$this->includeClassDependencies(array(
           'Supplier.class.php',  
           'CostCashOut.class.php',  
           'Currency.class.php',  
           'ChartOfAccount.class.php',  
           'Warehouse.class.php',  
           'Tax.class.php',  
           'CashBank.class.php',  
           'GeneralJournal.class.php',
           'COALink.class.php'
    ));  

    $this->overwriteConfig();

    }

    function getQuery(){
         
        $sql = '
            SELECT
                '.$this->tableName.'.*,  
                '.$this->tableWarehouse.'.name as warehousename,
                '.$this->tableCurrency.'.name as currencyname,
                '.$this->tableEmployee.'.name as employeename,
                '.$this->tableSupplier.'.name suppliername,
                '.$this->tableCustomer.'.name customername,
                '.$this->tableRecipientType.'.name as recipienttypename,
                if('.$this->tableName.'.recipienttypekey = '.RECIPIENT_TYPE['customer'].',  '.$this->tableCustomer.'.name, IF('.$this->tableName.'.recipienttypekey = '.RECIPIENT_TYPE['supplier'].','.$this->tableSupplier.'.name,'.$this->tableEmployee.'.name))  as sendername,
                 concat(' . $this->tableCOA .'.code, " - " , ' . $this->tableCOA .'.name ) as codename,
                '.$this->tableStatus.'.status as statusname
                
            FROM '.$this->tableStatus.',
                 '.$this->tableWarehouse.',
                 '.$this->tableName.'
                    left join '. $this->tableCurrency.' on ' . $this->tableCurrency .'.pkey = ' . $this->tableName .'.currencykey 
                    left join '. $this->tableCOA.' on ' . $this->tableCOA .'.pkey = ' . $this->tableName .'.coakey 
                    left join '. $this->tableEmployee.' on ' . $this->tableEmployee .'.pkey = ' . $this->tableName .'.employeekey 
                    left join '. $this->tableSupplier.' on ' . $this->tableSupplier .'.pkey = ' . $this->tableName .'.supplierkey 
                    left join '. $this->tableCustomer.' on ' . $this->tableCustomer .'.pkey = ' . $this->tableName .'.customerkey 
                    left join '. $this->tableRecipientType.' on ' . $this->tableRecipientType .'.pkey = ' . $this->tableName .'.recipienttypekey 

            WHERE   
                  '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey and
                  '.$this->tableName.'.warehousekey = '.$this->tableWarehouse.'.pkey 

            ' .$this->criteria ;
        
        $sql .=  $this->getWarehouseCriteria() ;
//        $sql .=  $this->getBusninessUnitCriteria() ;
                                         
        return $sql;
    }
    
    
    function afterStatusChanged($rsHeader){   
        $rsHeader = $this->getDataRowById($rsHeader[0]['pkey']);
        $rsDetail = $this->getDetailById($rsHeader[0]['pkey']);
        $rsDetail = array_column($rsDetail,'costkey');
        
         /*
        $isOnlyCost = () ? true : false;
        
        foreach($rsDetail as $row){
            if(empty($row))
                $isOnlyCost = false;
        }
        
        if ($rsHeader[0]['statuskey'] == 2 && $isOnlyCost)*/
        
        //$this->changeStatus($rsHeader[0]['pkey'],3);
        
        
    }

    function afterUpdateData($arrParam, $action){
          
        /*
if(isset($arrParam['hidId']) && !empty($arrParam['hidId'])){
            $rs = $this->getDataRowById($arrParam['pkey']);
            
            if ($rs[0]['statuskey'] == TRANSACTION_STATUS['konfirmasi']){   
                $cashBank = new CashBank();   
                
                // better nilai detail tarik ulang sj
                $rsDetails = $this->getDetailById($arrParam['pkey']);
                
                $totalDetails = count($rsDetails);
                
                $tablekey = $this->getTableKeyAndObj($this->tableName ,array('key'))['key'];  
                
                $arrDetails = array();
                for($i=0;$i<$totalDetails;$i++){
                    
                    if (!empty($rsDetails[$i]['costkey'])) continue;
                    
                    array_push($arrDetails,
                                array(
                                 'headerkey' => $rsDetails[$i]['refkey'],
                                 'detailkey' => $rsDetails[$i]['pkey'],
                                 'tablekey' => $tablekey,
                                 'supplierkey' => $rs[0]['supplierkey'],
                                 'description' => $rsDetails[$i]['trdesc'],
                                )
                    );
                }
                 
                $cashBank->reUpdateCashBankDetails($arrDetails);   // kirim supplierkey dan desc saja   
            }
             
        } 
*/

    }
    
    function validateForm($arr,$pkey = ''){  
        
		$costCashOut = new CostCashOut();
		$coa = new ChartOfAccount();

        $arrayToJs = parent::validateForm($arr,$pkey);  
    	$arrCOAHeaderKey = $arr['hidCOAHeaderKey'];
        $arrCostKey = $arr['hidCostKey'];
//        $supplierKey = $arr['hidSupplierKey'];
        $arrAmount = $arr['amount'];
        $arrTaxPercentage = $arr['detailTaxPercentage'];
        $arrIncludeTax = $arr['chkDetailIncludeTax'];
        $arrPPHType = $arr['selPPhType']; 
        $recipientTypeKey = $arr['selRecipientType'];
        $currencyRate = $arr['currencyRate'];

        switch($recipientTypeKey){
            case RECIPIENT_TYPE['customer']: 
                $hidInputName = 'hidCustomerKey';
                $errName = 'customer';
                
            break;
            
            case RECIPIENT_TYPE['supplier']: 
                $hidInputName = 'hidSupplierKey';
               $errName = 'supplier';

            break;
            case RECIPIENT_TYPE['employee']: 
                $hidInputName = 'hidEmployeeKey';
                     $errName = 'employee';
            break;
                
        }        
        
        $supplierKey = $arr[$hidInputName];
        
        // validasi kalo ksoong costcash nya, pelanggan wajib isi
        // jgn loop dr arrCostCash karena selectbox gk disabled 
        $arrTax = array();
        $arrIsInclude = array();
        $arrPPH = array();
        
        $arrTemp = array();     

        $rsCostCol = $costCashOut->searchDataRow(array($costCashOut->tableName.'.pkey'), ' and '.$costCashOut->tableName.'.pkey in ('.$this->oDbCon->paramString($arrCostKey,',').')');
        $rsCostCol = array_column($rsCostCol,null,'pkey');
        
        for($i=0;$i<count($arrAmount);$i++){ 
            
            if (empty($arrCostKey[$i]) && empty($supplierKey)) 
                $this->addErrorList($arrayToJs,false,$this->errorMsg[$errName][1]);   
             
            if ( $arrAmount[$i] <=0 ){ 
                $this->addErrorList($arrayToJs,false,$this->errorMsg['amount'][1]);
			}else{ 
                                 
                $rsCost = $rsCostCol[$arrCostKey[$i]];
                if(!empty($rsCost)){
                    $costKey = CASH_BANK_TYPE['isnottemporary'];
                }else{
                    $costKey = CASH_BANK_TYPE['temporary'];
           
                }
                array_push($arrTemp,$costKey); 
            }
        }

        
        $arrTemp = array_unique($arrTemp);
        $totalTemp = count($arrTemp);

        if($totalTemp > 1)
			$this->addErrorList($arrayToJs,false,$this->errorMsg['cashBank'][6]);

		
        if($currencyKey <> CURRENCY['idr'] && $this->unformatNumber($currencyRate) <= 0 )
           $this->addErrorList($arrayToJs,false, $this->errorMsg['rate'][5]);

		
		if(empty($arrCOAHeaderKey)){ 
			$this->addErrorList($arrayToJs,false,$this->errorMsg['coa'][1]); 
		}else{
            
            $rsCOA = $coa->getDataRowById($arrCOAHeaderKey);
            if(empty($rsCOA[0]['countercoakey'])) 
			     $this->addErrorList($arrayToJs,false,'<strong>'.$rsCOA[0]['code'].' - '.$rsCOA[0]['name'].'.</strong>  '.$this->errorMsg['coa'][1]); 
            
        }
	 
        return $arrayToJs;
    }
    
    function validateConfirm($rsHeader){
        
    }		
    
    function validateCancel($rsHeader, $autoChangeStatus = false){ 
         
        $id = $rsHeader[0]['pkey'];
        
        $cashBank = new CashBank();
        
        //cek sudah ad yg jenisnya bukan cost tp outstandingnya sudah beda 
        $tablekey = $this->getTableKeyAndObj($this->tableName ,array('key'))['key'];  
		$rsCashBank = $cashBank->searchDataRow( array($cashBank->tableName.'.pkey',$cashBank->tableName.'.code'),
                                                 ' and refkey = '.$this->oDbCon->paramString($id).' 
                                                   and reftabletype = '.$tablekey.'
                                                   and '.$cashBank->tableName.'.amount <> '.$cashBank->tableName.'.outstanding
                                                   and '.$cashBank->tableName.'.costkey = 0 
                                                   and '.$cashBank->tableName.'.statuskey <> ' . TRANSACTION_STATUS['batal']
                                               );
     
		if(!empty($rsCashBank)) {
            $rsCashBank = array_column($rsCashBank,'code');
			$this->addErrorLog( false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' . $this->errorMsg[201].'<br><b>' . implode(', ', $rsCashBank ).'</b>. '. $this->errorMsg['cashBank'][2]);
        }
        
    } 
	 

	function confirmTrans($rsHeader){
		$id = $rsHeader[0]['pkey'];

        $cashBank = new CashBank(); 
        $rsDetail = $this->getDetailById($id); 

        // agar kegenerate kode voucher keluar 
        $arrDesc = array();
        $amount= 0;
        $pphValue= 0;
        $taxValue= 0;
        $totalRs = count($rsDetail);
		for($i=0;$i<$totalRs; $i++){		 
            
            // biar bulet, gk koma kalo ppn nya include, hitugn dr total - pph saja
//            $outstanding = (empty($rsDetail[$i]['costkey'])) ?  ($rsDetail[$i]['total'] - $rsDetail[$i]['pphvalue']) : 0; // kalo sudah jelas transaksinya, gk ad outstanding
                    
                        $amount += ($rsDetail[$i]['total'] - $rsDetail[$i]['pphvalue']);
                        $pphValue += $rsDetail[$i]['pphvalue'];
                        $taxValue += $rsDetail[$i]['taxvalue'];

            array_push($arrDesc,$rsDetail[$i]['trdesc']);

		}	
        
           $outstanding = (empty($rsDetail[0]['costkey'])) ?  $amount : 0; // kalo sudah jelas transaksinya, gk ad outstanding

            $desc = implode(' , ', $arrDesc);

            $rsCashBank = $cashBank->addCashBank($rsHeader,$this->tableName, 
                                                    array(
                                                          'supplierkey' => $rsHeader[0]['supplierkey'],
                                                          'employeekey' => $rsHeader[0]['employeekey'],
                                                          'customerkey' => $rsHeader[0]['customerkey'],
                                                          'attnName' => $rsHeader[0]['attnname'],
                                                          'coakey' => $rsHeader[0]['coakey'],
//                                                          'businessunitkey' => $rsHeader[0]['businessunitkey'],
                                                          'desc' => $desc, 
                                                          'costkey' => $rsDetail[0]['costkey'],
                                                          'PPnValue' => $taxValue, 
                                                          'PPnPercentage' => $rsDetail[0]['taxpercentage'], 
                                                          'isPriceIncludeTax' => $rsDetail[0]['ispriceincludetax'], 
                                                          'PPhTypeKey' => $rsDetail[0]['pphtype'], 
                                                          'amount' => $amount, 
                                                          'outstanding' =>$outstanding, 
                                                          'PPhValue' => $pphValue, 
                                                          'overwriteGL' => 1
                                                         ),true); 
        
		if(!empty($rsCashBank['pkey'])){ 
            $arrCashBank['cashBankKey'] = $rsCashBank['pkey'];  
            $this->updateGL($rsHeader,$rsCashBank);
		}

	} 

    
    function reCountGrandtotal($arrParam){

        $grandtotal = 0;
        $amount = 0;
        $totalPPh = 0;
        $totalCost = 0;
        $totalCashOutAmount = 0;
        
        $arrItemDetail = array();

        $arrAmount = $arrParam['amount']; 
        $arrPPh = $arrParam['PPhValue'];  
        $arrTaxDetailValue = $arrParam['detailTaxValue'];  
        $arrDetailTaxPercentage = $arrParam['detailTaxPercentage'];  
        $arrDetailIncludeTax = $arrParam['chkDetailIncludeTax'];
        
        for ($i=0;$i<count($arrAmount);$i++){

            $arrAmount[$i] = $this->unFormatNumber($arrAmount[$i]);
            $arrPPh[$i] = $this->unFormatNumber($arrPPh[$i]);
            $arrDetailTaxPercentage[$i] = $this->unFormatNumber($arrDetailTaxPercentage[$i]);
            
            $DPP = ($arrAmount[$i] + $arrPPh[$i]) / (1 + ($arrDetailTaxPercentage[$i] / 100));
            $taxValue = $DPP * ($arrDetailTaxPercentage[$i] / 100);
            
            $afterTax = $DPP + $taxValue;
            $cashOutAmount = $afterTax - $arrPPh[$i];
            $totalAmount = $afterTax; 
            
            // gk boleh continue, kalo gk nanti index detail totalny gk sama
//            if (empty($arrAmount[$i]) )  continue;
             
//            $detailBeforeTax = $arrAmount[$i] + $arrPPh[$i];
//            $detailTotal = $detailBeforeTax; 
//            $taxDetailValue = $arrTaxDetailValue[$i];
//            
//            // perhitungan ppn harus tambhin nilai pph dulu, karena amonut yg dicatat hanya nilai yg dikeluarkan setelah dipotong pph
//            $this->recalculateTaxAndValue($detailBeforeTax,$detailTotal,$taxDetailValue, $arrDetailTaxPercentage[$i], $arrDetailIncludeTax[$i]); 


//            $amount += $detailTotal;
//            $totalPPh  += $arrPPh[$i];
            
//            $detailTotal += $arrPPh[$i];
//            $totalCost  += $detailTotal;
              
            $arrItemDetail[$i]['detailBeforeTax'] = $DPP; 
            $arrItemDetail[$i]['detailTaxValue'] = $taxValue; 
            $arrItemDetail[$i]['detailTotal'] = $totalAmount; 


            $totalPPh += $arrPPh[$i];
            $totalCost += $totalAmount ; 
            $totalCashOutAmount += $cashOutAmount ; 

        } 

        
        $reCountResult = array();
        $reCountResult['grandtotal'] = $totalCashOutAmount; 
        $reCountResult['totalPPh'] = $totalPPh; 
        $reCountResult['totalCost'] = $totalCost; 
        $reCountResult['itemDetail'] = $arrItemDetail; 

        return $reCountResult;
				
	}
    
    function updateGL($rs,$rsCashBank){
        
        if (!USE_GL) return;

        
         
        $warehouse = new Warehouse();
        $generalJournal = new GeneralJournal(); 
        $chartOfAccount = new ChartOfAccount();
        $costCashOut = new CostCashOut();
        $coaLink = new COALink(); 
        $tax = new Tax();
        
        $rsKey = $generalJournal->getTableKeyAndObj($this->tableName);
                
        $headerCurrencyKey = $rs[0]['currencykey'];
        $rate = $rs[0]['rate'];
        
		$arr = array();
		$arr['pkey'] = $generalJournal->getNextKey($generalJournal->tableName);
		$arr['code'] = 'xxxxx';
		$arr['refkey'] = $rs[0]['pkey'];
		$arr['refTableType'] = $rsKey['key'];
		$arr['trDate'] =  $this->formatDBDate($rs[0]['trdate'],'d / m / Y'); 
		$arr['trDesc'] = $rs[0]['trdesc'];
		$arr['createdBy'] = 0;  
		$arr['selWarehouseKey'] = $rs[0]['warehousekey'];
		
        $temp = -1;
        $rsDetail = $this->getDetailById($rs[0]['pkey']); 
        
        
        //karena cashbank out
        $creditType = -1;
//        $rs[0]['ppnvalue'] *= $creditType ;
//        $rs[0]['pphvalue'] *= $creditType ;
        $amount =  $rs[0]['grandtotal']  ;

        $coaKey =  $rs[0]['coakey'];

    /*    if(!empty($rs[0]['revenuekey'])) 
            $coaCounterKey = $revenueCashIn->getDataRowById($rs[0]['revenuekey'])[0]['coakey'] ;
        else if (!empty($rs[0]['costkey'])) 
            $coaCounterKey = $costCashOut->getDataRowById($rs[0]['costkey'])[0]['coakey'] ;
        else
            */
        $coaCounterKey = $chartOfAccount->getDataRowById($rs[0]['coakey'])[0]['countercoakey'];
            
		$temp++;
		$arr['hidCOAKey'][$temp] = $coaKey; 
		$arr['selBusinessUnitKey'][$temp] = 0 ; // karena bisnis unit tidak ada di header
		$arr['debit'][$temp] = 0;  // kalo ayat silang harusnya aman karena tax nya pasti direset jd 0;
		$arr['credit'][$temp] = $amount  * $rate ;  
		$arr['debitSource'][$temp] = 0; 
		$arr['creditSource'][$temp] =  $amount ; 
		$arr['selCurrencyKey'][$temp] = $headerCurrencyKey ; 
		$arr['rate'][$temp] = $rate ; 
        $arr['trdescDetail'][$temp] = $rsCashBank['code']; 

/*        $temp++;
        $arr['hidCOAKey'][$temp] = $coaCounterKey; 
        $arr['debit'][$temp] = 0; 
        $arr['credit'][$temp] = $amount - $rs[0]['ppnvalue'] + $rs[0]['pphvalue'];  */

         $arrCostKey = array();
         $arrPPHTypeKey = array();
         $taxValue = 0;
        foreach($rsDetail as $row){
            array_push($arrCostKey,$row['costkey']);
            array_push($arrPPHTypeKey,$row['pphtype']);
            $taxValue += $row['taxvalue'];
        }

        //biar ga query ulang
        $rsCostCashOutCol = $costCashOut->searchDataRow(array($costCashOut->tableName.'.pkey',$costCashOut->tableName.'.coakey'), ' and '.$costCashOut->tableName.'.pkey in ('.$this->oDbCon->paramString($arrCostKey,',').')');
        $rsCostCashOutCol = array_column($rsCostCashOutCol,null,'pkey');
        
        for($i=0;$i<count($rsDetail);$i++){
            
            $rsCostCashOut = $rsCostCashOutCol[$rsDetail[$i]['costkey']];
            
            $revenueCoaKey = (empty($rsDetail[$i]['costkey'])) ? $coaCounterKey : $rsCostCashOut['coakey'] ;
            
            
            // kalo negatif, otomatis nanti aka ndibalik di GL nya
       
            
            $temp++;
            $arr['hidCOAKey'][$temp] = $revenueCoaKey; 
            $arr['selBusinessUnitKey'][$temp] = 0; //$rsDetail[$i]['businessunitkey']; 
            $arr['debit'][$temp] = ($rsDetail[$i]['total'] - $rsDetail[$i]['taxvalue']) * $rate ; 
            $arr['credit'][$temp] = 0;
            $arr['debitSource'][$temp] = $rsDetail[$i]['total'] - $rsDetail[$i]['taxvalue']; 
            $arr['creditSource'][$temp] =  0; 
            $arr['selCurrencyKey'][$temp] = $headerCurrencyKey ; 
            $arr['rate'][$temp] = $rate ; 
            $arr['trdescDetail'][$temp] = $rsDetail[$i]['trdesc']; 

                
        }
        
        
        if($taxValue != 0){
            
            // format dulu agar bisa sama denga string
//            $taxFormatted = (float)$rs[0]['ppnpercentage'];
       
            //next akan di update menggunakan tax master
//            $rsTax = $tax->searchDataRow(array('taxincoakey', 'taxoutcoakey'), ' and '.$tax->tableName.'.typekey = '. $this->oDbCon->paramString(TAX_TYPE['PPN']).' and '.$tax->tableName.'.name = ' . $this->oDbCon->paramString($taxFormatted));
                   
            
            //untuk sementara ambil dari gudang
            $coaTaxOut = $coaLink->getCOALink ('taxout', $warehouse->tableName,$rs[0]['warehousekey'], 0)[0]['coakey']; 
            $coaTaxIn = $coaLink->getCOALink ('taxin', $warehouse->tableName,$rs[0]['warehousekey'], 0)[0]['coakey']; 

            $taxCOAKey = $coaTaxIn ;
             for($i=0;$i<count($rsDetail);$i++){

                $temp++;
                $arr['hidCOAKey'][$temp] = $taxCOAKey; 
                $arr['selBusinessUnitKey'][$temp] = 0; // $rsDetail[$i]['businessunitkey']; 
                $arr['debit'][$temp] = $rsDetail[$i]['taxvalue'] * $rate; 
                $arr['credit'][$temp] = 0; 
                $arr['debitSource'][$temp] = $rsDetail[$i]['taxvalue'] ; 
                $arr['creditSource'][$temp] =  0; 
                $arr['selCurrencyKey'][$temp] = $headerCurrencyKey ; 
                $arr['rate'][$temp] = $rate ; 
                $arr['trdescDetail'][$temp] = ''; 
             }
                    
        }
                    
        
        $rsTaxCol = $tax->searchDataRow(array('pkey','taxincoakey', 'taxoutcoakey'), ' and '.$tax->tableName.'.typekey = '. $this->oDbCon->paramString(TAX_TYPE['PPH']).' and '.$tax->tableName.'.pkey in (' . $this->oDbCon->paramString($arrPPHTypeKey,',').')');
        $rsTaxCol = array_column($rsTaxCol,null,'pkey');
 
        if($rs[0]['totalpph'] != 0){
              
            
            
//            $this->setLog(  ' and '.$tax->tableName.'.pkey = ' . $this->oDbCon->paramString($rs[0]['pphtypekey']), true);
            
            
             for($i=0;$i<count($rsDetail);$i++){
                 
                 $rsTax = $rsTaxCol[$rsDetail[$i]['pphtype']];
                 
                $taxCOAKey = $rsTax['taxincoakey'] ;

                $temp++;
                $arr['hidCOAKey'][$temp] = $taxCOAKey; 
                $arr['selBusinessUnitKey'][$temp] = 0; // $rsDetail[$i]['businessunitkey']; 
                $arr['debit'][$temp] = 0; 
                $arr['credit'][$temp] = $rsDetail[$i]['pphvalue'] * $rate;  
                $arr['debitSource'][$temp] = 0; 
                $arr['creditSource'][$temp] =  $rsDetail[$i]['pphvalue'] ; 
                $arr['selCurrencyKey'][$temp] = $headerCurrencyKey ; 
                $arr['rate'][$temp] = $rate ; 
                $arr['trdescDetail'][$temp] = ''; 
             }
                    
        }
        
		$arrayToJs = $generalJournal->addData($arr);
            
		if (!$arrayToJs[0]['valid'])
                throw new Exception('<strong>'.$rs[0]['code'] . '</strong>. '.$this->errorMsg[504].' '.$arrayToJs[0]['message']);    
    }
    
    function normalizeParameter($arrParam, $trim=false){
        
        $chartOfAccount = new ChartOfAccount();
        
        $rsCOA = $chartOfAccount->getDataRowById($arrParam['hidCOAHeaderKey']);
        //currency harus sesuai dengan setting coa
        $currencyKey = (empty($rsCOA[0]['currencykey'])) ? CURRENCY['idr']  : $rsCOA[0]['currencykey'];
        $arrParam['hidCurrencyKey'] = $currencyKey;
        
        
        //kalau external employee 0
        if($arrParam['selRecipientType'] == RECIPIENT_TYPE['customer']){
             $arrParam['hidEmployeeKey'] = 0;           
             $arrParam['hidSupplierKey'] = 0;           
        }else if($arrParam['selRecipientType'] == RECIPIENT_TYPE['supplier']){
             $arrParam['hidCustomerKey'] = 0;
             $arrParam['hidEmployeeKey'] = 0;
        }else{
             $arrParam['hidCustomerKey'] = 0;
             $arrParam['hidSupplierKey'] = 0;           

        }
        //kalau idr currency rate nya 1
        if($arrParam['hidCurrencyKey'] == CURRENCY['idr'])
            $arrParam['currencyRate'] = 1;
        
        foreach($arrParam['hidCostKey'] as $key=>$row){
            if($row == 0){ 
                $arrParam['PPhValue'][$key] = 0;
                $arrParam['detailTaxPercentage'][$key] = 0;
                $arrParam['chkDetailIncludeTax'][$key] = 0;
            }
        }
        
        $reCountResult = $this->reCountGrandtotal($arrParam);  
        $arrParam['total'] = $reCountResult['grandtotal'];
        $arrParam['totalPPh'] = $reCountResult['totalPPh'];
        $arrParam['totalCost'] = $reCountResult['totalCost'];
        
        foreach($reCountResult['itemDetail'] as $key=>$row){
            $arrParam['detailTotal'][$key] = $row['detailTotal'];
            $arrParam['detailBeforeTax'][$key] = $row['detailBeforeTax']; 
            $arrParam['detailTaxValue'][$key] = $row['detailTaxValue']; 
        }
        
        
        $arrParam = parent::normalizeParameter($arrParam,true); 
        
         
        return $arrParam;
        
        
    }
    
    function getRecipientType($pkey=''){ 
       
	   $sql = 'select
	   			'.$this->tableRecipientType .'.pkey, 
	   			'.$this->tableRecipientType .'.name
              from
			  	'.$this->tableRecipientType .' 
			  where
			  	'.$this->tableRecipientType .'.statuskey = 1';
       if(!empty($pkey))
            $sql .= ' and pkey in ('.$this->oDbCon->paramString($pkey,',').')';
        
//        $sql .=' order by orderlist asc';
         
       return $this->oDbCon->doQuery($sql);
	
   }
    
    function cancelTrans($rsHeader,$copy,$GLCancelDate='00 / 00 / 0000'){ 
		$id = $rsHeader[0]['pkey']; 


        $cashBank = new CashBank();
        $cashBank->cancelCashBank($rsHeader,$this->tableName);
        
        $this->cancelGLByRefkey($rsHeader[0]['pkey'],$this->tableName,$GLCancelDate);
        
		if ($copy)
			$this->copyDataOnCancel($id);	 

	} 

     function getDetailWithRelatedInformation($pkey,$criteria=''){
		 
    $sql = 'select
                '.$this->tableNameDetail.'.*,
                '.$this->tableSupplier.'.name as suppliername,
                '.$this->tableCostCashOut.'.name as costname
              from
                '.$this->tableNameDetail.'
                    left join '.$this->tableSupplier.' on '. $this->tableNameDetail.'.supplierkey = '.$this->tableSupplier.'.pkey 
                    left join '.$this->tableCostCashOut.' on '. $this->tableNameDetail.'.costkey = '.$this->tableCostCashOut.'.pkey 
              where   
                '. $this->tableNameDetail.'.refkey in  ('.$this->oDbCon->paramString($pkey,',') . ') ' ;

             $sql .= $criteria; 
             return $this->oDbCon->doQuery($sql);

    }
	
	 function getTransactionDescription($arrKey,$userkey= ''){
                   
        // yg boleh diakses
         
        // SAAT INI BLM BISA MENGKOMBINASIKAN INFORMASI DETAIL DARI USER, HARUS MENGIKUTI TEMPLATE DARI KITA MISALNYA
        // TRANSACTION_DESCRIPTION : DESKRIPSI SAJA, ATAU 
        // TRANSACTION_DESCRIPTION_AND_TYPE : TRANSAKSI + DESKRIPSI
//        $arrAvailableField = array(   
//								    array('code' => 'suppliername', 'param' => 'SUPPLIER_NAME', 'tableReference' => array('tableName' => $this->tableSupplier, 
//																													 'refField' => 'pkey', 
//																													 'field' => $this->tableSupplier.'.name',
//																													 'refkey' => 'supplierkey'
//																													)), 
//                                    array('code' => 'recipient', 'param' => 'RECIPIENT_NAME', 'field' => $this->tableName.'.attnname'), 
//                                    array('code' => 'detaildesc', 'param' => 'TRANSACTION_DESCRIPTION', 'tableDetail' => array('tableName' => $this->tableNameDetail, 
//																															   'field' => $this->tableNameDetail.'.trdesc',
//                                                                                                                               'tableDetail' => array( array('param'=>'TRANSACTION_TYPE',
//                                                                                                                                                             'tableName' => $this->tableCostCashOut,
//                                                                                                                                                             'refkey' => 'costkey' 
//                                                                                                                                                            ) 
//                                                                                                                                                     ) 
//                                                                                                                              ) ), 
//        );

         
          $arrAvailableField = array(   
								    array('tableName' => $this->tableName, 'param' => 'SUPPLIER_NAME', 'tableReference' => array('tableName' => $this->tableSupplier,  
																													 'field' => $this->tableSupplier.'.name',
																													 'refkey' => 'supplierkey'
																													)), 
                                    array('tableName' =>  $this->tableName, 'param' => 'RECIPIENT_NAME', 'field' => $this->tableName.'.attnname'), 
              
                                    array('tableName' =>  $this->tableNameDetail, 'param' => 'TRANSACTION_DESCRIPTION', 'field' => $this->tableNameDetail.'.trdesc' ),  
								    array('tableName' => $this->tableNameDetail, 'param' => 'TRANSACTION_TYPE', 'tableReference' => array('tableName' => $this->tableCostCashOut,  
																													 'field' => $this->tableCostCashOut.'.name',
																													 'refkey' => 'costkey'
																													)), 
        );
         
        return $this->stitchDescriptionV2(array('field' => $arrAvailableField, 'pkey' => $arrKey, 'userkey' => $userkey ));
	 }

}

?>
