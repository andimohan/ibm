<?php 

$borderTop = 'border-top:1px solid black;';
$borderLeft = 'border-left:1px solid black;';
$borderRight = 'border-right:1px solid black;';
$borderBottom = 'border-bottom:1px solid black;';


//Kolom ttd
$signTable= '
<div></div>
<table cellpadding="3" style="'.$borderLeft.$borderRight.$borderBottom.'text-align:center;font-weight:bold">
<tr><td style="width:100px;border:1px solid black;">Direksi</td><td style="width:110px;border:1px solid black;">Kabag Keu/Acc</td><td style="width:120px;border:1px solid black;">Kabag</td><td style="width:120px;border:1px solid black;">Acounting</td><td  style="width:120px;border:1px solid black;">Kasir</td><td style="width:110px;border:1px solid black;">Penerima</td></tr>
<tr><td style="width:100px;'.$borderRight.'"></td><td style="width:110px;'.$borderRight.'"></td><td style="width:120px;'.$borderRight.'"></td><td style="width:120px;'.$borderRight.'"></td><td style="width:120px;'.$borderRight.'"></td></tr>
<tr><td style="width:100px;'.$borderRight.'"></td><td style="width:110px;'.$borderRight.'"></td><td style="width:120px;'.$borderRight.'"></td><td style="width:120px;'.$borderRight.'"></td><td style="width:120px;'.$borderRight.'"></td></tr>
<tr><td style="width:100px;'.$borderRight.'"></td><td style="width:110px;'.$borderRight.'"></td><td style="width:120px;'.$borderRight.'"></td><td style="width:120px;'.$borderRight.'"></td><td style="width:120px;'.$borderRight.'"></td></tr>
</table>';    
    

$pdf->setCustomSettings(
     array( 
         'paperSetting' => 'A5,L',
         'showPrintHeader' => false, 
		 'marginFooter' => '25',
         'footer' => $signTable,  
         ) 
);

