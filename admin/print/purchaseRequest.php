<?php 

includeClass(array('PurchaseRequest.class.php','Supplier.class.php'));
$purchaseRequest = createObjAndAddToCol(new PurchaseRequest());

$obj = $purchaseRequest;
$generateReportContent = function ($dataset){ 

$obj = new PurchaseRequest();
$supplier = new Supplier();
    
$rs = $dataset['rs'];
$rsSupplier = $supplier->getDataRowById($rs[0]['supplierkey']);
$rsDetail = $obj->getDetailWithRelatedInformation($rs[0]['pkey']);

$html = $obj->printSetting['defaultStyle'];
$html .= ' 
<table cellpadding="2" > 
<tr><td><div class="title">'.$obj->lang['purchaseRequest'].'</div></td></tr>
<tr><td><div class="subtitle">'.$rs[0]['code'].'</div></td></tr>
</table> 

<div style="clear:both"></div>
 
<table cellpadding="2"> 
<tr><td class="header-row-header">'.$obj->lang['date'].'</td><td style="width:10px; text-align:center">:</td><td style="width: 500px;">'.$obj->formatDBDate($rs[0]['trdate'],'d / m / Y').'</td></tr>
<tr><td class="header-row-header">'.$obj->lang['supplier'].'</td><td style="text-align:center">:</td><td>'.$rsSupplier[0]['name'].'</td></tr>
</table> 

<div style="clear:both"></div>  
<table  cellpadding="4" class="table-transaction">';
     
$cellArray = array ();
array_push($cellArray, array('label' => $obj->lang['item']));
array_push($cellArray, array('label' => $obj->lang['amount'], 'width' => '70', 'align' => 'right'));
array_push($cellArray, array('label' => $obj->lang['unit'], 'width' => '70')); 
array_push($cellArray, array('label' => $obj->lang['price'].' @', 'width' => '70', 'align' => 'right')); 
array_push($cellArray, array('label' => $obj->lang['subtotal'], 'width' => '100', 'align' => 'right')); 
$html .= $obj->generatePrintTableRow( array('class' => 'col-header','docWidth' => '680', 'cell' =>  $cellArray));  
     
for ($i=0;$i<count($rsDetail);$i++){  
  $html .= '<tr><td>'.$rsDetail[$i]['itemname'].'</td><td style="text-align:right">'.$obj->formatNumber($rsDetail[$i]['qty']).'</td><td>'. $rsDetail[$i]['unitname'] .'</td><td style="text-align:right">'.$obj->formatNumber($rsDetail[$i]['priceinunit']).'</td><td style="text-align:right">'.$obj->formatNumber($rsDetail[$i]['subtotal']).'</td></tr>'; 
}
$html .= '</table>
<div style="clear:both"></div> 
<table cellpadding="4">
<tr><td><strong>'.$obj->lang['note'].'</strong> :<br>'.str_replace(chr(13),'<br>',$rs[0]['trdesc']).'</td></tr>
</table>
<div "clear:both"></div>';
$html .= $obj->generateSignLabel($rs); 
    
return $html;
}
?>