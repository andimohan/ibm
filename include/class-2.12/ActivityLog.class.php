<?php

class ActivityLog extends BaseClass{
 
   function __construct(){
		
		parent::__construct();

		$this->tableName = 'activity_log';  
		$this->tableCustomer = 'customer';  
	   
	    $this->arrData = array();
        $this->arrData['pkey'] = array('pkey'); 
        $this->arrData['code'] = array('code');
        $this->arrData['refkey'] = array('refkey');
        $this->arrData['refdate'] = array('refDate', 'datetime');
        $this->arrData['refgmt'] = array('refGMT');
        $this->arrData['amount'] = array('amount', 'number');
        $this->arrData['modulekey'] = array('modulekey');  
        $this->arrData['templatekey'] = array('templatekey');  
        $this->arrData['userkey'] = array('userkey');  
        $this->arrData['levelkey'] = array('levelkey');  
        $this->arrData['transkey'] = array('transkey');  
	   
//        $this->arrDataListAvailableColumn = array(); 
//        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 70));
//        array_push($this->arrDataListAvailableColumn, array('code' => 'location','title' => 'location','dbfield' => 'name','default'=>true,'width' => 250));
//        array_push($this->arrDataListAvailableColumn, array('code' => 'city','title' => 'city','dbfield' => 'citycategoryname','default'=>true, 'width' => 200));
//        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
             
	   
	    $this->includeClassDependencies(array(
                'Customer.class.php' ,
				'MembershipLevel.class.php'
			)
		);
	    
		$this->overwriteConfig();
		
   }
    
	function getQuery(){
	   
	   $sql = '
			select
					'.$this->tableName. '.*,
					'.$this->tableCustomer. '.name as customername
				from 
					'.$this->tableName . '  
					 left join '.$this->tableCustomer. ' on 
					 '.$this->tableName . '.refkey = '.$this->tableCustomer. '.pkey 
				where  		 
					1=1
					
 		' .$this->criteria ; 
		   
        return $sql;
    }
	
	function addNewLog($arrParam){ 
		foreach($arrParam as $row){
			
			if(!isset($row['userkey']))  
				$arrUserkey = array();
			else
				$arrUserkey = (!is_array($row['userkey'])) ? array($row['userkey']) : $row['userkey'];
			
			foreach($arrUserkey as $userkey){ 
				$row['userkey'] = $userkey;
				$row['code'] = 'xxxx';
				$this->addData($row);
			}
		}
	}
	
	function cancelLog($transkey){
		$sql = 'delete from '.$this->tableName. ' where transkey = '.$this->oDbCon->paramString($transkey);
		$this->oDbCon->execute($sql);
	}
	
	function compileActivityLog(&$rs){
		$customer = new Customer();
		$membershipLevel = new MembershipLevel();
		
		$totalRs = count($rs);
		 
        
		$arrUserkey = array_column($rs,'userkey');
		$arrLevelkey =  array_column($rs,'levelkey');
		
		$rsCustomer = $customer->searchDataRow(array($customer->tableName.'.pkey',$customer->tableName.'.name',$customer->tableName.'.gmt'),
											  ' and '.$customer->tableName.'.pkey in ('.$this->oDbCon->paramString($arrUserkey,',').')');
		
		$rsCustomer = array_column($rsCustomer,null,'pkey');
			
		$rsMembershipLevel = $membershipLevel->searchDataRow(array($membershipLevel->tableName.'.pkey',$membershipLevel->tableName.'.name'),
											  ' and '.$membershipLevel->tableName.'.pkey in ('.$this->oDbCon->paramString($arrLevelkey,',').')');
		
		$rsMembershipLevel = array_column($rsMembershipLevel,null,'pkey');
			
		for($i=0;$i<$totalRs;$i++){
			$templatekey = $rs[$i]['templatekey'];
			$modulekey = $rs[$i]['modulekey'];
			$userkey = $rs[$i]['userkey'];
			$refkey = $rs[$i]['refkey'];
			$refdate = $rs[$i]['refdate']; 
            
            $refdate = $this->convertToLocalTimeZone($refdate,$rs[$i]['refgmt'], LOCAL['timezone']['userGMT']); 
             
			$amount = $rs[$i]['amount'];
			$levelkey = $rs[$i]['levelkey'];
			
			$customerName = $rsCustomer[$userkey]['name'];
			$levelName = $rsMembershipLevel[$levelkey]['name'];
			
			$desc = $this->getMessageTemplate($templatekey);
			$moduleName = $this->getModuleObj($modulekey);
			
			$arrSearch = array('{{ USER_NAME }}','{{ MODULE_NAME }}','{{ REF_DATE }}','{{ AMOUNT }}','{{ LEVEL_NAME }}');
			$arrReplace = array('<span class="entity">'.$customerName.'</span>',
								'<span class="entity">'.$moduleName.'</span>',
								$this->toLocalDate($this->formatDBdate($refdate,'d M Y')), 
								'<span class="entity">'.$this->formatNumber($amount).'</span>', 
								'<span class="entity">'.$levelName.'</span>' );
			
			
			$desc = str_replace($arrSearch,$arrReplace,$desc);
			
			 
			$rs[$i]['desc'] = $desc;
		}
			
	}
	 
	function getModuleObj($key){
		$arr = array();
		
	    $arr[1] = 'Business Matching';
	    $arr[2] = 'One on One';
	    $arr[3] = $this->lang['settings'];
	    $arr[4] = $this->lang['profile'];
	    $arr[5] = $this->lang['bonusReferral'];
	    $arr[6] = $this->lang['bankInformation'];
	    $arr[7] = $this->lang['giveOpportunity'];
		
		return $arr[$key];
	}
	
	function getMessageTemplate($key){
		$arr = array();
		 
	   
	    $arr[1] = $this->lang['youCreateModule'];
	    $arr[2] = $this->lang['youInviteModule'];
	    $arr[3] = $this->lang['youAreInviteModule'];
	    $arr[4] = $this->lang['registeredAtModule'];
	    $arr[5] = $this->lang['youRegisteredModuleThatHosted'];
	    $arr[6] = $this->lang['canceledparticipateModuleOn'];
	    $arr[7] = $this->lang['youCancelledModuleThatHosted'];
		$arr[8] = $this->lang['hostCanceledparticipateModuleOn'];
		$arr[9] = $this->lang['youCancelledModule'];
		$arr[10] = $this->lang['youUpgradeMembership'];
		$arr[11] = $this->lang['attendedYourModule'];
		$arr[12] = $this->lang['youAttendedModuleThatHosted'];
		$arr[13] = $this->lang['youCancelUserAttendance'];
		$arr[14] = $this->lang['youAttendanceCanceledBy'];
		$arr[15] = $this->lang['youCanceledModuleWith'];
		$arr[16] = $this->lang['cancelledModule'];
		$arr[17] = $this->lang['youMakeChanges'];
		$arr[18] = $this->lang['youReceivedReferral'];
		$arr[19] = $this->lang['shareNewModule'];
		$arr[20] = $this->lang['youReceivedModule'];
		$arr[21] = $this->lang['shareAModule'];
		$arr[22] = $this->lang['youFollowUpModule'];
		$arr[23] = $this->lang['didAFollowUp'];
		$arr[24] = $this->lang['youChangedNoDeal'];
		$arr[25] = $this->lang['changeModuleToNoDeal'];
		$arr[26] = $this->lang['youUpdateModuleToDeal'];
		$arr[27] = $this->lang['changeModuleToDeal'];
		$arr[28] = $this->lang['youAttendedModule'];
		 
		return $arr[$key];
	}
  }

?>