<?php
  
class PurchaseOrder extends BaseClass{ 
  
 
   function __construct(){
		
		parent::__construct();
       
		$this->tableName = 'purchase_order_header';
		$this->tableNameDetail = 'purchase_order_detail';
        $this->tablePurchaseRequest = 'purchase_request_header';
        $this->tableDetailSerial = 'purchase_order_detail_sn';

        $this->tablePurchaseCategory = 'purchase_category';
		$this->tableSupplier = 'supplier';
		$this->tableWarehouse = 'warehouse';
		$this->tableCurrency = 'currency';
		$this->tableAP = 'ap';
		$this->tableSalesOrderCarService = 'sales_order_car_service_header';
		$this->tableStatus = 'transaction_status';
		$this->tableItemUnit = 'item_unit'; 
		$this->tableMovement = 'item_movement'; 
		$this->tableHistory = 'history';
		$this->tablePayment= 'purchase_order_payment';
		$this->tableItem = 'item'; 	
		$this->isTransaction = true; 	
        $this->tableBrand = 'brand';
        $this->tableCarSeries = 'car_series';
        $this->tableItemGroup = 'item_group';
        $this->tableCategoryAssetItem = 'category_asset_item';
        $this->tableAssetItem = 'asset_item';
        $this->tableTermOfPayment = 'term_of_payment'; 
		   
		$this->securityObject = 'PurchaseOrder'; 
        $this->updatePurchaseOrderInvoiceReferenceSecurityObject = 'updatePurchaseOrderInvoiceReference';
        $this->updatePurchasePriceSecurityObject = 'updatePurchasePrice';
       
	    $this->activeModule = $this->isActiveModule(array('PurchasePricing')); 
	  	 
        $this->arrLinkedTable = array(); 
        $defaultFieldName = 'refkey';
        array_push($this->arrLinkedTable, array('table'=>'purchase_receive_header','field'=>$defaultFieldName));  
        array_push($this->arrLinkedTable, array('table'=>'ap','field'=>$defaultFieldName));  
       

        $this->arrDataDetailSN = array();  
        $this->arrDataDetailSN['pkey'] = array('hidDetailSNKey');
        $this->arrDataDetailSN['refkey'] = array('hidDetailKey','ref');  
        $this->arrDataDetailSN['refheaderkey'] = array('pkey','ref');  
        $this->arrDataDetailSN['serialnumber'] = array('serialNumberDetail',array('mandatory'=>true));
        $this->arrDataDetailSN['costinbaseunit'] = array('COGSSN', 'number');

        $this->arrDataDetail = array();  
        $this->arrDataDetail['pkey'] = array('hidDetailKey',array('dataDetail' => array('dataset' => $this->arrDataDetailSN, 'tableName' => $this->tableDetailSerial)));
        $this->arrDataDetail['refkey'] = array('pkey','ref');
        $this->arrDataDetail['itemkey'] = array('hidItemKey');
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
        $this->arrDataDetail['isamortized'] = array('chkIsAmortize');
        $this->arrDataDetail['itemgroupkey'] = array('selPurchaseGroup');
        $this->arrDataDetail['name'] = array('detailName');
        $this->arrDataDetail['brandkey'] = array('hidBrandDetailKey');
        $this->arrDataDetail['typekey'] = array('hidTypeDetailKey');
        $this->arrDataDetail['serialnumber'] = array('serialNumber');
        $this->arrDataDetail['categorykey'] = array('hidCategoryDetailKey');           
        $this->arrDataDetail['qtyinpcs'] = array('qtyInPcs', 'number');
        $this->arrDataDetail['receivedqtyinpcs'] = array('receivedQtyInPcs','number');
        $this->arrDataDetail['priceinpcs'] = array('priceInPcs', 'number');
        $this->arrDataDetail['ispriceinpcs'] = array('chkPriceInPcs');
        $this->arrDataDetail['number'] = array('numberDetail', 'number');
        $this->arrDataDetail['trdesc'] = array('trDetailDesc');
        $this->arrDataDetail['snlist'] = array('snList');

       
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
        
	    $this->importUrl = 'import/purchaseOrder';
	   
        $this->printMenu = array();  
        array_push($this->printMenu,array('code' => 'printTransaction', 'name' => $this->lang['printTransaction'],  'icon' => 'print', 'url' => 'print/purchaseOrder'));
        array_push($this->printMenu,array('code' => 'printReceipt', 'name' => $this->lang['printReceipt'],  'icon' => 'print', 'url' => 'print/purchaseOrderDelivery'));
        
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

              'AP.class.php',
              'CashBank.class.php',
              'COALink.class.php',
              'GeneralJournal.class.php',
              'Item.class.php',
              'ItemUnit.class.php',
              'ItemMovement.class.php',
              'Marketplace.class.php',
              'PaymentMethod.class.php',
              'ItemProportional.class.php',
              'PurchaseReceive.class.php',
              'PurchaseRequest.class.php',
              'Supplier.class.php',
              'TermOfPayment.class.php',
			  'PurchaseCategory.class.php',
              'Currency.class.php',
              'Brand.class.php',
              'AssetItemTurnover.class.php',
              'AssetItemMovement.class.php',
              'AssetItem.class.php',
              'CategoryAssetItem.class.php',
              'CarSeries.class.php',
              'PrepaidExpense.class.php',
              'PurchasePricing.class.php'

        ));         
       
       if($this->isActiveModule('SalesOrderCarService'))
            $this->includeClassDependencies(array('SalesOrderCarService.class.php'));
       
        $this->overwriteConfig();
       
   }
    
    function getQuery(){
	   
	   $sql = '
			SELECT '.$this->tableName.'.* ,
               '.$this->tablePurchaseRequest.'.code as refcode,
			   '.$this->tableSupplier.'.code as suppliercode,
			   '.$this->tableSupplier.'.name as suppliername,
			   '.$this->tableWarehouse.'.code as warehousecode,
			   '.$this->tableWarehouse.'.name as warehousename,
			   '.$this->tableStatus.'.status as statusname,
               '.$this->tablePurchaseCategory.'.name as categoryname,
               '.$this->tableTermOfPayment.'.code as termofpaymentcode,
               '.$this->tableTermOfPayment.'.name as termofpaymentname,
               '.$this->tableTermOfPayment.'.duedays as termofpaymentduedays
			FROM 
                 '.$this->tableName.' 
                    left join '.$this->tablePurchaseRequest.' on '.$this->tableName.'.refkey = '.$this->tablePurchaseRequest.'.pkey
                    left join '.$this->tablePurchaseCategory.' on '.$this->tableName.'.categorykey = '.$this->tablePurchaseCategory.'.pkey   
                    left join '.$this->tableTermOfPayment.' on '.$this->tableName.'.termofpaymentkey = '.$this->tableTermOfPayment.'.pkey,
                 '.$this->tableStatus.',  
                 '.$this->tableSupplier.' ,
                 '.$this->tableWarehouse.'  
			WHERE '.$this->tableName.'.supplierkey = '.$this->tableSupplier.'.pkey and
                 '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey and
                 '.$this->tableName.'.warehousekey = '.$this->tableWarehouse.'.pkey  
 		' .$this->criteria ;  
		  
        $sql .=  $this->getCompanyCriteria() ;
        $sql .=  $this->getWarehouseCriteria() ;
      
      return $sql;
    }
    
    function afterStatusChanged($rsHeader){ 
        // retrieve latest status
        $rsHeader = $this->getDataRowById($rsHeader[0]['pkey']);
        
        if ($rsHeader[0]['isfullreceive'] == 1 && $rsHeader[0]['statuskey'] == 2){  
            $sql = 'update '.$this->tableNameDetail.' set receivedqtyinbaseunit = qtyinbaseunit,receivedqtyinpcs = qtyinpcs where refkey  = '.$this->oDbCon->paramString($rsHeader[0]['pkey']);
            $this->oDbCon->execute($sql);  
            $this->changeStatus($rsHeader[0]['pkey'],3); 
        }
        
		if( $this->isActiveModule('marketplace')){
			if ($rsHeader[0]['isfullreceive'] == 1 && ($rsHeader[0]['statuskey'] == 2 || $rsHeader[0]['statuskey'] == 4)){  
				$marketplace = new Marketplace();
				$rsDetail = $this->getDetailById($rsHeader[0]['pkey']);
				$arrItemKey = array_column($rsDetail,'itemkey'); 
				$marketplace->updateProductsQOHInAllMarketplace($arrItemKey); 
			} 
		}
         
    }
    
    function afterAddDataOnCopy($pkey, $oldkey){
        $sql = 'update ' .$this->tableNameDetail.' set receivedqtyinbaseunit = 0, receivedqtyinpcs = 0  where refkey = ' . $this->oDbCon->paramString($pkey);    
        $this->oDbCon->execute($sql); 
    }
    
    
	function reCountSubtotal($arrParam){
		
				$item = new Item(); 
        
				// default, ongkir dan cost dibagi berdasarkan proporsional gramasi/kubikasi
				$useGramasi = $this->loadSetting('costProportionalType');
				$useCostProportionalOnGL = $this->loadSetting('costProportionalToCOGSonGL');
		
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
                    
				$arrQty = $arrParam['qty']; 
				$arrPriceinunit = $arrParam['priceInUnit']; 
				$arrDiscountValueInUnit = $arrParam['discountValueInUnit']; 
				$arrDiscountType = $arrParam['selDiscountType'];  
				$arrTransUnitKey = $arrParam['selUnit']; 
                $arrItemGroup = (isset($arrParam['selPurchaseGroup'])) ? $arrParam['selPurchaseGroup'] : array();
                $unitName = $arrParam['detailName'];

                $arrIsPriceInPcs = (isset($arrParam['chkPriceInPcs'])) ? $arrParam['chkPriceInPcs'] : array();
                $arrQtyInPcs = (isset($arrParam['qtyInPcs'])) ? $arrParam['qtyInPcs'] : array();
        
                $arrPriceInPcs = (isset($arrParam['priceInPcs'])) ? $arrParam['priceInPcs'] : array();
        
				$arrItemDetail = array();
                $rsItemCol = $item->searchDataRow(array($item->tableName.'.pkey',
                                                     $item->tableName.'.gramasi', 
                                                     $item->tableName.'.isweightfixed', 
                                                     $item->tableName.'.baseunitkey'),
                                                ' and '. $item->tableName.'.pkey in ('.$this->oDbCon->paramString($arrItemkey,',').')');
                $rsItemCol = array_column($rsItemCol,null,'pkey');
        
				for ($i=0;$i<count($arrItemkey);$i++){
					
                     // versi jual unit
                    if($arrItemGroup[$i] == 2){
                        if (empty($unitName[$i]))  continue; 
                    }else{
                        // versi normal
                        if (empty($arrItemkey[$i]))  continue; 
                    }
			 
                    //$rsItem = $item->getDataRowById($arrItemkey[$i]);
                    $itemkey = $arrItemkey[$i];
				    $rsItem = $rsItemCol[$itemkey];
                    
                    $transactionUnitKey = $arrTransUnitKey[$i];
                    $baseunitkey = $rsItem['baseunitkey'];
                    $qty =  $this->unFormatNumber($arrQty[$i]);
                    $conversionMultiplier = $item->getConvMultiplier($itemkey,$transactionUnitKey,$baseunitkey); 
                    $qtyinbaseunit = $qty * $conversionMultiplier; 
                    $priceInUnit = $this->unFormatNumber($arrPriceinunit[$i]);
                    
                    $discount = $this->unFormatNumber($arrDiscountValueInUnit[$i]);
                    $discountType =  $this->unFormatNumber($arrDiscountType[$i]);
             
                    if ($arrItemGroup[$i] == 2) {
                        $baseunitkey = $transactionUnitKey;
                        $conversionMultiplier = 1;
                        $qtyinbaseunit = 1;
                    }                 
                    
                    
                    //if(isset($arrParam['chkPriceInPcs'])){ // gk bisa karena gk semua ad chk harga per pcs
                    if(in_array(PLAN_TYPE['categorykey'], array(COMPANY_TYPE['jewelry']))){   
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
 
                        $arrItemDetail[$i]['priceInPcs'] = $priceInPcs;
                        $arrItemDetail[$i]['qtyInPcs'] = $qtyInPcs;

                    }
                    
                    $arrItemDetail[$i]['baseUnitKey'] = $baseunitkey;
                    $arrItemDetail[$i]['unitConvMultiplier'] = $conversionMultiplier;
                    $arrItemDetail[$i]['qtyInBaseUnit'] = $qtyinbaseunit ;
                    $arrItemDetail[$i]['priceInUnit'] = $priceInUnit ; // perlu ad, kalo gk nilainya kereset jd 0 karena di normalize akan diisi ulang
                    $arrItemDetail[$i]['priceInBaseUnit'] = $priceInUnit / $conversionMultiplier ;
                          
                    $discountValue = $discount;
                    if ($discount != 0){
                        if ($discountType == 2)
                            $discountValue = $discount/100 * $priceInUnit;
                    }

                    //$detailSubtotal = $qtyinbaseunit * ($priceInUnit - $discountValue);
                    $detailSubtotal = $qty * ($priceInUnit - $discountValue);
                    //$this->setLog($qty .'* ( ' .$priceInUnit  .'-'. $discountValue.')');
                    $arrItemDetail[$i]['detailSubtotal'] = $detailSubtotal;

                    $subtotal += $detailSubtotal ;  

                    $arrItemDetail[$i]['gramasi'] =  ($rsItem['gramasi']*$qtyinbaseunit);
                    
                    // utk jewelry, harusnya sama dengan gramsi diatas, tp dipisah saja utk jaga2
                    // qtyinpcs adalah qty dalam gramasi, cuma variablenya sudah terlanjur pake "inpcs"
                    // kalo user gramasi gk disi, maka dianggap sama dengan qtyinbaseunit, harusnya masih aman karena sampe skrg baru Hans yg pake gramasi
                    if($rsItem['isweightfixed'])
                        $arrItemDetail[$i]['qtyInPcs'] = ($rsItem['gramasi'] > 0) ? ($rsItem['gramasi']*$qtyinbaseunit) : $qtyinbaseunit;
                        
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
				
				
/*				//count COGS per item 
				$totalCost = -$finalDiscount;
        
                // kalo pake cash, balance artinya selisih bayar
                if($rsTOP[0]['duedays'] == 0) 
                    $totalCost += $balance; 
            
            
				if (!USE_GL) 
				    $totalCost +=  $shipmentFee + $etcCost;*/
				 
		 		for ($i=0;$i<count($arrItemkey);$i++){
				
                   // versi jual unit
                    if($arrItemGroup[$i] == 2){
                        if (empty($unitName[$i]))  continue; 
                    }else{
                        // versi normal
                        if (empty($arrItemkey[$i]))  continue; 
                    }
                    
                    $discount = $this->unFormatNumber($arrDiscountValueInUnit[$i]);
                    $discountType =  $this->unFormatNumber($arrDiscountType[$i]);
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
					  
                    //$percentageCost = ($totalProportion == 0 ) ? 0 :  ($itemProportion / $totalProportion) * $totalCost;  
                     
                    // new calculation
                    $totalItemValue = $priceInBaseUnit;
                    $discountValue = ($discount != 0 && $discountType == 2) ? $discount/100 * $priceInUnit : $discount; // discount in transaction unit
                    $discountInBaseUnit =  $discountValue / $conversionMultiplier;
                    
                    //$this->setLog('$totalItemValue ' . $totalItemValue . ' - ' . $discountInBaseUnit);
                    $totalItemValue -= $discountInBaseUnit; 

                    //$finalDiscountProportion = ($totalItemValue / $subtotal) * $finalDiscount; 
                    //$this->setLog('('.$proportion .'*'. $finalDiscount.') /'. $qtyInBaseUnit);
                    $finalDiscountProportion = ($proportion * $finalDiscount) / $qtyInBaseUnit; 
                    $totalItemValue -= $finalDiscountProportion;
                    
                    //$this->setLog('$totalItemValue ' . $totalItemValue);
                    
                     if ($isPriceIncludeTax) {  
                            $taxValue  = ($taxPercentage/(100 + $taxPercentage)) * $totalItemValue;
                            $totalItemValue -= $taxValue;    
                            //$totalItemValue += ($finalDiscountProportion + $discountInBaseUnit ); 
                    }  
					
					// jika tdk menggunakan GL, pasti proportional 
					if (!USE_GL || $useCostProportionalOnGL == 1) {
						 
						$totalCost = 0; 

						// kalo pake cash, balance artinya selisih bayar
						if($rsTOP[0]['duedays'] == 0) $totalCost += $balance;  
						$totalCost += ($shipmentFee + $etcCost); 
						$totalItemValue +=  $proportion * $totalCost / $qtyInBaseUnit ; 
					}
					
					//$arrItemDetail[$i]['cogs']  = ($arrItemDetail[$i]['detailSubtotal'] + $percentageCost) / $qtyInBaseUnit; 
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
 
     function validateForm($arr,$pkey = ''){
		$item = new Item();  
        $purchaseRequest = new PurchaseRequest();
		
		$arrayToJs = parent::validateForm($arr,$pkey); 
        
		$supplierkey = $arr['hidSupplierKey']; 
		$arrItemkey = $arr['hidItemKey']; 
        $arrItemGroup = (isset($arr['selPurchaseGroup'])) ? $arr['selPurchaseGroup'] : array();
		$arrQty = $arr['qty']; 
		$arrPriceinunit = $arr['priceInUnit'];
		$arrSelUnit = $arr['selUnit']; 
        $arrQtyInPcs = $arr['qtyInPcs'];
        $arrSerialNumberDetail = $arr['serialNumberDetail'];
        $isFullReceive = $arr['chkIsFullReceive'];
		
		  
        if (PLAN_TYPE['maxpurchaseorder'] >= 0){ 
            $month = str_replace('\'','',$this->oDbCon->paramDate($arr['trDate'],' / ','m'));
            $year = str_replace('\'','',$this->oDbCon->paramDate($arr['trDate'],' / ','Y'));
            
            $sql = 'select
                        count(pkey) as total 
                    from 
                        ' .$this->tableName.'
                    where 
                        month(trdate) = '.$this->oDbCon->paramString($month).' and year(trdate) = '. $this->oDbCon->paramString($year);
            
            if (!empty($pkey))
                $sql .= ' and pkey <> ' . $pkey;
             
            $rs = $this->oDbCon->doQuery($sql);
            
            if($rs[0]['total'] >= PLAN_TYPE['maxpurchaseorder'])   
              $this->addErrorList($arrayToJs,false,$this->errorMsg['limit'][1]);   
        }
         
		//validasi kalo status gk menunggu gk bisa edit 
		if (!empty($pkey)){
			$rs = $this->getDataRowById($pkey);
			if ($rs[0]['statuskey'] <> 1){
				$this->addErrorList($arrayToJs,false,$this->errorMsg[212]);
			}
		} 
				 
		if(empty($supplierkey)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['supplier'][1]);
		}
			 
        if(empty($arrItemkey)) 
            $this->addErrorList($arrayToJs,false,  $this->errorMsg[501]);  
 
        // cek permintaan pembelian masih valid atau gk
        if(isset($arr['hidPurchaseRequestKey']) && !empty($arr['hidPurchaseRequestKey'])){
            $purchaseRequest = new PurchaseRequest();
            $rsPurchaseRequest =  $purchaseRequest->getDataRowById($arr['hidPurchaseRequestKey']);
            if(empty($rsPurchaseRequest) || !in_array($rsPurchaseRequest[0]['statuskey'], array(TRANSACTION_STATUS['konfirmasi'],TRANSACTION_STATUS['selesai'])))
                $this->addErrorList($arrayToJs,false,$this->errorMsg['purchaseRequest'][2]); 
        }
 
            
        $arrDetailKeys = array(); 
        $rsItemCol = $item->searchDataRow(array($item->tableName.'.pkey',
                                            $item->tableName.'.name',
                                            $item->tableName.'.needsn'
                                            ), ' and ' . $item->tableName.'.pkey in ('.$this->oDbCon->paramString($arrItemkey,',').')');
        $rsItemCol = array_column($rsItemCol, null,'pkey');

        $allowDuplicate = false;
        if($this->loadSetting('allowDuplicateItemOnPurchaseOrder') == 1)  $allowDuplicate = true;
         
		for($i=0;$i<count($arrItemkey);$i++) { 
            // utk jenis jual unit, di skip
            if ($arrItemGroup[$i] == 2) continue ;

			if (empty($arrItemkey[$i]) ){ 
				$this->addErrorList($arrayToJs,false, $this->errorMsg['item'][1]); 	
			} else{
                
                $rsItem = $rsItemCol[$arrItemkey[$i]];
                if ( $this->unFormatNumber($arrQty[$i]) <= 0 || $this->unFormatNumber($arrPriceinunit[$i]) <= 0){
                    $this->addErrorList($arrayToJs,false,$rsItem['name']. '. ' . $this->errorMsg[500]); 
                }

                 // cek punya konversi unit utk satuan yg dipilih gk  
                $conv = $item->getConvMultiplier($arrItemkey[$i],$arrSelUnit[$i]);
                if (empty($conv)){                    
		          $this->addErrorList($arrayToJs,false,$rsItem['name']. '. ' . $this->errorMsg['itemUnitConversion'][3]); 
                } 

                // cek ada detail double gk  
                if(!$allowDuplicate){ 
                    if (in_array($arrItemkey[$i],$arrDetailKeys)){                       
				        $this->addErrorList($arrayToJs,false, $rsItem['name'].'. '.$this->errorMsg[215]);  
                    }else{ 
                        array_push($arrDetailKeys, $arrItemkey[$i]);
                    } 
                }

                 if(in_array(PLAN_TYPE['categorykey'], array(COMPANY_TYPE['jewelry']))){  
                    if(isset($arr['qtyInPcs']) && $this->unFormatNumber($arr['qtyInPcs'][$i]) <= 0 ) {                   
			         $this->addErrorList($arrayToJs,false,'<strong>'.$rsItem['name']. '.</strong> ' . $this->errorMsg[510] . ' (Gr)'); 
                    } 
                   if(isset($arr['priceInPcs']) && $this->unFormatNumber($arr['priceInPcs'][$i]) <= 0) {
                        $this->addErrorList($arrayToJs,false,'<strong>'.$rsItem['name']. '.</strong> ' . $this->errorMsg[511] . ' (Gr)'); 
                    }

                }
                
      		  if($rsItem['needsn'] == 1 && $isFullReceive == 1) {
 
                        if(empty($arrSerialNumberDetail[$i])) {
                            $this->addErrorList($arrayToJs,false,'<strong>'.$rsItem['name']. '.</strong> '. $this->errorMsg['serialnumber'][1]); 
                        }
                  
                        // validasi jumlah serial number dan jumlah unit
                        // harus cek satu2 ad yg kosong gk, karena ad bug, kalo cuma 1 pcs, gk diisi SNnya, dianggap ada, karena indexnya ada, tp valuenya kosong
                        //$qtySN = count($arr['serialNumberDetail'][$i]); 
                        $qtySN = count(array_filter($arr['serialNumberDetail'][$i] ?? []));
                  
                        if ($this->unFormatNumber($arrQty[$i]) <> $qtySN){
                            $this->addErrorList($arrayToJs,false,$rsItem['name']. '. ' . $this->errorMsg['serialnumber'][2]); 
                        } 
                    
                }
            } 
             
		} 


        if (!empty($arrItemGroup)) {
            $assetUnitName = $arr['detailName']; 
            for($i=0;$i<count($arrItemkey);$i++) { 
                 
                if ($arrItemGroup[$i] == 2 && empty($assetUnitName[$i]) ){ 
                    $this->addErrorList($arrayToJs,false, $this->errorMsg['item'][1]); 	
                }
            }
        }

		return $arrayToJs;
	 }
	  

	function validateConfirm($rsHeader){
		
        $warehouse = new Warehouse();  
        $coaLink = new COALink();
        
		$id = $rsHeader[0]['pkey'];
		
		$purchaseOverCOGS = $this->loadSetting('purchaseOverCOGS');
		$purchaseOverCOGS = ($purchaseOverCOGS == 2) ? false : true; 
		
		$purchaseOverThreshold = $this->loadSetting('purchaseOverThreshold');
		$purchaseOverThreshold = ($purchaseOverThreshold == '') ? 0 : $purchaseOverThreshold; 
		
         // cek permintaan pembelian masih valid atau gk
        if(!empty($rsHeader[0]['refkey'])){
            $purchaseRequest = new PurchaseRequest();
            $rsPurchaseRequest =  $purchaseRequest->getDataRowById($rsHeader[0]['refkey']); 
            if(empty($rsPurchaseRequest) || !in_array($rsPurchaseRequest[0]['statuskey'], array(TRANSACTION_STATUS['konfirmasi'],TRANSACTION_STATUS['selesai'])))
                $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg['purchaseRequest'][2]); 
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
    
        if (USE_GL){ 
            $arrCOA = array();
            array_push($arrCOA, 'inventory' , 'inventorytemp', 'taxin', 'othercost',  'purchaseretaildiscount', 'shippingcost' , 'otherrevenue'); 
            for ($i=0;$i<count($arrCOA);$i++){
                $rsCOA = $coaLink->getCOALink ($arrCOA[$i], $warehouse->tableName,$rsHeader[0]['warehousekey'], 0); 
                if (empty($rsCOA))	
                    $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$arrCOA[$i]. ' ' .$this->errorMsg['coa'][3]);
            }   

            if ($isCash){   
                for($i=0;$i<count($rsPayment); $i++){ 
                    if ($rsPayment[$i]['amount'] > 0 ){ 
                        $rsCOA = $coaLink->getCOALink ('payment', $warehouse->tableName,$rsHeader[0]['warehousekey'], 0); 
                        if (empty($rsCOA))	
                            $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg['coa'][3]);
                    }
                }      
            }else{ 
                    $rsCOA = $coaLink->getCOALink ('ap', $warehouse->tableName,$rsHeader[0]['warehousekey'], 0); 
                    if (empty($rsCOA))	
                        $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg['coa'][3]);
            } 
        }
 
		 
        
        if(!$purchaseOverCOGS || $purchaseOverThreshold > 0){
            
			$item = new Item();
            
        	$rsDetail = $this->getDetailById($id);
            
			$arrItemKey = array_column($rsDetail,'itemkey');
            
			$rsItem = $item->searchDataRow(array($item->tableName.'.pkey',$item->tableName.'.name',$item->tableName.'.cogs',$item->tableName.'.sellingprice'), 
									   ' and '.$item->tableName.'.pkey in ('.$this->oDbCon->paramString( $arrItemKey ,',').')');
			$rsItem = array_column($rsItem,null,'pkey');


            //validasi harga jual 
            if(!$purchaseOverCOGS){

                for($i=0;$i<count($rsDetail);$i++){
                    $arrItem = $rsItem[$rsDetail[$i]['itemkey']];
                    $itemUnitPrice = $rsDetail[$i]['total'] /  $rsDetail[$i]['qtyinbaseunit']; // biar kehitung jg discuntnya
                    if ($itemUnitPrice > $arrItem['sellingprice']) 
                        $this->addErrorLog(false,'<strong>'.$arrItem['name'].'</strong>. '.$this->errorMsg['purchaseOrder'][3]);
                }
            }

            // pembelian diatas margin pembelian terakhir
            // kecuali blm pernah ad pembelian sebelumnya
            
            $security = new Security();
            $hasPurchaseOverAccess = $security->isAdminLogin('PurchaseOverThreshold',10);
 
            if(!$hasPurchaseOverAccess && $purchaseOverThreshold > 0){ 
                  
                $rsPurchase = $this->getLatestPurchase($id, $arrItemKey);
                
                if(!empty($rsPurchase)){
                     $rsPurchase = array_column($rsPurchase,null,'itemkey');
                
                     for($i=0;$i<count($rsDetail);$i++){
                         
                        $arrItem = $rsItem[$rsDetail[$i]['itemkey']];
                         
                        $itemkey = $rsDetail[$i]['itemkey'];
                         
                        // kalo blm ad historinya 
                        if(!isset($rsPurchase[$itemkey])) continue;
                            
                        $latestPrice = $rsPurchase[$itemkey]['total'] / $rsPurchase[$itemkey]['qtyinbaseunit']; // biar kehitung jg discuntnya
                        $itemUnitPrice = $rsDetail[$i]['total'] /  $rsDetail[$i]['qtyinbaseunit']; // biar kehitung jg discuntnya

                        if($itemUnitPrice <= $latestPrice) continue;

                        if ( (($itemUnitPrice-$latestPrice) / $latestPrice * 100) > $purchaseOverThreshold) 
                            $this->addErrorLog(false,'<strong>'.$arrItem['name'].'</strong>. '.$this->errorMsg['purchaseOrder'][4]);

                    }
                } 
                 
            }


        }
      
	 }
    
    function getLatestPurchase($id=0, $arrItemKey= array()){
        $sql = 'select ' .$this->tableNameDetail.'.* 
                from ' . $this->tableName.', ' .$this->tableNameDetail.'
                where ' . $this->tableName.'.pkey = ' .$this->tableNameDetail.'.refkey
                        and ' . $this->tableName.'.statuskey in (2,3)
                        and ' .$this->tableNameDetail.'.itemkey in ('.$this->oDbCon->paramString($arrItemKey,',').')
                        and ' . $this->tableName.'.pkey <> '.$this->oDbCon->paramString($id).'
                ';
         
        return $this->oDbCon->doQuery($sql);
    }
	
	
 	 function getItemMovementException(){
    
        // cari key yg gk perlu update stok dulu
        $arrExcp = array();
	    $typeKey = $this->getTableKeyAndObj($this->tableSalesOrderCarService,array('key')); 
        array_push($arrExcp,$typeKey['key']);
             
         return $arrExcp;
     }

     function seperatingItemUnit($rsDetail = array(), $groupType = 1){
        for($i=0;$i<=count($rsDetail); $i++){ 
            if ($rsDetail[$i]['itemgroupkey'] != $groupType) {
                unset($rsDetail[$i]);
            }
        }
        return array_values($rsDetail);
    }

	 
	function confirmTrans($rsHeader){
		   
        $id = $rsHeader[0]['pkey'];
        
		$supplier = new Supplier();
		$warehouse = new Warehouse();
        $coaLink = new COALink();
        $item = new Item();
        $useItemGroup = $this->isActiveModule('AssetItem');
		
		$rsSupplier = $supplier->getDataRowById($rsHeader[0]['supplierkey']);
		$note = $rsHeader[0]['code'].'. Beli dari '.$rsSupplier[0]['name'];
		$rsWarehouse = $warehouse->getDataRowById($rsHeader[0]['warehousekey']);
		$notecash = $rsHeader[0]['code'].'. Kas Keluar dari '.$rsWarehouse[0]['name'].' untuk pembelian barang dari '.$rsSupplier[0]['name'];
		$rsDetail = $this->getDetailById($rsHeader[0]['pkey']);
		
        $arrItemkey = array_column($rsDetail,'itemkey');
		
        if ($useItemGroup) {
            $rsDetailUnit = $rsDetail ;
            $rsDetail = $this->seperatingItemUnit($rsDetail, 1);
            $rsDetailUnit = $this->seperatingItemUnit($rsDetailUnit, 2);
        }
		 
		$termOfPayment = new TermOfPayment();
		$rsTOP = $termOfPayment->getDataRowById($rsHeader[0]['termofpaymentkey']);  
		$isCash = ($rsTOP[0]['duedays'] == 0) ? true : false; 
	   
        $rsPayment = array();
        
		// MENGHITUNG PAYMENT
			if ($isCash){
				$rsPayment = $this->getPaymentMethodDetail($id);   
				if(ADV_FINANCE){ 
                    //$cashMovement = new CashMovement();  
                    
                    $cashBank = new CashBank();     
                    for($i=0;$i<count($rsPayment); $i++){  
                        if($rsPayment[$i]['amount'] == 0) continue;
                            
                        if (USE_GL) {
                           $rsPaymentCOA = $coaLink->getCOALink ('payment', $warehouse->tableName,$rsHeader[0]['warehousekey'], $rsPayment[$i]['paymentkey']); 
                           $coakey = $rsPaymentCOA[0]['coakey']; 
                       }else{
                           $coakey = $rsPayment[$i]['paymentkey'];
                       }    

                        /*if(!empty($rsPaymentCOA))
                         $cashMovement->updateCashMovement($id, $rsPaymentCOA[0]['coakey'],$rsPayment[$i]['amount'],$this->tableName, $rsHeader[0]['warehousekey'], $notecash,$rsHeader[0]['trdate']);
                        */

                        $arrItemName =  array_column($rsDetail,'itemname');

                        $rsCashBank = $cashBank->addCashBank($rsHeader,$this->tableName, array('supplierkey' => $rsHeader[0]['supplierkey'],'coakey' => $coakey, 'desc' => $note, 'amount' => -$rsPayment[$i]['amount'])); 
                        $rsPayment[$i]['cashBankKey'] = $rsCashBank['pkey'];
                    }          
                }                  
			}
			else{
				//update AP
				$ap = new AP();
				
				$arrParam = array();	
                 
                $rsAPKey = $ap->getTableKeyAndObj($this->tableName,array('key')); 
                $arrParam['code'] = 'xxxxxx';
				$arrParam['hidSupplierKey'] = $rsHeader[0]['supplierkey'];
				$arrParam['hidRefKey'] = $id;
				$arrParam['hidRefHeaderKey'] = $id;
                $arrParam['hidRefCode'] =  $rsHeader[0]['code'];
                $arrParam['hidRefInvoiceCode'] =  $rsHeader[0]['refinvoicecode']; // agar bisa muncul di UI portal jg
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
			// END           
        if ($rsHeader[0]['isfullreceive']){
            
            if(!in_array($rsHeader[0]['reftabletype'],$this->getItemMovementException())){
                $itemMovement = new ItemMovement();  

                //return item, qtyinbaseunit, costinbaseunit  proporsional dan bukan proporsional
                //seharusnya ini tidak ada masalah jika tidak menggunakan modul item proportional
                //file modul item proporsional harus ada
                //atau lebih baik pakai settingan (?)
				// sementara yg full  receive saja dulu
				if ($this->isActiveModule('ItemProportional'))
                	$rsDetail = $this->getDetailItemProportional($rsDetail);

                $arrItem = $item->searchDataRow( array($item->tableName.'.pkey',$item->tableName.'.code',$item->tableName.'.warrantyperiodkey',$item->tableName.'.warrantyvendorperiodkey',$item->tableName.'.isrental',$item->tableName.'.needsn'),
                                            ' and '.$item->tableName.'.pkey in ('.$this->oDbCon->paramString($arrItemkey,',').')'
                                      );
                $arrItem = array_column($arrItem,null,'pkey');

                $rsSNCol = $this->getSerialNumber(array_column($rsDetail,'pkey')); 
                $rsSNCol = $this->reindexDetailCollections($rsSNCol,'refkey'); 
				
				
                for($i=0;$i<count($rsDetail); $i++){	 

                    $rsItem = $arrItem[$rsDetail[$i]['itemkey']];	
                    //kalau yang sisa di skip tidak perlu update item movement
                    if($rsDetail[$i]['iswaste'] == 1) continue;
                    $itemMovement->updateItemMovement($id,$rsDetail[$i]['itemkey'],array('qtyinbaseunit' => $rsDetail[$i]['qtyinbaseunit'], 'qtyinpcs' => $rsDetail[$i]['qtyinpcs'] ),$rsDetail[$i]['costinbaseunit'],$this->tableName, $rsHeader[0]['warehousekey'], $note,$rsHeader[0]['trdate']);
           
                    if($rsItem['needsn'] == 1){

                        $rsSN = $rsSNCol[$rsDetail[$i]['pkey']];

                         $rsTableKey = $this->getTableKeyAndObj($this->tableName,array('key'));

                        for($j=0;$j<count($rsSN); $j++)
                                    $itemMovement->updateItemSNMovement( 
                                            array(
                                            'refkey' => $rsDetail[$i]['pkey'],
                                            'refheaderkey' => $id,
                                            'itemkey' => $rsDetail[$i]['itemkey'],
                                            'sn' => $rsSN[$j]['serialnumber'],
                                            'qtyinbaseunit' => 1,
                                            'costinbaseunit' => $rsDetail[$i]['costinbaseunit'],
                                            'warehousekey' => $rsHeader[0]['warehousekey'],
                                            'note' => $note,
                                            'trdate' => $rsHeader[0]['trdate'] ,
                                            'reftabletype' => $rsTableKey['key'] 
                                )); 

                    }
                }

                if(!empty($rsDetailUnit))
                    $this->addItemAsset($rsHeader,$rsDetailUnit);
            }
        } 
        
        // update amortisasi
        if($this->isActiveModule('Amortization')){
            $this->addAmortization($rsHeader, $rsDetail);
        }
        
        //update jurnal umum 
        $this->updateGL($rsHeader,$rsPayment);
	} 
 
    function addItemAsset($rsHeader,$rsDetail){ 
        $assetItem = new AssetItem();
        $assetItemMovement = new AssetItemMovement();
        $assetItemTurnover = new AssetItemTurnover();
        $supplier = new Supplier();
        $rsSupplier = $supplier->getDataRowById($rsHeader[0]['supplierkey']);
        
        $otherCost =  ($rsHeader[0]['shipmentfee'] + $rsHeader[0]['etccost']) *  $rsHeader[0]['rate'];
        $subtotalInventory = $rsHeader[0]['subtotal'] *  $rsHeader[0]['rate'];
        
        for($i=0;$i<count($rsDetail);$i++){
            
            $arrParam = array();
            $arrParam['code'] = 'XXXXX';
            $arrParam['name'] = $rsDetail[$i]['name'];
            $arrParam['hidSupplierKey'] = $rsHeader[0]['supplierkey'];
            $arrParam['hidPurchaseKey'] = $rsHeader[0]['pkey'];
            //$arrParam['selCategory'] = $detailRow['categorykey'];
            $arrParam['selAssetGroup'] = $rsDetail[$i]['assetgroupkey'];
            $arrParam['hidBrandKey'] = $rsDetail[$i]['brandkey'];
            $arrParam['hidTypeKey'] = $rsDetail[$i]['typekey'];
            $arrParam['selStatus'] = 1;
            $arrParam['selWarehouse'] = $rsHeader[0]['warehousekey'];
            $arrParam['bookValue'] = $rsDetail[$i]['costinbaseunit'];
            $arrParam['acquisitionValue'] = $rsDetail[$i]['costinbaseunit'];
            $arrParam['selUnit'] = $rsHeader[0]['unitkey'];
            $arrParam['serialNumber'] = $rsDetail[$i]['serialnumber'];
            $arrParam['hidCategoryKey'] = $rsDetail[$i]['categorykey'];
            $arrParam['qoh'] = ($rsHeader[0]['isfullreceive'] == 1) ? 1 : 0; //qoh untuk pengurangan nanti di order penjualan
            $arrParam['acquisitionDate'] = $this->formatDBDate($rsHeader[0]['trdate'], 'd / m / Y');
            $arrParam['selStatus'] = 2;

            $arrParam['createdBy'] = 0;
            //$arrParam['islinked'] = 1;
            $arrayToJs = $assetItem->addData($arrParam);
         
            if (!$arrayToJs[0]['valid'])
               throw new Exception('<strong>' . $rsHeader[0]['code'] . '</strong>. ' . $this->errorMsg[201] . ' ' . $arrayToJs[0]['message']);
            
            $sql = 'update '.$this->tableNameDetail.' set assetitemkey = '.$arrayToJs[0]['data']['pkey'].' where pkey  = '.$this->oDbCon->paramString($rsDetail[$i]['pkey']);
            $assetItemKey =  $arrayToJs[0]['data']['pkey'];
            $this->oDbCon->execute($sql); 

            //if isfullreceive is checked, insert in to assetItemMovement
            if($rsHeader[0]['isfullreceive'] == 1) {
                //update item movement
                $movementNote = $rsHeader[0]['code'] . ', ' . $this->ucFirst($this->lang['assetItemPurchaseOrder'] . ' ' . $this->lang['from']) . ' ' . $rsSupplier[0]['name']; 
                $tablekey = $this->getTableKeyAndObj($this->tableName, array('key'))['key'];
                
           
                
                $assetItemMovement->updateAssetItemMovement($rsHeader[0]['pkey'], $rsHeader[0]['trdate'], '', $arrayToJs[0]['data']['pkey'], $rsHeader[0]['warehousekey'], 1, $tablekey, $movementNote, $rsHeader[0]['supplierkey'],$rsDetail[$i]['costinbaseunit']);

                $arrParam = array();
                $rsObjKey = $this->getTableKeyAndObj($this->tableName);   
                $arrParam['hidRefKey'] = $rsHeader[0]['pkey'];
                $arrParam['refCode'] = $rsHeader[0]['code'];
                $arrParam['trDate'] =   $this->formatDBDate($rsHeader[0]['trdate'],'d / m / Y'); 
                $arrParam['joDate'] =   $arrParam['trDate'] ; // samakan
                $arrParam['selWarehouse'] = $rsHeader[0]['warehousekey'];
                $arrParam['hidRefTable'] = $rsObjKey['key'];
                $arrParam['hidAssetItemKey'] = $assetItemKey;   
                $arrParam['amount'] = $rsDetail[$i]['costinbaseunit'];   
                $arrParam['amount'] *= -1 ; 
                $arrParam['selStatus'] = 1;
                
                $arrDesc = array();
                $itemDesc =  $this->lang['buying']. "  ".$this->formatNumber($rsDetail[$i]['qtyinbaseunit']). " UNIT ". $rsDetail[$i]['name'] ." @ Rp. ". $this->formatNumber($rsDetail[$i]['costinbaseunit']);
                array_push($arrDesc, $itemDesc);
                
                $arrParam['trDesc'] = implode(chr(13),$arrDesc);


                $arrayToJs =  $assetItemTurnover->addData($arrParam); 
                if (!$arrayToJs[0]['valid'])
                    throw new Exception('<strong>'.$rsHeader[0]['code'] . '</strong>. '.$this->errorMsg[201].' '.$arrayToJs[0]['message']); 

            }

      
            //update asset item key saat confirm
            // $sql = 'update '.$this->tableNameDetail.' set assetitemkey = '.$arrayToJs[0]['data']['pkey'].' where pkey  = '.$this->oDbCon->paramString($detailRow['pkey']);
            // $this->oDbCon->execute($sql); 

         }
    }
	 
	 
  function getDetailItemProportional($rsDetail){
                $itemProportional = new ItemProportional();

                $arrItemKey = array_column($rsDetail,'itemkey');
                $rsItemProportionalCol = $itemProportional->searchDataRow(array($itemProportional->tableName.'.pkey',$itemProportional->tableName.'.coakey',$itemProportional->tableName.'.itemkey',$itemProportional->tableName.'.remainpercentage'),' and '.$itemProportional->tableName.'.statuskey = 1 and '.$itemProportional->tableName.'.itemkey in ('.$this->oDbCon->paramString($arrItemKey,',').') ');
                $rsItemProportionalCol = array_column($rsItemProportionalCol,null,'itemkey');

        
                $arrItemPorpotional = array();
                $arrItemNonPorpotional = array();
                $arrItemRemainPercentage = array();
                
                for($i=0;$i<count($rsDetail); $i++){	
                    
                    $qtyinbaseunit = $rsDetail[$i]['qtyinbaseunit'];
                    $costinbaseunit = $rsDetail[$i]['costinbaseunit'];
                    
                    $rsItemProportional = $rsItemProportionalCol[$rsDetail[$i]['itemkey']];
                    
                    
                    if(!empty($rsItemProportional)){
                        
                           $rsDetailPercentage = $itemProportional->getDetailItemPercentage($rsItemProportional['pkey']);

                            $totalPercentage = 0;
                            for($j=0;$j<count($rsDetailPercentage);$j++){
                                
                                $itemPercentage = $rsDetailPercentage[$j]['percentage'];
                                
                                
                                $qtyAfterProportional = ($qtyinbaseunit * $itemPercentage ) / 100;
                                $costinbaseunitAferProportional = ($costinbaseunit * $itemPercentage) / 100 ;
                                
                                $arrItemPercentage[$j] = array();
                                $arrItemPercentage[$j]['iswaste'] = 0 ; //buat ngebedain kalau waste dan sisa dari percentage nya;
                                $arrItemPercentage[$j]['wastecoakey'] = 0; //buat ngebedain kalau waste dan sisa dari percentage nya; 1. kalo sisa. 0 bukan sisa                                
                                $arrItemPercentage[$j]['itemkey'] = $rsDetailPercentage[$j]['itemkey'];
                                $arrItemPercentage[$j]['qtyinbaseunit'] = $qtyAfterProportional;
                                $arrItemPercentage[$j]['costinbaseunit'] = $costinbaseunit; 
 
                                array_push($arrItemPorpotional,$arrItemPercentage[$j]);

                            }
                   
                        if($rsItemProportional['remainpercentage'] > 0){
                            
                                $qtyRemainPercentage = ($qtyinbaseunit * $rsItemProportional['remainpercentage'] ) / 100;
                            
                                $arrRemainPercentage = array();
                                $arrRemainPercentage['iswaste'] = 1; //buat ngebedain kalau waste dan sisa dari percentage nya; 1. kalo sisa. 0 bukan sisa
                                $arrRemainPercentage['wastecoakey'] = $rsItemProportional['coakey']; //buat ngebedain kalau waste dan sisa dari percentage nya; 1. kalo sisa. 0 bukan sisa
                                $arrRemainPercentage['itemkey'] = $rsItemProportional['itemkey'];
                                $arrRemainPercentage['qtyinbaseunit'] = $qtyRemainPercentage;
                                $arrRemainPercentage['costinbaseunit'] = $costinbaseunit;
                            
                                 array_push($arrItemRemainPercentage,$arrRemainPercentage);

                        }
                        
                    }else{
                        
                        $arrItemNonPorpotional[$i] = array();
                        $arrItemNonPorpotional[$i] = $rsDetail[$i];
                        $arrItemNonPorpotional[$i]['iswaste'] = 0; //buat ngebedain kalau waste dan sisa dari percentage nya;
                        $arrItemNonPorpotional[$i]['wastecoakey'] = 0; 
						
//                        $arrItemNonPorpotional[$i]['itemkey'] = $rsDetail[$i]['itemkey'];
//                        $arrItemNonPorpotional[$i]['qtyinbaseunit'] = $qtyinbaseunit;
//                        $arrItemNonPorpotional[$i]['costinbaseunit'] = $costinbaseunit;
                        
                        
                    }
                    
                    

                    
                }
        
           

        $arrDetailItemProportional = array_merge($arrItemPorpotional,$arrItemNonPorpotional,$arrItemRemainPercentage);
       
        return $arrDetailItemProportional;
        
    }
	
    function updateGL($rs,$rsPayment){
        if (!USE_GL) return;
        
        $warehouse = new Warehouse();
        $generalJournal = new GeneralJournal();
        $coaLink = new COALink();
        $supplier = new Supplier();
        $item = new Item();         
        
        $warehousekey = $rs[0]['warehousekey'];
		
		$useCostProportionalOnGL = $this->loadSetting('costProportionalToCOGSonGL');
		
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
         
        $rsDetail = $this->getDetailById($rs[0]['pkey']); // tetep perlu, untuk ambil detailnya dulu
		
		if($this->isActiveModule('ItemProportional'))
        	$rsDetail = $this->getDetailItemProportional($rsDetail);
		
		
        $finalDiscount = ($rs[0]['finaldiscount'] != 0 && $rs[0]['finaldiscounttype'] == 2) ? $rs[0]['finaldiscount']/100 * $rs[0]['subtotal'] : $rs[0]['finaldiscount']; 
      
        $arrItemCOA = array();
        $totalDetailDiscount = 0;
        foreach($rsDetail as $detail){
            
            if($detail['iswaste'] == 1){ 
                //untuk pengakuan sisa ke biaya. apabila kosong default ke other cost
                $itemCOAKey = (!empty($detail['wastecoakey'])) ? $detail['wastecoakey'] : $coaLink->getCOALink ('othercost', $warehouse->tableName,$warehousekey, 0)[0]['coakey'];

            }else{ 
                $itemCOAKey = ($rs[0]['isfullreceive']) ? $item->getInventoryCOAKey($detail['itemkey'],$warehousekey) : $item->getInventoryTempCOAKey($detail['itemkey'],$warehousekey);                
 
                // overwrite kalo tipenya unit
                $useItemGroup = $this->isActiveModule('AssetItem');
                if  ($useItemGroup && $detail['itemgroupkey'] == 2) {
                    $rsCOA = ($rs[0]['isfullreceive']) ? $coaLink->getCOALink('inventoryassetitem', $warehouse->tableName,  $warehousekey) : $coaLink->getCOALink('inventoryassetitemtemp', $warehouse->tableName,  $warehousekey);   
			        $itemCOAKey = $rsCOA[0]['coakey'];
                }   
            }
            
            $discountValue = ($detail['discount'] != 0 && $detail['discounttype'] == 2) ? $detail['discount']/100 * $detail['priceinunit'] : $detail['discount'];
            $detailDiscount =  $discountValue * $detail['qty'] ;
            $totalDetailDiscount += $detailDiscount;
            
            $totalItemValue = $detail['qtyinbaseunit']  * $detail['costinbaseunit'];

            $arrItemCOA[$itemCOAKey] = (!isset($arrItemCOA[$itemCOAKey])) ? $totalItemValue : $arrItemCOA[$itemCOAKey] + $totalItemValue; 
        }
        
        foreach ($arrItemCOA as $coakey => $coaValue){ 
            $temp++;
            $arr['hidCOAKey'][$temp] = $coakey;
            $arr['debit'][$temp] = $coaValue; 
            $arr['credit'][$temp] = 0; 
            $arr['refCashBankKey'][$temp] = '';
        }
         
        $rsCOA = $coaLink->getCOALink ('taxin', $warehouse->tableName,$warehousekey, 0); 
	    $temp++;
		$arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];
		$arr['debit'][$temp] =  $rs[0]['taxvalue']; 
		$arr['credit'][$temp] = 0; 
        $arr['refCashBankKey'][$temp] = '';
         
        // hanya dicatat jika tidak proporsional
		if ($useCostProportionalOnGL <> 1){
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
		}
       
         
        $termOfPayment = new TermOfPayment();
		$rsTOP = $termOfPayment->getDataRowById($rs[0]['termofpaymentkey']); 
		$isCash = ($rsTOP[0]['duedays'] == 0) ? true : false; 
        
        $totalPayment = 0;
        if ($isCash) {
            //$rsPayment = $this->getPaymentMethodDetail($rs[0]['pkey']);  
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
             
			 // hanya dicatat jika tidak proporsional
			if ($useCostProportionalOnGL <> 1){
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
			}

        }else {  
                $temp++;
                $arr['hidCOAKey'][$temp] = $supplier->getAPCOAKey($rs[0]['supplierkey'],$warehousekey);
                $arr['debit'][$temp] = 0; 
                $arr['credit'][$temp] =  $rs[0]['grandtotal']; 
                $arr['refCashBankKey'][$temp] = '';   
        }
        
        // DISKON tidak dicatat, karena sudah bagian dr harga modal
        
/*      $totalDiscount = $totalDetailDiscount + $finalDiscount;
        
        $rsCOA = $coaLink->getCOALink ('purchaseretaildiscount', $warehouse->tableName,$warehousekey, 0); 
	    $temp++;
		$arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];
		$arr['debit'][$temp] = 0; 
		$arr['credit'][$temp] = $totalDiscount; */
         
       
		$arrayToJs = $generalJournal->addData($arr);
         
		if (!$arrayToJs[0]['valid'])
                throw new Exception('<strong>'.$rs[0]['code'] . '</strong>. '.$this->errorMsg[504].' '.$arrayToJs[0]['message']);    
    }
    
 
	function cancelTrans($rsHeader,$copy){  
		
        $prepaidExpense = new PrepaidExpense();   
        
        $tablekey = $this->getTableKeyAndObj($this->tableName,array('key'));  
        
		$id = $rsHeader[0]['pkey']; 
 
		//$cashMovement = new CashMovement();   
		//$cashMovement->cancelMovement($id,$this->tableName);
        
        $useAssetItem = $this->isActiveModule('AssetItem');
        
         if ($rsHeader[0]['isfullreceive']){ 
            
            if(!in_array($rsHeader[0]['reftabletype'],$this->getItemMovementException())){
                $itemMovement = new ItemMovement();  
                $itemMovement->cancelMovement($id,$this->tableName);
                $itemMovement->cancelSNMovement($id,$this->tableName);

                // hapus movement asset unit
                if($useAssetItem){ 
                    $assetItemMovement = new AssetItemMovement();
                    $tablekey = $this->getTableKeyAndObj($this->tableName, array('key'))['key'];
                    $assetItemMovement->cancelAssetItemMovement($id, $tablekey);

                    $assetItemTurnover = new AssetItemTurnover();
                    $assetItemTurnover->cancelMovement($id,$tablekey);
                }
            }
        }


        // delete unit 
        if($useAssetItem){     
            $assetItem = new AssetItem();
            $rsAssetItem = $assetItem->searchDataRow(array($assetItem->tableName . '.pkey'),
                                                        ' and ' . $assetItem->tableName . '.refpurchasekey = ' . $this->oDbCon->paramString($id)
                                                 ); 
            foreach ($rsAssetItem as $assetItemRow) {
                $assetItem->delete($assetItemRow['pkey'], true);
            }
        }
		
		     
		$ap = new AP();
        $rsAPKey = $ap->getTableKeyAndObj($this->tableName,array('key')); 
		$rsAP = $ap->searchData('','',true,' and '.$ap->tableName.'.reftabletype = '.$this->oDbCon->paramString($rsAPKey['key']).' and '.$ap->tableName.'.refkey = '.$this->oDbCon->paramString($id).' and '.$ap->tableName.'.statuskey = 1');
		for($i=0;$i<count($rsAP);$i++) {
            $arrayToJs = $ap->changeStatus($rsAP[$i]['pkey'],4,'',false,true);
            if (!$arrayToJs[0]['valid'])
                throw new Exception('<strong>'.$rsHeader[0]['code'] . '</strong>. '.  $arrayToJs[0]['message']);    
        }	
           
		$purchaseReceive = new PurchaseReceive();
		$rsPurchaseReceive = $purchaseReceive->searchData('','',true,' and '.$purchaseReceive->tableName.'.refkey = '.$this->oDbCon->paramString($id).' and '.$purchaseReceive->tableName.'.statuskey = 1');
		for($i=0;$i<count($rsPurchaseReceive);$i++) {
			$arrayToJs = $purchaseReceive->changeStatus($rsPurchaseReceive[$i]['pkey'],4,'',false,true);
            if (!$arrayToJs[0]['valid'])
                throw new Exception('<strong>'.$rsHeader[0]['code'] . '</strong>. '.  $arrayToJs[0]['message']);    
        }
         
        $cashBank = new CashBank();
        $cashBank->cancelCashBank($rsHeader,$this->tableName);
        

        $rsCostReconsile = $prepaidExpense->searchDataRow( array( $prepaidExpense->tableName.'.pkey', $prepaidExpense->tableName.'.code'  ) , 
                                '  and '.$prepaidExpense->tableName.'.reftabletype = '.$tablekey['key'].' 
                                   and '.$prepaidExpense->tableName.'.refkey = '.$this->oDbCon->paramString($rsHeader[0]['pkey']).' and '.$prepaidExpense->tableName.'.statuskey = 1'  
                       );
    
        $totalCostReconsile = count($rsCostReconsile);
        for($i=0;$i<$totalCostReconsile;$i++)  
            $prepaidExpense->changeStatus($rsCostReconsile[$i]['pkey'],4,'',false, true);  
        
        
		if ($copy)
			$this->copyDataOnCancel($id);	  
	 
        $this->cancelGLByRefkey($rsHeader[0]['pkey'],$this->tableName);
	} 
 
    function validateCancel($rsHeader,$autoChangeStatus=false){ 
        
        $prepaidExpense = new PrepaidExpense();
        $tablekey = $this->getTableKeyAndObj($this->tableName,array('key'));    
        
		$id = $rsHeader[0]['pkey'];
  
		//cek apakah sudah ad penerimaan PO
        if (!$rsHeader[0]['isfullreceive']) {
            $purchaseReceive = new PurchaseReceive();
            $rsPurchaseReceive = $purchaseReceive->searchData('','',true,' and '.$purchaseReceive->tableName.'.refkey = '.$this->oDbCon->paramString($id).' and ('.$purchaseReceive->tableName.'.statuskey in (2,3))');
            
            if (!empty($rsPurchaseReceive))
			     $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' . $this->errorMsg[201].' ' .$this->errorMsg['purchaseOrder'][2]);
        }
         
		//cek ad AP terbayar
		$ap = new AP(); 
        $rsAPKey = $ap->getTableKeyAndObj($this->tableName,array('key'));  
		$rsAP = $ap->searchData('','',true,' and '.$ap->tableName.'.refkey = '.$this->oDbCon->paramString($id).' and '.$ap->tableName.'.reftabletype = '.$rsAPKey['key'].' and ('.$ap->tableName.'.statuskey in (2,3))');
		
		if(!empty($rsAP))  
			$this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' . $this->errorMsg[201].' ' .$this->errorMsg['ap'][2]);

		 
        //$this->setLog("validasi sudha ad amortisasi blm",true);
        //$this->setLog("kalo cancel, harus cancel jg prepaid expense",true);
        
        $rsCostReconsile = $prepaidExpense->searchDataRow( array(  $prepaidExpense->tableName.'.pkey', $prepaidExpense->tableName.'.code'  ) , 
                   ' and  '.$prepaidExpense->tableName.'.refkey = '.$this->oDbCon->paramString($id).' and '.$prepaidExpense->tableName.'.reftabletype = '.$tablekey['key'].' and ('.$prepaidExpense->tableName.'.statuskey in(2,3))'  
                    );

        if(!empty($rsCostReconsile))  
            $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' . $this->errorMsg[201].' ' .$this->errorMsg['prepaidExpense'][2]);

	 }
	 
	function getDetailWithRelatedInformation($pkey,$criteria = ''){
        
       if ($this->isActiveModule('AssetItem'))
           return $this->getDetailWithRelatedInformationForAssetItem($pkey,$criteria);
        
	   $sql = 'select
	   			'.$this->tableNameDetail.'.*, 
                '.$this->tableItem.'.name as itemname, 
                '.$this->tableItem.'.code as itemcode,
                '.$this->tableItem.'.deftransunitkey,
                '.$this->tableItem.'.isweightfixed,
                '.$this->tableItem.'.needsn,
                '.$this->tableItemUnit.'.code as unitcode,
                '.$this->tableItemUnit.'.name as unitname,
                 baseunit.name as baseunitname
			  from
			  	'.$this->tableNameDetail.',
                '.$this->tableItem.',
                '.$this->tableItemUnit.',
                '.$this->tableItemUnit.' baseunit
			  where
			  	'.$this->tableNameDetail.'.itemkey = '.$this->tableItem.'.pkey and
			  	'.$this->tableNameDetail.'.unitkey = '.$this->tableItemUnit.'.pkey and
			  	'.$this->tableItem.'.baseunitkey = baseunit.pkey and
                '.$this->tableNameDetail .'.refkey in ('.$this->oDbCon->paramString($pkey,',') . ') ';
        
        $sql .= $criteria;
              
		return $this->oDbCon->doQuery($sql);
	
   }

   function getDetailWithRelatedInformationForAssetItem($pkey,$criteria = ''){
         
        // gk bisa pake concat WS karena kode asset harus joib ke table aasset

        // biar gk kebykan query
        $baseSql = 'select
                   '.$this->tableNameDetail.'.*, 
                '. $this->tableBrand .'.name as brandname,
                '. $this->tableCarSeries .'.name as typename,
                '.$this->tableItemUnit.'.name as unitname,
                '.$this->tableItemGroup.'.name as groupname,
                '. $this->tableCategoryAssetItem .'.name as assetcategoryname,';

        $baseCriteria = '   left join '. $this->tableBrand .' on '.$this->tableNameDetail.'.brandkey = '. $this->tableBrand .'.pkey
                            left join '. $this->tableCarSeries .' on '.$this->tableNameDetail.'.typekey = '. $this->tableCarSeries .'.pkey
                            left join '. $this->tableItemUnit .' on '. $this->tableNameDetail .'.unitkey = '. $this->tableItemUnit .'.pkey               
                            left join ' . $this->tableCategoryAssetItem . ' on '. $this->tableNameDetail .'.categorykey = '. $this->tableCategoryAssetItem .'.pkey
                            left join '. $this->tableItemGroup .' on '. $this->tableNameDetail .'.itemgroupkey = '. $this->tableItemGroup .'.pkey ';



        $sql = $baseSql.                
                $this->tableItem.'.name as itemname, 
                '.$this->tableItem.'.code as itemcode,
                CONCAT('. $this->tableItem .'.code, " - ", '. $this->tableItem .'.name) as itemcodename,
                '.$this->tableItem.'.deftransunitkey,
                baseunit.name as baseunitname
            from
                  '.$this->tableNameDetail.'
                '.$baseCriteria.'  
                    left join '. $this->tableItem .' on '. $this->tableNameDetail .'.itemkey = '.$this->tableItem.'.pkey
                    left join '. $this->tableItemUnit .' baseunit on '. $this->tableItem .'.baseunitkey = baseunit.pkey 

            where
                '.$this->tableNameDetail .'.refkey in ('.$this->oDbCon->paramString($pkey,',') . ') and
                '. $this->tableNameDetail .'.itemgroupkey = 1
            ';

        $sql .= $criteria;

        $sql .= ' union all ';

        $sql .= $baseSql. 
                $this->tableNameDetail .'.serialnumber as itemcode,'.
                $this->tableNameDetail .'.name as itemname,
                CONCAT(item.code, " - ", item.name) as itemcodename,
                null as deftransunitkey,
                null as baseunitname
            from
                  '.$this->tableNameDetail.'
                '.$baseCriteria.' 
                    left join '. $this->tableAssetItem .' item on '. $this->tableNameDetail .'.assetitemkey = item.pkey

            where
                '.$this->tableNameDetail .'.refkey in ('.$this->oDbCon->paramString($pkey,',') . ') and
                '. $this->tableNameDetail .'.itemgroupkey = 2
            ';

        $sql .= $criteria;

        $result = $this->oDbCon->doQuery($sql);

        return $result;

    }
    
    function getSerialNumber($refkey){
        if(!is_array($refkey))
            $refkey = array($refkey);
        
        $sql = 'select * from '.$this->tableDetailSerial.' where refkey in ('.$this->oDbCon->paramString($refkey,',').')'; 
        return $this->oDbCon->doQuery($sql);
    }
    
    function searchDataForAutoComplete($fieldname='',$searchkey='',$mustmatch=false,$searchCriteria='',$orderCriteria='', $limit=''){
		$sql = 'select
					'.$this->tableName. '.pkey,  concat('.$this->tableName. '.code,\' - \', '.$this->tableSupplier.'.name) as value
				from 
					'.$this->tableName . ','.$this->tableSupplier.','.$this->tableStatus.'
				where  		
					'.$this->tableName . '.supplierkey = '.$this->tableSupplier.'.pkey and
					'.$this->tableName . '.statuskey = '.$this->tableStatus.'.pkey 
			';
	
		if(!empty($fieldname)){
			
			$sql .= ' and ' ;
			
			if($mustmatch)
				$sql .=  $fieldname .' = '. $this->oDbCon->paramString($searchkey);
			else
				$sql .=  $fieldname .' like '. $this->oDbCon->paramString('%'.$searchkey.'%');
		}
				
		if($searchCriteria <> '')
			$sql .= ' ' .$searchCriteria;
	
		if($orderCriteria <> ''){
			$sql .= ' ' .$orderCriteria;
	 
	 	}
			
		if($limit <> '')
			$sql .= ' ' .$limit;
		    
         
		return $this->oDbCon->doQuery($sql);	
	} 
    
    function normalizeParameter($arrParam, $trim = false){ 
            $termOfPayment = new TermOfPayment();
            $item = new Item();
 
            //$isFullReceive = 1; 
            $arrParam['refCode'] = (isset($arrParam['refCode'])) ? $arrParam['refCode'] : '';

            $trDate = $arrParam['trDate'];
            $supplierkey = $arrParam['hidSupplierKey'];

            $arrItemkey = $arrParam['hidItemKey']; 
            $arrPriceinunit = $arrParam['priceInUnit']; 
            $arrDiscountValueInUnit = $arrParam['discountValueInUnit']; 
            $arrDiscountType = $arrParam['selDiscountType'];  


            //overwrite master price
            if($this->activeModule['purchasepricing']) {
                $purchasePrice = new PurchasePricing();
 
                    $rsPurchasePrice = $purchasePrice->getLatestPurchasePricing($supplierkey, $arrItemkey,$trDate);
                    $rsPurchasePrice = $this->reindexDetailCollections($rsPurchasePrice,'itemkey');

                    for($i=0; $i<count($arrItemkey); $i++) { 
                        if(!isset($rsPurchasePrice[$arrItemkey[$i]])) continue; 
                        $rsPrice = $rsPurchasePrice[$arrItemkey[$i]]; 
                        $arrParam['priceInUnit'][$i] = $rsPrice[0]['price'];
                    } 
            }

            $rsTOP = $termOfPayment->getDataRowById($arrParam['selTermOfPaymentKey']);  
            if ($rsTOP[0]['duedays'] != 0){   
                for($i=0;$i<count( $arrParam['paymentMethodValue']);$i++){ 
                    $arrParam['paymentMethodValue'][$i] = 0; 
                    $arrParam['hidDetailPaymentKey'][$i] = 0;
                }
            }
        
            // init kalo qtyinpcs atau priceinpc kosong, diisi seusai qty dan priceinunit
            // karena akan digunakan dalam perhitungan
            if(in_array(PLAN_TYPE['categorykey'], array(COMPANY_TYPE['jewelry']))){    
                 for ($i=0;$i<count($arrItemkey);$i++){ 
                        if(!isset($arrParam['qtyInPcs']) || empty($arrParam['qtyInPcs'][$i])) $arrParam['qtyInPcs'][$i] = $arrParam['qty'][$i];
                        if(!isset($arrParam['priceInPcs']) || empty($arrParam['priceInPcs'][$i])) $arrParam['priceInPcs'][$i] = $arrParam['priceInUnit'][$i];
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

                $arrParam['qtyInPcs'][$i] = $arrParam['detailCOGS'][$i]['qtyInPcs']; 
                $arrParam['priceInPcs'][$i] = $arrParam['detailCOGS'][$i]['priceInPcs'];
                $arrParam['priceInUnit'][$i] = $arrParam['detailCOGS'][$i]['priceInUnit'];				
                    
                // set default jadi 0 lg, utk handle copy on cancel
                $arrParam['receivedQtyInBaseUnit'][$i] = 0;
                $arrParam['receivedQtyInPcs'][$i] = 0;
                  
            }
  
        
            if( $arrParam['chkIsFullReceive'] == 0){
                
                for($i=0;$i<count($arrParam['snList']);$i++){
                    $arrParam['snList'][$i] = ''; 
                    $arrParam['hidDetailSNKey'][$i] = 0;
                    $arrParam['serialNumberDetail'][$i] = '';
                    $arrParam['COGSSN'][$i] = 0;
                } 
                
            }else{
                    // ========= update SN
                    $arrParam['hidDetailSNKey'] = array();
                    $arrParam['serialNumberDetail'] = array();
                    $arrParam['hidDetailSNKeyTotalRows'] = array('1' => array()); 
 
                    $snCtr = 0;
                    for($i=0;$i<count($arrParam['hidItemKey']);$i++){
 
                        $snList = (isset($arrParam['snList'][$i]) && !empty($arrParam['snList'][$i])) ?  trim($arrParam['snList'][$i]): '';
                        
                        $arrSerialNumber = (!empty($snList)) ?  preg_split($this->SN_SPLIT_REGEX, $snList) : [];
                         
                        $arrParam['hidDetailSNKeyTotalRows'][1][$i] = count($arrSerialNumber); 
                        $cogsDetail = $arrParam['cogs'][$i]; 
                        foreach($arrSerialNumber as $snRow){  
                            $arrParam['hidDetailSNKey'][$snCtr] = 0;
                            $arrParam['serialNumberDetail'][$snCtr] = $snRow;
                            $arrParam['COGSSN'][$snCtr] = $cogsDetail;
                            $snCtr++;
                        } 
                        
                    }  
                    // ========= update SN
                
            }

            $details = array();
            array_push($details,$this->arrDataDetailSN);
            $arrParam = $this->prepareMultiLevelDetail($arrParam,$details);
        
        
            $arrParam = parent::normalizeParameter($arrParam); 

            return $arrParam;
    }
    
     function updatePurchaseOrderReceivedItem($pkey){ 
            $purchaseReceive = new PurchaseReceive(); 
            $rsHeader = $this->getDataRowById($pkey);  
            $rsDetail = $this->getDetailById($pkey); 

            for($i=0;$i<count($rsDetail); $i++){	
                $sql = 'select 
                        coalesce(sum(receivedqtyinbaseunit),0) as totalreceivedqtyinbaseunit,
                        coalesce(sum(receivedqtyinpcs),0) as totalreceivedqtyinpcs
                    from 
                        '. $purchaseReceive->tableName . ', '. $purchaseReceive->tableNameDetail . '
                    where 
                         '. $purchaseReceive->tableName . '.pkey = '. $purchaseReceive->tableNameDetail . '.refkey and
                         '. $purchaseReceive->tableName . '.refkey = '. $this->oDbCon->paramString($pkey) .' and
                         '. $purchaseReceive->tableNameDetail . '.itemkey = ' . $rsDetail[$i]['itemkey'] .' and 
                         '. $purchaseReceive->tableNameDetail . '.refpodetailkey = ' . $rsDetail[$i]['pkey'] .' and 
                         (statuskey = 2 or statuskey = 3)';
 
                $rsTotal = $this->oDbCon->doQuery($sql);

                // INI AKAN PROBLEM KALO DETAIL PUNYA 2 ITEM YG SAMA
                $sql = 'update 
                            ' . $this->tableNameDetail.' 
                        set  
                            receivedqtyinbaseunit = '. $rsTotal[0]['totalreceivedqtyinbaseunit'] .',
                            receivedqtyinpcs = '. $rsTotal[0]['totalreceivedqtyinpcs'] .'
                        where 
                            refkey = '.$pkey.' and 
                            pkey = '.$rsDetail[$i]['pkey'].' and 
                            itemkey = ' . $rsDetail[$i]['itemkey'];
                 
                $this->oDbCon->execute($sql); 
            }

            //check if all item received, change PO status to finish
            $sql = 'select * from ' . $this->tableNameDetail.' where refkey = '.$this->oDbCon->paramString($pkey).' and  receivedqtyinbaseunit < qtyinbaseunit';
            $rs = $this->oDbCon->doQuery($sql);

            $statuskey = (empty($rs)) ? 3 : 2; 
              
            if ($rsHeader[0]['statuskey'] <> $statuskey)
                $this->changeStatus($pkey,$statuskey,'',false,true);
      
    }
    
     function getPriceItem($itemkey,$supplierkey =''){
        
        $sql = 'select 
                    '.$this->tableNameDetail.'.*
                from
                    '.$this->tableNameDetail.',
                    '.$this->tableName.'
                where 
                    '.$this->tableName.'.statuskey in(2,3) and
                    '.$this->tableNameDetail.'.refkey = '.$this->tableName.'.pkey and
                    '.$this->tableNameDetail .'.itemkey = '.$this->oDbCon->paramString($itemkey);
                
        if(!empty($supplierkey))
            $sql .= ' and '.$this->tableName.'.supplierkey = '.$this->oDbCon->paramString($supplierkey);
        
        
        $sql .= ' order by '.$this->tableName.'.trdate desc ';
        $sql .= ' limit 1 ';
        
        $rs = $this->oDbCon->doQuery($sql); 

        return $rs;    
        
    }
    
    
    function getRelatedDataForCashBankReport($pkey){
        $arrReturn = array();
        
        $sql = 'select 
                    '. $this->tableName.'.pkey, 
                    '. $this->tableName.'.code as refcode,
                    '.$this->tableWarehouse.'.name as warehousename,
                    '.$this->tableSupplier.'.name as suppliername
                from 
                    '. $this->tableName.' 
                        left join '.$this->tableSupplier.' on '. $this->tableName.'.supplierkey = '.$this->tableSupplier.'.pkey,
                    '.$this->tableWarehouse.'
                where 
                    '. $this->tableName.'.pkey in ('.$this->oDbCon->paramString($pkey,',').') and 
                    '. $this->tableName.'.warehousekey = '. $this->tableWarehouse.'.pkey';
        
        
        $rs = $this->oDbCon->doQuery($sql); 
        $rs = array_column($rs, null,'pkey');
          
        return $rs;
    }
    
    function getDetailByAP($apkey){
        $typeKey = $this->getTableKeyAndObj($this->tableName,array('key'))['key']; 
        
        $sql = 'select   
                    '.$this->tableItem.'.name as itemname,
                    '.$this->tableNameDetail.'.priceinunit,
                    '.$this->tableNameDetail.'.total,
                    '.$this->tableNameDetail.'.qty,
                    '.$this->tableItemUnit.'.name as unitname
                from
                    '.$this->tableAP.','.$this->tableNameDetail.', '.$this->tableItem.','.$this->tableItemUnit.'
                where 
                    '.$this->tableAP.'.pkey in ('.$this->oDbCon->paramString($apkey,',').') and
                    '.$this->tableAP.'.reftabletype = '.$this->oDbCon->paramString($typeKey).' and
                    '.$this->tableAP.'.refkey = '.$this->tableNameDetail.'.refkey  and
                    '.$this->tableNameDetail.'.itemkey = '.$this->tableItem.'.pkey  and
                    '.$this->tableNameDetail.'.unitkey = '.$this->tableItemUnit.'.pkey  
                ';
        
        return $this->oDbCon->doQuery($sql); 
    }
    
        
    function addAmortization($rsHeader,$rsDetail){

        $prepaidExpense = new PrepaidExpense();
        $item = new Item();
          
        $tablekey = $this->getTableKeyAndObj($this->tableName,array('key'))['key'];
        $warehousekey =  $rsHeader[0]['warehousekey'];
        $rate = 1;
        
        $rsItemCol = $item->searchDataRow(array($item->tableName.'.pkey',$item->tableName.'.code',$item->tableName.'.isamortized',$item->tableName.'.amortizationaging'),
                                          ' and '.$item->tableName.'.pkey in ('. $this->oDbCon->paramString(array_column($rsDetail,'itemkey'),',').')'
                                         );

        $rsItemCol = array_column($rsItemCol,null,'pkey');
        
        for($i=0;$i<count($rsDetail);$i++){

            $rsItem = $rsItemCol[$rsDetail[$i]['itemkey']];
            $rsItem['amortizationaging'] = ($rsItem['amortizationaging'] <= 0) ? 1 : $rsItem['amortizationaging'];
            
            if($rsItem['isamortized'] == 0) continue;

            $arrParam = array();	
            $arrParam['code'] = 'xxxxx'; 
            $arrParam['hidRefKey'] = $rsHeader[0]['pkey'];
            $arrParam['hidJobOrderKey'] = 0; 
            $arrParam['selWarehouseKey'] = $warehousekey; 
            $arrParam['hidRefCode'] = $rsHeader[0]['code'];
            $arrParam['hidJobOrderHeaderKey'] = 0;
            $arrParam['refsalesordertabletype'] = 0;
            $arrParam['reftabletype'] = $tablekey;
            $arrParam['trDate'] = $this->formatDBDate($rsHeader[0]['trdate'],'d / m / Y');
            $arrParam['hidCostKey'] = $rsDetail[$i]['itemkey']; 
            $arrParam['currencyRate'] = $rate;
            $arrParam['selCurrency'] = CURRENCY['idr'];
            $arrParam['amount'] = $rsDetail[$i]['total'];
            $arrParam['amountIDR'] =  $rsDetail[$i]['total'] * $rate;
            $arrParam['overwriteGL'] = 1;
            $arrParam['outstanding'] = $rsDetail[$i]['total'];
            $arrParam['islinked'] = 1;  
            $arrParam['type'] = 2;//dari purchase order type 2
            $arrParam['amortizationAging'] = $rsItem['amortizationaging'];  
            $arrParam['amortizationValue'] =  $rsDetail[$i]['total'] / $rsItem['amortizationaging'];
            
            $arrayToJs = $prepaidExpense->addData($arrParam);  
            
            if (!$arrayToJs[0]['valid']){
                throw new Exception('<strong>'.$rsHeader[0]['code'] . '</strong>. '.$this->errorMsg[201].' '.$arrayToJs[0]['message']);  
            }

        }

    }


    function updateInvoiceReference($arrData)
    {
        $security = new Security();
        if (!$security->isAdminLogin($this->updatePurchaseOrderInvoiceReferenceSecurityObject, 10))  die;
        
        $result = [];        
        
        try {
			
			if(!$this->oDbCon->startTrans())
				throw new Exception($this->errorMsg[100]);

                $pkey = $arrData['pkey'];
                $refInvoiceCode = $arrData['refInvoiceCode'];

                //cek data
                $rsPO = $this->getDataRowById($pkey);
                if(in_array($rsPO[0]['statuskey'],array(4))){ 
					array_push($result, [ 'valid' => false, 'message' => $this->errorMsg[212] ]);
				} 
            
                // gk perlu, bisa saja utk hapus kode ref
//                if(empty($refInvoiceCode)){ 
//                    array_push($result, ['valid' => false,'message' =>  $rsPO[0]['code'] . '. ' . $this->lang['invoiceReference'] . ' Data harus diisi']);
//				}

                if(empty($result)) {

                    $sql = '
                        UPDATE
                            '. $this->tableName .'
                        SET
                            '.$this->tableName.'.refinvoicecode = '. $this->oDbCon->paramString($refInvoiceCode) .'
                        where
                            '.$this->tableName.'.pkey = '. $this->oDbCon->paramString($pkey) .'
                    ';

                    $rs = $this->oDbCon->execute($sql);
 
                    $this->setTransactionLog(UPDATE_DATA,$rsPO[0]['pkey']);
                    array_push($result, ['valid' => true,'message' => $rsPO[0]['code'] .' - '.$this->lang['dataHasBeenSuccessfullyUpdated']]);
                   
                }

                $this->oDbCon->endTrans();

        } catch (Exception $e) {
            $this->oDbCon->rollback();
            array_push($result, [  'valid' => false,  'message' => $e->getMessage()]);
        }
    
        return $result;

    }
    
    function getDetailJobOrder(){
        // utk shadow func, dari DN, karena cek getDetailJobOrder di Trucking/Forwarding PO
    }
    
    
    function getDetailForAPI($arrKey, $arrIndex = array()){
		$rsDetailsCol = array();
		
        if(in_array('detail', $arrIndex)){   
			//$rsDetailsCol = array();  // ini kalo ad didalam jadinya error kalo ad 2 detail atau lebih, karena kereset lg yg sebelumnya
            $rsDetails = $this->getDetailWithRelatedInformation($arrKey); 
            $arrDetailKey = array_column($rsDetails, 'pkey');
            $rsDetailSN = $this->getSerialNumber($arrDetailKey); 
            $rsDetailSN = $this->reindexDetailCollections($rsDetailSN,'refkey'); 
            for($i=0;$i<count($rsDetails);$i++) {
                $detailkey = $rsDetails[$i]['pkey'];
                $rsDetails[$i]['detail_sn'] = array();
                if (!isset($rsDetailSN[$detailkey]))  continue;
                $arrDetailSN = $rsDetailSN[$detailkey];
                for($j=0;$j<count($arrDetailSN);$j++) { 
                    $arrTemp = array();
                    $arrTemp['serial_number'] = $arrDetailSN[$j]['serialnumber'];
                    $arrTemp['cogs'] = $arrDetailSN[$j]['costinbaseunit'];
                    array_push($rsDetails[$i]['detail_sn'], $arrTemp);
                }
            }
            $rsDetails = $this->reindexDetailCollections($rsDetails,'refkey'); 
            $rsDetailsCol['detail'] = $rsDetails;
        }
        
	 	if(in_array('payment_method_detail', $arrIndex)){  
            $rsDetails = $this->getPaymentMethodDetail($arrKey); 
            $rsDetails = $this->reindexDetailCollections($rsDetails,'refkey');
            $rsDetailsCol['payment_method_detail'] = $rsDetails;
        }
          
        return $rsDetailsCol;
    }
    
 
}
?>