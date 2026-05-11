<?php   
 
include_once '../_config.php';  
include_once '../_include-v2.php';
 
if( ! in_array(DOMAIN_NAME, array('thmsrv.local', 'mandy.wintera.co.id') ) ) die;

$sql = 'select distinct(ap_payment_header.pkey) from ap_payment_header, ap_payment_detail, ap
where ap_payment_header.pkey = ap_payment_detail.refkey and  year(ap_payment_header.trdate) = 2022 and  ap_payment_header.statuskey in (2,3) and
ap_payment_detail.apkey = ap.pkey and ap.refcode2 like \'SO%\''; 
$rs = $class->oDbCon->doQuery($sql);

$arrKey = array_column($rs,'pkey');
$result = array_chunk($arrKey, 100);


$ctr = 1; 
        
foreach($result as $chunkRow){ 
    $link =  'https://eai.local/admin/print/apPayment/'.implode(',', $chunkRow);  
    echo $ctr++ . '. <a href="'.$link.'" target="_blank">'.$link.'</a><br>'; 
}
 
?>