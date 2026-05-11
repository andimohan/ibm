<?php

$companyName = $class->loadSetting('companyName');

$footer = '';
        // '<table cellpadding="3"><tr>
        // <td style="border-top:1px solid #333;text-align:center;">
        // <span style="font-weight:bold;font-size:17px;">'. strtoupper($companyName) .'</span>
        // <br><span style="font-weight:bold">Head Office : </span><span>Jl. Wijaya Kusuma BS 6 No. 01, Jatisampurna, Bekasi</span><br>
        // <span style="font-weight:bold;">Workshop : </span><span>Jl. Gemalapik No. 11 Pasir Sari Cikarang Selatan, Kab. Bekasi</span><br>
        // <span style="font-weight:bold;">Telp : </span><span>(021) 28671691</span>, <span style="font-weight:bold;">Email : </span><span>office@macrostransindo.com</span>, <span style="font-weight:bold;">Web : </span><span>www.macrostransindo.com</span>
        // </td>
        // </tr></table>';



  
$pdf->setCustomSettings(
    array(
        'showPrintHeader' => false,
        'marginFooter' =>20,
        'footer' => $footer,
        'pdfMarginHeader' => 40,
        'paperSetting' => 'A4'
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

    $rsCurrency = $currency->getDataRowById($rs[0]['currencykey']);

    $rsSupplier = $supplier->searchDataRow(array($supplier->tableName.'.pkey', 
                                                $supplier->tableName.'.code',
                                                $supplier->tableName.'.name',
                                                $supplier->tableName.'.statuskey'), ' and ' . $supplier->tableName.'.statuskey = 1');
    $rsSupplierCol = $obj->reindexDetailCollections($rsSupplier,'pkey');

    $invoiceTo = $rsCustomer[0]['name'] . '<br><span>' . nl2br($rsCustomer[0]['address']).'</span><br><span>NPWP : ' . $rsCustomer[0]['taxid'] . '</span>';
    if ($rs[0]['invoiceto'] == 1) {
        $invoiceTo = '<span style="font-weight:bold;">' . $rsCustomer[0]['name'] . '</span><br><span>' . nl2br($rsCustomer[0]['address']).'</span><br><span>NPWP : '. $rsCustomer[0]['taxid'] .'</span>';
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

    $companyLogo = $obj->loadSetting('companyLogo');
    $imgLetterhead = $obj->phpThumbURLSrc . 'setting/companyLogo/' . $companyLogo;

    $logo ='';// '<img src="' . $imgLetterhead . '" style="height: 140px">';
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
        .bg-color-grey { background-color: #d3d3d3; }
    </style>';
 
    $html .= '<table width="100%">
            <tr>
                <td style="width:260px;"></td>
                <td style="width:125px;"></td>
                <td style="width:300px;text-align:right;">'. $logo .'</td>
            </tr>
            <tr>
                <td style="width:220px;">
                <table cellpadding="2">
                    <tr><td class="bg-color-grey font-bold border-left border-top border-right border-bottom">&nbsp;SOLD TO</td></tr>
                    <tr><td class="border-left border-right border-bottom">' . $invoiceTo . '</td></tr>
                </table>
                </td>
                <td style="width:180px;"></td>  
                <td style="width:270px;">
                    <table cellpadding="2">
                    <tr><td class="bg-color-grey font-bold border-left border-top border-right border-bottom">&nbsp;SELLER</td></tr>
                    <tr><td class="border-left border-right">' . $seller . '</td></tr>
                    <tr><td class="border-left border-right">NPWP : '. $obj->loadSetting('companyTaxRegistrationNumber') .'</td></tr>
                    <tr><td class="border-left border-right border-bottom font-bold">VENDOR CODE : '. $rsCustomer[0]['vendorcode'] .'</td></tr>
                </table>
                </td>
            </tr>
    </table>';

    $html .= '<div style="clear:both"></div>';

    $html .= '<table width="100%">
        <tr>
                <td style="width:200px;"></td>
                <td style="width:200px;"><table cellpadding="2">
                    <tr><td class="font-bold align-center title">INVOICE</td></tr>
                    <tr><td class="font-bold align-center subtitle">' . $rs[0]['code'] . '</td></tr>
                </table></td>
                <td style="width:200px;"></td>
            </tr>
    </table>';

    $html .= '<div style="clear:both"></div>';

    $widthNo = 'width:20px;';
    $widthDate = 'width:65px;';
    $widthTypeTruck = 'width:65px;';
    $widthLicensePlate = 'width:55px;';
    $widthKewillNo = 'width:70px;';
    $widthNoDo = 'width:70px;';
    $widthSupplier = 'width:60px;';
    $widthCity = 'width:70px;';
    $widthAdditionalCost = 'width:70px;';
    $widthRate = 'width:60px;';
    $widthPrice = 'width:70px;';

    $height = 'line-height:20px;';

    $fontSize = 'font-size:0.8em;';

    $heightBody = '';

    $html .= '<table cellpadding="2" width="100%">
        <thead>
            <tr>
                <td class="font-bold align-center border-left-top-right-bottom bg-color-grey" style="' . $widthNo . $height . $fontSize.'">NO</td>
                <td class="font-bold align-center border-top-right-bottom bg-color-grey" style="' . $widthDate . $height . $fontSize.'">ORDER DATE</td>
                <td class="font-bold align-center border-top-right-bottom bg-color-grey" style="' . $widthLicensePlate . $height . $fontSize.'">NOPOL</td>
                <td class="font-bold align-center border-top-right-bottom bg-color-grey" style="' . $widthTypeTruck . $height . $fontSize.'">TRUCK TYPE</td>
                <td class="font-bold align-center border-top-right-bottom bg-color-grey" style="' . $widthKewillNo . $height . $fontSize.'">KEWILL NO</td>
                <td class="font-bold align-center border-top-right-bottom bg-color-grey" style="' . $widthNoDo  . $height . $fontSize.'">NO DO</td>
                <td class="font-bold align-center border-top-right-bottom bg-color-grey" style="' . $widthSupplier . $height . $fontSize.'">SUPPLIER</td>
                <td class="font-bold align-center border-top-right-bottom bg-color-grey" style="' . $widthCity . $height . $fontSize.'">CITY AREA</td>
                <td class="font-bold align-center border-top-right-bottom bg-color-grey" style="' . $widthRate . $height . $fontSize.'">RATE</td>
                <td class="font-bold align-center border-top-right-bottom bg-color-grey" style="' . $widthAdditionalCost . $fontSize.'">ADDITIONAL COST</td>
                <td class="font-bold align-center border-top-right-bottom bg-color-grey" style="' . $widthPrice . $height . $fontSize.'">PRICE (RP)</td>
            </tr>
        </thead>
        <tbody>
    ';

    $no = 1;
    for ($i = 0; $i < count($rsDetail); $i++) {

        $rsWO  = $rsWorkOrderCols[$rsDetail[$i]['salesorderkey']];
        $rsJO = $rsJobOrderCols[$rsDetail[$i]['salesorderkey']];
        $rsJODetail = $rsJODetailCols[$rsDetail[$i]['salesorderkey']];
        $rsJobOrderCol = $rsJobOrderCols[$rsDetail[$i]['salesorderkey']];
        $cityArea = $rsJO[0]['stuffinglocationfromname'] . ' - ' . $rsJO[0]['locationname'];
        $rsJOSellingDetailCol = $rsJOSellingDetailCols[$rsDetail[$i]['salesorderkey']];

        
        

        $rsItemDetailCol = $rsItemDetailCols[$rsDetail[$i]['pkey']];

        $truckingCost = 0;
        $additionalCost = 0;
        foreach($rsItemDetailCol as $detailItemRow) { 
            $itemkey = $detailItemRow['itemkey'];
            if(isset($rsTruckingServiceCols[$itemkey])) {
                $truckingCost += $detailItemRow['total'];
            } else {
                $additionalCost += $detailItemRow['total'];
            }
        }

        $additionalCost = ($additionalCost > 0) ? $obj->formatNumber($additionalCost) : '-';
       
        $supplierName = (isset($rsSupplierCol[$rsWO[0]['supplierkey']]) ? $rsSupplierCol[$rsWO[0]['supplierkey']][0]['name'] : '');
        $kewillNo = $rsJobOrderCol[0]['poreference'];
        $description = $rsJobOrderCol[0]['trdesc'];
        $policeNumber = ($rsWO[0]['isoutsource'] ? $rsWO[0]['outsourcecarregistrationnumber'] : $rsWO[0]['policenumber']);

        $stores = array_column($rsJOSellingDetailCol, 'store');
        $store = implode('+', $stores);
        $detailJODesc = $rsJODetail[0]['trdesc'] . (!empty($store) ? '+' . $store : '');

        $html .= '
            <tr>
                <td class="border-left-right-bottom align-right" style="' . $widthNo . $fontSize .' ">'. $no .'</td>
                <td class="border-right-bottom align-center" style="' . $widthDate .  $fontSize .'">'. $obj->formatDBDate($rsDetail[$i]['trdate'], 'd/m/Y') .'</td>
                <td class="border-right-bottom align-center" style="' . $widthLicensePlate .  $fontSize . 'text-align:center;">'.$policeNumber.'</td>
                <td class="border-right-bottom align-center" style="' . $widthTypeTruck .  $fontSize . '">' . $rsWO[0]['containername'] . '</td>
                <td class="border-right-bottom" style="' . $widthKewillNo . $fontSize . 'text-align:center;">'.$kewillNo.'</td>
                <td class="border-right-bottom align-center" style="' . $widthNoDo . $fontSize.  '">'. $rsDetail[$i]['donumber'] .'</td>
                <td class="border-right-bottom" style="' . $widthSupplier . $fontSize.  'text-align:center;">'. $description.'</td>
                <td class="border-right-bottom align-center" style="' . $widthCity . $fontSize.  '">'. $detailJODesc .'</td>
                <td class="border-right-bottom align-right" style="' . $widthRate . $fontSize.  '">'. $obj->formatNumber($truckingCost) .'</td>
                <td class="border-right-bottom align-right" style="' . $widthAdditionalCost . $fontSize.  '">'. $additionalCost .'</td>
                <td class="border-right-bottom align-right" style="' . $widthPrice . $fontSize. '">' . $obj->formatNumber($rsDetail[$i]['amount']) . '</td>
            </tr>

        ';

        $no++;
    }

    $subTotal = $rs[0]['subtotal'];
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

    $colspan = 10;

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
            <td style="width:446px;"></td>
            <td style="width:100px;' . $fontSize . '" class="align-center font-bold" >Bekasi, ' . $obj->toLocalDate($obj->formatDBDate($rs[0]['trdate'], 'd F Y')) . '</td>
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
            <td style="width:50px;"></td>
            <td style="width:400px;"></td>
            <td style="width:200px;' . $fontSize . '" class="align-center font-bold">'. (empty($invoiceSignature) ? 'Kiki Ramadhani Siregar' : $invoiceSignature) .'</td>
        </tr>
        <tr>
            <td style="width:400px">
                <table>
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
            </table>
            </td>
        </tr>

    </table>';


    return $html;

}

    ?>
