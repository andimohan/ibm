<?php
  
class TemplateEMKLPurchaseItem extends BaseClass{ 
 
    function __construct(){
		
		parent::__construct();
       
		$this->tableName = 'template_emkl_purchase_item_header';
		$this->tableNameDetail = 'template_emkl_purchase_item_detail'; 
		$this->tableStatus = 'master_status'; 
        $this->tableItem = 'item';
		$this->securityObject = 'TemplateEMKLPurchaseItem'; 
        $this->isTransaction = true; 
        
        $this->arrDataDetail = array();   
        $this->arrDataDetail['pkey'] = array('hidDetailKey');
        $this->arrDataDetail['refkey'] = array('pkey','ref');
        $this->arrDataDetail['itemkey'] = array('hidCostKey');
         
        $this->arrData = array(); 
        $this->arrData['pkey'] = array('pkey', array('dataDetail' => array('dataset' => $this->arrDataDetail)));
        $this->arrData['code'] = array('code');
        $this->arrData['name'] = array('name');
        $this->arrData['trdesc'] = array('trDesc');
        $this->arrData['statuskey'] = array('selStatus');

		$this->includeClassDependencies(array(
              'Item.class.php' 
        ));
   }
   
  function getQuery(){
	   
	   $sql=  '
			SELECT '.$this->tableName.'.*,
			  '.$this->tableStatus.'.status as statusname 
			FROM 
                 '.$this->tableName.',
                 '.$this->tableStatus.'
			WHERE 
                '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey
        
        ' .$this->criteria ;
	  
		return $sql; 
    } 
    
	
    function validateForm($arr,$pkey = ''){    
          
		$arrayToJs = parent::validateForm($arr,$pkey); 
        $arrCostKey = $arr['hidCostKey'];
		
		$templateName = $arr['name']; 
			 
		$rs = $this->isValueExisted($pkey,'name',$templateName); 
		if(empty($templateName)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['name'][1]);
		}else if (count($rs) <> 0){  
			$this->addErrorList($arrayToJs,false,$this->errorMsg['name'][2]);
		}
		
		for($i=0;$i<count($arrCostKey);$i++) {
			if (empty($arrCostKey[$i]) ) 
				$this->addErrorList($arrayToJs,false, $this->errorMsg['cost'][1]); 
		}
        
		return $arrayToJs;
	 }
    
 function getDetailWithRelatedInformation($pkey,$criteria=''){ 
       
	   $sql = 'select
	   			'.$this->tableNameDetail .'.*,
			  	'.$this->tableItem.'. name as costname
                
              from
			  	'.$this->tableNameDetail .',
			  	'.$this->tableItem .'   
			  where 
			  	'.$this->tableNameDetail .'.itemkey = '.$this->tableItem .'.pkey and
			  	'.$this->tableNameDetail .'.refkey = '.$this->oDbCon->paramString($pkey) . ' ';
       
        $sql .= $criteria;   
		return $this->oDbCon->doQuery($sql);
	
   }
	

	
    function normalizeParameter($arrParam, $trim=false){
         
        $arrParam = parent::normalizeParameter($arrParam, true);
         
        return $arrParam;
    }
    
     
 
		
}
?>
