<?php

$apiKey = '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';

$domainName = $_POST['domain'] ?? ''; // buat tau harus akses ke folder domain mana
$fileData   = $_POST['fileData'] ?? '';

if (!isValidDomain($domainName)) die; 

if(empty($_POST['data'])){
     
    
    $DOC_ROOT = rtrim($_SERVER['DOCUMENT_ROOT'], '/') . '/'; 
    $filePath = $DOC_ROOT. '../_temp/'.$domainName.'/data-ai/'.$fileData;  
    if (!file_exists($filePath)) die;
    

    // baca data JSON 
    $data = json_decode(file_get_contents($filePath),true);
   
}else{
    $data = json_decode($_POST['data'],true); 
}

 
$action = $data['action'];


switch($data['companyType']){
    
    case '1' : $companyType = 'Retail'; break;
    case '2' : $companyType = 'Transportasi / Forwarding / Jasa'; break;
    case '5' : $companyType = 'Transportasi / Forwarding / Jasa'; break;
    
    default : $companyType ='Retail';
}

if(empty($data['data'])) {
    echo 'respon gagal';
    die;
}

$maxOutputToken = 10000;
$systemPrompt = '';
$userPrompt = '';

switch($action){
    
    case 'incomeStatement' : 
                                // Build prompt
                                $systemPrompt = "
                                Anda adalah seorang Accounting Manager senior di Indonesia. Anda bekerja di perusahaan yang bergerak dibidang ".$companyType."
                                
                                Tugas Anda adalah menganalisis laporan laba rugi (income statement).
                                
                                Aturan wajib:
                                1. Gunakan Bahasa Indonesia formal untuk laporan manajemen.
                                2. Sajikan analisis dalam bentuk RINGKASAN EKSEKUTIF, dengan format sebagai berikut
                                
                                
                                <h1>ANALISA LAPORAN LABA RUGI</h1>
                                <h2>
                                    <div class=\"div-table\">
                                         <div class=\"div-table-row\">
                                            <div class=\"div-table-col-3\" style=\"font-weight:bold; padding-right:0.5em\">Nama Perusahaan</div>
                                            <div class=\"div-table-col-3\">".$data['companyName']."</div>
                                         </div>
                                         <div class=\"div-table-row\">
                                            <div class=\"div-table-col-3\" style=\"font-weight:bold\">Periode</div>
                                            <div class=\"div-table-col-3\"></div>
                                         </div>
                                    </div>
                                </h2>
                                <br><br>
                                
                                <detail laporan>
                                -profitabilitas
                                -struktur biaya
                                -tren utama
                                -risiko bisnis
                                -tabel ratio : Gross Profit Margin (GPM), Net Profit Margin (NPM), Operating Profit Margin (OPM)
                                Gunakan data yang tersedia, dan berikan penilaian rationya (tidak perlu rumus), apakah bagus atau tidak
                                
                                3. data yang diberikan dalam format JSON, jika memiliki periode lebih dari satu, maka jelaskan per periode. berikut adalah format JSON yang diberikan
                                     {
                                      \"period\":  periode transaksi,
                                      \"code\": kode COA,
                                      \"name\": nama COA,
                                      \"amount\": nilai transaksi,
                                      \"category\": kategori akun, gunakan kategori \"Pendapatan\", \"Biaya Operasional\" dan \"Beban Operasional\" untuk menghitung OPM
                                    },
                                
                                ";
                                
                                
                                $userPrompt = "
                                Silakan lakukan analisis sesuai dengan instruksi.
                                DATA:
                                " . json_encode($data['data']);
                            break;
                            
     case 'balanceSheet' : 
                                // Build prompt
                                $systemPrompt = "
                                Anda adalah seorang Accounting Manager senior di Indonesia. Anda bekerja di perusahaan yang bergerak dibidang ".$companyType."
                                
                                Tugas Anda adalah menganalisis laporan neraca.
                                
                                Aturan wajib:
                                1. Gunakan Bahasa Indonesia formal untuk laporan manajemen.
                                2. Sajikan analisis dalam bentuk RINGKASAN EKSEKUTIF, dengan format sebagai berikut
                                
                                
                                <h1>ANALISA LAPORAN NERACA KEUANGAN</h1>
                                <h2>
                                    <div class=\"div-table\">
                                         <div class=\"div-table-row\">
                                            <div class=\"div-table-col-3\" style=\"font-weight:bold; padding-right:0.5em\">Nama Perusahaan</div>
                                            <div class=\"div-table-col-3\">".$data['companyName']."</div>
                                         </div>
                                         <div class=\"div-table-row\">
                                            <div class=\"div-table-col-3\" style=\"font-weight:bold\">Periode</div>
                                            <div class=\"div-table-col-3\"></div>
                                         </div>
                                    </div>
                                </h2>
                                <br><br>
                                
                                <detail laporan> 
                                Analisis laporan neraca perusahaan berikut secara komprehensif. Jelaskan kondisi likuiditas, solvabilitas, dan struktur modal perusahaan dengan menghitung dan menginterpretasikan rasio keuangan yang relevan (current ratio, quick ratio, debt to asset ratio, dan debt to equity ratio). Berikan kesimpulan mengenai kesehatan keuangan perusahaan serta rekomendasi singkat.
                                -tabel ratio : Likuiditas, solvabilitas, Rasio Aktivitas dan struktur modal perusahaan
                                Gunakan data yang tersedia, dan berikan penilaian rationya (tidak perlu rumus), apakah bagus atau tidak
                                
                                3. data yang diberikan dalam format JSON, jika memiliki periode lebih dari satu, maka jelaskan per periode. berikut adalah format JSON yang diberikan
                                     {
                                      \"period\":  periode transaksi,
                                      \"code\": kode COA,
                                      \"name\": nama COA,
                                      \"amount\": nilai transaksi,
                                      \"category\": kategori akun                                   
                                      },
                             
                                ";
                                
                                
                                $userPrompt = "
                                Silakan lakukan analisis sesuai dengan instruksi.
                                DATA:
                                " . json_encode($data['data']);
                            break;
        
    case 'ARAging':             // Build prompt
                                $systemPrompt = "
                                Posisikan diri Anda sebagai Finange Manager di perusahaan yang bergerak dibidang ".$companyType.".
                             
                                
                                1. Tolong analisa ratio dibawah ini untuk Laporan Umur Piutang, untuk setiap mata uang (multi currency): 
                                Rasio Piutang Jatuh Tempo (Overdue Receivables Ratio)
                                Rasio Piutang Tidak Lancar
                                Days Sales Outstanding (DSO) 
                                
                                2. Setiap piutang dicatat dalam mata uang nya masing-masing, tidak dikonversi menjadi IDR, jadi jangan menjumlahkan outstanding yang berbeda mata uang nya.
                                3. Data yang diberikan dalam format JSON, dua level. level pertama adalah Jenis Mata Uang, level kedua adalah informasi umur dan outstanding per mata uang. 
                                4. format laporan :
                                    <h1>ANALISA LAPORAN UMUR PIUTANG</h1>
                                    <h2>
                                        <div class=\"div-table\">
                                             <div class=\"div-table-row\">
                                                <div class=\"div-table-col-3\" style=\"font-weight:bold; padding-right:0.5em\">Nama Perusahaan</div>
                                                <div class=\"div-table-col-3\">".$data['companyName']."</div>
                                             </div> 
                                        </div>
                                    </h2>
                                    <br><br>
                                    ulangi detail dibawah ini untuk setiap mata uang
                                    
                                    <!-- loop -->
                                    Tampilan table umur piutang, pastikan header kolom dan kolomnya selaras, tidak lari kolomnya.
                                    kolom pertama adalah umurnya, kolom kedua adalah mata uang, dan kolom ketiga adalah nilai outstandingnya, rata kanan.
                                    
                                    <br>
                                    
                                    Rasio Piutang Jatuh Tempo
                                    Rasio Piutang Tidak Lancar
                                    Days Sales Outstanding
                                    
                                    
                                    <!-- end loop -->
                                
                                ";
                                      
                                $userPrompt = "
                                Silakan lakukan analisis sesuai dengan instruksi.
                                DATA:
                                " . json_encode($data['data']);
                        break;
        
    case 'seoAnalyze':
                                  // Build prompt
                                $systemPrompt = "
                                Posisikan diri Anda sebagai SEO specialist yang paham dengan struktur SEO.
                                
                                Tolong analisa :
                                
                                Riset keyword (short-tail & long-tail)
                                Search intent (informational, commercial, transactional)
                                Meta Title, Meta Description, Keyword
                                Struktur konten SEO (H1–H3)
                                Optimasi on-page & content SEO
                                Penyesuaian untuk Google Indonesia
                                Fokus pada ranking, CTR, dan traffic organik";
                                
                                $userPrompt = "
                                Silakan lakukan analisis sesuai dengan instruksi.
                                alamat website :
                                " . json_encode($data['data']);
                        break;
        
        
    case 'salesOrderSummary':
                                // Build prompt
                                $systemPrompt = "
                                Posisikan diri Anda sebagai Marketing Manager di perusahaan yang bergerak dibidang ".$companyType.".
                                
                                Tolong analisa :
                                
                                Tren penjualan per cabang dengan aturan sebagai berikut :
                                 
                                1.  Laporan diurutkan berdasarkan periode 
                                2.  format laporan :
                                    <h1>ANALISA TREN PENJUALAN</h1>
                                    <h2>
                                        <div class=\"div-table\">
                                             <div class=\"div-table-row\">
                                                <div class=\"div-table-col-3\" style=\"font-weight:bold; padding-right:0.5em\">Nama Perusahaan</div>
                                                <div class=\"div-table-col-3\">".$data['companyName']."</div>
                                             </div> 
                                             <div class=\"div-table-row\">
                                                <div class=\"div-table-col-3\" style=\"font-weight:bold; padding-right:0.5em\">Periode</div>
                                                <div class=\"div-table-col-3\">".$data['period']."</div>
                                             </div> 
                                        </div>
                                    </h2>
                                    <br><br>
                                    
                                    <h3>PENJUALAN PER CABANG</h3>.
                                     
                                    <!-- loop per cabang-->
                                    <b><!-- nama cabang --></b>
                                      <div class=\"div-table\" style=\"margin-bottom:1em\">  
                                         <div class=\"div-table-row\">
                                            <div class=\"div-table-col-3\" style=\"font-weight:bold;\">Periode</div>
                                            <div class=\"div-table-col-3\" style=\"font-weight:bold;\">Total Penjualan</div>
                                         </div>   
                                            <!-- loop -->
                                             <div class=\"div-table-row\"> 
                                                <div class=\"div-table-col-3\"><!-- Periode --></div>
                                                <div class=\"div-table-col-3\"><!-- total penjualan --></div>
                                             </div>  
                                             <!-- end loop -->
                                    </div>
                                    <!-- rekomendasi untuk cabang -->
                                    <!-- end loop per cabang-->
                                    
                                    
                                    
                                    Data yang digunakan adalah format json berikut :
                                    ".json_encode($data['data']['salesByWarehouse'])."
                                    
                                    
                                    <h3>PELANGGAN TERATAS</h3>.
                                     
                                    <!-- loop per periode -->
                                     <b><!-- periode --></b>
                                      <div class=\"div-table\" style=\"margin-bottom:1em\">  
                                         <div class=\"div-table-row\">
                                            <div class=\"div-table-col-3\" style=\"font-weight:bold;\">Pelanggan</div>
                                            <div class=\"div-table-col-3\" style=\"font-weight:bold;\">Total Penjualan</div>
                                         </div>  

                                        <!-- loop -->
                                             <div class=\"div-table-row\">
                                                <div class=\"div-table-col-3\"><!-- Nama Pelanggan --></div>
                                                <div class=\"div-table-col-3\"><!-- total penjualan --></div>
                                             </div>  
                                        <!-- end loop -->
                                    </div> 
                                    <!-- end loop per periode-->
                                    <!-- analisa penjualan berdasarkan pelanggan teratas -->
                                    
                                    
                                    Data yang digunakan adalah format json berikut, kelompokan dulu berdasarkan periode, lalo urutan berdasarkan total penjualan terbesar :
                                    ".json_encode($data['data']['salesByCustomer'])."
                                    
                                    <h3>PERFORMA SALESPERSON</h3>.
                                     
                                    <!-- loop per periode -->
                                    <b><!-- periode --></b>
                                      <div class=\"div-table\" style=\"margin-bottom:1em\">  
                                         <div class=\"div-table-row\"> 
                                            <div class=\"div-table-col-3\" style=\"font-weight:bold;\">Salesperson</div>
                                            <div class=\"div-table-col-3\" style=\"font-weight:bold;\">Total Penjualan</div>
                                         </div>  

                                        <!-- loop -->
                                             <div class=\"div-table-row\"> 
                                                <div class=\"div-table-col-3\"><!-- Nama Salesperson --></div>
                                                <div class=\"div-table-col-3\"><!-- total penjualan --></div>
                                             </div>  
                                        <!-- end loop -->
                                    </div>
                                    <!-- end loop per periode-->
                                    <!-- analisa performa salesperson-->

                                    Data yang digunakan adalah format json berikut, kelompokan dulu berdasarkan periode, lalo urutan berdasarkan total penjualan terbesar :
                                    ".json_encode($data['data']['salesBySalesperson'])."
                                     
                                    <h3>TREN PENJUALAN ITEM</h3>.
                                     
                                    <!-- loop per periode -->
                                    <b><!-- periode --></b>
                                      <div class=\"div-table\" style=\"margin-bottom:1em\">  
                                         <div class=\"div-table-row\"> 
                                            <div class=\"div-table-col-3\" style=\"font-weight:bold;\">Nama Item</div>
                                            <div class=\"div-table-col-3\" style=\"font-weight:bold;\">Total Penjualan</div>
                                         </div>  

                                        <!-- loop -->
                                             <div class=\"div-table-row\"> 
                                                <div class=\"div-table-col-3\"><!-- Nama Item --></div>
                                                <div class=\"div-table-col-3\"><!-- total penjualan --></div>
                                             </div>  
                                        <!-- end loop -->
                                    </div>
                                    <!-- end loop per periode-->
                                    <!-- analisa penjualan item-->

                                    Data yang digunakan adalah format json berikut, kelompokan dulu berdasarkan periode, lalo urutan berdasarkan total penjualan terbesar :
                                    ".json_encode($data['data']['salesByItem'])."
                                     
                                ";
                                
                                $userPrompt = "
                                Silakan lakukan analisis sesuai dengan instruksi.
                                ";
                        break;
                            
    default : break;    
    
}



