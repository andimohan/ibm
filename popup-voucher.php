<?php 
require_once '_config.php'; 
require_once '_include-fe-v2.php';
require_once '_global.php';  
 
includeClass(array("Voucher.class.php","Item.class.php","DiscountScheme.class.php"));

$voucher = new Voucher();
$item = new Item();
$discountScheme = new DiscountScheme();

if(!$security->isMemberLogin(false))  header('location:/logout');

$arrItemKey = array_column($_SESSION[$class->loginSession]['cart'],'itemkey'); 
$rsItemCol = $item->searchDataRow(array($item->tableName.'.pkey',
                                       $item->tableName.'.name', 
                                       $item->tableName.'.deftransunitkey', 
                                       $item->tableName.'.sellingprice', 
                                       $item->tableName.'.gramasi', 
                                       $item->tableName.'.weightunitkey', 
                                       ), ' and '.$item->tableName.'.pkey in ('.$item->oDbCon->paramString( $arrItemKey,',').') ');

$discountScheme->applyDiscountScheme($rsItemCol); 
$rsItemCol = array_column($rsItemCol,null,'pkey');

$totalWeight = 0;
$subtotal = 0;

for($i=0;$i<count($_SESSION[$class->loginSession]['cart']); $i++){

    $itemkey = $_SESSION[$class->loginSession]['cart'][$i]['itemkey'];

    if(!isset($rsItemCol[$itemkey])) continue;

    $arrItem = $rsItemCol[$itemkey];

    $arrItem['qty'] =  $_SESSION[$class->loginSession]['cart'][$i]['qty'] ;     
    $rowSubtotal  = $arrItem['qty'] * $arrItem['sellingprice'];
    $arrItem['subtotal'] = $rowSubtotal;

    $subtotal += $rowSubtotal;

    $arrItem['gramasi'] = $arrItem['gramasi'];
    if ($arrItem['weightunitkey'] == UNIT['kg'])
        $arrItem['gramasi'] *= 1000;

    $arrItem['subtotalgramasi'] = $arrItem['qty'] * $arrItem['gramasi']; 
    $totalWeight += $arrItem['subtotalgramasi'];

} 


$pointDisc = 0; // nanti baru diupdate $pointNeeded * $pointUnitValue * -1;
$grandtotal = $subtotal + $pointDisc; 

//availabe voucher, harus cek ke sales ny jg, dan itemnya 
$rsVoucher = (!empty(USERKEY)) ?  $voucher->getAvailableVoucher(array('category' => array(VOUCHER_CATEGORY['sales'],VOUCHER_CATEGORY['shipment']),
													   		          'voucherType' => array(VOUCHER_TYPE['collectible'],VOUCHER_TYPE['regular'],VOUCHER_TYPE['claim']), 
                                                                      'userkey' => USERKEY,
                                                                      'totalsales' => $grandtotal
													  		)
													  ) : array();

$class->mknatsort($rsVoucher,'isAvailable',true);

$voucherkey = isset($_GET['voucherkey']) ? intval($_GET['voucherkey']) : 0; // intval buat jaga2 kalo diinject
$typekey = isset($_GET['typekey']) ? intval($_GET['typekey']) : 0; 
$categorykey = isset($_GET['categorykey']) ? intval($_GET['categorykey']) : 0; 
$descOnly = (isset($_GET['descOnly']) && !empty($_GET['descOnly'])) ? 1 : 0;
$shippingSelected = (isset($_GET['shippingSelected']) && !empty($_GET['shippingSelected'])) ? 1 : 0;

$arrTwigVar['voucherKey'] = $voucherkey;
$arrTwigVar['typeKey'] = $typekey;
$arrTwigVar['categoryKey'] = $categorykey;
//$arrTwigVar['hidDescOnly'] = $class->inputHidden('hidDescOnly', array('value' => $descOnly));
$arrTwigVar['descOnly'] = $descOnly;
$arrTwigVar['shippingSelected'] = $shippingSelected;

$arrTwigVar ['btnSubmit'] = $class->inputSubmit('btnSave',$class->lang['confirm']); 


//$getSelectedVoucher = $voucher->getDataRowById($voucherkey);
//$arrTwigVar['selectedVoucher'] = $getSelectedVoucher;

foreach($rsVoucher as $index => $row){ 
    $rsVoucher[$index]['inputCheckBoxVoucher'] = $class->inputCheckBox('chkUseVoucher', array("etc" => 'data-pkey = '.$row['pkey'].' data-typekey='.$row['typekey'].' data-categorykey='.$row['categorykey']));
}

// $item->setLog($rsVoucher,true);
    
$arrTwigVar ['rsVoucher'] =  $rsVoucher;

echo $twig->render('popup-voucher.html', $arrTwigVar);  
 
?>
