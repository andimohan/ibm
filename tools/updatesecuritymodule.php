<?php

include_once '../_config.php'; 
include_once '../_include-v2.php';
//include_once '../_global.php';   

$HOST = 'localhost';
define('DOMAIN_INIT', 'minerva.program-stok.com');

//if(!$security->isAdminLogin('SecurityPrivileges',10,true)); 
 
$psCon = $security->masterConn(); 
$sql = 'select * from customer_company where statuskey = 2';  
$rsComp = $psCon->doQuery($sql);  
$arrCompany = $class->convertForCombobox($rsComp,'pkey','name');   

if (isset($_POST) && !empty($_POST['action'])){
    updateData($psCon);
    die;
}

$psCon = null;

// select semua module 
$psMod = newConnection(DOMAIN_INIT);

$module = ' select * from security_object order by modulename asc';
$rsModule = $psMod->doQuery($module);
$arrModule = $class->convertForCombobox($rsModule,'pkey','modulename');

// set package
$rsModule = array_map('nestedLowercase', $rsModule); 
$rsModuleTemp = array_column($rsModule,'pkey','modulecode');

$arrModuleSet = getModuleSet(); 
$arrModuleSet = array_map('nestedLowercase', $arrModuleSet); 
$arrModuleSetKeys = array_keys($arrModuleSet);

foreach($arrModuleSet as &$row){ 
    foreach($row as &$moduleIndex){ 
        $moduleIndex = $rsModuleTemp[$moduleIndex];
    }
}
unset($row);
unset($moduleIndex);

$rsStatus = array();
$rsStatus[0]['pkey'] = 1;
$rsStatus[0]['label'] = 'Aktif';
$rsStatus[1]['pkey'] = 2;
$rsStatus[1]['label'] = 'Non Aktif';

$arrStatus = $class->convertForCombobox($rsStatus,'pkey','label'); 
    
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">  
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 
<link rel="stylesheet" type="text/css" href="<?php echo $class->adminCssPath; ?>jquery-font-awesome.min.css">  
<link rel="stylesheet" type="text/css" href="<?php echo $class->adminCssPath; ?>jquery-ui.min.css" />    
<link rel="stylesheet" type="text/css" href="<?php echo $class->adminCssPath; ?>bootstrap.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo $class->adminCssPath; ?>sol.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo $class->adminCssPath.ADMIN_CSS_VERSION; ?>">  
     
<script type="text/javascript" src="<?php echo $class->defaultJsPath; ?>jquery-3.3.1.min.js"></script>  
<script type="text/javascript" src="<?php echo $class->defaultJsPath; ?>sol.js"></script>  

<script>
    jQuery(document).ready(function(){  
        var package = <?php echo json_encode($arrModuleSet); ?>; 
            
        $('.multi-selectbox').searchableOptionList({
               maxHeight: '250px',
               showSelectAll: true,
               showSelectionBelowList: true
        });
        
        $( ".package li" ).click(function() {
            
            var packageList = $(this).attr("relkey");
            for(i=0;i<package[packageList].length;i++)
               $("[name='selModule[]'][value="+package[packageList][i]+"]:not(:checked)").click();
        });

		
    }) 
</script>    
    
