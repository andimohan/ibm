<?php
require_once '_config.php'; 
require_once '_include-fe-v2.php';
require_once '_global.php';  
	 
includeClass(array('ILCMember.class.php'));
$ilcMember = new ILCMember(); 
 
 
	if (isset($_POST) && !empty($_POST['type'])) {
		
		 $isAvailable = true;
		if ( $_POST['type'] == 'check' ){
			switch ($_POST['fieldtype']) {
				case 'email':
					$email = $_POST['email']; 
					$rsEmail = $ilcMember->isValueExisted('','email',$email);
					// Check the email existence ...
					if(count ($rsEmail) <> 0)
						$isAvailable = false;
					break;
		
				case 'email-negation':
					$email = $_POST['email'];
					$rsEmail = $ilcMember->isValueExisted('','email',$email);
					// Check the email existence ...
					if(count ($rsEmail) == 0)
						$isAvailable = false;
						
					break;
		 
			}
		
			echo json_encode(array(
				'valid' => $isAvailable,
			)); 
			die; 
		} 
		
	}	
	

	 
	if (isset($_POST) && !empty($_POST['action'])) {
		
			foreach ($_POST as $k => $v) { 
				if (!is_array($v))
					 $v = trim($v);  
				
				$arr[$k] = $v;     
			}  
			 
			$arrReturn = array();  
			
			switch ($_POST['action']) {
				case 'add':
								$arr['code'] = 'XXXXX';
								$arr['createdBy'] = 0;
								$arr['selStatus'] = 1;    
								$arr['fromFE'] = 1;
                      
								$arrReturn = $ilcMember->addData($arr); 
								break; 
			}; 
			
			echo json_encode($arrReturn);  
			die;  
	}
	
	 
?>