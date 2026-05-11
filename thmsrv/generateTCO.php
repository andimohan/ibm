<?php   
 
include_once '../_config.php';  
include_once '../_include-v2.php';
 
if( ! in_array(DOMAIN_NAME, array('thmsrv.local', 'mandy.wintera.co.id') ) ) die;

$sql = 'select distinct(employeekey) as employeekey from trucking_cost_cash_out_header  where year(trdate) = 2022 and statuskey in (2,3) '; // and month(trdate) = 1
$rs = $class->oDbCon->doQuery($sql);


$ctr = 1;
foreach($rs as $row){
    $sql =  'select pkey from trucking_cost_cash_out_header where employeekey = '.$row['employeekey'].' and year(trdate) = 2022  and statuskey in (2,3)  '; //and month(trdate) = 1
    $rsTCO =  $class->oDbCon->doQuery($sql); 
    
    
    $arrKey = array_column($rsTCO,'pkey');
    
    $result = array_chunk($arrKey, 100);
        
    foreach($result as $chunkRow){ 
        $link =  'https://eai.local/admin/print/truckingCostCashOut/'.implode(',', $chunkRow); 
         
        echo $ctr++ . '. <a href="'.$link.'" target="_blank">'.$link.'</a><br>';
//        exec($link);
        
//        file_get_contents($link);
        
//        usleep(500); 
//        $ch = curl_init();
//
//        // set URL and other appropriate options
//        curl_setopt($ch, CURLOPT_URL, $link);
//        curl_setopt($ch, CURLOPT_HEADER, 0);
//
//        // grab URL and pass it to the browser
//        curl_exec($ch);
//
//        // close cURL resource, and free up system resources
//        curl_close($ch);
//        echo $link;
//        die;
        
 
    }
   
}


//$sql = 'select pkey from trucking_cost_cash_out_header  where year(trdate) = 2022 and month(trdate) = 1 order by employeekey, code ';
//$rsTCO =  $class->oDbCon->doQuery($sql); 
//
//echo implode(',', array_column($rsTCO,'pkey')); 
//echo '<br>';

?>