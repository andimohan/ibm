<?php 
include '../../_config.php';  
include '../../_include.php'; 
 
$obj = $emklServiceOrderExport;
$securityObject  = $obj->securityObject;  

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
$itemChecklist = new ItemChecklist();
    
$rs = $dataset['rs'];  
$rsCust = $customer->getDataRowById($rs[0]['customerkey']);    
$arrCustPhone = array();
    
if (!empty($rsCust[0]['phone']))
    array_push($arrCustPhone,$rsCust[0]['phone']);
    
if (!empty($rsCust[0]['mobile']))
    array_push($arrCustPhone,$rsCust[0]['mobile']);

$shipmentName = '';
if (!empty($rs[0]['shippingcompanykey'])){
    $rsShipping = $emklShippingCompany->getDataRowById($rs[0]['shippingcompanykey']);
    $shipmentName = $rsShipping[0]['name'];
}

$html = $obj->printSetting['defaultStyle'];
$html .= ' 
<table cellpadding="2" > 
<tr><td><div class="title">JOBSHEET DOMESTIK</div></td></tr>
<tr><td><div class="subtitle">'.$rs[0]['code'].'</div></td></tr>
</table> 
<div style="clear:both"></div> 
<table cellpadding="2">
<tr><td class="header-row-header" style="width:120px;">Tgl.</td><td style="width:10px; text-align:center">:</td><td style="width:550px" colspan="4">'.$obj->formatDBDate($rs[0]['trdate'],'d / m / Y').'</td></tr>
<tr><td class="header-row-header">Nama Consignee</td><td style="width:10px; text-align:center">:</td><td colspan="4">'.$rs[0]['consigneename'].'</td></tr>
<tr><td class="header-row-header">Shipper</td><td style="text-align:center">:</td><td colspan="4">' .$rs[0]['customername'].'</td></tr>
<tr><td class="header-row-header">Telepon</td><td style="text-align:center">:</td><td colspan="4">'.implode(', ', $arrCustPhone).'</td></tr>
<tr><td class="header-row-header">Booking Pelayaran</td><td style="text-align:center">:</td><td style="width:205px">'.$shipmentName.'</td><td class="header-row-header" style="width:120px">Depo</td><td style="width:10px; text-align:center">:</td><td style="width:205px">' .$rs[0]['depotname'].'</td></tr>
<tr><td class="header-row-header">Tujuan Barang</td><td style="text-align:center">:</td><td colspan="4">' .$rs[0]['destination'].'</td></tr>
<tr><td class="header-row-header">Vessel</td><td style="text-align:center">:</td><td colspan="4">' .$rs[0]['vessel'].'</td></tr>
<tr><td class="header-row-header">Commodity</td><td style="text-align:center">:</td><td colspan="4">' .$rs[0]['commodity'].'</td></tr>
<tr><td class="header-row-header">Stack</td><td style="text-align:center">:</td><td>' .$rs[0]['terminalname'].'</td><td class="header-row-header">Booking #</td><td style="width:10px; text-align:center">:</td><td>' .$rs[0]['shipmentnumber'].'</td></tr>
<tr><td class="header-row-header">Tgl. Stuffing</td><td style="text-align:center">:</td><td colspan="4">'.$obj->formatDBDate($rs[0]['stuffingdate'],'d / m / Y').'</td></tr>
<tr><td class="header-row-header">ETD</td><td style="text-align:center">:</td><td colspan="4">'.$obj->formatDBDate($rs[0]['etddate'],'d / m / Y').'</td></tr>
<tr><td class="header-row-header">Opening</td><td style="text-align:center">:</td><td>'.$obj->formatDBDate($rs[0]['opendate'],'d / m / Y').'</td><td class="header-row-header">Jam</td><td style="width:10px; text-align:center">:</td><td>'.$obj->formatDBDate($rs[0]['opendate'],'H:i').'</td></tr>
<tr><td class="header-row-header">Closing</td><td style="text-align:center">:</td><td>'.$obj->formatDBDate($rs[0]['closingdate'],'d / m / Y').'</td><td class="header-row-header">Jam</td><td style="width:10px; text-align:center">:</td><td>'.$obj->formatDBDate($rs[0]['closingdate'],'H:i').'</td></tr>
<tr><td class="header-row-header">Party</td><td style="text-align:center">:</td><td colspan="4">'.str_replace(chr(13),'<br>',$rs[0]['partynote']).'</td></tr>
<tr><td class="header-row-header">Keterangan</td><td style="text-align:center">:</td><td colspan="4">' .$rs[0]['trdesc'].'</td></tr>
<div style="clear:both"></div>
</table> 
<div style="clear:both"></div>
Diisi Oleh Pengurus :
<div style="clear:both"></div>
<table >
<tr class="col-header"><td style="text-align:right;width:30px">No. </td><td style="text-align:left;width:100px">  Cont#</td><td>Seal#</td><td>Truck#</td><td>Driver</td><td>Chasis#</td><td>Full By</td><td>Date</td><td>Keterangan</td></tr>
</table>
<div style="clear:both;"></div>
<div style="clear:both;"></div>
';
      
    
$borderbox = '<table style="border:1px solid #333; width:15px; height:15px;"><tr><td></td></tr></table>';
    
