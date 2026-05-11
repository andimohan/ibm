<?php
class AssetGroup extends BaseClass
{

	function __construct()
	{

		parent::__construct();
		$this->tableName = 'asset_group';
		$this->tableStatus = 'master_status';
		$this->newLoad = true;
 
		$this->securityObject = 'AssetGroup';

		// join date detail di nonaktifkan, cuma bisa update dr front end

		$this->arrData = array();
		$this->arrData['pkey'] = array('pkey');
		$this->arrData['code'] = array('code');
		$this->arrData['name'] = array('name');
		$this->arrData['statuskey'] = array('selStatus');
		
		$this->arrDataListAvailableColumn = array();
		array_push($this->arrDataListAvailableColumn, array('code' => 'code', 'title' => 'code', 'dbfield' => 'code', 'default' => true, 'width' => 70));
		array_push($this->arrDataListAvailableColumn, array('code' => 'name', 'title' => 'name', 'dbfield' => 'name', 'default' => true, 'width' => 200));
		array_push($this->arrDataListAvailableColumn, array('code' => 'status', 'title' => 'status', 'dbfield' => 'statusname', 'default' => true, 'width' => 70));


		$this->arrSearchColumn = array();
		array_push($this->arrSearchColumn, array('Kode', $this->tableName . '.code'));
		array_push($this->arrSearchColumn, array('Nama', $this->tableName . '.name'));

		$this->overwriteConfig();
	}

	function getQuery()
	{

		$sql = '
                 select
                     ' . $this->tableName . '.*, 
                     ' . $this->tableStatus . '.status as statusname 
                 from 
                     ' . $this->tableName . ',
					 ' . $this->tableStatus . '
                 where  		
                     ' . $this->tableName . '.statuskey = ' . $this->tableStatus . '.pkey';

		$sql .= $this->criteria;

		return $sql;
	}

	function validateForm($arr, $pkey = '')
	{
		$arrayToJs = parent::validateForm($arr, $pkey);
        
		if ($arr['aging'] < 0)
			$this->addErrorList($arrayToJs, false, $this->errorMsg['aging'][2]);

		if (empty($arr['name']))
			$this->addErrorList($arrayToJs, false, $this->errorMsg['name'][1]);
 
        
		return $arrayToJs;
	}

	function normalizeParameter($arrParam, $trim = false)
	{
		$arrParam = parent::normalizeParameter($arrParam, true);
		return $arrParam;
	}
	 
}