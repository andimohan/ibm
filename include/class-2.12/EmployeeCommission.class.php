<?php 

class EmployeeCommission extends BaseClass {

    function __construct() {
        parent::__construct();

        $this->tableName = 'employee_commission_header';
        $this->tableNameDetail = 'employee_commission_detail';
		$this->securityObject = 'EmployeeCommission'; 
        $this->tableEmployee = 'employee';
        $this->tableWarehouse = 'warehouse';
        $this->tableCustomer = 'customer';
        $this->tableEMKLJobOrderHeader = 'emkl_job_order_header';
        $this->tableStatus = 'transaction_status';
        $this->overrideEmployeeCommissionObject = 'OverrideEmployeeCommission';
        
        $this->isTransaction = true;
        $this->newLoad = true;
        
        $this->arrDataDetail = array();
        $this->arrDataDetail['pkey'] = array('hidDetailKey');
        $this->arrDataDetail['refkey'] = array('pkey', 'ref');
        $this->arrDataDetail['jokey'] = array('hidJobOrderKey');
        $this->arrDataDetail['totalselling']   = array('totalSelling', 'number');
        $this->arrDataDetail['taxvalue']   = array('taxValue', 'number');
        $this->arrDataDetail['totalbuying']   = array('totalBuying', 'number');
        $this->arrDataDetail['purchaserefund']   = array('purchaseRefund', 'number');
        $this->arrDataDetail['creditnote']   = array('creditNote', 'number');
        $this->arrDataDetail['debitnote']   = array('debitNote', 'number');
        $this->arrDataDetail['profit']   = array('profit', 'number');
//        $this->arrDataDetail['commission']   = array('commission', 'number'); // gk bisa karena hitungnya keseluruhan
 
        $arrDetails = array();
        array_push($arrDetails, array('dataset' => $this->arrDataDetail));

        $this->arrData = array();
        $this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails));
        $this->arrData['code'] = array('code');
        $this->arrData['refkey'] = array('hidRefKey');
        $this->arrData['refcode'] = array('refCode');
        $this->arrData['reftabletype'] = array('reftabletype');
        $this->arrData['warehousekey'] = array('selWarehouse');
        $this->arrData['trdate'] = array('trDate', 'date');
        $this->arrData['perioddate'] = array('periodDate', 'date');
        $this->arrData['endperioddate'] = array('endPeriodDate', 'date');
        $this->arrData['employeekey'] = array('hidEmployeeKey');
        $this->arrData['totalprofit'] = array('totalProfit', 'number');
        $this->arrData['totalcommission'] = array('totalCommission', 'number');
        $this->arrData['commissionpercentage']   = array('commissionPercentage', 'number');
        $this->arrData['targetprofit']   = array('targetProfit', 'number');
        $this->arrData['targetmonthperiod']   = array('targetMonthPeriod', 'number');
        $this->arrData['trdesc'] = array('trDesc');
        $this->arrData['statuskey'] = array('selStatus');

        $this->arrSearchColumn = array();
        array_push($this->arrSearchColumn, array('Kode', $this->tableName . '.code'));
        array_push($this->arrSearchColumn, array('Tanggal', $this->tableName . '.trdate'));
        array_push($this->arrSearchColumn, array('Gudang', $this->tableWarehouse . '.name'));
        array_push($this->arrSearchColumn, array('Kode. Ref', $this->tableName . '.refcode'));
        array_push($this->arrSearchColumn, array('Karyawan', $this->tableEmployee . '.name'));
        array_push($this->arrSearchColumn, array('Status', $this->tableStatus . '.status'));

        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 90));
        array_push($this->arrDataListAvailableColumn, array('code' => 'date','title' => 'date','dbfield' => 'trdate','default'=>true, 'width' => 100, 'align' =>'center','format' => 'date'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'warehouse','title' => 'warehouse','dbfield' => 'warehousename','default'=>true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'period','title' => 'period','dbfield' => 'perioddate','default'=>true, 'width' => 100, 'format' => 'monthperiod'));
