<?php
class Commodity extends BaseClass
{

    function __construct()
    {

        parent::__construct();

        $this->tableName = 'commodity';
        $this->tableCommodityType = 'commodity_type';
        $this->tableStatus = 'master_status';

        $this->newLoad = true;
        $this->uploadFileFolder = 'commodity/';

        $this->securityObject = 'Commodity';

        $this->arrData = array();
        $this->arrData['pkey'] = array('pkey');
        $this->arrData['code'] = array('code');
        $this->arrData['name'] = array('name');
        $this->arrData['typekey'] = array('selType');
        ;
        $this->arrData['statuskey'] = array('selStatus');
        ;

        $this->arrLockedTable = array();
        $defaultFieldName = 'commoditykey';
        array_push($this->arrLockedTable, array('table' => 'emkl_quotation_order_commodity_detail', 'field' => $defaultFieldName));

        $this->arrDataListAvailableColumn = array();
        array_push($this->arrDataListAvailableColumn, array('code' => 'code', 'title' => 'code', 'dbfield' => 'code', 'default' => true, 'width' => 70));
        array_push($this->arrDataListAvailableColumn, array('code' => 'name', 'title' => 'name', 'dbfield' => 'name', 'default' => true, 'width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'type', 'title' => 'commodityType', 'dbfield' => 'typename', 'default' => true, 'width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status', 'title' => 'status', 'dbfield' => 'statusname', 'default' => true, 'width' => 70));


        $this->arrSearchColumn = array();
        array_push($this->arrSearchColumn, array('Kode', $this->tableName . '.code'));
        array_push($this->arrSearchColumn, array('Nama', $this->tableName . '.name'));
        array_push($this->arrSearchColumn, array('Type', $this->tableCommodityType . '.name'));

        $this->importUrl = 'import/commodity';

        $this->includeClassDependencies(array(
            'CommodityType.class.php'
        ));

        $this->overwriteConfig();
    }



    function getQuery()
    {

        return '
				select
					' . $this->tableName . '.*,
					' . $this->tableStatus . '.status as statusname,
					' . $this->tableCommodityType . '.name as typename
				from 
					' . $this->tableName . '
					left join ' . $this->tableCommodityType . ' on  ' . $this->tableName . '.typekey = ' . $this->tableCommodityType . '.pkey,
					' . $this->tableStatus . '
				where  		
					' . $this->tableName . '.statuskey = ' . $this->tableStatus . '.pkey
 		' . $this->criteria;

    }


    function getCommodityType($pkey = '')
    {

        $sql = 'select
	   			' . $this->tableCommodityType . '.pkey, 
	   			' . $this->tableCommodityType . '.name 
              from
			  	' . $this->tableCommodityType . ' 
			  where
			  	' . $this->tableCommodityType . '.statuskey = 1';
        if (!empty($pkey))
            $sql .= ' and pkey = ' . $this->oDbCon->paramString($pkey);

        $sql .= ' order by name asc';

        return $this->oDbCon->doQuery($sql);

    }


    function validateForm($arr, $pkey = '')
    {

        $arrayToJs = parent::validateForm($arr, $pkey);

        $commodityname = $arr['name'];

        $rs = $this->isValueExisted($pkey, 'name', $commodityname);
        if (empty($commodityname)) {
            $this->addErrorList($arrayToJs, false, $this->errorMsg['name'][1]);
        } else if (count($rs) <> 0) {
            $this->addErrorList($arrayToJs, false, $this->errorMsg['name'][2]);
        }


        return $arrayToJs;
    }

    function generateDefaultQueryForAutoComplete($returnField)
    {

        $sql = 'select
					' . $returnField['key'] . ',
                    ' . $returnField['value'] . ' as value 
				from 
					' . $this->tableName . ',' . $this->tableStatus . ' 
				where  		
					' . $this->tableName . '.statuskey = ' . $this->tableStatus . '.pkey   
			';


        return $sql;

    }


    //oveerwrite
    function normalizeParameter($arrParam, $trim = false)
    {

        $arrParam = parent::normalizeParameter($arrParam, true);
        return $arrParam;
    }


}

?>