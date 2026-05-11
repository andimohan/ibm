<?php  
$pdf->setCustomSettings(
    array( 
            'pdfMarginHeader' => '2',
            'showPrintHeader' => false,
            'footer' => '',
         ) 
);  

$invoiceContent = function ($dataset){ 
 
$obj = new TruckingServiceOrderInvoice();  
$truckingServiceOrder = new TruckingServiceOrder();    
$truckingServiceOrderCategory = new TruckingServiceOrderCategory();    
$truckingServiceWorkOrder = new TruckingServiceWorkOrder();
$customer = new Customer();
$item = new Item();
$setting = new Setting();
$cost = new Service(TRUCKING_SERVICE,1);
$customCode = new CustomCode();
$termOfPayment = new TermOfPayment();
$employee = new Employee();
$paymentMethod = new PaymentMethod(); 

    
$rs = $dataset['rs']; 
        
$rsDetail = $obj->getDetailById($rs[0]['pkey']);
$rsDetailPayment = $obj->getDataRowById($rs[0]['pkey']);
$rsCustomer = $customer->getDataRowById($rs[0]['customerkey']);
$rsTOP =   $termOfPayment->getDataRowById($rs[0]['termofpaymentkey']); 
$duedate = date('d-m-Y', strtotime('+'.$rsTOP[0]['duedays'].' days', strtotime($rs[0]['trdate'])));

    if(!empty($rsDetail[0]['salesorderkey'])){
        $rsSOHeader = $truckingServiceOrder->getDataRowById($rsDetail[0]['salesorderkey']);  
    } 
    
$rsPaymentMethod = $paymentMethod->getDataRowById($rs[0]['companybankkey']);
$arrBank = array();

//if(!empty($rsPaymentMethod)){
    if (!empty($rsPaymentMethod[0]['bankname'])) array_push($arrBank,'Pembayaran melalui transfer ke: <br><br>Rek. '.$rsPaymentMethod[0]['bankname']); 
    if (!empty($rsPaymentMethod[0]['bankaccountnumber'])) array_push($arrBank,'No Rek. '.$rsPaymentMethod[0]['bankaccountnumber']); 
    if (!empty($rsPaymentMethod[0]['bankaccountname'])) array_push($arrBank,'Atas Nama '.$rsPaymentMethod[0]['bankaccountname']); 
//}
$arrCustomer = array();
    
if (!empty($rsCustomer[0]['name'])) array_push($arrCustomer, $rsCustomer[0]['name']); 
if (!empty($rsCustomer[0]['address'])) array_push($arrCustomer, str_replace(chr(13),'<br>',$rsCustomer[0]['address'])); 
    
$companyPhone = $setting->getDetailByCode('companyPhone');
$companyAddress = $setting->loadSetting('companyAddress');
$arrCompanyPhone = array();  
for($i=0;$i<count($companyPhone);$i++) 
    array_push($arrCompanyPhone, $companyPhone[$i]['value']);

$companyContact = '';
if(!empty($arrCompanyPhone))
    $companyContact = implode (', ', $arrCompanyPhone);
    
$companyName = strtoupper($setting->loadSetting('companyName'));
$trnotes = (!empty($rs[0]['trdesc'])) ? '<div style="clear:both"></div><strong>Catatan :</strong> <br>' . str_replace(chr(13),'<br>',$rs[0]['trdesc']) : '';

$topSaid = ($rsTOP[0]['duedays'] > 0 ) ? $rsTOP[0]['duedays'] : $obj->lang['cash'];
$profileImg = $obj->loadSetting('companyLogo'); 
$img =  HTTP_HOST.'phpthumb/phpThumb.php?src='.$obj->phpThumbURLSrc.'setting/companyLogo/'.$profileImg.'&w=180&h=90&hash='.getPHPThumbHash($profileImg);

$html = $obj->printSetting['defaultStyle'];
$html .= '<div style="clear:both"></div>';
$html .= '<table>
    <tr>
        <td>
        <table cellpadding="3" style=""> 
            <tr>
                <td style="vertical-align:middle; width:140px" ><img src="'.$img.'"></td>
                <td style="width: 300px;"><b>'.$companyName.'</b><br>'.$companyAddress.'<br>'.$companyContact.'</td>
            </tr>
        </table>
        </td>
        <td ></td>
        <td>
            <table cellpadding="2" style=""> 
            <tr><td style="width:100px"></td><td style="width:10px"></td><td  style="text-align:right;width:100px"><strong>INVOICE</strong></td></tr>   
            <tr><td style="width:100px"><strong>No</strong></td><td style="width:10px">:</td><td style="text-align:right;width:100px">'.$rs[0]['code'].'</td></tr>   
            <tr><td style="width:100px"><strong>'.ucwords($obj->lang['date']).'</strong></td><td style="width:10px">:</td><td style="text-align:right;width:100px" >'.$obj->formatDBDate($rs[0]['trdate'],'d / m / Y').'</td></tr>   
            <tr><td style="width:100px"><strong>Payment Term</strong></td><td>:</td><td style="text-align:right;width:100px" >TOP '.$topSaid.'</td></tr> 
            </table>  
        </td>
    </tr> 
</table>
<div style="clear:both;"></div>';
    
    
$html .= ' 
<table>
<tr>
<td style="width:340px;">
<table cellpadding="2"> 
<tr><td><b>Kepada Yth :</b><br>'.implode('<br>',$arrCustomer).'</td></tr>   
</table> 
</td>
<td style="width:90px;"></td>
<td style="width:330px;">
</td>
</tr>
</table>
<div style="clear:both"></div>';
    
    
$cellArray = array();
array_push($cellArray, array('label' => 'Partai', 'width' => '50', 'align' => 'right'));
array_push($cellArray, array('label' => 'Deskripsi'));
array_push($cellArray, array('label' => 'Jenis Kendaraan', 'width' => '160'));
array_push($cellArray, array('label' => 'Harga @ (Rp)', 'width' => '80','align' => 'right'));
array_push($cellArray, array('label' => 'Total (Rp)', 'width' => '70','align' => 'right'));
  
$html .= '<table  cellpadding="4" class="table-transaction">';
$html .= $obj->generatePrintTableRow( array('class' => 'col-header', 'cell' =>  $cellArray));
    for($i=0;$i<count($rsDetail);$i++){ 

        $rsWO = array();
        $rsSOHeader = array();
        $serviceJO = '';

        if (!empty($rsDetail[$i]['salesorderkey'])){ 
            $rsInvoiceItemDetail = $obj->getItemDetail($rsDetail[$i]['pkey']);   
            $rsSOHeader = $truckingServiceOrder->getDataRowById($rsDetail[$i]['salesorderkey']);   
            $route = (!empty($rsSOHeader[0]['routefrom']) || !empty($rsSOHeader[0]['routeto'])) ? $rsSOHeader[0]['routefrom'] . ' - ' . $rsSOHeader[0]['routeto'] : '';
            $rsWO = $truckingServiceWorkOrder->searchData($truckingServiceWorkOrder->tableName.'.refkey',$rsSOHeader[0]['pkey'],true,' and '.$truckingServiceWorkOrder->tableName.'.statuskey in (2,3) ');
            
            if (!empty($rsInvoiceItemDetail)){
                $arrServiceJO = array();
                $serviceJO = '';
                for($j=0;$j<count($rsInvoiceItemDetail);$j++){ 
                    $rsItem = $item->getDataRowById($rsInvoiceItemDetail[$j]['itemkey']);                                         
                    $arrCar = array();
                    if(!empty($rsItem) && $rsItem[0]['itemtype'] == 2){
                        $rsSPK = $truckingServiceWorkOrder->searchData('','',true,' and '.$truckingServiceWorkOrder->tableName.'.refdetailkey='.$obj->oDbCon->paramString($rsInvoiceItemDetail[$j]['refsodetailkey']).' 
                                                                                    and '.$truckingServiceWorkOrder->tableName.'.refkey='.$obj->oDbCon->paramString($rsSOHeader[0]['pkey']).' 
                                                                                    and '.$truckingServiceWorkOrder->tableName.'.statuskey in (2,3)');
                        foreach($rsSPK as $spkRow){ 
                            $registrationNumber = (!$spkRow['isoutsource']) ? $spkRow['carcategoryname'].' '.$spkRow['policenumber'] : $spkRow['outsourcecarregistrationnumber']; 
                            if(!in_array($registrationNumber,$arrCar))
                                array_push($arrCar,$registrationNumber);
                        } 
                    }
                    
                    $servicename = (!empty($rsItem[0]['aliasname'])) ? $rsItem[0]['aliasname'] : $rsItem[0]['name'];
                    $itemname = (!empty($rsInvoiceItemDetail[$j]['aliasname'])) ? $rsInvoiceItemDetail[$j]['aliasname'] : $servicename.' '.$route;
                    $serviceJO .= '<tr><td style="text-align:right">'.$obj->formatNumber($rsInvoiceItemDetail[$j]['qtyinbaseunit']).'</td><td>'.$itemname.'</td><td>'.implode('<br>',$arrCar).'</td><td style="text-align:right">'.$obj->formatNumber($rsInvoiceItemDetail[$j]['priceinunit']).'</td><td style="text-align:right">'.$obj->formatNumber($rsInvoiceItemDetail[$j]['total']).'</td></tr>';
 
                }  
            }
             
        } else { 

        }
 
        $html .= $serviceJO;
} 
$html .= '</table>';
    
$html .= '<div style="clear:both"></div>';
    
$arrSubtotal = array();
$ctr = 1;   
     
if ($rs[0]['finaldiscount'] != 0){
    array_push($arrSubtotal, '<tr><td style="text-align:right; font-weight:bold;">'.ucwords($obj->lang['discount']).'</td><td style="text-align:right; font-weight:bold;"  >'.$obj->formatNumber($rs[0]['finaldiscount']).'</td></tr>');
    $ctr += 1;
}

if ($rs[0]['taxvalue'] != 0){
    array_push($arrSubtotal, '<tr><td style="text-align:right; font-weight:bold;">'.ucwords($obj->lang['beforeTax']).'</td><td style="text-align:right; font-weight:bold;"  >'.$obj->formatNumber($rs[0]['beforetaxtotal']).'</td></tr>');
    array_push($arrSubtotal, '<tr><td style="text-align:right; font-weight:bold;">'.ucwords($obj->lang['PPN']).'</td><td style="text-align:right; font-weight:bold;"  >'.$obj->formatNumber($rs[0]['taxvalue']).'</td></tr>');
    $ctr += 2;
}
    
if ($rs[0]['totaldownpayment'] > 0){
    array_push($arrSubtotal, '<tr><td style="text-align:right; font-weight:bold;">'.ucwords($obj->lang['downpayment']).'</td><td style="text-align:right; font-weight:bold;">'.$obj->formatNumber($rs[0]['totaldownpayment']).'</td></tr>'); 
    $ctr += 1;
} 
    
$html .= implode('',$arrSubtotal);

$footer = $obj->loadSetting('invoiceFooter');
$subtotalLabel = (!empty($arrSubtotal)) ? ucwords($obj->lang['subtotal']) : ucwords($obj->lang['total']) ; 

$html .= '    
</table>  

<table cellpadding="4"> 
<tr><td rowspan="'.$ctr.'" style="width:420px">'.$footer.'</td> <td style="text-align:right; font-weight:bold;  width:150px; ">'.$subtotalLabel.'</td><td style="text-align:right; font-weight:bold;  width:110px;"  >Rp '.$obj->formatNumber($rs[0]['grandtotal']).'</td></tr>
';

$html .= '
</table>
<div style="clear:both"></div>';
    
$arrSignLabel = array(); 
array_push($arrSignLabel, array('Pelanggan','Penerima'));
array_push($arrSignLabel, array('Hormat Kami','Magdalena Maya'));

$html .=' 
    <table cellpadding="4" class="sign" style="">
    <tr>'; 

$html .= '<td style="width:350px;">'.implode('<br>',$arrBank).'</td>';
        for ($i=0;$i<count($arrSignLabel);$i++){
        $html .='<td  class="sign-col" style="height:140px;text-align:center;"><strong>'.$arrSignLabel[$i][0].'</strong></td>';
        if ($i <> count($arrSignLabel) - 1)
            $html .= '<td class="sign-col-space"></td>';
    }
$html .='</tr>  

        <tr>'; 
$html .= '<td style="width:350px;"></td>';
        for ($i=0;$i<count($arrSignLabel);$i++){
            $arrSignLabel[$i][1] = (isset($arrSignLabel[$i][1])) ? $arrSignLabel[$i][1] : '';
            $html .='<td  class="" style="text-align:center;">'.$arrSignLabel[$i][1].'</td>';
            if ($i <> count($arrSignLabel) - 1)
                $html .= '<td class="sign-col-space"></td>';
        }
        $html .='</tr> 
</table>' ;
    

    
$html .= '<div style="clear:both; height:3em"></div><table>';
$html .= '<tr><td>'.$trnotes.'</td></tr>';
$html .= '</table>';
    
return $html;
}; 
   
$generateReportContent = array();
array_push($generateReportContent , $invoiceContent);

?>
