<?php

class GiveOpportunity extends BaseClass{
	
    function __construct()
    {

        parent::__construct();

        $this->tableName = 'give_opportunity';
        $this->tableCustomer = 'customer';
        $this->tableCategory = 'opportunity_category';
        $this->tableStatus = 'give_opportunity_status';
        $this->tableWarehouse = 'warehouse';

        $this->isTransaction = true;
        $this->newLoad = true;

        $this->securityObject = 'GiveOpportunity'; //sementara transaction_status

        $this->arrData = array();
        $this->arrData['pkey'] = array('pkey');
        $this->arrData['refkey'] = array('refkey');
        $this->arrData['code'] = array('code');
        $this->arrData['name'] = array('name');
        $this->arrData['phone'] = array('phone');
        $this->arrData['typekey'] = array('selType');
        $this->arrData['torecipientkey'] = array('hidRecipientKey');
        $this->arrData['categorykey'] = array('selCategoryKey');
        $this->arrData['description'] = array('description');
        $this->arrData['trdesc'] = array('trDesc');
        
        $this->arrDataListAvailableColumn = array();
        array_push($this->arrDataListAvailableColumn, array('code' => 'code', 'title' => 'code', 'dbfield' => 'code', 'default' => true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'category', 'title' => 'category', 'dbfield' => 'categoryname', 'default' => true,  'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'name', 'title' => 'name', 'dbfield' => 'name', 'default' => true,  'width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'phone', 'title' => 'phone', 'dbfield' => 'phone', 'default' => true,  'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'amount', 'title' => 'amount', 'dbfield' => 'amount', 'default' => true,  'width' => 100, 'format' => 'number', 'align' => 'right'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'description', 'title' => 'description', 'dbfield' => 'description', 'default' => true,  'width' => 300));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status', 'title' => 'status', 'dbfield' => 'statusname', 'default' => true, 'width' => 90));


        $this->arrSearchColumn = array();
        array_push($this->arrSearchColumn, array('Kode', $this->tableName . '.code'));
        array_push($this->arrSearchColumn, array('Nama PIC', $this->tableName . '.name'));
        array_push($this->arrSearchColumn, array('Phone PIC', $this->tableName . '.phone')); 
        array_push($this->arrSearchColumn, array('Kategori',  $this->tableCategory . '.name'));

        $this->includeClassDependencies(array(
            'Warehouse.class.php',
            'Customer.class.php',
			'ActivityLog.class.php',
			'CustomerFeatures.class.php'
        ));

        $this->overwriteConfig();
    }


    function getQuery() {
 
        $sql = '
                 select
                     ' . $this->tableName . '.*,
                     ' . $this->tableCustomer . '.name as torecipientname,
                     ' . $this->tableCategory . '.name as categoryname,
                     ' . $this->tableStatus . '.status as statusname,
					 giver.code as givercode,
					 giver.name as givername
					 
                 from 
                     ' . $this->tableName . ' 
							left join ' . $this->tableCategory. ' on ' . $this->tableName . '.categorykey = ' . $this->tableCategory . '.pkey
							left join ' . $this->tableCustomer . ' on ' . $this->tableName . '.torecipientkey = ' . $this->tableCustomer . '.pkey
							left join ' . $this->tableCustomer . ' giver on ' . $this->tableName . '.refkey = giver.pkey,
                     ' . $this->tableStatus . '
                 where 
                     ' . $this->tableName . '.statuskey = ' . $this->tableStatus . '.pkey '
            . $this->criteria;
 
        return $sql;
    }

