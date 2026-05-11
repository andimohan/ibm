<?php  
 
includeClass('ARAPEmployeeNetting.class.php');
$arapEmployeeNetting = createObjAndAddToCol( new ARAPEmployeeNetting()); 

$obj = $arapEmployeeNetting;
$generateReportContent = function ($dataset){  

$obj = new ARAPEmployeeNetting();  
$ar = $obj->getARObj();
$ap = $obj->getAPObj();
$employee = new Employee();
    
$rs = $dataset['rs']; 

$rsARPaymentDetail = $obj->getDetailARAP($obj->arapConstant['ar'],$rs[0]['pkey']);
$rsAPPaymentDetail = $obj->getDetailARAP($obj->arapConstant['ap'],$rs[0]['pkey']);
$rsEmployee = $employee->getDataRowById($rs[0]['employeekey']);
    
$totalDifference = $rs[0]['totalar'] - $rs[0]['totalap'];     
$period = ($rs[0]['usedateperiod']==1) ? $obj->formatDBDate($rs[0]['startdateperiod'],'d / m / Y').' - '.$obj->formatDBDate($rs[0]['enddateperiod'],'d / m / Y') : '-';
 
$html = $obj->printSetting['defaultStyle'];
$html .= ' 
<table cellpadding="2" > 
<tr><td><div class="title">ARAP Employee Netting</div></td></tr>
<tr><td><div class="subtitle">'.$rs[0]['code'].'</div></td></tr>
</table> 

<div style="clear:both"></div>
 
<table cellpadding="2"> 
<tr><td class="header-row-header">'.ucwords($obj->lang['date']).'</td><td style="width:10px; text-align:center">:</td><td style="width: 550px">'.$obj->formatDBDate($rs[0]['trdate'],'d / m / Y').'</td></tr>
<tr><td class="header-row-header">'.ucwords($obj->lang['employee']).'</td><td style="text-align:center">:</td><td>'.$rsEmployee[0]['name'].'</td></tr>
<tr><td class="header-row-header">'.ucwords($obj->lang['period']).'</td><td style="text-align:center">:</td><td>'.$period.'</td></tr>
<tr><td class="header-row-header">'.ucwords($obj->lang['totalDifference']).'</td><td style="text-align:center">:</td><td>'.$obj->formatNumber($totalDifference).'</td></tr>
</table>
<div style="clear:both"></div> ';
    
$html .= ' 
<table>
<tr>
<td style=width="600px">
<table cellpadding="4">
<tr><td style="text-align:left;width:150px;font-size:14px;"><strong>'.ucwords($obj->lang['accountsReceivable']).'</strong></td><td style="text-align:right;width:530px;"></td></tr>  
</table>
<table cellpadding="4" class="table-transaction">
   <tr class="col-header"><td style="text-align:left;width:120px; ">'.ucwords($obj->lang['arCode']).'</td><td style="width:130px;">'.ucwords($obj->lang['jobOrderNumber']).'</td><td style="width:180px;">'.ucwords($obj->lang['customer']).'</td><td style="text-align:right;width:160px;">'.ucwords($obj->lang['outstanding']).'</td><td style="text-align:right;width:90px;">'.ucwords($obj->lang['amount']).'</td></tr>  
    ';

    for($i=0;$i<count($rsARPaymentDetail);$i++){
 
        $html .= '<tr>
                <td>'.$rsARPaymentDetail[$i]['arcode'].'</td>
                <td style="">'. $rsARPaymentDetail[$i]['reftranscode2'].'</td>
                <td style="">'. $rsARPaymentDetail[$i]['customername'].'</td>
                <td style="text-align:right;">'.$obj->formatNumber($rsARPaymentDetail[$i]['outstanding']).'</td>
                <td style="text-align:right;">'.$obj->formatNumber($rsARPaymentDetail[$i]['amount']).'</td>

        </tr>';

    }    

$html .= '
</table>
<table>
<tr><td style="width:92px"></td><td style="width:50px"></td><td style="width:530px;text-align:right;"></td><td style="width:90px;text-align:right;"></td></tr>
<tr><td style="width:92px"></td><td style="width:50px"></td><td style="width:530px;text-align:right;"><strong>'.$obj->formatNumber($rs[0]['totalar']).'</strong></td></tr>
</table>
</td>
</tr>
<tr>
<td style=width="600px">
<table cellpadding="4">
<tr><td></td></tr>
<tr><td style="text-align:left;width:150px;font-size:14px;"><strong>'.ucwords($obj->lang['accountsPayable']).'</strong></td><td style="text-align:right;width:530px;"></td></tr>  
</table>
<table cellpadding="4" class="" style="border-bottom:1px solid black">
<tr class="col-header"><td style="text-align:left;width:120px; ">'.ucwords($obj->lang['apCode']).'</td><td style="width:130px;">'.ucwords($obj->lang['jobOrderNumber']).'</td><td style="width:180px;">'.ucwords($obj->lang['customer']).'</td><td style="text-align:right;width:160px;">'.ucwords($obj->lang['outstanding']).'</td><td style="text-align:right;width:90px;">'.ucwords($obj->lang['amount']).'</td></tr>  
';
    
for($j=0;$j<count($rsAPPaymentDetail);$j++){
     
    $html .= '<tr>
            <td>'.$rsAPPaymentDetail[$j]['arcode'].'</td>
            <td>'.$rsAPPaymentDetail[$j]['reftranscode2'].'</td>
            <td>'.$rsAPPaymentDetail[$j]['customername'].'</td>
            <td style="text-align:right;">'.$obj->formatNumber($rsAPPaymentDetail[$j]['outstanding']).'</td>
            <td style="text-align:right;">'.$obj->formatNumber($rsAPPaymentDetail[$j]['amount']).'</td>

    </tr>';
    
}    

$html .= '
</table>
<table>
<tr><td style="width:92px"></td><td style="width:50px"></td><td style="width:530px;text-align:right;"></td><td style="width:90px;text-align:right;"></td></tr>
<tr><td style="width:92px"></td><td style="width:50px"></td><td style="width:530px;text-align:right;"><strong>'.$obj->formatNumber($rs[0]['totalap']).'</strong></td></tr>
</table>
</td>
</tr>
</table>

';    

    
$html .= '<div style="clear:both"></div>';
$html .= $obj->generateSignLabel($rs); 
 
return $html;
}

?>
