<?php
  
class Projects extends BaseClass{ 
   
   function __construct(){
		
		parent::__construct();
       
		$this->tableName = 'projects_header';
		$this->tableNameDetail = 'projects_detail';
		$this->tableFile = 'projects_detail_file';
		$this->tableParticipant = 'projects_detail_participant';
		$this->tableLabel = 'projects_detail_label';
        $this->tableStatus = 'master_status';
        $this->tableEmployee = 'employee';
        $this->tableCustomer = 'customer';
        $this->securityObject = 'customer';
        $this->isTransaction = true;
        
        $this->arrDataDetail = array(); 
        $this->arrDataDetail['pkey'] = array('hidDetailKey');
        $this->arrDataDetail['refkey'] = array('pkey','ref');
        $this->arrDataDetail['description'] = array('detailDesc');
        $this->arrDataDetail['duedate'] = array('dueDate','date');
        $this->arrDataDetail['stickey'] = array('isStick');
        $this->arrDataDetail['createdon'] = array('createOn','date');
        $this->arrDataDetail['createdby'] = array('createBy');
       
        $this->arrData = array(); 
        $this->arrData['pkey'] = array('pkey', array('dataDetail' => array('dataset' => $this->arrDataDetail)));
        //$this->arrData['code'] = array('code');
        $this->arrData['name'] = array('name');
        $this->arrData['customerkey'] = array('hidCustomerKey');
        $this->arrData['trdate'] = array('trDate','date');
        //$this->arrData['employeekey'] = array('hidEmployeeKey'); 
        $this->arrData['trdesc'] = array('trDesc');
        $this->arrData['statuskey'] = array('selStatus');   
       
        $this->tableNeedToBeCopyOnCancel = array($this->tableNameDetail);
         
        //$this->arrLinkedTable = array();
   
   }
   
 
   function getQuery(){
	   
	   $sql =  '
			SELECT '.$this->tableName.'.* , 
			   '.$this->tableStatus.'.status as statusname , 
			   '.$this->tableCustomer.'.name as customername 
			FROM 
                '.$this->tableStatus.',  
                '.$this->tableCustomer.',  
                '.$this->tableName.'    
			WHERE     
                '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey and
                '.$this->tableName.'.customerkey = '.$this->tableCustomer.'.pkey 
 		' .$this->criteria ; 
         
       //$this->setLog($sql);
       return $sql;
		 
    }  
	 
    		
     function afterStatusChanged($rsHeader){   
        // retrieve latest status
        $rsHeader = $this->getDataRowById($rsHeader[0]['pkey']);
        if ($rsHeader[0]['statuskey'] == 2)
            $this->changeStatus($rsHeader[0]['pkey'],3); 
    }
    
    function afterUpdateData($arrParam, $action){   
        //$pkey = $arrParam['pkey'];
        $this->updateDetail($arrParam);
    }
    
    function addData($arrParam){
        $desc = $arrParam['detailDesc'];  
        //$sql = 'select pkey from '.$this->tableNameDetail.' order by pkey DESC LIMIT 1';
        //$rsTes = $this->oDbCon->doQuery($sql);
        $detailPkey = $this->getNextKey($this->tableNameDetail); 
        /*$detailPkey = 0;
        if(empty($rsTes)){
            $detailPkey = 1;
        }else{
            $detailPkey = $rsTes[0]['pkey'];
        }*/
            
        
            
        for($i=0;$i<count($desc);$i++){
            $arrParam['hidDetailKey'][$i] = $detailPkey + $i;     
            
        }
        return parent::addData($arrParam);    
    }

    function editData($arrParam){ 
        //unset( $this->arrData['warehousekey']); 
        return parent::editData($arrParam);    
    }
    
