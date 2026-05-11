<?php

class NewsCategory extends Category{ 
 
   function __construct(){
		
		parent::__construct();
       
		$this->tableName = 'news_category';  
		$this->securityObject = 'NewsCategory';  
	    $this->tableLangValue = 'news_category_lang';
		
        $this->arrLockedTable = array();
        $defaultFieldName = 'categorykey'; 
        array_push($this->arrLockedTable, array('table'=>'news','field'=>$defaultFieldName)); 
         
       
        $arrDetails = array();  
        array_push($arrDetails, array('dataset' => $this->arrDataLang, 'tableName' => $this->tableLangValue));
       
              
        $this->arrData = array();  
        $this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails)); 
        $this->arrData['code'] = array('code'); 
        $this->arrData['name'] = array('name');
        $this->arrData['orderlist'] = array('orderList', 'number');
        $this->arrData['parentkey'] = array('selCategory');
        $this->arrData['isleaf'] = array('isLeaf'); 
        $this->arrData['file'] = array('fileName');
        $this->arrData['statuskey'] = array('selStatus');
        $this->arrData['shortdescription'] = array('trShortDesc'); 
        $this->arrData['isshow'] = array('chkIsShow'); 
        $this->arrData['featured'] = array('chkIsFeatured'); 
        $this->arrData['description'] = array('txtDescription','raw'); 
         
       $this->newLoad=true;
       
        $this->includeClassDependencies(array( 
            'News.class.php'
        ));
   }
     
 
	function validateDelete($id,$forceDelete = false){
		    
		$arrayToJs = array();
		
		$news = new News();
		$rsData = $news->searchData($news->tableName.'.categorykey', $id,true); 
				
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