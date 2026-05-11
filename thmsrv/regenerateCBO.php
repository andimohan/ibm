<?php   
 
include_once '../_config.php';  
include_once '../_include-v2.php';
 
if( ! in_array(DOMAIN_NAME, array('thmsrv.local', 'mandy.wintera.co.id') ) ) die;

$sql = "select pkey from cash_out_header where code in ('CBO-083/01/2022','CBO-123/01/2022','CBO-183/01/2022','CBO-209/01/2022','CBO-027/02/2022','CBO-041/02/2022','CBO-073/02/2022','CBO-121/02/2022','CBO-006/03/2022','CBO-021/03/2022','CBO-037/03/2022','CBO-064/03/2022','CBO-087/03/2022','CBO-097/03/2022','CBO-192/03/2022','CBO-193/03/2022','CBO-219/03/2022','CBO-018/04/2022','CBO-038/04/2022','CBO-068/04/2022','CBO-086/04/2022','CBO-138/04/2022','CBO-186/04/2022','CBO-004/05/2022','CBO-063/05/2022','CBO-065/05/2022','CBO-086/06/2022','CBO-093/06/2022','CBO-152/06/2022','CBO-237/07/2022','CBO-073/08/2022','CBO-117/08/2022','CBO-156/08/2022','CBO-157/08/2022','CBO-174/08/2022','CBO-212/08/2022','CBO-227/08/2022','CBO-277/08/2022','CBO-285/08/2022','CBO-289/08/2022','CBO-327/08/2022','CBO-113/10/2022','CBO-046/11/2022','CBO-074/11/2022','CBO-101/11/2022','CBO-112/11/2022','CBO-161/11/2022','CBO-205/11/2022','CBO-206/11/2022','CBO-225/11/2022','CBO-099/12/2022','CBO-122/12/2022','CBO-157/12/2022','CBO-190/12/2022','CBO-191/12/2022','CBO-221/12/2022','CBO-285/12/2022','CBO-349/12/2022') order by trdate asc";
$rs = $class->oDbCon->doQuery($sql);

$arrKey = array_column($rs,'pkey');
$result = array_chunk($arrKey, 100);


$ctr = 1; 
        
foreach($result as $chunkRow){ 
    $link =  'https://eai.local/admin/print/cashOut/'.implode(',', $chunkRow);  
    echo $ctr++ . '. <a href="'.$link.'" target="_blank">'.$link.'</a><br>'; 
}
 
 
?>