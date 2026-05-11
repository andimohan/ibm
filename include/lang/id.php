<?php 
$this->errorMsg =  array();
 

// ERROR MSG
$this->systemErrorMsg[404] = 'Halaman tidak ditemukan.';

// DB CONNECTION 
$this->errorMsg[100] = 'Koneksi gagal.';

// UDPATE DATA ERROR
$this->errorMsg[200] = 'Item tidak valid.';
$this->errorMsg[201] = 'Perubahan status gagal.'; 
$this->errorMsg[202] = 'Data tidak dapat diubah ke status MENUNGGU.'; 
$this->errorMsg[203] = 'Data tidak dalam status MENUNGGU.'; 
$this->errorMsg[204] = 'Data tidak dalam status KONFIRMASI.';  
$this->errorMsg[205] = 'Data tidak dalam status SELESAI.'; 
$this->errorMsg[206] = 'Data tidak dalam status AKTIF.'; 
$this->errorMsg[207] = 'Data tidak dalam status NONAKTIF.'; 

$this->errorMsg[210] = 'Data tidak dapat dihapus.';   
$this->errorMsg[211] = 'Data <em>predefined</em> tidak dapat dihapus.'; 
$this->errorMsg[212] = 'Update data gagal.'; 
$this->errorMsg[213] = 'Data tidak ditemukan.';
$this->errorMsg[214] = 'Data telah diubah oleh user lain. Silahkan merubah ulang data Anda.'; 
$this->errorMsg[215] = 'Duplikasi data.';
$this->errorMsg[216] = 'File tidak ditemukan.'; 
$this->errorMsg[217] = 'File tidak ditemukan / belum selesai diunggah.'; 
$this->errorMsg[218] = 'Duplikasi data gagal.'; 
$this->errorMsg[219] = 'Duplikasi data hanya dapat dilakukan untuk data yang berstatus BATAL.';

$this->errorMsg[220] = 'Data sudah berstatus SELESAI.'; 
$this->errorMsg[221] = 'Data sudah berstatus BATAL.'; 
$this->errorMsg[223] = 'Data tidak sesuai.'; 
$this->errorMsg[224] = 'Data tidak dapat diubah ke status yang sama.'; 
$this->errorMsg[225] = 'Data sudah berstatus KONFIRMASI atau SELESAI.';  
$this->errorMsg[226] = 'Data tidak dalam status PROSES SPK.';  
$this->errorMsg[227] = 'Data harus berstatus MENUNGGU atau KONFIRMASI.';  
$this->errorMsg[228] = 'Data tidak dalam status KONFIRMASI atau SELESAI.';  
 
$this->errorMsg[250] = 'Anda tidak memiliki hak akses.';
$this->errorMsg[251] = 'Anda tidak memiliki hak akses untuk menghapus data.';
$this->errorMsg[252] = 'Anda tidak memiliki hak akses untuk merubah status transaksi.';

$this->errorMsg[280] = 'Duplikasi data.';  

// MEMBER LOGIN, LOGOUT, PROFILE, ACTIVATION etc 
$this->errorMsg[300] = 'Login gagal. Login ID dan Password tidak cocok.';
$this->errorMsg[301] = '';
$this->errorMsg[302] = 'Akun tidak ditemukan.';
$this->errorMsg[303] = 'Link telah kadaluarsa.';
$this->errorMsg[304] = '';
$this->errorMsg[305] = 'OTP tidak cocok.';
$this->errorMsg[306] = 'Akun telah aktif.';

//STOCK
$this->errorMsg[400] = 'Gudang telah memiliki data pergerakan.';
$this->errorMsg[401] = 'Barang telah memiliki data pergerakan.';
$this->errorMsg[402] = 'Stok tidak mencukupi.';
$this->errorMsg[403] = 'Barang telah digunakan untuk BOM.';

//TRANSACTION
$this->errorMsg[500] = 'Jumlah transaksi dan harga unit harus lebih besar dari 0.';
$this->errorMsg[501] = 'Detail transaksi tidak boleh kosong.';
$this->errorMsg[502] = 'Pembayaran tidak mencukupi.';
$this->errorMsg[503] = 'Jumlah transaksi harus lebih besar dari 0.';
$this->errorMsg[504] = 'Kesalahan GL.';
$this->errorMsg[505] = 'Jumlah lebih kecil dari yang telah difaktur.'; 
$this->errorMsg[506] = 'Transaksi belum difaktur penuh.'; 
$this->errorMsg[507] = 'Transaksi telah difaktur penuh.'; 
$this->errorMsg[508] = 'Jumlah transaksi melebihi sisa transaksi yang belum difaktur.'; 
$this->errorMsg[509] = 'Nilai pembayaran melebihi nilai transaksi.';
$this->errorMsg[510] = 'Jumlah transaksi harus lebih besar dari 0.';
$this->errorMsg[511] = 'Harga unit harus lebih besar dari 0.';
$this->errorMsg[512] = 'Jumlah transaksi tidak boleh sama dengan 0.';
$this->errorMsg[513] = 'Nilai transaksi melebihi batas yang telah ditentukan.';

//API 
$this->errorMsg[601] = 'Token tidak cocok.'; 
$this->errorMsg[602] = 'File tidak cocok.'; 
$this->errorMsg[603] = 'Nilai harus diisi.'; 
$this->errorMsg[604] = 'Tipe data salah.'; 

// ETC
$this->errorMsg[900] = 'Perubahan status gagal karena sedang aktif terhubung ke data lain.';
$this->errorMsg[901] = 'Email gagal dikirim.'; 
$this->errorMsg[902] = 'Tablekey tidak ditemukan.'; 
$this->errorMsg[903] = 'Alasan pembatalan harus diisi.'; 
$this->errorMsg[904] = 'Duplikasi data gagal karena sedang aktif terhubung ke data lain.';
$this->errorMsg[905] = 'Gudang tidak sama.';
$this->errorMsg[906] = 'Nilai atau detail transaksi telah berubah.';
 

// EMPTY FIELD
//general
$this->errorMsg['username'][1] = 'Username harus diisi.';
$this->errorMsg['username'][2] = 'Username Anda telah terdaftar. Silahkan memilih username lain.';
$this->errorMsg['username'][3] = 'Username harus diantara 5 - 30 karakter.';
$this->errorMsg['username'][4] = 'Username hanya dapat diisi huruf, angka, titik dan garis bawah.'; 
$this->errorMsg['username'][5] = 'Username dan password tidak cocok.';

$this->errorMsg['pallet'][1] = 'Nama pallet harus diisi.';
$this->errorMsg['pallet'][2] = 'Nama pallet telah terdaftar. Silahkan memilih nama lain.';

$this->errorMsg['code'][1] = 'Kode harus diisi.';
$this->errorMsg['code'][2] = 'Kode Anda telah terdaftar. Silahkan memilih kode lain.';
$this->errorMsg['code'][3] = 'Kode tidak terdaftar.'; 
$this->errorMsg['code'][4] = 'Kode tidak boleh diubah karena sudah terdapat transaksi turunan.';

$this->errorMsg['contract'][1] = 'Kontrak harus diisi.'; 
$this->errorMsg['contract'][2] = 'Nama kontrak harus diisi.';
$this->errorMsg['contract'][3] = 'Nama kontrak telah terdaftar. Silahkan memilih nama lain.';
$this->errorMsg['contract'][4] = 'Durasi kontrak harus lebih besar dari 0.';
$this->errorMsg['contract'][5] = 'Kontrak tidak dalam status KONFIRMASI.';
$this->errorMsg['contract'][6] = 'Kontrak sudah kadaluarsa.';
$this->errorMsg['contract'][7] = 'Kontrak sudah ada untuk pelanggan yang sama.';

$this->errorMsg['name'][1] = 'Nama harus diisi.';
$this->errorMsg['name'][2] = 'Nama Anda telah terdaftar. Silahkan memilih nama lain.';

// particular field
$this->errorMsg['address'][1] = 'Alamat harus diisi.';
$this->errorMsg['address'][2] = 'Alamat tidak boleh lebih dari 65 karakter.';

$this->errorMsg['zipcode'][1] = 'Kode pos harus diisi.';

$this->errorMsg['agent'][1] = 'Nama agent harus diisi.'; 

$this->errorMsg['asset'][1] = 'Nama aset harus diisi.';
$this->errorMsg['asset'][2] = 'Masa manfaat harus lebih besar dari 0.'; 
$this->errorMsg['asset'][3] = 'Link COA harus diisi.'; 
$this->errorMsg['asset'][4] = 'Aset tidak dapat dibatalkan karena telah terjadi penyusutan.';
$this->errorMsg['asset'][5] = 'Aset tidak dapat disusutkan lagi karena sudah 0.';
 
$this->errorMsg['amount'][1] = 'Jumlah harus diisi.';
$this->errorMsg['amount'][2] = 'Jumlah harus lebih besar dari 0.'; 

$this->errorMsg['answer'][1] = 'Jawaban harus diisi.';

$this->errorMsg['ap'][1] = 'Hutang harus diisi.';
$this->errorMsg['ap'][2] = 'Hutang tidak dalam status OPEN.';
$this->errorMsg['ap'][3] = 'Hutang akan berubah menjadi PARTIAL secara otomatis jika terjadi pembayaran hutang.';
$this->errorMsg['ap'][4] = 'Hutang tidak dapat dibatalkan karena terhubung dengan transaksi pembelian. Hutang ini akan otomatis dibatalkan jika pembelian dibatalkan.';
$this->errorMsg['ap'][5] = 'Data hutang berbeda dengan pemasok.'; 
$this->errorMsg['ap'][6] = 'Hutang tidak ditemukan atau telah lunas.';
$this->errorMsg['ap'][7] = 'Referensi Hutang berbeda.'; 
$this->errorMsg['ap'][8] = 'Hutang tidak dapat dibatalkan karena terhubung dengan transaksi realisasi. Hutang ini akan otomatis dibatalkan jika realisasi dibatalkan.'; 
$this->errorMsg['ap'][9] = 'Hutang tidak dapat dibatalkan karena terhubung dengan transaksi Debit Note.'; 

$this->errorMsg['apCommission'][2] = 'Hutang komisi tidak dalam status OPEN.';

$this->errorMsg['apPayment'][2] = 'Jumlah pembayaran hutang tidak valid.';
$this->errorMsg['apPayment'][3] = 'Jumlah pelunasan hutang harus lebih besar dari 0.'; 
$this->errorMsg['apPayment'][4] = 'Tanggal hutang harus lebih kecil dari tanggal pembayaran.';
$this->errorMsg['apPayment'][5] = 'Hutang tidak sesuai dengan mata uang.';

$this->errorMsg['apTax23'][6] = 'Hutang PPH 23 tidak ditemukan atau telah lunas.';

$this->errorMsg['ar'][1] = 'Piutang harus diisi.';
$this->errorMsg['ar'][2] = 'Piutang tidak dalam status OPEN.';
$this->errorMsg['ar'][3] = 'Piutang akan berubah menjadi PARTIAL secara otomatis jika terjadi pembayaran piutang.';
$this->errorMsg['ar'][4] = 'Piutang tidak dapat dibatalkan karena terhubung dengan transaksi penjualan. Piutang ini akan otomatis dibatalkan jika penjualan dibatalkan.'; 
$this->errorMsg['ar'][5] = 'Data piutang berbeda dengan pelanggan.'; 
$this->errorMsg['ar'][6] = 'Piutang tidak ditemukan atau telah lunas.';
$this->errorMsg['ar'][7] = 'Piutang tidak dapat dibatalkan karena terhubung dengan transaksi realisasi. Piutang ini akan otomatis dibatalkan jika realisasi dibatalkan.'; 
$this->errorMsg['ar'][8] = 'Piutang tidak mencukupi.';

$this->errorMsg['arPrepaid23'][3] = 'Bukti penerimaan sudah diinput.';
	
$this->errorMsg['arap'][1] = 'Netting hutang dan piutang tidak sama.';

$this->errorMsg['arEmployee'][2] = 'Piutang karyawan tidak dalam status OPEN.';
$this->errorMsg['arEmployee'][3] = 'Piutang karyawan harus lebih besar dari 0.';
$this->errorMsg['arEmployee'][4] = 'Piutang karyawan tidak boleh lebih besar dari total selisih.';
$this->errorMsg['arEmployee'][5] = 'Piutang karyawan harus sama dengan 0.';
$this->errorMsg['arEmployee'][6] = 'Mata uang tidak sesuai.';
$this->errorMsg['arEmployee'][7] = 'Piutang karyawan tidak dalam status OPEN atau PARTIAL.';
$this->errorMsg['arEmployee'][8] = 'Karyawan tidak sesuai.';
$this->errorMsg['arEmployee'][9] = 'Nilai pemotongan melebihi sisa piutang karyawan.';

$this->errorMsg['apEmployee'][2] = 'Hutang karyawan tidak dalam status OPEN.';
$this->errorMsg['apEmployee'][3] = 'Hutang karyawan harus lebih besar dari 0.';
$this->errorMsg['apEmployee'][4] = 'Karyawan tidak sesuai.';
$this->errorMsg['apEmployee'][5] = 'Nilai melebihi sisa hutang karyawan.';
 
$this->errorMsg['apEmployeePayment'][2] = 'Data tidak dapat dihapus / dibatalkan, karena masih aktif terhubung ke Pembayaran Hutang Komisi Karyawan.';

$this->errorMsg['apEmployeeCommission'][2] = 'Hutang komisi karyawan tidak dalam status OPEN.';

/*
$this->errorMsg['ar'][5] = 'Piutang tidak dapat dibatalkan karena telah memiliki pembayaran. Silahkan membatalkan pembayaran untuk piutang ini terlebih dahulu.';
$this->errorMsg['ar'][6] = 'Piutang tidak dapat diubah menjadi PARTIAL karena pembayaran piutang sudah penuh.';
$this->errorMsg['ar'][7] = 'Piutang tidak dapat diubah menjadi PARTIAL karena belum memiliki pembayaran.';
*/

$this->errorMsg['arPayment'][2] = 'Jumlah pembayaran piutang tidak valid.';
$this->errorMsg['arPayment'][3] = 'Jumlah pelunasan piutang harus lebih besar dari 0.';
$this->errorMsg['arPayment'][4] = 'Tanggal piutang harus lebih kecil dari tanggal pembayaran.';
$this->errorMsg['arPayment'][5] = 'Piutang tidak sesuai dengan mata uang.';
$this->errorMsg['arPayment'][6] = 'Pembayaran piutang memerlukan persetujuan tambahan.';

$this->errorMsg['arTax23'][6] = 'PPH 23 dibayar dimuka tidak ditemukan atau telah lunas.';

$this->errorMsg['article'][1] = 'Judul artikel harus diisi.';
$this->errorMsg['article'][2] = 'Judul artikel telah terdaftar. Silahkan memilih judul artikel lain.';

$this->errorMsg['assembly'][1] = 'Data BOM berbeda dengan barang yang dihasilkan.'; ; 

$this->errorMsg['banner'][1] = 'Nama banner harus diisi.';
$this->errorMsg['banner'][2] = 'Nama banner telah terdaftar. Silahkan memilih nama banner lain.';
 
$this->errorMsg['bank'][1] = 'Nama bank harus diisi.';

$this->errorMsg['bankaccountname'][1] = 'Nama pemegang rekening harus diisi.';

$this->errorMsg['bankaccountnumber'][1] = 'Nomor rekening harus diisi.';

$this->errorMsg['billofmaterials'][1] = 'Nama BOM harus diisi.';

$this->errorMsg['cashAdvance'][1] = 'Kasbon harus diisi.';

$this->errorMsg['cashAdvanceRealization'][2] = 'Kasbon tidak dalam status menunggu.';
$this->errorMsg['cashAdvanceRealization'][3] = 'Nilai realisasi tidak sesuai dengan nilai kasbon.';
$this->errorMsg['cashAdvanceRealization'][4] = 'Realisasi untuk kasbon yang sama telah terdaftar.';
$this->errorMsg['cashAdvanceRealization'][5] = 'Akun penyelesaian harus diisi.';
$this->errorMsg['cashAdvanceRealization'][6] = 'Transaksi detail belum selesai diproses semua.';
$this->errorMsg['cashAdvanceRealization'][7] = 'Kasbon tidak berasal dari gudang yang sama.';

$this->errorMsg['cashBankRealization'][2] = 'Transaksi telah pernah direalisasi.';

$this->errorMsg['company'][1] = 'Nama perusahaan tidak boleh kosong.';
$this->errorMsg['company'][2] = 'Nama perusahaan telah terdaftar. Silahkan memilih nama lain.';
$this->errorMsg['company'][3] = 'Perusahaan tidak cocok.';

 
$this->errorMsg['brand'][1] = 'Nama merk harus diisi.';
$this->errorMsg['brand'][2] = 'Nama merk telah terdaftar. Silahkan memilih nama merk lain.';

$this->errorMsg['businessPartner'][1] = 'Nama rekan usaha harus diisi.';

$this->errorMsg['campaign'][1] = 'Nama campaign harus diisi.';
$this->errorMsg['campaign'][2] = 'Nama campaign telah terdaftar. Silahkan memilih nama lain.';
$this->errorMsg['campaign'][3] = 'Detail marketplace harus diisi.';
$this->errorMsg['campaign'][4] = 'Detail merk harus diisi.';
$this->errorMsg['campaign'][5] = 'Detail barang harus diisi.';
$this->errorMsg['campaign'][6] = 'Detail kategori barang harus diisi.';

$this->errorMsg['car'][1] = 'No. polisi harus diisi.'; 
$this->errorMsg['car'][2] = 'No. polisi telah terdaftar. Silahkan memilih no. polisi lain.'; 
$this->errorMsg['car'][3] = 'No. stnk telah terdaftar. Silahkan memilih no. stnk lain.'; 
$this->errorMsg['car'][4] = 'No. KIR telah terdaftar. Silahkan memilih no. KIR lain.'; 
$this->errorMsg['car'][5] = 'No. mesin telah terdaftar. Silahkan memilih no. mesin lain.'; 
$this->errorMsg['car'][6] = 'No. rangka telah terdaftar. Silahkan memilih no. rangka lain.'; 
$this->errorMsg['car'][7] = 'No. BPKB telah terdaftar. Silahkan memilih no. BPKB lain.'; 
$this->errorMsg['car'][8] = 'Km tidak boleh lebih kecil dari sebelumnya.'; 
$this->errorMsg['car'][9] = 'No. polisi tidak terdaftar. Silahkan memilih no polisi lain.'; 
$this->errorMsg['car'][10] = 'Km tidak boleh lebih besar dari perawatan setelahnya.'; 

$this->errorMsg['careerField'][1] = 'Nama bidang perkerjaan harus diisi.';
$this->errorMsg['careerField'][2] = 'Nama bidang perkerjaan telah terdaftar. Silahkan memilih nama bidang perkerjaan lain.';

$this->errorMsg['carServiceMaintenance'][2] = 'Harga dasar telah berubah. Silahkan membuka dan menyimpan ulang data Anda.'; 
$this->errorMsg['carServiceMaintenance'][3] = 'Perawatan mobil tidak dalam status MENUNGGU.'; 
$this->errorMsg['carServiceMaintenance'][4] = 'Part bekas telah dikonfirmasi di pemasukan barang. Silahkan membatalkan terlebih pemasukan barang terlebih dahulu.'; 
$this->errorMsg['carServiceMaintenance'][4] = 'Posisi part tidak terdaftar.'; 
$this->errorMsg['carServiceMaintenance'][5] = 'Tidak dapat membatalkan maintenance karena barang dengan posisi part berikut telah ada maintenance terbaru.';
$this->errorMsg['carServiceMaintenance'][6] = 'Tidak dapat membatalkan maintenance karena barang dengan Serial Number berikut telah ada maintenance terbaru.';
	
$this->errorMsg['cashBank'][2] = 'Voucher kas / bank telah digunakan.'; 
$this->errorMsg['cashBank'][3] = 'Voucher kas / bank berbeda dengan pelanggan.';
$this->errorMsg['cashBank'][4] = 'Voucher kas / bank tidak mencukupi.';
$this->errorMsg['cashBank'][5] = 'Voucher kas / bank tidak ditemukan atau telah digunakan.';
$this->errorMsg['cashBank'][6] = 'Voucher kas / bank telah direkonsiliasi.';

$this->errorMsg['bankReconsiliation'][2] = 'Mata uang voucher kas / bank berbeda.';
$this->errorMsg['bankReconsiliation'][3] = 'COA voucher kas / bank berbeda.';
$this->errorMsg['bankReconsiliation'][4] = 'Periode voucher kas / bank tidak valid.';
$this->errorMsg['bankReconsiliation'][5] = 'Periode yang dipilih telah tutup buku.';
$this->errorMsg['bankReconsiliation'][6] = 'Nilai saldo awal tidak valid.';
    
$this->errorMsg['chassis'][1] = 'No. rangka harus diisi.'; 
$this->errorMsg['chassis'][2] = 'No. rangka telah terdaftar. Silahkan memilih no rangka lain.';  
$this->errorMsg['chassis'][3] = 'No. KIR telah terdaftar. Silahkan memilih no KIR lain.'; 

$this->errorMsg['cart'][1] = 'Anda belum memiliki daftar belanja.'; 

$this->errorMsg['category'][1] = 'Nama kategori harus diisi.';
$this->errorMsg['category'][2] = 'Nama kategori telah terdaftar. Silahkan memilih nama kategori lain.';
$this->errorMsg['category'][3] = 'Kategori tidak valid. Anda tidak dapat memilih kategori yang memiliki sub kategori';

$this->errorMsg['captcha'][1] = 'CAPTCHA tidak valid.'; 

$this->errorMsg['checkIn'][1] = 'Check In gagal. Login ID dan password tidak cocok.';

$this->errorMsg['coa'][1] = 'Nama akun harus diisi.';
$this->errorMsg['coa'][2] = 'Nama akun telah terdaftar. Silahkan memilih nama akun lain.';
$this->errorMsg['coa'][3] = 'Link akun tidak terdaftar.';
$this->errorMsg['coa'][4] = 'Akun telah digunakan dalam transaksi.';
$this->errorMsg['coa'][5] = 'Akun tidak sama.';
$this->errorMsg['coa'][6] = 'Tutup buku gagal karena masih ada voucher kas/bank yang belum di rekonsiliasi.';

$this->errorMsg['coatransfer'][1] = 'Akun asal dan akun tujuan tidak boleh sama.';
 
$this->errorMsg['codriver'][1] = 'Asisten sopir harus diisi.';

$this->errorMsg['container'][1] = 'Jenis container harus diisi.'; 
$this->errorMsg['container'][2] = 'Jenis container telah terdaftar. Silahkan memilih jenis lain.';  
$this->errorMsg['container'][3] = 'Jenis container tidak sesuai.';

$this->errorMsg['cost'][1] = 'Biaya harus diisi.';  
$this->errorMsg['cost'][2] = 'Biaya telah terdaftar. Silahkan memilih biaya lain.';  

$this->errorMsg['costRate'][1] = 'Tarif biaya harus diisi.';  
$this->errorMsg['costRate'][2] = 'Tarif dengan informasi yang sama telah terdaftar.';  

$this->errorMsg['currency'][1] = 'Nama mata uang harus diisi.';
$this->errorMsg['currency'][2] = 'Nama mata uang telah terdaftar. Silahkan memilih nama mata uang lain.';

$this->errorMsg['carat'][1] = 'Nama karat harus diisi.';
$this->errorMsg['carat'][2] = 'Nama karat telah terdaftar. Silahkan memilih nama lain.';

$this->errorMsg['customer'][1] = 'Nama pelanggan harus diisi.';
$this->errorMsg['customer'][2] = 'Nama pelanggan telah terdaftar. Silahkan memilih nama pelanggan lain.';
$this->errorMsg['customer'][3] = 'Pelanggan tidak sama.';
$this->errorMsg['customer'][4] = 'Pelanggan tidak sesuai.';
$this->errorMsg['customer'][5] = 'NPWP harus diisi.';

$this->errorMsg['city'][1] = 'Kota harus diisi.';
$this->errorMsg['city'][2] = 'Nama kota telah terdaftar. Silahkan memilih nama kota lain.';
$this->errorMsg['city'][3] = 'Kota tidak valid.';

$this->errorMsg['codeVariant'][1] = 'Nama variasi harus diisi.';
$this->errorMsg['codeVariant'][2] = 'Nama variasi untuk group yang sama telah terdaftar. Silahkan memilih nama variasi lain.'; 
  
$this->errorMsg['creditlimit'][1] = 'Kredit limit tidak mencukupi.';

$this->errorMsg['cancelReason'][1] = 'Alasan pembatalan harus diisi.';  

$this->errorMsg['creditNote'][1] = 'Nota kredit tidak boleh kosong.';
$this->errorMsg['creditNote'][2] = 'Nota kredit tidak boleh lebih besar dari outstanding piutang.';
$this->errorMsg['creditNote'][3] = 'Nota kredit dalam status OPEN.';
$this->errorMsg['creditNote'][4] = 'Piutang tidak sesuai dengan Mata Uang.';
$this->errorMsg['creditNote'][5] = 'Nilai piutang sudah berubah.';


$this->errorMsg['debitNote'][1] = 'Nilai nota debit tidak boleh kosong.';
$this->errorMsg['debitNote'][2] = 'Nota debit tidak boleh lebih besar dari nilai pembelian.';
$this->errorMsg['debitNote'][3] = 'Nota debit dalam status OPEN.';
$this->errorMsg['debitNote'][4] = 'Detail AP tidak boleh kosong.';

$this->errorMsg['commission'][1] = 'Jumlah komisi harus diisi.';
$this->errorMsg['commission'][2] = 'Jumah komisi harus lebih besar dari 0.'; 
$this->errorMsg['commission'][3] = 'Nama sales harus diisi.'; 

$this->errorMsg['date'][1] = 'Tanggal harus diisi.';
$this->errorMsg['date'][2] = 'Tanggal telah terdaftar. Silahkan memilih tanggal lain.'; 
$this->errorMsg['date'][3] = 'Tanggal mulai harus lebih kecil daripada tanggal selesai.'; 
   
$this->errorMsg['depot'][1] = 'Nama depot harus diisi.';
$this->errorMsg['depot'][2] = 'Nama depot telah terdaftar. Silahkan memilih nama lain.';
$this->errorMsg['depot'][3] = 'Kode DO harus diisi.';
$this->errorMsg['depot'][4] = 'Kode surat jalan harus diisi.';

$this->errorMsg['division'][1] = 'Nama divisi harus diisi.';
$this->errorMsg['division'][2] = 'Nama divisi telah terdaftar. Silahkan memilih nama divisi lain.';
 
$this->errorMsg['download'][1] = 'Judul harus diisi.';
$this->errorMsg['download'][2] = 'Judul telah terdaftar. Silahkan memilih judul lain.';
 
$this->errorMsg['downpayment'][3] = 'Data penjualan berbeda dengan pelanggan.';
$this->errorMsg['downpayment'][4] = 'Data penjualan berbeda dengan pemasok.';
$this->errorMsg['downpayment'][5] = 'Uang muka telah digunakan.'; 
$this->errorMsg['downpayment'][6] = 'Uang muka tidak sesuai dengan pelanggan.';
$this->errorMsg['downpayment'][7] = 'Uang muka tidak sesuai dengan pemasok.';
$this->errorMsg['downpayment'][8] = 'Nilai melebihi sisa uang muka.'; 
$this->errorMsg['downpayment'][9] = 'Uang muka tidak ditemukan atau telah digunakan.';
$this->errorMsg['downpayment'][10] = 'Uang muka tidak sesuai dengan Mata Uang.';

$this->errorMsg['driver'][1] = 'Sopir harus diisi.';

$this->errorMsg['duedays'][1] = 'Jatuh tempo harus diisi.';   
$this->errorMsg['duedays'][2] = 'Jatuh tempo harus lebih besar dari 0.';   

$this->errorMsg['email'][1] = 'Email harus diisi.';
$this->errorMsg['email'][2] = 'Email Anda telah terdaftar. Silahkan memilih email lain.'; 
$this->errorMsg['email'][3] = 'Email tidak valid.';
$this->errorMsg['email'][4] = 'Email tidak terdaftar, mohon memasukkan email lain.';

