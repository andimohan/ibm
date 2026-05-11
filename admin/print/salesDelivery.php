<?php 
include '../../_config.php';  
include '../../_include.php'; 
 
$obj = $salesDelivery;
$securityObject  = $obj->securityObject;   

include '_global.php'; 

// TABLE WIDTH = 670px

$arrID = (isset( $_GET['id']) && !empty( $_GET['id'])) ? explode(',',$_GET['id']) : array();

$title = array();
for($i=0;$i<count($arrID);$i++){
    $id = $arrID[$i];
    
    $pdf->startPageGroup();  
    $pdf->AddPage();

    $rs = $obj->getDataRowById($id);
    $pdf->rs = $rs;
    $obj->validateAllowedStatus($rs);
    
    $rsDetail = $obj->getDetailWithRelatedInformation($id); 
      
    $dataset = array();
    $dataset['rs'] = $rs;
    $dataset['rsDetail'] = $rsDetail; 
    
    $html = generatePrintTemplate($dataset); 
    $pdf->writeHTML($html);   
  
    array_push($title,$rs[0]['code'] );
}


$title = implode(', ', $title);

$pdf->SetTitle($title); 
$pdf->Output( substr($title,0,$obj->printSetting['fileNameLength']) .'.pdf', 'I'); 


function generatePrintTemplate($dataset){

$obj = new SalesDelivery();  
$salesOrder = new SalesOrder();
    
$rs = $dataset['rs'];
$rsDetail = $dataset['rsDetail'];  
    
$rsSO = $salesOrder->getDataRowById($rs[0]['refkey']);
    
$arrRecipient = array();
array_push($arrRecipient, $rsSO[0]['recipientname'], $rsSO[0]['recipientaddress'], $rsSO[0]['recipientphone']);
    
$html = $obj->printSetting['defaultStyle'];
$html .= ' 
<table cellpadding="2" > 
<tr><td><div class="title">SURAT JALAN</div></td></tr>
<tr><td><div class="subtitle">'.$rs[0]['code'].'</div></td></tr>
</table> 

<div style="clear:both"></div>
<table>
<tr>
<td>
<table cellpadding="2"> 
<tr><td class="header-row-header">Tanggal</td><td style="width:10px; text-align:center">:</td><td>'.$obj->formatDBDate($rs[0]['trdate'],'d / m / Y').'</td></tr>  
<tr><td class="header-row-header">Kode SO</td><td style="width:10px; text-align:center">:</td><td>'.$rsSO[0]['code'].'</td></tr>  
<tr><td colspan="3" class="header-row-header"></td></tr> 
<tr><td colspan="3" class="header-row-header">Kepada Yth.</td></tr> 
<tr><td colspan="3">'.implode('<br>',$arrRecipient).'</td></tr>  
</table> 
</td>
<td></td>
</tr>
  
<div style="clear:both"></div> ';

$html .= ' 
<table  cellpadding="4" class="table-transaction">
<tr class="col-header" ><td style="width:510px;">Item</td><td style="width:80px;text-align:right" >Jumlah</td><td style="width:80px;" >Unit</td></tr>';

for ($i=0;$i<count($rsDetail);$i++){    
  $html .= '<tr><td>'.$rsDetail[$i]['itemname'].'</td><td style="text-align:right">'.$obj->formatNumber($rsDetail[$i]['deliveredqty']).'</td><td>'. $rsDetail[$i]['unitname'] .'</td></tr>' ; 
}
$html .= '</table>' ;
   
$html .= '<table cellpadding="4">
<tr><td><strong>Catatan</strong> :<br>'.str_replace(chr(13),'<br>',$rs[0]['trnotes']).'</td></tr>
</table>
<div "clear:both"></div>';

$html .= $obj->generateSignLabel($rs); 
    
return $html;
}
