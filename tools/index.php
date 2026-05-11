<?php
die;

include_once '../_config.php'; 
include_once '../_include.php';  
 
if(!$security->isAdminLogin('SecurityPrivileges',10,true)); 

?>

<ul>
<li><a href="import/">Import</a></li>
<li><a href="updatesecuritymodule.php">Update Security Object</a></li>
</ul>