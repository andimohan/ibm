<?php  

includeClass('SalesOrderProperty.class.php');
$salesOrderProperty = createObjAndAddToCol( new SalesOrderProperty()); 
$obj = $salesOrderProperty;

$generateReportContent = function ($dataset){ 
    
$obj = new SalesOrderProperty(); 
$termOfPayment = new TermOfPayment();
$customer = new Customer();
    
$rs = $dataset['rs'];
$rsTOP = $termOfPayment->getDataRowById($rs[0]['termofpaymentkey']); 
$rsBuyer = $customer->getDataRowById($rs[0]['buyerkey']);
$buyerrName = $rsBuyer[0]['name'];
$buyerAddress = (!empty($rsBuyer[0]['address'])) ? $rsBuyer[0]['address'] : '';
$buyerPhone = (!empty($rsBuyer[0]['address'])) ? $rsBuyer[0]['phone'] : '';
$rsSeller = $customer->getDataRowById($rs[0]['selerkey']);


$arrRecipient = array();
array_push($arrRecipient, $buyerrName, str_replace(chr(13),'<br>',$buyerAddress), $buyerPhone);
    
$html = $obj->printSetting['defaultStyle'];
$html .= ' 
<table cellpadding="2" > 
<tr><td><div class="title">INVOICE</div></td></tr>
<tr><td><div class="subtitle">'.$rs[0]['code'].'</div></td></tr>
</table> 

<div style="clear:both"></div>
<table>
<tr>
<td >
<table cellpadding="2"> 
<tr><td class="header-row-header" style="width: 100px;">Tanggal</td><td style="width:10px; text-align:center">:</td><td style="width: 260px;">'.$obj->formatDBDate($rs[0]['trdate'],'d / m / Y').'</td><td class="header-row-header"  style="width:120px">'.ucwords($obj->lang['propertyInformation']).'</td><td style="width:10px; text-align:center">:</td><td rowspan="4" style="width:180px;">'.str_replace(chr(13),'<br>',$rs[0]['propertyinformation']).'</td></tr>  
<tr><td colspan="3" class="header-row-header"></td></tr> 
<tr><td colspan="3" class="header-row-header">Kepada Yth.</td></tr> 
<tr><td colspan="3"  style="width: 670px;">'.implode('<br>',$arrRecipient).'</td></tr>  
</table> 
</td>
<td></td>
</tr>
<div style="clear:both"></div><br>
Untuk pembayaran atas transaksi sbb:<br><br>
';

$html .= '<table  cellpadding="4" class="table-transaction">';
	
$cellArray = array ();
array_push($cellArray, array('label' => $obj->lang['number'], 'align' => 'right','width' => '40'));
array_push($cellArray, array('label' => $obj->lang['services'])); 
array_push($cellArray, array('label' => 'Harga Netto', 'align' => 'right', 'width' => '100'));
$html .= $obj->generatePrintTableRow( array('class' => 'col-header','docWidth' => '670', 'cell' =>  $cellArray));  

    
$arrService = array(); 
if($rs[0]['agencyfee'] != 0)  
    array_push($arrService,array('total' => $rs[0]['agencyfee'],'label'=> ucwords($obj->lang['commission']).' '.ucwords($obj->lang['sales'])));
 
if($rs[0]['orlead'] != 0)   
    array_push($arrService, array('total' => $rs[0]['orlead'],'label'=> 'Or Lead'));

if ($rs[0]['closingfeetotal'] != 0)
   array_push($arrService, array('total' => $rs[0]['closingfeetotal'], 'label'=> 'Closing Fee'));

if($rs[0]['cashrewardtotal'] != 0)
    array_push($arrService,array('total' => $rs[0]['cashrewardtotal'], 'label'=> 'Cash Rewards'));

    
$grandTotalService= 0;
for($i=0;$i<count($arrService);$i++){
$html .= '<tr><td style="text-align:right">'.($i+1).'</td><td>'.$arrService[$i]['label'].'</td><td style="text-align:right">'.$obj->formatNumber($arrService[$i]['total']).'</td></tr>' ; 
    $grandTotalService += $arrService[$i]['total'];

}


$html .= '</table>' ;

$html .= '<div style="clear:both"></div>';
        
$sayNumber = $obj->sayNumber($grandTotalService);


$subtotalLabel =  ucwords($obj->lang['total']) ; 
    
//$topSaid = ($rsTOP[0]['duedays'] > 0 ) ? $rsTOP[0]['duedays'] . ' ' . $obj->lang['day'] : $obj->lang['cash'];
    
$html .= '<table cellpadding="4" > 
<tr>
<td rowspan="'.(count($arrSubtotal) + 1).'" style="width:460px"><strong>Terbilang</strong> :<br>'.ucwords($sayNumber).' Rupiah.</td>
<td style="text-align:right; font-weight:bold;  width:100px;">'.$subtotalLabel.'</td>
<td style="text-align:right; font-weight:bold;  width:110px;">'.$obj->formatNumber($grandTotalService).'</td>
</tr>
<tr>
<td colspan="3"><br><br></td>
</tr>
<tr>
<td>
'.$obj->loadSetting('invoiceFooter').' 
</td> 
<td colspan="2" >
Jakarta, '.$obj->formatDBDate($rs[0]['trdate'],'d F y').'<br>
'.$obj->loadSetting('companyName').'
<br><br><br><br><br><br><br><br><br><br>
<span style="text-decoration:underline"></span><br>
DIREKTUR
</td>
</tr>
';  

    
$html .= '
</table>
<div style="clear:both"></div>';

//$html .= $obj->generateSignLabel($rs); 
    
return $html;
}
?>
