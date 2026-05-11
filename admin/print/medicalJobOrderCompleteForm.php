<?php

includeClass(array('MedicalJobOrder.class.php'));
$medicalJobOrder = createObjAndAddToCol(new MedicalJobOrder());

$obj = $medicalJobOrder;
 
$generateReportContent = function ($dataset){ 
 
$obj = new MedicalJobOrder();  
$medicalPurchaseOrder = new MedicalPurchaseOrder();  
$medicalRequestClaim = new MedicalRequestClaim();
        
$rs = $dataset['rs'];  
$rsDetail = $obj->getDetailWithRelatedInformation($rs[0]['pkey']);
$rsMedicalRequestClaim = $medicalRequestClaim->searchData($medicalRequestClaim->tableName.'.pkey', $rs[0]['refkey']);
$jobOrderKeys = $rs[0]['pkey']; 
$rsDiagnoseDetail = $obj->getDetailDiagnose($rs[0]['pkey']);

$arrInitialDiagnose = array();
for ($i=0; $i<count($rsDiagnoseDetail); $i++) {
    array_push($arrInitialDiagnose, $rsDiagnoseDetail[$i]['codenameinitialdiagnose']);
}
	
$rsBuying = $medicalPurchaseOrder->searchData('','', true, ' and '.$medicalPurchaseOrder->tableName.'.refkey in ('.$obj->oDbCon->paramString($jobOrderKeys,',').') and '.$medicalPurchaseOrder->tableName.'.statuskey in (1,2,3)');

$sayNumber = $obj->sayNumber($rs[0]['grandtotal']);

$html = $obj->printSetting['defaultStyle'];
$html .= ' 
<table cellpadding="2" > 
    <tr><td><div class="title">Order Penjualan</div></td></tr>
    <tr><td><div class="subtitle">'.$rs[0]['code'].' / '.$rs[0]['codelog'].'</div></td></tr>
</table> 

<div style="clear:both"></div>
 
<table>
    <tr>
        <td style="width:300px;" >
            <table cellpadding="2">
                <tr>
                    <td class="header-row-header">'.$obj->lang['date'].'</td>
                    <td style="width:10px; text-align:center">:</td>
                    <td style="width:170px">'. $obj->formatDBDate($rs[0]['trdate']) .'</td>
                </tr>
                <tr>
                    <td class="header-row-header">'.$obj->lang['policyNumber'].'</td>
                    <td style="width:10px; text-align:center">:</td>
                    <td style="width:230px">'.$rsMedicalRequestClaim[0]['policynumber'].'</td>
                </tr> 
                <tr>
                    <td class="header-row-header">'.$obj->lang['category'].'</td>
                    <td style="text-align:center">:</td>
                    <td >'.$rsMedicalRequestClaim[0]['categoryname'].'</td>
                </tr>
                <tr>
                    <td class="header-row-header">'.$obj->lang['company'].'</td>
                    <td style="text-align:center">:</td>
                    <td >'.$rsMedicalRequestClaim[0]['customername'].'</td>
                </tr>
                <tr>
                    <td class="header-row-header">'.$obj->lang['insuranceCompany'].'</td>
                    <td style="text-align:center">:</td>
                    <td>'.$rsMedicalRequestClaim[0]['insurancecompanyname'].'</td>
                </tr>
            </table>
        </td>
        <td style="width:10px;"></td>
        <td style="width:360px;"> 
            <table cellpadding="2">
                <tr>
                    <td class="header-row-header">'.$obj->lang['insuredName'].'</td>
                    <td style="width:10px; text-align:center">:</td>
                    <td style="width:180px">'.$rsMedicalRequestClaim[0]['insuredname'].'</td>
                </tr>
                <tr>
                    <td class="header-row-header">'.$obj->lang['IDNumber'].'</td>
                    <td style="text-align:center">:</td>
                    <td>'.$rsMedicalRequestClaim[0]['insuredid'].'</td>
                </tr>
                <tr>
                    <td class="header-row-header">'.$obj->lang['country'].'</td>
                    <td style="text-align:center">:</td>
                    <td>'.$rsMedicalRequestClaim[0]['countryname'].'</td>
                </tr>
                <tr>
                    <td class="header-row-header">'.$obj->lang['email'].'</td>
                    <td style="text-align:center">:</td>
                    <td>'.$rsMedicalRequestClaim[0]['insuredemail'].'</td>
                </tr>
                <tr>
                    <td class="header-row-header">'.$obj->lang['mobilePhone'].'</td>
                    <td style="text-align:center">:</td>
                    <td>'.$rsMedicalRequestClaim[0]['insuredmobile'].'</td>
                </tr>
                <tr>
                    <td class="header-row-header"></td>
                    <td style="text-align:center"></td>
                    <td></td>
                </tr>
                <tr>
                    <td class="header-row-header">'.$obj->lang['diagnose'].'</td>
                    <td style="text-align:center"></td>
                    <td></td>
                </tr>
                <tr>
                    <td colspan="3">'.implode('<br>',$arrInitialDiagnose).'</td>
                </tr>
            </table>
        </td>
    </tr>
</table>';
        
$html .= '<div style="clear:both"></div>
<div style="clear:both; text-align:left" class="subtitle"><strong>'.strtoupper($obj->lang['buying']).'</strong></div>
<br>';      
$tabelItem ='<table cellpadding="2" class="table-transaction">';

$cellArray = array ();
array_push($cellArray, array('label' => $obj->lang['code'], 'width' => '100'));
array_push($cellArray, array('label' => $obj->lang['service'], 'width' => '200'));
array_push($cellArray, array('label' => $obj->lang['qty'],'align' => 'right', 'width' => '90'));
array_push($cellArray, array('label' => $obj->lang['price'] , 'align' => 'right', 'width' => '140'));   
array_push($cellArray, array('label' => $obj->lang['subtotal'] .' (IDR)' , 'align' => 'right', 'width' => '140'));    
$tabelItem .= $obj->generatePrintTableRow( array('class' => 'col-header','docWidth' => '670', 'cell' =>  $cellArray));

$totalBuying = 0;
foreach($rsBuying as $buyingRow){ 
    $rsDetailBuying = $medicalPurchaseOrder->getDetailWithRelatedInformation($buyingRow['pkey']);
    $status = ($buyingRow['statuskey'] == 1) ? ' * ' : '';
 
    if(empty($rsDetailBuying)) continue; 
    
    $tabelItem .= '<tr>
                    <td colspan="'.count($cellArray).'" style="font-style:italic;font-size: 0.9em">
                    <br><br>'.$status.'<span style="font-weight: bold; ">'.$buyingRow['suppliername'].'</span></td>
                </tr>';
 
    for($i=0;$i<count($rsDetailBuying);$i++){
        $tabelItem .= '<tr>
                            <td>'.$buyingRow['code'].'</td>
                            <td>'.$rsDetailBuying[$i]['itemname'].'</td>
                            <td style ="text-align:right">'.$obj->formatNumber($rsDetailBuying[$i]['qty'],-2).'</td>
                            <td style ="text-align:right">'.$obj->formatNumber($rsDetailBuying[$i]['priceinunit'],-2).'</td>
                            <td style ="text-align:right">'.$obj->formatNumber($rsDetailBuying[$i]['total'],-2).'</td> 
                        </tr>';
        $totalBuying += $rsDetailBuying[$i]['total'];
        
    }
     
}
    
$tabelItem .= '<tr> <td colspan="'.count($cellArray).'"></td></tr>';
$tabelItem .= '</table>';
$tabelItem .= '<table cellpadding="3"><tr><td style="text-align:right; font-weight: bold">'.$obj->formatNumber($totalBuying).'</td></tr></table>';

// $html .= $tabelItem; 
// $html .= '<div></div>'; 

    
// $html .='
// <div style="clear:both"></div>
// <div style="clear:both; text-align:left" class="subtitle"><strong>'.strtoupper($obj->lang['purchaseRefund']).'</strong></div>
// <br>';                                 
     

// $tabelItem ='<table cellpadding="2" class="table-transaction">';

// $cellArray = array ();
// array_push($cellArray, array('label' => $obj->lang['code'], 'width' => '115')); 
// array_push($cellArray, array('label' => $obj->lang['qty'],'align' => 'right', 'width' => '40'));
// array_push($cellArray, array('label' => '', 'width' => '5'));
// array_push($cellArray, array('label' => $obj->lang['description']));
// array_push($cellArray, array('label' => $obj->lang['currencyShort'], 'align' => 'center', 'width' => '40'));
// array_push($cellArray, array('label' => $obj->lang['price'] , 'align' => 'right', 'width' => '70'));   
// array_push($cellArray, array('label' => $obj->lang['currencyRate'] , 'align' => 'right', 'width' => '50'));  
// array_push($cellArray, array('label' => $obj->lang['subtotal'] .' (IDR)' , 'align' => 'right', 'width' => '90'));    
// $tabelItem .= $obj->generatePrintTableRow( array('class' => 'col-header','docWidth' => '670', 'cell' =>  $cellArray));  
     
// $totalRefund = 0;
     
// foreach($rsRefund as $buyingRow){  
  
//     $rsDetailBuying = $emklCommission->getDetailWithRelatedInformation($buyingRow['pkey']);
//     $status = ($buyingRow['statuskey'] == 1) ? ' * ' : '';
 
//     if(empty($rsDetailBuying)) continue; 
    
//     $tabelItem .= '<tr><td colspan="'.count($cellArray).'" style="font-style:italic;font-size: 0.9em"><br><br>'.$status.'<span style="font-weight: bold; ">'.$buyingRow['suppliername'].'</span></td></tr>';
  
//      for($i=0;$i<count($rsDetailBuying);$i++){
        
//         $rate = ($rsDetailBuying[$i]['currencykey'] == CURRENCY['idr'] ) ? 1 : $buyingRow['rate'] ;
//         $subtotal =  $rsDetailBuying[$i]['subtotalcurrency'] ;
         
//         if ($rsDetailBuying[$i]['currencykey'] <> CURRENCY['idr'] ) 
//             $subtotal = $rsDetailBuying[$i]['subtotalcurrency'] * $buyingRow['rate'] ;
        
//         $tabelItem .= '<tr>
//                             <td>'.$buyingRow['code'].'</td> 
//                             <td style ="text-align:right">'.$obj->formatNumber($rsDetailBuying[$i]['qty'],-2).'</td>
//                             <td></td>
//                             <td>'.$rsDetailBuying[$i]['description'].'</td>
//                             <td style ="text-align:center">'.$rsCurrency[$rsDetailBuying[$i]['currencykey']].'</td>
//                             <td style ="text-align:right">'.$obj->formatNumber($rsDetailBuying[$i]['priceinunit'],-2).'</td> 
//                             <td style ="text-align:right">'.$obj->formatNumber($rate,-2).'</td>  
//                             <td style ="text-align:right">'.$obj->formatNumber($subtotal).'</td> 
//                         </tr>';
//         $totalRefund += $subtotal;
        
//     }
// }  
        
    
// $tabelItem .= '<tr> <td colspan="'.count($cellArray).'"></td></tr>';
// $tabelItem .= '</table>'; 
// $tabelItem .= '<table cellpadding="3"><tr><td style="font-style:italic" colspan="'.(count($cellArray)-1).'">*) Pending Approval</td><td style="text-align:right; font-weight: bold">'.$obj->formatNumber($totalRefund).'</td></tr></table>';

    
$html .= $tabelItem; 
$html .= '<div></div>'; 

$html .='
<div style="clear:both"></div>
<div style="clear:both; text-align:left" class="subtitle"><strong>'.strtoupper($obj->lang['selling']).'</strong></div>
<br>';                                 
     

$tabelItem ='<table cellpadding="2" class="table-transaction">';

$cellArray = array ();
array_push($cellArray, array('label' => $obj->lang['code'], 'width' => '100'));
array_push($cellArray, array('label' => $obj->lang['service'], 'width' => '200'));
array_push($cellArray, array('label' => $obj->lang['qty'],'align' => 'right', 'width' => '90'));
array_push($cellArray, array('label' => $obj->lang['price'] , 'align' => 'right', 'width' => '140'));   
array_push($cellArray, array('label' => $obj->lang['subtotal'] .' (IDR)' , 'align' => 'right', 'width' => '140'));    
$tabelItem .= $obj->generatePrintTableRow( array('class' => 'col-header','docWidth' => '670', 'cell' =>  $cellArray));  
    
    
$totalSelling = 0;
 
$tabelItem .= '<tr>
                    <td colspan="'.count($cellArray).'" style="font-style:italic;font-size: 0.9em">
                    <br><br>'.$status.'<span style="font-weight: bold; ">'.$rs[0]['customername'].'</span></td>
                </tr>';    

for($i=0;$i<count($rsDetail);$i++){

    $tabelItem .= '<tr>
        <td>'.$rs[0]['code'].'</td>
        <td>'.$rsDetail[$i]['itemname'].'</td>
        <td style ="text-align:right">'.$obj->formatNumber($rsDetail[$i]['qty'],-2).'</td>
        <td style ="text-align:right">'.$obj->formatNumber($rsDetail[$i]['priceinunit'],-2).'</td> 
        <td style ="text-align:right">'.$obj->formatNumber($rsDetail[$i]['total']).'</td> 
    </tr>
    ';
    $totalSelling += $rsDetail[$i]['total'];
}  
        
    
$tabelItem .= '<tr> <td colspan="'.count($cellArray).'"></td></tr>';
$tabelItem .= '</table>';
    
$balance = $totalSelling - $totalBuying ;
    
$color = ($balance == 0 ) ? '' : ($balance > 0) ? 'color:#568203' : 'color:#C41E3A' ;    
$tabelItem .= '<table cellpadding="3">
                    <tr><td  colspan="'.(count($cellArray)-1).'"></td><td style="text-align:right; font-weight: bold">'.$obj->formatNumber($totalSelling).'</td></tr>
                    <tr><td style="font-weight: bold; text-align:right;" colspan="'.count($cellArray).'"></td></tr>
                    <tr ><td style="font-weight: bold; text-align:right;'.$color.'" colspan="'.(count($cellArray)-1).'">Balance</td><td style="text-align:right; font-weight: bold;'.$color.'">'.$obj->formatNumber($balance).'</td></tr>
                </table>';

$html .= $tabelItem; 
    
$html  = '<div style="font-size:0.9em">'.$html.'</div>';    
return $html;
}

?>
