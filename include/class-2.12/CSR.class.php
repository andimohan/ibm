<?php

class CSR extends BaseClass{
   
   function __construct(){
		
		parent::__construct();
       
		$this->tableName = 'csr'; 
		$this->tableCategory = 'csr_category';  
		$this->tableImage = 'csr_image';
		$this->securityObject = 'CSR'; 
	    $this->tableLangValue = 'csr_lang';
		$this->tableStatus = 'master_status';
		$this->uploadFolder = 'csr/'; 
        $this->uploadFileFolder = 'csr-file/';
		
        $this->arrDataImageDetail = array();  
        $this->arrDataImageDetail['pkey'] = array('hidDetailitem-image-uploaderKey');
        $this->arrDataImageDetail['refkey'] = array('pkey','ref');
        $this->arrDataImageDetail['file'] = array('hidNameitem-image-uploader',array('datatype' => 'image', 'uploadFolder' => $this->uploadFolder,  'token' => 'token-item-image-uploader', 'fileName' => 'hidNameitem-image-uploader'));

       
        $arrDetails = array(); 
        array_push($arrDetails, array('dataset' => $this->arrDataLang, 'tableName' => $this->tableLangValue));
        array_push($arrDetails, array('dataset' => $this->arrDataImageDetail, 'tableName' => $this->tableImage));
       
        $this->arrData = array();  
        $this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails));
        $this->arrData['code'] = array('code');
        $this->arrData['title'] = array('title'); 
        $this->arrData['categorykey'] = array('hidCategoryKey');
        $this->arrData['shortdesc'] = array('txtShortDescription'); 
        $this->arrData['publishdate'] = array('publishDate','date');
        $this->arrData['detail'] = array('txtDetail','raw');  
//        $this->arrData['file'] = array('fileName');
        $this->arrData['image'] = array('item-image-uploader',array('datatype' => 'image', 'uploadFolder' => $this->uploadFolder,  'token' => 'token-item-image-uploader', 'fileName' => 'item-image-uploader'));
//        $this->arrData['file'] = array('item-file-uploader',array('datatype' => 'file', 'uploadFolder' => $this->uploadFileFolder,  'token' => 'token-item-file-uploader', 'fileName' => 'item-file-uploader'));
        $this->arrData['statuskey'] = array('selStatus');
        $this->arrData['featured'] = array('isFeatured');
        $this->arrData['tag'] = array('tag');
        $this->arrData['metatag'] = array('metaTag');  
        $this->arrData['metatitle'] = array('metaTitle');  
        $this->arrData['metadescription'] = array('metaDescription');  
        $this->arrData['metakeyword'] = array('metaKeyword');
        
        $this->arrSearchColumn = array ();
        array_push($this->arrSearchColumn, array('Kode', $this->tableName . '.code'));
        array_push($this->arrSearchColumn, array('Judul', $this->tableName . '.title')); 
        array_push($this->arrSearchColumn, array('Kategori', $this->tableCategory. '.name')); 
        array_push($this->arrSearchColumn, array('Deskripsi', $this->tableName . '.shortdesc')); 
 
            
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'name','title' => 'title','dbfield' => 'title','default'=>true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'category','title' => 'category','dbfield' => 'categoryname','default'=>true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'publishDate','title' => 'publishDate','dbfield' => 'publishdate','default'=>true, 'width' => 100, 'format' => 'date'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
        

        $this->newLoad = true;
                            
        $this->includeClassDependencies(array( 
            'Category.class.php', 
            'NewsCategory.class.php'
        ));
 
       
   }
   
   
	 function getQuery(){
	   
	   return '
				select
					'.$this->tableName. '.*,
					'.$this->tableStatus.'.status as statusname , 
					'.$this->tableCategory.'.name as categoryname		
				from 
					'.$this->tableName . ' left join '.$this->tableCategory.' on '.$this->tableName . '.categorykey = '.$this->tableCategory.'.pkey, '.$this->tableStatus.' 
				where  		 
					'.$this->tableName . '.statuskey = '.$this->tableStatus.'.pkey 
 		' .$this->criteria ; 
		 
    }
	  
	
	 function validateForm($arr,$pkey = ''){ 
          
		$arrayToJs = parent::validateForm($arr,$pkey); 
		$name = $arr['title'];  
	 
          
        $arrImage = explode(",",$arr['item-image-uploader']);
        for($i=0;$i<count($arrImage);$i++){
            $path = $this->uploadTempDoc.$this->uploadFolder.$arr['token-item-image-uploader']; 
            if (filesize($path.'/'.$arrImage[$i]) > (pow(1024,2) * PLAN_TYPE['maximagesize']))
                $this->addErrorList($arrayToJs,false,$this->errorMsg['limit'][4] .' ('.$this->lang['max'].' '. $this->formatNumber(PLAN_TYPE['maximagesize']). ' MB)' );
        }
         
	 	$rs = $this->isValueExisted($pkey,'title',$name);	 
		if(empty($name)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['csr'][1]);
		}else if(count($rs) <> 0){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['csr'][2]);
		}
		  
		return $arrayToJs;
	 }

 
        
    function normalizeParameter($arrParam, $trim = false){ 
                  
        $arrParam = $this->updateOthersLangValue($arrParam, $this->arrData); 
        $arrParam = parent::normalizeParameter($arrParam,true); 
           
         return $arrParam; 
    }
	    
}

?>