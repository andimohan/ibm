<?php
require_once '../../_config.php';  
require_once '_include.php';

require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Customer.class.php';   
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Category.class.php';    
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/CustomerCategory.class.php';    
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/QuestionnaireResponse.class.php';   
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/City.class.php';   
 
function getNewObj(){ return  new Customer(); }

$OBJ = getNewObj();


// INPUT QUERY
// field yang diterima dari parameter API
// convert ke nama parameter kita di class 
$contactPerson = array( 
    'pkey' => array('paramName' => 'pkey', 'return' => array('isReturn' => false)),  
    'name' => array('paramName' => 'name'),
    'position' =>  array('paramName' => 'position_name'),
);

$API_FIELDS = array_merge(array( 
               'name'  =>  array('paramName' => 'name', 'mandatory' => true),      
               'categorykey'  =>  array('paramName' => 'category_name', 'mandatory' => true, 'ref' => array('obj' => new CustomerCategory()), 'return' => array('paramName' => 'categoryname')), 
               'citykey'  =>  array('paramName' => 'city_name', 'ref' => array('obj' => new City()), 'return' => array('paramName' => 'cityname')), 
               'address'  =>  array('paramName' => 'address'), 
               'zipcode'  =>  array('paramName' => 'zip_code'), 
               'phone'  =>  array('paramName' => 'phone'), 
               'mobile'  =>  array('paramName' => 'mobile'), 
               'fax'  =>  array('paramName' => 'fax'), 
               'email'  =>  array('paramName' => 'email'), 
               'taxid'  =>  array('paramName' => 'tax_id'), 
               'statuskey'  =>  array('paramName' => 'status', 'ref' => array('tableName' => $OBJ->tableStatus, 'field' => 'status') , 'return' => array('isReturn' => false)), 
               //'contactperson' =>  array('paramName' => 'contact_person', 'dataset' => $OBJ->arrContactPerson, 'detail' =>  $contactPerson)
            ),$API_FIELDS);
         
require_once '_process.php';
     
?>