$this->errorMsg['emklJobOrder'][2] = 'Detail yang telah difaktur tidak dapat dihapus.';
$this->errorMsg['emklJobOrder'][3] = 'Detail container tidak dapat dihapus karena telah digunakan di HBL.';
$this->errorMsg['emklJobOrder'][4] = 'Nama sales harus diisi.';
$this->errorMsg['emklJobOrder'][5] = 'Nama agent harus diisi.';
$this->errorMsg['emklJobOrder'][6] = 'Data penjualan reimburse tidak dapat dihapus.';
$this->errorMsg['emklJobOrder'][7] = 'Jenis freight harus dipilih.';
$this->errorMsg['emklJobOrder'][8] = 'Jenis shipment harus dipilih.';
$this->errorMsg['emklJobOrder'][9] = 'Komisi telah dibayarkan.';
	
$this->errorMsg['emklOrderInvoice'][2] = 'Job Order tidak dalam status konfirmasi.'; 
$this->errorMsg['emklOrderInvoice'][3] = 'Job Order tidak sesuai dengan data pelanggan.';
$this->errorMsg['emklOrderInvoice'][4] = 'No Faktur tidak sesuai dengan data pelanggan.';
$this->errorMsg['emklOrderInvoice'][5] = 'No Faktur tidak dalam status konfirmasi.'; 
$this->errorMsg['emklOrderInvoice'][6] = 'Faktur terhubung dengan faktur lain.';  
$this->errorMsg['emklOrderInvoice'][7] = 'Faktur telah dibuatkan Faktur Pajak.';  
$this->errorMsg['emklOrderInvoice'][8] = 'Rekening yang dipilih tidak sesuai.';  

$this->errorMsg['employee'][1] = 'Nama karyawan harus diisi.';
$this->errorMsg['employee'][2] = 'Nama karyawan telah terdaftar. Silahkan memilih nama karyawan lain.'; 
$this->errorMsg['employee'][3] = 'Planner harus diisi.';

$this->errorMsg['eta'][1] = 'ETA harus diisi.'; 

$this->errorMsg['event'][1] = 'Judul event harus diisi.';
$this->errorMsg['event'][2] = 'Judul event telah terdaftar. Silahkan memilih judul event lain.'; 

$this->errorMsg['generalJournal'][1] = 'Jumlah debit dan kredit harus sama.';
$this->errorMsg['generalJournal'][2] = 'Tanggal transaksi lebih besar dari tanggal hari ini.';
$this->errorMsg['generalJournal'][3] = 'Data tidak berada di periode bulan berjalan.';
$this->errorMsg['generalJournal'][4] = 'Data tidak dapat dihapus / dibatalkan karena sedang aktif terhubung ke data lain.';
$this->errorMsg['generalJournal'][5] = 'Data telah ditutup.';
$this->errorMsg['generalJournal'][6] = 'Periode transaksi telah ditutup.';
$this->errorMsg['generalJournal'][8] = 'Jurnal umum belum dikonfirmasi semua.';
$this->errorMsg['generalJournal'][9] = 'Tgl. pembatalan tidak boleh lebih kecil dari tgl. transaksi.';
$this->errorMsg['generalJournal'][10] = 'Update jurnal reval gagal karena jurnal reval untuk periode yang sama sudah terbentuk.';


$this->errorMsg['gramasi'][1] = 'Berat harus diisi.';
$this->errorMsg['gramasi'][2] = 'Berat harus lebih besar atau sama dengan 0.'; 
$this->errorMsg['gramasi'][3] = 'Berat harus lebih besar dari 0.'; 

$this->errorMsg['insuranceCompany'][1] = 'Perusahaan asuransi harus diisi.';
$this->errorMsg['insuranceCompany'][2] = 'Perusahaan asuransi telah terdaftar. Silahkan memilih perusahaan lain.';
$this->errorMsg['insuranceCompany'][3] = 'Perusahaan asuransi tidak terdaftar di perusahaan.'; 
	
$this->errorMsg['invoice'][1] = 'Faktur harus diisi.';
$this->errorMsg['invoice'][2] = 'Faktur tidak sesuai dengan data pelanggan.';  
$this->errorMsg['invoice'][3] = 'Faktur telah memiliki faktur pajak.';  
$this->errorMsg['invoice'][4] = 'Faktur telah terdaftar.'; 
$this->errorMsg['invoice'][5] = 'Referensi faktur tidak valid.';
$this->errorMsg['invoice'][7] = 'Faktur belum lunas.';   

$this->errorMsg['invoiceTaxNumber'][1] = 'Nomor faktur pajak harus diisi.';  
$this->errorMsg['invoiceTaxNumber'][2] = 'Nomor faktur pajak telah terdaftar. Silahkan memilih nomor faktur pajak lain.'; 
$this->errorMsg['invoiceTaxNumber'][3] = 'Nilai pajak tidak boleh kosong.';  
$this->errorMsg['invoiceTaxNumber'][4] = 'Nomor faktur dengan jenis pajak yang sama telah di buat.'; 

$this->errorMsg['item'][1] = 'Nama barang harus diisi.';
$this->errorMsg['item'][2] = 'Nama barang telah terdaftar. Silahkan memilih nama barang lain.'; 
$this->errorMsg['item'][3] = 'Jenis barang tidak sama. Silahkan memilih barang lain.'; 
$this->errorMsg['item'][4] = 'Barang harus dalam satuan unit dasar.'; 
$this->errorMsg['item'][5] = 'Deskripsi singkat harus diisi.'; 
$this->errorMsg['item'][6] = 'Variant tidak valid.'; 
//$this->errorMsg['item'][7] = 'Barang bukan parent.'; 
$this->errorMsg['item'][8] = 'Nilai COGS diatas margin yang telah ditentukan.'; 

$this->errorMsg['itemAdjustment'][1] = 'Stok barang telah berubah.'; 

$this->errorMsg['itemCheckList'][2] = 'Jumlah barang harus lebih besar dari 0.';

$this->errorMsg['itemChecklistGroup'][1] = 'Nama grup harus diisi.';

$this->errorMsg['itemFilter'][1] = 'Filter barang harus diisi.';

$this->errorMsg['itemUnit'][1] = 'Nama unit harus diisi.';  
$this->errorMsg['itemUnit'][2] = 'Nama unit telah terdaftar. Silahkan memilih nama unit lain.';  

$this->errorMsg['itemUnitConversion'][1] = 'Konversi unit harus diisi.';  
$this->errorMsg['itemUnitConversion'][2] = 'Anda memasukan lebih dari satu konversi yang sama.';  
$this->errorMsg['itemUnitConversion'][3] = 'Barang ini tidak memiliki konversi untuk unit yang dipilih.';
$this->errorMsg['itemUnitConversion'][4] = 'Satuan transaksi harus memiliki konversi.';
$this->errorMsg['itemUnitConversion'][5] = 'Konversi harus lebih besar dari 0.';

$this->errorMsg['itemParent'][1] = 'Item <em>parent</em> harus diisi.'; 
$this->errorMsg['itemParent'][2] = 'Item <em>parent</em> tidak boleh memiliki QOH.'; 

$this->errorMsg['jobOrder'][1] = 'Job order harus diisi.';  
$this->errorMsg['jobOrder'][2] = 'Status job order tidak valid.';  

$this->errorMsg['jobOrderHeader'][1] = 'Job header harus diisi.';  
$this->errorMsg['jobOrderHeader'][2] = 'Status job header tidak valid.';  

$this->errorMsg['jobOpportunities'][1] = 'Judul lowongan pekerjaan harus diisi.';  

$this->errorMsg['jobType'][1] = 'Jenis pekerjaan harus diisi.';
$this->errorMsg['jobType'][2] = 'Jenis pekerjaan telah terdaftar. Silahkan memilih jenis pekerjaan lain.';   

$this->errorMsg['leasing'][2] = 'Jumlah pinjaman harus harus lebih besar dari 0.'; 
$this->errorMsg['leasing'][3] = 'Periode pinjaman harus diisi.'; 
$this->errorMsg['leasing'][4] = 'Cicilan harus harus lebih besar dari 0.'; 

$this->errorMsg['limit'][1] = 'Anda sudah mencapai batas maksimum untuk jumlah data.';
$this->errorMsg['limit'][2] = 'Anda sudah mencapai batas maksimum untuk foto.';
$this->errorMsg['limit'][3] = 'Anda sudah mencapai batas maksimum untuk file.';
$this->errorMsg['limit'][4] = 'Ukuran foto terlalu besar.';   
$this->errorMsg['limit'][5] = 'Ukuran file terlalu besar.';    
$this->errorMsg['limit'][6] = 'Kapasitas penyimpanan tidak mencukupi.';  
$this->errorMsg['limit'][7] = 'Ukuran file terlalu kecil.';      

$this->errorMsg['location'][1] = 'Lokasi harus diisi.';
$this->errorMsg['location'][2] = 'Lokasi telah terdaftar. Silahkan memilih lokasi lain.';
$this->errorMsg['location'][3] = 'Lokasi tidak valid.';

$this->errorMsg['login'][1] = 'Login gagal. Akun Anda belum diaktivasi.';
$this->errorMsg['login'][2] = 'Login gagal. Akun Anda dalam keadaan terblokir.';  
$this->errorMsg['login'][3] = 'Terlalu banyak kesalahan login. Silahkan mencoba login kembali dalam {{LOCKOUT_MINUTES}} menit.';  
 

$this->errorMsg['marketplace'][1] = 'Nama marketplace harus diisi.';
$this->errorMsg['marketplace'][2] = 'Nama marketplace telah terdaftar. Silahkan memilih nama lain.'; 
$this->errorMsg['marketplace'][3] = 'Transaksi untuk marketplace ini telah terdaftar.';
$this->errorMsg['marketplace'][4] = 'Informasi produk harus diisi.';
$this->errorMsg['marketplace'][5] = 'Kategori marketplace harus diisi.';

$this->errorMsg['price'][1] = 'Harga harus di isi';
$this->errorMsg['price'][2] = 'Harga harus Lebih Besar dari 0';
$this->errorMsg['maxattendance'][2] = 'Anda telah melewati jumlah maksimum kehadiran.';

$this->errorMsg['maxStockQty'][1] = 'Stok Maks. harus diisi.';
$this->errorMsg['maxStockQty'][2] = 'Stok Maks. harus lebih besar atau sama dengan 0.'; 

$this->errorMsg['media'][1] = 'Nama media harus diisi.';
$this->errorMsg['message'][1] = 'Pesan harus diisi.';

$this->errorMsg['minStockQty'][1] = 'Stok Min. harus diisi.';
$this->errorMsg['minStockQty'][2] = 'Stok Min. harus lebih besar atau sama dengan 0.'; 

$this->errorMsg['news'][1] = 'Judul berita harus diisi.';  
$this->errorMsg['news'][2] = 'Judul berita telah terdaftar. Silahkan memilih judul berita lain.';  

$this->errorMsg['csr'][1] = 'Judul CSR harus diisi.';  
$this->errorMsg['csr'][2] = 'Judul CSR telah terdaftar. Silahkan memilih judul CSR lain.';  

$this->errorMsg['achievement'][1] = 'Judul achievement harus diisi.';  
$this->errorMsg['achievement'][2] = 'Judul achievement telah terdaftar. Silahkan memilih judul achievement lain.';

$this->errorMsg['occupation'][1] = 'Pekerjaan harus diisi.';

$this->errorMsg['oilType'][1] = 'Jenis oli harus diisi.';
$this->errorMsg['oilType'][2] = 'Jenis oli telah terdaftar. Silahkan memilih jenis lain.';  

$this->errorMsg['orderList'][1] = 'No. urut harus diisi.'; 
$this->errorMsg['orderList'][2] = 'No. urut harus berupa angka.'; 

$this->errorMsg['page'][1] = 'Nama page harus diisi'; 
$this->errorMsg['page'][2] = 'Nama page telah terdaftar. Silahkan memilih nama page lain.'; 

$this->errorMsg['paymentConfirmation'][1] = 'Sales order tidak ditemukan.'; 
$this->errorMsg['paymentConfirmation'][2] = 'Sales order telah dibayar.';

$this->errorMsg['password'][1] = 'Password harus diisi.';
$this->errorMsg['password'][2] = 'Password harus diantara 8 - 30 karakter.';
$this->errorMsg['password'][3] = 'Password dan konfirmasi password tidak cocok.';
$this->errorMsg['password'][4] = 'Panjang harus diantara 8 - 30 karakter, dan merupakan kombinasi dari huruf kapital, huruf kecil, numerik dan simbol.';
 
$this->errorMsg['passwordConfirmation'][1] = 'Konfirmasi password harus diisi.';
$this->errorMsg['passwordConfirmation'][2] = 'Konfirmasi password harus diantara 5 - 30 karakter.'; 

$this->errorMsg['pawnSalesOrder'][1] = 'Total pinjaman harus lebih kecil atau sama dengan dari nilai barang.';

$this->errorMsg['paymentMethod'][1] = 'Metode pembayaran harus diisi.';  
$this->errorMsg['paymentMethod'][2] = 'Metode pembayaran telah terdaftar. Silahkan memilih nama metode pembayaran lain.';  

$this->errorMsg['phone'][1] = 'Nomor telepon harus diisi.';  
$this->errorMsg['phone'][2] = 'Nomor telepon sudah terdaftar. Silahkan memilih nomor telepon lain.';  

$this->errorMsg['point'][1] = 'Poin harus diisi'; 
$this->errorMsg['point'][2] = 'Nilai poin harus lebih besar dari 0.'; 
$this->errorMsg['point'][3] = 'Nilai poin tidak mencukupi.';   
 
$this->errorMsg['terminal'][1] = 'Nama terminal harus diisi.';
$this->errorMsg['terminal'][2] = 'Nama terminal telah terdaftar. Silahkan memilih nama lain.';

$this->errorMsg['temporaryAccount'][1] = 'Ayat silang harus diisi.';

$this->errorMsg['ticketSupport'][1] = 'Subjek harus diisi.';
$this->errorMsg['ticketSupport'][2] = 'Pesan harus diisi.';

$this->errorMsg['ticketSupportWorkOrder'][1] = 'Teknisi harus diisi.';
$this->errorMsg['ticketSupportWorkOrder'][2] = 'Keterangan Pekerjaan harus diisi.';
$this->errorMsg['port'][1] = 'Nama port harus diisi.';
$this->errorMsg['port'][2] = 'Nama port telah terdaftar. Silahkan memilih nama lain.';

$this->errorMsg['portfolio'][1] = 'Judul portfolio harus diisi.';
$this->errorMsg['portfolio'][2] = 'Judul portfolio telah terdaftar. Silahkan memilih judul portfolio lain.';

$this->errorMsg['prepaidExpense'][1] = 'Biaya Dimuka harus diisi.';
$this->errorMsg['prepaidExpense'][2] = 'Biaya Dimuka tidak dalam status OPEN.';
$this->errorMsg['prepaidExpense'][3] = 'Biaya Dimuka akan berubah menjadi PARTIAL secara otomatis jika terjadi pengakuan biaya.';
$this->errorMsg['prepaidExpense'][4] = 'Biaya Dimuka tidak dapat dibatalkan karena terhubung dengan transaksi pembelian. Biaya Dimuka ini akan otomatis dibatalkan jika pembelian dibatalkan.';
$this->errorMsg['prepaidExpense'][6] = 'Biaya Dimuka tidak ditemukan atau telah lunas.';


$this->errorMsg['project'][1] = 'Nama proyek harus diisi.'; 

$this->errorMsg['pricelist'][1] = 'Nama tarif harus diisi.'; 
$this->errorMsg['pricelist'][2] = 'Nama tarif telah terdaftar. Silahkan memilih nama lain.'; 
$this->errorMsg['pricelist'][3] = 'Tarif harus diisi.'; 

$this->errorMsg['print'][1] = 'Anda belum memilih data yang hendak dicetak.'; 
$this->errorMsg['print'][2] = 'Data gagal dicetak karena status transaksi.'; 
$this->errorMsg['print'][3] = 'Anda hanya dapat menggabungkan faktur untuk satu pelanggan yang sama.'; 
$this->errorMsg['print'][4] = 'Anda hanya dapat menggabungkan faktur untuk jenis pajak yang sama.'; 

$this->errorMsg['purchaseOrder'][1] = 'Order pembelian harus diisi.';  
$this->errorMsg['purchaseOrder'][2] = 'Order pembelian tidak dapat dibatalkan karena sudah terjadi penerimaan barang. Silahkan membatalkan penerimaan barang terlebih dahulu.';  
$this->errorMsg['purchaseOrder'][3] = 'Harga beli tidak boleh diatas jual.';
$this->errorMsg['purchaseOrder'][4] = 'Harga beli melebihi margin yang telah ditentukan.';
$this->errorMsg['purchaseOrder'][5] = 'Transaksi reimburse tidak dapat dikenakan PPN.';
$this->errorMsg['purchaseOrder'][6] = 'Transaksi harus dipilih jenis Penjualan atau Reimburse.';
$this->errorMsg['purchaseOrder'][7] = 'Detail JO tidak sesuai dengan Job Order.';
$this->errorMsg['purchaseOrder'][8] = 'Jenis container tidak sesuai dengan job order.';
$this->errorMsg['purchaseOrder'][9] = 'Layanan telah difaktur.';
$this->errorMsg['purchaseOrder'][10] = 'Ukuran barang harus diisi.';
$this->errorMsg['purchaseOrder'][11] = 'Pengemasan harus diisi.';
$this->errorMsg['purchaseOrder'][12] = 'Total jumlah didetail tidak cocok dengan header.';

$this->errorMsg['purchaseOrderJewelry'][1] = 'Nomor harus diisi.';
$this->errorMsg['purchaseOrderJewelry'][2] = 'Nomor sudah terdaftar. Silahkan pilih nomor lain.';
$this->errorMsg['purchaseOrderJewelry'][3] = 'Order pembelian tidak dapat dibatalkan karena sudah terjadi penerimaan barang. Silahkan membatalkan penerimaan barang terlebih dahulu.';  
$this->errorMsg['purchaseOrderJewelry'][4] = 'Barang belum diterima penuh.';  

$this->errorMsg['receivingPurchaseJewelry'][1] ='Tanggal penerimaan pembelian harus lebih besar atau sama dengan tanggal order pembelian.';  
$this->errorMsg['receivingPurchaseJewelry'][2] ='Jumlah tidak boleh lebih dari Order Pembelian.';  
$this->errorMsg['receivingPurchaseJewelry'][3] ='Jumlah (Gr) tidak boleh lebih dari Order Pembelian.';  
$this->errorMsg['receivingPurchaseJewelry'][4] ='Gross weight harus lebih besar dari 0.';  
$this->errorMsg['receivingPurchaseJewelry'][5] ='Jumlah diterima harus lebih besar dari 0.';  
$this->errorMsg['receivingPurchaseJewelry'][6] ='Gross Weight harus lebih besar dari Jumlah (Gr).';
$this->errorMsg['receivingPurchaseJewelry'][7] ='Satuan dasar tidak sesuai dengan barang.';

$this->errorMsg['purchaseReceive'][1] = 'Jumlah harus lebih besar dari 0 dan lebih kecil dari kekurangan.';
$this->errorMsg['purchaseReceive'][2] = 'Tanggal penerimaan pembelian harus lebih besar atau sama dengan tanggal order pembelian.';    
$this->errorMsg['purchaseReceive'][3] = 'Jumlah yang diterima telah berubah. Silahkan membuka dan menyimpan ulang data Anda.'; 

$this->errorMsg['purchaseRequest'][1] = 'Permintaan pembelian harus diisi.';  
$this->errorMsg['purchaseRequest'][2] = 'Permintaan pembelian tidak dalam status KONFIRMASI atau SELESAI.';


$this->errorMsg['qty'][1] = 'Jumlah harus lebih besar dari 0.';    

$this->errorMsg['weight'][1] = 'Berat harus di isi';

$this->errorMsg['description'][1] = 'Deskripsi harus di isi';

$this->errorMsg['questionnaire'][2] = 'Semua pertanyaan harus dijawab.';  

$this->errorMsg['question'][1] = 'Pertanyaan harus diisi.';

$this->errorMsg['salesOrderProperty'][1] = 'Nilai transaksi harus diisi.'; 
$this->errorMsg['salesOrderProperty'][2] = 'Penjual harus diisi.';
$this->errorMsg['salesOrderProperty'][3] = 'Pembeli harus diisi.';
$this->errorMsg['salesOrderProperty'][4] = 'Bank harus diisi.';

$this->errorMsg['rate'][1] = 'Kurs harus diisi.'; 
$this->errorMsg['rate'][2] = 'Kurs telah terdaftar.'; 
$this->errorMsg['rate'][3] = 'Kurs berbeda dengan pelanggan.'; 
$this->errorMsg['rate'][4] = 'Kurs telah berubah.';
$this->errorMsg['rate'][5] = 'Kurs tidak valid.'; ; 
$this->errorMsg['rate'][6] = 'Kurs harus lebih besar dari 0.';  


$this->errorMsg['rating'][1] = 'Rating harus bernilai antara 1 hingga 5.'; 

$this->errorMsg['realization'][1] = 'Nilai realisasi harus lebih kecil atau sama dengan nilai yang dikeluarkan.'; 

$this->errorMsg['recipient'][1] = 'Nama penerima harus diisi.'; 
$this->errorMsg['recipient'][2] = 'Nama penerima tidak sama.'; 

$this->errorMsg['reference'][1] = 'Referensi harus diisi.'; 

$this->errorMsg['registration'][1] = 'Anda harus setuju dengan syarat dan ketentuan.'; 

$this->errorMsg['represented'][1] = 'Perwakilan harus diisi.';

$this->errorMsg['review'][1] = 'Ulasan harus diisi.'; 
 
$this->errorMsg['salesOrder'][1] = 'Order penjualan harus diisi.';  
$this->errorMsg['salesOrder'][2] = 'Order penjualan tidak dapat dibatalkan karena sudah terjadi pengiriman barang. Silahkan membatalkan pengiriman barang terlebih dahulu.';  
$this->errorMsg['salesOrder'][3] = 'Transaksi telah diretur.';
$this->errorMsg['salesOrder'][4] = 'Harga jual tidak boleh dibawah COGS.';
$this->errorMsg['salesOrder'][5] = 'Order penjualan tidak dapat dibatalkan karena sudah terjadi retur barang. Silahkan membatalkan retur barang terlebih dahulu.';  

$this->errorMsg['salesDelivery'][1] = 'Jumlah harus lebih besar dari 0 dan lebih kecil dari kekurangan.';
$this->errorMsg['salesDelivery'][2] = 'Tanggal penerimaan pembelian harus lebih besar atau sama dengan tanggal order penjualan.';    
$this->errorMsg['salesDelivery'][3] = 'Jumlah yang diterima telah berubah. Silahkan membuka dan menyimpan ulang data Anda.'; 
 
$this->errorMsg['salesOrderRental'][1] = 'Order penjualan harus diisi.';  
$this->errorMsg['salesOrderRental'][2] = 'Penawaran penjualan tidak sesuai dengan pelanggan.';  
$this->errorMsg['salesOrderRental'][3] = 'Jml. barang melebihi penawaran.';  

$this->errorMsg['salesRentalQuotation'][1] = 'Penawaran penjualan harus diisi.';  


$this->errorMsg['sellingPrice'][1] = 'Harga jual harus diisi.';
$this->errorMsg['sellingPrice'][2] = 'Harga jual harus lebih besar atau sama dengan 0.'; 
$this->errorMsg['sellingPrice'][3] = 'Harga jual harus lebih besar dari 0.'; 
 
$this->errorMsg['sellingRate'][1] = 'Tarif jual harus diisi.'; 
$this->errorMsg['sellingRate'][2] = 'Tarif jual telah terdaftar.'; 
$this->errorMsg['sellingRate'][3] = 'Tarif jual berbeda dengan pelanggan.'; 
$this->errorMsg['sellingRate'][4] = 'Tarif jual telah berubah.';
$this->errorMsg['sellingRate'][5] = 'Tarif jual tidak valid.'; ; 

$this->errorMsg['series'][1] = 'Seri harus diisi.';  
$this->errorMsg['series'][2] = 'Nama seri telah terdaftar. Silahkan memilih nama lain.'; 
$this->errorMsg['series'][3] = 'Nama seri tidak sesuai dengan merk.'; 

$this->errorMsg['service'][1] = 'Layanan harus diisi.'; 
$this->errorMsg['service'][2] = 'Nama layanan telah terdaftar. Silahkan memilih nama lain.';  

$this->errorMsg['serviceOrder'][1] = 'Order penjualan jasa harus diisi.';  
$this->errorMsg['serviceOrder'][2] = 'Order penjualan jasa tidak dapat dibatalkan karena sudah terjadi SPK. Silahkan membatalkan SPK terlebih dahulu.';  

$this->errorMsg['shipper'][1] = 'Nama shipper harus diisi.';
$this->errorMsg['shipper'][2] = 'Nama shipper telah terdaftar. Silahkan memilih nama shipper lain.';
$this->errorMsg['shipper'][3] = 'Shipper tidak sama.';

$this->errorMsg['slot'][1] = 'Slot harus diisi.';
$this->errorMsg['slot'][2] = 'Slot harus lebih besar atau sama dengan 0.'; 
$this->errorMsg['slot'][3] = 'Slot harus lebih besar dari 0.'; 

$this->errorMsg['serialnumber'][1] = 'SN tidak boleh kosong.';  
$this->errorMsg['serialnumber'][2] = 'Jumlah SN tidak cocok.';  
$this->errorMsg['serialnumber'][3] = 'SN telah terdaftar. Silahkan memilih SN lain.';  
$this->errorMsg['serialnumber'][4] = 'SN tidak terdaftar. Silahkan memilih SN lain.';  
$this->errorMsg['serialnumber'][6] = 'SN dan part number vendor tidak cocok.';

$this->errorMsg['script'][1] = 'Script harus diisi.'; 

$this->errorMsg['shipment'][1] = 'Nama pengiriman harus diisi.';
$this->errorMsg['shipment'][2] = 'Nama pengiriman telah terdaftar. Silahkan memilih nama pengiriman lain.';
$this->errorMsg['shipment'][3] = 'Layanan pengiriman telah pernah digunakan dalam transaksi sehingga tidak bisa dihapus.'; 
$this->errorMsg['shipment'][4] = 'Pengiriman tidak tersedia.';

$this->errorMsg['shipmentTracking'][1] = 'nomor tracking harus diisi.'; 

$this->errorMsg['subject'][1] = 'Subyek harus diisi.';
  
$this->errorMsg['supplier'][1] = 'Nama pemasok harus diisi.';  
$this->errorMsg['supplier'][2] = 'Nama pemasok telah terdaftar. Silahkan memilih nama lain.'; 

$this->errorMsg['consignee'][1] = 'Nama consignee harus diisi.';
$this->errorMsg['consignee'][2] = 'Nama consignee telah terdaftar. Silahkan memilih nama lain.'; 

$this->errorMsg['title'][1] = 'Judul harus diisi.';
$this->errorMsg['title'][2] = 'Judul telah terdaftar. Silahkan memilih judul lain.'; 

$this->errorMsg['top'][1] = 'Cara pembayaran harus diisi.';  
$this->errorMsg['top'][2] = 'Cara pembayaran telah terdaftar. Silahkan memilih cara pembayaran lain.';    
 
$this->errorMsg['trucking'][1] = 'Trucking harus diisi.';  

$this->errorMsg['truckingServiceOrder'][3] = 'Biaya telah pernah dikeluarkan.';   
$this->errorMsg['truckingServiceOrder'][4] = 'Jumlah partai yang selesai lebih kecil dari jumlah partai SPK.';   
$this->errorMsg['truckingServiceOrder'][5] = 'Job Order tidak dalam status PROSES SPK atau SPK SELESAI.';   
$this->errorMsg['truckingServiceOrder'][6] = 'Status Job Order tidak valid.';   
$this->errorMsg['truckingServiceOrder'][7] = 'Mobil tidak terdaftar di SPK';   
$this->errorMsg['truckingServiceOrder'][8] = 'Kas keluar belum dikonfirmasi';

$this->errorMsg['truckingServiceOrderInvoice'][2] = 'Job Order tidak dalam status SIAP DIFAKTUR.';   
$this->errorMsg['truckingServiceOrderInvoice'][3] = 'Job Order tidak sesuai dengan data pelanggan.';   
$this->errorMsg['truckingServiceOrderInvoice'][4] = 'Faktur terhubung dengan faktur lain.';   
$this->errorMsg['truckingServiceOrderInvoice'][5] = 'Jenis pajak tidak sama dengan faktur sebelumnya.';   
$this->errorMsg['truckingServiceOrderInvoice'][6] = 'Jumlah faktur sebagian berbeda dengan sebelumnya.';   
$this->errorMsg['truckingServiceOrderInvoice'][7] = 'Referensi faktur memiliki pelanggan yang berbeda.';   


