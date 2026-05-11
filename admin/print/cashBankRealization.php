<?php 
includeClass('CashBankRealization.class.php');
$cashBankRealization = createObjAndAddToCol( new CashBankRealization());

$obj = $cashBankRealization;

$generateReportContent = function ($dataset){ 

$obj = new CashBankRealization();  
$item = new Item();
$employee = new Employee();
$truckingServiceWorkOrder = new TruckingServiceWorkOrder();
$truckingServiceOrder = new TruckingServiceOrder();
  
$rs = $dataset['rs']; 
     
$rsDetail = $obj->getDetailWithRelatedInformation($rs[0]['pkey']); 
$rsPlanner = $employee->getDataRowById($rs[0]['employeekey']); 
$trnotes = (!empty($rs[0]['note'])) ? '<div style="clear:both"></div><strong>Catatan :</strong> <br>' . str_replace(chr(13),'<br>',$rs[0]['trdesc']) : '';
$html = $obj->printSetting['defaultStyle'];
    
$driverName = $rs[0]['employeename'];   
$recipientName = $driverName;
$refcode ='';
if(!empty($rs[0]['refcode2']))
    $refcode = $rs[0]['refcode2'];

if(!empty($rs[0]['refcode3']))
    $refcode = $refcode.'/'.$rs[0]['refcode3'];
 
$html .= ' 

<table cellpadding="2" > 
<tr><td><div class="title">'.strtoupper($obj->lang['cashBankRealization']).'</div></td></tr>
<tr><td><div class="subtitle">'.$rs[0]['code'].' / '.$rs[0]['refcode'].'</div></td></tr>
</table>
<div style="clear:both"></div>
<table>
<table cellpadding="2">
<tr><td class="header-row-header" style="width:120px">'.$obj->lang['date'].'</td><td style="width:10px; text-align:center">:</td><td style="width:170px">'.$obj->formatDBDate($rs[0]['trdate'],'d / m / Y').'</td></tr>
<tr><td class="header-row-header">'.$obj->lang['recipient'].'</td><td style="text-align:center">:</td><td>'. $recipientName .'</td></tr> 
<tr><td class="header-row-header">'.$obj->lang['reference'].'</td><td style="text-align:center">:</td><td>'. $refcode .'</td></tr>
<tr><td class="header-row-header">'.$obj->lang['si'].'</td><td style="text-align:center">:</td><td>'. $rs[0]['donumber'] .'</td></tr>
<tr><td class="header-row-header">'.$obj->lang['bookingNumber'].'</td><td style="text-align:center">:</td><td>'. $rs[0]['shipmentnumber'] .'</td></tr>
</table>

<div style="clear:both"></div>
<table cellpadding="4" class="table-transaction">';
//$html .= '<tr class="col-header"><td style="text-align:left;width:30px">No</td><td style="text-align:left;width:200px">Biaya</td><td style="text-align:left;width:300px">Catatan</td><td style="text-align:right;width:140px">Jumlah</td></tr>';
    
$cellArray = array ();
array_push($cellArray, array('label' => $obj->lang['qty'], 'align' => 'right','width' => '40'));
array_push($cellArray, array('label' => $obj->lang['costName'], 'width' => '100'));
array_push($cellArray, array('label' => $obj->lang['description']));
array_push($cellArray, array('label' => $obj->lang['cost'], 'align' => 'right', 'width' => '90')); 
array_push($cellArray, array('label' => $obj->lang['realization'], 'align' => 'right', 'width' => '90'));
array_push($cellArray, array('label' => $obj->lang['subtotal'], 'align' => 'right', 'width' => '90'));
$html .= $obj->generatePrintTableRow( array('class' => 'col-header','docWidth' => '670', 'cell' =>  $cellArray));  

for($i=0;$i<count($rsDetail);$i++){ 
//    $rsItem = $item->getDataRowById($rsDetail[$i]['costkey']);   
    $html .= '<tr><td style="text-align:right">'.$obj->formatNumber($rsDetail[$i]['qty']).'</td> <td>'.$rsDetail[$i]['costname'].'</td><td>'.$rsDetail[$i]['description'].'</td><td style ="text-align:right">'.$obj->formatNumber($rsDetail[$i]['costvalue']).'</td><td style ="text-align:right">'.$obj->formatNumber($rsDetail[$i]['realcostvalue']).'</td><td style ="text-align:right">'.$obj->formatNumber($rsDetail[$i]['amount']).'</td></tr>';
} 

$sayNumber = $obj->sayNumber($rs[0]['total']);
$rowspan = 3 ;
$totalPayment = '';
$totalAP = '';
if($rs[0]['totalreceived']!=0){ 
    
    if($rs[0]['employeear']>0){
        $totalAP = '<tr><td style="text-align:right;font-weight:bold;" >'.$obj->lang['employeeAR'].'</td><td style="text-align:right; font-weight:bold;"  >'.$obj->formatNumber($rs[0]['employeear']).'</td></tr>';
        $rowspan = $rowspan + 1;
    }
}
$html .= '    
</table>  
<div style="clear:both"></div> 
<table cellpadding="4"> 
<tr><td rowspan=" '.$rowspan.' " style="width:440px"></td><td style="text-align:right; font-weight:bold;  width:120px; ">'.$obj->lang['total'].'</td><td style="text-align:right; font-weight:bold;  width:110px;"  >'.$obj->formatNumber($rs[0]['total']).'</td></tr>
<tr><td style="text-align:right;font-weight:bold;width:120px;" >'.$obj->lang['realization'].'</td><td style="text-align:right; font-weight:bold;  width:110px;">'.$obj->formatNumber($rs[0]['totalrealization']).'</td></tr>
<tr><td style="text-align:right;font-weight:bold;" >'.$obj->lang['balance'].'</td><td style="text-align:right; font-weight:bold;"  >'.$obj->formatNumber($rs[0]['totalreceived']).'</td></tr>
'.$totalPayment.$totalAP.'
</table>
<div style="clear:both"></div>   
'.$trnotes.'
<div style="clear:both"></div>  
'; 
      
$html .= $obj->generateSignLabel($rs); 
return $html;
}
?>