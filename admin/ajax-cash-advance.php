<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php';  

includeClass('CashAdvance.class.php');
$cashAdvance = createObjAndAddToCol( new CashAdvance()); 

$obj = $cashAdvance;   
$fieldValue = $obj->tableName.'.code'; 
 
include 'ajax-general.php';
if (isset($_GET) && !empty($_GET['action'])) {
	switch ( $_GET['action']){ 

		case 'searchDataAdvance' :   

			 $order = 'order by '.$obj->tableName.'.code asc';

			$returnField = array('key' => $obj->tableName.'.pkey','value' => $fieldValue) ;
			$searchFieldValue = (isset($_GET['searchField']) && !empty($_GET['searchField'])) ? explode(',',$_GET['searchField']) : $fieldValue;
			$searchOptions = array('field' => $searchFieldValue,  'key' => $_GET['term']) ;

			 $arrCriteria = array(); 
			 array_push ($arrCriteria, $obj->tableName.'.statuskey in (2)');

			$criteria = implode(' and ', $arrCriteria);  

			$searchOptions['criteria'] = ' and '.$criteria; 

			$rsData = $obj->searchDataForAutoComplete($returnField,$searchOptions,$order); 

			echo json_encode($rsData);   
			break; 
	}
} 
   
die;
  
?>
