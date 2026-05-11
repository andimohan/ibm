<?php 
 
includeClass(array('CashBank.class.php','CashBankRealization.class.php','CashOut.class.php',
				   'CashIn.class.php','CashBankTransfer.class.php','TruckingCostCashOut.class.php','APPayment.class.php',
				   'EMKLPurchaseOrder.class.php','CashAdvance.class.php','CashAdvanceRealization.class.php',
				   'CustomerDownpaymentSettlement.class.php','SupplierDownpaymentSettlement.class.php','CashBankIn.class.php','CashBankOut.class.php'));
$cashBank = new CashBank();
$obj = $cashBank; 


$arrID = array();
if (isset( $_GET['cashbankoutkey']) && !empty( $_GET['cashbankoutkey'])){  
	$cashBankOut = new CashBankOut();
	$rsTableType = $cashBankOut->getTableKeyAndObj($cashBankOut->tableName, array('key')); 
	
	$arrBankOutKey = explode(',',$_GET['cashbankoutkey']);
	$rsCashVoucher = $obj->searchData('','',true,' and '.$obj->tableName.'.statuskey in(2,3) and '.$obj->tableName.'.refkey  in (' . $obj->oDbCon->paramString($arrBankOutKey,',').' ) and reftabletype = ' . $obj->oDbCon->paramString($rsTableType['key']) ); 
	
	$arrID = array_column($rsCashVoucher,'pkey');
}else if (isset( $_GET['cashbankinkey']) && !empty( $_GET['cashbankinkey'])){  
	$cashBankIn = new CashBankIn();
	$rsTableType = $cashBankIn->getTableKeyAndObj($cashBankIn->tableName, array('key'));
	
	$arrBankInKey = explode(',',$_GET['cashbankinkey']);
	$rsCashVoucher = $obj->searchData('','',true,' and '.$obj->tableName.'.statuskey in(2,3) and '.$obj->tableName.'.refkey  in (' . $obj->oDbCon->paramString($arrBankInKey,',').' ) and reftabletype = ' . $obj->oDbCon->paramString($rsTableType['key']) ); 
	
	$arrID = array_column($rsCashVoucher,'pkey');
}else  if (isset( $_GET['bankoutkey']) && !empty( $_GET['bankoutkey'])){  
        $cashBankOut = new CashOut();
        $rsTableType = $cashBankOut->getTableKeyAndObj($cashBankOut->tableName, array('key')); 

        $arrBankOutKey = explode(',',$_GET['bankoutkey']);
        $rsCashVoucher = $obj->searchData('','',true,' and '.$obj->tableName.'.statuskey in(2,3) and '.$obj->tableName.'.refkey  in (' . $obj->oDbCon->paramString($arrBankOutKey,',').' ) and reftabletype = ' . $obj->oDbCon->paramString($rsTableType['key']) ); 

        $arrID = array_column($rsCashVoucher,'pkey');
  
}else  if (isset( $_GET['bankinkey']) && !empty( $_GET['bankinkey'])){  
        $cashBankIn = new CashIn();
        $rsTableType = $cashBankIn->getTableKeyAndObj($cashBankIn->tableName, array('key')); 

        $arrBankOutKey = explode(',',$_GET['bankinkey']);
        $rsCashVoucher = $obj->searchData('','',true,' and '.$obj->tableName.'.statuskey in(2,3) and '.$obj->tableName.'.refkey  in (' . $obj->oDbCon->paramString($arrBankOutKey,',').' ) and reftabletype = ' . $obj->oDbCon->paramString($rsTableType['key']) ); 

        $arrID = array_column($rsCashVoucher,'pkey');
  
}else{
	$arrID = explode(',',$_GET['id']);
}


