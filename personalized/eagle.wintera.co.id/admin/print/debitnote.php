<?php 

$pdf->setCustomSettings(
    array( 
         'showPrintHeader' => false,
         'footer' => '',  
         'fontName' => 'Courier'
         ) 
);


$generateReportContent = function ($dataset){ 
global $pdf;
 
$obj = new EMKLOrderInvoice(); 
$item = new Item(); 
$jobOrder = new EMKLJobOrder();
$container = new Container();
$employee = new Employee();
$customer = new Customer(); 
$currency = new Currency();
$paymentMethod = new PaymentMethod();
$emklInvoiceOrderDetail = array(); 
$arrCurrency = $currency->searchData();
$arrCurrency = array_column($arrCurrency,'name','pkey'); 
$rsContainer = $container->searchData();
$rsContainer = array_column($rsContainer,'name','pkey');

 
$termOfPayment = new TermOfPayment();
      
$rs = $dataset['rs'];
$draft = ($rs[0]['statuskey'] == 1) ? ' / DRAFT' : '';
$rsDetail = $obj->getDetailWithRelatedInformation($rs[0]['pkey']);
 
$rsCurrency = $currency->searchData();
$rsCurrency = array_column($rsCurrency,'name','pkey');
$rsPaymentMethod = $paymentMethod->getDataRowById($rs[0]['companybankkey']);
    
$currencyDecimal = 2; //($rs[0]['currencykey'] == CURRENCY['idr']) ? 0 : 2;
    
$rsJobOrder = $jobOrder->searchData($jobOrder->tableName.'.pkey',$rsDetail[0]['refsalesorderheaderkey']);
$rsJobOrderDetail = $jobOrder->getDetailByColumn($jobOrder->tableNameDetail.'.pkey',$rsDetail[0]['salesorderkey']);  

if(!empty($rsJobOrderDetail[0]['podkey'])){
	$port = new Port();
	$rsPort = $port->searchDataRow(array($port->tableName.'.name'), ' and '.$port->tableName.'.pkey =  '. $obj->oDbCon->paramString($rsJobOrderDetail[0]['podkey']));
	$PODName =  $rsPort[0]['name'];
}else{
	$PODName =  $rsJobOrder[0]['podname'];
} 
	
	
$sayNumber = $obj->sayNumberInEnglish($rs[0]['grandtotal']);
 
$rsCustomer = $customer->getDataRowById($rs[0]['customerkey']);
    
$invoiceAddress = (!empty($rs[0]['invoiceaddress']))  ? $rs[0]['invoiceaddress']: $rsCustomer[0]['address'];
    
$name='';
if(!empty($rsCustomer[0]['alias']))
    $name = $rsCustomer[0]['alias'];
else
    $name = $rsCustomer[0]['name'];
    
$rsTOP =   $termOfPayment->getDataRowById($rs[0]['termofpaymentkey']); 
    
$trnotes =  array();
/*     
    
if (!empty($rsJobOrder[0]['containernumber'])) 
    array_push($trnotes,str_replace(chr(13),'<br>',$rsJobOrder[0]['containernumber']));*/
    
if (!empty($rs[0]['trdesc'])) 
    array_push($trnotes, str_replace(chr(13),'<br>',$rs[0]['trdesc'])); 
 
    
$html = $obj->printSetting['defaultStyle'];
    
$html .= '<style>
.table-transaction {border-bottom:1px solid #fff;}
.col-header td{border:1px solid #333; font-weight:bold}
.table-transaction .detail-row td{border-right:1px solid #333; border-left:1px solid #333;}
</style>
<div></div> 
<table>
<tr>
<td style="width:370px;">
<div style="font-size:2em; font-weight:bold">DEBIT NOTE'.$draft.'</div><br>
<table cellpadding="6" style="border:1px solid #333;"> 
<tr><td style="height: 120px"><strong>'. $name .'</strong><br>'. str_replace(chr(13),'<br>',$invoiceAddress) .'</td></tr>
</table>
</td>
<td style="width:300px;">
</td>
</tr>
</table>

<div style="clear:both"></div>
Dear Sir or Madam,
We had debet your account for the following transaction below:
<div style="clear:both"></div>  
<table cellpadding="4">
<tr>
<td style="text-align:center; border:1px solid #333;"><strong>Invoice No</strong></td>
<td style="text-align:center; border:1px solid #333;"><strong>Invoice Date</strong></td>
<td style="text-align:center; border:1px solid #333;"><strong>Term Of Payment</strong></td>
<td style="text-align:center; border:1px solid #333;"><strong>Our Ref.</strong></td>
<td style="text-align:center; border:1px solid #333;"><strong>Your Ref.</strong></td>
</tr>
<tr>
<td style="border:1px solid #333;"><div style="text-align: center;">'. $rs[0]['code'] .'</div></td>
<td style="border:1px solid #333;"><div style="text-align: center; ">'. $obj->formatDBDate($rs[0]['trdate'],'d M Y',array('returnOnEmpty'=>true)) .'</div></td>
<td style="border:1px solid #333;"><div style="text-align: center;">' . $rsTOP[0]['name']. '</div></td>
<td style="border:1px solid #333;"><div style="text-align: center;">' . $rsDetail[0]['socode']. '</div></td>
<td style="border:1px solid #333;"><div style="text-align: center;">'. $rsJobOrder[0]['ponumber'] . '</div></td>
</tr>
<tr>
<td style="text-align:center; border:1px solid #333;"><strong>Bill of Loading</strong></td>
<td style="text-align:center; border:1px solid #333;"><strong>Vessel / Voyage</strong></td>
<td style="text-align:center; border:1px solid #333;"><strong>Port of Loading</strong></td>
<td style="text-align:center; border:1px solid #333;"><strong>ETD POL / ETA POD</strong></td>
<td style="text-align:center; border:1px solid #333;"><strong>Port of Discharge</strong></td>
</tr>
<tr>
<td style="border:1px solid #333;"><div style="text-align: center;">'. $rsJobOrderDetail[0]['hbl'] . '</div></td>
<td style="border:1px solid #333;"><div style="text-align: center; ">'. $rsJobOrder[0]['vesselname'] . ' '. $rsJobOrder[0]['vesselnumber'] . '<br>'. $rsJobOrder[0]['carriername'] .'</div></td>
<td style="border:1px solid #333;"><div style="text-align: center; ">'.$rsJobOrder[0]['polname'] .'</div></td>
<td style="border:1px solid #333;"><div style="text-align: center;">'. $obj->formatDBDate($rsJobOrder[0]['etdpol'],'d M Y',array('returnOnEmpty'=>true)).'<br>'.$obj->formatDBDate($rsJobOrder[0]['etapod'],'d M Y',array('returnOnEmpty'=>true)).'</div></td>
<td style="border:1px solid #333;"><div style="text-align: center;">'.$PODName. '</div></td>
</tr> 
</table>
<div style="clear:both"></div>';

$html .= '<table cellpadding="4" class="table-transaction">';
    
$cellArray = array ();
array_push($cellArray, array('label' => 'Charge Description', 'align' => 'center'));
array_push($cellArray, array('label' => 'Qty', 'align' => 'center', 'width' => '50'));
array_push($cellArray, array('label' => 'Price', 'align' => 'right', 'width' => '135'));
array_push($cellArray, array('label' => 'Cur', 'align' => 'center', 'width' => '50'));
array_push($cellArray, array('label' => 'Rate', 'align' => 'center', 'width' => '70'));
array_push($cellArray, array('label' => 'Amount', 'align' => 'right', 'width' => '100'));
$html .= $obj->generatePrintTableRow( array('class' => 'col-header','docWidth' => '672', 'cell' =>  $cellArray));  
 
$totalSell= 0;
//$rate = ($rs[0]['currencykey'] == CURRENCY['idr'] ) ? 1 : $rs[0]['rate'] ;
    
for($i=0;$i<count($rsDetail);$i++){ 
    
    $rsInvoiceDetail = $obj->getItemDetail($rsDetail[$i]['pkey']);
     
    for($j=0; $j<count($rsInvoiceDetail);$j++){ 
        
        $itemname = (!empty($rsInvoiceDetail[$j]['aliasname'])) ? $rsInvoiceDetail[$j]['aliasname'] : $rsInvoiceDetail[$j]['itemname']; 
     /*   $desc = ($i == 0 ) ? $rsDetail[0]['description'] : '';
        $desc = (!empty($desc)) ? '<br>' . $desc : '';*/
            
        $rate = ($rs[0]['currencykey'] == $rsInvoiceDetail[$j]['currencykey']) ? 1 : $rsInvoiceDetail[$j]['rate'] ;
        
        $html .=' 
            <tr class="detail-row">
                <td>'.$itemname.'</td>
                <td style="text-align:center">'.$obj->formatNumber($rsInvoiceDetail[$j]['qtyinbaseunit'],-2,'.',',').'</td>
                <td style="text-align:right">'.$obj->formatNumber($rsInvoiceDetail[$j]['priceinunit'],2,'.',',').'</td>
                <td style="text-align:center">'.$rsCurrency[$rsInvoiceDetail[$j]['currencykey']].'</td>
                <td style="text-align:center">'.$obj->formatNumber($rate,2,'.',',').'</td> 
                <td style="text-align:right">'.$obj->formatNumber($rsInvoiceDetail[$j]['total'],$currencyDecimal,'.',',').'</td>
            </tr>
            '; 
    }
 
}
    
$arrSubtotal = array(); 

if ($rs[0]['finaldiscount'] != 0){
    if ($rs[0]['finaldiscounttype'] == 2)
        $rs[0]['finaldiscount'] = $rs[0]['finaldiscount']/100 * $rs[0]['subtotal'];
 
    $rs[0]['finaldiscount'] *= -1;
   array_push($arrSubtotal, '<tr><td colspan="3" style="text-align:right; border:1px solid #333; font-weight:bold;">'.strtoupper($obj->lang['discount']).'</td><td style="border:1px solid #333; text-align:center">'.$rsCurrency[$rs[0]['currencykey']].'</td><td style="border:1px solid #333;"></td><td style="border:1px solid #333; text-align:right;">'.$obj->formatNumber($rs[0]['finaldiscount'],$currencyDecimal,'.',',').'</td></tr>');
}
    
if ($rs[0]['taxvalue'] != 0){
    array_push($arrSubtotal, '<tr><td colspan="3" style="text-align:right; border:1px solid #333; font-weight:bold;">'.strtoupper($obj->lang['beforeTax']).'</td><td style="border:1px solid #333; text-align:center">'.$rsCurrency[$rs[0]['currencykey']].'</td><td style="border:1px solid #333;"></td><td style="border:1px solid #333; text-align:right;">'.$obj->formatNumber($rs[0]['beforetaxtotal'],$currencyDecimal,'.',',').'</td></tr>');
    array_push($arrSubtotal, '<tr><td colspan="3" style="border:1px solid #333; text-align:right; font-weight:bold;">'.strtoupper($obj->lang['tax']).'</td><td style="border:1px solid #333; text-align:center">'.$rsCurrency[$rs[0]['currencykey']].'</td><td style="border:1px solid #333;"></td><td style="border:1px solid #333; text-align:right;"  >'.$obj->formatNumber($rs[0]['taxvalue'],$currencyDecimal,'.',',').'</td></tr>');

}   
 
    
if ($rs[0]['othercost'] != 0){
    array_push($arrSubtotal, '<tr><td colspan="3" style="text-align:right; border:1px solid #333; font-weight:bold;">MATERAI</td><td style="border:1px solid #333; text-align:center">IDR</td><td style="border:1px solid #333;"></td><td style="border:1px solid #333; text-align:right;">'.$obj->formatNumber($rs[0]['othercost'],$currencyDecimal,'.',',').'</td></tr>');  
}   
    
$html .= implode('',$arrSubtotal);
    
        
$html .= '
    <tr>
        <td colspan="3" style="text-align:right; border:1px solid #333; font-weight: bold">'.strtoupper($obj->lang['amount']).'</td>
        <td style="border:1px solid #333; text-align:center">'.$rsCurrency[$rs[0]['currencykey']].'</td> 
        <td style="border:1px solid #333;"></td> 
        <td style="border:1px solid #333; text-align:right">'.$obj->formatNumber($rs[0]['grandtotal'],$currencyDecimal,'.',',').'</td> 
    </tr>';
    
$html .= '</table>';

$html .= '<div></div><table cellpadding="4" style="border:1px solid #333;"><tr><td><strong>Container / Seal No.</strong><br>'. str_replace(chr(13),', ',$rsJobOrder[0]['containernumber']).'</td></tr></table>';
    
$topSaid = ($rsTOP[0]['duedays'] > 0 ) ? $rsTOP[0]['duedays'] . ' ' . $obj->lang['day'] : $obj->lang['cash'];
     
$leftside = '<strong>'.$obj->lang['saidAmount'].' :</strong><br>'.ucwords($sayNumber);

if(!empty($trnotes))    
$leftside .=  '<div style="clear:both"></div><strong><span style="text-decoration:underline">'.$obj->lang['note'].' :</span></strong> <br>'.implode('<br>',$trnotes);

if(!empty($rsPaymentMethod)){
    $leftside .= '<div style="clear:both"></div>
              <strong><u>Please tt to our account below :</u></strong><br><table cellpadding="0" style="width:350px;"> 
                <tr><td style="width: 100px"><strong>Bank </strong></td><td>'.$rsPaymentMethod[0]['bankname'].'</td></tr>
                <tr><td><strong>Ac. No.</strong></td><td>'.$rsPaymentMethod[0]['bankaccountnumber'].'</td></tr>
                <tr><td><strong>Undersign</strong></td><td>'.$rsPaymentMethod[0]['bankaccountname'].'</td></tr>
               ';
    if (!empty($rsPaymentMethod[0]['bankcode']))
        $leftside .= ' <tr><td><strong>Bank Code</strong></td><td>'.$rsPaymentMethod[0]['bankcode'].'</td></tr>';
        
   if (!empty($rsPaymentMethod[0]['swiftcode']))
        $leftside .= ' <tr><td><strong>Swift Code</strong></td><td>'.$rsPaymentMethod[0]['swiftcode'].'</td></tr>';
        
   $leftside .= ' </table>';
    
}
$leftside .=  '<div style="clear:both;"></div>'.$obj->loadSetting('emailInvoiceFooter'); 
    
$html .= '<div style="clear:both;"></div>
          <table>
              <tr>
                  <td style="width: 450px">'.$leftside.'</td>
                  <td style="width: 200px">
                    <table cellpadding="4">
                                <tr>
                                    <td style="height:100px;"></td> 
                                </tr> 
                                <tr>
                                    <td style="text-align:right; text-decoration:underline"><strong>AUTHORIZED SIGNATURE</strong></td> 
                                </tr> 
                    </table>
                 </td>
             </tr>
         </table>
          ';   
     
return '<div style="font-size:1.1em; font-weight: bold; ">'.$html.'</div>';
}

?>
