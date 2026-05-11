<?php 
 $pdf->setCustomSettings(
    array(  
         'paperSetting' => 'A5,L', 
         'showPrintHeader' => false,
         ) 
);  


$woContent = function ($dataset){ 

$obj = new TruckingServiceWorkOrder();  
$truckingServiceOrder = new TruckingServiceOrder();
$truckingServiceOrderCategory = new TruckingServiceOrderCategory();
$location = new Location();
$service = new Service();
$employee = new Employee();
$supplier = new Supplier();
    
$rs = $dataset['rs']; 
        
$rsDetail = $truckingServiceOrder->getDetailByColumn('pkey',$rs[0]['refdetailkey']); 
$rsService = $service->getDataRowById($rsDetail[0]['itemkey']);      
     
if($rs[0]['isoutsource']){
    $rsSupplier = $supplier->getDataRowById($rs[0]['supplierkey']);
    $driverName = $rsSupplier[0]['name']; 
}else{
    $driverName = $rs[0]['drivername'];
} 
 
$timeformat = ($obj->formatDBDate($rs[0]['stuffingdatetime'],'H:i') == "00:00") ? 'd / m / Y' : 'd / m / Y H:i';     
    
$trnotes = (!empty($rs[0]['trdesc'])) ? '<div style="clear:both"></div><strong>Catatan :</strong> <br>' . str_replace(chr(13),'<br>',$rs[0]['trdesc']) : '';     
    
$container = $rs[0]['containernumber'];
if (!empty($rs[0]['container2number'])) $container .= ', ' . $rs[0]['container2number']; 
    
$productDesc = str_replace(chr(13),'<br>',$rs[0]['productdesc']);
    
$seal = $rs[0]['sealnumber'];
if (!empty($rs[0]['seal2number'])) $seal .= ', ' . $rs[0]['seal2number'];
    
$depotname = (!empty($rs[0]['depotname'])) ? $rs[0]['depotname'] : ' - ';
$terminalname = (!empty($rs[0]['terminalname'])) ? $rs[0]['terminalname'] : ' - ';
    
$jobInformation = array();
array_push($jobInformation,$rs[0]['cargotype']);
array_push($jobInformation,$rs[0]['categoryname']); 
    
// kalo tipe pekerjaan cuma 1, gk perlu tampilin
$rsJobDetail = $truckingServiceOrderCategory->getDetailById($rs[0]['categorykey']);
if (count($rsJobDetail) > 1)
    array_push($jobInformation,$rs[0]['jobtypename']);

$jobInformation = implode(', ' ,$jobInformation);
        
$recipientName = (!empty($rs[0]['consigneename'])) ? $rs[0]['consigneename']  : $rs[0]['customername'];
$rsLocation = $location->getDataRowById($rs[0]['locationkey']);
$locationname = (isset($rsLocation[0]['name'])) ? $rsLocation[0]['name'] : '';
    
$html = $obj->printSetting['defaultStyle'];
$html .= ' 
<style>
    .sign .sign-col {height:50px;}
</style>
<table cellpadding="2" > 
<tr><td><div class="title">SURAT PERINTAH KERJA</div></td></tr>
<tr><td><div class="subtitle">'.$rs[0]['code'].'</div></td></tr>
</table> 

<div style="clear:both"></div>
<table>
<tr>
<td>
<table cellpadding="2"> 
<tr><td class="header-row-header" style="width:140px">Tanggal</td><td style="width:10px; text-align:center">:</td><td style="width:170px">'.$obj->formatDBDate($rs[0]['trdate'],'d / m / Y').'</td></tr>  
<tr><td class="header-row-header" style="width:140px">Tgl. Stuffing / Bongkar</td><td style="text-align:center">:</td><td>'.$obj->formatDBDate($rs[0]['stuffingdatetime'],$timeformat).'</td></tr>  
<tr><td class="header-row-header" style="width:140px">No. Order</td><td style="text-align:center">:</td><td>'. $rs[0]['serviceordercode'] .'</td></tr> 
<tr><td class="header-row-header" style="width:140px">S / I</td><td style="text-align:center">:</td><td>'. $rs[0]['donumber'] .'</td></tr>  
<tr><td  colspan="3"></td></tr> 
</table>
<table>
<tr><td style="font-weight:bold">No. Mobil</td><td style="font-weight:bold">No. Chasis</td><td style="font-weight:bold">Sopir</td></tr>  
<tr><td>'. $rs[0]['policenumber'] .'</td><td>'. $rs[0]['chassisnumber'] .'</td><td>'. $driverName.'</td></tr>  
</table> 
</td>
<td>
<table cellpadding="2" >
<tr><td class="header-row-header" style="width:120px">Kepada Yth</td><td style="width:10px; text-align:center">:</td><td style="width:200px;">'.$recipientName.'</td></tr> 
<tr><td class="header-row-header">Lokasi Stuffing</td><td style="width:10px; text-align:center">:</td><td>'.$locationname.'</td></tr> 
<tr><td class="header-row-header">Pabrik / Gudang</td><td style="width:10px; text-align:center">:</td><td>'.$rs[0]['warehouseconsigneename'].'</td></tr> 
<tr><td class="header-row-header">Alamat</td><td style="width:10px; text-align:center">:</td><td>'.str_replace(chr(13),'<br>',$rs[0]['stuffingaddress']).'</td></tr>  
<tr><td class="header-row-header">Depot / Terminal</td><td style="width:10px; text-align:center">:</td><td>'.$depotname.' / ' .$terminalname.'</td></tr>   
<tr><td class="header-row-header">Rute</td><td style="width:10px; text-align:center">:</td><td>'.$rs[0]['routefrom'].' - ' .$rs[0]['routeto'].'</td></tr>   
</table>
</td>
</tr>
</table>

<div style="clear:both"></div> 
 
<table cellpadding="4" class="table-transaction">';
    
$cellArray = array ();
array_push($cellArray, array('label' => $obj->lang['number'],'width' => '30'));
array_push($cellArray, array('label' => $obj->lang['size'],  'width' => '120'));
array_push($cellArray, array('label' => $obj->lang['container'], 'width' => '140')); 
array_push($cellArray, array('label' => $obj->lang['description'])); 
$html .= $obj->generatePrintTableRow( array('class' => 'col-header','cell' =>  $cellArray));  

$html .= '  
<tr><td style="text-align:right;">1.</td><td>'.$rsService[0]['name'].'</td><td>'.$container.'</td><td>'.$productDesc.'</td></tr>   
</table>  
'.$trnotes.' 
<div style="clear:both"></div>  
';
  
$html .= $obj->generateSignLabel($rs);  
    
return '<div  style="font-size:0.9em">'.$html.'</div>';
}; 


