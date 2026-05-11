<?php

class CostGrouping extends BaseClass
{
    function __construct()
    {
        parent::__construct();

        $this->tableName = 'cost_grouping_header';
        $this->tableNameDetail = 'cost_grouping_detail';
        $this->tableStatus = 'master_status';
        $this->tableCOA = 'chart_of_account';

        $this->securityObject = 'costGrouping';

        $this->newLoad = true;

        $this->arrDataDetail = array();
        $this->arrDataDetail['pkey'] = array('hidDetailKey');
        $this->arrDataDetail['refkey'] = array('pkey', 'ref');
        $this->arrDataDetail['coakey'] = array('hidCoaKey');

        $this->arrData = array();
        $this->arrData['pkey'] = array('pkey', array('dataDetail' => array('dataset' => $this->arrDataDetail)));
        $this->arrData['code'] = array('code');
        $this->arrData['name'] = array('name');
        $this->arrData['parentkey'] = array('hidParentKey');
        $this->arrData['orderlist'] = array('orderList');
        $this->arrData['statuskey'] = array('selStatus');
        $this->arrData['isleaf'] = array('isleaf');
        

        $this->arrSearchColumn = array();
        array_push($this->arrSearchColumn, array('Kode', $this->tableName . '.code'));
        array_push($this->arrSearchColumn, array('Nama', $this->tableName . '.name'));
        array_push($this->arrSearchColumn, array('Status', $this->tableStatus . '.status'));


        $this->arrDataListAvailableColumn = array();
        array_push($this->arrDataListAvailableColumn, array('code' => 'code', 'title' => 'code', 'dbfield' => 'code', 'default' => true, 'width' => 150)); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'name', 'title' => 'name', 'dbfield' => 'name', 'default' => true, 'width' => 200)); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'status', 'title' => 'status', 'dbfield' => 'statusname', 'default' => true, 'width' => 200));

        $this->includeClassDependencies(array(
            'GeneralJournal.class.php',
            'ChartOfAccount.class.php'
        ));

        $this->overwriteConfig();

    }

    function getQuery()
    {

        return '
				select
					' . $this->tableName . '.*,
                    '. $this->tableName .'.name as parentname,
                   	' . $this->tableStatus . '.status as statusname
				from 
                ' . $this->tableStatus . ', 
                ' . $this->tableName . '
				where  		
					' . $this->tableName . '.statuskey = ' . $this->tableStatus . '.pkey 
 		' . $this->criteria;
    }
 
    function getDetailWithRelatedInformation($pkey, $criteria = ''){
        $sql = 'select
	   			' . $this->tableNameDetail . '.*,
                ' . $this->tableCOA . '.name as coaname,
                ' . $this->tableCOA . '.code as coacode,
                concat(' . $this->tableCOA . '.code, " - ", ' . $this->tableCOA . '.name) as coacode 
			  from
			  	' . $this->tableNameDetail . ' 
                left join ' . $this->tableCOA . ' on ' . $this->tableCOA . '.pkey =' . $this->tableNameDetail . '.coakey
			  where
			  	' . $this->tableNameDetail . '.refkey = ' . $this->oDbCon->paramString($pkey);

        $sql .= $criteria;
 
        return $this->oDbCon->doQuery($sql);
    }

    function validateForm($arr, $pkey = '') {

        $arrayToJs = parent::validateForm($arr, $pkey);

        $name = $arr['name'];
        $orderList = $arr['orderList'];
        $coaKey = $arr['hidCoaKey'];
        $parent = $arr['hidParentKey'];
        $pkey = $arr['pkey'];
        $coaKey = $arr['hidCoaKey'];

        if(empty($name)){
            $this->addErrorList($arrayToJs, false, $this->errorMsg['name'][1]);
        }

        if(empty($orderList)){
            $this->addErrorList($arrayToJs, false, $this->errorMsg['orderList'][1]); 
        }

        if(empty($coaKey)){
            $this->addErrorList($arrayToJs, false, $this->errorMsg['coa'][1]);
        }

        if($parent == $pkey){
            $this->addErrorList($arrayToJs, false, $this->errorMsg['costGrouping'][1]);
        }
  
        //cek agar nilai tidak double 
        if(count($coaKey) !== count(array_unique($coaKey))) {
            $this->addErrorList($arrayToJs, false, $this->errorMsg['coa'][2]);
        }

        return $arrayToJs;
    }
 
    function afterStatusChanged($rs){
         $this->updateLeaf(); 
    } 
    
    function afterUpdateData($arrParam, $action){ 
		$this->updateLeaf(); 
		$this->updateRootInformation($arrParam['pkey']);
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
    
    function generateTreeReport($startDate ,$endDate , $arrWarehouse, $parentkey = 0, $level = 0, &$returnArr = array()){
        
        $chartOfAccount = new ChartOfAccount();
        
        $rs = $this->searchDataRow(array($this->tableName.'.pkey',$this->tableName.'.code',$this->tableName.'.name',$this->tableName.'.isleaf',$this->tableName.'.parentkey',$this->tableName.'.rootkey',$this->tableName.'.rootpath'),
                                 ' and '.$this->tableName.'.parentkey = ' .$this->oDbCon->paramString($parentkey) .' 
                                   and '.$this->tableName.'.statuskey = 1',
                                  'order by orderlist asc');
        
        foreach($rs as $row){
            $row['level'] = $level;
            $row['amount'] = 0;
            
            // hitung nilai
            if($row['isleaf'] == 1) {
                $rsDetail = $this->getDetailById($row['pkey']); 

                $arrCOAKey = array_column($rsDetail,'coakey');

                $arrCriteria = array();
                array_push($arrCriteria,$chartOfAccount->tableName.'.pkey in ('. $this->oDbCon->paramString($arrCOAKey,',').')'); 
                
                $coaCriteria = ' and ('.implode(' or ' , $arrCriteria).')';
                
//                $this->setLog(  $arrCOAKey , true);
//                $this->setLog( date('H:i:s',time()) , true);
                $coaAmount = $chartOfAccount->sumRunningAmount($startDate,$endDate, $coaCriteria,FINANCIAL_REPORT['incomeStatement'],1,$arrWarehouse); 
//                $this->setLog( date('H:i:s',time()) , true);
                
                $coaAmount = array_column($coaAmount,null,'pkey');
                
                foreach($rsDetail as $coaDetailRow) { 
                   $amount = $coaAmount[$coaDetailRow['coakey']]['amount'];
                   $row['amount'] += $amount;

                  // sum ke parent   
                  $parentkey =  $row['parentkey']; 
                  while($parentkey != 0){
                      $returnArr[$parentkey]['amount'] += $amount;
                      $parentkey =  $returnArr[$parentkey]['parentkey'];
                  }
                    
                }
            }

            $returnArr[$row['pkey']] = $row;
            
            if($row['isleaf'] == 0) 
                $this->generateTreeReport($startDate ,$endDate,$arrWarehouse, $row['pkey'],$level+1, $returnArr);
           
        }
        
        return $returnArr;
    }
    
    
	function updateRootInformation($id){
        
        $arrRoot = $this->getRootInformation($id);   
        
        $sql = '
                    UPDATE	
                     '.$this->tableName .'
                    SET	   
                     rootkey = '.$this->oDbCon->paramString($arrRoot['rootkey']).',
                     rootpath = '.$this->oDbCon->paramString($arrRoot['rootpath']).'  
                    WHERE	
                     pkey = '.$this->oDbCon->paramString($id).' 

            ';    
        $this->oDbCon->execute($sql); 
         

    } 
    
      function getRootInformation($id,&$rootpath=''){
          
        $rs = $this->getDataRowById($id);
        
        if ($rs[0]['parentkey'] == 0 ){ 
            return array( 'rootkey'=>$rs[0]['pkey'], 'rootpath'=> trim($rootpath));
        }
        
        $rootpath .= $rs[0]['parentkey'].' '; 
        
        return $this->getRootInformation($rs[0]['parentkey'],$rootpath); 
        
    }
    
    
    function normalizeParameter($arrParam, $trim = false){

        $arrParam['isleaf'] = 0; 
        $coaKey = $arrParam['hidCoaKey'];
        
 
        foreach($coaKey as $key => $value) {
            if(empty($value)) {
                unset($arrParam['hidDetailKey'][$key]);
                unset($coaKey[$key]);
                unset($arrParam['coaCode'][$key]);
            }
        }

        $arrParam = parent::normalizeParameter($arrParam, true);
        
        return $arrParam;
    } 
}

?>