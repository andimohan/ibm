<?php
class MeetingSchedule extends BaseClass
{

    function __construct($meetingType = '1')
    {

        parent::__construct();
        $this->tableName = 'meeting_schedule_header';
        $this->tableNameDetail = 'meeting_schedule_detail';
        $this->tableLanguage = 'language_choice';
        $this->tableOnlineOffline = 'online_offline';
        $this->tableMeetingPoint = 'meeting_point';
		$this->tableOnlineChannel = 'online_channel';
        $this->tableCustomer = 'customer'; 
       	$this->tableJobPosition = 'job_position';
        $this->tableCity = 'city'; 
        $this->tableCityCategory = 'city_category'; 
        $this->tableBusinessCategory = 'business_category';
        $this->tableStatus = 'transaction_status'; 
        $this->tablePaymentType = 'payment_type';
        $this->tableMembershipLevel= 'membership_level';	 
        $this->meetingType = $meetingType;
        $this->isTransaction = true;
		$this->newLoad = true;
		
        $this->securityObject = 'MeetingSchedule';

        $this->arrCustomer = array();
        $this->arrCustomer['pkey'] = array('hidDetailKey');
        $this->arrCustomer['customerkey'] = array('hidCustomerKey');
        $this->arrCustomer['refkey'] = array('pkey', 'ref');
        $this->arrCustomer['businesscategorykey'] = array('hidBusinessKey');
		
		// join date detail di nonaktifkan, cuma bisa update dr front end

        $this->arrData = array();
        $this->arrData['pkey'] = array('pkey', array('dataDetail' => array('dataset' => $this->arrCustomer)));
        $this->arrData['code'] = array('code');
        $this->arrData['name'] = array('name');
        $this->arrData['statuskey'] = array('selStatus');
        $this->arrData['meetingonlineoffline'] = array('selOnlineOffline');
        $this->arrData['locationkey'] = array('hidMeetingPointKey');
        $this->arrData['languagekey'] = array('selLanguage');
        $this->arrData['trdate'] = array('trDate', 'datetime');
        $this->arrData['meetinglink'] = array('meetingLink');
        $this->arrData['hostkey'] = array('hidHostKey');
        $this->arrData['meetingtypekey'] = array('selTypeOfMeeting');
        $this->arrData['paymenttypekey'] = array('selPaymentType');
        $this->arrData['onlinechannelkey'] = array('selOnlineChannel');
        $this->arrData['partnerkey'] = array('hidPartnerKey');
        $this->arrData['locationname'] = array('meetingPoint');
        $this->arrData['locationaddress'] = array('address');
		$this->arrData['citykey'] = array('hidCityKey');
		$this->arrData['gmt'] = array('selTimeZone');


        $this->arrDataListAvailableColumn = array();
        array_push($this->arrDataListAvailableColumn, array('code' => 'code', 'title' => 'code', 'dbfield' => 'code', 'default' => true, 'width' => 70));
        array_push($this->arrDataListAvailableColumn, array('code' => 'datetime', 'title' => 'time', 'dbfield' => 'trdate', 'default' => true,'align' =>'center', 'format'=> 'datetime','width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'host', 'title' => 'host', 'dbfield' => 'customername', 'default' => true, 'width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'topic', 'title' => 'topic', 'dbfield' => 'name', 'default' => true, 'width' => 300));
        array_push($this->arrDataListAvailableColumn, array('code' => 'type', 'title' => 'type', 'dbfield' => 'meetingonlineofflinename', 'default' => true,'align'=>'center', 'width' => 70));
		array_push($this->arrDataListAvailableColumn, array('code' => 'status', 'title' => 'status', 'dbfield' => 'statusname', 'default' => true, 'width' => 70));


        $this->arrSearchColumn = array();
        array_push($this->arrSearchColumn, array('Kode', $this->tableName . '.code'));
        array_push($this->arrSearchColumn, array('Nama', $this->tableName . '.name'));
        array_push($this->arrSearchColumn, array('Host', $this->tableCustomer . '.name'));

        $this->includeClassDependencies(array(
              'Customer.class.php',
              'CustomerFeatures.class.php',
              'ActivityLog.class.php',
			  'MeetingPoint.class.php'
        )); 

		
		$this->hostLevelThreshold = array( 'join' => 10, 'host' => 10, 'maxparticipant' => 15,'maxmember' => 10,'maxguest' => 5,'maxremindersent' => 1, 'reminderBroadcastTime' => 7200);
        $this->overwriteConfig();
    }

