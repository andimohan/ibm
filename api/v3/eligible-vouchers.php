<?php
require_once '../../_config.php';  
require_once '_include.php';   
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/VoucherTransaction.class.php';  

$voucherTransaction = new VoucherTransaction(); 
$voucher = new Voucher();


function endForRequestMethodError(){ 
    global $class;
    $RETURN_VALUE = array();
    $RETURN_VALUE['response_code'] = 400;
    $RETURN_VALUE['message'] = $class->errorMsg[213];
    http_response_code($RETURN_VALUE['response_code']); 
    echo json_encode($RETURN_VALUE); 
    die;   
}

if(!isset($_GET) || empty($_GET['userkey'])) endForRequestMethodError();

define ('USERKEY', $_GET['userkey']);

$hasSuccessValue = false;
$arrFailed = array();
$ARR_RETURN_VALUE = array();

$responseCode = 200;
$message = ''; 

// gk bisa pake getAvailableVoucher karena baru listing, blm ad nilai sales dsb 
$rsVoucherTrans = $voucherTransaction->searchData($voucherTransaction->tableName.'.customerkey',USERKEY,true,'
													and '.$voucherTransaction->tableName.'.statuskey in (2)
													and '.$voucherTransaction->tableName.'.expdate >= date(now())',
												    'order by '.$voucherTransaction->tableName.'.trdate desc,'.$voucherTransaction->tableName.'.pkey desc');
 
// collectible
// nanti ditambahkan, dikeluarin kalo utk yg cuma sekali pake
$rsVoucherCollectible = $voucher->searchData($voucher->tableName.'.statuskey',2,true,'
											and '.$voucher->tableName.'.typekey = 2
											and '.$voucher->tableName.'.qty >  '.$voucher->tableName.'.qtyused 
											and '.$voucher->tableName.'.startdate <=  date(now()) and  '.$voucher->tableName.'.enddate >= date(now()) ',
											'order by  '.$voucher->tableName.'.pkey desc');
 

// USERKKEY => user dari frontend 
if (!empty(USERKEY))
  $rsVoucherCollectible =  $voucher->removeOneTimeUse($rsVoucherCollectible);

foreach($rsVoucherCollectible as $row){
	array_push($rsVoucherTrans, array(
									'pkey' => $row['pkey'],
									'voucherlabel' => $row['name'],
									'code' =>$row['code'],
									'expdate' => $row['enddate'],
									'typekey' => $row['typekey'],
									'categorykey' => $row['categorykey'],
									'vouchershortdesc' => $row['shortdesc'],
									'voucherdesc' => $row['trdesc'], 
									'minamount' => $row['minamount'], 
									'maxdiscount' => $row['maxdiscount'], 
									'discounttype' => $row['discounttype'], 
									'value' => $row['value'], 
									'onetimeuse'=> $row['onetimeuse']
						)  
				);
}
 
$arrReturn = array();
foreach($rsVoucherTrans as $row){
	array_push($arrReturn, array(
						'key' => $row['pkey'],
						'code' =>$row['code'],
						'label' => $row['voucherlabel'],
						'exp_date' => strtotime($row['expdate']),
						'voucher_type_key' => $row['typekey'],
						'voucher_category_key' => $row['categorykey'],
						'short_description' => $row['vouchershortdesc'],
						'description' =>  $row['voucherdesc'],
						'min_sales_amount' => $row['minamount'],
						'max_discount' => $row['maxdiscount'],
						'discount_type' => $row['discounttype'],
						'discount_value' => $row['value'],
						'one_time_use' => $row['onetimeuse'],
					) 
			  );
}

$RETURN_VALUE['response_code'] = $responseCode;
$RETURN_VALUE['data'] = $arrReturn;
$RETURN_VALUE['message'] = $message;

http_response_code($RETURN_VALUE['response_code']); 
echo json_encode($RETURN_VALUE);
die;
?>