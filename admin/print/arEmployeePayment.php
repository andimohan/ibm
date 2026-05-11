<?php 

includeClass(array('ARPayment.class.php','AREmployeePayment.class.php'));
$arEmployeePayment = createObjAndAddToCol( new AREmployeePayment());  


$obj = $arEmployeePayment;
$generateReportContent = function ($dataset){ 

$obj = new AREmployeePayment();  
$ar = $obj->getARObj();
    
$rs = $dataset['rs'];
$rsDetail = $obj->getDetailById($rs[0]['pkey']);    
     
    
$html = $obj->printSetting['defaultStyle'];
$html .= ' 
<table cellpadding="2" > 
<tr><td><div class="title">'.strtoupper($obj->lang['employeeARPayment']).'</div></td></tr>
<tr><td><div class="subtitle">'.$rs[0]['code'].'</div></td></tr>
</table> 

<div style="clear:both"></div>
 
<table cellpadding="2"> 
<tr><td class="header-row-header">'.ucwords($obj->lang['date']).'</td><td style="width:10px; text-align:center">:</td><td style="width:540px;">'.$obj->formatDBDate($rs[0]['trdate'],'d / m / Y').'</td></tr>  
<tr><td class="header-row-header">'.ucwords($obj->lang['employee']).'</td><td style="text-align:center">:</td><td>'. $rs[0]['employeename'] .'</td></tr>    
</table> 
 
<div style="clear:both"></div> ';

$html .= ' 
<table  cellpadding="4" class="table-transaction">';
    
$cellArray = array ();
array_push($cellArray, array('label' => $obj->lang['ar'], 'width' => '100'));
array_push($cellArray, array('label' => $obj->lang['date'], 'align' => 'center', 'width' => '100'));
array_push($cellArray, array('label' => $obj->lang['refCode']  ));
//array_push($cellArray, array('label' => $obj->lang['jobOrderCode'], 'width' => '120' ));
//array_push($cellArray, array('label' => $obj->lang['customer']));
array_push($cellArray, array('label' => $obj->lang['payingSettlement'], 'align' => 'right', 'width' => '90'));
$html .= $obj->generatePrintTableRow( array('class' => 'col-header','docWidth' => '680', 'cell' =>  $cellArray));   
 
for ($i=0;$i<count($rsDetail);$i++){   
    $rsAr = $ar->searchData($ar->tableName.'.pkey',$rsDetail[$i]['arkey']);
    $refCode = array();
    if(!empty($rsAr[0]['refcode'])) array_push($refCode, $rsAr[0]['refcode']) ;
    if(!empty($rsAr[0]['refcode2'])) array_push($refCode, $rsAr[0]['refcode2']) ;
 //<td>'.$rsAr[0]['reftranscode2'].'</td><td>'.$rsAr[0]['customername'].'</td> 
  $html .= '<tr><td>'.$rsAr[0]['code'].'</td><td style="text-align:center">'. $obj->formatDBDate($rsAr[0]['trdate'],'d / m / Y',array('returnOnEmpty' => true, 'value' => '')) .'</td><td>'.implode(', ', $refCode).'</td><td style="text-align:right">'.$obj->formatNumber($rsDetail[$i]['amount']).'</td></tr>' ; 
}
$html .= '</table>' ;
   
    
//$sayNumber = $obj->sayNumber($rs[0]['grandtotal']);

    
//here
    
$arrSubtotal = array(); 

    
if (!empty($arrSubtotal)) { 
   array_push($arrSubtotal, '<tr><td style="text-align:right; font-weight:bold;">Total</td><td style="text-align:right; font-weight:bold;">'.$obj->formatNumber($rs[0]['grandtotal']).'</td></tr>'); 
}
    
if ($rs[0]['totalpayment'] != 0)  { 
     //array_push($arrSubtotal, '<tr><td></td><td></td></tr>'); 
     array_push($arrSubtotal, '<tr><td style="text-align:right; font-weight:bold;">'.ucwords($obj->lang['totalPayment']).'</td><td style="text-align:right; font-weight:bold;">'.$obj->formatNumber($rs[0]['totalpayment']).'</td></tr>'); 
     array_push($arrSubtotal, '<tr><td style="text-align:right; font-weight:bold;">'.ucwords($obj->lang['balance']).'</td><td style="text-align:right; font-weight:bold;">'.$obj->formatNumber(abs($rs[0]['grandtotal']-$rs[0]['totalpayment'])).'</td></tr>'); 
}
    

     
$subtotalLabel = (!empty($arrSubtotal)) ? ucwords($obj->lang['subtotal']) : ucwords($obj->lang['total']) ; 
    
$sayNumber = $obj->sayNumber($rs[0]['grandtotal']);
    
$payment = '';
$rsARPaymentMethodDetail = $obj->getPaymentMethodDetail($rs[0]['pkey']);    
$payment .= '<br><strong>'.$obj->lang['paymentMethod'].'</strong><br><table cellpadding="2">';
  
for ($j=0;$j<count($rsARPaymentMethodDetail);$j++){  
$payment .= '<tr>';
$payment .= '<td style="width: 120px;">'.$rsARPaymentMethodDetail[$j]['paymentmethodname'].'</td>';
$payment .= '<td style="text-align:center; width: 15px;">:</td>';
$payment .= '<td style="text-align:right; width: 80px;">'.$obj->formatNumber($rsARPaymentMethodDetail[$j]['amount']).'</td>';
$payment .= '</tr>'; 
}
$payment  .= '</table>'; 	
    
    
$html .= '    
</table>  
<div style="clear:both"></div> 
<table cellpadding="4"> 
<tr><td rowspan="'.(count($arrSubtotal)+1).'" style="width:440px;"><strong>Terbilang</strong><br>'.ucwords($sayNumber).' Rupiah. <br>'.$payment.'</td><td style="text-align:right; font-weight:bold;  width:120px; ">'.$subtotalLabel.'</td><td style="text-align:right; font-weight:bold;  width:110px;"  >'.$obj->formatNumber($rs[0]['totalreceived']).'</td></tr>
';


$html .= implode('',$arrSubtotal); 
    
$html .= '</table>';
 

   

$html .= '
<table cellpadding="4">
<tr><td><strong>Catatan</strong><br>'.str_replace(chr(13),'<br>',$rs[0]['trnotes']).'</td></tr>
</table>
<div "clear:both"></div>';

$html .= $obj->generateSignLabel($rs); 
    
return $html;
}
?>
