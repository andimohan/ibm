<?php 

includeClass('CashAdvance.class.php');
$cashAdvance = createObjAndAddToCol( new CashAdvance()); 

$obj = $cashAdvance;
$generateReportContent = function ($dataset){ 
$obj = new CashAdvance(); 
$warehouse = new Warehouse();    
$employee = new Employee();    
$chartOfAccount = new ChartOfAccount();    
$coaLink = new COALink();    
$cashBank = new CashBank();
    
$rs = $dataset['rs'];
$rsWarehouse = $warehouse->getDataRowById($rs[0]['warehousekey']); 
$rsCOA = $chartOfAccount->getDataRowById($rs[0]['coakey']);
$rsEmployee = $employee->getDataRowById($rs[0]['employeekey']);
$coakey = '';	

$rsCOAEmployee = $chartOfAccount->getDataRowById($rs[0]['cashadvancecoakey']);
$cashBankCode = (ADV_FINANCE) ? $cashBank->getCashBankRef($rs[0]['pkey'],$obj->tableName)['code'] : ''; 

$html = $obj->printSetting['defaultStyle'];
$html .= ' 
<table cellpadding="2" > 
<tr><td><div class="title">'.$obj->lang['cashAdvance'].'</div></td></tr>
<tr><td><div class="subtitle">'.$rs[0]['code'].'</div></td></tr>
</table> 
<div style="clear:both"></div> 
<table cellpadding="2">  
<tr><td class="header-row-header">'.$obj->lang['date'].'</td><td style="width:10px; text-align:center">:</td><td style="width:510px">'.$obj->formatDBDate($rs[0]['trdate'],'d / m / Y').'</td></tr> 
<tr><td class="header-row-header">'.$obj->lang['warehouse'].'</td><td style="text-align:center">:</td><td>'.$rsWarehouse[0]['name'].'</td></tr>
';
	
if(ADV_FINANCE)
 $html .= '<tr><td class="header-row-header">'.$obj->lang['voucherNumber'].'</td><td style=" text-align:center">:</td><td>'.$cashBankCode.'</td></tr>';
	
$html .= '<tr><td class="header-row-header">'.$obj->lang['cashBankAccount'].'</td><td style="text-align:center">:</td><td>'.$rsCOA[0]['code'].' - '.$rsCOA[0]['name'].'</td></tr>
<tr><td class="header-row-header">'.$obj->lang['recipient'].'</td><td style="text-align:center">:</td><td>'.$rsEmployee[0]['name'].'</td></tr>
<tr><td class="header-row-header">'.$obj->lang['recipientAccount'].'</td><td style="text-align:center">:</td><td>'.$rsCOAEmployee[0]['code'].'-'.$rsCOAEmployee[0]['name'].'</td></tr>
<tr><td class="header-row-header">'.$obj->lang['amount'].'</td><td style="text-align:center">:</td><td>'.$obj->formatNumber($rs[0]['amount']).'</td></tr>  
</table>  
<div style="clear:both"></div> 
<table cellpadding="4">
<tr><td><strong>Catatan</strong> :<br>'.str_replace(chr(13),'<br>',$rs[0]['trdesc']).'</td></tr>
</table>
<div "clear:both"></div>';

$html .= $obj->generateSignLabel($rs);
return $html;

}

?>
