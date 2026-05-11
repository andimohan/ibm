<?php
class BankReconsiliation extends BaseClass 
{

    function __construct()
    {
        parent::__construct();

        $this->tableName = 'bank_reconsiliation_header';
        $this->tableNameDetail = 'bank_reconsiliation_detail';
        $this->tableCOA = 'chart_of_account';
        $this->tableWarehouse = 'warehouse';
        $this->tableStatus = 'transaction_status';
        $this->tableCurrency = 'currency';
        $this->tableCashBank = 'cash_bank';
        
        $this->securityObject = 'BankReconsiliation';

        $this->isTransaction = true;
        $this->newLoad = true;

        $this->arrDataDetail = array();
        $this->arrDataDetail['pkey'] = array('hidDetailKey');
        $this->arrDataDetail['refkey'] = array('pkey', 'ref');
        $this->arrDataDetail['voucherkey'] = array('hidVoucherKey');  
        $this->arrDataDetail['vouchercode'] = array('VoucherCode');
        $this->arrDataDetail['debit'] = array('debit', 'number');
        $this->arrDataDetail['credit'] = array('credit', 'number');

        $this->arrData = array();
        $this->arrData['pkey'] = array('pkey', array('dataDetail' => array('dataset' => $this->arrDataDetail)));
        $this->arrData['code'] = array('code');
        $this->arrData['trdate'] = array('trDate', 'date');
        $this->arrData['warehousekey'] = array('selWarehouseKey');
        $this->arrData['startdatepriode'] = array('trStartDatePriode', 'date');
        $this->arrData['enddatepriode'] = array('trEndDatePriode', 'date');
        $this->arrData['coakey'] = array('hidCoaKey');
        $this->arrData['currencykey'] = array('selCurrency');
        $this->arrData['beginingbalance'] = array('beginingBalance', 'number');
        $this->arrData['endingbalance'] = array('endingBalance', 'number');
        $this->arrData['totaldebit'] = array('totalDebit', 'number');
        $this->arrData['totalcredit'] = array('totalCredit', 'number');
        $this->arrData['trdesc'] = array('trDesc');
        $this->arrData['statuskey'] = array('selStatus');

        $this->arrDataListAvailableColumn = array();
        array_push($this->arrDataListAvailableColumn, array('code' => 'code', 'title' => 'code', 'dbfield' => 'code', 'default' => true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'trdate', 'title' => 'date', 'dbfield' => 'trdate', 'align' => 'center', 'format' => 'date', 'default' => true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'period', 'title' => 'period', 'dbfield' => 'period', 'align' => 'center', 'default' => true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'warehouse', 'title' => 'warehouse', 'dbfield' => 'warehousename', 'default' => true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'coa', 'title' => 'account', 'dbfield' => 'codename', 'default' => true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'beginingbalance', 'title' => 'startingBalance', 'dbfield' => 'beginingbalance', 'default' => true, 'align' => 'right', 'format' => 'number', 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'endingbalance', 'title' => 'endingBalance', 'dbfield' => 'endingbalance', 'default' => true, 'align' => 'right', 'format' => 'number', 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status', 'title' => 'status', 'dbfield' => 'statusname', 'default' => true, 'width' => 150));


        $this->arrSearchColumn = array();
        array_push($this->arrSearchColumn, array('Kode', $this->tableName . '.code'));
        array_push($this->arrSearchColumn, array('Tanggal', $this->tableName . '.trdate'));
        array_push($this->arrSearchColumn, array('Gudang', $this->tableWarehouse . '.name'));
        array_push($this->arrSearchColumn, array('Akun', $this->tableCOA . '.name')); 
        array_push($this->arrSearchColumn, array('Status', $this->tableStatus . '.status'));

        $this->includeClassDependencies(array(
            'Currency.class.php',
            'CashBank.class.php',
            'ChartOfAccout.class.php',
            'GeneralJournal.class.php' ,
            'Warehouse.class.php' 
        ));

        $this->overwriteConfig();
    }

    function getQuery()
    {

        return '
				select
					' . $this->tableName . '.*,
                   	' . $this->tableStatus . '.status as statusname,
                    ' . $this->tableWarehouse . '.name as warehousename,
                    ' . $this->tableCurrency . '.name as currencyname,
                    ' . $this->tableCOA . '.code as coacode,
                    concat( MONTHNAME(' . $this->tableName . '.startdatepriode) , "  " ,  YEAR(' . $this->tableName . '.startdatepriode)  ) as period,
                    concat('. $this->tableCOA .'.code, " - ", '. $this->tableCOA .'.name ) as codename
				from 
                ' . $this->tableStatus . ', 
                ' . $this->tableName . '
                left join ' . $this->tableCOA . ' on ' . $this->tableName . ' . coakey =' . $this->tableCOA . '.pkey
                left join '. $this->tableWarehouse .' on '. $this->tableName .'. warehousekey ='. $this->tableWarehouse .'.pkey
                left join '. $this->tableCurrency .' on '. $this->tableName .'. currencykey ='. $this->tableCurrency .'.pkey
				where  		
                ' . $this->tableName . '.coakey =' . $this->tableCOA . '.pkey and
                ' . $this->tableName . '.warehousekey =' . $this->tableWarehouse . '.pkey and                
                ' . $this->tableName . '.statuskey = ' . $this->tableStatus . '.pkey
 		' . $this->criteria;

    
    }

    function getDetailWithRelatedInformation($pkey, $criteria = '')
    {
        $sql = 'select
	   			' . $this->tableNameDetail . '.*,
                '. $this->tableCashBank . '.code as vouchercode,
                '. $this->tableCashBank .'.refcode as refcode,
                '. $this->tableCashBank .'.trdate as date,
                '. $this->tableCashBank .'.trdesc as trdesc,
                '. $this->tableCashBank .'.currencykey,
                '. $this->tableCurrency .'.name as currencyname
			  from
			  	' . $this->tableNameDetail . ' 
                left join '. $this->tableCashBank .' on '. $this->tableNameDetail .'.voucherkey =' . $this->tableCashBank . '.pkey
                left join '. $this->tableCurrency .' on '. $this->tableCashBank .'.currencykey =' . $this->tableCurrency . '.pkey
			  where
			  	' . $this->tableNameDetail . '.refkey in (' . $this->oDbCon->paramString($pkey, ',') . ') ';
        $sql .= $criteria;
 
        return $this->oDbCon->doQuery($sql);
    }
    
    function getMonthListIndex($startDate/*, $endDate*/){
            // $startDate == yyyy-mm-dd
            
            $errData = array();
        
            $start    = (new DateTime($startDate))->modify('first day of this month');
            $start    = $start->format('Y-m-01');
          /*  $end      = (new DateTime($endDate))->modify('first day of next month');
            $interval = DateInterval::createFromDateString('1 month');
            $period   = new DatePeriod($start, $interval, $end);

            $arrDate = array();
            foreach ($period as $dt)  
                array_push($arrDate,  $dt->format("Y-m-01") );*/
        
            
            
            $chartOfAccount = new ChartOfAccount();
            $sql = 'select runningmonth,isclosed from '.$chartOfAccount->coaActivePeriod.' where runningmonth in ('.$this->oDbCon->paramString($start, ',') .') ';
            $rs = $this->oDbCon->doQuery($sql);
         
            foreach($rs as $row){
                if($row['isclosed'] == 1)
                  array_push($errData, $row);
            }
        
        return $errData;
    }
    
    function reCountGrandtotal($arrParam){

        $grandtotal = 0;
        $totalDebit = 0;
        $totalCredit = 0;
        $debit = 0;
        $credit = 0;
        
        
        $beginingBalance = $this->unFormatNumber($arrParam['beginingBalance']);
        $arrChkPick = $arrParam['chkPick']; 
        $arrDebit = $arrParam['debit']; 
        $arrCredit = $arrParam['credit']; 

        $arrARDetail = array();

        for ($i=0;$i<count($arrDebit);$i++){

            
            if (empty($arrChkPick[$i])) 
                continue;

            $debit += $this->unFormatNumber($arrDebit[$i]);
            $credit += $this->unFormatNumber($arrCredit[$i]);
        } 

        $totalDebit = $debit; 
        $totalCredit = $credit; 
        
        $totalDebitCredit = $debit + ($credit * -1 );
        $endingBalance = $beginingBalance + $totalDebitCredit;

        $reCountResult = array();
        $reCountResult['totalDebit'] = $totalDebit; 
        $reCountResult['totalCredit'] = $totalCredit; 
        $reCountResult['endingBalance'] = $endingBalance; 

        return $reCountResult;
				
	}
    
     function validateCancel($rsHeader,$autoChangeStatus=false){
      // kalo lebih kecil dr pd bulan berjalan, errror

         // ambil semua periode bulan dan tahun
   
        $rs = $this->getMonthListIndex($rsHeader[0]['startdatepriode']/*, $rsHeader[0]['enddatepriode']*/); 
      
         if(!empty($rs)){
             
             $arrCode = array();
             foreach($rs as $row)  
                 array_push($arrCode, $this->formatDBDate($row['runningmonth'],'F Y'));

             $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg[201].'<br><strong>' . implode(', ',$arrCode) . '</strong>. ' .$this->errorMsg['bankReconsiliation'][5]);     
            
         }
     } 
     
    

    function validateConfirm($rsHeader){
        $cashBank = new CashBank();

        $rsDetails = $this->getDetailWithRelatedInformation($rsHeader[0]['pkey']);

        $arrVoucherKey = array_column($rsDetails,'voucherkey');

        $rsCashBank = $cashBank->searchDataRow(
            array(
                $cashBank->tableName . '.pkey',
                $cashBank->tableName . '.code',
                $cashBank->tableName . '.coakey',
                $cashBank->tableName . '.isreconsile',
                $cashBank->tableName . '.currencykey',
                $cashBank->tableName . '.amount',
                $cashBank->tableName . '.statuskey',
                $cashBank->tableName . '.trdate'
            ), 
            ' and ' . $cashBank->tableName . '.pkey in 
                (' . $this->oDbCon->paramString($arrVoucherKey, ',') . ') 
            '
        );

        // harusnya validasi tgl, dan coakey mungkin terjadi, cukup validasi status kasbank saja
        // karena klo kasbanknya berubah datanya, pasti staatusny sudh jd cancel
        
        $arrCashBankErr = array();
        $arrCashBankErr['isReconsile'] = array(); 
        $arrCashBankErr['statuskey'] = array(); 
        
//        $fromDate =  $this->formatDBDate($rsHeader[0]['startdatepriode'],'d / m / Y');
//        $toDate =  $this->formatDBDate($rsHeader[0]['enddatepriode'],'d / m / Y');
        
         $month = $this->formatDBDate($rsHeader[0]['startdatepriode'],'m');
         $year = $this->formatDBDate($rsHeader[0]['startdatepriode'],'Y');
        
		// boleh beberapa kali rekon dalam 1 periode
//        $isCOACreatedInPeriod = $this->haveCOACreatedInPeriod($rsHeader[0]['pkey'],$rsHeader[0]['coakey'],$month,$year);
//
//         if($isCOACreatedInPeriod)
//               $this->addErrorLog(false, '<strong>' . $rsHeader[0]['code'] .'.</strong> '.$this->errorMsg['bankReconsiliation'][7]);
        
            //validasi opening balance
             $rsLastReconsileBalance = $this->getLastedReconsile($rsHeader[0]['coakey']);
              $lastReconsileBalance = (!empty($rsLastReconsileBalance)) ? $rsLastReconsileBalance[0]['endingbalance'] : 0;  
        
            //cek nilai yang di input sama ga sama nilai akhir balance
            if($rsHeader[0]['beginingbalance'] <> $lastReconsileBalance)
                   $this->addErrorLog(false,'<strong>' . $rsHeader[0]['code'] .'.</strong> '.$this->errorMsg['bankReconsiliation'][6]);
        
        for($v = 0; $v < count($rsCashBank); $v++) {

            if ($rsCashBank[$v]['isreconsile'] == 1)  
                array_push($arrCashBankErr['isReconsile'],$rsCashBank[$v]['code']);

//            if ($rsCashBank[$v]['coakey'] <> $rsHeader[0]['coakey'])  
//                array_push($arrCashBankErr['coakey'],$rsCashBank[$v]['code']);
            
//            if ($rsCashBank[$v]['currencykey'] <> $rsHeader[0]['currencykey'])
//                array_push($arrCashBankErr['currencykey'],$rsCashBank[$v]['code']);

            if(!in_array($rsCashBank[$v]['statuskey'], array(2,3))) 
                array_push($arrCashBankErr['statuskey'],$rsCashBank[$v]['code']);
            
//             $cashBankDate = $this->formatDBDate($rsCashBank[$v]['trdate'],'d / m / Y');
//
//             $dateDiff1 = $this->dateDiff($fromDate,$cashBankDate);  
//             $dateDiff2 = $this->dateDiff($cashBankDate,$toDate);  
//             
//            if ($dateDiff1 < 0 || $dateDiff2 < 0)
//                array_push($arrCashBankErr['date'],$rsCashBank[$v]['code']);
        }
        
        
        
      if (!empty($arrCashBankErr['isReconsile'])) 
                $this->addErrorLog(false, '<strong>' . $rsHeader[0]['code'] .'.</strong> '.  $this->errorMsg[201].'<br><strong>'. implode(', ',$arrCashBankErr['isReconsile']).'</strong>. '. $this->errorMsg['cashBank'][7]);
    
     if (!empty($arrCashBankErr['statuskey'])) 
                $this->addErrorLog(false, '<strong>' . $rsHeader[0]['code'] .'.</strong> '.  $this->errorMsg[201].'<br><strong>'. implode(', ',$arrCashBankErr['statuskey']).'</strong>. '. $this->errorMsg[228]);
    
        
     // periode blm boleh tutup
          $rs = $this->getMonthListIndex($rsHeader[0]['startdatepriode']/*, $rsHeader[0]['enddatepriode']*/); 
      
         if(!empty($rs)){
             
             $arrCode = array();
             foreach($rs as $row)  
                 array_push($arrCode, $this->formatDBDate($row['runningmonth'],'F Y'));

             $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg[201].'<br><strong>' . implode(', ',$arrCode) . '</strong>. ' .$this->errorMsg['bankReconsiliation'][5]);     
            
         }
    }
 

