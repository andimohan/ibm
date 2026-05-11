<?php

$PRINT_SETTINGS=array(
    'showPrintHeader' => false,
    'showPrintFooter' => false, 
    'marginFooter' => 6,
);
  
includeClass( array('DisposalWorkOrder.class.php'));

$disposalWorkOrder = createObjAndAddToCol( new DisposalWorkOrder()); 
$disposalWorkOrderDispatcher = createObjAndAddToCol( new DisposalWorkOrderDispatcher()); 

$obj = $disposalWorkOrder;

$arrID = array();
if (isset( $_GET['id']) && !empty( $_GET['id'])){ 
    $arrID = explode(',',$_GET['id']);
}else if (isset( $_GET['wolistkey']) && !empty( $_GET['wolistkey'])){ 
    $arrWO = explode(',',$_GET['wolistkey']);
        $rsTemp = $obj->searchData('', '', true, ' and ' . $obj->tableName . '.refkey in (' . $obj->oDbCon->paramString($_GET['wolistkey'], ',') . ') and ' . $obj->tableName . '.statuskey in (1,2,3)');
        $rsTemp = array_column($rsTemp,'pkey');
        for ($i=0;$i<count($rsTemp);$i++){ 
            array_push($arrID,$rsTemp[$i]);
        } 
}

