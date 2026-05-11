<?php 
require_once '_config.php'; 
require_once '_include-fe-v2.php';
require_once '_global.php';
if(!$security->isMemberLogin(false))  {
	header('location:'.KICKED_REDIRECT_URL); 
	die;
}

$arrTwigVar['inputvoucher'] = $class->inputText('voucher', array('etc' => 'style="text-align:center; font-size: 1.5em; padding: 1.5em"'));
$arrTwigVar['btnSubmit'] = $class->inputSubmit('btnSave',$class->lang['redeemVoucher']);   


echo $twig->render('redeem-voucher.html', $arrTwigVar);  
 
?>
