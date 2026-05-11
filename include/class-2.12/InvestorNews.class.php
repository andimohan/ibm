<?php

class InvestorNews extends BaseClass{

   function __construct()
   {

      parent::__construct();

      $this->tableName      = 'investor_news';
      $this->tableCategory  = 'investor_news_category';
      $this->securityObject = 'InvestorNews';
      $this->tableLangValue = 'investor_news_lang';
      $this->tableStatus    = 'master_status';
      $this->uploadFolder   = 'investor-news/';


      $arrDetails = array();
      array_push($arrDetails, array('dataset' => $this->arrDataLang, 'tableName' => $this->tableLangValue));

      $this->arrData                = array();
      $this->arrData['pkey']        = array('pkey', array('dataDetail' => $arrDetails));
      $this->arrData['code']        = array('code');
      $this->arrData['title']       = array('title');
      $this->arrData['categorykey'] = array('hidCategoryKey');
      $this->arrData['shortdesc']   = array('txtShortDescription');
      $this->arrData['publishdate'] = array('publishDate', 'date');
      $this->arrData['detail']      = array('txtDetail', 'raw'); 
      $this->arrData['image']           = array('item-image-uploader', array('datatype' => 'image', 'uploadFolder' => $this->uploadFolder, 'token' => 'token-item-image-uploader', 'fileName' => 'item-image-uploader'));
      $this->arrData['statuskey']       = array('selStatus');
      $this->arrData['featured']        = array('isFeatured');
      $this->arrData['tag']             = array('tag');
      $this->arrData['metatag']         = array('metaTag');
      $this->arrData['metatitle']       = array('metaTitle');
      $this->arrData['metadescription'] = array('metaDescription');
      $this->arrData['metakeyword']     = array('metaKeyword');

      $this->arrSearchColumn = array();
      array_push($this->arrSearchColumn, array('Kode', $this->tableName . '.code'));
      array_push($this->arrSearchColumn, array('Judul', $this->tableName . '.title'));
      array_push($this->arrSearchColumn, array('Kategori', $this->tableCategory . '.name'));
      array_push($this->arrSearchColumn, array('Deskripsi', $this->tableName . '.shortdesc'));


      $this->arrDataListAvailableColumn = array();
      array_push($this->arrDataListAvailableColumn, array('code' => 'code', 'title' => 'code', 'dbfield' => 'code', 'default' => true, 'width' => 100));
      array_push($this->arrDataListAvailableColumn, array('code' => 'name', 'title' => 'title', 'dbfield' => 'title', 'default' => true, 'width' => 150));
      array_push($this->arrDataListAvailableColumn, array('code' => 'category', 'title' => 'category', 'dbfield' => 'categoryname', 'default' => true, 'width' => 150));
      array_push($this->arrDataListAvailableColumn, array('code' => 'publishDate', 'title' => 'publishDate', 'dbfield' => 'publishdate', 'default' => true, 'width' => 100, 'align' => 'center', 'format' => 'date'));
      array_push($this->arrDataListAvailableColumn, array('code' => 'status', 'title' => 'status', 'dbfield' => 'statusname', 'default' => true, 'width' => 70));




      $this->newLoad = true;

      $this->includeClassDependencies(
         array(
            'Category.class.php',
            'InvestorNewsCategory.class.php'
         )
      );


   }


   function getQuery()
   {

      return '
				select
					' . $this->tableName . '.*,
					' . $this->tableStatus . '.status as statusname , 
					' . $this->tableCategory . '.name as categoryname		
				from 
					' . $this->tableName . ' left join ' . $this->tableCategory . ' on ' . $this->tableName . '.categorykey = ' . $this->tableCategory . '.pkey, ' . $this->tableStatus . ' 
				where  		 
					' . $this->tableName . '.statuskey = ' . $this->tableStatus . '.pkey 
 		' . $this->criteria;

   }


   function validateForm($arr, $pkey = '')
   {

      $arrayToJs = parent::validateForm($arr, $pkey);
      $name      = $arr['title'];


      $arrImage = explode(",", $arr['item-image-uploader']);
      for ($i = 0; $i < count($arrImage); $i++) {
         $path = $this->uploadTempDoc . $this->uploadFolder . $arr['token-item-image-uploader'];
         if (filesize($path . '/' . $arrImage[$i]) > (pow(1024, 2) * PLAN_TYPE['maximagesize']))
            $this->addErrorList($arrayToJs, false, $this->errorMsg['limit'][4] . ' (' . $this->lang['max'] . ' ' . $this->formatNumber(PLAN_TYPE['maximagesize']) . ' MB)');
      }

      $rs = $this->isValueExisted($pkey, 'title', $name);
      if (empty($name)) {
         $this->addErrorList($arrayToJs, false, $this->errorMsg['news'][1]);
      } else if (count($rs) <> 0) {
         $this->addErrorList($arrayToJs, false, $this->errorMsg['news'][2]);
      }

      return $arrayToJs;
   }



   function normalizeParameter($arrParam, $trim = false)
   {

      $arrParam = $this->updateOthersLangValue($arrParam, $this->arrData);
      $arrParam = parent::normalizeParameter($arrParam, true);

      return $arrParam;
   }

}

?>