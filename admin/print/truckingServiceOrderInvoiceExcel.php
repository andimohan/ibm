<?php

$PRINT_SETTINGS = array(
    'showPrintHeader' => false,
    'showPrintFooter' => false,
    'paperSetting' => 'F4,L'
);


includeClass('TruckingServiceOrderInvoice.class.php');
$truckingServiceOrderInvoice = createObjAndAddToCol(new TruckingServiceOrderInvoice());

$obj = $truckingServiceOrderInvoice;

$generateReportContent = function ($dataset) {

    $obj = new TruckingServiceOrderInvoice();
    $truckingServiceOrder = new TruckingServiceOrder();
    $truckingServiceWorkOrder = new TruckingServiceWorkOrder();
    $truckingCost = new Service(TRUCKING_SERVICE, 1);

    $arrCost = array(
        'CRT',
        'UJS',
        'MULTIDROP',
        'RETRIBUSI',
        'BENDED',
        'BIAYA PARKIR'
    );

    $rsTruckingCost = $truckingCost->searchData(
        $truckingCost->tableName . '.statuskey',
        1,
        true,
        ' and ' . $truckingCost->tableName . '.name in (' . $obj->oDbCon->paramString($arrCost, ',') . ') and isdroppointdetailprice = 1',
        'order by FIELD(' . $truckingCost->tableName . '.name, ' . $obj->oDbCon->paramString($arrCost, ',') . '), ismultipliedbyqty desc, name asc'
    );

    $staticWidth = 780; // Total width of static columns
    $totalTableWidth = 1200;
    $remainingWidth = $totalTableWidth - $staticWidth;
    $costColumnCount = count($rsTruckingCost);
    $dynamicWidth = floor($remainingWidth / $costColumnCount);

    $rs = $dataset['rs'];

    $typePrint = $_GET['type'];

    if (empty($_GET['type'])) {
        $typePrint = 'kontrak';
    }

    $rsDetail = $obj->getDetailWithRelatedInformation($rs[0]['pkey']);

    $arrJOKey = array_column($rsDetail, 'salesorderkey');

    $rsJobOrder = $truckingServiceOrder->searchData('', '', true, ' and ' . $truckingServiceOrder->tableName . '.pkey in (' . $obj->oDbCon->paramString($arrJOKey, ',') . ') ');

    $rsWorkOrder = $truckingServiceWorkOrder->searchData('', '', true, ' and ' . $truckingServiceWorkOrder->tableName . '.refkey in (' . $obj->oDbCon->paramString($arrJOKey, ',') . ') ');
    $arrWOKey = array_column($rsWorkOrder, 'pkey');

    $rsCargoDetail = $truckingServiceWorkOrder->getCargoDetail($arrWOKey);

    $arrCargoKey = array_column($rsCargoDetail, 'pkey');
    $arrCostKey = array_column($rsTruckingCost, 'pkey');


    $rsCostCargo = $truckingServiceWorkOrder->getCargoCostDetail($arrCargoKey, '', $arrCostKey);

    $rsWorkOrder = $obj->reindexDetailCollections($rsWorkOrder, 'refkey');
    $rsCargoDetail = $obj->reindexDetailCollections($rsCargoDetail, 'refkey');
    $rsCostCargo = $obj->reindexDetailCollections($rsCostCargo, 'refheaderkey');

    $rsDetailCols = $obj->reindexDetailCollections($rsDetail, 'salesorderkey');

    $workOrderData = []; //group by car / police number
    foreach ($rsWorkOrder as $refkey => $entries) {
        foreach ($entries as $entry) {
            $carkey = $entry['carkey'];

            if (!isset($workOrderData[$refkey][$carkey])) {
                $workOrderData[$refkey][$carkey] = [];
            }
            $workOrderData[$refkey][$carkey][] = $entry;
        }
    }
    // $obj->setLog($groupedData, true);

    $html = $obj->printSetting['defaultStyle'];

    $html .= '<style>
        .border-top{border-top: 1px solid black}
        .border-top-left{border-top: 1px solid black;border-left: 1px solid black}
        .border-top-right{border-top: 1px solid black;border-right: 1px solid black}
        .border-left{border-left: 1px solid black}
        .border-right{border-right: 1px solid black}
        .border-bottom{border-bottom: 1px solid black}
        .border-bottom-left{border-bottom: 1px solid black; border-left: 1px solid black}
    </style>';

    $html .= '
            <table cellpadding="3" style="width:1090px">
                <tr>
                    <td style="width:80px; font-weight:bold;">No. Invoice</td>
                    <td style="width:15px; font-weight:bold;">:</td>
                    <td style="width:200px; font-weight:bold;">' . $rs[0]['code'] . '</td>
                </tr>
                <tr>
                    <td style="width:80px; font-weight:bold;">Tgl. Invoice</td>
                    <td style="width:15px; font-weight:bold;">:</td>
                    <td style="width:150px; font-weight:bold;">' . $obj->formatDBDate($rs[0]['trdate'], 'd-M-Y') . '</td>
                </tr>
            </table>
            <div style="clear:both"></div>
        ';

    foreach ($rsJobOrder as $key => $joborder) {

        $rsDetailInvoice = $rsDetailCols[$joborder['pkey']];

        $html .= '
            <table cellpadding="3" style="width:1090px">
                <tr>
                    <td style="width:80px; font-weight:bold;">No. Job</td>
                    <td style="width:15px; font-weight:bold;">:</td>
                    <td style="width:120px; font-weight:bold;">' . $joborder['code'] . '</td>
                </tr>
                <tr>
                    <td style="width:80px; font-weight:bold;">Tgl. Job</td>
                    <td style="width:15px; font-weight:bold;">:</td>
                    <td style="width:120px; font-weight:bold;">' . $obj->formatDBDate($rsDetailInvoice[0]['trdate'], 'd-M-Y') . '</td>
                </tr>
            </table>
            <div style="clear:both"></div>
        ';

        if ($typePrint == 'dropPoint') {

        //tipe drop point
        $rsWorkOrderCol = $workOrderData[$joborder['pkey']];

        // $obj->setLog($rsWorkOrderCol, true);

        //<th class="border-top-left border-bottom" style="font-weight:bold;font-size:9.8px;width:80px;text-align:center">TGL</th>
        //<th class="border-top-left border-bottom" style="font-weight:bold;font-size:9.8px;width:100px">PELANGGAN</th>
        //<th class="border-top-left border-bottom" style="font-weight:bold;font-size:9.8px;width:50px">JENIS</th>
        $html .= '
        <table cellpadding="3" style="width:1090px">
            <thead>
                <tr>
                    <th class="border-top-left border-bottom" style="font-weight:bold;font-size:9.8px;width:30px;text-align:right">NO</th>
                    <th class="border-top-left border-bottom" style="font-weight:bold;font-size:9.8px;width:120px">TUJUAN</th>
                    <th class="border-top-left border-bottom" style="font-weight:bold;font-size:9.8px;width:70px">JENIS MOBIL</th>
                    <th class="border-top-left border-bottom" style="font-weight:bold;font-size:9.8px;width:80px">NOPOL</th>
                    <th class="border-top-left border-bottom" style="font-weight:bold;font-size:9.8px;width:100px">NAMA</th>
                    <th class="border-top-left border-bottom" style="font-weight:bold;font-size:9.8px;width:50px;text-align:right">MUAT</th>
            ';

        foreach ($rsTruckingCost as $cost) {
            $html .= '<th class="border-top-left border-bottom" style="font-weight:bold;font-size:9.8px;text-align:right;width:' . $dynamicWidth . 'px">' . $cost['name'] . '</th>';


            if ($cost['name'] == 'CRT')
                $html .= '<th class="border-top-left border-bottom" style="font-weight:bold;font-size:9.8px;text-align:right;width:' . $dynamicWidth . 'px">UANG CRT</th>';


        }

        $html .= '
                    <th class="border-top-left border-bottom" style="font-weight:bold;font-size:9.8px;width:80px;text-align:right">JUMLAH</th>
                    <th class="border-top-right border-left border-bottom" style="font-weight:bold;font-size:9.8px;width:80px">SHIPMENT</th>
                </tr>
            </thead>
        ';

        $n = 1;
        foreach ($rsWorkOrderCol as $workOrder) {

            foreach ($workOrder as $workorder) {

                if ($workorder['carkey'] != $currentCarKey) {
                    $currentCarKey = $workorder['carkey'];
                    $n = 1; // Reset penomoran
                }

                $rsCargoDetailCol = $rsCargoDetail[$workorder['pkey']];
                $rsCostCargoCol = $rsCostCargo[$workorder['pkey']];

                foreach ($rsCargoDetailCol as $cargoDetail) {

                    //<td class="border-bottom-left border-bottom" style="font-size:9.8px;width:80px;text-align:center">' . $obj->formatDBDate($joborder['trdate'], 'd-M-Y') . '</td>
                    //<td class="border-bottom-left border-bottom" style="font-size:9.8px;width:100px;">' . $joborder['customername'] . '</td>
                    //<td class="border-bottom-left border-bottom" style="font-size:9.8px;width:50px;">' . $workOrder['containername'] . '</td>

                    $cargoDestination = ($cargoDetail['destination'] ?: $cargoDetail['destinationname']);

                    $html .= '
                    <tbody>
                        <tr>
                            <td class="border-bottom-left border-bottom" style="font-size:9.8px;width:30px;text-align:right">' . $n . '.</td>
                            <td class="border-bottom-left border-bottom" style="font-size:9.8px;width:120px;">' . $cargoDestination . '</td>
                            <td class="border-bottom-left border-bottom" style="font-size:9.8px;width:70px;">' . $workorder['carcategoryname'] . '</td>
                            <td class="border-bottom-left" style="font-size:9.8px;width:80px;">' . $workorder['policenumber'] . '</td>
                            <td class="border-bottom-left border-bottom" style="font-size:9.8px;width:100px;">' . $workorder['drivername'] . '</td>
                            <td class="border-bottom-left" style="font-size:9.8px;width:50px;text-align:right">' . $obj->formatNumber($cargoDetail['qty']) . '</td>
                        ';

                    $totalCost = 0;
                    foreach ($rsTruckingCost as $cost) {

                        $costValue = 0;//cost value
                        $crtValue = 0;//uang crt value

                        foreach ($rsCostCargoCol as $costCargo) {
                            if ($costCargo['refkey'] == $cargoDetail['pkey'] && $costCargo['costkey'] == $cost['pkey']) {

                                $costValue = $obj->formatNumber($costCargo['sellingprice']); //Format the cost value

                                if ($costCargo['ismultipliedqty'] == 1) {
                                    $totalCost += $cargoDetail['qty'] * $costCargo['sellingprice'];
                                } else {
                                    $totalCost += $costCargo['sellingprice'];
                                }

                                //jika cost crt
                                if ($cost['name'] == 'CRT') {

                                    $qty = $cargoDetail['qty']; //qty
                                    $price = $costCargo['sellingprice']; //price

                                    //hitung qty dan harga
                                    $totalCrtValue = $qty * $price;
                                    $crtValue = $obj->formatNumber($totalCrtValue);//uang crt value
                                }

                                break; // Exit the loop once you find the matching cost

                            }
                        }

                        $html .= '
                                <td class="border-bottom-left" style="font-size:9.8px;width:' . $dynamicWidth . 'px;text-align:right;">' . $costValue . '</td>
                            ';

                        if ($cost['name'] == 'CRT') {
                            //jika cost crt maka tambah UANG CRT
                            $html .= '<td class="border-bottom-left" style="font-size:9.8px;width:' . $dynamicWidth . 'px;text-align:right;">' . $crtValue . '</td>';
                        }

                    }

                    $html .= '
                            <td class="border-bottom-left" style="font-size:9.8px;width:80px;text-align:right;">' . $obj->formatNumber($totalCost) . '</td>
                            <td class="border-bottom-left border-right" style="font-size:9.8px;width:80px;">' . $cargoDetail['workorder'] . '</td>
                        </tr>
                    </tbody>
                ';

                    $n++;
                }

            }

        }

        $html .= '
        </table>
    ';

    } else if($typePrint == 'kontrak') {
            //tipe kontrak
            $rsWorkOrderCol = $rsWorkOrder[$joborder['pkey']];
            $html .= '
                <table cellpadding="3" style="width:1090px">
                    <thead>
                        <tr>
                            <th class="border-top-left border-bottom" style="font-weight:bold;font-size:9.8px;width:30px;text-align:right">NO</th>
                            <th class="border-top-left border-bottom" style="font-weight:bold;font-size:9.8px;width:120px">TUJUAN</th>
                            <th class="border-top-left border-bottom" style="font-weight:bold;font-size:9.8px;width:70px">JENIS MOBIL</th>
                            <th class="border-top-left border-bottom" style="font-weight:bold;font-size:9.8px;width:80px">NOPOL</th>
                            <th class="border-top-left border-bottom" style="font-weight:bold;font-size:9.8px;width:100px">NAMA</th>
                            <th class="border-top-left border-bottom" style="font-weight:bold;font-size:9.8px;width:50px;text-align:right">MUAT</th>
                    ';

            foreach ($rsTruckingCost as $cost) {
                $html .= '<th class="border-top-left border-bottom" style="font-weight:bold;font-size:9.8px;text-align:right;width:' . $dynamicWidth . 'px">' . $cost['name'] . '</th>';


                if ($cost['name'] == 'CRT')
                    $html .= '<th class="border-top-left border-bottom" style="font-weight:bold;font-size:9.8px;text-align:right;width:' . $dynamicWidth . 'px">UANG CRT</th>';


            }

            $html .= '
                        <th class="border-top-left border-bottom" style="font-weight:bold;font-size:9.8px;width:80px;text-align:right">JUMLAH</th>
                        <th class="border-top-right border-left border-bottom" style="font-weight:bold;font-size:9.8px;width:80px">SHIPMENT</th>
                    </tr>
                </thead>
            ';

            $n = 1;
            foreach ($rsWorkOrderCol as $workorder) {


                $rsCargoDetailCol = $rsCargoDetail[$workorder['pkey']];
                $rsCostCargoCol = $rsCostCargo[$workorder['pkey']];

                foreach ($rsCargoDetailCol as $cargoDetail) {
                    $cargoDestination = ($cargoDetail['destination'] ?: $cargoDetail['destinationname']);
                    $html .= '
                            <tbody>
                                <tr>
                                    <td class="border-bottom-left border-bottom" style="font-size:9.8px;width:30px;text-align:right">' . ($n) . '.</td>
                                    <td class="border-bottom-left border-bottom" style="font-size:9.8px;width:120px;">' . $cargoDestination . '</td>
                                    <td class="border-bottom-left border-bottom" style="font-size:9.8px;width:70px;">' . $workorder['carcategoryname'] . '</td>
                                    <td class="border-bottom-left" style="font-size:9.8px;width:80px;">' . $workorder['policenumber'] . '</td>
                                    <td class="border-bottom-left border-bottom" style="font-size:9.8px;width:100px;">' . $workorder['drivername'] . '</td>
                                    <td class="border-bottom-left" style="font-size:9.8px;width:50px;text-align:right">' . $obj->formatNumber($cargoDetail['qty']) . '</td>
                                ';

                    $totalCost = 0;
                    foreach ($rsTruckingCost as $cost) {

                        $costValue = 0;//cost value
                        $crtValue = 0;//uang crt value

                        foreach ($rsCostCargoCol as $costCargo) {
                            if ($costCargo['refkey'] == $cargoDetail['pkey'] && $costCargo['costkey'] == $cost['pkey']) {

                                $costValue = $obj->formatNumber($costCargo['sellingprice']); //Format the cost value

                                if ($costCargo['ismultipliedqty'] == 1) {
                                    $totalCost += $cargoDetail['qty'] * $costCargo['sellingprice'];
                                } else {
                                    $totalCost += $costCargo['sellingprice'];
                                }

                                //jika cost crt
                                if ($cost['name'] == 'CRT') {

                                    $qty = $cargoDetail['qty']; //qty
                                    $price = $costCargo['sellingprice']; //price

                                    //hitung qty dan harga
                                    $totalCrtValue = $qty * $price;
                                    $crtValue = $obj->formatNumber($totalCrtValue);//uang crt value
                                }

                                break; // Exit the loop once you find the matching cost

                            }
                        }

                        $html .= '
                                        <td class="border-bottom-left" style="font-size:9.8px;width:' . $dynamicWidth . 'px;text-align:right;">' . $costValue . '</td>
                                    ';

                        if ($cost['name'] == 'CRT') {
                            //jika cost crt maka tambah UANG CRT
                            $html .= '<td class="border-bottom-left" style="font-size:9.8px;width:' . $dynamicWidth . 'px;text-align:right;">' . $crtValue . '</td>';
                        }

                    }

                    $html .= '
                                    <td class="border-bottom-left" style="font-size:9.8px;width:80px;text-align:right;">' . $obj->formatNumber($totalCost) . '</td>
                                    <td class="border-bottom-left border-right" style="font-size:9.8px;width:80px;">' . $cargoDetail['workorder'] . '</td>
                                </tr>
                            </tbody>
                        ';
                    $n++;
                }

            }

            $html .= '</table>';
    }

        $html .= '<div style="clear:both"></div>';

    

    }




    return $html;

}

    ?>
