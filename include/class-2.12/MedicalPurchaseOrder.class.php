<?php

class MedicalPurchaseOrder extends BaseClass
{

    function __construct()
    {

        parent::__construct();

        $this->tableName = 'medical_purchase_order_header';
        $this->tableNameDetail = 'medical_purchase_order_detail';
        $this->tableMedicalJobOrder = 'medical_job_order_header';
        $this->tableMedicalRequestClaim = 'medical_request_claim_header';
        $this->tablePayment= 'medical_purchase_order_payment';
        $this->tableStatus = 'transaction_status';
        $this->tableItemUnit = 'item_unit';
        $this->tableCustomer = 'customer';
        $this->tableCity = 'city';
        $this->tableWarehouse = 'warehouse';
        $this->tableCityCategory = 'city_category';
        $this->tableSupplier = 'supplier';
        $this->tableItem = 'item';
        $this->securityObject = 'MedicalPurchaseOrder'; 
        $this->tableFile = 'medical_purchase_order_file'; 
	    $this->uploadFileFolder = 'medical-purchase-order-file/';
        $this->isTransaction = true;
        $this->newLoad = true;

        $this->arrDataDetail = array();
        $this->arrDataDetail['pkey'] = array('hidDetailKey');
        $this->arrDataDetail['refkey'] = array('pkey', 'ref');
        $this->arrDataDetail['itemkey'] = array('hidItemKey');
        $this->arrDataDetail['qty'] = array('quantityValue', 'number');
        $this->arrDataDetail['priceinunit'] = array('priceValue', 'number');
        $this->arrDataDetail['unitkey'] = array('selUnit');
        $this->arrDataDetail['total'] = array('detailSubtotal','number');
        $this->arrDataDetail['costinbaseunit'] = array('cogs','number');
        $this->arrDataDetail['receivedqtyinbaseunit'] = array('receivedQtyInBaseUnit','number');
        $this->arrDataDetail['qtyinbaseunit'] = array('qtyInBaseUnit','number');
        $this->arrDataDetail['priceinbaseunit'] = array('priceInBaseUnit','number');

        $this->arrPaymentDetail = array(); 
        $this->arrPaymentDetail['pkey'] = array('hidDetailPaymentKey');
        $this->arrPaymentDetail['refkey'] = array('pkey', 'ref');
        $this->arrPaymentDetail['amount'] = array('paymentMethodValue',array('datatype' => 'number','mandatory'=>true));
        $this->arrPaymentDetail['paymentkey'] = array('selPaymentMethod',array('mandatory'=>true)); 

        $arrDetails = array();
        array_push($arrDetails, array('dataset' => $this->arrDataDetail));
        array_push($arrDetails, array('dataset' => $this->arrPaymentDetail, 'tableName' => $this->tablePayment));
        array_push($arrDetails, array('dataset' => $this->arrDataFile, 'tableName' => $this->tableFile, 
									  'datatype' => 'file', 'uploadFolder' => $this->uploadFileFolder,
									  'token' => 'token-item-file-uploader', 'fileName' => 'item-file-uploader'));   


        $this->arrData = array();
        $this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails));
        $this->arrData['code'] = array('code');
        $this->arrData['refkey'] = array('hidMedicalJobOrderkey');
        $this->arrData['guaranteetype'] = array('selGuaranteeType');
        $this->arrData['refrequestkey'] = array('hidMedicalRequestClaimKey');
        $this->arrData['warehousekey'] = array('selWarehouseKey');
        $this->arrData['trdate'] = array('trDate','date');
        $this->arrData['supplierkey'] = array('hidSupplierKey');
        $this->arrData['trdesc'] = array('trDesc');
        $this->arrData['subtotal'] = array('subtotal','number');
        $this->arrData['excessfee'] = array('excessFee','number');
        $this->arrData['grandtotal'] = array('total','number');
        $this->arrData['beforetaxtotal'] = array('beforeTaxTotal','number');
        $this->arrData['finaldiscounttype'] = array('selFinalDiscountType','number');
        $this->arrData['finaldiscount'] = array('finalDiscount','number');
        $this->arrData['shipmentfee'] = array('shipmentFee','number'); 
        $this->arrData['etccost'] = array('etcCost','number');
        $this->arrData['taxpercentage'] = array('taxPercentage','number');
        $this->arrData['taxvalue'] = array('taxValue','number');
        $this->arrData['ispriceincludetax'] = array('chkIncludeTax');
        $this->arrData['termofpaymentkey'] = array('selTermOfPaymentKey');
        $this->arrData['totalpayment'] = array('totalPayment','number');
        $this->arrData['balance'] = array('balance','number');

        $this->printMenu = array();  
        array_push($this->printMenu,array('code' => 'printTransaction', 'name' => $this->lang['printTransaction'],  'icon' => 'print', 'url' => 'print/medicalPurchaseOrder'));
   array_push($this->printMenu, array('code' => 'printLetterOfGuarantee', 'name' => $this->lang['printLetterOfGuarantee'],  'icon' => 'print', 'url' => 'print/letterOfGuarantee'));

        $this->allowedStatusForEdit = array(1, 2);

        $this->arrDataListAvailableColumn = array();
        array_push($this->arrDataListAvailableColumn, array('code' => 'code', 'title' => 'code', 'dbfield' => 'code', 'default' => true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'date','title' => 'date','dbfield' => 'trdate','default'=>true, 'width' => 130, 'align' =>'center','format' => 'date'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'jocode', 'title' => 'JOCode', 'dbfield' => 'jobordercode', 'default' => true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'name', 'title' => 'insuredName', 'dbfield' => 'insuredname', 'default' => true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'name', 'title' => 'company', 'dbfield' => 'companyname', 'default' => true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'code', 'title' => 'supplier', 'dbfield' => 'suppliername', 'default' => true, 'width' => 120));
		array_push($this->arrDataListAvailableColumn, array('code' => 'status', 'title' => 'status', 'dbfield' => 'statusname', 'default' => true, 'width' => 100));
      

		$this->arrSearchColumn = array();
        array_push($this->arrSearchColumn, array('Kode', $this->tableName . '.code'));
        array_push($this->arrSearchColumn, array('Tanggal', $this->tableName . '.trdate')); 
        array_push($this->arrSearchColumn, array('company', $this->tableCustomer . '.name'));
        array_push($this->arrSearchColumn, array('insuredName', $this->tableMedicalRequestClaim . '.insuredname'));
        array_push($this->arrSearchColumn, array('status', $this->tableStatus . '.status'));
        array_push($this->arrSearchColumn, array('supplier', $this->tableSupplier . '.name'));
        array_push($this->arrSearchColumn, array('JOCode', $this->tableMedicalJobOrder . '.code'));

        $this->includeClassDependencies(array(
            'Item.class.php',
            'AP.class.php',
            'ItemUnit.class.php',
            'Supplier.class.php',
            'ItemCategory.class.php',
            'CustomerCategory.class.php',
            'MedicalJobOrder.class.php',
            'MedicalRequestClaim.class.php',
            'Warehouse.class.php',
            'TermOfPayment.class.php',
            'PaymentMethod.class.php',
        ));

        $this->overwriteConfig();
    }

    function getQuery()
    {

        $sql = '
                 select
                     ' . $this->tableName . '.*, 
                     ' . $this->tableSupplier . '.code as suppliercode,
                     ' . $this->tableSupplier . '.name as suppliername,
                     ' . $this->tableStatus . '.status as statusname,
                     ' . $this->tableCustomer . '.name as companyname,
                     ' . $this->tableCity . '.name as cityname ,
                     ' . $this->tableCityCategory . '.name as citycategoryname,
                     ' . $this->tableMedicalRequestClaim . '.insuredname, 
                     ' . $this->tableWarehouse . '.name as warehousename, 
                     ' . $this->tableMedicalJobOrder . '.code as jobordercode
                 from 
                     ' . $this->tableName . '
                     left join ' . $this->tableMedicalRequestClaim . ' on ' . $this->tableName . '.refrequestkey = ' . $this->tableMedicalRequestClaim . '.pkey 
                     left join ' . $this->tableMedicalJobOrder . ' on ' . $this->tableName . '.refkey = ' . $this->tableMedicalJobOrder . '.pkey
                     left join ' . $this->tableCity . ' on ' . $this->tableMedicalJobOrder . '.citykey = ' . $this->tableCity . '.pkey  
                     left join ' . $this->tableCityCategory . ' on ' . $this->tableCity . '.categorykey = ' . $this->tableCityCategory . '.pkey 
                    left join ' . $this->tableCustomer . ' on ' . $this->tableMedicalJobOrder . '.customerkey = ' . $this->tableCustomer . '.pkey  
	                left join ' . $this->tableSupplier . ' on ' . $this->tableName . '.supplierkey = ' . $this->tableSupplier . '.pkey,'  
                     . $this->tableStatus . ',
                     ' . $this->tableWarehouse . '  
                 where  		
                     ' . $this->tableName . '.statuskey = ' . $this->tableStatus . '.pkey and
                     ' . $this->tableName . '.warehousekey = ' . $this->tableWarehouse . '.pkey  
          ' . $this->criteria;
        return $sql;
    }

    function validateForm($arr, $pkey = '')
    {
        $arrayToJs = parent::validateForm($arr, $pkey);
        
        $item = new Item();
        $arrItemkey = $arr['hidItemKey'];
        $arrQuantity = $arr['quantityValue'];
        $arrPrice = $arr['priceValue'];
        $arrSupplierKey = $arr['hidSupplierKey'];
        $arrMedicalJobOrderKey = $arr['hidMedicalJobOrderkey'];


        if (!empty($pkey)){
			$rs = $this->getDataRowById($pkey);
			if ($rs[0]['statuskey'] <> 1){
				$this->addErrorList($arrayToJs,false,$this->errorMsg[212]);
			}
		} 

 	$arrDetailKeys = array();

        for($i=0;$i<count($arrItemkey);$i++) { 
			if (empty($arrItemkey[$i]) ){ 
				$this->addErrorList($arrayToJs,false, $this->errorMsg['service'][1]); 	
			} else{
                
                // cek harga dan jumlah != 0
                if ( $this->unFormatNumber($arrQuantity[$i]) <= 0 || $this->unFormatNumber($arrPrice[$i]) <= 0){
                    $rsItem = $item->getDataRowById($arrItemkey[$i]);
                    $this->addErrorList($arrayToJs,false,$rsItem[0]['name']. '. ' . $this->errorMsg[500]); 
                }


                // cek detail double 
                if (in_array($arrItemkey[$i],$arrDetailKeys)){  
                    $rsItem = $item->getDataRowById($arrItemkey[$i]);
                    $this->addErrorList($arrayToJs,false, $rsItem[0]['name'].'. '.$this->errorMsg[215]); 	 
                }else{ 
                    array_push($arrDetailKeys, $arrItemkey[$i]);
                } 
            } 
             
		} 

        if (empty($arrSupplierKey)) {
            $this->addErrorList($arrayToJs, false, $this->errorMsg['supplier'][1]);
        }
		
        if (empty($arrMedicalJobOrderKey)) {
            $this->addErrorList($arrayToJs, false, $this->errorMsg['medicalJobOrder'][1]);
        }
        
 
       
        return $arrayToJs;
    }

	


    function getDetailWithRelatedInformation($pkey,$criteria = ''){

        $sql = 'select
                 ' . $this->tableNameDetail . '.*,
                 ' . $this->tableItem . '.name as itemname, 
                 ' . $this->tableItem . '.code as itemcode,
                '.$this->tableItemUnit.'.name as unitname
 
               from
                   ' . $this->tableNameDetail . ',
                '.$this->tableItemUnit.',
                 ' . $this->tableItem . '
               where
			  	'.$this->tableNameDetail.'.itemkey = '.$this->tableItem.'.pkey and
                '.$this->tableNameDetail.'.unitkey = '.$this->tableItemUnit.'.pkey and
                '.$this->tableNameDetail .'.refkey in ('.$this->oDbCon->paramString($pkey,',') . ') ';

        $sql .= $criteria;

        return $this->oDbCon->doQuery($sql);
    }


   function reCountSubtotal($arrParam){
				
				$item = new Item(); 
        
				// default, ongkir dan cost dibagi berdasarkan proporsional gramasi/kubikasi
				$useGramasi = $this->loadSetting('costProportionalType');
				 
				$isPriceIncludeTax = (!empty($arrParam['chkIncludeTax'])) ? 1 : 0;
			
				$subtotal = 0 ;
				$grandtotal = 0;
				$gramasi = 0;
				
				$arrItemkey = $arrParam['hidItemKey'];
				$taxValue = $this->unFormatNumber($arrParam['taxValue']);  
				$finalDiscount = $this->unFormatNumber($arrParam['finalDiscount']); 
				$finalDiscountType = $arrParam['selFinalDiscountType']; 
				$taxPercentage = $this->unFormatNumber($arrParam['taxPercentage']);  
				$shipmentFee = $this->unFormatNumber($arrParam['shipmentFee']); 
				$etcCost = $this->unFormatNumber($arrParam['etcCost']);  
                    
				$arrQty = $arrParam['quantityValue'];
				$arrPriceinunit = $arrParam['priceValue']; 
				$arrTransUnitKey = $arrParam['selUnit']; 
        
				$arrItemDetail = array();
        
				for ($i=0;$i<count($arrItemkey);$i++){
					
					if (empty($arrItemkey[$i]))  
						continue;
                    
                    $rsItem = $item->getDataRowById($arrItemkey[$i]);
					 
                    $itemkey = $arrItemkey[$i];
                    $transactionUnitKey = $arrTransUnitKey[$i];
                    $baseunitkey = $rsItem[0]['baseunitkey'];
                    $qty =  $this->unFormatNumber($arrQty[$i]);
                    $qtyinbaseunit = $qty * $conversionMultiplier; 
                    $priceInUnit = $this->unFormatNumber($arrPriceinunit[$i]);
             
                    $arrItemDetail[$i]['baseUnitKey'] = $baseunitkey;
                    $arrItemDetail[$i]['unitConvMultiplier'] = $conversionMultiplier;
                    $arrItemDetail[$i]['qtyInBaseUnit'] = $qtyinbaseunit ;
                    $arrItemDetail[$i]['priceInBaseUnit'] = $priceInUnit  ;
                          

                    //$detailSubtotal = $qtyinbaseunit * ($priceInUnit - $discountValue);
                    $detailSubtotal = $qty * $priceInUnit ;
                    $arrItemDetail[$i]['detailSubtotal'] = $detailSubtotal;

                    $subtotal += $detailSubtotal ; 

                    $arrItemDetail[$i]['gramasi'] =  ($rsItem[0]['gramasi']*$qtyinbaseunit);
                    $gramasi += $arrItemDetail[$i]['gramasi'];
					   
				} 
				
				$grandtotal = $subtotal;
				
				if ($finalDiscount != 0){
					if ($finalDiscountType == 2)
						$finalDiscount = $finalDiscount/100 * $grandtotal;
				} 
				
				$beforeTaxTotal = $subtotal - $finalDiscount;
				$grandtotal = $beforeTaxTotal;
					 
 				if ($isPriceIncludeTax == false) {
						$taxValue = $beforeTaxTotal * $taxPercentage / 100;
                        $taxValue = round($taxValue); // kalo ad koma, nilainya gantung di AP nanti
						$grandtotal += $taxValue;
				}else{
						$taxValue = ($taxPercentage/(100 + $taxPercentage)) * $grandtotal;   
				 		$beforeTaxTotal = $grandtotal - $taxValue ;
				}
				 
				$grandtotal +=  $shipmentFee + $etcCost;
			 
			 	
				$balance = 0;
				$totalPayment = 0; 
                
                $termOfPayment = new TermOfPayment();
                $rsTOP = $termOfPayment->getDataRowById($arrParam['selTermOfPaymentKey']);  
                if ($rsTOP[0]['duedays'] == 0){ 
                    $payment = $arrParam['paymentMethodValue'];
                    for($i=0;$i<count($payment);$i++){
                        $totalPayment += $this->unFormatNumber($payment[$i]);
                    }
                } 
 
				$balance = $totalPayment - $grandtotal;
				
				 
		 		for ($i=0;$i<count($arrItemkey);$i++){
					
					if (empty($arrItemkey[$i]))  
						continue;
					
                    
                    $priceInUnit = $this->unFormatNumber($arrPriceinunit[$i]);
                    $qtyInBaseUnit = $arrItemDetail[$i]['qtyInBaseUnit'];
                    $priceInBaseUnit = $arrItemDetail[$i]['priceInBaseUnit'];
                    $conversionMultiplier = $arrItemDetail[$i]['unitConvMultiplier'];
					
					if ($useGramasi == 1){
						$itemProportion = $arrItemDetail[$i]['gramasi'];
						$totalProportion = $gramasi;
					}else{
						$itemProportion = $arrItemDetail[$i]['detailSubtotal'];
						$totalProportion = $subtotal;
					}  
                    $proportion = (!empty($totalProportion)) ? $itemProportion / $totalProportion : 0;
					 

                    $totalItemValue = $priceInBaseUnit;
                    

                    $finalDiscountProportion = ($proportion * $finalDiscount) / $qtyInBaseUnit; 
                    $totalItemValue -= $finalDiscountProportion;
                    
                    
                     if ($isPriceIncludeTax) { 

                            $taxValue  = ($taxPercentage/(100 + $taxPercentage)) * $totalItemValue;
                            $totalItemValue -= $taxValue;    
                          
                            // kalo pake cash, balance artinya selisih bayar
                            if($rsTOP[0]['duedays'] == 0) 
                                $totalItemValue += $proportion * $balance; 


                            if (!USE_GL) 
                                $totalItemValue +=  $proportion * ($shipmentFee + $etcCost);
                         
                    }
                    
                    $arrItemDetail[$i]['cogs']  = $totalItemValue; 
				} 
				
				
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

    function afterStatusChanged($rsHeader){ 

        $medicalRequestClaim = new MedicalRequestClaim();
        
        $medicalJobOrder = new MedicalJobOrder();
        $this->setActivityTransactionLogDetail($rsHeader[0]['pkey'], $medicalRequestClaim, $rsHeader[0]['refrequestkey']);
        $this->setActivityTransactionLogDetail($rsHeader[0]['pkey'], $medicalJobOrder, $rsHeader[0]['refkey']);

        // retrieve latest status
        $rsHeader = $this->getDataRowById($rsHeader[0]['pkey']);
        
        if ( $rsHeader[0]['statuskey'] == 2){ 
            $this->changeStatus($rsHeader[0]['pkey'],3); 
        }
    }

    function afterUpdateData($arrParam, $action)  {
            $medicalJobOrder = new MedicalJobOrder();

            $medicalRequestClaim = new MedicalRequestClaim();
            $this->setActivityTransactionLogDetail($arrParam['pkey'], $medicalRequestClaim, $arrParam['hidMedicalRequestClaimKey']);
            $this->setActivityTransactionLogDetail($arrParam['pkey'], $medicalJobOrder, $arrParam['hidMedicalJobOrderkey']);
       
    }



    function validateConfirm($rsHeader){
		
		$id = $rsHeader[0]['pkey'];
        
        $medicalJobOrder = new MedicalJobOrder();
        $item = new Item();
		
		// tidak jadi divalidasi, bisa sja jualny paket, beliny unit ny saja
		
//        $rsMedicalJobOrderDetail = $medicalJobOrder->getDetailById($rsHeader[0]['refkey']);
//        $rsDetail = $this->getDetailById($id);
//
//        $arrItemMedicalJobOrderDetail = array_column($rsMedicalJobOrderDetail, null, 'itemkey');
//
//        for ($i=0;$i<count($rsDetail);$i++){
//            $arrDetail = $rsDetail[$i]['itemkey'];
//            if (!isset($arrItemMedicalJobOrderDetail[$arrDetail]))  {
//                $rsItem = $item->getDataRowById($arrDetail);
//                $this->addErrorLog(false,'<strong>'.$rsItem[0]['name'].'</strong>. '.$this->errorMsg['medicalSalesOrderQuotation'][3]);
//            }
//		}

        $rsPayment = $this->getPaymentMethodDetail($id);  
		$termOfPayment = new TermOfPayment();
 		$rsTOP = $termOfPayment->getDataRowById($rsHeader[0]['termofpaymentkey']); 
		$isCash = ($rsTOP[0]['duedays'] == 0) ? true : false; 
			 
        $totalPayment = 0; 
        for($i=0;$i<count($rsPayment); $i++)
            $totalPayment += $rsPayment[$i]['amount'];
        
        $balance = $totalPayment - $rsHeader[0]['grandtotal']; 
          
        if ($isCash && $rsHeader[0]['guaranteetype'] == 2){ 
            $thresholdDiscount = abs($this->loadSetting('roundedPaymentThreshold'));
            if($balance < ($thresholdDiscount * -1)) 
                $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg[502]);
            else if ($balance > $thresholdDiscount)
                $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg[509]); 
        }
	 }


     function cancelTrans($rsHeader,$copy){  
		
		$id = $rsHeader[0]['pkey']; 
		if ($copy)
			$this->copyDataOnCancel($id);	  
	} 

    function validateCancel($rsHeader,$autoChangeStatus=false){ 
        //cek ad AP terbayar
		$ap = new AP(); 
        $id = $rsHeader[0]['pkey'];
        $rsAPKey = $ap->getTableKeyAndObj($this->tableName,array('key'));  
		$rsAP = $ap->searchData('','',true,' and '.$ap->tableName.'.refkey = '.$this->oDbCon->paramString($id).' and '.$ap->tableName.'.reftabletype = '.$rsAPKey['key'].' and ('.$ap->tableName.'.statuskey in (2,3))');
		
		if(!empty($rsAP))  {
			$this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' . $this->errorMsg[201].' ' .$this->errorMsg['ap'][2]);
        }
        
	}

    function confirmTrans($rsHeader)
    {

        $id = $rsHeader[0]['pkey'];

        $supplier = new Supplier();

        $rsSupplier = $supplier->getDataRowById($rsHeader[0]['supplierkey']);
        $rsDetail = $this->getDetailById($rsHeader[0]['pkey']);

        $termOfPayment = new TermOfPayment();
        $rsTOP = $termOfPayment->getDataRowById($rsHeader[0]['termofpaymentkey']);
        $isCash = ($rsTOP[0]['duedays'] == 0) ? true : false;

        $rsPayment = array();

        // MENGHITUNG PAYMENT
        if ($rsHeader[0]['guaranteetype'] == 2) {
            if ($isCash) {
                // $rsPayment = $this->getPaymentMethodDetail($id);
                // if (ADV_FINANCE) {
                //     //$cashMovement = new CashMovement();  
    
                //     $cashBank = new CashBank();
                //     for ($i = 0; $i < count($rsPayment); $i++) {
                //         if ($rsPayment[$i]['amount'] == 0) continue;
    
                //         if (USE_GL) {
                //             $rsPaymentCOA = $coaLink->getCOALink('payment', $warehouse->tableName, $rsHeader[0]['warehousekey'], $rsPayment[$i]['paymentkey']);
                //             $coakey = $rsPaymentCOA[0]['coakey'];
                //         } else {
                //             $coakey = $rsPayment[$i]['paymentkey'];
                //         }
    
                //         /*if(!empty($rsPaymentCOA))
                //              $cashMovement->updateCashMovement($id, $rsPaymentCOA[0]['coakey'],$rsPayment[$i]['amount'],$this->tableName, $rsHeader[0]['warehousekey'], $notecash,$rsHeader[0]['trdate']);
                //             */
    
                //         $arrItemName =  array_column($rsDetail, 'itemname');
    
                //         $rsCashBank = $cashBank->addCashBank($rsHeader, $this->tableName, array('supplierkey' => $rsHeader[0]['supplierkey'], 'coakey' => $coakey, 'desc' => $note, 'amount' => -$rsPayment[$i]['amount']));
                //         $rsPayment[$i]['cashBankKey'] = $rsCashBank['pkey'];
                //     }
                // }
            } else {
                //update AP
                $ap = new AP();
    
                $arrParam = array();
    
                $rsAPKey = $ap->getTableKeyAndObj($this->tableName);
                $arrParam['code'] = 'xxxxxx';
                $arrParam['hidSupplierKey'] = $rsHeader[0]['supplierkey'];
                $arrParam['hidRefKey'] = $id;
                $arrParam['hidRefHeaderKey'] = $id;
                $arrParam['hidRefCode'] =  $rsHeader[0]['code'];
                $arrParam['hidRefTable'] = $rsAPKey['key'];
                $arrParam['hidRefDate'] =   $this->formatDBDate($rsHeader[0]['trdate'], 'd / m / Y');
                $arrParam['amount'] = abs($rsHeader[0]['grandtotal']);
                $arrParam['trDesc'] = '';
                $arrParam['trDate'] =  $this->formatDBDate($rsHeader[0]['trdate'], 'd / m / Y');
                $date = new DateTime($rsHeader[0]['trdate']);
                $date->add(new DateInterval('P' . $rsTOP[0]['duedays'] . 'D'));
                $arrParam['dueDate'] = $date->format('d / m / Y');
                $arrParam['createdBy'] = 0;
                $arrParam['selWarehouse'] = $rsHeader[0]['warehousekey'];
                $arrParam['islinked'] = 1;
                $arrParam['overwriteGL'] = 1;
                $arrParam['selAPType'] = 1;
    
                $arrayToJs = $ap->addData($arrParam);
                if (!$arrayToJs[0]['valid'])
                    throw new Exception('<strong>' . $rsHeader[0]['code'] . '</strong>. ' . $this->errorMsg[201] . ' ' . $arrayToJs[0]['message']);
            }
        }
    }

    function normalizeParameter($arrParam, $trim = false)
    {
        $termOfPayment = new TermOfPayment();
         

            $arrItemkey = $arrParam['hidItemKey']; 
            $arrPriceinunit = $arrParam['priceInUnit']; 
            $arrDiscountValueInUnit = $arrParam['discountValueInUnit']; 
            $arrDiscountType = $arrParam['selDiscountType'];  


            $rsTOP = $termOfPayment->getDataRowById($arrParam['selTermOfPaymentKey']);  
            if ($rsTOP[0]['duedays'] != 0){   
                for($i=0;$i<count( $arrParam['paymentMethodValue']);$i++){ 
                    $arrParam['paymentMethodValue'][$i] = 0; 
                    $arrParam['hidDetailPaymentKey'][$i] = 0;
                }
            }


            $reCountResult = $this->reCountSubtotal($arrParam);  
            $arrParam['detailCOGS'] = $reCountResult['detailCOGS'];
            $arrParam['subtotal'] = $reCountResult['subtotal'];
            $arrParam['beforeTaxTotal'] = $reCountResult['beforeTaxTotal'];
            $arrParam['isPriceIncludeTax'] = $reCountResult['isPriceIncludeTax'];
            $arrParam['grandtotal'] = $reCountResult['grandtotal'];
            $arrParam['totalPayment'] = $reCountResult['totalPayment'];
            $arrParam['balance'] = $reCountResult['balance']; 
        
   
             for ($i=0;$i<count($arrItemkey);$i++){ 
 
                $qtyinbaseunit = $arrParam['detailCOGS'][$i]['qtyInBaseUnit'];
                $arrParam['qtyInBaseUnit'][$i] = $qtyinbaseunit;
                $arrParam['unitConvMultiplier'][$i] = $arrParam['detailCOGS'][$i]['unitConvMultiplier'];
                $arrParam['cogs'][$i] = $arrParam['detailCOGS'][$i]['cogs'];
                $arrParam['priceInBaseUnit'][$i] = $arrParam['detailCOGS'][$i]['priceInBaseUnit']; 
                $arrParam['detailSubtotal'][$i] = $arrParam['detailCOGS'][$i]['detailSubtotal'];
             
                // set default jadi 0 lg, utk handle copy on cancel
                $arrParam['receivedQtyInBaseUnit'][$i] = 0;

            }
        
        $arrParam = parent::normalizeParameter($arrParam, true);

        return $arrParam;
    }
}
