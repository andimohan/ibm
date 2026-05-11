<?php
require_once '../../_config.php';  
require_once '_include.php';

require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Customer.class.php';   
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Category.class.php';    
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/CustomerCategory.class.php';     
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
                'requestid'  =>  array('paramName' => 'request_id' ) ,
                'code' =>   array('paramName' => 'code'), 
                'name'  =>  array('paramName' => 'name', 'mandatory' => true),    
                
				// gk tau kenapa dulu dikomen, mungkin kalo kirim dr API, ketimpa passwordnya
				//'password' => array('paramName' => 'password', 'return' => array('isReturn' => false)),
	
                'categoryname'  =>  array('paramName' => 'category_name','updatable' => false, 'return' => array('paramName' => 'categoryname')), 
                'categorykey'  =>  array('paramName' => 'category_id', 'mandatory' => true, 'ref' => array('obj' => new CustomerCategory(), 'field' => 'code'), 'return' => array('paramName' => 'categorycode')), 
                'cityname'  => array('paramName' => 'city_name','updatable' => false, 'return' => array('paramName' => 'cityname')), 
                'citykey'  =>  array('paramName' => 'city_id', 'ref' => array('obj' => new City(), 'field' => 'code'), 'return' => array('paramName' => 'citycode')), 
                'address'  =>  array('paramName' => 'address'), 
                'zipcode'  =>  array('paramName' => 'zip_code'), 
                'phone'  =>  array('paramName' => 'phone'), 
                'mobile'  =>  array('paramName' => 'mobile'), 
                'fax'  =>  array('paramName' => 'fax'), 
                'email'  =>  array('paramName' => 'email','search' => array('field' => $OBJ->tableName.'.email')), 
                'taxid'  =>  array('paramName' => 'tax_id'),  
                'mapaddress'  =>  array('paramName' => 'map_address'),  
                'latlng'  =>  array('paramName' => 'latlng'),  
                'statuskey'  =>  array('paramName' => 'status_key'), 
                'membershiplevel'  =>  array('paramName' => 'membership_level_key', 'updatable' => false),
                'membershiplevelname'  =>  array('paramName' => 'membership_level_name', 'updatable' => false),
                'point'  =>  array('paramName' => 'point', 'updatable' => false),
                'canusepoint'  =>  array('paramName' => 'canusepoint', 'updatable' => false, 'return' => array('paramName' => 'membershiplevelname')),
                'expdate'  =>  array('paramName' => 'membership_exp_date', 'updatable' => false, 'return' => array('format' => 'mktime')),
                //'contactperson' =>  array('paramName' => 'contact_person', 'dataset' => $OBJ->arrContactPerson, 'detail' =>  $contactPerson)
            ),$API_FIELDS);
         
require_once '_process.php';
     
?>