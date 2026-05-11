<?php 
$generateReportContent = function ($dataset){ 

$obj = new WarehouseTransfer(); 
$warehouse = new Warehouse();    
$item = new Item();  
$itemUnit = new ItemUnit();  
    
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

$totalItem = 0;
for ($i=0;$i<count($rsDetail);$i++){  
  $html .= '<tr><td>'.$rsDetail[$i]['itemname'].'</td><td style="text-align:right">'. $obj->formatNumber($rsDetail[$i]['qty']).'</td><td>'.$rsDetail[$i]['unitname'].'</td></tr>' ; 
  $totalItem += $rsDetail[$i]['qty']; 
}
    
$html .= '<table cellpadding="4" style="font-weight:bold"><tr><td style="text-align:right;width:510px">Total</td><td style="text-align:right;width:80px">'.$totalItem.'</td></tr></table>' ;

$html .= '</table>' ;

$destinationConversion = $obj->loadSetting('warehouseTransferConversion');
if($destinationConversion == 1){ 

    $rsItemConvert = $obj->convertItemConversion($rsDetail);
    
    $convertItem = ' <div style="clear:both"></div>
    <table  cellpadding="4" class="table-transaction">
    <tr><td style="font-size:1.2em;font-weight:bold"><div>Konversi Barang</div></td></tr>
    <tr class="col-header" ><td style="width:510px;">Item</td><td style="text-align:right; width:80px;">Jumlah</td><td style="width:80px;">Unit</td></tr>';

    $totalItemConvert = 0;

    $rsItem = $item->searchData('','',true, ' and '.$item->tableName.'.pkey in ('.$obj->oDbCon->paramString( array_keys($rsItemConvert),',').') ');
    $rsItem = array_column($rsItem,null,'pkey');
    
    foreach ($rsItemConvert as $key=>$row){  
        $convertItem .= '<tr><td>'.$rsItem[$key]['name'].'</td><td style="text-align:right">'. $obj->formatNumber($row['qty']).'</td><td>'.$rsItem[$key]['baseunitname'].'</td></tr>' ; 
        $totalItemConvert += $row['qty'];  
    }

    $convertItem .= '</table>' ;
    $convertItem .= '<table cellpadding="4" style="font-weight:bold"><tr><td style="text-align:right;width:510px">Total</td><td style="text-align:right;width:80px">'.$totalItemConvert.'</td></tr></table>' ;
 
    $html .= $convertItem;
}
    
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