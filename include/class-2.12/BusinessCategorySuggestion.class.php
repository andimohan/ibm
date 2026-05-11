<?php
class BusinessCategorySuggestion extends BaseClass
{

    function __construct()
    {

        parent::__construct();
        $this->tableName = 'business_category_suggestion';
        $this->tableStatus = 'transaction_status';
		$this->newLoad = true;
		$this->isTransaction = true;

        //nama security di db
        $this->securityObject = 'BusinessCategorySuggestion';

        $this->arrData = array();
        $this->arrData['pkey'] = array('pkey');
        $this->arrData['statuskey'] = array('selStatus');
        $this->arrData['code'] = array('code');
        $this->arrData['name'] = array('category');
        $this->arrData['customerkey'] = array('hidCustomerKey');

        $this->arrDataListAvailableColumn = array();
        array_push($this->arrDataListAvailableColumn, array('code' => 'code', 'title' => 'code', 'dbfield' => 'code', 'default' => true, 'width' => 0));
        array_push($this->arrDataListAvailableColumn, array('code' => 'name', 'title' => 'category', 'dbfield' => 'name', 'default' => true, 'width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status', 'title' => 'status', 'dbfield' => 'statusname', 'default' => true, 'width' =>80));


        $this->arrSearchColumn = array();
        array_push($this->arrSearchColumn, array('Kode', $this->tableName . '.code'));
        array_push($this->arrSearchColumn, array('Category', $this->tableName . '.name'));


        $this->includeClassDependencies(array(
            'BusinessCategory.class.php',
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
                     ' . $this->tableName . '
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
      
        if (empty($arr['hidCustomerKey'])) {
            $this->addErrorList($arrayToJs, false, $this->errorMsg['customer'][1]);
        }
		
		$name = $arr['category'];  
        
        if(empty($name)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['category'][1]);
		}else{
	 		$rsCategory = $this->isValueExisted($pkey,'name',$name);	
			if(count($rsCategory) <> 0) 
				$this->addErrorList($arrayToJs,false,$this->errorMsg['category'][2]); 
		}
		
        
        return $arrayToJs;
    }

    function normalizeParameter($arrParam, $trim = false)
    {
        $arrParam = parent::normalizeParameter($arrParam, true);
        return $arrParam;
    }
	
	function validateConfirm($rsHeader)
    {
        $businessCategory = new BusinessCategory(); 
		
		$checkBusinessCategory = $businessCategory->searchDataRow(array($businessCategory->tableName.'.pkey'),
															  ' and ('.$businessCategory->tableName.'.name ='. $this->oDbCon->paramString($rsHeader[0]['name']).'
																	)'
																);
		
		$checkBusinessCategorySuggestion = $this->searchDataRow(array($this->tableName.'.pkey'),
															  ' and ('.$this->tableName.'.name ='. $this->oDbCon->paramString($rsHeader[0]['name']).'
																	)
																and '.$this->tableName.'.pkey <> '.$this->oDbCon->paramString($rsHeader[0]['pkey'])
																);
		
		if(!empty($checkBusinessCategory) || !empty($checkBusinessCategorySuggestion) ){
			  $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. <strong>'.$rsHeader[0]['name'].'</strong>, '.$this->errorMsg['businessCategory'][2]); 
		} 
		
    }
	
    function confirmTrans($rsHeader)
    {
        $businessCategory = new BusinessCategory();
        $rsHeader = $rsHeader[0];
        $arrayToJs = array();
        $arrParam = array();
        $arrParam['code'] = 'xxxxxx';
        $arrParam['selStatus'] = '1';
        $arrParam['name'] = html_entity_decode($rsHeader['name']);

        $arrayToJs = $businessCategory->addData($arrParam);
    }
}
