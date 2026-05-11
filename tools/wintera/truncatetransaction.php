<?php
die("die, comment open for reset transaction");
// untuk cutoff transaksi

include_once '../../_config.php'; 
include_once '../../_include-v2.php';

if(DOMAIN_NAME!= 'eti.wintera.co.id') die;

if(!isset($_GET) || empty($_GET['trdate'])) die;

$truncateUntilDate = $_GET['trdate'];

$class->oDbCon->startTrans();

$arrTruncatetable = array();
//array_push($arrTruncatetable, array('tableName' => 'trucking_cost_cash_out_header',
//								  'detail' => array(
//									  				array('tableName' => 'trucking_cost_cash_out_detail'),
//									  				array('tableName' => 'trucking_cost_cash_out_file') 
//									  			)
//								 ));
//
//
//array_push($arrTruncatetable, array('tableName' => 'cash_bank'));

// harus invoice dihapus dulu, agar Jo nya bisa milih
//array_push($arrTruncatetable, array('tableName' => 'trucking_service_order_invoice_header',
//								  'detail' => array(
//									  				array('tableName' => 'trucking_service_order_invoice_detail'),
//									  				array('tableName' => 'trucking_service_order_invoice_file') ,
//									  				array('tableName' => 'trucking_service_order_invoice_item_detail', 'reffield' => 'refheaderkey'), 
//									  			)
//								 ));

// ini hapus data  Jo baru yg blm kebuat inv tdk ?
array_push($arrTruncatetable, array('tableName' => 'trucking_service_order_header',
								  'reftable' => 'trucking_service_order_invoice_detail',
								  'reffield' => 'pkey',
								  'criteria' => 'trucking_service_order_header.trdate <= ' .$class->oDbCon->paramString($truncateUntilDate),
								  //'criteria' => 'trucking_service_order_header.trdate <= \'2022-12-31\' ' , 
								  'reftablefield' => 'salesorderkey',
								  'detail' => array(
									  				array('tableName' => 'trucking_service_order_detail'),
									  				array('tableName' => 'trucking_service_order_file') 
									  			)
								 ));


array_push($arrTruncatetable, array('tableName' => 'trucking_service_work_order',
								  'reftable' => 'trucking_service_order_header',
								  'detail' => array(
									  				array('tableName' => 'trucking_service_work_order_cargo'),
									  				array('tableName' => 'trucking_service_work_order_cost_cargo', 'reffield' => 'refheaderkey'), 
									  				array('tableName' => 'trucking_service_work_order_cost'),
									  				array('tableName' => 'trucking_service_work_order_car'),
									  			)
								 ));

//array_push($arrTruncatetable, array('tableName' => 'cash_out_header',
//								  'detail' => array(
//									  				array('tableName' => 'cash_out_detail'),
//									  				array('tableName' => 'cash_out_file') 
//									  			)
//								 ));
//
//array_push($arrTruncatetable, array('tableName' => 'cash_in_header',
//								  'detail' => array(
//									  				array('tableName' => 'cash_in_detail') 
//									  			)
//								 ));
//
//array_push($arrTruncatetable, array('tableName' => 'cash_bank_transfer_header',
//								  'detail' => array(
//									  				array('tableName' => 'cash_bank_transfer_detail') ,
//									  				array('tableName' => 'cash_bank_transfer_file') , 
//									  			)
//								 ));
//
//
//array_push($arrTruncatetable, array('tableName' => 'ar_payment_header',
//								  'detail' => array(
//									  				array('tableName' => 'ar_payment_detail'),
//									  				array('tableName' => 'ar_payment_file'), 
//									  			)
//								 ));
//
//array_push($arrTruncatetable, array('tableName' => 'ap_payment_header',
//								  'detail' => array(
//									  				array('tableName' => 'ap_payment_detail'),
//									  				array('tableName' => 'ap_payment_file'), 
//									  			)
//								 ));
//
//// AP AR jgn langsugn dihapus karena bisa masih ad outstanding
//array_push($arrTruncatetable, array('tableName' => 'ar',
//								  'criteria' => 'statuskey = 4'
//								 ));
//
//
//// hapus AR yang sudah lunas, tp tdk ada di payment (karena payment sudah dicutoff)
//array_push($arrTruncatetable, array('tableName' => 'ar',
//								  'criteria' => 'statuskey = 3 and trdate <= ' .$class->oDbCon->paramString($truncateUntilDate),
//								  'reftable' => 'ar_payment_detail',
//								  'reffield' => 'pkey',
//								  'reftablefield' => 'arkey'
//								 ));
//
//array_push($arrTruncatetable, array('tableName' => 'ap',
//								  'criteria' => 'statuskey = 4'
//								 ));
//
//
//// hapus AP yang sudah lunas, tp tdk ada di payment (karena payment sudah dicutoff)
//array_push($arrTruncatetable, array('tableName' => 'ap',
//								  'criteria' => 'statuskey = 3 and trdate <= ' .$class->oDbCon->paramString($truncateUntilDate),
//								  'reftable' => 'ap_payment_detail',
//								  'reffield' => 'pkey',
//								  'reftablefield' => 'apkey'
//								 ));
//
//
//
//array_push($arrTruncatetable, array('tableName' => 'general_journal_header',
//								  'detail' => array(
//									  				array('tableName' => 'general_journal_detail'),
//									  				array('tableName' => 'general_journal_file'), 
//									  			)
//								 ));

$arrSQL = array(); 
foreach($arrTruncatetable as $tableRow){
	
	$headerTableName =  $tableRow['tableName'];
	
	$headerCriteria = isset($tableRow['criteria']) ? ' and ' .$tableRow['criteria'] : '';
	
	if(isset($tableRow['reftable'])){
		$refFieldHeader = (isset($tableRow['reffield']) && !empty($tableRow['reffield'])) ?  $tableRow['reffield']  : 'refkey';
		$refTableReferenceKey = (isset($tableRow['reftablefield']) && !empty($tableRow['reftablefield'])) ?  $tableRow['reftablefield']  : 'pkey';
		array_push($arrSQL,'delete from '.$headerTableName.' where  '.$refFieldHeader.' not in (select '.$refTableReferenceKey.' from '.$tableRow['reftable'].')' . $headerCriteria);
	}else{
		array_push($arrSQL,'delete from '.$headerTableName.' where trdate <= ' .$class->oDbCon->paramString($truncateUntilDate) . $headerCriteria);
	}
	
	if(!isset($tableRow['detail']) || empty($tableRow['detail'])) continue;
	
	
	$arrDetail = $tableRow['detail'];
	foreach($arrDetail as $detailRow){
		$refField = (isset($detailRow['reffield']) && !empty($detailRow['reffield'])) ? $detailRow['reffield'] : 'refkey';
	
		array_push($arrSQL,'delete from '.$detailRow['tableName'].' where '.$refField.' not in (select pkey from '.$headerTableName.')');
	}
}
 
// special case
//array_push($arrSQL,'truncate chart_of_account_amount'); ini harus open terus closing ulang dari awal
//array_push($arrSQL,'delete from chart_of_account_active_period where runningmonth  < ' .$class->oDbCon->paramString($truncateUntilDate));
//array_push($arrSQL,'delete from  `item_movement` WHERE statuskey =2'); 
	
foreach($arrSQL as $sql){
	echo $sql.';<br>';
	//die;
	$class->oDbCon->execute($sql);
}

$class->oDbCon->endTrans();
	
echo 'done'
 
?>
