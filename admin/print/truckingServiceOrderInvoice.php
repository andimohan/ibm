<?php  
 
includeClass('TruckingServiceOrderInvoice.class.php');
$truckingServiceOrderInvoice = createObjAndAddToCol( new TruckingServiceOrderInvoice()); 

$obj = $truckingServiceOrderInvoice;

$OPT_FUNCTION = function ($dataset){
    $customer = new Customer();
    $rs = $dataset['rs'];
    $rsCustomer = $customer->getDataRowById($rs[0]['customerkey']); 
 
    if (!empty($rsCustomer[0]['printinvoicefile']))
        return array('printFile' => $rsCustomer[0]['printinvoicefile']);
    
    return '';
};
 
  
$generateReportContent = function ($dataset){  

$obj = new TruckingServiceOrderInvoice();  
$truckingServiceOrder = new TruckingServiceOrder();    
$truckingServiceOrderCategory = new TruckingServiceOrderCategory();    
$truckingServiceWorkOrder = new TruckingServiceWorkOrder();
$customer = new Customer();
$consignee = new Consignee();
$cost = new Service(TRUCKING_SERVICE,1);
$customCode = new CustomCode();
$termOfPayment = new TermOfPayment();
    
$rs = $dataset['rs']; 
        
$rsDetail = $obj->getDetailById($rs[0]['pkey']);
$rsDetailPayment = $obj->getDataRowById($rs[0]['pkey']);
$rsCustomer = $customer->getDataRowById($rs[0]['customerkey']);
$rsTOP = $termOfPayment->getDataRowById($rs[0]['termofpaymentkey']);
    
$rsInvoiceType = $customCode->searchData($customCode->tableName.'.pkey',$rs[0]['customcodekey'], true);
$isDownpayment = $rs[0]['isdownpayment'];
 
$invoiceTo = $rsCustomer[0]['name'] .'<br>'.$rsCustomer[0]['address'];
if($rs[0]['invoiceto'] == 1){
  $invoiceTo = $rsCustomer[0]['name'] .'<br>'.$rsCustomer[0]['address'];
}else{ 
    // kalo bill ke consignee
    $totalRs = count($rsDetail);
    for($i=0;$i<$totalRs;$i++){  
        if (!empty($rsDetail[$i]['salesorderkey'])){ 
            $rsSOHeader = $truckingServiceOrder->getDataRowById($rsDetail[$i]['salesorderkey']);  
            $rsConsignee = $consignee->getDataRowById($rsSOHeader[0]['consigneekey']);  
            $invoiceTo = $rsConsignee[0]['name'] .'<br>'.$rsConsignee[0]['address'] ;
            break;
        }
    }
    
}
    
$invoiceTitle = (!empty($rsInvoiceType[0]['title'])) ? $rsInvoiceType[0]['title'] : $rsInvoiceType[0]['name'];
    
$proforma = ($rs[0]['statuskey'] == 1) ? '<div style="font-weight:normal; font-size:0.9em">(PROFORMA)</div>' : '';
$html = $obj->printSetting['defaultStyle'];
$html .= ' 
<table cellpadding="2" > 
<tr><td><div class="title">'.strtoupper($invoiceTitle).$proforma.'</div></td></tr>
<tr><td><div class="subtitle">'.$rs[0]['code'].'</div></td></tr>
</table> 

<div style="clear:both"></div>
 
<table cellpadding="2"> 
<tr><td class="header-row-header" style="width:80px">'.ucwords($obj->lang['date']).'</td><td style="width:10px; text-align:center">:</td><td style="width:580px;">'.$obj->formatDBDate($rs[0]['trdate'],'d / m / Y').'</td></tr>
<tr><td colspan="3"></td></tr> 
<tr><td colspan="3" style="font-weight:bold">Kepada Yth.</td></tr> 
<tr><td colspan="3">'. $invoiceTo .'</td></tr>   
</table> ';
      
$html .='<div style="clear:both"></div>
<table cellpadding="4" class="table-transaction" >
<tr class="col-header"><td style="text-align:left;width:30px">No</td><td style="text-align:left;width:500px; ">'.ucwords($obj->lang['description']).'</td><td style="text-align:right;width:140px;">'.ucwords($obj->lang['amount']).'</td></tr>  
';
        
    
$color = '#333';
     
$totalRs = count($rsDetail);
for($i=0;$i<$totalRs;$i++){ 
    
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
            $containerDetail = '<table cellpadding ="2" style="font-size:0.9em; font-style:italic; color:'.$color.';">';
                $containerDetail .= '<tr><td style="width:60px; text-align:left; font-weight:bold">'.ucwords($obj->lang['WOCode']).'</td><td style="width:100px; font-weight:bold">'.ucwords($obj->lang['containerNumber']).'</td><td style="width:100px;font-weight:bold">'.ucwords($obj->lang['sealNumber']).'</td><td style="width:110px;font-weight:bold">'.ucwords($obj->lang['productDescription']).'</td></tr>';
                for($k=0;$k<count($rsWO);$k++){
                    $arrContainer = array();
                    $arrSeal = array();
                   
                    $productDesc = str_replace(chr(13),'<br>',$rsWO[$k]['productdesc']);

                    if (!empty($rsWO[$k]['containernumber']))
                    array_push($arrContainer,$rsWO[$k]['containernumber']);

                    if (!empty($rsWO[$k]['container2number']))
                    array_push($arrContainer,$rsWO[$k]['container2number']);

                    if (!empty($rsWO[$k]['sealnumber']))
                    array_push($arrSeal,$rsWO[$k]['sealnumber']);

                    if (!empty($rsWO[$k]['seal2number']))
                    array_push($arrSeal,$rsWO[$k]['seal2number']);

                   $containerDetail .= '<tr><td>'.$rsWO[$k]['code'].'</td><td>'.implode(',',$arrContainer).'</td><td>'.implode(',',$arrSeal).'</td><td>'.$productDesc.'</td></tr>';
                }
            $containerDetail .= '</table>';
        }

        // LAYANAN
            $serviceJO = '<table cellpadding="2" style="font-size:0.9em; font-style:italic; color:'.$color.'">'; 
            $serviceJO .= '<tr><td style="width:100px; text-align:left; font-weight:bold">'.ucwords($obj->lang['services']).'</td><td style="width:70px;text-align:right; font-weight:bold;"></td><td style="width:20px;"></td><td style="width:70px; text-align:right; font-weight:bold;">'.ucwords($obj->lang['price']).'</td></tr>';
          
            if (!empty($rsInvoiceItemDetail)){
                for($j=0;$j<count($rsInvoiceItemDetail);$j++){ 
                    $serviceName = (!empty($rsInvoiceItemDetail[$j]['aliasname'])) ? $rsInvoiceItemDetail[$j]['aliasname'] : $rsInvoiceItemDetail[$j]['itemname'];
                    $serviceJO .=   '<tr><td>'.$obj->formatNumber($rsInvoiceItemDetail[$j]['qtyinbaseunit']).'x '.$serviceName.'</td><td>@'.$obj->formatNumber($rsInvoiceItemDetail[$j]['priceinunit']).'</td><td>=</td><td style="text-align:right;">'.$obj->formatNumber($rsInvoiceItemDetail[$j]['total']).'</td></tr>'; 
                }  
            }
        
            $serviceJO .= '</table>';
       
    } 
    
    if (!empty($rsDetail[$i]['itemkey'])){ 
        $rsCost = $cost->getDataRowById($rsDetail[$i]['itemkey']);
        $itemname = $rsCost[0]['name'];
    }
    
    $arrCategory = array();  
    if(!empty($rsSOCategory))
        array_push($arrCategory, $rsSOCategory[0]['name']);
    
    if(!empty($rsConsignee))
        array_push($arrCategory, $rsConsignee[0]['name']);
    
    
    
    $category = implode(', ', $arrCategory);
    $category = (!empty($category)) ? $category . '.' : '';
    
    $hasDetaiLJO = (!empty($containerDetail) || !empty($serviceJO) ) ? true : false;
    
    if($hasDetaiLJO && !$isDownpayment)
        $detailJO = '<table><tr><td  style="width: 370px;">'.$containerDetail.'</td><td style="width: 260px;">'.$serviceJO.'</td></tr></table>';  
    
    $html .= '<tr><td style="text-align:right; font-weight:bold ">'.($i+1).'.</td><td style="font-weight:bold;">'.$itemname.'. ' .$category.'</td><td style ="text-align:right"><span style=" font-weight:bold">'.$obj->formatNumber($rsDetail[$i]['amount']).'</span></td></tr>';
    $html .= (!empty($description)) ? '<tr><td></td><td colspan="2" style="font-size:0.9em">'.$description.'</td></tr>' : '';
    
    if($hasDetaiLJO && !$isDownpayment)
        $html .= '<tr><td></td><td colspan="2">'.$detailJO.'</td></tr>';

} 
$arrSubtotal = array(); 
     
