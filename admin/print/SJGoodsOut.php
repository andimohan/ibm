<?php
includeClass(array('GoodsOut.class.php'));

$PRINT_SETTINGS =  array(   
         'showPrintHeader' => false,
         );

$goodsOut = createObjAndAddToCol(new GoodsOut());
$obj = $goodsOut;

$generateReportContent = function ($dataset) {
    $obj = createObjAndAddToCol(new GoodsOut());

    $rs = $dataset['rs'];
    $rsDetail = $obj->getDetailWithRelatedInformation($rs[0]['pkey']);

    $companyName = strtoupper($obj->loadSetting('companyName'));
    $companyAddress = $obj->loadSetting('companyAddress');

    $html = '';

    $html .= '
        <table cellpadding="2" style="border-bottom: 1px solid black; width: 676px;">
            <tr>
                <td style="width: 340px; vertical-align:top;">
                    <table cellpadding="2">
                        <tr><td><b>' . htmlspecialchars($companyName, ENT_QUOTES) . '</b></td></tr>
                        <tr><td><b>' . htmlspecialchars($companyAddress, ENT_QUOTES) . '</b></td></tr>
                        <tr><td></td></tr>
                    </table>
                </td>
                <td style="width: 176px;"></td>
                <td style="width: 160px; vertical-align:top; text-align:center;">
                    <table cellpadding="4">
                        <tr><td></td></tr>
                        <tr>
                            <td style="border:1px solid black"><b>SURAT JALAN</b></td>
                        </tr>
                        <tr><td></td></tr>
                    </table>
                </td>
            </tr>
        </table>

        <table cellpadding="2" style="width: 676px; margin-top:6px;border-bottom: 1px solid black;">
            <tr>
                <td style="width:338px; vertical-align:top;">
                    <table cellpadding="2" style="width:100%;">
                        <tr style="font-weight:bold;">
                            <td style="width: 120px;">No. Pengeluaran</td>
                            <td style="width: 20px;">:</td>
                            <td style="width: 190px;"><b>' . htmlspecialchars($rs[0]['code'], ENT_QUOTES) . '</b></td>
                        </tr>
                        <tr style="font-weight:bold;">
                            <td>No. Pengajuan</td>
                            <td>:</td>
                            <td><b>' . htmlspecialchars($rs[0]['submissionnumber'], ENT_QUOTES) . '</b></td>
                        </tr>
                        <tr style="font-weight:bold;">
                            <td>No. Pendaftaran</td>
                            <td>:</td>
                            <td><b>' . htmlspecialchars($rs[0]['registrationnumber'], ENT_QUOTES) . '</b></td>
                        </tr>
                        <tr style="font-weight:bold;">
                            <td>Customer</td>
                            <td>:</td>
                            <td><b>' . htmlspecialchars($rs[0]['customername'], ENT_QUOTES) . '</b></td>
                        </tr>
                    </table>
                </td>
                <td style="width:338px; vertical-align:top;">
                    <table cellpadding="2" style="width:100%;">
                        <tr style="font-weight:bold;">
                            <td style="width: 120px;">Tgl. Pengeluaran</td>
                            <td style="width: 20px;">:</td>
                            <td style="width: 190px;text-align:right;"><b>' . htmlspecialchars(date('d - m - y', strtotime($rs[0]['trdate'])), ENT_QUOTES) . '</b></td>
                        </tr>
                        <tr style="font-weight:bold;">
                            <td>Tgl. Pengajuan</td>
                            <td>:</td>
                            <td style="text-align:right;"><b>' . htmlspecialchars(date('d - m - y', strtotime($rs[0]['submissiondate'])), ENT_QUOTES) . '</b></td>
                        </tr>
                        <tr style="font-weight:bold;">
                            <td>Tgl. Pendaftaran</td>
                            <td>:</td>
                            <td style="text-align:right;"><b>' . htmlspecialchars(date('d - m - y', strtotime($rs[0]['registrationdate'])), ENT_QUOTES) . '</b></td>
                        </tr>
                        <tr style="font-weight:bold;">
                            <td>Penerima</td>
                            <td>:</td>
                            <td style="text-align:right;"><b>' . htmlspecialchars($rs[0]['recipient'], ENT_QUOTES) . '</b></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>';

    $html .= '
        <table cellpadding="4" style="width: 676px; margin-top:6px;border-bottom: 1px solid black;">
            <tr>
                <td colspan="7" style="border-left:1px solid black;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;">DETAIL BARANG</td>
            </tr>
            <tr>
                <td style="border-left:1px solid black;width: 101px;border-top:1px solid black;border-bottom:1px solid black;"><span style="font-weight:bold;">No. Pengajuan</span></td>
                <td style="width: 100px;border-top:1px solid black;border-bottom:1px solid black;border-left:1px solid black;"><span style="font-weight:bold;">No. Penerimaan</span></td>
                <td style="width: 80px;border-top:1px solid black;border-bottom:1px solid black;border-left:1px solid black;"><span style="font-weight:bold;">Kode Barang</span></td>
                <td style="width: 155px;border-top:1px solid black;border-bottom:1px solid black;border-left:1px solid black;"><span style="font-weight:bold;">Nama Barang</span></td>
                <td style="width: 60px;border-top:1px solid black;border-bottom:1px solid black;border-left:1px solid black;text-align:left;"><span style="font-weight:bold;">Satuan</span></td>
                <td style="text-align:right; width: 80px;border-top:1px solid black;border-bottom:1px solid black;border-left:1px solid black;"><span style="font-weight:bold;">Qty</span></td>
                <td style="text-align:right; border-right:1px solid black;width: 100px;border-top:1px solid black;border-bottom:1px solid black;border-left:1px solid black;"><span style="font-weight:bold;">Value</span></td>
            </tr>';

    for ($i = 0; $i < count($rsDetail); $i++) {
        $submissionNumber = (!empty($rsDetail[$i]['documenttypename']) ? '[' . $rsDetail[$i]['documenttypename'] . '] ' : '') . $rsDetail[$i]['submissionnumber'];

        $html .= '
            <tr>
                <td style="border-left:1px solid black;width: 101px;border-bottom:1px solid black;">' . htmlspecialchars($submissionNumber, ENT_QUOTES) . '</td>
                <td style="width: 100px;border-bottom:1px solid black;border-left:1px solid black;">' . htmlspecialchars($rsDetail[$i]['receivingcode'], ENT_QUOTES) . '</td>
                <td style="width: 80px;border-bottom:1px solid black;border-left:1px solid black;">' . htmlspecialchars($rsDetail[$i]['itemdetailcode'], ENT_QUOTES) . '</td>
                <td style="width: 155px;border-bottom:1px solid black;border-left:1px solid black;">' . htmlspecialchars($rsDetail[$i]['itemname'], ENT_QUOTES) . '</td>
                <td style="width: 60px;border-bottom:1px solid black;border-left:1px solid black;">' . htmlspecialchars($rsDetail[$i]['unitname'], ENT_QUOTES) . '</td>
                <td style="width: 80px;border-bottom:1px solid black;border-left:1px solid black;text-align:right;">' . htmlspecialchars(number_format((float)$rsDetail[$i]['qty'], 2, '.', ','), ENT_QUOTES) . '</td>
                <td style="border-right:1px solid black;width: 100px;border-bottom:1px solid black;text-align:right;border-left:1px solid black;">' . htmlspecialchars(number_format((float)$rsDetail[$i]['amount'], 2, '.', ','), ENT_QUOTES) . '</td>
            </tr>';
    }

    $html .= '</table>';

    $html .= '
        <div style="height:60px;"></div>
        <table class="goodsout-signature-table" cellpadding="2" style="width:380px; margin-top:18px; border:1px solid black; border-collapse:collapse; page-break-inside:avoid;">
            <tr>
                <td style="border-right:1px solid black; border-bottom:1px solid black; text-align:center; font-weight:bold; padding:4px 4px; height:26px;">PETUGAS CHEKER</td>
                <td style="border-bottom:1px solid black; text-align:center; font-weight:bold; padding:4px 4px; height:26px;">DRIVER</td>
            </tr>
            <tr>
                <td style="border-right:1px solid black; text-align:center; padding:4px 4px; height:54px; vertical-align:bottom;"><span style="display:inline-block; min-width:92px; border-bottom:1px solid black; line-height:1;">&nbsp;</span></td>
                <td style="text-align:center; padding:4px 4px; height:54px; vertical-align:bottom;"><span style="display:inline-block; min-width:92px; border-bottom:1px solid black; line-height:1;">&nbsp;</span></td>
            </tr>
        </table>';

    return $html;
};

?>