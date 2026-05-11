<?php    
require_once '../_config.php'; 
require_once '../_include-v2.php';
 
ini_set('max_execution_time', '600'); //300 seconds = 5 minutes

// sementara utk bulanan dulu

includeClass(array('Customer.class.php','SalesOrderSubscription.class.php','InvoiceOrderSubscription.class.php'));

$salesOrderSubscription = new SalesOrderSubscription();
$invoiceOrderSubscription = new InvoiceOrderSubscription();
$customer = new Customer();

// invocie digenerate akhir bulan, utk periode tgl 1 bulan selanjutnya
$currDate = date('01 / m / Y', strtotime(date('Y-m-d'). ' + 1 months'));

// query semua job order yg statusnya maasih aktif dn recurring
// dan blm ad invoiceny di bulan berjalan 
$rsSalesOrder = $salesOrderSubscription->searchData('','',true,' and '.$salesOrderSubscription->tableName.'.statuskey in (3)
																 and '.$salesOrderSubscription->tableName.'.periodekey = 1 
																 and  '.$salesOrderSubscription->tableName.'.pkey not in (
																 	select '.$invoiceOrderSubscription->tableName.'.refkey 
																	from '.$invoiceOrderSubscription->tableName.'
																	where 
                                                                        '.$invoiceOrderSubscription->tableName.'.statuskey <> 4 
                                                                        and month('.$invoiceOrderSubscription->tableName.'.trdate) = '.$customer->oDbCon->paramDate($currDate,'/','m').' 
                                                                        and year('.$invoiceOrderSubscription->tableName.'.trdate) = '.$customer->oDbCon->paramDate($currDate,'/','Y').' 
																 ) ');
 
//echo count ($rsSalesOrder).'<br>';
//
//foreach($rsSalesOrder as $row){
//	echo $row['code'].'<br>';
//}
//die;

$rsCustomer = $customer->searchDataRow(array($customer->tableName.'.pkey',$customer->tableName.'.termofpaymentkey'),
									  	' and '.$customer->tableName.'.pkey in ('.$customer->oDbCon->paramString(array_column($rsSalesOrder,'customerkey'),',').')');
	
// create invoice 
// untuk testing, 10 invoice sja dulu
$tempCtr = 0;
 
foreach($rsSalesOrder as $row){
	
//		if($tempCtr++ > 15) break;
	
		$pkey = $row['pkey'];
	
		$invoiceOrderSubscription = new InvoiceOrderSubscription(); // coba init ulang, lupa masih perlu atau gk 
		$obj = $invoiceOrderSubscription;
	
		$rsDetail = $salesOrderSubscription->getItemForInvoice($pkey); 
 
		try { 
			
			if(!$obj->oDbCon->startTrans(true))
				throw new Exception($obj->errorMsg[100]);


			$arr = array();
			$arr['code'] = 'xxxx';
			$arr['trDate'] = $currDate ;
			$arr['hidCustomerKey'] = $row['customerkey'] ;
			$arr['selWarehouseKey'] = $row['warehousekey'] ;
			$arr['hidSoKey'] =  $pkey ;
			$arr['selStatus'] = 1 ;
			$arr['chkIncludeTax'] = 0 ;
			$arr['taxPercentage'] = 11 ;
			$arr['selTermOfPayment'] = $rsCustomer[$row['customerkey']]['termofpaymentkey'];

			$arr['hidDetailKey'] = array();
			$arr['hidRefSODetailKey'] = array();
			$arr['hidItemKey'] = array();
			$arr['qty'] = array();
			$arr['priceInUnit'] = array();
			$arr['detailSubtotal'] = array();
			$arr['description'] = array();

			foreach($rsDetail as $row){
				array_push($arr['hidDetailKey'],0);
				array_push($arr['hidRefSODetailKey'],$row['pkey']);
				array_push($arr['hidItemKey'],$row['itemkey']);
				array_push($arr['qty'],$row['qty']);
				array_push($arr['priceInUnit'],$row['priceinunit']);
				array_push($arr['detailSubtotal'],$row['total']);
				array_push($arr['description'],$row['description']);  
			}

			$obj->addData($arr);
			
			$obj->oDbCon->endTrans();   
			
	    } catch(Exception $e){ 
             
            $obj->oDbCon->rollback();  
		}		
				  
}

echo 'done';

?>