<?php  

// $pdf->customSettings(
//    array( 
//         'footer' => '', 
//         'showPrintHeader' => false,
//         ) 
//);  
includeClass('Termination.class.php');
$termination = createObjAndAddToCol( new Termination()); 

$obj = $termination;
$generateReportContent = function ($dataset){ 
    
    setlocale(LC_TIME, 'id_ID.utf8');
$obj = new Termination(); 
$salesOrderSubscription = new SalesOrderSubscription(); 
$installationBAST = new InstallationBAST(); 
$customer = new Customer(); 
$employee = new Employee(); 
$media = new Media(); 
$setting = new Setting(); 
$termOfPayment = new TermOfPayment();
    
$rs = $dataset['rs'];
 
$month = array(
                '01' => 'Januari',
                '02' => 'Febuari',
                '03' => 'Maret',
                '04' => 'April',
                '05' => 'Mei',
                '06' => 'Juni',
                '07' => 'Juli',
                '08' => 'Agustus',
                '09' => 'September',
                '10' => 'Oktober',
                '11' => 'November',
                '12' => 'Desember',
        );
$mnth = $month[date("m",strtotime($rs[0]['trdate']))];
$mnthTerminate = $month[date("m",strtotime($rs[0]['terminatedate']))];
$days = array(
                '1' => 'Senin',
                '2' => 'Selasa',
                '3' => 'Rabu',
                '4' => 'Kamis',
                '5' => 'Jumat',
                '6' => 'Sabtu',
                '7' => 'Minggu',

        );    
    
$day = $days[date("N",strtotime($rs[0]['trdate']))];
$dayTerminate = $days[date("N",strtotime($rs[0]['terminatedate']))];
$years = date("Y",strtotime($rs[0]['trdate']));
$yearsTerminate = date("Y",strtotime($rs[0]['terminatedate']));

$daynumber = date("d",strtotime($rs[0]['trdate']));
$daynumberterminate = date("d",strtotime($rs[0]['terminatedate']));
    
$date = $daynumber.' '.$mnth.' '.$years;
$dateTerminate = $daynumberterminate.' '.$mnthTerminate.' '.$yearsTerminate;
$rsSO = $salesOrderSubscription->getDataRowById($rs[0]['salesorderkey']);
$rsCustomer = $customer->getDataRowById($rsSO[0]['customerkey']);
	
$rsBast = $installationBAST->searchData('','',true,' and '.$installationBAST->tableName.'.refkey = '.$obj->oDbCon->paramString($rsSO[0]['pkey']));

$dateBast = (!empty($rsCustomer[0]['subscriptionactivationdate']) && !in_array($rsCustomer[0]['subscriptionactivationdate'], array('0000-00-00', '1970-01-01'))  ) ? date("d",strtotime($rsCustomer[0]['subscriptionactivationdate'])).' '.$month[date("m",strtotime($rsCustomer[0]['subscriptionactivationdate']))].' '.date("Y",strtotime($rsCustomer[0]['subscriptionactivationdate'])) : '';   
    
$rsEmployee = $employee->getDataRowById($rsSO[0]['employeekey']);
$invoiceAddress = '';
$salesMan = '';
$attentionMan = '';
$customerName = '';
$customerPhone = '';
$customerMedia = '';
$customerSID = '';
    $invoiceAddress = $rsCustomer[0]['address'];
    $attentionMan = $rsCustomer[0]['attention'];
    $customerName = $rsCustomer[0]['name'];
    $customerPhone = $rsCustomer[0]['phone'];
    $customerSID = $rsCustomer[0]['sid'];
    $rsMedia = $media->getDataRowById($rsCustomer[0]['mediakey']);
    $customerMedia = $rsMedia[0]['name'];
    $rsSales = $employee->getDataRowById($rsCustomer[0]['saleskey']);
    $subscriptionStatus = '';
    if($rsCustomer[0]['subscriptionstatuskey']==2){
        $subscriptionStatus = 'Dinonaktifkan';
    }
    if(!empty($rsSales))
        $salesMan = $rsSales[0]['name'];

    
if(!empty($rs[0]['representedkey'])){
    $rsRepresent = $employee->getDataRowById($rs[0]['representedkey']);
}


$fontWeight = 'bold';

$companyAddress = $setting->loadSetting('companyAddress'); 
    
$companyPhone = $setting->getDetailByCode('companyPhone');
$arrCompanyPhone = array();  
for($i=0;$i<count($companyPhone);$i++) 
    array_push($arrCompanyPhone, $companyPhone[$i]['value']);

$companyContact = '';
if(!empty($arrCompanyPhone))
    $companyContact = implode (', ', $arrCompanyPhone); 
    

$profileImg = $obj->loadSetting('companyLogo'); 
//$img =  HTTP_HOST.'phpthumb/phpThumb.php?src='.$obj->phpThumbURLSrc.'setting/companyLogo/'.$profileImg.'&w=1000&h=500&hash='.getPHPThumbHash($profileImg);

//$arrRecipient = array();
//array_push($arrRecipient, $rs[0]['recipientname'], str_replace(chr(13),'<br>',$rs[0]['recipientaddress']), $rs[0]['recipientphone']);
    
$html = $obj->printSetting['defaultStyle'];
$html .= ' 
<table cellpadding="2" > 
<tr><td><div class="title">BERITA ACARA TERMINASI</div></td></tr>
<tr><td><div class="subtitle">No : '.$rs[0]['code'].'</div></td></tr>
</table> 

<div style="clear:both"></div>
<table cellpadding="4" style="font-size:13px;"> 
<tr><td colspan="2">Pada hari ini, '.$day.', tanggal '.ucwords($obj->sayNumber($daynumber)).' bulan '.ucwords($mnth).' tahun '.ucwords($obj->sayNumber($years)).' ('.$obj->formatDBDate($rs[0]['trdate'],'d-m-Y').') yang bertanda tangan dibawah ini :</td></tr>
</table>
<table style="font-size:14px;">
<tr><td></td></tr>
</table>
<table cellpadding="4" style="font-size:13px;" >
<tr><td style="width:10px;"></td><td style="width:30px;">1. </td><td style="width:150px;">Nama Perusahaan</td><td style="width:15px">:</td><td style="width:450px;">PT Mitra Visioner Pratama</td></tr>
<tr><td style="width:10px;"></td><td></td><td>Diwakili Oleh</td><td style="width:15px">:</td><td>'.$rs[0]['representedname'].'</td></tr>
<tr><td style="width:10px;"></td><td></td><td>Alamat</td><td style="width:15px">:</td><td>'.$companyAddress.'</td></tr>
</table>
<table cellpadding="8" style="font-size:13px;">
<tr><td  style="width:400px;">dalam hal ini bertindak sebagai MVNET dan</td></tr>
</table>

<table cellpadding="4" style="font-size:13px;">
<tr><td style="width:10px;"></td><td style="width:30px;">2. </td><td style="width:150px;">Nama Perusahaan</td><td style="width:15px">:</td><td style="width:450px;">'.$customerName.'</td></tr>
<tr><td style="width:10px;"></td><td></td><td>Diwakili Oleh</td><td style="width:15px">:</td><td></td></tr>
<tr><td style="width:10px;"></td><td></td><td>Alamat</td><td style="width:15px">:</td><td>'.$invoiceAddress.'</td></tr>
</table> 
<table cellpadding="8" style="font-size:13px;">
<tr><td  style="width:400px;">dalam hal ini bertindak sebagai PELANGGAN.</td></tr>
</table>

<table cellpadding="4" style="font-size:13px;">
<tr><td colspan="2">Dengan ini menyatakan bahwa layanan :</td></tr>
<tr><td style="width:30px;"></td><td style="width:150px;">Jenis Layanan</td><td style="width:15px">:</td><td>'.$customerMedia.'</td></tr>
<tr><td></td><td>Alamat Pemasangan</td><td style="width:15px">:</td><td>'.$invoiceAddress.'</td></tr>
<tr><td></td><td>Tanggal Berita Acara</td><td style="width:15px">:</td><td>'.$dateBast.'</td></tr>
<tr><td></td><td>CID</td><td style="width:15px">:</td><td>'.$customerSID.'</td></tr>
<tr><td></td><td>Kapasitas</td><td style="width:15px">:</td><td>'.$rsBast[0]['capacity'].'</td></tr>
</table>
<table style="font-size:13px;">
<tr><td></td></tr>
</table>

<table style="font-size:13px;">
<tr><td colspan="2">Akan diteriminasi pada tanggal '.$dateTerminate.'. Bahwa, PELANGGAN telah melakukan terminasi dini atas jaringan MVNET dengan rincian sebagai berikut :</td></tr>
</table>

<table cellpadding="4" style="font-size:13px;">
<tr><td style="width:30px;"></td><td style="width:150px;">Biaya Bulanan</td><td style="width:15px">:</td><td>Rp. '.$obj->formatNumber($rsSO[0]['subtotalmonthly']).'</td></tr>
<tr><td></td><td>Masa Kontrak</td><td style="width:15px">:</td><td></td></tr>
<tr><td></td><td>Sisa Kontrak</td><td style="width:15px">:</td><td></td></tr>
<tr><td></td><td>Biaya Penalty</td><td style="width:15px">:</td><td></td></tr>
</table>
<div style="clear:both"></div>';

  
$html .= '
</table>
<div style="clear:both"></div>';

$html .= '<table >';
    
$html .= '<tr>
            <td style="height:100px; font-size:13px;">'.$customerName.'</td><td style="text-align:center;height:100px; font-size:14px;">PT Mitra Visioner Pratama</td>
          </tr>';

$html .= '</table>';
    
$html .= '<table>';
    
$html .= '<tr>
            <td style="font-weight: '.$fontWeight.'; font-size:14px;"></td><td style="font-weight: '.$fontWeight.'; font-size:14px;"></td>
          </tr>
          <tr>
            <td style="font-size:13px;">
            <table>
            <tr><td style="width:15px">(</td><td style="width:250px"></td><td style="width:15px">)</td></tr>
            </table>
            </td>
            <td style="text-align:center;font-weight:font-size:13px;">'.$rsRepresent[0]['name'].'</td>
          </tr>';
$html .= '</table><div style="clear:both"></div>';
    

    
//$html .= $obj->generateSignLabel($rs); 
    
return $html;
}


?>
