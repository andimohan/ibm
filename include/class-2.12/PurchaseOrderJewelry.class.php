<?php

class PurchaseOrderJewelry extends BaseClass
{

    function __construct()
    {

        parent::__construct();

        $this->tableName = 'purchase_order_jewelry_header';
        $this->tableNameDetail = 'purchase_order_jewelry_detail';
        $this->tablePurchaseRequest = 'purchase_request_header';
        $this->tablePayment= 'purchase_order_jewelry_payment';
        $this->tablePurchaseCategory = 'purchase_category';
        $this->tablePurchaseCategory = 'purchase_category';
        $this->tableSupplier = 'supplier';
		$this->tableWarehouse = 'warehouse';
        $this->tableStatus = 'transaction_status';
		$this->tableItemUnit = 'item_unit';

        $this->isTransaction = true; 

        $this->securityObject = 'PurchaseOrderJewelry';

        $this->arrDataDetail = array();
        $this->arrDataDetail['pkey'] = array('hidDetailKey');
        $this->arrDataDetail['refkey'] = array('pkey', 'ref');
        $this->arrDataDetail['itemname'] = array('itemName');
        $this->arrDataDetail['qty'] = array('qty','number');
        $this->arrDataDetail['qtyinbaseunit'] = array('qtyInBaseUnit','number');
        $this->arrDataDetail['unitkey'] = array('selUnit');
        $this->arrDataDetail['priceinunit'] = array('priceInUnit','number');
        $this->arrDataDetail['priceinbaseunit'] = array('priceInBaseUnit','number');
        $this->arrDataDetail['unitconvmultiplier'] = array('unitConvMultiplier','number');
        $this->arrDataDetail['discounttype'] = array('selDiscountType');
        $this->arrDataDetail['discount'] = array('discountValueInUnit','number');
        $this->arrDataDetail['total'] = array('detailSubtotal','number');
        $this->arrDataDetail['costinbaseunit'] = array('cogs','number');
        $this->arrDataDetail['receivedqtyinbaseunit'] = array('receivedQtyInBaseUnit','number');

        $this->arrDataDetail['qtyinpcs'] = array('qtyInPcs', 'number');
        $this->arrDataDetail['receivedqtyinpcs'] = array('receivedQtyInPcs','number');
        $this->arrDataDetail['priceinpcs'] = array('priceInPcs', 'number');
        $this->arrDataDetail['ispriceinpcs'] = array('chkPriceInPcs');
        $this->arrDataDetail['number'] = array('numberDetail', 'number');
        $this->arrDataDetail['trdesc'] = array('detailNotes');

        $this->arrPaymentDetail = array(); 
        $this->arrPaymentDetail['pkey'] = array('hidDetailPaymentKey');
        $this->arrPaymentDetail['refkey'] = array('pkey', 'ref');
        $this->arrPaymentDetail['amount'] = array('paymentMethodValue',array('datatype' => 'number','mandatory'=>true));
        $this->arrPaymentDetail['paymentkey'] = array('selPaymentMethod',array('mandatory'=>true)); 
    

        $arrDetails = array();
        array_push($arrDetails, array('dataset' => $this->arrDataDetail));
        array_push($arrDetails, array('dataset' => $this->arrPaymentDetail, 'tableName' => $this->tablePayment));

        $this->arrData = array();
        $this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails));
        $this->arrData['code'] = array('code');
        $this->arrData['reftabletype'] = array('selType');
        $this->arrData['refkey'] = array('hidPurchaseRequestKey');
        $this->arrData['refservicekey'] = array('hidServiceKey');
        $this->arrData['trdate'] = array('trDate','date');
        $this->arrData['warehousekey'] = array('selWarehouseKey');
        $this->arrData['supplierkey'] = array('hidSupplierKey');
        $this->arrData['termofpaymentkey'] = array('selTermOfPaymentKey');
        $this->arrData['categorykey'] = array('hidPurchaseCategoryKey');
        $this->arrData['trdesc'] = array('trDesc');
        $this->arrData['subtotal'] = array('subtotal','number');
        $this->arrData['finaldiscounttype'] = array('selFinalDiscountType','number');
        $this->arrData['finaldiscount'] = array('finalDiscount','number');
        $this->arrData['beforetaxtotal'] = array('beforeTaxTotal','number');
        $this->arrData['ispriceincludetax'] = array('isPriceIncludeTax');
        $this->arrData['taxpercentage'] = array('taxPercentage','number');
        $this->arrData['taxvalue'] = array('taxValue','number');
        $this->arrData['shipmentfee'] = array('shipmentFee','number'); 
        $this->arrData['etccost'] = array('etcCost','number');
        $this->arrData['grandtotal'] = array('grandtotal','number');
        $this->arrData['totalpayment'] = array('totalPayment','number');
        $this->arrData['isfullreceive'] = array('chkIsFullReceive');
        $this->arrData['balance'] = array('balance','number');
        $this->arrData['refinvoicecode'] = array('refInvoiceCode');
        $this->arrData['statuskey'] = array('selStatus');
        $this->arrData['rate'] = array('currencyRate', 'number');       
        $this->arrData['currencykey'] = array('selCurrency');

        $this->arrData['afterfirstdiscount'] = array('afterFirstDiscount', 'number');
        $this->arrData['finaldiscount2type'] = array('selFinalDiscount2Type', 'number');
        $this->arrData['finaldiscount2'] = array('finalDiscount2', 'number');



        $this->arrSearchColumn = array();
        array_push($this->arrSearchColumn, array('Kode', $this->tableName . '.code'));
        array_push($this->arrSearchColumn, array('Permintaan Pembelian', $this->tablePurchaseRequest . '.code'));
        array_push($this->arrSearchColumn, array('Tanggal', $this->tableName . '.trdate'));
        array_push($this->arrSearchColumn, array('Gudang', $this->tableWarehouse . '.name'));
        array_push($this->arrSearchColumn, array('Supplier', $this->tableSupplier . '.name'));
        array_push($this->arrSearchColumn, array('Referensi Invoice', $this->tableName . '.refinvoicecode'));
        array_push($this->arrSearchColumn, array('Total', $this->tableName . '.grandtotal'));
        array_push($this->arrSearchColumn, array('Catatan', $this->tableName . '.trdesc'));

        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'requestcode','title' => 'purchaseRequest','dbfield' => 'refcode',  'width' => 160));
        array_push($this->arrDataListAvailableColumn, array('code' => 'date','title' => 'date','dbfield' => 'trdate','default'=>true, 'width' => 100, 'align' =>'center','format' => 'date'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'warehouse','title' => 'warehouse','dbfield' => 'warehousename','default'=>true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'category','title' => 'category','dbfield' => 'categoryname',  'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'reference','title' => 'invoiceReference','dbfield' => 'refinvoicecode', 'width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'supplier','title' => 'supplier','dbfield' => 'suppliername','default'=>true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'total','title' => 'total','dbfield' => 'grandtotal','default'=>true, 'width' => 100, 'align' =>'right','format' => 'number'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
        array_push($this->arrDataListAvailableColumn, array('code' => 'desc','title' => 'note','dbfield' => 'trdesc',  'width' => 200));

        $this->includeClassDependencies(array(
            'Warehouse.class.php',
            'ItemUnit.class.php',
            'PaymentMethod.class.php',
            'PurchaseRequest.class.php',
            'Supplier.class.php',
            'TermOfPayment.class.php',
			'PurchaseCategory.class.php',
            'ReceivingPurchaseJewelry.class.php'
        ));

        $this->overwriteConfig();

    }

    function getQuery()
    {
        $sql = '
			SELECT '.$this->tableName.'.*,
                ' . $this->tablePurchaseRequest . '.code as refcode,
			    ' . $this->tableSupplier . '.name as suppliername,
			    ' . $this->tableWarehouse . '.name as warehousename,
			    ' . $this->tableStatus . '.status as statusname,
                ' . $this->tablePurchaseCategory . '.name as categoryname
			FROM 
                '.$this->tableName.' 
                    left join '.$this->tablePurchaseRequest.' on '.$this->tableName.'.refkey = '.$this->tablePurchaseRequest.'.pkey
                    left join '.$this->tablePurchaseCategory.' on '.$this->tableName.'.categorykey = '.$this->tablePurchaseCategory.'.pkey,
                '.$this->tableStatus.',  
                '.$this->tableSupplier.' ,
                '.$this->tableWarehouse.'  
			WHERE
                '.$this->tableName.'.supplierkey = '.$this->tableSupplier.'.pkey and
                '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey and
                '.$this->tableName.'.warehousekey = '.$this->tableWarehouse.'.pkey  
		' . $this->criteria;

        $sql .= $this->getCompanyCriteria();
        $sql .= $this->getWarehouseCriteria();

        return $sql;
    }

    function getDetailWithRelatedInformation($pkey, $criteria = '', $orderBy = '')
    {
         $sql = 'select
	   			'.$this->tableNameDetail.'.*,
                ('.$this->tableNameDetail.'.qtyinbaseunit - '.$this->tableNameDetail.'.receivedqtyinbaseunit) as outstanding,
                ('.$this->tableNameDetail.'.qtyinpcs - '.$this->tableNameDetail.'.receivedqtyinpcs) as outstandinginpcs,
                '.$this->tableItemUnit.'.name as unitname
			  from
			  	'.$this->tableNameDetail.',
                '.$this->tableItemUnit.',
                '.$this->tableName.'
			  where
			  	'.$this->tableNameDetail.'.refkey = '.$this->tableName.'.pkey and
			  	'.$this->tableNameDetail.'.unitkey = '.$this->tableItemUnit.'.pkey and
                '.$this->tableNameDetail .'.refkey in ('.$this->oDbCon->paramString($pkey,',') . ') ';
        
        $sql .= $criteria;
        
        if(!empty($orderBy))
        $sql .= ' '.$orderBy;
              
		return $this->oDbCon->doQuery($sql);
    }


    function validateForm($arr,$pkey = ''){

        $arrayToJs = parent::validateForm($arr, $pkey);


        $supplierkey = $arr['hidSupplierKey'];

        $arrItemName = $arr['itemName'];
        $arrQty = $arr['qty'];
        $arrPriceinunit = $arr['priceInUnit'];
        $arrSelUnit = $arr['selUnit'];
        $arrQtyInPcs = $arr['qtyInPcs'];
        $arrPriceInPcs = $arr['priceInPcs'];
        $arrDetailNo = $arr['detailNo'];

        if(empty($supplierkey)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['supplier'][1]);
		}

        $arrDetailKeys = array();
        $arrDetailNumber = array();
        for($i=0; $i<count($arrItemName); $i++){
            if(empty($arrItemName[$i])){
                $this->addErrorList($arrayToJs,false,$this->errorMsg['item'][1]);
            } else {
                // if($this->unFormatNumber($arrDetailNo[$i]) <= 0) {
                //     $this->addErrorList($arrayToJs,false,'<strong>'.$arrItemName[$i].'.</strong> '.$this->errorMsg['purchaseOrderJewelry'][1]);
                // } else {

                //     if (in_array($this->unFormatNumber($arrDetailNo[$i]), $arrDetailNumber)) {
                //         $this->addErrorList($arrayToJs, false, '<strong></strong>'.$arrItemName[$i] . '.</strong> ' . $this->errorMsg['purchaseOrderJewelry'][2]);
                //     } else {
                //         array_push($arrDetailNumber, $this->unFormatNumber($arrDetailNo[$i]));
                //     }

                // }

                if ( $this->unFormatNumber($arrQty[$i]) <= 0 || $this->unFormatNumber($arrPriceinunit[$i]) <= 0){
                    $this->addErrorList($arrayToJs,false,'<strong>'.$arrItemName[$i]. '.</strong> ' . $this->errorMsg[500]); 
                }

        
                if($this->unFormatNumber($arrQtyInPcs[$i]) <= 0 ) {
                    $this->addErrorList($arrayToJs,false,'<strong>'.$arrItemName[$i]. '.</strong> ' . $this->errorMsg[510] . ' (PCS)'); 
                }

                if($this->unFormatNumber($arrPriceInPcs[$i]) <= 0) {
                    $this->addErrorList($arrayToJs,false,'<strong>'.$arrItemName[$i]. '.</strong> ' . $this->errorMsg[511] . ' (PCS)'); 
                }

                $itemName = strtolower(trim($arrItemName[$i]));
                if (in_array($itemName, $arrDetailKeys)) {
                    $this->addErrorList($arrayToJs, false, $itemName . '. ' . $this->errorMsg[215]);
                } else {
                    array_push($arrDetailKeys, $itemName);
                }
                
            }
        }


        return $arrayToJs;
    }

    function validateConfirm($rsHeader)
    {
        $id = $rsHeader[0]['pkey'];

        $rsPayment = $this->getPaymentMethodDetail($id);
        $termOfPayment = new TermOfPayment();
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
        }
    }

    function confirmTrans($rsHeader){
		
        $id = $rsHeader[0]['pkey'];

        $termOfPayment = new TermOfPayment();
		$rsTOP = $termOfPayment->getDataRowById($rsHeader[0]['termofpaymentkey']);  
		$isCash = ($rsTOP[0]['duedays'] == 0) ? true : false; 
   
    }

    function validateCancel($rsHeader, $autoChangeStatus = false)
    {
        $receivingPurchaseJewelry = new ReceivingPurchaseJewelry();

        $id = $rsHeader[0]['pkey'];

        //cek if isset receiving status is confirm or finished, can`t cancel
        $rsReceivingPurchase = $receivingPurchaseJewelry->searchData('','',true, ' and ' . $receivingPurchaseJewelry->tableName.'.refkey = '.$this->oDbCon->paramString($id).' and '.$receivingPurchaseJewelry->tableName.'.statuskey in (2,3) ');

        if(!empty($rsReceivingPurchase)) {
            $arrReceivingCode = array_column($rsReceivingPurchase,'code');
            $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' . $this->errorMsg[201].' ' .$this->errorMsg['purchaseOrderJewelry'][3] . '<br><strong>' . implode('<br>,', $arrReceivingCode).'</strong>');
        }


    }

    function cancelTrans($rsHeader, $copy)
    {
        $id = $rsHeader[0]['pkey'];

        $receivingPurchaseJewelry = new ReceivingPurchaseJewelry();

        //cek apakah ada penerimaan yang status menunggu ? kalau ada ubah ke batal
        $rsReceivingPurchase = $receivingPurchaseJewelry->searchData('','',true,' and ' . $receivingPurchaseJewelry->tableName.'.refkey = '.$this->oDbCon->paramString($id).' and '.$receivingPurchaseJewelry->tableName.'.statuskey = '.TRANSACTION_STATUS['menunggu'].' ');
        for($i=0;$i<count($rsReceivingPurchase);$i++) {
			$arrayToJs = $receivingPurchaseJewelry->changeStatus($rsReceivingPurchase[$i]['pkey'],4,'',false,true);
            if (!$arrayToJs[0]['valid'])
                throw new Exception('<strong>'.$rsHeader[0]['code'] . '</strong>. '.  $arrayToJs[0]['message']);    
        }

        if ($copy)
            $this->copyDataOnCancel($id);

    }

    function validateClose($rsHeader)
    {

        $id = $rsHeader[0]['pkey'];

        //cek apakah barang sudah di terima semua, kalau belum tidak bisa selesai
        $rsDetail = $this->getDetailById($id);
        
        $arrMsg = array();
        for($i=0; $i<count($rsDetail);$i++) {
            if(($rsDetail[$i]['receivedqtyinbaseunit'] < $rsDetail[$i]['qtyinbaseunit']) || $rsDetail[$i]['receivedqtyinpcs'] < $rsDetail[$i]['qtyinpcs'])
            {
                array_push($arrMsg, '<strong>'.$rsDetail[$i]['itemname'].'. </strong>'. $this->errorMsg['purchaseOrderJewelry'][4]);
            }
        }

        if(!empty($arrMsg)) {
            $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' . $this->errorMsg[201].'<br>' . implode('<br>', $arrMsg));
        }

    }

    function closeTrans($rsHeader){  

    }

    function afterStatusChanged($rsHeader){ 
        // retrieve latest status
        $rsHeader = $this->getDataRowById($rsHeader[0]['pkey']);
    
    }

    function afterAddDataOnCopy($pkey, $oldkey){
         
    }

    function updateGL($rs, $rsPayment)
    {
        if (!USE_GL)
            return;

    }

    function updateReceivingPurchaseOrderJewelryItem($pkey){ 
            
        $receivingPurchaseJewelry = new ReceivingPurchaseJewelry(); 
        
        $rsHeader = $this->getDataRowById($pkey);
        $rsDetail = $this->getDetailById($pkey); 

        for($i=0;$i<count($rsDetail); $i++){

            $sql = 'select 
                        coalesce(sum(receivedqtyinbaseunit),0) as totalreceivedqtyinbaseunit,
                        coalesce(sum(receivedqtyinpcs),0) as totalreceivedqtyinpcs
                    from 
                        '. $receivingPurchaseJewelry->tableName . ', '. $receivingPurchaseJewelry->tableNameDetail . '
                    where 
                        '. $receivingPurchaseJewelry->tableName . '.pkey = '. $receivingPurchaseJewelry->tableNameDetail . '.refkey and
                        '. $receivingPurchaseJewelry->tableName . '.refkey = '. $this->oDbCon->paramString($pkey) .' and 
                        '. $receivingPurchaseJewelry->tableNameDetail . '.refpodetailkey = ' . $rsDetail[$i]['pkey'] .' and 
                        ('. $receivingPurchaseJewelry->tableName . '.statuskey = 2 or '. $receivingPurchaseJewelry->tableName . '.statuskey = 3)';
 
            $rsTotal = $this->oDbCon->doQuery($sql);

        $sql = 'update 
                        ' . $this->tableNameDetail.' 
                set  
                        receivedqtyinbaseunit = '. $this->oDbCon->paramString($rsTotal[0]['totalreceivedqtyinbaseunit']) .',
                        receivedqtyinpcs = '. $this->oDbCon->paramString($rsTotal[0]['totalreceivedqtyinpcs']) .'
                where 
                        refkey = '.$this->oDbCon->paramString($pkey).' and 
                        pkey = '.$this->oDbCon->paramString($rsDetail[$i]['pkey'])
                ;
                
                $this->oDbCon->execute($sql);

                
            }

            //check if all item received, change PO status to finish
            $sql = 'select * from ' . $this->tableNameDetail.' where refkey = '.$this->oDbCon->paramString($pkey).' and  receivedqtyinbaseunit < qtyinbaseunit and receivedqtyinpcs < qtyinpcs';
            $rs = $this->oDbCon->doQuery($sql);
            
            $statuskey = (empty($rs)) ? 3 : 2; 
            
            if ($rsHeader[0]['statuskey'] <> $statuskey)
                $this->changeStatus($pkey,$statuskey,'',false,true);
    }

    

    function searchDataForAutoComplete($fieldname = '', $searchkey = '', $mustmatch = false, $searchCriteria = '', $orderCriteria = '', $limit = '')
    {
        $sql = 'select
					' . $this->tableName . '.pkey,  concat(' . $this->tableName . '.code,\' - \', ' . $this->tableSupplier . '.code) as value
				from 
					' . $this->tableName . ',' . $this->tableSupplier . ',' . $this->tableStatus . '
				where  		
					' . $this->tableName . '.supplierkey = ' . $this->tableSupplier . '.pkey and
					' . $this->tableName . '.statuskey = ' . $this->tableStatus . '.pkey 
			';

        if (!empty($fieldname)) {

            $sql .= ' and ';

            if ($mustmatch)
                $sql .= $fieldname . ' = ' . $this->oDbCon->paramString($searchkey);
            else
                $sql .= $fieldname . ' like ' . $this->oDbCon->paramString('%' . $searchkey . '%');
        }

        if ($searchCriteria <> '')
            $sql .= ' ' . $searchCriteria;

        if ($orderCriteria <> '') {
            $sql .= ' ' . $orderCriteria;

        }

        if ($limit <> '')
            $sql .= ' ' . $limit;


        return $this->oDbCon->doQuery($sql);
    }

    function reCountSubtotal($arrParam){
	
				$isPriceIncludeTax = (!empty($arrParam['chkIncludeTax'])) ? 1 : 0;
			
				$subtotal = 0 ;
                $afterFirstDiscount = 0;
				$grandtotal = 0;
				$gramasi = 0;
				
				
                $arrItemName = $arrParam['itemName'];
				$taxValue = $this->unFormatNumber($arrParam['taxValue']);  
				$finalDiscount = $this->unFormatNumber($arrParam['finalDiscount']); 
				$finalDiscountType = $arrParam['selFinalDiscountType']; 
				$finalDiscount2 = $this->unFormatNumber($arrParam['finalDiscount2']); 
				$finalDiscount2Type = $arrParam['selFinalDiscount2Type']; 
				$taxPercentage = $this->unFormatNumber($arrParam['taxPercentage']);  
				$shipmentFee = $this->unFormatNumber($arrParam['shipmentFee']); 
				$etcCost = $this->unFormatNumber($arrParam['etcCost']);  
                    
				$arrQty = $arrParam['qty']; 
				$arrPriceinunit = $arrParam['priceInUnit']; 
				$arrDiscountValueInUnit = $arrParam['discountValueInUnit']; 
				$arrDiscountType = $arrParam['selDiscountType'];  
				$arrTransUnitKey = $arrParam['selUnit']; 

                $arrIsPriceInPcs = (isset($arrParam['chkPriceInPcs'])) ? $arrParam['chkPriceInPcs'] : array();
                $arrQtyInPcs = (isset($arrParam['qtyInPcs'])) ? $arrParam['qtyInPcs'] : array();
                $arrPriceInPcs = (isset($arrParam['priceInPcs'])) ? $arrParam['priceInPcs'] : array();

        
        
				$arrItemDetail = array();
        
				for ($i=0;$i<count($arrItemName);$i++){

                    $itemName = $arrItemName[$i];
                    $transactionUnitKey = $arrTransUnitKey[$i];
                    $qty =  $this->unFormatNumber($arrQty[$i]);
                 
                    $priceInUnit = $this->unFormatNumber($arrPriceinunit[$i]);
                    $discount = $this->unFormatNumber($arrDiscountValueInUnit[$i]);
                    $discountType =  $this->unFormatNumber($arrDiscountType[$i]);
                    
                    if(isset($arrParam['chkPriceInPcs'])) {

                        $isPriceInPcs = $arrIsPriceInPcs[$i];
                        $qtyInPcs = $this->unFormatNumber($arrQtyInPcs[$i]);
                        $priceInPcs = $this->unFormatNumber($arrPriceInPcs[$i]);
                                        

                        if($isPriceInPcs == 1) {
                            if ($qty != 0){
                                $priceInBaseUnit = ($qtyInPcs * $priceInPcs) / $qty;
                                $priceInUnit = $priceInBaseUnit;
                            }
                        } else {
                            if ($qtyInPcs != 0) {
                                $priceInPcsValue = ($qty * $priceInUnit) / $qtyInPcs;
                                $priceInPcs = $priceInPcsValue;
                            }
                        }

                        $arrItemDetail[$i]['priceInUnit'] = $priceInUnit;
                        $arrItemDetail[$i]['priceInPcs'] = $priceInPcs;

                    }
                    
                    $arrItemDetail[$i]['baseUnitKey'] = $transactionUnitKey;
                    $arrItemDetail[$i]['qtyInBaseUnit'] = $qty;
                    $arrItemDetail[$i]['priceInBaseUnit'] = $priceInUnit;
                          
                    $discountValue = $discount;
                    if ($discount != 0){
                        if ($discountType == 2)
                            $discountValue = $discount/100 * $priceInUnit;
                    }

                    $detailSubtotal = $qty * ($priceInUnit - $discountValue);
                    $detailSubtotal = $qty * ($priceInUnit - $discountValue);

                    $arrItemDetail[$i]['detailSubtotal'] = $detailSubtotal;

                    $subtotal += $detailSubtotal ;  

        
				} 
				
				$grandtotal = $subtotal;
				
				if ($finalDiscount != 0){
					if ($finalDiscountType == 2)
						$finalDiscount = $finalDiscount/100 * $grandtotal;
				} 
				
                $afterFirstDiscount = $subtotal - $finalDiscount;

				if ($finalDiscount2 != 0){
					if ($finalDiscount2Type == 2)
						$finalDiscount2 = $finalDiscount2/100 * $afterFirstDiscount;
				} 
				
				$beforeTaxTotal = $afterFirstDiscount - $finalDiscount2;
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
				
		
				$reCountResult = array();
				$reCountResult['subtotal'] = $subtotal;
				$reCountResult['afterFirstDiscount'] = $afterFirstDiscount;
				$reCountResult['beforeTaxTotal'] = $beforeTaxTotal;
				$reCountResult['isPriceIncludeTax'] = $isPriceIncludeTax;
				$reCountResult['grandtotal'] = $grandtotal;
				$reCountResult['totalPayment'] = $totalPayment;
				$reCountResult['balance'] = $balance;
				$reCountResult['detailCOGS'] = $arrItemDetail;

				return $reCountResult;
				
	} 

    function normalizeParameter($arrParam, $trim = false){  
            
            $arrItemName = $arrParam['itemName'];

            $reCountResult = $this->reCountSubtotal($arrParam); 
            //$this->setLog($reCountResult, true);
            $arrParam['detailCOGS'] = $reCountResult['detailCOGS'];
            $arrParam['subtotal'] = $reCountResult['subtotal'];
            $arrParam['afterFirstDiscount'] = $reCountResult['afterFirstDiscount'];
            $arrParam['beforeTaxTotal'] = $reCountResult['beforeTaxTotal'];
            $arrParam['isPriceIncludeTax'] = $reCountResult['isPriceIncludeTax'];
            $arrParam['grandtotal'] = $reCountResult['grandtotal'];
            $arrParam['totalPayment'] = $reCountResult['totalPayment'];
            $arrParam['balance'] = $reCountResult['balance']; 

            for ($i=0;$i<count($arrItemName);$i++){ 
 
                $qtyinbaseunit = $arrParam['detailCOGS'][$i]['qtyInBaseUnit'];
                $arrParam['qtyInBaseUnit'][$i] = $qtyinbaseunit;
                //$arrParam['unitConvMultiplier'][$i] = $arrParam['detailCOGS'][$i]['unitConvMultiplier'];
                $arrParam['cogs'][$i] = $arrParam['detailCOGS'][$i]['cogs'];
                $arrParam['priceInBaseUnit'][$i] = $arrParam['detailCOGS'][$i]['priceInBaseUnit']; 
                $arrParam['detailSubtotal'][$i] = $arrParam['detailCOGS'][$i]['detailSubtotal'];
                
                $arrParam['priceInPcs'][$i] = $arrParam['detailCOGS'][$i]['priceInPcs'];;
                $arrParam['priceInUnit'][$i] = $arrParam['detailCOGS'][$i]['priceInUnit'];;

                // set default jadi 0 lg, utk handle copy on cancel
                $arrParam['receivedQtyInBaseUnit'][$i] = 0;
                $arrParam['receivedQtyInPcs'][$i] = 0;

            }

        $arrParam = parent::normalizeParameter($arrParam,true);

        return $arrParam; 
    }  

}

?>
