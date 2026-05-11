<?php 
 
$obj = $cashOut; 

$generateReportContent = function ($dataset){  
    
global $pdf;
    
$obj = new CashOut();
$chartOfAccount = new ChartOfAccount();
    
$rs = $dataset['rs'];
$rsDetail = $obj->getDetailWithRelatedInformation($rs[0]['pkey']);
$rsCOA = $chartOfAccount->getDataRowById($rs[0]['coakey']);
    
$html = $obj->printSetting['defaultStyle'];
$html .= ' 
<table cellpadding="2" > 
<tr><td><div class="title">'.strtoupper($obj->lang['cashOut']).'</div></td></tr>
<tr><td><div class="subtitle">'.$rs[0]['code'].'</div></td></tr>
</table> 

<div style="clear:both"></div>

<table cellpadding="2" >  
<tr><td class="header-row-header">'.$obj->lang['date'].'</td><td style="width:10px; text-align:center">:</td><td>'.$obj->formatDBDate($rs[0]['trdate'],'d / m / Y').'</td></tr> 
<tr><td class="header-row-header">'.$obj->lang['account'].'</td><td style=" text-align:center">:</td><td>'.$rs[0]['coaname'].'</td></tr> 
<tr><td class="header-row-header">'.$obj->lang['recipient'].'</td><td style="text-align:center">:</td><td>'.$rs[0]['recipientname'].'</td></tr> 
</table>   
<div style="clear:both"></div> ';

$cellArray = array();
array_push($cellArray, array('label' => $obj->lang['account'], 'width' => '225'));
array_push($cellArray, array('label' => $obj->lang['description']));
array_push($cellArray, array('label' => $obj->lang['amount'],'align' => 'right', 'width' => '100'));
  
$html .= '<table  cellpadding="4" class="table-transaction">';
$html .= $obj->generatePrintTableRow( array('class' => 'col-header', 'cell' =>  $cellArray)); 

for ($i=0;$i<count($rsDetail);$i++) 
  $html .= '<tr><td>'.$rsDetail[$i]['coaname'].'</td><td>'. $rsDetail[$i]['trdesc'] .'</td><td style="text-align:right">'.$obj->formatNumber($rsDetail[$i]['amount']).'</td></tr>' ; 
 
$html .= '</table>' ;

$sayNumber = $obj->sayNumber($rs[0]['grandtotal']);
$html .= '
<div style="clear:both"></div> 
<table cellpadding="4">';
  
$cellArray = array ();
array_push($cellArray, array('label' => '<strong>'.$obj->lang['say'].'</strong> :<br>'.ucwords($sayNumber).' Rupiah.'));
array_push($cellArray, array('label' => $obj->lang['total'],'align' => 'right', 'width' => '80','style' => 'font-weight:bold'));
array_push($cellArray, array('label' => $rs[0]['grandtotal'],'align' => 'right', 'format' => 'number', 'width' => '100'));
$html .= $obj->generatePrintTableRow( array('cell' =>  $cellArray));  
    
$html .= '</table>
<table cellpadding="4">
<tr><td><strong>Catatan</strong> :<br>'.str_replace(chr(13),'<br>',$rs[0]['trdesc']).'</td></tr>
</table>
<div "clear:both"></div>';

$html .= $obj->generateSignLabel($rs); 
    
return $html;
}
?>