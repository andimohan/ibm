<?php 
   
$obj = $warrantyClaim;
 
$generateReportContent = function ($dataset){ 
 
$obj = new warrantyClaim();  
$warehouse = new Warehouse();
$employee = new Employee();
  
$rs = $dataset['rs']; 
$rs = $obj->searchData('', '', true, ' and '.$obj->tableName.'.pkey = '.$rs[0]['pkey'].' ');	      
$rsDetail = $obj->getDetailWithRelatedInformation($rs[0]['pkey']);
$rsWarehouse = $warehouse->getDataRowById($rs[0]['warehousekey']); 

$trnotes = (!empty($rs[0]['trdesc'])) ? '<div style="clear:both"></div><strong>Catatan :</strong> <br>' . str_replace(chr(13),'<br>',$rs[0]['trdesc']) : '';
$html = $obj->printSetting['defaultStyle'];
$html .= ' 
<table cellpadding="2" > 
<tr><td><div class="title">Tanda Terima Barang Klaim</div></td></tr>
<tr><td><div class="subtitle">'.$rs[0]['code'].'</div></td></tr>
</table> 

<div style="clear:both"></div>
 
<table>
<tr>
<td style="width:300px;" >
<table cellpadding="2">
<tr><td class="header-row-header" >Tanggal</td><td style="width:10px; text-align:center">:</td><td style="width:200px">'.$obj->formatDBDate($rs[0]['trdate'],'d / m / Y').'</td></tr>
<tr><td class="header-row-header">Pelanggan</td><td style="text-align:center">:</td><td >'. $rs[0]['customername'] .'</td></tr>
<tr><td class="header-row-header">Telepon</td><td style="text-align:center">:</td><td >'. $rs[0]['customerphone'] .'</td></tr> 
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
for($i=0;$i<count($rsDetail);$i++){
    $html .= '<div style="border-top:1px solid #dedede"></div>';
    $html .= '<table style="width:680px;" cellpadding="2">';
    $rsClaim = $obj->getClaimResult(' and '.$obj->tableClaimResult .'.pkey = '.$obj->oDbCon->paramString($rsDetail[$i]['claimresultkey']).'');
    $html .= '<tr>
                <td class="header-row-header">SN / PN</td>
                <td style="width:10px; text-align:center">:</td>
                <td style="width:228px">'.$rsDetail[$i]['serialnumber'].' / '.$rsDetail[$i]['partnumber'].'</td>
                <td style="width:10px"></td>
                <td class="header-row-header">Tgl. Akhir Garansi</td>
                <td style="width:10px; text-align:center">:</td>
                <td >'.$obj->formatDBDate($rsDetail[$i]['warrantyperiodexpireddate'],'d / m / Y').'</td>
            </tr>';
   
    $html .= '</table>';
    
    $rsItemContent = $obj->getItemContentDetail($rsDetail[$i]['pkey'],' and '.$obj->tableContentOfPackage .'.ischeck = 1 '); 
    
    $tabelItem = ''; 
    $tabelItem ='<div><strong>Kelengkapan Produk</strong></div><table cellpadding="2">';
    if (empty($rsItemContent))
        $tabelItem .= '<tr><td style="text-align:left; width:100px">Tidak Ada</td></tr>'; 
    
    for($j=0;$j<count($rsItemContent);$j++){
        if ($rsItemContent[$j]['qty'] <= 0) continue; 
        $tabelItem .= '<tr><td style="text-align:right; width:25px">'.$obj->formatNumber($rsItemContent[$j]['qty']).' x</td><td>'.$rsItemContent[$j]['itemname'].'</td></tr>';
    } 
    $tabelItem .= '</table>';
  
    
    $rsIssue = $obj->getIssueDetail($rsDetail[$i]['pkey']); 
    $arrIssueKey = array_column($rsIssue,'issuekey');
    $arrIssue = array();
    for($j=0;$j<count($rsIssue);$j++){
        array_push($arrIssue, $rsIssue[$j]['issue']); 
    }
    
    $tableIssue = '<table cellpadding="2">
                     <tr><td><strong>Masalah</strong><br>'.implode(', ', $arrIssue).'.</td></tr>
                     </table>';
    
    $html .='<br><table> 
             <tr>
                <td style="width:370px">'.$tableIssue.'</td><td style="width:310px">'.$tabelItem.'</td>
             </tr>
             </table>
             '; 
    
$html .='<br>';
}
    
$html .= '<div style="border-top:1px solid #dedede"></div>'; 
    
if (!empty($rs[0]['trdesc']))    
    $html .= '<div><strong>'.$obj->lang['note'].'</strong><br>'.str_replace(chr(13),'<br>',$rs[0]['trdesc']).'</div>'; 
    
$html .= '<div style="clear:both"></div>'; 
     
$html .=  $obj->loadSetting('warrantyClaimAgreement');
$html .= '<div style="clear:both"></div>'; 

        
$rsEmployee = $employee->getDataRowById($obj->userkey);
$rsEmployeeCreated = $employee->getDataRowById($rs[0]['createdby']);
$arrSignLabel = array(); 
array_push($arrSignLabel, array('Diterima',$rsEmployee[0]['name']) ); 
array_push($arrSignLabel, array('Mengetahui','') ); 
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