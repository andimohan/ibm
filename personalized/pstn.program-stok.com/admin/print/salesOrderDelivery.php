<?php 
$pdf->setCustomSettings(
    array( 
         'paperSetting' => 'A5,L', 
         'footer' => '', 
         'showPrintHeader' => false,
         ) 
); 

$obj = $salesOrder;
$generateReportContent = function ($dataset){ 
$obj = new SalesOrder();  
$setting = new Setting(); 
    
$rs = $dataset['rs'];
$rsDetail = $obj->getDetailWithRelatedInformation($rs[0]['pkey']);   
    
$arrRecipient = array();
    
if (!empty($rs[0]['recipientname'])) array_push($arrRecipient, $rs[0]['recipientname']); 
if (!empty($rs[0]['recipientaddress'])) array_push($arrRecipient, str_replace(chr(13),'<br>',$rs[0]['recipientaddress'])); 
if (!empty($rs[0]['recipientphone'])) array_push($arrRecipient, $rs[0]['recipientphone']); 
    
$companyPhone = $setting->getDetailByCode('companyPhone');
$arrCompanyPhone = array();  
for($i=0;$i<count($companyPhone);$i++) 
    array_push($arrCompanyPhone, $companyPhone[$i]['value']);

$companyContact = '';
if(!empty($arrCompanyPhone))
    $companyContact = implode (', ', $arrCompanyPhone);
    
$companyName = strtoupper($obj->loadSetting('companyName'));
     
    
$html = $obj->printSetting['defaultStyle'];
$html .= '<div style="clear:both"></div>';
$html .= '<table>
    <tr>
        <td>
        <table cellpadding="2"> 
            <tr><td class="header-row-header" colspan = "2" style="width:300px">'.$companyName.'<br>'.$companyContact.'</td></tr>
            <tr><td colspan = "2"></td></tr>
            <tr><td class="header-row-header" style="width:70px;">No. Faktur</td><td style="width:10">:</td><td>'.$rs[0]['code'].'</td></tr> 
        </table>
        </td>
        <td>
            <table cellpadding="2"> 
            <tr><td style="width:80px" class="header-row-header">'.ucwords($obj->lang['date']).'</td><td style="width:10">:</td><td style="width:240px" >'.$obj->formatDBDate($rs[0]['trdate'],'d / m / Y').'</td></tr>   
            <tr><td><strong>Kepada Yth.</strong></td><td>:</td><td>'.implode('<br>',$arrRecipient).'</td></tr> 
            </table>  
        </td>
    </tr> 
</table>
<div style="clear:both;"></div>';

$html .= '<table cellpadding="2" class="table-transaction">
<tr class="col-header"><td style="width:40px;text-align:right" >No.</td><td style="width:100px;text-align:center" >Jumlah</td><td style="width:540px;">Nama Barang</td></tr>'; 

$totalQty = 0;
for ($i=0;$i<count($rsDetail);$i++){  
  $totalQty += $rsDetail[$i]['qty'];
  $html .= '<tr><td style="text-align:right">'.($i+1).'.</td><td style="text-align:center">'.$obj->formatNumber($rsDetail[$i]['qty']).' '. $rsDetail[$i]['unitname'] .'</td><td>'.$rsDetail[$i]['itemname'].'</td></tr>' ; 
}
$html .= '</table>';
     
$html .= '
</table>
<div style="clear:both"></div>
Jumlah barang : '.$obj->formatNumber($totalQty).' '. $rsDetail[0]['unitname'].'<br><br>';
    
$arrSignLabel = array(); 
array_push($arrSignLabel, array('Hormat Kami'));
array_push($arrSignLabel, array('Telah Diterima'));

$html .=' 
    <table cellpadding="4" class="sign">
    <tr>'; 
    for ($i=0;$i<count($arrSignLabel);$i++){
        $html .='<td  class="sign-col" style="height:50px"><strong>'.$arrSignLabel[$i][0].'</strong></td>';
        if ($i <> count($arrSignLabel) - 1)
            $html .= '<td class="sign-col-space"></td>';
    }
    $html .='</tr>  
    </table>' ;


return '<div style="font-size:0.9em">'.$html.'</div>';
}
?>