$cashOutContent = function ($dataset){
    
global $pdf;
    
$obj = new TruckingServiceWorkOrder();  
$truckingSeviceOrder = new TruckingServiceOrder();  
$cashOut = new truckingCostCashOut();   
$cost = new Service(TRUCKING_SERVICE,1);
$service = new Service();
$location = new Location();
$employee = new Employee();
$supplier = new Supplier();

$arrHTML = array();
    
$rs = $dataset['rs']; 
        
$rsTableType = $obj->getTableKeyAndObj($obj->tableName); 
$rsCashOut = $cashOut->searchData('','',true,' and '.$cashOut->tableName.'.statuskey = 1 and '.$cashOut->tableName.'.refkey = ' . $obj->oDbCon->paramString($rs[0]['pkey']).' and reftabletype = ' . $obj->oDbCon->paramString($rsTableType['key']) );
     
if (empty($rsCashOut)) return '';
  
    
foreach($rsCashOut as $cashOutRow){

    $rsTruckingCost =  $cashOut->getDetailWithRelatedInformation($cashOutRow['pkey']);  

    if (empty($rsTruckingCost))  continue; 

    //$rsCost = $cost->searchData($cost->tableName.'.statuskey', 1, true, '', ' order by fixedcost desc, name asc');
    $rsService = $service->getDataRowById($rs[0]['itemkey']);      

    $rsLocation = $location->getDataRowById($rs[0]['locationkey']);
    $locationname = (isset($rsLocation[0]['name'])) ? $rsLocation[0]['name'] : '';

    $timeformat = ($obj->formatDBDate($rs[0]['stuffingdatetime'],'H:i') == "00:00") ? 'd / m / Y' : 'd / m / Y H:i';      

    $depotname = (!empty($rs[0]['depotname'])) ? $rs[0]['depotname'] : ' - ';
    $terminalname = (!empty($rs[0]['terminalname'])) ? $rs[0]['terminalname'] : ' - ';

    $recipientName = $cashOutRow['employeename']; 

    if($rs[0]['isoutsource']){
        $rsSupplier = $supplier->getDataRowById($rs[0]['supplierkey']);
        $driverName = $rsSupplier[0]['name']; 
    }else{
        $driverName = $rs[0]['drivername'];
    } 
        
    $carRegistrationNumber = (!empty($rs[0]['policenumber'])) ? ' ('.$rs[0]['policenumber'].')' : '';
        
        
    $customerName = (!empty($rs[0]['consigneename'])) ? $rs[0]['consigneename']  : $rs[0]['customername'];
    $party = $truckingSeviceOrder->getPartyDescription($rs[0]['refkey']);
    
    $html = $obj->printSetting['defaultStyle'];
    $html .= '
<style>
    .sign .sign-col {height:50px;}
</style>
    <table cellpadding="2" > 
    <tr><td><div class="title">VOUCHER PENGELUARAN KAS</div></td></tr>
    <tr><td><div class="subtitle">'.$cashOutRow['code'].' / '.$rs[0]['code'].'</div></td></tr>
    </table> 

    <div style="clear:both"></div>
    <table>
    <tr>
    <td style="width:300px;" >
    <table cellpadding="2"> 
    <tr><td class="header-row-header"  style="width:120px">Tanggal</td><td style="width:10px; text-align:center">:</td><td style="width:170px">'.$obj->formatDBDate($cashOutRow['trdate'],'d / m / Y').'</td></tr>  
    <tr><td class="header-row-header">Tgl. Stuffing</td><td style="width:10px; text-align:center">:</td><td>'.$obj->formatDBDate($rs[0]['stuffingdatetime'],$timeformat).'</td></tr>  
    <tr><td class="header-row-header">No. Order</td><td style="width:10px; text-align:center">:</td><td>'. $rs[0]['serviceordercode'] .'</td></tr> 
    <tr><td class="header-row-header">S / I</td><td style="width:10px; text-align:center">:</td><td>'. $rs[0]['donumber'] .'</td></tr>  
    <tr><td class="header-row-header">Penerima</td><td style="width:10px; text-align:center">:</td><td>'. $recipientName.'</td></tr>   
    <tr><td class="header-row-header">Sopir</td><td style="width:10px; text-align:center">:</td><td>'. $driverName.$carRegistrationNumber.'</td></tr>   
    </table> 
    </td>
    <td style="width:370px;">
    <table cellpadding="2" >
    <tr><td class="header-row-header" style="width:120px">'.$obj->lang['shipper'].' / '.$obj->lang['consignee'].'</td><td style="width:10px; text-align:center">:</td><td style="width:240px;">'.$customerName.'</td></tr> 
    <tr><td class="header-row-header">Lokasi Stuffing</td><td style="width:10px; text-align:center">:</td><td>'.$locationname.'</td></tr> 
    <tr><td class="header-row-header">Depot / Terminal</td><td style="width:10px; text-align:center">:</td><td>'.$depotname.' / '.$terminalname.'</td></tr>   
    <tr><td class="header-row-header">Rute</td><td style="width:10px; text-align:center">:</td><td>'.$rs[0]['routefrom'].' - ' .$rs[0]['routeto'].'</td></tr>   
    <tr><td class="header-row-header">Partai</td><td style="width:10px; text-align:center">:</td><td>'.$party.'</td></tr>   
    </table>
    </td>
    </tr>
    </table>
    <div style="clear:both"></div>  
    ';

    $html .= '<table cellpadding="4" style="width:670px"  class="table-transaction">';
    $html .= '
    <tr class="col-header">
    <td style="width:30px; text-align:right">No.</td>
    <td style="width:240px" >Keterangan</td>
    <td style="width:100px" >Size</td>
    <td style="width:100px" >No. Container</td>
    <td style="width:200px; text-align:right" >Biaya</td>
    </tr>';

    $totalCost = 0;
    $ctr = 0;
    for($i=0;$i<count($rsTruckingCost);$i++){

        //$rsCostValue = $obj->getCostDetail($rs[0]['pkey'], $rsCost[$i]['pkey']);
        //$cost = (!empty($rsCostValue)) ? $rsCostValue[0]['requestamount'] : 0;
        $cost = $rsTruckingCost[$i]['amount'];
        $totalCost += $cost;

       if ($cost == 0)
           continue;

        $html .= '
        <tr>
        <td style="text-align:right">'.(++$ctr).'.</td>
        <td>'.$rsTruckingCost[$i]['costname'].'</td>
        <td>'.$rsService[0]['name'].'</td>
        <td></td>
        <td style="text-align:right" >'.$obj->formatNumber($cost).'</td>
        </tr>';
    }

    $sayNumber = $obj->sayNumber($totalCost);    

    $html .= '</table>'; 

    $html .= ' 
    <table cellpadding="4"> 
    <tr><td style="width:460px"><strong>Terbilang</strong> :<br>'.ucwords($sayNumber).' Rupiah.</td><td style="text-align:right; font-weight:bold;  width:100px; ">Total</td><td style="text-align:right; font-weight:bold;  width:110px;"  >'.$obj->formatNumber($totalCost).'</td></tr>
    </table>
    ';  

    $html .= '<div style="clear:both"></div>'; 

    $html .= $obj->generateSignLabel($rs);  
    
    $html = '<div  style="font-size:0.9em">'.$html.'</div>';
    array_push($arrHTML,$html);
}
    
return $arrHTML;
};
   

$generateReportContent = array();
array_push($generateReportContent , $woContent);
array_push($generateReportContent , array('content' => $cashOutContent, 'newGroup' => true));

?>
