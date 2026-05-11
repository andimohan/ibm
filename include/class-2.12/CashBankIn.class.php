<?php

class CashBankIn extends BaseClass{
	
    function __construct(){

    parent::__construct();

    $this->tableName = 'cash_bank_in_header';
    $this->tableNameDetail = 'cash_bank_in_detail';
    $this->tableWarehouse = 'warehouse';   
    $this->tableCustomer = 'customer'; 
    $this->tableEmployee = 'employee'; 
    $this->tableSupplier = 'supplier'; 
    $this->tableCOA = 'chart_of_account';  
    $this->tableRevenue = 'revenue_cash_in';
    $this->tableStatus = 'transaction_status';
    $this->tableCurrency = 'currency';
    $this->tableRecipientType = 'recipient_type';
    $this->isTransaction = true;    
        
    $this->securityObject = 'CashBankIn';
    $this->newLoad = true;
   // $this->allowedStatusForEdit = array(1,2);

    $this->arrDataDetail = array(); 
    $this->arrDataDetail['pkey'] = array('hidDetailKey');
    $this->arrDataDetail['refkey'] = array('pkey','ref'); 
    $this->arrDataDetail['customerkey'] = array('hidCustomerKey'); 
    $this->arrDataDetail['revenuekey'] = array('hidRevenueKey');
    $this->arrDataDetail['trdesc'] = array('trdescDetail'); 
    $this->arrDataDetail['amount'] = array('amount','number'); 

    $arrDetails = array();
    array_push($arrDetails, array('dataset' => $this->arrDataDetail));
        
    $this->arrData = array(); 
    $this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails));  
    $this->arrData['code'] = array('code');
    $this->arrData['customerkey'] = array('hidCustomerKey');
    $this->arrData['employeekey'] = array('hidEmployeeKey');
    $this->arrData['supplierkey'] = array('hidSupplierKey');
    $this->arrData['recipienttypekey'] = array('selRecipientType');
    $this->arrData['warehousekey'] = array('selWarehouseKey');
    $this->arrData['currencykey'] = array('hidCurrencyKey');
    $this->arrData['rate'] = array('currencyRate', 'number');
