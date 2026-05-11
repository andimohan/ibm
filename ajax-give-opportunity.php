<?php
require_once '_config.php'; 
require_once '_include-fe-v2.php';
require_once '_global.php';  
	 
includeClass(array('GiveOpportunity.class.php'));

$giveOpportunity = new GiveOpportunity();

if (isset($_POST) && !empty($_POST['action'])) {

		foreach ($_POST as $k => $v) { 
			if (!is_array($v))
				 $v = trim($v);  

			$arr[$k] = $v;     
		}  

		$arrReturn = array();  

		switch ($_POST['action']) {
			case 'add':
					if(empty(USERKEY)) die;
				
					$arr['code'] = 'XXXXX';
					$arr['refkey'] = USERKEY;
					$arr['selStatus'] = 1;  
					$arr['fromFE'] = 1;
					
					$arrReturn = $giveOpportunity->addData($arr);
					break;
				
			case 'followup' : 
				
					if(empty(USERKEY)) die;
				
					$arr['code'] = 'XXXXX'; 
					$arr['chkIsFollowUp'] = 1;  
					$arr['hidUserKey'] = USERKEY;
					$arr['fromFE'] = 1;
					
					$arrReturn = $giveOpportunity->updateFollowUp($arr);
					break;

									
			case 'nodeal' : 
				
					if(empty(USERKEY)) die;
				
					$arr['code'] = 'XXXXX';  
					$arr['hidUserKey'] = USERKEY;
					$arr['fromFE'] = 1;
					
					$arrReturn = $giveOpportunity->updateNoDeal($arr);
					break;

						
			case 'deal' : 
				
					if(empty(USERKEY)) die;
				
					$arr['code'] = 'XXXXX';  
					$arr['hidUserKey'] = USERKEY;
					$arr['fromFE'] = 1;
					
					$arrReturn = $giveOpportunity->updateDeal($arr);
					break;

					
		}; 

		echo json_encode($arrReturn);  
		die;  
}

	 
?>