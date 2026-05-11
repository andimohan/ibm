<?php

$obj = $salesOrderCarService;


$generateReportContent = function ($dataset){  

$obj = new SalesOrderCarService();  
$customer = new Customer();
$termOfPayment = new TermOfPayment();
$customCode = new CustomCode();
    
$rs = $dataset['rs']; 
        
$rsDetail = $obj->getDetailWithRelatedInformation($rs[0]['pkey']);
$rsDetailPayment = $obj->getDataRowById($rs[0]['pkey']);
$rsCustomer = $customer->getDataRowById($rs[0]['customerkey']);
$rsTOP = $termOfPayment->getDataRowById($rs[0]['termofpaymentkey']);

$borderLeft = 'border-left:1px solid black';
$borderRight = 'border-right:1px solid black';
 
$rsInvoiceType = $customCode->searchData($customCode->tableName.'.pkey',$rs[0]['customcodekey'], true);
 
$proforma = ($rs[0]['statuskey'] == 1) ? '<div style="font-weight:normal; font-size:0.9em">(PROFORMA)</div>' : '';$html = $obj->printSetting['defaultStyle'];
$html .= ' 
<table cellpadding="2" > 
<tr><td><div class="title">INVOICE</div></td></tr>
<tr><td><div class="subtitle"></div></td></tr>
</table> 

<div style="clear:both"></div>
 
<table cellpadding="2"> 
<tr><td style="width:50px" rowspan="2">Kepada :</td><td style="width:400px" rowspan="2">'.$rsCustomer[0]['name'].'<br>'.$rsCustomer[0]['address'].'</td><td style="width:100px">No Invoice</td><td style="width:10px">:</td><td>'.$rs[0]['code'].'</td></tr>   
<tr><td style="width:100px">Tanggal</td><td style="width:10px">:</td><td>'.$obj->formatDBDate($rs[0]['trdate']).'</td></tr>   
</table> 
 

<div style="clear:both"></div>  
<div style="clear:both"></div>  
<table cellpadding="4" class="table-transaction">
<tr class="col-header"><td style="text-align:left;width:30px;'.$borderLeft.';'.$borderRight.'">No</td><td style="text-align:center;width:250px;'.$borderRight.'">DESCRPITIONS</td><td style="text-align:center;width:150px;'.$borderRight.'" colspan="2">QUANTITY</td><td style="text-align:center;width:125px;'.$borderRight.'"  colspan="2">PRICE</td><td style="text-align:center;width:125px;'.$borderRight.'"  colspan="2">TOTAL</td></tr>  
';
        
    
$color = '#666';
    
for($i=0;$i<count($rsDetail);$i++){ 
    
//if ($rsDetail[$i]['discounttype'] == 2)
//    $rsDetail[$i]['discount'] = $rsDetail[$i]['discount']/100 * $rsDetail[$i]['priceinunit'];
        
      $detailDesc = (!empty($rsDetail[$i]['description'])) ? '<br>'.$rsDetail[$i]['description'] : '';
        
      $itemname = (!empty($rsDetail[$i]['alias'])) ? $rsDetail[$i]['alias'] : $rsDetail[$i]['itemname'];
      $html .= '<tr><td style="text-align:right;'.$borderLeft.';'.$borderRight.'">'.($i+1).'.</td><td style="'.$borderRight.'">'.$itemname.''.$detailDesc.'</td><td style="width:40px;text-align:center;'.$borderRight.'">'.$obj->formatNumber($rsDetail[$i]['qtyinbaseunit']).'</td><td style="width:110px;text-align:center;'.$borderRight.'">'.$rsDetail[$i]['unitname'].'</td><td style="width:40px;text-align:center;">Rp</td><td style="width:85px;text-align:right;'.$borderRight.'">'.$obj->formatNumber($rsDetail[$i]['priceinunit']).'</td><td style="width:40px;text-align:center;">Rp</td><td style="width:85px;text-align:right;'.$borderRight.'">'.$obj->formatNumber($rsDetail[$i]['total']).'</td></tr>';
                    
}
    
$arrSubtotal = array(); 
    
$borderTop= 'border-top:1px solid black';

if ($rs[0]['finaldiscount'] != 0){
    if ($rs[0]['finaldiscounttype'] == 2)
        $rs[0]['finaldiscount'] = $rs[0]['finaldiscount']/100 * $rs[0]['subtotal'];
    
    //$finaldiscount = ($rs[0]['finaldiscount'] != 0) ?  $obj->formatNumber($rs[0]['finaldiscount'] * -1) : 0;  
    $rs[0]['finaldiscount'] *= -1;
    array_push($arrSubtotal, '<tr><td style="text-align:left; font-weight:bold;">'.ucwords($obj->lang['discount']).'</td><td style="width:40px;text-align:center;">Rp</td><td style="text-align:right; font-weight:bold;'.$borderRight.'"  >'.$obj->formatNumber($rs[0]['finaldiscount']).'</td></tr>');
}
    

if ($rs[0]['taxvalue'] != 0){
//    array_push($arrSubtotal, '<tr><td style="text-align:left; font-weight:bold;">'.ucwords($obj->lang['beforeTax']).'</td><td style="text-align:right; font-weight:bold;"  >'.$obj->formatNumber($rs[0]['beforetaxtotal']).'</td></tr>');
    array_push($arrSubtotal, '<tr><td style="text-align:left; font-weight:bold;">'.ucwords($obj->lang['PPN']).' 10%</td><td style="width:40px;text-align:center;">Rp</td><td style="text-align:right; font-weight:bold;'.$borderRight.'"  >'.$obj->formatNumber($rs[0]['taxvalue']).'</td></tr>');
}
    
if ($rs[0]['totaldownpayment'] > 0){
    array_push($arrSubtotal, '<tr><td style="text-align:left; font-weight:bold;">'.ucwords($obj->lang['downpayment']).'</td><td style="width:40px;text-align:center;">Rp</td><td style="text-align:right; font-weight:bold;'.$borderRight.'">'.$obj->formatNumber($rs[0]['totaldownpayment']).'</td></tr>'); 
} 
    
//if (!empty($arrSubtotal)) { 
//    $html .= '<tr><td></td> <td style="text-align:right; font-weight:bold;  ">Total</td><td style="text-align:right; font-weight:bold;"  >'.$obj->formatNumber($rs[0]['outstanding']).'</td></tr>';
////    array_push($arrSubtotal, '<tr><td style="text-align:left; font-weight:bold;'.$borderRight.'">Total</td><td style="width:40px;text-align:center;">Rp</td><td style="text-align:right; font-weight:bold;'.$borderRight.'">'.$obj->formatNumber($rs[0]['outstanding']).'</td></tr>'); 
//} 
    
    
if ($rs[0]['tax23value'] != 0)  { 

//    array_push($arrSubtotal, '<tr><td></td><td></td></tr>'); 
    array_push($arrSubtotal, '<tr><td style="text-align:left; font-weight:bold;">'.ucwords('PPh').' '.$obj->formatNumber($rs[0]['tax23percentage']).'%</td><td style="width:40px;text-align:center;">Rp</td><td style="text-align:right; font-weight:bold;'.$borderRight.'">'.$obj->formatNumber($rs[0]['tax23value']).'</td></tr>'); 
//    array_push($arrSubtotal, '<tr><td style="text-align:right; font-weight:bold;">'.ucwords($obj->lang['balance']).'</td><td style="text-align:right; font-weight:bold;">'.$obj->formatNumber(abs($rs[0]['grandtotal']-$rs[0]['tax23value'])).'</td></tr>'); 
    
}
    
array_push($arrSubtotal, '<tr><td style="text-align:left; font-weight:bold;'.$borderTop.'">Grand Total</td><td style="width:40px;text-align:center;'.$borderTop.'">Rp</td><td style="text-align:right; font-weight:bold;'.$borderRight.';'.$borderTop.'">'.$obj->formatNumber($rs[0]['grandtotal']).'</td></tr>'); 


    
$subtotalLabel = (!empty($arrSubtotal)) ? ucwords($obj->lang['total']) : ucwords($obj->lang['total']) ;
$sayNumber = $obj->sayNumber($rs[0]['grandtotal']);
    
$topSaid = ($rsTOP[0]['duedays'] > 0 ) ? $rsTOP[0]['duedays'] . ' ' . $obj->lang['day'] : $obj->lang['cash'];  

    
$html .= '    
</table>  
<table cellpadding="4" class="table-transaction"> 
<tr><td rowspan="'.(count($arrSubtotal) + 1).'" style="width:430px;'.$borderLeft.';'.$borderRight.'"><strong>Terbilang</strong> : '.ucwords($sayNumber).' Rupiah.</td> <td style="text-align:left; font-weight:bold; width:125px;">'.$subtotalLabel.'</td><td style="width:40px;text-align:center;">Rp</td><td style="text-align:right; font-weight:bold;  width:85px; '.$borderRight.'"  >'.$obj->formatNumber($rs[0]['subtotal']).'</td></tr>
';

$html .= implode('',$arrSubtotal); 
$html  .= '</table><div style="clear:both"></div>';
        
$bank = '<table cellpadding="1" >';
$bank .= '<tr><td style="width:90px">Branch</td><td style="width:10px">:</td><td style="width:640px">Kelapa Gading - Hibrida</td></tr>';
$bank .= '<tr><td style="width:90px">Name of the A/C</td><td style="width:10px">:</td><td style="width:640px">Total Crane Indonesia</td></tr>';
$bank .= '<tr><td style="width:90px">A/C No</td><td style="width:10px">:</td><td style="width:640px">Mandiri <b>1250099699969</b></td></tr>';
$bank .= '<tr><td style="width:90px"></td><td style="width:10px"></td><td style="width:640px"></td></tr>';
$bank .= '<tr><td style="width:90px">Branch</td><td style="width:10px">:</td><td style="width:640px">Kelapa Gading - Rivirea</td></tr>';
$bank .= '<tr><td style="width:90px">Name of the A/C</td><td style="width:10px">:</td><td style="width:640px">Total Crane Indonesia PT</td></tr>';
$bank .= '<tr><td style="width:90px">A/C No</td><td style="width:10px">:</td><td style="width:640px">BCA <b>8710231207</b></td></tr>';
$bank .= '</table>';
        
$sign  = '<table cellpadding="1"  class="sign">';
$sign .= '<tr><td ></td><td class="sign-col-space"></td></tr>';
$sign .= '<tr><td ></td><td class="sign-col-space"></td></tr>';
$sign .= '<tr><td class="sign-col" style="height:90px">Jakarta, '.$obj->formatDBDate($rs[0]['trdate'],'d F Y').'</td><td class="sign-col-space"></td></tr>';
$sign .= '<tr><td style=""><u>Andri Tohir</u></td><td class="sign-col-space"></td></tr>';
$sign .= '<tr><td >Direktur</td><td class="sign-col-space"></td></tr>';
$sign .= '</table>';

$html .= '<div style="clear:both"></div>';
$html .= '<table>
<tr><td style="width:430px">BANK DETAILS <br><br> '.$bank.'</td><td style="width:450px">'.$sign.'</td></tr>
</table>';

return $html;
}

?>
