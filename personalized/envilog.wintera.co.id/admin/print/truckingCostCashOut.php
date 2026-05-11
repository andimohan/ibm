<?php

$generateReportContent = function ($dataset){ 

$obj = new TruckingCostCashOut();  
$item = new Item();
$employee = new Employee();
$cashBank = new CashBank();
$truckingServiceWorkOrder = new TruckingServiceWorkOrder();
$truckingServiceOrder = new TruckingServiceOrder();
  
$rs = $dataset['rs']; 
     
$rsDetail = $obj->getDetailById($rs[0]['pkey']); 
$trnotes = (!empty($rs[0]['note'])) ? '<div style="clear:both"></div><strong>Catatan :</strong> <br>' . str_replace(chr(13),'<br>',$rs[0]['trdesc']) : '';
$cashBankCode = '';
if(ADV_FINANCE){
    $arrCode = array();
    for($i=0;$i<count($rsDetail);$i++){
        $cashcode = $cashBank->getCashBankRef($rs[0]['pkey'],$obj->tableName,$rsDetail[$i]['coakey'])['code'];
        //(!empty($cashcode) && !in_array($cashcode,$arrCode)) ? array_push($arrCode,$cashcode) : '';
    }
    //(!empty($arrCode)) ? $cashBankCode = implode(", ",$arrCode):'';
      
}    
    
$rsTableObj = $obj->getTableNameAndObjById($rs[0]['reftabletype']);

$rsObj = $rsTableObj['obj'];
    
$rsTable = $rsObj->searchData($rsObj->tableName.'.pkey',$rs[0]['refkey']);

if(!empty($rsTable[0]['refkey'])){
    $rsJO = $truckingServiceOrder->searchData($truckingServiceOrder->tableName.'.pkey',$rsTable[0]['refkey']);
    $code = $rsJO[0]['code'];
    $donumber = $rsJO[0]['donumber'];
    $shipmentnumber = $rsJO[0]['shipmentnumber'];
    $categoryname = $rsJO[0]['categoryname'];
    $plannerkey = $rsJO[0]['plannerkey'];
    $customerName = $rsJO[0]['customername'];
    $locationName = $rsJO[0]['locationname'];
    $stuffingAdress = $rsJO[0]['stuffingaddress'];
    $depotname = $rsJO[0]['depotname'];
    $terminalname = $rsJO[0]['terminalname'];
    $consigneename = $rsJO[0]['consigneename'];
}else{
    $code = $rsTable[0]['code'];
    $donumber = $rsTable[0]['donumber'];
    $shipmentnumber = $rsTable[0]['shipmentnumber'];
    $categoryname = $rsTable[0]['categoryname'];
    $plannerkey = $rsTable[0]['plannerkey'];
    $customerName = $rsTable[0]['customername'];
    $locationName = $rsTable[0]['locationname'];
    $stuffingAdress = $rsTable[0]['stuffingaddress'];
    $depotname = $rsTable[0]['depotname'];
    $terminalname = $rsTable[0]['terminalname'];
    $consigneename = $rsTable[0]['consigneename'];

}

$rsPlanner = $employee->getDataRowById($plannerkey);  
$plannerName = (!empty($rsPlanner)) ? $rsPlanner[0]['name'] : '';
    
$reciepientname = (!empty($rs[0]['employeename'])) ? $rs[0]['employeename'] : $rsPlanner[0]['name'] ;
//$reciepientname =  $rsPlanner[0]['name'] ;
    
    
$driverName = $rs[0]['employeename'];   
$recipientName = $driverName;
      
$sokey = (!empty($rs[0]['refkey2'])) ? $rs[0]['refkey2'] : $rs[0]['refkey'];
$rsParty= $truckingServiceOrder->getPartyDescription($sokey); 

$html = $obj->printSetting['defaultStyle'];

        $html .= '  
        <table cellpadding="2" > 
        <tr><td><div class="title">VOUCHER PENGELUARAN KAS</div></td></tr>
        <tr><td><div class="subtitle">'.$rs[0]['code'].' / '.$code.'</div></td></tr>
        </table>

    
        <div style="clear:both"></div>

    <table>
    <tr>
    <td style="width:300px;" >
    <table cellpadding="2"> 
    <tr><td class="header-row-header"  style="width:120px">Tanggal</td><td style="width:10px; text-align:center">:</td><td style="width:170px">'.$obj->formatDBDate($rs[0]['trdate'],'d / m / Y').'</td></tr>  
    <tr><td class="header-row-header">No. Order</td><td style="width:10px; text-align:center">:</td><td>'. $code.'</td></tr> 
    <tr><td class="header-row-header">S / I</td><td style="width:10px; text-align:center">:</td><td>'. $donumber .'</td></tr>  
    <tr><td class="header-row-header">Booking Pelayaran</td><td style="width:10px; text-align:center">:</td><td>'. $shipmentnumber .'</td></tr>   
    <tr><td class="header-row-header">Jenis Pekerjaan</td><td style="width:10px; text-align:center">:</td><td>'. $categoryname.'</td></tr>   
    <tr><td class="header-row-header">Penerima</td><td style="width:10px; text-align:center">:</td><td>'. $reciepientname.'</td></tr>   
    <tr><td class="header-row-header">Partai</td><td style="width:10px; text-align:center">:</td><td>'.$rsParty.'</td></tr>   
    </table> 
    </td>
    <td style="width:370px;">
    <table cellpadding="2" >
    <tr><td class="header-row-header" style="width:120px">Pelanggan</td><td style="width:10px; text-align:center">:</td><td style="width:240px;">'.$customerName.'</td></tr> 
    <tr><td class="header-row-header">Consignee</td><td style="text-align:center">:</td><td>'.$consigneename.'</td></tr> 
    <tr><td class="header-row-header">Lokasi Stuffing</td><td style="width:10px; text-align:center">:</td><td>'.$locationName.'</td></tr> 
    <tr><td class="header-row-header">Alamat</td><td style="width:10px; text-align:center">:</td><td>'.str_replace(chr(13),'<br>',$stuffingAdress).'</td></tr> 
    <tr><td class="header-row-header">Depo / Terminal</td><td style="width:10px; text-align:center">:</td><td>'.$depotname.' / '.$terminalname.'</td></tr>   
    </table>
    </td>
    </tr>
    </table>
    <div style="clear:both"></div>
    <table cellpadding="4"  class="table-transaction">';
//$html .= '<tr class="col-header"><td style="text-align:left;width:30px">No</td><td style="text-align:left;width:200px">Biaya</td><td style="text-align:left;width:300px">Catatan</td><td style="text-align:right;width:140px">Jumlah</td></tr>';
    
$cellArray = array ();
array_push($cellArray, array('label' => $obj->lang['number'], 'align' => 'right','width' => '40'));
array_push($cellArray, array('label' => $obj->lang['cost'], 'width' => '200'));
array_push($cellArray, array('label' => $obj->lang['description']));
    
array_push($cellArray, array('label' => $obj->lang['amount'], 'align' => 'right', 'width' => '140'));
$html .= $obj->generatePrintTableRow( array('class' => 'col-header','docWidth' => '670', 'cell' =>  $cellArray));  

for($i=0;$i<count($rsDetail);$i++){ 
    $rsItem = $item->getDataRowById($rsDetail[$i]['costkey']);   
    $html .= '<tr><td style="text-align:right">'.($i+1).'</td> <td>'.$rsItem[0]['name'].'</td><td>'.$rsDetail[$i]['description'].'</td><td style ="text-align:right">'.$obj->formatNumber($rsDetail[$i]['amount']).'</td></tr>';
} 

$sayNumber = $obj->sayNumber($rs[0]['total']);   
$html .= '    
</table>   
<table cellpadding="4"> 
<tr><td style="width:460px"><strong>Terbilang</strong> :<br>'.ucwords($sayNumber).' Rupiah.</td><td style="text-align:right; font-weight:bold;  width:100px; ">Total</td><td style="text-align:right; font-weight:bold;  width:110px;"  >'.$obj->formatNumber($rs[0]['total']).'</td></tr>
</table>


<div style="clear:both"></div>  
'; 
    
    
$html .= $obj->generateSignLabel($rs); 

return $html;
}
?>