    function validateForm($arr, $pkey = '') {
        $arrayToJs = parent::validateForm($arr, $pkey);
		
		$customer = new Customer();
				 
		// khusus member
		 if ($arr['selType'] > 0){
			 if ( empty($arr['hidRecipientKey']) ) {
				  $this->addErrorList($arrayToJs, false, $this->errorMsg['customer'][1]); 
			 }else{
				  // khusus PRO
				  $rsCust = $customer->searchDataRow(array($customer->tableName.'.membershiplevel',$customer->tableName.'.hostlevelkey'),
							   'and '. $customer->tableName.'.pkey='. $this->oDbCon->paramString($arr['hidRecipientKey']).'
							    and '. $customer->tableName.'.membershiplevel >= 3'
							  );
				 
				  if (empty($rsCust))
				  	$this->addErrorList($arrayToJs, false, $this->errorMsg['giveOpportunity'][3]); 
					  
				 
				  if ($arr['hidRecipientKey'] == USERKEY)
				  	$this->addErrorList($arrayToJs, false, $this->errorMsg['giveOpportunity'][5]);  
			 }
		 }
		
				
		 if (isset($arr['description'])){
			 if(strlen($arr['description']) > 150)
                 $this->addErrorList($arrayToJs,false,$this->lang['maxDescriptionLength']);  
		 }
			
		 if (empty($arr['refkey'])){ 
			$this->addErrorList($arrayToJs, false, $this->errorMsg['customer'][1]); 
		 }else{
			 // khusus PRO
			  $rsCust = $customer->searchDataRow(array($customer->tableName.'.membershiplevel',$customer->tableName.'.hostlevelkey'),
						   'and '. $customer->tableName.'.pkey='. $this->oDbCon->paramString($arr['refkey']).'
							and '. $customer->tableName.'.membershiplevel >= 3'
						  );

			  if (empty($rsCust))
				$this->addErrorList($arrayToJs, false, $this->errorMsg['giveOpportunity'][3]); 
	 	}
      	
        return $arrayToJs;
    }


    function validateConfirm($rsHeader){

        $id = $rsHeader[0]['pkey'];

    }

    function confirmTrans($rsHeader)
    {

        $id = $rsHeader[0]['pkey']; 
    }

	
    function validateCancel($rsHeader, $autoChangeStatus = false) {
        $id = $rsHeader[0]['pkey'];
      
    }

    function cancelTrans($rsHeader, $copy)
    {
        $id = $rsHeader[0]['pkey']; 

        if ($copy)
            $this->copyDataOnCancel($id); 
    }
 
    function normalizeParameter($arrParam, $trim = false){
        
        if($arrParam['selType'] == 0)
            $arrParam['hidRecipientKey'] = 0;
		 
        $arrParam = parent::normalizeParameter($arrParam, true);
        return $arrParam;
    }
    

	 function afterUpdateData($arrParam, $action){ 
		
		if ($action == INSERT_DATA)	{ 
			
			$customerFeatures = new CustomerFeatures();
			$customerFeatures->updateMembershipFeaturesCounter($arrParam['refkey'], 'referBusiness');
			$customerFeatures->updateMembershipAchievementsCounter($arrParam['refkey'], 'referBusiness');
			
			$arrActivityLog = array();
			if($arrParam['selType'] == 0){
				array_push($arrActivityLog, 
												array(
														'modulekey' => 7,
														'templatekey' => 19, 
														'refkey' => 0 ,  
														'userkey' => $arrParam['refkey'],  
													) 
											  ); 	
			}else{
				array_push($arrActivityLog, 
												array(
														'modulekey' => 7,
														'templatekey' => 20,  
														'refkey' => $arrParam['hidRecipientKey'],
														'userkey' => $arrParam['refkey'],  
													) 
											  ); 
				
				array_push($arrActivityLog, 
												array(
														'modulekey' => 7,
														'templatekey' => 21, 
														'refkey' =>  $arrParam['refkey'],  
														'userkey' => $arrParam['hidRecipientKey'],  
													) 
											  ); 
			}

			$activityLog = new ActivityLog();
			$activityLog->addNewLog($arrActivityLog); 
			
			if(!empty($arrParam['hidRecipientKey']))
				$this->sendOpportunityEmail($arrParam['pkey']);
			
		}

		
	 }
	
	function getCategoryType($pkey = '')  {
        $sql = 'SELECT * FROM ' . $this->tableCategory . ' WHERE 1=1';

        if (!empty($pkey))
            $sql .= ' and ' . $this->tableCategory . '.pkey in (' . $this->oDbCon->paramString($pkey).')';

        return $this->oDbCon->doQuery($sql);
    }
	
