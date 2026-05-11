<?php 
require_once '_config.php'; 
require_once '_include-fe-v2.php';
require_once '_global.php';  
 
includeClass(array("Customer.class.php", "CityCategory.class.php","City.class.php","Item.class.php","Shipment.class.php", "DiscountScheme.class.php", "Voucher.class.php", "VoucherTransaction.class.php"));

$customer = new Customer();
$city = new City();
$item = new Item();
$itemUnit = new ItemUnit();
$shipment = new Shipment();
$discountScheme = new DiscountScheme();
$voucherTransaction = new VoucherTransaction();
$voucher = new Voucher();
$salesOrder = new SalesOrder();

$_POST['action'] ='add';  
$arrTwigVar ['inputHidAction'] =  $class->inputHidden('action'); 

$eligiblePoint = 0;
$availableVoucher = array();

// perlu update ulang di ajax


if (USERKEY != 0){     
    $rsUser = $customer->getDataRowById(USERKEY); 
    $_POST['recipientName'] = $rsUser[0]['name'];
    $_POST['recipientPhone'] = $rsUser[0]['phone'];
    $_POST['recipientEmail'] = $rsUser[0]['email'];
    $_POST['recipientAddress'] = $rsUser[0]['address'];
    $_POST['recipientZipcode'] = $rsUser[0]['zipcode'];
    
    $rsCity = $city->searchData($city->tableName.'.pkey',$rsUser[0]['citykey'],true);
    $_POST['hidRecipientCityKey'] = (!empty($rsCity)) ? $rsCity[0]['pkey'] : '';
    $_POST['hidRecipientCityName'] = (!empty($rsCity)) ? $rsCity[0]['citycategoryname'] : '';
    $_POST['mapAddress'] = $rsUser[0]['mapaddress'];
    $_POST['hidLatLng'] = $rsUser[0]['latlng'];
	
    
	$eligiblePoint = $rsUser[0]['point'];
	$pointUnitValue = $class->loadSetting('rewardsPointUnitValue');
	
	$arrTwigVar['rewardsPointUnitValue'] = $pointUnitValue;
	$arrTwigVar['rsCustomer'] = $rsUser[0];
    
    
    $rsShippingAddress = $customer->getMultipleAddress(USERKEY,1,'',' order by pkey desc');
    $arrTwigVar['arrAddress'] = $rsShippingAddress; // biar gk kebawa yg chooseAddress
	
    if(empty($rsShippingAddress)){
        $rsShippingAddress = array(array('pkey'=>0,'name'=> $class->lang['chooseAddress']));
    }
    // cari yg primary, preselected 
    foreach($rsShippingAddress as $row){
        if ($row['isprimary'] == 1){ 
            $_POST['selInputAddress'] =  $row['pkey'];
            break;
        }
    }
    
    $arrAddress = $class->convertForCombobox($rsShippingAddress,'pkey','name');   
	$arrTwigVar['selInputAddress'] = $class->inputSelect('selInputAddress', $arrAddress);
    
	// CUSTOMER_TYPE['enduser'] ==> jika mau diset per jenis customer bisa ditambahkan, utk kenari tidak perlu
	
//	$availableVoucher =  $voucher->getAvailableVoucher(array('category' => array(VOUCHER_CATEGORY['sales'],VOUCHER_CATEGORY['shipment']),
//													   		 'voucherType' => array(VOUCHER_TYPE['collectible'],VOUCHER_TYPE['regular']), 
//															   'brandkey' => array(), 
//															   'itemkey' =>array(),
//															   'itemcategorykey' => array(),
//													  		)
//													  );
 
}

$totalItem = 0;
$grandtotal = 0;
$subtotal = 0;
$totalWeight = 0;
$rsCartList = array(); 
$arrTwigVar['hidItemKey'] = array(); 
$arrTwigVar['hidItemWeight'] = array(); 

