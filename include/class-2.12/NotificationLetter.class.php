<?php

class NotificationLetter extends BaseClass{

    function __construct()
    {

        parent::__construct();

        $this->tableName = 'notification_letter_header';
        $this->tableNameDetail = 'notification_letter_detail'; 
        $this->tableCustomer = 'customer';
        $this->tableWarehouse = 'warehouse';
        $this->tableAR = 'ar';
        $this->tableStatus = 'transaction_status';

        $this->isTransaction = true;
        $this->securityObject = 'NotificationLetter';

        $this->arrDataDetail = array();
        $this->arrDataDetail['pkey'] = array('hidDetailKey');
        $this->arrDataDetail['refkey'] = array('pkey', 'ref');
        $this->arrDataDetail['detail'] = array('txtDetailAttachment', 'raw');

        $arrDetails = array();
        array_push($arrDetails, array('dataset' => $this->arrDataDetail));

        $this->arrData = array();
        $this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails));
        $this->arrData['code'] = array('code');
        $this->arrData['trdate'] = array('trDate','date');
        $this->arrData['warehousekey'] = array('selWarehouseKey');
        $this->arrData['customerkey'] = array('hidCustomerKey');
        $this->arrData['detail'] = array('txtDetail', 'raw');
//        $this->arrData['startperiod'] = array('startPeriod', 'date');
//        $this->arrData['endperiod'] = array('endPeriod', 'date');
        $this->arrData['amount'] = array('grandTotal', 'number');
        $this->arrData['statuskey'] = array('selStatus');

        $this->arrDataListAvailableColumn = array();
        array_push($this->arrDataListAvailableColumn, array('code' => 'code', 'title' => 'code', 'dbfield' => 'code', 'default' => true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'date', 'title' => 'date', 'dbfield' => 'trdate', 'default' => true, 'width' => 80, 'align' => 'center', 'format' => 'date'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'warehouse', 'title' => 'warehouse', 'dbfield' => 'warehousename', 'default' => true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'customer', 'title' => 'customer', 'dbfield' => 'customername', 'default' => true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'amount', 'title' => 'amount', 'dbfield' => 'amount', 'default' => true, 'width' => 120, 'format' => 'number', 'align' => 'right'));

        $this->arrSearchColumn = array();
        array_push($this->arrSearchColumn, array('Kode', $this->tableName . '.code'));
        array_push($this->arrSearchColumn, array('Tanggal', $this->tableName . '.trdate'));
        array_push($this->arrSearchColumn, array('Gudang', $this->tableWarehouse . '.name'));
        array_push($this->arrSearchColumn, array('Warga', $this->tableCustomer . '.name'));
        array_push($this->arrSearchColumn, array('Status', $this->tableStatus . '.status'));

        $this->newLoad = true;
        $this->printMenu = array();
        array_push($this->printMenu, array('code' => 'printTransaction', 'name' => $this->lang['printTransaction'], 'icon' => 'print', 'url' => 'print/overdueOutstandingLetter'));

        $this->includeClassDependencies(array(
            'AR.class.php'
        ));

    }

    function getQuery()
    {

        $sql = '
            select
                '. $this->tableName .'.*,
                '. $this->tableCustomer .'.name as customername,
                '. $this->tableWarehouse .'.name as warehousename,
                '. $this->tableStatus .'.status as statusname
            from
                '. $this->tableName .'
                left join '. $this->tableCustomer .' on '. $this->tableName .'.customerkey = '. $this->tableCustomer .'.pkey,
                '. $this->tableStatus .',
                '. $this->tableWarehouse .'
            where
                '. $this->tableName .'.warehousekey = '. $this->tableWarehouse .'.pkey and
                '. $this->tableName .'.statuskey = '. $this->tableStatus .'.pkey 
 	    ' . $this->criteria;

        $sql .= $this->getCompanyCriteria();
        $sql .= $this->getWarehouseCriteria();

        return $sql;

    }

    function  getDetailWithRelatedInformation($pkey,$criteria='')
    {

        $sql = 'select
                    '.$this->tableNameDetail .'.*,
                    '. $this->tableAR .'.code as arcode,
                    '. $this->tableAR .'.trdate as ardate
                from
                    '.$this->tableNameDetail .'
                        left join '. $this->tableAR .' on '. $this->tableNameDetail .'.arkey = '. $this->tableAR .'.pkey 

                where
                    '.$this->tableNameDetail .'.refkey in ('.$this->oDbCon->paramString($pkey,',') . ') ';

        $sql .= $criteria;

        return $this->oDbCon->doQuery($sql);

    }

    function validateForm($arr, $pkey = '')
    {
        $arrayToJs = parent::validateForm($arr,$pkey);

        $customerkey = $arr['hidCustomerKey'];

        if(empty($customerkey)) {
            $this->addErrorList($arrayToJs, false, $this->errorMsg['customer'][1]);
        }

        
        // gk perlu validasi karena bisa general nanti
        
        
//        // $arrDetail = $arr['hidDetailKey'];
//        $arrARKey = $arr['hidARKey'];
//        $arrARCode = $arr['arCode'];
//        $arrAmount = $arr['amount'];
//
//        if(empty($arrARKey[0])) {
//            $this->addErrorList($arrayToJs, false, $this->errorMsg[501]);
//        } else {
//
//            $errMsg = array();
//
//            $arrKey = array();
//            for($i=0; $i<count($arrARKey); $i++) {
//
//                if (in_array($arrARKey[$i], $arrKey)) {
//                    array_push($errMsg,  ' <strong>'. $arrARCode[$i] .'. </strong> ' . $this->errorMsg[215]);
//                }
//
//                $arrKey[] = $arrARKey[$i];
//
//
//                $amount = $this->unFormatNumber($arrAmount[$i]);
//
//                if($amount <= 0) {
//                    $this->addErrorList($arrayToJs, false, ' <strong>' . $arrARCode[$i] . '. </strong> ' . $this->errorMsg[503]);
//                }
//
//            }
//
//            if(!empty($errMsg)) {
//                $this->addErrorList($arrayToJs, false, implode('<br>',$errMsg));
//            }
//
//        }


        return $arrayToJs;
    }

    
    function validateConfirm($rsHeader)
    {

        
    }

    function confirmTrans($rsHeader)
    {

    }


    function validateCancel($rsHeader, $autoChangeStatus = false)
    {
        $id = $rsHeader[0]['pkey'];

    }

    function cancelTrans($rsHeader,$copy)
    {  
        
        $id = $rsHeader[0]['pkey'];
		
		if ($copy)
			$this->copyDataOnCancel($id);	
        
	}

