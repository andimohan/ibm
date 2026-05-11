<?php  
  $companyName = $class->loadSetting('companyName');
  $companyAddress = $class->loadSetting('companyAddress');
  $pdf->setCustomSettings(
    array(  
         'showPrintHeader' => false,
         'marginFooter' => 25,
         'footer' => '<div style="border-top:1px solid #999; clear:both;"></div><b><span  style=" font-size:9px; color:#002985 ">'.strtoupper($companyName).'</span></b><br><span style="color:#666;  font-size:9px; color:#002985; ">Operational Address : Gading Griya Lestari Blok C1 No. 29, RT 12/RW 5, Sukapura, Jakarta Utara. 14140.</span>', 
         ) 
  );  

$containerLimit = 10;
$invoiceType =  isset($_GET['type']) ? $_GET['type'] : 0;

$invoiceContent = function ($dataset,$opt){  
global $pdf;
    
$containerLimit = $opt['containerLimit'] ;
$invoiceType = $opt['invoiceType'];
    
$pdf->setListIndentWidth(5);

$obj = new TruckingServiceOrderInvoice();   
$truckingServiceOrder = new TruckingServiceOrder();    
$truckingServiceOrderCategory = new TruckingServiceOrderCategory();    
$truckingServiceWorkOrder = new TruckingServiceWorkOrder();
$customer = new Customer();
$consignee = new Consignee();
$cost = new Service(TRUCKING_SERVICE,1);
$customCode = new CustomCode();
$setting = new Setting();
$paymentMethod = new PaymentMethod();
$termOfPayment = new TermOfPayment();
$item = new Item();
$vessel  = new Vessel();
    
$rs = $dataset['rs'];
$printReimburse = (isset($_GET) && $_GET['selling'] == 1) ? false : true; 
        
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
<table cellpadding="2" style="font-size:9px;"> 
<tr><td><b>Bill To :</b></td><td></td><td></td></tr>
<tr>
    <td style="width:400px;">'.strtoupper($rsCustomer[0]['name'] ).'<div class="lite-color">'.str_replace(chr(13),'<br>',strtoupper($rsCustomer[0]['address'])).'</div></td>
    <td style="width:60px;"></td>
    <td style="width:270px;">
        <table cellpadding="2" >';
    
    if ($invoiceType != 1) {
        $html .=' <tr>
                <td class="lite-color" style="width:90px;">Order ID</td>
                <td style="width:110px; text-align:right;">'.$rsSO[0]['code'].'</td>
            </tr>';
    }
            
    $html .= ' <tr>
                <td class="lite-color" style="width:90px;">Due Date</td>
                <td style="width:110px; text-align:right;">'.$duedate.'</td>
            </tr>
            <tr>
                <td class="lite-color" style="width:90px;">Payment Method</td>
                <td style="width:110px; text-align:right;">'.(($rsTOP[0]['duedays'] > 0) ? 'Pay Later' : 'Pay Now').'</td>
            </tr>
        </table>
    </td>
</tr>
</table> 
<div style="border-bottom:1px solid #ccc; clear:both;"></div>';
      
if($invoiceType === 0) {
$html .='
<div style="clear:both;"></div>
<br>
<table cellpadding="2" style="font-size:0.9em">
<tr class="lite-color"><td style="width:90px">Document No.</td><td style="width:70px">Order Type</td><td style="width:80px">Stuffing Date</td><td style="width:110px">Ref. Code</td><td style="width:140px;">Container Quantity</td><td style="width:170px">Route</td></tr>  
';
}
        
    
$color = '#333';
$serviceJO = '<table cellpadding="2" style="font-size:0.9em">';
$serviceJO .= '<tr class="row-header"><td style="width:245px">Description</td><td style="width: 40px; text-align:right">VAT</td><td style="width: 40px; text-align:right">Qty</td><td  style="width: 35px;"></td><td style="text-align:right; width: 80px">Unit Price</td><td  style="width: 35px;"></td><td style="text-align:right; width: 80px">Disc Unit Price</td><td  style="width: 35px;"></td><td style="text-align:right">Amount</td></tr>';
    
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
    
for($i=0;$i<count($rsDetail);$i++){ 
    
    $description = $rsDetail[$i]['description']; 
    
    if (!empty($rsDetail[$i]['salesorderkey'])){  
        
        // ambil informasi JO pertama, asumsi hanya selalu ad 1 JO
        $rsSOHeader = $truckingServiceOrder->getDataRowById($rsDetail[$i]['salesorderkey']);
        

        $rsContainer = $truckingServiceOrder->getContainerDetail($rsDetail[$i]['salesorderkey']);
        foreach($rsContainer as $containerRow)
            if(!empty($containerRow['container']))
                array_push($arrContainer,$containerRow['container']);       
        
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
			if($rsInvoiceItemDetail[$j]['servicecost'] == 0) $hasTrucking = true;
			
            $itemkey = $rsInvoiceItemDetail[$j]['itemkey'];
            $qty = $rsInvoiceItemDetail[$j]['qtyinbaseunit'];
            
            // kalo tipenya reimburse, di LOGOOL patokannya kalo gk ad tax dianggap reimburse 
            if($rsInvoiceItemDetail[$j]['taxdetail'] == 0){
                $totalReimburseJo += $rsInvoiceItemDetail[$j]['aftertaxdetailvalue'];
                $itemname = (!empty($rsInvoiceItemDetail[$j]['aliasname'])) ? $rsInvoiceItemDetail[$j]['aliasname'] : $rsInvoiceItemDetail[$j]['itemname'];
                $itemname = trim(strtolower($itemname));
                $indexItem = $itemname;//.'-'.$rsInvoiceItemDetail[$j]['priceinunit'];
  
                if(!isset($arrInvoiceReimburse[$indexItem])){
					$arrInvoiceReimburse[$indexItem]['tax23'] = $rsInvoiceItemDetail[$j]['taxdetailvalue'];
					$arrInvoiceReimburse[$indexItem]['itemkey'] = $itemkey;
					$arrInvoiceReimburse[$indexItem]['itemname'] = $itemname;
					$arrInvoiceReimburse[$indexItem]['total'] = $rsInvoiceItemDetail[$j]['aftertaxdetailvalue'];
				}else{
					$arrInvoiceReimburse[$indexItem]['total'] += $rsInvoiceItemDetail[$j]['aftertaxdetailvalue'];
				} 
            }else{
//                if($rsInvoiceItemDetail[$j]['taxdetail']>0){
                      $indexTax = $obj->formatNumber($rsInvoiceItemDetail[$j]['taxdetail']);
                      if(!isset($arrServicePPN[$indexTax])) $arrServicePPN[$indexTax] = 0; 
                      $arrServicePPN[$indexTax] +=  $rsInvoiceItemDetail[$j]['taxdetailvalue'];
//                }

                $priceUnit = ($rsInvoiceItemDetail[$j]['ispriceincludetax'] == 1) ? ($rsInvoiceItemDetail[$j]['priceinunit'] - ($rsInvoiceItemDetail[$j]['taxdetailvalue'] / $rsInvoiceItemDetail[$j]['qtyinbaseunit'])) : $rsInvoiceItemDetail[$j]['priceinunit']; // kalo pajak include
                
                // gk boleh pake item key, karena ad alias
                $itemname = (!empty($rsInvoiceItemDetail[$j]['aliasname'])) ? $rsInvoiceItemDetail[$j]['aliasname'] : $rsInvoiceItemDetail[$j]['itemname'];
                $itemname = trim(strtolower($itemname));
				$indexItem = $itemname.'-'.$rsInvoiceItemDetail[$j]['priceinunit'].'-'. $rsInvoiceItemDetail[$j]['taxdetail'];
                
                $discountValueRow = $obj->getDiscountValue($priceUnit, $rsInvoiceItemDetail[$j]['discountdetailvalue'],$rsInvoiceItemDetail[$j]['discountdetailtype']);
                $subtotal = $qty * ($priceUnit- $discountValueRow);
                

                $tax23 = 0;
                if ($rsInvoiceItemDetail[$j]['istax23'] == 1) {
                    $tax23 = $rsInvoiceItemDetail[$j]['beforetaxdetailvalue'] * 2 /100;
                }
                
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
                    $arrInvoiceItem[$indexItem]['tax23'] =  $tax23;
                     
                }else{ 
                    $arrInvoiceItem[$indexItem]['discountdetailvalue'] += $discountValueRow;
                    $arrInvoiceItem[$indexItem]['taxdetailvalue'] += $rsInvoiceItemDetail[$j]['taxdetailvalue'];
                    $arrInvoiceItem[$indexItem]['qty'] += $qty;
                    $arrInvoiceItem[$indexItem]['tax23'] += $tax23;
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
            } 
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
 
if($printReimburse){
    if(!empty($arrInvoiceReimburse))  $serviceReimburse .=  '<tr> <td  colspan="7"><b><br>Reimbursement</b></td></tr>'; 
    foreach($arrInvoiceReimburse as $row){
                    $serviceReimburse .= '<tr class="lite-color" >
                    <td colspan="7">' .strtoupper($row['itemname']).'</td> 
                    <td style="text-align:right;">Rp.</td>
                    <td style="text-align:right;">'.$obj->formatNumber($row['total']).'</td>
                    <td></td>
                    </tr>';
    }

    $serviceReimburse .= '<tr class="lite-color"  style=" font-weight:bold">
    <td colspan="7" style=" border-top:1px solid #ccc;" >Subtotal Reimbursement</td> 
    <td style="text-align:right;  border-top:1px solid #ccc; ">Rp.</td>
    <td style="text-align:right;  border-top:1px solid #ccc; " >'.$obj->formatNumber($totalReimburseJo).'</td>
    <td></td>
    </tr>';   
}else{
    $totalReimburseJo = 0; // reset jd 0 agar tdk kejumlah
}

	
if(!empty($arrInvoiceItem)) $serviceJO .=  '<tr> <td  colspan="7"><b><br>Service</b></td></tr>';
    
$totalPPN = 0;
$arrVat  = array();
$totalpph23 = 0;
foreach($arrInvoiceItem as $row){	 
        $serviceJO .=  '<tr class="lite-color">
        <td style="">'.$row['itemname'].'</td>
        <td style=" text-align:right">'.$obj->formatNumber($row['ppn']).'%</td>
        <td style=" text-align:right">'.$obj->formatNumber($row['qty'],0).'</td> 
        <td style="text-align:right; ">Rp.</td>
        <td style="text-align:right; ">'.$obj->formatNumber($row['price']).'</td>
        <td style="text-align:right; ">Rp.</td>
        <td style="text-align:right; ">'.$obj->formatNumber($row['discountdetailvalue']).'</td>
        <td style="text-align:right; ">Rp.</td>
        <td style="text-align:right; ">'.$obj->formatNumber($row['subtotal']).'</td>  
        </tr>';
    
        if(!empty($row['ppn'])){ 
            if(!isset($arrVat[$row['ppn']])) $arrVat[$row['ppn']] = 0;
            $arrVat[$row['ppn']] += $row['taxdetailvalue'];
            $totalPPN += $row['taxdetailvalue'];
        }
        $totalpph23 += $row['tax23'];
}

foreach($arrVat as $key=>$row){	
    if($key == 0) continue;

    $serviceJO .=  '<tr class="lite-color" >
    <td style="">Vat '.$obj->formatNumber($key).'%</td>
    <td colspan="7"></td> 
    <td style="text-align:right; ">'.$obj->formatNumber($row).'</td>  
    </tr>';  
}

$serviceJO .=  '<tr class="lite-color" >
<td style="">PPH 23 </td>
<td colspan="7"></td> 
<td style="text-align:right; ">('.$obj->formatNumber($totalpph23).')</td>  
</tr>';  
    
$serviceJO .=   '<tr class="lite-color"  style=" font-weight:bold">
                <td style=" border-top:1px solid #ccc; border-bottom:1px solid #ccc;">Subtotal Service</td>
                <td style="border-top:1px solid #ccc; border-bottom:1px solid #ccc;" colspan="7"></td>
                <td style="border-top:1px solid #ccc; border-bottom:1px solid #ccc; text-align:right; ">'.$obj->formatNumber($totalServiceJo  + $totalPPN - $totalpph23).'</td>  
                </tr>';  
    

$bookingCode = array();
if(!empty($rsSOHeader[0]['donumber'])) array_push($bookingCode, $rsSOHeader[0]['donumber']);
if(!empty($rsSOHeader[0]['shipmentnumber'])) array_push($bookingCode, $rsSOHeader[0]['shipmentnumber']);
if($invoiceType == 0) {
$html .= '<tr class="logol-color"><td>'.implode('<br>',$bookingCode).'</td><td>'.implode('<br>',$arrJobType).'</td><td>'.$obj->formatDBDate($rsSOHeader[0]['lastwodate']).'</td><td >'.$rsSOHeader[0]['poreference'].'</td><td>'.strtoupper($containerQtyInformation).'</td><td >'.implode('<br>',$routeInformation).'</td></tr>';
}if($totalReimburseJo>0)  $serviceJO .= $serviceReimburse;   

$serviceJO .= '</table>';

if($invoiceType == 0) {
$html .= '</table>';
}

$html .= '<br><br>'.$serviceJO;
$html .= '<div style="clear:both"></div>';

//$arrStuffingDate =  array_unique($arrStuffingDate);
//sort($arrStuffingDate);
        
$containerNumber = (count($arrContainer) <= $containerLimit) ? implode(' ', $arrContainer ) : 'For more container number please see appendix';
    
if($rsPaymentMethod[0]['isvirtualaccount'] == 1)
    $paymentChannel =  $rsPaymentMethod[0]['name'] . ' ' .$rs[0]['vanumber'];
else
    $paymentChannel = $rsPaymentMethod[0]['bankname'].' Cabang '.$rsPaymentMethod[0]['branch'].'<br>'.$rsPaymentMethod[0]['bankaccountname'].' '.$rsPaymentMethod[0]['bankaccountnumber'];

    
$grandTotal = $totalServiceJo+$totalReimburseJo+$totalPPN-$totalpph23; 
$html .= '<table cellpadding="2" style="width:665px; font-size:0.9em">
<tr  style="background-color:#f4f8fe" class="logol-color"><td style="width:567px; text-align:right">Grand Total&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td><td style="width:25px">Rp.</td><td style="text-align:right; width:72px" >'.$obj->formatNumber($grandTotal).'</td></tr>
<tr> <td colspan="3" class="lite-color" style="text-align:right"><br><br>'.str_replace('And', 'and',ucwords($obj->sayNumberInEnglish($grandTotal))).' Rupiah</td> </tr>
</table>
';
    
$html .= ' 
<br><br>
<span class="lite-color">Container Number</span>
<div class="logol-color">'.$containerNumber.'</div>

<br><span class="lite-color">Account Info</span> 
<div class="logol-color">'.$paymentChannel.'</div>
'.$trnotes;
  
$html .= '<div style="clear:both"></div>';
$html .= '<div style="clear:both"></div>';

$footerInvoice = $obj->loadSetting('invoiceFooter'); 
    
$html .= '<table style="font-size:9px; ">
    <tr>
        <td style="width:440px" class="lite-color">'.$footerInvoice.'</td>
        <td style="width:40px"></td> 
        <td  class="lite-color" style="width:190px; text-align:center">
                Jakarta, '.$obj->formatDBDate($rs[0]['trdate'],'d F Y').'
                <br><br>
                <img src="/personalized/envilog.wintera.co.id/img/ttd.jpg" style="width:150px">
                <br>
                <div class="logol-color">Billing Team</div>
        </td>
    </tr>   
</table>'; 
    
return $html;
};

$containerContent = function ($dataset,$opt){  
global $pdf;
    
$containerLimit = $opt['containerLimit'] ;
$arrContainer = $opt['arrContainer'] ;
$invoiceType = $opt['invoiceType'];
    
$obj = new TruckingServiceOrderInvoice();   
$truckingServiceOrder = new TruckingServiceOrder();    
$truckingServiceOrderCategory = new TruckingServiceOrderCategory();    
$truckingServiceWorkOrder = new TruckingServiceWorkOrder();
$customer = new Customer();
$consignee = new Consignee();
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
    
$img = '';// HTTP_HOST.'phpthumb/phpThumb.php?src='.$obj->phpThumbURLSrc.'setting/companyLogo/'.$profileImg.'&w=200&h=50&hash='.getPHPThumbHash($profileImg);
    
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
<table cellpadding="2" style="font-size:9px"> 
<tr><td><b>Bill To :</b></td><td></td><td></td></tr>
<tr>
    <td style="width:400px;">'.strtoupper($rsCustomer[0]['name'] ).'<div class="lite-color">'.str_replace(chr(13),'<br>',strtoupper($rsCustomer[0]['address'])).'</div></td>
    <td style="width:60px;"></td>
    <td style="width:270px;">
        <table cellpadding="2" >';
    
    if ($invoiceType != 1) {
        $html .=' <tr>
                <td class="lite-color" style="width:90px;">Order ID</td>
                <td style="width:110px; text-align:right;">'.$rsSO[0]['code'].'</td>
            </tr>';
    }
            
    $html .= ' <tr>
                <td class="lite-color" style="width:90px;">Due Date</td>
                <td style="width:110px; text-align:right;">'.$duedate.'</td>
            </tr>
            <tr>
                <td class="lite-color" style="width:90px;">Payment Method</td>
                <td style="width:110px; text-align:right;">'.(($rsTOP[0]['duedays'] > 0) ? 'Pay Later' : 'Pay Now').'</td>
            </tr>
        </table>
    </td>
</tr>
</table> 
<div style="border-bottom:1px solid #ccc; clear:both;"></div>';
      
if($invoiceType === 0) {
$html .='
<div style="clear:both;"></div>
<br>
<table cellpadding="2" style="font-size:0.9em">
<tr class="lite-color"><td style="width:90px">Document No.</td><td style="width:70px">Order Type</td><td style="width:80px">Stuffing Date</td><td style="width:110px">Ref. Code</td><td style="width:140px;">Container Quantity</td><td style="width:170px">Route</td></tr>  
';
}
        
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
            if($rsInvoiceItemDetail[$j]['taxdetail'] == 0){
             
            }else{
                
                // hitung total container
                if($rsItem[$itemkey]['servicecost'] == 0){
                    $qtyItemName = $rsInvoiceItemDetail[$j]['itemname'];
                    if(!isset($qtyContainer[$qtyItemName]))   $qtyContainer[$qtyItemName] = 0;  
                    $qtyContainer[$qtyItemName] += $qty;  // jgn pake alias, karena utk penamaan 20GP / 40 GP
                }       
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
 

  
$bookingCode = array();
if(!empty($rsSOHeader[0]['donumber'])) array_push($bookingCode, $rsSOHeader[0]['donumber']);
if(!empty($rsSOHeader[0]['shipmentnumber'])) array_push($bookingCode, $rsSOHeader[0]['shipmentnumber']);
     
if($invoiceType === 0) {    
$html .= '<tr class="logol-color"><td>'.implode('<br>',$bookingCode).'</td><td>'.implode('<br>',$arrJobType).'</td><td>'.$obj->formatDBDate($rsSOHeader[0]['lastwodate']).'</td><td >'.$rsSOHeader[0]['poreference'].'</td><td>'.strtoupper($containerQtyInformation).'</td><td >'.implode('<br>',$routeInformation).'</td></tr>';
$html .= '</table>'; 
$html .= '<div style="clear:both"></div>';
$html .= '<div style="clear:both"></div>';
}
    
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
array_push($generateReportContent , array('content' => $invoiceContent, 'param' => array('containerLimit' => $containerLimit, 'invoiceType' => $invoiceType)));

// container
// detail item
 
$arrContainer = array();
$truckingServiceWorkOrder = new TruckingServiceWorkOrder();
$truckingServiceOrder = new TruckingServiceOrder();    

$rsDetail = $obj->getDetailById($pdf->dataset['rs'][0]['pkey']);
for($i=0;$i<count($rsDetail);$i++){ 
     
    if (!empty($rsDetail[$i]['salesorderkey'])){  
          $rsSPK =  $truckingServiceWorkOrder->searchDataRow(
            array($truckingServiceWorkOrder->tableName.'.pkey', $truckingServiceWorkOrder->tableName.'.stuffingdatetime'),
            ' and '.$truckingServiceWorkOrder->tableName.'.statuskey in (2,3)
            and '.$truckingServiceWorkOrder->tableName.'.refkey = ' . $obj->oDbCon->paramString($rsDetail[$i]['salesorderkey']).' order by '.$truckingServiceWorkOrder->tableName.'.stuffingdatetime asc'
          );
        
        $rsContainer = $truckingServiceOrder->getContainerDetail($rsDetail[$i]['salesorderkey']);
        foreach($rsContainer as $containerRow)
            if(!empty($containerRow['container']))
                array_push($arrContainer,$containerRow['container']); 


       /*  foreach($rsSPK as $spkRow){  
            $rsContainer = $truckingServiceWorkOrder->getCarDetail($spkRow['pkey']);
            foreach($rsContainer as $containerRow)
                if(!empty($containerRow['container']))
                    array_push($arrContainer,$containerRow['container']); 

        }*/

    }
}
  

$jobOrderListContent = function ($dataset,$opt){  
global $pdf;
    
$invoiceType = $opt['invoiceType'];

$obj = new TruckingServiceOrderInvoice();   
$truckingServiceOrder = new TruckingServiceOrder();    
$truckingServiceOrderCategory = new TruckingServiceOrderCategory();    
$truckingServiceWorkOrder = new TruckingServiceWorkOrder();
$customer = new Customer();
$consignee = new Consignee();
$paymentMethod = new PaymentMethod();
$termOfPayment = new TermOfPayment();
$item = new Item();
    
$rs = $dataset['rs'];


$rsDetail = $obj->getDetailWithRelatedInformation($rs[0]['pkey']);
$rsDetailPayment = $obj->getDataRowById($rs[0]['pkey']);
$rsCustomer = $customer->getDataRowById($rs[0]['customerkey']);
$rsTOP = $termOfPayment->getDataRowById($rs[0]['termofpaymentkey']);
$rsPaymentMethod = $paymentMethod->getDataRowById($rs[0]['companybankkey']);    

$duedate = date('d M Y', strtotime('+'.$rsTOP[0]['duedays'].' days', strtotime($rs[0]['trdate'])));

$isDownpayment = $rs[0]['isdownpayment'];

$arrSOKeys = array_column($rsDetail,'salesorderkey');
$rsSO = $truckingServiceOrder->searchData('','',true, ' and ' . $truckingServiceOrder->tableName.'.pkey in ('.$obj->oDbCon->paramString($arrSOKeys,',').')');
$rsSOCols = $obj->reindexDetailCollections($rsSO,'pkey');


$trnotes = '<br><span class="lite-color">Notes</span>';
$trnotes .= (!empty($rs[0]['trdesc'])) ?  '<div class="logol-color">'.str_replace(chr(13),'<br>',$rs[0]['trdesc']).'</div>' : '-';

$profileImg = $obj->loadSetting('companyLogo');
     
$img =  HTTP_HOST.'download.php?filename=setting/companyLogo/'.$profileImg;
  
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
                <td class="lite-color" style="width:250px; text-align:right;">'.$draft.'<br>'.$rs[0]['code'].'</td>
            </tr>   
        </table>
    </td>
</tr>  
</table>
<div style="border-bottom:1px solid #ccc; clear:both;"></div>
';
$html .= '<br>
<table cellpadding="2" style="font-size:9px"> 
<tr><td  colspan="4"><b>Bill To :</b></td></tr>
<tr>
    <td rowspan="2" style="width:400px;">'.strtoupper($rsCustomer[0]['name'] ).'<div class="lite-color">'.str_replace(chr(13),'<br>',strtoupper($rsCustomer[0]['address'])).'</div></td>
    <td  rowspan="2"  style="width:70px;"></td>
    <td class="lite-color" style="width:90px;">Due Date</td>
    <td style="width:110px; text-align:right;">'.$duedate.'</td>
</tr>
<tr><td class="lite-color" >Payment Method</td><td style=" text-align:right; font-weight:bold">'.(($rsTOP[0]['duedays'] > 0) ? 'Pay Later' : 'Pay Now').'</td></tr>
</table> 
<div style="border-bottom:1px solid #ccc; clear:both;"></div>
<div style="clear:both;"></div>';


$html .='
<br>
<table cellpadding="2" style="font-size:0.9em">
    <tr class="row-header">
        <td style="text-align:center;width:40px">NO</td>
        <td style="text-align:center;width:190px">JOB NUMBER</td>
        <td style="text-align:center;width:155px">NOMOR AJU</td>
        <td style="text-align:center;width:160px">NOMOR REFERENSI</td>
        <td style="text-align:center;width:130px">TANGGAL JOB</td>
    </tr>  
';
for($i=0;$i<count($rsDetail);$i++) {
    $rsSOCol = $rsSOCols[$rsDetail[$i]['salesorderkey']];
    $html .='<tr class="lite-color">
        <td style="text-align:center;">'.($i+1).'</td>
        <td style="text-align:center;">'.$rsDetail[$i]['socode'].'</td>
        <td style="text-align:center;">'.($rsSOCol[0]['aju'] ?? '-').'</td>
        <td style="text-align:center;">'.($rsSOCol[0]['poreference'] ?? '-').'</td>
        <td style="text-align:center;">'.$obj->formatDBDate($rsDetail[$i]['sodate'],'d / m / Y').'</td>
    </tr>';
}
$html.='</table>';


return $html;
};


if(count($arrContainer) > $containerLimit)
    array_push($generateReportContent , array('content' => $containerContent, 'newGroup' => true, 'param' => array('containerLimit' => $containerLimit, 'arrContainer' => $arrContainer,'invoiceType' => $invoiceType)));

if($invoiceType == 1)  
    array_push($generateReportContent , array('content' => $jobOrderListContent, 'newGroup' => true, 'param' => array('invoiceType' => $invoiceType)));


?>