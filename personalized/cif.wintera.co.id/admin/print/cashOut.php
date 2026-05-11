<?php 
 $pdf->setCustomSettings(
    array(  
         'fontName' => 'Courier', 
         ) 
);


includeClass('CashOut.class.php');
$cashOut = createObjAndAddToCol( new CashOut());

$obj = $cashOut; 

$generateReportContent = function ($dataset){  
    
global $pdf;
    
$obj = new CashOut();
$cashBank = new CashBank();
$chartOfAccount = new ChartOfAccount();
$employee = new Employee();
    
$rs = $dataset['rs'];
$rsDetail = $obj->getDetailWithRelatedInformation($rs[0]['pkey']);
$rsCOA = $chartOfAccount->getDataRowById($rs[0]['coakey']);
$cashBankCode = (ADV_FINANCE) ? $cashBank->getCashBankRef($rs[0]['pkey'],$obj->tableName)['code'] : '';

$rsEmployee = $employee->searchDataRow(array($employee->tableName.'.pkey',$employee->tableName.'.code',$employee->tableName.'.name'), ' and '.$employee->tableName.'.pkey = '.$employee->oDbCon->paramString($rs[0]['createdby']));  

    
$html = $obj->printSetting['defaultStyle'];
$html .= ' 
<table cellpadding="2" > 
<tr><td><div class="title">'.strtoupper($obj->lang['cashOut']).'</div></td></tr>
<tr><td><div class="subtitle">'.$rs[0]['code'].'</div></td></tr>
</table> 

<div style="clear:both"></div>

<table cellpadding="1" >  
<tr><td class="header-row-header">'.$obj->lang['date'].'</td><td style="width:10px; text-align:center">:</td><td style="width: 400px;">'.$obj->formatDBDate($rs[0]['trdate'],'d / m / Y').'</td></tr>';
    
if(ADV_FINANCE)
 $html .= '<tr><td class="header-row-header">'.$obj->lang['voucherNumber'].'</td><td style=" text-align:center">:</td><td>'.$cashBankCode.'</td></tr>';
    
$html .= '<tr><td class="header-row-header">'.$obj->lang['account'].'</td><td style=" text-align:center">:</td><td>'.$rs[0]['coaname'].'</td></tr> 
<tr><td class="header-row-header">'.$obj->lang['recipient'].'</td><td style="text-align:center">:</td><td>'.$rs[0]['recipientname'].'</td></tr> 
</table>   
<div style="clear:both"></div> ';

$cellArray = array();
array_push($cellArray, array('label' => $obj->lang['cost'], 'width' => '190'));
array_push($cellArray, array('label' => $obj->lang['description']));
array_push($cellArray, array('label' => $obj->lang['PPN'],'align' => 'right', 'width' => '110'));
array_push($cellArray, array('label' => 'PPH','align' => 'right', 'width' => '110'));
array_push($cellArray, array('label' => $obj->lang['amount'],'align' => 'right', 'width' => '130'));
  
$html .= '<table  cellpadding="4" class="table-transaction">';
$html .= $obj->generatePrintTableRow( array('class' => 'col-header', 'cell' =>  $cellArray)); 

$beforeTaxTotal = 0;
$totalPPN = 0; // kalo dari header, ad pembulatan
for ($i=0;$i<count($rsDetail);$i++){
            
    $itemName = $rsDetail[$i]['coaname'];
    
    if($obj->useMasterCost)
        $itemName = $rsDetail[$i]['costname'].'<br>'.$itemName;      
    
    $html .= '<tr><td>'.$itemName.'</td><td>'. $rsDetail[$i]['trdesc'] .'</td><td style="text-align:right">'.$obj->formatNumber($rsDetail[$i]['taxvalue']).'</td><td style="text-align:right">'.$obj->formatNumber($rsDetail[$i]['pphvalue']).'</td><td style="text-align:right">'.$obj->formatNumber($rsDetail[$i]['amount']).'</td></tr>' ; 
    
    $beforeTaxTotal += $rsDetail[$i]['beforetax'];
    $totalPPN += $rsDetail[$i]['taxvalue'];
} 
$html .= '</table>' ;

$sayNumber = $obj->sayNumber($rs[0]['grandtotal']);
$html .= '
<div style="clear:both"></div> 
<table cellpadding="4" >';
  
$html .= '<tr><td rowspan="4" style="width: 490px"><strong>'.$obj->lang['say'].'</strong> :<br>'.ucwords($sayNumber).' '.($rs[0]['currencykey'] == CURRENCY['idr'] ? 'Rupiah' : $rs[0]['currencyname'] ).'.</td><td  style="width: 60px"><b>'.$obj->lang['dpp'].'</b></td><td  style="width: 130px; text-align:right;">'.$obj->formatNumber($beforeTaxTotal).'</td></tr>' ; 
$html .= '<tr><td><b>'.$obj->lang['PPN'].'</b></td><td  style="text-align:right;">'.$obj->formatNumber($totalPPN).'</td></tr>' ; 
$html .= '<tr><td><b>PPH</b></td><td  style="text-align:right;">'.$obj->formatNumber($rs[0]['totalpph']).'</td></tr>' ; 
$html .= '<tr><td><b>'.$obj->lang['total'].'</b></td><td  style="text-align:right;">'.$obj->formatNumber($rs[0]['grandtotal']).'</td></tr>' ; 
   
$html .= '</table>
<table cellpadding="4">
<tr><td><strong>Catatan</strong> :<br>'.str_replace(chr(13),'<br>',$rs[0]['trdesc']).'</td></tr>
</table>
<div "clear:both"></div>';

//$html .= $obj->generateSignLabel($rs); 
  
$html .= ' 
        <table cellpadding="4" class="sign">
        <tr>
            <td  class="sign-col"><strong>Prepared</strong></td>
            <td class="sign-col-space"></td>
            <td  class="sign-col"><strong>Approved</strong></td>
            <td class="sign-col-space"></td>
            <td  class="sign-col"><strong>Received</strong></td>
        </tr> 
        <tr>
            <td  class="sign-name">'. $rsEmployee[0]['name'] .'</td>
            <td class="sign-col-space"></td>
            <td  class="sign-name"></td>
            <td class="sign-col-space"></td>
            <td  class="sign-name"></td>
            <td class="sign-col-space"></td>
        </tr> 
        </table>';

return '<div style="font-size:1.1em;font-weight:bold">'.$html.'</div>';
}
?>
