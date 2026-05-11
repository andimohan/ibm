<?php 
$PRINT_SETTINGS =  array(   
     'showPrintHeader' => false,
     );
   
includeClass(array('EMKLJobOrder.class.php'));
$emklJobOrderExport = createObjAndAddToCol(new EMKLJobOrder(EMKL['jobType']['export']));

$obj = $emklJobOrderExport;
 
$generateReportContent = function ($dataset){ 
 
$obj = new EMKLJobOrder(EMKL['jobType']['export']);  
$emklPurchaseOrderExport = new EMKLPurchaseOrder(EMKL['jobType']['export']);  
    
$service = new Service(SERVICE);
$employee = new Employee();
$container = new Container();
$currency = new Currency(); 
$emklCommission = new EMKLCommission();
$vessel = new Vessel();
$terminal = new Terminal();
$customer = new Customer();
    
$rsCurrency = $currency->searchData();
$rsCurrency = array_column($rsCurrency,'name','pkey');
    
$rsContainer = $container->searchData();
$rsContainer = array_column($rsContainer,'name','pkey');
    
$rsService = $service->searchData();  
$rsService = array_column($rsService,'name','pkey');
     
$rsFreightTerm = $obj->getFreightTerm();
$rsFreightTerm = array_column($rsFreightTerm,'name','pkey');
        
$rs = $dataset['rs'];  
$rsDetail = $obj->getDetailWithRelatedInformation($rs[0]['pkey']);
$rsCustomer = $customer->getDataRowById($rs[0]['customerkey']);

 
$vesselName = '';
if(!empty($rs[0]['vesselkey'])){
    $rsVessel = $vessel->getDataRowById($rs[0]['vesselkey']);
    $vesselName = $rsVessel[0]['name'];
    
    if (!empty( $rs[0]['vesselnumber']))
        $vesselName .= ' ' .$rs[0]['vesselnumber'];  
}    
    
$party = '';
if(in_array($rs[0]['loadcontainertypekey'], array(EMKL['container']['fcl'],EMKL['container']['trucking'])) &&  $rs[0]['transportationtypekey'] == EMKL['shipping']['sea']){   
    $arrParty = array();    
    $rsParty = $obj->getDetailVolume($rs[0]['pkey']); 
    for($i=0;$i<count($rsParty);$i++) 
         array_push($arrParty,$obj->formatNumber($rsParty[$i]['qty']) . 'x ' . $rsParty[$i]['itemname']);
    $party = implode('<br>',$arrParty);
}else{
//    $rsParty = $obj->getCubicVolume($rs[0]['pkey']);
    
    $temp = array();
    if(!empty($rs[0]['weight'])) array_push($temp, $obj->formatNumber($rs[0]['weight'],2) . ' KG');
    if(!empty($rs[0]['volume'])) array_push($temp, $obj->formatNumber($rs[0]['volume'],2). ' CBM');
    
    $party = implode(', ',$temp);
}
  
$terminalName = '';
if (!empty($rs[0]['terminalkey'])){
    $rsTerminal = $terminal->getDataRowById($rs[0]['terminalkey']); 
    $terminalName = $rsTerminal[0]['name'];
}
$trNotes .= (!empty($rs[0]['trdesc'])) ?   str_replace(chr(13),'<br>',$rs[0]['trdesc']) : '<div style="clear:both; height: 5px"></div>';

    
$style = 'border-bottom:solid 1px #000;';
    
$rsBuying = $emklPurchaseOrderExport->searchData($emklPurchaseOrderExport->tableName.'.refkey', $rs[0]['pkey'], true, ' and '.$emklPurchaseOrderExport->tableName.'.statuskey in (1,2,3)');
$rsRefund = $emklCommission->searchData($emklCommission->tableName.'.refkey', $rs[0]['pkey'], true, ' and '.$emklCommission->tableName.'.statuskey in (1,2,3)');
$sayNumber = $obj->sayNumber($rs[0]['grandtotal']);
$trnotes = (!empty($rs[0]['trdesc'])) ? '<div style="clear:both"></div><strong>'.$obj->lang['note'].' :</strong> <br>' . str_replace(chr(13),'<br>',$rs[0]['trdesc']) : '';

$html = $obj->printSetting['defaultStyle'];
$html .= ' 
<table cellpadding="2" > 
<tr><td><div class="title">Order Sheet Export</div></td></tr>
<tr><td><div class="subtitle">'.$rs[0]['code'].'</div></td></tr>
</table> 

<div style="clear:both"></div>

<table>
<tr>
<td style=""><table cellpadding="3" style="border:solid 1px black">
    <tr><td style="border-bottom:solid 1px #000;width:110px;">'.$obj->lang['jobOrderNumber'].'</td><td style="border-bottom:solid 1px #000;width:12px;">:</td><td style="border-bottom:solid 1px #000;width:200px;font-size:1.2em;font-weight:bold">'.$rsCustomer[0]['alias'].' / '. $rs[0]['code'].'</td></tr>
    <tr><td style="border-bottom:solid 1px #000;width:110px;">'.$obj->lang['party'].'</td><td style="border-bottom:solid 1px #000;width:12px;">:</td><td style="border-bottom:solid 1px #000;width:200px;font-size:1.2em;font-weight:bold">'.$party.'</td></tr>
    </table> 
</td>
<td style=""><table cellpadding="3"  style="border:solid 1px black">
    <tr><td style="border-bottom:solid 1px #000;width:110px;">KAPAL</td><td style="border-bottom:solid 1px #000;width:12px;">:</td><td style="border-bottom:solid 1px #000;width:200px;font-size:1.2em;font-weight:bold">'.strtoupper($vesselName).'</td></tr>
    <tr><td style="border-bottom:solid 1px #000;width:110px;">MBL / HBL NO</td><td style="border-bottom:solid 1px #000;width:12px;">:</td><td style="border-bottom:solid 1px #000;width:200px;font-size:1.2em;font-weight:bold">'.$rs[0]['mblnumber'].'</td></tr>
    </table>
</td>
</tr>
<tr><td></td></tr>
<tr>
<td style=""><table>
    <tr><td>Pembuatan Dokumen : PIC : CLEARANCE DEPT.</td></tr>
    </table>     
    <table cellpadding="3" style="border:solid 1px black">
    <tr><td style="border-bottom:solid 1px #000;width:110px;">No. AJU PEB</td><td style="border-bottom:solid 1px #000;width:12px;">:</td><td style="border-bottom:solid 1px #000;width:200px;font-size:1.2em;font-weight:bold">'.$rs[0]['aju'].'</td></tr>
    <tr><td style="border-bottom:solid 1px #000;width:110px;">Pembuatan draft PEB</td><td style="border-bottom:solid 1px #000;width:12px;">:</td><td style="border-bottom:solid 1px #000;width:200px;font-size:1.2em;font-weight:bold"></td></tr>
    </table> 
    <table>
    <tr><td></td></tr>
    <tr><td>Penerimaan dokumen dari Bank/ Importir/PIC:CS</td></tr>
    </table>     
    <table cellpadding="3" style="border:solid 1px black">
    <tr><td style="'.$style.'width:110px;">Tanggal terima dok.</td><td style="'.$style.';width:12px;">:</td><td style="border-bottom:solid 1px #000;width:200px;font-size:1.2em;font-weight:bold;text-align:center">'.$obj->formatDBDate($rs[0]['datedoc'],'', array('returnOnEmpty'=>true, 'value' => '')).'</td></tr>
    <tr><td style="'.$style.'width:110px;">Shipping Instruction	</td><td style="'.$style.';width:12px;">:</td><td style="border-bottom:solid 1px #000;width:200px;font-size:1.2em;font-weight:bold;text-align:center">'.$obj->formatDBDate($rs[0]['sidate'],'', array('returnOnEmpty'=>true, 'value' => '')).'</td></tr>
    <tr><td style="'.$style.'width:110px;">Booking Confirmation	</td><td style="'.$style.';width:12px;">:</td><td style="border-bottom:solid 1px #000;width:200px;font-size:1.2em;font-weight:bold;text-align:center">'.$obj->formatDBDate($rs[0]['bookingdate'],'', array('returnOnEmpty'=>true, 'value' => '' )).'</td></tr>
    <tr><td style="'.$style.'width:110px;">'.$obj->lang['invoice'].'</td><td style="'.$style.';width:12px;">:</td><td style="border-bottom:solid 1px #000;width:200px;font-size:1.2em;font-weight:bold;text-align:center">'.$obj->formatDBDate($rs[0]['invoicedate'],'', array('returnOnEmpty'=>true, 'value' => '')).'</td></tr>
    <tr><td style="'.$style.'width:110px;">Packing List</td><td style="'.$style.';width:12px;">:</td><td style="border-bottom:solid 1px #000;width:200px;font-size:1.2em;font-weight:bold;text-align:center">'.$obj->formatDBDate($rs[0]['packingdate'],'', array('returnOnEmpty'=>true, 'value' => '')).'</td></tr>
    <tr><td style="'.$style.'width:110px;">'.$obj->lang['insurance'].'</td><td style="'.$style.';width:12px;">:</td><td style="border-bottom:solid 1px #000;width:200px;font-size:1.2em;font-weight:bold;text-align:center">'.$obj->formatDBDate($rs[0]['insurancedate'],'', array('returnOnEmpty'=>true, 'value' => '')).'</td></tr>
    <tr><td style="'.$style.'width:110px;">Form: D, E, AK, Jiepa,dll</td><td style="'.$style.';width:12px;">:</td><td style="border-bottom:solid 1px #000;width:200px;font-size:1.2em;font-weight:bold;text-align:center">'.$obj->formatDBDate($rs[0]['formdate'],'', array('returnOnEmpty'=>true, 'value' => '')).'</td></tr>
    <tr><td style="'.$style.'width:110px;">'.$obj->lang['stuffingLocation'].'</td><td style="'.$style.';width:12px;">:</td><td style="border-bottom:solid 1px #000;width:200px;font-size:1.2em;font-weight:bold;text-align:center">'.$rs[0]['stuffinglocation'].'</td></tr>
    </table> 
    <table>
    <tr><td></td></tr>
    <tr><td>PIC : CLEARANCE DEPT.</td></tr>
    </table>     
    <table cellpadding="3" style="border:solid 1px black">
    <tr><td style="'.$style.'width:110px;">Nilai Pabean  </td><td style="'.$style.';width:12px;">:</td><td style="border-bottom:solid 1px #000;width:200px;font-size:1.2em;font-weight:bold">'.$obj->formatNumber($rs[0]['pabean']).'</td></tr>
    <tr><td style="'.$style.'width:110px;">Incoterm</td><td style="'.$style.';width:12px;">:</td><td style="border-bottom:solid 1px #000;width:200px;font-size:1.2em;font-weight:bold">'.$rs[0]['incoterm'].'</td></tr>
    <tr><td style="'.$style.'width:110px;">Lartas   </td><td style="'.$style.';width:12px;">:</td><td style="border-bottom:solid 1px #000;width:200px;font-size:1.2em;font-weight:bold">'.$rs[0]['lartas'].'</td></tr>
    <tr><td style="'.$style.'width:110px;">Jumlah/jenis kemasan  </td><td style="'.$style.';width:12px;">:</td><td style="border-bottom:solid 1px #000;width:200px;font-size:1.2em;font-weight:bold">'.$rs[0]['qtypack'].'</td></tr>
    <tr><td style="'.$style.'width:110px;">Transfer PEB </td><td style="'.$style.';width:12px;">:</td><td style="border-bottom:solid 1px #000;width:200px;font-size:1.2em;font-weight:bold;text-align:center">'.$obj->formatDBDate($rs[0]['transferpibdate'],'', array('returnOnEmpty'=>true, 'value' => '')).'</td></tr>
    <tr><td style="'.$style.'width:110px;">Respon / P. Jaluran </td><td style="'.$style.';width:12px;">:</td><td style="border-bottom:solid 1px #000;width:200px;font-size:1.2em;font-weight:bold">'.$rs[0]['response'].'</td></tr>
    <tr><td style="'.$style.'width:110px;">NPE</td><td style="'.$style.';width:12px;">:</td><td style="border-bottom:solid 1px #000;width:200px;font-size:1.2em;font-weight:bold;text-align:center">'.$obj->formatDBDate($rs[0]['deliverynpedate'],'', array('returnOnEmpty'=>true, 'value' => '')).'</td></tr>
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
    <tr><td style="border-bottom:solid 1px #000;width:110px;">Proses Dokumen</td><td style="border-bottom:solid 1px #000;width:12px;">:</td><td style="border-bottom:solid 1px #000;width:200px;font-size:1.2em;font-weight:bold;text-align:center">'.$obj->formatDBDate($rs[0]['transferpibdate'],'', array('returnOnEmpty'=>true, 'value' => '')).'</td></tr>
    <tr><td style="border-bottom:solid 1px #000;width:110px;">DEPO AMBIL EMPTY</td><td style="border-bottom:solid 1px #000;width:12px;">:</td><td style="border-bottom:solid 1px #000;width:200px;font-size:1.2em;font-weight:bold;text-align:center"></td></tr>
    <tr><td style="border-bottom:solid 1px #000;width:110px;">'.$obj->lang['stuffing'].'</td><td style="border-bottom:solid 1px #000;width:12px;">:</td><td style="border-bottom:solid 1px #000;width:200px;font-size:1.2em;font-weight:bold;text-align:center">'.$obj->formatDBDate($rs[0]['stuffingin'],'', array('returnOnEmpty'=>true, 'value' => '')).'</td></tr>
    <tr><td style="border-bottom:solid 1px #000;width:110px;">'.$obj->lang['closingDate'].'</td><td style="border-bottom:solid 1px #000;width:12px;">:</td><td style="border-bottom:solid 1px #000;width:200px;font-size:1.2em;font-weight:bold;text-align:center">'.$obj->formatDBDate($rs[0]['closingdate'],'', array('returnOnEmpty'=>true, 'value' => '')).'</td></tr>
    <tr><td style="border-bottom:solid 1px #000;width:110px;">'.$obj->lang['etd'].'</td><td style="border-bottom:solid 1px #000;width:12px;">:</td><td style="border-bottom:solid 1px #000;width:200px;font-size:1.2em;font-weight:bold;text-align:center">'.$obj->formatDBDate($rs[0]['etdpol'],'', array('returnOnEmpty'=>true, 'value' => '')).'</td></tr>
    <tr><td style="border-bottom:solid 1px #000;width:110px;">'.$obj->lang['eta'].'</td><td style="border-bottom:solid 1px #000;width:12px;">:</td><td style="border-bottom:solid 1px #000;width:200px;font-size:1.2em;font-weight:bold;text-align:center">'.$obj->formatDBDate($rs[0]['etapod'],'', array('returnOnEmpty'=>true, 'value' => '')).'</td></tr>
    <tr><td style="border-bottom:solid 1px #000;width:110px;">'.$obj->lang['time'].'</td><td style="border-bottom:solid 1px #000;width:12px;">:</td><td style="border-bottom:solid 1px #000;width:200px;font-size:1.2em;font-weight:bold;text-align:center">'.$obj->formatDBDate($rs[0]['closingdate'],'H:i', array('returnOnEmpty'=>true, 'value' => '')).'</td></tr>
    <tr><td style="border-bottom:solid 1px #000;width:110px;">Lokasi CY</td><td style="border-bottom:solid 1px #000;width:12px;">:</td><td style="border-bottom:solid 1px #000;width:200px;font-size:1.2em;font-weight:bold;text-align:center">'.$terminalName.'</td></tr>
    </table>
    <table>
    <tr><td></td></tr>
    <tr><td>Bukti Pembayaran PIC: OPERASIONAL DEPT.</td></tr>
    </table> 
    <table cellpadding="3"  style="border:solid 1px black">
    <tr><td style="border-bottom:solid 1px #000;width:110px;">Adm D/O</td><td style="border-bottom:solid 1px #000;width:12px;">:</td><td style="border-bottom:solid 1px #000;width:200px;font-size:1.2em;font-weight:bold;text-align:center">'.$obj->formatDBDate($rs[0]['admdodate'],'', array('returnOnEmpty'=>true, 'value' => '')).'</td></tr>
    <tr><td style="border-bottom:solid 1px #000;width:110px;">THC / CFS  / LAINNYA </td><td style="border-bottom:solid 1px #000;width:12px;">:</td><td style="border-bottom:solid 1px #000;width:200px;font-size:1.2em;font-weight:bold;text-align:center">'.$obj->formatDBDate($rs[0]['thcdate'],'', array('returnOnEmpty'=>true, 'value' => '')).'</td></tr>
    <tr><td style="border-bottom:solid 1px #000;width:110px;">Lift Off dari trucking </td><td style="border-bottom:solid 1px #000;width:12px;">:</td><td style="border-bottom:solid 1px #000;width:200px;font-size:1.2em;font-weight:bold;text-align:center">'.$obj->formatDBDate($rs[0]['liftondate'],'', array('returnOnEmpty'=>true, 'value' => '')).'</td></tr>
    <tr><td style="border-bottom:solid 1px #000;width:110px;">Tgl. Pengambilan Empty</td><td style="border-bottom:solid 1px #000;width:12px;">:</td><td style="border-bottom:solid 1px #000;width:200px;font-size:1.2em;font-weight:bold;text-align:center"></td></tr>
    <tr><td style="border-bottom:solid 1px #000;width:110px;">Penyerahan NPE dari CS</td><td style="border-bottom:solid 1px #000;width:12px;">:</td><td style="border-bottom:solid 1px #000;width:200px;font-size:1.2em;font-weight:bold;text-align:center">'.$obj->formatDBDate($rs[0]['deliverynpedate'],'', array('returnOnEmpty'=>true, 'value' => '')).'</td></tr>
    <tr><td style="border-bottom:solid 1px #000;width:110px;">Tgl Penerbitan KARTU EKSPOR</td><td style="border-bottom:solid 1px #000;width:12px;">:</td><td style="border-bottom:solid 1px #000;width:200px;font-size:1.2em;font-weight:bold;text-align:center">'.$obj->formatDBDate($rs[0]['exportcarddate'],'', array('returnOnEmpty'=>true, 'value' => '')).'</td></tr>
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
<div style="clear:both"></div>

<table cellpadding="2" style="border:solid 1px black">
    <tr><td style="border-bottom:solid 1px #000;width:110px;">Nama Barang</td><td style="border-bottom:solid 1px #000;width:12px;">:</td><td style="border-bottom:solid 1px #000;width:542px;font-size:1.2em;font-weight:bold;text-align:center">'.$rs[0]['itemdescription'].'</td></tr>
    <tr><td style="border-bottom:solid 1px #000;width:110px;">'.$obj->lang['note'].'</td><td style="border-bottom:solid 1px #000;width:12px;">:</td><td style="border-bottom:solid 1px #000;width:542x">'.$trNotes.'</td></tr>
</table>

';
    

// $html .= $obj->generateSignLabel($rs); 

    
$html  = '<div style="font-size:0.9em">'.$html.'</div>';    
return $html;
}

?>