    function afterStatusChanged($rsHeader){   
        // retrieve latest status
        $rsHeader = $this->getDataRowById($rsHeader[0]['pkey']);
        
        if(in_array($rsHeader[0]['statuskey'],array(2,4))){
            
            $rsDetails = $this->getDetailWithRelatedInformation($rsHeader[0]['pkey']);
            $rsHeader = $this->getDataRowById($rsHeader[0]['pkey']);

            $isReconsile = ( $rsHeader[0]['statuskey'] == 2 ) ? 1 : 0;
            $trDate = ( $rsHeader[0]['statuskey'] == 2 ) ? $this->formatDBDate($rsHeader[0]['trdate']) : DEFAULT_EMPTY_DATE;

            $this->changeIsReconsile(array_column($rsDetails,'voucherkey'), $isReconsile,$trDate);
        }
         
    }

    function changeIsReconsile($id, $newIsReconsile, $trDate)
    {
       if (!is_array($id))   $id = array($id);
        
        //ubah value isreconsile di table cash_bank
        $sql = ' UPDATE '. $this->tableCashBank .'
                SET  isreconsile = '. $this->oDbCon->paramString($newIsReconsile) .',
                reconsiledate = '. $this->oDbCon->paramDate($trDate,' / ') .' 
                WHERE pkey in ('. $this->oDbCon->paramString($id,',').' )
        ';

        $this->oDbCon->execute($sql);
    }
    
    
    function haveCOACreatedInPeriod($pkey,$coakey,$month,$year){
        
        $criteria = '';
        if(!empty($pkey))
           $criteria  = ' and '.$this->tableName.'.pkey not in ('.$this->oDbCon->paramString($pkey).')';

        $rs = $this->searchDataRow(
                    array(
                        $this->tableName . '.pkey',
                        $this->tableName . '.trdate',
                        $this->tableName . '.code'
                    ),
                    ' and '.$this->tableName.'.statuskey in (1,2,3)
                      and '.$this->tableName.'.coakey = '.$this->oDbCon->paramString($coakey).'
                      and MONTH('.$this->tableName.'.startdatepriode) = '.$this->oDbCon->paramString($month).'
                      and YEAR('.$this->tableName.'.startdatepriode) = '.$this->oDbCon->paramString($year).'
                    '.$criteria
                );
        
        

        return (!empty($rs)) ? true : false;
        
    }
    
