<?php 
$pdf->setCustomSettings(
    array( 
         'paperSetting' => 'A5,L',
         'showPrintHeader' => false,
         'showPrintFooter' => false,
         'footer' => '',  
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
$depot = new Depot();

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
$rsDetailContainer = $obj->getDetailContainer($rs[0]['pkey']);
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

if( in_array($rs[0]['loadcontainertypekey'], array(EMKL['emklType']['lcl'], EMKL['emklType']['lclnc']))) 
  $volume =   $obj->formatNumber($rs[0]['volume'],2);    

$trNotes = '<div style="clear:both"></div><strong>'.$obj->lang['note'].' :</strong> <br>';    
$trNotes .= (!empty($rs[0]['trdesc'])) ?   str_replace(chr(13),'<br>',$rs[0]['trdesc']) : '<div style="clear:both; height: 5px"></div>';

    $depotName = '';
        
     if (!empty($rs[0]['depotkey'])){
        $rsDepot = $depot->getDataRowById($rs[0]['depotkey']); 
        $depotName = $rsDepot[0]['name'];
    }
    
$containerNumberTable = '';
if($rs[0]['jobtypekey'] == 1){
    $labelCustomer = $obj->lang['importir'].' / '.$obj->lang['consignee'];
    $labelShipper = $obj->lang['shipper'];
    $labelStuffing = 'Stripping';

       
    $tbExportImport .= '
    <tr><td style="border-bottom:solid 1px #000;">'.$obj->lang['volume'].'</td><td style="border-bottom:solid 1px #000;">:</td><td style="border-bottom:solid 1px #000;font-weight:bold;">'.$volume.'</td><td style="border-bottom:solid 1px #000;">'.$obj->lang['terminal'].'</td><td style="border-bottom:solid 1px #000;">:</td><td style="border-bottom:solid 1px #000;font-weight:bold;">'.$terminalName.'</td></tr>
    <tr><td style="border-bottom:solid 1px #000;">AJU PIB</td><td style="border-bottom:solid 1px #000;">:</td><td style="border-bottom:solid 1px #000;font-weight:bold;">'.$rs[0]['aju'].'</td><td style="border-bottom:solid 1px #000;">'.$labelStuffing.'</td><td style="border-bottom:solid 1px #000;">:</td><td style="border-bottom:solid 1px #000;font-size:1.2em;font-weight:bold;">'.$rs[0]['stuffing'].'</td></tr>
    <tr><td style="border-bottom:solid 1px #000;">'.$obj->lang['planningSPPB'].'</td><td style="border-bottom:solid 1px #000;">:</td><td style="border-bottom:solid 1px #000;font-weight:bold;">'.$obj->formatDBDate($rs[0]['truckingplanningdate'],'d / m / Y H:i', array('returnOnEmpty'=>true)).'</td><td style="border-bottom:solid 1px #000;"></td><td style="border-bottom:solid 1px #000;"></td><td style="border-bottom:solid 1px #000;"></td></tr>
    <tr><td style="border-bottom:solid 1px #000;">'.$obj->lang['carrier'].'</td><td style="border-bottom:solid 1px #000;">:</td><td style="border-bottom:solid 1px #000;font-weight:bold;">'.$rs[0]['carriername'].'</td><td style="border-bottom:solid 1px #000;"></td><td style="border-bottom:solid 1px #000;"></td><td style="border-bottom:solid 1px #000;"></td></tr>
    <tr><td style="border-bottom:solid 1px #000;">'.$obj->lang['goodsDescription'].'</td><td style="border-bottom:solid 1px #000;">:</td><td  style="border-bottom:solid 1px #000;font-weight:bold;">'.$rs[0]['itemdescription'].'</td><td style="border-bottom:solid 1px #000;"></td><td style="border-bottom:solid 1px #000;"></td><td style="border-bottom:solid 1px #000;"></td></tr>
    ';


    $containerNumberTable = '<div style="clear:both"></div><strong>'.ucwords($obj->lang['container']).'/'. ucwords($obj->lang['seal']).' :</strong> <br>';

    if(!empty($rsDetailContainer)){
       $totalCol = 4;
       $containerNumber = array_chunk($rsDetailContainer,$totalCol);
       $containerNumberTable .= '<table  cellpadding="4">';
        foreach($containerNumber as $row){
            $containerNumberTable .= '<tr>';
            for($i=0; $i<$totalCol; $i++) 
                $containerNumberTable .= (isset($row[$i])) ? '<td style="width:170px; text-align:center; border:1px solid #999;">'.$row[$i]['containerno'].' / '.$row[$i]['sealno'].'</td>': '<td style="width:170px;border:1px solid #999;">&nbsp;</td>' ;
            $containerNumberTable .= '</tr>';
        }  
        $containerNumberTable .= '</table>';
    }else{
        $containerNumberTable .= '<div style="clear:both; height: 5px"></div>';
    }    

}else{

    $labelCustomer = $obj->lang['exportir'].' / '.$obj->lang['shipper'];
    $labelShipper = $obj->lang['consignee'];
    $labelStuffing = $obj->lang['location'].' '.$obj->lang['stuffing'] ;
   
    $tbExportImport .= '
    <tr><td style="border-bottom:solid 1px #000;">'.$obj->lang['volume'].'</td><td style="border-bottom:solid 1px #000;">:</td><td style="border-bottom:solid 1px #000;font-weight:bold;">'.$volume.'</td><td style="border-bottom:solid 1px #000;">'.$obj->lang['closingDate'].' </td><td style="border-bottom:solid 1px #000;">:</td><td style="border-bottom:solid 1px #000;font-weight:bold;">'.$obj->formatDBDate($rs[0]['closingdate'],'', array('returnOnEmpty'=>true)).'</td></tr>
    <tr><td style="border-bottom:solid 1px #000;">AJU PEB</td><td style="border-bottom:solid 1px #000;">:</td><td style="border-bottom:solid 1px #000;font-weight:bold;">'.$rs[0]['aju'].'</td><td style="border-bottom:solid 1px #000;">'.$obj->lang['stuffingDate'].'</td><td style="border-bottom:solid 1px #000;">:</td><td style="border-bottom:solid 1px #000;font-weight:bold;">'.$obj->formatDBDate($rs[0]['stuffingin'],'', array('returnOnEmpty'=>true)).'</td></tr>
    <tr><td style="border-bottom:solid 1px #000;">'.$obj->lang['planningSPPB'].'</td><td style="border-bottom:solid 1px #000;">:</td><td style="border-bottom:solid 1px #000;font-weight:bold;">'.$obj->formatDBDate($rs[0]['truckingplanningdate'],'d / m / Y H:i', array('returnOnEmpty'=>true)).'</td><td style="border-bottom:solid 1px #000;">'.$obj->lang['terminal'].'</td><td style="border-bottom:solid 1px #000;">:</td><td style="border-bottom:solid 1px #000;font-weight:bold;">'.$terminalName.'</td></tr>
    <tr><td style="border-bottom:solid 1px #000;">'.$obj->lang['carrier'].'</td><td style="border-bottom:solid 1px #000;">:</td><td style="border-bottom:solid 1px #000;font-weight:bold;">'.$rs[0]['carriername'].'</td><td style="border-bottom:solid 1px #000;">'.$obj->lang['depot'].'</td><td style="border-bottom:solid 1px #000;">:</td><td style="border-bottom:solid 1px #000;font-weight:bold;">'.$depotName.'</td></tr>
    <tr><td style="border-bottom:solid 1px #000;">'.$obj->lang['goodsDescription'].'</td><td style="border-bottom:solid 1px #000;">:</td><td  style="border-bottom:solid 1px #000;font-weight:bold;">'.$rs[0]['itemdescription'].'</td><td style="border-bottom:solid 1px #000;">'.$labelStuffing.'</td><td style="border-bottom:solid 1px #000;">:</td><td style="border-bottom:solid 1px #000;font-weight:bold;">'.$rs[0]['stuffing'].'</td></tr>
    ';
}
    

 
$html = $obj->printSetting['defaultStyle'];
$html .= '';

$html .= '<table cellpadding="4" style="border:solid 1px black"> 
<tr><td style="border-bottom:solid 1px #000;width:100px;">'.$obj->lang['jobOrderNumber'].'</td><td style="border-bottom:solid 1px #000;width:12px;">:</td><td style="border-bottom:solid 1px #000;width:300px;font-weight:bold">'.$rs[0]['code'].'</td><td style="border-bottom:solid 1px #000;width:100px;">'.$obj->lang['mbl'].'</td><td style="border-bottom:solid 1px #000;width:12px;">:</td><td style="border-bottom:solid 1px #000;width:155px;font-weight:bold;">'.$rs[0]['mbl'].'</td></tr>
<tr><td style="border-bottom:solid 1px #000;width:100px;">'.$obj->lang['date'].'</td><td style="border-bottom:solid 1px #000;width:12px;">:</td><td style="border-bottom:solid 1px #000;width:300px;font-weight:bold">'.$obj->formatDBDate($rs[0]['trdate'],'', array('returnOnEmpty'=>true)).'</td><td style="border-bottom:solid 1px #000;width:100px">'.$obj->lang['vessel'].' / '.$obj->lang['voyage'].'</td><td style="border-bottom:solid 1px #000;">:</td><td style="border-bottom:solid 1px #000;width:155px;font-weight:bold;">'.$vesselName.'</td></tr>
<tr><td style="border-bottom:solid 1px #000;">'.$obj->lang['jobType'].'</td><td style="border-bottom:solid 1px #000;">:</td><td style="border-bottom:solid 1px #000;font-weight:bold">'.strtoupper($rsJobType[0]['name']).'</td><td style="border-bottom:solid 1px #000;">'.$obj->lang['etd'].'</td><td style="border-bottom:solid 1px #000;">:</td><td style="border-bottom:solid 1px #000;font-weight:bold;">'.$obj->formatDBDate($rs[0]['etdpol'],'', array('returnOnEmpty'=>true)).'</td></tr>
<tr><td style="border-bottom:solid 1px #000;">'.$labelCustomer.'</td><td style="border-bottom:solid 1px #000;">:</td><td style="border-bottom:solid 1px #000;font-weight:bold;">'.$customerName.'</td><td style="border-bottom:solid 1px #000;">'.$obj->lang['eta'].'</td><td style="border-bottom:solid 1px #000;">:</td><td style="border-bottom:solid 1px #000;font-weight:bold;">'.$obj->formatDBDate($rs[0]['etapod'],'', array('returnOnEmpty'=>true)).'</td></tr>
<tr><td style="border-bottom:solid 1px #000;">'.$labelShipper.'</td><td style="border-bottom:solid 1px #000;">:</td><td style="border-bottom:solid 1px #000;font-weight:bold;">'.$rs[0]['consigneename'].'</td><td style="border-bottom:solid 1px #000;">'.$obj->lang['pol'].'/'.$obj->lang['pod'].'</td><td style="border-bottom:solid 1px #000;">:</td><td style="border-bottom:solid 1px #000;font-weight:bold;">'.$rs[0]['polname'].' - '.$rs[0]['podname'].'</td></tr>

';
 $html .= $tbExportImport;
 $html .= '</table> ';

//$tabelChecklist = '<div  style="clear:both"></div><table cellpadding="4">'; 
//$totalCol = 5;
//$arrItemCheckList = array_chunk($rsItemCheckList,$totalCol);
//foreach($arrItemCheckList as $row){
//    $tabelChecklist .='<tr>'; 
//    for($i=0; $i<$totalCol; $i++) 
//        $tabelChecklist .= (isset($row[$i])) ? '<td style="border:solid 1px black;width:135px; text-align:center; ">'.$row[$i].'</td>': '<td style="border:solid 1px black;width:135px;">&nbsp;</td>' ;
//   
//    $tabelChecklist .='</tr>';
//}
//$tabelChecklist .= '</table>'; 

$html .= $containerNumberTable;    
$html .= $trNotes;
//$html .= $tabelChecklist;
//$html .= '<div style="clear:both"></div>';

//$html .= '<div style="clear:both"></div>';
//$html .= $obj->generateSignLabel($rs); 
return $html;
}

?>