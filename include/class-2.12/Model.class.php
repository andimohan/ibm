<?php 

class Model extends Category
{
 
    function __construct(){
		
		parent::__construct();

        $this->tableName = 'model';
        $this->tableStatus = 'master_status'; 
        $this->uploadFolder = 'model/'; 

		$this->securityObject = 'Model';

        $this->arrData = array();
        $this->arrData['pkey'] = array('pkey');
        $this->arrData['code'] = array('code');
        $this->arrData['name'] = array('name');
        $this->arrData['parentkey'] = array('selModel');
        $this->arrData['isleaf'] = array('isLeaf');
        $this->arrData['orderlist'] = array('orderList', 'number');
        $this->arrData['statuskey'] = array('selStatus');
        $this->arrData['shortdescription'] = array('trShortDesc');
        $this->arrData['description'] = array('txtDescription', 'raw');
        $this->arrData['file'] = array('model-image-uploader', array('datatype' => 'image', 'uploadFolder' => $this->uploadFolder, 'token' => 'token-model-image-uploader', 'fileName' => 'model-image-uploader'));

        $this->newLoad = true;

        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 70));
        array_push($this->arrDataListAvailableColumn, array('code' => 'name','title' => 'name','dbfield' => 'name','default'=>true,'width' => 250));
        array_push($this->arrDataListAvailableColumn, array('code' => 'parent','title' => 'parent','dbfield' => 'parentname','default'=>true, 'width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'note','title' => 'note','dbfield' => 'shortdescription', 'width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
        array_push($this->arrDataListAvailableColumn, array('code' => 'orderlist','title' => 'orderList','dbfield' => 'orderlist', 'align' => 'right', 'format' => 'integer', 'width' => 70));

        $this->arrSearchColumn = array ();
        array_push($this->arrSearchColumn, array('Kode', $this->tableName . '.code'));
        array_push($this->arrSearchColumn, array('Name', $this->tableName . '.name'));          
        array_push($this->arrSearchColumn, array('Parent', $this->tableName . '.parentname'));          
        
        $this->includeClassDependencies(array(
        ));

        $this->overwriteConfig();

    }

    function getQuery()
    {

        $sql = '
			select
					' . $this->tableName . '.*,
					' . $this->tableStatus . '.status as statusname,
					parentcat.name as parentname
				from 
					' . $this->tableName . ' left join ' . $this->tableName . ' parentcat on 	parentcat.pkey = ' . $this->tableName . '.parentkey ,' . $this->tableStatus . ' 
				where  		
					' . $this->tableName . '.statuskey = ' . $this->tableStatus . '.pkey 
 		' . $this->criteria;

        return $sql;
    }

    function validateForm($arr, $pkey = '')
    {

        $arrayToJs = parent::validateForm($arr, $pkey);

        $name = $arr['name'];
        $orderlist = (!empty($arr['orderList'])) ? $this->unformatNumber($arr['orderList']) : 0;

        $pkeyCriteria = (!empty($pkey)) ? ' and ' . $this->tableName . '.pkey <> ' . $this->oDbCon->paramString($pkey) : '';

        $rsModel = $this->searchData('', '', true, ' and ' . $this->tableName . '.name = ' . $this->oDbCon->paramString($name) . ' and ' . $this->tableName . '.parentkey = ' . $this->oDbCon->paramString($arr['selModel']) . ' ' . $pkeyCriteria);
        if (empty($name)) {
            $this->addErrorList($arrayToJs, false, $this->errorMsg['model'][1]);
        } else if (count($rsModel) <> 0) {
            $this->addErrorList($arrayToJs, false, $this->errorMsg['model'][2]);
        }

        if (!empty($orderlist)) {
            if (!is_numeric($orderlist)) {
                $this->addErrorList($arrayToJs, false, $this->errorMsg['orderList'][2]);
            }
        }

        return $arrayToJs;
    }

    function generateDefaultQueryForAutoComplete($returnField)
    {
        if (is_array($returnField['value'])) {
            $returnField['value'] = "CONCAT(" . implode(", ' - ', ", $returnField['value']) . ")";
        } 

        $sql = 'select
					' . $returnField['key'] . ',
					' . $returnField['value'] . ' as value
				from 
					' . $this->tableName . ', 
                    ' . $this->tableStatus . ' 
				where  		 
					' . $this->tableName . '.statuskey = ' . $this->tableStatus . '.pkey  
			';

        return $sql;
    }

    function normalizeParameter($arrParam, $trim = false)
    {
        $arrParam = parent::normalizeParameter($arrParam, true);

        return $arrParam;
    }

}

?>