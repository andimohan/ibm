<?php
  
class SalesOrderCarService extends BaseClass{ 
  
   function __construct(){
		
		parent::__construct();
       
		$this->tableName = 'sales_order_car_service_header';
		$this->tableNameDetail = 'sales_order_car_service_detail';
		$this->tableCustomer = 'customer';
		$this->tableCity = 'city';
		$this->tableCar = 'car';
		$this->tableEmployee = 'employee';
		$this->tableWarehouse = 'warehouse'; 
		$this->tableStatus = 'transaction_status';
		$this->tableMovement = 'item_movement'; 
		$this->tableHistory = 'history';
		$this->tablePayment= 'sales_order_payment'; 	
		$this->tableItem = 'item'; 	
		$this->tableItemUnit = 'item_unit'; 	
		$this->tableBrand = 'brand'; 	
		$this->tableItemCategory = 'item_category'; 	
        $this->tablePackageDetail = 'sales_order_package_detail';
		$this->tableCartTemp = 'cart_temp'; 	
        $this->isTransaction = true; 

		  
       
        $this->arrDataDetail = array();  
        $this->arrDataDetail['pkey'] = array('hidDetailKey');
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
        $this->arrDataDetail['profit'] = array('detailProfit','number'); 
        $this->arrDataDetail['saleskey'] = array('hidDetailSalesKey');
        $this->arrDataDetail['warehousekey'] = array('hidDetailWarehouseKey');
        $this->arrDataDetail['movementtype'] = array('selMovementType');
        $this->arrDataDetail['itemtype'] = array('itemType');
        $this->arrDataDetail['ispackage'] = array('isPackage');
        $this->arrDataDetail['ispackage'] = array('isPackage');
        $this->arrDataDetail['alias'] = array('aliasName'); 
        $this->arrDataDetail['istax23'] = array('chkIsTax23'); 
        $this->arrDataDetail['description'] = array('detailNote'); 

           
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
        $this->arrData['trdate'] = array('trDate','date');
        $this->arrData['trdateout'] = array('trDateOut','date');
        $this->arrData['warehousekey'] = array('selWarehouseKey');
        $this->arrData['customerkey'] = array('hidCustomerKey');
        $this->arrData['termofpaymentkey'] = array('selTermOfPaymentKey');
        $this->arrData['trnotes'] = array('trDesc');
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
        $this->arrData['isfulldeliver'] = array('chkIsFullDeliver');
        $this->arrData['profit'] = array('profit','number');
        $this->arrData['recipientname'] = array('recipientName');
        $this->arrData['recipientphone'] = array('recipientPhone');
        $this->arrData['recipientemail'] = array('recipientEmail');
        $this->arrData['recipientaddress'] = array('recipientAddress');
        $this->arrData['pointvalue'] = array('pointValue','number');
        $this->arrData['useinsurance'] = array('useInsurance'); 
        $this->arrData['shipmentkey'] = array('selShipment');
        $this->arrData['mileage'] = array('mileage','number');
        $this->arrData['carkey'] = array('hidCarKey');
        $this->arrData['techniciankey'] = array('hidTechicianKey'); 
        $this->arrData['technician2key'] = array('hidTechician2Key'); 
        $this->arrData['balance'] = array('balance');
        $this->arrData['vehicleid'] = array('vehicleid');
        $this->arrData['statuskey'] = array('selStatus'); 
        $this->arrData['tax23percentage'] = array('tax23Percentage','number');    
        $this->arrData['tax23value'] = array('tax23Value','number');  
                                
		$this->tableNeedToBeCopyOnCancel = array($this->tableNameDetail, $this->tablePackageDetail, $this->tablePayment); 
		$this->securityObject = 'SalesOrderCarService';  
 
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'date','title' => 'date','dbfield' => 'trdate','default'=>true, 'width' => 100, 'align'=>'center', 'format' => 'date'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'policenumber','title' => 'carRegistrationNumber','dbfield' => 'policenumber','default'=>true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'customer','title' => 'customer','dbfield' => 'customername','default'=>true, 'width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'technician','title' => 'technician','dbfield' => 'technicianname','default'=>true, 'width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'total','title' => 'total','dbfield' => 'grandtotal','default'=>true, 'width' => 100, 'align' => 'right', 'format'=>'number'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
        
        $this->printMenu = array();  
        array_push($this->printMenu,array('code' => 'printInvoice', 'name' => $this->lang['print'] . ' ' .$this->lang['invoice'],  'icon' => 'print', 'url' => 'print/salesOrderCarService'));
        array_push($this->printMenu,array('code' => 'printCarServiceBuying', 'name' => $this->lang['print'] . ' ' .$this->lang['buying'],  'icon' => 'print', 'url' => 'print/salesOrderCarServiceBuying'));
        
       
        $this->includeClassDependencies(array(
                   'TermOfPayment.class.php', 
                   'Warehouse.class.php',  
                   'SalesCarServiceReturn.class.php',  
                   'PaymentMethod.class.php', 
                   'City.class.php', 
                   'Customer.class.php', 
                   'Item.class.php', 
                   'Car.class.php', 
                   'Shipment.class.php', 
                   'RewardsPoint.class.php', 
                   'PurchaseOrder.class.php', 
                   'COALink.class.php',
                   'ItemMovement.class.php',
                   'CashMovement.class.php',
                   'AR.class.php',
                   'ARPayment.class.php',
                   'ChartOfAccount.class.php',
                   'GeneralJournal.class.php',
                   'ItemUnit.class.php',
                   'Brand.class.php',
                   'Category.class.php',
                   'ItemCategory.class.php',
                   'ItemCondition.class.php',
                   'ItemPackage.class.php'
            ));       
   }
   
    function getQuery(){
	   
	   $sql =  '
			SELECT '.$this->tableName.'.* ,
			   '.$this->tableCustomer.'.name as customername,
               '.$this->tableEmployee.'.name as technicianname,
			   '.$this->tableWarehouse.'.name as warehousename,
			   '.$this->tableStatus.'.status as statusname ,
			   '.$this->tableCar.'.policenumber
			FROM 
                '.$this->tableStatus.', 
                '.$this->tableCustomer.' 
                    left join '.$this->tableCity.' on  '.$this->tableCustomer.'.citykey = '.$this->tableCity.'.pkey,
                '.$this->tableWarehouse.',
                '.$this->tableName.'
                    left join '.$this->tableEmployee.' on  '.$this->tableName.'.techniciankey = '.$this->tableEmployee.'.pkey
					left join '.$this->tableCar.' on '.$this->tableName.'.carkey = '.$this->tableCar.'.pkey
			WHERE '.$this->tableName.'.customerkey = '.$this->tableCustomer.'.pkey and
					 '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey and
					 '.$this->tableName.'.warehousekey = '.$this->tableWarehouse.'.pkey 
 		' .$this->criteria ; 
		 
       $sql .= $this->getCompanyCriteria()	; 
       
       return $sql;
    }  
    
    
	
    function afterUpdateData($arrParam, $action){  
        $this->updatePackage($arrParam);  
    }
    
    function addData($arrParam){ 
        if (isset($arrParam['hidSendEmail']) && !empty($arrParam['hidSendEmail'])){
            $this->sendInvoice($pkey);
        }  
		return parent::addData($arrParam);   
	} 
        
    function editData($arrParam){ 
        // hitung ulang subtotal   
        if (isset($arrParam['hidSendEmail']) && !empty($arrParam['hidSendEmail'])){
            $this->sendInvoice($pkey);
        }       
		return parent::editData($arrParam);  
	}
     
     function afterStatusChanged($rsHeader){ 
        // retrieve latest status
        $rsHeader = $this->getDataRowById($rsHeader[0]['pkey']);
        if ($rsHeader[0]['isfulldeliver'] == 1 && $rsHeader[0]['statuskey'] == 2)
            $this->changeStatus($rsHeader[0]['pkey'],3); 
    }
    
    function sendInvoice($pkey){
          		
		try{  
				if(!$this->oDbCon->startTrans())
					throw new Exception($this->errorMsg[100]);
            
                    $arrayToJs = array(); 
        
                    $rs = $this->getDataRowById($pkey);
                    if(empty($rs)){ 
                        $this->addErrorList($arrayToJs,false,$this->errorMsg['901']);  
                        return $arrayToJs;
                    } 
                    
					$invoice =  $this->generateInvoice($pkey); 
					 
					$this->sendMail(array(),$this->lang['invoice'] . ' '. $rs[0]['code'],$invoice,array('email'=>$rs[0]['recipientemail']));
                    
                    $invoiceArchiveEmail = $this->loadSetting('invoiceArchiveEmail');
                    if (!empty($invoiceArchiveEmail))
					   $this->sendMail(array(),$this->lang['invoice'] . ' '.  $rs[0]['code'],$email,array('email'=>$invoiceArchiveEmail));
					 
					$sql = 'update ' .$this->tableName.' set invoicesent = invoicesent + 1 where pkey = ' . $pkey ;
                 	$this->oDbCon->execute($sql); 
        
                    $this->oDbCon->endTrans();
            
                    $this->addErrorList($arrayToJs,true,$this->lang['dataHasBeenSuccessfullyUpdated']);   
            
        }catch (Exception $e){
            $this->oDbCon->rollback();
			$this->addErrorList($arrayToJs,false,$e->getMessage());    
        }
		      
        return $arrayToJs; 
        
    }
     
