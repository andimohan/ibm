<?php 
 
$pdf->setCustomSettings(
    array( 
         'showPrintHeader' => false, 
         'footer' => '<table > <tr><td>Halaman {{ GROUP_PAGE_NO }}</td></tr> </table>',    
    ) 
); 

$generateReportContent = function ($dataset){ 
global $pdf;
    
$obj = new TruckingServiceOrderInvoice();  
$truckingServiceOrder = new TruckingServiceOrder();    
$truckingServiceOrderCategory = new TruckingServiceOrderCategory();    
$truckingServiceWorkOrder = new TruckingServiceWorkOrder();
$termOfPayment = new TermOfPayment();
$customer = new Customer();
$consignee = new Consignee();
$cost = new Service(TRUCKING_SERVICE,1);
$customCode = new CustomCode();
$paymentMethod = new PaymentMethod();
    
$rs = $dataset['rs'];  
    
$rsInvoiceType = $customCode->searchData($customCode->tableName.'.pkey',$rs[0]['customcodekey'], true);
    
$rsDetail = $obj->getDetailById($rs[0]['pkey']);
$rsDetailPayment = $obj->getDataRowById($rs[0]['pkey']);
$sayNumber = $obj->sayNumber($rs[0]['grandtotal']);
$rsCustomer = $customer->getDataRowById($rs[0]['customerkey']);
 

$rsPaymentMethod = $paymentMethod->getDataRowById($rs[0]['companybankkey']);
$proforma = ($rs[0]['statuskey'] == 1) ? '<div style="font-weight:normal; font-size:0.9em">(PROFORMA)</div>' : '';
$html = $obj->printSetting['defaultStyle'];
    
$pdf->Header();
    
$html .= ' 
<table cellpadding="2" > 
<tr><td><div class="title">'.strtoupper($rsInvoiceType[0]['name']).$proforma.'</div></td></tr>
<tr><td><div class="subtitle">'.$rs[0]['code'].'</div></td></tr>
</table> 

<div style="clear:both"></div>
 
<table cellpadding="2"> 
<tr><td class="header-row-header"  style="width:80px">Tanggal</td><td style="width:10px; text-align:center">:</td><td style="width:580px;">'.$obj->formatDBDate($rs[0]['trdate'],'d / m / Y').'</td></tr>
<tr><td colspan="3"></td></tr> 
<tr><td colspan="3" style="font-weight:bold">Kepada Yth.</td></tr> 
<tr><td colspan="3"><strong>'. $rsCustomer[0]['name'] .'</strong><br><span style="font-size:0.8em">'.$rsCustomer[0]['address'].'</span></td></tr>   
<tr><td colspan="3">NPWP : '. $rsCustomer[0]['taxid'] .'</td></tr>   
</table> ';
      
$html .='<div style="clear:both"></div>
<table cellpadding="4" class="table-transaction" >
<tr class="col-header"><td style="text-align:left;width:30px">No</td><td style="text-align:left;width:500px; ">Deskripsi</td><td style="text-align:right;width:140px;">Jumlah</td></tr>  
';
        
    
$color = '#000';
     
for($i=0;$i<count($rsDetail);$i++){ 
    
    $itemname = '';
    $containerDetail = '';
    $serviceJO = '';
    
    $rsWO = array();
    $rsCost = array();
    $rsSOCategory = array();
    $rsConsignee = array();
    
    $description = $rsDetail[$i]['description'];
    
   
    if (!empty($rsDetail[$i]['salesorderkey'])){ 

        $rsSOHeader = $truckingServiceOrder->getDataRowById($rsDetail[$i]['salesorderkey']);  
        $rsSOCategory = $truckingServiceOrderCategory->getDataRowById($rsSOHeader[0]['categorykey']);  
        $rsConsignee = $consignee->getDataRowById($rsSOHeader[0]['consigneekey']);  
        $rsInvoiceItemDetail = $obj->getItemDetail($rsDetail[$i]['pkey']);   
        $rsWO = $truckingServiceWorkOrder->searchData($truckingServiceWorkOrder->tableName.'.refkey',$rsSOHeader[0]['pkey'],true,' and '.$truckingServiceWorkOrder->tableName.'.statuskey in (2,3) ');
   
        $itemname = $rsSOHeader[0]['code']; 

        // CONTAINER DETAIL 
        if (!empty($rsWO)){ 
            $arrContainer = array();    
            $containerDetail = '<table cellpadding ="2" style="font-size:0.9em; font-style:italic; color:'.$color.'">'; 
            for($k=0;$k<count($rsWO);$k++){
                    
                    $arrSeal = array();
 
                    $rsWO[$k]['containernumber'] = str_replace(' ','',$rsWO[$k]['containernumber']);
                    if (!empty($rsWO[$k]['containernumber']) && !in_array($rsWO[$k]['containernumber'],$arrContainer ))
                    array_push($arrContainer,$rsWO[$k]['containernumber']);

                    $rsWO[$k]['container2number'] = str_replace(' ','',$rsWO[$k]['container2number']);
                    if (!empty($rsWO[$k]['container2number']) && !in_array($rsWO[$k]['container2number'],$arrContainer ))
                    array_push($arrContainer,$rsWO[$k]['container2number']);
 
                }
            $containerDetail .= '<tr><td style="width:100px; text-align:left; font-weight:bold">No. Container :</td> <td style="width:550px; font-weight:bold;">'.implode(', ',$arrContainer).'</td></tr>';
            $containerDetail .= '</table>';
        }

        // LAYANAN
            $serviceJO = '<table cellpadding="2" style="font-size:0.9em; font-style:italic; color:'.$color.'">'; 
            $serviceJO .= '<tr><td style="width:110px; text-align:left; font-weight:bold">Layanan</td><td style="width:80px; font-weight:bold;"></td><td style="width:30px;"></td><td style="width:100px; text-align:right; font-weight:bold;">Harga</td></tr>';
          
            if (!empty($rsInvoiceItemDetail)){
                for($j=0;$j<count($rsInvoiceItemDetail);$j++){
//					$serviceName = (!empty($rsInvoiceItemDetail[$j]['aliasname'])) ? $rsInvoiceItemDetail[$j]['aliasname']  : $rsInvoiceItemDetail[$j]['itemname'] ;
					$serviceName= $rsInvoiceItemDetail[$j]['itemname'];
                    $serviceJO .=   '<tr><td>'.$obj->formatNumber($rsInvoiceItemDetail[$j]['qtyinbaseunit']).'x '.$serviceName.'</td><td>'.$obj->formatNumber($rsInvoiceItemDetail[$j]['priceinunit']).'</td><td>=</td><td style="text-align:right;">'.$obj->formatNumber($rsInvoiceItemDetail[$j]['total']).'</td></tr>'; 
                }  
            }
        
            $serviceJO .= '</table>';
       
    } 
    
    if (!empty($rsDetail[$i]['itemkey'])){ 
        $rsCost = $cost->getDataRowById($rsDetail[$i]['itemkey']);
        $itemname = $rsCost[0]['name'];
    }

    $arrTemp = array();
     
    $arrDescription = implode(', ', $arrTemp);
    
    $arrCategory = array();
    if (!empty($itemname))
    array_push($arrCategory, $itemname);
    
    if (!empty($rsSOHeader[0]['donumber']))
        array_push($arrCategory, 'S/I : ' .$rsSOHeader[0]['donumber']);  
     
    //informasi rute
    $arrRoute = array();
    if (!empty($rsSOHeader[0]['routefrom'])) array_push($arrRoute, $rsSOHeader[0]['routefrom']);
    if (!empty($rsSOHeader[0]['routeto'])) array_push($arrRoute, $rsSOHeader[0]['routeto']);
    
    if(!empty($arrRoute))
        array_push($arrCategory, '<br>Rute : ' .implode(' - ',$arrRoute ));  
    
/*    if(!empty($rsSOCategory))
        array_push($arrCategory, $rsSOCategory[0]['name']);*/
    
    /*if(!empty($rsConsignee))
        array_push($arrCategory, $rsConsignee[0]['name']);*/
     
    if (!empty($description))
        array_push($arrCategory, $description); 
     
    
    $category = implode(', ', $arrCategory);
    
    $detailJO = '<table><tr><td  style="width: 300px;">'.$containerDetail.'</td><td style="width: 330px;">'.$serviceJO.'</td></tr></table>';   
    $html .= '<tr><td style="text-align:right; font-weight:bold ">'.($i+1).'.</td><td style="font-weight:bold;">'.$category.'</td><td style ="text-align:right"><span style=" font-weight:bold">'.$obj->formatNumber($rsDetail[$i]['amount']).'</span></td></tr>';
    $html .= (!empty($serviceJO)) ?  '<tr><td></td><td colspan="2">'.$serviceJO.'</td></tr>' : '';
    $html .= (!empty($containerDetail)) ? '<tr><td></td><td colspan="2">'.$containerDetail.'</td></tr>' : '';

} 
    
$arrSubtotal = array();
 
if ($rs[0]['finaldiscount'] != 0){
    if ($rs[0]['finaldiscounttype'] == 2)
        $rs[0]['finaldiscount'] = $rs[0]['finaldiscount']/100 * $rs[0]['subtotal'];
    
    $rs[0]['finaldiscount'] *= -1;
    
    array_push($arrSubtotal, '<tr><td style="text-align:right; font-weight:bold;">Diskon</td><td style="text-align:right; font-weight:bold;"  >'.$obj->formatNumber($rs[0]['finaldiscount']).'</td></tr>');
    array_push($arrSubtotal, '<tr><td style="text-align:right; font-weight:bold;">Sebelum Pajak</td><td style="text-align:right; font-weight:bold;"  >'.$obj->formatNumber($rs[0]['beforetaxtotal']).'</td></tr>');
}

if ($rs[0]['taxvalue'] != 0){
    array_push($arrSubtotal, '<tr><td style="text-align:right; font-weight:bold;">PPN</td><td style="text-align:right; font-weight:bold;"  >'.$obj->formatNumber($rs[0]['taxvalue']).'</td></tr>');
}

if ($rs[0]['totaldownpayment'] > 0){
    array_push($arrSubtotal, '<tr><td style="text-align:right; font-weight:bold;">'.ucwords($obj->lang['downpayment']).'</td><td style="text-align:right; font-weight:bold;">'.$obj->formatNumber($rs[0]['totaldownpayment']).'</td></tr>'); 
} 
    
if (!empty($arrSubtotal)) { 
    //$html .= '<tr><td></td> <td style="text-align:right; font-weight:bold;  ">Total</td><td style="text-align:right; font-weight:bold;"  >'.$obj->formatNumber($rs[0]['outstanding']).'</td></tr>';
    array_push($arrSubtotal, '<tr><td style="text-align:right; font-weight:bold;">Total</td><td style="text-align:right; font-weight:bold;">'.$obj->formatNumber($rs[0]['outstanding']).'</td></tr>'); 
}

    
$subtotalLabel = (!empty($arrSubtotal)) ? ucwords($obj->lang['subtotal']) : ucwords($obj->lang['total']) ; 
$rsTOP = $termOfPayment->getDataRowById($rs[0]['termofpaymentkey']);
    
$html .= '    
</table>   
<table cellpadding="4"> 
<tr><td rowspan="'.(count($arrSubtotal) + 1).'" style="width:460px"><strong>Terbilang</strong> :<br>'.ucwords($sayNumber).' Rupiah.<br><br><strong>'.$obj->lang['duedate'].' :</strong> ' . $rsTOP[0]['name'] .'</td> <td style="text-align:right; font-weight:bold;  width:100px; ">'.$subtotalLabel.'</td><td style="text-align:right; font-weight:bold;  width:110px;"  >'.$obj->formatNumber($rs[0]['subtotal']).'</td></tr>
';

$html .= implode('',$arrSubtotal);
    
 
$html .= ' 
<br>
<table cellpadding="4"> 
<tr>
    <td style="width:400px; font-size:0.9em"><table cellpadding="2">
            <tr>
                <td colspan="3" style="">Pembayaran dilakukan melalui </td>
            </tr>
            <tr>
                <td style="width:35%;">Bank</td>
                <td style="width:3%;">:</td>
                <td style="width:200px;">' . $rsPaymentMethod[0]['bankname'] . '</td>
            </tr>
            <tr>
                <td style="width:35%;">Cabang</td>
                <td style="width:3%;">:</td>
                <td style="width:200px">' . $rsPaymentMethod[0]['branch'] . '</td>
            </tr>
            <tr>
                <td style="width:35%;">Nama Rekening</td>
                <td style="width:3%;">:</td>
                <td style="width:200px">' . $rsPaymentMethod[0]['bankaccountname'] . '</td>
            </tr>
            <tr>
                <td style="width:35%;">Nomor Rekening</td>
                <td style="width:3%;">:</td>
                <td style="width:200px">' . $rsPaymentMethod[0]['bankaccountnumber'] . '</td>
            </tr>
        </table>
    </td>
<td>
<table style="font-weight:bold"> 
<tr><td style="text-align:center;">'.strtoupper($obj->loadSetting('companyName')).'</td></tr>
<tr><td style="height:120px"></td></tr> 
<tr><td style="text-align:center;"> Authorized Signature </td></tr> 
</table>
</td> 
</tr>
</table>  
';
       
$html .= '<div style="clear:both"></div>';
       
return $html;
}

?>
