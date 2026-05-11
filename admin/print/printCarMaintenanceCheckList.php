<?php 
include '../../_config.php';  
include '../../_include.php'; 
 
$obj = $carMaintenanceChecklist;
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

    $html = ($customPrint['status']) ? generatePrintCustom($dataset) :  generatePrintTemplate($dataset); 
    
    $pdf->writeHTML($html);   
    
    array_push($title,$rs[0]['code'] );
}


$title = implode(', ', $title);

$pdf->SetTitle($title); 
$pdf->Output( substr($title,0,$obj->printSetting['fileNameLength']) .'.pdf', 'I'); 



function generatePrintTemplate($dataset){
global $pdf;

$obj = new CarMaintenanceChecklist();  
$itemChecklistGroup = new ItemChecklistGroup();
$itemChecklist = new ItemChecklist();
$customer = new Customer();
$car = new Car();
$carSeries = new CarSeries();
$item = new Item();
$itemPackage = new ItemPackage();
$brand = new Brand();
$oilType = new OilType();

  
$rs = $dataset['rs']; 
$rsDetail = $obj->getDetailById($rs[0]['pkey']);
$rsCar = $rsCar = $car->searchData($car->tableName.'.pkey',$rs[0]['carkey'], true); 
if (!empty($rsCar)) {
 $policeNumber = $rsCar[0]['policenumber'];  
 $year = $rsCar[0]['year']; 
 $capacity = $rsCar[0]['capacity']; 
 $carSeries = $rsCar[0]['seriesname'];
 $fuelType = $rsCar[0]['fueltype'];
}
    
$rsCustomer = $customer->getDataRowById($rs[0]['customerkey']);
if (!empty($rsCustomer)) { 
 $arrPhone = array(); 
    
 if (!empty($rsCustomer[0]['phone']))
    array_push($arrPhone,$rsCustomer[0]['phone']);
 if (!empty($rsCustomer[0]['mobile']))
    array_push($arrPhone,$rsCustomer[0]['mobile']);
    
 $mobilePhone = implode(', ',$arrPhone);
 $email = $rsCustomer[0]['email']; 
}
    
$rsAC = $itemPackage->searchItemByGroupCategory('acPackage') ;  
$arrAC = array_column($rsAC,'name','pkey');    
    
$rsTuneUp = $itemPackage->searchItemByGroupCategory('tuneupPackage');   
$arrTuneUp = array_column($rsTuneUp,'name','pkey'); 
    
$rsTuneUp = $itemPackage->searchItemByGroupCategory('bbmPackage');   
$arrBBMPackage = array_column($rsTuneUp,'name','pkey'); 
     
    
$rsOilType = $oilType->searchData('','',true, ' and ('.$oilType->tableName.'.statuskey)');
$arrOilType = array_column($rsOilType,'name','pkey');    
    
    
$oilFilter = '';
if(!empty($rs[0]['oilfilter'])) 
    $oilFilter = ($rs[0]['oilfilter']==1) ? 'Ganti' : 'Dibersihkan'; 
    
$airFilter = '';
if(!empty($rs[0]['airfilter'])) 
    $airFilter = ($rs[0]['airfilter']==1) ? 'Ganti' : 'Dibersihkan'; 

$rsOilBrand = $brand->getDataRowById($rs[0]['oilbrandkey']);
$oilBrand = (!empty($rsOilBrand)) ? $rsOilBrand[0]['name'] : '';

    /*
$rsUltimate = $obj->getUltimateData($rs[0]['ultimatepackagekey']);
if(!empty($rsUltimate))
    $ultimate = $rsUltimate[0]['name'];
*/

$trNotes = (!empty($rs[0]['trdesc'])) ? '<strong>Keluhan Customer<br></strong>'. str_replace(chr(13),'<br>',$rs[0]['trdesc']) .'<br><br>': '';
$trWork = (!empty($rs[0]['trworkdesc'])) ? '<strong>Keterangan Pengerjaan<br></strong>'. str_replace(chr(13),'<br>',$rs[0]['trworkdesc']) .'<br><br>': '';
$trPart = (!empty($rs[0]['trpartchangedesc'])) ? '<strong>Pergantian Part<br></strong>'. str_replace(chr(13),'<br>',$rs[0]['trpartchangedesc']) .'<br><br>': '';
$trSuggestion = (!empty($rs[0]['trsuggestiondesc'])) ? '<strong>Saran Dan Solusi Dari Teknisi<br></strong>'. str_replace(chr(13),'<br>',$rs[0]['trsuggestiondesc']) .'<br><br>': '';

$arrGroup = array(1,2);
$checklistTable = '';    
for($ctr=0;$ctr<count($arrGroup);$ctr++){
    $rsGroup = $itemChecklistGroup->getDataRowById($arrGroup[$ctr]);
    $groupkey = $rsGroup[0]['pkey'];

    $checklistTable .= '
    <td style="width:50%">
    <table cellpadding="4" class="table-transaction" style="border-bottom:0px solid #fff;">
    <tr class="col-header"><td style="text-align:left;width:120px; padding:0">'.  strtoupper ($rsGroup[0]['name']) .'</td><td style="width:20px;">C</td><td style="width:20px;">R</td><td style="text-align:left;width:160px;padding:0">CATATAN</td></tr>
    ';

    $rsDetailValue =  $obj->getDetailValue($rs[0]['pkey'], $groupkey);
    $arrDetailValue = array_column($rsDetailValue, 'description', 'itemkey'); 
    $arrDetailCheck = array_column($rsDetailValue, 'ischeck', 'itemkey'); 
    $arrDetailReplace = array_column($rsDetailValue, 'isreplace', 'itemkey');
    
     
    $rsCheckDetail = $itemChecklistGroup->getDetailById($arrGroup[$ctr]);
    $detailDescription = array();
    for($i=0;$i<count($rsCheckDetail);$i++) {
        $itemkey = $rsCheckDetail[$i]['itemkey'];
        $rsItemlist = $itemChecklist->getDataRowById($itemkey);
         
        $isCheck =   (isset($arrDetailCheck[$itemkey]) && !empty($arrDetailCheck[$itemkey])) ? 'v' : '';  
        $isReplace =  (isset($arrDetailReplace[$itemkey]) && !empty($arrDetailReplace[$itemkey])) ? 'v' : '';  

        $detailValue = (isset($arrDetailValue[$rsCheckDetail[$i]['itemkey']]))  ? $arrDetailValue[$rsCheckDetail[$i]['itemkey']] : '';
        $checklistTable .= '
        <tr><td>'.$rsItemlist[0]['name'].'</td><td style="text-align:center">'.$isCheck.'</td><td style="text-align:center">'.$isReplace.'</td><td>'.$detailValue.'</td></tr> 
        ';
    }
        
    $checklistTable .= '
    </table>
    </td>';

}
     
    
$html = $obj->printSetting['defaultStyle'];
$html .= ' 
<table cellpadding="2" > 
<tr><td><div class="title">CHECKLIST KERJA</div></td></tr>
<tr><td><div class="subtitle">'.$rs[0]['code'].'</div></td></tr>
</table> 
<div style="clear:both"></div> 

<table style="font-size:0.9em">
<tr>
<td style="width:300px;" >
<table cellpadding="2">
<tr><td class="header-row-header" style="width:120px;">Jenis Mobil</td><td style="width:10px; text-align:center">:</td><td style="width:170px;">'.$carSeries.'</td></tr> 
<tr><td class="header-row-header">CC Mobil</td><td style="text-align:center">:</td><td >'. $capacity.'</td></tr>
<tr><td class="header-row-header">Plat No.</td><td style="text-align:center">:</td><td>'. $policeNumber .'</td></tr>
<tr><td class="header-row-header">Tahun Mobil</td><td style="text-align:center">:</td><td >'. $year.'</td></tr>
<tr><td class="header-row-header">KM</td><td style="text-align:center">:</td><td>'.$obj->formatNumber($rs[0]['mileage']).'</td></tr>
<tr><td class="header-row-header">Pengecekan Accu</td><td style="text-align:center">:</td><td>'.$rs[0]['accucheck'].'</td></tr>
</table>
</td>
<td style="width:370px;"> 
<table cellpadding="2">
<tr><td class="header-row-header" style="width:120px">Tanggal</td><td style="width:10px; text-align:center">:</td><td style="width:240px">'.$obj->formatDBDate($rs[0]['trdate'],'d / m / Y').'</td></tr>
<tr><td class="header-row-header">Pelanggan</td><td style="text-align:center">:</td><td style="width:240px">'. $rs[0]['customername'] .'</td></tr>
<tr><td class="header-row-header">Telepon</td><td style="text-align:center">:</td><td>'.$mobilePhone.'</td></tr>
<tr><td class="header-row-header">Email</td><td style="text-align:center">:</td><td >'.str_replace(chr(13),'<br>',$email).'</td></tr>
</table>
</td>
</tr>
</table>

<div style="clear:both"></div> 
'; 
    
$html .= '<table style="font-size:0.8em"><tr>'.$checklistTable.'</tr></table>';
        
$html .= '     
<div style="clear:both;"></div>
<div style="clear:both;"></div>
<table style="font-size:0.8em">
<tr>
<td  >
<table cellpadding="2">
<tr><td style="width:90px; font-weight:bold;">Paket AC</td><td style="width:10px; text-align:center">:</td><td>'.$arrAC[$rs[0]['ackey']].'</td></tr> 
<tr><td style="font-weight:bold;">Suhu AC (Before)</td><td style="text-align:center">:</td><td>'.$obj->formatNumber($rs[0]['actemperaturebefore'],2).'</td></tr>
<tr><td style="font-weight:bold;">Suhu AC (After)</td><td style="text-align:center">:</td><td>'.$obj->formatNumber($rs[0]['actemperatureafter'],2).'</td></tr>
<tr><td style="font-weight:bold;">Fogging</td><td style="text-align:center">:</td><td>'.$obj->formatNumber($rs[0]['fogging']).' Menit</td></tr>
<tr><td style="font-weight:bold;">Accu Life</td><td style="text-align:center">:</td><td>'.$obj->formatNumber($rs[0]['acculife'],2).' %</td></tr>
<tr><td style="font-weight:bold;">Accu AH</td><td style="text-align:center">:</td><td>'.$obj->formatNumber($rs[0]['accuah'],2).' Ah</td></tr>
<tr><td style="font-weight:bold;">Accu Resistance</td><td style="text-align:center">:</td><td >'.$rs[0]['accuresistance'].' Ah</td></tr>
</table>
</td>

<td> 
<table cellpadding="2">
<tr><td style="width:90px;font-weight:bold;">BBM</td><td style="width:10px; text-align:center">:</td><td style="width:100px">'.$fuelType.'</td></tr>
<tr><td style="font-weight:bold;">Oli Mesin Keluar</td><td style="width:10px; text-align:center">:</td><td>'.$obj->formatNumber($rs[0]['oilout'],2).'</td></tr>
<tr><td style="font-weight:bold;">Oli Mesin Masuk</td><td style="width:10px; text-align:center">:</td><td>'.$obj->formatNumber($rs[0]['oilin'],2).'</td></tr>
<tr><td style="font-weight:bold;">Jenis Oli</td><td style="width:10px; text-align:center">:</td><td>'.$arrOilType[$rs[0]['oiltypekey']].'</td></tr>
<tr><td style="font-weight:bold;">Merk Oli</td><td style="width:10px; text-align:center">:</td><td>'.$oilBrand.'</td></tr>
<tr><td style="font-weight:bold;">KM Pergantian</td><td style="text-align:center">:</td><td>'.$obj->formatNumber($rs[0]['mileagemaintenance']).'</td></tr>
<tr><td style="font-weight:bold;">KM Kembali</td><td style="text-align:center">:</td><td>'.$obj->formatNumber($rs[0]['mileagenextdue']).'</td></tr> 
</table>
</td>

<td > 
<table cellpadding="2" >
<tr><td style="width:100px; font-weight:bold;">Paket Tune Up</td><td style="width:10px; text-align:center">:</td><td style="width:140px;">'.$arrTuneUp[$rs[0]['tuneupkey']].'</td></tr>
<tr><td style="font-weight:bold;">Filter Oli</td><td style="text-align:center">:</td><td>'.$oilFilter.'</td></tr>
<tr><td style="font-weight:bold;">Filter Udara</td><td style="text-align:center">:</td><td>'.$airFilter.'</td></tr>
<tr><td style="font-weight:bold;">Paket Hemat BBM</td><td style="text-align:center">:</td><td>'.$arrBBMPackage[$rs[0]['ultimatepackagekey']].'</td></tr>  
</table>
</td>

</tr>
</table>  
<div style="clear:both;"></div>
<table cellpadding="4" style="text-align:justify; font-size: 0.8em">
<tr>
<td>'.$trNotes.$trWork.'</td> 
<td>'.$trPart.$trSuggestion.'</td>
</tr>  
</table> 
<div style="clear:both;"></div>
'; 
    
$html .= $obj->generateSignLabel($rs); 
return $html;
}
