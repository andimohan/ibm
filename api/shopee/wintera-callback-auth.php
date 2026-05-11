<?php

// update code 
require_once '../../_mp-client.php'; 

//$class->setLog(DOMAIN_NAME. ' start => '.$_GET['shop_id'].'.'.$_GET['code'],true,'shopee.txt');

$domainName = MP_SH_CLIENT[$_GET['shop_id']];
if(empty($domainName)) die("domain not found");

$url = 'https://'.$domainName.'/marketplace-auth?shop_id='.$_GET['shop_id'].'&code='.$_GET['code'];

header('location: '.$url);
die;
?>