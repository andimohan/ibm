<?php
class MeetingPointSuggestion extends BaseClass
{

    function __construct()
    {

        parent::__construct();
        $this->tableName = 'meeting_point_suggestion';
        $this->tableCustomer = 'customer';
        $this->tableCity = 'city';
        $this->tableCountry = 'country';
        $this->tableCityCategory = 'city_category';
        $this->tableStatus = 'transaction_status';
		$this->isTransaction = true;
		$this->newLoad = true;
		
        //nama security di ?
        $this->securityObject = 'MeetingPointSuggestion';

        $this->arrData = array();
        $this->arrData['pkey'] = array('pkey');
        $this->arrData['statuskey'] = array('selStatus');
        $this->arrData['code'] = array('code');
        $this->arrData['name'] = array('name');
        $this->arrData['phone'] = array('phone');
        $this->arrData['address'] = array('address');
        $this->arrData['description'] = array('descriptionPoint');
        $this->arrData['countrykey'] = array('selCountry');
        $this->arrData['citykey'] = array('hidCityKey');
        $this->arrData['customerkey'] = array('hidCustomerKey');


        $this->arrDataListAvailableColumn = array();
        array_push($this->arrDataListAvailableColumn, array('code' => 'code', 'title' => 'code', 'dbfield' => 'code', 'default' => true, 'width' => 70));
        array_push($this->arrDataListAvailableColumn, array('code' => 'name', 'title' => 'name', 'dbfield' => 'name', 'default' => true, 'width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'phone', 'title' => 'phone', 'dbfield' => 'phone', 'default' => true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'address', 'title' => 'address', 'dbfield' => 'address', 'default' => true, 'width' => 300));
        array_push($this->arrDataListAvailableColumn, array('code' => 'cityname', 'title' => 'city', 'dbfield' => 'cityname', 'default' => true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'countryname', 'title' => 'country', 'dbfield' => 'countryname', 'default' => true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status', 'title' => 'status', 'dbfield' => 'statusname', 'default' => true, 'width' => 70));


        $this->arrSearchColumn = array();
        array_push($this->arrSearchColumn, array('Kode', $this->tableName . '.code'));
        array_push($this->arrSearchColumn, array('Nama', $this->tableName . '.name'));
        array_push($this->arrSearchColumn, array('Alamat', $this->tableName . '.address'));
        array_push($this->arrSearchColumn, array('Kota', $this->tableCity . '.name')); 
        array_push($this->arrSearchColumn, array('Negara', $this->tableCountry . '.name'));

		
        $this->includeClassDependencies(array(
			 'CityCategory.class.php',
              'City.class.php',
              'MeetingPoint.class.php',
        ));
		 
        $this->overwriteConfig();
    }


    function getQuery()
    {

        $sql = '
                 select
                     ' . $this->tableName . '.*,
                     CONCAT (' . $this->tableCity . '.name,", ",' . $this->tableCityCategory . '.name) as cityname, 
                     ' . $this->tableCountry . '.name as  countryname, 
                     ' . $this->tableCustomer . '.name as customername, 
                     ' . $this->tableStatus . '.status as statusname 
                 from 
                     ' . $this->tableName . '
						 left join ' . $this->tableCity . ' on ' . $this->tableName . '.citykey = ' . $this->tableCity . '.pkey
						 left join ' . $this->tableCityCategory . ' on ' . $this->tableCity . '.categorykey = ' . $this->tableCityCategory . '.pkey
                         left join ' . $this->tableCountry . ' on ' . $this->tableName . '.countrykey = ' . $this->tableCountry . '.pkey 
						 left join ' . $this->tableCustomer . ' on ' . $this->tableName . '.customerkey = ' . $this->tableCustomer . '.pkey,
                     ' . $this->tableStatus . '
                 where  		
                     ' . $this->tableName . '.statuskey = ' . $this->tableStatus . '.pkey
          ' . $this->criteria;
        return $sql;
    }

    function validateForm($arr, $pkey = '')
    {
        $arrayToJs = parent::validateForm($arr, $pkey);
        if (empty($arr['code'])) {
            $this->addErrorList($arrayToJs, false, $this->errorMsg['code'][1]);
        }
        if (empty($arr['hidCustomerKey'])) {
            $this->addErrorList($arrayToJs, false, $this->errorMsg['customer'][1]);
        }
		
		$name = $arr['name'];  
        
        if(empty($name)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['meetingPoint'][1]);
		}else{
	 		$rsMeetingPoint = $this->isValueExisted($pkey,'name',$name);	
			if(count($rsMeetingPoint) <> 0) 
				$this->addErrorList($arrayToJs,false,$this->errorMsg['meetingPoint'][2]); 
		}
		 
        if (empty($arr['address'])) {
            $this->addErrorList($arrayToJs, false, $this->errorMsg['address'][1]);
        }
        
        if (empty($arr['phone'])) {
            $this->addErrorList($arrayToJs, false, $this->errorMsg['phone'][1]);
        }
        return $arrayToJs;
    }

    function normalizeParameter($arrParam, $trim = false)
    {
        $arrParam = parent::normalizeParameter($arrParam, true);
        return $arrParam;
    }
	
	function validateConfirm($rsHeader){
		  
		$meetingPoint = new MeetingPoint();
		
		$checkCityMeetingPoint = $meetingPoint->searchDataRow(array($meetingPoint->tableName.'.pkey'),
															  ' and ('.$meetingPoint->tableName.'.name ='. $this->oDbCon->paramString($rsHeader[0]['name']).'
																	)'
																);
		
		$checkCityMeetingPointSuggestion = $this->searchDataRow(array($this->tableName.'.pkey'),
															  ' and ('.$this->tableName.'.name ='. $this->oDbCon->paramString($rsHeader[0]['name']).'
																	)
																and '.$this->tableName.'.pkey <> '.$this->oDbCon->paramString($rsHeader[0]['pkey'])
																);
		
		if(!empty($checkCityMeetingPoint) || !empty($checkCityMeetingPointSuggestion) ){
			  $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. <strong>'.$rsHeader[0]['name'].'</strong>, '.$this->errorMsg['meetingPoint'][2]); 
		} 
		 
	 }

	function confirmTrans($rsHeader){
		$rsHeader= $rsHeader[0];
		$meetingPoint = new MeetingPoint();

		$arrParam = array();
		$arrParam['code'] = 'xxxxxx';
		$arrParam['selStatus'] = '1';
		$arrParam['name'] = html_entity_decode($rsHeader['name']);
		$arrParam['address'] =  html_entity_decode($rsHeader['address']);
		$arrParam['hidCityKey'] = $rsHeader['citykey'];
		$arrParam['selCountry'] = $rsHeader['countrykey'];

		$arrayToJs = $meetingPoint->addData($arrParam);
   
	}
} 