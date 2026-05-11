<?php

class ChartOfAccount extends Baseclass{
  
    function __construct($rsRunningPeriod = array()){
		
		parent::__construct();
    
		$this->tableName = 'chart_of_account';  
		$this->tableStatus = 'master_status';  
        $this->coaActivePeriod = 'chart_of_account_active_period';
		$this->securityObject = 'ChartOfAccount'; 
        $this->coaAmount = 'chart_of_account_amount';
		$this->tableJournalHeader = 'general_journal_header';   
		$this->tableJournalDetail = 'general_journal_detail';   
        $this->tableNameCounter = 'chart_of_account_counter';
        $this->tableCurrency = 'currency';
		
        if (empty($rsRunningPeriod))
            $this->rsRunningPeriod = $this->getRunningPeriod();
        else
            $this->rsRunningPeriod = $rsRunningPeriod;
        
         
        $this->arrData = array(); 
        $this->arrData['pkey'] = array('pkey');
        $this->arrData['code'] = array('code');
        $this->arrData['statuskey'] = array('selStatus');
        $this->arrData['name'] = array('name');
        $this->arrData['orderlist'] = array('orderlist','number');
        $this->arrData['parentkey'] = array('selCategory');
        $this->arrData['isleaf'] = array('isleaf');
        $this->arrData['iscashbank'] = array('chkCashBank');
        $this->arrData['currencykey'] = array('selCurrency');

        $this->arrData['isusevoucher'] = array('chkIsUseVoucher');
        $this->arrData['outcode'] = array('outCode');
        $this->arrData['incode'] = array('inCode');
        $this->arrData['incounter'] = array('inCounter','number');
        $this->arrData['outcounter'] = array('outCounter','number');
        $this->arrData['countercoakey'] = array('hidCounterCOAKey');
        $this->arrData['digit'] = array('digit','number');
        $this->arrData['resettypekey'] = array('selResetType'); 
        /*$this->arrData['accountno'] = array('accountNo');
        $this->arrData['accountbank'] = array('accountBank');
        $this->arrData['accountname'] = array('accountName');*/
        
        //$this->coaPrivileges = false;
        
        $this->arrLockedTable = array();
        $defaultFieldName = 'coakey';
        array_push($this->arrLockedTable, array('table'=>'general_journal_detail','field'=>$defaultFieldName));
        array_push($this->arrLockedTable, array('table'=>'coa_link','field'=>$defaultFieldName));
           
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'account','title' => 'account','dbfield' => 'coaname','default'=>true, 'width' => 400, 'sortable' => false));  
        array_push($this->arrDataListAvailableColumn, array('code' => 'total','title' => 'total','dbfield' => 'amount', 'default'=>true, 'width' => 200, 'align' => 'right', 'format' => 'accounting'));
        
        $this->includeClassDependencies(array(
              'Employee.class.php', 
              'GeneralJournal.class.php', 
              'COALink.class.php',  
              'CashBank.class.php',
              'CustomCode.class.php',
              'Currency.class.php',
              'CurrencyRate.class.php',
              'AP.class.php',
              'AR.class.php',
              'Currency.class.php',
              'Supplier.class.php',
              'Customer.class.php'   
        )); 
        $this->overwriteConfig();
        
	}
	
