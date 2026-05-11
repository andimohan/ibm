<?php
require_once '../../_config.php';  
require_once '_include.php';

require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Category.class.php';    
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/CityCategory.class.php';  
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/City.class.php';   
require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/Country.class.php';
 
function getNewObj(){ return  new City(); }

$OBJ = getNewObj();


// INPUT QUERY
// field yang diterima dari parameter API
// convert ke nama parameter kita di class 
 

$API_FIELDS = array_merge(array(
					'requestid'  =>  array('paramName' => 'request_id' ) ,
					'code' =>   array('paramName' => 'code'), 
					'name'  =>  array('paramName' => 'name', 'mandatory' => true),   
					'categoryname'  =>  array('paramName' => 'category_name','updatable' => false, 'return' => array('paramName' => 'categoryname')), 
					'categorykey'  =>  array('paramName' => 'category_id', 'ref' => array('obj' => new CityCategory(), 'field' => 'code'), 'return' => array('paramName' => 'categorycode')), 
					'countryname' => array('paramName' => 'country_name', 'updatable' => false, 'return' => array('paramName' => 'countryname')),
					'countrykey' => array('paramName' => 'country_id', 'ref' => array('obj' => new Country(), 'field' => 'code'), 'return' => array('paramName' => 'countrycode')), 
					'isdefaultshipment'  =>  array('paramName' => 'is_default_shipment'),  
                	'statuskey'  =>  array('paramName' => 'status_key'),  
            ),$API_FIELDS);
         
require_once '_process.php';
     
?>
