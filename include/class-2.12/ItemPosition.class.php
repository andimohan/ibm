<?php
class ItemPosition extends BaseClass
{

    function __construct()
    {

        parent::__construct();

        $this->tableName = 'item_position';
        $this->securityObject = 'ItemPosition';
        $this->tableStatus = 'master_status';


        $this->arrData = array();
        $this->arrData['pkey'] = array('pkey');
        $this->arrData['code'] = array('code');
        $this->arrData['name'] = array('name');
        $this->arrData['statuskey'] = array('selStatus');


        $this->arrDataListAvailableColumn = array();
        array_push($this->arrDataListAvailableColumn, array('code' => 'code', 'title' => 'code', 'dbfield' => 'code', 'default' => true, 'width' => 70));
        array_push($this->arrDataListAvailableColumn, array('code' => 'name', 'title' => 'name', 'dbfield' => 'name', 'default' => true, 'width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status', 'title' => 'status', 'dbfield' => 'statusname', 'default' => true, 'width' => 70));

        $this->overwriteConfig();

    }

    function getQuery()
    {

        return '
				select
					' . $this->tableName . '.*,
					' . $this->tableStatus . '.status as statusname
				from 
					' . $this->tableName . ',' . $this->tableStatus . '	
				where  		
					' . $this->tableName . '.statuskey = ' . $this->tableStatus . '.pkey  
 		' . $this->criteria;

    }

    function validateForm($arr, $pkey = '')
    {

        $arrayToJs = parent::validateForm($arr, $pkey);

        $name = $arr['name'];

        $rsItem = $this->isValueExisted($pkey, 'name', $name);
        if (empty($name)) {
            $this->addErrorList($arrayToJs, false, $this->errorMsg['itemUnit'][1]);
        } else if (count($rsItem) <> 0) {
            $this->addErrorList($arrayToJs, false, $this->errorMsg['itemUnit'][2]);
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