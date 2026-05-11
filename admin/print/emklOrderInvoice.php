<?php 

includeClass(array('EMKLOrderInvoice.class.php','Item.class.php'));
$emklOrderInvoice = createObjAndAddToCol(new EMKLOrderInvoice());

$obj = $emklOrderInvoice; 
 
$generateReportContent = function ($dataset){ 
 
$obj = new EMKLOrderInvoice(); 
$item = new Item();
//$service = new Service(SERVICE);
$emklJobOrder = new EMKLJobOrder(EMKL['jobType']['export']);
$container = new Container();
$employee = new Employee();
$customer = new Customer(); 
$currency = new Currency();
$emklInvoiceOrderDetail = array(); 
$arrCurrency = $currency->searchData();
$arrCurrency = array_column($arrCurrency,'name','pkey'); 
$rsContainer = $container->searchData();
$rsContainer = array_column($rsContainer,'name','pkey');
/*
$rsService = $service->searchData();  
$rsService = array_column($rsService,'name','pkey');*/

$termOfPayment = new TermOfPayment();
      
$rs = $dataset['rs']; 
$rsDetail = $obj->getDetailWithRelatedInformation($rs[0]['pkey']);
 
$rsCurrency = $currency->getDataRowById($rs[0]['currencykey']); 
$rsJobOrder = $emklJobOrder->searchData($emklJobOrder->tableName.'.pkey',$rsDetail[0]['refsalesorderheaderkey']); 
$rsJobOrderDetail = $emklJobOrder->getDetailByColumn($emklJobOrder->tableNameDetail.'.pkey',$rsDetail[0]['salesorderkey']); 
$pkey = $rsDetail[0]['pkey'];

$sayNumber = $obj->sayNumber($rs[0]['grandtotal']);
/*$refKey = $rsDetail[0]['salesorderkey'];
$jobkey = $rsDetail[0]['refsalesorderheaderkey'];  
$vesselkey = $rsJobOrder[0]['vesselkey'];
$rsVessel = $vessel->getDataRowById($vesselkey);
$itemkey = $rsDetail[0]['pkey'];
$rsItem = $obj->getItemDetail($itemkey);*/
/* 
$podkey = $rsJobOrder[0]['podkey'];
$polkey = $rsJobOrder[0]['polkey'];
 */
//$rsPolKey = $port->getDataRowById($polkey);    
//$rsPodKey = $port->getDataRowById($podkey);
$rsCustomer = $customer->getDataRowById($rs[0]['customerkey']);
$rsTOP =   $termOfPayment->getDataRowById($rs[0]['termofpaymentkey']); 
$name='';
if(!empty($rsCustomer[0]['alias']))
    $name = $rsCustomer[0]['alias'];
else
    $name = $rsCustomer[0]['name'];
    
$trnotes = (!empty($rs[0]['trdesc'])) ? '<div style="clear:both"></div><strong>Catatan :</strong> <br>' . str_replace(chr(13),'<br>',$rs[0]['trdesc']) : '';
$profileImg = $obj->loadSetting('companyLogo'); 
$img =  HTTP_HOST.'phpthumb/phpThumb.php?src='.$obj->phpThumbURLSrc.'setting/companyLogo/'.$profileImg.'&w=150&h=70&hash='.getPHPThumbHash($profileImg);
$html = $obj->printSetting['defaultStyle'];

$html .= ' 
<table>
<tr>
<td style="width:300px;">
<br><br><br>
<table>
<tr><td style="font-size:20px;"><b>INVOICE</b></td></tr>
<tr>
<td>
<table cellpadding="6" style="border:1px solid #333;"> 
<tr><td>
<b>'. $name .'</b><br>'.str_replace(chr(13),'<br>',$rsCustomer[0]['address']).'
</td></tr>
</table>
</td>
</tr>
</table>
</td>
<td style="width:110px;"></td>
<td style="width:300px;">
<br><br><br>
<table> 
<tr><td style="width:250px; text-align:right"><img src="'.$img.'" /></td></tr>
<tr><td style="text-align:center">'.str_replace(chr(13),'<br>',$obj->loadSetting('companyAddress')).' </td></tr>
</table> 
</td>
</tr>
</table>
<div style="clear:both"></div> 
<table cellpadding="6" >
<tr>
<td></td>
<td></td>
<td></td>
<td></td>
<td style="border:1px solid #333;"><p>Invoice No.</p><td style="text-align: center; font-size: 12px; font-weight: bold;">'. $rs[0]['code'] .'</td></td>
</tr>

</table>     
<table cellpadding="6" >
<tr>
<td style="border:1px solid #333;"><p >Invoice Date.</p><td style="text-align: center; font-size: 12px;">'. $obj->formatDBDate($rs[0]['trdate'],'d / m / y') .'</td></td>
<td style="border:1px solid #333;"><p >Our ref.</p></td>
<td style="border:1px solid #333;"><p>Your ref.</p></td>
<td style="border:1px solid #333;"><p>Bill of loading No.</p></td>
<td style="border:1px solid #333;"><p>Port of loading.</p><td style="text-align: center; font-size: 12px;">'.$rsJobOrder[0]['podname'] .'</td></td>
</tr>

</table>
<table cellpadding="6">
<tr>
<td style="border:1px solid #333;"><p>Ex. Rate</p><td style="text-align: center; font-size: 12px;">'. $obj->formatNumber($rs[0]['rate']) .'</td></td>
<td style="border:1px solid #333;"><p>ETD POL / ETA POD</p> <td style="text-align: center; font-size: 12px;">'. $obj->formatDBDate($rsJobOrder[0]['etdpol'],'d / m / y') .' - <br>'. $obj->formatDBDate($rsJobOrder[0]['etapod'],'d / m / y') .'</td></td>
<td style="border:1px solid #333;" colspan="2"><p>Vessel / Voyage</p><td style="text-align: center; font-size: 12px;">'. $rsJobOrder[0]['vesselname'] . ' / '. $rsJobOrder[0]['carriername'] .'</td></td>
<td style="border:1px solid #333;"><p>Port of Discharge</p><td style="text-align: center; font-size: 12px;">'.$rsJobOrder[0]['polname'] . '</td></td>
</tr>

</table>
<div style="clear:both"></div>
';
$tableItem = '<table cellpadding="10" style="border:1px solid #333;">
    <tr>
        <th colspan="4" style="border:1px solid #333; text-align:center">Charge Description</th>
        <th style="border:1px solid #333; text-align:center">QTY</th>
        <th colspan="2" style="border:1px solid #333; text-align:center">Rate</th>
        <th style="border:1px solid #333; text-align:center">Cur</th>
        <th colspan="2" style="border:1px solid #333; text-align:right">Amount</th>
    </tr>';
    
$totalSell= 0;
    
for($i=0;$i<count($rsDetail);$i++){
     
    $rsInvoiceDetail = $obj->getItemDetail($rsDetail[$i]['pkey']);
    
    if(empty($rsInvoiceDetail)) continue;
    
    for($j=0; $j<count($rsInvoiceDetail);$j++){
        
        $rate = ($rs[0]['currencykey'] == CURRENCY['idr'] ) ? 1 : $rs[0]['rate'] ;
         
        $tableItem .=' 
            <tr>
                <td colspan="4" style="border:1px solid #333; text-align:center">'.$rsInvoiceDetail[$j]['itemname'].'</td>
                <td style="border:1px solid #333; text-align:center">'.$obj->formatNumber($rsInvoiceDetail[$j]['qtyinbaseunit']).'</td>
                <td colspan="2" style="border:1px solid #333; text-align:center">'.$rs[0]['currencykey'].'</td>
                <td style="border:1px solid #333; text-align:center">'.$obj->formatNumber($rate,-2).'</td>
                <td colspan="2" style="border:1px solid #333; text-align:center">'.$obj->formatNumber($rsInvoiceDetail[$j]['priceinunit'],-2).'</td>
            </tr>
            '; 
        
    }
}
    
$tableItem .=  '<tr>
        <td rowspan="2" colspan="7" style="border:1px solid #333; text-align:center">
        <p>AMOUNT DUE</p></td>
        <td  style=" text-align:center">USD
        </td>
        <td colspan="2" style="border:1px solid #333; text-align:center">
        </td>
    </tr>';
$tableItem .=   ' <tr style="border:1px solid #333;">
        <td style="border:1px solid #333; text-align:center">IDR
        </td>
        <td colspan="2" style="border:1px solid #333; text-align:center">'.$obj->formatNumber($rsInvoiceDetail[0]['total']).'</td>
        
    </tr>';
$tableItem .= '</table>';

$tableItem .= '<div style="clear:both"></div>';
$tableItem .= '<div style="clear:both"></div>';

    
$html .= $tableItem;

$html .= $obj->generateSignLabel($rs); 
return $html;
}

?>