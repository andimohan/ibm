<?php
class AssetCategory extends BaseClass
{

	function __construct()
	{

		parent::__construct();
		$this->tableName = 'asset_category';
		$this->tableAssetType = 'asset_type';
		$this->tableStatus = 'master_status';
		$this->newLoad = true;
 
		$this->securityObject = 'AssetCategory';

		// join date detail di nonaktifkan, cuma bisa update dr front end

		$this->arrData = array();
		$this->arrData['pkey'] = array('pkey');
		$this->arrData['code'] = array('code');
		$this->arrData['name'] = array('name');
		$this->arrData['statuskey'] = array('selStatus');
		$this->arrData['typekey'] = array('selAssetType');
		$this->arrData['coaassetkey'] = array('hidCOAAssetKey');
		$this->arrData['coadepreciationkey'] = array('hidCOADepreciationKey');
		$this->arrData['coaaccumulatedkey'] = array('hidCOAAccumulatedKey');
		$this->arrData['aging'] = array('aging', 'number');
		
		$this->arrDataListAvailableColumn = array();
		array_push($this->arrDataListAvailableColumn, array('code' => 'code', 'title' => 'code', 'dbfield' => 'code', 'default' => true, 'width' => 70));
		array_push($this->arrDataListAvailableColumn, array('code' => 'name', 'title' => 'name', 'dbfield' => 'name', 'default' => true, 'width' => 200));
		array_push($this->arrDataListAvailableColumn, array('code' => 'typename', 'title' => 'type', 'dbfield' => 'typename', 'default' => true, 'width' => 150));
		array_push($this->arrDataListAvailableColumn, array('code' => 'aging', 'title' => 'usefulLife', 'dbfield' => 'aging', 'default' => true, 'width' => 90,'align'=> 'right', 'format' => 'number'));
		array_push($this->arrDataListAvailableColumn, array('code' => 'status', 'title' => 'status', 'dbfield' => 'statusname', 'default' => true, 'width' => 70));


		$this->arrSearchColumn = array();
		array_push($this->arrSearchColumn, array('Kode', $this->tableName . '.code'));
		array_push($this->arrSearchColumn, array('Nama', $this->tableName . '.name'));
		array_push($this->arrSearchColumn, array('Jenis', $this->tableAssetType . '.name'));

		$this->includeClassDependencies(array('Category.class.php'));

		$this->overwriteConfig();
	}

	function getQuery()
	{

		$sql = '
                 select
                     ' . $this->tableName . '.*, 
					 ' . $this->tableAssetType . '.name as typename,
                     ' . $this->tableStatus . '.status as statusname 
                 from 
                     ' . $this->tableName . ',
					 ' . $this->tableStatus . ', 
					 ' . $this->tableAssetType . ' 
                 where  		
                     ' . $this->tableName . '.statuskey = ' . $this->tableStatus . '.pkey and
                     ' . $this->tableName . '.typekey = ' . $this->tableAssetType . '.pkey';

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

		if (empty($arr['code']))
			$this->addErrorList($arrayToJs, false, $this->errorMsg['code'][1]);
		return $arrayToJs;
	}

	function normalizeParameter($arrParam, $trim = false)
	{
		$arrParam = parent::normalizeParameter($arrParam, true);
		return $arrParam;
	}
	 
}