if(empty($userPrompt)) {
    echo 'respon gagal';
    die;
}

$systemPrompt .= ' KETENTUAN TAMBAHAN
                                   1. Kembalikan hasil analisis dalam format HTML MURNI.
                                   2. Jangan gunakan Markdown.
                                   3. Gunakan elemen HTML seperti <h2>, <h3>, <p>, <ul>, dan <table>. 
                                    - tag <table> ganti dengan <div class=\"div-table\">
                                    - tag <tr> atau <th> ganti dengan <div class=\"div-table-row\"> 
                                    - tag <td> ganti dengan <div class=\"div-table-col-3\">
                                    - format akan menjadi 
                                        <div class=\"div-table\">
                                            <!-- baris header -->
                                            <div class=\"div-table-row col-header\">
                                                <div class=\"div-table-col-3\">
                                                    <!-- Nama kolom disini -->
                                                </div>
                                            </div>
                                            <!-- baris detail -->
                                            <div class=\"div-table-row\">
                                                <div class=\"div-table-col-3\">
                                                 <!-- Isi data disini -->
                                                </div>
                                            </div>
                                        </div>
                                    - jangan ubah urutan kolom dan jangan tambahkan kolom lain    
                                    - format angka dalam table gunakan rata kanan
                                    - jika ada format tanggal, jangan diubah, gunakan sesuai dengan data yang diberikan
                                 4. maksimal dalam batas output '.$maxOutputToken.' token.';


// OpenAI request payload
$data = [
    "model" => "gpt-4.1-mini",
    "input" => [
        ["role" => "system", "content" => $systemPrompt],
        ["role" => "user", "content" => $userPrompt]
    ],
    "temperature" => 0.0,
    "max_output_tokens" => $maxOutputToken
];

// Send request
$ch = curl_init("https://api.openai.com/v1/responses");
curl_setopt_array($ch, [
    CURLOPT_POST => true,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        "Content-Type: application/json",
        "Authorization: Bearer $apiKey"
    ],
    CURLOPT_POSTFIELDS => json_encode($data)
]);

$response = curl_exec($ch);

if (curl_errno($ch)) {
    http_response_code(500);
    echo json_encode(["error" => curl_error($ch)]);
    exit;
}

curl_close($ch);

$response = json_decode($response, true);
$result = $response['output'][0]['content'][0]['text'];

echo $result;


function isValidDomain($domain) {
    // normalize
    $domain = strtolower(trim($domain));

    // exact domain or subdomain
    return $domain === 'wintera.co.id'
        || preg_match('/\.wintera\.co\.id$/', $domain);
}
?>