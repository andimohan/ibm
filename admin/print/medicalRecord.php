<?php  
$PRINT_SETTINGS =  array(   
         'showPrintHeader' => false,
         );
  
includeClass( array('MedicalRecord.class.php'));

$medicalRecord = createObjAndAddToCol( new MedicalRecord()); 

$obj = $medicalRecord;
$generateReportContent = function ($dataset){ 
$obj = new MedicalRecord(); 
$customer = new Customer(); 
$employee = new Employee(); 
$setting = new Setting(); 
    
$rs = $dataset['rs'];
$rsDetail = $obj->getDetailWithRelatedInformation($rs[0]['pkey']);
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
//    
//$arrHeader = array();
//array_push($arrHeader, $companyName); 
//array_push($arrHeader, $companyTaxRegistrationNumber); 
//array_push($arrHeader, $companyContact); 
//array_push($arrHeader, $companyAddress); 
    
$trnotes = (!empty($rs[0]['trdesc'])) ? '<div style="clear:both"></div><strong>Catatan :</strong> <br>' . str_replace(chr(13),'<br>',$rs[0]['trdesc']) : '';
$img = '';// HTTP_HOST.'phpthumb/phpThumb.php?src='.$obj->phpThumbURLSrc.'setting/companyLogo/'.$profileImg.'&w=180&h=100&hash='.getPHPThumbHash($profileImg);

$arrRecipient = array();
array_push($arrRecipient, $rs[0]['recipientname'], str_replace(chr(13),'<br>',$rs[0]['recipientaddress']), $rs[0]['recipientphone']);
    
$html = $obj->printSetting['defaultStyle'];
$html .= ' 
<table cellpadding="" style="text-align:center;border-bottom:2px solid black"> 
<tr><td rowspan="6" style="width:40px"></td><td rowspan="6" style="width:140px"><img src="'.$img.'"></td><td style="width:425px;font-size:1.8em;font-weight:bold;">PRAKTEK DOKTER UMUM</td><td rowspan="6" style="width:60px"></td></tr>
<tr><td style="font-size:1.4em;font-weight:bold;">'.$companyName.'</td></tr>
<tr><td style="font-size:1.1em;">'.$companyTaxRegistrationNumber.'</td></tr>
<tr><td style="font-size:1.2em;">Telp. '.$companyContact.'</td></tr>
<tr><td>'.$companyAddress.'</td></tr>
<tr><td></td></tr>
</table> 



<div style="clear:both"></div>
<table>
<tr>
<td >
<table cellpadding="2"> 
<tr>
<td class="header-row-header" style="width: 100px;">NAMA</td><td style="width:10px; text-align:center">:</td><td style="width: 330px;">'.$rsCustomer[0]['name'].'</td>
<td class="header-row-header" style="width: 100px;">No. RM</td><td style="width:10px; text-align:center">:</td><td style="width: 330px;">'.$rsCustomer[0]['code'].'</td>
</tr>  
<tr>
<td class="header-row-header" style="width: 100px;">UMUR</td><td style="width:10px; text-align:center">:</td><td style="width: 330px;">'.$age.' Tahun</td>
<td class="header-row-header" style="width: 100px;">DPJP</td><td style="width:10px; text-align:center">:</td><td style="width: 330px;">'.$dpjpname.'</td>
</tr>  
<tr>
<td class="header-row-header" style="width: 100px;">ALAMAT</td><td style="width:10px; text-align:center">:</td><td style="width: 330px;">'.str_replace(chr(13),'<br>',$rsCustomer[0]['address']).'</td>
<td class="header-row-header" style="width: 100px;">ALERGI OBAT</td><td style="width:10px; text-align:center">:</td><td style="width: 330px;">'.str_replace(chr(13),'<br>',$rsCustomer[0]['description']).'</td>
</tr>  
</table> 
</td>
<td></td>
</tr>
<div style="clear:both"></div> ';

$html .= ' 
<table cellpadding="" style="text-align:center;"> 
    <tr><td style="font-size:1.2em;">CATATAN PERKEMBANGAN PASIEN TERINTEGRASI (CPPT)</td></tr>
</table> 

<div style="clear:both"></div> 

<table  cellpadding="4" class="" style="border:1px solid black">
<tr class="col-header" ><td style="width:120px;text-align:center;border-right:1px solid black">Tanggal / Jam</td><td style="width:100px;border-right:1px solid black" >Profesi</td><td style="width:225px;border-right:1px solid black" >SOAP</td><td style="width:225px;" >THERAPI</td></tr>';

for ($i=0;$i<count($rsDetail);$i++){  

  $html .= '<tr><td style="text-align:center;border-right:1px solid black">'.$obj->formatDBDate($rsDetail[$i]['date'],' d / m / Y h:i').'</td><td style="border-right:1px solid black">'.$rsDetail[$i]['employeename'].'</td><td style="border-right:1px solid black">'. str_replace(chr(13),'<br>',$rsDetail[$i]['soapdescription']) .'</td><td style="border-right:1px solid black">'. str_replace(chr(13),'<br>',$rsDetail[$i]['therapydescription']) .'</td></tr>' ; 
}
$html .= '</table>' ;

$html .= '<div style="clear:both"></div>';
        


//$html .= $obj->generateSignLabel($rs); 
    
return $html;
}
?>