    function sendNotificationInvoice($pkey){
        // untuk email ke admin saja, bukan ke user
          		
		try{  
				if(!$this->oDbCon->startTrans())
					throw new Exception($this->errorMsg[100]);
            
                    $arrayToJs = array(); 
        
                    $rs = $this->getDataRowById($pkey);
                    if(empty($rs)){ 
                        $this->addErrorList($arrayToJs,false,$this->errorMsg['901']);  
                        return $arrayToJs;
                    } 
                    
					$invoice =  $this->generateInvoice($pkey);
					  
                    $invoiceArchiveEmail = $this->loadSetting('invoiceArchiveEmail');
                    $this->sendMail(array(),$this->lang['invoice'] . ' '.  $rs[0]['code'],$invoice,array('email'=>$invoiceArchiveEmail));
		 
                    $this->oDbCon->endTrans();
            
                    $this->addErrorList($arrayToJs,true,$this->lang['dataHasBeenSuccessfullyUpdated']);   
            
        }catch (Exception $e){
            $this->oDbCon->rollback();
			$this->addErrorList($arrayToJs,false,$e->getMessage());    
        }
		      
        return $arrayToJs; 
        
    } 
	
	function reCountSubtotal($arrParam){

                $isPriceIncludeTax = (!empty($arrParam['chkIncludeTax'])) ? 1 : 0; 
                $arrParam['pointValue'] = (isset($arrParam['pointValue']) && !empty($arrParam['pointValue'])) ? $this->unFormatNumber($arrParam['pointValue']) : 0; 
			
				$subtotal = 0 ;
				$subtotalistax23 = 0 ;
				$grandtotal = 0;
				
				$arrItemKey = $arrParam['hidItemKey'];
				 
				$finalDiscount = $this->unFormatNumber($arrParam['finalDiscount']); 
				$finalDiscountType = $arrParam['selFinalDiscountType']; 
				$taxPercentage = $this->unFormatNumber($arrParam['taxPercentage']); 
				$taxValue = $this->unFormatNumber($arrParam['taxValue']); 
				$shipmentFee = $this->unFormatNumber($arrParam['shipmentFee']); 
				$etcCost = $this->unFormatNumber($arrParam['etcCost']); 
				$pointValue =$this->unFormatNumber($arrParam['pointValue']);   
				 
				$arrQty = $arrParam['qty']; 
				$arrPriceinunit = $arrParam['priceInUnit']; 
				$arrDiscountValueInUnit = $arrParam['discountValueInUnit']; 
				$arrDiscountType = $arrParam['selDiscountType']; 
				$arrTransUnitKey = $arrParam['selUnit']; 
				$arrChkTax  = $arrParam['chkIsTax23'];                     

				 
				$arrItemDetail = array();
				$item = new Item();
				$totalProfit = 0;
				
				for ($i=0;$i<count($arrItemKey);$i++){
					
					if (empty($arrItemKey[$i]))  
						continue; 
                    
                        $rsItem = $item->getDataRowById($arrItemKey[$i]);


                        $itemkey = $arrItemKey[$i];
                        $transactionUnitKey = $arrTransUnitKey[$i];
                        $baseunitkey = $rsItem[0]['baseunitkey']; 
						$qty =  $this->unFormatNumber($arrQty[$i]);
                        $conversionMultiplier = $item->getConvMultiplier($itemkey,$transactionUnitKey,$baseunitkey); 
                        $qtyinbaseunit = $qty * $conversionMultiplier;
						$priceInUnit = $this->unFormatNumber($arrPriceinunit[$i]);
						$discount = $this->unFormatNumber($arrDiscountValueInUnit[$i]);
						$discountType =  $this->unFormatNumber($arrDiscountType[$i]);
					 
					 	if ($discount != 0 && $discountType == 2){
							$discount = $discount/100 * $priceInUnit;
						}
                    
						

                        $arrItemDetail[$i]['baseUnitKey'] = $baseunitkey;
                        $arrItemDetail[$i]['unitConvMultiplier'] = $conversionMultiplier;
						$arrItemDetail[$i]['isPackage'] = $rsItem[0]['ispackage'];
						$arrItemDetail[$i]['itemType'] = $rsItem[0]['itemtype']; 
                        $arrItemDetail[$i]['qtyInBaseUnit'] = $qtyinbaseunit ; 
                        $arrItemDetail[$i]['priceInBaseUnit'] = $priceInUnit / $conversionMultiplier ;
						$detailSubtotal = $qty * ($priceInUnit - $discount);
						$arrItemDetail[$i]['unitDiscountValue'] = $discount;
						$arrItemDetail[$i]['detailSubtotal'] = $detailSubtotal;

						$subtotal += $detailSubtotal ; 
                        
                        if(!empty($arrChkTax[$i]))
                            $subtotalistax23 += $detailSubtotal;

				} 
				  
				$grandtotal = $subtotal;

				if ($finalDiscount != 0){
					if ($finalDiscountType == 2)
						$finalDiscount = $finalDiscount/100 * $grandtotal;
				} 
				 
                $totalFinalDiscountAndPointValue = $finalDiscount + $pointValue;
                
                for ($i=0;$i<count($arrItemKey);$i++){
					
					if (empty($arrItemKey[$i]))  
						continue;
					   
                        $qtyinbaseunit = $arrItemDetail[$i]['qtyInBaseUnit'];
                        $conversionMultiplier = $arrItemDetail[$i]['unitConvMultiplier'];
						$priceInUnit = $this->unFormatNumber($arrPriceinunit[$i]); 
                    
                        $unitDiscountedValue = $priceInUnit - $arrItemDetail[$i]['unitDiscountValue'] ;
						$priceInUnitBeforeTax = $unitDiscountedValue - (($unitDiscountedValue/$subtotal) * $totalFinalDiscountAndPointValue);
					
						if ($isPriceIncludeTax == true) { 
								$taxValue = ($taxPercentage/(100 + $taxPercentage)) * $priceInUnitBeforeTax;   
								$priceInUnitBeforeTax = $priceInUnitBeforeTax - $taxValue ;
						}  
						
						$rsItem = $item->getDataRowById($arrItemKey[$i]);
						$arrItemDetail[$i]['cogs'] = $rsItem[0]['cogs'];	

                    	$arrItemDetail[$i]['profit'] = ($priceInUnitBeforeTax / $conversionMultiplier) - $rsItem[0]['cogs'];
                    
					    $totalProfit += ($qtyinbaseunit * $arrItemDetail[$i]['profit']); 
				} 
				
        
        
        
				$beforeTaxTotal = $subtotal - $totalFinalDiscountAndPointValue;
        

				$grandtotal = $beforeTaxTotal;
			    $tax23Percentage = $arrParam['tax23Percentage'];

 				if ($isPriceIncludeTax == false) {
						$taxValue = $beforeTaxTotal * $taxPercentage / 100;
						$grandtotal += $taxValue;

				}else{
						$taxValue = ($taxPercentage/(100 + $taxPercentage)) * $grandtotal;   
				 		$beforeTaxTotal = $grandtotal - $taxValue ;
                        $subtotalistax23 = $subtotalistax23 - (($taxPercentage/(100 + $taxPercentage)) * $subtotalistax23);

				}

                $istax23 = $tax23Percentage * $subtotalistax23/100;
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
         
            
                $balance = $totalPayment - $grandtotal ; 

				$reCountResult = array();
				$reCountResult['subtotal'] = $subtotal;
				$reCountResult['beforeTaxTotal'] = $beforeTaxTotal;
				$reCountResult['isPriceIncludeTax'] = $isPriceIncludeTax;
				$reCountResult['grandtotal'] = $grandtotal;
				$reCountResult['totalPayment'] = $totalPayment;
				$reCountResult['balance'] = $balance;
				$reCountResult['profit'] = $totalProfit;
				$reCountResult['tax23Value'] = $istax23;
				$reCountResult['detailCOGS'] = $arrItemDetail;
				
				return $reCountResult;
				
	}
	   
