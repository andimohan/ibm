<?php

includeClass(array('EMKLOrderInvoice.class.php', 'EMKLJobOrder.class.php', 'Customer.class.php', 'Currency.class.php', 'TermOfPayment.class.php', 'CustomCode.class.php'));
$emklOrderInvoice = createObjAndAddToCol(new EMKLOrderInvoice);
$paymentMethod = new PaymentMethod();
$obj = $emklOrderInvoice;

$pdf->setCustomSettings(
    array(
        'showPrintHeader' => false,
        'footer' => '',
        'marginFooter' => '0px'
    )
);

$generateReportContent = function ($dataset) {

    global $pdf;

    $obj = new EMKLOrderInvoice();
    $emklJobOrder = new EMKLJobOrder();
    $customer = new Customer();
    $currency = new Currency();
    $termOfPayment = new TermOfPayment();
    $paymentMethod = new PaymentMethod();
    $customCode = new CustomCode();

    $rs = $dataset['rs'];



    $rsDetail = $obj->getDetailWithRelatedInformation($rs[0]['pkey']);

    $rsInvoiceType = $customCode->searchData($customCode->tableName . '.pkey', $rs[0]['customcodekey'], true);
    $invoiceTitle = (!empty($rsInvoiceType[0]['title'])) ? $rsInvoiceType[0]['title'] : $rsInvoiceType[0]['name'];

    $rsCustomer = $customer->getDataRowById($rs[0]['customerkey']);


    $arrDetailKey = array_column($rsDetail, 'pkey');
    $arrSOHeaderKey = array_column($rsDetail, 'refsalesorderheaderkey');

    $rsJobOrder = $emklJobOrder->searchData('', '', true, ' and ' . $emklJobOrder->tableName . '.pkey in (' . $obj->oDbCon->paramString($arrSOHeaderKey, ',') . ') ');

    //$hblArray = array_column($rsDetail, 'hbl');
    $arrHBL = array();
    foreach($rsDetail as $detailRow) {
        $hblCode = (!empty($detailRow['hbl']) ? $detailRow['hbl'] : $detailRow['refdetailhbl']);
        array_push($arrHBL, $hblCode);
    }

    $hbl = implode(',', $arrHBL);

    $mblNumberArray = array_column($rsJobOrder, 'mblnumber');
    $mblNumber = implode(',', $mblNumberArray);;

    $ajuArray = array_column($rsJobOrder, 'aju');
    $aju = implode(',', $ajuArray);

    $poNumberArray = array_column($rsJobOrder, 'ponumber');
    $poNumber = implode(',', $poNumberArray);

    $arrFeederVessel = array();
    $arrDestination = array();
    $arrEtdEta = array();
    foreach ($rsJobOrder as $joRow) {
        array_push($arrFeederVessel, $joRow['feedervesselname'] . ' / ' . $joRow['feedernumber']);
        array_push($arrDestination, $joRow['finaldestinationname']);
        array_push($arrEtdEta, ($joRow['etdpol'] == '0000-00-00' ? '-' : $obj->formatDBDate($joRow['etdpol'], 'd, M-Y')) . ' / ' . ($joRow['etapod'] == '0000-00-00' ? '-' : $obj->formatDBDate($joRow['etapod'], 'd, M-Y')));
    }

    $vessel = implode(',', $arrFeederVessel);
    $placeOfReceiptName = implode(',', $arrDestination);
    $etdEta = implode(',', $arrEtdEta);

    $rsItemDetail = $obj->getItemDetail($arrDetailKey);
    $arrJOItemDetailKey = array_column($rsItemDetail, 'refsodetailkey');

    $rsItemDetail = $obj->reindexDetailCollections($rsItemDetail, 'refkey');

    $rsJOItemDetail = $emklJobOrder->getItemDetail('', $arrJOItemDetailKey);
    $rsJOItemDetail = $obj->reindexDetailCollections($rsJOItemDetail, 'pkey');

    $rsCurrency = $currency->searchData('', '', true, ' and ' . $currency->tableName . '.statuskey = 1 ');
    $rsCurrency = $obj->reindexDetailCollections($rsCurrency, 'pkey');

    $rsTOP = $termOfPayment->getDataRowById($rs[0]['termofpaymentkey']);
    $dueDays = $rsTOP[0]['duedays'];
    $dueDate = new DateTime($rs[0]['trdate']);
    $dueDate->add(new DateInterval('P' . $dueDays . 'D'));




    $rsPaymentMethod = $paymentMethod->getDataRowById($rs[0]['companybankkey']);
    $bankName = (empty($rsPaymentMethod[0]['bankname']) ? $rsPaymentMethod[0]['name'] : $rsPaymentMethod[0]['bankname']);
    $branch = $rsPaymentMethod[0]['branch'];
    $bankAccountNumber = $rsPaymentMethod[0]['bankaccountnumber'];
    $swiftCode = $rsPaymentMethod[0]['swiftcode'];
    $bankAccountName = $rsPaymentMethod[0]['bankaccountname'];
    $bankAddress = $rsPaymentMethod[0]['bankaddress'];

    $rsContainer = $emklJobOrder->getDetailContainer($arrSOHeaderKey);
    $arrContainer = array();

    foreach ($rsContainer as $containerRow)
        if (!empty($containerRow['containerno']))
            array_push($arrContainer, $containerRow['containerno'] . ' - ' . $containerRow['containername']);

    //    $arrContainer = array_column($rsContainer, 'containerno');
    $container = implode(', ', $arrContainer);

    $roundType = $obj->loadSetting('invoiceTaxRoundType');
    $roundTaxType = $obj->loadSetting('invoiceTaxRoundType');

    if ($rs[0]['currencykey'] == CURRENCY['idr']) {
        $decimalQtyTotal = 3;
        $subtotalDecimal = 2;
        $decimal = 0;
    } else {
        $decimal = 2;
        $decimalQtyTotal = $decimal;
        $subtotalDecimal = $decimalQtyTotal;
    }

    $pebPibTitle = ($rsJobOrder[0]['jobtypekey'] == EMKL['jobType']['import']) ? 'PIB. No.' : 'PEB. No.';

    $html = $obj->printSetting['defaultStyle'];

    $html .= '
        <div style="clear:both"></div>
            <div style="clear:both"></div>
            <div style="clear:both"></div>
            <div style="clear:both"></div>
            <table>
                <tr>
                    <td style="text-align:center"><h2>' . $invoiceTitle . '</h2></td>
                </tr>
            </table>
            <div style="clear:both"></div>
        ';

    $html .= '
    <table cellpadding="4">
        <tr>
            <td style="width:390px"><table>
                <tr>
                    <td style="width:30px;font-size:12px;"><b>TO :</b></td>
                </tr>
                <tr>
                    <td style="width:300px;font-size:12px;">' . $rs[0]['customername'] . ' <br>' . $rsCustomer[0]['address'] . '</td>
                </tr>
            </table></td>
            <td style="width:300px"><table>
                <tr>
                    <td style="width:60px;font-size:12px;"><b>Ref No.</b></td>
                    <td style="width:10px;font-size:12px;">:</td>
                    <td style="width:150px;font-size:12px;">' . $rs[0]['code'] . '</td>
                </tr>
                <tr>
                    <td style="width:60px;font-size:12px;">Date</td>
                    <td style="width:10px;font-size:12px;">:</td>
                    <td style="width:150px;font-size:12px;">' . $obj->formatDBDate($rs[0]['trdate'], 'd-M-Y') . '</td>
                </tr>
                <tr>
                    <td style="width:60px;font-size:12px;">Due Date</td>
                    <td style="width:10px;font-size:12px;">:</td>  
                    <td style="width:150px;font-size:12px;">' . $dueDate->format('d, M-Y') . '</td>
                </tr>
            </table></td>
        </tr>
    </table>
    ';

    $html .= ' <div style="clear:both"></div>';

    $shipperInformation = ($rs[0]['currencykey'] != CURRENCY['idr']) ? '<tr>
                    <td style="width:80px;font-size:12px;">Shipper</td>
                    <td style="width:10px;font-size:12px;">:</td>
                    <td style="width:170px;font-size:12px;">' . $rsJobOrder[0]['customername'] . '</td></tr>' : '';

    $html .= '
    <table cellpadding="4">
        <tr>
            <td style="width:350px"><table>
                <tr>
                    <td style="width:85px;font-size:12px;">HBL / '.$pebPibTitle.'</td>
                    <td style="width:10px;font-size:12px;">:</td>
                    <td style="width:200px;font-size:12px;">' . $hbl . ' / '. $aju .'</td>
                </tr>
                <tr>
                    <td style="width:85px;font-size:12px;">MBL</td>
                    <td style="width:10px;font-size:12px;">:</td>
                    <td style="width:200px;font-size:12px;">' . $mblNumber . '</td>
                </tr>
                <tr>
                    <td style="width:85px;font-size:12px;">Destination</td>
                    <td style="width:10px;font-size:12px;">:</td>
                    <td style="width:200px;font-size:12px;">' . $placeOfReceiptName . '</td>
                </tr>
                <tr>
                    <td style="width:85px;font-size:12px;">VSL / VOY</td>
                    <td style="width:10px;font-size:12px;">:</td>
                    <td style="width:150px;font-size:12px;">' . $vessel . '</td>
                </tr>
            </table></td>
            <td style="width:320px"><table>
                <tr>
                    <td style="width:80px;font-size:12px;">Container No</td>
                    <td style="width:10px;font-size:12px;">:</td>
                    <td style="width:200px;font-size:12px;">' . $container . '</td>
                </tr>
                <tr>
                    <td style="width:80px;font-size:12px;"></td>
                    <td style="width:10px;font-size:12px;"></td>
                    <td style="width:150px;font-size:12px;"></td>
                </tr>
                <tr>
                    <td style="width:80px;font-size:12px;">ETD / ETA</td>
                    <td style="width:10px;font-size:12px;">:</td>
                    <td style="width:170px;font-size:12px;">' . $etdEta . '</td></tr>
                ' . $shipperInformation . ' 
            </table></td>
        </tr>
    </table>
    ';

    $html .= '
    <table cellpadding="4">
        <thead>
            <tr>
                <td style="width:330px;text-align:center;border-bottom:1px solid black;font-size:12px;">DESCRIPTION</td>
                <td style="width:100px;text-align:right;border-bottom:1px solid black;font-size:12px;">QUANTITY</td>
                <td style="width:120px;text-align:right;border-bottom:1px solid black;font-size:12px;">UNIT PRICE</td>
                <td style="width:120px;text-align:right;border-bottom:1px solid black;font-size:12px;">AMOUNT</td>
            </tr>
        </thead>
        <tbody>
        ';

        if (!empty($poNumber)) {
            $html .= '
                <tr>
                    <td>INV NO. ' . $poNumber . '</td>
                </tr>
            ';
        }
    

    $grandTotal = $rs[0]['grandtotal'];
    $taxValue = $rs[0]['taxvalue'];
    $beforeTaxTotal = $rs[0]['beforetaxtotal'];

    $vatPercentage = 0;

    foreach ($rsItemDetail as $key => $itemDetail) {

        for ($i = 0; $i < count($itemDetail); $i++) {
            $rsCurrencyCol = $rsCurrency[$itemDetail[$i]['currencykey']];
            $rsJOItemDetailCol = $rsJOItemDetail[$itemDetail[$i]['refsodetailkey']];

            $itemName = (!empty($itemDetail[$i]['aliasname']) ? $itemDetail[$i]['aliasname'] : $itemDetail[$i]['itemname']);

            //$priceInUnit = $itemDetail[$i]['priceinunit'];
            $priceInUnit = $itemDetail[$i]['beforetaxdetailvalue'] /  $itemDetail[$i]['qtyinbaseunit'];
            if ($rs[0]['currencykey'] != CURRENCY['idr']) {
                //invoice currency header idr dan item usd
                $priceInUnit = ($itemDetail[$i]['currencykey'] == CURRENCY['idr'])
                    ? $priceInUnit / $itemDetail[$i]['rate']
                    : $priceInUnit;
            } else {
                $priceInUnit = ($itemDetail[$i]['currencykey'] != CURRENCY['idr'])
                    ? $priceInUnit * $itemDetail[$i]['rate']
                    : $priceInUnit;
            }

            $total = $itemDetail[$i]['beforetaxdetailvalue'];
            $itemDesc = (!empty($rsJOItemDetailCol[0]['trdesc'])) ? '. ' . $rsJOItemDetailCol[0]['trdesc'] : '';
            $html .= '
                <tr>
                    <td style="width:330px;font-size:12px;">' . $itemName . $itemDesc . '</td>
                    <td style="width:100px;text-align:right;font-size:12px;">' . $obj->formatNumber($itemDetail[$i]['qtyinbaseunit'], $decimalQtyTotal) . '</td>
                    <td style="width:120px;text-align:right;font-size:12px;">' . $obj->formatNumber($priceInUnit, $decimalQtyTotal) . '</td>
                    <td style="width:120px;text-align:right;font-size:12px;">' . $obj->formatNumber($total, $subtotalDecimal) . '</td>
                </tr>
            ';


            if ($itemDetail[$i]['taxdetail'] > $vatPercentage)
                $vatPercentage = $itemDetail[$i]['taxdetail'];



            //            if (!empty($rsJOItemDetailCol[0]['trdesc'])) {
//                $html .= '<tr>
//                    <td style="width:270px;font-size:10px;font-size:12px;">' . $rsJOItemDetailCol[0]['trdesc'] . '</td>
//                    <td colspan="3"></td>
//                </tr>
//            ';
//            }

        }
    }

    $html .= '
        </tbody>
    </table>';


    $sayNumberAmount = $obj->unformatNumber($obj->formatNumber($grandTotal, $decimal));


    $footerContent = '<table cellpadding="2"><tr>
            <td style="width:490px"><table cellpadding="2">
            <tr>
                <td style="font-size:12px;text-align:left">TOTAL : </td>
            </tr>
            <tr>
                <td style="font-size:12px;text-align:left">VAT AMOUNT : ' . $obj->formatNumber($vatPercentage, 2) . ' %</td>
            </tr>
            <tr>
                <td style="font-size:12px;text-align:left">THE SUM OF : </td>
            </tr>
            
            <tr>
                <td style="width:400px;font-size:12px;text-align:left"># ' . strtoupper($obj->sayNumberInEnglish($sayNumberAmount)) . ' ' . ($rs[0]['currencykey'] == CURRENCY['idr'] ? 'RUPIAHS' : 'DOLLARS') . ' ONLY #</td>
            </tr>
            </table></td>

            <td style="width:160px;font-size:12px;"><table cellpadding="2">
            <tr>
                <td style="width:50px; border-bottom:1px solid black;font-size:12px;">' . ($rs[0]['currencykey'] == CURRENCY['idr'] ? 'IDR' : 'USD') . ' </td>
                <td style="width:120px; text-align:right; border-bottom:1px solid black;font-size:12px;">' . $obj->formatNumber($beforeTaxTotal, $decimal) . '</td>
            </tr>
            <tr>
                <td style="width:50px; border-bottom:1px solid black;font-size:12px;">' . ($rs[0]['currencykey'] == CURRENCY['idr'] ? 'IDR' : 'USD') . ' </td>
                <td style="width:120px; text-align:right; border-bottom:1px solid black;font-size:12px;">' . $obj->formatNumber($taxValue, $decimal) . '</td>
            </tr>

            <tr>
                <td style="width:50px; border-bottom:1px solid black;font-size:12px;">' . ($rs[0]['currencykey'] == CURRENCY['idr'] ? 'IDR' : 'USD') . ' </td>
                <td style="width:120px; text-align:right; border-bottom:1px solid black;font-size:12px;">' . $obj->formatNumber($grandTotal, $decimal) . '</td>
            </tr></table></td>
            
        </tr></table>
        
        <table cellpadding="4">
            <tr>
            <td style="width:470px"><table>
                <tr>
                    <td style="width:5px">*</td>
                    <td style="width:320px;text-align:left;">We receive Full Amount In Our Account</td>
                </tr>
                <tr>
                    <td style="width:5px">*</td>
                    <td style="width:320px;text-align:left;">Payment By Cheque/Draft etc. Is Not Considered Valid Before Is Is Cashed Or Cleared By Our Bank</td>
                </tr>
                <tr>
                    <td style="width:5px"></td>
                    <td style="width:320px"></td>
                </tr>

                <tr>
                    <td style="width:300px;text-align:left">Please Kindly Ensure The Payment To ' . ($rs[0]['currencykey'] == CURRENCY['idr'] ? 'IDR' : 'USD') . ' Account :</td>
                </tr>
                <tr>
                    <td style="width:85px;text-align:left">Bank</td>
                    <td style="width:10px">:</td>
                    <td style="width:250px;text-align:left">' . $bankName . '</td>
                </tr>
                <tr>
                    <td style="width:85px;text-align:left">Bank Address</td>
                    <td style="width:10px">:</td>
                    <td style="width:250px;text-align:left">' . $bankAddress . '</td>
                </tr>
                <tr>
                    <td style="width:85px;text-align:left">A/C</td>
                    <td style="width:10px">:</td>
                    <td style="width:250px;text-align:left">' . $bankAccountNumber . '</td>
                </tr>
                <tr>
                    <td style="width:85px;text-align:left">Swift Code</td>
                    <td style="width:10px">:</td>
                    <td style="width:250px;text-align:left">' . $swiftCode . '</td>
                </tr>
                <tr>
                    <td style="width:85px;text-align:left">Beneficiary</td>
                    <td style="width:10px">:</td>
                    <td style="width:250px;text-align:left">' . $bankAccountName . '</td>
                </tr>

            </table></td>
                    <td style="width:200px;text-align:center"><table>
                        <tr>
                            <td style="width:200px;font-size:12px;">' . $obj->loadSetting('companyName') . '</td>
                        </tr>
                        <tr><td style="width:200px"></td></tr>
                        <tr><td style="width:200px"></td></tr>
                        <tr><td style="width:200px"></td></tr>
                        <tr><td style="width:200px"></td></tr>
                        <tr><td style="width:200px"></td></tr>
                        <tr><td style="width:200px"></td></tr>
                        <tr><td style="width:200px"></td></tr>
                        <tr><td style="width:200px"></td></tr>
                        <tr><td style="width:200px;font-size:12px;">Authorized Signature</td></tr>
                    </table></td>
                </tr>
            </table>';

    $myX = 10;
    $myY = 218;
    $pdf->writeHTMLCell(50, '', $myX, $myY, $footerContent, 0, 0, 0, true, 'C', true);

    //reset position
    $pdf->SetXY(10, 10, true);
    return $html;
};

?>
