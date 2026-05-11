<?php  
$obj = $salesOrderRental;
$generateReportContent = function ($dataset){ 
$obj = new SalesOrderRental(); 
$termOfPayment = new TermOfPayment();
$item = new Item();
    
$rs = $dataset['rs'];
$rsDetail = $obj->getDetailWithRelatedInformation($rs[0]['pkey']);  
$rsTOP = $termOfPayment->getDataRowById($rs[0]['termofpaymentkey']); 
    
$arrRecipient = array();
array_push($arrRecipient, $rs[0]['recipientname'], str_replace(chr(13),'<br>',$rs[0]['recipientaddress']), $rs[0]['recipientphone']);


$profileImg = $obj->loadSetting('companyLogo'); 
$img =  HTTP_HOST.'phpthumb/phpThumb.php?src='.$obj->phpThumbURLSrc.'setting/companyLogo/'.$profileImg.'&w=1000&h=500&hash='.getPHPThumbHash($profileImg);
    
$html = $obj->printSetting['defaultStyle'];
$html .= ' 
<table cellpadding="2" > 
<tr><td><div class="title">Price Bid Proposal</div></td></tr>
<tr><td><div class="subtitle">'.$rs[0]['code'].'</div></td></tr>
</table> 

<table cellpadding="2"> 
<tr><td colspan="3" class="">Pekerjaan :</td></tr> 
<tr><td colspan="3"  style="width: 670px;"><br><strong>'.$rs[0]['jobname'].'</strong></td></tr>  
</table> 
 ';

$html .= ' 
<table  cellpadding="4" class="">
<tr class="header-row-header" ><td style="" colspan="7">Peralatan Yang Dibutuhkan</td></tr>
<tr class="header-row-header" ><td style="border:solid 1px black; width:40px;text-align:center;">No</td><td style="border:solid 1px black; width:170px;text-align:center;">Deskripsi</td><td style=" border:solid 1px black; width:60px;text-align:right" >Jumlah</td><td style="width:60px; border:solid 1px black;" >Satuan</td><td style="width:60px;text-align:right;border:solid 1px black;" >Jumlah </td><td style="width:60px;text-align:left;border:solid 1px black;" >Waktu </td><td style="width:120px;text-align:center;border:solid 1px black;" >Tarif Sewa/joint/hari</td><td style="text-align:right;border:solid 1px black; width:110px;">Subtotal</td></tr>
<tr class="header-row-header" ><td style="border:solid 1px black; text-align:center; ">A</td><td style="border:solid 1px black;" colspan="7">Hitungan Tarif Sewa / Hari (24 jam)</td></tr>';

for ($i=0;$i<count($rsDetail);$i++){  

  $html .= '<tr>
                <td style="border:solid 1px black; text-align:center;">'.($i+1).'</td>
                <td style="border:solid 1px black;">'.$rsDetail[$i]['itemname'].'</td>
                <td style="border:solid 1px black; text-align:right">'.$obj->formatNumber($rsDetail[$i]['qty']).'</td>
                <td style="border:solid 1px black;">'. $rsDetail[$i]['unitname'] .'</td>
                <td style="border:solid 1px black; text-align:right">'.$obj->formatNumber($rsDetail[$i]['totaldays']).'</td>
                <td style="border:solid 1px black;">'. $rsDetail[$i]['timename'] .'</td>
                <td style="border:solid 1px black; text-align:right">'.$obj->formatNumber($rsDetail[$i]['priceinunit']).'</td>
                <td style="border:solid 1px black; text-align:right">'.$obj->formatNumber($rsDetail[$i]['total']).'</td>
            </tr>' ; 
}
$html .= '</table>' ;

//$html .= '<div style="clear:both"></div>';
        
//$sayNumber = $obj->sayNumber($rs[0]['grandtotal']);
 
$html .= '<table cellpadding="4" > 
<tr>
<td style="border:solid 1px black;text-align:right; font-weight:bold;  width:40px;"></td>
<td style="border:solid 1px black;text-align:center; font-weight:bold;  width:530px;">TOTAL</td>
<td style="border:solid 1px black;text-align:right; font-weight:bold;  width:110px;">'.$obj->formatNumber($rs[0]['subtotal']).'</td>
</tr>
';  
    
$html .= implode('',$arrSubtotal); 
    
$html .= '
</table>
<div style="clear:both"></div>';
    
$html .= ' 
<table  cellpadding="4" class="">
<tr class="header-row-header" ><td style="border:solid 1px black; width:40px;text-align:center;">No</td><td style="border:solid 1px black;width:220px;text-align:center;">Deskripsi</td><td style="border:solid 1px black;width:190px;text-align:right" ></td><td style="border:solid 1px black;width:120px;text-align:center" >Tarif Lost In Hole</td><td style="text-align:right; border:solid 1px black;width:110px;"></td></tr>
<tr class="header-row-header" ><td style="border:solid 1px black; text-align:center; ">B</td><td style="border:solid 1px black;" colspan="6">Price List Lost In Hole</td></tr>';
for ($j=0;$j<count($rsDetail);$j++){  
    
    $rsItem = $item->getDataRowById($rsDetail[$j]['itemkey']);

    $html .= '<tr>
                <td style="text-align:center;border:solid 1px black;">'.($j+1).'</td><td style="border:solid 1px black;">'.$rsDetail[$j]['itemname'].'</td><td style="border:solid 1px black;text-align:right"></td><td style="border:solid 1px black;text-align:right">'.$obj->formatNumber($rsItem[0]['sellingprice']).'</td><td style="border:solid 1px black;text-align:right"></td></tr>' ; 
}
$html .= '</table><div style="clear:both"></div>' ;
    
$html .= ' 
<table  cellpadding="4" class="">
<tr><td style="width:500;"></td><td style="height:100px;width:190px;text-align:right;">Jakarta, '.$obj->formatDBDate($rs[0]['trdate'],'N F  Y').'<br><strong>PT. Binakarindo Yacoagung</strong></td></tr>
<tr><td style=""></td><td style="text-align:right;"><strong>RIANTO KRISTANTO</strong><br>Direktur</td></tr>
</table>';

$html .= '' ;
    
$html .= ' 
<table  cellpadding="4" class="">
<tr><td style="width:40px">Note :</td><td style="width:650px"></td></tr>
<tr><td></td><td>'.str_replace(chr(13),'<br>',$rs[0]['trdesc']).'</td></tr>
</table>';

$html .= '' ;
    
    
//$html .= $obj->generateSignLabel($rs); 
    
return $html;
}
?>
