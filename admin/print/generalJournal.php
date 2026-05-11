<?php 
includeClass(array('GeneralJournal.class.php'));
$generalJournal = createObjAndAddToCol(new GeneralJournal());

$obj = $generalJournal;
 
$generateReportContent = function ($dataset){ 

$obj = new GeneralJournal();
$chartOfAccount = new ChartOfAccount();
        
$rs = $dataset['rs'];
$rsDetail = $obj->getDetailById($rs[0]['pkey']); 
    
$html = $obj->printSetting['defaultStyle'];
$html .= ' 
<table cellpadding="2" > 
<tr><td><div class="title">JURNAL UMUM</div></td></tr> 
<tr><td><div class="subtitle">'.$rs[0]['code'].'</div></td></tr>
</table> 

<div style="clear:both"></div>

<table cellpadding="2">  
<tr><td class="header-row-header">Tanggal</td><td style="width:10px; text-align:center">:</td><td>'.$obj->formatDBDate($rs[0]['trdate'],'d / m / Y').'</td></tr>  
<tr><td class="header-row-header">Referensi</td><td style="width:10px; text-align:center">:</td><td>'. $rs[0]['refcode'] .'</td></tr>  
</table>   
<div style="clear:both"></div> ';

$html .= ' 
<table  cellpadding="4" class="table-transaction">
<tr class="col-header" ><td style="width:450px;">Kas Asal</td><td style="width:110px; text-align:right;" >Debit</td><td style="text-align:right; width:110px;">Kredit</td></tr>';

for ($i=0;$i<count($rsDetail);$i++){ 
  $rsCOA  = $chartOfAccount->getDataRowById($rsDetail[$i]['coakey']); 
  $html .= '<tr><td>'.$rsCOA[0]['code'].' - '.$rsCOA[0]['name'].'</td><td style="text-align:right">'.$obj->formatNumber($rsDetail[$i]['debit']).'</td><td style="text-align:right">'.$obj->formatNumber($rsDetail[$i]['credit']).'</td></tr>' ; 
  if (!empty($rsDetail[$i]['trdesc']))    
  $html .= '<tr><td colspan="1" class="footnote">'.$rsDetail[$i]['trdesc'].'</td><td colspan="2"></td></tr>' ; 
}
$html .= '</table>' ;

$sayNumber = $obj->sayNumber($rs[0]['totaldebit']);
$html .= '
<div style="clear:both"></div> 
<table cellpadding="4"> 
<tr><td style="width:370px"><strong>Terbilang</strong> :<br>'.ucwords($sayNumber).' Rupiah.</td><td style="text-align:right; font-weight:bold;  width:80px; ">Total</td><td style="text-align:right; font-weight:bold;  width:110px;"  >'.$obj->formatNumber($rs[0]['totaldebit']).'</td><td style="text-align:right; font-weight:bold;  width:110px;"  >'.$obj->formatNumber($rs[0]['totalcredit']).'</td></tr>
</table>
<table cellpadding="4">
<tr><td><strong>Catatan</strong> :<br>'.str_replace(chr(13),'<br>',$rs[0]['trdesc']).'</td></tr>
</table>
<div "clear:both"></div>';

$html .= $obj->generateSignLabel($rs); 
    
return $html;
}
?>