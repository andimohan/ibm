<?php 
 $pdf->setCustomSettings(
    array(  
         'paperSetting' => 'A5,L',
         'showPrintHeader' => false,
         ) 
);  


$generateReportContent = function ($dataset){ 

$obj = new TruckingCostCashOut();  
$item = new Item();
$employee = new Employee();
$truckingServiceWorkOrder = new TruckingServiceWorkOrder();
  
$rs = $dataset['rs']; 
     
$rsDetail = $obj->getDetailById($rs[0]['pkey']); 
$rsPlanner = $employee->getDataRowById($rs[0]['employeekey']); 
$trnotes = (!empty($rs[0]['note'])) ? '<div style="clear:both"></div><strong>Catatan :</strong> <br>' . str_replace(chr(13),'<br>',$rs[0]['trdesc']) : '';
$html = $obj->printSetting['defaultStyle'];
    
$driverName = $rs[0]['employeename'];   
$recipientName = $driverName;
 
$html .= '
<table cellpadding="2" > 
<tr><td><div class="title">TANDA TERIMA PENGELUARAN UANG</div></td></tr>
<tr><td><div class="subtitle">'.$rs[0]['code'].' / '.$rs[0]['refcode'].'</div></td></tr>
</table>
<div style="clear:both"></div>
<table>
<table cellpadding="2">
<tr><td class="header-row-header" style="width:120px">Tanggal</td><td style="width:10px; text-align:center">:</td><td style="width:170px">'.$obj->formatDBDate($rs[0]['trdate'],'d / m / Y').'</td></tr>
<tr><td class="header-row-header">Penerima</td><td style="text-align:center">:</td><td>'. $recipientName .'</td></tr> 
<tr><td class="header-row-header">Referensi</td><td style="text-align:center">:</td><td>'. $rs[0]['refcode'] .'</td></tr>
</table>

<div style="clear:both"></div>
<table cellpadding="4" class="table-transaction">';
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
<div style="clear:both"></div> 
<table cellpadding="4"> 
<tr><td rowspan="3" style="width:460px"><strong>Terbilang</strong> :<br>'.ucwords($sayNumber).' Rupiah.</td><td style="text-align:right; font-weight:bold;  width:100px; ">SubTotal</td><td style="text-align:right; font-weight:bold;  width:110px;"  >'.$obj->formatNumber($rs[0]['total']).'</td></tr>
</table>
<div style="clear:both"></div>   
'.$trnotes.'
<div style="clear:both"></div>  
'; 
    
$rsEmployee = $employee->getDataRowById(base64_decode($_SESSION[$employee->loginAdminSession]['id']));
    
$arrSignLabel = array();  
array_push($arrSignLabel, array('Kasir', $rsEmployee[0]['name'])); 
array_push($arrSignLabel, array('Sopir',$driverName) ); 

 $html .=' 
        <table cellpadding="4" class="sign">
        <tr>'; 
        for ($i=0;$i<count($arrSignLabel);$i++){
            $html .='<td  class="sign-col" style="height:40px;"><strong>'.$arrSignLabel[$i][0].'</strong></td>';
            if ($i <> count($arrSignLabel) - 1)
                $html .= '<td class="sign-col-space"></td>';
        }
        $html .='</tr> 
        <tr>'; 
        for ($i=0;$i<count($arrSignLabel);$i++){
            $arrSignLabel[$i][1] = (isset($arrSignLabel[$i][1])) ? $arrSignLabel[$i][1] : '';
            $html .='<td  class="sign-name">'.$arrSignLabel[$i][1].'</td>';
            if ($i <> count($arrSignLabel) - 1)
                $html .= '<td class="sign-col-space"></td>';
        }
        $html .='</tr> 
        </table>' ;

return $html;
}
?>