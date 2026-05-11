<?php

class Event extends BaseClass{
 
   function __construct(){
		
		parent::__construct();
       
		$this->tableName = 'event';   
		$this->tableNameCategory = 'event_category';   
		$this->tableImage = 'event_image';
		$this->securityObject = 'event'; 
		$this->tableStatus = 'master_status';
	    $this->tableLangValue = 'event_lang';
		$this->uploadFolder = 'event/';
	      
        $this->arrDataImageDetail = array();  
        $this->arrDataImageDetail['pkey'] = array('hidDetailitem-image-uploaderKey');
        $this->arrDataImageDetail['refkey'] = array('pkey','ref');
        $this->arrDataImageDetail['file'] = array('hidNameitem-image-uploader',array('datatype' => 'image', 'uploadFolder' => $this->uploadFolder,  'token' => 'token-item-image-uploader', 'fileName' => 'hidNameitem-image-uploader'));
         
        $arrDetails = array(); 
        array_push($arrDetails, array('dataset' => $this->arrDataLang, 'tableName' => $this->tableLangValue));
        array_push($arrDetails, array('dataset' => $this->arrDataImageDetail, 'tableName' => $this->tableImage));
       
   	    $this->arrData = array(); 
        $this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails)); 
        $this->arrData['code'] = array('code');
        $this->arrData['title'] = array('title');
        $this->arrData['shortdesc'] = array('txtShortDescription');
        $this->arrData['eventdatefrom'] = array('txtEventDateFrom','datetime');
        $this->arrData['eventdateto'] = array('txtEventDateTo','datetime');
        $this->arrData['isfeatured'] = array('chkIsFeatured');
        $this->arrData['categorykey'] = array('hidCategoryKey'); 
        $this->arrData['statuskey'] = array('selStatus'); 
        $this->arrData['termsandconditionsdetail'] = array('txtTermsAndConditions','raw'); 
        $this->arrData['mechanismdetail'] = array('txtMechanism','raw'); 
        $this->arrData['detail'] = array('txtDescription','raw'); 
