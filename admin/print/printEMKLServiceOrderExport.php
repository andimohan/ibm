<?php 
include '../../_config.php';  
include '../../_include.php'; 
 
$obj = $emklServiceOrderExport;
$securityObject  = $obj->securityObject;   

$SHOW_PRINT_HEADER = false; 

include '_global.php'; 

// TABLE WIDTH = 670px

$arrID = (isset( $_GET['id']) && !empty( $_GET['id'])) ? explode(',',$_GET['id']) : array();

$title = array();
for($i=0;$i<count($arrID);$i++){
    $id = $arrID[$i];
     
    $pdf->startPageGroup();  
    $pdf->AddPage(); 
    
    $rs = $obj->searchData($obj->tableName.'.pkey',$id); 
    $pdf->rs = $rs;
    $obj->validateAllowedStatus($rs);
    
    $dataset = array();
    $dataset['rs'] = $rs; 
    
    $html = generatePrintTemplate($dataset); 
    $pdf->writeHTML($html);   
 
    array_push($title,$rs[0]['code'] );
}


$title = implode(', ', $title);

$pdf->SetTitle($title); 
$pdf->Output( substr($title,0,$obj->printSetting['fileNameLength']) .'.pdf', 'I'); 



function generatePrintTemplate($dataset){
global $pdf;
 
$obj = new emklServiceOrder();    
$emklShippingCompany = new emklShippingCompany();
$customer = new Customer();
$item = new Item();
$itemChecklist = new ItemChecklist();
$itemChecklistGroup = new ItemChecklistGroup();
$vessel = new Vessel();
$terminal = new Terminal();
    
$rs = $dataset['rs'];  
$rsCust = $customer->getDataRowById($rs[0]['customerkey']);  
$arrCustPhone = array();
$rsVessel = $vessel->getDataRowById($rs[0]['vesselkey']);
    
if (!empty($rsCust[0]['phone']))
    array_push($arrCustPhone,$rsCust[0]['phone']);
    
if (!empty($rsCust[0]['mobile']))
    array_push($arrCustPhone,$rsCust[0]['mobile']);

$shipmentName = '';
if (!empty($rs[0]['shippingcompanykey'])){
    $rsShipping = $emklShippingCompany->getDataRowById($rs[0]['shippingcompanykey']);
    $shipmentName = $rsShipping[0]['name'];
}

$transhipment = '';
if (!empty($rs[0]['transhipmentkey'])){
    $rsTranshipment = $terminal->getDataRowById($rs[0]['transhipmentkey']);
    $transhipment = $rsTranshipment[0]['name'];
}

$html = $obj->printSetting['defaultStyle'];
$html .= ' 
<table cellpadding="2" > 
<tr><td><div class="title">JOBSHEET EXPORT</div></td></tr>
<tr><td><div class="subtitle">'.$rs[0]['code'].'</div></td></tr>
</table> 
<div style="clear:both"></div> 
<table cellpadding="2">
<tr><td class="header-row-header" style="width:120px;">Tgl.</td><td style="width:10px; text-align:center">:</td><td style="width:550px" colspan="4">'.$obj->formatDBDate($rs[0]['trdate'],'d / m / Y').'</td></tr>
<tr><td class="header-row-header">Nama Consignee</td><td style="text-align:center">:</td><td colspan="4">'.$rs[0]['consigneename'].'</td></tr>
<tr><td class="header-row-header">Shipper</td><td style="text-align:center">:</td><td colspan="4">' .$rs[0]['customername'].'</td></tr>
<tr><td class="header-row-header">Telepon</td><td style="text-align:center">:</td><td colspan="4">'.implode(', ', $arrCustPhone).'</td></tr>
<tr><td class="header-row-header">Booking Pelayaran</td><td style="text-align:center">:</td><td style="width:205px">'.$shipmentName.'</td><td class="header-row-header" style="width:120px">Depot</td><td style="width:10px; text-align:center">:</td><td style="width:205px">' .$rs[0]['depotname'].'</td></tr>
<tr><td class="header-row-header">Tujuan Barang</td><td style="text-align:center">:</td><td colspan="4">' .$rs[0]['destination'].'</td></tr>
<tr><td class="header-row-header">Vessel</td><td style="text-align:center">:</td><td colspan="4">' .$rsVessel[0]['name'].'-' .$rs[0]['vesselnumber'].'</td></tr>
<tr><td class="header-row-header">Via</td><td style="text-align:center">:</td><td>' .$rs[0]['via'].'</td><td class="header-row-header">Transhipment</td><td style="text-align:center">:</td><td>'.$transhipment.'</td></tr>
<tr><td class="header-row-header">Stack</td><td style="text-align:center">:</td><td>' .$rs[0]['terminalname'].'</td><td class="header-row-header">Booking #</td><td style="text-align:center">:</td><td>' .$rs[0]['shipmentnumber'].'</td></tr>
<tr><td class="header-row-header">Tgl. Stuffing</td><td style="text-align:center">:</td><td>'.$obj->formatDBDate($rs[0]['stuffingdate'],'d / m / Y').'</td><td class="header-row-header">PEB #</td><td style="text-align:center">:</td><td>' .$rs[0]['peb'].' </td></tr>
<tr><td class="header-row-header">ETD</td><td style="text-align:center">:</td><td colspan="4">'.$obj->formatDBDate($rs[0]['etddate'],'d / m / Y').'</td></tr>
<tr><td class="header-row-header">Opening</td><td style="text-align:center">:</td><td>'.$obj->formatDBDate($rs[0]['opendate'],'d / m / Y').'</td><td class="header-row-header">Jam</td><td style="text-align:center">:</td><td>'.$obj->formatDBDate($rs[0]['opendate'],'H:i').'</td></tr>
<tr><td class="header-row-header">Closing</td><td style="text-align:center">:</td><td>'.$obj->formatDBDate($rs[0]['closingdate'],'d / m / Y').'</td><td class="header-row-header">Jam</td><td style="text-align:center">:</td><td>'.$obj->formatDBDate($rs[0]['closingdate'],'H:i').'</td></tr>
<tr><td class="header-row-header">Party</td><td style="text-align:center">:</td><td colspan="4">'.str_replace(chr(13),'<br>',$rs[0]['partynote']).'</td></tr>
<tr><td class="header-row-header">Keterangan</td><td style="text-align:center">:</td><td colspan="4">' .$rs[0]['trdesc'].'</td></tr>
<div style="clear:both"></div>
</table> 
<div style="clear:both"></div>
Diisi Oleh Pengurus :
<div style="clear:both"></div>
<table cellpadding="4">
<tr class="col-header"><td style="text-align:right; width:40px">No. </td><td style="width:80px;">Date</td><td style="width:150px;">Container</td><td style="width:150px">Cont #</td><td style="width:100px">Seal #</td><td style="width:150px">Driver</td></tr>
';

$rsDetail = $obj->getDetailById($rs[0]['pkey']);
$ctr = 0;
for($i=0;$i<count($rsDetail);$i++){
$rsItem = $item->getDataRowById($rsDetail[$i]['itemkey']);   
    
        for($j=0;$j<$rsDetail[$i]['qtyinbaseunit'];$j++){
             
            $borderStyle = 'col-border-bottom';
             
            $html .= '
            <tr> 
            <td class="'.$borderStyle.'" style="text-align:right;">'.(++$ctr).'.</td>
            <td class="'.$borderStyle.'"></td> 
            <td class="'.$borderStyle.'">'.$rsItem[0]['name'].'</td>
            <td class="'.$borderStyle.'"></td> 
            <td class="'.$borderStyle.'"></td> 
            <td class="'.$borderStyle.'"></td>  
            </tr>';  
        }
   
}

$html .= '</table>
<div style="clear:both;"></div>
<div style="clear:both;"></div>
';
    


      
$borderbox = '<table style="border:1px solid #333; width:15px; height:15px;"><tr><td></td></tr></table>';
 
$rsTruckingChecking = $itemChecklistGroup->getDetailWithRelatedInformation(2);
$truckingChecking = '<table cellpadding="2" style="font-size:0.8em">';        
for($i=0;$i<count($rsTruckingChecking);$i++){
    
    $rsItemChecklist = $itemChecklist->getDataRowById($rsTruckingChecking[$i]['itemkey']);
    
    if ($i % 2 == 0)
        $truckingChecking .= '<tr>';       
    
    $truckingChecking .= '<td><table><tr><td style="width:15px;">'.$borderbox.'</td><td style="width:5px;"></td><td style="width:70px">'.$rsItemChecklist[0]['name'].'</td><td>Tgl. :</td></tr></table></td>';
    
    if ( $i == count($rsTruckingChecking) -1 && $i % 2 == 0)
        $truckingChecking .= '<td></td></tr>';       
    elseif ( $i <> 0 && ($i+1) % 2 ==0)
        $truckingChecking .= '</tr>';           
}
$truckingChecking .= '</table>'; 
 
$rsDocument = $itemChecklistGroup->getDetailWithRelatedInformation(3);
$documentChecking = '<table cellpadding="2"  style="font-size:0.8em">';        
for($i=0;$i<count($rsDocument);$i++){
    
    $rsChecklist = $itemChecklist->getDataRowById($rsDocument[$i]['itemkey']);
    
    if ($i % 2 == 0)
        $documentChecking .= '<tr>';       
        
    $documentChecking .= '<td><table><tr><td style="width:15px;">'.$borderbox.'</td><td style="width:5px;"></td><td style="width:315px">'.$rsChecklist[0]['name'].'</td></tr></table></td>';
   
    if ( $i == count($rsDocument) -1 && $i % 2 == 0)
        $documentChecking .= '<td></td></tr>';       
    elseif ( $i <> 0 && ($i+1) % 2 ==0)
        $documentChecking .= '</tr>';           
}
$documentChecking .= '</table>'; 
    
    
$html .= '<table cellpadding="10" ><tr><td style="width:335px; vertical-align:top; border:1px solid #666;"><div style="font-weight:bold">TRUCKING CHECKING<br></div>'.$truckingChecking.'</td><td style="width:335px; vertical-align:top;  border:1px solid #666;"><div style="font-weight:bold">DOCUMENT CHECKING<br></div>'.$documentChecking.'</td></tr></table>'; 
 
return $html;
}