	function updateFollowUp($arr){
		$arrayToJs = array();
		
		try{		   
			 	
				if (!$this->oDbCon->startTrans(true))
					throw new Exception($class->errorMsg[100]);
 	
				$rs = $this->getDataRowById($arr['hidRefKey']);
			
				$sql  = 'update '.$this->tableName.' set isfollowup = 1,  statuskey = 2 where '.$this->tableName.'.pkey = ' .  $this->oDbCon->paramString($arr['hidRefKey']);
			
				if(isset($arr['fromFE']) && $arr['fromFE'] == 1)
					$sql .= ' and '.$this->tableName.'.torecipientkey = '. $this->oDbCon->paramString($arr['hidUserKey']);
					 
				$this->oDbCon->execute($sql);  

				$arrActivityLog = array();
				array_push($arrActivityLog, 
									array(
											'modulekey' => 7,
											'templatekey' => 22, 
											'refkey' =>  $arr['hidUserKey'],
											'userkey' => $rs[0]['refkey'],  
										) 
						   ); 
			
				array_push($arrActivityLog, 
									array(
											'modulekey' => 7,
											'templatekey' => 23, 
											'refkey' =>  $rs[0]['refkey'],  
											'userkey' => $arr['hidUserKey'],  
										) 
						   ); 

				$activityLog = new ActivityLog();
				$activityLog->addNewLog($arrActivityLog); 
			
				$this->oDbCon->endTrans();  
			
				$this->sendFollowUpEmail($arr['hidRefKey']);
			
				$this->addErrorList($arrayToJs,true,$this->lang['dataHasBeenSuccessfullyUpdated']);    


		}catch(Exception $e){
				$this->oDbCon->rollback();
				$this->addErrorList($arrayToJs,false, $e->getMessage()); 
		}
	}
	
	
	function updateNoDeal($arr){
		$arrayToJs = array();
		
		try{		   
			 	
				if (!$this->oDbCon->startTrans(true))
					throw new Exception($class->errorMsg[100]);
 	
				$rs = $this->getDataRowById($arr['hidRefKey']);
			
				$sql  = 'update '.$this->tableName.' set statuskey = 4 where '.$this->tableName.'.pkey = ' .  $this->oDbCon->paramString($arr['hidRefKey']);
			
				if(isset($arr['fromFE']) && $arr['fromFE'] == 1)
					$sql .= ' and '.$this->tableName.'.torecipientkey = '. $this->oDbCon->paramString($arr['hidUserKey']);
					 
				$this->oDbCon->execute($sql);  

				$arrActivityLog = array();
				array_push($arrActivityLog, 
									array(
											'modulekey' => 7,
											'templatekey' => 24, 
											'refkey' =>  $arr['hidUserKey'],
											'userkey' => $rs[0]['refkey'],  
										) 
						   ); 
			
				array_push($arrActivityLog, 
									array(
											'modulekey' => 7,
											'templatekey' => 25, 
											'refkey' =>  $rs[0]['refkey'],  
											'userkey' => $arr['hidUserKey'],  
										) 
						   ); 

				$activityLog = new ActivityLog();
				$activityLog->addNewLog($arrActivityLog); 
			
				$this->oDbCon->endTrans();  
			
				$this->sendDealEmail($arr['hidRefKey']);
			
				$this->addErrorList($arrayToJs,true,$this->lang['dataHasBeenSuccessfullyUpdated']);    


		}catch(Exception $e){
				$this->oDbCon->rollback();
				$this->addErrorList($arrayToJs,false, $e->getMessage()); 
		}
	}
	 
	function updateDeal($arr){
		$arrayToJs = array();
		
		$amount = $this->unformatNumber($arr['amount']);
	 
		if ($amount <= 0)
			$this->addErrorList($arrayToJs,false,$this->errorMsg['giveOpportunity'][4]);    
		
		if(!empty($arrayToJs))  	
			return $arrayToJs; 
		
		try{		   
			 	
				if (!$this->oDbCon->startTrans(true))
					throw new Exception($class->errorMsg[100]);
 	
				$rs = $this->getDataRowById($arr['hidRefKey']);
			
				$sql  = 'update '.$this->tableName.' set statuskey = 3, isdeal= 1, amount = '. $this->oDbCon->paramString($amount).' where '.$this->tableName.'.pkey = ' .  $this->oDbCon->paramString($arr['hidRefKey']);
			
				if(isset($arr['fromFE']) && $arr['fromFE'] == 1)
					$sql .= ' and '.$this->tableName.'.torecipientkey = '. $this->oDbCon->paramString($arr['hidUserKey']);
					 
				$this->oDbCon->execute($sql);  

				$arrActivityLog = array();
				array_push($arrActivityLog, 
									array(
											'modulekey' => 7,
											'templatekey' => 26, 
											'refkey' =>  $arr['hidUserKey'],
											'amount' =>  $amount,
										) 
						   ); 
			
				array_push($arrActivityLog, 
									array(
											'modulekey' => 7,
											'templatekey' => 27, 
											'refkey' =>  $rs[0]['refkey'],  
											'userkey' => $arr['hidUserKey'], 
											'amount' =>  $amount, 
										) 
						   ); 

				$activityLog = new ActivityLog();
				$activityLog->addNewLog($arrActivityLog); 
			
				$this->oDbCon->endTrans();  
			
				$this->sendDealEmail($arr['hidRefKey']);
			
				$this->addErrorList($arrayToJs,true,$this->lang['dataHasBeenSuccessfullyUpdated']);    


		}catch(Exception $e){
				$this->oDbCon->rollback();
				$this->addErrorList($arrayToJs,false, $e->getMessage()); 
		} 
		
		return $arrayToJs;
	}
	
