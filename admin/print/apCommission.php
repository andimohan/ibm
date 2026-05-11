<?php 

includeClass(array('AP.class.php','APCommission.class.php'));
$apCommission = createObjAndAddToCol( new APCommission());
$supplier = createObjAndAddToCol( new Supplier());


$obj = $apCommission; 
$generateReportContent = function ($dataset){ 

$obj = new APCommission(); 
$ap = $obj->getAPObj(); 
$supplier = new Supplier();    

$decimalPrice = ($rs[0]['currencykey'] == CURRENCY['idr'] ) ? 0 : 2;    

$rs = $dataset['rs'];
$rsHeader = $obj->getDataRowById($rs[0]['pkey']); 
$rsSupplier = $supplier->getDataRowById($rsHeader[0]['supplierkey']); 
$sayNumber = $obj->sayNumber($rs[0]['amount']);

$html = $obj->printSetting['defaultStyle'];
$html .= ' 
<table cellpadding="2" > 
<tr><td><div class="title">'.$obj->lang['apCommission'].'</div></td></tr>
<tr><td><div class="subtitle">'.$rs[0]['code'].'</div></td></tr>
</table> 

<div style="clear:both"></div>

<table cellpadding="2">  
<table cellpadding="2">  
<tr><td style="width:100px; font-weight:bold">'.$obj->lang['date'].'</td><td style="width:10px; text-align:center">:</td><td style="width:570px">'.$obj->formatDBDate($rs[0]['trdate'],'d / m / Y').'</td></tr> 
<tr><td style="font-weight:bold">'.$obj->lang['supplier'].'</td><td style="text-align:center">:</td><td>'.$rsSupplier[0]['name'].'</td></tr>
<tr><td style="font-weight:bold">'.$obj->lang['amount'].'</td><td style="text-align:center">:</td><td>'.$obj->formatNumber($rsHeader[0]['amount']).' ('.$rs[0]['currencyname'].')</td></tr>  
<tr><td style="font-weight:bold">'.$obj->lang['saidAmount'].'</td><td style="text-align:center">:</td><td>'.ucwords($sayNumber).'</td></tr>  
</table>   
</table>   
<div style="clear:both"></div> ';
 
$html .= '
<div style="clear:both"></div> 
<table cellpadding="4">
<tr><td><strong>'.$obj->lang['note'].' </strong> :<br>'.str_replace(chr(13),'<br>',$rs[0]['trdesc']).'</td></tr>
</table>
<div "clear:both"></div>';

$html .= $obj->generateSignLabel($rs);
return $html;

}

?>