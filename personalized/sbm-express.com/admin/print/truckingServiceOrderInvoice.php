<?php
$generateReportContent = function ($dataset){  

$obj = new TruckingServiceOrderInvoice();  
$truckingServiceOrder = new TruckingServiceOrder();     
$truckingServiceWorkOrder = new TruckingServiceWorkOrder();
$customer = new Customer();
$employee = new Employee();
$cost = new Service(TRUCKING_SERVICE,1);
$termOfPayment = new TermOfPayment();
$customCode = new CustomCode();
    
$rs = $dataset['rs']; 
        
$rsDetail = $obj->getDetailById($rs[0]['pkey']);
$rsDetailPayment = $obj->getDataRowById($rs[0]['pkey']);
$rsCustomer = $customer->getDataRowById($rs[0]['customerkey']);
$rsTOP = $termOfPayment->getDataRowById($rs[0]['termofpaymentkey']);
 
$rsInvoiceType = $customCode->searchData($customCode->tableName.'.pkey',$rs[0]['customcodekey'], true);
 
$proforma = ($rs[0]['statuskey'] == 1) ? '<div style="font-weight:normal; font-size:0.9em">(PROFORMA)</div>' : '';$html = $obj->printSetting['defaultStyle'];
$html .= ' 
<table cellpadding="2" > 
<tr><td><div class="title">'.strtoupper($rsInvoiceType[0]['name']).$proforma.'</div></td></tr>
<tr><td><div class="subtitle">'.$rs[0]['code'].'</div></td></tr>
</table> 

<div style="clear:both"></div>
 
<table cellpadding="2"> 
<tr><td class="header-row-header"  style="width:80px">Tgl. Invoice</td><td style="width:10px; text-align:center">:</td><td style="width:170px">'.$obj->formatDBDate($rs[0]['trdate'],'d / m / Y').'</td></tr>
<tr><td colspan="3" class="header-row-header"></td></tr> 
<tr><td colspan="3" class="header-row-header">Kepada Yth.</td></tr> 
<tr><td style="width:300px" colspan="3">'. $rsCustomer[0]['name'] .'<br>'.$rsCustomer[0]['address'].'</td></tr>   
</table> 
 

<div style="clear:both"></div>
<table cellpadding="4" class="table-transaction">
<tr class="col-header"><td style="text-align:left;width:30px">No</td><td style="text-align:left;width:350px">Deskripsi</td><td style="text-align:right;width:290px">Jumlah</td></tr>  
';
        
    
$color = '#666';
    
for($i=0;$i<count($rsDetail);$i++){ 
    
    $itemname = '';
    $containerDetail = '';
    $serviceJO = '';
    $route = '';
    
    $rsWO = array();
    $rsSalesDetailCost = array();
    $rsCost = array();
    
    $description = $rsDetail[$i]['description'];
    
    if (!empty($rsDetail[$i]['salesorderkey'])){ 

        $rsSOHeader = $truckingServiceOrder->getDataRowById($rsDetail[$i]['salesorderkey']);
        $rsInvoiceItemDetail = $obj->getItemDetail($rsDetail[$i]['pkey']);

        $rsSalesDetailCost = $truckingServiceOrder->getSellingCostDetail($rsSOHeader[0]['pkey']); 
        $rsWO = $truckingServiceWorkOrder->searchData($truckingServiceWorkOrder->tableName.'.refkey',$rsSOHeader[0]['pkey'],true,' and '.$truckingServiceWorkOrder->tableName.'.statuskey in (2,3) ');
   
        $itemname = $rsSOHeader[0]['code'];
           
        $route = $rsSOHeader[0]['routefrom'].' - '.$rsSOHeader[0]['routeto']; 

        // CONTAINER DETAIL 
        if (!empty($rsWO)){ 
            $containerDetail = '<table cellpadding ="2" style="font-size:0.9em; font-style:italic; color:'.$color.';">';
                $containerDetail .= '<tr><td style="width:80px; text-align:left; font-weight:bold">No. SPK</td><td style="width:220px; font-weight:bold">No. Container</td></tr>';
                for($k=0;$k<count($rsWO);$k++){
                    $arrContainer = array();

                    if (!empty($rsWO[$k]['containernumber']))
                        array_push($arrContainer,$rsWO[$k]['containernumber']);

                    if (!empty($rsWO[$k]['container2number']))
                        array_push($arrContainer,$rsWO[$k]['container2number']);
 
                    //$productDesc = str_replace(chr(13),'<br>',$rsWO[$k]['productdesc']);
                    
                    if (!empty($rsWO[$k]['seal2number']))
                    array_push($arrSeal,$rsWO[$k]['seal2number']);

                   $containerDetail .= '<tr><td>'.$rsWO[$k]['code'].'</td><td>'.implode(',',$arrContainer).'</td></tr>';
                }
            $containerDetail .= '</table>';
        }

        // LAYANAN
            $serviceJO = '<table cellpadding="2" style="font-size:0.9em; font-style:italic; color:'.$color.'">'; 
            $serviceJO .= '<tr><td style="width:170px; text-align:left; font-weight:bold">Layanan</td><td style="width:30px;"></td><td style="width:100px; text-align:right; font-weight:bold;">Harga</td></tr>';
          
            if (!empty($rsInvoiceItemDetail)){
                for($j=0;$j<count($rsInvoiceItemDetail);$j++){
                    $serviceJO .=   '<tr><td style="font-style:italic;">'.$obj->formatNumber($rsInvoiceItemDetail[$j]['qtyinbaseunit']).'x '.$rsInvoiceItemDetail[$j]['itemname'].'</td><td>=</td><td style="text-align:right;">'.$obj->formatNumber($rsInvoiceItemDetail[$j]['total']).'</td></tr>'; 
                }  
            }
        
            $serviceJO .= '</table>';
       
    } 
    
    if (!empty($rsDetail[$i]['itemkey'])){ 
        $rsCost = $cost->getDataRowById($rsDetail[$i]['itemkey']);
        $itemname = $rsCost[0]['name'];
    }

    $arrTemp = array();
    if (!empty($itemname))
    array_push($arrTemp, $itemname);
    
    if (!empty($route))
    array_push($arrTemp, $route);
    
    if (!empty($description))
    array_push($arrTemp,'<br>'.nl2br($description)); 
    $itemname = implode(', ', $arrTemp);
    
    $detailJO = '<br><table><tr><td  style="width: 330px;">'.$containerDetail.'</td><td style="width: 300px;">'.$serviceJO.'</td></tr></table>'; 
      
    $html .= '<tr><td style="text-align:right; ">'.($i+1).'.</td><td><span style=" font-weight:bold">'.$itemname.'</span>'.$detailJO.'</td><td style ="text-align:right"><span style=" font-weight:bold">'.$obj->formatNumber($rsDetail[$i]['amount']).'</span></td></tr>';
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
    
/*if ($rs[0]['tax23value'] != 0)  { 
    array_push($arrSubtotal, '<tr><td></td><td></td></tr>'); 
    array_push($arrSubtotal, '<tr><td style="text-align:right; font-weight:bold;">'.ucwords($obj->lang['tax23']).'</td><td style="text-align:right; font-weight:bold;">'.$obj->formatNumber($rs[0]['tax23value']).'</td></tr>'); 
    array_push($arrSubtotal, '<tr><td style="text-align:right; font-weight:bold;">'.ucwords($obj->lang['balance']).'</td><td style="text-align:right; font-weight:bold;">'.$obj->formatNumber(abs($rs[0]['grandtotal']-$rs[0]['tax23value'])).'</td></tr>'); 
    
}*/
    
$subtotalLabel = (!empty($arrSubtotal)) ? ucwords($obj->lang['subtotal']) : ucwords($obj->lang['total']) ;
$sayNumber = $obj->sayNumber($rs[0]['outstanding']);
    
$topSaid = ($rsTOP[0]['duedays'] > 0 ) ? $rsTOP[0]['duedays'] . ' ' . $obj->lang['day'] : $obj->lang['cash'];  

    
$html .= '    
</table>  
<div style="clear:both"></div> 
<table cellpadding="4" > 
<tr><td rowspan="'.(count($arrSubtotal) + 1).'" style="width:460px;"><strong>Terbilang</strong> :<br>'.ucwords($sayNumber).' Rupiah.<br><br><strong>'.$obj->lang['termofpayment'].' :</strong> '.$topSaid.'</td> <td style="text-align:right; font-weight:bold;  width:100px; ">'.$subtotalLabel.'</td><td style="text-align:right; font-weight:bold;  width:110px;"  >'.$obj->formatNumber($rs[0]['subtotal']).'</td></tr>
';

$html .= implode('',$arrSubtotal); 
     
$box = '<table style="width:20px"><tr><td style="border:1px solid #333"></td></tr></table>';
    
$html .= '<table cellpadding="4" >';
$html .= '<tr><td style="width:30px">'.$box.'</td><td style="width:640px">BCA 4191718199 atas nama PT. Samudera Bahtera Maritim Transpor</td></tr>';
$html .= '<tr><td style="width:30px">'.$box.'</td><td style="width:640px">BCA 4194777789 atas nama Williem</td></tr>';
$html .= '</table>';
$html .= '<div style="clear:both"></div>';

    
$rsEmployee = $employee->getDataRowById(base64_decode($_SESSION[$employee->loginAdminSession]['id']));
$arrSignLabel = array(); 
array_push($arrSignLabel, array('Dibuat',$rsEmployee[0]['name'])); 
array_push($arrSignLabel, array('Diterima') ); 

 $html .=' 
<table cellpadding="4" class="sign">
<tr>'; 
for ($i=0;$i<count($arrSignLabel);$i++){
    $html .='<td  class="sign-col" style="height:40px;"><strong>'.$arrSignLabel[$i][0].'</strong></td>';
    if ($i <> count($arrSignLabel) - 1)
        $html .= '<td class="sign-col-space"></td>';
}
$html .='</tr> 
<tr>'; 
for ($i=0;$i<count($arrSignLabel);$i++){
    $arrSignLabel[$i][1] = (isset($arrSignLabel[$i][1])) ? $arrSignLabel[$i][1] : '';
    $html .='<td  class="sign-name">'.$arrSignLabel[$i][1].'</td>';
    if ($i <> count($arrSignLabel) - 1)
        $html .= '<td class="sign-col-space"></td>';
}
$html .='</tr> 
</table>' ;


return $html;
}

?>
