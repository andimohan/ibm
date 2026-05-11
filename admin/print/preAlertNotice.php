<?php

$PRINT_SETTINGS = array(
    'showPrintHeader' => false,
    'footer' => '',
    'pdfMarginHeader' => 8,
    'marginFooter' => 0,
    'paperSetting' => 'A4,P',
);

includeClass(array('EMKLHouseBL.class.php', 'Vessel.class.php', 'Port.class.php', 'EMKLJobOrder.class.php', 'Customer.class.php', 'Employee.class.php', 'City.class.php'));
$emklHBL = new EMKLHouseBL();
$vessel = new Vessel();
$port = new Port();
$obj = $emklHBL;
$emklJoborder = new EMKLJobOrder();
$customer = new Customer();
$employee = new Employee();
$city = new City();

$generateReportContent = function ($dataset) {

    $obj = new EMKLHouseBL();
    $emklJoborder = new EMKLJobOrder();
    $port = new Port();
    $vessel = new Vessel();
    $customer = new Customer();
    $employee = new Employee();
    $city = new City();

    $rs = $dataset['rs'];

    $rsEmployee = $employee->getDataRowById(base64_decode($_SESSION[$obj->loginAdminSession]['id']));
    $from = $rsEmployee[0]['name'];

    $html = $obj->printSetting['defaultStyle'];

    $companyName = $obj->loadSetting('companyName');
    $companyAddress = $obj->loadSetting('companyAddress');

    $rsJobOrder = $emklJoborder->getDataRowById($rs[0]['refheaderkey']);

    $mblNumber = $rsJobOrder[0]['mblnumber'];

    $polName = '';
    $podName = '';

    if ($rs[0]['isoverwritepol'] == 1) {
        $polName = $rs[0]['portofloading'];
        $podName  = $rs[0]['portofdischarge'];
    } else {
        $rsPOL = $port->getDataRowById($rs[0]['polkey']);
        if (!empty($rsPOL)) {
            $polName = $rsPOL[0]['name'];
        }

        $rsPOD = $port->getDataRowById($rs[0]['podkey']);
        if (!empty($rsPOD)) {
            $podName = $rsPOD[0]['name'];
        }
    }

    $finalDestination = '';
    if($rs[0]['isoverwritefinaldestination'] == 1) {
        $finalDestination = $rs[0]['finaldestination'];
    } else {
    $rsCity = $city->getDataRowById($rs[0]['finaldestinationkey']);
        if (!empty($rsCity)) {
            $finalDestination = $rsCity[0]['name'];
        }
    }

    $placeOfDelivery = '';
    if($rs[0]['isoverwritepod'] == 1) {
        $placeOfDelivery = $rs[0]['placeofdelivery'];
    } else {
        $rsPODelivery = $port->getDataRowById($rs[0]['podeliverykey']);
        if (!empty($rsPODelivery)) {
            $placeOfDelivery = $rsPODelivery[0]['name'];
        }
    }

    $rsFeeder = $vessel->getDataRowById($rs[0]['feederkey']);
    $feederVessel = $rsFeeder[0]['name'] . ' ' . $rs[0]['feedernumber'];

    $rsVessel = $vessel->getDataRowById($rs[0]['vesselkey']);
    $motherVessel = $rsVessel[0]['name'] . ' ' . $rs[0]['vesselnumber'];

    $ETD = $obj->formatDBDate($rs[0]['etdpol'], 'M, d-Y', array('returnOnEmpty' => true));
    $ETA = $obj->formatDBDate($rs[0]['etapod'], 'M, d-Y', array('returnOnEmpty' => true));

    $shipperName = $rs[0]['shippername'];
    $consigneeName = $rs[0]['consigneename'];
    $notifyParty = $rs[0]['carriername'];

    $rsHBLContainer = $obj->getDetailHBLContainer($rs[0]['pkey']);

    $containers = '<table><tr><td style="width:75px">CONTAINER</td><td style="width:10px"> / </td><td style="width:34px">SIZE</td><td style="width:10px"> / </td><td style="width:60px">SEAL NO </td><td width:10px> / </td><td style="width:30px"></td></tr><tr><td style="width:224px;border-top:1px solid #000"></td></tr></table>';
    foreach ($rsHBLContainer as $container) {
        $containers .= '<span>' . $container['containerno'] . '  /  ' . $container['containername'] . '  /  ' . $container['sealno'] . ' <br></span>';
    }

    $agentName = '';
    if($rs[0]['isoverwriteagent']) {
        $agentName = $rs[0]['agentname'];
    } else {
        $rsCustomer = $customer->getDataRowById($rs[0]['agentkey']);
        $agentName = $rsCustomer[0]['name'];
    }
    //START HEADER
    $html .= '<table cellpadding="2">
                    <tr>
                        <td style="width:500px">
                            <table>
                                <tr><td><span style="font-size:15px;font-weight:bold;">' . strtoupper($companyName) . '</span></td></tr>
                                <tr><td><span>' . nl2br(htmlspecialchars_decode($companyAddress)) . '</span></td></tr>
                            </table>
                        </td>
                        <td style="width:176px">
                            <table>
                                <tr><td style="width:40px;">Telp.</td><td style="width:10px">:</td><td style="width:80px;"> 61-21 6250901</td></tr>
                                <tr><td style="width:40px;">Fax</td><td style="width:10px">:</td><td style="width:80px;"> 62-21 6252919</td></tr>
                                <tr><td style="width:40px;">E-mail</td><td style="width:10px">:</td><td style="width:80px;"> </td></tr>
                                <tr><td style="width:40px;">Date</td><td style="width:10px">:</td><td style="width:80px;"> ' . $obj->formatDBDate($rs[0]['trdate'], 'd-M-Y') . '</td></tr>
                            </table>
                        </td>
                    </tr>
                </table>
                <hr>';

    $html .= ' 
        <table cellpadding="2" > 
            <tr><td></td></tr>
            <tr><td><div class="title" style="text-decoration:underline">PRE-ALERT-NOTICE</div></td></tr>
            <tr><td></td></tr>
        </table> ';


    $html .= '<table cellpadding="2"> 
                <tr>
                    <td style="width:40px">TO</td>
                    <td style="width:10px">:</td>
                    <td style="width:400px">' . $agentName . '</td></tr>
                <tr>
                    <td style="width:40px">ATTN</td>
                    <td style="width:10px">:</td>
                    <td style="width:400px"></td></tr>
                <tr>
                    <td style="width:40px">FROM</td>
                    <td style="width:10px">:</td>
                    <td style="width:300px">'. $from .'</td></tr>
                <tr>
                    <td style="width:40px">DATE</td>
                    <td style="width:10px">:</td>
                    <td style="width:100px">' . $obj->formatDBDate($rs[0]['trdate'], 'd-M-Y') . '</td></tr>
            </table> ';

    $html .= '<div style="clear:both"></div>';

    $html .= ' 
        <table cellpadding="2"> 
            <tr>
                <td><p>Enclose total - 3- pages this cover, Please contact us at the above fax</p></td>
            </tr>
            <tr>
                <td><p>If you do not receive  total pages stated</p></td>
            </tr>
            <tr>
                <td><p>You are advice that shipment of appended below is enroute to your port.</p></td>
            </tr>
            <tr>
                <td><p>Please arrange accordingly</p></td>
            </tr>
        </table> ';


    $html .= '<div style="clear:both"></div>';

    $html .= '<table cellpadding="2" style="width:676px">
            <tr>
                <td style="width:120px">H BL</td>
                <td style="width:10px">:</td>
                <td style="width:300px">' . $rs[0]['code'] . '</td>
            </tr>
            <tr>
                <td style="width:120px">M BL</td>
                <td style="width:10px">:</td>
                <td style="width:300px">' . $mblNumber . '</td>
            </tr>
            <tr>
                <td style="width:120px">POL</td>
                <td style="width:10px">:</td>
                <td style="width:300px">' . $polName . '</td>
            </tr>
            <tr>
                <td style="width:120px">POD</td>
                <td style="width:10px">:</td>
                <td style="width:300px">' . $podName . '</td>
            </tr>
            <tr>
                <td style="width:120px">FINAL DESTINATION</td>
                <td style="width:10px">:</td>
                <td style="width:300px">' . $finalDestination . '</td>
            </tr>
            <tr>
                <td style="width:120px">DESTINATION</td>
                <td style="width:10px">:</td>
                <td style="width:300px">' . $placeOfDelivery . '</td>
            </tr>
            <tr>
                <td style="width:120px">FEEDER VSL</td>
                <td style="width:10px">:</td>
                <td style="width:300px">' . $feederVessel . '</td>
            </tr>
            <tr>
                <td style="width:120px">ETD / ETA</td>
                <td style="width:10px">:</td>
                <td style="width:300px">' . $ETD . ' / ' . $ETA . '</td>
            </tr>
            <tr>
                <td style="width:120px">MOTHER VSL</td>
                <td style="width:10px">:</td>
                <td style="width:300px">' . $motherVessel . '</td>
            </tr>
            <tr>
                <td style="width:120px">ETA</td>
                <td style="width:10px">:</td>
                <td style="width:300px">' . $ETA . '</td>
            </tr>
            <tr>
                <td style="width:120px">SHIPPER</td>
                <td style="width:10px">:</td>
                <td style="width:300px">' . $shipperName . '</td>
            </tr>
            <tr>
                <td style="width:120px">CONSIGNEE</td>
                <td style="width:10px">:</td>
                <td style="width:300px">' . $consigneeName . '</td>
            </tr>
            <tr>
                <td style="width:120px">RLSD DOC TO CNEE</td>
                <td style="width:10px">:</td>
                <td style="width:300px"></td>
            </tr>
            <tr>
                <td style="width:120px">NOTIFY PARTY</td>
                <td style="width:10px">:</td>
                <td style="width:300px">' . $notifyParty . '</td>
            </tr>
            <tr>
                <td style="width:120px">D/N</td>
                <td style="width:10px">:</td>
                <td style="width:300px"></td>
            </tr>
            <tr>
                <td style="width:120px">CONTAINER</td>
                <td style="width:10px">:</td>
                <td style="width:300px">' . $containers . '</td>
            </tr>
    </table>';

    $html .= '<table cellpadding="2">
        <tr><td>Note :</td></tr>
        <tr><td><p>Please acknowledge copy & fax it back to us to ensure you received all the page</p></td></tr>
        <tr><td><p>Thank You</p></td></tr>
    </table>';

    return $html;
};

?>