if (isset($_SESSION[$class->loginSession]) && !empty($_SESSION[$class->loginSession]['cart'])) {

    $arrItemKey = array_column($_SESSION[$class->loginSession]['cart'],'itemkey'); 
    $rsItemCol = $item->searchDataRow(array($item->tableName.'.pkey',
                                           $item->tableName.'.name', 
                                           $item->tableName.'.isvariant', 
                                           $item->tableName.'.deftransunitkey', 
                                           $item->tableName.'.sellingprice', 
                                           $item->tableName.'.parentkey', 
                                           $item->tableName.'.gramasi', 
                                           $item->tableName.'.weightunitkey', 
                                           ), ' and '.$item->tableName.'.pkey in ('.$item->oDbCon->paramString( $arrItemKey,',').') ');
    
    $discountScheme->applyDiscountScheme($rsItemCol); 
    
    $rsItemUnit  = $itemUnit->searchDataRow(array($itemUnit->tableName.'.pkey', $itemUnit->tableName.'.name'), ' and '.$itemUnit->tableName.'.pkey in ('.$item->oDbCon->paramString( array_column( $rsItemCol,'deftransunitkey'),',').') ');
    $rsItemParentCol  = $item->searchDataRow(array($item->tableName.'.pkey', $item->tableName.'.name'), ' and '.$item->tableName.'.pkey in ('.$item->oDbCon->paramString( array_column( $rsItemCol,'parentkey'),',').') ');
  
    $rsItemCol = array_column($rsItemCol,null,'pkey');
    $rsItemUnitCol = array_column($rsItemUnit,null,'pkey');
    $rsItemParentCol = array_column($rsItemParentCol,null,'pkey');
    
    
    for($i=0;$i<count($_SESSION[$class->loginSession]['cart']); $i++){

        $itemkey = $_SESSION[$class->loginSession]['cart'][$i]['itemkey'];
        
        if(!isset($rsItemCol[$itemkey])) continue;
        
        $arrItem = $rsItemCol[$itemkey];
        $arrItemParent = !empty($rsItemParentCol[$arrItem['parentkey']]) ? $rsItemParentCol[$arrItem['parentkey']] : array();
        
        // bisa lemot gk kalo byk itemmnya 
 
        $arrItem['image'] =  $item->getMainImage($arrItem['pkey']);

        
        $arrItem['qty'] =  $_SESSION[$class->loginSession]['cart'][$i]['qty'] ;   
        $arrItem['parent'] =  $arrItemParent;
        
        $arrItem ['inputQty'] =  $class->inputNumber('qty[]',array('value' => $arrItem['qty']));
        $arrItem ['hidItemKey'] =  $class->inputHidden('hidItemKey[]',array('value' => $arrItem['pkey']));
        $arrItem ['hidItemPrice'] =  $class->inputHidden('hidItemPrice[]',array('value' => $arrItem['sellingprice']));
         
        
        $arrItem['unitname'] = $rsItemUnitCol[$arrItem['deftransunitkey']]['name']; 
        $rowSubtotal  = $arrItem['qty'] * $arrItem['sellingprice'];
        $arrItem['subtotal'] = $rowSubtotal;

        $totalItem +=  $arrItem['qty'] ;
        $subtotal += $rowSubtotal;
         
        $arrItem['gramasi'] = $arrItem['gramasi'];
        if ($arrItem['weightunitkey'] == UNIT['kg'])
            $arrItem['gramasi'] *= 1000;
        
        $arrItem['subtotalgramasi'] = $arrItem['qty'] * $arrItem['gramasi'];
        //$class->setLog($arrItem['qty'] * $arrItem['gramasi'],true);
        $totalWeight += $arrItem['subtotalgramasi'];
        
        // utk opsi ganti variasi
        
        //getItemVariationDetail
        $arrItem ['selVariant'] = '';
        if($arrItem['isvariant']){
            $rsItemVariant = $item->searchDataRow(array($item->tableName.'.pkey',$item->tableName.'.name') , 
                                                ' and '.$item->tableName.'.isvariant = 1 
                                                  and '.$item->tableName.'.statuskey = 1
                                                  and '.$item->tableName.'.parentkey ='. $item->oDbCon->paramString($arrItem['parentkey'])
                                            );

            $shipmentOptions=$class->generateComboboxOpt(array('data' => $rsItemVariant,'label' => 'name', 'value' => 'pkey'));
            $_POST['selVariant[]'] = $arrItem['pkey'];
            $arrItem ['selVariant'] =  $class->inputSelect('selVariant[]',$shipmentOptions); 
        }
        array_push($rsCartList, $arrItem);
        //array_push($arrTwigVar['hidItemKey'], $class->inputHidden('hidItemKey[]', array('value' => $arrItem['pkey'])));
        //array_push($arrTwigVar['hidItemWeight'], $class->inputHidden('hidItemWeight[]', array('value' => $arrItem['gramasi'])));
 
    } 
    
	// hitung point
	$pointNeeded = ($pointUnitValue > 0) ? ceil($subtotal/$pointUnitValue) : 0;
	$pointNeeded = ($pointNeeded > $eligiblePoint) ? $eligiblePoint : $eligiblePoint;
	
	$_POST['point'] = $pointNeeded;
	
	$pointDisc = $pointNeeded * $pointUnitValue * -1;
	$grandtotal = $subtotal + $pointDisc; 
	
	$arrTwigVar['pointValue'] = $pointDisc;
   // $voucherCriteria['totalsales'] = $subtotal;
	
}