$this->errorMsg['disposalSalesInvoice'][3] = 'Job Order tidak sesuai dengan data pelanggan.';  
$this->errorMsg['disposalSalesInvoice'][4] = 'Referensi faktur memiliki pelanggan yang berbeda.';   

$this->errorMsg['truckingServiceWorkOrder'][1] = 'SPK harus diisi.';   
$this->errorMsg['truckingServiceWorkOrder'][2] = 'SPK belum selesai.';   
$this->errorMsg['truckingServiceWorkOrder'][3] = 'Biaya telah pernah dikeluarkan.';   
$this->errorMsg['truckingServiceWorkOrder'][4] = 'Pemasok tidak sesuai dengan order pembelian.';    
$this->errorMsg['truckingServiceWorkOrder'][5] = 'Transaksi tidak dapat diselesaikan karena masih terdapat kas keluar dalam status MENUNGGU.';    
$this->errorMsg['truckingServiceWorkOrder'][6] = 'Penerima hanya boleh salah satu dari karyawan atau pemasok.';    
$this->errorMsg['truckingServiceWorkOrder'][7] = 'Biaya melebihi batas yang ditentukan.';  
$this->errorMsg['truckingServiceWorkOrder'][8] = 'Biaya outsource tidak boleh melebihi uang muka outsource.'; 
$this->errorMsg['truckingServiceWorkOrder'][9] = 'Detail layanan tidak dapat diubah karena SPK tidak dalam status MENUNGGU.'; 
$this->errorMsg['truckingServiceWorkOrder'][10] = 'Ongkos trucking harus diisi.'; 
$this->errorMsg['truckingServiceWorkOrder'][11] = 'Dokumen tidak lengkap.'; 
$this->errorMsg['truckingServiceWorkOrder'][12] = 'Harga trucking tidak dapat diubah karena sudah terdapat order pembelian.'; 
$this->errorMsg['truckingServiceWorkOrder'][13] = 'Pemasok tidak dapat diubah karena sudah terdapat order pembelian.'; 
$this->errorMsg['truckingServiceWorkOrder'][14] = 'SPK tidak dapat diubah karena sudah terdapat order pembelian.'; 
$this->errorMsg['truckingServiceWorkOrder'][15] = 'Jenis dan nilai biaya tidak bisa diubah karena sudah terdapat order pembelian.'; 
$this->errorMsg['truckingServiceWorkOrder'][16] = 'Pemasok tidak bisa diubah karena sudah terdapat order pembelian.'; 
$this->errorMsg['truckingServiceWorkOrder'][17] = 'Biaya yang telah difaktur tidak dapat diubah / dihapus.'; 
$this->errorMsg['truckingServiceWorkOrder'][18] = 'Mobil pengganti tidak boleh sama dengan mobil utama.';
$this->errorMsg['truckingServiceWorkOrder'][19] = 'Progres sopir telah diselesaikan.';
$this->errorMsg['truckingServiceWorkOrder'][20] = 'Progres sopir tidak dapat diselesaikan. Silahkan menyelesaikan progress sebelumnya.';
$this->errorMsg['truckingServiceWorkOrder'][21] = 'Progres sopir telah diselesaikan. Silahkan menyelesaikan progress selanjutnya.';
$this->errorMsg['truckingServiceWorkOrder'][22] = 'Tanggal progress pekerjaan tidak boleh lebih kecil dari';

$this->errorMsg['truckingPurchase'][2] = 'Pemasok tidak sesuai.';   
$this->errorMsg['truckingPurchase'][3] = 'Biaya di SPK telah berubah.';   


$this->errorMsg['url'][1] = 'URL harus diisi.';  
$this->errorMsg['url'][2] = 'URL telah terdaftar. Silahkan memilih URL lain.';   
$this->errorMsg['url'][3] = 'URL tidak valid';   

$this->errorMsg['vendorPartNumber'][1] = 'Part number vendor tidak boleh kosong.';  
$this->errorMsg['vendorPartNumber'][2] = 'Part number vendor telah terdaftar. Silahkan memilih part number lain.';  
$this->errorMsg['vendorPartNumber'][3] = 'Part number vendor tidak sesuai dengan item.';  

$this->errorMsg['warehouse'][1] = 'Nama gudang harus diisi.';
$this->errorMsg['warehouse'][2] = 'Nama gudang telah terdaftar. Silahkan memilih nama gudang lain.';

$this->errorMsg['warehouseTransfer'][2] = 'Gudang asal dan gudang tujuan tidak boleh sama.';
$this->errorMsg['warehouseTransfer'][3] = 'Jumlah asal dan tujuan tidak sama.';

$this->errorMsg['warrantyClaim'][2] = 'Alasan harus diisi.';
$this->errorMsg['warrantyClaim'][3] = 'Garansi sudah kadaluarsa.';
$this->errorMsg['warrantyClaim'][4] = 'Part Number pengganti harus diisi.';
$this->errorMsg['warrantyClaim'][5] = 'Biaya harus lebih besar dari 0.'; 
$this->errorMsg['warrantyClaim'][6] = 'SN pengganti tidak boleh sama dengan SN yang di klaim.';
$this->errorMsg['warrantyClaim'][7] = 'Item pengganti harus sama.';
$this->errorMsg['warrantyClaim'][8] = 'SN pengganti tidak boleh kosong.';

$this->errorMsg['youtube'][1] = 'Judul youtube harus diisi.';
$this->errorMsg['youtube'][2] = 'Judul youtube telah terdaftar. Silahkan memilih judul youtube lain.';
$this->errorMsg['youtube'][3] = 'ID youtube harus diisi.';

$this->errorMsg['warrantyPeriod'][1] = 'Masa garansi harus diisi.';   
$this->errorMsg['warrantyPeriod'][2] = 'Masa garansi harus lebih besar dari 0.';   

$this->errorMsg['waste'][1] = 'Limbah harus diisi';   
$this->errorMsg['waste'][2] = 'Limbah Belum terdaftar di Layanan';   
$this->errorMsg['waste'][3] = 'Limbah tidak sesuai dengan kategori limbah';   


$this->errorMsg['carrier'][1] = 'Pelayaran harus diisi.';
$this->errorMsg['carrier'][2] = 'Nama pelayaran telah terdaftar. Silahkan memilih nama pelayaran lain.';
 
$this->errorMsg['vendorWarrantyClaim'][3] = 'Nama barang yang hendak diganti tidak sesuai dengan histori claim.';
$this->errorMsg['vendorWarrantyClaim'][4] = 'SN tidak sesuai.';

$this->errorMsg['vendorWarrantyClaimReturn'][2] = 'SN pengganti harus diisi.';
    
$this->errorMsg['volume'][1] = 'Volume harus diisi.';
$this->errorMsg['volume'][2] = 'Volume harus lebih besar dari 0.';
 
$this->errorMsg['itemIn'][1] = 'Pemasukan barang harus diisi.'; 
$this->errorMsg['itemIn'][2] = 'Pemasukan barang telah memiliki penerimaan barang. Silahkan membatalkan penerimaan barang terlebih dahulu.';

$this->errorMsg['itemOut'][1] = 'Pengeluaran barang harus diisi.'; 
$this->errorMsg['itemOut'][2] = 'Pengeluaran barang telah memiliki pengiriman barang. Silahkan membatalkan pengiriman barang terlebih dahulu.';  

$this->errorMsg['itemInReceive'][2] = 'Tanggal penerimaan barang harus lebih besar atau sama dengan tanggal pemasukan barang.';  

$this->errorMsg['itemOutDelivery'][2] = 'Tanggal pengiriman barang harus lebih besar atau sama dengan tanggal pengeluaran barang.';  
 
$this->errorMsg['issue'][1] = 'Masalah harus diisi.';   

$this->errorMsg['membership'][1] = 'Keanggotaan tidak valid.';   

$this->errorMsg['membershipAttendance'][2] = 'Registrasi Keanggotaan telah memiliki Kehadiran Keanggotaan. Silahkan membatalkan Kehadiran Keanggotaan terlebih dahulu.';  

$this->errorMsg['customerMembership'][1] = 'Registrasi Keanggotaan harus diisi.';  
$this->errorMsg['customerMembership'][2] = 'Registrasi Keanggotaan telah memiliki Transaksi Voucher. Silahkan membatalkan Transaksi Voucher terlebih dahulu.';  
$this->errorMsg['customerMembership'][3] = 'Tanggal kehadiran tidak didalam periode keanggotaan.';  
$this->errorMsg['customerMembership'][4] = 'Keanggotaan tidak valid.';   

$this->errorMsg['warrantyClaimProgress'][2] = 'Jenis barang harus sama.';  

$this->errorMsg['RMA'][1] = 'No. RMA harus diisi.';
$this->errorMsg['RMA'][2] = 'No. RMA telah terdaftar. Silahkan memilih no. RMA lain.';


$this->errorMsg['width'][2] = 'Lebar harus lebih besar atau sama dengan 0.'; 
$this->errorMsg['length'][2] = 'Panjang harus lebih besar atau sama dengan 0.'; 
$this->errorMsg['height'][2] = 'Tinggi harus lebih besar atau sama dengan 0.'; 

$this->errorMsg['routineCost'][1] = ''; 
$this->errorMsg['routineCost'][2] = 'Deskripsi biaya harus diisi.'; 

$this->errorMsg['voucher'][2] = 'Nilai voucher harus lebih besar dari 0.'; 
$this->errorMsg['voucher'][3] = 'Status voucher tidak valid.'; 
$this->errorMsg['voucher'][4] = 'Voucher melebihi kuota yang disediakan.'; 
$this->errorMsg['voucher'][5] = 'Kepemilikan voucher tidak sesuai.'; 
$this->errorMsg['voucher'][6] = 'Voucher tidak valid.'; 
$this->errorMsg['voucher'][7] = 'Keanggotaan Anda belum dapat menggunakan voucher.'; 
$this->errorMsg['voucher'][8] = 'Voucher telah kadaluarsa.'; 
$this->errorMsg['voucher'][9] = 'Nilai pembelian tidak memenuhi minimal transaksi.'; 

$this->errorMsg['technician'][1] = 'Teknisi harus diisi.';

$this->errorMsg['installationworkorder'][2] = 'Penjualan SC tidak dalam status konfirmasi.';
$this->errorMsg['installationworkorder'][3] = 'Jumlah terpakai harus lebih kecil dari jumlah trasansaksi.';


$this->errorMsg['invoicePeriod'][1] = 'Periode harus diisi.';  
$this->errorMsg['invoicePeriod'][2] = 'Periode telah terdaftar. Silahkan memilih Periode lain.';  
$this->errorMsg['invoicePeriod'][3] = 'Periode invoice harus lebih besar dari 0.';

$this->errorMsg['salesOrderSubscription'][1] = 'Penjualan SC harus diisi.';  
$this->errorMsg['salesOrderSubscription'][2] = 'Penjualan SC tidak dalam status konfirmasi.';
$this->errorMsg['salesOrderSubscription'][3] = 'Penjualan SC akan berubah menjadi terminated secara otomatis jika terjadi terminasi.';
$this->errorMsg['salesOrderSubscription'][4] = 'Penjualan SC tidak dalam status selesai.';
$this->errorMsg['salesOrderSubscription'][5] = 'Penjualan SC tidak dalam status selesai atau onhold.';

$this->errorMsg['quiz'][2] = 'Semua pertanyaan harus diisi.';

$this->errorMsg['shortDescription'][2] = 'Deskripsi singkat harus lebih dari 50 karakter.';

$this->errorMsg['membershipLevel'][1] = 'Jenis keanggotaan harus diisi.';
$this->errorMsg['membershipLevel'][2] = 'Jenis keanggotaan sudah terdaftar.';

$this->errorMsg['policyNumber'][1] = 'No Polis harus diisi.';
$this->errorMsg['policyNumber'][2] = 'No. Polis telah terdaftar. Silahkan memilih no polis lain.';  

$this->errorMsg['policyNumber'][4] = 'Anda hanya boleh memilih satu asuransi untuk jenis individu.';
$this->errorMsg['policyNumber'][5] = 'Polis sudah terdaftar.';
$this->errorMsg['policyNumber'][6] = 'Anda tidak dapat menambahkan polis manual untuk kategori individu.';

$this->errorMsg['diagnose'][1] = 'Diagnosa harus diisi.';

$this->errorMsg['meetingSchedule'][1] = 'Topik pertemuan harus diisi.';
$this->errorMsg['meetingSchedule'][3] = 'Waktu pertemuan harus lebih besar dari saat ini.';
$this->errorMsg['meetingSchedule'][4] = 'Link meeting harus diisi.';
$this->errorMsg['meetingSchedule'][5] = 'Lokasi meeting harus diisi.'; 
$this->errorMsg['meetingSchedule'][6] = 'Anda sudah terdaftar dimeeting ini.'; 
$this->errorMsg['meetingSchedule'][7] = 'Anda harus memiliki tingkat keanggotaan minimal <b>Host</b> untuk dapat menjadi host meeting.'; 
$this->errorMsg['meetingSchedule'][8] = 'Jumlah peserta (anggota) sudah penuh.'; 
$this->errorMsg['meetingSchedule'][9] = 'Jumlah peserta (guest) sudah penuh.'; 
$this->errorMsg['meetingSchedule'][10] = 'Jumlah peserta sudah penuh.'; 
$this->errorMsg['meetingSchedule'][11] = 'Peserta harus memiliki minimal keanggotaan PRO.'; 
$this->errorMsg['meetingSchedule'][12] = 'Update gagal, meeting sudah selesai.'; 
$this->errorMsg['meetingSchedule'][13] = 'Anda tidak dapat mendaftar dengan meeting yang sedang berlangsung.'; 
$this->errorMsg['meetingSchedule'][14] = 'Anda hanya dapat bergabung ketika meeting sedang berlangsung.'; 

$this->errorMsg['giveOpportunity'][3] = 'Peserta harus memiliki minimal keanggotaan PRO.';  
$this->errorMsg['giveOpportunity'][4] = 'Nilai deal harus diisi.'; 
$this->errorMsg['giveOpportunity'][5] = 'Anda tidak dapat mengirimkan opportunity ke Anda sendiri.'; 

$this->errorMsg['medicalJobOrder'][1] = 'Job order harus diisi.';

$this->errorMsg['medicalRequestClaim'][1] = 'Permintaan harus diisi.'; 
$this->errorMsg['medicalRequestClaim'][3] = 'Layanan membutuhkan penawaran yang telah disetujui.';

$this->errorMsg['medicalSalesOrderQuotation'][3] = 'Layanan tidak terdaftar di permintaan baru / order penjualan.';
$this->errorMsg['medicalSalesOrderQuotation'][4] = 'Layanan sudah memiliki penawaran yang telah disetujui.';

$this->errorMsg['meetingPoint'][1] = 'Lokasi meeting harus diisi.';
$this->errorMsg['meetingPoint'][2] = 'Lokasi meeting telah terdaftar. Silahkan memilih lokasi meeting lain.';
 
$this->errorMsg['businessCategory'][1] = 'Kategori usaha harus diisi. Silahkan update kategori usaha anda di menu update profile.';
$this->errorMsg['businessCategory'][2] ='Kategori usaha telah terdaftar. Silahkan memilih kategori usaha lain.';

$this->errorMsg['costReconsile'][2] = 'Jumlah realisasi tidak valid.';
$this->errorMsg['costReconsile'][4] = 'Tanggal realisasi harus lebih besar dari tanggal pembelian.';
$this->errorMsg['costReconsile'][5] = 'Mata uang tidak sesuai.';
$this->errorMsg['costReconsile'][6] = 'Biaya telah direalisasi.';

$this->errorMsg['cobrokePercentage'][1] = 'Total persentasi co-broke harus 100%.';

$this->errorMsg['salesOrder'][3] = 'Pengiriam dan penerima tidak boleh sama.';

$this->errorMsg['hbl'][1] = 'HBL harus diisi.';
$this->errorMsg['hbl'][2] = 'HBL telah terdaftar. Silahkan memilih HBL lain.';
$this->errorMsg['hbl'][3] = 'Job Order telah memiliki HBL.';
$this->errorMsg['hbl'][4] = 'No. Container tidak terdaftar di Job Order.'; 
$this->errorMsg['hbl'][5] = 'HBL / Ref HBL. harus diisi salah satu.';
$this->errorMsg['hbl'][6] = 'HBL belum terdaftar.'; 

$this->errorMsg['logisticSalesOrder'][3] = 'Transaksi telah terdaftar di manifest';
$this->errorMsg['logisticSalesOrder'][4] = 'Transaksi akan berubah status menjadi SELESAI secara otomatis jika sudah diproses manifest.';

$this->errorMsg['emklHouseBL'][9] = 'No. HBL telah di input secara manual.'; 

$this->errorMsg['disposalJobOrder'][1] = 'Job Order tidak dalam status menunggu.';  
$this->errorMsg['disposalJobOrder'][2] = 'SPK dalam status konfirmasi.'; 
$this->errorMsg['disposalJobOrder'][3] = 'Faktur telah dibuat.'; 
$this->errorMsg['disposalJobOrder'][4] = 'Jumlah layanan telah melebihi kuota.'; 
$this->errorMsg['disposalJobOrder'][5] = 'Memiliki JO atau AR yang belum dibayar.'; 
 
$this->errorMsg['disposalWorkOrder'][2] = 'Pekerjaan telah difaktur.'; 

$this->errorMsg['disposalWorkOrderDispatcher'][1] = 'Kode Dispatch harus diisi.'; 

$this->errorMsg['truckingCostCashOut'][1] = 'Kas keluar biaya trucking harus diisi.'; 
$this->errorMsg['truckingCostCashOut'][2] = 'Kas keluar sedang dalam pengajuan.';
 
$this->errorMsg['truckingCashOutRequest'][1] = 'Pengajuan kas keluar harus diisi.'; 
$this->errorMsg['truckingCashOutRequest'][2] = 'Nama penerima tidak sama.'; 
$this->errorMsg['truckingCashOutRequest'][3] = 'Tgl. kas keluar harus dalam periode permintaan.'; 

$this->errorMsg['investorRelations'][2] = 'Data untuk periode yang sama telah terdaftar.'; 

$this->errorMsg['corporateValues'][1] = 'Nilai perusahaan harus diisi.';
$this->errorMsg['corporateValues'][2] = 'Nilai perusahaan telah terdaftar.';

$this->errorMsg['recurringPeriod'][1] = 'Nama periode harus diisi.'; 
$this->errorMsg['recurringPeriod'][2] = 'Periode telah terdaftar. Silahkan memilih periode yang lain.';
$this->errorMsg['recurringPeriod'][3] = 'Periode tidak di temukan.';
$this->errorMsg['recurringPeriod'][4] = 'Jumlah periode harus lebih besar dari 0.';

  
$this->errorMsg['salesOrderRecurringSubscription'][1] = '';
$this->errorMsg['salesOrderRecurringSubscription'][2] = 'Subscription tidak dapat di batalkan karena telah terjadi terminasi.';
$this->errorMsg['salesOrderRecurringSubscription'][3] = 'Status subscription tidak dapat di rubah karena telah terjadi terminasi.';
$this->errorMsg['salesOrderRecurringSubscription'][4] = 'Perubahan status gagal, silahkan melalukan perubahan status dari menu Terminasi.';
//$this->errorMsg['salesOrderRecurringSubscription'][5] = 'Subscription tidak dapat di batalkan karena tehubung dengan transaksi lain. Subscription ini akan otomatis di batalkan jika di terminasi.';

$this->errorMsg['salesOrderRecurringSubscriptionTerminate'][1] = 'Kode subscription harus di isi.';
$this->errorMsg['salesOrderRecurringSubscriptionTerminate'][2] = 'Kode tidak di temukan.';

$this->errorMsg['buildingUnit'][1] = 'Unit bangunan harus diisi.';
$this->errorMsg['buildingUnit'][2] = 'Blok harus diisi.';
$this->errorMsg['buildingUnit'][3] = 'Nomor Unit harus diisi.';
$this->errorMsg['buildingUnit'][4] = 'Luas unit harus diisi.';

$this->errorMsg['itemProportional'][1] = 'Barang telah terdaftar. Silahkan memilih barang lain.';
$this->errorMsg['itemProportional'][2] = 'Total persentasi harus lebih dari 0.';
$this->errorMsg['itemProportional'][3] = 'Total persentasi lebih dari 100.';

$this->errorMsg['emklQuotation'][1] = 'Penawaran tidak boleh kosong.';
$this->errorMsg['emklQuotation'][5] = 'Penawaran tidak dalam status Customer Approved.'; 
$this->errorMsg['emklQuotation'][6] = 'Tanggal kadaluarsa haruss lebih besar dari hari ini.';
$this->errorMsg['emklQuotation'][7] = 'Biaya harus lebih besar dari 0.';
$this->errorMsg['emklQuotation'][8] = 'Penawaran telah kadaluarsa.';
$this->errorMsg['emklQuotation'][9] = 'Selling dan cost harus lebih besar dari 0';
$this->errorMsg['emklQuotation'][10] = 'Tax percentage must be same';

$this->errorMsg['port'][1] = 'Nama port tidak boleh kosong.';
$this->errorMsg['port'][2] = 'Nama port sudah terdaftar. Silahkan memilih nama lain.';
$this->errorMsg['port'][5] = 'Nama port tidak sesuai.'; 
$this->errorMsg['port'][6] = 'Nama port tidak boleh sama.';

$this->errorMsg['templateActivity'][1] = 'Tipe data harus diisi.';
$this->errorMsg['templateActivity'][2] = 'Detail tipe data tidak boleh kosong.';
$this->errorMsg['templateActivity'][3] = 'Nama pilihan data harus diisi.';

$this->errorMsg['taxInvoiceBatch'][1] = 'No. awal faktur pajak harus diisi.'; 
$this->errorMsg['taxInvoiceBatch'][2] = 'No. akhir faktur pajak harus diisi.'; 
$this->errorMsg['taxInvoiceBatch'][3] = 'No. awal faktur pajak harus lebih kecil dari no. akhir faktur pajak.'; 

$this->errorMsg['taxInvoiceNumber'][1] = 'Nomor faktur pajak telah terdaftar. Silahkan memilih nomor faktur pajak lain.'; 
$this->errorMsg['taxInvoiceNumber'][2] = 'No. faktur pajak sudah digunakan.'; 
$this->errorMsg['taxInvoiceNumber'][3] = 'No. faktur pajak tidak available.'; 
$this->errorMsg['taxInvoiceNumber'][4] = 'Periode faktur pajak tidak sesuai.'; 
$this->errorMsg['taxInvoiceNumber'][5] = 'Nomor faktur pajak tidak valid.'; 

$this->errorMsg['vatOut'][2] = 'Periode faktur penjualan tidak sesuai.'; 
$this->errorMsg['vatOut'][3] = 'Gudang faktur penjualan tidak sesuai.'; 
$this->errorMsg['vatOut'][4] = 'Unit bisnis tidak sama.'; 
$this->errorMsg['vatOut'][5] = 'Jenis pajak tidak sama.'; 
$this->errorMsg['vatOut'][6] = 'Tanggal faktur penjualan tidak boleh lebih kecil dari tanggal faktur pajak.'; 
$this->errorMsg['vatOut'][7] = 'Faktur sudah pernah dibuatkan Faktur Pajak.';  


$this->errorMsg['activityProgress'][1] = 'Job Order telah diinput.';
$this->errorMsg['activityProgress'][2] = 'Activity Progress status tidak dalam menunggu, silahkan batalkan terlebih dahulu.';
$this->errorMsg['activityProgress'][3] = 'Response harus diisi.';
$this->errorMsg['activityProgress'][4] = 'Aktivitas harus diisi.';

$this->errorMsg['templateActivity'][1] = 'Tipe data harus diisi.';
$this->errorMsg['templateActivity'][2] = 'Detail tipe data tidak boleh kosong.';
$this->errorMsg['templateActivity'][3] = 'Nama pilihan data harus diisi.';

$this->errorMsg['continent'][1] = 'Nama benua harus diisi.';
$this->errorMsg['continent'][2] = 'Nama benua telah terdaftar. Silahkan memilih nama lain.';

$this->errorMsg['country'][1] = 'Nama negara harus diisi.';
$this->errorMsg['country'][2] = 'Nama negara telah terdaftar. Silahkan memilih nama lain.';

$this->errorMsg['commodity'][1] = 'Nama komoditas harus diisi.';

$this->errorMsg['truckingAdditionalCost'][1] = 'SPK tidak dalam status KONFIRMASI.';
$this->errorMsg['truckingAdditionalCost'][2] = 'Job order tidak sesuai dengan data SPK.';
$this->errorMsg['truckingAdditionalCost'][3] = 'Lokasi usaha tidak sesuai dengan data SPK.';
$this->errorMsg['truckingAdditionalCost'][4] = 'Biaya telah di keluarkan.';
$this->errorMsg['truckingAdditionalCost'][5] = 'Sopir tidak sesuai dengan SPK.';
$this->errorMsg['color'][1] = 'Nama warna harus diisi.';  
$this->errorMsg['color'][2] = 'Warna telah terdaftar. Silahkan memilih warna lain.';  

$this->errorMsg['plating'][1] = 'Nama lapisan harus diisi.';  
$this->errorMsg['plating'][2] = 'Nama lapisan telah terdaftar. Silahkan memilih nama lain.'; 
 
$this->errorMsg['material'][1] = 'Nama material harus diisi.';  
$this->errorMsg['material'][2] = 'Nama material telah terdaftar. Silahkan memilih nama lain.';  

$this->errorMsg['texture'][1] = 'Nama tekstur harus diisi.';  
$this->errorMsg['texture'][2] = 'Nama tekstur telah terdaftar. Silahkan memilih nama lain.';  

$this->errorMsg['model'][1] = 'Nama model harus diisi.';  
$this->errorMsg['model'][2] = 'Nama model telah terdaftar. Silahkan memilih nama lain.';  


$this->errorMsg['character'][1] = 'Nama karakter harus diisi.';  
$this->errorMsg['character'][2] = 'Nama karakter telah terdaftar. Silahkan memilih nama lain.';  

$this->errorMsg['ringSize'][1] = 'Nama ukuran harus diisi.';  
$this->errorMsg['ringSize'][2] = 'Nama ukuran telah terdaftar. Silahkan memilih nama lain.';  

$this->errorMsg['packaging'][1] = 'Nama kemasan harus diisi.';  
$this->errorMsg['packaging'][2] = 'Nama kemasan telah terdaftar. Silahkan memilih nama lain.';

$this->errorMsg['itemVariation'][1] = 'Nama variasi harus diisi.';  
$this->errorMsg['itemVariation'][2] = 'Nama variasi telah terdaftar. Silahkan memilih nama lain.';

$this->errorMsg['age'][1] = 'Usia harus diisi.';  
$this->errorMsg['age'][2] = 'Usia telah terdaftar. Silahkan memilih usia lain.';

$this->errorMsg['jobProgress'][1] = 'Nama progress harus diisi'; 
$this->errorMsg['jobProgress'][2] = 'Nama progres telah terdaftar. Silahkan memilih nama lain.';

$this->errorMsg['itemPosition'][1] = 'Nama posisi harus diisi.';  
$this->errorMsg['itemPosition'][2] = 'Nama posisi telah terdaftar. Silahkan memilih nama posisi lain.';

$this->errorMsg['itemPosition'][1] = 'Position name cannot be empty.';
$this->errorMsg['itemPosition'][2] = 'The position name already exists. Please choose another position name.';

$this->lang['itemPosition'] = 'Item Position';
$this->lang['itemPosition'] = 'Posisi Barang';