	function getTotalOpportunity($customerkey,$datePeriod = ''){
		
		if(empty($datePeriod)) $datePeriod = date('01 / m / Y');
			
		$total = 0;
		
		$generalCriteria = $this->tableName.'.statuskey in (1,2,3) ';
		 
		// tambah counter sebagai host  
		$sql = 'select coalesce(count('.$this->tableName.'.pkey),0)  as total
				from '.$this->tableName.' 
				where 
					'.$generalCriteria .' and
					'.$this->tableName.'.refkey = ' . $this->oDbCon->paramString($customerkey) .' and
					month('.$this->tableName.'.createdon) = '.$this->oDbCon->paramDate($datePeriod,'/', 'm').' and 
					year('.$this->tableName.'.createdon) = '.$this->oDbCon->paramDate($datePeriod,'/', 'Y').'
				';
	
		
		$rs = $this->oDbCon->doQuery($sql);
		$total += $rs[0]['total'];
			
		return $total ;
		
	}
	
	function sendFollowUpEmail($pkey){
		// karena bisa sj data dr backend
		require_once  $_SERVER ['DOCUMENT_ROOT'].'/_include-twig.php';  
		$customer = new Customer();
		
		// kirim email 
        $rs = $this->getDataRowById($pkey);
		$rsRecipient = $customer->getDataRowById($rs[0]['torecipientkey']);
		$rsCust = $customer->getDataRowById($rs[0]['refkey']);
		
        $arrTwigVar = array();
        $arrTwigVar = $this->getDefaultEmailVariable();
         
        $arrTwigVar['CUSTOMER_NAME'] = $rsCust[0]['name'];
        $arrTwigVar['RECIPIENT_NAME'] = $rsRecipient[0]['name'];  
		
        $lang = new Lang();
        $rsLang = $lang->searchDataRow(array($lang->tableName.'.code'),
                                ' and '.$lang->tableName.'.pkey = '.$this->oDbCon->paramString($rsCust[0]['langkey'])
                              );
        
        $content = $twig->render($this->getLangTemplatePath('email-followup.html',true,$rsLang[0]['code']), $arrTwigVar);
        $this->setLog($content,true);
        $this->sendMail(array(), $this->lang['opportunity'] . ' - ' . DOMAIN_NAME,$content,array('name' => $rsCust[0]['name'], 'email'=>$rsCust[0]['email'])); 
        
		// kirim WA
		// content WA harus bisa disetting per user
		if(!empty($this->loadSetting('WAGatewayAPIKey'))){ 
			$content = $twig->render($this->getLangTemplatePath('wa-followup.html',true,$rsLang[0]['code']), $arrTwigVar);
			$content = html_entity_decode(strip_tags($content));
            $this->setLog($content,true);
			
            if(!empty($rsCust[0]['mobilecode'])) $rsCust[0]['mobile'] = $rsCust[0]['mobilecode'] . $rsCust[0]['mobile'];
			$this->sendWA($rsCust[0]['mobile'],$content,true);
		}
		 
	}
    
