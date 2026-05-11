<?php
$pdf->setCustomSettings(
    array(   
         'showPrintHeader' => false,
         ) 
); 


//$obj = $itemOut;
$generateReportContent = function ($dataset){ 
$obj = new ItemOut(); 
$warehouse = new Warehouse(); 
$setting = new Setting(); 
    
$rs = $dataset['rs'];
$rsDetail = $obj->getDetailWithRelatedInformation($rs[0]['pkey'],'',' order by brandname asc, itemcategoryname asc');   
    
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
            <tr><td class="header-row-header" colspan = "2" style="width:300px">'.$companyName.'<br>Telp. '.$companyContact.'</td></tr>
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

$cellArray = array();
array_push($cellArray, array('label' => 'No.', 'width' => '40','align' => 'right'));
array_push($cellArray, array('label' => 'Nama Barang'));
array_push($cellArray, array('label' => 'Jumlah', 'width' => '60','align' => 'right'));

  
$html .= '<table  cellpadding="4" class="table-transaction">';
$html .= $obj->generatePrintTableRow( array('class' => 'col-header', 'cell' =>  $cellArray));

$tempBrandKey = 0;
$tempCategoryKey = 0;
$tempTotal = 0;
$totalQty = 0;
$unitName = '';
    
$arrTotalQty = array();
for ($i=0;$i<count($rsDetail);$i++){  
    $itemCategoryKey = $rsDetail[$i]['itemcategorykey'];
    $itemCategoryName = $rsDetail[$i]['itemcategoryname'];
    $itemUnit = $rsDetail[$i]['unitname'];
    $brandKey = $rsDetail[$i]['brandkey'];
    $unitName = $rsDetail[$i]['unitname'];
        
    if($tempBrandKey <> $brandKey || $tempCategoryKey <> $itemCategoryKey){
         
        if ($i > 0)
            $html .= '<tr><td colspan="2"></td><td style="text-align:right; border-top:1px solid #666;">'.$obj->formatNumber($tempTotal).' '. $unitName.'<br></td></tr>' ; 
   
        $tempTotal = 0;   
    }
    
    
    if(!isset($arrTotalQty[$itemCategoryKey])) $arrTotalQty[$itemCategoryKey] = array('name' => $itemCategoryName,'qty' => 0, 'unit' => $itemUnit);
        
    
    $tempTotal += $rsDetail[$i]['qty'];
    $totalQty += $rsDetail[$i]['qty'];
    $arrTotalQty[$itemCategoryKey]['qty'] += $rsDetail[$i]['qty'];
 
    $html .= '<tr><td style="text-align:right">'.($i+1).'.</td><td>'.$rsDetail[$i]['itemname'].'</td><td style="text-align:right">'.$obj->formatNumber($rsDetail[$i]['qty']).' '. $rsDetail[$i]['unitname'] .'</td></tr>' ; 
     
    $tempBrandKey = $brandKey;
    $tempCategoryKey = $itemCategoryKey; 
     
}    
    
$html .= '<tr><td colspan="2"></td><td style="text-align:right; border-top:1px solid #666;">'.$obj->formatNumber($tempTotal).' '.$unitName .'<br></td></tr>' ; 
   
    
$html .= '</table>';
 
$html .= '<div style="clear:both"></div>';
    
$decription = (!empty($rs[0]['trdesc'])) ? '<br><strong>Catatan</strong> :<br>'.str_replace(chr(13),'<br>',$rs[0]['trdesc']) : '';
    
$html .= '<table cellpadding="2" > 
';
    
$html .= '<tr><td colspan="2" style="">';
$html .= $decription.'

</td><td style="width:227px;text-align:right"><b>Total : '.$obj->formatNumber($totalQty).' '. $unitName.'</b></td></tr>' ; 

$html .= '
</table>
<div style="clear:both"></div>';
    
//$html .= $obj->generateSignLabel($rs);

return '<div style="font-size:0.9em">'.$html.'</div>';
}
?>