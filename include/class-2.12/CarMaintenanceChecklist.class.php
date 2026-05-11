<?php
class CarMaintenanceChecklist extends BaseClass{
    
   function __construct(){
		
		parent::__construct();
		
		$this->tableName = 'car_maintenance_checklist_header';
        $this->tableNameDetail ='car_maintenance_checklist_detail';
        $this->tableGroupItemHeader = 'item_checklist_group_header';
        $this->tableGroupItemDetail = 'item_checklist_group_detail';
        $this->tableItem = 'item';
        $this->tableUltimate = 'ultimate_package';
        $this->tableCategory = 'item_category';
        $this->tableItemGroup = 'item_category_group';
        $this->tableCustomer = 'customer';
        $this->tableCar = 'car';
        $this->tableSeries = 'car_series';
		$this->tableStatus = 'master_status'; 
		$this->securityObject = 'CarMaintenanceChecklist'; 
		
       
        $this->arrData = array(); 
        $this->arrData['pkey'] = array('pkey');
        $this->arrData['code'] = array('code');
        $this->arrData['trdate'] = array('trDate','date');
        $this->arrData['carkey'] = array('hidCarKey'); 
        $this->arrData['statuskey'] = array('selStatus');  
        $this->arrData['mileage'] = array('mileage', 'number');
        $this->arrData['actemperaturebefore'] = array('acTemperatureBefore','number');
        $this->arrData['actemperatureafter'] = array('acTemperatureAfter','number'); 
        $this->arrData['fogging'] = array('fogging');  
        $this->arrData['accucheck'] = array('accuCheck');
        $this->arrData['acculife'] = array('accuLife','number');
        $this->arrData['accuah'] = array('accuAh','number');
        $this->arrData['accuresistance'] = array('accuResistance','number');
        $this->arrData['oilout'] = array('oilOut');
        $this->arrData['oilin'] = array('oilIn');
        $this->arrData['mileagemaintenance'] = array('mileageMaintenance');
        $this->arrData['mileagenextdue'] = array('mileageNextDue'); 
        $this->arrData['oilfilter'] = array('oilFilter','number');
        $this->arrData['airfilter'] = array('airFilter','number');
        $this->arrData['trdesc'] = array('trDesc');
        $this->arrData['trworkdesc'] = array('trWorkDesc');
        $this->arrData['trpartchangedesc'] = array('trPartChangeDesc');
        $this->arrData['trsuggestiondesc'] = array('trSuggestionDesc');  
        $this->arrData['customerkey'] = array('hidCustomerKey');
       
        $this->arrData['oiltypekey'] = array('selOilType');
        $this->arrData['oilbrandkey'] = array('oilBrandKey');
        $this->arrData['ackey'] = array('selAc');
        $this->arrData['tuneupkey'] = array('selTuneUp');
        $this->arrData['ultimatepackagekey'] = array('selUltimate');
       
       
	}
	
	 function getQuery(){
	   
	   $sql = '
			select
					'.$this->tableName. '.*,
                    '.$this->tableCustomer.'.name as customername,
                    '.$this->tableCar.'.policenumber as policenumber,
                    '.$this->tableSeries.'.name as seriesname,
					'.$this->tableStatus.'.status as statusname
				from
					'.$this->tableName.' 
                        left join  '.$this->tableCustomer.' on '.$this->tableName.'.customerkey = '.$this->tableCustomer.'.pkey  ,
                    '.$this->tableStatus.', 
                    '.$this->tableCar.' 
                        left join '.$this->tableSeries.' on  '.$this->tableCar.'.serieskey = '.$this->tableSeries.'.pkey
                where
					'.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey and
                    '.$this->tableName.'.carkey = '.$this->tableCar.'.pkey 
                    
 		' .$this->criteria ; 
		  
        $sql .= $this->getCompanyCriteria()	; 
       
       return $sql;
    }

    function afterUpdateData($arrParam, $action){
        $this->updateDetail($arrParam['pkey'],$arrParam);
    }
    
