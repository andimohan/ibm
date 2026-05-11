<?php 
 
$obj = $cashBankIn; 

$generateReportContent = function ($dataset){  
    
global $pdf;
    
$obj = new CashBankIn();
$cashBank = new CashBank();
$chartOfAccount = new ChartOfAccount();
    
$rs = $dataset['rs'];
$rsDetail = $obj->getDetailWithRelatedInformation($rs[0]['pkey']);
$rsCOA = $chartOfAccount->getDataRowById($rs[0]['coakey']);
 
$html = $obj->printSetting['defaultStyle'];
$html .= ' 
<table cellpadding="2" > 
<tr><td><div class="title">'.strtoupper($obj->lang['cashBankIn']).'</div></td></tr>
<tr><td><div class="subtitle">'.$rs[0]['code'].'</div></td></tr>
</table> 

<div style="clear:both"></div>

<table cellpadding="2" >  
<tr><td class="header-row-header">'.$obj->lang['date'].'</td><td style="width:10px; text-align:center">:</td><td>'.$obj->formatDBDate($rs[0]['trdate'],'d / m / Y').'</td></tr>';

$html .= '<tr><td class="header-row-header">'.$obj->lang['cashBank'].'</td><td style=" text-align:center">:</td><td>'.$rs[0]['codename'].'</td></tr> 
</table>   
<div style="clear:both"></div> ';

$cellArray = array();
array_push($cellArray, array('label' => $obj->lang['customer'], 'width' => '180'));
array_push($cellArray, array('label' => $obj->lang['note']));
array_push($cellArray, array('label' => $obj->lang['transactionType'], 'width' => '100')); 
array_push($cellArray, array('label' => $obj->lang['cashBankNumber'], 'width' => '120'));
array_push($cellArray, array('label' => $obj->lang['amount'],'align' => 'right', 'width' => '80'));
  
$html .= '<table  cellpadding="4" class="table-transaction">';
$html .= $obj->generatePrintTableRow( array('class' => 'col-header', 'cell' =>  $cellArray)); 

for ($i=0;$i<count($rsDetail);$i++){

    $rsCashBank = $cashBank->getCashBankRef($rs[0]['pkey'],$obj->tableName,$rs[0]['coakey'],$rsDetail[$i]['pkey']); 
    
    $html .= '<tr><td>'.$rsDetail[$i]['customername'].'</td>
                  <td>'. $rsDetail[$i]['trdesc'] .'</td>
                  <td>'. ((!empty($rsDetail[$i]['revenuename'])) ? $rsDetail[$i]['revenuename'] : $obj->lang['temporaryAccount']) .'</td>
                  <td style="">'.$rsCashBank['code'].'</td>
                  <td style="text-align:right">'.$obj->formatNumber($rsDetail[$i]['amount']).'</td>
            </tr>' ; 
    
} 
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