$arrTwigVar['availableVoucher'] = $availableVoucher;

$totalWeight = $totalWeight / 1000;

$arrTwigVar['cartList'] = $rsCartList;
$arrTwigVar['subtotal'] = $subtotal;
$arrTwigVar['totalItem'] = $totalItem;


// nanti perlu diupdate kalo settingannya include
$tax = $class->getFrontEndTax($subtotal)['taxValue'];  
$arrTwigVar['tax'] = $tax;

$arrTwigVar['grandtotal'] = $grandtotal + $tax;
$arrTwigVar['totalWeight'] =  $totalWeight;

	
//$arrTwigVar['btnDeleteRows'] = $class->inputLinkButton('btnCartDeleteRows' , '<i class="fas fa-times"></i>', array('etc'=>'tabIndex="-1"','class' => 'btn btn-link mnv-delete-cart-row remove-button')); 

/*$arrTwigVar ['inputQty'] =  $class->inputNumber('qty[]');*/
$arrTwigVar ['inputDays'] =  $class->inputNumber('totalDays'); // untuk rental quotation
$arrTwigVar ['inputDiscount'] =  $class->inputHidden('discount');
$arrTwigVar ['inputShippingCost'] =  $class->inputNumber('shippingCost', array('readonly' => 'true')); 
$arrTwigVar ['inputName'] =  $class->inputText('recipientName'); 
$arrTwigVar ['labelName'] =  (isset($_POST['recipientName']) && !empty($_POST['recipientName'])) ? $_POST['recipientName'] : ''; 
$arrTwigVar ['inputPhone'] =  $class->inputText('recipientPhone'); 
$arrTwigVar ['labelPhone'] =  (isset($_POST['recipientPhone']) && !empty($_POST['recipientPhone'])) ? $_POST['recipientPhone'] : ''; 
$arrTwigVar ['inputEmail'] =  $class->inputText('recipientEmail'); 
$arrTwigVar ['labelEmail'] =  (isset($_POST['recipientEmail']) && !empty($_POST['recipientEmail'])) ? $_POST['recipientEmail'] : ''; 
$arrTwigVar ['inputAddress'] =   $class->inputTextArea('recipientAddress', array( 'etc' => 'style="height:10em"')); 
$arrTwigVar ['labelAddress'] =  (isset($_POST['recipientAddress']) && !empty($_POST['recipientAddress'])) ? str_replace(chr(13),'<br>',$_POST['recipientAddress']) : '';  

$arrTwigVar ['inputTrDesc'] =   $class->inputTextArea('recipientTrDesc', array( 'etc' => 'style="height:10em"')); 
$arrTwigVar ['labelTrDesc'] =  (isset($_POST['recipientTrDesc']) && !empty($_POST['recipientTrDesc'])) ? str_replace(chr(13),'<br>',$_POST['recipientTrDesc']) : '';  

$arrTwigVar ['inputZipcode'] =   $class->inputText('recipientZipcode');
$arrTwigVar ['labelZipcode'] =  (isset($_POST['recipientZipcode']) && !empty($_POST['recipientZipcode'])) ? $_POST['recipientZipcode'] : '';  
$arrTwigVar ['inputWeight'] =  $class->inputDecimal('totalWeight',array('value' => $class->formatNumber($totalWeight,2), 'readonly' => 'true'));
$arrTwigVar ['inputPoint'] =  $class->inputNumber('point',array('add-class'=>'label-style','etc' => 'style="text-align:right"')); 
$arrTwigVar ['btnSubmit'] =   $class->inputSubmit('btnSave',$lang->lang['proceedToPayment']); // untuk checkout manual

$arrTwigVar ['btnNext'] =   $class->inputButton('btnNext',$lang->lang['next']); 
$arrTwigVar ['inputMapAddress'] =  $class->inputText('mapAddress', array('class' => 'form-control search-address', 'etc' => 'placeholder="'.$class->lang['searchLocation'].'"')); 
$arrTwigVar ['hidLatLng'] = $class->inputHidden('hidLatLng', array('add-class' => 'hidLatLng'));

