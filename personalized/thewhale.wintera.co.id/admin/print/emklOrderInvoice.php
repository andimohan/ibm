<?php 

$showHeader = (isset($_GET['header']) && $_GET['header'] == 0) ? false : true;

$pdf->setCustomSettings(
    array( 
         'showPrintHeader' => $showHeader,
         'showPrintFooter' => false,
		 'logoSize' => '45,22',
         'footer' => '',  
         ) 
);


 
$generateReportContent = function ($dataset){ 

$obj = new EMKLOrderInvoice();  
$emklJobOrder = new EMKLJobOrder();
$customer = new Customer(); 
$currency = new Currency();
$setting = new Setting(); 
$termOfPayment = new TermOfPayment();
$paymentMethod = new PaymentMethod();    

$rs = $dataset['rs']; 
    
$rsCurrency = $currency->searchData();
$rsCurrency = array_column($rsCurrency,'name','pkey');
 
$rsDetail = $obj->getDetailById($rs[0]['pkey']);
$rsCustomer = $customer->getDataRowById($rs[0]['customerkey']);
$rsJobOrderDetail = $emklJobOrder->getDetailByColumn($emklJobOrder->tableNameDetail.'.pkey',$rsDetail[0]['salesorderkey']);  

$criteria = ' and '.$emklJobOrder->tableName.'.pkey = '.$rsDetail[0]['refsalesorderheaderkey'];
$rsJobOrder = $emklJobOrder->searchData('','',true,$criteria );

$dateReturnOnEmpty = array('returnOnEmpty'=>true, 'value' => '00 / 00 / 0000');
$rsTOP = $termOfPayment->getDataRowById($rs[0]['termofpaymentkey']);

$date = new DateTime($rs[0]['trdate']);
$date->add(new DateInterval('P' . $rsTOP[0]['duedays'] . 'D'));

$currencyDecimal = 2; //($rs[0]['currencykey'] == CURRENCY['idr']) ? 0 : 2;
    
$grandTotal = $obj->formatNumber($rs[0]['grandtotal'],$currencyDecimal,'.',',');
$sayNumber = $obj->sayNumberInEnglish($rs[0]['grandtotal']);    

$rsCompanyBank= $paymentMethod->getDataRowById($rs[0]['companybankkey']);
    
    
$arrCustomer = array();
    
$invoiceAddress = (!empty($rs[0]['invoiceaddress']))  ? $rs[0]['invoiceaddress']: $rsCustomer[0]['address'];
	
if (!empty($rsCustomer[0]['name'])) array_push($arrCustomer, $rsCustomer[0]['name']); 
if (!empty($invoiceAddress)) array_push($arrCustomer, str_replace(chr(13),'<br>',$invoiceAddress)); 
    
$companyPhone = $setting->getDetailByCode('companyPhone');
$companyCode = $setting->getDetailByCode('code');;
$companyAddress = $setting->loadSetting('companyAddress');
$companyAddress = str_replace(chr(13),'<br>',$companyAddress);
	
$arrCompanyPhone = array();  
for($i=0;$i<count($companyPhone);$i++) 
    array_push($arrCompanyPhone, 'Telp :'.$companyPhone[$i]['value']);

    $topSaid = ($rsTOP[0]['duedays'] > 0 ) ? $rsTOP[0]['duedays'] . ' DAYS ' : $obj->lang['cash'];

$companyName = strtoupper($setting->loadSetting('companyName'));

$html = $obj->printSetting['defaultStyle'];

//detail
$cellArray = array();
array_push($cellArray, array('label' => $obj->lang['number'], 'width' => '30')); 
array_push($cellArray, array('label' => 'Service Type')); 
array_push($cellArray, array('label' => 'Qty', 'align' => 'center','width' => '80'));
array_push($cellArray, array('label' => '','align' => 'right', 'width' => '35'));
array_push($cellArray, array('label' => 'Amount','align' => 'right', 'width' => '85'));
array_push($cellArray, array('label' => 'Rate','align' => 'right', 'width' => '70'));
array_push($cellArray, array('label' => '','align' => 'right', 'width' => '35'));
array_push($cellArray, array('label' => 'Exch. Amount','align' => 'right', 'width' => '95'));
array_push($cellArray, array('label' => 'Total Amount','align' => 'right', 'width' => '95'));


$detail = '<table  cellpadding="4" class="table-transaction">';
$detail .= $obj->generatePrintTableRow( array('class' => 'col-header', 'cell' =>  $cellArray));

$rsContanerDetail = $emklJobOrder->getDetailContainer($rsDetail[0]['refsalesorderheaderkey']);
$constainers = array();
foreach ($rsContanerDetail as $key) {
    array_push($constainers,$key['containerno']);
}
    
$constainers = implode(", ",$constainers);


$ctr = 0;
for ($i=0;$i<count($rsDetail);$i++){  
      
    $rsInvoiceDetail = $obj->getItemDetail($rsDetail[$i]['pkey']);

    if(empty($rsInvoiceDetail)) continue;
    
    for($j=0; $j<count($rsInvoiceDetail);$j++){
        
        $rsContainer = $emklJobOrder->getItemDetail( $rsDetail[$i]['salesorderkey'] , $rsInvoiceDetail[$j]['refsodetailkey']);  
        $containerName = (!empty($rsContainer[0])) ? ' x '.$rsContainer[0]['containername'] : "";

        $rate = ($rs[0]['currencykey'] == $rsInvoiceDetail[$j]['currencykey']) ? 1 : $rsInvoiceDetail[$j]['rate'] ;
        $itemname = (!empty($rsInvoiceDetail[$j]['aliasname'])) ? $rsInvoiceDetail[$j]['aliasname'] : $rsInvoiceDetail[$j]['itemname'];
                
        $priceInCurrency = ($rsInvoiceDetail[$j]['currencykey'] == CURRENCY['idr']) ? $rsInvoiceDetail[$j]['priceinunit'] / $rate : $rsInvoiceDetail[$j]['priceinunit'] * $rate;

        $detail .= '<tr>
          <td style="text-align:right">'.(++$ctr).'</td>  
          <td>'.$itemname.'</td>  
          <td style="text-align:center">'.$obj->formatNumber($rsInvoiceDetail[$j]['qtyinbaseunit'],-2,'.',',').$containerName.'</td>  
          <td>'.$rsCurrency[$rsInvoiceDetail[$j]['currencykey']].'</td>
          <td style="text-align:right">'.$obj->formatNumber($rsInvoiceDetail[$j]['priceinunit'],2,'.',',').'</td>  
          <td style="text-align:right">'.$obj->formatNumber($rate,2,'.',',').'</td>  
          <td style="text-align:center">'.$rsCurrency[$rs[0]['currencykey']].'</td>
          <td style="text-align:right">'.$obj->formatNumber($priceInCurrency ,$currencyDecimal,'.',',').'</td>
          <td style="text-align:right">'.$obj->formatNumber($rsInvoiceDetail[$j]['total'],$currencyDecimal,'.',',').'</td>
          </tr>' ; 
     
    }
}
    
$detail .= '</table>' ;

$detail .= '<table cellpadding="2" >' ;

$arrSubtotal = array(); 

if ($rs[0]['finaldiscount'] != 0){
    if ($rs[0]['finaldiscounttype'] == 2)
        $rs[0]['finaldiscount'] = $rs[0]['finaldiscount']/100 * $rs[0]['subtotal'];
 
    $rs[0]['finaldiscount'] *= -1;
   array_push($arrSubtotal, '<tr><td colspan="5" style="text-align:right;font-weight:bold;">Discount</td><td style="text-align:center">'.$rsCurrency[$rs[0]['currencykey']].'</td><td style=" text-align:right;width:99px">'.$obj->formatNumber($rs[0]['finaldiscount'],$currencyDecimal,'.',',').'</td></tr>');
}
    
if ($rs[0]['taxvalue'] != 0){
    array_push($arrSubtotal, '<tr><td colspan="5" style="text-align:right; font-weight:bold;">Before Tax</td><td style=" text-align:center">'.$rsCurrency[$rs[0]['currencykey']].'</td><td style=" text-align:right;width:99px">'.$obj->formatNumber($rs[0]['beforetaxtotal'],$currencyDecimal,'.',',').'</td></tr>');
    array_push($arrSubtotal, '<tr><td colspan="5" style=" text-align:right; font-weight:bold;">Tax</td><td style=" text-align:center">'.$rsCurrency[$rs[0]['currencykey']].'</td><td style=" text-align:right;width:99px">'.$obj->formatNumber($rs[0]['taxvalue'],$currencyDecimal,'.',',').'</td></tr>');

}   


if ($rs[0]['othercost'] != 0){
    array_push($arrSubtotal, '<tr><td colspan="5" style="text-align:right;font-weight:bold;">Other Cost</td><td style=" text-align:center">IDR</td><td style=" text-align:right;width:99px">'.$obj->formatNumber($rs[0]['othercost'],$currencyDecimal,'.',',').'</td></tr>');  
}   
    
$detail .= implode('',$arrSubtotal);
    
        
$detail .= '
    <tr>
        <td colspan="3" style="text-align:center;color:blue;">Credit Term : '.$topSaid.'</td>
        <td colspan="2" style="text-align:right;font-weight: bold">Total</td>
        <td style=" text-align:center">'.$rsCurrency[$rs[0]['currencykey']].'</td> 
        <td style=" text-align:right;width:99px">'.$grandTotal.'</td> 
    </tr>';

$detail .= '</table>' ;

$style = '
<style>  
.text-center{text-align:center}.text-right{text-align:right}.header-title{width:70px}.header-body{width:150px}.body-title{width:110px}.body-body{width:220px}.footer-title{width:80px}.footer-body{width:280px}.colon{width:15px}.bold{font-weight:700}
</style>
';

    
$header='
<table cellpadding="2">
	<tr>
		<td style="width: 380px">
			<br><br><br>
			<table> 
				 <tr>
					<td class="body-title">Customer</td>
					<td class="colon"> : </td>
					<td style="width:270px">'.implode('<br>',$arrCustomer).'</td>
				</tr> 
			</table>
		</td>
		<td style="width: 50px">
		
		</td>
		<td style="width: 270px">
			<table cellpadding="2" >
				<tr>
					<td colspan="3" style="font-weight:bold;font-size:20px;text-align:left;">CUSTOMER INVOICE</td>
				</tr>
				 <tr>
					<td style="width:90px">Doc No</td>
					<td class="colon"> : </td>
					<td style="width:140px">'.$rs[0]['code'].'</td>
				</tr>
				 <tr>
					<td style="width:90px">Doc Date</td>
					<td class="colon"> : </td>
					<td style="width:140px">'.$obj->formatDBDate($rs[0]['trdate'],'d / m / Y').'</td>
				</tr>
				<tr>
					<td style="width:90px">Due Date</td>
					<td class="colon"> : </td>
					<td style="width:140px">'.$date->format('d / m / Y').'</td>
				</tr>
			</table>
		</td>
	</tr> 
</table>
<div style="clear:both"></div>
';

$body='
<table >
    <tr>
        <td>
            <table cellpadding="2">
                <tr>
                    <td class="body-title">Customer PI/ PO Ref</td>
                    <td class="colon"> : </td>
                    <td class="body-body">'.$rsJobOrder[0]['ponumber'].'</td>
                </tr>
                <tr>
                    <td class="body-title">Feeder Vessel</td>
                    <td class="colon"> : </td>
                    <td class="body-body">'.$rsJobOrder[0]['feedervesselname'].'</td>
                </tr>
                <tr>
                    <td class="body-title">Feeder Voy.</td>
                    <td class="colon"> : </td>
                    <td class="body-body">'.$rsJobOrder[0]['feedernumber'].'</td>
                </tr>
                <tr>
                    <td class="body-title">Mother Vessel</td>
                    <td class="colon"> : </td>
                    <td class="body-body">'.$rsJobOrder[0]['vesselname'].'</td>
                </tr>
                <tr>
                    <td class="body-title">Mother Voy.</td>
                    <td class="colon"> : </td>
                    <td class="body-body">'.$rsJobOrder[0]['vesselnumber'].'</td>
                </tr>
          
                <tr>
                    <td class="body-title">Master BL No</td>
                    <td class="colon"> : </td>
                    <td class="body-body">'.$rsJobOrder[0]['mblnumber'].'</td>
                </tr>

            </table>
        </td>
        <td>
            <table cellpadding="2">
                <tr>
                    <td class="body-title">Job Sheet No</td>
                    <td class="colon"> : </td>
                    <td class="body-body">'.$rsJobOrder[0]['code'].'</td>
                </tr>
                <tr>
                    <td class="body-title">Port of Loading</td>
                    <td class="colon"> : </td>
                    <td class="body-body">'.$rsJobOrder[0]['polname'].'</td>
                </tr>
                <tr>
                    <td class="body-title">Port of Discharge</td>
                    <td class="colon"> : </td>
                    <td class="body-body">'.$rsJobOrder[0]['podname'].'</td>
                </tr>
                <tr>
                    <td class="body-title">ETD</td>
                    <td class="colon"> : </td>
                    <td class="body-body">'.$obj->formatDBDate($rsJobOrder[0]['etdpol'], 'd / m / Y',$dateReturnOnEmpty).'</td>
                </tr>
                <tr>
                    <td class="body-title">ETA</td>
                    <td class="colon"> : </td>
                    <td class="body-body" >'.$obj->formatDBDate($rsJobOrder[0]['etapod'], 'd / m / Y',$dateReturnOnEmpty).'</td>
                </tr>
                <tr>
                    <td class="body-title">House BL No</td>
                    <td class="colon"> : </td>
                    <td class="body-body">'.$rsJobOrderDetail[0]['hbl'].'</td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<div style="clear:both"></div>
';

$footer ='

<table cellpadding="2" >
    <tr> <td class="bold">Containers</td></tr>
    <tr><td>'.$constainers.'</td></tr>
</table>
<div style="clear:both"></div>

<table cellpadding="2" >
    <tr> <td class="bold">Note :</td></tr>
    <tr><td>'.str_replace(chr(13),'<br>',$rs[0]['trdesc']).'</td></tr>
</table>


<div style="clear:both"></div>

<table>
    <tr>
        <td style="width: 100px;">
            <table>
                <tr>
                    <td class="bold"></td>
                </tr> 

            </table>
        </td>
        <td style="width: 430px;">
            <table cellpadding="2" >
                <tr>
                    <td colspan="0">In case of any discrepancy, please notify us within 7 days of invoice receipt
                        otherwise this invoice will be deemed correct and payable in full.
                        Late payment of invoice will be subject to 2% interest per month
                        or part thereof of outstanding amount until settled.<br>
                    </td>
                </tr>
                <tr>
                    <td colspan="0">Payments should be made to:</td>
                </tr>
                <tr>
                    <td class="footer-title">Beneficiary</td>
                    <td class="colon">:</td>
                    <td class="footer-body">'.$rsCompanyBank[0]['bankaccountname'].'</td>
                </tr>
                <tr>
                    <td class="footer-title">A/C No.</td>
                    <td class="colon">:</td>
                    <td class="footer-body" >'.$rsCompanyBank[0]['bankaccountnumber'].'</td>
                </tr>
                <tr>
                    <td class="footer-title">Bank</td>
                    <td class="colon">:</td>
                    <td class="footer-body" >'.$rsCompanyBank[0]['bankname'].'</td>
                </tr>
                <tr>
                    <td class="footer-title">Bank Add.</td>
                    <td class="colon">:</td>
                    <td class="footer-body" >'.nl2br($rsCompanyBank[0]['bankaddress']).'</td>
                </tr>
                <tr>
                    <td class="footer-title">Swift Code</td>
                    <td class="colon">:</td>
                    <td class="footer-body" >'.$rsCompanyBank[0]['swiftcode'].'</td>
                </tr>
            </table>
        </td>
        <td style="width: 150px;">
            <table>
                <tr>
                    <td class="bold text-center">Yours Faithfully</td>
                </tr>
                <tr>
                    <td style="height: 80px;"></td>
                </tr>
                <tr>
                    <td class="text-center">Accounting</td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<div style="clear:both"></div>
';
$footerInformation = '
<table style="font-size: 0.9em;">
    <tr>
        <td style="width: 50%;"><b>'.$companyName.'</b></td>
        <td style="width: 50%;">'.$obj->lang['email'].' :</td>
    </tr>
    <tr>
        <td style="width: 50%;">'.$companyAddress.'</td>
        <td>For outbound inquiries, please contact:<br>
cs.export@thewhale.co.id<br>
clearance.export@thewhale.co.id<br><br>

For inbound inquiries, please contact:<br>
cs.import@thewhale.co.id<br>
clearance.import@thewhale.co.id<br>
</td>
</tr>
</table>
';
 
$html .=$style;
//$html .=$title;
$html .= (isset($_GET['header']) && $_GET['header'] == 0) ? '<table><tr><td style="height:99px;"></td></tr></table>' : '';
$html .=$header;
$html .=$body;
$html .=$detail;
$html .=$footer;
$html .= (isset($_GET['header']) && $_GET['header'] == 0) ? '<table><tr><td style="height:50px;"></td></tr></table>' : $footerInformation;
    
    
return '<div style="font-size:11px">'.$html.'</div>';


}
?>