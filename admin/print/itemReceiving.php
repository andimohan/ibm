<?php 

$PRINT_SETTINGS =  array(   
         'showPrintHeader' => false,
         );
  

includeClass(array('ItemReceiving.class.php'));

$itemReceiving = createObjAndAddToCol(new ItemReceiving());
$obj = $itemReceiving;

$generateReportContent = function ($dataset){ 

    $obj = new ItemReceiving(); 
    $warehouse = new Warehouse();    
    $setting = new Setting();
        
    $rs = $dataset['rs'];
    $rsDetail = $obj->getDetailWithRelatedInformation($rs[0]['pkey']);

    $companyName = strtoupper($setting->loadSetting('companyName'));
    $companyAddress = $setting->loadSetting('companyAddress');
    
    $html = $obj->printSetting['defaultStyle'];

    $html .= '
        <table cellpadding="2" style="border-bottom: 1px solid black; width: 676px;">
            <tr>
                <td style="width: 340px; vertical-align:top;">
                    <table cellpadding="2">
                        <tr><td><b>'.$companyName.'</b></td></tr>
                        <tr><td><b>'.$companyAddress.'</b></td></tr>
                        <tr><td></td></tr>
                    </table>
                </td>
                <td style="width: 176px;"></td>
                <td style="width: 160px; vertical-align:top; text-align:center;">
                    <table cellpadding="4" >
                        <tr><td></td></tr>
                        <tr>
                            <td style="border:1px solid black"><b>PENERIMAAN</b></td>
                        </tr>
                            <tr><td></td></tr>
                    </table>
                </td>
            </tr>
        </table>


        <table cellpadding="2" style="width: 676px; margin-top:6px;border-bottom: 1px solid black;">
            <tr>
                <td style="width:338px; vertical-align:top;">
                    <table cellpadding="2" style="width:100%;">
                        <tr style="font-weight:bold;">
                            <td style="width: 120px;">No. Penerimaan</td>
                            <td style="width: 20px;">:</td>
                            <td style="width: 190px;"><b>'.$rs[0]['code'].'</b></td>
                        </tr>
                        <tr style="font-weight:bold;">
                            <td>No. Pengajuan</td>
                            <td>:</td>
                            <td><b>'.$rs[0]['submissionnumber'].'</b></td>
                        </tr>
                        <tr style="font-weight:bold;">
                            <td>No. Invoice</td>
                            <td>:</td>
                            <td><b>'.$rs[0]['invoicenumber'].'</b></td>
                        </tr>
                        <tr style="font-weight:bold;">
                            <td>No. BL</td>
                            <td>:</td>
                            <td><b>'.$rs[0]['blnumber'].'</b></td>
                        </tr>
                    </table>
                </td>
                <td style="width:338px; vertical-align:top;">
                    <table cellpadding="2" style="width:100%;">
                        <tr style="font-weight:bold;">
                            <td style="width: 120px;">Tgl. Penerimaan</td>
                            <td style="width: 20px;">:</td>
                            <td style="width: 190px;text-align:right;"><b>'.$obj->formatDBDate($rs[0]['trdate'], 'd - m - y').'</b></td>
                        </tr>
                        <tr style="font-weight:bold;">
                            <td>Tgl. Pengajuan</td>
                            <td>:</td>
                            <td style="text-align:right;"><b>'.$obj->formatDBDate($rs[0]['submissiondate'], 'd - m - y').'</b></td>
                        </tr>
                        <tr style="font-weight:bold;">
                            <td>Tgl. Invoice</td>
                            <td>:</td>
                            <td style="text-align:right;"><b>'.$obj->formatDBDate($rs[0]['invoicedate'], 'd - m - y').'</b></td>
                        </tr>
                        <tr style="font-weight:bold;">
                            <td>Tgl. BL</td>
                            <td>:</td>
                            <td style="text-align:right;"><b>'.$obj->formatDBDate($rs[0]['bldate'], 'd - m - y').'</b></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>';

        $containerNumber = array_unique(array_column($rsDetail,'containernumber'));
        $containerNumber = implode(',', $containerNumber);
        $containerType = array_unique(array_column($rsDetail,'containertype'));
        $containerType = implode(',', $containerType);
        $containerSize = array_unique(array_column($rsDetail,'containersize'));
        $containerSize = implode(',', $containerSize);

        $html .='<table cellpadding="4" style="width: 676px; margin-top:6px;border-bottom: 1px solid black;">
                <tr>
                    <td style="border-left:1px solid black;border-right:1px solid black;border-bottom:1px solid black;">DETAIL PETI KEMAS</td>
                </tr>
                <tr>
                    <td style="border-left:1px solid black;width: 225px;text-align:center;">NO.</td>
                    <td style="border-left:1px solid black;width: 225px;text-align:center;">TIPE</td>
                    <td style="border-left:1px solid black;border-right:1px solid black;width: 226px;text-align:center;">UKURAN</td>
                </tr>
                <tr>
                    <td style="border-left:1px solid black;width: 225px;text-align:center;border-top:1px solid black;">'.$containerNumber.'</td>
                    <td style="border-left:1px solid black;width: 225px;text-align:center;border-top:1px solid black;">'.$containerType.'</td>
                    <td style="border-left:1px solid black;border-right:1px solid black;width: 226px;text-align:center;border-top:1px solid black;">'.$containerSize.'</td>
                </tr>
                <tr>
                    <td colspan="3" style="border-left:1px solid black;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;">DETAIL BARANG</td>
                </tr>
                <tr>
                    <td style="border-left:1px solid black;width: 40px;text-align:center;border-top:1px solid black;">
                    <span style="font-weight:bold;">No.</span>
                    </td>
                    <td style="width: 185px;border-top:1px solid black;">
                        <span style="font-weight:bold;">- HS</span><br>
                        <span style="font-weight:bold;">- Uraian Jenis Barang</span><br>
                        <span style="font-weight:bold;">- Asal Negara</span>
                    </td>
                    <td style="width: 150px;border-top:1px solid black;">
                        <span style="font-weight:bold;">- Kategori Barang</span><br>
                        <span style="font-weight:bold;">- Fasilitas & Nomor Urut</span><br>
                    </td>
                    <td style="width: 190px;border-top:1px solid black;">
                        <span style="font-weight:bold;">- Jumlah & Jenis Satuan</span><br>
                        <span style="font-weight:bold;">- Jumlah & Jenis Kemasan</span><br>
                        <span style="font-weight:bold;">- Kadar</span><br>
                        <span style="font-weight:bold;">- Golongan Alkohol</span><br>
                        <span style="font-weight:bold;">- Mili Liter</span><br>
                        <span style="font-weight:bold;">- Isi Per Carton</span>
                    </td>
                    <td style="border-right:1px solid black;width: 110px;border-top:1px solid black;">
                        <span style="font-weight:bold;">- Nilai</span>
                    </td>
                </tr>';

            for($i=0; $i<count($rsDetail); $i++) {

            $hs = (!empty($rsDetail[$i]['hs'])  ? $rsDetail[$i]['hs'] : '-');
            $label = (!empty($rsDetail[$i]['label'])  ? $rsDetail[$i]['label'] : '-');
            $countryOfOrigin = (!empty($rsDetail[$i]['countryoforiginid'])  ? ''. $rsDetail[$i]['countryoforiginid'] : '-');
            $category = (!empty($rsDetail[$i]['category'])  ? $rsDetail[$i]['category'] : '-');
            $facility = (!empty($rsDetail[$i]['facility'])  ? $rsDetail[$i]['facility'] : '-');
            $orderList = (!empty($rsDetail[$i]['orderlist'])  ? $rsDetail[$i]['orderlist'] : '-');

            $qty = (!empty($rsDetail[$i]['qty'])  ? $obj->formatNumber($rsDetail[$i]['qty'],2) : 0);
            $unit = (!empty($rsDetail[$i]['unitname'])  ? $rsDetail[$i]['unitname'] : '-');

            $qtyPackaging = (!empty($rsDetail[$i]['qtypackage'])  ? $obj->formatNumber($rsDetail[$i]['qtypackage'],2) : 0);
            $packagingUnit = (!empty($rsDetail[$i]['packaging'])  ? $rsDetail[$i]['packaging'] : '-');

            $alcoholContent = (!empty($rsDetail[$i]['alcoholcontent'])  ? $obj->formatNumber($rsDetail[$i]['alcoholcontent'],2) : 0);
            $type = (!empty($rsDetail[$i]['typename'])  ? $rsDetail[$i]['typename'] : '-');
            $mililiter = (!empty($rsDetail[$i]['mililiter'])  ? $obj->formatNumber($rsDetail[$i]['mililiter'],2) : 0);
            $qtyCarton = (!empty($rsDetail[$i]['qtycarton'])  ? $obj->formatNumber($rsDetail[$i]['qtycarton']) : 0);
            $amount = (!empty($rsDetail[$i]['amount'])  ? $obj->formatNumber($rsDetail[$i]['amount'],2) : 0);
            

            $html.='<tr>
                    <td style="border-left:1px solid black;width: 40px;text-align:center;border-top:1px solid black;">
                        <span>'.($i+1).'</span>
                    </td>
                    <td style="width: 185px;border-top:1px solid black;"><span>- '.$hs.'</span><br><span>- '.$label.'</span><br><span>- '.$countryOfOrigin.'</span></td>
                    <td style="width: 150px;border-top:1px solid black;"><span>- '.$category.'</span><br><span>- '.$facility.' - '.$orderList.'</span></td>
                    <td style="width: 190px;border-top:1px solid black;"><span>- '.$qty.' '.$unit.'</span><br><span>- '.$qtyPackaging.' '.$packagingUnit.'</span><br><span>- '.$alcoholContent.'%</span><br><span>- '.$type.'</span><br><span>- '.$mililiter.'</span><br><span>- '.$qtyCarton.'</span>
                    </td>
                    <td style="border-right:1px solid black;width: 110px;border-top:1px solid black;"><span>- CFR : '.$amount.'</span>
                    </td>
                </tr>';
            }

        $html.='</table>';

    return $html;

}

?>