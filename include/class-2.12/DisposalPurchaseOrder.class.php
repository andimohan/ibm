<?php
class DisposalPurchaseOrder extends BaseClass{
    // CustomerTypeCategory
    function __construct(){

        parent::__construct();

        $this->tableName = 'disposal_purchase_order_header';
        $this->tableNameDetail = 'disposal_purchase_order_detail';
        $this->tableStatus = 'transaction_status';
        $this->tableSupplier = 'supplier';
        $this->isTransaction = true;
        $this->tableFile = 'disposal_purchase_order_file';
        $this->tableDisposalWorkOrderDispatcher = 'disposal_work_order_dispatcher_header';
        $this->tableWaste = 'waste';
        $this->tableTax = 'tax';
        $this->tableWareHouse = 'warehouse';
        $this->tablePayment = 'disposal_purchase_order_payment';
        $this->uploadFileFolder = 'disposal-purchase-order-file/';
        $this->newLoad = true;
        $this->securityObject = 'DisposalPurchaseOrder';

        $this->arrDataDetail = array();
        $this->arrDataDetail['pkey'] = array('hidDetailKey');
        $this->arrDataDetail['refkey'] = array('pkey', 'ref');
        $this->arrDataDetail['wastekey'] = array('hidWasteKey');
        $this->arrDataDetail['weightdetail'] = array('weightDetail', 'number');
        $this->arrDataDetail['priceinunit'] = array('priceInUnit', 'number');
        $this->arrDataDetail['subtotaldetailbeforetax'] = array('subTotalDetailBeforeTax', 'number');
        $this->arrDataDetail['taxpercentage'] = array('taxPercentage', 'number');
        $this->arrDataDetail['ispriceincludetax'] = array('chkIncludeTaxDetail');
        $this->arrDataDetail['total'] = array('subTotalDetail', 'number');
        $this->arrDataDetail['taxvaluedetail'] = array('taxValueDetail', 'number');
        $this->arrDataDetail['taxkey'] = array('taxDetailKey');

        $this->arrPaymentDetail = array();
        $this->arrPaymentDetail['pkey'] = array('hidDetailPaymentKey');
        $this->arrPaymentDetail['refkey'] = array('pkey', 'ref');
        $this->arrPaymentDetail['amount'] = array('paymentMethodValue', array('datatype' => 'number', 'mandatory' => true));
        $this->arrPaymentDetail['paymentkey'] = array('selPaymentMethod', array('mandatory' => true));

        $arrDetails = array();
        array_push($arrDetails, array('dataset' => $this->arrDataDetail, 'tableName' => $this->tableNameDetail));
        array_push($arrDetails, array('dataset' => $this->arrPaymentDetail, 'tableName' => $this->tablePayment));
        array_push($arrDetails, array(
            'dataset' => $this->arrDataFile, 'tableName' => $this->tableFile,
            'datatype' => 'file', 'uploadFolder' => $this->uploadFileFolder,
            'token' => 'token-item-file-uploader', 'fileName' => 'item-file-uploader'
        ));

        $this->arrData = array();
        $this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails));
        $this->arrData['code'] = array('code');
        // $this->arrData['name'] = array('name');
        $this->arrData['trdate'] = array('trDate', 'date');
        $this->arrData['trdesc'] = array('trDesc');
        $this->arrData['supplierkey'] = array('hidSupplierKey');
        $this->arrData['warehousekey'] = array('selWarehouseKey');
        $this->arrData['refinvoicecode'] = array('refInvoiceCode');
        $this->arrData['statuskey'] = array('selStatus');
        $this->arrData['totalweight'] = array('totalWeight', 'number');
        $this->arrData['balance'] = array('balance', 'number');
        $this->arrData['termofpaymentkey'] = array('selTermOfPayment'); 
        $this->arrData['taxvalue'] = array('taxValue', 'number');
        $this->arrData['subtotal'] = array('subtotal', 'number');
        $this->arrData['beforetaxtotal'] = array('beforeTaxTotal', 'number');
        $this->arrData['grandtotal'] = array('total', 'number');
        $this->arrData['totalpayment'] = array('totalPayment', 'number');
        $this->arrData['totalweight'] = array('totalWeight', 'number');
        $this->arrData['dispatchkey'] = array('hidDispatchKey');

        $this->arrDataListAvailableColumn = array();
        array_push($this->arrDataListAvailableColumn, array('code' => 'code', 'title' => 'code', 'dbfield' => 'code', 'default' => true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'date', 'title' => 'date', 'dbfield' => 'trdate', 'default' => true, 'width' => 80, 'align' => 'center', 'format' => 'date'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'supplier', 'title' => 'supplier', 'dbfield' => 'suppliername', 'default' => true, 'width' => 250));
        array_push($this->arrDataListAvailableColumn, array('code' => 'workOrderDispatcherCode', 'title' => 'workOrderDispatcherCode', 'dbfield' => 'dispatchcode', 'default' => true, 'width' => 250));
        array_push($this->arrDataListAvailableColumn, array('code' => 'invoiceReference', 'title' => 'invoiceReference', 'dbfield' => 'refinvoicecode', 'default' => true, 'width' => 160));
        array_push($this->arrDataListAvailableColumn, array('code' => 'totalWeight', 'title' => 'totalWeight', 'dbfield' => 'totalweight', 'default' => true, 'width' => 100, 'align' => 'right', 'format' => 'number'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'note', 'title' => 'note', 'dbfield' => 'trdesc', 'default' => true, 'width' => 250));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status', 'title' => 'status', 'dbfield' => 'statusname', 'default' => true, 'width' => 70));

        $this->arrSearchColumn = array();
        array_push($this->arrSearchColumn, array('Kode', $this->tableName . '.code'));
        array_push($this->arrSearchColumn, array('invoiceReference', $this->tableName . '.refinvoicecode'));
        array_push($this->arrSearchColumn, array('Supplier', $this->tableSupplier . '.name'));
        array_push($this->arrSearchColumn, array('workOrderDispatcherCode', $this->tableDisposalWorkOrderDispatcher . '.code'));
        array_push($this->arrSearchColumn, array('status', $this->tableStatus . '.status'));
        $this->overwriteConfig();


        $this->printMenu = array();
        array_push($this->printMenu, array('code' => 'printTransaction', 'name' => $this->lang['printTransaction'],  'icon' => 'print', 'url' => 'print/disposalPurchaseOrder'));

        $this->includeClassDependencies(array(
            'TermOfPayment.class.php',
            'DisposalWorkOrderDispatcher.class.php',
            'DisposalWorkOrder.class.php',
            'Waste.class.php',
            'AP.class.php',
            'GeneralJournal.class.php',
            'COALink.class.php',
            'Supplier.class.php'
        ));
    }



    function getQuery()
    {
        return '
				select
					' . $this->tableName . '.*,
					' . $this->tableSupplier . '.name as suppliername,
					' . $this->tableWareHouse . '.name as warehousename,
					' . $this->tableDisposalWorkOrderDispatcher . '.code as dispatchcode,
					' . $this->tableStatus . '.status as statusname
				from 
                    ' . $this->tableStatus . ',
					' . $this->tableName . '
                        left join ' . $this->tableSupplier . ' on ' . $this->tableName . '.supplierkey = ' . $this->tableSupplier . '.pkey  
                        left join ' . $this->tableWareHouse . ' on ' . $this->tableName . '.warehousekey = ' . $this->tableWareHouse . '.pkey  
                        left join ' . $this->tableDisposalWorkOrderDispatcher . ' on ' . $this->tableName . '.dispatchkey = ' . $this->tableDisposalWorkOrderDispatcher . '.pkey  
				where  		
					' . $this->tableName . '.statuskey = ' . $this->tableStatus . '.pkey
 		' . $this->criteria;
    }


    function validateForm($arr, $pkey = '')
    {

        $arrayToJs = parent::validateForm($arr, $pkey);
        $supplierKey = $arr['hidSupplierKey'];
        $dispatchKey = $arr['hidDispatchKey'];
        $arrWastKey = $arr['hidWasteKey'];
        $refInvoiceCode = $arr['refInvoiceCode'];

        if (empty($supplierKey)) {
            $this->addErrorList($arrayToJs, false, $this->errorMsg['customer'][1]);
        }

        if(empty($dispatchKey)) 
            $this->addErrorList($arrayToJs,false,  $this->errorMsg['disposalWorkOrderDispatcher'][1]);  

        if(empty($arrWastKey)) 
            $this->addErrorList($arrayToJs,false,  $this->errorMsg[501]);  

        if(empty($refInvoiceCode)) 
            $this->addErrorList($arrayToJs,false,  $this->errorMsg['invoice'][1]);  


        return $arrayToJs;
    }

    function confirmTrans($rsHeader){
		   
        $id = $rsHeader[0]['pkey'];
        
		$supplier = new Supplier();
		$warehouse = new Warehouse();
        $coaLink = new COALink();
		
		$rsSupplier = $supplier->getDataRowById($rsHeader[0]['supplierkey']);
		$note = $rsHeader[0]['code'].'. Beli dari '.$rsSupplier[0]['name'];
		$rsWarehouse = $warehouse->getDataRowById($rsHeader[0]['warehousekey']);
		$notecash = $rsHeader[0]['code'].'. Kas Keluar dari '.$rsWarehouse[0]['name'].' untuk pembelian barang dari '.$rsSupplier[0]['name'];
		$rsDetail = $this->getDetailById($rsHeader[0]['pkey']);
		 
		$termOfPayment = new TermOfPayment();
		$rsTOP = $termOfPayment->getDataRowById($rsHeader[0]['termofpaymentkey']);  
		$isCash = ($rsTOP[0]['duedays'] == 0) ? true : false; 
	   
        $rsPayment = array();
        
		// MENGHITUNG PAYMENT
			if (!$isCash){
				//update AP
				$ap = new AP();
				
				$arrParam = array();	
                 
                $rsAPKey = $ap->getTableKeyAndObj($this->tableName,array('key')); 
                $arrParam['code'] = 'xxxxxx';
				$arrParam['hidSupplierKey'] = $rsHeader[0]['supplierkey'];
				$arrParam['hidRefKey'] = $id;
				$arrParam['hidRefHeaderKey'] = $id;
                $arrParam['hidRefCode'] =  $rsHeader[0]['code'];
                //$arrParam['hidRefCode2'] =  $rsHeader[0]['refcode'];
                $arrParam['hidRefTable'] = $rsAPKey['key'];
                $arrParam['hidRefDate'] =   $this->formatDBDate($rsHeader[0]['trdate'],'d / m / Y'); 
				$arrParam['amount'] = abs($rsHeader[0]['grandtotal']);
				$arrParam['trDesc'] = '';
                $arrParam['trDate'] =  $this->formatDBDate($rsHeader[0]['trdate'],'d / m / Y');  
                $date = new DateTime($rsHeader[0]['trdate']);
                $date->add(new DateInterval('P'.$rsTOP[0]['duedays'].'D'));
                $arrParam['dueDate'] = $date->format('d / m / Y');// date ('d / m / Y', mktime(0, 0, 0, date("m")  , date("d")+$rsTOP[0]['duedays'], date("Y")));
				$arrParam['createdBy'] = 0;
                $arrParam['selWarehouse'] = $rsHeader[0]['warehousekey'];
                $arrParam['islinked'] = 1;
                $arrParam['overwriteGL'] = 1;
                $arrParam['selAPType'] = 1;
				 
				$arrayToJs = $ap->addData($arrParam);  
                if (!$arrayToJs[0]['valid'])
                    throw new Exception('<strong>'.$rsHeader[0]['code'] . '</strong>. '.$this->errorMsg[201].' '.$arrayToJs[0]['message']);    
			}
        
        
        //update jurnal umum 
        $this->updateGL($rsHeader,$rsPayment);
	} 

    function validateCancel($rsHeader,$autoChangeStatus=false){ 
        // cek ad AP terbayar
		$ap = new AP(); 
        $id = $rsHeader[0]['pkey'];
        $rsAPKey = $ap->getTableKeyAndObj($this->tableName,array('key'));  
		$rsAP = $ap->searchData('','',true,' and '.$ap->tableName.'.refkey = '.$this->oDbCon->paramString($id).' and '.$ap->tableName.'.reftabletype = '.$rsAPKey['key'].' and ('.$ap->tableName.'.statuskey in (2,3))');
		
		if(!empty($rsAP))  {
			$this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' . $this->errorMsg[201].' ' .$this->errorMsg['ap'][2]);
        }
        
	}

    function updateGL($rs,$rsPayment){
        if (!USE_GL) return;
        
        $warehouse = new Warehouse();
        $generalJournal = new GeneralJournal();
        $coaLink = new COALink();
        $supplier = new Supplier();
        $item = new Item();         
        
        $warehousekey = $rs[0]['warehousekey'];
		
        $rsKey = $generalJournal->getTableKeyAndObj($this->tableName);
		$arr = array();
		$arr['pkey'] = $generalJournal->getNextKey($generalJournal->tableName);
		$arr['code'] = 'xxxxx';
		$arr['refkey'] = $rs[0]['pkey'];
		$arr['refTableType'] = $rsKey['key'];
		$arr['trDate'] =  $this->formatDBDate($rs[0]['trdate'],'d / m / Y');  
		$arr['createdBy'] = 0; 
		$arr['selWarehouseKey'] = $rs[0]['warehousekey'];

        $rsSupplier = $supplier->getDataRowById($rs[0]['supplierkey']);
		$arr['trDesc'] = $this->ucFirst($this->lang['purchase']. ' ' .  $this->lang['from']) . ' '. $rsSupplier[0]['name'].'.';  
        
        $temp = -1; 
         
        $rsDetail = $this->getDetailById($rs[0]['pkey']);
        $finalDiscount = ($rs[0]['finaldiscount'] != 0 && $rs[0]['finaldiscounttype'] == 2) ? $rs[0]['finaldiscount']/100 * $rs[0]['subtotal'] : $rs[0]['finaldiscount']; 
      
        $arrItemCOA = array();
         
        $rsCOA = $coaLink->getCOALink ('taxin', $warehouse->tableName,$warehousekey, 0); 
	    $temp++;
		$arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];
		$arr['debit'][$temp] =  $rs[0]['taxvalue']; 
		$arr['credit'][$temp] = 0; 
        $arr['refCashBankKey'][$temp] = '';
         
        
        $rsCOA = $coaLink->getCOALink ('shippingcost', $warehouse->tableName,$warehousekey, 0); 
	    $temp++;
		$arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];
		$arr['debit'][$temp] =  $rs[0]['shipmentfee'] ; 
		$arr['credit'][$temp] = 0; 
        $arr['refCashBankKey'][$temp] = '';
        
        $rsCOA = $coaLink->getCOALink ('othercost', $warehouse->tableName,$warehousekey, 0); 
	    $temp++;
		$arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];
		$arr['debit'][$temp] = $rs[0]['etccost']; 
		$arr['credit'][$temp] = 0; 
        $arr['refCashBankKey'][$temp] = '';
         
        $termOfPayment = new TermOfPayment();
		$rsTOP = $termOfPayment->getDataRowById($rs[0]['termofpaymentkey']); 
		$isCash = ($rsTOP[0]['duedays'] == 0) ? true : false; 
        
        $totalPayment = 0;
        if ($isCash) {
            $rsPayment = $this->getPaymentMethodDetail($rs[0]['pkey']);  
            for($i=0;$i<count($rsPayment); $i++){ 
                 $rsCOA = $coaLink->getCOALink ('payment', $warehouse->tableName,$warehousekey, $rsPayment[$i]['paymentkey']);
                 $temp++;
                 $arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];
                 $arr['debit'][$temp] = 0;
                 $arr['credit'][$temp] =  $rsPayment[$i]['amount'];  
                 $arr['refCashBankKey'][$temp] = $rsPayment[$i]['cashBankKey']; 
                 $totalPayment += $rsPayment[$i]['amount'];  
            }
		
             //selisih pembayaran  
            
            if($rs[0]['balance'] != 0){ 
                $temp++; 
                if ($rs[0]['balance'] < 0){ 
                    $rsCOA = $coaLink->getCOALink ('otherrevenue', $warehouse->tableName,$warehousekey, 0); 
                    $arr['debit'][$temp] = 0; 
                    $arr['credit'][$temp] = abs($rs[0]['balance']); 
                }else{ 
                    $rsCOA = $coaLink->getCOALink ('othercost', $warehouse->tableName,$warehousekey, 0); 
                    $arr['debit'][$temp] = abs($rs[0]['balance']);  
                    $arr['credit'][$temp] = 0;
                }
             
                $arr['refCashBankKey'][$temp] = '';        
                $arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];
            }

        }else {  
                $temp++;
                $arr['hidCOAKey'][$temp] = $supplier->getAPCOAKey($rs[0]['supplierkey'],$warehousekey);
                $arr['debit'][$temp] = 0; 
                $arr['credit'][$temp] =  $rs[0]['grandtotal']; 
                $arr['refCashBankKey'][$temp] = '';   
        }

        $coaKey  = $coaLink->getCOALink('hpp', $warehouse->tableName,$warehousekey, 0);
        $subtotal = $rs[0]['beforetaxtotal'];
        $temp++;
        $arr['hidCOAKey'][$temp] = $coaKey[0]['coakey'];
        $arr['debit'][$temp] = $subtotal;
        $arr['credit'][$temp] = 0; 
       
		$arrayToJs = $generalJournal->addData($arr);
         
		if (!$arrayToJs[0]['valid'])
                throw new Exception('<strong>'.$rs[0]['code'] . '</strong>. '.$this->errorMsg[504].' '.$arrayToJs[0]['message']);    
    }

     function cancelTrans($rsHeader,$copy){  
		
		$id = $rsHeader[0]['pkey']; 
		     
		$ap = new AP();
        $rsAPKey = $ap->getTableKeyAndObj($this->tableName,array('key')); 
		$rsAP = $ap->searchData('','',true,' and '.$ap->tableName.'.reftabletype = '.$this->oDbCon->paramString($rsAPKey['key']).' and '.$ap->tableName.'.refkey = '.$this->oDbCon->paramString($id).' and '.$ap->tableName.'.statuskey = 1');
		for($i=0;$i<count($rsAP);$i++) {
            $arrayToJs = $ap->changeStatus($rsAP[$i]['pkey'],4,'',false,true);
            if (!$arrayToJs[0]['valid'])
                throw new Exception('<strong>'.$rsHeader[0]['code'] . '</strong>. '.  $arrayToJs[0]['message']);    
        }	
           
		if ($copy)
			$this->copyDataOnCancel($id);	  
	 
        $this->cancelGLByRefkey($rsHeader[0]['pkey'],$this->tableName);
	} 

    function validateConfirm($rsHeader){
		
        $warehouse = new Warehouse();  
        $coaLink = new COALink();
        
		$id = $rsHeader[0]['pkey'];

        //  cuma boleh 1 Kode Invoice yg untuk supplier yang sama
        $rsDisposalPO = $this->searchDataRow( array($this->tableName.'.code'), 
                                           ' and '.$this->tableName.'.supplierkey = ' . $this->oDbCon->paramString($rsHeader[0]['supplierkey']). ' and '.$this->tableName.'.refinvoicecode = ' . $this->oDbCon->paramString($rsHeader[0]['refinvoicecode']).' and '.$this->tableName.'.statuskey = 2');
        if (!empty($rsDisposalPO)) { 
            $this->addErrorLog(false, '<strong>' . $rsHeader[0]['refinvoicecode'] .'</strong>. '. $this->errorMsg['invoice'][4]);
        }
        
        $rsPayment = $this->getPaymentMethodDetail($id);  
		$termOfPayment = new TermOfPayment();
 		$rsTOP = $termOfPayment->getDataRowById($rsHeader[0]['termofpaymentkey']); 
		$isCash = ($rsTOP[0]['duedays'] == 0) ? true : false; 
			 
        $totalPayment = 0; 
        for($i=0;$i<count($rsPayment); $i++)
            $totalPayment += $rsPayment[$i]['amount'];
        
        $balance = $totalPayment - $rsHeader[0]['grandtotal']; 
          
        if ($isCash){ 
            $thresholdDiscount = abs($this->loadSetting('roundedPaymentThreshold'));
            if($balance < ($thresholdDiscount * -1)) 
                $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg[502]);
            else if ($balance > $thresholdDiscount)
                $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg[509]); 
        }
		
	 }

    function getDetailWithRelatedInformation($pkey, $criteria = '')
    {

        $sql = 'select
	   			' . $this->tableNameDetail . '.*,
                concat (' . $this->tableWaste . '.code, " - ", ' . $this->tableWaste . '.name) as waste
			  from
			  	' . $this->tableNameDetail . '
                  left join ' . $this->tableWaste . ' on ' . $this->tableNameDetail . '.wastekey = ' . $this->tableWaste . '.pkey
			  where
                ' . $this->tableNameDetail . '.refkey in (' . $this->oDbCon->paramString($pkey, ',') . ') ';
        $sql .= $criteria;

        return $this->oDbCon->doQuery($sql);
    }

    function getTax($pkey = '')
    {

        $sql = 'select
                    ' . $this->tableTax . '.*
               from
                   ' . $this->tableTax;

        if (!empty($pkey))
            $sql .= ' where  		
             ' . $this->tableTax . '.pkey = ' . $this->oDbCon->paramString($pkey);


        $sql .= ' order by tax';

        return $this->oDbCon->doQuery($sql);
    }

    function reCountGrandTotal($arrParam)
    {

        $rsTax = $this->getTax();
        $rsTax = array_column($rsTax, null, 'pkey');

        $isPriceIncludeTax = (!empty($arrParam['chkIncludeTax'])) ? 1 : 0;

        $subtotal = 0;
        $beforeTaxTotal = 0;
        $taxTotal = 0;
        $grandtotal = 0;
        $totalWeight = 0;

        $arrWasteKey = $arrParam['hidWasteKey'];
        $arrTaxKey = $arrParam['taxDetailKey'];
        // $arrTaxKey = $this->unFormatNumber($arrParam['taxDetailKey']);

        $arrWeightDetail = $arrParam['weightDetail'];
        $arrPriceinunit = $arrParam['priceInUnit'];
        $arrIsIncludeTax = $arrParam['chkIncludeTaxDetail'];
        $arrDiscountType = $arrParam['selDiscountType'];
        $arrTransUnitKey = $arrParam['selUnit'];

        $arrItemDetail = array();

        for ($i = 0; $i < count($arrWasteKey); $i++) {

            if (empty($arrWasteKey[$i]))
                continue;

            $wasteKey = $arrWasteKey[$i];
            $WeightDetail =  $this->unFormatNumber($arrWeightDetail[$i]);
            $priceInUnit = $this->unFormatNumber($arrPriceinunit[$i]);
            $taxKey = $arrTaxKey[$i];
            $isIncludeTax = $arrIsIncludeTax[$i];
            $taxPercentage = $this->unFormatNumber($rsTax[$taxKey]['tax']);

            $detailSubtotal = $WeightDetail * $priceInUnit;
            $beforeTaxDetail = $detailSubtotal ;

            if ($taxPercentage != 0) {
                if ($isIncludeTax == 0) {
                    $taxValue = $detailSubtotal * $taxPercentage / 100;
                    $taxValue = round($taxValue); 
                } else {
                    $taxValue = ($taxPercentage / (100 + $taxPercentage)) * $detailSubtotal;
                    $beforeTaxDetail = $detailSubtotal - $taxValue;
                }
            }

            $arrItemDetail[$i]['subTotalDetail'] = $beforeTaxDetail;
            $arrItemDetail[$i]['beforeTaxDetail'] = $beforeTaxDetail;
            $arrItemDetail[$i]['taxValueDetail'] = $taxValue;
            $arrItemDetail[$i]['subTotalDetailBeforeTax'] = $detailSubtotal;

            $subtotal += $detailSubtotal;
            $beforeTaxTotal += $beforeTaxDetail;
            $taxTotal += $taxValue;
            $totalWeight += $WeightDetail;
        }
        $grandtotal = $subtotal + $taxTotal ;


        $balance = 0;
        $totalPayment = 0;

        $termOfPayment = new TermOfPayment();
        $rsTOP = $termOfPayment->getDataRowById($arrParam['selTermOfPayment']);
        if ($rsTOP[0]['duedays'] == 0) {
            $payment = $arrParam['paymentMethodValue'];
            for ($i = 0; $i < count($payment); $i++) {
                $totalPayment += $this->unFormatNumber($payment[$i]);
            }
        }
        
        $balance = $totalPayment - $grandtotal;
        
        
        $reCountResult = array();
        $reCountResult['subtotal'] = $beforeTaxTotal;
        $reCountResult['beforeTaxTotal'] = $beforeTaxTotal;
        $reCountResult['isPriceIncludeTax'] = $isPriceIncludeTax;
        $reCountResult['grandtotal'] = $grandtotal;
        $reCountResult['totalPayment'] = $totalPayment;
        $reCountResult['balance'] = $balance;
        $reCountResult['taxValue'] = $taxTotal;
        $reCountResult['detailCOGS'] = $arrItemDetail;
        $reCountResult['totalWeight'] = $totalWeight;

        return $reCountResult;
    }

    function normalizeParameter($arrParam, $trim = false)
    {
        $termOfPayment = new TermOfPayment();
        
        $rsTOP = $termOfPayment->getDataRowById($arrParam['selTermOfPayment']);
        if ($rsTOP[0]['duedays'] != 0) {
            for ($i = 0; $i < count($arrParam['paymentMethodValue']); $i++) {
                $arrParam['paymentMethodValue'][$i] = 0;
                $arrParam['hidDetailPaymentKey'][$i] = 0;
            }
        }

        $reCountResult = $this->reCountGrandTotal($arrParam); 

        $arrParam['subtotal'] = $reCountResult['subtotal'];
        $arrParam['grandTotal'] = $reCountResult['grandTotal'];
        $arrParam['beforeTaxTotal'] = $reCountResult['beforeTaxTotal'];
        $arrParam['ispriceincludetax'] = $reCountResult['isPriceIncludeTax'];
        $arrParam['totalPayment'] = $reCountResult['totalPayment'];
        $arrParam['totalDownpayment'] = $reCountResult['totalDownpayment'];
        $arrParam['outstanding'] = $reCountResult['outstanding'];
        $arrParam['balance'] = $reCountResult['balance'];
        $arrParam['tax23Value'] = $reCountResult['tax23Value']; 
        $arrParam['taxValue'] = $reCountResult['taxValue'];
        $arrParam['totalWeight'] = $reCountResult['totalWeight'];

        $recountDetail = $reCountResult['recountDetail']; 
        for($i=0;$i<count($recountDetail);$i++){
            $arrParam['subTotalDetail'][$i] = $recountDetail[$i]['subTotalDetail'];
            $arrParam['beforeTaxDetail'][$i] = $recountDetail[$i]['beforeTaxDetail'];
            $arrParam['taxValueDetail'][$i] = $recountDetail[$i]['taxValueDetail'];
            $arrParam['subTotalDetailBeforeTax'][$i] = $recountDetail[$i]['subTotalDetailBeforeTax'];
        }

        $arrParam = parent::normalizeParameter($arrParam, true);
        return $arrParam;
    }
}
