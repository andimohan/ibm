<?php
class SalesPrice extends BaseClass
{

    function __construct()
    {

        parent::__construct();

        $this->tableName = 'sales_price_header';
        $this->tableNameDetail = 'sales_price_detail';
        $this->tableCustomer = 'customer';
        $this->tableItem = 'item';

        $this->tableStatus = 'master_status';

        $this->securityObject = 'SalesPrice';

        $this->arrDataDetail = array();
        $this->arrDataDetail['pkey'] = array('hidDetailKey');
        $this->arrDataDetail['refkey'] = array('pkey', 'ref');
        $this->arrDataDetail['itemkey'] = array('hidItemKey');
        $this->arrDataDetail['price'] = array('price', 'number');

        $this->arrData = array();
        $this->arrData['pkey'] = array('pkey', array('dataDetail' => array('dataset' => $this->arrDataDetail)));
        $this->arrData['code'] = array('code');
        $this->arrData['customerkey'] = array('hidCustomerKey');
        $this->arrData['statuskey'] = array('selStatus');
        $this->arrData['notes'] = array('trDesc');


        $this->arrSearchColumn = array();
        array_push($this->arrSearchColumn, array($this->lang['code'], $this->tableName . '.code'));
        array_push($this->arrSearchColumn, array($this->lang['customer'], $this->tableCustomer . '.name'));
        array_push($this->arrSearchColumn, array($this->lang['status'], $this->tableStatus . '.status'));

        $this->arrDataListAvailableColumn = array();
        array_push($this->arrDataListAvailableColumn, array('code' => 'code', 'title' => 'code', 'dbfield' => 'code', 'default' => true, 'width' => 70));
        array_push($this->arrDataListAvailableColumn, array('code' => 'customer', 'title' => 'customer', 'dbfield' => 'customername', 'default' => true, 'width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status', 'title' => 'status', 'dbfield' => 'statusname', 'default' => true, 'width' => 70));

        $this->includeClassDependencies(array(
            'Item.class.php',
            'Supplier.class.php'
        ));

        $this->overwriteConfig();
    }


    function getQuery()
    {

        $sql = '
				select
					' . $this->tableName . '.*,
                    ' . $this->tableCustomer . '.name as customername,
					' . $this->tableStatus . '.status as statusname
				from 
					' . $this->tableName . ',
                    ' . $this->tableCustomer . ', 
                    ' . $this->tableStatus . '
				where  		
					' . $this->tableName . '.statuskey = ' . $this->tableStatus . '.pkey and  
                    ' . $this->tableName . '.customerkey = ' . $this->tableCustomer . '.pkey
 	    ';

        $sql .= $this->criteria;

        return $sql;
    }


    function getDetailWithRelatedInformation($pkey, $criteria = '')
    {

        $sql = 'select
	   			    ' . $this->tableNameDetail . '.*,
                    ' . $this->tableItem . '.code as itemcode,
                    ' . $this->tableItem . '.barcode as itembarcode,
                    ' . $this->tableItem . '.name as itemname
			    from
			  	    ' . $this->tableNameDetail . ',
                    ' . $this->tableItem . '
			    where 
                    ' . $this->tableNameDetail . '.itemkey = ' . $this->tableItem . '.pkey and
			  	    ' . $this->tableNameDetail . '.refkey = ' . $this->oDbCon->paramString($pkey);

        $sql .= $criteria;
        return $this->oDbCon->doQuery($sql);
    }

    function getSalesPrice($customerkey, $itemkey = '')
    {

        if (empty($customerkey))
            return;

        $sql = '
            select
                ' . $this->tableNameDetail . '.*,
                ' . $this->tableItem . '.code as itemcode,
                ' . $this->tableItem . '.barcode as itembarcode,
                ' . $this->tableItem . '.name as itemname,
                ' . $this->tableCustomer . '.name as customername
            from
                ' . $this->tableNameDetail . '
                    left join ' . $this->tableItem . ' on ' . $this->tableNameDetail . '.itemkey = ' . $this->tableItem . '.pkey,
                ' . $this->tableName . ',
                ' . $this->tableCustomer . '
            where
                ' . $this->tableNameDetail . '.refkey = ' . $this->tableName . '.pkey and
                ' . $this->tableName . '.customerkey = ' . $this->tableCustomer . '.pkey and
                ' . $this->tableName . '.customerkey = ' . $this->oDbCon->paramString($customerkey) . '
        ';

        if(!empty($itemkey)){
            $sql .= ' and ' . $this->tableNameDetail . '.itemkey in (' . $this->oDbCon->paramString($itemkey, ',') . ')';
        }

        $rs = $this->oDbCon->doQuery($sql);

        return $rs;
    }


    function validateForm($arr, $pkey = '')
    {

        $item = new Item();

        $arrayToJs = parent::validateForm($arr, $pkey);

        $arrCustomerKey = $arr['hidCustomerKey'];
        $arrItemKey = $arr['hidItemKey'];
        $arrPrice = $arr['price'];

        if (empty($arrCustomerKey)) {
            $this->addErrorList($arrayToJs, false, $this->errorMsg['customer'][1]);
        }

        $rsItem = $item->searchData('', '', true, ' and ' . $item->tableName . '.pkey in (' . $this->oDbCon->paramString($arrItemKey, ',') . ') ');
        $rsItemCol = $this->reindexDetailCollections($rsItem, 'pkey');


        $arrItemDetailKeys = array();
        for ($i = 0; $i < count($arrItemKey); $i++) {

            if (empty($arrItemKey[$i])) {
                $this->addErrorList($arrayToJs, false, $this->errorMsg['item'][1]);
            } else {

                $rsItem = $rsItemCol[$arrItemKey[$i]];
                $price = $this->unFormatNumber($arrPrice[$i]);
                if (in_array($arrItemKey[$i], $arrItemDetailKeys)) {
                    $this->addErrorList($arrayToJs, false, '<strong>' . $rsItem[0]['name'] . '. </strong>' . $this->errorMsg[215]);
                } else {
                    array_push($arrItemDetailKeys, $arrItemKey[$i]);
                }

                if ($price <= 0) {
                    $this->addErrorList($arrayToJs, false, '<strong>' . $rsItem[0]['name'] . '. </strong>' . $this->errorMsg['price'][1]);
                }

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

?>