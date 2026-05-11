<?php 

$generateReportContent = function ($dataset){ 
$obj = new CarServiceMaintenance();  
$car = new Car(); 
$chassis = new Chassis(); 
$company = new Company(); 
$warehouse = new Warehouse();
$employee = new Employee();
$termOfPayment = new TermOfPayment();
$supplier = new Supplier();
    
$rs = $dataset['rs'];
$rsDetail = $obj->getDetailWithRelatedInformation($rs[0]['pkey']); 
    
//$rsCar = $car->searchData($car->tableName.'.pkey',$rs[0]['carkey'],true); 
$rsPayment = $termOfPayment->getDataRowById($rs[0]['termofpaymentkey']);
$rsEmployee = $employee->getDataRowById($rs[0]['techniciankey']);
$rsCompany = $company->getDataRowById($rs[0]['companykey']);
$rsWarehouse = $warehouse->getDataRowById($rs[0]['warehousekey']);
$rsCreatedBy = $employee->getDataRowById($rs[0]['createdby']);
$rsSupplier = $supplier->getDataRowById($rs[0]['supplierkey']);
$rsTOP = $termOfPayment->getDataRowById($rs[0]['termofpaymentkey']);
    
//$arrRecipient = array();
//array_push($arrRecipient, $rs[0]['recipientname'], $rs[0]['recipientaddress'], $rs[0]['recipientphone']); 
    
$isOutsource = false;
$isOutsourceHeader ='';
$isOutsourceDetailHeader ='';
$discWidth = 320;
if($rs[0]['isoutsource']){
    $isOutsourceHeader = '<tr><td class="header-row-header">'.$obj->lang['externalWorkshop'].'</td><td>:</td><td>'.$rs[0]['suppliername'].'</td></tr> ';
    $isOutsource = true;
    $isOutsourceDetailHeader = '<td style="width:80px;text-align:right" >Diskon @</td>';
    $discWidth = 240;
}
$carType = '';
switch($rs[0]['typekey']){
    
  case '1' :  
        
        if(!empty($rs[0]['carkey'])){
            $rsCar = $car->searchData($car->tableName.'.pkey',$rs[0]['carkey'],true); 

            $carType = '<tr><td class="header-row-header">'.$obj->lang['carRegistrationNumber'].'</td><td style="width:10px; text-align:center">:</td><td>'.$rsCar[0]['policenumber'].'</td></tr> 
                    <tr><td class="header-row-header">'.$obj->lang['year'].'</td><td style="width:10px; text-align:center">:</td><td>'.$rsCar[0]['year'].'</td></tr> 
                    <tr><td class="header-row-header">'.$obj->lang['typesOfFuel'].'</td><td style="width:10px; text-align:center">:</td><td>'.$rsCar[0]['fueltype'].'</td></tr> 
                    <tr><td class="header-row-header">'.$obj->lang['carSeries'].'</td><td style="width:10px; text-align:center">:</td><td>'.$rsCar[0]['seriesname'].'</td></tr>  
                    <tr><td class="header-row-header">'.$obj->lang['mileage'].'</td><td style="width:10px; text-align:center">:</td><td>'.$obj->formatNumber($rs[0]['mileage']).'</td></tr> ';
        }
        break;
        
   case '2' :  
        
        if(!empty($rs[0]['chassiskey'])){
        
        $rsChassis = $chassis->getDataRowById($rs[0]['chassiskey']);
        $carType = '<tr><td class="header-row-header">'.$obj->lang['chassisNumber'].'</td><td style="width:10px; text-align:center">:</td><td>'.$rsChassis[0]['chassisnumber'].'</td></tr>';
            
        }
        break;

}
    
$html = $obj->printSetting['defaultStyle'];
$html .= ' 
<table cellpadding="2" > 
<tr><td><div class="title">'.strtoupper($obj->lang['carMaintenance']).'</div></td></tr>
<tr><td><div class="subtitle">'.$rs[0]['code'].'</div></td></tr>
</table> 

<div style="clear:both"></div>
<table>
<tr>
<td style="width:390px">
<table cellpadding="2"> 
<tr><td class="header-row-header" >'.$obj->lang['date'].'</td><td style="width:10px; text-align:center">:</td><td style="width: 200px">'.$obj->formatDBDate($rs[0]['trdate'],'d / m / Y').'</td></tr>  
<tr><td class="header-row-header" >'.$obj->lang['warehouse'].'</td><td>:</td><td>'.$rs[0]['warehousename'].'</td></tr>  
<tr><td class="header-row-header" >'.$obj->lang['reference'].'</td><td>:</td><td>'.$rs[0]['refcode'].'</td></tr> 
<tr><td class="header-row-header" >'.$obj->lang['technician'].'</td><td>:</td><td>'.$rs[0]['technicianname'].'</td></tr> 
'.$isOutsourceHeader.'
</table>
</td>
<td style="width:390px">
<table cellpadding="2" >
'.$carType.'
</table>
</td>
</tr>
</table>

<div style="clear:both"></div> ';

$html .= ' 
<table  cellpadding="4" class="table-transaction">';
    
$isDiscount = '';

$cellArray = array ();
array_push($cellArray, array('label' => $obj->lang['item']));
array_push($cellArray, array('label' => $obj->lang['qty'], 'align' => 'right', 'width' => '70'));
array_push($cellArray, array('label' => $obj->lang['unit'], 'width' => '90'));
array_push($cellArray, array('label' => $obj->lang['price'],'align' => 'right', 'width' => '100'));
if($isOutsource)
    array_push($cellArray, array('label' => $obj->lang['discount'],'align' => 'right', 'width' => '80'));
    
array_push($cellArray, array('label' => $obj->lang['total'],'align' => 'right', 'width' => '120')); 
$html .= $obj->generatePrintTableRow( array('class' => 'col-header','docWidth' => '670', 'cell' =>  $cellArray));  

for ($i=0;$i<count($rsDetail);$i++){  
    if($isOutsource){
        if ($rsDetail[$i]['discounttype'] == 2)
        $rsDetail[$i]['discount'] = $rsDetail[$i]['discount']/100 * $rsDetail[$i]['priceinunit'];
        $isDiscount = '<td style="text-align:right">'.$obj->formatNumber($rsDetail[$i]['discount']).'</td>';
    }
    
    
    $html .= '<tr><td>'.$rsDetail[$i]['itemname'].'</td><td style="text-align:right">'.$obj->formatNumber($rsDetail[$i]['qty']).'</td><td>'. $rsDetail[$i]['unitname'] .'</td><td style="text-align:right">'.$obj->formatNumber($rsDetail[$i]['priceinunit']).'</td>'.$isDiscount.'<td style="text-align:right">'.$obj->formatNumber($rsDetail[$i]['total']).'</td></tr>' ; 
}
$html .= '</table>';
    
if ($rs[0]['finaldiscounttype'] == 2)
    $rs[0]['finaldiscount'] = $rs[0]['finaldiscount']/100 * $rs[0]['subtotal'];
    
$finaldiscount = ($rs[0]['finaldiscount'] != 0) ?  $obj->formatNumber($rs[0]['finaldiscount'] * -1) : 0;   
$sayNumber = $obj->sayNumber($rs[0]['grandtotal']);
    
$arrSubtotal = array(); 
    
$ctr = 4 ;     
if ($rs[0]['taxvalue'] != 0){
    array_push($arrSubtotal, '<tr><td style="text-align:right; font-weight:bold;">DPP</td><td style="text-align:right; font-weight:bold;"  >'.$obj->formatNumber($rs[0]['beforetaxtotal']).'</td></tr>');
    array_push($arrSubtotal, '<tr><td style="text-align:right; font-weight:bold;">Pajak</td><td style="text-align:right; font-weight:bold;"  >'.$obj->formatNumber($rs[0]['taxvalue']).'</td></tr>');
    $ctr += 2;
}   
     
    
if ( $rs[0]['etccost'] != 0){
    array_push($arrSubtotal, '<tr><td style="text-align:right; font-weight:bold;">Biaya Lain</td><td style="text-align:right; font-weight:bold;"  >'.$obj->formatNumber($rs[0]['etccost']).'</td></tr>');
    $ctr ++;
}   
    
$top = '';
$totalLabel = 'Total';
if($isOutsource){ 
    $totalLabel = 'SubTotal';
    $topSaid = ($rsTOP[0]['duedays'] > 0 ) ? $rsTOP[0]['duedays'] . ' ' . $obj->lang['day'] : $obj->lang['cash'];
    $top = '<br><br><strong>'.$obj->lang['termofpayment'].' :</strong>  '.$topSaid;
}
     
    
$html .= '
<div style="clear:both"></div> 
<table cellpadding="4"> 
<tr><td rowspan="'.$ctr.'" style="width:460px"><strong>Terbilang</strong> :<br>'.ucwords($sayNumber).' Rupiah.'.$top.'<br><br><strong>Catatan</strong> :<br>'.str_replace(chr(13),'<br>',$rs[0]['trnotes']).'</td><td style="text-align:right; font-weight:bold;  width:100px; ">'.$totalLabel.'</td><td style="text-align:right; font-weight:bold;  width:110px;"  >'.$obj->formatNumber($rs[0]['subtotal']).'</td></tr>
';

if($isOutsource){
    $html .= '<tr><td style="text-align:right; font-weight:bold;">Diskon</td><td style="text-align:right; font-weight:bold;"  >'.$finaldiscount.'</td></tr>';  
    $html .= implode('',$arrSubtotal);
    $html .= '<tr><td style="text-align:right; font-weight:bold;">Total</td><td style="text-align:right; font-weight:bold;"  >'.$obj->formatNumber($rs[0]['grandtotal']).'</td></tr>';

}
$html .= '    </table>
<div style="clear:both"></div> 
<div style="clear:both"></div> ';
    
$html .= '
</table>
<div style="clear:both"></div>';
    
$arrSignLabel = array(); 
array_push($arrSignLabel, array('Dibuat'));
array_push($arrSignLabel, array('Disetujui'));
array_push($arrSignLabel, array('Diterima'));

$html .=' 
    <table cellpadding="4" class="sign">
    <tr>'; 
    for ($i=0;$i<count($arrSignLabel);$i++){
        $html .='<td  class="sign-col" style="height:120px;border-bottom:1px solid black;"><strong>'.$arrSignLabel[$i][0].'</strong></td>';
        if ($i <> count($arrSignLabel) - 1)
            $html .= '<td class="sign-col-space"></td>';
    }
    $html .='</tr>  
    </table>' ;
        
    
        
    
    
        

//$html .= $obj->generateSignLabel($rs); 
return $html;
}
?>
