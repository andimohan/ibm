<?php 
 

includeClass('WarehouseTransfer.class.php');
$warehouseTransfer = createObjAndAddToCol( new WarehouseTransfer()); 
$obj = $warehouseTransfer;

$generateReportContent = function ($dataset){ 

$obj = new WarehouseTransfer(); 
$warehouse = new Warehouse();    
$rs = $dataset['rs'];
$rsDetail = $obj->getDetailWithRelatedInformation($rs[0]['pkey']); 
  
$html = $obj->printSetting['defaultStyle'];
$html .= ' 
<table cellpadding="2" > 
<tr><td><div class="title">TRANSFER GUDANG</div></td></tr>
<tr><td><div class="subtitle">'.$rs[0]['code'].'</div></td></tr>
</table> 

<div style="clear:both"></div>

<table cellpadding="2">  
<tr><td class="header-row-header">Tanggal</td><td style="width:10px; text-align:center">:</td><td>'.$obj->formatDBDate($rs[0]['trdate'],'d / m / Y').'</td></tr> 
<tr><td class="header-row-header">Gudang Asal</td><td style="width:10px; text-align:center">:</td><td>'.$rs[0]['warehousefromname'].'</td></tr>  
<tr><td class="header-row-header">Gudang Tujuan</td><td style="width:10px; text-align:center">:</td><td>'.$rs[0]['warehousetoname'].'</td></tr>  
</table>   
<div style="clear:both"></div> ';

$html .= ' 
<table  cellpadding="4" class="table-transaction">
<tr class="col-header" ><td style="width:510px;">Item</td><td style="text-align:right; width:80px;">Jumlah</td><td style="width:80px;">Unit</td></tr>';

for ($i=0;$i<count($rsDetail);$i++){  
  $html .= '<tr><td>'.$rsDetail[$i]['itemname'].'</td><td style="text-align:right">'. $obj->formatNumber($rsDetail[$i]['qty']).'</td><td>'.$rsDetail[$i]['unitname'].'</td></tr>' ; 
}
$html .= '</table>' ;
 
$html .= '
<div style="clear:both"></div> 
<table cellpadding="4">
<tr><td><strong>Catatan</strong> :<br>'.str_replace(chr(13),'<br>',$rs[0]['trdesc']).'</td></tr>
</table>
<div "clear:both"></div>';

$html .= $obj->generateSignLabel($rs); 
    
return $html;
}
?>