//    function getStartAndEndPeriod($id) 
//    {
//
//        $data = [
//            'startPeriod' => '0000-00-00',
//            'endPeriod' => '0000-00-00',
//        ]; //default 0000-00-00
//
//        $rsDetail = $this->getDetailWithRelatedInformation($id);
//    
//        if(empty($rsDetail)) 
//            return $data;
//
//        $dateField = 'ardate';
//
//        $arrDate = array_column($rsDetail, $dateField);
//        $arrDate = array_filter($arrDate, 'strtotime');
//
//        if(empty($arrDate)) 
//            return $data;
//
//        sort($arrDate);//shorting date
//
//        $data = array();
//        $data['startPeriod'] = reset($arrDate);
//        $data['endPeriod'] = end($arrDate) ?: '0000-00-00';
//
//        return $data;
//    }

//    function updatePeriodDate($id)
//    {
//
//        $rsPeriod = $this->getStartAndEndPeriod($id);
//        $letter = $this->generateLetter($rsPeriod['startPeriod'], $rsPeriod['endPeriod']);
//
//        if (empty($rsPeriod))
//            return;
//
//        $sql = '
//            UPDATE
//                '. $this->tableName .'
//            SET
//                letter = '.$this->oDbCon->paramString($letter) .',
//                startperiod = '. $this->oDbCon->paramString($rsPeriod['startPeriod']) .',
//                endperiod = '. $this->oDbCon->paramString($rsPeriod['endPeriod']) .'
//            WHERE
//                pkey = '. $this->oDbCon->paramString($id) .'
//        ';
//
//        $this->oDbCon->execute($sql);
//        
//    }


    function generateOverdueOutstandingLetter($arrData=array()) 
    {

        $period ='XXX';
        $amount = $arrData['amount'];
        $currdate = $arrData['trdate'];
        $duedate = $arrData['duedate'];
        $toName = $arrData['toName'];
        $ARDetail =  $arrData['ARDetail'];

        $content = array();
        array_push($content, 'Bersama surat ini kami sebagai Pengurus RT 014 memberitahukan kepada Bapak/Ibu, bahwa dalam data kami Bapak/Ibu memiliki tunggakan IPKL sebesar <b>Rp. '.$this->formatNumber($amount).'</b> (rincian terlampir). Untuk itu kami mohon agar Bapak/Ibu dapat menyelesaikan tunggakan IPKL tersebut sebelum tanggal <b>'.$duedate.'</b>.<br><br>Pembayaran IPKL setiap bulan dapat dilakukan melalui <b>TRANSFER KE BANK DKI</b> no. rekening <b>12528100010</b> atas nama <b>RT 014 14 3175061004</b>.
                   <br><br>Bagi warga yang memiliki tunggakan akan menerima sanksi sebagai berikut :
                    <ol><li>Unit Bapak/Ibu akan ditempel stiker dan papan <b>"UNIT BELUM MEMBAYAR IPKL".</b></li>
                        <li>Sampah unit Bapak/Ibu tidak akan di angkut.</li>
                        <li>Keamanan dan kebersihan unit Bapak/Ibu tidak akan di perhatikan.</li>
                        <li>Data warga yang memiliki tunggakan akan diumumkan ke warga.</li></ol><br>Kami mengingatkan bahwa partisipasi Bapak/Ibu dalam membayar Iuran Pemeliharaan Lingkungan adalah <b>WAJIB</b> dan sangat penting untuk menjaga kebersihan, keamanan, penerangan dan keamanan di lingkungan tempat kita tinggal.<br><br>Apabila Bapak/Ibu telah membayar tunggakan IPKL sebelum surat pemberitahuan ini di terima, mohon abaikan pemberitahuan ini serta mengirim bukti pembayaran ke Pengurus RT 014.<br><br>Demikian surat ini kami sampaikan, atas perhatiannya kami ucapkan terima kasih.
                ');
        
        // outstanding 
        $tableDetail = '<style>.cell-border{border-right:1px solid #000000; border-top:1px solid #000000;}</style>';
        $tableDetail .= '<br><div style="font-size: 1.5em; font-weight: bold; text-align:center">RINCIAN TUNGGAKAN</div><br><br>';
        $tableDetail .= '<table cellpadding="4">';
        $tableDetail .= '<tr>';  
        $tableDetail .= '<td style="width:50px;"></td><td style="width:200px; font-weight: bold; border-left:1px solid #000;" class="cell-border" >No. Tagihan</td><td style="width:120px;  font-weight: bold; text-align:center"  class="cell-border">Periode</td><td style="width:260px; font-weight: bold; text-align:right"  class="cell-border">Total</td><td style="width:50px;"></td>';  
        $tableDetail .= '</tr>';  
        for($i=0;$i<count($ARDetail['invoiceCode']);$i++){ 
            $tableDetail .= '<tr>';  
            $tableDetail .= '<td></td><td class="cell-border" style=" border-left:1px solid #000;">'.$ARDetail['invoiceCode'][$i].'</td><td style="text-align:center"  class="cell-border">'.$this->toLocalDate($this->formatDBDate($ARDetail['invoiceDate'][$i],'F Y')).'</td><td style="text-align:right"  class="cell-border">'.$this->formatNumber($ARDetail['amount'][$i]).'</td><td></td>';  
            $tableDetail .= '</tr>';   
        }
        
        $tableDetail .= '<tr>';  
        $tableDetail .= '<td></td><td colspan="2" style="font-weight: bold; text-align:right ;  border-left:1px solid #000; border-bottom:1px solid #000;"  class="cell-border">TOTAL</td><td style="font-weight: bold; text-align:right;border-bottom:1px solid #000;"  class="cell-border">'.$this->formatNumber($amount).'</td><td></td>';  
        $tableDetail .= '</tr>';  
        $tableDetail .= '</table>';
        
        array_push($content, $tableDetail);
        return $content;

    }

    function afterStatusChanged($rsHeader)
    {
//        $rsHeader = $this->getDataRowById($rsHeader[0]['pkey']);
//
//        if(($rsHeader[0]['statuskey'] <> TRANSACTION_STATUS['batal']) || ($rsHeader[0]['statuskey'] <> TRANSACTION_STATUS['selesai'])) {
//            $this->updatePeriodDate($rsHeader[0]['pkey']);
//        }

    }

    function afterAddDataOnCopy($pkey, $oldkey)
    {
//        $this->updatePeriodDate($pkey);
    }

    function afterUpdateData($arrParam, $action)
    {
        $pkey = $arrParam['pkey']; 
//        $this->updatePeriodDate($pkey);
    }

   

    function normalizeParameter($arrParam, $trim = false)
    {
        $arrParam = parent::normalizeParameter($arrParam);
 
        return $arrParam;
    }

}

?>