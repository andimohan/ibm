<?php
require_once '../../_config.php';  
require_once '_include.php';

require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Car.class.php';   
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Category.class.php';    
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/CarCategory.class.php';     
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Brand.class.php';      
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Warehouse.class.php';                 
 
function getNewObj()
{
   return new Car();
}

$OBJ =  getNewObj();

$API_FIELDS = array_merge($API_FIELDS,array(
               'code' =>   array('paramName' => 'code'),   
               'brandkey'  =>  array('paramName' => 'brand_name', 'ref' => array('obj' => new Brand())),   
               'categorykey'  =>  array('paramName' => 'category_name', 'mandatory' => true, 'ref' => array('obj' => new CarCategory())),  
               'bpkbname'  =>  array('paramName' => 'ownership_name'),      
               'bpkbnumber'  =>  array('paramName' => 'ownership_number'),      
               'year'  =>  array('paramName' => 'year'),      
               'policenumber'  =>  array('paramName' => 'registration_number','mandatory' => true),   
               'licensenumber'  =>  array('paramName' => 'license_number'),      
               'licenseexpirydate'  =>  array('paramName' => 'license_expired_date'),   
               'taxexpirydate'  =>  array('paramName' => 'tax_expired_date'),         
               'kir'  =>  array('paramName' => 'kir_number'),      
               'kirexpirydate'  =>  array('paramName' => 'kir_expired_date'),   
               'machinenumber'  =>  array('paramName' => 'machine_number'),     
               'chassisnumber'  =>  array('paramName' => 'chassis_number'),        
               'statuskey'  =>  array('paramName' => 'status', 'ref' => array('tableName' => $OBJ->tableStatus, 'field' => 'status') )
            ));
       
require_once '_process.php'; 
     
?>