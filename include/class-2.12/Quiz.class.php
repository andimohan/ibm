<?php

class Quiz extends BaseClass{
	
    function __construct(){

    parent::__construct();

    $this->tableName = 'quiz_header';
    $this->tableNameDetail = 'quiz_detail';
    $this->tableNameItemDetail = 'quiz_multichoice_detail';
    $this->tableNameDescDetail = 'quiz_description_detail';
    $this->tableNameResult = 'quiz_result';
    $this->tableWarehouse = 'warehouse';   
    $this->tableStatus = 'master_status';
    $this->uploadFolder = 'quiz/';
    $this->securityObject = 'Quiz'; 
        
    $this->arrItem = array();  
    $this->arrItem['pkey'] = array('hidDetailItemKey');
    $this->arrItem['refkey'] = array('hidDetailKey','ref');  
    $this->arrItem['refheaderkey'] = array('pkey','ref');  
    $this->arrItem['answers'] = array('answers');
    $this->arrItem['isanswer'] = array('chkIsAnswer');
        
    $this->arrDataDetail = array(); 
    $this->arrDataDetail['pkey'] = array('hidDetailKey', array('dataDetail' => array('dataset' => $this->arrItem, 'tableName' => $this->tableNameItemDetail)));
    $this->arrDataDetail['refkey'] = array('pkey','ref'); 
    $this->arrDataDetail['question'] = array('question','raw'); 
                    
    $this->arrDesc = array();  
    $this->arrDesc['pkey'] = array('hidDetailItemDescKey');
    $this->arrDesc['refkey'] = array('pkey','ref');  
    $this->arrDesc['fromvalue'] = array('from','number');  
    $this->arrDesc['tovalue'] = array('to','number');   
    $this->arrDesc['level'] = array('level','number');   
    $this->arrDesc['description'] = array('descDetail','raw');  
        
    $arrDetails = array();
    array_push($arrDetails, array('dataset' => $this->arrDataDetail, 'tableName' => $this->tableNameDetail));
    array_push($arrDetails, array('dataset' => $this->arrDesc, 'tableName' => $this->tableNameDescDetail));
        
    $this->arrData = array(); 
    $this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails));   
    $this->arrData['code'] = array('code');
    $this->arrData['warehousekey'] = array('selWarehouseKey');
    $this->arrData['name'] = array('name');
    $this->arrData['trdate'] = array('trDate','date');
    $this->arrData['description'] = array('description');
    $this->arrData['statuskey'] = array('selStatus');
    $this->arrData['uploadfile'] = array('fileName'); 


    $this->arrDataListAvailableColumn = array(); 
    array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 120));
    array_push($this->arrDataListAvailableColumn, array('code' => 'name','title' => 'name','dbfield' => 'name','default'=>true, 'width' => 150));
    array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
         
    }

    function getQuery(){
        
        
        $sql = '
            SELECT
                '.$this->tableName.'.* ,  
                '.$this->tableWarehouse.'.name as warehousename,
                '.$this->tableStatus.'.status as statusname
                
            FROM '.$this->tableStatus.',
                 '.$this->tableWarehouse.',
                 '.$this->tableName.'
            WHERE   
                  '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey and
                  '.$this->tableName.'.warehousekey = '.$this->tableWarehouse.'.pkey 

            ' .$this->criteria ;
                                         
        return $sql;
    }

    function validateForm($arr,$pkey = ''){ 

        $arrayToJs = parent::validateForm($arr,$pkey); 
        
        $detailQuestion = $arr['question'];
        $detailAnswers = $arr['answers'];
        $chkisanswer = $arr['chkIsAnswer'];
        
        foreach($detailQuestion as $question){   
            if(empty($question))
                $this->addErrorList($arrayToJs,false, $this->errorMsg['question'][1]); 
        }
        
        foreach($detailAnswers as $row){  
            foreach($row as $value)   
                if(empty($value))
                    $this->addErrorList($arrayToJs,false, $this->errorMsg['answer'][1]); 
        }
        
        return $arrayToJs;
    }

    
    function normalizeParameter($arrParam, $trim=false){
        
        $details = array();
        array_push($details,$this->arrItem); 
        $arrParam = $this->prepareMultiLevelDetail($arrParam,$details);
        $arrParam['fileName'] = $this->updateImages($arrParam['pkey'], $arrParam['token-item-image-uploader'], $arrParam['item-image-uploader'],'',$this->uploadFolder); 

        $arrParam = parent::normalizeParameter($arrParam,true); 
        
        return $arrParam;
    }


    function getDetailWithRelatedInformation($pkey,$criteria=''){
        $sql = 'select
            '.$this->tableNameDetail.'.*
          from
            '.$this->tableNameDetail.'
          where  
            '. $this->tableNameDetail.'.refkey in  ('.$this->oDbCon->paramString($pkey,',') . ') ' ;

        $sql .= $criteria;
  
        return $this->oDbCon->doQuery($sql);

    }
      
    function getRandomQuestion($pkey,$totalQuestion=0){
        
        $sql = 'select '.$this->tableNameDetail.'.* from  '.$this->tableNameDetail.' where refkey = '.$this->oDbCon->paramString($pkey);
        $rs = $this->oDbCon->doQuery($sql);
        $rsRand = array_rand($rs,$totalQuestion);
         
        $limit = ($totalQuestion > 0) ? $totalQuestion : count($rs);
        
        $arrReturn = array();
        for($i=0;$i<$limit;$i++)
            array_push($arrReturn,$rs[$rsRand[$i]]);
         
        return $arrReturn; 
    }
      
    function getDescDetail($refkey){
        $sql = 'select 
                    ' .$this->tableNameDescDetail.'.*
               from 
                    ' .$this->tableNameDescDetail.'
                where 
                    refkey = ' . $this->oDbCon->paramString($refkey);

        return $this->oDbCon->doQuery($sql);
    }
     function getItemDetail($refkey, $refdetailkey = ''){
        $sql = 'select 
                    ' .$this->tableNameItemDetail.'.*
               from 
                    ' .$this->tableNameItemDetail.'
                where 
                    refkey = ' . $this->oDbCon->paramString($refkey);
        
        if (!empty($refdetailkey))
            $sql .= ' and ' .$this->tableNameItemDetail.'.pkey = ' . $this->oDbCon->paramString($refdetailkey);
        
        return $this->oDbCon->doQuery($sql);
    }
    
    
	 function checkAnswers($arr,$pkey = ''){
		    
		$arrayToJs = parent::validateForm($arr,$pkey); 
          
		 
        if (!IS_DEVELOPMENT){ 
			   $captchaResponse = $arr['g-recaptcha-response'];  
				$secretkey = $this->loadSetting('reCaptchaSecretKey');

				// post request to server
				$url = 'https://www.google.com/recaptcha/api/siteverify';
				$data = array('secret' => $secretkey, 'response' => $captchaResponse);

				$options = array(
					'http' => array(
						'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
						'method'  => 'POST',
						'content' => http_build_query($data)
						)
				);

				$context  = stream_context_create($options);
				$response = file_get_contents($url, false, $context);
				$responseKeys = json_decode($response,true);

				if(empty($responseKeys) || !$responseKeys["success"]) {
				  $this->addErrorList($arrayToJs,false,$this->errorMsg['captcha'][1]);
				}

		}
		 
	 
         if(empty($arr['name']))
			$this->addErrorList($arrayToJs,false,$this->errorMsg['name'][1]);
		 
		 if(empty($arr['phone']))
			$this->addErrorList($arrayToJs,false,$this->errorMsg['phone'][1]);
		 
         if(isset($arr['email']) && !empty($arr['email'])){
            $email = $arr['email'];
  
			if(!filter_var($email, FILTER_VALIDATE_EMAIL)) 
				$this->addErrorList($arrayToJs,false,$this->errorMsg['email'][3]);
			 
		 } else{
        	$this->addErrorList($arrayToJs,false,$this->errorMsg['email'][1]);     
         }
         
         // klao ad pertanyaan yg gk dijawab
         // tarik dulu semua pertanyaan, agar bisa diambil pkeynya
         // kalo nanti bisa random pertanyaannya, maka dicek totalnya sama tdk dengan yg digenerate
         
         // kalo gk dijawab anggap aj salah
         
         $arrQuestion = $arr['hidQuestionKey'];
         
         $arrQA = array(); 
         foreach($arrQuestion as $questionKey) 
            array_push($arrQA,array($questionKey,$arr['question-'.$questionKey]));
         
         //$this->setLog($arrQA,true);
         
         /*
         foreach($rsQuizQuestion as $row){
            if(empty($arr['question-'.$row['pkey']])){
                $this->addErrorList($arrayToJs,false,$this->errorMsg['quiz'][2]);     
                break;
            }
         }*/
         
		// kalo ad error return
        if(!empty($arrayToJs))   return $arrayToJs;
         

		$arrResult =  array(); 
         
		$quizkey = $arr['headerQuizKey'];
		foreach($arrQA as  $value){
            // kalo gk diisi
            if (empty($value[1])){
                $arrResult[0] +=1; 
                continue;
            } 
            
			$rsAnswer = $this->getItemDetail($value[0],$value[1]); 
			if($rsAnswer[0]['isanswer'])
				$arrResult[1] +=1;
            else
				$arrResult[0] +=1; 
		}
        
		try{ 
			
			if(!$this->oDbCon->startTrans())
				throw new Exception($this->errorMsg[100]);
	 
			$sql = 'insert into '.$this->tableNameResult.' (
                            refkey,
                            name, 
                            email, 
                            phone, 
                            rightanswer,
							wronganswer,
                            createdon
                         ) values (
                            '.$this->oDbCon->paramString($quizkey).',
                            '.$this->oDbCon->paramString($arr['name']).',
                            '.$this->oDbCon->paramString($arr['email']).',
                            '.$this->oDbCon->paramString($arr['phone']).',
                            '.$this->oDbCon->paramString($arrResult[1]).',
                            '.$this->oDbCon->paramString($arrResult[0]).',
                            now()
                        )';
			
			$this->oDbCon->execute($sql);
			 
			$this->oDbCon->endTrans();
            
			$this->addErrorList($arrayToJs,true,$this->lang['dataHasBeenSuccessfullyUpdated']);   
		
            $arrRecipient = array(
                'name' => $arr['name'],
                'email' => $arr['email'],
                'phone' => $arr['phone'] 
            );
            
            $rsQuiz = $this->getDataRowById($arr['headerQuizKey']); 
            $this->mailResult($rsQuiz, $arrResult,$arrRecipient);
            
	    } catch(Exception $e){
			$this->oDbCon->rollback();
			$this->addErrorList($arrayToJs,false,$e->getMessage()); 
		}
         
        
		
		return $arrayToJs;
	 }
    
    function mailResult($rsQuiz, $arrResult, $recipient){ 
        
            $id = $rsQuiz[0]['pkey'];
            $correctAnswer = $arrResult[1];
		
			// sementara gannti utk IELTS
			$score = 0; 
		
			if ($correctAnswer >= 10)
				$score = 9;
			elseif ($correctAnswer >= 9)
				$score = 8;
			elseif ($correctAnswer >= 8)
				$score = 7;
			elseif ($correctAnswer >= 7)
				$score = 6.5;
			elseif ($correctAnswer >= 6)
				$score = 6;
			elseif ($correctAnswer >= 5)
				$score = 5.5;
			elseif ($correctAnswer >= 4)
				$score = 4.5;
			elseif ($correctAnswer >= 3)
				$score = 4;
			elseif ($correctAnswer >= 2)
				$score = 3;
			else
				$score = 2;
   
		
			if($score >= 5)
				$correctAnswer = 9; // dummy saja
			if($score >= 4)
				$correctAnswer = 8; // dummy saja
			else
				$correctAnswer = 0; // dummy saja
				
		
            $sql = 'select * from '.$this->tableNameDescDetail.' where 
                    refkey = '.$this->oDbCon->paramString($id).' and
                    fromvalue <= '.$this->oDbCon->paramString($correctAnswer).'
                    order by fromvalue desc limit 1';
         
            $rsQuizTiering = $this->oDbCon->doQuery($sql);
              
            $content = 'Thank you for participating in <b>'.$rsQuiz[0]['name'].'</b>. <br>
Here is your test result:<br><br>
Level : <b>'.$rsQuizTiering[0]['level'].'</b><br><br>
' .$rsQuizTiering[0]['description'];
         
            $this->sendMail(array(), $this->loadSetting('companyName') . ' - Your Test Result' ,$content,array('email'=>$recipient['email'])); 
    }

}

?>
