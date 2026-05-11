<?php

$PRINT_SETTINGS = array(
    'showPrintHeader' => false,
    'footer' => '',
    'pdfMarginHeader' => 8,
    'marginFooter' => 0,
    'paperSetting' => 'A4,P',
);

includeClass(array('EMKLJobOrder.class.php', 'Employee.class.php', 'Customer.class.php', 'Vessel.class.php', 'Port.class.php', 'EMKLHouseBL.class.php'));
$emklJobOrder = new EMKLJobOrder();
$vessel = new Vessel();
$port = new Port();
$emklHouseBL = new EMKLHouseBL();
$customer = new Customer();
$employee = new Employee();

$obj = $emklJobOrder;

$generateReportContent = function ($dataset) {

    $obj = new EMKLJobOrder();
    $vessel = new Vessel();
    $port = new Port();
    $emklHouseBL = new EMKLHouseBL();
    $customer = new Customer();
    $employee = new Employee();


    $rs = $dataset['rs'];


    $carrierName = $rs[0]['carriername'];

    // $arrShipper = array();
    // if (!empty($rs[0]['customername'])) {
    //     array_push($arrShipper, strtoupper(htmlspecialchars_decode($rs[0]['customername'])));
    // }
    // if (!empty($rs[0]['customeraddress'])) {
    //     array_push($arrShipper, str_replace(chr(13), '<br>', strtoupper(htmlspecialchars_decode($rs[0]['customeraddress']))));
    // }
    // $shipper = (!empty($arrShipper) ? implode('<br>', $arrShipper) : '');
    $shipper = 'PT. CIF TRANSPORTASI INDONESIA<br>JL. GUNUNG SAHARI BLOK B / 7<br>JAKARTA<br>TELP. : 62-21 6250901 , FAX : 62-21 6252919';

    
    $consignee = '';

    if ($rs[0]['jobtypekey'] == EMKL['jobType']['import']) {
        $arrConsignee = array();
        if (!empty($rs[0]['customername'])) {
            array_push($arrConsignee, strtoupper(htmlspecialchars_decode($rs[0]['customername'])));
        }
        if (!empty($rs[0]['customeraddress'])) {
            array_push($arrConsignee, str_replace(chr(13), '<br>', strtoupper(htmlspecialchars_decode($rs[0]['customeraddress']))));
        }
        $consignee = (!empty($arrConsignee) ? implode('<br>', $arrConsignee) : '');
    } else {
        $consignee = $rs[0]['consigneename'];
    }

    $agent = ''; 
    if(!empty($rs[0]['agentkey'])) {
        $arrAgent = array();
        $rsCustomer = $customer->getDataRowById($rs[0]['agentkey']);
        
        if(!empty($rsCustomer[0]['name'])) {
            array_push($arrAgent, strtoupper(htmlspecialchars_decode($rsCustomer[0]['name'])));
        }

        if (!empty($rsCustomer[0]['address'])) {
            array_push($arrAgent, str_replace(chr(13), '<br>', strtoupper(htmlspecialchars_decode($rsCustomer[0]['address']))));
        }

        $agent = (!empty($arrAgent) ? implode('<br>', $arrAgent) : '');
    }

    $createdBy = $employee->getDataRowById($rs[0]['createdby']);


    $notifyParty = '';

    $rsFeeder = $vessel->getDataRowById($rs[0]['feederkey']);
    $feederVessel = $rsFeeder[0]['name'];
    $flag = $rsFeeder[0]['flag'];
    $feederNumber = $rs[0]['feedernumber'];

    $feederVesselFlag = '';
    if (!empty($feederVessel)) {
        $feederVesselFlag = $feederVessel . (!empty($feederNumber) ? ' / ' . $feederNumber : '') . (!empty($flag) ? ' / ' . $flag : '');
    }


    $rsPortOfReceipt = $port->getDataRowById($rs[0]['placeofreceiptkey']);
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

    $rsMotherVessel = $vessel->getDataRowById($rs[0]['vesselkey']);
    $motherVessel = $rsMotherVessel[0]['name'] . ' ' . $rs[0]['vesselnumber'];

    $rsPOD = $port->getDataRowById($rs[0]['podkey']);
    $podName = '';
    if (!empty($rsPOD)) {
        $podName = $rsPOD[0]['name'];
    }

    $rsPortOfDelivery = $port->getDataRowById($rs[0]['placeofdeliverykey']);
    $portOfDelivery = '';
    if (!empty($rsPortOfDelivery)) {
        $portOfDelivery = $rsPortOfDelivery[0]['name'];
    }

    $shipmentTerm = $obj->getShipmentTerm($rs[0]['shipmenttermkey']);
    $shipmentTerm2 = $obj->getShipmentTerm($rs[0]['shipmentterm2key']);
    $typeOfMovement = $shipmentTerm[0]['name'] . ' - ' . $shipmentTerm2[0]['name'];
    $originalToBeReleasedAt = '';

    $rsContainer = $obj->getDetailContainer($rs[0]['pkey']);
    $rsVolume = $obj->getDetailVolume($rs[0]['pkey']);

    $TOTAL_GW = 0;
    $TOTAL_NW = 0;
    $TOTAL_MEAS = 0;
    $TOTAL_QTY = 0;

    $ARR_CONTAINER_SEAL_NO = array();
    foreach ($rsContainer as $container) {
        $TOTAL_GW += $container['grossweight'];
        $TOTAL_NW += $container['netweight'];
        $TOTAL_MEAS += $container['meas'];
        $TOTAL_QTY += $container['qty'];

        $container_seal_no = $container['containerno'] . ' / ' . $container['sealno'];
        array_push($ARR_CONTAINER_SEAL_NO, $container_seal_no);
    }

    $GW = $obj->formatNumber($TOTAL_GW, 2) . ' KGS';
    $NW = $obj->formatNumber($TOTAL_NW, 2) . ' KGS';
    $MEAS = $obj->formatNumber($TOTAL_MEAS, 4) . ' CBM';
    $QTY = $obj->formatNumber($TOTAL_QTY, 2) . ' ' . $rsContainer[0]['unitname'];

    $CONTAINER_SEAL_NO = '';
    if (!empty($ARR_CONTAINER_SEAL_NO)) {
        $CONTAINER_SEAL_NO = implode('<br>', $ARR_CONTAINER_SEAL_NO);
    }

    $arrVolume = array();
    foreach ($rsVolume as $volume) {
        $volQty = $obj->formatNumber($volume['qty']) . ' X ' . $volume['itemname'];
        array_push($arrVolume, $volQty);
    }

    $volumeQty = '';
    if (!empty($arrVolume)) {
        $volumeQty = implode('<br>', $arrVolume);
    }

    $rsCommodity = $obj->getDetailCommodity($rs[0]['pkey']);

    $arrCommodity = array_column($rsCommodity, 'commodityname');

    $commodity = (empty($arrCommodity) ? '' : implode(',<br>', $arrCommodity));

    $description = '';
    $descriptionAndQty = (!empty($description) ? $description . '<br>' : '') . $QTY . (!empty($commodity) ? '<br><br>'. $commodity.'' : '');

    $GROSS_WEIGHT_NET_WEIGHT = 'GW :<br>' . $GW . '<br><br>NW :<br>' . $NW;

    $serviceContract = (!empty($rs[0]['servicecontract']) ? $rs[0]['servicecontract'] . ' - Service Contract' : '');
    $freight = $obj->getFreightTerm($rs[0]['freighttermkey']);
    $ETD = $obj->formatDBDate($rs[0]['etdpol'], 'd-M-Y', array('returnOnEmpty' => true));
    $ETA = $obj->formatDBDate($rs[0]['etapod'], 'd-M-Y', array('returnOnEmpty' => true));
    $STUFFING = '';

    $rsHBL = $emklHouseBL->searchData('', '', true, ' and ' . $emklHouseBL->tableName . '.refheaderkey = ' . $obj->oDbCon->paramString($rs[0]['pkey']) . ' ');

    $notifyParty = strtoupper(htmlspecialchars_decode($rs[0]['notifypartyname'])) . '<br>' . str_replace(chr(13), '<br>', strtoupper($rs[0]['notifypartyaddress']));
    $alsoNotifyParty = $rs[0]['alsonotifyparty'];

    $remarks = $rs[0]['trdesc']; 

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

    $title = '<h3>PERFORMA SHIPPING INSTRUCTION ( SI )</h3>';


    $html .= '<table cellpadding="2">
                    <tr>
                        <td class="border-left border-top-right" style="width:125px"></td>
                        <td class="border-top-right" colspan="3" style="width:300px;text-align:center;">' . $title . '</td>
                        <td class="border-top-right" style="width:60px;font-size:0.8em"> (4) DATE</td>
                        <td class="border-top-right" style="width:191px;font-size:0.9em;">&nbsp;' . $obj->formatDBDate($rs[0]['trdate'], 'd M Y') . '</td>
                    </tr>
                    <tr>
                        <td class="border-left border-right" style="width:125px"></td>
                        <td class="" style="width:90px;text-align:center;font-size:0.8em"> (1) SI NO</td>
                        <td class="" style="width:120px;text-align:center;font-weight:bold;font-size:13px;">&nbsp;'. $rs[0]['code'] .'</td>
                        <td class="border-right" style="width:90px;text-align:center;font-size:0.8em"> (5) EXPORT REFERENCES</td>
                        <td class="border-right" style="width:60px;font-size:0.8em"> (4a) TO</td>
                        <td class="border-right" style="width:191px;font-size:0.9em;">&nbsp;<table><tr><td>' . $carrierName . '</td></tr></table></td>
                    </tr>
                    <tr>
                        <td class="border-left border-right" style="width:125px"></td>
                        <td class="" style="width:90px;text-align:center;"></td>
                        <td class="" style="width:120px;text-align:center;"></td>
                        <td class="border-right" style="width:90px;text-align:center;"></td>
                        <td class="border-right" style="width:60px;font-size:0.8em"> (4b) ATTN</td>
                        <td class="border-right" style="width:191px;font-size:0.9em">&nbsp;<table><tr><td> </td></tr></table></td>
                    </tr>
                </table>
            ';//code, date, to, attn content

    $html .= '<table cellpadding="2">
                    <tr>
                        <td class="border-left border-top-right" style="width:415px;font-size:0.8em"> (6) SHIPPER/EXPORTER (COMPLETE NAME AND ADDRESS)</td>
                        <td class="border-top-right" style="width:261px;font-size:0.8em"> (2) BOOKING NO</td>
                    </tr>
                    <tr>
                        <td class="border-left border-right" rowspan="3" style="width:415px;height:35px;font-size:0.9em;">&nbsp;<table><tr><td>' . $shipper . '</td></tr></table></td>
                        <td class="border-right" style="width:261px;height:35px"></td>
                    </tr>
                    
                    <tr>
                        <td class="border-top-right" style="width:261px;font-size:0.8em"> (3) BILL OF LANDING No</td>
                    </tr>
                    <tr>
                        <td class="border-right" style="width:261px;height:35px;font-size:0.9em;">&nbsp;</td>
                    </tr>
                </table>
            ';//shipper, bill of landing no

    $html .= '<table cellpadding="2">
                    <tr>
                        <td class="border-left border-top-right" style="width:415px;font-size:0.8em"> (7) CONSIGNEE (COMPLETE NAME AND ADDRESS)</td>
                        <td class="border-top-right" style="width:261px;font-size:0.8em"> (9) FORWADING AGENT REFERENCES</td>
                    </tr>
                    <tr>
                        <td class="border-left border-right" rowspan="3" style="width:415px;height:35px;font-size:0.9em;">&nbsp;<table><tr><td>'. $agent .'</td></tr></table></td>
                        <td class="border-right" style="width:261px;height:35px">&nbsp;<table><tr><td> </td></tr></table></td>
                    </tr>
                    
                    <tr>
                        <td class="border-top-right" style="width:261px;font-size:0.8em"> (10) POINT AND COUNTRY OF ORIGIN OF GOODS</td>
                    </tr>
                    <tr>
                        <td class="border-right" style="width:261px;height:35px">&nbsp;<table><tr><td> </td></tr></table></td>
                    </tr>
                </table>
            ';

    $html .= '<table cellpadding="2">
                    <tr>
                        <td class="border-left border-top-right" style="width:415px;font-size:0.8em"> (8) NOTIFY PARTY (COMPLETE NAME AND ADDRESS)</td>
                        <td class="border-top-right" style="width:261px;font-size:0.8em"> (11) ALSO NOTIFY PARTY - ROUTING INSTRUCTIONS</td>
                    </tr>
                    <tr>
                        <td class="border-left border-right" style="width:415px;height:70px;font-size:0.9em;">&nbsp;<table><tr><td>'. $notifyParty .'</td></tr></table></td>
                        <td class="border-right" rowspan="3" style="width:261px;font-size:0.9em;height:70px">&nbsp;<table><tr><td> '. $alsoNotifyParty .' </td></tr></table></td>
                    </tr>

                    <tr>
                        <td class="border-left border-top-right" style="width:207.5px;font-size:0.8em"> (12) FEEDER VESSEL/VOYAGE/FLAG</td>
                        <td class="border-left border-top-right" style="width:207.5px;font-size:0.8em"> (15) PORT OF RECEIPT</td>
                    </tr>
                    <tr>
                        <td class="border-left border-right" style="width:207.5px;height:35px;font-size:0.9em;">&nbsp;<table><tr><td>' . $feederVesselFlag . '</td></tr></table></td>
                        <td class="border-right" style="width:207.5;height:35px;font-size:0.9em;">&nbsp;<table><tr><td>' . $portOfReceipt . '</td></tr></table></td>
                    </tr>

                </table>
            ';

    $html .= '<table cellpadding="2">
                    <tr>
                        <td class="border-left border-top-right" style="width:207.5px;font-size:0.8em"> (13) CONNECTING VESSEL/VOYAGE/FLAG</td>
                        <td class="border-top-right" style="width:207.5px;font-size:0.8em"> (16) PORT OF LOADING</td>
                        <td class="border-top-right" style="width:261px;font-size:0.8em"></td>
                    </tr>
                    <tr>
                        <td class="border-left border-right" style="width:207.5px;height:35px;font-size:0.9em;">&nbsp;<table><tr><td>'. $motherVessel .'</td></tr></table></td>
                        <td class="border-left border-right" style="width:207.5px;height:35px;font-size:0.9em;">&nbsp;<table><tr><td>' . $polName . '</td></tr></table></td>
                        <td class="border-right" style="width:261px;height:35px"></td>
                    </tr>
                </table>
            ';

    $html .= '<table cellpadding="2">
                    <tr>
                        <td class="border-left border-top-right" style="width:207.5px;font-size:0.8em"> (14) PORT OF DISCHARGE</td>
                        <td class="border-top-right" style="width:207.5px;font-size:0.8em"> (16) PORT OF DELIVERY</td>
                        <td class="border-top-right" style="width:110.5px;font-size:0.7em"> (18) TYPE OF MOVEMENT</td>
                        <td class="border-top-right" style="width:150.5PX;font-size:0.7em"> (19) ORIGINAL TO BE RELEASED AT</td>
                    </tr>
                    <tr>
                        <td class="border-left border-right border-bottom" style="width:207.5px;height:35px;font-size:0.9em;">&nbsp;<table><tr><td>'. $podName .'</td></tr></table></td>
                        <td class="border-left border-right border-bottom" style="width:207.5px;height:35px;font-size:0.9em;">&nbsp;<table><tr><td>' . $portOfDelivery . '</td></tr></table></td>
                        <td class="border-right border-bottom" style="width:110.5px;height:35px;font-size:0.9em;">&nbsp;<table><tr><td>' . $typeOfMovement . '</td></tr></table></td>
                        <td class="border-right border-bottom" style="width:150.5px;height:35px;font-size:0.9em;">&nbsp;<table><tr><td>' . $originalToBeReleasedAt . '</td></tr></table></td>
                    </tr>
                </table>
            ';

    $html .= '<table cellpadding="2">
            <tr>
                <td style="width:230px;font-size: 0.8em;"> (CHECK `HM` COLUMN IF HAZARDOUS MATERIAL) </td>
                <td style="width:446px;font-size: 0.8em;"> PARTICULARS DECLARED BY SHIPPER BUT NOT ACKNOWLEDGED BY THE CARRIER</td>
            </tr>
        </table>';

    $html .= '<table cellpadding="2">
            <tr>
                <td class="border-left border-top" style="width:125px;font-size: 0.8em;"> (20) CNTR NO W/ SEAL NO</td>
                <td class="border-top" style="width:100px;font-size: 0.8em;"> (21) QUANTITY</td>
                <td class="border-top" style="width:251px;font-size: 0.8em;"> (22) DESCRIPTION OF GOODS</td>
                <td class="border-top" style="width:100px;font-size: 0.8em;"> (23) GROSS WEIGHT</td>
                <td class="border-right border-top" style="width:100px;font-size: 0.8em;"> (24) MEASUREMENT</td>
            </tr>

            <tr>
                <td class="border-left border-right" style="width:125px;height:120px;font-size: 0.8em;">&nbsp;<table><tr><td>' . $CONTAINER_SEAL_NO . '</td></tr></table></td>
                <td class="border-right" style="width:100px;height:120px;text-align:center;font-size: 0.8em;">&nbsp;<table><tr><td>' . $volumeQty . '</td></tr></table></td>
                <td class="border-right" style="width:251px;height:120px;font-size: 0.8em;">&nbsp;<table><tr><td>' . $descriptionAndQty . '</td></tr></table></td>
                <td class="border-right" style="width:100px;height:120px;font-size: 0.8em;">&nbsp;<table><tr><td>'. $GROSS_WEIGHT_NET_WEIGHT .'</td></tr></table></td>
                <td class="border-right" style="width:100px;height:120px;font-size: 0.8em;">&nbsp;<table><tr><td>' . $MEAS . '</td></tr></table></td>
            </tr>
        </table>';

    $html .= '<table cellpadding="2">
            <tr>
                <td class="border-left border-top border-right" style="width:225px;font-size: 0.8em;"> (25) ADDITIONAL COMMENTS/REQUIREMENTS</td>
                <td class="border-top border-right" style="width:451px;font-size: 0.8em;"> (26) REMARKS</td>
            </tr>

            <tr>
                <td class="border-left border-right" style="width:225px;height:60px;font-size: 0.8em;">
                <table>
                    <tr>
                        <td style="width:50px">FREIGHT</td>
                        <td style="width:15px">:</td>
                        <td style="width:150px;font-weight:0.8e,;">'. strtoupper($freight[0]['name']) .'</td>
                    </tr>
                    <tr>
                        <td style="width:50px">ETD</td>
                        <td style="width:15px">:</td>
                        <td style="width:150px;font-weight:0.8em;">' . $ETD . '</td>
                    </tr>
                    <tr>
                        <td style="width:50px">ETA</td>
                        <td style="width:15px">:</td>
                        <td style="width:150px;font-weight:0.8px">' . $ETA . '</td>
                    </tr>
                    <tr>
                        <td style="width:50px">STUFFING</td>
                        <td style="width:15px">:</td>
                        <td style="width:150px;font-weight:0.8px">' . $STUFFING . '</td>
                    </tr>
                </table>
                </td>
                <td class="border-right" style="width:451px;height:60px;font-size: 0.8em;">&nbsp;'. $remarks .'</td>
            </tr>
        </table>';

    $html .= '<table cellpadding="2">
                <tr>
                    <td class="border-left border-right" style="width:225px;font-size: 0.8em;"></td>
                    <td class="border-right" style="width:251px;font-size: 0.8em;"></td>
                    <td class="border-right border-top" style="width:200px;"> '. $createdBy[0]['name'] .'</td>
                </tr>

                <tr>
                    <td class="border-left border-right" style="width:225px;font-size: 0.8em;"></td>
                    <td class="border-right" style="width:251px;font-size: 0.8em;"></td>
                    <td class="border-right border-top" style="width:80px;font-size: 0.8em;text-align:center;"> SIGNED BY </td>
                    <td class="border-right border-top" style="width:120px;font-size: 0.8em;"> </td>
                </tr>
                <tr>
                    <td class="border-left border-right border-bottom" style="width:225px;font-size:0.8em;height:50px;"></td>
                    <td class="border-right border-bottom" style="width:251px;font-size:0.8em;height:50px;"></td>
                    <td class="border-right border-bottom" style="width:80px;font-size:0.8em;height:50px;text-align:center;"></td>
                    <td class="border-right border-bottom" style="width:120px;font-size:0.8em;height:50px;"> </td>
                </tr>
            </table>';

    

    return $html;
};

?>