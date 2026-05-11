<?php 
 $pdf->setCustomSettings(
    array(  
         'showPrintFooter' => false,
         ) 
); 
$obj = $salesOrderInvoiceReceipt;
 
$generateReportContent = function ($dataset){ 
 
$obj = new SalesOrderInvoiceReceipt();  
$truckingServiceOrderInvoice = new TruckingServiceOrderInvoice();
$truckingServiceOrder = new TruckingServiceOrder();
$customer = new Customer();
$employee = new Employee();
$top = new TermOfPayment();
  
$rs = $dataset['rs']; 
$rsDetail = $obj->getDetailById($rs[0]['pkey']); 
    
$rsInvoiceTop = $truckingServiceOrderInvoice->getDataRowById($rsDetail[0]['invoicekey']); 
$rsTop = $top->getDataRowById($rsInvoiceTop[0]['termofpaymentkey']);   
    
$rsCustomer = $customer->getDataRowById($rs[0]['customerkey'] );  
$customerName = (!empty($rsCustomer)) ? $rsCustomer[0]['name'] : '';
$sayNumber = $obj->sayNumber($rs[0]['grandtotal']);
$trnotes = (!empty($rs[0]['trdesc'])) ? '<div style="clear:both"></div><strong>Catatan :</strong> <br>' . str_replace(chr(13),'<br>',$rs[0]['trdesc']) : '';
$html = $obj->printSetting['defaultStyle'];
$html .= ' 
<table cellpadding="2" > 
<tr><td><div class="title">Total Penjualan</div></td></tr>
<tr><td><div class="subtitle">'.$rs[0]['code'].'</div></td></tr>
</table> 

<div style="clear:both"></div>
 
<table>
<tr>
<td style="width:300px;" >
<table cellpadding="2">
<tr><td class="header-row-header" style="width:120px">'.$obj->lang['date'].'</td><td style="width:10px; text-align:center">:</td><td style="width:170px">'.$obj->formatDBDate($rs[0]['trdate'],'d / m / Y').'</td></tr>
<tr><td class="header-row-header">'.$obj->lang['customer'].'</td><td style="text-align:center">:</td><td>'. $customerName .'</td></tr>
</table>
</td>
<td style="width:370px;"> 
</td>
</tr>
</table>

<div style="clear:both"></div>
<table cellpadding="4" class="table-transaction">';

$cellArray = array ();
array_push($cellArray, array('label' => $obj->lang['number'],'width' => '30'));
array_push($cellArray, array('label' => $obj->lang['invoiceDate'], 'width' => '90','align' =>'center')); 
array_push($cellArray, array('label' => $obj->lang['invoiceCode'],'width' => '120' ));
array_push($cellArray, array('label' => 'Debitur' ));
array_push($cellArray, array('label' => 'Ukuran', 'width'=>'100', 'align'=> 'center'));
array_push($cellArray, array('label' => $obj->lang['route'], 'width' => '130','align' =>'center')); 
array_push($cellArray, array('label' => $obj->lang['amount'],'align' =>'right', 'width' => '70'));  
$html .= $obj->generatePrintTableRow( array('class' => 'col-header','cell' =>  $cellArray));  
 

    
for($i=0;$i<count($rsDetail);$i++){ 
    $rsInvoice = $truckingServiceOrderInvoice->getDataRowById($rsDetail[$i]['invoicekey']);
    
    $rsCustomer = $customer->getDataRowById($rsInvoice[0]['customerkey']);
    
    $rsDetailInvoice = $truckingServiceOrderInvoice->getDetailById($rsInvoice[0]['pkey']);
    //$obj->setLog($rsDetailInvoice,true);
    $arrRoute = array();
    
    if(!empty($rsDetailInvoice)){
        for($j=0;$j<count($rsDetailInvoice);$j++){
            
            $rsItemInvoice = $truckingServiceOrderInvoice->getItemDetail($rsDetailInvoice[$j]['pkey']);
            //$obj->setLog($rsItemInvoice,true);
            $rsSOHeader = $truckingServiceOrder->getDataRowById($rsDetailInvoice[$j]['salesorderkey']); 
            
            $route = $rsSOHeader[0]['routefrom'].' - '.$rsSOHeader[0]['routeto'];
                    array_push($arrRoute,$route);

            $arrService = array();
            for($k=0;$k<count($rsItemInvoice);$k++){
                
                $itemname = (!empty($rsItemInvoice[$k]['aliasname'])) ? $rsItemInvoice[$k]['aliasname'] : $rsItemInvoice[$k]['itemname'];
                $servicename = $obj->formatNumber($rsItemInvoice[$k]['qtyinbaseunit']).'x '.$itemname;
                
                array_push($arrService,$servicename);
                

            }
            
            $detailService = implode('<br>',$arrService);
                

        }
        
        $detailRoute = implode('<br>', $arrRoute);
    }
    $html .= '<tr><td style="text-align:right">'.($i+1).'.</td><td style ="text-align:center">'.$obj->formatDBDate($rsInvoice[0]['trdate']).'</td><td>'.$rsInvoice[0]['code'].'</td><td>'.$rsCustomer[0]['name'].'</td><td style ="text-align:center">'.$detailService.'</td><td style ="text-align:center">'.$detailRoute.'</td><td style ="text-align:right">'.$obj->formatNumber($rsDetail[$i]['amount']).'</td></tr>';

}  

    
$html .= '    
</table>  
<div style="clear:both"></div> 
<table cellpadding="4"> 
<tr><td rowspan="3" style="width:440px"><strong>Terbilang</strong> :<br>'.ucwords($sayNumber).' Rupiah.</td><td style="text-align:right; font-weight:bold;  width:130px; ">Total</td><td style="text-align:right; font-weight:bold;  width:110px;"  >'.$obj->formatNumber($rs[0]['grandtotal']).'</td></tr>
</table>

<div style="clear:both"></div>   
'.$trnotes.'
<div style="clear:both"></div>  
<table>
<tr><td><strong>Tempo Pembayaran : </strong>'.$rsTop[0]['name'].' </td></tr>
</table>
<div style="clear:both"></div>  
<table>
<tr><td style="width:30px;border:solid 1px black;"></td><td style="width:10px;"></td><td style="width:450px">BCA 4191718199 atas nama PT.Samudera Bahtera Maritim Transpor</td></tr>
<tr><td></td><td></td><td></td></tr>
<tr><td style="width:30px;border:solid 1px black;"></td><td></td><td>BCA 4194777789 atas nama Williem</td></tr>
</table>
<div style="clear:both"></div>
<div style="clear:both"></div>
';
  
$arrSignedUser = array();
if (!empty($rs[0]['createdby'])){ 
    $rsEmployee = $employee->getDataRowById($rs[0]['createdby']);
    $user = $rsEmployee[0]['name'];
}
array_push($arrSignedUser, array($obj->lang['created'],$user));    
//array_push($arrSignedUser, array($obj->lang['messenger']));     
//array_push($arrSignedUser, array($obj->lang['received']));    
$html .= $obj->generateSignLabel($rs, $arrSignedUser); 
return $html;
}

?>