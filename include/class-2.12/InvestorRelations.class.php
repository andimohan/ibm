<?php
class InvestorRelations extends BaseClass
{

    function __construct($meetingType = '1')
    {

        parent::__construct();
        $this->tableName = 'investor_relations_header';
		$this->tableStatus = 'master_status';
        
        
		$this->coverUploadFolder = 'investor-relations/cover/';
		$this->chartUploadFolder = 'investor-relations/chart/';
         
         
		$this->newLoad = true;
		
        $this->securityObject = 'InvestorRelations';
 

        $this->arrData = array();
        $this->arrData['pkey'] = array('pkey');
        $this->arrData['code'] = array('code');
        $this->arrData['name'] = array('name');
        $this->arrData['statuskey'] = array('selStatus');
        $this->arrData['year'] = array('selYear');  
        $this->arrData['coverimage'] = array('cover-image-uploader',array('datatype' => 'image', 'uploadFolder' => $this->coverUploadFolder,  'token' => 'token-cover-image-uploader', 'fileName' => 'cover-image-uploader'));
        $this->arrData['chartimage'] = array('chart-image-uploader',array('datatype' => 'image', 'uploadFolder' => $this->chartUploadFolder,  'token' => 'token-chart-image-uploader', 'fileName' => 'chart-image-uploader'));

        $this->arrDataListAvailableColumn = array();
        array_push($this->arrDataListAvailableColumn, array('code' => 'code', 'title' => 'code', 'dbfield' => 'code', 'default' => true, 'width' => 70));
        array_push($this->arrDataListAvailableColumn, array('code' => 'year', 'title' => 'period', 'dbfield' => 'year', 'default' => true,'align' =>'center' ,'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status', 'title' => 'status', 'dbfield' => 'statusname', 'default' => true, 'width' => 110));


        $this->arrSearchColumn = array();
        array_push($this->arrSearchColumn, array('Kode', $this->tableName . '.code'));
        array_push($this->arrSearchColumn, array('Tahun', $this->tableName . '.year')); 

        $this->includeClassDependencies(array(
              
        )); 
 
        $this->overwriteConfig();
    }

    function getQuery()
    {

        $sql = '
                 select
                     ' . $this->tableName . '.*, 
                     ' . $this->tableStatus . '.status as statusname 
                 from 
                     ' . $this->tableName . ', ' . $this->tableStatus . '
                 where  		
                     ' . $this->tableName . '.statuskey = ' . $this->tableStatus . '.pkey';
			
		$sql .= $this->criteria;
        
        return $sql;
    }
    
    
	function validateForm($arr,$pkey = ''){
		  
		$arrayToJs = parent::validateForm($arr,$pkey); 
		  
		$name = $arr['selYear'];  
		$rs = $this->isValueExisted($pkey,'year',$name);	 
		if(count($rs) <> 0){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['investorRelations'][2]);
		}
        
        
//		$year = $arr['selYear'];  
//       
//        $rs = $this->searchDataRow(array( $this->tableName.'.pkey' ),' and '.$this->tableName.'.statuskey  = 1 and  '.$this->tableName.'.year = ' .$this->oDbCon->paramString($year) );
//     	if(!empty($rs)){
//			$this->addErrorList($arrayToJs,false,$this->errorMsg['investorRelations'][2]);
//		}
//		
       
		return $arrayToJs;
	 } 
	   
    
}
?>