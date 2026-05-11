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
$customer = new Customer();
$employee = new Employee();
$obj = $emklHBL;

$generateReportContent = function ($dataset) {

    $obj = new EMKLHouseBL();
    $emklJobOrder = new EMKLJobOrder();
    $vessel = new Vessel();
    $port = new Port();
    $customer = new Customer();
    $employee = new Employee();

    $rs = $dataset['rs'];

    $html = $obj->printSetting['defaultStyle'];

    $html .= '
                <style>
                    .full-border{ border-bottom:1px solid #333; border-right:1px solid #333; border-left:1px solid #333; } 
                    .border-bottom-right{ border-bottom:1px solid #333; border-right:1px solid #333; } 
                    .border-right{ border-right:1px solid #333;} 
                    .border-bottom{ border-bottom:1px solid #333;} 
                    .border-top{ border-top:1px solid #333;} 
                    .border-bottom-left{ border-bottom:1px solid #333; border-left:1px solid #333;} 
                    .border-left{  border-left:1px solid #333;} 
                    .head-title{ font-weight:bold; }
                    
                    .border{ border :1px solid color:#2E2E84; }
                    .border-top-bottom-right{
                        border-top :1px solid color:#2E2E84;
                        border-right:1px solid color:#2E2E84;
                        border-bottom:1px solid color:#2E2E84;} 
                    .border-right{ border-right:1px solid color:#2E2E84} 
                    .border-bottom{ border-bottom:1px solid color:#2E2E84} 
                    .head-title{ font-weight:bold; }
                    
                    .border-bottom-right-dotted{
                        border-bottom :1px solid color:#2E2E84;    
                        border-right:1px dotted color:#2E2E84;} 
                    .border-top-right{
                        border-top :1px solid color:#2E2E84;    
                        border-right:1px solid color:#2E2E84;} 

                        .border-bottom-right{
                            border-bottom :1px solid 2E2E84;    
                            border-right:1px solid 2E2E84;} 
                    
                            .border-top-bottom{
                                border-bottom :1px solid 2E2E84;    
                                border-top:1px solid 2E2E84;} 
                    .border-top{border-top :1px solid 2E2E84;} 
                    .border-top-left{
                        border-top :1px solid 2E2E84;
                        border-left :1px solid 2E2E84;} 
                    .border-left{ 
                        border-left :1px solid 2E2E84;} 
                    
                    .border-right{ border-right:1px solid 2E2E84} 
                    .border-bottom{ border-bottom:1px solid 2E2E84} 
                    .head-title{ font-weight:bold; }

                    .text-top{vertical-align: top;}
                    .middle{vertical-align: middle;}
                    .bottom{vertical-align: bottom;}
                    .initial{vertical-align: initial;}
                    
                    .capital {text-transform: uppercase;}
                    .border-right-dotted {
                        border-right:1px dashed 2E2E84;
                    }
                    
                    .bg-grey {
                        background-color:#D3D3D3
                    }
                </style>';

    $rsJobOrder = $emklJobOrder->searchData($emklJobOrder->tableName . '.pkey', $rs[0]['refheaderkey']);

    // $arrShipper = array();
    // if (!empty($rs[0]['shippername'])) {
    //     array_push($arrShipper, strtoupper(htmlspecialchars_decode($rs[0]['shippername'])));
    // }
    // if (!empty($rs[0]['shipperaddress'])) {
    //     array_push($arrShipper, str_replace(chr(13), '<br>', strtoupper(htmlspecialchars_decode($rs[0]['shipperaddress']))));
    // }

    // $shipper = (!empty($arrShipper) ? implode('<br>', $arrShipper) : '');

    $shipper = 'PT. CIF TRANSPORTASI INDONESIA<br>JL. GUNUNG SAHARI BLOK B / 7<br>JAKARTA<br>TELP. : 62-21 6250901 , FAX : 62-21 6252919';

    $rsEmployee = $employee->getDataRowById(base64_decode($_SESSION[$obj->loginAdminSession]['id']));
    $from  = $rsEmployee[0]['name'];
    
    $consignee = strtoupper(htmlspecialchars_decode($rs[0]['consigneename'])) . '<br>' . str_replace(chr(13), '<br>', strtoupper($rs[0]['consigneeaddress']));
    $notifyParty = strtoupper(htmlspecialchars_decode($rsJobOrder[0]['notifypartyname'])) . '<br>' . str_replace(chr(13), '<br>', strtoupper($rsJobOrder[0]['notifypartyaddress']));

    $agent = '';
    if($rs[0]['isoverwriteagent'] == 0) {
        
        $agent = strtoupper(htmlspecialchars_decode($rs[0]['agentname'])) . '<br>' . str_replace(chr(13), '<br>', strtoupper($rs[0]['agentaddress']));
    
    } else {
        
        $rsAgent = $customer->getDataRowById($rs[0]['agentkey']);
        $agentName = $rsAgent[0]['name'];
        $agentAddress = $rsAgent[0]['address'];

        $agent = strtoupper(htmlspecialchars_decode($agentName)) . '<br>' . str_replace(chr(13), '<br>', strtoupper($agentAddress));
    }
    
    $alsoNotifyParty = $rsJobOrder[0]['alsonotifyparty'];
    
    $rsFeeder = $vessel->getDataRowById($rs[0]['feederkey']);
    $feederVessel = $rsFeeder[0]['name'];
    $flag = $rsFeeder[0]['flag'];
    $feederNumber = $rs[0]['feedernumber'];

    $rsMotherVessel = $vessel->getDataRowById($rs[0]['vesselkey']);
    $motherVessel = $rsMotherVessel[0]['name'] . ' ' . $rs[0]['vesselnumber'];

    $feederVesselFlag = '';
    if (!empty($feederVessel)) {
        $feederVesselFlag = $feederVessel . (!empty($feederNumber) ? ' / ' . $feederNumber : '') . (!empty($flag) ? ' / ' . $flag : '');
    }

    $rsPortOfReceipt = $port->getDataRowById($rs[0]['poreceiptkey']);
    $portOfReceipt = '';
    if (!empty($rsPortOfReceipt)) {
        $portOfReceipt = $rsPortOfReceipt[0]['name'];
    }

    $rsPOL = $port->getDataRowById($rs[0]['polkey']);
    $polName = '';
    if (!empty($rsPOL)) {
        $polName = $rsPOL[0]['name'];
    }

    $connectingVesselFlag = '';

    $rsConnectingVessel = $vessel->getDataRowById($rs[0]['connectingvesselkey']);
    $connectingVessel = $rsConnectingVessel[0]['name'];
    $connectingVesselFlag = $rsConnectingVessel[0]['flag'];
    $connectingVesselNumber = $rs[0]['connectingvesselnumber'];

    if (!empty($connectingVessel)) {
        $connectingVesselFlag = $connectingVessel . (!empty($connectingVesselNumber) ? ' / ' . $connectingVesselNumber : '') . (!empty($connectingVesselFlag) ? ' / ' . $connectingVesselFlag : '');
    }



    $rsPOD = $port->getDataRowById($rs[0]['podkey']);
    $podName = '';
    if (!empty($rsPOD)) {
        $podName = $rsPOD[0]['name'];
    }

    $rsPortOfDelivery = $port->getDataRowById($rs[0]['podeliverykey']);
    $portOfDelivery = '';
    if (!empty($rsPortOfDelivery)) {
        $portOfDelivery = $rsPortOfDelivery[0]['name'];
    }

    $shippedOnBoardDate = $obj->formatDBDate($rs[0]['trdate'], 'M, d-Y', array('returnOnEmpty' => true));
    $ETD = $obj->formatDBDate($rs[0]['etdpol'], 'd-M-Y', array('returnOnEmpty' => true));
    $ETA = $obj->formatDBDate($rs[0]['etapod'], 'M, d-Y', array('returnOnEmpty' => true));

    $numberOfOriginal = (!empty($rs[0]['numberoforiginal']) ? $rs[0]['numberoforiginal'] . ' ( ' . strtoupper($obj->sayNumber($rs[0]['numberoforiginal'])) . ' )' : '');

    $paymentType = ($rs[0]['freighttermkey'] == 1) ? 'PREPAID' : 'COLLECT';

    $marksNumber = (!empty($rs[0]['marksnumber'])) ? str_replace(chr(13), '<br>', strtoupper(htmlspecialchars_decode($rs[0]['marksnumber']))) : '';

    $party = '';
    if (in_array($rsJobOrder[0]['loadcontainertypekey'], array(EMKL['container']['fcl'], EMKL['container']['trucking'])) && $rsJobOrder[0]['transportationtypekey'] == EMKL['shipping']['sea']) {
        $arrParty = array();
        $rsParty = $emklJobOrder->getDetailVolume($rsJobOrder[0]['pkey']);
        for ($i = 0; $i < count($rsParty); $i++)
            array_push($arrParty, $obj->formatNumber($rsParty[$i]['qty']) . ' x ' . $rsParty[$i]['itemname']);
        $party = implode(', ', $arrParty);
    }
    $lclFclDescription = (in_array($rsJobOrder[0]['loadcontainertypekey'], LCL_CONTAINER_TYPE)) ? ' ' : '<br>' . $party . ' CONTAINER STC : <br>';
    $description = $lclFclDescription . '<br>' . nl2br(strtoupper($rs[0]['shortdescription']));

    $GW = $rs[0]['sumgrossweight'];
    $NW = $rs[0]['sumnetweight'];
    $MEAS = $rs[0]['summeas'];
    $totalPkgs = $rs[0]['sumqty'];

    $unitMeas = $rs[0]['unitofmeaskey'] == 1 ? 'M3' : 'CBM';

    $rsHBLContainer = $obj->getDetailHBLContainer($rs[0]['pkey']);

    $containers = '<table><tr><td style="width:75px;font-size:0.8em;">CONTAINER</td><td style="width:10px;font-size:0.8em;"> / </td><td style="width:34px;font-size:0.8em;">SIZE</td><td style="width:10px;font-size:0.8em;"> / </td><td style="width:60px;font-size:0.8em;">SEAL NO </td><td style="width:10px;font-size:0.8em;"> / </td><td style="width:30px;font-size:0.8em;"></td></tr><tr><td style="width:224px;font-size:0.8em;border-top:1px solid #000"></td></tr></table>';
    foreach ($rsHBLContainer as $container) {
        $containers .= '<span style="font-size:0.8em;">' . $container['containerno'] . '  /  ' . htmlspecialchars_decode($container['containername']) . '  /  ' . $container['sealno'] . ' <br></span>';
    }

    $rsCommodity = $emklJobOrder->getDetailCommodity($rs[0]['refheaderkey']);

    $arrCommodity = array();
    foreach ($rsCommodity as $commodity) {
        array_push($arrCommodity, $commodity['commodityname']);
    }

    $commodity = '';
    if (!empty($arrCommodity)) {
        $commodity = implode(',<br>', $arrCommodity);
    }

    $shippingLine = $rs[0]['shippinglinename'];

    $rsCustomer = $customer->getDataRowById($rs[0]['agentkey']);
    $agentName = $rsCustomer[0]['name'];

    $GW_NW_MEAS = '<table cellpadding="2">
                            <tr>
                                <td style="width:40px;text-align:right;">GW</td>
                                <td style="width:10px;text-align:center">:</td>
                                <td style="width:90px;text-align:right;">' . $obj->formatNumber($GW, 2) . ' KGS</td>
                            </tr>
                            <tr>
                                <td style="width:40px;text-align:right;">NW</td>
                                <td style="width:10px;text-align:center">:</td>
                                <td style="width:90px;text-align:right;">' . $obj->formatNumber($NW, 2) . ' KGS</td>
                            </tr>
                            <tr>
                                <td style="width:40px;text-align:right;">MEAS</td>
                                <td style="width:10px;text-align:center">:</td>
                                <td style="width:90px;text-align:right;">' . $obj->formatNumber($MEAS, 4) . ' ' . $unitMeas . ' </td>
                            </tr>
                        </table>';

    $companyName = $obj->loadSetting('companyName');
    $companyAddress = $obj->loadSetting('companyAddress');                

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
                                <tr><td style="width:40px;">Page</td><td style="width:10px">:</td><td style="width:80px;">1 of 1</td></tr>
                            </table>
                        </td>
                    </tr>
                </table>';

    $html .= '<table cellpadding="2">
                    <tr>
                        <td class="border-bottom" style="width:226.5px"></td>
                        <td style="width:220px;">Deliver Your Trust With Our Responsibility</td>
                        <td class="border-bottom" style="width:226.5px"></td>
                    </tr>
                </table>
                '; //END HEADER

    $html .= '<div style="clear:both"></div> ';

    $html .= '<table>
                    <tr><td style="width:676px;font-weight:bold;font-size:16px;text-align:center;">SHIPPING INSTRUCTION</td></tr>
                    <tr><td style="width:676px;font-weight:bold;font-size:13px;text-align:center;">S.I No. : ' . $rs[0]['code'] . '</td></tr>
                </table>';

    $html .= '<div style="clear:both"></div> ';

    $html .= '<table cellpadding="2">
                <tr>
                    <td class="border-left border-top" style="width:406px;font-size:0.8em;"> (6) SHIPPER/EXPORTER (COMPLETE NAME AND ADDRESS)</td>
                    <td class="border-left border-top border-right" style="width:270px;font-size:0.8em;"> DATE : </td>
                </tr>
                <tr>
                    <td class="border-left" rowspan="5" style="width:406px;font-size:0.8em;">&nbsp;<table><tr><td>' . $shipper . '</td></tr></table></td>
                    <td class="border-left border-right" style="width:270px;font-size:0.8em;height:20px;text-align:center;"><table><tr><td>' . $obj->formatDBDate($rs[0]['trdate'], 'M d, Y') . '</td></tr></table></td>
                </tr>

                <tr>
                    <td class="border-left border-top border-right" style="width:270px;font-size:0.8em;"> TO : </td>
                </tr>
                <tr>
                    <td class="border-left border-right" style="width:270px;font-size:0.8em;height:20px;text-align:center;"><table><tr><td>' . $shippingLine . '</td></tr></table></td>
                </tr>

                <tr>
                    <td class="border-left border-top border-right" style="width:270px;font-size:0.8em;"> ATTN :</td>
                </tr>
                <tr>
                    <td class="border-left border-right" style="width:270px;font-size:0.8em;height:20px;"></td>
                </tr>

                <tr>
                    <td class="border-left border-top" style="width:406px;font-size:0.8em;"> (7) CONSIGNEE (COMPLETE NAME AND ADDRESS)</td>
                    <td class="border-left border-top" style="width:135px;font-size:0.8em;"> PHONE :</td>
                    <td class="border-top border-right" style="width:135px;font-size:0.8em;"> FAX :</td>
                </tr>
                <tr>
                    <td class="border-left" rowspan="2" style="width:406px;font-size:0.8em;">&nbsp;<table><tr><td>' . $agent . '</td></tr></table></td>
                    <td class="border-left border-right" style="width:270px;font-size:0.8em;height:20px;"></td>
                </tr>

                <tr>
                    <td class="border-left border-top border-right" rowspan="2" style="width:270px;font-size:0.8em;height:60px"><table cellpadding="2">
                    <tr><td style="width:38px;">FROM</td><td style="width:10px">:</td><td style="width:180px">'. $from .'</td></tr>
                    <tr><td style="width:38px;">PHONE</td><td style="width:10px">:</td><td style="width:180px"> 61-21 6250901</td></tr>
                    <tr><td style="width:38px;">FAX</td><td style="width:10px">:</td><td style="width:180px"> 61-21 6252919</td></tr>
                    </table></td>    
                </tr>

        </table>';

    $html .= '<table cellpadding="2">
                    <tr>
                        <td class="border-left border-top" style="width:406px;font-size:0.8em;"> (8) NOTIFY PARTY (COMPLETE NAME AND ADDRESS)</td>
                        <td class="border-left border-top border-right" style="width:270px;font-size:0.8em;"> (11) ALSO NOTIFY PARTY - ROUTING INSTRUCTIONS</td>
                    </tr>
                    <tr>
                        <td class="border-left" style="width:406px;font-size:0.8em;height:85px;">&nbsp;<table><tr><td>' . $notifyParty . '</td></tr></table></td>
                        <td class="border-left border-right" style="width:270px;font-size:0.8em;height:85px;">&nbsp;<table><tr><td>' . $alsoNotifyParty . '</td></tr></table></td>
                    </tr>
                </table>';

    $html .= '<table cellpadding="2">
                    <tr>
                        <td class="border-left border-top" style="width:203px;font-size:0.8em;"> (12) FEEDER VESSEL/VOYAGE/FLAG</td>
                        <td class="border-left border-top" style="width:203px;font-size:0.8em;"> (15) PORT OF RECEIPT</td>
                        <td class="border-left border-top border-right" style="width:270px;font-size:0.8em;"> EQUIPMENT</td>
                    </tr>
                    <tr>
                        <td class="border-left" style="width:203px;font-size:0.8em;height:25px;">&nbsp;<table><tr><td>' . $feederVesselFlag . '</td></tr></table></td>
                        <td class="border-left" style="width:203px;font-size:0.8em;height:25px;">&nbsp;<table><tr><td>' . $portOfReceipt . '</td></tr></table></td>
                        <td class="border-left border-right" style="width:270px;font-size:0.8em;height:25px;">&nbsp;<table><tr><td>' . $party . '</td></tr></table></td>
                    </tr>
                </table>';

    $html .= '<table cellpadding="2">
                    <tr>
                        <td class="border-left border-top" style="width:203px;font-size:0.8em;"> (13) CONNECTING VESSEL/VOYAGE/FLAG</td>
                        <td class="border-left border-top" style="width:203px;font-size:0.8em;"> (16) PORT OF LOADING</td>
                        <td class="border-left border-top border-right" style="width:270px;font-size:0.8em;"> COMMODITY</td>
                    </tr>
                    <tr>
                        <td class="border-left" style="width:203px;font-size:0.8em;height:25px;">&nbsp;<table><tr><td>' . $motherVessel . '</td></tr></table></td>
                        <td class="border-left" style="width:203px;font-size:0.8em;height:25px;">&nbsp;<table><tr><td>' . $polName . '</td></tr></table></td>
                        <td class="border-left border-right" style="width:270px;font-size:0.8em;height:25px;">&nbsp;<table><tr><td>' . $commodity . '</td></tr></table></td>
                    </tr>
                </table>';

    $html .= '<table cellpadding="2">
                    <tr>
                        <td class="border-left border-top" style="width:203px;font-size:0.8em;"> (14) PORT OF DISCHARGE</td>
                        <td class="border-left border-top" style="width:203px;font-size:0.8em;"> (17) PORT OF DELIVERY</td>
                        <td class="border-left border-top border-right" style="width:135px;font-size:0.8em;"> SHIPPED ON BOARD</td>
                        <td class="border-left border-top border-right" style="width:135px;font-size:0.8em;"> NUMBER OF BL :</td>
                    </tr>
                    <tr>
                        <td class="border-left border-bottom" rowspan ="3"style="width:203px;font-size:0.8em;height:18px;">&nbsp;<table><tr><td>' . $podName . '</td></tr></table></td>
                        <td class="border-left border-bottom" rowspan ="3"style="width:203px;font-size:0.8em;height:18px;">&nbsp;<table><tr><td>' . $portOfDelivery . '<br><br>ETA : ' . $ETA . '</td></tr></table></td>
                        <td class="border-left border-right border-bottom" style="width:135px;font-size:0.8em;height:18px;">&nbsp;<table><tr><td>' . $shippedOnBoardDate . '</td></tr></table></td>
                        <td class="border-left border-right border-bottom" rowspan ="3"style="width:135px;font-size:0.8em;height:18px;">&nbsp;<table><tr><td>' . $numberOfOriginal . '</td></tr></table></td>
                    </tr>

                    <tr>
                        <td class="border-left border-top border-right" style="width:135px;font-size:0.8em;"> FREIGHT</td>
                    </tr>
                    <tr>
                        <td class="border-left border-right border-bottom" style="width:135px;font-size:0.8em;height:18px;">&nbsp;<table><tr><td>' . $paymentType . '</td></tr></table></td>
                    </tr>

                </table>';


    $html .= '
            <table cellpadding="2"><tr><td style="width:676px;font-weight:bold;font-size:14px;text-align:center;">** STUFFING ON **</td></tr></table>
        ';

    $html .= '<table cellpadding="2">
                    <tr>
                        <td class="border-left border-top border-bottom" style="width:186px;font-size:0.8em;text-align:center;">MARKS AND<br>NUMBER</td>
                        <td class="border-left border-top border-bottom" style="width:60px;font-size:0.8em;text-align:center;">NO OF<br>PKGS</td>
                        <td class="border-left border-top border-right border-bottom" style="width:290px;font-size:0.8em;text-align:center;">DESCRIPTION OF<br>PACKAGES AND GOODS</td>
                        <td class="border-left border-top border-right border-bottom" style="width:60px;font-size:0.8em;text-align:center;">GROSS<br>WEIGHT</td>
                        <td class="border-left border-top border-right border-bottom" style="width:80px;font-size:0.8em;text-align:center;">MEASUREMENT</td>
                    </tr>
                </table>
                ';

    $html .='
        <table cellpadding="2">
            <tr>
                <td style="width:246px;font-size:0.8em;">' . $marksNumber . '</td>
                <td style="width:290px;font-size:0.8em;">' . $description . '</td>
                <td style="width:150px;font-size:0.8em;">' . $GW_NW_MEAS . '</td>
            </tr>
            <tr>
                <td style="width:400px">' . $containers . '</td>
            </tr>
        </table>
    ';

    return $html;

};

?>