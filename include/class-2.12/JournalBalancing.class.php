<?php
  
class JournalBalancing extends BaseClass{ 
 
   function __construct(){
		
		parent::__construct();
       
		$this->tableName = 'journal_balancing';
        $this->tableCOA = 'chart_of_account';
		$this->tableStatus = 'transaction_status';
        $this->isTransaction = true;
        $this->newLoad = true;
       
		$this->securityObject = 'JournalBalancing';  
       
        $this->arrData = array();  
        $this->arrData['pkey'] = array('pkey');
        $this->arrData['code'] = array('code');
        $this->arrData['coakey'] = array('hidCOAKey');
        $this->arrData['coatokey'] = array('hidCOAToKey');
        $this->arrData['amount'] = array('amount', 'number');
        $this->arrData['trdate'] = array('trDate','date');
        $this->arrData['trdesc'] = array('trDesc'); 
        $this->arrData['statuskey'] = array('selStatus');
         
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'date','title' => 'date','dbfield' => 'trdate','default'=>true, 'width' => 100, 'align' =>'center', 'format' => 'date'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'coakey', 'title' => 'chartOfAccount', 'dbfield' => 'coa_name','default'=>true, 'width' => 225));
        array_push($this->arrDataListAvailableColumn, array('code' => 'coatokey', 'title' => 'temporaryAccount', 'dbfield' => 'counter_coa_name','default'=>true, 'width' => 225));
        array_push($this->arrDataListAvailableColumn, array('code' => 'amount', 'title' => 'amount', 'dbfield' => 'amount','default'=>true, 'width' => 100, 'format'=>'number', 'align' => 'right'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
 
         
        $this->includeClassDependencies(array( 
            'ChartOfAccount.class.php',
            'GeneralJournal.class.php', 
        ));
       
        $this->overwriteConfig();
       
   }
   
   function getQuery(){
	   
	   $sql = '
			SELECT '.$this->tableName.'.* ,  
			   coa_0.name as coa_name,
               coa_1.name as counter_coa_name,
			   '.$this->tableStatus.'.status as statusname
			FROM '.$this->tableStatus.', 
                 '.$this->tableName.' 
                  left join '.$this->tableCOA.' coa_0 on  '.$this->tableName.'.coakey = '.'coa_0.pkey
                  left join '.$this->tableCOA.' coa_1 on '.$this->tableName.'.coatokey = '.'coa_1.pkey
			WHERE '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey 
		   ' .$this->criteria ;
        
        return $sql;
    }
        
    
     function validateForm($arr,$pkey = ''){
		$arrayToJs = parent::validateForm($arr,$pkey); 
         
		$coakey = isset($arr['hidCOAKey']) ? $arr['hidCOAKey'] : 0;
        $coatokey = isset($arr['hidCOAToKey']) ? $arr['hidCOAToKey'] : 0;
        $amount = $arr['amount'];
         
        if (empty($coakey)){
            $this->addErrorList($arrayToJs, false, $this->errorMsg['coa'][1]);
        }
         
        if (empty($coatokey)){
            $this->addErrorList($arrayToJs, false, $this->errorMsg['temporaryAccount'][1]);
        }
  
		return $arrayToJs;
	 }

	function confirmTrans($rsHeader){        
		//update jurnal umum 
        $this->updateGL($rsHeader);
	} 
      
	function cancelTrans($rsHeader,$copy){
        $id = $rsHeader[0]['pkey'];
        
        if ($copy)
			$this->copyDataOnCancel($id);
            
        $this->cancelGLByRefkey($id, $this->tableName);
	} 
    
    function normalizeParameter($arrParam, $trim = false){
        $arrParam = parent::normalizeParameter($arrParam, true);  
        return $arrParam;
    }
    
    function updateGL($rsHeader){
        if (!USE_GL) return;
        
        $generalJournal = new GeneralJournal();
        
        $rsKey = $generalJournal->getTableKeyandObj($this->tableName);
        $arr = array();
        $arr['pkey'] = $generalJournal->getNextKey($generalJournal->tableName);
        $arr['code'] = 'xxxxx';
        $arr['refkey'] = $rsHeader[0]['pkey'];
        $arr['refTableType'] = $rsKey['key'];
        $arr['isbalancing'] = 1; 
        $arr['trDate'] = $this->formatDBDate($rsHeader[0]['trdate'], 'd / m / Y');
        $arr['createdBy'] = 0;
		//$arr['selWarehouseKey'] = $rs[0]['warehousekey'];
        
        $amount = $rsHeader[0]['amount'];
        
        // Nambahin akun balancing di jurnal
        $arrStartingBalance = $generalJournal->sumAccount($rsHeader[0]['coakey'], '', $arr['trDate']);
        $balanceDiff = $amount - $arrStartingBalance['balance'];
        
        $arr['hidCOAKey'][0]= $rsHeader[0]['coakey'];
        $arr['debit'][0] = $balanceDiff;
        $arr['credit'][0] = 0;
        
        $arr['hidCOAKey'][1]= $rsHeader[0]['coatokey'];
        $arr['debit'][1] = 0;
        $arr['credit'][1] = $balanceDiff;
        
        $arrayToJs = $generalJournal->addData($arr);
        
        if(!$arrayToJs[0]['valid'])
            throw new Exception('<strong>'.$rsHeader[0]['code'] . '</strong>. '.$this->errorMsg[504].' '.$arrayToJs[0]['message']); 
    }

}
?>
