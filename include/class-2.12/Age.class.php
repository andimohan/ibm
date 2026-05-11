<?php
class Age extends BaseClass
{

    function __construct()
    {

        parent::__construct();

        $this->tableName = 'age';
        $this->tableStatus = 'master_status';

        $this->securityObject = 'Age';


        $this->arrData = array();
        $this->arrData['pkey'] = array('pkey');
        $this->arrData['code'] = array('code');
        $this->arrData['name'] = array('name');
        $this->arrData['statuskey'] = array('selStatus');
        $this->arrData['trdesc'] = array('trDesc');


        $this->arrDataListAvailableColumn = array();
        array_push($this->arrDataListAvailableColumn, array('code' => 'code', 'title' => 'code', 'dbfield' => 'code', 'default' => true, 'width' => 70));
        array_push($this->arrDataListAvailableColumn, array('code' => 'name', 'title' => 'name', 'dbfield' => 'name', 'default' => true, 'width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'description', 'title' => 'description', 'dbfield' => 'trdesc', 'default' => true, 'width' => 300));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status', 'title' => 'status', 'dbfield' => 'statusname', 'default' => true, 'width' => 70));

        $this->arrSearchColumn = array();
        array_push($this->arrSearchColumn, array('Kode', $this->tableName . '.code'));
        array_push($this->arrSearchColumn, array('Nama', $this->tableName . '.name'));
        array_push($this->arrSearchColumn, array('Status', $this->tableStatus . '.status'));

        $this->includeClassDependencies(array(
        ));

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
					' . $this->tableName . '.statuskey = ' . $this->tableStatus . '.pkey 
                    
 		' . $this->criteria;

        return $sql;
    }

    function validateForm($arr, $pkey = '')
    {

        $arrayToJs = parent::validateForm($arr, $pkey);

        $ringSizeName = $arr['name'];

        $rs = $this->isValueExisted($pkey, 'name', $ringSizeName);
        if (empty($ringSizeName)) {
            $this->addErrorList($arrayToJs, false, $this->errorMsg['age'][1]);
        } else if (count($rs) <> 0) {
            $this->addErrorList($arrayToJs, false, $this->errorMsg['age'][2]);
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