<?php  

$sign = '
<table>
<tr>
<td style="width: 540px;">
</td>
<td  style="width:140px;">
<table style="width:140px; font-size:0.9em"> 
    <tr>
        <td style="text-align:center; width: 70px; border: 1px solid black;">Pihak Pertama</td>
        <td style="text-align:center; width: 70px; border: 1px solid black;">Pihak Kedua</td>
    </tr>
    <tr>
        <td style="height:40px;border: 1px solid black;"></td>
        <td style="border: 1px solid black;"></td>
    </tr>
</table>
</td>
</tr> 
</table>
<br><br>
';


$PRINT_SETTINGS =  array(   
         'showPrintHeader' => false,
         'marginFooter' => 30,
         'footer' => $sign.'<table style="text-align:center;border-top:1px solid black"><tr><td style="width:680px">JI.Utama Pesona Metropolitan, Gardenia Residenoe Blok.RB No.06 Kel.Bojong Rawa Lumbu, Kec.Rawa Lumbu,<br>Kota Bekasi - Jawa Barat 17116 - Indonesia Telp.021-82724215</td></tr></table>',
);

includeClass('DisposalContract.class.php');
$disposalContract= createObjAndAddToCol( new DisposalContract()); 

$obj = $disposalContract;

$contractsContent1 = function ($dataset){ 
$obj = new DisposalContract(); 
$customer = new Customer(); 
$employee = new Employee(); 
$service = new Service(); 
$setting = new Setting(); 

    
$rs = $dataset['rs'];
$rsCustomer = $customer->getDataRowById($rs[0]['customerkey']);

$startingMonth = $obj->toLocalDate($obj->formatDBDate($rs[0]['startingdate'],'F'), 'id');
$startingDay = $obj->toLocalDate($obj->formatDBDate($rs[0]['startingdate'],'l'), 'id');
$startingYear = $obj->formatDBDate($rs[0]['startingdate'],'Y');
$dateStarting = $obj->formatDBDate($rs[0]['startingdate'],'d');
$startingDate = $dateStarting.' '.$startingMonth.' '.$startingYear;

$contractMonth = $obj->toLocalDate($obj->formatDBDate($rs[0]['trdate'],'F'), 'id');
$contractDay = $obj->toLocalDate($obj->formatDBDate($rs[0]['trdate'],'l'), 'id');
$contratctYear = $obj->formatDBDate($rs[0]['trdate'],'Y');
$contractDate = $obj->formatDBDate($rs[0]['trdate'],'d');

$month = $obj->toLocalDate($obj->formatDBDate($rs[0]['validdate'],'F'), 'id');
$year = $obj->formatDBDate($rs[0]['validdate'],'Y');
$date = $obj->formatDBDate($rs[0]['validdate'],'d');
$finishDate = $date.' '.$month.' '.$year;

$profileImg = $obj->loadSetting('companyLogo'); 
$logo =  (isset($_GET['logo']) && $_GET['logo'] == 0) ? '' : '<img src="'.$obj->phpThumbURLSrc.'setting/companyLogo/'.$profileImg.'" style="height:50px">';
$companyName = strtoupper($setting->loadSetting('companyName'));

$rsService = $service->getDataRowById($rs[0]['servicekey']);

/*$rsAssetDetail = $obj->getAssetGroupDetail($rs[0]['pkey']);
$rsItemDetail = $obj->getItemDetail($rs[0]['pkey']);*/

/*$rsService = $service->getDataRowById($rs[0]['servicekey']);
$qtyService = $obj->formatNumber($rs[0]['qtyservice']); 
$periodic = '';
if ($rsService[0]['duration'] == 12) {
    $periodic = '/ Tahun';
    $frequency = $qtyService. ' kali pertahun';
} else {
    $periodic = '/ Bulan';
    $frequency = $qtyService. ' kali perbulan';
}
$facilities = array();
foreach ($rsAssetDetail as $asset) {
    $facility = $obj->formatNumber($asset['qty'], -2).' x '.$asset['assetgroupname'];
    array_push($facilities, $facility);
}
foreach ($rsItemDetail as $item) {
    $facility = $obj->formatNumber($item['qty'], -2).' '.$item['unitname'].' '.$item['itemname'];
    if ($item['isperiodically'] == 1) {
        $facility = $facility.' '. $periodic;
    } 
    array_push($facilities, $facility);
}
$facilities = implode(chr(13),$facilities);*/

$html = $obj->printSetting['defaultStyle'];
    
/*$disposalPrice = $rs[0]['extraprice']; 
$maximuWeight = $obj->formatNumber($rs[0]['maximumweight'], 2). ' kg'; 
if ($maximuWeight == 0) {
    $maximuWeight = 'Non Kuota';
}
$servicePrice =  $rs[0]['sellingprice']; 
$additionalPrice = $rs[0]['exceedprice'];*/
    
$html .= '
<style>  
    .table-contract{ width:670px;} 
</style>
';
     
$html .= '
<table class="table-contract " style="font-weight:bold">
<tr><td style="font-size:14px;" >'.$logo.'</td></tr>
</table>';  
    
$html .= '
<div></div>
<div></div>
<table class="table-contract " style="text-align:center;font-weight:bold">
<tr><td style="font-size:14px;" >PERJANJIAN KERJASAMA PENGANGKUTAN LIMBAH </td></tr>
<tr><td style=";" >BAHAN BERBAHAYA DAN BERACUN (B3)</td></tr>
<tr><td style=";" >No.'.$rs[0]['code'].'</td></tr>
</table>
';
    
    
 $html .= '<div style="clear:both"></div>

<table class="table-contract" >
<tr>
<td >Pada hari ini, '.$contractDay.', tanggal '.$contractDate.' bulan '.$contractMonth.' tahun '.$contratctYear.', bertempat di '.$rs[0]['cityname'].'-, dibuat dan ditandatangani <b>Perjanjian Kerjasama Pengangkutan Limbah Bahan Berbahaya dan Beracun (B3)</b> oleh dan antara :</td>
</tr>
</table>
';   
    
$html .= '<div style="clear:both"></div>

<table cellpadding="8" class="table-contract " style="width:940px" >
<tr>
<td style="border:1px solid black;width:160px;text-align:center;font-weight:bold">PARA PIHAK</td>
<td style="border:1px solid black;width:520px">
<table cellpadding="4">
<tr><td style="width:20px;font-size:10px"><b>A.</b></td><td style="width:490px;text-align: justify;"><b>'.$rs[0]['customername'].'</b> beralamat di '.$rsCustomer[0]['address'].',
yang dalam hal ini diwakili oleh <b>'.$rs[0]['pic'].'</b> selaku <b>'.$rs[0]['jobposition'].'</b>, untuk selanjutnya
disebut sebagai "<b>Pihak Pertama</b>".	
</td></tr>
<tr><td style="font-size:10px"><b>B.</b></td><td style="width:490px;text-align: justify;"><b>PT BHUMAN CATUR LESTARI</b> beralamat di Pesona Metropolitan Gardenia Residence Blok RB 06 RT 006 RW 042, Kel.Bojong Rawalumbu, Kec.RawaIumbu, Kota Bekasi 17116, yang dalam hal ini diwakili oleh <b>OKTO MEGA NAPITUPULU</b> selaku <b>DIREKTUR UTAMA</b> untuk selanjutnya disebut <b>"Pihak Kedua"</b>.</td></tr>
<tr><td ><b> - </b></td><td style="width:490px;text-align: justify;">Selanjutnya <b>PIHAK KESATU</b> dan <b>PIHAK KEDUA</b> secara bersama-sama disebut <b>PARA PIHAK</b>, dan masing-masing disebut <b>PIHAK</b>, setuju dan sepakat untuk melakukan Perjanjian Kerjasama menurut syarat dan ketentuan sebagaimana tercantum dalam pasal-pasal sebagai berikut</td></tr>
</table>
</td>
</tr>
<tr>
<td style="border:1px solid black;width:160px;text-align:center;font-weight:bold">Pasal 1<br>RUANG LINGKUP DAN URAIAN PEKERJAAN</td>
<td style="border:1px solid black;text-align: justify;">Pihak Pertama menunjuk Pihak Kedua untuk mengangkut limbah B3 yang dihasilkan oleh Pihak Pertama, sesuai dengan ijin yang dimiliki oleh PIHAK KEDUA dari Kementerian Lingkungan Hidup dan Kehutanan dan Kementerian Perhubungan Direktorat Jenderal Perhubungan Darat Republik Indonesia seta instansi terkait lainnya</td>

</tr>
<tr>
<td style="border:1px solid black;width:160px;text-align:center;font-weight:bold">Pasal 2<br><b>JANGKA WAKTU</b></td>
<td style="border:1px solid black;">
<table cellpadding="">
<tr><td style="width:20px" ><b> - </b></td><td style="width:480px;text-align: justify;">Perjanjian ini berlaku untuk 1 (satu) tahun terhitung sejak Perjanjian ini disepakati dan ditandatangani atau mulai '.$startingDate.' hingga '.$finishDate.'</td></tr>
<tr><td ><b> - </b></td><td style="width:480px;text-align: justify;">Perjanjian ini dapat diperpanjang kembali, apabila dikemudian hari PARA PIHAK saling setuju dan mufakat.</td></tr>
</table>
</td>
</tr>

<tr>
<td style="border:1px solid black;width:160px;text-align:center;font-weight:bold">Pasal 3<br><b>LOKASI LAYANAN</b></td>
<td style="border:1px solid black;text-align: justify;">
<table cellpadding="">
<tr><td style="width:20px;font-size:10px"><b> 1. </b></td><td style="width:480px;text-align: justify;">Untuk lokasi layanan / tempat pengambilan limbah B3 terletak di '.$rsCustomer[0]['address'].'</td></tr>
<tr><td style="width:20px;font-size:10px"><b> 2. </b></td><td style="width:480px;text-align: justify;">Apabila lokasi layanan tidak sesuai dengan ayat (1) diatas, maka Pihak Kedua berhak melakukan penyesuaian biaya kepada Pihak Pertama.</td></tr>
</table>
</td>
</tr>

<tr>
<td style="border:1px solid black;width:160px;text-align:center;font-weight:bold">Pasal 4<br>BIAYA-BIAYA DAN CARA PEMBAYARAN</td>
<td style="border:1px solid black;width:520px">
<table cellpadding="">
<tr><td  style="width:20px;"><b> - </b></td><td style="width:480px;text-align: justify;font-size:11px">Para Pihak setuju bahwa Biaya Jasa yang dikenakan oleh Pihak Kedua kepada Pihak Pertama adalah sebagaimana tersebut pada Lampiran Tabel Biaya dan Jasa dalam Lampiran 2 Perjanjian ini.</td></tr>
<tr><td ><b> - </b></td><td style="width:480px;text-align: justify;font-size:11px">Pihak Pertama akan melakukan pembayaran biaya jasa tersebut sesuai dengan tagihan yang diterbitkan oleh Pihak Kedua paling lambat 7 (tujuh) hari setelah tagihan diterima, dan pembayaran dapat dilakukan ke rekening PIHAK KEDUA yang tertera pada lembaran tagihan.</td></tr>
<tr><td ><b> - </b></td><td style="width:480px;text-align: justify;font-size:11px">Apabila pembayaran tidak diterima sampai dengan waktu yang ditentukan, maka Pihak Kedua berhak untuk melakukan penghentian layanan sementara sampai Pihak pertama melakukan pelunasan kewajibannya.</td></tr>
<tr><td ><b> - </b></td><td style="width:480px;text-align: justify;font-size:11px">Semua Biaya yang disebutkan dalam Perjanjian ini belum termasuk PPN.</td></tr>
<tr><td ><b> - </b></td><td style="width:480px;text-align: justify;font-size:11px">Para Pihak wajib melakukan pembayaran dan pelaporan pajak sesuai dengan ketentuan dan peraturan pergajakan yang berlaku di Indonesia dan segala denda yang muncul dikarenakan kelalaian dan kesalahan pembayaran atau pelaporan akan menjadi tanggung jawab dan kewajiban dari masing-masing pihak.</td></tr>
</table>
</td>
</tr>
<tr>
<td style="border:1px solid black;width:160px;text-align:center;font-weight:bold">Pasal 5<br>HAK DAN<br>KEWAJIBAN PIHAK PERTAMA</td>
<td style="border:1px solid black;width:520px">
<table cellpadding="">
<tr><td  style="width:20px;"><b> - </b></td><td style="width:480px;text-align: justify;font-size:11px">Pihak Pertama berhak mendapatkan pelayanan jasa sebagaimana tercantum pada Tabel Biaya dan Jasa pada perjanjian ini.</td></tr>
<tr><td ><b> - </b></td><td style="width:480px;text-align: justify;font-size:11px">Pihak Pertama membayar Biaya Jasa Pengangkutan kepada Pihak Kedua dimuka pada saat tanggal awal perjanjian dan pembayaran dilakukan tepat waktu.</td></tr>
<tr><td ><b> - </b></td><td style="width:480px;text-align: justify;font-size:11px">Pihak Pertama menyediakan 1 (satu) tempat penyimpanan sementara ("TPS") Limbah B3  yang dapat dilalui oleh armada pengangkut milik Pihak Kedua.</td></tr>
<tr><td ><b> - </b></td><td style="width:480px;text-align: justify;font-size:11px">Pihak Pertama menunjuk wakilnya yang akan mendampingi Pihak Kedua pada saat pengangkutan berlangsung sesuai jadwal yang telah ditentukan.</td></tr>
<tr><td ><b> - </b></td><td style="width:480px;text-align: justify;font-size:11px">Pihak Pertama wajib melakukan semua tindakan pencegahan dan keamanan berkaitan dengan penanganan, pemilahan, dan penyimpanan/pengumpulan Limbah B3 sebelum dan hingga waktu pengangkutan oleh Pihak Kedua.</td></tr>
<tr><td ><b> - </b></td><td style="width:480px;text-align: justify;font-size:11px">Pihak Pertama menjamin bahwa Limbah B3 harus terpilah dengan baik dan tersimpan dalam wadah / kantong berkode warna sesuai prinsip dasar pengemasan limbah B3.</td></tr>
<tr><td ><b> - </b></td><td style="width:480px;text-align: justify;font-size:11px">Pihak Pertama harus memisahkan Limbah B3 dengan bahan-bahan limbah lainnya yang tidak sesuai dengan spesifikasi pemilahan dan kategori Limbah B3 dalam kondisi siap angkut.</td></tr>
<tr><td ><b> - </b></td><td style="width:480px;text-align: justify;font-size:11px">Pihak Pertama memberikan label dan simbol beserta karakteristik dari limbah B3, dan dikemas rapi dan aman.</td></tr>
<tr><td ><b> - </b></td><td style="width:480px;text-align: justify;font-size:11px">Pihak Pertama wajib memastikan bahwa limbah yang ada didalamnya sesuai dengan label dan jenis limbah yang akan dikirim.</td></tr>
<tr><td ><b> - </b></td><td style="width:480px;text-align: justify;font-size:11px">Apabila limbah didalam kemasan tidak sesuai dengan label dan jenis karakteristiknya, maka segala biaya pengembalian limbah dari Pihak Pengumpul dan atau Pengolah menjadi tanggung jawab Pihak Pertama.</td></tr>
<tr><td ><b> - </b></td><td style="width:480px;text-align: justify;font-size:11px">Pihak Pertama membuat akun Festronik dan mengisi atau memperbaiki data Dokumen Limbah B3 melalui akun Festronik sesuai dengan berat aktual yang telah dilakukan penimbangan.</td></tr>
</table>
</td>
</tr>
<tr>
<td style="border:1px solid black;width:160px;text-align:center;font-weight:bold">Pasal 6<br>HAK DAN<br>KEWAJIBAN PIHAK KEDUA</td>
<td style="border:1px solid black;width:520px">
<table cellpadding="">
<tr><td style="width:20px;"><b> - </b></td><td style="width:480px;text-align: justify;font-size:11px">Pihak Kedua berhak menerima pembayaran dari Pihak Pertama dengan kesepakatan yang telah disetujui dalam Tabel Biaya dan Jasa.</td></tr>
<tr><td ><b> - </b></td><td style="width:480px;text-align: justify;font-size:11px">Pihak Kedua Kedua berhak menolak pengangkutan Limbah B3 apabila Pihak Pertama tidak memenuhi ketentuan yang telah disepakati.</td></tr>
<tr><td ><b> - </b></td><td style="width:480px;text-align: justify;font-size:11px">Pihak Kedua melaksanakan setiap pekerjaan sesuai dengan syarat dan ketentuan dalam Perjanjian ini.</td></tr>
<tr><td ><b> - </b></td><td style="width:480px;text-align: justify;font-size:11px">Pihak Kedua berhak menentukan jadwal pengangkutan Limbah B3 sesuai dengan frekuensi layanan.</td></tr>
<tr><td ><b> - </b></td><td style="width:480px;text-align: justify;font-size:11px">Pihak Kedua wajib melakukan pengambilan Limbah B3 hanya di TPS yang disediakan oleh Pihak Pertama.</td></tr>
<tr><td ><b> - </b></td><td style="width:480px;text-align: justify;font-size:11px">Sesuai dengan prosedur Keselamatan dan Kesehatan Kerja, Pihak Kedua tidak akan pernah membuka kemasan Limbah B3 yang diserahterimakan dari Pihak Pertama.</td></tr>
<tr><td ><b> - </b></td><td style="width:480px;text-align: justify;font-size:11px">Pihak Kedua wajib menimbang Limbah B3 dengan menggunakan timbangan Pihak Kedua dengan didampingi oleh Pihak Pertama, kemudian Dokumen Limbah B3 ditandatangani oleh wakil-wakil Para Pihak.</td></tr>
<tr><td ><b> - </b></td><td style="width:480px;text-align: justify;font-size:11px">Pihak Kedua wajib melakukan pengangkutan dari TPS ke tempat Pengolahan Limbah B3.</td></tr>
<tr><td ><b> - </b></td><td style="width:480px;text-align: justify;font-size:11px">Pihak Kedua wajib menyerahkan dokumen Limbah B3  dari pengolah limbah B3 kepada Pihak Pertama.</td></tr>
</table>
</td>
</tr>  

<tr>
<td style="border:1px solid black;width:160px;text-align:center;font-weight:bold">Pasal 7 <br>KETENTUAN LAIN</td>
<td style="border:1px solid black;width:520px">
<table cellpadding="">
<tr><td style="width:20px;"><b> - </b></td><td style="width:480px;text-align: justify;font-size:11px">Syarat Ketentuan yang terdapat dalam lampiran 1, merupakan bagian yang tidak terpisahkan dari Perjanjian Kerjasama Pengangkutan Limbah Bahan Berbahaya dan Beracutn (B3) ini.</td></tr>
<tr><td ><b> - </b></td><td style="width:480px;text-align: justify;font-size:11px">Para Pihak sepakat bahwa segala bentuk permasalahan yang timbul atas pelaksaan Perjanjian ini akan diselesaikan dengan cara musyawarah.</td></tr>
<tr><td ><b> - </b></td><td style="width:480px;text-align: justify;font-size:11px">Apabila dalam waktu 30 (tiga puluh) hari tidak tercapai perdamaian, maka Para Pihak setuju untuk menyelesaikan melalui arbitarase menurut prosedur Badan Arbitrase Nasional Indonesia (BANI) oleh Arbiter yang ditunjuk menurut peraturan tersebut.</td></tr>
<tr><td ><b> - </b></td><td style="width:480px;text-align: justify;font-size:11px">Para Pihak setuju untuk mengesampingkan berlakunya Psal 1266 dan 1267 dari Kitab Undang-undang Hukum Perdata sehubungan dengan pengakhiran Perjanjian ini.</td></tr>
<tr><td ><b> - </b></td><td style="width:480px;text-align: justify;font-size:11px">Para Pihak wajib menjaga dan dilarang menyebarkan dokumentasi seperti foto, gambar, tulisan, rekaman dan dokumen-dokumen lainnya milik masing-masing tanpa persetujuan tertulis dari Pihak lain, baik saat berjalan maupun setelah berakhirnya perjanjian ini yang dapat berakibat pada tuntutan hukum di kemudia hari.</td></tr>
<tr><td ><b> - </b></td><td style="width:480px;text-align: justify;font-size:11px">Perjanjian ini tunduk kepada ketentuan-ketentuan hukum yang berlaku di wilayah negara Republik Indonesia.</td></tr>
<tr><td ><b> - </b></td><td style="width:480px;text-align: justify;font-size:11px">Perjanjian ini bukan merupakan atau tidak dapat dipakai sebagai Bukti Ketaatan Pengelolaan Limbah B3 (compliance). Bukti ketaaatan harus menggunakan dokumen Limbah B3 (manifest) sesuai dengan jumlah Limbah B3 yang dihasilkan menurut Undang-Undang dan Peraturan yang berlaku di Indonesia.</td></tr>
</table></td>
</tr>
</table> 
';
    
$html .= '</table>' ;
     
return $html;
};