// WEB CONTENT 
$this->lang['2FAuthentication'] = 'Autentikasi Dua Langkah'; 
$this->lang['2FDisabled'] = 'Autentikasi Dua Faktor Tidak Aktif'; 
$this->lang['2FEnabled'] = 'Autentikasi Dua Faktor Aktif'; 
$this->lang['AC'] = 'AC';  
$this->lang['AH'] = 'AH';  
$this->lang['APAgingReport'] = 'Laporan Umur Hutang';     
$this->lang['APPaymentReport'] = 'Laporan Pembayaran Hutang' ;
$this->lang['APReport'] = 'Laporan Hutang' ;
$this->lang['ARAccount'] = 'Akun Piutang'; 
$this->lang['ARAgingReport'] = 'Laporan Umur Piutang';      
$this->lang['ARPaymentReport'] = 'Laporan Pembayaran Piutang' ;
$this->lang['ARReport'] = 'Laporan Piutang' ;
$this->lang['ARSOAReport'] = 'Laporan AR SOA';
$this->lang['GL'] = 'GL';
$this->lang['GPS'] = 'GPS';
$this->lang['GPSID'] = 'ID GPS';
$this->lang['GPSTracker'] = 'Pelacakan GPS'; 
$this->lang['IDInformation'] = 'Informasi sesuai KTP'; 
$this->lang['IDNumber'] = 'No. Identitas'; 
$this->lang['JOCode'] = 'Kode JO';   
$this->lang['PEB'] = 'PEB'; 
$this->lang['PPN'] = 'PPN';
$this->lang['RMANumber'] = 'No. RMA'; 
$this->lang['TL'] = 'TL'; 
$this->lang['WOCode'] = 'Kode SPK'; 
$this->lang['aboutus'] = 'Tentang Kami' ;
$this->lang['acPackage'] = 'Paket AC';
$this->lang['account'] = 'Akun'; 
$this->lang['accountActivation'] = 'Aktivasi Akun' ;
$this->lang['accountActivationSuccessful'] = 'Selamat, akun Anda telah berhasil diaktivasi!<br>Anda sekarang dapat mengakses fitur member kami. Terima Kasih.';
$this->lang['accountCode'] = 'Kode Akun'; 
$this->lang['accountInformation'] = 'Informasi Akun' ;
$this->lang['accountName'] = 'Nama Akun'; 
$this->lang['accountRecovery'] = 'Pemulihan Akun' ;
$this->lang['accountsPayable'] = 'Hutang' ;
$this->lang['accountsPayableBalance'] = 'Sisa Hutang';
$this->lang['accountsPayablePayment'] = 'Pembayaran Hutang' ; 
$this->lang['accountsPayablePaymentReport'] = 'Laporan Pembayaran Hutang' ; 
$this->lang['accountsPayableReport'] = 'Laporan Hutang' ;
$this->lang['accountsReceivable'] = 'Piutang' ;
$this->lang['accountsReceivablePayment'] = 'Pembayaran Piutang' ;
$this->lang['accountsReceivablePaymentReport'] = 'Laporan Pembayaran Piutang' ;
$this->lang['accountsReceivableReport'] = 'Laporan Piutang' ;
$this->lang['accu'] = 'Accu';  
$this->lang['accuCheck'] = 'Pengecekan Accu';  
$this->lang['action'] = 'Aksi' ;
$this->lang['actionTime'] = 'Waktu Perubahan' ;
$this->lang['activationEmail'] = 'Email Aktivasi' ; 
$this->lang['activity'] = 'Aktivitas'; 
$this->lang['add'] = 'Tambah' ;
$this->lang['addCustomer'] = 'Tambah Pelanggan' ;
$this->lang['addReimburse'] = 'Tambah Reimburse';
$this->lang['addReimburseCost'] = 'Tambah Biaya Reimburse';
$this->lang['addRows'] = 'Tambah Baris' ; 
$this->lang['addSearchFilter'] = 'Tambah Filter Pencarian';
$this->lang['addToCart'] = 'Tambah ke Keranjang' ;
$this->lang['additional'] = 'Tambahan'; 
$this->lang['additionalCost'] = 'Biaya Tambahan';
$this->lang['address'] = 'Alamat' ;
$this->lang['adjustment'] = 'Penyesuaian'; 
$this->lang['after'] = 'setelah';  
$this->lang['afterSales'] = 'Purna Jual'; 
$this->lang['agent'] = 'Agent';
$this->lang['aging'] = 'Aging';    
$this->lang['airFilter'] = 'Filter Udara';  
$this->lang['airwayBill'] = 'Airway Bill';
$this->lang['alias'] = 'Alias'; 
$this->lang['allCOA'] = 'Semua COA';
$this->lang['allCategories'] = 'Semua Kategori' ;
$this->lang['allLocation'] = 'Semua Lokasi';
$this->lang['allProducts'] = 'Semua Produk';
$this->lang['allBrands'] = 'Semua Merk';
$this->lang['allMarketplace'] = 'Semua Marketplace';
$this->lang['allWarehouse'] = 'Semua Gudang';
$this->lang['amount'] = 'Jumlah';
$this->lang['ap'] = 'Hutang';  
$this->lang['ap/ar'] = 'Hutang Piutang';
$this->lang['apAccount'] = 'Akun Hutang'; 
$this->lang['apCode'] = 'Kode Hutang'; 
$this->lang['ar'] = 'Piutang';
$this->lang['ar/ap'] = 'Hutang / Piutang' ;
$this->lang['arAccount'] = 'Akun Piutang'; 
$this->lang['arCode'] = 'Kode Piutang'; 
$this->lang['article'] = 'Artikel';
$this->lang['articles'] = 'Artikel';
$this->lang['articleCategory'] = 'Kategori Artikel'; 
$this->lang['articleContent'] = 'Isi Artikel'; 
$this->lang['articleNewsAndMedia'] = 'Artikel, Berita & Media'; 
$this->lang['asCost'] = 'Sebagai Biaya'; 
$this->lang['assembly'] = 'Perakitan' ;
$this->lang['assemblyItem'] = 'Perakitan Barang';
$this->lang['asset'] = 'Aset' ;
$this->lang['assetCategory'] = 'Kategori Aset' ;
$this->lang['assetDepreciation'] = 'Depresiasi Aset' ;
$this->lang['assetList'] = 'Daftar Aset' ;
$this->lang['assetPurchaseOrder'] = 'Order Pembelian Aset';
$this->lang['attachment'] = 'Lampiran'; 
$this->lang['authenticationCode'] = 'Kode Autentikasi'; 
$this->lang['availability'] = 'Ketersediaan';
$this->lang['axis'] = 'Sumbu';
$this->lang['back'] = 'Kembali'; 
$this->lang['backTo'] = 'Kembali ke';
$this->lang['backToTop'] = 'Kembali ke Atas';
$this->lang['balance'] = 'Balance'; 
$this->lang['balanceQty'] = 'Jml. Akhir'; 
$this->lang['balanceSheetReport'] = 'Laporan Neraca';
$this->lang['bankAccountName'] = 'Nama Pemegang Rekening';
$this->lang['bankAccountNumber'] = 'No. Rekening';
$this->lang['bankName'] = 'Nama Bank';
$this->lang['banner'] = 'Banner';
$this->lang['baseunit'] = 'Satuan Dasar';
$this->lang['bbmPackage'] = 'Paket BBM'; 
$this->lang['before'] = 'sebelum';  
$this->lang['beforeTax'] = 'Sebelum Pajak';
$this->lang['beginningCOGS'] = 'COGS Awal'; 
$this->lang['beginningQty'] = 'Jml. Awal'; 
$this->lang['bestSellingItems'] = 'Penjualan Barang Terlaris';
$this->lang['billOfMaterials'] = 'Bill of Materials'; 
$this->lang['billingStatement'] = 'Tagihan'; 
$this->lang['bookingDate'] = 'Tgl. Booking'; 
$this->lang['bookingNumber'] = 'No. Booking'; 
$this->lang['bpkbRegisteredName'] = 'Nama BPKB';
$this->lang['bpkbRegisteredNumber'] = 'No. BPKB';
$this->lang['branch'] = 'Cabang';
$this->lang['branchOrFranchise'] = 'Cabang / Franchise'; 
$this->lang['brand'] = 'Merk';
$this->lang['brandList'] = 'Daftar Merk';
$this->lang['bugReport'] = 'Pelaporan Bug'; 
$this->lang['businessLocationAccess'] = 'Akses Lokasi Usaha';
$this->lang['businessPartner'] = 'Rekan Usaha';  
$this->lang['buying'] = 'Pembelian'; 
$this->lang['campaignDate'] = 'Tgl. Campaign';
$this->lang['cancel'] = 'Batal';
$this->lang['cancelReason'] = 'Alasan Pembatalan';
$this->lang['cancellationRate'] = 'Tingkat pembatalan';
$this->lang['capacity'] = 'Kapasitas';  
$this->lang['car'] = 'Mobil';  
$this->lang['carCategory'] = 'Kategori Mobil';
$this->lang['carInformation'] = 'Informasi Mobil'; 
$this->lang['carList'] = 'Daftar Mobil';
$this->lang['carMaintenance'] = 'Perawatan Mobil';
$this->lang['carMaintenanceHistoryReport'] = 'Laporan Histori Perawatan Mobil';
$this->lang['carRegistrationNumber'] = 'No. Polisi';
$this->lang['carReport'] = 'Laporan Mobil'; 
$this->lang['carRevenue'] = 'Pendapatan Mobil';
$this->lang['carSeries'] = 'Seri Mobil';  
$this->lang['carService'] = 'Perbaikan Mobil';
$this->lang['carTurnoverReport'] = 'Laporan Turnover Mobil';   
$this->lang['cargoType'] = 'Jenis Kargo'; 
$this->lang['carrier'] = 'Pelayaran';
$this->lang['carrierBookingNumber'] = 'No. Booking Pelayaran'; 
$this->lang['cart'] = 'Kereta Belanja';
$this->lang['cartSubmitSuccessful'] = 'Pesanan Anda telah berhasil terkirim. Anda akan menerima faktur dan detail cara pembayaran di email Anda segera.';
$this->lang['cash'] = 'Tunai';  
$this->lang['cash/ap'] = 'Kas / Hutang'; 
$this->lang['cashBack'] = 'Cash Back'; 
$this->lang['cashBank'] = 'Kas Bank';
$this->lang['cashBankAccount'] = 'Akun Kas Bank'; 
$this->lang['cashBankAccount'] = 'Akun Kas Bank';   
$this->lang['cashBankRealization'] = 'Realisasi Kas Bank';    
$this->lang['cashBankRealizationReport'] = 'Laporan Realisasi Kas Bank';      
$this->lang['cashBankTransfer'] = 'Transfer Kas Bank';
$this->lang['cashBankTransferReport'] = 'Laporan Transfer Kas Bank';
$this->lang['cashFlowReport'] = 'Laporan Arus Kas';
$this->lang['cashIn'] = 'Kas Masuk';
$this->lang['cashInReport'] = 'Laporan Kas Masuk';
$this->lang['cashMovementReport'] = 'Laporan Pergerakan Kas'; 
$this->lang['cashOut'] = 'Kas Keluar'; 
$this->lang['cashOutCode'] = 'Kode Kas Keluar'; 
$this->lang['cashOutDate'] = 'Tgl. Kas Keluar';
$this->lang['cashOutReport'] = 'Laporan Kas Keluar';
$this->lang['category'] = 'Kategori';
$this->lang['change'] = 'ganti';  
$this->lang['changeStatus'] = 'Ubah Status';
$this->lang['chartNotAvailable'] = 'Grafik tidak tersedia'; 
$this->lang['chartOfAccount'] = 'Daftar Akun';
$this->lang['chassis'] = 'Chassis'; 
$this->lang['chassisCategory'] = 'Kategori Chassis'; 
$this->lang['chassisCategoryList'] = 'Daftar Kategori Chassis'; 
$this->lang['chassisList'] = 'Daftar Chassis'; 
$this->lang['chassisNumber'] = 'No. Rangka';
$this->lang['check'] = 'Cek';
$this->lang['checkOut'] = 'Check Out';
$this->lang['checkingResult'] = 'Hasil Pengecekan';  
$this->lang['chooseStatus'] = 'Pilih Status';
$this->lang['city'] = 'Kota'; 
$this->lang['cityCategory'] = 'Kategori Kota'; 
$this->lang['cityOrLocation'] = 'Kota / Lokasi'; 
$this->lang['cityOrLocationCategory'] = 'Kategori Kota / Lokasi'; 
$this->lang['claim'] = 'Klaim';
$this->lang['claimSettlement'] = 'Penyelesaian Klaim';
$this->lang['claimedItem'] = 'Barang yang di klaim';
$this->lang['clean'] = 'bersihkan';  
$this->lang['clearTag'] = 'Hilangkan Tag';
$this->lang['clickHere'] = 'Klik disini'; 
$this->lang['close'] = 'Tutup'; 
$this->lang['closeAll'] = 'Tutup Semua'; 
$this->lang['closed'] = 'Telah ditutup'; 
$this->lang['closing'] = 'Closing';  
$this->lang['closingDate'] = 'Tgl. Tutup'; 
$this->lang['closingPeriod'] = 'Tutup Buku'; 
$this->lang['coaPrivileges'] = 'Hak Akses COA';
$this->lang['coalink'] = 'COA Link'; 
$this->lang['code'] = 'Kode';
$this->lang['codeSetting'] = 'Pengaturan Kode';
$this->lang['codriver'] = 'Asisten Sopir'; 
$this->lang['codriverCommission'] = 'Komisi Asisten Sopir'; 
$this->lang['cogs'] = 'COGS';
$this->lang['color'] = 'Warna';  
$this->lang['commission'] = 'Komisi'; 
$this->lang['commissionAP'] = 'Hutang Komisi'; 
$this->lang['commissionAPAccount'] = 'Akun Hutang Komisi'; 
$this->lang['commissionCost'] = 'Biaya Komisi'; 
$this->lang['commissionPerUnit'] = 'Komisi Satuan';  
$this->lang['commodity'] = 'Komoditas';  
$this->lang['company'] = 'Perusahaan'; 
$this->lang['companyList'] = 'Daftar Perusahaan';
$this->lang['companyType'] = 'Jenis Perusahaan';
$this->lang['confirm'] = 'Konfirmasi';
$this->lang['consignee'] = 'Consignee'; 
$this->lang['consigneeInformation'] = 'Informasi Consignee';
$this->lang['consigneeReport'] = 'Laporan Consignee'; 
$this->lang['contact'] = 'Kontak';
$this->lang['contactPerson'] = 'Contact Person'; 
$this->lang['contactUs'] = 'Hubungi Kami'; 
$this->lang['contactUsCategory'] = 'Kategori Hubungi Kami';
$this->lang['contactUsSuccessful'] = 'Pesan Anda telah terkirim dan akan kami balas secepatnya.'; 
$this->lang['container'] = 'Container';
$this->lang['containerNumber'] = 'No. Container'; 
$this->lang['content'] = 'Isi Halaman';  
$this->lang['contentOfPackage'] = 'Kelengkapan Produk'; 
$this->lang['duration'] = 'Durasi'; 
$this->lang['contractDuration'] = 'Durasi Kontrak'; 
$this->lang['contractPrice'] = 'Kontrak Harga'; 
$this->lang['cost'] = 'Biaya'; 
$this->lang['expenseAccount'] = 'Akun Biaya';  
$this->lang['costAmount'] = 'Total Biaya'; 
$this->lang['costInformation'] = 'Informasi Biaya'; 
$this->lang['costList'] = 'Daftar Biaya';
$this->lang['costName'] = 'Nama Biaya'; 
$this->lang['revenueName'] = 'Nama Pendapatan'; 
$this->lang['costRate'] = 'Tarif Biaya';   
$this->lang['costReport'] = 'Laporan Biaya'; 
$this->lang['costType'] = 'Jenis Biaya';
$this->lang['credit'] = 'Kredit'; 
$this->lang['creditLimit'] = 'Kredit Limit'; 
$this->lang['curr'] = 'MU'; 
$this->lang['currency'] = 'Mata Uang'; 
$this->lang['currencyList'] = 'Daftar Mata Uang'; 
$this->lang['currencyRate'] = 'Kurs Mata Uang'; 
$this->lang['currencyRate'] = 'Kurs';  
$this->lang['currencyShort'] = 'MU'; 
$this->lang['currentEarning'] = 'Laba Rugi Berjalan'; 
$this->lang['currentPassword'] = 'Password saat ini';
$this->lang['currentRate'] = 'Kurs Sekarang';
$this->lang['currentworkprogressname'] = 'Progres Pekerjaan Sekarang';
$this->lang['customCode'] = 'Kode Kustom';
$this->lang['customer'] = 'Pelanggan'; 
$this->lang['customerCategory'] = 'Kategori Pelanggan';
$this->lang['customerComplain'] = 'Keluhan Pelanggan';  
$this->lang['customerDO'] = 'DO Pelanggan'; 
$this->lang['customerDownpayment'] = 'Uang Muka Pelanggan';
$this->lang['customerDownpaymentReport'] = 'Laporan Uang Muka Pelanggan'; 
$this->lang['customerInformation'] = 'Informasi Pelanggan'; 
$this->lang['customerInformation'] = 'Informasi Pelanggan';  
$this->lang['customerPO'] = 'PO Pelanggan';
$this->lang['customerReport'] = 'Laporan Pelanggan';
$this->lang['dailyReport'] = 'Laporan Harian';
$this->lang['dataHasBeenSuccessfullyDeleted'] = 'Data telah berhasil dihapus.'; 
$this->lang['dataHasBeenSuccessfullyUpdated'] = 'Data telah berhasil diubah.'; 
$this->lang['dataHasBeenSuccessfullySent'] = 'Data telah berhasil dikirim.'; 
$this->lang['date'] = 'Tanggal';
$this->lang['dateAndTime'] = 'Tgl / Jam';
$this->lang['dateOfBirth'] = 'Tgl. Lahir'; 
$this->lang['day'] = 'Hari';
$this->lang['day(s)'] = 'hari';     
$this->lang['days'] = 'Hari'; 
$this->lang['debit'] = 'Debit'; 
$this->lang['debitNote'] = 'Nota Debit'; 
$this->lang['default'] = 'Default';
$this->lang['defaultForShipment'] = 'Default Pengiriman';  
$this->lang['defaultTansactionUnit'] = 'Unit Transaksi (Default)';
$this->lang['delete'] = 'Hapus';
$this->lang['deliveredQty'] = 'Jml. Terkirim'; 
$this->lang['deliveryNotes'] = 'Surat Jalan'; 
$this->lang['depot'] = 'Depot';
$this->lang['depotItemMovementReport'] = 'Laporan Pergerakan Barang Depot';
$this->lang['depotList'] = 'Daftar Depot';
$this->lang['description'] = 'Deskripsi';
$this->lang['deselectAll'] = 'Batalkan Semua Pilihan';
$this->lang['destination'] = 'Tujuan'; 
$this->lang['destinationWarehouse'] = 'Gudang Tujuan';
$this->lang['detail'] = 'Detil';
$this->lang['digit'] = 'Digit';
$this->lang['disable2fAuthentication'] = 'Nonaktifkan Autentikasi Dua Faktor'; 
$this->lang['discount'] = 'Diskon';
$this->lang['discountScheme'] = 'Skema Diskon';
$this->lang['division'] = 'Divisi';
$this->lang['doCode'] = 'Kode DO';
$this->lang['doDocuments'] = 'Dokumen DO';
$this->lang['downloadList'] = 'Daftar Unduh';  
$this->lang['downpayment'] = 'Uang Muka';
$this->lang['downpaymentAccount'] = 'Akun Uang Muka'; 
$this->lang['dpp'] = 'DPP';
$this->lang['driver'] = 'Sopir';
$this->lang['driverCashBank'] = 'Kas/Bank Sopir';
$this->lang['driverCommission'] = 'Komisi Sopir'; 
$this->lang['driverProgressStep'] = 'Daftar Progres Sopir';
$this->lang['driverSummaryReport'] = 'Laporan Rangkuman Sopir';
$this->lang['drivingLicense'] = 'SIM'; 
$this->lang['drivingLicenseExpirationDate'] = 'Masa Berlaku SIM';
$this->lang['dropship'] = 'Dropship'; 
$this->lang['dropshiper'] = 'Dropshiper'; 
$this->lang['duedate'] = 'Jatuh Tempo'; 
$this->lang['duplicate'] = 'Duplikasi';
$this->lang['duplicateData'] = 'Duplikasi Data';
$this->lang['duplicateDeletedData'] = 'Duplikasi Data Terhapus'; 
$this->lang['edit'] = 'Ubah';
$this->lang['editCustomer'] = 'Ubah Pelanggan';
$this->lang['email'] = 'Email';
$this->lang['emailSentSuccessful'] = 'Email telah berhasil terkirim.';
$this->lang['emergencyContact'] = 'Kontak Darurat'; 
$this->lang['employee'] = 'Karyawan'; 
$this->lang['employeeAR'] = 'Piutang Karyawan'; 
$this->lang['employeeARPayment'] = 'Pembayaran Piutang Karyawan'; 
$this->lang['employeeAP'] = 'Hutang Karyawan'; 
$this->lang['employeeAPPayment'] = 'Pembayaran Hutang Karyawan'; 
$this->lang['employeeARAccount'] = 'Akun Piutang Karyawan'; 
$this->lang['employeeAccountsReceivable'] = 'Piutang Karyawan'; 
$this->lang['employeeAccountsReceivablePayment'] = 'Pembayaran Piutang Karyawan';  
$this->lang['employeeAccountsReceivablePaymentReport'] = 'Laporan Pembayaran Piutang Karyawan'; 
$this->lang['employeeAccountsReceivableReport'] = 'Laporan Piutang Karyawan';  
$this->lang['employeeCommission'] = 'Komisi Karyawan'; 
$this->lang['employeeCommissionPayment'] = 'Pembayaran Komisi Karyawan'; 
$this->lang['employeeCommissionPaymentReport'] = 'Laporan Pembayaran Komisi Karyawan'; 
$this->lang['employeeCommissionReport'] = 'Laporan Komisi Karyawan'; 
$this->lang['employeeDivision'] = 'Divisi Karyawan'; 
$this->lang['employeeReport'] = 'Laporan Karyawan';   
$this->lang['employees'] = 'Karyawan'; 
$this->lang['emptyFieldPasswordDontChange'] = 'Kosongkan field <strong>Password Baru</strong> jika Anda tidak ingin merubah password.';
$this->lang['emptyStock'] = 'Stok Kosong';
$this->lang['endingBalance'] = 'Saldo Akhir'; 
$this->lang['eta'] = 'ETA';
$this->lang['etccost'] = 'Biaya Lain'; 
$this->lang['etd'] = 'ETD';   
$this->lang['event'] = 'Event';
$this->lang['eventCategory'] = 'Kategori Event';
$this->lang['expirationDate'] = 'Masa Berlaku'; 
$this->lang['exportExcel'] = 'Ekspor Excel';
$this->lang['exportOrderSheet'] = 'Order Sheet Export';  
$this->lang['exportTemplate'] = 'Export Template';    
$this->lang['externalWorkshop'] = 'Bengkel Luar'; 
$this->lang['faq'] = 'FAQ';
$this->lang['fax'] = 'Fax';
$this->lang['featuredArticle'] = 'Artikel Utama';  
$this->lang['featuredItem'] = 'Produk Unggulan'; 
$this->lang['featuredProducts'] = 'Produk Unggulan'; 
$this->lang['file'] = 'File';
$this->lang['fileDiskUsage'] = 'Penyimpanan File'; 
$this->lang['fileSize'] = 'Ukuran File'; 
$this->lang['files'] = 'File(s)';
$this->lang['filesPerItem'] = 'File / Item'; 
$this->lang['filter'] = 'Filter';
$this->lang['filterCategory'] = 'Kategori Filter';
$this->lang['finalDiscount'] = 'Diskon';
$this->lang['finalPrice'] = 'Harga Akhir';
$this->lang['finance'] = 'Keuangan';
$this->lang['financialInformation'] = 'Informasi Keuangan';
$this->lang['firstPage'] = 'Halaman Pertama';
$this->lang['fixedCost'] = 'Fixed Cost';
$this->lang['fogging'] = 'Fogging';  
$this->lang['followUs'] = 'Follow Kami';
$this->lang['for'] = 'untuk';
$this->lang['forgotPassword'] = 'Lupa Password';
$this->lang['forgotPasswordMessage'] = 'Mohon masukkan alamat email yang Anda gunakan ketika melakukan registrasi.';
$this->lang['format'] = 'Format';
$this->lang['freightTerm'] = 'Penagihan';  
$this->lang['frequentlyAskedQuestions'] = 'Frequently Asked Questions'; 
$this->lang['from'] = 'Dari'; 
$this->lang['fromAccount'] = 'Akun Asal'; 
$this->lang['fullDelivered'] = 'Terkirim Penuh'; 
$this->lang['fullPayment'] = 'Pembayaran Penuh';  
$this->lang['fullReceived'] = 'Terima Penuh';
$this->lang['gallery'] = 'Galeri';
$this->lang['generalInformation'] = 'Informasi Umum'; 
$this->lang['generalJournal'] = 'Jurnal Umum';
$this->lang['generalJournalReport'] = 'Laporan Jurnal Umum'; 
$this->lang['generalLedger'] = 'Buku Besar';
$this->lang['generalLedgerReport'] = 'Laporan Buku Besar'; 
$this->lang['grossProfit'] = 'Laba Kotor'; 
$this->lang['group'] = 'Grup';
$this->lang['handling'] = 'Handling';
$this->lang['handlingRateFCL'] = 'Tarif Handling FCL';
$this->lang['hbl'] = 'HBL';
$this->lang['refHBL'] = 'Ref. HBL';
$this->lang['hblNumber'] = 'No. HBL'; 
$this->lang['height'] = 'Tinggi';
$this->lang['heightShort'] = 'T';
$this->lang['hideDetail'] = 'Sembunyikan Detail';
$this->lang['hideNotAvailableItem'] = 'Sembunyikan Stok Kosong'; 
$this->lang['hidePartial'] = 'Sembunyikan Sebagian';
$this->lang['historyLog'] = 'Log Histori'; 
$this->lang['home'] = 'Beranda';
$this->lang['hour'] = 'Jam';
$this->lang['hour(s)'] = 'Jam';
$this->lang['image'] = 'Gambar'; 
$this->lang['imageCover'] = 'Gambar Cover'; 
$this->lang['imageLink'] = 'Link Gambar'; 
$this->lang['imageSize'] = 'Ukuran Gambar'; 
$this->lang['images'] = 'Gambar'; 
$this->lang['imagesPerItem'] = 'Gambar / Barang'; 
$this->lang['import'] = 'Import'; 
$this->lang['importFrom'] = 'Import Dari';
$this->lang['importItem'] = 'Import Barang';  
$this->lang['importOrderSheet'] = 'Order Sheet Import';  
$this->lang['in'] = 'masuk';  
$this->lang['inStock'] = 'Stok Tersedia';
$this->lang['inThousand'] = 'dalam Ribuan'; 
$this->lang['incomeStatementReport'] = 'Laporan Laba Rugi'; 
$this->lang['indexRandomProductTitle'] = 'Produk Kami';
$this->lang['inhouse'] = 'In-House'; 
$this->lang['inhouseCost'] = 'Biaya In-House'; 
$this->lang['inhouseCostSummary'] = 'Rangkuman Biaya In-House';
$this->lang['insurance'] = 'Asuransi'; 
$this->lang['internalUse'] = 'Penggunaan Internal'; 
$this->lang['internetConencted'] = 'Koneksi berhasil.'; 
$this->lang['internetFailToConnect'] = 'Koneksi gagal, silahkan cek koneksi internet Anda.'; 
$this->lang['invalidLicense'] = 'Lisensi tidak valid'; 
$this->lang['inventory'] = 'Persediaan';
$this->lang['inventoryAccount'] = 'Akun Persediaan';
$this->lang['inventoryAdjustment'] = 'Selisih Persediaan'; 
$this->lang['inventoryList'] = 'Daftar Barang';
$this->lang['inventoryPreorderList'] = 'Item PO'; 
$this->lang['inventoryTempAccount'] = 'Akun Persediaan Sementara';
$this->lang['invoice'] = 'Faktur';
$this->lang['invoiceAmount'] = 'Nilai Faktur';
$this->lang['invoiceCode'] = 'Kode Faktur'; 
$this->lang['invoiceDate'] = 'Tgl. Faktur'; 
$this->lang['invoiceId'] = 'No. Faktur';
$this->lang['invoiceIssued'] = 'Telah Difaktur';  
$this->lang['invoiceNumber'] = 'No. Faktur';
$this->lang['invoiceOutstanding'] = 'Sisa Tagihan';
$this->lang['invoiceReceipt'] = 'Tanda Terima Faktur';
$this->lang['invoiceReference'] = 'Referensi Invoice'; 
$this->lang['invoiceTo'] = 'Tujuan Penagihan';
$this->lang['invoiceType'] = 'Jenis Faktur';
$this->lang['issue'] = 'Masalah';
$this->lang['issueCategory'] = 'Kategori Issue';
$this->lang['item'] = 'Barang'; 
$this->lang['item(s)'] = 'Barang';
$this->lang['itemAdjustment'] = 'Penyesuain Stok Barang'; 
$this->lang['itemAdjustmentReport'] = 'Laporan Penyesuaian Stok Barang'; 
$this->lang['itemCategory'] = 'Kategori Barang';
$this->lang['itemChecklist'] = 'Checklist Item';
$this->lang['itemChecklist'] = 'Item Checklist';  
$this->lang['itemChecklistGroup'] = 'Grup Item Checklist';  
$this->lang['itemCode'] = 'Kode Barang';   
$this->lang['itemDepotList'] = 'Daftar Barang Depot';
$this->lang['itemDetail'] = 'Detail Barang'; 
$this->lang['itemFilter'] = 'Filter Barang';
$this->lang['itemFilterReport'] = 'Laporan Filter Barang'; 
$this->lang['itemHasBeenAddedToCart'] = 'Barang telah ditambahkan ke keranjang';
$this->lang['itemIn'] = 'Pemasukan Barang'; 
$this->lang['itemInCode'] = 'Kode Pemasukan Barang';  
$this->lang['itemInDate'] = 'Tgl. Barang Masuk';
$this->lang['itemInReceive'] = 'Penerimaan Barang';  
$this->lang['itemInReport'] = 'Laporan Pemasukan Barang'; 
$this->lang['itemList'] = 'Daftar Barang';
$this->lang['itemMovement'] = 'Pergerakan Barang';
$this->lang['itemName'] = 'Nama Barang';
$this->lang['itemName'] = 'Nama Barang';    
$this->lang['itemOrService'] = 'Barang / Jasa'; 
$this->lang['itemOut'] = 'Pengeluaran Barang'; 
$this->lang['itemOutCode'] = 'Kode Pengeluaran Barang';  
$this->lang['itemOutDate'] = 'Tgl. Barang Keluar'; 
$this->lang['itemOutDelivery'] = 'Pengiriman Barang'; 
$this->lang['itemOutReport'] = 'Laporan Pengeluaran Barang'; 
$this->lang['itemPackage'] = 'Paket Barang';    
$this->lang['itemPackageReport'] = 'Laporan Barang Paket';
$this->lang['itemReport'] = 'Laporan Barang';
$this->lang['itemReportForMassUpload'] = 'Laporan Barang untuk Mass Upload'; 
$this->lang['itemUnit'] = 'Unit Barang';
$this->lang['items'] = 'Barang';
$this->lang['jobDate'] = 'Tgl. Pekerjaan';
$this->lang['jobInformation'] = 'Informasi Pekerjaan'; 
$this->lang['jobOrder'] = 'Job Order'; 
$this->lang['jobHeader'] = 'Job Header'; 
$this->lang['jobOrderCategory'] = 'Kategori Job Order'; 
$this->lang['jobOrderCode'] = 'Kode Job Order';   
$this->lang['jobOrderNumber'] = 'No Job Order';  
$this->lang['jobOrderDate'] = 'Tgl. Job Order'; 
$this->lang['jobOrderReport'] = 'Laporan Job Order'; 
$this->lang['jobOrderSummary'] = 'Rekapitulasi Job Order';
$this->lang['jobType'] = 'Jenis Pekerjaan';
$this->lang['journalCode'] = 'Kode Jurnal'; 
$this->lang['journalBalancing'] = 'Jurnal Penyesuaian Cutoff'; 
$this->lang['kirExpiredDate'] = 'Tgl. Berlaku KIR';
$this->lang['kirNumber'] = 'KIR';
$this->lang['lastPage'] = 'Halaman Terakhir';
$this->lang['lastRate'] = 'Kurs Terakhir';
$this->lang['lclmaster'] = 'Master LCL'; 
$this->lang['length'] = 'Panjang';
$this->lang['lengthShort'] = 'P';
$this->lang['licenseExpired'] = 'Lisensi telah kadaluarsa';
$this->lang['licenseIsValidUntil'] = 'Lisensi berlaku hingga {{duedate}}';
$this->lang['licenseTaxExpiryDate'] = 'Tgl. Pajak STNK';   
$this->lang['licenseWillExpireInDays'] = 'Lisensi akan berakhir dalam {{duedate}} hari';
$this->lang['life'] = 'life';  
$this->lang['lifespan'] = 'Masa Waktu';  
$this->lang['limited'] = 'Terbatas';
$this->lang['linkto'] = 'Tautan ke';
$this->lang['livingAddress'] = 'Alamat Tinggal';  
$this->lang['loading'] = 'Loading';
$this->lang['location'] = 'Lokasi';
$this->lang['locationCategory'] = 'Kategori Lokasi';
$this->lang['locationReport'] = 'Laporan Lokasi';   
$this->lang['login'] = 'Login';
$this->lang['loginHistory'] = 'Histori Login';
$this->lang['loginRequired'] = 'Anda harus login terlebih dahulu.';
$this->lang['loginSuccessful'] = 'Login berhasil. Anda akan di <em>redirect</em> ke halaman utama.';
$this->lang['logout'] = 'Logout';
$this->lang['lowStock'] = 'Stok Terbatas';
$this->lang['machineNumber'] = 'No. Mesin';
$this->lang['maintenance'] = 'Maintenance';  
$this->lang['maintenanceChecklist'] = 'Maintenance Checklist';  
$this->lang['maintenanceCost'] = 'Biaya Maintenance';
$this->lang['margin'] = 'Margin';
$this->lang['maritalStatus'] = 'Status Pernikahan';  
$this->lang['marketplace'] = 'Marketplace'; 
$this->lang['master'] = 'Master'; 
$this->lang['masterRates'] = 'Daftar Tarif'; 
$this->lang['maturity'] = 'Jatuh Tempo'; 
$this->lang['max'] = 'Maks.';
$this->lang['maxStock'] = 'Stok Maks.';
$this->lang['mbl'] = 'MBL';
$this->lang['mblNumber'] = 'No. MBL';
$this->lang['measurement'] = 'Ukuran';
$this->lang['socialMedia'] = 'Media Sosial';
$this->lang['memoDocuments'] = 'Dokumen Memo';
$this->lang['message'] = 'Pesan';
$this->lang['mileage'] = 'Km';  
$this->lang['mileageMaintenance'] = 'KM Penggantian';   
$this->lang['mileageNextDue'] = 'KM Kembali';   
$this->lang['minStock'] = 'Stok Min.';
$this->lang['minute'] = 'menit';  
$this->lang['mobilePhone'] = 'No. HP';
$this->lang['module'] = 'Modul';
$this->lang['modulePrivileges'] = 'Hak Akses Modul';
$this->lang['month'] = 'Bulan';
$this->lang['monthlySalesReport'] = 'Laporan Penjualan per Bulan'; 
$this->lang['monthlySummaryReport'] = 'Laporan Rangkuman per Bulan'; 
$this->lang['movement'] = 'Pergerakan';
$this->lang['multiPointJobOrder'] = 'Multi Point Job Order';
$this->lang['name'] = 'Nama';
$this->lang['nameOfCost'] = 'Nama Biaya';     
$this->lang['nameOfRate'] = 'Nama Tarif'; 
$this->lang['nationality'] = 'Kewarganegaraan';  
$this->lang['navigate'] = 'Navigasi';  
$this->lang['newPassword'] = 'Password Baru';
$this->lang['newPasswordConfirmation'] = 'Konfirmasi Password Baru';
$this->lang['newQty'] = 'Jml. Baru'; 
$this->lang['newSerialNumber'] = 'Serial Number Baru';
$this->lang['news'] = 'Berita';
$this->lang['newsCategory'] = 'Kategori Berita'; 
$this->lang['newsContent'] = 'Isi berita';  
$this->lang['nextPage'] = 'Halaman Selanjutnya';
$this->lang['noDataFound'] = 'Data tidak ditemukan.';
$this->lang['noDescriptionAvailable'] = 'Deskripsi tidak tersedia'; 
$this->lang['normalPrice'] = 'Harga Normal';
$this->lang['note'] = 'Catatan';
$this->lang['notificationSuccessMessage'] = 'Kami akan menghubungi Anda ketika stok tersedia.';
$this->lang['notifyMe'] = 'Informasikan saya ketika tersedia.';
$this->lang['number'] = 'No.'; 
$this->lang['oil'] = 'oli';  
$this->lang['oilFilter'] = 'Filter Oli';  
$this->lang['oilIn'] = 'oil mesin masuk';  
$this->lang['oilOut'] = 'oil mesin keluar';  
$this->lang['oilType'] = 'Jenis Oli';
$this->lang['oldPassword'] = 'Password Lama';
$this->lang['open'] = 'Open';   
$this->lang['openingBalance'] = 'Saldo Awal'; 
$this->lang['operationalAR'] = 'Piutang Operasional'; 
$this->lang['operationalCost'] = 'Biaya Operasional'; 
$this->lang['opsCashBank'] = 'Kas/Bank Ops.';
$this->lang['order'] = 'Urutan';
$this->lang['orderList'] = 'Urutan';
$this->lang['orderNumber'] = 'No. Order'; 
$this->lang['orderSheet'] = 'Order Sheet';  
$this->lang['printOrderSheet'] = 'Cetak Order Sheet';  
$this->lang['jobOrderImport'] = 'Job Order Import';  
$this->lang['jobOrderExport'] = 'Job Order Export';
$this->lang['orderedQty'] = 'Jml. Dipesan'; 
$this->lang['origin'] = 'Asal'; 
$this->lang['otherCost'] = 'Biaya Lain'; 
$this->lang['otherDocuments'] = 'Dokumen Lain';
$this->lang['otherRevenue'] = 'Pendapatan Lain'; 
$this->lang['others'] = 'Lain-Lain';
$this->lang['othersInformation'] = 'Informasi Lainnya';
$this->lang['othersOption'] = 'Opsi Lainnya'; 
$this->lang['othersPosition'] = 'Posisi Lain';
$this->lang['ourProducts'] = 'Produk Kami';
$this->lang['out'] = 'keluar';   
$this->lang['outOfStock'] = 'Stok Kosong';
$this->lang['outsource'] = 'Outsource'; 
$this->lang['outsourceCost'] = 'Biaya Outsource'; 
$this->lang['outsourceCostSummary'] = 'Rangkuman Biaya Outsource';
$this->lang['outsourceFee'] = 'Biaya Outsource'; 
$this->lang['outstanding'] = 'Outstanding';
$this->lang['overStock'] = 'Stok Berlebih';
$this->lang['overdueAccountsPayable'] = 'Hutang Jatuh Tempo';
$this->lang['overdueAccountsReceivable'] = 'Piutang Jatuh Tempo';
$this->lang['ownerInformation'] = 'Informasi Pemilik';  
$this->lang['packageName'] = 'Nama Paket';
$this->lang['page'] = 'Halaman';
$this->lang['pageName'] = 'Nama Halaman';  
$this->lang['paidTo'] = 'Dibayarkan Kepada'; 
$this->lang['parent'] = 'Parent';
$this->lang['partChange'] = 'Penggantian Parts';   
$this->lang['partialInvoice'] = 'Faktur Sebagian';  
$this->lang['partialShipment'] = 'Pengiriman Sebagian';
$this->lang['party'] = 'Partai'; 
$this->lang['password'] = 'Password';
$this->lang['passwordConfirmation'] = 'Konfirmasi Password';
$this->lang['pawnSalesOrder'] = 'Order Penjualan Gadai';
$this->lang['pay'] = 'Bayar';  
$this->lang['payableTax23AgingReport'] = 'Laporan Aging Hutang PPH 23';
$this->lang['payableTax23'] = 'Hutang PPH 23';
$this->lang['payableTax'] = 'Hutang PPH';
$this->lang['payableTax23Payment'] = 'Bukti Bayar PPH 23'; 
$this->lang['payableTax23PaymentReport'] = 'Laporan Bukti Bayar PPH 23'; 
$this->lang['payableTax23Report'] = 'Laporan Hutang PPH 23';
$this->lang['payingOffAmount'] = 'Total Pelunasan';
$this->lang['payment'] = 'Pembayaran';
$this->lang['paymentAmount'] = 'Jml. Pembayaran'; 
$this->lang['paymentCode'] = 'Kode Pembayaran';
$this->lang['paymentConfirmation'] = 'Konfirmasi Pembayaran';
$this->lang['paymentConfirmationSuccessful'] = 'Konfirmasi Anda sudah kami terima dan akan kami proses secepatnya.'; 
$this->lang['paymentDate'] = 'Tanggal Pembayaran';
$this->lang['paymentDetail'] = 'Detail Pembayaran'; 
$this->lang['paymentDiscount'] = 'Diskon Pembayaran'; 
$this->lang['paymentMethod'] = 'Metode Pembayaran';
$this->lang['paymentRounding'] = 'Selisih Pembulatan'; 
$this->lang['perDocument'] = 'Per Dokumen';   
$this->lang['perItem'] = 'Per Item';    
$this->lang['period'] = 'Periode';
$this->lang['personalInformation'] = 'Informasi Data Diri';
$this->lang['personincharge'] = 'Penanggung Jawab'; 
$this->lang['phone'] = 'Telepon';
$this->lang['photo'] = 'Foto';
$this->lang['photoID'] = 'Foto ID';
$this->lang['placeAndDateOfBirth'] = 'Tempat, Tgl. Lahir'; 
$this->lang['placeOfBirth'] = 'Tempat Lahir'; 
$this->lang['planner'] = 'Planner';
$this->lang['pleaseReopenAndSaveTheData']= 'Mohon dibuka dan simpan ulang data Anda';
$this->lang['pleasestarttyping']= 'Silahkan mulai mengetik ...'; 
$this->lang['poCode'] = 'Kode PO'; 
$this->lang['poList'] = 'Daftar PO';
$this->lang['poPrice'] = 'Harga PO';
$this->lang['point'] = 'Poin';
$this->lang['pointReport'] = 'Laporan Poin';
$this->lang['pointValue'] = 'Nilai Poin';
$this->lang['pointofsales'] = 'Point of Sales';
$this->lang['port'] = 'Port'; 
$this->lang['portList'] = 'Daftar Port';  
$this->lang['portfolio'] = 'Portfolio';
$this->lang['portfolioCategory'] = 'Kategori Portfolio'; 
$this->lang['position'] = 'Posisi'; 
$this->lang['preorder'] = 'Pre-Order';
$this->lang['preorderSales'] = 'Penjualan PO';
$this->lang['prepaidTax23AgingReport'] = 'Laporan Aging PPH 23 Dibayar Dimuka';
$this->lang['prepaidTax23'] = 'PPH 23 Dibayar Dimuka';
$this->lang['prepaidTax'] = 'PPH Dibayar Dimuka';
$this->lang['prepaidTax23Payment'] = 'Bukti Penerimaan PPH 23';
$this->lang['prepaidTax23PaymentReport'] = 'Laporan Bukti Penerimaan PPH 23'; 
$this->lang['prepaidTax23Report'] = 'Laporan PPH 23 Dibayar Dimuka';
$this->lang['prevQty'] = 'Jml. Awal'; 
$this->lang['previousPage'] = 'Halaman Sebelumnya';
$this->lang['price'] = 'Harga';
$this->lang['priceAdjustment'] = 'Penyesuaian Harga'; 
$this->lang['priceExcludesTax'] = 'Harga tidak termasuk pajak';
$this->lang['pricePerUnit'] = 'Harga Satuan';  
$this->lang['pricelist'] = 'Daftar Harga'; 
$this->lang['print'] = 'Cetak';
$this->lang['printCashOutVoucher'] = 'Cetak Voucher Kas Keluar'; 
$this->lang['printCashOutRequest'] = 'Cetak Permintaan Kas Keluar';  
$this->lang['printDeliveryNote'] = 'Cetak Surat Jalan';
$this->lang['printInvoice'] = 'Cetak Faktur';
$this->lang['printReceipt'] = 'Cetak Tanda Terima';
$this->lang['printTransaction'] = 'Cetak Transaksi';
$this->lang['printVoucher'] = 'Cetak Voucher';
$this->lang['printWorkOrder'] = 'Cetak SPK'; 
$this->lang['privileges'] = 'Hak Akses'; 
$this->lang['productAndService'] = 'Produk & Layanan'; 
$this->lang['productCategories'] = 'Kategori Produk';
$this->lang['productDescription'] = 'Deskripsi Produk';
$this->lang['productInformation'] = 'Informasi Produk'; 
$this->lang['productManagement'] = 'Pengaturan Produk';
$this->lang['products'] = 'Produk';
$this->lang['profile'] = 'Profil';
$this->lang['profit'] = 'Laba';
$this->lang['profitByBrand'] = 'Laba Berdasarkan Merk';
$this->lang['profitByCategory'] = 'Laba Berdasarkan Kategori';
$this->lang['profitByItem'] = 'Laba Berdasarkan Barang';
$this->lang['profitLoss'] = 'Laba / Rugi';
$this->lang['progressInformation'] = 'Informasi Progres';
$this->lang['promo'] = 'Promo';
$this->lang['promoAndCampaign'] = 'Promo & Campaign';
$this->lang['promoItem'] = 'Barang Promo'; 
$this->lang['promoTitle'] = 'Promo Minggu Ini';
$this->lang['publishDate'] = 'Tgl. Tampil'; 
$this->lang['purchase'] = 'Pembelian';
$this->lang['purchaseOrder'] = 'Order Pembelian';
$this->lang['purchaseOrderExport'] = 'Order Pembelian Export';
$this->lang['purchaseOrderImport'] = 'Order Pembelian Import';
$this->lang['purchaseOrderDomestic'] = 'Order Pembelian Domestik';
$this->lang['purchaseReceive'] = 'Penerimaan Pembelian';
$this->lang['purchaseReceiveReport'] = 'Laporan Penerimaan Pembelian';
$this->lang['purchaseRefund'] = 'Refund Pembelian';
$this->lang['truckingPurchaseRefund'] = 'Refund Pembelian (Trucking)';
$this->lang['purchaseRefundReport'] = 'Laporan Refund Pembelian';  
$this->lang['purchaseRetailDiscount'] = 'Diskon Pembelian Barang'; 
$this->lang['purchaseServiceDiscount'] = 'Diskon Pembelian Jasa'; 
$this->lang['purchaseReturn'] = 'Retur Pembelian';
$this->lang['purchasing'] = 'Pembelian';
$this->lang['purchasingCost'] = 'Biaya Pembelian'; 
$this->lang['qoh'] = 'QOH';
$this->lang['qty'] = 'Jml.';
$this->lang['qtyBom'] = 'Jml. BOM';
$this->lang['qtyUsed'] = 'Jml. Digunakan';
$this->lang['quantity'] = 'Jumlah';
$this->lang['quickSearch'] = 'Pencarian Cepat';
$this->lang['rate'] = 'Kurs';  
$this->lang['rateList'] = 'Daftar Tarif'; 
$this->lang['rawItemWarehouse'] = 'Gudang Bahan';
$this->lang['readMore'] = 'Baca Selengkapnya'; 
$this->lang['realization'] = 'Realisasi';   
$this->lang['reasonToClaim'] = 'Alasan Klaim'; 
$this->lang['receivedQty'] = 'Jml. Diterima';
$this->lang['recipient'] = 'Penerima';
$this->lang['refCode'] = 'Kode Ref.';
$this->lang['refDate'] = 'Tgl. Ref.';
$this->lang['refTransactionDate'] = 'Tgl. Ref. Transaksi'; 
$this->lang['reference'] = 'Referensi'; 
$this->lang['poReference'] = 'Referensi PO';  
$this->lang['refresh'] = 'Refresh'; 
$this->lang['register'] = 'Daftar' ;
$this->lang['registration'] = 'Registrasi';
$this->lang['registrationReActivation'] = 'Jika Anda telah pernah registrasi, Anda tidak perlu registrasi ulang.<br>Silahkan klik <a href="/resend-activation" title="Kirim Ulang Aktivasi">link ini</a> untuk mengirimkan ulang email aktivasi.';
$this->lang['registrationSuccessMessage'] = 'Registrasi Anda telah berhasil.';
$this->lang['registrationSuccessActivationMessage'] = 'Registrasi Anda telah berhasil. Anda akan menerima email yang memuat instruksi selanjutnya untuk proses aktivasi.';
$this->lang['reimburse'] = 'Reimburse';
$this->lang['reim'] = 'Reim'; 
$this->lang['relation'] = 'Hubungan';  
$this->lang['religion'] = 'Agama'; 
$this->lang['replacement'] = 'Pengganti';
$this->lang['replacementItem'] = 'Barang Pengganti';
$this->lang['report'] = 'Laporan'; 
$this->lang['requestDemo'] = 'Request Demo';
$this->lang['resend'] = 'Kirim Ulang'; 
$this->lang['resendActivation'] = 'Aktivasi Ulang';
$this->lang['resendActivationMessage'] = 'Mohon masukkan alamat email yang Anda gunakan ketika melakukan registrasi.';
$this->lang['resendActivationSuccessMessage'] = 'Permohonan Anda telah berhasil diproses. Kami telah mengirimkan email yang berisi instruksi untuk melakukan aktivasi ulang. Terima Kasih.';
$this->lang['resetCost'] = 'Reset Biaya';
$this->lang['resetEvery'] = 'Set Ulang Setiap';
$this->lang['resetPassword'] = 'Reset Password';
$this->lang['resetPasswordSuccessMessage'] = 'Permohonan Anda telah berhasil diproses. Kami telah mengirimkan email yang berisi instruksi untuk melakukan reset password. Terima Kasih.';
$this->lang['resetPasswordSuccessful'] = 'Password Anda berhasil di reset! Password baru Anda telah dikirim ke email Anda.';
$this->lang['resetType'] = 'Set Ulang';
$this->lang['resistance'] = 'resistance';  
$this->lang['restockList'] = 'List Restock'; 
$this->lang['resultItemWarehouse'] = 'Gudang Hasil';
$this->lang['retail'] = 'Retail';
$this->lang['retainedEarning'] = 'Laba Rugi Ditahan'; 
$this->lang['revenue'] = 'Pendapatan';
$this->lang['revenueAccount'] = 'Akun Pendapatan';
$this->lang['reverseClosingPeriod'] = 'Reverse Tutup Buku'; 
$this->lang['reward'] = 'Reward';
$this->lang['rewardPoints'] = 'Reward Points'; 
$this->lang['rma'] = 'RMA';
$this->lang['roleTemplate'] = 'Template Hak Akses'; 
$this->lang['route'] = 'Rute';
$this->lang['runningNumber'] = 'No. Berjalan';
$this->lang['saidAmount'] = 'Terbilang'; 
$this->lang['sales'] = 'Penjualan'; 
$this->lang['salesCommission'] = 'Komisi Penjualan'; 
$this->lang['salesCommissionPayment'] = 'Pembayaran Komisi Penjualan'; 
$this->lang['commissionPayment'] = 'Pembayaran Komisi'; 
$this->lang['salesCommissionPaymentReport'] = 'Laporan Pembayaran Komisi Penjualan'; 
$this->lang['salesCommissionReport'] = 'Laporan Komisi Penjualan'; 
$this->lang['salesmanCommission'] = 'Komisi Sales'; 
$this->lang['salesDelivery'] = 'Pengiriman Penjualan';
$this->lang['salesDeliveryReport'] = 'Laporan Pengiriman Penjualan';
$this->lang['salesDiscount'] = 'Diskon Penjualan';
$this->lang['salesGraph'] = 'Grafik Penjualan';
$this->lang['salesInformation'] = 'Informasi Penjualan';
$this->lang['salesInvoice'] = 'Faktur Penjualan'; 
$this->lang['salesWasteInvoice'] = 'Faktur Penjualan Limbah'; 
$this->lang['salesInvoiceReport'] = 'Laporan Faktur Penjualan'; 
$this->lang['salesOrder'] = 'Order Penjualan';
$this->lang['salesOrderDomestic'] = 'Penjualan Domestik';  
$this->lang['salesOrderExport'] = 'Penjualan Export'; 
$this->lang['salesOrderReport'] = 'Laporan Order Penjualan';
$this->lang['salesOrderByGroupReport'] = 'Laporan Grup Order Penjualan'; 
$this->lang['salesRetail'] = 'Penjualan Barang'; 
$this->lang['salesRetailDiscount'] = 'Diskon Penjualan Barang'; 
$this->lang['salesReturn'] = 'Retur Penjualan';
$this->lang['salesReturnReport'] = 'Laporan Retur Penjualan';
$this->lang['salesService'] = 'Penjualan Jasa'; 
$this->lang['salesServiceDiscount'] = 'Diskon Penjualan Jasa'; 
$this->lang['salesTransaction'] = 'Transaksi Penjualan';
$this->lang['salesman'] = 'Sales'; 
$this->lang['primarySalesman'] = 'Sales Utama'; 
$this->lang['save'] = 'Simpan';
$this->lang['saveAndProceed'] = 'Simpan & Proses';  
$this->lang['saveAndProceedTo'] = 'Simpan & Proses ke';    
$this->lang['say'] = 'Terbilang';
$this->lang['seal'] = 'Seal';
$this->lang['sealNumber'] = 'No. Segel'; 
$this->lang['search'] = 'Cari';
$this->lang['searchFilter'] = 'Filter Pencarian';
$this->lang['searchProduct'] = 'Cari Produk';
$this->lang['searchResult'] = 'Hasil Pencarian'; 
$this->lang['second'] = 'Detik';
$this->lang['security'] = 'Keamanan';  
$this->lang['securityPrivileges'] = 'Hak Akses'; 
$this->lang['selectAll'] = 'Pilih Semua';
$this->lang['selling'] = 'Penjualan';   
$this->lang['sellingPrice'] = 'Harga Jual';
$this->lang['sellingRate'] = 'Tarif Jual';
$this->lang['send'] = 'Kirim';
$this->lang['serialNumber'] = 'Serial Number'; 
$this->lang['serialNumberReplacement'] = 'Serial Number Pengganti'; 
$this->lang['series'] = 'Seri';  
$this->lang['service'] = 'Layanan';
$this->lang['serviceAndCostCategory'] = 'Kategori Layanan & Biaya';  
$this->lang['serviceBooking'] = 'Booking Servis';
$this->lang['serviceCategory'] = 'Kategori Layanan';
$this->lang['serviceCategory'] = 'Kategori Layanan'; 
$this->lang['serviceList'] = 'Daftar Layanan'; 
$this->lang['serviceManagement'] = 'Pengaturan Layanan';
$this->lang['serviceName'] = 'Nama Layanan';
$this->lang['serviceOrder'] = 'Order Penjualan Jasa'; 
$this->lang['serviceOrderCategory'] = 'Kategori Penjualan Jasa'; 
$this->lang['serviceOrderInvoiceReport'] = 'Laporan Faktur Penjualan';  
$this->lang['serviceOrderReport'] = 'Laporan Penjualan Jasa';
$this->lang['serviceWorkOrder'] = 'Surat Perintah Kerja'; 
$this->lang['serviceWorkOrderDate'] = 'Tgl. SPK';  
$this->lang['services'] = 'Layanan'; 
$this->lang['setting'] = 'Pengaturan';
$this->lang['settlement'] = 'Penyelesaian';
$this->lang['settlementType'] = 'Jenis Pelunasan'; 
$this->lang['sex'] = 'Jenis Kelamin';  
$this->lang['shipment'] = 'Pengiriman';
$this->lang['shipmentReceipt'] = 'No. Pengiriman';
$this->lang['shipper'] = 'Shipper';  
$this->lang['shipperPEB'] = 'Shipper (PEB)'; 
$this->lang['shipperPIB'] = 'Shipper (PIB)';   
$this->lang['shippingAddress'] = 'Alamat Pengiriman';   
$this->lang['shippingCompany'] = 'Pelayaran';     
$this->lang['shippingCompanyList'] = 'Daftar Pelayaran';   
$this->lang['shippingCost'] = 'Biaya Kirim'; 
$this->lang['shippingCourier'] = 'Kurir Pengiriman'; 
$this->lang['shippingDate'] = 'Tgl. Kirim'; 
$this->lang['shippingFee'] = 'Ongkos Kirim';
$this->lang['shippingInformation'] = 'Informasi Pengiriman';
$this->lang['shippingLabel'] = 'Slip Pengiriman'; 
$this->lang['shippingLine'] = 'Shipping Line'; 
$this->lang['shippingReceipt'] = 'Resi Pengiriman'; 
$this->lang['shopId'] = 'ID Toko';
$this->lang['shoppingOrder'] = 'Shopping Order'; 
$this->lang['shortDescription'] = 'Deskripsi Singkat';
$this->lang['showAll'] = 'Tampilkan Semua';
$this->lang['showDetail'] = 'Tampilkan Detail';
$this->lang['showIn'] = 'Tampilkan di';  
$this->lang['showInPaymentConfirmation'] = 'Tampilkan di Konfirmasi Pembayaran';
$this->lang['showInvoice'] = 'Lihat Faktur';
$this->lang['si'] = 'SI'; 
$this->lang['size'] = 'Ukuran';
$this->lang['slot'] = 'Slot';
$this->lang['snInformation'] = 'Informasi SN';
$this->lang['snMovementReport'] = 'Laporan Pergerakan SN';
$this->lang['soCode'] = 'Kode SO'; 
$this->lang['soldDate'] = 'Tgl. Terjual'; 
$this->lang['sourceWarehouse'] = 'Gudang Asal';
$this->lang['specification'] = 'Spesifikasi';
$this->lang['specificationFile'] = 'File Spesifikasi';
$this->lang['speed'] = 'Kecepatan';
$this->lang['spkDocuments'] = 'Dokumen Surat Jalan';
$this->lang['status'] = 'Status';
$this->lang['stepProgress'] = 'Step Progress'; 
$this->lang['stnkExpiredDate'] = 'Tgl. Berlaku STNK';
$this->lang['stnkNumber'] = 'No. STNK';
$this->lang['stock'] = 'Stok';
$this->lang['stockCardDepotReport'] = 'Laporan Kartu Stok Depot';
// $this->lang['stockCardReport'] = 'Laporan Kartu Stok';
$this->lang['stockCardReport'] = 'Laporan Pergerakan Barang';
$this->lang['stockInformation'] = 'Informasi Stok';
$this->lang['store'] = 'Toko';
$this->lang['storeLocation'] = 'Lokasi Toko';
$this->lang['storeName'] = 'Nama Toko';
$this->lang['stuffing'] = 'Stuffing';
$this->lang['stuffingAndDestuffingDateTime'] = 'Tgl. Muat / Bongkar';
$this->lang['stuffingDate'] = 'Tgl. Stuffing';
$this->lang['stuffingDestuffingInformation'] = 'Informasi Stuffing / Destuffing';
$this->lang['stuffingInformation'] = 'Informasi Stuffing';
$this->lang['destuffingInformation'] = 'Informasi Destuffing';
$this->lang['subject'] = 'Subyek';
$this->lang['submit'] = 'Submit';
$this->lang['subtotal'] = 'Subtotal';
$this->lang['supplier'] = 'Pemasok';
$this->lang['supplierDownpayment'] = 'Uang Muka Pemasok'; 
$this->lang['supplierDownpaymentReport'] = 'Laporan Uang Muka Pemasok'; 
$this->lang['supplierReport'] = 'Laporan Pemasok';
$this->lang['tag'] = 'Tag'; 
$this->lang['tariffList'] = 'Daftar Tarif';
$this->lang['tax'] = 'Pajak';
$this->lang['tax23'] = 'PPH 23'; 
$this->lang['taxIdentificationNumber'] = 'NPWP'; 
$this->lang['taxIn'] = 'Pajak Masukan'; 
$this->lang['taxOut'] = 'Pajak Keluaran'; 
$this->lang['taxRegistrationAddress'] = 'Alamat NPWP'; 
$this->lang['taxRegistrationName'] = 'Nama NPWP'; 
$this->lang['taxRegistrationNumber'] = 'NPWP'; 
$this->lang['technician'] = 'Teknisi';    
$this->lang['technicianSolutions'] = 'Solusi Teknisi';
$this->lang['tempInventory'] = 'Persediaan Sementara'; 
$this->lang['temperature'] = 'suhu';  
$this->lang['temperatureAfter'] = 'Suhu AC (Setelah)';  
$this->lang['temperatureBefore'] = 'Suhu AC (Sebelum)';  
$this->lang['termOfPaymentName'] = 'Nama TOP'; 
$this->lang['terminal'] = 'Terminal';
$this->lang['terminalList'] = 'Daftar Terminal'; 
$this->lang['termofpayment'] = 'Tempo Pembayaran';
$this->lang['mechanism'] = 'Mekanisme'; 
$this->lang['termsAndConditions'] = 'Syarat dan Ketentuan Berlaku'; 
$this->lang['termsandagreements'] = 'Syarat dan Ketentuan';
$this->lang['testimonial'] = 'Testimonial';
$this->lang['thisUserHasNoHistoryOfLogin'] = 'User ini belum memiliki histori login'; 
$this->lang['tidExpiredDate'] = 'Tgl. Berlaku TID';
$this->lang['tidNumber'] = 'TID';
$this->lang['timeLog'] = 'Log Waktu';
$this->lang['title'] = 'Judul'; 
$this->lang['toAccount'] = 'Akun Tujuan'; 
$this->lang['token'] = 'Token';
$this->lang['tools'] = 'Peralatan';
$this->lang['top'] = 'TOP';
$this->lang['topCustomers'] = 'Pelanggan Teratas';
$this->lang['total'] = 'Total';
$this->lang['totalBuying'] = 'Total Pembelian'; 
$this->lang['totalCOGS'] = 'Total COGS';
$this->lang['totalCommission'] = 'Total Komisi';  
$this->lang['totalCost'] = 'Total Biaya';  
$this->lang['totalData'] = 'Total Data';
$this->lang['totalDifference'] = 'Total Selisih';
$this->lang['totalDiscount'] = 'Total Diskon'; 
$this->lang['totalExpense'] = 'Jumlah Biaya'; 
$this->lang['totalIncome'] = 'Jumlah Pendapatan'; 
$this->lang['totalPayment'] = 'Jumlah Pembayaran'; 
$this->lang['totalPoint'] = 'Total Poin'; 
$this->lang['totalSales'] = 'Total Penjualan'; 
$this->lang['totalTrip'] = 'Jml. Rit';
$this->lang['totalVolume'] = 'Total Volume'; 
$this->lang['totalWeight'] = 'Berat Total';
$this->lang['transactionCode'] = 'Kode Transaksi'; 
$this->lang['transactionDate'] = 'Tgl. Transaksi'; 
$this->lang['transactionHistory'] = 'Histori Transaksi'; 
$this->lang['transactionInformation'] = 'Informasi Transaksi'; 
$this->lang['transactionType'] = 'Jenis Transaksi'; 
$this->lang['transactionUnit'] = 'Unit Transaksi'; 
$this->lang['transhipment'] = 'Transhipment'; 
$this->lang['trip'] = 'Rit'; 
$this->lang['trucking'] = 'Trucking';   
$this->lang['truckingCashFlowReportReport'] = 'Laporan Arus Kas Trucking';
$this->lang['truckingCostCashIn'] = 'Kas Masuk Biaya Trucking';  
$this->lang['truckingCostCashOut'] = 'Kas Keluar Biaya Trucking'; 
$this->lang['truckingCostCashOutReport'] = 'Laporan Kas Keluar Biaya Trucking';  
$this->lang['truckingCostList'] = 'Daftar Biaya Trucking'; 
$this->lang['truckingFee'] = 'Ongkos Trucking';
$this->lang['truckingRate'] = 'Tarif Trucking';
$this->lang['truckingRateFCL'] = 'Tarif Trucking FCL';  
$this->lang['truckingServiceList'] = 'Daftar Layanan Trucking'; 
$this->lang['truckingServiceOrderReport'] = 'Laporan Order Trucking'; 
$this->lang['truckingServiceWorkOrder'] = 'Surat Perintah Kerja';
$this->lang['tuneupPackage'] = 'Paket Tune Up';
$this->lang['type'] = 'Jenis';
$this->lang['typeOfJob'] = 'Jenis Pekerjaan';
$this->lang['typesOfFuel'] = 'Jenis Bahan Bakar';  
$this->lang['typesOfOil'] = 'jenis oli';  
$this->lang['underMaintenance'] = 'Dalam Perbaikan';
$this->lang['underMarginSalesOrder'] = 'Penjualan Dibawah Margin'; 
$this->lang['unit'] = 'Unit';
$this->lang['unitName'] = 'Nama Unit';
$this->lang['unproccesedPurchaseOrder'] = 'Pembelian Belum Diproses';
$this->lang['unproccesedGuaranteeLetter'] = 'Guarantee Letter Belum Diproses';
$this->lang['unproccesedNewRequest'] = 'Permintaan Baru Belum Diproses';
$this->lang['unproccesedSalesOrder'] = 'Penjualan Belum Diproses'; 
$this->lang['unproccesedSalesOrderQuotation'] = 'Penawaran Belum Diproses'; 
$this->lang['unproccesedInvoice'] = 'Faktur Penjualan Belum Diproses'; 
$this->lang['unproccesedReminder'] = 'Pengingat Belum Diproses'; 
$this->lang['update'] = 'Update'; 
$this->lang['updateCost'] = 'Update Biaya';
$this->lang['updatePassword'] = 'Ubah Password';
$this->lang['updateProgress'] = 'Update Progres';
$this->lang['updateSearchFilter'] = 'Ubah Filter Pencarian';
$this->lang['url'] = 'url'; 
$this->lang['usageHistory'] = 'Histori Penggunaan';
$this->lang['useInsurance'] = 'Gunakan Asuransi';
$this->lang['user'] = 'User'; 
$this->lang['userPrivileges'] = 'Hak Akses User'; 
$this->lang['username'] = 'Username'; 
$this->lang['value'] = 'Nilai';  
$this->lang['variableSetting'] = 'Pengaturan Variabel';
$this->lang['vat'] = 'VAT'; 
$this->lang['vendorPartNumber'] = 'Part Number Vendor';  
$this->lang['vendorWarrantyClaim'] = 'Klaim Garansi (Vendor)';  
$this->lang['vendorWarrantyClaimReceive'] = 'Penerimaan Klaim Garansi (Vendor)'; 
$this->lang['verificationFailed'] = 'Verifikasi gagal'; 
$this->lang['verificationSuccessful'] = 'Verifikasi berhasil'; 
$this->lang['verify'] = 'Verifikasi';
$this->lang['vessel'] = 'Vessel';   
$this->lang['masterVessel'] = 'Master Vessel';  
$this->lang['feederVessel'] = 'Feeder Vessel';  
$this->lang['vesselList'] = 'Daftar Vessel';
$this->lang['vesselNumber'] = 'No. Vessel';  
$this->lang['via'] = 'Via';  
$this->lang['viewOrEdit'] = 'Lihat / Ubah';
$this->lang['volume'] = 'Volume';
$this->lang['warehouse'] = 'Gudang';
$this->lang['warehouseAccess'] = 'Akses Gudang';
$this->lang['warehousePrivileges'] = 'Hak Akses Gudang';
$this->lang['warehouseTransfer'] = 'Transfer Gudang'; 
$this->lang['warehouseTransferReport'] = 'Laporan Transfer Gudang'; 
$this->lang['warrantyClaim'] = 'Klaim Garansi'; 
$this->lang['warrantyClaimProgress'] = 'Progres Klaim Garansi';
$this->lang['warrantyClaimProgressReport'] = 'Laporan Progres Klaim Garansi';
$this->lang['warrantyExpiredDate'] = 'Tgl. Akhir Garansi';
$this->lang['warrantyPeriod'] = 'Masa Garansi';
$this->lang['warrantySettlement'] = 'Penyelesaian Garansi';
$this->lang['warrantyTermsAndConditions'] = 'Syarat dan Ketentuan Garansi';  
$this->lang['webpage'] = 'Halaman Situs';
$this->lang['websiteAccount'] = 'Akun Website';
$this->lang['webstore'] = 'Webstore';
$this->lang['weight'] = 'Berat';
$this->lang['weightGrams'] = 'Berat (gr)';
$this->lang['welcome'] = 'Selamat Datang';
$this->lang['width'] = 'Lebar';
$this->lang['widthShort'] = 'L';
$this->lang['workDescription'] = 'Keterangan Pengerjaan';  
$this->lang['workOrderCostReport'] = 'Laporan Biaya SPK'; 
$this->lang['workOrderReport'] = 'Laporan SPK'; 
$this->lang['workProgress'] = 'Progres Pekerjaan';
$this->lang['workshopServiceList'] = 'Daftar Layanan Bengkel';  
$this->lang['writeOffAccountsReceivable'] = 'Penghapusan Piutang'; 
$this->lang['year'] = 'Tahun';
$this->lang['youtube'] = 'Youtube';
$this->lang['zipcode'] = 'Kode Pos';
$this->lang['dragToReorder'] = 'Drag untuk merubah urutan';
$this->lang['settingsSaved'] = 'Pengaturan telah disimpan.';
$this->lang['pleaseReopenThisTab'] = 'Silahkan membuka ulang tab ini.';
$this->lang['purchaseOrderReport'] = 'Laporan Order Pembelian';
$this->lang['loginLogReport'] = 'Laporan Histori Login';
$this->lang['ipaddress'] = 'Alamat IP';
$this->lang['pageShort'] = 'Hal.';
$this->lang['transactionLogReport'] = 'Laporan Histori Transaksi';
$this->lang['qtyInUnit'] = 'Jml. Dalam Unit';
$this->lang['salesOrderExportReport'] = 'Laporan Penjualan Export'; 
$this->lang['salesOrderImportReport'] = 'Laporan Penjualan Import'; 
$this->lang['salesOrderDomesticReport'] = 'Laporan Penjualan Domestik'; 
$this->lang['salesOrderInvoiceReceipt'] = 'Tanda Terima Faktur Penjualan'; 
$this->lang['receiptDate'] = 'Tgl. Tanda Terima'; 
$this->lang['created'] = 'Dibuat'; 
$this->lang['createdBy'] = 'Dibuat Oleh'; 
$this->lang['approved'] = 'Disetujui'; 
$this->lang['approvedBy'] = 'Disetujui Oleh'; 
$this->lang['received'] = 'Diterima'; 
$this->lang['messenger'] = 'Kurir';
$this->lang['salesOrderInvoiceReceiptReport'] = 'Laporan Tanda Terima Faktur';
$this->lang['maxFileSizeUpload'] = 'Maks. Ukuran Upload File'; 
$this->lang['maxSizeUploadPerFile'] = 'Maks. Ukuran Upload / File';     
$this->lang['manage'] = 'Atur'; 
$this->lang['diskUsage'] = 'Penggunaan Kapasitas'; 
$this->lang['noSpaceBeingUsed'] = 'Tidak ada penyimpanan'; 
$this->lang['carScheduleReport'] = 'Laporan Jadwal Mobil'; 
$this->lang['conversion'] = 'Konversi';
$this->lang['conversionUnit'] = 'Unit Konversi';
$this->lang['consigneeWarehouse'] = 'Gudang Consignee';
$this->lang['creditNote'] = 'Nota Kredit';
$this->lang['itemAgingReport'] = 'Laporan Umur Barang'; 
$this->lang['changeItemSN'] = 'Ubah SN Item';
$this->lang['old'] = 'Lama';
$this->lang['new'] = 'Baru';
$this->lang['entrustedCar'] = 'Mobil Titipan';
$this->lang['apPeriod'] = 'Periode Hutang';
$this->lang['payingSettlement'] = 'Pelunasan';
$this->lang['printSummary'] = 'Cetak Rangkuman';
$this->lang['routineCost'] = 'Biaya Rutin';
$this->lang['forEach'] = 'Untuk Setiap';
$this->lang['repeatEvery'] = 'Berulang Setiap';
$this->lang['runNow'] = 'Jalankan Sekarang';
$this->lang['TheProcessHasBeenRun'] = 'Proses telah dijalankan';
$this->lang['allCustomer'] = 'Semua Pelanggan';
$this->lang['customerPrivileges'] = 'Hak Akses Pelanggan';
$this->lang['autoCode'] = 'Kode Otomatis';
$this->lang['leasing'] = 'Pembiayaan';
$this->lang['termOfLease'] = 'Lama Pembiayaan';
$this->lang['startingDate'] = 'Tgl. Mulai';
$this->lang['loanAmount'] = 'Jumlah Pinjaman';
$this->lang['installment'] = 'Cicilan';
$this->lang['assetId'] = 'ID Aset' ;
$this->lang['vehicle'] = 'Kendaraan' ;
$this->lang['itemCondition'] = 'Kondisi Barang' ;
$this->lang['noActiveMarketplace'] = 'Tidak ada marketplace yang aktif.' ;
$this->lang['jobDescription'] = 'Deskripsi Pekerjaan' ;
$this->lang['notDue'] = 'Belum Jatuh Tempo' ;
$this->lang['arPeriod'] = 'Periode Piutang' ;
$this->lang['pod'] = 'POD' ;
$this->lang['pol'] = 'POL' ;
$this->lang['templatePurchaseItem'] = 'Template Item Pembelian' ;
$this->lang['auto'] = 'Auto';
$this->lang['cn/dn'] = 'CN / DN';
$this->lang['owner'] = 'Pemilik';
$this->lang['shareProfit'] = 'Bagi Hasil';
$this->lang['templateCustomer'] = 'Template Pelanggan';
$this->lang['templateSupplier'] = 'Template Pemasok';
$this->lang['chooseTemplate'] = 'Pilih Template';
$this->lang['searchTemplate'] = 'Cari Template';
$this->lang['notMovingStock'] = 'Stok Tidak Bergerak';
$this->lang['membership'] = 'Keanggotaan';
$this->lang['maxAttendance'] = 'Maks. Kehadiran';
$this->lang['timeLimit'] = 'Batas Waktu';
$this->lang['yes'] = 'Ya';
$this->lang['no'] = 'Tidak';
$this->lang['choose'] = 'Pilih';
$this->lang['occupation'] = 'Pekerjaan';
$this->lang['terminal'] = 'Terminal';
$this->lang['stuffingDestuffingLocation'] = 'Lokasi Stuffing / Destuffing';
$this->lang['vehicleLicense'] = 'Izin Kendaraan';
$this->lang['vehicleLicenseOverdue'] = 'Surat Kendaraan Jatuh Tempo';
$this->lang['dailyTransactionSummary'] = 'Penjualan Harian';
$this->lang['dailyMarketplaceTransactionSummary'] = 'Penjualan Marketplace Harian';
$this->lang['iAgreeToTermsAndConditions'] = 'Saya setuju dengan syarat dan ketentuan';
$this->lang['membershipAttendance'] = 'Kehadiran Keanggotaan';
$this->lang['membershipType'] = 'Jenis Keanggotaan';
$this->lang['customerMembership'] = 'Keanggotaan Pelanggan'; 
$this->lang['biodata'] = 'Data Diri'; 
$this->lang['attendance'] = 'Kehadiran'; 
$this->lang['expiredOn'] = 'Berakhir pada'; 
$this->lang['checkIn'] = 'Check In'; 
$this->lang['checkInSuccessful'] = 'Check In Berhasil'; 
$this->lang['checkInTime'] = 'Waktu Check In'; 
$this->lang['class'] = 'Kelas'; 
$this->lang['compareProducts'] = 'Bandingkan Produk'; 
$this->lang['quickView'] = 'Lihat'; 
$this->lang['subscribe'] = 'Daftar'; 
$this->lang['searchResultFor'] = 'Hasil pencarian untuk'; 
$this->lang['swift'] = 'SWIFT'; 
$this->lang['warranty'] = 'Garansi'; 
$this->lang['moreDetails'] = 'Detail lebih lengkap'; 
$this->lang['dontHaveAnAccountYet'] = 'Belum punya akun';
$this->lang['createNewAccount'] = 'Buat akun baru'; 
$this->lang['paymentTo'] = 'Pembayaran ke';
$this->lang['bankCode'] = 'Kode Bank';
$this->lang['itemSpecification'] = 'Spesifikasi Barang'; 
$this->lang['referral'] = 'Referral'; 
$this->lang['importAttributes'] = 'Import Atribut';
$this->lang['voucher'] = 'Voucher';
$this->lang['voucherTransaction'] = 'Transaksi Voucher';
$this->lang['voucherAmount'] = 'Nilai Voucher';
$this->lang['minimumTransaction'] = 'Transaksi Minimum';
$this->lang['issuedQty'] = 'Jml. Dikeluarkan';
$this->lang['used'] = 'Terpakai';
$this->lang['allowToCombine'] = 'Dapat Dikombinasikan';
$this->lang['maxDiscount'] = 'Diskon Maksimal';
$this->lang['leaveItBlankForUnlimited'] = 'Kosongkan jika tidak ada batas';
$this->lang['reseller'] = 'Reseller';
$this->lang['endUser'] = 'End User';
$this->lang['customerType'] = 'Jenis Pelanggan';
$this->lang['criteria'] = 'Kriteria';
$this->lang['startDate'] = 'Tanggal Mulai';
$this->lang['endDate'] = 'Tanggal Selesai';
$this->lang['issued'] = 'Dikeluarkan';
$this->lang['usedQty'] = 'Jml. Terpakai';
$this->lang['usedOn'] = 'Digunakan Pada';
$this->lang['membershipRegistration'] = 'Registrasi Keanggotaan';
$this->lang['activationDate'] = 'Tgl. Aktivasi';
$this->lang['compare'] = 'Bandingkan';
$this->lang['blog'] = 'Blog';
$this->lang['customerCode'] = 'Kode Pelanggan';
$this->lang['noSpecificationAvailable'] = 'Spesifikasi tidak tersedia';
$this->lang['registrationCost'] = 'Biaya Registrasi'; 
$this->lang['activeVoucher'] = 'Voucher Aktif';
$this->lang['usedVoucher'] = 'Voucher Terpakai';
$this->lang['nothingToCompare'] = 'Tidak ada yang dibandingkan';
$this->lang['productsComparison'] = 'Perbandingan Produk';
$this->lang['GrossProfitReport'] = 'Laporan Pendapatan Kotor';
$this->lang['API'] = 'API';
$this->lang['serviceCode'] = 'Kode Layanan';
$this->lang['adminFee'] = 'Biaya Admin';
$this->lang['costRateReport'] = 'Laporan Tarif Biaya';
$this->lang['sellingRateReport'] = 'Laporan Tarif Jual';
$this->lang['currencyPreference'] = 'Preferensi Mata Uang';
$this->lang['salesType'] = 'Jenis Penjualan';
$this->lang['partnershipType'] = 'Bentuk Kerja Sama';
$this->lang['onCall'] = 'On Call';
$this->lang['contract'] = 'Kontrak';
$this->lang['apEmployeeCommissionReport'] = 'Laporan Komisi Karyawan';
$this->lang['vehicleChecklist'] = 'Daftar Pengecekan Kendaraan';
$this->lang['youDontHaveAnyJobYet'] = 'Anda belum memiliki pekerjaan';
$this->lang['workOrder'] = 'SPK';
$this->lang['vehicleAvailabilityReport'] = 'Laporan Ketersediaan Mobil';
$this->lang['available'] = 'Tersedia';
$this->lang['availableOnly'] = 'Hanya yang tersedia';
$this->lang['goodCondition'] = 'Baik';
$this->lang['badCondition'] = 'Tidak';
$this->lang['jobList'] = 'Daftar Pekerjaan';
$this->lang['vehicleCondition'] = 'Kondisi Kendaraan';
$this->lang['proceed'] = 'Proses';
$this->lang['pleaseInputWOCodeYouWantToProceed'] = 'Silahkan mengisi No. SPK yang hendak diproses';
$this->lang['verificationCode'] = 'Kode verifikasi'; 
$this->lang['SNAgingReport'] = 'Laporan Aging Serial Number'; 
$this->lang['arapNetting'] = 'Netting Hutang / Piutang'; 
$this->lang['employeeARAP'] = 'Hutang / Piutang Karyawan';
$this->lang['employeeARAPNetting'] = 'Netting Hutang / Piutang Karyawan'; 
$this->lang['netting'] = 'Netting'; 
$this->lang['purchaseOrderExportReport'] = 'Laporan Order Pembelian Export'; 
$this->lang['purchaseOrderImportReport'] = 'Laporan Order Pembelian Import'; 
$this->lang['purchaseOrderDomesticReport'] = 'Laporan Order Pembelian Domestik'; 
$this->lang['commissionPeriod'] = 'Periode Komisi'; 
$this->lang['showInWebstore'] = 'Tampilkan di Webstore'; 
$this->lang['carMaintenanceReport'] = 'Laporan Perawatan Mobil'; 
$this->lang['storefront'] = 'Etalase'; 
$this->lang['jobsDate'] = 'Tgl. Pekerjaan'; 
$this->lang['syncToAllMarketplaces'] = 'Sinkronisasi ke semua marketplace'; 
$this->lang['ritaseSummaryReport'] = 'Laporan Rangkuman Ritase';  
$this->lang['paymentInformation'] = 'Informasi Pembayaran';
$this->lang['APCardReport'] = 'Laporan Kartu Hutang';
$this->lang['ARCardReport'] = 'Laporan Kartu Piutang';
$this->lang['APCommissionCardReport'] = 'Laporan Kartu Hutang Komisi';
$this->lang['voucherCashInCode'] = 'Kode Voucher Kas Masuk';
$this->lang['voucherCashOutCode'] = 'Kode Voucher Kas Keluar';
$this->lang['counterAccount'] = 'Ayat Silang';
$this->lang['counterCashBank'] = 'Ayat Silang';
$this->lang['AROutstanding'] = 'Outstanding Piutang';
$this->lang['attention'] = 'Attention';
$this->lang['PIC'] = 'PIC';
$this->lang['initialCost'] = 'Biaya Pertama';
$this->lang['monthlyCost'] = 'Biaya Bulanan';
$this->lang['salesOrderRental'] = 'Order Sewa';
$this->lang['rentalSales'] = 'Penjualan Sewa';
$this->lang['woDate'] = 'Tgl. SPK';
$this->lang['woStartDate'] = 'Tgl. Mulai Kerja';
$this->lang['woEndDate'] = 'Tgl. Selesai Kerja';
$this->lang['media'] = 'Media';  
$this->lang['jobDetails'] = 'Detail Pekerjaan'; 
$this->lang['supportWorkOrder'] = 'SPK Support';
$this->lang['installationWorkOrder'] = 'SPK Instalasi';
$this->lang['ticketSupport'] = 'Tiket Support';
$this->lang['technician'] = 'Teknisi';
$this->lang['urgency'] = 'Urgensi';
$this->lang['timeUnit'] = 'Satuan Waktu'; 
$this->lang['lostPrice'] = 'Harga Kehilangan';
$this->lang['rentPrice'] = 'Harga Sewa';
$this->lang['stagesProcess'] = 'Tahapan Pekerjaan';
$this->lang['purchaseRequest'] = 'Permintaan Pembelian';
$this->lang['mainAccount'] = 'Akun Utama';
$this->lang['sid'] = 'SID';
$this->lang['salesRentalQuotation'] = 'Penawaran Penjualan (Sewa)';
$this->lang['quotationName'] = 'Nama Penawaran';
$this->lang['quotation'] = 'Penawaran';
$this->lang['requestAmount'] = 'Jumlah Permintaan';
$this->lang['request'] = 'Permintaan';
$this->lang['startTime'] = 'Waktu mulai';
$this->lang['endTime'] = 'Waktu selesai';
$this->lang['review'] = 'Ulasan';
$this->lang['partners'] = 'Partner';
$this->lang['partnersCategory'] = 'Kategori Partner';
$this->lang['jobOpportunities'] = 'Lowongan Pekerjaan';
$this->lang['confirmedDate'] = 'Tgl. Konfirmasi';
$this->lang['language'] = 'Bahasa';
$this->lang['allLanguages'] = 'Semua Bahasa';
$this->lang['featured'] = 'Unggulan';
$this->lang['managementTeam'] = 'Tim Manajemen';
$this->lang['managementStructure'] = 'Struktur Manajemen';
$this->lang['cashAndBankVoucherReport'] = 'Laporan Voucher Kas & Bank';
$this->lang['cashBankNumber'] = 'No Kas Bank';
$this->lang['updating'] = 'Sedang diupdate';
$this->lang['printQRCode'] = 'Cetak Kode QR';
$this->lang['QRCode'] = 'Kode QR';
$this->lang['deliveryWorkOrder'] = 'SPK Pengiriman';
$this->lang['subscriptionStatus'] = 'Status Berlangganan';
$this->lang['invoiceTaxNumber'] = 'No. Faktur Pajak';
$this->lang['paidAmount'] = 'Jumlah Pembayaran';
$this->lang['jobQueue'] = 'Antrian pekerjaan';
$this->lang['campaign'] = 'Campaign';
$this->lang['BAST'] = 'Berita Acara Serah Terima';
$this->lang['cashAndBankVoucher'] = 'Voucher Kas & Bank';
$this->lang['cashInVoucher'] = 'Voucher Masuk Kas/Bank ';
$this->lang['cashOutVoucher'] = 'Voucher Keluar Kas/Bank';
$this->lang['sender'] = 'Pengirim';
$this->lang['minimumStatusRequired'] = 'Minimum status yang dibutuhkan';
$this->lang['freight'] = 'Freight';
$this->lang['cityReport'] = 'Laporan Kota';
$this->lang['cityCategoryReport'] = 'Laporan Kategori Kota';
$this->lang['serviceReport'] = 'Laporan Layanan';
$this->lang['dumper'] = 'Dumper';
$this->lang['project'] = 'Proyek';
$this->lang['distance'] = 'Jarak';
$this->lang['destinationSite'] = 'Lokasi Tujuan'; 
$this->lang['termination'] = 'Terminasi'; 
$this->lang['terminationDate'] = 'Tgl. Terminasi';
$this->lang['representedby'] = 'Diwakili Oleh';
$this->lang['department'] = 'Departemen';
$this->lang['invoiceRecurring'] = 'Invoice Recurring';
$this->lang['printHeader'] = 'Cetak Header';
$this->lang['marketplaceLogReport'] = 'Laporan Histori Marketplace';
$this->lang['success'] = 'Berhasil';
$this->lang['failed'] = 'Gagal';
$this->lang['voucherNumber'] = 'No. Voucher';
$this->lang['qor'] = 'QOR';
$this->lang['salesInvoiceRental'] = 'Faktur Penjualan (Sewa)';
$this->lang['quotationCode'] = 'Kode Penawaran';
$this->lang['requestPickup'] = 'Request Pickup';
$this->lang['delivered'] = 'Terkirim'; 
$this->lang['syncMarketplace'] = 'Sinkronisasi Marketplace'; 
$this->lang['forEachWarehouse'] = 'Untuk setiap gudang'; 
$this->lang['voyage'] = 'Voyage'; 
$this->lang['containerType'] = 'Tipe Container'; 
$this->lang['feeder'] = 'Feeder';
$this->lang['stackArea'] = 'Stack Area';
$this->lang['stuffingIn'] = 'Stuffing In';
$this->lang['stuffingOut'] = 'Stuffing Out';
$this->lang['vendor'] = 'Vendor';
$this->lang['rentalTimesheetReport'] = 'Laporan Timesheet Rental';
$this->lang['time'] = 'Waktu';
$this->lang['salesOrderDumperReport'] = 'Laporan Order Penjualan Dumper'; 
$this->lang['jobOrderHeaderExport'] = 'Job Header Export'; 
$this->lang['jobOrderHeaderExportReport'] = 'Laporan Job Header Export'; 
$this->lang['jobOrderHeaderImport'] = 'Job Header Import'; 
$this->lang['jobOrderHeaderImportReport'] = 'Laporan Job Header Import'; 
$this->lang['jobOrderHeaderDomestic'] = 'Job Header Domestik'; 
$this->lang['jobOrderHeaderDomesticReport'] = 'Laporan Job Header Domestik'; 
$this->lang['stackArea'] = 'Stack Area';
$this->lang['transaction'] = 'Transaksi';
$this->lang['blNumber'] = 'BL Number';
$this->lang['next'] = 'Selanjutnya';
$this->lang['course'] = 'Kelas';
$this->lang['courseList'] = 'Daftar Kelas';
$this->lang['quiz'] = 'Set Pertanyaan';
$this->lang['question'] = 'Pertanyaan';
$this->lang['answer'] = 'Jawaban';
$this->lang['courseCategory'] = 'Kategori Kelas';
$this->lang['containerType'] = 'Jenis Container';
$this->lang['leaveItBlankForDefaultItemName'] = 'Kosongkan jika mengikuti nama barang diatas';
$this->lang['leaveItBlankForDefaultInformation'] = 'Kosongkan jika mengikuti informasi barang diatas';
$this->lang['garageCashVoucherReport'] = 'Laporan Kas Garasi'; 
$this->lang['maintenanceCashVoucherReport'] = 'Laporan Kas Maintenance'; 
$this->lang['printFormJO'] = 'Print JO Form';
$this->lang['cashBankIn'] = 'Kas / Bank Masuk';
$this->lang['cashBankOut'] = 'Kas / Bank Keluar';
$this->lang['revenueList'] = 'Daftar Pendapatan'; 
$this->lang['cash/bank'] = 'Kas / Bank'; 
$this->lang['temporaryAccount'] = 'Ayat Silang'; 
$this->lang['profitLossRate'] = 'Laba / Rugi Selisih Kurs'; 
$this->lang['senderOrRecipient'] = 'Pengirim / Penerima'; 
$this->lang['rememberMe'] = 'Ingat login saya'; 
$this->lang['itemReturn'] = 'Pengembalian barang'; 
$this->lang['cashAdvance'] = 'Kasbon'; 
$this->lang['cashAdvanceRealization'] = 'Realisasi Kasbon'; 
$this->lang['cashBankInReport'] = 'Laporan Kas / Bank Masuk';  
$this->lang['cashBankOutReport'] = 'Laporan Kas / Bank Keluar';  
$this->lang['settlementAccount'] = 'Akun Penyelesaian'; 
$this->lang['recipientAccount'] = 'Akun Penerima';   
$this->lang['cashAdvanceReport'] = 'Laporan Kasbon';
$this->lang['cashAdvanceAmount'] = 'Jml. Kasbon';
$this->lang['cashAdvanceRealizationReport'] = 'Laporan Realisasi Kas Bon';
$this->lang['combo'] = 'Combo';
$this->lang['realizationDate'] = 'Tgl. Realisasi';
$this->lang['prepaidTaxReceiptCode'] = 'Kode Bukti Potong';
$this->lang['prepaidTaxReceiptDate'] = 'Tgl. Bukti Potong';
$this->lang['prepaidTaxReceiptAmount'] = 'Jml. Bukti Potong';
$this->lang['trialBalanceReport'] = 'Laporan Neraca Saldo';
$this->lang['beginningBalance'] = 'Saldo Awal';
$this->lang['endingBalance'] = 'Saldo Akhir';
$this->lang['overPaid'] = 'Kelebihan Pembayaran';
$this->lang['detailsNote'] = 'Catatan Detail';
$this->lang['supplierCommission'] = 'Komisi Pemasok';
$this->lang['commissionAccount'] = 'Akun Komisi';
$this->lang['withholdingCode'] = 'Bukti Potong';
$this->lang['dateRef'] = 'Tgl. Referensi';
$this->lang['activityDate'] = 'Tgl. Aktivitas'; 
$this->lang['itemConversion'] = 'Konversi Barang' ;
$this->lang['uninvoicedSalesOrderReport'] = 'Laporan Order Penjualan Belum Difaktur';
$this->lang['uninvoicedSalesOrderExportReport'] = 'Laporan Order Penjualan Export belum diinvoice';
$this->lang['uninvoicedSOReport'] = 'SO belum diinvoice';
$this->lang['invoiced'] = 'difaktur';
$this->lang['ARCreditNote'] = 'Nota Kredit Piutang';
$this->lang['realizationCode'] = 'Kode Realisasi';
$this->lang['realizationAmount'] = 'Nilai Realisasi';
$this->lang['salesmanPrivileges'] = 'Akses Salesman';
$this->lang['allSalesman'] = 'Semua Salesman';
$this->lang['ARDiscountApproval'] = 'Persetujuan Pemotongan Piutang';
$this->lang['allServices'] = 'Semua Layanan';
$this->lang['submissionDate'] = 'Tgl. Pengajuan';
$this->lang['JOWODate'] = 'Tgl. JO / SPK';
$this->lang['cashOutRef'] = 'Ref. Kas Keluar';
$this->lang['shipmentManifestReport'] = 'Laporan Manifest Pengiriman';
$this->lang['journalDate'] = 'Tgl. Jurnal';