$obj = $cashBank;
$generateReportContent = function ($dataset){ 
    
global $pdf; 
$obj = new CashAdvance();  
    
$rs = $dataset['rs'];
if ($rs[0]['amount'] > 0){
    $title =  $obj->lang['cashInVoucher'];
    $recipientLabel = 'Diterima dari';
} else{
    $title =  $obj->lang['cashOutVoucher'] ; 
    $recipientLabel = 'Dibayar kepada';
} 
	
	
if(!empty($rs[0]['reftabletype'])){
    $cashOut = createObjAndAddToCol(new CashOut());
    $cashIn = createObjAndAddToCol(new CashIn());
    $cashBankIn = createObjAndAddToCol(new CashBankIn());
    $cashBankTransfer = createObjAndAddToCol(new CashBankTransfer());
    $cashBankRealization = createObjAndAddToCol(new CashBankRealization());
    $truckingCostCashOut = createObjAndAddToCol(new TruckingCostCashOut());
    $apPayment = createObjAndAddToCol(new APPayment());
    $arPayment = createObjAndAddToCol(new ARPayment());
    $emklPurchaseOrder = createObjAndAddToCol(new EMKLPurchaseOrder());
    $cashAdvance = createObjAndAddToCol(new CashAdvance());
    $cashAdvanceRealization = createObjAndAddToCol(new CashAdvanceRealization());
    $customerDownpaymentSettlement = createObjAndAddToCol(new CustomerDownpaymentSettlement());
    $supplierDownpaymentSettlement = createObjAndAddToCol(new SupplierDownpaymentSettlement());
    $supplierDownpayment = createObjAndAddToCol(new SupplierDownpayment());
    $customerDownpayment = createObjAndAddToCol(new CustomerDownpayment());
	
    $cashOutTableKey = $obj->getTableKeyAndObj($cashOut->tableName,array('key'))['key'];
    $cashInTableKey = $obj->getTableKeyAndObj($cashIn->tableName,array('key'))['key'];
    $cashBankInTableKey = $obj->getTableKeyAndObj($cashBankIn->tableName,array('key'))['key'];
    $cashBankTransferTableKey = $obj->getTableKeyAndObj($cashBankTransfer->tableName,array('key'))['key'];
    $cashBankRealizationTableKey = $obj->getTableKeyAndObj($cashBankRealization->tableName,array('key'))['key'];
    $truckingCostCashOutTableKey = $obj->getTableKeyAndObj($truckingCostCashOut->tableName,array('key'))['key'];
    $apPaymentTableKey = $obj->getTableKeyAndObj($apPayment->tableName,array('key'))['key'];
    $arPaymentTableKey = $obj->getTableKeyAndObj($arPayment->tableName,array('key'))['key'];
    $emklPurchaseOrderKey = $obj->getTableKeyAndObj($emklPurchaseOrder->tableName,array('key'))['key']; 
    $cashAdvanceKey = $obj->getTableKeyAndObj($cashAdvance->tableName,array('key'))['key'];
    $cashAdvanceRealizationKey = $obj->getTableKeyAndObj($cashAdvanceRealization->tableName,array('key'))['key'];
    $customerDownpaymentSettlementKey = $obj->getTableKeyAndObj($customerDownpaymentSettlement->tableName,array('key'))['key'];
    $supplierDownpaymentSettlementKey = $obj->getTableKeyAndObj($supplierDownpaymentSettlement->tableName,array('key'))['key'];
    $supplierDownpaymentKey = $obj->getTableKeyAndObj($supplierDownpayment->tableName,array('key'))['key'];
    $customerDownpaymentKey = $obj->getTableKeyAndObj($customerDownpayment->tableName,array('key'))['key'];
        
	
    $refObj = $obj->getTableNameAndObjById($rs[0]['reftabletype'])['obj'];   
    $rsObj = $refObj->searchData($refObj->tableName.'.pkey',$rs[0]['refkey']);
    
    // kalo kas keluar 
    switch($rs[0]['reftabletype']){
        case $cashOutTableKey : $rsDetailObj = $refObj->getDetailWithRelatedInformation($rs[0]['refkey']);
                                $recipient =  $rsObj[0]['recipientname'] ;
                                break;
            
         case $cashInTableKey : $rsDetailObj = $refObj->getDetailWithRelatedInformation($rs[0]['refkey']);
                                $recipient =  $rsObj[0]['recipientname'] ;
                                break;
                        
        case $truckingCostCashOutTableKey : $rsDetailObj = $refObj->getDetailWithRelatedInformation($rs[0]['refkey']);
                                            foreach($rsDetailObj as $key=>$detailRow)
                                                if ($detailRow['coakey'] <> $rs[0]['coakey'])
                                                    unset($rsDetailObj[$key]);
                                            $rsDetailObj = array_values($rsDetailObj);
            
                                           $recipient = $rsObj[0]['employeename'];
                                           break;
         case $cashBankRealizationTableKey : $rsDetailObj = $refObj->getDetailWithRelatedInformation($rs[0]['refkey']);
                                            $rsDetailObj = array_values($rsDetailObj);
                                           $recipient = $rsObj[0]['employeename'];
                                           break;    
         case $cashBankInTableKey : $rsDetailObj = $refObj->getDetailWithRelatedInformation($rs[0]['refkey']);
                                            foreach($rsDetailObj as $key=>$detailRow){ 
                                                     if ($detailRow['pkey'] <> $rs[0]['detailkey'])
                                                        unset($rsDetailObj[$key]); 
                                            }
                                           $rsDetailObj = array_values($rsDetailObj);
                                           $recipient = $rsDetailObj[0]['customername'];
                                           break;   
        case $cashBankTransferTableKey : $rsDetailObj = $refObj->getDetailWithRelatedInformation($rs[0]['refkey']);
            
                                foreach($rsDetailObj as $key=>$detailRow){ 
                                        if ($detailRow['pkey'] <> $rs[0]['detailkey'])
                                                unset($rsDetailObj[$key]); 
                                }
            
                                $rsDetailObj = array_values($rsDetailObj); 
            
                                $recipient = ($rs[0]['amount'] > 0) ?  $rsDetailObj[0]['codenamefrom'] :  $rsDetailObj[0]['codenameto'] ;
                                break;
            
            
        case $apPaymentTableKey : $rsTemp = $refObj->getDetailWithRelatedInformation($rs[0]['refkey']);  
             
                                $apDesc = array();
                                foreach($rsTemp as $detailRow){
                                    array_push($apDesc, $detailRow['apcode']. '. ' .$detailRow['refcode2']);
                                }
            
                                $rsDetailObj = array();
                                $rsDetailObj[0]['description'] = implode('<br>',$apDesc);
                                $rsDetailObj[0]['amount'] = $rs[0]['amount'];
                                $recipient =  $rsObj[0]['suppliername'] ;
                                break;
            
        case $arPaymentTableKey : $rsTemp = $refObj->getDetailWithRelatedInformation($rs[0]['refkey']);  
             
                                $apDesc = array();
                                foreach($rsTemp as $detailRow){
                                    array_push($arDesc, $detailRow['arcode']. '. ' .$detailRow['refcode2']);
                                }
            
                                $rsDetailObj = array();
                                $rsDetailObj[0]['description'] = implode('<br>',$apDesc);
                                $rsDetailObj[0]['amount'] = $rs[0]['amount'];
                                $recipient =  $rsObj[0]['customername'] ;
                                break;            
            
        case $emklPurchaseOrderKey : $rsTemp = $refObj->getDetailWithRelatedInformation($rs[0]['refkey']);  
             
                                $desc = array();
                                foreach($rsTemp as $detailRow){
                                    array_push($desc, $detailRow['servicename']);
                                }
            
                                $rsDetailObj = array();
                                $rsDetailObj[0]['description'] =  implode('<br>',$desc);
                                $rsDetailObj[0]['amount'] = $rs[0]['amount'];
                                $recipient =  $rsObj[0]['suppliername'] ;
                                break;
            
        case $cashAdvanceKey :  
             					$rsTemp = $refObj->searchDataRow(array( $refObj->tableName.'.pkey',  $refObj->tableName.'.trdesc' ),
																 ' and '.  $refObj->tableName.'.pkey  = '.$rs[0]['refkey']
																 );  
                                $rsDetailObj = array();
                                $rsDetailObj[0]['description'] = str_replace(chr(13),'<br>',$rsTemp[0]['trdesc']);
                                $rsDetailObj[0]['amount'] = $rs[0]['amount'];
                                $recipient = $rsObj[0]['employeename'] ;
                                break;
        case $cashAdvanceRealizationKey :  
								
             					$rsTemp = $refObj->getDetailCashAdvance($rs[0]['refkey']);  
                                $rsDetailObj = array();
                                $rsDetailObj[0]['description'] = implode(', ',array_column($rsTemp,'cashadvancecode')); 
                                $rsDetailObj[0]['amount'] = $rs[0]['amount'];
                                $recipient = $rsTemp[0]['employeename'] ;
                                break;
            
        case $customerDownpaymentSettlementKey :  
								
             					$rsTemp = $refObj->getDetailWithRelatedInformation($rs[0]['refkey']);  
                                $rsDetailObj = array(); 
                                $rsDetailObj[0]['amount'] = $rs[0]['amount'];
                                $recipient = $rsObj[0]['customername'] ;
                                break;
            
        case $supplierDownpaymentSettlementKey :  
								
             					$rsTemp = $refObj->getDetailWithRelatedInformation($rs[0]['refkey']);  
                                $rsDetailObj = array(); 
                                $rsDetailObj[0]['amount'] = $rs[0]['amount'];
                                $recipient = $rsObj[0]['suppliername'] ;
                                break;
            
        case $supplierDownpaymentKey :  
                                $rsTemp = $refObj->searchDataRow(array( $refObj->tableName.'.pkey',  $refObj->tableName.'.amount' ),
																 ' and '.  $refObj->tableName.'.pkey  = '.$rs[0]['refkey']
																 );  
                                $rsDetailObj = array(); 
                                $rsDetailObj[0]['amount'] = $rs[0]['amount'];
                                $recipient = $rsObj[0]['suppliername'] ;
                                break;
        case $customerDownpaymentKey :  
                                $rsTemp = $refObj->searchDataRow(array( $refObj->tableName.'.pkey',  $refObj->tableName.'.amount' ),
																 ' and '.  $refObj->tableName.'.pkey  = '.$rs[0]['refkey']
																 ); 
                                $rsDetailObj = array(); 
                                $rsDetailObj[0]['amount'] = $rs[0]['amount'];
                                $recipient = $rsObj[0]['customername'] ;
                                break;
            
    }
     
}


	

//$rsCOAEmployee = $chartOfAccount->getDataRowById($rs[0]['cashadvancecoakey']);
//$cashBankCode = (ADV_FINANCE) ? $cashBank->getCashBankRef($rs[0]['pkey'],$obj->tableName)['code'] : ''; 
$borderTop = 'border-top:1px solid black;';
$borderLeft = 'border-left:1px solid black;';
$borderRight = 'border-right:1px solid black;';
$borderBottom = 'border-bottom:1px solid black;';
$profileImg = $obj->loadSetting('companyLogo'); 
$img =  HTTP_HOST.'phpthumb/phpThumb.php?src='.$obj->phpThumbURLSrc.'setting/companyLogo/'.$profileImg.'&w=220&h=110&hash='.getPHPThumbHash($profileImg);

$html = $obj->printSetting['defaultStyle'];
$html .= '
<table style="'.$borderTop.$borderRight.$borderLeft.'width:660px">
    <tr>
        <td  style="width:170px">
        <table cellpadding="3"> 
            <tr>
                <td style="vertical-align:middle; width:180px;font-size:2.4em;font-weight:bold;font-family:Arial Black;font-style:italic" >OKATRANS</td>
            </tr>
        </table>
        </td>
        <td style="width:280px">
            <table cellpadding="2" style="text-align:left;"> 
            <tr><td></td></tr>
            <tr><td style="width:40px;"></td><td style="text-algin:center"><div class="title">'.$title.'</div></td></tr>
            <tr><td style="width:200px;font-size:1.2em"><b>Tgl.</b> '.$obj->formatDBDate($rs[0]['trdate'],'d / m / Y').'</td><td style="width:190px;font-size:1.2em"><b>No: '.$rs[0]['code'].' </b></td></tr>
            </table>  
        </td>
        <td style="width:229.9px">

        </td>
    </tr> 
    <tr><td></td></tr>
</table>
';
     
$html .= '<table cellpadding="2" style="'.$borderRight.$borderLeft.'">
<tr>
	<td style="width:30px"></td>
	<td class="header-row-header" style="font-size:1.2em;">'.$recipientLabel.'</td>
	<td style="width:10px">:</td>
	<td style="width:520px;font-size:1.2em;">'.$recipient.'</td>
</tr> 
<tr>
	<td></td>
	<td class="header-row-header" style="font-size:1.2em;">Akun Kas/Bank</td>
	<td>:</td>
	<td style="font-size:1.2em;">'.$rs[0]['coacode']. ' - '.$rs[0]['coaname'].'</td>
	</tr> 
</table>';

 
$html .= '<table  cellpadding="3" style="'.$borderLeft.$borderRight.'">
<tr class="col-header" ><td style="'.$borderRight.'width:570px;" >Deskripsi</td><td style="text-align:right; width:110px;">Jumlah</td></tr>';
 
	
for ($i=0;$i<count($rsDetailObj);$i++){
      
    switch($rs[0]['reftabletype']){
            case $apPaymentTableKey : $description = $obj->lang['accountsPayablePayment']; 
                                      $description .= '<br>'. $rsDetailObj[0]['description'];
                                      break;
            case $emklPurchaseOrderKey : $description = '<b>'.$rs[0]['refcode'].'</b>'; 
                                      $description .= '<br>'. $rsDetailObj[0]['description'];
                                      break;
            case $cashAdvanceKey : $description = '<b>'.$rs[0]['refcode'].'</b>'; 
                                   $description .= '<br>'.$rsDetailObj[0]['description'];
                                      break;
            case $cashAdvanceRealizationKey : $description = '<b>'.$rs[0]['refcode'].'</b>'; 
                                   $description .= '<br>'.$obj->lang['cashAdvanceRealization']. ' ' . $rsDetailObj[0]['description'];
                                      break; 
            default :   $description =  (isset($rsDetailObj[$i]['costname']) && !empty($rsDetailObj[$i]['costname'] )) ? $rsDetailObj[$i]['costname'] : $rsDetailObj[$i]['trdesc']; 
                        if(empty($description))
                            $description = $rs[0]['refcode'];
                        break;
    }
    
    //$html .= '<tr><td>'. $description .'</td><td style="text-align:right">'.$obj->formatNumber(abs($rsDetailObj[$i]['amount'])).'</td></tr>' ; 
    $html .= '<tr><td style="'.$borderRight.'">'.$description.'</td><td style="text-align:right">'.$obj->formatNumber(abs($rsDetailObj[$i]['amount'])).'</td></tr>' ; 
 
    $detailTotal += abs($rsDetailObj[$i]['amount']);
    //$sayNumber = $obj->sayNumber($detailTotal);

}
	
   
$html .= '</table>' ;
 
 
$html .= '<table  cellpadding="3" style="'.$borderTop.'">
<tr class="" ><td style="width:235px;"></td><td style="width:335px;text-align:right" ></td><td style="'.$borderRight.$borderBottom.$borderLeft.'text-align:right; width:110px;">'.$obj->formatNumber($detailTotal).'</td></tr>
<tr class="" ><td style="width:235px;"></td><td style="width:335px;text-align:right" ></td><td style="text-align:right; width:110px;"></td></tr>
</table>   
'; 
return $html;
}
?>
