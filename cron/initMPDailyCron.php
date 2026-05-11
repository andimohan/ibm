<?php
 
set_include_path('/home/wintera/public_html/'); 
require_once '_mp-client.php';

// ini nanti harus dicari cuma unique domain saja kaalao sudah multiple account
foreach(MP_TP_CLIENT as $domain)
    runCronJob($domain);
    
function runCronJob($domain){ // sementara
         
    // harus reset ulang
    $arrCronFile = array('mpUpdateAR');
  
    $basepath = 'https://'.$domain.'/cron/'; 
  
    foreach( $arrCronFile as $cronfile){
        $url = $basepath.$cronfile;

        $header = array(
            'Content-Type: application/json', 
        );

        $payload = array('mnv-cron' => 1); 
        $payload = urldecode(http_build_query($payload)); 

        $connection = curl_init(); 
        curl_setopt($connection, CURLOPT_URL, $url); 
        curl_setopt($connection, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($connection, CURLOPT_SSL_VERIFYHOST, 0);  
        curl_setopt($connection, CURLOPT_POST, 1);  
        curl_setopt($connection, CURLOPT_POSTFIELDS, $payload); 
        curl_setopt($connection, CURLOPT_RETURNTRANSFER, 1);

        $response = curl_exec($connection); 
        curl_close($connection); 
    }
  
}

echo 'daily marketplace cron done';
?>