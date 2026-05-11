<?php  

$PRINT_SETTINGS =  array(   
    'showPrintHeader' => false,
    'footer' => '',
    'pdfMarginHeader' => 8, 
	'marginFooter' => 6
);


includeClass(array('EMKLHouseBL.class.php','Vessel.class.php', 'ItemUnit.class.php'));
$emklHBL = new EMKLHouseBL();
$vessel =  new Vessel();
$itemUnit = new ItemUnit();

$arrContainers = array();
$arrCopy = array('ORIGINAL', 'COPY');
$needAttachment = false;

$obj = $emklHBL;

$content = function ($dataset) {
    global $needAttachment;
    global $arrCopy;

    $generateHeaderTable = function ($dataset, $param) {

        global $pdf;
        global $arrContainers;
        global $needAttachment;
        global $paperSetting;
        global $arrCopy;

        

        $obj = new EMKLHouseBL();
        $emklJobOrder = new EMKLJobOrder();
        $itemUnit = new ItemUnit();

        $rs = $dataset['rs'];
        $attachment = $param['attachment'];

        $html = $obj->printSetting['defaultStyle'];

        if (!empty($rs[0]['description'])) {
            $needAttachment = true; // kondisi ketika perlu attachment
        }

        $heightDesc = (!$attachment) ? 'height:200px' : 'height:480px';


        if(!$attachment){ 
            // untuk jenis halaman pertama
            $description =  $rs[0]['shortdescription'];
            $attachtmentBorder = '';
        }else{
            // untuk jenis attachment
            $description =  $rs[0]['description'];
            $attachtmentBorder = 'border-bottom:1px solid #333';
            $heightDesc =  'height:480px';
        }




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
                    .text-top{vertical-align: top;}
                    .middle{vertical-align: middle;}
                    .bottom{vertical-align: bottom;}
                    .initial{vertical-align: initial;}
                    .text-center{ text-align:center;}
                    .capital {text-transform: uppercase;}
                    .border-right-dotted {
                        border-right:1px dashed 2E2E84;
                    }
                    
                    .bg-grey {
                        background-color:#D3D3D3
                    }
                </style>';

        $html .= '
            <table cellpadding="4">
                <tr>
                    <td class="border-left border-top border-left border-right head-title" style="width:338px;">Exporter</td>
                    <td rowspan="3" class="head-title" style="width:198px;font-size:15px;">HOUSE BILL OF LADING<br></td>
                    <td class="border-left border-top border-right head-title" style="width:140px;">House Bill of Lading</td>
                </tr>
                <tr>
                    <td class="border-left border-right border-top"></td>
                    <td class="border-right border-left border-top border-bottom"></td>
                </tr>  

                <tr>
                    <td class="border-left border-right" style="height:50px;"></td>
                    <td></td>
                </tr>

                <tr>
                    <td class="border-left border-top border-left border-right head-title" style="width:338px;">Consignee</td>
                    <td class="head-title" style="width:338px;"> </td>
                </tr>
                <tr>
                    <td class="border-left border-right border-top"></td>
                    <td class=""></td>
                </tr>
                <tr>
                    <td class="border-left border-right" style="height:50px;"></td>
                    <td class=""></td>
                </tr> 

                <tr>
                    <td class="border-left border-top border-left border-right head-title" style="width:338px;">Notify Party</td>
                    <td rowspan="5" class="" style="width:338px;"><span style="font-size:0.8em;text-align:justify;">Received by the Carrier, the Goods  as specified below in apparent good order and condition unless otherwise stated, to be transported to such place as agreed, authorised or permitted herein and subject to all the terms and conditions appearing on th front and revers of this Bill of lading to witch the Merchant agrees by accepting this Bill of Lading, anu Local privileges and customs not withstanding.</span>
                        <br><span style="font-size:0.8em;text-align:justify;">The particulars given below as stated by the the shipper and the weight, measure, quantity, condition, contents and value of the Goods are unknown to the Carrier.</span>
                        <br><span style="font-size:0.8em;text-align:justify;">In WITNESSm whereof one (1) original Bill of Lading has been signed if not otherwise stated below, the same being accomplished the other(s), if any to be void. If required by the Carrier one (1) original Bill of Lading must be surrendered duly endorsed in exchange for the Goods or deliver order.</span>
                    </td>
                </tr>
                <tr>
                    <td class="border-left border-right border-top"></td>
                </tr> 
                <tr>
                    <td class="border-left border-right"style="height:50px;"></td>
                </tr> 

                <tr>
                    <td class="border-left border-top border-left border-right head-title" style="width:228px;">Vessel</td>
                    <td class="border-left border-top border-left border-right head-title" style="width:110px;">Voyage</td>
                </tr>
                <tr>
                    <td class="border-left border-right border-top"></td>
                    <td class="border-right border-top"></td>
                </tr> 

            </table>
        ';

        $html .= '<table cellpadding="4">
                <tr>
                    <td class="border-left border-top border-right head-title" style="width:169px;">Port of Discharge</td>
                    <td class="border-top border-right head-title" style="width:169px;">Destination (if on carr)</td>
                    <td class="border-top border-right head-title text-center" style="width:169px;">Port of Loading</td>
                    <td class="border-top border-right head-title text-center" style="width:169px;">Release</td>
                </tr>
                <tr>
                    <td class="border-left border-right border-top"></td>
                    <td class="border-right border-top"></td>
                    <td class="border-left border-right border-top"></td>
                    <td class="border-right border-top"></td>
                </tr> 
        </table>';
        
        $html .= '<table cellpadding="4">
                <tr>
                    <td class="border-left border-top border-right head-title" style="width:169px;">Shipped On Board</td>
                    <td class="border-top border-right head-title" style="width:169px;">Print Date</td>
                    <td class="border-top border-right head-title text-center" style="width:169px;">Freight Payable At</td>
                    <td class="border-top border-right head-title text-center" style="width:169px;">No. of Original B/L</td>
                </tr>
                <tr>
                    <td class="border-left border-right border-top"></td>
                    <td class="border-right border-top"></td>
                    <td class="border-left border-right border-top"></td>
                    <td class="border-right border-top"></td>
                </tr> 
        </table>';

        $html .= '<table cellpadding="2">
            <tr>
                <td class="text-center border-left border-top border-right" style="width:676px;">Details of cargo declared by Shipper</td>
            </tr>
        </table>';

        $html .= '
            <table cellpadding="2">
                <tr>
                    <td class="border-top border-left border-right head-title border-bottom" style="width:180px;">Marks and Numbers</td>
                    <td class="border-top border-right head-title border-bottom" style="width:340px;">Description of Goods</td>
                    <td class="border-top border-right border-right head-title border-bottom" style="width:78px;">Gross Mass</td>
                    <td class="border-top border-right head-title border-bottom" style="width:78px;">Cubic (M3)</td>
                </tr>';

        if ($attachment) {
            $html .= '<tr><td colspan="3" style="text-align:center"><b>** CONTINUATION **</b></td></tr>';
        }

        $html .='
                <tr>
                    <td class="border-right border-left border-bottom" rowspan="2" style="'.$heightDesc.'"></td>
                    <td class="border-right" style="'.$heightDesc.'"></td>
                    <td class="border-right border-bottom" rowspan="2" style="'.$heightDesc.'"></td>
                    <td class="border-right border-bottom" rowspan="2" style="'.$heightDesc.'"></td>
                </tr>
                <tr>
                    <td class="border-right border-bottom"><span style="text-decoration:italic;">*Shipper load and count</span></td>
                </tr>
            </table>
        ';

        return $html;

    };

    $generateFooterTable = function ($dataset, $param) {
        global $arrCopy;

        $obj = new EMKLHouseBL();
        $emklJobOrder = new EMKLJobOrder();
        $customer = new Customer();
        $setting = new Setting();

        $rs = $dataset['rs'];

        if (!empty($rs[0]['description'])) {
            $needAttachment = true; // kondisi ketika perlu attachment
        }

        $attachment = $param['attachment'];

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
                    .text-top{vertical-align: top;}
                    .middle{vertical-align: middle;}
                    .bottom{vertical-align: bottom;}
                    .initial{vertical-align: initial;}
                    .text-center{ text-align:center;}
                    .capital {text-transform: uppercase;}
                    .border-right-dotted {
                        border-right:1px dashed 2E2E84;
                    }
                    
                    .bg-grey {
                        background-color:#D3D3D3
                    }
                </style>';

             // header container
                $html .='<table> 
                <tr>
                    <td class="border-left border-bottom" style="width:100px;height:78px;"> Container</td>
                    <td class="border-bottom" style="width:100px; ">Seal</td>
                    <td class="border-bottom" style="width:70px;">Types</td>
                    <td class="border-bottom" style="width:70px; ">Weight(KG)</td> 
                    <td class="border-bottom" style="width:75px; ">Vol (M3)</td>
                    <td class="border-bottom" style="width:75px; ">Packages</td>
                    <td class="border-bottom border-right" style="width:186px;">Mode</td>
                </tr>
        </table>';

                $html .= '<table cellpadding="2">
                <tr>
                    <td class="border-left border-top border-left border-right head-title text-center" style="width:338px;">Delivery Agent</td>
                    <td class="border-left border-top border-left border-right head-title text-center" style="width:338px;">Freight and Charges</td>
                </tr>
                <tr>
                    <td class="border-left border-right border-top" height="50px;"><span style="font-size:0.9em">WSI<br>1000 LAKES DR STE 320 WES<br>COVINA CA 91790-2938<br>phone +1 626-448-1818</span></td>
                    <td rowspan="6" class="border-right border-top"></td>
                </tr> 

                <tr>
                    <td class="border-left border-right" style="width:338px;"><span style="font-size:0.7em;">In witness of the contract herein contained, the above stated number of originals Bills of Lading have been issued, one of witch to be accomplished, the others(s) being void.</span></td>
                </tr>
                <tr>
                    <td class="border-left border-right" style="width:338px;">AS CARRIER</td>
                </tr>

                <tr>
                    <td class="border-left border-right" style="height:40px;"> </td>
                </tr>

                <tr>
                    <td class="border-left" style="width:169px;">Place Of Issue :</td>
                    <td class="border-right" style="width:169px;">Date Of Issue :</td>
                </tr>   
                <tr>
                    <td class="border-left"></td>
                    <td class="border-right"></td>
                </tr>

                <tr>
                    <td class="border-left border-top head-title text-center" style="width:169px;">Place of Acceptance :</td>
                    <td class="border-left border-top border-right head-title text-center" style="width:169px;">Date of Delivery:</td>
                    <td class="border-top border-right head-title text-center" style="width:338px;">Total No. of Packages</td>
                </tr>
                <tr>
                    <td class="border-top border-left border-right border-bottom"></td>
                    <td class="border-right border-top border-bottom"></td>
                    <td class="border-top border-right border-right border-bottom"></td>
                </tr>

        </table>';


        return $html;
    };

    $setXYContent = function ($dataset, $param) {

        global $pdf;
        global $arrCopy;
        global $arrContainers;
        //global $containerIndex;
        global $needAttachment;
        global $paperSetting;

        $obj = new EMKLHouseBL();
        $emklJobOrder = new EMKLJobOrder();    
        $customer = new Customer();
        $port = new Port();
        $vessel = new Vessel();
        $city = new City();
        $currency = new Currency();
        $itemUnit = new ItemUnit();
        $consignee = new Consignee();
        
        $rs = $dataset['rs'];
        $rsCarrier = $consignee->getDataRowById($rs[0]['carrierkey']);

       
        $originalLabel = '';
        if($param['originalLabelKey'] == '0') {
            $originalLabel = 'ORIGINAL'; // ORIGINAL
        } else {
            $originalLabel = 'COPY'; // COPY
        }
        //$obj->setLog($contentLabel, true);
        $attachment = $param['attachment'];

        
        $arrTestXY = array();
      
        if (!empty($rs[0]['description'])) {
            $needAttachment = true; // kondisi ketika perlu attachment
        }

        $logo = '<img src="/template/thewhale.wintera.co.id/img/wsilogo.png" style="width:320px;">'; 

        $arrShipper = array();
        if (!empty($rs[0]['shippername']))
            array_push($arrShipper, strtoupper(htmlspecialchars_decode($rs[0]['shippername'])));
        if (!empty($rs[0]['shipperaddress']))
            array_push($arrShipper, str_replace(chr(13), '<br>', strtoupper(htmlspecialchars_decode($rs[0]['shipperaddress']))));

        $HBLCode = $rs[0]['prefix'] . '' . $rs[0]['code'];

        //$notifyParty = strtoupper(htmlspecialchars_decode($rs[0]['carriername']));

        $notifyParty = (!empty($rs[0]['carrierkey'])) ? $rsCarrier[0]['name'].'<br>'.str_replace(chr(13),'<br>',$rsCarrier[0]['address']) : 'Same as consignee';
        $notifyParty = strtoupper(htmlspecialchars_decode($notifyParty));
        
        $rsJobOrder = $emklJobOrder->searchData($emklJobOrder->tableName . '.pkey', $rs[0]['refheaderkey']); 
        $rsDetail = $emklJobOrder->getDetailByColumn($emklJobOrder->tableNameDetail . '.pkey', $rs[0]['refkey']);
        $rsUnit = $itemUnit->getDataRowById($rs[0]['sumunitkey']);

         //feeder vessel
         $feederName = '';
         $feederNumber = '';
         if(!empty($rsJobOrder[0]['feederkey'])) {
             $rsFeeder = $vessel->getDataRowById($rsJobOrder[0]['feederkey']);
             $feederName = $rsFeeder[0]['name'];
             $feederNumber = $rsJobOrder[0]['feedernumber'];
         }

         $rsPOD = $port->getDataRowById($rs[0]['podkey']);
         $rsPOL = $port->getDataRowById($rs[0]['polkey']);
         $rsPODelivery = $port->getDataRowById($rs[0]['podeliverykey']);
             

        $placeOfDeliveryName = (!empty($rs[0]['podeliverykey'])) ? $rsPODelivery[0]['name'] : $rsPOD[0]['name'];
        $podName = (!empty($rs[0]['podkey'])) ? $rsPOD[0]['name'] :  $rsJobOrder[0]['podname'];
        $polName = (!empty($rs[0]['polkey'])) ? $rsPOL[0]['name'] :  $rsJobOrder[0]['polname'];


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

        $heightDesc = ($needAttachment) ? 'height:480px' : 'height:200px';

        
        if(!$attachment){ 
            // untuk jenis halaman pertama
            $description =  $rs[0]['shortdescription'];
            $attachtmentBorder = '';
            $GW = $obj->formatNumber($rs[0]['weight'],2) . " KGS";
            $VOLUME = $obj->formatNumber($rs[0]['volume'],2) . " M3";
        }else{
            // untuk jenis attachment
            $description =  $rs[0]['description'];
            $attachtmentBorder = 'border-bottom:1px solid #333';
            $heightDesc =  'height:480px';
        }

        $freightCharges = $rs[0]['freightcharges'];
        

        $rsContainer = $emklJobOrder->getDetailContainer($rs[0]['refheaderkey']);
    
        $rsItemUnit = $itemUnit->getDataRowById($rs[0]['unitkey']);
        $noOfPackages = $obj->formatNumber($rs[0]['qty']) . ' ' . $rsItemUnit[0]['name'];

        $telex = '';
        if($rs[0]['isrelease'] == 1) {
            $telex = 'SURRENDER';
        }


        $logoContent = '<table cellpadding="2">
                        <tr>
                            <td style="width:338px;">'. $logo .'</td>
                        </tr>
                    </table>';

        $contentLabel = '<table cellpadding="2">
                        <tr>
                            <td  style="font-weight:bold;width:198px;font-size:15px;">'. $originalLabel .'</td>
                        </tr>
                    </table>';


        $content1 = '<table cellpadding="2">
                        <tr>
                            <td style="width:338px;">'. implode('<br>',$arrShipper) .'</td>
                        </tr>
                    </table>'; //EXPORTER

        $content2 = '<table cellpadding="2">
                        <tr>
                            <td style="width:140px;text-align:center;">'. $HBLCode .'</td>
                        </tr>
                    </table>'; //HBL CODE

        $content3 = '<table cellpadding="2">
                        <tr>
                            <td style="width:338px;">' . strtoupper(htmlspecialchars_decode($rs[0]['consigneename'])) . '<br>' . str_replace(chr(13), '<br>', strtoupper(htmlspecialchars_decode($rs[0]['consigneeaddress']))) . '</td>
                        </tr>
                    </table>'; //CONSINGNEE
        
        $content4 = '<table cellpadding="2">
                        <tr>
                            <td style="width:338px;">'.$notifyParty.'</td>
                        </tr>
                    </table>'; //NOTIFY PARTY

        $content5 = '<table cellpadding="2">
                        <tr>
                            <td style="width:228px;">'.$feederName.'</td>
                        </tr>
                    </table>'; //VESSEL
        $content6 = '<table cellpadding="2">
                        <tr>
                            <td style="width:110px;">'.$feederNumber.'</td>
                        </tr>
                    </table>'; //VOYAGE

        $content7 = '<table cellpadding="2">
                <tr>
                    <td class="" style="width:169px;">'. $podName .'</td>
                    <td class="" style="width:169px;"></td>
                    <td class="" style="width:169px;">'. $polName .'</td>
                    <td class="" style="width:169px;">'.$telex.'</td>
                </tr>'; //POD, DESTINATION (IF ON CARR), POL, RELEASE

        $content8 = '<table cellpadding="2">
                <tr>
                    <td class="" style="width:169px;">' . $feederName . ' '. $feederNumber .'</td>
                    <td class="" style="width:169px;">'.strtoupper($obj->formatDBDate(date('Y-m-d'),'M d, Y',array('returnOnEmpty' => true))).'</td>
                    <td class="" style="width:169px;">'. $polName .'</td>
                    <td class="" style="width:169px;">Three (3) Original Copies</td>
                </tr>'; //Shipped On Board, Print Date, Freight Payable At, No. of Original B/L

        $content9 = '<table cellpadding="2">
                <tr>
                    <td class="border-right border-left border-top border-bottom" rowspan="2" style="width:180px;'.$heightDesc.'">'. $marksNumber .'</td>
                    <td class="border-right border-top" style="width:340px;'.$heightDesc.'">'.$description.'</td>
                    <td class="border-right border-top border-bottom" rowspan="2" style="width:78px;'.$heightDesc.'">'.$GW.'</td>
                    <td class="border-right border-top border-bottom" rowspan="2" style="width:78px;'.$heightDesc.'">'.$VOLUME.'</td>
                </tr>`
        </table>';
 
        $arrShipmentTerm = $emklJobOrder->getShipmentTerm();
        $arrShipmentTerm = array_column($arrShipmentTerm,'name','pkey');
        
        $content10 = '<table>';
        for($i=0;$i<count($rsContainer);$i++){
            $content10 .= '<tr>
                    <td class="" style="width:100px;"> '.$rsContainer[$i]['containerno'] .'</td>
                    <td class="" style="width:100px;">'.$rsContainer[$i]['sealno'].'</td>
                    <td class="" style="width:70px;">'.$rsContainer[$i]['containername'].'</td>
                    <td class="" style="width:70px;">'.$obj->formatNumber($rsContainer[$i]['grossweight'],2).'</td> 
                    <td class="" style="width:75px;">'.$obj->formatNumber($rsContainer[$i]['meas'],3).'</td> 
                    <td class="" style="width:75px;">'.$rsContainer[$i]['unitname'].'</td> 
                    <td class="" style="width:75px;">'.$arrShipmentTerm[$rsJobOrder[0]['shipmenttermkey']].' / '.$arrShipmentTerm[$rsJobOrder[0]['shipmentterm2key']].'</td> 
                    <td class="" style=""></td>
                </tr>'; 
           
        }
                 
        $content10 .= '</table>';  

        $content11 = '<table cellpadding="2">
                        <tr>
                            <td style="width:338px;">'.$freightCharges.'</td>
                        </tr>
                    </table>'; //Freight and charhges

        $content12 = '<table cellpadding="2">
                        <tr>
                            <td style="width:169px;">Jakarta</td>
                            <td style="width:169px;">'.$obj->formatDBDate($rsJobOrder[0]['trdate'],'d / m / Y',array('returnOnEmpty' => true)).'</td>
                        </tr>
                    </table>'; //place of issue, Date of issue

        $content13 = '<table cellpadding="2">
                        <tr>
                            <td style="width:338px;">'.$noOfPackages.'</td>
                        </tr>
                    </table>';//no of packages

        $content14 = '<table cellpadding="2">
                    <tr>
                        <td style="width:169px;">'. $podName .'</td>
                        <td style="width:169px;">'.$obj->formatDBDate($rsJobOrder[0]['etdpol'],'d / m / Y',array('returnOnEmpty' => true)).'</td>
                    </tr>
                </table>'; //place of acceptant, date of delivery

        
    

        

        array_push($arrTestXY,array('x' => 106, 'y' => 13, 'content' => $contentLabel));
        array_push($arrTestXY,array('x' => 110, 'y' => 25, 'content' => $logoContent));
        array_push($arrTestXY,array('x' => 11, 'y' => 14, 'content' => $content1));
        array_push($arrTestXY,array('x' => 162, 'y' => 14, 'content' => $content2));
        array_push($arrTestXY,array('x' => 11, 'y' => 41, 'content' => $content3)); 
        array_push($arrTestXY,array('x' => 11, 'y' => 67, 'content' => $content4)); 
        array_push($arrTestXY,array('x' => 11, 'y' => 94, 'content' => $content5)); 
        array_push($arrTestXY,array('x' => 75, 'y' => 94, 'content' => $content6)); 
        array_push($arrTestXY,array('x' => 11, 'y' => 107, 'content' => $content7)); 
        array_push($arrTestXY,array('x' => 11, 'y' => 120, 'content' => $content8)); 

        if(!$attachment) {
            array_push($arrTestXY,array('x' => 11, 'y' => 136, 'content' => $content9)); 
            array_push($arrTestXY,array('x' => 11, 'y' => 202, 'content' => $content10)); 
            array_push($arrTestXY,array('x' => 106, 'y' => 225, 'content' => $content11)); 
            array_push($arrTestXY,array('x' => 11, 'y' => 269, 'content' => $content12)); 
            array_push($arrTestXY,array('x' => 106, 'y' => 280, 'content' => $content13)); 
            array_push($arrTestXY,array('x' => 11, 'y' => 280, 'content' => $content14)); 
        } else {
            array_push($arrTestXY,array('x' => 11, 'y' => 140, 'content' => $content9)); 
        }


        return $arrTestXY;

    };


    $obj = new EMKLHouseBL();
    $emklJobOrder = new EMKLJobOrder();

    $rs = $dataset['rs'];

    $returnHTML = array();

    $needAttachment = false;
    $index = 0;
    foreach($arrCopy as $index=>$label){
        $arrTestXY = $setXYContent($dataset, array('attachment' => false, 'originalLabelKey' => $index));
        $html = $generateHeaderTable($dataset, array('attachment' => false, 'originalLabelKey' => $index));
        //$html .= ($needAttachment) ? '<div style="width:680px; text-align:center;"><b>** TO BE CONTINUED ON ATTACHED LIST **</b></div>' : '';
        $html .= $generateFooterTable($dataset, array('attachment' => false, 'originalLabelKey' => $index));
        array_push($returnHTML,array('html' =>$html,'content'=> $arrTestXY));
        // kalo ada attachment
        if ($needAttachment) {
            $html = $generateHeaderTable($dataset, array('attachment' => true));
            $arrTestXY = $setXYContent($dataset, array('attachment' => true, 'originalLabelKey' => $index));
            array_push($returnHTML, array('html' => $html, 'content' => $arrTestXY));
        }
    }

    return $returnHTML;
};

$generateReportContent = array();
array_push($generateReportContent, array('content' => $content));


?>