$generateReportContent = function ($dataset){  
    
$obj = new CashBank();
//$chartOfAccount = new ChartOfAccount();
    
$rs = $dataset['rs']; 
//$rsCOA = $chartOfAccount->getDataRowById($rs[0]['coakey']);
    
if ($rs[0]['amount'] > 0){
    $title =  'Kas/Bank Masuk';
    $recipientLabel = $obj->lang['sender'];
} else{
    $title =  'Kas/Bank Keluar';
    $recipientLabel = $obj->lang['recipient'];
} 
    
if(!empty($rs[0]['reftabletype'])){
    $cashOut = createObjAndAddToCol(new CashOut());
    $cashIn = createObjAndAddToCol(new CashIn());
    $cashBankIn = createObjAndAddToCol(new CashBankIn());
    $cashBankOut  = createObjAndAddToCol(new CashBankOut());
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
    $cashBankOutTableKey = $obj->getTableKeyAndObj($cashBankOut->tableName,array('key'))['key'];
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
        case $cashOutTableKey :  
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
			// sdh gk perlu,  ini utk yg model lama
//                                            foreach($rsDetailObj as $key=>$detailRow){ 
//                                                     if ($detailRow['pkey'] <> $rs[0]['detailkey'])
//                                                        unset($rsDetailObj[$key]); 
//                                            }
                                           $rsDetailObj = array_values($rsDetailObj);

										   $arrRecipient = array();
										   if(!empty($rs[0]['recipientname'])) array_push($arrRecipient,$rs[0]['recipientname']);
										   if(!empty($rs[0]['attnname'])) array_push($arrRecipient,$rs[0]['attnname']);

										   $recipient = implode(' / ', $arrRecipient);

                                           break;   
 
	  	case $cashBankOutTableKey : $rsDetailObj = $refObj->getDetailWithRelatedInformation($rs[0]['refkey']);
			// sdh gk perlu,  ini utk yg model lama
//									foreach($rsDetailObj as $key=>$detailRow){ 
//											 if ($detailRow['pkey'] <> $rs[0]['detailkey'])
//												unset($rsDetailObj[$key]); 
//									}
								   $rsDetailObj = array_values($rsDetailObj);
			
								   $arrRecipient = array();
								   if(!empty($rs[0]['recipientname'])) array_push($arrRecipient,$rs[0]['recipientname']);
								   if(!empty($rs[0]['attnname'])) array_push($arrRecipient,$rs[0]['attnname']);
			 
								   $recipient = implode(' / ',$arrRecipient);

									$title =  $obj->lang['cashOutVoucher'];
									$recipientLabel = $obj->lang['recipient'];

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


$html = $obj->printSetting['defaultStyle'];
      
$html .= ' 
<table cellpadding="2" > 
<tr><td><div class="title">'.strtoupper($title).'</div></td></tr>
<tr><td><div class="subtitle">'.$rs[0]['code'].'</div></td></tr>
</table> 

<div style="clear:both"></div>

<table cellpadding="2" >  
<tr><td class="header-row-header">'.$obj->lang['date'].'</td><td style="width:10px; text-align:center">:</td><td>'.$obj->formatDBDate($rs[0]['trdate'],'d / m / Y').'</td></tr> 
<tr><td class="header-row-header">'.$obj->lang['account'].'</td><td style=" text-align:center">:</td><td>'.$rs[0]['coacode']. ' - '.$rs[0]['coaname'].'</td></tr> 
<tr><td class="header-row-header">'.$obj->lang['reference'].'</td><td style="text-align:center">:</td><td>'.$rs[0]['refcode'].'</td></tr> 
<tr><td class="header-row-header">'.$recipientLabel.'</td><td style="text-align:center">:</td><td>'.$recipient.'</td></tr> 
</table>   
<div style="clear:both"></div> ';

$cellArray = array();
//array_push($cellArray, array('label' => $obj->lang['cost'], 'width' => '225'));
array_push($cellArray, array('label' => $obj->lang['description']));
array_push($cellArray, array('label' => $obj->lang['amount'],'align' => 'right', 'width' => '100'));
  
$html .= '<table  cellpadding="4" class="table-transaction">';
$html .= $obj->generatePrintTableRow( array('class' => 'col-header', 'cell' =>  $cellArray)); 

$detailTotal=0;
	
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
			case $cashBankOutTableKey : 
										// harusnya udah gk ad cost name
										$arrDesc = array();
										if(!empty($rsDetailObj[$i]['costname'])) array_push($arrDesc ,$rsDetailObj[$i]['costname']);
										if(!empty($rsDetailObj[$i]['trdesc'])) array_push($arrDesc ,$rsDetailObj[$i]['trdesc']);
											
										$description = implode('. ',$arrDesc );
            
										break;
          
            default :   $description =  (isset($rsDetailObj[$i]['costname']) && !empty($rsDetailObj[$i]['costname'] )) ? $rsDetailObj[$i]['costname'] : $rsDetailObj[$i]['trdesc']; 
                        if(empty($description))
                            $description = $rs[0]['refcode'];
            
                        if(isset($rsDetailObj[$i]['coacodename']) && !empty($rsDetailObj[$i]['coacodename']))
                            $description .= '<br>'.$rsDetailObj[$i]['coacodename'];
            
                        break;
    }
    
    $html .= '<tr><td>'. $description .'</td><td style="text-align:right">'.$obj->formatNumber(abs($rsDetailObj[$i]['amount'])).'</td></tr>' ; 
    $detailTotal += abs($rsDetailObj[$i]['amount']);
    $sayNumber = $obj->sayNumber($detailTotal);

}
$html .= '</table>' ;

$html .= '
<div style="clear:both"></div> 
<table cellpadding="4">';
  
$cellArray = array ();
array_push($cellArray, array('label' => '<strong>'.$obj->lang['say'].'</strong> :<br>'.ucwords($sayNumber).' Rupiah.'));
array_push($cellArray, array('label' => $obj->lang['total'],'align' => 'right', 'width' => '80','style' => 'font-weight:bold'));
array_push($cellArray, array('label' => $detailTotal,'align' => 'right', 'format' => 'number', 'width' => '100'));
$html .= $obj->generatePrintTableRow( array('cell' =>  $cellArray));  
    
//$html .= '</table>
//<table cellpadding="4">
//<tr><td><strong>Catatan</strong> :<br>'.str_replace(chr(13),'<br>',$rs[0]['trdesc']).'</td></tr>
//</table>';
	
$html .='<div "clear:both"></div>';

$html .= $obj->generateSignLabel($rs); 
    
return $html;
}
?>