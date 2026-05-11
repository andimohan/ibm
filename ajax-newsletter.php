<?php
require_once '_config.php'; 
require_once '_include-fe-v2.php';
require_once '_global.php';  
 

includeClass(array("NewsletterSubscription.class.php"));
$newsletterSubscription = new NewsletterSubscription();

$arr = array();
foreach ($_POST as $k => $v) { 
	if (!is_array($v)) $v = trim($v);   
	$arr[$k] = $v;   
}  
 

// biar kedepan bisa utk pembayaran jenis registrasi, deposit, dsb
  
switch($_POST['action']){
	case 'add' :  		$valid = true;
		
						$arrReturn = array(); 
					
						$_SESSION['newsletterLoaded'] = true;
		
						$email = (isset($_POST['email'])) ? $_POST['email'] : '';
						if (empty($email)) die;
						
						// cek email ad atau sdh terdaftar blm
						$rsNewsletter = $newsletterSubscription->searchDataRow(array($newsletterSubscription->tableName.'.pkey'),
																			   ' and '. $newsletterSubscription->tableName.'.email = ' . $newsletterSubscription->oDbCon->paramString($email)
																			  );

						if(!empty($rsNewsletter)) die;
		 
				 
						$arr['code'] = 'XXXXX';
						$arr['email'] = $email; 
						$arrReturn = $newsletterSubscription->addData($arr); 
					  
						echo json_encode($arrReturn); 
					    break;
 
		
}

die; 
	
?>