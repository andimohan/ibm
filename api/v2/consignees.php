<?php
require_once '../../_config.php';  
require_once '_include.php';

require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Consignee.class.php';   
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Location.class.php';   
 
$OBJ = new Consignee();

$API_FIELDS = array_merge($API_FIELDS,array(
               'code' =>   array('paramName' => 'code'), 
               'name'  =>  array('paramName' => 'name', 'mandatory' => true),       
               'address'  =>  array('paramName' => 'address'), 
               'locationkey'  =>  array('paramName' => 'location_name', 'ref' => array('obj' => new Location())), 
               'statuskey'  =>  array('paramName' => 'status', 'ref' => array('tableName' => $OBJ->tableStatus, 'field' => 'status') ), 
               //'contactperson' =>  array('paramName' => 'contact_person', 'dataset' => $OBJ->arrContactPerson, 'detail' =>  $contactPerson)
            ));
       
require_once '_process.php';
     
?>