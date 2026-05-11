<?php
// $PRINT_SETTINGS = array(
//     'showPrintHeader' => false,
//     'footer' => '',
//     'pdfMarginHeader' => 8,
//     'marginFooter' => 0
// );
//'<div>Page '.$pdf->getAliasNumPage().' of '.$pdf->getAliasNbPages().'</div>'
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

$arrContainers = array();
// $arrCopy = array('Original');
// $arrCopy = array('Original', 'Non-Negotiable Copy');
$needAttachment = false;
///$containerIndex = 0;

$obj = $emklHBL;

$content = function ($dataset) {
    global $needAttachment;
    global $arrCopy;

    $attachmentPages = array();

    $generateHeaderTable = function ($dataset, $param) {

        global $pdf;
        global $arrContainers;
        //global $containerIndex;
        global $needAttachment;
        global $paperSetting;
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


        $logoImg = '';// $obj->loadSetting('companyLogo');
        $imgLetterhead = '';// $obj->phpThumbURLSrc . 'setting/companyLogo/' . $logoImg;

        $rs = $dataset['rs'];
        $attachment = $param['attachment'];
        $shippingType = $param['shippingtype'];
        
        $isShowTitle = $param['showTitle'];
        $isShowBorder = $param['showBorder'];
        
        $showTitle = '';
        if($isShowTitle == 0) {
            $showTitle = 'display:none;'; 
        }
        
        $rsJobOrder = $emklJobOrder->searchData($emklJobOrder->tableName . '.pkey', $rs[0]['refheaderkey']);

        $marksNumberAttachment = htmlspecialchars_decode($rs[0]['marksnumberattachment']);
        if (!empty($rs[0]['description']) || !empty($marksNumberAttachment)) {
            $needAttachment = true; // kondisi ketika perlu attachment
        }

        // marks and number hanya muncul di halaman pertama
        $marksNumber = '';
        if (!$attachment) {
            $marksNumber = (!empty($rs[0]['marksnumber'])) ? str_replace(chr(13), '<br>', strtoupper(htmlspecialchars_decode($rs[0]['marksnumber']))) : '';
            $marksNumber = '<tr><td>' . $marksNumber . '</td></tr>';
        }


        // $party = '';
        // if (in_array($rsJobOrder[0]['loadcontainertypekey'], array(EMKL['container']['fcl'], EMKL['container']['trucking'])) && $rsJobOrder[0]['transportationtypekey'] == EMKL['shipping']['sea']) {
        //     $arrParty = array();
        //     $rsParty = $emklJobOrder->getDetailVolume($rsJobOrder[0]['pkey']);
        //     for ($i = 0; $i < count($rsParty); $i++)
        //         array_push($arrParty, $obj->formatNumber($rsParty[$i]['qty']) . ' x ' . $rsParty[$i]['itemname']);
        //     $party = implode(', ', $arrParty);
        // }

        if($shippingType == EMKL['shipping']['sea']) {
            
            //type sea
            $heightDesc = ($needAttachment) ? 'height:170px' : 'height:155px';

            if (!$attachment) {
                $attachmentBorder = '';
                //$heightDesc = 'height:210px';
            } else {
                $attachmentBorder = 'border-bottom:1px solid #333';
                $heightDesc = 'height:450px';
            }

        }else if($shippingType == EMKL['shipping']['air']) {

            //type air
            $heightDesc = ($needAttachment) ? 'height:128px' : 'height:127px';
    
            if (!$attachment) {
                // untuk jenis halaman pertama
                // $description = $lclFclDescription . '<br>' . strtoupper($rs[0]['shortdescription']);
                $attachmentBorder = '';
            } else {
                // untuk jenis attachment
                // $description = $lclFclDescription . '<br>' . strtoupper($rs[0]['description']);
                $attachmentBorder = 'border-bottom:1px solid #333';
                $heightDesc = 'height:408px';
            }
        }


        $html = $obj->printSetting['defaultStyle'];

        
        $originalOrCopy = '';
        if (!$attachment) {
            if ($arrCopy[$param['originalLabelKey']] == 'Original') {
                //$originalOrCopy = '<span style="font-size:2.4em;font-weight:bold;text-stroke:red">ORIGINAL</span>';
                $originalOrCopy = '<span style="font-size:2.4em;font-weight:bold;text-stroke:red"></span>';
            } else {
                //$originalOrCopy = '<br><span style="font-size:1.7em;font-weight:bold;text-stroke:red">COPY</span><br><span style="font-weight:bold">NON-NEGOTIABLE</span>';
                $originalOrCopy = '<br><span style="font-size:1.7em;font-weight:bold;text-stroke:red"> </span><br><span style="font-weight:bold"> </span>';
            }
        } 
        
        if ($isShowBorder == 1) {
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
        }
        

        
        if($shippingType == EMKL['shipping']['sea']) {
        
            //transportation type sea / ocean

            if ($isShowTitle == 1) {
                $logo = '<img src="' . $imgLetterhead . '" style="height:70px">';
    
                $companyName = "<h2>CHINA INT'L FREIGHT CO., LTD</h2>";
                $hblType = '<h1 style="text-align:center;font-size:22px;">OCEAN BILL OF LADING</h1>';
        
                $text = '<p style="font-size:0.9em;text-align:justify;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;RECEIVED in apparent good order and condition except as otherwise noted the total number of containers or other packages or units enumerated below for transportation from the place of receipt to the place of delivery subject to the terms hereof.</p>';
                $text2 = '<p style="font-size:0.9em;text-align:justify;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;One of signed bills of lading must be surrendered duly endorsed in Exchange for the Goods or delivery order On presentation of this document (duly endorsed) to the Delivery agent by the Horder the rights and liabilities arising in accordance with the terms here for shall (without prejudice to any rule of common law or statute rendering them binding on the Merchant) become binding in all respect the Carrier and the Holder as thought the contract evidenced hereby had been made between them.</p>';
                $text3 = '<p style="font-size:0.9em;text-align:justify;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;IN WITNESS Where of this number of original Bill of Lading stated below all of tenor and date on of wich accomplished the others to stand void.</p>';
            }

            if (!$attachment) {

                $html .= '
                        <table cellpadding="2"> 
                            <tr>
                                <td class="border-top border-right" style="font-size:0.9em;width:338px;"><span style="' . $showTitle . '">Shipper/Exporter (complete name and address)</span></td>
                                <td class="" rowspan="4" style="font-size:0.9em;width:150px;text-align:center"><span style="' . $showTitle . '">' . $logo . '</span></td>
                                <td class="border-top border-left border-right border-bottom" rowspan="3" style="font-size:0.9em;width:188px;">
                                <span style="' . $showTitle . '">Bill of Lading No.</span>
                                </td>
                            </tr>

                            <tr>
                                <td class="border-right border-bottom" rowspan="4" style="height:10px"></td>
                                <td class="" style="height:10px"></td>
                            </tr>
                            <tr>
                                <td class="" style="height:10px"></td>
                            </tr>
                            <tr>
                                <td class=" " style="height:26px"> </td>
                            </tr>
                            <tr>
                                <td class="" style="height:26px;text-align:center;" colspan="2">' . $companyName . '</td>
                            </tr>

                            <tr>
                                <td class="border-top border-right" style="font-size:0.9em;width:338px;"><span style="' . $showTitle . '">Consignee (complete name and address)</span></td>
                                <td class="" rowspan="7" style="width:338px;height:225px">' . $hblType . '<br>
                                <table>
                                    <tr>
                                        <td>' . $text . '</td>
                                    </tr>
                                    <tr>
                                        <td>' . $text2 . '</td>
                                    </tr>
                                    <tr>
                                        <td>' . $text3 . '</td>
                                    </tr>
                                </table>
                                </td>
                            </tr>
                            <tr>
                                <td class="border-right border-bottom" style="height:75px"></td>
                            </tr>
                            <tr>
                                <td class="border-top border-right" style="font-size:0.9em;"><span style="' . $showTitle . '">Notify Party (complete name and address)</span></td>
                            </tr>
                            <tr>
                                <td class="border-right border-bottom" style="height:80px"></td>
                            </tr>
                            <tr>
                                <td class="border-top border-right-dotted" style="font-size:0.9em;width:169px;"><span style="' . $showTitle . '">Place of receipt</span></td>
                                <td class="border-top border-right" style="font-size:0.9em;width:169px;"><span style="' . $showTitle . '">Port of loading</span></td>
                            </tr>
                            <tr>
                                <td class="border-right-dotted border-bottom" ></td>
                                <td class="border-right" ></td>
                            </tr>
                            <tr>
                                <td class="border-top border-right-dotted" style="font-size:0.9em;width:169px;"><span style="' . $showTitle . '">Vessel</span></td>
                                <td class="border-top border-right" style="font-size:0.9em;width:169px;"><span style="' . $showTitle . '">Voyage</span></td>
                            </tr>
                            <tr>
                                <td class="border-right-dotted border-bottom"></td>
                                <td class="border-right border-bottom"></td>
                            </tr>
                            <tr>
                                <td class="border-top border-right-dotted" style="font-size:0.9em;width:169px;"><span style="' . $showTitle . '">Port of discharge</span></td>
                                <td class="border-top border-right" style="font-size:0.9em;width:169px;"><span style="' . $showTitle . '">Place of delivery</span></td>
                                <td class="border-top" style="font-size:0.9em;width:338px;"><span style="' . $showTitle . '">Final destination (for the merchant`s reference)</span></td>
                            </tr>
                            <tr>
                                <td class="border-right-dotted border-bottom"></td>
                                <td class=" border-bottom border-right"></td>
                                <td class="border-bottom"></td>
                            </tr>
                        </table>
                    ';

                    $html .= '
                        <table cellpadding="4">
                            <tr>
                                <td class="border-bottom" style="width:676px;text-align:center;"><span style="' . $showTitle . '"><b>PARTICULARS FURNISHED BY SHIPPER </b></span></td>
                            </tr>
                        </table>
                    ';


                    $html .= '
                        <table cellpadding="2" style="' . $attachmentBorder . '">
                            <tr>
                                <td class="border-bottom border-right" style="font-size:0.9em;text-align:center;width:160px"><span style="' . $showTitle . '">MKS & NOS/CONTAINER NOS</span></td>
                                <td class="border-bottom border-right" style="font-size:0.9em;text-align:center;width:90px"><span style="' . $showTitle . '">NO. OF PKGS</span></td>
                                <td class="border-bottom border-right" style="font-size:0.9em;text-align:center;width:247px"><span style="' . $showTitle . '">DESCRIPTION OF PACKAGES AND GOODS</span></td>
                                <td class="border-bottom border-right" style="font-size:0.9em;text-align:center;width:90px"><span style="' . $showTitle . '">GROSS WEIGHT</span></td>
                                <td class="border-bottom" style="font-size:0.9em;text-align:center;width:90px"><span style="' . $showTitle . '">MEASUREMENT</span></td>
                            </tr>
                    ';

                    if ($attachment) {
                        $html .= '<tr><td colspan="4" style="text-align:center"><b>** CONTINUATION **</b></td></tr>';
                    }

                    $html .='
                        <tr>
                            <td class="border-right" style="' . $heightDesc . '"></td>
                            <td class="border-right" style="' . $heightDesc . '"></td>
                            <td class="border-right" style="' . $heightDesc . '"></td>
                            <td class="border-right" style="' . $heightDesc . '"></td>
                            <td class="" style="' . $heightDesc . '"></td>
                        </tr>
                        
                        <tr>
                            <td class="border-right"></td>
                            <td class="border-right"></td>
                            <td class="border-right"></td>
                        ';
                    if(!$attachment) {
                        $html .='
                                <td class="" colspan="2" style="text-align:center">'. $originalOrCopy .'</td>
                            </tr>
                        ';
                    } else {
                            $html .='
                                <td class="border-right" colspan="" style="text-align:center"></td>
                            </tr>
                        ';
                    }

                    // if (!$needAttachment) {
                    //     $html .= '<tr><td class="border-right"></td><td class="border-right"></td><td class="border-right"></td><td class="border-right"></td></tr>';
                    // }

                    $html .='
                        </table>
                    ';

            } else {

                $html .= '<table cellpadding="2">
                            <tr>
                                <td style="border-bottom:1.5px solid black;font-weight:bold;width:160px">ATTACHMENT FOR B/L NO.</td>
                                <td style="border-bottom:1.5px solid black;width:10px;text-align:center;font-weight:bold">:</td>
                                <td style="border-bottom:1.5px solid black;font-weight:bold;width:510px; text-align:right">Page : '. $pdf->getAliasNumPage().'</td>
                            </tr>
                        </table>';

                $html .= '<table cellpadding="3">
                            <tr>
                                <td style="width:40px;"></td>
                                <td style="width:210px;font-weight:bold;border-bottom:1.5px solid black;">*** MARKS & NOS/CONTAINER NO ***</td>
                                <td style="width:40px;"></td>
                                <td style="width:34px;font-weight:bold;border-bottom:1.5px solid black;">******</td>
                                <td style="width:220px;font-weight:bold;border-bottom:1.5px solid black;text-align:center;">DESCRIPTION OF GOODS</td>
                                <td style="width:34px;font-weight:bold;border-bottom:1.5px solid black;">******</td>
                            </tr>
                        </table>';

            }

        }else if($shippingType == EMKL['shipping']['air']) {

            if($isShowTitle == 1) { 
                $companyNameAndAddress = '<span style="font-size:18px;font-weight:bold;">CHINA INT`L FREIGHT CO., LTD</span>
                                <br><span style="font-weight:bold;font-size:12px">NOT NEGOTIABLE AIR WAY BILL</span>
                                <br><span style="font-size:10px">(AIR CONSIGNMENT NOTE) ISSUED BY</span>
                                <br><span style="font-size:12px;font-weight:bold;">' . strtoupper($obj->loadSetting('companyName')) . '</span>
                                <br><br><span style="">' . nl2br($obj->loadSetting('companyAddress')) .'</span>';
                $logo = '<img src="' . $imgLetterhead . '" style="height:90px">';

                $companyName = $obj->loadSetting('companyName');
            }

            $html .= '
                <table cellpadding="2"> 
                    <tr>
                        <td class="border-left border-top-right" style="font-size:0.9em;width:198px;text-align:center"><span style="'. $showTitle.'">AIR WAYBILL NUMBER</span></td>
                        <td class="border-left border-top-right border-bottom" style="font-size:0.9em;width:140px;text-align:center"><span style="' . $showTitle . '">MASTER AIRWAY BILL NO</span></td>
                        <td class="" rowspan="4" style="font-size:0.9em;width:338px;text-align:center;height:113px">'. $logo.'</td>
                    </tr>

                    <tr>
                        <td class=" border-left border-top-right border-bottom" style="height:40px"></td>
                        <td class="border-right border-bottom" style="height:40px"></td>
                    </tr>

                    <tr>
                        <td class="border-left border-top-right" style="font-size:0.9em;width:169px;"><span style="'. $showTitle.'">Shipper`s Name and address</span></td>
                        <td class="border-top-right" style="font-size:0.9em;width:169px;text-align:center"><span style="'. $showTitle.'">Shipper`s Account Number</span></td>
                    </tr>   
                    <tr>
                        <td class="border-left border-right" > </td>
                        <td class="border-right border-bottom"style="height:20px"></td>
                    </tr>
                    <tr>
                        <td class="border-bottom border-right border-left" colspan="2" style="height:50px"></td>
                        <td class="" rowspan="4" style="text-align:center">' . $companyNameAndAddress . '</td>
                    </tr>

                    <tr>
                        <td class="border-left border-top-right" style="font-size:0.9em;width:169px;"><span style="'. $showTitle.'">Consignee`s Name and address</span></td>
                        <td class="border-top-right bg-grey" style="font-size:0.9em;width:169px;text-align:center;"><span style="'. $showTitle.'">Consignee`s Account Number</span></td>
                    </tr>   
                    <tr>
                        <td class="border-left border-right" > </td>
                        <td class="border-right border-bottom bg-grey"style="height:40px;"></td>
                    </tr>
                    <tr>
                        <td class="border-right border-left" colspan="2"></td>
                    </tr>
                    <tr>
                        <td class="border-bottom border-right border-left" colspan="2" rowspan="2" style="height:40px"></td>
                        <td class="border-top-bottom border-right" style="font-size:0.9em;width:338px;text-align:center;"><span style="' . $showTitle . '">Copies 1,2 and 3 of this Air Waybill are originals and have same validity</span></td>
                    </tr>
                    <tr>
                        <td class="border-top-bottom border-right" rowspan="4" style="font-size:0.9em;width:338px;"></td>
                    </tr>

                    <tr>
                        <td class="border-left border-right" style="font-size:0.9em;width:338px;"><span style="'. $showTitle.'">Issue Carrier`s Agent and City</span></td>
                    </tr>
                    <tr>
                        <td class="border-left border-right" style="font-size:0.9em;width:338px;height:40px"></td>
                    </tr>

                    <tr>
                        <td class="border-top border-right border-left" style="font-size:0.9em;width:169px;"><span style="'. $showTitle.'">Agent`s IATA Code</span></td>
                        <td class="border-top border-right" style="font-size:0.9em;width:169px;"><span style="'. $showTitle.'">Account No.</span></td>
                    </tr>
                    <tr>
                        <td class="border-right border-left" style="font-size:0.9em;width:169px;"> </td>
                        <td class="border-right" style="font-size:0.9em;width:169px;"> </td>
                        <td class="border-top border-right" rowspan="3" style="font-size:0.9em;width:338px;"><span style="'. $showTitle.'">Accounting Information</span></td>
                    </tr>

                    <tr>
                        <td class="border-right border-top border-left" style="font-size:0.9em;width:338px;"><span style="' . $showTitle . '">Airport of Departure (Addr, of first Carrier) and requested Routing</span></td>
                    </tr>
                    <tr>
                        <td class="border-left border-right" style="height:35px;"></td>
                    </tr>

                    <tr>
                        <td class="border-top border-right border-left bg-grey" style="font-size:0.8em;width:35px;"><span style="'. $showTitle.'">&nbsp;&nbsp;to</span></td>
                        <td class="border-right border-top" style="font-size:0.8em;width:163px;"><span style="'. $showTitle.'">By First Carrier</span></td>
                        <td class="border-right border-left border-top bg-grey" style="font-size:0.8em;width:35px;"><span style="'. $showTitle.'">to</span></td>
                        <td class="border-right border-top bg-grey" style="font-size:0.8em;width:35px;"><span style="'. $showTitle.'">by</span></td>
                        <td class="border-right border-top bg-grey" style="font-size:0.8em;width:35px;"><span style="'. $showTitle.'">to</span></td>
                        <td class="border-right border-top bg-grey" style="font-size:0.8em;width:35px;"><span style="'. $showTitle.'">by</span></td>

                        <td class="border-top border-right" style="font-size:0.6em;width:40px"><span style="'. $showTitle.'">Currency</span></td>
                        <td class="border-top border-right" style="font-size:0.6em;width:26px" rowspan="2"><span style="'. $showTitle.'">CHGS code</span></td>
                        <td class="border-top border-right" style="font-size:0.6em;width:40px;text-align:center;"><span style="'. $showTitle.'">WT/VAL</span></td>
                        <td class="border-top border-right" style="font-size:0.6em;width:40px;text-align:center"><span style="'. $showTitle.'">Other</span></td>
                        <td class="border-top border-right" style="font-size:0.6em;width:96px"><span style="'. $showTitle.'">Declared Value for Carriage</span></td>
                        <td class="border-top border-right" style="font-size:0.6em;width:96px"><span style="'. $showTitle.'">Declared Value for Customs</span></td>
                    </tr>
                    <tr>
                        <td class="border-right border-left bg-grey" style="font-size:0.9em;width:35px;"> </td>
                        <td class="border-right" style="font-size:0.9em;width:163px;"> </td>
                        <td class="border-right border-left bg-grey" style="font-size:0.9em;width:35px;"> </td>
                        <td class="border-right bg-grey" style="font-size:0.9em;width:35px;"> </td>
                        <td class="border-right bg-grey" style="font-size:0.9em;width:35px;"> </td>
                        <td class="border-right bg-grey" style="font-size:0.9em;width:35px;"> </td>
                        
                        <td class="border-right" style="font-size:0.6em;width:40px"></td>

                        <td class="border-right border-top" style="font-size:0.5em;width:20px;text-align:center;"><span style="'. $showTitle.'">PPD</span></td>
                        <td class="border-right border-top" style="font-size:0.5em;width:20px;text-align:center"><span style="'. $showTitle.'">COLL</span></td>
                        <td class="border-right border-top" style="font-size:0.5em;width:20px;text-align:center;"><span style="'. $showTitle.'">PPD</span></td>
                        <td class="border-right border-top" style="font-size:0.5em;width:20px;text-align:center"><span style="'. $showTitle.'">COLL</span></td>

                        
                    </tr>

                    <tr>
                        <td class="border-top border-right border-left" style="font-size:0.6em;text-align:center;width:156px"><span style="'. $showTitle.'">Airport of Destination</span></td>
                        <td class="border-top border-right" style="font-size:0.6em;width:50px;text-align:center;"><span style="'. $showTitle.'">Flight/Date</span></td>
                        <td class="border-top border-bottom" style="font-size:0.6em;width:90px;text-align:center;" colspan="2"><span style="'. $showTitle.'">For Carrier Use Only</span></td>
                        <td class="border-top border-right border-left" style="font-size:0.6em;width:50px;text-align:center;"><span style="'. $showTitle.'">Flight Date</span></td>
                        <td class="border-top border-right" style="font-size:0.6em;width:100px;text-align:center;"><span style="'. $showTitle.'">Amount Of Insurance</span></td>
                        <td class="border-top border-right border-left" style="font-size:0.6em;width:230px" rowspan="2"><span style="'. $showTitle.'">INSURANCE if shipper request insurance in accordance with conditions on reverse hereof indicate amount to be insured in figures in box marked amount of insurance</span></td>
                    </tr>

                    <tr>
                        <td class="border-right border-left"></td>
                        <td class=""></td>
                        
                        <td class="border-right"></td>
                        <td class=""></td>

                        <td class="border-right"></td>
                        <td class="border-right"></td>
                    </tr>

                    <tr>
                        <td class="border-top border-right border-left" style="font-size:0.6em;width:676px;height:35px"><span style="' . $showTitle . '">Handling Information</span></td>
                    </tr>
                    <tr>
                        <td class="border-right border-left" style="font-size:0.6em;width:676px"></td>
                    </tr>
                </table>
            ';

            $html .='
                <table cellpadding="2" style="'. $attachmentBorder.'">

                    <tr>
                        <td class="border-right border-left border-top border-bottom" style="font-size:0.6em;width:30px;text-align:center;" rowspan="2" ><span style="'. $showTitle.'">No of Pieces RCP</span></td>
                        <td class="border-top border-right border-bottom" style="font-size:0.6em;width:30px;" rowspan="2"><span style="'. $showTitle.'">Gross Weight</span></td>
                        <td class="border-top border-right border-bottom" style="font-size:0.6em;width:13px;" rowspan="2"><span style="'. $showTitle.'">Kg ib</span></td>
                        <td class="border-top border-right border-bottom bg-grey" style="font-size:0.6em;width:10px;" rowspan="2"></td>
                        <td class="border-top" style="font-size:0.6em;width:10px;"></td>
                        <td class="border-top border-right" style="font-size:0.6em;width:40px"><span style="'. $showTitle.'">Rate Class</span></td>
                        <td class="border-top border-bottom border-left border-right bg-grey" style="font-size:0.6em;width:10px;" rowspan="2"></td>
                        <td class="border-top border-right border-bottom" style="font-size:0.6em;width:50px;text-align:center;" rowspan="2"><span style="'. $showTitle.'">Chargeable Weight</span></td>
                        <td class="border-top border-right border-bottom bg-grey" style="font-size:0.6em;width:10px;" rowspan="2"></td>

                        <td class="border-top" style="font-size:0.6em;width:30px;text-align:center;"><span style="'. $showTitle.'">Rate</span></td>
                        <td class="border-top border-right" style="font-size:0.6em;width:30px;text-align:center;" ></td>

                        <td class="border-top border-bottom border-left border-right bg-grey" style="font-size:0.6em;width:10px;" rowspan="2"></td>

                        <td class="border-top border-bottom border-right" style="width:70px;font-size:0.6em;text-align:center" rowspan="2"><span style="'. $showTitle.'">Total</span></td>
                        <td class="border-top border-bottom border-right bg-grey" style="font-size:0.6em;width:10px;" rowspan="2"></td>
                        <td class="border-top border-right" style="font-size:0.6em;text-align:center;width:323px"><span style="'. $showTitle.'">Nature and Quality of Goods</span></td>
                    </tr>
                    
                    <tr>
                        <td class="" style="font-size:0.6em;"></td>
                        <td class="border-top border-right border-left border-bottom" style="font-size:0.4em;height:5px;"><span style="'. $showTitle.'">Commodity<br>Item No.</span></td>
                        <td class="border-bottom" style="font-size:0.6em;"></td>
                        <td class="border-right border-bottom" style="font-size:0.6em;"><span style="' . $showTitle . '">Charge</span></td>


                        <td class="border-bottom border-right" style="font-size:0.6em;text-align:center;width:323px"><span style="'. $showTitle.'">(incl. Dimension or Volume)</span></td>
                    </tr>

                    <tr>
                        <td class="border-right border-left" style="height:'.$heightDesc.'"></td>
                        <td class="border-right"></td>
                        <td class="border-right" rowspan="2"></td>
                        <td class="border-right border-bottom bg-grey" style="width:10px;" rowspan="2"></td>
                        <td class="border-right" rowspan="2"></td>
                        <td class="border-right" rowspan="2"></td>
                        <td class="border-right border-bottom bg-grey" rowspan="2" style="width:10px;"></td>
                        <td class="border-right" rowspan="2" ></td>
                        <td class="border-right border-bottom bg-grey"  rowspan="2" style="width:10px;"></td>

                        <td class="border-right" colspan="2" rowspan="2"></td>
                    
                        <td class="border-right border-bottom bg-grey" rowspan="2"style="width:10px;"></td>

                        <td class="border-right" ></td>

                        <td class="border-right border-bottom bg-grey" rowspan="2" style="width:10px;"></td>
                        <td class="border-right" rowspan="2"></td>
                    </tr>

                    <tr>
                        <td class="border-right border-left border-top"></td>
                        <td class="border-right border-top"></td>
                    
                        <td class="border-right border-top" style=""></td>
                    </tr>

                </table>
            ';
        
        }


        return $html;
    };

    $generateFooterTable = function ($dataset, $param) {
        global $arrCopy;

        $obj = new EMKLHouseBL();
        $emklJobOrder = new EMKLJobOrder();
        $customer = new Customer();
        $setting = new Setting();

        $rs = $dataset['rs'];

        $attachment = $param['attachment'];

        $rsJobOrder = $emklJobOrder->searchData($emklJobOrder->tableName . '.pkey', $rs[0]['refheaderkey']);

        $html = $obj->printSetting['defaultStyle'];

        $byInformation = '';
        // $byInformation = $rs[0]['byinformation'];
        $by2Information = '';
        $placeOfDelivery = $rs[0]['podeliveryname'];

        $shippingType = $param['shippingtype'];

        $isShowTitle = $param['showTitle'];
        $isShowBorder = $param['showBorder'];
        
        $showTitle = '';
        if($isShowTitle == 0) {
            $showTitle = 'display:none;'; 
        }

        if($shippingType == EMKL['shipping']['sea']) {
            //transportation type sea / ocean

            if($isShowTitle == 1) {
                $company = 'CHINA INT`L FREIGHT CO.,LTD';
            }

            $html .= '
                <table cellpadding="2">
                    <tr>
                        <td class="border-bottom border-right border-top" style="font-size:0.9em;text-align:center;width:160px"><span style="'. $showTitle .'">Total number packages :</span></td>
                        <td class="border-bottom border-top" style="font-size:0.9em;text-align:center;width:517px"> </td>
                    </tr>
                </table>
            ';

            // $html .='<table cellpadding="2">
            //     <tr>
            //         <td class="border-right" style="font-size:0.9em;width:180px"><span style="'. $showTitle .'">Freight and charges : </span></td>
            //         <td class="border-right" style="font-size:0.9em;text-align:center;width:80px"><span style="'. $showTitle .'">Prepaid</span></td>
            //         <td class="border-right" style="font-size:0.9em;text-align:center;width:80px"><span style="'. $showTitle .'">Collect</span></td>
            //         <td style="font-size:0.9em;width:317px;height:30px"><span style="' . $showTitle . '">FOR DELIVERY OF GOODS PLEASE APPLY<br>TO</span></td>
            //     </tr>
            //     <tr>
            //         <td rowspan="7" class="border-right" style="height:68px"></td>
            //         <td rowspan="7" class="border-right" style="height:68px"></td>
            //         <td rowspan="7" class="border-right" style="height:68px"></td>
            //         <td style="height:68px; width:317px"></td>
            //     </tr>
            //     <tr>
            //         <td class="border-right border-top" style="font-size:0.9em;width:158.5px"><span style="'. $showTitle .'">Prepaid at</span></td>
            //         <td class="border-top " style="font-size:0.9em;width:158.5px"><span style="'. $showTitle .'">Payable at</span></td>
            //     </tr>
            //     <tr>
            //         <td class="border-right"></td>
            //         <td></td>
            //     </tr>
            //     <tr>
            //         <td class="border-right border-top" style="font-size:0.9em;width:158.5px;"><span style="'. $showTitle .'">Number of original B (s)/L</span></td>
            //         <td class="border-top " style="font-size:0.9em;width:158.5px"><span style="'. $showTitle .'">Shipper Reference</span></td>
            //     </tr>
            //     <tr>
            //         <td rowspan="3" class="border-right border-bottom"></td>
            //         <td class=""></td>
            //     </tr>
            //     <tr>
            //         <td class=""style="font-size:0.9em;"><span style="'. $showTitle .'">S/O No.</span></td>
            //     </tr>
            //     <tr>
            //         <td class="border-bottom"></td>
            //     </tr>
            //     <tr>
            //         <td class="border-left border-right border-top"><span style="' . $showTitle . '">Total Prepaid</span></td>
            //         <td class="border-right border-top"></td>
            //         <td class="border-right border-top" style="text-align:center">-</td>
            //         <td rowspan="5" colspan="2" style="text-align:center">&nbsp;&nbsp;&nbsp;<table width="100%">
            //         <tr><td></td></tr>
            //         <tr><td style="font-weight:bold;font-size:15px">'.$company.'</td></tr>
            //         <tr><td></td></tr>
            //         <tr><td style="font-weight:bold;">'. $byInformation .'</td></tr>
            //         <tr><td></td></tr>
            //         <tr><td><tr><td><table><tr><td style="width:20px;font-size:0.8em;">By</td><td style="font-size:1em;font-weight:bold;text-align:center;border-bottom:1px solid 2E2E84;width:280px">' . $by2Information . '</td></tr></table></td></tr></td></tr>
            //         </table></td>
            //     </tr>
            //     <tr>
            //         <td class="border-left border-right border-top"><span style="' . $showTitle . '">Total Collect</span></td>
            //         <td class="border-right border-top" style="text-align:center">-</td>
            //         <td class="border-right border-top"></td>
            //     </tr>
            //     <tr>
            //         <td class="border-left border-right border-top" colspan="3"><span style="'. $showTitle .'">Place and date of Issue</span></td>
            //     </tr>
            //     <tr>
            //         <td class="border-left border-right border-top" colspan="3"><span style="'. $showTitle .'">On Board Date</span></td>
            //     </tr>
            //     <tr>
            //         <td class="border-left border-right border-top border-bottom" colspan="3"><span style="' . $showTitle . '">SIGNATURE</span></td>
            //     </tr>
            // </table>';

        } else if($shippingType == EMKL['shipping']['air']) {
            
            $html .= '<table cellpadding="2">
                <tr>
                    <td class="border-top border-left border-right" style="width:10px;font-size:0.6em;"></td>
                    <td class="border-top border-right border-bottom" style="width:41.5px;font-size:0.6em;text-align:center;"><span style="'. $showTitle .'">Prepaid</span></td>
                    <td class="border-right border-top border-bottom" style="width:90px;font-size:0.6em;text-align:center;"><span style="'. $showTitle .'">Weight Charge</span></td>
                    <td class="border-top border-bottom" style="width:41.5px;font-size:0.6em;text-align:center;"><span style="'. $showTitle .'">Collect</span></td>
                    <td class="border-top border-left border-right" style="width:20px;font-size:0.6em;"></td>

                    <td class="border-top border-right" style="width:473px;font-size:0.6em" rowspan="2"><span style="'. $showTitle .'">&nbsp;&nbsp;Other Charges</span></td>

                </tr>
                <tr>
                    <td class="border-left border-right" style="width:101.5px;font-size:0.6em;"></td>
                    <td class="border-left border-right" style="width:101.5px;font-size:0.6em;"></td>
                </tr>

                <tr>
                    <td class="border-top border-left border-right" style="width:51.5px;font-size:0.6em;"></td>
                    <td class="border-right border-top border-bottom" style="width:90px;font-size:0.6em;text-align:center;"><span style="'. $showTitle .'">Valuation Charge</span></td>
                    <td class="border-top border-left border-right" style="width:61.5px;font-size:0.6em;"></td>

                    <td class="border-top border-right" style="width:473px;font-size:0.6em" rowspan="2"> </td>

                </tr>
                <tr>
                    <td class="border-left border-right" style="width:101.5px;font-size:0.6em;"></td>
                    <td class="border-left border-right" style="width:101.5px;font-size:0.6em;"></td>
                </tr>

                <tr>
                    <td class="border-top border-left border-right" style="width:81.5px;font-size:0.6em;"></td>
                    <td class="border-right border-top border-bottom" style="width:30px;font-size:0.6em;text-align:center;"><span style="'. $showTitle .'">Tax</span></td>
                    <td class="border-top border-left border-right" style="width:91.5px;font-size:0.6em;"></td>

                    <td class="border-top border-right border-bottom" style="width:473px;font-size:0.6em" rowspan="2"> </td>

                </tr>
                <tr>
                    <td class="border-left border-right" style="width:101.5px;font-size:0.6em;"></td>
                    <td class="border-left border-right" style="width:101.5px;font-size:0.6em;"></td>
                </tr>

                <tr>
                    <td class="border-top border-left border-right" style="width:46.5px;font-size:0.6em;"></td>
                    <td class="border-right border-top border-bottom" style="width:110px;font-size:0.6em;text-align:center;"><span style="'. $showTitle .'">Total other Charges Due Agent</span></td>
                    <td class="border-top border-left border-right" style="width:46.5px;font-size:0.6em;"></td>

                    <td class="border-top border-right" style="width:473px;font-size:0.7em" rowspan="3"><span style="'. $showTitle .'">Shipper certifies that the particulars on the face hereof are correct and that insofar as any part of the consignment contains dangerous goods, such part is properly described by name and is in proper condition for carriage by air according to the International Air Transport Association`s Dangerous Good Regulations, or the International Civil Aviation Organization`s Technical Instructions For the Safe Transport of Dangerous Goods By Air, as applicable.</span></td>

                </tr>
                <tr>
                    <td class="border-left border-right" style="width:101.5px;font-size:0.6em;"></td>
                    <td class="border-left border-right" style="width:101.5px;font-size:0.6em;"></td>
                </tr>

                <tr>
                    <td class="border-top border-left border-right" style="width:46.5px;font-size:0.6em;"></td>
                    <td class="border-right border-top border-bottom" style="width:110px;font-size:0.6em;text-align:center;"><span style="' . $showTitle . '">Total other Charges Due Carrier</span></td>
                    <td class="border-top border-left border-right" style="width:46.5px;font-size:0.6em;"></td>

                </tr>
                <tr>
                    <td class="border-left border-right" style="width:101.5px;font-size:0.6em;"></td>
                    <td class="border-left border-right" style="width:101.5px;font-size:0.6em;"></td>

                    <td class="border-right border-bottom" style="width:473px;text-align:center" rowspan="2"><table><tr><td style="width:75px"></td><td><table style="width:300px;">
                    <tr><td style="text-align:center;font-size:1.2em;color:#333;"><span style="' . $showTitle . '"> </span></td></tr>
                    <tr><td style="border-bottom:1px dotted solid;'. $showTitle .'"> </td></tr>  
                    <tr><td style="font-size:0.6em;"><span style="' . $showTitle . '">Signature of Shipper or his Agent</span></td></tr>
                    </table></td></tr></table></td>

                </tr>

                <tr>
                    <td class="border-left border-right border-top border-bottom bg-grey" style="width:101.5px;font-size:0.6em;height:20px"></td>
                    <td class="border-left border-right border-top border-bottom bg-grey" style="width:101.5px;font-size:0.6em;height:20px"></td>
                </tr>
                
                <tr>
                    <td class="border-left border-right" style="width:101.5px;font-size:0.6em;"></td>
                    <td class="border-left border-right" style="width:101.5px;font-size:0.6em;"></td>
                </tr>

                <tr>

                    <td class="border-top border-right border-bottom border-left border-bottom bg-grey" style="width:101.5px;font-size:0.6em;text-align:center;"><span style="'. $showTitle .'">Currency Conversion Rates</span></td>

                    <td class="border-top border-bottom border-right border-bottom bg-grey" style="width:101.5px;font-size:0.6em;text-align:center;"><span style="'. $showTitle .'">cc charges in Dest Currency</span></td>

                </tr>
                <tr>
                    <td class="border-left border-right border-bottom bg-grey" style="width:101.5px;font-size:0.6em;"></td>
                    <td class="border-left border-right border-bottom bg-grey" style="width:101.5px;font-size:0.6em;"></td>
                </tr>

                <tr>
                    <td class="border-right border-left border-bottom bg-grey" rowspan="2" style="width:101.5px;font-size:0.6em; text-align:center;"><span style="' . $showTitle . '">For carrier Use only at Description</span></td>
                    <td class="border-right border-bottom bg-grey" style="width:101.5px;font-size:0.6em; text-align:center;"><span style="'. $showTitle .'">Charges ad Destination</span></td>
                    <td class="border-right border-bottom bg-grey" style="width:101.5px;font-size:0.6em; text-align:center;"><span style="'. $showTitle .'">Total collect Charges</span></td>
                </tr>
                <tr>
                    <td class="border-bottom border-right bg-grey" style=""></td>
                    <td class="border-bottom border-right bg-grey" style=""></td>
                </tr>

            </table>';

            if (!$attachment) {
                // $html .= '<table><tr><td></td></tr><tr><td style="color:red;font-weight:bold;width:675px;text-align:center;font-size:1.3em;">EXTRA COPY (ACCOUNTING  DESTINATION USE)</td></tr></table>';
            }
            
        }

        return $html;

    };

    $setXYContent = function ($dataset, $param) use (&$attachmentPages) {

        
        global $pdf;
        global $arrContainers;
        //global $containerIndex;
        global $needAttachment;
        global $paperSetting;

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
        $itenUnit = new ItemUnit();
            
        $rs = $dataset['rs'];
        
        $attachment = $param['attachment'];
        $shippingType = $param['shippingtype'];

        $rsData = $obj->getDataRowById($rs[0]['pkey']); 
        $rsJobOrder = $emklJobOrder->searchData($emklJobOrder->tableName . '.pkey', $rs[0]['refheaderkey']); 
        $rsDetail = $emklJobOrder->getDetailByColumn($emklJobOrder->tableNameDetail . '.pkey', $rs[0]['refkey']);
        $rsUnit = $itenUnit->getDataRowById($rs[0]['sumunitkey']);
        
        $arrShipper = array();
        if (!empty($rs[0]['shippername']))
            array_push($arrShipper, strtoupper(htmlspecialchars_decode($rs[0]['shippername'])));
        if (!empty($rs[0]['shipperaddress']))
            array_push($arrShipper, str_replace(chr(13), '<br>', strtoupper(htmlspecialchars_decode($rs[0]['shipperaddress']))));

        $HBLCode = $rs[0]['prefix'] . '' . $rs[0]['code'];

        $rsCurrency = $currency->getDataRowById($rsDetail[0]['currencykey']);
        $currencyName = $rsCurrency[0]['name'];
        
        //feeder vessel
        $feederName = '';
        $feederNumber = '';
        if(!empty($rs[0]['feederkey'])) {
            $rsFeeder = $vessel->getDataRowById($rs[0]['feederkey']);
            $feederName = $rsFeeder[0]['name'];
            $feederNumber = $rs[0]['feedernumber'];
        }

        //define array
        $arrMarksNumberData = array();
        $arrIntendedToConnect = array();
        $arrContainer = array();
        $arrMarksAndNumber = array();

        $arrDescriptionOfGoodsData = array();
        $arrDescriptionOfGoods = array();
        $arrDescriptionOfPackageAndGoodsAttachment = array();
        $arrDetailContainerInformation = array();

        

        //mother vessel
        $motherVessel = '';
        $vesselName = '';
        $vesselNumber = '';
        if(!empty($rs[0]['vesselkey'])) {
            $rsVessel = $vessel->getDataRowById($rs[0]['vesselkey']);
            $motherVessel = 'INTENDED TO CONNECT <br>' . $rsVessel[0]['name'] . ' ' . $rs[0]['vesselnumber'];

            //push data to array
            array_push($arrIntendedToConnect, 'INTENDED TO CONNECT <br>');
            array_push($arrIntendedToConnect, $rsVessel[0]['name'] . ' ' . $rs[0]['vesselnumber'] . '<br>');

            $vesselName = $rsVessel[0]['name'];
            $vesselNumber = $rsVessel[0]['vesselnumber'];
        }

        // $polName = $rs[0]['polname'];
        $polName = ($rs[0]['isoverwritepod'] == 0) ? $rs[0]['polname']: $rs[0]['placeofreceipt'];
        // $podName = $rs[0]['podname'];
        $podName = ($rs[0]['isoverwritepol'] == 0) ? $rs[0]['podname']: $rs[0]['portofdischarge'];

        // $placeOfDelivery = $rs[0]['podeliveryname'];
        $placeOfDelivery = ($rs[0]['isoverwritepod'] == 0) ? $rs[0]['podeliveryname']: $rs[0]['placeofdelivery'];
        $placeOfReceipt = $rsJobOrder[0]['placeofreceiptname'];

        $finalDestination = '';
        $rsCity = $city->searchData('', '', true, ' and ' . $city->tableName . '.pkey = (' . $obj->oDbCon->paramString($rs[0]['finaldestinationkey']) . ') ');   
        if($rs[0]['isoverwritefinaldestination'] == 0) {
            if(!empty($rs[0]['finaldestinationkey'])) {
                //$finalDestination = $rsCity[0]['citycategoryname'];
                $finalDestination = $rsCity[0]['name'].', '.$rsCity[0]['countryname'];
            }
        } else {
            $finalDestination = $rs[0]['finaldestination'];
        }   

        

        $connectingVessel = '';
        if(!empty($rs[0]['connectingvesselkey']) || !empty($rs[0]['connectingvessel2key'])) {
            $connectingVessel1 = '';
            if(!empty($rs[0]['connectingvesselkey'])) {
                $rsVessel = $vessel->getDataRowById($rs[0]['connectingvesselkey']);
                $connectingVessel1 = 'INTENDED TO CONNECT <br>' . $rsVessel[0]['name'] . ' ' . $rs[0]['connectingvesselnumber'];
    
                //push data to array
                array_push($arrIntendedToConnect, 'INTENDED TO CONNECT<br>');
                array_push($arrIntendedToConnect, $rsVessel[0]['name'] . ' ' . $rs[0]['connectingvesselnumber']);
            }

            $connectingVessel2 = '';
            if (!empty($rs[0]['connectingvessel2key'])) {
                $rsVessel2 = $vessel->getDataRowById($rs[0]['connectingvessel2key']);
                $connectingVessel2 = '<br>INTENDED TO CONNECT <br>' . $rsVessel2[0]['name'] . ' ' . $rs[0]['connectingvessel2number'] . '<br>';
            
                //push data to array
                array_push($arrIntendedToConnect, '<br>INTENDED TO CONNECT<br>');
                array_push($arrIntendedToConnect, $rsVessel2[0]['name'] . ' ' . $rs[0]['connectingvessel2number'].'<br>');
     
            }

            // $connectingVessel3 = '';
            // if (!empty($rs[0]['connectingvessel3key'])) {
            //     $rsVessel3 = $vessel->getDataRowById($rs[0]['connectingvessel3key']);
            //     $connectingVessel3 = $rsVessel3[0]['name'] . ' ' . $rs[0]['connectingvessel3number'] . '<br>';
            // }

            $connectingVessel = $connectingVessel1 . $connectingVessel2;

        }

        // kondisi yg harus dicek
        if (!$attachment) {
            $rsContainerDetail = $emklJobOrder->getDetailContainer($rs[0]['refheaderkey']);
            foreach ($rsContainerDetail as $key => $row)
                array_push($arrContainers, array('container' => $row['containerno'], 'seal' => $row['sealno'], 'containername' => $row['containername']));

            $maxContainerPerPage = (empty($rs[0]['marksnumber'])) ? 6 : 3; // tentukan batasan container per halaman, tergantung dr ad marks number atau tdk
        } else {
            $maxContainerPerPage = 9999; //count($arrContainers) - $containerIndex; 
        }

        if (!empty($rs[0]['description'])) {
            $needAttachment = true; // kondisi ketika perlu attachment
        }

        // marks and number hanya muncul di halaman pertama
        
        $marksNumber = '';
        if(!empty($motherVessel)) {
            $marksNumber .= $motherVessel . '<br>';
        }
        if(!empty($connectingVessel)) {
            $marksNumber .= $connectingVessel . '<br>';
        }

        if (!$attachment) {
            $marksNumber .= (!empty($rs[0]['marksnumber'])) ?  str_replace(chr(13), '<br>', strtoupper(htmlspecialchars_decode($rs[0]['marksnumber']))) : '';
            
        }
        
        
        //marks number push to array
        $marksNumbers = (!empty($rs[0]['marksnumber'])) ? str_replace(chr(13), '<br>|', str_replace(' ' ,'&nbsp;', strtoupper(htmlspecialchars_decode($rs[0]['marksnumber'])))) : '';
      
        if(!empty($marksNumbers)) {
            array_push($arrMarksAndNumber, '<br>');
        }
        $marksNumbersArray = explode('|', $marksNumbers);
        $arrMarksAndNumber = array_merge($arrMarksAndNumber, $marksNumbersArray);//push to array


        if(!empty($marksNumber))
        $marksNumber = '<tr><td>' . $marksNumber . '</td></tr>';
    
        $party = '';
        if (in_array($rsJobOrder[0]['loadcontainertypekey'], array(EMKL['container']['fcl'], EMKL['container']['trucking'])) && $rsJobOrder[0]['transportationtypekey'] == EMKL['shipping']['sea']) {
            $arrParty = array();
            $rsParty = $emklJobOrder->getDetailVolume($rsJobOrder[0]['pkey']);
            for ($i = 0; $i < count($rsParty); $i++)
                array_push($arrParty, $obj->formatNumber($rsParty[$i]['qty']) . ' x ' . $rsParty[$i]['itemname']);
            $party = implode(', ', $arrParty);
        }

        $lclFclDescription = '';

        if($shippingType == EMKL['shipping']['sea']) {
        
            //$lclFclDescription = (in_array($rsJobOrder[0]['loadcontainertypekey'], LCL_CONTAINER_TYPE)) ? 'LCL SAID TO CONTAIN' : '<br>' . $party . ' CONTAINERS : <br>';
            $lclFclDescription = (in_array($rsJobOrder[0]['loadcontainertypekey'], LCL_CONTAINER_TYPE)) ? 'LCL SAID TO CONTAIN' : '<br>' . $party .' CONTAINERS :';

            // $heightDesc = ($needAttachment) ? 'height:150px' : 'height:150px';
        
            if (!$attachment) {
                // untuk jenis halaman pertama
                //$description = $lclFclDescription . '<br>' . strtoupper($rs[0]['shortdescription']);
                $description = strtoupper($rs[0]['shortdescription']);
                $attachmentBorder = '';
            } else {
                // untuk jenis attachment
                $description = strtoupper($rs[0]['description']);
                $attachmentBorder = 'border-bottom:1px solid #333';
                // $heightDesc = 'height:450px';
            }

            $descriptions = strtoupper(htmlspecialchars_decode($rs[0]['shortdescription']));
            $descriptionsAttachment = strtoupper($rs[0]['description']);

        } else if($shippingType == EMKL['shipping']['air'])  {

            $lclFclDescription = (in_array($rsJobOrder[0]['loadcontainertypekey'], LCL_CONTAINER_TYPE)) ? 'LCL SAID TO CONTAIN' : '<br>' . $party . ' CONTAINERS : <br>';
            $heightDesc = ($needAttachment) ? 'height:85px' : 'height:130px';
            
            if (!$attachment) {
                // untuk jenis halaman pertama
                $description = strtoupper(htmlspecialchars_decode($rs[0]['shortdescription']));
                $attachmentBorder = '';
            } else {
                // untuk jenis attachment
                $description = strtoupper(htmlspecialchars_decode($rs[0]['description']));
                $attachmentBorder = 'border-bottom:1px solid #333';
                $heightDesc = 'height:405px';
            }

            $text = '<p style="font-size:0.9em;">It is agreed that the goods described herein are accepted in apparent good order and condition (except as noted) for carriage SUBJECT TO THE CONDITIONS OF CONTRACT ON THE REVERSE HEREOF THE SHIPPER`S ATTENTION IS DRAWN TO THE NOTICE CONCERNING CARRIERS LIMITATION OF LIABILITY, Shipper may increase such limitation of liability by declaring a higher value for carriage and paying a supplemental charge if required.</p>';
        
            $rsConnVessel = $vessel->getDataRowById($rs[0]['connectingvesselkey']);
            // $connectingCode = $rsConnVessel[0]['name'];
            $connectingCode = $rs[0]['connectingvesselnumber'];
            $connectingCode = substr($connectingCode, 0, 2);
            $rsConn2Vessel = $vessel->getDataRowById($rs[0]['connectingvessel2key']);
            // $connecting2Code = $rsConn2Vessel[0]['name'];
            $connecting2Code = $rs[0]['connectingvessel2number'];
            $connecting2Code = substr($connecting2Code, 0, 2);

            $connectingAirport = '';
            if(!empty($rs[0]['connectingcountrykey'])) {
                $rsConnAirport = $port->getDataRowById($rs[0]['connectingcountrykey']);
                $connectingAirport = $rsConnAirport[0]['code'];
            }

            $connecting2Airport = '';
            if(!empty($rs[0]['connectingcountry2key'])) {
                $rsConn2Airport = $port->getDataRowById($rs[0]['connectingcountry2key']);
                $connecting2Airport = $rsConn2Airport[0]['code'];
            }

            $connecting3Airport = '';
            if(!empty($rs[0]['connectingcountry3key'])) {
                $rsConn3Airport = $port->getDataRowById($rs[0]['connectingcountry3key']);
                $connecting3Airport = $rsConn3Airport[0]['code'];
            }

            $currencyType = ($rsDetail[0]['currencykey'] == 1) ? 'IDR' : 'USD';

            $notifyParty = 'NOTIFY PARTY : ' . $rs[0]['carriername'];
        
        
        }

        $rsContainer = $emklJobOrder->getDetailContainer($rs[0]['refheaderkey']);

        $unitMeas = $rs[0]['unitofmeaskey'] == 1 ? 'M3' : 'CBM';

        // $totalPkgs = 0;
        $GW = $rs[0]['sumgrossweight'];
        $CW = $rs[0]['sumchargeweight'];
        $NW = $rs[0]['sumnetweight'];
        $MEAS = $rs[0]['summeas'];
        $totalPkgs = $rs[0]['sumqty'];
        $byInformationData = $rs[0]['byinformation'];
        $by2InformationData = $rs[0]['by2information'];


        $arrContainerDetail = array();

        $detailContainerInformation ='<table><tr><td style="width:90px;font-size:0.9em;font-weight:bold;">CONTAINER</td><td style="width:80px;font-size:0.9em;font-weight:bold;">QTY</td><td style="width:95px;font-size:0.9em;font-weight:bold;">G.W</td><td style="width:95px;font-size:0.9em;font-weight:bold;">MEAS</td></tr></table>';
        //$containerNOS ='<table ><tr><td style="width:75px">CONTAINER</td><td style="width:10px"> / </td><td style="width:34px">SIZE</td><td style="width:10px"> / </td><td style="width:60px">SEAL NO :</td></tr></table>';
        $containerNOS ='<table ><tr><td style="width:70px">CONTAINER/</td><td style="width:28px">SIZE/</td><td style="width:60px">SEAL NO :</td></tr></table>';
        
        //push container information
        array_push($arrContainer, '<br>');
        array_push($arrContainer, $containerNOS);

        //push detail container information
        if ($rs[0]['isshowcontainernumber'] == 1) { //Push If Show Container Information checked
            array_push($arrContainerDetail, $detailContainerInformation);
        }

        foreach($rsContainer as $container) {
            
            // $GW += $container['grossweight'];
            // $NW += $container['netweight'];
            // $MEAS += $container['meas'];

            // $totalPkgs += $container['qty'];
        
            //$containerNOS .='<span>'. $container['containerno'] .'  /  '. $container['containername'] .'  /  '. $container['sealno']  .' <br></span>';
            $containerNos ='<span>'. $container['containerno'] .'/'. $container['containername'] .'/'. $container['sealno']  .' <br></span>';
            //push container data
            array_push($arrContainer, $containerNos);

            //$detailContainerInformation .= '<tr><td>'.$container['containerno'].'</td><td>'. $obj->formatNumber($container['qty']) .' '. $container['unitname'] .'</td><td>' . $obj->formatNumber($container['grossweight'],3) . ' KGS</td><td>' . $obj->formatNumber($container['meas'], 2,'.') . ' CBM</td></tr>';

            if ($rs[0]['isshowcontainernumber'] == 1) { //Push If Show Container Information checked
                $containerInformation = '<table><tr><td style="width:90px;">' . $container['containerno'] . '</td>  <td style="width:80px;">' . $obj->formatNumber($container['qty']) . ' ' . $container['unitname'] . '</td>  <td style="width:95px;">' . $obj->formatNumber($container['grossweight'], 4) . ' KGS</td><td style="width:95px;">' . $obj->formatNumber($container['meas'], 4, '.') . ' CBM</td></tr></table>';
                array_push($arrContainerDetail, $containerInformation);
            }
        }

        //$detailContainerInformation .='</tbody></table>';



        //$noOfPkgs = (empty($totalPkgs) ? '' : $obj->formatNumber($totalPkgs) . ' ' . $rsContainer[0]['unitname']);
        $noOfPkgs = (empty($totalPkgs) ? '' : $obj->formatNumber($totalPkgs) . ' ' . $rsUnit[0]['name']);

        //$totalGW_NW = $obj->formatNumber($GW,3) . ' KGS <br>&nbsp;&nbsp;&nbsp;&nbsp;NET WEIGHT<br>' .  $obj->formatNumber($NW,3) .' KGS';
        //$totalGW_NW = 'GW:&nbsp;&nbsp;&nbsp;'.$obj->formatNumber($GW,3) . ' KGS <br>NW:&nbsp;&nbsp;&nbsp;' .  $obj->formatNumber($NW,3) .' KGS <br>MS:&nbsp;&nbsp;&nbsp;'.$obj->formatNumber($MEAS,4) . ' '. $unitMeas;
        
        $totalGW_NW = '';

        if($rs[0]['sumgrossweight'] != 0) {
            $totalGW_NW .= 'GW:&nbsp;&nbsp;&nbsp;'.$obj->formatNumber($GW,4) . ' KGS';
        }
        
        if($rs[0]['sumnetweight'] != 0) { //NW kalau 0 di hide
            $totalGW_NW .='<br>NW:&nbsp;&nbsp;&nbsp;' .  $obj->formatNumber($NW,4) .' KGS';
        }

        if($rs[0]['summeas'] != 0) { //MEAS kalau 0 di hide
            $totalGW_NW .= '<br>MS:&nbsp;&nbsp;&nbsp;'.$obj->formatNumber($MEAS,4) . ' '. $unitMeas;
        }
    
        // $totalGW_NW = 'GW:&nbsp;&nbsp;&nbsp;'.$obj->formatNumber($GW,3) . ' KGS <br>NW:&nbsp;&nbsp;&nbsp;' .  $obj->formatNumber($NW,3) .' KGS';
        $totalMEAS = 'MS:&nbsp;&nbsp;&nbsp;'.$obj->formatNumber($MEAS,4) . ' '. $unitMeas;

        $shipmentTerm = $rs[0]['shipmenttermname'];
        $paymentType = ($rs[0]['freighttermkey'] == 1) ? 'ORIGIN' : 'DESTINATION';
        $paymentTypePrepaid = ($rs[0]['freighttermkey'] == 1) ? 'FREIGHT PREPAID<br><br>'. $shipmentTerm.'' : '';
        $paymentTypeCollect = ($rs[0]['freighttermkey'] == 1) ? '' : 'FREIGHT COLLECT<br><br>' . $shipmentTerm . '';
        $freightCharges = $rs[0]['freightcharges'];

        $prepaidAt = $rs[0]['prepaidat'];
        $payableAt = $rs[0]['payableat'];

        $paymentPrepaidCollect = (($rs[0]['freighttermkey'] == 1) ? 'FREIGHT PREPAID' : 'FREIGHT COLLECT');

        $numberOfOriginal = (empty($rs[0]['numberoforiginal']) ? '' : $rs[0]['numberoforiginal'] . ' '.' ( '. strtoupper($obj->sayNumberInEnglish($rs[0]['numberoforiginal'])).')');

        $rsAgent = $customer->getDataRowById($rs[0]['agentkey']);
        $agentName = $rsAgent[0]['name'];
        
        $agentAddress = nl2br(htmlspecialchars_decode($rsAgent[0]['address']));
        $agentFax = $rsAgent[0]['fax'];
        $agentPhone = $rsAgent[0]['phone'];

        $agentInformation = '';
        if($rs[0]['isoverwriteagent'] == 0) {
            $agentInformation = $rsAgent[0]['name'] . (!empty($agentAddress) ? '<br>' . $agentAddress : '') . (empty($agentFax) ? '' : '<br>FAX : '. $agentFax .' ') . (empty($agentPhone) ? '' : '<br>TEL : '. $agentPhone .' ');
        } else {
            $agentInformation = strtoupper(htmlspecialchars_decode($rs[0]['agentname'])) . '<br>' . str_replace(chr(13), '<br>', strtoupper(htmlspecialchars_decode($rs[0]['agentaddress'])));
        }

        $agentAir = 'PT. CIF TRANSPORTATION INDONESIA';
        
        $rsPOD = $port->getDataRowById($rsJobOrder[0]['podkey']);
        $podCode = $rsPOD[0]['code'];

        $freightTerm = $rs[0]['freighttermkey'];

        $etdPol = $obj->formatDBDate($rs[0]['etdpol'], 'M d, Y', array('returnOnEmpty' => true));
        $etaPod = $obj->formatDBDate($rs[0]['etapod'], 'M d, Y', array('returnOnEmpty' => true));

        $firstByCarrier = '';
        if(!empty($rs[0]['feederkey'])) {
        $rsFirstCarrier = $vessel->getDataRowById($rs[0]['feederkey']);
            // $firstByCarrier =  $rs[0]['feedernumber'] . ' / ' . $obj->formatDBDate($rs[0]['etapod'], 'M. d', array('returnOnEmpty' => true));
            //$firstByCarrier =  $rs[0]['feedernumber'] . ' / ' . $obj->formatDBDate($rs[0]['etdpol'], 'M. d', array('returnOnEmpty' => true));
            $firstByCarrier =  $rs[0]['feedernumber'] . ' / ' .(empty($rs[0]['etdpol']) || $rs[0]['etdpol'] == '0000-00-00' ? '' : $obj->formatDBDate($rs[0]['etdpol'], 'd'));
        }
        
        // $flightDate2 = $rs[0]['connectingvessel2number'] . ' / ' . $obj->formatDBDate($rs[0]['transit2date'], 'd', array('returnOnEmpty' => true));
        //$flightDate2 = $rs[0]['connectingvessel2number'] . ' / ' . $obj->formatDBDate($rs[0]['etapod'], 'd', array('returnOnEmpty' => true));
        
        
        $flightDate1 = $rs[0]['connectingvesselnumber'] . ' / ' . (empty($rs[0]['transit1date']) || $rs[0]['transit1date'] == '0000-00-00' ? '' : $obj->formatDBDate($rs[0]['transit1date'], 'd'));
        $flightDate2 = $rs[0]['connectingvessel2number'] . ' / ' . (empty($rs[0]['transit2date']) || $rs[0]['transit2date'] == '0000-00-00' ? '' : $obj->formatDBDate($rs[0]['transit2date'], 'd'));

        $descriptionOfPackageAndGoods = '';
        
        $descriptionOfGoods = '';
        //show / hidden container, push to array
        if ($rs[0]['isshowcontainernumber'] == 1) {
            $descriptionOfGoods = str_replace(chr(13), '<br>|', str_replace(' ','&nbsp;', $descriptions));
        } else {
            $descriptionOfGoods = str_replace(chr(13), '<br>|', str_replace(' ','&nbsp;', $descriptions));
        }
        
        if(!empty($descriptionOfGoods)) {
            $descriptionOfGoodsArray = explode('|', $descriptionOfGoods);
            $arrDescriptionOfGoods = array_merge($arrDescriptionOfGoods, $descriptionOfGoodsArray);
            array_push($arrDescriptionOfGoods, '<br>');
            array_push($arrDescriptionOfGoods, '<br>');
        }

        //push detail container
        $arrDetailContainerInformation = array_merge($arrDetailContainerInformation, $arrContainerDetail);

        $marskAndNumbers = '';
        $GW_NW = '';
        $MEAS = '';
        $NO_OF_PKGS = '';
        if(!$attachment){
            $marskAndNumbers = $marksNumber . '<br>'.$containerNOS;
            $NO_OF_PKGS = $noOfPkgs;

            //show / hidden container
            if ($rs[0]['isshowcontainernumber'] == 1) {
                $descriptionOfPackageAndGoods = str_replace(chr(13), '<br>', $description) . '<br><br>' . $detailContainerInformation;
            } else {
                $descriptionOfPackageAndGoods = str_replace(chr(13), '<br>', $description);
            }
             
            $GW_NW = $totalGW_NW;
            $MEAS = $totalMEAS;
        } else {
            //$descriptionOfPackageAndGoods = '&nbsp;&nbsp;'.str_replace(chr(13), '<br>', $description);
            $descriptionOfPackageAndGoods = str_replace(chr(13), '<br>|', $description);

            $descriptionOfPackagesGoodsArray = explode('|', $descriptionOfPackageAndGoods);

            $arrDescriptionOfPackageAndGoodsAttachment = array_merge($arrDescriptionOfPackageAndGoodsAttachment, $descriptionOfPackagesGoodsArray);
        }

        
        $by2Information = $rs[0]['by2information'];
        $by3Information = $rs[0]['by3information'];
        $by4Information = $rs[0]['by4information'];

        //$alsoNotifyParty = $rs[0]['alsonotifyparty'];
        $alsoNotifyParty = str_replace(chr(13), '<br>',$rs[0]['shipto']);

        //breakdown marks & nos and description of packages and goods
        $marksNumber1 = [];
        $marksNumber2 = [];

        
        $arrMarksNumberData = array_merge($arrIntendedToConnect, $arrContainer, $arrMarksAndNumber);
        $arrDescriptionOfGoodsData = array_merge($arrDescriptionOfGoods, $arrDetailContainerInformation);
            
        $maxQuota = 15;
        $maxDescQuota = 17;

        $lengthIntendedConnect = count($arrIntendedToConnect);
        $lengthContainer = count($arrContainer);
        $lengthMarksNumber = count($arrMarksAndNumber);

        $lengthMarksNumberData = count($arrMarksNumberData);
        $totalDescription = count($arrDescriptionOfGoodsData);

        //kalau jml / panjang > quota maka buat $needAttachment = true, agar sisa ke pages 2
        if (($lengthMarksNumberData > $maxQuota) || ($totalDescription > $maxDescQuota)) {
            $needAttachment = true;
        }

        // $marksNumber1 = array_slice($arrMarksNumberData, 0, $maxQuota);
        // $marksNumber2 = array_slice($arrMarksNumberData, $maxQuota);

        if ($lengthIntendedConnect <= $maxQuota) {
            $marksNumber1 = $arrIntendedToConnect;
            $remainingQuota = $maxQuota - $lengthIntendedConnect;
            if ($remainingQuota > 0) {
                $marksNumber1 = array_merge($marksNumber1, array_slice($arrContainer, 0, $remainingQuota));
                $remainingQuota -= min(count($arrContainer), $remainingQuota);
            }
            if ($remainingQuota > 0) {
                $marksNumber1 = array_merge($marksNumber1, array_slice($arrMarksAndNumber, 0, $remainingQuota));
            }
            $marksNumber2 = array_slice($arrMarksNumberData, $maxQuota);
        } else {
            // Jika jumlah $arrIntendedToConnect sendiri sudah melebihi maxQuota, potong langsung
            $marksNumber1 = array_slice($arrIntendedToConnect, 0, $maxQuota);
            $marksNumber2 = array_merge(array_slice($arrIntendedToConnect, $maxQuota), $arrContainer, $arrMarksAndNumber);
        }   

        
        //descriptions
        $descriptionPackagesAndGoods1 = array_slice($arrDescriptionOfGoodsData, 0, $maxDescQuota);
        $descriptionPackagesAndGoods2 = array_slice($arrDescriptionOfGoodsData, $maxDescQuota);


        if ($totalDescription <= $maxDescQuota) {
            $descriptionPackagesAndGoods1 = $arrDescriptionOfGoodsData;
            $descriptionPackagesAndGoods2 = [];
        } else {
            $descriptionPackagesAndGoods1 = array_slice($arrDescriptionOfGoodsData, 0, $maxDescQuota);
            $descriptionPackagesAndGoods2 = array_slice($arrDescriptionOfGoodsData, $maxDescQuota);
        }
        // $obj->setlog($descriptionPackagesAndGoods1, true);
        $marksNumberNotAttachment = '';
        $marksNumberAttachment = '';
        $descriptionPackagesAndGoodsNotAttachment = '';
        $descriptionPackagesAndGoodsAttachment = '';

        if(!empty($marksNumber1)) {
            $arrMarksNumbersNotAttachment = implode('', $marksNumber1);
            $marksNumberNotAttachment = $arrMarksNumbersNotAttachment;
        }
        
        if(!empty($marksNumber2)) {
            $arrMarksNumbersAttachment = implode('', $marksNumber2);
            $marksNumberAttachment = $arrMarksNumbersAttachment;
        }
        
        //description of packages and goods
        if(!empty($descriptionPackagesAndGoods1)) {
            $arrDescriptionPackagesAndGoodsNotAttachment = implode('', $descriptionPackagesAndGoods1);
            $descriptionPackagesAndGoodsNotAttachment = $arrDescriptionPackagesAndGoodsNotAttachment;
        }
        
        if(!empty($descriptionPackagesAndGoods2)) {
            $arrDescriptionPackagesAndGoodsAttachment = implode('', $descriptionPackagesAndGoods2);
            $descriptionPackagesAndGoodsAttachment = $arrDescriptionPackagesAndGoodsAttachment;    
        }
        
        //desc attachment
        if(!empty($arrDescriptionOfPackageAndGoodsAttachment)) {
            $arrDescGoodsAttachment = implode('', $arrDescriptionOfPackageAndGoodsAttachment);
            if(!empty($descriptionPackagesAndGoodsAttachment)) {
                $descriptionPackagesAndGoodsAttachment .= '<br>';
            }
            $descriptionPackagesAndGoodsAttachment .= '<br>'. $arrDescGoodsAttachment;
        }

        //breakdown attachment di page baru
        if (!empty($descriptionPackagesAndGoods2)) {
            //merge kalau ke 1 lebih masuk ke attachment
            $mergedDescription = array_merge(
                is_array($descriptionPackagesAndGoods2) ? $descriptionPackagesAndGoods2 : [$descriptionPackagesAndGoods2],
                is_array($arrDescriptionOfPackageAndGoodsAttachment) ? $arrDescriptionOfPackageAndGoodsAttachment : [$arrDescriptionOfPackageAndGoodsAttachment]
            );
        } else {
            $mergedDescription = is_array($arrDescriptionOfPackageAndGoodsAttachment) ? $arrDescriptionOfPackageAndGoodsAttachment : [$arrDescriptionOfPackageAndGoodsAttachment];
        }
        
        //$maxAttachmentQuota = 20; //MAX PER PAGES ATTACHMENT DESCRIPTION

        // $marksNumberPages = [];
        // if(!empty($marksNumber2)) {
        //     $marksNumberAttachment = implode('', $marksNumber2);
        //     $marksNumberAttachment = htmlspecialchars_decode(strtolower($marksNumberAttachment));
        //     $marksNumberAttachment = str_replace('&nbsp;', ' ', $marksNumberAttachment);
        //     $lineArray = array_map('trim', explode("\n", $marksNumberAttachment));
        //     $marksNumberPages = array_chunk($lineArray, $maxAttachmentQuota);
        // }

        // $descPages = [];
        // $plainAttachment = implode('', $mergedDescription);
        // $plainAttachment = htmlspecialchars_decode(strtolower($plainAttachment));
        // // $plainAttachment = str_replace('&nbsp;', ' ', $plainAttachment);
        // $lineArray = array_map('trim', explode("\n", $plainAttachment));
        // $descPages= array_chunk($lineArray, $maxAttachmentQuota);

        // $maxPages = max(count($marksNumberPages), count($descPages));

        // for ($i = 0; $i < $maxPages; $i++) {
        //     $attachmentPages[] = [
        //         'marksnumber' => $marksNumberPages[$i] ?? [],
        //         'description' => $descPages[$i] ?? []
        //     ];
        // }

        $maxAttachmentQuota = 50;

        $marksNumberPages = [];
        if (!empty($marksNumber2)) {
            $marksNumberAttachment = strtolower(implode('', $marksNumber2));
            $marksNumberAttachment = html_entity_decode($marksNumberAttachment, ENT_QUOTES | ENT_HTML5);
            $marksNumberAttachment = str_replace("\xC2\xA0", ' ', $marksNumberAttachment); // replace non-breaking space
            $lineArray = array_map('trim', explode("\n", $marksNumberAttachment));
            $marksNumberPages = array_chunk($lineArray, $maxAttachmentQuota);
        }

        $descPages = [];
        $plainAttachment = strtolower(implode('', $mergedDescription));
        $plainAttachment = html_entity_decode($plainAttachment, ENT_QUOTES | ENT_HTML5);
        $plainAttachment = str_replace("\xC2\xA0", ' ', $plainAttachment);
        $lineArray = array_map('trim', explode("\n", $plainAttachment));
        $descPages = array_chunk($lineArray, $maxAttachmentQuota);

        $maxPages = max(count($marksNumberPages), count($descPages));

        $attachmentPages = []; // pastikan di-reset
        for ($i = 0; $i < $maxPages; $i++) {
            $attachmentPages[] = [
                'marksnumber' => $marksNumberPages[$i] ?? [],
                'description' => $descPages[$i] ?? []
            ];
        }


        $marksNumberAttachment = isset($param['attachmentContent']) ? strtoupper(implode('',$param['attachmentContent']['marksnumber'])) : $marksNumberAttachment;
        $descriptionPackagesAndGoodsAttachment = isset($param['attachmentContent']) ? strtoupper(implode('',$param['attachmentContent']['description'])) : $descriptionPackagesAndGoodsAttachment;

        $arrTestXY = array();

        if($shippingType == EMKL['shipping']['sea']) {  

            $content = '
                <table cellpadding="2" style="font-size:1.1em; font-weight: bold">
                    <tr>
                        <td style="font-weight:bold;font-size:0.9em; text-align:left; width:338px;">' . implode('<br>', $arrShipper) . '</td>
                    </tr>
                </table>';//shipper content

            $content2 = '
                <table> 
                    <tr>
                        <td style="width:140px;font-weight:bold;font-size:1.3em;font-weight:bold;">'. $HBLCode .'</td>
                    </tr>
                </table>';//hbl content

            $content3 = '
                <table style="font-size:1.1em; font-weight: bold">
                    <tr>
                        <td  style="width:338px;font-size:0.9em;font-weight:bold;">' . strtoupper(htmlspecialchars_decode($rs[0]['consigneename'])) . '<br>' . str_replace(chr(13), '<br>', strtoupper(htmlspecialchars_decode($rs[0]['consigneeaddress']))) . '</td>
                    </tr>
                </table>';//consignee content

            $content4 = '
                <table style="font-size:1.1em; font-weight: bold" cellpadding="2">
                    <tr>
                        <td  style="width:338px;font-size:0.9em;font-weight:bold;">' . strtoupper(htmlspecialchars_decode($rsData[0]['carriername'])) . '<br>' . str_replace(chr(13), '<br>', strtoupper(htmlspecialchars_decode($rsData[0]['carrieraddress']))) . '</td>
                    </tr>
                </table>';//carrier  content

            $content5 = '
                <table style="font-size:1.1em; font-weight: bold" cellpadding="2">
                    <tr>
                        <td  style="width:174px;font-size:0.9em;font-weight:bold;">' . strtoupper($polName) . '</td>
                        <td  style="width:169px;font-size:0.9em;font-weight:bold;">' . strtoupper($polName) . '</td>
                    </tr>
                </table>';//port of receipt and port of loading content
            
            $content6 = '
                <table style="font-size:1.1em; font-weight: bold" cellpadding="2">
                    <tr>
                        <td  style="width:174px;font-size:0.9em;font-weight:bold;">' . $feederName . '</td>
                        <td  style="width:169px;font-size:0.9em;font-weight:bold;">'. $feederNumber .'</td>
                    </tr>
                </table>';//vessel and voyage content
            
            $content7 = '
                <table style="font-size:1.1em; font-weight: bold" cellpadding="2">
                    <tr>
                        <td  style="width:174px;font-size:0.9em;font-weight:bold;">' . strtoupper($podName) . '</td>
                        <td  style="width:183px;font-size:0.9em;font-weight:bold;">'. strtoupper($placeOfDelivery) .'</td>
                        <td  style="width:338px;font-size:0.9em;font-weight:bold;">'. $finalDestination .'</td>
                    </tr>
                </table>';//port of discharge, place of delivery and final destination content
            
            // $content8A = '
            //     <table style="font-size:1.1em; font-weight: bold" cellpadding="2">
            //         <tr>
            //             <td  style="width:255px;font-size:0.9em;font-weight:bold;">'. $marksNumber.'</td>
            //         </tr>
            //     </table>';//marks and number, description of packages and good, and GW  content

            if(!$attachment) {
            $content8A = '
                <table style="font-size:1.1em; font-weight: bold" cellpadding="2">
                    <tr>
                        <td  style="width:255px;font-size:0.9em;font-weight:bold;">'.$marksNumberNotAttachment.'</td>
                    </tr>
                </table>';//marks and number, description of packages and good, and GW  content
            } else {
                $content8A = ' <table style="font-size:1.1em; font-weight: bold" cellpadding="2">
                    <tr>
                        <td  style="width:255px;font-size:0.9em;font-weight:bold;">'.$marksNumberAttachment.'</td>
                    </tr>
                </table>';//marks and number, description of packages and good, and GW  content
            }
            
            // $content8B = '
            //     <table style="font-size:1.1em; font-weight: bold" cellpadding="2">
            //         <tr>
            //             <td  style="width:255px;font-size:0.9em;font-weight:bold;">'.$containerNOS.'</td>
            //         </tr>
            //     </table>';//marks and number, description of packages and good, and GW  content

            // $content10 = '
            //     <table style="font-size:1.1em; font-weight: bold" cellpadding="2">
            //         <tr>
            //             <td  style="width:250px;font-size:0.9em;font-weight:bold;">'.$descriptionOfPackageAndGoods.'</td>
            //         </tr>
            //     </table>';//marks and number, description of packages and good, and GW  content

            if(!$attachment) {
                $content10 = '
                    <table style="font-size:1.1em; font-weight: bold" cellpadding="2">
                        <tr>
                            <td  style="width:270px;font-size:0.9em;font-weight:bold;">'.$descriptionPackagesAndGoodsNotAttachment.'</td>
                        </tr>
                    </table>';//marks and number, description of packages and good, and GW  content
            
            } else {
                $content10 = '
                    <table style="font-size:1.1em; font-weight: bold" cellpadding="2">
                        <tr>
                            <td  style="width:270px;font-size:0.9em;font-weight:bold;">'. $descriptionPackagesAndGoodsAttachment.'</td>
                        </tr>
                    </table>';//marks and number, description of packages and good, and GW  content
                }

            $content9 = '
                <table  style="font-size:1.1em; font-weight: bold" cellpadding="2">
                    <tr>
                        <td  style="width:200px;font-size:0.9em;font-weight:bold;">'.$GW_NW.'</td>
                    </tr>
                </table>';//GW_NW  content

            // $content10 = '<div style="font-size:1.1em; font-weight: bold; text-align:right; width:80px;border:1px solid #000000">'.$MEAS.'</div>';//MEAS  content
            // $content10 = '
            //     <table style="font-size:1.1em; font-weight: bold;" cellpadding="2">
            //         <tr>
            //             <td  style="width:200px;font-size:0.9em;font-weight:bold;">'.$MEAS.'</td>
            //         </tr>
            //     </table>';//MEAS  content

            $content11 = '
                <table style="font-size:1.1em; font-weight: bold" cellpadding="2">
                    <tr>
                        <td  style="width:320px;font-size:0.9em;font-weight:bold;">'.$agentInformation.'</td>
                    </tr>
                </table>';//Agent  content

            $content12 = ' <table style="font-size:1.1em; font-weight: bold" cellpadding="2">
                    <tr>
                        <td  style="width:180px;font-size:0.9em;font-weight:bold;">'.$alsoNotifyParty.'</td>
                    </tr>
                </table>';//also notify party content


            $contentPkgs = '<table style="font-size:1.1em; font-weight: bold" ><tr><td style="width:80px;font-size:0.9em;font-weight:bold;">' . $NO_OF_PKGS . '</td></tr></table>';

            $descriptionCustom = '
                    <table style="width:350px;text-align:left;   font-weight: bold">
                        <tr><td >SHIPPER`S LOAD COUNT AND SEAL</td></tr>
                        <tr><td >SHIPPED ON BOARD ' . $feederName . ' '. $feederNumber .' </td></tr>
                        <tr><td >'.strtoupper($obj->formatDBDate($rs[0]['trdate'],'M d, Y',array('returnOnEmpty' => true))).' AT ' . $polName . '</td></tr>
                    </table>
            ';

            $contentFooter = '
                <table style="font-size:1.1em; font-weight: bold" cellpadding="2">
                    <tr>
                        <td class="border-bottom border-right border-top" style="font-size:0.9em;font-weight:bold;text-align:center;width:160px"> </td>
                        <td class="border-bottom border-top" style="font-size:0.9em;font-weight:bold;;width:517px">' . $noOfPkgs . '</td>
                    </tr>
                </table>
            ';

            $contentFooter1 = '
                <table style="font-size:1.1em; font-weight: bold" cellpadding="2">
                    <tr>
                        <td  style="width:180px;font-size:0.9em;font-weight:bold;">' . $freightCharges . '</td>
                        <td  style="width:80px;font-size:0.9em;font-weight:bold;">' . $paymentTypePrepaid . '</td>
                        <td  style="width:80px;font-size:0.9em;font-weight:bold;">' . $paymentTypeCollect . '</td>
                    </tr>
                </table>';//freight and charge, prepaid and collect content
            
                $contentFooter2 = '
                    <table style="font-size:1.1em; font-weight: bold" cellpadding="2">
                        <tr>
                            <td  style="width:200.5px;font-size:0.9em;font-weight:bold;">' . strtoupper($prepaidAt) . '</td>
                            <td  style="width:158.5px;font-size:0.9em;font-weight:bold;">' . strtoupper($payableAt) . '</td>
                        </tr>
                    </table>';//prepaid at and payable at content
                
                    $contentFooter3 = '
                        <table style="font-size:1.1em; font-weight: bold" cellpadding="2">
                            <tr>
                                <td  style="width:158.5px;font-size:0.9em;font-weight:bold;">' . $numberOfOriginal . '</td>
                            </tr>
                        </table>
                    ';//number of original content

                    $contentFooter4 = '
                        <table style="font-size:1.1em; font-weight: bold" cellpadding="2">
                            <tr>
                                <td  style="width:338px;font-size:0.9em;font-weight:bold;">JAKARTA, ' . strtoupper($obj->formatDBDate($rs[0]['etdpol'], 'M d, Y', array('returnOnEmpty' => true))) . '</td>
                            </tr>
                        </table>
                    ';//place and date issue content
                    $contentFooter5 = '
                        <table style="font-size:1.1em; font-weight: bold" cellpadding="2">
                            <tr>
                                <td  style="width:338px;font-size:0.9em;font-weight:bold;">' . strtoupper($obj->formatDBDate($rs[0]['trdate'], 'M d, Y', array('returnOnEmpty' => true))) . '</td>
                            </tr>
                        </table>
                    ';//on board date content
                    $contentByInformation = '<table style="font-size:1.1em; font-weight: bold" cellpadding="2">
                        <tr><td style="font-weight:bold;">'. strtoupper($byInformationData) .'</td></tr>
                        <tr><td></td></tr>
                    </table>';
                    $contentBy2Information = '<table style="font-size:1.1em; font-weight: bold" cellpadding="2">
                        <tr>
                            <td style="font-size:1em;font-weight:bold;text-align:center;width:280px">' . strtoupper($by2InformationData) . '</td>
                        </tr>
                        </table></td>';

                    
                
                    $contentAttachment1 = '<table><tr><td style="width:150px;font-weight:bold;">'. $HBLCode .'</td></tr></table>';

                        
                        
            if(!$attachment) { 
                array_push($arrTestXY,array('x' => 11, 'y' => 13, 'content' => $content));
                array_push($arrTestXY,array('x' => 168, 'y' => 15, 'content' => $content2));
                array_push($arrTestXY, array('x' => 11, 'y' => 43, 'content' => $content3));
                array_push($arrTestXY, array('x' => 11, 'y' => 70, 'content' => $content4));
                array_push($arrTestXY, array('x' => 12, 'y' => 100, 'content' => $content5));
                array_push($arrTestXY, array('x' => 12, 'y' => 106.6, 'content' => $content6));
                array_push($arrTestXY, array('x' => 12, 'y' => 114, 'content' => $content7));
    
                
                array_push($arrTestXY, array('x' => 62, 'y' => 133, 'content' => $contentPkgs));



                array_push($arrTestXY, array('x' => 12, 'y' => 132, 'content' => $content8A));
                //array_push($arrTestXY, array('x' => 12, 'y' => 175, 'content' => $content8B));
                array_push($arrTestXY, array('x' => 84, 'y' => 132, 'content' => $content10));
                array_push($arrTestXY, array('x' => 162, 'y' => 132, 'content' => $content9));
                // array_push($arrTestXY, array('x' => 162, 'y' => 147, 'content' => $content10));
                array_push($arrTestXY, array('x' => 113, 'y' => 226, 'content' => $content11));
                array_push($arrTestXY, array('x' => 78, 'y' => 200, 'content' => $descriptionCustom));

                 array_push($arrTestXY, array('x' => 12, 'y' => 220, 'content' => $content12));
                
                //FOOTER
                // array_push($arrTestXY, array('x' => 16, 'y' => 190, 'content' => $contentFooter));
                array_push($arrTestXY, array('x' => 16.5, 'y' => 225, 'content' => $contentFooter1));
                array_push($arrTestXY, array('x' => 114, 'y' => 252, 'content' => $contentFooter2));
                array_push($arrTestXY, array('x' => 114, 'y' => 263, 'content' => $contentFooter3));
                array_push($arrTestXY, array('x' => 45, 'y' => 280, 'content' => $contentFooter4));
                array_push($arrTestXY, array('x' => 46, 'y' => 283.5, 'content' => $contentFooter5));
            
            } else {

                array_push($arrTestXY, array('x' => 60, 'y' => 8.5, 'content' => $contentAttachment1));

                array_push($arrTestXY, array('x' => 21, 'y' => 20, 'content' => $content8A));
                array_push($arrTestXY, array('x' => 92, 'y' => 20, 'content' => $content10));

                // array_push($arrTestXY, array('x' => 12, 'y' => 132, 'content' => $content8A));
                // array_push($arrTestXY, array('x' => 84, 'y' => 138, 'content' => $content10));
            }

            if(!$attachment) {
                array_push($arrTestXY, array('x' => 135, 'y' => 282, 'content' => $contentByInformation));
                array_push($arrTestXY, array('x' => 120, 'y' => 290, 'content' => $contentBy2Information));
            }

            if ($rs[0]['isrelease'] == 1 && !$attachment) {
                $myX = 155;
                $myY = 185;

                //$surrenderHTML = '<div class="surrender" style="color:#f8a0a4; width:10px;  border:1px solid #f8a0a4; font-weight:bold;  font-size: 2em; text-align:center;">SURRENDER</div>';
                $surrenderHTML = '<table><tr><td style="width:180px"><div class="surrender" style="color:#000; width:10px;  border:1px solid #000; font-weight:bold;  font-size: 2em; text-align:center;">SURRENDERED</div></td></tr></table>';
                array_push($arrTestXY, array('x' => $myX, 'y' => $myY, 'content' => $surrenderHTML));
            }


        }else  if($shippingType == EMKL['shipping']['air']) {
            
            //AIR

            $content = '<table celpadding="2">
                <tr>
                    <td style="height:40px; width:198px;  font-size:1.2em; font-weight:bold;">' . $rs[0]['prefix'] . '' . $rs[0]['code'] . '</td>
                </tr>
            </table>';//Air Way Bill Number Content

            $content2 = '<table celpadding="2">
                <tr>
                    <td style="height:40px; width:140px; font-size:1.2em; font-weight:bold;">' . $rsJobOrder[0]['mblnumber'] . '</td>
                </tr>
            </table>'; //MASTER AIR WAY BILL Content

            $content3 = '<table celpadding="2">
                <tr>
                    <td style="width:300px; font-size:0.9em; font-weight:bold;">' . implode('<br>', $arrShipper) . '</td>
                </tr>
            </table>'; //shipper content

            $content4 = '<table celpadding="2">
                <tr>
                    <td style="width:300px; font-size:0.9em; font-weight:bold;">' . strtoupper(htmlspecialchars_decode($rs[0]['consigneename'])) . '<br>' . str_replace(chr(13), '<br>', strtoupper(htmlspecialchars_decode($rs[0]['consigneeaddress']))) . '</td>
                </tr>
            </table>'; //consignee content

            $content5 = '<table celpadding="2">
                <tr>
                    <td style="width:338px; font-size:0.9em; font-weight:bold;">' . $agentAir . '</td>
                </tr>
            </table>'; //agent content

            $content6 = '<table celpadding="2">
                <tr>
                    <td style="width:334px; font-size:0.9em; font-weight:bold;">' . $text . '</td>
                </tr>
            </table>'; //tesx It is .... content


            $content7 = '<table celpadding="2">
                <tr>
                    <td style="width:169px; font-size:0.9em; font-weight:bold;"> </td>
                    <td style="width:169px; font-size:0.9em; font-weight:bold;"> </td>
                </tr>
            </table>'; //gent`s IATA Code, Account No. content

            
            $content8 = '<table celpadding="2">
                <tr>
                    <td style="width:338px font-size:0.9em; font-weight:bold;">' . $polName . '</td>
                </tr>
            </table>'; //POL Content
            
            $content9 = '<table celpadding="2">
                <tr>
                    <td style="width:338px font-size:0.9em; font-weight:bold;">'. $paymentPrepaidCollect .'</td>
                </tr>
            </table>'; //Accounting Information

            $content10 = '<table celpadding="2">
                <tr>
                    <td style="text-align:center;font-size:1em;width:35px;font-weight:bold;">'. $connectingAirport .'</td>
                    <td style="text-align:center;font-size:1em;width:146px;font-weight:bold;">&nbsp;' . $firstByCarrier . '</td>
                    <td style="text-align:center;font-size:1em;width:45px;font-weight:bold;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'. $connecting2Airport .'</td>
                    <td style="text-align:center;font-size:1em;width:40px;font-weight:bold;">&nbsp;&nbsp;' . $connectingCode . '</td>
                    <td style="text-align:center;font-size:1em;width:40px;font-weight:bold;">&nbsp;&nbsp;'. $connecting3Airport .'</td>
                    <td style="text-align:center;font-size:0.1em;width:40px;font-weight:bold;">&nbsp;&nbsp;' . $connecting2Code . '</td>
                </tr>
            </table>'; //to, By First Carier, to content

            $content11 = '<table celpadding="2">    
                <tr>
                    <td style="text-align:center;font-size:1em;width:40px;font-weight:bold;"> '. $currencyName .' </td>
                    <td style="text-align:center;font-size:1em;width:20px;font-weight:bold;"> PP </td>
                </tr>
            </table>'; //CHGS content

            $content12 = '<table celpadding="2">
                <tr>
                    <td style="width:156px;font-size:1em;font-weight:bold;">' . $finalDestination . '</td>
                </tr>
            </table>'; //Air port destination content
        
    
            $content13 = '<table celpadding="2">
                <tr>
                    <td style="width:576px;font-size:0.9em;font-weight:bold;">' . $notifyParty . '</td>
                </tr>
            </table>'; //notify party content

            $content14 = '<table celpadding="2">
                <tr>
                    <td style="width:30px;font-size:1em;font-weight:bold;">' . $obj->formatNumber($totalPkgs) . '</td>
                    <td style="width:40px;font-size:1em;font-weight:bold;">' . $obj->formatNumber($GW, 3) . '</td>
                    <td style="width:10px;font-size:1em;font-weight:bold;"></td>
                    <td style="width:10px;font-size:1em;font-weight:bold;"></td>
                    <td style="width:10px;font-size:1em;font-weight:bold;"></td>
                    <td style="width:20px;font-size:1em;font-weight:bold;"></td>
                    <td style="width:65px;font-size:1em;font-weight:bold;"></td>
                    <td style="width:50px;font-size:1em;font-weight:bold;">' . $obj->formatNumber($CW, 3) . '</td>
                </tr>
            </table>'; //No of pieces, and GW content
            $content15 = '<table celpadding="2">
                <tr>
                    <td style="width:348px;font-size:0.9em;font-weight:bold;">' . str_replace(chr(13), '<br>', $description) . '</td>
                </tr>
            </table>'; //Nature and Quality of Goods content
        
            

            $content16 = '<table><tr><td style="width:100px; font-weight:bold;font-size:0.9em">AS ARRANGED</td></tr></table>';
            
            $content17 = '<table><tr><td style="width:100px; font-weight:bold;font-size:0.7.5em">AS ARRANGED</td></tr></table>';
            $content19 = '<table><tr><td class="border-right" style="font-size:0.7em;font-weight:bold;text-align:center;width:96px">NVD</td>
                        <td class="border-right" style="font-size:0.7em;font-weight:bold;text-align:center;width:96px">NVC</td></tr></table>';
            $content20 = '<table><tr><td style="width:37px"></td><td style="width:133px;text-align:center;">' . $obj->formatDBDate($rs[0]['trdate'], 'M, d Y', array('returnOnEmpty' => true)) . '</td> <td style="width:148px;text-align:center;">'. $placeOfDelivery .'</td> <td style="width:133px;text-align:center;">CIF - JKT</td> <td style="width:37px"></td></tr></table></td></tr>
                    <tr><td></td></tr>
                    <tr><td></td></tr>
                    <tr><td style="width:10px"></td><td style="border-bottom:1px dotted solid;width:450px"></td><td style="width:10px"></td></tr>
                    <tr><td></td></tr>
                    </table>';

            $content18 = '<table celpadding="2"><tr><td style="width:60px;font-size:0.9em;font-weight:bold;text-align:center;">&nbsp;&nbsp;'. $flightDate1 .'</td><td style="width:55px;font-size:0.7em;font-weight:bold;text-align:center;"></td><td style="width:60px;font-size:0.9em;font-weight:bold;">&nbsp;&nbsp;'. $flightDate2 .'</td></tr></table>';
            $content21 = '<table><tr><td>PT. CIF TRANSPORTATION INDONESIA</td></tr></table>';
            $shipTo = '';
            if (!empty($rs[0]['shipto'])) {
                $shipTo = '
                    <table style="width:270px;text-align:left;">
                        <tr><td style="font-weight:bold;width:60px;">SHIP TO : </td><td style="width:220px;font-weight:bold;">' . strtoupper(htmlspecialchars_decode(nl2br($rs[0]['shipto']))) . '</td></tr>
                    </table>
            ';
            }

            array_push($arrTestXY, array('x' => 23, 'y' => 16, 'content' => $content));
            array_push($arrTestXY, array('x' => 69, 'y' => 16, 'content' => $content2));
            array_push($arrTestXY, array('x' => 18, 'y' => 29, 'content' => $content3));
            array_push($arrTestXY, array('x' => 18, 'y' => 54, 'content' => $content4));
            array_push($arrTestXY, array('x' => 18, 'y' => 80, 'content' => $content5));
            // array_push($arrTestXY, array('x' => 107, 'y' => 81, 'content' => $content6)); 
            array_push($arrTestXY, array('x' => 16, 'y' => 101, 'content' => $content7)); 

            array_push($arrTestXY, array('x' => 18, 'y' => 102, 'content' => $content8));
            array_push($arrTestXY, array('x' => 120, 'y' => 97, 'content' => $content9));
            array_push($arrTestXY, array('x' => 16, 'y' => 110.5, 'content' => $content10));
            array_push($arrTestXY, array('x' => 105, 'y' => 112, 'content' => $content11));
            array_push($arrTestXY, array('x' => 18, 'y' => 119.5, 'content' => $content12));
            array_push($arrTestXY, array('x' => 48, 'y' => 129, 'content' => $content13));

            array_push($arrTestXY, array('x' => 18, 'y' => 152, 'content' => $content14));
            // body right
            array_push($arrTestXY, array('x' => 147, 'y' => 152, 'content' => $content15));
            array_push($arrTestXY, array('x' => 91, 'y' => 152, 'content' => $content16));
            array_push($arrTestXY, array('x' => 61, 'y' => 120, 'content' => $content18));
            array_push($arrTestXY, array('x' => 147, 'y' => 112, 'content' => $content19));
            array_push($arrTestXY, array('x' => 75, 'y' => 272, 'content' => $content20));
            array_push($arrTestXY, array('x' => 109, 'y' => 251, 'content' => $content21));
            
            if (!$attachment) {
                //array_push($arrTestXY, array('x' => 10, 'y' => 204, 'content' => $contentFooter));
                $x = ($freightTerm == '1' ? 18 : 59); //Prepaid / Collect
                $x2 = ($freightTerm == '1' ? 18 : 59); //Prepaid / Collect
                array_push($arrTestXY, array('x' => $x, 'y' => 214, 'content' => $content17));
                array_push($arrTestXY, array('x' => $x2, 'y' => 268 , 'content' => $content17));
                array_push($arrTestXY, array('x' => 30, 'y' => 180, 'content' => $shipTo));
            }

        }

        return $arrTestXY;
    };

    $obj = new EMKLHouseBL();
    $emklJobOrder = new EMKLJobOrder();

    $rs = $dataset['rs'];

    $rsJobOrder = $emklJobOrder->getDataRowById($rs[0]['refheaderkey']);

    $shippingtype = $rsJobOrder[0]['transportationtypekey'];
    
    $isShowTitle = isset($_GET['showTitle']) ? $_GET['showTitle'] : 1;
    $isShowBorder = isset($_GET['showBorder']) ? $_GET['showBorder'] : 1;


    $returnHTML = array();
    // foreach ($arrCopy as $index => $label) {
        $needAttachment = false;
        $index = 0;
        
        $arrTestXY = $setXYContent($dataset, array('attachment' => false, 'originalLabelKey' => $index, 'shippingtype' => $shippingtype, 'showTitle' => $isShowTitle, 'showBorder' => $isShowBorder));
        $html = $generateHeaderTable($dataset, array('attachment' => false, 'originalLabelKey' => $index, 'shippingtype' => $shippingtype, 'showTitle' => $isShowTitle, 'showBorder' => $isShowBorder));
        //$html .= ($needAttachment) ? '<div style="width:680px; text-align:center;"><b>** TO BE CONTINUED ON ATTACHED LIST **</b></div>' : '';
        $html .= $generateFooterTable($dataset, array('attachment' => false, 'originalLabelKey' => $index, 'shippingtype' => $shippingtype, 'showTitle' => $isShowTitle, 'showBorder' => $isShowBorder));
        array_push($returnHTML,array('html' =>$html,'content'=> $arrTestXY, 'shippingtype' => $shippingtype, 'showTitle' => $isShowTitle, 'showBorder' => $isShowBorder));
        // kalo ada attachment
        if ($needAttachment) {
            // $html = $generateHeaderTable($dataset, array('attachment' => true, 'shippingtype' => $shippingtype, 'showTitle' => $isShowTitle, 'showBorder' => $isShowBorder));
            $arrTestXY = $setXYContent($dataset, array('attachment' => true, 'originalLabelKey' => $index, 'shippingtype' => $shippingtype, 'showTitle' => $isShowTitle, 'showBorder' => $isShowBorder));
            // array_push($returnHTML, array('html' => $html, 'content' => $arrTestXY, 'shippingtype' => $shippingtype, 'showTitle' => $isShowTitle, 'showBorder' => $isShowBorder));

            for ($i = 0; $i < count($attachmentPages); $i++) {
                $pageContent = $attachmentPages[$i];
                $html = $generateHeaderTable($dataset, array('attachment' => true, 'shippingtype' => $shippingtype, 'showTitle' => $isShowTitle, 'showBorder' => $isShowBorder));
                $arrTestXY = $setXYContent($dataset, array('attachment' => true, 'originalLabelKey' => $index, 'shippingtype' => $shippingtype, 'showTitle' => $isShowTitle, 'showBorder' => $isShowBorder, 'attachmentContent' => $pageContent));
                array_push($returnHTML, array('html' => $html, 'content' => $arrTestXY, 'shippingtype' => $shippingtype, 'showTitle' => $isShowTitle, 'showBorder' => $isShowBorder));
            }
        }
    // }
     
    
    return $returnHTML;
};

$generateReportContent = array();
array_push($generateReportContent, array('content' => $content));

?>
