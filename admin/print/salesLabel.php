<?php 

includeClass('SalesOrder.class.php');
$salesOrder = createObjAndAddToCol( new SalesOrder()); 

$obj = $salesOrder;

$generateReportContent = function ($dataset){  
$obj = new SalesOrder(); 
$setting = new Setting();
$shipment = new Shipment();
    
$rs = $dataset['rs']; 
    
$arrRecipient = array();
array_push($arrRecipient, $rs[0]['recipientname'], str_replace(chr(13),'<br>',$rs[0]['recipientaddress']), $rs[0]['recipientphone']);
    
    
$arrShipper = array();
    
if($rs[0]['isdropship'] == 1){ 
    array_push($arrShipper, $rs[0]['dropshipername']);  
    array_push($arrShipper, $rs[0]['dropshiperphone']);  
    array_push($arrShipper, str_replace(chr(13),'<br>',$rs[0]['dropshiperaddress']));  
}else{
    $companyName = $obj->loadSetting('companyName');
    $companyAddress = str_replace(chr(13),'<br>',$obj->loadSetting('companyAddress'));
    $companyPhone = $setting->getDetailByCode('companyPhone');

    array_push($arrShipper, $companyName, $companyAddress );

    for($i=0;$i<count($companyPhone);$i++) 
        array_push($arrShipper, $companyPhone[$i]['value']);  
}

$rsShipment = $shipment->getDataRowById($rs[0]['shipmentkey']);
$recipientCourier = $rsShipment[0]['name'];
if($rs[0]['useinsurance'] == 1)
$recipientCourier .= '. Asuransi'; 
 
$html = $obj->printSetting['defaultStyle'];
$html .= ' 
<table cellpadding="2" > 
<tr><td><div class="title">SURAT JALAN</div></td></tr>
<tr><td><div class="subtitle">'.$rs[0]['code'].'</div></td></tr>
</table> 

<div style="clear:both"></div>
<table>
<tr>
<td>
<table cellpadding="10" style="border:1px solid #333">  
<tr>
<td style="width: 340px">
<strong>KEPADA</strong><br>
'.implode('<br>',$arrRecipient).'
</td>
<td style="width: 330px">
<strong>DARI</strong><br>
'.implode('<br>',$arrShipper).' 
</td>
</tr>  
<tr>
<td colspan="2">
<strong>PENGIRIMAN</strong><br>
'.$recipientCourier.'
</td>
</tr>
</table> 
</td>
<td></td>
</tr>
  
<div style="clear:both"></div> ';
   
    
return $html;
}
?>