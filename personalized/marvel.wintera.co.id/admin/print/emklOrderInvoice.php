<?php

$pdf->setCustomSettings(
   array(
      'showPrintHeader' => false,
      'showPrintFooter' => false,
   )
);

includeClass(array('EMKLOrderInvoice.class.php', 'Item.class.php', 'Customer.class.php', 'TermOfPayment.class.php', 'EMKLJobOrder.class.php', 'Currency.class.php'));
$emklOrderInvoice = createObjAndAddToCol(new EMKLOrderInvoice());

$obj = $emklOrderInvoice; 
 
$generateReportContent = function ($dataset){ 

   $obj = new EMKLOrderInvoice();
   $customer = new Customer();
   $termOfPayment = new TermOfPayment();
   $emklJobOrder = new EMKLJobOrder();
   $currency = new Currency();

   $rs = $dataset['rs'];    
	
	$customCode = new CustomCode();
	$rsInvoiceType = $customCode->searchData($customCode->tableName.'.pkey',$rs[0]['customcodekey'], true);
	$title = $invoiceTitle = (!empty($rsInvoiceType[0]['title'])) ? $rsInvoiceType[0]['title'] : $rsInvoiceType[0]['name'];
	if ($rsInvoiceType[0]['pkey'] == 1) $title = '';

   $rsCustomer = $customer->getDataRowById($rs[0]['customerkey']);
   $rsTermOfPayment = $termOfPayment->getDataRowById($rs[0]['termofpaymentkey']);

   $rsDetail = $obj->getDetailWithRelatedInformation($rs[0]['pkey']);
   $arrDetailKey = array_column($rsDetail, 'pkey');

   $rsCurr = $currency->searchData();
   $rsCurr = array_column($rsCurr,null,'pkey');
   $headerCurrName = $rsCurr[$rs[0]['currencykey']]['name']; 

   $rsItemDetail = $obj->getItemDetail($arrDetailKey);

   //JO  Pertama
   $rsJobOrder = $emklJobOrder->searchData('','', true, ' and ' . $emklJobOrder->tableName.'.pkey = '. $obj->oDbCon->paramString($rsDetail[0]['refsalesorderheaderkey']) .' ');

//   $rsJOContainer = $emklJobOrder->getDetailContainer($rsJobOrder[0]['pkey']);
   $rsVolumeDetail = $emklJobOrder->getDetailVolume($rsJobOrder[0]['pkey']);

//   $arrContainerNo = array();
//   foreach($rsJOContainer as $container) {
//      $arrContainerNo[] = $container['containerno'] . ' / ' . $container['sealno'];
//   }

   $arrVolume = array();
   foreach ($rsVolumeDetail as $key => $value) {
      $arrVolume[] = $obj->formatNumber($value['qty'],0) . ' x ' . $value['itemname'];
   }

	
   $containerSealNo = implode(', ',explode(chr(13),$rsJobOrder[0]['containernumber'])) ;
   $volume = implode('<br>', $arrVolume);

   $html = $obj->printSetting['defaultStyle'];
    
  $draft = ($rs[0]['statuskey'] == 1) ? ' (DRAFT)': '';
	
   $html .= '<div style="width: 680px; text-align:center; font-size:1.5em"><b>'.$title.$draft.'</b></div>';		
   $html .= ' 
         <table cellpadding="4"> 
            <tr>
               <td style="width:370px;" >
                  <table cellpadding="2">
                     <tr>
                        <td style="width:65px;font-weight:bold;">Invoice To</td>
                        <td style="width:8px;text-align:center;font-weight:bold">:</td>
                        <td></td>
                     </tr>
                     <tr>
                        <td style="width:300px;">'. strtoupper(preg_replace("/&#?[a-z0-9]+;/i", "", $rs[0]['customername'])) .'</td>
                     </tr>
                     <tr>
                        <td style="width:300px;">'. strtoupper(nl2br($rsCustomer[0]['address'])) .'</td>
                     </tr>
                  </table>
               </td>
               <td style="width:370px;"> 
                  <table cellpadding="2">
                     <tr>
                        <td style="width:120px;font-weight:bold;">Invoice No</td>
                        <td style="width:10px;text-align:center;font-weight:bold">:</td>
                        <td style="width:300px;text-align:left;">'. $rs[0]['code'] .'</td>
                     </tr>
                     <tr>
                        <td style="width:120px;font-weight:bold;">Invoice Date</td>
                        <td style="width:10px;text-align:center;font-weight:bold">:</td>
                        <td style="width:300px;text-align:left;">'. $obj->formatDBDate($rs[0]['trdate'], 'd-M-Y') .'</td>
                     </tr>
                     <tr>
                        <td style="width:120px;font-weight:bold;">PO. No</td>
                        <td style="width:10px;text-align:center;font-weight:bold">:</td>
                        <td style="width:300px;text-align:left;">'. $rsJobOrder[0]['ponumber'] .'</td>
                     </tr>
                     <tr>
                        <td style="width:120px;font-weight:bold;">Job. No</td>
                        <td style="width:10px;text-align:center;font-weight:bold">:</td>
                        <td style="width:300px;text-align:left;">'. $rsJobOrder[0]['code'] .'</td>
                     </tr>
                     <tr>
                        <td style="width:120px;font-weight:bold;">Term of Payment</td>
                        <td style="width:10px;text-align:center;font-weight:bold">:</td>
                        <td style="width:300px;text-align:left;">'. $rsTermOfPayment[0]['name'] .'</td>
                     </tr>
                  </table>
               </td>
            </tr>
         </table>
      ';

      $html .='<div style="clear:both"></div>';

      $html .= ' 
         <table cellpadding="4" style="border:1px solid #333;"> 
            <tr>
               <td style="width:373px;" >
                  <table cellpadding="2">
                     <tr>
                        <td style="width:110px;font-weight:bold;">Bill Of Landing No.</td> 
                        <td style="width:10px;font-weight:bold;">:</td> 
                        <td style="width:235px">' . $rsDetail[0]['hbl'] . '</td> 
                     </tr>
                     <tr>
                        <td style="width:110px;font-weight:bold;">Vessel</td> 
                        <td style="width:10px;font-weight:bold;">:</td> 
                        <td style="width:235px">'. $rsJobOrder[0]['vesselname'] .'</td> 
                     </tr>
                  </table>
               </td>
               <td style="width:300px;"> 
                  <table cellpadding="2">
                     <tr>
                        <td style="width:110px;font-weight:bold;">Volume</td> 
                        <td style="width:10px;font-weight:bold;">:</td> 
                        <td style="width:200px">'.$volume.'</td> 
                     </tr>
                     <tr>
                        <td style="width:110px;font-weight:bold;">ETD / ETA</td> 
                        <td style="font-weight:bold;">:</td> 
                        <td>'. (($rsJobOrder[0]['etdpol'] == null || $rsJobOrder[0]['etdpol'] == '0000-00-00') ? '' : $obj->formatDBDate($rsJobOrder[0]['etdpol'], 'd-M-Y')) .' / '. (($rsJobOrder[0]['etapod'] == null || $rsJobOrder[0]['etapod'] == '0000-00-00') ? '' : $obj->formatDBDate($rsJobOrder[0]['etapod'], 'd-M-Y')) .'</td> 
                     </tr>
                     <tr>
                        <td style="width:110px;font-weight:bold;">POL / POD</td> 
                        <td style="font-weight:bold;">:</td> 
                        <td>'. $rsJobOrder[0]['polname'] .' / '. $rsJobOrder[0]['podname'] .'</td> 
                     </tr>
                  </table>
               </td>
            </tr>
         </table>
      ';

   $html .= ' 
         <table cellpadding="4" style=""> 
            <tr><td></td></tr>
         </table>
      ';

   $html .= ' 
         <table cellpadding="4" style="border:1px solid #333;"> 
               <tr>
                  <td style="font-weight:bold;text-decoration:underline;">Container / Seal No.</td> 
               </tr>
               <tr>
                  <td style="">'. $containerSealNo .'</td> 
               </tr> 
         </table>
      ';

   $html .= ' 
         <table cellpadding="4" style=""> 
            <tr><td></td></tr>
         </table>
      ';

   $html .= ' 
         <table cellpadding="3" style="border:1px solid #333;"> 
            <thead>
               <tr>
                  <th style="text-decoration:underline;font-weight:bold;width:263px;text-align:center;">Charge Description</th>
                  <th style="text-decoration:underline;font-weight:bold;width:60px;text-align:center;">Qty</th>
                  <th style="text-decoration:underline;font-weight:bold;width:90px;text-align:center;">Unit</th>
                  <th style="text-decoration:underline;font-weight:bold;width:80px;text-align:center;">Rate</th>
                  <th style="text-decoration:underline;font-weight:bold;width:80px;text-align:center;">Cur</th>
                  <th style="text-decoration:underline;font-weight:bold;width:100px;text-align:center">Amount</th>
               </tr>
            </thead>
            <tbody>
         ';
         
      $grandTotal = 0;
      for($i=0; $i <count( $rsItemDetail); $i++) { 
	 
         $itemname = (!empty($rsItemDetail[$i]['aliasname'])) ? $rsItemDetail[$i]['aliasname'] : $rsItemDetail[$i]['itemname']; 
            
		  
         $html .='
                  <tr>
                     <td style="width:263px;">'. $itemname.'</td>
                     <td style="width:60px;text-align:center;">'. $obj->formatNumber($rsItemDetail[$i]['qtyinbaseunit'],0) .'</td>
                     <td style="width:90px;text-align:right;">' . $obj->formatNumber($rsItemDetail[$i]['priceinunit']) . '</td>
                     <td style="width:80px;text-align:right;">' . $obj->formatNumber($rsItemDetail[$i]['rate']) . '</td>
                     <td style="width:80px;text-align:center;">'. $rsCurr[$rsItemDetail[$i]['currencykey']]['name'] .'</td>
                     <td style="width:100px;text-align:right;">' . $obj->formatNumber($rsItemDetail[$i]['total']) . '</td>
                  </tr>
         ';
      
      }

   $grandTotal = $rs[0]['beforetaxtotal'] + $rs[0]['taxvalue']+ $rs[0]['othercost'];
      $html .='
               <tr>
                  <td style="font-weight:bold;width:683px;"></td>
               </tr>

               <tr>
                  <td style="width:333px;"></td>
                  <td style="width:80px;text-align:right;">Sub-Total</td>
                  <td style="width:140px;text-align:left;">'.$headerCurrName.'</td>
                  <td style="width:120px;text-align:right;">'. $obj->formatNumber($rs[0]['beforetaxtotal']) .'</td>
               </tr>
               <tr>
                  <td style="width:333px;"></td>
                  <td style="width:80px;text-align:right;">Tax</td>
                  <td style="width:140px;text-align:left;">'.$headerCurrName.'</td>
                  <td style="width:120px;text-align:right;">' . $obj->formatNumber($rs[0]['taxvalue']) . '</td>
               </tr>
               <tr>
                  <td style="width:333px;"></td>
                  <td style="width:80px;text-align:right;">Stamp</td>
                  <td style="width:140px;text-align:left;">'.$headerCurrName.'</td>
                  <td style="width:120px;text-align:right;">' . $obj->formatNumber($rs[0]['othercost']) . '</td>
               </tr>
               <tr>
                  <td style="width:333px;"></td>
                  <td style="width:80px;text-align:right;">Total</td>
                  <td style="width:140px;text-align:left;">'.$headerCurrName.'</td>
                  <td style="width:120px;text-align:right;">'. $obj->formatNumber($grandTotal) .'</td>
               </tr>
               
            </tbody>
         </table>
      ';
   $html .= ' 
         <table cellpadding="4" style=""> 
            <tbody>
            <tr>
               <td style="width:72px;"><b>The Sum Of</b></td>
               <td style="width:601px;">'. strtoupper($obj->sayNumberInEnglish($grandTotal)) .'</td>
            </tr>
            </tbody>
         </table>
      ';
   $html .= ' 
         <table cellpadding="4" style=""> 
            <tr>
               <td style="width:493px;">
                  <table cellpadding="2" style="text-align:center">
                     <tr>
                        <td style="font-weight:bold;">BANK BCA - KCP GADING BUKIT INDAH</td>
                     </tr>
                     <tr>
                        <td style="font-weight:bold;">PT. MARVEL ABADI PERKASA</td>
                     </tr>
                     <tr>
                        <td style="font-weight:bold;">Acc No. 7481 8989 98 (IDR)</td>
                     </tr>
                     <tr>
                        <td style="font-weight:bold;">Acc. No. 7481 98 8989 (USD)</td>
                     </tr>
                     <tr>
                        <td style="font-weight:bold;">Bank Code : 014 - Swift Code : CENAIDJA</td>
                     </tr>
                  </table>
               </td>
               <td style="width:180px;"> 
                  <table cellpadding="2">
                     <tr><td style="font-weight:bold; height: 130px"></td></tr> 
                     <tr>
                        <td style="font-weight:bold;">Autorized Signature</td>
                     </tr>
                  </table>
               </td>
            </tr>
         </table>
      ';

   $html .= ' 
         <table cellpadding="4" style="border:1px solid #333;"> 
            <tbody>
            <tr>
               <td style="width:673px;text-decoration:underline;">Remarks :</td>
            </tr>
            <tr>
               <td style="width:673px;">1. Mohon cantumkan No. Bill of Landing atau No. Invoice di bukti pembayaran.</td>
            </tr>
            </tbody>
         </table>
      ';

   //$html .= $obj->generateSignLabel($rs);
   return $html;

}

?>