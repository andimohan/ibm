<?php 

$pdf->setCustomSettings(
    array(
         'showPrintHeader' => false,
         'paperSetting' => 'A4',
         ) 
);

$generateReportContent = function ($dataset){ 
global $pdf;

$obj = new EMKLJobOrderHeader();  
    
$service = new Service(SERVICE);
$employee = new Employee();
$supplier = new Supplier();
$itemChecklist = new ItemChecklist();
$vessel = new Vessel();
$terminal = new Terminal();

$rs = $dataset['rs'];  
$rsVendor = $supplier->getDataRowById($rs[0]['truckingkey']);   
 
$vesselName = '';
if(!empty($rs[0]['vesselkey'])){
    $rsVessel = $vessel->getDataRowById($rs[0]['vesselkey']);
    $vesselName = $rsVessel[0]['name'];
    
    if (!empty( $rs[0]['vesselnumber']))
        $vesselName .= ' ' .$rs[0]['vesselnumber'];  
}
  
$terminalName = '';
if (!empty($rs[0]['terminalkey'])){
    $rsTerminal = $terminal->getDataRowById($rs[0]['terminalkey']); 
    $terminalName = $rsTerminal[0]['name'];
}

$rsDetail = $obj->getDetailWithRelatedInformation($rs[0]['pkey']);
$customerName = $rs[0]['customername'];
$rsItemCheckList = $itemChecklist->searchData($itemChecklist->tableName.'.statuskey',1,true);
$rsItemCheckList = array_column($rsItemCheckList,'name');
    
$rsJobType = $obj->getEmklType($rs[0]['loadcontainertypekey']);
        
if($rs[0]['loadcontainertypekey']==EMKL['emklType']['fcl'] || $rs[0]['loadcontainertypekey']==EMKL['emklType']['trucking']){ 
    $volume = '<table>';
    for($i=0;$i<count($rsDetail);$i++) 
        $volume .= '<tr><td>'.$obj->formatNumber($rsDetail[$i]['qty']).'x '.$rsDetail[$i]['itemname'].'</td></tr>'; 
    $volume .= '</table>';
}

if(  in_array( $rs[0]['loadcontainertypekey'] , array(EMKL['emklType']['lcl'],EMKL['emklType']['lclnc'])) )
  $volume =   $obj->formatNumber($rs[0]['volume'],2);    

$trNotes = '<div style="clear:both"></div><strong>'.$obj->lang['note'].' :</strong> <br>';    
$trNotes .= (!empty($rs[0]['trdesc'])) ?   str_replace(chr(13),'<br>',$rs[0]['trdesc']) : '<div style="clear:both; height: 5px"></div>';

$containerNumber = explode(chr(13),$rs[0]['containernumber']);
$totalCol = 4;
$containerNumber = array_chunk($containerNumber,$totalCol);
     
$containerNumberTable = '<div style="clear:both"></div><strong>'.ucwords($obj->lang['container']).'/'. ucwords($obj->lang['seal']).' :</strong> <br>';
if(!empty($rs[0]['containernumber'])){
    $containerNumberTable .= '<table  cellpadding="4">';
    foreach($containerNumber as $row){
        $containerNumberTable .= '<tr>';
        for($i=0; $i<$totalCol; $i++) 
            $containerNumberTable .= (isset($row[$i])) ? '<td style="width:170px; text-align:center; border:1px solid #999;">'.$row[$i].'</td>': '<td style="width:170px;border:1px solid #999;">&nbsp;</td>' ;
        $containerNumberTable .= '</tr>';
    }  
    $containerNumberTable .= '</table>';
}else{
    $containerNumberTable .= '<div style="clear:both; height: 5px"></div>';
}
 
$html = $obj->printSetting['defaultStyle'];
$html .= '
<table cellpadding="4"><tr><td style="width:100px;">'.$obj->lang['bookingNumber'].'</td><td style="width:12px;">:</td><td style="width:300px;font-size:1.2em;font-weight:bold">'.$rs[0]['bookingnumber'].'</td><td style="width:100px;">'.$obj->lang['trucking'].'</td><td style="width:12px;">:</td><td style="width:155px;">'.$rsVendor[0]['name'].'</td></tr>
</table>';

$html .= '<table cellpadding="4" style="border:solid 1px black"> 
<tr><td style="border-bottom:solid 1px #000;width:100px;">'.$obj->lang['jobOrderNumber'].'</td><td style="border-bottom:solid 1px #000;width:12px;">:</td><td style="border-bottom:solid 1px #000;width:300px;font-size:1.2em;font-weight:bold">'.$rs[0]['code'].'</td><td style="border-bottom:solid 1px #000;width:100px;">'.$obj->lang['etd'].'</td><td style="border-bottom:solid 1px #000;width:12px;">:</td><td style="border-bottom:solid 1px #000;width:155px;">'.$obj->formatDBDate($rs[0]['etdpol'],'', array('returnOnEmpty'=>true)).'</td></tr>
<tr><td style="border-bottom:solid 1px #000;width:100px;">'.$obj->lang['date'].'</td><td style="border-bottom:solid 1px #000;width:12px;">:</td><td style="border-bottom:solid 1px #000;width:300px;">'.$obj->formatDBDate($rs[0]['trdate'],'', array('returnOnEmpty'=>true)).'</td><td style="border-bottom:solid 1px #000;width:100px;">'.$obj->lang['eta'].'</td><td style="border-bottom:solid 1px #000;">:</td><td style="border-bottom:solid 1px #000;width:155px;">'.$obj->formatDBDate($rs[0]['etapod'],'', array('returnOnEmpty'=>true)).'</td></tr>
<tr><td style="border-bottom:solid 1px #000;">'.$obj->lang['jobType'].'</td><td style="border-bottom:solid 1px #000;">:</td><td style="border-bottom:solid 1px #000;font-weight:bold">'.strtoupper($rsJobType[0]['name']).'</td><td style="border-bottom:solid 1px #000;">AJU</td><td style="border-bottom:solid 1px #000;">:</td><td style="border-bottom:solid 1px #000;">'.$rs[0]['aju'].'</td></tr>
<tr><td style="border-bottom:solid 1px #000;">'.$obj->lang['shipper'].'</td><td style="border-bottom:solid 1px #000;">:</td><td style="border-bottom:solid 1px #000;">'.$customerName.'</td><td style="border-bottom:solid 1px #000;">'.(( $rs[0]['jobtypekey'] == EMKL['jobType']['import']) ? 'PIB' : 'PEB').'</td><td style="border-bottom:solid 1px #000;">:</td><td style="border-bottom:solid 1px #000;">'.$rs[0]['peb'].'</td>';


$html .= '</tr>
<tr><td style="border-bottom:solid 1px #000;">'.$obj->lang['pol'].'/'.$obj->lang['pod'].'</td><td style="border-bottom:solid 1px #000;">:</td><td style="border-bottom:solid 1px #000;">'.$rs[0]['polname'].' - '.$rs[0]['podname'].'</td><td style="border-bottom:solid 1px #000;">'.$obj->lang['invoiceNumber'].'</td><td style="border-bottom:solid 1px #000;">:</td><td style="border-bottom:solid 1px #000;">'.$rs[0]['invoicenumber'].'</td></tr>
<tr><td style="border-bottom:solid 1px #000;">'.$obj->lang['carrier'].'</td><td style="border-bottom:solid 1px #000;">:</td><td style="border-bottom:solid 1px #000;">'.$rs[0]['carriername'].'</td><td style="border-bottom:solid 1px #000;">'.$obj->lang['stuffing'].' '.$obj->lang['location'].'</td><td style="border-bottom:solid 1px #000;">:</td><td style="border-bottom:solid 1px #000;">'.$rs[0]['stuffing'].'</td></tr>
<tr><td style="border-bottom:solid 1px #000;">'.$obj->lang['volume'].'</td><td style="border-bottom:solid 1px #000;">:</td><td style="border-bottom:solid 1px #000;">'.$volume.'</td><td style="border-bottom:solid 1px #000;">'.$obj->lang['temperature'].'</td><td style="border-bottom:solid 1px #000;">:</td><td style="border-bottom:solid 1px #000;">'.$obj->formatNumber($rs[0]['temperature'],-2).'</td></tr>
<tr><td style="border-bottom:solid 1px #000;">'.$obj->lang['vessel'].'</td><td style="border-bottom:solid 1px #000;">:</td><td style="border-bottom:solid 1px #000;">'.$vesselName.'</td><td style="border-bottom:solid 1px #000;">'.$obj->lang['stuffingIn'].'</td><td style="border-bottom:solid 1px #000;">:</td><td style="border-bottom:solid 1px #000;">'.$obj->formatDBDate($rs[0]['stuffingin'],'', array('returnOnEmpty'=>true)).'</td></tr>
<tr><td style="border-bottom:solid 1px #000;">'.$obj->lang['stackArea'].'</td><td style="border-bottom:solid 1px #000;">:</td><td style="border-bottom:solid 1px #000;">'.$terminalName.'</td><td style="border-bottom:solid 1px #000;">'.$obj->lang['stuffingOut'].'</td><td style="border-bottom:solid 1px #000;">:</td><td style="border-bottom:solid 1px #000;">'.$obj->formatDBDate($rs[0]['stuffingout'],'', array('returnOnEmpty'=>true)).'</td></tr>
<tr><td style="border-bottom:solid 1px #000;">'.$obj->lang['closing'].'</td><td style="border-bottom:solid 1px #000;">:</td><td  style="border-bottom:solid 1px #000;">'.$obj->formatDBDate($rs[0]['closingdate'],'d / m / Y H:i', array('returnOnEmpty'=>true)).'</td><td style="border-bottom:solid 1px #000;"></td><td style="border-bottom:solid 1px #000;"></td><td style="border-bottom:solid 1px #000;"></td></tr>
</table> 
';
 
$tabelChecklist = '<div  style="clear:both"></div><table cellpadding="4">'; 
$totalCol = 5;
$arrItemCheckList = array_chunk($rsItemCheckList,$totalCol);
foreach($arrItemCheckList as $row){
    $tabelChecklist .='<tr>'; 
    for($i=0; $i<$totalCol; $i++) 
        $tabelChecklist .= (isset($row[$i])) ? '<td style="border:solid 1px black;width:135px; text-align:center; ">'.$row[$i].'</td>': '<td style="border:solid 1px black;width:135px;">&nbsp;</td>' ;
   
    $tabelChecklist .='</tr>';
}
$tabelChecklist .= '</table>'; 


$html .= $containerNumberTable;    
$html .= $trNotes;
$html .= $tabelChecklist;
//$html .= '<div style="clear:both"></div>';

//$html .= '<div style="clear:both"></div>';
//$html .= $obj->generateSignLabel($rs); 
return $html;
}

?>