<?php 

class NewRelease extends BaseClass
{

   function __construct()
   {
      parent::__construct();

      $this->tableName = 'new_release_header';
      $this->tableNameDetail = 'new_release_image_detail';
      $this->tableImage = 'new_release_image_detail';
      $this->tableStatus    = 'master_status';
	  $this->tableLangValue = 'subsidiaries_lang';
      $this->uploadFolder = 'new-release/';
      $this->uploadDetailFolder = 'new-release-detail/';

      $this->securityObject = 'NewRelease'; 
       
      $this->arrDataImageDetail = array();  
      $this->arrDataImageDetail['pkey'] = array('hidDetailitem-detail-image-uploaderKey');
      $this->arrDataImageDetail['refkey'] = array('pkey','ref');
      $this->arrDataImageDetail['file'] = array('hidNameitem-detail-image-uploader',array('datatype' => 'image', 'uploadFolder' => $this->uploadDetailFolder,  'token' => 'token-item-detail-image-uploader', 'fileName' => 'hidNameitem-detail-image-uploader'));


      $arrDetails = array(); 
      array_push($arrDetails, array('dataset' => $this->arrDataLang, 'tableName' => $this->tableLangValue));
      array_push($arrDetails, array('dataset' => $this->arrDataImageDetail, 'tableName' => $this->tableImage));
       
      $this->arrData                    = array();
      $this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails)); 
      $this->arrData['code']            = array('code');
      $this->arrData['trdate']          = array('trDate', 'date');
      $this->arrData['publishdate']     = array('trPublishDate', 'date');
      $this->arrData['orderlist']       = array('orderList');
      $this->arrData['title']           = array('title');
      $this->arrData['url']           = array('url');
      $this->arrData['shortdesc']       = array('txtShortDescription');
      $this->arrData['trdesc']       = array('txtDescription','raw');
      $this->arrData['image'] = array('cover-image-uploader',array('datatype' => 'image', 'uploadFolder' => $this->uploadFolder,  'token' => 'token-cover-image-uploader', 'fileName' => 'cover-image-uploader'));
      $this->arrData['statuskey']       = array('selStatus');

      $this->newLoad = true;


      $this->arrSearchColumn = array();
      array_push($this->arrSearchColumn, array('Kode', $this->tableName . '.code'));
      array_push($this->arrSearchColumn, array('Tanggal', $this->tableName . '.trdate'));
      array_push($this->arrSearchColumn, array('Tanggal Publish', $this->tableName. '.publishdate'));
      array_push($this->arrSearchColumn, array('Judul', $this->tableName . '.title'));
      array_push($this->arrSearchColumn, array('Status', $this->tableStatus . '.status'));

      $this->arrDataListAvailableColumn = array();
      array_push($this->arrDataListAvailableColumn, array('code' => 'code', 'title' => 'code', 'dbfield' => 'code', 'default' => true, 'width' => 120));
      array_push($this->arrDataListAvailableColumn, array('code' => 'date', 'title' => 'date', 'dbfield' => 'trdate', 'align' => 'center', 'format' => 'date', 'default' => true, 'width' => 150));
      array_push($this->arrDataListAvailableColumn, array('code' => 'publishdate', 'title' => 'publishDate', 'dbfield' => 'publishdate', 'align' => 'center', 'format' => 'date', 'default' => true, 'width' => 150));
      array_push($this->arrDataListAvailableColumn, array('code' => 'title', 'title' => 'title', 'dbfield' => 'title', 'default' => true, 'width' => 200));
      array_push($this->arrDataListAvailableColumn, array('code' => 'shortdesc', 'title' => 'shortDescription', 'dbfield' => 'shortdesc', 'default' => true, 'width' => 400));
      array_push($this->arrDataListAvailableColumn, array('code' => 'status', 'title' => 'status', 'dbfield' => 'statusname', 'default' => true, 'width' => 100));

      $this->includeClassDependencies(
         array()
      );

      $this->overwriteConfig();
   
   }

   function getQuery()
   {

      return '
				select
					' . $this->tableName . '.*,
					' . $this->tableStatus . '.status as statusname
				from 
					' . $this->tableName .',
               '. $this->tableStatus . ' 
				where  		 
					' . $this->tableName . '.statuskey = ' . $this->tableStatus . '.pkey 
 		' . $this->criteria;

   }

   function getDetailWithRelatedInformation($pkey, $criteria = '')
   {
      
   }

   function validateForm($arr, $pkey = '')
   {

      $arrayToJs = parent::validateForm($arr, $pkey);

      $name = $arr['title'];
 
//         nanti saja karena ad detail 
//        $arrImage = explode(",",$arr['item-image-uploader']);
//        for($i=0;$i<count($arrImage);$i++){
//            $path = $this->uploadTempDoc.$this->uploadFolder.$arr['token-item-image-uploader']; 
//            if (filesize($path.'/'.$arrImage[$i]) > (pow(1024,2) * PLAN_TYPE['maximagesize']))
//                $this->addErrorList($arrayToJs,false,$this->errorMsg['limit'][4] .' ('.$this->lang['max'].' '. $this->formatNumber(PLAN_TYPE['maximagesize']). ' MB)' );
//        }
//         
	 	$rs = $this->isValueExisted($pkey,'title',$name);	 
		if(empty($name)){
			$this->addErrorList($arrayToJs,false, $this->errorMsg['title'][1]);
		}else if(count($rs) <> 0){
			$this->addErrorList($arrayToJs,false, $this->errorMsg['title'][2]);
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