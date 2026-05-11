<?php 

class GPS extends BaseClass {

   function __construct() {

      parent::__construct();
	   
      $this->tableName = 'gps';
      $this->tableStatus = 'master_status';

      $this->securityObject = 'gps';
      $this->newLoad = true;

      $this->arrData = array();
      $this->arrData['pkey'] = array('pkey');
      $this->arrData['code'] = array('code');
      $this->arrData['name'] = array('name');
      $this->arrData['username'] = array('username');
      $this->arrData['password'] = array('password');
      $this->arrData['statuskey'] = array('selStatus');

      $this->arrDataListAvailableColumn = array();
      array_push($this->arrDataListAvailableColumn, array('code' => 'code', 'title' => 'code', 'dbfield' => 'code', 'default' => true, 'width' => 70));
      array_push($this->arrDataListAvailableColumn, array('code' => 'name', 'title' => 'name', 'dbfield' => 'name', 'default' => true, 'width' => 200));
      array_push($this->arrDataListAvailableColumn, array('code' => 'status', 'title' => 'status', 'dbfield' => 'statusname', 'default' => true, 'width' => 70));


      $this->arrSearchColumn = array();
      array_push($this->arrSearchColumn, array('Kode', $this->tableName . '.code'));
      array_push($this->arrSearchColumn, array('Nama', $this->tableName . '.name'));
      array_push($this->arrSearchColumn, array('Kategori', $this->tableStatus . '.status'));

      $this->includeClassDependencies(array(
         'Car.class.php'
      ));

      $this->overwriteConfig();

   }

   function getQuery()
   {

      return '
			select
					' . $this->tableName . '.*,
					' . $this->tableStatus . '.status as statusname
				from
					' . $this->tableName . ',' . $this->tableStatus . ' where
					' . $this->tableName . '.statuskey = ' . $this->tableStatus . '.pkey 
 		' . $this->criteria;

   }

   function validateForm($arr, $pkey = '')
   {
      $arrayToJs = parent::validateForm($arr, $pkey);

      $name = $arr['name'];

      $rs = $this->isValueExisted($pkey, 'name', $name);
      if (empty($name)) {
         $this->addErrorList($arrayToJs, false, $this->errorMsg['name'][1]);
      } else if (count($rs) <> 0) {
         $this->addErrorList($arrayToJs, false, $this->errorMsg['name'][2]);
      }

      return $arrayToJs;
   }

   function validateDelete($id, $forceDelete = false)
   {

      $arrayToJs = array();

      $car = new Car();
      $rsData = $car->searchData($car->tableName . '.gpskey', $id, true);

      if (!empty($rsData)) {
         $rs = $this->getDataRowById($id);
         $this->addErrorList($arrayToJs, false, '<strong>' . $rs[0]['name'] . '</strong>. ' . $this->errorMsg[900] . ' <strong>(' . $rsData[0]['code'] . ' - ' . $rsData[0]['policenumber'] . ')</strong>.');
      }

      return $arrayToJs;
   }

   function normalizeParameter($arrParam, $trim = false)
   {

      $arrParam = parent::normalizeParameter($arrParam, true);
      return $arrParam;
   }

}

?>