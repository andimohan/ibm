<?php 
 

$generateReportContent = function ($dataset){ 
 
$obj = new SalesOrderInvoiceReceipt();  
$truckingServiceOrderInvoice = new TruckingServiceOrderInvoice();
$customer = new Customer();
$employee = new Employee();
  
$rs = $dataset['rs']; 
$rsDetail = $obj->getDetailById($rs[0]['pkey']); 
$rsCustomer = $customer->getDataRowById($rs[0]['customerkey'] );  
$customerName = (!empty($rsCustomer)) ? $rsCustomer[0]['name'] : '';
$sayNumber = $obj->sayNumber($rs[0]['grandtotal']);
$trnotes = (!empty($rs[0]['trdesc'])) ? '<div style="clear:both"></div><strong>Catatan :</strong> <br>' . str_replace(chr(13),'<br>',$rs[0]['trdesc']) : '';
$html = $obj->printSetting['defaultStyle'];


 $html .= '<style>
.lite-color {color:#666} 
.logol-color {color:#002985}
</style>';
    
$html .= ' 
<table cellpadding="2" > 
<tr><td><div class="title">'.$obj->lang['invoiceReceipt'].'</div></td></tr>
<tr><td><div class="subtitle">'.$rs[0]['code'].'</div></td></tr>
</table> 

<div style="clear:both"></div>
 
<table>
<tr>
<td style="width:300px;" >
<table cellpadding="2">
<tr><td class="header-row-header" style="width:120px">'.$obj->lang['date'].'</td><td style="width:10px; text-align:center">:</td><td style="width:300px">'.$obj->formatDBDate($rs[0]['trdate'],'d / m / Y').'</td></tr>
<tr><td class="header-row-header">'.$obj->lang['customer'].'</td><td style="text-align:center">:</td><td>'. $customerName .'</td></tr>
<tr><td class="header-row-header">'.$obj->lang['attention'].'</td><td style="text-align:center">:</td><td>'. $rs[0]['picname'] .'</td></tr>
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
array_push($cellArray, array('label' => $obj->lang['invoiceCode'], ));
array_push($cellArray, array('label' => 'Document No.','width' => '170'));
array_push($cellArray, array('label' => $obj->lang['invoiceDate'], 'width' => '130','align' =>'center')); 
array_push($cellArray, array('label' => $obj->lang['amount'],'align' =>'right', 'width' => '110'));  
$html .= $obj->generatePrintTableRow( array('class' => 'col-header','cell' =>  $cellArray));  

// select JO utk dpt booking code

$arrInvoiceKey = array_column($rsDetail,'invoicekey');
    
$rsInvoiceCol =  $truckingServiceOrderInvoice->searchDataRow(array($truckingServiceOrderInvoice->tableName.'.pkey',
                                                                   $truckingServiceOrderInvoice->tableName.'.code',
                                                                   $truckingServiceOrderInvoice->tableName.'.trdate'
                                                                  ),
                                                            ' and '.$truckingServiceOrderInvoice->tableName.'.pkey in ('.$obj->oDbCon->paramString($arrInvoiceKey,',').')'
                                                            );
$rsInvoiceCol = array_column($rsInvoiceCol,null,'pkey');
$rsJobDetailCol = $truckingServiceOrderInvoice->getJODetail($arrInvoiceKey);
$rsJobDetailCol = $truckingServiceOrderInvoice->reindexDetailCollections($rsJobDetailCol,'refkey');  
    
for($i=0;$i<count($rsDetail);$i++){ 
    $rsInvoice = $rsInvoiceCol[$rsDetail[$i]['invoicekey']] ; //$truckingServiceOrderInvoice->getDataRowById($rsDetail[$i]['invoicekey']);
    $rsJobDetail = $rsJobDetailCol[$rsDetail[$i]['invoicekey']];
    

    $bookingCode = array();
    foreach($rsJobDetail as $joRow){
        if(!empty($joRow['donumber'])) array_push($bookingCode, $joRow['donumber']);
        if(!empty($joRow['shipmentnumber'])) array_push($bookingCode, $joRow['shipmentnumber']);  
    }

 
    $html .= '<tr><td style="text-align:right">'.($i+1).'.</td><td>'.$rsInvoice['code'].'</td><td>'.implode('<br>',$bookingCode).'</td><td style ="text-align:center">'.$obj->formatDBDate($rsInvoice['trdate']).'</td><td style ="text-align:right">'.$obj->formatNumber($rsDetail[$i]['amount']).'</td></tr>';
}  

    
$html .= '    
</table>  
<div style="clear:both"></div> 
<table cellpadding="4"> 
<tr><td rowspan="3" style="width:440px"><strong>Terbilang</strong> :<br>'.ucwords($sayNumber).' Rupiah.</td><td style="text-align:right; font-weight:bold;  width:130px; ">Total</td><td style="text-align:right; font-weight:bold;  width:110px;"  >'.$obj->formatNumber($rs[0]['grandtotal']).'</td></tr>
</table>
<div style="clear:both"></div>   
'.$trnotes.' <br>
<table  cellpadding="4"> 
<tr><td>
<div style="font-size: 0.9em">
<b>Catatan:</b><br>
<table cellpadding="2">
<tr>
<td style="width:15px">1.</td><td style="width: 650px">Mohon melakukan pengecekan atas Invoice beserta kelengkapan yang diterima sesuai dengan list pada tabel di atas.</td>
</tr>
<tr>
<td>2.</td><td>Apabila ada kesalahan/ketidakcocokan atas Invoice yang diterima, mohon menginformasikan kepada kami selambat-lambatnya dalam 7 (tujuh) hari sejak tanda terima ini  ditandatangani.</td>
</tr>
<tr>
<td>3.</td><td>Revisi atau permintaan apa pun atas Invoice tersebut di atas, hanya akan kami proses apabila kami menerima laporannya dalam 7 (tujuh) hari sejak tanda terima ini ditandatangani.</td>
</tr>
<tr>
<td>4.</td><td>Tanda tangan dan/atau stempel pada kolom penerima adalah sebagai tanda konfirmasi bahwa Invoice sudah benar beserta dengan dokumen pendukungnya.</td>
</tr> 
</table>
<br><br>
<b>AR Dept:</b><br>
- Andreas: andreas.joko@envilog.co.id<br>
- Marwin: marwin.keby@envilog.co.id<br>
</div>
</td></tr>
</table>
<br>
';
  
$arrSignedUser = array();
if (!empty($rs[0]['createdby'])){ 
    $rsEmployee = $employee->getDataRowById($rs[0]['createdby']);
    $user = $rsEmployee[0]['name'];
}
array_push($arrSignedUser, array($obj->lang['created'],$user));    
array_push($arrSignedUser, array($obj->lang['messenger']));     
array_push($arrSignedUser, array($obj->lang['received']));    
$html .= $obj->generateSignLabel($rs, $arrSignedUser); 
return $html;
}

?>