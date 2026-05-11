<?php
require_once '../../_config.php';  
require_once '_include.php';

require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Service.class.php';   
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Category.class.php';    
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/ServiceCategory.class.php';  
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/ChartOfAccount.class.php';       

function getNewObj(){
   return new  Service(SERVICE);
}

$OBJ = getNewObj();
$serviceCategory = new ServiceCategory();
$chartOfAccount = new ChartOfAccount();

$imageUrl = array( 
    'pkey' => array('paramName' => 'key'),   
    'url' => array('paramName' => 'url'),
);

$arrCondition = array('baru' => '8001', 'pernah digunakan' => '8002'); 

// name from each keya
$API_FIELDS = array_merge($API_FIELDS,array(
    'code' =>   array('paramName' => 'code'), 
    'barcode' =>   array('paramName' => 'barcode'), 
    'name'  =>  array('paramName' => 'name', 'mandatory' => true,'search' => array('field' => $OBJ->tableName.'.name')),  
    'categorykey' => array('paramName' => 'category_id', 'mandatory' => true,'search' => array('field' => $serviceCategory->tableName.'.code'), 'ref' => array('obj' => $serviceCategory, 'field' => 'code' ), 'return' => array('paramName' => 'categorycode')), 
    'categoryname' => array('paramName' => 'category_name','updatable' => false), 
    'sellingprice' => array('paramName' => 'selling_price'),
    'revenuecoakey' => array('paramName' => 'revenue_coa_id','ref' => array('obj' => $chartOfAccount, 'field' => 'code')),
    'prepaidexpensecoakey' => array('paramName' => 'prepaid_expense_coa_id','ref' => array('obj' => $chartOfAccount, 'field' => 'code')),
    'costcoakey' => array('paramName' => 'cost_coa_id','ref' => array('obj' => $chartOfAccount, 'field' => 'code')),
    'shortdescription'  =>  array('paramName' => 'short_description'),
 ));

require_once '_process.php';

?>