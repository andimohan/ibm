<?php

$PRINT_SETTINGS = array(
    'showPrintHeader' => false,
);

includeClass(array('GoodsOut.class.php'));

$goodsOut = createObjAndAddToCol(new GoodsOut());
$obj = $goodsOut;

$generateReportContent = function ($dataset) {


    $obj = new GoodsOut();
    $warehouse = new Warehouse();
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
                            <td style="border:1px solid black"><b>PENGELUARAN</b></td>
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
                            <td style="width: 120px;">No. Pengeluaran</td>
                            <td style="width: 20px;">:</td>
                            <td style="width: 190px;"><b>' . $rs[0]['code'] . '</b></td>
                        </tr>
                        <tr style="font-weight:bold;">
                            <td>No. Pengajuan</td>
                            <td>:</td>
                            <td><b>' . $rs[0]['submissionnumber'] . '</b></td>
                        </tr>
                        <tr style="font-weight:bold;">
                            <td>No. Pendafatran</td>
                            <td>:</td>
                            <td><b>' . $rs[0]['registrationnumber'] . '</b></td>
                        </tr>
                        <tr style="font-weight:bold;">
                            <td>Customer</td>
                            <td>:</td>
                            <td><b>' . $rs[0]['customername'] . '</b></td>
                        </tr>
                    </table>
                </td>
                <td style="width:338px; vertical-align:top;">
                    <table cellpadding="4" style="width:100%;">
                        <tr style="font-weight:bold;">
                            <td style="width: 120px;">Tgl. Penerimaan</td>
                            <td style="width: 20px;">:</td>
                            <td style="width: 190px;text-align:right;"><b>' . $obj->formatDBDate($rs[0]['trdate'], 'd - m - y') . '</b></td>
                        </tr>
                        <tr style="font-weight:bold;">
                            <td>Tgl. Pengajuan</td>
                            <td>:</td>
                            <td style="text-align:right;"><b>' . $obj->formatDBDate($rs[0]['submissiondate'], 'd - m - y') . '</b></td>
                        </tr>
                        <tr style="font-weight:bold;">
                            <td>Tgl. Pendaftaran</td>
                            <td>:</td>
                            <td style="text-align:right;"><b>' . $obj->formatDBDate($rs[0]['registrationdate'], 'd - m - y') . '</b></td>
                        </tr>
                        <tr style="font-weight:bold;">
                            <td>Penerima</td>
                            <td>:</td>
                            <td style="text-align:right;"><b>' . $rs[0]['recipient'] . '</b></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>';

    $html .= '<table cellpadding="4" style="width: 676px; margin-top:6px;border-bottom: 1px solid black;">
                <tr>
                    <td style="border-left:1px solid black;border-bottom:1px solid black;border-top:1px solid black;width:250px;">NO. PICKING LIST</td>
                    <td style="border-left:1px solid black;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;width:426px;"></td>
                </tr>
                <tr>
                    <td style="border-left:1px solid black;border-right:1px solid black;border-bottom:1px solid black;width: 676px;">DETAIL BARANG</td>
                </tr>
                <tr>
                    <td style="border-left:1px solid black;width: 101px;border-top:1px solid black;border-bottom:1px solid black;"><span style="font-weight:bold;">No. Pengajuan</span></td>
                    <td style="width: 100px;border-top:1px solid black;border-bottom:1px solid black;"><span style="font-weight:bold;">No. Penerimaan</span></td>
                    <td style="width: 80px;border-top:1px solid black;border-bottom:1px solid black;"><span style="font-weight:bold;">Kode Barang</span></td>
                    <td style="width: 155px;border-top:1px solid black;border-bottom:1px solid black;"><span style="font-weight:bold;">Nama Barang</span></td>
                    <td style="width: 60px;border-top:1px solid black;border-bottom:1px solid black;text-align:left;"><span style="font-weight:bold;">Satuan</span></td>
                    <td style="width: 80px;border-top:1px solid black;border-bottom:1px solid black;"><span style="font-weight:bold;">Qty</span></td>
                    <td style="border-right:1px solid black;width: 100px;border-top:1px solid black;border-bottom:1px solid black;"><span style="font-weight:bold;">Value</span></td>
                </tr>';

    for ($i = 0; $i < count($rsDetail); $i++) {
        $submissionNumber = (!empty($rsDetail[$i]['documenttypename']) ? '[' . $rsDetail[$i]['documenttypename'] . ']' : '') . $rsDetail[$i]['submissionnumber'];
        $html .= '<tr>
                    <td style="border-left:1px solid black;width: 101px;border-bottom:1px solid black;">' . $submissionNumber . '</td>
                    <td style="width: 100px;border-bottom:1px solid black;">' . $rsDetail[$i]['receivingcode'] . '</td>
                    <td style="width: 80px;border-bottom:1px solid black;">' . $rsDetail[$i]['itemcode'] . '</td>
                    <td style="width: 155px;border-bottom:1px solid black;">' . $rsDetail[$i]['itemname'] . '</td>
                     <td style="width: 60px;border-bottom:1px solid black;">' . $rsDetail[$i]['unitname'] . '</td>
                    <td style="width: 80px;border-bottom:1px solid black;text-align:right;">' . $obj->formatNumber($rsDetail[$i]['qty'], 2) . '</td>
                    <td style="border-right:1px solid black;width: 100px;border-bottom:1px solid black;text-align:right;">' . $obj->formatNumber($rsDetail[$i]['amount'], 2) . '</td>
                </tr>';

    }


    $html .= '</table>';


    return $html;

}

    ?>