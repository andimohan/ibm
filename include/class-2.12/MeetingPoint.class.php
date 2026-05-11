<?php
class MeetingPoint extends BaseClass
{

    function __construct()
    {

        parent::__construct();
        $this->tableName = 'meeting_point';
        $this->tableStatus = 'master_status';
		$this->tableCity = 'city';
        $this->tableCountry = 'country';
		$this->tableCityCategory = 'city_category';

        //nama security di ?
        $this->securityObject = 'MeetingPoint';
        $this->newLoad = true;

        $this->arrData = array();
        $this->arrData['pkey'] = array('pkey');
        $this->arrData['code'] = array('code');
        $this->arrData['name'] = array('name');
        $this->arrData['statuskey'] = array('selStatus');
        $this->arrData['address'] = array('address');
        $this->arrData['citykey'] = array('hidCityKey');
        $this->arrData['countrykey'] = array('selCountry');

        $this->arrDataListAvailableColumn = array();
        array_push($this->arrDataListAvailableColumn, array('code' => 'code', 'title' => 'code', 'dbfield' => 'code', 'default' => true, 'width' => 70));
        array_push($this->arrDataListAvailableColumn, array('code' => 'name', 'title' => 'name', 'dbfield' => 'name', 'default' => true, 'width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'address', 'title' => 'address', 'dbfield' => 'address', 'default' => true, 'width' => 300));
        array_push($this->arrDataListAvailableColumn, array('code' => 'city', 'title' => 'city', 'dbfield' => 'citycategoryname', 'default' => true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'countryname', 'title' => 'country', 'dbfield' => 'countryname', 'default' => true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status', 'title' => 'status', 'dbfield' => 'statusname', 'default' => true, 'width' => 100));

        $this->arrSearchColumn = array();
        array_push($this->arrSearchColumn, array('Kode', $this->tableName . '.code'));
        array_push($this->arrSearchColumn, array('Nama', $this->tableName . '.name'));
        array_push($this->arrSearchColumn, array('Alamat', $this->tableName . '.address'));
        array_push($this->arrSearchColumn, array('Kota', $this->tableCity . '.name')); 
        array_push($this->arrSearchColumn, array('Negara', $this->tableCountry . '.name'));

        $this->overwriteConfig();
    }


    //  wajib
    function getQuery(){

        $sql = '
                 select
                     ' . $this->tableName . '.*,
					 ' . $this->tableCity . '.name as cityname,
					 concat('.$this->tableCity.'.name, ", ",'.$this->tableCityCategory.'.name) as citycategoryname,
                     ' . $this->tableCountry . '.name as  countryname, 
                     ' . $this->tableStatus . '.status as statusname 
                 from 
                     ' . $this->tableName . '
					 	left join  ' . $this->tableCity . ' on  ' . $this->tableName . '.citykey = ' . $this->tableCity . '.pkey
                        left join '.$this->tableCityCategory.' on '.$this->tableCity.'.categorykey =  '.$this->tableCityCategory.'.pkey
                         left join ' . $this->tableCountry . ' on ' . $this->tableName . '.countrykey = ' . $this->tableCountry . '.pkey 
					,' . $this->tableStatus . '
                 where  		
                     ' . $this->tableName . '.statuskey = ' . $this->tableStatus . '.pkey
          ' . $this->criteria;
        return $sql;
    }

    function validateForm($arr, $pkey = '')  {
        $arrayToJs = parent::validateForm($arr, $pkey);
		
		 $meetingpointname  = $arr['name']; 
         $rs = $this->isValueExisted($pkey,'name',$meetingpointname); 
         if(empty($meetingpointname)){
             $this->addErrorList($arrayToJs,false,$this->errorMsg['meetingPoint'][1]);
         }else if (count($rs) <> 0){  
             $this->addErrorList($arrayToJs,false,$this->errorMsg['meetingPoint'][2]);
         }
		
         $meetingpointaddress = $arr['address']; 
         if(empty($meetingpointaddress)) 
             $this->addErrorList($arrayToJs,false,$this->errorMsg['address'][1]);
        
        return $arrayToJs;
    }
	
    function normalizeParameter($arrParam, $trim = false) { 
        $arrParam = parent::normalizeParameter($arrParam, true);
        return $arrParam;
    }
}
