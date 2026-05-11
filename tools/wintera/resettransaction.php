<?php
die("die, comment open for reset transaction");

include_once '../../_config.php'; 
include_once '../../_include-v2.php';

// utk jaga2 aj biar gk keakses sembarangan
if(DOMAIN_NAME!= 'triharmoni.wintera.co.id') die ('wrong domain');

$sql = 'show tables';
$rs = $class->oDbCon->doQuery($sql);

// 'item_sn','item_sn_movement','item_sn_replace_log', gk masuk, karena SN diambil dari transaksi
$arrMaster = array('_code','_fuel_type','_nextkey','_plan_type','_setting','_setting_category','_setting_detail','_setting_form_list_detail','_setting_form_list_header','_sex','_user_code','_user_setting','_widget','_widget_properties','_widget_setting','ap_commission_type','ap_type','ar_employee_type','ar_status','ar_type','asset','asset_category','asset_group','asset_type','background','banner','banner_position','brand','brand_lang','brand_marketplace_detail','brand_storefront_detail','business_category','business_unit','car','car_category','car_checklist','car_revenue','car_series','career_category','career_field','cargo_type','chart_of_account','chassis','chassis_category','city','city_category','coa_link','coa_type','company','consignee','contact_category','contact_person','contact_us','container','container_type','contract_duration','country','course_category','currency','custom_code','custom_code_counter','custom_code_reset_type','customer','customer_account_detail','customer_business_category_detail','customer_category','customer_downpayment_settlement_cost','customer_downpayment_settlement_detail','customer_downpayment_settlement_header','customer_downpayment_settlement_payment','customer_features','customer_features_detail','customer_file','customer_item_alias_detail','customer_membership','customer_membership_features_detail','customer_news','customer_social_media','customer_status','depot','depot_detail','depot_shipping_cost','depot_shipping_item_price','division','download','download_category','download_file','emkl_air_sea','emkl_bill_type','emkl_fcl_lcl','emkl_freight_term','emkl_import_export','emkl_job_type','emkl_volume_unit','employee','employee_category','employee_detail_commission','employee_detail_company','employee_detail_customer','employee_detail_sales','employee_detail_warehouse','employee_downpayment','employee_image','employee_status','faq','filter_category','filter_detail','filter_header','fixed_unit_conversion','interest_maturity','invoice_period','issue_category','item','item_category','item_category_group','item_category_lang','item_category_marketplace_attributes','item_category_marketplace_detail','item_category_storefront','item_category_storefront_detail','item_checklist','item_checklist_group_detail','item_checklist_group_header','item_coa_link','item_condition','item_condition_detail','item_content_of_package_detail','item_conversion','item_description','item_detail_time','item_detail_video','item_file','item_image','item_image_variant','item_in_type','item_lang','item_marketplace_check','item_marketplace_link','item_marketplace_logistics','item_marketplace_sync_detail','item_marketplace_variant','item_out_category','item_out_type','item_package_detail','item_promo_detail','item_promo_header','item_rental_movement','item_specification','item_specification_detail','item_type','item_unit','item_unit_conversion','item_upload_receipt_detail','item_upload_receipt_header','item_upload_receipt_status','item_vendor_part_number','job_details','job_position','lang','lang_detail','location','login_log_status','marital_status','marketplace','marketplace_action_type','master_status','membership','membership_level','multiple_address_detail','multiple_cost_detail','news_category','oil_type','payment_method','port','portfolio_status','preorder_item_status','prepaid_expense','purchase_category','recipient_type','religion','rental_time_unit','repeat_periode','report_settings','routine_cost_charge_type','routine_cost_detail','routine_cost_header','security_access','security_object','security_object_category','security_role','service_area_detail','service_asset_group_detail','service_category','service_item_detail','shipment','shipment_detail','shipment_marketplace_detail','social_media','stages_process','subscribe','supplier','supplier_category','supplier_bank_detail','supplier_status','tablekey','tag','tax','tax_type','template_customer_detail','template_customer_header','template_emkl_purchase_item_detail','template_emkl_purchase_item_header','template_role','template_supplier_detail','template_supplier_header','term_of_payment','terminal','time_unit','time_unit_master','transaction_log_action','transaction_status','trucking_cost_cash_out_status','trucking_job','trucking_selling_rate_detail','trucking_selling_rate_header','trucking_service_order_category','trucking_service_order_category_detail','trucking_service_order_category_progress','trucking_service_order_detail_status','trucking_service_order_status','trucking_service_work_order_status','urgency','user_coa_access','user_security_object','user_themes_settings','vehicle_partnership_type','vessel','voucher','voucher_category','voucher_type','warehouse','waste_category','widget_properties_values','work_progress_detail','work_progress_step','youtube','emkl_quotation_order_status','color','plating','ring_size','texture','material','model','character');

$class->oDbCon->startTrans();

foreach($rs as $row){
	
	if(in_array($row[0], $arrMaster)) continue;
	
	$sql = 'truncate '.$row[0];
	$class->oDbCon->execute($sql);
	
}

$arrSQL = array();
array_push($arrSQL,'insert into chart_of_account_active_period values (1,\'2024-01-01\',0)');
array_push($arrSQL,'update chart_of_account set amount=0');
array_push($arrSQL,'update _user_code set counter = 1 where codekey in ( select pkey from _code where code not in ('.$class->oDbCon->paramString($arrMaster,',').') )');

foreach($arrSQL as $sql)
	$class->oDbCon->execute($sql);
 

$class->oDbCon->endTrans();
	
echo 'done'
 
?>
