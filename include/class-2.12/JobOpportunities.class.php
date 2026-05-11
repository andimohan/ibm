<?php

class JobOpportunities extends BaseClass{
   
   function __construct(){
		
		parent::__construct();
       
		$this->tableName = 'job_opportunities'; 
	    $this->tableLangValue = 'job_opportunities_lang';
//		$this->tableCategory = 'career_category'; 
//		$this->tableJobField = 'career_field'; 
		$this->tableCity = 'city'; 
		$this->tableDepartment = 'career_department'; 
		$this->tableExperience = 'job_experience'; 
		$this->tableCityCategory = 'city_category'; 
		$this->securityObject = 'JobOpportunities'; 
        $this->tableFile = 'job_opportunities_file'; 
		$this->tableStatus = 'master_status';
        $this->uploadFolder = 'job-opportunities-image/';
        $this->uploadFileFolder = 'job-opportunities-file/';
       

        $this->arrLockedTable = array();
        $defaultFieldName = 'jobkey';
        array_push($this->arrLockedTable, array('table'=>'recruitment','field'=>$defaultFieldName)); 
       
        $arrDetails = array();
        array_push($arrDetails, array('dataset' => $this->arrDataLang, 'tableName' => $this->tableLangValue));
       
        $this->arrData = array();  
        $this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails));
        $this->arrData['code'] = array('code');
        $this->arrData['title'] = array('title');
        $this->arrData['experiencekey'] = array('selExperience');
        $this->arrData['departmentkey'] = array('selDepartment');
        $this->arrData['citykey'] = array('hidCityKey');
        $this->arrData['shortdesc'] = array('trDesc');  
        $this->arrData['isfeatured'] = array('chkIsFeatured');  
        $this->arrData['jobdesc'] = array('jobDesc','raw');  
        $this->arrData['requirement'] = array('reqDesc','raw');    

        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'title','title' => 'title','dbfield' => 'title','default'=>true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'department','title' => 'department','dbfield' => 'departmentname','default'=>true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'experience','title' => 'jobExperience','dbfield' => 'experience','default'=>true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'city','title' => 'city','dbfield' => 'cityandcategoryname', 'width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
       

        $this->arrSearchColumn = array ();
        array_push($this->arrSearchColumn, array('Kode', $this->tableName . '.code'));  
        array_push($this->arrSearchColumn, array('name', $this->tableName . '.title'));  
        array_push($this->arrSearchColumn, array('Departement', $this->tableDepartment. '.name') );
        array_push($this->arrSearchColumn, array('Deskripsi', $this->tableName . '.shortdesc')); 


        $this->newLoad = true;
       
        $this->includeClassDependencies(array(
            'CityCategory.class.php',
            'City.class.php',
            'CareerDepartment.class.php',
            'JobExperience.class.php'
        )); 
           
           
        $this->overwriteConfig();
   }
   
   
	 function getQuery(){
	   
	   $sql= '
				select
					'.$this->tableName. '.*,
					'.$this->tableCity.'.name as cityname,   
                    concat ('.$this->tableCity. '.name, ", ", '.$this->tableCityCategory.'.name) as citycatname,
					'.$this->tableDepartment.'.name as departmentname,   
					'.$this->tableExperience.'.name as experience,   
					'.$this->tableStatus.'.status as statusname 
				from 
					'.$this->tableName . ' 
                        left join '.$this->tableCity.' on '.$this->tableName.'.citykey = '.$this->tableCity.'.pkey
				        left join '.$this->tableExperience.' on '.$this->tableName.'.experiencekey = '.$this->tableExperience.'.pkey
				        left join '.$this->tableDepartment.' on '.$this->tableName.'.departmentkey = '.$this->tableDepartment.'.pkey
				        left join '.$this->tableCityCategory.' on '.$this->tableCity . '.categorykey = '.$this->tableCityCategory.'.pkey, 
                    '.$this->tableStatus.' 
				where  		 
					'.$this->tableName . '.statuskey = '.$this->tableStatus.'.pkey  
 		' .$this->criteria ; 
         
//         $this->setLog($sql,true);
         return $sql;
		 
    }
	 
	
	 function validateForm($arr,$pkey = ''){
		     
		$arrayToJs = parent::validateForm($arr,$pkey); 
         
		$name = $arr['title'];  
         $categorykey = $arr['hidCategoryKey'];
         $jobfieldkey = $arr['hidJobFieldKey'];
         //$locationkey = $arr['hidLocationKey'];

		if(empty($name)) 
			$this->addErrorList($arrayToJs,false,$this->errorMsg['jobOpportunities'][1]);
	 
         return $arrayToJs;
         
	 }
     
        
    function normalizeParameter($arrParam, $trim = false){ 
                 
       
        $arrParam = $this->updateOthersLangValue($arrParam, $this->arrData); 
        $arrParam = parent::normalizeParameter($arrParam,true); 
          
         return $arrParam; 
    }
		
    
}

?>