$this->lang['invoicePeriod'] = 'Periode Invoice';
$this->lang['dateRecurring'] = 'Tgl Recurring';
$this->lang['postPaid'] = 'Pascabayar';
$this->lang['billingDate'] = 'Tanggal Penagihan';
$this->lang['salesOrderSubscriptionReport'] = 'Laporan Order Berlangganan';
$this->lang['installationWorkOrderReport'] = 'Laporan SPK Instalasi';
$this->lang['qtyAverage'] = 'Jml. Rata-rata';
$this->lang['avg'] = 'Rata-rata';

$this->lang['ticketSupportReport'] = 'Laporan Ticket Support';
$this->lang['ticketSupportWorkOrderReport'] = 'Laporan SPK Support';
$this->lang['installationBASTReport'] = 'Laporan Berita Acara Serah Terima';
$this->lang['invoiceOrderSubscriptionReport'] = 'Laporan Faktur Penjualan Berlangganan';

$this->lang['timespanReport'] = 'Laporan Timespan';
$this->lang['interval'] = 'Interval';
$this->lang['needRealization'] = 'Perlu Realisasi';
$this->lang['variantProduct'] = 'Variasi Produk';
$this->lang['arStatus'] = 'Status Piutang';
$this->lang['quizResult'] = 'Hasil Quiz';
$this->lang['correct'] = 'Benar';
$this->lang['incorrect'] = 'Salah';
$this->lang['variant'] = 'Varian';
$this->lang['Option'] = 'Opsi'; 
$this->lang['Tiering'] = 'Tiering'; 
$this->lang['correctAnswer'] = 'Jawaban Benar'; 
$this->lang['level'] = 'Tingkat'; 
$this->lang['primary'] = 'Utama';
$this->lang['ARAPCashflowReport'] = 'Laporan Cashflow Hutang / Piutang'; 
$this->lang['ar/apPayment'] = 'Pembayaranh Hutang / Piutang'; 
$this->lang['icon'] = 'Icon';  
$this->lang['unlimited'] = 'Unlimited';
$this->lang['minimum'] = 'Minimum';
$this->lang['lumpSum'] = 'Lump Sum';
$this->lang['start'] = 'Start';
$this->lang['break'] = 'Break';
$this->lang['end'] = 'End';
$this->lang['workingTime'] = 'Working Time';
$this->lang['workHour'] = 'Work Hour';
$this->lang['overTime'] = 'Over Time';
$this->lang['workTime'] = 'Waktu Pengerjaan';
$this->lang['salesOrderWorkshop'] = 'Penjualan Workshop'; 
$this->lang['receiptValidation'] = 'Validasi Struk'; 
$this->lang['signature'] = 'TTD';
$this->lang['externalLink'] = 'External Link';  
$this->lang['virtualAccount'] = 'Virtual Account'; 
$this->lang['sort'] = 'Urutkan'; 
$this->lang['asc'] = 'Naik'; 
$this->lang['desc'] = 'Menurun'; 
$this->lang['reset'] = 'Atur Ulang'; 
$this->lang['totalShopping'] = 'Total Belanja'; 
$this->lang['shoppingList'] = 'Daftar Belanja';
$this->lang['continueToPayment'] = 'Lanjutkan ke Pembayaran';
$this->lang['voucherReport'] = 'Laporan Voucher';
$this->lang['customerName'] = 'Nama Pelanggan';
$this->lang['splitReimburse'] = 'Pisah Total Reimburse';
$this->lang['careerCategory'] = 'Kategori Karier';
$this->lang['careerField'] = 'Bidang Perkerjaan';
$this->lang['requirement'] = 'Persyaratan';
$this->lang['requirements'] = 'Persyaratan';
$this->lang['paymentInstruction'] = 'Instruksi pembayaran';
$this->lang['careers'] = 'Karier';
$this->lang['dropOffLocation'] = 'Lokasi Drop Off'; 
$this->lang['searchLocation'] = 'Cari lokasi'; 
$this->lang['apCommission'] = 'Hutang Komisi'; 
$this->lang['apCommissionPayment'] = 'Pembayaran Hutang Komisi'; 
$this->lang['apCommissionReport'] = 'Laporan Hutang Komisi'; 
$this->lang['apCommissionPaymentReport'] = 'Laporan Pembayaran Hutang Komisi'; 
$this->lang['recruitment'] = 'Rekrutmen'; 
$this->lang['galleryHumanResource'] = 'Galeri (HRD)'; 
$this->lang['galleryCategory'] = 'Kategori Galeri';  
$this->lang['galleryHumanResourceCategory'] = 'Kategori Galeri (HRD)';  
$this->lang['newsletterSubscription'] = 'Newsletter Subscription';  
$this->lang['newsletterSubscriptionReport'] = 'Laporan Newsletter Subscription';  
$this->lang['importir'] = 'Importir';  
$this->lang['goodsDescription'] = 'Deskripsi Barang';  
$this->lang['destuffingLocation'] = 'Lokasi Destuffing';  
$this->lang['stuffingLocation'] = 'Lokasi Stuffing';  
$this->lang['exportir'] = 'Exportir';
$this->lang['deadline'] = 'Batas Waktu'; 
$this->lang['salesOrderToInvoiceReport'] = 'Laporan Waktu Order Penjualan ke Faktur';   
$this->lang['medicalRecord'] = 'Histori Medis';
$this->lang['diff'] = 'Selisih';
$this->lang['video'] = 'Video';
$this->lang['showHistoryOf'] = 'Tampilkan histori tahun';
$this->lang['transactionCashFlowReport'] = 'Laporan Arus Kas Transaksi';
$this->lang['apPayment'] = 'Pembayaran Hutang';
$this->lang['arPayment'] = 'Pembayaran Piutang'; 
$this->lang['paymentGateway'] = 'Payment Gateway';  
$this->lang['supplierDownpaymentSettlement'] = 'Penyelesaian Uang Muka Pemasok';
$this->lang['customerDownpaymentSettlement'] = 'Penyelesaian Uang Muka Pelanggan';
$this->lang['downpaymentCode'] = 'Kode Uang Muka';
$this->lang['refund'] = 'Pengembalian';
$this->lang['pointDeduction'] = 'Pemotongan Poin';
$this->lang['pointHistory'] = 'Histori Poin';
$this->lang['eligiblePoint'] = 'Poin yang dapat digunakan';
$this->lang['claimVoucher'] = 'Tukar Voucher';
$this->lang['prepaidExpense'] = 'Biaya Dimuka';
$this->lang['prepaidCost'] = 'Biaya Dimuka';
$this->lang['prepaidCostReport'] = 'Laporan Biaya Dimuka';
$this->lang['costReconsiliation'] = 'Rekonsiliasi Biaya';
$this->lang['costReconsiliationReport'] = 'Laporan Rekonsiliasi Biaya';
$this->lang['reconsiliation'] = 'Rekonsiliasi';
$this->lang['invoiceDetail'] = 'Detail Faktur'; 
$this->lang['costAccount'] = 'Akun Biaya';
$this->lang['voucherCode'] = 'Kode Voucher';
$this->lang['startingBalance'] = 'Saldo Awal';
$this->lang['cashback'] = 'Cashback';
$this->lang['expDate'] = 'Tgl. Exp';
$this->lang['apiLogReport'] = 'Laporan Log API';
$this->lang['salesOrderSummaryReport'] = 'Laporan Rangkuman Penjualan';
$this->lang['buyer'] = 'Pembeli';
$this->lang['seller'] = 'Penjual'; 
$this->lang['transactionValue'] = 'Nilai Transaksi'; 
$this->lang['propertyInformation'] = 'Informasi Properti';
$this->lang['bankProvision'] = 'Provisi Bank';
$this->lang['bank'] = 'Bank';  
$this->lang['adminRevenue'] = 'Pendapatan Admin';
$this->lang['commissionTransaction'] = 'Komisi Transaksi'; 
$this->lang['salesOrderSummary'] = 'Rekapitulasi Penjualan';
$this->lang['salesOrderCategory'] = 'Kategori Order Penjualan';
$this->lang['property'] = 'Properti';
$this->lang['activePeriod'] = 'Masa Aktif';
$this->lang['memberID'] = 'ID Anggota';
$this->lang['meetingPoint'] = 'Lokasi Meeting';
$this->lang['meetingSchedule'] = 'Jadwal Meeting';
$this->lang['meeting'] = 'Meeting';
$this->lang['meetingType'] = 'Jenis Meeting';
$this->lang['meetingLink'] = 'Link Meeting';
$this->lang['apCustomerCommission'] = 'Hutang Komisi Pelanggan';
$this->lang['vehicleCode'] = 'Kode Kendaraan';
$this->lang['meetingScheduleReport'] = 'Laporan Jadwal Meeting';
$this->lang['meetingPointSuggestionReport'] = 'Laporan Saran Lokasi Meeting';
$this->lang['businessCategorySuggestionReport'] = 'Laporan Saran Kategori Usaha';
$this->lang['meetingPointReport'] = 'Laporan Lokasi Meeting';
$this->lang['apCustomerCommissionReport'] = 'Laporan Komisi Pelanggan';
$this->lang['newRequest'] = 'Permintaan Baru';
$this->lang['callerInformation'] = 'Informasi Penelepon'; 
$this->lang['relationshipToInsured'] = 'Hubungan dengan tertanggung';
$this->lang['insuredInformation'] = 'Informasi Tertanggung';
$this->lang['policyNumber'] = 'Nomor Polis';
$this->lang['diagnose'] = 'Diagnosa';
$this->lang['callerName'] = 'Nama Penelepon';
$this->lang['insuredName'] = 'Nama Tertanggung';
$this->lang['insuranceCompany'] = 'Perusahaan Asuransi';
$this->lang['caseInformation'] = 'Informasi Kasus';
$this->lang['host'] = 'Host';
$this->lang['participants'] = 'Peserta';
$this->lang['participantsList'] = 'Daftar Peserta';
$this->lang['topic'] = 'Topik';
$this->lang['mblawb'] = 'MBL / AWB';
$this->lang['refundDetail'] = 'Detail Refund';
$this->lang['APCommissionReviewReport'] = 'Laporan Review Komisi';
$this->lang['meetingPointSuggestion'] = 'Saran Lokasi Meeting';
$this->lang['searchMeeting'] = 'Cari Meeting';
$this->lang['multiplePurchase'] = 'Pembelian Berulang';
$this->lang['newMeeting'] = 'Meeting Baru';  
$this->lang['jobPosition'] = 'Jabatan';
$this->lang['private'] = 'Private';
$this->lang['cobroke'] = 'Co-broke';
$this->lang['closingFee'] = 'Closing Fee';
$this->lang['to'] = 'Kepada';
$this->lang['companyRevenue'] = 'Pendapatan Perusahan';
$this->lang['agentRevenue'] = 'Pendapatan Agent';

