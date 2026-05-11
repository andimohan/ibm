<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass('ChartOfAccount.class.php');
$obj = createObjAndAddToCol(new ChartOfAccount());

// tarik semua bulan yg bisa diedit
// tentukan dulu periode awal, patokan dari terakhir period masih open

$startPeriod = $obj->getRunningPeriod()[0]['runningmonth']; 
$currentPeriod = date('Y-m-01');

// jika startPeriod > currentPeriod
$dateDiff = $obj->dateDiff($startPeriod,$currentPeriod);
if ($dateDiff < 0)    $currentPeriod = $startPeriod; 
    
 // plus 1 bulan dr $currentPeriod
$currentPeriod =  date("Y-m-01", strtotime("+1 month", $currentPeriod));

$monthPeriod = $obj->getMonthPeriod($startPeriod,$currentPeriod);
$arrKeyPeriod = array(); 
foreach ($monthPeriod as $dt) { 
    $keyIndex = $dt->format('nY');  
    $arrKeyPeriod[$keyIndex] = array('label' => $dt->format('M Y')); 
}
 
$arrKeyPeriod = array_reverse($arrKeyPeriod);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title>  
<script type="text/javascript"> 
  
</script> 
</head>  

<body> 
<div class="popup-form open-gl-period" style="">
    <div class="title-panel"><?php echo $obj->getLang('lockPeriod'); ?></div>
    <div class="form-panel">
    <div class="period-list">
        <?php 
            $currYear = '';
        
            foreach($arrKeyPeriod as $keyIndex=>$month){
                $year = explode(' ',$month['label'])[1];
                 
                if($currYear <> $year){ 
                    $currYear = $year;
                    echo '<div style="clear:both"></div><div class="section-title">'.$currYear.'</div>';
                }
                
                echo '<div class="item">'. $class->inputCheckBox('chk'.$keyIndex) .' '.$month['label'].'</div>';
                
                
            }
        ?>
        <div style="clear:both; height: 2em"></div>
    </div> 
    </div>    
    <div class="action-panel">
        <?php echo $class->inputSubmit('btnSave', $class->lang['save']); ?>
    </div>
</div>
</body>

</html>
