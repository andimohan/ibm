<?php  
 $PRINT_SETTINGS =  array(   
    'showPrintHeader' => false,
    'showPrintFooter' => false,
);
includeClass(array('DisposalSalesWasteInvoice.class.php'));
$disposalSalesWasteInvoice = createObjAndAddToCol(new DisposalSalesWasteInvoice());

$obj = $disposalSalesWasteInvoice;
 
  
$generateReportContent = function ($dataset){  

    $obj = new DisposalSalesWasteInvoice();
    $customer = new Customer();
    $disposalJobOrder = new DisposalJobOrder();
    $termOfPayment = new TermOfPayment();
    $setting = new Setting();

    $rs = $dataset['rs'];
    $rsDetail = $obj->getDetailWithRelatedInformation($rs[0]['pkey']);

    $rsTOP = $termOfPayment->getDataRowById($rs[0]['termofpaymentkey']);
    $rsCustomer = $customer->getDataRowById($rs[0]['customerkey']);
    $topSaid = ($rsTOP[0]['duedays'] > 0 ) ? $rsTOP[0]['duedays'] . ' ' . $obj->lang['day'] : $obj->lang['cash'];
 

    $companyPhone = $setting->getDetailByCode('companyPhone');
    $companyAddress = $setting->loadSetting('companyAddress');
    $companyName = strtoupper($setting->loadSetting('companyName'));
    $invoiceTo = $rs[0]['customername'] .'<br>'.$rsCustomer[0]['address'];
    $isDownpayment = $rs[0]['isdownpayment'];


    $proforma = ($rs[0]['statuskey'] == 1) ? '<div style="font-weight:normal; font-size:0.9em">(PROFORMA)</div>' : '';
    $html = $obj->printSetting['defaultStyle'];

    $htmlDetail = '';

    $totalRs = count($rsDetail);
    $no = 0;
    for($i=0;$i<$totalRs;$i++){ 

        if (!empty($rsDetail[$i]['wastekey'])){ 


            // CONTAINER DETAIL 
                $no++;
                 $serviceName = "Jasa Pemanfaatan Limbah - ".$rsDetail[$i]['wastecode'];
                 $amount = $rsDetail[$i]['priceinunit'];
                 $quantity = $obj->formatNumber($rsDetail[$i]['quantity'], 2) .' '.$rsDetail[$i]['unitname'];
                 $htmlDetail .= '<tr><td style="text-align:right;">'.($no).'.</td><td>'.$serviceName.'</td><td style="text-align:left;">'.$quantity.'</td><td style ="text-align:right">'.$obj->formatNumber($amount).'</td><td style ="text-align:right">'.$obj->formatNumber($rsDetail[$i]['total']).'</td></tr>';

        } 

    } 
    
    
    
$html .= ' 
<div style="clear:both;"></div>
<div style="clear:both;"></div>
<div style="clear:both;"></div>
<div style="clear:both;"></div>
<table cellpadding="2" > 
<tr><td><div class="title">'.strtoupper('INVOICE').'</div></td></tr>
</table>  
<div style="clear:both"></div>
<table >
 <tr>
    <td style="width: 440px">
        <table cellpadding="2"> 
        <tr><td class="header-row-header">'.ucwords($obj->lang['customerCode']).'</td><td style="width:10px; text-align:center">:</td><td style="width:290px;">'.$rs[0]['customercode'].'</td></tr>
        <tr><td class="header-row-header">'.ucwords($obj->lang['company']).'</td><td style="text-align:center">:</td><td>'.$rs[0]['customername'].'</td></tr>
        <tr><td class="header-row-header">'.ucwords($obj->lang['taxIdentificationNumber']).'</td><td style="text-align:center">:</td><td>'.$rsCustomer[0]['taxid'].'</td></tr>
        <tr><td class="header-row-header">'.ucwords($obj->lang['address']).'</td><td style="text-align:center">:</td><td>'.$rsCustomer[0]['address'].'</td></tr>
        </table>
    </td>
    <td style="width: 10px"></td>
    <td style="width: 170px">
        <table cellpadding="2"> 
            <tr><td class="header-row-header" style="width: 80px">'.ucwords($obj->lang['date']).'</td><td style="width:10px; text-align:center">:</td><td style="width:120px;">'.$obj->formatDBDate($rs[0]['trdate'],'d / m / Y').'</td></tr>
            <tr><td class="header-row-header" style="width: 80px">'.ucwords($obj->lang['invoiceId']).'</td><td style="text-align:center">:</td><td>'.$rs[0]['code'].'</td></tr>

        </table>
    </td>
 </tr>
</table>
 ';

$html .= '<div style="clear:both"></div>';

    


$html .='<div style="clear:both"></div>
<table cellpadding="4" class="table-transaction" >
<tr class="col-header"><td style="text-align:left;width:30px">No</td><td style="text-align:left;width:400px; ">'.ucwords($obj->lang['description']).'</td><td style="text-align:left;width:80px;">'.ucwords($obj->lang['waste']).'</td><td style="text-align:right;width:80px;">'.ucwords($obj->lang['price']).'</td><td style="text-align:right;width:80px;">'.ucwords($obj->lang['amount']).'</td></tr>  
';
    
$html .= $htmlDetail;
    
$arrSubtotal = array(); 
     
if ($rs[0]['finaldiscount'] != 0){
    if ($rs[0]['finaldiscounttype'] == 2)
        $rs[0]['finaldiscount'] = $rs[0]['finaldiscount']/100 * $rs[0]['subtotal'];
    
    //$finaldiscount = ($rs[0]['finaldiscount'] != 0) ?  $obj->formatNumber($rs[0]['finaldiscount'] * -1) : 0;  
    $rs[0]['finaldiscount'] *= -1;
    array_push($arrSubtotal, '<tr><td style="text-align:right; font-weight:bold;">'.ucwords($obj->lang['discount']).'</td><td style="text-align:right; font-weight:bold;"  >'.$obj->formatNumber($rs[0]['finaldiscount']).'</td></tr>');
}
    

if ($rs[0]['taxvalue'] != 0){
//    array_push($arrSubtotal, '<tr><td style="text-align:right; font-weight:bold;">'.ucwords($obj->lang['beforeTax']).'</td><td style="text-align:right; font-weight:bold;"  >'.$obj->formatNumber($rs[0]['beforetaxtotal']).'</td></tr>');
    array_push($arrSubtotal, '<tr><td style="text-align:right; font-weight:bold;">'.ucwords($obj->lang['PPN']).'</td><td style="text-align:right; font-weight:bold;"  >'.$obj->formatNumber($rs[0]['taxvalue']).'</td></tr>');
}
    
if ($rs[0]['totaldownpayment'] > 0){
    array_push($arrSubtotal, '<tr><td style="text-align:right; font-weight:bold;">'.ucwords($obj->lang['downpayment']).'</td><td style="text-align:right; font-weight:bold;">'.$obj->formatNumber($rs[0]['totaldownpayment']).'</td></tr>'); 
} 
    
if (!empty($arrSubtotal)) { 
    //$html .= '<tr><td></td> <td style="text-align:right; font-weight:bold;  ">Total</td><td style="text-align:right; font-weight:bold;"  >'.$obj->formatNumber($rs[0]['outstanding']).'</td></tr>';
    array_push($arrSubtotal, '<tr><td style="text-align:right; font-weight:bold;">Total</td><td style="text-align:right; font-weight:bold;">'.$obj->formatNumber($rs[0]['outstanding']).'</td></tr>'); 
} 
    
if ($rs[0]['tax23value'] != 0)  { 
     array_push($arrSubtotal, '<tr><td></td><td></td></tr>'); 
     array_push($arrSubtotal, '<tr><td style="text-align:right; font-weight:bold;">'.ucwords($obj->lang['tax23']).'</td><td style="text-align:right; font-weight:bold;">'.$obj->formatNumber($rs[0]['tax23value']).'</td></tr>'); 
     array_push($arrSubtotal, '<tr><td style="text-align:right; font-weight:bold;">'.ucwords($obj->lang['balance']).'</td><td style="text-align:right; font-weight:bold;">'.$obj->formatNumber(abs($rs[0]['grandtotal']-$rs[0]['tax23value'])).'</td></tr>'); 
    
}
    

     
$subtotalLabel = (!empty($arrSubtotal)) ? ucwords($obj->lang['subtotal']) : ucwords($obj->lang['total']) ; 
    
$sayNumber = $obj->sayNumber($rs[0]['outstanding']);
    
$topSaid = ($rsTOP[0]['duedays'] > 0 ) ? $rsTOP[0]['duedays'] . ' ' . $obj->lang['day'] : $obj->lang['cash'];
    
$html .= '    
</table>  
<div style="clear:both"></div> 
<table cellpadding="4" > 
<tr><td rowspan="'.(count($arrSubtotal) + 1).'" style="width:460px;"><strong>Terbilang</strong> :<br><span style="font-style:italic">'.ucwords($sayNumber).' Rupiah.</span><br><br><strong>'.$obj->lang['termofpayment'].' :</strong> '.$topSaid.'</td><td style="text-align:right; font-weight:bold;  width:100px; ">'.$subtotalLabel.'</td><td style="text-align:right; font-weight:bold;  width:110px;"  >'.$obj->formatNumber($rs[0]['subtotal']).'</td></tr>
';

$html .= implode('',$arrSubtotal); 
 
	$html .='<div style="font-weight:bold">Pembayaran ke BCA Virtual Account :</div>';
	
$html .='
	<br>
	<table cellpadding="4">
		<tr>
			
			<td style="width: 250px">
				<b>Pembayaran via transfer</b><br>
				<table cellpadding="2" >
				<tr>
					<td style="width: 80px">Bank</td>
					<td  style="width:8px">:</td>
					<td style="width: 150px;text-align:left">Bank Central Asia</td>
				</tr>
				<tr>
					<td>a.n</td>
					<td>:</td>
					<td style="text-align:left">Bhuman Catur Lestari PT</td>
				</tr>
				<tr>
					<td>No. Rekening</td>
					<td>:</td>
					<td style="text-align:left">065-6441199</td>
				</tr>
			</table>
			</td>
			<td style="width: 250px">
				<b>Pembayaran via BCA Virtual Account</b><br>
				<table cellpadding="2">
					<tr>
						<td style="width: 60px">No. VA</td>
						<td style="width:8px">:</td>
						<td style="width: 150px; text-align:left">'.$rsCustomer[0]['virtualaccount'].'</td>
					</tr>
					<tr>
						<td>a.n</td>
						<td>:</td>
						<td style="text-align:left">Bhuman Catur Lestari PT</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>  
		<br><br>';
	 
	$html .='<style>
				.table2 {border-style: solid; font-size:0.9em; border-top: 1px; border-right: 1px; border-bottom: 1px; border-left: 1px; border-color:gray; }
			</style>
		<table style="padding; 1px;">
			<tr>
				<td style="width: 20px;">-</td>
				<td style="width: 640px;">Pembayaran harus dilakukan 7 hari setelah invoice diterima dan atau sesuai kesepakatan dalam MOU</td>
			</tr>
			<tr>
				<td></td>
				<td>Invoice yang belum diterima pembayarannya sampai dengan 45 hari dari tanggal invoice, maka secara sistem akan masuk</td>
			</tr>
			<tr>
				<td></td>
				<td>kedalam daftar penghentian pelayanan sementara (Hold).</td>
			</tr>
			<tr>
				<td>-</td>
				<td>Pelayanan setelah penghentian sementara hanya dapat dilakukan kembali apabila semua tagihan telah di selesaikan.</td>
			</tr>
			<tr>
				<td>-</td>
				<td>Keluhan dan Permintaan revisi atas invoice, Kwitansi dan Faktur Pajak setelah 30 hari dari tanggal Invoice tidak dapat dilayani.</td>
			</tr>
			<tr>
				<td>-</td>
				<td>Pelanggan WAPU harus melakukan kewajiban pembayaran PPN sesuai periode pajak dan mengirimkan Bukti Pembayaran</td>
			</tr>
			<tr>
				<td></td>
				<td>ke Bhuman setelah Pembayaran</td>
			</tr>
			<tr>
				<td>-</td>
				<td>Denda karena keterlambatan pembayaran PPN WAPU akan menjadi tanggung jawab Pelanggan</td>
			</tr>
			<tr>
				<td>-</td>
				<td>Pemotongan PPH harus sesuai dengan peraturan yang berlaku dan bukti pemotongan harus disampaikan ke Bhuman</td>
			</tr>
			<tr>
				<td></td>
				<td>apabila pelanggan tidak dapat memberikan bukti pemotongan maka selisih tersebut akan ditagihkan kembali ke pelanggan.</td>
			</tr>
			<tr>
				<td>-</td>
				<td>Komplain dan pertanyaan terkait penagihan dan pembayaran dapat disampaikan ke alamat</td>
			</tr>
			<tr>
				<td></td>
				<td>email :<span style="font-style: italic;"> arie.ristiawan@bhumancaturlestari.com</span></td>
			</tr>
		</table>
		<br>
		<br>
		<br>
		<br>';

    
$html .= '
<table cellpadding="4" style="font-size:10px"> 
</table> 
<div style="clear:both"></div>  
';
 
return $html;
}

?>
