<?php
require_once '../../_config.php';  
require_once '_include.php';

require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Customer.class.php';   
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Category.class.php';    
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/CustomerCategory.class.php';    
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/QuestionnaireResponse.class.php';   
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/City.class.php';   
 
$OBJ = new Customer();

// INPUT QUERY
// field yang diterima dari parameter API
// convert ke nama parameter kita di class 
$contactPerson = array( 
    'pkey' => array('paramName' => 'pkey'),
    'name' => array('paramName' => 'name'),
    'position' =>  array('paramName' => 'position_name'),
);

$API_FIELDS = array_merge($API_FIELDS,array(
               'code' =>   array('paramName' => 'code'), 
               'name'  =>  array('paramName' => 'name', 'mandatory' => true),      
               'categorykey'  =>  array('paramName' => 'category_name', 'mandatory' => true, 'ref' => array('obj' => new CustomerCategory())),  
               'address'  =>  array('paramName' => 'address'), 
               'citykey'  =>  array('paramName' => 'city_name', 'ref' => array('obj' => new City())), 
               'zipcode'  =>  array('paramName' => 'zip_code'), 
               'phone'  =>  array('paramName' => 'phone'), 
               'mobile'  =>  array('paramName' => 'mobile'), 
               'fax'  =>  array('paramName' => 'fax'), 
               'email'  =>  array('paramName' => 'email'), 
               'taxid'  =>  array('paramName' => 'tax_id'), 
               'statuskey'  =>  array('paramName' => 'status', 'ref' => array('tableName' => $OBJ->tableStatus, 'field' => 'status') ), 
               //'contactperson' =>  array('paramName' => 'contact_person', 'dataset' => $OBJ->arrContactPerson, 'detail' =>  $contactPerson)
            ));
       
require_once '_process.php';
     
?>