<?php 
 $pdf->setCustomSettings(
    array( 
         'footer' => '',   
         ) 
);  

$generateReportContent = function ($dataset){
    
    $obj = new TruckingServiceOrderInvoice(); 
    $customer = new Customer();
    $employee = new Employee();
    
    $rs = $dataset['rs'];
    
    $customerkey = $rs[0]['customerkey'];
    $rsCustomer = $customer->getDataRowById($customerkey);
        
    $html = $obj->printSetting['defaultStyle'];
    $html .= ' 
    <table cellpadding="2" > 
    <tr><td><div class="title">'.strtoupper($obj->lang['invoiceReceipt']).'</div></td></tr>
    </table> 

    <div style="clear:both"></div>

    <table cellpadding="2"> 
    <tr><td class="header-row-header" style="width:80px">'.ucwords($obj->lang['date']).'</td><td style="width:10px; text-align:center">:</td><td style="width:580px;">'.date('d / m / Y').'</td></tr>
    <tr><td colspan="3"></td></tr> 
    <tr><td colspan="3" style="font-weight:bold">Kepada Yth.</td></tr> 
    <tr><td colspan="3">'. $rsCustomer[0]['name'] .'<br>'.str_replace(chr(13),'<br>',$rsCustomer[0]['address']).'</td></tr>   
    </table> ';
    
    $html .='<div style="clear:both"></div>';

    $html .= '<table cellpadding="4" class="table-transaction">';

    $cellArray = array ();
    array_push($cellArray, array('label' => $obj->lang['number'],'width' => '30'));
    array_push($cellArray, array('label' => $obj->lang['invoiceCode'],  'width' => '200'));
    array_push($cellArray, array('label' => $obj->lang['invoiceDate'], 'width' => '140','align' =>'center')); 
    array_push($cellArray, array('label' => $obj->lang['amount'],'align' =>'right'));  
    $html .= $obj->generatePrintTableRow( array('class' => 'col-header','cell' =>  $cellArray));  
 
    for($i=0;$i<count($rs);$i++){ 
        $html .= '  
            <tr><td style="text-align:right;">'.($i+1).'.</td><td>'.$rs[$i]['code'].'</td><td style="text-align:center">'.$obj->formatDBDate($rs[$i]['trdate']).'</td><td style="text-align:right">'.$obj->formatNumber($rs[$i]['grandtotal']).'</td></tr>   
        ';
    }
    
    $html .= '</table>';
    
    
    $html .='<div style="clear:both"></div>';
    $html .='<div style="clear:both"></div>';
   
    $rsEmployee = $employee->getDataRowById($obj->userkey);
    $user = $rsEmployee[0]['name'];
 
    
     $html .=' 
        <table cellpadding="4" class="sign">
        <tr>
            <td class="sign-col"><strong>Dibuat</strong></td>
            <td class="sign-col-space"></td>
            <td class="sign-col"><strong>Kurir</strong></td>
            <td class="sign-col-space"></td>
            <td class="sign-col"><strong>Diterima</strong></td>
        </tr>
        <tr>
            <td  class="sign-name">'.$user.'</td>
            <td class="sign-col-space"></td>
            <td  class="sign-name"></td>
            <td class="sign-col-space"></td>
            <td  class="sign-name">'.$rsCustomer[0]['name'].'</td>
        </tr>
        </table>' ;

    
    
    return $html;
};
  
?>