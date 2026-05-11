<?php

class MedicalJobOrder extends BaseClass{

    function __construct(){

        parent::__construct();

        $this->tableName = 'medical_job_order_header';
        $this->tableNameDetail = 'medical_job_order_detail';
        $this->tableMedicalRequestClaim = 'medical_request_claim_header';
        $this->tableStatus = 'transaction_status';
        $this->tableInitialDiagnoseDetail = 'medical_job_order_initial_diagnose_detail';
        $this->tableInitialDiagnose = 'diagnose';
        $this->tableSupplier = 'supplier';
        $this->tableCity = 'city';
        $this->tableItem = 'item';
        $this->tableItemUnit = 'item_unit';
        $this->tableCountry = 'country';
        $this->tableCustomer = 'customer';
        $this->tableCustomerCategory = 'customer_category';
        $this->tableCustomerInsurancePolicy = 'customer_insurance_policy';
        //$this->tablePayment = 'job_order_payment';
        $this->tableCityCategory = 'city_category';
        $this->tablePartialInvoice = 'medical_job_order_partial_invoice';
        $this->securityObject = 'MedicalJobOrder';
        $this->tableFile = 'medical_job_order_file'; 
	    $this->uploadFileFolder = 'medical-job-order-file/';
        $this->isTransaction = true;
        $this->newLoad = true;

        $this->allowedStatusForEdit = array(1,2);

        $this->arrDataDetail = array();
        $this->arrDataDetail['pkey'] = array('hidDetailKey');
        $this->arrDataDetail['refkey'] = array('pkey', 'ref');
        $this->arrDataDetail['itemkey'] = array('hidItemKey');
        $this->arrDataDetail['qty'] = array('qty', 'number');
        $this->arrDataDetail['priceinunit'] = array('priceInUnit', 'number');
        $this->arrDataDetail['unitkey'] = array('selUnit');
        $this->arrDataDetail['trdesc'] = array('detailDescription');
        $this->arrDataDetail['statuskey'] = array('hidStatusKey');
        $this->arrDataDetail['total'] = array('detailSubtotal', 'number');
        $this->arrDataDetail['costinbaseunit'] = array('cogs', 'number');
        $this->arrDataDetail['receivedqtyinbaseunit'] = array('receivedQtyInBaseUnit', 'number');
        $this->arrDataDetail['qtyinbaseunit'] = array('qtyInBaseUnit', 'number');
        $this->arrDataDetail['priceinbaseunit'] = array('priceInBaseUnit', 'number');
        $this->arrDataDetail['refquotationkey'] = array('hidQuotationKey');

//        $this->arrPaymentDetail = array();
//        $this->arrPaymentDetail['pkey'] = array('hidDetailPaymentKey');
//        $this->arrPaymentDetail['refkey'] = array('pkey', 'ref');
//        $this->arrPaymentDetail['amount'] = array('paymentMethodValue', array('datatype' => 'number', 'mandatory' => true));
//        $this->arrPaymentDetail['paymentkey'] = array('selPaymentMethod', array('mandatory' => true));

        $this->arrInitialDiagnoseDetail = array(); 
        $this->arrInitialDiagnoseDetail['pkey'] = array('hidInitialDiagnoseDetailKey');
        $this->arrInitialDiagnoseDetail['refkey'] = array('pkey','ref');
        $this->arrInitialDiagnoseDetail['initialdiagnosekey'] = array('hidInitialDiagnoseKey',array('mandatory' => true));

        $this->allowedStatusForEdit = array(1,2);
        $arrDetails = array();
        array_push($arrDetails, array('dataset' => $this->arrDataDetail));
        //array_push($arrDetails, array('dataset' => $this->arrPaymentDetail, 'tableName' => $this->tablePayment));
        array_push($arrDetails, array('dataset' => $this->arrInitialDiagnoseDetail, 'tableName' => $this->tableInitialDiagnoseDetail));    
 		array_push($arrDetails, array('dataset' => $this->arrDataFile, 'tableName' => $this->tableFile, 
									  'datatype' => 'file', 'uploadFolder' => $this->uploadFileFolder,
									  'token' => 'token-item-file-uploader', 'fileName' => 'item-file-uploader'));   

        $this->arrData = array();
        $this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails));
        $this->arrData['refkey'] = array('hidMedicalRequestClaimKey');
        $this->arrData['refjobkey'] = array('hidMedicalJobOrderkey');
        $this->arrData['trdate'] = array('trDate','date');
        $this->arrData['statuskey'] = array('selStatus');
        $this->arrData['code'] = array('code');
        $this->arrData['codelog'] = array('codeLog'); 
        $this->arrData['customerkey'] = array('hidCustomerKey');
        //$this->arrData['customerinsurancepolicykey'] = array('hidCustomerInsurancePolicyKey');
        //$this->arrData['callername'] = array('callerName');
        //$this->arrData['relationtoinsured'] = array('relationToInsured');
        //$this->arrData['mobile'] = array('mobile');
        //$this->arrData['email'] = array('email');
        //$this->arrData['supplierkey'] = array('hidSupplierKey');
        // $this->arrData['age'] = array('age', 'number');
 
		$this->arrData['casephone'] = array('casePhone');
        $this->arrData['address'] = array('address');
		
        $this->arrData['citykey'] = array('hidCityKey');
        $this->arrData['subtotal'] = array('subtotal', 'number');
        $this->arrData['grandtotal'] = array('total', 'number');
        $this->arrData['beforetaxtotal'] = array('beforeTaxTotal', 'number');
        $this->arrData['finaldiscounttype'] = array('selFinalDiscountType', 'number');
        $this->arrData['finaldiscount'] = array('finalDiscount', 'number');
        $this->arrData['shipmentfee'] = array('shipmentFee', 'number');
        $this->arrData['etccost'] = array('etcCost', 'number');
        $this->arrData['taxpercentage'] = array('taxPercentage', 'number');
        $this->arrData['taxvalue'] = array('taxValue', 'number');
        $this->arrData['ispriceincludetax'] = array('chkIncludeTax');
        $this->arrData['reftabletype'] = array('selJOType'); 
        $this->arrData['trdesc'] = array('trDesc');
        $this->arrData['termofpaymentkey'] = array('selTermOfPaymentKey');
        $this->arrData['totalpayment'] = array('totalPayment', 'number');
        $this->arrData['balance'] = array('balance', 'number');
        $this->arrData['casephone'] = array('casePhone');
			
       

        $this->arrDataListAvailableColumn = array();
        array_push($this->arrDataListAvailableColumn, array('code' => 'code', 'title' => 'code', 'dbfield' => 'code', 'default' => true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'codeLog', 'title' => 'log', 'dbfield' => 'codelog', 'default' => true, 'width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'policyNumber', 'title' => 'policyNumber', 'dbfield' => 'policynumber', 'default' => true, 'width' => 120));
		array_push($this->arrDataListAvailableColumn, array('code' => 'id', 'title' => 'IDNumber', 'dbfield' => 'insuredid',  'width' => 120));
   		//array_push($this->arrDataListAvailableColumn, array('code' => 'callerName', 'title' => 'callerName', 'dbfield' => 'callername', 'width' => 200));
		array_push($this->arrDataListAvailableColumn, array('code' => 'insuredName', 'title' => 'insuredName', 'dbfield' => 'insuredname', 'default' => true, 'width' => 160));
		array_push($this->arrDataListAvailableColumn, array('code' => 'company', 'title' => 'company', 'dbfield' => 'customername', 'default' => true, 'width' => 160));
		array_push($this->arrDataListAvailableColumn, array('code' => 'insuredCompany', 'title' => 'insuranceCompany', 'dbfield' => 'suppliername', 'default' => true, 'width' => 120));
		array_push($this->arrDataListAvailableColumn, array('code' => 'mobile', 'title' => 'mobilePhone', 'dbfield' => 'insuredmobile', 'default' => true, 'width' => 100));
         array_push($this->arrDataListAvailableColumn, array('code' => 'status', 'title' => 'status', 'dbfield' => 'statusname', 'default' => true, 'width' => 100));


        $this->arrSearchColumn = array();
        array_push($this->arrSearchColumn, array('Kode', $this->tableName . '.code'));
        array_push($this->arrSearchColumn, array('No Polis', $this->tableCustomerInsurancePolicy . '.policynumber'));
        array_push($this->arrSearchColumn, array('Kode Log', $this->tableMedicalRequestClaim . '.code'));
        array_push($this->arrSearchColumn, array('Nama Perusahaan', $this->tableCustomer . '.name')); 
        array_push($this->arrSearchColumn, array('Nama Tertanggung', $this->tableMedicalRequestClaim . '.insuredphone')); 
        array_push($this->arrSearchColumn, array('Nama Tertanggung', $this->tableMedicalRequestClaim . '.insuredname')); 
        array_push($this->arrSearchColumn, array('Nama Tertanggung', $this->tableMedicalRequestClaim . '.insuredmobile')); 


        $this->printMenu = array();
        array_push($this->printMenu, array('code' => 'printTransaction', 'name' => $this->lang['printTransaction'],  'icon' => 'print', 'url' => 'print/medicalJobOrder'));
        array_push($this->printMenu, array('code' => 'printSummary', 'name' => $this->lang['printSummary'],  'icon' => 'print', 'url' => 'print/medicalJobOrderCompleteForm'));
     
        $this->includeClassDependencies(array(
            'Item.class.php',
            'Supplier.class.php',
            'ItemCategory.class.php',
            'Customer.class.php',
            'CustomerCategory.class.php',
            'MedicalRequestClaim.class.php',
            'TermOfPayment.class.php',
            'PaymentMethod.class.php',
            'MedicalPurchaseOrder.class.php',
            'Country.class.php',
            'Diagnose.class.php',
            'MedicalSalesInvoice.class.php',
            'CustomerInsurancePolicy.class.php',
            'MedicalSalesOrderQuotation.class.php'
        ));

        $this->overwriteConfig();
    }

    function getQuery(){

        $sql = '
                 select
                     ' . $this->tableName . '.*, 
                     ' . $this->tableCustomer . '.code as customercode,
                     ' . $this->tableCustomer . '.name as customername,
                     ' . $this->tableCustomerInsurancePolicy . '.policynumber,
                     ' . $this->tableMedicalRequestClaim . '.insuredname,  
                     ' . $this->tableMedicalRequestClaim . '.insuredid,
                     ' . $this->tableMedicalRequestClaim . '.insuredemail,
                     ' . $this->tableMedicalRequestClaim . '.insuredmobile,
                     ' . $this->tableMedicalRequestClaim . '.insuredphone,
                     ' . $this->tableMedicalRequestClaim . '.dateofbirth,
                     ' . $this->tableMedicalRequestClaim . '.age,
                     ' . $this->tableCountry . '.name as countryname, 
                     ' . $this->tableCustomerCategory . '.name as categoryname,	
                     ' . $this->tableSupplier . '.code as suppliercode,
                     ' . $this->tableSupplier . '.name as suppliername, 
                     ' . $this->tableSupplier . '.name as insurancecompanyname,
					 ' . $this->tableCity . '.name as cityname ,
                     ' . $this->tableCityCategory . '.name as citycategoryname ,
                     concat('.$this->tableCity.'.name,\', \', '.$this->tableCityCategory.'.name ) as casecityandcategoryname,
                     ' . $this->tableStatus . '.status as statusname
                 from 
                     ' . $this->tableName . ' 
					 left join ' . $this->tableMedicalRequestClaim . ' on ' . $this->tableName . '.refkey = ' . $this->tableMedicalRequestClaim . '.pkey 
                     left join ' . $this->tableCustomerInsurancePolicy . ' on ' . $this->tableMedicalRequestClaim . '.customerinsurancepolicykey = ' . $this->tableCustomerInsurancePolicy . '.pkey 
                     left join ' . $this->tableCustomer . ' on ' . $this->tableMedicalRequestClaim . '.customerkey = ' . $this->tableCustomer . '.pkey
                     left join ' . $this->tableCustomerCategory . ' on ' . $this->tableMedicalRequestClaim . '.customercategorykey = ' . $this->tableCustomerCategory . '.pkey
                     left join ' . $this->tableCountry . ' on ' . $this->tableMedicalRequestClaim . '.countrykey = ' . $this->tableCountry . '.pkey
                     left join ' . $this->tableSupplier . ' on ' . $this->tableMedicalRequestClaim . '.supplierkey = ' . $this->tableSupplier . '.pkey  
                     left join ' . $this->tableCity . ' on ' . $this->tableName . '.citykey = ' . $this->tableCity . '.pkey 
                     left join ' . $this->tableCityCategory . ' on ' . $this->tableCity . '.categorykey = ' . $this->tableCityCategory . '.pkey,' . $this->tableStatus . '
                 where  		
                     ' . $this->tableName . '.statuskey = ' . $this->tableStatus . '.pkey
          ' . $this->criteria;
        return $sql;
    }
    function afterUpdateData($arrParam, $action)  {
        $medicalRequestClaim = new MedicalRequestClaim(); 
        $this->setActivityTransactionLogDetail($arrParam['pkey'], $medicalRequestClaim, $arrParam['hidMedicalRequestClaimKey']); 
    }

    function afterStatusChanged($rsHeader){ 
        $medicalRequestClaim = new MedicalRequestClaim();
        $this->setActivityTransactionLogDetail($rsHeader[0]['pkey'], $medicalRequestClaim, $rsHeader[0]['refkey']);
    }

    function validateCancel($rsHeader, $autoChangeStatus = false)
    {
		// Job boleh dicancel meskipun telah disetujui quotation
		
		$pkey = $rsHeader[0]['pkey'];
		$medicalPurchaseOrder = new MedicalPurchaseOrder(); 

		
       $rsPurchase = $medicalPurchaseOrder->searchDataRow(
           array($medicalPurchaseOrder->tableName . '.pkey', $medicalPurchaseOrder->tableName . '.code'),
           ' and ' . $medicalPurchaseOrder->tableName . '.refkey = ' . $this->oDbCon->paramString($pkey) . ' 
                                                          and ' . $medicalPurchaseOrder->tableName . '.statuskey in (2,3)'
       );

       if (!empty($rsPurchase))
           $this->addErrorLog(false, '<strong>' . $rsHeader[0]['code'] . '</strong> ' . $this->errorMsg[201] . '<br><strong>' . $rsPurchase[0]['code'] . '</strong>, ' . $this->errorMsg[225]);
 
       $rsInvoiced = $this->getInvoiceInformation($pkey);
       if (!empty($rsInvoiced)) 
           $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' .$this->errorMsg[900].' <strong>'.$rsInvoiced[0]['code'].'</strong>');

    }

    function getInvoiceInformation($pkey, $statuskey = array(2,3)){
 	if(!is_array($statuskey)) $statuskey = array($statuskey);
        
        $medicalSalesInvoice = new MedicalSalesInvoice();
      
        $sql = 'select
            '.$medicalSalesInvoice->tableNameDetail.'.salesorderkey,     
            '.$medicalSalesInvoice->tableName.'.pkey,
            '.$medicalSalesInvoice->tableName.'.code,    
            '.$medicalSalesInvoice->tableName.'.trdate,
            '.$medicalSalesInvoice->tableName.'.isdownpayment,
            '.$medicalSalesInvoice->tableName.'.customerkey,
            '.$medicalSalesInvoice->tableName.'.grandtotal,
            '.$medicalSalesInvoice->tableName.'.statuskey,
            '.$medicalSalesInvoice->tableName.'.requestid,
            '.$medicalSalesInvoice->tableStatus.'.status as statusname,
            '.$medicalSalesInvoice->tableNameDetail.'.amount,
            '.$medicalSalesInvoice->tableCustomCode.'.pkey as invoicetypekey,
            '.$medicalSalesInvoice->tableCustomCode.'.name as invoicetypename
          from 
            '.$medicalSalesInvoice->tableName.',
            '.$medicalSalesInvoice->tableStatus.',
            '.$medicalSalesInvoice->tableNameDetail.',
            '.$medicalSalesInvoice->tableCustomCode.'
          where  
            '. $medicalSalesInvoice->tableNameDetail.'.salesorderkey in ('.$this->oDbCon->paramString($pkey,',') .') and   
            '. $medicalSalesInvoice->tableName.'.pkey = '. $medicalSalesInvoice->tableNameDetail.'.refkey and
            '. $medicalSalesInvoice->tableName.'.statuskey = '. $medicalSalesInvoice->tableStatus.'.pkey and
            '. $medicalSalesInvoice->tableName.'.statuskey in ('.$this->oDbCon->paramString($statuskey,',').') and
            '. $medicalSalesInvoice->tableName.'.customcodekey =  '. $medicalSalesInvoice->tableCustomCode.'.pkey';
 
        return $this->oDbCon->doQuery($sql);

    }

    function validateForm($arr, $pkey = '') {
	 
		// satu case boleh lebih dari satu job (utk job susulan)
		
        $arrayToJs = parent::validateForm($arr, $pkey);
        $item = new Item();
        $arrItemkey = $arr['hidItemKey'];
        $arrQuantity = $arr['qty'];
        $arrPrice = $arr['priceInUnit'];

        $arrDetailKeys = array();

		if (empty($arr['hidMedicalRequestClaimKey']))  
			$this->addErrorList($arrayToJs, false, $this->errorMsg['reference'][1]);
	   
        for ($i = 0; $i < count($arrItemkey); $i++) {
            if (empty($arrItemkey[$i])) {
                $this->addErrorList($arrayToJs, false, $this->errorMsg['service'][1]);
            } else {

                // cek harga dan jumlah != 0
                if ($this->unFormatNumber($arrQuantity[$i]) <= 0 || $this->unFormatNumber($arrPrice[$i]) <= 0) {
                    $rsItem = $item->getDataRowById($arrItemkey[$i]);
                    $this->addErrorList($arrayToJs, false, $rsItem[0]['name'] . '. ' . $this->errorMsg[500]);
                }


                // cek detail double 
				// nanti dibuka jika diperlukan
                if (in_array($arrItemkey[$i], $arrDetailKeys)) {
                    $rsItem = $item->getDataRowById($arrItemkey[$i]);
                    $this->addErrorList($arrayToJs, false, $rsItem[0]['name'] . '. ' . $this->errorMsg[215]);
                } else {
                    array_push($arrDetailKeys, $arrItemkey[$i]);
                }
            }
        }
 

        return $arrayToJs;
    }

    function validateConfirm($rsHeader)
    {

        $id = $rsHeader[0]['pkey'];

        // $rsPayment = $this->getPaymentMethodDetail($id);
        // $termOfPayment = new TermOfPayment();
        // $rsTOP = $termOfPayment->getDataRowById($rsHeader[0]['termofpaymentkey']);
        // $isCash = ($rsTOP[0]['duedays'] == 0) ? true : false;

        // $totalPayment = 0;
        // for ($i = 0; $i < count($rsPayment); $i++)
        //     $totalPayment += $rsPayment[$i]['amount'];

        // $balance = $totalPayment - $rsHeader[0]['grandtotal'];

        // if ($isCash) {
        //     $thresholdDiscount = abs($this->loadSetting('roundedPaymentThreshold'));
        //     if ($balance < ($thresholdDiscount * -1))
        //         $this->addErrorLog(false, '<strong>' . $rsHeader[0]['code'] . '</strong>. ' . $this->errorMsg[502]);
        //     else if ($balance > $thresholdDiscount)
        //         $this->addErrorLog(false, '<strong>' . $rsHeader[0]['code'] . '</strong>. ' . $this->errorMsg[509]);
        // }
    }

   function generateDefaultQueryForAutoComplete($returnField){ 

        $sql = 'select
					'.$returnField['key'].',
					'.$returnField['value'].' as value, 
                    '.$this->tableName . '.code,
                    '.$this->tableName . '.codelog,
                    '.$this->tableName . '.trdate,
                    '.$this->tableName . '.grandtotal,
                    '.$this->tableName . '.address as caseaddress,
                    concat('.$this->tableCity.'.name,\', \', '.$this->tableCityCategory.'.name ) as casecityandcategoryname,
                    '.$this->tableName . '.casephone,
                    '.$this->tableName . '.trdesc as casedescription,
                    '.$this->tableName . '.refkey,
                    '.$this->tableMedicalRequestClaim . '.insuredname
				from 
					'.$this->tableName . '
                    left join '.$this->tableCity.'  on '.$this->tableName.'.citykey = '.$this->tableCity.'.pkey 
					left join '.$this->tableCityCategory.'  on '.$this->tableCity.'.categorykey = '.$this->tableCityCategory.'.pkey
                    left join ' . $this->tableMedicalRequestClaim . ' on ' . $this->tableName . '.refkey = ' . $this->tableMedicalRequestClaim . '.pkey,
                    '.$this->tableStatus.' 
				where  		 
					'.$this->tableName . '.statuskey = '.$this->tableStatus.'.pkey  
			';
        
        $sql .=  $this->getCompanyCriteria() ;
        return $sql;
        
    }

    function getDetailWithRelatedInformation($pkey, $criteria = '')
    {

        $sql = 'select
	   			' . $this->tableNameDetail . '.*, 
                ' . $this->tableItem . '.name as itemname, 
                ' . $this->tableItem . '.isquotation, 
                ' . $this->tableItem . '.code as itemcode,
                ' . $this->tableItemUnit . '.name as unitname
			  from
			  	' . $this->tableNameDetail . ',
                ' . $this->tableItemUnit . ',
                ' . $this->tableItem . '
			  where
			  	' . $this->tableNameDetail . '.itemkey = ' . $this->tableItem . '.pkey and
                ' . $this->tableNameDetail . '.unitkey = ' . $this->tableItemUnit . '.pkey and
                ' . $this->tableNameDetail . '.refkey in (' . $this->oDbCon->paramString($pkey, ',') . ') ';

        $sql .= $criteria;

        return $this->oDbCon->doQuery($sql);
    }


    function reCountSubtotal($arrParam)
    {

        $item = new Item(); 
        $isPriceIncludeTax = (!empty($arrParam['chkIncludeTax'])) ? 1 : 0;

        $subtotal = 0;
        $grandtotal = 0;
        
        $arrItemkey = $arrParam['hidItemKey'];
        $taxValue = $this->unFormatNumber($arrParam['taxValue']);
        $finalDiscount = $this->unFormatNumber($arrParam['finalDiscount']);
        $finalDiscountType = $arrParam['selFinalDiscountType'];
        $taxPercentage = $this->unFormatNumber($arrParam['taxPercentage']);
        $shipmentFee = $this->unFormatNumber($arrParam['shipmentFee']);
        $etcCost = $this->unFormatNumber($arrParam['etcCost']);

        $arrQty = $arrParam['qty'];
        $arrPriceinunit = $arrParam['priceInUnit'];
        $arrTransUnitKey = $arrParam['selUnit'];

        $arrItemDetail = array();

        for ($i = 0; $i < count($arrItemkey); $i++) {

            if (empty($arrItemkey[$i]))
                continue;

            $rsItem = $item->getDataRowById($arrItemkey[$i]);

            $itemkey = $arrItemkey[$i];
            $transactionUnitKey = $arrTransUnitKey[$i];
            $baseunitkey = $rsItem[0]['baseunitkey'];
            $qty =  $this->unFormatNumber($arrQty[$i]);
            $qtyinbaseunit = $qty;
            $priceInUnit = $this->unFormatNumber($arrPriceinunit[$i]);

            $arrItemDetail[$i]['baseUnitKey'] = $baseunitkey;
            $arrItemDetail[$i]['qtyInBaseUnit'] = $qtyinbaseunit;
            $arrItemDetail[$i]['priceInBaseUnit'] = $priceInUnit;


            //$detailSubtotal = $qtyinbaseunit * ($priceInUnit - $discountValue);
            $detailSubtotal = $qty * $priceInUnit;
            $arrItemDetail[$i]['detailSubtotal'] = $detailSubtotal;

            $subtotal += $detailSubtotal;

            $arrItemDetail[$i]['gramasi'] =  ($rsItem[0]['gramasi'] * $qtyinbaseunit);
            $gramasi += $arrItemDetail[$i]['gramasi'];
        }

        $grandtotal = $subtotal;

        if ($finalDiscount != 0) {
            if ($finalDiscountType == 2)
                $finalDiscount = $finalDiscount / 100 * $grandtotal;
        }

        $beforeTaxTotal = $subtotal - $finalDiscount;
        $grandtotal = $beforeTaxTotal;

        if ($isPriceIncludeTax == false) {
            $taxValue = $beforeTaxTotal * $taxPercentage / 100;
            $taxValue = round($taxValue); // kalo ad koma, nilainya gantung di AP nanti
            $grandtotal += $taxValue;
        } else {
            $taxValue = ($taxPercentage / (100 + $taxPercentage)) * $grandtotal;
            $beforeTaxTotal = $grandtotal - $taxValue;
        }

        $grandtotal +=  $shipmentFee + $etcCost;


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
        $reCountResult['grandtotal'] = $grandtotal;
        $reCountResult['totalPayment'] = $totalPayment;
        $reCountResult['balance'] = $balance;
        $reCountResult['detailCOGS'] = $arrItemDetail;

        return $reCountResult;
    }

    function cancelTrans($rsHeader, $copy)
    {
		// untuk saat ini, bm otoamtis cancel quotation yg masih pending
		
		$pkey = $rsHeader[0]['pkey'];
		
		$medicalRequestClaim = new MedicalRequestClaim();
        $rsMedicalRequestType = $medicalRequestClaim->getTableKeyAndObj($medicalRequestClaim->tableName, array('key'));
		
		// update ulang referensi job order di quotation
		// satu case bisa lebih dr satu quotation
		// tetep select reftabletype untuk memastikan dr request
        $medicalSalesOrderQuotation = new MedicalSalesOrderQuotation();
        $rsMedicalSalesOrderQuotation = $medicalSalesOrderQuotation->searchDataRow( array($medicalSalesOrderQuotation->tableName . '.pkey'),
																	 	' and ' . $medicalSalesOrderQuotation->tableName . '.refkey = ' . $this->oDbCon->paramString($rsHeader[0]['pkey']) . ' 
																		  and ' . $medicalSalesOrderQuotation->tableName . '.reftabletype = ' .  $this->oDbCon->paramString($rsMedicalRequestType['key'])
																	);
		
	 
		$sql = 'update ' . $medicalSalesOrderQuotation->tableName.' 
				set 
					refkey = 0, refcode = \'\' 
				where  
					'.$medicalSalesOrderQuotation->tableName . '.refkey = ' . $this->oDbCon->paramString($pkey) . ' and 
					'.$medicalSalesOrderQuotation->tableName . '.reftabletype = ' .  $this->oDbCon->paramString($rsMedicalRequestType['key']);
												
		$this->oDbCon->execute($sql);
		  
       $medicalPurchaseOrder = new MedicalPurchaseOrder();
       $rsPurchase = $medicalPurchaseOrder->searchDataRow(
           array($medicalPurchaseOrder->tableName . '.pkey', $medicalPurchaseOrder->tableName . '.code'),
           ' and ' . $medicalPurchaseOrder->tableName . '.refkey = ' . $this->oDbCon->paramString($pkey) . ' 
                                                         and ' . $medicalPurchaseOrder->tableName . '.statuskey in (1)'
       );

       for ($i = 0; $i < count($rsPurchase); $i++) {
           // cancel purchase yg terbentuk dr JO
           $medicalPurchaseOrder->changeStatus($rsPurchase[$i]['pkey'], 4, '', false, true);
       }
 
		if ($copy)
            $this->copyDataOnCancel($rsHeader[0]['pkey']);
		
    }


    function getTotalInvoicedAndOutstanding($id, $customCodeKey = '')
    {

        $customCodeCriteria = (!empty($customCodeKey)) ? ' and customcodekey = ' . $this->oDbCon->paramString($customCodeKey) : '';

        $sql = 'select pkey, amount  from ' . $this->tablePartialInvoice . ' where refkey = ' . $this->oDbCon->paramString($id) . ' and amount > 0 ' . $customCodeCriteria;
        $rs = $this->oDbCon->doQuery($sql);

        $totalInvoiced = 0;
        foreach ($rs as $row)
            $totalInvoiced += $row['amount'];

        $sql = 'select coalesce(sum(amount),0) as outstanding  from ' . $this->tablePartialInvoice . ' where refkey = ' . $this->oDbCon->paramString($id) . $customCodeCriteria;
        $rsOutstanding = $this->oDbCon->doQuery($sql);

        $arr = array();
        $arr['rsTotalnvoiced'] = $rs;
        $arr['totalInvoiced'] = $totalInvoiced;
        $arr['outstanding'] = $rsOutstanding[0]['outstanding'];

        return $arr;
    }

    function validateClose($rsHeader){
        
        parent::validateClose($rsHeader);
        
        $id = $rsHeader[0]['pkey'];
        
        $sql = 'select  pkey from  ' . $this->tableNameDetail.'  where  
            refkey = '.$this->oDbCon->paramString($id).' and  
            (qty > qtyinvoiced or qtyinvoiced = 0)
        ';
        
        $rs =  $this->oDbCon->doQuery($sql);
        if (!empty($rs)) 
             $this->addErrorLog(false, '<b>'.$rsHeader[0]['code'].'</b>. '.$this->errorMsg[506]);      
     
    }		


    function getUnInvoicedItemDetail($pkey){
        
        // asumsi itemkey dan costkey, pasti pkeynya unique, dan masing2 hanya bisa di detail atau di cost
        $sql = '  SELECT trans.*, item.name as itemname,item.istax23,ispriceincludetax,taxpercentage, item.aliasname from ( 
                    select concat(pkey,\'-\',itemkey) as joinkey, pkey, refkey, itemkey, trdesc, qty as qtyinbaseunit, (qty - qtyinvoiced) as outstandingqty, priceinunit, (qty - qtyinvoiced) * priceinunit as total, \'2\' as orderlist from '.$this->tableNameDetail.' where refkey in ('.$this->oDbCon->paramString($pkey,',').')
                 ) trans, item 
                 where  
                    trans.refkey in ('.$this->oDbCon->paramString($pkey,',').') and  
                    trans.itemkey = item.pkey  and outstandingqty > 0  
                 order by orderlist asc, pkey asc
                ';
           
		$rs = $this->oDbCon->doQuery($sql);
        return $rs;
    }

    function getJobOrderByMonth($startPeriod, $endPeriod){
        $sql = 'select 
                   month(trdate) as month,  
                   DATE_FORMAT(trdate, \'%b\')  as monthname, 
                   year(trdate) as year, 
                   sum(grandtotal) as total
               from 
                   '.$this->tableName.'
               where (statuskey >= 2 and statuskey <= 6) and trdate between \''. date("Y-m-d", strtotime($startPeriod)) .'\' and LAST_DAY(\''. date("Y-m-d 23:59", strtotime($endPeriod)) .'\')';
       
         $sql .=  $this->getWarehouseCriteria() ;
         $sql .= ' group by year(trdate),month(trdate)';
       
        return $this->oDbCon->doQuery($sql); 
   } 
    function updateAmountInvoiced($pkey){
        
        // gk perlu udpate customer AR outstanding karena sudah diupdate di ketika terbentuk AR
        
        $medicalSalesInvoice = new MedicalSalesInvoice(); 
        
        $sql = 'update ' . $this->tableName.' set totalinvoiced = (
                    select coalesce(sum('.$medicalSalesInvoice->tableNameDetail.'.amount),0) as amount
                    from
                        '.$medicalSalesInvoice->tableNameDetail.',
                        '.$medicalSalesInvoice->tableName.'
                    where
                        '.$medicalSalesInvoice->tableName.'.statuskey in (2,3) and 
                        '.$medicalSalesInvoice->tableName.'.pkey =  '.$medicalSalesInvoice->tableNameDetail.'.refkey and
                        '.$medicalSalesInvoice->tableNameDetail.'.salesorderkey = '.$this->oDbCon->paramString($pkey).'
                ) where pkey = '.$this->oDbCon->paramString($pkey);
         
        $this->oDbCon->execute($sql); 
    }


    function updateQtyInvoiced($pkey,$isValidated = false){  
        $rsHeader = $this->getDataRowById($pkey);
        
        $arrayToJs = array();
        
        $rsItemDetail = $this->getDetailById($pkey);
              
        // update setiap SO, sudah brp qty yg ditagih, item dan cost 
        try{
            
            if(!$this->oDbCon->startTrans())
				throw new Exception($this->errorMsg[100]);
            
            
            for($j=0;$j<count($rsItemDetail);$j++){
                if(!$isValidated)
                    $totalInvoiced = $this->getTotalQtyInvoiced($pkey,$rsItemDetail[$j]['pkey'],$rsItemDetail[$j]['itemkey']);
                else
                    $totalInvoiced = $rsItemDetail[$j]['qtyinbaseunit'];


                $sql = 'update 
                            ' . $this->tableNameDetail.'
                        set 
                            qtyinvoiced = '.$this->oDbCon->paramString($totalInvoiced).' 
                        where  
                            pkey = '.$this->oDbCon->paramString($rsItemDetail[$j]['pkey']).' 
                        ';

                $this->oDbCon->execute($sql);

            } 
            
            $this->oDbCon->endTrans();
             
		
	    }  catch(Exception $e){ 
            $this->oDbCon->rollback(); 
          /*  
            if (!empty($e->getMessage()))
                $this->addErrorLog(false,$e->getMessage()); */
		}		
        
        
        
        // cek utk SO, semua sudah tertagih atau blm. lalu ubah status 
        $sql = 'SELECT * from (
                    select  pkey,itemkey from   ' . $this->tableNameDetail.'  where  refkey = '.$this->oDbCon->paramString($pkey).' and  qty > qtyinvoiced 
                ) trans ';
         
        $rs =  $this->oDbCon->doQuery($sql);
        
        if (empty($rs)) { 
            if($rsHeader[0]['statuskey'] <> 3)
                $arrayToJs = $this->changeStatus($pkey,3,'',false,true);
        }else{ 
            if ($rsHeader[0]['statuskey'] == 3) 
                $arrayToJs = $this->changeStatus($pkey,2,'',false,true);
        }
        
        return $arrayToJs;
           
    }


    function getTotalQtyInvoiced($pkey,$detailkey, $itemkey){ 
        // tambahkan paramter itemkey untuk membedakan dr detail atau selling cost
        // dengan ada item key sudah pasti beda karena detail item dan item cost 1 table, jd pkey pasti beda
        // kenapa $itemkeyny jd gk kepake ??
        
            $medicalSalesInvoice = new MedicalSalesInvoice(); 
        
         // update setiap SO, sudah brp qty yg ditagih, item dan cost
            $sql = 'select 
                        coalesce(sum(qtyinbaseunit),0) as totalinvoiced
                    from  
                        '.$medicalSalesInvoice->tableName.',  
                        '.$medicalSalesInvoice->tableNameDetail.',
                        '.$medicalSalesInvoice->tableNameItemDetail.' 
                    where 
                        '.$medicalSalesInvoice->tableName.'.pkey = '.$medicalSalesInvoice->tableNameDetail.'.refkey and
                        '.$medicalSalesInvoice->tableNameDetail.'.pkey = '.$medicalSalesInvoice->tableNameItemDetail.'.refkey and
                        '.$medicalSalesInvoice->tableName.'.statuskey in (2,3) and
                        '.$medicalSalesInvoice->tableNameDetail.'.salesorderkey = '.$this->oDbCon->paramString($pkey).' and
                        '.$medicalSalesInvoice->tableNameItemDetail.'.refsodetailkey = '.$this->oDbCon->paramString($detailkey).' and
                        '.$medicalSalesInvoice->tableNameItemDetail.'.itemkey =  '.$this->oDbCon->paramString($itemkey).' 
                    ';
  
            $rsTotal = $this->oDbCon->doQuery($sql);
         
            return $rsTotal[0]['totalinvoiced'];
    }

    function getItemFile($pkey){
		$sql = 'select * from '.$this->tableFile.' where refkey = '.$this->oDbCon->paramString($pkey).' order by pkey asc';	
		return $this->oDbCon->doQuery($sql);
    } 
	
    function getDetailDiagnose($pkey,$criteria=''){
        
        $sql = 'select
               '.$this->tableInitialDiagnoseDetail .'.*,
               '.$this->tableInitialDiagnose .'.name as initialdiagnose,
			   	concat ('.$this->tableInitialDiagnose . '.code,\' - \','.$this->tableInitialDiagnose . '.name ) as codenameinitialdiagnose
          from
              '. $this->tableInitialDiagnoseDetail .' 
              left join ' . $this->tableInitialDiagnose . ' on ' . $this->tableInitialDiagnoseDetail . '.initialdiagnosekey = ' . $this->tableInitialDiagnose . '.pkey 
          where  
              '.$this->tableInitialDiagnoseDetail .'.refkey = '.$this->oDbCon->paramString($pkey);
     
            
		$sql .= $criteria;
		return $this->oDbCon->doQuery($sql);
    }
 
    function normalizeParameter($arrParam, $trim = false){
   
        $rsDetail = $this->getDetailById($arrParam['hidId']);
        $arrItemDetail = array_column($rsDetail, null, 'pkey');
		
// gk perlu,utk job lanjutan, tetep mengarah ke new case lama
//        if ($arrParam['selJOType'] == $rsMedicalRequestType['key']) {
//            $arrParam['hidMedicalJobOrderkey'] = 0; 
//        } else {
//            $arrParam['hidMedicalRequestClaimKey'] = 0;  
//        }
 
        $arrItemkey = $arrParam['hidItemKey'];
  
        $reCountResult = $this->reCountSubtotal($arrParam);
        $arrParam['detailCOGS'] = $reCountResult['detailCOGS'];
        $arrParam['subtotal'] = $reCountResult['subtotal'];
        $arrParam['beforeTaxTotal'] = $reCountResult['beforeTaxTotal'];
        $arrParam['isPriceIncludeTax'] = $reCountResult['isPriceIncludeTax'];
        $arrParam['grandtotal'] = $reCountResult['grandtotal'];
        $arrParam['totalPayment'] = $reCountResult['totalPayment'];
        $arrParam['balance'] = $reCountResult['balance'];
		
		 for ($i=0;$i<count($arrItemkey);$i++){  
			 
			$hidDetailKey = $arrParam['hidDetailKey'][$i];
			 
			$arrParam['detailSubtotal'][$i] = $arrParam['detailCOGS'][$i]['detailSubtotal']; 
			$arrParam['selUnit'][$i] = 1; // sementara 
			 
			// buat jaga yg sudah ad quotation gk bisa diganti namanya
			// gimana caranyaa agar statusnya gk bisa di tempered ?
            if (isset($arrItemDetail[$hidDetailKey]) && $arrItemDetail[$hidDetailKey]['statuskey'] == 2) { 
                $arrParam['hidItemKey'][$i] = $arrItemDetail[$hidDetailKey]['itemkey'];
                $arrParam['hidQuotationKey'][$i] = $arrItemDetail[$hidDetailKey]['refquotationkey'];
			}
			 
		}
		
		// update ulang status quotation
 

        $arrParam = parent::normalizeParameter($arrParam, true);

        return $arrParam;
    }
}
