<?php
$url = 'https://www.wintera.co.id/cron/defaultSystemCronJob.php';

$connection = curl_init(); 
curl_setopt($connection, CURLOPT_URL, $url); 
curl_setopt($connection, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($connection, CURLOPT_SSL_VERIFYHOST, 0);   
curl_setopt($connection, CURLOPT_RETURNTRANSFER, 1);

$response = curl_exec($connection); 
curl_close($connection); 
?>