//        array_push($this->arrDataListAvailableColumn, array('code' => 'refcode','title' => 'refCode','dbfield' => 'refcode','default'=>true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'employee','title' => 'employee','dbfield' => 'employeename','default'=>true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'totalAchievement','title' => 'DPI','dbfield' => 'totalachievement','default'=>true, 'width' => 150, 'align' => 'right', 'format' => 'decimal'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'totalCommission','title' => 'totalCommission','dbfield' => 'totalcommission','default'=>true, 'width' => 150, 'align' => 'right', 'format' => 'decimal'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
        array_push($this->arrDataListAvailableColumn, array('code' => 'desc','title' => 'note','dbfield' => 'trdesc',  'width' => 200));

        $this->printMenu = array();
        array_push($this->printMenu, array('code' => 'printTransaction', 'name' => $this->lang['printTransaction'], 'icon' => 'print', 'url' => 'print/employeeCommission'));

        $this->includeClassDependencies(array(
            'Employee.class.php',
            'AR.class.php',
            'ARPayment.class.php',
            'APEmployeeCommission.class.php',
            'EMKLJobOrder.class.php',
            'EMKLOrderInvoice.class.php'
        )); 

    }

    function getQuery() {

        $sql = '
            select
                '. $this->tableName .'.*,
                '. $this->tableEmployee .'.name as employeename,
                '. $this->tableWarehouse .'.name as warehousename,
                '. $this->tableStatus .'.status as statusname,
                ('. $this->tableName .'.totalprofit-'. $this->tableName .'.targetprofit) as totalachievement
            from
                '. $this->tableName .',
                '. $this->tableEmployee .',
                '. $this->tableWarehouse .',
                '. $this->tableStatus .'
            where
                '. $this->tableName .'.employeekey = '. $this->tableEmployee .'.pkey and
                '. $this->tableName .'.warehousekey = '. $this->tableWarehouse .'.pkey and
                '. $this->tableName .'.statuskey = '. $this->tableStatus .'.pkey
        ' . $this->criteria;

        return $sql;

    }

   function validateForm($arr, $pkey = '')
    {
        $arrayToJs = parent::validateForm($arr,$pkey);


        $employeeKey = $arr['hidEmployeeKey'];
        $arrJOKey = $arr['hidJobOrderKey'];
        $arrJOCode = $arr['jobOrderCode'];

        if(empty($employeeKey)) {
            $this->addErrorList($arrayToJs, false, $this->errorMsg['employee'][1]);
        }

        if(empty($arrJOKey[0])) {
            $this->addErrorList($arrayToJs, false, $this->errorMsg[501]);
        } else {

            $arrDetailKey = array();
            for($i=0; $i<count($arrJOKey); $i++) {
                if (in_array($arrJOKey[$i],$arrDetailKey)){  
				$this->addErrorList($arrayToJs,false, '<strong>'.$arrJOCode[$i].'. </strong>'.$this->errorMsg[215]); 	 
			}else{ 
				if (!empty($arrJOKey[$i])) {  
					array_push($arrDetailKey, $arrJOKey[$i]);
				}
			}
            }

        }

        return $arrayToJs;
    }
    function calculateCommission($arrParam)
    {
        
        $arrSOKey = $arrParam['hidJobOrderKey'];
        $employeekey = $arrParam['hidEmployeeKey'];

        $employee = new Employee();

        $rsEmployee = $employee->getDataRowById($employeekey);
        
        $sql = 'select 
                    '.$this->tableEMKLJobOrderHeader.'.pkey as sokey,
                    '.$this->tableEMKLJobOrderHeader.'.code as socode,
                    '.$this->tableEMKLJobOrderHeader.'.totalselling,
                    '.$this->tableEMKLJobOrderHeader.'.totalbuying,
                    '.$this->tableEMKLJobOrderHeader.'.totalbuyingtax,
                    '.$this->tableEMKLJobOrderHeader.'.totalreimburse,
                    '.$this->tableEMKLJobOrderHeader.'.taxvalue,
                    '.$this->tableEMKLJobOrderHeader.'.totalcreditnote,
                    '.$this->tableEMKLJobOrderHeader.'.totaldebitnote,
                    '.$this->tableEMKLJobOrderHeader.'.totalcommission 
                from 
                    '.$this->tableEMKLJobOrderHeader.'
                where
                    '.$this->tableEMKLJobOrderHeader.'.pkey in ('.$this->oDbCon->paramString($arrSOKey,',').') and
                    '.$this->tableEMKLJobOrderHeader.'.statuskey = 3  and
                    '.$this->tableEMKLJobOrderHeader.'.iscommissionpaid = 0 ';
                    
        $arrJO = $this->oDbCon->doQuery($sql);

        $arrResult = array();
        $totalProfit = 0;
        $arrDetail = array(); 
        foreach($arrJO as $joRow) {
            $tempProfit = ($joRow['totalselling'] - $joRow['taxvalue'] - ($joRow['totalbuying'] + $joRow['totalbuyingtax']) - $joRow['totalcommission'] - $joRow['totalcreditnote'] + $joRow['totaldebitnote']);                            
            $totalProfit += $tempProfit;

            array_push($arrDetail, array(
                'joborderkey' => $joRow['sokey'],
                'totalselling' => $joRow['totalselling'],
                'totalbuying' => $joRow['totalbuying'],
                'totalbuyingtax' => $joRow['totalbuyingtax'],
                'totalreimburse' => $joRow['totalreimburse'],
                'taxvalue' => $joRow['taxvalue'],
                'totalcreditnote' => $joRow['totalcreditnote'],
                'totaldebitnote' => $joRow['totaldebitnote'],
                'totalcommission' => $joRow['totalcommission'],
                'profit' => $tempProfit
            ));
        }

        $profit = $totalProfit - $rsEmployee[0]['targetprofit']; 
        $commissionAmount = $profit * $rsEmployee[0]['commissionpercentage'] / 100;
        if($commissionAmount <=0) $commissionAmount = 0;
        
        $arrDetailCol = $this->reindexDetailCollections($arrDetail, 'joborderkey');


        $arrResult['profitamount'] = $totalProfit;
        $arrResult['commissionamount'] = $commissionAmount;
        $arrResult['detail'] = $arrDetailCol;


        return $arrResult;
    }


    function  getEmployeeCommissionData($arrParam = array(), $searchCriteria = '', $orderCriteria = ''){
       

        // 1. kalo komisi per bulan, gk bisa pake tgl, harus selalu patokan tgl 1 dan tgl 30/31
        //    kalo pertengahan misalnya tgl 15 sampe 15 bulan selanjutnya, harus beda paramter, pake tgl awal dan tgl akhir
        // 2. karena job order byk yg gk ad nama salesnya, patokan sales kita ambil dari master customer
        
        $employee = new Employee();
        $ar = new AR();
        $arPayment = new ARPayment();
        $emklOrderInvoice = new EMKLOrderInvoice(); 
        $apEmployeeCommission = new APEmployeeCommission();
        //$currentDate = '2025-02-01';
        
        $invTableKey = $this->getTableKeyAndObj($emklOrderInvoice->tableName, array('key'))['key'];  
        $employeeCommissionTableKey = $this->getTableKeyAndObj($this->tableName, array('key'))['key']; 

        $employeeKeyCriteria = (isset($arrParam['employeekey']) && !empty($arrParam['employeekey'])) ? ' and '.$employee->tableName.'.pkey in ('.$this->oDbCon->paramString( $arrParam['employeekey'], ',' ).')' : '';
        
        $rsEmployee = $employee->searchDataRow(array(
            $employee->tableName.'.pkey',
            $employee->tableName.'.code',
            $employee->tableName.'.name',
            $employee->tableName.'.warehousekey',
            $employee->tableName.'.statuskey',
            $employee->tableName.'.commissionpercentage',
            $employee->tableName.'.targetprofit',
            $employee->tableName.'.targetmonthperiod'
        ), ' and ' . $employee->tableName.'.issales = 1 and '.  $employee->tableName.'.statuskey = 2 ' . $employeeKeyCriteria);
    
        $rsEmployeeCols = $this->reindexDetailCollections($rsEmployee, 'pkey');
        
        // cari maks bulan terlama
        //        $longestPeriod = 0;
        //        foreach($rsEmployee as $row)
        //            if ($row['targetmonthperiod'] > $longestPeriod)
        //                $longestPeriod = $row['targetmonthperiod'];
        
        // 1. per bulan
        // 2. per periode tanggal
        $periodType = (isset($arrParam['periodType']) && !empty($arrParam['periodType'])) ? $arrParam['periodType'] : 1;
        
        // trDate utk data AP nanti tgl brp pengakuannya
        $trDate = (isset($arrParam['trDate'])) ? $arrParam['trDate'] : $arrParam['periodDate'];
        $startDate = (isset($arrParam['periodDate']) && !empty($arrParam['periodDate'])) ? $arrParam['periodDate'] :  date('t / m / Y 23:59');
        $endDate = (isset($arrParam['endDate']) && !empty($arrParam['endDate'])) 
                        ? $arrParam['endDate'] 
                        :  date('t / m / Y 23:59',strtotime( str_replace('\'','',$this->oDbCon->paramDate($startDate,' / ')  ))); // tetep kepake buat patokan query
        
        //$this->setLog($arrParam,true);

        $startDateDBFormat = str_replace('\'','',$this->oDbCon->paramDate($startDate,' / '));
        $endDateDBFormat = str_replace('\'','',$this->oDbCon->paramDate($endDate,' / '));

        // utk subtract date, kalo pake akhir bulan bisa salah , khusunya kalo ketemu feb
        // jadi akalin balikin ke tgl 1 dulu
        //        $endPeriodFirstDayDBFormat = (isset($arrParam['endDate']) && !empty($arrParam['endDate'])) ? $arrParam['endDate'] :  date('Y-m-1');

        $arrResult = [];
        foreach($rsEmployee as $employeeRow) {

            //ambil dar ar dan arpayment
            // cari semua pembayaran AR dalam periode target, dan yg AR nya sudah LUNAS
            // ambil informasi invocienya, cek ulang invoicenya sudah lunas semua atau blm

            // cari pembayarn yagn terjadi dalam periode target,
            // ambil dari maks batas target dari semua karyawan, misalnya 5 bln
            // nanti perhitungan bulannya baru di bagian bawah

            $targetMonthPeriod = $employeeRow['targetmonthperiod'];
            if($targetMonthPeriod < 1) $targetMonthPeriod = 1; 
            
            if($periodType == 1){  
                //$date = new DateTime($endDateDBFormat);  

                $date = new DateTime($startDateDBFormat);  
                $date->modify('first day of this month'); 
                $date->sub(new DateInterval('P'.($targetMonthPeriod-1).'M'));   
                $startDate = $date->format('1 / m / Y');  
            }else{
                $startDate = (isset($arrParam['periodDate']) && !empty($arrParam['periodDate'])) ? $arrParam['periodDate'] :  date('d / m / Y');
            }
                
            // ambil pkey sales order dulu, gk bisa di join langsung karena bisa double (1 JO beberapa invoice)
                 
            // patokan criteria tgl
            // thewhale dr tgl pelunasan
            // TEL dan yg lainnnya, dari tgl JO
            
            $dateForCommissionCalculation = $this->loadSetting('dateForCommissionCalculation');
     
            //if ($dateForCommissionCalculation == 1) 
            //    $dateField = $arPayment->tableName ;
            //else
            //    $dateField = $this->tableEMKLJobOrderHeader;
            
            if($dateForCommissionCalculation == 1){
                $sql = '
                    select
                        '. $emklOrderInvoice->tableNameDetail .'.refsalesorderheaderkey as sokey 
                    from
                        '. $arPayment->tableName .',
                        '. $arPayment->tableNameDetail .',
                        '. $ar->tableName  .',
                        '. $emklOrderInvoice->tableName .',
                        '. $emklOrderInvoice->tableNameDetail .', 
                        '. $this->tableCustomer .' 
                    where
                        '. $arPayment->tableName .'.pkey = '. $arPayment->tableNameDetail .'.refkey and 
                        '. $arPayment->tableNameDetail .'.arkey = '.$ar->tableName.'.pkey and 
                        '. $ar->tableName .'.reftabletype = '.$this->oDbCon->paramString($invTableKey).' and
                        '. $ar->tableName .'.refkey = '.$emklOrderInvoice->tableName.'.pkey and 
                        '. $emklOrderInvoice->tableName .'.pkey = '.$emklOrderInvoice->tableNameDetail.'.refkey and 
                        '. $emklOrderInvoice->tableName .'.customerkey = '.$this->tableCustomer.'.pkey and 
                        '. $this->tableCustomer .'.saleskey = '.$this->oDbCon->paramString($employeeRow['pkey']).' and 
                        '. $arPayment->tableName .'.statuskey in (2,3) and
                        '. $ar->tableName .'.statuskey = 3 and
                        date('. $arPayment->tableName .'.trdate) between date('.$this->oDbCon->paramDate($startDate).') and date('.$this->oDbCon->paramDate($endDate).') 
                ';

            }else{
                  $sql = '
                    select
                        '. $emklOrderInvoice->tableNameDetail .'.refsalesorderheaderkey as sokey 
                    from
                        '. $arPayment->tableName .',
                        '. $arPayment->tableNameDetail .',
                        '. $ar->tableName  .',
                        '. $emklOrderInvoice->tableName .',
                        '. $emklOrderInvoice->tableNameDetail .', 
                        '. $this->tableCustomer .',
                        '. $this->tableEMKLJobOrderHeader.' 
                    where
                        '. $arPayment->tableName .'.pkey = '. $arPayment->tableNameDetail .'.refkey and 
                        '. $arPayment->tableNameDetail .'.arkey = '.$ar->tableName.'.pkey and 
                        '. $ar->tableName .'.reftabletype = '.$this->oDbCon->paramString($invTableKey).' and
                        '. $ar->tableName .'.refkey = '.$emklOrderInvoice->tableName.'.pkey and 
                        '. $emklOrderInvoice->tableName .'.pkey = '.$emklOrderInvoice->tableNameDetail.'.refkey and 
                        '. $emklOrderInvoice->tableName .'.customerkey = '.$this->tableCustomer.'.pkey and 
                        '. $this->tableCustomer .'.saleskey = '.$this->oDbCon->paramString($employeeRow['pkey']).' and 
                        '. $arPayment->tableName .'.statuskey in (2,3) and
                        '. $ar->tableName .'.statuskey = 3 and
                        '.$emklOrderInvoice->tableNameDetail.'.refsalesorderheaderkey = '.$this->tableEMKLJobOrderHeader.'.pkey and 
                        date('. $this->tableEMKLJobOrderHeader .'.trdate) between date('.$this->oDbCon->paramDate($startDate).') and date('.$this->oDbCon->paramDate($endDate).') 
                ';

            }
            
            
                $rsData = $this->oDbCon->doQuery($sql); 
    
                if (empty($rsData)) continue;
                
                $arrSOKey = array_unique(array_column($rsData,'sokey'));
            
                // ambil informasi JO, harusnya gk perlu group by sales lg karena diatas sudah difilter
                $sql = 'select 
                            '.$this->tableEMKLJobOrderHeader.'.pkey as sokey,
                            '.$this->tableEMKLJobOrderHeader.'.code as socode,
                            '.$this->tableEMKLJobOrderHeader.'.totalselling,
                            '.$this->tableEMKLJobOrderHeader.'.totalbuying,
                            '.$this->tableEMKLJobOrderHeader.'.totalbuyingtax,
                            '.$this->tableEMKLJobOrderHeader.'.totalreimburse,
                            '.$this->tableEMKLJobOrderHeader.'.taxvalue,
                            '.$this->tableEMKLJobOrderHeader.'.totalcreditnote,
                            '.$this->tableEMKLJobOrderHeader.'.totaldebitnote,
                            '.$this->tableEMKLJobOrderHeader.'.totalcommission,
                            '.$this->tableEMKLJobOrderHeader.'.iscommissionpaid 
                        from 
                            '.$this->tableEMKLJobOrderHeader.'
                        where
                            '.$this->tableEMKLJobOrderHeader.'.pkey in ('.$this->oDbCon->paramString($arrSOKey,',').') and
                            '.$this->tableEMKLJobOrderHeader.'.statuskey = 3 and
                            '.$this->tableEMKLJobOrderHeader.'.iscommissionpaid = 0';
                       if($searchCriteria <> '') {
                    $sql .= ' ' .$searchCriteria;
                }

                if($orderCriteria <> ''){
                    $sql .= ' ' .$orderCriteria;
                }
            
                $arrJO = $this->oDbCon->doQuery($sql); 

                //  empty($employeeRow['targetprofit']) bisa saja gk ad target, semua pasti dpt komisi 
                if(empty($employeeRow['commissionpercentage']) ||  empty($employeeRow['targetmonthperiod'])) continue;
  
                // potong creditnote ada kemungkinan missed kah ? karena credit note sekalian motong nilai PPN,
                // sedangkan komisi tdk termasuk ppn

                // ASUMSI : 
                // 1. $joRow['totalreimburse']  gk perlu ad nilai reimbursement, karena komisi dihitung dari profit jadi akan 0 ketika dipotong dengan buying
                // 2. sisi cost harusnya nilainya termasuk PPN, asumsi kalo freight sebagian besar PPN masukan di absorb 
                //    TAPI yang sekarang totalbuying nya tidak termasuk ppn, jd nanti dibenerin

                //  totalreimburse dan totalbuyingtax menyusul

                
                $arrDetailToAdd = array();
                
                $totalProfit = 0; 
                foreach($arrJO as $joRow) {
                    // kalo minus (rugi) tetep dimasukan dalam perhitungan
                        
                    $tempProfit = ($joRow['totalselling'] - $joRow['taxvalue'] - ($joRow['totalbuying'] + $joRow['totalbuyingtax']) - $joRow['totalcommission'] - $joRow['totalcreditnote'] + $joRow['totaldebitnote']);
                    
                    $totalProfit += $tempProfit;
                        
                        
                    array_push($arrDetailToAdd, 
                                    array(
                                            'pkey' => $joRow['sokey'],
                                            'value' => $joRow['socode'], 
                                            'socode' => $joRow['socode'],
                                            'totalselling' => $joRow['totalselling'],
                                            'taxvalue' => $joRow['taxvalue'],
                                            'totalbuying' => $joRow['totalbuying'],
                                            'totalbuyingtax' => $joRow['totalbuyingtax'],
                                            'totalcommission' => $joRow['totalcommission'],
                                            'totalcreditnote' => $joRow['totalcreditnote'],
                                            'totaldebitnote' => $joRow['totaldebitnote'],
                                            'profit' => $tempProfit, 
                                        ));
                               
                }
                        
                $profit = $totalProfit - $employeeRow['targetprofit']; 
                $commissionAmount = $profit * $employeeRow['commissionpercentage'] / 100; 

                $arrResult[] = array(
                    'trdate' => $trDate,
                    'perioddate' =>$this->formatDBDate($startDateDBFormat, 'F Y'),
                    'endperioddate' => $this->formatDBDate($endDateDBFormat, 'F Y'),
                    'employeekey' => $employeeRow['pkey'],
                    'employeecode' => $employeeRow['code'],
                    'employeename' => $employeeRow['name'],
                    'targetmonthperiod' => $employeeRow['targetmonthperiod'],
                    'targetprofit' => $employeeRow['targetprofit'], 
                    'commissionpercentage' => $employeeRow['commissionpercentage'],
                    'totalprofit' => $totalProfit, 
                    'commissionableProfit' => $profit,
                    'totalcommission' => $commissionAmount,
                    'detail' => $arrDetailToAdd 
                );

        }
    
        return $arrResult;

    }

    function generateEmployeeCommission($arrParam=array())
    {
        
        try{ 
            if(!$this->oDbCon->startTrans(true))
                  throw new Exception($this->errorMsg[100]); 

                $rsCommissionData = $this->getEmployeeCommissionData($arrParam);
                
                foreach($rsCommissionData as $commissionRow) {
        
                    // add ke log komisi 
                    $arr['code'] = 'xxxxx';
                    $arr['selStatus'] = 1;
                    $arr['trDate'] = $commissionRow['trdate']; //$this->formatDBDate(date('Y-m-d'), 'd / m / Y');
                    $arr['periodDate'] = $commissionRow['perioddate']; // format ulang karena kena di normalize
                    $arr['endPeriodDate'] = $commissionRow['endperioddate'];
                    $arr['selWarehouse'] = 1;
                    $arr['hidEmployeeKey'] = $commissionRow['employeekey'];
                    $arr['totalCommission'] = $commissionRow['totalcommission'];
                    $arr['commissionPercentage'] = $commissionRow['commissionpercentage'];
                    $arr['targetProfit'] = $commissionRow['targetprofit'];
                    $arr['targetMonthPeriod'] = $commissionRow['targetmonthperiod']; 
                    $arr['totalProfit'] = $commissionRow['totalprofit'];
                        
                    $arr['hidDetailKey'] = array();
                    $arr['hidJobOrderKey'] = array();
                    $arr['totalSelling'] = array();
                    $arr['taxValue'] = array();
                    $arr['totalBuying'] = array();
                    $arr['totalBuyingTax'] = array();
                    $arr['purchaseRefund'] = array();
                    $arr['creditNote'] = array();
                    $arr['debitNote'] = array();
                    $arr['profit'] = array();

                    $rsDetail = $commissionRow['detail'];
 
                    foreach ($rsDetail as $commissionDetailRow) {
                        array_push($arr['hidDetailKey'], 0);
                        array_push($arr['hidJobOrderKey'], $commissionDetailRow['pkey']);
                        array_push($arr['totalSelling'], $commissionDetailRow['totalselling']);
                        array_push($arr['taxValue'], $commissionDetailRow['taxvalue']);
                        array_push($arr['totalBuying'], $commissionDetailRow['totalbuying']);
                        array_push($arr['totalBuyingTax'], $commissionDetailRow['totalbuyingtax']);
                        array_push($arr['purchaseRefund'], $commissionDetailRow['totalcommission']);
                        array_push($arr['creditNote'], $commissionDetailRow['totalcreditnote']);
                        array_push($arr['debitNote'], $commissionDetailRow['totaldebitnote']);
                        array_push($arr['profit'], $commissionDetailRow['profit']);
                    }
                    
                         
                    if(!empty($rsDetail)){ 
                        $result = $this->addData($arr);
                    }
                }
            
            
            $this->oDbCon->endTrans();
            //
        }catch (Exception $e) {
            $this->oDbCon->rollback();
        }
    }

    function getDetailWithRelatedInformation($pkey,$criteria='',$orderBy=''){
            
            $sql = 'select
	   			'.$this->tableNameDetail .'.*,
                '.$this->tableEMKLJobOrderHeader.'.code as jocode, 
                '.$this->tableEMKLJobOrderHeader.'.trdate as jodate,
                '.$this->tableCustomer.'.name as customername
			  from
			  	'.$this->tableNameDetail .' ,
			  	'.$this->tableEMKLJobOrderHeader .' ,
			  	'.$this->tableCustomer .' 
			  where 
			  	'.$this->tableNameDetail .'.jokey = '.$this->tableEMKLJobOrderHeader.'.pkey and
			  	'.$this->tableEMKLJobOrderHeader .'.customerkey = '.$this->tableCustomer.'.pkey and
			  	'.$this->tableNameDetail .'.refkey in('.$this->oDbCon->paramString($pkey,',').')';
          
            $sql .= $criteria;
        
            if(empty($orderBy))
                $sql .= ' order by pkey asc';
            else
                $sql .= ' ' .$orderBy;
                
         
		return $this->oDbCon->doQuery($sql);
    }


    function validateConfirm($rsHeader) {

        $emklJobOrder = new EMKLJobOrder();

        $id = $rsHeader[0]['pkey'];

        $employeekey = $rsHeader[0]['employeekey'];
        $rsDetail = $this->getDetailWithRelatedInformation($id);
        $arrJOKey = array_column($rsDetail, 'jokey');
        
        $arrMsg = array();
        
        $rsJO = $emklJobOrder->searchDataRow(array(
                    $emklJobOrder->tableName.'.pkey',
                    $emklJobOrder->tableName.'.code',
                    $emklJobOrder->tableName.'.saleskey',
                    $emklJobOrder->tableName.'.iscommissionpaid'
                ), ' and ' . $emklJobOrder->tableName.'.pkey in ('. $this->oDbCon->paramString($arrJOKey,',') .') ');

        $rsJOCols = $this->reindexDetailCollections($rsJO, 'pkey');
        
        $arrJO = array();
        

        //validasi iscommissionpad masih 0 tdk.

        for($i=0;$i<count($rsJO);$i++) {
            if($rsJO[$i]['iscommissionpaid'] <> 0) 
                array_push($arrMsg, '<strong>'.$rsJO[$i]['code'].'. </strong>' . $this->errorMsg['employeeCommission'][1]); 
        }
        // gk perlu validasi dulu, anggap semau baesd on sales customer
        
//        for($i=0; $i<count($rsDetail); $i++) {
//            $jokey = $rsDetail[$i]['jokey'];
//
//            if(isset($rsJOCols[$jokey])) {
//
//                $rsJOCol = $rsJOCols[$jokey];
//                $saleskey = $rsJOCol[0]['saleskey'];
//
//                if($saleskey <> $employeekey) {
//                    array_push($arrMsg, '<strong>'. $rsJOCol[0]['code'] .'. </strong>' . $this->errorMsg['employeeCommission'][1]);
//                }
//            }
//
//            if(in_array($jokey, $arrJO)) {
//                array_push($arrMsg, '<strong>'. $rsDetail[$i]['jocode'] .'. </strong>' . $this->errorMsg[280]);
//            }
//
//            array_push($arrJO, $jokey);
//
//        }

        if(!empty($arrMsg)) {
            $this->addErrorLog(false, '<strong>' . $rsHeader[0]['code'] . '. </strong>' . $this->errorMsg[201] . '<br>' . implode('<br>', $arrMsg) . '</strong>');
        }

	}

    function confirmTrans($rsHeader){ 
        
//        $id = $rsHeader[0]['pkey'];
//        
//        $arrParam = array();
//        $arrParam['employeekey'] = $rsHeader[0]['employeekey'];
//        $arrParam['trDate'] = $this->formatDBDate($rsHeader[0]['trdate'], 'd / m / Y');
//
//        
//        $date = new DateTime($rsHeader[0]['perioddate']); 
//        $date->sub(new DateInterval('P1D'));     
//        $arrParam['endDate'] = $date->format('d / m / Y'); 
//            
//            
//        $this->generateEmployeeCommission($arrParam);
        $this->addAPEmployeeCommission($rsHeader);
	}

    function validateCancel($rsHeader,$autoChangeStatus=false) {
        $id = $rsHeader[0]['pkey'];

        $apEmployeeCommission = new APEmployeeCommission();

        $employeeCommissionTableKey = $this->getTableKeyAndObj($this->tableName, array('key'))['key'];
        $rsAP = $apEmployeeCommission->searchDataRow(
            array($apEmployeeCommission->tableName . '.pkey', $apEmployeeCommission->tableName . '.code'),
            ' and  ' . $apEmployeeCommission->tableName . '.refkey = ' . $this->oDbCon->paramString($rsHeader[0]['pkey']) . ' and ' . $apEmployeeCommission->tableName . '.reftabletype = ' . $employeeCommissionTableKey . ' and ' . $apEmployeeCommission->tableName . '.statuskey in (2,3)'
        );

        $totalAP = count($rsAP);
        $errMsg = array();
        for ($i = 0; $i < $totalAP; $i++) {
            array_push($errMsg, '<strong>'. $rsAP[$i]['code'] .'. </strong>' . $this->errorMsg[203]);
        }

        if(!empty($errMsg)) {
            $this->addErrorLog(false, '<strong>' . $rsHeader[0]['code'] . '</strong>. ' . $this->errorMsg[201] . '<br>' . implode('<br>', $errMsg));
        }
    }


    function cancelTrans($rsHeader,$copy){
        $id = $rsHeader[0]['pkey']; 

        $this->cancelAPEmployeeCommission($rsHeader);

		if ($copy) $this->copyDataOnCancel($id);

	} 

    function normalizeParameter($arrParam, $trim = false)
    {
        $security = new Security();
        $this->overrideCommission = $security->isAdminLogin($this->overrideEmployeeCommissionObject,10);
            
        $arrParam = parent::normalizeParameter($arrParam,true);  
    
        $arrParam['periodDate'] =  date("01 / m / Y",strtotime($arrParam['periodDate']));
        $arrParam['endPeriodDate'] =  (isset($arrParam['endPeriodDate'])) ? 
                                    date("t / m / Y 23:59",strtotime($arrParam['endPeriodDate'])) : 
                                    date("t / m / Y 23:59",strtotime($arrParam['periodDate']));
        
        $calculateCommission = $this->calculateCommission($arrParam);
        
        $arrParam['totalProfit'] = $calculateCommission['profitamount'];
        
        if (!$this->overrideCommission)
            $arrParam['totalCommission'] = $calculateCommission['commissionamount'];

        $arrDetail = $calculateCommission['detail'];

        $arrJOKey = $arrParam['hidJobOrderKey'];

        for($i=0; $i<count($arrParam['hidDetailKey']); $i++) {

            if (!isset($arrDetail[$arrParam['hidJobOrderKey'][$i]]))
                continue;

            $arrDetailCol = $arrDetail[$arrParam['hidJobOrderKey'][$i]];

            $arrParam['totalBuying'][$i] = $arrDetailCol[0]['totalbuying'];
            $arrParam['totalSelling'][$i] = $arrDetailCol[0]['totalselling'];
            $arrParam['taxValue'][$i] = $arrDetailCol[0]['taxvalue']; 
            $arrParam['purchaseRefund'][$i] = $arrDetailCol[0]['totalcommission'];
            $arrParam['creditNote'][$i] = $arrDetailCol[0]['totalcreditnote'];
            $arrParam['debitNote'][$i] = $arrDetailCol[0]['totaldebitnote'];
            $arrParam['profit'][$i] = $arrDetailCol[0]['profit'];
        }

        return $arrParam;

    }

    function addAPEmployeeCommission($rsHeader)
    {
        $apEmployeeCommission = new APEmployeeCommission();
        $employee = new Employee();

        $employeeCommissionTableKey = $this->getTableKeyAndObj($this->tableName, array('key'))['key'];

        $commissionKey = $rsHeader[0]['pkey'];
        $commissionCode = $rsHeader[0]['code'];
        $trDate = $this->formatDBDate($rsHeader[0]['trdate'], 'd / m / Y');
        $employeekey = $rsHeader[0]['employeekey'];
        $rsEmployee = $employee->getDataRowById($rsHeader[0]['employeekey']);
        $commissionAmount = $rsHeader[0]['totalcommission'];

        if ($commissionAmount <=0) return;
        
        $arrDataAP = array(
                        'code' => 'xxxxx',
                        'trDate' => $trDate,
                        'hidRefTable' => $employeeCommissionTableKey,
                        'hidRefKey' => $commissionKey,
                        'hidRefCode'  => $commissionCode,
                        'hidEmployeeKey' => $employeekey,  
                        'selWarehouse' => $rsEmployee[0]['warehousekey'], 
                        'selCurrency' => CURRENCY['idr'],
                        'rate' => 1,
                        'amount' => $commissionAmount, 
                        'selAPType' => AP_TYPE['salesCommission']
                );

        $arrayToJs = $apEmployeeCommission->addData($arrDataAP);
        //$this->setLog($arrayToJs, true);
        if (!$arrayToJs[0]['valid'])
            throw new Exception('<strong>' . $rsHeader[0]['code'] . '</strong>. ' . $this->errorMsg[201] . ' ' . $arrayToJs[0]['message']);
    }

    function cancelAPEmployeeCommission($rsHeader) {
        $apEmployeeCommission = new APEmployeeCommission();

        $employeeCommissionTableKey = $this->getTableKeyAndObj($this->tableName, array('key'))['key'];
        $rsAP = $apEmployeeCommission-> searchDataRow( array($apEmployeeCommission->tableName.'.pkey', $apEmployeeCommission->tableName.'.code'  ) , 
                            ' and  '.$apEmployeeCommission->tableName.'.refkey = '.$this->oDbCon->paramString($rsHeader[0]['pkey']).' and '.$apEmployeeCommission->tableName.'.reftabletype = '.$employeeCommissionTableKey.' and '.$apEmployeeCommission->tableName.'.statuskey = 1'  
                    );

        $totalAP = count($rsAP);
        for($i=0;$i<$totalAP;$i++) { 
            $apEmployeeCommission->changeStatus($rsAP[$i]['pkey'],4,'',false, true);  
        }
    }

    function getLastPaymentDate($pkey) 
    {

        $ar = new AR();
        $arPayment = new ARPayment();
        $emklOrderInvoice = new EMKLOrderInvoice(); 
        $emklJobOrder = new EMKLJobOrder();
        
        $invTableKey = $this->getTableKeyAndObj($emklOrderInvoice->tableName, array('key'))['key'];  
        $sql = '
                        select
                            '. $emklOrderInvoice->tableNameDetail .'.refsalesorderheaderkey as sokey,
                            '. $arPayment->tableName .'.code as arpaymentcode,
                            '. $arPayment->tableName .'.trdate as arpaymentdate,
                            '. $emklJobOrder->tableName .'.code 
                        from
                            '. $arPayment->tableName .',
                            '. $arPayment->tableNameDetail .',
                            '. $ar->tableName  .',
                            '. $emklOrderInvoice->tableName .',
                            '. $emklOrderInvoice->tableNameDetail .', 
                            '. $this->tableCustomer .',
                           '. $emklJobOrder->tableName .' 
                        where
                            '. $arPayment->tableName .'.pkey = '. $arPayment->tableNameDetail .'.refkey and 
                            '. $arPayment->tableNameDetail .'.arkey = '.$ar->tableName.'.pkey and 
                            '. $ar->tableName .'.reftabletype = '.$this->oDbCon->paramString($invTableKey).' and
                            '. $ar->tableName .'.refkey = '.$emklOrderInvoice->tableName.'.pkey and 
                            '. $emklOrderInvoice->tableName .'.pkey = '.$emklOrderInvoice->tableNameDetail.'.refkey and 
                            '. $emklOrderInvoice->tableName .'.customerkey = '.$this->tableCustomer.'.pkey and 
                            '. $emklOrderInvoice->tableNameDetail .'.refsalesorderheaderkey = '. $emklJobOrder->tableName .'.pkey and
                            '. $arPayment->tableName .'.statuskey in (2,3) and
                            '. $ar->tableName .'.statuskey = 3 and
                            '. $emklOrderInvoice->tableNameDetail .'.refsalesorderheaderkey in ('. $this->oDbCon->paramString($pkey,',') .')
                    ';
                    $rsData = $this->oDbCon->doQuery($sql);

                    $groupedData = [];
                    foreach ($rsData as $row) {
                        $sokey = $row['sokey'];
                    
                        if (!isset($groupedData[$sokey]) || strtotime($row['arpaymentdate']) > strtotime($groupedData[$sokey]['arpaymentdate'])) {
                            $groupedData[$sokey] = $row;
                        }
                    }
            
        return $groupedData;
    }
  function afterStatusChanged($rsHeader){ 
        $id = $rsHeader[0]['pkey'];

        $rsHeader = $this->getDataRowById($id);
        //update Job Order iscommissionpaid
        $this->updateJobOrderCommissionIsPaid($id, $rsHeader[0]['statuskey']);
    }

    function updateJobOrderCommissionIsPaid($pkey, $statuskey)
    {

        $rsDetail = $this->getDetailWithRelatedInformation($pkey);

        if(empty($rsDetail)) return;

        $isPaid = (($statuskey == TRANSACTION_STATUS['konfirmasi']) || ($statuskey == TRANSACTION_STATUS['selesai']) ? 1 : 0);
        

        $sql = '
        update ' . $this->tableEMKLJobOrderHeader . '
        set  iscommissionpaid = ' . $this->oDbCon->paramString($isPaid) . '
        where  pkey in (' . $this->oDbCon->paramString(array_column($rsDetail,'jokey'),',') . ')';

        $this->oDbCon->execute($sql);
        
        //
        //foreach($rsDetail as $detailRow) {
        //    //$this->setJobOrderCommissionPaid($detailRow['jokey'], $isPaid); 
        //}

    } 

}

?>
