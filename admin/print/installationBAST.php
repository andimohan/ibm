<?php 

includeClass(array('InstallationBAST.class.php'));
$installationBAST = new InstallationBAST();

$obj= $installationBAST;  

$generateReportContent = function ($dataset){ 
setlocale(LC_TIME, 'id_ID.utf8');
$obj = new InstallationBAST(); 
$salesOrderSubscription = new SalesOrderSubscription();
$loc = new Location();
$cust = new Customer();
$jobDet = new JobDetails();
 
$rs = $dataset['rs'];
$rsHeader = $obj->getDataRowById($rs[0]['pkey']); 

if(!empty($rs[0]['sobkey'])){
    $rsSOB = $salesOrderSubscription->getDataRowById($rs[0]['sobkey']);
}
if(!empty($rsSOB[0]['customerkey'])){
    $rsCustomer = $cust->getDataRowById($rsSOB[0]['customerkey']);
}
if(!empty($rsCustomer[0]['locationkey'])){
    $rsLocation = $loc->getDataRowById($rsCustomer[0]['locationkey']);
}
if(!empty($rsSOB[0]['jobdetailskey'])){
    $rsJobDetail = $jobDet->getDataRowById($rsSOB[0]['jobdetailskey']);
}
$companyName = 'PT. MITRA VISIONER PRATAMA';
$html = $obj->printSetting['defaultStyle'];
$html .= ' 
<table cellpadding="2" > 
<tr><td><div class="title">Berita Acara Aktivasi</div></td></tr>
<tr><td><div class="subtitle">No : '.$rs[0]['code'].'</div></td></tr>
</table> 

<div style="clear:both"></div>
<table cellpadding="2">  
<tr><td>
Pada hari ini 
('.$obj->formatDBDate($rs[0]['trdate'],'D').'), tanggal 
('.$obj->formatDBDate($rs[0]['trdate'],'d').'), bulan 
('.$obj->formatDBDate($rs[0]['trdate'],'M').'), tahun 
('.$obj->formatDBDate($rs[0]['trdate'],'Y').') 
('.$obj->formatDBDate($rs[0]['trdate'],'d / m / Y').'), bertempat di Jalan Bangka IX No.43 A RT 05 / RW 12, Kel. Pela Mampang, Kec. Mampang Prapatan, Jakarta Selatan. Kami yang bertanda tangan di bawah ini:
</td></tr>
<tr><td style="width:150px; font-weight:bold; border:1px solid black">Nama</td><td style="width:500px; border:1px solid black;">: '.$rsSOB[0]['pic'].'</td></tr> 
<tr><td style="font-weight:bold; border:1px solid black">Jabatan</td><td style="border:1px solid black;">: </td></tr>
<tr><td style="font-weight:bold; border:1px solid black">Departemen</td><td style="border:1px solid black;">: </td></tr>  
<tr><td style="font-weight:bold; border:1px solid black">Lokasi Kerja</td><td style="border:1px solid black;">: '.$rsLocation[0]['name'].'</td></tr>  
<tr><td style="width:500px;">Selanjutnya disebut <strong>'.$companyName.'</strong></td></tr>
<br>
<tr><td style="width:150px; font-weight:bold; border:1px solid black">Nama</td><td style="width:500px; border:1px solid black;">:</td></tr> 
<tr><td style="font-weight:bold; border:1px solid black">Jabatan</td><td style="border:1px solid black;">: </td></tr>
<tr><td style="font-weight:bold; border:1px solid black">Departemen</td><td style="border:1px solid black;">: </td></tr>  
<tr><td style="font-weight:bold; border:1px solid black">Lokasi Kerja</td><td style="border:1px solid black;">: </td></tr>  
<tr><td style="width:500px;">Selanjutnya disebut <strong>PELANGGAN</strong></td></tr>
<br>
<tr><td style="width:500px;">Menyatakan bahwa sebagai berikut:</td></tr>
<tr><td style="width:150px; font-weight:bold; border:1px solid black">Atas Nama Perusahaan</td><td style="width:500px; border:1px solid black;">: '.$rsCustomer[0]['name'].'</td></tr> 
<tr><td style="font-weight:bold; border:1px solid black">Jenis Layanan</td><td style="border:1px solid black;">: '.$rsJobDetail[0]['name'].'</td></tr>
<tr><td style="font-weight:bold; border:1px solid black">Produk</td><td style="border:1px solid black;">: '.$rsSOB[0]['product'].'</td></tr>  
<tr><td style="font-weight:bold; border:1px solid black">Kapasitas</td><td style="border:1px solid black;">: (Pending)</td></tr>  
<tr><td style="font-weight:bold; border:1px solid black">Keterangan</td><td style="border:1px solid black;">: '.str_replace(chr(13),'<br>',$rs[0]['trdesc']).'</td></tr>  
</table>   
<div style="clear:both"></div> ';
 
$html .= '
<div style="clear:both"></div> 
<table cellpadding="4">
<tr><td style="width:100%;">Telah selesai dilakukan integrasi, serta dinyatakan <strong>SIAP DIGUNAKAN / DIOPERASIKAN</strong>, terhitung sejak tanggal <strong>'.$obj->formatDBDate($rs[0]['trdate'],'d-m-Y').'</strong>.
<br><br>
Demikian Berita Acara ini dibuat dengan sebenar-benarnya dan sesuai dengan keadaan di lapangan agar dapat digunakan sebagaimana mestinya.
</td></tr>
<br><br><br>
<tr><td>Jakarta, '.$obj->formatDBDate($rs[0]['trdate'],'d-m-Y').'</td></tr>
<br><br>
<tr><td style="width:350px;"><strong>Pelangan</strong></td><td style="width:200px;text-align:center"><strong>'.$companyName.'</strong></td></tr>
<tr><td><strong>PT</strong></td></tr>
<br><br><br><br><br><br>
<tr>
<td style="width:200px;border-bottom:2px solid black">
</td><td style="width:150px;"></td>
<td style="width:200px; border-bottom:2px solid black; text-align:center">'.$rsSOB[0]['pic'].'</td></tr>
</table>
<div "clear:both"></div>';

// $html .= $obj->generateSignLabel($rs);
return $html;

}

?>