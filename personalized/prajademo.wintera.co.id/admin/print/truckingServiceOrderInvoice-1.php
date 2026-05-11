<?php

$profileImg = $class->loadSetting('companyLogo');
$imgLetterhead = '';// $obj->phpThumbURLSrc . 'setting/companyLogo/' . $profileImg;

$pdf->setCustomSettings(
    array(
        'header' => '',
        'showPrintHeader' => false,
        'showPrintFooter' => false,
    )
);

includeClass('TruckingServiceOrderInvoice.class.php');
$truckingServiceOrderInvoice = createObjAndAddToCol(new TruckingServiceOrderInvoice());

$obj = $truckingServiceOrderInvoice;

$generateReportContent = function ($dataset) {

    $obj = new TruckingServiceOrderInvoice();
    $truckingServiceOrder = new TruckingServiceOrder();
    $truckingServiceWorkOrder = new TruckingServiceWorkOrder();
    $paymentMethod = new PaymentMethod();
    $customCode = new CustomCode();

    $rs = $dataset['rs'];

    $rsInvoiceType = $customCode->searchData($customCode->tableName . '.pkey', $rs[0]['customcodekey'], true);
    $invoiceTitle = (!empty($rsInvoiceType[0]['title'])) ? $rsInvoiceType[0]['title'] : $rsInvoiceType[0]['name'];


    $rsPaymentMethod = $paymentMethod->getDataRowById($rs[0]['companybankkey']);
    $rsDetail = $obj->getDetailWithRelatedInformation($rs[0]['pkey']);
    $rsItemDetail = $obj->getItemDetail($rs[0]['pkey'], 'refheaderkey');

    $arrDetailWOKey = array_column($rsDetail, 'pkey');
    $arrSalesOrderKey = array_column($rsDetail, 'salesorderkey');


    $rsJobOrder = $truckingServiceOrder->searchData('', '', true, ' and ' . $truckingServiceOrder->tableName . '.pkey in (' . $obj->oDbCon->paramString($arrSalesOrderKey, ',') . ') ');
    $categoryName = $rsJobOrder[0]['categoryname'];

    $rsData = $truckingServiceOrder->getDataForPrintInvoice($arrSalesOrderKey);
    $rsSelling = $truckingServiceOrder->getSellingDataForPrintInvoice($arrSalesOrderKey);

    $arrJOKey = array_column($rsData, 'joheaderkey');
    $rsWorkOrder = $truckingServiceWorkOrder->searchData('', '', true, ' and ' . $truckingServiceWorkOrder->tableName . '.refkey in (' . $obj->oDbCon->paramString($arrJOKey, ',') . ') ');
    $rsWorkOrder = $obj->reindexDetailCollections($rsWorkOrder, 'refkey');

    // $obj->setLog($rsWorkOrder, true);

    //get data utk JO Kontrak
    $rsJobOrderContract = $truckingServiceOrder->getDataForPrintInvoiceContract($arrSalesOrderKey);

    $arrWOKey = array_column($rsData, 'workorderkey');
    $rsDetailReindex = $obj->reindexDetailCollections($rsDetail, 'salesorderkey');

    $rsTotalQty = $truckingServiceWorkOrder->getTotalQtyCargoDetail($arrWOKey);

    $rsDetailCargo = $truckingServiceWorkOrder->getCargoDetail($arrWOKey);
    $rsDetailCargoWO = $obj->reindexDetailCollections($rsDetailCargo, 'refkey');


    $itemIndex = [];
    $aliasMap = [];
    foreach ($rsItemDetail as $itemDetail) {
        $itemIndex[$itemDetail['refsodetailkey']][$itemDetail['itemkey']] = true;
        $aliasMap[$itemDetail['refsodetailkey']][$itemDetail['itemkey']] = $itemDetail['aliasname'];
    }
    
    $rsSelling = $obj->reindexDetailCollections($rsSelling, 'indexkey');
    $rsTotalQty = $obj->reindexDetailCollections($rsTotalQty, 'refkey');

    //GROUPING BY NOPOL
    $arrJOContract = array();
    foreach ($rsJobOrderContract as $contract) {
        $groupkey = $contract['carkey'];

        if (!isset($arrJOContract[$groupkey])) {
            $arrJOContract[$groupkey] = array(
                'joheaderkey' => $contract['joheaderkey'],
                'policenumber' => $contract['policenumber'],
                'itemname' => $contract['itemname'],
                'carcategoryname' => $contract['carcategoryname'],
                'description' => $contract['description']
            );
        }

    }

    $rsJobOrderContract = $arrSelling = array_values($arrJOContract);

    $html = $obj->printSetting['defaultStyle'];

    $profileImg = $obj->loadSetting('companyLogo');
    $imgLetterhead =  $obj->phpThumbURLSrc . 'setting/companyLogo/' . $profileImg;

    $html .= '<table><tr>
	  					<td style="width:230px"><img src="' . $imgLetterhead . '" style="height: 100px"></td>
	  					<td style="width:200px"></td>
	  					<td style="width:235px"><table><tr>
                            <td style="width:20px;font-size:2em;border-left:1px solid black;"></td>
                            <td style="width:215px;text-align:left; font-size:0.9em;"><h3>' . $obj->loadSetting('companyName') . '</h3><br>' . nl2br($obj->loadSetting('companyAddress')) . '</td>
                        </tr></table></td>
					</tr></table>';

    $html .= ' 
    <h1 style="text-align:center">'.strtoupper($invoiceTitle).'</h1> 
    
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
                        <td style="width:100px"><b>No. ' . $invoiceTitle . '</b></td>
                        <td style="width:15px;text-align:center">:</td>
                        <td>' . $rs[0]['code'] . '</td>
                    </tr>
                    <tr>
                        <td style="width:100px"><b>Tgl. ' . $invoiceTitle . '</b></td>
                        <td style="width:15px;text-align:center">:</td>
                        <td>' . $obj->formatDBDate($rs[0]['trdate'], 'd-M-Y') . '</td>
                    </tr>
                    <tr>
                        <td style="width:100px"><b>Keterangan</b></td>
                        <td style="width:15px;text-align:center">:</td>
                        <td>' . $categoryName . '</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    ';

    $html .= '<div style="clear:both"></div>';

    $html .= '
    <table cellpadding="3" style="width:100%l; font-size:0.9em"> 
            <tr>
                <td style="font-size:10px;font-weight:bold;width:35px;text-align:center;border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black; text-align:right">NO</td>
                <td style="font-size:10px;font-weight:bold;width:80px;text-align:center;border-left:1px solid black;border-top:1px solid black; border-bottom:1px solid black;">TANGGAL</td>
                <td style="font-size:10px;font-weight:bold;width:110px;text-align:center;border-left:1px solid black;border-top:1px solid black; border-bottom:1px solid black;">TUJUAN</td>
                <td style="font-size:10px;font-weight:bold;width:75px;text-align:center;border-left:1px solid black;border-top:1px solid black; border-bottom:1px solid black;">JENIS MOBIL</td>
                <td style="font-size:10px;font-weight:bold;width:70px;text-align:center;border-left:1px solid black;border-top:1px solid black; border-bottom:1px solid black;">NO POL</td>
                <td style="font-size:10px;font-weight:bold;width:40px;text-align:center;border-left:1px solid black;border-top:1px solid black; border-bottom:1px solid black;">QTY</td>
                <td style="font-size:10px;font-weight:bold;width:80px;text-align:center;border-left:1px solid black;border-top:1px solid black; border-bottom:1px solid black;">SHIP/SPPK</td>
                <td style="font-size:10px;font-weight:bold;width:90px;text-align:center;border-left:1px solid black;border-top:1px solid black; border-bottom:1px solid black;; border-right:1px solid black; ">TOTAL HARGA</td>
				<td style="font-size:10px;font-weight:bold;width:95px;text-align:center;border-left:1px solid black;border-top:1px solid black; border-bottom:1px solid black;border-right:1px solid black;">KETERANGAN</td>
            </tr> 
        <tbody>
        ';

    //TIPE ONCALL
    if (strtolower($categoryName) != 'kontrak') {

        $counter = 1;
        for ($i = 0; $i < count($rsData); $i++) {

            $pkey = $rsData[$i]['pkey'];
            $itemkey = $rsData[$i]['itemkey'];

            $rsSellingCol = $rsSelling[$rsData[$i]['indexkey']];

            if (isset($itemIndex[$pkey][$itemkey])) {

                $rsDetailCol = $rsDetailReindex[$rsData[$i]['joheaderkey']];
                $rsTotalQtyCol = $rsTotalQty[$rsData[$i]['workorderkey']];

                $rsWorkOrderCol = $rsWorkOrder[$rsData[$i]['joheaderkey']];
                $rsDetailCargoWOCol = $rsDetailCargoWO[$rsData[$i]['workorderkey']];


                $qty = $rsTotalQtyCol[0]['totalqty'];
                $subtotal = $rsData[$i]['priceinunit'];
                $description = '';


                //$shipSppk = '';
                //$destination = $rsData[$i]['routefrom'] . ' - ' . $rsData[$i]['routeto'];

                //destination value
                // foreach ($rsWorkOrderCol as $WO) {
                //     array_push($destinations, $WO['routefrom'] . ' - ' . $WO['routeto']);
                // }
                // 
                    
                // $shipSPPK = [];
                // $destinations = [];
                // foreach ($rsDetailCargoWOCol as $WODetail) {
                //     array_push($shipSPPK, $WODetail['workorder']);
                //     array_push($destinations, $WODetail['destination']);
                // }

                // $shipSppk = (!empty($shipSPPK) ? implode(', ', $shipSPPK) : '');
                // $destination = (!empty($destinations) ? implode(', ', $destinations) : '-');

                $shipSppk = $rsDetailCargoWOCol[0]['workorder'];
                $destination = $rsDetailCargoWOCol[0]['destination'];
            
                $html .= '
                        <tr>
                            <td style="border-left:1px solid black;border-bottom:1px solid black;text-align:right;">' . $counter . '</td>
                            <td style="text-align:center;border-left:1px solid black;border-bottom:1px solid black;">' . $obj->formatDBDate($rsDetailCol[0]['trdate'], 'd-M-Y') . '</td>
                            <td style="border-left:1px solid black;border-bottom:1px solid black;">' . $destination . '</td>
                            <td style="text-align:center;border-left:1px solid black;border-bottom:1px solid black;">' . $rsData[$i]['carcategoryname'] . '</td>
                            <td style="text-align:center;border-left:1px solid black;border-bottom:1px solid black;">' . $rsData[$i]['policenumber'] . '</td>
                            <td style="text-align:center; border-left:1px solid black;border-bottom:1px solid black;">' . $obj->formatNumber($qty) . '</td>
                            <td style="text-align:center; border-left:1px solid black;border-bottom:1px solid black;">' . $shipSppk . '</td> 
                            <td style="text-align:right; border-left:1px solid black;border-bottom:1px solid black;border-right:1px solid black;">' . $obj->formatNumber($subtotal) . '</td>
                            <td style="border-left:1px solid black;border-bottom:1px solid black;border-right:1px solid black;">' . $description . '</td>
                        </tr>
                    ';

                $counter++;
            }


            if (!empty($rsSellingCol)) {
                for ($j = 0; $j < count($rsSellingCol); $j++) {

                    $pkey = $rsSellingCol[$j]['pkey'];
                    $costkey = $rsSellingCol[$j]['costkey'];
  
                    if ((isset($itemIndex[$pkey][$costkey]))) {

                        $rsDetailCols = $rsDetailReindex[$rsSellingCol[$j]['joheaderkey']];
                        $qtyWorkOrder = $rsSellingCol[$j]['qtyworkorder'];
                        $subTotal = $rsSellingCol[$j]['subtotal'];
                    
                        $description = isset($aliasMap[$pkey][$costkey]) && !empty($aliasMap[$pkey][$costkey]) ? $aliasMap[$pkey][$costkey] : $rsSellingCol[$j]['costname'];

                        $shipSppk = $rsSellingCol[$j]['workorder'];
                        $destination = $rsSellingCol[$j]['destination'];

                        $html .= '
                                <tr>
                                    <td style="border-left:1px solid black;border-bottom:1px solid black;text-align:right;">' . $counter . '</td>
                                    <td style="text-align:center;border-left:1px solid black;border-bottom:1px solid black;">' . $obj->formatDBDate($rsDetailCols[0]['trdate'], 'd-M-Y') . '</td>
                                    <td style="border-left:1px solid black;border-bottom:1px solid black;">' . $destination . '</td>
                                    <td style="text-align:center;border-left:1px solid black;border-bottom:1px solid black;">' . $rsSellingCol[$j]['carcategoryname'] . '</td>
                                    <td style="text-align:center;border-left:1px solid black;border-bottom:1px solid black;">' . $rsSellingCol[$j]['policenumber'] . '</td>
                                    <td style="text-align:center; border-left:1px solid black;border-bottom:1px solid black;">' . $obj->formatNumber($qtyWorkOrder) . '</td>
                                    <td style="text-align:center; border-left:1px solid black;border-bottom:1px solid black;">' . $shipSppk . '</td>
                                    <td style="text-align:right; border-left:1px solid black;border-bottom:1px solid black;border-right:1px solid black;">' . $obj->formatNumber($subTotal) . '</td>
                                    <td style="border-left:1px solid black;border-bottom:1px solid black;border-right:1px solid black;">' . $description . '</td>
                                </tr>
                            ';

                        $counter++;
                    }
                }
            }

        }

    } else {
        //Untuk tipe kontrak asumsi semua SPINMILL & INDAH JAYA

        for ($k = 0; $k < count($rsJobOrderContract); $k++) {

            $rsDetailCols = $rsDetailReindex[$rsJobOrderContract[$k]['joheaderkey']];

            $qty = 0; //QTY untuk kontrak 0
            $tujuan = 'KOTA - KOTA';
            $shipSPPK = 'TERLAMPIR';

            if ($k === 0) {
                $subTotal = $rs[0]['subtotal'];
                $description = $rsJobOrderContract[$k]['description'];
            } else {
                $subTotal = 0;
                $description = '';
            }

            $html .= '
                    <tr>
                        <td style="border-left:1px solid black;border-bottom:1px solid black;text-align:right;">' . ($k + 1) . '</td>
                        <td style="text-align:center;border-left:1px solid black;border-bottom:1px solid black;">' . $obj->formatDBDate($rsDetailCols[0]['trdate'], 'd-M-Y') . '</td>
                        <td style="border-left:1px solid black;border-bottom:1px solid black;">' . $tujuan . '</td>
                        <td style="text-align:center;border-left:1px solid black;border-bottom:1px solid black;">' . $rsJobOrderContract[$k]['carcategoryname'] . '</td>
                        <td style="text-align:center;border-left:1px solid black;border-bottom:1px solid black;">' . $rsJobOrderContract[$k]['policenumber'] . '</td>
                        <td style="text-align:center; border-left:1px solid black;border-bottom:1px solid black;">' . $obj->formatNumber($qty) . '</td>
                        <td style="text-align:center; border-left:1px solid black;border-bottom:1px solid black;">' . $shipSPPK . '</td>
                        <td style="text-align:right; border-left:1px solid black;border-bottom:1px solid black;border-right:1px solid black;">' . $obj->formatNumber($subTotal) . '</td>
                        <td style="border-left:1px solid black;border-bottom:1px solid black;border-right:1px solid black;">' . $description . '</td>
                    </tr>
                ';
        }

    }

    $html .= '
        </tbody>
    </table>
    
    ';
    $totalInvoice = $rs[0]['grandtotal'] - $rs[0]['tax23value'];

    $html .= '
    <table cellpadding="3" style="width:100%">
        <tr>
            <td style="width:330px">
                

			</td>
            <td width:300px>
                <table cellpadding="2">
                    <tr>
                        <td style="width:100px"><b>Sub Total</b></td>
                        <td style="width:40px;"><b>( Rp )</b></td>
                        <td style="width:100px;text-align:right;font-weight:bold;">' . $obj->formatNumber($rs[0]['subtotal']) . '</td>
                    </tr>
                ';

    if ($rs[0]['finaldiscount'] > 0) {

        if ($rs[0]['finaldiscounttype'] == 1) {
            $html .= '
                                            <tr>
                                                <td style="width:100px"><b>Diskon</b></td>
                                                <td style="width:40px;"><b>( Rp )</b></td>
                                                <td style="width:100px;text-align:right;font-weight:bold;">' . $obj->formatNumber($rs[0]['finaldiscount']) . '</td>
                                            </tr>
                                        ';
        } else {

            $finalDiscount = $rs[0]['finaldiscount'];
            $discountValue = $finalDiscount / 100 * $rs[0]['subtotal'];

            $html .= '
                                            <tr>
                                                <td style="width:100px"><b>Diskon ' . $obj->formatNumber($rs[0]['finaldiscount']) . ' %</b></td>
                                                <td style="width:40px;"><b>( Rp )</b></td>
                                                <td style="width:100px;text-align:right;font-weight:bold;">' . $obj->formatNumber($discountValue) . '</td>
                                            </tr>
                                        ';
        }
    }

    $html .= '
                    <tr>
                        <td style="width:100px"><b>PPn ' . $obj->formatNumber($rs[0]['taxpercentage']) . ' %</b></td>
                        <td style="width:40px;"><b>( Rp )</b></td>
                        <td style="width:100px;text-align:right;font-weight:bold;">' . $obj->formatNumber($rs[0]['taxvalue']) . '</td>
                    </tr>
                    <tr>
                        <td style="width:100px"><b>PPH ' . $obj->formatNumber($rs[0]['tax23percentage']) . ' %</b></td>
                        <td style="width:40px;"><b>( Rp )</b></td>
                        <td style="width:100px;text-align:right;font-weight:bold;">' . $obj->formatNumber($rs[0]['tax23value']) . '</td>
                    </tr>
                    <tr>
                        <td style="width:100px"><b>Materai</b></td>
                        <td style="width:40px;"><b>( Rp )</b></td>
                        <td style="width:100px;text-align:right;font-weight:bold;">' . $obj->formatNumber($rs[0]['stampfee']) . '</td>
                    </tr> 
                    <tr>
                        <td style="width:100px;border-top:1px solid black;"><b>Total Invoice</b></td>
                        <td style="width:40px;border-top:1px solid black;"><b>( Rp )</b></td>
                        <td style="width:100px;border-top:1px solid black;text-align:right;font-weight:bold;">' . $obj->formatNumber($totalInvoice) . '</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    ';

    $html .= '
        <br>
        <br>
        <table cellpadding="3">
            <tr>
                <td style="width:60px"><b>Terbilang</b></td>
                <td style="width:10px">:</td>
                <td style="width:600px">' . ucwords($obj->sayNumber($totalInvoice)) . ' Rupiah</td>
            </tr>
        </table>
        <br>
        <table cellpadding="2">
            <tr>
                <br>
			    <td style="width: 450px">
                    <table cellpadding="2" style="font-weight:bold; border:1px solid #000">
                        <tr>
                            <td style="width:300px">Pembayaran ditujukan ke :</td>
                        </tr>
                        <tr>
                            <td style="width:40px">A/C</td>
                            <td style="width:10px">:</td>
                            <td>' . $rsPaymentMethod[0]['bankaccountname'] . '</td>
                        </tr>
                        <tr>
                            <td style="width:40px">A/N</td>
                            <td style="width:10px">:</td>
                            <td>' . $rsPaymentMethod[0]['bankaccountnumber'] . '</td>
                        </tr>
                        <tr>
                            <td style="width:280px">' . $rsPaymentMethod[0]['branch'] . '</td>
                        </tr>
                    </table>
                </td>
                <td style="width: 200px">
                    <table cellpadding="2" style="width:200px;text-align:center">
                    <tr><td>Jakarta, ' . $obj->formatDBDate(date('Y-m-d'), 'd-M-Y') . '</td></tr>
                    <tr><td>Hormat Kami,</td></tr>
                    <tr><td></td></tr>
                    <tr><td></td></tr>
                    <tr><td></td></tr>
                    <tr><td></td></tr>
                    <tr><td></td></tr>
                    <tr><td></td></tr>
                    <tr><td>Remmy Jahja</td></tr>
                    </table>
                </td>
            </tr>
        </table>
    ';

    return $html;
}


    ?>