$contractsContent2 = function ($dataset){ 
$obj = new DisposalContract(); 
$customer = new Customer(); 
$employee = new Employee(); 
$setting = new Setting(); 

    
$rs = $dataset['rs'];
$rsEmployee = $employee->searchData('', '', true, ' and ' . $employee->tableName . '.pkey = ' . $obj->oDbCon->paramString($rs[0]['saleskey']));
$rsCustomer = $customer->getDataRowById($rs[0]['customerkey']);


$profileImg = $obj->loadSetting('companyLogo'); 
$logo = (isset($_GET['logo']) && $_GET['logo'] == 0) ? '' : '<img src="'.$obj->phpThumbURLSrc.'setting/companyLogo/'.$profileImg.'" style="height:50px">';
$companyName = strtoupper($setting->loadSetting('companyName'));

$html = $obj->printSetting['defaultStyle'];

$html .= '
<style>  
    .table-contract{ width:670px;} 
</style>
';
       
    
$html .= '<div style="clear:both"></div>

<table cellpadding="8" class="table-contract " style="width:940px" >

<tr>
<td style="border:1px solid black;width:160px;text-align:center;font-weight:bold">Pasal 8 <br>PEMBERITAHUAN / KORESPONDENSI</td>
<td style="border:1px solid black;width:520px">
Setiap pemberitahuan dalam Perjanjian ini wajib dibuat secara tertulis dan dikirimkan kepada Para Pihak sebagai berikut :
<br><br>
<table cellpadding="4">
<tr>
<td style="width:20px;font-size:10px"><b>A.</b></td><td style="width:490px;text-align: justify;">
<b>PIHAK PERTAMA</b><br>

<table cellpadding="0" style="width:475px;">
<tr>
<td style="width:5px;"></td><td style="width:70px">Nama</td><td style="width:10px">:</td><td style="width:390px">'.ucwords($rs[0]['correspondentname']).'</td></tr>
<tr><td ></td><td >Jabatan</td><td >:</td><td >'.$rs[0]['correspondentjobposition'].'</td></tr>
<tr><td ></td><td >Alamat</td><td >:</td><td >'.str_replace(chr(13),'<br>',$rs[0]['correspondentaddress']).'</td></tr>
<tr><td ></td><td >Telp</td><td >:</td><td >'.$rs[0]['correspondentphone'].'</td></tr>
<tr><td ></td><td >Email</td><td >:</td><td >'.$rs[0]['correspondentemail'].'</td></tr>
</table>

</td>
</tr>
<tr>
<td style="width:20px;font-size:10px"><b>B.</b></td>
<td style="width:490px;text-align: justify;">
<b>PIHAK KEDUA</b><br>
<table cellpadding="0" style="width:475px;">
<tr><td  style="width:5px;" ></td><td style="width:70px">Nama</td><td style="width:10px" >:</td><td style="width:390px">'.ucwords($rsEmployee[0]['name']).'</td></tr>
<tr><td ></td><td >Jabatan</td><td >:</td><td >'.$rsEmployee[0]['categoryname'].'</td></tr>
<tr><td ></td><td >Alamat</td><td >:</td><td >'.$rsEmployee[0]['livingaddress1'].'<br>'.$rsEmployee[0]['livingaddress2'].'</td></tr>
<tr><td ></td><td >Telp</td><td >:</td><td >'.$rsEmployee[0]['phone'].'</td></tr>
<tr><td ></td><td >Email</td><td >:</td><td >'.$rsEmployee[0]['email'].'</td></tr>
</table>    
</td></tr>
</table>
</td>
</tr>

</table>
<div style="clear:both"></div>

<table>
<tr>
<td style="width:680px;text-align:left">
Demikianlah Perjanjian Kerjasama Pengangkutan Limbah Bahan Berbahaya dan Beracun (B3) Medis ini ditandatangani oleh Para Pihak dibuat 2 ( dua ) rangkap dan bermaterai cukup, untuk dapat disepakati bersama oleh Para Pihak.</td>
</tr>
</table>


<div style="clear:both"></div>
<div style="clear:both"></div>


';   
    
$arrSignLabel = array(); 
array_push($arrSignLabel, array('Pihak Pertama,',$rsCustomer[0]['name'],$rs[0]['pic'],$rs[0]['jobposition']));
array_push($arrSignLabel, array('Pihak Kedua,',$companyName,'OKTO MEGA NAPITUPULU','DIREKTUR UTAMA'));

$html .=' 
    <table cellpadding="4" class="sign" >
    <tr>'; 
 
for ($i=0;$i<count($arrSignLabel);$i++){ 
    $html .='<td  class="" style="width:320px;text-align:left;"><strong>'.$arrSignLabel[$i][0].'</strong></td>';
    if ($i <> count($arrSignLabel) - 1)
        $html .= '<td class="sign-col-space"></td>';
}
    
$html .='</tr> '; 
    
$html .= '<tr>';
for ($i=0;$i<count($arrSignLabel);$i++){
        $arrSignLabel[$i][1] = (isset($arrSignLabel[$i][1])) ? $arrSignLabel[$i][1] : '';
        $html .='<td style="text-align:left;height:130px;"><strong>'.$arrSignLabel[$i][1].'</strong></td>'; 
        if ($i <> count($arrSignLabel) - 1)
            $html .= '<td class="sign-col-space"></td>';
}
$html .='</tr> ';     
    
    
$html .= '<tr>';
for ($i=0;$i<count($arrSignLabel);$i++){
    $arrSignLabel[$i][2] = (isset($arrSignLabel[$i][2])) ? $arrSignLabel[$i][2] : '';
    $html .='<td style="text-align:left; border-bottom:1px solid #000; font-weight:bold">Nama : '.$arrSignLabel[$i][2].'</td>';
    if ($i <> count($arrSignLabel) - 1)
            $html .= '<td class="sign-col-space"></td>';
}
$html .='</tr>';
    

$html .= '<tr>';
for ($i=0;$i<count($arrSignLabel);$i++){
    $arrSignLabel[$i][3] = (isset($arrSignLabel[$i][3])) ? $arrSignLabel[$i][3] : '';
    $html .='<td style="text-align:left;">Jabatan : '.$arrSignLabel[$i][3].'</td>';
    if ($i <> count($arrSignLabel) - 1)
            $html .= '<td class="sign-col-space"></td>';
}
$html .='</tr>';
    
    
$html .= '</table>' ;
    
    
 
return $html;
};