//    $this->arrData['businessunitkey'] = array('selBusinessUnitKey');
    $this->arrData['coakey'] = array('hidCOAHeaderKey');
    $this->arrData['trdate'] = array('trDate','date');
    $this->arrData['trdesc'] = array('note');
    $this->arrData['statuskey'] = array('selStatus');
    $this->arrData['grandtotal'] = array('total','number');
    $this->arrData['attnname'] = array('attnName');
 	$this->arrData['isunknownsource'] = array('chkUnknownSource');
 
		
			
	$this->importUrl = 'import/cashBankIn';
	$this->autoPrintURL = 'print/cashBankVoucherFromCashBankIn';

    $this->arrDataListAvailableColumn = array(); 
    array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 120));
    array_push($this->arrDataListAvailableColumn, array('code' => 'date','title' => 'date','dbfield' => 'trdate','default'=>true, 'width' => 100, 'align' =>'center','format' => 'date'));
    array_push($this->arrDataListAvailableColumn, array('code' => 'warehouse','title' => 'warehouse','dbfield' => 'warehousename','default'=>true, 'width' => 100));    
    array_push($this->arrDataListAvailableColumn, array('code' => 'currency','title' => 'currency','dbfield' => 'currencyname', 'width' => 100));    
    array_push($this->arrDataListAvailableColumn, array('code' => 'currency','title' => 'currency','dbfield' => 'currencyname', 'width' => 100));    
    array_push($this->arrDataListAvailableColumn, array('code' => 'currencyrate','title' => 'currencyRate','dbfield' => 'rate', 'width' => 100, 'format' => 'number'));    
    array_push($this->arrDataListAvailableColumn, array('code' => 'recipientType','title' => 'recipientType','dbfield' => 'recipienttypename','default'=>true, 'width' => 100));    
    array_push($this->arrDataListAvailableColumn, array('code' => 'customer','title' => 'sender','dbfield' => 'sendername','default'=>true, 'width' => 200));    
    array_push($this->arrDataListAvailableColumn, array('code' => 'attention','title' => 'attention','dbfield' => 'attnname', 'width' => 150));    
    array_push($this->arrDataListAvailableColumn, array('code' => 'account','title' => 'account','dbfield' => 'codename', 'default'=>true, 'width' => 200));
    array_push($this->arrDataListAvailableColumn, array('code' => 'grandtotal','title' => 'amount','dbfield' => 'grandtotal','default'=>true,'align' =>'right',  'format' => 'number' ,'width' => 100));   
    array_push($this->arrDataListAvailableColumn, array('code' => 'desc','title' => 'note','dbfield' => 'trdesc','default'=>true, 'width' => 250));     
    array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
        
        
    $this->arrSearchColumn = array ();
    array_push($this->arrSearchColumn, array($this->lang['code'], $this->tableName . '.code')); 
    array_push($this->arrSearchColumn, array($this->lang['customer'], $this->tableCustomer . '.name')); 
    array_push($this->arrSearchColumn, array($this->lang['employee'], $this->tableEmployee . '.name')); 
    array_push($this->arrSearchColumn, array($this->lang['warehouse'], $this->tableWarehouse . '.name')); 
    array_push($this->arrSearchColumn, array($this->lang['attention'], $this->tableName . '.attnname')); 
    array_push($this->arrSearchColumn, array($this->lang['note'], $this->tableName . '.trdesc')); 
        
       
    array_push($this->filterCriteria, array('title' => $this->lang['warehouse'], 'field' => 'warehousekey'));
            
	$this->printMenu = array();
    array_push($this->printMenu,array('code' => 'print', 'name' => $this->lang['printTransaction'],  'icon' => 'print', 'url' => 'print/cashBankIn'));
	array_push($this->printMenu,array('code' => 'printVoucher', 'name' => $this->lang['printVoucher'],  'icon' => 'print', 'url' => 'print/cashBankVoucherFromCashBankIn'));
    
    $this->includeClassDependencies(array(
           'Customer.class.php',  
           'RevenueCashIn.class.php',  
           'ChartOfAccount.class.php', 
           'Employee.class.php',   
           'Warehouse.class.php',  
           'CashBank.class.php',  
           'GeneralJournal.class.php',
           'GeneralJournal.class.php',
           'COALink.class.php',
           'Currency.class.php'
    ));  

    $this->overwriteConfig();

    }

    function getQuery(){
        
   $sql = '
            SELECT
                '.$this->tableName.'.* ,  
                '.$this->tableWarehouse.'.name as warehousename,
                '.$this->tableCurrency.'.name as currencyname,
                '.$this->tableEmployee.'.name as employeename,
                '.$this->tableCustomer.'.name as customername,
                if('.$this->tableName.'.recipienttypekey = '.RECIPIENT_TYPE['customer'].',  '.$this->tableCustomer.'.name, IF('.$this->tableName.'.recipienttypekey = '.RECIPIENT_TYPE['supplier'].','.$this->tableSupplier.'.name,'.$this->tableEmployee.'.name))  as sendername,
                '.$this->tableRecipientType.'.name as recipienttypename,
                 concat(' . $this->tableCOA .'.code, " - " , ' . $this->tableCOA .'.name ) as codename,
                '.$this->tableStatus.'.status as statusname
                
            FROM '.$this->tableStatus.',
                 '.$this->tableWarehouse.',
                 '.$this->tableName.'   
                    left join '. $this->tableCurrency .' on '. $this->tableName .'.currencykey = '. $this->tableCurrency .'.pkey
                    left join '. $this->tableCOA.' on ' . $this->tableCOA .'.pkey = ' . $this->tableName .'.coakey 
                    left join '. $this->tableEmployee.' on ' . $this->tableEmployee .'.pkey = ' . $this->tableName .'.employeekey 
                    left join '. $this->tableCustomer.' on ' . $this->tableCustomer .'.pkey = ' . $this->tableName .'.customerkey 
                    left join '. $this->tableSupplier.' on ' . $this->tableSupplier .'.pkey = ' . $this->tableName .'.supplierkey 
                    left join '. $this->tableRecipientType.' on ' . $this->tableRecipientType .'.pkey = ' . $this->tableName .'.recipienttypekey 
            WHERE   
                  '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey and
                  '.$this->tableName.'.warehousekey = '.$this->tableWarehouse.'.pkey 

            ' .$this->criteria ;
        
        $sql .=  $this->getWarehouseCriteria() ;
                                                 return $sql;
    }
    
    function afterStatusChanged($rsHeader){   
        $rsHeader = $this->getDataRowById($rsHeader[0]['pkey']);
        $rsDetail = $this->getDetailById($rsHeader[0]['pkey']);
        $rsDetail = array_column($rsDetail,'revenuekey');
        
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
          
//        if(isset($arrParam['hidId']) && !empty($arrParam['hidId'])){
//            $rs = $this->getDataRowById($arrParam['pkey']);
//            
//            if ($rs[0]['statuskey'] == TRANSACTION_STATUS['konfirmasi']){   
//                $cashBank = new CashBank();   
//                
//                // better nilai detail tarik ulang sj
//                $rsDetails = $this->getDetailById($arrParam['pkey']);
//                
//                $totalDetails = count($rsDetails);
//                
//                $tablekey = $this->getTableKeyAndObj($this->tableName ,array('key'))['key'];  
//                
//                $arrDetails = array();
//                for($i=0;$i<$totalDetails;$i++){
//                    
//                    if (!empty($rsDetails[$i]['revenuekey'])) continue;
//                    
//                    array_push($arrDetails,
//                                array(
//                                 'headerkey' => $rsDetails[$i]['refkey'],
//                                 'detailkey' => $rsDetails[$i]['pkey'],
//                                 'tablekey' => $tablekey,
//                                 'customerkey' => $rs[0]['customerkey'],
//                                 'description' => $rsDetails[$i]['trdesc'],
//                                )
//                    );
//                }
//                 
//                $cashBank->reUpdateCashBankDetails($arrDetails);   // kirim customerkey dan desc saja   
//            }
//             
//        } 

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
    function  validateForm($arr,$pkey = ''){  
        
        $revenueCashIn = new RevenueCashIn();
        $coa = new ChartOfAccount();
        
        $arrayToJs = parent::validateForm($arr,$pkey);

        
    	$isUnknownSource = $arr['chkUnknownSource'];
    	$arrCOAHeaderKey = $arr['hidCOAHeaderKey'];
        $arrRevenueKey = $arr['hidRevenueKey'];
        $currencyKey = $arr['hidCurrencyKey'];
        $currencyRate = $arr['currencyRate'];
        $arrAmount = $arr['amount'];
        $recipientTypeKey = $arr['selRecipientType'];
       
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
        
        $arrCustomerKey = $arr[$hidInputName];

        $arrTemp = array();     

        $rsRevenueCol = $revenueCashIn->searchDataRow(array($revenueCashIn->tableName.'.pkey'), ' and '.$revenueCashIn->tableName.'.pkey in ('.$this->oDbCon->paramString($arrRevenueKey,',').')');
        $rsRevenueCol = array_column($rsRevenueCol,null,'pkey');
        
        // validasi kalo kosong revenuekey nya, pelanggan wajib isi
        // jgn loop dr arrRevenue karena selectbox gk disabled 
        for($i=0;$i<count($arrAmount);$i++){ 
            
            //cek lagi, katanya bisa terjadi unknown resource jika dibuka validasinya
			
			// hanya jika unknownsource dicentang
            if ($arrRevenueKey[$i] == 0 && empty($arrCustomerKey) && $isUnknownSource == 0) 
                $this->addErrorList($arrayToJs,false,$this->errorMsg[$errName][1]);  
             
            if ( $arrAmount[$i] <=0 ){ 
                $this->addErrorList($arrayToJs,false,$this->errorMsg['amount'][1]); 
			}else{
                $rsRevenue = $rsRevenueCol[$arrRevenueKey[$i]];
                if(!empty($rsRevenue)){
                    $revenueKey = CASH_BANK_TYPE['isnottemporary'];
                }else{
                    $revenueKey = CASH_BANK_TYPE['temporary'];
           
                }
                                
                
                array_push($arrTemp,$revenueKey);
            }
          
        }
		
		// validasi jenis detail harus sama semua, temporary atau bukan
        $arrTemp = array_unique($arrTemp);
        $totalTemp = count($arrTemp);

        if($totalTemp > 1)
        	$this->addErrorList($arrayToJs,false,$this->errorMsg['cashBank'][6]);
 
		
        if($currencyKey <> CURRENCY['idr'] && $this->unformatNumber($currencyRate) <= 0 )
           $this->addErrorList($arrayToJs,false,$this->errorMsg['rate'][5]);

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
        
        $tablekey = $this->getTableKeyAndObj($this->tableName ,array('key'))['key'];  
 
        
        //cek sudah ad yg jenisnya bukan revenue tp outstandingnya sudah beda 
		$rsCashBank = $cashBank->searchDataRow( array($cashBank->tableName.'.pkey',$cashBank->tableName.'.code'),
                                                 ' and refkey = '.$this->oDbCon->paramString($id).' 
                                                   and reftabletype = '.$tablekey.'
                                                   and '.$cashBank->tableName.'.amount <> '.$cashBank->tableName.'.outstanding
                                                   and '.$cashBank->tableName.'.revenuekey = 0 
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
         
        $totalRs = count($rsDetail);
		
		$amount = 0;
        $arrDesc = array();
		
		for($i=0;$i<$totalRs; $i++){ 
            $amount += $rsDetail[$i]['amount'];
            array_push($arrDesc,$rsDetail[$i]['trdesc']); 
		}	
                
		$outstanding = (empty($rsDetail[0]['revenuekey'])) ?  $amount : 0; // kalo sudah jelas transaksinya, gk ad outstanding

		$desc = implode(' , ', $arrDesc);

	        $rsCashBank = $cashBank->addCashBank($rsHeader,$this->tableName, 
                                             
                                             array(
                                                 'customerkey' => $rsHeader[0]['customerkey'], 
                                                 'employeekey' => $rsHeader[0]['employeekey'] ,
                                                 'supplierkey' => $rsHeader[0]['supplierkey'] ,
                                                 'attnName' => $rsHeader[0]['attnname'],
                                                 'coakey' => $rsHeader[0]['coakey'],
                                                 'desc' => $desc, 
                                                 'amount' => $amount, 
                                                 'revenuekey' => $rsDetail[0]['revenuekey'], 
                                                 'outstanding' => $outstanding, 
                                                 'overwriteGL' => 1
                                             )); 		 
		if(!empty($rsCashBank['pkey'])){ 
			$arrCashBank['cashBankKey'] = $rsCashBank['pkey'];   
            $this->updateGL($rsHeader,$rsCashBank);
		}
	} 


    function updateGL($rs,$rsCashBank){
        if (!USE_GL) return;
        
        
//        $this->setLog($rs,true);
         
        $warehouse = new Warehouse();
        $generalJournal = new GeneralJournal(); 
        $chartOfAccount = new ChartOfAccount();
        $revenueCashIn = new RevenueCashIn();
        $coaLink = new COALink(); 
//        $tax = new Tax();
        
        $headerCurrencyKey = $rs[0]['currencykey'];
        $rate = $rs[0]['rate'];
        $rsKey = $generalJournal->getTableKeyAndObj($this->tableName);
        
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
        

        $amount =  $rs[0]['grandtotal'] * $rate;
        $coaKey =  $rs[0]['coakey'];
        $coaCounterKey = $chartOfAccount->getDataRowById($rs[0]['coakey'])[0]['countercoakey'];
        

		$temp++;
		$arr['hidCOAKey'][$temp] = $coaKey; 
		$arr['selBusinessUnitKey'][$temp] = 0; //0 karena tidak berdasarkan bisnis unit
		$arr['debit'][$temp] = $amount;  
		$arr['credit'][$temp] = 0;  
		$arr['debitSource'][$temp] = $rs[0]['grandtotal'] ; 
		$arr['creditSource'][$temp] =  0; 
		$arr['selCurrencyKey'][$temp] = $headerCurrencyKey ; 
		$arr['rate'][$temp] = $rate ; 
		$arr['trdescDetail'][$temp] = $rsCashBank['code']; 

        
        $arrRevenueKey = array();
        foreach($rsDetail as $row){
            array_push($arrRevenueKey,$row['revenuekey']);
        }
        
        //biar ga query ulang
        $rsRevenueInCol = $revenueCashIn->searchDataRow(array($revenueCashIn->tableName.'.pkey',$revenueCashIn->tableName.'.coakey'), ' and '.$revenueCashIn->tableName.'.pkey in ('.$this->oDbCon->paramString($arrRevenueKey,',').')');
        $rsRevenueInCol = array_column($rsRevenueInCol,null,'pkey');
        
        for($i=0;$i<count($rsDetail);$i++){
            
            $rsRevenueIn = $rsRevenueInCol[$rsDetail[$i]['revenuekey']];
            
            $revenueCoaKey = (empty($rsDetail[$i]['revenuekey'])) ? $coaCounterKey : $rsRevenueIn['coakey'] ;
            
          
                    
            $temp++;
            $arr['hidCOAKey'][$temp] = $revenueCoaKey; 
            $arr['selBusinessUnitKey'][$temp] = $rsDetail[$i]['businessunitkey']; 
            $arr['debit'][$temp] = 0; 
            $arr['credit'][$temp] = $rsDetail[$i]['amount'] * $rate;
            $arr['debitSource'][$temp] = 0; 
            $arr['creditSource'][$temp] =  $rsDetail[$i]['amount']; 
            $arr['selCurrencyKey'][$temp] = $headerCurrencyKey ; 
            $arr['rate'][$temp] = $rate ; 
			$arr['trdescDetail'][$temp] = ''; 
                
        }
  
		$arrayToJs = $generalJournal->addData($arr);
        
		if (!$arrayToJs[0]['valid'])
                throw new Exception('<strong>'.$rs[0]['code'] . '</strong>. '.$this->errorMsg[504].' '.$arrayToJs[0]['message']);    
    }
    
    function reCountGrandtotal($arrParam){

        $grandtotal = 0;
        $amount = 0;

        $arrAmount = $arrParam['amount']; 

        $arrARDetail = array();

        for ($i=0;$i<count($arrAmount);$i++){

            $arrAmount[$i] = $this->unFormatNumber($arrAmount[$i]);
            if (empty($arrAmount[$i]) ) 
                continue;

            $amount += $this->unFormatNumber($arrAmount[$i]);
        } 

        $grandtotal = $amount; 

        $reCountResult = array();
        $reCountResult['grandtotal'] = $grandtotal; 

        return $reCountResult;
				
	}
    
    function normalizeParameter($arrParam, $trim=false){
        
        $reCountResult = $this->reCountGrandtotal($arrParam);  
        $arrParam['total'] = $reCountResult['grandtotal'];
   
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
                
		// kalo unknown source 
        if($arrParam['chkUnknownSource'] == 1){ 
            $arrParam['hidEmployeeKey'] = 0;
            $arrParam['hidCustomerKey'] = 0;
            $arrParam['hidSupplierKey'] = 0;
		}
		
        //kalau idr currency rate nya 1
        if($arrParam['hidCurrencyKey'] == CURRENCY['idr'])
            $arrParam['currencyRate'] = 1;
        $arrParam = parent::normalizeParameter($arrParam,true); 
        
        return $arrParam;
        
        
    }
    
    function cancelTrans($rsHeader,$copy, $GLCancelDate = '00 / 00 / 0000'){ 
		$id = $rsHeader[0]['pkey']; 


        $cashBank = new CashBank();
        $cashBank->cancelCashBank($rsHeader,$this->tableName);
        
        $this->cancelGLByRefkey($rsHeader[0]['pkey'],$this->tableName,$GLCancelDate);

        
		if ($copy)
			$this->copyDataOnCancel($id);	 

	} 

     function getDetailWithRelatedInformation($pkey,$criteria=''){
		 
		// jgn connect ke cust, karena sdh tdk didetail
		 
        $sql = 'select
            '.$this->tableNameDetail.'.*, 
            '.$this->tableRevenue.'.name as revenuename
          from
            '.$this->tableNameDetail.' 
                left join '.$this->tableRevenue.' on '. $this->tableNameDetail.'.revenuekey = '.$this->tableRevenue.'.pkey 
          where   
            '. $this->tableNameDetail.'.refkey in  ('.$this->oDbCon->paramString($pkey,',') . ') ' ;

         $sql .= $criteria; 
         
         return $this->oDbCon->doQuery($sql);

    }
	
		function getTransactionDescription($arrKey,$userkey= ''){
          
        // yg boleh diakses
//          $arrAvailableField = array(  
//                                      array('code' => 'customername', 'param' => 'CUSTOMER_NAME', 'tableReference' => array('tableName' => $this->tableCustomer, 
//																														 'refField' => 'pkey', 
//																														 'field' => $this->tableCustomer.'.name',
//																														 'refkey' => 'customerkey'
//																														)), 
//                                      array('code' => 'sender', 'param' => 'SENDER_NAME', 'field' => $this->tableName.'.attnname'), 
//                                   	  array('code' => 'detaildesc', 'param' => 'TRANSACTION_DESCRIPTION', 'tableDetail' => array('tableName' => $this->tableNameDetail,  
//																																 'field' => $this->tableNameDetail.'.trdesc'
//																																)), 
//        );
			 
            
              $arrAvailableField = array(   
								    array('tableName' => $this->tableName, 'param' => 'CUSTOMER_NAME', 'tableReference' => array('tableName' => $this->tableCustomer,  
																													 'field' => $this->tableCustomer.'.name',
																													 'refkey' => 'customerkey'
																													)), 
                                    array('tableName' =>  $this->tableName, 'param' => 'SENDER_NAME', 'field' => $this->tableName.'.attnname'), 
              
                                    array('tableName' =>  $this->tableNameDetail, 'param' => 'TRANSACTION_DESCRIPTION', 'field' => $this->tableNameDetail.'.trdesc' ),  
								    array('tableName' => $this->tableNameDetail, 'param' => 'TRANSACTION_TYPE', 'tableReference' => array('tableName' => $this->tableRevenue,  
																													 'field' => $this->tableRevenue.'.name',
																													 'refkey' => 'revenuekey'
																													)), 
        );
         
            
        return $this->stitchDescriptionV2(array('field' => $arrAvailableField, 'pkey' => $arrKey, 'userkey' => $userkey ));
	 }
	 
}

?>
