<?php

class CashAdvance extends BaseClass{
	
    function __construct(){
 
    parent::__construct();

    $this->tableName = 'cash_advance';
    $this->tableWarehouse = 'warehouse';   
    $this->tableEmployee = 'employee';   
    $this->tableItem = 'item';   
    $this->tableCOA = 'chart_of_account';  
    $this->tableCashAdvanceRealizationHeader = 'cash_advance_realization_header';  
    $this->tableCashAdvanceRealizationDetail = 'cash_advance_realization_detail';  
	$this->tableCashAdvanceRealizationAdvanceDetail	= 'cash_advance_realization_advance';
	$this->tableJobOrder = 'emkl_job_order_header';
	$this->tablePurchaseOrder = 'emkl_purchase_order_header';
    $this->tableContainer = 'container';
    $this->tableStatus = 'transaction_status';
    $this->tableSupplier  = 'supplier';
        
    $this->isTransaction = true;    
        
    $this->securityObject = 'CashAdvance';

    $this->arrData = array(); 
    $this->arrData['pkey'] = array('pkey');  
    $this->arrData['code'] = array('code');
    $this->arrData['warehousekey'] = array('selWarehouseKey');
    $this->arrData['employeekey'] = array('hidEmployeeKey');
    $this->arrData['trdate'] = array('trDate','date');
    $this->arrData['trdesc'] = array('note');
    $this->arrData['statuskey'] = array('selStatus');
    $this->arrData['amount'] = array('amount','number');
    $this->arrData['coakey'] = array('hidCOAKey');
    $this->arrData['cashadvancecoakey'] = array('cashadvancecoakey');
 

    $this->arrDataListAvailableColumn = array(); 
    array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 100));
    array_push($this->arrDataListAvailableColumn, array('code' => 'date','title' => 'date','dbfield' => 'trdate','default'=>true, 'width' => 100, 'align' =>'center','format' => 'date'));
    array_push($this->arrDataListAvailableColumn, array('code' => 'realizationdate','title' => 'realizationDate','dbfield' => 'realizationdate','default'=>true, 'width' => 100, 'align' =>'center','format' => 'date'));
    array_push($this->arrDataListAvailableColumn, array('code' => 'warehouse','title' => 'warehouse','dbfield' => 'warehousename','default'=>true, 'width' => 120));    
    array_push($this->arrDataListAvailableColumn, array('code' => 'recipient','title' => 'recipient','dbfield' => 'employeename','default'=>true, 'width' => 120));   
    array_push($this->arrDataListAvailableColumn, array('code' => 'cashbank','title' => 'cashBankAccount','dbfield' => 'coacodename','default'=>true, 'width' => 130));
    array_push($this->arrDataListAvailableColumn, array('code' => 'recipientcashbank','title' => 'recipientAccount','dbfield' => 'coaadvancecodename', 'width' => 130)); 
	array_push($this->arrDataListAvailableColumn, array('code' => 'amount','title' => 'amount','dbfield' => 'amount','default'=>true,'format' => 'number', 'align' => 'right', 'width' => 100));    
    array_push($this->arrDataListAvailableColumn, array('code' => 'desc','title' => 'note','dbfield' => 'trdesc','default'=>true, 'width' => 150));
	array_push($this->arrDataListAvailableColumn, array('code' => 'realization','title' => 'realizationCode','dbfield' => 'realizationcode', 'width' => 130));
	// nilai realisasi sudah tidak valid karena bisa beberapa kasbon dalam satu penyelesaian
	//array_push($this->arrDataListAvailableColumn, array('code' => 'realizationamount','title' => 'realizationAmount','dbfield' => 'realizationamount','default'=>false,'format' => 'number', 'align' => 'right', 'width' => 130));   
	array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 90));
   	
    array_push($this->filterCriteria, array('title' => $this->lang['warehouse'], 'field' => 'warehousekey'));
        
	$this->printMenu = array();
    array_push($this->printMenu,array('code' => 'printTransaction', 'name' => $this->lang['printTransaction'],  'icon' => 'print', 'url' => 'print/cashAdvance'));
         
    $this->includeClassDependencies(array(
           'Warehouse.class.php',
           'CashAdvanceRealization.class.php' , 
           'COALink.class.php',
           'CashBank.class.php',
           'GeneralJournal.class.php'
    ));  

    $this->overwriteConfig();

    }

    function getQuery(){
         
        $sql = '
            SELECT
                '.$this->tableName.'.* ,  
                '.$this->tableWarehouse.'.name as warehousename,
                '.$this->tableEmployee.'.name as employeename,
				'.$this->tableCOA .'.name as coaname,
                '.$this->tableCOA .'.code as coacode, 
                concat('.$this->tableCOA .'.code," - " ,'.$this->tableCOA .'.name ) as coacodename,
                concat(coaadvance.code," - " ,coaadvance.name ) as coaadvancecodename,  
                '.$this->tableStatus.'.status as statusname
                
            FROM '.$this->tableStatus.',
                 '.$this->tableWarehouse.',
                 '.$this->tableEmployee.',
                 '.$this->tableCOA.',
                 '.$this->tableName.' 
                    left join '.$this->tableCOA.' coaadvance on '.$this->tableName.'.cashadvancecoakey = coaadvance.pkey
      	        WHERE   
                  '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey and
                  '.$this->tableName.'.employeekey = '.$this->tableEmployee.'.pkey and
                  '.$this->tableName.'.coakey = '.$this->tableCOA.'.pkey and
                  '.$this->tableName.'.warehousekey = '.$this->tableWarehouse.'.pkey 

            ' .$this->criteria ;       
        
        $sql .=  $this->getWarehouseCriteria() ;
                                
        return $sql;
    }
    
    function getOutstandingSummary($warehousekey = ''){
        $sql = 'select 
                   sum(amount) as total,
                   '.$this->tableEmployee.'.name as employeename 
               from 
                   '.$this->tableName.'
                   left join ' .$this->tableEmployee.' on   ' .$this->tableName.'.employeekey = '.$this->tableEmployee.'.pkey 
               where 
                    '.$this->tableName.'.statuskey = 2 ';

		
		 if (!empty($warehousekey))
				$sql .= ' and  '.$this->tableName.'.warehousekey in ('. $this->oDbCon->paramString($warehousekey,',').' )';
		
         $sql .=  $this->getWarehouseCriteria() ;
         $sql .= ' group by '.$this->tableName.'.employeekey ';

        return $this->oDbCon->doQuery($sql); 
    } 

    function afterStatusChanged($rsHeader){   
        //$rsHeader = $this->getDataRowById($rsHeader[0]['pkey']); 
    }

    function afterUpdateData($arrParam, $action){ 
    }
    
    function validateForm($arr,$pkey = ''){  
        $arrayToJs = parent::validateForm($arr,$pkey);  
 		$arrEmployeeKey = $arr['hidEmployeeKey'];
 		$amount = $this->unFormatNumber($arr['amount']);
        $coaKey = $arr['hidCOAKey'];
 
		if(empty($arrEmployeeKey)) 
			$this->addErrorList($arrayToJs,false,$this->errorMsg['employee'][1]); 
	 
        if ( $amount <= 0) 
			$this->addErrorList($arrayToJs,false,$this->errorMsg['amount'][1]);
		  
        if(empty($coaKey)) 
			$this->addErrorList($arrayToJs,false,$this->errorMsg['coa'][1]); 
		 
        return $arrayToJs;
    }
    
    function validateBackConfirm($rsHeader){  
		$this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'.</strong> '.$this->errorMsg[900],true);   
    }		
    
	
	function validateClose($rsHeader){ 
		$this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'.</strong> '.$this->errorMsg[900],true);   
	 }		
    
     function getCashAdvanceRealizationInformation($pkey){
        $cashAdvanceRealization = new CashAdvanceRealization();
      
        $sql = 'select
            '.$cashAdvanceRealization->tableName.'.code,    
            '.$cashAdvanceRealization->tableName.'.trdate,
            '.$cashAdvanceRealization->tableName.'.pkey,
            '.$cashAdvanceRealization->tableNameDetailAdvance.'.amount,
            '.$cashAdvanceRealization->tableNameDetailAdvance.'.cashadvancekey
          from 
            '.$cashAdvanceRealization->tableName.',
            '.$cashAdvanceRealization->tableNameDetailAdvance.'
          where  
            '. $cashAdvanceRealization->tableNameDetailAdvance.'.cashadvancekey in ('.$this->oDbCon->paramString($pkey,',') .')  and   
            '. $cashAdvanceRealization->tableName.'.pkey = '. $cashAdvanceRealization->tableNameDetailAdvance.'.refkey and
            '. $cashAdvanceRealization->tableName.'.statuskey in (2,3) ';
 
        return $this->oDbCon->doQuery($sql);

    }

    function validateCancel($rsHeader, $autoChangeStatus = false){ 

        $id = $rsHeader[0]['pkey'];
        
//        $cashAdvanceRealization = new CashAdvanceRealization();
        
//        $rsCashAdvanceRealization = $cashAdvanceRealization->searchDataRow( 
//                                                        array($cashAdvanceRealization->tableName.'.pkey',$cashAdvanceRealization->tableName.'.code'),
//                                                        ' and '.$cashAdvanceRealization->tableName.'.refkey = '.$this->oDbCon->paramString($id).' 
//                                                          and '. $cashAdvanceRealization->tableName.'.statuskey in (2,3)' 
//        ); 
        
        $rsCashAdvanceRealization = $this->getCashAdvanceRealizationInformation($id);        
        if (!empty($rsCashAdvanceRealization)) 
           $this->addErrorLog( false, '<strong>'.$rsHeader[0]['code'].'</strong> ' .$this->errorMsg[201].'<br><strong>'.$rsCashAdvanceRealization[0]['code'].'</strong>, ' .$this->errorMsg[225] );
  
    } 
	 

	function confirmTrans($rsHeader){
		$id = $rsHeader[0]['pkey'];
         
        // update ulang cashadvancecoakeynya 
        $warehouse = new Warehouse();
        $coaLink = new COALink();
        $employee = new Employee();
        $rsEmployee = $employee->getDataRowById($rsHeader[0]['employeekey']);
     
        $coakey = $employee->getCashAdvCOAKey($rsHeader[0]['employeekey'],$rsHeader[0]['warehousekey']);
         
        $rsHeader[0]['cashadvancecoakey'] = $coakey; 
        $sql = 'update '.$this->tableName.' set cashadvancecoakey = ' . $this->oDbCon->paramString($rsHeader[0]['cashadvancecoakey']) .' where pkey = ' . $this->oDbCon->paramString($id) ;
        $this->oDbCon->execute($sql);
        
        $cashBank = new CashBank(); 
        $rsCashBank = $cashBank->addCashBank($rsHeader,$this->tableName, array('employeekey' => $rsEmployee[0]['pkey'], 'desc' => $this->lang['cashAdvance'].', ' . $rsEmployee[0]['name'], 'amount' => -$rsHeader[0]['amount'])); 
        $arrCashBank['cashFromKey'] = $rsCashBank['pkey']; 
            
        $this->updateGL($rsHeader,$arrCashBank); 
	} 
 
    function normalizeParameter($arrParam, $trim=false){
        
        $employee = new Employee();
        
        // perlu diisi diawal, karena utk print
        $arrParam['cashadvancecoakey'] = $employee->getCashAdvCOAKey($arrParam['hidEmployeeKey'],$arrParam['selWarehouseKey']);
            
        $arrParam = parent::normalizeParameter($arrParam,true); 
        
        return $arrParam; 
    }
    
    function cancelTrans($rsHeader,$copy){ 
		$id = $rsHeader[0]['pkey']; 
        
        $cashAdvanceRealization = new CashAdvanceRealization();
		
        $rsCashAdvanceRealization = $cashAdvanceRealization->searchDataRow( 
                                                        array($cashAdvanceRealization->tableName.'.pkey',$cashAdvanceRealization->tableName.'.code'),
                                                        ' and '.$cashAdvanceRealization->tableName.'.refkey = '.$this->oDbCon->paramString($id).' 
                                                          and '. $cashAdvanceRealization->tableName.'.statuskey = 1'
        
        ); 
        for($i=0;$i<count($rsCashAdvanceRealization);$i++) 
          $cashAdvanceRealization->changeStatus($rsCashAdvanceRealization[$i]['pkey'],4,'',false,true); 
        
        
        $cashBank = new CashBank();
        $cashBank->cancelCashBank($rsHeader,$this->tableName);
        
		if ($copy)
			$this->copyDataOnCancel($id);	
        
        $this->cancelGLByRefkey($rsHeader[0]['pkey'],$this->tableName);

	}
    
    function updateGL($rs,$arrCashBank){
        
        if (!USE_GL) return;
        
        $warehouse = new Warehouse();
        $coaLink = new COALink();
        $generalJournal = new GeneralJournal();
        $employee = new Employee();
        
        $warehousekey = $rs[0]['warehousekey']; 

        $rsKey = $generalJournal->getTableKeyAndObj($this->tableName);
        $arr = array();
        $arr['pkey'] = $generalJournal->getNextKey($generalJournal->tableName);
        $arr['code'] = 'xxxxx';
        $arr['refkey'] = $rs[0]['pkey'];
        $arr['refTableType'] = $rsKey['key'];
        $arr['trDate'] = $this->formatDBDate($rs[0]['trdate'],'d / m / Y');  
        $arr['createdBy'] = 0;        
        $arr['selWarehouseKey'] = $rs[0]['warehousekey'];
 
        $rsEmployee = $employee->getDataRowById($rs[0]['employeekey']);
        
        $desc = array();
        array_push($desc, $rsEmployee[0]['name']);
        if (!empty($rs[0]['trdesc'])) array_push($desc, $rs[0]['trdesc']);
        $arr['trDesc'] = implode(chr(13),$desc);

        $temp = -1;
        $temp++;
        $arr['hidCOAKey'][$temp] = $rs[0]['cashadvancecoakey'];
        $arr['debit'][$temp] = $rs[0]['amount']; 
        $arr['credit'][$temp] = 0;  
        $arr['refCashBankKey'][$temp]  = '';
 
        $temp++;
        $arr['hidCOAKey'][$temp] = $rs[0]['coakey'];
        $arr['debit'][$temp] = 0;  
        $arr['credit'][$temp] = $rs[0]['amount'];
        $arr['refCashBankKey'][$temp]  = $arrCashBank['cashFromKey']; 
        
        $arrayToJs = $generalJournal->addData($arr);
        
		if (!$arrayToJs[0]['valid'])
                throw new Exception('<strong>'.$rs[0]['code'] . '</strong>. '.$this->errorMsg[504].' '.$arrayToJs[0]['message']);    

    }
	
	function updateCashAdvance($cashkey){
		$cashAdvanceRealization = new CashAdvanceRealization();
        
		$rsCash = $this->getDataRowById($cashkey);
		$statuskey = $rsCash[0]['statuskey'];
		 
        $sql = 'select  
                      '. $cashAdvanceRealization->tableName.'.pkey,'. $cashAdvanceRealization->tableName.'.code,'. $cashAdvanceRealization->tableName.'.trdate
				 from 
				 	  '. $cashAdvanceRealization->tableName.',
				 	  '. $cashAdvanceRealization->tableNameDetailAdvance.'
				 where  
				 	  ( '.$cashAdvanceRealization->tableName .'.statuskey in ('.TRANSACTION_STATUS['selesai'].') ) and
					  '.$cashAdvanceRealization->tableNameDetailAdvance.'.refkey = '.$cashAdvanceRealization->tableName.'.pkey and
					  '.$cashAdvanceRealization->tableNameDetailAdvance.'.cashadvancekey = '.$this->oDbCon->paramString($rsCash[0]['pkey']).'
				';
		$rsRealization =  $this->oDbCon->doQuery($sql);
        
        // update sementara kalo cuma pasti 1 realisasi
        
		//if($rsRealization[0]['totalrealization']>=$rsCash[0]['amount']){
        if(!empty($rsRealization)){
			$dateNow = date('d / m / Y');
			$realizationCode = $rsRealization[0]['code'];
			$realizationDate = $rsRealization[0]['trdate'];
			$statuskey = TRANSACTION_STATUS['selesai'];
		}else{
			$dateNow = DEFAULT_EMPTY_DATE;
			$realizationCode = '';
			$realizationDate = DEFAULT_EMPTY_DATE;
			$statuskey = TRANSACTION_STATUS['konfirmasi'];
		}
		
		$sql  = 'update '.$this->tableName.' set 
					realizationcode =  '.$this->oDbCon->paramString($realizationCode).',
					realizationdate =  '.$this->oDbCon->paramString($realizationDate).',
					realizationclosingdate =  '.$this->oDbCon->paramDate($dateNow,' / ').'  
				where pkey = ' .$this->oDbCon->paramString($rsCash[0]['pkey']);	
		
		$this->oDbCon->execute($sql);  
		
		if($rsCash[0]['statuskey'] <> $statuskey)
            $this->changeStatus($rsCash[0]['pkey'],$statuskey, '', false, true,true);
	
	} 
    
    function getRealizationSumAmount($pkey){
        $sql = 'select
                    '.$this->tableCashAdvanceRealizationHeader.'.refkey as cashadvancekey, 
                    coalesce('.$this->tableItem.'.name,\''.$this->lang['trucking'].'\') as itemname,
                    sum('.$this->tableCashAdvanceRealizationDetail.'.beforetaxtotal+'.$this->tableCashAdvanceRealizationDetail.'.taxvalue) as amount
                from 
                    '.$this->tableCashAdvanceRealizationDetail.'
                        left join '.$this->tableItem.' on '.$this->tableCashAdvanceRealizationDetail.'.servicekey = '.$this->tableItem.'.pkey,
                    '.$this->tableCashAdvanceRealizationHeader.' 
                where
                    '.$this->tableCashAdvanceRealizationDetail.'.refkey = '.$this->tableCashAdvanceRealizationHeader.'.pkey and
                    '.$this->tableCashAdvanceRealizationHeader.'.refkey in ('.$this->oDbCon->paramString($pkey,',').') and 
                    '.$this->tableCashAdvanceRealizationHeader.'.statuskey in (2,3)
                group by 
                    '.$this->tableCashAdvanceRealizationHeader.'.refkey,
                    '.$this->tableCashAdvanceRealizationDetail.'.servicekey
                order by  itemname asc'
            ; // agar Others yg itemkey nya 0 terakhir posisinya
         
        return $this->oDbCon->doQuery($sql); 
    }
	
		function generateDefaultQueryForAutoComplete($returnField){ 
        
        $sql = 'select
					'.$this->tableName . '.pkey,
                    '.$this->tableName . '.code as value, 
                    '.$this->tableName . '.amount,
					'.$this->tableEmployee.'.name as employeename
                    
				from 
					'.$this->tableName . ', 
                    '.$this->tableStatus.',
					'.$this->tableEmployee.'
				where  		 
					'.$this->tableName . '.statuskey = '.$this->tableStatus.'.pkey and
					'.$this->tableName . '.employeekey = '.$this->tableEmployee.'.pkey
			';
        
          
         return $sql;
     }
    
     
    function getJobInformation($arrPkey){
        // untuk laporan buku besar
         
        $sql = 'select distinct
                 '.$this->tableJobOrder.'.pkey as jokey,
                 '.$this->tableJobOrder.'.code as jocode,
                 '.$this->tableCashAdvanceRealizationAdvanceDetail.'.cashadvancekey as reftablekey
                from  
                 '.$this->tableJobOrder.',
                 '.$this->tableCashAdvanceRealizationHeader.',
                 '.$this->tableCashAdvanceRealizationDetail.',
                 '.$this->tableCashAdvanceRealizationAdvanceDetail.'  
                where   
                '.$this->tableCashAdvanceRealizationHeader.'.statuskey in (2,3) and
                '.$this->tableCashAdvanceRealizationHeader.'.pkey = '.$this->tableCashAdvanceRealizationDetail.'.refkey and
                '.$this->tableCashAdvanceRealizationHeader.'.pkey = '.$this->tableCashAdvanceRealizationAdvanceDetail.'.refkey and
                '.$this->tableCashAdvanceRealizationDetail.'.joborderkey = '.$this->tableJobOrder.'.pkey and    
                '.$this->tableCashAdvanceRealizationAdvanceDetail.'.cashadvancekey in ('.$this->oDbCon->paramString($arrPkey,',').') 
                    
              ';
        
        //$this->setLog($sql,true);
         
        $rs = $this->oDbCon->doQuery($sql);
        
        return $rs;
    }
    
    
    function getCashAdvanceRealizationDetail($pkey, $criteria = ''){
        
        $sql = '
            select
                '.$this->tableCashAdvanceRealizationDetail.'.*,
                '.$this->tableCashAdvanceRealizationAdvanceDetail.'.cashadvancekey,
                '.$this->tableName.'.code as cashadvancecode,
                '.$this->tableCashAdvanceRealizationHeader.'.pkey as realizationkey,
                '.$this->tableCashAdvanceRealizationHeader.'.code as realizationcode,
                '.$this->tableCashAdvanceRealizationHeader.'.trdate as realizationdate,
                concat(' . $this->tableCOA .'.code, " - " , ' . $this->tableCOA .'.name ) as coaname,
                '.$this->tableItem.'.code as servceicode,
                '.$this->tableItem.'.name as servicename,
                '.$this->tableSupplier.'.name as suppliername,
                '.$this->tableContainer.'.name as containername,
                '.$this->tableSupplier.'.name as suppliername,
                '.$this->tableJobOrder.'.code as jobordercode
            from
                '.$this->tableCashAdvanceRealizationDetail.'
                    left join '.$this->tableItem.' on '.$this->tableCashAdvanceRealizationDetail.'.servicekey = '.$this->tableItem.'.pkey
                    left join '.$this->tableSupplier.' on '.$this->tableCashAdvanceRealizationDetail.'.supplierkey = '.$this->tableSupplier.'.pkey
                    left join '.$this->tableContainer.' on '. $this->tableCashAdvanceRealizationDetail.'.itemkey = '.$this->tableContainer.'.pkey 
                    left join '.$this->tableJobOrder.' on '. $this->tableCashAdvanceRealizationDetail.'.joborderkey = '.$this->tableJobOrder.'.pkey  
                    left join '.$this->tableCOA.' on '. $this->tableCashAdvanceRealizationDetail.'.coakey = '.$this->tableCOA.'.pkey ,
                '.$this->tableCashAdvanceRealizationAdvanceDetail.'
                    left join '.$this->tableName.' on '.$this->tableCashAdvanceRealizationAdvanceDetail.'.cashadvancekey = '.$this->tableName.'.pkey,
                '.$this->tableCashAdvanceRealizationHeader.'
            where
                '.$this->tableCashAdvanceRealizationDetail.'.refkey = '.$this->tableCashAdvanceRealizationHeader.'.pkey and
                '.$this->tableCashAdvanceRealizationHeader.'.statuskey in (2,3) and
                '.$this->tableCashAdvanceRealizationHeader.'.pkey = '.$this->tableCashAdvanceRealizationAdvanceDetail.'.refkey and
                '.$this->tableName.'.pkey in ('.$this->oDbCon->paramString($pkey,',').') 
        ';

        $rs = $this->oDbCon->doQuery($sql);
        
        if (empty($rs)) return $rs;
        
        // tambah informasi PO, gk bisa langsung join karena detailkey cash realization pake koma di PO, gk normalize
        $sql = 'select 
                    pkey,code, refcashadvancekey,  refcashadvancedetailkey 
                from
                    '.$this->tablePurchaseOrder.'
                where
                    '.$this->tablePurchaseOrder.'.refcashadvancekey in ('.$this->oDbCon->paramString(array_column($rs, 'realizationkey'),',').')
                ';
        
        $rsPO = $this->oDbCon->doQuery($sql);
        
        foreach($rsPO as $key=>$poRow) 
            $rsPO[$key]['cashadvancedetailkey'] = explode(',',$poRow['refcashadvancedetailkey']);
      
        foreach($rs as $key=>$row){
        
            $poCode = '';
            foreach($rsPO as $poRow){
                // $row['pkey'] ==> pkey detail realisai
                if (in_array(  $row['pkey'] ,$poRow['cashadvancedetailkey'] )){
                    $poCode = $poRow['code'];
                    break;
                } 
            } 
                
            $rs[$key]['pocode'] = $poCode;
        }
        
        
        return $rs;

    }
     
    
}

?>
