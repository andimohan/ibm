<?php  

includeClass('SalesOrder.class.php');
$salesOrder = createObjAndAddToCol( new SalesOrder()); 
$pdf->setCustomSettings(
    array(   
         'paperSetting' => 'A5,L',
         'showPrintHeader' => false,  
         ) 
);  
$obj = $salesOrder;
$generateReportContent = function ($dataset){ 
$obj = new SalesOrder(); 
$termOfPayment = new TermOfPayment();
    
$rs = $dataset['rs'];
$rsDetail = $obj->getDetailWithRelatedInformation($rs[0]['pkey']);  
$rsTOP = $termOfPayment->getDataRowById($rs[0]['termofpaymentkey']); 
    
$arrRecipient = array();
array_push($arrRecipient, $rs[0]['recipientname'], str_replace(chr(13),'<br>',$rs[0]['recipientaddress']), $rs[0]['recipientphone']);
    
$pin = substr($rs[0]['checksum'], -6);
$validateURL = DOMAIN_NAME.'/vso/'.$obj->convertNumAlpha($rs[0]['pkey']).$pin; // kalo pake code repot nanti kalo panjang
$qrResult = $obj->createQR($validateURL,10);
    
$html = $obj->printSetting['defaultStyle'];
$html .= ' 
    <table style="width: 100%;" cellpadding="2">
        <tr>
            <td style="width: 50%; vertical-align: top;">
                <img src="'.PERSONALIZED_DOC_PATH.'include/img/logo-invoice.png" style="width: 100px">
                <div style="font-size: 0.8em;">
                    @sparklebox.id : Instagram - Shopee - Tokopedia - TikTok 
                    
                </div>
            </td>
            <td style="width: 50%; text-align: left; vertical-align: top;">
                <table style="width: 100%;" cellpadding="2">
                    <tr>
                        <td style="width: 25%;">'.ucwords($obj->lang['name']).'</td>
                        <td style="width: 5%;">:</td>
                        <td style="width: 70%; border-bottom: 1px dotted #333;">'.$rs[0]['customername'].'</td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top;">'.ucwords($obj->lang['address']).'</td>
                        <td style="width: 5%; vertical-align: top;">:</td>
                        <td style=" border-bottom: 1px dotted #333;">'.$rs[0]['customeraddress'].'</td>
                    </tr>
                    <tr>
                        <td>Telp/HP</td>
                        <td style="width: 5%;">:</td>
                        <td style=" border-bottom: 1px dotted #333;">'.$rs[0]['customerphone'].'</td>
                    </tr>
                    <tr>
                        <td>'.ucwords($obj->lang['date']).'</td>
                        <td style="width: 5%;">:</td>
                        <td style=" border-bottom: 1px dotted #333;">'.$obj->formatDBDate($rs[0]['trdate'],'d / m / Y').'</td>
                    </tr> 
                </table>
            </td>
        </tr>
    </table>
    <div style="clear:both;"></div>
    <table style="width: 100%;" cellpadding="2">
        <tr>
        <td style="width: 400px;">
            <table cellpadding="2">
                <tr>
                    <td style="border: 1px solid #000; width: 30px; text-align: center; vertical-align: middle;"></td>
                    <td style="width: 360px; font-weight: bold; vertical-align: middle;"> BARANG SUDAH DI CEK PEMBELI DALAM KEADAAN BAIK</td>
                </tr>
            </table>
        </td>
        <td style="width: 274px">
            <table style="width: 100%; text-align: right;" cellpadding="2">
                <tr>
                    <td style="width: 135px;">NO. NOTA</td>
                    <td style="width: 10px;"></td>
                    <td style="border: 1px solid #000; width: 120px; text-align: center; font-weight:bold">'.$rs[0]['code'].'</td>
                </tr>
            </table>
        </td>
        </tr>
    </table>
    <div style="clear:both;height: 0.5em"></div>

    <table style="width: 100%; border-collapse: collapse;" cellpadding="2">
        <tr style="border: 1px solid #000; font-weight:bold">
            <td 
                style="width: 7%; border: 1px solid #000; text-align: center; vertical-align: middle; padding: 5px;">
                Qty
            </td>
            <td style="width: 40%; border: 1px solid #000; text-align: center; vertical-align: middle; padding: 5px;">
                Nama Barang
            </td>
            <td
                style="width: 7%; border: 1px solid #000; text-align: center; padding: 5px;">
                Kadar
            </td>
            <td
                style="width: 12%; border: 1px solid #000; text-align: center; padding: 5px;">
                Berat (Gr)
            </td>
            <td
                style="width: 17%; border: 1px solid #000; text-align: center; padding: 5px;">
                Harga Satuan
            </td>
            <td
                style="width: 17%; border: 1px solid #000; text-align: center; padding: 5px;">
                Jumlah
            </td>
        </tr>';

for ($i=0; $i<count($rsDetail); $i++) {
    $html .= '<tr style="border: 1px solid #000;">
                <td style="border: 1px solid #000; text-align:center;padding: 5px;">'.$obj->formatNumber($rsDetail[$i]['qty']).'</td>
                <td style="border: 1px solid #000; padding: 5px;">'.$rsDetail[$i]['itemname'].'</td>
                <td style="border: 1px solid #000; text-align:center;padding: 5px;">'.$obj->formatNumber($rsDetail[$i]['kadar']).'</td>
                <td style="border: 1px solid #000; text-align:center;padding: 5px;">'.$obj->formatNumber($rsDetail[$i]['qtyinpcs']).'</td>
                <td style="border: 1px solid #000; text-align:center;padding: 5px;">'.$obj->formatNumber($rsDetail[$i]['priceinunit']).'</td>
                <td style="border: 1px solid #000; text-align:center;padding: 5px;">'.$obj->formatNumber($rsDetail[$i]['total']).'</td>
              </tr>';
}

$html .= '</table>
<div style="clear:both;"></div>';


$html .= '<table style="width: 100%"> 
    <tr>
        <td style="text-align: left; vertical-align: top;"> 
            <table>
                <tr>
                    <td style="text-align:center;width: 80px;"><img src="'.$qrResult['url'].'"/><br>'.$pin.'</td>
                    <td></td>
                </tr>
            </table> 
            <div style="font-size: 0.9em;">'.$obj->loadSetting('invoiceFooter').'</div>
        </td>
        <td style="text-align: right; vertical-align: top;">
            <table cellpadding="5" style="margin-left: auto;">
                <tr>
                    <td style="width:66%; text-align: right; font-weight: bold; padding: 8px;">TOTAL</td>
                    <td style="text-align: center; background-color: #d1d1d1; font-weight: bold; padding: 8px; width: 35%;">
                        '.$obj->formatNumber($rs[0]['grandtotal']).'
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>';
    
return $html;
}
?>