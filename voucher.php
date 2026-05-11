<?php 
require_once '_config.php'; 
require_once '_include-fe-v2.php'; 
require_once '_global.php';  

includeClass(array('Customer.class.php','VoucherTransaction.class.php'));
$voucherTransaction = new VoucherTransaction();
$customer = new Customer();
$voucher = new Voucher();

if(!$security->isMemberLogin(false)) 
	header('location:/logout'); 

// gk bisa pake getAvailableVoucher karena baru listing, blm ad nilai sales dsb 
$rsVoucherTrans = $voucherTransaction->searchData($voucherTransaction->tableName.'.customerkey',USERKEY,true,'
													and '.$voucherTransaction->tableName.'.statuskey in (2)
													and '.$voucherTransaction->tableName.'.expdate >= date(now())',
												    'order by '.$voucherTransaction->tableName.'.trdate desc,'.$voucherTransaction->tableName.'.pkey desc');

// USERKKEY => user dari frontend 
// gk kepake
//if (!empty(USERKEY))
//  $rsVoucherCollectible =  $voucherTransaction->removeOneTimeUse($rsVoucherTrans);

//$rsVoucherTrans = $voucherTransaction->searchData($voucherTransaction->tableName.'.customerkey',USERKEY,true,'and '.$voucherTransaction->tableName.'.statuskey in (3)', 'order by '.$voucherTransaction->tableName.'.useddate desc,'.$voucherTransaction->tableName.'.pkey desc');
//$arrTwigVar ['rsInactiveVoucher'] =  $rsVoucherTrans;

// collectible
// nanti ditambahkan, dikelaurin kalo utk yg cuma sekali pake
$rsVoucherCollectible = $voucher->searchData($voucher->tableName.'.statuskey',2,true,'
											and '.$voucher->tableName.'.typekey = 2
											and '.$voucher->tableName.'.qty >  '.$voucher->tableName.'.qtyused 
											and '.$voucher->tableName.'.startdate <=  date(now()) and  '.$voucher->tableName.'.enddate >= date(now()) ',
											'order by  '.$voucher->tableName.'.pkey desc');

// USERKKEY => user dari frontend 
if (!empty(USERKEY))
  $rsVoucherCollectible =  $voucher->removeOneTimeUse($rsVoucherCollectible);



//$arrTwigVar ['rsActiveCollectibleVoucher'] =  $rsVoucherCollectible;

foreach($rsVoucherCollectible as $row){
	array_push($rsVoucherTrans, array(
									'voucherkey' => $row['pkey'],
									'voucherlabel' =>$row['name'],
									'code' =>$row['code'],
									'expdate' => $row['enddate'],
									'typekey' => $row['typekey'],
									'vouchershortdesc' => $row['shortdesc'],
						) 
				);
}

$minimumPoint = $class->loadSetting('minimumFirstPoint'); 

$rsCustomer = $customer->searchDataRow(array($customer->tableName.'.point',$customer->tableName.'.membershiplevel',$customer->tableName.'.canusepoint'),' and '.$customer->tableName.'.pkey = ' . $customer->oDbCon->paramString(USERKEY));
$arrTwigVar ['eligiblePoint'] =  $rsCustomer[0]['point'];  
$arrTwigVar ['minimumPoint'] =  $minimumPoint;  
$arrTwigVar ['rsActiveVoucher'] =  $rsVoucherTrans; 
$arrTwigVar ['membershipLevel'] =   $rsCustomer[0]['membershiplevel'];  
$arrTwigVar ['canUsePoint'] =   $rsCustomer[0]['canusepoint'];  
echo $twig->render('voucher.html', $arrTwigVar);

?>