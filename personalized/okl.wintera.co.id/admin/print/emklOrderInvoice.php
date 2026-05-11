<?php

$pdf->setCustomSettings(
    array(
        'showPrintHeader' => false,
        'showPrintFooter' => false,
        'footer' => '',
    )
);


includeClass('EMKLOrderInvoice.class.php');
$emklOrderInvoice = createObjAndAddToCol(new EMKLOrderInvoice());

$obj = $emklOrderInvoice;

$generateReportContent = function ($dataset) {

    $obj = new EMKLOrderInvoice();
    $emklJobOrder = new EMKLJobOrder();
    $container = new Container();
    $employee = new Employee();
    $customer = new Customer();
    $currency = new Currency();
    $arrCurrency = $currency->searchData();
    $arrCurrency = array_column($arrCurrency, 'name', 'pkey');
    $rsContainer = $container->searchData();
    $rsContainer = array_column($rsContainer, 'name', 'pkey');
    $customCode = new CustomCode();
    $paymentMethod = new PaymentMethod();

    $rs = $dataset['rs'];


    $rsDetail = $obj->getDetailWithRelatedInformation($rs[0]['pkey']);

    $companyLogo = $obj->loadSetting('companyLogo');
    $imgLetterhead = $obj->phpThumbURLSrc . 'setting/companyLogo/' . $companyLogo;
    $logo = '';// '<img src="' . $imgLetterhead . '" style="height: 50px">';

    $companyName = $obj->loadSetting('companyName');
    $companyAddress = $obj->loadSetting('companyAddress');

    $rsInvoiceType = $customCode->searchData($customCode->tableName . '.pkey', $rs[0]['customcodekey'], true);

    $invoiceTitle = (!empty($rsInvoiceType[0]['title'])) ? $rsInvoiceType[0]['title'] : $rsInvoiceType[0]['name'];

    $jokey = $rsDetail[0]['refsalesorderheaderkey'];

    $rsJobOrder = $emklJobOrder->searchDataRow(
        array($emklJobOrder->tableName . '.pkey', $emklJobOrder->tableName . '.code', $emklJobOrder->tableName . '.itemdescription', $emklJobOrder->tableName . '.aju', $emklJobOrder->tableName . '.peb', $emklJobOrder->tableName . '.mblnumber', $emklJobOrder->tableName . '.jobtypekey', $emklJobOrder->tableName . '.weight', $emklJobOrder->tableName . '.volume', $emklJobOrder->tableName . '.loadcontainertypekey', $emklJobOrder->tableName . '.ponumber'),
        ' and ' . $emklJobOrder->tableName . '.pkey = ' . $obj->oDbCon->paramString($jokey)
    );

    $rsCustomer = $customer->getDataRowById($rs[0]['customerkey']);
    $arrCustomer = array();
    if (!empty($rsCustomer[0]['name']))
        array_push($arrCustomer, $rsCustomer[0]['name']);
    if (!empty($rsCustomer[0]['address']))
        array_push($arrCustomer, str_replace(chr(13), '<br>', $rsCustomer[0]['address']));

    $labelTypePIB = ($rsJobOrder[0]['jobtypekey'] == 1) ? 'PIB No. AJU' : 'PEB No. AJU';

    $isLCL = (in_array($rsJobOrder[0]['loadcontainertypekey'], array(EMKL['container']['lcl'],EMKL['container']['lclnc']))) ? true : false;

    if (!$isLCL) {
        $rsItemDetail = $emklJobOrder->getDetailVolume($jokey);
        $arrParty = array();
        foreach ($rsItemDetail as $value) {
            array_push($arrParty, $obj->formatNumber($value['qty'], 0) . ' x ' . $rsContainer[$value['itemkey']]);
        }

        $party = implode(', ', $arrParty);
        $partyLabel = 'Partai';
    } else {
        $party = $emklJobOrder->formatNumber($rsJobOrder[0]['weight']) . ' KG, ' . $emklJobOrder->formatNumber($rsJobOrder[0]['volume'], 2) . ' CBM';
        $partyLabel = 'Volume';
    }

    $rsPaymentMethod = $paymentMethod->getDataRowById($rs[0]['companybankkey']);

    $arrDetailKey = array_column($rsDetail, 'pkey');
    $arrSOKey = array_column($rsDetail, 'salesorderkey');

    $rsItemDetail = $obj->getItemDetail($arrDetailKey);
    $rsItemDetailCols = $obj->reindexDetailCollections($rsItemDetail, 'refkey');


    $rsCargoType = $emklJobOrder->getCargoType($rsJobOrder[0]['containertypekey']);

    $mblNumber = $rsJobOrder[0]['mblnumber'];
    $poNumber = $rsJobOrder[0]['ponumber'];
    $ajuNumber = $rsJobOrder[0]['aju'];
    $cargoType = $rsCargoType[0]['name'];

    $html = $obj->printSetting['defaultStyle'];

    $html .= '<table cellpadding="2" width="100%"><tr>
                    <td style="width:250px;"><span style="font-weight:bold;font-size:14px;"></span>
                    <br><span style="font-size:11px;"></span>
                    </td>
                    <td style="width:275px;"></td>
                    </tr></table>';
    $html .= '<div style="clear:both"></div>';
    $html .= '<div style="clear:both"></div>';
    $html .= '<table cellpadding="2" > 
            <tr><td><div class="title">' . strtoupper($invoiceTitle) . '</div></td></tr>
            <tr><td><div class="subtitle">No. ' . $rs[0]['code'] . '</div></td></tr>
            </table> ';

    $html .='<div style="clear:both"></div>
        <table cellpadding="2">
        <tr>
            <td style="width:400px"></td>
            <td style="width:275px">
            <table>
            <tr><td>Jakarta, '. $obj->formatDBDate($rs[0]['trdate'],'d F Y') .'<br></td></tr>
            </table></td>
        </tr>
        <tr>
            <td style="width:400px">
            <table>
                <tr>
                    <td style="width:100px;font-weight:bold;">Jenis Barang</td>
                    <td style="width:10px;tex-align:center;">:</td>
                    <td style="width:150px;">'. $rsJobOrder[0]['itemdescription'] .'</td>
                </tr>
                <tr>
                    <td style="width:100px;font-weight:bold;">'. $partyLabel .'</td>
                    <td style="width:10px;tex-align:center;">:</td>
                    <td style="width:150px;">'. $party.'</td>
                </tr>
                <tr>
                    <td style="width:100px;font-weight:bold;">No. B/L</td>
                    <td style="width:10px;tex-align:center;">:</td>
                    <td style="width:150px;">'. $mblNumber.'</td>
                </tr>
                <tr>
                    <td style="width:100px;font-weight:bold;">'.$labelTypePIB.'</td>
                    <td style="width:10px;tex-align:center;">:</td>
                    <td style="width:150px;">'. $ajuNumber.'</td>
                </tr>
                <tr>
                    <td style="width:100px;font-weight:bold;">No. PO</td>
                    <td style="width:10px;tex-align:center;">:</td>
                    <td style="width:150px;">'. $poNumber.'</td>
                </tr>
            </table>
            </td>
            <td style="width:275x">
                <table>
                    <tr>
                        <td style="font-weight:bold;">Kepada :</td>
                    </tr>
                    <tr>
                        <td>' . implode('<br>', $arrCustomer) . '</td>
                    </tr>
                </table>
            </td>
        </tr> 
        </table> ';

    $html .= '<div style="clear:both"></div>';

    $labelTable = (($rsInvoiceType[0]['isreimburse'] == 1) ? 'PERINCIAN REIMBURSEMENT' : 'PERINCIAN') . ' :';

    $html .= '<table cellpadding="4" style="border-bottom:1px solid black">
        <tr>
            <td style="width:60px;border-top:1px solid black;border-left:1px solid black;"></td>
            <td style="width:490px;border-top:1px solid black;">' . $labelTable . '</td>
            <td style="width:125px;border-top:1px solid black;border-left:1px solid black; border-right:1px solid black; border-bottom:1px solid black;text-align:center;">JUMLAH RUPIAH</td>
        </tr>';

    for ($i = 0; $i < count($rsDetail); $i++) {

        $detailkey = $rsDetail[$i]['pkey'];

        $rsItemDetailCol = $rsItemDetailCols[$detailkey];


        foreach ($rsItemDetailCol as $itemRow) {


            $itemName = (!empty($itemRow['aliasname']) ? $itemRow['aliasname'] : $itemRow['itemname']);


            $html .= '
                <tr>
                    <td style="width:60px;border-left:1px solid black;"></td>
                    <td style="width:490px;">-&nbsp;' . $itemName . '</td>
                    <td style="width:125px;border-left:1px solid black; border-right:1px solid black;text-align:right;">' . $obj->formatNumber($itemRow['total']) . '</td>
                </tr>
            ';

        }

    }

    // $html .= '<tr>
    //                 <td style="width:60px;border-left:1px solid black; border-bottom:1px solid black;"></td>
    //                 <td style="width:490px; border-bottom:1px solid black;"></td>
    //                 <td style="width:125px;border-left:1px solid black; border-bottom:1px solid black; border-right:1px solid black;"> </td>
    //             </tr>';


    $html .= '</table>';

    $subtotal = $rs[0]['beforetaxtotal'];

    $taxValue = $rs[0]['taxvalue'];

    $otherCost= $rs[0]['othercost'];
    $PPh23Value = $rs[0]['tax23value'];
    $PPh23Percentage = $rs[0]['tax23percentage'];

    $totalDownpayment = $rs[0]['totaldownpayment'];

    $grandTotal = $rs[0]['outstanding'];

    $sayNumber = ($rs[0]['outstanding'] > 0) ? $obj->sayNumber($rs[0]['outstanding']) : 'Nol';
    $arrSubtotal = array();

    if ($taxValue > 0) {
        array_push($arrSubtotal,'<tr>
            <td style="width:80px;">PPN</td>
            <td style="width:40px;text-align:right;"></td>
            <td style="width:125px;text-align:right;border-left:1px solid black; border-right:1px solid black;">' . $obj->formatNumber($taxValue) . '</td>
        </tr>');
    }

    if ($otherCost > 0) {
        array_push($arrSubtotal, '<tr>
            <td style="width:80px;">Materai</td>
            <td style="width:40px;text-align:right;"></td>
            <td style="width:125px;text-align:right;border-left:1px solid black; border-right:1px solid black;">' . $obj->formatNumber($otherCost) . '</td>
        </tr>');
    }


    if ($PPh23Value > 0) {
        array_push($arrSubtotal,'<tr>
            <td style="width:80px;">PPh 23</td>
            <td style="width:40px;text-align:right;">' . $obj->formatNumber($PPh23Percentage,0) . ' %</td>
            <td style="width:125px;text-align:right;border-left:1px solid black; border-right:1px solid black;">' . $obj->formatNumber($PPh23Value) . '</td>
        </tr>');
    }

    if ($totalDownpayment > 0) {
        array_push($arrSubtotal,'<tr>
            <td style="width:80px;">Uang Muka</td>
            <td style="width:40px;text-align:right;"></td>
            <td style="width:125px;text-align:right;border-left:1px solid black; border-right:1px solid black;">' . $obj->formatNumber($totalDownpayment) . '</td>
        </tr>');
    }


    $tableSubtotal ='<table cellpadding="4">
                <tr>
                    <td rowspan="'.(count($arrSubtotal) + 2).'" style="width:100px;">TERBILANG : </td>
                    <td rowspan="'.(count($arrSubtotal) + 2).'" style="width:330px;">' . ucwords($sayNumber) . ' Rupiah.</td>
                    <td style="width:80px;">Sub total</td>
                    <td style="width:40px;text-align:right;"></td>
                    <td style="width:125px;text-align:right;border-left:1px solid black; border-right:1px solid black;">' . $obj->formatNumber($subtotal) . '</td>
                </tr>';

    $tableSubtotal .= implode('', $arrSubtotal);

    $tableSubtotal .= '<tr>
                            <td style="width:80px;font-weight:bold;">Total</td>
                            <td style="width:40px;text-align:right;"></td>
                            <td style="width:125px;text-align:right;border-left:1px solid black; border-right:1px solid black;border-top:1pz solid black; border-bottom:1px solid black;">' . $obj->formatNumber($grandTotal) . '</td>
                        </tr>
                    </table>';

    $html .= $tableSubtotal;


    $html .= '<table cellpadding="2">
        <tr>
            <td style="width:70px;">Catatan</td>
            <td style="width:10px;text-aling:center;">:</td>
            <td style="width:350px;">' . $rs[0]['trdesc'] . '</td>
        </tr>
        <tr><td></td></tr>
        <tr><td></td></tr>
        <tr><td></td></tr>
        <tr><td></td></tr>
        <tr><td></td></tr>
        <tr>
            <td style="width:550px;"><table><tr>
                    <td style="width:300px;">Pembayaran mohon ditransfer ke rekening : </td>
                </tr>
                <tr>
                    <td style="width:300px;">' . $rsPaymentMethod[0]['bankaccountname'] . '</td>
                </tr>
                <tr>
                    <td style="width:300px;">' . $rsPaymentMethod[0]['bankname'] . '</td>
                </tr>
                <tr>
                    <td style="width:300px;">A/C No. ' . $rsPaymentMethod[0]['bankaccountnumber'] . '</td>
                </tr>
                </table>
            </td>
            <td style="width:125px;"><table>
                    <tr><td></td></tr>
                    <tr><td></td></tr>
                    <tr><td></td></tr>
                    <tr><td><b>Suprihanto</b></td></tr>
                </table>
            </td>
        </tr>
    </table>';

    $html = '<div style="font-size:1.2em">'.$html.'</div>';
    return $html;

}

?>