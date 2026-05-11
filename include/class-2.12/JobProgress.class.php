<?php 

class JobProgress extends BaseClass
{
    function __construct(){
		
		parent::__construct();
       
		$this->tableName = 'job_progress_header';  
		$this->tableNameDetail = 'job_progress_detail';
        $this->tableStatus = 'master_status'; 
        $this->tableTruckingServiceOrderCategory = 'trucking_service_order_category';
         
		$this->securityObject = 'JobProgress';

        $this->arrDataDetail = array();
        $this->arrDataDetail['pkey'] = array('hidDetailKey');
        $this->arrDataDetail['refkey'] = array('pkey', 'ref');
        $this->arrDataDetail['number'] = array('numberDetail', 'number');
        $this->arrDataDetail['name'] = array('name');
        $this->arrDataDetail['needpod'] = array('chkNeedPOD');

        $arrDetails = array();
        array_push($arrDetails, array('dataset' => $this->arrDataDetail, 'tableName' => $this->tableNameDetail));

        $this->arrData = array();
        $this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails));
        $this->arrData['code'] = array('code');
        $this->arrData['categorykey'] = array('hidCategoryKey');
        $this->arrData['trdesc'] = array('trDesc');
        $this->arrData['statuskey'] = array('selStatus');


        $this->arrDataListAvailableColumn = array();
        array_push($this->arrDataListAvailableColumn, array('code' => 'code', 'title' => 'code', 'dbfield' => 'code', 'default' => true, 'width' => 70));
        array_push($this->arrDataListAvailableColumn, array('code' => 'category', 'title' => 'category', 'dbfield' => 'categoryname', 'default' => true, 'width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'notes', 'title' => 'note', 'dbfield' => 'trdesc', 'default' => true, 'width' => 350));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status', 'title' => 'status', 'dbfield' => 'statusname', 'default' => true, 'width' => 70));

        $this->arrSearchColumn = array();
        array_push($this->arrSearchColumn, array('Kode', $this->tableName . '.code'));
        array_push($this->arrSearchColumn, array('Kategori', $this->tableTruckingServiceOrderCategory . '.name'));
        array_push($this->arrSearchColumn, array('Status', $this->tableStatus . '.status'));

        $this->includeClassDependencies(array(
            'TruckingServiceOrderCategory.class.php',
        ));

        $this->overwriteConfig();
    }

    function getQuery()
    {
        $sql = '
				select
					' . $this->tableName . '.*,
                    ' . $this->tableTruckingServiceOrderCategory . '.name as categoryname,
					' . $this->tableStatus . '.status as statusname
				from 
					' . $this->tableName . ',
                    ' . $this->tableStatus . ',
                    ' . $this->tableTruckingServiceOrderCategory . '
				where  		 
                    '.$this->tableName.'.categorykey = '.$this->tableTruckingServiceOrderCategory.'.pkey and
					' . $this->tableName . '.statuskey = ' . $this->tableStatus . '.pkey 
                    
 		' . $this->criteria;
        return $sql;
    }

    function getDetailWithRelatedInformation($pkey, $criteria = '')
    {
        $sql = 'select
	   			' . $this->tableNameDetail . '.*
			  from
			  	' . $this->tableNameDetail . '
			  where
                ' . $this->tableNameDetail . '.refkey in (' . $this->oDbCon->paramString($pkey, ',') . ') ';
        $sql .= $criteria;

        return $this->oDbCon->doQuery($sql);
    }

    function validateForm($arr, $pkey = '')
    {

        $arrayToJs = parent::validateForm($arr, $pkey);

        $categorykey = $arr['hidCategoryKey'];

        $arrName = $arr['name'];

        if(empty($categorykey))  {
            $this->addErrorList($arrayToJs, false, $this->errorMsg['jobProgress'][1]);
        }

        for($i=0; $i<count($arrName); $i++) {
            if(empty($arrName[$i])) {
                $this->addErrorList($arrayToJs, false, '<strong>'.$this->errorMsg[501].'</strong> '. $this->errorMsg['name'][1] );
            }
        } 

        return $arrayToJs;
    }

    function getJobProgressByCategory($pkey)
    {
        $sql = '
            select
                '.$this->tableNameDetail.'.*,
                '.$this->tableName.'.code,
                '.$this->tableName.'.categorykey,
                '.$this->tableTruckingServiceOrderCategory.'.name as categoryname
            from
                '.$this->tableNameDetail.',
                '.$this->tableName.',
                '.$this->tableTruckingServiceOrderCategory.'
            where
                '.$this->tableNameDetail.'.refkey = '.$this->tableName.'.pkey and
                '.$this->tableName.'.categorykey = '.$this->tableTruckingServiceOrderCategory.'.pkey and
                '.$this->tableName.'.categorykey = '.$this->oDbCon->paramString($pkey).' order by '.$this->tableNameDetail.'.number asc
        ';

        return $this->oDbCon->doQuery($sql);
    }

    function normalizeParameter($arrParam, $trim = false){
        
        $totalRows = count($arrParam['numberDetail']);
        for($i=0;$i<$totalRows;$i++)
            $arrParam['numberDetail'][$i] = ($i+1);
        
        $arrParam = parent::normalizeParameter($arrParam, true);

        return $arrParam;
    }

}

?>