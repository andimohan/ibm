<?php

// $pdf->setCustomSettings(
// array(
//         'showPrintHeader' => '',
//         'marginFooter' => 25,
//         'footer' => '',
//         'pdfMarginHeader' => 40
//     )
// );

includeClass('TruckingServiceOrderInvoice.class.php');
$truckingServiceOrderInvoice = createObjAndAddToCol( new TruckingServiceOrderInvoice()); 

$obj = $truckingServiceOrderInvoice;
$companyName = $obj->loadSetting('companyName');

$PRINT_SETTINGS = array(
        'showPrintHeader' => false,
        'marginFooter' =>25,
        'footer' => '',
        'pdfMarginHeader' => 40,
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

    $rsCustomer = $customer->searchData('','',true, ' and '. $customer->tableName .'.pkey = '. $obj->oDbCon->paramString($rs[0]['customerkey']) .'');
    $customerName = $rsCustomer[0]['taxregistrationname'] ?: $rsCustomer[0]['name'] ?: '';
    $customerAddress = $rsCustomer[0]['taxregistrationaddress'] ?: $rsCustomer[0]['address'] ?: '';

    $rsPaymentMethod = $paymentMethod->getDataRowById($rs[0]['companybankkey']);
    $rsDetail = $obj->getDetailWithRelatedInformation($rs[0]['pkey'],'','order by  orderlist asc, socode asc');

    $bankName = $rsPaymentMethod[0]['bankname'];
    $branch = $rsPaymentMethod[0]['branch'];
    $accountName = $rsPaymentMethod[0]['bankaccountname'];
    $accountNumber = $rsPaymentMethod[0]['bankaccountnumber'];

    $arrSOKey = array_column($rsDetail, 'salesorderkey');

    $rsWorkOrder = $truckingServiceWorkOrder->searchData('','',true, ' and ' . $truckingServiceWorkOrder->tableName.'.statuskey = 3 and '. $truckingServiceWorkOrder->tableName .'.refkey in ('. $obj->oDbCon->paramString($arrSOKey,',') .') ');
    $rsWorkOrderCols = $obj->reindexDetailCollections($rsWorkOrder, 'refkey');

    $rsItemDetail = $obj->getItemDetail($rs[0]['pkey'], 'refheaderkey');
    $rsItemDetailCols = $obj->reindexDetailCollections($rsItemDetail, 'refkey');

    $rsJOHeader = $truckingServiceOrder->searchDataRow(
        array(
            $truckingServiceOrder->tableName . '.pkey',
            $truckingServiceOrder->tableName . '.code',
            $truckingServiceOrder->tableName . '.donumber'
        ),
        ' and ' . $truckingServiceOrder->tableName . '.pkey in (' . $obj->oDbCon->paramString($arrSOKey, ',') . ')'
    );
    $rsJOHeaderCols = $obj->reindexDetailCollections($rsJOHeader, 'pkey');

    $rsJODetail = $truckingServiceOrder->getDetailWithRelatedInformation($arrSOKey);
    $rsJODetailCols = $obj->reindexDetailCollections($rsJODetail, 'refkey');

    $rsSellingDetail = $truckingServiceOrder->getSellingCostDetail($arrSOKey);
    $rsSellingDetailCols = $obj->reindexDetailCollections($rsSellingDetail, 'refkey');


    $invoiceTo = '<span style="font-weight:bold;">' . $customerName . '</span><br><span>' . nl2br($customerAddress) . '</span><br><span>NPWP : ' . $rsCustomer[0]['taxid'] . '</span>';
    if ($rs[0]['invoiceto'] == 1) {
        $invoiceTo = '<span style="font-weight:bold;">' . $customerName . '</span><br><span>' . nl2br($customerAddress) . '</span><br><span>NPWP : ' . $rsCustomer[0]['taxid'] . '</span>';
    } else {
        // kalo bill ke consignee
        $totalRs = count($rsDetail);
        for ($i = 0; $i <$totalRs; $i++) {
            if (!empty($rsDetail[$i]['salesorderkey'])) {
                $rsSOHeader = $truckingServiceOrder->getDataRowById($rsDetail[$i]['salesorderkey']);
                $rsConsignee = $consignee->getDataRowById($rsSOHeader[0]['consigneekey']);
                $invoiceTo = '<span style="font-weight:bold;">'. $rsConsignee[0]['name'] . '</span> <br>' . nl2br($rsConsignee[0]['address']);
                break;
            }
        }

    }

    $invoiceSignature = $rs[0]['invoicesignaturename'];
    

    $seller = '<span style="font-weight:bold;">'. $obj->loadSetting('companyName') .'</span><br><span>' . nl2br($obj->loadSetting('companyAddress')) .'</span><br><span>NPWP : '. $obj->loadSetting('companyTaxRegistrationNumber') .'</span>';

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
    </style>';

    $html .= '<table width="100%">
            <tr>
                <td style="width:300px;">
                <table cellpadding="2">
                    <tr><td class="border-left border-top border-right border-bottom">&nbsp;SOLD TO</td></tr>
                    <tr><td class="border-left border-right border-bottom">'. $invoiceTo .'</td></tr>
                </table>
                </td>
                <td style="width:125px;"></td>
                <td style="width:250px;">
                    <table cellpadding="2">
                    <tr><td class="border-left border-top border-right border-bottom">&nbsp;SELLER</td></tr>
                    <tr><td class="border-left border-right border-bottom">'. $seller .'</td></tr>
                </table>
                </td>
            </tr>
    </table>';

    $html .='<div style="clear:both"></div>';

    $html .= '<table width="100%">
        <tr>
                <td style="width:300px;">
                <table cellpadding="2">
                    <tr><td class="font-bold align-center" style="text-decoration:underline;">INVOICE</td></tr>
                    <tr><td class="font-bold align-center" style="text-decoration:underline;">'. $rs[0]['code'] .'</td></tr>
                </table>
                </td>
                <td style="width:125px;"></td>
                <td style="width:250px;">
                </td>
            </tr>
    </table>';

    $html .='<div style="clear:both"></div>';

    $widthNo = 'width:40px;';
    $widthDate = 'width:80px;';
    $widthTypeTruck = 'width:80px;';
    $widthLicensePlate = 'width:90px;';
    $widthPasschecklistNo = 'width:175px;';
    $widthStore = 'width:110px;';
    $widthPrice = 'width:100px;';
    
    $html .='<table cellpadding="2" width="100%">
        <thead>
            <tr>
                <td class="bg-blue  font-bold align-center border-left-top-right-bottom" style="'.$widthNo.'">No</td>
                <td class="bg-blue  font-bold align-center border-top-right-bottom" style="'.$widthDate.'">Date</td>
                <td class="bg-blue  font-bold align-center border-top-right-bottom" style="'.$widthTypeTruck.'">Type Truck</td>
                <td class="bg-blue  font-bold align-center border-top-right-bottom" style="'.$widthLicensePlate.'">License Plate</td>
                <td class="bg-blue  font-bold align-center border-top-right-bottom" style="'.$widthPasschecklistNo.'">Passchecklist No</td>
                <td class="bg-blue  font-bold align-center border-top-right-bottom" style="'.$widthStore.'">Store</td>
                <td class="bg-blue  font-bold align-center border-top-right-bottom" style="'.$widthPrice.'">Price</td>
            </tr>
        </thead>
        <tbody>
    ';

    $no = 1;
    for($i=0; $i<count($rsDetail); $i++) {
    
        $rsItemDetailCol = $rsItemDetailCols[$rsDetail[$i]['pkey']];
        $rsWO = $rsWorkOrderCols[$rsDetail[$i]['salesorderkey']];

        $rsJOHeaderCol = $rsJOHeaderCols[$rsDetail[$i]['salesorderkey']];

        $rsJODetailCol = $rsJODetailCols[$rsDetail[$i]['salesorderkey']];
        $rsJODetailCol = $obj->reindexDetailCollections($rsJODetailCol, 'pkey');

        $rsSellingDetailCol = $rsSellingDetailCols[$rsDetail[$i]['salesorderkey']];
        $rsSellingDetailCol = $obj->reindexDetailCollections($rsSellingDetailCol, 'pkey');


        $policeNumber = $rsWO[0]['policenumber'];
        if ($rsWO[0]['isoutsource'] == 1) {
            $policeNumber = $rsWO[0]['outsourcecarregistrationnumber'];
        }

        foreach($rsItemDetailCol as $key => $value) {

            $store = '';
            $passchecklistNo = '';
            if($rsSellingDetailCol[$value['refsodetailkey']]) {
                $rsSellingDetails = $rsSellingDetailCol[$value['refsodetailkey']];
                $store = $rsSellingDetails[0]['store'];
                $passchecklistNo = $rsSellingDetails[0]['notes'];
            }

            if(isset($rsJODetailCol[$value['refsodetailkey']])) {
                $rsJODetails = $rsJODetailCol[$value['refsodetailkey']];
                $store = $rsJODetails[0]['trdesc'];
                $passchecklistNo = $rsJOHeaderCol[0]['donumber'];
            }

        $html .='
                <tr>
                    <td class="border-left-right-bottom align-center" style="' . $widthNo . ';">'. $no .' </td>
                    <td class="border-right-bottom align-center" style="' . $widthDate . '">'. $obj->toLocalDate($obj->formatDBDate($rsDetail[$i]['trdate'],'d-M-y')) .'</td>
                    <td class="border-right-bottom align-center" style="' . $widthTypeTruck . '">'. $rsWO[0]['containername'] .'</td>
                    <td class="border-right-bottom align-center" style="' . $widthLicensePlate . '">' . $policeNumber . '</td>
                    <td class="border-right-bottom align-center" style="' . $widthPasschecklistNo . '">' . $passchecklistNo . '</td>
                    <td class="border-right-bottom align-center" style="' . $widthStore . '">' . $store . '</td>
                    <td class="border-right-bottom align-right" style="' . $widthPrice . '">'.$obj->formatNumber($value['total'])  .'</td>
                </tr>
            ';
            $no++;
        }

    }

    $subTotal = $rs[0]['subtotal'];
    $dpp = $subTotal * (11/12);
    $taxValue = $rs[0]['taxvalue'];
    $taxPercentage = $rs[0]['taxpercentage'];
    $isPriceIncludeTax = $rs[0]['ispriceincludetax'];

    $discountValue = $rs[0]['finaldiscount'];
    $finalDiscountType = $rs[0]['finaldiscounttype'];

    if($finalDiscountType == 2) {
        $discountValue = $subTotal * ($discountValue / 100);
    }

    $stampFee = $rs[0]['stampfee'];

    $grandTotal = $rs[0]['grandtotal'];

    $html .='
        <tr>
            <td colspan="6" class="border-left-right-bottom font-bold align-center">Total</td>
            <td class="border-right-bottom align-right font-bold    ">'. $obj->formatNumber($subTotal) .'</td>
        </tr>
    ';

    if ($discountValue > 0) {

        $html .= '
            <tr>
                <td colspan="6" class="border-left-right-bottom font-bold align-center">Diskon</td>
                <td class="border-right-bottom align-right font-bold    ">' . $obj->formatNumber($discountValue) . '</td>
            </tr>
        ';

    }


    $html .='
        <tr>
            <td colspan="6" class="border-left-right-bottom font-bold align-center">DPP</td>
            <td class="border-right-bottom align-right font-bold    ">'. $obj->formatNumber($dpp) .'</td>
        </tr>
    ';

    if($taxValue > 0) {

        $html .='
            <tr>
                <td colspan="6" class="border-left-right-bottom font-bold align-center">PPN</td>
                <td class="border-right-bottom align-right font-bold    ">'. $obj->formatNumber($taxValue) .'</td>
            </tr>
        ';

    }

    if ($stampFee > 0) {
        $html .= '
        <tr>
            <td colspan="6" class="border-left-right-bottom font-bold align-center">Biaya Materail</td>
            <td class="border-right-bottom align-right font-bold    ">' . $obj->formatNumber($stampFee) . '</td>
        </tr>
    ';
    }

    $html .='
        <tr>
            <td colspan="6" class="border-left-right-bottom font-bold align-center">Grand Total</td>
            <td class="border-right-bottom align-right font-bold    ">'. $obj->formatNumber($grandTotal) .'</td>
        </tr>
    ';

    $html .='
        </tbody>
    </table>';

    $html .= '<div style="clear:both"></div>';

    $sayNumber = $obj->sayNumber($grandTotal);

    $html .= '<table cellpadding="2" width="100%">
            <tr>
                <td style="width:480px";><table cellpadding="3">
                        <tr>
                            <td class="font-bold" style="width:100px;">Terbilang:</td>
                        </tr>
                        <tr>
                            <td class="border-left-top-right-bottom" style="width:470px;">' . ucwords($sayNumber) . ' Rupiah</td>
                        </tr>
                    </table>
                    <table cellpadding="3"><tr><td></td></tr></table>
                    <table cellpadding="3">
                        <tr>
                            <td style="width:400px">BANK ' . strtoupper($bankName) . ' CAB. ' . strtoupper($branch) . '</td>
                        </tr>
                        <tr>
                            <td style="width:400px">A/N : ' . strtoupper($accountName) . '</td>
                        </tr>
                        <tr>    
                            <td style="width:400px">A/C : ' . $accountNumber . '</td>
                        </tr>
                    </table>
                </td>
                
                <td style="width:200px";>
                    <table cellpadding="3">
                        <tr>
                            <td style="width:190px;" class="align-center font-bold">Bekasi, '. $obj->toLocalDate($obj->toLocalDate($obj->formatDBDate($rs[0]['trdate'], 'd F Y'))) .'</td>
                        </tr>
                        <tr><td></td></tr>
                        <tr><td></td></tr>
                        <tr><td></td></tr>
                        <tr><td></td></tr>
                        <tr><td></td></tr>
                        <tr>
                            <td style="width:190px;" class="align-center font-bold">' . (empty($invoiceSignature) ? 'Lana Lainufar' : $invoiceSignature) . '</td>
                        </tr>
                    </table>
                </td>
            </tr>
            </table>';


    return $html;

}

?>
