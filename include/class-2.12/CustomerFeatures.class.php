<?php  

class CustomerFeatures extends BaseClass{
 
   function __construct(){
		
		parent::__construct(); 
		$this->tableName = 'customer_features';
		$this->tableAchievements = 'customer_achievements';
	    $this->tableCustomerFeaturesDetail = 'customer_features_detail'; 
	    $this->tableCustomerAchievementDetail = 'customer_achievements_detail'; 
		$this->tableStatus = 'master_status';  
		$this->securityObject = 'CustomerFeatures';
        $this->newLoad = true; 
 
        $this->arrData = array();
        $this->arrData['pkey'] = array('pkey');
        $this->arrData['code'] = array('code'); 
        $this->arrData['name'] = array('name');
        $this->arrData['statuskey'] = array('selStatus');  
 
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 70));
        array_push($this->arrDataListAvailableColumn, array('code' => 'name','title' => 'name','dbfield' => 'name','default'=>true,'width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
        
        $this->arrSearchColumn = array(); 
        array_push($this->arrSearchColumn, array('Kode', $this->tableName . '.code'));
        array_push($this->arrSearchColumn, array('Nama', $this->tableName . '.name'));


        $this->includeClassDependencies(array(
              'Customer.class.php',
			  'MembershipLevel.class.php',
			  'MeetingSchedule.class.php',
			  'GiveOpportunity.class.php',
			  'MembershipSubscription.class.php'
        )); 
         
		$this->overwriteConfig(); 
   }
	 
	 
	//  wajib
    function getQuery(){
	   
	   return '
				select
					'.$this->tableName. '.*,
					'.$this->tableStatus.'.status as statusname 
				from 
					'.$this->tableName . ','.$this->tableStatus.'
				where  		
					'.$this->tableName . '.statuskey = '.$this->tableStatus.'.pkey
 		' .$this->criteria ; 
		 
    }
    function validateForm($arr,$pkey = ''){
		  
		$arrayToJs = parent::validateForm($arr,$pkey); 
          
        // validasi tambahan
        $name = $arr['name'];  
        
        if(empty($name)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['name'][1]);
		}else{
               
			$rsFeatures = $this->isValueExisted($pkey,'name',$name);	
			if(count($rsFeatures) <> 0) 
				$this->addErrorList($arrayToJs,false,$this->errorMsg['name'][2]);
	 
		}
        
        
		return $arrayToJs;
	 }     
	
    function normalizeParameter($arrParam, $trim = false){  
            
        $arrParam = parent::normalizeParameter($arrParam,true); 
        return $arrParam; 
    }   
    
	
	function getFeaturesQuota($customerkey, $opt = array()){
		 
		// untuk ambil informasi quota features
		// ujung2nya manggil $customer->getFeaturesDetail utk cek quota terpakai, TAPI
		// function ini returnnya array dengan index increment
		
		$featurekey = (isset($opt['featurekey'])) ? $opt['featurekey'] : '' ;
		$funckey = (isset($opt['funckey'])) ? $opt['funckey'] : '' ;
		$period = (isset($opt['period'])) ? $opt['period'] : date('01 / m / Y');
		
		// get semua features
		if(empty($featurekey)){
			$rsFeatures = $this->searchDataRow(array($this->tableName.'.pkey'),
													   ' and '.$this->tableName.'.functionkey = ' . $this->oDbCon->paramString($funckey,',').'
													     and '.$this->tableName.'.statuskey = 1',
													   'order by pkey asc limit 1'
													  );
			if(empty($rsFeatures)) return array();
			
			$featurekey = array_column($rsFeatures,'pkey');
		}
		
		// kalo $featurekey di kirim bentuk int
		if(!is_array($featurekey))
			$featurekey = array($featurekey);
		
		$customer = new Customer();
		
		$rsFeatured = $this->getFeaturesDetail($customerkey, array('period' => $period,'featurekey' => $featurekey[0], 'showedOnly' => false));
		$rsFeatured = array_column($rsFeatured,null,'featurekey');
		  
		// return dalam bentuk array, agar kedepan ready kalo sekaligus select beberapa features
		$arrReturn = array();
		foreach($featurekey as $key) { 
			$quota = (isset($rsFeatured[$key])) ? $rsFeatured[$key]['quota'] : 0;
			$quotaused = (isset($rsFeatured[$key])) ? $rsFeatured[$key]['quotaused'] : 0;
			array_push($arrReturn, array('featurekey' => $key, 'quota' =>  $quota, 'quotaused' => $quotaused )); 
		}
		
		return $arrReturn;	
	}
	
	  function getFeaturesDetail($pkey,$opt=array()){ 
		
		$membershipLevel = new MembershipLevel();
		$customer = new Customer();
		  
		// utk load detail form,
		// berbeda dengan $customerFeatures->getFeaturesQuota, function ini return keynya adalah featurekey
		 
		$showedOnly = (isset($opt['showedOnly'])) ? $opt['showedOnly'] : true ;
          //jika achievement
		$isAchievement = (isset($opt['isAchievement'])) ? $opt['isAchievement'] : false ;
          
        $dateFormat = (!$isAchievement)  ? date('01 / m / Y') : date('01 / 01 / Y');
		$datePeriod = (isset($opt['period'])) ? $opt['period'] :  $dateFormat;
		  
		$arrFeatureKey = (isset($opt['featurekey'])) ? $opt['featurekey'] : array() ;
		//$funckey = (isset($opt['funckey'])) ? $opt['funckey'] : array() ;
		
		 if(!is_array($arrFeatureKey))
			 $arrFeatureKey = array($arrFeatureKey);
		
		 // cari level customer agar tau quota nya
		$rsCustomer = $customer->searchDataRow( array($customer->tableName.'.pkey', $customer->tableName.'.membershiplevel'), 
										   ' and '. $customer->tableName.'.pkey = '. $this->oDbCon->paramString($pkey)
										   );
	
		$membershipLevelCriteria = '';
		if($showedOnly) $membershipLevelCriteria .= ' and '.$membershipLevel->tableCustomerFeatures . '.isshow = 1';
		if(!empty($arrFeatureKey)) $membershipLevelCriteria .= ' and '.$membershipLevel->tableMembershipLevelDetail . '.featurekey in ('. $this->oDbCon->paramString($arrFeatureKey,',').')';
			
		$rsFeaturesDetail = (!empty($rsCustomer[0]['membershiplevel'])) ? $membershipLevel->getFeaturesDetail($rsCustomer[0]['membershiplevel'],$membershipLevelCriteria) : array();
		
        $criteria = '';
        if(!empty($datePeriod))         
			$criteria .= ' and month(dateperiod) = ' .$this->oDbCon->paramDate($datePeriod,' / ','m'). '  and year(dateperiod) = ' .$this->oDbCon->paramDate($datePeriod,' / ','Y');
		
        if(!empty($arrFeatureKey))  
			$criteria .= ' and featurekey in ('.$this->oDbCon->paramString($arrFeatureKey,',').') ';
		 
		   
		// utk ambil detail dan quota terpakai
        $sql = (!$isAchievement) ? 
			  'select * from ' . $this->tableCustomerFeaturesDetail . ' where refkey = ' . $this->oDbCon->paramString($pkey).$criteria :
		  	  'select * from ' . $this->tableCustomerAchievementDetail . ' where refkey = ' . $this->oDbCon->paramString($pkey).$criteria;  
		  
        $rs = $this->oDbCon->doQuery($sql);
          
		$rs = array_column($rs,null,'featurekey');
		
		// cocokin dengan $rsFeaturesDetail
		// kalo ad featureskey yg dikirim better difilter
		 
		foreach($rsFeaturesDetail as $key=>$row){
			
			if(!isset($rs[$row['featurekey']])){
				$rsFeaturesDetail[$key]['quotaused'] = 0;
				continue;
			}
			
			$arrFeatures = $rs[$row['featurekey']]; 
			$rsFeaturesDetail[$key]['quotaused'] = (!empty($arrFeatures['quotaused'])) ? $arrFeatures['quotaused'] : 0; 
			//$rsFeaturesDetail[$key]['dateperiod'] = (!empty($arrFeatures['dateperiod'])) ? $arrFeatures['dateperiod'] : ''; 
		}
		
		return $rsFeaturesDetail;
    }
	
 
    function getAchievements($pkey=''){
        
        $sql = 'select 
                    '.$this->tableAchievements.'.*
                from
                    '.$this->tableAchievements.'
                where 
                    '.$this->tableAchievements.'.statuskey = 1';
                    
        if(!empty($pkey))
            $sql .= ' and '.$this->tableAchievements.'.functionkey = '. $this->oDbCon->paramString($pkey);
        		
        $sql .= ' order by '.$this->tableAchievements.'.orderlist asc';
		
        $rs = $this->oDbCon->doQuery($sql);

        return $rs;
        
    }    
    
    
    function getAchievementsLevel($arr){
		$level = 0;
		
		// diamond
		if($arr['onlineOfflineMeeting'] >= 150 &&
		   $arr['referral'] >= 100 &&
		   $arr['referBusiness'] >= 50 &&
		   $arr['oneOnOne'] >= 15
		  ){
			$level = 3;
		}else if($arr['onlineOfflineMeeting'] >= 100 &&
		   $arr['referral'] >= 30 &&
		   $arr['referBusiness'] >= 15 &&
		   $arr['oneOnOne'] >= 10
		  ){
			$level = 2;
		}else if($arr['onlineOfflineMeeting'] >= 50 &&
		   $arr['referral'] >= 10 &&
		   $arr['referBusiness'] >= 5 &&
		   $arr['oneOnOne'] >= 5
		  ){
			$level = 1;
		}
		
		return $level;
	}
    
    function getAchievementsDetail($pkey,$opt=array()){
        
        
        $datePeriod = (isset($opt['period'])) ? $opt['period'] :  date('01 / 01 / Y');
        $arrFeatureKey = (isset($opt['featurekey'])) ? $opt['featurekey'] : array() ;

        
        $rsAchievementsDetail = $this->getAchievements();
        
        $criteria = '';
        if(!empty($datePeriod))         
			$criteria .= ' and month(dateperiod) = ' .$this->oDbCon->paramDate($datePeriod,' / ','m'). '  and year(dateperiod) = ' .$this->oDbCon->paramDate($datePeriod,' / ','Y');
		
        if(!empty($arrFeatureKey))  
			$criteria .= ' and featurekey in ('.$this->oDbCon->paramString($arrFeatureKey,',').') ';
		 
        
        $sql = 'select * from ' . $this->tableCustomerAchievementDetail . ' where refkey in (' . $this->oDbCon->paramString($pkey,',').')' .$criteria;  
        $rs = $this->oDbCon->doQuery($sql);
         
    	$rs = $this->reindexDetailCollections($rs,'refkey');
		
		$arrReturn = array();
		
		foreach($rs as $customerkey=>$achievementRow){
			
			$rsFeatures = array_column($achievementRow,null,'featurekey');
			foreach($rsAchievementsDetail as $key=>$row){

				// reset nilai awal
				$rsAchievementsDetail[$key]['quotaused'] = 0;
				
				if(!isset($rsFeatures[$row['pkey']]))  continue; 

				$arrAchievements = $rsFeatures[$row['pkey']]; 

				$rsAchievementsDetail[$key]['quotaused'] = (!empty($arrAchievements['quotaused'])) ? $arrAchievements['quotaused'] : 0; 

			}

			$arrReturn[$customerkey] = $rsAchievementsDetail;
		}
			
       
		return $arrReturn;
        
        
    }
    
    function updateMembershipAchievementsCounter($customerkey,$funckey,$opt = array()){ 
		//ini untuk Achievement, bukan Gamification

		// asumsi bulan berjalan
		$datePeriod = date('01 / 01 / Y');
		
        //search feature 
		$rsAchievements = $this->getAchievements($funckey);
        

		$featurekey = $rsAchievements[0]['pkey'];
		$featurequota = 0;
			
		$arrCriteria = array();  
		  
		$criteria = (!empty($arrCriteria)) ? ' and '. implode(' and ', $arrCriteria) : '';
		
		// cari dulu sudah terdaftar blm, kalo blm ad, add new row
		$sql = 'select pkey 
				from  '.$this->tableCustomerAchievementDetail.'     
				where  
					dateperiod = '.$this->oDbCon->paramDate($datePeriod).' and
					refkey = '.$this->oDbCon->paramString($customerkey) .' and
					featurekey = '. $this->oDbCon->paramString($featurekey); 
		$sql .= $criteria;
		
		$rs = $this->oDbCon->doQuery($sql);
		

		// recount
		switch($funckey){
			case 'referral':  
					$membershipSubscription = new MembershipSubscription();
					$quotaUsed = count($membershipSubscription->getNewCustomerReferred($customerkey,array(3)));  
					break;
			case 'onlineOfflineMeeting':
					$meetingSchedule = new MeetingSchedule(1);
					$arrOnlineOfflineKey = array(1,2);
					$quotaUsed = $meetingSchedule->getTotalParticipatedMeeting($customerkey,$arrOnlineOfflineKey,true);
					break;
			case 'oneOnOne' : 
					$meetingSchedule = new MeetingSchedule(2);
					// harus cari yg confirm saja
					$quotaUsed = $meetingSchedule->getTotalParticipatedOneOnOne($customerkey,'',true); 
					break;
			case 'referBusiness' : 
					$giveOpportunity = new GiveOpportunity();
					$quotaUsed = $giveOpportunity->getTotalOpportunity($customerkey); 
					break;
			default :	$quotaUsed = 0;
						break;
		}

		// harus cek ulang 
		// 1. referal based on month
		// 2. reset referalkey based on month
  
		if(empty($rs)){
			if($quotaUsed > 0){ 
				$sql = 'insert into '.$this->tableCustomerAchievementDetail.' (refkey,featurekey,quota,quotaused,dateperiod) 
						values (
								'.$this->oDbCon->paramString($customerkey).',
								'.$this->oDbCon->paramString($featurekey).',
								'.$this->oDbCon->paramString($featurequota).',
								'.$this->oDbCon->paramString($quotaUsed).',
								'.$this->oDbCon->paramDate($datePeriod).') ';
				
				$this->oDbCon->execute($sql);
			} 
		}else{
		 	$sql = 'update '.$this->tableCustomerAchievementDetail.'
				set quotaused = '.$this->oDbCon->paramString($quotaUsed).' 
				where
					dateperiod = '.$this->oDbCon->paramDate($datePeriod).' and
					refkey = '.$this->oDbCon->paramString($customerkey) .' and
					featurekey = '. $this->oDbCon->paramString($featurekey);
			
			$this->oDbCon->execute($sql);
		}
			

	}	
	
	function updateMembershipFeaturesCounter($customerkey,$funckey,$opt = array()){
		//ini utk Gamification, bukan untuk Achievement
		
		// asumsi bulan berjalan
		$datePeriod = date('01 / m / Y');
		
		$arrReturn = $this->getFeaturesQuota($customerkey, array('funckey' => $funckey));
		if(empty($arrReturn)) return;
		
		$featurekey = $arrReturn[0]['featurekey'];
		$featurequota = $arrReturn[0]['quota'];
			
		$arrCriteria = array();  
		  
		$criteria = (!empty($arrCriteria)) ? ' and '. implode(' and ', $arrCriteria) : '';
		
		// cari dulu sudah terdaftar blm, kalo blm ad, add new row
		$sql = 'select pkey 
				from  '.$this->tableCustomerFeaturesDetail.' 
				where  
					dateperiod = '.$this->oDbCon->paramDate($datePeriod).' and
					refkey = '.$this->oDbCon->paramString($customerkey) .' and
					featurekey = '. $this->oDbCon->paramString($featurekey); 
		$sql .= $criteria;
		
		$rs = $this->oDbCon->doQuery($sql);
		 
		if(empty($rs)){
			// kalo kosong, insert counter baru
			
			$sql = 'insert into '.$this->tableCustomerFeaturesDetail.' (refkey,featurekey,quota,quotaused,dateperiod) 
					values (
							'.$this->oDbCon->paramString($customerkey).',
							'.$this->oDbCon->paramString($featurekey).',
							'.$this->oDbCon->paramString($featurequota).',
							1,
							'.$this->oDbCon->paramDate($datePeriod).') ';
			
			//$this->setLog($sql,true);
			$this->oDbCon->execute($sql);
		}else{
			
			// recount
			switch($funckey){
				case 'referral': 
					// harus hitugn ulang berdasarkan customer yg  ?? 
						$arrReturn = $this->getFeaturesQuota($customerkey, array('funckey' => 'referral'));
						$quotaUsed = (!empty($arrReturn)) ? $arrReturn[0]['quotaused']  : 0;  
						$quotaUsed++;
						break;
				case 'onlineMeeting':
						$meetingSchedule = new MeetingSchedule(1);
						$quotaUsed = $meetingSchedule->getTotalParticipatedMeeting($customerkey, array(1));
						break;
				case 'offlineMeeting':
						$meetingSchedule = new MeetingSchedule(1);
						$quotaUsed = $meetingSchedule->getTotalParticipatedMeeting($customerkey, array(2));
						break;
				case 'oneOnOne' : 
						// hanya dipanggil sekali pada saat dicreate. ketika user konfirmasi hadir, tdk ad update apa2.
						// karena function ini hanya utk perhitungan quota boleh atau tidak diawal.
						$meetingSchedule = new MeetingSchedule(2);
						$quotaUsed = $meetingSchedule->getTotalParticipatedOneOnOne($customerkey); 
						break;
				case 'referBusiness' : 
						$giveOpportunity = new GiveOpportunity();
						$quotaUsed = $giveOpportunity->getTotalOpportunity($customerkey); 
						break;
				default :	$quotaUsed = 0;
							break;
			}
			
			// harus cek ulang 
			// 1. referal based on month
			// 2. reset referalkey based on month
			
		    $sql = 'update '.$this->tableCustomerFeaturesDetail.'
					set quotaused = '.$this->oDbCon->paramString($quotaUsed).' 
					where
						dateperiod = '.$this->oDbCon->paramDate($datePeriod).' and
						refkey = '.$this->oDbCon->paramString($customerkey) .' and
						featurekey = '. $this->oDbCon->paramString($featurekey);
			 
			//$this->setLog($sql,true);
			$this->oDbCon->execute($sql);
		}
	
	}
    
  }

?>