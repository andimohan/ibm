<?php

class GoodCorporateGovernment extends BaseClass
{

   function __construct()
   {

      parent::__construct();

      $this->tableName      = 'good_corporate_government_header';
      $this->tableCategory  = 'good_corporate_government_category';
      $this->tableGoodCorporateGovernmentReport = 'good_corporate_government_report';
      $this->tableGoodCorporateGovernmentReportDetail = 'good_corporate_government_report_detail';
      $this->tableGoodCorporateGovernmentTeam = 'good_corporate_government_team';
      $this->securityObject = 'GoodCorporateGovernment';
      $this->tableLangValue = 'good_corporate_government_header_lang';
      $this->managementTeam = 'management_team';
      $this->managementTeamStructure = 'management_structure';   
      $this->uploadFolder  = 'gcg/';
      $this->uploadFileFolder  = 'gcg-file/';
      $this->tableStatus    = 'master_status';
      $this->newLoad  = true;

      $this->arrDataDetail                 = array();
      $this->arrDataDetail['pkey']         = array('hidDetailKey');
      $this->arrDataDetail['refkey']       = array('pkey', 'ref');
      $this->arrDataDetail['refreportkey']         = array('hidReportKey'); ;


      $this->arrDataGoodCorporateGovernmentTeam = array();
      $this->arrDataGoodCorporateGovernmentTeam['pkey'] = array('hidGoodCorporateGovernmentTeamKey');
      $this->arrDataGoodCorporateGovernmentTeam['refkey'] = array('pkey', 'ref');
      $this->arrDataGoodCorporateGovernmentTeam['refteamkey'] = array('hidRefTeamKey');

      $arrDetails = array();
      array_push($arrDetails, array('dataset' => $this->arrDataLang, 'tableName' => $this->tableLangValue));
      array_push($arrDetails, array('dataset' => $this->arrDataDetail, 'tableName' => $this->tableGoodCorporateGovernmentReportDetail));
      array_push($arrDetails, array('dataset' => $this->arrDataGoodCorporateGovernmentTeam, 'tableName' => $this->tableGoodCorporateGovernmentTeam));

      $this->arrData                = array();
      $this->arrData['pkey']        = array('pkey', array('dataDetail' => $arrDetails));
      $this->arrData['code']        = array('code');
      $this->arrData['title']       = array('title');
      $this->arrData['categorykey'] = array('hidCategoryKey');
      $this->arrData['shortdesc']   = array('txtShortDescription');
      $this->arrData['description'] = array('txtDescription','raw');
      $this->arrData['statuskey']   = array('selStatus');
      $this->arrData['file'] = array('item-file-uploader',array('datatype' => 'file', 'uploadFolder' => $this->uploadFileFolder,  'token' => 'token-item-file-uploader', 'fileName' => 'item-file-uploader'));
      $this->arrData['image'] = array('item-image-uploader',array('datatype' => 'image', 'uploadFolder' => $this->uploadFolder,  'token' => 'token-item-image-uploader', 'fileName' => 'item-image-uploader'));


      $this->arrSearchColumn = array();
      array_push($this->arrSearchColumn, array('Kode', $this->tableName . '.code'));
      array_push($this->arrSearchColumn, array('Judul', $this->tableName . '.title'));
      array_push($this->arrSearchColumn, array('Kategori', $this->tableCategory . '.name'));
      array_push($this->arrSearchColumn, array('Deskripsi', $this->tableName . '.shortdesc'));


      $this->arrDataListAvailableColumn = array();
      array_push($this->arrDataListAvailableColumn, array('code' => 'code', 'title' => 'code', 'dbfield' => 'code', 'default' => true, 'width' => 100));
      array_push($this->arrDataListAvailableColumn, array('code' => 'name', 'title' => 'title', 'dbfield' => 'title', 'default' => true, 'width' => 150));
      array_push($this->arrDataListAvailableColumn, array('code' => 'category', 'title' => 'category', 'dbfield' => 'categoryname', 'default' => true, 'width' => 100));
      array_push($this->arrDataListAvailableColumn, array('code' => 'shortDesc', 'title' => 'shortDescription', 'dbfield' => 'shortdesc', 'default' => true, 'width' => 250));
      array_push($this->arrDataListAvailableColumn, array('code' => 'status', 'title' => 'status', 'dbfield' => 'statusname', 'default' => true, 'width' => 100));

      $this->includeClassDependencies(
         array(
            'GoodCorporateGovernmentCategory.class.php'
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

   function getGoodCorporateGovernmentReportDetail($pkey,$criteria = '')
   {
      $sql = 'select 
                    '.$this->tableGoodCorporateGovernmentReportDetail.'.*,
                    '.$this->tableGoodCorporateGovernmentReport.'.pkey as reportkey,
                    '.$this->tableGoodCorporateGovernmentReport.'.title as reporttitle,
                    '.$this->tableGoodCorporateGovernmentReport.'.image as reportimage,
                    '.$this->tableGoodCorporateGovernmentReport.'.file as reportfile
                from 
                    '.$this->tableGoodCorporateGovernmentReportDetail.',
                    '.$this->tableGoodCorporateGovernmentReport.'
                where
                    '.$this->tableGoodCorporateGovernmentReportDetail.'.refkey = '.$this->oDbCon->paramString($pkey).' and 
                    '.$this->tableGoodCorporateGovernmentReportDetail.'.refreportkey =  '.$this->tableGoodCorporateGovernmentReport.'.pkey ';
      
       if(!empty($criteria)) $sql .= ' ' . $criteria;
       
      return $this->oDbCon->doQuery($sql);
   }
 

   function getGoodCorporateGovernmentTeam($pkey)
   {
      $sql = 'select 
                '.$this->tableGoodCorporateGovernmentTeam.'.pkey as teamkey,
                '.$this->tableGoodCorporateGovernmentTeam.'.refteamkey,
                '.$this->managementTeam.'.*,
                '.$this->managementTeamStructure.'.name as structurename
              from 
                '.$this->tableGoodCorporateGovernmentTeam.',
                '.$this->managementTeam.' ,
                '.$this->managementTeamStructure.' 
            where
                '.$this->tableGoodCorporateGovernmentTeam . '.refteamkey = '.$this->managementTeam.'.pkey and
                '.$this->managementTeamStructure . '.pkey = '.$this->managementTeam.'.structurekey and
                '.$this->tableGoodCorporateGovernmentTeam . '.refkey = ' . $this->oDbCon->paramString($pkey) . ' ';

      return $this->oDbCon->doQuery($sql);
   }


   function validateForm($arr, $pkey = '') {

      $arrayToJs = parent::validateForm($arr, $pkey);
      $name      = $arr['title'];

      $rs = $this->isValueExisted($pkey, 'title', $name);
      if (empty($name)) {
         $this->addErrorList($arrayToJs, false, $this->errorMsg['title'][1]);
      } else if (count($rs) <> 0) {
         $this->addErrorList($arrayToJs, false, $this->errorMsg['title'][2]);
      }

      return $arrayToJs;
   }



   function normalizeParameter($arrParam, $trim = false) {

      $arrParam = $this->updateOthersLangValue($arrParam, $this->arrData);
      $arrParam = parent::normalizeParameter($arrParam, true);

      return $arrParam;
   }

}

?>