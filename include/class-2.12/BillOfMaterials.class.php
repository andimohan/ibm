<?php
  
class BillOfMaterials extends BaseClass{ 

 
   function __construct(){
		
		parent::__construct();
       
		$this->tableName = 'bill_of_materials_header';
		$this->tableNameDetail = 'bill_of_materials_detail';
		$this->tableItem = 'item';
		$this->tableItemUnit = 'item_unit';
		$this->tableStatus = 'master_status';
	   
		$this->securityObject = 'BillOfMaterials';  
        	   	
	    $this->newLoad = true;
	   
	    $this->arrDataDetail = array();   
        $this->arrDataDetail['pkey'] = array('hidDetailKey');
	   	$this->arrDataDetail['refkey'] = array('pkey','ref');
        $this->arrDataDetail['itemkey'] = array('hidItemKeyDetail');
        $this->arrDataDetail['qty'] = array('qty','number'); 

        $arrDetails = array();
        array_push($arrDetails, array('dataset' => $this->arrDataDetail, 'tableName' => $this->tableNameDetail)); 

        $this->arrData = array();
        $this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails));
        $this->arrData['code'] = array('code');
        $this->arrData['name'] = array('name');
        $this->arrData['itemkey'] = array('hidItemKey');
        $this->arrData['trdesc'] = array('trDesc');
        $this->arrData['statuskey'] = array('selStatus');
		 
       
        $this->tableNeedToBeCopyOnCancel = array($this->tableNameDetail);
	   
	    $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'name','title' => 'name','dbfield' => 'name','default'=>true,'width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'item','title' => 'itemName','dbfield' => 'itemname','default'=>true,'width' => 250));
        array_push($this->arrDataListAvailableColumn, array('code' => 'desc','title' => 'description','dbfield' => 'trdesc','default'=>true,'width' => 270));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 90));
 			
	   	$this->arrSearchColumn = array();
	    array_push($this->arrSearchColumn, array('Kode', $this->tableName . '.code'));
		array_push($this->arrSearchColumn, array('Nama', $this->tableName . '.name')); 
		array_push($this->arrSearchColumn, array('Item', $this->tableItem . '.name'));
	   
	   $this->includeClassDependencies(array(
            'Warehouse.class.php',
            'Item.class.php',
                  
        ));  
	   
        $this->overwriteConfig();
	   
   }
   
   function getQuery(){
	   
	   return '
       
			SELECT 
                '.$this->tableName.'.* ,  
                '.$this->tableItem.'.name as itemname ,
                '.$this->tableStatus.'.status as statusname 
			FROM 
                '.$this->tableStatus.',
                '.$this->tableName.', 
                '.$this->tableItem.'
			WHERE 
                '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey  and
                '.$this->tableName.'.itemkey = '.$this->tableItem.'.pkey

 		' .$this->criteria ; 
		 
    }  

                 
     function validateForm($arr,$pkey = ''){
		  
		$item = new Item();   
		  
		$arrayToJs = parent::validateForm($arr,$pkey);
         
		$name = $arr['name'];
		$arrItemkey = $arr['hidItemKey']; 
		$arrItemkeyDetail = $arr['hidItemKeyDetail']; 
		$arrQty = $arr['qty'];  
  
		if(empty($name)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['name'][1]);
		} 	  
         
		if(empty($arrItemkey)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['item'][1]);
		}
 
         
        $arrDetailKeys = array(); 
		for($i=0;$i<count($arrItemkeyDetail);$i++) {
			if (empty($arrItemkeyDetail[$i]) ){ 
				$this->addErrorList($arrayToJs,false, $this->errorMsg['item'][1]); 	
			} else{
                if ($this->unFormatNumber($arrQty[$i]) <= 0){
                    $rsItem = $item->getDataRowById($arrItemkeyDetail[$i]);
                    $this->addErrorList($arrayToJs,false,$rsItem[0]['name']. '. ' . $this->errorMsg[503]); 
                }
                
                // cek ada detail double gk 
                if (in_array($arrItemkeyDetail[$i],$arrDetailKeys)){  
                    $rsItem = $item->getDataRowById($arrItemkeyDetail[$i]);
                    $this->addErrorList($arrayToJs,false, $rsItem[0]['name'].'. '.$this->errorMsg[215]); 	 
                }else{ 
                    array_push($arrDetailKeys, $arrItemkeyDetail[$i]);
                }  
            } 
		}
  
		
		return $arrayToJs;
	 }
 

	function getDetailWithRelatedInformation($pkey,$criteria=''){
	   $sql = 'select
	   			'. $this->tableNameDetail .'.*, 
                '.$this->tableItem.'.name as itemname, 
                '.$this->tableItem.'.code as itemcode,
                '.$this->tableItemUnit.'.name as unitname,
                baseunit.name as baseunitname
			  from
			  	' . $this->tableNameDetail .',
                '.$this->tableItem.',
                '.$this->tableItemUnit.',
                '.$this->tableItemUnit.' baseunit
			  where
			  	' . $this->tableNameDetail .'.itemkey = '.$this->tableItem.'.pkey and
			  	' . $this->tableItem .'.baseunitkey = '.$this->tableItemUnit.'.pkey and
			  	' . $this->tableItem .'.baseunitkey = baseunit.pkey and
			  	refkey in ('.$this->oDbCon->paramString($pkey,',') . ')  ';
       
        $sql .= $criteria;
        
		return $this->oDbCon->doQuery($sql);
	
   }
     
}
?>