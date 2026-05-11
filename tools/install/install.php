<?php

date_default_timezone_set('Asia/Jakarta');

ini_set("zlib.output_compression", "On");
ini_set('display_errors', 0);
ini_set('log_errors', 1);

define('CLASS_VERSION', 'class-2.12'); 


$DOC_ROOT = $_SERVER ['DOCUMENT_ROOT'] ;
if(substr($DOC_ROOT,-1) <> "/") $DOC_ROOT .= '/'; 
  
define('DOC_ROOT',$DOC_ROOT);


$patterns = array('www.',':');
$replacements = array('','-');
$DOMAIN_NAME = str_replace($patterns, $replacements, $_SERVER['HTTP_HOST']); 

// FOR DEVELOPMENT 
$IS_DEVELOPMENT = false;
if(file_exists(DOC_ROOT.'_development.php')) include DOC_ROOT.'_development.php';
     
define('DOMAIN_NAME',$DOMAIN_NAME); 

$FILE_NAME = basename ($_SERVER['PHP_SELF'] ,".php");

require_once DOC_ROOT. 'connections/_connection.php'; 
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/BaseClass.class.php';
 
$GLOBALS['ObjCol'] = array();
$GLOBALS['oDbCon'] = new Database($rs[0]['dbusername'],$rs[0]['dbpass'],$rs[0]['dbname'],$host);

$class = new Baseclass();
$GLOBALS['class'] = $class;


$arrInitTable = array('_code','_fuel_type','_nextkey','_plan_type','_setting', '_setting_category', '_setting_detail','_setting_form_list_detail',
					  '_setting_form_list_header','_sex','_user_code','_user_setting','_widget','_widget_properties','_widget_setting','security_object',
				      'security_access', 'user_security_object','security_object_category','lang', 'login_log', 'login_log_status', 'transaction_log',
					  'transaction_log_action','transaction_log_detail','transaction_status','api_log', 'tablekey','tag', 'tag_detail','read_log',
					  'master_status','warehouse','employee','employee_category','employee_status','employee_detail_company',
					  'employee_detail_customer','employee_detail_sales','employee_detail_warehouse','employee_downpayment','employee_image','employee_detail_commission',
					  'company', 'city','city_category','custom_code','custom_code_counter','custom_code_reset_type','contact_person','religion','marital_status','template_role','currency',
					  'lang_detail','location','job_position','widget_properties_values','partner_settings','country','user_themes_settings','report_settings','tax','business_unit','brand',
					  'employee_detail_payment_method');

// tarik dr db init

// select semua module 
$psMod = newConnection('minerva.program-stok.com');
 
$class->oDbCon->startTrans();
 
foreach($arrInitTable as $table){ 
	
	// create table
	$sql = ' SHOW CREATE TABLE ' . $table;
	$rs = $psMod->doQuery($sql);
	  
	$sql = $rs[0]['Create Table']; 
	$class->oDbCon->execute($sql);
	
	// insert table, biar lebih pasti ambil dr nama kolom saja
	$sql = 'show columns from ' . $table;
	$rs = $psMod->doQuery($sql);
	$rsField = array_column($rs,'Field');
	
	$sql = 'select * from ' . $table;
	$rs = $psMod->doQuery($sql);
	
	foreach($rs as  $row) { 
		
		$arrValue = array();
	  	foreach($rsField as $fieldName)  
			array_push( $arrValue, '\''.$row[$fieldName].'\'');
		
		$sql = 'insert into ' . $table.' values ('.implode(',',$arrValue).')';
		$class->oDbCon->execute($sql);
														   
	}
}


$class->oDbCon->endTrans();

echo 'done';
 
?>