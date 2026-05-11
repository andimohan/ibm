<?php

class SupplierCategory extends Category{
   
   function __construct(){

      parent::__construct();

      $this->tableName = 'supplier_category';
      $this->tableStatus = 'master_status';
      $this->securityObject = 'SupplierCategory';

      $this->arrData = array();
      $this->arrData['pkey'] = array('pkey');
      $this->arrData['code'] = array('code');
      $this->arrData['name'] = array('name');
      $this->arrData['statuskey'] = array('selStatus');

      // $this->arrLockedTable = array();
      // $defaultFieldName = 'categorykey';
      // array_push($this->arrLockedTable, array('table' => 'supplier', 'field' => $defaultFieldName));

      $this->newLoad = true;

      $this->arrSearchColumn = array ();
      array_push($this->arrSearchColumn, array('Kode', $this->tableName . '.code'));
      array_push($this->arrSearchColumn, array('Nama', $this->tableName . '.name'));
      array_push($this->arrSearchColumn, array('Status', $this->tableStatus . '.status'));

      $this->arrDataListAvailableColumn = array(); 
      array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 100));
      array_push($this->arrDataListAvailableColumn, array('code' => 'category','title' => 'category','dbfield' => 'name','default'=>true, 'width' => 200));
      array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));

      $this->includeClassDependencies(
         array(
            'Supplier.class.php'
         )
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
   
   function validateForm($arr, $pkey = '')
   {

      $arrayToJs = parent::validateForm($arr, $pkey);

      $name = $arr['name'];   
      $rs = $this->isValueExisted($pkey, 'name', $name);
      if (empty($name)) {
         $this->addErrorList($arrayToJs, false, $this->errorMsg['category'][1]);
      } 
      if (count($rs) <> 0) {
         // $this->addErrorList($arrayToJs, false, $this->errorMsg['category'][2]);
      }

      return $arrayToJs;
   }

   function normalizeParameter($arrParam, $trim = false){
      $arrParam = parent::normalizeParameter($arrParam,true); 
      
      return $arrParam; 
   }  



}

?>