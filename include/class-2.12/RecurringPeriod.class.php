<?php

class RecurringPeriod extends BaseClass
{

   function __construct()
   {
      parent::__construct();

      $this->tableName = 'recurring_period';
      $this->tableRepeatPeriod = 'repeat_periode';
      $this->tableStatus = 'master_status';

      $this->securityObject = 'RecurringPeriod';

      $this->arrData = array();
      $this->arrData['pkey'] = array('pkey');
      $this->arrData['code'] = array('code');
      $this->arrData['date'] = array('date', 'date');
      $this->arrData['name'] = array('name');
      $this->arrData['numberofperiod'] = array('numberOfPeriod', 'number');
      $this->arrData['timeperiodkey'] = array('selTimePeriod');

      $this->arrData['statuskey'] = array('selStatus');

      $this->newLoad = true;

      $this->arrDataListAvailableColumn = array();
      array_push($this->arrDataListAvailableColumn, array('code' => 'code', 'title' => 'code', 'dbfield' => 'code', 'default' => true, 'width' => 150));
      array_push($this->arrDataListAvailableColumn, array('code' => 'name', 'title' => 'name', 'dbfield' => 'name', 'default' => true, 'width' => 200));
      array_push($this->arrDataListAvailableColumn, array('code' => 'numberOfPeriod', 'title' => 'numberOfPeriod', 'dbfield' => 'numberofperiod', 'default' => true, 'align' => 'right', 'width' => 150));
      array_push($this->arrDataListAvailableColumn, array('code' => 'timePeriod', 'title' => 'timePeriod', 'dbfield' => 'repeatperiodname', 'default' => true, 'width' => 150));
      array_push($this->arrDataListAvailableColumn, array('code' => 'status', 'title' => 'status', 'dbfield' => 'statusname', 'default' => true, 'width' => 150));

      $this->arrSearchColumn = array();
      array_push($this->arrSearchColumn, array('Kode', $this->tableName . '.code'));
      array_push($this->arrSearchColumn, array('Nama', $this->tableName . '.name'));
      array_push($this->arrSearchColumn, array('Jumlah Periode', $this->tableName . '.numberofperiod'));
      array_push($this->arrSearchColumn, array('Waktu Periode',   $this->tableRepeatPeriod . '.name'));
      array_push($this->arrSearchColumn, array('Status', $this->tableStatus . '.status'));

      $this->includeClassDependencies(
         array(
         )
      );

      $this->overwriteConfig();

   }

   function getQuery()
   {
      $sql = '
         SELECT
            '. $this->tableName .'.*,
            '. $this->tableStatus .'.status as statusname,
            '. $this->tableRepeatPeriod .'.name as repeatperiodname,
            CONCAT('. $this->tableName .'.numberofperiod, " ", ' .$this->tableRepeatPeriod.'.name) as periodname
         FROM
            '. $this->tableStatus .',
            '. $this->tableName .'
                  left join '. $this->tableRepeatPeriod .' on '. $this->tableName .'.timeperiodkey = '. $this->tableRepeatPeriod .'.pkey
         WHERE
            '. $this->tableName .'.statuskey = '. $this->tableStatus .'.pkey
      ';

      $sql .= $this->criteria;   

      return $sql;
   }

   function validateForm($arr, $pkey = '') {

      $arrayToJs = parent::validateForm($arr, $pkey);

      $name = $arr['name'];
      $numberOfPeriod = $arr['numberOfPeriod'];
      $timePeriodKey = $arr['selTimePeriod'];
 

		$rsItem = $this->isValueExisted($pkey,'name',$name);	 
		if(empty($name)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['recurringPeriod'][1]);
		}else if(count($rsItem) <> 0){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['recurringPeriod'][2]);
		}

      if($numberOfPeriod <= 0 )  
         $this->addErrorList($arrayToJs, false, $this->errorMsg['recurringPeriod'][4]); 
 
      return $arrayToJs;

   }

   function normalizeParameter($arrParam, $trim = false)
   {
   
      $arrParam = parent::normalizeParameter($arrParam, true);
      return $arrParam;

   } 

}

?>