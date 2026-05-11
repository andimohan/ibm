<?php 

includeClass(array('EMKLOrderInvoice.class.php','Item.class.php', ));
$emklOrderInvoice = createObjAndAddToCol(new EMKLOrderInvoice());

$obj = $emklOrderInvoice; 
$customer = new Customer(); 

$companyName = '<span style="font-size:13px; font-weight:bold;">' . strtoupper($obj->loadSetting('companyName')) . '</span>';
$companyLogo = $obj->loadSetting('companyLogo');
$imgLetterhead =  PHPTHUMB_URL_PATH . 'setting/companyLogo/' . $companyLogo;
$logo = '<img src="' . $imgLetterhead . '" style="width:150px"/>';

$rsCustomer = $customer->getDataRowById($rs[0]['customerkey']);

$name= (!empty($rsCustomer[0]['alias'])) ?  $rsCustomer[0]['alias'] : $rsCustomer[0]['name'] ;
$invoiceTo = nl2br($rs[0]['invoiceaddress']);

$header = '<table width="680px"><tr>
            <td style="width:260px;">'. $logo .'<br>'.$companyName.'</td>
            <td style="width:79px;"></td>
            <td style="width:340px;">
                <table cellpadding="5">
                    <tr style="background-color:#0a3e62; color:#fff;">
                        <td>ALAMAT PENAGIHAN</td>
                    </tr>
                    <tr>
                        <td>'. $name .'<br>'.$invoiceTo.'</td>
                    </tr> 
                </table>
            </td>
            </tr></table>';

$pdf->setCustomSettings(
    array(
        'showPrintHeader' => true,
        'header' => $header,
        'footer' => $footer,
        'marginFooter' => '5px'
    )
);
 
