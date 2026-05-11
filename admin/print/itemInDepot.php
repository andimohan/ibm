<?php 

$obj = $itemInDepot;
 
$generateReportContent = function ($dataset){ 

$obj = new ItemInDepot(); 
$depot = new Depot();    
$supplier = new Supplier();    
$customer = new Customer();    
    
$rs = $dataset['rs'];
$rsDetail = $obj->getDetailWithRelatedInformation($rs[0]['pkey']); 
    
$rsDepot = $depot->getDataRowById($rs[0]['depotkey']);
$rsTrucking = $supplier->getDataRowById($rs[0]['truckingvendorkey']);
$rsCustomer = $customer->getDataRowById($rs[0]['customerkey']);
    
$html = $obj->printSetting['defaultStyle'];
$html .= ' 
<table cellpadding="2" > 
<tr><td><div class="title">'.$obj->lang['itemIn'].' '.$obj->lang['depot'].'</div></td></tr>
<tr><td><div class="subtitle">'.$rs[0]['code'].'</div></td></tr>
</table> 

<div style="clear:both"></div>
  
<table>
<tr>
<td style="width:330px">
<table cellpadding="2"> 
<tr><td class="header-row-header" style="width:120px">'.$obj->lang['date'].'</td><td style="width:10px; text-align:center">:</td><td style="width:200px">'.$obj->formatDBDate($rs[0]['trdate'],'d / m / Y').'</td></tr>  
<tr><td class="header-row-header">'.$obj->lang['depot'].'</td><td style="width:10px; text-align:center">:</td><td>'.$rsDepot[0]['name'].'</td></tr>  
<tr><td class="header-row-header">'.$obj->lang['doCode'].'</td><td style="width:10px; text-align:center">:</td><td>'. $rs[0]['docode'] .'</td></tr>
</table>
</td>
<td style="width:340px">
<table cellpadding="2">
<tr><td class="header-row-header" style="width:120px">'.$obj->lang['customer'].'</td><td style="width:10px; text-align:center">:</td><td style="width:210px;">'.$rsCustomer[0]['name'].'</td></tr> 
<tr><td class="header-row-header">'.$obj->lang['trucking'].'</td><td style="width:10px; text-align:center">:</td><td>'.$rsTrucking[0]['name'].'</td></tr> 
<tr><td class="header-row-header">'.$obj->lang['carRegistrationNumber'].'</td><td style="width:10px; text-align:center">:</td><td>'.$rs[0]['policenumber'].'</td></tr> 
</table>
</td>
</tr>
</table> 
<div style="clear:both"></div> ';

$html .= ' 
<table  cellpadding="4" class="table-transaction">';
    
//$html .= '<tr class="col-header" ><td style="width:480px;">Item</td><td style="text-align:right; width:110px;">Jumlah</td><td style="width:80px;">Unit</td></tr>';

$cellArray = array ();
array_push($cellArray, array('label' => $obj->lang['itemName']));
array_push($cellArray, array('label' => $obj->lang['qty'], 'align' => 'right', 'width' => '100'));
array_push($cellArray, array('label' => $obj->lang['unit'], 'width' => '120'));
$html .= $obj->generatePrintTableRow( array('class' => 'col-header','docWidth' => '670', 'cell' =>  $cellArray));     
    
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