<?php

class LogisticSalesOrder extends BaseClass{
	
    function __construct()
    {

        parent::__construct();

        $this->tableName = 'logistic_sales_order_header';
        $this->tableNameDetail = 'logistic_sales_order_detail';
        $this->tableCustomer = 'customer';
        $this->tableCity = 'city';
        $this->tableUnit = 'item_unit';
        $this->tableStatus = 'transaction_status';
        $this->tablePayment = 'logistic_sales_order_payment';
        $this->tableWarehouse = 'warehouse';
        $this->tableTermOfPayment = 'term_of_payment'; 
        $this->tableTransportation = 'transportation';
        $this->overwriteContractSecurityObject = 'overwriteContract';

        $this->isTransaction = true;
        $this->newLoad = true;

        $this->securityObject = 'LogisticSalesOrder'; //sementara transaction_status


        $this->arrPaymentDetail = array();
        $this->arrPaymentDetail['pkey'] = array('hidDetailPaymentKey');
        $this->arrPaymentDetail['refkey'] = array('pkey', 'ref');
        $this->arrPaymentDetail['amount'] = array('paymentMethodValue', array('datatype' => 'number', 'mandatory' => true));
        $this->arrPaymentDetail['paymentkey'] = array('selPaymentMethod', array('mandatory' => true));


        $arrDetailLogistic = array();
        $arrDetailLogistic['pkey'] = array('hidDetailKey');
        $arrDetailLogistic['refkey'] = array('pkey', 'ref');
        $arrDetailLogistic['description'] =  array('detailDescription');
        $arrDetailLogistic['weight'] =  array('detailWeight', 'number');
        $arrDetailLogistic['length'] =  array('detailLength', 'number');
        $arrDetailLogistic['width'] =  array('detailWidth', 'number');
        $arrDetailLogistic['height'] =  array('detailHeight', 'number');
        $arrDetailLogistic['cbmweight'] = array('detailCBMWeight', 'number');
        $arrDetailLogistic['finalweight'] = array('detailFinalWeight', 'number'); 
        $arrDetailLogistic['priceinunit'] = array('priceInUnit', 'number');
        $arrDetailLogistic['subtotal'] = array('detailSubtotal', 'number');

        $arrDetails = array();
        array_push($arrDetails, array('dataset' => $arrDetailLogistic, 'tableName' => $this->tableNameDetail));
        array_push($arrDetails, array('dataset' => $this->arrPaymentDetail, 'tableName' => $this->tablePayment));

        $this->arrData = array();
        $this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails));
        $this->arrData['code'] = array('code');
        $this->arrData['trdate'] = array('trDate', 'date');
        $this->arrData['termofpaymentkey'] = array('selTermOfPaymentKey');
        $this->arrData['trdesc'] = array('trDesc');
        $this->arrData['subtotal'] = array('subtotal', 'number');
        $this->arrData['finaldiscounttype'] = array('selFinalDiscountType', 'number');
        $this->arrData['finaldiscount'] = array('finalDiscount', 'number');
        $this->arrData['beforetaxtotal'] = array('beforeTaxTotal', 'number');
        $this->arrData['taxpercentage'] = array('taxPercentage', 'number');
        $this->arrData['taxvalue'] = array('taxValue', 'number');
        $this->arrData['etccost'] = array('etcCost', 'number');
        $this->arrData['grandtotal'] = array('grandTotal', 'number');
        $this->arrData['totalpayment'] = array('totalPayment', 'number');
        $this->arrData['balance'] = array('balance', 'number');
        $this->arrData['statuskey'] = array('selStatus');
        $this->arrData['recipientkey'] = array('hidRecipientKey');
        $this->arrData['useinsurance'] = array('useInsurance');
        $this->arrData['senderkey'] = array('hidSenderKey');
        $this->arrData['ispriceincludetax'] = array('chkIncludeTax');
        $this->arrData['totalweight'] = array('totalWeight', 'number');
        $this->arrData['totalqty'] = array('totalQty', 'number');
        $this->arrData['warehousekey'] = array('selWarehouseKey');
        $this->arrData['price'] = array('price', 'number');
        $this->arrData['transportationkey'] = array('selTransportation');
        $this->arrData['packingfee'] = array('packingFee', 'number');
        $this->arrData['itemdescription'] = array('goodsDescription');
        $this->arrData['sendercitykey'] = array('hidSenderCityKey');
        $this->arrData['recipientcitykey'] = array('hidRecipientCityKey');
        $this->arrData['verificationcode'] = array('verificationCode'); 
        $this->arrData['courier'] = array('courier');
        $this->arrData['senderaddress'] = array('senderAddress');
        $this->arrData['sendername'] = array('senderName');
        $this->arrData['senderphone'] = array('senderPhone');
        $this->arrData['recipientaddress'] = array('recipientAddress');
        $this->arrData['recipientname'] = array('recipientName');
        $this->arrData['recipientphone'] = array('recipientPhone');

        $this->printMenu = array();
        array_push($this->printMenu, array('code' => 'printOriginal', 'name' => $this->lang['printConnote']. ' (Original)',  'icon' => 'print', 'url' => 'print/logisticSalesOrder/?original=1'));
        array_push($this->printMenu, array('code' => 'printConnote', 'name' => $this->lang['printConnote'],  'icon' => 'print', 'url' => 'print/logisticSalesOrder/?copy=1'));
