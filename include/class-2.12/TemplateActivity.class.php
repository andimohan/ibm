<?php 

class TemplateActivity extends BaseClass 
{
    function __construct()
    {
        parent::__construct();

        $this->tableName = 'template_activity';
        $this->tableDataTypeDetail = 'template_activity_data_type';
        $this->tableType = 'template_activity_type';
        $this->tableStatus = 'master_status';

        $this->newLoad = true;
        $this->securityObject = 'TemplateActivity';

        $this->arrDataTypDetail = array();
        $this->arrDataTypeDetail['pkey'] = array('hidDetailDataTypeKey');
        $this->arrDataTypeDetail['refkey'] = array('pkey', 'ref');
        $this->arrDataTypeDetail['name'] = array('dataTypeDetailName');

        $arrDetails = array();
        array_push($arrDetails, array('dataset' => $this->arrDataTypeDetail, 'tableName' => $this->tableDataTypeDetail));

        $this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails));
        $this->arrData['code'] = array('code');
        $this->arrData['name'] = array('name');
        $this->arrData['typekey'] = array('selDataType');
        $this->arrData['notification'] = array('chkIsNotification');
        $this->arrData['orderlist'] = array('orderList', 'number');
        $this->arrData['statuskey'] = array('selStatus');

        $this->arrDataListAvailableColumn = array();
        array_push($this->arrDataListAvailableColumn, array('code' => 'code', 'title' => 'code', 'dbfield' => 'code', 'default' => true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'name', 'title' => 'name', 'dbfield' => 'name', 'default' => true, 'width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'type', 'title' => 'type', 'dbfield' => 'typename', 'default' => true, 'width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'orderList', 'title' => 'orderList', 'dbfield' => 'orderlist', 'default' => true, 'align'=>'right', 'width' => 80));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status', 'title' => 'status', 'dbfield' => 'statusname', 'default' => true, 'width' => 70));


        $this->arrSearchColumn = array();
        array_push($this->arrSearchColumn, array('Kode', $this->tableName . '.code'));
        array_push($this->arrSearchColumn, array('Nama', $this->tableName . '.name'));
        array_push($this->arrSearchColumn, array('Tipe', $this->tableType . '.name'));

        $this->overwriteConfig();
    }

    function getQuery()
    {
        $sql = '
            select
                '. $this->tableName .'.*,
                '. $this->tableStatus .'.status as statusname,
                '. $this->tableType .'.name as typename
            from
                '. $this->tableName .',
                '. $this->tableType .',
                '. $this->tableStatus .'
            where
                '. $this->tableName .'.typekey = '. $this->tableType .'.pkey and
                '. $this->tableName .'.statuskey = '. $this->tableStatus .'.pkey
        ' . $this->criteria;

        return $sql;
    }

    function validateForm($arr, $pkey = '')
    {
        $arrayToJs = parent::validateForm($arr, $pkey);

        $name = $arr['name'];

        $selDataType = $arr['selDataType'];
        $arrDataTypeName = $arr['dataTypeDetailName'];

        if(empty($name)) {
            $this->addErrorList($arrayToJs,false,$this->errorMsg['name'][1]);
        }

        if(empty($selDataType)) {
            $this->addErrorList($arrayToJs,false,$this->errorMsg['templateActivity'][1]);
        } else {
            if(($selDataType == TEMPLATE_ACTIVITY_TYPE['selectBox']) || ($selDataType == TEMPLATE_ACTIVITY_TYPE['checkBox'])) {
                $rsType = $this->getDataType($selDataType);
                if(empty($arrDataTypeName[0])) {
                    $this->addErrorList($arrayToJs, false, '<strong>'.$rsType[0]['name'].'. </strong>' . $this->errorMsg['templateActivity'][2]);
                } else {
                    for($i=0; $i<count($arrDataTypeName); $i++) {
                        if(empty($arrDataTypeName[$i])) {
                            $this->addErrorList($arrayToJs, false, '<strong>'.$rsType[0]['name'].'. </strong>' . $this->errorMsg['templateActivity'][3]);
                        }
                    }
                }
            }
        }

        return $arrayToJs;
    }

    function getDataTypeDetail($pkey) {
        $sql = '
            select 
                ' . $this->tableDataTypeDetail . '.*
            from
                ' . $this->tableDataTypeDetail . '
            where
                ' . $this->tableDataTypeDetail . '.refkey in (' . $this->oDbCon->paramString($pkey, ',') . ')
        ';

        $result = $this->oDbCon->doQuery($sql);

        return $result;
    }

    function getDataType($pkey = '') 
    {
        $sql = '
            select 
                '. $this->tableType .'.*
            from
                '. $this->tableType .'
            where
                '. $this->tableType .'.statuskey = 1
        ';

        if(!empty($pkey)) {
            $sql .= ' and '. $this->tableType .'.pkey in ('. $this->oDbCon->paramString($pkey,',') .') ';
        }

        $result = $this->oDbCon->doQuery($sql);

        return $result;
    }

    function normalizeParameter($arrParam, $trim = false)
    {

        $arrParam = parent::normalizeParameter($arrParam, true);

        return $arrParam;
    }

}

?>
