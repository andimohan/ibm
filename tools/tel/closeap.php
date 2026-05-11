<?php

include_once '../../_config.php'; 
include_once '../../_include-v2.php';  

if(!$security->isAdminLogin('SecurityPrivileges',10,true));
    
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
    <form method="post" action="closeap.php">
     	<?php echo $class->inputHidden('action', array('value' => 'update')); ?>
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
    
    $class->oDbCon->startTrans();
    
    $arrAP = array(); 
    $apCode = $_POST['apCode'];
    $arrAP = preg_split('/[\ \n\,]+/', $apCode);
    
     foreach($arrAP as $key => $row) 
            $arrAP[$key] = trim($row); 
    
    // group per status dulu
    $sql = 'select * from ap where code in(' . $class->oDbCon->paramString($arrAP,',').')'; 
    $rsAP = $class->oDbCon->doQuery($sql);
  
    $arrAPByStatus = $class->reindexDetailCollections($rsAP,'statuskey');
     
    // cek dulu status AP
    // kalo open, cek ad AP Paymentnya gk, kalo ad, throw error 
    // kalo partial, langsung proses saja
    
    
	$sql = '
			select 
				*
			from  
				ap_payment_detail,ap_payment_header,ap
			where
				ap_payment_detail.refkey = ap_payment_header.pkey and
				ap_payment_detail.apkey = ap.pkey and
				ap.code in(' . $class->oDbCon->paramString($arrAP,',').') and
				ap_payment_header.statuskey in (1) and
                ap.statuskey = 1
		   ';
    
	$rs = $class->oDbCon->doQuery($sql);
    
    if(!empty($rs)){ 
        $arrAPCode = array_column($rs,'code');
        die( 'AP sudah ada payment dalam status menunggu. ' . implode(', ',$arrAPCode));
    }
    
    //kalo statusnya open ubah ke => cancel
    //kalo statusnya partial ubah ke => selesai
    foreach($arrAPByStatus as $statuskey => $apCol){
        $newStatus = ($statuskey == 1) ? 4 : 3;
        
        $apCode = array_column($apCol,'code');
         
        $sql = 'update ap set outstanding = 0, tagkey = 5, statuskey = '.$newStatus.' 
                where code in(' .$class->oDbCon->paramString($apCode,',').')';
        echo $sql.'<br>';
        $class->oDbCon->execute($sql); 
         
    }
    
   
    echo '<div>AP berhasil di update</div>';


    $class->oDbCon->endTrans();
}

?>