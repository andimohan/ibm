<?php

class MedicalSalesOrderQuotation extends BaseClass{

    function __construct()
    {

        parent::__construct();

        $this->tableName = 'medical_sales_order_quotation_header';
        $this->tableNameDetail = 'medical_sales_order_quotation_detail';
        $this->tableCustomer = 'customer';
        $this->tableCustomerCategory = 'customer_category';
        $this->tableMedicalJobOrder = 'medical_job_order_header';
        $this->tableMedicalRequestClaim = 'medical_request_claim_header';
        $this->tableSupplier = 'supplier';
        $this->tableCity = 'city';
        $this->tableCustomerInsurancePolicy = 'customer_insurance_policy';
        $this->tableWarehouse = 'warehouse';
        $this->tableStatus = 'transaction_status';
        $this->tableItem = 'item';
        $this->tableItemCategory = 'item_category';
        $this->tableItemUnit = 'item_unit';
        $this->isTransaction = true;
        $this->newLoad = true;

        $this->securityObject = 'MedicalSalesOrderQuotation';

        $this->arrDataDetail = array();
        $this->arrDataDetail['pkey'] = array('hidDetailKey');
        $this->arrDataDetail['refkey'] = array('pkey', 'ref');
        $this->arrDataDetail['itemkey'] = array('hidItemKey');
        $this->arrDataDetail['qty'] = array('qty', 'number');
        $this->arrDataDetail['priceinunit'] = array('priceInUnit', 'number');
        $this->arrDataDetail['unitkey'] = array('selUnit');
        $this->arrDataDetail['trdesc'] = array('detailDescription');
        $this->arrDataDetail['total'] = array('detailSubtotal', 'number');
        $this->arrDataDetail['costinbaseunit'] = array('cogs', 'number');
        $this->arrDataDetail['qtyinbaseunit'] = array('qtyInBaseUnit', 'number');
        $this->arrDataDetail['priceinbaseunit'] = array('priceInBaseUnit', 'number');


        $arrDetails = array();
        array_push($arrDetails, array('dataset' => $this->arrDataDetail));

        $this->arrData = array();
        $this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails));
        $this->arrData['code'] = array('code');
        $this->arrData['refcode'] = array('medicalJobOrderCode');
        $this->arrData['refkey'] = array('hidMedicalJobOrderkey');
        $this->arrData['refrequestcode'] = array('medicalRequestClaimCode');
        $this->arrData['refrequestkey'] = array('hidMedicalRequestClaimKey');
        $this->arrData['customcodekey'] = array('selCustomCode');
        $this->arrData['trdate'] = array('trDate', 'date');
        $this->arrData['warehousekey'] = array('selWarehouseKey');
        $this->arrData['customerkey'] = array('hidCustomerKey');
        $this->arrData['termofpaymentkey'] = array('selTermOfPaymentKey');
        $this->arrData['trdesc'] = array('trDesc');
        $this->arrData['subtotal'] = array('subtotal', 'number');
        $this->arrData['finaldiscounttype'] = array('selFinalDiscountType', 'number');
        $this->arrData['finaldiscount'] = array('finalDiscount', 'number');
        $this->arrData['beforetaxtotal'] = array('beforeTaxTotal', 'number');
        $this->arrData['ispriceincludetax'] = array('chkIncludeTax');
        $this->arrData['taxpercentage'] = array('taxPercentage', 'number');
        $this->arrData['taxvalue'] = array('taxValue', 'number');
        $this->arrData['etccost'] = array('etcCost', 'number');
        $this->arrData['grandtotal'] = array('total', 'number');
        $this->arrData['statuskey'] = array('selStatus');
        $this->arrData['attention'] = array('attention');
        $this->arrData['reftabletype'] = array('selJOType');
        $this->arrData['guaranteetype'] = array('selGuaranteeType');
        //$this->arrData['supplierkey'] = array('hidSupplierKey');
        $this->arrData['customerinsurancepolicykey'] = array('hidCustomerInsurancePolicyKey');


        $this->arrDataListAvailableColumn = array();
        array_push($this->arrDataListAvailableColumn, array('code' => 'code', 'title' => 'code', 'dbfield' => 'code', 'default' => true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'codeLog', 'title' => 'log', 'dbfield' => 'codelog', 'default' => true, 'width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'date', 'title' => 'date', 'dbfield' => 'trdate', 'default' => true, 'width' => 100, 'align' => 'center', 'format' => 'date'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'warehouse', 'title' => 'warehouse', 'dbfield' => 'warehousename', 'default' => true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'company', 'title' => 'company', 'dbfield' => 'companyname', 'default' => true, 'width' => 130));
        array_push($this->arrDataListAvailableColumn, array('code' => 'policynumber', 'title' => 'policyNumber', 'dbfield' => 'policynumber', 'default' => true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'name', 'title' => 'insuredName', 'dbfield' => 'insuredname', 'default' => true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'total', 'title' => 'total', 'dbfield' => 'grandtotal', 'default' => true, 'width' => 100, 'align' => 'right', 'format' => 'number'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status', 'title' => 'status', 'dbfield' => 'statusname', 'default' => true, 'width' => 70));
        array_push($this->arrDataListAvailableColumn, array('code' => 'desc', 'title' => 'note', 'dbfield' => 'trdesc', 'width' => 200));


        $this->printMenu = array();
        array_push($this->printMenu, array('code' => 'printInvoice', 'name' => $this->lang['printTransaction'],  'icon' => 'print', 'url' => 'print/medicalSalesOrderQuotation'));

        $this->arrSearchColumn = array();
        array_push($this->arrSearchColumn, array('Kode', $this->tableName . '.code'));
        array_push($this->arrSearchColumn, array('insuredName', $this->tableMedicalRequestClaim . '.insuredname'));
        array_push($this->arrSearchColumn, array('company', $this->tableCustomer . '.name'));
        array_push($this->arrSearchColumn, array('status', $this->tableStatus . '.status'));
        array_push($this->arrSearchColumn, array('JOCode', $this->tableMedicalJobOrder . '.code'));
        array_push($this->arrSearchColumn, array('codeLog', $this->tableMedicalRequestClaim . '.code'));


        $this->includeClassDependencies(array(
            'MedicalJobOrder.class.php',
            'MedicalRequestClaim.class.php',
            'Warehouse.class.php',
            'City.class.php',
            'Customer.class.php',
            'Item.class.php', 
            'ItemUnit.class.php',
            'Category.class.php',
            'CustomerCategory.class.php',
            'CustomerInsurancePolicy.class.php', 
            'Supplier.class.php'
        ));

        $this->actionMenu = array();

        //array_push($this->filterCriteria, array('title' => $this->lang['warehouse'], 'field' => 'warehousekey')); 

        $this->overwriteConfig();
    }

    function getQuery(){

		// semua transaksi mengacu ke no new case / no log.
		// meskipun quote dr job order, tetep konekin ke neww case nya / no log nya
		// tableCustomerInsurancePolicy hanya utk ambil no polis
		
		//  left join ' . $this->tableCustomerCategory . ' on ' . $this->tableMedicalRequestClaim . '.customercategorykey = ' . $this->tableCustomerCategory . '.pkey  
						
        $sql = '
                 select
                     ' . $this->tableName . '.*,  
                     ' . $this->tableMedicalRequestClaim . '.code as codelog,
                     ' . $this->tableMedicalRequestClaim . '.insuredname,
                     ' . $this->tableCustomerInsurancePolicy . '.policynumber, 
                     ' . $this->tableMedicalJobOrder . '.code as jobordercode,
                     ' . $this->tableWarehouse . '.name as warehousename, 
                     ' . $this->tableCustomer . '.name as companyname,  
                     ' . $this->tableCustomerCategory . '.name as categoryname,
                     ' . $this->tableSupplier . '.name as insurancecompanyname,  
                     ' . $this->tableStatus . '.status as statusname
                 from 
                     ' . $this->tableName . '
						 left join ' . $this->tableMedicalJobOrder . ' on ' . $this->tableName . '.refkey = ' . $this->tableMedicalJobOrder . '.pkey  
						 left join ' . $this->tableMedicalRequestClaim . ' on ' . $this->tableName . '.refrequestkey = ' . $this->tableMedicalRequestClaim . '.pkey  
						 left join ' . $this->tableCustomer . ' on ' . $this->tableMedicalRequestClaim . '.customerkey = ' . $this->tableCustomer . '.pkey  
						 left join ' . $this->tableCustomerCategory . ' on ' . $this->tableMedicalRequestClaim . '.customercategorykey = ' . $this->tableCustomerCategory . '.pkey  
						 left join ' . $this->tableSupplier . ' on ' . $this->tableMedicalRequestClaim . '.supplierkey = ' . $this->tableSupplier . '.pkey  
						 left join ' . $this->tableCustomerInsurancePolicy . ' on ' . $this->tableMedicalRequestClaim . '.customerinsurancepolicykey = ' . $this->tableCustomerInsurancePolicy . '.pkey,  
                     ' . $this->tableStatus . ',
                     ' . $this->tableWarehouse . '  
                 where  		
                     ' . $this->tableName . '.statuskey = ' . $this->tableStatus . '.pkey and
                     ' . $this->tableName . '.warehousekey = ' . $this->tableWarehouse . '.pkey  
          ' . $this->criteria;
		 
        return $sql;
    }
	
	   
	function getDetailDiagnose($pkey,$criteria=''){
        
			$sql = 'select
				   '.$this->tableInitialDiagnoseDetail .'.*,
				   '.$this->tableInitialDiagnose .'.name as initialdiagnose
			  from
				  '. $this->tableInitialDiagnoseDetail .' 
				  left join ' . $this->tableInitialDiagnose . ' on ' . $this->tableInitialDiagnoseDetail . '.initialdiagnosekey = ' . $this->tableInitialDiagnose . '.pkey 
			  where  
				  '.$this->tableInitialDiagnoseDetail .'.refkey = '.$this->oDbCon->paramString($pkey);


		$sql .= $criteria;
		return $this->oDbCon->doQuery($sql);
	}



    function reCountSubtotal($arrParam) {

        $item = new Item();

        // default, ongkir dan cost dibagi berdasarkan proporsional gramasi/kubikasi
        //$useGramasi = $this->loadSetting('costProportionalType');

        $isPriceIncludeTax = (!empty($arrParam['chkIncludeTax'])) ? 1 : 0;

        $subtotal = 0;
        $grandtotal = 0;
        //$gramasi = 0;

        $arrItemkey = $arrParam['hidItemKey'];
        $taxValue = $this->unFormatNumber($arrParam['taxValue']);
        $finalDiscount = $this->unFormatNumber($arrParam['finalDiscount']);
        $finalDiscountType = $arrParam['selFinalDiscountType'];
        $taxPercentage = $this->unFormatNumber($arrParam['taxPercentage']);
        $etcCost = $this->unFormatNumber($arrParam['etcCost']);

        $arrQty = $arrParam['qty'];
        $arrPriceinunit = $arrParam['priceInUnit'];
        $arrTransUnitKey = $arrParam['selUnit'];

        $arrItemDetail = array();

        for ($i = 0; $i < count($arrItemkey); $i++) {

            if (empty($arrItemkey[$i]))  continue;

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
 
            $detailSubtotal = $qty * $priceInUnit;
            $arrItemDetail[$i]['detailSubtotal'] = $detailSubtotal;

            $subtotal += $detailSubtotal;
 
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

        $grandtotal +=  $etcCost;
 
        $reCountResult = array();
        $reCountResult['subtotal'] = $subtotal;
        $reCountResult['beforeTaxTotal'] = $beforeTaxTotal;
        $reCountResult['isPriceIncludeTax'] = $isPriceIncludeTax;
        $reCountResult['grandtotal'] = $grandtotal;
        $reCountResult['detailCOGS'] = $arrItemDetail;

        return $reCountResult;
    }

    function validateCancel($rsHeader, $autoChangeStatus = false){
		 
		$medicalRequestClaim = new MedicalRequestClaim();
		$typekey = $medicalRequestClaim->getTableKeyAndObj($medicalRequestClaim->tableName, array('key'));
		
		// cari statusnya new case dulu, JO perlakuannya mungkin berbeda
		if($typekey['key'] == $rsHeader[0]['reftabletype']){
			$rs = $medicalRequestClaim->searchDataRow(array($medicalRequestClaim->tableName.'.pkey',$medicalRequestClaim->tableName.'.code'),
							  ' and pkey = '.$this->oDbCon->paramString($rsHeader[0]['refrequestkey']).'
								and  '.$medicalRequestClaim->tableName.'.statuskey  in (2,3) ');
			
			if(!empty($rs)) 
				$this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' . $this->errorMsg[201].' ' .$this->errorMsg[225]);
	 	}
		  
    }

	function getRequestObj($typekey){
		$medicalRequestClaim = new MedicalRequestClaim();
		$rsMedicalRequestType = $medicalRequestClaim->getTableKeyAndObj($medicalRequestClaim->tableName, array('key'));
		
		//$this->setLog($typekey .'=='. $rsMedicalRequestType['key'],true);
		
		return ($typekey == $rsMedicalRequestType['key']) ?
					array('obj' => $medicalRequestClaim, 'refField' => 'refrequestkey', 'param' => 'hidMedicalRequestClaimKey')  : 
					array('obj' => new MedicalJobOrder(), 'refField' => 'refkey', 'param' => 'hidMedicalJobOrderkey');
	}
	
    function validateConfirm($rsHeader) {
		$item = new Item();
		
		$obfRef = $this->getRequestObj($rsHeader[0]['reftabletype']);
		$obj = $obfRef['obj'];
		$refField = $obfRef['refField'];
		 
		$rs = $obj->getDataRowById($rsHeader[0][$refField]); 
		$rsDetail = $this->getDetailById($rsHeader[0]['pkey']); 
		$arrItemkey = array_column($rsDetail,'itemkey');
		 
		// $arrItemkey detail
		$rsItemCol = $item->searchDataRow(array($item->tableName.'.pkey',$item->tableName.'.name'),
										  ' and '.$item->tableName.'.pkey in ('.$this->oDbCon->paramString($arrItemkey, ',').')'
										 );
		$rsItemCol =  array_column($rsItemCol,null,'pkey');
		
		// detail obj tujuan (new request / jo)	
		$rsObjDetail = $obj->getDetailById($rsHeader[0][$refField]); 
		$rsDetailItemRef = array_column($rsObjDetail,null,'itemkey');
		
			
		// untuk new request, begitu di konfirmasi, langsung di close saja 
		if (in_array($rs[0]['statuskey'], array(3,4))) { // kalo udah selesai gk boleh confirm
			$this->addErrorLog(false, '<strong>' . $rsHeader[0]['code'] . ', ' . $rs[0]['code'] . '</strong>. ' . $this->errorMsg[227]);
		}
		
		foreach($arrItemkey as $itemkey){
			if(!isset($rsDetailItemRef[$itemkey]))
				$this->addErrorLog(false, '<strong>' . $rsHeader[0]['code'] . ', ' . $rsItemCol[$itemkey]['name'] . '</strong>. ' . $this->errorMsg['medicalSalesOrderQuotation'][3]);
			elseif ($rsDetailItemRef[$itemkey]['statuskey'] <> 1)
				$this->addErrorLog(false, '<strong>' . $rsHeader[0]['code'] . ', ' . $rsItemCol[$itemkey]['name'] . '</strong>. ' . $this->errorMsg['medicalSalesOrderQuotation'][4]);
				
		}
		
		// cek detailnya jg sudah pernah diproses quotation atau blm 

    }


    function validateForm($arr, $pkey = ''){
        $arrayToJs = parent::validateForm($arr, $pkey);
        $item = new Item();
        $arrItemkey = $arr['hidItemKey'];
        $arrQuantity = $arr['qty'];
        $arrPrice = $arr['priceInUnit'];
    	$rsDetail = array();
		
		// validasi dulu biar gk error dibawah
	 	if (empty($arr['hidMedicalRequestClaimKey']) && empty($arr['hidMedicalJobOrderkey']) ) {  
			$this->addErrorList($arrayToJs, false, $this->errorMsg['reference'][1]);
			return 	$arrayToJs;
		}
		
		$obfRef = $this->getRequestObj($arr['selJOType']);
		$obj = $obfRef['obj']; 
		$param = $obfRef['param'];
		
		$rsDetail = $obj->getDetailById($arr[$param]);
		
		// pastikan itemkey nya semua terdaftar di request dan statusnya blm diapprove 
		$arrRequestItemDetail = array_column($rsDetail,null,'itemkey');
		  
		$rsItemCol = $item->searchDataRow(array($item->tableName.'.pkey',$item->tableName.'.name'),
										  ' and '.$item->tableName.'.pkey in ('.$this->oDbCon->paramString($arrItemkey, ',').')'
										 );
		$rsItemCol =  array_column($rsItemCol,null,'pkey');
		
        $arrDetailKeys = array();
		
		// jika tdk ada baris sama sekali, termasuk baris kosong
		// terjadi ketika import dari job order misalnya tp tidak ad detail 
		if(count($arrItemkey) <= 0)
			   $this->addErrorList($arrayToJs, false, $this->errorMsg['service'][1]);
 
        for ($i = 0; $i < count($arrItemkey); $i++) {
			
            if (empty($arrItemkey[$i])) {
                $this->addErrorList($arrayToJs, false, $this->errorMsg['service'][1]);
            } else {

				$itemkey = $arrItemkey[$i];
				$rsItem = $rsItemCol[$itemkey];
 
				// cek item terdaftar tdk di request
				if(!isset($arrRequestItemDetail[$itemkey]))
					  $this->addErrorList($arrayToJs, false, $rsItem['name'] . '. ' . $this->errorMsg['medicalSalesOrderQuotation'][3]);
				else if ($arrRequestItemDetail[$itemkey]['statuskey'] <> 1)
					  $this->addErrorList($arrayToJs, false, $rsItem['name'] . '. ' . $this->errorMsg['medicalSalesOrderQuotation'][4]);
				
                // cek harga dan jumlah != 0
                if ($this->unFormatNumber($arrQuantity[$i]) <= 0 || $this->unFormatNumber($arrPrice[$i]) <= 0) { 
                    $this->addErrorList($arrayToJs, false, $rsItem['name'] . '. ' . $this->errorMsg[500]);
                }

                // cek detail double 
                if (in_array($itemkey, $arrDetailKeys)) { 
                    $this->addErrorList($arrayToJs, false, $rsItem['name'] . '. ' . $this->errorMsg[215]);
                } else {
                    array_push($arrDetailKeys, $itemkey);
                }
            }
        }

        return $arrayToJs;
    }

 

    function getDetailWithRelatedInformation($pkey, $criteria = ''){ 

        $sql = 'select
	   			' . $this->tableNameDetail . '.*, 
                ' . $this->tableItem . '.name as itemname, 
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
	
	function confirmTrans($rsHeader){
		$pkey = $rsHeader[0]['pkey'];
		
		$rsDetail = $this->getDetailById($pkey);
		$obRef = $this->getRequestObj($rsHeader[0]['reftabletype']);
		$obj = $obRef['obj'];
		$refField = $obRef['refField'];
		 
		// kalo utk support item yg ad 2, better pake pkey.
		
		$sql = 'update 
					'.$obj->tableNameDetail.' 
				set 
					statuskey = 2, refquotationkey = '. $this->oDbCon->paramString($pkey).'
				where 
					refkey = '.$this->oDbCon->paramString($rsHeader[0][$refField]).' and 
					itemkey in ('.$this->oDbCon->paramString(array_column($rsDetail,'itemkey'),',').')'; 
		
		//$this->setLog($sql,true);
		$this->oDbCon->execute($sql);
		 
	}
	
	function cancelTrans($rsHeader,$copy){
		$id = $rsHeader[0]['pkey'];
		
		$rsDetail = $this->getDetailById($id);
		$obRef = $this->getRequestObj($rsHeader[0]['reftabletype']);
		$obj = $obRef['obj'];
		$refField = $obRef['refField'];
		 
		// kalo utk support item yg ad 2, better pake pkey.
		
		$sql = 'update 
					'.$obj->tableNameDetail.' 
				set 
					statuskey = 1, refquotationkey = 0 
				where 
					refkey =  '.$this->oDbCon->paramString($rsHeader[0][$refField]).' and 
					refquotationkey =  '.$this->oDbCon->paramString($id)
				;
		
		$this->oDbCon->execute($sql);
		
        if ($copy) 
			$this->copyDataOnCancel($id); 
	}

   function afterStatusChanged($rsHeader) {
    	$this->countQuotation($rsHeader[0]['refrequestkey']); 

        $medicalJobOrder = new MedicalJobOrder();

        $medicalRequestClaim = new MedicalRequestClaim();

        $this->setActivityTransactionLogDetail($rsHeader[0]['pkey'], $medicalRequestClaim, $rsHeader[0]['refrequestkey']);
        if (!empty($rsHeader[0]['refkey'])) {
            $this->setActivityTransactionLogDetail($rsHeader[0]['pkey'], $medicalJobOrder, $rsHeader[0]['refkey']);
        }
    }

//    function checkQuotation($pkey, $itemkey, $reftabletype)  {
//        $medicalJobOrder = new MedicalJobOrder();
//        $rsMedicalJobOrderType = $medicalJobOrder->getTableKeyAndObj($medicalJobOrder->tableName, array('key'));
//
//        if ($reftabletype == $rsMedicalJobOrderType['key']) {
//            $rsMedicalSalesOrderQuotation = $this->searchDataRow(
//                array($this->tableName . '.pkey', $this->tableName . '.statuskey'),
//                ' and ' . $this->tableName . '.refkey = ' . $this->oDbCon->paramString($pkey). ' and ' . $this->tableName . '.statuskey in (2,3,4)');
//        } else {
//            $rsMedicalSalesOrderQuotation = $this->searchDataRow(
//                array($this->tableName . '.pkey', $this->tableName . '.statuskey'),
//                ' and ' . $this->tableName . '.refrequestkey = ' . $this->oDbCon->paramString($pkey). ' and ' . $this->tableName . '.statuskey in (2,3,4)');
//        }
//
//        $data = array();
//        for ($k = 0; $k < count($rsMedicalSalesOrderQuotation); $k++) {
//            // $rsMedicalSalesOrderQuotationDetail = $this->getDetailWithRelatedInformation($rsMedicalSalesOrderQuotation[$k]['pkey']);
//            $rsDetail = $this->getDetailByColumn('refkey', $rsMedicalSalesOrderQuotation[$k]['pkey']);
//            $arrItemDetailKey = array_column($rsDetail, 'itemkey');
//            $rsDetailKey = array_column($rsDetail, 'pkey');
//            // $rsMedicalSalesOrderQuotationDetailItemKey = array_column($rsMedicalSalesOrderQuotationDetail, 'itemkey');
//            $rsMedicalSalesOrderQuotation[$k]['detail'] = $rsDetail;
//            // $rsMedicalSalesOrderQuotation[$k]['itemkey'] = $itemDetailKey;
//            if(in_array($itemkey, $arrItemDetailKey)){
//                $rsDetail['statuskey'] = $rsMedicalSalesOrderQuotation[$k]['statuskey'];
//                array_push($data, $rsDetail);
//            }
//        }
//        // $this->setLog($data, true);
//        return $data;
//
//            // $sql = 'select
//            //        '.$this->tableName .'.pkey,
//            //        '.$this->tableName .'.statuskey as statusheader,
//            //        '.$this->tableNameDetail .'.pkey as detailkey,
//            //        '.$this->tableNameDetail .'.itemkey
//            //   from
//            //       '. $this->tableName .' 
//            //       left join ' . $this->tableNameDetail . ' on ' . $this->tableName . '.pkey = ' . $this->tableNameDetail . '.refkey 
//            //   where  
//            //       ' . $this->tableNameDetail . '.refkey in (' . $this->oDbCon->paramString($pkey, ',') . ') ';
//
//
//        // $sql .= $criteria;
//        // $this->setLog($sql, true);
//        // return $this->oDbCon->doQuery($sql);
//    }

    function afterUpdateData($arrParam, $action)  {
		// sementara utk request saja
        $this->countQuotation($arrParam['hidMedicalRequestClaimKey']);

            $medicalJobOrder = new MedicalJobOrder();

            $medicalRequestClaim = new MedicalRequestClaim();
            $this->setActivityTransactionLogDetail($arrParam['pkey'], $medicalRequestClaim, $arrParam['hidMedicalRequestClaimKey']);
            if (!empty($arrParam['hidMedicalJobOrderkey'])) {
                $this->setActivityTransactionLogDetail($arrParam['pkey'], $medicalJobOrder, $arrParam['hidMedicalJobOrderkey']);
            }

    }


    function countQuotation($pkey)  {

        $medicalRequestClaim = new MedicalRequestClaim();
 
        $totalQuotation = count($this->searchDataRow(array('pkey'), ' and ' . $this->tableName . '.refrequestkey = ' . $this->oDbCon->paramString($pkey) . ' and (' . $this->tableName . '.statuskey in (1,2,3))'));
        $totalQuotationApproved = count($this->searchDataRow(array('pkey'), ' and ' . $this->tableName . '.refrequestkey = ' . $this->oDbCon->paramString($pkey) . ' and (' . $this->tableName . '.statuskey in (2,3))'));

        try {

            if (!$this->oDbCon->startTrans())
                throw new Exception($this->errorMsg[100]);


            $sql = 'update 
                            ' . $medicalRequestClaim->tableName . '
                        set 
                            quotetotal = ' . $this->oDbCon->paramString($totalQuotation) . ', 
                            quoteapproved = ' . $this->oDbCon->paramString($totalQuotationApproved) . ' 
                        where  
                            pkey = ' . $this->oDbCon->paramString($pkey) . ' 
                        ';

            $this->oDbCon->execute($sql);
 
            $this->oDbCon->endTrans();
        } catch (Exception $e) {
            $this->oDbCon->rollback();
        }
    }

    function normalizeParameter($arrParam, $trim = false){
  
  
        $medicalRequestClaim = new MedicalRequestClaim();
        
        $rsMedicalRequestType = $medicalRequestClaim->getTableKeyAndObj($medicalRequestClaim->tableName, array('key'));

        if ($arrParam['selJOType'] == $rsMedicalRequestType['key']) {
			// kalo dr request
            $arrParam['hidMedicalJobOrderkey'] = 0; 
        }else{
			// kalo dr job
            // tarik ulang request key
			$medicalJobOrder = new MedicalJobOrder();
			$rsJob = $medicalJobOrder->searchDataRow( array($medicalJobOrder->tableName.'.refkey') ,' and '. $medicalJobOrder->tableName.'.pkey = ' .$this->oDbCon->paramString($arrParam['hidMedicalJobOrderkey']) );
			
			$arrParam['hidMedicalRequestClaimKey'] = $rsJob[0]['refkey'];  
		}

        $arrItemkey = $arrParam['hidItemKey'];
		
        $reCountResult = $this->reCountSubtotal($arrParam);
        $arrParam['detailCOGS'] = $reCountResult['detailCOGS'];
        $arrParam['subtotal'] = $reCountResult['subtotal'];
        $arrParam['beforeTaxTotal'] = $reCountResult['beforeTaxTotal'];
        $arrParam['isPriceIncludeTax'] = $reCountResult['isPriceIncludeTax'];
        $arrParam['grandtotal'] = $reCountResult['grandtotal'];

        for ($i = 0; $i < count($arrItemkey); $i++) { 
            $qtyinbaseunit = $arrParam['detailCOGS'][$i]['qtyInBaseUnit'];
            $arrParam['qtyInBaseUnit'][$i] = $qtyinbaseunit; 
            $arrParam['priceInBaseUnit'][$i] = $arrParam['detailCOGS'][$i]['priceInBaseUnit'];
            $arrParam['detailSubtotal'][$i] = $arrParam['detailCOGS'][$i]['detailSubtotal'];
        }
		
		$arrParam = parent::normalizeParameter($arrParam, true);
 
        return $arrParam;
    }
}
