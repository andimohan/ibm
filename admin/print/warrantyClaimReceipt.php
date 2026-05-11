<?php 

$obj = $warrantyClaimProgress;
 
$generateReportContent = function ($dataset){ 
 
$obj = new warrantyClaimProgress();  
$warrantyClaim = new WarrantyClaim();
$warehouse = new Warehouse();
$employee = new Employee();
    
$rs = $dataset['rs']; 
$rs = $obj->searchData('', '', true, ' and '.$obj->tableName.'.pkey = '.$rs[0]['pkey'].' ');	      
//$rsDetail = $obj->getDetailWithRelatedInformation($rs[0]['pkey']);
$rsWarehouse = $warehouse->getDataRowById($rs[0]['warehousekey']); 

$trnotes = (!empty($rs[0]['trdesc'])) ? '<div style="clear:both"></div><strong>Catatan :</strong> <br>' . str_replace(chr(13),'<br>',$rs[0]['trdesc']) : '';
$html = $obj->printSetting['defaultStyle'];
    
$customerphone = array();
if(!empty($rs[0]['customerphone'])) array_push($customerphone,$rs[0]['customerphone']);
if(!empty($rs[0]['customermobile'])) array_push($customerphone,$rs[0]['customermobile']);
    
$html .= ' 
<table cellpadding="2" > 
<tr><td><div class="title">Tanda Terima Pengembalian Barang</div></td></tr>
<tr><td><div class="subtitle">'.$rs[0]['code'].' / '.$rs[0]['warrantycode'].'</div></td></tr>
</table> 

<div style="clear:both"></div>
 
<table>
<tr>
<td style="width:300px;" >
<table cellpadding="2">
<tr><td class="header-row-header" >Tanggal</td><td style="width:10px; text-align:center">:</td><td style="width:200px">'.$obj->formatDBDate($rs[0]['trdate'],'d / m / Y').'</td></tr>
<tr><td class="header-row-header">Pelanggan</td><td style="text-align:center">:</td><td >'. $rs[0]['customername'] .'</td></tr>
<tr><td class="header-row-header">No. Telp</td><td style="text-align:center">:</td><td >'. implode(', ',$customerphone) .'</td></tr>
</table>
</td>
<td style="width:370px;"> 
<table cellpadding="2">
<tr><td></td></tr>
</table>
</td>
</tr>
</table>
<div></div>';                                                                            
    $html .= '<div><strong>'.strtoupper($obj->lang['claimedItem']).'</strong></div><br>';    
    $html .= '<table  cellpadding="2">';
    $html .= '<tr>
                <td class="header-row-header">Serial Number</td>
                <td style="width:10px; text-align:center">:</td>
                <td style="width:200px">'.$rs[0]['serialnumber'].'</td>
                <td style="width:20px;"> </td>
                <td class="header-row-header">Tgl. Akhir Garansi</td>
                <td style="width:10px; text-align:center">:</td>
                <td style="width:200px">'.$obj->formatDBDate($rs[0]['warrantydate'],'d / m / Y').'</td>
            </tr>';
    $html .= '<tr>
                <td><b>Part Number</b></td>
                <td style="text-align:center">:</td>
                <td>'.$rs[0]['partnumber'].'</td>
                <td></td>
                <td><b>Barang</b></td>
                <td>:</td>
                <td>'.$rs[0]['itemname'].'</td>
             </tr>';
    $html .= '</table><div style="clear:both"></div>';

    
    $html .= '<div ><strong>'.strtoupper($obj->lang['replacementItem']).'</strong></div><br>'; 
    $html .= '<table cellpadding="2">';
    $html .= '<tr>
                <td class="header-row-header">Serial Number</td>
                <td style="width:10px; text-align:center">:</td>
                <td style="width:200px">'.$rs[0]['newserialnumber'].'</td>
                <td style="width:20px"></td>
                <td class="header-row-header">Tgl. Akhir Garansi</td>
                <td style="width:10px; text-align:center">:</td>
                <td  style="width:200px">'.$obj->formatDBDate($rs[0]['newwarrantydate'],'d / m / Y').'</td>
            </tr>';
    $html .= '<tr>
                <td><b>Part Number</b></td>
                <td style="text-align:center">:</td>
                <td>'.$rs[0]['newpartnumber'].'</td>
                <td></td>
                <td><b>Barang</b></td>
                <td>:</td>
                <td>'.$rs[0]['newitemname'].'</td>
             </tr>';
    $html .= '</table><div style="clear:both"></div>';

    //$rsItemContent = $warrantyClaim->getItemContentDetail($rsDetail[$i]['pkey'],' and '.$obj->tableContentOfPackage .'.ischeck = 1 '); 
    
    $tabelItem = ''; 
    $tabelItem ='<div><strong>Kelengkapan Produk</strong></div><table cellpadding="2">';
    if (empty($rsItemContent))
        $tabelItem .= '<tr><td style="text-align:left; width:100px">Tidak Ada</td></tr>'; 
    
    for($j=0;$j<count($rsItemContent);$j++){
        if ($rsItemContent[$j]['qty'] <= 0) continue; 
        $tabelItem .= '<tr><td style="text-align:right; width:25px">'.$obj->formatNumber($rsItemContent[$j]['qty']).' x</td><td>'.$rsItemContent[$j]['itemname'].'</td></tr>';
    } 
    $tabelItem .= '</table>';
    
$html .= '<div><strong>Biaya Upgrade : </strong> Rp. '.$obj->formatNumber($rs[0]['amount']).'</div>';
    
$html .= '<div style="clear:both"></div>';  
$html .= $obj->loadSetting('warrantyReceiptAgreement');
$html .= '<div style="clear:both"></div>'; 
    
$confirmedName = '';
if (!empty($rs[0]['confirmedby'])){ 
    $rsEmployee = $employee->getDataRowById($rs[0]['confirmedby']);
    $confirmedName = $rsEmployee[0]['name'];
}
    
    
//$rsEmployeeCreated = $employee->getDataRowById($rs[0]['createdby']);
$arrSignLabel = array(); 
array_push($arrSignLabel, array('Disetujui',$confirmedName));  
array_push($arrSignLabel, array('Mengetahui','')); 
array_push($arrSignLabel, array('Customer',''));  

 $html .=' 
        <table cellpadding="4" class="sign">
        <tr>'; 
        for ($i=0;$i<count($arrSignLabel);$i++){
            $html .='<td  class="sign-col" style="height:40px;"><strong>'.$arrSignLabel[$i][0].'</strong></td>';
            if ($i <> count($arrSignLabel) - 1)
                $html .= '<td class="sign-col-space"></td>';
        }
        $html .='</tr> <tr><td colspan = "'.(count($arrSignLabel) + 1).'"></td></tr>
        <tr>'; 
        for ($i=0;$i<count($arrSignLabel);$i++){ 
            $html .='<td  class="sign-name"></td>';
            if ($i <> count($arrSignLabel) - 1)
                $html .= '<td class="sign-col-space"></td>';
        }
        $html .='</tr> 
      <tr>'; 
        for ($i=0;$i<count($arrSignLabel);$i++){
            $arrSignLabel[$i][1] = (isset($arrSignLabel[$i][1])) ? $arrSignLabel[$i][1] : '';
            $html .='<td>'.$arrSignLabel[$i][1].'</td>';
            if ($i <> count($arrSignLabel) - 1)
                $html .= '<td class="sign-col-space"></td>';
        }
        $html .='</tr> 
        </table>' ;
    
return $html;
     
}

?>