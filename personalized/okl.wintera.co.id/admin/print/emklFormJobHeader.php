<?php 

$PRINT_SETTINGS =  array(   
         'showPrintHeader' => false,
         'showPrintFooter' => false,
         );

$generateReportContent = function ($dataset){ 
global $pdf; 
    
$obj = new EMKLJobOrderHeader(); 
$jobOrder = new EMKLJobOrder();  
$supplier = new Supplier();
$vessel = new Vessel();
$terminal = new Terminal();
$employee = new Employee();

$rs = $dataset['rs'];    
$rsJobOrder = $jobOrder->searchData('','',true,' and '.$jobOrder->tableName.'.headerorderkey  = '.$obj->oDbCon->paramString($rs[0]['pkey']).' and '.$jobOrder->tableName.'.statuskey  in (1,2,3)');
//$companyName = $obj->loadSetting('companyName'); 
$customerName = $rs[0]['customername'];     
$rsDetail = $obj->getDetailWithRelatedInformation($rs[0]['pkey']);
$rsTrucking = $supplier->getDataRowById($rs[0]['truckingkey']);
$truckingName = (!empty($rsTrucking)) ? $rsTrucking[0]['name'] : '';
$salesmanName = $rs[0]['salesname']; 
    
//$rsJobType = $obj->getEmklType($rs[0]['loadcontainertypekey']);
    $party = '';
if($rsJobOrder[0]['loadcontainertypekey']==EMKL['emklType']['fcl'] || $rsJobOrder[0]['loadcontainertypekey']==EMKL['emklType']['trucking']){ 
    $arrParty = array();    
    $rsParty = $jobOrder->getDetailVolume($rsJobOrder[0]['pkey']); 
    for($i=0;$i<count($rsParty);$i++) 
         array_push($arrParty,$obj->formatNumber($rsParty[$i]['qty']) . 'x ' . $rsParty[$i]['itemname']);
    $party = implode('<br>',$arrParty);
}else{
    $rsParty = $jobOrder->getCubicVolume($rsJobOrder[0]['pkey']);
    $temp = array();
    if(!empty($rsParty[0]['weight'])) array_push($temp, $obj->formatNumber($rsParty[0]['weight'],2) . ' KG');
    if(!empty($rsParty[0]['measurement'])) array_push($temp, $obj->formatNumber($rsParty[0]['measurement'],2). ' CBM');
    
    $party = implode(', ',$temp);
}
    
$terminalName = '';
if (!empty($rsJobOrder[0]['terminalkey'])){
    $rsTerminal = $terminal->getDataRowById($rsJobOrder[0]['terminalkey']); 
    $terminalName = $rsTerminal[0]['name'];
}
$vesselName = '';
if(!empty($rsJobOrder[0]['vesselkey'])){
    $rsVessel = $vessel->getDataRowById($rsJobOrder[0]['vesselkey']);
    $vesselName = $rsVessel[0]['name'];
    
    if (!empty( $rs[0]['vesselnumber']))
        $vesselName .= ' ' .$rs[0]['vesselnumber'];  
} 
     
$trNotes = (!empty($rsJobOrder[0]['trdesc'])) ?   str_replace(chr(13),', ',$rsJobOrder[0]['trdesc']) : '';
    
$html = $obj->printSetting['defaultStyle'];
$html .= ' 


<div style="clear:both"></div>

'; 
    
$tableExport ='<table>
<tr>
<td style="">
    <table cellpadding="3" style="border:solid 1px black">
    <tr><td style="border-bottom:solid 1px #000;width:110px;">'.$obj->lang['jobOrderNumber'].'</td><td style="border-bottom:solid 1px #000;width:12px;">:</td><td style="border-bottom:solid 1px #000;width:200px;font-size:1.2em;font-weight:bold">'.$rsJobOrder[0]['code'].'</td></tr>
    <tr><td style="border-bottom:solid 1px #000;width:110px;">'.$obj->lang['party'].'</td><td style="border-bottom:solid 1px #000;width:12px;">:</td><td style="border-bottom:solid 1px #000;width:200px;font-size:1.2em;font-weight:bold">'.$party.'</td></tr>
    </table> 
</td>
<td style="">
    <table cellpadding="3"  style="border:solid 1px black">
    <tr><td style="border-bottom:solid 1px #000;width:110px;">KAPAL</td><td style="border-bottom:solid 1px #000;width:12px;">:</td><td style="border-bottom:solid 1px #000;width:200px;font-size:1.2em;font-weight:bold">'.strtoupper($vesselName).'</td></tr>
    <tr><td style="border-bottom:solid 1px #000;width:110px;">MBL / HBL NO</td><td style="border-bottom:solid 1px #000;width:12px;">:</td><td style="border-bottom:solid 1px #000;width:200px;font-size:1.2em;font-weight:bold">'.$rsJobOrder[0]['mblnumber'].'</td></tr>
    </table>
</td>
</tr>
<tr><td></td></tr>
<tr>
<td style="">
    <table>
    <tr><td>Pembuatan Dokumen : PIC : CLEARANCE DEPT.</td></tr>
    </table>     
    <table cellpadding="3" style="border:solid 1px black">
    <tr><td style="border-bottom:solid 1px #000;width:110px;">No. AJU PEB</td><td style="border-bottom:solid 1px #000;width:12px;">:</td><td style="border-bottom:solid 1px #000;width:200px;font-size:1.2em;font-weight:bold">'.$rsJobOrder[0]['aju'].'</td></tr>
    <tr><td style="border-bottom:solid 1px #000;width:110px;">Pembuatan draft PEB</td><td style="border-bottom:solid 1px #000;width:12px;">:</td><td style="border-bottom:solid 1px #000;width:200px;font-size:1.2em;font-weight:bold"></td></tr>
    </table> 
    <table>
    <tr><td></td></tr>
    <tr><td>Penerimaan dokumen dari Bank/ Importir/PIC:CS</td></tr>
    </table>     
    <table cellpadding="3" style="border:solid 1px black">
    <tr><td style="'.$style.'width:110px;">Tanggal terima dok.</td><td style="'.$style.';width:12px;">:</td><td style="border-bottom:solid 1px #000;width:200px;font-size:1.2em;font-weight:bold;text-align:center">'.$obj->formatDBDate($rsJobOrder[0]['datedoc'],'', array('returnOnEmpty'=>true)).'</td></tr>
    <tr><td style="'.$style.'width:110px;">Shipping Instruction	</td><td style="'.$style.';width:12px;">:</td><td style="border-bottom:solid 1px #000;width:200px;font-size:1.2em;font-weight:bold;text-align:center">'.$obj->formatDBDate($rsJobOrder[0]['sidate'],'', array('returnOnEmpty'=>true)).'</td></tr>
    <tr><td style="'.$style.'width:110px;">Booking Confirmation	</td><td style="'.$style.';width:12px;">:</td><td style="border-bottom:solid 1px #000;width:200px;font-size:1.2em;font-weight:bold;text-align:center">'.$obj->formatDBDate($rsJobOrder[0]['bookingdate'],'', array('returnOnEmpty'=>true)).'</td></tr>
    <tr><td style="'.$style.'width:110px;">'.$obj->lang['invoice'].'</td><td style="'.$style.';width:12px;">:</td><td style="border-bottom:solid 1px #000;width:200px;font-size:1.2em;font-weight:bold;text-align:center">'.$obj->formatDBDate($rsJobOrder[0]['invoicedate'],'', array('returnOnEmpty'=>true)).'</td></tr>
    <tr><td style="'.$style.'width:110px;">Packing List</td><td style="'.$style.';width:12px;">:</td><td style="border-bottom:solid 1px #000;width:200px;font-size:1.2em;font-weight:bold;text-align:center">'.$obj->formatDBDate($rsJobOrder[0]['packingdate'],'', array('returnOnEmpty'=>true)).'</td></tr>
    <tr><td style="'.$style.'width:110px;">'.$obj->lang['insurance'].'</td><td style="'.$style.';width:12px;">:</td><td style="border-bottom:solid 1px #000;width:200px;font-size:1.2em;font-weight:bold;text-align:center">'.$obj->formatDBDate($rsJobOrder[0]['insurancedate'],'', array('returnOnEmpty'=>true)).'</td></tr>
    <tr><td style="'.$style.'width:110px;">Form: D, E, AK, Jiepa,dll</td><td style="'.$style.';width:12px;">:</td><td style="border-bottom:solid 1px #000;width:200px;font-size:1.2em;font-weight:bold;text-align:center">'.$obj->formatDBDate($rsJobOrder[0]['formdate'],'', array('returnOnEmpty'=>true)).'</td></tr>
    <tr><td style="'.$style.'width:110px;">'.$obj->lang['stuffingLocation'].'</td><td style="'.$style.';width:12px;">:</td><td style="border-bottom:solid 1px #000;width:200px;font-size:1.2em;font-weight:bold;text-align:center">'.$rsJobOrder[0]['stuffinglocation'].'</td></tr>
    </table> 
    <table>
    <tr><td></td></tr>
    <tr><td>PIC : CLEARANCE DEPT.</td></tr>
    </table>     
    <table cellpadding="3" style="border:solid 1px black">
    <tr><td style="'.$style.'width:110px;">Nilai Pabean  </td><td style="'.$style.';width:12px;">:</td><td style="border-bottom:solid 1px #000;width:200px;font-size:1.2em;font-weight:bold">'.$obj->formatNumber($rsJobOrder[0]['pabean']).'</td></tr>
    <tr><td style="'.$style.'width:110px;">Incoterm</td><td style="'.$style.';width:12px;">:</td><td style="border-bottom:solid 1px #000;width:200px;font-size:1.2em;font-weight:bold">'.$rsJobOrder[0]['incoterm'].'</td></tr>
    <tr><td style="'.$style.'width:110px;">Lartas   </td><td style="'.$style.';width:12px;">:</td><td style="border-bottom:solid 1px #000;width:200px;font-size:1.2em;font-weight:bold">'.$rsJobOrder[0]['lartas'].'</td></tr>
    <tr><td style="'.$style.'width:110px;">Jumlah/jenis kemasan  </td><td style="'.$style.';width:12px;">:</td><td style="border-bottom:solid 1px #000;width:200px;font-size:1.2em;font-weight:bold">'.$rsJobOrder[0]['qtypack'].'</td></tr>
    <tr><td style="'.$style.'width:110px;">Transfer PEB </td><td style="'.$style.';width:12px;">:</td><td style="border-bottom:solid 1px #000;width:200px;font-size:1.2em;font-weight:bold">'.$obj->formatDBDate($$rsJobOrderrs[0]['transferpibdate'],'', array('returnOnEmpty'=>true)).'</td></tr>
    <tr><td style="'.$style.'width:110px;">Respon / P. Jaluran </td><td style="'.$style.';width:12px;">:</td><td style="border-bottom:solid 1px #000;width:200px;font-size:1.2em;font-weight:bold">'.$rsJobOrder[0]['response'].'</td></tr>
    <tr><td style="'.$style.'width:110px;">NPE</td><td style="'.$style.';width:12px;">:</td><td style="border-bottom:solid 1px #000;width:200px;font-size:1.2em;font-weight:bold">'.$obj->formatDBDate($rsJobOrder[0]['deliverynpedate'],'', array('returnOnEmpty'=>true)).'</td></tr>
    </table> 
    <table>
    <tr><td></td></tr>
    <tr><td>ACC. & FINANCE DEPT.</td></tr>
    </table>     
    <table cellpadding="3" style="border:solid 1px black">
    <tr><td style="'.$style.'width:110px;">Nomor DN / Tgl </td><td style="'.$style.';width:12px;">:</td><td style="border-bottom:solid 1px #000;width:200px;font-size:1.2em;font-weight:bold"></td></tr>
    <tr><td style="'.$style.'width:110px;">No. Kwitansi / Tgl </td><td style="'.$style.';width:12px;">:</td><td style="border-bottom:solid 1px #000;width:200px;font-size:1.2em;font-weight:bold"></td></tr>
    </table> 
</td>
<td style="">
    <table>
    <tr><td>PIC: CUSTOMER SERVICE</td></tr>
    </table> 
    <table cellpadding="3"  style="border:solid 1px black">
    <tr><td style="border-bottom:solid 1px #000;width:110px;">Proses Dokumen</td><td style="border-bottom:solid 1px #000;width:12px;">:</td><td style="border-bottom:solid 1px #000;width:200px;font-size:1.2em;font-weight:bold;text-align:center">'.$obj->formatDBDate($rsJobOrder[0]['transferpibdate'],'', array('returnOnEmpty'=>true)).'</td></tr>
    <tr><td style="border-bottom:solid 1px #000;width:110px;">DEPO AMBIL EMPTY</td><td style="border-bottom:solid 1px #000;width:12px;">:</td><td style="border-bottom:solid 1px #000;width:200px;font-size:1.2em;font-weight:bold;text-align:center"></td></tr>
    <tr><td style="border-bottom:solid 1px #000;width:110px;">'.$obj->lang['stuffing'].'</td><td style="border-bottom:solid 1px #000;width:12px;">:</td><td style="border-bottom:solid 1px #000;width:200px;font-size:1.2em;font-weight:bold;text-align:center">'.$obj->formatDBDate($rsJobOrder[0]['stuffingin'],'', array('returnOnEmpty'=>true)).'</td></tr>
    <tr><td style="border-bottom:solid 1px #000;width:110px;">'.$obj->lang['closingDate'].'</td><td style="border-bottom:solid 1px #000;width:12px;">:</td><td style="border-bottom:solid 1px #000;width:200px;font-size:1.2em;font-weight:bold;text-align:center">'.$obj->formatDBDate($rsJobOrder[0]['closingdate'],'', array('returnOnEmpty'=>true)).'</td></tr>
    <tr><td style="border-bottom:solid 1px #000;width:110px;">'.$obj->lang['etd'].'</td><td style="border-bottom:solid 1px #000;width:12px;">:</td><td style="border-bottom:solid 1px #000;width:200px;font-size:1.2em;font-weight:bold;text-align:center">'.$obj->formatDBDate($rsJobOrder[0]['etdpol'],'', array('returnOnEmpty'=>true)).'</td></tr>
    <tr><td style="border-bottom:solid 1px #000;width:110px;">'.$obj->lang['eta'].'</td><td style="border-bottom:solid 1px #000;width:12px;">:</td><td style="border-bottom:solid 1px #000;width:200px;font-size:1.2em;font-weight:bold;text-align:center">'.$obj->formatDBDate($rsJobOrder[0]['etapod'],'', array('returnOnEmpty'=>true)).'</td></tr>
    <tr><td style="border-bottom:solid 1px #000;width:110px;">'.$obj->lang['time'].'</td><td style="border-bottom:solid 1px #000;width:12px;">:</td><td style="border-bottom:solid 1px #000;width:200px;font-size:1.2em;font-weight:bold;text-align:center">'.$obj->formatDBDate($rsJobOrder[0]['closingdate'],'H:i', array('returnOnEmpty'=>true)).'</td></tr>
    <tr><td style="border-bottom:solid 1px #000;width:110px;">Lokasi CY</td><td style="border-bottom:solid 1px #000;width:12px;">:</td><td style="border-bottom:solid 1px #000;width:200px;font-size:1.2em;font-weight:bold;text-align:center">'.$terminalName.'</td></tr>
    </table>
    <table>
    <tr><td></td></tr>
    <tr><td>Bukti Pembayaran PIC: OPERASIONAL DEPT.</td></tr>
    </table> 
    <table cellpadding="3"  style="border:solid 1px black">
    <tr><td style="border-bottom:solid 1px #000;width:110px;">Adm D/O</td><td style="border-bottom:solid 1px #000;width:12px;">:</td><td style="border-bottom:solid 1px #000;width:200px;font-size:1.2em;font-weight:bold;text-align:center">'.$obj->formatDBDate($rsJobOrder[0]['admdodate'],'', array('returnOnEmpty'=>true)).'</td></tr>
    <tr><td style="border-bottom:solid 1px #000;width:110px;">THC / CFS  / LAINNYA </td><td style="border-bottom:solid 1px #000;width:12px;">:</td><td style="border-bottom:solid 1px #000;width:200px;font-size:1.2em;font-weight:bold;text-align:center">'.$obj->formatDBDate($rsJobOrder[0]['thcdate'],'', array('returnOnEmpty'=>true)).'</td></tr>
    <tr><td style="border-bottom:solid 1px #000;width:110px;">Lift Off dari trucking </td><td style="border-bottom:solid 1px #000;width:12px;">:</td><td style="border-bottom:solid 1px #000;width:200px;font-size:1.2em;font-weight:bold;text-align:center">'.$obj->formatDBDate($rsJobOrder[0]['liftondate'],'', array('returnOnEmpty'=>true)).'</td></tr>
    <tr><td style="border-bottom:solid 1px #000;width:110px;">Tgl. Pengambilan Empty</td><td style="border-bottom:solid 1px #000;width:12px;">:</td><td style="border-bottom:solid 1px #000;width:200px;font-size:1.2em;font-weight:bold;text-align:center"></td></tr>
    <tr><td style="border-bottom:solid 1px #000;width:110px;">Penyerahan NPE dari CS</td><td style="border-bottom:solid 1px #000;width:12px;">:</td><td style="border-bottom:solid 1px #000;width:200px;font-size:1.2em;font-weight:bold;text-align:center">'.$obj->formatDBDate($rsJobOrder[0]['deliverynpedate'],'', array('returnOnEmpty'=>true)).'</td></tr>
    <tr><td style="border-bottom:solid 1px #000;width:110px;">Tgl Penerbitan KARTU EKSPOR</td><td style="border-bottom:solid 1px #000;width:12px;">:</td><td style="border-bottom:solid 1px #000;width:200px;font-size:1.2em;font-weight:bold;text-align:center">'.$obj->formatDBDate($rsJobOrder[0]['exportcarddate'],'', array('returnOnEmpty'=>true)).'</td></tr>
    </table>
    <table>
    <tr><td></td></tr>
    <tr><td>PIC: TRUCKING DEPT..</td></tr>
    </table> 
    <table cellpadding="3"  style="border:solid 1px black">
    <tr><td style="border-bottom:solid 1px #000;width:110px;">Terima surat jalan</td><td style="border-bottom:solid 1px #000;width:12px;">:</td><td style="border-bottom:solid 1px #000;width:200px;font-size:1.2em;font-weight:bold"></td></tr>
    <tr><td style="border-bottom:solid 1px #000;width:110px;">Order Trucking</td><td style="border-bottom:solid 1px #000;width:12px;">:</td><td style="border-bottom:solid 1px #000;width:200px;font-size:1.2em;font-weight:bold"></td></tr>
    <tr><td style="border-bottom:solid 1px #000;width:110px;">Truck gate-in</td><td style="border-bottom:solid 1px #000;width:12px;">:</td><td style="border-bottom:solid 1px #000;width:200px;font-size:1.2em;font-weight:bold"></td></tr>
    <tr><td style="border-bottom:solid 1px #000;width:110px;">Truck gate-out</td><td style="border-bottom:solid 1px #000;width:12px;">:</td><td style="border-bottom:solid 1px #000;width:200px;font-size:1.2em;font-weight:bold"></td></tr>
    <tr><td style="border-bottom:solid 1px #000;width:110px;">Pengambilan lift on/off</td><td style="border-bottom:solid 1px #000;width:12px;">:</td><td style="border-bottom:solid 1px #000;width:200px;font-size:1.2em;font-weight:bold"></td></tr>
    <tr><td style="border-bottom:solid 1px #000;width:110px;">Catatan Trucking</td><td style="border-bottom:solid 1px #000;width:12px;">:</td><td style="border-bottom:solid 1px #000;width:200px;font-size:1.2em;font-weight:bold"></td></tr>
    </table>
</td>
</tr>


</table>
';
    
    
$tableImport  ='<table>
<tr>
<td style="">
    <table cellpadding="3" style="border:solid 1px black">
    <tr><td style="border-bottom:solid 1px #000;width:110px;">'.$obj->lang['jobOrderNumber'].'</td><td style="border-bottom:solid 1px #000;width:12px;">:</td><td style="border-bottom:solid 1px #000;width:200px;font-size:1.2em;font-weight:bold">'.$rsJobOrder[0]['code'].'</td></tr>
    <tr><td style="border-bottom:solid 1px #000;width:110px;">'.$obj->lang['party'].'</td><td style="border-bottom:solid 1px #000;width:12px;">:</td><td style="border-bottom:solid 1px #000;width:200px;font-size:1.2em;font-weight:bold">'.$party.'</td></tr>
    </table> 
</td>
<td style="">
    <table cellpadding="3"  style="border:solid 1px black">
    <tr><td style="border-bottom:solid 1px #000;width:110px;">KAPAL</td><td style="border-bottom:solid 1px #000;width:12px;">:</td><td style="border-bottom:solid 1px #000;width:200px;font-size:1.2em;font-weight:bold">'.strtoupper($vesselName).'</td></tr>
    <tr><td style="border-bottom:solid 1px #000;width:110px;">MBL / HBL NO</td><td style="border-bottom:solid 1px #000;width:12px;">:</td><td style="border-bottom:solid 1px #000;width:200px;font-size:1.2em;font-weight:bold">'.$rsJobOrder[0]['mblnumber'].'</td></tr>
    </table>
</td>
</tr>
<tr><td></td></tr>
<tr>
<td style="">
    <table>
    <tr><td>Pembuatan Dokumen : PIC : CLEARANCE DEPT.</td></tr>
    </table>     
    <table cellpadding="3" style="border:solid 1px black">
    <tr><td style="border-bottom:solid 1px #000;width:110px;">No. AJU PIB</td><td style="border-bottom:solid 1px #000;width:12px;">:</td><td style="border-bottom:solid 1px #000;width:200px;font-size:1.2em;font-weight:bold">'.$rsJobOrder[0]['aju'].'</td></tr>
    <tr><td style="border-bottom:solid 1px #000;width:110px;">Pembuatan draft PIB</td><td style="border-bottom:solid 1px #000;width:12px;">:</td><td style="border-bottom:solid 1px #000;width:200px;font-size:1.2em;font-weight:bold"></td></tr>
    </table> 
    <table>
    <tr><td></td></tr>
    <tr><td>Penerimaan dokumen dari Bank/ Importir/PIC:CS</td></tr>
    </table>     
    <table cellpadding="3" style="border:solid 1px black">
    <tr><td style="'.$style.'width:110px;">Tanggal terima dok.</td><td style="'.$style.';width:12px;">:</td><td style="border-bottom:solid 1px #000;width:200px;font-size:1.2em;font-weight:bold;text-align:center">'.$obj->formatDBDate($rsJobOrder[0]['datedoc'],'', array('returnOnEmpty'=>true)).'</td></tr>
    <tr><td style="'.$style.'width:110px;">B/L Original</td><td style="'.$style.';width:12px;">:</td><td style="border-bottom:solid 1px #000;width:200px;font-size:1.2em;font-weight:bold;text-align:center">'.$obj->formatDBDate($rsJobOrder[0]['originaldate'],'', array('returnOnEmpty'=>true)).'</td></tr>
    <tr><td style="'.$style.'width:110px;">'.$obj->lang['invoice'].'</td><td style="'.$style.';width:12px;">:</td><td style="border-bottom:solid 1px #000;width:200px;font-size:1.2em;font-weight:bold;text-align:center">'.$obj->formatDBDate($rsJobOrder[0]['invoicedate'],'', array('returnOnEmpty'=>true)).'</td></tr>
    <tr><td style="'.$style.'width:110px;">Packing List</td><td style="'.$style.';width:12px;">:</td><td style="border-bottom:solid 1px #000;width:200px;font-size:1.2em;font-weight:bold;text-align:center">'.$obj->formatDBDate($rsJobOrder[0]['packingdate'],'', array('returnOnEmpty'=>true)).'</td></tr>
    <tr><td style="'.$style.'width:110px;">'.$obj->lang['insurance'].'</td><td style="'.$style.';width:12px;">:</td><td style="border-bottom:solid 1px #000;width:200px;font-size:1.2em;font-weight:bold;text-align:center">'.$obj->formatDBDate($rsJobOrder[0]['insurancedate'],'', array('returnOnEmpty'=>true)).'</td></tr>
    <tr><td style="'.$style.'width:110px;">Surat Kuasa</td><td style="'.$style.';width:12px;">:</td><td style="border-bottom:solid 1px #000;width:200px;font-size:1.2em;font-weight:bold;text-align:center">'.$obj->formatDBDate($rsJobOrder[0]['formdate'],'', array('returnOnEmpty'=>true)).'</td></tr>
    <tr><td style="'.$style.'width:110px;">Form: D, E, AK, Jiepa,dll</td><td style="'.$style.';width:12px;">:</td><td style="border-bottom:solid 1px #000;width:200px;font-size:1.2em;font-weight:bold;text-align:center">'.$obj->formatDBDate($rsJobOrder[0]['procurationdate'],'', array('returnOnEmpty'=>true)).'</td></tr>
    <tr><td style="'.$style.'width:110px;">SK Pabean </td><td style="'.$style.';width:12px;">:</td><td style="border-bottom:solid 1px #000;width:200px;font-size:1.2em;font-weight:bold;text-align:center">'.$obj->formatDBDate($rsJobOrder[0]['procurationpabeandate'],'', array('returnOnEmpty'=>true)).'</td></tr>
    <tr><td style="'.$style.'width:110px;">SK DO</td><td style="'.$style.';width:12px;">:</td><td style="border-bottom:solid 1px #000;width:200px;font-size:1.2em;font-weight:bold;text-align:center">'.$obj->formatDBDate($rsJobOrder[0]['procurationdodate'],'', array('returnOnEmpty'=>true)).'</td></tr>
    <tr><td style="'.$style.'width:110px;">LS</td><td style="'.$style.';width:12px;">:</td><td style="border-bottom:solid 1px #000;width:200px;font-size:1.2em;font-weight:bold;text-align:center">'.$obj->formatDBDate($rsJobOrder[0]['lsdate'],'', array('returnOnEmpty'=>true)).'</td></tr>
    </table> 
    <table>
    <tr><td></td></tr>
    <tr><td>PIC : CLEARANCE DEPT.</td></tr>
    </table>     
    <table cellpadding="3" style="border:solid 1px black">
    <tr><td style="'.$style.'width:110px;">Nilai Pabean  </td><td style="'.$style.';width:12px;">:</td><td style="border-bottom:solid 1px #000;width:200px;font-size:1.2em;font-weight:bold">'.$obj->formatNumber($rsJobOrder[0]['pabean']).'</td></tr>
    <tr><td style="'.$style.'width:110px;">Incoterm</td><td style="'.$style.';width:12px;">:</td><td style="border-bottom:solid 1px #000;width:200px;font-size:1.2em;font-weight:bold">'.$rsJobOrder[0]['incoterm'].'</td></tr>
    <tr><td style="'.$style.'width:110px;">Lartas   </td><td style="'.$style.';width:12px;">:</td><td style="border-bottom:solid 1px #000;width:200px;font-size:1.2em;font-weight:bold">'.$rsJobOrder[0]['lartas'].'</td></tr>
    <tr><td style="'.$style.'width:110px;">Jumlah/jenis kemasan  </td><td style="'.$style.';width:12px;">:</td><td style="border-bottom:solid 1px #000;width:200px;font-size:1.2em;font-weight:bold">'.$rsJobOrder[0]['qtypack'].'</td></tr>
    <tr><td style="'.$style.'width:110px;">Lokasi Barang Pada Saat Transfer</td><td style="'.$style.';width:12px;">:</td><td style="border-bottom:solid 1px #000;width:200px;font-size:1.2em;font-weight:bold">'.$rsJobOrder[0]['stuffinglocation'].'</td></tr>
    <tr><td style="'.$style.'width:110px;">Transfer PIB </td><td style="'.$style.';width:12px;">:</td><td style="border-bottom:solid 1px #000;width:200px;font-size:1.2em;font-weight:bold">'.$obj->formatDBDate($rsJobOrder[0]['transferpibdate'],'', array('returnOnEmpty'=>true)).'</td></tr>
    <tr><td style="'.$style.'width:110px;">Respon / P. Jaluran </td><td style="'.$style.';width:12px;">:</td><td style="border-bottom:solid 1px #000;width:200px;font-size:1.2em;font-weight:bold">'.$rsJobOrder[0]['response'].'</td></tr>
    <tr><td style="'.$style.'width:110px;">SPPB Final</td><td style="'.$style.';width:12px;">:</td><td style="border-bottom:solid 1px #000;width:200px;font-size:1.2em;font-weight:bold">'.$obj->formatDBDate($rsJobOrder[0]['sppbdate'],'', array('returnOnEmpty'=>true)).'</td></tr>
    </table> 
    <table>
    <tr><td></td></tr>
    <tr><td>ACC. & FINANCE DEPT.</td></tr>
    </table>     
    <table cellpadding="3" style="border:solid 1px black">
    <tr><td style="'.$style.'width:110px;">Nomor DN / Tgl </td><td style="'.$style.';width:12px;">:</td><td style="border-bottom:solid 1px #000;width:200px;font-size:1.2em;font-weight:bold"></td></tr>
    <tr><td style="'.$style.'width:110px;">No. Kwitansi / Tgl </td><td style="'.$style.';width:12px;">:</td><td style="border-bottom:solid 1px #000;width:200px;font-size:1.2em;font-weight:bold"></td></tr>
    </table> 
</td>
<td style="">
    <table>
    <tr><td>PIC: CUSTOMER SERVICE</td></tr>
    </table> 
    <table cellpadding="3"  style="border:solid 1px black">
    <tr><td style="border-bottom:solid 1px #000;width:110px;">Proses Dokumen</td><td style="border-bottom:solid 1px #000;width:12px;">:</td><td style="border-bottom:solid 1px #000;width:200px;font-size:1.2em;font-weight:bold;text-align:center">'.$obj->formatDBDate($rsJobOrder[0]['transferpibdate'],'', array('returnOnEmpty'=>true)).'</td></tr>
    <tr><td style="border-bottom:solid 1px #000;width:110px;">Kapal Transit</td><td style="border-bottom:solid 1px #000;width:12px;">:</td><td style="border-bottom:solid 1px #000;width:200px;font-size:1.2em;font-weight:bold;text-align:center"></td></tr>
    <tr><td style="border-bottom:solid 1px #000;width:110px;">'.$obj->lang['etd'].'</td><td style="border-bottom:solid 1px #000;width:12px;">:</td><td style="border-bottom:solid 1px #000;width:200px;font-size:1.2em;font-weight:bold;text-align:center">'.$obj->formatDBDate($rsJobOrder[0]['etdpol'],'', array('returnOnEmpty'=>true)).'</td></tr>
    <tr><td style="border-bottom:solid 1px #000;width:110px;">'.$obj->lang['eta'].'</td><td style="border-bottom:solid 1px #000;width:12px;">:</td><td style="border-bottom:solid 1px #000;width:200px;font-size:1.2em;font-weight:bold;text-align:center">'.$obj->formatDBDate($rsJobOrder[0]['etapod'],'', array('returnOnEmpty'=>true)).'</td></tr>
    <tr><td style="border-bottom:solid 1px #000;width:110px;">Lokasi awal</td><td style="border-bottom:solid 1px #000;width:12px;">:</td><td style="border-bottom:solid 1px #000;width:200px;font-size:1.2em;font-weight:bold;text-align:center">'.$rsJobOrder[0]['stuffinglocation'].'</td></tr>
    <tr><td style="border-bottom:solid 1px #000;width:110px;">Lokasi O/B</td><td style="border-bottom:solid 1px #000;width:12px;">:</td><td style="border-bottom:solid 1px #000;width:200px;font-size:1.2em;font-weight:bold;text-align:center">'.$terminalName.'</td></tr>
    <tr><td style="border-bottom:solid 1px #000;width:110px;">BC 1.1/ POS / TGL</td><td style="border-bottom:solid 1px #000;width:12px;">:</td><td style="border-bottom:solid 1px #000;width:200px;font-size:1.2em;font-weight:bold;text-align:center"></td></tr>
    </table>
    <table>
    <tr><td></td></tr>
    <tr><td>Bukti Pembayaran PIC: OPERASIONAL DEPT.</td></tr>
    </table> 
    <table cellpadding="3"  style="border:solid 1px black">
    <tr><td style="border-bottom:solid 1px #000;width:110px;">Adm D/O</td><td style="border-bottom:solid 1px #000;width:12px;">:</td><td style="border-bottom:solid 1px #000;width:200px;font-size:1.2em;font-weight:bold;text-align:center">'.$obj->formatDBDate($rsJobOrder[0]['admdodate'],'', array('returnOnEmpty'=>true)).'</td></tr>
    <tr><td style="border-bottom:solid 1px #000;width:110px;">THC / CFS</td><td style="border-bottom:solid 1px #000;width:12px;">:</td><td style="border-bottom:solid 1px #000;width:200px;font-size:1.2em;font-weight:bold;text-align:center">'.$obj->formatDBDate($rsJobOrder[0]['thcdate'],'', array('returnOnEmpty'=>true)).'</td></tr>
    <tr><td style="border-bottom:solid 1px #000;width:110px;">Lift Off dari trucking </td><td style="border-bottom:solid 1px #000;width:12px;">:</td><td style="border-bottom:solid 1px #000;width:200px;font-size:1.2em;font-weight:bold;text-align:center">'.$obj->formatDBDate($rsJobOrder[0]['liftoffdate'],'', array('returnOnEmpty'=>true)).'</td></tr>
    <tr><td style="border-bottom:solid 1px #000;width:110px;">Agency Fee </td><td style="border-bottom:solid 1px #000;width:12px;">:</td><td style="border-bottom:solid 1px #000;width:200px;font-size:1.2em;font-weight:bold;text-align:center">'.$obj->formatDBDate($rsJobOrder[0]['agencydate'],'', array('returnOnEmpty'=>true)).'</td></tr>
    <tr><td style="border-bottom:solid 1px #000;width:110px;">Mekanis </td><td style="border-bottom:solid 1px #000;width:12px;">:</td><td style="border-bottom:solid 1px #000;width:200px;font-size:1.2em;font-weight:bold;text-align:center">'.$obj->formatDBDate($rsJobOrder[0]['mechanicdate'],'', array('returnOnEmpty'=>true)).'</td></tr>
    <tr><td style="border-bottom:solid 1px #000;width:110px;">Penumpukan / Lift On </td><td style="border-bottom:solid 1px #000;width:12px;">:</td><td style="border-bottom:solid 1px #000;width:200px;font-size:1.2em;font-weight:bold;text-align:center">'.$obj->formatDBDate($rsJobOrder[0]['liftondate'],'', array('returnOnEmpty'=>true)).'</td></tr>
    <tr><td style="border-bottom:solid 1px #000;width:110px;">Demurrage </td><td style="border-bottom:solid 1px #000;width:12px;">:</td><td style="border-bottom:solid 1px #000;width:200px;font-size:1.2em;font-weight:bold;text-align:center">'.$obj->formatDBDate($rsJobOrder[0]['demurragedate'],'', array('returnOnEmpty'=>true)).'</td></tr>
    <tr><td style="border-bottom:solid 1px #000;width:110px;">Tgl. Pengambilan DO</td><td style="border-bottom:solid 1px #000;width:12px;">:</td><td style="border-bottom:solid 1px #000;width:200px;font-size:1.2em;font-weight:bold;text-align:center">'.$obj->formatDBDate($rsJobOrder[0]['dodate'],'', array('returnOnEmpty'=>true)).'</td></tr>
    <tr><td style="border-bottom:solid 1px #000;width:110px;">Penyerahan PIB / COO</td><td style="border-bottom:solid 1px #000;width:12px;">:</td><td style="border-bottom:solid 1px #000;width:200px;font-size:1.2em;font-weight:bold;text-align:center">'.$obj->formatDBDate($rsJobOrder[0]['deliverypibdate'],'', array('returnOnEmpty'=>true)).'</td></tr>
    </table>
    <table>
    <tr><td></td></tr>
    <tr><td>PIC: TRUCKING DEPT.</td></tr>
    </table> 
    <table cellpadding="3"  style="border:solid 1px black">
    <tr><td style="border-bottom:solid 1px #000;width:110px;">Terima surat jalan</td><td style="border-bottom:solid 1px #000;width:12px;">:</td><td style="border-bottom:solid 1px #000;width:200px;font-size:1.2em;font-weight:bold"></td></tr>
    <tr><td style="border-bottom:solid 1px #000;width:110px;">Tgl Penerbitan SP2</td><td style="border-bottom:solid 1px #000;width:12px;">:</td><td style="border-bottom:solid 1px #000;width:200px;font-size:1.2em;font-weight:bold"></td></tr>
    <tr><td style="border-bottom:solid 1px #000;width:110px;">Order Trucking</td><td style="border-bottom:solid 1px #000;width:12px;">:</td><td style="border-bottom:solid 1px #000;width:200px;font-size:1.2em;font-weight:bold"></td></tr>
    <tr><td style="border-bottom:solid 1px #000;width:110px;">Truck gate-in</td><td style="border-bottom:solid 1px #000;width:12px;">:</td><td style="border-bottom:solid 1px #000;width:200px;font-size:1.2em;font-weight:bold"></td></tr>
    <tr><td style="border-bottom:solid 1px #000;width:110px;">Truck gate-out</td><td style="border-bottom:solid 1px #000;width:12px;">:</td><td style="border-bottom:solid 1px #000;width:200px;font-size:1.2em;font-weight:bold"></td></tr>
    <tr><td style="border-bottom:solid 1px #000;width:110px;">Pengambilan lift on/off</td><td style="border-bottom:solid 1px #000;width:12px;">:</td><td style="border-bottom:solid 1px #000;width:200px;font-size:1.2em;font-weight:bold"></td></tr>
    <tr><td style="border-bottom:solid 1px #000;width:110px;">Catatan Trucking</td><td style="border-bottom:solid 1px #000;width:12px;">:</td><td style="border-bottom:solid 1px #000;width:200px;font-size:1.2em;font-weight:bold"></td></tr>
    </table>
</td>
</tr>


</table>';
    
if($rsJobOrder[0]['jobtypekey'] == 1){
    
    $html .= $tableImport;
    
}else{
    $html .= $tableExport;
}
    
$html .= '
<div style="clear:both"></div>

<table cellpadding="2" style="border:solid 1px black">
    <tr><td style="border-bottom:solid 1px #000;width:110px;">Nama Barang</td><td style="border-bottom:solid 1px #000;width:12px;">:</td><td style="border-bottom:solid 1px #000;width:542px;font-size:1.2em;font-weight:bold;text-align:center">'.$rsJobOrder[0]['itemdescription'].'</td></tr>
    <tr><td style="border-bottom:solid 1px #000;width:110px;">'.$obj->lang['note'].'</td><td style="border-bottom:solid 1px #000;width:12px;">:</td><td style="border-bottom:solid 1px #000;width:542x">'.$trNotes.'</td></tr>
</table>

';
 
return $html;
}

?>