$generateReportContent = function ($dataset){ 
 
$obj = new EMKLOrderInvoice(); 

$item = new Item();
//$service = new Service(SERVICE);
$emklJobOrder = new EMKLJobOrder();
$container = new Container();
$employee = new Employee();
$currency = new Currency();
$itemUnit = new ItemUnit();
$paymentMethod = new PaymentMethod();
		
$emklInvoiceOrderDetail = array(); 
$arrCurrency = $currency->searchData(); 
$arrCurrency = array_column($arrCurrency,'name','pkey'); 
$rsContainer = $container->searchData();
$rsContainer = array_column($rsContainer,'name','pkey');

$termOfPayment = new TermOfPayment();

$rs = $dataset['rs']; 

//$rsObjKey = $obj->getTableKeyAndObj($obj->tableName, array('key')); 
//$rsAR = $ar->searchData('','',true,' and reftabletype = '.$obj->oDbCon->paramString($rsObjKey['key']).' and '.$ar->tableName.'.refkey = '.$obj->oDbCon->paramString($rs[0]['pkey']).' and '.$ar->tableName.'.statuskey = 1');

$rsTOP = $termOfPayment->getDataRowById($rs[0]['termofpaymentkey']); 
$dueDate = date('d/m/Y', strtotime($rs[0]['trdate'] . ' +'.$rsTOP[0]['duedays'].' days'));//!empty($rsAR[0]['duedate']) ? $obj->formatDBDate($rsAR[0]['duedate'],'d/m/Y') : '';
$rsPaymentMethod = $paymentMethod->getDataRowById($rs[0]['companybankkey']);

$bankName = $rsPaymentMethod[0]['bankname'];
$branch = $rsPaymentMethod[0]['branch'];
$accountName = $rsPaymentMethod[0]['bankaccountname'];
$accountNumber = $rsPaymentMethod[0]['bankaccountnumber'];
$rsDetail = $obj->getDetailById($rs[0]['pkey']);

$arrDetailKey = array_column($rsDetail, 'pkey');

$rsCurrency = $currency->getDataRowById($rs[0]['currencykey']); 
$rsJobOrder = $emklJobOrder->searchData($emklJobOrder->tableName.'.pkey',$rsDetail[0]['refsalesorderheaderkey']); 

$rsInvoiceDetailCol = array();
foreach($rsDetail as $detailRow) 
    $rsInvoiceDetailCol[$detailRow['pkey']] = $obj->getItemDetail($detailRow['pkey']);

// detail ambil diawal karena perlu cari nilai tonase
$rsJobOrderDetail = $emklJobOrder->getDetailById($rsJobOrder[0]['pkey']); 
    
$polName = $rsJobOrder[0]['polname'];
$podName = $rsJobOrder[0]['podname'];
$etd = $obj->formatDBDate($rsJobOrder[0]['etdpol'],'d/m/Y');
$eta = $obj->formatDBDate($rsJobOrder[0]['etapod'],'d/m/Y');
$mblNumber = $rsJobOrder[0]['mblnumber'];
$vesselName = $rsJobOrder[0]['vesselname'];
$vesselNumber = $rsJobOrder[0]['vesselnumber'];
$currencyName = $rsCurrency[0]['name'];

$rsItemUnit = $itemUnit->searchData('','',true); // yg nonaktif jg harus keselect, karena bisa sja unitnya sudah nonaktif
$rsItemUnitCols = array_column($rsItemUnit,'name', 'pkey');

$taxPercentage = 0; //default
$sayNumber = $obj->sayNumber($rs[0]['grandtotal']);

$containerInformation = array();

// jika pekerjaan import/export
//if(in_array(  $rsJobOrder[0]['jobtypekey'], array(EMKL['jobType']['import'], EMKL['jobType']['exort']))){ 
   
//}
// asumsi hanya ada 1 JO per invoice
$rsVolume = $emklJobOrder->getDetailVolume($rsDetail[0]['refsalesorderheaderkey']);
foreach($rsVolume as $volumeRow) {
    if($volumeRow['qty'] <= 0) continue;
    array_push($containerInformation, $obj->formatNumber($volumeRow['qty'],0) .'x ' . $volumeRow['itemname']); 
}
    
if($rsJobOrderDetail[0]['weight'] > 0){ 
    $totalTonase = $obj->formatNumber($rsJobOrderDetail[0]['weight']);    
    array_push($containerInformation, $totalTonase. ' KG');
}
    
if($rsJobOrderDetail[0]['measurement'] > 0){ 
    $totalTonase = $obj->formatNumber($rsJobOrderDetail[0]['measurement']);    
    array_push($containerInformation, $totalTonase. ' CBM');
}
    
$invoiceLabel = ($rs[0]['statuskey'] == 1) ? 'PROFORMA INVOICE' : '';
    
$containerInformation  = implode(', ',$containerInformation);
    
$salesOrderNumber = (in_array($rsJobOrder[0]['jobtypekey'],array(EMKL['jobType']['import'], EMKL['jobType']['export']))) ? $mblNumber : $rsJobOrder[0]['ponumber'] ;
    
//$trnotes = (!empty($rs[0]['trdesc'])) ? '<div style="clear:both"></div><strong>Catatan :</strong> <br>' . str_replace(chr(13),'<br>',$rs[0]['trdesc']) : '';
$html = $obj->printSetting['defaultStyle'];

$html .= '<style>
                .header-cell{background-color: #0a3e62; color: #fff; border:1px solid #000; border-bottom:1px solid #fff; width: 90px} 
                .header-cell-last{border-bottom:1px solid #000}
                .header-cell-detail{background-color: #0a3e62; color: #fff;border-top:1px solid #000;border-bottom:1px solid #000; border-right:1px solid #fff;} 
                .cell-border{border:1px solid #333}
                .cell-border-ltb{border-left:1px solid #333;border-top:1px solid #333;border-bottom:1px solid #333;}
                .cell-border-rtb{border-right:1px solid #333;border-top:1px solid #333;border-bottom:1px solid #333;}
                .header-cell-info{width: 134px}
        </style>';
$html .= '<div style="clear:both"></div>';
 
    
$html .= '<table cellpadding="4" style="font-size:1.5em"><tr><td class="header-cell header-cell-last cell-border" style="width: 130px; text-align:center">Nomor Faktur</td><td class="cell-border" style="width: 184px; text-align:center">'.$rs[0]['code'].'</td><td style="text-align:center; width: 385px; ">'.$invoiceLabel.'</td></tr></table>'; 
$html .= '<div></div>';
$html .= '<table  style="width: 680px;"  cellpadding="4">
<tr>
    <td class="header-cell cell-border">Tanggal Faktur</td><td class="header-cell-info cell-border">'.$obj->formatDBDate($rs[0]['trdate'],'d/m/Y').'</td>
    <td class="header-cell cell-border">POL</td><td class="header-cell-info cell-border">'.$polName.'</td>
    <td class="header-cell cell-border">ETD</td><td class="header-cell-info cell-border">'.$etd.'</td> 
</tr>
<tr>
    <td class="header-cell cell-border">Jatuh Tempo</td><td class="cell-border">'.$dueDate.'</td>
    <td class="header-cell cell-border">POD</td><td class="cell-border">'.$podName.'</td>
    <td class="header-cell cell-border">ETA</td><td class="cell-border">'.$eta.'</td> 
</tr>
<tr>
    <td class="header-cell cell-border">Internal Ref</td><td class="cell-border">'.$rsJobOrder[0]['code'].'</td>
    <td class="header-cell cell-border">MBL</td><td class="cell-border">'.$mblNumber.'</td>
    <td class="header-cell cell-border">Muatan</td><td class="cell-border">'.$containerInformation.'</td> 
</tr>
<tr>
    <td class="header-cell cell-border">Sales Order</td><td class="cell-border">'.$salesOrderNumber.'</td>
    <td class="header-cell cell-border">Tujuan Akhir</td><td class="cell-border"></td>
    <td class="header-cell cell-border">Tanggal</td><td class="cell-border"></td> 
</tr>
<tr>
    <td class="header-cell header-cell-last cell-border">Kapal</td><td class="cell-border">'.$vesselName.'</td>
    <td class="header-cell header-cell-last cell-border">Voyage</td><td class="cell-border">'.$vesselNumber.'</td>
    <td class="header-cell header-cell-last cell-border"></td><td class="cell-border"></td> 
</tr>
</table>';
    
    
$html .= '
<div></div>
<table style="width: 680px;" cellpadding="4">
    <tr>
        <th class="header-cell-detail" style="width:277px; border-left:1px solid #000">Deskripsi</th>
        <th class="header-cell-detail" style="width:85px; text-align:right">Kuantitas</th> 
        <th class="header-cell-detail" style="width:90px;">Satuan</th> 
        <th class="header-cell-detail" style="width:110px; text-align:right">Harga</th>
        <th class="header-cell-detail" style="width:110px; text-align:right; border-right:1px solid #000">Harga '.$currencyName.'</th>
    </tr>
';
    
    
for($i=0;$i<count($rsDetail);$i++){
     
    //$rsInvoiceDetail = $obj->getItemDetail($rsDetail[$i]['pkey']);
    $rsInvoiceDetail = $rsInvoiceDetailCol[$rsDetail[$i]['pkey']];
    
    if(empty($rsInvoiceDetail)) continue;
    
    for($j=0; $j<count($rsInvoiceDetail);$j++){
        
        if($rsInvoiceDetail[$j]['taxdetail'] > 0) $taxPercentage = $rsInvoiceDetail[$j]['taxdetail'];
        
        $rate = ($rs[0]['currencykey'] == CURRENCY['idr'] ) ? 1 : $rs[0]['rate'] ; 
        $rsItemUnitCol =  $rsItemUnitCols[$rsInvoiceDetail[$j]['unitkey']] ;
        
        $bgColor = ($j % 2 == 0) ? '#ffffff' : '#dedede' ;
        $lastData = ($j == count($rsInvoiceDetail) -1) ? 'border-bottom: 1px solid #000;' : '' ;
         
        $itemName = (!empty($rsInvoiceDetail[$j]['aliasname'])) ? $rsInvoiceDetail[$j]['aliasname'] : $rsInvoiceDetail[$j]['itemname'];
        $html .='  
                <tr style="background-color: '.$bgColor.';">
                    <td style="text-align:left; border-left: 1px solid #000; border-right: 1px solid #000;'.$lastData.'">'.$itemName.'</td>
                    <td style="text-align:right; border-left: 1px solid #000; border-right: 1px solid #000;'.$lastData.'">'.$obj->formatNumber($rsInvoiceDetail[$j]['qtyinbaseunit'], 3).'</td>
                    <td style="text-align:left; border-left: 1px solid #000; border-right: 1px solid #000;'.$lastData.'">'.$rsItemUnitCol.'</td> 
                    <td style="text-align:right; border-left: 1px solid #000; border-right: 1px solid #000;'.$lastData.'">'.$obj->formatNumber($rsInvoiceDetail[$j]['priceinunit'], 2).'</td>
                    <td style="text-align:right; border-left: 1px solid #000; border-right: 1px solid #000; '.$lastData.'">'.$obj->formatNumber($rsInvoiceDetail[$j]['beforetaxdetailvalue'], 2).'</td>
                </tr>
            '; 
    }
}  
$html .= '</table>';    
    
    
$bankAccountTable = '<table>
                        <tr><td style="width: 90px; font-weight:bold">Bank</td><td>'.$bankName.'</td></tr>
                        <tr><td style="font-weight:bold">Atas Nama</td><td>'.$accountName.'</td></tr>
                        <tr><td style="font-weight:bold">No. Rekening</td><td>'.$accountNumber.'</td></tr>
                        <tr><td style="font-weight:bold">Cabang</td><td>'.$branch.'</td></tr>
                    </table>';
$totalTable = '<table cellpadding="4">
                <tr>
                    <td class="header-cell cell-border" >Subtotal</td><td class="cell-border-ltb" style="width:40px; text-align:center">'.$currencyName.'</td><td class="cell-border-rtb" style="width:110px; text-align:right">'.$obj->formatNumber($rs[0]['beforetaxtotal'],2).'</td>
                </tr>
                <tr>
                    <td class="header-cell cell-border" >PPN '.$obj->formatNumber($taxPercentage,-2).'%</td><td class="cell-border-ltb" style="text-align:center">'.$currencyName.'</td><td class="cell-border-rtb" style="text-align:right">'.$obj->formatNumber($rs[0]['taxvalue'],2).'</td>
                </tr>
                <tr>
                    <td class="header-cell header-cell-last cell-border" >Total</td><td class="cell-border-ltb" style="text-align:center">'.$currencyName.'</td><td class="cell-border-rtb" style="text-align:right">'.$obj->formatNumber($rs[0]['grandtotal'],2).'</td>
                </tr>
            </table>';
    
$html .= '<div></div><table>
            <tr>
            <td style="width: 432px"><b>Terbilang</b><br>'.ucwords($sayNumber).' '.$currencyName.'<br><br>'.$bankAccountTable.'</td>
            <td style="width: 250px">'.$totalTable.'</td>
            </tr>
        </table>';    
    
return $html;
}

?>