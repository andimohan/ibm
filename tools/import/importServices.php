<?php
require_once '../../_config.php'; 
require_once '_include.php';    
require_once 'function-v2.php';    
  
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Service.class.php'; 
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Category.class.php';     
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/ServiceCategory.class.php';     
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/ChartOfAccount.class.php';     

$OBJ = new Service(SERVICE);
$MODULE_NAME = 'services';
$TITLE = $OBJ->lang['service'];
$AJAX_FILE = 'ajax-api-service';

$RESET_TABLE = array( 
            'service', 
            'service_category' 
); 
  
$serviceCategory = new ServiceCategory();
$chartOfAccount = new ChartOfAccount();

$DATA_STRUCTURE = array();
array_push($DATA_STRUCTURE, array('field' => '')); // index 0 gk dipake, karena excel indexnya dari 1
array_push($DATA_STRUCTURE, array('field' => 'code'));
array_push($DATA_STRUCTURE, array('field' => 'name'));
array_push($DATA_STRUCTURE, array('field' => 'alias'));
array_push($DATA_STRUCTURE, array('field' => 'category_id', 'replace' => array('obj' => $serviceCategory)));
array_push($DATA_STRUCTURE, array('field' => 'selling_price'));
array_push($DATA_STRUCTURE, array('field' => 'revenue_coa_id', 'replace' => array('obj' => $chartOfAccount)));
array_push($DATA_STRUCTURE, array('field' => 'prepaid_expense_coa_id', 'replace' => array('obj' => $chartOfAccount)));
array_push($DATA_STRUCTURE, array('field' => 'cost_coa_id', 'replace' => array('obj' => $chartOfAccount)));
array_push($DATA_STRUCTURE, array('field' => 'short_description'));
array_push($DATA_STRUCTURE, array('field' => 'status'));
require_once '_import.php';
?>
