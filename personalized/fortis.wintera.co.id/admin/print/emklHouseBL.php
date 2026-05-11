<?php

$pdf->setCustomSettings(
    array(
        'showPrintHeader' => false,
        'footer' => '',
        'pdfMarginHeader' => 8,
        'marginFooter' => -10,
        'paperSetting' => 'A4,P',
    )
);


includeClass(array('EMKLHouseBL.class.php', 'Vessel.class.php'));
$emklHBL = new EMKLHouseBL();
$vessel = new Vessel();

$needAttachment = false;

$obj = $emklHBL;


$arrCopy = array('Original', 'Non-Negotiable Copy');

$content = function ($dataset) {
    global $needAttachment;
    global $arrCopy;

    $generateHeaderTable = function ($dataset, $param) {

        global $pdf;
        global $needAttachment;
        global $arrCopy;

        $obj = new EMKLHouseBL();
        $emklJobOrder = new EMKLJobOrder();
        $customer = new Customer();
        $consignee = new Consignee();
        $port = new Port();
        $vessel = new Vessel();
        $container = new Container();
        $city = new City();
        $country = new Country();
        $currency = new Currency();
        $port = new Port();

        $rs = $dataset['rs'];

        $attachment = $param['attachment'];

        $companyName = $obj->loadSetting('companyName');
        $companyLogo = $obj->loadSetting('companyLogo');

        $rs = $dataset['rs'];
        $attachment = $param['attachment'];
        $shippingType = $param['shippingtype'];

        $html = $obj->printSetting['defaultStyle'];

        $html .= '
                <style>
                    .full-border{ border-bottom:1px solid #0080FF; border-right:1px solid #0080FF; border-left:1px solid #0080FF; } 
                    .border{ border :1px solid #0080FF; }
                    .border-top-right-bottom{
                        border-top :1px solid #0080FF;
                        border-right:1px solid #0080FF;
                        border-bottom:1px solid #0080FF;} 
                    .border-right{ border-right:1px solid #0080FF} 
                    .border-bottom{ border-bottom:1px solid #0080FF} 
                    .head-title{ font-weight:bold; }
                    .border-top-right{
                        border-top :1px solid #0080FF;    
                        border-right:1px solid #0080FF;} 

                        .border-bottom-right{
                            border-bottom :1px solid #0080FF;    
                            border-right:1px solid #0080FF;} 
                    
                            .border-top-bottom{
                                border-bottom :1px solid #0080FF;    
                                border-top:1px solid #0080FF;} 
                    .border-top{border-top :1px solid #0080FF;} 
                    .border-top-left{
                        border-top :1px solid #0080FF;
                        border-left :1px solid #0080FF;} 
                    .border-left{ 
                        border-left :1px solid #0080FF;} 
                    
                    .border-right{ border-right:1px solid #0080FF} 
                    .border-bottom{ border-bottom:1px solid #0080FF}
                    .border-left-top-right-bottom {
                        border-left :1px solid #0080FF;
                        border-top :1px solid #0080FF;
                        border-right:1px solid #0080FF;
                        border-bottom:1px solid #0080FF;} 
                    .border-left-top-right {
                        border-left :1px solid #0080FF;
                        border-top :1px solid #0080FF;
                        border-right:1px solid #0080FF;}
                    .border-left-right-bottom {
                        border-left :1px solid #0080FF;
                        border-right:1px solid #0080FF;
                        border-bottom:1px solid #0080FF;}
                    .font-bold{ font-weight:bold; }
                    .font-color-blue{ color:#0080FF; }
                    .text-center{ text-align:center; }
                </style>';

        //$imgLetterhead = $obj->phpThumbURLSrc . 'setting/companyLogo/' . $companyLogo;
        // $logo = '<img src="' . $imgLetterhead . '" style="height: 80px">';
        //$text = '<span class="font-color-blue font-bold text-center" style="font-size:1.1em;">COMBINED TRANSPORT BILL OF LADING</span><br><br><span class="font-color-blue text-center font-bold" style="font-size:0.8em;">FOR COMBINED TRANSPORT SHIPMENT OR PORT TO PORT SHIPMENT</span>';


        $marksNumberAttachment = htmlspecialchars_decode($rs[0]['marksnumberattachment']);
        if (!empty($rs[0]['description']) || !empty($marksNumberAttachment)) {
            $needAttachment = true; // kondisi ketika perlu attachment
        }

        $marksNumber = '';
        if (!$attachment) {
            $marksNumber = (!empty($rs[0]['marksnumber'])) ? str_replace(chr(13), '<br>', strtoupper(htmlspecialchars_decode($rs[0]['marksnumber']))) : '';
            $marksNumber = '<tr><td>' . $marksNumber . '</td></tr>';
        }

        $heightDesc = ($needAttachment) ? 'height:233px' : 'height:245px';

        if (!$attachment) {
            $attachmentBorder = '';
        } else {
            $attachmentBorder = 'border-bottom:1px solid #333';
            $heightDesc = 'height:480px';
        }

        // $originalOrCopy = '';
        if (!$attachment) {
            if ($arrCopy[$param['originalLabelKey']] == 'Original') {
                $originalOrCopy = '<span style="font-size:8px;">ORIGINAL</span>';
            } else {
                $originalOrCopy = '<span style="font-size:8px;">NON-NEGOTIABLE COPY</span>';
            }
        }

        // if ($shippingType == EMKL['shipping']['sea']) {

        // } else if ($shippingType == EMKL['shipping']['air']) {

        // }

        $heightConsignee = 'height: 90px;';
        $heightPOR = 'height: 25px;';
        $fontSize = 'font-size: 9px;';

        $html .= '<table cellpadding="2" width="100%">
                <tr>
                    <td class="border-left-top-right font-bold font-color-blue" style="width:318px;'. $fontSize .'">SHIPPER</td>
                    <td class="border-top-right" style="width:138px;'. $fontSize .'"></td>
                    <td class="border-top-right font-bold font-color-blue" style="width:220px;'. $fontSize .'">B/L NO.</td>
                </tr>
                <tr>
                    <td rowspan="2" class="border-left-right-bottom" style="width:318px;"></td>
                    <td class="border-right" style="width:138px;"></td>
                    <td class="border-bottom-right text-center font-bold" style="width:220px;height:20px;font-size:14px;"> </td>
                </tr>
                <tr>
                    <td colspan="2" rowspan="2"  class="border-right text-center" style="width:358px;height:80px;"></td>
                    
                </tr>
                <tr>
                    <td class="border-left border-right font-bold font-color-blue" style="width:318px;' . $fontSize . '">CONSIGNEE</td>
                </tr>
                <tr>
                    <td class="border-left-right-bottom" style="width:318px;'.$heightConsignee.'"></td>
                    <td class="border-bottom-right" style="width:358px;"> </td>
                </tr>
            </table>
        ';//shipper, bl no, consignee, logo, text

        $html .= '<table cellpadding="2" width="100%">
                    <tr>
                        <td class="border-left-top-right font-bold font-color-blue" style="width:318px;'. $fontSize .'">NOTIFY PARTY</td>
                        <td class="border-top-right font-bold font-color-blue" style="width:358px;'. $fontSize .'">APPLICATION FOR DELIVERY MUST BE MADE TO:</td>
                    </tr>
                    <tr>
                        <td class="border-left-right-bottom" style="width:318px;'.$heightConsignee.'"></td>
                        <td class="border-bottom-right" style="width:358px;"></td>
                    </tr>
                </table>
            '; //notify party, application form delivery must be made to

        $html .= '<table cellpadding="2" width="100%">
                    <tr>
                        <td class="border-left-top-right font-bold font-color-blue" style="width:318px;'. $fontSize .'">PRE CARRIAGE BY</td>
                        <td class="border-top-right font-bold font-color-blue" style="width:358px;'. $fontSize .'">PLACE OF RECEIPT</td>
                    </tr>
                    <tr>
                        <td class="border-left-right-bottom" style="width:318px;'. $heightPOR.'"></td>
                        <td class="border-bottom-right" style="width:358px;"></td>
                    </tr>
                </table>
            '; //place of receipt, place of delivery

        $html .= '<table cellpadding="2" width="100%">
                    <tr>
                        <td class="border-left-top-right font-bold font-color-blue" style="width:166px;'. $fontSize .'">OCEAN VESSEL</td>
                        <td class="border-left-top-right font-bold font-color-blue" style="width:60px;'. $fontSize .'">VOYAGE</td>
                        <td class="border-top-right font-bold font-color-blue" style="width:225px;'. $fontSize .'">PORT OF LOADING</td>
                        <td class="border-top-right font-bold font-color-blue" style="width:225px;'. $fontSize .'">LOADING PIER (TERMINAL)</td>
                    </tr>
                    <tr>
                        <td class="border-left-right-bottom" style="width:166px;'. $heightPOR.'"></td>
                        <td class="border-left-right-bottom" style="width:60px;'. $heightPOR.'"></td>
                        <td class="border-bottom-right" style="width:225px;"></td>
                        <td class="border-bottom-right" style="width:225px;"></td>
                    </tr>
                </table>
            '; // ocean vessel, voyage, place of receipt, place of delivery

        $html .= '<table cellpadding="2" width="100%">
                    <tr>
                        <td class="border-left-top-right font-bold font-color-blue" style="width:226px;'. $fontSize .'">PORT OF DISCHARGE</td>
                        <td class="border-left-top-right font-bold font-color-blue" style="width:225px;'. $fontSize .'">PLACE OF DELIVERY</td>
                        <td class="border-top-right font-bold font-color-blue" style="width:225px;'. $fontSize .'">TYPE OF MOVE</td>
                    </tr>
                    <tr>
                        <td class="border-left-right-bottom" style="width:226px;'. $heightPOR.'"></td>
                        <td class="border-left-right-bottom" style="width:225px;"></td>
                        <td class="border-bottom-right" style="width:225px;"></td>
                    </tr>
                </table>
            '; // port of discharge, place of delivery, type of move

        $html .= '<table cellpadding="2" width="100%">
                    <tr>
                        <td class="border-left-top-right-bottom font-bold font-color-blue text-center" style="width:676px;' . $fontSize . '">PARTICULARS FURNISHED BY SHIPPER</td>
                    </tr>
                </table>';



        $html .= '<table cellpadding="2" style="' . $attachmentBorder . '" width="100%">
            <tr>
                <td class="border-left border-right font-bold font-color-blue text-center" style="width:113px;' . $fontSize . '">MARKS & NUMBERS<br>CONTAINER/SEAL NO.</td>
                <td class="border-right font-bold font-color-blue text-center" style="width:113px;' . $fontSize . '">NO. OF PACKAGES:</td>
                <td class="border-right font-bold font-color-blue text-center" style="width:225px;' . $fontSize . '">DESCRIPTION PACKAGES AND GOODS</td>
                <td class="border-left border-right font-bold font-color-blue text-center" style="width:112.5px;' . $fontSize . '">GROSS WEIGHT</td>
                <td class="border-right font-bold font-color-blue text-center" style="width:112.5px;' . $fontSize . '">MEASUREMENT</td>
            </tr>';

        if ($attachment) {
            $html .= '<tr><td colspan="5" class="font-color-blue" style="text-align:center;' . $fontSize . '"><b>** CONTINUATION **</b></td></tr>';
        }

        $html.='
            <tr>
                <td rowspan="2" class="border-left border-right font-bold font-color-blue text-center" style="width:113px;'.$heightDesc.'"></td>
                <td rowspan="2" class="border-right font-bold font-color-blue text-center" style="width:113px;'.$heightDesc.'"></td>
                <td rowspan="2" class="border-right font-bold font-color-blue text-center" style="width:225px;'.$heightDesc.'"></td>
                <td rowspan="2" class="border-left border-right font-bold font-color-blue text-center" style="width:112.5px;'.$heightDesc.'"></td>
                <td class="border-right font-bold font-color-blue text-center" style="width:112.5px;' . $heightDesc . '"></td>
            </tr>';

        if(!$attachment){
            $html.='
                <tr>
                    <td class="border-right border-top border-bottom font-bold font-color-blue" style="width:112.5px;">'. $originalOrCopy .'</td>
                </tr>
            ';
        }

        $html.='
        </table>'; // marks & numbers, no of packages, description packages and goods, gross weight, measurement

        return $html;

    };

    $generateFooterTable = function ($dataset, $param) {


        $obj = new EMKLHouseBL();
        $emklJobOrder = new EMKLJobOrder();
        $customer = new Customer();

        $rs = $dataset['rs'];

        $attachment = $param['attachment'];
        $shippingType = $param['shippingtype'];


        $html = $obj->printSetting['defaultStyle'];

        $html .= '
                <style>
                    .full-border{ border-bottom:1px solid #0080FF; border-right:1px solid #0080FF; border-left:1px solid #0080FF; } 
                    .border{ border :1px solid #0080FF; }
                    .border-top-right-bottom{
                        border-top :1px solid #0080FF;
                        border-right:1px solid #0080FF;
                        border-bottom:1px solid #0080FF;} 
                    .border-right{ border-right:1px solid #0080FF} 
                    .border-bottom{ border-bottom:1px solid #0080FF} 
                    .head-title{ font-weight:bold; }
                    .border-top-right{
                        border-top :1px solid #0080FF;    
                        border-right:1px solid #0080FF;} 

                        .border-bottom-right{
                            border-bottom :1px solid #0080FF;    
                            border-right:1px solid #0080FF;} 
                    
                            .border-top-bottom{
                                border-bottom :1px solid #0080FF;    
                                border-top:1px solid #0080FF;} 
                    .border-top{border-top :1px solid #0080FF;} 
                    .border-top-left{
                        border-top :1px solid #0080FF;
                        border-left :1px solid #0080FF;} 
                    .border-left{ 
                        border-left :1px solid #0080FF;} 
                    
                    .border-right{ border-right:1px solid #0080FF} 
                    .border-bottom{ border-bottom:1px solid #0080FF}
                    .border-left-top-right-bottom {
                        border-left :1px solid #0080FF;
                        border-top :1px solid #0080FF;
                        border-right:1px solid #0080FF;
                        border-bottom:1px solid #0080FF;} 
                    .border-left-top-right {
                        border-left :1px solid #0080FF;
                        border-top :1px solid #0080FF;
                        border-right:1px solid #0080FF;}
                    .border-left-right-bottom {
                        border-left :1px solid #0080FF;
                        border-right:1px solid #0080FF;
                        border-bottom:1px solid #0080FF;}
                    .font-bold{ font-weight:bold; }
                    .font-color-blue{ color:#0080FF; }
                    .text-center{ text-align:center; }
                </style>';

        $fontSize = 'font-size: 9px;';

        // if ($shippingType == EMKL['shipping']['sea']) {
        
        // } else if ($shippingType == EMKL['shipping']['air']) {
        
        // }

        $textFooter = '<span class="font-bold">RECEIVED</span>&nbsp;<span>by the Carrier, the goods specified herein, in apparent good order adn condition unless otherwise stated, to be transported to a place as agreed, authorized, or permitted herein. The transportation is subject to all terms and conditions appearing on the front and reserve of this Bill of Lading, witch the Merchant agrees to by accepting this Bill of Lading, notwithstanding any local privileges and customs. The particulars stated herein are as declared by the Shipper. The Carrier does nit acknowledge the accuracy of the weight, measurement, quality, condition, contents, or value of the goods.</span>';
        $textFooter2 = '<br><span class="font-bold font-color-blue">IN WITNESS WHEREOF</span>,&nbsp;<span>one (1) original Bill of Lading has been signed unless otherwise stated above. Upon the accomplishment of the same, any other copies, if issued, shall be void. If required by the Carrier, one (1) duly endorsed Bill of Lading must surrendered in exchange for the goods or a Delivery Order.</span>';
        $textFooter3 = '<br><span class="font-bold font-color-blue">JURISDICTION</span>:&nbsp;<span>The contract evidenced by contained in this Bill of Lading is governed by the laws of indonesia. Any claim or dispute arising hereunder or in connection herewith shall be determined by the courts of indonesia or other component courts as applicable.</span>';

        $html .= '<table cellpadding="2" width="100%">
            <tr>
                <td class="border-left border-top border-right font-bold font-color-blue" style="width:307px;' . $fontSize . '">SHIPPER DECLARED VALUE:</td> 
                <td class="border-right border-top font-bold font-color-blue" style="width:144px;' . $fontSize . '">FREIGHT PAYABLE AT:</td> 
                <td class="border-right border-top font-bold font-color-blue" style="width:225px;' . $fontSize . '">NO OF ORIGINAL BLs</td> 
            </tr>
            <tr>
                <td class="border-left border-right font-bold font-color-blue" style="width:307px;"></td> 
                <td class="border-right font-bold font-color-blue" style="width:144px;"></td> 
                <td class="border-right font-bold font-color-blue" style="width:225px;"></td> 
            </tr>
        </table>'; // shipper declared value, freight payable at, no of original bls

        $html .= '<table cellpadding="2" width="100%">
            <tr>
                <td class="border-left border-right border-top font-bold font-color-blue" style="width:163px;' . $fontSize . '">FREIGHT RATE CHARGES</td> 
                <td class="border-left border-right border-top font-bold font-color-blue" style="width:144px;' . $fontSize . '">PREPAID</td> 
                <td class="border-right border-top font-bold font-color-blue" style="width:144px;' . $fontSize . '">COLLECT</td> 
                <td class="border-right border-top font-bold font-color-blue" style="width:225px;' . $fontSize . '">SHIPPED ON BOARD</td> 
            </tr>
            <tr>
                <td rowspan="3" class="border-left border-right border-bottom font-bold font-color-blue" style="width:163px;"></td> 
                <td rowspan="3" class="border-left border-right border-bottom font-bold font-color-blue" style="width:144px;"></td> 
                <td rowspan="3" class="border-right border-bottom font-bold font-color-blue" style="width:144px;"></td> 
                <td class="border-right font-bold font-color-blue" style="width:225px;"></td> 
            </tr>
            <tr>
                <td class="border-right border-top font-bold font-color-blue" style="width:225px;' . $fontSize . '">PLACE AND DATE OF ISSUE</td> 
            </tr>
            <tr>
                <td class="border-right border-bottom font-bold font-color-blue" style="width:225px;"></td> 
            </tr>
        </table>'; // freight rate charges, prepaid, collect, shipped on board, place and date

        $html .= '<table cellpadding="2" width="100%">
            <tr>
                <td rowspan="2" class="border-left border-right border-bottom font-color-blue" style="width:451px;'. $fontSize .'">'. $textFooter.$textFooter2.$textFooter3.'</td>
                <td class="border-right font-bold font-color-blue" style="width:225px;'. $fontSize .';">SIGNED ON BEHALF OF CARRIER</td>
            </tr>
            <tr>
                <td class="border-right border-bottom font-bold font-color-blue" style="width:225px;"></td>
            </tr>
        </table>';

        return $html;

    };


    $setXYContent = function ($dataset, $param) {

        global $needAttachment;


        $obj = new EMKLHouseBL();
        $emklJobOrder = new EMKLJobOrder();
        $customer = new Customer();
        $consignee = new Consignee();
        $port = new Port();
        $vessel = new Vessel();
        $setting = new Setting();
        $location = new Location();
        $city = new City();
        $country = new Country();
        $currency = new Currency();
        $itemUnit = new ItemUnit();


        $rs = $dataset['rs'];
        $attachment = $param['attachment'];
        $shippingType = $param['shippingtype'];

        $rsData = $obj->getDataRowById($rs[0]['pkey']);
        $rsJobOrder = $emklJobOrder->searchData($emklJobOrder->tableName . '.pkey', $rs[0]['refheaderkey']);
        $rsJODetail = $emklJobOrder->getDetailByColumn('pkey', $rs[0]['refkey']);

        $arrShipper = array();
        if (!empty($rs[0]['shippername']))
            array_push($arrShipper, strtoupper(htmlspecialchars_decode($rs[0]['shippername'])));
        if (!empty($rs[0]['shipperaddress']))
            array_push($arrShipper, str_replace(chr(13), '<br>', strtoupper(htmlspecialchars_decode($rs[0]['shipperaddress']))));

        $arrConsignee = array();
        if (!empty($rs[0]['consigneename']))
            array_push($arrConsignee, strtoupper(htmlspecialchars_decode($rs[0]['consigneename'])));
        if (!empty($rs[0]['consigneeaddress']))
            array_push($arrConsignee, str_replace(chr(13), '<br>', strtoupper(htmlspecialchars_decode($rs[0]['consigneeaddress']))));


        $arrCarrier = array();
        if (!empty($rs[0]['carriername']))
            array_push($arrCarrier, strtoupper(htmlspecialchars_decode($rs[0]['carriername'])));
        if (!empty($rs[0]['carrieraddress']))
            array_push($arrCarrier, str_replace(chr(13), '<br>', strtoupper(htmlspecialchars_decode($rs[0]['carrieraddress']))));

        $placeOfReceipt = $rs[0]['poreceiptname'];
        $placeOfDelivery = $rs[0]['podeliveryname'];

        $feederVessel = $rsJobOrder[0]['feedervesselname'];
        $feederNumber = $rsJobOrder[0]['feedernumber'];

        $POL = $rs[0]['polname'];
        $POD =  $rs[0]['podname'];


        // marks and number hanya muncul di halaman pertama
        $marksNumber = '';
        if (!$attachment) {
            $marksNumber = (!empty($rs[0]['marksnumber'])) ? str_replace(chr(13), '<br>', $rs[0]['marksnumber']) : 'N/M';
            $marksNumber = '<tr><td>' . $marksNumber . '</td></tr>';
        }


        $rsContainer = $emklJobOrder->getDetailContainer($rs[0]['refheaderkey']);

        $arrContainer = array();
        foreach($rsContainer as $container) {

            if (empty($container['containerno']) && empty($container['sealno']) && empty($container['containername'])) {
                continue; 
            }

            array_push($arrContainer, $container['containerno'].'/'.$container['sealno'].'/'.$container['containername']);
        }

        $containerData = implode('<br><br>', $arrContainer);
        $marksNumber .= '<br>'.$containerData;

        $rsItemUnit = $itemUnit->getDataRowById($rs[0]['unitkey']);
        //$noOfPackages = $obj->formatNumber($rs[0]['qty']) . ' ' . $rsItemUnit[0]['name'];
        $noOfPackages = $obj->formatNumber($rs[0]['qty']) . ' ' . $rsItemUnit[0]['name'];
        
        
        if (!$attachment) {
            $netWeight = '';
            //$netWeight = (($rs[0]['netweight'] > 0) ? '<br><br><br>NET WEIGHT ' . $obj->formatNumber($rs[0]['netweight'], 2) . ' KG' : '');
            $description = str_replace(chr(13), '<br>',$rs[0]['shortdescription']);
            $description .= $netWeight;
            // $grossWeight = $obj->formatNumber($rs[0]['weight'], 2) . ' KGS';
            // $measurement = $obj->formatNumber($rs[0]['volume'], 2) . ' CMB';
            $grossWeight = $obj->formatNumber($rs[0]['weight'], 2) . ' KGS';
            $measurement = $obj->formatNumber($rs[0]['volume'], 3) . ' CMB';
        } else {
            $description = str_replace(chr(13), '<br>',$rs[0]['description']);
            $netWeight = '';
            $grossWeight = '';
            $measurement = '';
        }

        $freightPayableAt = 
        $freightPrepaidOrCollect = $rsJODetail[0]['freighttermkey'] == 1 ? 'ORIGIN' : 'DESTINATION';
        $numberOfOriginalBL  = empty($rs[0]['numberoforiginal']) ? '' : $obj ->formatNumber($rs[0]['numberoforiginal']) . ' ('. $obj->sayNumberInEnglish($rs[0]['numberoforiginal']) .')';

        $freightPrepaidOrCollect = $rsJODetail[0]['freighttermkey'] == 1 ? 'FREIGHT PREPAID' : 'FREIGHT COLLECT';

        $shipmentTerm = $rs[0]['shipmenttermname'];
        $shipmentTerm = str_replace(' - ',' / ',$rs[0]['shipmenttermname']);

        $HBLCode = $rs[0]['prefix'] . '' . $rs[0]['code'];
        $companyLogo = $obj->loadSetting('companyLogo');
        $imgLetterhead = $obj->phpThumbURLSrc . 'setting/companyLogo/' . $companyLogo;
        $logo = '<img src="' . $imgLetterhead . '" style="height: 80px">';
        $text = '<span  style="color: #0080FF;font-size:14px;font-weight:bold;text-align:center;font-size:1.1em;">COMBINED TRANSPORT BILL OF LADING</span><br><span style="color: #0080FF;text-align:center;font-weight:bold;font-size:0.8em;">FOR COMBINED TRANSPORT SHIPMENT OR PORT TO PORT SHIPMENT</span>';


        $arrTestXY = array();

        // if ($shippingType == EMKL['shipping']['sea']) {
        // } else if ($shippingType == EMKL['shipping']['air']) {
        // }

        $fontSizeContent = 'font-size:9px;';

        $HBLCodeContent = '<table cellpadding="2">
            <tr>
                <td style="width:220px;font-size:14px;font-weight:bold;text-align:center;">'. $HBLCode .'</td>
            </tr>
        </table>';

        $logoContent = '<table cellpadding="2">
            <td style="width:358px;">'. $logo .'</td>
        </table';

        $textContent = '<table cellpadding="2">
            <td style="width:358px;">'. $text .'</td>
        </table';

        $content = '<table cellpadding="2">
            <tr>
                <td style="'. $fontSizeContent.';width:318px;">' . implode('<br>', $arrShipper) . '</td>
            </tr>
        </table>'; //shipper

        $content2 = '<table cellpadding="2">
                    <tr>
                        <td  style="' . $fontSizeContent . ';width:318px;">' . implode('<br>',$arrConsignee) . '</td>
                    </tr>
                </table>';//consignee content

        $content3 = '<table cellpadding="2">
            <tr>
                <td style="'. $fontSizeContent.';width:318px;">' . implode('<br>', $arrCarrier) . '</td>
            </tr>
        </table>'; //notify party

        $content4 ='<table cellpadding="2">
            <td style="'. $fontSizeContent.';width:358px;"> </td>
        </table>'; //APPLICATION FOR DELIVERY MUST BE MADE TO

        $content5 ='<table cellpadding="2">
            <td style="'. $fontSizeContent.';width:318px;"></td>
        </table>'; //PRE CARRIAGE BY
        $content6 ='<table cellpadding="2">
            <td style="'. $fontSizeContent.';width:358px;">'. strtoupper($placeOfReceipt) .'</td>
        </table>'; //PLACE OF RECEIPT
            
        $content7 = '<table cellpadding="2">
            <td style="' . $fontSizeContent . ';width:166px;">'. $feederVessel .'</td>
        </table>'; //OCEAN VESSEL

        $content8 = '<table cellpadding="2">
            <td style="' . $fontSizeContent . ';width:60px;">'. $feederNumber.'</td>
        </table>'; //VOYAGE

        $content9 = '<table cellpadding="2">
            <td style="' . $fontSizeContent . ';width:225px;">'. $POL .'</td>
        </table>'; //PORT OF LOADING

        $content10 = '<table cellpadding="2">
            <td style="' . $fontSizeContent . ';width:225px;"></td>
        </table>'; //LOADING PIER (TERMINAL)

        $content11 = '<table cellpadding="2">
            <td style="'. $fontSizeContent .'width:226px;">'. $POD .'</td>
        </table>';//PORT OF DISCHARGE

        $content12 = '<table cellpadding="2">
            <td style="'. $fontSizeContent .'width:225px;">'. $placeOfDelivery .'</td>
        </table>';//PLACE OF DELIVERY

        $content13 = '<table cellpadding="2">
            <td style="'. $fontSizeContent .'width:225px;">'. $shipmentTerm .'</td>
        </table>';//TYPE OF MOVE

        $content14 = '<table cellpadding="2">
            <td style="'. $fontSizeContent .'width:225px;width:113">'. $marksNumber .'</td>
        </table>'; //MARKS AND NUMBER CONTAINER/SEAL NO.

        $content15 = '<table cellpadding="2">
            <td style="'. $fontSizeContent .'width:225px;width:113">'. $noOfPackages.'</td>
        </table>'; // NO OF PACKAGES

        $content16 = '<table cellpadding="2">
            <td style="'. $fontSizeContent .'width:225px;width:113">'. $description .'</td>
        </table>'; //DESCRIPTION PACKAGES AND GOODS

        $content17 = '<table cellpadding="2">
            <td style="'. $fontSizeContent .'width:112.5px">'. $grossWeight .'</td>
        </table>'; //GROSS WEIGHT

        $content18 = '<table cellpadding="2">
            <td style="'. $fontSizeContent .'width:112.5px">'. $measurement .'</td>
        </table>'; //MEASUREMENTS

        $content19 = '<table cellpadding="2">
            <td style="'. $fontSizeContent .'width:307px;"> </td>
        </table>';//SHIPPER DECLARED VALUE

        $content20 = '<table cellpadding="2">
            <td style="'. $fontSizeContent .'width:144px;color:red;">'. $freightPayableAt .'</td>
        </table>'; //FREIGHT PAYABLE AT

        $content21 = '<table cellpadding="2">
            <td style="'. $fontSizeContent .'width:225px;">'. strtoupper($numberOfOriginalBL) .'</td>
        </table>'; //NO OF ORIGINAL BLs

        $content22 = '<table cellpadding="2">
            <td style="'. $fontSizeContent .'width:163px;"></td>
        </table>'; //FREIGHT RATE CHARGES

        $content23 =   '<table cellpadding="2">
            <td style="'. $fontSizeContent .'width:144px;">'. $freightPrepaidOrCollect .'</td>
        </table>'; //PREPAID / COLLECT

        $content24 =   '<table cellpadding="2">
            <td style="font-weight:bold;'. $fontSizeContent .'width:225px;">' . strtoupper($obj->formatDBDate($rs[0]['trdate'], 'M . d . Y', array('returnOnEmpty' => true))) . '</td>
        </table>'; //SHIPPEN ON BOARD

        $content25 =   '<table cellpadding="2">
            <td style="'. $fontSizeContent .'width:225px;"><table><tr><td>JAKARTA</td><td style="width:110px;">' . strtoupper($obj->formatDBDate($rs[0]['trdate'], 'M . d . Y', array('returnOnEmpty' => true))) . '</td></tr></table></td>
        </table>'; //PLACE AND DATE OF ISSUE

        
            array_push($arrTestXY,array('x' => 138.5, 'y' => 12, 'content' => $HBLCodeContent));
            array_push($arrTestXY,array('x' => 112, 'y' => 28, 'content' => $logoContent));
            array_push($arrTestXY,array('x' => 100, 'y' => 57, 'content' => $textContent));
            array_push($arrTestXY,array('x' => 10, 'y' => 12, 'content' => $content));
            array_push($arrTestXY,array('x' => 10, 'y' => 47, 'content' => $content2));
            array_push($arrTestXY,array('x' => 10, 'y' => 77, 'content' => $content3));
            array_push($arrTestXY,array('x' => 100.3, 'y' => 77, 'content' => $content4));
            array_push($arrTestXY,array('x' => 10, 'y' => 107, 'content' => $content5));
            array_push($arrTestXY,array('x' => 100.3, 'y' => 107, 'content' => $content6));
            array_push($arrTestXY,array('x' => 10.5, 'y' => 119, 'content' => $content7));
            array_push($arrTestXY,array('x' => 57.3, 'y' =>119, 'content' => $content8));
            array_push($arrTestXY,array('x' => 74.5, 'y' => 119, 'content' => $content9));
            array_push($arrTestXY,array('x' => 138, 'y' => 119, 'content' => $content10));
            array_push($arrTestXY,array('x' => 10.5, 'y' => 130, 'content' => $content11));
            array_push($arrTestXY,array('x' => 74.5, 'y' => 130, 'content' => $content12));
            array_push($arrTestXY, array('x' => 138, 'y' => 130, 'content' => $content13));

        if(!$attachment) { 
            array_push($arrTestXY, array('x' => 10.5, 'y' => 150, 'content' => $content14));
            array_push($arrTestXY, array('x' => 43, 'y' => 150, 'content' => $content15));
            array_push($arrTestXY, array('x' => 74.5, 'y' => 150, 'content' => $content16));
            array_push($arrTestXY, array('x' => 138, 'y' => 150, 'content' => $content17));
            array_push($arrTestXY, array('x' => 170, 'y' => 150, 'content' => $content18));
            array_push($arrTestXY, array('x' => 10.5, 'y' => 226, 'content' => $content19));
            array_push($arrTestXY, array('x' => 97.5, 'y' => 225, 'content' => $content20));
            array_push($arrTestXY, array('x' => 138, 'y' => 225, 'content' => $content21));
            array_push($arrTestXY, array('x' => 10.5, 'y' => 237, 'content' => $content22));


            if($rsJODetail[0]['freighttermkey'] == 1) {
                //prepaid
                $xPrepaidCollect=56.5;
                $yPrepaidCollect=234;
            } else {
                //collect
                $xPrepaidCollect=97.5;
                $yPrepaidCollect=234;
            }
            array_push($arrTestXY, array('x' => $xPrepaidCollect, 'y' => $yPrepaidCollect, 'content' => $content23));
            array_push($arrTestXY, array('x' => 138, 'y' => 234, 'content' => $content24));
            array_push($arrTestXY, array('x' => 138, 'y' => 243, 'content' => $content25));
        } else {
            array_push($arrTestXY, array('x' => 74.5, 'y' => 150, 'content' => $content16));
        }

        if ($rs[0]['isrelease'] == 1 && !$attachment) {
            $myX = 155;
            $myY = 185;

            $surrenderHTML = '<table><tr><td style="width:180px"><div class="surrender" style="color:#f8a0a4; width:10px;  border:1px solid #f8a0a4; font-weight:bold;  font-size: 2em; text-align:center;">SURRENDERED</div></td></tr></table>';
            array_push($arrTestXY, array('x' => $myX, 'y' => $myY, 'content' => $surrenderHTML));
        }

        return $arrTestXY;

    };

    $generateContentStandartConditions = function ($dataset) {

        $obj = new EMKLHouseBL();
        $emklJobOrder = new EMKLJobOrder();
        $customer = new Customer();

        $rs = $dataset['rs'];

        $html = $obj->printSetting['defaultStyle'];

        $html .='<style>
                .font-color-blue{ color:#0080FF; }
                .font-bold{ font-weight:bold; }
                .text-justify{ text-align:justify; }
                .text-center{ text-align:center; }  
        </style>';

        $fontSize = 'font-size:0.63em;';

        $html .='<table cellpadding="2" width="100%">
            <tr>
                <td style="width:338px;">
                    <table width="100%">
                        <tr>
                            <td style="'.$fontSize.'width:338px;" class="font-color-blue font-bold">Standard Conditions (1984) governing FIATA COMBINED TRANSPORT BILLS OF LADING</td>
                        </tr>
                        <tr>
                            <td style="'.$fontSize.'width:338px;" class="font-color-blue font-bold"></td>
                        </tr>

                        <tr>
                            <td style="'.$fontSize.'width:38px;" class="font-color-blue">Definitions</td>
                            <td style="' . $fontSize . 'width:300px;" class="font-color-blue text-justify">"Merchant" means and includes the Shipper, the Consignor, the Consignee, the holder of the Bill of Lading, the Receiver and the Owner of the Goods. “The Freight Forwarder” means the issuer of this Bill of Lading as named on the face of it.</td>
                        </tr>
                        <tr>
                            <td style="'.$fontSize.'width:338px;text-decoration:italic" class="font-color-blue">The headings set forth below are for easy reference only</td>
                        </tr>
                        <tr>
                            <td style="'.$fontSize.'width:338px;" class="font-color-blue font-bold text-center">CONDITIONS</td>
                        </tr>

                        <tr>
                            <td class="font-bold font-color-blue" style="'. $fontSize .'width:15px">1.</td>
                            <td class="font-bold font-color-blue" style="'. $fontSize .'width:323px">Applicability</td>
                        </tr>
                        <tr>
                            <td class="font-color-blue" style="'. $fontSize .'width:15px"></td>
                            <td class="font-color-blue text-justify" style="'. $fontSize .'width:323px">Notwithstanding the heading “Combined Transport Bill of Lading” the provisions set out and referred to in document shall also apply if the transport as described on the face of the Bill of Lading is performed by one mode of transport only.</td>
                        </tr>
                        <tr>
                            <td style="'.$fontSize.'width:338px;" class="font-color-blue font-bold"></td>
                        </tr>

                        <tr>
                            <td class="font-bold font-color-blue" style="'. $fontSize .'width:15px">2.</td>
                            <td class="font-bold font-color-blue" style="'. $fontSize .'width:323px">Issuance of the "Combined Transport Bill of Lading"</td>
                        </tr>
                        <tr>
                            <td class="font-color-blue" style="'. $fontSize .'width:15px">2.1</td>
                            <td class="font-color-blue text-justify" style="'. $fontSize .'width:323px">By the issuance of this “Combined Transport Bill of Lading” the Freight Forwarder
                            a) undertakes to perform and/or in his own name to procure the performance of the entire transport, from the place at which the goods are taken in charge to the place designated for delivery in this Bill of Lading
                            b) assumes liability as set out in these Conditions.
                            </td>
                        </tr>
                        <tr>
                            <td class="font-color-blue" style="'. $fontSize .'width:15px">2.2</td>
                            <td class="font-color-blue text-justify" style="'. $fontSize .'width:323px">For the purposes and subject to the provisions of this Bill of Lading, the Freight Forwarder shall be responsible for the acts and omissions of any person of whose services he makes use for the performance of the contract evidenced by the Bill of Lading.
                            </td>
                        </tr>
                        <tr>
                            <td style="'.$fontSize.'width:338px;" class="font-color-blue font-bold"></td>
                        </tr>

                        <tr>
                            <td class="font-bold font-color-blue" style="'. $fontSize .'width:15px">3.</td>
                            <td class="font-bold font-color-blue" style="'. $fontSize .'width:323px">Negotiability and title to the goods"</td>
                        </tr>
                        <tr>
                            <td class="font-color-blue" style="'. $fontSize .'width:15px">3.1</td>
                            <td class="font-color-blue text-justify" style="'. $fontSize .'width:323px">By accepting this Bill of Lading the Merchant and his transferees agree with the Freight Forwarder that unless it is marked “non-negotiable”, it shall constitute title to the goods and the holder, by endorsement of this Bill of Lading, shall be entitled to receive or to transfer the goods herein mentioned.
                            </td>
                        </tr>
                        <tr>
                            <td class="font-color-blue" style="'. $fontSize .'width:15px">3.2</td>
                            <td class="font-color-blue text-justify" style="'. $fontSize .'width:323px">This Bill of Lading shall be prima facie evidence of the taking in charge by the Freight Forwarder of the goods as herein described. However, proof to the contrary shall be admissible when this Bill of Lading has been negotiated or transferred for valuable consideration to a third party acting in good faith.
                            </td>
                        </tr>
                        <tr>
                            <td style="' . $fontSize . 'width:338px;" class="font-color-blue font-bold"></td>
                        </tr>

                        <tr>
                            <td class="font-bold font-color-blue" style="'. $fontSize .'width:15px">4.</td>
                            <td class="font-bold font-color-blue" style="'. $fontSize .'width:323px">Dangerous Goods and indemnity"</td>
                        </tr>
                        <tr>
                            <td class="font-color-blue" style="'. $fontSize .'width:15px">4.1</td>
                            <td class="font-color-blue text-justify" style="'. $fontSize .'width:323px">The Merchant shall comply with rules which are mandatory according to the national law or by reason of international convention, relating to the carriage of goods of a dangerous nature, and shall in any case inform the Freight Forwarder in writing of the exact nature of the danger, before goods of a dangerous nature are taken in charge by the Freight Forwarder and indicates to him, if need be the precautions to be taken.
                            </td>
                        </tr>
                        <tr>
                            <td class="font-color-blue" style="'. $fontSize .'width:15px">4.2</td>
                            <td class="font-color-blue text-justify" style="'. $fontSize .'width:323px">If the Merchant fails to provide such information and the Freight Forwarder is unaware of the dangerous nature of the goods and the necessary precautions to be taken and if, at any time, they are deemed to be a hazard to life or property, they may at any place be unloaded, destroyed or rendered harmless, as circumstances may require, without compensation, and the Merchant shall be liable for all loss, damage delay or expenses arising out of their being taken in charge, of their carriage, or of any service incidental thereto.
                                                The burden of proving the Freight Forwarder knew the exact nature of the danger constituted by the carriage of the said goods shall rest upon the person entitled to the goods.
                            </td>
                        </tr>
                        <tr>
                            <td class="font-color-blue" style="'. $fontSize .'width:15px">4.3</td>
                            <td class="font-color-blue text-justify" style="'. $fontSize .'width:323px">If any goods shipped with the knowledge of the Freight Forwarder as to their dangerous nature shall become a danger to the vehicle or cargo, they may in like manner be unloaded or landed at any place or destroyed or rendered innocuous by the Freight Forwarder, without liability on the part of the Freight Forwarder except to General Average, if any.</td>
                        </tr>
                        <tr>
                            <td style="'.$fontSize.'width:338px;" class="font-color-blue font-bold"></td>
                        </tr>

                        <tr>
                            <td class="font-bold font-color-blue" style="'. $fontSize .'width:15px">5.</td>
                            <td class="font-bold font-color-blue" style="'. $fontSize .'width:323px">Description of goods and Merchant’s Packing"</td>
                        </tr>
                        <tr>
                            <td class="font-color-blue" style="'. $fontSize .'width:15px">5.1</td>
                            <td class="font-color-blue text-justify" style="'. $fontSize .'width:323px">The consignor shall be deemed to have guaranteed to the Freight Forwarder the accuracy, at the time the goods were taken in charge by the Freight Forwarder, of the description of the goods, marks, number, quantity, weight and/or volume as furnished by him, and the Consignor shall indemnify the Freight Forwarder against all losses, damage and expenses arising or resulting from inaccuracy of or inadequacy of such particulars. The right of the Freight Forwarder to such indemnity shall in no way limit his responsibility and liability under this Bill of Lading to any person other than the Consignor.
                            </td>
                        </tr>
                        <tr>
                            <td class="font-color-blue" style="'. $fontSize .'width:15px">5.2</td>
                            <td class="font-color-blue text-justify" style="'. $fontSize .'width:323px">Without prejudice to Clause 6 (A) (2) (c), the Merchant shall be liable for any loss, damage or injury caused by faulty or insufficient packing of goods or by faulty loading or packing within containers and trailers and on flats when such loading or packing has been performed by the Merchant or on behalf of the Merchant by a person other than the Freight Forwarder, or by the defect or unsuitability of the Containers, trailers, or flats, when supplied by the Merchant, and shall indemnify the Freight Forwarder against any additional expenses so caused.
                            </td>
                        </tr>
                        <tr>
                            <td style="'.$fontSize.'width:338px;" class="font-color-blue font-bold"></td>
                        </tr>

                        <tr>
                            <td class="font-bold font-color-blue" style="'. $fontSize .'width:15px">6.</td>
                            <td class="font-bold font-color-blue" style="'. $fontSize .'width:323px">Extent of liability"</td>
                        </tr>
                        <tr>
                            <td class="font-color-blue" style="'. $fontSize .'width:10px"></td>
                            <td class="font-color-blue text-justify" style="'. $fontSize .'width:10px">A.</td>
                            <td class="font-color-blue text-justify" style="'. $fontSize .'width:10px">1)</td>
                            <td class="font-color-blue text-justify" style="'. $fontSize .'width:308px">The Freight Forwarder shall be liable for loss of or damage to the goods occurring between the time when he takes the goods into his charge and the time of delivery.</td>
                        </tr>
                        <tr>
                            <td class="font-color-blue" style="'. $fontSize .'width:10px"></td>
                            <td class="font-color-blue text-justify" style="'. $fontSize .'width:10px"></td>
                            <td class="font-color-blue text-justify" style="'. $fontSize .'width:10px">2)</td>
                            <td class="font-color-blue text-justify" style="'. $fontSize .'width:308px">The Freight Forwarder shall, however, be relieved of liability for any loss or damage if such loss or damage was caused by :</td>
                        </tr>
                        <tr>
                            <td class="font-color-blue" style="'. $fontSize .'width:10px"></td>
                            <td class="font-color-blue text-justify" style="'. $fontSize .'width:10px"></td>
                            <td class="font-color-blue text-justify" style="'. $fontSize .'width:10px"></td>
                            <td class="font-color-blue text-justify" style="'. $fontSize .'width:10px">a)</td>
                            <td class="font-color-blue text-justify" style="'. $fontSize .'width:298px">an act or omission of the Merchant, or person other than the Freight Forwarder acting on behalf of the Merchant or from whom the Freight Forwarder took the goods in charge.</td>
                        </tr>
                        <tr>
                            <td class="font-color-blue" style="'. $fontSize .'width:10px"></td>
                            <td class="font-color-blue text-justify" style="'. $fontSize .'width:10px"></td>
                            <td class="font-color-blue text-justify" style="'. $fontSize .'width:10px"></td>
                            <td class="font-color-blue text-justify" style="'. $fontSize .'width:10px">a)</td>
                            <td class="font-color-blue text-justify" style="'. $fontSize .'width:298px">an act or omission of the Merchant, or person other than the Freight Forwarder acting on behalf of the Merchant or from whom the Freight Forwarder took the goods in charge.</td>
                        </tr>
                        <tr>
                            <td class="font-color-blue" style="'. $fontSize .'width:10px"></td>
                            <td class="font-color-blue text-justify" style="'. $fontSize .'width:10px"></td>
                            <td class="font-color-blue text-justify" style="'. $fontSize .'width:10px"></td>
                            <td class="font-color-blue text-justify" style="'. $fontSize .'width:10px">b)</td>
                            <td class="font-color-blue text-justify" style="'. $fontSize .'width:298px">insufficiency or defective condition of the packaging or marks and/or numbers.</td>
                        </tr>
                        <tr>
                            <td class="font-color-blue" style="'. $fontSize .'width:10px"></td>
                            <td class="font-color-blue text-justify" style="'. $fontSize .'width:10px"></td>
                            <td class="font-color-blue text-justify" style="'. $fontSize .'width:10px"></td>
                            <td class="font-color-blue text-justify" style="'. $fontSize .'width:10px">c)</td>
                            <td class="font-color-blue text-justify" style="'. $fontSize .'width:298px">handling, loading, stowage or unloading of the goods by the Merchant or any person acting on behalf of the Merchant.</td>
                        </tr>
                        <tr>
                            <td class="font-color-blue" style="'. $fontSize .'width:10px"></td>
                            <td class="font-color-blue text-justify" style="'. $fontSize .'width:10px"></td>
                            <td class="font-color-blue text-justify" style="'. $fontSize .'width:10px"></td>
                            <td class="font-color-blue text-justify" style="'. $fontSize .'width:10px">d)</td>
                            <td class="font-color-blue text-justify" style="'. $fontSize .'width:308px">inherent vice of the goods.</td>
                        </tr>
                        <tr>
                            <td class="font-color-blue" style="'. $fontSize .'width:10px"></td>
                            <td class="font-color-blue text-justify" style="'. $fontSize .'width:10px"></td>
                            <td class="font-color-blue text-justify" style="'. $fontSize .'width:10px"></td>
                            <td class="font-color-blue text-justify" style="'. $fontSize .'width:10px">e)</td>
                            <td class="font-color-blue text-justify" style="'. $fontSize .'width:298px">strike, lockout, stoppage or restraint of labour the consequences of which the Freight Forwarder could not avoid by the exercise of reasonable diligence.</td>
                        </tr>
                        <tr>
                            <td class="font-color-blue" style="'. $fontSize .'width:10px"></td>
                            <td class="font-color-blue text-justify" style="'. $fontSize .'width:10px"></td>
                            <td class="font-color-blue text-justify" style="'. $fontSize .'width:10px"></td>
                            <td class="font-color-blue text-justify" style="'. $fontSize .'width:10px">f)</td>
                            <td class="font-color-blue text-justify" style="'. $fontSize .'width:298px">any cause or event which the Freight Forwarder could not avoid and the consequences whereof he could not prevent by the exercise of reasonable diligence.</td>
                        </tr>
                        <tr>
                            <td class="font-color-blue" style="'. $fontSize .'width:10px"></td>
                            <td class="font-color-blue text-justify" style="'. $fontSize .'width:10px"></td>
                            <td class="font-color-blue text-justify" style="'. $fontSize .'width:10px"></td>
                            <td class="font-color-blue text-justify" style="'. $fontSize .'width:10px">g)</td>
                            <td class="font-color-blue text-justify" style="'. $fontSize .'width:298px">a nuclear incident if the operator of a nuclear installation or a person acting for him is liable for this damage under an applicable International Convention or national law governing liability in respect of nuclear energy.</td>
                        </tr>

                        <tr>
                            <td class="font-color-blue" style="'. $fontSize .'width:10px"></td>
                            <td class="font-color-blue text-justify" style="'. $fontSize .'width:10px"></td>
                            <td class="font-color-blue text-justify" style="'. $fontSize .'width:10px">3.</td>
                            <td class="font-color-blue text-justify" style="'. $fontSize .'width:308px">The burden of proving that the loss or damage was due to one or more of the above causes or events shall rest upon the Freight Forwarder.
                                When the Freight Forwarder establishes that in circumstances of the case, the loss or damage could be attributed to one or more of the causes or events specified in b) to g) above, it shall be presumed that it was so caused. The claimant shall, however, be entitled to prove that the loss or damage was not, in fact, caused wholly or partly by one or more of those causes or events.
                                </td>
                        </tr>

                        <tr>
                            <td class="font-color-blue" style="'. $fontSize .'width:10px"></td>
                            <td class="font-color-blue text-justify" style="'. $fontSize .'width:10px">B.</td>
                            <td class="font-color-blue text-justify" style="'. $fontSize .'width:318px">When in accordance with clause 6A 1 the Freight Forwarder is liable to pay compensation in respect of loss or damage to the goods and the stage of transport where the loss or damage occurred is known, the liability of the Freight Forwarder in respect of such loss or damage shall be determined by the Provisions contained in any International convention or national law, which provisions</td>
                        </tr>
                        <tr>
                            <td class="font-color-blue" style="'. $fontSize .'width:10px"></td>
                            <td class="font-color-blue text-justify" style="'. $fontSize .'width:10px"></td>
                            <td class="font-color-blue text-justify" style="'. $fontSize .'width:10px">(i)</td>
                            <td class="font-color-blue text-justify" style="'. $fontSize .'width:308px">cannot be departed from by private contract, to the detriment of the Claimant, and</td>
                        </tr>
                        
                        <tr>
                            <td class="font-color-blue" style="'. $fontSize .'width:10px"></td>
                            <td class="font-color-blue text-justify" style="'. $fontSize .'width:10px"></td>
                            <td class="font-color-blue text-justify" style="'. $fontSize .'width:10px">(ii)</td>
                            <td class="font-color-blue text-justify" style="'. $fontSize .'width:308px">Would have applied if the Claimant had made a separate and direct contract with the Freight Forwarder in respect of the particular stage of transport where the loss or damage occurred and received as evidence thereof any particular, document which must be issued in order to make such international convention or national law applicable.</td>
                        </tr>
                        <tr>
                            <td style="'.$fontSize.'width:338px;" class="font-color-blue font-bold"></td>
                        </tr>

                        <tr>
                            <td class="font-bold font-color-blue" style="'. $fontSize .'width:10px">7.</td>
                            <td class="font-bold font-color-blue" style="'. $fontSize .'width:328px">Paramount Clause"</td>
                        </tr>
                        <tr>
                            <td class="font-color-blue" style="'. $fontSize .'width:10px"></td>
                            <td class="font-color-blue text-justify" style="'. $fontSize .'width:328px">The Hague Rules contained in the international convention for the unification of certain rules relating to Bill of Lading, date Brussels 25th August 1924, or in those countries where they are already in force the Hague-Visby Rules Contained in the protocol of Brussels, dated February 23rd 1968, as enacted in the Country of Shipment, shall apply to all carriage of goods by sea and, where no mandatory international or national law applies, to the carriage of goods by in land waterways also and such provisions shall apply to all goods whether carried on deck or under deck.
                            </td>
                        </tr>
                        <tr>
                            <td style="'.$fontSize.'width:338px;" class="font-color-blue font-bold"></td>
                        </tr>

                        <tr>
                            <td class="font-bold font-color-blue" style="'. $fontSize .'width:10px">8.</td>
                            <td class="font-bold font-color-blue" style="'. $fontSize .'width:328px">Limitation Amount</td>
                        </tr>
                        <tr>
                            <td class="font-color-blue" style="'. $fontSize .'width:10px">8.1</td>
                            <td class="font-color-blue text-justify" style="'. $fontSize .'width:328px">When the Freight Forwarder is liable for compensation in respect of loss of or damage to the goods, such compensation shall be calculated by reference to the value of such goods at the place and time they are delivered to the Consignee in accordance with the contract or should have been so delivered.
                            </td>
                        </tr>

                        </table>
                        </td>
                        
                        <td style="width:8px"></td>
                        
                        <td style="width:330px;">
                        <table width="100%">

                        <tr>
                            <td class="font-color-blue" style="'. $fontSize .'width:15px">8.2</td>
                            <td class="font-color-blue text-justify" style="'. $fontSize .'width:315px">The value of the goods shall be fixed according to the current commodity exchange price, or, if there be no such price, according to the current market price, or, if there be no commodity exchange price or current market price, by reference to the normal value of goods of the same kind and quality.
                            </td>
                        </tr>
                        <tr>
                            <td class="font-color-blue" style="'. $fontSize .'width:15px">8.3</td>
                            <td class="font-color-blue text-justify" style="'. $fontSize .'width:315px">Compensation shall not however, exceed 2 SDR (Special Drawing Rights) per kilo of gross weight of the goods lost or damaged, unless, with the consent of the Freight Forwarder, the Merchant has declared a higher value for the goods and such higher value has been stated in the OT Bill of Lading, in which case such higher value shall be the limit. However, the Freight Forwarder shall not, in any case, be liable for an amount greater than the actual loss to the person entitled to make the claim.
                            </td>
                        </tr>
                        <tr>
                            <td style="'.$fontSize.'width:330px;" class="font-color-blue font-bold"></td>
                        </tr>

                        <tr>
                            <td class="font-bold font-color-blue" style="'. $fontSize .'width:15px">9.</td>
                            <td class="font-bold font-color-blue" style="'. $fontSize .'width:315px">Delay Consequential Loss, etc.</td>
                        </tr>
                        <tr>
                            <td class="font-color-blue" style="'. $fontSize .'width:15px"></td>
                            <td class="font-color-blue text-justify" style="'. $fontSize .'width:315px">Arrival times are not guaranteed by the Freight Forwarder if the Freight Forwarder is held liable in respect of delay, consequential loss or damage other than loss of or damage to the goods, the liability of the Freight Forwarder shall be limited to double the freight for the transport covered by Bill of Lading, or the value of the goods as determined in Clause 8, Whichever is the less.
                            </td>
                        </tr>
                        <tr>
                            <td style="'.$fontSize.'width:330px;" class="font-color-blue font-bold"></td>
                        </tr>

                        <tr>
                            <td class="font-bold font-color-blue" style="'. $fontSize .'width:15px">10.</td>
                            <td class="font-bold font-color-blue" style="'. $fontSize .'width:315px">Defences</td>
                        </tr>
                        <tr>
                            <td class="font-color-blue" style="'. $fontSize .'width:18px"></td>
                            <td class="font-color-blue" style="'. $fontSize .'width:18px">10.1</td>
                            <td class="font-color-blue text-justify" style="'. $fontSize .'width:294px">The defences and liability limits provided for in these Conditions shall apply in any action against the Freight Forwarder for loss of or damage or delay to the goods whether the action be founded in contract or in tort.
                            </td>
                        </tr>
                        <tr>
                            <td class="font-color-blue" style="'. $fontSize .'width:18px"></td>
                            <td class="font-color-blue" style="'. $fontSize .'width:18px">10.2</td>
                            <td class="font-color-blue text-justify" style="'. $fontSize .'width:294px">The Freight Forwarder shall not be entitled to the benefit of the limitation of liability provided for in paragraph 3 of Clause 8 if it is proved that the loss or damage resulted from an act or omission of the Freight Forwarder done with intent to cause damage or recklessly and with knowledge that damage would probably result.
                            </td>
                        </tr>
                        <tr>
                            <td style="'.$fontSize.'width:330px;" class="font-color-blue font-bold"></td>
                        </tr>

                        <tr>
                            <td class="font-bold font-color-blue" style="'. $fontSize .'width:15px">11.</td>
                            <td class="font-bold font-color-blue" style="'. $fontSize .'width:315px">Liability of Servants and Sub-contractors</td>
                        </tr>
                        <tr>
                            <td class="font-color-blue" style="'. $fontSize .'width:18px"></td>
                            <td class="font-color-blue" style="'. $fontSize .'width:18px">11.1</td>
                            <td class="font-color-blue text-justify" style="'. $fontSize .'width:294px">If an action for loss of or damage to the goods is brought against a person referred to in paragraph 2 of Clause 2, such person shall be entitled to avail himself of the defences and limits of liability which the Freight Forwarder is entitled to invoke under these Conditions.
                            </td>
                        </tr>
                        <tr>
                            <td class="font-color-blue" style="'. $fontSize .'width:18px"></td>
                            <td class="font-color-blue" style="'. $fontSize .'width:18px">11.2</td>
                            <td class="font-color-blue text-justify" style="'. $fontSize .'width:294px">Subject to the provisions of paragraph 2 of Clause 10 and paragraph 2 of this Clause the aggregate of the amounts recoverable from the Freight Forwarder and the person referred to in paragraph 2 of Clause 2 shall in no case exceed the limits provided for in these Conditions.
                            </td>
                        </tr>
                        <tr>
                            <td style="'.$fontSize.'width:330px;" class="font-color-blue font-bold"></td>
                        </tr>

                        <tr>
                            <td class="font-bold font-color-blue" style="'. $fontSize .'width:15px">12.</td>
                            <td class="font-bold font-color-blue" style="'. $fontSize .'width:315px">Method and Route of Transportation</td>
                        </tr>
                        <tr>
                            <td class="font-bold font-color-blue" style="'. $fontSize .'width:15px"></td>
                            <td class="font-color-blue text-justify" style="'. $fontSize .'width:315px">The Freight Forwarder reserves to himself a reasonable liberty as to the means, route and procedure to be followed in the handling, storage and transportation of goods.</td>
                        </tr>
                        <tr>
                            <td style="'.$fontSize.'width:330px;" class="font-color-blue font-bold"></td>
                        </tr>

                        <tr>
                            <td class="font-bold font-color-blue" style="'. $fontSize .'width:15px">13.</td>
                            <td class="font-bold font-color-blue" style="'. $fontSize .'width:315px">Delivery</td>
                        </tr>
                        <tr>
                            <td class="font-bold font-color-blue" style="'. $fontSize .'width:15px"></td>
                            <td class="font-color-blue text-justify" style="'. $fontSize .'width:315px">If delivery of the goods or any part thereof is not taken by the Merchant, at the time and place when and where the Freight Forwarder is entitled to call upon the Merchant to take delivery thereof, the Freight Forwarder shall be entitled to store the goods or the part thereof at the sole risk of the Merchant, whereupon the liability of the Freight Forwarder in respect of the goods or that part thereof stored as aforesaid (as the case may be) shall wholly cease and the cost of such storage (if paid by or payable by the Freight Forwarder or any agent or sub-contractor of the Freight Forwarder) shall forthwith upon demand be paid by the Merchant to the Freight Forwarder.</td>
                        </tr>
                        <tr>
                            <td style="'.$fontSize.'width:330px;" class="font-color-blue font-bold"></td>
                        </tr>

                        <tr>
                            <td class="font-bold font-color-blue" style="'. $fontSize .'width:15px">14.</td>
                            <td class="font-bold font-color-blue" style="'. $fontSize .'width:315px">Freight and Charges</td>
                        </tr>
                        <tr>
                            <td class="font-color-blue" style="' . $fontSize . 'width:18px"></td>
                            <td class="font-color-blue" style="' . $fontSize . 'width:18px">14.1</td>
                            <td class="font-color-blue text-justify" style="' . $fontSize . 'width:294px">Freight shall be paid in cash without discount and, whether prepayable or payable at destination shall be considered as earned on receipt of the goods and not to be returned or relinquished in any event.
                            </td>
                        </tr>
                        <tr>
                            <td class="font-color-blue" style="' . $fontSize . 'width:18px"></td>
                            <td class="font-color-blue" style="' . $fontSize . 'width:18px">14.2</td>
                            <td class="font-color-blue text-justify" style="' . $fontSize . 'width:294px">Freight and all other amounts mentioned in this Bill of Lading are to be paid in the currency named in the Bill of Lading or, at the Freight Forwarder’s option in the currency of the country of dispatch or destination at the highest rate of exchange (or bankers sight bills current for prepayable freight on the day of dispatch) and for freight payable at destination on the day when the Merchant is notified of arrival of the goods there or on the date of withdrawal of the goods by the Merchant, whose rate is the higher, or at the option of the Freight Forwarder on the date of the Bill of Lading.
                            </td>
                        </tr>
                        <tr>
                            <td class="font-color-blue" style="' . $fontSize . 'width:18px"></td>
                            <td class="font-color-blue" style="' . $fontSize . 'width:18px">14.3</td>
                            <td class="font-color-blue text-justify" style="' . $fontSize . 'width:294px">All dues, taxes and charges or other expenses in connection with the goods shall be paid by the Merchant.
                            </td>
                        </tr>
                        <tr>
                            <td class="font-color-blue" style="' . $fontSize . 'width:18px"></td>
                            <td class="font-color-blue" style="' . $fontSize . 'width:18px">14.4</td>
                            <td class="font-color-blue text-justify" style="' . $fontSize . 'width:294px">The Merchant shall reimburse the Freight Forwarder in proportion to the amount of freight for any costs for deviation or delay or any other increase of costs of whatever nature caused by war, warlike operations, epidemics, strikes, government directions or force majeure.
                            </td>
                        </tr>
                        <tr>
                            <td class="font-color-blue" style="' . $fontSize . 'width:18px"></td>
                            <td class="font-color-blue" style="' . $fontSize . 'width:18px">14.5</td>
                            <td class="font-color-blue text-justify" style="' . $fontSize . 'width:294px">The Merchant warrants the correctness of the declaration of contents, insurance, weight measurements or value of the goods but the Freight Forwarder reserves the right to have the contents inspected and the weight measurements or value verified on such inspection if it is found the declaration is not correct it is agreed that a sum equal either to five times the correct difference between the correct figure and the freight charged, or double the correct freight for the whole consignment, whichever sum is the smaller, shall be payable as liquidated damage to the Freight Forwarder for his inspection costs and losses of freight on other goods notwithstanding any other sum having been stated on the Bill of Lading as freight payable.
                            </td>
                        </tr>
                        <tr>
                            <td style="' . $fontSize . 'width:330px;" class="font-color-blue font-bold"></td>
                        </tr>

                        <tr>
                            <td class="font-bold font-color-blue" style="'. $fontSize .'width:15px">15.</td>
                            <td class="font-bold font-color-blue" style="'. $fontSize .'width:315px">Lien</td>
                        </tr>
                        <tr>
                            <td class="font-bold font-color-blue" style="'. $fontSize .'width:15px"></td>
                            <td class="font-color-blue text-justify" style="'. $fontSize .'width:315px">The Freight Forwarder shall have a lien on the goods for any amount due under this Bill of Lading including storage fees and for the cost of recovering same, and may enforce such lien in any reasonable manner which he may think fit.</td>
                        </tr>
                        <tr>
                            <td style="'.$fontSize.'width:330px;" class="font-color-blue font-bold"></td>
                        </tr>

                        <tr>
                            <td class="font-bold font-color-blue" style="'. $fontSize .'width:15px">16.</td>
                            <td class="font-bold font-color-blue" style="'. $fontSize .'width:315px">General Average</td>
                        </tr>
                        <tr>
                            <td class="font-bold font-color-blue" style="'. $fontSize .'width:15px"></td>
                            <td class="font-color-blue text-justify" style="'. $fontSize .'width:315px">The Merchant shall indemnify the Freight Forwarder in respect of any claims of a General Average nature which may be made on him and shall provide such security as may be required by the Freight Forwarder in this connection.</td>
                        </tr>
                        <tr>
                            <td style="'.$fontSize.'width:338px;" class="font-color-blue font-bold"></td>
                        </tr>

                        <tr>
                            <td class="font-bold font-color-blue" style="'. $fontSize .'width:15px">17.</td>
                            <td class="font-bold font-color-blue" style="'. $fontSize .'width:315px">General Average</td>
                        </tr>
                        <tr>
                            <td class="font-bold font-color-blue" style="'. $fontSize .'width:15px"></td>
                            <td class="font-color-blue text-justify" style="'. $fontSize .'width:315px">Unless notice of or damage to the goods and the general nature of it be given in writing to the Freight Forwarder or the persons referred to in paragraph 2 of Clause 2, at the place of delivery before or at the time of the goods into the custody of the person entitled to delivery under this Bill of Lading, or if the loss or damage be not apparent, within seven consecutive days thereafter, such removal shall be prima facie evidence of the delivery by the Freight Forwarder of the goods as described in this Bill of Lading.</td>
                        </tr>
                        <tr>
                            <td style="'.$fontSize.'width:338px;" class="font-color-blue font-bold"></td>
                        </tr>

                        <tr>
                            <td class="font-bold font-color-blue" style="' . $fontSize . 'width:15px">18.</td>
                            <td class="font-bold font-color-blue" style="' . $fontSize . 'width:315px">Non delivery</td>
                        </tr>
                        <tr>
                            <td class="font-bold font-color-blue" style="' . $fontSize . 'width:15px"></td>
                            <td class="font-color-blue text-justify" style="' . $fontSize . 'width:315px">Failure to effect delivery within 90 days after the expiry of a time limit agreed and expressed in a B or Bill of Lading or, where no time limit is agreed and so expressed, failure to effect delivery within 90 days after the time when it would be reasonable to allow for diligent completion of the combined transport operation shall, in the absence of evidence to the contrary give to the party entitled to receive delivery, the right to treat the goods as lost.</td>
                        </tr> 
                        <tr>
                            <td style="'.$fontSize.'width:338px;" class="font-color-blue font-bold"></td>
                        </tr>

                        <tr>
                            <td class="font-bold font-color-blue" style="' . $fontSize . 'width:15px">19.</td>
                            <td class="font-bold font-color-blue" style="' . $fontSize . 'width:315px">Time Bar</td>
                        </tr>
                        <tr>
                            <td class="font-bold font-color-blue" style="' . $fontSize . 'width:15px"></td>
                            <td class="font-color-blue text-justify" style="' . $fontSize . 'width:315px">The Freight Forwarder shall be discharged of all liability under the rules of these conditions, unless suit is brought within nine months after</td>
                        </tr> 
                        <tr>
                            <td class="font-bold font-color-blue" style="' . $fontSize . 'width:15px"></td>
                            <td class="font-color-blue" style="' . $fontSize . 'width:15px">(i)</td>
                            <td class="font-color-blue text-justify" style="' . $fontSize . 'width:300px">the delivery of the goods or</td>
                        </tr> 
                        <tr>
                            <td class="font-bold font-color-blue" style="' . $fontSize . 'width:15px"></td>
                            <td class="font-color-blue" style="' . $fontSize . 'width:15px">(ii)</td>
                            <td class="font-color-blue text-justify" style="' . $fontSize . 'width:300px">the date when the goods should have been delivered, or</td>
                        </tr> 
                        <tr>
                            <td class="font-bold font-color-blue" style="' . $fontSize . 'width:15px"></td>
                            <td class="font-color-blue" style="' . $fontSize . 'width:15px">(iii)</td>
                            <td class="font-color-blue text-justify" style="' . $fontSize . 'width:300px">(iii)	the date when in accordance with clause 18, failure to deliver the goods would, in the absence of evidence to the contrary, give to the party entitled to receive delivery the right to treat the goods as lost.</td>
                        </tr> 
                        <tr>
                            <td style="'.$fontSize.'width:338px;" class="font-color-blue font-bold"></td>
                        </tr>

                        <tr>
                            <td class="font-bold font-color-blue" style="' . $fontSize . 'width:15px">20.</td>
                            <td class="font-bold font-color-blue" style="' . $fontSize . 'width:315px">Jurisdiction</td>
                        </tr>
                        <tr>
                            <td class="font-bold font-color-blue" style="' . $fontSize . 'width:15px"></td>
                            <td class="font-color-blue text-justify" style="' . $fontSize . 'width:315px">Actions against the Freight Forwarder may only be instituted in the country where the Freight Forwarder has his principal place of business and shall be decided according to the law of such country.</td>
                        </tr> 
                        <tr>
                            <td style="'.$fontSize.'width:338px;" class="font-color-blue font-bold"></td>
                        </tr>

                    </table>

                </td>
            </tr>
      
        </table>';

        return $html;

    };  

    $obj = new EMKLHouseBL();
    $emklJobOrder = new EMKLJobOrder();

    $rs = $dataset['rs'];

    $rsJobOrder = $emklJobOrder->getDataRowById($rs[0]['refheaderkey']);
    $shippingtype = $rsJobOrder[0]['transportationtypekey'];

    $returnHTML = array();
    //foreach ($arrCopy as $index => $label) {

        $needAttachment = false;
        $index = 0;

        $arrTestXY = $setXYContent($dataset, array('attachment' => false, 'originalLabelKey' => $index, 'shippingtype' => $shippingtype));
        $html = $generateHeaderTable($dataset, array('attachment' => false, 'originalLabelKey' => $index, 'shippingtype' => $shippingtype));
        $html .= ($needAttachment) ? '<div style="width:680px; text-align:center;font-size:9px;color:#0080FF"><b>** TO BE CONTINUED ON ATTACHED LIST **</b></div>' : '';
        $html .= $generateFooterTable($dataset, array('attachment' => false, 'originalLabelKey' => $index, 'shippingtype' => $shippingtype));
        array_push($returnHTML, array('html' => $html, 'content' => $arrTestXY, 'shippingtype' => $shippingtype));

        if ($needAttachment) {
            $html = $generateHeaderTable($dataset, array('attachment' => true, 'shippingtype' => $shippingtype));
            $arrTestXY = $setXYContent($dataset, array('attachment' => true, 'originalLabelKey' => $index, 'shippingtype' => $shippingtype));
            array_push($returnHTML, array('html' => $html, 'content' => $arrTestXY, 'shippingtype' => $shippingtype));
        }

        $standartConditions = $generateContentStandartConditions($dataset);
        array_push($returnHTML, array('html' => $standartConditions, 'content' => ''));

    //}
   
    return $returnHTML;
};

$generateReportContent = array();
array_push($generateReportContent, array('content' => $content));


?>