$generateReportContent = function ($dataset){ 

$obj = new DisposalWorkOrder(); 
$customer = new Customer(); 
$employee = new Employee(); 
$setting = new Setting(); 
    
$rs = $dataset['rs'];
$rsCustomer = $customer->getDataRowById($rs[0]['customerkey']);
$age = $customer->getCustomersAge($rs[0]['customerkey']);

if(!empty($rsCustomer[0]['saleskey'])){
$rsEmployee = $employee->getDataRowById($rsCustomer[0]['saleskey']);
$dpjpname = $rsEmployee[0]['name'];
}
    
$companyPhone = $setting->getDetailByCode('companyPhone');
$companyAddress = $setting->loadSetting('companyAddress');
$companyTaxRegistrationNumber = $setting->loadSetting('companyTaxRegistrationNumber');
$arrCompanyPhone = array();  
for($i=0;$i<count($companyPhone);$i++) 
    array_push($arrCompanyPhone, $companyPhone[$i]['value']);

$companyContact = '';
if(!empty($arrCompanyPhone))
    $companyContact = implode (', ', $arrCompanyPhone);
    
$companyName = strtoupper($setting->loadSetting('companyName'));
$profileImg = $obj->loadSetting('companyLogo'); 


$trnotes = (!empty($rs[0]['trdesc'])) ? '<div style="clear:both"></div><strong>Catatan :</strong> <br>' . str_replace(chr(13),'<br>',$rs[0]['trdesc']) : '';
$img = '';// HTTP_HOST.'phpthumb/phpThumb.php?src='.$obj->phpThumbURLSrc.'setting/companyLogo/'.$profileImg.'&w=180&h=100&hash='.getPHPThumbHash($profileImg);

$arrRecipient = array();
array_push($arrRecipient, $rs[0]['recipientname'], str_replace(chr(13),'<br>',$rs[0]['recipientaddress']), $rs[0]['recipientphone']);

$html = $obj->printSetting['defaultStyle'];
$html .= '<table style="font-weight:bold;">
<tr><td style="width:470px">No. '.$rs[0]['code'].'</td><td >No.Rekom Pengangkutan :</td></tr>
</table>
<table cellpadding="4"  style="border:1px solid black;width:670px;"> 
<tr><td style="font-size:1em;font-weight:bold;text-align:center;">BAGIAN YANG HARUS DI ISI OLEH PENGANGKUT (PT. BHUMAN CATUR LESTARI)<br>(THIS SECTION MUST BE COMPLETED BT THE TRANSPORTER)</td></tr>
<tr>
    <td style="border:1px solid black">
        <table>
            <tr><td></td><td></td><td></td></tr>
            <tr><td style="width:140px;">Nama dan Alamat Penghasil</td><td style="width:10px;">:</td><td><b>'. $rsCustomer[0]['name'] .'</b><br>'.str_replace(chr(13),'<br>',$rsCustomer[0]['address']).'</td></tr>
            <tr><td></td><td></td></tr>
        </table>
    </td>
</tr>
<tr>
    <td style="width:134px;border:1px solid black">
        <table>
            <tr>
                <td style="width:12px;">A.</td>
                <td  style="width:120px;">Jenis Limbah<br><span style="font-size:0.9em;">(Physical state)</span></td>
            </tr>
            <tr><td rowspan="2" style="height:30px;"></td></tr>
        </table>
    </td>
    <td style="width:134px;border:1px solid black">
        <table>
            <tr>
                <td style="width:12px;">B.</td>
                <td style="width:120px;">Nama Teknik, bila ada : <br><span style="font-size:0.9em;">(Technical name if applicable)</span></td>
            </tr>
            <tr><td rowspan="2"></td></tr>
        </table>
    </td>
    <td style="width:134px;border:1px solid black">
        <table>
            <tr>
                <td style="width:12px;">C.</td>
                <td style="width:120px;">Karakteristik Limbah<br><span style="font-size:0.9em;">(Hazard class)</span></td>
            </tr>
        </table>
    </td>   
    <td style="width:134px;border:1px solid black">
        <table>
            <tr>
                <td style="width:12px;">D.</td>
                <td style="width:120px;">Kode Limbah B3<br><span style="font-size:0.9em;">(Hazarddous waste code)</span></td>
            </tr>
        </table>
    </td>  
    <td style="width:134px;border:1px solid black">
        <table>
            <tr>
                <td style="width:12px;">E.</td>
                <td style="width:120px;">Jumlah Limbah<br><span style="font-size:0.9em;">(Sum)</span></td>
            </tr>
        </table>
    </td>     
</tr>
<tr><td colspan="3">Pencatatan Limbah dalam BIN/Kontainer/Wadah Penampung (catatan rincian bila ada)</td><td colspan="2" style="text-align:right;">Bila Jumlah = 0 (tidak ada) langsung ke area (F)</td></tr>
</table> 

<table cellpadding="4" style="width:670px;">
<tr>
    <td style="width:67px;text-align:center;border:1px solid black">No Bin</td><td style="width:67px;text-align:center;border:1px solid black">Isi (Kg)</td>
     <td style="width:67px;text-align:center;border:1px solid black">No Bin</td><td style="width:67px;text-align:center;border:1px solid black">Isi (Kg)</td>
     <td style="width:67px;text-align:center;border:1px solid black">No Bin</td><td style="width:67px;text-align:center;border:1px solid black">Isi (Kg)</td>
     <td style="width:67px;text-align:center;border:1px solid black">No Bin</td><td style="width:67px;text-align:center;border:1px solid black">Isi (Kg)</td>
     <td style="width:67px;text-align:center;border:1px solid black">No Bin</td><td style="width:67px;text-align:center;border:1px solid black">Isi (Kg)</td>
</tr>
<tr>
    <td style="border:1px solid black;"></td><td style="border:1px solid black;"></td>
    <td style="border:1px solid black;"></td><td style="border:1px solid black;"></td>
    <td style="border:1px solid black;"></td><td style="border:1px solid black;"></td>
    <td style="border:1px solid black;"></td><td style="border:1px solid black;"></td>
    <td style="border:1px solid black;"></td><td style="border:1px solid black;"></td>
</tr>
<tr>
    <td style="border:1px solid black;"></td><td style="border:1px solid black;"></td>
    <td style="border:1px solid black;"></td><td style="border:1px solid black;"></td>
    <td style="border:1px solid black;"></td><td style="border:1px solid black;"></td>
    <td style="border:1px solid black;"></td><td style="border:1px solid black;"></td>
    <td style="border:1px solid black;"></td><td style="border:1px solid black;"></td>
</tr>
<tr>
    <td style="border:1px solid black;"></td><td style="border:1px solid black;"></td>
    <td style="border:1px solid black;"></td><td style="border:1px solid black;"></td>
    <td style="border:1px solid black;"></td><td style="border:1px solid black;"></td>
    <td style="border:1px solid black;"></td><td style="border:1px solid black;"></td>
    <td style="border:1px solid black;"></td><td style="border:1px solid black;"></td>
</tr>
<tr>
    <td style="border:1px solid black;"></td><td style="border:1px solid black;"></td>
    <td style="border:1px solid black;"></td><td style="border:1px solid black;"></td>
    <td style="border:1px solid black;"></td><td style="border:1px solid black;"></td>
    <td style="border:1px solid black;"></td><td style="border:1px solid black;"></td>
    <td style="border:1px solid black;"></td><td style="border:1px solid black;"></td>
</tr>
<tr><td colspan="10" style="border:1px solid black;">TOTAL BIN YANG DIPINJAMKAN YANG BERADA DI LOKASI PENGHASIL = _____________ UNIT</td></tr>

</table>

<table cellpadding="4" style="width:670px;">
<tr><td style="line-height:1px;border:1px solid black;"></td></tr>
<tr><td style="font-weight:bold;width:536px;border:1px solid black;">Segi pelayanan (wajib diisi oleh pelanggan atau Penghasil Limbah B3) :</td><td style="text-align:center;width:67px;border:1px solid black;">Ya</td><td style="text-align:center;width:67px;border:1px solid black;">Tidak</td></tr>
<tr><td style="border:1px solid black;">Apakah Anda sebagai Wakil dan Pihak Penghasil (Pelanggan) untuk serah terima Limbah B3 ini?</td><td style="border:1px solid black;"></td><td style="border:1px solid black;"></td></tr>
<tr><td style="border:1px solid black;">Pelanggan (Penghasil Limbah B3) menyaksikan Proses dan Penimbangan Limbah B3?</td><td style="border:1px solid black;"></td><td style="border:1px solid black;"></td></tr>
<tr><td style="border:1px solid black;">Petugas pengangkut berpakain / seragam dengan rapih?</td><td style="border:1px solid black;"></td><td style="border:1px solid black;"></td></tr>
<tr><td style="border:1px solid black;">Ketika pengambilan limbah B3, Petugas menggunakan APD (Masker, Sarung Tangan, Apron)?</td><td style="border:1px solid black;"></td><td style="border:1px solid black;"></td></tr>
<tr><td style="border:1px solid black;">Timbangan yang digunakan dalam kondisi baik ?</td><td style="border:1px solid black;"></td><td style="border:1px solid black;"></td></tr>
<tr><td style="border:1px solid black;">Jumlah Limbah B3 telah ditulis dan diperiksa dengan benar dan sama dengan tertulis di Manifest Elektronik</td><td style="border:1px solid black;"></td><td style="border:1px solid black;"></td></tr>
</table>
<table cellpadding="4" style="width:670px;">
<tr><td style="width:375px;border:1px solid black;">Tulis nomer Manifest: WW ..................................... /KLHH ....................................</td><td  style="border:1px solid black;width:295px;background-color:black;"></td></tr>
<tr>
<td style="width:140px;border:1px solid black;">
<table style="text-align:center;">
<tr><td style="height:100px;">Petugas Pengambil Limbah B3 :</td></tr>
<tr><td style="">'.$rs[0]['drivername'].'</td></tr>
<tr><td style="">Tanda tangan dan Stempel</td></tr>
</table>
</td>
<td style="width:200px;border:1px solid black;">
<table >
<tr><td style="width:14px;"></td><td style="width:172px;text-align:center;font-weight:bold"><u>Consignment Notes</u></td></tr>
<tr><td > -</td><td style="">Ini Bukan dokumen yang sah dan legal dari Kementrian Lingkungan Hidup dan Kehutanan untuk serah terima Limbah B33</td></tr>
<tr><td ></td><td></td></tr>
<tr><td > -</td><td>CN hanya berfungsi sebagai dokumen internal sekaligusBukti Penagihan dari PT.Bhuman Catur Lestari</td></tr>
<tr><td ></td><td></td></tr>
<tr><td > -</td><td>Dokumen serah terima yang legal adalah Manifes Elektronik</td></tr>
</table>
</td>
<td style="width:330px;border:1px solid black;">
<table>
<tr><td>Pelanggan (Penghasil Limbah B3)</td></tr>
<tr><td style="width:10px;">1.</td><td>Menyatakan Nota ini telah diisi lengkap dan diperiksa dengan benar</td></tr>
<tr><td style="height:90px;">2.</td><td>Menyadari / Mengetahui isi didalam kemasan yang diserahkan kepada perusahaan pengangkut adalah Limbah B3 / Medis</td></tr>
<tr><td style="width:330px;">Tanda tangan dan Stempel</td></tr>
</table>
</td>
</tr>
<tr><td colspan="2" style="border:1px solid black;">Nama (Name) :</td><td style="border:1px solid black;">Nama (Name) :</td></tr>
<tr><td  colspan="2" style="border:1px solid black;">Jabatan (Title) :</td><td style="border:1px solid black;">Jabatan (Title) :</td></tr>
<tr><td  colspan="2" style="border:1px solid black;">Tanggal (Date) :</td><td style="border:1px solid black;">Tanggal (Date) :</td></tr>
<tr><td  colspan="4" style="font-weight:bold;">Bila terjadi Ketidakadaan Limbah B3 = 0 (Nihil), maka melengkapi area (F) dibawah ini:</td></tr>
</table>

<table cellpadding="4" style="width:670px;border:1px solid black;">
<tr><td style="width:500px;font-weight:bold;">BERITA ACARA KETIDAKADAAN LIMBAH B3 (BAKL)</td><td style="width:50px">Tanggal</td><td style="width:20px;">:</td><td style="width:100px;">'. $obj->formatDBDate($rs[0]['trdate'],'d / m / y') .'</td></tr>
<tr><td ></td><td>Jam</td><td>:</td><td></td></tr>
<tr>
<td >
<table cellpadding="2">
<tr>
<td style="width:60px;line-height:5px;">Penyebab :</td>
<td style="width:10px;"><table ><tr><td style="width:5px;line-height:5px;border:1px solid black;"></td></tr></table></td><td style="width:130px;line-height:5px;">Huru-hara</td>
<td style="width:10px;"><table ><tr><td style="width:5px;line-height:5px;border:1px solid black;"></td></tr></table></td><td style="width:140px;line-height:5px;">Cuaca Buruk</td>
<td style="width:10px;"><table ><tr><td style="width:5px;line-height:5px;border:1px solid black;"></td></tr></table></td><td style="width:170px;line-height:5px;">Perbaikan Jalan / Akses tertutup</td>
<td style="width:10px;"></td><td style="width:120px;"></td></tr>
<tr>
<td></td>
<td ><table ><tr><td style="width:5px;line-height:5px;border:1px solid black;"></td></tr></table></td><td style="line-height:5px;">Kerusakan kendaraan</td>
<td style="width:10px;"><table ><tr><td style="width:5px;line-height:5px;border:1px solid black;"></td></tr></table></td><td style="line-height:5px;">Timbangan Rusak</td>
<td style="width:10px;"><table ><tr><td style="width:5px;line-height:5px;border:1px solid black;"></td></tr></table></td><td style="line-height:5px;">Muatan Penuh</td>
<td style="width:10px;"><table ><tr><td style="width:5px;line-height:5px;border:1px solid black;"></td></tr></table></td>
<td style="line-height:5px;">Alamat tidak ditemukan</td></tr>
<tr>
<td></td>
<td ><table ><tr><td style="width:5px;line-height:5px;border:1px solid black;"></td></tr></table></td><td style="line-height:5px;">Gudang Terkunci</td>
<td style="width:10px;"><table ><tr><td style="width:5px;line-height:5px;border:1px solid black;"></td></tr></table></td><td style="line-height:5px;">Lokasi Tutup</td>
<td style="width:10px;"><table ><tr><td style="width:5px;line-height:5px;border:1px solid black;"></td></tr></table></td><td style="line-height:5px;">Penghasill tidak memberikannya</td>
<td style="width:10px;"><table ><tr><td style="width:5px;line-height:5px;border:1px solid black;"></td></tr></table></td><td style="line-height:5px;">Lainnya.............................</td></tr>
</table>
</td>
</tr>
<tr>
<td>
<table cellpadding="2" style="text-align:center;">
<tr>
<td style="width:70px;"></td>
<td style="width:130px;border:1px solid black;">Mengetahui Dilaporkan</td>
<td style="width:130px;border:1px solid black;">Dilaporkan</td>
<td style="width:130px;border:1px solid black;">Diperiksa</td>
<td style="width:130px;border:1px solid black;">Diperiksa</td>
</tr>
<tr>
<td></td>
<td style="width:130px;border:1px solid black;">Penghasilan LB3 (Pelanggan)</td>
<td style="border:1px solid black;">HWC</td>
<td style="border:1px solid black;">Schedule Admin</td>
<td style="border:1px solid black;">Koordinator Logistik</td>
</tr>
<tr>
<td style="height:60px;"></td>
<td style="border:1px solid black;"></td>
<td style="border:1px solid black;"></td>
<td style="border:1px solid black;"></td>
<td style="border:1px solid black;"></td>
</tr>
<tr>
<td ></td>
<td style="border:1px solid black;"></td>
<td style="border:1px solid black;"></td>
<td style="border:1px solid black;"></td>
<td style="border:1px solid black;"></td>
</tr>
<tr>
<td style="width:10px;"></td>
<td style="width:295px">*Tanda tangan, Stempel & Nama Jelas</td><td style="width:280px">**Penyebab pilihan diberikan tanda v atau tulis untuk alasan lainnya</td>
</tr>
</table>
</td>
</tr>
</table>
<table>
<tr><td>Setelah diisi lengkap, semua Lembar Nota ini diambil oleh Petugas Pengangkut dari <b>PT.Bhuman Catur Lestari</b></td></tr>
<tr><td></td><td></td><td></td></tr>
<tr><td style="width:140px;">Lembar Ke-1 (Putih)</td><td style="width:8px;">:</td><td style="width:400px;">Untuk Bagian Keuangan PT>Bhuman Catur Lestari (sebagai Lampiran penagihan)</td></tr>
<tr><td>Lembar Ke-2 (Kuning) & 3 (Hijau)</td><td>:</td><td>Untuk Arsip PT.Bhuman Catur Lestari</td></tr>
<tr><td>Lembar Ke-4 (Biru)</td><td>:</td><td>Untuk Penghasil Limba B3 / Pelanggan</td></tr>
</table>';

return '<div style="font-size:0.8em;">'.$html.'</div>';
};
?>