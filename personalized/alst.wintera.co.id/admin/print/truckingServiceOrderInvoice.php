<?php
$pdf->setCustomSettings(
    array(
        'showPrintHeader' => false,
        'marginFooter' => 20,
        'footer' => '',
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
    $consignee = new Consignee();
    $employee = new Employee();

    $rs = $dataset['rs'];
    $rsCustomer = $customer->searchData('', '', true, ' and ' . $customer->tableName . '.pkey = ' . $obj->oDbCon->paramString($rs[0]['customerkey']) . '');
    $rsPaymentMethod = $paymentMethod->getDataRowById($rs[0]['companybankkey']);
    $rsDetail = $obj->getDetailWithRelatedInformation($rs[0]['pkey']);
    $rsEmployee = $employee->getDataRowById($rs[0]['confirmedby'] ?? '');

    $employeeNameConfirmed = $rsEmployee[0]['name'];
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
    $stamp = $_GET['stamp'];
    $img = ($stamp == '1') ? '<img src="'.PERSONALIZED_DOC_PATH.'include/img/logo-invoice.jpg" style="height: 120px">' : '';
    $logo = '<img src="'.PERSONALIZED_DOC_PATH.'include/img/logo.jpg" style="height: 120px">';

    $invoiceTo = $rsCustomer[0]['name'] . '<br><span>' . nl2br($rsCustomer[0]['address']) . '</span>';
    if ($rs[0]['invoiceto'] == 1) {
        $invoiceTo = '<span style="font-weight:bold;">' . $rsCustomer[0]['name'] . '</span><br><span>' . nl2br($rsCustomer[0]['address']) . '</span>';
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
    </style>';

        $html .= '<table>
        <tr>
            <td style="width:180px;"> 
                '. $logo .'
            </td>
            <td style="width:530px; align-vertical:middle;"><span style="font-weight:bold; font-size:20px;">'.$obj->loadSetting('companyName').'</span>
            <br/>
            '.$obj->loadSetting('companyAddress').'
            <br/>
            Phone: 6224- 3521980
            </td>
        </tr>
    </table>';
    $html .= '<div style="border-bottom: 1px solid #333;"></div>
        <div style="clear:both"></div> ';
  
    $html .= '<table cellpadding="2" > 
<tr><td><div class="title" style="font-size: 2em">INVOICE</div></td></tr>
<tr><td><div class="subtitle" style="font-size:1.2em;">No : '.$rs[0]['code'].'</div></td></tr>
</table> 

<div style="clear:both"></div>
<table>
<tr>
<td >
<table cellpadding="2"> 
<tr><td colspan="3" class="header-row-header"></td></tr> 
<tr><td colspan="3" class="header-row-header">To : </td></tr> 
<tr><td colspan="3"  style="width: 300px;">'.$invoiceTo.'</td></tr>  
</table> 
</td>
<td></td>
</tr>
<div style="clear:both"></div> ';


    $widthNo = 'width:20px;';
    $widthDate = 'width:70px;';
    $widthRoute = 'width:100px;';
    $widthKet = 'width:130px;';
    $widthTruckType = 'width:80px;';
    $widthTruckNo = 'width:90px;';
    $widthPrice = 'width:90px;';
    $widthAddCost = 'width:90px;';
    $widthTotalPrice = 'width:105px;';

    $fontSize = 'font-size:0.9em;';

    $html .= '<table cellpadding="2" width="100%">
            <thead>
                <tr>
                    <th class="border-left-top-right-bottom align-center" style="' . $widthNo . $fontSize . '">No</th>
                    <th class="border-top-right-bottom align-center" style="' . $widthDate . $fontSize . '">Tanggal Muat/<br>Bongkar</th>
                    <th class="border-top-right-bottom align-center" style="width:80px;' . $fontSize . '">Surat Jalan</th>
                    <th class="border-top-right-bottom align-center" style="width:80px;' . $fontSize . '">No Container/<br>No DO</th>
                    <th class="border-top-right-bottom align-center" style="width:200px;' . $fontSize . '">Keterangan</th>
                    <th class="border-top-right-bottom align-center" style="width:28px;' . $fontSize . '">Qty.</th>
                    <th class="border-top-right-bottom align-center" style="' . $widthPrice . $fontSize . '">Harga</th>
                    <th class="border-top-right-bottom align-center" style="' . $widthTotalPrice . $fontSize . '">Jumlah</th>
                </tr>
            </thead>
            <tbody>
        ';
    for ($i = 0; $i < count($rsDetail); $i++) {

        $rsJO = $rsJobOrderCols[$rsDetail[$i]['salesorderkey']];
        $rsWO = $rsWorkOrderCols[$rsDetail[$i]['salesorderkey']];
        $rsItemDetailCol = $rsItemDetailCols[$rsDetail[$i]['pkey']];
        $rsJOSellingDetailCol = $rsJOSellingDetailCols[$rsDetail[$i]['salesorderkey']];

        $rsJODetailCol = $rsJODetailCols[$rsDetail[$i]['salesorderkey']];


        $cityArea = $rsJO[0]['stuffinglocationfromname'] . ' - ' . $rsJO[0]['locationname'];
        $policeNumber = ($rsWO[0]['isoutsource'] ? $rsWO[0]['outsourcecarregistrationnumber'] : $rsWO[0]['policenumber']);

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

        $stores = array_column($rsJOSellingDetailCol, 'store');
        $store = implode('+', $stores);
        $route = $rsWO[0]['routefrom'] . ' - ' . $rsWO[0]['routeto'];
        $detailJODesc = $rsJODetailCol[0]['trdesc'];

        $html .= '
                    <tr>
                        <td class="border-left-right-bottom align-center    " style="' . $widthNo  . $fontSize. '">' . ($i + 1) . '</td>
                        <td class="border-right-bottom align-center" style="' . $widthDate . $fontSize . '">' . $obj->formatDBDate($rsDetail[$i]['trdate'], 'd-M-y') . '</td>
                        <td class="border-right-bottom align-center" style="width:80px;' . $fontSize . '">' . $rsWO[0]['code'] . '</td>
                        <td class="border-right-bottom align-center" style="width:80px;' . $fontSize . '">'. $rsWO[0]['containernumber'] .'<br/>' . $rsJobOrder[0]['donumber'] . '</td>
                        <td class="border-right-bottom align-left" style="width:200px;' . $fontSize . '">' . $rsWO[0]['containername'] .'<br/>' . '<span style="font-style:italic;"> Rute: '.$route .'</span></td>
                        <td class="border-right-bottom align-center" style="width:28px;'. $fontSize . '">' .$obj->formatNumber($rsWO[0]['qtyinbaseunit']) . '</td>
                        <td class="border-right-bottom align-center " style="' . $widthPrice . $fontSize . '">'.$rsCurrency[0]['name'].' ' . $obj->formatNumber($truckingCost) . '</td>
                        <td class="border-right-bottom align-center " style="' . $widthTotalPrice . $fontSize . '">' . $rsCurrency[0]['name'].' ' . $obj->formatNumber($rsDetail[$i]['amount']) . '</td>
                    </tr>
            ';
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

    $colspan = 7;


    $html .= '<tr style="background-color:#fcccfc;">
        <td class="border-left-right-bottom align-left font-bold" colspan="' . $colspan . '" >TOTAL INVOICE TO BE PAID</td>
        <td class="border-right-bottom align-center font-bold" style="' . $widthTotalPrice . '">' . $rsCurrency[0]['name'] . ' ' . $obj->formatNumber($grandTotal) . '</td>
    </tr>';

    $html .= '
            </tbody>
        </table>
    ';

    $html .= '<div style="clear:both"></div>';

    $sayNumber = $obj->sayNumber($grandTotal);

    $html .= '<table cellpadding="2" width="100%">
    <tr>
        <td style="width:465px";>
            <table cellpadding="0"><tr><td class="font-bold">PLEASE SEND ALL NON-CASH PAYMENT TO:</td></tr></table>
            <table cellpadding="0">
                <tr style="font-style: italic;">
                    <td style="width:90px;' . $fontSize . '">BANK NAME</td>
                    <td style="width:10px;text-align:center;' . $fontSize . '">:</td>
                    <td style="width:300px;' . $fontSize . '">' . strtoupper($bankName) . ' ' . strtoupper($branch) . '</td>
                </tr>
                <tr style="font-style: italic;">
                    <td style="width:90px;' . $fontSize . '">BANK ACCOUNT</td>
                    <td style="width:10px;text-align:center;' . $fontSize . '">:</td>
                    <td style="width:300px;' . $fontSize . '">' . $accountNumber . '</td>
                </tr>
                <tr style="font-style: italic;">
                    <td style="width:90px;' . $fontSize . '">ACCOUNT NAME</td>
                    <td style="width:10px;text-align:center;' . $fontSize . '">:</td><td style="width:300px;' . $fontSize . '">' . strtoupper($accountName) . '</td>
                </tr>
                <tr><td></td></tr>
                <tr><td></td></tr>
                <tr>
                    <td style="width:400px;color:red;" class="font-bold">PAYMENT BY T/T MUST BE RECEIVED IN FULL AMOUNT</td>
                </tr>
                <tr>
                    <td>* For payment confirmation kindly contact</td>
                </tr>
                <tr>
                    <td>024-3521980 / Email : finance@alstrans-id.com</td>
                </tr>
            </table>
        </td>
        
        <td style="width:200px";>
            <table cellpadding="3">
                <tr>
                    <td style="width:85px;" class="align-left">Date</td>
                    <td style="width:10px;" class="align-left">:</td>
                    <td style="width:95px;">' . $obj->toLocalDate($obj->formatDBDate($rs[0]['trdate'], 'd-M-y')) . '</td>
                </tr>   
                <tr>
                    <td class="align-left">Signed By</td>
                    <td>:</td>
                    <td>' . $employeeNameConfirmed . '</td>
                </tr>   
                <tr>
                    <td style="vertical-align: top;" colspan="3">
                        ' . $img . '
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    </table>';
    return $html;
}

    ?>
