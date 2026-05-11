<?php

class Tax extends BaseClass{
    
    function __construct(){
		
        // $taxType : 1. ppn, 2. pph
        
		parent::__construct();
       
		$this->tableName = 'tax';   
		$this->tableStatus = 'master_status';
        $this->tableCOA = 'chart_of_account'; 
        $this->tableType = 'tax_type'; 
        $this->newLoad = true;
        $this->securityObject = 'Tax';
        $this->PPh23Type = array('pph23', 'tax23', 'taxarticle23', 'taxart23');
        
        $this->arrData = array(); 
        $this->arrData['pkey'] = array('pkey');
        $this->arrData['code'] = array('code');
        $this->arrData['name'] = array('name'); 
        $this->arrData['statuskey'] = array('selStatus');  
        $this->arrData['typekey'] = array('selTaxType');  
        $this->arrData['taxincoakey'] = array('hidTaxInCOAKey');
        $this->arrData['taxoutcoakey'] = array('hidTaxOutCOAKey');
        $this->arrData['orderlist'] = array('orderList');
        $this->arrData['haswithholding'] = array('chkHasWithholding');
                  
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'name','title' => 'name','dbfield' => 'name','default'=>true, 'width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'typename','title' => 'type','dbfield' => 'typename','default'=>true, 'width' => 80));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
       

        $this->arrSearchColumn = array ();
        array_push($this->arrSearchColumn, array('Kode', $this->tableName . '.code'));
        array_push($this->arrSearchColumn, array('Nama', $this->tableName . '.name'));  
        array_push($this->arrSearchColumn, array('Tipe', $this->tableType . '.name'));  
        array_push($this->arrSearchColumn, array(ucwords($this->lang['coalink']), $this->tableCOA. '.name'));  
        array_push($this->arrSearchColumn, array(ucwords($this->lang['coalink']), $this->tableCOA. '.code'));  


        $this->overwriteConfig();
    }
    
    
    function getQuery(){ 
	   
	   $sql = '
				select
					'.$this->tableName. '.*,
                    '.$this->tableType.'.name as typename, 
					'.$this->tableStatus.'.status as statusname
				from 
					'.$this->tableName . ',
                    '.$this->tableStatus.',
                    '.$this->tableType.'
				where  		 
					'.$this->tableName . '.typekey = '.$this->tableType.'.pkey and
					'.$this->tableName . '.statuskey = '.$this->tableStatus.'.pkey 
                    
 		' .$this->criteria ; 
		  
       return $sql;
   }
    
    
  function validateForm($arr,$pkey = ''){
		       
		$arrayToJs = parent::validateForm($arr,$pkey);  
        $chartOfAccount = new ChartOfAccount();
        
		$name = $arr['name'];     
        $taxInCOAKey = $arr['hidTaxInCOAKey'];
        $taxOutCOAKey = $arr['hidTaxOutCOAKey'];
 
		$rsItem = $this->isValueExisted($pkey,'name',$name);	 
		if($name == ''){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['name'][1]);
		}else if(count($rsItem) <> 0){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['name'][2]);
		}
		  
        if(empty($taxInCOAKey) || empty($taxOutCOAKey) ){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['coa'][1]);
		} 
	
          
		return $arrayToJs;
	 }
    
    
    function getTaxType(){
        $sql = 'select * from '.$this->tableType.' where '.$this->tableType.'.statuskey = 1';
        return $this->oDbCon->doQuery($sql);
        
    }
    

    public function getPPhCOA($arrPPHTypeKey,$warehousekey,  $isAR = true)
    {
        $coaLink = new COALink();
        $warehouse = new Warehouse();
        //$warehousekey = $rs[0]['warehousekey'];

        if(!is_array($arrPPHTypeKey)) $arrPPHTypeKey = array($arrPPHTypeKey);
        
        $rsTax = $this->searchDataRow(
                array(
                    $this->tableName.'.pkey',
                    $this->tableName . '.name', 
                    $this->tableName.'.taxincoakey',
                    $this->tableName.'.taxoutcoakey'),
                ' and ' . $this->tableName . '.pkey in (' . $this->oDbCon->paramString($arrPPHTypeKey,',').') ');

        $result = [];

        if($isAR) {
            $tax23COA = ($this->loadSetting('tax23GLInInvoice') == 1 ) ? 'prepaidtax23Counter': 'prepaidtax23'; 
        } else {
            $tax23COA = 'payabletax23'; 
        }
        
        $rsCOA = $coaLink->getCOALink ($tax23COA, $warehouse->tableName,$warehousekey,0); 
         
        foreach($rsTax as $taxRow) {

            $COAKey = ($isAR) ? $taxRow['taxoutcoakey'] : $taxRow['taxincoakey'];

            // khusus pph 23, dan kalo kosong, ambil dari settingan gudang
            // agar bisa back compatible jg
            if($raxRow['namecode'] == WITHHOLDING_TAX['art23'] && empty($COAKey)) {
                $COAKey = !empty($rsCOA[0]['coakey']) ? $rsCOA[0]['coakey'] : 0; // ambil mapping coa pph 23 dari gudang kalau dari tax belum ada mapping coa
            } 

            array_push($result, array(
                'pkey' => $taxRow['pkey'],
                'name' => $taxRow['name'],
                'coakey' => $COAKey
            ));
        }
 
        // failsafe utk model lama
        // kalo gk ad arr Tax, tembak pph 23 dari gudang
        if(empty($result)){ 
            array_push($result, array(
                'pkey' => 0,
                'name' => 'PPH 23',
                'coakey' => $rsCOA[0]['coakey']
            ));
        }
        
        return $result;
    
    }
  
    function normalizeParameter($arrParam, $trim=false){
        
        $arrParam = parent::normalizeParameter($arrParam,true);
        
        return $arrParam;
    }
    
} 
  
?>