$this->lang['documentInformation'] = 'Informasi Dokumen';

$this->lang['shippingRate'] = 'Tarif Pengiriman';
$this->lang['deliveryDate'] = 'Tanggal Pengiriman';
$this->lang['senderSignature'] = 'Tanda Tangan Pengirim';
$this->lang['recipientSignature'] = 'Tanda Tangan Penerima';
$this->lang['officer'] = 'Petugas';
$this->lang['destinationCity'] = 'Kota Tujuan';  
$this->lang['sender'] = 'Pengirim';
$this->lang['fromCity'] = 'Kota Asal';
$this->lang['destinationCity'] = 'Kota Tujuan';
$this->lang['recipientName'] = 'Nama Penerima';
$this->lang['senderName'] = 'Nama Pengirim';
$this->lang['bale'] = 'Koli';

$this->lang['mblType'] = 'MBL Type';
$this->lang['mblDate'] = 'MBL Date';
$this->lang['hblType'] = 'HBL Type';
$this->lang['hblDate'] = 'HBL Date';
$this->lang['optionType'] = 'Option Type';
$this->lang['optionDate'] = 'Option Date';
$this->lang['proposeDate'] = 'Propose Date';
$this->lang['voucherDate'] = 'Voucher Date';
$this->lang['transferFtp'] = 'Transfer FTP';
$this->lang['PADate'] = 'PA Date';
$this->lang['PLDate'] = 'PL Date'; 
$this->lang['eManifestDate'] = 'e-Manifest'; 
$this->lang['placeOfIssued'] = 'Place Of Issued';
$this->lang['placeOfReciept'] = 'Place Of Reciept';
$this->lang['jobOrderReminder'] = 'JO Reminder';
$this->lang['emklReminderJobOrderReport'] = 'Laporan Reminder Job Order';
$this->lang['refundAgentShipper'] = 'Refund Agent / Shipper';
$this->lang['reminder'] = 'Pengingat';

