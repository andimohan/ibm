<?php 

includeClass('APPayment.class.php');
$apPayment = createObjAndAddToCol( new APPayment());

$obj = $apPayment;

$summaryContent = function ($dataset){
    
$obj = new APPayment();    
$ap = $obj->getAPObj();
    
$rs = $dataset['rs'];
      
$rsDetail = $obj->getDetailWithRelatedInformation($rs[0]['pkey']); 
$arType = array_column($rsDetail,'aptypename', 'aptypekey');    
         
$periodeHTMTL = '';
if($rs[0]['usedateperiod'])
   $periodeHTMTL = '<tr><td class="header-row-header">Periode</td><td style="text-align:center">:</td><td>'. $obj->formatDBDate($rs[0]['startdateperiod']) .' - '.$obj->formatDBDate($rs[0]['enddateperiod']).'</td></tr>';
    
    
$html = $obj->printSetting['defaultStyle'];
$html .= ' 
<table cellpadding="2" > 
<tr><td><div class="title">PEMBAYARAN HUTANG</div></td></tr>
<tr><td><div class="subtitle">'.$rs[0]['code'].'</div></td></tr>
</table> 

<div style="clear:both"></div>

<table cellpadding="2"> 
<tr><td class="header-row-header">Tanggal</td><td style="width:10px; text-align:center">:</td><td style="width:540px;">'.$obj->formatDBDate($rs[0]['trdate'],'d / m / Y').'</td></tr>  
<tr><td class="header-row-header">Pemasok</td><td style="text-align:center">:</td><td>'. $rs[0]['suppliername'] .'</td></tr>    
'.$periodeHTMTL.'
</table> ';

$html .= '<div style="clear:both"></div><table cellpadding="4" style="border-top:1px solid #333; border-bottom:1px solid #333;">' ; 
    
$cellArray = array ();
array_push($cellArray, array('label' => $obj->lang['description'] ));  
array_push($cellArray, array('label' => $obj->lang['payingSettlement'], 'align' => 'right', 'width' => '140'));
array_push($cellArray, array('label' => $obj->lang['tax23'], 'align' => 'right', 'width' => '100'));
$html .= $obj->generatePrintTableRow( array('class' => 'col-header','docWidth' => '670', 'cell' =>  $cellArray));  

   
foreach($arType as $key=>$type){
      
    $subtotal = 0;
    $tax = 0;
    for ($i=0;$i<count($rsDetail);$i++){    
      if($rsDetail[$i]['aptypekey'] != $key) continue;
           
      $subtotal += $rsDetail[$i]['amount'];
      $tax += $rsDetail[$i]['taxamount'];
        
    } 
    $html .= '<tr><td>'.$type.'</td><td style="text-align:right">'.$obj->formatNumber($subtotal).'</td><td style="text-align:right">'.$obj->formatNumber($tax).'</td></tr>'; 
     
}   

$html .= '</table>';
    
$arrSubtotal = array(); 
 
if ($rs[0]['totaldiscount'] > 0){
    array_push($arrSubtotal, '<tr><td style="text-align:right; font-weight:bold;">'.ucwords($obj->lang['totalDiscount']).'</td><td style="text-align:right; font-weight:bold;"  >'.$obj->formatNumber($rs[0]['totaldiscount']).'</td></tr>');
}
    

if ($rs[0]['payabletax23'] != 0){
    array_push($arrSubtotal, '<tr><td style="text-align:right; font-weight:bold;">'.ucwords($obj->lang['tax23']).'</td><td style="text-align:right; font-weight:bold;"  >'.$obj->formatNumber($rs[0]['payabletax23'] * -1).'</td></tr>');
}
    
if ($rs[0]['totaldownpayment'] > 0){

    array_push($arrSubtotal, '<tr><td style="text-align:right; font-weight:bold;">'.ucwords($obj->lang['downpayment']).'</td><td style="text-align:right; font-weight:bold;">'.$obj->formatNumber($rs[0]['totaldownpayment'] * -1).'</td></tr>'); 
}
    
$rsARCost = $obj->getCostDetail($rs[0]['pkey']);  
for ($j=0;$j<count($rsARCost);$j++){  
 array_push($arrSubtotal, '<tr><td style="text-align:right; font-weight:bold;">'.ucwords($rsARCost[$j]['costname']).'</td><td style="text-align:right; font-weight:bold;">'.$obj->formatNumber($rsARCost[$j]['amount']).'</td></tr>'); 
}
    
if (!empty($arrSubtotal)){ 
   array_push($arrSubtotal, '<tr><td style="text-align:right; font-weight:bold;">Total</td><td style="text-align:right; font-weight:bold;">'.$obj->formatNumber($rs[0]['grandtotal']).'</td></tr>'); 
} 
    
if ($rs[0]['totalpayment'] != 0) { 
     //array_push($arrSubtotal, '<tr><td></td><td></td></tr>'); 
     array_push($arrSubtotal, '<tr><td style="text-align:right; font-weight:bold;">'.ucwords($obj->lang['totalPayment']).'</td><td style="text-align:right; font-weight:bold;">'.$obj->formatNumber($rs[0]['totalpayment']).'</td></tr>'); 
     array_push($arrSubtotal, '<tr><td style="text-align:right; font-weight:bold;">'.ucwords($obj->lang['balance']).'</td><td style="text-align:right; font-weight:bold;">'.$obj->formatNumber(abs($rs[0]['grandtotal']-$rs[0]['totalpayment'])).'</td></tr>');   
}
    

     
$subtotalLabel = (!empty($arrSubtotal)) ? ucwords($obj->lang['payingOffAmount']) : ucwords($obj->lang['total']) ; 
    
$sayNumber = $obj->sayNumber($rs[0]['grandtotal']);
    
$payment = '';
$rsARPaymentMethodDetail = $obj->getPaymentMethodDetail($rs[0]['pkey']);    
$payment .= '<br><strong>'.$obj->lang['paymentMethod'].'</strong><br><table cellpadding="4">';
  
for ($j=0;$j<count($rsARPaymentMethodDetail);$j++){  
if ($rsARPaymentMethodDetail[$j]['amount'] == 0) continue;
$payment .= '<tr>';
$payment .= '<td style="width: 150px;">'.$rsARPaymentMethodDetail[$j]['paymentmethodname'].'</td>';
$payment .= '<td style="text-align:center; width: 50px;">:</td>';
$payment .= '<td style="text-align:right; width: 80px;">'.$obj->formatNumber($rsARPaymentMethodDetail[$j]['amount']).'</td>';
$payment .= '</tr>'; 
}

$payment  .= '</table>'; 	
    
    
$html .= '    
</table>  
<div style="clear:both"></div> 
<table cellpadding="4"> 
<tr><td rowspan="'.(count($arrSubtotal)+1).'" style="width:440px;"><strong>Terbilang</strong> :<br>'.ucwords($sayNumber).' Rupiah. <br>'.$payment.'</td><td style="text-align:right; font-weight:bold;  width:120px; ">'.$subtotalLabel.'</td><td style="text-align:right; font-weight:bold;  width:110px;"  >'.$obj->formatNumber($rs[0]['totalpaid']).'</td></tr>
';


$html .= implode('',$arrSubtotal); 
    
$html .= '</table>';  

$html .= '
<table cellpadding="4">
<tr><td><strong>Catatan</strong> :<br>'.str_replace(chr(13),'<br>',$rs[0]['trnotes']).'</td></tr>
</table>
<div "clear:both"></div>';
 
    
return $html;
};

