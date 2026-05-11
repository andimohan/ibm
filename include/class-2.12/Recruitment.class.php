<?php

class Recruitment extends BaseClass{
   
   function __construct(){
		
		parent::__construct();
       
		$this->tableName = 'recruitment'; 
		$this->tableJob = 'job_opportunities'; 
		$this->securityObject = 'Recruitment'; 
        $this->tableFile = 'recruitment_file'; 
		$this->tableStatus = 'master_status';
        $this->uploadFileFolder = 'recruitment-file/';
       
        $arrDetails = array();
        array_push($arrDetails, array('dataset' => $this->arrDataLang, 'tableName' => $this->tableLangValue));
       
        $this->arrData = array();  
        $this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails));
        $this->arrData['trdate'] = array('trDate','date');
        $this->arrData['code'] = array('code');
        $this->arrData['name'] = array('name');
        $this->arrData['email'] = array('email');
        $this->arrData['phone'] = array('phone');
        $this->arrData['address'] = array('address');
        $this->arrData['jobkey'] = array('hidJobKey');
        $this->arrData['description'] = array('description');  

        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'name','title' => 'name','dbfield' => 'name','default'=>true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'job','title' => 'jobOpportunities','dbfield' => 'title','default'=>true, 'width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'email','title' => 'email','dbfield' => 'email','default'=>true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'phone','title' => 'phone','dbfield' => 'phone','default'=>true, 'width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'address','title' => 'address','dbfield' => 'address','default'=>true, 'width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
       
        $this->newLoad = true;
       
        $this->includeClassDependencies(array(
            'JobOpportunities.class.php',
        )); 
           
           
        $this->overwriteConfig();
   }
   
   
	 function getQuery(){
	   
	   return '
				select
					'.$this->tableName. '.*,
					'.$this->tableJob.'.title, 
					'.$this->tableStatus.'.status as statusname 
				from 
					'.$this->tableName . ',
					'.$this->tableJob . ',
                    '.$this->tableStatus.' 
				where  		 
					'.$this->tableName . '.jobkey = '.$this->tableJob.'.pkey and
					'.$this->tableName . '.statuskey = '.$this->tableStatus.'.pkey 
 		' .$this->criteria ; 
		 
    }
	 
	
	 function validateForm($arr,$pkey = ''){
		     
		$arrayToJs = parent::validateForm($arr,$pkey); 
         
		$name = $arr['name'];  
         $email = $arr['email'];
         $phone = $arr['phone'];
         $address = $arr['address'];
         $jobfieldkey = $arr['hidJobFieldKey'];
        $file =  $arr['item-file-uploader'];

		if(empty($name)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['name'][1]);
		}
         
        if(isset($arr['email']) && !empty($arr['email'])){
            $email = $arr['email'];
            
			if(!filter_var($email, FILTER_VALIDATE_EMAIL)) 
				$this->addErrorList($arrayToJs,false,$this->errorMsg['email'][3]);

		}  
                
        if( empty($email)){ 
            $this->addErrorList($arrayToJs,false,$this->errorMsg['email'][1]); 
		  }
         
        if (empty($phone)){ 
            $this->addErrorList($arrayToJs,false,$this->errorMsg['phone'][1]); 
		}
         
        if (empty($address)){ 
            $this->addErrorList($arrayToJs,false,$this->errorMsg['address'][1]); 
		}
         
         if(isset($arr['item-file-uploader']) && !empty($arr['item-file-uploader'])){ 
            $arrFile = explode(",",$arr['item-file-uploader']);
            if(count($arrFile) > PLAN_TYPE['maxproductfile'])
                $this->addErrorList($arrayToJs,false,$this->errorMsg['limit'][3] .' ('.$this->lang['max'].' '. $this->formatNumber(PLAN_TYPE['maxproductfile']). ' '. strtolower($this->lang['files']).')' );

            for($i=0;$i<count($arrFile);$i++){
                if (empty($arrFile[$i]))
                    continue;

                $path = $this->uploadTempDoc.$this->uploadFileFolder.$arr['token-item-file-uploader']; 
                if (filesize($path.'/'.$arrFile[$i]) > (pow(1024,2) * PLAN_TYPE['maxfilesize']) )
                    $this->addErrorList($arrayToJs,false,$this->errorMsg['limit'][5] .' ('.$this->lang['max'].' '. $this->formatNumber(PLAN_TYPE['maxfilesize']). ' MB)' );
            }
        }
         
        if(isset($arr['fromFE']) && $arr['fromFE'] == 1){

            if(empty($file))
                $this->addErrorList($arrayToJs,false, 'Dokumen harus diupload.'); 

        }

        
         return $arrayToJs;
         
	 }
    
    function delete($id,$forceDelete = false,$reason = ''){
		 
		$arrayToJs =  array();
		// tdk bisa didelete utk transaksi, tp ubah ke cancel
		if(isset( $this->tableNameDetail) &&!empty($this->tableNameDetail)){  
             $arrayToJs = $this->changeStatus($id, 7,$reason,false,$forceDelete);  
             return $arrayToJs; 
		} 
		
		try{ 
		
	 		$arrayToJs = $this->validateDelete($id);
			if (!empty($arrayToJs)) 
				return $arrayToJs;
					 
			 if(!$this->oDbCon->startTrans())
				throw new Exception($this->errorMsg[100]);
				 
				$sql = 'delete from  '.$this->tableName.' where pkey = ' . $this->oDbCon->paramString($id);
				$this->oDbCon->execute($sql);
                $this->deleteAll($this->defaultDocUploadPath.$this->uploadFileFolder.$id);

                $this->setTransactionLog(DELETE_DATA,$id);
            
				$this->oDbCon->endTrans();
					 
				$this->addErrorList($arrayToJs,true,$this->lang['dataHasBeenSuccessfullyUpdated']); 
				 
		} catch(Exception $e){
			$this->oDbCon->rollback(); 
			$this->addErrorList($arrayToJs,false, $e->getMessage()); 
			
		}		 
			 	
 		return $arrayToJs; 
	}
    
    function getItemFile($pkey){
		$sql = 'select * from '.$this->tableFile.' where refkey = '.$this->oDbCon->paramString($pkey).' order by pkey asc';	
		return $this->oDbCon->doQuery($sql);
    } 
    
    function updateFile($pkey,$token,$arrFile){		
		 
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
				
				$sql = 'insert into '.$this->tableFile.' (pkey,refkey,file) values ('.$this->oDbCon->paramString($imagekey).','.$this->oDbCon->paramString($pkey).','.$this->oDbCon->paramString($arrFile[$i]).')';	
				$this->oDbCon->execute($sql);	 
				 
			}		
		} 
					
	}   
  
        
    function normalizeParameter($arrParam, $trim = false){ 
                 
               
        // harusnya boleh diupdate kalo sudah di save
            if(isset($arrParam['token-item-file-uploader']))
               $this->updateFile($arrParam['pkey'], $arrParam['token-item-file-uploader'], $arrParam['item-file-uploader']);    
            
        $arrParam = $this->updateOthersLangValue($arrParam, $this->arrData); 
        $arrParam = parent::normalizeParameter($arrParam,true); 
          
         return $arrParam; 
    }
		
    
}

?>
