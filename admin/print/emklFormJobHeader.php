<?php 
includeClass('EMKLJobOrderHeader.class.php');
$emklJobOrderHeader = createObjAndAddToCol(new EMKLJobOrderHeader());

$PRINT_SETTINGS =  array(   
         'showPrintHeader' => false,
         'showPrintFooter' => false,
         );


$obj = $emklJobOrderHeader;

$generateReportContent = function ($dataset){ 
global $pdf; 
    
$obj = new EMKLJobOrderHeader();      
$supplier = new Supplier();
$vessel = new Vessel();
$employee = new Employee();

$rs = $dataset['rs'];    
//$companyName = $obj->loadSetting('companyName'); 
$customerName = $rs[0]['customername'];     
$rsDetail = $obj->getDetailWithRelatedInformation($rs[0]['pkey']);
$rsTrucking = $supplier->getDataRowById($rs[0]['truckingkey']);
$truckingName = (!empty($rsTrucking)) ? $rsTrucking[0]['name'] : '';
$salesmanName = $rs[0]['salesname']; 
    
//$rsJobType = $obj->getEmklType($rs[0]['loadcontainertypekey']);
    
if($rs[0]['loadcontainertypekey']==EMKL['emklType']['fcl'] || $rs[0]['loadcontainertypekey']==EMKL['emklType']['trucking']){ 
    $volume = array();
    for($i=0;$i<count($rsDetail);$i++) 
        array_push($volume,$obj->formatNumber($rsDetail[$i]['qty']).'x '.$rsDetail[$i]['itemname']);
    
    $volume = implode(', ', $volume); 
}

	
if(  in_array( $rs[0]['loadcontainertypekey'] , array(EMKL['emklType']['lcl'],EMKL['emklType']['lclnc'])) ) 
  $volume =   $obj->formatNumber($rs[0]['volume'],2);  
 
$containerNumber = str_replace(chr(13),', ',$rs[0]['containernumber']); 
    
$vesselName = '';
if(!empty($rs[0]['vesselkey'])){
    $rsVessel = $vessel->getDataRowById($rs[0]['vesselkey']);
    $vesselName = $rsVessel[0]['name'];
    
    if (!empty( $rs[0]['vesselnumber']))
        $vesselName .= ' ' .$rs[0]['vesselnumber'];  
} 
     
$trNotes = (!empty($rs[0]['trdesc'])) ?   str_replace(chr(13),', ',$rs[0]['trdesc']) : '';
    
    
$html = $obj->printSetting['defaultStyle'];
/*$html .= ' 
<table> 
<tr><td><div class="title">'.strtoupper($companyName).'</div></td></tr>
</table> 
<div style="clear:both"></div>';*/
$html .= '
<style>
.header-row-header{width:100px; font-weight:bold}
</style>
<div style="font-size:0.9em;">
<table style="width: 670px;">
<tr>
<td style="width:330px">
<table cellpadding="2" >
<tr><td class="header-row-header">'.$obj->lang['jobOrder'].'</td><td style="width:10px; text-align:center">:</td><td style="width:220px">'.$rs[0]['code'].'</td></tr>
<tr><td class="header-row-header">'.$obj->lang['shipper'].' / CNEE</td><td style="text-align:center">:</td><td>'.$customerName.'</td></tr>
<tr><td class="header-row-header">'.$obj->lang['pol'].' / '.$obj->lang['pod'].'</td><td style="text-align:center">:</td><td>'.$rs[0]['polname'].' - '.$rs[0]['podname'].'</td></tr>
<tr><td class="header-row-header">'.$obj->lang['volume'].'</td><td style="text-align:center">:</td><td>'.$volume.'</td></tr>
<tr><td class="header-row-header">'.$obj->lang['freightTerm'].'</td><td style="text-align:center">:</td><td>Prepaid / Collect</td></tr>
<tr><td class="header-row-header">'.$obj->lang['trucking'].'</td><td style="text-align:center">:</td><td>'.$truckingName.'</td></tr>
<tr><td class="header-row-header">'.$obj->lang['stuffing'].'</td><td style="text-align:center">:</td><td>'.$rs[0]['stuffing'].'</td></tr>
<tr><td class="header-row-header">'.ucwords($obj->lang['in']).' - '.ucwords($obj->lang['out']).'</td><td style="text-align:center">:</td><td>'. $obj->formatDBDate($rs[0]['stuffingin']) .' &nbsp;-&nbsp; '. $obj->formatDBDate($rs[0]['stuffingout']) .'</td></tr>
</table>
</td>
<td style="width:8px;"></td>
<td style="width:330px">
<table cellpadding="2" >
<tr><td class="header-row-header">'.$obj->lang['carrier'].'</td><td style="width:10px; text-align:center">:</td><td style="width:220px">'.$rs[0]['carriername'].'</td></tr> 
<tr><td class="header-row-header">MBL / HBL No.</td><td style="text-align:center">:</td><td >'. $rs[0]['bookingnumber'] .'</td></tr>
<tr><td class="header-row-header">Feeder, M/Vessel</td><td style="text-align:center">:</td><td>'.$vesselName.'</td></tr>
<tr><td class="header-row-header">'.$obj->lang['etd'].' - '.$obj->lang['eta'].'</td><td style="text-align:center">:</td><td>'. $obj->formatDBDate($rs[0]['etdpol']) .' &nbsp;-&nbsp; '. $obj->formatDBDate($rs[0]['etapod']) .'</td></tr>
<tr><td class="header-row-header">AJU</td><td style="text-align:center">:</td><td>'. $rs[0]['aju'] .'</td></tr>
<tr><td class="header-row-header">'.(( $rs[0]['jobtypekey'] == EMKL['jobType']['import']) ? 'PIB' : 'PEB').'</td><td style="text-align:center">:</td><td>'. $rs[0]['peb'] .'</td></tr>
<tr><td class="header-row-header">'.$obj->lang['invoiceNumber'].'</td><td style="text-align:center">:</td><td>'. $rs[0]['invoicenumber'] .'</td></tr>
<tr><td class="header-row-header">'.$obj->lang['salesman'].'</td><td style="text-align:center">:</td><td>'. $salesmanName.'</td></tr>
</table>
</td>
</tr>
</table>
<div style="clear:both"></div>';
$html .= '<table cellpadding="4" style="font-size:0.9 em;">
<tr class="col-header"><td style="text-align:left;width:170px">Buying Rate</td><td style="text-align:center;width:20px"></td><td style="text-align:center;width:70px">20</td><td style="text-align:center;width:20px"></td><td style="text-align:center;width:30px">Vol</td><td style="text-align:center;width:20px"></td><td style="text-align:center;width:90px">Total</td><td style="text-align:center;width:20px"></td><td style="text-align:center;width:70px">40</td><td style="text-align:center;width:20px"></td><td style="text-align:center;width:30px">Vol</td><td style="text-align:center;width:20px"></td><td style="text-align:center;width:90px">Total</td></tr>';
$qtyBuying=12;
for($i=0;$i<$qtyBuying;$i++)
    $html .= '<tr><td style="border-bottom: 1px solid black;"></td><td>:</td><td style="border-bottom: 1px solid black;"></td><td>X</td><td style="border-bottom: 1px solid black;"></td><td>=</td><td style="border-bottom: 1px solid black;"></td><td></td><td style="border-bottom: 1px solid black;"></td><td style="text-align:center;width:20px">X</td><td style="border-bottom: 1px solid black;"></td><td>=</td><td style="border-bottom: 1px solid black;"></td></tr>  ';

$html .='<tr><td style="text-align:right;"><b>Total Buying</b></td><td></td><td></td><td></td><td></td><td></td><td style="border-bottom: 1px solid black;"></td><td></td><td></td><td></td><td></td><td></td><td style="border-bottom: 1px solid black;"></td></tr>';
$html .= '</table>';
    
$html .= '<div style="clear:both"></div><br><table cellpadding="4" style="font-size:0.9 em;">
<tr class="col-header"><td style="text-align:left;width:170px">Selling Rate</td><td style="text-align:center;width:20px"></td><td style="text-align:center;width:70px">20</td><td style="text-align:center;width:20px"></td><td style="text-align:center;width:30px">Vol</td><td style="text-align:center;width:20px"></td><td style="text-align:center;width:90px">Total</td><td style="text-align:center;width:20px"></td><td style="text-align:center;width:70px">40</td><td style="text-align:center;width:20px"></td><td style="text-align:center;width:30px">Vol</td><td style="text-align:center;width:20px"></td><td style="text-align:center;width:90px">Total</td></tr>';
$qtySelling=9;
for($i=0;$i<$qtySelling;$i++)
    $html .= '<tr><td style="border-bottom: 1px solid black;"></td><td>:</td><td style="border-bottom: 1px solid black;"></td><td>X</td><td style="border-bottom: 1px solid black;"></td><td>=</td><td style="border-bottom: 1px solid black;"></td><td></td><td style="border-bottom: 1px solid black;"></td><td style="text-align:center;width:20px">X</td><td style="border-bottom: 1px solid black;"></td><td>=</td><td style="border-bottom: 1px solid black;"></td></tr>  ';

$html .='<tr><td style="text-align:right;"><b>Total Selling</b></td><td></td><td></td><td></td><td></td><td></td><td style="border-bottom: 1px solid black;"></td><td></td><td></td><td></td><td></td><td></td><td style="border-bottom: 1px solid black;"></td></tr>';
$html .= '</table><div style="clear:both"></div>';
    
$html .= '
<table>
<tr>
<td style="width:250px;">
<table cellpadding="2">
<tr><td class="header-row-header">'.$obj->lang['commission'].'</td><td style="width:10px; text-align:center"></td><td style="width:150px"></td></tr>
<tr><td class="header-row-header">'.$obj->lang['shipper'].'</td><td style="text-align:center">:</td><td style="border-bottom: 1px solid black;"></td></tr>
<tr><td class="header-row-header">'.$obj->lang['carrier'].'</td><td style="text-align:center">:</td><td style="border-bottom: 1px solid black;"></td></tr>
<tr><td class="header-row-header">'.$obj->lang['agent'].'</td><td style="text-align:center">:</td><td style="border-bottom: 1px solid black;"></td></tr>
<tr><td class="header-row-header">'.$obj->lang['creditTerm'].'</td><td style="text-align:center">:</td><td style="border-bottom: 1px solid black;"></td></tr>
</table>
</td>
<td style="width:75px;"></td>
<td style="width:360px;"> 
<table cellpadding="2">
<tr><td class="header-row-header">'.$obj->lang['grossValue'].'</td><td style="width:10px; text-align:center">:</td><td style="width:230px;border-bottom: 1px solid black;"></td></tr> 
<tr><td class="header-row-header"></td><td style="text-align:center"></td><td></td></tr>
<tr><td class="header-row-header"></td><td style="text-align:center"></td><td></td></tr>
<tr><td class="header-row-header"></td><td style="text-align:center"></td><td></td></tr>
<tr><td class="header-row-header">'.$obj->lang['netValue'].'</td><td style="text-align:center">:</td><td style="border-bottom: 1px solid black;"></td></tr>
</table>
</td>
</tr>
</table>
<div style="clear:both"></div>
<div style="clear:both"><b>Remarks</b></div>';
    
if(!empty($containerNumber))
    $html .= '<br>'.$containerNumber;
    
if(!empty($trNotes))
    $html .= '<br>'.$trNotes;

$html .= '<br>
<table cellpadding="4">
<tr><td style="border-bottom: 1px solid black;"></td></tr>
<tr><td style="border-bottom: 1px solid black;"></td></tr>
<tr><td style="border-bottom: 1px solid black;"></td></tr>
</table>
<div style="clear:both"></div>';
    
$html .= '<table cellpadding="4">
<tr><td><b>Prepared By</b></td><td></td><td><b>Checked By</b></td><td></td><td><b>Approved</b></td></tr>
</table>
</div>
';
 
return $html;
}

?>
