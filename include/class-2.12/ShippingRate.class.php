<?php

class ShippingRate extends BaseClass{


    function __construct()
    {

        parent::__construct();

        $this->tableName = 'shipping_rate_header';
        $this->tableNameDetail = 'shipping_rate_detail';
        $this->tableStatus = 'master_status';
        $this->tableTransportation = 'transportation';
        $this->tableUnit = 'item_unit';
        $this->tableNameCity = 'city';
        $this->newLoad = true;

        $this->securityObject = 'ShippingRate'; 

        $arrDetail = array();
        $arrDetail['pkey'] = array('hidDetailKey');
        $arrDetail['refkey'] = array('pkey', 'ref');
        $arrDetail['transportationkey'] = array('hidTransportationKey', 'number');
        $arrDetail['firstfee'] = array('firstFee', 'number');
        $arrDetail['nextfee'] = array('nextFee', 'number');

        $arrDetails = array();
        array_push($arrDetails, array('dataset' => $arrDetail, 'tableName' => $this->tableNameDetail));

        $this->arrData = array();
        $this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails));
        $this->arrData['code'] = array('code');
        $this->arrData['fromcitykey'] = array('hidFromCityKey');
        $this->arrData['destinationcitykey'] = array('hidDestinationCityKey');
 

        $this->arrDataListAvailableColumn = array();
        array_push($this->arrDataListAvailableColumn, array('code' => 'code', 'title' => 'code', 'dbfield' => 'code', 'default' => true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'fromcity', 'title' => 'fromCity', 'dbfield' => 'fromcityname', 'default' => true, 'width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'destinationcity', 'title' => 'destinationCity', 'dbfield' => 'destinationcityname', 'default' => true, 'width' => 200));
        //array_push($this->arrDataListAvailableColumn, array('code' => 'rate', 'title' => 'price', 'dbfield' => 'price', 'default' => true, 'width' => 100, 'align' => 'right'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status', 'title' => 'status', 'dbfield' => 'statusname', 'default' => true, 'width' => 70));

        $this->arrSearchColumn = array();
        array_push($this->arrSearchColumn, array('Kode', $this->tableName . '.code'));
        array_push($this->arrSearchColumn, array('Kota Asal', 'fromcity.name'));
        array_push($this->arrSearchColumn, array('Kota Tujuan', 'destinationcity.name'));

        $this->includeClassDependencies(array(
            'LogisticSalesOrder.class.php',
            'City.class.php',
            'CityCategory.class.php',
        ));

        $this->overwriteConfig();
    }


    function getQuery()
    {

        $sql = '
                 select
                     ' . $this->tableName . '.*,
                     fromcity.name as fromcityname,
                     destinationcity.name as destinationcityname, 
                     ' . $this->tableStatus . '.status as statusname 
                 from 
                     ' . $this->tableName . '  
                    left join ' . $this->tableNameCity . ' fromcity on ' . $this->tableName . '.fromcitykey = fromcity.pkey
                    left join ' . $this->tableNameCity . ' destinationcity on ' . $this->tableName . '.destinationcitykey = destinationcity.pkey,
                    ' . $this->tableStatus . '
                 where  		
                     ' . $this->tableName . '.statuskey = ' . $this->tableStatus . '.pkey '
            . $this->criteria;
        return $sql;
    }

    function validateForm($arr, $pkey = '')
    {

        $arrayToJs = parent::validateForm($arr, $pkey);
        if (empty($arr['code'])) {
            $this->addErrorList($arrayToJs, false, $this->errorMsg['code'][1]);
        }
        
        if (empty($arr['hidFromCityKey'])) {
            $this->addErrorList($arrayToJs, false, $this->errorMsg['city'][1]);
        }
        if (empty($arr['hidDestinationCityKey'])) {
            $this->addErrorList($arrayToJs, false, $this->errorMsg['city'][1]);
        }
         
        for ($i = 0; $i < count($arr['hidDetailKey']); $i++) {
            if ( $arr['firstFee'][$i] <= 0)
                $this->addErrorList($arrayToJs, false, $this->errorMsg['price'][1]);
			
            if  ($arr['nextFee'][$i] <= 0)
                  $this->addErrorList($arrayToJs, false, $this->errorMsg['price'][1]);
        }
		 
        return $arrayToJs;
    }


    function normalizeParameter($arrParam, $trim = false) {
		
        $arrParam = parent::normalizeParameter($arrParam,true);  
		
        return $arrParam;
    }
    
    
    function getDetailWithRelatedInformation($pkey, $criteria = '', $orderby = '')
    {
        $sql = '
            select
                ' . $this->tableNameDetail . '.*,
                ' . $this->tableTransportation . '.name as transportationname,
                ' . $this->tableUnit . '.name as unitname
            from 
                ' . $this->tableNameDetail . '
                left join ' . $this->tableTransportation . ' on ' . $this->tableNameDetail . '.transportationkey = ' . $this->tableTransportation . '.pkey
				left join ' . $this->tableUnit . ' on ' . $this->tableTransportation . '.unitkey = ' . $this->tableUnit . '.pkey

            where  		 
            ' . $this->tableNameDetail . '.refkey in (' . $this->oDbCon->paramString($pkey, ',') . ') ';

        $sql .= $criteria;

        $sql .= ' ' . $orderby;

        return $this->oDbCon->doQuery($sql);
    }
	 
}
