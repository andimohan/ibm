<?php
  
class SalesOrderRental extends BaseClass{ 
  
    function __construct(){

            parent::__construct();

            $this->tableName = 'sales_order_rental_header';
            $this->tableNameDetail = 'sales_order_rental_detail';
            $this->tableDeliveryDetail = 'sales_order_rental_delivery_detail';
            $this->tableCustomer = 'customer';
            $this->tableLocation = 'location';
            $this->tableCity = 'city';
            $this->tableEmployee = 'employee';
            $this->tableWarehouse = 'warehouse'; 
            $this->tableStatus = 'transaction_status';
            $this->tableMovement = 'item_movement'; 
            $this->tableHistory = 'history';
            $this->tablePayment = 'sales_order_rental_payment'; 	
            $this->tableItem = 'item'; 	
            $this->tableItemCategory = 'item_category'; 	
            $this->tableItemUnit = 'item_unit'; 		
            $this->tableTimeUnit = 'time_unit';
            $this->tableBrand = 'brand'; 	
            $this->tableItemCategory = 'item_category'; 	
            $this->tableCartTemp = 'cart_temp'; 
            $this->tablePaymentConfirmation = 'payment_confirmation'; 
            $this->tableQuotation = 'sales_rental_quotation_header'; 
            $this->isTransaction = true; 		

            $this->autoPrintURL = 'print/salesOrder';
         
            $this->securityObject = 'SalesOrderRental';   
	        $this->overwriteContractSecurityObject = 'overwriteContract';

            $this->arrLinkedTable = array(); 
            $defaultFieldName = 'refkey';
            array_push($this->arrLinkedTable, array('table'=>'sales_delivery_header','field'=>$defaultFieldName));  
            array_push($this->arrLinkedTable, array('table'=>'ar','field'=>$defaultFieldName));  

            $this->arrDataDetail = array();  
            $this->arrDataDetail['pkey'] = array('hidDetailKey');
            $this->arrDataDetail['refkey'] = array('pkey','ref');
            $this->arrDataDetail['itemkey'] = array('hidItemKey'); 
            $this->arrDataDetail['qty'] = array('qty','number'); 
            $this->arrDataDetail['qtyinbaseunit'] = array('qtyInBaseUnit','number');
            $this->arrDataDetail['unitkey'] = array('selUnit');
            $this->arrDataDetail['timeunitkey'] = array('selTimeUnit');
            $this->arrDataDetail['priceinunit'] = array('priceInUnit','number'); 
            $this->arrDataDetail['totaldays'] = array('totalDays','number'); 
            $this->arrDataDetail['priceinbaseunit'] = array('priceInBaseUnit','number'); 
            $this->arrDataDetail['unitconvmultiplier'] = array('unitConvMultiplier','number');
            $this->arrDataDetail['total'] = array('detailSubtotal','number');
            $this->arrDataDetail['deliveredqtyinbaseunit'] = array('deliveredQtyInBaseUnit','number');
            $this->arrDataDetail['itemtype'] = array('itemType');

            $arrDetails = array();
            array_push($arrDetails, array('dataset' => $this->arrDataDetail));

            $this->arrData = array(); 
            $this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails)); 
            $this->arrData['code'] = array('code'); 
            $this->arrData['trdate'] = array('trDate','date');
            $this->arrData['warehousekey'] = array('selWarehouseKey');
            $this->arrData['refkey'] = array('hidSalesQuotationKey');  
            $this->arrData['customerkey'] = array('hidCustomerKey');  
            $this->arrData['trdesc'] = array('trDesc'); 
            $this->arrData['subtotal'] = array('subtotal','number'); 
            $this->arrData['grandtotal'] = array('grandtotal','number'); 
            $this->arrData['statuskey'] = array('selStatus');
            $this->arrData['saleskey'] = array('hidSalesKey');
            $this->arrData['locationkey'] = array('hidLocationKey');
            $this->arrData['recipientname'] = array('recipientName');
            $this->arrData['recipientphone'] = array('recipientPhone');
            $this->arrData['recipientemail'] = array('recipientEmail');
            $this->arrData['recipientaddress'] = array('recipientAddress');
            $this->arrData['recipientcitykey'] = array('hidRecipientCityKey');     
            $this->arrData['isunlimited'] = array('chkIsUnlimited');      
			$this->arrData['termdetail'] = array('termDetail','raw');
          
            $this->arrDataListAvailableColumn = array(); 
            array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 100));
            array_push($this->arrDataListAvailableColumn, array('code' => 'date','title' => 'date','dbfield' => 'trdate','default'=>true, 'width' => 100, 'align'=>'center', 'format' => 'date'));
            array_push($this->arrDataListAvailableColumn, array('code' => 'warehouse','title' => 'warehouse','dbfield' => 'warehousename','default'=>true, 'width' => 100));
            array_push($this->arrDataListAvailableColumn, array('code' => 'refcode','title' => 'refCode','dbfield' => 'refcode','default'=>true, 'width' => 100));
            array_push($this->arrDataListAvailableColumn, array('code' => 'customer','title' => 'customer','dbfield' => 'customername','default'=>true, 'width' => 200));
            array_push($this->arrDataListAvailableColumn, array('code' => 'total','title' => 'total','dbfield' => 'grandtotal','default'=>true, 'width' => 100, 'align' => 'right', 'format'=>'number'));
            array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
            array_push($this->arrDataListAvailableColumn, array('code' => 'desc','title' => 'note','dbfield' => 'trdesc', 'width' => 200));
            array_push($this->arrDataListAvailableColumn, array('code' => 'salesman','title' => 'salesman','dbfield' => 'salesname', 'width' => 150));
            array_push($this->arrDataListAvailableColumn, array('code' => 'location','title' => 'location','dbfield' => 'locationname', 'width' => 150));
       
			$this->includeClassDependencies(array(
                   'Warehouse.class.php',  
                   'City.class.php', 
                   'Customer.class.php', 
                   'Item.class.php', 
                   'Service.class.php', 
				   'SalesRentalQuotation.class.php',
				   'SalesOrderRentalInvoice.class.php',
				   'SalesOrderRentalWorkOrder.class.php',
                   'ItemMovement.class.php',  
                   'ItemUnit.class.php',
                   'TimeUnit.class.php',
                   'Location.class.php',
                   'GeneralJournal.class.php',
                   'Employee.class.php' 
            )); 

            array_push($this->filterCriteria, array('title' => $this->lang['warehouse'], 'field' => 'warehousekey'));
        
    }
 
            
    
    function getQuery(){

        $sql = '
            SELECT '.$this->tableName.'.* ,
               '.$this->tableCustomer.'.name as customername,
               '.$this->tableWarehouse.'.name as warehousename,
               '.$this->tableStatus.'.status as statusname ,
               '.$this->tableQuotation.'.code as refcode ,

               '.$this->tableLocation.'.name as locationname ,
               '.$this->tableCity.'.name as cityname ,
               '.$this->tableEmployee.'.name as salesname 
            FROM 
                '.$this->tableStatus.', 
                '.$this->tableCustomer.' left join '.$this->tableCity.' on  
                     '.$this->tableCustomer.'.citykey = '.$this->tableCity.'.pkey,
                '.$this->tableWarehouse.',
                '.$this->tableName.' 
					left join '.$this->tableQuotation.' on  '.$this->tableName.'.refkey = '.$this->tableQuotation.'.pkey 
					left join '.$this->tableEmployee.' on  '.$this->tableName.'.saleskey = '.$this->tableEmployee.'.pkey 
                    left join '.$this->tableLocation.' on '.$this->tableName.'.locationkey = '.$this->tableLocation.'.pkey 
            WHERE '.$this->tableName.'.customerkey = '.$this->tableCustomer.'.pkey and
                     '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey and
                     '.$this->tableName.'.warehousekey = '.$this->tableWarehouse.'.pkey 
        ' .$this->criteria ; 

//        $this->setLog($sql,true);
        $sql .=  $this->getWarehouseCriteria() ;
        $sql .=  $this->getCompanyCriteria() ;

        return $sql;
    }  
 
      
    function editData($arrParam){ 
        // kalo edit, marketplacenya jgn diupdate, kalo gk jd 0
        
		unset($this->arrData['marketplacekey']);  
        return parent::editData($arrParam);
	}
    
    
    function afterAddDataOnCopy($pkey, $oldkey){
        $sql = 'update ' .$this->tableNameDetail.' set deliveredqtyinbaseunit = 0 where refkey = ' . $this->oDbCon->paramString($pkey);    
        $this->oDbCon->execute($sql); 
    }

    function reCountSubtotal($arrParam){
  
            $subtotal = 0 ;
          
            $arrItemKey = $arrParam['hidItemKey'];
 
            $arrQty = $arrParam['qty']; 
            $arrPriceinunit = $arrParam['priceInUnit']; 
            $arrTotalDays = $arrParam['totalDays']; 
            $arrTransUnitKey = $arrParam['selUnit']; 
            $arrTimeUnitKey = $arrParam['selTimeUnit']; 

            $arrItemDetail = array();
            $item = new Item();
        
            for ($i=0;$i<count($arrItemKey);$i++){

                if (empty($arrItemKey[$i]) || empty($arrTimeUnitKey[$i]))   continue; 

                    $rsItem = $item->getDataRowById($arrItemKey[$i]);
 
                    $itemkey = $arrItemKey[$i];
                    $transactionUnitKey = $arrTransUnitKey[$i];
                    $baseunitkey = $rsItem[0]['baseunitkey']; 
                    $qty =  $this->unFormatNumber($arrQty[$i]);
                    $conversionMultiplier = $item->getConvMultiplier($itemkey,$transactionUnitKey,$baseunitkey); 
                    $qtyinbaseunit = $qty * $conversionMultiplier;
                    $priceInUnit = $this->unFormatNumber($arrPriceinunit[$i]);
                    $totalDays = $this->unFormatNumber($arrTotalDays[$i]);
 
                    $arrItemDetail[$i]['baseUnitKey'] = $baseunitkey;
                    $arrItemDetail[$i]['unitConvMultiplier'] = $conversionMultiplier;
                    $arrItemDetail[$i]['qtyInBaseUnit'] = $qtyinbaseunit ; 
                    $arrItemDetail[$i]['priceInBaseUnit'] = $priceInUnit / $conversionMultiplier ;
   
                    $detailSubtotal = $qty * $priceInUnit * $totalDays;
                    $arrItemDetail[$i]['detailSubtotal'] = $detailSubtotal; 
				    $arrItemDetail[$i]['itemType'] = $rsItem[0]['itemtype']; 

                    $subtotal += $detailSubtotal ; 
                
            } 

            $grandtotal = $subtotal;
       
 
            $reCountResult = array(); 
            $reCountResult['subtotal'] = $grandtotal;  
            $reCountResult['grandtotal'] = $grandtotal;  
            $reCountResult['detailCOGS'] = $arrItemDetail; 

            return $reCountResult;

    } 
   

    function validateForm($arr,$pkey = ''){
            $salesRentalQuotation = new SalesRentalQuotation();   
			$security = new Security();

            $arrayToJs = parent::validateForm($arr,$pkey); 
			$overwriteContractAllowed = $security->isAdminLogin($this->overwriteContractSecurityObject,10);

            $customerkey = $arr['hidCustomerKey'];  
            $arrItemkey = $arr['hidItemKey']; 
            $arrQty = $arr['qty']; 
            $arrQtyInBaseUnit = $arr['qtyInBaseUnit']; 
            $arrPriceinunit = $arr['priceInUnit'];
            $arrTotalDays = $arr['totalDays'];
            $email = $arr['recipientEmail'];
            $arrSelUnit = $arr['selUnit']; 
            $quotationkey = $arr['hidSalesQuotationKey']; 
		    $item = new Item();   
 
 

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

            // kalo pake quotation
            if(!$overwriteContractAllowed){
				if(empty($quotationkey)){
					$this->addErrorList($arrayToJs,false,$this->errorMsg['salesRentalQuotation'][1]);
				}else{
					$rsQuotation = $salesRentalQuotation->getDataRowById($quotationkey);
					if($customerkey <> $rsQuotation[0]['customerkey']) 
						$this->addErrorList($arrayToJs,false,$this->errorMsg['salesOrderRental'][2]); 

					$rsQuotationDetail = $salesRentalQuotation->getDetailWithRelatedInformation($rsQuotation[0]['pkey']);
					$arrQuotationItemKey = array_column($rsQuotationDetail, 'itemkey');
					$arrQuotationItemQty = array_column($rsQuotationDetail,'qtyinbaseunit','itemkey');
					for($i=0;$i<count($arrItemkey);$i++){

						$rsItem = $item->getDataRowById($arrItemkey[$i]); 

						if($arrQtyInBaseUnit[$i]>$arrQuotationItemQty[$arrItemkey[$i]]){ 
							$this->addErrorList($arrayToJs,false, $rsItem[0]['name'].'. '.$this->errorMsg['salesOrderRental'][3]); 
						}

						if(!in_array($arrItemkey[$i],$arrQuotationItemKey)){
							$this->addErrorList($arrayToJs,false, $rsItem[0]['name'].'. '.$this->errorMsg['salesOrderRental'][3]);
						}
					}
				}
            }

     

            if(!empty($email)){
                if(!filter_var($email, FILTER_VALIDATE_EMAIL)) 
                    $this->addErrorList($arrayToJs,false,$this->errorMsg['email'][3]); 
            }  

            if(empty($arrItemkey)) 
                 $this->addErrorList($arrayToJs,false,  $this->errorMsg[501]);  


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
                    
                    if ($this->unFormatNumber($arrTotalDays[$i]) <= 0){ 
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
				
				if (in_array($arrItemkey[$i],$arrDetailKeys)){  
					$rsItem = $item->getDataRowById($arrItemkey[$i]);
					$this->addErrorList($arrayToJs,false, $rsItem[0]['name'].'. '.$this->errorMsg[215]); 	 
				}else{ 
					array_push($arrDetailKeys, $arrItemkey[$i]);
				} 
                

            }



            return $arrayToJs;
    }
 

    function validateConfirm($rsHeader){
        $warehouse = new Warehouse();    
        $item = new Item(); 
 
        $id = $rsHeader[0]['pkey']; 
        $customerkey = $rsHeader[0]['customerkey']; 
        $quotationkey = $rsHeader[0]['refkey'];  
        
        $rsDetail = $this->getDetailById($id);
        $rsPayment = $this->getPaymentMethodDetail($id); 
  
         // kalo pake quotation
        /*if(empty($quotationkey)){
            $this->addErrorList($arrayToJs,false,$this->errorMsg['salesRentalQuotation'][1]);
        }else{

            $salesRentalQuotation = new SalesRentalQuotation();
            $rsQuotation = $salesRentalQuotation->searchData('','',true,' and '.$salesRentalQuotation->tableName.'.statuskey in (2,3) and '.$salesRentalQuotation->tableName.'.pkey = '.$this->oDbCon->paramString($quotationkey));
            if(empty($rsQuotation)){ 
                $this->addErrorList($arrayToJs,false, $this->errorMsg[213]);
            }else{
                if($customerkey <> $rsQuotation[0]['customerkey']) 
                    $this->addErrorList($arrayToJs,false,$this->errorMsg['salesOrderRental'][2]); 

                $rsQuotationDetail = $salesRentalQuotation->getDetailWithRelatedInformation($rsQuotation[0]['pkey']);
                $arrQuotationItemKey = array_column($rsQuotationDetail, 'itemkey');
                $arrQuotationItemQty = array_column($rsQuotationDetail,'qtyinbaseunit','itemkey');
                for($i=0;$i<count($rsDetail);$i++){

                    $rsItem = $item->getDataRowById($rsDetail[$i]['itemkey']); 

                    if($rsDetail[$i][''>$arrQuotationItemQty[$rsDetail[$i]['itemkey']]){ 
                        $this->addErrorList($arrayToJs,false, $rsItem[0]['name'].'. '.$this->errorMsg['salesOrderRental'][3]); 
                    }

                    if(!in_array($rsDetail[$i]['itemkey'],$arrQuotationItemKey)){
                        $this->addErrorList($arrayToJs,false, $rsItem[0]['name'].'. '.$this->errorMsg['salesOrderRental'][3]);
                    }

                }
            }

        } */

    }
  

    function confirmTrans($rsHeader){  
        
        $id = $rsHeader[0]['pkey']; 
       
        $item = new Item(); 
        $itemMovement = new ItemMovement(); 
        $rsSalesDetail = $this->getDetailWithRelatedInformation($id);
		$isUnlimited = $this->loadSetting('useLumpSum'); 
        for($i=0;$i<count($rsSalesDetail);$i++){
            if(empty($rsSalesDetail[$i]['itemkey']) || $rsSalesDetail[$i]['qtyinbaseunit'] < 1)
                continue;
            
			$rsItem = $item->getDataRowById($rsSalesDetail[$i]['itemkey']);
			if(!empty($rsItem) && $rsItem[0]['itemtype']==ITEM)
            $itemMovement->updateQORRental($rsSalesDetail[$i]['itemkey'],$rsHeader[0]['warehousekey'],$rsSalesDetail[$i]['qtyinbaseunit']);  
            
        }         

        
        //update jurnal umum 
        //$this->updateGL($rsHeader);
         

    } 


    function validateCancel($rsHeader,$autoChangeStatus=false){ 
        $id = $rsHeader[0]['pkey'];
 
    	$salesOrderRentalWorkOrder = new SalesOrderRentalWorkOrder();
        $rsWorkOrder = $salesOrderRentalWorkOrder->searchData('','',true,' and '.$salesOrderRentalWorkOrder->tableName.'.refkey = '.$this->oDbCon->paramString($id).' and ('.$salesOrderRentalWorkOrder->tableName.'.statuskey  in (2,3))');
        if(!empty($rsWorkOrder)) 
            $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' . $this->errorMsg[201].' <strong>'.$rsWorkOrder[0]['code'].'</strong>. ' .$this->errorMsg[203]);
        
        $salesOrderRentalInvoice = new SalesOrderRentalInvoice();
        $rsInvoice = $salesOrderRentalInvoice->searchData('','',true,' and '.$salesOrderRentalInvoice->tableName.'.refkey = '.$this->oDbCon->paramString($id).' and ('.$salesOrderRentalInvoice->tableName.'.statuskey  in (2,3))');
        if(!empty($rsInvoice)) 
            $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' . $this->errorMsg[201].' <strong>'.$rsInvoice[0]['code'].'</strong>. ' .$this->errorMsg[203]);
 
    } 



    function cancelTrans($rsHeader,$copy){ 
        $id = $rsHeader[0]['pkey']; 
         

       $itemMovement = new ItemMovement();  
       $rsDetail = $this->getDetailWithRelatedInformation($id); 
 
//        for($i=0;$i<count($rsDetail);$i++)
//			$itemMovement->updateQORRental($rsDetail[$i]['itemkey'],$rsHeader[0]['warehousekey'],-$rsDetail[$i]['qtyinbaseunit']);
   
            
        $salesOrderRentalWorkOrder = new SalesOrderRentalWorkOrder();
		$rsWorkOrder = $salesOrderRentalWorkOrder->searchData('','',true,' and '.$salesOrderRentalWorkOrder->tableName.'.refkey = '.$this->oDbCon->paramString($rsHeader[0]['pkey']).' and '.$salesOrderRentalWorkOrder->tableName.'.statuskey = 1');
		for($i=0;$i<count($rsWorkOrder);$i++) 
          $salesOrderRentalWorkOrder->changeStatus($rsWorkOrder[$i]['pkey'],4,'',false,true); 
        
        $salesOrderRentalInvoice = new SalesOrderRentalInvoice();
		$rsInvoice = $salesOrderRentalInvoice->searchData('','',true,' and '.$salesOrderRentalInvoice->tableName.'.refkey = '.$this->oDbCon->paramString($rsHeader[0]['pkey']).' and '.$salesOrderRentalInvoice->tableName.'.statuskey = 1');
		for($i=0;$i<count($rsInvoice);$i++) 
          $salesOrderRentalInvoice->changeStatus($rsInvoice[$i]['pkey'],4,'',false,true); 
        if ($copy)
            $this->copyDataOnCancel($id);	  

        $this->cancelGLByRefkey($rsHeader[0]['pkey'],$this->tableName);

    }  
 

    function updateGL($rs){
       
    }
    
    
    function updateUserCartSession(){
       
    }

    function addToCartSession($arr){   	 
 
    } 

    function addToTemporaryCart($itemkey,$qty){
 
    }

    function clearTemporaryCart(){
    
    }

    function getDetailWithRelatedInformation($pkey,$criteria=''){
        
      $sql = 'select
            '.$this->tableNameDetail.'.*,
            ('.$this->tableNameDetail.'.qtyinbaseunit - '.$this->tableNameDetail.'.deliveredqtyinbaseunit) as outstanding,
			concat(round('.$this->tableNameDetail.'.qty)," x ", '.$this->tableItem.'.name) as label,
            '.$this->tableItem.'.name as itemname,
            '.$this->tableItem.'.code as itemcode,
            '.$this->tableItem.'.baseunitkey,
            '.$this->tableTimeUnit.'.name as timename,
            '.$this->tableTimeUnit.'.minimaltime,
            '.$this->tableItemUnit.'.name as unitname,
            baseunit.name as baseunitname
        from
            '.$this->tableNameDetail.'
				left join '.$this->tableItemUnit.' on '.$this->tableNameDetail.'.unitkey = '.$this->tableItemUnit.'.pkey,
            '.$this->tableItem.'
				left join '.$this->tableItemUnit.' baseunit on '.$this->tableItem.'.baseunitkey = baseunit.pkey,
            '.$this->tableTimeUnit.'
        where  
            '.$this->tableNameDetail .'.itemkey = '.$this->tableItem.'.pkey and
            '.$this->tableNameDetail.'.timeunitkey = '.$this->tableTimeUnit.'.pkey and
            '. $this->tableNameDetail.'.refkey in  ('.$this->oDbCon->paramString($pkey,',') . ') ' ;
 
        $sql .= $criteria;
  
        return $this->oDbCon->doQuery($sql);
    }
    
    function getDeliveryDetail($pkey,$criteria=''){
        
      $sql = 'select
            '.$this->tableDeliveryDetail.'.*,
            '.$this->tableNameDetail.'.priceinunit,
            '.$this->tableNameDetail.'.timeunitkey,
            '.$this->tableItem.'.name as itemname,
            '.$this->tableItem.'.code as itemcode,
            '.$this->tableTimeUnit.'.name as timename,
            '.$this->tableItemUnit.'.name as unitname,
            baseunit.name as baseunitname
        from
            '.$this->tableDeliveryDetail.',
            '.$this->tableNameDetail.',
            '.$this->tableItem.',
            '.$this->tableTimeUnit.',
            '.$this->tableItemUnit.',
            '.$this->tableItemUnit.' baseunit
        where  
            '.$this->tableDeliveryDetail .'.refsodetailkey = '.$this->tableNameDetail.'.pkey and
            '.$this->tableDeliveryDetail.'.itemkey = '.$this->tableItem.'.pkey and
            '.$this->tableDeliveryDetail.'.unitkey = '.$this->tableItemUnit.'.pkey and
            '.$this->tableNameDetail.'.timeunitkey = '.$this->tableTimeUnit.'.pkey and
            '.$this->tableItem.'.baseunitkey = baseunit.pkey
            and '. $this->tableDeliveryDetail.'.refkey = '.$this->oDbCon->paramString($pkey);
 
        $sql .= $criteria;
        return $this->oDbCon->doQuery($sql);
    }
    
    function getDeliverySchedule($pkey='',$criteria='',$orderby=''){
        
      $sql = 'select
            '.$this->tableDeliveryDetail.'.*,
            '.$this->tableNameDetail.'.priceinunit,
            '.$this->tableNameDetail.'.timeunitkey,
            '.$this->tableName.'.code as socode,
            '.$this->tableItem.'.name as itemname,
            '.$this->tableItem.'.code as itemcode,
            '.$this->tableTimeUnit.'.name as timename,
            '.$this->tableItemUnit.'.name as unitname,
            baseunit.name as baseunitname
        from
            '.$this->tableDeliveryDetail.',
            '.$this->tableNameDetail.',
            '.$this->tableName.',
            '.$this->tableItem.',
            '.$this->tableTimeUnit.',
            '.$this->tableItemUnit.',
            '.$this->tableItemUnit.' baseunit
        where  
            '.$this->tableDeliveryDetail .'.refsodetailkey = '.$this->tableNameDetail.'.pkey and
            '.$this->tableNameDetail .'.refkey = '.$this->tableName.'.pkey and
            '.$this->tableDeliveryDetail.'.itemkey = '.$this->tableItem.'.pkey and
            '.$this->tableDeliveryDetail.'.unitkey = '.$this->tableItemUnit.'.pkey and
            '.$this->tableNameDetail.'.timeunitkey = '.$this->tableTimeUnit.'.pkey and
            '.$this->tableItem.'.baseunitkey = baseunit.pkey';
        
        if(!empty($pkey))
            $sql .=' and '. $this->tableDeliveryDetail.'.refkey = '.$this->oDbCon->paramString($pkey).' ' ;
        $sql .= $criteria;
        if(!empty($orderby))
            $sql .= $orderby;
        
        //$this->setLog($sql,true);
        return $this->oDbCon->doQuery($sql);
    }

    function searchDataForAutoComplete($fieldname='',$searchkey='',$mustmatch=false,$searchCriteria='',$orderCriteria='', $limit=''){

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
    } 
 
    function generateShipmentTracking($pkey){ 
     
    }
 
    function generateInvoice($pkey){   
        $rsHeader = $this->getDataRowById($pkey);   

        $file=  HTTP_HOST . 'invoice/'.$pkey.'/'.md5($pkey . $rsHeader[0]['grandtotal'] . $this->secretKey).'/1';   
        $invoice =  file_get_contents($file);
        
        return $invoice;
    }
        
     function normalizeParameter($arrParam, $trim = false){ 
         
            $arrParam = parent::normalizeParameter($arrParam); 

            $arrParam['recipientName'] = (empty($arrParam['recipientName'])) ? '' : $arrParam['recipientName'];
            $arrParam['recipientPhone'] = (empty($arrParam['recipientPhone'])) ? '' : $arrParam['recipientPhone'];
            $arrParam['recipientEmail'] = (empty($arrParam['recipientEmail'])) ? '' : $arrParam['recipientEmail'];
            $arrParam['recipientAddress'] = (empty($arrParam['recipientAddress'])) ? '' : $arrParam['recipientAddress'];
            $arrParam['selSalesKey'] = (isset($arrParam['selSalesKey'])) ? $arrParam['selSalesKey'] : 0; 
            $arrParam['hidSalesKey'] = (isset($arrParam['hidSalesKey'])) ? $arrParam['hidSalesKey'] : 0;
         
         
            $arrItemkey = $arrParam['hidItemKey'];
            $arrQty = $arrParam['qty']; 
            $arrPriceinunit = $arrParam['priceInUnit'];   
            $arrTotalDays = $arrParam['totalDays'];   
            $arrUnitKey = $arrParam['selUnit']; 
  			$security = new Security();
		 	$overwriteContractAllowed = $security->isAdminLogin($this->overwriteContractSecurityObject,10);
		 	if(!$overwriteContractAllowed){
				
				$arrParam['hidDetailKey'] = array();
				$arrParam['hidItemKey'] = array();
				$arrParam['qty'] = array();
				$arrParam['qtyInBaseUnit'] = array();
				$arrParam['selUnit'] = array();
				$arrParam['selTimeUnit'] = array();
				$arrParam['priceInUnit'] = array();
				$arrParam['totalDays'] = array();
				$arrParam['unitConvMultiplier'] = array();
				$arrParam['priceInBaseUnit'] = array();
				$arrParam['detailSubtotal'] = array();
				$arrParam['itemType'] = array();
				
				$salesRentalQuotation = new SalesRentalQuotation(); 
		 		$quotationkey = $arrParam['hidSalesQuotationKey']; 
		 		$rsQuotation = $salesRentalQuotation->getDataRowById($quotationkey);
				$rsQuotationDetail = $salesRentalQuotation->getDetailWithRelatedInformation($rsQuotation[0]['pkey']); 
				for($i=0;$i<count($rsQuotationDetail);$i++){
					$arrParam['hidDetailKey'][$i] = '';
					$arrParam['hidItemKey'][$i] = $rsQuotationDetail[$i]['itemkey'];
					$arrParam['qty'][$i] = $this->formatNumber($rsQuotationDetail[$i]['qty']);
					$arrParam['selUnit'][$i] = $rsQuotationDetail[$i]['unitkey'];
					$arrParam['selTimeUnit'][$i] = $rsQuotationDetail[$i]['timeunitkey'];
					$arrParam['priceInUnit'][$i] = $this->formatNumber($rsQuotationDetail[$i]['priceinunit']);
					$arrParam['totalDays'][$i] = $this->formatNumber($rsQuotationDetail[$i]['totaldays']);
					$arrParam['detailSubtotal'][$i] = $rsQuotationDetail[$i]['total'];
					
				}
			}
		 	
		 	
				$reCountResult = $this->reCountSubtotal($arrParam); 
				$arrParam['detailCOGS'] = $reCountResult['detailCOGS']; 
				$arrParam['grandtotal'] = $reCountResult['grandtotal']; 

				 for ($i=0;$i<count($arrItemkey);$i++){ 

					$qtyinbaseunit = $arrParam['detailCOGS'][$i]['qtyInBaseUnit'];  
					$arrParam['qtyInBaseUnit'][$i] = $qtyinbaseunit;
					$arrParam['unitConvMultiplier'][$i] = $arrParam['detailCOGS'][$i]['unitConvMultiplier']; 
					$arrParam['priceInBaseUnit'][$i] = $arrParam['detailCOGS'][$i]['priceInBaseUnit']; 
					$arrParam['detailSubtotal'][$i] = $arrParam['detailCOGS'][$i]['detailSubtotal'];
					$arrParam['itemType'][$i] = $arrParam['detailCOGS'][$i]['itemType'];
					$arrParam['deliveredQtyInBaseUnit'][$i] = 0;

				}
			
       
        return $arrParam;
    }
     
    function deleteDetailDelivery($wokey){
        $salesOrderRentalWorkOrder = new SalesOrderRentalWorkOrder();
        $rsWO = $salesOrderRentalWorkOrder->getDataRowById($wokey);
        if(!empty($rsWO)){
            $sql = 'delete from '.$this->tableDeliveryDetail.' where refwokey = '. $this->oDbCon->paramString($wokey);
            $this->oDbCon->execute($sql);
        }
    }
    
    function updateDetailDelivery($wokey){
        $salesOrderRentalWorkOrder = new SalesOrderRentalWorkOrder();
        $rsWO = $salesOrderRentalWorkOrder->getDataRowById($wokey);
        if(!empty($rsWO)){
            $this->deleteDetailDelivery($wokey);
            $rsWODetail = $salesOrderRentalWorkOrder->getDetailWithRelatedInformation($wokey);
            $invoiceDate = $this->formatDBDate($rsWO[0]['invoicedate'],'d / m / Y ');
            $startDate = strtotime($rsWO[0]['invoicedate']);
            $format = 'days';
            for($i=0;$i<count($rsWODetail);$i++){
                $rsSODetail = $this->getDetailByColumn('pkey',$rsWODetail[$i]['refsodetailkey']);
                $totalDays = floor($rsSODetail[0]['totaldays']);
                $endDate = date('Y-m-d', strtotime(($totalDays-1).' '.$format, $startDate));
                $endDate = $this->formatDBDate($endDate,'d / m / Y ');
                $sql = 'insert into '.$this->tableDeliveryDetail.' (
                        refkey,
                        refsodetailkey,
                        refwokey,
                        refwodetailkey,
                        itemkey,
                        qty,  
                        unitkey,
                        baseunitkey,
                        unitconvmultiplier, 
                        qtyinbaseunit,
                        trdate,
                        enddate
                     ) values ( 
                        '.$this->oDbCon->paramString($rsWO[0]['refkey']).',
                        '.$this->oDbCon->paramString($rsWODetail[$i]['refsodetailkey']).',
                        '.$this->oDbCon->paramString($rsWO[0]['pkey']).',
                        '.$this->oDbCon->paramString($rsWODetail[$i]['pkey']).',
                        '.$this->oDbCon->paramString($rsWODetail[$i]['itemkey']).',
                        '.$this->oDbCon->paramString($this->unFormatNumber($rsWODetail[$i]['qty'])).',
                        '.$this->oDbCon->paramString($rsWODetail[$i]['unitkey']).',
                        '.$this->oDbCon->paramString($rsWODetail[$i]['baseunitkey']).',
                        '.$this->oDbCon->paramString($this->unFormatNumber($rsWODetail[$i]['unitconvmultiplier'])).',
                        '.$this->oDbCon->paramString($this->unFormatNumber($rsWODetail[$i]['qtyinbaseunit'])).',
                        '.$this->oDbCon->paramDate($invoiceDate,' / ').',
                        '.$this->oDbCon->paramDate($endDate,' / ').'
                    )';	 
                
                $this->oDbCon->execute($sql);
            }
              
        }
        
    }
    
    function updateSalesOrderDeliveredItem($pkey){ 
            $salesOrderRentalWorkOrder = new SalesOrderRentalWorkOrder(); 
            $rsHeader = $this->getDataRowById($pkey);  
            $rsDetail = $this->getDetailById($pkey); 

            for($i=0;$i<count($rsDetail); $i++){	
                $sql = 'select 
                        coalesce(sum(qtyinbaseunit),0) as totaldeliveredqtyinbaseunit
                    from 
                        '. $salesOrderRentalWorkOrder->tableName . ', '. $salesOrderRentalWorkOrder->tableNameDetail . '
                    where 
                         '. $salesOrderRentalWorkOrder->tableName . '.pkey = '. $salesOrderRentalWorkOrder->tableNameDetail . '.refkey and
                         '. $salesOrderRentalWorkOrder->tableName . '.refkey = '. $this->oDbCon->paramString($pkey) .' and
                         '. $salesOrderRentalWorkOrder->tableNameDetail . '.itemkey = ' . $rsDetail[$i]['itemkey'] .' and 
                         '. $salesOrderRentalWorkOrder->tableNameDetail . '.refsodetailkey = ' . $rsDetail[$i]['pkey'] .' and 
                         (statuskey = 2 or statuskey = 3)';
 
                $rsTotal = $this->oDbCon->doQuery($sql);

                // INI AKAN PROBLEM KALO DETAIL PUNYA 2 ITEM YG SAMA
                $sql = 'update 
                            ' . $this->tableNameDetail.' 
                        set  
                            deliveredqtyinbaseunit = '. $rsTotal[0]['totaldeliveredqtyinbaseunit'] .'
                        where 
                            refkey = '.$pkey.' and 
                            pkey = '.$rsDetail[$i]['pkey'].' and 
                            itemkey = ' . $rsDetail[$i]['itemkey'];
                 
                $this->oDbCon->execute($sql); 
            }

            //check if all item received, change PO status to finish
            $sql = 'select * from ' . $this->tableNameDetail.' where refkey = '.$this->oDbCon->paramString($pkey).' and  deliveredqtyinbaseunit < qtyinbaseunit';
            $rs = $this->oDbCon->doQuery($sql);

            $statuskey = (empty($rs)) ? 3 : 2; 
              
            if ($rsHeader[0]['statuskey'] <> $statuskey)
                $this->changeStatus($pkey,$statuskey); 
      
    }

    function getItemForInvoice($pkey,$startDate,$endDate){
        $salesOrderRentalInvoice = new SalesOrderRentalInvoice();
        $arrItemDelivery = array();
        $salesOrderRentalWorkOrder = new SalesOrderRentalWorkOrder();
        $customer = new Customer();
        $arrItemInvoice = array();
        $arrItemDelivery = array();
        $tglAwal = strtotime($startDate);
        $tglAkhir = strtotime($endDate);  
        $startDate = $this->formatDBDate($startDate,'d / m / Y');
        $endDate = $this->formatDBDate($endDate,'d / m / Y');
        $criteria = ' and '.$this->tableDeliveryDetail.'.enddate >= '.$this->oDbCon->paramDate($startDate,' / ').' and '.$this->tableDeliveryDetail.'.trdate <= '.$this->oDbCon->paramDate($endDate,' / ').' '; 
        $rsDelivery = $this->getDeliveryDetail($pkey,$criteria);
        for($i=0;$i<count($rsDelivery);$i++){
            $rsWo = $salesOrderRentalWorkOrder->getDataRowById($rsDelivery[$i]['refwokey']); 
            $date = $this->formatDBDate($rsDelivery[$i]['trdate'],'d / m / Y');
            $invoiceStartDate = strtotime($rsDelivery[$i]['trdate']);      
            $invoiceStartDate = ($invoiceStartDate<$tglAwal) ? $tglAwal:$invoiceStartDate;
                
            $invoiceEndDate = strtotime($rsDelivery[$i]['enddate']);
            $invoiceEndDate = ($invoiceEndDate>$tglAkhir) ? $tglAkhir:$invoiceEndDate;
            $daysDiff = (floor(($invoiceEndDate - $invoiceStartDate)/(60*60*24)))+1;
            if($daysDiff<1)
                continue;
            
            $rsDelivery[$i]['datediff'] = $daysDiff;
            $total = $daysDiff * $rsDelivery[$i]['qty'] * $rsDelivery[$i]['priceinunit'];
            $rsDelivery[$i]['total'] = $total;
            $rsDelivery[$i]['invoicestartdate'] = date('Y-m-d',$invoiceStartDate);
            $rsDelivery[$i]['invoiceenddate'] = date('Y-m-d',$invoiceEndDate);
        }
        
        for($i=0;$i<count($rsDelivery);$i++){

            $arrIndex = $rsDelivery[$i]['itemkey'].'_'.$rsDelivery[$i]['datediff'];
            if(!isset($arrItemDelivery[$arrIndex])){
                $arrItemDelivery[$arrIndex]['pkey'] = $rsDelivery[$i]['pkey'];
                $arrItemDelivery[$arrIndex]['itemkey'] = $rsDelivery[$i]['itemkey'];
                $arrItemDelivery[$arrIndex]['itemname'] = $rsDelivery[$i]['itemname'];
                $arrItemDelivery[$arrIndex]['qty'] = $rsDelivery[$i]['qty'];
                $arrItemDelivery[$arrIndex]['priceinunit'] = $rsDelivery[$i]['priceinunit'];
                $arrItemDelivery[$arrIndex]['datediff'] = $rsDelivery[$i]['datediff'];
                $arrItemDelivery[$arrIndex]['timeunitkey'] = $rsDelivery[$i]['timeunitkey'];
                $arrItemDelivery[$arrIndex]['unitkey'] = $rsDelivery[$i]['unitkey'];
                $arrItemDelivery[$arrIndex]['trdate'] = $rsDelivery[$i]['trdate'];
                $arrItemDelivery[$arrIndex]['invoicestartdate'] = $rsDelivery[$i]['invoicestartdate'];
                $arrItemDelivery[$arrIndex]['invoiceenddate'] = $rsDelivery[$i]['invoiceenddate'];
                $arrItemDelivery[$arrIndex]['total'] = $rsDelivery[$i]['total'];

            }else{
                $arrItemDelivery[$arrIndex]['qty'] += $rsDelivery[$i]['qty'];
                $arrItemDelivery[$arrIndex]['total'] += $rsDelivery[$i]['total'];
                $arrItemDelivery[$arrIndex]['pkey'] = $arrItemDelivery[$arrIndex]['pkey'].','.$rsDelivery[$i]['pkey'];
            }
        }
        //$this->setLog()
        return array_values($arrItemDelivery);
        
    }
    
    function bulan($bulan){
        Switch ($bulan){
            //bisa diganti lang
            case 1 : $bulan="Januari";
                Break;
            case 2 : $bulan="Februari";
                Break;
            case 3 : $bulan="Maret";
                Break;
            case 4 : $bulan="April";
                Break;
            case 5 : $bulan="Mei";
                Break;
            case 6 : $bulan="Juni";
                Break;
            case 7 : $bulan="Juli";
                Break;
            case 8 : $bulan="Agustus";
                Break;
            case 9 : $bulan="September";
                Break;
            case 10 : $bulan="Oktober";
                Break;
            case 11 : $bulan="November";
                Break;
            case 12 : $bulan="Desember";
                Break;
        }
    return $bulan;
    }
}
?>
