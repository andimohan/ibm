<?php  
$pdf->setCustomSettings(
    array(
         'showPrintHeader' => false,  
         'footer' => '<table><tr><td style="text-align:center">Powered by www.wintera.co.id</td></tr></table>',   
         ) 
); 

$generateReportContent = function ($dataset){  

$obj = new TruckingServiceOrderInvoice();  
$truckingServiceOrder = new TruckingServiceOrder();    
$truckingServiceOrderCategory = new TruckingServiceOrderCategory();    
$truckingServiceWorkOrder = new TruckingServiceWorkOrder();
$customer = new Customer();
//$consignee = new Consignee();
$cost = new Service(TRUCKING_SERVICE,1);
$customCode = new CustomCode();
$termOfPayment = new TermOfPayment();
$employee = new Employee();
    
$rs = $dataset['rs']; 
        
$rsDetail = $obj->getDetailById($rs[0]['pkey']);
$rsDetailPayment = $obj->getDataRowById($rs[0]['pkey']);
$rsCustomer = $customer->getDataRowById($rs[0]['customerkey']);
$rsTOP =   $termOfPayment->getDataRowById($rs[0]['termofpaymentkey']); 
$rsInvoiceType = $customCode->searchData($customCode->tableName.'.pkey',$rs[0]['customcodekey'], true);
//$duedate = date('Y-m-d', strtotime('+'.$rsTOP[0]['duedays'].' days', strtotime($rs[0]['trdate'])));
 
$proforma = ($rs[0]['statuskey'] == 1) ? '<div style="font-weight:normal; font-size:0.9em">(PROFORMA)</div>' : '';
$html = $obj->printSetting['defaultStyle'];
$html .= ' 
<table cellpadding="2" > 
<tr><td><div class="title">'.strtoupper($rsInvoiceType[0]['title']).$proforma.'</div></td></tr>
<tr><td><div class="subtitle">'.$rs[0]['code'].'</div></td></tr>
</table> 

<div style="clear:both"></div>
<table>
<tr>
<td style="width:370px;">
<table cellpadding="2"> 
<tr><td style="font-weight:bold">Kepada Yth.</td></tr> 
<tr><td>'. $rsCustomer[0]['name'] .'<br>'.$rsCustomer[0]['address'].'</td></tr>   
</table> 
</td>
<td style="width:90px;"></td>
<td style="width:370px;">
<table cellpadding="2"> 
<tr><td class="header-row-header" style="width:130px">Tgl. Invoice</td><td style="width:10px; text-align:center">:</td><td style="width:500px;">'.$obj->formatDBDate($rs[0]['trdate'],'d / m / Y').'</td></tr>
<tr><td class="header-row-header" style="width:130px">Jatuh Tempo Invoice</td><td>:</td><td>'.$rsTOP[0]['name'].'</td></tr>
</table> 
</td>
</tr>
</table>';
      
$html .='<div style="clear:both"></div>
<table cellpadding="4" class="table-transaction" >
<tr class="col-header"><td style="text-align:left;width:30px">No</td><td style="text-align:left;width:280px; ">'.ucwords($obj->lang['description']).'</td><td style="width:100px; text-align:center">'.ucwords($obj->lang['date']).'</td><td style="width:150px;">'.ucwords($obj->lang['route']).'</td><td style="text-align:right;width:110px;">'.ucwords($obj->lang['amount']).'</td></tr>  
';
        
    
$color = '#333';
     
for($i=0;$i<count($rsDetail);$i++){ 
    
    $itemname = '';
    $containerDetail = '';
    $serviceJO = '';
    
    $rsWO = array();
    $rsCost = array();
    $rsSOCategory = array();
    //$rsConsignee = array();
    
    $description = $rsDetail[$i]['description']; 
   
    if (!empty($rsDetail[$i]['salesorderkey'])){ 

        $rsSOHeader = $truckingServiceOrder->getDataRowById($rsDetail[$i]['salesorderkey']);  
        $rsSOCategory = $truckingServiceOrderCategory->getDataRowById($rsSOHeader[0]['categorykey']);  
        //$rsConsignee = $consignee->getDataRowById($rsSOHeader[0]['consigneekey']);  
        $rsInvoiceItemDetail = $obj->getItemDetail($rsDetail[$i]['pkey']);   
        
//        $showSPK = false;
//        if(isset($_GET) && !empty($_GET['spk']) && $_GET['spk'] == 1){ 
//            $rsWO = $truckingServiceWorkOrder->searchData($truckingServiceWorkOrder->tableName.'.refkey',$rsSOHeader[0]['pkey'],true,' and '.$truckingServiceWorkOrder->tableName.'.statuskey in (2,3) ');
//            $showSPK = true;
//        }
        
        $itemname = $rsSOHeader[0]['code'];
   

        // CONTAINER DETAIL 
        if (!empty($rsWO)){ 
            $containerDetail = '<table cellpadding ="2" style="font-size:0.9em; font-style:italic; color:'.$color.';">';
                $containerDetail .= '<tr><td style="width:80px; text-align:left; font-weight:bold">'.ucwords($obj->lang['WOCode']).'</td><td style="width:120px; font-weight:bold">'.ucwords($obj->lang['containerNumber']).'</td><td style="font-weight:bold">'.ucwords($obj->lang['productDescription']).'</td></tr>';
                for($k=0;$k<count($rsWO);$k++){
                    $arrContainer = array();
                    $arrSeal = array();

                    if (!empty($rsWO[$k]['containernumber']))
                    array_push($arrContainer,$rsWO[$k]['containernumber']);

                    if (!empty($rsWO[$k]['container2number']))
                    array_push($arrContainer,$rsWO[$k]['container2number']);

                    if (!empty($rsWO[$k]['sealnumber']))
                    array_push($arrSeal,$rsWO[$k]['sealnumber']);

                    if (!empty($rsWO[$k]['seal2number']))
                    array_push($arrSeal,$rsWO[$k]['seal2number']);

                   $containerDetail .= '<tr><td>'.$rsWO[$k]['code'].'</td><td>'.implode(',',$arrContainer).'</td><td></td></tr>';
                }
            $containerDetail .= '</table>';
        }

        // LAYANAN
        $serviceJO = '<table cellpadding="2" style="font-size:0.9em; font-style:italic; color:'.$color.'">'; 
        $serviceJO .= '<tr><td style="width:100px; text-align:left; font-weight:bold">'.ucwords($obj->lang['services']).'</td><td style="width:70px;text-align:right; font-weight:bold;"></td><td style="width:10px;"></td><td style="width:80px; text-align:right; font-weight:bold;">'.ucwords($obj->lang['price']).'</td></tr>';

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
    
/*    if(!empty($rsConsignee))
        array_push($arrCategory, $rsConsignee[0]['name']);*/
    
    $category = implode(', ', $arrCategory);
    $category = (!empty($category)) ? $category . '.' : '';
    
    $spkCol = ''; // ($showSPK) ? '<td  style="width: 360px;">'.$containerDetail.'</td>' : '';
    $detailJO = '<table><tr>'.$spkCol.'<td style="width: 270px;">'.$serviceJO.'</td></tr></table>';   
    $html .= '<tr><td style="text-align:right; font-weight:bold ">'.($i+1).'.</td><td style="font-weight:bold;">'.$itemname.'. ' .$category.'</td><td style="text-align:center;font-weight:bold;">'.$obj->formatDBDate($rsSOHeader[0]['trdate'],'d / m / Y').'</td><td style="font-weight:bold;">'.$rsSOHeader[0]['routefrom'].' - '.$rsSOHeader[0]['routeto'].'</td><td style ="text-align:right"><span style=" font-weight:bold">'.$obj->formatNumber($rsDetail[$i]['amount']).'</span></td></tr>';
    $html .= (!empty($description)) ? '<tr><td></td><td colspan="2" style="font-size:0.9em">'.$description.'</td><td></td></tr>' : '';
    $html .= '<tr><td></td><td colspan="2">'.$detailJO.'</td></tr>';

} 
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

     
$subtotalLabel = (!empty($arrSubtotal)) ? ucwords($obj->lang['subtotal']) : ucwords($obj->lang['total']) ; 
    
$sayNumber = $obj->sayNumber($rs[0]['outstanding']);
    
$html .= '    
</table>  
<div style="clear:both"></div> 
<table cellpadding="4"> 
<tr><td rowspan="'.$ctr.'" style="width:410px"><strong>Terbilang</strong> :<br>'.ucwords($sayNumber).' Rupiah.</td> <td style="text-align:right; font-weight:bold;  width:150px; ">'.$subtotalLabel.'</td><td style="text-align:right; font-weight:bold;  width:110px;"  >'.$obj->formatNumber($rs[0]['subtotal']).'</td></tr>
';

$html .= implode('',$arrSubtotal);
    
if (!empty($arrSubtotal))  
$html .= '<tr><td></td> <td style="text-align:right; font-weight:bold;  ">Total</td><td style="text-align:right; font-weight:bold;"  >'.$obj->formatNumber($rs[0]['outstanding']).'</td></tr>';

    
if ($rs[0]['tax23value'] != 0)  { 
//$html .= '<tr><td colspan="3"></td></tr>';
$html .= '<tr><td></td> <td style="text-align:right; font-weight:bold;  ">PPH 23</td><td style="text-align:right; font-weight:bold;"  >('.$obj->formatNumber($rs[0]['tax23value']).')</td></tr>';
$html .= '<tr><td></td> <td style="text-align:right; font-weight:bold;  ">Yang Harus Dibayarkan</td><td style="text-align:right; font-weight:bold;"  >'.$obj->formatNumber(abs($rs[0]['grandtotal']-$rs[0]['tax23value'])).'</td></tr>';
}
 
      
$confirmedName = '';
if (!empty($rs[0]['confirmedby'])){ 
    $rsEmployee = $employee->getDataRowById($rs[0]['confirmedby']);
    $confirmedName = $rsEmployee[0]['name'];
}
    
$html .= '<div style="clear:both"></div>';
$html .= '
<table cellpadding="4"> 
<tr>
<td style="width:400px; font-size:0.9em">

Pembayaran Mohon di Transfer Ke :<br><br>
<table> 
<tr> 
<td style="width:80px;">Bank</td>
<td style="width:10px;">:</td>
<td style="width:100px;">BCA</td> 
</tr>
<tr> 
<td>No. Rek</td>
<td>:</td>
<td>690 092 0715</td> 
</tr>
<tr> 
<td>Atas Nama</td>
<td>:</td>
<td>Martin Halim Kusuma</td> 
</tr>
</table>


</td>
<td>
<table style="font-weight:bold"> 
<tr><td style="text-align:center;">Jakarta, '.$obj->formatDBDate($rs[0]['trdate'],'d / m / Y').'</td></tr>
<tr><td style="height:120px"></td></tr> 
<tr><td style="text-align:center;">'.$confirmedName.'</td></tr> 
</table>
</td> 
</tr>
</table>  
'; 
 
return $html;
}

?>
