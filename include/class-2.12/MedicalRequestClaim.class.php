<?php
// data yang diambil dr polis adalah data yg tdk bisa berubah, seperti : nama, tgl lahir, 
// sisanya disimpan sebagai histori ditransaksi

class MedicalRequestClaim extends BaseClass{

    function __construct(){

        parent::__construct();

        $this->tableName = 'medical_request_claim_header';
        $this->tableNameDetail = 'medical_request_claim_detail';
        $this->tableNameDetailStatus = 'transaction_status';
        $this->tableStatus = 'transaction_status';
        $this->tableSupplier = 'supplier';
        $this->tableMedicalJobOrder = 'medical_job_order';
        $this->tableInitialDiagnoseDetail = 'medical_request_claim_initial_diagnose_detail';
        $this->tableInitialDiagnose = 'diagnose';
        $this->tableCity = 'city';
        $this->tableItem = 'item';
        $this->tableCountry = 'country';
        $this->tableCustomer = 'customer';
        $this->tableCustomerCategory = 'customer_category';
        $this->tableCustomerInsurancePolicy = 'customer_insurance_policy';
        $this->tableCityCategory = 'city_category';
        $this->securityObject = 'MedicalRequestClaim';
        $this->tableFile = 'medical_request_claim_file'; 
	    $this->uploadFileFolder = 'request-claim-file/';
        $this->isTransaction = true;
        $this->newLoad = true;
        $this->tableActionLog = 'transaction_log_action';

        $this->arrDataDetail = array();
        $this->arrDataDetail['pkey'] = array('hidDetailKey');
        $this->arrDataDetail['refkey'] = array('pkey', 'ref');
        $this->arrDataDetail['itemkey'] = array('hidItemKey');
        $this->arrDataDetail['qty'] = array('qty', 'number');
        $this->arrDataDetail['priceinunit'] = array('priceInUnit', 'number');
        $this->arrDataDetail['total'] = array('detailSubtotal', 'number');
        $this->arrDataDetail['trdesc'] = array('detailDescription');

        $this->arrInitialDiagnoseDetail = array(); 
        $this->arrInitialDiagnoseDetail['pkey'] = array('hidInitialDiagnoseDetailKey');
        $this->arrInitialDiagnoseDetail['refkey'] = array('pkey','ref');
        $this->arrInitialDiagnoseDetail['initialdiagnosekey'] = array('hidInitialDiagnoseKey',array('mandatory' => true));
 
        
        $arrDetails = array(); 
        array_push($arrDetails, array('dataset' => $this->arrDataDetail, 'tableName' => $this->tableNameDetail)); 
        array_push($arrDetails, array('dataset' => $this->arrInitialDiagnoseDetail, 'tableName' => $this->tableInitialDiagnoseDetail));   
 		array_push($arrDetails, array('dataset' => $this->arrDataFile, 'tableName' => $this->tableFile, 
									  'datatype' => 'file', 'uploadFolder' => $this->uploadFileFolder,
									  'token' => 'token-item-file-uploader', 'fileName' => 'item-file-uploader'));   

        $this->arrData = array();
        $this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails));
        $this->arrData['code'] = array('code');
        $this->arrData['codelog'] = array('codeLog');
        $this->arrData['callername'] = array('callerName');
        $this->arrData['relationtoinsured'] = array('relationToInsured');
        $this->arrData['mobile'] = array('mobile');
        $this->arrData['email'] = array('email');
        $this->arrData['trdate'] = array('trDate', 'date');
        $this->arrData['insuredname'] = array('insuredName');
        $this->arrData['address'] = array('address');
        $this->arrData['citykey'] = array('hidCityKey');
        $this->arrData['dateofbirth'] = array('dateOfBirth', 'date'); 
        //$this->arrData['policynumber'] = array('policyNumber'); // diambil dr database polis, agar gk kebykan kode
        $this->arrData['casephone'] = array('casePhone');
        $this->arrData['refkey'] = array('hidRefKey');
        $this->arrData['trdesc'] = array('trDesc'); 
        $this->arrData['subtotal'] = array('subtotal', 'number');
        $this->arrData['finaldiscounttype'] = array('selFinalDiscountType', 'number');
        $this->arrData['finaldiscount'] = array('finalDiscount', 'number');
        $this->arrData['beforetaxtotal'] = array('beforeTaxTotal', 'number');
        $this->arrData['ispriceincludetax'] = array('chkIncludeTax');
        $this->arrData['taxpercentage'] = array('taxPercentage', 'number');
        $this->arrData['taxvalue'] = array('taxValue', 'number');
        $this->arrData['etccost'] = array('etcCost', 'number');
        $this->arrData['grandtotal'] = array('grandtotal', 'number');
         
		
		// ukt history
        $this->arrData['customercategorykey'] = array('hidCustomerCategoryKey');
        $this->arrData['customerkey'] = array('hidCustomerKey');
        $this->arrData['customerinsurancepolicykey'] = array('hidCustomerInsurancePolicyKey');
        $this->arrData['supplierkey'] = array('hidSupplierKey');
        $this->arrData['insuredid'] = array('insuredID');
        $this->arrData['countrykey'] = array('selCountry');
        $this->arrData['age'] = array('age', 'number');
        $this->arrData['insuredmobile'] = array('insuredMobile');
        $this->arrData['insuredphone'] = array('insuredPhone');
        $this->arrData['insuredemail'] = array('insuredEmail');


        $this->arrDataListAvailableColumn = array();
        array_push($this->arrDataListAvailableColumn, array('code' => 'code', 'title' => 'code', 'dbfield' => 'code', 'default' => true, 'width' => 120));
        //array_push($this->arrDataListAvailableColumn, array('code' => 'codeLog', 'title' => 'codeLog', 'dbfield' => 'codelog', 'default' => true, 'width' => 120));
        //array_push($this->arrDataListAvailableColumn, array('code' => 'callerName', 'title' => 'callerName', 'dbfield' => 'callername', 'width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'policyNumber', 'title' => 'policyNumber', 'dbfield' => 'policynumber', 'default' => true, 'width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'id', 'title' => 'IDNumber', 'dbfield' => 'insuredid',  'width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'insuredName', 'title' => 'insuredName', 'dbfield' => 'insuredname', 'default' => true, 'width' => 160));
        array_push($this->arrDataListAvailableColumn, array('code' => 'company', 'title' => 'company', 'dbfield' => 'customername', 'default' => true, 'width' => 160));
        array_push($this->arrDataListAvailableColumn, array('code' => 'insuredCompany', 'title' => 'insuranceCompany', 'dbfield' => 'insurancecompanyname', 'default' => true, 'width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'mobile', 'title' => 'mobilePhone', 'dbfield' => 'mobile', 'default' => true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'totalQuote', 'title' => '[icon]file', 'dbfield' => 'quotetotal', 'default' => true, 'width' => 50, 'format' => 'number', 'align' => 'center'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'totalQuoteApproved', 'title' => '[icon]check', 'dbfield' => 'quoteapproved', 'default' => true, 'width' => 50, 'format' => 'number', 'align' => 'center'));
        //array_push($this->arrDataListAvailableColumn, array('code' => 'diagnose', 'title' => 'initialDiagnose', 'dbfield' => 'initialdiagnose',  'width' => 250));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status', 'title' => 'status', 'dbfield' => 'statusname', 'default' => true, 'width' => 100));


        $this->arrSearchColumn = array();
        array_push($this->arrSearchColumn, array('Kode', $this->tableName . '.code'));
        array_push($this->arrSearchColumn, array('Nama Penelepon', $this->tableName . '.callername'));
        array_push($this->arrSearchColumn, array('No Polis', $this->tableCustomerInsurancePolicy . '.policynumber'));
        array_push($this->arrSearchColumn, array('No Telp', $this->tableName . '.mobile'));
        array_push($this->arrSearchColumn, array('email', $this->tableName . '.email'));
        array_push($this->arrSearchColumn, array('Nama Tertanggung', $this->tableName . '.insuredname'));
        array_push($this->arrSearchColumn, array('Telp. Tertanggung', $this->tableName . '.insuredmobile'));
        array_push($this->arrSearchColumn, array('Email Tertanggung', $this->tableName . '.insuredemail'));
        array_push($this->arrSearchColumn, array('ID Tertanggung', $this->tableName . '.insuredid'));
        array_push($this->arrSearchColumn, array('company', $this->tableCustomer . '.name'));
        array_push($this->arrSearchColumn, array('insuranceCompany', $this->tableSupplier . '.name'));
        array_push($this->arrSearchColumn, array('status', $this->tableStatus . '.status'));

        $this->printMenu = array();
        array_push($this->printMenu, array('code' => 'printTransaction', 'name' => $this->lang['printTransaction'],  'icon' => 'print', 'url' => 'print/medicalRequestClaim'));
 
        $this->includeClassDependencies(array(
            'Item.class.php',
            'Supplier.class.php',
            'ItemCategory.class.php',
            'Customer.class.php',
            'CustomerCategory.class.php',
            'CustomerInsurancePolicy.class.php',
            'Country.class.php',
            'Diagnose.class.php',
            //'MedicalSalesOrderQuotation.class.php',
            'MedicalJobOrder.class.php'
        ));

        $this->overwriteConfig();
    }

    function getQuery(){

        $sql = '
                 select
                     ' . $this->tableName . '.*,
                     ' . $this->tableCustomer . '.categorykey, 
                     ' . $this->tableCustomer . '.pkey as customerkey,
                     ' . $this->tableCustomer . '.name as customername,
                     ' . $this->tableCustomerInsurancePolicy . '.policynumber,
                     ' . $this->tableCustomerInsurancePolicy . '.dateofbirth,
                     ' . $this->tableCountry . '.name as countryname,
                     ' . $this->tableCustomerCategory . '.name as categoryname,
                     ' . $this->tableStatus . '.status as statusname,
                     ' . $this->tableSupplier . '.code as insurancecompanycode,
                     ' . $this->tableSupplier . '.name as insurancecompanyname,
					 ' . $this->tableCity . '.name as cityname ,
                     concat(' . $this->tableCity. '.name,\', \', '. $this->tableCityCategory . '.name) as cityandcategoryname
                 from 
                     ' . $this->tableName . '
						 left join ' . $this->tableSupplier . ' on ' . $this->tableName . '.supplierkey = ' . $this->tableSupplier . '.pkey  
						 left join ' . $this->tableCountry . ' on ' . $this->tableName . '.countrykey = ' . $this->tableCountry . '.pkey  
						 left join ' . $this->tableCustomerCategory . ' on ' . $this->tableName . '.customercategorykey = ' . $this->tableCustomerCategory . '.pkey  
						 left join ' . $this->tableCustomerInsurancePolicy . ' on ' . $this->tableName . '.customerinsurancepolicykey = ' . $this->tableCustomerInsurancePolicy . '.pkey  
						 left join ' . $this->tableCustomer . ' on ' . $this->tableName . '.customerkey = ' . $this->tableCustomer. '.pkey  
						 left join ' . $this->tableCity . ' on ' . $this->tableName . '.citykey = ' . $this->tableCity . '.pkey 
						 left join ' . $this->tableCityCategory . ' on ' . $this->tableCity . '.categorykey = ' . $this->tableCityCategory . '.pkey,
					' . $this->tableStatus . '
                 where  		
                     ' . $this->tableName . '.statuskey = ' . $this->tableStatus . '.pkey
          ' . $this->criteria;
		 
		//$this->setLog($sql,true);
		
        return $sql;
    }

    function validateForm($arr, $pkey = '')
    {
        $arrayToJs = parent::validateForm($arr, $pkey);

        $item = new Item();
        $arrItemkey = $arr['hidItemKey'];
        $arrQuantity = $arr['qty'];
        $arrPrice = $arr['priceInUnit'];
        $name = $arr['callerName'];

        if (empty($name)) {
            $this->addErrorList($arrayToJs, false, $this->errorMsg['customer'][1]);
        }

        $arrDetailKeys = array();

        for ($i = 0; $i < count($arrItemkey); $i++) {
            if (empty($arrItemkey[$i])) {
                // layanan boleh kosong
				// $this->addErrorList($arrayToJs, false, $this->errorMsg['service'][1]);
            } else {

                // cek harga dan jumlah != 0
                if ($this->unFormatNumber($arrQuantity[$i]) <= 0 || $this->unFormatNumber($arrPrice[$i]) <= 0) {
                    $rsItem = $item->getDataRowById($arrItemkey[$i]);
                    $this->addErrorList($arrayToJs, false, $rsItem[0]['name'] . '. ' . $this->errorMsg[500]);
                }

                // cek detail double 
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

	function afterAddDataOnCopy($pkey, $oldkey){
		
		// update jml quote
		$sql = 'update '.$this->tableName.' set quoteapproved = 0, quotetotal = 0 where pkey = ' .$this->oDbCon->paramString($pkey) ; 
		$this->oDbCon->execute($sql);
		
		// update status approval
		$sql = 'update '.$this->tableNameDetail.' set statuskey = 1 where refkey = ' .$this->oDbCon->paramString($pkey) ; 
		$this->oDbCon->execute($sql);
		 
    }
	
    function validateConfirm($rsHeader) {
        $id = $rsHeader[0]['pkey'];

        //$medicalSalesOrderQuotation = new MedicalSalesOrderQuotation(); 
        $rsDetail = $this->getDetailWithRelatedInformation($rsHeader[0]['pkey']);
		
		$needApproval = false;
		$totalApproved = 0;
        for ($i=0; $i< count($rsDetail); $i++) {
			if ($rsDetail[$i]['isquotation']){
				$needApproval = true;	
				
				if ($rsDetail[$i]['statuskey'] == 2)  $totalApproved++;
			} 
				
        }
		
		
		if ($needApproval && $totalApproved == 0)
			 $this->addErrorLog(false,  '<strong>' . $rsHeader[0]['code'] . '</strong>, ' . $this->errorMsg['medicalRequestClaim'][3]); 

    }

    function confirmTrans($rsHeader){

        $customer = new Customer();
        $medicalJobOrder = new MedicalJobOrder();

        $rsMedicalRequestType = $this->getTableKeyAndObj($this->tableName,array('key'));
        $arrParamJob = array();
        $arrParamJob['code'] = 'xxxxx';
        $arrParamJob['hidMedicalRequestClaimKey'] = $rsHeader[0]['pkey'];
        $arrParamJob['codeLog'] = $rsHeader[0]['codelog'];
		$arrParamJob['trDate'] =  $this->formatDBDate($rsHeader[0]['trdate'],'d / m / Y');
        $arrParamJob['selJOType'] = $rsMedicalRequestType['key'];
	
        $arrParamJob['address'] = $rsHeader[0]['address'];  
		$arrParamJob['hidCityKey'] =  $rsHeader[0]['citykey'];
        $arrParamJob['casePhone'] =  $rsHeader[0]['casephone'];
        
		$arrParamJob['subtotal'] =  $rsHeader[0]['subtotal'];
        $arrParamJob['total'] =  $rsHeader[0]['grandtotal'];
        $arrParamJob['trDesc'] =  $rsHeader[0]['trdesc'];
        $arrParamJob['beforeTaxTotal'] =  $rsHeader[0]['beforetaxtotal'];
        $arrParamJob['selFinalDiscountType'] =  $rsHeader[0]['finaldiscounttype'];
        $arrParamJob['finalDiscount'] =  $rsHeader[0]['finaldiscount'];
        $arrParamJob['etcCost'] =  $rsHeader[0]['etccost'];
        $arrParamJob['taxPercentage'] =  $rsHeader[0]['taxpercentage'];
        $arrParamJob['taxValue'] =  $rsHeader[0]['taxvalue'];
        $arrParamJob['chkIncludeTax'] =  $rsHeader[0]['ispriceincludetax'];
        $arrParamJob['hidCustomerKey'] =  $rsHeader[0]['customerkey'];
        
        $rsInitialDiagnoseDetail = $this->getDetailDiagnose($rsHeader[0]['pkey']);
		
		$arrParamJob['hidInitialDiagnoseDetailKey'] = array();
		$arrParamJob['hidInitialDiagnoseKey'] = array();
        foreach ($rsInitialDiagnoseDetail as $diagnoseRow) {
            array_push($arrParamJob['hidInitialDiagnoseDetailKey'], 0);
            array_push($arrParamJob['hidInitialDiagnoseKey'], $diagnoseRow['initialdiagnosekey']);
        }
		
        $rsDetail = $this->getDetailWithRelatedInformation($rsHeader[0]['pkey']);
		 
		$arrParamJob['hidDetailKey'] = array();
		$arrParamJob['hidItemKey'] = array();
		$arrParamJob['qty'] = array();
		$arrParamJob['priceInUnit'] = array();
		$arrParamJob['selUnit'] = array();
		$arrParamJob['detailSubtotal'] = array();
		$arrParamJob['detailDescription'] = array();
		$arrParamJob['hidStatusKey'] = array();
		$arrParamJob['hidQuotationKey'] = array();
		
        foreach ($rsDetail as $detailRow) { 
            array_push($arrParamJob['hidDetailKey'], 0);
            array_push($arrParamJob['hidItemKey'], $detailRow['itemkey']);
            array_push($arrParamJob['qty'], $detailRow['qty']);
            array_push($arrParamJob['priceInUnit'], $detailRow['priceinunit']);
            array_push($arrParamJob['selUnit'], 1);
            array_push($arrParamJob['detailSubtotal'], $detailRow['total']);
            array_push($arrParamJob['detailDescription'], $detailRow['trdesc']);
            array_push($arrParamJob['hidStatusKey'], $detailRow['statuskey']);
            array_push($arrParamJob['hidQuotationKey'], $detailRow['refquotationkey']);
        }

        //file gk perlu dicopy, kaarena akan dpisah 
        $arrayToJs = $medicalJobOrder->addData($arrParamJob);	 
   
		// update ulang referensi di quotation
		// satu case bisa lebih dr satu quotation
        $medicalSalesOrderQuotation = new MedicalSalesOrderQuotation();
        $rsMedicalSalesOrderQuotation = $medicalSalesOrderQuotation->searchDataRow( array($medicalSalesOrderQuotation->tableName . '.pkey'),
																	 	' and ' . $medicalSalesOrderQuotation->tableName . '.refrequestkey = ' . $this->oDbCon->paramString($rsHeader[0]['pkey']) . ' 
																		  and ' . $medicalSalesOrderQuotation->tableName . '.reftabletype = ' .  $this->oDbCon->paramString($rsMedicalRequestType['key'])
																	);
		
		for ($i=0; $i<count($rsMedicalSalesOrderQuotation); $i++) {
			$sql = 'update 
						' . $medicalSalesOrderQuotation->tableName.'
					set 
						refkey = '.$this->oDbCon->paramString($arrayToJs[0]['data']['pkey']).', 
						refcode = '.$this->oDbCon->paramString($arrayToJs[0]['data']['code']).' 
					where  
						pkey = '.$this->oDbCon->paramString($rsMedicalSalesOrderQuotation[$i]['pkey']).' 
					';

			$this->oDbCon->execute($sql);
		} 

		
        if (!$arrayToJs[0]['valid'])
            throw new Exception('<strong>' . $rsHeader[0]['code'] . '</strong>. ' . $this->errorMsg[201] . ' ' . $arrayToJs[0]['message']);

        return $arrayToJs;
    }


    function validateCancel($rsHeader, $autoChangeStatus = false)
    {

        $medicalJobOrder = new MedicalJobOrder();
        $arrayToJs = array();

        $id = $rsHeader[0]['pkey'];
 
        $rsJO = $medicalJobOrder->searchDataRow(
            array($medicalJobOrder->tableName . '.pkey', $medicalJobOrder->tableName . '.code'),
            ' and ' . $medicalJobOrder->tableName . '.refkey = ' . $this->oDbCon->paramString($id) . ' 
                                                               and ' . $medicalJobOrder->tableName . '.statuskey in (2,3)'
        );

        if (!empty($rsJO))
            $this->addErrorLog(false, '<strong>' . $rsHeader[0]['code'] . '</strong> ' . $this->errorMsg[201] . '<br><strong>' . $rsJO[0]['code'] . '</strong>, ' . $this->errorMsg[225]);
    }
	
   function cancelTrans($rsHeader, $copy){

        $medicalJobOrder = new MedicalJobOrder();
        $medicalSalesOrderQuotation = new MedicalSalesOrderQuotation();
        $rsTableType = $this->getTableKeyAndObj($this->tableName, array('key'));

        $id = $rsHeader[0]['pkey'];

		// hapus JO yg masih menunggu
        $rsJO = $medicalJobOrder->searchDataRow( array($medicalJobOrder->tableName . '.pkey', $medicalJobOrder->tableName . '.code'),
												' and ' . $medicalJobOrder->tableName . '.refkey = ' . $this->oDbCon->paramString($id) . ' 
                                                  and ' . $medicalJobOrder->tableName . '.statuskey in (1)'
        );

        for ($i = 0; $i < count($rsJO); $i++) {
            $arrayToJs = $medicalJobOrder->changeStatus($rsJO[$i]['pkey'], 4, '', false, true);
            if (!$arrayToJs[0]['valid'])
                throw new Exception('<strong>' . $rsHeader[0]['code'] . '</strong>. ' .  $arrayToJs[0]['message']);
        }
		 

	  	// dipindah, harusnya yg reset dari Job Order
		// update ulang referensi job order di quotation
		// satu case bisa lebih dr satu quotation
//        $medicalSalesOrderQuotation = new MedicalSalesOrderQuotation();
//        $rsMedicalSalesOrderQuotation = $medicalSalesOrderQuotation->searchDataRow( array($medicalSalesOrderQuotation->tableName . '.pkey'),
//																	 	' and ' . $medicalSalesOrderQuotation->tableName . '.refrequestkey = ' . $this->oDbCon->paramString($rsHeader[0]['pkey']) . ' 
//																		  and ' . $medicalSalesOrderQuotation->tableName . '.reftabletype = ' .  $this->oDbCon->paramString($rsMedicalRequestType['key'])
//																	);
//		
//		for ($i=0; $i<count($rsMedicalSalesOrderQuotation); $i++) {
//			$sql = 'update ' . $medicalSalesOrderQuotation->tableName.' set refkey = 0, refcode = \'\' where   pkey = '.$this->oDbCon->paramString($rsMedicalSalesOrderQuotation[$i]['pkey']);
//			$this->oDbCon->execute($sql);
//		} 


        if ($copy)
            $this->copyDataOnCancel($id);

        $this->cancelGLByRefkey($rsHeader[0]['pkey'], $this->tableName);
    }

	
    function generateDefaultQueryForAutoComplete($returnField){ 
        
        $sql = 'select
					'.$returnField['key'].',
					'.$returnField['value'].' as value,  
					'.$this->tableName.'.code as codelog,
					'.$this->tableName.'.insuredname,
					'.$this->tableName.'.insuredphone,
					'.$this->tableName.'.insuredemail,
					'.$this->tableName.'.insuredmobile,
					'.$this->tableName.'.insuredid, 
					'.$this->tableName.'.callername, 
					'.$this->tableName.'.relationtoinsured, 
					'.$this->tableName.'.customerkey, 
					'.$this->tableName.'.mobile, 
					'.$this->tableName.'.casephone, 
					'.$this->tableName.'.citykey, 
					'.$this->tableName.'.customerinsurancepolicykey, 
					'.$this->tableName.'.email, 
					'.$this->tableName.'.age,
					'.$this->tableCustomerInsurancePolicy.'.policynumber,
					'.$this->tableCustomerInsurancePolicy.'.dateofbirth,
					'.$this->tableCountry.'.name as countryname,
					'.$this->tableSupplier.'.name as insurancecompanyname ,
					'.$this->tableCustomerCategory.'.name as customercategoryname,
					'.$this->tableCustomer.'.name as companyname, 
					'.$this->tableName.'.address as caseaddress , 
					concat('.$this->tableCity.'.name,\', \', '.$this->tableCityCategory.'.name ) as casecityandcategoryname,
					'.$this->tableName.'.casephone, 
					'.$this->tableName.'.trdesc as casedescription
				from 
					'.$this->tableName . ' 
						left join '.$this->tableCustomer.'  on '.$this->tableName.'.customerkey = '.$this->tableCustomer.'.pkey 
						left join '.$this->tableCustomerInsurancePolicy.'  on '.$this->tableName.'.customerinsurancepolicykey = '.$this->tableCustomerInsurancePolicy.'.pkey 
						left join '.$this->tableCustomerCategory.'  on '.$this->tableName.'.customercategorykey = '.$this->tableCustomerCategory.'.pkey 
						left join '.$this->tableCountry.'  on '.$this->tableName.'.countrykey = '.$this->tableCountry.'.pkey 
						left join '.$this->tableCity.'  on '.$this->tableName.'.citykey = '.$this->tableCity.'.pkey 
						left join '.$this->tableCityCategory.'  on '.$this->tableCity.'.categorykey = '.$this->tableCityCategory.'.pkey 
						left join '.$this->tableSupplier.'  on '.$this->tableName.'.supplierkey = '.$this->tableSupplier.'.pkey , 
                    '.$this->tableStatus.' 
				where  		 
					'.$this->tableName . '.statuskey = '.$this->tableStatus.'.pkey  
			';
           
		 return $sql;
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
				  '.$this->tableInitialDiagnoseDetail.'.refkey in ('.$this->oDbCon->paramString($pkey,',').')';


		$sql .= $criteria;
		return $this->oDbCon->doQuery($sql);
	}

    function getDetailWithRelatedInformation($pkey, $criteria = ''){
        $sql = 'select
                    ' . $this->tableNameDetail . '.*,
                 ' . $this->tableItem . '.name as itemname, 
                 ' . $this->tableItem . '.isquotation, 
                 ' . $this->tableItem . '.code as itemcode
 
               from
                   ' . $this->tableNameDetail . ',
                 ' . $this->tableItem . '
               where
                   ' . $this->tableNameDetail . '.itemkey = ' . $this->tableItem . '.pkey and
                   refkey in (' . $this->oDbCon->paramString($pkey, ',') . ')';

        $sql .= $criteria;
 

        return $this->oDbCon->doQuery($sql);
    }
	
	 function reCountSubtotal($arrParam){
 
            $subtotal = 0 ;
            $grandtotal = 0;

            $arrItemKey = $arrParam['hidItemKey'];    
            $arrQty = $arrParam['qty']; 
            $arrPriceinunit = $arrParam['priceInUnit'];  

            $arrItemDetail = array(); 
        
            for ($i=0;$i<count($arrItemKey);$i++){

                if (empty($arrItemKey[$i])) continue; 

				$itemkey = $arrItemKey[$i]; 
				$qty =  $this->unFormatNumber($arrQty[$i]); 
				$priceInUnit = $this->unFormatNumber($arrPriceinunit[$i]);

				$detailSubtotal = $qty * $priceInUnit;  
				$arrItemDetail[$i]['detailSubtotal'] = $detailSubtotal;  

				$subtotal += $detailSubtotal ;  
				$totalGramasi += ($qty * $gramasi);
            } 

            $grandtotal = $subtotal;
    
            $reCountResult = array(); 
            $reCountResult['grandtotal'] = $grandtotal; 
            $reCountResult['detailCOGS'] = $arrItemDetail; 
 
            return $reCountResult;

    } 

    function normalizeParameter($arrParam, $trim = false) {
        
        $rsDetail = $this->getDetailById($arrParam['hidId']);
        $arrItemDetail = array_column($rsDetail, null, 'pkey');
		
		
        $arrParam['codeLog'] = $arrParam['code']; 
		
		// utk history, gk perlu validasi add / edit, karena kalo confirm udah gk bisa edit
		// di form jg perlu
		
		$customerInsurancePolicy = new CustomerInsurancePolicy();
		
		$rsPolicy = $customerInsurancePolicy->searchData( $customerInsurancePolicy->tableName.'.pkey', $arrParam['hidCustomerInsurancePolicyKey'] );
		
		$arrParam['hidCustomerCategoryKey'] = $rsPolicy[0]['customercategorykey'];
		$arrParam['hidCustomerKey'] = $rsPolicy[0]['refkey'];
		$arrParam['hidSupplierKey'] = $rsPolicy[0]['supplierkey'];
		$arrParam['insuredID'] = $rsPolicy[0]['idnumber'];
		$arrParam['dateOfBirth'] = $this->formatDBDate($rsPolicy[0]['dateofbirth']);
		$arrParam['selCountry'] = $rsPolicy[0]['countrykey']; 
		$arrParam['insuredMobile'] = $rsPolicy[0]['mobile']; 
		$arrParam['insuredPhone'] = $rsPolicy[0]['phone']; 
		$arrParam['insuredEmail'] = $rsPolicy[0]['email']; 
		
        //$this->arrData['age'] = array('age', 'number'); 
		
        $arrItemkey = $arrParam['hidItemKey'];
		$reCountResult = $this->reCountSubtotal($arrParam); 
		$arrParam['detailCOGS'] = $reCountResult['detailCOGS']; 
		$arrParam['grandtotal'] = $reCountResult['grandtotal']; 
		
		 for ($i=0;$i<count($arrItemkey);$i++){  
			$arrParam['detailSubtotal'][$i] = $arrParam['detailCOGS'][$i]['detailSubtotal']; 
   
			$hidDetailKey = $arrParam['hidDetailKey'][$i];
			 
            if (isset($arrItemDetail[$hidDetailKey]) && $arrItemDetail[$hidDetailKey]['statuskey'] == 2)  
                $arrParam['hidItemKey'][$i] = $arrItemDetail[$hidDetailKey]['itemkey'];
              
		}
		

        $arrParam = parent::normalizeParameter($arrParam, true);

        return $arrParam;
    }
 
}
