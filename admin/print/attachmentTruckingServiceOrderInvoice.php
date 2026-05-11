<?php
$PRINT_SETTINGS = array(
    'showPrintHeader' => false,
    'showPrintFooter' => false
);

includeClass('TruckingServiceOrderInvoice.class.php');
$truckingServiceOrderInvoice = createObjAndAddToCol(new TruckingServiceOrderInvoice());

$obj = $truckingServiceOrderInvoice;

$generateReportContent = function ($dataset) {

    $obj = new TruckingServiceOrderInvoice();
    $truckingServiceWorkOrder = new TruckingServiceWorkOrder();
    $truckingServiceOrder = new TruckingServiceOrder();

    $groupType = $_GET['groupby'];
    $rs = $dataset['rs'];

    $rsDetail = $obj->getDetailWithRelatedInformation($rs[0]['pkey']);
    $rsItemDetail = $obj->getItemDetail($rs[0]['pkey'], 'refheaderkey');

    $arrJOKey = array_column($rsDetail, 'salesorderkey');

    $rsJobOrder = $truckingServiceOrder->searchData('','',true, ' and '. $truckingServiceOrder->tableName.'.pkey in ('. $obj->oDbCon->paramString($arrJOKey,',') .') ');
    $categoryName = $rsJobOrder[0]['categoryname'];

    //work order
    $rsWorkOrder = $truckingServiceWorkOrder->searchData('','', true, ' and ' . $truckingServiceWorkOrder->tableName.'.refkey in ('. $obj->oDbCon->paramString($arrJOKey,',') .')
                                                            and '. $truckingServiceWorkOrder->tableName .'.statuskey in (3) order by code,trdate asc ');

    $rsWorkOrderDetail = $truckingServiceWorkOrder->getDataForPrintInvoiceAttachment($arrJOKey);

    //Filter SPPK sudah terdaftar harga 0 maka tidak muncul
    $existingData = [];
    $workOrderData = [];
    foreach ($rsWorkOrderDetail as $row) {
        if (!in_array($row['workorderdetailkey'], $existingData) || $row['sellingprice'] != 0) {
            $workOrderData[] = $row;
            $existingData[] = $row['workorderdetailkey'];
        }
    }
    
    $rsWorkOrderDetail = $obj->reindexDetailCollections($workOrderData, 'woheaderkey');
    $rsWorkOrderDetailJO = $obj->reindexDetailCollections($workOrderData, 'jokey');

    $rsDetails = $obj->reindexDetailCollections($rsDetail, 'salesorderkey');

    $itemIndex = [];
    $aliasMap = [];
    foreach ($rsItemDetail as $itemDetail) {
        $itemIndex[$itemDetail['refsodetailkey']][$itemDetail['itemkey']] = true;
        $aliasMap[$itemDetail['refsodetailkey']][$itemDetail['itemkey']] = $itemDetail['aliasname'];
    }

 

    $html = $obj->printSetting['defaultStyle'];

    if ($groupType == 'workOrder') {

    $n = 0;
    foreach($rsWorkOrder as $key => $workorder) {

        $rsWorkOrderDetailCol = $rsWorkOrderDetail[$workorder['pkey']];

        $html .= ' 
            <div style="clear:both"></div>
            <table cellpadding="2" > 
            <tr><td><div class="title">RENTAL DETAIL</div></td></tr>
            </table> 
        ';

        $html .= ' 
            <table width="850px">
                <tr>
                    <td style="width:430px">
                        <table cellpadding="4">
                            <tr>
                                <td><b>Kepada Yth,</b></td>
                            </tr>
                            <tr>
                                <td>' . $rs[0]['customername'] . '</td>
                            </tr>
                        </table>
                    </td>
                    <td style="width:365px">
                        <table cellpadding="4" >
                            <tr>
                                <td style="width:100px"><b>No. Job</b></td>
                                <td style="width:15px;text-align:center">:</td>
                                <td>' . $workorder['code'] . '</td>
                            </tr>
                            <tr>
                                <td style="width:100px"><b>Tgl. Job</b></td>
                                <td style="width:15px;text-align:center">:</td>
                                <td>' . $obj->formatDBDate($workorder['trdate'], 'd-M-Y') . '</td>
                            </tr>
                            <tr>
                                <td style="width:100px"><b>Nama Job</b></td>
                                <td style="width:15px;text-align:center">:</td>
                                <td>'. $categoryName .'</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        ';

            $html .= '
            <table cellpadding="3" style="width:100%;border-collapse:collapse;">
                <thead>
                    <tr>
                        <td style="font-size:0.8em;font-weight:bold;width:35px;text-align:center;border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;background-color:#C0C0C0;">NO</td>
                        <td style="font-size:0.8em;font-weight:bold;width:80px;text-align:center;border-left:1px solid black;border-right:1px solid black;border-top:1px solid black; border-bottom:1px solid black;background-color:#C0C0C0">TANGGAL</td>
                        <td style="font-size:0.8em;font-weight:bold;width:145px;text-align:center;border-left:1px solid black;border-right:1px solid black;border-top:1px solid black; border-bottom:1px solid black;background-color:#C0C0C0">TUJUAN</td>
                        <td style="font-size:0.8em;font-weight:bold;width:60px;text-align:center;border-left:1px solid black;border-right:1px solid black;border-top:1px solid black; border-bottom:1px solid black;background-color:#C0C0C0">JENIS MOBIL</td>
                        <td style="font-size:0.8em;font-weight:bold;width:70px;text-align:center;border-left:1px solid black;border-right:1px solid black;border-top:1px solid black; border-bottom:1px solid black;background-color:#C0C0C0">NO POL</td>
                        <td style="font-size:0.8em;font-weight:bold;width:40px;text-align:center;border-left:1px solid black;border-right:1px solid black;border-top:1px solid black; border-bottom:1px solid black;background-color:#C0C0C0">QTY</td>
                        <td style="font-size:0.8em;font-weight:bold;width:90px;text-align:center;border-left:1px solid black;border-right:1px solid black;border-top:1px solid black; border-bottom:1px solid black;background-color:#C0C0C0">SHIP/SPPK</td>
                        <td style="font-size:0.8em;font-weight:bold;width:70px;text-align:center;border-left:1px solid black;border-right:1px solid black;border-top:1px solid black; border-bottom:1px solid black;background-color:#C0C0C0">HARGA</td>
                        <td style="font-size:0.8em;font-weight:bold;width:90px;text-align:center;border-left:1px solid black;border-top:1px solid black; border-bottom:1px solid black;border-right:1px solid black;background-color:#C0C0C0">KETERANGAN</td>
                    </tr>
                </thead>
            <tbody>
        ';


            foreach($rsWorkOrderDetailCol as $workOrderDetail) {

                $rsDetailCol = $rsDetails[$workOrderDetail['jokey']];

                $jodetailkey = $workOrderDetail['jodetailkey'];
                $costkey = $workOrderDetail['costkey'];

                $qty = ($workOrderDetail['ismultipliedqty'] == 1) ? $obj->formatNumber($workOrderDetail['qty']) : '-';

                $totalSelling = $workOrderDetail['sellingprice'];
                if($workOrderDetail['ismultipliedqty'] == 1) {
                    $totalSelling = $workOrderDetail['qty'] * $workOrderDetail['sellingprice'];
                }

                $description = '';
                if($totalSelling > 0) {
                    $description = isset($aliasMap[$jodetailkey][$costkey]) && !empty($aliasMap[$jodetailkey][$costkey]) ? $aliasMap[$jodetailkey][$costkey] : $workOrderDetail['costname'];
                }

                $spkDate = (isset($_GET) && !empty($_GET['datetype']==1)) ? $rsDetailCol[0]['trdate'] : $workOrderDetail['stuffingdatetime'];
                
                $workOrderDestination = ($workOrderDetail['destination'] ?: $workOrderDetail['destinationname']);

                $html .= '  
                        <tr>
                            <td style="text-align:center;font-size:0.8em;width:35px;border-left:1px solid black;border-bottom:1px solid black;">' . ($n+1) . '</td>
                            <td style="text-align:center;font-size:0.8em;width:80px;border-left:1px solid black;border-bottom:1px solid black;">'. $obj->formatDBDate($spkDate, 'd-M-Y') .'</td>
                            <td style="font-size:0.8em;width:145x;border-left:1px solid black;border-bottom:1px solid black;">' . $workOrderDestination . '</td>
                            <td style="text-align:center;font-size:0.8em;width:60px;border-left:1px solid black;border-bottom:1px solid black;">'. $workOrderDetail['carcategoryname'] .'</td>
                            <td style="text-align:center;font-size:0.8em;width:70px;border-left:1px solid black;border-bottom:1px solid black;">'. $workOrderDetail['policenumber'] .'</td>
                            <td style="text-align:center;font-size:0.8em;width:40px;border-left:1px solid black;border-bottom:1px solid black;">'. $qty .'</td>
                            <td style="text-align:center;font-size:0.8em;width:90px;border-left:1px solid black;border-bottom:1px solid black;">' . $workOrderDetail['workorder'] . '</td>
                            <td style="text-align:right;font-size:0.8em;width:70px;border-left:1px solid black;border-bottom:1px solid black;">'. $obj->formatNumber($totalSelling) .'</td>
                            <td style="font-size:0.8em;width:90px;border-left:1px solid black;border-bottom:1px solid black;border-right:1px solid black;">' . $description . '</td>
                        </tr>
                    ';

            }


        $html .= '
            </tbody>
        </table>
        ';
        $html .= '<div style="clear:both"></div>';

        $n++;
    }
 } else if($groupType == 'jobOrder') {

        $n = 0;
        foreach ($rsDetail as $key => $detail) {

            $rsWorkOrderDetailJOCol = $rsWorkOrderDetailJO[$detail['salesorderkey']];

            $html .= ' 
                <div style="clear:both"></div>
                <table cellpadding="2" > 
                <tr><td><div class="title">RENTAL DETAIL</div></td></tr>
                </table> 
            ';

            $html .= ' 
                <table width="850px">
                    <tr>
                        <td style="width:430px">
                            <table cellpadding="4">
                                <tr>
                                    <td><b>Kepada Yth,</b></td>
                                </tr>
                                <tr>
                                    <td>' . $rs[0]['customername'] . '</td>
                                </tr>
                            </table>
                        </td>
                        <td style="width:365px">
                            <table cellpadding="4" >
                                <tr>
                                    <td style="width:100px"><b>No. Job</b></td>
                                    <td style="width:15px;text-align:center">:</td>
                                    <td>' . $detail['socode'] . '</td>
                                </tr>
                                <tr>
                                    <td style="width:100px"><b>Tgl. Job</b></td>
                                    <td style="width:15px;text-align:center">:</td>
                                    <td>' . $obj->formatDBDate($detail['trdate'], 'd-M-Y') . '</td>
                                </tr>
                                <tr>
                                    <td style="width:100px"><b>Nama Job</b></td>
                                    <td style="width:15px;text-align:center">:</td>
                                    <td>' . $categoryName . '</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            ';

            $html .= '
                <table cellpadding="3" style="width:100%;border-collapse:collapse;">
                    <thead>
                        <tr>
                            <td style="font-size:0.8em;font-weight:bold;width:35px;text-align:center;border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;background-color:#C0C0C0;">NO</td>
                            <td style="font-size:0.8em;font-weight:bold;width:80px;text-align:center;border-left:1px solid black;border-right:1px solid black;border-top:1px solid black; border-bottom:1px solid black;background-color:#C0C0C0">TANGGAL</td>
                            <td style="font-size:0.8em;font-weight:bold;width:145px;text-align:center;border-left:1px solid black;border-right:1px solid black;border-top:1px solid black; border-bottom:1px solid black;background-color:#C0C0C0">TUJUAN</td>
                            <td style="font-size:0.8em;font-weight:bold;width:60px;text-align:center;border-left:1px solid black;border-right:1px solid black;border-top:1px solid black; border-bottom:1px solid black;background-color:#C0C0C0">JENIS MOBIL</td>
                            <td style="font-size:0.8em;font-weight:bold;width:70px;text-align:center;border-left:1px solid black;border-right:1px solid black;border-top:1px solid black; border-bottom:1px solid black;background-color:#C0C0C0">NO POL</td>
                            <td style="font-size:0.8em;font-weight:bold;width:40px;text-align:center;border-left:1px solid black;border-right:1px solid black;border-top:1px solid black; border-bottom:1px solid black;background-color:#C0C0C0">QTY</td>
                            <td style="font-size:0.8em;font-weight:bold;width:90px;text-align:center;border-left:1px solid black;border-right:1px solid black;border-top:1px solid black; border-bottom:1px solid black;background-color:#C0C0C0">SHIP/SPPK</td>
                            <td style="font-size:0.8em;font-weight:bold;width:70px;text-align:center;border-left:1px solid black;border-right:1px solid black;border-top:1px solid black; border-bottom:1px solid black;background-color:#C0C0C0">HARGA</td>
                            <td style="font-size:0.8em;font-weight:bold;width:90px;text-align:center;border-left:1px solid black;border-top:1px solid black; border-bottom:1px solid black;border-right:1px solid black;background-color:#C0C0C0">KETERANGAN</td>
                        </tr>
                    </thead>
                <tbody>
            ';

            foreach ($rsWorkOrderDetailJOCol as $workOrderDetail) {


                $rsDetailCol = $rsDetails[$workOrderDetail['jokey']];

                $jodetailkey = $workOrderDetail['jodetailkey'];
                $costkey = $workOrderDetail['costkey'];

                $qty = ($workOrderDetail['ismultipliedqty'] == 1) ? $obj->formatNumber($workOrderDetail['qty']) : '-';

                $totalSelling = $workOrderDetail['sellingprice'];
                if ($workOrderDetail['ismultipliedqty'] == 1) {
                    $totalSelling = $workOrderDetail['qty'] * $workOrderDetail['sellingprice'];
                }
                $workOrderDestination = ($workOrderDetail['destination'] ?: $workOrderDetail['destinationname']);
                $description = isset($aliasMap[$jodetailkey][$costkey]) && !empty($aliasMap[$jodetailkey][$costkey]) ? $aliasMap[$jodetailkey][$costkey] : $workOrderDetail['costname'];

                $html .= '  
                            <tr>
                                <td style="text-align:center;font-size:0.8em;width:35px;border-left:1px solid black;border-bottom:1px solid black;">' . ($n + 1) . '</td>
                                <td style="text-align:center;font-size:0.8em;width:80px;border-left:1px solid black;border-bottom:1px solid black;">' . $obj->formatDBDate($rsDetailCol[0]['trdate'], 'd-M-Y') . '</td>
                                <td style="font-size:0.8em;width:145x;border-left:1px solid black;border-bottom:1px solid black;">' . $workOrderDestination . '</td>
                                <td style="text-align:center;font-size:0.8em;width:60px;border-left:1px solid black;border-bottom:1px solid black;">' . $workOrderDetail['carcategoryname'] . '</td>
                                <td style="text-align:center;font-size:0.8em;width:70px;border-left:1px solid black;border-bottom:1px solid black;">' . $workOrderDetail['policenumber'] . '</td>
                                <td style="text-align:center;font-size:0.8em;width:40px;border-left:1px solid black;border-bottom:1px solid black;">' . $qty . '</td>
                                <td style="text-align:center;font-size:0.8em;width:90px;border-left:1px solid black;border-bottom:1px solid black;">' . $workOrderDetail['workorder'] . '</td>
                                <td style="text-align:right;font-size:0.8em;width:70px;border-left:1px solid black;border-bottom:1px solid black;">' . $obj->formatNumber($totalSelling) . '</td>
                                <td style="font-size:0.8em;width:90px;border-left:1px solid black;border-bottom:1px solid black;border-right:1px solid black;">' . $description . '</td>
                            </tr>
                        ';

            }

            $html .= '
                </tbody>
            </table>
            ';
            $html .= '<div style="clear:both"></div>';

            $n++;

        }

    } else {

    }

    return $html;

}

    ?>
