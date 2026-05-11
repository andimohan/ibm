<?php
require_once '../../_config.php';  
require_once '_include.php';

require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Supplier.class.php';   
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Category.class.php';     
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/SupplierCategory.class.php';     
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/City.class.php';   
 
function getNewObj(){ return  new Supplier(); }

$OBJ = new Supplier();
 
//$contactPerson = array( 
//    'pkey' => array('paramName' => 'pkey'),
//    'name' => array('paramName' => 'name'),
//    'position' =>  array('paramName' => 'position_name'),
//);

$API_FIELDS = array_merge(array(
               'code' =>   array('paramName' => 'code'), 
               'name'  =>  array('paramName' => 'name', 'mandatory' => true),   
                'categoryname'  =>  array('paramName' => 'category_name','updatable' => false, 'return' => array('paramName' => 'categoryname')), 
                'categorykey'  =>  array('paramName' => 'category_id', 'ref' => array('obj' => new SupplierCategory(), 'field' => 'code'), 'return' => array('paramName' => 'categorycode')), 
 
               'taxid'  =>  array('paramName' => 'tax_id'),       
               'address1'  =>  array('paramName' => 'address'), 
                'cityname'  => array('paramName' => 'city_name','updatable' => false, 'return' => array('paramName' => 'cityname')), 
                'citykey'  =>  array('paramName' => 'city_id', 'ref' => array('obj' => new City(), 'field' => 'code'), 'return' => array('paramName' => 'citycode')), 
             
               'zipcode'  =>  array('paramName' => 'zip_code'), 
               'phone'  =>  array('paramName' => 'phone'), 
               'mobile'  =>  array('paramName' => 'mobile'), 
               'fax'  =>  array('paramName' => 'fax'), 
               'email'  =>  array('paramName' => 'email'), 
                'statuskey'  =>  array('paramName' => 'status_key'),  
               //'contactperson' =>  array('paramName' => 'contact_person', 'dataset' => $OBJ->arrContactPerson, 'detail' =>  $contactPerson)
            ),$API_FIELDS);
       
require_once '_process.php';
     
?>