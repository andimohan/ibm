<?php 

includeClass(array('TicketSupportWorkOrder.class.php','JobDetails.class.php','Division.class.php'));
$ticketSupportWorkOrder = new TicketSupportWorkOrder();

$obj = $ticketSupportWorkOrder;
 
$generateReportContent = function ($dataset){ 
 
$obj = new TicketSupportWorkOrder();  
$item = new Item();
$customer = new Customer(); 
$jobDetails = new JobDetails(); 
$employee = new Employee();
$division = new Division();
$city = new City();
$ticketSupport = new TicketSupport();
$media = new Media(); 
$tandaBenar = ''; 
$rs = $dataset['rs']; 
$rsDetail = $obj->getDetailWithRelatedInformation($rs[0]['pkey']);
$rsDetailTechnician = $obj->getDetailTechnicianWithRelatedInformation($rs[0]['pkey']);
$rsTicket = $ticketSupport->getDataRowById($rs[0]['ticketkey']);

if(!empty($rsTicket)){
    $rsCustomer = $customer->getDataRowById($rsTicket[0]['customerkey']);
    if(!empty($rsCustomer)){
        $rsMedia = $media->getDataRowById($rsCustomer[0]['mediakey']);
                    $rsCity = $city->getDataRowById($rsCustomer[0]['citykey']);

    }
}    
    
$border = 'border-bottom:solid 1px black;border-top:solid 1px black;';

$trnotes = (!empty($rs[0]['notes'])) ? '<strong>Catatan :</strong> <br> ' . str_replace(chr(13),'<br>',$rs[0]['notes']) : '';

$rsJobDetails = $jobDetails->searchData('','',true,' and ('.$jobDetails->tableName.'.statuskey = 1)',' order by '.$jobDetails->tableName.'.name asc');    


    
$html = $obj->printSetting['defaultStyle'];
$html .= ' 
<table cellpadding="2" > 
<tr><td><div class="title">WORK ORDER SUPPORT</div></td></tr>
<tr><td><div class="subtitle">'.$rs[0]['code'].'</div></td></tr>
</table> 
<div style="clear:both"></div>

<table cellpadding="4" style=""> 
<tr><td style="font-weight:bold; font-size:14px;">DATA PENGERJAAN</td></tr>
<tr>
<td class="" style="width: 120px; border:solid 1px black;">Jenis Pemasangan</td><td style="width:15px;'.$border.'">:</td><td style="width: 205px;'.$border.';border-right:solid 1px black;">'.$rsMedia[0]['name'].'</td>
<td class="" style="width: 120px; border:solid 1px black;">Tgl. Mulai Kerja</td><td style="width:15px;'.$border.'">:</td><td style="width: 205px; '.$border.';border-right:solid 1px black;">'.$obj->formatDBDate($rs[0]['starttime'],'d / m / Y H:i').'</td>
</tr>
<tr>
<td class="" style="width: 120px; border:solid 1px black;">Tgl. Work Order</td><td style="width:15px;'.$border.'">:</td><td style="width: 205px; '.$border.';border-right:solid 1px black;">'.$obj->formatDBDate($rs[0]['trdate'],'d / m / Y').'</td>
<td class="" style="width: 120px; border:solid 1px black;">Tgl. Selesai Kerja</td><td style="width:15px;'.$border.'">:</td><td style="width: 205px; '.$border.';border-right:solid 1px black;">'.$obj->formatDBDate($rs[0]['endtime'],'d / m / Y H:i').'</td>
</tr> 
<tr>
<td class="" style="width: 120px; border:solid 1px black;">Vendor</td><td style="width:15px;'.$border.'">:</td><td colspan="3" style="width: 545px; '.$border.';border-right:solid 1px black;"></td>
</tr>
<tr>
<td class="" style="width: 120px; border:solid 1px black;">Keterangan Pengerjaan</td><td style="width:15px;'.$border.'">:</td><td colspan="3" style="width: 545px; '.$border.';border-right:solid 1px black;">'.str_replace(chr(13),'<br>',$rs[0]['workdescription']).'</td>
</tr>
</table> <div style="clear:both"></div>
';
    
$html .= '
<table cellpadding="4" style=""> 
<tr><td style="font-weight:bold; font-size:14px;">DATA PELANGGAN</td></tr>
<tr>
<td class="" style="width: 100px; border:solid 1px black;">SID</td><td style="width:15px;'.$border.'">:</td><td colspan="3" style="width: 580px; '.$border.';border-right:solid 1px black;">'.$rsCustomer[0]['sid'].'</td>
</tr>
<tr>
<td class="" style="width: 100px; border:solid 1px black;">Nama Pelanggan</td><td style="width:15px;'.$border.'">:</td><td colspan="3" style="width: 580px; '.$border.';border-right:solid 1px black;">'.$rsCustomer[0]['name'].'</td>
</tr>
<tr>
<td rowspan="2" class="" style="width: 100px; border:solid 1px black;">Alamat Lengkap</td><td rowspan="2" style="width:15px;'.$border.'">:</td><td colspan="3" rowspan="2" style="width: 580px; '.$border.';border-right:solid 1px black;">'.str_replace(chr(13),'<br>',$rsCustomer[0]['address']).'</td>
</tr>
<tr>
<td></td>
</tr>
<tr>
<td class="" style="width: 100px; border:solid 1px black;">Attention</td><td style="width:15px;'.$border.'">:</td><td style="width: 580px; '.$border.';border-right:solid 1px black;">'.$rsCustomer[0]['attention'] .'</td>
</tr> 
<tr>
<td class="" style="width: 100px; border:solid 1px black;">No. Telepon</td><td style="width:15px;'.$border.'">:</td><td style="width: 580px;'.$border.';border-right:solid 1px black;">'.$rsCustomer[0]['phone'].'</td>
</tr>
<tr>
<td class="" style="width: 100px; border:solid 1px black;">Email</td><td style="width:15px;'.$border.'">:</td><td style="width: 580px; '.$border.';border-right:solid 1px black;">'.$rsCustomer[0]['email'].'</td>
</tr> 
<tr>
<td class="" style="width: 100px; border:solid 1px black;">Kota</td><td style="width:15px;'.$border.'">:</td><td style="width: 580px; '.$border.';border-right:solid 1px black;">'.$rsCity[0]['name'].'</td>
</tr> 
<tr>
<td class="" style="width: 100px; border:solid 1px black;">Masalah</td><td style="width:15px;'.$border.'">:</td><td style="width: 580px; '.$border.';border-right:solid 1px black;">'.$rsTicket[0]['message'].'</td>
</tr> 

</table> 
<div style="clear:both"></div>

';


/*$html .= '<table cellpadding="2"><tr><td style="font-weight:bold; font-size:14px;">DETAIL PENGERJAAN</td></tr>';
$html .= '<tr>';

for ($i=0;$i<count($rsJobDetails);$i++){
    if ($i%3==0) $html .= '</tr><tr>';
    $checkV = '';
    if($rsJobDetails[$i]['pkey']==$rsSalesOrder[0]['jobdetailskey'])
        $checkV = 'V';
    
    $html .= '<td style="width:20px; border-bottom:1px solid #333; text-align:center">'.$checkV.'</td><td style="width: 200px;">'.$rsJobDetails[$i]['name'].'</td>';

}

$i=$i%3;  
if ($i != 0)    
for($j=$i;$j<3;$j++)
    $html .= '<td style="width: 20px;"></td><td style="width: 200px;"></td>';
    
    
$html .= '</tr></table>';*/
    
$html .= ' 
<table cellpadding="4" class="" style="">
<tr><td style="font-weight:bold; font-size:14px;">DATA TEKNISI</td></tr>
<tr class="" ><td style="border:solid 1px black;width:170;text-align:left;">Nama</td><td style="border:solid 1px black;width:170;text-align:center;">Jam</td><td style="border:solid 1px black;width:170;text-align:center;">Tanda Tangan</td><td style="border:solid 1px black;width:170;text-align:center;">Keterangan</td></tr>';

for ($i=0;$i<count($rsDetailTechnician);$i++){  

    $html .= '<tr>
            <td style="border:solid 1px black;text-align:left;"> '.$rsDetailTechnician[$i]['technicianname'].'</td>
            <td style="border:solid 1px black;text-align:center;"></td>
            <td style="border:solid 1px black;text-align:center;"></td>
            <td style="border:solid 1px black;text-align:center;"></td>
            </tr>' ; 
}
$html .= '</table><div style="clear:both"></div>' ;
      

$html .= ' 
<table cellpadding="4" class="" style="">
<tr><td style="font-weight:bold; font-size:14px;">MATERIAL SUPPORT</td></tr>
<tr class="" ><td style="border:solid 1px black;width:40px;text-align:right;">No.</td><td style="border:solid 1px black;width:510px;;text-align:left;">Material</td><td style="border:solid 1px black;; width:70px;text-align:right" >Quantity</td><td style="border:solid 1px black;width:60px;" >Unit</td></tr>';

for ($i=0;$i<count($rsDetail);$i++){  

    $html .= '<tr>
            <td style="border:solid 1px black; text-align:right">'.($i+1).'.</td>
            <td style="border:solid 1px black;  text-align:left">'.$rsDetail[$i]['itemname'].'</td>
            <td style="border:solid 1px black; text-align:right">'.$obj->formatNumber($rsDetail[$i]['qty']).'</td>
            <td style="border:solid 1px black;">'. $rsDetail[$i]['unitname'] .'</td>
            </tr>' ; 
}
$html .= '</table><div style="clear:both"></div>' ;
    
/*$html .= ' 
<table cellpadding="4" class="" style="">
<tr><td style="font-weight:bold; font-size:14px;">DATA SALES</td></tr>
<tr class="" ><td style="border:solid 1px black;width:226px;;text-align:center;">Nama</td><td style="border:solid 1px black;; width:226px;text-align:center;" >Tanda Tangan 1</td><td style="border:solid 1px black;width:226px;text-align:center;" >Tanda Tangan 2</td></tr>
<tr class="" ><td style="height:100px;border:solid 1px black;width:226px;;text-align:center;"></td><td style="height:100px;border:solid 1px black;; width:226px;text-align:center;" ></td><td style="height:100px;border:solid 1px black;width:226px;" ></td></tr>
';
//
//for ($i=0;$i<count($rsDetail);$i++){  

//    $html .= '<tr>
//            <td style="border:solid 1px black; text-align:center">'.($i+1).'</td>
//            <td style="border:solid 1px black;">'.$rsDetail[$i]['itemname'].'</td>
//            <td style="border:solid 1px black; text-align:right">'.$obj->formatNumber($rsDetail[$i]['qty']).'</td>
//            <td style="border:solid 1px black;">'. $rsDetail[$i]['unitname'] .'</td>
//            </tr>' ; 
//}
$html .= '</table><div style="clear:both"></div>' ;*/

 $html .= '
<table cellpadding="2" style="font-size:12px;"> 
<tr>
<td class="" style="border-bottom:solid 1px black; width: 150px;height:100px text-align:center;">Mengetahui,<br>Administrasi,</td><td ></td><td colspan="2" style="border:solid 1px black;width: 360;"> '.$trnotes.' </td>
</tr>
</table> 
<div style="clear:both"></div>

';     
//$html .= $obj->generateSignLabel($rs); 
return $html;
}

?>