if ($rs[0]['finaldiscount'] != 0){
    if ($rs[0]['finaldiscounttype'] == 2)
        $rs[0]['finaldiscount'] = $rs[0]['finaldiscount']/100 * $rs[0]['subtotal'];
    
    //$finaldiscount = ($rs[0]['finaldiscount'] != 0) ?  $obj->formatNumber($rs[0]['finaldiscount'] * -1) : 0;  
    $rs[0]['finaldiscount'] *= -1;
    array_push($arrSubtotal, '<tr><td style="text-align:right; font-weight:bold;">'.ucwords($obj->lang['discount']).'</td><td style="text-align:right; font-weight:bold;"  >'.$obj->formatNumber($rs[0]['finaldiscount']).'</td></tr>');
}
    

if ($rs[0]['taxvalue'] != 0){
    array_push($arrSubtotal, '<tr><td style="text-align:right; font-weight:bold;">'.ucwords($obj->lang['beforeTax']).'</td><td style="text-align:right; font-weight:bold;"  >'.$obj->formatNumber($rs[0]['beforetaxtotal']).'</td></tr>');
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
    //$html .= '<tr><td colspan="3"></td></tr>';
    //$html .= '<tr><td></td> <td style="text-align:right; font-weight:bold;  ">'.ucwords($obj->lang['tax23']).'</td><td style="text-align:right; font-weight:bold;" >- '.$obj->formatNumber($rs[0]['tax23value']).'</td></tr>';
    //$html .= '<tr><td></td> <td style="text-align:right; font-weight:bold;  ">'.ucwords($obj->lang['balance']).'</td><td style="text-align:right; font-weight:bold;"  >'.$obj->formatNumber(abs($rs[0]['grandtotal']-$rs[0]['tax23value'])).'</td></tr>';
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
<tr><td rowspan="'.(count($arrSubtotal) + 1).'" style="width:460px;"><strong>Terbilang</strong> :<br>'.ucwords($sayNumber).' Rupiah.<br><br><strong>'.$obj->lang['termofpayment'].' :</strong> '.$topSaid.'</td><td style="text-align:right; font-weight:bold;  width:100px; ">'.$subtotalLabel.'</td><td style="text-align:right; font-weight:bold;  width:110px;"  >'.$obj->formatNumber($rs[0]['subtotal']).'</td></tr>
';

$html .= implode('',$arrSubtotal); 
    

    
$html .= '
<table cellpadding="4" style="font-size:10px"> 
</table> 
<div style="clear:both"></div>  
';
     
$html .= $obj->loadSetting('emailInvoiceFooter');   
$html .= '<div style="clear:both"></div>';
$html .= $obj->generateSignLabel($rs); 
 
return $html;
}

?>