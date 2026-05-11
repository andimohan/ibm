<?php
require_once '_config.php'; 
require_once '_include-fe-v2.php';
require_once '_global.php';

$domainName = $class->getDomain($_SERVER['HTTP_REFERER']); // buat tau mau ambil data dari mana
$class->setLog($domainName,true,'../../ai-log/domain.txt');


$_POST['domain'] = $domainName;

if (isset($_POST['data']) && !empty($_POST['data']))
if(!empty($_POST['data']))
    $class->setLog($_POST['data'],true,'../../ai-log/domain.txt');

$_POST['data'] = htmlspecialchars($_POST['data'], ENT_QUOTES);

$arrTwigVar ['inputHidFileData'] =  $class->inputHidden('fileData'); 
$arrTwigVar ['inputHidDomain'] =  $class->inputHidden('domain'); 
$arrTwigVar ['inputHidData'] =  $class->inputHidden('data'); 

echo $twig->render('ai-analyze.html', $arrTwigVar);
?>