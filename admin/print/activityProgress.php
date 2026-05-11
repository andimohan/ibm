<?php 
$PRINT_SETTINGS =  array(   
'showPrintHeader' => false,
'showPrintFooter' => false
);

includeClass(array('ActivityProgress.class.php'));
$activityProgress = createObjAndAddToCol(new ActivityProgress());

$obj = $activityProgress;

$generateReportContent = function ($dataset) {
    
    $obj = new ActivityProgress();
    $emklJobOrder = createObjAndAddToCol(new EMKLJobOrder());
    $customer = createObjAndAddToCol(new Customer());

    $profileImg = $obj->loadSetting('companyLogo'); 
    $imgLetterhead =  HTTP_HOST.'download.php?filename=setting/companyLogo/'.$profileImg; 
    
    $rs = $dataset['rs'];
    $html = $obj->printSetting['defaultStyle'];

    $rsJobOrder = $emklJobOrder->searchData('','',true,  ' and ' . $emklJobOrder->tableName.'.pkey = ('. $obj->oDbCon->paramString($rs[0]['joborderkey']) .') ');

    $rsJODetail = $emklJobOrder->getDetailWithRelatedInformation($rs[0]['joborderkey']);
    $rsActivity = $obj->getActivityProgressByJobOrder($rs[0]['joborderkey']);

    if ($rsJobOrder[0]['jobtypekey'] == EMKL['jobType']['import']) {
        $rsCustomer = $customer->getDataRowById($rsJobOrder[0]['customerkey']);
        $shipperName = htmlspecialchars_decode($rsJobOrder[0]['consigneename']);
        $consigneeName = htmlspecialchars_decode($rsCustomer[0]['name']);
    } else {
        $shipperName = htmlspecialchars_decode($rsJobOrder[0]['shippername']);
        $consigneeName = htmlspecialchars_decode($rsJobOrder[0]['consigneename']);
    }

    $hblNumber = array_column($rsJODetail, 'hbl');
    $hblNumber = (!empty($hblNumber) ? implode(', ', $hblNumber) : '');

    $dimensionCalculate = $rsJODetail[0]['length'] * $rsJODetail[0]['width'] * $rsJODetail[0]['height'];
    $dimension = $obj->formatNumber($dimensionCalculate,2) . ' CM';
    $meas = $obj->formatNumber($rsJODetail[0]['meas'], 2);
    $GWNW =  $obj->formatNumber($rsJODetail[0]['grossweight'], 2) . ' / ' . $obj->formatNumber($rsJODetail[0]['weight'], 2);
    $qty = $obj->formatNumber($rsJODetail[0]['qty'], 2);

    $html .='
        <table>
            <tr>
            <td><img src="'.$imgLetterhead.'" style="height: 100px"></td>
            <td style="font-size:1.5em; font-weight:bold; text-align:center;vertical-align:bottom;"><br><br><br><span>= '. $obj->formatDBDate($rsJobOrder[0]['trdate'], 'Y') .' =</span><br><span style="text-decoration:underline;">CUSTOMER JOB SHEET</span></td>
            <td style="text-align:right; font-size:0.9em"></td>
            </tr>
        </table>
    ';

    $html .= '<div style="clear:both"></div>';
    
    $html .= '
        <table cellpadding="1">
            <tr>
                <td style="width:365px"><table cellpadding="2">
                        <tr>
                            <td style="width:80px;font-weight:bold;">Job Order</td>
                            <td style="width:10px;text-align:center;">:</td>
                            <td style="width:200px">'. $rsJobOrder[0]['code'] .'</td>
                        </tr>
                        <tr>
                            <td style="width:80px;">Ownership</td>
                            <td style="width:10px;text-align:center;">:</td>
                            <td style="width:200px;">Nomination / Freehand</td>
                        </tr>
                        <tr>
                            <td style="width:80px;">Mode</td>
                            <td style="width:10px;text-align:center;">:</td>
                            <td style="width:200px"><b>AE/SE - AI/SI</b> (LCL/FCL) </td>
                        </tr>
                        <tr>
                            <td style="width:80px;">SI/P.O No</td>
                            <td style="width:10px;text-align:center;">:</td>
                            <td style="width:200px;border-bottom:1px solid black"></td>
                        </tr>
                        <tr>
                            <td style="width:80px;">Shipper</td>
                            <td style="width:10px;text-align:center;">:</td>
                            <td style="width:200px;border-bottom:1px solid black">'. $shipperName .'</td>
                        </tr>
                        <tr>
                            <td style="width:80px;">Consignee</td>
                            <td style="width:10px;text-align:center;">:</td>
                            <td style="width:200px;border-bottom:1px solid black">'. $consigneeName .'</td>
                        </tr>
                        <tr>
                            <td style="width:80px;">Origin</td>
                            <td style="width:10px;text-align:center;">:</td>
                            <td style="width:200px;border-bottom:1px solid black">'. $rsJobOrder[0]['placeofdeliveryname'] .'</td>
                        </tr>
                        <tr>
                            <td style="width:80px;">Destination</td>
                            <td style="width:10px;text-align:center;">:</td>
                            <td style="width:200px;border-bottom:1px solid black">'. $rsJobOrder[0]['placeofreceiptname'] .'</td>
                        </tr>
                    </table>
                </td>
                <td style="width:365px"><table cellpadding="2">
                        <tr>
                            <td style="width:80px;font-weight:bold;">AJU No.</td>
                            <td style="width:10px;text-align:center;">:</td>
                            <td style="width:200px;border-bottom:1px solid black">'.$rsJobOrder[0]['aju'].'</td>
                        </tr>
                        <tr>
                            <td style="width:80px;">Subject Email</td>
                            <td style="width:10px;text-align:center;">:</td>
                            <td style="width:200px;border-bottom:1px solid black"></td>
                        </tr>
                        <tr>
                            <td style="width:80px;">Agent (PIC)</td>
                            <td style="width:10px;text-align:center;">:</td>
                            <td style="width:200px;border-bottom:1px solid black">'. $rsJobOrder[0]['agentname'] .'</td>
                        </tr>
                        <tr>
                            <td style="width:80px;">Sales Name</td>
                            <td style="width:10px;text-align:center;">:</td>
                            <td style="width:200px;border-bottom:1px solid black">'.$rsJobOrder[0]['salesname'].'</td>
                        </tr>
                        <tr>
                            <td style="width:80px;">Terms</td>
                            <td style="width:10px;text-align:center;">:</td>
                            <td style="width:200px;border-bottom:1px solid black"></td>
                        </tr>
                        <tr>
                            <td style="width:80px;">MBL/MAWB</td>
                            <td style="width:10px;text-align:center;">:</td>
                            <td style="width:200px;border-bottom:1px solid black">'. $rsJobOrder[0]['mblnumber'] .'</td>
                        </tr>
                        <tr>
                            <td style="width:80px;">HBL/HAWB</td>
                            <td style="width:10px;text-align:center;">:</td>
                            <td style="width:200px;border-bottom:1px solid black">'. $hblNumber .'</td>
                        </tr>
                        <tr>
                            <td style="width:80px;">Remarks</td>
                            <td style="width:10px;text-align:center;">:</td>
                            <td style="width:200px;border-bottom:1px solid black"></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <div style="clear:both"></div>
        <table cellpadding="2">
            <tr>
                <td rowspan="2" style="border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;width:400px">
                    <table>
                        <tr>
                            <td style="width:90px">Quantity</td>
                            <td style="width:10px;text-align:center;">:</td>
                            <td style="width:300px">'. $qty .'</td>
                        </tr>
                    </table>
                </td>
                <td style="border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;width:270px">
                <table>
                        <tr>
                            <td style="width:80px">DIM</td>
                            <td style="width:10px;text-align:center;">:</td>
                            <td style="width:180px">'. $dimension .'</td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td style="border-bottom:1px solid black;border-right:1px solid black;width:270px">
                    <table>
                        <tr>
                            <td style="width:80px">MEAS</td>
                            <td style="width:10px;text-align:center;">:</td>
                            <td style="width:180px">'. $meas .'</td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td style="border-left:1px solid black;border-bottom:1px solid black;border-right:1px solid black;width:400px">
                    <table>
                        <tr>
                            <td style="width:90px">Carrier Name</td>
                            <td style="width:10px;text-align:center;">:</td>
                            <td style="width:300px"></td>
                        </tr>
                    </table>
                </td>
                <td style="border-bottom:1px solid black;border-right:1px solid black;width:270px">
                    <table>
                        <tr>
                            <td style="width:80px">VOL.WT</td>
                            <td style="width:10px;text-align:center;">:</td>
                            <td style="width:180px"></td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td style="border-left:1px solid black;border-right:1px solid black;width:400px">
                    <table>
                        <tr>
                            <td style="width:90px">Schedule details</td>
                            <td style="width:10px;text-align:center;">:</td>
                            <td style="width:300px"></td>
                        </tr>
                    </table>
                </td>
                <td style="border-bottom:1px solid black;border-right:1px solid black;width:270px">
                    <table>
                        <tr>
                            <td style="width:80px">GW / NW</td>
                            <td style="width:10px;text-align:center;">:</td>
                            <td style="width:180px">'. $GWNW .'</td>
                        </tr></table></td>
            </tr>

            <tr>
                <td style="border-left:1px solid black;border-right:1px solid black;width:400px">
                    <table><tr>
                            <td style="width:90px;text-align:right"></td>
                            <td style="width:5px;text-align:right"></td>
                            <td style="width:145px;">
                                <table>
                                <tr>
                                    <td style="width:30px">ETD</td>
                                    <td style="width:5px;text-align:left;">:</td>
                                    <td style="width:100px;border-bottom:1px solid black;">'. $obj->formatDBDate($rsJobOrder[0]['etdpol'], 'd / m / Y') .'</td>
                                </tr>
                            </table></td>
                            <td style="width:165px">
                            <table>
                                <tr>
                                    <td style="width:30px">ETA</td>
                                    <td style="width:5px;text-align:left;">:</td>
                                    <td style="width:100px;border-bottom:1px solid black;">'. $obj->formatDBDate($rsJobOrder[0]['etapod'], 'd / m / Y') .'</td>
                                </tr>
                            </table>
                            </td>
                        </tr></table>
                </td>
                <td style="border-bottom:1px solid black;border-right:1px solid black;width:270px"></td>
            </tr>
            <tr>
                <td style="border-left:1px solid black;border-bottom:1px solid black;border-right:1px solid black;width:400px">
                <table><tr>
                            <td style="width:90px">Reschedule</td>
                            <td style="width:10px;text-align:center;">:</td>
                            <td style="width:300px"><table><tr>
                            <td style="width:145px;"><table>
                                <tr>
                                    <td style="width:30px">ETD</td>
                                    <td style="width:5px;text-align:left;">:</td>
                                    <td style="width:100px;border-bottom:1px solid black;"></td>
                                </tr>
                            </table></td>
                            <td style="width:155px"><table>
                                <tr>
                                    <td style="width:30px">ETA</td>
                                    <td style="width:5px;text-align:left;">:</td>
                                    <td style="width:100px;border-bottom:1px solid black;"></td>
                                </tr>
                            </table>
                            </td>
                        </tr></table></td>
                        </tr></table>
                </td>
                <td style="border-bottom:1px solid black;border-right:1px solid black;width:270px"></td>
            </tr>
        </table>
        <table cellpadding="2">
        <tr>
            <td style="width:245px;border-bottom:1px solid black;border-left:1px solid black">Remarks/ Special Request :</td>
            <td style="width:155px;border-bottom:1px solid black">14 days free  :</td>
            <td style="text-align:left;width:270px;border-bottom:1px solid black;border-right:1px solid black">fumigation/ISPM 15 : No / YES</td>
        </tr>
        </table>
    ';

    $html .='<div style="clear:both"></div>';

    $html .='
        <table cellpadding="4">
            <thead>
                <tr>
                    <td style="text-align:center;font-weight:bold;width:330px;border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;">ACTIVITY</td>
                    <td style="text-align:center;font-weight:bold;width:120px;border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;">DATE</td>
                    <td style="text-align:center;font-weight:bold;width:220px;border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;">PIC ( Person In Charge )</td>
                </tr>
            </thead>
            <tbody>
        ';
        for ($i = 0; $i < count($rsActivity); $i++) {
            $html .= '
                <tr>
                    <td style="width:330px;border-left:1px solid black;border-bottom:1px solid black;">'. $rsActivity[$i]['activityname'] .'</td>
                    <td style="text-align:center;width:120px;border-left:1px solid black;border-bottom:1px solid black;">'. $obj->formatDBDate($rsActivity[$i]['date'], 'd / m / Y') .'</td>
                    <td style="width:220px;border-left:1px solid black;border-bottom:1px solid black;border-right:1px solid black;">'. nl2br($rsActivity[$i]['trdesc']) .'</td>
                </tr>
            ';
        }
    $html .='
            </tbody>
        </table>
    ';

    return $html;


}


?>