    function updatePackage($arrParam){
        $pkey = $arrParam['pkey'];

        $sql = 'delete from '.$this->tablePackageDetail.' where refheaderkey = '. $this->oDbCon->paramString($pkey);
        $this->oDbCon->execute($sql);

        $rsDetail = $this->getDetailById($pkey); 

        $item = new Item();
        $itemPackage = new ItemPackage();

        for ($i=0;$i<count($rsDetail);$i++){
            $rsItem = $item->getDataRowById($rsDetail[$i]['itemkey']);
            if(empty($rsItem[0]['pkey']) || !$rsItem[0]['ispackage'])
                continue;

            $rsItemPackage = $itemPackage->getDetailWithRelatedInformation($rsItem[0]['pkey']);
            for ($j=0;$j<count($rsItemPackage);$j++){
                if(empty($rsItemPackage[$j]['itemkey']))
                    continue;
                $qtyinbaseunitPackage = $rsDetail[$i]['qtyinbaseunit'];

                $sql = 'insert into '.$this->tablePackageDetail.' (
                            refkey,
                            refheaderkey,
                            itemkey,
                            qty,  
                            qtyinbaseunit,  
                            unitkey,
                            priceinunit, 
                            priceinbaseunit, 
                            unitconvmultiplier , 
                            costinbaseunit  
                         ) values (
                            '.$this->oDbCon->paramString($rsDetail[$i]['pkey']).',
                            '.$this->oDbCon->paramString($pkey).',
                            '.$this->oDbCon->paramString($rsItemPackage[$j]['itemkey']).',
                            '.$this->oDbCon->paramString($rsItemPackage[$j]['qty']).',
                            '.$this->oDbCon->paramString($rsItemPackage[$j]['qtyinbaseunit']).',
                            '.$this->oDbCon->paramString($rsItemPackage[$j]['unitkey']).',
                            '.$this->oDbCon->paramString($rsItemPackage[$j]['priceinunit']).',
                            '.$this->oDbCon->paramString($rsItemPackage[$j]['priceinbaseunit']).',
                            '.$this->oDbCon->paramString($rsItemPackage[$j]['unitconvmultiplier']).' ,
                            '.$this->oDbCon->paramString($rsItemPackage[$j]['cogs']).' 
                        )';	 
                     $this->oDbCon->execute($sql);

                }
        }
    } 
     
    function getPackageDetail($detailkey,$itemtype=''){
        $sql = 'select 
                    '.$this->tablePackageDetail.'.*,
                    '.$this->tableItem .'.name as itemname, 
                    '.$this->tableItemUnit .'.name as unitname
                from
                    '.$this->tablePackageDetail.',
                    '.$this->tableItem .',
                    '.$this->tableItemUnit .'
                where 
                    '.$this->tablePackageDetail.'.refkey = '. $this->oDbCon->paramString($detailkey).' and
                    '.$this->tablePackageDetail.'.itemkey = '.$this->tableItem .'.pkey and
                    '.$this->tablePackageDetail.'.unitkey = '.$this->tableItemUnit .'.pkey
                ';
        
        if (!empty($itemtype))
            $sql .= ' and '.$this->tableItem .'.itemtype in ('.$itemtype.')';
        
        return $this->oDbCon->doQuery($sql);         
    }

  
    function validateForm($arr,$pkey = ''){
        $item = new Item();   

        $arrayToJs = parent::validateForm($arr,$pkey); 
        $detailMustUnique = $this->loadSetting('SalesDetailMustUnique');
        $customerkey = $arr['hidCustomerKey'];   
        $carkey = $arr['hidCarKey']; 
        $vehicleid = $arr['vehicleid']; 

        $arrItemkey = $arr['hidItemKey']; 
        $arrItemkey = $arr['hidItemKey']; 
        $arrQty = $arr['qty']; 
        $arrPriceinunit = $arr['priceInUnit'];
        //$email = $arr['recipientEmail'];
        $arrSelUnit = $arr['selUnit']; 
        


        if (PLAN_TYPE['maxsalesorder'] >= 0){ 
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

            if($rs[0]['total'] >= PLAN_TYPE['maxsalesorder'])   
              $this->addErrorList($arrayToJs,false,$this->errorMsg['limit'][1]);   
        }

        if(empty($carkey) && empty($vehicleid)){
            $this->addErrorList($arrayToJs,false,$this->errorMsg['car'][1]);

        }

        //validasi kalo status gk menunggu gk bisa edit 
        if (!empty($pkey)){
            $rs = $this->getDataRowById($pkey);
            if ($rs[0]['statuskey'] <> 1){
                $this->addErrorList($arrayToJs,false,$this->errorMsg[212]);
            }
        }  

        if(empty($customerkey)){
            $this->addErrorList($arrayToJs,false,$this->errorMsg['customer'][1]);
        } 

        /*if(!empty($email)){
            if(!filter_var($email, FILTER_VALIDATE_EMAIL)) 
                $this->addErrorList($arrayToJs,false,$this->errorMsg['email'][3]); 
        }*/ 


        $arrDetailKeys = array(); 

        for($i=0;$i<count($arrItemkey);$i++) { 
            if (empty($arrItemkey[$i]) ){ 
                $this->addErrorList($arrayToJs,false, $this->errorMsg['item'][1]); 	
            }

            if (!empty($arrItemkey[$i])){
                 $rsItem = $item->getDataRowById($arrItemkey[$i]);
                if ($this->unFormatNumber($arrQty[$i]) <= 0){ 
                    $this->addErrorList($arrayToJs,false,$rsItem[0]['name']. '. ' . $this->errorMsg[510]);  
                }

                $priceMandatory = $this->loadSetting('priceMandatory');
                if ($priceMandatory == 1 && $this->unFormatNumber($arrPriceinunit[$i]) <= 0){  
                    $this->addErrorList($arrayToJs,false,$rsItem[0]['name']. '. ' . $this->errorMsg[511]);  
                }  

                // cek punya konversi unit utk satuan yg dipilih gk  
                $conv = $item->getConvMultiplier($arrItemkey[$i],$arrSelUnit[$i]);
                if (empty($conv)){
                    $rsItem = $item->getDataRowById($arrItemkey[$i]);
                    $this->addErrorList($arrayToJs,false,$rsItem[0]['name']. '. ' . $this->errorMsg['itemUnitConversion'][3]); 
                }  
            }

            if($detailMustUnique == 1){
                // cek ada detail double gk 
                if (in_array($arrItemkey[$i],$arrDetailKeys)){  
                    $rsItem = $item->getDataRowById($arrItemkey[$i]);
                    $this->addErrorList($arrayToJs,false, $rsItem[0]['name'].'. '.$this->errorMsg[215]); 	 
                }else{ 
                    array_push($arrDetailKeys, $arrItemkey[$i]);
                }   
            }
             
             
        }
        //validasi creditlimit
        if (isset($arr['total'])){ 
            $customer = new Customer(); 
            if ($customer->willExceedCreditLimit($customerkey,$arr['total'])){
                 $this->addErrorList($arrayToJs,false,$this->errorMsg['creditlimit'][1]);
            }
        } 


        /* utk handle edit bagian UI frontend */ 
        if (isset($arr['fromFE']) && !empty($arr['fromFE'])){

            $captchaResponse = $arr['g-recaptcha-response'];  
            $request = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$this->loadSetting('reCaptchaSecretKey')."&response=".$captchaResponse);
            $captchaResult = json_decode($request);

            $errorCaptcha= $captchaResult->{'error-codes'}; 


            if (empty($captchaResponse)){
                $this->addErrorList($arrayToJs,false,$this->errorMsg['captcha'][1]);
            } else if(!$captchaResult->{'success'}){
                $this->addErrorList($arrayToJs,false,$this->errorMsg['captcha'][1]);
            } 


             $recipientName =  $arr['recipientName'];
             $recipientPhone=  $arr['recipientPhone'];
             $recipientAddress=  $arr['recipientAddress'];

            if(empty($recipientName)){
                $this->addErrorList($arrayToJs,false,$this->errorMsg['name'][1]);
            } 

            if(empty($recipientPhone)){
                $this->addErrorList($arrayToJs,false,$this->errorMsg['phone'][1]);
            }

            if(empty($email)){
                $this->addErrorList($arrayToJs,false,$this->errorMsg['email'][1]);
            }

            if(empty($recipientAddress)){
                $this->addErrorList($arrayToJs,false,$this->errorMsg['address'][1]);
            }

        }
 
        return $arrayToJs;
     } 

    function updateGL($rs){
        if (!USE_GL) return;

        $warehouse = new Warehouse();
        $coaLink = new COALink();
        $generalJournal = new GeneralJournal();
        $customer = new Customer();
        $item = new Item();
        
        $warehousekey = $rs[0]['warehousekey'];

        $rsKey = $generalJournal->getTableKeyAndObj($this->tableName);
        $arr = array();
        $arr['pkey'] = $generalJournal->getNextKey($generalJournal->tableName);
        $arr['code'] = 'xxxxx';
        $arr['refkey'] = $rs[0]['pkey'];
	    $arr['refTableType'] = $rsKey['key'];
        $arr['trDate'] = $this->formatDBDate($rs[0]['trdate'],'d / m / Y');  
        $arr['createdBy'] = 0;
        $arr['trDesc'] = ''; 
		$arr['selWarehouseKey'] = $rs[0]['warehousekey'];

        $temp = -1;

        //HPP 
        $totalDisc = 0 ; 

        $rsDetail = $this->getDetailById($rs[0]['pkey']);  
        $finalDiscount = ($rs[0]['finaldiscount'] != 0 && $rs[0]['finaldiscounttype'] == 2) ? $rs[0]['finaldiscount']/100 * $rs[0]['subtotal'] : $rs[0]['finaldiscount']; 

        $arrItemSellingCOA = array(); 
        $arrItemCOGSCOA = array(); 
        $totalDetailDiscount = 0;
        $totalHPP = 0;
        
        foreach($rsDetail as $detail){
                
            // COGS
            $itemCOGS = $detail['costinbaseunit'] * $detail['qtyinbaseunit'];
            $totalHPP += $itemCOGS;

            if($rs[0]['isfulldeliver'])
                $itemCOAKey = $item->getInventoryCOAKey($detail['itemkey'],$warehousekey);
            else
                $itemCOAKey = $item->getInventoryTempCOAKey($detail['itemkey'],$warehousekey); 
 
            $arrItemCOGSCOA[$itemCOAKey] = (!isset($arrItemCOGSCOA[$itemCOAKey])) ? $itemCOGS : $arrItemCOGSCOA[$itemCOAKey] + $itemCOGS; 
               
            // SELLING
            $totalItemValue = $detail['qtyinbaseunit'] * $detail['priceinbaseunit'];
            $discountValue = ($detail['discount'] != 0 && $detail['discounttype'] == 2) ? $detail['discount']/100 * $detail['priceinunit'] : $detail['discount'];  
            $detailDiscount =  $discountValue * $detail['qty'] ;
            $totalDetailDiscount += $detailDiscount;

            if ($rs[0]['ispriceincludetax'] == 1) { 
                $totalItemValue -= $detailDiscount; 

                $finalDiscountProportion = ($totalItemValue / $rs[0]['subtotal']) * $finalDiscount; 
                $totalItemValue -= $finalDiscountProportion;

                $taxValue  = ($rs[0]['taxpercentage']/(100 + $rs[0]['taxpercentage'])) * $totalItemValue;
                $totalItemValue -= $taxValue;   

                $totalItemValue += ($finalDiscountProportion + $detailDiscount ); 
            }


            $itemCOAKey = $item->getRevenueCOAKey($detail['itemkey'],$warehousekey); 
            $arrItemSellingCOA[$itemCOAKey] = (!isset($arrItemSellingCOA[$itemCOAKey])) ? $totalItemValue : $arrItemSellingCOA[$itemCOAKey] + $totalItemValue; 
 
        }

        foreach ($arrItemCOGSCOA as $coakey => $coaValue){  
            $temp++; 
            $arr['hidCOAKey'][$temp] = $coakey;
            $arr['debit'][$temp] = 0; 
            $arr['credit'][$temp] = $coaValue;
        } 

        foreach ($arrItemSellingCOA as $coakey => $coaValue){  
            $temp++; 
            $arr['hidCOAKey'][$temp] = $coakey;
            $arr['debit'][$temp] = 0; 
            $arr['credit'][$temp] = $coaValue;
        }
         
        $rsCOA = $coaLink->getCOALink ('hpp', $warehouse->tableName,$warehousekey, 0);
        $temp++;
        $arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];
        $arr['debit'][$temp] = $totalHPP; 
        $arr['credit'][$temp] = 0;   

        $totalDisc = $finalDiscount + $rs[0]['pointvalue'] + $totalDisc;

        $termOfPayment = new TermOfPayment();
        $rsTOP = $termOfPayment->getDataRowById($rs[0]['termofpaymentkey']);  
        $isCash = ($rsTOP[0]['duedays'] == 0) ? true : false; 

        if ($isCash) {
            $rsPayment = $this->getPaymentMethodDetail($rs[0]['pkey']);  
            for($i=0;$i<count($rsPayment); $i++){ 
                 $rsCOA = $coaLink->getCOALink ('payment', $warehouse->tableName,$warehousekey, $rsPayment[$i]['paymentkey']);
                 $temp++;
                 $arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];
                 $arr['debit'][$temp] = $rsPayment[$i]['amount']; 
                 $arr['credit'][$temp] = 0;  
            }
            
            //selisih pembayaran   
            $temp++; 
            if ($rs[0]['balance'] < 0){ 
                $rsCOA = $coaLink->getCOALink ('othercost', $warehouse->tableName,$warehousekey, 0); 
                $arr['debit'][$temp] = abs($rs[0]['balance']); 
                $arr['credit'][$temp] = 0; 
            }else{ 
                $rsCOA = $coaLink->getCOALink ('otherrevenue', $warehouse->tableName,$warehousekey, 0); 
                $arr['debit'][$temp] = 0; 
                $arr['credit'][$temp] = abs($rs[0]['balance']); 
            }
            $arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];

        }else { 

                 //akun piutang 
                $temp++;
                $arr['hidCOAKey'][$temp] =  $customer->getARCOAKey($rs[0]['customerkey'],$warehousekey);
                $arr['debit'][$temp] = $rs[0]['grandtotal']; 
                $arr['credit'][$temp] = 0;  
        }

        $rsCOA = $coaLink->getCOALink ('salesretaildiscount', $warehouse->tableName,$warehousekey, 0);
        $temp++;
        $arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];
        $arr['debit'][$temp] = $totalDisc; 
        $arr['credit'][$temp] = 0;  
    
        $rsCOA = $coaLink->getCOALink ('taxout', $warehouse->tableName,$warehousekey, 0);
        $temp++;
        $arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];
        $arr['debit'][$temp] = 0;
        $arr['credit'][$temp] = $rs[0]['taxvalue'];  

        $rsCOA = $coaLink->getCOALink ('otherrevenue', $warehouse->tableName,$warehousekey, 0);
        $temp++;
        $arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];
        $arr['debit'][$temp] = 0;
        $arr['credit'][$temp] = $rs[0]['shipmentfee'] + $rs[0]['etccost'];  
 
        $arrayToJs = $generalJournal->addData($arr);

        if (!$arrayToJs[0]['valid'])
                throw new Exception('<strong>'.$rs[0]['code'] . '</strong>. '.$this->errorMsg[504].' '.$arrayToJs[0]['message']);    
    }
    
    function getlatestSellingPrice($itemkey,$customerkey){

       $useLatestSellingPrice = $this->loadSetting('rememberLatestSellingPrice');
       $rs = array();

       if ($useLatestSellingPrice == 1){
                $sql = 'select 
                        coalesce(priceInUnit,0) as price
                    from 
                        '.$this->tableName.',' . $this->tableNameDetail .'
                    where 
                        '.$this->tableName.'.pkey ='.$this->tableNameDetail.'.refkey and
                        '.$this->tableName.'.customerkey = '.$this->oDbCon->paramString($customerkey).' and
                        '.$this->tableNameDetail.'.itemkey = '.$this->oDbCon->paramString($itemkey);


            $sql .= ' order by trdate desc limit 1';

            $rs =  $this->oDbCon->doQuery($sql); 
       } 


        if (empty($rs)){
            $item = new Item();
            $rsItem = $item->getDataRowById($itemkey);
            return $rsItem[0]['sellingprice'];
        }else{ 
            return $rs[0]['price'];
        }
    }
    
