<?php

$PRINT_SETTINGS = array(
    'showPrintHeader' => false,
    'footer' => '',
    'pdfMarginHeader' => 8,
    'marginFooter' => 0,
    'paperSetting' => 'A4,P',
);

includeClass(array('EMKLHouseBL.class.php', 'Vessel.class.php', 'Port.class.php', 'EMKLJobOrder.class.php', 'Customer.class.php','Employee.class.php'));
$emklHBL = new EMKLHouseBL();
$vessel = new Vessel();
$port = new Port();
$obj = $emklHBL;
$emklJoborder = new EMKLJobOrder();
$customer = new Customer();
$employee = new Employee();

$generateReportContent = function ($dataset) {

    $obj = new EMKLHouseBL();
    $emklJoborder = new EMKLJobOrder();
    $port = new Port();
    $vessel = new Vessel();
    $customer = new Customer();
    $employee = new Employee();

    $rs = $dataset['rs'];

    $html = $obj->printSetting['defaultStyle'];

    $companyName = $obj->loadSetting('companyName');
    $companyAddress = $obj->loadSetting('companyAddress');

    $rsJobOrder = $emklJoborder->getDataRowById($rs[0]['refheaderkey']);
    $mblNumber = $rsJobOrder[0]['mblnumber'];

    $rsEmployee = $employee->getDataRowById(base64_decode($_SESSION[$obj->loginAdminSession]['id']));
    $from = $rsEmployee[0]['name'];

    $arrShipper = array();
    if (!empty($rs[0]['shippername'])) {
        array_push($arrShipper, strtoupper(htmlspecialchars_decode($rs[0]['shippername'])));
    }
    if (!empty($rs[0]['shipperaddress'])) {
        array_push($arrShipper, str_replace(chr(13), '<br>', strtoupper(htmlspecialchars_decode($rs[0]['shipperaddress']))));
    }

    $shipper = (!empty($arrShipper) ? implode('<br>', $arrShipper) : '');
    $consignee = strtoupper(htmlspecialchars_decode($rs[0]['consigneename'])) . '<br>' . str_replace(chr(13), '<br>', strtoupper($rs[0]['consigneeaddress']));
    $notifyParty = strtoupper(htmlspecialchars_decode($rs[0]['carriername'])) . '<br>' . str_replace(chr(13), '<br>', strtoupper($rs[0]['carrieraddress']));

    $alsoNotifyParty = $rs[0]['alsonotifyparty'];


    $rsPOD = $port->getDataRowById($rs[0]['podkey']);
    $podName = '';
    if (!empty($rsPOD)) {
        $podName = $rsPOD[0]['name'];
    }
    $rsVessel = $vessel->getDataRowById($rs[0]['vesselkey']);
    $motherVessel = $rsVessel[0]['name'] . ' ' . $rs[0]['vesselnumber'];

    $rsFeederVessel = $vessel->getDataRowById($rs[0]['feederkey']);
    $feederVesselName = $rsFeederVessel[0]['name'] . ' ' . $rs[0]['feedernumber'];

    $rsCustomer = $customer->getDataRowById($rs[0]['agentkey']);
    $agentName = $rsCustomer[0]['name'];

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
            <tr><td><div class="title" style="text-decoration:underline">CARGO RELEASED</div></td></tr>
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
                    <td style="width:120px">' . $obj->formatDBDate($rs[0]['telexdate'], 'd-M-Y') . '</td></tr>
            </table> ';

    $html .= '<hr>';
    $html .= '<div style="clear:both"></div>';


    $html .= ' 
        <table cellpadding="2"> 
            <tr>
                <td style="width:120px">Shipper</td>
                <td style="width:10px">:</td>
                <td style="width:300px">' . $shipper . '</td>
            </tr>
        </table> ';

    $html .= '<div style="clear:both"></div>';
    $html .= '<div style="clear:both"></div>';

    $html .= ' 
        <table cellpadding="2"> 
            <tr>
                <td style="width:120px">Consignee</td>
                <td style="width:10px">:</td>
                <td style="width:300px">' . $consignee . '</td>
            </tr>
        </table> ';

    $html .= '<div style="clear:both"></div>';
    $html .= '<div style="clear:both"></div>';

    $html .= ' 
        <table cellpadding="2"> 
            <tr>
                <td style="width:338px"><table>
                        <tr><td></td></tr>
                        <tr>
                            <td style="width:120px">Notify Party</td>
                            <td style="width:10px">:</td>
                            <td style="width:300px">' . $notifyParty . '</td>
                        </tr>
                    </table>
                </td>
                <td style="width:338px"><table>
                        <tr>
                            <td style="width:120px">Also Notify Party</td>
                            <td style="width:10px">:</td>
                            <td style="width:200px">' . $alsoNotifyParty . '</td>
                        </tr>
                    </table></td>
            </tr>
        </table> ';

    $html .= '<div style="clear:both"></div>';
    $html .= '<div style="clear:both"></div>';

    $html .= ' 
        <table cellpadding="2"> 
            <tr>
                <td style="width:120px">Destination</td>
                <td style="width:10px">:</td>
                <td style="width:300px">' . $podName . '</td>
            </tr>
            <tr>
                <td style="width:120px">B/L No.</td>
                <td style="width:10px">:</td>
                <td style="width:300px">' . $rs[0]['code'] . '</td>
            </tr>
            <tr>
                <td style="width:120px">M BL</td>
                <td style="width:10px">:</td>
                <td style="width:300px">' . $mblNumber . '</td>
            </tr>
            <tr>
                <td style="width:120px">Vessel Voy.</td>
                <td style="width:10px">:</td>
                <td style="width:300px">' . $feederVesselName . '</td>
            </tr>
        </table> ';

    $html .= '<div style="clear:both"></div>';

    $html .= ' 
        <table cellpadding="2"> 
            <tr>
                <td style="width:676px"><p>THE ABOVE MENTIONED SHIPPER HAS SURRENDERED FULL SET OFF ORIGINAL BILL OFF LANDING TO US KINDLY RELEASED CARGO TO CONSIGNEE WITHOUT PRESENTING ORIGINAL BILL OF LANDING <br>THANKS AND BEST REGARDS</p></td>
            </tr
        </table> ';



    return $html;
};

?>