<?php 

//$obj = $truckingServiceOrder;

$cashOutContent = function ($dataset){
    
global $pdf;
    
$obj = new TruckingServiceOrder();  
$cashOut = new truckingCostCashOut();
$cost = new Service(TRUCKING_SERVICE,1);
$service = new Service();
$location = new Location();
$employee = new Employee();

$arrHTML = array();
    
$rs = $dataset['rs']; 
        
$rsPlanner =  $employee->getDataRowById($rs[0]['plannerkey']); 
$plannerName = (!empty($rsPlanner)) ? $rsPlanner[0]['name'] : '';
    
$rsTableType = $obj->getTableKeyAndObj($obj->tableName); 
$rsCashOut = $cashOut->searchData('','',true,' and '.$cashOut->tableName.'.statuskey = 1 and '.$cashOut->tableName.'.refkey = ' . $obj->oDbCon->paramString($rs[0]['pkey']).' and reftabletype = ' . $obj->oDbCon->paramString($rsTableType['key']) );
      
$rsParty= $obj->getPartyDescription($rs[0]['pkey']);
    
if (empty($rsCashOut)) return '';
    
foreach($rsCashOut as $cashOutRow){

    $rsTruckingCost =  $cashOut->getDetailWithRelatedInformation($cashOutRow['pkey']);  

    if (empty($rsTruckingCost))  continue;       

    $depotname = (!empty($rs[0]['depotname'])) ? $rs[0]['depotname'] : ' - ';
    $terminalname = (!empty($rs[0]['terminalname'])) ? $rs[0]['terminalname'] : ' - ';
    $stuffingAdress = (!empty($rs[0]['stuffingaddress'])) ? $rs[0]['stuffingaddress'] : ' ';
    $driverName = $cashOutRow['employeename'];
    $recipientName = $driverName;
    $totalCost = $cashOutRow['total']; 
    $customerName = (!empty($rs[0]['consigneename'])) ? $rs[0]['consigneename']  : $rs[0]['customername'];

    $html = $obj->printSetting['defaultStyle'];
    $html .= ' 
    <table cellpadding="2" > 
    <tr><td><div class="title">VOUCHER PENGELUARAN KAS</div></td></tr>
    <tr><td><div class="subtitle">'.$cashOutRow['code'].' / '.$rs[0]['code'].'</div></td></tr>
    </table> 

    <div style="clear:both"></div>
    <table>
    <tr>
    <td style="width:300px;" >
    <table cellpadding="2"> 
    <tr><td class="header-row-header"  style="width:120px">Tanggal</td><td style="width:10px; text-align:center">:</td><td style="width:170px">'.$obj->formatDBDate($rsCashOut[0]['trdate'],'d / m / Y').'</td></tr>  
    <tr><td class="header-row-header">No. Order</td><td style="width:10px; text-align:center">:</td><td>'. $rs[0]['code'] .'</td></tr> 
    <tr><td class="header-row-header">S / I</td><td style="width:10px; text-align:center">:</td><td>'. $rs[0]['donumber'] .'</td></tr>  
    <tr><td class="header-row-header">Booking Pelayaran</td><td style="width:10px; text-align:center">:</td><td>'. $rs[0]['shipmentnumber'] .'</td></tr>   
    <tr><td class="header-row-header">Jenis Pekerjaan</td><td style="width:10px; text-align:center">:</td><td>'. $rs[0]['categoryname'] .'</td></tr>   
    <tr><td class="header-row-header">Penerima</td><td style="width:10px; text-align:center">:</td><td>'. $recipientName.'</td></tr>   
    <tr><td class="header-row-header">Partai</td><td style="width:10px; text-align:center">:</td><td>'.$rsParty.'</td></tr>   
    </table> 
    </td>
    <td style="width:370px;">
    <table cellpadding="2" >
    <tr><td class="header-row-header" style="width:120px">Pelanggan</td><td style="width:10px; text-align:center">:</td><td style="width:240px;">'.$rs[0]['customername'].'</td></tr>  
    <tr><td class="header-row-header">Consignee</td><td style="text-align:center">:</td><td>'.$rs[0]['consigneename'].'</td></tr> 
    <tr><td class="header-row-header">Lokasi Stuffing</td><td style="width:10px; text-align:center">:</td><td>'.$rs[0]['locationname'].'</td></tr> 
    <tr><td class="header-row-header">Alamat</td><td style="width:10px; text-align:center">:</td><td>'.str_replace(chr(13),'<br>',$stuffingAdress).'</td></tr> 
    <tr><td class="header-row-header">Depo / Terminal</td><td style="width:10px; text-align:center">:</td><td>'.$depotname.' / '.$terminalname.'</td></tr>  
    </table>
    </td>
    </tr>
    </table>
    <div style="clear:both"></div>  
    ';

    $html .= '<table cellpadding="4" style="width:670px"  class="table-transaction">';
    $cellArray = array ();
    array_push($cellArray, array('label' => $obj->lang['number'], 'align' => 'right','width' => '40'));
    array_push($cellArray, array('label' => $obj->lang['cost'], 'width' => '200'));
    array_push($cellArray, array('label' => $obj->lang['description']));
    array_push($cellArray, array('label' => $obj->lang['amount'], 'align' => 'right', 'width' => '140'));
    $html .= $obj->generatePrintTableRow( array('class' => 'col-header','docWidth' => '670', 'cell' =>  $cellArray));  

    for($i=0;$i<count($rsTruckingCost);$i++){ 
        //$rsItem = $item->getDataRowById($rsDetail[$i]['costkey']);   
        $html .= '<tr><td style="text-align:right">'.($i+1).'</td> <td>'.$rsTruckingCost[$i]['costname'].'</td><td>'.$rsTruckingCost[$i]['description'].'</td><td style ="text-align:right">'.$obj->formatNumber($rsTruckingCost[$i]['amount']).'</td></tr>';
    }

    $sayNumber = $obj->sayNumber($totalCost);    

    $html .= '</table>'; 

    $html .= ' 
    <table cellpadding="4"> 
    <tr><td style="width:460px"><strong>Terbilang</strong> :<br>'.ucwords($sayNumber).' Rupiah.</td><td style="text-align:right; font-weight:bold;  width:100px; ">Total</td><td style="text-align:right; font-weight:bold;  width:110px;"  >'.$obj->formatNumber($totalCost).'</td></tr>
    </table>
    ';  

    $html .= '  
    <div style="clear:both"></div>  
    '; 

   
    $html .= $obj->generateSignLabel($rsCashOut); 

    
    array_push($arrHTML,$html);
}
    
return $arrHTML;
};

$generateReportContent = array();
array_push($generateReportContent , array('content' => $cashOutContent, 'newGroup' => true));

?>