<?php 
 $pdf->setCustomSettings(
    array(  
         'paperSetting' => 'A4',        
         'showPrintHeader' => false,
         ) 
);  


$obj = $emklInvoiceReceipt;
 
$generateReportContent = function ($dataset){ 
 
$obj = new EMKLInvoiceReceipt();  
$emklOrderInvoice = new EMKLOrderInvoice();
$customer = new Customer();
$employee = new Employee();
  
$rs = $dataset['rs']; 
$rsDetail = $obj->getDetailWithRelatedInformation($rs[0]['pkey']); 
$rsCustomer = $customer->getDataRowById($rs[0]['customerkey'] );  
$customerName = (!empty($rsCustomer)) ? $rsCustomer[0]['name'] : '';
$sayNumber = $obj->sayNumber($rs[0]['grandtotal']);
$trnotes = (!empty($rs[0]['trdesc'])) ? '<div style="clear:both"></div><strong>Note :</strong> <br>' . str_replace(chr(13),'<br>',$rs[0]['trdesc']) : '';

        
$profileImg = $obj->loadSetting('companyLogo'); 
//$img = HTTP_HOST.'phpthumb/phpThumb.php?src='.$obj->phpThumbURLSrc.'setting/companyLogo/'.$profileImg.'&w=200&h=60&hash='.getPHPThumbHash($profileImg);
$img = $obj->phpThumbURLSrc.'setting/companyLogo/'.$profileImg.'';

$html = $obj->printSetting['defaultStyle'];
$html .= ' 
<style>
    .col-custom-header {
        border :solid  1px  black;
    }
    
    .table-transaction-custom {
        border :solid  1px  black;
        width: 100%;
    }
</style>
<table cellpadding="2" > 
<tr><td style="width:135px"><div class="title" style="text-align:left;border-bottom:1px solid black;">TANDA TERIMA</div></td><td style="text-align:right;width:550px;"><img src="'.$img.'" style="width:50px;"/></td></tr>
</table> 

 
<table>
<tr>
<td style="width:300px;" >
<table cellpadding="2">
<tr><td class="header-row-header" style="width:50px">Kepada</td><td style="width:10px;">:</td><td style="width:250px">'. $customerName .'</td></tr>
</table>
</td>
<td style="width:370px;"> 
</td>
</tr>
</table>

<div style="clear:both"></div>
<table cellpadding="4" class="table-transaction-custom">';

/*
$cellArray = array ();
array_push($cellArray, array('label' => $obj->lang['number'],'width' => '30'));
array_push($cellArray, array('label' => $obj->lang['invoiceCode'], 'width' => '130' ));
array_push($cellArray, array('label' => $obj->lang['note'])); 
array_push($cellArray, array('label' => $obj->lang['invoiceDate'], 'width' => '130','align' =>'center')); 
array_push($cellArray, array('label' => $obj->lang['curr'], 'width' => '40','align' =>'center')); 
array_push($cellArray, array('label' => $obj->lang['amount'],'align' =>'right', 'width' => '100'));  
$html .= $obj->generatePrintTableRow( array('class' => 'col-header','cell' =>  $cellArray));  
*/
    
    
$html .= '<tr>
                <td style="border:1px solid black;width:30px;text-align:right;">'.$obj->lang['number'].'</td>
                <td style="border:1px solid black;width:300px;text-align:center;">NO INVOICE</td>
                <td style="border:1px solid black;width:350px;text-align:center;">DESKRIPSI</td>
        </tr>';

    

    
for($i=0;$i<count($rsDetail);$i++){ 
    $html .= '<tr>
                <td style="text-align:right;border:1px solid black;width:30px;border-bottom:1px solid black">'.($i+1).'.</td>
                <td style="border-right:1px solid black;border:1px solid black">'.$rsDetail[$i]['invoicecode'].'</td>
                <td style="border:1px solid black">'.$rsDetail[$i]['description'].'</td>
        </tr>';
}  

//
//<div style="clear:both"></div> 
//<table cellpadding="4"> 
//<tr><td rowspan="3" style="width:440px"><strong>Terbilang</strong> :<br>'.ucwords($sayNumber).' Rupiah.</td><td style="text-align:right; font-weight:bold;  width:130px; ">Total</td><td style="text-align:right; font-weight:bold;  width:110px;"  >'.$obj->formatNumber($rs[0]['grandtotal']).'</td></tr>
//</table>
//<div style="clear:both"></div>   
    
$html .= '</table> ';

$html .='<div style="clear:both"><div style="clear:both"></div>
<table cellpadding="4" class="sign" >
<tr>
<td></td>
<td class="sign-col-space" style="width: 200px"></td>
<td >Jakarta, '.$obj->formatDBDate($rs[0]['trdate'],'d F  Y').'</td> 
</tr>
<tr>
<td style="height:100px; border-bottom:1px dotted black;"><strong>PENGIRIM :</strong></td>
<td class="sign-col-space"  style="width: 200px"></td>
<td   style="border-bottom:1px dotted black;"><strong>YANG MENERIMA :</strong></td> 
</tr>  
    
</table>' ;
     
$html .= $trnotes;
return $html;
}

?>