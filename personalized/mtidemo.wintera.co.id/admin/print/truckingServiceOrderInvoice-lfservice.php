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
    $currency = new Currency();
    $truckingService = new Service();
    $supplier = new Supplier();
    $consignee = new Consignee();


    $rs = $dataset['rs'];

    $rsCustomer = $customer->searchData('', '', true, ' and ' . $customer->tableName . '.pkey = ' . $obj->oDbCon->paramString($rs[0]['customerkey']) . '');

    $rsPaymentMethod = $paymentMethod->getDataRowById($rs[0]['companybankkey']);
    $rsDetail = $obj->getDetailWithRelatedInformation($rs[0]['pkey']);

    $bankName = $rsPaymentMethod[0]['bankname'];
    $branch = $rsPaymentMethod[0]['branch'];
    $accountName = $rsPaymentMethod[0]['bankaccountname'];
    $accountNumber = $rsPaymentMethod[0]['bankaccountnumber'];

    $arrSOKey = array_column($rsDetail, 'salesorderkey');

    $rsJobOrder = $truckingServiceOrder->searchData('', '', true, ' and ' . $truckingServiceOrder->tableName . '.pkey in (' . $obj->oDbCon->paramString($arrSOKey, ',') . ') ');
    $rsJobOrderCols = $obj->reindexDetailCollections($rsJobOrder, 'pkey');

    $rsJOSellingDetail = $truckingServiceOrder->getSellingCostDetail($arrSOKey);
    $rsJOSellingDetailCols = $obj->reindexDetailCollections($rsJOSellingDetail, 'refkey');

    $rsJODetail = $truckingServiceOrder->getDetailWithRelatedInformation($arrSOKey);
    $rsJODetailCols = $obj->reindexDetailCollections($rsJODetail, 'refkey');

    $rsWorkOrder = $truckingServiceWorkOrder->searchData('', '', true, ' and ' . $truckingServiceWorkOrder->tableName . '.statuskey = 3 and ' . $truckingServiceWorkOrder->tableName . '.refkey in (' . $obj->oDbCon->paramString($arrSOKey, ',') . ') ');
    $rsWorkOrderCols = $obj->reindexDetailCollections($rsWorkOrder, 'refkey');

    $rsItemDetail = $obj->getItemDetail($rs[0]['pkey'], 'refheaderkey');
    $rsItemDetailCols = $obj->reindexDetailCollections($rsItemDetail, 'refkey');

    $rsTruckingService = $truckingService->searchData('', '', true, ' and ' . $truckingService->tableName . '.statuskey =1 ');
    $rsTruckingServiceCols = $obj->reindexDetailCollections($rsTruckingService, 'pkey');

    $rsSupplier = $supplier->searchDataRow(array($supplier->tableName.'.pkey', 
                                                $supplier->tableName.'.code',
                                                $supplier->tableName.'.name',
                                                $supplier->tableName.'.statuskey'), ' and ' . $supplier->tableName.'.statuskey = 1');
    $rsSupplierCol = $obj->reindexDetailCollections($rsSupplier,'pkey');

    $invoiceSignature = $rs[0]['invoicesignaturename'];


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

    $arrTruckingCost = array();
    $arrAdditionalCost  = array();

    foreach($rsItemDetail as $detailItemRow) {

        $itemkey = $detailItemRow['itemkey'];

        if(isset($rsTruckingServiceCols[$itemkey])) {
            array_push(
                $arrTruckingCost, array(
                    'refkey' => $detailItemRow['refkey'],
                    'itemkey' => $detailItemRow['itemkey'],
                    'total' => $detailItemRow['total']
                )
            );
        }

    }

    $seller = '<span style="font-weight:bold;">' . $obj->loadSetting('companyName') . '</span> <br> <span>' . nl2br($obj->loadSetting('companyAddress')) . '</span><br><span>NPWP : ' . $obj->loadSetting('companyTaxRegistrationNumber') . '</span>';


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
    $html .= '<div style="clear:both"></div>';

    $html .= '<table width="100%">
            <tr>
                <td style="width:300px;"></td>
                <td style="width:165px;"></td>
                <td style="width:210px;">
                    
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

    $widthNo = 'width:25px;';
    $widthDate = 'width:75px;';
    $widthTruckType = 'width:70px;';
    $widthPoliceNumber = 'width:75px;';
    $widthManifestLoad = 'width:70px;';
    $widthDesc = 'width:120px;';
    $widthPrice = 'width:80px;';
    $widthTKBM = 'width:80px;';
    $widthTotal = 'width:80px;';

    $height = 'line-height:15px;';
    $fontSize = 'font-size:0.9em;';

    $html .= '<table cellpadding="4" width="100%">
                <thead>
                    <tr>
                        <th class="bg-blue  font-bold align-center border-left-top-right-bottom" style="' . $widthNo . $height . $fontSize . '">No</th>
                        <th class="bg-blue  font-bold align-center border-top-right-bottom" style="' . $widthDate . $height . $fontSize . '">Tanggal</th>
                        <th class="bg-blue  font-bold align-center border-top-right-bottom" style="' . $widthTruckType . $height . $fontSize . '">Truck Type</th>
                        <th class="bg-blue  font-bold align-center border-top-right-bottom" style="' . $widthPoliceNumber . $height . $fontSize . '">Nopol</th>
                        <th class="bg-blue  font-bold align-center border-top-right-bottom" style="' . $widthManifestLoad . $height . $fontSize . '">Nomor Load Manifest</th>
                        <th class="bg-blue  font-bold align-center border-top-right-bottom" style="' . $widthDesc . $height . $fontSize . '">Keterangan</th>
                        <th class="bg-blue  font-bold align-center border-top-right-bottom" style="' . $widthPrice . $height . '">Harga</th>
                        <th class="bg-blue  font-bold align-center border-top-right-bottom" style="' . $widthTKBM . $height . $fontSize .'">TKBM</th>
                        <th class="bg-blue  font-bold align-center border-top-right-bottom" style="' . $widthTotal . $height . $fontSize .'">Total Harga</th>
                    </tr>
                </thead>
                <tbody>';
            
            $no = 1;
            for ($i = 0; $i < count($rsDetail); $i++) {

                $rsWO  = $rsWorkOrderCols[$rsDetail[$i]['salesorderkey']];
                $rsJO = $rsJobOrderCols[$rsDetail[$i]['salesorderkey']];

                $policeNumber = ($rsWO[0]['isoutsource'] ? $rsWO[0]['outsourcecarregistrationnumber'] : $rsWO[0]['policenumber']);

                $rsItemDetailCol = $rsItemDetailCols[$rsDetail[$i]['pkey']];

                $truckingCost = 0;
                $additionalCost = 0;
                foreach ($rsItemDetailCol as $detailItemRow) {
                    $itemkey = $detailItemRow['itemkey'];
                    if (isset($rsTruckingServiceCols[$itemkey])) {
                        $truckingCost += $detailItemRow['total'];
                    } else {
                        $additionalCost += $detailItemRow['total'];
                    }
                }

                $additionalCost = ($additionalCost > 0) ? $obj->formatNumber($additionalCost) : '-';

                $html .= '<tr>
                        <td class="border-left border-bottom border-right align-center" style="' . $widthNo. $fontSize .'">'. $no .'</td>
                        <td class="border-right border-bottom align-center" style="' . $widthDate . $fontSize .'">'. $obj->formatDBDate($rsDetail[$i]['trdate'], 'd/m/Y') .'</td>
                        <td class="border-right border-bottom align-center" style="' . $widthTruckType . $fontSize .'">'. $rsWO[0]['containername'].'</td>
                        <td class="border-right border-bottom align-center" style="' . $widthPoliceNumber . $fontSize .'">'. $policeNumber.'</td>
                        <td class="border-right border-bottom align-center" style="' . $widthManifestLoad . $fontSize .'">'. $rsJO[0]['donumber'] .'</td>
                        <td class="border-right border-bottom align-center" style="' . $widthDesc . $fontSize .'">'.$rsJO[0]['trdesc'].'</td>
                        <td class="border-right border-bottom align-right" style="' . $widthPrice . $fontSize .'">' . $obj->formatNumber($truckingCost) . '</td>
                        <td class="border-right border-bottom align-right" style="' . $widthTKBM . $fontSize .'">' . $additionalCost . '</td>
                        <td class="border-right border-bottom align-right" style="' . $widthTotal .'">' . $obj->formatNumber($rsDetail[$i]['amount']) . '</td>
                        <td></td>
                </tr>';

                $no++;
            }

        $subTotal = $rs[0]['subtotal'];
        $beforeTaxTotal = $rs[0]['beforetaxtotal'];
        $taxValue = $rs[0]['taxvalue'];
        $taxPercentage = $rs[0]['taxpercentage'];
        $isPriceIncludeTax = $rs[0]['ispriceincludetax'];

        $discountValue = $rs[0]['finaldiscount'];
        $finalDiscountType = $rs[0]['finaldiscounttype'];

        $tax23Percentage = $rs[0]['tax23percentage'];
        $tax23Value = $rs[0]['tax23value'];


        if ($finalDiscountType == 2) {
            $discountValue = $subTotal * ($discountValue / 100);
        }

        $stampFee = $rs[0]['stampfee'];

        $grandTotal = $rs[0]['grandtotal'];

        $colspan = 8;

        $html .= '
            <tr>
                <td colspan="'. $colspan .'" style="line-height:18px;' . $fontSize . '" class="border-left-right-bottom font-bold align-center">Total</td>
                <td class="border-right-bottom align-right font-bold    " style="line-height:18px;' . $fontSize . '">' . $obj->formatNumber($subTotal) . '</td>
            </tr>
        ';


    if ($discountValue > 0) {

        $html .= '
            <tr>
                <td colspan="'. $colspan .'" style="line-height:18px;' . $fontSize . '" class="border-left-right-bottom font-bold align-center">Discount</td>
                <td class="border-right-bottom align-right font-bold    " style="line-height:18px;' . $fontSize . '">' . $obj->formatNumber($discountValue) . '</td>
            </tr>
        ';

    }

    $html .= '
        <tr>
            <td colspan="'. $colspan .'" style="line-height:18px;' . $fontSize . '" class="border-left-right-bottom font-bold align-center">Tax Basis</td>
            <td class="border-right-bottom align-right font-bold    " style="line-height:18px;' . $fontSize . '">' . $obj->formatNumber($beforeTaxTotal) . '</td>
        </tr>
    ';

    if ($taxValue > 0) {

        $html .= '
            <tr>
                <td colspan="'. $colspan .'" style="line-height:18px;'.$fontSize.'" class="border-left-right-bottom font-bold align-center">VAT</td>
                <td class="border-right-bottom align-right font-bold    " style="line-height:18px;'.$fontSize.'">' . $obj->formatNumber($taxValue) . '</td>
            </tr>
        ';

    }

    if ($stampFee > 0) {
        $html .= '
        <tr>
            <td colspan="'. $colspan .'" style="line-height:18px;'.$fontSize.'" class="border-left-right-bottom font-bold align-center">Stamp Fee</td>
            <td class="border-right-bottom align-right font-bold    " style="line-height:18px;'.$fontSize.'">' . $obj->formatNumber($stampFee) . '</td>
        </tr>
    ';
    }

    if ($tax23Value > 0) {
        $html .= '
            <tr>
                <td colspan="'. $colspan .'" style="line-height:18px;'.$fontSize.'" class="border-left-right-bottom font-bold align-center">PPh 23</td>
                <td class="border-right-bottom align-right font-bold    " style="line-height:18px;'.$fontSize.'">' . $obj->formatNumber($tax23Value) . '</td>
            </tr>
        ';
    }
    

    $html .= '
        <tr>
            <td colspan="' . $colspan . '" style="line-height:18px;'.$fontSize.'" class="border-left-right-bottom font-bold align-center">Grand Total</td>
            <td class="border-right-bottom align-right font-bold    " style="line-height:18px;'.$fontSize.'">' . $obj->formatNumber($grandTotal) . '</td>
        </tr>
    ';

    $html .= '
        </tbody>
    </table>';

    $html .= '<div style="clear:both"></div>';

    $sayNumber = $obj->sayNumber($grandTotal);

    $html .= '<table cellpadding="3" width="100%">
        <tr>
            <td style="width:50px;"></td>
            <td class="font-bold" style="width:100px;' . $fontSize . '">Terbilang:</td>
        </tr>
        <tr>
            <td style="width:50px;"></td>
            <td class="border-left-top-right-bottom align-center font-bold" style="width:480px;'.$fontSize.'">' . ucwords($sayNumber) . ' Rupiah</td>
        </tr>

        <tr>
            <td></td>
            <td></td>
            <td></td>
        </tr>

        <tr>
            <td style="width:50px;"></td>
            <td style="width:446px;"><table>
                    <tr>
                        <td style="width:200px;'.$fontSize.'">BANK ' . strtoupper($bankName) . '</td>
                    </tr>
                    <tr>
                        <td style="width:60px;'.$fontSize.'">A/N</td><td style="width:10px;text-align:center">:</td><td style="width:400px;'.$fontSize.'">' . strtoupper($accountName) . '</td>
                    </tr>
                    <tr>
                        <td style="width:60px;'.$fontSize.'">A/C</td><td style="width:10px;text-align:center">:</td><td style="width:400px;'.$fontSize.'">' . $accountNumber . '</td>
                    </tr>
                    <tr>
                        <td style="width:60px;'.$fontSize.'">Currency</td><td style="width:10px;text-align:center">:</td><td style="width:400px;'.$fontSize.'">' . $rsCurrency[0]['name'] . '</td>
                    </tr>
            </table></td>
            <td style="width:120px;' . $fontSize . '" class="align-center font-bold">Bekasi, ' .$obj->toLocalDate($obj->formatDBDate($rs[0]['trdate'], 'd F Y')). '</td>
        </tr>

        <tr>
            <td style="width:50px;"></td><td style="width:720px;"></td><td style="width:200px;" class=""></td>
        </tr>
        <tr>
            <td style="width:50px;"></td><td style="width:720px;"></td><td style="width:200px;" class=""></td>
        </tr>
        <tr>
            <td style="width:50px;"></td><td style="width:720px;"></td><td style="width:200px;" class=""></td>
        </tr>
        <tr>
            <td style="width:55px;"></td>
            <td style="width:400px;"></td>
            <td style="width:200px;' . $fontSize . '" class="align-center font-bold">'. (empty($invoiceSignature) ? 'Kiki Ramadhani Siregar' : $invoiceSignature) .'</td>
        </tr>

    </table>';
    
    return $html;  

}

?>
