<?php
  
if(isset($_GET) && !empty($_GET['domain'])){  
	$PROTOCOL = (!empty($_SERVER['HTTPS'])) ? 'https' : 'http'; 
    header('Location: '. $PROTOCOL . '://'. $_GET['domain'].'/lazada-update-auth/'.$_GET['code']);
    die; 
}
 
?>
