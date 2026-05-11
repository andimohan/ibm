<?php 

include_once '../_config.php';  
include_once '../_include-v2.php';

?>

<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />  
<link rel="stylesheet" type="text/css" href="<?php echo $class->adminCssPath; ?>jquery-ui.min.css" />        
<script type="text/javascript" src="<?php echo $class->defaultJsPath; ?>jquery-3.3.1.min.js"></script>   

<script type="text/javascript">
    jQuery(document).ready(function(){
         $("[name=no], [name=msg]").change(function(){   
             var wa = $('[name=no]').val();
             var msg = window.encodeURIComponent($('[name=msg]').val());
             console.log(msg);
             
             var link = 'https://api.whatsapp.com/send?phone=' + wa + '&text=' + msg;
              
             $('.btn').attr("href",link);
             $('.btn').click();
          })     
    });
</script>
    
<input type="text" name="no" ><br><br>
<textarea name="msg" style="height:30em; width:50em">
Halo,

Salam kenal, saya Jaehan dari PT. Winn Teknologi Nusantara (Wintera). Kami ingin mengenalkan program kami yang terhubung dengan platform Marketplace seperti Tokopedia, Shopee, dan Lazada. Dengan sekali klik, Anda dapat mengatur semua penjualan dan stok produk Anda di seluruh Marketplace.

Ini akan sangat membantu bagi Anda! 😇😇 Selain itu, program kami juga memiliki fitur sebagai berikut:

•	Tidak terbatas jumlah produk
•	Tidak terbatas jumlah pengguna
•	Stok dan transaksi jual beli yang diperbarui secara real-time
•	Dukungan untuk unit dan gudang yang berbeda
•	Modul pembelian, penjualan, hutang, dan piutang
•	Pembuatan jurnal otomatis
•	Laporan stok, penjualan, dan keuangan
•	Dukungan online gratis

Semua ini bisa Anda dapatkan hanya dengan Rp300.000 per bulan loh!!! 
Sangat menarik bukan? kesempatan ini tidak boleh dilewatkan! Segera dapatkan uji coba gratisnya sekarang juga. Tapi ingat, waktu terbatas. Jangan sampai Anda melewatkan kesempatan ini!

Kami juga menawarkan layanan pembuatan website kustom untuk keperluan perusahaan Anda. Kami akan membantu mendesain website sesuai dengan kebutuhan Anda, termasuk integrasi dengan platform e-commerce untuk membantu meningkatkan penjualan Anda secara maksimal.

berikut sedikit penjelasan untuk Program kami dan portofolio untuk website yang pernah kami kerjakan, jika ingin mengetahui lebih lanjut dan mendapatkan free trial dapat menghubungi kami di:
marketing@wintera.co.id atau Whatsapp +62 877-8687-6667
dan kunjungi kami di
website :  https://wintera.co.id/.
instagram : @wintera.co.id

PT. Winn Teknologi Nusantara terletak di Infiniti Office, Arcade Business Center 6th Floor Unit 6-03
Jl. Pantai Indah Utara 2 Kav, C1, PIK Penjaringan, Jakarta Utara. 14460

Terima Kasih.
    
Warm Regards,
Jaehan P.
</textarea><br><br>
<a href="#" target="_blank" class="btn">Buka WA</a> 
</head>
</html>