$terms1 = function ($dataset){
     
$rs = $dataset['rs'];
    
$html = ' 
<style>  
    .table-contract{ width:670px;} 
    .main-table-col {width:335px;text-align:justify; font-size:0.9em}
    .agreement-col {width:331px; text-align:justify; }
    .agreement-col .bullets {width: 20px; text-align:right}
    .agreement-col .agreement {width:311px;}
</style> 

<div style="font-weight:bold">LAMPIRAN 1 - SYARAT DAN KETENTUAN<br>PERJANJIAN KERJASAMA PENGELOLAAN LIMBAH B3 NO. '.$rs[0]['code'].'</div> 
<div style="clear:both"></div>
<table class="table-contract">
<tr>
<td class="main-table-col">
<!-- kolom kiri -->
<span style="font-weight:bold">A. ISTILAH</span><br>
Dalam Perjanjian ini, kecuali konteksnya menentukan Iain, kata-kata dan pengertian-pengertian berikut ini memiliki arti sebagai berikut:
<br>
<table class="agreement-col">
<tr>
<td class="bullets">a.</td>
<td class="agreement"><b>"Pengelolaan Limbah B3"</b> adalah kegiatan yang meliputi pengurangan, penyimpanan, pengumpulan, pengangkutan, pemanfaatan, pengolahan, dan/atau penimbunan.</td>
</tr>

<tr>
<td>b.</td>
<td ><b>"Jasa"</b> adalah semua pelayanan yang meliputi Pengangkutan, Penimbangan, Pencatatan dan Pengumpulan/Pengolahan/Pemusnahan/Pemanfaatan, yang diberikan oleh Pihak Kedua kepada Pihak Pertama selama masa berlaku Perjanjian</td>
</tr>
<tr>
<td>c.</td>
<td ><b>"Layanan"</b> adalah kedatangan alat angkut ke lokasi layanan atau lokasi Pihak Pertama sesuai yang sudah ditentukan.</td>
</tr>
<tr>
<td>d.</td>
<td ><b>"Limbah Bahan Berbahaya dan Beracun"</b> yang selanjutnya disebut Limbah B3 adalah sisa suatu usaha dan/atau kegiatan yang mengandung B3.</td>
</tr>
<tr>
<td>e.</td>
<td ><b>"Limbah B3"</b> adalah sisa suatu usaha dan/atau kegiatan yang mengandung bahan Berbahaya dan beracun.</td>
</tr>
<tr>
<td>f.</td>
<td ><b>"Pengolah Limbah B3"</b> adalah satu atau lebih badan usaha yang diijinkan mengoperasikan fasilitas pengolahan atau pemusnahan Limbah B3 oleh Kementerian Lingkungan Hidup dan Kehutanan.</td>
</tr>
<tr>
<td>g.</td>
<td><b>"Penyimpanan, Pengumpulan, Pemanfaatan dan/atau Penimbunan Limbah B3"</b> adalah satu atau lebih badan usaha yang diizinkan melakukan penyimpanan, pengumpulan, pemanfaatan dan/atau penimbunan Limbah B3
</td>
</tr>
<tr>
<td>h.</td>
<td><b>"Pengangkut Limbah B3"</b> adalah satu atau lebih badan usaha yang diizinkan melakukan kegiatan pengangkutan Limbah B3.</td>
</tr>
<tr>
<td>i.</td>
<td ><b>"Berita Acara Ketiadaan Limbah B3"</b> adalah bukti tertulis mengenai ketidakadaan timbulan Limbah B3 atau LIMBAH NIHIL pada saat dilakukan pengangkutan oleh Pihak Kedua.</td>
</tr>
<tr>
<td>j.</td>
<td><b>"Dokumen Limbah B3"</b> adalah bukti tertulis yang disetujui Para Pihak, baik berupa dokumen hasil cetakan maupun elektronik yang memuat pernyataan serah terima dan informasi mengenai Limbah B3 dalam bentuk Manifest maupun Nota Pengangkutan.</td>
</tr>
</table>   
<br><br>
<span style="font-weight:bold">B. PENGELOLAAN LIMBAH B3</span><br>
<table class="agreement-col">
<tr>
<td class="bullets" >1.</td>
<td class="agreement" >Pihak Kedua adalah perusahan yang telah memiliki Izin untuk mengangkut Limbah B3 yaitu Rekomendasi Pengangkutan Limbah Bahan Berbahaya dan Beracun yang diterbitkan oleh Kementerian Lingkungan Hidup dan Kehutanan Republik Indonesia, Izin Penyelenggaraan Angkutan Barang Khusus Untuk Mengangkut Barang Berbahaya (B3) dan Kartu Pengawasan Izin Penyelenggaraan Angkutan Barang Berbahaya (B3) yang diterbitkan oleh Kementerian Perhubungan.</td>
</tr>
<tr >
<td>2.</td>
<td>Pihak Kedua memiliki Perjanjian Kerjasama Kemitraan dengan Pengumpul, Pengolah, Pemanfaat dan Penimbun Limbah B3 dalam kaitannya dengan rangkaian Pengelolaan Limbah B3 yang telah mendapat Izin dari Kementerian Lingkungan Hidup dan Kehutanan Republik Indonesia dan Izin lainnya yang dikeluarkan oleh Pemerintah / Kementerian / Instansi / Lembaga / Dinas Republik Indonesia yang berwenang baik yang telah bekerja sama dengan Pihak Kedua saat Perjanjian ini berlaku maupun Pengelola limbah B3 lain yang akan bekerja sama dengan Pihak Kedua diwaktu yang akan datang.</td>
</tr> 
<tr >
<td>3.</td>
<td>Pihak Kedua akan mengangkut, mengumpulkan, mengolah, menimbun dan/atau memanfaatkan Limbah B3 milik Pihak Pertama dengan menggunakan Armada Pengangkut milik Pihak Kedua atau Armada Pengangkut Iain yang ditunjuk Pihak Kedua ke fasilitas Pengumpulan, Pengolahan, Penimbunan dan/atau Pemanfaatan Limbah B3.</td>
</tr>
<tr >
<td>4.</td>
<td>Apabila perjanjian kemitraan di atas berakhir sebelum periode Perjanjian ini berakhir, Para Pihak sepakat bahwa perjanjian ini akan mengikuti perpanjangan perjanjian kemitraan.</td>
</tr>
</table>
</td> 
<td>
<!-- kolom kanan -->
<span style="font-weight:bold">C. BIAYA JASA DAN PAJAK</span><br>
<table  class="agreement-col"> 
   <tr >
    <td  class="bullets">1.</td>
    <td  class="agreement">Pihak Kedua dapat melakukan penyesuaian Biaya Jasa dengan pemberitahuan tertulis paling lambat 30 (tiga puluh) hari sebelum berlakunya biaya tersebut sehubungan dengan terjadinya perubahan-perubahan biaya yang terjadi sebagai akibat dari biaya pengolahan limbah B3, tekanan inflasi, perubahan kebijakan upah minimum regional, dan bahan bakar minyak.</td>
    </tr>
    <tr >
    <td >2.</td>
    <td  >Pihak Kedua dapat menghentikan pelayanan sementara, apabila Pihak Pertama terlambat dalam pembayaran jasa melebihi 30 (tiga puluh) hari kalender sejak tanggal tagihan diterbitkan.</td>
    </tr>
    <tr >
    <td >3.</td>
    <td  >Bila Pihak Pertama belum melakukan pembayaran Biaya Jasa hingga melebihi 60 (enam puluh) hari kalender sejak tanggal tagihan, maka Pihak Kedua berhak mengakhiri Perjanjian dan Pihak Pertama wajib menyelesaikan biaya jasa yang tertunda.</td>
    </tr>
    <tr >
    <td >4.</td>
    <td  >Semua Biaya yang disebutkan dalam Perjanjian ini belum termasuk Pajak Pertambahan Nilai (PPN).</td>
    </tr>
    <tr >
    <td >5.</td>
    <td  >Para Pihak sepakat bahwa pajak yang timbul akibat dari pelaksanaan Perjanjian akan ditanggung oleh masing-masing Pihak sesuai dengan peraturan berlaku.</td>
    </tr>
</table>
<br><br>
<span style="font-weight:bold">D. PROSEDUR TANGGAP DARURAT</span><br>
<table  class="agreement-col"> 
  <tr >
    <td class="bullets">1.</td>
    <td class="agreement" >Bilamana terjadi sesuatu di fasilitas Pengolah Limbah B3 berupa malfungsi, kerusakan, perbaikan alat, dan segala hal yang menyebabkan terhentinya proses pengolahan dan/atau pemusnahan Limbah B3, maka Para Pihak setuju dan sepakat bahwa Limbah B3 tersebut untuk jangka waktu tertentu dapat diolah di fasilitas Pengolah Limbah B3 berijin lainnya yang sudah terikat perjanjian kerjacama kemitraan dengan Pihak Kedua.</td>
    </tr>
    <tr >
    <td >2.</td>
    <td  >Dalam hal Pengolah Limbah B3 berizin lain yang terikat perjanjian kerja sama kemitraan dengan Pihak Kedua tidak dapat mengolah dengan alasan sesuai ayat 1 diatas, maka Pihak Kedua akan mencari alternatif pengolah berizin yang Iain.</td>
    </tr>
    <tr >
    <td >3.</td>
    <td  >Apabila prosedur tanggap darurat sebagaimana ayat 1 dan 2 di atas tidak dapat dilaksanakan. maka Pihak Kedua berhak melakukan penghentian pelayanan sementara pengangkutan Limbah B3 dengan pemberitahuan tertulis paling lambat 7(tujuh) hari setelah prosedur tanggap darurat sebagaimana ayat 1 dan 2 tidak dapat dilakukan.</td>
    </tr>
</table> 
</td>

</tr>
</table>
';
     
$html .= '
<div style="clear:both"></div>
<div style="clear:both"></div>
<div style="clear:both"></div>
<div style="clear:both"></div>
<table  class="table-contract">
<tr>
<td class="main-table-col">
<!-- kolom kiri -->
<span style="font-weight:bold">E. BERITA ACARA KETIADAAN LIMBAH B3 ("BAKL")</span><br>
Bilamana tidak terdapat timbulan Limbah B3 yang karena hal berikut: (1) fasilitas TPS Limbah B3 Pihak Pertama tutup atau pindah lokasi atau belum beroperasi; (2) tidak ada petugas mewakili Pihak Pertama atau; (3) Pihak Pertama tidak menghasilkan Limbah B3 (nihil), maka suatu BAKL akan diterbitkan oleh Pihak Kedua dan dinyatakan berlaku oleh Para Pihak dengan dengan atau tanpa tanda tangan dari wakil Pihak Pertama. BAKL akan menjadi bukti bahwa Pihak Kedua telah melakukan pelayanan yang sesuai dengan jadwal yang telah disepakati.
<br><br>
<span style="font-weight:bold">F. PERNYATAAN DAN JAMINAN</span><br>
<table  class="agreement-col"> 
<tr >
<td class="bullets">1.</td>
<td class="agreement">Pihak Pertama menjamin bahwa Limbah B3 yang diserahterimakan kepada Pihak Kedua sesuai dengan Peraturan Pemerintah No. 22 Tahun 2021 tentang Penyelenggaraan Perlindungan dan Pengelolaan Lingkungan Hidup, Peraturan Menteri Kesehatan No. 27 Tahun 2017 tentang Pedoman Pencegahan dan Pengendalian Infaksi di Fasilitas Pelayanan Kesehatan dan Keputusan Menteri Kesehatan R.I. No. 7 Tahun 2019 tentang Kesehatan Lingkungan Rumah Sakit.</td>
</tr>
<tr >
<td>2.</td>
<td >Pihak Pertama menjamin bahwa Limbah B3 yang diserahterimakan kepada Pihak Kedua tidak tersangkut permasalahan hukum dan/atau tuntutan dari pihak manapun dan Pihak Pertama membebaskan Pihak Kedua dari segala tuntutan atau gugatan hukum apapun baik Perdata maupun Pidana dari pihak manapun apabila Limbah B3 yang diserahterimakan kepada Pihak Kedua tersangkut permasalahan hukum dan/atau tuntutan.</td>
</tr>
<tr >
<td>3.</td>
<td >Pihak Kedua menjamin bahwa Limbah B3 yang diserahterimakan dari Pihak Pertama akan di kelola dengan baikdan sesuai dengan perizinan yang dimiliki oleh Pihak Kedua maupun Mitra Pihak Kedua dan sesuai dengan ketentuan atau peraturan yang berlaku di Indonesia dan membebaskan Pihak Pertama dari segala tuntutan atau gugatan hukum apapun baik Perdata maupun Pidana dari pihak manapun apabila Limbah B3yang diserahterimakan dari Pihak Pertama tidak di kelola denganbaik oleh Pihak Kedua dan/atau Mitra Pihak Kedua.</td>
</tr>
</table> 

<br><br>
<span style="font-weight:bold">G. FORCE MAJEURE</span><br>
<table  class="agreement-col"> 
<tr >
<td class="bullets">1.</td>
<td class="agreement">
Yang dimaksud Force Majeure dalam Perjanjian ini adalah keadaan diluar kemampuan dari Para Pihak atau salah satu Pihak yang mengakibatkan Para Pihak atau salah satu Pihak tidak dapat menjalankan dan/atau melaksanakan kewajibannya sebagaimana dalam Perjanjian ini antara lain sebagai berikut:
<br>
<table>
<tr> 
<td style="width:20px;">a.</td>
<td style="width:275px;">Keadaan yang terjadi karena bencana alam, seperti gempa bumi, banjir, gunung meletus, kebakaran, dan tanah longsor. </td>
</tr>
<tr> 
<td>b.</td>
<td>Perang, terorisme, pemberontakan. huru-hara, pemogokan. wabah penyakit, blockade, dan sabotase.</td>
</tr>
<tr> 
<td>c.</td>
<td>Tindakan, kebijakan dan Peraturan Pemerintah Republik Indonesia.</td>
</tr>
</table>
</td>
</tr>
<tr>
<td>2.</td>
<td>Pihak yang tidak dapat menjalankan dan/atau melaksanakan kewajibannya dikarenakan kondisi dan/atau keadaan sebagaimana ayat (1) di atas, wajib memberitahukan secara tertulis dalam waktu paling lambat 14 (empat belas) hari kerja sejak terjadinya hal tersebut.</td>
</tr>
<tr>
<td>3.</td>
<td>Segala kerugian yang timbul sehubungan dengan <i>force majeure</i> menjadi tanggung jawab masing-masing Pihak.</td>
</tr>
</table>

<br><br>
<span style="font-weight:bold">H. PEMBATALAN DAN/ATAU PENGAKHIRAN</span><br>
<table  class="agreement-col">
<tr>
<td class="bullets">1.</td>
<td class="agreement">Pembatalan dan/atau Pengakhiran Perjanjian hanya bisa dilakukan apabila salah satu Pihak tidak dapat memenuhi kewajibannya sebagaimana diatur dalam Perjanjian ini.</td>
</tr>
</table> 

</td>
<td class="main-table-col">
<!-- kolom kanan -->
<table  class="agreement-col">
<tr>
<td class="bullets">2.</td>
<td class="agreement">Pembatalan dan/atau Pengakhiran Perjanjian sebagaimana ayat (1) diatas, wajib diberitahukan secara tertulis sekurang- kurangnya 30 (tiga puluh) hari kalender oleh salah satu Pihak</td>
</tr> 
<tr>
<td>3.</td>
<td >Para Pihak sepakat mengesampingkan ketentuan Pasal 1266 dari Kitab Undang-Undang Hukum Perdata sepanjang mengenai Pembatalan dan/atau Pengakhiran Perjanjian diperlukannya putusan Pengadilan.</td>
</tr>
</table>

<br><br>
<span style="font-weight:bold">I. KERAHASIAAN</span><br>
Para Pihak wajib menjaga dan dilarang menyebarkan dokumentasi seperti foto, gambar, tulisan, rekaman dan dokumen-dokumen lainnya baik cetak maupun elektronik milik masing-masing Pihak tanpa persetujuan tertulis dari Pihak Iain, baik saat berjalan maupun setelah berakhimya Perjanjian ini yang dapat berakibat pada tuntutan hukum di kemudian hari.

<br><br>
<span style="font-weight:bold">J. HUKUM YANG BERLAKU</span><br>
Perjanjian berlaku dan tunduk pada Hukum Negara Republik Indonesia.


<br><br>
<span style="font-weight:bold">K. PENYELESAIAN PERSELISIHAN</span><br>
<table  class="agreement-col">
<tr >
<td class="bullets">1.</td>
<td class="agreement">Para Pihak sepakat bahwa segala bentuk perrnasalahan yang timbul atas pelaksanaan Perjanjian ini akan diselesaikan dengan cara musyawarah.</td>
</tr>
<tr >
<td>2.</td>
<td>Apabila dalam waktu 30 (tiga puluh) hari setelah musyawarah tidak tercapai kesepakatan damai, maka Para Pihak sepakat untuk menyelesaikannya melalui Badan Arbitrase Nasional Indonesia (BANI).</td>
</tr>
</table>

<br><br>
<span style="font-weight:bold">L. LAIN-LAIN</span><br>
<table  class="agreement-col">
<tr >
<td class="bullets" >1.</td>
<td  class="agreement">Apabila satu atau lebih dari ketentuan yang tercantum dalam Perjanjian dan/atau Syarat dan Ketentuan ini tidak berlaku, tidak sah, atau tidak dapat dilaksanakan, maka keberlakuan, keabsahan, atau penerapan ketentuan Iain dari Perjanjian dan/atau Syarat dan Ketentuan ini tidak akan terpengaruh atau berkurang maknanya.</td>
</tr>
<tr >
<td >2.</td>
<td  >Apabila Pihak Pertama tidak mengimplementasikan Festronik sampai dengan berakhimya Perjanjian sebagaimana Pasal 2 Perjanjian ini, maka Pihak Kedua berhak untuk tidak melakukan pelayanan Jasa, dan segala biaya yang sudah diterima Pihak Kedua tidak dapat dikembalikan.</td>
</tr>
<tr >
<td >3.</td>
<td  >Tanpa adanya meterai di dalam Perjanjian ini tidak mengurangi dan/atau mempengaruhi keabsahan dari Perjanjian ini karena syarat sah suatu Perjanjian berdasarkan Pasal 1320 Kitab Undang-Undang Hukum Perdata terpenuhi.</td>
</tr>
<tr >
<td >4.</td>
<td  >Perjanjian ini merupakan seluruh perjanjian dan kesepakatan Para Pihak dan menggantikan seluruh perjanjian secara verbal maupun tertulis, janji-janji atau kesepakatan-kesepakatan lainnya sehubungan dengan hal-hal yang diatur dalam Perjanjian ini. Tidak ada Pihak yang dapat menyatakan suatu perjanjian atau kesepakatan berlaku, yang tidak dinyatakan dalam Perjanjian ini.</td>
</tr>
<tr >
<td >5.</td>
<td  >Segala bentuk perubahan, penambahan, pergantian dan hal-hal Iain yang belum diatur atau belum cukup diatur dalam Perjanjian ini akan diatur kemudian secara tertulis di dalam suatu perjanjian tambahan addendum/amandemen atau dokumen tambahan lainnya yang disetujui dan ditandatangani oleh Para Pihak serta merupakan satu kesatuan dan bagian yang tidak terpisahkan dari Perjanjian ini.</td>
</tr>
</table>

<br><br>
<span style="font-weight:bold">M. KHUSUS</span><br>
Perjanjian ini bukan merupakan Bukti Ketaatan Pengelolaan Limbah B3 (<i>compliance</i>). Bukti ketaatan harus menggunakan Dokumen Limbah B3 (Festronik) sesuai dengan jumlah Limbah B3 yang dihasilkan menurut hukum yang berlaku.

</td>
</tr>
</table>
';
    
return $html;
} ;


