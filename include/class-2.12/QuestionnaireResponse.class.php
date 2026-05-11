<?php 
class QuestionnaireResponse extends BaseClass{ 
 
    function __construct(){
		
		parent::__construct();
       
		$this->tableName = 'questionnaire_response_header';
		$this->tableNameDetail = 'questionnaire_response_detail';
        $this->tableQuestionnaireHeader = 'questionnaire_header';	  
		$this->tableQuestionnaireDetail = 'questionnaire_detail';	
        $this->tableStatus = 'master_status';
        $this->securityObject = 'Questionnaire';
        
        $this->arrDataDetail = array();  
        $this->arrDataDetail['pkey'] = array('hidDetailKey');
        $this->arrDataDetail['refkey'] = array('pkey','ref');
        $this->arrDataDetail['refquestionkey'] = array('hidQuestionKey'); 
        $this->arrDataDetail['answer'] = array('answer'); 
        $this->arrDataDetail['trdesc'] = array('trDesc');

        $arrDetails = array();
        array_push($arrDetails, array('dataset' => $this->arrDataDetail));
        
        $this->arrData = array();
        $this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails));
        $this->arrData['code'] = array('code');
        $this->arrData['trdate'] = array('trDate','date');
        $this->arrData['refkey'] = array('hidRefKey');  
        $this->arrData['userkey'] = array('userkey');
        $this->arrData['statuskey'] = array('selStatus'); 
         
        
        
//        $this->arrDataListAvailableColumn = array(); 
//        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 70));
//        array_push($this->arrDataListAvailableColumn, array('code' => 'date','title' => 'date','dbfield' => 'repeatdate','default'=>true, 'width' => 150, 'align' =>'center', 'format' => 'date'));
//        array_push($this->arrDataListAvailableColumn, array('code' => 'repeatEvery','title' => 'repeatEvery','dbfield' => 'repeatperiodname','default'=>true, 'width' => 150));
//        array_push($this->arrDataListAvailableColumn, array('code' => 'supplier','title' => 'supplier','dbfield' => 'suppliername','default'=>true,'width' => 150));
//        array_push($this->arrDataListAvailableColumn, array('code' => 'desc','title' => 'note','dbfield' => 'trdesc','default'=>true, 'width' => 200));
//        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
      
//        $this->actionMenu = array();  
//        array_push($this->actionMenu,array('code' => 'runNow', 'name' => $this->lang['runNow'],  'icon' => 'run', 'url' => '../cron/routineCost'));
//  
        $this->overwriteConfig();

   }
   
    function getQuery(){ 
        
        return '
			SELECT
                '.$this->tableName.'.*, 
                '.$this->tableStatus.'.status as statusname
			FROM 
                '.$this->tableName.',
                '.$this->tableStatus.'
            WHERE 
                '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey   

 		' .$this->criteria ;
		 
    }  
     
    function validateForm($arr,$pkey = ''){ 
                
		$arrayToJs = parent::validateForm($arr,$pkey);
        
          
		return $arrayToJs;
	 }
 
    function normalizeParameter($arrParam, $trim=false){
         
        // remove uncheck 
       
        $arrParam = parent::normalizeParameter($arrParam,true);  
        
    
        return $arrParam;
    }
    
    function getDetailWithRelatedInformation($pkey){
        $sql = 'select  
                    '.$this->tableQuestionnaireDetail.'.question,
                    '.$this->tableNameDetail.'.answer,
                    '.$this->tableNameDetail.'.trdesc
                from 
                    '.$this->tableNameDetail.',
                    '.$this->tableQuestionnaireDetail.'
                where
                    '.$this->tableNameDetail.'.refkey = '.$this->oDbCon->paramString($pkey).' and
                    '.$this->tableNameDetail.'.refquestionkey = '.$this->tableQuestionnaireDetail.'.pkey'
              ;
        
        return $this->oDbCon->doQuery($sql);
        
    }
    
    
}
?>