function getQuery(){
	   
	   $sql = '
				select
					'.$this->tableName. '.*,
                    concat('.$this->tableName. '.code,\' - \','.$this->tableName. '.name) as coaname, 
					'.$this->tableCurrency.'.name as currencyname, 
					'.$this->tableStatus.'.status as statusname, 
                    ('.$this->tableName. '.amount)  as amount 
				from 
					'.$this->tableName . '
                     left join '. $this->tableCurrency .' on '. $this->tableName .'.currencykey = '. $this->tableCurrency .'.pkey,
                    '.$this->tableStatus.' 
				where  		
					'.$this->tableName . '.statuskey = '.$this->tableStatus.'.pkey  
                    
 		' .$this->criteria ; 
		   
        $sql .=  $this->getCOACriteria() ;
        
        return $sql;
    }

	
	 function validateForm($arr,$pkey = ''){
		    
		$arrayToJs = array();
		
		$code = $arr['code'];
		$name = $arr['name'];  
        $categorykey = $arr['selCategory'];
        
        $rsCOA = $this->getDataRowById($categorykey); 
         
        $generalJournal = new GeneralJournal();
        $rsJournal = $generalJournal->getDetailByColumn('coakey',$categorykey,true,'','', 'limit 1');
         
        // nanti diganti coba boleh ganti nama  
        if(!empty($pkey)){
            $rs = $this->getDataRowById($pkey);
            if ($rs[0]['systemVariable'] == 1)   
			 $this->addErrorList($arrayToJs,false,'<strong>'.$rs[0]['name'].'</strong>. ' . $this->errorMsg[212], true); 
        }
        
         
        // cek hanya utk COA yg leaf  
        if($rsCOA[0]['isleaf'] == 1&& !empty($rsJournal))
            $this->addErrorList($arrayToJs,false,'<strong>'.$rsCOA[0]['code'].' - '.$rsCOA[0]['name'].'</strong>. '. $this->errorMsg['coa'][4]);	 
          
	 	$rs = $this->isValueExisted($pkey,'code',$code,2);	 
		if(empty($code)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['code'][1]);
		}else if(count($rs) <> 0){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['code'][2]);
		}
         
		$rsCategory = $this->isValueExisted($pkey,'name',$name,2);
		if(empty($name)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['coa'][1]);
		}else if(count($rsCategory) <> 0){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['coa'][2]);
		}
		  
		return $arrayToJs;
	 }
		
	function searchDataForAutoComplete($fieldname='',$searchkey='',$mustmatch=false,$searchCriteria='',$orderCriteria='', $limit=''){
		$sql = 'select
					'.$this->tableName. '.pkey,

					'.$this->tableName. '.currencykey,
concat('.$this->tableName. '.code," - ",'.$this->tableName.'.name) as value 
				from 
					'.$this->tableName . ','.$this->tableStatus.' 
				where  		
					'.$this->tableName . '.statuskey = '.$this->tableStatus.'.pkey
			';
	
		if(!empty($fieldname)){
			
			$sql .= ' and ' ;
			
			if($mustmatch)
				$sql .=  $fieldname .' = '. $this->oDbCon->paramString($searchkey);
			else
				$sql .=  $fieldname .' like '. $this->oDbCon->paramString('%'.$searchkey.'%');
		}
				
		if($searchCriteria <> '')
			$sql .= ' ' .$searchCriteria;
	
        $sql .=  $this->getCOACriteria() ;
        
		if($orderCriteria <> ''){
			$sql .= ' ' .$orderCriteria;
	 
	 	}
			
		if($limit <> '')
			$sql .= ' ' .$limit;
		    
		return $this->oDbCon->doQuery($sql);	
	} 
    
    
	function afterUpdateData($arrParam, $action){
		$security = new Security(); 
		$customCode = new CustomCode(); 
		$this->updateLeaf();
		$this->updateOrderList(); 
		$this->updateRootInformation($arrParam['pkey']);
		 
		if($security->hasSecurityAccess( $this->userkey ,$security->getSecurityKey($customCode->securityObject),10))
			$this->updateCounter($arrParam,true);

            
    }
     
	function updateRootInformation($id){
        
        $arrRoot = $this->getRootInformation($id);  
        $rs = $this->getDataRowById($id);
        $rsParent =$this->getDataRowById($rs[0]['parentkey']);
        
        $sql = '
                    UPDATE	
                     '.$this->tableName .'
                    SET	  
                     debittype = '.$rsParent[0]['debittype'].',
                     rootkey = '.$this->oDbCon->paramString($arrRoot['rootkey']).',
                     rootpath = '.$this->oDbCon->paramString($arrRoot['rootpath']).'  
                    WHERE	
                     pkey = '.$this->oDbCon->paramString($id).' 

            ';    
        $this->oDbCon->execute($sql); 
         

    } 
       
	function updateLeaf(){
	 
		$sql = 'update ' . $this->tableName . ' set isleaf =  0';
		$this->oDbCon->execute($sql);
			
		$rs = array ();
		
		$sql = 'select * from ' . $this->tableName . ' where '.$this->tableName . '.parentkey =  0 and  ' . $this->tableName . '.statuskey = 1  order by orderlist asc';
		$rsTree = $this->oDbCon->doQuery($sql);	
		$this->updateLeafChild ($rsTree,$rs); 
		 
	}
	
	function updateLeafChild ($arrChild,&$rs) {
		 		
		for ($i=0;$i<count($arrChild);$i++) {   
			$sql = 'select  * from  ' . $this->tableName . ' where '.$this->tableName . '.parentkey = ' .$this->oDbCon->paramString($arrChild[$i]['pkey']) .  '  and  ' . $this->tableName . '.statuskey = 1 order by orderlist asc' ;  
			$rsTemp =  $this->oDbCon->doQuery($sql);
			if (empty($rsTemp)){
				$sql = 'update ' . $this->tableName . ' set isleaf =  1 where pkey = ' .$this->oDbCon->paramString($arrChild[$i]['pkey'])   ; 
				$this->oDbCon->execute($sql);	
			}else{		
				$this->updateLeafChild ($rsTemp,$rs);
			}
		}
	
	}  
    
	function getChildren($parentkey=0, &$arrChild=array()){
		// utk mencari semua node dibawah node $parentkey
		$rs = $this->searchData($this->tableName.'.statuskey',1,true,' and '.$this->tableName . '.parentkey = ' . $this->oDbCon->paramString($parentkey),' order by '.$this->tableName . '.code asc');
		 
		for ($i=0;$i<count($rs);$i++){ 
			 array_push($arrChild,$rs[$i]['pkey']);
			 if ($rs[$i]['isleaf'] == 0)
			 	$this->getChildren($rs[$i]['pkey'],$arrChild);
		}
		
		return $arrChild;
		 
	}
    
    function updateOrderList($parentkey = 0,&$startIncr = 0,$level = 0){
        $sql = 'select * from '.$this->tableName.' where parentkey = '. $parentkey .' order by code asc ';
        $rs =  $this->oDbCon->doQuery($sql);
        
        for ($i=0;$i<count($rs);$i++){
            $sql = 'update '.$this->tableName.' set orderlist  = '.$startIncr++.', level = '.$level.' where pkey = '. $rs[$i]['pkey'];
            $this->oDbCon->execute($sql);
            $this->updateOrderList($rs[$i]['pkey'],$startIncr,$level + 1);
        }  
    }
	   
    function updateParentAmountFromRoot($coakey,$firstCall = true){
        // fungsi ini hanya menghitung nilai parent berdasarkan coa children
        // harus update dr root, karena kalo edit kategori COA, 1 root harus diupdate ulang totalnya
		
        $rsCOA = $this->getDataRowById($coakey);    
        if ($firstCall){
              $coakey = $rsCOA[0]['rootkey']; 
        }
          
        $sql = 'select * from '.$this->tableName.' where parentkey = ' . $this->oDbCon->paramString($coakey) .' and isleaf = 0';  
        $rs = $this->oDbCon->doQuery($sql);  
 
        for($i=0;$i<count($rs);$i++){ 
              $this->updateParentAmountFromRoot($rs[$i]['pkey'],false);  
        }
         
        
        $sql = '
            select  
                sum('.$this->tableName. '.amount ) as amount ,
                sum('.$this->tableName. '.sourceamount ) as sourceamount 
            from 
                '.$this->tableName . '  
            where  		
                '.$this->tableName . '.statuskey =  1 and parentkey = '.$this->oDbCon->paramString($coakey).' 
        '; 
		
        $rsSubtotal =  $this->oDbCon->doQuery($sql);  
        
        $total = 0;
        $totalSource = 0;
        if (!empty($rsSubtotal[0]['amount']))
            $total = $rsSubtotal[0]['amount'];

// kalo bukan coa kas bank, gk ad source amount
        if (!empty($rsSubtotal[0]['sourceamount']))
            $totalSource = ($rsCOA[0]['iscashbank'] == 0) ? $total : $rsSubtotal[0]['sourceamount'];

        $sql = 'update '.$this->tableName.' set amount = '.$total.', sourceamount = '.$totalSource.' where pkey = ' . $this->oDbCon->paramString($coakey);
        //$this->setLog($sql,true);
        $this->oDbCon->execute($sql);  

        return $total;
 
    } 
    
    function calculateCYE($calculateFromGL = false, $criteria = ''){
          
        if ($calculateFromGL){ 
             $sql = 'select 
                    sum('.$this->tableJournalDetail.'.debitsource - '.$this->tableJournalDetail.'.creditsource) * '.$this->tableName.'.debittype as totalsource,
                    sum('.$this->tableJournalDetail.'.debit - '.$this->tableJournalDetail.'.credit) * '.$this->tableName.'.debittype as total
                from 
                   '.$this->tableJournalHeader.',
                   '.$this->tableJournalDetail.',
                   '.$this->tableName.'
                where 
                    '.$this->tableName.'.isleaf = 1 and
                    '.$this->tableJournalHeader.'.pkey = '.$this->tableJournalDetail.'.refkey and
                    '.$this->tableJournalDetail.'.coakey = '.$this->tableName.'.pkey   
                ' . $criteria;
             
        }else  {  
            // ini kayanya buat chart of account list
            $sql = 'select  coalesce(sum(amount),0)  as total, coalesce(sum(sourceamount),0)  as totalsource from  '.$this->tableName.'  where  '.$this->tableName.'.isleaf = 1 ';
        }
          
        // INCOME  
        $arrCOAType = array('income');
        $rsCOAKey = $this->searchData('','',true,' and '.$this->tableName.'.coatype in ('.implode(',',$this->oDbCon->paramString($arrCOAType)).')'); 
        $rsCOAKey = array_column($rsCOAKey,'pkey');
        
        $coaCriteria = ' and ' .$this->tableName.'.rootkey in ('.$this->oDbCon->paramString($rsCOAKey,',').')';    
        
        $rs =  $this->oDbCon->doQuery($sql . $coaCriteria); 
        $totalIncome = $rs[0]['total'];
        $totalSourceIncome = $rs[0]['totalsource'];
        
        // COST 
        $arrCOAType = array('expense');
        $rsCOAKey = $this->searchData('','',true,' and '.$this->tableName.'.coatype in ('.implode(',',$this->oDbCon->paramString($arrCOAType)).')'); 
        $rsCOAKey = array_column($rsCOAKey,'pkey');
        $coaCriteria = ' and ' . $this->tableName.'.rootkey in ('.$this->oDbCon->paramString($rsCOAKey,',').')';    
         
        $rs =  $this->oDbCon->doQuery($sql . $coaCriteria); 
        $totalCost = $rs[0]['total'];
        $totalSourceCost = $rs[0]['totalsource'];
          
        // bedain utk BL an COA List
        if($calculateFromGL){ 
            $totalCost *= -1; 
            $totalSourceCost *= -1; 
        }
        
        $totalRevenue = $totalIncome + $totalCost; 
        $totalSourceRevenue = $totalSourceIncome + $totalSourceCost; //untuk source amount
        
        $arrReturn['totalIncome'] = $totalIncome;
        $arrReturn['totalCost'] = $totalCost;
        $arrReturn['totalRevenue'] = $totalRevenue;
        
        //untuk source amount
        $arrReturn['totalSourceIncome'] = $totalSourceIncome;
        $arrReturn['totalSourceCost'] = $totalSourceCost;
        $arrReturn['totalSourceRevenue'] = $totalSourceRevenue;
         
        return $arrReturn;
    }
    
    function updateCurrentYearEarnings($calculateFromGL = false){  
        
        $rsTotalRevenue = $this->calculateCYE($calculateFromGL, ' and '. $this->tableJournalHeader.'.statuskey = 2');
        
        $coaLink = new COALink();
        $coaYearEarnings = $coaLink->getCOALink ('currentyearearnings');   
        
 
        $totalRevenue = $rsTotalRevenue['totalRevenue'];   
        $totalSourceRevenue = $rsTotalRevenue['totalRevenue'];   //karena currentyearearnings bukan termasuk iscashbank
        
        $sql = 'update  '. $this->tableName .'  set  amount = ' .$totalRevenue. ', sourceamount = ' .$totalSourceRevenue. '   where  pkey = '.$coaYearEarnings[0]['coakey'] ;    
        $this->oDbCon->execute($sql);  
 
        $this->updateParentAmountFromRoot($coaYearEarnings[0]['coakey']);
    } 
    
    function getRootInformation($id,&$rootpath=''){
          
        $rs = $this->getDataRowById($id);
        
        if ($rs[0]['parentkey'] == 0 ){ 
            return array('debittype'=>$rs[0]['debittype'], 'rootkey'=>$rs[0]['pkey'], 'rootpath'=> trim($rootpath));
        }
        
        $rootpath .= $rs[0]['parentkey'].' '; 
        
        return $this->getRootInformation($rs[0]['parentkey'],$rootpath); 
        
    }
     
    function temporaryUpdate(){
          
        //return;
      /*
        try{			 
			 
                if(!$this->oDbCon->startTrans())
				    throw new Exception($this->errorMsg[100]); 

                $rs = $this->searchData('','',true,' and '.$this->tableName.'.parentkey <> 0');
                for($i=0;$i<count($rs);$i++){
                    $arrRoot = $this->getRootInformation($rs[$i]['pkey']);

                    $sql = 'update '.$this->tableName.' set debittype = ' . $this->oDbCon->paramString($arrRoot['debittype']) .', rootkey = ' . $this->oDbCon->paramString($arrRoot['rootkey']) .', rootpath = ' . $this->oDbCon->paramString($arrRoot['rootpath']) .' where pkey = ' .$rs[$i]['pkey'];
                    $this->oDbCon->execute($sql);
                    
                    $this->updateParentAndCYE($rs[$i]['pkey']);
                }
            
            
            	$this->oDbCon->endTrans(); 
				$this->addErrorList($arrayToJs,true,$this->lang['dataHasBeenSuccessfullyUpdated']);     
				
			}catch(Exception $e){
				$this->oDbCon->rollback();
				$this->addErrorList($arrayToJs,false, $e->getMessage()); 
		  } */
    } 
     
	function getFirstPeriod(){
		$sql = 'select * from ' . $this->coaActivePeriod.' order by pkey asc limit 1'; 
        $rs = $this->oDbCon->doQuery($sql);
        return $rs;
	}
	
    function getRunningPeriod($month='',$year=''){ 
        $sql = 'select * from ' . $this->coaActivePeriod;
        
        if (!empty($month) && !empty($year))
            $sql .= ' where runningmonth = \''.$year.'-'.$month.'-01\'';
            
        $sql .= ' order by pkey desc limit 1';
        
        $rs = $this->oDbCon->doQuery($sql);
         
        return $rs;
    }
    
    function annualClosing($runningYear, $runningPeriodKey){
   
        $arrCOAType = array('income','expense');
        
        $sql = ' select 
                    '.$this->coaAmount.'.refkey,
                    '.$this->coaAmount.'.closingamount 
                from  
                    '.$this->coaAmount.', 
                    '.$this->tableName.' 
                where 
                    '.$this->coaAmount.'.refkey = '.$this->tableName.'.pkey and
                    '.$this->tableName.'.rootkey in 
                        (select pkey from '.$this->tableName.' where '.$this->tableName.'.coatype in ('.$this->oDbCon->paramString($arrCOAType,',').'))
                    and '.$this->tableName.'.isleaf = 1 
                    and periodkey = ' . $this->oDbCon->paramString($runningPeriodKey);
        
        
        $rsCOA = $this->oDbCon->doQuery($sql);  
        $rsCOA = array_column($rsCOA,'closingamount','refkey');
         
        $generalJournal = new GeneralJournal();
        $coaLink = new COALink();

        $total = 0;
        $arr = array();
        $arr['pkey'] = $generalJournal->getNextKey($generalJournal->tableName);
        $arr['code'] = 'xxxxx';
        $arr['refkey'] = 0;
        $arr['refTableType'] = 0;
        $arr['trDate'] = date(' 01 / 01 / '.($runningYear + 1));  
        $arr['cancelForPeriod'] = $runningPeriodKey;  
        $arr['refCode'] = '';

        $temp = -1; 

        foreach($rsCOA as $coakey=>$amount){ 

            if( $amount == 0) continue; 

            $total += $amount;

            // amount sudah positif negatif 
            $temp++; 
            $arr['hidCOAKey'][$temp] = $coakey;  
            $arr['debit'][$temp] =  0; 
            $arr['debitSource'][$temp] =  0; 
            $arr['credit'][$temp] = $amount;   
            $arr['creditSource'][$temp] = $amount;  
			$arr['selCurrencyKey'][$temp] =  CURRENCY['idr']; 
			$arr['rate'][$temp] = 1;   

        }

        $retainedearnings = $coaLink->getCOALink ('retainedearnings');
        $coaYearEarnings = $coaLink->getCOALink ('currentyearearnings');  

        $temp++; 
        $arr['hidCOAKey'][$temp] = $retainedearnings[0]['coakey']; 
        $arr['debit'][$temp] =  $total; 
        $arr['debitSource'][$temp] =  $total; 
        $arr['credit'][$temp] = 0; 
        $arr['creditSource'][$temp] = 0; 
        $arr['selCurrencyKey'][$temp] =  CURRENCY['idr']; 
        $arr['rate'][$temp] = 1; 
        $arr['hidSaveAndProceed'] = 1;
        $arr['annualClosingJournal'] = 1;

        if($total != 0)  $arrayToJs = $generalJournal->addData($arr); 
      
        return array('retainedEarnings' => $total);
         
        
    }

	function calculateRevaluation($date){
        
            $arrayToJs = array();
		
			// $date Y-m-d

			$ap = new AP();
			$ar = new AR(); 
			$supplier = new Supplier();
			$customer = new Customer();
		
			$startingDate = date('Y-m-01', strtotime($date));
			$lastDate = date('Y-m-t', strtotime($date));
 

            // validasi cek dulu sudah ada jurnal reval blm diperiode yang sama 
			$runningPeriodKey = $this->getRunningPeriod()[0]['pkey']; 
            $sql = 'select * from general_journal_header where statuskey <> 4 and isreval = 1 and monthlyclosingkey = ' . $this->oDbCon->paramString($runningPeriodKey);
            $rs = $this->oDbCon->doQuery($sql); 
            if(!empty($rs)) {
                $this->addErrorList($arrayToJs,false, $this->errorMsg['generalJournal'][10]);
                return $arrayToJs;
            }
            
        
			// harus cari pake kartu stok 
			$rsAR = $ar->searchARCard(date('d / m / Y', strtotime($lastDate)), ' and '.$ar->tableName.'.currencykey > 1 ');

			$rsARKey = $ar->getTableKeyAndObj($ar->tableName,array('key')); 
			$arrCustomerKey = array_column($rsAR, 'customerkey');
			$rsCustomer = $customer->searchDataRow(array($customer->tableName.'.pkey',
																	   $customer->tableName.'.reimbursearcoakey',
																	   $customer->tableName.'.arcoakey'
																	  ),
																 ' and '.$customer->tableName.'.pkey in ('.$customer->oDbCon->paramString($arrCustomerKey,',').')'
																 );
			$rsCustomer = array_column($rsCustomer, null, 'pkey');
			for ($i=0; $i<count($rsAR); $i++) {
				$customerKey = $rsAR[$i]['customerkey'];
				$rsAR[$i]['coakey'] = ($rsAR[$i]['artype'] == AR_TYPE['reimburse']) ? $rsCustomer[$customerKey]['reimbursearcoakey'] : $rsCustomer[$customerKey]['arcoakey'];
				$rsAR[$i]['reftablekey'] = $rsARKey['key'];
				$rsAR[$i]['table'] = 'ar';
				$rsAR[$i]['amount'] = $rsAR[$i]['outstanding'];
				$rsAR[$i]['amountidr'] = $rsAR[$i]['outstanding'] * $rsAR[$i]['rate'];
			}



			// AP

			//$sql = 'select * from ap where statuskey in (1,2) and currencykey > 1 and trdate between '.$ap->oDbCon->paramString($startingDate).'and '.$ap->oDbCon->paramString($lastDate);
			//$rsAP = $ap->oDbCon->doQuery($sql);

			$rsAP = $ap->searchAPCard(date('d / m / Y', strtotime($lastDate)), ' and '.$ap->tableName.'.currencykey > 1');


			$totalAR = 0;
			$totalARIDR = 0;
			foreach($rsAP as $row){
				$totalAR += $row['outstanding'];
				$totalARIDR += ($row['outstanding']*$row['rate']);
			}

			//echo $totalAR.'<br>';
			//echo $totalARIDR.'<br>';
			//die;

			$rsAPKey = $ap->getTableKeyAndObj($ap->tableName,array('key'));  
			$arrSupplierKey = array_column($rsAP, 'supplierkey');
			$rsSupplier = $supplier->searchDataRow(array($supplier->tableName.'.pkey',
																	   $supplier->tableName.'.reimburseapcoakey',
																	   $supplier->tableName.'.apcoakey'
																	  ),
																 ' and '.$supplier->tableName.'.pkey in ('.$supplier->oDbCon->paramString($arrSupplierKey,',').')'
																 );
			$rsSupplier = array_column($rsSupplier, null, 'pkey');
			for ($i=0; $i<count($rsAP); $i++) {
				$supplierKey = $rsAP[$i]['supplierkey'];
				$rsAP[$i]['coakey'] = ($rsAP[$i]['aptype'] == AP_TYPE['reimburse']) ? $rsSupplier[$supplierKey]['reimburseapcoakey'] : $rsSupplier[$supplierKey]['apcoakey'];
				$rsAP[$i]['reftablekey'] = $rsAPKey['key'];
				$rsAP[$i]['table'] = 'ap';
				$rsAP[$i]['amount'] = $rsAP[$i]['outstanding'];
				$rsAP[$i]['amountidr'] = $rsAP[$i]['outstanding'] * $rsAP[$i]['rate'];
				$rsAP[$i]['counter'] = -1;
			}


			//$sql = 'select * from chart_of_account where currencykey > 1';
			//$rsCOA = $this->oDbCon->doQuery($sql);

			$rsCOA = $this->sumRunningAmount('', date('d / m / Y', strtotime($lastDate)),' and '.$this->tableName.'.currencykey > 1 and  '.$this->tableName.'.isleaf = 1 and  '.$this->tableName.'.statuskey = 1');

			//$this->setLog($rsCOA,true);
			//die;

			$rsCOAKey = $this->getTableKeyAndObj($this->tableName,array('key')); 
			for ($i=0; $i<count($rsCOA); $i++) {
				$rsCOA[$i]['reftablekey'] = $rsCOAKey['key'];
				$rsCOA[$i]['amountidr'] = $rsCOA[$i]['amount'];
				$rsCOA[$i]['coakey'] = $rsCOA[$i]['pkey'];
				$rsCOA[$i]['table'] = 'chart_of_account';
			}

			$data = array_merge($rsAR, $rsAP, $rsCOA);
			$this->updateRevaluation($data, false, $lastDate);
        
        
            $this->addErrorList($arrayToJs,true, $this->errorMsg['dataHasBeenSuccessfullyUpdated']);
            return $arrayToJs;
	}
    
    function reverseClosingMonthly(){
		$arrayToJs = array();
         try{			  
                if(!$this->oDbCon->startTrans())
				    throw new Exception($this->errorMsg[100]); 
    
                $coaLink = new COALink();  
                $generalJournal = new GeneralJournal();
             
                $coaRetainedEarnings = $coaLink->getCOALink ('retainedearnings'); 
              
                $runningPeriodKey = $this->rsRunningPeriod[0]['pkey'];
                  
                //delete active period
                $sql = 'delete from ' . $this->coaActivePeriod .' where pkey = ' . $runningPeriodKey;
              	$this->oDbCon->execute($sql); 
              
                $runningPeriod = $this->getRunningPeriod();
                if (empty($runningPeriod)) return;
             
                $runningPeriodKey = $runningPeriod[0]['pkey'];
                $runningMonth = $this->formatDBDate($runningPeriod[0]['runningmonth'],'m');
                $runningYear = $this->formatDBDate($runningPeriod[0]['runningmonth'],'Y');
             
                $this->rsRunningPeriod = $runningPeriod;
               
             
                //delete jurnal reverse dan jurnal closing   
                //$rsGL = $generalJournal->searchData($this->tableJournalHeader .'.cancelforperiod', $runningPeriodKey);
                $rsGL = $generalJournal->searchDataRow(
                     array($this->tableJournalHeader.'.pkey', $this->tableJournalHeader.'.reversefor' ),
                    ' and '.$this->tableJournalHeader .'.cancelforperiod = ' .$this->oDbCon->paramString($runningPeriodKey) 
                );
             
                $rsGLReverseFor = array_column($rsGL,'reversefor');
                $rsGL = array_column($rsGL,'pkey');
                
                //ambil coakey yg nanti perlu di hitung ulang 
                $rsGLDetail = $generalJournal->getDetailByColumn('','',true,' and '.$this->tableJournalDetail.'.refkey in ('.$this->oDbCon->paramString($rsGL,',').') ');
             
                // hapus semua jurnal yg perlu dihapus
		// kalo nanti diubah seperti GPI, JGN DIHAPUS JURNAL REVERSE, karena jurnal awalnya tdk dicancel
                $sql = 'delete from ' . $this->tableJournalHeader .' where pkey in ('.$this->oDbCon->paramString($rsGL,',').')';
                $this->oDbCon->execute($sql); 
                 
                $sql = 'delete from ' . $this->tableJournalDetail .' where refkey in ('.$this->oDbCon->paramString($rsGL,',').')';
                $this->oDbCon->execute($sql);  
                
            
                $sql = 'update ' . $this->coaActivePeriod .' set isclosed = 0 where pkey = ' . $runningPeriodKey;  
                $this->oDbCon->execute($sql); 
             
                //delete amount tahun reverse
                $sql = 'delete from ' . $this->coaAmount .' where periodkey = ' . $runningPeriodKey;
              	$this->oDbCon->execute($sql);  
             
                // udpate status close = 1
                 $sql = 'update 
                            '.$this->tableJournalHeader.' 
                        set 
                            statuskey = 2
                        where 
                            statuskey = 3 and
                            year(trdate) = '.$runningYear.' and 
                            month(trdate) = '.$runningMonth;
              
                $this->oDbCon->execute($sql);
               
                 // cancel GL yg sudah pernah direverse
                foreach($rsGLReverseFor as $key){ 
                    if ($key == 0) continue;
                    $generalJournal->changeStatus($key,4,'',false,true);  
                }
             
             
                // hitung ulang semua coa, soalnya, ad yg GL reverse / closingan
                foreach($rsGLDetail as $glDetailRow)
                    $this->updateCOAAmount($glDetailRow['coakey']); 
              
             
                // hitung ulang CYE kalo beda tahun 
                if($runningMonth == 12){ 
                    // hitung ulang RE dan CYE 
                    $this->updateCurrentYearEarnings();  
                }
             
                $this->updateParentAmountFromRoot(0);
             
            	$this->oDbCon->endTrans(); 
            	$this->addErrorList($arrayToJs,true,$this->lang['dataHasBeenSuccessfullyUpdated']);     
				
             
             
			}catch(Exception $e){
				$this->oDbCon->rollback();
				$this->addErrorList($arrayToJs,false, $e->getMessage()); 
		  } 
    }
    
    function monthlyClosing(){
        
        $arrayToJs = array();
        
        // validasi ada kasbank yg blm direkon tidak
         
        $runningPeriodKey = $this->rsRunningPeriod[0]['pkey']; 
        $runningMonth = $this->formatDBDate($this->rsRunningPeriod[0]['runningmonth'],'m');
        $runningYear = $this->formatDBDate($this->rsRunningPeriod[0]['runningmonth'],'Y');

        if($this->isActiveModule('BankReconsiliation')){  
            $cashBank = new CashBank();
             
            $rsCashBank = $cashBank->searchDataRow(array($cashBank->tableName.'.pkey'),
                                                   ' and '.$cashBank->tableName.'.statuskey <> 4 
                                                     and '.$cashBank->tableName.'.isreconsile = 0
                                                     and month('.$cashBank->tableName.'.trdate) = '.$this->oDbCon->paramString($runningMonth).' 
                                                     and year('.$cashBank->tableName.'.trdate) = '.$this->oDbCon->paramString($runningYear).'
                                                   '
                                                  );
            
            if(!empty($rsCashBank))
				$this->addErrorList($arrayToJs,false, $this->errorMsg['coa'][6]); 
                
        }

		$generalJournal = new GeneralJournal();
		$rsGeneralJournal = $generalJournal->searchDataRow(array($generalJournal->tableName.'.pkey'),
                                                   ' and statuskey = 1
                                                     and month('.$generalJournal->tableName.'.trdate) = '.$this->oDbCon->paramString($runningMonth).' 
                                                     and year('.$generalJournal->tableName.'.trdate) = '.$this->oDbCon->paramString($runningYear).'
                                                   '
                                                  );
		
		 if(!empty($rsGeneralJournal))
				$this->addErrorList($arrayToJs,false, $this->errorMsg['generalJournal'][8]); 
        
        if(!empty($arrayToJs))
            return $arrayToJs;
        
        try{			  
              
                if(!$this->oDbCon->startTrans())
				    throw new Exception($this->errorMsg[100]); 
  
                $coaLink = new COALink();
                $coaYearEarnings = $coaLink->getCOALink ('currentyearearnings');  
                $coaRetainedEarnings = $coaLink->getCOALink ('retainedearnings'); 
 
                // select history periode sebelumnya
                $sql = 'select * from ' . $this->coaActivePeriod.' where pkey < ' .$runningPeriodKey. ' order by pkey desc limit 1 ';
                $rs =  $this->oDbCon->doQuery($sql);
                 
                $rsHistoryAmount = array();
                $rsHistorySourceAmount = array();
                if (!empty($rs)) { 
                    $sql = 'select * from ' . $this->coaAmount .' where  periodkey = ' . $rs[0]['pkey'];
                    $rsTemp =  $this->oDbCon->doQuery($sql); 
                    $rsHistoryAmount = array_column($rsTemp, 'closingamount', 'refkey'); 
                    $rsHistorySourceAmount = array_column($rsTemp, 'sourceclosingamount', 'refkey'); 
                }
            
                // insert history utk cache
                // sum semua jurnal yg terjadi di bln berjalan. 
                 $sql ='
                        select 
                            '.$this->tableName.'.pkey,
                            coalesce(coaamount.amount,0) as amount,
                            coalesce(coaamount.amountsource,0) as amountsource
                        from '.$this->tableName.' left join ( 
                                select  
                                    '.$this->tableName.'.pkey,
                                    sum('.$this->tableJournalDetail.'.debitsource - '.$this->tableJournalDetail.'.creditsource) as amountsource,
                                    sum('.$this->tableJournalDetail.'.debit - '.$this->tableJournalDetail.'.credit) as amount
                               from 
                                    '.$this->tableName.',
                                    '.$this->tableJournalHeader.', 
                                    '.$this->tableJournalDetail.'
                                where 
                                    '.$this->tableJournalHeader.'.statuskey in (2,3) and
                                     year(trdate) = '.$runningYear.' and 
                                     month(trdate) = '.$runningMonth .' and 
                                    '.$this->tableJournalHeader.'.pkey = '.$this->tableJournalDetail.'.refkey and
                                    '.$this->tableJournalDetail.'.coakey = '.$this->tableName.'.pkey
                                group by
                                    '.$this->tableJournalDetail.'.coakey
                        ) coaamount on '.$this->tableName.'.pkey = coaamount.pkey
                        where
                            '.$this->tableName.'.statuskey = 1
                        ';
            
//             $this->setLog($sql,true);
              
                $rsRunningAmount = $this->oDbCon->doQuery($sql);
                $rsRunningAmount = array_column($rsRunningAmount,null,'pkey');
             

                foreach($rsRunningAmount as $coakey=>$row){  
					$runningAmount = $row['amount'];
					$runningSourceAmount = $row['amountsource'];
                     
                    $prevAmount = isset($rsHistoryAmount[$coakey]) ? $rsHistoryAmount[$coakey] : 0; 
                    $prevSourceAmount = isset($rsHistorySourceAmount[$coakey]) ? $rsHistorySourceAmount[$coakey] : 0; 
                    if ($coakey == $coaYearEarnings[0]['coakey'] ){  
                        $BLDateCriteria = ' and month('.$this->tableJournalHeader.'.trdate) = '.$runningMonth.' and year('.$this->tableJournalHeader.'.trdate) = '.$runningYear;
                        $arrRevenue = $this->calculateCYE(true, ' and ('. $this->tableJournalHeader.'.statuskey in(2,3)) '.$BLDateCriteria); 
                        $runningAmount = $arrRevenue['totalRevenue']; 
                        $runningSourceAmount = $arrRevenue['totalSourceRevenue']; 
                    } 
                     
                    $amount = $runningAmount + $prevAmount;
                    $amountSource = $runningSourceAmount + $prevSourceAmount;


                    $sql = 'insert into ' . $this->coaAmount .' (refkey,periodkey,runningamount, sourcerunningamount,closingamount,sourceclosingamount) values ('.$coakey.','.$runningPeriodKey.','.$runningAmount.','.$runningSourceAmount.','.$amount.','.$amountSource.')';
                    $this->oDbCon->execute($sql);  
                }
              
                // lock jurnal yg sudah diclose
                $sql = 'update   '.$this->tableJournalHeader.'  set  statuskey = 3  where   statuskey = 2 and  year(trdate) = '.$runningYear.' and   month(trdate) = '.$runningMonth;
                $this->oDbCon->execute($sql);
              
                // update running month
                $sql = 'update ' . $this->coaActivePeriod .' set isclosed = 1';  
                $this->oDbCon->execute($sql); 
            
            
                // buat jurnal penutup 
                if ($runningMonth == 12)   $closingValue = $this->annualClosing($runningYear,$runningPeriodKey);   
            
                // jgn pake $runningMonth karena format tanggal 
                $newPeriodKey = $this->getNextKey($this->coaActivePeriod); 
                $sql = 'insert into ' . $this->coaActivePeriod .' (pkey,runningMonth) values ('.$newPeriodKey.',\''.$this->rsRunningPeriod[0]['runningmonth'].'\' + interval 1 month) ';  
                $this->oDbCon->execute($sql);  
            
            
            	$this->oDbCon->endTrans(); 
            	$this->addErrorList($arrayToJs,true,$this->lang['dataHasBeenSuccessfullyUpdated']);     
				
			}catch(Exception $e){
				$this->oDbCon->rollback();
				$this->addErrorList($arrayToJs,false, $e->getMessage()); 
		  } 
        
        return $arrayToJs;
    }
    
    function calculateAmountForCF( $startDt , $endDt , $arrCOAType ){
        
        $rsCOAKey = $this->searchData('','',true,' and '.$this->tableName.'.coasubtype in ('.implode(',',$this->oDbCon->paramString($arrCOAType)).')'); 

        $arrCriteria = array();
        for($i=0;$i<count($rsCOAKey);$i++) {  
            array_push($arrCriteria,$this->tableName.'.pkey = \''.$rsCOAKey[$i]['pkey'].'\''); 
            array_push($arrCriteria,$this->tableName.'.rootpath regexp \'[[:<:]]'.$rsCOAKey[$i]['pkey'].'[[:>:]]\''); 
        }


        $coaCriteria = ' and ('.implode(' or ' , $arrCriteria).')';     
        $latestDate = date('d / m / Y',strtotime(str_replace('\'','',$this->oDbCon->paramDate($startDt,' / ','Y-m-d')).' -1 day'));
        $rsLatest = $this->sumRunningAmount('',$latestDate,$coaCriteria);
        $rsCurrent = $this->sumRunningAmount($startDt, $endDt ,$coaCriteria);
        
        $rs = array();
        for($i=0;$i<count($rsCurrent);$i++){
            if (in_array($rsCurrent[$i]['coasubtype'], $arrCOAType)){
                $rsCurrent[$i]['previousamount'] = $rsLatest[$i]['amount'];
                $rs[$rsCurrent[$i]['coasubtype']] = $rsCurrent[$i]; 
            }
        }
        
        return $rs;
        
    }
    
    function sumRunningAmount($startDt = '', $endDt, $coaCriteria='', $forReport = 0, $invert = 1, $arrWarehouseKey = array()){ 
       
        $parentAmount = array();
        
        if (empty($startDt)) $startDt = '01 / 01 / 1970'; 
        
        //ambil dari closing per bln
        //baru ditambahkan dengan bln terakhir
          
        if ($forReport == FINANCIAL_REPORT['balanceSheet']){
            $coaLink = new COALink();
            $coaYearEarnings = $coaLink->getCOALink ('currentyearearnings');  
            $coaRetainedEarnings = $coaLink->getCOALink ('retainedearnings');
              
            // cek jika periode tgl akhir masih dalam chart_of_account_amount 
            $sql = 'select pkey as periodkey  from ' . $this->coaActivePeriod.'  where  runningmonth = '. $this->oDbCon->paramDate($endDt,' / ','Y-m-01');
            $rs = $this->oDbCon->doQuery($sql);
       
            if (empty($rs)){  
                // jika bkn dlm bln berjalan, ambil amount terakhir 
                $sql = 'select  pkey as periodkey, runningmonth  from ' . $this->coaActivePeriod.'  where runningmonth < '.$this->oDbCon->paramDate($endDt,' / ','Y-m-01').' and isclosed = 1 order by pkey desc limit 1';
                $rsHistory = $this->oDbCon->doQuery($sql);   
                
            } else{
                // jika masih dalam amount berjalan, ambil periode sebelumnya
                $prevPeriod = date('Y-m-01',strtotime(str_replace('\'','',$this->oDbCon->paramDate($endDt,' / ','Y-m-01')).' -1 month')); 
                $sql = 'select  pkey as periodkey, runningmonth  from   ' . $this->coaActivePeriod.'  where  runningmonth = '. $this->oDbCon->paramString($prevPeriod) .' and isclosed = 1 order by pkey desc limit 1';
               
                $rsHistory = $this->oDbCon->doQuery($sql); 
            }
                
            $rsAmountHistory = array();
            $arrLastAmount = array();
            if (!empty($rsHistory)){
                
                $startDt = date('01 / m / Y',strtotime($rsHistory[0]['runningmonth'].' +1 month')); 
 
                $sql = 'select * from '. $this->coaAmount.' where periodkey = ' . $rsHistory[0]['periodkey'] ; 
                $rsAmountHistory = $this->oDbCon->doQuery($sql);

                for($i=0;$i<count($rsAmountHistory);$i++){ 
                    $arrLastAmount[$rsAmountHistory[$i]['refkey']] =  $rsAmountHistory[$i]['closingamount'];
                }
            } 
        }
        
        
        $dtCriteria = ' and trdate between '.$this->oDbCon->paramDate( $startDt,' / ').' AND '.$this->oDbCon->paramDate( $endDt,' / '); 
          
        $closingJournalCriteria = ($forReport == FINANCIAL_REPORT['incomeStatement']) ? ' and annualclosingjournal = 0' : '';
        //$closingJournalCriteria = ($forReport == FINANCIAL_REPORT['incomeStatement']) ? '' : '';
        
        //  * '.$this->tableName.'.debittype 
        
        $warehouseCriteria = (!empty($arrWarehouseKey)) ? ' and '.$this->tableJournalHeader.'.warehousekey in ('.$this->oDbCon->paramString($arrWarehouseKey,',').') ' : '';
        $sql = '
            select 
                '.$this->tableName.'.pkey,
                '.$this->tableName.'.name,
                '.$this->tableName.'.rootkey,
                '.$this->tableName.'.parentkey,
                '.$this->tableName.'.debittype,
                concat('.$this->tableName. '.code,\' - \','.$this->tableName. '.name) as coaname, 
                '.$this->tableName.'.code, 
                '.$this->tableName.'.orderlist,
                '.$this->tableName.'.rootpath,
                '.$this->tableName.'.isleaf,
                '.$this->tableName.'.level,
                '.$this->tableName.'.coasubtype,
                '.$this->tableName.'.currencykey,
                coaamount.*
            from '.$this->tableName.' left join ( 
                    select  
                        '.$this->tableJournalDetail.'.coakey,
                        sum('.$this->tableJournalDetail.'.debit - '.$this->tableJournalDetail.'.credit) as amount ,
                        sum('.$this->tableJournalDetail.'.debitsource - '.$this->tableJournalDetail.'.creditsource) as sourceamount 
                    from 
                        '.$this->tableName.','.$this->tableJournalHeader.', '.$this->tableJournalDetail.' 
                    where 
                        '.$this->tableName.'.pkey = '.$this->tableJournalDetail.'.coakey and
                        '.$this->tableJournalHeader.'.pkey = '.$this->tableJournalDetail.'.refkey and
                        ('.$this->tableJournalHeader.'.statuskey = 2 or '.$this->tableJournalHeader.'.statuskey = 3 )
                        '.$warehouseCriteria.'
                        '.$dtCriteria.'
                        '.$coaCriteria.'
                        '.$closingJournalCriteria.'
                        group by coakey  
            )coaamount on '.$this->tableName.'.pkey = coaamount.coakey
            where '.$this->tableName.'.statuskey = 1
            '.$coaCriteria.'
            order by orderlist asc';
        
        $rs =  $this->oDbCon->doQuery($sql);
        
        // sum amount di root 
        for($i=0;$i<count($rs);$i++){
            //add amount rs dengan amount sebelumnya
            $lastAmount = (isset($arrLastAmount[$rs[$i]['pkey']])) ? $arrLastAmount[$rs[$i]['pkey']] : 0;  
             
            if ($forReport == FINANCIAL_REPORT['balanceSheet'] && ($rs[$i]['pkey'] == $coaYearEarnings[0]['coakey']) ){   
                $lastAmount *= $invert;
                 
                $arrRevenue = $this->calculateCYE(true, ' and ('. $this->tableJournalHeader.'.statuskey in(2,3)) '.$dtCriteria);
                $rs[$i]['amount'] = $arrRevenue['totalRevenue'] * $invert; 
            } 
            
             $rs[$i]['amount'] += $lastAmount; 
            
            //adj debit credit
            $rs[$i]['amount'] *= $invert;
            
            if ($rs[$i]['isleaf']){
                
                if(empty($rs[$i]['amount']))
                    $rs[$i]['amount'] = 0;
                
                $arrPath = explode(' ',$rs[$i]['rootpath']);
                for($j=0;$j<count($arrPath);$j++){ 
                    if (!isset($parentAmount[$arrPath[$j]]))
                        $parentAmount[$arrPath[$j]] = 0;
              
                    $parentAmount[$arrPath[$j]] += $rs[$i]['amount'];
                }    
                
            } 
        } 
        
        for($i=0;$i<count($rs);$i++){
              if (!$rs[$i]['isleaf'] && isset($parentAmount[$rs[$i]['pkey']])) 
                  $rs[$i]['amount'] = $parentAmount[$rs[$i]['pkey']]; 
              
        }
         
        return $rs;
    }
    
    function getTotalClosedPeriod(){
        $sql = 'select count(pkey) as total from ' . $this->coaActivePeriod .' where isclosed = 1';
        $rs = $this->oDbCon->doQuery($sql);	
        
        return $rs[0]['total'];
    }
    
    function normalizeParameter($arrParam, $trim = false){ 
   		$security = new Security(); 
		$customCode = new CustomCode(); 
		
        if(!isset($arrParam['selCurrency']) || empty($arrParam['selCurrency']))
            $arrParam['selCurrency'] = CURRENCY['idr'];
                 
        $arrParam['selStatus'] = 1;
        $arrParam['isleaf'] = 0; 
        
        if($arrParam['chkCashBank'] == 0){
            $arrParam['outCode'] = '';
            $arrParam['inCode'] = '';
            $arrParam['inCounter'] = '0';
            $arrParam['outCounter'] = '0';
            $arrParam['hidCounterCOAKey'] = '0';  
        }
            
        
        $resetType = $arrParam['selResetType'];
        switch ($resetType) {  
				case '2':
                    $arrParam['trDate'] =  $arrParam['selDailyPeriod']; 
				    $arrParam['inIncrementNumber']  = (!empty($arrParam['inDailyIncrement'])) ? $arrParam['inDailyIncrement'] : 0;   
				    $arrParam['outIncrementNumber']  = (!empty($arrParam['outDailyIncrement'])) ? $arrParam['outDailyIncrement'] : 0;   
                    break;
                case '3': 
                    // sementara
                    $arrParam['trDate'] =  $this->formatForMonthly($arrParam['selMonthlyPeriod']); 
//                  $arrParam['trDate'] =  date('d / m / Y',strtotime($arrParam['selMonthlyPeriod']));  
                    $arrParam['inIncrementNumber'] = (!empty($arrParam['inMonthlyIncrement'])) ? $arrParam['inMonthlyIncrement'] : 0;   
                    $arrParam['outIncrementNumber'] = (!empty($arrParam['outMonthlyIncrement'])) ? $arrParam['outMonthlyIncrement'] : 0;   
                    break;
                case '4':
                    $arrParam['trDate'] = $this->formatForAnnualy($arrParam['selAnnuallyPeriod']); 
//                    $arrParam['trDate'] =  date('01 / 01 / Y',strtotime($arrParam['selAnnuallyPeriod']));  
					$arrParam['inIncrementNumber'] = (!empty($arrParam['inAnnuallyIncrement'])) ? $arrParam['inAnnuallyIncrement'] : 0;  
					$arrParam['outIncrementNumber'] = (!empty($arrParam['outAnnuallyIncrement'])) ? $arrParam['outAnnuallyIncrement'] : 0;  
                    break;  
                default : 
                    $arrParam['trDate'] = DEFAULT_EMPTY_DATE; 
				    $arrParam['inIncrementNumber']  = (!empty($arrParam['inIncrement'])) ? $arrParam['inIncrement'] : 0;   
				    $arrParam['outIncrementNumber']  = (!empty($arrParam['outIncrement'])) ? $arrParam['outIncrement'] : 0;   
                    break;

        } 
         
         $arrParam['inIncrementNumber'] = ( $arrParam['inIncrementNumber'] <= 0) ? 1 :  $arrParam['inIncrementNumber'];
         $arrParam['outIncrementNumber'] = ( $arrParam['outIncrementNumber'] <= 0) ? 1 :  $arrParam['outIncrementNumber'];
        
		if(!$security->hasSecurityAccess( $this->userkey ,$security->getSecurityKey($customCode->securityObject),10)){
			unset($this->arrData['digit']);  
			unset($this->arrData['resettypekey']);
			unset($this->arrData['incode']);
			unset($this->arrData['outcode']);
			unset($this->arrData['incounter']);
			unset($this->arrData['outcounter']);
//			unset($this->arrData['countercoakey']); // gk perlu, karena gk tergantung akses custom code 
		}
		
        $arrParam = parent::normalizeParameter($arrParam,true); 
     
      
        return $arrParam; 
    }

    function afterStatusChanged($rs){
         $this->updateLeaf(); 
    } 
    
	function delete($id, $forceDelete = false,$reason = '',$GLCancelDate = '00 / 00 / 0000'){
		
		$rs = $this->getDataRowById($id); 
        
		$arrayToJs =  array();
		 
		try{			 
                $arrayToJs = $this->validateDelete($id);
                if (!empty($arrayToJs)) 
                    return $arrayToJs;
			  
                if(!$this->oDbCon->startTrans())
				    throw new Exception($this->errorMsg[100]); 
            
            	$sql = 'delete from  '.$this->tableName.' where pkey = ' . $this->oDbCon->paramString($id);
				$this->oDbCon->execute($sql); 
                 
                $this->updateLeaf();
             
                $this->setTransactionLog(DELETE_DATA,$id);	
                    
				$this->oDbCon->endTrans();
										 
			    $this->addErrorLog(true,$this->lang['dataHasBeenSuccessfullyUpdated']);    
				
			}catch(Exception $e){
				$this->oDbCon->rollback();
				if (!empty($e->getMessage()))
                     $this->addErrorLog(false,$e->getMessage());
		}			
			
		 return $this->getErrorLog(); 
	}
    
    function updateCOAAmount($coakey){
            $coa = new ChartOfAccount();
            $rsCOA = $coa->getDataRowById($coakey);
    
            // diupdate, ambil dr closingan terahkir, terus jumlahkan semua amount yg masih blm closed
            $sql = 'select * from '.$this->coaActivePeriod.' where isclosed = 1 order by runningmonth desc limit 1';
            $rsActivePeriod = $this->oDbCon->doQuery($sql);

            $sql = 'select 
                        coalesce(closingamount,0) as amount,
                        coalesce(sourceclosingamount,0) as sourceamount  
                    from 
                        '.$this->coaAmount.' 
                    where 
                        '.$this->coaAmount.'.refkey = '.$this->oDbCon->paramString($coakey).' and
                        '.$this->coaAmount.'.periodkey = '.$this->oDbCon->paramString($rsActivePeriod[0]['pkey']);
  
            $rsAmountClosed = $this->oDbCon->doQuery($sql);
            $amount = (isset($rsAmountClosed[0]['amount'])) ? $rsAmountClosed[0]['amount'] : 0;
 
            $sourceAmountClosed = ($rsCOA[0]['iscashbank'] == 0) ? $rsAmountClosed[0]['amount'] : $rsAmountClosed[0]['sourceamount'];
            $sourceAmount = (isset($sourceAmountClosed) ) ?  $sourceAmountClosed : 0;
        
            // ambil total jurnal di bln yg blm closing
            $sql = 'select 
                        sum(debit-credit) as amount,
                        sum(debitsource-creditsource) as sourceamount  
                    from 
                        '.$this->tableJournalHeader.',
                        '.$this->tableJournalDetail.' 
                    where 
                        '.$this->tableJournalHeader.'.pkey = '.$this->tableJournalDetail.'.refkey and
                        '.$this->tableJournalHeader.'.statuskey = 2 and
                        '.$this->tableJournalDetail.'.coakey = '.$this->oDbCon->paramString($coakey);

             
            $rsAmount = $this->oDbCon->doQuery($sql);
                
        
            $amount += $rsAmount[0]['amount'];
        
            //karena yang berlaku hanya coa yang is cash bank
            $sourceAmount += ($rsCOA[0]['iscashbank'] == 0) ?  $rsAmount[0]['amount'] : $rsAmount[0]['sourceamount'] ;


            $sql = 'update '.$this->tableName.' set amount = ' . $amount .', sourceamount = ' . $sourceAmount .'  where pkey = ' . $coakey;
            $this->oDbCon->execute($sql);    
    }
    
    /*function validateDelete($rs){

        $id = $rs[0]['pkey'];
        
		if ($rs[0]['systemVariable'] == 1)   
			$this->addErrorLog(false,'<strong>'.$rs[0]['name'].'</strong>. ' . $this->errorMsg[211], true);
		 
		$generalJournal = new GeneralJournal();
	 	$rsDetail = $generalJournal->getDetailByColumn('coakey',$id,true,'','','limit 1');
				
		if(!empty($rsDetail)) 
			$this->addErrorLog(false,"<strong>" . $rs[0]['code'] .' - ' .$rs[0]['name'] . "</strong>. " . $this->errorMsg['coa'][4],true); 
	    
	  
	 } */
  
    function updateCounter($arrParam, $formUpdate = false){
        // $this->setLog($arrParam,true);
        
        $returnCounter = 0 ;
        
        $coakey = $arrParam['pkey'];
        $resetType = $arrParam['selResetType'];
        $trDate = isset($arrParam['trDate']) ? $arrParam['trDate'] : DEFAULT_EMPTY_DATE;
        
        // kalo gk pernah reset, overwrite,  set tgl jd empty date_create
        if ($resetType == 1)  $trDate = DEFAULT_EMPTY_DATE;

        $sql = 'select 
                pkey, counterin,counterout 
            from  
                ' .$this->tableNameCounter. '  
            where 
                refkey = '.$this->oDbCon->paramString($coakey).' and 
                resettypekey = '.$this->oDbCon->paramString($resetType).' and 
                trdate = ' .$this->oDbCon->paramDate($trDate,' / ',$format='Y-m-d') ;

        $rs = $this->oDbCon->doQuery($sql);  
       
        if (empty($rs)){  
            
             if ($formUpdate){
                 $inIncrementNumber = $arrParam['inIncrementNumber'];
                 $outIncrementNumber = $arrParam['outIncrementNumber']; 
             }else{ 
                 $inIncrementNumber =  ($arrParam['amount'] > 0 ) ? 2 : 1;
                 $outIncrementNumber = ($arrParam['amount'] < 0 || $arrParam['creditType']<0) ? 2 : 1;
                 $returnCounter = 1;
             }
            
             $sql = 'insert into 
                        ' .$this->tableNameCounter. ' (refkey,resettypekey,trdate,counterin,counterout)
                     values (
                        '.$this->oDbCon->paramString($coakey).', 
                        '.$this->oDbCon->paramString($resetType).',
                        '.$this->oDbCon->paramDate($trDate,' / ').',
                        '.$this->oDbCon->paramString($this->unformatNumber($inIncrementNumber)).',
                        '.$this->oDbCon->paramString($this->unformatNumber($outIncrementNumber)).'
                    ) '; 
        }else{
            
             if ($formUpdate){
                 // kalo dr update form udah pasti gk perlu kirim return value 
                 
                 $sql = 'update 
                            ' .$this->tableNameCounter. ' 
                        set 
                            counterin = '.$this->oDbCon->paramString($this->unformatNumber($arrParam['inIncrementNumber'])).',
                            counterout = '.$this->oDbCon->paramString($this->unformatNumber($arrParam['outIncrementNumber'])).' 
                        where 
                            pkey = ' . $this->oDbCon->paramString($rs[0]['pkey']) ;
                 
             }else{
                 
                 //update dr transaksi 
                if($arrParam['amount']<0 || $arrParam['creditType']<0){
                    $updateField = 'counterout';
                    $incementNumber = $rs[0]['counterout'];
                }else{
                    $updateField = 'counterin';
                    $incementNumber = $rs[0]['counterin'];
                }
                   
                 $returnCounter = $incementNumber;
                 
                $sql = 'update 
                        ' .$this->tableNameCounter. ' 
                    set 
                        '.$updateField.' = '.$this->oDbCon->paramString($this->unformatNumber($incementNumber+1)).' 
                    where 
                        pkey = ' . $this->oDbCon->paramString($rs[0]['pkey']) ; 
             }
             
           
        }
        
        //$this->setLog($sql,true);
        $this->oDbCon->execute($sql);
         
        
        return $returnCounter;
    }
    
    
    function getRunningNumber($pkey,$resetType = 0,$trDate){ 
         
          
        switch ($resetType) { 
				case '2': 
                    $criteria = ' and trdate = ' .$this->oDbCon->paramDate($trDate,' / '); 
					break;
                
				case '3':
					$criteria = ' and month(trdate) = ' .$this->oDbCon->paramDate($trDate,' / ','m') .' and year(trdate) = '. $this->oDbCon->paramDate($trDate,' / ','Y') ; 
					break; 
                
				case '4':
                    $criteria = ' and year(trdate) = ' .$this->oDbCon->paramDate($trDate,' / ','Y'); 
					break;
                
        }
        
        
        $sql = 'select counterin,counterout from '.$this->tableNameCounter.' where refkey = ' . $this->oDbCon->paramString($pkey) .' and resettypekey = ' .$this->oDbCon->paramString($resetType) ;
          
        if (!empty($criteria))
            $sql .= $criteria;
        
        $rs = $this->oDbCon->doQuery($sql); 
        
        return $rs ;
        
    }
	
    // overwrite karena ad warehouse Criteria
	function generateComboboxOpt($opt = array(),$queryOpt = array(),$preselected='',$relOpt = array()){
		// nanti dilihat perlu isset gk, atau selalu ditambahkan saja
		if(isset($queryOpt['criteria'])) $queryOpt['criteria'] .=  $this->getCOACriteria()  ;
		return parent::generateComboboxOpt($opt,$queryOpt,$preselected ,$relOpt );
	}
    
    function lockPeriod(){
        // cek ulang dri awal active periode sampe skrg ada tidak bulan yg sudah closing, tp blm dilock, kalo ad, dilock dulu
        
        // harusnya cukup ngelock sampe bulan dan tahun saat ini
        
    }
    
    function updateRevaluation($data, $annual = false, $date = '0000-00-00') {
 
			$date = ($date == '0000-00-00') ? date('d / m / Y') : $this->formatDBDate($date,'d / m / Y');
 
            
            // get Last Currency Rate
            $warehouse = new Warehouse();
            $currencyRate = new CurrencyRate();
            $coaLink = new COALink(); 
            $customer = new Customer(); 
            $supplier = new Supplier(); 
            $generalJournal = new GeneralJournal(); 
			
            $arrCurrencyRate = $currencyRate->getCurrencyLastRate('',$date,false);
            $arrCurrencyRate = $this->reindexDetailCollections($arrCurrencyRate, 'currencykey'); 
			 
			
			// sementara pake 1 warehouse dulu, gk split warehouse
			$warehousekey = $warehouse->getDefaultData(); 
			$rsCOA = $coaLink->getCOALink('lossprofitrate', $warehouse->tableName,$warehousekey, 0); 
			$profitLossCOAKey = $rsCOA[0]['coakey'];
			
			
			$businessUnitKey=0;
			 
			$dontReverseCOA = array();
            
			$arr = array();
			$arr['hidCOAKey'] = array();
			$arr['selBusinessUnitKey']= array();
			$arr['debit']= array();
			$arr['debitSource']= array();
			$arr['credit']= array();
			$arr['creditSource']= array();
			$arr['selCurrencyKey']= array();
			$arr['rate']= array();
			
			
            $temp = -1; 
			
			$totalDiff = 0;
			
            for ($i=0; $i<count($data); $i++) {
                $currencyKey = $data[$i]['currencykey'];
                $rate  = $arrCurrencyRate[$currencyKey][0]['rate'];
                
                if ($data[$i]['table'] == 'chart_of_account') {
                    $amountField = 'amount';
                    $amount = $rate * $data[$i]['sourceamount']; 
					
					array_push($dontReverseCOA, $data[$i]['coakey']);
					
                }else{ 
                	$amountField = 'amountidr';
                	$amount = $rate * $data[$i]['outstanding'];  
				}
				
				
                // amount yang lama disimpan juga
                if ($annual) {
                    $sql = 'insert into reval_currency_rate (trdate,refkey,reftabletype,currencykey,rate,amountidr) values (now(),'.$this->oDbCon->paramString($data[$i]['pkey']).','.$this->oDbCon->paramString($data[$i]['reftablekey']).','.$this->oDbCon->paramString($currencyKey).','.$this->oDbCon->paramString($data[$i]['rate']).','.$this->oDbCon->paramString($data[$i]['amountidr']).')';
                    $this->oDbCon->execute($sql);
                    $sql = 'update '.$data[$i]['table'].' set '.$amountField.' = '. $this->oDbCon->paramString($amount). ', rate = '. $this->oDbCon->paramString($rate) .' where pkey = ' . $data[$i]['pkey'];
                    $this->oDbCon->execute($sql); 
                }

                if ($amount != $data[$i]['amountidr']) {
                    $amountDiff = $amount - $data[$i]['amountidr'];
					if (isset($data[$i]['counter']) && $data[$i]['counter'] == -1 ) $amountDiff *=-1;
					
                    $temp++; 
                    $arr['hidCOAKey'][$temp] = $data[$i]['coakey'];
                    $arr['selBusinessUnitKey'][$temp]  = $businessUnitKey; //$data[$i]['businessunitkey'];
                    $arr['debit'][$temp] = $amountDiff; 
                    $arr['debitSource'][$temp] = 0; //$amountDiff; // coba di set nol, karena reval awal tahun harus selalu kebawa debit/credit nya
                    $arr['credit'][$temp] = 0;  
                    $arr['creditSource'][$temp] = 0 ; 
					$arr['selCurrencyKey'][$temp] = CURRENCY['idr']; 
					$arr['rate'][$temp] = 1; 
                    
					$totalDiff += $amountDiff; 
                }
                
            }
 			
			$temp++; 
			$arr['hidCOAKey'][$temp] = $profitLossCOAKey;
			$arr['selBusinessUnitKey'][$temp]  = $businessUnitKey; //$data[$i]['businessunitkey'];
			$arr['debit'][$temp] = 0; 			
			$arr['debitSource'][$temp] = 0 ;   
			$arr['credit'][$temp] = $totalDiff; 
			$arr['creditSource'][$temp] = 0; //$totalDiff; 
			$arr['selCurrencyKey'][$temp] =  CURRENCY['idr']; 
			$arr['rate'][$temp] = 1; 
			
			 
			
			// di group aja, nanti kasi opsi kalo memang ad pilihan 
			// di loop aj harusnya gk byk accountnya
			
			$runningPeriodKey = $this->getRunningPeriod()[0]['pkey'];
		
			$arrGL = array();   
            $arrGL['pkey'] = $generalJournal->getNextKey($generalJournal->tableName);
            $arrGL['code'] = 'xxxxx';
            $arrGL['refkey'] = 0;
            $arrGL['refTableType'] = '';
            $arrGL['trDate'] =  $date;
            $arrGL['createdBy'] = 0;
            $arrGL['refCode'] = '';
            $arrGL['selWarehouseKey'] = '';
			$arrGL['monthlyClosingKey'] = $runningPeriodKey ;
			$arrGL['isReval'] = 1 ;
		 
			$arrGL['hidCOAKey'] = array();
				
			$totalArr = count($arr['hidCOAKey']);
			$temp = -1;
			 
			
			for($i=0;$i<$totalArr;$i++){
				
				$found = false;
				
				$totalArrGL = count($arrGL['hidCOAKey']);
				for($j=0;$j<$totalArrGL;$j++){
					 
					if ($arrGL['hidCOAKey'][$j] == $arr['hidCOAKey'][$i]) {
						
						$arrGL['debit'][$j] +=  $arr['debit'][$i]; 		
						$arrGL['debitSource'][$j] += $arr['debitSource'][$i]; 	
						$arrGL['credit'][$j] +=  $arr['credit'][$i]; 		
						$arrGL['creditSource'][$j] += $arr['creditSource'][$i]; 	 
						
						$found = true;
						break;
					}
				}
				  
					
				if (!$found){ 
					
						$temp++;
						$arrGL['hidCOAKey'][$temp] = $arr['hidCOAKey'][$i];
						$arrGL['selBusinessUnitKey'][$temp]  = $arr['selBusinessUnitKey'][$i]; //$data[$i]['businessunitkey'];
						$arrGL['debit'][$temp] =  $arr['debit'][$i]; 		
						$arrGL['debitSource'][$temp] = $arr['debitSource'][$i]; 		
						$arrGL['credit'][$temp] = $arr['credit'][$i]; 		
						$arrGL['creditSource'][$temp] = $arr['creditSource'][$i]; 		
						$arrGL['selCurrencyKey'][$temp] = $arr['selCurrencyKey'][$i]; 	
						$arrGL['rate'][$temp] = $arr['rate'][$i]; 
					 
				}
				
			}
			
			 
			
            $arrayToJs = $generalJournal->addData($arrGL); 
			$generalJournal->changeStatus($arrayToJs[0]['data']['pkey'],2); 
			
			// kalo monthly, balikin di tgl 1 selanjutnya
			
			if(!$annual){

				$currDate = str_replace('\'','',$this->oDbCon->paramDate($date,' / ','Y-m-d')); 
				$currDate = new DateTime($currDate); 
				$currDate->add(new DateInterval('P1D'));  
			 	$datePlusOne = date_format($currDate,'d / m / Y');

				$arrGLReverse = array();   
				$arrGLReverse['pkey'] = $generalJournal->getNextKey($generalJournal->tableName);
				$arrGLReverse['code'] = 'xxxxx';
				$arrGLReverse['refkey'] = 0;
				$arrGLReverse['refTableType'] = '';
				$arrGLReverse['trDate'] =  $datePlusOne;
				$arrGLReverse['createdBy'] = 0;
				$arrGLReverse['refCode'] = '';
				$arrGLReverse['selWarehouseKey'] = '';
				$arrGLReverse['monthlyClosingKey'] = $runningPeriodKey ;
				$arrGLReverse['isReval'] = 1 ;

				$arrGLReverse['hidCOAKey'] = array();	
				$arrGLReverse['debit'] = array();
				$arrGLReverse['debitSource'] = array();
				$arrGLReverse['credit'] = array();
				$arrGLReverse['creditSource'] = array();
				
				$totalArr = count($arrGL['hidCOAKey']);
				 
				$totalDontReverse = 0;
				
				$temp = -1;
				for($j=0;$j<$totalArr;$j++){ 
					
					if (in_array($arrGL['hidCOAKey'][$j],$dontReverseCOA)) {
						$totalDontReverse +=  ($arrGL['debit'][$j] - $arrGL['credit'][$j]);
						continue;
					}
					
					$temp++;
					$arrGLReverse['hidCOAKey'][$temp] = $arrGL['hidCOAKey'][$j];
					$arrGLReverse['selBusinessUnitKey'][$temp] = $arrGL['selBusinessUnitKey'][$j];
					$arrGLReverse['selCurrencyKey'][$temp] = $arrGL['selCurrencyKey'][$j];
					$arrGLReverse['rate'][$temp] = $arrGL['rate'][$j];
					
					$arrGLReverse['debit'][$temp] = $arrGL['credit'][$j];
					$arrGLReverse['debitSource'][$temp] = $arrGL['creditSource'][$j];
					$arrGLReverse['credit'][$temp] = $arrGL['debit'][$j];
					$arrGLReverse['creditSource'][$temp] = $arrGL['debitSource'][$j]; 
				} 
 
				// kalo ad yg tdk direverse, seperti COA USD
				if ($totalDontReverse <> 0 ){
					
					// cari akun profitloss
					for($k=0;$k<count($arrGLReverse['hidCOAKey']);$k++){
						if ($arrGLReverse['hidCOAKey'][$k] == $profitLossCOAKey){
							
							$totalProfitLoss = $arrGLReverse['debit'][$k] - $arrGLReverse['credit'][$k];
							$totalProfitLoss -= $totalDontReverse;
							
							$arrGLReverse['debit'][$k] = $totalProfitLoss;
							$arrGLReverse['credit'][$k] =0;
							
							break;	
						} 
					}
					 
				} 
				
				if (!empty($arrGLReverse['hidCOAKey'])){
					$arrayToJsReverse = $generalJournal->addData($arrGLReverse); 
					$generalJournal->changeStatus($arrayToJsReverse[0]['data']['pkey'],2); 
				}
            		
				
			}
				
			
			
            if (!$arrayToJs[0]['valid']){  
				$errMsg = array();
				array_push($errMsg, $arrayToJs[0]['message']); 
				throw new Exception('<strong>'.$arrayToJs[0]['code'] . '</strong>. '.$this->errorMsg[504].' '.implode(',',$errMsg)); 
			}
                 

    }
   
	
function getEndDate($currDate,$reportType){
	
	// hati2, obj date selalu dikirim dalam bentuk reference
	// jd harus diconvert dulu agar tidak merubah nilai asli jika mau di add interval
	
	switch($reportType){
	
		case 1 : $periodInterval = 1;  
			     $formattedEndDate = date_format($currDate,'t / m / Y');
				 break;
		case 2 : $periodInterval = 12;  
			     $formattedEndDate = date_format($currDate,'31 / 12 / Y');
				 break;
		case 3 : 
		case 4 : $periodInterval = 3;  
				 $endDate = new DateTime(date_format($currDate,'Y-m-d')); 
				 $endDate->add(new DateInterval('P2M'));  
			     $formattedEndDate = date_format($endDate,'t / m / Y');
				 break; 
		case 5 : $periodInterval = 1;  
			     $formattedEndDate = date_format($currDate,'t / m / Y');
				 break; 
	}
	 
	return $formattedEndDate;
}


function generateIncome($arrCOAType,$startDt,$endDt,$arrWarehouse){
	
	$balanceAsPositive = $this->loadSetting('GLAsPositiveBalance');
	$balanceAsPositive = ($balanceAsPositive) ? -1 : 1;
 
	
	// $startDt : d / m / Y
	
    $rsCOAKey = $this->searchData('','',true,' and '.$this->tableName.'.coatype in ('.implode(',',$this->oDbCon->paramString($arrCOAType)).')');  
    
    $arrCriteria = array();
     
    for($i=0;$i<count($rsCOAKey);$i++) {  
        array_push($arrCriteria,$this->tableName.'.pkey = \''.$rsCOAKey[$i]['pkey'].'\''); 
        array_push($arrCriteria,$this->tableName.'.rootkey = \''.$rsCOAKey[$i]['pkey'].'\''); 
    }
     
    $coaCriteria = ' and ('.implode(' or ' , $arrCriteria).')';
    $rs = $this->sumRunningAmount($startDt,$endDt, $coaCriteria,FINANCIAL_REPORT['incomeStatement'],-1,$arrWarehouse); 
   
        
    for ($i=0;$i<count($rs);$i++){ 
  
			$GLStartDate = str_replace('\'','',$this->oDbCon->paramDate($startDt,' / ','Y-01-01'));
			$GLEndDate = str_replace('\'','',$this->oDbCon->paramDate($endDt,' / ','Y-12-31'));
			$rs[$i]['gl-url'] = 'reportGeneralLedger/'.$rs[$i]['pkey'].'/'.$GLStartDate.'/'.$GLEndDate;
			  
			$rs[$i]['balanceAsPositive'] = 1; // init
			if($rs[$i]['debittype'] == 1)  $rs[$i]['balanceAsPositive'] *= $balanceAsPositive; 
			if($balanceAsPositive == -1 && ($rs[$i]['pkey'] == 5 || $rs[$i]['rootkey'] == 5))   $rs[$i]['balanceAsPositive'] *= $balanceAsPositive;
		
    }
  
    $return['rs'] = array_column($rs,null,'pkey'); // agar mudah diakses per coa

    return $return;
} 
	
function getCOAAmount($arrCOAType, $startPeriod,$endPeriod,$reportType, $arrWarehouseKey = array()){
	// $startPeriod : d / M / Y
	// $periodInterval dalam bulan
	
	$periodInterval = 1;
	$periodStartDateFormat = '01 / m / Y';
		
	switch($reportType){
	
		case 1 : $periodInterval = 1;
				 $periodStartDateFormat = '01 / m / Y';
				 break;
		case 2 : $periodInterval = 12;
				 $periodStartDateFormat = '01 / 01 / Y';
				 break;
		case 3 : 
		case 4 : $periodInterval = 3;
				 $periodStartDateFormat = '01 / m / Y';
				 break; 
		case 5 : $periodInterval = 1;
				 $periodStartDateFormat = '01 / m / Y';
				 break; 
	}

 	

	// patokan dalam detik, tgl berakhir
	
	$uTimeEnd = str_replace('\'','',$this->oDbCon->paramDate($endPeriod,' / ','Y-m-d')); 
	$uTimeEnd = date_format( new DateTime($uTimeEnd),'U'); 

	$arrIncome = array();
	$ctr = 0;  

	// convert balik ke standart format waktu
	$currDate = str_replace('\'','',$this->oDbCon->paramDate($startPeriod,' / ','Y-m-d')); 
	$currDate = new DateTime($currDate);

	do{

		if($ctr > 0){ 
			$currDate->add(new DateInterval('P'.$periodInterval.'M'));  
			if(date_format($currDate,'U') > $uTimeEnd) break;
		}
 
		
		$formattedCurrDate = date_format($currDate,$periodStartDateFormat);
		$formattedEndDate = $this->getEndDate($currDate,$reportType); //date_format($currDate,$periodEndDateFormat);
		
//		$this->setLog($formattedCurrDate. ' ---- '. $formattedEndDate,true);
		//$rsIncome = generateIncome($arrCOAType, $formattedEndDate, $invert ); 
		$rsIncome = $this->generateIncome($arrCOAType, $formattedCurrDate, $formattedEndDate, $arrWarehouseKey ); 

 
		$arrIncome[$formattedCurrDate] = $rsIncome;

		$ctr++;

		// buat jaga2
		if($ctr > 23) break;

	}while (true);
	
	return $arrIncome;
}
    
}

?>