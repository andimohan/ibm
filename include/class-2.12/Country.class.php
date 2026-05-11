<?php
class Country extends BaseClass
{

    function __construct()
    {

        parent::__construct();

        $this->tableName = 'country';
        $this->tableContinent = 'continent';
        $this->tableStatus = 'master_status';

        $this->securityObject = 'Country';

        $this->arrData = array();
        $this->arrData['pkey'] = array('pkey');
        $this->arrData['code'] = array('code');
        $this->arrData['name'] = array('name');
        $this->arrData['continentkey'] = array('hidContinentKey');
        $this->arrData['statuskey'] = array('selStatus');


		$this->importUrl = 'import/country';
		
        $this->arrDataListAvailableColumn = array();
        array_push($this->arrDataListAvailableColumn, array('code' => 'code', 'title' => 'code', 'dbfield' => 'code', 'default' => true, 'width' => 70));
        array_push($this->arrDataListAvailableColumn, array('code' => 'name', 'title' => 'name', 'dbfield' => 'name', 'default' => true, 'width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'continent', 'title' => 'continent', 'dbfield' => 'continentname', 'default' => true, 'width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status', 'title' => 'status', 'dbfield' => 'statusname', 'default' => true, 'width' => 70));

        $this->arrSearchColumn = array();
        array_push($this->arrSearchColumn, array('Kode', $this->tableName . '.code'));
        array_push($this->arrSearchColumn, array('Nama Negara', $this->tableName . '.name'));
        array_push($this->arrSearchColumn, array('Nama Benua', $this->tableContinent . '.name'));
        array_push($this->arrSearchColumn, array('Status', $this->tableStatus . '.status'));


        $this->includeClassDependencies(array(
            'Continent.class.php'
        ));

        $this->overwriteConfig();
    }

    function getQuery()
    {
        return '
				select
					' . $this->tableName . '.*,
					' . $this->tableStatus . '.status as statusname,
                    ' . $this->tableContinent .'.code as continentcode,
                    ' . $this->tableContinent .'.name as continentname
				from 
					' . $this->tableName . '
                    left join '. $this->tableContinent .' on '. $this->tableName .'.continentkey = '. $this->tableContinent .'.pkey,
                    ' . $this->tableStatus . '
				where  		
					' . $this->tableName . '.statuskey = ' . $this->tableStatus . '.pkey
 	    ' . $this->criteria;
    }
    function validateForm($arr, $pkey = '')
    {

        $arrayToJs = parent::validateForm($arr, $pkey);

        $name = $arr['name'];

        $continentKey = $arr['hidContinentKey'];

        if (empty($continentKey)) {
            $this->addErrorList($arrayToJs, false, $this->errorMsg['continent'][1]);
        }

        $rs = $this->isValueExisted($pkey, 'name', $name);
        if (empty($name)) {
            $this->addErrorList($arrayToJs, false, $this->errorMsg['country'][1]);
        } else if (count($rs) <> 0) {
            $this->addErrorList($arrayToJs, false, $this->errorMsg['country'][2]);
        }

        return $arrayToJs;
    }
    
    function normalizeParameter($arrParam, $trim=false){ 
        $arrParam = parent::normalizeParameter($arrParam,true);  
        return $arrParam;
    }

 
    function getPhoneCode($criteria = ''){
        
        $arrCriteria = array();
        array_push($arrCriteria,  $this->tableName.'.phonecode <> \'\'');
        array_push($arrCriteria, ' not '.$this->tableName.'.phonecode is null ');
        array_push($arrCriteria, $this->tableName.'.statuskey = 1');
        
        $criteria .= ' and ' .implode(' and ', $arrCriteria);
        
        $rs =  $this->searchDataRow(array($this->tableName.'.pkey','concat(\'+\','.$this->tableName.'.phonecode) as plusphonecode','phonecode'), $criteria, ' order by phonecode asc'); 
        return $rs;
        
    }
    
    function getNationality($criteria = ''){
            
        $arrCriteria = array();
        array_push($arrCriteria,  $this->tableName.'.nationality <> \'\'');
        array_push($arrCriteria, ' not '.$this->tableName.'.nationality is null '); 
        array_push($arrCriteria, $this->tableName.'.statuskey = 1');
        
        $criteria .= ' and ' .implode(' and ', $arrCriteria);
        
        $rs =  $this->searchDataRow(array($this->tableName.'.pkey',$this->tableName.'.nationality',$this->tableName.'.code'), $criteria, ' order by '.$this->tableName.'.nationality asc'); 
        
        return $rs;
        
    }
}

?>
