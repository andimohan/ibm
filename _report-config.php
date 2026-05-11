<?php 

include 'assets/vendor/autoload.php';  
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Excel.class.php';   
 
if(!$security->isMemberLogin(false)) 
	header('location:/logout'); 


$rsCustomer = $customer->getDataRowById(USERKEY);

$arrTwigVar['btnUpdateFilter'] = $class->inputSubmit('btnUpdateFilter',$class->lang['submit'], array('overwritePost' => false, 'etc' => 'style="display:none"'));   
$arrTwigVar['btnShowFilter'] = $class->inputButton('btnShowFilter',$class->lang['searchFilter'], array('overwritePost' => false));   
$arrTwigVar['btnExportExcel'] = $class->inputButton('btnExportToExcel',$class->lang['exportExcel'], array('overwritePost' => false, 'class' => 'btn btn-primary export-excel'));
$arrTwigVar['hidExportExcel'] = $class->inputHidden('hidExportExcel', array('overwritePost' => false)); 
$arrTwigVar['hidAction'] = $class->inputHidden('hidAction',array('value' => 'add')); 
?>