$rsChecklist = $itemChecklist->searchData($itemChecklist->tableName.'.statuskey',1,true,' and '.$itemChecklist->tableName.'.categorykey= 3',' order by name asc');    
$truckingChecking = '<table cellpadding="2" style="font-size:0.8em">';        
for($i=0;$i<count($rsChecklist);$i++){
    if ($i % 2 == 0)
        $truckingChecking .= '<tr>';       
    
    $truckingChecking .= '<td><table><tr><td style="width:15px;">'.$borderbox.'</td><td style="width:5px;"></td><td style="width:70px">'.$rsChecklist[$i]['name'].'</td><td>Tgl. :</td></tr></table></td>';
    
    if ( $i == count($rsChecklist) -1 && $i % 2 == 0)
        $truckingChecking .= '<td></td></tr>';       
    elseif ( $i <> 0 && ($i+1) % 2 ==0)
        $truckingChecking .= '</tr>';           
}
$truckingChecking .= '</table>'; 
    
$rsChecklist = $itemChecklist->searchData($itemChecklist->tableName.'.statuskey',1,true,' and '.$itemChecklist->tableName.'.categorykey= 4',' order by name asc');    
$documentChecking = '<table cellpadding="2"  style="font-size:0.8em">';        
for($i=0;$i<count($rsChecklist);$i++){
    if ($i % 2 == 0)
        $documentChecking .= '<tr>';       
        
    $documentChecking .= '<td><table><tr><td style="width:15px;">'.$borderbox.'</td><td style="width:5px;"></td><td style="width:315px">'.$rsChecklist[$i]['name'].'</td></tr></table></td>';
   
    if ( $i == count($rsChecklist) -1 && $i % 2 == 0)
        $documentChecking .= '<td></td></tr>';       
    elseif ( $i <> 0 && ($i+1) % 2 ==0)
        $documentChecking .= '</tr>';           
}
$documentChecking .= '</table>'; 
    
    
$html .= '<table cellpadding="10" ><tr><td style="width:335px; vertical-align:top; border:1px solid #666;"><div style="font-weight:bold">TRUCKING CHECKING<br></div>'.$truckingChecking.'</td><td style="width:335px; vertical-align:top;  border:1px solid #666;"><div style="font-weight:bold">DOCUMENT CHECKING<br></div>'.$documentChecking.'</td></tr></table>'; 
$html .= '<div style="clear:both;"></div>';
$html .= '<div style="clear:both;"></div>';
$html .= $obj->generateSignLabel($rs); 
return $html;
}
