<?php
class MembershipLevel extends BaseClass
{

    function __construct(){

        parent::__construct(); 
        $this->tableName = 'membership_level';
        $this->tableNameDetail = 'membership_level_detail';
        $this->tableStatus = 'master_status'; 
        $this->tableMembershipLevelDetail = 'membership_features_detail'; 
		$this->tableCustomerFeatures = 'customer_features';
		$this->uploadFolder = 'membership-level/'; 
		
        $this->securityObject = 'MembershipLevel';
        $this->newLoad = true;  
		
        $this->arrMembershipLevel = array();
        $this->arrMembershipLevel['pkey'] = array('hidFeaturesDetailKey');
        $this->arrMembershipLevel['refkey'] = array('pkey', 'ref');
        $this->arrMembershipLevel['featurekey'] = array('hidFeatureKey');
        $this->arrMembershipLevel['quota'] = array('quota','number');

        $arrDetails = array();
        array_push($arrDetails, array('dataset' => $this->arrMembershipLevel, 'tableName' => $this->tableMembershipLevelDetail));


        //field yg ada di db yg di insert
        $this->arrData = array();
        $this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails));
        $this->arrData['code'] = array('code');
        $this->arrData['name'] = array('name');
        $this->arrData['statuskey'] = array('selStatus');
        $this->arrData['sellingprice'] = array('sellingprice', 'number'); 
        $this->arrData['commissiontype'] = array('commissionType', 'number'); 
        $this->arrData['commissiontotal'] = array('commissionTotal', 'number'); 
        $this->arrData['activeperiodmonth'] = array('activePeriod','number');
        $this->arrData['file'] = array('fileName');


        $this->arrDataListAvailableColumn = array();
        array_push($this->arrDataListAvailableColumn, array('code' => 'code', 'title' => 'code', 'dbfield' => 'code', 'default' => true, 'width' => 70));
        array_push($this->arrDataListAvailableColumn, array('code' => 'name', 'title' => 'name', 'dbfield' => 'name', 'default' => true, 'width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'sellingprice', 'title' => 'price', 'dbfield' => 'sellingprice', 'default' => true, 'width' =>100, 'format' => 'number', 'align' => 'right'));
		
        $this->arrSearchColumn = array();
        array_push($this->arrSearchColumn, array('Kode', $this->tableName . '.code'));
        array_push($this->arrSearchColumn, array('Nama', $this->tableName . '.name')); 
  
        $this->overwriteConfig(); 
    }

 
    function getQuery(){

        $sql = '
                 select
                     ' . $this->tableName . '.*,
                     ' . $this->tableStatus . '.status as statusname 
                 from 
                     ' . $this->tableName . ',' . $this->tableStatus . '
                 where  		
                     ' . $this->tableName . '.statuskey = ' . $this->tableStatus . '.pkey
          ' . $this->criteria;  
        return $sql;
    }

    function validateForm($arr, $pkey = '') {
		
        $arrayToJs = parent::validateForm($arr, $pkey);
		
		$name = $arr['name'];  
		
        if(empty($name)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['membershipLevel'][1]);
		}else{ 
			$rs = $this->isValueExisted($pkey,'name',$name);	
			if(count($rs) <> 0) 
				$this->addErrorList($arrayToJs,false,$this->errorMsg['membershipLevel'][2]); 
		}
		
		$arrImage = explode(",",$arr['item-image-uploader']); 
        for($i=0;$i<count($arrImage);$i++){
            if (empty($arrImage[$i]))
                continue;
            
            $path = $this->uploadTempDoc.$this->uploadFolder.$arr['token-item-image-uploader']; 
            if (filesize($path.'/'.$arrImage[$i]) > (pow(1024,2) * PLAN_TYPE['maximagesize']))
                $this->addErrorList($arrayToJs,false,$this->errorMsg['limit'][4] .' ('.$this->lang['max'].' '. $this->formatNumber(PLAN_TYPE['maximagesize']). ' MB)' );
        } 
		
  		return $arrayToJs;
    }
	
	
	
	function delete($id, $forceDelete = false,$reason = ''){ 
		 
		try{			
				  
				$arrayToJs =  array();
			 	
				if (!$this->oDbCon->startTrans())
					throw new Exception($this->errorMsg[100]);
			
		 		 
				$sql = 'delete from  '.$this->tableName.' where pkey = ' . $this->oDbCon->paramString($id);
				$this->oDbCon->execute($sql);  
				$this->deleteAll($this->defaultDocUploadPath.$this->uploadFolder.$id);
			
        
                $this->setTransactionLog(DELETE_DATA,$id); 
				$this->oDbCon->endTrans(); 
				$this->addErrorList($arrayToJs,true,$this->lang['dataHasBeenSuccessfullyUpdated']);
				
			}catch(Exception $e){
				$this->oDbCon->rollback();
				$this->addErrorList($arrayToJs,false, $e->getMessage()); 
		}			
			
		return $arrayToJs;	
	}
    
	
    function normalizeParameter($arrParam, $trim = false) {  
        $arrParam['fileName'] = $this->updateImages($arrParam['pkey'], $arrParam['token-item-image-uploader'], $arrParam['item-image-uploader']); 
        $arrParam = parent::normalizeParameter($arrParam, true);
        return $arrParam;
    }
	
	
    function getMembershipPrice($pkey,$currencykey){
        
                //seharusnya sesuai yang aktif membership
                $sql = 'select 
                            '.$this->tableNameDetail.'.sellingprice
                        from 
                            '.$this->tableNameDetail.'
                        where
                            '.$this->tableNameDetail.'.refkey = '.$this->oDbCon->paramString($pkey).' and
                            '.$this->tableNameDetail.'.currencykey = '.$this->oDbCon->paramString($currencykey).'
                ';
 
                $rs = $this->oDbCon->doQuery($sql);

            return $rs;

        
    }
	function getFeaturesDetail($pkey, $criteria=''){ 
		
		// gk usah validasi status, karena ad kemungkinaan user masih distatus ini (atau status free- kalo di icommunity)
		// ' . $this->tableCustomerFeatures . '.statuskey = 1 and
		 
		$sql = 'select 
					'.$this->tableMembershipLevelDetail . '.*,
					'.$this->tableCustomerFeatures.'.name
				from 
					' . $this->tableMembershipLevelDetail . ',
					' . $this->tableCustomerFeatures . '
				where 
					' . $this->tableCustomerFeatures . '.pkey = ' . $this->tableMembershipLevelDetail . '.featurekey and 
					' . $this->tableMembershipLevelDetail . '.refkey = ' . $this->oDbCon->paramString($pkey); 
		
		if (!empty($criteria))
				$sql .= ' ' .$criteria;
		
        $rs = $this->oDbCon->doQuery($sql);
		
		return $rs;
    }
	
	function getDefaultLevel(){
		$sql = 'select pkey,name,sellingprice from '.$this->tableName.' where statuskey = 1 order by isdefault desc, pkey desc limit 1'; 
		$rs = $this->oDbCon->doQuery($sql);
		
		return $rs;
	}
     
   
}