<title>Update Modul</title>  
<style>
    .package{list-style: none; padding: 0; margin: 0}
    .package li { cursor: pointer; float:left; border-radius: 0.3em; margin-right: 0.5em; border:1px solid #999; display: inline-block; padding: 0.3em 0.5em; background-color: #dedede}
    .package li:hover {background-color: #999;}
</style>
</head> 
<body style="margin:1em">    
    <form method="post" action="updatesecuritymodule.php">
     <?php echo $class->inputHidden('action', array('value' => 'update')); ?>
     <div>Customer</div>
     <?php echo  $class->inputSelect('selCompany[]', $arrCompany, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox') ); ?>
    <br>
    <div>Module</div>
     <div>
        <?php 
            echo '<ul class="package">';
            foreach($arrModuleSetKeys as $row)
                echo '<li relkey="'.$row.'">'.$row.'</li>';
         
            echo '</ul>';

         ?>
     </div>
    <div style="clear:both; height: 1em"></div>    
     <?php echo  $class->inputSelect('selModule[]', $arrModule, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox') ); ?>
    <br>
    <div>Update Status</div>
     <?php echo  $class->inputSelect('selStatus', $arrStatus); ?>
     <br>    
     <?php echo $class->inputSubmit('btnSubmit','Upload'); ?>
    </form>
    
</body> 
</html> 
<?php 

function updateData($psCon){
    global $HOST;
    global $class;
	
	
    $class->oDbCon->startTrans();
    
    $tableName = 'security_object';
    $tableSecurityObject = 'user_security_object';
    $module =  $_POST['selModule'];
    $company =  $_POST['selCompany'];
    $updateStatus =  $_POST['selStatus'];
    $host = 'localhost';

    if(empty($company) || empty($updateStatus))  die; 

	// select semua module, ambil perlu table ap aj
	$psInit = newConnection(DOMAIN_INIT); 
	$sql = ' select pkey,modulecode,tablename from security_object order by modulename asc';
	$rsModuleInit = $psInit->doQuery($sql);
	$rsModuleInit = array_column($rsModuleInit,null,'pkey');
	$psInit = null;
	
    for($i=0;$i<count($company);$i++) {
        
        $sql = 'select * from customer_company where pkey = '.$class->oDbCon->paramString($company[$i]);  
        $rsComp = $psCon->doQuery($sql); 
 
        $psMod = newConnection($rsComp[0]['name']);
        
        echo  '<strong>'.$rsComp[0]['name'] .'</strong><br>';

        for($j=0;$j<count($module);$j++) {
             
            $moduleRow =  $rsModuleInit[$module[$j]];
         
            //klo ngak perlu ngecek si modul lagi langsung hapus aja
            $tableModule = 'select * from '.$tableName.' where modulecode = '.$class->oDbCon->paramString($moduleRow['modulecode']); 
			echo $tableModule.'<br>';
            $rsModule = $psMod->doQuery($tableModule);
			
            if(empty($rsModule)) continue;

            $modulekey = $rsModule[0]['pkey'];
            $psMod->startTrans();

            $sql = 'select * from '.$tableSecurityObject.' where security_object_key = '.$class->oDbCon->paramString($modulekey) ; 
            $rsAccess = $psMod->doQuery($sql);
            
            // kalo gk ad INSERT
            if (empty($rsAccess)){
                 $sql = 'insert into '.$tableSecurityObject.' (security_object_key,statuskey) values ('.$class->oDbCon->paramString($modulekey).',1)';
                 echo $sql .'<br>';
                 $psMod->execute($sql);
            }
             
         
            $updateAccsess = 'update '.$tableSecurityObject.' set statuskey = '.$class->oDbCon->paramString($updateStatus).'  where security_object_key = '.$class->oDbCon->paramString($modulekey) ; 
            echo $updateAccsess .'<br>';
            $psMod->execute($updateAccsess);
            
			  
			createTable($psMod,$rsModuleInit[$module[$j]]['tablename']);
			
            $psMod->endTrans();

        }
        
        
        echo '<br>';
    } 

    $class->oDbCon->endTrans();
    echo 'done' ;
}


function createTable($con,$tableName){  
	global $class;
	
	$arrTable = json_decode($tableName,true);  
	if (empty($arrTable)) return;
	
	foreach($arrTable as $table){
		
		$tableName = (!is_array($table)) ? $table : $table['tablename'];
		$copydata = (is_array($table) && !empty($table['copydata']) )  ? true : false;
		
		//echo $tableName.'<br>';
			
		$sql = 'SHOW TABLES LIKE ' . $con->paramString(trim($tableName));
		echo $sql.'<br>';
		$rs = $con->doQuery($sql);
		
		// kalo blm ad tablenya, buat
 		if(empty($rs)){
			
			$psInit = newConnection(DOMAIN_INIT);
			
			// get table structure
			$sql = ' SHOW CREATE TABLE ' . $tableName; 
			$rs = $psInit->doQuery($sql);
			 
			$sql = $rs[0]['Create Table']; 
			$con->execute($sql);

			// insert table, biar lebih pasti ambil dr nama kolom saja
			if($copydata){
				$sql = 'show columns from ' . $tableName;
				$rs = $con->doQuery($sql);
				$rsField = array_column($rs,'Field');

				$sql = 'select * from ' . $tableName;
				$rs = $psInit->doQuery($sql);

				foreach($rs as  $row) { 

					$arrValue = array();
					foreach($rsField as $fieldName)  
						array_push( $arrValue, '\''.$row[$fieldName].'\'');

					$sql = 'insert into ' . $tableName.' values ('.implode(',',$arrValue).')';
					$con->execute($sql);

				}
			}
			$psInit = null;

			
		}
	}
		
}



function getModuleSet(){
    

//Template Modul
$arrDefault = array('warehouse', 'setting', 'employee', 'employeeCategory' , 'ReportEmployee', 'City', 'CityCategory', 'RoleTemplate','CustomCode', 'SecurityPrivileges', 'Setting');

$arrBusinessPartner = array('customer', 'CustomerCategory', 'supplier',
							'ReportCustomer', 'ReportSupplier'); 
 
$arrInventory = array('Item', 'COGS','ItemCategory', 'Brand', 'ItemIn', 'ItemOut', 'ItemAdjustment', 'WarehouseTransfer',
					  'ReportItem', 'ReportItemIn', 'ReportItemOut','ReportItemAdjustment','ReportWarehouseTransfer', 'ReportStockCard'); 
	
$arrPayment = array('TermOfPayment', 'PaymentMethod'); 
	
$arrDownpayment = array('SupplierDownpayment','CustomerDownpayment',
						'ReportCustomerDownpayment','ReportSupplierDownpayment');  
	
$arrRetailPurchase = array('PurchaseOrder','PurchaseReceive',
						  'ReportPurchaseOrder', 'ReportPurchaseReceive');
	
$arrRetailSales = array('SellingPrice','SalesOrder',
					   'ReportSalesOrder', 'ReportSalesDelivery');
	
$arrARAP = array('AP','APPayment','AR', 'ARPayment','ARAPNetting',
				 'ReportAP', 'ReportAPPayment', 'ReportAR', 'ReportARPayment'
				);
	
$arrPrepaidTax = array('APPayableTax23','APPayableTax23Payment','ARPrepaidTax23', 'ARPrepaidTax23Payment',
				 'reportARPrepaidTax23', 'reportARPrepaidTax23Payment', 'reportAPPayableTax23', 'reportAPPayableTax23Payment'
				);
	
$arrARAPEmployee = array('APEmployee','APEmployeePayment','AREmployee','AREmployeePayment','ARAPEmployeeNetting',
				   'ReportAREmployee', 'ReportAREmployeePayment'
				);

$arrARAPEmployeeCommission = array('APEmployeeCommission','APEmployeeCommissionPayment',
				   'ReportAPEmployeeCommission', 'ReportAPEmployeeCommissionPayment'
				);
	
$arrCashBank = array('CashBank','CostCashOut','CashIn','CashOut', 'CashBankTransfer',
				 'ReportCashIn', 'ReportCashOut', 'ReportCashBankTransfer', 'ReportCashBankVoucher'
				);
	
$arrCashAdvance = array('CashBankRealization',  'ReportCashBankRealization'
				);
 
$arrGL = array('COALink','ChartOfAccount','GeneralJournal',
				 'ReportGeneralJournal', 'ReportGeneralLedger', 'ReportBalanceSheet', 'ReportIncomeStatement', 'ReportTrialBalance'
				);
 
 
$arrModuleSet = array();
	
$module = 'Init';
$arrModuleSet[$module] = array();
$arrModuleSet[$module] = array_merge($arrModuleSet[$module] , $arrDefault);
	
$module = 'Cash Bank';
$arrModuleSet[$module] = array();
$arrModuleSet[$module] = array_merge($arrModuleSet[$module] , $arrCashBank);
	
$module = 'GL';
$arrModuleSet[$module] = array();
$arrModuleSet[$module] = array_merge($arrModuleSet[$module] , $arrCashBank);
$arrModuleSet[$module] = array_merge($arrModuleSet[$module] , $arrGL);
	
$module = 'Winstok';
$arrModuleSet[$module] = array();
$arrModuleSet[$module] = array_merge($arrModuleSet[$module] , $arrDefault);
$arrModuleSet[$module] = array_merge($arrModuleSet[$module] , $arrBusinessPartner);
$arrModuleSet[$module] = array_merge($arrModuleSet[$module] , $arrInventory);
$arrModuleSet[$module] = array_merge($arrModuleSet[$module] , $arrPayment);
$arrModuleSet[$module] = array_merge($arrModuleSet[$module] , $arrDownpayment);
$arrModuleSet[$module] = array_merge($arrModuleSet[$module] , $arrRetailPurchase);
$arrModuleSet[$module] = array_merge($arrModuleSet[$module] , $arrRetailSales);
$arrModuleSet[$module] = array_merge($arrModuleSet[$module] , $arrARAP);
	 
	
$module = 'TMS';
$arrModuleSet[$module] = array();
$arrModuleSet[$module] = array_merge($arrModuleSet[$module] , $arrDefault);
$arrModuleSet[$module] = array_merge($arrModuleSet[$module] , $arrBusinessPartner);
$arrModuleSet[$module] = array_merge($arrModuleSet[$module] , $arrInventory);
$arrModuleSet[$module] = array_merge($arrModuleSet[$module] , $arrPayment);
$arrModuleSet[$module] = array_merge($arrModuleSet[$module] , $arrDownpayment);
$arrModuleSet[$module] = array_merge($arrModuleSet[$module] , $arrRetailPurchase);
$arrModuleSet[$module] = array_merge($arrModuleSet[$module] , $arrARAP);
$arrModuleSet[$module] = array_merge($arrModuleSet[$module] , $arrARAPEmployee);
$arrModuleSet[$module] = array_merge($arrModuleSet[$module] , $arrARAPEmployeeCommission);
$arrModuleSet[$module] = array_merge($arrModuleSet[$module] , $arrPrepaidTax);
$arrModuleSet[$module] = array_merge($arrModuleSet[$module] , $arrCashBank);
$arrModuleSet[$module] = array_merge($arrModuleSet[$module] , $arrCashAdvance);
$arrModuleSet[$module] = array_merge($arrModuleSet[$module] , $arrGL);
	
$arrModuleSet[$module] = array_merge($arrModuleSet[$module] , array(
	'SellingPrice','overwriteContract', 'Consignee', 'TruckingService', 'ServiceCategory', 'Car','CarCategory','Chassis', 'ChassisCategory', 'Depot', 'Terminal',
	'Location', 'TruckingJob', 'TruckingServiceOrderCategory', 'TruckingServiceOrder', 'TruckingServiceWorkOrder', 'TruckingServiceWorkOrderCost',
	'TruckingServiceOrderInvoice', 'TruckingSellingRate', 'CostRate', 'WorkProgressStep', 'TruckingPurchase','TruckingCost', 'TruckingCostCashOut',
	
	'ReportConsignee', 'ReportCar', 'reportTruckingServiceOrder', 'reportTruckingServiceWorkOrder', 'reportTruckingServiceOrderInvoice',
	'reportTruckingCost', 'reportLocation'
));
	
	
$module = 'FMS';
$arrModuleSet[$module] = array();
$arrModuleSet[$module] = array_merge($arrModuleSet[$module] , $arrDefault);
$arrModuleSet[$module] = array_merge($arrModuleSet[$module] , $arrBusinessPartner);
$arrModuleSet[$module] = array_merge($arrModuleSet[$module] , $arrPayment);
$arrModuleSet[$module] = array_merge($arrModuleSet[$module] , $arrDownpayment);
$arrModuleSet[$module] = array_merge($arrModuleSet[$module] , $arrARAP);
$arrModuleSet[$module] = array_merge($arrModuleSet[$module] , $arrARAPEmployee);
$arrModuleSet[$module] = array_merge($arrModuleSet[$module] , $arrPrepaidTax);
$arrModuleSet[$module] = array_merge($arrModuleSet[$module] , $arrCashBank);
$arrModuleSet[$module] = array_merge($arrModuleSet[$module] , $arrCashBank);
$arrModuleSet[$module] = array_merge($arrModuleSet[$module] , $arrGL);

$arrModuleSet[$module] = array_merge($arrModuleSet[$module] , array(
	'Consignee','EMKLJobOrder','EMKLPurchaseOrder','EMKLOrderInvoice','EMKLCommission','EMKLOrder','EMKLInvoiceReceipt','EMKLInvoiceReceipt', 'Depot', 'Terminal','Port',
	'Container','CashAdvance','CashAdvanceRealization','Item', 'ItemCategory', 'ServiceCategory','Vessel','Service','CreditNote', 'DebitNote',
	
	'ReportEMKLCommission','ReportPurchaseOrderExportFF','ReportPurchaseOrderImportFF','ReportSalesOrderExportFF','ReportSalesOrderImportFF',
	'ReportSalesOrderInvoiceFF','ReportEMKLInvoiceReceipt','reportEmklJobOrderHeaderExport','reportEmklJobOrderHeaderImport','ReportCashAdvance','ReportCashAdvanceRealization'
));
	
return $arrModuleSet;
}

function nestedLowercase($value) {
    if (is_array($value)) {
        return array_map('nestedLowercase', $value);
    }
    return strtolower($value);
}

  

?>