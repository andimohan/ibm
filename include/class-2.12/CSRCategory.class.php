<?php

class CSRCategory extends Category{ 
 
   function __construct(){
		
		parent::__construct();
       
		$this->tableName = 'csr_category';  
		$this->tableImage = 'csr_category_image';
	    $this->tableLangValue = 'csr_category_lang';
		$this->securityObject = 'CSRCategory';  
		$this->uploadFolder = 'csr-category/';
		$this->uploadFileFolder = 'csr-category-file/';
		
        $this->arrLockedTable = array();
        $defaultFieldName = 'categorykey'; 
        array_push($this->arrLockedTable, array('table'=>'csr','field'=>$defaultFieldName)); 
         
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
        $this->arrData['name'] = array('name');
        $this->arrData['orderlist'] = array('orderList', 'number');
        $this->arrData['parentkey'] = array('selCategory');
        $this->arrData['isleaf'] = array('isLeaf'); 
//        $this->arrData['file'] = array('fileName');
        $this->arrData['filedownload'] = array('item-file-uploader',array('datatype' => 'file', 'uploadFolder' => $this->uploadFileFolder,  'token' => 'token-item-file-uploader', 'fileName' => 'item-file-uploader'));
      
        $this->arrData['statuskey'] = array('selStatus');
        $this->arrData['shortdescription'] = array('trShortDesc'); 
        $this->arrData['isshow'] = array('chkIsShow'); 
        $this->arrData['featured'] = array('chkIsFeatured'); 
        $this->arrData['orderlist'] = array('orderList'); 
        $this->arrData['description'] = array('txtDescription','raw'); 
       
        $this->newLoad = true;
        $this->includeClassDependencies(array( 
            'CSR.class.php'
        ));
   }
     
 
	function validateDelete($id,$forceDelete = false){
		    
		$arrayToJs = array();
		
		$csr = new CSR();
		$rsData = $csr->searchData($news->tableName.'.categorykey', $id,true); 
				
		if(!empty($rsData)){
			$rs = $this->getDataRowById($id);
			$this->addErrorList($arrayToJs,false,'<strong>'.$rs[0]['name']. '</strong>. '. $this->errorMsg[900] .' <strong>(' . $rsData[0]['code'] . ' - ' . $rsData[0]['title'] . ')</strong>.');
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