    function updateDetail($arrParam){
		$pkey = $arrParam['pkey'];
		$detailkey = $arrParam['hidDetailKey'];
		$employeekey = $arrParam['selParticipants'];
		$desc = $arrParam['detailDesc'];
        
        for($i=0;$i<count($desc);$i++){
            $sql = 'delete from '.$this->tableParticipant.' where refkey = '. $this->oDbCon->paramString($detailkey);
            //$this->oDbCon->execute($sql);
        
            $sql = 'delete from '.$this->tableLabel.' where refkey = '. $this->oDbCon->paramString($detailkey);
            //$this->oDbCon->execute($sql);
            
            for($j=0;$j<count($employeekey);$j++){
                $sql = 'insert into '.$this->tableParticipant.' (
						refkey,
						refheaderkey,
                        employeekey
					 ) values (
						'.$this->oDbCon->paramString($detailkey[$i]).',
						'.$this->oDbCon->paramString($pkey).',
						'.$this->oDbCon->paramString($employeekey[$j]).'
					)';	  
            
			     $this->oDbCon->execute($sql);    
                
            }
            
            $this->updateFile($pkey,$detailkey[$i],$arrParam['token-item-file-uploader'], $arrParam['item-file-uploader']);
            
            
        }
        
	}
    
    function updateFile($pkey,$detailkey,$token,$arrFile){		
		 
        if(!empty($arrFile)) 
            $this->validateDiskUsage(); 
        
		$sourcePath = $this->uploadTempDoc.$this->uploadFileFolder.$token;
		$destinationPath = $this->defaultDocUploadPath.$this->uploadFileFolder;
		
			
		if(!is_dir($destinationPath)) 
			mkdir ($destinationPath,  0755, true);
			
		$destinationPath .= $pkey;  
		 
		
		//delete previous files	    
		$this->deleteAll($destinationPath);  
		$sql = 'delete from '.$this->tableFile.' where refkey = '. $this->oDbCon->paramString($pkey);
		$this->oDbCon->execute($sql); 
		
		 
		if(!is_dir($sourcePath)) 
			return;
	
		if (!empty($arrFile))	{
			$arrFile = explode(",",$arrFile);
			for ($i=0;$i<count($arrFile);$i++){   
				$this->uploadImage($sourcePath, $destinationPath,$arrFile[$i]);
				
				$imagekey = $this->getNextKey($this->tableFile);  
				
				$sql = 'insert into '.$this->tableFile.' 
                (pkey,refkey,refheaderkey,file) values 
                ('.$this->oDbCon->paramString($imagekey).','.$this->oDbCon->paramString($detailkey).','.$this->oDbCon->paramString($pkey).','.$this->oDbCon->paramString($arrFile[$i]).')';	
				$this->oDbCon->execute($sql);	 
				 
			}		
		} 
					
	}
        
    function validateForm($arr,$pkey = ''){ 
		  
		$arrayToJs = parent::validateForm($arr,$pkey); 
		  
		
		
		return $arrayToJs;
	 }
    
    
     
    function normalizeParameter($arrParam, $trim=false){
        $warehouse = new Warehouse();
        
        $arrParam['trDesc'] = (isset($arrParam['trDesc'])) ? $arrParam['trDesc'] : ''; 
        $arrParam['hidEmployeeKey'] = (isset($arrParam['hidEmployeeKey'])) ? $arrParam['hidEmployeeKey'] : '';
        $arrParam['hidCustomerKey'] = (isset($arrParam['hidCustomerKey'])) ? $arrParam['hidCustomerKey'] : ''; 
        $arrParam['detailDesc'] = (isset($arrParam['detailDesc'])) ? $arrParam['detailDesc'] : array();  
        $arrParam['isStick'] = (isset($arrParam['isStick'])) ? $arrParam['isStick'] : array();  
        $arrParam['hidDetailKey'] = (isset($arrParam['hidDetailKey'])) ? $arrParam['hidDetailKey'] : array();  
            
        return $arrParam;
    }
       
    /*function getDetailWithRelatedInformation($pkey,$criteria=''){
        $sql = 'select
	   			'.$this->tableNameDetail .'.*, 
                concat('.$this->tableCOA. '.code,\' - \','.$this->tableCOA. '.name) as coaname, 
                '.$this->tableItem.'.name as costname
			  from
			  	'. $this->tableNameDetail .'
                left join '.$this->tableCOA.' on  '.$this->tableNameDetail.'.coakey = '.$this->tableCOA.'.pkey ,
                '.$this->tableItem.'
			  where
			  	' . $this->tableNameDetail .'.costkey = '.$this->tableItem.'.pkey and
			  	'.$this->tableNameDetail .'.refkey = '.$this->oDbCon->paramString($pkey);
        
        $sql .= $criteria; 
		return $this->oDbCon->doQuery($sql);
    }*/
    
    
    function confirmTrans($rsHeader){   
        
	} 
     
	function validateConfirm($rsHeader){
        
		 
    }		
     
    function cancelTrans($rsHeader,$copy){  
          	
        $id = $rsHeader[0]['pkey'];
        
		//if ($rsHeader[0]['statuskey'] == 1)
			//return;
		 
		if ($copy)
			$this->copyDataOnCancel($id);	
        
	} 
    
    
    function validateCancel($rsHeader,$autoChangeStatus=false){ 
        parent:: validateCancel($rsHeader,$autoChangeStatus);

        $id = $rsHeader[0]['pkey'];


    }
}
?>