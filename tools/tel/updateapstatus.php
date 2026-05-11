<?php
die('sudah di nonaktifkan');
include_once '../../_config.php'; 
include_once '../../_include.php';  

if(!$security->isAdminLogin('SecurityPrivileges',10,true)); 

$rsStatus = array();
$rsStatus[0]['pkey'] = 1;
$rsStatus[0]['label'] = 'Open';
$rsStatus[1]['pkey'] = 3;
$rsStatus[1]['label'] = 'Closing';

$arrStatus = $class->convertForCombobox($rsStatus,'pkey','label'); 
    
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">  
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 
<link rel="stylesheet" type="text/css" href="<?php echo $class->adminCssPath; ?>jquery-font-awesome.min.css">  
<link rel="stylesheet" type="text/css" href="<?php echo $class->adminCssPath; ?>jquery-ui.min.css" />    
<link rel="stylesheet" type="text/css" href="<?php echo $class->adminCssPath; ?>bootstrap.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo $class->adminCssPath; ?>sol.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo $class->adminCssPath.ADMIN_CSS_VERSION; ?>">  
     
<script type="text/javascript" src="<?php echo $class->defaultJsPath; ?>jquery-3.3.1.min.js"></script>  
<script type="text/javascript" src="<?php echo $class->defaultJsPath; ?>sol.js"></script>  

<script>
    jQuery(document).ready(function(){  


		
    }) 
</script>    
    
<title>Update Modul</title>  
<style>
    .package{list-style: none; padding: 0; margin: 0}
    .package li { cursor: pointer; float:left; border-radius: 0.3em; margin-right: 0.5em; border:1px solid #999; display: inline-block; padding: 0.3em 0.5em; background-color: #dedede}
    .package li:hover {background-color: #999;}
</style>
</head> 
<body style="margin:1em">    
    <form method="post" action="updateapstatus.php">
     	<?php echo $class->inputHidden('action', array('value' => 'update')); ?>
    	<div>Update Status</div>
     	<?php echo  $class->inputSelect('selStatus', $arrStatus); ?>
		<br>
    	<div>AP Code</div>
     	<?php echo  $class->inputTextArea('apCode', array('etc' => 'style="height:10em;"')); ?>
     	<br> 
		<div style="clear:both; height: 1em"></div>
     	<?php echo $class->inputSubmit('btnSubmit','Submit'); ?>
    </form>
    
</body> 
</html> 
<?php 
  
if(isset($_POST) && !empty($_POST['btnSubmit'])){
    $arrAP = array();
    $statuskey = $_POST['selStatus'];
    $apCode = $_POST['apCode'];
    //$apCode =  preg_replace("/\s+/", "", $apCode);
    
    $arrAP = preg_split('/[\ \n\,]+/', $apCode);
    foreach($arrAP as $key=>$row) 
        $arrAP[$key] = trim($row);
    
	//$arrAP = explode(',',$apCode);
    
	$class->oDbCon->startTrans(); 
	
	$sql = '
			select 
				ap_payment_detail.* ,ap.code as apcode ,ap_payment_header.code as paymentcode 
			from  
				ap_payment_detail,ap_payment_header,ap
			where
				ap_payment_detail.refkey = ap_payment_header.pkey and
				ap_payment_detail.apkey = ap.pkey and
				ap.code in(' . $class->oDbCon->paramString($arrAP,',').') and
				(ap_payment_header.statuskey in (2,3))
		   ';
	$rs = $class->oDbCon->doQuery($sql);
    
	echo '<br><br>';
    
	if(!empty($rs)){ 
        foreach($rs as $row) 
            echo '<div class="text-red-cardinal"><strong>'.$row['apcode'].'</strong>. ' . $class->errorMsg[212].' pembayaran sudah dilakukan ' .$row['paymentcode'].'</div>';    
	}else{
	   
        $sql = 'update ap set outstanding = amount, statuskey = '.$class->oDbCon->paramString($statuskey).' 
                where code in(' .$class->oDbCon->paramString($arrAP,',').') and
				trdate < \'2021-06-01\' '; // AP2 baru gk boleh diutak atik
         
        $class->oDbCon->execute($sql); 
        echo '<div>AP berhasil di update</div>';
		 
	}

    $class->oDbCon->endTrans();
}

?>