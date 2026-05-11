<?php  


$generateReportContent = function ($dataset){  

$obj = new CashBankTransfer();
$chartOfAccount = new ChartOfAccount();
        
$rs = $dataset['rs'];
$rsDetail = $obj->getDetailWithRelatedInformation($rs[0]['pkey']);
    
$html = $obj->printSetting['defaultStyle'];
$html .= ' 
<table cellpadding="2" > 
<tr><td><div class="title">TRANSFER KAS BANK</div></td></tr> 
<tr><td><div class="subtitle">'.$rs[0]['code'].'</div></td></tr>
</table> 

<div style="clear:both"></div>

<table cellpadding="2">  
<tr><td class="header-row-header">Tanggal</td><td style="width:10px; text-align:center">:</td><td>'.$obj->formatDBDate($rs[0]['trdate'],'d / m / Y').'</td></tr>  
</table>   
<div style="clear:both"></div> ';

$html .= ' 
<table  cellpadding="4" class="table-transaction">
<tr class="col-header" ><td style="width:280px;">Kas Asal</td><td style="width:280px;" >Kas Tujuan</td><td style="text-align:right; width:110px;">Jumlah</td></tr>';

for ($i=0;$i<count($rsDetail);$i++){ 
  $html .= '<tr><td>'.$rsDetail[$i]['coafromcode'].'</td><td>'.$rsDetail[$i]['coatocode'].'</td><td style="text-align:right">'.$obj->formatNumber($rsDetail[$i]['amount']).'</td></tr>' ; 
  if (!empty($rsDetail[$i]['trdesc']))    
  $html .= '<tr><td colspan="3" class="footnote">'.$rsDetail[$i]['trdesc'].'</td></tr>' ; 
}
$html .= '</table>' ;

$sayNumber = $obj->sayNumber($rs[0]['grandtotal']);
$html .= '
<div style="clear:both"></div> 
<table cellpadding="4"> 
<tr><td style="width:460px"><strong>Terbilang</strong> :<br>'.ucwords($sayNumber).' Rupiah.</td><td style="text-align:right; font-weight:bold;  width:100px; ">Total</td><td style="text-align:right; font-weight:bold;  width:110px;"  >'.$obj->formatNumber($rs[0]['grandtotal']).'</td></tr>
</table>
<table cellpadding="4">
<tr><td><strong>Catatan</strong> :<br>'.str_replace(chr(13),'<br>',$rs[0]['trdesc']).'</td></tr>
</table>
<div "clear:both"></div>';

$html .= $obj->generateSignLabel($rs); 
    
return $html;
}
?>