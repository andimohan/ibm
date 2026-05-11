<?php

class GoodCorporateGovernmentCategory extends Category
{

   function __construct()
   {

      parent::__construct();

      $this->tableName      = 'good_corporate_government_category';
      $this->tableStatus    = 'master_status';
      $this->tableLangValue = 'good_corporate_government_category_lang';
      $this->securityObject = 'GoodCorporateGovernmentCategory';

      $arrDetails = array();
      array_push($arrDetails, array('dataset' => $this->arrDataLang, 'tableName' => $this->tableLangValue));

      $this->arrData = array();
      $this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails));
      $this->arrData['code'] = array('code');
      $this->arrData['parentkey'] = array('selCategory');
      $this->arrData['name']   = array('name');
      $this->arrData['statuskey']   = array('selStatus');
      $this->arrData['description']   = array('txtDetail','raw');

      $this->arrDataListAvailableColumn = array(); 
      array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 120));
      array_push($this->arrDataListAvailableColumn, array('code' => 'name','title' => 'name','dbfield' => 'name','default'=>true, 'width' => 150));
      array_push($this->arrDataListAvailableColumn, array('code' => 'parent','title' => 'parent','dbfield' => 'parentname','default'=>true, 'width' => 150));
      array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));

      $this->arrSearchColumn = array();
      array_push($this->arrSearchColumn, array('Kode', $this->tableName . '.code'));
      array_push($this->arrSearchColumn, array('Nama', $this->tableName . '.name'));

      $this->newLoad = true;

      $this->includeClassDependencies(
         array(
            'GoodCorporateGovernment.class.php'
         )
      );

      $this->overwriteConfig();
   }


   function validateDelete($id, $forceDelete = false)
   {

      $arrayToJs = array();

      $goodCorporateGovernment  = new GoodCorporateGovernment();
      $rsData = $goodCorporateGovernment->searchData($goodCorporateGovernment->tableName . '.categorykey', $id, true);

      if (!empty($rsData)) {
         $rs = $this->getDataRowById($id);
         $this->addErrorList($arrayToJs, false, '<strong>' . $rs[0]['name'] . '</strong>. ' . $this->errorMsg[900] . ' <strong>(' . $rsData[0]['code'] . ' - ' . $rsData[0]['title'] . ')</strong>.');
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