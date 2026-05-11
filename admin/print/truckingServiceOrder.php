<?php 
includeClass(array('TruckingServiceOrder.class.php'));
$truckingServiceOrder = createObjAndAddToCol( new TruckingServiceOrder()); 

$obj = $truckingServiceOrder;
 
$generateReportContent = function ($dataset){ 
 
$obj = new TruckingServiceOrder();  
$item = new Item();
$employee = new Employee();
$security = new Security();
  
$hasSellingPriceAccess = $security->isAdminLogin($obj->sellingPriceSecurityObject,10);  
	
$rs = $dataset['rs']; 


if (!$hasSellingPriceAccess) {  
	$rs[0]['subtotal'] = 0; 
	$rs[0]['totalsellingcost'] = 0; 
	$rs[0]['grandtotal'] = 0; 
}


$rsDetail = $obj->getDetailById($rs[0]['pkey']);
$rsPlanner = $employee->getDataRowById($rs[0]['plannerkey'] );  
$plannerName = (!empty($rsPlanner)) ? $rsPlanner[0]['name'] : '';
$sayNumber = $obj->sayNumber($rs[0]['grandtotal']);
$trnotes = (!empty($rs[0]['trdesc'])) ? '<div style="clear:both"></div><strong>Catatan :</strong> <br>' . str_replace(chr(13),'<br>',$rs[0]['trdesc']) : '';
$html = $obj->printSetting['defaultStyle'];
$html .= ' 
<table cellpadding="2" > 
<tr><td><div class="title">ORDER PENJUALAN</div></td></tr>
<tr><td><div class="subtitle">'.$rs[0]['code'].'</div></td></tr>
</table> 

<div style="clear:both"></div>
 
<table>
<tr>
<td style="width:300px;" >
<table cellpadding="2">
<tr><td class="header-row-header" style="width:120px">Tanggal</td><td style="width:10px; text-align:center">:</td><td style="width:170px">'.$obj->formatDBDate($rs[0]['trdate'],'d / m / Y').'</td></tr>
<tr><td class="header-row-header">'.$obj->lang['si'].'</td><td style="text-align:center">:</td><td>'. $rs[0]['donumber'] .'</td></tr>
<tr><td class="header-row-header">No. Booking</td><td style="text-align:center">:</td><td >'. $rs[0]['shipmentnumber'] .'</td></tr>
<tr><td class="header-row-header">Planner</td><td style="text-align:center">:</td><td>'. $plannerName .'</td></tr>
<tr><td class="header-row-header">Jenis Pekerjaan</td><td style="text-align:center">:</td><td>'. $rs[0]['cargotype'] .' / '.$rs[0]['categoryname'].'</td></tr>
</table>
</td>
<td style="width:370px;"> 
<table cellpadding="2">
<tr><td class="header-row-header" style="width:120px">Pelanggan</td><td style="width:10px; text-align:center">:</td><td style="width:240px">'. $rs[0]['customername'] .'</td></tr>
<tr><td class="header-row-header">Cosignee</td><td style="text-align:center">:</td><td>'. $rs[0]['consigneename'] .'</td></tr>
<tr><td class="header-row-header">Lokasi Stuffing</td><td style="text-align:center">:</td><td>'.$rs[0]['locationname'].'</td></tr>
<tr><td class="header-row-header">Gudang</td><td style="text-align:center">:</td><td>'.$rs[0]['consigneewarehousename'].'</td></tr>
<tr><td class="header-row-header">Alamat</td><td style="text-align:center">:</td><td >'.str_replace(chr(13),'<br>',$rs[0]['consigneeaddress']).'</td></tr>
<tr><td class="header-row-header">Depot / Terminal</td><td style="text-align:center">:</td><td>'.$rs[0]['depotname'].' / '.$rs[0]['terminalname'].'</td></tr>
</table>
</td>
</tr>
</table>

<div style="clear:both"></div>
<table cellpadding="4" class="table-transaction">
<tr class="col-header"><td style="text-align:left;width:30px">No</td><td style="text-align:right;width:60px">Partai</td><td style="text-align:left;width:300px">Layanan</td><td style="text-align:right;width:140px">Harga</td><td style="text-align:right;width:140px">Jumlah</td></tr>  
';


for($i=0;$i<count($rsDetail);$i++){ 
    $rsItem = $item->getDataRowById($rsDetail[$i]['itemkey']);   
	

	if (!$hasSellingPriceAccess) { 
		$rsDetail[$i]['priceinunit'] = 0; 
		$rsDetail[$i]['total'] = 0; 
	}
            
	
    $html .= '<tr><td style="text-align:right">'.($i+1).'.</td><td style ="text-align:right">'.$obj->formatNumber($rsDetail[$i]['qtyinbaseunit']).'</td><td>'.$rsItem[0]['name'].'</td><td style ="text-align:right">'.$obj->formatNumber($rsDetail[$i]['priceinunit']).'</td><td style ="text-align:right">'.$obj->formatNumber($rsDetail[$i]['total']).'</td></tr>';
}  

    
$html .= '    
</table>  
<div style="clear:both"></div> 
<table cellpadding="4"> 
<tr><td rowspan="3" style="width:430px"><strong>Terbilang</strong> :<br>'.ucwords($sayNumber).' Rupiah.</td><td style="text-align:right; font-weight:bold;  width:130px; ">Subtotal</td><td style="text-align:right; font-weight:bold;  width:110px;"  >'.$obj->formatNumber($rs[0]['subtotal']).'</td></tr>
<tr><td style="text-align:right; font-weight:bold; ">Biaya Tambahan</td><td style="text-align:right; font-weight:bold;"  >'.$obj->formatNumber($rs[0]['totalsellingcost']).'</td></tr>
<tr><td style="text-align:right; font-weight:bold;">Total</td><td style="text-align:right; font-weight:bold;"  >'.$obj->formatNumber($rs[0]['grandtotal']).'</td></tr>
</table>
<div style="clear:both"></div>   
'.$trnotes.'
<div style="clear:both"></div>  
';
  
$html .= $obj->generateSignLabel($rs); 
return $html;
}

?>