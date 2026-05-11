<?php

$PRINT_SETTINGS = array(
    'showPrintHeader' => false,
    'marginFooter' => 20,
    'footer' => '',
    'pdfMarginHeader' => 40,
    'paperSetting' => 'A4'
);

includeClass('TruckingServiceOrderInvoice.class.php');
$truckingServiceOrderInvoice = createObjAndAddToCol(new TruckingServiceOrderInvoice());

$obj = $truckingServiceOrderInvoice;

$generateReportContent = function ($dataset) {

    $obj = new TruckingServiceOrderInvoice();
    $truckingServiceOrder = new TruckingServiceOrder();
    $truckingServiceWorkOrder = new TruckingServiceWorkOrder();
    $paymentMethod = new PaymentMethod();
    $customCode = new CustomCode();
    $customer = new Customer();
    $currency = new Currency();
    $truckingService = new Service();
    $consignee = new Consignee();

    $rs = $dataset['rs'];

    $rsCustomer = $customer->searchData('', '', true, ' and ' . $customer->tableName . '.pkey = ' . $obj->oDbCon->paramString($rs[0]['customerkey']) . '');
    $customerName = $rsCustomer[0]['taxregistrationname'] ?: $rsCustomer[0]['name'] ?: '';
    $customerAddress = $rsCustomer[0]['taxregistrationaddress'] ?: $rsCustomer[0]['address'] ?: '';


    $rsPaymentMethod = $paymentMethod->getDataRowById($rs[0]['companybankkey']);
    $rsDetail = $obj->getDetailWithRelatedInformation($rs[0]['pkey'],'','order by '.$obj->tableNameDetail.'.orderlist asc, '.$obj->tableNameDetail.'.pkey asc');

    $bankName = $rsPaymentMethod[0]['bankname'];
    $branch = $rsPaymentMethod[0]['branch'];
    $accountName = $rsPaymentMethod[0]['bankaccountname'];
    $accountNumber = $rsPaymentMethod[0]['bankaccountnumber'];

    $arrSOKey = array_column($rsDetail, 'salesorderkey');

    $rsJobOrder = $truckingServiceOrder->searchData('', '', true, ' and ' . $truckingServiceOrder->tableName . '.pkey in (' . $obj->oDbCon->paramString($arrSOKey, ',') . ') ');
    $rsJobOrderCols = $obj->reindexDetailCollections($rsJobOrder, 'pkey');

    $rsJODetail = $truckingServiceOrder->getDetailWithRelatedInformation($arrSOKey);
    $rsJODetailCols = $obj->reindexDetailCollections($rsJODetail, 'refkey');

    $rsWorkOrder = $truckingServiceWorkOrder->searchData('', '', true, ' and ' . $truckingServiceWorkOrder->tableName . '.statuskey = 3 and ' . $truckingServiceWorkOrder->tableName . '.refkey in (' . $obj->oDbCon->paramString($arrSOKey, ',') . ') ');
    $rsWorkOrderCols = $obj->reindexDetailCollections($rsWorkOrder, 'refkey');

    $rsItemDetail = $obj->getItemDetail($rs[0]['pkey'], 'refheaderkey');
    $rsItemDetailCols = $obj->reindexDetailCollections($rsItemDetail, 'refkey');

    $rsTruckingService = $truckingService->searchData('', '', true, ' and ' . $truckingService->tableName . '.statuskey =1 ');
    $rsTruckingServiceCols = $obj->reindexDetailCollections($rsTruckingService, 'pkey');

    $rsJOSellingDetail = $truckingServiceOrder->getSellingCostDetail($arrSOKey);
    $rsJOSellingDetailCols = $obj->reindexDetailCollections($rsJOSellingDetail, 'refkey');

    $rsCurrency = $currency->getDataRowById($rs[0]['currencykey']);

    $invoiceTo = $customerName . '<br><span>' . nl2br($customerAddress) . '</span>';
    if ($rs[0]['invoiceto'] == 1) {
        $invoiceTo = '<span style="font-weight:bold;">' . $customerName . '</span><br><span>' . nl2br($customerAddress) . '</span>';
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
    $invoiceSignature = $rs[0]['invoicesignaturename'];

    $seller = '<span style="font-weight:bold;">' . $obj->loadSetting('companyName') . '</span> <br>' . nl2br($obj->loadSetting('companyAddress'));

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
        .bg-blue { background-color: 	#0080ff; }
        .bg-red { background-color: #dc3545; }
    </style>';

    $html .= '<table width="100%">
            <tr>
                <td style="width:250px;">
                <table cellpadding="2">
                    <tr><td class="bg-color-grey font-bold border-left border-top border-right border-bottom bg-red">&nbsp;SOLD TO</td></tr>
                    <tr><td class="border-left border-right">' . $invoiceTo . '</td></tr>
                    <tr><td class="border-left border-right border-bottom">NPWP : ' . $rsCustomer[0]['taxid'] . '</td></tr>
                </table>
                </td>
                <td style="width:120px;"></td>  
                <td style="width:300px;">
                    <table cellpadding="2">
                    <tr><td class="bg-color-grey font-bold border-left border-top border-right border-bottom bg-red">&nbsp;SELLER</td></tr>
                    <tr><td class="border-left border-right">' . $seller . '</td></tr>
                    <tr><td class="border-left border-right border-bottom">NPWP : ' . $obj->loadSetting('companyTaxRegistrationNumber') . '</td></tr>
                </table>
                </td>
            </tr>
    </table>';

    $html .= '<div style="clear:both"></div>';
    $html .= '<div style="clear:both"></div>';

    $html .= '<table width="100%">
            <tr>
                <td style="width:300px;"></td>
                <td style="width:125px;"></td>
                <td style="width:210px;">
                    <table cellpadding="2">';
                    
                    if (!empty($rsJobOrder[0]['donumber'])) {
                        $html .= '<tr><td style="width:100px;" class="font-bold">Ref Num</td><td style="width:10px;text-align:center;">:</td><td style="width:150px"class="font-bold">'.$rsJobOrder[0]['donumber'].'</td></tr>';
                    }
                    if (!empty($rs[0]['reference1'])) {
                        $html .= '<tr><td style="width:100px;" class="font-bold">No PO</td><td style="width:10px;text-align:center;">:</td><td style="width:150px"class="font-bold">'.$rs[0]['reference1'].'</td></tr>';
                    }

                    $html .= '</table>
                </td>
            </tr>
            <tr>
                <td style="width:100px;"></td>
                <td style="width:300px;"><table cellpadding="2">
                    <tr><td class="font-bold align-center title" style="text-decoration:underline;">INVOICE</td></tr>
                    <tr><td class="font-bold align-center subtitle" style="text-decoration:underline;">' . $rs[0]['code'] . '</td></tr>
                </table></td>
                <td style="width:300px;"></td>
            </tr>
    </table>';
    $html .= '<div style="clear:both"></div>';

    $widthNo = 'width:20px;';
    $widthDate = 'width:100px;';
    $widthRoute = 'width:100px;';
    $widthKet = 'width:220px;';
    $widthTruckType = 'width:80px;';
    $widthTruckNo = 'width:90px;';
    $widthPrice = 'width:120px;';
    $widthAddCost = 'width:90px;';
    $widthTotalPrice = 'width:125px;';

    $fontSize = 'font-size:0.9em;';

    $html .= '<table cellpadding="2" width="100%">
            <thead>
                <tr>
                    <th class="border-left-top-right-bottom align-center font-bold bg-blue" style="' . $widthNo . $fontSize . '">No</th>
                    <th class="border-top-right-bottom align-center font-bold bg-blue" style="' . $widthDate . $fontSize . '">Periode</th>
                    <th class="border-top-right-bottom align-center font-bold bg-blue" style="' . $widthKet . $fontSize . '">Description</th>
                    <th class="border-top-right-bottom align-center font-bold bg-blue" style="' . $widthPrice . $fontSize . '">Rate Per Unit / Bulan</th>
                    <th class="border-top-right-bottom align-center font-bold bg-blue" style="' . $widthTruckNo . $fontSize . '">Jumlah Unit / Manpower</th>
                    <th class="border-top-right-bottom align-center font-bold bg-blue" style="' . $widthTotalPrice . $fontSize . '">Total Harga</th>
                </tr>
            </thead>
            <tbody>
        ';
        $no = 0;
    for ($i = 0; $i < count($rsDetail); $i++) {

        $rsJO = $rsJobOrderCols[$rsDetail[$i]['salesorderkey']];
        $rsWO = $rsWorkOrderCols[$rsDetail[$i]['salesorderkey']];
        $rsItemDetailCol = $rsItemDetailCols[$rsDetail[$i]['pkey']];
        $rsJOSellingDetailCol = $rsJOSellingDetailCols[$rsDetail[$i]['salesorderkey']] ?? [];
        $rsJODetailCol = $rsJODetailCols[$rsDetail[$i]['salesorderkey']];
        $rsJODetailCol = $obj->reindexDetailCollections($rsJODetailCol, 'pkey');
        $rsJOSellingDetailCol = $obj->reindexDetailCollections($rsJOSellingDetailCol, 'pkey');
        
        
        $cityArea = $rsJO[0]['stuffinglocationfromname'] . ' - ' . $rsJO[0]['locationname'];
        $policeNumber = ($rsWO[0]['isoutsource'] ? $rsWO[0]['outsourcecarregistrationnumber'] : $rsWO[0]['policenumber']);
        
        $truckingCost = 0;
        $additionalCost = 0;
        foreach ($rsItemDetailCol as $detailItemRow) {
            $no++;
            $salesorderDetailKey = $detailItemRow['refsodetailkey'];
            $itemkey = $detailItemRow['itemkey'];
            $trDescDetailJO = ($detailItemRow['servicecost'] == 1) ? $rsJOSellingDetailCol[$salesorderDetailKey][0]['notes'] : $rsJODetailCol[$salesorderDetailKey][0]['trdesc'];


            $html .= '
                    <tr>
                        <td class="border-left-right-bottom align-right" style="' . $widthNo  . $fontSize. '">' . ($no) . '</td>
                        <td class="border-right-bottom align-center" style="' . $widthDate . $fontSize . '">' . $obj->formatDBDate($rsDetail[$i]['trdate'], 'F Y') . '</td>
                        <td class="border-right-bottom align-center" style="' . $widthKet . $fontSize . '">' . $trDescDetailJO . '</td>
                        <td class="border-right-bottom align-right" style="' . $widthPrice . $fontSize . '">' . $obj->formatNumber($detailItemRow['priceinunit']) . '</td>
                        <td class="border-right-bottom align-center" style="' . $widthTruckNo . $fontSize . '">' . $obj->formatNumber($detailItemRow['qtyinbaseunit']) . '</td>
                        <td class="border-right-bottom align-right" style="' . $widthTotalPrice . $fontSize . '">' . $obj->formatNumber($detailItemRow['total']) . '</td>
                    </tr>
            ';
        }

        $additionalCost = ($additionalCost > 0) ? $obj->formatNumber($additionalCost) : '-';

        $stores = array_column($rsJOSellingDetailCol, 'store');
        $store = implode('+', $stores);
        

        
    }

    $total = $rs[0]['subtotal'];
     $dpp = $total * (11/12);

    $taxValue = $rs[0]['taxvalue'];
    $taxPercentage = $rs[0]['taxpercentage'];
    $isPriceIncludeTax = $rs[0]['ispriceincludetax'];

    $discountValue = $rs[0]['finaldiscount'];
    $finalDiscountType = $rs[0]['finaldiscounttype'];

    if ($finalDiscountType == 2) {
        $discountValue = $total * ($discountValue / 100);
    }

    $stampFee = $rs[0]['stampfee'];

    $tax23Percentage = $rs[0]['tax23percentage'];
    $tax23Value = $rs[0]['tax23value'];

    $grandTotal = $rs[0]['grandtotal'];

    $colspan = 5;

    $html .= '<tr>
        <td class="border-left-right-bottom align-center font-bold" colspan="' . $colspan . '" style="'. $fontSize.'">Total</td>
        <td class="border-right-bottom align-right font-bold" style="' . $widthTotalPrice . $fontSize . '">' . $obj->formatNumber($total) . '</td>
    </tr>';

    if ($discountValue > 0) {
        $html .= '<tr>
            <td class="border-left-right-bottom align-center font-bold" colspan="' . $colspan . '" style="'. $fontSize.'">Discount</td>
            <td class="border-right-bottom align-right font-bold" style="' . $widthTotalPrice . $fontSize . '">' . $obj->formatNumber($discountValue) . '</td>
        </tr>';
    }
    $html .= '<tr>
        <td class="border-left-right-bottom align-center font-bold" colspan="' . $colspan . '" style="'. $fontSize.'">DPP</td>
        <td class="border-right-bottom align-right font-bold" style="' . $widthTotalPrice . $fontSize . '">' . $obj->formatNumber($dpp) . '</td>
    </tr>';

    if ($taxValue > 0) {
        $html .= '<tr>
            <td class="border-left-right-bottom align-center font-bold" colspan="' . $colspan . '" style="'. $fontSize.'">PPN</td>
            <td class="border-right-bottom align-right font-bold" style="' . $widthTotalPrice . $fontSize . '">' . $obj->formatNumber($taxValue) . '</td>
        </tr>';
    }


    if ($stampFee > 0) {
        $html .= '<tr>
            <td class="border-left-right-bottom align-center font-bold" colspan="' . $colspan . '" style="'. $fontSize.'">Stamp Fee</td>
            <td class="border-right-bottom align-right font-bold" style="' . $widthTotalPrice . $fontSize . '">' . $obj->formatNumber($stampFee) . '</td>
        </tr>';
    }

    // if ($tax23Value > 0) {
    //     $html .= '<tr>
    //         <td class="border-left-right-bottom align-center font-bold" colspan="' . $colspan . '" style="'. $fontSize.'">PPH 23</td>
    //         <td class="border-right-bottom align-right font-bold" style="' . $widthTotalPrice . $fontSize .  '">' . $obj->formatNumber($tax23Value) . '</td>
    //     </tr>';
    // }

    $html .= '<tr>
        <td class="border-left-right-bottom align-center font-bold" colspan="' . $colspan . '">Grand Total</td>
        <td class="border-right-bottom align-right font-bold" style="' . $widthTotalPrice . '">' . $obj->formatNumber($grandTotal) . '</td>
    </tr>';

    $html .= '
            </tbody>
        </table>
    ';

    $html .= '<div style="clear:both"></div>';

    $sayNumber = $obj->sayNumber($grandTotal);

    $html .= '<table cellpadding="2" width="100%">
    <tr>
        <td style="width:480px";><table cellpadding="3">
                <tr>
                    <td class="font-bold" style="width:100px;' . $fontSize . '">Terbilang:</td>
                </tr>
                <tr>
                    <td class="border-left-top-right-bottom font-bold" style="width:470px;' . $fontSize . '">' . ucwords($sayNumber) . ' Rupiah</td>
                </tr>
            </table>
            <table cellpadding="3"><tr><td></td></tr></table>
            <table cellpadding="3">
                <tr>
                    <td style="width:300px;' . $fontSize . '">BANK ' . strtoupper($bankName) . '</td>
                </tr>
                <tr>
                    <td style="width:60px;' . $fontSize . '">A/N</td><td style="width:10px;text-align:center;' . $fontSize . '">:</td><td style="width:300px;' . $fontSize . '">' . strtoupper($accountName) . '</td>
                </tr>
                <tr>
                    <td style="width:60px;' . $fontSize . '">A/C</td><td style="width:10px;text-align:center;' . $fontSize . '">:</td><td style="width:300px;' . $fontSize . '">' . $accountNumber . '</td>
                </tr>
            </table>
        </td>
        
        <td style="width:200px";>
            <table cellpadding="3">
                <tr>
                    <td style="width:190px;" class="align-center font-bold">Bekasi, ' . $obj->toLocalDate($obj->formatDBDate($rs[0]['trdate'], 'd F Y')) . '</td>
                </tr>
                <tr><td></td></tr>
                <tr><td></td></tr>
                <tr><td></td></tr>
                <tr><td></td></tr>
                <tr><td></td></tr>
                <tr>
                    <td style="width:190px;" class="align-center font-bold">' . (empty($invoiceSignature) ? 'Kiki Ramadhani Siregar' : $invoiceSignature) . '</td>
                </tr>
            </table>
        </td>
    </tr>
    </table>';
    return $html;
}

    ?>
