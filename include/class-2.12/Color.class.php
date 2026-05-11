<?php  
class Color extends BaseClass{
 
    function __construct(){
		
		parent::__construct();

        $this->tableName = 'color';
		$this->tableStatus = 'master_status'; 

		$this->securityObject = 'Color';


        $this->arrData = array();
        $this->arrData['pkey'] = array('pkey');
        $this->arrData['code'] = array('code'); 
        $this->arrData['name'] = array('name');
        $this->arrData['colorhex'] = array('colorHex');
        $this->arrData['statuskey'] = array('selStatus'); 
        $this->arrData['trdesc'] = array('trDesc'); 


        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 70));
        array_push($this->arrDataListAvailableColumn, array('code' => 'name','title' => 'name','dbfield' => 'name','default'=>true,'width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'colorHex','title' => 'colorHex','dbfield' => 'colorhex','default'=>true,'width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'description','title' => 'description','dbfield' => 'trdesc','default'=>true,'width' => 300));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));

        $this->arrSearchColumn = array ();
        array_push($this->arrSearchColumn, array('Kode', $this->tableName . '.code'));
        array_push($this->arrSearchColumn, array('Nama', $this->tableName . '.name'));  
        array_push($this->arrSearchColumn, array('Hex Color', $this->tableName . '.colorhex'));  
        array_push($this->arrSearchColumn, array('Status', $this->tableStatus . '.status'));  

        $this->includeClassDependencies(array( 
        ));

		$this->overwriteConfig();

    }

    function getQuery(){
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


    function validateForm($arr,$pkey = ''){
		
		$arrayToJs = parent::validateForm($arr,$pkey); 
	
	 	$colorname = $arr['name'];

        $rs = $this->isValueExisted($pkey, 'name', $colorname);
        if (empty($colorname )) {
            $this->addErrorList($arrayToJs, false, $this->errorMsg['color'][1]);
        } else if (count($rs) <> 0) {
            $this->addErrorList($arrayToJs, false, $this->errorMsg['color'][2]);
        }
		
		return $arrayToJs;
	}

    function normalizeParameter($arrParam, $trim = false){  
        $arrParam = parent::normalizeParameter($arrParam,true);
        return $arrParam; 
    }  

}

?>