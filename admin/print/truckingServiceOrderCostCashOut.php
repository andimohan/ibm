<?php   
includeClass(array('TruckingServiceOrder.class.php'));
$truckingServiceOrder = createObjAndAddToCol( new TruckingServiceOrder());  

$obj = $truckingServiceOrder;

$generateReportContent = function ($dataset){ 

$obj = new TruckingServiceOrder();  
$item = new Item();
$employee = new Employee();
$truckingCostCashOut = new TruckingCostCashOut();
  
$rs = $dataset['rs']; 
     
$rsDetail = $obj->getHeaderCost($rs[0]['pkey']); 
$rsPlanner = $employee->getDataRowById($rs[0]['plannerkey']); 
    
$rsTableKey = $obj->getTableKeyAndObj($obj->tableName); 
    
$rsCashOut = $truckingCostCashOut->searchData($truckingCostCashOut->tableName.'.refkey',$rs[0]['pkey'],true,' and '.$truckingCostCashOut->tableName.'.reftabletype = '.$rsTableKey['key'].' and '. $truckingCostCashOut->tableName.'.statuskey = 1','order by pkey desc');

$html = $obj->printSetting['defaultStyle'];
     
//$recipientName = $rsPlanner[0]['name'];
 
if (empty($rsCashOut)) return '<div style="text-align:center">'.$obj->errorMsg[213].'</div>';
    
$rsTruckingCost = $truckingCostCashOut->getDetailWithRelatedInformation($rsCashOut[0]['pkey']); 

if (empty($rsTruckingCost))  return ''; 

$totalCost = $rsCashOut[0]['total'];   
    
    

$depotname = (!empty($rs[0]['depotname'])) ? $rs[0]['depotname'] : ' - ';
$terminalname = (!empty($rs[0]['terminalname'])) ? $rs[0]['terminalname'] : ' - ';
$recipientName = (!empty($rsPlanner[0]['name'])) ? $rsPlanner[0]['name'] : ' ';
$stuffingAdress = (!empty($rs[0]['stuffingaddress'])) ? $rs[0]['stuffingaddress'] : ' ';
$customerName = (!empty($rs[0]['consigneename'])) ? $rs[0]['consigneename']  : $rs[0]['customername'];

$html = $obj->printSetting['defaultStyle'];
$html .= ' 
<table cellpadding="2" > 
<tr><td><div class="title">VOUCHER PENGELUARAN KAS</div></td></tr>
<tr><td><div class="subtitle">'.$rsCashOut[0]['code'].' / '.$rs[0]['code'].'</div></td></tr>
</table> 

<div style="clear:both"></div>
<table>
<tr>
<td style="width:300px;" >
<table cellpadding="2"> 
<tr><td class="header-row-header"  style="width:120px">Tanggal</td><td style="width:10px; text-align:center">:</td><td style="width:170px">'.$obj->formatDBDate($rsCashOut[0]['trdate'],'d / m / Y').'</td></tr>  
<tr><td class="header-row-header">No. Order</td><td style="width:10px; text-align:center">:</td><td>'. $rs[0]['code'] .'</td></tr> 
<tr><td class="header-row-header">S / I</td><td style="width:10px; text-align:center">:</td><td>'. $rs[0]['donumber'] .'</td></tr>  
<tr><td class="header-row-header">Booking Pelayaran</td><td style="width:10px; text-align:center">:</td><td>'. $rs[0]['shipmentnumber'] .'</td></tr>   
<tr><td class="header-row-header">Jenis Pekerjaan</td><td style="width:10px; text-align:center">:</td><td>'. $rs[0]['categoryname'] .'</td></tr>   
<tr><td class="header-row-header">Penerima</td><td style="width:10px; text-align:center">:</td><td>'. $recipientName.'</td></tr>   
</table> 
</td>
<td style="width:370px;">
<table cellpadding="2" >
<tr><td class="header-row-header" style="width:120px">Shipper</td><td style="width:10px; text-align:center">:</td><td style="width:240px;">'.$customerName.'</td></tr> 
<tr><td class="header-row-header">Lokasi Stuffing</td><td style="width:10px; text-align:center">:</td><td>'.$rs[0]['locationname'].'</td></tr> 
<tr><td class="header-row-header">Alamat</td><td style="width:10px; text-align:center">:</td><td>'.str_replace(chr(13),'<br>',$stuffingAdress).'</td></tr> 
<tr><td class="header-row-header"></td></tr>  
<tr><td class="header-row-header">Depo / Terminal</td><td style="width:10px; text-align:center">:</td><td>'.$depotname.' / '.$terminalname.'</td></tr>   
</table>
</td>
</tr>
</table>
<div style="clear:both"></div>  
';

$html .= '<table cellpadding="4" style="width:670px"  class="table-transaction">';
/*$html .= '
<tr class="col-header">
<td style="width:30px; text-align:right">No.</td>
<td style="width:240px" >Keterangan</td>
<td style="width:100px" >Size</td>
<td style="width:100px" >No. Container</td>
<td style="width:200px; text-align:right" >Biaya</td>
</tr>';

$totalCost = 0;
$ctr = 0;
for($i=0;$i<count($rsTruckingCost);$i++){

    //$rsCostValue = $obj->getCostDetail($rs[0]['pkey'], $rsCost[$i]['pkey']);
    //$cost = (!empty($rsCostValue)) ? $rsCostValue[0]['requestamount'] : 0;
    $cost = $rsTruckingCost[$i]['amount'];
    $totalCost += $cost;

   if ($cost == 0)
       continue;

    $html .= '
    <tr>
    <td style="text-align:right">'.(++$ctr).'.</td>
    <td>'.$rsTruckingCost[$i]['costname'].'</td>
    <td>'.$rsService[0]['name'].'</td>
    <td></td>
    <td style="text-align:right" >'.$obj->formatNumber($cost).'</td>
    </tr>';
}*/
    
$cellArray = array ();
array_push($cellArray, array('label' => $obj->lang['number'], 'align' => 'right','width' => '40'));
array_push($cellArray, array('label' => $obj->lang['cost'], 'width' => '200'));
array_push($cellArray, array('label' => $obj->lang['description']));
array_push($cellArray, array('label' => $obj->lang['amount'], 'align' => 'right', 'width' => '140'));
$html .= $obj->generatePrintTableRow( array('class' => 'col-header','docWidth' => '670', 'cell' =>  $cellArray));  

for($i=0;$i<count($rsTruckingCost);$i++){ 
    //$rsItem = $item->getDataRowById($rsDetail[$i]['costkey']);   
    $html .= '<tr><td style="text-align:right">'.($i+1).'</td> <td>'.$rsTruckingCost[$i]['costname'].'</td><td>'.$rsTruckingCost[$i]['description'].'</td><td style ="text-align:right">'.$obj->formatNumber($rsTruckingCost[$i]['amount']).'</td></tr>';
}

$sayNumber = $obj->sayNumber($totalCost);    

$html .= '</table>'; 

$html .= ' 
<table cellpadding="4"> 
<tr><td style="width:460px"><strong>Terbilang</strong> :<br>'.ucwords($sayNumber).' Rupiah.</td><td style="text-align:right; font-weight:bold;  width:100px; ">Total</td><td style="text-align:right; font-weight:bold;  width:110px;"  >'.$obj->formatNumber($totalCost).'</td></tr>
</table>
';  

$html .= '  
<div style="clear:both"></div>  
'; 

$html .= $obj->generateSignLabel($rs);  
 

return $html;
}
?>