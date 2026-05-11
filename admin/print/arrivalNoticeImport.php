<?php

includeClass(array('EMKLJobOrder.class.php', 'Customer.class.php', 'Port.class.php', 'Supplier.class.php', 'Vessel.class.php', 'Employee.class.php', 'Container.class.php', 'Service.class.php', 'Currency.class.php'));
$emklJobOrderImport = createObjAndAddToCol(new EMKLJobOrder(EMKL['jobType']['import']));

$obj = $emklJobOrderImport;

$generateReportContent = function ($dataset) {
   $obj = new EMKLJobOrder(EMKL['jobType']['import']);

   $service = new Service(SERVICE);
   $employee = new Employee();
   $container = new Container();
   $currency = new Currency();
   $vessel = new Vessel();
   $supplier = new Supplier();
   $port = new Port();
   $customer = new Customer();

   $rsCurrency = $currency->searchData();
   $rsCurrency = array_column($rsCurrency, 'name', 'pkey');

   $rsContainer = $container->searchData();
   $rsContainer = array_column($rsContainer, 'name', 'pkey');

   $rsService = $service->searchData();
   $rsService = array_column($rsService, 'name', 'pkey');

   $rs = $dataset['rs'];

   // kalo LCL harus merge dengan anak2nya
   if ($rs[0]['ismaster'] && in_array($rs[0]['loadcontainertypekey'], array (EMKL['emklType']['lcl'], EMKL['emklType']['lclnc']))) {
      $rsDetail = $obj->getLCLDetailWithRelatedInformation($rs[0]['pkey']);
   } else {
      $rsDetail = $obj->getDetailWithRelatedInformation($rs[0]['pkey']);
   }

   $rsCurrencyIDR = $currency->getDataRowById(CURRENCY['idr']);

   $rsFeeder = $vessel->getDataRowById($rs[0]['feederkey']);
   $feederName = $rsFeeder[0]['name'];

   $rsVessel = $vessel->getDataRowById($rs[0]['vesselkey']);
   $vesselName = $rsVessel[0]['name'];

   $rsCarrier = $supplier->getDataRowById($rs[0]['carrierkey']);
   $shippingLine = $rsCarrier[0]['name'];

   $rsPOD = $port->getDataRowById($rs[0]['podkey']);
   $placeOfDeliveryName = $rsPOD[0]['name'];

   $rsPOL = $port->getDataRowById($rs[0]['polkey']);
   $placeOfReceiptName = $rsPOL[0]['name'];

   $rsCustomer = $customer->getDataRowById($rs[0]['customerkey']);
   $consigneeName = $rsCustomer[0]['name'];

   $ETA = (empty($rs[0]['etapod'])|| $rs[0]['etapod'] == '0000-00-00' ? '' : $obj->formatDBDate($rs[0]['etapod'], 'd / m / Y'));
   $ETD = (empty($rs[0]['etdpol'])|| $rs[0]['etdpol'] == '0000-00-00' ? '' : $obj->formatDBDate($rs[0]['etdpol'], 'd / m / Y'));

   $shipperName = $rs[0]['consigneename'];

   $rsVolumeDetail = $obj->getDetailVolume($rs[0]['pkey']);
   $containerTotal = count($rsVolumeDetail);

   $rsDetailContainer = $obj->getDetailContainer($rs[0]['pkey']);
   $arrContainerNo = array_column($rsDetailContainer, 'containerno');

   $arrContainer = array();
   foreach ($rsVolumeDetail as $key => $value) {
      $arrContainer[] = $obj->formatNumber($value['qty']) . ' x ' . $value['itemname'];
   }
   $arrContainer = implode(', ', $arrContainer);
   $arrContainerNo = implode(', ', $arrContainerNo);

   $arrContainerAndNumber = $arrContainer . '.<br> ' . $arrContainerNo;

   $rsDetail = $obj->getDetailWithRelatedInformation($rs[0]['pkey']);
   $hblNumber = array_column($rsDetail, 'hbl'); 
   $hblNumber = implode(', ', $hblNumber);

   $rsInvoiceDetail = $obj->getInvoiceDetailItem($rs[0]['pkey']);
   $rsInvoiceDetail = $obj->reindexDetailCollections($rsInvoiceDetail, 'refheaderkey');

   $html = $obj->printSetting['defaultStyle'];

   $html .= '
      <style>
      .table-transaction {border-bottom:1px solid #999}
      .col-header td{border-bottom:1px solid #999; border-top:1px solid #999; font-weight: bold}
      </style>
      <table cellpadding="2" > 
         <tr><td><div class="title">ARRIVAL NOTICE</div></td></tr>
      </table> 
   ';

   $html .= '
      <p>Dear Sir/Madam,<br>Kindly be advised below information:</p>
   ';

   $html .= '
   <table cellpadding="2">
   <tr>
      <td style="width:100px">Feeder Vessel</td>
      <td style="width:8px">:</td>
      <td style="width:560px;">' . $feederName . '</td>
   </tr>
   <tr>
      <td style="width:100px">Mother Vessel</td>
      <td style="width:8px">:</td>
      <td>'. $vesselName .'&nbsp;/&nbsp;FLAG : '. strtoupper($rs[0]['flag']) .'</td>
   </tr>
   <tr>
      <td style="width:100px">Shipping Line</td>
      <td style="width:8px">:</td>
      <td>'. $shippingLine.'</td>
   </tr>
   <tr>
      <td style="width:100px">Master B/L No.</td>
      <td style="width:8px">:</td>
      <td>'.$rs[0]['mblnumber'].'</td>
   </tr>
   <tr>
      <td style="width:100px">House B/L No.</td>
      <td style="width:8px">:</td>
      <td>'.$hblNumber.'</td>
   </tr>
   <tr>
      <td style="width:100px">Port of Receipt</td>
      <td style="width:8px">:</td>
      <td>'. $placeOfReceiptName.'</td>
   </tr>
   <tr>
      <td style="width:100px">Port of Delivery</td>
      <td style="width:8px">:</td>
      <td>'. $placeOfDeliveryName.'</td>
   </tr>
   <tr>
      <td style="width:100px">ETA</td>
      <td style="width:8px">:</td>
      <td>'. $ETA .'</td>
   </tr>
   <tr>
      <td style="width:100px">ETD</td>
      <td style="width:8px">:</td>
      <td>'. $ETD.'</td>
   </tr>
   <tr>
      <td style="width:100px">Consignee</td>
      <td style="width:8px">:</td>
      <td>'. $consigneeName .'</td>
   </tr>
   <tr>
      <td style="width:100px">Shipper</td>
      <td style="width:8px">:</td>
      <td>'. $shipperName .'</td>
   </tr>
   <tr>
      <td style="width:100px">Total Container</td>
      <td style="width:8px">:</td>
      <td>'. $containerTotal .' '.$obj->lang['container'].'</td>
   </tr>
   <tr>
      <td style="width:100px">Containers</td>
      <td style="width:8px">:</td>
      <td>'. $arrContainerAndNumber.'</td>
   </tr>
   </table>
   ';

   $html .= '<div style="clear:both"></div>';
   if (!empty ($rsInvoiceDetail)) {
      
      foreach ($rsInvoiceDetail as $rsInvoice) {

         $tabelItem .= '<table cellpadding="2" class="table-transaction">';
         
         $cellArray = array ();
         array_push($cellArray, array ('label' => 'No', 'align' => 'right', 'width' => '25'));
         array_push($cellArray, array ('label' => 'Service Type', 'width' => '130'));
         array_push($cellArray, array ('label' => 'Quantity', 'align' => 'right', 'width' => '70'));
         array_push($cellArray, array ('label' => '', 'align' => 'center', 'width' => '65'));
         array_push($cellArray, array ('label' => 'Amount', 'align' => 'right', 'width' => '85'));
         array_push($cellArray, array ('label' => 'Total Amount', 'align' => 'right', 'width' => '95'));
         array_push($cellArray, array ('label' => 'Rate', 'align' => 'right', 'width' => '60'));
         array_push($cellArray, array ('label' => '', 'align' => 'right', 'width' => '30'));
         array_push($cellArray, array ('label' => 'Total Exch Amount', 'align' => 'right', 'width' => '120'));
         $tabelItem .= $obj->generatePrintTableRow(array ('class' => 'col-header', 'docWidth' => '670', 'cell' => $cellArray));

         $rsDetails = $obj->getDetailWithRelatedInformation($rs[0]['pkey']);

         $grandTotal = 0;
            if (empty ($rsInvoice))
               continue;

            for ($j = 0; $j < count($rsInvoice); $j++) {
               $quantity = $obj->formatNumber($rsInvoice[$j]['qtyinbaseunit']) . ' x ' . $rsInvoice[$j]['containername'];
               $rate = ($rsInvoice[$j]['currencykey'] == CURRENCY['idr']) ? 1 : $rsInvoice[$j]['rate'];
               $subtotalIDR = $rsInvoice[$j]['total'];

               if ($rsInvoice[$j]['currencykey'] <> CURRENCY['idr']) {
                  $subtotalIDR = $rsInvoice[$j]['priceinunit'] * $rate;
               }

            $tabelItem .= '<tr>
            <td style="text-align:right">' . ($j + 1) . '.</td>
            <td>' . $rsInvoice[$j]['servicename'] . '</td>
            <td style="text-align:right">' . $quantity . '</td>
            <td style="text-align:center">' . $rsInvoice[$j]['currencyname'] . '</td>
            <td style="text-align:right">' . $obj->formatNumber($rsInvoice[$j]['priceinunit'], 2) . '</td>
            <td style="text-align:right">' . $obj->formatNumber($rsInvoice[$j]['total'], 2) . '</td>
            <td style="text-align:right">' . $obj->formatNumber($rate, 2) . '</td>
            <td style="text-align:right; ">' . $rsCurrencyIDR[0]['name'] . '</td>
            <td style="text-align:right; ">' . $obj->formatNumber($subtotalIDR, 2) . '</td>
         </tr>';

               $grandTotal += $subtotalIDR;
            }
 
         $tabelItem .= '</table>';
         $tabelItem .= '<table cellpadding="2"><tr><td style="width: 530px;"></td><td style="text-align:right;width: 30px; font-weight: bold">' . $rsCurrencyIDR[0]['name'] . '</td><td  style="text-align:right; font-weight: bold; width: 120px; ">' . $obj->formatNumber($grandTotal, 2) . '</td></tr></table>';
         $tabelItem .= '<div style="clear:both"></div>';
      }


      $tabelItem .='
      <table style="width:100%">
         <tr>
            <td style="width:50px">'. $obj->lang['note'] .'</td>
            <td style="width:4px">:</td>
            <td>: '. $rs[0]['trdesc'] .'</td>
         </tr>
      </table>
      ';

   } else {
      $tableItem = null;
   }

   $html .= $tabelItem;

   return $html;

}

?>