$this->lang['transportation'] = 'Transportasi';
$this->lang['lengthShort'] = 'P';
$this->lang['widthShort'] = 'L';
$this->lang['heightShort'] = 'T';
$this->lang['totalPrice'] = 'Total Harga';
$this->lang['totalCollie'] = 'Total Koli';
$this->lang['manifestReport'] = 'Laporan Manifest';
$this->lang['logisticSalesOrderReport'] = 'Laporan Penjualan Logistic';

$this->lang['manageMembership'] = 'Keanggotaan';
$this->lang['activeUntil'] = 'Aktif Hingga';

$this->lang['packingFee'] = 'Biaya Packing';
$this->lang['senderCity'] = 'Kota asal';
$this->lang['senderPhone'] = 'Telepon Pengirim';
$this->lang['senderAddress'] = 'Alamat Pengirim';

$this->lang['recipientCity'] = 'Kota tujuan';
$this->lang['recipientPhone'] = 'Telepon Penerima';
$this->lang['recipientAddress'] = 'Alamat Penerima'; 
$this->lang['placeOfDelivery'] = 'Tempat Pengiriman';
$this->lang['placeOfReceipt'] = 'Tempat Penerima';
$this->lang['placeOfIssue'] = 'Tempat Penerbitan';
$this->lang['merchant'] = 'Merchant';
$this->lang['exportReference'] = 'Export Reference';
$this->lang['notifyParty'] = 'Notify Party';

$this->lang['transactionSubmitSuccessful'] = 'Transaksi Anda telah berhasil. Anda akan menerima faktur dan detail cara pembayaran di email Anda segera.';
$this->lang['partnerID'] = 'partner ID';

$this->lang['printManifest'] = 'Cetak Manifest';
$this->lang['aju'] = 'AJU';
$this->lang['ajuPEB'] = 'AJU PEB';
$this->lang['ajuPIB'] = 'AJU PIB';
$this->lang['PIB/PEB'] = 'PIB/PEB'; 
$this->lang['HouseBL'] = 'House BL';
$this->lang['apCustomerCommissionPayment'] = 'Pembayaran Komisi Pelanggan';
$this->lang['metaTitle'] = 'Judul Meta';
$this->lang['metaDescription'] = 'Deskripsi Meta';
$this->lang['manifest'] = 'Manifest';
$this->lang['marksAndNumber'] = 'Marks & Number';
$this->lang['printConnote'] = 'Cetak Connote';
$this->lang['printLabel'] = 'Cetak Label';
$this->lang['telex'] = 'Telex'; 
$this->lang['telexDate'] = 'Tgl. Telex'; 

$this->lang['assetGroup'] = 'Grup Aset';
$this->lang['TLX'] = 'TLX'; 
$this->lang['release'] = 'Release'; 
$this->lang['shippingIntruction'] = 'Instruksi Pengiriman'; 
$this->lang['billType'] = 'Jenis Tagihan'; 

$this->lang['opportunity'] = 'Opportunity'; 
$this->lang['preprinted'] = 'Pre Printed'; 
$this->lang['grossPLReport'] = 'Laporan Gross P&L';  
$this->lang['freightCharges'] = 'freight &amp; Charges'; 
$this->lang['servicesCOALink'] = 'COA Link Layanan';
$this->lang['link'] = 'Tautan';
$this->lang['NIB'] = 'NIB';
$this->lang['country'] = 'Negara';  
$this->lang['policy'] = 'Polis';
$this->lang['newCase'] = 'Case Baru';
$this->lang['age'] = 'Umur';
$this->lang['needQuotation'] = 'Butuh Penawaran';
$this->lang['priceQuotation'] = 'Penawaran Harga';
$this->lang['log'] = 'Log';
$this->lang['linearBarcode'] = 'Barcode Linear';
$this->lang['barcode'] = 'Barcode';
$this->lang['showQuotation'] = 'Lihat Penawaran';
$this->lang['overwrite'] = 'Overwrite'; 
$this->lang['companyAddress'] = 'Alamat Perusahaan'; 
$this->lang['containerInformationInWords'] = 'Informasi Container (kalimat)'; 
$this->lang['activityLog'] = 'Log Aktifitas';
$this->lang['bookValue'] = 'Nilai Buku';
$this->lang['downpaymentCode'] = 'Kode Downpayment';
$this->lang['acquisitionValue'] = 'Nilai Perolehan';
$this->lang['acquisitionDate'] = 'Tgl. Perolehan';
$this->lang['usefulLife'] = 'Masa Manfaat';
$this->lang['depreciation'] = 'Penyusutan';
$this->lang['accumulatedDepreciation'] = 'Akm. Penyusutan';
$this->lang['depreciationExpense'] = 'Beban Penyusutan';

$this->lang['printLetterOfGuarantee'] =  'Cetak Surat Penjaminan';
$this->lang['reportNewRequestHistory'] =  'Laporan Histori Permintaan Baru';
$this->lang['assetReport'] = 'Laporan Aset';
$this->lang['assetPurchaseReport'] = 'Laporan Order Pembelian Aset';
$this->lang['assetDepreciationReport'] = 'Laporan Depresiasi Aset';
$this->lang['stampFee'] =  'Biaya Materai';
$this->lang['assemblyReport'] = 'Laporan Perakitan Barang';
$this->lang['billOfMaterialsReport'] = 'Laporan Bill of Materials'; 
	
$this->lang['newRequestReport'] = 'Laporan Permintaan Baru';
$this->lang['priceQuotationReport'] = 'Laporan Penawaran Harga';
$this->lang['guaranteeLetterReport'] = 'Laporan Guarantee Letter';
$this->lang['cod'] = 'COD'; 
$this->lang['membershipSubscriptionnReport'] = 'Laporan Registrasi Keanggotaan';  
$this->lang['monthlySubscriptionnReport'] = 'Laporan Registrasi Bulanan';  
$this->lang['purchaseCategory'] = 'Kategori Pembelian';  
$this->lang['totalVisit'] = 'Jumlah Kunjungan';  
$this->lang['maxWeight'] = 'Berat Maks.';  
$this->lang['minWeight'] = 'Berat Min.';  
$this->lang['visit'] = 'Kunjungan';  
$this->lang['serviceInformation'] = 'Informasi Layanan';  
$this->lang['workOrderDispatcher'] = 'Dispatcher SPK'; 
$this->lang['workOrderDispatcherReport'] = 'Laporan Dispatcher SPK';   
$this->lang['dateSent'] = 'Tgl. Dikirim';  
$this->lang['dateReceived'] = 'Tgl. Diterima';  
$this->lang['invoiceReceivedDate'] = 'Tgl. Faktur Diterima';  
$this->lang['taxInvoice'] = 'Faktur Pajak';  
$this->lang['needDocumentProof'] = 'Perlu Bukti Dokumen';  
$this->lang['driverLicenseExpired'] = 'SIM Kadaluarsa';
$this->lang['jobContract'] = 'Kontrak Kerja';
$this->lang['contractReport'] = 'Laporan Kontrak';
$this->lang['logistic'] = 'Logistik'; 
$this->lang['accDepreciation'] = 'Akum. Depresiasi';
$this->lang['withholdingNo'] = 'No. Bukti Potong';
$this->lang['ntpn'] = 'NTPN';
$this->lang['campaignNewsletter'] = 'Campaign Newsletter';
$this->lang['undername'] = 'Undername';
$this->lang['pickUpList'] = 'Daftar Pengambilan';