	function sendDealEmail($pkey){
		require_once  $_SERVER ['DOCUMENT_ROOT'].'/_include-twig.php'; 
		$customer = new Customer();
		
		// kirim email 
        $rs = $this->getDataRowById($pkey);
		$rsRecipient = $customer->getDataRowById($rs[0]['torecipientkey']);
		$rsCust = $customer->getDataRowById($rs[0]['refkey']);
		
        $arrTwigVar = array();
        $arrTwigVar = $this->getDefaultEmailVariable();
         
        $arrTwigVar['CUSTOMER_NAME'] = $rsCust[0]['name'];
        $arrTwigVar['RECIPIENT_NAME'] = $rsRecipient[0]['name']; 
        $arrTwigVar['DEAL_NODEAL'] = ($rs[0]['isdeal'] == 1) ? 'DEAL' : 'NO DEAL';  
		
        $lang = new Lang();
        $rsLang = $lang->searchDataRow(array($lang->tableName.'.code'),
                                ' and '.$lang->tableName.'.pkey = '.$this->oDbCon->paramString($rsCust[0]['langkey'])
                              );
        
        $content = $twig->render($this->getLangTemplatePath('email-deal.html',true,$rsLang[0]['code']), $arrTwigVar);
        $this->sendMail(array(), $this->lang['opportunity'] . ' - ' . DOMAIN_NAME,$content,array('name' => $rsCust[0]['name'], 'email'=>$rsCust[0]['email'])); 
        
		// kirim WA
		// content WA harus bisa disetting per user
		if(!empty($this->loadSetting('WAGatewayAPIKey'))){  
            $content = $twig->render($this->getLangTemplatePath('wa-deal.html',true,$rsLang[0]['code']), $arrTwigVar);
			$content = html_entity_decode(strip_tags($content));
            
            if(!empty($rsCust[0]['mobilecode'])) $rsCust[0]['mobile'] = $rsCust[0]['mobilecode'] . $rsCust[0]['mobile'];
			$this->sendWA($rsCust[0]['mobile'],$content,true);
		}
		 
	}
	
	function sendOpportunityEmail($pkey){
		require_once  $_SERVER ['DOCUMENT_ROOT'].'/_include-twig.php';  
		$customer = new Customer();
		
		// kirim email 
        $rs = $this->getDataRowById($pkey);
		if(empty($rs[0]['torecipientkey'])) return;
		
		$rsSender = $customer->getDataRowById($rs[0]['refkey']); 
		$rsCust = $customer->getDataRowById($rs[0]['torecipientkey']); 
		
        $arrTwigVar = array();
        $arrTwigVar = $this->getDefaultEmailVariable();
         
        $arrTwigVar['CUSTOMER_NAME'] = $rsCust[0]['name'];
        $arrTwigVar['SENDER_NAME'] = $rsSender[0]['name'];
		
        $lang = new Lang();
        $rsLang = $lang->searchDataRow(array($lang->tableName.'.code'),
                                ' and '.$lang->tableName.'.pkey = '.$this->oDbCon->paramString($rsCust[0]['langkey'])
                              );

        $content = $twig->render($this->getLangTemplatePath('email-opportunity.html',true,$rsLang[0]['code']), $arrTwigVar);
  
        $this->sendMail(array(), $this->lang['opportunity'] . ' - ' . DOMAIN_NAME,$content,array('name' => $rsCust[0]['name'], 'email'=>$rsCust[0]['email'])); 
        
		// kirim WA
		// content WA harus bisa disetting per user
		if(!empty($this->loadSetting('WAGatewayAPIKey'))){ 
			$content = $twig->render($this->getLangTemplatePath('wa-opportunity.html',true,$rsLang[0]['code']), $arrTwigVar);
			$content = html_entity_decode(strip_tags($content));
	 
            if(!empty($rsCust[0]['mobilecode'])) $rsCust[0]['mobile'] = $rsCust[0]['mobilecode'] . $rsCust[0]['mobile'];
			$this->sendWA($rsCust[0]['mobile'],$content,true);
		}
		 
	}
    
	function countSummary(){
		$sql = 'select count('.$this->tableName.'.pkey) as totaldata, sum('.$this->tableName.'.amount) as transactionamount from '.$this->tableName.' where '.$this->tableName.'.statuskey <> 5';
		$rs = $this->oDbCon->doQuery($sql);
		
		return array('businessRefer' =>  $rs[0]['totaldata'], 'transactionAmount' => $rs[0]['transactionamount']);
	}
	
}
