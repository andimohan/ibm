<?php
  
class CashIn extends BaseClass{ 
 
   function __construct(){
		
		parent::__construct();
       
		$this->tableName = 'cash_in_header';
		$this->tableNameDetail = 'cash_in_detail'; 
		$this->tableStatus = 'transaction_status';
        $this->tableRevenue = 'revenue_cash_in';
        $this->tableCOA = 'chart_of_account';
        $this->tableWarehouse = 'warehouse';
        $this->tableCurrency = 'currency';
        //$this->tableCustomer = 'customer';
        $this->isTransaction = true; 
        
        $this->useMasterRevenue = $this->loadSetting('useMasterRevenue');
		
        $this->securityObject = 'CashIn'; 
       
        $this->newLoad = true;
	   
        $this->arrDataDetail = array(); 
        $this->arrDataDetail['pkey'] = array('hidDetailKey');
        $this->arrDataDetail['refkey'] = array('pkey','ref');
        $this->arrDataDetail['coakey'] = array('hidCOAKey');
        $this->arrDataDetail['revenuekey'] = array('hidRevenueKey');
        $this->arrDataDetail['amount'] = array('amount','number');
        $this->arrDataDetail['trdesc'] = array('trdesc');
    
        $this->arrData = array(); 
        $this->arrData['pkey'] = array('pkey', array('dataDetail' => array('dataset' => $this->arrDataDetail)));
        $this->arrData['code'] = array('code');
        $this->arrData['refkey'] = array('refkey');
        $this->arrData['reftable'] = array('reftable');
        $this->arrData['trdate'] = array('trDate','date');
        $this->arrData['recipientname'] = array('recipientName');
        $this->arrData['coakey'] = array('hidCOAHeaderKey');
        $this->arrData['trdesc'] = array('trDesc');
        $this->arrData['grandtotal'] = array('total','number');
        //$this->arrData['islinked'] = array('islinked'); 
        $this->arrData['statuskey'] = array('selStatus'); 
        $this->arrData['warehousekey'] = array('selWarehouseKey');
        $this->arrData['bankrefcode'] = array('bankRefCode');
        $this->arrData['currencykey'] = array('hidCurrencyKey');
        $this->arrData['rate'] = array('currencyRate', 'number');
        //$this->arrData['customerkey'] = array('hidCustomerKey');     
         
         
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'date','title' => 'date','dbfield' => 'trdate','default'=>true, 'width' => 100, 'align' =>'center', 'format' => 'date'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'warehouseName','title' => 'warehouse','dbfield' => 'warehousename',  'width' => 100));
		array_push($this->arrDataListAvailableColumn, array('code' => 'bankrefcode','title' => 'bankRef','dbfield' => 'bankrefcode','default'=>false, 'width' => 100));
		array_push($this->arrDataListAvailableColumn, array('code' => 'from','title' => 'from','dbfield' => 'recipientname', 'default'=>true, 'width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'account','title' => 'account','dbfield' => 'codename', 'default'=>true, 'width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'currency','title' => 'curr','dbfield' => 'currencyname', 'default'=>true, 'width' => 60, 'align' =>'center'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'total','title' => 'total','dbfield' => 'grandtotal', 'default'=>true, 'width' => 120, 'align' =>'right',  'format' => 'number'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
        array_push($this->arrDataListAvailableColumn, array('code' => 'desc','title' => 'note','dbfield' => 'trdesc',  'width' => 250));


		$this->arrSearchColumn = array ();
		array_push($this->arrSearchColumn, array('Kode', $this->tableName . '.code'));
		array_push($this->arrSearchColumn, array('COA', $this->tableCOA . '.code')); 
		array_push($this->arrSearchColumn, array('COA', $this->tableCOA . '.name')); 
		array_push($this->arrSearchColumn, array('Dari', $this->tableName . '.recipientname'));
		array_push($this->arrSearchColumn, array('Tanggal', $this->tableName . '.trdate'));
		array_push($this->arrSearchColumn, array('Catatan', $this->tableName . '.trdesc') );
		array_push($this->arrSearchColumn, array('Gudang', $this->tableWarehouse . '.name') ); 
		array_push($this->arrSearchColumn, array('Ref. Bank', $this->tableName . '.bankrefcode') ); 


        $this->printMenu = array();
        array_push($this->printMenu,array('code' => 'print', 'name' => $this->lang['printTransaction'],  'icon' => 'print', 'url' => 'print/cashIn'));
        array_push($this->printMenu,array('code' => 'printVoucher', 'name' => $this->lang['printVoucher'],  'icon' => 'print', 'url' => 'print/cashBankVoucherFromBankIn'));
    
        array_push($this->filterCriteria, array('title' => $this->lang['warehouse'], 'field' => 'warehousekey'));
       
       $this->includeClassDependencies(array(
            'ChartOfAccount.class.php',
            'CashBank.class.php',
            'COALink.class.php',
            'GeneralJournal.class.php',
            'RevenueCashIn.class.php'

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
               '.$this->tableCurrency.'.name as currencyname
			FROM '.$this->tableStatus.', '.$this->tableName.' 
                left join '. $this->tableCOA.' on ' . $this->tableCOA .'.pkey = ' . $this->tableName .'.coakey  
                left join '.$this->tableWarehouse.' on '.$this->tableName.'.warehousekey = '.$this->tableWarehouse.'.pkey
                left join '.$this->tableCurrency.' on '.$this->tableName.'.currencykey = '.$this->tableCurrency.'.pkey
			WHERE '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey 
 	      ' .$this->criteria ; 
       
        $sql .=  $this->getCOACriteria() ;
        $sql .=  $this->getWarehouseCriteria() ;
       
		 //  left join '. $this->tableCustomer.' on ' . $this->tableCustomer .'.pkey = ' . $this->tableName .'.customerkey 
       return $sql;
    }
        
    function afterStatusChanged($rsHeader){   
        // retrieve latest status
        $rsHeader = $this->getDataRowById($rsHeader[0]['pkey']);
        if ($rsHeader[0]['statuskey'] == 2)
            $this->changeStatus($rsHeader[0]['pkey'],3); 
    }
    
    function validateForm($arr,$pkey = ''){ 
		   
		$arrayToJs = parent::validateForm($arr,$pkey); 
		
		$chartOfAccount = new ChartOfAccount(); 
        $revenueCashIn = new RevenueCashIn();
        
        
		$arrCOAkey = $arr['hidCOAKey']; 
		$arrAmount = $arr['amount']; 
		$arrCOAHeaderKey = $arr['hidCOAHeaderKey'];
		
		//validasi kalo status gk menunggu gk bisa edit 
		if (!empty($pkey)){
			$rs = $this->getDataRowById($pkey);
			if ($rs[0]['statuskey'] <> 1){
				$this->addErrorList($arrayToJs,false,$this->errorMsg[212]);
			}
		}   
		
		if(empty($arrCOAHeaderKey)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['coa'][1]); 
		}	
         
        if($this->useMasterRevenue){ 

            for($i=0;$i<count($arrCCOkey);$i++) { 

                if (empty($arrCCOkey[$i]) )  
                    $this->addErrorList($arrayToJs,false, $this->errorMsg['revenue'][1]); 	

                if (!empty($arrCCOkey[$i]) && $this->unFormatNumber($arrAmount[$i]) <= 0){
                    $rsCCO = $revenueCashIn->getDataRowById($arrCCOkey[$i]); 
                    $this->addErrorList($arrayToJs,false,$rsCCO[0]['name'] .'. '. $this->errorMsg[503]); 
                }
            }

        }else{
                for($i=0;$i<count($arrCOAkey);$i++) { 

                    if (empty($arrCOAkey[$i])){ 
                        $this->addErrorList($arrayToJs,false, $this->errorMsg['coa'][1]); 	
                    }
                    if (!empty($arrCOAkey[$i]) && $this->unFormatNumber($arrAmount[$i]) <= 0){
                        $rsCOA = $chartOfAccount->getDataRowById($arrCOAkey[$i]);
                        $this->addErrorList($arrayToJs,false,$rsCOA[0]['code'] . ' - ' .$rsCOA[0]['name']. '. ' . $this->errorMsg[503]); 
                    }
                }

        }

		
		return $arrayToJs;
	 }  

	function validateConfirm($rsHeader){
        
	 }		

	function confirmTrans($rsHeader){
		$id = $rsHeader[0]['pkey'];
        
	    $revenueCashIn = new RevenueCashIn();
		 
		//$cashMovement = new CashMovement();  
        //$note = $rsHeader[0]['code'] .'. Kas Masuk. '; 
		
        $rsDetail = $this->getDetailById($rsHeader[0]['pkey']); 
		
		/*for($i=0;$i<count($rsDetail); $i++){		 
		   $cashMovement->updateCashMovement($id,$rsHeader[0]['coakey'],$rsDetail[$i]['amount'],$this->tableName, 0, $note .$rsDetail[$i]['trdesc'],$rsHeader[0]['trdate'] );
		}*/	 
        
            
		for($i=0;$i<count($rsDetail); $i++){		 
		    //$cashMovement->updateCashMovement($id,$rsHeader[0]['coakey'],-$rsDetail[$i]['amount'],$this->tableName, 0, $note.$rsDetail[$i]['trdesc'],$rsHeader[0]['trdate']);

            if($this->useMasterRevenue){
                $rsRevenueCashIn = $revenueCashIn->getDataRowById($rsDetail[$i]['revenuekey']); 
                $coakey = $rsRevenueCashIn[0]['coakey'];
                $sql = 'update '.$this->tableNameDetail.'  
                        set coakey = '.$this->oDbCon->paramString($coakey).'
                        where pkey = ' .$rsDetail[$i]['pkey'];
                $this->oDbCon->execute($sql);
            }
            
		}	 
        
         
		$arrCashBank = array();
		if( $this->isActiveModule('CashBank') ){
			$cashBank = new CashBank(); 
			$rsCashBank = $cashBank->addCashBank($rsHeader,$this->tableName, array('amount' =>  $rsHeader[0]['grandtotal'])); 
			$arrCashBank['cashToKey'] = $rsCashBank['pkey'];  
		}
        
		//update jurnal umum 
        $this->updateGL($rsHeader,$arrCashBank);
	} 
    
	function updateGL($rs,$arrCashBank){
        if (!USE_GL) return;
        
        $warehouse = new Warehouse();
        $generalJournal = new GeneralJournal(); 
        $chartOfAccount = new ChartOfAccount();
        
        $desc = array();
        if (!empty($rs[0]['recipientname']))
            array_push($desc, $rs[0]['recipientname']);
        
        if (!empty($rs[0]['trdesc']))
            array_push($desc, $rs[0]['trdesc']);
        
        $desc = html_entity_decode(implode(chr(13),$desc));
        $rsKey = $generalJournal->getTableKeyAndObj($this->tableName);
        
        $rsCOA = $chartOfAccount->getDataRowById($rs[0]['coakey']);
        
        $currencykey = $rsCOA[0]['currencykey'];
        $rate = (!empty($rs[0]['rate'])) ? $rs[0]['rate'] : 1;
        
		$arr = array();
		$arr['pkey'] = $generalJournal->getNextKey($generalJournal->tableName);
		$arr['code'] = 'xxxxx';
		$arr['refkey'] = $rs[0]['pkey'];
		$arr['refTableType'] = $rsKey['key'];
		$arr['trDate'] =  $this->formatDBDate($rs[0]['trdate'],'d / m / Y'); 
		$arr['trDesc'] = $desc;
		$arr['selWarehouseKey'] = $rs[0]['warehousekey'];
		$arr['createdBy'] = 0; 
		
        $temp = -1;
        
        $totalAmount = 0 ;
        
        $rsDetail = $this->getDetailById($rs[0]['pkey']); 
        for($i=0;$i<count($rsDetail);$i++){
            $totalAmount += $rsDetail[$i]['amount']; 
        } 
  
        $temp++;
        $arr['hidCOAKey'][$temp] = $rs[0]['coakey'];
        $arr['debit'][$temp] = $totalAmount * $rate;  
        $arr['credit'][$temp] = 0; 	  
        $arr['selCurrencyKey'][$temp] = $currencykey ; 
        $arr['debitSource'][$temp] = $totalAmount; 
        $arr['creditSource'][$temp] = 0; 
        $arr['rate'][$temp] = $rate ;   
        $arr['trdescDetail'][$temp] = html_entity_decode($rs[0]['trdesc']);	
		
		if(!empty($arrCashBank))
        	$arr['refCashBankKey'][$temp] =  $arrCashBank['cashToKey']; 

        
		for($i=0;$i<count($rsDetail);$i++){
            $temp++;
            $arr['hidCOAKey'][$temp] = $rsDetail[$i]['coakey'];
            $arr['debit'][$temp] = 0;
            $arr['credit'][$temp] =  $rsDetail[$i]['amount'] * $rate;
            $arr['selCurrencyKey'][$temp] = $currencykey ; 
            $arr['debitSource'][$temp] = 0; 
            $arr['creditSource'][$temp] = $rsDetail[$i]['amount']; 
            $arr['rate'][$temp] = $rate ;   
            $arr['trdescDetail'][$temp] = html_entity_decode($rsDetail[$i]['trdesc']);
			
			
			if(!empty($arrCashBank))
				$arr['refCashBankKey'][$temp] = '';
        }
		
		
		$arrayToJs = $generalJournal->addData($arr);
        
		if (!$arrayToJs[0]['valid'])
                throw new Exception('<strong>'.$rs[0]['code'] . '</strong>. '.$this->errorMsg[504].' '.$arrayToJs[0]['message']);    
    }

    
	 
	function cancelTrans($rsHeader,$copy){ 
		$id = $rsHeader[0]['pkey']; 
		  	  
		/*$cashMovement = new CashMovement();  
		$cashMovement->cancelMovement($id,$this->tableName);*/
		         
		if( $this->isActiveModule('CashBank') ){
			$cashBank = new CashBank();
			$cashBank->cancelCashBank($rsHeader,$this->tableName);
		}
		
		if ($copy)
			$this->copyDataOnCancel($id);	 
        
         
        $this->cancelGLByRefkey($rsHeader[0]['pkey'],$this->tableName);
	} 
    
    function reCountGrandtotal($arrParam){

				$grandtotal = 0;
				$amount = 0;
				
				$arrCOAKey = $arrParam['hidCOAKey'];
				$arrAmount = $arrParam['amount']; 
				
				$arrARDetail = array();
				$aR = new AR();
				
				for ($i=0;$i<count($arrCOAKey);$i++){
					
				    $arrAmount[$i] = $this->unFormatNumber($arrAmount[$i]);
					if ( empty($arrCOAKey[$i]) || empty($arrAmount[$i]) )  
						continue;
					
					$amount += $this->unFormatNumber($arrAmount[$i]);
				} 
				
				$grandtotal = $amount; 

				$reCountResult = array();
				$reCountResult['grandtotal'] = $grandtotal; 
				
				return $reCountResult;
				
	}
     
    function getDetailWithRelatedInformation($pkey,$criteria=''){
        $sql = 'select
	   			'.$this->tableNameDetail .'.*,
                '.$this->tableRevenue.'.name as revenuename, 
                '.$this->tableCOA.'.name as coaname,
                concat(' . $this->tableCOA .'.code, " - " , ' . $this->tableCOA .'.name ) as coacodename
			  from
			  	'. $this->tableNameDetail .'
                    left join  '.$this->tableRevenue.' on '. $this->tableNameDetail .'.revenuekey = '.$this->tableRevenue.'.pkey
                    left join  '.$this->tableCOA.' on '. $this->tableNameDetail .'.coakey = '.$this->tableCOA.'.pkey 
			  where 
			  	'.$this->tableNameDetail .'.refkey = '.$this->oDbCon->paramString($pkey);
        
        $sql .= $criteria;
		return $this->oDbCon->doQuery($sql);
    }
	
 
/*    function getDetailWithRelatedInformation($pkey,$criteria=''){
        
            $sql = 'select
	   			'.$this->tableNameDetail .'.*,
                '.$this->tableCost.'.name as costname, 
                 concat('.$this->tableCOA. '.code,\' - \','.$this->tableCOA. '.name) as coaname 
			  from
			  	'. $this->tableNameDetail .' 
                    left join  '.$this->tableCost.' on '. $this->tableNameDetail .'.costkey = '.$this->tableCost.'.pkey
                    left join  '.$this->tableCOA.' on '. $this->tableNameDetail .'.coakey = '.$this->tableCOA.'.pkey 
			  where  
			  	'.$this->tableNameDetail .'.refkey = '.$this->oDbCon->paramString($pkey);
         

        $sql .= $criteria;
		return $this->oDbCon->doQuery($sql);
    }*/
    

    function normalizeParameter($arrParam, $trim = false){ 
        
        $revenueCashIn = new RevenueCashIn(); 
        
        //$isFullReceive = 1;   
        //$arrParam['refkey'] = (isset($arrParam['refkey'])) ? $arrParam['refkey'] : 0;
        //$arrParam['reftable'] = (isset($arrParam['reftable'])) ? $arrParam['reftable'] : '';
        //$arrParam['islinked'] = (!empty($arrParam['islinked'])) ? 1 : 0; 

        if($this->useMasterRevenue){ 
            $arrRevenuekey = $arrParam['hidRevenueKey']; 
            for($i=0;$i<count($arrParam['hidDetailKey']);$i++){ 
                $rsRevenue = $revenueCashIn->getDataRowById($arrRevenuekey[$i]); 
                $arrParam['hidCOAKey'][$i] = $rsRevenue[0]['coakey'];
            }
        }

        $reCountResult = $this->reCountGrandtotal($arrParam);  
        $arrParam['total'] = $reCountResult['grandtotal'];
 
        
        //kalau idr currency rate nya 1
        if($arrParam['hidCurrencyKey'] == CURRENCY['idr'])
            $arrParam['currencyRate'] = 1;
        
        
        $arrParam = parent::normalizeParameter($arrParam,true); 

        
        return $arrParam;
    }
     
	function getTransactionDescription($arrKey,$userkey= ''){
                   
        // yg boleh diakses
//          $arrAvailableField = array(  
////                                    array('code' => 'trdesc', 'param' => 'DESCRIPTION', 'field' => $this->tableName.'.trdesc'),  
//                                      array('code' => 'sender', 'param' => 'SENDER_NAME', 'field' => $this->tableName.'.attnname'), 
//                                   	  array('code' => 'detaildesc', 'param' => 'TRANSACTION_DESCRIPTION', 'tableDetail' => array('tableName' => $this->tableNameDetail,
//																																 'refField' =>  'refkey',
//																																 'refkey' => $this->tableNameDetail.'refkey',
//																																 'field' => $this->tableNameDetail.'.trdesc')), 
//        );
        
        $arrAvailableField = array(  
                        array('tableName' => $this->tableName, 'param' => 'SENDER_NAME', 'field' => $this->tableName.'.attnname'),  
                        array('tableName' => $this->tableName, 'param' => 'DESCRIPTION', 'field' => $this->tableName.'.trdesc'),   
                        array('tableName' =>  $this->tableNameDetail, 'param' => 'TRANSACTION_DESCRIPTION', 'field' => $this->tableNameDetail.'.trdesc' ),  
        );
		
        return $this->stitchDescriptionV2(array('field' => $arrAvailableField, 'pkey' => $arrKey, 'userkey' => $userkey ));
	 }
    
}
?>