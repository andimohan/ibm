<?php 

includeClass('AREmployee.class.php');
$arEmployee = createObjAndAddToCol( new AREmployee());
$obj = $arEmployee;
 
$generateReportContent = function ($dataset){ 
global $pdf;
	
$obj = new AREmployee(); 
$employee = new Employee();    
    
$rs = $dataset['rs'];
$rsHeader = $obj->getDataRowById($rs[0]['pkey']); 
$rsEmployee = $employee->getDataRowById($rsHeader[0]['customerkey']); 
$sayNumber = $obj->sayNumber($rs[0]['amount']);

$html = $obj->printSetting['defaultStyle'];
$html .= ' 
<table cellpadding="2" > 
<tr><td><div class="title">'.$obj->lang['employeeAR'].'</div></td></tr>
<tr><td><div class="subtitle">'.$rs[0]['code'].'</div></td></tr>
</table> 

<div style="clear:both"></div>

<table cellpadding="2">  
<tr><td style="width:100px; font-weight:bold">'.$obj->lang['date'].'</td><td style="width:10px; text-align:center">:</td><td style="width:570px">'.$obj->formatDBDate($rs[0]['trdate'],'d / m / Y').'</td></tr> 
<tr><td style="font-weight:bold">'.$obj->lang['employee'].'</td><td style="text-align:center">:</td><td>'.$rsEmployee[0]['name'].'</td></tr>
<tr><td style="font-weight:bold">'.$obj->lang['quantity'].'</td><td style="text-align:center">:</td><td>'.$obj->formatNumber($rsHeader[0]['amount']).'</td></tr>  
<tr><td style="font-weight:bold">'.$obj->lang['saidAmount'].'</td><td style="text-align:center">:</td><td>'.ucwords($sayNumber).'</td></tr>  
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