//        array_push($this->printMenu, array('code' => 'printConnote', 'name' => $this->lang['printConnote'],  'icon' => 'print', 'url' => 'print/logisticSalesOrder/'));
		array_push($this->printMenu, array('code' => 'printCopy', 'name' => $this->lang['printConnote']. ' (Copy)',  'icon' => 'print', 'url' => 'print/logisticSalesOrder/?triplay=1'));
//		array_push($this->printMenu, array('code' => 'printCopy', 'name' => $this->lang['printConnote']. ' (Copy)',  'icon' => 'print', 'url' => 'print/logisticSalesOrder/?copy=1'));
     	array_push($this->printMenu, array('code' => 'printLabel', 'name' => $this->lang['printLabel'],  'icon' => 'print', 'url' => 'print/logisticShippingLabel'));
 
	 	$this->arrDataListAvailableColumn = array();
        array_push($this->arrDataListAvailableColumn, array('code' => 'code', 'title' => 'code', 'dbfield' => 'code', 'default' => true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'date', 'title' => 'date', 'dbfield' => 'trdate', 'default' => true, 'width' => 100, 'align' => 'center', 'format' => 'date'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'warehouse', 'title' => 'warehouse', 'dbfield' => 'warehousename', 'default' => true,  'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'transportation', 'title' => 'type', 'dbfield' => 'transportationname', 'default' => true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'bale', 'title' => 'bale', 'dbfield' => 'totalqty', 'width' => 60, 'align' => 'right', 'default' => true, 'format' => 'number'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'weight', 'title' => 'weight', 'dbfield' => 'totalweight', 'width' => 60, 'align' => 'right', 'default' => true, 'format' => 'number'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'sendername', 'title' => 'senderName', 'dbfield' => 'sendername', 'default' => true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'recipientname', 'title' => 'recipientName', 'dbfield' => 'recipientname', 'default' => true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'total', 'title' => 'total', 'dbfield' => 'grandtotal', 'default' => true, 'width' => 100, 'align' => 'right', 'format' => 'number'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status', 'title' => 'status', 'dbfield' => 'statusname', 'default' => true, 'width' => 90));


        $this->arrSearchColumn = array();
        array_push($this->arrSearchColumn, array('Kode', $this->tableName . '.code'));
        array_push($this->arrSearchColumn, array('Nama Pengirim', $this->tableName . '.sendername'));
        array_push($this->arrSearchColumn, array('Nama Penerima', $this->tableName . '.recipientname'));
        array_push($this->arrSearchColumn, array('Total',  $this->tableName . '.grandtotal'));
        array_push($this->arrSearchColumn, array('Gudang',  $this->tableWarehouse . '.name'));
        array_push($this->arrSearchColumn, array('Transportasi',  $this->tableTransportation . '.name'));

        $this->includeClassDependencies(array(
            'Warehouse.class.php',
            'Customer.class.php',
            'TermOfPayment.class.php',
            'CashMovement.class.php',
            'City.class.php',
            'PaymentMethod.class.php',
            'COALink.class.php',
            'Customer.class.php',
            'Downpayment.class.php',
            'GeneralJournal.class.php',
            'LogisticSalesOrderManifest.class.php',
            'CustomerDownpayment.class.php',
            'PaymentConfirmation.class.php',
            'ShippingRate.class.php' 
        ));

        $this->overwriteConfig();
    }



    function getQuery() {
 
        $sql = '
                 select
                     ' . $this->tableName . '.*,
					 sendercity.name as sendercityname,
					 recipientcity.name as recipientcityname,
                     ' . $this->tableWarehouse . '.name as warehousename,
                     '.$this->tableTermOfPayment.'.name as termofpaymentname,
                     ' . $this->tableTransportation . '.name as transportationname,
                     ' . $this->tableStatus . '.status as statusname 
                 from 
                     ' . $this->tableName . '  
                    left join ' . $this->tableTransportation . ' on ' . $this->tableName . '.transportationkey = ' . $this->tableTransportation . '.pkey
				    left join ' . $this->tableCity . '  sendercity on ' . $this->tableName . '.sendercitykey = sendercity.pkey
				    left join ' . $this->tableCity . ' recipientcity on  ' . $this->tableName . '.recipientcitykey = recipientcity.pkey
                    left join '.$this->tableTermOfPayment.' on '.$this->tableName.'.termofpaymentkey = '.$this->tableTermOfPayment.'.pkey,                   
                    ' . $this->tableWarehouse . ',
                    ' . $this->tableStatus . '
                 where 
                    ' . $this->tableName . '.warehousekey = ' . $this->tableWarehouse . '.pkey  and
                     ' . $this->tableName . '.statuskey = ' . $this->tableStatus . '.pkey '
            . $this->criteria;


        $sql .=  $this->getCompanyCriteria();
        $sql .=  $this->getWarehouseCriteria();

        return $sql;
    }

    function validateForm($arr, $pkey = '') {

         $security = new Security();
        $overwriteSellingPriceAllowed = $security->isAdminLogin($this->sellingPriceSecurityObject,10); 
                
	$arrayToJs = parent::validateForm($arr, $pkey);
        
        
        $recipientAddress = $arr['recipientAddress'];
        $senderAddress = $arr['senderAddress'];

//        if (empty($arr['hidRecipientKey'])) 
//            $this->addErrorList($arrayToJs, false, $this->errorMsg['recipient'][1]);
//   
//        if (empty($arr['hidSenderKey'])) 
//            $this->addErrorList($arrayToJs, false, $this->errorMsg['sender'][1]);

        if (empty($arr['hidSenderCityKey'])) {
            $this->addErrorList($arrayToJs, false, $this->errorMsg['city'][1]);
        }
        if (empty($arr['hidRecipientCityKey'])) {
            $this->addErrorList($arrayToJs, false, $this->errorMsg['city'][1]);
        }
		
	 	if (empty($arr['senderPhone'])) {
            $this->addErrorList($arrayToJs, false, $this->errorMsg['phone'][1]);
        }	
		
		if (empty($arr['recipientPhone'])) {
            $this->addErrorList($arrayToJs, false, $this->errorMsg['phone'][1]);
        }		
        
        /*if(strlen(trim($recipientAddress)) > 65 || strlen(trim($senderAddress)) > 65){
            $this->addErrorList($arrayToJs, false, $this->errorMsg['address'][2]);
        }*/


      /*  if ($arr['hidSenderKey'] == $arr['hidRecipientKey']) {
            $this->addErrorList($arrayToJs, false, $this->errorMsg['salesOrder'][3]);
        }
*/
  
        for ($i = 0; $i < count($arr['hidDetailKey']); $i++) { 
            if ( $arr['detailFinalWeight'][$i] <= 0 ) 
				  $this->addErrorList($arrayToJs, false, $this->errorMsg['weight'][1]); 
  if ( $arr['detailSubtotal'][$i] <= 0 ) 
				  $this->addErrorList($arrayToJs, false, $this->errorMsg['price'][1]); 
               
        }
		
        return $arrayToJs;
    }


    function validateConfirm($rsHeader){

        $id = $rsHeader[0]['pkey'];

        $rsPayment = $this->getPaymentMethodDetail($id);

        $termOfPayment = new TermOfPayment();
        $customer = new Customer();
        $shippingRate = new ShippingRate();
        $coaLink = new COALink();
        $rsTOP = $termOfPayment->getDataRowById($rsHeader[0]['termofpaymentkey']);
        $isCash = ($rsTOP[0]['duedays'] == 0) ? true : false;

        $totalPayment = 0;
        for ($i = 0; $i < count($rsPayment); $i++)
            $totalPayment += $rsPayment[$i]['amount'];


        $balance = $totalPayment - $rsHeader[0]['grandtotal'];

        if ($isCash) {
            $thresholdDiscount = abs($this->loadSetting('roundedPaymentThreshold'));
            if ($balance < ($thresholdDiscount * -1))
                $this->addErrorLog(false, '<strong>' . $rsHeader[0]['code'] . '</strong>. ' . $this->errorMsg[502]);
            else if ($balance > $thresholdDiscount)
                $this->addErrorLog(false, '<strong>' . $rsHeader[0]['code'] . '</strong>. ' . $this->errorMsg[509]);
        } else {

            //validasi creditlimit
            $customer = new Customer();
            $senderKey = $rsHeader[0]['senderkey'];
            $rsSender = $customer->getDataRowById($senderKey);

            if ($rsSender[0]['creditlimit'] > 0) {
                $total = $this->unFormatNumber($rsHeader[0]['grandtotal']);
                if ($customer->willExceedCreditLimit($senderKey, $total)) {
                    $this->addErrorLog(false, '<strong>' . $rsHeader[0]['code'] . '</strong>. ' . $this->errorMsg['creditlimit'][1]);
                }
            }
        }



        /*   if (USE_GL){
                $arrCOA = array();
                array_push($arrCOA,  'taxout', 'otherrevenue' , 'salesservicediscount'); 
                for ($i=0;$i<count($arrCOA);$i++){
                    $rsCOA = $coaLink->getCOALink ($arrCOA[$i], $warehouse->tableName,$rsHeader[0]['warehousekey'], 0); 
                    if (empty($rsCOA))	
                        $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$arrCOA[$i]. ' ' .$this->errorMsg['coa'][3]);
                }   

                if ($isCash){
                    for($i=0;$i<count($rsPayment); $i++){ 
                        if ($rsPayment[$i]['amount'] > 0 ){ 
                            $rsCOA = $coaLink->getCOALink ('payment', $warehouse->tableName,$rsHeader[0]['warehousekey'], $rsPayment[$i]['paymentkey']); 
                            if (empty($rsCOA))	
                                $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg['coa'][3]);
                        }
                    } 
                }else{ 
                        // validasi COA piutang  
                        $rsCOA = $coaLink->getCOALink ('ar', $warehouse->tableName,$rsHeader[0]['warehousekey'], 0); 
                        if (empty($rsCOA))	
                            $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg['coa'][3]);
                }
 
         } */
    }

    function confirmTrans($rsHeader)
    {

        $id = $rsHeader[0]['pkey'];

        $coaLink = new COALink();
        $warehouse = new Warehouse();


        $termOfPayment = new TermOfPayment();
        $rsTOP = $termOfPayment->getDataRowById($rsHeader[0]['termofpaymentkey']);

        $isCash = ($rsTOP[0]['duedays'] == 0) ? true : false;
        $customerKey = ($rsTOP[0]['iscod'] == 0) ? $rsHeader[0]['senderkey'] : $rsHeader[0]['recipientkey'];

        $refTableKey = $this->getTableKeyAndObj($this->tableName, array('key'))['key'];

        // MENGHITUNG PAYMENT wajib
        if ($isCash) {
            $rsPayment = $this->getPaymentMethodDetail($id);
            $cashMovement = new CashMovement();

            for ($i = 0; $i < count($rsPayment); $i++) {
                if (USE_GL) {
                    $rsPaymentCOA = $coaLink->getCOALink('payment', $warehouse->tableName, $rsHeader[0]['warehousekey'], $rsPayment[$i]['paymentkey']);
                    $coakey = $rsPaymentCOA[0]['coakey'];
                } else {
                    $coakey = $rsPayment[$i]['paymentkey'];
                }

                $cashMovement->updateCashMovement($id, $coakey, $rsPayment[$i]['amount'], $this->tableName, $rsHeader[0]['warehousekey'], $notecash, $rsHeader[0]['trdate']);
            }
            $this->updatePaidStatus($id, 3);
        } else {
            if ($this->isActiveModule('AR')) {
                //update AR
                $ar = new AR();

                $arrParam = array();

                $arrParam['code'] = 'xxxxx';
                $arrParam['hidCustomerKey'] = $customerKey;
                $arrParam['hidRefKey'] = $id;
                $arrParam['hidRefHeaderKey'] = $id;
                $arrParam['hidRefCode'] =  $rsHeader[0]['code'];
                $arrParam['hidRefTable'] = $refTableKey;
                $arrParam['hidRefDate'] =   $this->formatDBDate($rsHeader[0]['trdate'], 'd / m / Y');
                $arrParam['selWarehouse'] = $rsHeader[0]['warehousekey'];
                $arrParam['selARType'] = 1;
                $arrParam['amount'] = abs($rsHeader[0]['grandtotal']);
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
        }
        // END       

        $this->updateGL($rsHeader);
    }

    function updatePaidStatus($pkey, $paidStatus)
    {
        $sql = 'update ' . $this->tableName . ' set paidstatuskey = ' . $this->oDbCon->paramString($paidStatus) . ' where pkey = ' . $this->oDbCon->paramString($pkey);
        $this->oDbCon->execute($sql);
    }

    function validateBackConfirm($rsHeader){ 
           
        $id = $rsHeader[0]['pkey'];
		$rs  = $this->getDataRowById($id);
		$this->addErrorLog(false,'<strong>'.$rs[0]['code'].'</strong>. ' . $this->errorMsg[201].' ' . $this->errorMsg['logisticSalesOrder'][3] );
		 
    } 
	
    function validateClose($rsHeader){ 
           
        $id = $rsHeader[0]['pkey'];
		$rs  = $this->getDataRowById($id);
		$this->addErrorLog(false,'<strong>'.$rs[0]['code'].'</strong>. ' . $this->errorMsg[201].' ' . $this->errorMsg['logisticSalesOrder'][4] );
		 
    } 
	
    function validateCancel($rsHeader, $autoChangeStatus = false) {
        $id = $rsHeader[0]['pkey'];
        $isActive = $this->isActiveModule(array('AR'));

        if ($isActive['ar']) {
            //cek ad AR terbayar 
            $ar = new AR();
            $rsARKey = $ar->getTableKeyAndObj($this->tableName);
            $rsAR = $ar->searchData('', '', true, ' and reftabletype = ' . $this->oDbCon->paramString($rsARKey['key']) . ' and refkey = ' . $this->oDbCon->paramString($id) . ' and (' . $ar->tableName . '.statuskey  in (2,3))');
            if (!empty($rsAR))
                $this->addErrorLog(false, '<strong>' . $rsHeader[0]['code'] . '</strong>. ' . $this->errorMsg[201] . ' ' . $this->errorMsg['ar'][2]);
        }
 
		$logisticSalesOrderManifest = new LogisticSalesOrderManifest();
		$rsManifest = $logisticSalesOrderManifest->getManifest($id,' and '.$logisticSalesOrderManifest->tableName.'.statuskey in (2,3) ');
		if(!empty($rsManifest)) 
			$this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' . $this->errorMsg[201].'<br><strong>'.$rsManifest[0]['code'].'</strong>. ' .$this->errorMsg[203],true);
             
    }

    function cancelTrans($rsHeader, $copy)
    {
        $id = $rsHeader[0]['pkey'];

        $isActive = $this->isActiveModule(array('AR'));

        if ($isActive['ar']) {
            $ar = new AR();
            $rsARKey = $ar->getTableKeyAndObj($this->tableName);
            $rsAR = $ar->searchData('', '', true, ' and reftabletype = ' . $this->oDbCon->paramString($rsARKey['key']) . ' and refkey = ' . $this->oDbCon->paramString($id) . ' and ' . $ar->tableName . '.statuskey = 1');
            for ($i = 0; $i < count($rsAR); $i++) {
                $arrayToJs = $ar->changeStatus($rsAR[$i]['pkey'], TRANSACTION_STATUS['batal'], '', false, true);
                if (!$arrayToJs[0]['valid'])
                    throw new Exception('<strong>' . $rsHeader[0]['code'] . '</strong>. ' .  $arrayToJs[0]['message']);
            }
        }

         // gk perlu, validasi di manifestnya saja 
//        $logisticSalesOrderManifest = new LogisticSalesOrderManifest(); 
//		$rsManifest = $logisticSalesOrderManifest->getManifest($id,' and '.$logisticSalesOrderManifest->tableName.'.statuskey = 1');
//		for($i=0;$i<count($rsManifest);$i++) { 
//			$arrayToJs = $logisticSalesOrderManifest->changeStatus($rsManifest[$i]['pkey'],4,'',false, true);
//            if (!$arrayToJs[0]['valid'])
//                throw new Exception('<strong>'.$rsHeader[0]['code'] . '</strong>. '.  $arrayToJs[0]['message']);    
//        }

        if ($copy)
            $this->copyDataOnCancel($id);

        $this->cancelGLByRefkey($rsHeader[0]['pkey'], $this->tableName);
    }

    function updateGL($rs)
    {
        if (!USE_GL) return;

        $warehouse = new Warehouse();
        $coaLink = new COALink();
        $generalJournal = new GeneralJournal();
        $customer = new Customer();

        $warehousekey = $rs[0]['warehousekey'];

        $omitSalesDiscountGL = $this->loadSetting('omitSalesDiscountGL');

        $rsKey = $generalJournal->getTableKeyAndObj($this->tableName);
        $arr = array();
        $arr['pkey'] = $generalJournal->getNextKey($generalJournal->tableName);
        $arr['code'] = 'xxxxx';
        $arr['refkey'] = $rs[0]['pkey'];
        $arr['refTableType'] = $rsKey['key'];
        $arr['trDate'] = $this->formatDBDate($rs[0]['trdate'], 'd / m / Y');
        $arr['createdBy'] = 0;
        $arr['selWarehouseKey'] = $rs[0]['warehousekey'];

        $rsCustomer = $customer->getDataRowById($rs[0]['senderkey']);
		$desc = array();
        array_push($desc,$this->ucFirst($this->lang['sender']) . ' ' . $rsCustomer[0]['name'] ); 
        array_push($desc,$this->ucFirst($this->lang['bale']).' : '.$this->formatNumber($rs[0]['totalqty']));
        array_push($desc,$this->ucFirst($this->lang['weight']).' : '.$this->formatNumber($rs[0]['totalweight']).' KG'); 
		$arr['trDesc'] = implode(chr(13),$desc);  
		
        $temp = -1;

        $totalDisc = 0;
        
        //nilai pakai subtotal karena udah di hitung ulang di recount 
        $rsCOA = $coaLink->getCOALink('salesservice', $warehouse->tableName, $warehousekey, 0);
        $temp++;
        $arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];
        $arr['debit'][$temp] = 0;
        $arr['credit'][$temp] = $rs[0]['subtotal'];
        
        $termOfPayment = new TermOfPayment();
        $rsTOP = $termOfPayment->getDataRowById($rs[0]['termofpaymentkey']);
        $isCash = ($rsTOP[0]['duedays'] == 0) ? true : false;

        if ($isCash) {
            $rsPayment = $this->getPaymentMethodDetail($rs[0]['pkey']);
            for ($i = 0; $i < count($rsPayment); $i++) {
                $rsCOA = $coaLink->getCOALink('payment', $warehouse->tableName, $warehousekey, $rsPayment[$i]['paymentkey']);
                $temp++;
                $arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];
                $arr['debit'][$temp] = $rsPayment[$i]['amount'];
                $arr['credit'][$temp] = 0;
            }


            //selisih pembayaran   
            $temp++;
            if ($rs[0]['balance'] < 0) {
                $rsCOA = $coaLink->getCOALink('othercost', $warehouse->tableName, $warehousekey, 0);
                $arr['debit'][$temp] = abs($rs[0]['balance']);
                $arr['credit'][$temp] = 0;
            } else {
                $rsCOA = $coaLink->getCOALink('otherrevenue', $warehouse->tableName, $warehousekey, 0);
                $arr['debit'][$temp] = 0;
                $arr['credit'][$temp] = abs($rs[0]['balance']);
            }
            $arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];
        } else {
            //akun piutang  
            $temp++;
            $arr['hidCOAKey'][$temp] = $customer->getARCOAKey($rs[0]['senderkey'], $warehousekey);
            $arr['debit'][$temp] = $rs[0]['grandtotal'];
            $arr['credit'][$temp] = 0;
        }

        /*$rsCOA = $coaLink->getCOALink('salesservicediscount', $warehouse->tableName, $warehousekey, 0);
        $temp++;
        $arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];
        $arr['debit'][$temp] = $totalDisc;
        $arr['credit'][$temp] = 0;*/

        /*$rsCOA = $coaLink->getCOALink('taxout', $warehouse->tableName, $warehousekey, 0);
        $temp++;
        $arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];
        $arr['debit'][$temp] = 0;
        $arr['credit'][$temp] = $rs[0]['taxvalue'];*/

        $rsCOA = $coaLink->getCOALink('otherrevenue', $warehouse->tableName, $warehousekey, 0);
        $temp++;
        $arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];
        $arr['debit'][$temp] = 0;
        $arr['credit'][$temp] = $rs[0]['packingfee'] + $rs[0]['etccost'];

        $arrayToJs = $generalJournal->addData($arr);


        if (!$arrayToJs[0]['valid'])
            throw new Exception('<strong>' . $rs[0]['code'] . '</strong>. ' . $this->errorMsg[504] . ' ' . $arrayToJs[0]['message']);
    }

    function getDetailWithRelatedInformation($pkey, $criteria = '', $orderby = '')
    {
        $sql = '
            select
                ' . $this->tableNameDetail . '.*
            from 
                ' . $this->tableNameDetail . '
            where  		 
            ' . $this->tableNameDetail . '.refkey in (' . $this->oDbCon->paramString($pkey, ',') . ') ';
        return $this->oDbCon->doQuery($sql);

        $sql .= $criteria;

        $sql .= ' ' . $orderby;

        return $this->oDbCon->doQuery($sql);
    }
     
    function calculateTotalShippingPrice($senderCityKey, $recipientCityKey, $transportationkey, $weightDetail, $totalWeight)   {
        $shippingRate = new ShippingRate();
 
        $critriaShippingRate =  ' and ' . $shippingRate->tableName . '.statuskey = 1
								  and ' .  $shippingRate->tableName . '.fromcitykey=' . $this->oDbCon->paramString($senderCityKey) .' 
								  and ' . $shippingRate->tableName . '.destinationcitykey=' . $this->oDbCon->paramString($recipientCityKey);
		
        $rsShippingRateKey = $shippingRate->searchDataRow(array($shippingRate->tableName . '.pkey'), $critriaShippingRate);
        
		$rsShippingRateDetail = $shippingRate->getDetailWithRelatedInformation($rsShippingRateKey[0]['pkey'], ' and transportationkey=' . $this->oDbCon->paramString($transportationkey));
 
		if(empty($rsShippingRateDetail)) return 0;
			
        $firstPrice = $rsShippingRateDetail[0]['firstfee'];
        $nextPrice = $rsShippingRateDetail[0]['nextfee'];
  		
		// idealnya bkn seperti ini
		$price = ($totalWeight == 1) ? $firstPrice : $nextPrice;		
				
        $arrResult =  array('priceInUnit' => $price, 'total' => ( $price * $weightDetail )) ;
		
		return $arrResult;
	}

  
    function reCountSubtotal($arrParam)
    {
        $isPriceIncludeTax = (!empty($arrParam['chkIncludeTax'])) ? 1 : 0;

        $senderCityKey = $arrParam['hidSenderCityKey'];
        $recipientCityKey = $arrParam['hidRecipientCityKey'];
        $selTransportation = $arrParam['selTransportation'];

        $transportationDivision = $this->getTransportationType($arrParam['selTransportation']);
 
        $grandtotal = 0;

        $packingFee = $this->unFormatNumber($arrParam['packingFee']);
        $taxValue = $this->unFormatNumber($arrParam['taxValue']);
        $finalDiscount = $this->unFormatNumber($arrParam['finalDiscount']);
        $finalDiscountType = $arrParam['selFinalDiscountType'];
        $taxPercentage = $this->unFormatNumber($arrParam['taxPercentage']);
        $etcCost = $this->unFormatNumber($arrParam['etcCost']); 

        
        $security = new Security();
        $overwriteContractAllowed = $security->isAdminLogin($this->overwriteContractSecurityObject,10); 
 
        //perhitungan detail di sini 
        $totalPrice = 0;
		$totalWeight = 0;
		
        for ($i = 0; $i < count($arrParam['hidDetailKey']); $i++) {
 
            $detailLength = $this->unFormatNumber($arrParam['detailLength'][$i]);
            $detailWidth = $this->unFormatNumber($arrParam['detailWidth'][$i]);
            $detailHeight = $this->unFormatNumber($arrParam['detailHeight'][$i]);

            $weight = $this->unFormatNumber($arrParam['detailWeight'][$i]);
			 
            $weightCMB = ($detailLength * $detailWidth * $detailHeight) / $transportationDivision[0]['division'];
 
			// harusnya tergantung, udara laut, besar mana
            if ($weightCMB  > $weight)
                $weight = $weightCMB;

			// pembulatan ke atas
			$weight = ceil($weight); 
            $totalWeight += $weight;
			
            $arrParam['detailCBMWeight'][$i] = $weightCMB;
			$arrParam['detailFinalWeight'][$i] = $weight; 
		}
		
		// dipish kaarena perlu hitung totalny dulu
        for ($i = 0; $i < count($arrParam['hidDetailKey']); $i++) {
			
			$weight = $arrParam['detailFinalWeight'][$i];
				
           if(!$overwriteContractAllowed){
				
                $arrPrice = $this->calculateTotalShippingPrice($senderCityKey, $recipientCityKey,  $selTransportation, $weight, $totalWeight); 
                $priceInUnit = $arrPrice['priceInUnit'];
                $priceTotal = $arrPrice['total'];
            }else{
                $priceInUnit = $this->unFormatNumber($arrParam['detailSubtotal'][$i]);
                $priceTotal = $this->unFormatNumber($arrParam['detailSubtotal'][$i]);
            }
            
			$arrParam['priceInUnit'][$i] = $priceInUnit;
            $arrParam['detailSubtotal'][$i] = $priceTotal;
                
            $totalPrice += $priceTotal;
			  
        }

		
        $subtotal = $totalPrice;

        $grandtotal = $subtotal;

        if ($finalDiscount != 0) {
            if ($finalDiscountType == 2)
                $finalDiscount = $finalDiscount / 100 * $grandtotal;
        }


        $totalFinalDiscountAndPointValue = $finalDiscount;

        $beforeTaxTotal = $subtotal - $totalFinalDiscountAndPointValue;
        $grandtotal = $beforeTaxTotal;

        if ($isPriceIncludeTax == false) {
            $taxValue = $beforeTaxTotal * $taxPercentage / 100;
            $taxValue = round($taxValue); // kalo ad koma, nilainya gantung di AR nanti
            $grandtotal += $taxValue;
        } else {
            $taxValue = ($taxPercentage / (100 + $taxPercentage)) * $grandtotal;
            $beforeTaxTotal = $grandtotal - $taxValue;
        }

        $grandtotal += $etcCost + $packingFee;
 
        $balance = 0;
        $totalPayment = 0;

        $termOfPayment = new TermOfPayment();
        $rsTOP = $termOfPayment->getDataRowById($arrParam['selTermOfPaymentKey']);
        if ($rsTOP[0]['duedays'] == 0) {
            $payment = $arrParam['paymentMethodValue'];
            for ($i = 0; $i < count($payment); $i++) {
                $totalPayment += $this->unFormatNumber($payment[$i]);
            }
        }


        $balance = $totalPayment - $grandtotal;

		
        $reCountResult = array();
        $reCountResult['subtotal'] = $subtotal;
        $reCountResult['beforeTaxTotal'] = $beforeTaxTotal;
        $reCountResult['isPriceIncludeTax'] = $isPriceIncludeTax;
        $reCountResult['taxValue'] = $taxValue;
        $reCountResult['grandtotal'] = $grandtotal;
        $reCountResult['totalPayment'] = $totalPayment;
        $reCountResult['balance'] = $balance;
        $reCountResult['totalQty'] = count($arrParam['hidDetailKey']);
        $reCountResult['totalWeight'] = $totalWeight;
		
		
        $reCountResult['detailCBMWeight'] = $arrParam['detailCBMWeight'];
        $reCountResult['detailFinalWeight'] = $arrParam['detailFinalWeight'];
        $reCountResult['priceInUnit'] = $arrParam['priceInUnit'];
        $reCountResult['detailSubtotal'] = $arrParam['detailSubtotal'];

        return $reCountResult;
    }

    

	function normalizeParameter($arrParam, $trim = false){
  
		if(empty($arrParam['hidId']))
        	$arrParam['verificationCode'] = $this->generateStrongPassword(6, '', 'lud');

		
		$customer = new Customer();
		$rsCustomer = $customer->autoUpdateData(array('field' => 'phone', 'value' => $arrParam['senderPhone']),
								  array(
								  	'name' => $arrParam['senderName'],
								  	'phone' => $arrParam['senderPhone'],
								  	'mobile' => '',
								  	'address' => $arrParam['senderAddress'],
									'statuskey' => 2, // sementara tembak dulu
									'citykey' => $arrParam['hidSenderCityKey']
								  )	
								 );
		  
        $arrParam['hidSenderKey'] = $rsCustomer['pkey']; // isi ulang di pkey
		
		
		
		$rsRecipient = $customer->autoUpdateData(array('field' => 'phone', 'value' => $arrParam['recipientPhone']),
								  array(
								  	'name' => $arrParam['recipientName'],
								  	'phone' => $arrParam['recipientPhone'],
								  	'mobile' => '',
								  	'address' => $arrParam['recipientAddress'],
									'statuskey' => 2, // sementara tembak dulu
									'citykey' => $arrParam['hidRecipientCityKey']
								  )	
								 );
		   
        $arrParam['hidRecipientKey'] = $rsRecipient['pkey']; // isi ulang di pkey
     

        $recount = $this->reCountSubtotal($arrParam); 
		
        $arrParam['subtotal'] = $recount['subtotal'];
        $arrParam['beforeTaxTotal'] = $recount['beforeTaxTotal'];
        $arrParam['isPriceIncludeTax'] = $recount['isPriceIncludeTax'];
        $arrParam['taxValue'] = $recount['taxValue'];
        $arrParam['grandTotal'] = $recount['grandtotal'];
        $arrParam['totalPayment'] = $recount['totalPayment'];
        $arrParam['balance'] = $recount['balance'];
        $arrParam['totalQty'] = $recount['totalQty']; 
        $arrParam['totalWeight'] = $recount['totalWeight'];
		
		// detail
        $arrParam['detailCBMWeight'] = $recount['detailCBMWeight'];
        $arrParam['detailFinalWeight'] = $recount['detailFinalWeight']; 
        $arrParam['priceInUnit'] = $recount['priceInUnit']; 
        $arrParam['detailSubtotal'] = $recount['detailSubtotal']; 
		 
		 
        $arrParam = parent::normalizeParameter($arrParam, true);
  
		
        return $arrParam;
    }
	
    
    function generateDefaultQueryForAutoComplete($returnField){ 
        $sql = 'select
                '.$returnField['key'].',
                '.$returnField['value'].' as value,
                '.$this->tableName . '.code,
                '.$this->tableName . '.trdate,
                '.$this->tableName . '.grandtotal
            from 
                '.$this->tableName . ',
                '.$this->tableStatus.'  
            where  		
                '.$this->tableName . '.statuskey = '.$this->tableStatus.'.pkey  
        ';
        
        $sql .=  $this->getCompanyCriteria() ;
        return $sql;
        
    }
    

	function getTransportationType($pkey = '')  {
        $sql = 'SELECT 
					' . $this->tableTransportation . '.* ,
					'.$this->tableUnit.'.name as unitname
				FROM ' . $this->tableTransportation . ', '.$this->tableUnit.'  
					WHERE ' . $this->tableTransportation . '.unitkey = '.$this->tableUnit.'.pkey ';
		 
        if (!empty($pkey))
            $sql .= ' and ' . $this->tableTransportation . '.pkey in (' . $this->oDbCon->paramString($pkey).')';

        return $this->oDbCon->doQuery($sql);
    }
}
