<?php
$PRINT_SETTINGS = array(
    'showPrintHeader' => false,
    'showPrintFooter' => false,
);

includeClass(array('EMKLJobOrder.class.php'));
$emklJobOrder = createObjAndAddToCol(new EMKLJobOrder());

$obj = $emklJobOrder;

function getMonth($month)
{
    switch ($month) {
        case 'January':
            $months = 'Januari';
            break;
        case 'February':
            $months = 'Febuari';
            break;
        case 'March':
            $months = 'Maret';
            break;
        case 'April':
            $months = 'April';
            break;
        case 'May':
            $months = 'Mei';
            break;
        case 'June':
            $months = 'Juni';
            break;
        case 'July':
            $months = 'Juli';
            break;
        case 'August':
            $months = 'Agustus';
            break;
        case 'September':
            $months = 'September';
            break;
        case 'October':
            $months = 'Oktober';
            break;
        case 'November':
            $months = 'November';
            break;
        case 'December':
            $months = 'Desember';
            break;
        default:
            $months = '';
            break;
    }
    return $months;
};



function generateReportContent($dataset, $param) {
    $obj = new EMKLJobOrder();
    $itemUnit = new ItemUnit();

    $profileImg = $obj->loadSetting('companyLogo');
    $logo = (isset($_GET['logo']) && $_GET['logo'] == 0) ? '' : '<img src="' . PHPTHUMB_URL_PATH . 'setting/companyLogo/' . $profileImg . '" style="width:100px;height:100px">';

    $rs = $dataset['rs'];
    $container = $param['container'];

    $day = $obj->formatDBDate($rs[0]['trdate'], 'd');
    $months = $obj->formatDBDate($rs[0]['trdate'], 'F');
    $months = getMonth($months);
    $year = $obj->formatDBDate($rs[0]['trdate'], 'Y');

    $date = $day . ' ' . strtoupper($months) . ' ' . $year;

    $itemDescription = empty($rs[0]['itemdescription']) ? '' : nl2br(htmlspecialchars($rs[0]['itemdescription']));

    $rsDetail = $obj->getDetailWithRelatedInformation($rs[0]['pkey']);

    $customerName = htmlspecialchars_decode($rs[0]['customername']);
    $customerAddress = htmlspecialchars_decode($rs[0]['customeraddress']);

    if ($rs[0]['jobtypekey'] == EMKL['jobType']['import']) {
        $shipperName = htmlspecialchars_decode($rs[0]['consigneename']);
    } else {
        $shipperName = htmlspecialchars_decode($rs[0]['shippername']);
    }
    
    $indexNumber = $param['indexNumber'];
    $numberAJU = substr($rs[0]['aju'], -6);
    $workOrderNumber = '&nbsp;' . $numberAJU .'-'.$indexNumber;

    //$itemDescription = nl2br(htmlspecialchars_decode($rs[0]['itemdescription']));

    $rsItemUnit = $itemUnit->getDataRowById($rsDetail[0]['unitkey']);
    $unitName = $rsItemUnit[0]['name'];

    $weight = $obj->formatNumber($rsDetail[0]['weight'],1);
    $measurement = $obj->formatNumber($rsDetail[0]['measurement'],2);

    $rsVolume = $obj->getDetailVolume($rs[0]['pkey']);

    $arrVolume = array();
    for($i=0; $i<count($rsVolume); $i++) {
        array_push($arrVolume, $obj->formatNumber($rsVolume[$i]['qty']) . ' x ' . $obj->formatNumber($rsVolume[$i]['volume']) . ' FEET');
    }

    $volume = implode(',<br>',$arrVolume); 

    $html = $obj->printSetting['defaultStyle'];

    $html .= '
        <table cellpadding="4" style="border-bottom:2.5px solid black;">
            <tr>
                <td style="width:235px">
                    <table cellpadding="2">
                        <tr><td style="font-weight:bold;">' . $obj->loadSetting('companyName') . '</td></tr>
                        <tr><td>' . $obj->loadSetting('companyAddress') . '</td></tr>
                    </table>
                </td>
                <td style="width:200px">
                    <table cellpadding="2">
                        <tr><td style="font-weight:bold;text-align:center">' . $logo . '</td></tr>
                    </table>
                </td>
                <td style="width:235px">
                    <table cellpadding="2">
                        <tr><td style="font-weight:bold;text-align:center"></td></tr>
                        <tr><td style="font-weight:bold;text-align:center"></td></tr>
                        <tr><td style="font-weight:bold;text-align:center;font-size:20px">SURAT JALAN</td></tr>
                        <tr><td style="font-weight:bold;text-align:center"></td></tr>
                        <tr><td style="font-weight:bold;text-align:center"></td></tr>
                    </table>
                </td>
            </tr>
        </table>
    ';

    $html .= '<table cellpadding="4">
        <tr>
            <td style="width:300px">
                <table cellpadding="2">
                    <tr><td></td></tr>
                    <tr>
                        <td>Kepada Yth.,</td>
                    </tr>
                    <tr>
                        <td style="font-weight:bold;">' . $customerName . '</td>
                    </tr>
                    <tr>
                        <td style="font-weight:bold;">' . $customerAddress . '</td>
                    </tr>
                </table>    
            </td>
            <td style="width:150px">

            </td>
            <td style="width:220px">
                <table cellpadding="2">
                    <tr>
                        <td>NO. ' . $workOrderNumber . '</td>
                    </tr>
                    <tr>
                        <td>Tanggal : ' . $date . '</td>
                    </tr>
                </table> 
            </td>
        </tr>
    </table>';

    $html .= '<div style="clear:both"></div><div style="clear:both"></div>';

    $html .= '
        <table cellpadding="4">
            <thead>
                <tr>
                    <td style="width:360px; text-align:center; border-left: 1px solid black; border-top: 1px solid black; border-bottom: 1px solid black;">DESKRIPSI BARANG</td>
                    <td style="width:140px; text-align:center; border-left: 1px solid black; border-top: 1px solid black; border-bottom: 1px solid black;">JUMLAH BARANG</td>
                    <td style="width:170px; text-align:center; border-left: 1px solid black; border-top: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black;">KETERANGAN</td>
                </tr>
            </thead>
            <tbody>

            <tr>
                <td style="width:360px; border-left: 1px solid black;"><table>
                    <tr>
                        <td style="font-weight:bold;">SHIPPER :</td>
                    </tr>
                    <tr>
                        <td style="font-weight:bold;">' . $shipperName . '</td>
                    </tr>
                </table></td>
                <td style="width:140px; border-left: 1px solid black;"></td>
                <td style="width:170px; border-left: 1px solid black; border-right: 1px solid black;"></td>
            </tr>

            <tr>
                <td style="width:360px; border-left: 1px solid black;">
                </td>
                <td style="width:140px; border-left: 1px solid black;"></td>
                <td style="width:170px; border-left: 1px solid black; border-right: 1px solid black;"></td>
            </tr>


            <tr>
                <td style="width:360px; border-left: 1px solid black;"><table>
                    <tr>
                        <td style="font-weight:bold;">' . $itemDescription . '</td>
                    </tr>
                </table></td>
                <td style="width:140px; border-left: 1px solid black;"></td>
                <td style="width:170px; border-left: 1px solid black; border-right: 1px solid black;"></td>
            </tr>

            <tr>
                <td style="width:360px; border-left: 1px solid black;font-weight:bold;">AS PER INVOICE : '. $rs[0]['ponumber'] .'</td>
                <td style="width:140px; border-left: 1px solid black;"></td>
                <td style="width:170px; border-left: 1px solid black; border-right: 1px solid black;"></td>
            </tr>
            
            <tr>
                <td style="width:360px; border-left: 1px solid black;"><table>
                    <tr>
                        <td style="font-size:14px;font-weight:bold;width:25px">BL</td>
                        <td style="font-size:14px;font-weight:bold;width:10px;text-align:center">:</td>
                        <td style="font-size:14px;font-weight:bold;width:200px">'. $rs[0]['mblnumber'] .'</td>
                    </tr>
                </table></td>
                <td style="width:140px; border-left: 1px solid black;"></td>
                <td style="width:170px; border-left: 1px solid black; border-right: 1px solid black;"></td>
            </tr>

            <tr>
                <td style="width:360px; border-left: 1px solid black;"><table>
                    <tr>
                        <td style="font-style:italic;">Nomor Pengajuan :</td>
                    </tr>
                    <tr>
                        <td style="font-style:italic;font-weight:bold;">' . $rs[0]['aju'] . '</td>
                    </tr>
                </table></td>
                <td style="width:140px; border-left: 1px solid black;"><table>
                    <tr>
                        <td style="font-weight:bold;text-align:center;">'. $obj->formatNumber($rsDetail[0]['qty']) .'</td>
                    </tr>
                    <tr>
                        <td style="font-weight:bold;text-align:center;">'. $unitName .'</td>
                    </tr>
                </table></td>
                <td style="width:170px; border-left: 1px solid black; border-right: 1px solid black;"><table>
                    <tr>
                        <td style="text-align:center;">SESUAI B/L</td>
                    </tr>
                    <tr>
                        <td style="font-weight:bold;"> </td>
                    </tr>
                </table></td>
            </tr>
            <tr>
                <td style="width:360px; border-left: 1px solid black;"><table>
                    <tr>
                        <td style="font-style:italic;">Nomor Pendaftaran PIB :</td>
                    </tr>
                    <tr>
                        <td style="font-style:italic;font-weight:bold;">' . $rs[0]['pibregistrationnumber'] . ' Tanggal '. $obj->formatDBDate($rs[0]['pibregistrationdate'], 'd-m-Y')  .'</td>
                    </tr>
                </table></td>
                <td style="width:140px; border-left: 1px solid black;"></td>
                <td style="width:170px; border-left: 1px solid black; border-right: 1px solid black;"><table>
                    <tr>
                        <td style="font-weight:bold;">No. Container :</td>
                    </tr>
                    <tr>
                        <td style="font-weight:bold;">'. $container['containerno'] .'</td>
                    </tr>
                    <tr>
                        <td style="font-weight:bold;">SEAL : '. $container['sealno'] .'</td>
                    </tr>
                </table></td>
            </tr>

            <tr>
                <td style="width:360px; border-left: 1px solid black;"></td>
                <td style="width:140px; border-left: 1px solid black;"></td>
                <td style="width:170px; border-left: 1px solid black; border-right: 1px solid black;"></td>
            </tr>

            <tr>
                <td style="width:360px; border-left: 1px solid black;"><table>
                    <tr>
                        <td style="font-weight:bold">DELIVERY :</td>
                    </tr>
                    <tr>
                        <td style="font-weight:bold;">'. nl2br($container['deliveryaddress']) .'</td>
                    </tr>
                </table></td>
                <td style="width:140px; border-left: 1px solid black;"></td>
                <td style="width:170px; border-left: 1px solid black; border-right: 1px solid black;"><table>
                    <tr>
                        <td style="font-weight:bold;">'. $volume .'</td>
                    </tr>
                </table></td>
            </tr>

            <tr>
                <td style="width:360px; border-left: 1px solid black; border-bottom:1px solid black;"></td>
                <td style="width:140px; border-left: 1px solid black; border-bottom:1px solid black;"></td>
                <td style="width:170px; border-left: 1px solid black; border-bottom:1px solid black; border-right: 1px solid black;"></td>
            </tr>

            <tr>
                <td style="width:360px; border-left: 1px solid black;">PERHATIAN:</td>
                <td style="width:140px; border-left: 1px solid black;">TOTAL BERAT</td>
                <td style="width:170px;border-right: 1px solid black;">'. $weight .' KGS // '. $measurement .' CBM</td>
            </tr> 
            <tr>
                <td style="width:360px; border-left: 1px solid black;font-size:10px">1. Surat jalan ini merupakan bukti resmi penerimaan barang</td>
                <td style="width:140px; border-left: 1px solid black;">Catatan</td>
                <td style="width:170px;border-right: 1px solid black;">'. $rs[0]['trdesc'] .'</td>
            </tr> 
            <tr>
                <td style="width:360px; border-left: 1px solid black;font-size:10px">2. Surat Jalan ini bukan bukti penjualan</td>
                <td style="width:140px; border-left: 1px solid black;">CODE TR</td>
                <td style="width:170px; border-right: 1px solid black;"></td>
            </tr> 
            <tr>
                <td style="width:360px; border-left: 1px solid black;font-size:10px">3. Surat Jalan ini akan dilengkapi dengan Invoice sebagai bukti  penjualan</td>
                <td style="width:140px; border-left: 1px solid black;"></td>
                <td style="width:170px; border-right: 1px solid black;"></td>
            </tr>
            
            <tr>
                <td style="width:360px; border-left: 1px solid black; border-bottom:1px solid black;"></td>
                <td style="width:140px; border-left: 1px solid black; border-bottom:1px solid black;"></td>
                <td style="width:170px; border-bottom:1px solid black; border-right: 1px solid black;"></td>
            </tr>
        

            </tbody>
        </table>
    ';

    $html .='<table>
                <tr><td style="width:360px;"></td></tr>
                <tr>
                    <td style="width:360px;">BARANG SUDAH DITERIMA DALAM KEADAAN DAN CUKUP oleh :</td>
                </tr>
                <tr>
                    <td style="width:360px;">(tanda tangan dan cap (stempel) perusahaan)</td>
                </tr>
                 <tr><td style="width:360px;"></td></tr>
            </table>';

    $html .= '<div style="clear:both"></div>';
    
    $html .= '<table><tr>
        <td style="width:335px">
            <table><tr>
                <td style="text-align:center;">Penerima Barang</td>
            </tr></table>
        </td>
        <td style="width:335px">
            <table><tr>
                <td style="text-align:center;">Pengirim Barang</td>
            </tr></table>
        </td>
    </tr></table>';

    return $html;

};

$content = function($dataset) {

    $obj = new EMKLJobOrder();

    $rs = $dataset['rs'];

    $rsContainerDetail = $obj->getDetailContainer($rs[0]['pkey']);

    $returnHTML = array();
    foreach($rsContainerDetail as $key => $container) {

        $indexNumber = ($key + 1);
        $html = generateReportContent($dataset, array('container' => $container, 'indexNumber' => $indexNumber));

        array_push($returnHTML, $html);
    }   

    return $returnHTML;

};

$generateReportContent = array();
array_push($generateReportContent, array('content' => $content));

?>