$detailContent = function ($dataset){ 

$obj = new APPayment();    
$ap = $obj->getAPObj();
    
$rs = $dataset['rs'];
      
$rsDetail = $obj->getDetailWithRelatedInformation($rs[0]['pkey']); 
$arType = array_column($rsDetail,'aptypename', 'aptypekey');  
    
$html = $obj->printSetting['defaultStyle'];
$html .= '<table cellpadding="2" > 
<tr><td><div class="title">'.strtoupper($obj->lang['attachment']).'</div></td></tr>
<tr><td><div class="subtitle">'.$rs[0]['code'].'</div></td></tr>
</table> ';
    
foreach($arType as $key=>$type){
    
    $html .= '<div style="clear:both"></div>
              <div><strong>'.strtoupper($type).'</strong></div><br>';
    
    $html .= '<table  cellpadding="4" class="table-transaction">';

    
     $cellArray = array (); 
     switch($key){
                case AP_TYPE['carServiceMaintenance']:  array_push($cellArray, array('label' => $obj->lang['code'], 'width' => '100'));
                                                        array_push($cellArray, array('label' => $obj->lang['description'] )); 
                                                        array_push($cellArray, array('label' => $obj->lang['refDate'], 'align' => 'center', 'width' => '100'));
                                                        array_push($cellArray, array('label' => $obj->lang['payingSettlement'], 'align' => 'right', 'width' => '100'));
                                                        array_push($cellArray, array('label' => $obj->lang['tax23'], 'align' => 'right', 'width' => '100'));
                                                        break;
                case AP_TYPE['otherCost']:  array_push($cellArray, array('label' => $obj->lang['code'], 'width' => '100'));
                                            array_push($cellArray, array('label' => $obj->lang['description'] )); 
                                            array_push($cellArray, array('label' => $obj->lang['payingSettlement'], 'align' => 'right', 'width' => '100'));
                                            array_push($cellArray, array('label' => $obj->lang['tax23'], 'align' => 'right', 'width' => '100'));
                                            break;
                default :   array_push($cellArray, array('label' => $obj->lang['code'], 'width' => '100'));
                            array_push($cellArray, array('label' => $obj->lang['refCode'] ));
                            array_push($cellArray, array('label' => $obj->lang['refDate'], 'align' => 'center', 'width' => '100'));
                            array_push($cellArray, array('label' => $obj->lang['payingSettlement'], 'align' => 'right', 'width' => '100'));
                            array_push($cellArray, array('label' => $obj->lang['tax23'], 'align' => 'right', 'width' => '100'));
                
     }
    
    $html .= $obj->generatePrintTableRow( array('class' => 'col-header','docWidth' => '670', 'cell' =>  $cellArray));  

    $subtotal = 0;
    $tax = 0;
    for ($i=0;$i<count($rsDetail);$i++){   
      
      if($rsDetail[$i]['aptypekey'] != $key) continue;
          
      $refCode = array();
      if(!empty($rsDetail[$i]['refcode'])) array_push($refCode, $rsDetail[$i]['refcode']) ;
      if(!empty($rsDetail[$i]['refcode2'])) array_push($refCode, $rsDetail[$i]['refcode2']) ;
           
       $arrCol = array();    
       switch($key){
                case AP_TYPE['carServiceMaintenance']:  $maintenanceDesc = array();
                                                        //$maintenanceDesc = array_merge($maintenanceDesc, $refCode);
                                                        array_push($maintenanceDesc, str_replace(chr(13),'<br>',$rsDetail[$i]['apdesc']));
               
                                                        array_push($arrCol,'<td>'.$rsDetail[$i]['apcode'].'</td>');
                                                        array_push($arrCol,'<td>'.implode('<br>',$maintenanceDesc).'</td>');
                                                        array_push($arrCol,'<td style="text-align:center">'. $obj->formatDBDate($rsDetail[$i]['refdate'],'',array('returnOnEmpty' => true, 'value' => '')) .'</td>');
                                                        array_push($arrCol,'<td style="text-align:right">'.$obj->formatNumber($rsDetail[$i]['amount']).'</td>');
                                                        array_push($arrCol,'<td style="text-align:right">0</td>');
                                                        break;

                case AP_TYPE['otherCost']:  array_push($arrCol,'<td>'.$rsDetail[$i]['apcode'].'</td>');
                                            array_push($arrCol,'<td>'.str_replace(chr(13),'<br>',$rsDetail[$i]['apdesc']).'</td>');
                                            array_push($arrCol,'<td style="text-align:right">'.$obj->formatNumber($rsDetail[$i]['amount']).'</td>');
                                            array_push($arrCol,'<td style="text-align:right">0</td>');
                                            break;
               
                default :   array_push($arrCol,'<td>'.$rsDetail[$i]['apcode'].'</td>');
                            array_push($arrCol,'<td>'.implode(', ',$refCode).'</td>');
                            array_push($arrCol,'<td style="text-align:center">'. $obj->formatDBDate($rsDetail[$i]['refdate'],'',array('returnOnEmpty' => true, 'value' => '')) .'</td>');
                            array_push($arrCol,'<td style="text-align:right">'.$obj->formatNumber($rsDetail[$i]['amount']).'</td>'); 
                            array_push($arrCol,'<td style="text-align:right">'.$obj->formatNumber($rsDetail[$i]['taxamount']).'</td>'); 
                      
               
       }
        
       $html .= '<tr>'.implode('',$arrCol).'</tr>';
        
      $subtotal += $rsDetail[$i]['amount'];
      $tax += $rsDetail[$i]['taxamount'];
        
    }
    $html .= '</table>' ; 
      
}   
      
$html .= '    
</table>
'; 
    
return $html;
};

$generateReportContent = array();
array_push($generateReportContent , $summaryContent); 
array_push($generateReportContent , $detailContent); 


?>