    function getQuery()
    {

        $sql = '
                 select
                     ' . $this->tableName . '.*,
                     ' . $this->tablePaymentType . '.name as paymenttypename,
                     ' . $this->tableLanguage . '.language as languagename,
                     ' . $this->tableOnlineOffline . '.name as meetingonlineofflinename,
                     ' . $this->tableOnlineChannel . '.name as onlinechannel,
                     ' . $this->tableMeetingPoint . '.name as meetingpointname,
                     ' . $this->tableMeetingPoint . '.address as meetingpointaddress,
                     ' . $this->tableCustomer . '.code as customercode,
                     ' . $this->tableCustomer . '.name as customername,
                     ' . $this->tableCustomer . '.photofile as hostphoto,
                     ' . $this->tableCustomer . '.companyname,
					 partner.name as partnername,
					 partner.code as partnercode,
					 partner.photofile as partnerphoto,
                     ' . $this->tableStatus . '.status as statusname ,
					 ' . $this->tableMembershipLevel.'.name as membershiplevelname,
					 ' . $this->tableJobPosition.'.name as jobpositionname,
                     concat(' . $this->tableCity . '.name,\', \','.$this->tableCityCategory.'.name ) as meetingpointcitycategoryname,
                     concat(locationcity.name,\', \',locationcitycategory.name ) as locationcitycategoryname
                 from 
                     ' . $this->tableName . '
                     left join ' . $this->tablePaymentType . ' on ' . $this->tableName . '.paymenttypekey = ' . $this->tablePaymentType . '.pkey
                     left join ' . $this->tableLanguage . ' on ' . $this->tableName . '.languagekey = ' . $this->tableLanguage . '.pkey
                     left join ' . $this->tableOnlineChannel . ' on ' . $this->tableName . '.onlinechannelkey = ' . $this->tableOnlineChannel . '.pkey
                     left join ' . $this->tableOnlineOffline . ' on ' . $this->tableName . '.meetingonlineoffline = ' . $this->tableOnlineOffline . '.pkey
                     left join ' . $this->tableMeetingPoint . ' on ' . $this->tableName . '.locationkey = ' . $this->tableMeetingPoint . '.pkey
                     left join ' . $this->tableCustomer . ' on ' . $this->tableName . '.hostkey = ' . $this->tableCustomer . '.pkey 
                     left join ' . $this->tableCustomer . ' partner on ' . $this->tableName . '.partnerkey = partner.pkey 
					 left join '.$this->tableMembershipLevel.' on '.$this->tableCustomer.'.membershiplevel = '.$this->tableMembershipLevel.'.pkey
					 left join '.$this->tableCity.' on '.$this->tableMeetingPoint.'.citykey = '.$this->tableCity.'.pkey
					 left join '.$this->tableCityCategory.' on '.$this->tableCity.'.categorykey = '.$this->tableCityCategory.'.pkey
					 left join '.$this->tableCity.' locationcity on '.$this->tableName.'.citykey = locationcity.pkey
					 left join '.$this->tableCityCategory.' locationcitycategory on locationcity.categorykey = locationcitycategory.pkey
					 left join '.$this->tableJobPosition.' on '.$this->tableCustomer.'.jobpositionkey = '.$this->tableJobPosition.'.pkey,
                    ' . $this->tableStatus . '
                 where  		
                     ' . $this->tableName . '.statuskey = ' . $this->tableStatus . '.pkey and
					 ' . $this->tableName . '.meetingtypekey = ' . $this->meetingType;
			
		$sql .= $this->criteria;
        
        return $sql;
    }
	
	function validateCancelFE($arrParam){  
		// $arrParam['hidCustomerKey'] harusnya aman karena diinject dr ajax
		
		$customerkey = $arrParam['hidCustomerKey'];
		$meetingkey = $arrParam['hidMeetingKey'];
		 

		$arrayToJs =array();

		// kalo user key bukan host dan bukan peserta ataupun partner 
		$rsHeader = $this->getDataRowById($meetingkey);
		$rsDetail = $this->getDetailWithRelatedInformation($meetingkey);
		$arrParticipantsKey = array_column($rsDetail,'customerkey');

		if($this->meetingType == 2){
			if ($customerkey <> $rsHeader[0]['hostkey'] && $customerkey <> $rsHeader[0]['partnerkey']) 
				$this->addErrorList($arrayToJs,false, $this->errorMsg[212]);   
		}else{
			//khusus IBM
			if ($customerkey <> $rsHeader[0]['hostkey'] && !in_array($customerkey,$arrParticipantsKey)) 
				$this->addErrorList($arrayToJs,false, $this->errorMsg[212]);    
		}

		$trdate = $this->formatDBDate($rsHeader[0]['trdate'],'d / m / Y H:i');
		if (!$this->checkBeforeTime($trdate)) 
			$this->addErrorList($arrayToJs,false, $this->errorMsg['meetingSchedule'][12]); 

		return $arrayToJs;
          
	 }
	
	
	function validateCheckInFE($arrParam){  
		// $arrParam['hidCustomerKey'] harusnya aman karena diinject dr ajax
		
        $customer = new Customer();
        
		$customerkey = $arrParam['hidCustomerKey'];
		$meetingkey = $arrParam['hidMeetingKey'];
		 
        $rsCustomer = $customer->searchDataRow(array($customer->tableName.'.gmt'),
                                              ' and '.$customer->tableName.'.pkey = ' . $this->oDbCon->paramString($customerkey)
                                            );
         
		$arrayToJs =array();

		// kalo user key bukan host dan bukan peserta ataupun partner 
		$rsHeader = $this->getDataRowById($meetingkey);
		$rsDetail = $this->getDetailWithRelatedInformation($meetingkey);
		$arrParticipantsKey = array_column($rsDetail,'customerkey');
 
        $rsHeader[0]['trdate'] = $this->convertToLocalTimeZone($rsHeader[0]['trdate'],$rsHeader[0]['gmt'] ,$rsCustomer[0]['gmt']);
  
        
		if($this->meetingType == 2){
		
		}else{
			//khusus IBM
			if (!in_array($customerkey,$arrParticipantsKey)) 
				$this->addErrorList($arrayToJs,false, $this->errorMsg[212]);    
		}

		$trdate = $this->formatDBDate($rsHeader[0]['trdate'],'d / m / Y H:i');
		if (!$this->inMeetingTime($trdate)) 
			$this->addErrorList($arrayToJs,false, $this->errorMsg['meetingSchedule'][14]); 

		return $arrayToJs;
          
	 }
     
	
    function cancelMeeting($arrParam) {
		// $arrParam['hidCustomerKey'] harusnya aman karena diinject dr ajax
		$customer = new Customer();
		$customerkey = $arrParam['hidCustomerKey'];
		$meetingkey = $arrParam['hidMeetingKey'];
			
		$arrActivityLog = array();
		
        $arrayToJs = array();
        try {
                if (!$this->oDbCon->startTrans()) 
            		throw new Exception($this->errorMsg[100]);

			    
				$arrayToJs = $this->validateCancelFE($arrParam);
				if(!empty($arrayToJs)) return $arrayToJs;
			 
			
                $rsHeader = $this->getDataRowById($meetingkey);
			
				if($this->meetingType == 2){
					// khusus One on nOne
					
					// hapus kalo bener peserta / host nya == userkey 
					//$this->setLog($customerkey. ' ' .$rsHeader[0]['hostkey'],true);
					if ($rsHeader[0]['hostkey'] == $customerkey || $rsHeader[0]['partnerkey'] == $customerkey) { 
						
						$arrayToJs = $this->changeStatus($rsHeader[0]['pkey'], 4,'',false,true); 
						
						$customerFeatures = new CustomerFeatures(); 
						$customerFeatures->updateMembershipFeaturesCounter($rsHeader[0]['hostkey'], 'oneOnOne');
						$customerFeatures->updateMembershipFeaturesCounter($rsHeader[0]['partnerkey'] , 'oneOnOne'); 

						// kalo cancel gpp, hitung ulang saja semua
                        $customerFeatures->updateMembershipAchievementsCounter($rsHeader[0]['hostkey'], 'oneOnOne');
                        $customerFeatures->updateMembershipAchievementsCounter($rsHeader[0]['partnerkey'] , 'oneOnOne');
 
						//add activity log
						array_push($arrActivityLog, 
									array(
											'modulekey' => 2,
											'templatekey' => 15, 
											'refkey' => $customerkey, 
											'refDate' => $this->formatDBDate($rsHeader[0]['trdate'],'d / m / Y H:i'), 
											'userkey' => ($customerkey == $rsHeader[0]['hostkey']) ? $rsHeader[0]['partnerkey']  : $rsHeader[0]['hostkey'], 
										) ,
									array(
											'modulekey' => 2,
											'templatekey' => 16,  
											'refkey' => ($customerkey == $rsHeader[0]['hostkey']) ? $rsHeader[0]['partnerkey']  : $rsHeader[0]['hostkey'], 
											'refDate' => $this->formatDBDate($rsHeader[0]['trdate'],'d / m / Y H:i'),
											'userkey' => $customerkey, 
										) 
								  ); 
					}

					
				}else{
					
					// khusus IBM 
					
					$customerFeatures = new CustomerFeatures();
					$onlineOffline = ($rsHeader[0]['meetingonlineoffline'] == 1) ? 'onlineMeeting' : 'offlineMeeting';

					if ($rsHeader[0]['hostkey'] == $customerkey) { 
						$rsDetail = $this->getDetailWithRelatedInformation($meetingkey);
						$arrayToJs = $this->changeStatus($rsHeader[0]['pkey'], 4,'',false,true);
						
						// cancel counter online sebagai host
						$customerFeatures->updateMembershipFeaturesCounter($rsHeader[0]['hostkey'], $onlineOffline);
						$customerFeatures->updateMembershipAchievementsCounter($rsHeader[0]['hostkey'], 'onlineOfflineMeeting');		
						
						
						//add activity log
						array_push($arrActivityLog, 
									array(
											'modulekey' => 1,
											'templatekey' => 9, 
											'refkey' => $rsHeader[0]['hostkey'] , 
											'refDate' => $this->formatDBDate($rsHeader[0]['trdate'],'d / m / Y H:i'),
											'userkey' => $rsHeader[0]['hostkey'],  
										) 
								  ); 
						
						for ($i=0; $i<count($rsDetail); $i++) { 
							$customerkey = $rsDetail[$i]['customerkey'];
							
							$customerFeatures->updateMembershipFeaturesCounter($customerkey, $onlineOffline);	
                            $customerFeatures->updateMembershipAchievementsCounter($customerkey, 'onlineOfflineMeeting');		 
							$this->updateHostLevelKey($customerkey); 
							 
							//add activity log
							array_push($arrActivityLog, 
										array(
												'modulekey' => 1,
												'templatekey' => 8, 
												'refkey' => $customerkey, 
												'refDate' => $this->formatDBDate($rsHeader[0]['trdate'],'d / m / Y H:i'),
												'userkey' => $rsHeader[0]['hostkey'],  
											) 
									  ); 

							// kirim email, sementara taro disini dulu, nanti lihat lemot atau gk
							$this->sendHostCancelEmail($customerkey);
						} 
						
						
						
					} else {
						$sql = 'delete from '.$this->tableNameDetail.' 
								where 
									refkey = ' . $this->oDbCon->paramString($meetingkey).'  and 
									customerkey = ' . $this->oDbCon->paramString($customerkey);
						$this->oDbCon->execute($sql); 
						
						$customerFeatures->updateMembershipFeaturesCounter($customerkey, $onlineOffline);
						$customerFeatures->updateMembershipAchievementsCounter($customerkey, 'onlineOfflineMeeting');	 	  
						$this->updateHostLevelKey($customerkey); 
						
						//add activity log
						array_push($arrActivityLog, 
									array(
											'modulekey' => 1,
											'templatekey' => 7, 
											'refkey' => $customerkey, 
											'refDate' => $this->formatDBDate($rsHeader[0]['trdate'],'d / m / Y H:i'),
                                            'refGMT' =>  $rsHeader[0]['gmt'],
											'userkey' => $rsHeader[0]['hostkey']  
										) ,
									array(
											'modulekey' => 1,
											'templatekey' => 6,  
											'refkey' => $rsHeader[0]['hostkey'], 
											'refDate' => $this->formatDBDate($rsHeader[0]['trdate'],'d / m / Y H:i'),
                                            'refGMT' =>  $rsHeader[0]['gmt'],
											'userkey' => $customerkey
										) 
								  ); 

						
					}
            
				}
             

				$activityLog = new ActivityLog();
				$activityLog->addNewLog($arrActivityLog); 

                $this->addErrorList($arrayToJs, true, $this->lang['dataHasBeenSuccessfullyUpdated']);
			
				$this->oDbCon->endTrans(); 
            } catch (Exception $e) {
                $this->oDbCon->rollback();
                $this->addErrorList($arrayToJs, false, $e->getMessage());
            }
		
        return $arrayToJs;
    }
	
	
    function checkInMeeting($arrParam) {
		//if($this->meetingType == 2) return;
		
		// $arrParam['hidCustomerKey'] harusnya aman karena diinject dr ajax
		$customer = new Customer();
		$customerFeatures = new CustomerFeatures();
		
		$customerkey = $arrParam['hidCustomerKey'];
		//$partnerkey = $arrParam['hidPartnerKey']; // gk perlu, karena yg login (hidCustomerKey) bisa host / partner
		$meetingkey = $arrParam['hidMeetingKey'];
			
        $arrayToJs = array();
        try {
                if (!$this->oDbCon->startTrans()) 
            		throw new Exception($this->errorMsg[100]);

			    
				$arrayToJs = $this->validateCheckInFE($arrParam);
			
				if(!empty($arrayToJs)) return $arrayToJs;
			
                $rsHeader = $this->getDataRowById($meetingkey);
			
				if($this->meetingType == 2){
						// khusus One on One, salah satu checkin, keduanya otomatis checkin
					 
					    $sql = 'update '.$this->tableName.' set checkindate = now(), ischeckin = 1 where 
									pkey = ' . $this->oDbCon->paramString($meetingkey).'  and 
									 ( hostkey = ' . $this->oDbCon->paramString($customerkey). ' or
									   partnerkey = ' . $this->oDbCon->paramString($customerkey). '  
									 ) and
									ischeckin = 0
									';
					
						$this->oDbCon->execute($sql); 
					
						// tambah nanti utk achievement
						// jangan sampe double jika host dan partner checkin, karena kalo satu checkin yg satu lg pasti otoamtis dianggap checkin
						$customerFeatures->updateMembershipAchievementsCounter($rsHeader[0]['hostkey'], 'oneOnOne');
						$customerFeatures->updateMembershipAchievementsCounter($rsHeader[0]['partnerkey'], 'oneOnOne');
							
						$arrActivityLog = array();
						array_push($arrActivityLog, 
						   					array(
													'modulekey' => 2,
													'templatekey' => 28, 
													'refkey' => $rsHeader[0]['partnerkey'],  
													'userkey' => $rsHeader[0]['hostkey']
												) ,
						   					array(
													'modulekey' => 2,
													'templatekey' => 28,  
													'refkey' => $rsHeader[0]['hostkey'] ,  
													'userkey' =>$rsHeader[0]['partnerkey'] 
												) 
						  					);  
						
						$activityLog = new ActivityLog();
						$activityLog->addNewLog($arrActivityLog); 

				}else{
					
					// khusus IBM   
					if ($rsHeader[0]['hostkey'] == $customerkey) { 
						// abaikan
					} else {
					   $sql = 'update '.$this->tableNameDetail.' set checkindate = now(), ischeckin = 1 where 
									refkey = ' . $this->oDbCon->paramString($meetingkey).'  and 
									customerkey = ' . $this->oDbCon->paramString($customerkey). ' and
									ischeckin = 0
									';
						$this->oDbCon->execute($sql); 
						
						
						$customerFeatures->updateMembershipAchievementsCounter($customerkey, 'onlineOfflineMeeting');
						
						$arrActivityLog = array();
						array_push($arrActivityLog, 
						   					array(
													'modulekey' => 1,
													'templatekey' =>12, 
													'refkey' => $customerkey,  
													'userkey' => $rsHeader[0]['hostkey']
												) ,
						   					array(
													'modulekey' => 1,
													'templatekey' => 11,  
													'refkey' => $rsHeader[0]['hostkey'],  
													'userkey' => $customerkey 
												) 
						  					);  
						
						$activityLog = new ActivityLog();
						$activityLog->addNewLog($arrActivityLog); 
  
						$this->updateHostLevelKey($customerkey);
					}
            
				}
             
			
                $this->addErrorList($arrayToJs, true, $this->lang['dataHasBeenSuccessfullyUpdated']);
			
				$this->oDbCon->endTrans(); 
            } catch (Exception $e) {
                $this->oDbCon->rollback();
                $this->addErrorList($arrayToJs, false, $e->getMessage());
            }
		
        return $arrayToJs;
    }
	
	function cancelCheckInMeeting($arrParam){ 	
		$customerFeatures = new CustomerFeatures();
		
		// khusus HOST yg bisa cancel
		
		// perlu validasi jam / tgl kah ? kalo hostnya cancel peserta dr js 
		
		if($this->meetingType == 2) return;
		
		// $arrParam['hidCustomerKey'] harusnya aman karena diinject dr ajax
		$customer = new Customer();
		$hostkey = $arrParam['hidCustomerKey'];
		$participantkey =  $arrParam['hidParticipantKey'];
		$meetingkey = $arrParam['hidMeetingKey'];
			
        $arrayToJs = array();
        try {
                if (!$this->oDbCon->startTrans()) 
            		throw new Exception($this->errorMsg[100]);

                $rsHeader = $this->getDataRowById($meetingkey);
			
				if($this->meetingType == 2){
					// khusus One on nOne
					 

				}else{
					
					// khusus IBM 
					 
					if ($rsHeader[0]['hostkey'] == $hostkey) { 
					
						// join ke header, pastikan bener host yg cancel utk peserta
						$sql = 'update 
									'.$this->tableName .', '.$this->tableNameDetail.' 
								set '.$this->tableNameDetail.'.checkindate = \'\', '.$this->tableNameDetail.'.ischeckin = 0
								where 
									'.$this->tableNameDetail.'.refkey = ' . $this->oDbCon->paramString($meetingkey).'  and 
									'.$this->tableNameDetail.'.customerkey = ' . $this->oDbCon->paramString($participantkey). ' and
									'.$this->tableName .'.hostkey = '. $this->oDbCon->paramString($hostkey).' 
									';
						
						//$this->setLog($sql,true);
						$this->oDbCon->execute($sql); 
						
						$customerFeatures->updateMembershipAchievementsCounter($participantkey, 'onlineOfflineMeeting');
						
						$arrActivityLog = array();
						array_push($arrActivityLog, 
						   					array(
													'modulekey' => 1,
													'templatekey' => 13, 
													'refkey' => $hostkey,  
													'userkey' => $participantkey 
												) ,
						   					array(
													'modulekey' => 1,
													'templatekey' => 14,  
													'refkey' => $participantkey,  
													'userkey' => $hostkey 
												) 
						  					);  
						
						$activityLog = new ActivityLog();
						$activityLog->addNewLog($arrActivityLog); 
						
						
						$this->updateHostLevelKey($participantkey);
					} else {
					  
					}
            
				}
             
			
                $this->addErrorList($arrayToJs, true, $this->lang['dataHasBeenSuccessfullyUpdated']);
			
				$this->oDbCon->endTrans(); 
            } catch (Exception $e) {
                $this->oDbCon->rollback();
                $this->addErrorList($arrayToJs, false, $e->getMessage());
            }
		
        return $arrayToJs;
	}
	
    function validateForm($arr, $pkey = '')
    {
        $arrayToJs = parent::validateForm($arr, $pkey);
        $customer = new Customer();
		
		$onlineOffline = $arr['selOnlineOffline']; 
		$meetingLink = $arr['meetingLink']; 
		$meetingPoint = $arr['hidMeetingPointKey'];
		$meetingAddress = $arr['address'];
		$meetingPointName = $arr['meetingPoint'];


        // cek hostLevelKey
		// harus PRO dan HOST
        $rsCust = $customer->searchDataRow(array($customer->tableName.'.membershiplevel',$customer->tableName.'.hostlevelkey'),
										   'and '. $customer->tableName.'.pkey='. $this->oDbCon->paramString($arr['hidHostKey'])
										  );
 
		// cek quota, kalo utk add aja
//		if(empty($pkey)){
//			$customerFeatures = new CustomerFeatures();
//			$rsFeatureDetail = $customerFeatures->getFeaturesQuota($arr['hidHostKey'],array('funckey' => ($this->meetingType == 1) ? 'host' : 'oneOnOne' )); 
//			
//			if( empty($rsFeatureDetail) || $rsFeatureDetail[0]['quotaused'] >= $rsFeatureDetail[0]['quota']  ){ 
//					$this->addErrorList($arrayToJs,false,$this->lang['monthlyQuotaExceed']);
//			}
//
//		}
		
		
		$dateDiff = $this->dateDiff($arr['trDate'],date('d / m / Y H:i'));
        if ($dateDiff > 0) {
            $this->addErrorList($arrayToJs, false, $this->errorMsg['meetingSchedule'][3]);
        }
		
		if($onlineOffline == 1){
			 if ( empty($meetingLink) )
				$this->addErrorList($arrayToJs,false,$this->errorMsg['meetingSchedule'][4]);
			
		}elseif($onlineOffline == 2) {
			if ( ($this->meetingType == 1 && empty($meetingPoint)) || ( $this->meetingType == 2 &&  empty($meetingPointName) ) ) 
				$this->addErrorList($arrayToJs,false,$this->errorMsg['meetingSchedule'][5]);			 
		} 
		
		
		if ($this->meetingType == 2){
 
			if (empty($arr['hidPartnerKey'])){
				$this->addErrorList($arrayToJs, false, $this->errorMsg['customer'][1]);
			} else{

				$rsPartner = $customer->searchDataRow(array($customer->tableName.'.membershiplevel'),
													  'and '. $customer->tableName.'.pkey='. $this->oDbCon->paramString($arr['hidPartnerKey'])
													 );

				if (($rsCust[0]['membershiplevel'] < 3) || ($rsPartner[0]['membershiplevel'] < 3))
					$this->addErrorList($arrayToJs, false, $this->errorMsg['meetingSchedule'][11]);

				if ($arr['hidHostKey'] == $arr['hidPartnerKey'] )
					$this->addErrorList($arrayToJs,false,$this->errorMsg['meetingSchedule'][6]); 
			}
                
            
		}else {
			// harus pro dan host
			if($rsCust[0]['membershiplevel'] < 3 || $rsCust[0]['hostlevelkey'] <= 0)
				$this->addErrorList($arrayToJs,false,$this->errorMsg['meetingSchedule'][7]);

			if (in_array( $arr['hidHostKey'] , $arr['hidCustomerKey']))
				$this->addErrorList($arrayToJs,false,$this->errorMsg['meetingSchedule'][6]); 
		}
		 
        return $arrayToJs;
    }
	
	 function afterUpdateData($arrParam, $action){ 
        
        $pkey = $arrParam['pkey'];
		$customerkey = $arrParam['hidHostKey'];
		$partnerkey = $arrParam['hidPartnerKey'];
		 
		 //query ulang
		$rsMeeting = $this->searchDataRow(array($this->tableName.'.pkey',$this->tableName.'.trdate',$this->tableName.'.gmt',$this->tableName.'.meetingonlineoffline'),
										 'and '. $this->tableName.'.pkey='.$this->oDbCon->paramString($pkey)
										 );
		  
		$arrActivityLog = array();
		 
		// khusus IBM
		if($this->meetingType == 1) {

			// perlu hitugn ulang utk semua peserta jg
			// ketika add meeting atau edit meeting dr admin ataupun dr front end
			$customerFeatures = new CustomerFeatures();

			$onlineOffline = ($rsMeeting[0]['meetingonlineoffline'] == 1) ? 'onlineMeeting' : 'offlineMeeting';
			$customerFeatures->updateMembershipFeaturesCounter($customerkey, $onlineOffline);
			
			$customerFeatures->updateMembershipAchievementsCounter($customerkey, 'onlineOfflineMeeting');		
			 
			if ($action == INSERT_DATA)	{ 
				$this->updateHostLevelKey($customerkey);
				
				array_push($arrActivityLog, array(
													'modulekey' => 1,
													'templatekey' => 1,
													'refDate' => $this->formatDBDate($rsMeeting[0]['trdate'],'d / m / Y H:i'),
													'refkey' => $customerkey 
												) ); 
			}
				 
		}else if($this->meetingType == 2) { 
			$customerFeatures = new CustomerFeatures(); 
			
			// untuk gamification gk masalah, karena gamification berbicara masalah quota, apakah boleh add meeting atau tdk diawal
			$customerFeatures->updateMembershipFeaturesCounter($customerkey, 'oneOnOne');
			$customerFeatures->updateMembershipFeaturesCounter($partnerkey, 'oneOnOne'); 
			
			//  dipindah, update setelah checkin
			// $customerFeatures->updateMembershipAchievementsCounter($customerkey, 'oneOnOne');	
			/// $customerFeatures->updateMembershipAchievementsCounter($partnerkey, 'oneOnOne');	
			
			if ($action == INSERT_DATA)	{ 
				// one on one
				array_push($arrActivityLog, 
						   					array(
													'modulekey' => 2,
													'templatekey' => 2, 
													'refkey' => $customerkey, 
													'refDate' => $this->formatDBDate($rsMeeting[0]['trdate'],'d / m / Y H:i'),
                                                    'refGMT' =>  $rsMeeting[0]['gmt'],
													'userkey' => $partnerkey
												) ,
						   					array(
													'modulekey' => 2,
													'templatekey' => 3,  
													'refkey' => $partnerkey, 
													'refDate' => $this->formatDBDate($rsMeeting[0]['trdate'],'d / m / Y H:i'),
                                                    'refGMT' =>  $rsMeeting[0]['gmt'],
													'userkey' => $customerkey
												) 
						  					);  
				
			}
		}
               
		$activityLog = new ActivityLog();
		$activityLog->addNewLog($arrActivityLog); 
    } 
	
    function normalizeParameter($arrParam, $trim = false){ 
		$arrParam['trDate'] =  $arrParam['trDate'].' '.$arrParam['selHour'];
		
        $meetingkey = $arrParam['selOnlineOffline'];
        if ($meetingkey == '1') {
            $arrParam['hidMeetingPointKey'] = '';
            $arrParam['selPaymentType'] = '';
        } else {
            $arrParam['selOnlineChannel'] = '';
            $arrParam['meetingLink'] = '';
        }
        if ($this->meetingType <> 2) {
            $arrParam['hidPartnerKey'] = '';
        }

		// kalo meeting type nya 1 dan offline
		if ($this->meetingType == 1 && $arrParam['selOnlineOffline'] == 2 ){
			$meetingPoint = new MeetingPoint();
			$rsMeetingPoint = $meetingPoint->searchDataRow( array($meetingPoint->tableName.'.pkey',$meetingPoint->tableName.'.address',$meetingPoint->tableName.'.citykey'),
															' and  '.$meetingPoint->tableName.'.pkey = '.$this->oDbCon->paramString($arrParam['hidMeetingPointKey']) 
														  );
			
			$arrParam['address'] = html_entity_decode($rsMeetingPoint[0]['address']);
			$arrParam['hidCityKey'] = $rsMeetingPoint[0]['citykey'];
			
		}
			
		$arrParam['selTypeOfMeeting'] = $this->meetingType;
		
		
        $arrParam = parent::normalizeParameter($arrParam, true);
        return $arrParam;
    }
    function getLanguage($pkey = '')
    {
        if (empty($pkey)) {
            $sql = 'select * from ' . $this->tableLanguage;
        } else {
            $sql = 'select * from ' . $this->tableLanguage . ' where pkey=' . $pkey;
        }
        return $this->oDbCon->doQuery($sql);
    }
    function getOnlineOffline($pkey = '')
    {

        $sql = 'select * from ' . $this->tableOnlineOffline . ' where 1=1 ';
        if (!empty($pkey)) {
            $sql .= 'and pkey=' . $this->oDbCon->paramString($pkey);
        }
        return $this->oDbCon->doQuery($sql);
    }

    function getOnlineOfflineById($pkey){
        $sql = 'select * from ' . $this->tableOnlineOffline . ' where pkey in (' . $pkey . ') ';
        return $this->oDbCon->doQuery($sql);
    }
    
    function getLanguageById($pkey){
        $sql = 'select ' . $this->tableLanguage . '.language from ' . $this->tableLanguage . ' where `pkey` in (' . $pkey . ') ';
        return $this->oDbCon->doQuery($sql);
    }
	
  function getDetailWithRelatedInformation($pkey)
    {
        $sql = '
        select
            ' . $this->tableNameDetail . '.*,
            ' . $this->tableCustomer . '.pkey as customerkey,
            ' . $this->tableCustomer . '.name as customername,
            ' . $this->tableCustomer . '.mobile as customermobile,
            ' . $this->tableCustomer . '.email as customeremail,
            ' . $this->tableCustomer . '.membershiplevel,
            ' . $this->tableCustomer . '.companyname,
            ' . $this->tableCustomer . '.photofile,
            ' . $this->tableBusinessCategory . '.name as businesscategoryname,
            ' . $this->tableBusinessCategory . '.pkey as businesscategorykey,
			' . $this->tableJobPosition.'.name as jobpositionname
        from 
            ' . $this->tableNameDetail . '
            left join ' . $this->tableCustomer . ' on ' . $this->tableNameDetail . '.customerkey = ' . $this->tableCustomer . '.pkey
            left join ' . $this->tableBusinessCategory . ' on ' . $this->tableNameDetail . '.businesscategorykey = ' . $this->tableBusinessCategory . '.pkey 
			left join '.$this->tableJobPosition.' on '.$this->tableCustomer.'.jobpositionkey = '.$this->tableJobPosition.'.pkey
        where  		 
            ' . $this->tableNameDetail . '.refkey in (' . $this->oDbCon->paramString($pkey,',').')';
        return $this->oDbCon->doQuery($sql);
    }
 
	function addDetail($arr)
    {
        $arrayToJs =array();
        $customer = new Customer();
		$customerFeatures = new CustomerFeatures();
	 
		$meetingkey = $arr['hidMeetingKey'];
		$customerkey = $arr['hidCustomerKey'];
		
		$rsMeeting = $this->getDataRowById($meetingkey);
	 	$rsDetail = $this->getDetailById($meetingkey); 
		
		$arrParticipant = array_column($rsDetail,'customerkey');
		$arrParticipantBusinesssCategory = array_column($rsDetail,'businesscategorykey');
		
		$rsBusinessHost = $customer->searchDataRow(array($customer->tableName.'.mainbusinesskey'),'and '. $customer->tableName.'.pkey='. $this->oDbCon->paramString($rsMeeting[0]['hostkey']));
      
		// validasi total peserta
		$rsParticipantInformation = $this->getParticipantInformation($meetingkey);
		$rsParticipantInformation = $rsParticipantInformation[$meetingkey];
		
		$rsCustomer = $customer->searchDataRow(array($customer->tableName.'.pkey',$customer->tableName.'.membershiplevel'), 
											   ' and '.$customer->tableName.'.pkey = '.$this->oDbCon->paramString($customerkey)
											  );
		
		$totalParticipant = count($rsDetail) + 1; // plus host ny sendiri
		$totalGuest = $rsParticipantInformation['guest'];
		$totalMember = $rsParticipantInformation['member'];
		$maxGuest = $rsParticipantInformation['maxguest'];
		$maxMember = $rsParticipantInformation['maxmember'];
		$maxParticipant = $rsParticipantInformation['maxparticipant']; // jgn ambil dr class, utk jaga2 kalo meeting bisa beda2 jumlah peserta
		
		
		// validasi total member dan guest
		  
		if($rsCustomer[0]['membershiplevel'] == 1){ 
			$totalGuest++;
			if($totalGuest > $maxGuest) 
				$this->addErrorList($arrayToJs,false, $this->errorMsg['meetingSchedule'][8]);
		}

		if(in_array($rsCustomer[0]['membershiplevel'], array(2,3))){ 
			$totalMember++;
			if($totalMember > $maxMember) 
				$this->addErrorList($arrayToJs,false, $this->errorMsg['meetingSchedule'][9]);
		}
		
		$meetingTime = $this->formatDBDate($rsMeeting[0]['trdate'],'d / m / Y H:i');  
		if( ! $this->checkBeforeTime($meetingTime) )
			$this->addErrorList($arrayToJs,false, $this->errorMsg['meetingSchedule'][13]);
		   
		// validasi total peserta
		//$this->setLog('('.$totalGuest.'+'.$totalMember.') > '. $maxParticipant,true); 
		if( ($totalGuest + $totalMember) > $maxParticipant ) 
				$this->addErrorList($arrayToJs,false, $this->errorMsg['meetingSchedule'][10],true);  
		
		
		if(empty($rsMeeting))
			 	$this->addErrorList($arrayToJs,false,$this->errorMsg['meetingSchedule'][1]);
			
        if (empty($customerkey)) 
            $this->addErrorList($arrayToJs, false, $this->errorMsg[302]); 
	  	 
		// peserta gk boelh daftar 2x dan gk boleh host itu sendiri
		if ( in_array($customerkey, $arrParticipant) || $customerkey == $rsMeeting[0]['hostkey'] )  
			$this->addErrorList($arrayToJs, false, $this->errorMsg['meetingSchedule'][6]); 
		
		if ( in_array($arr['hidBusinessKey'], $arrParticipantBusinesssCategory) )  
			$this->addErrorList($arrayToJs, false, $this->errorMsg['businessCategory'][2]); 
	 
        //cek mainbusinesskey host dg customer/user, asumsi selalu mainbusinesskey yg didaftar kan 
        if( empty($arr['hidBusinessKey'])) 
            $this->addErrorList($arrayToJs, false, $this->errorMsg['businessCategory'][1]);
     
		if( $arr['hidBusinessKey'] == $rsBusinessHost[0]['mainbusinesskey']) 
            $this->addErrorList($arrayToJs, false, $this->errorMsg['businessCategory'][2]);
     
		//validasi quota  
		$rsFeatureDetail = $customerFeatures->getFeaturesQuota($customerkey,array('funckey' => 'onlineMeeting' ));  
		if( empty($rsFeatureDetail) || $rsFeatureDetail[0]['quotaused'] >= $rsFeatureDetail[0]['quota'] )
				$this->addErrorList($arrayToJs,false,$this->lang['monthlyQuotaExceed']);
		
		
		if(!empty($arrayToJs)) return $arrayToJs;
		

		try {

			if (!$this->oDbCon->startTrans()) //ini penting
				throw new Exception($this->errorMsg[100]);

			$sql = "INSERT INTO " . $this->tableNameDetail . " (refkey, customerkey,businesscategorykey,joindate) VALUES (" . $this->oDbCon->paramString($meetingkey) . ", " . $this->oDbCon->paramString($customerkey) . ",". $this->oDbCon->paramString($arr['hidBusinessKey']).",now())";
			$this->oDbCon->execute($sql);
			 
			// khusus IBM
			if($this->meetingType == 1){ 
				$onlineOffline = ($rsMeeting[0]['meetingonlineoffline'] == 1) ? 'onlineMeeting' : 'offlineMeeting';
				$customerFeatures->updateMembershipFeaturesCounter($customerkey, $onlineOffline);
				
				// dipindahkan ketika absen
				// $customerFeatures->updateMembershipAchievementsCounter($customerkey, 'onlineOfflineMeeting');		
        		
				$this->updateHostLevelKey($customerkey);
			}
			
			//add activity log
			$arrActivityLog = array();
			array_push($arrActivityLog, 
						array(
								'modulekey' => 1,
								'templatekey' => 5, 
								'refkey' => $customerkey, 
								'refDate' => $this->formatDBDate($rsMeeting[0]['trdate'],'d / m / Y H:i'),
                                'refGMT' =>  $rsMeeting[0]['gmt'],
								'userkey' => $rsMeeting[0]['hostkey']
							) ,
						array(
								'modulekey' => 1,
								'templatekey' => 4,  
								'refkey' => $rsMeeting[0]['hostkey'], 
								'refDate' => $this->formatDBDate($rsMeeting[0]['trdate'],'d / m / Y H:i'),
                                'refGMT' =>  $rsMeeting[0]['gmt'],
								'userkey' => $customerkey
							) 
					  ); 
			 
			$activityLog = new ActivityLog();
			$activityLog->addNewLog($arrActivityLog); 
			
			// kirim email
			$this->sendReminderEmail($customerkey,$meetingkey,'iCommunity Business Matching Reminder');
				
			$this->addErrorList($arrayToJs, true, $this->lang['dataHasBeenSuccessfullyUpdated']); 
			
			$this->oDbCon->endTrans(); //ini penting
			
		} catch (Exception $e) {
			$this->oDbCon->rollback();
			$this->addErrorList($arrayToJs, false, $e->getMessage());
		}
 
        return $arrayToJs;
    }
	
 	function updateHostLevelKey($customerkey) {

		// semua sudah dhitung berdasarkan yg checkin
		
		// khusus IBM
		
		if($this->meetingType != 1) return;
		
        $customer = new Customer();

        $rsCustomer = $customer->searchDataRow(array($customer->tableName.'.hostlevelkey',$customer->tableName.'.membershiplevel'),
											   'and '. $customer->tableName.'.pkey='. $this->oDbCon->paramString($customerkey)
											  );

        if (empty($rsCustomer)) return;

        $updateLevelHost = 0; 
       
		// hosting masuk dalam "peserta"
		// khusus kalo peserta pro keatas
		$totalHost = 0;
		$totalJoinMeeting = 0;
		
		if($rsCustomer[0]['membershiplevel'] == 3){
			$totalJoinMeeting = $this->countJoinMeeting($customerkey); 
			$totalHost = $this->countHostMeeting($customerkey);

			//$this->setLog(' total : '. ($totalJoinMeeting + $totalHost),true);


			if( ($totalJoinMeeting + $totalHost) >= $this->hostLevelThreshold['join'] ) 
				$updateLevelHost = 1;

			if( $totalHost >=  $this->hostLevelThreshold['host']  ) 
				$updateLevelHost =2; 
		}
      
	 
		// khusus beberapa user jadi HOST
		$excCust = array(8014, 8066,8064,8065,8045,8083, 8167,8171,8167,8183,8190,8106,8084,8497,8513,8329,8565); //8167 ini official
		if(in_array($customerkey,$excCust) && $updateLevelHost == 0)
			$updateLevelHost = 1;
		
		
		// khusus beberapa user jadi MASTER HOST
		$excCust = array(8015);
		if(in_array($customerkey,$excCust ))
			$updateLevelHost = 2;
			
        try{ 
            if(!$this->oDbCon->startTrans())
                throw new Exception($this->errorMsg[100]);
                
            $sql = 'update '.$customer->tableName.'
                set 
					'.$customer->tableName.'.hostlevelkey = '.$this->oDbCon->paramString($updateLevelHost).',
					'.$customer->tableName.'.totalhost = '.$this->oDbCon->paramString($totalHost).',
					'.$customer->tableName.'.totaljoin = '.$this->oDbCon->paramString($totalJoinMeeting).'
                where '.$customer->tableName.'.pkey = '.$this->oDbCon->paramString($customerkey);
			
            $this->oDbCon->execute($sql);
            $this->oDbCon->endTrans(); 
			
        } catch(Exception $e){
            $this->oDbCon->rollback(); 
            $this->addErrorList($arrayToJs,false, $e->getMessage()); 
            
        }
    }

    function countJoinMeeting($customerkey) {
        $sql = 'select 
					    count('.$this->tableName.'.pkey) as totaljoinmeeting
				    from 
                        '.$this->tableName . ' 
						left join ' . $this->tableNameDetail . ' on ' . $this->tableName . '.pkey = ' . $this->tableNameDetail . '.refkey
				    where 
						'.$this->tableNameDetail.'.customerkey =  ' .$this->oDbCon->paramString($customerkey). ' and 
						'.$this->tableNameDetail.'.ischeckin = 1 and
						'.$this->tableName.'.statuskey in (1,2,3) and 
						'.$this->tableName.'.trdate  < now() and 
						'.$this->tableName.'.meetingtypekey = ' . $this->meetingType;
		 
		$rs = $this->oDbCon->doQuery($sql);
 
		return $rs[0]['totaljoinmeeting'];
    }
	
    function countHostMeeting($customerkey) {
        $sql = 'select
					    count('.$this->tableName.'.pkey) as totalhostmeeting 
				    from 
                        '.$this->tableName.' 
				    where 
					    '.$this->tableName.'.hostkey =  ' .$this->oDbCon->paramString($customerkey).'  and
                        '.$this->tableName.'.statuskey in (1,2,3) and 
						'.$this->tableName.'.trdate  < now() and	 
						'.$this->tableName.'.meetingtypekey = ' . $this->meetingType;
		 
        $rs = $this->oDbCon->doQuery($sql);               

        return $rs[0]['totalhostmeeting'];
    }
	
 	function getAllMeeting($hostKey, $criteria = '', $limit = '')  { 
        $sql = '
                 select
                     ' . $this->tableName . '.*,
                     ' . $this->tableNameDetail . '.refkey as detail,
                     ' . $this->tableNameDetail . '.customerkey,
                     ' . $this->tableLanguage . '.language as languagename,
                     ' . $this->tableOnlineOffline . '.name as meetingonlineofflinename,
                     ' . $this->tableOnlineChannel . '.name as onlinechannel,
                     ' . $this->tableMeetingPoint . '.name as meetingpointname,
                     ' . $this->tableStatus . '.status as statusname ,
                     ' . $this->tableCustomer . '.photofile as hostphoto,
                     ' . $this->tableCustomer . '.code as hostcode ,
                     ' . $this->tableCustomer . '.name as hostname ,
                     ' . $this->tableCustomer . '.membershiplevel ,
                     ' . $this->tableCustomer . '.companyname,
                     ' . $this->tableMembershipLevel . '.name as membershiplevelname,
					 ' . $this->tableJobPosition.'.name as jobpositionname,
                     concat(' . $this->tableCity . '.name,\', \','.$this->tableCityCategory.'.name ) as meetingpointcitycategoryname, 
                     ' . $this->tablePaymentType . '.name as paymenttypename
                 from 
                     ' . $this->tableName . '
                     left join ' . $this->tableCustomer . ' on ' . $this->tableName . '.hostkey = ' . $this->tableCustomer . '.pkey
					 left join ' . $this->tableMembershipLevel . ' on ' . $this->tableCustomer . '.membershiplevel = ' . $this->tableMembershipLevel . '.pkey
                     left join ' . $this->tableNameDetail . ' on ' . $this->tableName . '.pkey = ' . $this->tableNameDetail . '.refkey
                     left join ' . $this->tableLanguage . ' on ' . $this->tableName . '.languagekey = ' . $this->tableLanguage . '.pkey
                     left join ' . $this->tableOnlineChannel . ' on ' . $this->tableName . '.onlinechannelkey = ' . $this->tableOnlineChannel . '.pkey
                     left join ' . $this->tableOnlineOffline . ' on ' . $this->tableName . '.meetingonlineoffline = ' . $this->tableOnlineOffline . '.pkey
                     left join ' . $this->tableMeetingPoint . ' on ' . $this->tableName . '.locationkey = ' . $this->tableMeetingPoint . '.pkey
					 left join '.$this->tableCity.' on '.$this->tableMeetingPoint.'.citykey = '.$this->tableCity.'.pkey
					 left join '.$this->tableCityCategory.' on '.$this->tableCity.'.categorykey = '.$this->tableCityCategory.'.pkey
					 left join '.$this->tableJobPosition.' on '.$this->tableCustomer.'.jobpositionkey = '.$this->tableJobPosition.'.pkey
                    left join ' . $this->tablePaymentType . ' on ' . $this->tableName . '.paymenttypekey = ' . $this->tablePaymentType . '.pkey,
                    ' . $this->tableStatus . '
                 where  
				  	 ' . $this->tableName . '.meetingtypekey = ' . $this->meetingType .' and  
                     ' . $this->tableName . '.statuskey = ' . $this->tableStatus . '.pkey  and 
					 ( ' . $this->tableName . '.hostkey=' . $hostKey . 'OR ' . $this->tableNameDetail . '.customerkey =' . $hostKey . ')
					 
          ' . $criteria;
		
        $sql .= ' GROUP BY ' . $this->tableName . '.pkey' ;
        $sql .= $limit;
		
		//$this->setLog($sql,true);
        return $this->oDbCon->doQuery($sql);
    }
	
	function getHostInformation($arrMeetingKey){
 		
		if (!is_array($arrMeetingKey))
			$arrMeetingKey = array($arrMeetingKey);
		
		$sql = 'select 
					'.$this->tableName.'.pkey,
					'.$this->tableCustomer.'.membershiplevel
				from
					'.$this->tableName.',
					'.$this->tableCustomer.'
				where
					'.$this->tableName.'.pkey in ( '.$this->oDbCon->paramString($arrMeetingKey,',').' ) and
					'.$this->tableName.'.hostkey = '.$this->tableCustomer.'.pkey
				';
		
		$rs = $this->oDbCon->doQuery($sql); 
		return  array_column($rs,null,'pkey');
	}
	
	function getParticipantInformation($arrMeetingKey, $optReturn = array()){ 
		
		$arrReturn = array();
		
		if (!is_array($arrMeetingKey))
			$arrMeetingKey = array($arrMeetingKey);
		 
		$rsHostInformation = $this->getHostInformation($arrMeetingKey);
		
		$rsDetail = $this->getDetailWithRelatedInformation($arrMeetingKey);
		$rsDetail = $this->reindexDetailCollections($rsDetail,'refkey');    

		$arrMemberLevel = array(2,3);
		
		foreach($arrMeetingKey as $meetingkey){
		 	$arrDetail = (isset($rsDetail[$meetingkey])) ? $rsDetail[$meetingkey] : array();
			$arrHostInformation= $rsHostInformation[$meetingkey];
			$arrParticipantkey = array_column($arrDetail,'customerkey') ;
			 
			$arrTemp = array('member' => 0,
							 'maxmember' => $this->hostLevelThreshold['maxmember'], 
							 'guest' => 0,
							 'maxguest' =>  $this->hostLevelThreshold['maxguest'],
							 'maxparticipant' => $this->hostLevelThreshold['maxparticipant'],
							 'maxremindersent' =>  $this->hostLevelThreshold['maxremindersent'],
							 'participantkey' => $arrParticipantkey 
							);
			
			// biar gk berat
			if(in_array( 'detail' ,$optReturn)){  
				foreach($arrDetail as $detailKey=>$detailRow)
					$arrDetail[$detailKey]['phpthumbhash'] = getPHPThumbHash($detailRow['photofile']);
					
				$arrTemp['detail'] = $arrDetail;
			}
			
			$totalGuest = 0 ;
			$totalMember = 0 ; 
			foreach($arrDetail as $detailRow){
				if($detailRow['membershiplevel'] == 1) $totalGuest++;
				else if (in_array($detailRow['membershiplevel'] ,$arrMemberLevel)) $totalMember++;
			}

			// tambah si host itu sendiri
			if ($arrHostInformation['membershiplevel'] == 1) $totalGuest++;
			if (in_array($arrHostInformation['membershiplevel'] ,$arrMemberLevel)) $totalMember++;

			$arrTemp['guest'] = $totalGuest;
			$arrTemp['member'] = $totalMember;
			
			$arrReturn[$meetingkey] = $arrTemp;

		}
		
		return $arrReturn;
	}
	
	function sendHostCancelEmail($customerkey){
		
        global $twig;
         
		$customer = new Customer();
		$title = 'IBM yang Anda ikuti dibatalkan oleh host';
		
		// kirim email
        $rsCust = $customer->searchDataRow(array($customer->tableName.'.name',$customer->tableName.'.email',$customer->tableName.'.langkey',$customer->tableName.'.mobilecode',$customer->tableName.'.mobile'),
										   ' and '. $customer->tableName.'.pkey =  ' .  $customer->oDbCon->paramString($customerkey)
										  ); 
		 
		
        $lang = new Lang();
        $rsLang = $lang->searchDataRow(array($lang->tableName.'.code'),
                                ' and '.$lang->tableName.'.pkey = '.$this->oDbCon->paramString($rsCust[0]['langkey'])
                              );

        $arrTwigVar = array();
        $arrTwigVar = $this->getDefaultEmailVariable();
         
        $arrTwigVar['CUSTOMER_NAME'] = $rsCust[0]['name'];
			
        $content = $twig->render($this->getLangTemplatePath('email-host-cancel.html',true,$rsLang[0]['code']), $arrTwigVar); 
		$this->sendMail(array(), $title,$content,array('name' => $rsCust[0]['name'], 'email'=>$rsCust[0]['email']));
		
		// kirim WA 
		// content WA harus bisa disetting per user
		if(!empty($this->loadSetting('WAGatewayAPIKey'))){ 
			$content = $twig->render($this->getLangTemplatePath('wa-host-cancel.html',true,$rsLang[0]['code']), $arrTwigVar);
			$content = html_entity_decode(strip_tags($content));
            
            if(!empty($rsCust[0]['mobilecode'])) $rsCust[0]['mobile'] = $rsCust[0]['mobilecode'] . $rsCust[0]['mobile'];
 			$this->sendWA($rsCust[0]['mobile'],$content,true);
		}
		
	}
	
	
	function sendReminderEmail($userkey,$meetingkey,$title){
		
        global $twig;
         
		$customer = new Customer();
		
		// kirim email 
        $rsMeeting = $this->getDataRowById($meetingkey);
		if (empty($rsMeeting)) return;
		
        $rsCust = $customer->searchDataRow(array($customer->tableName.'.name',$customer->tableName.'.email',$customer->tableName.'.langkey',$customer->tableName.'.gmt',$customer->tableName.'.mobilecode',$customer->tableName.'.mobile'),
										   ' and '. $customer->tableName.'.pkey =  ' .  $customer->oDbCon->paramString($userkey)
										  );
		 
        $rsHost = $customer->searchDataRow(array($customer->tableName.'.name',$customer->tableName.'.email'),
										   ' and '. $customer->tableName.'.pkey =  ' .  $this->oDbCon->paramString($rsMeeting[0]['hostkey'])
										  );
		   
        $lang = new Lang();
        $rsLang = $lang->searchDataRow(array($lang->tableName.'.code'),
                                ' and '.$lang->tableName.'.pkey = '.$this->oDbCon->paramString($rsCust[0]['langkey'])
                              );

      
        
        $rsMeeting[0]['trdate'] = $this->convertToLocalTimeZone($rsMeeting[0]['trdate'],$rsMeeting[0]['gmt'] ,$rsCust[0]['gmt']);
    
         
        $arrTwigVar = array();
        $arrTwigVar = $this->getDefaultEmailVariable();
         
        $arrTwigVar['CUSTOMER_NAME'] = $rsCust[0]['name'];
        $arrTwigVar['HOST_NAME'] = $rsHost[0]['name'];
        $arrTwigVar['MEETING_LINK'] = str_replace(chr(13),'<br>',$rsMeeting[0]['meetinglink']);
			
        $arrTwigVar['DAY_NAME'] =  $this->toLocalDate($this->formatDBDate($rsMeeting[0]['trdate'],'l'),$rsLang[0]['code']);
        $arrTwigVar['DATE'] =  $this->formatDBDate($rsMeeting[0]['trdate'],'d / m / Y');
        $arrTwigVar['HOUR'] = $this->formatDBDate($rsMeeting[0]['trdate'],'H:i');
		$arrTwigVar['TIMEZONE'] = $rsCust[0]['gmt'];

        if($rsMeeting[0]['meetingonlineoffline'] == 2){
           $emailTemplate = 'email-meeting-reminder-offline.html';
           $waTemplate = 'wa-meeting-reminder-offline.html';
           $arrTwigVar['MEETING_VENUE'] = $rsMeeting[0]['locationname'];
           $arrTwigVar['MEETING_ADDRESS'] = $rsMeeting[0]['locationaddress'];
       }else{
           $emailTemplate = 'email-meeting-reminder.html';
           $waTemplate = 'wa-meeting-reminder.html';
       }
        
        $content = $twig->render($this->getLangTemplatePath($emailTemplate,true,$rsLang[0]['code']), $arrTwigVar);
        $this->sendMail(array(), $title,$content,array('name' => $rsCust[0]['name'], 'email'=>$rsCust[0]['email']));
		
		// kirim WA 
		// content WA harus bisa disetting per user
		if(!empty($this->loadSetting('WAGatewayAPIKey'))){
            $content = $twig->render($this->getLangTemplatePath($waTemplate,true,$rsLang[0]['code']), $arrTwigVar);
			$content = html_entity_decode(strip_tags($content)); 
            
            if(!empty($rsCust[0]['mobilecode'])) $rsCust[0]['mobile'] = $rsCust[0]['mobilecode'] . $rsCust[0]['mobile'];
			$this->sendWA($rsCust[0]['mobile'],$content,true);
		}
		 
	}
	
	function sendReminderOneOnOneEmail($meetingkey){
		
        global $twig;
         
		$title = 'One on One Reminder';
		
		$customer = new Customer();
		
		// kirim email 
        $rsMeeting = $this->getDataRowById($meetingkey);
		if (empty($rsMeeting)) return;
		
        $rsHost = $customer->searchDataRow(array($customer->tableName.'.name',$customer->tableName.'.email',$customer->tableName.'.langkey',$customer->tableName.'.gmt',$customer->tableName.'.mobilecode',$customer->tableName.'.mobile'),
										   ' and '. $customer->tableName.'.pkey =  ' .  $this->oDbCon->paramString($rsMeeting[0]['hostkey'])
										  );
		
		$rsPartner = $customer->searchDataRow(array($customer->tableName.'.name',$customer->tableName.'.email',$customer->tableName.'.langkey',$customer->tableName.'.gmt',$customer->tableName.'.mobilecode',$customer->tableName.'.mobile'),
										   ' and '. $customer->tableName.'.pkey =  ' .  $customer->oDbCon->paramString($rsMeeting[0]['partnerkey'])
										  );
        
        	 
        $lang = new Lang();
        $rsLang = $lang->searchDataRow(array($lang->tableName.'.code'),
                                        ' and '.$lang->tableName.'.pkey = '.$this->oDbCon->paramString($rsHost[0]['langkey'])
                                      ); 
      
        
        $arrTwigVar = array();
        $arrTwigVar = $this->getDefaultEmailVariable();
          
        
		// utk host
		$arrTwigVar['CUSTOMER_NAME'] = $rsHost[0]['name'];
        $arrTwigVar['PARTNER_NAME'] = $rsPartner[0]['name'];
        
        $rsMeeting[0]['trdate'] = $this->convertToLocalTimeZone($rsMeeting[0]['trdate'],$rsMeeting[0]['gmt'] ,$rsHost[0]['gmt']);
		$arrTwigVar['DAY_NAME'] =  $this->toLocalDate($this->formatDBDate($rsMeeting[0]['trdate'],'l'),$rsLang[0]['code']);
        $arrTwigVar['DATE'] =  $this->formatDBDate($rsMeeting[0]['trdate'],'d / m / Y');
        $arrTwigVar['HOUR'] = $this->formatDBDate($rsMeeting[0]['trdate'],'H:i');
		$arrTwigVar['TIMEZONE'] = $rsHost[0]['gmt'];
        
        $content = $twig->render($this->getLangTemplatePath('email-host-one-reminder.html',true,$rsLang[0]['code']), $arrTwigVar);
		$this->sendMail(array(), $title,$content,array('name' => $rsHost[0]['name'], 'email'=>$rsHost[0]['email']));
		
		if(!empty($this->loadSetting('WAGatewayAPIKey'))){  
            $content = $twig->render($this->getLangTemplatePath('wa-host-one-reminder.html',true,$rsLang[0]['code']), $arrTwigVar);
			$content = html_entity_decode(strip_tags($content));
            
            if(!empty($rsHost[0]['mobilecode'])) $rsHost[0]['mobile'] = $rsHost[0]['mobilecode'] . $rsHost[0]['mobile'];
			$this->sendWA($rsHost[0]['mobile'],$content,true);
		}
		
		// utk partner
        
        $rsLang = $lang->searchDataRow(array($lang->tableName.'.code'),
                                        ' and '.$lang->tableName.'.pkey = '.$this->oDbCon->paramString($rsPartner[0]['langkey'])
                                      ); 
      
        
		$arrTwigVar['CUSTOMER_NAME'] = $rsPartner[0]['name'];
        $arrTwigVar['HOST_NAME'] = $rsHost[0]['name'];
        
        $rsMeeting[0]['trdate'] = $this->convertToLocalTimeZone($rsMeeting[0]['trdate'],$rsMeeting[0]['gmt'] ,$rsPartner[0]['gmt']);
		$arrTwigVar['DAY_NAME'] =  $this->toLocalDate($this->formatDBDate($rsMeeting[0]['trdate'],'l'),$rsLang[0]['code']);
        $arrTwigVar['DATE'] =  $this->formatDBDate($rsMeeting[0]['trdate'],'d / m / Y');
        $arrTwigVar['HOUR'] = $this->formatDBDate($rsMeeting[0]['trdate'],'H:i');
		$arrTwigVar['TIMEZONE'] = $rsPartner[0]['gmt'];
        
        $content = $twig->render($this->getLangTemplatePath('email-partner-one-reminder.html',true,$rsLang[0]['code']), $arrTwigVar);
		$this->sendMail(array(), $title,$content,array('name' => $rsPartner[0]['name'], 'email'=>$rsPartner[0]['email']));
		
		if(!empty($this->loadSetting('WAGatewayAPIKey'))){  
            $content = $twig->render($this->getLangTemplatePath('wa-partner-one-reminder.html',true,$rsLang[0]['code']), $arrTwigVar); 
			$content = html_entity_decode(strip_tags($content));
            
            if(!empty($rsPartner[0]['mobilecode'])) $rsPartner[0]['mobile'] = $rsPartner[0]['mobilecode'] . $rsPartner[0]['mobile'];
			$this->sendWA($rsPartner[0]['mobile'],$content,true);
		}
		 
	}
	
	
	function sendReminder($meetingkey,$userkey = '', $bypassTimeLimit = false){
		// kalo ad userkey, dari front end, wajib ada, dan sudah di die di ajax kalo gk ad userkey
		 
		// cek ulang userkey dan host nya sama tdk
		$rsMeeting = $this->searchDataRow(array($this->tableName.'.pkey',$this->tableName.'.trdate'),
										  ' and '.$this->tableName.'.hostkey = ' . $this->oDbCon->paramString($userkey).
										  ' and '.$this->tableName.'.pkey = ' . $this->oDbCon->paramString($meetingkey)
										  );

		if (empty($rsMeeting)) return;
		
		// cek waktu kirim, masih boleh gk
		if(!$bypassTimeLimit)
			if(!$this->inReminderTime($this->formatDBDate($rsMeeting[0]['trdate'],'d / m / Y H:i'))) 
				return;
									  

		$title = 'iCommunity Business Matching Reminder'; // tembak dulu
		
		  try {			
			  
                if (!$this->oDbCon->startTrans()) 
            		throw new Exception($this->errorMsg[100]);
			  		

				// ambil semua detail peserta 
				$rsDetail = $this->getDetailWithRelatedInformation($meetingkey);

				foreach($rsDetail as $row) 
					$this->sendReminderEmail($row['customerkey'],$meetingkey, $title);

				// update counter reminder
				$sql = 'update '.$this->tableName.' set '.$this->tableName.'.remindersent = '.$this->tableName.'.remindersent + 1 where '.$this->tableName.'.pkey = ' .$this->oDbCon->paramString($meetingkey);
			  	$this->oDbCon->execute($sql);
			  
				$this->oDbCon->endTrans(); 
            } catch (Exception $e) {
                $this->oDbCon->rollback(); 
            }
		 
		
	}
	
	
	function sendIBMEmail($rsCust, $counter){
		
        global $twig;
        
        $arrTwigVar = array();
        $arrTwigVar = $this->getDefaultEmailVariable();
     
        $arrTwigVar['CUSTOMER_NAME'] = $rsCust['name']; 
        $arrTwigVar['TOTAL_REF'] = $counter['businessRefer'];
        $arrTwigVar['TOTAL_AMOUNT'] = $counter['transactionAmount'];
			 
        $lang = new Lang();
        $rsLang = $lang->searchDataRow(array($lang->tableName.'.code'),
                                        ' and '.$lang->tableName.'.pkey = '.$this->oDbCon->paramString($rsCust['langkey'])
                                      ); 
        $content = $twig->render($this->getLangTemplatePath('email-ibm.html',true,$rsLang[0]['code']), $arrTwigVar);
         
		//$this->sendMail(array(), 'iCommunity Business Matching',$content,array('name' => $rsCust['name'], 'email'=>$rsCust['email']));
		
		// kirim WA 
		// content WA harus bisa disetting per user
		if(!empty($this->loadSetting('WAGatewayAPIKey'))){ 
			$content = $twig->render($this->getLangTemplatePath('wa-ibm.html',true,$rsLang[0]['code']), $arrTwigVar); 
			$content = html_entity_decode(strip_tags($content));
            
            if(!empty($rsCust['mobilecode'])) $rsCust['mobile'] = $rsCust['mobilecode'] . $rsCust['mobile'];
			$this->sendWA($rsCust['mobile'],$content,true);
		}
	}
	
	
	function inReminderTime($trdate,$before = ''){
		
		if(empty($before))
			$before = $this->hostLevelThreshold['reminderBroadcastTime'];  
		
		$dateDiff = $this->dateDiff(date('d / m / Y H:i'),$trdate);  
        return ($dateDiff > 0 && $dateDiff < $before) ? true : false;
		
	}
	
	function inMeetingTime($trdate,$before=0,$after=3600){ 
		// default waktu meeting adalah  1 jam
		
		$dateDiff = $this->dateDiff(date('d / m / Y H:i'),$trdate);   
		
		// pastikan negative
		if ($after > 0) $after *= -1;
		
		if($after <> 0)
			return  ($dateDiff <= 0 && $dateDiff > $after) ? true : false;
			 
        return  ($dateDiff < 0) ? true : false;
	}
	
	function checkBeforeTime($trdate,$before=0,$after=0){
		// $before, $after utk extended time kalo nanti kepake
	 	
		$dateDiff = $this->dateDiff(date('d / m / Y H:i'),$trdate);  
		
//		if ($before > 0) 
//			return ($dateDiff > 0 && $dateDiff < $limit) ? true : false; 
		
        return  ($dateDiff > 0) ? true : false;
		
	}
	
	function getTotalParticipatedMeeting($customerkey, $onlineOfflineKey,$isAchievement = false){
		
		// GAMIFICATION
		// HOST ambil dari tgl meeting
		// PESERTA ambil dari tgl join
		
		
		// ACHIEVEMENT
		// HOST ambil dari tgl meeting, tdk masalah, karena ad atau gk ad peserta tetep dihitung
		// PESERTA ambil dari tgl absen
		
		// gk masalah utk achievement karena nanti di where, yearnya aj jg bisa
		$datePeriod = date('01 / m / Y');
		
		$total = 0;
		
		// sebagai peserta
		$generalCriteria = $this->tableName.'.statuskey in (1,2,3) and
						   '.$this->tableName.'.meetingtypekey = ' . $this->meetingType.' and
						   '.$this->tableName.'.meetingonlineoffline in (' . $this->oDbCon->paramString($onlineOfflineKey,',') .')';
		
		 
		$sql = 'select coalesce(count('.$this->tableName.'.pkey),0)  as total
				from '.$this->tableName.','.$this->tableNameDetail.'
				where
					'.$generalCriteria .' and
					'.$this->tableName.'.pkey = '.$this->tableNameDetail.'.refkey and
					'.$this->tableNameDetail.'.customerkey = ' . $this->oDbCon->paramString($customerkey) .' and 
					year('.$this->tableNameDetail.'.joindate) = '.$this->oDbCon->paramDate($datePeriod,'/', 'Y');
		
		
		if(!$isAchievement)
			$sql .= ' and month('.$this->tableNameDetail.'.joindate) = '.$this->oDbCon->paramDate($datePeriod,'/', 'm'); 
		else
			$sql .= ' and '.$this->tableNameDetail.'.ischeckin = 1';
	
		
//		$this->setLog('>> getTotalParticipatedMeeting ',true);
//		$this->setLog($sql,true);
 
		$rs = $this->oDbCon->doQuery($sql);
		$total += $rs[0]['total'];
		
		// tambah counter sebagai host  
		$sql = 'select coalesce(count('.$this->tableName.'.pkey),0)  as total
				from '.$this->tableName.' 
				where 
					'.$generalCriteria .' and
					'.$this->tableName.'.hostkey = ' . $this->oDbCon->paramString($customerkey) .' and
					year('.$this->tableName.'.trdate) = '.$this->oDbCon->paramDate($datePeriod,'/', 'Y').'
				';
	
		if(!$isAchievement)
			 $sql .= ' and month('.$this->tableName.'.trdate) = '.$this->oDbCon->paramDate($datePeriod,'/', 'm');
			
//		$this->setLog($sql,true);
	
		$rs = $this->oDbCon->doQuery($sql);
		$total += $rs[0]['total'];
			
		return $total ;
		
	}
	
	function getTotalParticipatedOneOnOne($customerkey, $onlineOfflineKey = '', $isAchievement = false){
		 
		$datePeriod = date('01 / m / Y');
			
		$total = 0;
		
		$generalCriteria = $this->tableName.'.statuskey in (1,2,3) and
						   '.$this->tableName.'.meetingtypekey = ' . $this->meetingType;
		
		if(!empty($onlineOfflineKey))
			$generalCriteria .= 'and '.$this->tableName.'.meetingonlineoffline = ' . $this->oDbCon->paramString($onlineOfflineKey);
		 
		// tambah counter sebagai host  
		$sql = 'select coalesce(count('.$this->tableName.'.pkey),0)  as total
				from '.$this->tableName.' 
				where 
					'.$generalCriteria .' and
					 ( '.$this->tableName.'.hostkey = ' . $this->oDbCon->paramString($customerkey) .' OR '.$this->tableName.'.partnerkey = ' . $this->oDbCon->paramString($customerkey) .' ) and
					year('.$this->tableName.'.trdate) = '.$this->oDbCon->paramDate($datePeriod,'/', 'Y').'
				';
	
		if(!$isAchievement)
			$sql .= ' and month('.$this->tableName.'.trdate) = '.$this->oDbCon->paramDate($datePeriod,'/', 'm'); 
		else
			$sql .= ' and '.$this->tableName.'.ischeckin = 1';
		
		//$this->setLog('>> getTotalParticipatedOneOnOne ',true);
		//$this->setLog($sql,true);
		
		$rs = $this->oDbCon->doQuery($sql);
		$total += $rs[0]['total'];
			
		return $total ;
		
	}
	
	function getLatestCheckedIn($customerkey){
		
		// sementara status meeting "menunggu" jg termasuk, karena gk pernah diupdate statusnya
		
		$sql = 'select  
					'.$this->tableName.'.code, '.$this->tableName.'.trdate,'.$this->tableName.'.hostkey 
				from 
					'.$this->tableName.', '.$this->tableNameDetail.'
				where 
					'.$this->tableName.'.pkey =  '.$this->tableNameDetail.'.refkey and
					'.$this->tableName.'.statuskey in (1,2,3) and
				 	'.$this->tableNameDetail.'.ischeckin = 1 and
					'.$this->tableNameDetail.'.customerkey = '.$this->oDbCon->paramString($customerkey).' 
				order by
					'.$this->tableName.'.trdate desc limit 1
		' ;
		
		return $this->oDbCon->doQuery($sql);
		
	} 
 
    
}
?>