$arrTwigVar ['hidVoucherKey'] = $class->inputHidden('hidVoucherKey[]');  
$arrTwigVar ['hidVoucherType'] = $class->inputHidden('hidVoucherType[]'); 
$arrTwigVar ['hidVoucherCategoryKey'] = $class->inputHidden('hidVoucherCategoryKey[]');
	
$autoCompleteCity =  $class->inputAutoComplete(array(  
                                                            'element' => array('value' => 'hidRecipientCityName',
                                                                               'key' => 'hidRecipientCityKey'),
                                                            'source' =>array(
                                                                                'url' => 'ajax-city.php',
                                                                                'data' => array(  'action' =>'searchData' )
                                                                            ) ,
                                                            //'callbackFunction' => 'onChangeCity()', // biteship gk pake kota, jd nonaktifkan dulu
                                                            'explodeScript' => true
    
                                                          )
                                                    );  
 
$arrTwigVar ['inputCity']  = $autoCompleteCity['input'];
$arrTwigVar ['labelCity'] =  (isset($_POST['hidRecipientCityKey']) && !empty($_POST['hidRecipientCityKey'])) ? $_POST['hidRecipientCityName'] : ''; 
  
$criteria = '';
$criteria .= ' and '.$shipment->tableShipmentService.'.isdefault = 1'; 
// if(empty($_SESSION[$class->loginSession]))
//     $criteria .= ' and '.$shipment->tableShipmentService.'.issameday = 0';

$rsShipment = $shipment->getAllShipment('', '', $criteria);
$rsShipmentService = $class->reindexDetailCollections($rsShipment, 'pkey');

$arrServiceName = array();

foreach ($rsShipmentService as $pkey => $services) {
    foreach ($services as $service) {
        $arrServiceName[$service['pkey']] = [
            'servicekey'   => $service['servicekey'],
            'servicename'  => $service['servicename'],
            'needlocation' => $service['needlocation'],
        ];
    }
}

$shipmentOptions=$class->generateComboboxOpt(array('data' => $arrServiceName,'label' => 'servicename', 'value' => 'servicekey'),null,$lang->lang['chooseShippingService'],array('data-needlocation' => 'needlocation'));

$arrTwigVar['inputShipment'] = $class->inputSelect('selShipmentService', $shipmentOptions);
$arrTwigVar ['arrShipmentService'] = json_encode($arrServiceName);

//$arrTwigVar ['arrShipmentService'] =  json_encode($rsShipmentService);
$arrTwigVar ['showMapOnLoad'] = ($rsShipment[0]['needlocation']) ? true : false;

//$arrCourier = array_column($rsShipment,null,'pkey'); 
$rsCourier = $shipment->searchData($shipment->tableName.'.statuskey',1);

$arrCourier = $class->convertForCombobox($rsCourier,'pkey','name');   
$arrShipmentService = $class->convertForCombobox($rsShipmentService[$rsCourier[0]['pkey']],'servicekey','servicename');  

// asumsi bukan jenis voucher berdasarkan barang
//$useVoucherPoint = $class->loadSetting('transactionVoucherPoint');	
//if($useVoucherPoint == 1){
//	$rsVoucher = (!empty(USERKEY)) ? $voucherTransaction->getAvailableVoucher(USERKEY) : array();
//	$arrVoucher = $class->convertForCombobox($rsVoucher,'pkey','voucherlabel');   
//}

//$arrTwigVar ['inputCourier'] =   $class->inputSelect('selCourier', $arrCourier);
//$arrTwigVar ['inputShipment'] =   $class->inputSelect('selShipmentService', $arrShipmentService);
//$arrTwigVar ['inputVoucher'] =   $class->inputSelect('selVoucher', $arrVoucher);
$arrTwigVar ['inputInsurance'] =   $class->inputCheckBox('useInsurance');

$arrTwigVar ['JSScript']  = str_replace(array('<script type="text/javascript">','</script>'),array('',''),$autoCompleteCity['script']); 
 

// sementara, nanti bisa pilih alamat kirim
$arrTwigVar['recipientAddress'] = ((USERKEY != 0)) ? $rsUser[0]['address'] : '';

echo $twig->render('cart.html', $arrTwigVar);  
 
?>