//    function getDetailServiceItem($pkey,$itemType=1){
//        
//        
//        
//    }

    function getDetailWithRelatedInformation($pkey,$itemtype='',$criteria=''){


       $sql = 'select
                '.$this->tableNameDetail .'.*, 
                '.$this->tableItem.'.name as itemname, 
                '.$this->tableItem.'.code as itemcode, 
                '.$this->tableItem.'.itemtype,
                '.$this->tableItem.'.commissiontype,
                '.$this->tableItem.'.commission,
                '.$this->tableBrand.'.name as brandname ,
                '.$this->tableItem.'.deftransunitkey,
                '.$this->tableItemUnit.'.name as unitname,
                '.$this->tableEmployee.'.name as salesname,
                 baseunit.name as baseunitname
              from
                '.$this->tableNameDetail .'   
                left join '.$this->tableEmployee.' on  '.$this->tableNameDetail.'.saleskey = '.$this->tableEmployee.'.pkey,
                '.$this->tableItemUnit.',
                '.$this->tableItemUnit.' baseunit,
                '.$this->tableItem.'
                    left join '.$this->tableBrand.' on 	' . $this->tableItem .'.brandkey = '.$this->tableBrand.'.pkey 

              where
                '.$this->tableNameDetail .'.itemkey = '.$this->tableItem.'.pkey and
                '.$this->tableNameDetail.'.unitkey = '.$this->tableItemUnit.'.pkey and
                '.$this->tableItem.'.baseunitkey = baseunit.pkey and
                refkey = '.$this->oDbCon->paramString($pkey) . ' ';

        $sql .= $criteria;
  
        return $this->oDbCon->doQuery($sql);

    }
 function generateDefaultQueryForAutoComplete($returnField){ 
        $sql = 'select
                '.$returnField['key'].',
                '.$returnField['value'].' as value,
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

   /* function searchDataForAutoComplete($fieldname='',$searchkey='',$mustmatch=false,$searchCriteria='',$orderCriteria='', $limit=''){

         $sql = 'select
                    '.$this->tableName. '.pkey,  concat('.$this->tableName. '.code,\' - \', '.$this->tableCustomer.'.name) as value,  grandtotal
                from 
                    '.$this->tableName . ','.$this->tableCustomer.','.$this->tableStatus.'
                where  		
                    '.$this->tableName . '.customerkey = '.$this->tableCustomer.'.pkey and
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
    }*/ 

 
    function generateInvoice($pkey){  

        $rsHeader = $this->getDataRowById($pkey);   

        $file=  HTTP_HOST . 'invoice/'.$pkey.'/'.md5($pkey . $rsHeader[0]['grandtotal'] . $this->secretKey).'/1';  
        $invoice =  file_get_contents($file);

        return $invoice;
    }

	
function updateCostAndProfit($id){
        $rs = $this->getDataRowById($id);
        $rsDetail = $this->getDetailById($id);
        $item = new Item();

        $subtotal = 0;
        $totalProfit = 0;

        for($i=0;$i<count($rsDetail);$i++){

            $rsItem = $item->getDataRowById($rsDetail[$i]['itemkey']);
            $cogs = $rsItem[0]['cogs'];

            $discount = $rsDetail[$i]['discount'];  
            if ($discount != 0 && $rsDetail[$i]['discounttype'] == 2){
                    $discount = $discount/100 * $rsDetail[$i]['priceinunit'];
            }

            $rsDetail[$i]['cogs'] = $cogs;
            $rsDetail[$i]['unitDiscountValue'] = $discount;
            $detailSubtotal = $rsDetail[$i]['qty'] * ( $rsDetail[$i]['priceinunit'] - $discount);

            $subtotal += $detailSubtotal ; 
        }

        $finalDiscount  = $rs[0]['finaldiscount'];
        if ($finalDiscount != 0 && $rs[0]['finaldiscounttype'] == 2){
                $finalDiscount = $finalDiscount/100 * $rs[0]['subtotal'];
        }

        $totalFinalDiscountAndPointValue = 0;
        if (!USE_GL){ 
            $totalFinalDiscountAndPointValue = $finalDiscount + $rs[0]['pointvalue'] ; 
        }

        for($i=0;$i<count($rsDetail);$i++){

            $unitDiscountedValue = $rsDetail[$i]['priceinunit'] -  $rsDetail[$i]['unitDiscountValue']  ;
            $proportionDisc = ($subtotal <> 0) ? $unitDiscountedValue/$subtotal : 0;
            
            $priceInUnitBeforeTax = $unitDiscountedValue - ($proportionDisc * $totalFinalDiscountAndPointValue);

            if ($rs[0]['ispriceincludetax'] == 1) { 
                    $taxValue = ($rs[0]['taxpercentage']/(100 + $rs[0]['taxpercentage'])) * $priceInUnitBeforeTax;   
                    $priceInUnitBeforeTax = $priceInUnitBeforeTax - $taxValue ; 
            }  

            $profit = ($priceInUnitBeforeTax / $rsDetail[$i]['unitconvmultiplier']) - $rsDetail[$i]['cogs'];
                
            $sql = 'update '. $this->tableNameDetail .' set costinbaseunit = '.$rsDetail[$i]['cogs'].', profit = '.$profit.' where pkey = ' . $rsDetail[$i]['pkey'];
            $this->oDbCon->execute($sql);

            $totalProfit  += $rsDetail[$i]['qtyinbaseunit'] * $profit;

        }

        //update header 
        $sql = 'update '. $this->tableName  .' set profit = '.$totalProfit.' where pkey = ' . $rs[0]['pkey'];
        $this->oDbCon->execute($sql);

    }

    function getSalesByMonth($startPeriod, $endPeriod){
         $sql = 'select 
                    month(trdate) as month,  DATE_FORMAT(trdate, \'%b\')  as monthname, year(trdate) as year, sum(beforetaxtotal) as total, sum(profit) as profit
                from 
                    '.$this->tableName.'
                where (statuskey = 2 or statuskey = 3) and trdate between \''. date("Y-m-d", strtotime($startPeriod)) .'\' and LAST_DAY(\''. date("Y-m-d 23:59", strtotime($endPeriod)) .'\') 
                    group by year(trdate),month(trdate)';

        return $this->oDbCon->doQuery($sql); 

    }

    function getMostProfitableSalesByGroup($groupBy, $startPeriod, $endPeriod, $limit = 5){

        $sql = 'select 
                  sum('.$this->tableNameDetail.'.profit * '.$this->tableNameDetail.'.qtyinbaseunit) as profit, 
                  '.$this->tableItemCategory.'.name  as categoryname,
                  '.$this->tableBrand.'.name  as brandname,
                  '.$this->tableItem.'.name as itemname
                from 
                    '.$this->tableName.', 
                    '.$this->tableNameDetail.',
                    '.$this->tableItem.', 
                    '.$this->tableItemCategory.', 
                    '.$this->tableBrand.'
                where 
                    ('.$this->tableName.'.statuskey = 2 or '.$this->tableName.'.statuskey = 3) and 
                     '.$this->tableName.'.pkey = '.$this->tableNameDetail.'.refkey and
                     trdate between \''. date("Y-m-01 00:00", strtotime($startPeriod)) .'\' and LAST_DAY(\''. date("Y-m-d 23:59", strtotime($endPeriod)) .'\') and 
                     '.$this->tableNameDetail.'.itemkey = '.$this->tableItem.'.pkey and 
                     '.$this->tableItem.'.categorykey = '.$this->tableItemCategory.'.pkey  and 
                     '.$this->tableItem.'.brandkey = '.$this->tableBrand.'.pkey 
                 group by 
                    '.$groupBy.'
                 order by profit desc limit ' . $limit;

        return $this->oDbCon->doQuery($sql); 
    }   

    function getBestSellingByGroup($groupBy, $startPeriod, $endPeriod, $limit = 5){

        $sql = 'select 
                  sum('.$this->tableNameDetail.'.qtyinbaseunit) as qty, 
                  '.$this->tableItemCategory.'.name  as categoryname,
                  '.$this->tableBrand.'.name  as brandname,
                  '.$this->tableItem.'.name as itemname,
                  '.$this->tableItemUnit.'.name as unitname 
                from 
                    '.$this->tableName.', 
                    '.$this->tableNameDetail.',
                    '.$this->tableItem.', 
                    '.$this->tableItemCategory.', 
                    '.$this->tableBrand.',
                    '.$this->tableItemUnit.' 
                where 
                    ('.$this->tableName.'.statuskey = 2 or '.$this->tableName.'.statuskey = 3) and 
                     '.$this->tableName.'.pkey = '.$this->tableNameDetail.'.refkey and
                     trdate between \''. date("Y-m-01 00:00", strtotime($startPeriod)) .'\' and LAST_DAY(\''. date("Y-m-d 23:59", strtotime($endPeriod)) .'\') and 
                     '.$this->tableNameDetail.'.itemkey = '.$this->tableItem.'.pkey and 
                     '.$this->tableItem.'.categorykey = '.$this->tableItemCategory.'.pkey  and 
                     '.$this->tableItem.'.brandkey = '.$this->tableBrand.'.pkey   and 
                     '.$this->tableItem.'.baseunitkey = '.$this->tableItemUnit.'.pkey   
                 group by 
                    '.$groupBy.'
                 order by qty desc limit ' . $limit;

        return $this->oDbCon->doQuery($sql); 
    }  

    function getBestSalesAmountByGroup($groupBy, $startPeriod, $endPeriod, $limit = 5){
        // Sales Amount

        $sql = 'select 
                  sum('.$this->tableName.'.beforetaxtotal) as amount, 
                  '.$this->tableCustomer.'.name  as customername
                from 
                    '.$this->tableName.', 
                    '.$this->tableCustomer.' 
                where 
                    ('.$this->tableName.'.statuskey = 2 or '.$this->tableName.'.statuskey = 3) and 
                     '.$this->tableName.'.customerkey = '.$this->tableCustomer.'.pkey and
                     trdate between \''. date("Y-m-01 00:00", strtotime($startPeriod)) .'\' and LAST_DAY(\''. date("Y-m-d 23:59", strtotime($endPeriod)) .'\')  
                 group by 
                    '.$groupBy.'
                 order by amount desc limit ' . $limit;

        return $this->oDbCon->doQuery($sql); 
    }  
  
     function normalizeParameter($arrParam, $trim=false){
            $item = new Item();
            $termOfPayment = new TermOfPayment();
         
            $arrParam = parent::normalizeParameter($arrParam);  

            $arrParam['recipientName'] = (empty($arrParam['recipientName'])) ? '' : $arrParam['recipientName'];
            $arrParam['recipientPhone'] = (empty($arrParam['recipientPhone'])) ? '' : $arrParam['recipientPhone'];
            $arrParam['recipientEmail'] = (empty($arrParam['recipientEmail'])) ? '' : $arrParam['recipientEmail'];
            $arrParam['recipientAddress'] = (empty($arrParam['recipientAddress'])) ? '' : $arrParam['recipientAddress']; 
  
            $arrParam['chkIsFullDeliver'] = 1; //(!empty($arrParam['chkIsFullDeliver'])) ? 1 : 0; 

            $arrItemkey = $arrParam['hidItemKey'];
            $arrQty = $arrParam['qty']; 
            $arrPriceinunit = $arrParam['priceInUnit']; 
            $arrDiscountValueInUnit = $arrParam['discountValueInUnit']; 
            $arrDiscountType = $arrParam['selDiscountType']; 
            $arrUnitKey = $arrParam['selUnit']; 

            $rsTOP = $termOfPayment->getDataRowById($arrParam['selTermOfPaymentKey']);  
            if ($rsTOP[0]['duedays'] != 0){   
                for($i=0;$i<count( $arrParam['paymentMethodValue']);$i++){ 
                    $arrParam['paymentMethodValue'][$i] = 0; 
                    $arrParam['hidDetailPaymentKey'][$i] = 0;
                }
            }

            $reCountResult = $this->reCountSubtotal($arrParam); 
            $arrParam['detailCOGS'] = $reCountResult['detailCOGS']; 
            $arrParam['profit'] = $reCountResult['profit'];
            $arrParam['subtotal'] = $reCountResult['subtotal'];
            $arrParam['beforeTaxTotal'] = $reCountResult['beforeTaxTotal'];
            $arrParam['isPriceIncludeTax'] = $reCountResult['isPriceIncludeTax'];
            $arrParam['grandtotal'] = $reCountResult['grandtotal'];
            $arrParam['totalPayment'] = $reCountResult['totalPayment'];
            $arrParam['tax23Value'] = $reCountResult['tax23Value'];
            $arrParam['balance'] = $reCountResult['balance'];


             for ($i=0;$i<count($arrItemkey);$i++){ 
 
                $qtyinbaseunit = $arrParam['detailCOGS'][$i]['qtyInBaseUnit'];
                $arrParam['qtyInBaseUnit'][$i] = $qtyinbaseunit;
                $arrParam['unitConvMultiplier'][$i] = $arrParam['detailCOGS'][$i]['unitConvMultiplier'];
                $arrParam['cogs'][$i] = $arrParam['detailCOGS'][$i]['cogs'];
                $arrParam['detailProfit'][$i] = $arrParam['detailCOGS'][$i]['profit'];

                $arrParam['priceInBaseUnit'][$i] = $arrParam['detailCOGS'][$i]['priceInBaseUnit'];
                $arrParam['detailSubtotal'][$i] = $arrParam['detailCOGS'][$i]['detailSubtotal']; 
                $arrParam['isPackage'][$i] = $arrParam['detailCOGS'][$i]['isPackage'];
                $arrParam['itemType'][$i] = $arrParam['detailCOGS'][$i]['itemType'];
                 
                $arrParam['hidDetailSalesKey'][$i] = (!isset($arrParam['hidDetailSalesKey'][$i]) || empty($arrParam['hidDetailSalesKey'][$i])) ? 0 : $arrParam['hidDetailSalesKey'][$i]; 
                $arrParam['hidDetailWarehouseKey'][$i] = (!isset($arrParam['hidDetailWarehouseKey'][$i]) || empty($arrParam['hidDetailWarehouseKey'][$i])) ? 0 : $arrParam['hidDetailWarehouseKey'][$i]; 
  
            }


        return $arrParam;
    }
     
    function updateDetailTablesOnCopy($id,$newPkey, $arrTableDetail){ 
         
        for($k=0;$k<count($arrTableDetail);$k++){
            $rsDetail = $this->getDetailById($id,'','',$arrTableDetail[$k]);

            $sql = 'show columns from ' . $arrTableDetail[$k] ;   
            $rsColumnsName = $this->oDbCon->doQuery ($sql); 

            for ($j=0;$j<count($rsDetail);$j++){
                $fields = '';
                $data = ''; 
                $oldDetailKey = $rsDetail[$j]['pkey'];
                
                if ($arrTableDetail[$k] == $this->tableNameDetail)  
                     $rsDetail[$j]['pkey'] = $this->getNextKey($this->tableNameDetail);  
              
                $rsDetail[$j]['refkey'] = $newPkey; 
                
                for ($i=1;$i<count($rsColumnsName);$i++){

                    $fields .= $rsColumnsName[$i]['Field'];  
                    $data .=   $this->oDbCon->paramString($rsDetail[$j][$rsColumnsName[$i]['Field']]);

                    if ($i <> count($rsColumnsName) - 1){
                      $data .= ',';   
                      $fields.= ',';    
                    }

                }

                $sql = 'insert into ' .$arrTableDetail[$k].'  ('.$fields.') values ('.$data.')'; 
                $this->oDbCon->execute ($sql);	
                
                if (isset($rsDetail[$j]['ispackage']) &&  $rsDetail[$j]['ispackage'] == 0 )
                    continue;
                    
                // ============= update detail Package
                
                $rsItemDetail = $this->getPackageDetail($oldDetailKey);
                $sql = 'show columns from ' . $this->tablePackageDetail;   
                $rsDetailsColumnsName = $this->oDbCon->doQuery($sql); 
                 
               for ($z=0;$z<count($rsItemDetail);$z++){
                    $fields = '';
                    $data = ''; 

                    for ($i=1;$i<count($rsDetailsColumnsName);$i++){

                        $fields .= $rsDetailsColumnsName[$i]['Field'];

                        $rsItemDetail[$z]['refheaderkey'] = $newPkey;
                        $rsItemDetail[$z]['refkey'] = $rsDetail[$j]['pkey']; 

                        $data .= $this->oDbCon->paramString($rsItemDetail[$z][$rsDetailsColumnsName[$i]['Field']]);

                        if ($i <> count($rsDetailsColumnsName) - 1){
                          $data .= ',';   
                          $fields.= ',';    
                        }

                    }

                    $sql = 'insert into ' .$this->tablePackageDetail.'  ('.$fields.') values ('.$data.')';  
                    $this->oDbCon->execute ($sql);	 
               }
                
                
                // ============= end update detail Package
                
            }  
        }  
        
    }
    
     function validateConfirm($rsHeader){
        $rewardsPoint = new RewardsPoint();
        $warehouse = new Warehouse();  
        $coaLink = new COALink(); 
        $item = new Item();
         
        $id = $rsHeader[0]['pkey'];
        $rsDetail = $this->getDetailById($id);
        $rsPayment = $this->getPaymentMethodDetail($id); 

        $termOfPayment = new TermOfPayment();
        $rsTOP = $termOfPayment->getDataRowById($rsHeader[0]['termofpaymentkey']); 
        $isCash = ($rsTOP[0]['duedays'] == 0) ? true : false; 
 
        $totalPayment = 0; 
        for($i=0;$i<count($rsPayment); $i++)
            $totalPayment += $rsPayment[$i]['amount'];

        $balance = $totalPayment - $rsHeader[0]['grandtotal'] ;  
  
        if ($isCash){ 
            $thresholdDiscount = abs($this->loadSetting('roundedPaymentThreshold'));
            if($balance < ($thresholdDiscount * -1)) 
                $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg[502]);
            else if ($balance > $thresholdDiscount)
                $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg[509]); 
        }
         
        // validasi point
        $point = $rsHeader[0]['pointvalue'];
        $currentPoint = $rewardsPoint->getSumTotalRewards($rsHeader[0]['customerkey']) * $this->loadSetting('rewardsPointUnitValue');
        if ($point > $currentPoint)
            $this->addErrorLog(false,$this->errorMsg['point'][3]);

         if (USE_GL){
                $arrCOA = array();
                array_push($arrCOA, 'salesretail' , 'taxout', 'otherrevenue', 'hpp' , 'inventory' , 'inventorytemp', 'salesretaildiscount'); 
                for ($i=0;$i<count($arrCOA);$i++){
                    $rsCOA = $coaLink->getCOALink ($arrCOA[$i], $warehouse->tableName,$rsHeader[0]['warehousekey'], 0); 
                    if (empty($rsCOA))	
                        $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg['coa'][3]);
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

         } 


        //validasi stock
        $itemMovement = new itemMovement(); 
        for($i=0;$i<count($rsDetail);$i++){
             $arrDetailKey = array();
             $arrQty = array();

            if(empty($rsDetail[$i]['itemkey']) || $rsDetail[$i]['itemtype'] == SERVICE)
                continue;

           if ($rsDetail[$i]['ispackage'] == 0){
                array_push($arrDetailKey, $rsDetail[$i]['itemkey']);
                array_push($arrQty, $rsDetail[$i]['qtyinbaseunit']); 
            }else{
                $rsPackageDetail = $this->getPackageDetail($rsDetail[$i]['pkey'],1);

                for($j=0;$j<count($rsPackageDetail); $j++){
                    array_push($arrDetailKey, $rsPackageDetail[$j]['itemkey']);
                    array_push($arrQty, ($rsPackageDetail[$j]['qtyinbaseunit']*$rsDetail[$i]['qtyinbaseunit'])); 
                }
            } 

            $warehousekey = array();
            array_push($warehousekey,$rsHeader[0]['warehousekey']);

            if (!empty($rsDetail[$i]['warehousekey'])){
                if (!empty($rsDetail[$i]['warehousekey']) && $rsDetail[$i]['movementtype'] == 1){
                    array_push($warehousekey,$rsDetail[$i]['warehousekey']);
                }else{
                    $warehousekey = array($rsDetail[$i]['warehousekey']);
                }
            } 

            for($k=0;$k<count($arrDetailKey);$k++) {

                $saldoakhir = 0 ;
                for($l=0;$l<count($warehousekey);$l++){ 
                    //$this->setLog($warehousekey[$l] . '  x-> ' . $arrDetailKey[$k] . '  : ' . $itemMovement->getItemQOH($arrDetailKey[$k], $warehousekey[$l]));
                    $saldoakhir += $itemMovement->getItemQOH($arrDetailKey[$k], $warehousekey[$l]);  
                }

                 $totalqty = $saldoakhir - $arrQty[$k];  
                 if($totalqty<0){   
                    $rsItem = $item->getDataRowById($arrDetailKey[$k]); 
                    $this->addErrorLog(false,'<strong>'.$rsItem[0]['name'].'</strong>. '.$this->errorMsg[402]);
                 }
            }


        } 
 
     }
 
    function confirmTrans($rsHeader){ 

        $id = $rsHeader[0]['pkey'];
        $rsDetail = $this->getDetailWithRelatedInformation($rsHeader[0]['pkey']);
   
        $this->updateCostAndProfit($id);

        $item = new Item();
        $customer = new Customer();
        $coaLink = new COALink();
        $rsCustomer = $customer->getDataRowById($rsHeader[0]['customerkey']);
        $note = $rsHeader[0]['code'].'. Jual ke '.$rsCustomer[0]['name'];
        $warehouse = new Warehouse();
        $rsWarehouse = $warehouse->getDataRowById($rsHeader[0]['warehousekey']);
        $notecash = $rsHeader[0]['code'].'. Kas Masuk dari '.$rsWarehouse[0]['name'].' untuk penjualan barang dari '.$rsCustomer[0]['name'];
        $termOfPayment = new TermOfPayment();
        $rsTOP = $termOfPayment->getDataRowById($rsHeader[0]['termofpaymentkey']); 
		$isCash = ($rsTOP[0]['duedays'] == 0) ? true : false; 


        // MENGHITUNG PAYMENT
        if ($isCash){
            $rsPayment = $this->getPaymentMethodDetail($id);   
            $cashMovement = new CashMovement();  

            for($i=0;$i<count($rsPayment); $i++){  
                   if (USE_GL) {
                       $rsPaymentCOA = $coaLink->getCOALink ('payment', $warehouse->tableName,$rsHeader[0]['warehousekey'], $rsPayment[$i]['paymentkey']); 
				       $coakey = $rsPaymentCOA[0]['coakey']; 
				   }else{
				       $coakey = $rsPayment[$i]['paymentkey'];
				   }  
                 $cashMovement->updateCashMovement($id, $coakey,$rsPayment[$i]['amount'],$this->tableName, $rsHeader[0]['warehousekey'], $notecash,$rsHeader[0]['trdate']);
            }          
        }else{
            //update AR
            $ar = new AR();
            $item = new Item(); 

            $arrParam = array();	

            $rsARKey = $ar->getTableKeyAndObj($this->tableName);  
            $arrParam['code'] = 'xxxxxx';
            $arrParam['hidCustomerKey'] = $rsHeader[0]['customerkey'];
            $arrParam['hidSalesKey'] = $rsHeader[0]['saleskey']; 
            $arrParam['hidRefKey'] = $id;
            $arrParam['hidRefHeaderKey'] = $id;
            $arrParam['hidRefCode'] =  $rsHeader[0]['code'];
            $arrParam['hidRefDate'] =  $this->formatDBDate($rsHeader[0]['trdate'],'d / m / Y');  
            $arrParam['hidRefTable'] = $rsARKey['key'];
            $arrParam['selWarehouse'] = $rsHeader[0]['warehousekey'];
            $arrParam['selARType'] = 1; 
            $arrParam['amount'] = abs($rsHeader[0]['balance']);
            $arrParam['trDesc'] = '';
            $arrParam['trDate'] =  $this->formatDBDate($rsHeader[0]['trdate'],'d / m / Y');  
            $date = new DateTime($rsHeader[0]['trdate']);
            $date->add(new DateInterval('P'.$rsTOP[0]['duedays'].'D'));
            $arrParam['dueDate'] = $date->format('d / m / Y');// date ('d / m / Y', mktime(0, 0, 0, date("m")  , date("d")+$rsTOP[0]['duedays'], date("Y")));
            $arrParam['createdBy'] = 0;
            $arrParam['islinked'] = 1; 
            $arrParam['overwriteGL'] = 1;

            $arrayToJs = $ar->addData($arrParam); 
            if (!$arrayToJs[0]['valid'])
                throw new Exception('<strong>'.$rsHeader[0]['code'] . '</strong>. '.$this->errorMsg[201].' '.$arrayToJs[0]['message']); 
            
        }
        
         // UPDATE AP COMMISSION
        /*$apCommission = new APCommission(); 

        for($j=0;$j<count($rsDetail);$j++){
  
            if(empty($rsDetail[$j]['saleskey']) || empty($rsDetail[$j]['commission']))
                continue; 

            $arrParam = array();  
            $price = $rsDetail[$j]['total'];

            if ($rsHeader[0]['finaldiscount'] > 0){ 
                $discount = ($rsHeader[0]['finaldiscounttype'] == 1) ? $rsHeader[0]['finaldiscount']  : $rsHeader[0]['subtotal'] * $rsHeader[0]['finaldiscount'] / 100;
                $discount = ($price / $rsHeader[0]['subtotal']) * $discount; 
                $price -= $discount;
            } 

            //$this->setLog($rsDetail[$j]['qtyinbaseunit'] . ' * '.$rsDetail[$j]['commission']);
            $commission = ($rsDetail[$j]['commissiontype'] == 1) ?  $rsDetail[$j]['qtyinbaseunit'] * $rsDetail[$j]['commission'] : $price * $rsDetail[$j]['commission'] / 100 ;
 
            if ($commission <= 0)
                continue;
   
            $commissionDuedate = 30;
            $rsARKey = $apCommission->getTableKeyAndObj($this->tableName); 

            $arrParam['code'] = 'xxxxxx';
            $arrParam['hidSupplierKey'] = $rsDetail[$j]['saleskey'];
            $arrParam['hidRefKey'] = $rsDetail[$j]['pkey'];
            $arrParam['hidRefHeaderKey'] = $rsHeader[0]['pkey'];
            $arrParam['hidRefCode'] =  $rsHeader[0]['code'];
            $arrParam['hidRefDate'] =   $this->formatDBDate($rsHeader[0]['trdate'],'d / m / Y');  
            $arrParam['hidRefTable'] = $rsARKey['key'];
            $arrParam['amount'] = $commission;
            $arrParam['trDesc'] = '';
            $arrParam['trDate'] =  $this->formatDBDate($rsHeader[0]['trdate'],'d / m / Y');  
            $date = new DateTime($rsHeader[0]['trdate']);
            $date->add(new DateInterval('P'.$commissionDuedate.'D'));
            $arrParam['dueDate'] = $date->format('d / m / Y'); 
            $arrParam['createdBy'] = 0;
            $arrParam['selWarehouse'] = $rsHeader[0]['warehousekey'];
            $arrParam['islinked'] = 1;
            $arrParam['selAPType'] = 1;

            $arrayToJs = $apCommission->addData($arrParam);
            if (!$arrayToJs[0]['valid'])
                throw new Exception('<strong>'.$rsHeader[0]['code'] . '</strong>. '.$this->errorMsg[201].' '.$arrayToJs[0]['message']);  

        }
*/
        // END            


        if ($rsHeader[0]['isfulldeliver']){
            $item = new Item();
            $itemMovement = new ItemMovement();   

            for($i=0;$i<count($rsDetail); $i++){
                $arrDetailKey = array();
                $arrQty = array();
                $arrCost = array();
                
               // $pointValue = (isset($arrParam['pointValue']) && !empty($arrParam['pointValue'])) ? $this->unFormatNumber($arrParam['pointValue']) : 0; 
              
                $warehousekey = array();
                array_push($warehousekey,$rsHeader[0]['warehousekey']);
                
                if (!empty($rsDetail[$i]['warehousekey'])){
                    if (!empty($rsDetail[$i]['warehousekey']) && $rsDetail[$i]['movementtype'] == 1){
                        array_push($warehousekey,$rsDetail[$i]['warehousekey']);
                    }else{
                        $warehousekey = array($rsDetail[$i]['warehousekey']);
                    }
                } 
                
                if(empty($rsDetail[$i]['itemkey']) || $rsDetail[$i]['itemtype']==2)
                    continue;
                
               if ($rsDetail[$i]['ispackage'] == 0){
                    array_push($arrDetailKey, $rsDetail[$i]['itemkey']);
                    array_push($arrQty, $rsDetail[$i]['qtyinbaseunit']);
                    array_push($arrCost, $rsDetail[$i]['costinbaseunit']);
                }else{
                    $rsPackageDetail = $this->getPackageDetail($rsDetail[$i]['pkey'],1);

                    for($j=0;$j<count($rsPackageDetail); $j++){
                        array_push($arrDetailKey, $rsPackageDetail[$j]['itemkey']);
                        array_push($arrQty, ($rsPackageDetail[$j]['qtyinbaseunit']*$rsDetail[$i]['qtyinbaseunit']));
                        array_push($arrCost, $rsPackageDetail[$j]['costinbaseunit']);
                    }
                } 

                for($k=0;$k<count($arrDetailKey);$k++) {
                       
                    $saldoakhir = 0 ;
                    for($l=0;$l<count($warehousekey);$l++){ 
                        //$this->setLog($warehousekey[$l] . '  x-> ' . $arrDetailKey[$k] . '  : ' . $itemMovement->getItemQOH($arrDetailKey[$k], $warehousekey[$l]));
                        $qtyDeducted = $itemMovement->getItemQOH($arrDetailKey[$k], $warehousekey[$l]);    
                        $qtyDeducted = ($qtyDeducted >= $arrQty[$k]) ? $arrQty[$k] : $qtyDeducted;
                            
                        $arrQty[$k] -= $qtyDeducted;
                        $itemMovement->updateItemMovement($id,$arrDetailKey[$k],-$qtyDeducted, $arrCost[$k] ,$this->tableName, $warehousekey[$l], $note,$rsHeader[0]['trdate']);
                    }
                    
                    
                }
            }
            
        }

        // potong point jika digunakan
        if ($rsHeader[0]['pointvalue'] > 0){ 
            $totalPoint = $rsHeader[0]['pointvalue'] / $this->loadSetting('rewardsPointUnitValue'); 

            $rewardsPoint = new RewardsPoint();
            $arr = array();
            $arr['pkey']  = $rewardsPoint->getNextKey($rewardsPoint->tableName);
            $arr['code'] = 'xxxxx';
            $arr['trDate'] =  date('d / m / Y');
            $arr['hidCustomerKey'] = $rsHeader[0]['customerkey'];
            $arr['hidSalesOrderKey'] = $rsHeader[0]['pkey'];
            $arr['point'] = $totalPoint*-1;
            $arr['createdBy'] = 0;
            $arr['notes'] = 'Redeem'; 

            $rewardsPoint->addData($arr);  
            $rewardsPoint->changeStatus($arr['pkey'] ,2);
        }

        //update point jika ada
        $pointMultiple = $this->loadSetting('rewardsPointMultiple');
        if ($pointMultiple > 0){ 
            $beforeTaxTotal = $rsHeader[0]['beforetaxtotal']; 
            $totalPoint = floor($beforeTaxTotal/$pointMultiple); 

            if ($totalPoint > 0){
                $rewardsPoint = new RewardsPoint();
                $arr = array();
                $arr['pkey']  = $rewardsPoint->getNextKey($rewardsPoint->tableName);
                $arr['code'] = 'xxxxx';
                $arr['trDate'] =  date('d / m / Y');
                $arr['hidCustomerKey'] = $rsHeader[0]['customerkey'];
                $arr['hidSalesOrderKey'] = $rsHeader[0]['pkey'];
                $arr['point'] = $totalPoint ;
                $arr['createdBy'] = 0;
                $arr['notes'] = ''; 

                $rewardsPoint->addData($arr);  
                $rewardsPoint->changeStatus($arr['pkey'] ,2);
            }

        }

        //update jurnal umum 
        $this->updateGL($rsHeader);

    }  

    function cancelTrans($rsHeader,$copy){ 

		$id = $rsHeader[0]['pkey']; 
        $rewardsPoint = new RewardsPoint();  
 
        $cashMovement = new CashMovement();   
        $cashMovement->cancelMovement($id,$this->tableName);

        if ($rsHeader[0]['isfulldeliver']){ 
            $itemMovement = new ItemMovement();  
            $itemMovement->cancelMovement($id,$this->tableName); 
        }

        $rsRewards = $rewardsPoint->searchData('refkey',$id,true);
        for($i=0;$i<count($rsRewards); $i++)
            $rewardsPoint->changeStatus($rsRewards[$i]['pkey'] ,4,'',false,true);

        $ar = new AR();
        $rsARKey = $ar->getTableKeyAndObj($this->tableName); 
        $rsAR = $ar->searchData('','',true,' and reftabletype = '.$this->oDbCon->paramString($rsARKey['key']).' and  refkey = '.$this->oDbCon->paramString($id).' and '.$ar->tableName.'.statuskey = 1');
        for($i=0;$i<count($rsAR);$i++) {
            $arrayToJs = $ar->changeStatus($rsAR[$i]['pkey'],4,'',false,true);
            if (!$arrayToJs[0]['valid'])
                throw new Exception('<strong>'.$rsHeader[0]['code'] . '</strong>. '.  $arrayToJs[0]['message']);    
        }

        /*$ap = new APCommission();
        $rsAPKey = $ap->getTableKeyAndObj($this->tableName);  
        $rsAP = $ap->searchData('','',true,' and reftabletype = '.$this->oDbCon->paramString($rsAPKey['key']).' and  refheaderkey = '.$this->oDbCon->paramString($id).' and '.$ap->tableName.'.statuskey = 1');
        for($i=0;$i<count($rsAP);$i++) {
            $arrayToJs = $ap->changeStatus($rsAP[$i]['pkey'],4,'',false,true);
            if (!$arrayToJs[0]['valid'])
                throw new Exception('<strong>'.$rsHeader[0]['code'] . '</strong>. '.  $arrayToJs[0]['message']);    
        }*/
        
        $salesCarServiceReturn = new SalesCarServiceReturn();
        $rsSalesReturn = $salesCarServiceReturn->searchData('','',true,' and '.$salesCarServiceReturn->tableName.'.refkey = '.$this->oDbCon->paramString($id).' and ('.$salesCarServiceReturn->tableName.'.statuskey = 1)');
        for($i=0;$i<count($rsSalesReturn);$i++) {
            $arrayToJs = $salesCarServiceReturn->changeStatus($rsSalesReturn[$i]['pkey'],4,'',false,true);
            if (!$arrayToJs[0]['valid'])
                throw new Exception('<strong>'.$rsHeader[0]['code'] . '</strong>. '.  $arrayToJs[0]['message']);    
        }
        
        
        $purchaseOrder = new PurchaseOrder();
        $rsPO= $purchaseOrder->searchData('','',true,' and '.$purchaseOrder->tableName.'.refservicekey = '.$this->oDbCon->paramString($id).' and ('.$purchaseOrder->tableName.'.statuskey = 1)');
        for($i=0;$i<count($rsPO);$i++) {
            $arrayToJs = $purchaseOrder->changeStatus($rsPO[$i]['pkey'],4,'',false,true);
            if (!$arrayToJs[0]['valid'])
                throw new Exception('<strong>'.$rsHeader[0]['code'] . '</strong>. '.  $arrayToJs[0]['message']);    
        }
        /*
        $salesDelivery = new SalesDelivery();
        $rsSalesDelivery = $salesDelivery->searchData('','',true,' and refkey = '.$this->oDbCon->paramString($id).' and '.$salesDelivery->tableName.'.statuskey = 1');
        for($i=0;$i<count($rsSalesDelivery);$i++) {
            $arrayToJs = $salesDelivery->changeStatus($rsSalesDelivery[$i]['pkey'],4,'',false,true);
            if (!$arrayToJs[0]['valid'])
                throw new Exception('<strong>'.$rsHeader[0]['code'] . '</strong>. '.  $arrayToJs[0]['message']);    
        }
        */

        if ($copy)
            $this->copyDataOnCancel($id);	  

        $this->cancelGLByRefkey($rsHeader[0]['pkey'],$this->tableName);

    }  

    function validateCancel($rsHeader,$autoChangeStatus=false){ 
       
        $id = $rsHeader[0]['pkey'];
        
        //cek apakah sudah ad penerimaan PO
        /*
        if (!$rsHeader[0]['isfulldeliver']) {
            $salesDelivery = new SalesDelivery();
            $rsSalesDelivery = $salesDelivery->searchData('','',true,' and '.$salesDelivery->tableName.'.refkey = '.$this->oDbCon->paramString($id).' and ('.$salesDelivery->tableName.'.statuskey =2 or '.$salesDelivery->tableName.'.statuskey = 3)');

            if (!empty($rsSalesDelivery))
                 $this->addErrorList($arrayToJs,false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' . $this->errorMsg[201].' ' .$this->errorMsg['salesOrder'][2]);
        }
        */

        //cek retur 
        $salesCarServiceReturn = new SalesCarServiceReturn();
        $rsSalesReturn = $salesCarServiceReturn->searchData('','',true,' and '.$salesCarServiceReturn->tableName.'.refkey = '.$this->oDbCon->paramString($id).' and ('.$salesCarServiceReturn->tableName.'.statuskey in (2,3))');
        if(!empty($rsSalesReturn)){  
            $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' . $this->errorMsg[201].' <strong>'.$rsSalesReturn[0]['code'].'</strong>,' .$this->errorMsg['salesOrder'][3]);
        }
        
        $purchaseOrder = new PurchaseOrder();
        $rsPO= $purchaseOrder->searchData('','',true,' and '.$purchaseOrder->tableName.'.refservicekey = '.$this->oDbCon->paramString($id).' and ('.$purchaseOrder->tableName.'.statuskey in (2,3))');
        if(!empty($rsPO)){  
            $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' . $this->errorMsg[201].' <strong>'.$rsPO[0]['code'].'</strong>,' .$this->errorMsg['purchaseOrder'][2]);
        }

        //cek ad AR terbayar 
        $ar = new AR();
        $rsARKey = $ar->getTableKeyAndObj($this->tableName); 
        $rsAR = $ar->searchData('','',true,' and reftabletype = '.$this->oDbCon->paramString($rsARKey['key']).' and refkey = '.$this->oDbCon->paramString($id).' and ('.$ar->tableName.'.statuskey in (2,3))');
        if(!empty($rsAR)){  
            $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' . $this->errorMsg[201].' ' .$this->errorMsg['ar'][2]);
        } 

        //cek ad AP Commission terbayar 
        /*$ap = new APCommission();
        $rsAPKey = $ap->getTableKeyAndObj($this->tableName);  
        $rsAP = $ap->searchData('','',true,' and reftabletype = '.$this->oDbCon->paramString($rsAPKey['key']).' and refheaderkey = '.$this->oDbCon->paramString($id).' and ('.$ap->tableName.'.statuskey in (2,3))');
        if(!empty($rsAP)){  
            $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' . $this->errorMsg[201].' ' .$this->errorMsg['apCommission'][2]);
        }  */
     } 
 
        function getCarMaintenanceHistory($carkey='',$criteria=''){ 

           $sql = 'select
                    '.$this->tableName .'.code as salescode, 
                    '.$this->tableName .'.trdate as salesdate, 
                    '.$this->tableCar .'.policenumber, 
                    '.$this->tableCustomer .'.name as customername, 
                    '.$this->tableNameDetail .'.qty, 
                    '.$this->tableItem.'.name as itemname, 
                    '.$this->tableItem.'.code as itemcode, 
                    '.$this->tableItem.'.itemtype,
                    '.$this->tableBrand.'.name as brandname ,
                    '.$this->tableItem.'.deftransunitkey,
                    '.$this->tableItemUnit.'.name as unitname 
                  from
                    '.$this->tableName .',
                    '.$this->tableCar.',
                    '.$this->tableNameDetail .',
                    '.$this->tableCustomer .',
                    '.$this->tableItemUnit.', 
                    '.$this->tableItem.'
                        left join '.$this->tableBrand.' on 	' . $this->tableItem .'.brandkey = '.$this->tableBrand.'.pkey 

                  where
                    '.$this->tableNameDetail .'.itemkey = '.$this->tableItem.'.pkey and
                    '.$this->tableName .'.pkey = '.$this->tableNameDetail.'.refkey and
                    '.$this->tableName .'.carkey = '.$this->tableCar.'.pkey and
                    '.$this->tableName .'.customerkey = '.$this->tableCustomer.'.pkey and
                    '.$this->tableNameDetail.'.unitkey = '.$this->tableItemUnit.'.pkey and
                    '.$this->tableName .'.statuskey in(2,3)';
            
            
            if (!empty($carkey))
                  $sql .= ' and '.$this->tableCar.'.pkey = '. $this->oDbCon->paramString($carkey);
            
            if (!empty($criteria))
                  $sql .= $criteria;
            
            

            $sql .= $criteria;

            return $this->oDbCon->doQuery($sql);

        }
    }
?>
