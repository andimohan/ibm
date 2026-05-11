<?php


$companyAddress = $class->loadSetting('companyAddress');

$pdf->setCustomSettings(
    array(
        'showPrintHeader' => false,
        'showPrintFooter' => true,
        'footer' => '<table width="100%">
            <tr>
                <td style="width:675px;text-align:left;">ARCADE BUSSINESS CENTER 6 FLOOR UNIT 6-03</td>
            </tr>
            <tr>
                <td style="width:675px;text-align:left;">JL PANTAI INDAH UTARA 2 KAV.C1, KAPUK MUARA , PENJARINGAN, JAKARTA UTARA</td>
            </tr>
            <tr>
                <td style="width:675px;text-align:left;">DKI JAKARTA, 14460</td>
            </tr>
            <tr>
                <td style="width:675px;text-align:left;">eramandiritrans@gmail.com </td>
            </tr>
        </table>',
        'marginFooter' => '15px'
    )
);

includeClass('TruckingServiceOrderInvoice.class.php');
$truckingServiceOrderInvoice = createObjAndAddToCol(new TruckingServiceOrderInvoice());

$obj = $truckingServiceOrderInvoice;

$generateReportContent = function ($dataset) {

    $obj = new TruckingServiceOrderInvoice();
    $truckingServiceOrder = new TruckingServiceOrder();
    $paymentMethod = new PaymentMethod();
    $termOfPayment = new TermOfPayment();
    $customer = new Customer();
    $customCode = new CustomCode();
    $consignee = new Consignee();
    $truckingServiceWorkOrder = new TruckingServiceWorkOrder();
    $cost = new Service(TRUCKING_SERVICE, 1);
    $employee = new Employee();
    $truckingService = new Service();

    $rs = $dataset['rs'];

    $rsInvoiceType = $customCode->searchData($customCode->tableName . '.pkey', $rs[0]['customcodekey'], true);
    $rsPaymentMethod = $paymentMethod->getDataRowById($rs[0]['companybankkey']);
    $rsTermOfPayment = $termOfPayment->getDataRowById($rs[0]['termofpaymentkey']);
    $rsCustomer = $customer->getDataRowById($rs[0]['customerkey']);

    $rsDetail = $obj->getDetailWithRelatedInformation($rs[0]['pkey']);
    $arrJobOrderKey = array_column($rsDetail, 'salesorderkey');

    $rsSOHeader = $truckingServiceOrder->searchData('', '', true, ' and ' . $truckingServiceOrder->tableName . '.pkey in (' . $obj->oDbCon->paramString($arrJobOrderKey, ',') . ')');
    $rsSOHeaderCols = $obj->reindexDetailCollections($rsSOHeader, 'pkey');
    $rsSODetail = $truckingServiceOrder->getDetailWithRelatedInformation($arrJobOrderKey);
    $rsSODetailCols = $obj->reindexDetailCollections($rsSODetail, 'pkey');

    $rsItemDetail = $obj->getItemDetail($rs[0]['pkey'], 'refheaderkey');
    $rsItemDetailCols = $obj->reindexDetailCollections($rsItemDetail, 'refkey');

    $rsWorkOrder = $truckingServiceWorkOrder->searchData('','',true, ' and ' . $truckingServiceWorkOrder->tableName.'.refkey in ('. $obj->oDbCon->paramString($arrJobOrderKey ,',') .') and '. $truckingServiceWorkOrder->tableName .'.statuskey = 3 ');
    $rsWorkOrderCols = $obj->reindexDetailCollections($rsWorkOrder, 'refdetailkey');

    $top = (empty($rsTermOfPayment)) ? 0 : $rsTermOfPayment[0]['duedays'];
    $date = new DateTime($rs[0]['trdate']);
    $date->add(new DateInterval('P' . $top . 'D'));
    $dueDays = $date->format('d-M-Y');

    $invoiceTo = $rsCustomer[0]['name'];
    $invoiceToAddress = nl2br($rsCustomer[0]['address']);
    $email = $rsCustomer[0]['email'];
    $phone = $rsCustomer[0]['phone'];
    if($rs[0]['invoiceto'] == 1){
        $invoiceTo = $rsCustomer[0]['name'];
        $invoiceToAddress = nl2br($rsCustomer[0]['address']);
        $email = $rsCustomer[0]['email'];
        $phone = $rsCustomer[0]['phone'];
    } else {
        //bill to consignee
        $totalRs = count($rsDetail);
        for($i=0;$i<$totalRs;$i++){  
            if (!empty($rsDetail[$i]['salesorderkey'])){ 
                $rsSOHeader = $truckingServiceOrder->getDataRowById($rsDetail[$i]['salesorderkey']);  
                $rsConsignee = $consignee->getDataRowById($rsSOHeader[0]['consigneekey']);  
                $invoiceTo = $rsConsignee[0]['name'];
                $invoiceToAddress = nl2br($rsConsignee[0]['address']);
                $email = $rsConsignee[0]['email'];
                $phone = $rsConsignee[0]['phone'];
                break;
            }
        }   
    }

    
    $invoiceTitle  = 'INVOICE';
    if($rsInvoiceType[0]['isreimburse'] == 1) {
        $invoiceTitle = (!empty($rsInvoiceType[0]['title'])) ? $rsInvoiceType[0]['title'] : $rsInvoiceType[0]['name'];
    }

    $companyLogo = $obj->loadSetting('companyLogo');
    $imgLetterhead = $obj->phpThumbURLSrc . 'setting/companyLogo/' . $companyLogo;
    $logo = '<img src="' . $imgLetterhead . '" style="height: 70px">';
    
    $html = $obj->printSetting['defaultStyle'];

    $html.='<table cellpadding="2" width="100%"><tr>
    <td style="width:337px;">'.$logo.'</td>
    <td style="width:337px;"></td>
    </tr></table>';
    $html .= '<div style="clear:both"></div>';
    $html .='<table cellpadding="2" width="100%"><tr>
        <td style="width:425px;border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;"><table cellpadding="2" width="100%">
            <tr>
                <td style="width:100px;">INVOICE #</td>
                <td style="width:10px;text-align:center;">:</td>
                <td style="width:250px">'. $rs[0]['code'] .'</td>
            </tr>
            <tr>
                <td style="width:100px;">INVOICE DATE</td>
                <td style="width:10px;text-align:center;">:</td>
                <td style="width:250px;">'. $obj->formatDBDate($rs[0]['trdate'], 'd-M-Y') .'</td>
            </tr>
        </table></td>
        <td style="width:250px;border-top:1px solid black;border-right:1px solid black;border-bottom:1px solid black;"><table cellpadding="2" width="100%">
            <tr>
                <td style="width:100px;"></td>
                <td style="width:10px;text-align:center;"></td>
                <td style="width:140px"></td>
            </tr>
            <tr>
                <td style="width:100px;">JATUH TEMPO</td>
                <td style="width:10px;text-align:center;">:</td>
                <td style="width:140px">'. $dueDays.'</td>
            </tr>
        </table></td>
    </tr>
    <tr><td style="width:675px;"></td></tr>
    <tr>
    <td style="width:675px;border-top:1px solid black;border-bottom:1px solid black; border-left:1px solid black; border-right:1px solid black;"><table cellpadding="2">
            <tr>
                <td style="width:100px;">Kepada</td>
                <td style="width:10px;text-align:center;">:</td>
                <td style="width:560px;">'.$invoiceTo.'</td>
            </tr>
            <tr>
                <td style="width:100px;">Alamat</td>
                <td style="width:10px;text-align:center;">:</td>
                <td style="width:560px;">'. $invoiceToAddress .'</td>
            </tr>
            <tr>
                <td style="width:100px;">Telephone</td>
                <td style="width:10px;text-align:center;">:</td>
                <td style="width:560px;">'.$phone.'</td>
            </tr>
            <tr>
                <td style="width:100px;">Email</td>
                <td style="width:10px;text-align:center;">:</td>
                <td style="width:560px;">'. $email .'</td>
            </tr>
    </table></td>
    </tr>
    </table>';
    $html .='<div style="clear:both"></div>';

    $html .= '<table cellpadding="2" width="100%"><tr><td style="width:100%;background-color:#000000;color:#ffffff;font-weight:bold;text-align:right;font-size:15px">'. strtoupper($invoiceTitle) .'</td></tr></table>';
    
    $html .='<div style="clear:both"></div>';
    
    $html .='<table cellpadding="2" width="100%">
            <thead>
                <tr>
                    <th style="width:40px;text-align:center;border-left:1px solid black; border-top:1px solid black; border-right:1px solid black; border-bottom:1px solid black;">No</th>
                    <th style="width:80px;text-align:center;border-top:1px solid black; border-right:1px solid black; border-bottom:1px solid black;">Date</th>
                    <th style="width:90px;text-align:center;border-top:1px solid black; border-right:1px solid black; border-bottom:1px solid black;">Destination</th>
                    <th style="width:90px;text-align:center;border-top:1px solid black; border-right:1px solid black; border-bottom:1px solid black;">Item</th>
                    <th style="width:80px;text-align:center;border-top:1px solid black; border-right:1px solid black; border-bottom:1px solid black;">Qty</th>
                    <th style="width:90px;text-align:center;border-top:1px solid black; border-right:1px solid black; border-bottom:1px solid black;">Price</th>
                    <th style="width:90px;text-align:center;border-top:1px solid black; border-right:1px solid black; border-bottom:1px solid black;">Amount</th>
                    <th style="width:115px;text-align:center;border-top:1px solid black; border-right:1px solid black; border-bottom:1px solid black;">Desc</th>
                </tr>
            </thead>
            <tbody>
            ';

            $no = 1;
            for($i=0; $i<count($rsDetail); $i++) {

                $rsItemDetailCol = $rsItemDetailCols[$rsDetail[$i]['pkey']];
                
                $rsWorkOrder;
                
                foreach($rsItemDetailCol as $itemRow) {
                    
                    $rsWorkOrderCol = $rsWorkOrderCols[$itemRow['refsodetailkey']];
                    $rsSODetailCol = $rsSODetailCols[$itemRow['refsodetailkey']];
                    $desc = $rsSODetailCol[0]['trdesc'];

                    $arrContainerNumber = array();
                    $arrDestination = array();
                    foreach($rsWorkOrderCol as $workOrderRow) {
                        $containerNumber = $workOrderRow['containernumber'];
                        $container2Number = (!empty($workOrderRow['container2number']) ? ',<br>'.$workOrderRow['container2number'] : '');
                        $containerNumber .= $container2Number;

                        if(!empty($containerNumber)) {
                            array_push($arrContainerNumber,$containerNumber);
                        }

                        if (!empty($workOrderRow['routeto'])) {

                            $routeTo = $workOrderRow['routeto'];
                            
                            if (!in_array($routeTo, $arrDestination)) {
                                array_push($arrDestination, $routeTo);
                            }
                        }
                    }

                    $containerNumbers = implode('<br>', $arrContainerNumber);
                    $destinations = implode(',<br>', $arrDestination);

                    $itemName = (!empty($itemRow['aliasname']) ? $itemRow['aliasname'] : $itemRow['itemname']);
                    $qty = $obj->formatNumber($itemRow['qtyinbaseunit']);

                    $qtyItem = $qty . ' x ' . $itemName;

                    $priceInUnit = $obj->formatNumber($itemRow['priceinunit']);
                    $total = $obj->formatNumber($itemRow['total']);

                $html .='
                    <tr>
                        <td style="width:40px;text-align:right;border-left:1px solid black; border-right:1px solid black; border-bottom:1px solid black;">'. $no .'</td>
                        <td style="width:80px;text-align:center;border-right:1px solid black; border-bottom:1px solid black;">'. $obj->formatDBDate($rsDetail[$i]['trdate'], 'd-M-Y') .'</td>
                        <td style="width:90px;border-right:1px solid black; border-bottom:1px solid black;">'. $destinations .'</td>
                        <td style="width:90px;border-right:1px solid black; border-bottom:1px solid black;">'. $containerNumbers.'</td>
                        <td style="width:80px;text-align:center;border-right:1px solid black; border-bottom:1px solid black;">'. $qtyItem .'</td>
                        <td style="width:90px;text-align:right;border-right:1px solid black; border-bottom:1px solid black;">'.$priceInUnit.'</td>
                        <td style="width:90px;text-align:right;border-right:1px solid black; border-bottom:1px solid black;">'.$total.'</td>
                        <td style="width:115px;border-right:1px solid black; border-bottom:1px solid black;">'. $desc.'</td>
                    </tr>';

                    $no++;
                }

            }

            $subtotal = $rs[0]['subtotal'];

            $beforeTaxTotal = $rs[0]['beforetaxtotal'];

            $discountAmount = $rs[0]['finaldiscount'];
            $finalDiscountType = $rs[0]['finaldiscounttype'];

            if ($finalDiscountType == 2) {
                $discount = $rs[0]['finaldiscount'];
                $discountAmount = $rs[0]['grandtotal'] * ($discount / 100);
            }

            $taxValue = $rs[0]['taxvalue'];
            $taxPercentage = $rs[0]['taxpercentage'];
            $isPriceIncludeTax = $rs[0]['ispriceincludetax'];

            $stampFee = $rs[0]['stampfee'];

            $downPayment = $rs[0]['totaldownpayment'];

            $grandTotal = $rs[0]['outstanding'];


            $html .='
                <tr>
                    <td colspan="6" style="text-align:right;border-bottom:1px solid black;border-left:1px solid black;border-right:1px solid black;">INVOICE SUBTOTAL</td>
                    <td style="text-align:right;border-bottom:1px solid black;border-right:1px solid black;">'. $obj->formatNumber($subtotal) .'</td>
                    <td></td>
                </tr>
            ';

            $html .='
                <tr>
                    <td colspan="6" style="text-align:right;border-bottom:1px solid black;border-left:1px solid black;border-right:1px solid black;">BEFORE TAX</td>
                    <td style="text-align:right;border-bottom:1px solid black;border-right:1px solid black;">'. $obj->formatNumber($beforeTaxTotal) .'</td>
                    <td></td>
                </tr>
            ';

            if($discountAmount > 0) {

                $html .= '
                        <tr>
                            <td colspan="6" style="text-align:right;border-bottom:1px solid black;border-left:1px solid black;border-right:1px solid black;">DISCOUNT </td>
                            <td style="text-align:right;border-bottom:1px solid black;border-right:1px solid black;">' . $obj->formatNumber($discountAmount) . '</td>
                            <td></td>
                        </tr>
                    ';

            }

            if ($taxValue > 0) {

                $html .= '
                        <tr>
                            <td colspan="6" style="text-align:right;border-bottom:1px solid black;border-left:1px solid black;border-right:1px solid black;">TAX '.($isPriceIncludeTax ? '[Include]' : '') .'  (' . $obj->formatNumber($taxPercentage) . '%) </td>
                            <td style="text-align:right;border-bottom:1px solid black;border-right:1px solid black;">' . $obj->formatNumber($taxValue) . '</td>
                            <td></td>
                        </tr>
                    ';

            }

            if ($stampFee > 0) {

                $html .= '
                        <tr>
                            <td colspan="6" style="text-align:right;border-bottom:1px solid black;border-left:1px solid black;border-right:1px solid black;">STAMP FEE</td>
                            <td style="text-align:right;border-bottom:1px solid black;border-right:1px solid black;">' . $obj->formatNumber($stampFee) . '</td>
                            <td></td>
                        </tr>
                    ';

            }

            if ($downPayment > 0) {

                $html .= '
                        <tr>
                            <td colspan="6" style="text-align:right;border-bottom:1px solid black;border-left:1px solid black;border-right:1px solid black;">DOWNPAYMENT</td>
                            <td style="text-align:right;border-bottom:1px solid black;border-right:1px solid black;">' . $obj->formatNumber($downPayment) . '</td>
                            <td></td>
                        </tr>
                    ';

            }

            $html .='
                <tr>
                    <td colspan="6" style="text-align:right;border-bottom:1px solid black;border-left:1px solid black;border-right:1px solid black;">TOTAL</td>
                    <td style="text-align:right;border-bottom:1px solid black;border-right:1px solid black;">'. $obj->formatNumber($grandTotal) .'</td>
                    <td></td>
                </tr>
            ';

            $html .='
            </tbody>
    </table>';

    $html .= '<div style="clear:both"></div>';

    $sayNumber = $obj->sayNumber($rs[0]['outstanding']);

    $html .= '
        <table cellpadding="2" width="100%"><tr><td style="width:100%;border:1px solid black;">Terbilang : '.ucwords($sayNumber).' Rupiah</td></tr></table>
    ';

    $html .= '<div style="clear:both"></div>';

    $html .= '<table cellpadding="2"><tr>
    <td style="width:475px;"><table cellpadding="2">
        <tr>
            <td style="width:100px;">No Rek Bank</td>
            <td style="width:10px;text-align:center;">:</td>
            <td style="width:180px;">' . $rsPaymentMethod[0]['bankaccountname'] . '</td>
        </tr>
        <tr>
            <td style="width:100px;">Bank</td>
            <td style="width:10px;text-align:center;">:</td>
            <td style="width:180px;">' . $rsPaymentMethod[0]['bankname'] . '</td>
        </tr>
        <tr>
            <td style="width:100px;">Cabang</td>
            <td style="width:10px;text-align:center;">:</td>
            <td style="width:180px">' . $rsPaymentMethod[0]['branch'] . '</td>
        </tr>
        <tr>
            <td style="width:100px;">A/N</td>
            <td style="width:10px;text-align:center;">:</td>
            <td style="width:180px;">' . $rsPaymentMethod[0]['bankaccountname'] . '</td>
        </tr>
        <tr>
            <td style="width:100px;">A/C</td>
            <td style="width:10px;text-align:center;">:</td>
            <td style="width:180px;">' . $rsPaymentMethod[0]['bankaccountnumber'] . '</td>
        </tr>
    </table></td>
    <td style="width:200px;">
    <table cellpadding="2">
        <tr>
            <td style="width:150px;text-align:center;">Hormat Kami</td>
        </tr>
        <tr><td style="width:150px;text-align:center;"></td></tr>
        <tr><td style="width:150px;text-align:center;"></td></tr>
        <tr><td style="width:150px;text-align:center;"></td></tr>
        <tr><td style="width:150px;text-align:center;"></td></tr>
        <tr><td style="width:150px;text-align:center;text-decoration:underline;">Finance</td></tr>
    </table>
    </td>
    </tr></table>';


    return $html;
}

?>