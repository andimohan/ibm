<?php

$pdf->setCustomSettings(
    array(
        'showPrintHeader' => '',
        'marginFooter' => 25,
        'footer' => '',
        'pdfMarginHeader' => 40,
    )
);

$generateReportContent = function ($dataset) {

    $obj = new TruckingServiceOrderInvoice();
    $truckingServiceOrder = new TruckingServiceOrder();
    $truckingServiceWorkOrder = new TruckingServiceWorkOrder();
    $paymentMethod = new PaymentMethod();
    $customCode = new CustomCode();
    $customer = new Customer();
    $consignee = new Consignee();

    $rs = $dataset['rs'];

    $type = $_GET['datetype'];
 
    if($type == 'perioddate') {
        $labelDate = 'Periode';      
        $invoiceDate =  $obj->simplifyDateRange($rs[0]['startdateperiod'], $rs[0]['enddateperiod']);
    } else if($type == 'invoicedate') {
        $labelDate = 'Tanggal';
        $invoiceDate = $obj->formatDBDate($rs[0]['trdate'], 'd  F  Y');
    }

    $rsCustomer = $customer->searchData('', '', true, ' and ' . $customer->tableName . '.pkey = ' . $obj->oDbCon->paramString($rs[0]['customerkey']) . '');

    $rsPaymentMethod = $paymentMethod->getDataRowById($rs[0]['companybankkey']);
    $rsDetail = $obj->getDetailWithRelatedInformation($rs[0]['pkey']);

    $bankName = $rsPaymentMethod[0]['bankname'];
    $branch = $rsPaymentMethod[0]['branch'];
    $accountName = $rsPaymentMethod[0]['bankaccountname'];
    $accountNumber = $rsPaymentMethod[0]['bankaccountnumber'];

    $arrSOKey = array_column($rsDetail, 'salesorderkey');

    $rsJOHeader = $truckingServiceOrder->searchData('','', true, ' and ' . $truckingServiceOrder->tableName . '.pkey in ('. $obj->oDbCon->paramString($arrSOKey,',') .') ');
    $rsJOHeaderCols = $obj->reindexDetailCollections($rsJOHeader, 'pkey');
    $rsJODetail = $truckingServiceOrder->getDetailWithRelatedInformation($arrSOKey);

    $rsWO = $truckingServiceWorkOrder->searchData('','',true, ' and ' . $truckingServiceWorkOrder->tableName.'.refkey in  (' . $obj->oDbCon->paramString($arrSOKey,',') .') ');
    $rsWOCols = $obj->reindexDetailCollections($rsWO,'refkey');

    $invoiceSignature = $rs[0]['invoicesignaturename'];

    $arrJobOrderData = array();
    foreach($rsJODetail as $detailRow) {

        $rsJOHeaderCol = $rsJOHeaderCols[$detailRow['refkey']];

        array_push($arrJobOrderData, array(
            'pkey' => $detailRow['pkey'],
            'headerkey' => $detailRow['refkey'],
            'socode' => $rsJOHeaderCol[0]['code'],
            'trdate' => $rsJOHeaderCol[0]['trdate'],
            'routefromkey' => $rsJOHeaderCol[0]['stuffinglocationfromkey'],
            'routetokey' => $rsJOHeaderCol[0]['stuffinglocationkey'],
            'routefromname' => $rsJOHeaderCol[0]['stuffinglocationfromname'],
            'routetoname' => $rsJOHeaderCol[0]['locationname'],
            'itemkey' => $detailRow['itemkey'],
            'itemname' => $detailRow['itemname'],
            'qtyinbaseunit' => $detailRow['qtyinbaseunit'],
            'priceinunit' => $detailRow['priceinunit'],
            'total' => $detailRow['total'],
            'indexkey' => $rsJOHeaderCol[0]['stuffinglocationfromkey'].'-'. $rsJOHeaderCol[0]['stuffinglocationkey'].'-'. $detailRow['itemkey'].'-'. $detailRow['total']
        ));

    }

    $arrJOSummary = array();

    
        $arrJobOrderDataCol = $obj->reindexDetailCollections($arrJobOrderData, 'indexkey');
        foreach($arrJobOrderDataCol as $index => $row) {
        
        if($type == 'perioddate') {

            usort($row, function ($a, $b) {
                return strtotime($a['trdate']) <=> strtotime($b['trdate']);
            });

            $minDate = $row[0]['trdate'];
            $maxDate = $row[count($row) - 1]['trdate'];


            $period = $obj->formatDBDate($minDate, 'd') . ' - ' . $obj->formatDBDate($maxDate, 'd F Y');
            $route = trim($row[0]['routetoname']);
            $truckType = $row[0]['itemname'];
            $price = $row[0]['priceinunit'];

            $tripCount = count($row);
            $total = array_sum(array_column($row, 'total'));

            array_push($arrJOSummary, 
                    array(
                        // 'period' => $period,
                        'period' => $obj->toLocalDate($invoiceDate),
                        'route' => $route,
                        'trucktype' => $truckType,
                        'triptotal' => $tripCount,
                        'price' => $price, 
                        'grandtotal' => $total,
                        'refkey' => 0
                    ));

            } else if($type == 'invoicedate') {

                
                foreach($row as $rowData) {
             
                    $jobOrderDate = $obj->formatDBDate($rowData['trdate'], 'd F Y');
                    
                    array_push($arrJOSummary, 
                        array(
                            'period' => $obj->toLocalDate($jobOrderDate),
                            'route' => $rowData['routetoname'],
                            'trucktype' => $rowData['itemname'],
                            'triptotal' => 1, //per jo
                            'price' => $rowData['priceinunit'], 
                            'grandtotal' => $rowData['total'],
                            'refkey' => $rowData['headerkey']
                        ));
                }

            }

        }

 


    $invoiceTo = '<span style="font-weight:bold;">' . $rsCustomer[0]['name'] . '</span><br><span>' . nl2br($rsCustomer[0]['address']) . '</span><br><span>NPWP : ' . $rsCustomer[0]['taxid'] . '</span>';
    if ($rs[0]['invoiceto'] == 1) {
        $invoiceTo = '<span style="font-weight:bold;">' . $rsCustomer[0]['name'] . '</span><br><span>' . nl2br($rsCustomer[0]['address']) . '</span><br><span>NPWP : ' . $rsCustomer[0]['taxid'] . '</span>';
    } else {
        // kalo bill ke consignee
        $totalRs = count($rsDetail);
        for ($i = 0; $i < $totalRs; $i++) {
            if (!empty($rsDetail[$i]['salesorderkey'])) {
                $rsSOHeader = $truckingServiceOrder->getDataRowById($rsDetail[$i]['salesorderkey']);
                $rsConsignee = $consignee->getDataRowById($rsSOHeader[0]['consigneekey']);
                $invoiceTo = '<span style="font-weight:bold;">' . $rsConsignee[0]['name'] . '</span> <br>' . nl2br($rsConsignee[0]['address']);
                break;
            }
        }

    }

    $seller = '<span style="font-weight:bold;">' . $obj->loadSetting('companyName') . '</span> <br> <span>' . nl2br($obj->loadSetting('companyAddress')) . '</span><br><span>NPWP : ' . $obj->loadSetting('companyTaxRegistrationNumber') . '</span>';

    $html = $obj->printSetting['defaultStyle'];

    $html .= '<style>
        .border-left { border-left: 1px solid #333; }
        .border-top { border-top: 1px solid #333; }
        .border-bottom { border-bottom: 1px solid #333; }
        .border-right { border-right: 1px solid #333; }
        .font-bold { font-weight: bold; }
        .border-left-top-right-bottom { border-left: 1px solid #333; border-top: 1px solid #333; border-right: 1px solid #333; border-bottom: 1px solid #333; }
        .border-top-right-bottom { border-top: 1px solid #333; border-right: 1px solid #333; border-bottom: 1px solid #333; }
        .border-left-right-bottom { border-left: 1px solid #333; border-right: 1px solid #333; border-bottom: 1px solid #333; }
        .border-right-bottom { border-right: 1px solid #333; border-bottom: 1px solid #333; }
        .align-center { text-align: center; }
        .align-right { text-align: right; }
        .bg-blue { background-color: #007bff; }
        .bg-red { background-color: #dc3545; }
        .font-bold { font-weight: bold; }
    </style>';

    $html .= '<table width="100%">
            <tr>
                <td style="width:300px;">
                <table cellpadding="2">
                    <tr><td class="border-left border-top border-right border-bottom bg-red font-bold">&nbsp;SOLD TO</td></tr>
                    <tr><td class="border-left border-right border-bottom">' . $invoiceTo . '</td></tr>
                </table>
                </td>
                <td style="width:125px;"></td>
                <td style="width:250px;">
                    <table cellpadding="2">
                    <tr><td class="border-left border-top border-right border-bottom bg-red font-bold">&nbsp;SELLER</td></tr>
                    <tr><td class="border-left border-right border-bottom">' . $seller . '</td></tr>
                </table>
                </td>
            </tr>
    </table>';

    $html .= '<div style="clear:both"></div>';
    $html .= '<div style="clear:both"></div>';

    $html .= '<table width="100%">
                <tr>
                    <td style="width:300px;"></td>
                    <td style="width:165px;"></td>
                    <td style="width:210px;">
                        <table cellpadding="2">
                            <tr><td style="width:40px;" class="font-bold">No PO</td><td style="width:10px;text-align:center;">:</td><td style="width:150px"class="font-bold">'.$rs[0]['reference1'].'</td></tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td style="width:300px;">
                        <table cellpadding="2">
                            <tr><td class="font-bold align-center" style="text-decoration:underline;">INVOICE</td></tr>
                            <tr><td class="font-bold align-center" style="text-decoration:underline;">' . $rs[0]['code'] . '</td></tr>
                        </table>
                    </td>
                    <td style="width:125px;"></td>
                    <td style="width:250px;"></td>
                </tr>
            </table>';

    $widthNo = 'width:30px;';
    $widthPeriod = ($type == 'perioddate') ? 'width:145px;' : 'width:90px;';
    $widthRoute = ($type == 'perioddate') ? 'width:165px;' : 'width:155px;';
    $widthTruckType = 'width:75px;';
    if($type == 'invoicedate') {
        $widthTruckNumber = 'width:80px;';
    }
    $widthTotalTrip = 'width:75px;';
    $widthPrice = 'width:75px;';
    $widthTotal = ($type == 'perioddate') ? 'width:110px;' : 'width:95px;';

    $html .=    '<table cellpadding="4" width="100%">
                    <thead>
                        <tr>
                            <th class="bg-blue  font-bold align-center border-left-top-right-bottom" style="' . $widthNo . '">No</th>
                            <th class="bg-blue  font-bold align-center border-top-right-bottom" style="' . $widthPeriod . '">'. $labelDate .'</th>
                            <th class="bg-blue  font-bold align-center border-top-right-bottom" style="' . $widthRoute . '">Route</th>
                            <th class="bg-blue  font-bold align-center border-top-right-bottom" style="' . $widthTruckType . '">Truck Type</th>
                        ';
                    if($type == 'invoicedate') {
                        $html .='
                            <th class="bg-blue  font-bold align-center border-top-right-bottom" style="' . $widthTruckNumber . '">No Truck</th>
                        ';
                    }
                    $html .='
                            <th class="bg-blue  font-bold align-center border-top-right-bottom" style="' . $widthTotalTrip . '">Jumlah Trip</th>
                            <th class="bg-blue  font-bold align-center border-top-right-bottom" style="' . $widthPrice . '">Harga</th>
                            <th class="bg-blue  font-bold align-center border-top-right-bottom" style="' . $widthTotal . '">Total Harga</th>
                        </tr>
                    </thead>
                    <tbody>';
                    
                    $no=1;
                    foreach($arrJOSummary as $rowData) {
                        $html .='
                            <tr>
                                <td class="border-left-right-bottom align-center" style="' . $widthNo . '">'. $no .'</td>
                                <td class="border-right-bottom align-center" style="' . $widthPeriod . '">'. $rowData['period'] .'</td>
                                <td class="border-right-bottom align-center" style="' . $widthRoute . '">'. $rowData['route'] .'</td>
                                <td class="border-right-bottom align-center" style="' . $widthTruckType . '">'. $rowData['trucktype'] .'</td>
                                
                                ';

                            if($type == 'invoicedate') {

                                $rsWOCol = $rsWOCols[$rowData['refkey']];
                                $policeNumber = ($rsWOCol[0]['isoutsource'] ? $rsWOCol[0]['outsourcecarregistrationnumber'] : $rsWOCol[0]['policenumber']);

                                $html.='
                                    <td class="border-right-bottom align-center" style="' . $widthTruckNumber . '">'. $policeNumber .'</td>
                                ';
                            }

                            $html .='
                                <td class="border-right-bottom align-right" style="' . $widthTotalTrip . '">' . $obj->formatNumber($rowData['triptotal']) . '</td>
                                <td class="border-right-bottom align-right" style="' . $widthPrice . '">' . $obj->formatNumber($rowData['price']) . '</td>
                                <td class="border-right-bottom align-right" style="' . $widthTotal . '">'. $obj->formatNumber($rowData['grandtotal']) .'</td>
                            </tr>
                        ';
                        $no++;
                    }

                    $total = $rs[0]['subtotal'];
                    $dpp = $total * (11/12);

                    $discount = $rs[0]['finaldiscount'];
                    $discountType = $rs[0]['finaldiscounttype'];

                    $discountAmount = $discount;
                    if($discountType == 2) {
                        $discountAmount = $total * ($discount / 100);
                    }

                    $taxValue = $rs[0]['taxvalue'];

                    $stampFee = $rs[0]['stampfee'];

                    $totalDownpayment = $rs[0]['totaldownpayment'];

                    $grandTotal = $rs[0]['outstanding'];

                    if($type == 'invoicedate') {
                        $colspan = 7;
                    } else {
                        $colspan = 6;
                    }

                    $html .= '<tr>
                        <td colspan="'.$colspan.'" class="border-left-right-bottom align-center font-bold">Total</td>
                        <td class="border-right-bottom align-right font-bold">'. $obj->formatNumber($total) .'</td>
                    </tr>';

                    if($discountAmount > 0) {
                        $html .= '<tr>
                            <td colspan="'.$colspan.'" class="border-left-right-bottom align-center font-bold">Diskon</td>
                            <td class="border-right-bottom align-right font-bold">'. $obj->formatNumber($discountAmount) .'</td>
                        </tr>';
                    }

                    $html .= '<tr>
                        <td colspan="'.$colspan.'" class="border-left-right-bottom align-center font-bold">DPP</td>
                        <td class="border-right-bottom align-right font-bold">'. $obj->formatNumber($dpp) .'</td>
                    </tr>';

                    if ($taxValue > 0) {
                        $html .= '<tr>
                            <td colspan="'.$colspan.'" class="border-left-right-bottom align-center font-bold">PPN</td>
                            <td class="border-right-bottom align-right font-bold">' . $obj->formatNumber($taxValue) . '</td>
                        </tr>';
                    }

                    if ($stampFee > 0) {
                        $html .= '<tr>
                            <td colspan="'.$colspan.'" class="border-left-right-bottom align-center font-bold">Biaya Materai</td>
                            <td class="border-right-bottom align-right font-bold">' . $obj->formatNumber($stampFee) . '</td>
                        </tr>';
                    }

                    if ($totalDownpayment > 0) {
                        $html .= '<tr>
                            <td colspan="'.$colspan.'" class="border-left-right-bottom align-center font-bold">Uang Muka</td>
                            <td class="border-right-bottom align-right font-bold">' . $obj->formatNumber($totalDownpayment) . '</td>
                        </tr>';
                    }

                    $html .= '<tr>
                        <td colspan="'.$colspan.'" class="border-left-right-bottom align-center font-bold">Grand Total</td>
                        <td class="border-right-bottom align-right font-bold">'. $obj->formatNumber($grandTotal) .'</td>
                    </tr>';

                    $html.='
                    </tbody>
                </table>';



    $html .= '<div style="clear:both"></div>';

    $sayNumber = $obj->sayNumber($grandTotal);

    $html .= '<table cellpadding="2" width="100%">
        <tr>
            <td style="width:50px;"></td>
            <td class="font-bold" style="width:100px;">Terbilang:</td>
        </tr>
        <tr>
            <td style="width:50px;"></td>
            <td class="border-left-top-right-bottom align-center" style="width:410px;">' . ucwords($sayNumber) . ' Rupiah</td>
        </tr>

        <tr>
            <td style="width:50px;"></td>
            <td style="width:410px;"></td>
            <td class="align-center font-bold" >Bekasi, ' . $obj->toLocalDate($obj->formatDBDate($rs[0]['trdate'], 'd F Y')) . '</td>
        </tr>

        <tr>
            <td ></td><td style="height:100px;"></td><td class=""></td>
        </tr>
        <tr>
            <td style="width:50px;"></td>
            <td style="width:410px;"></td>
            <td class="align-center">'. (empty($invoiceSignature) ? 'Lana Lainufar' : $invoiceSignature) .'</td>
        </tr>
        
        <tr>
            <td style="width:400px">BANK ' . strtoupper($bankName) . ' CAB. ' . strtoupper($branch) . '</td>
        </tr>
        <tr>
            <td style="width:400px">A/N : ' . strtoupper($accountName) . '</td>
        </tr>
        <tr>    
            <td style="width:400px">A/C : ' . $accountNumber . '</td>
        </tr>

    </table>';

    return $html;   
}

?>