//        $this->arrData['image'] = array('item-image-uploader',array('datatype' => 'image', 'uploadFolder' => $this->uploadFolder,  'token' => 'token-item-image-uploader', 'fileName' => 'item-image-uploader'));
        $this->arrData['speakers'] = array('hostName');
       
	   	$this->newLoad=true;
	   
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'date','title' => 'date','dbfield' => 'eventdatefrom','default'=>true,  'width' => 130, 'align' =>'center', 'format' => 'datetime'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'name','title' => 'title','dbfield' => 'title', 'default'=>true, 'width' => 250));
        array_push($this->arrDataListAvailableColumn, array('code' => 'host','title' => 'host','dbfield' => 'speakers',  'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'category','title' => 'category','dbfield' => 'categoryname', 'default'=>true, 'width' => 150));
      	array_push($this->arrDataListAvailableColumn, array('code' => 'statusname','title' => 'status','dbfield' => 'statusname','default'=>true,  'width' => 100)); 
	   
	 
		$this->arrSearchColumn = array ();
		array_push($this->arrSearchColumn, array('Kode', $this->tableName . '.code'));
		array_push($this->arrSearchColumn, array('Judul', $this->tableName . '.title'));
		array_push($this->arrSearchColumn, array('kategori', $this->tableNameCategory . '.name'));
		array_push($this->arrSearchColumn, array('Deskripsi', $this->tableName . '.shortdesc'));
		//array_push($this->arrSearchColumn, array('Pengisi Acara', $this->tableName . '.speakers')); 

   }
   
   function getQuery(){
	   
	   return '
				select
					'.$this->tableName. '.*,
					'.$this->tableNameCategory. '.name as categoryname,
					'.$this->tableStatus.'.status as statusname 
				from 
					'.$this->tableName . '
                        left join '.$this->tableNameCategory.' on  '.$this->tableName . '.categorykey = '.$this->tableNameCategory.'.pkey
                    ,'.$this->tableStatus.'  
				where  		
					'.$this->tableName . '.statuskey = '.$this->tableStatus.'.pkey 
 		' .$this->criteria ; 
		 
    }  
	
		
    function delete($id, $forceDelete = false,$reason = ''){ 
		$arrayToJs =  array();
		 
		try{			 
				 
				$arrayToJs = $this->validateDelete($id);
				if (!empty($arrayToJs)) 
					return $arrayToJs;
		 		
				if (!$this->oDbCon->startTrans())
					throw new Exception($this->errorMsg[100]);
			 
				
				$sql = 'delete from  '.$this->tableName.' where pkey = ' . $this->oDbCon->paramString($id);
				$this->oDbCon->execute($sql);
	
				$this->deleteAll($this->defaultDocUploadPath.$this->uploadFolder.$id);
   
            
                $this->setTransactionLog(DELETE_DATA,$id);
            
				$this->oDbCon->endTrans();
										 
				$this->addErrorList($arrayToJs,true,$this->lang['dataHasBeenSuccessfullyUpdated']);    
			 
				
			}catch(Exception $e){
				$this->oDbCon->rollback();
				$this->addErrorList($arrayToJs,false, $e->getMessage()); 
		}			
			
		return $arrayToJs;	
	}

	 function validateForm($arr,$pkey = ''){
		   
		$arrayToJs = parent::validateForm($arr,$pkey); 
         
		$name = $arr['title'];  
	 	$datefrom = $arr['txtEventDateFrom'];
		 
        $arrImage = explode(",",$arr['item-image-uploader']);
        for($i=0;$i<count($arrImage);$i++){
            $path = $this->uploadTempDoc.$this->uploadFolder.$arr['token-item-image-uploader']; 
            if (filesize($path.'/'.$arrImage[$i]) > (pow(1024,2) * PLAN_TYPE['maximagesize']))
                $this->addErrorList($arrayToJs,false,$this->errorMsg['limit'][4] .' ('.$this->lang['max'].' '. $this->formatNumber(PLAN_TYPE['maximagesize']). ' MB)' );
      } 
		
		// judul event boleh sama
		//$rs = $this->isValueExisted($pkey,'title',$name);	 
		if(empty($name)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['event'][1]);
		}
	 
		 if(isset($arr['txtEventDateTo'])){
	 		$dateto = $arr['txtEventDateTo'];
			 $date = new DateTime(str_replace('\'','',$this->oDbCon->paramDate($dateto,' / ')));
			$date->add(new DateInterval('PT1M'));
			$dateto = $date->format('d / m / Y H:i');

			if(  $this->dateDiff($datefrom,$dateto)  <= 0){
				$this->addErrorList($arrayToJs,false,$this->errorMsg['date'][3]);
			}  
		 }
        
		  
		return $arrayToJs;
	 }
	
	 
	function sendEventEmail($rsCust,$rsEvent, $langCode){
		
        global $twig;
          
        $arrTwigVar = array();
        $arrTwigVar = $this->getDefaultEmailVariable();
        
        $arrTwigVar['CUSTOMER_NAME'] = $rsCust['name'];
        $arrTwigVar['HOST_NAME'] =  $rsEvent['speakers'];
        $arrTwigVar['EVENT_TITLE'] = $rsEvent['title'];
        $arrTwigVar['EVENT_START'] = $rsEvent['eventdatefrom'];

        $content = $twig->render($this->getLangTemplatePath('email-ilc.html',true,$langCode), $arrTwigVar);
        
		$title = 'ILC - '. $rsEvent['title']; 
		//$this->sendMail(array(), $title,$content,array('name' => $rsCust['name'], 'email'=>$rsCust['email']));
	 
		// kirim WA 
		// content WA harus bisa disetting per user
		if(!empty($this->loadSetting('WAGatewayAPIKey'))){ 
            $content = $twig->render($this->getLangTemplatePath('wa-ilc.html',true,$langCode), $arrTwigVar); 
  	
			$content = html_entity_decode(strip_tags($content)); 
            
            if(!empty($rsCust['mobilecode'])) $rsCust['mobile'] = $rsCust['mobilecode'] . $rsCust['mobile'];
			$this->sendWA($rsCust['mobile'],$content,true);
		}
		 
	}
	
	function normalizeParameter($arrParam, $trim = false){   
         
        $arrParam = $this->updateOthersLangValue($arrParam, $this->arrData); 
        $arrParam = parent::normalizeParameter($arrParam,true);  
        
        return $arrParam;
        
    }
	 
    
}

?>