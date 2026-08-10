<?php

$PRINT_SETTINGS = array(
    'showPrintHeader' => false,
);

includeClass(array('PutAway.class.php'));

$putAway = createObjAndAddToCol(new PutAway(3));
$obj = $putAway;

$generateReportContent = function ($dataset) {


    $obj = new PutAway(3);
    $setting = new Setting();

    $rs = $dataset['rs'];

    $rsDetail = $obj->getDetailWithRelatedInformation($rs[0]['pkey']);

    $companyName = strtoupper($setting->loadSetting('companyName'));
    $companyAddress = $setting->loadSetting('companyAddress');

    $html = $obj->printSetting['defaultStyle'];

    $html .= '
        <table cellpadding="2" style="border-bottom: 1px solid black; width: 676px;">
            <tr>
                <td style="width: 340px; vertical-align:top;">
                    <table cellpadding="2">
                        <tr><td><b>' . $companyName . '</b></td></tr>
                        <tr><td><b>' . $companyAddress . '</b></td></tr>
                        <tr><td></td></tr>
                    </table>
                </td>
                <td style="width: 176px;"></td>
                <td style="width: 160px; vertical-align:top; text-align:center;">
                    <table cellpadding="4" >
                        <tr><td></td></tr>
                        <tr>
                            <td style="border:1px solid black"><b>PICKING LIST</b></td>
                        </tr>
                            <tr><td></td></tr>
                    </table>
                </td>
            </tr>
        </table>';

    $html .= '<table cellpadding="4" style="width: 676px; margin-top:6px;">
            <tr>
                <td style="width:338px; vertical-align:top;">
                    <table cellpadding="4" style="width:100%;">
                        <tr style="font-weight:bold;">
                            <td style="width: 120px;">No. Picling List</td>
                            <td style="width: 20px;">:</td>
                            <td style="width: 190px;"><b>' . $rs[0]['code'] . '</b></td>
                        </tr>
                       
                    </table>
                </td>
                <td style="width:338px; vertical-align:top;">
                    <table cellpadding="4" style="width:100%;">
                        <tr style="font-weight:bold;">
                            <td style="width: 120px;">Tgl. Picking List</td>
                            <td style="width: 20px;">:</td>
                            <td style="width: 190px;text-align:right;"><b>' . $obj->formatDBDate($rs[0]['trdate'], 'd - m - y') . '</b></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>';

    $html .= '<table cellpadding="4" style="width: 676px; margin-top:6px;border-bottom: 1px solid black;">
               
                <tr>
                    <td style="border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;width: 676px;">DETAIL BARANG</td>
                </tr>
                <tr>
                    <td style="text-align:center;border-left:1px solid black;width: 41px;border-top:1px solid black;border-right:1px solid black;border-bottom:1px solid black;">NO.</td>
                    <td style="text-align:center;width: 110px;border-top:1px solid black;border-right:1px solid black;border-bottom:1px solid black;">DOKUMEN PENGAJUAN</td>
                    <td style="text-align:center;width: 85px;border-top:1px solid black;border-right:1px solid black;border-bottom:1px solid black;">NO. PENERIMAAN</td>
                    <td style="text-align:center;width: 60px;border-top:1px solid black;border-right:1px solid black;border-bottom:1px solid black;">NO. PETI KEMAS</td>
                    <td style="text-align:center;width: 60px;border-top:1px solid black;border-right:1px solid black;border-bottom:1px solid black;">LOKASI</td>
                    <td style="text-align:center;width: 60px;border-top:1px solid black;border-right:1px solid black;border-bottom:1px solid black;">PALLET</td>
                    <td style="text-align:center;width: 60px;border-top:1px solid black;border-right:1px solid black;border-bottom:1px solid black;">KODE BARANG</td>
                    <td style="text-align:center;width: 80px;border-top:1px solid black;border-right:1px solid black;border-bottom:1px solid black;">NAMA BARANG</td>
                    <td style="text-align:center;width: 60px;border-top:1px solid black;border-right:1px solid black;border-bottom:1px solid black;">QTY</td>
                    <td style="text-align:center;width: 60px;border-top:1px solid black;border-right:1px solid black;border-bottom:1px solid black;">SATUAN</td>
                </tr>';

    for ($i = 0; $i < count($rsDetail); $i++) {
        $documentNumber = $rs[0]['documenttypename'] . $rs[0]['submissionnumber'];
        $html .= '<tr>
                    <td style="text-align:center;border-left:1px solid black;width: 41px;border-right:1px solid black;border-bottom:1px solid black;">' . ($i + 1) . '</td>
                    <td style="text-align:center;width: 110px;border-right:1px solid black;border-bottom:1px solid black;">' . $documentNumber . '</td>
                    <td style="text-align:center;width: 85px;border-right:1px solid black;border-bottom:1px solid black;">' . $rs[0]['refcode'] . '</td>
                    <td style="text-align:center;width: 60px;border-right:1px solid black;border-bottom:1px solid black;">' . $rsDetail[$i]['itemreceivingcontainernumber'] . '</td>
                    <td style="text-align:center;width: 60px;border-right:1px solid black;border-bottom:1px solid black;"></td>
                    <td style="text-align:center;width: 60px;border-right:1px solid black;border-bottom:1px solid black;"></td>
                    <td style="text-align:center;width: 60px;border-right:1px solid black;border-bottom:1px solid black;">' . $rsDetail[$i]['itemcode'] . '</td>
                    <td style="text-align:center;width: 80px;border-right:1px solid black;border-bottom:1px solid black;">' . $rsDetail[$i]['itemname'] . '</td>
                    <td style="text-align:center;width: 60px;border-right:1px solid black;border-bottom:1px solid black;text-align:right;">' . $obj->formatNumber($rsDetail[$i]['qty'], 2) . '</td>
                    <td style="text-align:center;width: 60px;border-right:1px solid black;border-bottom:1px solid black;">' . $rsDetail[$i]['unitname'] . '</td>
                </tr>';

    }


    $html .= '</table>';


    return $html;

}

    ?>