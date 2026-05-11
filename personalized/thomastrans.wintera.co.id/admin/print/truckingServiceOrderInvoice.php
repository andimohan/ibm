<?php
$companyName = $class->loadSetting('companyName');
$companyAddress = $class->loadSetting('companyAddress');
$pdf->setCustomSettings(
    array(
        'showPrintHeader' => false,  
        'marginFooter' => 25,
        'pdfMarginHeader' => 5, 
        'footer' => '<div style="border-top:1px solid #000; font-size:9px; color: #15386b"><br><b>' . strtoupper($companyName) . '</b><br>Head Office: Genova Asya Commercial Area, Unit GA-037 Jl. Lake Garden Boulevard Jakarta Garden City, Cakung Timur, Telp (021) 388 64272<br>Operational Address: Jl. Semper Kebantenan No. 10A, Semper Timur, Cilincing, Jakarta Utara. 14130 Telp: (021) 440 4175 / (021) 441 4839
        <br>website: www.thomastrans.co.id</div>',
    )
);

$invoiceContent = function ($dataset) {

    $obj = new TruckingServiceOrderInvoice();
    $truckingServiceOrder = new TruckingServiceOrder();
    $truckingServiceOrderCategory = new TruckingServiceOrderCategory();
    $truckingServiceWorkOrder = new TruckingServiceWorkOrder();
    $customer = new Customer();
    $consignee = new Consignee();
    $cost = new Service(TRUCKING_SERVICE, 1);
    $customCode = new CustomCode();
    $termOfPayment = new TermOfPayment();
    $employee = new Employee();

    $rs = $dataset['rs'];

    $useSign = (isset($_GET) && $_GET['sign'] == 1) ? true : false;

    $useConsignee = ($rs[0]['invoiceto'] == 2) ? true : false;
    $useNotify = ($rs[0]['usenotify'] == 1) ? true : false;
    $useNotifyConsignee = ($rs[0]['invoicenotify'] == 2) ? true : false;

    $autoSign = ($useSign) ? '<br><br><img src="/personalized/yellowegg.wintera.co.id/img/ttd.jpg" style="width:150px">' : '';


    $rsDetail = $obj->getDetailById($rs[0]['pkey']);
    $jobOrderKey = array_column($rsDetail, 'salesorderkey');

    $rsItemDetail = $obj->getItemDetail($rs[0]['pkey'], 'refheaderkey');
    $rsItemDetailCols = $obj->reindexDetailCollections($rsItemDetail, 'refkey');
    
    $rsJobOrder = $truckingServiceOrder->searchData('','', true, ' and ' . $truckingServiceOrder->tableName.'.pkey in ('. $obj->oDbCon->paramString($jobOrderKey,',') .') ');
    $poReference = array_column($rsJobOrder,'poreference');
    $poReference = (!empty($poReference)) ? implode(',' , $poReference) : '-';
    
    $rsJobOrderCols = $obj->reindexDetailCollections($rsJobOrder, 'pkey');

    $rsJobOrderDetail = $truckingServiceOrder->getDetailWithRelatedInformation($jobOrderKey);
    $rsJobOrderDetailCols = $obj->reindexDetailCollections($rsJobOrderDetail, 'pkey');

    $rsDetailPayment = $obj->getDataRowById($rs[0]['pkey']);
    $rsCustomer = $customer->getDataRowById($rs[0]['customerkey']);
    $rsTOP = $termOfPayment->getDataRowById($rs[0]['termofpaymentkey']);
    $rsInvoiceType = $customCode->searchData($customCode->tableName . '.pkey', $rs[0]['customcodekey'], true);

    $notify = '';

    $realCustomerName = (!empty($rsCustomer[0]['alias'])) ? $rsCustomer[0]['alias'] : $rsCustomer[0]['name'];
    $realCustomerAddress = $rsCustomer[0]['address'];

    if ($useConsignee) {
        $customerName = $rs[0]['invoiceconsigneename'];
        $customerAddress = nl2br($rs[0]['invoiceconsigneeaddress']);

    } else {
        $customerName = $realCustomerName;
        $customerAddress = $realCustomerAddress;
    }

    if ($useNotify) {
        if ($useNotifyConsignee) {
            $notifyName = $rs[0]['invoiceconsigneenotifyname'];
            $notifyAddress = nl2br($rs[0]['invoiceconsigneenotifyaddress']);

        } else {
            $notifyName = $realCustomerName;
            $notifyAddress = $realCustomerAddress;
        }

        $notify = '<br><br><b>Notify</b><br>';
        $notify .= $notifyName . '<br>' . $notifyAddress;

    }


    //$duedate = date('Y-m-d', strtotime('+'.$rsTOP[0]['duedays'].' days', strtotime($rs[0]['trdate'])));

    $invoiceTitle = (!empty($rsInvoiceType[0]['title'])) ? $rsInvoiceType[0]['title'] : $rsInvoiceType[0]['name'];
    $isReimburse = ($rsInvoiceType[0]['isreimburse'] == 1) ? true : false;
    $proforma = ($rs[0]['statuskey'] == 1) ? 'PROFORMA ' : '';

    $profileImg = $obj->loadSetting('companyLogo');
    $img =  HTTP_HOST . 'download.php?filename=setting/companyLogo/' . $profileImg;
    
    $html = $obj->printSetting['defaultStyle'];
    
    $html .= ' 
    <style>.brand-color{color: #15386b}</style>
<table  style="border-bottom:1px solid black; "> 
<tr>
    <td style="width:415px;">
        <table > 
            <tr>
                <td style="vertical-align:middle; width:110px" ><img src="' . $img . '"></td>
                <td style="width: 280px;"></td>
            </tr>
        </table>
    </td> 
    <td style="width:255px; text-align:right">
        <table cellpadding="2" style="width:250px;">  
            <tr><td style="text-align:right;" class="brand-color" style="font-size:2em; font-weight:bold;">'.$proforma.strtoupper($invoiceTitle).'</td></tr>   
            <tr> 
                <td style="width:250px; text-align:right;">' . $rs[0]['code'] . '</td>
            </tr>   
        </table></td>
</tr>  
</table>
';

$html .= '<br><br>
<table>
<tr>
    <td style="width:410px"><table cellpadding="2">
        <tr><td class="brand-color"><b>Kepada :</b></td></tr>
        <tr><td style="width:380px;">' . $customerName . '<div class="lite-color">' . str_replace(chr(13), '<br>', $customerAddress) .$notify. '</div></td></tr>
        <tr><td></td></tr>';
    
if ($rsCustomer[0]['tintypekey'] == TIN_TYPE['TIN']){
    $html .= '<tr><td style="width:80px">NPWP 16 Digit</td><td style="width:10px">:</td><td style="width:300px">'. $rsCustomer[0]['taxid'] .'</td></tr>
            <tr><td style="width:80px">NITKU</td><td style="width:10px">:</td><td style="width:300px">' . $rsCustomer[0]['taxid'] . ' ' . $rsCustomer[0]['tku'] . '</td></tr>';    
}else if ($rsCustomer[0]['tintypekey'] == TIN_TYPE['NIK']){
      $html .= '<tr><td style="width:80px">NIK</td><td style="width:10px">:</td><td style="width:300px">'. $rsCustomer[0]['nik'] .'</td></tr>';    

}
    
$html .= '</table></td>
    <td><table cellpadding="2">
    <tr><td style="width:115px;font-weight:bold;" class="brand-color">Tgl. Invoice</td><td style="width:10px;text-align:center;">:</td><td style="width:135px">' . $obj->formatDBDate($rs[0]['trdate'], 'd / m / Y') . '</td></tr>
    <tr><td style="font-weight:bold;" class="brand-color">Referensi PO</td><td style="text-align:center;">:</td><td >' .$poReference . '</td></tr>
    <tr><td style="font-weight:bold;" class="brand-color" >Termin Pembayaran</td style="text-align:center;"><td>:</td><td>' . $rsTOP[0]['name'] . '</td></tr>
    </table></td>
</tr>
</table>

';

$html .='
<div style="clear:both;"></div>
<table cellpadding="2" class="table-transaction" >
    <tr class="col-header brand-color">
        <td style="text-align:center;width:60px">'.ucwords($obj->lang['party']) .'</td>
        <td style="width:130; ">' . ucwords($obj->lang['service']) . '</td>
        <td style="width:90px; text-align:center ">' . ucwords($obj->lang['date']) . '</td>
        <td style="width:174px;">' . ucwords($obj->lang['route']) . '</td>
        <td style="text-align:center;width:20px;"></td>
        <td style="text-align:right;width:70px; ">' . ucwords($obj->lang['price']) . '</td>
        <td style="text-align:right;width:40px;"></td>
        <td style="text-align:right;width:80px;">' . ucwords($obj->lang['amount']) . '</td></tr>  
';

    for ($i = 0; $i < count($rsDetail); $i++) {

        $rsItemDetailCol = $rsItemDetailCols[$rsDetail[$i]['pkey']];
        $rsJobOrderCol = $rsJobOrderCols[$rsDetail[$i]['salesorderkey']];
        
        if ($i==0)
            
            $html .= '<tr><td colspan="8"></td></tr>';
        
        foreach ($rsItemDetailCol as $itemDetail) {

            $rsJobOrderDetailCol = $rsJobOrderDetailCols[$itemDetail['refsodetailkey']];

            $itemName = (empty($itemDetail['aliasname']) ? $itemDetail['itemname'] : $itemDetail['aliasname']);

            $route = $rsJobOrderCol[0]['routefrom'] . ' - ' . $rsJobOrderCol[0]['routeto'];

            $html .= '<tr>
                <td style="text-align:center">'. $obj->formatNumber($itemDetail['qtyinbaseunit']) .'</td>
                <td >'. $itemName .'</td>
                <td style=" text-align:center">'. $obj->formatDBDate($rsJobOrderCol[0]['trdate'], 'd / m / Y') .'</td>
                <td>'. $route .'</td>
                <td style="text-align:center;">Rp</td>
                <td style="text-align:right;">'. $obj->formatNumber($itemDetail['priceinunit']) .'</td>
                <td style="text-align:center;">Rp</td>
                <td style="text-align:right;">'. $obj->formatNumber($itemDetail['total']) .'</td>
            </tr>';
            if (!empty($rsJobOrderDetailCol[0]['trdesc'])) {
                $html .= '<tr><td></td><td colspan="3">' . $rsJobOrderDetailCol[0]['trdesc'] . '</td><td colspan="4"></td></tr>';
            }
            
            $html .= '<tr><td colspan="8"></td></tr>';
        }



    }

$html .='
</table>
';

  
    $arrSubtotal = array();
    $ctr = 1;

    $subtotal = $rs[0]['subtotal'];
    $percentage = 12 / 100;
    $fixedValue = 11 / 12;
    $PPN = ($subtotal * $fixedValue) * $percentage;

    
    // kalo customer gk munculin pph, diset 0 biar gk nongol
    if ($rsCustomer[0]['displaytax23ininvoice'] != 1)  $rs[0]['tax23value'] = 0;
    
    if ($rs[0]['totaldownpayment'] > 0){
        array_push($arrSubtotal, '<tr><td class="brand-color" style="text-align:left; font-weight:bold;">'.ucwords($obj->lang['downpayment']).'</td><td style="text-align:center;">Rp</td><td style="text-align:right; width:80px">('.$obj->formatNumber($rs[0]['totaldownpayment']).')</td></tr>'); 
        $ctr += 1;
    } 

    if(!$isReimburse){
        if($rs[0]['tax23value'] > 0){
            array_push($arrSubtotal, '<tr><td class="brand-color" style="text-align:left; font-weight:bold;">PPH 23</td><td style="text-align:center;">Rp</td><td style="text-align:right; width:80px"  >('.$obj->formatNumber($rs[0]['tax23value']).')</td></tr>');
            $ctr++;

        }
    
        array_push($arrSubtotal, '<tr><td style="text-align:left; font-weight:bold;" class="brand-color">PPN</td><td style="text-align:center;">Rp</td><td style="text-align:right; width:80px"  >'.$obj->formatNumber($PPN).'</td></tr>');
        array_push($arrSubtotal, '<tr><td style="text-align:left; font-weight:bold;" class="brand-color">PPN dibebaskan</td><td style="text-align:center; ">Rp</td><td style="text-align:right; width:80px"  >('.$obj->formatNumber($PPN).')</td></tr>');
        $ctr += 2; 
    }
    
    $subtotalLabel = (!empty($arrSubtotal)) ? ucwords($obj->lang['subtotal']) : ucwords($obj->lang['total']);

    // $amount = $rs[0]['outstanding'] - $rs[0]['taxvalue'];
    $amount = $rs[0]['outstanding'] - $rs[0]['tax23value'];
    $sayNumber = ($amount == 0) ? 'Nol' : $obj->sayNumber($amount);



    $html .= '    
</table>  
<div style="clear:both"></div> 
<table cellpadding="2" > 
<tr><td rowspan="' . $ctr . '" style="width:445px" ><strong class="brand-color">Terbilang</strong> :<br>' . ucwords($sayNumber) . ' Rupiah.</td> 
<td style="text-align:left; font-weight:bold;  width:100px; " class="brand-color">' . $subtotalLabel . '</td><td style="width:40px;text-align:center;">Rp</td><td style="text-align:right;   width:80px;"  >' . $obj->formatNumber($rs[0]['subtotal']) . '</td></tr>
';

    $html .= implode('', $arrSubtotal);

    if (!empty($arrSubtotal))
        $html .= '<tr><td></td> <td style="text-align:left; font-weight:bold;  " class="brand-color">Total Bayar</td><td style="text-align:center;">Rp</td><td style="text-align:right; "  >' . $obj->formatNumber($rs[0]['outstanding'] - $rs[0]['tax23value']) . '</td></tr>';


    $confirmedName = 'Anton';
    //if (!empty($rs[0]['confirmedby'])) {
    //    $rsEmployee = $employee->getDataRowById($rs[0]['confirmedby']);
    //    $confirmedName = $rsEmployee[0]['name'];
    //}

    $html .= '<div style="clear:both"></div>';
    $html .= '
<table cellpadding="4"> 
<tr>
<td style="width:450px;"  class="brand-color">

<b>Pembayaran mohon di transfer ke :</b><br><br>
<table> 

<tr> 
<td style="width:80px;">Bank</td>
<td style="width:10px;">:</td>
<td style="width:170px;">BCA</td> 
</tr>

<tr> 
<td>No. Rek</td>
<td>:</td>
<td>6900 638 777</td> 
</tr>

<tr> 
<td>Atas Nama</td>
<td>:</td>
<td>PT. Thomas Trans</td> 
</tr>

<tr> 
<td>NPWP</td>
<td>:</td>
<td>'.$obj->loadSetting('companyTaxRegistrationNumber').'</td> 
</tr>
<tr> 
<td>NITKU</td>
<td>:</td>
<td>'.$obj->loadSetting('companyTaxRegistrationNumber').' '.$obj->loadSetting('companyTKU').'</td> 
</tr>

</table>';
    
if (!$isReimburse){
    $html .='<div style="clear:both"></div>
    <div style="font-size:0.9em">
    MOHON DIPERHATIKAN !
    <ul>
    <li><i>PPN dibebaskan berdasarkan PP no 49 tahun 2022.</i> Tarif PPN efektif 11% (11/12 * 12%).</i></li>
    <li><i>PPH 23 dihitung dari nilai transaksi.</i></li>
    <li><i>Perubahan invoice berlaku selambat-lambatnya 7(tujuh) hari terhitung dari tanggal invoice diterima.</i></li>
    <li><i>Revisi faktur pajak paling lambat tgl 10 (sepuluh) dibulan berikutnya.</i></li>
    <li><i>PT. Thomas Trans mengacu pada STC ALFI/ILFA 2016 edisi ke-3 (dilampirkan sesuai permintaan).</i></li>
    </ul> '; 
}


$html .='</td>

<td style="width:200px;">
<table style="font-weight:bold"> 
<tr><td style="text-align:center;">' . strtoupper($obj->loadSetting('companyName')) . '</td></tr>
<tr><td style="height:120px; text-align:center;">' . $autoSign . '</td></tr> 
<tr><td style="text-align:center;">' . $confirmedName . '</td></tr> 
</table>
</td> 
</tr>
</table>  
';

    return $html;
};


$generateReportContent = array();
array_push($generateReportContent, $invoiceContent);
//array_push($generateReportContent , $woContent);

?>