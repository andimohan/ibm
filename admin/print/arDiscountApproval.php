<?php 
includeClass('ARDiscountApproval.class.php');
$arDiscountApproval = createObjAndAddToCol( new ARDiscountApproval());
$arPayment = createObjAndAddToCol( new ARPayment());

$obj = $arDiscountApproval;
$generateReportContent = function ($dataset){ 

$obj = new ARDiscountApproval();  
$arPayment = new ARPayment();
$ar = $obj->getARObj();
    
$rs = $dataset['rs'];
$rsDetail = $obj->getDetailWithRelatedInformation($rs[0]['pkey']);    
$rsCost = $obj->getCostDetail($rs[0]['pkey']); 
    
    
$html = $obj->printSetting['defaultStyle'];
$html .= ' 
<table cellpadding="2" > 
<tr><td><div class="title">PERSETUJUAN PEMOTONGAN PIUTANG</div></td></tr>
<tr><td><div class="subtitle">'.$rs[0]['code'].'</div></td></tr>
</table> 

<div style="clear:both"></div>
 
<table cellpadding="2"> 
<tr><td class="header-row-header">Tanggal</td><td style="width:10px; text-align:center">:</td><td style="width:540px;">'.$obj->formatDBDate($rs[0]['trdate'],'d / m / Y').'</td></tr>  
<tr><td class="header-row-header">Pelanggan</td><td style="text-align:center">:</td><td>'. $rs[0]['customername'] .'</td></tr>    
<tr><td class="header-row-header">Total Cost</td><td style="text-align:center">:</td><td>'.$obj->formatNumber($rs[0]['totalcost']).'</td></tr>    
<tr><td class="header-row-header">Total Discount</td><td style="text-align:center">:</td><td>'. $obj->formatNumber($rs[0]['totaldiscount']).'</td></tr>    
</table> 
 
<div style="clear:both"></div> ';

$cellArray = array();
array_push($cellArray, array('label' => $obj->lang['arCode'], 'width' => '100')); 
array_push($cellArray, array('label' => $obj->lang['reference'])); 
array_push($cellArray, array('label' => $obj->lang['si'],'width' => '150')); 
array_push($cellArray, array('label' => $obj->lang['amount'],'align' => 'right', 'width' => '80'));
array_push($cellArray, array('label' => $obj->lang['outstanding'],'align' => 'right', 'width' => '80'));
array_push($cellArray, array('label' => $obj->lang['discount'],'align' => 'right', 'width' => '80'));

$html .= '<table  cellpadding="4" class="table-transaction">';
$html .= $obj->generatePrintTableRow( array('class' => 'col-header', 'cell' =>  $cellArray)); 
 
for ($i=0;$i<count($rsDetail);$i++){   
    $rsAr = $ar->getDataRowById($rsDetail[$i]['arkey']);

    $refCode = array();
    array_push($refCode, $rsAr[0]['refcode']) ;

  $html .= '<tr>
  <td>'.$rsDetail[$i]['arcode'].'</td><td>'.implode(', ', $refCode).'</td> <td>'.$rsAr[0]['refcode2'].'</td> 
  <td style="text-align:right">'.$obj->formatNumber($rsAr[0]['amount']).'</td>
  <td style="text-align:right">'.$obj->formatNumber($rsDetail[$i]['outstanding']).'</td>
  <td style="text-align:right">'.$obj->formatNumber($rsDetail[$i]['discount']).'</td>
  </tr>' ; 
}
$html .= '</table><div style="clear:both"></div><div style="clear:both"></div> ' ;
    
   
$cellArray = array();
array_push($cellArray, array('label' => $obj->lang['costName'],)); 
array_push($cellArray, array('label' => $obj->lang['amount'],'align' => 'right', 'width' => '80'));

$html .= '<table  cellpadding="4" class="table-transaction">';
$html .= $obj->generatePrintTableRow( array('class' => 'col-header', 'cell' =>  $cellArray)); 
 
for ($i=0;$i<count($rsCost);$i++){   


  $html .= '<tr>
  <td>'.$rsCost[$i]['costname'].'</td>
  <td style="text-align:right">'.$obj->formatNumber($rsCost[$i]['amount']).'</td>
  </tr>' ; 
}
$html .= '</table><div style="clear:both"></div>' ;

//    
//$html .= '    
//</table>  
//<div style="clear:both"></div> 
//<table cellpadding="4"> 
//<tr><td rowspan="'.(count($arrSubtotal)+1).'" style="width:450px;"><strong>Terbilang</strong> :<br>'.ucwords($sayNumber).' Rupiah. <br>'.$payment.'</td><td style="text-align:right; font-weight:bold;  width:120px; ">'.$subtotalLabel.'</td><td style="text-align:right; font-weight:bold;  width:110px;"  >'.$obj->formatNumber($rs[0]['totalreceived']).'</td></tr>
//';


$html .= implode('',$arrSubtotal); 
    
$html .= '</table>';
 

   

$html .= '
<table cellpadding="4">
<tr><td><strong>Catatan</strong> :<br>'.str_replace(chr(13),'<br>',$rs[0]['trnotes']).'</td></tr>
</table>
<div "clear:both"></div>';

$html .= $obj->generateSignLabel($rs); 
    
return $html;
}
?>