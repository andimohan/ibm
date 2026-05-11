<?php
class CustomerDownpayment extends Downpayment
{

    function __construct()
    {

        parent::__construct();

        $this->tableName = 'customer_downpayment';
        $this->tableCustomer = 'customer';
        $this->tableCurrency = 'currency';
        $this->tablePayment = 'customer_downpayment_payment';
        $this->tableCashBank = 'cash_bank';
        $this->securityObject = 'CustomerDownpayment';
        $this->isTransaction = true;


        $arrPaymentDetail = array();
        $arrPaymentDetail['pkey'] = array('hidDetailPaymentKey');
        $arrPaymentDetail['refkey'] = array('pkey', 'ref');
        $arrPaymentDetail['amount'] = array('paymentMethodValue', array('datatype' => 'number', 'mandatory' => true));
        $arrPaymentDetail['paymentkey'] = array('selPaymentMethod');   // gk boleh mandatory, karena kadang pake payment kadang pake voucher, validasi di add saja
		$arrPaymentDetail['cashbankvoucherkey'] = array('selVoucher');  // gk boleh mandatory, karena kadang pake payment kadang pake voucher, validasi di add saja
       

        $this->arrDetails = array();
        array_push($this->arrDetails, array('dataset' => $arrPaymentDetail, 'tableName' => $this->tablePayment));

        $this->arrData = array();
        $this->arrData['pkey'] = array('pkey', array('dataDetail' => $this->arrDetails));
        $this->arrData['code'] = array('code');
        $this->arrData['trdate'] = array('trDate', 'date');
        $this->arrData['refheaderkey'] = array('hidRefHeaderKey');
        $this->arrData['refkey'] = array('hidRefKey');
        $this->arrData['refcode'] = array('refCode');
        $this->arrData['reftabletype'] = array('selDPType');
        $this->arrData['trdesc'] = array('trDesc');
        $this->arrData['amount'] = array('amount', 'number');
        $this->arrData['outstanding'] = array('amount', 'number');
        //$this->arrData['beforetaxtotal'] = array('beforeTaxTotal', 'number'); // recalculate
        //$this->arrData['ispriceincludetax'] = array('chkIncludeTax');
        //$this->arrData['taxpercentage'] = array('taxPercentage','number'); 
        //$this->arrData['taxvalue'] = array('taxValue','number');  // recalculate
        //$this->arrData['subtotal'] = array('subtotal','number');   // recalculate
        //$this->arrData['prepaidtax23percentage'] = array('prepaidTax23Percentage', 'number'); 
        //$this->arrData['prepaidtax23'] = array('prepaidTax23', 'number');   // recalculate
        $this->arrData['payment'] = array('payment', 'number'); // recalculate

        $this->arrData['statuskey'] = array('selStatus');
        $this->arrData['overwriteGL'] = array('overwriteGL');
        $this->arrData['warehousekey'] = array('selWarehouse');
        $this->arrData['customerkey'] = array('hidCustomerKey');
        $this->arrData['currencykey'] = array('selCurrency');
        $this->arrData['termofpaymentkey'] = array('selTermOfPaymentKey');
        $this->arrData['rate'] = array('currencyRate', 'number');


        $this->arrDataListAvailableColumn = array();
        array_push($this->arrDataListAvailableColumn, array('code' => 'code', 'title' => 'code', 'dbfield' => 'code', 'default' => true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'date', 'title' => 'date', 'dbfield' => 'trdate', 'default' => true, 'width' => 80, 'align' => 'center', 'format' => 'date'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'refCode', 'title' => 'reference', 'dbfield' => 'refcode', 'default' => true, 'width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'customer', 'title' => 'customer', 'dbfield' => 'customername', 'default' => true, 'width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'amount', 'title' => 'downpayment', 'dbfield' => 'amount', 'default' => true, 'width' => 80, 'align' => 'right', 'format' => 'number'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'outstanding', 'title' => 'outstanding', 'dbfield' => 'outstanding', 'default' => true, 'width' => 80, 'align' => 'right', 'format' => 'number'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'currency', 'title' => 'curr', 'dbfield' => 'currencyname', 'default' => true, 'align' => 'center', 'width' => 60));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status', 'title' => 'status', 'dbfield' => 'statusname', 'default' => true, 'width' => 70));
        array_push($this->arrDataListAvailableColumn, array('code' => 'warehouse', 'title' => 'warehouse', 'dbfield' => 'warehousename',  'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'desc', 'title' => 'note', 'dbfield' => 'trdesc',  'width' => 200));

        $this->printMenu = array();
        array_push($this->printMenu, array('code' => 'printTransaction', 'name' => $this->lang['printTransaction'],  'icon' => 'print', 'url' => 'print/customerDownpayment'));


        $this->includeClassDependencies(array(
            'Downpayment.class.php',
            'Currency.class.php',
            'Customer.class.php',
            'TruckingServiceOrder.class.php',
            'COALink.class.php',
            'GeneralJournal.class.php',
            'Warehouse.class.php',
            'PaymentMethod.class.php',
            'TermOfPayment.class.php',
            'CustomerDownpaymentSettlement.class.php',
            'AR.class.php',
            'ARPayment.class.php',
            'CashBank.class.php',
            'TruckingServiceOrderInvoice.class.php',
            'EMKLOrderInvoice.class.php',
            'SalesOrder.class.php',
            'SalesOrderProperty.class.php',
            'ChartOfAccount.class.php'
        ));


        $this->overwriteConfig();
    }

    function getQuery()
    {

        return '
				select
					' . $this->tableName . '.*,
					' . $this->tableCustomer . '.name as customername,
					' . $this->tableStatus . '.status as statusname,
					' . $this->tableWarehouse . '.name as warehousename ,
                    ' . $this->tableCurrency . '.name as currencyname
				from 
					' . $this->tableName . '
                        left join ' . $this->tableCurrency . ' on ' . $this->tableName . '.currencykey = ' . $this->tableCurrency . '.pkey,
                    ' . $this->tableStatus . ' ,
                    ' . $this->tableCustomer . ' ,
                    ' . $this->tableWarehouse . ' 
				where  		
					' . $this->tableName . '.statuskey = ' . $this->tableStatus . '.pkey and 
					' . $this->tableName . '.warehousekey = ' . $this->tableWarehouse . '.pkey and 
					' . $this->tableName . '.customerkey = ' . $this->tableCustomer . '.pkey
		' . $this->criteria;
    }

    function validateForm($arr, $pkey = '')
    {

        $arrayToJs = parent::validateForm($arr, $pkey);

        $customer = new Customer();

        $customerkey = $arr['hidCustomerKey'];
        $transactionType = $arr['selDPType'];
        $hidRefKey = $arr['hidRefKey'];

        //validasi kalo status gk menunggu gk bisa edit 
        if (!empty($pkey)) {
            $rs = $this->getDataRowById($pkey);
            if ($rs[0]['statuskey'] <> 1) {
                $this->addErrorList($arrayToJs, false, $this->errorMsg[212]);
            }
        }

        $rsCustomer = $customer->getDataRowById($customerkey);
        if (empty($rsCustomer))
            $this->addErrorList($arrayToJs, false, $this->errorMsg['customer'][1]);


        // cek transaksi sesuai gk dengan customer 
        if (!empty($hidRefKey)) {
            $rsObj = $this->getTableNameAndObjById($transactionType);
            $obj = $rsObj['obj'];
            //$this->setLog($obj->tableName. ' : ' . $hidRefKey);
            $rsTSO = $obj->getDataRowById($hidRefKey);
            if ($rsTSO[0]['customerkey'] <> $customerkey) {
                $this->addErrorList($arrayToJs, false, $this->errorMsg['downpayment'][3]);
            }
        }

        return $arrayToJs;
    }

        
	 function validateClose($rsHeader){ 
        
        $id = $rsHeader[0]['pkey'];
         
        $arrayToJs = array(); 
		$rs  = $this->getDataRowById($id); 
         
        if($rs[0]['outstanding'] <=0) return; // boleh closed kalo sudah habis
            
        $this->addErrorLog(false, '<strong>' . $rsHeader[0]['code'] . '</strong>. ' . $this->errorMsg[201]);
		return $arrayToJs;
          
	 } 	 
    
    
    function validateConfirm($rsHeader)
    {

        $id = $rsHeader[0]['pkey'];
		
        $customerkey = $rsHeader[0]['customerkey'];
        $coaLink = new COALink();
        $warehouse = new Warehouse();
        $termOfPayment = new TermOfPayment();
        $rsTOP = $termOfPayment->getDataRowById($rsHeader[0]['termofpaymentkey']);
        $isCash = ($rsTOP[0]['duedays'] == 0) ? true : false;


        // cek transaksi sesuai gk dengan customer
        if (!empty($rsHeader[0]['refkey'])) {
            $rsObj = $this->getTableNameAndObjById($rsHeader[0]['reftabletype']);
            $obj = $rsObj['obj'];
            $rs = $obj->getDataRowById($rsHeader[0]['refkey']);

            if ($rs[0]['customerkey'] <> $rsHeader[0]['customerkey'])
                $this->addErrorLog(false, '<strong>' . $rsHeader[0]['code'] . '</strong>. ' . $this->errorMsg['downpayment'][3]);
        }

		
        $rsPayment = (ADV_FINANCE && TEST_VOUCHER) ?  $this->getPaymentVoucherDetail($id) : $this->getPaymentMethodDetail($id); 
      
        $totalPayment = 0;
        for ($i = 0; $i < count($rsPayment); $i++)
            $totalPayment += $rsPayment[$i]['amount'];

        $balance = $totalPayment - $rsHeader[0]['amount'];

        if ($isCash) {
            $thresholdDiscount = abs($this->loadSetting('roundedPaymentThreshold'));
            if ($balance < ($thresholdDiscount * -1))
                $this->addErrorLog(false, '<strong>' . $rsHeader[0]['code'] . '</strong>. ' . $this->errorMsg[502]);
            else if ($balance > $thresholdDiscount)
                $this->addErrorLog(false, '<strong>' . $rsHeader[0]['code'] . '</strong>. ' . $this->errorMsg[509]);
        } else {
            $customer = new Customer();
            $rsCustomer = $customer->getDataRowById($customerkey);

            if ($rsCustomer[0]['creditlimit'] > 0) {
                $total = $this->unFormatNumber($rsHeader[0]['grandtotal']);
                if ($customer->willExceedCreditLimit($customerkey, $total)) {
                    $this->addErrorLog(false, '<strong>' . $rsHeader[0]['code'] . '</strong>. ' . $this->errorMsg['creditlimit'][1]);
                }
            }
        }

        if (USE_GL) {
            $arrCOA = array();
            array_push($arrCOA, 'customerdownpayment');
            for ($i = 0; $i < count($arrCOA); $i++) {
                $rsCOA = $coaLink->getCOALink($arrCOA[$i], $warehouse->tableName, $rsHeader[0]['warehousekey'], 0);
                if (empty($rsCOA))
                    $this->addErrorLog(false, '<strong>' . $rsHeader[0]['code'] . '</strong>. ' . $arrCOA[$i] . ' ' . $this->errorMsg['coa'][3]);
            }
			
            if ($isCash){ 
				  if (ADV_FINANCE && TEST_VOUCHER){
						for($i=0;$i<count($rsPayment); $i++){ 
							// cek kalo customerkey sudah beda
							if ($rsPayment[$i]['vouchercustomerkey'] <> $customerkey)
								 $this->addErrorLog(false,'<b>'.$rsPayment[$i]['vouchercode']. '</b>. ' . $this->errorMsg['cashBank'][3]); 
							else if ($rsPayment[$i]['voucheroutstanding'] < $rsPayment[$i]['amount'])
								// cek kalo outstanding masih cukup
								 $this->addErrorLog(false,'<b>'.$rsPayment[$i]['vouchercode']. '</b>. ' . $this->errorMsg['cashBank'][4]); 

							else if ($rsPayment[$i]['voucherstatuskey'] <> TRANSACTION_STATUS['konfirmasi'])
								 $this->addErrorLog(false,'<b>'.$rsPayment[$i]['vouchercode']. '</b>. ' . $this->errorMsg['cashBank'][5]); 

						}  
					}else{ 
							for ($i = 0; $i < count($rsPayment); $i++) {
								if ($rsPayment[$i]['amount'] > 0) {
									$rsCOA = $coaLink->getCOALink('payment', $warehouse->tableName, $rsHeader[0]['warehousekey'], $rsPayment[$i]['paymentkey']);
									if (empty($rsCOA))
										$this->addErrorLog(false, '<strong>' . $rsHeader[0]['code'] . '</strong>. ' . $this->errorMsg['coa'][3]);
								}
							}
				  }
			}
        }
    }

    function confirmTrans($rsHeader)
    {
        $id = $rsHeader[0]['pkey'];
        $termOfPayment = new TermOfPayment();
        $coaLink = new COALink();
        $warehouse = new Warehouse();
        $customer = new Customer();
        $ar = new AR();

        $isActiveCashBank = $this->isActiveModule('CashBank');
        if ($isActiveCashBank)
            $cashBank = new CashBank();

        $rsCustomer = $customer->getDataRowById($rsHeader[0]['customerkey']);
        $note = $this->lang['customerDownpayment'] . ', ' . $rsCustomer[0]['name'];
        $rsTOP = $termOfPayment->getDataRowById($rsHeader[0]['termofpaymentkey']);
        $isCash = ($rsTOP[0]['duedays'] == 0) ? true : false;
        $rate = ($rsHeader[0]['currencykey']==CURRENCY['idr']) ? 1 : $rsHeader[0]['rate']; 
		$rsPayment = $this->getPaymentMethodDetail($id);
		
        //update ar service
        if ($isCash) {


			if (ADV_FINANCE && TEST_VOUCHER){ 
				$rsPayment = $this->getPaymentVoucherDetail($id);

				$rsARKey = $this->getTableKeyAndObj($this->tableName,array('key'));    

				// update outstanding voucher  
				foreach($rsPayment as $voucherlist){ 
					$cashBank->insertTransaction(
						array('refkey' => $voucherlist['cashbankvoucherkey'],
							  'reftablekey' => $rsARKey['key'],
							  'reftranskey' => $rsHeader[0]['pkey'],
							  'refcode' => $rsHeader[0]['code'],
							  'refdate' => $rsHeader[0]['trdate'],
							  'amount' => $voucherlist['amount'],
							 )
					); 
				}

			}else{
				for ($i = 0; $i < count($rsPayment); $i++) {
					if (USE_GL) {
						$rsPaymentCOA = $coaLink->getCOALink('payment', $warehouse->tableName, $rsHeader[0]['warehousekey'], $rsPayment[$i]['paymentkey']);
						$coakey = $rsPaymentCOA[0]['coakey'];
					} else {
						$coakey = $rsPayment[$i]['paymentkey'];
					}

					if ($isActiveCashBank) {
						$rsCashBank = $cashBank->addCashBank($rsHeader, $this->tableName, array('desc' => $note, 'coakey' => $coakey, 'customerkey' => $rsHeader[0]['customerkey'], 'amount' => $rsPayment[$i]['amount'], 'outstanding' => 0 /*$rsPayment[$i]['amount']*/));
						$rsPayment[$i]['cashBankKey'] = $rsCashBank['pkey'];
					}
				}

			}
        } else {
			
        	$refTableKey = $this->getTableKeyAndObj($this->tableName, array('key'))['key'];
            $ar = new AR();

            $arrParam = array();

            $arrParam['code'] = 'xxxxxx';
            $arrParam['hidCustomerKey'] = $rsHeader[0]['customerkey'];
            $arrParam['hidSalesKey'] = $rsHeader[0]['saleskey'];
            $arrParam['hidRefKey'] = $id;
            $arrParam['hidRefHeaderKey'] = $id;
            $arrParam['hidRefCode'] =  $rsHeader[0]['code'];
            $arrParam['hidRefCode2'] =  $rsHeader[0]['refcode'];
            $arrParam['hidRefTable'] = $refTableKey;
            $arrParam['hidRefDate'] =   $this->formatDBDate($rsHeader[0]['trdate'], 'd / m / Y');
            $arrParam['selWarehouse'] = $rsHeader[0]['warehousekey'];
            $arrParam['selARType'] = 4;
            $arrParam['amount'] = abs($rsHeader[0]['amount']);
			$arrParam['amountIDR'] = abs($rsHeader[0]['amount']) * $rate; 
            $arrParam['trDesc'] = '';
            $arrParam['trDate'] =  $this->formatDBDate($rsHeader[0]['trdate'], 'd / m / Y');
            $date = new DateTime($rsHeader[0]['trdate']);
            $date->add(new DateInterval('P' . $rsTOP[0]['duedays'] . 'D'));
            $arrParam['dueDate'] = $date->format('d / m / Y'); // date ('d / m / Y', mktime(0, 0, 0, date("m")  , date("d")+$rsTOP[0]['duedays'], date("Y")));
            $arrParam['createdBy'] = 0;
            $arrParam['islinked'] = 1;
            $arrParam['overwriteGL'] = 1;

            $arrayToJs = $ar->addData($arrParam);

            if (!$arrayToJs[0]['valid'])
                throw new Exception('<strong>' . $rsHeader[0]['code'] . '</strong>. ' . $this->errorMsg[201] . ' ' . $arrayToJs[0]['message']);
        }
        //update jurnal umum 
        // biar gk usah query ulang
        $rsHeader[0]['customername'] = $rsCustomer[0]['name'];
        $this->updateGL($rsHeader, $rsPayment);
    }

    function cancelTrans($rsHeader, $copy)
    {

        $id = $rsHeader[0]['pkey'];

        $rsKey = $this->getTableKeyAndObj($this->tableName,array('key')); 
		
		if( $this->isActiveModule('CashBank') ){
			$cashBank = new CashBank();
			if (ADV_FINANCE && TEST_VOUCHER){ 
				$cashBank->removeTransaction($id,$rsKey['key']);
			}else{ 
				$cashBank->cancelCashBank($rsHeader,$this->tableName);
			}
		}
		
        $ar = new AR();
        $rsARKey = $ar->getTableKeyAndObj($this->tableName);
        $rsAR = $ar->searchData('', '', true, ' and reftabletype = ' . $this->oDbCon->paramString($rsARKey['key']) . ' and refkey = ' . $this->oDbCon->paramString($id) . ' and ' . $ar->tableName . '.statuskey = 1');
        for ($i = 0; $i < count($rsAR); $i++) {
            $arrayToJs = $ar->changeStatus($rsAR[$i]['pkey'], TRANSACTION_STATUS['batal'], '', false, true);
            if (!$arrayToJs[0]['valid'])
                throw new Exception('<strong>' . $rsHeader[0]['code'] . '</strong>. ' .  $arrayToJs[0]['message']);
        }


        if ($copy)
            $this->copyDataOnCancel($id);

        $this->cancelGLByRefkey($id, $this->tableName);
    }


    function updateGL($rsHeader, $rsPayment)
    {
        if (!USE_GL) return;

        $isActiveCashBank = $this->isActiveModule('CashBank');

        // kalo dr invoice, jgn buat jurnal DP lg.

        $warehouse = new Warehouse();
        $coaLink = new COALink();
        $customer = new Customer();
        $cashBank = new CashBank();
        $generalJournal = new GeneralJournal();
		$chartOfAccount = new ChartOfAccount();

        $warehousekey = $rsHeader[0]['warehousekey'];

        //$hasPrepaidTax = (!empty($rs[0]['prepaidtax23'])) ? true : false;
        //$hasTax = (!empty($rs[0]['prepaidtax23'])) ? true : false;

        $rate = ($rsHeader[0]['rate'] > 0) ? $rsHeader[0]['rate'] : 1;

        $desc = array();
        if (!empty($rsHeader[0]['customername']))
            array_push($desc, $this->lang['customerDownpayment'] . ', ' . $rsHeader[0]['customername']);

        if (!empty($rsHeader[0]['trdesc']))
            array_push($desc, $rsHeader[0]['trdesc']);

        $desc = implode(chr(13), $desc);

        $rsKey = $generalJournal->getTableKeyAndObj($this->tableName);
        $arr = array();
        $arr['pkey'] = $generalJournal->getNextKey($generalJournal->tableName);
        $arr['code'] = 'xxxxx';
        $arr['refkey'] = $rsHeader[0]['pkey'];
        $arr['refTableType'] = $rsKey['key'];
        $arr['trDate'] =  $this->formatDBDate($rsHeader[0]['trdate'], 'd / m / Y');
        $arr['trDesc'] = $desc;
        $arr['createdBy'] = 0;
        $arr['selWarehouseKey'] = $rsHeader[0]['warehousekey'];

        $termOfPayment = new TermOfPayment();
        $rsTOP = $termOfPayment->getDataRowById($rsHeader[0]['termofpaymentkey']);
        $isCash = ($rsTOP[0]['duedays'] == 0) ? true : false;

        $temp = -1;
        if ($isCash) {
//            for ($i = 0; $i < count($rsPayment); $i++) {
//                $rsCOA = $coaLink->getCOALink('payment', $warehouse->tableName, $warehousekey, $rsPayment[$i]['paymentkey']);
//                $temp++;
//                $arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];
//                $arr['debit'][$temp] = $rsPayment[$i]['amount'] * $rate;
//                $arr['credit'][$temp] = 0;
//
//                if ($isActiveCashBank) $arr['refCashBankKey'][$temp] = $rsPayment[$i]['cashBankKey'];
//            }
			
			
			if(ADV_FINANCE && TEST_VOUCHER) 
				$rsPayment = $this->getPaymentVoucherDetail($rsHeader[0]['pkey']);
			
				
			for($i=0;$i<count($rsPayment); $i++){ 
				// khusus logol
				// adv_finance, payment menggunakan voucher

				if(ADV_FINANCE && TEST_VOUCHER){ 
					$rsCashBank = $cashBank->getDataRowById($rsPayment[$i]['cashbankvoucherkey']); 
					$rsCOA = $chartOfAccount->getDataRowById($rsCashBank[0]['coakey']); 
					$paymentcoakey = $rsCOA[0]['countercoakey'];
				}else{
					$rsCOA = $coaLink->getCOALink('payment', $warehouse->tableName, $warehousekey, $rsPayment[$i]['paymentkey']);
					$paymentcoakey = $rsCOA[0]['coakey'];
				}

				 $temp++; 
				 $paymentAmount = $rsPayment[$i]['amount'] * $rate;
				 $arr['hidCOAKey'][$temp] = $paymentcoakey;
				 $arr['debit'][$temp] = $paymentAmount; 
				 $arr['credit'][$temp] = 0; 
				 $arr['refCashBankKey'][$temp] = (isset($rsPayment[$i]['cashBankKey'])) ? $rsPayment[$i]['cashBankKey'] : 0;  // perlu dicek gk logol sama atau gk
//				 $totalPaymentAmount += $paymentAmount;
			}
			
        } else {
            //akun piutang  
            $temp++;
            $arr['hidCOAKey'][$temp] = $customer->getARCOAKey($rsHeader[0]['customerkey'], $warehousekey);
            $arr['debit'][$temp] = $rsHeader[0]['amount']* $rate;
            $arr['credit'][$temp] = 0;
            $arr['refCashBankKey'][$temp] = '';  
        }

 
        $temp++;
        $arr['hidCOAKey'][$temp] = $customer->getDownpaymentCOAKey($rsHeader[0]['customerkey'], $warehousekey);
        $arr['debit'][$temp] = 0;
        $arr['credit'][$temp] = $rsHeader[0]['amount'] * $rate;
        $arr['refCashBankKey'][$temp] = '';  

        $arrayToJs = $generalJournal->addData($arr);

        if (!$arrayToJs[0]['valid'])
            throw new Exception('<strong>' . $rsHeader[0]['code'] . '</strong>. ' . $this->errorMsg[504] . ' ' . $arrayToJs[0]['message']);
    }

    /*function delete($id, $forceDelete = false,$reason = ''){ 
		
		 $arrayToJs =  array();  
		 $arrayToJs = $this->changeStatus($id, 4);  
		 return $arrayToJs; 

	}*/

    function getTotalDownpayment($refkey, $customerkey)
    {
        $sql = 'select 
                    coalesce(sum(amount),0) as totaldownpayment
                from 
                    ' . $this->tableName . ' 
                where statuskey in (2,3,4) ';

        if (!empty($refkey))
            $sql .= ' and refkey = ' . $this->oDbCon->paramString($refkey);

        if (!empty($customerkey))
            $sql .= ' and customerkey = ' . $this->oDbCon->paramString($customerkey);


        //$this->setLog($sql);
        $rs = $this->oDbCon->doQuery($sql);

        return $rs;
    }

    function getSalesOrder($criteria = '', $orderBy = '')
    {

        $arrTable = array();

        if ($this->isActiveModule('TruckingServiceOrder'))  array_push($arrTable, array('tableName' => $this->tableJobOrder, 'statuskey' => '(1,2,3,4,5)'));
        if ($this->isActiveModule('SalesOrder')) array_push($arrTable, array('tableName' => $this->tableSalesOrder, 'statuskey' => '(1,2)'));
        if ($this->isActiveModule('SalesOrderProperty')) array_push($arrTable, array('tableName' => $this->tableSalesOrderProperty, 'statuskey' => '(1,2)'));


        $arrSQL = array();
        foreach ($arrTable as $table) {
            $rsKey = $this->getTableKeyAndObj($table['tableName']);

            $sql = 'select pkey, code as value, ' . $rsKey['key'] . ' as  tabletypekey from ' . $table['tableName'] . ' where statuskey in ' . $table['statuskey'];
            array_push($arrSQL, $sql);
        }

        $sql = 'select * from (' . implode(' union ', $arrSQL) . ') as sales_order where 1=1 ';

        if (!empty($criteria))
            $sql .= ' ' . $criteria;

        if (!empty($orderBy))
            $sql .= ' ' . $orderBy;

        $rs =  $this->oDbCon->doQuery($sql);
        return $rs;
    }

    function generateDefaultQueryForAutoComplete($returnField)
    {
        $sql = 'select
                ' . $returnField['key'] . ',
                ' . $returnField['value'] . ' as value,
                ' . $this->tableName . '.refcode,
                ' . $this->tableName . '.amount,
                ' . $this->tableName . '.outstanding
            from 
                ' . $this->tableName . ',
                ' . $this->tableStatus . '  
            where  		
                ' . $this->tableName . '.statuskey = ' . $this->tableStatus . '.pkey  
        ';

        $sql .=  $this->getCompanyCriteria();
        return $sql;
    }

    function normalizeParameter($arrParam, $trim = false)
    {

		$termOfPayment = new TermOfPayment();
		
        $arrParam['currencyRate'] =  (isset($arrParam['currencyRate'])) ? $arrParam['currencyRate'] : 1;

        $arrParam = parent::normalizeParameter($arrParam);

        $transactionType = $arrParam['selDPType'];
        $hidRefKey = $arrParam['hidRefKey'];

		$rsTOP = $termOfPayment->getDataRowById($arrParam['selTermOfPaymentKey']);  
		if ($rsTOP[0]['duedays'] != 0){   
			for($i=0;$i<count( $arrParam['paymentMethodValue']);$i++){ 
				$arrParam['paymentMethodValue'][$i] = 0; 
				$arrParam['hidDetailPaymentKey'][$i] = 0;
				$arrParam['selVoucher'][$i] = 0;
			}
		}


        $arrParam['paymentMethodValue'] = (isset($arrParam['paymentMethodValue'])) ? $arrParam['paymentMethodValue'] : array();

        $reCountResult = $this->reCountGrandtotal($arrParam);
		 
        $arrParam['payment'] = $reCountResult['payment'];

        $arrParam['hidRefHeaderKey'] = (isset($arrParam['hidRefHeaderKey'])) ? $arrParam['hidRefHeaderKey'] : 0;
		
        return $arrParam;
    }

    function updateOutstanding($id)
    {

        $rs = $this->getDataRowById($id);

        try {

            if (!$this->oDbCon->startTrans())
                throw new Exception($this->errorMsg[100]);

            //            $arrObj = array();
            //            array_push($arrObj, new ARPayment());
            //            array_push($arrObj, new TruckingServiceOrderInvoice());

            //$sqlArr = array();

            $totalUsedAmount = $this->getUsedDPList($id);
            $totalUsedAmount = $totalUsedAmount['usedamount'];

            $totalDPUsedAmount = $this->getDPSettlementList($rs[0]['pkey']);
            $totalDPUsedAmount = $totalDPUsedAmount['usedamount'];

            $totalAmount = $totalUsedAmount + $totalDPUsedAmount;

            $statuskey = ($totalAmount >= $rs[0]['amount']) ? 3 : 2;

            $sql  = 'update ' . $this->tableName . ' set outstanding = amount - ' . $totalAmount . ' where statuskey in (2,3) and pkey = ' . $this->oDbCon->paramString($id);
            $this->oDbCon->execute($sql);

            if ($rs[0]['statuskey'] <> $statuskey)
                $this->changeStatus($id, $statuskey, '', false, true, true);

            $this->oDbCon->endTrans();
        } catch (Exception $e) {
            $this->oDbCon->rollback();
        }
    }


    function getDownpaymentForAR($arKey, $currencykey = '')
    {
        $ar = new AR();
        $arrAvailableDP = array();
        $currencyCriteria = (!empty($currencykey)) ? ' and ' . $ar->tableName . '.currencykey = ' . $this->oDbCon->paramString($currencykey) : '';

        if (!is_array($arKey))
            $arKey[0] = $arKey;

        $arKey = $this->oDbCon->paramString($arKey);

        $rsAR = (!empty($arKey)) ?  $ar->searchData('', '', true, ' and ' . $ar->tableName . '.pkey in (' . implode(',', $arKey) . ') ' . $currencyCriteria) : array();

        $arrTableType = array();
        // =============== INVOICE LIST
        // AR bisa dr sales invoice, trucking invoice atau yg lainnya
        // pisahkan key berdasarkan tipe asal AR
        foreach ($rsAR as $arRow) {
            $reftabletype = $arRow['reftabletype'];
            if (empty($reftabletype)) continue;

            if (!isset($arrTableType[$reftabletype]))
                $arrTableType[$reftabletype] = array();

            array_push($arrTableType[$reftabletype], $arRow['refkey']);
        }

        // =============== SALES ORDER LIST
        // $arrTableType =>trucking_service_order_invoice / sales_order_invoce
        // ex. 256 => trucking_service_order_invoice
        // ex. $arrTableType[256] = array(pkeyInvoice, pkeyInvoice); 
        // cari setiap sales order / trucking sales order berdasarkan invoice
        foreach ($arrTableType as $tabletype => $row) {
            $rsTemp = $this->getTableNameAndObjById($tabletype);
            $objName = $rsTemp['obj'];

            $salesOrderJob = $objName->getSalesOrderObj();
            $rsTemp = $this->getTableKeyAndObj($salesOrderJob->tableName);

            // pkey sales order ad di detail invoice 
            $rsSalesOrder = $objName->getDetailByColumn('', '', true, ' and refkey in (' . implode(',', $row) . ')');
            $arrSalesKey = array_column($rsSalesOrder, 'salesorderkey');
            //$arrSalesKey = $this->oDbCon->paramString($arrSalesKey);  
            //$rsSales = $salesOrderJob->searchData('','',true,' and pkey in ('.implode(',',$arrSalesKey).')');

            $rsDP =  $this->getAvailableDownpayment(array('refkey' => $arrSalesKey, 'reftabletype' => $rsTemp['key']));

            foreach ($rsDP as $dpRow)
                array_push($arrAvailableDP, $dpRow);
        }

        return $arrAvailableDP;
    }


    function getDownpaymentForTruckingServiceOrderInvoice($soKey, $currencykey = '')
    {

        $truckingServiceOrder = new TruckingServiceOrder();
        $arrAvailableDP = array();

        if (!is_array($soKey))
            $soKey[0] = $soKey;


        $rsTemp =   $this->getTableKeyAndObj($truckingServiceOrder->tableName);
        $rsDP =  $this->getAvailableDownpayment(array('refkey' => $soKey, 'reftabletype' => $rsTemp['key'], 'currencykey' => $currencykey));

        /*foreach($rsDP as $dpRow)  
           array_push($arrAvailableDP, $dpRow); 

        return $arrAvailableDP;*/

        return $rsDP;
    }

    function getTotalOutstanding($customerkey, $currencykey = CURRENCY['idr'], $refkey = ''){
        $sql = 'select 
                    coalesce(sum(outstanding),0) as totaldownpayment,
                    ' . $this->tableCurrency . '.name as currencyname
                from 
                    '.$this->tableName.'
                    left join ' . $this->tableCurrency . ' on ' . $this->tableName . '.currencykey = ' . $this->tableCurrency . '.pkey
                where
                    '.$this->tableName . '.customerkey = '.$this->oDbCon->paramString($customerkey).' and
                    '.$this->tableName . '.currencykey = '.$this->oDbCon->paramString($currencykey).' and
                    '.$this->tableName . '.outstanding > 0 and '.$this->tableName . '.statuskey in (1,2) 
                ';
        
        if(!empty($refkey))
            $sql .= ' and refkey = '.$this->oDbCon->paramString($refkey);
         
        $rs = $this->oDbCon->doQuery($sql);	
        
        return $rs;
    }
    
    function getAvailableDownpayment($salesOrderKey = array())
    {
        if (empty($salesOrderKey['refkey']))
            return array();

        $criteria = ' and ' . $this->tableName . '.statuskey = 2 ';

        if (!is_array($salesOrderKey['refkey']))
            $salesOrderKey[0] = $salesOrderKey['refkey'];

        $refkey = implode(',', $this->oDbCon->paramString($salesOrderKey['refkey']));

        $criteria .= ' and reftabletype = ' . $this->oDbCon->paramString($salesOrderKey['reftabletype']);
        if (!empty($refkey))
            $criteria .= ' and refkey in(' . $refkey . ')';

        if (!empty($salesOrderKey['currencykey']))
            $criteria .= ' and currencykey = ' . $this->oDbCon->paramString($salesOrderKey['currencykey']) . ' ';

        //$this->setLog($criteria);
        $rs = $this->searchData('', '', true, $criteria);
        return $rs;
    }

    function getUsedDPList($id)
    {

        $arrObj = array();

        if ($this->isActiveModule('ARPayment'))  array_push($arrObj, new ARPayment());
        if ($this->isActiveModule('TruckingServiceOrderInvoice')) array_push($arrObj, new TruckingServiceOrderInvoice());
        if ($this->isActiveModule('EMKLOrderInvoice')) array_push($arrObj, new EMKLOrderInvoice());
        if ($this->isActiveModule('SalesOrderProperty')) array_push($arrObj, new SalesOrderProperty());

        $sqlArr = array();

        $totalUsedAmount = 0;
        foreach ($arrObj as $obj) {
            $sql = ' select 
                                ' . $obj->tableName . '.code,
                                ' . $obj->tableName . '.trdate,
                                ' . $obj->tableDownpaymentDetail . '.downpaymentkey,
                                ' . $obj->tableDownpaymentDetail . '.amount
                            from 
                                ' . $obj->tableName . ',
                                ' . $obj->tableDownpaymentDetail . '
                            where 
                                ' . $obj->tableName . '.statuskey in (2,3) and
                                ' . $obj->tableName . '.pkey = ' . $obj->tableDownpaymentDetail . '.refkey and
                                downpaymentkey = ' . $this->oDbCon->paramString($id);

            array_push($sqlArr, $sql);
        }

        $sql = 'select * from (' . implode(' UNION ', $sqlArr) . ') downpayment';
        $rs = $this->oDbCon->doQuery($sql);

        foreach ($rs as $row)
            $totalUsedAmount += $row['amount'];

        $arrReturn = array();
        $arrReturn['usedamount'] = $totalUsedAmount;
        $arrReturn['history'] = $rs;

        return $arrReturn;
    }


    function getDPSettlementList($id)
    {
        // cari DP yg disettle tanpa transaksi

        $arrObj = array();
        array_push($arrObj, $this->getPaymentObj());

        $sqlArr = array();

        $totalUsedAmount = 0;
        foreach ($arrObj as $obj) {
            $sql = ' select 
                                ' . $obj->tableName . '.code,
                                ' . $obj->tableName . '.trdate,
                                ' . $obj->tableNameDetail . '.downpaymentkey,
                                ' . $obj->tableNameDetail . '.amount
                            from 
                                ' . $obj->tableName . ',
                                ' . $obj->tableNameDetail . '
                            where 
                                ' . $obj->tableName . '.statuskey in (2,3) and
                                ' . $obj->tableName . '.pkey = ' . $obj->tableNameDetail . '.refkey and
                                downpaymentkey = ' . $this->oDbCon->paramString($id);

            array_push($sqlArr, $sql);
        }

        $sql = 'select * from (' . implode(' UNION ', $sqlArr) . ') downpayment';
        $rs = $this->oDbCon->doQuery($sql);

        foreach ($rs as $row)
            $totalUsedAmount += $row['amount'];

        $arrReturn = array();
        $arrReturn['usedamount'] = $totalUsedAmount;
        $arrReturn['history'] = $rs;

        return $arrReturn;
    }

    function reCountGrandtotal($arrParam)
    {

        /*        $isPriceIncludeTax =  $arrParam['chkIncludeTax']; 
        $taxPercentage =  $this->unFormatNumber($arrParam['taxPercentage']);
        $prepaidTax23Percentage =  $this->unFormatNumber($arrParam['prepaidTax23Percentage']);*/

        //$subtotal = $this->unFormatNumber($arrParam['amount']); 

        /*            
        
        $beforeTaxTotal = $subtotal;
        
        if (!$isPriceIncludeTax){ 
            $taxValue = $subtotal * $taxPercentage / 100;
            $subtotal += $taxValue;
        } { 
            $taxValue = ($taxPercentage/(100 + $taxPercentage)) * $subtotal;   
            $beforeTaxTotal = $subtotal - $taxValue ;
        }
  
        $prepaidTax23 = $beforeTaxTotal * $prepaidTax23Percentage / 100; 
        $payment = $beforeTaxTotal + $taxValue - $prepaidTax23;*/

        $totalPayment = 0;
        $paymentDetail = $arrParam['paymentMethodValue'];
        for ($i = 0; $i < count($paymentDetail); $i++) {
            $totalPayment += $this->unFormatNumber($paymentDetail[$i]);
        }

        $reCountResult = array();
        /*        $reCountResult['beforeTaxTotal'] = $beforeTaxTotal; 
        $reCountResult['taxValue'] = $taxValue;  
        $reCountResult['subtotal'] = $subtotal;
        $reCountResult['prepaidTax23'] = $prepaidTax23;  */
        $reCountResult['payment'] = $totalPayment;


        return $reCountResult;
    }


    function getPaymentObj()
    {
        return  new CustomerDownpaymentSettlement();
    }

    function validateCancel($rsHeader, $autoChangeStatus = false)
    {
        $id = $rsHeader[0]['pkey'];
        $rsDP = $this->getUsedDPList($rsHeader[0]['pkey']);
        $rsDP = $rsDP['history'];

        if (!empty($rsDP))
            $this->addErrorLog(false, '<strong>' . $rsHeader[0]['code'] . '</strong> ' . $this->errorMsg[201] . '<br><strong>' . $rsDP[0]['code'] . '</strong>, ' . $this->errorMsg[225]);

        //cek downpayment yang sudah di lakukan pelunasan 
        $rsDPSettlement = $this->getDPSettlementList($rsHeader[0]['pkey']);
        $rsDPSettlement = $rsDPSettlement['history'];
        if (!empty($rsDPSettlement))
            $this->addErrorLog(false, '<strong>' . $rsHeader[0]['code'] . '</strong>. ' . $this->errorMsg[201] . '<br><strong>' . $rsDPSettlement[0]['code'] . '</strong> ' . $this->errorMsg[225]);

        $ar = new AR();
        $rsARKey = $ar->getTableKeyAndObj($this->tableName);
        $rsAR = $ar->searchData('', '', true, ' and reftabletype = ' . $this->oDbCon->paramString($rsARKey['key']) . ' and refkey = ' . $this->oDbCon->paramString($id) . ' and (' . $ar->tableName . '.statuskey  in (2,3))');
        if (!empty($rsAR))
            $this->addErrorLog(false, '<strong>' . $rsHeader[0]['code'] . '</strong>. ' . $this->errorMsg[201] . ' ' . $this->errorMsg['ar'][2]);
    }
}
?>
