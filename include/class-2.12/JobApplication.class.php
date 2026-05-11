<?php

class JobApplication extends BaseClass{
   
   function __construct(){
		
		parent::__construct();
       
		$this->tableName = 'job_application'; 
		$this->tableJobOpportunity = 'job_opportunities';   
		$this->securityObject = 'JobApplication';  
        $this->tableDepartment = 'career_department';  
        $this->tableExperience = 'job_experience';  
       
		$this->tableStatus = 'job_application_status';
        $this->uploadResumeFolder = 'job-application-resume/'; 
    
        $this->arrData = array();  
        $this->arrData['pkey'] = array('pkey');
        $this->arrData['code'] = array('code');
        $this->arrData['refjobopportunitykey'] = array('hidRefJobOpportunity');
        $this->arrData['name'] = array('name');
        $this->arrData['email'] = array('email');
        $this->arrData['phone'] = array('phone');
        $this->arrData['sexkey'] = array('selSex');
        $this->arrData['address'] = array('address');
        $this->arrData['resumefile'] = array('item-file-uploader',array('datatype' => 'file', 'uploadFolder' => $this->uploadResumeFolder,  'token' => 'token-item-file-uploader', 'fileName' => 'item-file-uploader'));
        $this->arrData['portfoliourl'] = array('portfolioURL');
        $this->arrData['latestrole'] = array('latestRole');
        $this->arrData['latestcompany'] = array('latestCompany');
        $this->arrData['startdate'] = array('trStartDate','date');
        $this->arrData['enddate'] = array('trEndDate','date');
        $this->arrData['isstillwork'] = array('chkStillWork');
        $this->arrData['referencekey'] = array('selReference');
        $this->arrData['considerationkey'] = array('selConsideration');
        

        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'name','title' => 'name','dbfield' => 'name','default'=>true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'phone','title' => 'phone','dbfield' => 'phone','default'=>true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'email','title' => 'email','dbfield' => 'email','default'=>true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'jobTitle','title' => 'jobOpportunities','dbfield' => 'jobtitle','default'=>true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'department','title' => 'department','dbfield' => 'departmentname', 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'experience','title' => 'experience','dbfield' => 'experience',  'width' => 100)); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
       

        $this->arrSearchColumn = array ();
        array_push($this->arrSearchColumn, array('Kode', $this->tableName . '.code'));  
        array_push($this->arrSearchColumn, array('name', $this->tableName . '.name'));  
        array_push($this->arrSearchColumn, array('phone', $this->tableName. '.phone') );
        array_push($this->arrSearchColumn, array('email', $this->tableName . '.email')); 


        $this->newLoad = true;
       
        $this->includeClassDependencies(array(
            
        )); 
           
           
        $this->overwriteConfig();
   }
   
   
	 function getQuery(){
	   
	   $sql= '
				select
					'.$this->tableName. '.*, 
					'.$this->tableJobOpportunity.'.title as jobtitle,  
					'.$this->tableDepartment.'.name as departmentname,   
					'.$this->tableExperience.'.name as experience,   
					'.$this->tableStatus.'.status as statusname 
				from 
					'.$this->tableName . ' 
                         left join '.$this->tableJobOpportunity.' on '.$this->tableName . '.refjobopportunitykey = '.$this->tableJobOpportunity.'.pkey
                         left join '.$this->tableDepartment.' on '.$this->tableJobOpportunity . '.departmentkey = '.$this->tableDepartment.'.pkey
                         left join '.$this->tableExperience.' on '.$this->tableJobOpportunity . '.experiencekey = '.$this->tableExperience.'.pkey, 
                    '.$this->tableStatus.' 
				where  		 
					'.$this->tableName . '.statuskey = '.$this->tableStatus.'.pkey  
 		' .$this->criteria ; 
         
//         $this->setLog($sql,true);
         return $sql;
		 
    }
	 
	
	 function validateForm($arr,$pkey = ''){
		     
		$arrayToJs = parent::validateForm($arr,$pkey); 
         
		$name = $arr['name'];    
		if(empty($name)) 
			$this->addErrorList($arrayToJs,false,$this->errorMsg['name'][1]);
         
		$email = $arr['email'];    
		if(empty($email)) 
			$this->addErrorList($arrayToJs,false,$this->errorMsg['email'][1]);
	 
         return $arrayToJs;
         
	 }
     
        
    function normalizeParameter($arrParam, $trim = false){ 
                 
        
        $arrParam = $this->updateOthersLangValue($arrParam, $this->arrData); 
        $arrParam = parent::normalizeParameter($arrParam,true); 
          
        $this->setLog($arrParam,true);
         return $arrParam; 
    }
		
    
}

?>