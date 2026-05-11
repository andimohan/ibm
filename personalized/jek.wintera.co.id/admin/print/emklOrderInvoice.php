<?php

$profileImg = $class->loadSetting('companyLogo'); 
$imgLetterhead =  HTTP_HOST.'download.php?filename=setting/companyLogo/'.$profileImg; 

$customCode = new CustomCode();
$rsInvoiceType = $customCode->searchData($customCode->tableName.'.pkey',$rs[0]['customcodekey'], true);
$title = $invoiceTitle = (!empty($rsInvoiceType[0]['title'])) ? $rsInvoiceType[0]['title'] : $rsInvoiceType[0]['name'];

$pdf->setCustomSettings(
   array(
 	  'header' => '<table><tr>
	  					<td><img src="'.$imgLetterhead.'" style="height: 70px"></td>
						<td style="font-size:2em; font-weight:bold; text-align:center">'.$title.'</td>
						<td style="text-align:right; font-size:0.7em">'.$class->loadSetting('companyName').'<br>'.nl2br($class->loadSetting('companyAddress')).'</td>
					</tr></table>', 
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
   $employee = new Employee();

   $rs = $dataset['rs'];
   
   $rsCustomer = $customer->getDataRowById($rs[0]['customerkey']);
   $rsTOP = $termOfPayment->getDataRowById($rs[0]['termofpaymentkey']);
   $rsCreatedBy = $employee->getDataRowById($rs[0]['createdby']);
    
//   $rsPIC = $customer->getCustomerPersonInChargeDetail($rs[0]['customerkey']);
	
	// sementara
	$rsContactPerson = $customer->getContactPerson($rs[0]['customerkey']);
	$picName = '';
	$picPhone = '';
	foreach($rsContactPerson as $picRow){
		if(strtolower($picRow['position']) == 'pic'){
			$picName = $picRow['name'];
			$picPhone = $picRow['phone'];
			break;
		}
	}
		

   $rsCurr = $currency->searchData();
   $rsCurr = array_column($rsCurr,null,'pkey');
   $headerCurrName = $rsCurr[$rs[0]['currencykey']]['name'];
   $headerRounding = ($rs[0]['currencykey']==CURRENCY['idr']) ? 0 : 2;

   $rsDetail = $obj->getDetailWithRelatedInformation($rs[0]['pkey']);

   $arrDetailKey = array_column($rsDetail, 'pkey');
   $rsItemDetail = $obj->getItemDetail($arrDetailKey);

   //JO  Pertama
   $rsJobOrder = $emklJobOrder->searchData('', '', true, ' and ' . $emklJobOrder->tableName . '.pkey = ' . $obj->oDbCon->paramString($rsDetail[0]['refsalesorderheaderkey']) . ' ');

   $shipperName = ($rsJobOrder[0]['jobtypekey'] == EMKL['jobType']['import'])  ? $rsJobOrder[0]['consigneename'] : $rsJobOrder[0]['customername'];
	
	
   $html = $obj->printSetting['defaultStyle'];

   $html .= '
      <table cellpadding="2"> 
         <tr>
            <td style="width:360px;">
               <table>
                  <tr>
                     <td style="width:80px">Perusahaan</td>
                     <td style="width:10px;text-align:center">:</td>
                     <td style="width:230px">'.  preg_replace("/&#?[a-z0-9]+;/i", "", $rsCustomer[0]['name']) .'</td>
                  </tr>
                  <tr>
                     <td >PIC</td>
                     <td style="text-align:center">:</td>
                     <td >'. $picName .'</td>
                  </tr>
                  <tr>
                     <td >Alamat</td>
                     <td style="text-align:center">:</td>
                     <td>' . nl2br($rsCustomer[0]['address']) . '</td>
                  </tr>
                  <tr>
                     <td >No. Telp.</td>
                     <td style="text-align:center">:</td>
                     <td >'. $picPhone .'</td>
                  </tr>
               </table>
            </td>
            <td style="width:355px;">
               <table>
                  <tr>
                     <td style="width:100px">No. / Tgl. Invoice</td>
                     <td style="width:10px;text-align:center">:</td>
                     <td style="width:200px;">'. $rs[0]['code'] .' / '. $obj->formatDBDate($rs[0]['trdate'], 'd M Y') .'</td>
                  </tr>
                  <tr>
                     <td style="width:100px">Jatuh Tempo</td>
                     <td style="text-align:center">:</td>
                     <td >'.$rsTOP[0]['name'].'</td>
                  </tr>
                  <tr>
                     <td style="width:100px">Jenis Shipment</td>
                     <td style="text-align:center">:</td>
                     <td >' . strtoupper($rsJobOrder[0]['transportationtype']) . ' SHIPMENT / '. strtoupper($rsJobOrder[0]['transportationtype']) .'</td>
                  </tr>
                  <tr>
                     <td style="width:100px">No. BL / AWB</td>
                     <td style="text-align:center">:</td>
                     <td >'. $rsJobOrder[0]['mblnumber'] . '</td>
                  </tr>
                  <tr>
                     <td style="width:100px">No. AJU / Shipper</td>
                     <td style="text-align:center">:</td>
                     <td >' . $rsJobOrder[0]['aju'] . ' / '. html_entity_decode($shipperName) .'</td>
                  </tr>
               </table>
            </td>
         </tr>
      </table>
   ';

   $html .= '<div style="clear:both"></div>';

   $html .= ' 
         <table cellpadding="2" style="border:1px solid #333 ">  
               <tr>
                  <td style="border-left:1px solid #333; border-bottom:1px solid #333; font-weight:bold;width:310px;text-align:center;">DESKRIPSI TAGIHAN</td>
                  <td style="border-left:1px solid #333; border-bottom:1px solid #333; font-weight:bold;width:50px;text-align:center;">JML</td>
                  <td style="border-left:1px solid #333; border-bottom:1px solid #333; font-weight:bold;width:50px;text-align:center;">MU</td>
                  <td style="border-left:1px solid #333; border-bottom:1px solid #333; font-weight:bold;width:90px;text-align:center;">SATUAN</td>
                  <td style="border-left:1px solid #333; border-bottom:1px solid #333; font-weight:bold;width:70px;text-align:center;">RATE</td>
                  <td style="border-left:1px solid #333; border-bottom:1px solid #333; font-weight:bold;width:110px;text-align:center">NOMINAL ('.$headerCurrName.')</td>
               </tr>  
         ';

   $grandTotal = 0;
   for ($i = 0; $i < count($rsItemDetail); $i++) {
	   
	  $itemName = (!empty($rsItemDetail[$i]['aliasname'])) ? $rsItemDetail[$i]['aliasname'] : $rsItemDetail[$i]['itemname'];
		  
	  $detailRate = ($rsItemDetail[$i]['currencykey'] == $rs[0]['currencykey']) ? 1 : $rsItemDetail[$i]['rate'];
	  $rounding = ($rsItemDetail[$i]['currencykey'] == CURRENCY['idr']) ? 0 : 2;
		  
      $html .= '
         <tr>
            <td style="border-left:1px solid #333;text-align:left;">' . $itemName . '</td>
            <td style="border-left:1px solid #333;text-align:center;">' . $obj->formatNumber($rsItemDetail[$i]['qtyinbaseunit'], 0) . '</td>
            <td style="border-left:1px solid #333;text-align:center;">' . $rsCurr[$rsItemDetail[$i]['currencykey']]['name'] . '</td>
            <td style="border-left:1px solid #333;text-align:right;">' . $obj->formatNumber($rsItemDetail[$i]['priceinunit'],2) . '</td>
            <td style="border-left:1px solid #333;text-align:center;">' . $obj->formatNumber($detailRate, 2) . '</td>
            <td style="border-left:1px solid #333;text-align:right;">' . $obj->formatNumber($rsItemDetail[$i]['total'],2) . '</td>
         </tr>
      ';
   }

   $grandTotal = $rs[0]['beforetaxtotal'] + $rs[0]['taxvalue'];
   $html .= ' 
         </table>   
   ';

   $html .= '
      <table cellpadding="2">
         <tr>
            <td style="width:395px;">
            <tr></tr>
               <table cellpadding="" style="">
                  <tr>
                     <td></td>
                  </tr>
                  <tr>
                     <td>PEMBAYARAN HARAP DIBAYARKAN KE REKENING BERIKUT :</td>
                  </tr>
                  <tr>
                     <td></td>
                  </tr>
                  <tr>
                     <td>PT. BANK MANDIRI (PERSERO) Tbk.</td>
                  </tr>
                  <tr>
                     <td>CABANG JAKARTA KRAMAT RAYA</td>
                  </tr>
                  <tr>
                     <td style="width:80px">REKENING</td>
                     <td style="width:10px;text-align:center">:</td>
                     <td>123-00-0779660-2 (IDR)</td>
                  </tr>
                  <tr>
                     <td style="width:80px">NAMA</td>
                     <td style="width:10px;text-align:center">:</td>
                     <td>PT JAKARTA EKSPRES KARGO</td>
                  </tr>
                  <tr>
                     <td style="width:80px">SWIFT CODE</td>
                     <td style="width:10px;text-align:center">:</td>
                     <td>BMRIIDJA</td>
                  </tr>
               </table>
            </td>
            <td style="width:345px;">
               <table cellpadding="4" style="">
                  <tr>
                     <td style="font-weight:bold;width:140px">SUB TOTAL</td>
                     <td style="font-weight:bold;width:30px">'. $headerCurrName .'</td>
                     <td style="font-weight:bold;width:110px;text-align:right">' . $obj->formatNumber( $rs[0]['beforetaxtotal'] ,2) . '</td>
                  </tr>
                  <tr>
                     <td style="font-weight:bold;width:140px">PPN ('. $obj->formatNumber($rs[0]['taxpercentage'],2) .' %)</td>
                     <td style="font-weight:bold;width:30px">' . $headerCurrName. '</td>
                     <td style="font-weight:bold;width:110px;text-align:right">' . $obj->formatNumber($rs[0]['taxvalue'],2) . '</td>
                  </tr>
                  <tr>
                     <td style="font-weight:bold;width:140px">TOTAL</td>
                     <td style="font-weight:bold;width:30px">' . $headerCurrName. '</td>
                     <td style="font-weight:bold;width:110px;text-align:right">' . $obj->formatNumber($grandTotal,2) . '</td>
                  </tr>
                  <tr><td></td><td></td><td></td></tr>
                  <tr><td></td><td></td><td></td></tr>
                  <tr>
                     <td style="text-align:center;width:280px">Hormat Kami,</td>
                  </tr>
                  <tr><td></td><td></td><td></td></tr>
                  <tr><td></td><td></td><td></td></tr>
                  <tr><td></td><td></td><td></td></tr>
                  <tr><td></td><td></td><td></td></tr>
                  <tr><td></td><td></td><td></td></tr>
                  <tr>
                     <td style="text-align:center;width:280px">( '.$rsCreatedBy[0]['name'].' )</td>
                  </tr>
               </table>
            </td>
         </tr>      
      </table>
   '; 
   
   return $html;

}

?>