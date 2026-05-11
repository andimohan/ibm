<?php
class Pallet extends BaseClass
{

    function __construct()
    {

        parent::__construct();

        $this->tableName = 'pallet';;
        $this->tableStatus = 'master_status';

        $this->securityObject = 'Pallet';

        $this->arrData = array();
        $this->arrData['pkey'] = array('pkey');
        $this->arrData['code'] = array('code');
        $this->arrData['name'] = array('name');
        $this->arrData['statuskey'] = array('selStatus');
        $this->arrData['trdesc'] = array('trDesc');

        $this->arrLockedTable = array();
        $defaultFieldName = 'disposalkey';
        array_push($this->arrLockedTable, array('table' => 'disposal_work_order_detail', 'field' => $defaultFieldName));

        $this->arrDataListAvailableColumn = array();
        array_push($this->arrDataListAvailableColumn, array('code' => 'code', 'title' => 'code', 'dbfield' => 'code', 'default' => true, 'width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'name', 'title' => 'name', 'dbfield' => 'name', 'default' => true, 'width' => 250));
        array_push($this->arrDataListAvailableColumn, array('code' => 'note', 'title' => 'note', 'dbfield' => 'trdesc', 'default' => true, 'width' => 300));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status', 'title' => 'status', 'dbfield' => 'statusname', 'default' => true, 'width' => 70));

        $this->includeClassDependencies(array());

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


    function generateDefaultQueryForAutoComplete($returnField)
    {

        $sql = 'select
                  ' . $returnField['key'] . ',
                  concat(' . $this->tableName . '.code' . '," - ",' . $returnField['value'] . ') as value 
              from 
                  ' . $this->tableName . ',
                  ' . $this->tableStatus . '
              where  		
                  ' . $this->tableName . '.statuskey = ' . $this->tableStatus . '.pkey
          ';
        return $sql;
    }

    function validateForm($arr, $pkey = '')
    {

        $arrayToJs = parent::validateForm($arr, $pkey);

        $name = $arr['name'];

        $rs = $this->isValueExisted($pkey, 'name', $name);
        if (empty($name)) {
            $this->addErrorList($arrayToJs, false, $this->errorMsg['pallet'][1]);
        } else {
            if (count($rs) <> 0) {
                $this->addErrorList($arrayToJs, false, $this->errorMsg['pallet'][2]);
            }
        }

        return $arrayToJs;
    }

    function normalizeParameter($arrParam, $trim = false)
    {

        $arrParam = parent::normalizeParameter($arrParam, true);
        return $arrParam;
    }
}
