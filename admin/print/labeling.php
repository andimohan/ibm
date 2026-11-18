<?php

$PRINT_SETTINGS = array(
    'showPrintHeader' => false,
);

includeClass(array('Labeling.class.php'));

$Labeling = createObjAndAddToCol(new Labeling());
$obj = $Labeling;

$generateReportContent = function ($dataset) {


    $obj = new Labeling();
    $setting = new Setting();

    $rs = $dataset['rs'];

    $rsDetail = $obj->getDetailWithRelatedInformation($rs[0]['pkey']);
    $obj->setLog('rsDetail'.print_r($rsDetail, true),true);
    $companyName = strtoupper($setting->loadSetting('companyName'));
    $companyAddress = $setting->loadSetting('companyAddress');
    $companyPhone = $setting->loadSetting('companyPhone');
    $companyFax = $setting->loadSetting('companyFax');

    $html = $obj->printSetting['defaultStyle'];

    $html .= '
        <table cellpadding="2" style="border-bottom: 1px solid black; width: 676px;">
            <tr>
                <td style="width: 340px; vertical-align:top;">
                    <table cellpadding="2">
                        <tr><td><b>' . $companyName . '</b></td></tr>
                        <tr><td><b>' . $companyAddress . '</b></td></tr>
                        <tr><td><b>No. Telepon : ' . $companyPhone . ' - No. Fax : ' . $companyFax . '</b></td></tr>
                        <tr><td><b></b></td></tr>
                        <tr><td></td></tr>
                    </table>
                </td>
                <td style="width: 176px;"></td>
                <td style="width: 160px; vertical-align:top; text-align:center;">
                    <table cellpadding="4" >
                        <tr><td></td></tr>
                        <tr>
                            <td style="border:1px solid black"><b>PROSES LABEL</b></td>
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
                            <td style="width: 120px;">No. Labeling</td>
                            <td style="width: 20px;">:</td>
                            <td style="width: 190px;"><b>' . $rs[0]['code'] . '</b></td>
                        </tr>
                       
                    </table>
                </td>
                <td style="width:338px; vertical-align:top;">
                    <table cellpadding="4" style="width:100%;">
                        <tr style="font-weight:bold;">
                            <td style="width: 120px;">Tgl. Labeling</td>
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
                    <td style="text-align:center;border-left:1px solid black;width: 30px;border-top:1px solid black;border-right:1px solid black;border-bottom:1px solid black;">NO.</td>
                    <td style="text-align:center;width: 75px;border-top:1px solid black;border-right:1px solid black;border-bottom:1px solid black;">NO. PENGAJUAN</td>
                    <td style="text-align:center;width: 75px;border-top:1px solid black;border-right:1px solid black;border-bottom:1px solid black;">NO. PENERIMAAN</td>
                    <td style="text-align:center;width: 70px;border-top:1px solid black;border-right:1px solid black;border-bottom:1px solid black;">NO. PETI KEMAS</td>
                    <td style="text-align:center;width: 70px;border-top:1px solid black;border-right:1px solid black;border-bottom:1px solid black;">KODE BARANG</td>
                    <td style="text-align:center;width: 255px;border-top:1px solid black;border-right:1px solid black;border-bottom:1px solid black;">NAMA BARANG</td>
                    <td style="text-align:center;width: 55px;border-top:1px solid black;border-right:1px solid black;border-bottom:1px solid black;">QTY</td>
                    <td style="text-align:center;width: 46px;border-top:1px solid black;border-right:1px solid black;border-bottom:1px solid black;">SATUAN</td>
                </tr>';

    for ($i = 0; $i < count($rsDetail); $i++) {
        $html .= '<tr>
                    <td style="text-align:center;border-left:1px solid black;width: 30px;border-right:1px solid black;border-bottom:1px solid black;">' . ($i + 1) . '</td>
                    <td style="text-align:center;width: 75px;border-right:1px solid black;border-bottom:1px solid black;">' . $rsDetail[$i]['submissionnumber'] . '</td>
                    <td style="text-align:center;width: 75px;border-right:1px solid black;border-bottom:1px solid black;">' . $rsDetail[$i]['receivingcode'] . '</td>
                    <td style="text-align:center;width: 70px;border-right:1px solid black;border-bottom:1px solid black;">' . $rsDetail[$i]['containernumber'] . '</td>
                    <td style="text-align:center;width: 70px;border-right:1px solid black;border-bottom:1px solid black;">' . $rsDetail[$i]['itemcode'] . '</td>
                    <td style="text-align:left;width: 255px;border-right:1px solid black;border-bottom:1px solid black;">' . $rsDetail[$i]['itemname'] . '</td>
                    <td style="text-align:right;width: 55px;border-right:1px solid black;border-bottom:1px solid black;">' . $obj->formatNumber($rsDetail[$i]['qty']) . '</td>
                    <td style="text-align:center;width: 46px;border-right:1px solid black;border-bottom:1px solid black;">' . $rsDetail[$i]['unitname'] . '</td>
                </tr>';

    }


    $html .= '</table>';


    return $html;

}

    ?>