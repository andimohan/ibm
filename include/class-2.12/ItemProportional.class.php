<?php
class ItemProportional extends BaseClass
{

	function __construct()
	{

		parent::__construct();

		$this->tableName = 'item_proportional';
		$this->tableNameDetail = 'item_proportional_detail';
		$this->tableItem = 'item';
		$this->tableStatus = 'master_status';

		$this->securityObject = 'ItemProportional';

		// Mapping Form Detail
		$this->arrDetailProportional = array();
		$this->arrDetailProportional['pkey'] = array('hidDetailKey');
		$this->arrDetailProportional['refkey'] = array('pkey', 'ref');
		$this->arrDetailProportional['itemkey'] = array('hidItemDetailKey', array('mandatory' => true));
		$this->arrDetailProportional['percentage'] = array('detailPercentage', 'decimal');
        
        $this->percentage = 100;

		$this->importUrl = 'import/itemProportional';
		
		// Insert Form Detail
		$arrDetails = array();
		array_push($arrDetails, array('dataset' => $this->arrDetailProportional, 'tableName' => $this->tableNameDetail));

		$this->arrData = array();
		$this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails));
		$this->arrData['code'] = array('code');
		$this->arrData['name'] = array('name');
		$this->arrData['coakey'] = array('hidCOAKey');
		$this->arrData['itemkey'] = array('hidItemKey');
		$this->arrData['remainpercentage'] = array('remainpercentage', 'decimal');
		$this->arrData['trdesc'] = array('trDesc');
		$this->arrData['statuskey'] = array('selStatus');
		

		$this->arrDataListAvailableColumn = array();
		array_push($this->arrDataListAvailableColumn, array('code' => 'code', 'title' => 'code', 'dbfield' => 'code', 'default' => true, 'width' => 70));
		array_push($this->arrDataListAvailableColumn, array('code' => 'name', 'title' => 'name', 'dbfield' => 'name', 'default' => true, 'width' => 150));
		array_push($this->arrDataListAvailableColumn, array('code' => 'item', 'title' => 'item', 'dbfield' => 'itemname', 'default' => true, 'width' => 150));
		array_push($this->arrDataListAvailableColumn, array('code' => 'remainpercentage', 'title' => 'remainPercentage', 'dbfield' => 'remainpercentage','format'=> 'number','default' => true,'align' => 'right', 'width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status', 'title' => 'status', 'dbfield' => 'statusname', 'default' => true, 'width' => 70));

		// Function for Search
		$this->arrSearchColumn = array();
		array_push($this->arrSearchColumn, array('Kode', $this->tableName . '.code'));
		array_push($this->arrSearchColumn, array('Name', $this->tableName . '.name'));
		array_push($this->arrSearchColumn, array('Barang', $this->tableItem . '.name'));
        
        
		$this->newLoad = true;

		$this->includeClassDependencies(
			array(
				'Item.class.php',
				'ChartOfAccount.class.php',
			)
		);

		$this->overwriteConfig();
	}

    
    


	// Untuk fetching data
	function getQuery()
	{

			return '
				select
					' . $this->tableName . '.*,
					' . $this->tableItem . '.name as itemname,
					' . $this->tableStatus . '.status as statusname
				from 
					' . $this->tableName . '
                           left join ' . $this->tableItem . '  ON ' . $this->tableName . '.itemkey = ' . $this->tableItem . '.pkey,
					' . $this->tableStatus . '
				where  		
					' . $this->tableName . '.statuskey = ' . $this->tableStatus . '.pkey  
 		' . $this->criteria;

	}
 

	function validateForm($arr, $pkey = '')
	{
        
        $item = new Item();
		$arrayToJs = parent::validateForm($arr, $pkey);

		// pembagian proporsional tidak boleh lebih dari 100
        
        $arrItemPercentage = $arr['detailPercentage'];
        $itemKey = $arr['hidItemKey'];
        $arrItemkey = $arr['hidItemDetailKey'];
        
        $totalPercentage = 0;
        foreach($arrItemPercentage as $itemPercentage){
            
            $totalPercentage += $itemPercentage;
            
        }
        
        
        //validasi tidak boleh lebih dari 100 proporsionalnya
        if($totalPercentage > $this->percentage)    
           $this->addErrorList($arrayToJs,false,$this->errorMsg['itemProportional'][3]);

        //tidak boleh 0
        if($totalPercentage == 0)
		  $this->addErrorList($arrayToJs,false,$this->errorMsg['itemProportional'][2]);
        
        //validasi hanya satu barang yang boleh di proporsional

        $rsItemHeader = $item->getDataRowById($itemKey);
        
        if( empty($arr['hidId'])){
            
            $rs = $this->searchDataRow(array($this->tableName.'.pkey'),' and '.$this->tableName.'.itemkey = '.$this->oDbCon->paramString($itemKey));
        
        }else{
            
             $rs = $this->searchDataRow(array($this->tableName.'.pkey'),' and '.$this->tableName.'.pkey <> '.$this->oDbCon->paramString($arr['hidId']).' and '.$this->tableName.'.itemkey = '.$this->oDbCon->paramString($itemKey));
    
        }    
        
        if(!empty($rs))
                $this->addErrorList($arrayToJs,false,  '<b>'.$rsItemHeader[0]['name'].'</b>. '.$this->errorMsg['itemProportional'][1]); 	 

        
        $arrDetailKeys = array(); 
         
		for($i=0;$i<count($arrItemkey);$i++) { 
			if (empty($arrItemkey[$i]) ){ 
				$this->addErrorList($arrayToJs,false, $this->errorMsg['item'][1]); 	
			} else{
       
                // cek ada detail double gk  
                if (in_array($arrItemkey[$i],$arrDetailKeys)){  
                    $rsItem = $item->getDataRowById($arrItemkey[$i]);
                    $this->addErrorList($arrayToJs,false, '<b>'.$rsItem[0]['name'].'</b>. '.$this->errorMsg[215]); 	 
                }else{ 
                    array_push($arrDetailKeys, $arrItemkey[$i]);
                } 
            } 
             
		} 

		return $arrayToJs;
	}
	
	
	
	function afterUpdateData($arrParam, $action)
	{
		// search ulang detail ut kupdate header
		
		$pkey = $arrParam['pkey']; 
		
	    $rsItemPercentage = $this->getDetailItemPercentage($pkey);
        $totalPercentage = 0;
         foreach($rsItemPercentage as $row){
            
            $totalPercentage += $row['percentage'];
            
        }
        
        $remainPercentage = $this->percentage - $totalPercentage;
		
		$sql = '
			update  ' . $this->tableName . '
			set 
				remainpercentage = '.$this->oDbCon->paramString($remainPercentage).'
			where 
				pkey = ' . $this->oDbCon->paramString($pkey);

		$this->oDbCon->execute($sql);
		
        

	}
	
  
	function normalizeParameter($arrParam, $trim = false)
	{
		$arrParam = parent::normalizeParameter($arrParam, true);
		// tambahin default owner dan tenant key
		
		return $arrParam;
	}
    
    
	function getDetailItemPercentage($pkey,$criteria='')
	{

		
		// wajib pake paramstring
		
		$sql = '
				select
					' . $this->tableNameDetail . '.*,
					' . $this->tableItem . '.name as itemname
				from 
					' . $this->tableNameDetail . '
                    left join ' . $this->tableItem . '  ON ' . $this->tableNameDetail . '.itemkey = ' . $this->tableItem . '.pkey
				where 
					' . $this->tableNameDetail . '.refkey = ' . $this->oDbCon->paramString($pkey);

		if (!empty($orderBy)) $sql .= ' '. $criteria;
			
		return $this->oDbCon->doQuery($sql);
	}


}

?>