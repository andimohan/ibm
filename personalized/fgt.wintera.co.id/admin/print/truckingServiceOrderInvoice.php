<?php  
  $companyName = $class->loadSetting('companyName');
  $companyAddress = $class->loadSetting('companyAddress');
  $pdf->setCustomSettings(
    array(  
         'showPrintHeader' => false,
         'marginFooter' => 60,
         'footer' => '<table  cellpadding="2" style="width: 6700px; text-align:left; border-left:1px solid black;border-top:1px solid black;border-right:1px solid black;border-bottom:1px solid black;">
                <tr>
                    <td style="width:125px; font-weight:bold">BANK</td>
                    <td style="width:5px;"></td>
                    <td style="width:520px;text-align:left;">BANK CENTRAL ASIA (KCU SCBD)</td>
                </tr>
                <tr>
                    <td style=" font-weight:bold">BANK ADDRESS</td>
                    <td></td>
                    <td>Equity Tower, Ground Floor, Unit D &amp; Lantai 8, Unit E,<br>Jl. Jenderal Sudirman Floor 9, RT.5/RW.3, Senayan, Kec. Kby. Baru,<br>Kota Jakarta Selatan, Jakarta 12190</td>
                </tr>
                <tr>
                    <td style=" font-weight:bold">ACCOUNT NUMBER</td>
                    <td></td>
                    <td >0062883012</td>
                </tr>
                <tr>
                    <td style=" font-weight:bold">BENEFICIARY NAME</td>
                    <td></td>
                    <td >PT. FAJAR GEMILANG TRANSPORT</td>
                </tr>
                <tr>
                    <td style=" font-weight:bold">SWIFT CODE</td>
                    <td></td>
                    <td >CENAIDJA</td>
                </tr>    
                    
        <div style="clear:both;"></div>
        <table><tr><td style="width:664px;color:red;font-size:9px;text-align:left;">Subject to objection in writing within 7 days from the date of invoice issuance hereby agreed that interest will be changed at 2% per montnth on payment overdue</td></tr></table>
        </table>
            ',
         ) 
  );  
  


  $generateReportContent = function ($dataset){  

    global $pdf;

    $obj = new TruckingServiceOrderInvoice();   
    $truckingServiceOrder = new TruckingServiceOrder();    
    $truckingServiceOrderCategory = new TruckingServiceOrderCategory();    
    $truckingServiceWorkOrder = new TruckingServiceWorkOrder();
    $customer = new Customer(); 
    $customCode = new CustomCode();
    $setting = new Setting();
    $paymentMethod = new PaymentMethod();
    $termOfPayment = new TermOfPayment();
    $item = new Item(); 
    $customCode = new CustomCode();
    $currency = new Currency();
    $itemUnit = new ItemUnit();
    $employee = new Employee();

    $rs = $dataset['rs']; 

    $rsEmployee = $employee->getDataRowById(base64_decode($_SESSION[$employee->loginAdminSession]['id']));
    $rsInvoiceType = $customCode->searchData($customCode->tableName.'.pkey',$rs[0]['customcodekey'], true);
    
    $rsDetailPayment = $obj->getDataRowById($rs[0]['pkey']);
    $rsCustomer = $customer->getDataRowById($rs[0]['customerkey']);
    $rsTOP = $termOfPayment->getDataRowById($rs[0]['termofpaymentkey']);
    $rsPaymentMethod = $paymentMethod->getDataRowById($rs[0]['companybankkey']);

    //INVOICE DETAIL
    $rsDetail = $obj->getDetailById($rs[0]['pkey']);
    $arrSOKey = array_column($rsDetail, 'salesorderkey');

    //JO HEADER
    $rsSO = $truckingServiceOrder->searchData('','',true, ' and ' . $truckingServiceOrder->tableName.'.pkey in ('.$obj->oDbCon->paramString($arrSOKey,',').')');

    //JO DETAIL
    $rsSODetail = $truckingServiceOrder->getDetailWithRelatedInformation($arrSOKey);

    //INVOICE ITEM DETAIL
    $rsItemDetail = $obj->getItemDetail($rs[0]['pkey'],'refheaderkey');
    $arrItemKey = array_column($rsItemDetail,'itemkey');

    $rsItem = $item->searchDataRow(array(
        $item->tableName.'.pkey',
        $item->tableName.'.code',
        $item->tableName.'.reimburse'
    ), ' and ' . $item->tableName.'.pkey in ('.$obj->oDbCon->paramString($arrItemKey,',').') and '.$item->tableName.'.itemtype = 2 and '.$item->tableName.'.servicecost = 1');
    $rsItemCol = array_column($rsItem,null,'pkey'); 
    
    $arrShipper = array_unique(array_filter(array_column($rsSO, 'shippername')));
    $shipperName = implode(', ', $arrShipper);



    $arrRef = array_filter(array_column($rsSO, 'poreference'));
    $refNumber = implode(', ', $arrRef);

    $arrVessel = array_column($rsSO, 'vesselnumber');
    $vessel = implode(', ', $arrVessel);

    $arrDescriptionOfGoods = array_column($rsSO, 'goodsdescription');
    $descriptionOfGoods = implode(', ', $arrDescriptionOfGoods);

    $routes = [];
    foreach ($rsSO as $row) {
        if (!empty(trim($row['routefrom'])) && !empty(trim($row['routeto']))) {
            $routes[] = $row['routefrom'] . ' / ' . $row['routeto'];
        }
    }
    $routeText = implode(', ', array_unique($routes));

    $arrMbl = array_unique(array_filter(array_column($rsSO, 'mbl')));
    $mbl = implode(', ', $arrMbl);

    $arrMeas = array();
    for($i=0; $i<count($rsSODetail); $i++) {
        $itemkey = $rsSODetail[$i]['itemkey'];
        $itemName = $rsSODetail[$i]['itemname'];
        $qty = $rsSODetail[$i]['qtyinbaseunit'];

        if(!isset($arrMeas[$itemkey])) {
            $arrMeas[$itemkey] = [
                'itemkey' => $itemkey,
                'itemname' => $itemName,
                'totalqty' => 0,
            ];
        }

        $arrMeas[$itemkey]['totalqty'] += $qty;

    }

    //MEASUREMENT
    $measurement = array();
    foreach($arrMeas as $row) {
        $qtyMeas = $obj->formatNumber($row['totalqty']) .' X ' . $row['itemname'];
        array_push($measurement, $qtyMeas);
    }

    $meas = (!empty($measurement) ? implode(', ', $measurement) : '');

    //UNIT
    $rsUnit = $itemUnit->searchData('','',true);
    $rsUnitCol = array_column($rsUnit,null,'pkey');    

    //SPK
    $rsWO = $truckingServiceWorkOrder->searchData('','',true, ' and ' . $truckingServiceWorkOrder->tableName.'.statuskey in (2,3) and '.$truckingServiceWorkOrder->tableName.'.refkey in ('.$obj->oDbCon->paramString($arrSOKey,',').') ');

    $rsContainer = $truckingServiceOrder->getContainerDetail($arrSOKey);
    $containerNumber = implode(', ', array_column($rsContainer, 'container'));

    $QTY = 0;
    $GW = 0;
    $NW = 0;
    for($i=0; $i<count($rsWO); $i++){
        $QTY += $rsWO[$i]['cargoqty'];
        $GW += $rsWO[$i]['cargoweight'];
        $NW += $rsWO[$i]['netweight']; 
    }

    $qtyUnit = $rsUnitCol[$rsWO[0]['cargoqtyunit']]['name'];
    $gwUnit = $rsUnitCol[$rsWO[0]['cargoweightunit']]['name'];
    $nwUnit = $rsUnitCol[$rsWO[0]['netweightunit']]['name'];

    $QTY_GW_NW = (($QTY > 0 ? $obj->formatNumber($QTY,2) . ' ' . $qtyUnit : 0)) . ' / ' . (($GW > 0 ? $obj->formatNumber($GW,2) . ' ' . $gwUnit : 0)) . ' / ' . (($NW > 0 ? $obj->formatNumber($NW,2) . ' ' . $nwUnit : 0));
    
    $etd = $obj->formatDBDate($rsWO[0]['stuffingdatetime'], 'M d, Y');

    $duedate = date('d M Y', strtotime('+'.$rsTOP[0]['duedays'].' days', strtotime($rs[0]['trdate'])));

    $rsCurrency = $currency->getDataRowById($rs[0]['currencykey']);
    $currencyName = strtoupper($rsCurrency[0]['name']);

    $companyName = $obj->loadSetting('companyName');
    $companyAddress = $obj->loadSetting('companyAddress');
    $profileImg = $obj->loadSetting('companyLogo');
        
    //$img = HTTP_HOST.'phpthumb/phpThumb.php?src='.$obj->phpThumbURLSrc.'setting/companyLogo/'.$profileImg.'&w=200&h=50&hash='.getPHPThumbHash($profileImg);
    $img =  HTTP_HOST.'download.php?filename=setting/companyLogo/'.$profileImg;

    $html = $obj->printSetting['defaultStyle'];
        
    $html .= '<table cellpadding="2" width="100%">
    <tr>
        <td style="width:415px;">
            <table cellpadding="3" style=""> 
                <tr>
                    <td style="vertical-align:middle; width:110px" ><img src="'.$img.'"></td>
                    <td style="width: 280px;"></td>
                </tr>
                <tr>
                    <td style="width: 280px;font-weight:bold;">'.$companyName.'</td>
                </tr>
            </table>
        </td> 
        <td style="width:255px; text-align:right">
            <table cellpadding="2" style="width:250px;">  
                <tr><td style="text-align:right;"> </td></tr>   
                <tr> 
                    <td class="lite-color" style="width:250px; text-align:right;"></td>
                </tr>   
            </table></td>
    </tr>  
    </table>
    <div style="border-bottom:1px solid #ccc; clear:both;"></div>
    ';

    $html .='<div style="clear:both;"></div>';

    $html .= '<table width="100%">
    <tr>
        <td style="width:495px;"><table cellpadding="2" style=""> 
                <tr>
                    <td style="vertical-align:middle; width:30px;color: #002985;" >TO</td>
                    <td style="width:10px;">:</td>
                    <td style="width:400px;font-weight:bold;">'.strtoupper($rsCustomer[0]['name']).'</td>
                </tr>
                <tr>
                    <td style="vertical-align:middle; width:30px;color: #002985;"> </td>
                    <td style="width:10px;"> </td>
                    <td style="width:450px;">'.strtoupper($rsCustomer[0]['address']).'</td>
                </tr>
            </table>
        </td> 
        <td style="width:150px;">
            <table cellpadding="2" style="width:170px;">  
                <tr>
                    <td style=""><div style="font-size:2em; font-weight:bold;">INVOICE</div></td>
                </tr>
                <tr>
                    <td style="width:50px;color: #002985;">Number</td>
                    <td style="width:10px;">:</td>
                    <td style="width:100px; font-weight:bold;">'.$rs[0]['code'].'</td>
                </tr>   
                <tr>
                    <td style="width:50px;color: #002985;">Date</td>
                    <td style="width:10px;">:</td>
                    <td style="width:100px;">'.$obj->formatDBDate($rs[0]['trdate'], 'M d, Y').'</td>
                </tr>   
            </table>
        </td>
    </tr>  
    </table>
    ';

    $html .='<div style="clear:both;"></div>';
        
    $html.='<table cellpadding="2" width="100%">
        <tr>
            <td style="width:170px;color: #002985;">NPWP</td>
            <td style="width:10px;text-align:center;">:</td>
            <td style="width:400px;">'.$rsCustomer[0]['taxid'].'</td>
        </tr>
        <tr>
            <td style="width:170px;color: #002985;">Job Order Number</td>
            <td style="width:10px;text-align:center;">:</td>
            <td style="font-weight:bold;width:400px;">'.$rs[0]['salesordercodecache'].'</td>
        </tr>
        <tr>
            <td style="width:170px;color: #002985;">Description of Goods</td>
            <td style="width:10px;text-align:center;">:</td>
            <td style="width:400px;">'.$descriptionOfGoods.'</td>
        </tr>
        <tr>
            <td style="width:170px;color: #002985;">Ocean BL / HBL</td>
            <td style="width:10px;text-align:center;">:</td>
            <td style="width:400px;">'.$mbl.'</td>
        </tr>
        <tr>
            <td style="width:170px;color: #002985;">VESSEL Nr. / ETA</td>
            <td style="width:10px;text-align:center;">:</td>
            <td style="width:400px;">'.$vessel.' / '.$etd.'</td>
        </tr>
        <tr>
            <td style="width:170px;color: #002985;">Origin / Destination</td>
            <td style="width:10px;text-align:center;">:</td>
            <td style="width:400px;">'.$routeText.'</td>
        </tr>
        <tr>
            <td style="width:170px;color: #002985;">Pcs/Gross WT/Net. Weight</td>
            <td style="width:10px;text-align:center;">:</td>
            <td style="width:400px;">'.$QTY_GW_NW.'</td>
        </tr>
        <tr>
            <td style="width:170px;color: #002985;">Measurement / TEUS</td>
            <td style="width:10px;text-align:center;">:</td>
            <td style="width:400px;">'.$meas.'</td>
        </tr>
        <tr>
            <td style="width:170px;color: #002985;">Shipper</td>
            <td style="width:10px;text-align:center;">:</td>
            <td style="width:400px;">'.$shipperName.'</td>
        </tr>
        <tr>
            <td style="width:170px;color: #002985;">Reff. No</td>
            <td style="width:10px;text-align:center;">:</td>
            <td style="width:400px;">'.$refNumber.'</td>
        </tr>
        <tr>
            <td style="width:170px;color: #002985;">Container Number</td>
            <td style="width:10px;text-align:center;">:</td>
            <td style="width:400px;">'.$containerNumber.'</td>
        </tr>
    </table>';

    $html .='<div style="clear:both;"></div>';


    $html.='<table cellpadding="3" width="100%">
        <thead>
            <tr>
                <td style="width:350px;text-align:center;font-weight:bold;border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;font-size:12px;">Description</td>
                <td style="width:80px;text-align:center;font-weight:bold;border-top:1px solid black;border-bottom:1px solid black;font-size:12px;">Curr.</td>
                <td style="width:124px;text-align:right;font-weight:bold;border-top:1px solid black;border-bottom:1px solid black;font-size:12px;">Amount</td>
                <td style="width:100px;text-align:right;font-weight:bold;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;font-size:12px;">Vat</td>
            </tr>
        </thead>
        <tbody>';
    
    for($i=0;$i<count($rsItemDetail); $i++) {
    
    $rsItem = $rsItemCol[$rsItemDetail[$i]['itemkey']];
    
    $itemName = (!empty($rsItemDetail[$i]['aliasname']) ? $rsItemDetail[$i]['aliasname'] : $rsItemDetail[$i]['itemname']);
    
    $vat = ($rsItem['reimburse'] == 0) ? $obj->formatNumber($rs[0]['taxpercentage'],2) : $obj->formatNumber(0,2);
        

    $html .='
        <tr>
            <td style="width:20px;">'.($i+1).'.</td>
            <td style="width:330px;">'.$itemName.'</td>
            <td style="width:80px;text-align:center;">'.$currencyName.'</td>
            <td style="width:120px;text-align:right;">'.$obj->formatNumber($rsItemDetail[$i]['total']).'</td>
            <td style="width:100px;text-align:right;">'.$vat.'</td>
        </tr>
    ';
    }

    $html.='
        </tbody>
    </table>';



    $subTotal = $rs[0]['subtotal'];
    $finalDiscount = 0;
    if ($rs[0]['finaldiscount'] != 0){

        $finalDiscount = $rs[0]['finaldiscount'];
        
        if ($rs[0]['finaldiscounttype'] == 2) {
            $finalDiscount = $rs[0]['finaldiscount']/100 * $rs[0]['subtotal'];
        }
    
        $finalDiscount *= -1;
    }

    $beforeTaxTotal = $rs[0]['beforetaxtotal'];
    $taxValue = $rs[0]['taxvalue'];
    $isPiriceIncludeTax = ($rs[0]['ispriceincludetax'] == 1) ? '<span style="font-weight:bold;">[Include]</span>' : '';
    $stampFee = $rs[0]['stampfee'];
    $downPayment = $rs[0]['totaldownpayment'];
    $grandTotal = $rs[0]['grandtotal'] - $downPayment;
    $tax23Percentage = '('. $obj->formatNumber($rs[0]['tax23percentage'],2) . ' %)';
    $tax23Value = $rs[0]['tax23value'];


    $myX = 10;
    $myY = 190;

    $footerContent = '<table><tr>
                        <td style="width:364px"><table  cellpadding="2" style="text-aling:center;width:150px;">
                            <tr><td>Finance</td></tr>
                            <tr><td style="font-weight:bold;">TTD</td></tr>
                            <tr><td><br><br><br><img src="'.PERSONALIZED_DOC_PATH.'include/img/ttd-rizal.png" style="width:100px"><br></td></tr> 
                            <tr><td style="font-weight:bold;">Rizali Fauzan</td></tr>
                            <tr><td></td></tr>
                        </table></td>
                    <td style="width:300px"><table  cellpadding="2">';

                    $footerContent .='
                        <tr>
                            <td style="width:135px;text-align:left;">Sub Total</td>
                            <td style="width:10px;">:</td>
                            <td style="width:35px;text-align:left">'.$currencyName.'</td>
                            <td style="width:110px;text-align:right;">'.$obj->formatNumber($subTotal).'</td>
                        </tr>';

                    if($finalDiscount !== 0){
                        $myY -= 5;
                        $footerContent .='
                            <tr>
                                <td style="text-align:left;">Discount</td>
                                <td >:</td>
                                <td style="text-align:left">'.$currencyName.'</td>
                                <td style="text-align:right;">('.$obj->formatNumber($finalDiscount).')</td>
                            </tr>';
                    }

                    $footerContent .='
                        <tr>
                            <td style="text-align:left;">Before Tax</td>
                            <td >:</td>
                            <td style="text-align:left">'.$currencyName.'</td>
                            <td style="text-align:right;">'.$obj->formatNumber($beforeTaxTotal).'</td>
                        </tr>';
                        

                    if($downPayment > 0) {
                        $myY -= 5;
                        $footerContent .='
                            <tr>
                                <td style="text-align:left;">Down Payment</td>
                                <td >:</td>
                                <td style="text-align:left">'.$currencyName.'</td>
                                <td style="text-align:right;">('.$obj->formatNumber($downPayment).')</td>
                            </tr>';
                    }

                    if($taxValue > 0){
                        $myY -= 5;
                        $footerContent .='
                            <tr>
                                <td style="text-align:left;">Tax '.$isPiriceIncludeTax.'</td>
                                <td >:</td>
                                <td style="text-align:left">'.$currencyName.'</td>
                                <td style="text-align:right;">'.$obj->formatNumber($taxValue).'</td>
                            </tr>';
                    }

                    if($stampFee > 0){
                        $myY -= 5;
                        $footerContent .='
                            <tr>
                                <td style="text-align:left;">Stamp Fee</td>
                                <td >:</td>
                                <td style="text-align:left">'.$currencyName.'</td>
                                <td style="text-align:right;">'.$obj->formatNumber($stampFee).'</td>
                            </tr>';
                    }


                    $footerContent .='
                        <tr>
                            <td style="text-align:left;font-weight:bold;">Grand Total</td>
                            <td style="width:10px;font-weight:bold;">:</td>
                            <td style="font-weight:bold;text-align:left">'.$currencyName.'</td>
                            <td style="font-weight:bold;text-align:right;">'.$obj->formatNumber($grandTotal).'</td>
                        </tr>';

                    if($tax23Value > 0){
                        $footerContent .='
                            <tr><td></td></tr>
                            <tr>
                                <td style="text-align:left;">Tax 23 '.$tax23Percentage.'</td>
                                <td >:</td>
                                <td style="text-align:left">'.$currencyName.'</td>
                                <td style="text-align:right;">'.$obj->formatNumber($tax23Value).'</td>
                            </tr>';
                    } else {
                        $myY += 10;
                    }


                    $footerContent.='
                    </table></td>
            </tr></table> 
     ';

    $html .= '<br><br>'. $footerContent;
      
    //$pdf->writeHTMLCell(50, '', $myX, $myY, $footerContent, 0, 0, 0, true, 'C', true);

    //reset position
    //$pdf->SetXY(10, 10, true);

    return $html;

  }

  ?>