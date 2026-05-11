<?php 

$borderTop = 'border-top:1px solid black;';
$borderLeft = 'border-left:1px solid black;';
$borderRight = 'border-right:1px solid black;';
$borderBottom = 'border-bottom:1px solid black;';

//Kolom ttd
$signTable = '
<div></div>
<table cellpadding="3" style="'.$borderLeft.$borderRight.$borderBottom.'text-align:center;font-weight:bold">
<tr><td style="width:100px;border:1px solid black;">Direksi</td><td style="width:110px;border:1px solid black;">Kabag Keu/Acc</td><td style="width:120px;border:1px solid black;">Kabag</td><td style="width:120px;border:1px solid black;">Acounting</td><td  style="width:120px;border:1px solid black;">Kasir</td><td style="width:110px;border:1px solid black;">Penerima</td></tr>
<tr><td style="width:100px;'.$borderRight.'"></td><td style="width:110px;'.$borderRight.'"></td><td style="width:120px;'.$borderRight.'"></td><td style="width:120px;'.$borderRight.'"></td><td style="width:120px;'.$borderRight.'"></td></tr>
<tr><td style="width:100px;'.$borderRight.'"></td><td style="width:110px;'.$borderRight.'"></td><td style="width:120px;'.$borderRight.'"></td><td style="width:120px;'.$borderRight.'"></td><td style="width:120px;'.$borderRight.'"></td></tr>
<tr><td style="width:100px;'.$borderRight.'"></td><td style="width:110px;'.$borderRight.'"></td><td style="width:120px;'.$borderRight.'"></td><td style="width:120px;'.$borderRight.'"></td><td style="width:120px;'.$borderRight.'"></td></tr>
</table>';    

$pdf->setCustomSettings(
    array( 
         'paperSetting' => 'A5,L',
         'showPrintHeader' => false, 
		 'marginFooter' => '25',
         'footer' => $signTable,  
         ) 
);

$generateReportContent = function ($dataset){  
    
global $pdf;

$obj = new CashIn();
$chartOfAccount = new ChartOfAccount();
    
$rs = $dataset['rs'];
$rsDetail = $obj->getDetailWithRelatedInformation($rs[0]['pkey']); 
    
$borderTop = 'border-top:1px solid black;';
$borderLeft = 'border-left:1px solid black;';
$borderRight = 'border-right:1px solid black;';
$borderBottom = 'border-bottom:1px solid black;';
    $profileImg = $obj->loadSetting('companyLogo'); 
$img =  HTTP_HOST.'phpthumb/phpThumb.php?src='.$obj->phpThumbURLSrc.'setting/companyLogo/'.$profileImg.'&w=220&h=110&hash='.getPHPThumbHash($profileImg);
    
    
$html = $obj->printSetting['defaultStyle'];
$html .= '
<table style="'.$borderTop.$borderRight.$borderLeft.'width:660px">
    <tr>
        <td  style="width:170px">
        <table cellpadding="3"> 
            <tr>
                <td style="vertical-align:middle; width:180px;font-size:2.4em;font-weight:bold;font-family:Arial Black;font-style:italic" >OKATRANS</td>
            </tr>
        </table>
        </td>
        <td style="width:280px">
            <table cellpadding="2" style="text-align:left;"> 
            <tr><td></td></tr>
            <tr><td style="width:40px;"></td><td style="text-algin:center"><div class="title">Terima Kas/Bank</div></td></tr>
            <tr><td style="width:200px;font-size:1.2em"><b>Tgl.</b> '.$obj->formatDBDate($rs[0]['trdate'],'d / m / Y').'</td><td style="width:190px;font-size:1.2em"><b>No: '.$rs[0]['code'].' </b></td></tr>
            </table>  
        </td>
        <td style="width:229.9px">

        </td>
    </tr> 
    <tr><td></td></tr>
</table>
';
$html .= '
<table cellpadding="2" style="'.$borderRight.$borderLeft.'">
<tr><td style="width:30px"></td><td class="header-row-header" style="font-size:1.2em;">Dari :</td><td style="width:530px;font-size:1.2em;">'.$rs[0]['recipientname'].'</td></tr> 

</table>   ';


$html .= ' 
<table  cellpadding="3" style="'.$borderLeft.$borderRight.'">
<tr class="col-header" ><td style="width:235px;'.$borderRight.'">Pendapatan</td><td style="width:335px;'.$borderRight.'" >Deskripsi</td><td style="text-align:right; width:110px;'.$borderRight.'">Jumlah</td></tr>';

for ($i=0;$i<count($rsDetail);$i++){ 
  $html .= '<tr><td style="'.$borderRight.'">'.$rsDetail[$i]['codename'].'</td><td style="'.$borderRight.'">'. $rsDetail[$i]['trdesc'] .'</td><td style="text-align:right;'.$borderRight.'">'.$obj->formatNumber($rsDetail[$i]['amount']).'</td></tr>' ; 
}
    
$html .= '</table>' ;
    
$html .= '<table  cellpadding="4" style="'.$borderTop.'">
<tr class="" ><td style="width:235px;"></td><td style="width:335px;text-align:right" ></td><td style="'.$borderRight.$borderBottom.$borderLeft.'text-align:right; width:110px;">'.$obj->formatNumber($rs[0]['grandtotal']).'</td></tr>
<tr class="" ><td style="width:235px;"></td><td style="width:335px;text-align:right" ></td><td style="text-align:right; width:110px;"></td></tr>
</table> 
';


//$html .= $obj->generateSignLabel($rs); 
    
return $html;
}
?>