<?php 

class CustomerNews extends BaseClass{

   function __construct()
   {

      parent::__construct();

      $this->tableName      = 'customer_news';
      $this->securityObject = 'CustomerNews';
      $this->tableStatus    = 'master_status';
      $this->uploadFolder   = 'news/';

      $this->arrData                = array();
      $this->arrData['pkey']        = array('pkey');
      $this->arrData['code']        = array('code');
      $this->arrData['title']       = array('title');
      $this->arrData['shortdesc']   = array('txtShortDescription');
      $this->arrData['publishdate'] = array('publishDate', 'date');
      $this->arrData['detail']      = array('txtDetail', 'raw');
      $this->arrData['featured']    = array('isFeatured');
//      $this->arrData['file']        = array('fileName');
      $this->arrData['statuskey']   = array('selStatus');
      $this->arrData['tag']         = array('tag');

      $this->newLoad = true;

      $this->arrSearchColumn = array();
      array_push($this->arrSearchColumn, array('Kode', $this->tableName . '.code'));
      array_push($this->arrSearchColumn, array('Judul', $this->tableName . '.title'));
      array_push($this->arrSearchColumn, array('Status', $this->tableStatus . '.status'));
      array_push($this->arrSearchColumn, array('Deskripsi', $this->tableName . '.shortdesc'));

      $this->arrDataListAvailableColumn = array();
      array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 150));
      array_push($this->arrDataListAvailableColumn, array('code' => 'title', 'title' => 'title', 'dbfield' => 'title', 'default' => true, 'width' => 150));
      array_push($this->arrDataListAvailableColumn, array('code' => 'date','title' => 'publishDate','dbfield' => 'publishdate','default'=>true, 'width' => 150, 'align'=>'center', 'format' => 'date'));
      array_push($this->arrDataListAvailableColumn, array('code' => 'status', 'title' => 'status', 'dbfield' => 'statusname', 'default' => true, 'width' => 100));


   }

   function getQuery()
   {

      return '
				select
					' . $this->tableName . '.*,
					' . $this->tableStatus . '.status as statusname	
				from 
					' . $this->tableName . ',
               ' . $this->tableStatus . ' 
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
         $this->addErrorList($arrayToJs, false, $this->errorMsg['title'][1]);
      } else if (count($rs) <> 0) {
         $this->addErrorList($arrayToJs, false, $this->errorMsg['title'][2]);
      }

      return $arrayToJs;
   }

   function normalizeParameter($arrParam, $trim = false)
   {
 
      // $arrParam = $this->updateOthersLangValue($arrParam, $this->arrData);
      $arrParam = parent::normalizeParameter($arrParam, true);

      return $arrParam;
   }

}

?>