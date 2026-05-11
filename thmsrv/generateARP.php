<?php   
 
include_once '../_config.php';  
include_once '../_include-v2.php';
 
if( ! in_array(DOMAIN_NAME, array('thmsrv.local', 'mandy.wintera.co.id') ) ) die;

$sql = 'select pkey from ar_payment_header where statuskey in (2,3) and year(trdate) = 2022 order by trdate asc, code asc '; // and month(trdate) = 1
$rs = $class->oDbCon->doQuery($sql);

$arrKey = array_column($rs,'pkey');
$result = array_chunk($arrKey, 100);


$ctr = 1; 
        
foreach($result as $chunkRow){ 
    $link =  'https://eai.local/admin/print/arPayment/'.implode(',', $chunkRow);  
    echo $ctr++ . '. <a href="'.$link.'" target="_blank">'.$link.'</a><br>'; 
}
 
?>