$this->lang['wasteCategory'] = 'Kategori Limbah';
$this->lang['waste'] = 'Limbah';
$this->lang['workOrderCode'] = 'No Surat Jalan';
$this->lang['manifestCode'] = 'No. Manifest';
$this->lang['ticketNumber'] = 'No. Tiket';
$this->lang['characteristic'] = 'Karakteristik';
$this->lang['workOrderDispatcherCode'] = 'Kode Dispatch';
$this->lang['driverIncentive'] = 'Biaya Insentif Sopir';
$this->lang['PPhType'] = 'Jenis PPH';
$this->lang['PPhValue'] = 'Nilai PPH';
$this->lang['accountIn'] = 'Akun Masukan';
$this->lang['accountOut'] = 'Akun Keluaran';
$this->lang['serviceFacilities'] = 'Fasilitas Layanan';
$this->lang['correspondentInformation'] = 'Informasi Koresponden';
$this->lang['periodically'] = 'Secara Berkala';
$this->lang['uninvoiced'] = 'Belum Difaktur';
$this->lang['uninvoicedSOReport'] = 'Job Order Belum Difaktur';
$this->lang['documentNo'] = 'No. Dokumen';
$this->lang['paymentUpfront'] = 'Pembayaran Dimuka';
$this->lang['ARStatus'] = 'Status AR';
$this->lang['noAccess'] = 'Tidak ada akses';
$this->lang['CN/DN'] = 'CN/DN';
$this->lang['taxPeriod'] = 'Masa Pajak';
$this->lang['taxObjectCode'] = 'Kode Objek Pajak';
$this->lang['costGrouping'] = 'Pengelompokan Biaya';
$this->lang['accounting'] = 'Akunting';
$this->lang['bankInformation'] = 'Informasi Bank';
$this->lang['servicesPrice'] = 'Informasi Layanan';
$this->lang['currencyRateMaster'] = 'Transaksi Mata Uang (Manual)';
$this->lang['keyword'] = 'Kata Kunci';
$this->lang['truckingCashOutRequest'] = 'Pengajuan Kas Keluar Trucking';
$this->lang['bankReconsiliation'] = 'Rekonsiliasi Bank';
$this->lang['subsidiaries'] = 'Subsidiaries';
$this->lang['companyHistory'] = 'Sejarah Perusahaan';
$this->lang['awardsAndAchievements'] = 'Penghargaan &amp; Pencapaian';
$this->lang['investorRelations'] = 'Relasi Investor';
$this->lang['corporateValues'] = 'Nilai Perusahaaan';
$this->lang['jobExperience'] = 'Pengalaman Kerja';
$this->lang['careerDepartment'] = 'Department Karir';
$this->lang['careerReference'] = 'Referensi Karir';
$this->lang['joiningConsideration'] = 'Pertimbangan Bergabung';
$this->lang['salesOrderQuotation'] = 'Penawaran Harga';
$this->lang['CSR'] = 'CSR';
$this->lang['CSRCategory'] = 'Kategori CSR'; 
$this->lang['JobApplication'] = 'Lamaaran Kerja'; 
$this->lang['latestRoleTitle'] = 'Posisi terakhir bekerja'; 
$this->lang['latestCompany'] = 'Perusahaan terakhir bekerja'; 
$this->lang['iStillWorkHere'] = 'Saya masih bekerja disini'; 
$this->lang['personalDetails'] = 'Personal Details'; 
$this->lang['resume'] = 'Resume';  
$this->lang['otherInformation'] = 'Informasi Lainnya';   
$this->lang['whereDidYouHearAboutUs'] = 'Dari mana kamu mendengar tentang kami?';  
$this->lang['whatAreYourConsiderationsForJoiningOurCompany'] = 'Apa pertimbangan kamu untuk bergabung dengan perusahaan Kami?';  

$this->lang['personalDetails'] = 'Personal Details'; 
$this->lang['resume'] = 'Resume'; 
$this->lang['investorNews'] = 'Berita Investor';  
$this->lang['investorNewsCategory'] = 'Kategori Berita Investor';  
$this->lang['investorReport'] = 'Laporan Investor';  
$this->lang['alwaysShown'] = 'Selalu Diperlihatkan';  
$this->lang['widget'] = 'Widget';  
$this->lang['tableFormat'] = 'Format Tabel';  
$this->lang['GCG'] = 'GCG';  
$this->lang['GCGReport'] = 'Laporan GCG';  
$this->lang['GCGCategory'] = 'GCG Category';
$this->lang['totalselling'] = 'Total Penjualan';
$this->lang['totalcogs'] = 'Total COGS';
$this->lang['reconsiliationDate'] = 'Tgl. Rekonsiliasi';
$this->lang['lockPeriod'] = 'Kunci Periode';
$this->lang['approval'] = 'Persetujuan';
$this->lang['bankReconsiliationReport'] = 'Laporan Rekonsiliasi Kas / Bank';
$this->lang['cashBankCardReport'] = 'Laporan Kartu Kas / Bank';
$this->lang['totalDebit'] = 'Total Debit';
$this->lang['totalCredit'] = 'Total Credit';
$this->lang['loop'] = 'Berulang';
$this->lang['letterhead'] = 'kop surat';
$this->lang['cargoInformation'] = 'Informasi Kargo';
$this->lang['asSupplier'] = 'Sebagai Pemasok';
$this->lang['documentFiles'] = 'Document File(s)';
$this->lang['witholdingReceipt'] = 'Bukti Potong';
$this->lang['customerNews'] = 'Berita Pelanggan';
$this->lang['purchaseOrderTrucking'] = 'Order Pembelian (Trucking)';
$this->lang['moduleName'] = 'Nama Modul';
$this->lang['followCode'] = 'Mengikuti Penomoran';
$this->lang['periodically'] = 'Periode';
$this->lang['monthly'] = 'Bulanan';
$this->lang['annualy'] = 'Tahunan';
$this->lang['truckingPurchaseUnInvoicedReport'] = 'Laporan Pembelian Trucking Belum Difaktur';
$this->lang['quarterly'] = 'Triwulan';
$this->lang['yearOverYear'] = 'Tahun ke Tahun';
$this->lang['referenceNumber'] = 'No. Referensi';
$this->lang['cubication'] = 'Kubikasi';
$this->lang['capactiy'] = 'Kapasitas';
$this->lang['consigneeName'] = 'Nama Consignee';
$this->lang['consigneeAddress'] = 'Alamat Consignee';
$this->lang['recipientType'] = 'Jenis Penerima';
 
$this->lang['recurringPeriod'] = 'Periode Recurring';
$this->lang['numberOfPeriod'] = 'Jumlah Periode';
$this->lang['timePeriod'] = 'Jangka Waktu';
$this->lang['recurring'] = 'Recurring';

$this->lang['salesOrderRecurringSubscription'] = 'Order Penjualan Berlangganan';
$this->lang['lastRecurringDate'] = 'Tgl. Recurring Terakhir';
$this->lang['nextRecurringDate'] = 'Tgl. Recurring Selanjutnya';
$this->lang['lastInvoiceDate'] =  'Tgl. Invoice Terakhir';
$this->lang['nextInvoiceDate'] =  'Tgl. Invoice Selanjutnya';
$this->lang['subscriptionCode'] = 'Kode Berlangganan';
$this->lang['salesOrderRecurringTermination'] = 'Terminasi Order Penjualan Berlangganan';

$this->lang['billingEmail'] = 'Email Billing';
$this->lang['billingMobile'] = 'Telp. Billing';
$this->lang['HRD'] = 'HRD';
$this->lang['employeeAttendance'] = 'Kehadiran Karyawan'; 

$this->lang['workDays'] = 'Hari Kerja'; 
$this->lang['absenceDays'] = 'Hari Absen'; 
$this->lang['lateDays'] = 'Hari Telat'; 
$this->lang['unpaidLeave'] = 'Cuti Tidak Dibayar';
$this->lang['attendanceID'] = 'ID Absensi';
$this->lang['cut'] = 'Potongan';
$this->lang['late'] = 'Telat';
$this->lang['halfDay'] = '1/2 Hari';
$this->lang['employeeAttendanceReport'] = 'Laporan Absensi Karyawan';
$this->lang['useVoucher'] = 'Menggunakan Voucher';
$this->lang['unknownSource'] = 'Sumber tidak diketahui';
$this->lang['employeeCode'] = 'Kode karyawan';
$this->lang['hideEmptyAmount'] = 'Sembunyikan nilai kosong';
$this->lang['ICA'] = 'ICA';
$this->lang['ICACOA'] = 'Akun ICA'; 
$this->lang['withoutAmount'] = 'Tanpa Harga';
$this->lang['basedOn'] = 'Berdasarkan';
$this->lang['returnQty'] = 'Jml. Retur';
$this->lang['financialSummaryReport'] = 'Laporan Rangkuman Keuangan';
$this->lang['financialReport'] = 'Laporan Keuangan';
$this->lang['monthlyReport'] = 'Laporan Bulanan';
$this->lang['invoiceCalculation'] = 'Perhitungan Faktur';
$this->lang['accountStatementImport'] = 'Import Rekening Koran';
$this->lang['bankRef'] = 'Ref. Bank';
$this->lang['buildingUnit'] = 'Unit Bangunan';
$this->lang['block'] = 'Blok';
$this->lang['tenantDetail'] = 'Detail Tenant';
$this->lang['ownerDetail'] = 'Detail Pemilik';
$this->lang['tenant'] = 'Tenant';
$this->lang['unitSize'] = 'Luas Unit';
$this->lang['notify'] = 'Notify';
$this->lang['notifyTo'] = 'Tujuan Notify';
$this->lang['informationInThePrintOut'] = 'Informasi di cetakan';
$this->lang['normalBalance'] = 'Saldo Normal';
$this->lang['totalResidents'] = 'Jumlah Penghuni';
$this->lang['buildingUnitReport'] = 'Laporan Unit Bangunan';
$this->lang['statementOfAccountReport'] = 'Laporan Statement of Account';
$this->lang['unconfirm'] = 'Pending';
	
$this->lang['remainPercentage'] = 'Persentasi Sisa';
$this->lang['itemProportional'] = 'Proporsional Barang';
$this->lang['itemPercentage'] = 'Persentasi Barang';
$this->lang['wasteAccount'] = 'Akun Sisa';
$this->lang['productLink'] = 'Link Produk';
$this->lang['PICGroup'] = 'Group PIC';
$this->lang['flag'] = 'Bendera';
$this->lang['quotationOrderImport'] = 'Penawaran Import'; 
$this->lang['quotationOrderExport'] = 'Penawaran Export'; 
$this->lang['quotationOrderDomestic'] = 'Penawaran Domestik';
$this->lang['revision'] = 'Revisi';
$this->lang['validUntil'] = 'Berlaku hingga';
$this->lang['assetDisposal'] = 'Penghapusan Aset';
$this->lang['supplierCategory'] = 'Kategori Pemasok';
$this->lang['printNOA'] = 'Cetak NOA';
$this->lang['originCharge'] = 'Biaya Asal';
$this->lang['destinationCharge'] = 'Biaya Tujuan';

$this->lang['quoteTo'] = 'Penawaran Untuk';
$this->lang['quoteNo'] = 'No. Penawaran';
$this->lang['quoteDate'] = 'Tgl. Penawaran';
$this->lang['preparedBy'] = 'Disiapkan Oleh';
$this->lang['headerText'] = 'Teks Header';
$this->lang['jobComplete'] = 'Pekerjaan selesai';
$this->lang['fetching'] = 'Sedang diupdate';
$this->lang['enterYourEmail'] = 'Input email Anda'; 
$this->lang['lastUpdate'] = 'Update terakhir';
$this->lang['dropPointDetailPrice'] = 'Harga Detail Drop Point';
$this->lang['arReimbursementAccount'] = 'Akun Piutang Reimbursement';
$this->lang['arReimbursement'] = 'Piutang Reimbursement';
$this->lang['multipliedByQty'] = 'Dikalikan Qty';
$this->lang['quotaOrTarget'] = 'Kuota / Target';
$this->lang['templateActivity'] = 'Template Aktivitas';
$this->lang['notification'] = 'Notifikasi';
$this->lang['exportQuotaRealizationReport'] = 'Laporan Realisasi Quota Export';
$this->lang['importQuotaRealizationReport'] = 'Laporan Realisasi Quota Import';

$this->lang['taxInvoiceBatch'] = 'Batch Faktur Pajak';
$this->lang['batch'] = 'Batch';
$this->lang['startNumber'] = 'Nomor Mulai';
$this->lang['endNumber'] = 'Nomor Akhir';
$this->lang['taxInvoiceNumber'] = 'No. Faktur Pajak';
$this->lang['effectiveDate'] = 'Tgl. Mulai Berlaku';
$this->lang['transactionTypeCode'] = 'Kode Jenis Transaksi';
$this->lang['document'] = 'Dokumen';
$this->lang['taxPercentage'] = 'Tax %';
$this->lang['additionalId'] = 'ID Tambahan';
$this->lang['uploadDate'] = 'Tgl. Upload';
$this->lang['fileName'] = 'Nama File'; 
$this->lang['templateActivity'] = 'Template Aktivitas';
$this->lang['notification'] = 'Notifikasi'; 
$this->lang['activityProgress'] = 'Progress Aktivitas';
$this->lang['response'] = 'Response'; 
$this->lang['domesticOrderSheet'] = 'Order Sheet Domestik';     
$this->lang['pibRegistrationNumber'] = 'Nomor Pendaftaran PIB';
$this->lang['deliveryAddress'] = 'Alamat Pengiriman';
$this->lang['continent'] = 'Benua';
$this->lang['country'] = 'Negara'; 
$this->lang['debitSource'] = 'Debit Source'; 
$this->lang['creditSource'] = 'Credit Source';

$this->lang['shipmentTerm'] = 'Shipment Term';  
$this->lang['finalDestination'] = 'Tujuan Akhir';
$this->lang['firstConnectingVessel'] = 'Vessel Connecting #1';
$this->lang['secondConnectingVessel'] = 'Vessel Connecting #2';
$this->lang['thirdConnectingVessel'] = 'Vessel Connecting #3';

$this->lang['freightTerm'] = 'Pembayaran Pengiriman';
$this->lang['commodityList'] = 'Daftar Komoditas';  
$this->lang['commodityType'] = 'Jenis Komoditas'; 
$this->lang['motherVessel'] = 'Mother Vessel'; 
$this->lang['shipmentType'] = 'Jenis Shipment'; 
$this->lang['maintenanceSummaryReport'] = 'Laporan Rangkuman Perawatan Mobil'; 
$this->lang['securityPrivilegesReport'] = 'Laporan Hak Akses';

$this->lang['displayOption'] = 'Opsi Tampilan';
$this->lang['view'] = 'Lihat';
$this->lang['vatIn'] = 'PPN Masukan'; 
$this->lang['vatOut'] = 'PPN Keluaran';
$this->lang['carServiceMaintenanceGraph'] = 'Grafik Perawatan Mobil';
$this->lang['showContainerInformation'] = 'Tampilkan Informasi Container';
$this->lang['jobOrderSummaryReport'] = 'Laporan Ringkasan Job Order';
$this->lang['cashBankVoucher'] = 'Voucher Kas Bank';

$this->lang['carMaintenanceRequest'] = 'Permintaan Perawatan Mobil';
$this->lang['complaint'] = 'Keluhan';
$this->lang['firstTransitTime'] = 'Transit Time #1';
$this->lang['secondTransitTime'] = 'Transit Time #2';
$this->lang['firstConnectingAirport'] = 'Connecting Airport #1';
$this->lang['secondConnectingAirport'] = 'Connecting Airport #2';
$this->lang['thirdConnectingAirport'] = 'Connecting Airport #3';
$this->lang['liftingReport'] = 'Laporan Lifting';
$this->lang['sailDate'] = 'Tgl. Layar'; 
$this->lang['notificationLetter'] = 'Surat Pemberitahuan'; 
$this->lang['targetProfit'] = 'Target Profit';
$this->lang['target'] = 'Target';
$this->lang['taxType'] = 'Jenis Pajak';
$this->lang['identificationType'] = 'Jenis Identifikasi';
$this->lang['additionalInfo'] = 'Informasi Tambahan';
$this->lang['facilityStamp'] = 'Cap Fasilitas';
$this->lang['passportID'] = 'ID Passport';
$this->lang['truckingAdditionalCost'] = 'Biaya Tambahan Trucking';
$this->lang['truckingAdditionalCostReport'] = 'Laporan Biaya Tambahan Trucking';
$this->lang['displayTaxArt23InInvoice'] = 'Tampilkan PPH 23 di faktur';
$this->lang['ARAPReimburse'] = 'AR/AP Reimburse';

$this->lang['latitude'] = 'Latitude';
$this->lang['longitude'] = 'Longitude'; 
$this->lang['employeeCommission'] = 'Komisi Karyawan';
$this->lang['employeeCommissionAP'] = 'Hutang Komisi Karyawan';
$this->lang['employeeCommissionAPPayment'] = 'Pembayaran Hutang Komisi Karyawan'; 
$this->lang['generateEmployeeCommission'] = 'Generate Komisi Karyawan';
$this->lang['lastPayment'] = 'Pembayaran Terakhir';
$this->lang['importCost'] = 'Import Biaya';
$this->lang['importSellingPrice'] = 'Import Harga Jual';
$this->lang['DPI'] = 'DPI'; // dasar perhitungan insentif
$this->lang['importJobOrder'] = 'Import Job Order';
$this->lang['importSellingProce'] = 'Import Harga Jual';
$this->lang['amortization'] = 'Amortisasi';
$this->lang['taxServiceCode'] = 'Kode Layanan Pajak';
$this->lang['taxServiceUnit'] = 'Kode Unit Pajak';
$this->lang['generalTransaction'] = 'Transaksi Umum';
$this->lang['carat'] = 'Karat';
$this->lang['caratRate'] = 'Harga Karat';
$this->lang['pricingScheme'] = 'Skema Harga';
$this->lang['additionalPrice'] = 'Harga Tambahan';
$this->lang['syncGPS'] = 'Sync GPS';
$this->lang['dailyCashReport'] = 'Laporan Kas Harian';
$this->lang['creditAccount'] = 'Akun Kredit';
$this->lang['journalAccounts'] = 'Akun Jurnal';
$this->lang['pricingCategory'] = 'Kategori Harga';
$this->lang['priceUpdate'] = 'Update Rate Harga Jual';
$this->lang['caratBasis'] = 'Basis Karat';
$this->lang['totalJO'] = 'Total JO';
$this->lang['templateEMKLJobOrderImport'] = 'Import Order Sheet Template';
$this->lang['templateEMKLJobOrderExport'] = 'Export Order Sheet Template';
$this->lang['templateJobOrder'] = 'Template Order Sheet';
$this->lang['incoterms'] = 'Incoterms'; 
$this->lang['dailyReport'] = 'Laporan Harian';
$this->lang['jobOrderPeriod'] = 'Periode Job Order';
$this->lang['paymentMethodPrivileges'] = 'Hak Akses Metode Pembayaran';
$this->lang['allPaymentMethod'] = 'Semua Metode Pembayaran';
$this->lang['executeDate'] = 'Tgl. Pekerjaan';
$this->lang['shortThousand'] = 'rb';
$this->lang['proceedToPayment'] = 'Bayar Sekarang'; 
$this->lang['signerOfTheInvoice'] = 'Penandatangan faktur';
$this->lang['label'] = 'Label';
$this->lang['withholdingTax'] = 'PPH';
$this->lang['withholdingTaxType'] = 'Jenis PPH';

$this->lang['assetItemList'] = 'Daftar Unit';
$this->lang['categoryAssetItem'] = 'Kategori Unit';
$this->lang['itemType'] = 'Jenis Barang';
$this->lang['inventoryAssetItem'] = 'Persediaan Unit';
$this->lang['tempAssetItemInventory'] = 'Persediaan Unit Sementara';
$this->lang['mast'] = 'Mast';
$this->lang['hpp'] = 'HPP';
$this->lang['costDifference'] = 'Selisih Biaya';
$this->lang['noReviewYet'] = 'Belum ada ulasan';
$this->lang['unCollectedAR'] = 'Piutang Tidak Tertagih';
$this->lang['unCollected'] = 'Tidak tertagih';


$this->lang['color'] = 'Warna';
$this->lang['colorHex'] = 'Hex Warna';
$this->lang['plating'] = 'Plating';
$this->lang['material'] = 'Material';
$this->lang['texture'] = 'Tekstur';
$this->lang['model'] = 'Model';
$this->lang['character'] = 'Karakter';
$this->lang['ringSize'] = 'Ring Size';

$this->lang['truckingServiceOrderByVehicleReport'] = 'Laporan Job Order Per Kendaraan';
$this->lang['feet'] = 'Feet';
$this->lang['fee'] = 'Ongkos';
$this->lang['netResult'] = 'Hasil Bersih';

$this->lang['quotationOrderDomestic'] = 'Penawaran Domestik';
$this->lang['dimension'] = 'Dimensi';
$this->lang['itemDimension'] = 'Dimensi Barang';
$this->lang['pettyCash'] = 'Kas Kecil';
$this->lang['allVehicle'] = 'Semua Kendaraan';	
$this->lang['perVehicle'] = 'Per Kendaraan';
$this->lang['perCategory'] = 'Per Kategori';
$this->lang['packaging'] = 'Kemasan';
$this->lang['packagingInformation'] = 'Informasi Kemasan';
$this->lang['revenueAndCost'] = 'Pendapatan dan Biaya';
$this->lang['carServiceMaintenanceByItemCategory'] = 'Biaya Perawatan Mobil Per Kategori Barang';
$this->lang['ready'] = 'Ready';
$this->lang['indent'] = 'Indent';
$this->lang['monthlySalesSummaryReport'] = 'Laporan Rangkuman Penjualan Bulanan';
$this->lang['packagingCodeReport'] = 'Laporan Kode Kemasan';
$this->lang['purchaseTransaction'] = 'Transaksi Pembelian';
$this->lang['pettyCashReport'] = 'Laporan Kas Kecil';
$this->lang['chooseShippingService'] = 'Pilih Layanan Pengiriman';
$this->lang['fileReference'] = 'Referensi file';
$this->lang['carSummaryTurnoverReport'] = 'Laporan Rangkuman Turnover Mobil';
$this->lang['fixedWeight'] = 'Berat Tetap';
$this->lang['itemVariation'] = 'Variasi Barang';
$this->lang['age'] = 'Umur';
$this->lang['discountVoucher'] = 'Diskon Voucher';
$this->lang['discountShipment'] = 'Diskon Ongkir';
$this->lang['jobProgress'] = 'Progres Pekerjaan';
$this->lang['updateTime'] = 'Waktu Update';
$this->lang['warehouseOrderSheet'] = 'Order Sheet Warehouse';
$this->lang['truckingOrderSheet'] = 'Order Sheet Trucking';
$this->lang['salesOrderWarehouseReport'] = 'Laporan Penjualan Warehouse'; 
$this->lang['salesOrderTruckingReport'] = 'Laporan Penjualan Trucking'; 
$this->lang['purchaseOrderWarehouse'] = 'Order Pembelian Warehouse';
$this->lang['purchaseOrderTrucking'] = 'Order Pembelian Trucking';
$this->lang['purchaseOrderWarehouseReport'] = 'Laporan Order Pembelian Warehouse'; 
$this->lang['purchaseOrderTruckingReport'] = 'Laporan Order Pembelian Trucking'; 
$this->lang['itemPosition'] = 'Posisi Barang';
$this->lang['tire'] = 'Ban';
$this->lang['sideMirror'] = 'Spion';
$this->lang['battery'] = 'Aki';
$this->lang['filter'] = 'Filter';
$this->lang['customerIssue'] = 'Kendala Pelanggan';
$this->lang['viewLocation'] = 'Lihat Lokasi';
$this->lang['viewPOD'] = 'Lihat POD';
$this->lang['customerIssueReport'] = 'Laporan Kendala Pelanggan';
$this->lang['help'] = 'Bantuan';
$this->lang['purchasePricing'] = 'Pricing Pembelian';

$this->lang['itemPosition'] = 'Posisi Barang';
$this->lang['sparePartType'] = 'Tipe Suku Cadang';
$this->lang['maintenanceReminder'] = 'Pengingat Perawatan Mobil'; 
$this->lang['lastSerialNumber'] = 'Serial Number Lama';
	
$this->lang['carSpareparts'] = 'Suku Cadang Mobil';
$this->lang['lastItemOrService'] = 'Barang / Jasa Lama'; 
$this->lang['partsPosition'] = 'Posisi Part'; 
$this->lang['importirExportir'] = 'Importir / Exportir'; 
$this->lang['debitAP'] = 'Debit AP'; 
$this->lang['receiveCashBank'] = 'Terima Kas/Bank'; 
$this->lang['debitMemo'] = 'Debit Memo';
$this->lang['maintenanceReminder'] = 'Pengingat Perawatan';
$this->lang['previousPart'] = 'Part sebelumnya';
$this->lang['AIAnalysis'] = 'Analisis AI';
$this->lang['purity'] = 'Kadar';
$this->lang['deleteAll'] = 'Hapus semua';
$this->lang['warehouseLayout'] = 'Tata Letak Gudang';
$this->lang['pallet'] = 'Pallet';
$this->lang['warehouseLocation'] = 'Letak Gudang';
$this->lang['beginningWarehouseLocation'] = 'Letak Gudang Awal';
$this->lang['finalWarehouseLocation'] = 'Letak Gudang Akhir';

$this->lang['itemReceiving'] = 'Penerimaan Barang';
$this->lang['receivedDate'] = 'Tgl. Penerimaan';
$this->lang['documentType'] = 'Jenis Dokumen';
$this->lang['submissionNumber'] = 'No. Pengajuan';
$this->lang['submissionDate'] = 'Tgl. Pengajuan';
$this->lang['invoiceNumber'] =  'No. Invoice';
$this->lang['invoiceDate'] =  'Tgl. Invoice';
$this->lang['blNumber'] =  'No. BL';
$this->lang['blDate'] =  'Tgl. BL';
$this->lang['registerNumber'] =  'No. Pendaftaran';
$this->lang['registerDate'] =  'Tgl. Pendaftaran';
$this->lang['valueType'] =  'Jenis Nilai';

$this->lang['itemBarcode'] = 'Barcode Barang';
$this->lang['itemCode'] = 'Kode Barang';
$this->lang['hs'] = 'HS';
$this->lang['countryOfOriginId'] = 'ID Asal Negara';
$this->lang['facility'] = 'Fasilitas';
$this->lang['alcoholContent'] = 'Kadar Alkohol';
$this->lang['alcohol'] = 'Alkohol';
$this->lang['mililiter'] = 'Mililiter';
$this->lang['qtyCarton'] = 'Isi / Carton';
$this->lang['qtyPackage'] = 'Jml. Kemasan';
$this->lang['value'] = 'Nilai';
$this->lang['putAway'] = 'Put Away';
$this->lang['putAwayDate'] = 'Tgl. Put Away';
$this->lang['afterPutAwayQty'] = 'Jml. Telah di Put Away';
$this->lang['originLocation'] = 'lokasi Asal';
$this->lang['targetLocation'] = 'lokasi Tujuan';
$this->lang['transitLocation'] = 'lokasi Transit';
$this->lang['gol'] = 'Gol';
$this->lang['kind'] = 'Tipe';

$this->errorMsg['putAway'][1] = 'Penerimaan Barang tidak boleh kosong.';
$this->errorMsg['putAway'][2] = 'Jumlah barang di terima harus lebih besar dari 0.';
$this->errorMsg['putAway'][3] = 'Jumlah tidak boleh melebihi jumlah yang diterima.';

$this->lang['activationEmailContent'] = 'Hi {{CUSTOMER_NAME}},
									 <br>
									Terima kasih telah melakukan registrasi. Untuk menyelesaikan proses registrasi silahkan klik link dibawah ini untuk melakukan verifikasi akun dan email Anda.
									<br><br> 
									{{ACTIVATION_LINK}}
									<br><br> 
									Salam,<br>
									{{COMPANY_NAME}}
								';
								
												
$this->lang['resetPasswordRequestEmailContent'] = 'Hi {{CUSTOMER_NAME}},
			 <br>
			 Anda atau seseorang telah mengajukan permohonan untuk melakukan reset password. Silahkan klik link dibawah ini untuk melakukan reset password.<br> 
			{{RESET_PASSWORD_LINK}}
			 <br><br> 
			Salam,<br>
			{{COMPANY_NAME}}';
			
			
$this->lang['resetPasswordContent'] =  '
					Hi  {{CUSTOMER_NAME}},
					 <br>
					  Password Anda telah diubah menjadi <strong>{{NEW_PASSWORD}}</strong><br><br>
					  Salam,<br>
					{{COMPANY_NAME}}';
				';';			

$this->lang['paymentSuccessContent'] =  'Transaksi berhasil. Terima kasih telah melakukan transaksi di situs kami.';
$this->lang['paymentPendingContent'] =  'Terima kasih telah melakukan transaksi di situs kami.<br>Silahkan melakukan pembayaran sesuai dengan instruksi yang telah kami kirimkan ke email Anda. Terima kasih.';
  
?>
