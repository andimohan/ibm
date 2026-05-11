<?php  
  $companyName = $class->loadSetting('companyName');
  $companyAddress = $class->loadSetting('companyAddress');
  $pdf->setCustomSettings(
    array(  
         'showPrintHeader' => false,
         'marginFooter' => 25,
         'footer' => '<div style="border-top:1px solid #999; clear:both;"></div><b><span  style=" font-size:9px; color:#002985 ">'.strtoupper($companyName).'</span></b><br><span style="color:#666;  font-size:9px; color:#002985; ">Residence Address : Rukan Gading Bukit Indah TB No. 6 Jl. Gading Kirana Raya, Kelapa Gading. 14240. Telp: (021) 453 4049</span><br><span style="color:#666; color:#002985;  font-size:9px; ">Operational Address : Graha Boulevard, Blok C No. 21 Komplek, Jl. Boulevard Raya, RW 1 Kelapa Gading. 14240. Telp: (021) 29375245</span>', 
         ) 
  );  

$containerLimit = 10;

$invoiceContent = function ($dataset,$opt){  
global $pdf;
    
$containerLimit = $opt['containerLimit'] ;
    
$pdf->setListIndentWidth(5);

$obj = new TruckingServiceOrderInvoice();   
$truckingServiceOrder = new TruckingServiceOrder();    
$truckingServiceOrderCategory = new TruckingServiceOrderCategory();    
$truckingServiceWorkOrder = new TruckingServiceWorkOrder();
$customer = new Customer(); 
$cost = new Service(TRUCKING_SERVICE,1);
$customCode = new CustomCode();
$setting = new Setting();
$paymentMethod = new PaymentMethod();
$termOfPayment = new TermOfPayment();
$item = new Item(); 
$customCode = new CustomCode();
    
$rs = $dataset['rs'];
	
$rsInvoiceType = $customCode->searchData($customCode->tableName.'.pkey',$rs[0]['customcodekey'], true);

        
$rsDetail = $obj->getDetailById($rs[0]['pkey']);
$rsDetailPayment = $obj->getDataRowById($rs[0]['pkey']);
$rsCustomer = $customer->getDataRowById($rs[0]['customerkey']);
$rsTOP = $termOfPayment->getDataRowById($rs[0]['termofpaymentkey']);
$rsPaymentMethod = $paymentMethod->getDataRowById($rs[0]['companybankkey']);
    
$duedate = date('d M Y', strtotime('+'.$rsTOP[0]['duedays'].' days', strtotime($rs[0]['trdate'])));
    
$isDownpayment = $rs[0]['isdownpayment'];
$rsSO = $truckingServiceOrder->getDataRowById($rsDetail[0]['salesorderkey']);
 
$trnotes = (!empty($rs[0]['trdesc'])) ?  '<br><span class="lite-color">Notes</span><div class="logol-color">'.str_replace(chr(13),'<br>',$rs[0]['trdesc']).'</div>' : '';

$profileImg = $obj->loadSetting('companyLogo');
    
//$img = HTTP_HOST.'phpthumb/phpThumb.php?src='.$obj->phpThumbURLSrc.'setting/companyLogo/'.$profileImg.'&w=200&h=50&hash='.getPHPThumbHash($profileImg);
$img = HTTP_HOST.'download.php?filename=setting/companyLogo/'.$profileImg;
    
$proforma = ($rs[0]['statuskey'] == 1) ? '<div style="font-weight:normal; font-size:0.9em">(PROFORMA)</div>' : '';
$html = $obj->printSetting['defaultStyle'];
$html .= '<style>
.lite-color {color:#666}
.row-header td {background-color:#e3e8ed; color: #002985} 
.logol-color {color:#002985}
</style>';
    
// khsusu damco
$draft = ($rs[0]['statuskey'] == 1 && $rs[0]['customerkey'] != 92) ? '<span>PROFORMA</span>' : '';
$customerAddress  = ( $rs[0]['customerkey'] == 48 ) ? '' : str_replace(chr(13),'<br>',strtoupper($rsCustomer[0]['address']));     
    
$tableWidth = '670';    
$html .= '<table >
<tr>
    <td style="width:415px;">
        <table cellpadding="3" style=""> 
            <tr>
                <td style="vertical-align:middle; width:110px" ><img src="'.$img.'"></td>
                <td style="width: 280px;"></td>
            </tr>
        </table>
    </td> 
    <td style="width:255px; text-align:right">
        <table cellpadding="2" style="width:250px;">  
            <tr><td style="text-align:right;"><div style="font-size:2em; font-weight:bold;">INVOICE</div></td></tr>   
            <tr> 
                <td class="lite-color" style="width:250px; text-align:right;">'.$draft.'<br>'.$rs[0]['code'].'</td>
            </tr>   
        </table></td>
</tr>  
</table>
<div style="border-bottom:1px solid #ccc; clear:both;"></div>
';
$html .= '<br>
<table cellpadding="2" style="font-size:0.9em"> 
<tr><td  colspan="4"><b>Bill To :</b></td></tr>
<tr><td rowspan="3" style="width:400px;">'.strtoupper($rsCustomer[0]['name'] ).'<div class="lite-color">'.$customerAddress.'</div></td><td  rowspan="3"  style="width:70px;"></td><td class="lite-color" style="width:90px;">Order ID</td><td style="width:110px; text-align:right;">'.$rsSO[0]['code'].'</td></tr>
<tr><td class="lite-color" >Due Date</td><td style="text-align:right">'.$duedate.'</td></tr>
<tr><td class="lite-color" >Payment Method</td><td style=" text-align:right; font-weight:bold">'.$rsTOP[0]['name'].'</td></tr>
</table> 
<div style="border-bottom:1px solid #ccc; clear:both;"></div>
<div style="clear:both;"></div>';
      
$html .='
<br>
<table cellpadding="2" style="font-size:0.9em">
<tr class="lite-color">
<td style="width:90px">Document No.</td>
<td style="width:70px">Order Type</td>
<td style="width:80px">Stuffing Date</td>
<td style="width:110px">Ref. Code</td>
<td style="width:140px;">Container Quantity</td>
<td style="width:170px">Route</td>
</tr>  
';
        
    
$color = '#333';
$serviceJO = '<table cellpadding="2" style="font-size:0.9em;">';
$serviceJO .= '<tr class="row-header"><td style="width:400px">Description</td><td style="width: 40px; text-align:right">Qty</td><td  style="width: 35px;"></td><td style="text-align:right; width: 80px">Unit Price</td><td  style="width: 35px;"></td><td style="text-align:right; width: 80px">Amount</td></tr>';

$arrServicePPN = array();
$totalServiceJo = 0;
$totalReimburseJo = 0;
$serviceReimburse = ''; 
    
$arrContainer = array();
$arrStuffingDate = array();
$qtyContainer = array();
$routeInformation = array();
$arrInvoiceItem = array();
$arrInvoiceReimburse = array();
    
$hasTrucking = false;
	
$arrJobType = array();    
$totalContainerInJOHeader = 0;

$arrSOKey = array_column($rsDetail, 'salesorderkey');

$rsWorkOrder = $truckingServiceWorkOrder->searchData('', '', true, ' and ' . $truckingServiceWorkOrder->tableName.'.refkey in ('. $obj->oDbCon->paramString($arrSOKey,',') .')');
    
if(!empty($rsWorkOrder)) {
    foreach($rsWorkOrder as $workOrder) {
        if (!empty($workOrder['containernumber'])) {
            array_push($arrContainer, $workOrder['containernumber']);
        }
        if (!empty($workOrder['container2number'])) {
            array_push($arrContainer, $workOrder['container2number']);
        }
    }
}

for($i=0;$i<count($rsDetail);$i++){ 

    $description = $rsDetail[$i]['description']; 
    
    if (!empty($rsDetail[$i]['salesorderkey'])){  
        
        // ambil informasi JO pertama, asumsi hanya selalu ad 1 JO
        $rsSOHeader = $truckingServiceOrder->getDataRowById($rsDetail[$i]['salesorderkey']);
        
        // $rsContainer = $truckingServiceOrder->getContainerDetail($rsDetail[$i]['salesorderkey']);

        // foreach ($rsContainer as $containerRow) {
        //     if (!empty($containerRow['container']))
        //         array_push($arrContainer, $containerRow['container']);
        // }



        // job type
        $rsTruckingOrderCategory = $truckingServiceOrderCategory->getDataRowById($rsSOHeader[0]['categorykey']);
        if(!in_array($rsTruckingOrderCategory[0]['name'],$arrJobType))
            array_push($arrJobType,$rsTruckingOrderCategory[0]['name']);
        
        $route = strtoupper($rsSOHeader[0]['routefrom']).' - '.strtoupper($rsSOHeader[0]['routeto']);
        if(!in_array($route,$routeInformation))
            array_push($routeInformation, $route);
         
        // detail item
        $rsInvoiceItemDetail = $obj->getItemDetail($rsDetail[$i]['pkey']);

        
        $arrItemKey = array_column($rsInvoiceItemDetail, 'itemkey');
        $rsItem = $item->searchDataRow( array($item->tableName.'.pkey', $item->tableName.'.reimburse', $item->tableName.'.servicecost') , 
                                ' and '.$item->tableName.'.pkey in ('.$obj->oDbCon->paramString($arrItemKey,',').')'  
                       ); 
        
        $rsItem = array_column($rsItem,null,'pkey');  
            
        for($j=0;$j<count($rsInvoiceItemDetail);$j++){
               
			if($rsInvoiceItemDetail[$j]['servicecost'] == 0) 
                $hasTrucking = true;
			
            $itemkey = $rsInvoiceItemDetail[$j]['itemkey'];
            $qty = $rsInvoiceItemDetail[$j]['qtyinbaseunit'];
            
            //di AMT nggak ada REIMBURSE
            // kalo tipenya reimburse, di LOGOOL patokannya kalo gk ad tax dianggap reimburse 
            // if($rsInvoiceItemDetail[$j]['taxdetail'] == 0){
            //     $totalReimburseJo += $rsInvoiceItemDetail[$j]['aftertaxdetailvalue'];
            //     $itemname = (!empty($rsInvoiceItemDetail[$j]['aliasname'])) ? $rsInvoiceItemDetail[$j]['aliasname'] : $rsInvoiceItemDetail[$j]['itemname'];
            //     $itemname = trim(strtolower($itemname));
            //     $indexItem = $itemname;//.'-'.$rsInvoiceItemDetail[$j]['priceinunit'];
  
            //     if(!isset($arrInvoiceReimburse[$indexItem])){
			// 		$arrInvoiceReimburse[$indexItem]['tax23'] = $rsInvoiceItemDetail[$j]['taxdetailvalue'];
			// 		$arrInvoiceReimburse[$indexItem]['itemkey'] = $itemkey;
			// 		$arrInvoiceReimburse[$indexItem]['itemname'] = $itemname;
			// 		$arrInvoiceReimburse[$indexItem]['total'] = $rsInvoiceItemDetail[$j]['aftertaxdetailvalue'];
			// 	}else{
			// 		$arrInvoiceReimburse[$indexItem]['total'] += $rsInvoiceItemDetail[$j]['aftertaxdetailvalue'];
			// 	} 
            // }else{
                //if($rsInvoiceItemDetail[$j]['taxdetail']>0){
                    $indexTax = $obj->formatNumber($rsInvoiceItemDetail[$j]['taxdetail']);
                    if(!isset($arrServicePPN[$indexTax])) $arrServicePPN[$indexTax] = 0; 
                    $arrServicePPN[$indexTax] +=  $rsInvoiceItemDetail[$j]['taxdetailvalue'];
                //}

                $priceUnit = ($rsInvoiceItemDetail[$j]['ispriceincludetax'] == 1) ? ($rsInvoiceItemDetail[$j]['priceinunit'] - ($rsInvoiceItemDetail[$j]['taxdetailvalue'] / $rsInvoiceItemDetail[$j]['qtyinbaseunit'])) : $rsInvoiceItemDetail[$j]['priceinunit']; // kalo pajak include
                
                // gk boleh pake item key, karena ad alias
                $itemname = (!empty($rsInvoiceItemDetail[$j]['aliasname'])) ? $rsInvoiceItemDetail[$j]['aliasname'] : $rsInvoiceItemDetail[$j]['itemname'];
                $itemname = trim(strtolower($itemname));
				$indexItem = $itemname.'-'.$rsInvoiceItemDetail[$j]['priceinunit'].'-'. $rsInvoiceItemDetail[$j]['taxdetail'];
                
                $discountValueRow = $obj->getDiscountValue($priceUnit, $rsInvoiceItemDetail[$j]['discountdetailvalue'],$rsInvoiceItemDetail[$j]['discountdetailtype']);
                $subtotal = $qty * ($priceUnit- $discountValueRow);
                
                if(!isset($arrInvoiceItem[$indexItem])){
                    $arrInvoiceItem[$indexItem]['itemkey'] = $itemkey;
                    $arrInvoiceItem[$indexItem]['itemname'] = strtoupper($itemname);
                    $arrInvoiceItem[$indexItem]['price'] = $priceUnit;
                    $arrInvoiceItem[$indexItem]['discountdetailvalue'] = $discountValueRow ;
                    $arrInvoiceItem[$indexItem]['discountdetailtype'] = $rsInvoiceItemDetail[$j]['discountdetailtype'] ; 
                    $arrInvoiceItem[$indexItem]['ppn'] = $rsInvoiceItemDetail[$j]['taxdetail'];
                    $arrInvoiceItem[$indexItem]['taxdetailvalue'] = $rsInvoiceItemDetail[$j]['taxdetailvalue'];
                    $arrInvoiceItem[$indexItem]['qty'] = $qty;
                    $arrInvoiceItem[$indexItem]['subtotal'] =  $subtotal;
                     
                }else{ 
                    $arrInvoiceItem[$indexItem]['discountdetailvalue'] += $discountValueRow;
                    $arrInvoiceItem[$indexItem]['taxdetailvalue'] += $rsInvoiceItemDetail[$j]['taxdetailvalue'];
                    $arrInvoiceItem[$indexItem]['qty'] += $qty;
                    $arrInvoiceItem[$indexItem]['subtotal'] += $subtotal;
                     
                }
 
//                $totalServiceJo += ($qty * ($priceUnit-$arrInvoiceItem[$indexItem]['discountdetailvalue']));
                
                 $totalServiceJo += $subtotal;
            
                
                // hitung total container
                if($rsItem[$itemkey]['servicecost'] == 0){
                    $qtyItemName = $rsInvoiceItemDetail[$j]['itemname'];
                    if(!isset($qtyContainer[$qtyItemName]))   $qtyContainer[$qtyItemName] = 0;  
                    $qtyContainer[$qtyItemName] += $qty;  // jgn pake alias, karena utk penamaan 20GP / 40 GP
                }       
            //} 
        }  
        
        $rsSPK =  $truckingServiceWorkOrder->searchDataRow(
                array($truckingServiceWorkOrder->tableName.'.pkey', $truckingServiceWorkOrder->tableName.'.stuffingdatetime'),
                ' and '.$truckingServiceWorkOrder->tableName.'.statuskey in (2,3)
                and '.$truckingServiceWorkOrder->tableName.'.refkey = ' . $obj->oDbCon->paramString($rsDetail[$i]['salesorderkey']).' order by '.$truckingServiceWorkOrder->tableName.'.stuffingdatetime asc'
         );
            
         foreach($rsSPK as $spkRow){
			 if($hasTrucking){
/*
				$rsContainer = $truckingServiceWorkOrder->getCarDetail($spkRow['pkey']);
				foreach($rsContainer as $containerRow)
					if(!empty($containerRow['container']))
						array_push($arrContainer,$containerRow['container']); 

*/
			 }

            $stuffingdatetime = $obj->formatDBDate($spkRow['stuffingdatetime'],'d M Y');
            array_push($arrStuffingDate, $stuffingdatetime);

        }
            
			
		// KHUSUS AMT, KALO INV REIMBURSE TARIK ULANG INGORMASI TRUCKING DARI JO
		if (empty($qtyContainer)){
			$qtyContainer = array(); // reset, utk jaga2 format array 
			$rsJODetail = $truckingServiceOrder->getDetailWithRelatedInformation($rsSOHeader[0]['pkey']);
			
			foreach($rsJODetail as $joDetailRow){
				if(!isset($qtyContainer[$joDetailRow['itemname']])) $qtyContainer[$joDetailRow['itemname']] = 0;
				$qtyContainer[$joDetailRow['itemname']] += $joDetailRow['qtyinbaseunit'];
			}
				
		}
		
		
        // total container
        $containerQtyInformation = array();
        foreach($qtyContainer as $key=>$qty) 
            array_push( $containerQtyInformation , $qty.'x '.$key);
         
        $containerQtyInformation = implode('<br>',$containerQtyInformation);
      
        // hitugn total container per JO
        $rsContainerJO = $truckingServiceOrder->getContainerDetail($rsSOHeader[0]['pkey']);
        $totalContainerInJOHeader += count($rsContainerJO);
            
    } 
    
}   
    
        
// kalo gk ad spk    
if(empty($containerQtyInformation)){
    $containerQtyInformation = $totalContainerInJOHeader . ' Container'; 
}
 

// if(!empty($arrInvoiceReimburse))  $serviceReimburse .=  '<tr> <td  colspan="7"><b><br>Reimbursement</b></td></tr>';
    
// foreach($arrInvoiceReimburse as $row){
// 				$serviceReimburse .= '<tr class="lite-color" >
//                 <td colspan="7">' .strtoupper($row['itemname']).'</td> 
//                 <td style="text-align:right;">Rp.</td>
//                 <td style="text-align:right;">'.$obj->formatNumber($row['total']).'</td>
//                 <td></td>
//                 </tr>';
// }
    
// $serviceReimburse .= '<tr class="lite-color"  style=" font-weight:bold">
// <td colspan="7" style=" border-top:1px solid #ccc;" >Subtotal Reimbursement</td> 
// <td style="text-align:right;  border-top:1px solid #ccc; ">Rp.</td>
// <td style="text-align:right;  border-top:1px solid #ccc; " >'.$obj->formatNumber($totalReimburseJo).'</td>
// <td></td>
// </tr>';

if(!empty($arrInvoiceItem)) 
    $serviceJO .=  '<tr> <td  colspan="7"><b><br>Service</b></td></tr>';
    
//$totalPPN = $rs[0]['taxvalue'];
//$arrVat  = array();
foreach($arrInvoiceItem as $row){	 
        $serviceJO .=  '<tr class="lite-color">
        <td style="">'.$row['itemname'].'</td>
        <td style=" text-align:right">'.$obj->formatNumber($row['qty'],0).'</td> 
        <td style="text-align:right; ">Rp.</td>
        <td style="text-align:right; ">'.$obj->formatNumber($row['price']).'</td>
        <td style="text-align:right; ">Rp.</td>
        <td style="text-align:right; ">'.$obj->formatNumber($row['subtotal']).'</td>  
        </tr>';
    
//        if(!empty($row['ppn'])){ 
//            if(!isset($arrVat[$row['ppn']])) $arrVat[$row['ppn']] = 0;
//            $arrVat[$row['ppn']] += $row['taxdetailvalue'];
//            $totalPPN += $row['taxdetailvalue'];
//        }
}

//foreach($arrVat as $key=>$row){	
//    if($key == 0) continue;
//
//    $serviceJO .=  '<tr class="lite-color" >
//    <td style="">Vat '.$obj->formatNumber($key).'%</td>
//    <td colspan="7"></td> 
//    <td style="text-align:right; ">'.$obj->formatNumber($row).'</td>  
//    </tr>';  
//}
//    
	
//	  $serviceJO .=  '<tr class="lite-color" >
//    <td style="">Vat '.$obj->formatNumber($key).'%</td>
//    <td colspan="7"></td> 
//    <td style="text-align:right; ">'.$obj->formatNumber($row).'</td>  
//    </tr>';  
	
//$serviceJO .=   '<tr class="lite-color"  style=" font-weight:bold">
//                <td style=" border-top:1px solid #ccc; border-bottom:1px solid #ccc;">Subtotal Service</td>
//                <td style="border-top:1px solid #ccc; border-bottom:1px solid #ccc;" colspan="4"></td>
//                <td style="border-top:1px solid #ccc; border-bottom:1px solid #ccc; text-align:right; ">'.$obj->formatNumber($totalServiceJo).'</td>  
//                </tr>';  
    

$bookingCode = array();
if(!empty($rsSOHeader[0]['donumber'])) array_push($bookingCode, $rsSOHeader[0]['donumber']);
if(!empty($rsSOHeader[0]['shipmentnumber'])) array_push($bookingCode, $rsSOHeader[0]['shipmentnumber']);
$html .= '<tr class="logol-color"><td>'.implode('<br>',$bookingCode).'</td><td>'.implode('<br>',$arrJobType).'</td><td>'.$obj->formatDBDate($rsSOHeader[0]['lastwodate']).'</td><td >'.$rsSOHeader[0]['poreference'].'</td><td>'.strtoupper($containerQtyInformation).'</td><td >'.implode('<br>',$routeInformation).'</td></tr>';

if($totalReimburseJo>0)  $serviceJO .= $serviceReimburse;   

$serviceJO .= '</table>';
     
$html .= '</table>';
$html .= '<br><br>'.$serviceJO;
$html .= '<div style="clear:both"></div>';

//$arrStuffingDate =  array_unique($arrStuffingDate);
//sort($arrStuffingDate);
        
$containerNumber = (count($arrContainer) <= $containerLimit) ? implode(' ', $arrContainer ) : 'For more container number please see appendix';
    
if($rsPaymentMethod[0]['isvirtualaccount'] == 1)
    $paymentChannel =  $rsPaymentMethod[0]['name'] . ' ' .$rs[0]['vanumber'];
else
    $paymentChannel = $rsPaymentMethod[0]['bankname'].' Cabang '.$rsPaymentMethod[0]['branch'].'<br>'.$rsPaymentMethod[0]['bankaccountname'].' '.$rsPaymentMethod[0]['bankaccountnumber'];

$totalPPN = $rs[0]['taxvalue'];    
$grandTotal = $totalServiceJo+$totalReimburseJo+$totalPPN; 
	
if (isset($_GET) && !empty($_GET['rounding']))
    $grandTotal = ceil($grandTotal / 10) * 10;

	
$subtotalRow = '';	
if($rsInvoiceType[0]['isreimburse'] == 0){
	$subtotalRow = '
<tr class="logol-color"><td style="width:155px; text-align:right">Subtotal</td><td style="width: 10px"></td><td style="width:25px">Rp.</td><td style="text-align:right; width:77px" >'.$obj->formatNumber($totalServiceJo).'</td></tr>
<tr class="logol-color"><td style="text-align:right">Vat '.$obj->formatNumber($rs[0]['taxpercentage'],2).'%</td><td ></td><td>Rp.</td><td style="text-align:right" >'.$obj->formatNumber($totalPPN).'</td></tr>
<tr class="logol-color"><td style="text-align:right">Grand Total</td><td ></td><td>Rp.</td><td style="text-align:right;" >'.$obj->formatNumber($grandTotal).'</td></tr>
';
}else{
	$subtotalRow = '<tr class="logol-color"><td style="width:155px; text-align:right">Grand Total</td><td style="width: 10px"></td><td style="width:25px">Rp.</td><td style="text-align:right; width:77px" >'.$obj->formatNumber($totalServiceJo).'</td></tr>'; 
}

$html .= '
<table cellpadding="2" style=" font-size:0.9em;  border-top:1px solid #dedede">
	<tr>
		<td style="width: 380px"><span class="lite-color">Container Number</span>
			<div class="logol-color">'.$containerNumber.'</div>

			<br><span class="lite-color">Account Info</span> 
			<div class="logol-color">'.$paymentChannel.'</div>
			'.$trnotes.' 
		</td>
		<td style="width:22px"></td>
		<td style="width: 265px">
			<table cellpadding="2" >
				'.$subtotalRow.' 
				<tr> <td colspan="4" class="lite-color" style="text-align:right"><br><br>'.str_replace('And', 'and',ucwords($obj->sayNumberInEnglish($grandTotal))).' Rupiah</td> </tr>
			</table>
		</td>
	</tr>
</table>
';
  
$html .= '<div style="clear:both"></div>';
$html .= '<div style="clear:both"></div>';

$footerInvoice = $obj->loadSetting('invoiceFooter'); 
    
$html .= '<table style="font-size: .9em; ">
    <tr>
        <td style="width:440px" class="lite-color">'.$footerInvoice.'</td>
        <td style="width:40px"></td> 
        <td  class="lite-color" style="width:190px; text-align:center">
                Jakarta, '.$obj->formatDBDate($rs[0]['trdate'],'d F Y').'
                <br><br> <br> <br> 
                <br><br><br>
                <div class="logol-color">Billing Team</div>
        </td>
    </tr>   
</table>'; 
    
return '<div style="font-size:1.1em">'.$html.'</div>';
};

$containerContent = function ($dataset,$opt){  
global $pdf;
    
$containerLimit = $opt['containerLimit'] ;
$arrContainer = $opt['arrContainer'] ;

$obj = new TruckingServiceOrderInvoice();   
$truckingServiceOrder = new TruckingServiceOrder();    
$truckingServiceOrderCategory = new TruckingServiceOrderCategory();    
$truckingServiceWorkOrder = new TruckingServiceWorkOrder();
$customer = new Customer(); 
$paymentMethod = new PaymentMethod();
$termOfPayment = new TermOfPayment();
$item = new Item();
    
$rs = $dataset['rs'];
        
$rsDetail = $obj->getDetailById($rs[0]['pkey']);
$rsDetailPayment = $obj->getDataRowById($rs[0]['pkey']);
$rsCustomer = $customer->getDataRowById($rs[0]['customerkey']);
$rsTOP = $termOfPayment->getDataRowById($rs[0]['termofpaymentkey']);
$rsPaymentMethod = $paymentMethod->getDataRowById($rs[0]['companybankkey']);    

$duedate = date('d M Y', strtotime('+'.$rsTOP[0]['duedays'].' days', strtotime($rs[0]['trdate'])));
    
$isDownpayment = $rs[0]['isdownpayment'];
$rsSO = $truckingServiceOrder->getDataRowById($rsDetail[0]['salesorderkey']);
 
$trnotes = '<br><span class="lite-color">Notes</span>';
$trnotes .= (!empty($rs[0]['trdesc'])) ?  '<div class="logol-color">'.str_replace(chr(13),'<br>',$rs[0]['trdesc']).'</div>' : '-';

$profileImg = $obj->loadSetting('companyLogo');
    
$img = HTTP_HOST.'phpthumb/phpThumb.php?src='.$obj->phpThumbURLSrc.'setting/companyLogo/'.$profileImg.'&w=200&h=50&hash='.getPHPThumbHash($profileImg);
    
$proforma = ($rs[0]['statuskey'] == 1) ? '<div style="font-weight:normal; font-size:0.9em">(PROFORMA)</div>' : '';
$html = $obj->printSetting['defaultStyle'];
$html .= '<style>
.lite-color {color:#666}
.row-header td {background-color:#e3e8ed; color: #002985} 
.logol-color {color:#002985}
</style>';
    
// khsusu damco
$draft = ($rs[0]['statuskey'] == 1 && $rs[0]['customerkey'] != 92) ? '<span>PROFORMA</span>' : '';
     
    
$tableWidth = '670';    
$html .= '<table >
<tr>
    <td style="width:415px;">
        <table cellpadding="3" style=""> 
            <tr>
                <td style="vertical-align:middle; width:110px" ><img src="'.$img.'"></td>
                <td style="width: 280px;"></td>
            </tr>
        </table>
    </td> 
    <td style="width:255px; text-align:right">
        <table cellpadding="2" style="width:250px;">  
            <tr><td style="text-align:right;"><div style="font-size:2em; font-weight:bold;">INVOICE</div></td></tr>   
            <tr> 
                <td class="lite-color" style="width:250px; text-align:right;">'.$draft.'<br>'.$rs[0]['code'].'<div class="logol-color">#APPENDIX</div></td>
            </tr>   
        </table></td>
</tr>  
</table>
<div style="border-bottom:1px solid #ccc; clear:both;"></div>
';
$html .= '<br>
<table cellpadding="2" style="font-size:0.9em"> 
<tr><td  colspan="4"><b>Bill To :</b></td></tr>
<tr><td rowspan="3" style="width:400px;">'.strtoupper($rsCustomer[0]['name'] ).'<div class="lite-color">'.str_replace(chr(13),'<br>',strtoupper($rsCustomer[0]['address'])).'</div></td><td  rowspan="3"  style="width:70px;"></td><td class="lite-color" style="width:90px;">Order ID</td><td style="width:110px; text-align:right;">'.$rsSO[0]['code'].'</td></tr>
<tr><td class="lite-color" >Due Date</td><td style="text-align:right">'.$duedate.'</td></tr>
<tr><td class="lite-color" >Payment Method</td><td style=" text-align:right; font-weight:bold">'.$rsTOP[0]['name'].'</td></tr>
</table> 
<div style="border-bottom:1px solid #ccc; clear:both;"></div>
<div style="clear:both;"></div>';
      
$html .='
<br>
<table cellpadding="2" style="font-size:0.9em">
<tr class="lite-color"><td style="width:90px">Document No.</td><td style="width:70px">Order Type</td><td style="width:80px">Stuffing Date</td><td style="width:110px">Ref. Code</td><td style="width:140px;">Container Quantity</td><td style="width:170px">Route</td></tr>  
';
        
$arrStuffingDate = array();
$qtyContainer = array();
$routeInformation = array();
$arrInvoiceItem = array();
$arrInvoiceReimburse = array();
    
$hasTrucking = false;
$containerQtyInformation = 0;	
$arrJobType = array();    
for($i=0;$i<count($rsDetail);$i++){ 
      
    if (!empty($rsDetail[$i]['salesorderkey'])){  
        
        // ambil informasi JO pertama, asumsi hanya selalu ad 1 JO
        $rsSOHeader = $truckingServiceOrder->getDataRowById($rsDetail[$i]['salesorderkey']);
        
        // job type
        $rsTruckingOrderCategory = $truckingServiceOrderCategory->getDataRowById($rsSOHeader[0]['categorykey']);
        if(!in_array($rsTruckingOrderCategory[0]['name'],$arrJobType))
            array_push($arrJobType,$rsTruckingOrderCategory[0]['name']);
        
        $route = strtoupper($rsSOHeader[0]['routefrom']).' - '.strtoupper($rsSOHeader[0]['routeto']);
        if(!in_array($route,$routeInformation))
            array_push($routeInformation, $route);

          
        // detail item
        $rsInvoiceItemDetail = $obj->getItemDetail($rsDetail[$i]['pkey']);
   
        $arrItemKey = array_column($rsInvoiceItemDetail, 'itemkey');
        $rsItem = $item->searchDataRow( array($item->tableName.'.pkey', $item->tableName.'.reimburse', $item->tableName.'.servicecost') , 
                                ' and '.$item->tableName.'.pkey in ('.$obj->oDbCon->paramString($arrItemKey,',').')'  
                       ); 
        
        $rsItem = array_column($rsItem,null,'pkey'); 
        
        for($j=0;$j<count($rsInvoiceItemDetail);$j++){  
			   
            $itemkey = $rsInvoiceItemDetail[$j]['itemkey']; 
            $qty = $rsInvoiceItemDetail[$j]['qtyinbaseunit'];
            
            
            // kalo tipenya reimburse, di LOGOOL patokannya kalo gk ad tax dianggap reimburse
            // if($rsInvoiceItemDetail[$j]['taxdetail'] == 0){
             
            // }else{
                
                // hitung total container
                if($rsItem[$itemkey]['servicecost'] == 0){
                    $qtyItemName = $rsInvoiceItemDetail[$j]['itemname'];
                    if(!isset($qtyContainer[$qtyItemName]))   $qtyContainer[$qtyItemName] = 0;  
                    $qtyContainer[$qtyItemName] += $qty;  // jgn pake alias, karena utk penamaan 20GP / 40 GP
                }       
            //} 
       }  
     
        
        // total container
        $containerQtyInformation = array();

        foreach($qtyContainer as $key=>$qty) 
            array_push( $containerQtyInformation , $qty.'x '.$key);
        
        $containerQtyInformation = implode('<br>',$containerQtyInformation);
         
        // hitugn total container per JO
        $rsContainerJO = $truckingServiceOrder->getContainerDetail($rsSOHeader[0]['pkey']);
        $totalContainerInJOHeader += count($rsContainerJO);

    } 
    
}   
    
// kalo gk ad spk    
if(empty($containerQtyInformation)){
    $containerQtyInformation = $totalContainerInJOHeader . ' Container'; 
}
 

  
$bookingCode = array();
if(!empty($rsSOHeader[0]['donumber'])) array_push($bookingCode, $rsSOHeader[0]['donumber']);
if(!empty($rsSOHeader[0]['shipmentnumber'])) array_push($bookingCode, $rsSOHeader[0]['shipmentnumber']);
     
$html .= '<tr class="logol-color"><td>'.implode('<br>',$bookingCode).'</td><td>'.implode('<br>',$arrJobType).'</td><td>'.$obj->formatDBDate($rsSOHeader[0]['lastwodate']).'</td><td >'.$rsSOHeader[0]['poreference'].'</td><td>'.strtoupper($containerQtyInformation).'</td><td >'.implode('<br>',$routeInformation).'</td></tr>';
$html .= '</table>'; 
$html .= '<div style="clear:both"></div>';
$html .= '<div style="clear:both"></div>';
$html .= '<table cellpadding="6"><tr><td style="background-color:#f4f8fe">Container Number</td></tr>';
$html .= '<tr><td  class="logol-color">'.implode('&nbsp;&nbsp;&nbsp;',$arrContainer).'</td></tr>';
$html .= '</table>';    
    
if($rsPaymentMethod[0]['isvirtualaccount'] == 1)
    $paymentChannel =  $rsPaymentMethod[0]['name'] . ' ' .$rs[0]['vanumber'];
else
    $paymentChannel = $rsPaymentMethod[0]['bankname'].' Cabang '.$rsPaymentMethod[0]['branch'].'<br>'.$rsPaymentMethod[0]['bankaccountname'].' '.$rsPaymentMethod[0]['bankaccountnumber'];
    
return $html;
};

 
$generateReportContent = array();
array_push($generateReportContent , array('content' => $invoiceContent, 'param' => array('containerLimit' => $containerLimit)));

// container
// detail item
 
$arrContainer = array();
$truckingServiceWorkOrder = new TruckingServiceWorkOrder();
$truckingServiceOrder = new TruckingServiceOrder();    

$rsDetail = $obj->getDetailById($pdf->dataset['rs'][0]['pkey']);

$arrSOKey = array_column($rsDetail, 'salesorderkey');

$rsWorkOrder = $truckingServiceWorkOrder->searchData('', '', true, ' and ' . $truckingServiceWorkOrder->tableName.'.refkey in ('. $obj->oDbCon->paramString($arrSOKey,',') .')');
    
if(!empty($rsWorkOrder)) {
    foreach($rsWorkOrder as $workOrder) {
        if (!empty($workOrder['containernumber'])) {
            array_push($arrContainer, $workOrder['containernumber']);
        }
        if (!empty($workOrder['container2number'])) {
            array_push($arrContainer, $workOrder['container2number']);
        }
    }
}

for($i=0;$i<count($rsDetail);$i++){ 
     
    if (!empty($rsDetail[$i]['salesorderkey'])){  
          $rsSPK =  $truckingServiceWorkOrder->searchDataRow(
            array($truckingServiceWorkOrder->tableName.'.pkey', $truckingServiceWorkOrder->tableName.'.stuffingdatetime'),
            ' and '.$truckingServiceWorkOrder->tableName.'.statuskey in (2,3)
            and '.$truckingServiceWorkOrder->tableName.'.refkey = ' . $obj->oDbCon->paramString($rsDetail[$i]['salesorderkey']).' order by '.$truckingServiceWorkOrder->tableName.'.stuffingdatetime asc'
          );
        
        // $rsContainer = $truckingServiceOrder->getContainerDetail($rsDetail[$i]['salesorderkey']);
        // foreach($rsContainer as $containerRow)
        //     if(!empty($containerRow['container']))
        //         array_push($arrContainer,$containerRow['container']); 


       /*  foreach($rsSPK as $spkRow){  
            $rsContainer = $truckingServiceWorkOrder->getCarDetail($spkRow['pkey']);
            foreach($rsContainer as $containerRow)
                if(!empty($containerRow['container']))
                    array_push($arrContainer,$containerRow['container']); 

        }*/

    }
}
  

if(count($arrContainer) > $containerLimit)
    array_push($generateReportContent , array('content' => $containerContent, 'newGroup' => true, 'param' => array('containerLimit' => $containerLimit, 'arrContainer' => $arrContainer)));
 
?>