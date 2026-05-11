<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php';  
 
includeClass('TicketSupport.class.php');   
$ticketSupport = createObjAndAddToCol( new TicketSupport()); 

$obj = $ticketSupport;    
 
$fieldValue = $obj->tableName.'.code';

include 'ajax-general.php';
 
die;
  
?>