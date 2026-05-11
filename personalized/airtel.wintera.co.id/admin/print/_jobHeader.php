<?php 

$pdf->setCustomSettings(
    array( 
         'showPrintHeader' => false,
         'footer' => '',  
         ) 
);

$generateReportContent = function ($dataset){ 
    global $pdf;

$obj = new EMKLJobOrder(EMKL['jobType']['export']);  
$emklPurchaseOrderExport = new EMKLPurchaseOrder(EMKL['jobType']['export']);  
    
$service = new Service(SERVICE);
$employee = new Employee();
$location = new Location(); 
$emklCommission = new EMKLCommission();
$supplier = new Supplier();
    
$rsStuffing = $location->getDataRowById($rs[0]['locationkey']);
$rsDetail = $obj->getDetailWithRelatedInformation($rs[0]['pkey']);

$arrCust = array();
for($i=0;$i<count($rsDetail);$i++){
    array_push($arrCust,$rsDetail[$i]['customername']);
}
    
$customerName = implode(' ,',$arrCust);
    
$rs = $dataset['rs'];  
    
    
$containerNumber = $rs[0]['containernumber'];
    
$arrContainer = explode(chr(13),$containerNumber);
    
$html = $obj->printSetting['defaultStyle'];
$html .= '
<table cellpadding="4"><tr><td style="width:150px;">BOOKING</td><td style="width:12px;">:</td><td style="width:268px;font-weight:bold;">'.$rs[0]['bookingnumber'].'</td><td style="width:300px;">'.$vendorName.'</td></tr>
</table>';

$html .= '<table cellpadding="4" style="border:solid 1px black"> 
<tr><td style="border-bottom:solid 1px #e0e0;width:150px;">REF NO</td><td style="border-bottom:solid 1px #e0e0;width:12px;">:</td><td style="border-bottom:solid 1px #e0e0;width:268px;font-weight:bold;">'.$rs[0]['code'].'</td><td style="border-bottom:solid 1px #e0e0;width:120px;">ETD</td><td style="border-bottom:solid 1px #e0e0;width:12px;">:</td><td style="border-bottom:solid 1px #e0e0;width:120px;">'.$obj->formatDBDate($rs[0]['etdpol'],'d-M-y').'</td></tr>
<tr><td style="border-bottom:solid 1px #e0e0;">SHIPPER</td><td style="border-bottom:solid 1px #e0e0;">:</td><td style="border-bottom:solid 1px #e0e0;">'.$customerName.'</td><td style="border-bottom:solid 1px #e0e0;">ETA</td><td style="border-bottom:solid 1px #e0e0;width:12px;">:</td><td style="border-bottom:solid 1px #e0e0;">'.$obj->formatDBDate($rs[0]['etapod'],'d-M-y').'</td></tr>
<tr><td style="border-bottom:solid 1px #e0e0;">POL/POD</td><td style="border-bottom:solid 1px #e0e0;">:</td><td style="border-bottom:solid 1px #e0e0;font-weight:bold;">'.$rs[0]['polname'].' - '.$rs[0]['podname'].'</td>';


$tableContainer = '<table cellpadding="2">';    

for($j=0;$j<count($arrContainer);$j++){
    $arrCont = explode('/',trim($arrContainer[$j])); 
    $tableContainer .= '<tr><td style="text-align:center;width:120px;">'.$arrCont[0].'</td><td style="text-align:center;width:10px;">/</td><td style="text-align:center;width:115px;">'.$arrCont[1].'</td></tr>';  
}    
$tableContainer .= '</table>';
 
$html .= '<td rowspan="4" colspan="3" style="border:solid 1px black;">'.$tableContainer.'</td>';
 

$html .= '</tr>
<tr><td style="border-bottom:solid 1px #e0e0;">CARRIER</td><td style="border-bottom:solid 1px #e0e0;">:</td><td style="border-bottom:solid 1px #e0e0;font-weight:bold;">'.$rs[0]['carriername'].'</td></tr>
<tr><td style="border-bottom:solid 1px #e0e0;">VOLUME</td><td style="border-bottom:solid 1px #e0e0;">:</td><td style="border-bottom:solid 1px #e0e0;"></td></tr>
<tr><td style="border-bottom:solid 1px #e0e0;">FEEDER</td><td style="border-bottom:solid 1px #e0e0;">:</td><td style="border-bottom:solid 1px #e0e0;">'.$rs[0]['feeder'].'</td></tr>
<tr><td style="border-bottom:solid 1px #e0e0;">STACK AREA</td><td style="border-bottom:solid 1px #e0e0;">:</td><td style="border-bottom:solid 1px #e0e0;">'.$rs[0]['stackarea'].'</td><td style="border-bottom:solid 1px #e0e0;">AJU</td><td style="border-bottom:solid 1px #e0e0;">:</td><td style="border-bottom:solid 1px #e0e0;">'.$rs[0]['aju'].'</td></tr>
<tr><td style="border-bottom:solid 1px #e0e0;">CLOSING</td><td style="border-bottom:solid 1px #e0e0;">:</td><td style="border-bottom:solid 1px #e0e0;">'.$obj->formatDBDate($rs[0]['closingdate'],'d M y H:i').'</td><td style="border-bottom:solid 1px #e0e0;">PEB</td><td style="border-bottom:solid 1px #e0e0;">:</td><td style="border-bottom:solid 1px #e0e0;">'.$rs[0]['peb'].'</td></tr>
<tr><td style="border-bottom:solid 1px #e0e0;">INVOICE NO</td><td style="border-bottom:solid 1px #e0e0;">:</td><td  style="border-bottom:solid 1px #e0e0;"></td><td style="border-bottom:solid 1px #e0e0;">DATE </td><td style="border-bottom:solid 1px #e0e0;">:</td><td style="border-bottom:solid 1px #e0e0;">'.$obj->formatDBDate($rs[0]['trdate'],'d / m / Y').'</td></tr>
<tr><td style="border-bottom:solid 1px #e0e0;">STUFFING</td><td style="border-bottom:solid 1px #e0e0;">:</td><td style="border-bottom:solid 1px #e0e0;">'.$rs[0]['stuffinglocation'].'</td><td style="border-bottom:solid 1px #e0e0;">IN</td><td style="border-bottom:solid 1px #e0e0;">:</td><td style="border-bottom:solid 1px #e0e0;">'.$obj->formatDBDate($rs[0]['stuffingin'],'d / m / Y').'</td></tr>
<tr><td style="border-bottom:solid 1px #e0e0;">Temp</td><td style="border-bottom:solid 1px #e0e0;">:</td><td style="border-bottom:solid 1px #e0e0;">'.$obj->formatNumber($rs[0]['temperature'],0).'</td><td style="border-bottom:solid 1px #e0e0;">OUT</td><td style="border-bottom:solid 1px #e0e0;">:</td><td style="border-bottom:solid 1px #e0e0;">'.$obj->formatDBDate($rs[0]['stuffingout'],'d / m / Y').'</td></tr>
</table> 
';

$html .= '<div  style="clear:both"></div><table cellpadding="4">
<tr>
<td style="border:solid 1px black;width:90px;text-align:center;">INV</td><td style="width:5px"></td>
<td style="border:solid 1px black;width:90px;text-align:center;">LOI</td><td style="width:5px"></td>
<td style="border:solid 1px black;width:90px;text-align:center;">PEB</td><td style="width:5px"></td>
<td style="border:solid 1px black;width:90px;text-align:center;">NPE</td><td style="width:5px"></td>
<td style="border:solid 1px black;width:160px;text-align:center;">Custom Reefer / Custom Dry</td><td style="width:5px"></td>
<td style="border:solid 1px black;width:120px;text-align:center;">Submit Final</td><td style="width:5px"></td>
</tr>    
    
</table>';
$html .= '<div  style="clear:both"></div><table cellpadding="4">
<tr>
<td style="border:solid 1px black;width:90px;text-align:center;">Submit VGM</td><td style="width:5px"></td>
<td style="border:solid 1px black;width:90px;text-align:center;">ORI, Sent</td><td style="width:5px"></td>
<td style="border:solid 1px black;width:90px;text-align:center;">Notul, Sent</td><td style="width:5px"></td>
</tr>    
    
</table>';
//$html .= $obj->generateSignLabel($rs); 
return $html;
}

?>
