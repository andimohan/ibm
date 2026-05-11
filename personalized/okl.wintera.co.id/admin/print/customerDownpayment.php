<?php 

$pdf->setCustomSettings(
    array( 
         'showPrintHeader' => false,
         'showPrintFooter' => false,
         'footer' => '',  
         ) 
);

$obj = $customerDownpayment; 
 
$generateReportContent = function ($dataset){ 
global $pdf;
    
$obj = new CustomerDownpayment(); 
$setting = new Setting();
$customer = new Customer();
      
$rs = $dataset['rs']; 

$rsCustomer = $customer->getDataRowById($rs[0]['customerkey']);

$arrCustomer = array();
    
if (!empty($rsCustomer[0]['name'])) array_push($arrCustomer, $rsCustomer[0]['name']); 
if (!empty($rsCustomer[0]['address'])) array_push($arrCustomer, str_replace(chr(13),'<br>',$rsCustomer[0]['address'])); 
    
$companyPhone = $setting->getDetailByCode('companyPhone');
$companyAddress = $setting->loadSetting('companyAddress');
$arrCompanyPhone = array();  
for($i=0;$i<count($companyPhone);$i++) 
    array_push($arrCompanyPhone, 'Telp :'.$companyPhone[$i]['value']);

$companyContact = '';
if(!empty($arrCompanyPhone))
    $companyContact = implode (', ', $arrCompanyPhone);
    
$companyName = strtoupper($setting->loadSetting('companyName'));    
    
    $approvedName = '';
if(!empty($rs[0]['approvedbykey'])){
    $rsApproved = $employee->getDataRowById($rs[0]['approvedbykey']);
    $approvedName = $rsApproved[0]['name'];
}
    
$borderTop = 'border-top:1px solid black;';
$borderLeft = 'border-left:1px solid black;';
$borderRight = 'border-right:1px solid black;';
$borderBottom = 'border-bottom:1px solid black;';    
$border = 'border:1px double black;';    
$sayNumber = $obj->sayNumber($rs[0]['amount']);

$name='';
if(!empty($rsCustomer[0]['alias']))
    $name = $rsCustomer[0]['alias'];
else
    $name = $rsCustomer[0]['name'];
    

$trnotes = (!empty($rs[0]['trdesc'])) ? '<div style="clear:both"></div><strong>Catatan :</strong> <br>' . str_replace(chr(13),'<br>',$rs[0]['trdesc']) : '';
$profileImg = $obj->loadSetting('companyLogo'); 
$img =  ''; //HTTP_HOST.'phpthumb/phpThumb.php?src='.$obj->phpThumbURLSrc.'setting/companyLogo/'.$profileImg.'&w=220&h=110&hash='.getPHPThumbHash($profileImg);

    $html = $obj->printSetting['defaultStyle'];

$html .= ' 

<table>
    <tr>
        <td>
        <table cellpadding="3"> 
            <tr>
                <td style="vertical-align:middle;" ><img src="'.$img.'"></td>
            </tr>
        </table>
        </td>

    </tr> 
</table>
<div style="clear:both"></div>
<div style="clear:both"></div>
<div style="clear:both"></div>
<div style="clear:both"></div>
<div style="clear:both"></div>
';

$html .= '
<table>
    <tr><td class="title"><u><i>KWITANSI</i></u></td></tr>
</table>
<div style="clear:both"></div>
<div style="clear:both"></div>
<div style="clear:both;"></div>
<table cellpadding="4">
    <tr><td style="width:100px;"></td><td style="width:150px;">No. </td><td style="width:20px">:</td><td>'.$rs[0]['code'].'</td></tr>
    <tr><td style="width:100px;"></td><td style="width:150px"><i>Telah terima dari</i></td><td style="width:20px">:</td><td style="width:300px">'.$rsCustomer[0]['name'].'</td></tr>
    <tr><td style="width:100px;"></td><td style="width:150px"><i>Sejumlah uang</i></td><td style="width:20px">:</td><td>'.ucwords($sayNumber).' Rupiah</td></tr>
    <tr><td style="width:100px;"></td><td style="width:150px"><i>Untuk pembayaran</i></td><td style="width:20px">:</td><td>'.str_replace(chr(13),'<br>',$rs[0]['trdesc']).'</td></tr>
</table>
<div style="clear:both"></div>
<div style="clear:both"></div>
<div style="clear:both"></div>
<div style="clear:both"></div>
<div style="clear:both"></div>


';


    
//table ini yang di stay ke bawah
$html .= '
    
<table>
<tr>
<td style="width:300px;">

<table cellpadding="4" style="'.$borderTop.$borderBottom.'width:200px;">
<tr><td style="text-align:right;width:300px;text-align:center">Jumlah  : Rp '.$obj->formatNumber($rs[0]['amount']).'</td></tr>
</table>
<div style="clear:both"></div>

<table cellpadding="2" style="border:1px solid black;width:300px;text-align:center">
<tr><td style="width:300px">Pembayaran Mohon ditransfer ke rekening :</td></tr>
<tr><td style="width:300px">'.$obj->loadSetting('companyName').'</td></tr>
<tr><td style="width:300px">MAYBANK KCP. WARUNG BUNCIT</td></tr>
<tr><td style="width:300px">A/C No 2.017.930800</td></tr>
</table>
</td> 
<td style="width:400px">
<table> 
<tr>
<td></td>
</tr>
</table>
<table> 
<tr>
<td></td>
<td style="text-align:center;"><i>Jakarta, '.$obj->formatDBDate($rs[0]['trdate'],'d F Y').'</i></td></tr>
<tr><td></td><td style="height:60px;text-align:center;"><img src="'.$imgSignature.'" /></td></tr>
<tr>
<td></td>
<td style="text-align:center;"><b>Suprihanto</b></td>
</tr>
</table>
</td>
</tr>
</table>
';
    
    
//$html .= $obj->generateSignLabel($rs); 
return '<div style="font-size:13px">'.$html.'</div>';
}

?>