    function getLastedReconsile($coakey){

        //seharusnya cari berdasarkan coakey
        //ambil yang status nya sudah di konfirmasi dan selesai karena sudah di rekonsile
        //order by confirmed don dan ambil yang terakhir jam, menit dan detik
        //biar pasti dilimit 1
        
        $sql = 'select endingbalance from '.$this->tableName.' where statuskey in(2,3) and coakey = '.$this->oDbCon->paramString($coakey).' order by confirmedon desc limit 1';
        
        
        $rs = $this->oDbCon->doQuery($sql);
        

        return  $rs;

        
        
        
    }

    function validateForm($arr, $pkey = '')
    {
 
        $arrayToJs = parent::validateForm($arr, $pkey);
  
        $coaName = $arr['coaName'];
        $coaKey = $arr['hidCoaKey'];
        $currencyKey = $arr['selCurrency'];
        $voucherKey = $arr['hidVoucherKey']; 
        $startDatePriode = $arr['trStartDatePriode'];
        $endDatePriode = $arr['trEndDatePriode'];
        $beginingBalance = $arr['beginingBalance'];
        $arrDetailKey = array();

        
        $cashBank = new CashBank();
        $rsCashBank = $cashBank->searchDataRow(
            array(
                $cashBank->tableName . '.pkey',
                $cashBank->tableName . '.code',
                $cashBank->tableName . '.coakey',
                $cashBank->tableName . '.isreconsile',
                $cashBank->tableName . '.currencykey',
                $cashBank->tableName . '.amount',
                $cashBank->tableName . '.statuskey',
                $cashBank->tableName . '.trdate'
            ), 
            ' and ' . $cashBank->tableName . '.pkey in 
                (' . $this->oDbCon->paramString($voucherKey, ',') . ') 
            '
        );
                
        $rsVoucher = (!empty($voucherKey)) ? $cashBank->searchData('','',true, ' and '.$cashBank->tableName.'.pkey in ('.implode(',',$this->oDbCon->paramString($voucherKey)).') ') : array(); 

        //validasi voucher detail tidak boleh kosong
        // boleh kosong, utk init awal atau jika tdk ad transaksi
//        $hasVoucher = false; 
//        for($i=0;$i<count($voucherKey);$i++) { 
//            if (!empty($voucherKey[$i]))  //  && !empty($arrPick[$i])
//                $hasVoucher = true;  
//
//            if (in_array($voucherKey[$i],$arrDetailKey)){   
//                $this->addErrorList($arrayToJs,false, $rsVoucher[$voucherKey[$i]]['code'].'. '.$this->errorMsg[215]); 	 
//            }else{ 
//                array_push($arrDetailKey, $voucherKey[$i]); 
//            }
//
//        }

//        if (!$hasVoucher)
//            $this->addErrorList($arrayToJs,false, $this->errorMsg['cashBank'][1]); 
        
        // validasi tidak boleh ad cashbank yg sama 
        $arrDuplicate = $this->getDuplicatedValue($voucherKey,$rsCashBank,array('code'));  
        if(count($arrDuplicate['key']) > 0 )  
             $this->addErrorList($arrayToJs, false,implode(', ',array_column($arrDuplicate['data'],'code')). '. '. $this->errorMsg[215]);
    
            
        
            
        $arrCashBankErr = array();
        $arrCashBankErr['isReconsile'] = array();
        $arrCashBankErr['coakey'] = array();
        $arrCashBankErr['currencykey'] = array();
        $arrCashBankErr['statuskey'] = array();
        $arrCashBankErr['date'] = array();
        
        
        $fromDate = $arr['trStartDatePriode']; 
        $toDate = $arr['trEndDatePriode'];  

        //format ke bulan dan tahun diambil dari periode        
        $month = str_replace('\'','',$this->oDbCon->paramDate($fromDate,' / ','m'));
         $year = str_replace('\'','',$this->oDbCon->paramDate($fromDate,' / ','Y'));
       
         for($v = 0; $v < count($rsCashBank); $v++) {

            if ($rsCashBank[$v]['isreconsile'] == 1)  
                array_push($arrCashBankErr['isReconsile'],$rsCashBank[$v]['code']);

            if ($rsCashBank[$v]['coakey'] <> $coaKey)  
                array_push($arrCashBankErr['coakey'],$rsCashBank[$v]['code']);
            
            if ($rsCashBank[$v]['currencykey'] <> $currencyKey)
                array_push($arrCashBankErr['currencykey'],$rsCashBank[$v]['code']);

            if(!in_array($rsCashBank[$v]['statuskey'], array(2,3))) 
                array_push($arrCashBankErr['statuskey'],$rsCashBank[$v]['code']);
            
             
             $cashBankDate = $this->formatDBDate($rsCashBank[$v]['trdate'],'d / m / Y');

/*             $dateDiff1 = $this->dateDiff($fromDate,$cashBankDate);  
             $dateDiff2 = $this->dateDiff($cashBankDate,$toDate);  */
             
             $cashBankMonth = $this->formatDBDate($rsCashBank[$v]['trdate'],'m');
             $cashBankYear = $this->formatDBDate($rsCashBank[$v]['trdate'],'Y');
             
             // cek bulan dan tahun nya harus sama periodenya
            if ($cashBankMonth !== $month || $cashBankYear !== $year)
                array_push($arrCashBankErr['date'],$rsCashBank[$v]['code']);

        }
        
        //validasi opening balance
  
              $openingBalance = $this->unFormatNumber($beginingBalance);
              
              //get last reconsile buat cari tau nilai nya 
            $rsLastReconsileBalance = $this->getLastedReconsile($arr['hidCoaKey']);
              $lastReconsileBalance = (!empty($rsLastReconsileBalance)) ? $rsLastReconsileBalance[0]['endingbalance'] : 0;  
            //cek nilai yang di input sama ga sama nilai akhir balance
            if($openingBalance <> $lastReconsileBalance)
                   $this->addErrorList($arrayToJs,false, $this->errorMsg['bankReconsiliation'][6]);

        

         //validasi coa key harus diisi
        //validasi coa haanya boleh rekon dlaam satu periode
        if(!empty($arr['hidCoaKey'])){
			// boleh beberapa kali rekon dalam 1 periode
			
//             $isCOACreatedInPeriod = $this->haveCOACreatedInPeriod($arr['hidId'],$arr['hidCoaKey'],$month,$year);
//
//             if($isCOACreatedInPeriod)
//                   $this->addErrorList($arrayToJs,false, $this->errorMsg['bankReconsiliation'][7]);
        }else{
            
             $this->addErrorList($arrayToJs,false, $this->errorMsg['coa'][1]);

        }
            
          if (!empty($arrCashBankErr['isReconsile'])) 
                    $this->addErrorList($arrayToJs,false,   $this->errorMsg[201].'<br>'. implode(', ',$arrCashBankErr['isReconsile']).'. '. $this->errorMsg['cashBank'][7]);

         if (!empty($arrCashBankErr['coakey'])) 
                    $this->addErrorList($arrayToJs,false,  $this->errorMsg[201].'<br>'. implode(', ',$arrCashBankErr['coakey']).'. '. $this->errorMsg['bankReconsiliation'][3]);

         if (!empty($arrCashBankErr['currencykey'])) 
                   $this->addErrorList($arrayToJs,false,  $this->errorMsg[201].'<br>'. implode(', ',$arrCashBankErr['currencykey']).'. '. $this->errorMsg['bankReconsiliation'][2]);

         if (!empty($arrCashBankErr['statuskey'])) 
                    $this->addErrorList($arrayToJs,false, $this->errorMsg[201].'<br>'. implode(', ',$arrCashBankErr['statuskey']).'. '. $this->errorMsg[228]);
 
         if (!empty($arrCashBankErr['date'])) 
                    $this->addErrorList($arrayToJs,false, $this->errorMsg[201].'<br>'. implode(', ',$arrCashBankErr['date']).'. '.$this->errorMsg['bankReconsiliation'][4]);
 
         
        return $arrayToJs;
    }


    function normalizeParameter($arrParam, $trim = false)
    {
        
		// remove uncheck 
        $this->removeUnCheckRows($arrParam,$this->arrDataDetail);
        
        $arrParam['trStartDatePriode'] = date('01 / m / Y',strtotime($arrParam['trStartDatePriode']));
        
        $reCountResult = $this->reCountGrandtotal($arrParam);  
        $arrParam['totalDebit'] = $reCountResult['totalDebit'];
        $arrParam['totalCredit'] = $reCountResult['totalCredit'];
        $arrParam['endingBalance'] = $reCountResult['endingBalance'];
        
        $arrParam = parent::normalizeParameter($arrParam, true);
      
        return $arrParam;
    } 

}


?>
