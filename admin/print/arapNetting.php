<?php  

includeClass(array('ARAPNetting.class.php'));
$arapNetting = createObjAndAddToCol( new ARAPNetting()); 

$obj = $arapNetting;

$generateReportContent = function ($dataset){  

$obj = new ARAPNetting(); 
$ar = $obj->getARObj();
$ap = $obj->getAPObj();
$customer = new Customer();
$supplier = new Supplier();
    
$rs = $dataset['rs']; 

$rsARPaymentDetail = $obj->getDetailARAP($obj->arapConstant['ar'],$rs[0]['pkey']);
$rsAPPaymentDetail = $obj->getDetailARAP($obj->arapConstant['ap'],$rs[0]['pkey']);
    
$rsCustomer = $customer->getDataRowById($rs[0]['customerkey']);
$rsSupplier = $supplier->getDataRowById($rs[0]['supplierkey']);
    
$period = ($rs[0]['usedateperiod']==1) ? $obj->formatDBDate($rs[0]['startdateperiod'],'d / m / Y').' - '.$obj->formatDBDate($rs[0]['enddateperiod'],'d / m / Y') : '-';
 
$html = $obj->printSetting['defaultStyle'];
$html .= ' 
<table cellpadding="2" > 
<tr><td><div class="title">ARAP Netting</div></td></tr>
<tr><td><div class="subtitle">'.$rs[0]['code'].'</div></td></tr>
</table> 

<div style="clear:both"></div>
 
<table cellpadding="2"> 
<tr><td class="header-row-header" style="width:80px">'.ucwords($obj->lang['date']).'</td><td style="width:10px; text-align:center">:</td><td style="width:580px;">'.$obj->formatDBDate($rs[0]['trdate'],'d / m / Y').'</td></tr>
<tr><td class="header-row-header" style="width:80px">'.ucwords($obj->lang['customer']).'</td><td style="width:10px; text-align:center">:</td><td style="width:580px;">'.$rsCustomer[0]['name'].'</td></tr>
<tr><td class="header-row-header" style="width:80px">'.ucwords($obj->lang['supplier']).'</td><td style="width:10px; text-align:center">:</td><td style="width:580px;">'.$rsSupplier[0]['name'].'</td></tr>
<tr><td class="header-row-header" style="width:80px">'.ucwords($obj->lang['period']).'</td><td style="width:10px; text-align:center">:</td><td style="width:580px;">'.$period.'</td></tr>
</table><div style="clear:both"></div> ';
   
$html .= ' 
<table>
<tr>
<td style=width="600px">
<table cellpadding="4">
<tr><td style="text-align:left;width:150px;font-size:14px;"><strong>'.ucwords($obj->lang['accountsReceivable']).'</strong></td><td style="text-align:right;width:530px;"></td></tr>  
</table>
<table cellpadding="4" class="table-transaction">
   <tr class="col-header"><td style="text-align:left;width:90px; ">'.ucwords($obj->lang['arCode']).'</td><td style="width:120px;">'.ucwords($obj->lang['reference']).'</td><td style="width:200px;">'.ucwords($obj->lang['reference']).' 2</td><td style="text-align:right;width:120px;">'.ucwords($obj->lang['outstanding']).'</td><td style="text-align:right;width:100px;">'.ucwords($obj->lang['amount']).'</td><td style="text-align:right;width:60px;">'.ucwords($obj->lang['tax23']).'</td></tr>  
    ';

    for($i=0;$i<count($rsARPaymentDetail);$i++){
        $rsAR = $ar->getDataRowById($rsARPaymentDetail[$i]['arkey']);
    
        $html .= '<tr>
                <td>'.$rsARPaymentDetail[$i]['arcode'].'</td>
                <td style="">'. $rsARPaymentDetail[$i]['refcode'].'</td>
                <td style="">'. $rsAR[0]['refcode2'].'</td>
                <td style="text-align:right;">'.$obj->formatNumber($rsARPaymentDetail[$i]['outstanding']).'</td>
                <td style="text-align:right;">'.$obj->formatNumber($rsARPaymentDetail[$i]['amount']).'</td>
                <td style="text-align:right;">'.$obj->formatNumber($rsARPaymentDetail[$i]['taxamount']).'</td>

        </tr>';

    }    

$html .= '
</table>
<table>
<tr><td style="width:92px"></td><td style="width:50px"></td><td style="width:480px;text-align:right;"></td><td style="width:60px;text-align:right;"></td></tr>
<tr><td style="width:92px"></td><td style="width:50px"></td><td style="width:480px;text-align:right;"><strong>'.$obj->formatNumber($rs[0]['totalar']).'</strong></td><td style="width:60px;text-align:right;"><strong>'.$obj->formatNumber($rs[0]['totaltaxar']).'</strong></td></tr>
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
<tr class="col-header"><td style="text-align:left;width:90px; ">'.ucwords($obj->lang['apCode']).'</td><td style="width:120px;">'.ucwords($obj->lang['reference']).'</td><td style="width:200px;">'.ucwords($obj->lang['reference']).' 2</td><td style="text-align:right;width:120px;">'.ucwords($obj->lang['outstanding']).'</td><td style="text-align:right;width:100px;">'.ucwords($obj->lang['amount']).'</td><td style="text-align:right;width:60px;">'.ucwords($obj->lang['tax23']).'</td></tr>  
';
    
for($j=0;$j<count($rsAPPaymentDetail);$j++){
     
    $rsAP = $ap->getDataRowById($rsAPPaymentDetail[$j]['apkey']);
    
    
    $html .= '<tr>
            <td>'.$rsAPPaymentDetail[$j]['arcode'].'</td>
            <td>'.$rsAPPaymentDetail[$j]['refcode'].'</td>
            <td>'.$rsAP[0]['refcode2'].'</td>
            <td style="text-align:right;">'.$obj->formatNumber($rsAPPaymentDetail[$j]['outstanding']).'</td>
            <td style="text-align:right;">'.$obj->formatNumber($rsAPPaymentDetail[$j]['amount']).'</td>
            <td style="text-align:right;">'.$obj->formatNumber($rsAPPaymentDetail[$j]['taxamount']).'</td>

    </tr>';
    
}    

$html .= '
</table>
<table>
<tr><td style="width:92px"></td><td style="width:50px"></td><td style="width:480px;text-align:right;"></td><td style="width:70px;text-align:right;"></td></tr>
<tr><td style="width:92px"></td><td style="width:50px"></td><td style="width:480px;text-align:right;"><strong>'.$obj->formatNumber($rs[0]['totalap']).'</strong></td><td style="width:60px;text-align:right;"><strong>'.$obj->formatNumber($rs[0]['totaltaxap']).'</strong></td></tr>
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