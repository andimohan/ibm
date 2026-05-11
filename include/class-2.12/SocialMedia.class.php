<?php

class SocialMedia extends BaseClass{
 
   function __construct($domainName = ''){
		 
		parent::__construct();  
       
		$this->tableName = 'social_media'; 
        $this->tableStatus = 'master_status';   
		$this->securityObject = 'SocialMedia'; 
         
        $this->arrData = array();
        $this->arrData['pkey'] = array('pkey');
        $this->arrData['code'] = array('code'); 
        $this->arrData['statuskey'] = array('selStatus');   
        
        
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 70));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
 
        
		$this->overwriteConfig();
   }
 
    function getQuery(){ 
	   $sql = '
			select
					'.$this->tableName. '.*, 
                    '.$this->tableStatus. '.status as statusname
				from 
					'.$this->tableName . ',
					'.$this->tableStatus. ' 
                where
                    '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey  
                     
 		' .$this->criteria ;
  
        return $sql;
    } 
     
   function normalizeParameter($arrParam, $trim=false){ 
  
      
        return $arrParam; 
    }
      
 
    function getSocialMediaInput(){
        
        $rs = $this->searchData($this->tableName.'.statuskey',1);
        
        foreach($rs as $key=>$value){
            $socialkey = $value['pkey'];
            
            $rs[$key]['hidsocialkey'] = $this->inputHidden('hidSocialKey[]', array('value' =>$socialkey));
            $rs[$key]['inputsocial'] = $this->inputText('socialMedia'.$socialkey); 
            
        }
        
        return $rs;
       
    }
    
    
}
?>