    function updateDetail($pkey,$arrParam){ 
	 	$sql = 'delete from '.$this->tableNameDetail.' where refkey = '. $this->oDbCon->paramString($pkey);
		$this->oDbCon->execute($sql);

        $arrGroup = array(1,2); 
            
	 	         
     	for ($i=0;$i<count($arrGroup);$i++){

            $arrChkItemKey = $arrParam['hidItemKey_'.$arrGroup[$i]]; 
            $arrDescription = $arrParam['description_'.$arrGroup[$i]]; 
            $arrIsCheck = $arrParam['chkIsCheck_'.$arrGroup[$i]];
            $arrHidIsCheck = $arrParam['hidChkIsCheck_'.$arrGroup[$i]];
            
            
            $arrIsReplace = $arrParam['chkIsReplace_'.$arrGroup[$i]];
            $arrHidIsReplace = $arrParam['hidChkIsReplace_'.$arrGroup[$i]];
            for ($j=0;$j<count($arrChkItemKey);$j++){
                
                if (empty($arrChkItemKey[$j]) || (empty($arrDescription[$j]) && $arrHidIsCheck[$j]<1 && $arrHidIsReplace[$j]<1 ))
                    continue;
                

                $sql = 'insert into '.$this->tableNameDetail.' (
                            refkey,
                            itemkey,
                            groupkey,
                            description,
                            ischeck,
                            isreplace
                         ) values (
                            '.$this->oDbCon->paramString($pkey).',
                            '.$this->oDbCon->paramString($arrChkItemKey[$j]).',
                            '.$this->oDbCon->paramString($arrGroup[$i]).',
                            '.$this->oDbCon->paramString($arrDescription[$j]).',
                            '.$this->oDbCon->paramString($arrHidIsCheck[$j]).',
                            '.$this->oDbCon->paramString($arrHidIsReplace[$j]).'
                        )';	 
                $this->oDbCon->execute($sql); 
                 
            } 
        }
	
	}
	
	function validateForm($arr,$pkey = ''){
		 
		$arrayToJs = parent::validateForm($arr,$pkey);
 
		return $arrayToJs; 
	 }	 
	   
    function getDetailValue($pkey, $groupkey){
        
        $sql = 'select 
                    *
                from
                    '.$this->tableNameDetail.'
                where
                    '.$this->tableNameDetail.'.refkey = '.$this->oDbCon->paramString($pkey).' and
                    '.$this->tableNameDetail.'.groupkey = '.$this->oDbCon->paramString($groupkey).'   
                ' 
            ;
        
        
        return $this->oDbCon->doQuery($sql);
    }
      /*
    function getPackage($ref){
        
        $sql = 'select 
                    '.$this->tableItem.'.*,
                    concat( '.$this->tableItem.'.code,\' - \',  '.$this->tableItem.'.name) as itemcodename
                from
                    '.$this->tableItem.'
                where
                    '.$this->tableItem.'.categorykey in 
                    ( select categorykey from '.$this->tableItemGroup.'
                      where '.$this->tableItemGroup.'.ref = '.$this->oDbCon->paramString($ref).' )' 
            ;
        
        return $this->oDbCon->doQuery($sql);
    }
    
  
    function getUltimate(){
        
        $sql = 'select
                    '.$this->tableUltimate.'.*
                from
                    '.$this->tableUltimate.'
                where 
                    '.$this->tableUltimate.'.statuskey = 1
            
        ';
              
        return $this->oDbCon->doQuery($sql);
    }
    
    function getUltimateData($ulti){
        
        $sql = 'select
                    '.$this->tableUltimate.'.*
                from
                    '.$this->tableUltimate.'
                where 
                    '.$this->tableUltimate.'.statuskey = 1 and
                    pkey = '.$this->oDbCon->paramString($ulti).'
            
        ';
              
        return $this->oDbCon->doQuery($sql);
    }
  */             
    
}
?>