<?php

includeClass(array('EMKLPurchaseOrder.class.php' ,'EMKLOrderInvoice.class.php', 'EMKLJobOrder.class.php', 'Customer.class.php', 'Currency.class.php', 'TermOfPayment.class.php', 'CustomCode.class.php'));
$emklOrderInvoice = createObjAndAddToCol(new EMKLOrderInvoice);
$paymentMethod = new PaymentMethod();
$obj = $emklOrderInvoice;


$companyName = '<span style="font-size:10px; font-weight:bold;">' . strtoupper($obj->loadSetting('companyName')) . '</span>';
$companyLogo = $obj->loadSetting('companyLogo');
$companyAddress = '<span style="font-size:10px">' . nl2br($obj->loadSetting('companyAddress')) . '</span>';
$zipCode = '<span style="font-size:10px">' . nl2br($obj->loadSetting('companyZipcode')) . '</span>';

// $imgLetterhead = $obj->phpThumbURLSrc . 'setting/companyLogo/' . $companyLogo;
$imgLetterhead = PHPTHUMB_URL_PATH . 'setting/companyLogo/' . $companyLogo;
$watermarkImage = PERSONALIZED_DOC_PATH.'include/img/watermark.png';

$logo = '<img src="' . $imgLetterhead . '" style="height:90px;"/>';

$tax = '<br><span style="font-size:10px">NPWP. '. $obj->loadSetting('companyTaxRegistrationNumber') .'</span>';
$phoneEmail = '<br><span style="font-size:10px">Email : jkt-biz@fortisea.com<br>Phone : 021-21694792</span>';

$header = '<table width="679px"><tr>
            <td style="width:230px;">'. $logo .'</td>
            <td style="width:160px;"></td>
            <td style="width:300px;text-align:right; color:#202b5e;">'. $companyName .'<br><span>'. $companyAddress .' '. $zipCode . $phoneEmail . $tax.'</span></td>
            </tr></table>';

$grandTotal = ($rs[0]['currencykey'] == CURRENCY['idr']) ? $rs[0]['grandtotal'] : $rs[0]['grandtotal'] * $rs[0]['rate'];
$above5Million = ($grandTotal > 5000000) ? true : false;

if (!$above5Million) {
    $footer = '<p style="font-weight:bold;text-align:center;">THIS IS COMPUTER GENERATED INVOICE AND DOES NOT REQUIRE A SIGNATURE</p>';
}

$pdf->setCustomSettings(
    array(
        'showPrintHeader' => true,
        'header' => $header,
        'footer' => $footer,

        'marginFooter' => '5px'
    )
);

