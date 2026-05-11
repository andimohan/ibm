<?php     
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/BaseClass.class.php';   
require_once DOC_ROOT. 'connections/_connection.php';     
 
$GLOBALS['oDbCon'] = new Database($rs[0]['dbusername'],$rs[0]['dbpass'],$rs[0]['dbname'],$host);
  
$class = new Baseclass();
?>