$attachment = function ($dataset){
     
$rs = $dataset['rs'];
$obj = new DisposalContract(); 
$customer = new Customer(); 
$service = new Service(); 
$setting = new Setting(); 
$rsService = $service->getDataRowById($rs[0]['servicekey']);
$rsWasteDetail = $obj->getWasteDetail($rs[0]['pkey']);
$rsCustomer = $customer->getDataRowById($rs[0]['customerkey']);
    
$html = '  
<table class="table-contract " style="text-align:center;font-weight:bold">
<tr><td class="text-title" style="font-size:16px;" >LAMPIRAN 2  </td></tr>
<tr><td class="text-subtitle" style="font-size:16px;" >TABEL JASA LAYANAN PENGAMBILAN DAN PEMUSNAHAN</td></tr>
<tr><td class="text-subtitle" style="font-size:16px;" >LIMBAH B3 UMUM</td></tr>
</table>
<div style="clear:both"></div>
<div style="clear:both"></div>

<table cellpadding="4" style="width:680px;">
<tr>
<td style="width:226px;text-align:center;border:1px solid black;font-weight:bold;font-size:12px;background-color:#91cdff">Jasa Layanan Pengambilan Limbah</td>
<td style="width:226px;text-align:center;border:1px solid black;font-weight:bold;font-size:12px;background-color:#91cdff">Jumlah Pengambilan</td>
<td style="width:226px;text-align:center;border:1px solid black;font-weight:bold;font-size:12px;background-color:#91cdff">Nilai Paket Layanan Per Tahun (Rp)</td>
</tr>
<tr>
<td style="border:1px solid black;text-align:left;">'.$rsService[0]['name'].'</td>
<td style="border:1px solid black;text-align:center;">'.$obj->formatNumber($rs[0]['qtyservice']).' kali pengambilan</td>
<td style="border:1px solid black;text-align:center;">'.$obj->formatNumber($rs[0]['sellingprice']).'</td>
</tr>
<tr>
<td style="border:1px solid black;text-align:left;"></td>
<td style="border:1px solid black;text-align:center;"></td>
<td style="border:1px solid black;text-align:center;"></td>
</tr>
</table>

<table cellpadding="4" style="width:680px;">
<tr>
<td style="width:226px;text-align:center;border:1px solid black;font-weight:bold;font-size:12px;background-color:#91cdff">Jasa Limbah</td>
<td style="width:226px;text-align:center;border:1px solid black;font-weight:bold;font-size:12px;background-color:#91cdff">Jasa Pemusnahan/kg (Rp)</td>
<td style="width:226px;text-align:center;border:1px solid black;font-weight:bold;font-size:12px;background-color:#91cdff">Batas minimal ambil per Pengambilan</td>
</tr>
';
foreach ($rsWasteDetail as $wasteDetail) {
    $html .= '<tr><td style="border:1px solid black;text-align:left;">'.$wasteDetail['wastename'].' ('.$wasteDetail['wastecode'].')</td>
    <td style="border:1px solid black;text-align:right;">'.$obj->formatNumber($wasteDetail['weightprice'], 2).'</td>
    <td style="border:1px solid black;text-align:center;">'.$obj->formatNumber($wasteDetail['minweight'], 2).' Kg</td>
    </tr>';

}
$html .= '</table>
<div style="clear:both"></div>

<table cellpadding="4" style="width:680px;font-size:13px;">
<tr>
<td style="width:150px">Jasa </td>
<td style="width:20px">1.</td>
<td style="width:510px">Jasa Layanan Pengambilan Limbah</td>
</tr>
<tr>
<td style=""></td>
<td style="">2.</td>
<td style="  text-align: justify;">Jasa Pemusnahan Limbah dengan Minimum Pengambilan. Artinya bilamana Jumlah Limbah Timbul, Limbah yang dihasilkan tidak mencapai Jumlah Batasan Minimum Limbah maka akan tetap dikenakan sejumlah Batas Minimum Limbah tersebut.</td>
</tr>
<tr>
<td style=""></td>
<td style=""></td>
<td style=""></td>
</tr>
<tr>
<td style="">PPn</td>
<td colspan="2"style="">Semua Jasa <b>Belum</b> termasuk PPn 11%</td>
</tr>
<tr>
<td style=""></td>
<td style=""></td>
<td style=""></td>
</tr>
<tr>
<td style="">Penagihan</td>
<td style="">1.</td>
<td style="">Jasa Layanan Pengambilan Limbah ditagihkan 100 % setelah Perjanjian Kerjasama ditandatangani</td>
</tr>
<tr>
<td style=""></td>
<td style="">2.</td>
<td style="">Jasa Pemusnahan Limbah ditagihkan Pengambilan Jumlah Timbulan Limbah B3</td>
</tr>
<tr>
<td style=""></td>
<td style=""></td>
<td style=""></td>
</tr>
<tr>
<td style="">Jumlah Pelayanan</td>
<td colspan="2"style="">'.$obj->formatNumber($rs[0]['qtyservice']).'  kali / Pertahun</td>
</tr>
<tr>
<td style=""></td>
<td style=""></td>
<td style=""></td>
</tr>
<tr>
<td style="">Lokasi Pelayanan</td>
<td colspan="2"style="">'.$rsCustomer[0]['address'].'</td>
</tr>
</table>
<div style="clear:both"></div>
<div style="clear:both"></div>

<table cellpadding="4" style="width:680px;font-size:13px;">
<tr>
<td style="">Lampiran Tabel Jasa Layanan Pengambilan dan Pemusnahan Limbah B3 ini merupakan bagian yang tidak terpisahkan dari Perjanjian dengan <b>No '.$rs[0]['code'].'</b></td>
</tr>

</table>
';
    
return $html;
} ;




$generateReportContent = array();
array_push($generateReportContent , $contractsContent1);
array_push($generateReportContent , $contractsContent2);
array_push($generateReportContent , $terms1);
array_push($generateReportContent , $attachment);

?>