$generateReportContent = function ($dataset) {
    global $pdf;    
    $obj = new EMKLOrderInvoice();
    $emklJobOrder = new EMKLJobOrder();
    $emklPurchaseOrder = new EMKLPurchaseOrder();
    $customer = new Customer();
    $currency = new Currency();
    $termOfPayment = new TermOfPayment();
    $paymentMethod = new PaymentMethod();
    $customCode = new CustomCode();
    $itemUnit  = new ItemUnit();

    $rs = $dataset['rs'];


    $html = $obj->printSetting['defaultStyle'];

    $rsDetail = $obj->getDetailWithRelatedInformation($rs[0]['pkey']);
    $arrDetailKey = array_column($rsDetail, 'pkey');
    $rsItemDetail = $obj->getItemDetail($arrDetailKey);
    
    $arrSOHeaderKey = array_column($rsDetail, 'refsalesorderheaderkey');
    
    $rsJobOrder = $emklJobOrder->searchData('', '', true, ' and ' . $emklJobOrder->tableName . '.pkey in (' . $obj->oDbCon->paramString($arrSOHeaderKey, ',') . ') ');
    $rsJODetail = $emklJobOrder->getDetailWithRelatedInformation($arrSOHeaderKey);

    $rsCurrency = $currency->searchData('', '', true, ' and ' . $currency->tableName . '.statuskey = 1');
    $rsCurrencyCols = $obj->reindexDetailCollections($rsCurrency, 'pkey');

    $rsPaymentMethod = $paymentMethod->getDataRowById($rs[0]['companybankkey']);

    $rsItemUnit = $itemUnit->searchData('','',true, ' and ' . $itemUnit->tableName.'.statuskey = 1');
    $rsItemUnitCols = $obj->reindexDetailCollections($rsItemUnit, 'pkey');

    $customerNameAddress = $rs[0]['customername'];

    $rsTOP = $termOfPayment->getDataRowById($rs[0]['termofpaymentkey']);
    $terms = $rsTOP[0]['duedays'] . ' DAYS';

    $rsContactPerson = $customer->getContactPerson($rs[0]['customerkey']);
    $rsCustomer = $customer->getDataRowById($rs[0]['customerkey']);
    $customerAddress = nl2br($rsCustomer[0]['address']);
    
    $headerCurrency = $rsCurrencyCols[$rs[0]['currencykey']][0]['name'];
    $JOCode = $rsJobOrder[0]['code'];
    $shipperName = ($rsJobOrder[0]['jobtypekey'] == EMKL['jobType']['import']) ? $rsJobOrder[0]['consigneename'] : $rsJobOrder[0]['customername'];
    $consigneeName = ($rsJobOrder[0]['jobtypekey'] == EMKL['jobType']['import']) ? $rsJobOrder[0]['customername'] : $rsJobOrder[0]['consigneename'];
    $vesselVoyage = $rsJobOrder[0]['feedervesselname'] . ' / ' . $rsJobOrder[0]['feedernumber'];
    $eta = $obj->formatDBDate($rsJobOrder[0]['etapod'], 'd/m/Y', array('returnOnEmpty' => true));
    $polPod = $rsJobOrder[0]['polname'] . ' / ' . $rsJobOrder[0]['podname'];
    $mblNumber = $rsJobOrder[0]['mblnumber'];
    $itemDescription = $rsJobOrder[0]['itemdescription'];
    $hsCode = $rsJobOrder[0]['hscode'];

    $rsContainer = $emklJobOrder->getDetailContainer($arrSOHeaderKey);
    $rsVolume = $emklJobOrder->getDetailVolume($arrSOHeaderKey);
    
    $arrContainer = array();

    foreach ($rsContainer as $containerRow) {
        if (!empty($containerRow['containerno'])) {
            array_push($arrContainer, $containerRow['containerno']);
        }
    }

    $arrHBL = array();
    foreach($rsJODetail as $joDetail) {
        if (!empty($joDetail['hbl'])) {
            array_push($arrHBL, $joDetail['hbl']);
        }
    }

    $contType  = '';
    $m3Wt = '';
    $arrVolume = array();
    $decimal = ($rs[0]['currencykey'] == CURRENCY['idr']) ? 0 : 2;

    if (in_array($rsJobOrder[0]['loadcontainertypekey'], [EMKL['emklType']['fcl'],EMKL['emklType']['trucking']])) {
        foreach ($rsVolume as $volumeRow) {
            $party = $obj->formatNumber($volumeRow['qty']);
            $volume = $party . ' x ' . $volumeRow['itemname'];
            array_push($arrVolume, $volume);
        }
        $contType = implode(',', $arrVolume);
    } else if(in_array($rsJobOrder[0]['loadcontainertypekey'],[EMKL['emklType']['lcl'],EMKL['emklType']['lclnc']])) {
        //$contType = $rsJobOrder[0]['containername'];
        $m3Wt = $obj->formatNumber($rsJobOrder[0]['volume'], 3) . ' M3 / ' . $obj->formatNumber($rsJobOrder[0]['weight'], 2) . ' KGS';
    }

    $hblNumber = implode(', ', $arrHBL);
    $containerNo = implode(', ', $arrContainer);

    $rsPaymentMethod = $paymentMethod->getDataRowById($rs[0]['companybankkey']);
    $bankName = (empty($rsPaymentMethod[0]['bankname']) ? $rsPaymentMethod[0]['name'] : $rsPaymentMethod[0]['bankname']);
    $branch = $rsPaymentMethod[0]['branch'];
    $bankAccountNumber = $rsPaymentMethod[0]['bankaccountnumber'];
    $swiftCode = $rsPaymentMethod[0]['swiftcode'];
    $bankAccountName = $rsPaymentMethod[0]['bankaccountname'];
    $bankAddress = $rsPaymentMethod[0]['bankaddress'];

    $companyName = $obj->loadSetting('companyName');

    $html .= '<table><tr><td><div class="title">INVOICE</div></td></tr></table>';

    $html .= '<table cellpadding="2" width="100%"><tr>
        <td style="width:370px;"><table>
        <tr>
            <td style="width:80px;">Bill To</td>
            <td style="width:10px;text-align:center;">:</td>
            <td style="width:250px;">'. strtoupper($customerNameAddress).'</td>
        </tr>
        <tr>
            <td style="width:80px;vertical-align:top;">Address</td>
            <td style="width:10px;vertical-align:top;text-align:center;">:</td>
            <td style="width:250px;">'. strtoupper($customerAddress).'</td>
        </tr>
        </table></td><td style="width:300px;"><table>
        <tr>
            <td style="width:120px;">Invoice No.</td>
            <td style="width:10px;text-align:center;">:</td>
            <td style="width:170px;">'. $rs[0]['code'] .'</td>
        </tr>
        <tr>
            <td style="width:120px;">Date</td>
            <td style="width:10px;text-align:center;">:</td>
            <td style="width:170px;">'. $obj->formatDBDate($rs[0]['trdate'], 'd/m/Y') .'</td>
        </tr>
        <tr>
            <td style="width:120px;">Terms</td>
            <td style="width:10px;text-align:center;">:</td>
            <td style="width:170px;">'. strtoupper($terms) .'</td>
        </tr>
        <tr>
            <td style="width:120px;">Customer Ref.</td>
            <td style="width:10px;text-align:center;">:</td>
            <td style="width:170px;">'.strtoupper($rsJobOrder[0]['ponumber']) .'</td>
        </tr>
        </table></td>
    </tr></table>';
    
    $html .= '<div style="clear:both"></div>';
    
    $html .= '<table cellpadding="2" width="100%"><tr>
        <td style="width:370px;"><table>
        <tr>
            <td style="width:80px;">Shipper</td>
            <td style="width:10px;text-align:center;">:</td>
            <td style="width:250px;">'. strtoupper($shipperName) .'</td>
        </tr>
        <tr>
            <td style="width:80px;">Consignee</td>
            <td style="width:10px;text-align:center;">:</td>
            <td style="width:250px;">'. strtoupper($consigneeName)   .'</td>
        </tr>
        <tr>
            <td style="width:80px;">Vessel/Voy</td>
            <td style="width:10px;text-align:center;">:</td>
            <td style="width:250px;">'. strtoupper($vesselVoyage).'</td>
        </tr>
        <tr>
            <td style="width:80px;">ETA</td>
            <td style="width:10px;text-align:center;">:</td>
            <td style="width:250px;">'. $eta.'</td>
        </tr>
        <tr>
            <td style="width:80px;">POL/POD</td>
            <td style="width:10px;text-align:center;">:</td>
            <td style="width:250px;">'. strtoupper($polPod).'</td>
        </tr>
        </table></td>
        <td style="width:300px;"><table>
        <tr>
            <td style="width:120px;">Job No.</td>
            <td style="width:10px;text-align:center;">:</td>
            <td style="width:170px;">'. $JOCode.'</td>
        </tr>
        <tr>
            <td style="width:120px;">M.B/L</td>
            <td style="width:10px;text-align:center;">:</td>
            <td style="width:170px;">'. $mblNumber .'</td>
        </tr>
        <tr>
            <td style="width:120px;">H.B/L No</td>
            <td style="width:10px;text-align:center;">:</td>
            <td style="width:170px;">'. $hblNumber.'</td>
        </tr>
        <tr>
            <td style="width:120px;">M3/WT</td>
            <td style="width:10px;text-align:center;">:</td>
            <td style="width:170px;">'.$m3Wt.'</td>
        </tr>
        <tr>
            <td style="width:120px;">Cont Type</td>
            <td style="width:10px;text-align:center;">:</td>
            <td style="width:170px;">'. $contType.'</td>
        </tr>
        </table></td>
    </tr></table>';

    $html .= '<div style="clear:both"></div>';
    $html .= '<table cellpadding="2" width="100%">
        <thead>
            <tr>
                <th style="width:20px;text-align:right;border-bottom:1px solid #000;">No</th>
                <th style="width:160px;border-bottom:1px solid #000;">Description</th>
                <th style="width:70px;border-bottom:1px solid #000;">UoM</th>
                <th style="width:60px;text-align:right;border-bottom:1px solid #000;">Qty</th>
                <th style="width:10px;border-bottom:1px solid #000;"></th>
                <th style="width:60px;text-align:left;border-bottom:1px solid #000;">Currency</th>
                <th style="width:90px;text-align:right;border-bottom:1px solid #000;">Basic Rate</th>
                <th style="width:90px;text-align:right;border-bottom:1px solid #000;">Exc. Rate</th>
                <th style="width:120px;text-align:right;border-bottom:1px solid #000;">Amount <br></th>
            </tr>
        </thead>
        <tbody> ';

        for ($i = 0; $i < count($rsItemDetail); $i++) {

            $rsItemUnitCol = $rsItemUnitCols[$rsItemDetail[$i]['unitkey']];

            $itemName = (!empty($rsItemDetail[$i]['aliasname'])) ? $rsItemDetail[$i]['aliasname'] : $rsItemDetail[$i]['itemname'];

            $rsCurrencyCol = $rsCurrencyCols[$rsItemDetail[$i]['currencykey']];

            $currencyName = $rsCurrencyCol[0]['name'];
            $priceInUnit = $obj->formatNumber($rsItemDetail[$i]['priceinunit'], $decimal);

 //           $totalInIDR = $rsItemDetail[$i]['beforetaxdetailvalue'];
 //           if (($rs[0]['currencykey'] != CURRENCY['idr']) && ($rsItemDetail[$i]['currencykey'] != CURRENCY['idr'])) {
 //               $totalInIDR *= $rsItemDetail[$i]['rate'];
 //           } 

            $html .= '
                <tr>
                    <td style="width:20px;text-align:right;">'. ($i+1) .'</td>
                    <td style="width:160px;">'. $itemName .'</td>
                    <td style="width:70px;">'.$rsItemUnitCol[0]['name'].'</td>
                    <td style="width:60px;text-align:right;">' . $obj->formatNumber($rsItemDetail[$i]['qtyinbaseunit'],3) . '</td>
                    <td style="width:10px;"></td>
                    <td style="width:60px;text-align:left;">'. $currencyName.'</td>
                    <td style="width:90px;text-align:right;">'. $priceInUnit.'</td>
                    <td style="width:90px;text-align:right;">'. $obj->formatNumber($rsItemDetail[$i]['rate'], $decimal) .'</td>
                    <td style="width:120px;text-align:right;">'. $obj->formatNumber($rsItemDetail[$i]['beforetaxdetailvalue'], $decimal) .'</td>
                </tr>
            ';

        }

        $html .= '
        </tbody>
    </table>';


    
        $subTotal = $rs[0]['beforetaxtotal'];
        $taxValue = $rs[0]['taxvalue'];
        $otherCost = $rs[0]['othercost'];
        $downPayment = $rs[0]['totaldownpayment'];
        $totalPayable = $rs[0]['grandtotal'];

        if($downPayment > 0) {
            $totalPayable -= $downPayment;
        }

    $sayNumber = $obj->sayNumberInEnglish($totalPayable);

    $html .= '<table cellpadding="2" width="100%">
            <tr>
                <td colspan="9"></td>            
            </tr>
            <tr>
                <td style="border-bottom:1px solid #000;width:680px;" colspan="9">'. ($rs[0]['currencykey'] == CURRENCY['idr'] ? 'IDR' : 'US DOLLAR') .' : '.strtoupper($sayNumber).' ONLY</td>            
            </tr>
    </table>';


    $html .= '<table cellpadding="2" width="100%"><tr>
        <td style="width:425px;font-size:10px;"><table>
            <tr>
                <td style="width:115px;"></td>
                <td style="width:10px;text-align:center;"></td>
                <td style="width:275px;text-align:right;"></td>
            </tr>
            <tr>
                <td style="width:130px;">Payment Communcation</td>
                <td style="width:10px;text-align:center;">:</td>
                <td style="width:275px;">'. $rs[0]['code'] .'</td>
            </tr>
            <tr>
                <td style="width:130px;">Bank Name</td>
                <td style="width:10px;text-align:center;">:</td>
                <td style="width:275px;">'. $bankName .'</td>
            </tr>
            <tr>
                <td style="width:130px;">Bank Address</td>
                <td style="width:10px;text-align:center;">:</td>
                <td style="width:275px;">'. $bankAddress .'</td>
            </tr>
            <tr>
                <td style="width:130px;">Beneficiary Name</td>
                <td style="width:10px;text-align:center;">:</td>
                <td style="width:275px;">'. $bankAccountName .'</td>
            </tr>
            <tr>
                <td style="width:130px;">Beneficiary Address</td>
                <td style="width:10px;text-align:center;">:</td>
                <td style="width:275px;">'. $obj->loadSetting('companyAddress') .'</td>
            </tr>
            <tr>
                <td style="width:130px;">SWIFT Code</td>
                <td style="width:10px;text-align:center;">:</td>
                <td style="width:275px;">'. $swiftCode .'</td>
            </tr>
            <tr>
                <td style="width:130px;">Account Number</td>
                <td style="width:10px;text-align:center;">:</td>
                <td style="width:275px;">479-9900111 (IDR) <br>479-9988388 (USD)</td>
            </tr>
        </table></td>
        <td style="width:250px;"><table>
            <tr>
                <td style="width:115px;"></td>
                <td style="width:10px;text-align:center;"></td>
                <td style="width:130px;text-align:right;"></td>
            </tr>
            <tr>
                <td style="width:115px;">Sub Total</td>
                <td style="width:10px;text-align:center;">:</td>
                <td style="width:130px;text-align:right;">'. $obj->formatNumber($subTotal, $decimal) .'</td>
            </tr>';

            if($taxValue > 0) {
                $html .='
                <tr>
                    <td style="width:115px;">VAT</td>
                    <td style="width:10px;text-align:center;">:</td>
                    <td style="width:130px;text-align:right;">' . $obj->formatNumber($taxValue, $decimal) . '</td>
                </tr>';
            }

            if ($otherCost > 0) {
                $html .= '
                <tr>
                    <td style="width:115px;">Other Cost</td>
                    <td style="width:10px;text-align:center;">:</td>
                    <td style="width:130px;text-align:right;">' . $obj->formatNumber($otherCost, $decimal) . '</td>
                </tr>';
            }

            if($downPayment > 0) {
            $html .='
            <tr>
                <td style="width:115px;">Downpayment</td>
                <td style="width:10px;text-align:center;">:</td>
                <td style="width:130px;text-align:right;">' . $obj->formatNumber($downPayment, $decimal) . '</td>
            </tr>';
            }

            $html .='
            <tr>
                <td style="width:115px;border-top:1px solid #000;">Total Payable Amount</td>
                <td style="width:10px;text-align:center;border-top:1px solid #000;">:</td>
                <td style="width:130px;text-align:right;border-top:1px solid #000;">'. $obj->formatNumber($totalPayable, $decimal) .'</td>
            </tr>
        </table></td>
    </tr></table>';

    $grandTotal = ($rs[0]['currencykey'] == CURRENCY['idr']) ? $rs[0]['grandtotal'] : $rs[0]['grandtotal'] * $rs[0]['rate'];
    $above5Million = ($grandTotal > 5000000) ? true : false;

    $html .= '<table cellpadding="2" width="680px">
            <tr>
                <td style="font-size:10px; width: 400px">
                <span style="font-weight:bold;">Terms & Conditions : </span>
                <ul style="text-align: justify; margin: 0; padding: 0;">
                    <li style="margin-left: 0 !important; padding: 0;">Payments must be made in accordance with the agreed payment terms specific to each transaction.</li>
                    <li style="margin-left: 0; padding: 0;">The total amount includes applicable taxes unless specified otherwise. Any withholding tax (PPh 23) must be deducted and reported by the customer.</li>
                    <li style="margin-left: 0; padding: 0;">The applicable VAT (PPN) will be charged in accordance with prevailing laws and regulations.</li>
                    <li style="margin-left: 0; padding: 0;">Please make payments to the account specified above. All transfer fees are the responsibility of the customer.</li>
                    <li style="margin-left: 0; padding: 0;">For any questions or clarifications, please contact our Finance Department at aulya@fortisea.com or imam.ghozalih@fortisea.com</li>
                </ul>
                </td>';

    if($above5Million) {
    $html .= '<td> <br>
                    <table style="text-align:center;">
                    <tr><td><span style="font-size:10px;"><span style="font-size:10px;">Best Regards, </span><br>' . $companyName . '</span></td></tr>
                    <tr><td></td></tr>
                    <tr><td></td></tr>
                    <tr><td></td></tr>
                    <tr><td></td></tr>
                    <tr><td></td></tr>
                    <tr><td></td></tr>
                    <tr><td></td></tr>
                    <tr><td style="text-decoration:underline; font-size:10px;">Imam Ghozalih</td></tr>
                    <tr><td style="font-size:10px;">Finance Manager</td></tr>
                    </table>
                </td>';
    }
            
    $html .= '</tr></table>';
    $html = '<span style="color:#202b5e">'.$html.'</span>';
    return $html;
}

?>
