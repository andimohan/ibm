<?php
$pdf->setCustomSettings(
    array(
        'showPrintHeader' => false,
    )
);
$PRINT_SETTINGS = array(
    'showPrintHeader' => false,
);

includeClass(array('EMKLOrderInvoice.class.php', 'EMKLJobOrder.class.php', 'Customer.class.php', 'Currency.class.php', 'TermOfPayment.class.php', 'CustomCode.class.php'));
$emklOrderInvoice = createObjAndAddToCol(new EMKLOrderInvoice);
$obj = $emklOrderInvoice;

$generateReportContent = function ($dataset) {

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


        $arrDetailKey =  array_column($rsDetail, 'pkey');
        $arrSOHeaderKey =  array_column($rsDetail, 'refsalesorderheaderkey');

        $rsJobOrder = $emklJobOrder->searchData('', '', true, ' and ' . $emklJobOrder->tableName . '.pkey = (' . $obj->oDbCon->paramString($arrSOHeaderKey, ',') . ') ');

        $hblArray = array_column($rsDetail, 'hbl');
        $hbl = implode(',', $hblArray);
        $mblNumberArray = array_column($rsJobOrder, 'mblnumber');
        $mblNumber = implode(',', $mblNumberArray);;

        $arrFeederVessel = array();
        $arrDestination = array();
        $arrEtdEta = array();
        foreach($rsJobOrder as $joRow) {
            array_push($arrFeederVessel, $joRow['feedervesselname'] . ' / ' . $joRow['feedernumber']);
            array_push($arrDestination, $joRow['placeofreceiptname']);
            array_push($arrEtdEta, ($joRow['etdpol'] == '0000-00-00' ? '-' : $obj->formatDBDate($joRow['etdpol'], 'd, M-Y')) . ' / ' . ($joRow['etapod'] == '0000-00-00' ? '-' : $obj->formatDBDate($joRow['etapod'], 'd, M-Y')));
        }

        $vessel =   implode(',', $arrFeederVessel);
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

        $rsContainer = $emklJobOrder->getDetailContainer($arrSOHeaderKey);

        $arrContainer = array_column($rsContainer, 'containerno');
        $container = implode(', ', $arrContainer);

        $roundType = $obj->loadSetting('invoiceTaxRoundType');
        $roundTaxType = $obj->loadSetting('invoiceTaxRoundType');


        $html = $obj->printSetting['defaultStyle'];

        $html .= '
         <div style="clear:both"></div>
            <div style="clear:both"></div>
            <div style="clear:both"></div>
            <div style="clear:both"></div>
            <table>
                <tr>
                    <td style="text-align:center"><h3>' . $invoiceTitle . '</h3></td>
                </tr>
            </table>
            <div style="clear:both"></div>
        ';

        $html .= '
    <table cellpadding="4">
        <tr>
            <td style="width:390px"><table>
                <tr>
                    <td style="width: 30px"><b>TO :</b></td>
                </tr>
                <tr>
                    <td style="width: 300px">' . $rs[0]['customername'] . ' <br>' . $rsCustomer[0]['address'] . '</td>
                </tr>
            </table></td>
            <td style="width:300px"><table>
                <tr>
                    <td style="width:60px"><b>Ref No.</b></td>
                    <td style="width:10px;">:</td>
                    <td style="width:150px;">' . $rs[0]['code'] . '</td>
                </tr>
                <tr>
                    <td style="width:60px">Date</td>
                    <td style="width:10px;">:</td>
                    <td style="width:150px;">' . $obj->formatDBDate($rs[0]['trdate'], 'd-M-Y') . '</td>
                </tr>
                <tr>
                    <td style="width:60px">Due Date</td>
                    <td style="width:10px;">:</td>  
                    <td style="width:150px;">' . $dueDate->format('d, M-Y') . '</td>
                </tr>
            </table></td>
        </tr>
    </table>
    ';

        $html .= ' <div style="clear:both"></div>';

        $html .= '
    <table cellpadding="4">
        <tr>
            <td style="width:350px"><table>
                <tr>
                    <td style="width:85px">HBL / PEB No.</td>
                    <td style="width:10px">:</td>
                    <td style="width:200px">' . $hbl . ' / </td>
                </tr>
                <tr>
                    <td style="width:85px">MBL</td>
                    <td style="width:10px">:</td>
                    <td style="width:200px">' . $mblNumber . '</td>
                </tr>
                <tr>
                    <td style="width:85px">Destination</td>
                    <td style="width:10px">:</td>
                    <td style="width:200px">' . $placeOfReceiptName . '</td>
                </tr>
                <tr>
                    <td style="width:85px">VSL / VOY</td>
                    <td style="width:10px">:</td>
                    <td style="width:150px">'. $vessel .'</td>
                </tr>
            </table></td>
            <td style="width:320px"><table>
                <tr>
                    <td style="width:80px">Container No</td>
                    <td style="width:10px">:</td>
                    <td style="width:200px">' . $container . '</td>
                </tr>
                <tr>
                    <td style="width:80px"></td>
                    <td style="width:10px"></td>
                    <td style="width:150px"></td>
                </tr>
                <tr>
                    <td style="width:80px">ETD / ETA</td>
                    <td style="width:10px">:</td>
                    <td style="width:170px">' . $etdEta . '</td></tr>
            </table></td>
        </tr>
    </table>
    ';

    // $obj->setLog($rsJobOrder[0]['etdpol'], true);

        $html .= '<table>
            <tr><td style="height:380px"><table cellpadding="4">
        <thead>
            <tr>
                <td style="width:330px;text-align:center;border-bottom:1px solid black;">DESCRIPTION</td>
                <td style="width:100px;text-align:right;border-bottom:1px solid black;">QUANTITY</td>
                <td style="width:120px;text-align:right;border-bottom:1px solid black;">UNIT PRICE</td>
                <td style="width:120px;text-align:right;border-bottom:1px solid black;">AMOUNT</td>
            </tr>
        </thead>
        <tbody>
        ';
        // <tr>
        //     <td style="width:270px;">INVOICE NO. ' . $rs[0]['code'] . '</td>
        //     <td style="width:100px;"></td>
        //     <td style="width:120px;"></td>
        //     <td style="width:60px;"></td>
        //     <td style="width:120px;"></td>
        // </tr>

        $grandTotal = 0;
        $taxDetailValue = 0;
        $taxValue = 0;
        foreach ($rsItemDetail as $key => $itemDetail) {

            for ($i = 0; $i < count($itemDetail); $i++) {
                $rsCurrencyCol = $rsCurrency[$itemDetail[$i]['currencykey']];
                $rsJOItemDetailCol = $rsJOItemDetail[$itemDetail[$i]['refsodetailkey']];

                $itemName = (!empty($itemDetail[$i]['aliasname']) ? $itemDetail[$i]['aliasname'] : $itemDetail[$i]['itemname']);

                if(!empty($itemDetail[$i]['taxdetailvalue'])) {
                    $taxDetailValue += $itemDetail[$i]['taxdetailvalue'];
                }


                $priceInUnit = $itemDetail[$i]['priceinunit'];
                if($rs[0]['currencykey'] != CURRENCY['idr']) {
                    //invoice currency header idr dan item usd
                    $priceInUnit = ($itemDetail[$i]['currencykey'] == CURRENCY['idr']) 
                                    ? $priceInUnit / $itemDetail[$i]['rate']    
                                    : $priceInUnit;
                } else {
                    $priceInUnit = ($itemDetail[$i]['currencykey'] != CURRENCY['idr'])
                                    ? $priceInUnit * $itemDetail[$i]['rate']
                                    : $priceInUnit;
                }
            
                $total = $itemDetail[$i]['total'];

                $grandTotal += $total;  

                $html .= '
                <tr>
                    <td style="width:330px;">' . $itemName . '</td>
                    <td style="width:100px;text-align:right">' . $obj->formatNumber($itemDetail[$i]['qtyinbaseunit'], 3) . '</td>
                    <td style="width:120px;text-align:right">' . $obj->formatNumber($priceInUnit, 2) . '</td>
                    <td style="width:120px;text-align:right">' . $obj->formatNumber($total, 2) . '</td>
                </tr>
            ';
            
                if (!empty($rsJOItemDetailCol[0]['trdesc'])) {
                    $html .= '
            <tr>
                <td style="width:270px;font-size:10px">' . $rsJOItemDetailCol[0]['trdesc'] . '</td>
            </tr>
            ';
                }

            }
        }

        $html .= '
        </tbody>
    </table>';


        $html .= '
            </td></tr>
        ';

        $html .= '<tr><td style="height:100px;"><table cellpadding="2"><tr>
            <td style="width:490px"><table cellpadding="2">
            <tr>
                <td>TOTAL : </td>
            </tr>
            <tr>
                <td>VAT AMOUNT : </td>
            </tr>
            <tr>
                <td>THE SUM OF : </td>
            </tr>
            ';
            if(!empty($rs[0]['taxvalue'])) {
                $taxValue  = $rs[0]['taxvalue'];
            }

            $sayNumberAmount = $grandTotal;
            if (!empty($taxValue)) {
                $sayNumberAmount += $taxValue;
            }

            $html .='
            <tr>
                <td style="width:300px"># ' . strtoupper($obj->sayNumberInEnglish($sayNumberAmount)) . ' ' . ($rs[0]['currencykey'] == CURRENCY['idr'] ? 'RUPIAHS' : 'DOLLARS') . ' ONLY #</td>
            </tr>
            </table></td>
            <td style="width:160px"><table cellpadding="2">
            <tr>
                <td style="width:50px; border-bottom:1px solid black;">'.($rs[0]['currencykey'] == CURRENCY['idr'] ? 'IDR' : 'USD').' </td>
                <td style="width:120px; text-align:right; border-bottom:1px solid black;">' . $obj->formatNumber($grandTotal, 2) . '</td>
            </tr>
            <tr>
                <td style="width:50px; border-bottom:1px solid black;">'.($rs[0]['currencykey'] == CURRENCY['idr'] ? 'IDR' : 'USD').' </td>
                <td style="width:120px; text-align:right; border-bottom:1px solid black;">' . $obj->formatNumber($taxValue, 2) . '</td>
            </tr>
            ';

            if (!empty($taxValue)) {
                $grandTotal += $taxValue;
            }

            $html .='
            <tr>
                <td style="width:50px; border-bottom:1px solid black;">'.($rs[0]['currencykey'] == CURRENCY['idr'] ? 'IDR' : 'USD').' </td>
                <td style="width:120px; text-align:right; border-bottom:1px solid black;">' . $obj->formatNumber($grandTotal, 2) . '</td>
            </tr>
            </table></td>
        </tr></table>
        </td></tr>
        <tr>
            <td><table cellpadding="4">
            <tr>
            <td style="width:470px"><table>
                <tr>
                    <td style="width:5px">*</td>
                    <td style="width:320px">We receive Full Amount In Our Account</td>
                </tr>
                <tr>
                    <td style="width:5px">*</td>
                    <td style="width:320px">Payment By Cheque/Draft etc. Is Not Considered Valid Before Is Is Cashed Or Cleared By Our Bank</td>
                </tr>
                <tr>
                    <td style="width:5px"></td>
                    <td style="width:320px"></td>
                </tr>

                <tr>
                    <td style="width:250px">Please Kindly Ensure The Payment To :</td>
                </tr>
                <tr>
                    <td>Bank ' . $bankName . ' Branch ' . $branch . '</td>
                </tr>
                <tr>
                    <td>****** Rupiah Account :</td>
                </tr>
                <tr>
                    <td>A/C : ' . $bankAccountNumber . '</td>
                </tr>
                <tr>
                    <td></td>
                </tr>
                <tr>
                    <td>Swift Code : ' . $swiftCode . '</td>
                </tr>
                <tr>
                    <td style="width:200px"></td>
                </tr>
                <tr>
                    <td>Beneficiary : ' . $bankAccountName . '</td>
                </tr>

            </table></td>
                    <td style="width:200px;text-align:center"><table>
                        <tr>
                            <td style="width:200px">' . $obj->loadSetting('companyName') . '</td>
                        </tr>
                        <tr><td style="width:200px"></td></tr>
                        <tr><td style="width:200px"></td></tr>
                        <tr><td style="width:200px"></td></tr>
                        <tr><td style="width:200px"></td></tr>
                        <tr><td style="width:200px"></td></tr>
                        <tr><td style="width:200px">Authorized Signature</td></tr>
                    </table></td>
                </tr>
            </table>
            </td>
        </tr>
        </table>';

        $html .= '';

        return $html;
    };
    
?>
