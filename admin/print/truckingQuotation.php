<?php
includeClass(array('TruckingQuotation.class.php'));
$truckingQuotation = createObjAndAddToCol(new TruckingQuotation());

$obj = $truckingQuotation;

$generateReportContent = function ($dataset) {

    $obj = new TruckingQuotation();
    $employee = new Employee();

    $rs = $dataset['rs'];
    $rsDetail = $obj->getDetailById($rs[0]['pkey']);
    $sayNumber = $obj->sayNumber($rs[0]['total']);
    $trnotes = (!empty($rs[0]['trdesc'])) ? '<div style="clear:both"></div><strong>Catatan :</strong> <br>' . str_replace(chr(13), '<br>', $rs[0]['trdesc']) : '';
    $html = $obj->printSetting['defaultStyle'];
    
    $html .= ' 
<table cellpadding="2" > 
<tr><td><div class="title">QUOTATION</div></td></tr>
<tr><td><div class="subtitle">' . $rs[0]['code'] . '</div></td></tr>
</table> 

<div style="clear:both"></div>
 
<table>
<tr>
<td style="width:300px;" >
<table cellpadding="2">
<tr><td class="header-row-header" style="width:120px">'.$obj->lang['date'].'</td><td style="width:10px; text-align:center">:</td><td style="width:170px">' . $obj->formatDBDate($rs[0]['trdate'], 'd / m / Y') . '</td></tr>
<tr><td class="header-row-header">'.$obj->lang['typeOfJob'].'</td><td style="text-align:center">:</td><td>' . $rs[0]['cargotype'] . ' / ' . $rs[0]['categoryname'] . '</td></tr>
</table>
</td>
<td style="width:370px;"> 
<table cellpadding="2">
<tr><td class="header-row-header" style="width:120px">'.$obj->lang['customer'].'</td><td style="width:10px; text-align:center">:</td><td style="width:240px">' . $rs[0]['customername'] . '</td></tr>
<tr><td class="header-row-header">UP</td><td style="text-align:center">:</td><td style="width:240px">' . $rs[0]['recipientname'] . '</td></tr>
<tr><td class="header-row-header" >'.$obj->lang['route'].'</td><td style="text-align:center">:</td><td>' . $rs[0]['routefrom'] . ' - '.$rs[0]['routeto'].'</td></tr>
</table>
</td>
</tr>
</table>

<div style="clear:both"></div>
<table cellpadding="4" class="table-transaction">
<tr class="col-header"><td style="text-align:left;width:30px">No</td><td style="text-align:right;width:60px">Partai</td><td style="text-align:left;width:300px">Layanan</td><td style="text-align:right;width:140px">Harga</td><td style="text-align:right;width:140px">Jumlah</td></tr>  
';

for ($i = 0; $i < count($rsDetail); $i++) {
     
    $detailDesc = (!empty($rsDetail[$i]['trdesc'])) ? '<br>'.nl2br($rsDetail[$i]['trdesc']) : '';
    $html .= '
        <tr>
            <td style="text-align:right">' . ($i + 1) . '.</td>
            <td style ="text-align:right">' . $obj->formatNumber($rsDetail[$i]['qtyinbaseunit']) . '</td>
            <td>' . $rsDetail[$i]['servicename'] .$detailDesc. '</td>
            <td style ="text-align:right">' . $obj->formatNumber($rsDetail[$i]['priceinunit']) . '</td>
            <td style ="text-align:right">' . $obj->formatNumber($rsDetail[$i]['subtotal']) . '</td>
            </tr>';
}

$html .= '    
</table>  
<div style="clear:both"></div> 
<table cellpadding="4"> 
<tr>
<td rowspan="3" style="width:430px"><strong>Terbilang</strong> :<br>' . ucwords($sayNumber) . ' Rupiah.</td>
<td style="text-align:right; font-weight:bold;  width:130px; ">Total</td>
<td style="text-align:right; font-weight:bold;  width:110px;" >' . $obj->formatNumber($rs[0]['total']) . '</td>
</tr>
</table>
<div style="clear:both"></div>   
' . $trnotes . '
<div style="clear:both"></div>  
';

$html .= $obj->generateSignLabel($rs);
return $html;
}

?>