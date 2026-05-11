<?php 

include '../../_config.php';  
include '../../_include.php';

require_once  $_SERVER ['DOCUMENT_ROOT'].'/Twig/Autoloader.php';
Twig_Autoloader::register();

$loader = new Twig_Loader_Filesystem($class->defaultDocAdminPath.'/pos/template');
$twig = new Twig_Environment($loader);
 
$arrTwigVar ['TEMPLATE_CSS_PATH'] =  $class->adminCssPath;
$arrTwigVar ['TEMPLATE_JS_PATH'] =  $class->defaultJsPath; 
$arrTwigVar ['SELF_PAGE'] = $_SERVER['PHP_SELF'];

/* settings */
$rsSetting =  $setting->getSettingData();
for ($i=0;$i<count($rsSetting);$i++){
	$code = $rsSetting[$i]['code'];
	 
	if ($rsSetting[$i]['multivalue'] == 0){ 
			if ($rsSetting[$i]['type'] == 3 )
				$arrTwigVar ['settings'][$code] =str_replace(chr(13),'<br>',$rsSetting[$i]['value']);
			else
				$arrTwigVar ['settings'][$code] = $rsSetting[$i]['value'] ;
	}else{ 
		$arrDetail = $setting->getDetailByCode($code);
		$arrTwigVar ['settings'][$code] = $arrDetail;
	} 
		 
}   

$obj = $salesOrder;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,11,true));

$_POST['qty[]'] = 1;

$arrPaymentMethod = $paymentMethod->searchData ('','',true,' and ('.$paymentMethod->tableName.'.statuskey = 1)');
for($i=0;$i<count($arrPaymentMethod);$i++) {     
   $arrTwigVar['paymentMethod'][$i] = $arrPaymentMethod[$i]['name'];
   $arrTwigVar['hidPaymentMethodKey'][$i] = $class->input('hidden','paymentMethodKey[]',true,$arrPaymentMethod[$i]['pkey']);
   $arrTwigVar['inputPaymentMethodValue'][$i]  = $class->input('text','paymentMethodValue[]',true,'','style="text-align:right;"  onChange="pointOfSalesCalculateTotal()"','form-control inputnumber');
}  

$arrTwigVar['inputItemCode'] =  $class->input('text','itemCode',true,'','disabled="disabled"');  
$arrTwigVar['inputHidItemKey'] = $class->input('hidden','hidHeaderItemKey');
$arrTwigVar['inputItemName'] =  $class->input('text','itemName');
$arrTwigVar['inputPriceInUnit'] =  $class->input('text','priceInUnit',true,'','disabled="disabled"','form-control inputnumber');
$arrTwigVar['inputAdd'] = $class->input('button','btnAddRows',false,'Tambah Baris','style="margin-top:0.2em;"');

$arrTwigVar['inputHidItemKeyDetail'] = $class->input('hidden','hidItemKey[]',false,'','disabled="disabled"');
$arrTwigVar['inputItemNameDetail'] =  $class->input('text','itemName[]',false,'','disabled="disabled"');
$arrTwigVar['inputQty'] =  $class->input('text','qty[]',true,'',' style="text-align:right;"  onChange="pointOfSalesCalculateDetail(this)"','form-control inputnumber');
$arrTwigVar['inputPriceInUnitDetail'] =  $class->input('text','priceInUnit[]',true,'','disabled="disabled" style="text-align:right;"  onChange="pointOfSalesCalculateDetail(this)"','form-control inputnumber');
$arrTwigVar['inputDiscountValueInUnit'] = $class->input('text','discountValueInUnit[]',false,'',' style="text-align:right;"  onChange="pointOfSalesCalculateDetail(this)"','form-control inputnumber');
$arrTwigVar['selDiscountType'] = $class->inputSelect('selDiscountType[]', $class->arrDiscountType,false,'',' onChange="pointOfSalesCalculateDetail(this)"');
$arrTwigVar['inputSubtotalDetail'] = $class->input('text','subtotal[]',true,'','disabled="disabled" style="text-align:right;" readonly="readonly"','form-control inputnumber');
$arrTwigVar['removeButton'] = $class->input('button','btnDeleteRows',false,'Hapus','','btn btn-link remove-button');

$arrTwigVar['inputSubtotal'] = $class->input('text','subtotal',true,'','style="text-align:right;" readonly="readonly"','form-control inputnumber');
$arrTwigVar['inputFinalDiscount'] = $class->input('text','finalDiscount',true,'','style="text-align:right;" onChange="pointOfSalesCalculateTotal()"','form-control inputnumber');
$arrTwigVar['selFinalDiscountType'] = $class->inputSelect('selFinalDiscountType', $class->arrDiscountType,true,'', 'onChange="pointOfSalesCalculateTotal()"');
$arrTwigVar['inputBeforeTaxTotal'] = $class->input('text','beforeTaxTotal',true,'','style="text-align:right;" readonly="readonly"','form-control inputnumber');
$arrTwigVar['inputTaxValue'] = $class->input('text','taxValue',true,'','style="text-align:right;"  onChange="pointOfSalesCalculateTotal()" readonly="readonly"','form-control inputnumber');
$arrTwigVar['inputTaxPercentage'] = $class->input('text','taxPercentage',true,'','style="text-align:right;"  onChange="pointOfSalesCalculateTotal()"','form-control inputnumber');
$arrTwigVar['inputTotal'] = $class->input('text','total',true,'','style="text-align:right;" readonly="readonly"','form-control inputnumber');
$arrTwigVar['inputTotalPayment'] = $class->input('text','totalPayment',true,'','disabled="disabled" style="text-align:right;" onChange="pointOfSalesCalculateTotal()"','form-control inputnumber');
$arrTwigVar['inputBalance'] = $class->input('text','balance',true,'','style="text-align:right;" readonly="readonly"','form-control inputnumber');

$arrTwigVar['saveButton'] = $salesOrder->generateSaveButton();

echo $twig->render('template.html', $arrTwigVar); 

?>