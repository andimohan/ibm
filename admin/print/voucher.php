<?php  
$obj = $voucherTransaction;
$generateReportContent = function ($dataset){ 
$obj = new VoucherTransaction(); 
//$voucher = new VoucherTransaction(); 
    
$rs = $dataset['rs'];
//$rsDetail = $obj->getDataRowById($rs[0]['pkey']);  
$sayNumber = $obj->sayNumber($rs[0]['value']);


$html = $obj->printSetting['defaultStyle'];

$html .= ' 
<table cellpadding="2" > 
<tr><td><div class="title" style="font-size:20px">Voucher</div></td></tr>
<tr><td><div class="subtitle" style="font-size:16px">'.$rs[0]['code'].'</div></td></tr>
</table> 

<div style="clear:both"></div>
<div style="clear:both"></div>
<table cellpadding="4" style="height:2480px">
<tr>
<td style=" font-size:14px;">Selamat <strong>'.strtoupper($rs[0]['customername']).'</strong> !</td>
</tr>
<tr>
<td style=" font-size:14px;">Selamat anda mendapatkan voucher sejumlah <strong>Rp.'.$obj->formatNumber($rs[0]['value']).'</strong> ( <span style="text-transform:uppercase;">'.$sayNumber.'rupiah</span> ) anda dapat menggunakan voucher ini untuk potongan harga.</td>
</tr>

</table>


<div style="clear:both"></div>

<table cellpadding="4" style="height:2480px">
<tr>
<td style=" font-size:14px;">Term & Condition</td>
</tr>
<tr>
<td style=" font-size:14px;"></td>
</tr>
<tr>
<td style=" font-size:10px;">Voucher ini berlaku hingga '.$obj->formatDBDate($rs[0]['expdate']).'</td>
</tr>

</table>
';
    

    
return $html;
}
?>