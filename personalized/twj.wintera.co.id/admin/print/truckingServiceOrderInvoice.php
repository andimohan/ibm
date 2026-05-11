<?php

$pdf->setCustomSettings(
    array(
        'showPrintHeader' => false,
        'header' => '',
        'showPrintFooter' => false,
    )
);

includeClass('TruckingServiceOrderInvoice.class.php');
$truckingServiceOrderInvoice = createObjAndAddToCol(new TruckingServiceOrderInvoice());

$obj = $truckingServiceOrderInvoice;

$generateReportContent = function ($dataset) {

    $obj = new TruckingServiceOrderInvoice();
    $truckingServiceOrder = new TruckingServiceOrder();
    $paymentMethod = new PaymentMethod();
    $termOfPayment = new TermOfPayment();
    $customer  = new Customer();
    $customCode = new CustomCode();
    $consignee = new Consignee();
    $truckingServiceWorkOrder = new TruckingServiceWorkOrder();
    $cost = new Service(TRUCKING_SERVICE,1);
    $employee = new Employee();
    $truckingService = new Service();

    $rs = $dataset['rs'];
    
    // khusus GPI
    $taxFree = (isset($_GET) && $_GET['useTax'] == 1) ? false : true;

    $rsInvoiceType = $customCode->searchData($customCode->tableName . '.pkey', $rs[0]['customcodekey'], true);
    $rsPaymentMethod = $paymentMethod->getDataRowById($rs[0]['companybankkey']);
    $rsTermOfPayment = $termOfPayment->getDataRowById($rs[0]['termofpaymentkey']);
    $rsCustomer = $customer->getDataRowById($rs[0]['customerkey']);
    $rsApproved = $employee->getDataRowById($rs[0]['confirmedby']);
    
    $rsDetail = $obj->getDetailWithRelatedInformation($rs[0]['pkey']);
    $rsDetailCols = $obj->reindexDetailCollections($rsDetail, 'salesorderkey');
    $arrJobOrderKey = array_column($rsDetail, 'salesorderkey');
    
    $rsSOHeader = $truckingServiceOrder->searchData('','',true, ' and ' . $truckingServiceOrder->tableName.'.pkey in ('. $obj->oDbCon->paramString($arrJobOrderKey,',') .')');
    $rsSOHeaderCols = $obj->reindexDetailCollections($rsSOHeader, 'pkey');
    $rsSODetail = $truckingServiceOrder->getDetailWithRelatedInformation($arrJobOrderKey);
    $rsSODetailCols = $obj->reindexDetailCollections($rsSODetail, 'refkey');
    
    $arrCostKey = array_column($rsSODetail, 'itemkey');

    $rsTruckingService = $truckingService->searchData('','',true, ' and ' . $truckingService->tableName.'.statuskey = 1');
    $rsTruckingServiceCols = $obj->reindexDetailCollections($rsTruckingService, 'pkey');


    $rsItemDetail = $obj->getItemDetail($rs[0]['pkey'], 'refheaderkey');

    $rsWO= $truckingServiceWorkOrder->searchData('','',true, ' and ' . $truckingServiceWorkOrder->tableName.'.refkey in ('. $obj->oDbCon->paramString($arrJobOrderKey,',') .') and '. $truckingServiceWorkOrder->tableName .'.statuskey = 3 ');

    //$stuffingDate = ''; 
    //if(!empty($rsWO)) {
    //    $stuffingDate = $obj->formatDBDate($rsWorkOrder[0]['stuffingdatetime'], 'd-M-Y');
    //}
    
    $stuffingDate =  $obj->formatDBDate($rsSOHeader[0]['trdate'], 'd-M-Y');
    $rsItemDetailCols = $obj->reindexDetailCollections($rsItemDetail, 'refkey');


    $shipTo = $rsCustomer[0]['name'] .'<br>'.nl2br($rsCustomer[0]['address']);
    if(($rs[0]['usenotify'] == 1) && ($rs[0]['invoicenotify'] == 2)) {
        $shipTo = $rs[0]['invoiceconsigneenotifyname'] . '<br>' . nl2br($rs[0]['invoiceconsigneenotifyaddress']);
    }
    
    if($rs[0]['invoiceto'] == 1){
        $invoiceTo = $rsCustomer[0]['name'] .'<br>'.nl2br($rsCustomer[0]['address']);
    } else {
        $invoiceTo = $rs[0]['invoiceconsigneename'] .'<br>'.nl2br($rs[0]['invoiceconsigneeaddress']);
        //bill to consignee
        //$totalRs = count($rsDetail);
        //for($i=0;$i<$totalRs;$i++){  
        //    if (!empty($rsDetail[$i]['salesorderkey'])){ 
        //        $rsSOHeader = $truckingServiceOrder->getDataRowById($rsDetail[$i]['salesorderkey']);  
        //        $rsConsignee = $consignee->getDataRowById($rsSOHeader[0]['consigneekey']);  
        //        $shipTo = $rsConsignee[0]['name'] .'<br>'.nl2br($rsConsignee[0]['address']);
        //        break;
        //    }
        //} 
    }

    
    $invoiceTitle  = 'Sales Invoice';
    if($rsInvoiceType[0]['isreimburse'] == 1) {
        $invoiceTitle = (!empty($rsInvoiceType[0]['title'])) ? $rsInvoiceType[0]['title'] : $rsInvoiceType[0]['name'];
    }
    
    $html = $obj->printSetting['defaultStyle'];

    $html .= '
        <style>
            .border-top{ border-top:1px solid #333; }
            .border-left{ border-left:1px solid #333; }
            .border-top-left{ border-top:1px solid #333; border-left:1px solid #333; }
            .border-bottom-right{ border-bottom:1px solid #333; border-right:1px solid #333; } 
            .border-right{ border-right:1px solid #333;} 
            .border-bottom{ border-bottom:1px solid #333;}
            .border-left-top-right-bottom{ border-left:1px solid #333; border-top:1px solid #333; border-right:1px solid #333; border-bottom:1px solid #333;}
            .border-left-top-right{ border-left:1px solid #333; border-top:1px solid #333; border-right:1px solid #333;}
            .font-bold{ font-wight: bold;}
        </style>
    ';

    $companyName = '<span style="font-size:15px;font-weight:bold;color:green">'. strtoupper($obj->loadSetting('companyName')) .'</span>';
    $companyLogo = $obj->loadSetting('companyLogo');
    $companyAddress = $obj->loadSetting('companyAddress');
    
    $imgLetterhead = $obj->phpThumbURLSrc . 'setting/companyLogo/' . $companyLogo;

    $logo = '<img src="' . $imgLetterhead . '" style="height: 25px">';
    
    $html .= '
        <table cellpadding="2" width="100%">
            <tr>
                <td class="border-left-top-right-bottom" style="width:337.5px;line-height:30px;margin:auto;"> '. $logo .' &nbsp;&nbsp;&nbsp; '. $companyName .' </td>
                <td class="" style="width:337.5px"></td> 
            </tr>
            <tr>
                <td class="border-left border-right border-bottom" style="height:80px">' . nl2br($companyAddress) . '</td>
                <td style="text-align:center;">
                    <span style="font-size:22px;">'. $invoiceTitle .'</span><br><span>'. $rs[0]['code'] .'</span>
                </td> 
            </tr>
        </table>';

    $html .='<div style="clear:both"></div>';

    $html .= '
        <table cellpadding="2" width="100%">
            <tr>
                <td class="border-left-top-right-bottom" style="width:210px; text-align:center;">Bill To</td>
                <td style="width:15px;"></td>
                <td class="border-left-top-right-bottom" style="width:210px; text-align:center;">Ship To</td>
                <td style="width:5px;"></td>
                <td rowspan="2" style="width:220px;">
                    <table>
                        <tr>
                            <td class="border-left-top-right-bottom" style="width:110px;text-align:center;">Invoice Date</td>
                            <td style="width:4px"></td>
                            <td class="border-left-top-right-bottom" style="width:110px;text-align:center;">Job</td>
                        </tr>
                        
                        <tr>
                            <td class="border-left border-right border-bottom" style="text-align:center;">'. $obj->formatDBDate($rs[0]['trdate'], 'd-M-Y') .'</td>
                            <td style="width:4px"></td>
                            <td class="border-left border-right border-bottom" style="text-align:center">'.$rsSOHeader[0]['shipmentnumber'].'</td>
                        </tr>

                        <tr><td style="height:5px"></td></tr>

                        <tr>
                            <td class="border-left-top-right-bottom" style="text-align:center;">Stuffing Date</td>
                            <td style="width:4px"></td>
                            <td class="border-left-top-right-bottom"  style="text-align:center;">Terms</td>
                        </tr>
                        <tr>
                            <td class="border-left border-right border-bottom" style="text-align:center;">'. $stuffingDate .'</td>
                            <td style="width:4px"></td>
                            <td class="border-left border-right border-bottom" style="text-align:center;">'. $rsTermOfPayment[0]['name'] .'</td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td class="border-left border-right border-bottom">'. $invoiceTo .'</td>
                <td></td>
                <td class="border-left border-right border-bottom">'. $shipTo.'</td>
                <td></td>
            </tr>
        </table>
    ';

    $html .='<div style="clear:both"></div>';


    $itemWidth = 'width:9%;';
    $descriptionWidth = 'width:53%;';
    $qtyWidth = 'width:6%;';
    $unitPriceWidth = 'width:12%;';
    $amountWidth = 'width:12%;';
    $currWidth= 'width:4%;';
    $heighBody = 'height:0px;';
    


    $html .= '
    <table cellpadding="2" width="100%">
        <thead>
            <tr>
                <th class="border-left-top-right-bottom" style="text-align:center;'. $itemWidth .'">Item</th>
                <th class="border-left-top-right-bottom" style="text-align:center;'. $descriptionWidth .'">Item Description</th>
                <th class="border-left-top-right-bottom" style="text-align:center;'. $qtyWidth .'">Qty</th>
                <th colspan="2" class="border-left-top-right-bottom" style="text-align:center;width:16%;">Unit Price</th>
                <th colspan="2" class="border-left-top-right-bottom" style="text-align:center;width:16%;">Amount</th>
            </tr>
        </thead>
        <tbody>
    ';

    //asumsi 1 Job Order
    for($i=0; $i<count($rsDetail); $i++){

            if(empty($rsDetail[$i]['workorderkey'])) {
                $criteria = ' and ' . $truckingServiceWorkOrder->tableName.'.refkey = '. $obj->oDbCon->paramString($rsDetail[$i]['salesorderkey']) .' and '. $truckingServiceWorkOrder->tableName .'.statuskey = 3';
            } else {
                $criteria = ' and ' . $truckingServiceWorkOrder->tableName.'.pkey = '. $obj->oDbCon->paramString($rsDetail[$i]['workorderkey']) .' and '. $truckingServiceWorkOrder->tableName .'.statuskey = 3';
            }

            $rsWorkOrder = $truckingServiceWorkOrder->searchData('','',true, $criteria);

            $rsItemDetailCol = $rsItemDetailCols[$rsDetail[$i]['pkey']];
            $rsSOHeaderCol = $rsSOHeaderCols[$rsDetail[$i]['salesorderkey']];
            $rsSODetailCol = $rsSODetailCols[$rsDetail[$i]['salesorderkey']];
            //$route = (!empty($rsSOHeaderCol[0]['routefrom']) ? $rsSOHeaderCol[0]['routefrom'] . ' - ' . $rsSOHeaderCol[0]['routeto'] : '');

            $description = $rsDetail[$i]['description'];
            
            $consigneeName = (!empty($rsSOHeaderCol[0]['consigneename']) ? ' - ' . $rsSOHeaderCol[0]['consigneename'] : '');


            $uniqueRoutes = [];
            foreach($rsWorkOrder as $workOrder) {
                
                $route = strtolower($workOrder['routefrom'].'-'.$workOrder['routeto'].'-'.$workOrder['routelast']);
                if (!isset($uniqueRoutes[$route])) {
                    $uniqueRoutes[$route] = [
                        'pkey'          => $workOrder['pkey'],
                        'code'          => $workOrder['code'],
                        'refkey'        => $workOrder['refkey'],
                        'refdetailkey'  => $workOrder['refdetailkey'],
                        'routefrom'     => $workOrder['routefrom'],
                        'routeto'       => $workOrder['routeto'],
                        'routelast'     => $workOrder['routelast'],
                        'routes'        => $workOrder['routefrom'] . (!empty($workOrder['routeto']) ? ' - ' . $workOrder['routeto'] : '') . (!empty($workOrder['routelast']) ? ' - ' . $workOrder['routelast'] : '')
                    ];
                }
                
            }
            $arrWorkOrders = array_values($uniqueRoutes);
            
        //type reimburse
        if($rsInvoiceType[0]['isreimburse'] == 1) {

                
            $costkey = $rsSODetailCol[0]['itemkey'];


            $rsTruckingServiceCol = $rsTruckingServiceCols[$costkey];
            
            $itemName = (!empty($rsTruckingServiceCol[0]['aliasname']) ? $rsTruckingServiceCol[0]['aliasname'] : $rsSODetailCol[0]['itemname']);

            //trucking service ada nama consginee
            if (isset($rsTruckingServiceCols[$costkey])) {
                $itemName .= ' ' . $consigneeName;
            }

            //item name sales order
            $html .= '<tr>
                        <td class="border-left border-right" style="text-align:center;'. $itemWidth . '">1</td>
                        <td class="border-left border-right" style="'. $descriptionWidth . '">'. $itemName .'</td>
                        <td class="border-left border-right" style="text-align:center;'. $qtyWidth . '"> </td>
                        <td style="text-align:center;'. $currWidth . '"></td>
                        <td class="border-right" style="text-align:right;'. $unitPriceWidth . '"> </td>
                        <td style="text-align:center;'. $currWidth . '"></td>
                        <td class="border-right" style="text-align:right;'. $amountWidth . '"> </td>
                    </tr>';

                //route       
                if (!empty($arrWorkOrders)) {

                    foreach ($arrWorkOrders as $wo) {
                        $html .= '<tr>
                                    <td class="border-left border-right" style="text-align:center;' . $itemWidth . '"></td>
                                    <td class="border-left border-right" style="' . $descriptionWidth . '">' . $wo['routes'] . '</td>
                                    <td class="border-left border-right" style="text-align:center;' . $qtyWidth . '"> </td>
                                    <td style="text-align:center;' . $currWidth . '"></td>
                                    <td class="border-right" style="text-align:right;' . $unitPriceWidth . '"> </td>
                                    <td style="text-align:center;' . $currWidth . '"></td>
                                    <td class="border-right" style="text-align:right;' . $amountWidth . '"> </td>
                                </tr>';
                    }
                }

                $html .= '<tr>
                                <td class="border-left border-right" style="text-align:center;' .$itemWidth . '"></td>
                                <td class="border-left border-right" style="' .$descriptionWidth . '"></td>
                                <td class="border-left border-right" style="text-align:center;' .$qtyWidth . '"> </td>
                                <td style="text-align:center;' . $currWidth . '"></td>
                                <td class="border-right" style="text-align:right;' .$unitPriceWidth . '"> </td>
                                <td style="text-align:center;' .$currWidth . '"></td>
                                <td class="border-right" style="text-align:right;' .$amountWidth . '"> </td>
                            </tr>';

                //item
                foreach($rsItemDetailCol as $key => $rowItem) {

                    $itemName = (!empty($rowItem['aliasname']) ? $rowItem['aliasname'] : $rowItem['itemname']);

                    $html .= '<tr>
                            <td class="border-left border-right" style="text-align:center;'.$heighBody . $itemWidth .'"></td>
                            <td class="border-left border-right" style="'.$heighBody . $descriptionWidth .'">'. $itemName .'</td>
                            <td class="border-left border-right" style="text-align:center;'.$heighBody . $qtyWidth .'">'. $obj->formatNumber($rowItem['qtyinbaseunit']) .'</td>
                            <td style="text-align:center;'.$heighBody . $currWidth .'">Rp</td>
                            <td class="border-right" style="text-align:right;'.$heighBody  . $unitPriceWidth.'">'. $obj->formatNumber($rowItem['priceinunit']) .'</td>
                            <td style="text-align:center;' . $heighBody . $currWidth . '">Rp</td>
                            <td class="border-right" style="text-align:right;'.$heighBody . $amountWidth .'">' . $obj->formatNumber($rowItem['total']) . '</td>
                        </tr>';

                }

                
            } else {

                foreach($rsItemDetailCol as $key => $rowItem) {

                    $itemName = (!empty($rowItem['aliasname']) ? $rowItem['aliasname'] : $rowItem['itemname']);

                    //trucking service ada nama consginee
                    if(isset($rsTruckingServiceCols[$rowItem['itemkey']])) {
                        $itemName .= ' ' . $consigneeName;
                    }

                    $html .= '<tr>
                            <td class="border-left border-right" style="text-align:center;'.$heighBody . $itemWidth .'">1</td>
                            <td class="border-left border-right" style="'.$heighBody . $descriptionWidth .'">'. $itemName .'</td>
                            <td class="border-left border-right" style="text-align:center;'.$heighBody . $qtyWidth .'">'. $obj->formatNumber($rowItem['qtyinbaseunit']) .'</td>
                            <td style="text-align:center;'.$heighBody . $currWidth .'">Rp</td>
                            <td class="border-right" style="text-align:right;'.$heighBody  . $unitPriceWidth.'">'. $obj->formatNumber($rowItem['priceinunit']) .'</td>
                            <td style="text-align:center;' . $heighBody . $currWidth . '">Rp</td>
                            <td class="border-right" style="text-align:right;'.$heighBody . $amountWidth .'">' . $obj->formatNumber($rowItem['total']) . '</td>
                        </tr>';

                }

                if (!empty($arrWorkOrders)) {

                    foreach ($arrWorkOrders as $wo) {
                        $html .= '<tr>
                                        <td class="border-left border-right" style="text-align:center;' . $itemWidth . '"></td>
                                        <td class="border-left border-right" style="' . $descriptionWidth . '">' . $wo['routes'] . '</td>
                                        <td class="border-left border-right" style="text-align:center;' . $qtyWidth . '"> </td>
                                        <td style="text-align:center;' . $currWidth . '"></td>
                                        <td class="border-right" style="text-align:right;' . $unitPriceWidth . '"> </td>
                                        <td style="text-align:center;' . $currWidth . '"></td>
                                        <td class="border-right" style="text-align:right;' . $amountWidth . '"> </td>
                                    </tr>';
                    }
                }

            }

                if(!empty($description)) {
                    $html .= '<tr>
                                        <td class="border-left border-right" style="text-align:center;' .$itemWidth . '"></td>
                                        <td class="border-left border-right" style="' .$descriptionWidth . '"></td>
                                        <td class="border-left border-right" style="text-align:center;' .$qtyWidth . '"> </td>
                                        <td style="text-align:center;' . $currWidth . '"></td>
                                        <td class="border-right" style="text-align:right;' .$unitPriceWidth . '"> </td>
                                        <td style="text-align:center;' .$currWidth . '"></td>
                                        <td class="border-right" style="text-align:right;' .$amountWidth . '"> </td>
                                    </tr>';
                    $html .= '<tr>
                                        <td class="border-left border-right" style="text-align:center;' .$itemWidth . '"></td>
                                        <td class="border-left border-right" style="' .$descriptionWidth . '">'. nl2br($description) .'</td>
                                        <td class="border-left border-right" style="text-align:center;' .$qtyWidth . '"> </td>
                                        <td style="text-align:center;' . $currWidth . '"></td>
                                        <td class="border-right" style="text-align:right;' .$unitPriceWidth . '"> </td>
                                        <td style="text-align:center;' .$currWidth . '"></td>
                                        <td class="border-right" style="text-align:right;' .$amountWidth . '"> </td>
                                    </tr>';
                }

                $html .= '  
                    <tr>
                    <td class="border-left border-right" style="text-align:center;height:20px;' . $itemWidth . '"> </td>
                    <td class="border-left border-right" style="' . $descriptionWidth . '"></td>
                    <td class="border-left border-right" style="text-align:center;' . $qtyWidth . '"> </td>
                    <td style="text-align:center;' . $currWidth . '"> </td>
                    <td class="border-right" style="text-align:right;' . $unitPriceWidth . '"> </td>
                    <td style="text-align:center;' . $currWidth . '"> </td> 
                    <td class="border-right" style="text-align:right;' . $amountWidth . '"> </td>
                </tr> ';

            $indexKeyMapWO = [];
            foreach($rsWorkOrder as $woRow) {

                $containerNumber = $woRow['containernumber'];
                $container2Number = $woRow['container2number'];
                $policeNumber = ($woRow['isoutsource'] == 0) ? $woRow['policenumber'] : $woRow['outsourcecarregistrationnumber'];

                $containersNumber = (!empty($containerNumber) ? $containerNumber : '').(!empty($container2Number) ? ', '. $container2Number : '');

                $indexParts = array_filter([
                    !empty($containerNumber) ? strtolower(str_replace(' ', '', $containerNumber)) : null,
                    !empty($container2Number) ? strtolower(str_replace(' ', '', $container2Number)) : null,
                    !empty($policeNumber) ? strtolower(str_replace(' ', '', $policeNumber)) : null
                ]);

                $indexKey = implode('-', $indexParts);

                $indexKeyMapWO[$indexKey] = [
                    'pkey' => $woRow['pkey'],
                    'code' => $woRow['code'],
                    'containernumber' => $containerNumber,
                    'container2number' => $container2Number,
                    'containersnumber' => $containersNumber,
                    'policenumber' => $policeNumber,
                    'aju' => $woRow['aju'],
                    'indexkey' => $indexKey
                ];

            }

            $arrUniqueWOContainer = array_values($indexKeyMapWO);
            
            foreach($arrUniqueWOContainer as $woRow) {
                $containerNumber = $woRow['containersnumber'];

                $containerOrDesc = '';
                if (!empty($containerNumber)) {
                    $containerOrDesc .= 'No Cont.  ' . $containerNumber . ' -- ';
                }

                $policeNumber = $woRow['policenumber'];

                $ajuPIBPEB = (!empty($woRow['aju'])) ? ' -- ' . $woRow['aju'] : '';

                if (!empty($containerNumber)) {
                    $html .= '
                            <tr>
                                <td class="border-left border-right" style="text-align:center;' . $itemWidth . '"> </td>
                                <td class="border-left border-right" style="' . $descriptionWidth . '">' . $containerOrDesc . $policeNumber . $ajuPIBPEB . '</td>
                                <td class="border-left border-right" style="text-align:center;' . $qtyWidth . '"> </td>
                                <td style="text-align:center;' . $currWidth . '"> </td>
                                <td class="border-right" style="text-align:right;' . $unitPriceWidth . '"> </td>
                                <td style="text-align:center;' . $currWidth . '"> </td>
                                <td class="border-right" style="text-align:right;' . $amountWidth . '"> </td>
                            </tr>
                            ';
                }

            }

        

            $html .= '  
                    <tr>
                    <td class="border-left border-right" style="text-align:center;height:20px;' . $itemWidth . '"> </td>
                    <td class="border-left border-right" style="' . $descriptionWidth . '"></td>
                    <td class="border-left border-right" style="text-align:center;' . $qtyWidth . '"> </td>
                    <td style="text-align:center;' . $currWidth . '"> </td>
                    <td class="border-right" style="text-align:right;' . $unitPriceWidth . '"> </td>
                    <td style="text-align:center;' . $currWidth . '"> </td> 
                    <td class="border-right" style="text-align:right;' . $amountWidth . '"> </td>
                </tr> ';
    }

    
    

        $html .= '
                <tr>
                    <td class="border-left border-right" style="text-align:center;' . $itemWidth . '"> </td>
                    <td class="border-left border-right" style="' . $descriptionWidth . '"></td>
                    <td class="border-left border-right" style="text-align:center;' . $qtyWidth . '"> </td>
                    <td style="text-align:center;' . $currWidth . '"> </td>
                    <td class="border-right" style="text-align:right;' . $unitPriceWidth . '"> </td>
                    <td style="text-align:center;' . $currWidth . '"> </td>
                    <td class="border-right" style="text-align:right;' . $amountWidth . '"> </td>
                </tr>';

    $discountAmount = $rs[0]['finaldiscount'];
    $finalDiscountType = $rs[0]['finaldiscounttype'];

    if($finalDiscountType == 2) {
        $discount = $rs[0]['finaldiscount'];
        $discountAmount = $rs[0]['grandtotal'] * ($discount / 100);
    }

    if($discountAmount > 0) {
    $html .= '
            <tr>
                    <td class="border-left border-right" style="text-align:center;' . $heighBody . $itemWidth . '"> </td>
                    <td class="border-left border-right" style="' . $heighBody . $descriptionWidth . '"></td>
                    <td class="border-left border-right" style="text-align:center;' . $heighBody . $qtyWidth . '"> </td>
                    <td style="text-align:center;' . $heighBody . $currWidth . '"> </td>
                    <td class="border-right" style="text-align:right;' . $heighBody . $unitPriceWidth . '"> </td>
                    <td style="text-align:center;' . $heighBody . $currWidth . '"> </td>
                    <td class="border-right" style="text-align:right;' . $heighBody . $amountWidth . '"> </td>
            </tr>
            <tr>
                <td class="border-left border-right" style="'.$itemWidth .'"></td>
                <td class="border-left border-right" style="'.$descriptionWidth .'">Diskon</td>
                <td class="border-left border-right" style="'.$qtyWidth .'"></td>
                <td class="" style="text-align:center;'.$heighBody . $currWidth .'"> </td>
                <td class="border-right" style="'.$unitPriceWidth .'"></td>
                <td class="" style="text-align:center;'.$heighBody . $currWidth .'">Rp</td>
                <td class="border-right" style="text-align:right;'.$amountWidth .'">('. $obj->formatNumber($discountAmount) .')</td>
            </tr>';
    }

    $taxValue = $rs[0]['taxvalue'];
    $taxPercentage = $rs[0]['taxpercentage'];
    $isPriceIncludeTax = $rs[0]['ispriceincludetax'];

    if($taxValue > 0) {
        
        $ppnDesc = ($taxFree) ? '(PPN Dibebaskan berdasarkan PP Nomor 49 Tahun 2022)': '';
        
        $html .='
            <tr>
                <td class="border-left border-right" style="'.$itemWidth .'"></td>
                <td class="border-left border-right" style="'.$descriptionWidth .'">PPN'. ($isPriceIncludeTax == 1 ? ' [Include]' : '') .' - '. $obj->formatNumber($taxPercentage,-2) .'% '.$ppnDesc.'</td>
                <td class="border-left border-right" style="'.$qtyWidth .'"></td>
                <td class="" style="text-align:center;'.$heighBody . $currWidth .'"> </td>
                <td class="border-right" style="'.$unitPriceWidth .'"></td>
                <td class="" style="text-align:center;'.$heighBody . $currWidth .'">Rp</td>
                <td class="border-right" style="text-align:right;'.$amountWidth .'">'. $obj->formatNumber($taxValue) .'</td>
            </tr>
        ';
    }

    $stampFee = $rs[0]['stampfee'];
    if($stampFee > 0) {
        $html .='
            <tr>
                <td class="border-left border-right" style="'.$itemWidth .'"></td>
                <td class="border-left border-right" style="'.$descriptionWidth .'">Materai</td>
                <td class="border-left border-right" style="'.$qtyWidth .'"></td>
                <td class="" style="text-align:center;'.$heighBody . $currWidth .'"> </td>
                <td class="border-right" style="'.$unitPriceWidth .'"></td>
                <td class="" style="text-align:center;'.$heighBody . $currWidth .'">Rp</td>
                <td class="border-right" style="text-align:right;'.$amountWidth .'">('. $obj->formatNumber($stampFee) .')</td>
            </tr>
        ';
    }


    $html .= '
            <tr>
                <td class="border-left border-right border-bottom" style="'.$itemWidth .'"></td>
                <td class="border-left border-right border-bottom" style="'.$descriptionWidth .'"></td>
                <td class="border-left border-right border-bottom" style="'.$qtyWidth .'"></td>
                <td class="border-bottom" style="text-align:center;'.$heighBody . $currWidth .'"> </td>
                <td class="border-right border-bottom" style="'.$unitPriceWidth .'"></td>
                <td class="border-bottom" style="text-align:center;'.$heighBody . $currWidth .'"></td>
                <td class="border-right border-bottom" style="text-align:right;'.$amountWidth .'"></td>
            </tr>';


    $html .='
        </tbody>
    </table>
    ';

    $html .='<div style="clear:both"></div>';

    $totalInvoice = $rs[0]['grandtotal'] ;
    
    if(!$taxFree)
         $totalInvoice += $taxValue;
        
    //if(!$isPriceIncludeTax) $totalInvoice -= $taxValue;
    $sayNumber = $obj->sayNumber($totalInvoice);

    $html .='
        <table cellpadding="2" width="100%">
            <tr>
                <td class="border-left-top-right-bottom" style="text-align:center;line-height:40px;'. $itemWidth .'">Terbilang: </td>
                <td class="border-left-top-right-bottom" style="line-height:40px;'. $descriptionWidth .'">'. ucwords($sayNumber) .' Rupiah</td>
                <td class="" style="text-align:center;height:40px;'. $qtyWidth .'"> </td>
                <td class="border-left-top-right-bottom" style="text-align:right;line-height:40px;font-weight:bold;width:16%;">Total Invoice :</td>
                <td class="border-left-top-right-bottom" style="text-align:right;line-height:40px;font-weight:bold;width:16%;">'. $obj->formatNumber($totalInvoice) .'</td>
            </tr>
        </table>
    ';

    $html .='<div style="clear:both"></div>';

    $html .= '
        <table cellpadding="2" class="border-left-top-right-bottom" width="62%">
            <tr>
                <td colspan="3" style="font-weight:bold;">Payment please Transfer To</td>
            </tr>
            <tr>
                <td style="font-weight:bold;width:35%;">Bank</td>
                <td style="font-weight:bold;width:3%;">:</td>
                <td style="font-weight:bold;width5%;">' . $rsPaymentMethod[0]['bankname'] . '</td>
            </tr>
            <tr>
                <td style="font-weight:bold;width:35%;">Branch</td>
                <td style="font-weight:bold;width:3%;">:</td>
                <td style="font-weight:bold;">' . $rsPaymentMethod[0]['branch'] . '</td>
            </tr>
            <tr>
                <td style="font-weight:bold;width:35%;">Account Holder Name</td>
                <td style="font-weight:bold;width:3%;">:</td>
                <td style="font-weight:bold;">' . $rsPaymentMethod[0]['bankaccountname'] . '</td>
            </tr>
            <tr>
                <td style="font-weight:bold;width:35%;">Account Number</td>
                <td style="font-weight:bold;width:3%;">:</td>
                <td style="font-weight:bold;">' . $rsPaymentMethod[0]['bankaccountnumber'] . '</td>
            </tr>
        </table>
    ';

    $html .='<div style="clear:both"></div>';

    $html .= ' 
        <table cellpadding="4" class="sign">
        <tr>
            <td  class="sign-col"> Approved By</td>
            <td class="sign-col-space"></td>
            <td  class="sign-col"> Received By</td>
        </tr> 
        <tr>
            <td  class="sign-name"></td>
            <td class="sign-col-space"></td>
            <td  class="sign-name"></td>
        </tr> 
        </table>';

    return $html;
}

?>
