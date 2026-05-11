<?php  

$pdf->setCustomSettings(
    array(   
         'paperSetting' => 'A5,L',
         'showPrintHeader' => false,
         //'footer' => '',
         //'marginFooter' => 0,
         'fontName' => 'courier'
         ) 
);  


$obj = $salesOrder;
$generateReportContent = function ($dataset){ 
global $pdf;    
    
$obj = new SalesOrder(); 
$termOfPayment = new TermOfPayment();
    
$rs = $dataset['rs'];
$rsDetail = $obj->getDetailWithRelatedInformation($rs[0]['pkey']);  

$pin = substr($rs[0]['checksum'], -6);
$validateURL = DOMAIN_NAME.'/vso/'.$obj->convertNumAlpha($rs[0]['pkey']).$pin; // kalo pake code repot nanti kalo panjang
$qrResult = $obj->createQR($validateURL,10);
     
//$pdf->Image(PERSONALIZED_DOC_PATH.'include/img/pattern.jpg' ,-5,10, 30,270, '', '', '', false, 300, '', false, false, 0);
//$pdf->Image(PERSONALIZED_DOC_PATH.'include/img/pattern.jpg',205,0, 100, 40, 'JPG', '', '', false, 300, '', false, false, 1);
    
$html = '';//$obj->printSetting['defaultStyle'];
    
$html .= ' 
    <table style="width: 670px" cellpadding="2">
        <tr>
            <td style="width: 250px;">
                <img src="'.PERSONALIZED_DOC_PATH.'include/img/logo-invoice.png" style="width: 250px">
            </td>  
            <td style="width: 10px"></td>
            <td style="width: 200px; font-size:1.1em">
                <table cellpadding="2">
                    <tr><td style="width:30px; text-align:center;"><img src="'.PERSONALIZED_DOC_PATH.'include/img/loc.png" style="width:13px; "></td><td  style="width:170px">Jl. Sukowati Gemolong,<br>Sragen - Jawa Tengah</td></tr>
                    <tr><td style="text-align:center; line-height:20px; "><img src="'.PERSONALIZED_DOC_PATH.'include/img/wa.png" style="width:13px"></td><td>0813-9060-1100</td></tr>
                    <tr><td style="text-align:center;  line-height:20px;   "><img src="'.PERSONALIZED_DOC_PATH.'include/img/ig.png" style="width:13px;"></td><td>@lm_rodamakmur</td></tr>
                </table>
            </td>
            <td style="width: 10px"></td>
            <td style=" width: 190px; text-align: left; vertical-align: top;">
                <table cellpadding="4">
                    <tr>
                        <td style="font-size:1.2em; background-color: #d3d3d3;  text-align:center;">'.$rs[0]['code'].'</td>
                    </tr>
                    <tr>
                        <td style=" border-bottom: 1px solid #333; text-align:center">'.$obj->formatDBDate($rs[0]['trdate']).'</td>
                    </tr>
                    <tr>
                        <td style=" border-bottom: 1px solid #333; text-align:center">'.$rs[0]['customername'].'</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <div></div>
    <table style="width: 670px;" cellpadding="6">
        <tr style="border:1px  solid #dede; background-color: #dabe8a; font-size:1.2em">
            <td style="width: 50px; border: 1px solid #333333; text-align: center;">Jml</td>
            <td style="width: 290px; border: 1px solid #333333;">Item</td>
            <td style="width: 90px;  border:1px solid #333333; text-align: center;">Gram</td>
            <td style="width: 120px;  border: 1px solid #333333; text-align: right; ">Harga</td>
            <td style="width: 120px;  border:1px solid #333333; text-align: right">Total</td>
        </tr>';

for ($i=0; $i<count($rsDetail); $i++) {
    $sn = $obj->getSerialNumber($rsDetail[$i]['pkey']);
    $arrSN = implode(', ',array_column($sn,'serialnumber'));
    
    $descRow = array();
    if (!empty($arrSN)) array_push($descRow,$arrSN);
    if (!empty($rsDetail[$i]['trdesc'])) array_push($descRow,$rsDetail[$i]['trdesc']);
    
    $desc = (!empty($descRow)) ? '<br><i>'.implode('<br>',$descRow).'</i>' : '';
    $html .= '<tr style="border: 1px solid #333333;">
                <td style="border: 1px solid #333333; text-align:center;">'.$obj->formatNumber($rsDetail[$i]['qty']).'</td>
                <td style="border: 1px solid #333333; ">'.$rsDetail[$i]['itemname'].$desc.'</td>
                <td style="border: 1px solid #333333; text-align:center;">'.$obj->formatNumber($rsDetail[$i]['gramasi'],2).'</td>
                <td style="border: 1px solid #333333; text-align:right;">'.$obj->formatNumber($rsDetail[$i]['priceinunit']).'</td>
                <td style="border: 1px solid #333333; text-align:right;">'.$obj->formatNumber($rsDetail[$i]['total']).'</td>
              </tr>';
}

$html .= '</table>
<div style="clear:both;"></div>';

$html .= '<table style="width:670px"> 
        <tr>
        <td style="width: 340px; text-align: left; vertical-align: top;"><div style="font-size: 0.9em;">'.nl2br($obj->loadSetting('invoiceFooter')).'</div><br><br><table cellpadding="8">
            <tr>
                <td style="background-color: #dedede; width: 300px; text-align:center;">
                    <span style="font-weight: bold; font-size: 1.2em">BCA 4900505162 a.n Vina Christina</span><br>
                    <span style="font-size:0.8em"><i>Note : Wajib lampirkan invoice ini untuk buyback</i></span>
                </td>
            </tr>
        </table>
        </td>
        <td style="width:13px"></td>
        <td style="width: 80px; text-align:center"><img src="'.$qrResult['url'].'" /><br>'.$pin.'<br></td>
        <td style="width: 230px; text-align: right; vertical-align: top;">
            <table cellpadding="6" style="width: 220px; font-weight: bold;">
                <tr>
                    <td style="width: 100px; text-align: right; padding: 8px;">TOTAL</td>
                    <td style="width: 120px; text-align: right; border: 1px solid #dabe8a;">'.$obj->formatNumber($rs[0]['grandtotal']).'</td>
                </tr>
            </table>  
        </td>
    </tr>
</table>

<div style="clear:both;"></div>';

//$html .= 'test<br>test<br>test<br>test<br>test<br>test<br>test<br>test<br>test<br>test<br>'; 
return $html;
}
?>