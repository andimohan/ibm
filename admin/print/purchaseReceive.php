<?php 
$obj = $purchaseReceive;
$generateReportContent = function ($dataset){ 

$obj = new PurchaseReceive();  
$supplier = new Supplier();
$purchaeOrder = new PurchaseOrder();
    
$rs = $dataset['rs'];
$rsDetail = $obj->getDetailWithRelatedInformation($rs[0]['pkey']);   
$rsPO = $purchaeOrder->getDataRowById($rs[0]['refkey']);
$rsSupplier = $supplier->getDataRowById($rsPO[0]['supplierkey']);

$arrRecipient = array();
array_push($arrRecipient, $rsSupplier[0]['name']);
if (!empty($rsSupplier[0]['address1'])) array_push($arrRecipient, $rsSupplier[0]['address1']);
if (!empty($rsSupplier[0]['address2'])) array_push($arrRecipient, $rsSupplier[0]['address2']); 
    
if (!empty($rsSupplier[0]['citykey'])){
    $rsCity = $city->searchData('city.pkey',$rsSupplier[0]['citykey'],true);
    $cityname = $rsCity[0]['name'] .', ' . $rsCity[0]['categoryname'];
    
    array_push($arrRecipient, $cityname); 
}
if (!empty($rsSupplier[0]['zipcode'])) array_push($arrRecipient, $rsSupplier[0]['zipcode']); 
if (!empty($rsSupplier[0]['phone'])) array_push($arrRecipient, $rsSupplier[0]['phone']); 
if (!empty($rsSupplier[0]['mobile'])) array_push($arrRecipient, $rsSupplier[0]['mobile']); 

    
$html = $obj->printSetting['defaultStyle'];
$html .= ' 
<table cellpadding="2" > 
<tr><td><div class="title">PENERIMAAN PEMBELIAN</div></td></tr>
<tr><td><div class="subtitle">'.$rs[0]['code'].'</div></td></tr>
</table> 

<div style="clear:both"></div>
<table>
<tr>
<td>
<table cellpadding="2"> 
<tr><td class="header-row-header">Tanggal</td><td style="width:10px; text-align:center">:</td><td>'.$obj->formatDBDate($rs[0]['trdate'],'d / m / Y').'</td></tr>   
<tr><td class="header-row-header">Kode PO</td><td style="width:10px; text-align:center">:</td><td>'.$rsPO[0]['code'].'</td></tr>  
<tr><td colspan="3" class="header-row-header"></td></tr> 
<tr><td colspan="3" class="header-row-header">Kepada Yth.</td></tr> 
<tr><td colspan="3">'.implode('<br>',$arrRecipient).'</td></tr>  
</table> 
</td>
<td></td>
</tr>
  
<div style="clear:both"></div> ';

$html .= ' 
<table  cellpadding="4" class="table-transaction">
<tr class="col-header" ><td style="width:510px;">Item</td><td style="width:80px;text-align:right" >Jumlah</td><td style="width:80px;" >Unit</td></tr>';

for ($i=0;$i<count($rsDetail);$i++){    
  $html .= '<tr><td>'.$rsDetail[$i]['itemname'].'</td><td style="text-align:right">'.$obj->formatNumber($rsDetail[$i]['receivedqty']).'</td><td>'. $rsDetail[$i]['unitname'] .'</td></tr>' ; 
}
$html .= '</table>' ;
   
$html .= '<table cellpadding="4">
<tr><td><strong>Catatan</strong> :<br>'.str_replace(chr(13),'<br>',$rs[0]['trdesc']).'</td></tr>
</table>
<div "clear:both"></div>';

$html .= $obj->generateSignLabel($rs); 
    
return $html;
}
?>
