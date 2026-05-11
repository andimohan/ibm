<?php

includeClass('SalesOrder.class.php');
$salesOrder = createObjAndAddToCol( new SalesOrder()); 
$pdf->setCustomSettings(
    array(   
         'paperSetting' => 'A4,P',
         'showPrintHeader' => false,
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

    
$arrFiles = array();
$rsItemFile = $obj->getFileDetail($rs[0]['pkey']);
foreach($rsItemFile as $fileRow){ 
    $filePath = ($obj->useStorage) ? $obj->createPresignedURL(DOMAIN_NAME.'/'. $obj->uploadFileFolder.$fileRow['refkey'].'/'.$fileRow['file']) : $obj->defaultDocUploadPath. $obj->uploadFileFolder.$fileRow['refkey'].'/'.$fileRow['file']; 
    array_push($arrFiles,$filePath);
}
    
$profileImg = $obj->loadSetting('companyLogo'); 
$logo =  '<img src="'.$obj->phpThumbURLSrc.'setting/companyLogo/'.$profileImg.'"
        style="height:70px; width:110px; display:block;">';

$html = '';

$html .= '<table style="width: 670px;">
        <tr>
            <td style="width: 250px;"><span style="font-size:50px;">INVOICE</span></td> 
            <td style="width: 200px; "></td>
            <td style=" width: 220px; text-align: right; vertical-align: top;">'.$logo.'</td>
        </tr>
    </table>
<div style="clear:both;"></div>
';

$html .='<table> 
            <tr>
                <td style="width:400px; text-align:left"><img src="'.$qrResult['url'].'" style="width: 80px" /></td>
                <td  style="width:180px"><table cellpadding="2"> 
                        <tr>
                            <td class="header-row-header" style="width:90px;font-weight:bold; ">Invoice No</td>
                            <td style="width: 560px;">'.$rs[0]['code'].'</td>
                        </tr>  
                        <tr>
                            <td class="header-row-header" style="font-weight:bold; ">Date</td>
                            <td style="width: 560px;">'.strtoupper($obj->formatDBDate($rs[0]['trdate'],'d F Y')).'</td>
                        </tr> 
                        <tr>
                            <td class="header-row-header" style="font-weight:bold; ">Billed To</td>
                            <td style="width: 560px;">'.strtoupper($rs[0]['customername']).'</td>
                        </tr>  
                    </table>
                </td>
                <td style="width:10px;"></td>
                
            </tr>
        </table>     
        ';
     
$html .='<div style="clear:both"></div> ';


$html .='
<table style="width: 670px;" cellpadding="6">
        <tr style="border:1px  solid #dede; background-color: #9e9e9e; color: #ffffff; font-weight:bold;">
            <td style="width: 50px; border: 1px solid #ffffff; text-align: center;">No</td>
            <td style="width: 290px; border: 1px solid #ffffff;text-align: center;">Description</td>
            <td style="width: 120px;  border: 1px solid #ffffff; text-align: center; ">Price</td>
            <td style="width: 90px;  border:1px solid #ffffff; text-align: center;">Unit</td>
            <td style="width: 120px;  border:1px solid #ffffff; text-align: center;">Total</td>
        </tr>
';

$totalQty = 0;
for ($i=0; $i<count($rsDetail); $i++) {
    

    $bgColor = ($i % 2 == 0) ? '#ffffff' : '#f2f2f2';
    

    $sn = $obj->getSerialNumber($rsDetail[$i]['pkey']);
    $arrSN = implode(', ',array_column($sn,'serialnumber'));
    
    $descRow = array();
    if (!empty($arrSN)) array_push($descRow,$arrSN);
    if (!empty($rsDetail[$i]['trdesc'])) array_push($descRow,$rsDetail[$i]['trdesc']);

    $priceInUnit = ($rsDetail[$i]['total'] == 0) ? 'Free' : $obj->formatNumber($rsDetail[$i]['priceinunit']);
    $total = ($rsDetail[$i]['total'] == 0) ? 'Free' : $obj->formatNumber($rsDetail[$i]['total']);
    
    $desc = (!empty($descRow)) ? '<br><i>'.implode('<br>',$descRow).'</i>' : '';
    $html .= '<tr style="border: 1px solid #ffffff;background-color: '.$bgColor.';">
                <td style="border: 1px solid #ffffff;background-color: '.$bgColor.'; text-align:center;">'.($i+1).'</td>
                <td style="border: 1px solid #ffffff;background-color: '.$bgColor.'; ">'.$rsDetail[$i]['itemname'].$desc.'</td>
                <td style="border: 1px solid #ffffff;background-color: '.$bgColor.'; text-align:right;">'.$priceInUnit.'</td>
                <td style="border: 1px solid #ffffff;background-color: '.$bgColor.'; text-align:center;">'.$obj->formatNumber($rsDetail[$i]['qty']).'</td>
                <td style="border: 1px solid #ffffff;background-color: '.$bgColor.'; text-align:right;">'.$total.'</td>
              </tr>';

    $totalQty += $rsDetail[$i]['qty'];
}

$html .='
<tr style="border: 1px solid #ffffff;background-color: #bfbfbf;">
    <td colspan="3" style="border: 1px solid #ffffff;background-color: #bfbfbf; text-align:right;font-weight:bold;">TOTAL</td>
    <td style="border: 1px solid #ffffff;background-color: #bfbfbf; text-align:center;font-weight:bold;">'.$obj->formatNumber($totalQty).'</td>
    <td style="border: 1px solid #ffffff;background-color: #bfbfbf; text-align:right;font-weight:bold;">'.$obj->formatNumber($rs[0]['grandtotal']).'</td>
</tr>
';

$html .='
</table>';

$html .='<div style="clear:both"></div> ';

    
// IMAGE
    
$maxWidth = 650;
$maxCols  = 3;

/* cell width like 1fr */
$cellWidth = $maxWidth / $maxCols;

/* image width = 70% of cell */
$imgWidth = $cellWidth * 0.7;

$col = 0;

$imgContent = '<table width="'.$maxWidth.'" cellpadding="4"><tr>';

foreach ($arrFiles as $img) {

    $imgContent .= '
        <td width="'.$cellWidth.'" align="center" valign="middle">
            <img src="'.$img.'" width="'.floor($imgWidth).'">
        </td>';

    $col++;

    if ($col % $maxCols === 0) {
        $imgContent .= '</tr><tr>';
    }
}

$imgContent .= '</tr></table>';

    
// IMAGE
     //<td style="width:180px; text-align:center; font-size:1.2em"><b>Payment Method</b><br>Transfer BCA<br>Thendra Crisnanda<br>0570396188</td>
       //
$html .= '<br><table style="width:670px"> 
        <tr>
            <td style="width: 670px;">
                '.$imgContent.'
            </td> 
        </tr>
</table>';

 return '<div  style=" font-size: 1.2em">'.$html.'</div>';
};

?>