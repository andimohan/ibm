<?php
  
class CarServiceMaintenance extends BaseClass{ 
  
   function __construct(){
		
		parent::__construct();
       
		$this->tableName = 'car_service_maintenance_header';
		$this->tableNameDetail = 'car_service_maintenance_detail';
		$this->tableCategory = 'car_service_maintenance_category';
        $this->tableMaintenanceType = 'car_service_maintenance_type';
        $this->tableCarMaintenanceRequest = 'car_service_maintenance_request_header';
		$this->tableCustomer = 'customer';
		$this->tableSupplier = 'supplier'; 
		$this->tableCar = 'car';
		$this->tableChassis = 'chassis';
		$this->tableEmployee = 'employee';
		$this->tableWarehouse = 'warehouse'; 
		$this->tableStatus = 'transaction_status';
		$this->tableMovement = 'item_movement'; 
		$this->tableHistory = 'history';
		$this->tablePayment= 'sales_order_payment'; 	
		$this->tableItem = 'item'; 	
		$this->tableItemUnit = 'item_unit'; 	
		$this->tableBrand = 'brand'; 	
		$this->tableUnit = 'item_unit'; 
        $this->tableItemSN = 'item_sn'; 	
		$this->tableItemPosition = 'item_position';	
		$this->tableItemCategory = 'item_category'; 	
        $this->tablePackageDetail = 'sales_order_package_detail'; 
        $this->tableItemPosition = 'item_position';
        $this->tableFile = 'car_service_maintenance_file'; 
        $this->uploadFileFolder = 'car-service-maintenance/';
       
       
        $this->useStorage = $this->useStorage('S3');
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
        /*$this->arrDataDetail['warehousekey'] = array('hidDetailWarehouseKey');
        $this->arrDataDetail['movementtype'] = array('selMovementType');*/
        $this->arrDataDetail['itemtype'] = array('itemType');
        $this->arrDataDetail['ispackage'] = array('isPackage');
        $this->arrDataDetail['priceinunitafterdiscount'] = array('priceInUnitAfterDiscount','number');  
        $this->arrDataDetail['trdesc'] = array('detailDesc');
        $this->arrDataDetail['newsnkey'] = array('hidNewSNKey');
        $this->arrDataDetail['lastsn'] = array('lastSN');
        $this->arrDataDetail['itemposition'] = array('selItemPosition');
        $this->arrDataDetail['newsn'] = array('newSN');
        $this->arrDataDetail['lastitemkey'] = array('hidLastItemKey');
       
       // sementara pake pricein inut after discount aj dulu
//        $this->arrDataDetail['taxvalue'] = array('taxValue','number'); 
//        $this->arrDataDetail['headerdiscount'] = array('headerDiscount','number'); 
//        $this->arrDataDetail['headeretccost'] = array('headerEtcCost','number'); 
           
        $this->arrPaymentDetail = array(); 
        $this->arrPaymentDetail['pkey'] = array('hidDetailPaymentKey');
        $this->arrPaymentDetail['refkey'] = array('pkey', 'ref');
        $this->arrPaymentDetail['amount'] = array('paymentMethodValue',array('datatype' => 'number','mandatory'=>true));
        $this->arrPaymentDetail['paymentkey'] = array('selPaymentMethod',array('mandatory'=>true)); 

       
  
        $arrDetails = array();
        array_push($arrDetails, array('dataset' => $this->arrDataDetail));
        array_push($arrDetails, array('dataset' => $this->arrPaymentDetail, 'tableName' => $this->tablePayment));

        if($this->useStorage){ 
            
            $this->arrDataFileDetail = array();  
            $this->arrDataFileDetail['pkey'] = array('hidDetailFileKey');
            $this->arrDataFileDetail['refkey'] = array('pkey','ref');
            $this->arrDataFileDetail['file'] = array('fileDetail',array('datatype' => 'file','uploadFolder' => $this->uploadFileFolder));
            
            array_push($arrDetails, array('dataset' => $this->arrDataFileDetail, 'tableName' => $this->tableFile));
        
        }else{ 
            array_push($arrDetails, array('dataset' => $this->arrDataFile, 'tableName' => $this->tableFile, 
                                          'datatype' => 'file', 'uploadFolder' => $this->uploadFileFolder,
                                          'token' => 'token-item-file-uploader', 'fileName' => 'item-file-uploader')); 
        }
       
        $this->arrData = array(); 
        $this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails));  
        $this->arrData['code'] = array('code');
        $this->arrData['trdate'] = array('trDate','date');
        $this->arrData['estdate'] = array('estDate','date');
        $this->arrData['executedate'] = array('executeDate','date');
        $this->arrData['refkey'] = array('hidCarMaintenanceRequestKey');
        $this->arrData['isoutsource'] = array('chkIsOutsource');
        $this->arrData['supplierkey'] = array('hidSupplierKey');
        $this->arrData['driverkey'] = array('hidDriverKey');
        $this->arrData['warehousekey'] = array('selWarehouseKey');
        $this->arrData['categorykey'] = array('selCategory');
        $this->arrData['typekey'] = array('selType');
        $this->arrData['termofpaymentkey'] = array('selTermOfPaymentKey');
        $this->arrData['trnotes'] = array('trDesc');
        $this->arrData['subtotal'] = array('subtotal','number');
        $this->arrData['finaldiscounttype'] = array('selFinalDiscountType','number');
        $this->arrData['finaldiscount'] = array('finalDiscount','number');
        $this->arrData['beforetaxtotal'] = array('beforeTaxTotal','number');
        $this->arrData['ispriceincludetax'] = array('chkIncludeTax');
        $this->arrData['taxpercentage'] = array('taxPercentage','number');
        $this->arrData['taxvalue'] = array('taxValue','number');
        $this->arrData['etccost'] = array('etcCost','number');
        $this->arrData['grandtotal'] = array('grandtotal','number');
        $this->arrData['totalpayment'] = array('totalPayment','number'); 
        $this->arrData['mileage'] = array('mileage','number');
        $this->arrData['carkey'] = array('hidCarKey');
        $this->arrData['chassiskey'] = array('hidChassisKey');
        $this->arrData['techniciankey'] = array('hidTechicianKey'); 
        $this->arrData['balance'] = array('balance');
        $this->arrData['statuskey'] = array('selStatus'); 
        $this->arrData['refcode'] = array('refCode'); 
        $this->arrData['complaint'] = array('complaint');
                                
		$this->tableNeedToBeCopyOnCancel = array($this->tableNameDetail, $this->tablePackageDetail, $this->tablePayment); 
		$this->securityObject = 'CarServiceMaintenance';  
		  
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'date','title' => 'date','dbfield' => 'trdate','default'=>true, 'width' => 100, 'align' =>'center','format' => 'date'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'warehouse','title' => 'warehouse','dbfield' => 'warehousename','default'=>true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'refCode','title' => 'reference','dbfield' => 'carmaintenacerequestcode','default'=>true, 'width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'mileage','title' => 'mileage','dbfield' => 'mileage','default'=>true, 'width' => 60,'align' =>'right','format' => 'number'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'invoiceReference','title' => 'invoiceReference','dbfield' => 'refcode','default'=>true, 'width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'carRegistrationNumber','title' => 'vehicle','dbfield' => 'policenumber','default'=>true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'technician','title' => 'technician','dbfield' => 'technicianname','default'=>true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'total','title' => 'total','dbfield' => 'grandtotal','default'=>true, 'width' => 100, 'align' =>'right','format' => 'number'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'supplier','title' => 'supplier','dbfield' => 'suppliername',  'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'description','title' => 'note','dbfield' => 'trnotes',  'width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'category','title' => 'category','dbfield' => 'categoryname',  'width' => 200));
        
        $this->printMenu = array();
        array_push($this->printMenu,array('code' => 'print', 'name' => $this->lang['printTransaction'],  'icon' => 'print', 'url' => 'print/carServiceMaintenance'));
             
	    
        $this->includeClassDependencies(array( 
            'AP.class.php',  
            'Brand.class.php',  
            'Car.class.php',  
            'Category.class.php',  
            'CarCategory.class.php',  
            'CarTurnover.class.php',
            'CashBank.class.php',  
            'Chassis.class.php',  
            'COALink.class.php',  
            'GeneralJournal.class.php',  
            'PaymentMethod.class.php',  
            'Supplier.class.php',  
            'Shipment.class.php',  
            'TermOfPayment.class.php',  
            'Item.class.php',
            'ItemPackage.class.php',
            'ItemMovement.class.php',
            'ItemUnit.class.php',
            'Warehouse.class.php',
            'ItemPosition.class.php',
            'ItemIn.class.php',
            'ItemOut.class.php',
            'CarServiceMaintenanceRequest.class.php'
          
        ));
       
       $this->overwriteConfig();
		 
       
   }
   
    function getQuery(){
	   
	   $sql =  '
			SELECT '.$this->tableName.'.* ,
               '.$this->tableEmployee.'.name as technicianname,
			   '.$this->tableWarehouse.'.name as warehousename,
			   '.$this->tableSupplier.'.name as suppliername,
			   '.$this->tableStatus.'.status as statusname ,
               ' . $this->tableCarMaintenanceRequest . '.code as carmaintenacerequestcode,
			   '.$this->tableCategory.'.name as categoryname ,
               CONCAT_WS(\'\', '.$this->tableCar.'.code , '.$this->tableChassis.'.code) as vehiclecode ,
               CONCAT_WS(\'\', '.$this->tableCar.'.policenumber , '.$this->tableChassis.'.chassisnumber) as policenumber,
               driver.name as drivername
			FROM 
                '.$this->tableStatus.', 
                '.$this->tableWarehouse.',
                '.$this->tableCategory.',
                '.$this->tableName.'
                    left join '.$this->tableEmployee.' on  '.$this->tableName.'.techniciankey = '.$this->tableEmployee.'.pkey
	                left join '.$this->tableEmployee.' driver on  '.$this->tableName.'.driverkey = driver.pkey
					left join '.$this->tableCar.' on '.$this->tableName.'.carkey = '.$this->tableCar.'.pkey
					left join '.$this->tableChassis.' on '.$this->tableName.'.chassiskey = '.$this->tableChassis.'.pkey
					left join '.$this->tableSupplier.' on '.$this->tableName.'.supplierkey = '.$this->tableSupplier.'.pkey 
					left join '.$this->tableCarMaintenanceRequest.' on '.$this->tableName.'.refkey = '.$this->tableCarMaintenanceRequest.'.pkey 
			WHERE 
                '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey and
                '.$this->tableName.'.categorykey = '.$this->tableCategory.'.pkey and
                '.$this->tableName.'.warehousekey = '.$this->tableWarehouse.'.pkey 
 		' .$this->criteria ;  
        
       return $sql;
    }  
	
    function afterUpdateData($arrParam, $action){  
        $this->updatePackage($arrParam);  
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
        $supplier = new Supplier();   
        $car= new Car();   
        $chassis = new Chassis();   

        $arrayToJs = parent::validateForm($arr,$pkey); 
        $isOutsource = $arr['chkIsOutsource'];
        $supplierkey = $arr['hidSupplierKey'];   
        $arrItemkey = $arr['hidItemKey']; 
        $arrQty = $arr['qty']; 
        $arrPriceinunit = $arr['priceInUnit']; 
        $arrSelUnit = $arr['selUnit'];
        $arrCarKey = $arr['hidCarKey'];
        $chassisKey = $arr['hidChassisKey'];
        $arrMileage = $this->unFormatNumber($arr['mileage']);
        //$arrSNkey = $arr['hidNewSNKey'];  // udah gk dipake
        $arrSN = $arr['newSN'];  
  
     
        $trDate = $arr['executeDate'];
 
        //validasi kalo status gk menunggu gk bisa edit 
        if (!empty($pkey)){
            $rs = $this->getDataRowById($pkey);
            if ($rs[0]['statuskey'] <> 1){
                $this->addErrorList($arrayToJs,false,$this->errorMsg[212]);
            }
        } 
        
        // kalo jenisnya mobil
        if ($arr['selType'] == 1){
            $rsCar= $car->getDataRowById($arrCarKey);
            if(empty($rsCar)){ 
                $this->addErrorList($arrayToJs,false, $this->errorMsg['car'][1]);
            }else{ 
                $validateMileage = $this->loadSetting('validateMileage');
                if($validateMileage == 1){
                    $rsKMBefore = $this->searchDataRow(
                        array(
                            $this->tableName.'.pkey',
                            $this->tableName.'.code',
                            $this->tableName.'.mileage',
                            $this->tableName.'.carkey',
                            $this->tableName.'.statuskey'
                        ), ' and ' . $this->tableName.'.carkey = '. $this->oDbCon->paramString($arrCarKey) .'
                            and '. $this->tableName .'.trdate < '.$this->oDbCon->paramDate($trDate).'
                            and '. $this->tableName .'.statuskey in (2,3) ', ' order by mileage desc limit 1');
    
                    $rsKMAfter = $this->searchDataRow(
                        array(
                            $this->tableName.'.pkey',
                            $this->tableName.'.code',
                            $this->tableName.'.mileage',
                            $this->tableName.'.carkey',
                            $this->tableName.'.statuskey'
                        ), ' and ' . $this->tableName.'.carkey = '. $this->oDbCon->paramString($arrCarKey) .'
                            and '. $this->tableName .'.trdate > '.$this->oDbCon->paramDate($trDate).'
                            and '. $this->tableName .'.statuskey in (2,3) ', ' order by mileage asc limit 1');


                    if(!empty($rsKMBefore) && !empty($rsKMAfter)) {
                        
                        $minMileage = min($rsKMBefore[0]['mileage'], $rsKMAfter[0]['mileage']);
                        $maxMileage = max($rsKMBefore[0]['mileage'], $rsKMAfter[0]['mileage']);

                        if ($arrMileage < $minMileage) {
                            $this->addErrorList($arrayToJs,false, '<strong>'.$this->errorMsg[201].'</strong> <br> ' . $this->errorMsg['car'][8] . ' <strong>' . $rsKMBefore[0]['code'] . ' - ' . $this->formatNumber($rsKMBefore[0]['mileage']) .  ' Km </strong>');
                        }
                        
                        if ($arrMileage > $maxMileage) {
                            $this->addErrorList($arrayToJs,false, '<strong>'.$this->errorMsg[201].'</strong> <br> ' . $this->errorMsg['car'][10] . ' <strong>' . $rsKMAfter[0]['code'] . ' - ' . $this->formatNumber($rsKMAfter[0]['mileage']) . ' Km </strong>');
                        }

                    } else if(!empty($rsKMBefore) && empty($rsKMAfter)) {

                        if ($arrMileage < $rsKMBefore[0]['mileage']) {
                            $this->addErrorList($arrayToJs,false, '<strong>'.$this->errorMsg[201].'</strong> <br> ' . $this->errorMsg['car'][8] . ' <strong>' . $rsKMBefore[0]['code'] . ' - ' . $this->formatNumber($rsKMBefore[0]['mileage']) .  ' Km </strong>');
                        }
                    } else if(empty($rsKMBefore) && !empty($rsKMAfter)) {

                        if ($arrMileage > $rsKMAfter[0]['mileage']) {
                            $this->addErrorList($arrayToJs,false, '<strong>'.$this->errorMsg[201].'</strong> <br> ' . $this->errorMsg['car'][10] . ' <strong>' . $rsKMAfter[0]['code'] . ' - ' . $this->formatNumber($rsKMAfter[0]['mileage']) . ' Km </strong>');
                        }

                    }    
                }        
            } 
        }else if ($arr['selType'] == 2){ 
            $rsChassis= $chassis->getDataRowById($chassisKey);
            if(empty($rsChassis))
                $this->addErrorList($arrayToJs,false, $this->errorMsg['chassis'][1]);
        }
       
        
        
        if($isOutsource==1){
            $rsSupplier = $supplier->getDataRowById($arr['hidSupplierKey']);
            if(empty($rsSupplier))
                $this->addErrorList($arrayToJs,false, $this->errorMsg['supplier'][1]); 
             
             for($i=0;$i<count($arrItemkey);$i++){
                 if (!empty($arrItemkey[$i])){
                    if ($this->unFormatNumber($arrPriceinunit[$i]) <= 0){ 
                        $rsItem = $item->getDataRowById($arrItemkey[$i]);
                        $this->addErrorList($arrayToJs,false,$rsItem[0]['name']. '. ' . $this->errorMsg[500]);  
                    }

                }
            } 
        }

        $arrDetailKeys = array(); 
        $arrDetailSNKeys = array(); 

        $rsItem = $item->searchData('','', true, ' and ' . $item->tableName.'.pkey in ('.$this->oDbCon->paramString($arrItemkey,',').')');
        $rsItemCol = array_column($rsItem,null, 'pkey');

        for($i=0;$i<count($arrItemkey);$i++) { 
            $rsItem = $rsItemCol[$arrItemkey[$i]];

            if (empty($arrItemkey[$i]) ){ 
                $this->addErrorList($arrayToJs,false, $this->errorMsg['item'][1]); 	
            }


            if (!empty($arrItemkey[$i])){
                if ($this->unFormatNumber($arrQty[$i]) <= 0){  
                    $this->addErrorList($arrayToJs,false,$rsItem['name']. '. ' . $this->errorMsg[500]);  
                }

                // cek punya konversi unit utk satuan yg dipilih gk  
                $conv = $item->getConvMultiplier($arrItemkey[$i],$arrSelUnit[$i]);
                if (empty($conv)){ 
                    $this->addErrorList($arrayToJs,false,$rsItem['name']. '. ' . $this->errorMsg['itemUnitConversion'][3]); 
                }  
            }


            // cek ada detail double gk  
            // boleh double karena ad kalanya servicenya sama tp harga berbeda
//            if (in_array($arrItemkey[$i],$arrDetailKeys)){  
//                $this->addErrorList($arrayToJs,false, $rsItem['name'].'. '.$this->errorMsg[215]); 	 
//            }else{ 
//                array_push($arrDetailKeys, $arrItemkey[$i]);
//            } 

            // cek ada detail double SN
            $indexSN = $arrItemkey[$i].'-'.$arrSNkey[$i];
            if (in_array($indexSN,$arrDetailSNKeys) && $rsItem[0]['needsn'] == 1){  
                $this->addErrorList($arrayToJs,false, $arrSN[$i].'. '.$this->errorMsg[215]); 	 
            }else{ 
                array_push($arrDetailSNKeys, $indexSN);
            } 
        }
 
        return $arrayToJs;
     } 
  
    function validateConfirm($rsHeader){
      
        $warehouse = new Warehouse();  
        $coaLink = new COALink(); 
        $item = new Item();
        $car = new Car();
         
        $id = $rsHeader[0]['pkey'];
        $rsDetail = $this->getDetailById($id);
        $rsPayment = $this->getPaymentMethodDetail($id); 

        $termOfPayment = new TermOfPayment();
        $rsTOP = $termOfPayment->getDataRowById($rsHeader[0]['termofpaymentkey']); 
        $isCash = false;
        if ($rsTOP[0]['duedays'] == 0)
            $isCash = true;

        $balance = 0;
        $totalPayment = 0;

        for($i=0;$i<count($rsPayment); $i++)
            $totalPayment += $rsPayment[$i]['amount'];

        $balance = $totalPayment - $rsHeader[0]['grandtotal'] ;  
 

        if($rsHeader[0]['isoutsource'] == 1) {
            if($isCash && $balance < 0 ) 
                $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg[502]);
       
            if (USE_GL){
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
                        $rsCOA = $coaLink->getCOALink ('ap', $warehouse->tableName,$rsHeader[0]['warehousekey'], 0); 
                        if (empty($rsCOA))	
                            $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg['coa'][3]); 
                }   
            }

            
        }else{
            
            //validasi stock
            $itemMovement = new ItemMovement(); 
            $arrItemKey = array_column($rsDetail,'itemkey'); 
            $rsItemCOGS = $item->getCOGS($arrItemKey, ' and trdate <= '.$this->oDbCon->paramString($rsHeader[0]['trdate'])); 
            $rsItemCOGS = array_column($rsItemCOGS, null,'itemkey'); 
            $arrItemKeys = array_column($rsDetail,'itemkey');
            
            $rsItem = $item->searchData('','',true, ' and ' . $item->tableName.'.pkey in ('.$this->oDbCon->paramString($arrItemKeys,',').') ');
            $rsItemCol = array_column($rsItem,null,'pkey');
            
            foreach($rsDetail as $detailRow){  
                 $arrDetailKey = array();
                 $arrQty = array();

                if(empty($detailRow['itemkey']) || $detailRow['itemtype'] == SERVICE)
                    continue;
                
                $rsItem = $rsItemCol[$detailRow['itemkey']];

                
                // cek COGS sudah berubah apa blm 
                 
                if ($detailRow['itemtype'] == ITEM){  
                    $itemCOGS = $rsItemCOGS[$detailRow['itemkey']];
                    
                    $COGS = (!empty($itemCOGS)) ? $itemCOGS['cogs'] : 0;
                    if(!$this->compareNumberEpsilon($COGS,$detailRow['priceinbaseunit'])){
                        $this->addErrorLog(false,'<strong>'.$rsItem['name'].'</strong>. '.$this->errorMsg['carServiceMaintenance'][2]);
                    }     

                }
 
        
               if ($detailRow['ispackage'] == 0){
                    array_push($arrDetailKey, $detailRow['itemkey']);
                    array_push($arrQty, $detailRow['qtyinbaseunit']); 
                }else{
                    $rsPackageDetail = $this->getPackageDetail($detailRow['pkey'],1);

                    for($j=0;$j<count($rsPackageDetail); $j++){
                        array_push($arrDetailKey, $rsPackageDetail[$j]['itemkey']);
                        array_push($arrQty, ($rsPackageDetail[$j]['qtyinbaseunit']*$detailRow['qtyinbaseunit'])); 
                    }
                } 

                $warehousekey = array();
                array_push($warehousekey,$rsHeader[0]['warehousekey']);

                if (!empty($detailRow['warehousekey'])){
                    if (!empty($detailRow['warehousekey']) && $detailRow['movementtype'] == 1){
                        array_push($warehousekey,$detailRow['warehousekey']);
                    }else{
                        $warehousekey = array($detailRow['warehousekey']);
                    }
                } 

                for($k=0;$k<count($arrDetailKey);$k++) {

                    $saldoakhir = 0 ;
                    for($l=0;$l<count($warehousekey);$l++){ 
                        $saldoakhir += $itemMovement->getItemQOH($arrDetailKey[$k], $warehousekey[$l]);   
                    }

                     $totalqty = $saldoakhir - $arrQty[$k];  
                     if($totalqty<0){    
                        $this->addErrorLog(false,'<strong>'.$rsItem['name'].'</strong>. '.$this->errorMsg[402]);
        
                    }  

                } 

            if ($rsItem['needsn'] == 1){ 
                    // cek sn 
                    $saldoakhir = $itemMovement->getItemSNQOH($detailRow['itemkey'], $detailRow['newsn'], $rsHeader[0]['warehousekey']);
                    if($saldoakhir<=0) {
                        $this->addErrorLog(false,'<strong>'.$rsItem['name'].', '. $detailRow['newsn'].'</strong>. '.$this->errorMsg[402]);    
                    }

                    //cek last sn
                    $rsLastSN = $car->getCarItemLastSN($rsHeader[0]['carkey'], $detailRow['itemkey'], $detailRow['itemposition']); 

                    if(!empty($rsLastSN)) { 
                        
                        $lastSN = $detailRow['lastsn'];
                        if($rsLastSN[0]['serialnumber'] <> $lastSN) {
                            $this->addErrorLog(false,'<strong>'.$rsItem['name'].'. </strong> '. $this->lang['lastSN'].' '.$this->errorMsg[223]);
                        }

                        $lastItemKey = $detailRow['lastitemkey'];
                        if($rsLastSN[0]['itemkey'] <> $lastItemKey) {
                            $this->addErrorLog(false,'<strong>'.$rsItem['name'].'. </strong> '. $this->lang['lastItemOrService'].' '.$this->errorMsg[223]);
                        }
                    }


                }
            }
            
        } 
        
        if($rsHeader[0]['typekey'] == 1){
            
                $validateMileage = $this->loadSetting('validateMileage');
                if($validateMileage == 1){
                       $rsKMBefore = $this->searchDataRow(
                                        array(
                                            $this->tableName.'.pkey',
                                            $this->tableName.'.code',
                                            $this->tableName.'.mileage',
                                            $this->tableName.'.carkey',
                                            $this->tableName.'.statuskey'
                                        ), ' and ' . $this->tableName.'.carkey = '. $this->oDbCon->paramString($rsHeader[0]['carkey']) .'
                                            and '. $this->tableName .'.trdate < '.$this->oDbCon->paramString($rsHeader[0]['executedate']).'
                                            and '. $this->tableName .'.statuskey in (2,3) ', ' order by mileage desc limit 1');
                    
                        $rsKMAfter = $this->searchDataRow(
                                            array(
                                                $this->tableName.'.pkey',
                                                $this->tableName.'.code',
                                                $this->tableName.'.mileage',
                                                $this->tableName.'.carkey',
                                                $this->tableName.'.statuskey'
                                            ), ' and ' . $this->tableName.'.carkey = '. $this->oDbCon->paramString($rsHeader[0]['carkey']) .'
                                                and '. $this->tableName .'.trdate > '.$this->oDbCon->paramString($rsHeader[0]['executedate']).'
                                                and '. $this->tableName .'.statuskey in (2,3) ', ' order by mileage asc limit 1');


                        if(!empty($rsKMBefore) && !empty($rsKMAfter)) {

                            $minMileage = min($rsKMBefore[0]['mileage'], $rsKMAfter[0]['mileage']);
                            $maxMileage = max($rsKMBefore[0]['mileage'], $rsKMAfter[0]['mileage']);

                            // if ($rsHeader[0]['mileage'] < $minMileage || $rsHeader[0]['mileage'] > $maxMileage) {
                            //     $this->addErrorLog(false, $this->errorMsg['car'][8]);
                            // }

                            if ($rsHeader[0]['mileage'] < $minMileage) {
                                $this->addErrorLog(false, '<strong>'.$rsHeader[0]['code'] . '. '. $this->errorMsg[201].'</strong> <br> ' . $this->errorMsg['car'][8] . '<strong>'. $rsKMBefore[0]['code'] . ' - ' . $this->formatNumber($rsKMBefore[0]['mileage']) . ' Km</strong>');
                            }

                            if ($rsHeader[0]['mileage'] > $maxMileage) {
                                $this->addErrorLog(false, '<strong>'.$rsHeader[0]['code'] . '. '. $this->errorMsg[201].'</strong> <br> ' . $this->errorMsg['car'][10] . '<strong>'. $rsKMAfter[0]['code'] . ' - ' . $this->formatNumber($rsKMAfter[0]['mileage']) . ' Km</strong>');
                            }

                        } else if(!empty($rsKMBefore) && empty($rsKMAfter)) {

                            if ($rsHeader[0]['mileage'] < $rsKMBefore[0]['mileage']) {
                                $this->addErrorLog(false, '<strong>'.$rsHeader[0]['code'] . '. '. $this->errorMsg[201].'</strong> <br> ' . $this->errorMsg['car'][8] . '<strong>'. $rsKMBefore[0]['code'] . ' - ' . $this->formatNumber($rsKMBefore[0]['mileage']) . ' Km</strong>');
                            }
                        } else if(empty($rsKMBefore) && !empty($rsKMAfter)) {

                            if ($rsHeader[0]['mileage'] > $rsKMAfter[0]['mileage']) {
                                $this->addErrorLog(false, '<strong>'.$rsHeader[0]['code'] . '. '. $this->errorMsg[201].'</strong> <br> ' . $this->errorMsg['car'][10] . '<strong>'. $rsKMAfter[0]['code'] . ' - ' . $this->formatNumber($rsHeader[0]['mileage']) . ' Km</strong>');
                            }
                        }  
                }
                 
        }
        
     }
    
    function confirmTrans($rsHeader){ 
 
        $id = $rsHeader[0]['pkey'];
        
        $rsDetail = $this->getDetailWithRelatedInformation($rsHeader[0]['pkey']);

        $car = new Car(); 
        $item = new Item();
        $chassis = new Chassis();
        $supplier = new Supplier(); 
        $coaLink = new COALink();
        $warehouse = new Warehouse(); 
        $carServiceMaintenanceRequest = new CarServiceMaintenanceRequest();
        
        $notecash = $rsHeader[0]['code'].'. Kas Keluar untuk '.$rsHeader[0]['code'];
        $termOfPayment = new TermOfPayment();
        $rsTOP = $termOfPayment->getDataRowById($rsHeader[0]['termofpaymentkey']); 
        $isCash = ($rsTOP[0]['duedays'] == 0) ? true : false;
 
        $rsMaintenanceCategory  = $this->getMaintenanceCategory($rsHeader[0]['categorykey']);
        $categoryName  = $rsMaintenanceCategory[0]['name'];
        $movementNote = '';
        
        switch($rsHeader[0]['typekey']){
            case '1' :  $rsCar = $car->getDataRowById($rsHeader[0]['carkey']);
                        $movementNote = $this->ucFirst($categoryName).' ' .$rsCar[0]['policenumber'].', KM ' . $rsHeader[0]['mileage'].'.';
                        break;
            case '2' :  $rsChassis = $chassis->getDataRowById($rsHeader[0]['chassiskey']);
                        $movementNote =  $this->ucFirst($categoryName).' ' .$rsChassis[0]['code'];
                        break;
        }


        $rsPayment = array();
        
        if($rsHeader[0]['isoutsource'] == 1) {
            // kalo pake bengkel luar
            
            // MENGHITUNG PAYMENT
            if ($isCash){
                
                $rsPayment = $this->getPaymentMethodDetail($id);   
                
                if(ADV_FINANCE){ 
                    //$cashMovement = new CashMovement();  
                    $cashBank = new CashBank(); 

                    for($i=0;$i<count($rsPayment); $i++){  
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

                        $rsCashBank = $cashBank->addCashBank($rsHeader,$this->tableName, array('supplierkey' => $rsHeader[0]['supplierkey'],'coakey' => $coakey, 'desc' => $movementNote.chr(13).implode(', ', $arrItemName), 'amount' => -$rsPayment[$i]['amount'])); 
                        $rsPayment[$i]['cashBankKey'] = $rsCashBank['pkey'];
                    }          
                }
                
                
            }else{

                $ap = new AP(); 
                
                $arrParam = array();	

                $rsAPKey = $ap->getTableKeyAndObj($this->tableName);  
                $arrParam['code'] = 'xxxxxx';
                $arrParam['hidSupplierKey'] = $rsHeader[0]['supplierkey'];
                $arrParam['hidRefKey'] = $id;
                $arrParam['hidRefHeaderKey'] = $id;
                $arrParam['hidRefCode'] =  $rsHeader[0]['code'];
                $arrParam['hidRefCode2'] =  $rsHeader[0]['refcode'];
                $arrParam['hidRefInvoiceCode'] =  $rsHeader[0]['refcode'];
                $arrParam['hidRefDate'] =  $this->formatDBDate($rsHeader[0]['trdate'],'d / m / Y');  
                $arrParam['hidRefTable'] = $rsAPKey['key'];
                $arrParam['selWarehouse'] = $rsHeader[0]['warehousekey'];
                $arrParam['selAPType'] = 1; 
                $arrParam['amount'] = abs($rsHeader[0]['balance']);
                $arrParam['trDesc'] = '';
                $arrParam['trDate'] =  $this->formatDBDate($rsHeader[0]['trdate'],'d / m / Y');  
                $date = new DateTime($rsHeader[0]['trdate']);
                $date->add(new DateInterval('P'.$rsTOP[0]['duedays'].'D'));
                $arrParam['dueDate'] = $date->format('d / m / Y');// date ('d / m / Y', mktime(0, 0, 0, date("m")  , date("d")+$rsTOP[0]['duedays'], date("Y")));
                $arrParam['createdBy'] = 0;
                $arrParam['islinked'] = 1; 
                $arrParam['overwriteGL'] = 1;
 
                $arrayToJs = $ap->addData($arrParam); 
                if (!$arrayToJs[0]['valid'])
                    throw new Exception('<strong>'.$rsHeader[0]['code'] . '</strong>. '.$this->errorMsg[201].' '.$arrayToJs[0]['message']);   

            }        

        }else{
              // kalo pake stok sendiri
            
            //$this->updateCostAndPrice($id);
//            $rsHeader = $this->getDataRowById($id); // ambil ulang header nya, kalo berubah grandtotal
//            $rsDetail = $this->getDetailById($id); // ambil ulang detail nya, kalo berubah nilai cogs nya
                
            $itemMovement = new ItemMovement();
                
            for($i=0;$i<count($rsDetail); $i++){
                $arrDetailKey = array();
                $arrQty = array();
                $arrCost = array();

                $warehousekey = array();
                array_push($warehousekey,$rsHeader[0]['warehousekey']);
                
                if (!empty($rsDetail[$i]['warehousekey'])){
                    if (!empty($rsDetail[$i]['warehousekey']) && $rsDetail[$i]['movementtype'] == 1){
                        array_push($warehousekey,$rsDetail[$i]['warehousekey']);
                    }else{
                        $warehousekey = array($rsDetail[$i]['warehousekey']);
                    }
                } 
                
                if(empty($rsDetail[$i]['itemkey']) || $rsDetail[$i]['itemtype'] == SERVICE)
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
                        $qtyDeducted = $itemMovement->getItemQOH($arrDetailKey[$k], $warehousekey[$l]);    
                        $qtyDeducted = ($qtyDeducted >= $arrQty[$k]) ? $arrQty[$k] : $qtyDeducted;

                        $arrQty[$k] -= $qtyDeducted;
                        $itemMovement->updateItemMovement($id,$arrDetailKey[$k],-$qtyDeducted, $arrCost[$k] ,$this->tableName, $warehousekey[$l],$movementNote ,$rsHeader[0]['trdate']);
                    } 

                }    
                
            }
            
        }
        

        //update Car Turnover
        $carTurnover = new CarTurnover();

        $arrParam = array();	 

        if($rsHeader[0]['typekey']==1){
            $rsObjKey = $this->getTableKeyAndObj($this->tableName);   
            $arrParam['hidRefKey'] = $rsHeader[0]['pkey'];
            $arrParam['refCode'] = $rsHeader[0]['code'];
            $arrParam['trDate'] =   $this->formatDBDate($rsHeader[0]['trdate'],'d / m / Y'); 
            $arrParam['joDate'] =   $arrParam['trDate'] ; // samakan kalo utk maintenance
            $arrParam['selWarehouse'] = $rsHeader[0]['warehousekey'];
            $arrParam['hidRefTable'] = $rsObjKey['key'];
            $arrParam['hidCarKey'] = $rsHeader[0]['carkey'];   
            $arrParam['amount'] = ($rsHeader[0]['isoutsource']==1) ? $rsHeader[0]['grandtotal'] : $rsHeader[0]['grandtotal'];   
            $arrParam['amount'] *= -1 ; 
            $arrParam['selStatus'] = 1;
            
            $maintenanceDesc = array();
            for ($i=0; $i<count($rsDetail); $i++) {
                $descDetail = (!empty($rsDetail[$i]['trdesc'])) ? ', '.$rsDetail[$i]['trdesc'] : '';
                $itemDesc =  $this->formatNumber($rsDetail[$i]['qty']). "  ". $rsDetail[$i]['unitname'] ." ". $rsDetail[$i]['itemname'] .$descDetail." @ Rp. ". $this->formatNumber($rsDetail[$i]['priceinunitafterdiscount']);
                array_push($maintenanceDesc, $itemDesc);
            }
            
            $arrParam['trDesc'] = implode(chr(13),$maintenanceDesc);

            $arrayToJs =  $carTurnover->addData($arrParam); 
            if (!$arrayToJs[0]['valid'])
                throw new Exception('<strong>'.$rsHeader[0]['code'] . '</strong>. '.$this->errorMsg[201].' '.$arrayToJs[0]['message']);    

            $rsCar = $car->getDataRowById($rsHeader[0]['carkey']);

            // masukin ke AP Negative
            if(!empty($rsCar[0]['supplierkey'])){
                $ap = new AP();   

                $vendorkey = $rsCar[0]['supplierkey'];
                $rsSupplier = $supplier->searchData($supplier->tableName.'.pkey', $vendorkey); 
                $duedays = $rsSupplier[0]['duedays'];

                $arrParam = array();	 
                $arrParam['code'] = 'xxxxxx';
                $arrParam['hidRefKey'] = $rsHeader[0]['pkey'];
                $arrParam['hidRefHeaderKey'] = $rsHeader[0]['pkey'];
                $arrParam['hidRefCode'] = $rsHeader[0]['code'];
                $arrParam['trDate'] =   $this->formatDBDate($rsHeader[0]['trdate'],'d / m / Y'); 
                $arrParam['hidRefDate'] =   $this->formatDBDate($rsHeader[0]['trdate'],'d / m / Y'); 
                $arrParam['selWarehouse'] = $rsHeader[0]['warehousekey'];
                $arrParam['hidRefTable'] = $rsObjKey['key'];
                $arrParam['hidCarKey'] = $rsCar[0]['pkey'];  
                $arrParam['hidSupplierKey'] = $vendorkey;  
                $arrParam['amount'] = -$rsHeader[0]['grandtotal'];
                $arrParam['trDesc'] = $rsCar[0]['policenumber'].chr(13).implode(chr(13),$maintenanceDesc); 
                $arrParam['selStatus'] = 1;
                $arrParam['overwriteGL'] = 1;
                $arrParam['islinked'] = 1;
                $arrParam['selAPType'] = AP_TYPE['carServiceMaintenance'];
                $date = new DateTime($rsHeader[0]['trdate']);
                $date->add(new DateInterval('P'.$duedays.'D'));
                $arrParam['dueDate'] = $date->format('d / m / Y');

                $arrayToJs =  $ap->addData($arrParam); 
                if (!$arrayToJs[0]['valid'])
                    throw new Exception('<strong>'.$rsHeader[0]['code'] . '</strong>. '.$this->errorMsg[201].' '.$arrayToJs[0]['message']);   

            }
        }
        
        // jangan dulu, ad kemungkinan 1 request bisa beberapa kali pengerjaan
        //update status ke selesai carServiceMaintenanceRequest 
//        if(!empty($rsHeader[0]['refkey'])) 
//            $carServiceMaintenanceRequest->changeStatus($rsHeader[0]['refkey'], 3, '', false, true);
   
        //update jurnal umum 
        $this->updateGL($rsHeader,$rsPayment);
        $this->updateItemSN($rsHeader,$rsDetail);

    } 
    
    function afterStatusChanged($rsHeader){
  	   //$rs = $this->getDataRowById($rsHeader[0]['pkey']);
        
        //if($rs[0]['typekey']==1) { // siapa tau chassis / genset nanti jg perlu
            $car = new Car();
            $car->updateCarItemDetail($rsHeader[0]['carkey']);
        //}

    }


    function updateItemSN($rsHeader, $rsDetail){ 
  
        $itemMovement = new ItemMovement();
        $car = new Car();
        $chassis = new Chassis();
        $itemIn = new ItemIn();

        $rsMaintenanceCategory  = $this->getMaintenanceCategory($rsHeader[0]['categorykey']);
        $categoryName  = $rsMaintenanceCategory[0]['name'];
        $rsObjKey = $this->getTableKeyAndObj($this->tableName);

        $movementNote = '';
        
        switch($rsHeader[0]['typekey']){
            case '1' :  $rsCar = $car->getDataRowById($rsHeader[0]['carkey']);
                        $movementNote = $this->ucFirst($categoryName).' ' .$rsCar[0]['policenumber'].', KM ' . $rsHeader[0]['mileage'].'.';
                        break;
            case '2' :  $rsChassis = $chassis->getDataRowById($rsHeader[0]['chassiskey']);
                        $movementNote =  $this->ucFirst($categoryName).' ' .$rsChassis[0]['code'];
                        break;
        }

        $arrParamItemIn = array();
        $arrParamItemIn['code'] = 'xxxxxx';
        $arrParamItemIn['trDate'] = $this->formatDBDate($rsHeader[0]['trdate'], 'd / m / Y');
        $arrParamItemIn['selWarehouseKey'] = $rsHeader[0]['warehousekey']; // nanti harusnya bukan ini warehouse nya
        $arrParamItemIn['refCode'] = $rsHeader[0]['code'];
        $arrParamItemIn['refkey'] = $rsHeader[0]['pkey'];
        $arrParamItemIn['chkIsFullDelivered'] = 1;
        $arrParamItemIn['chkIsInternal'] = 1;
        $arrParamItemIn['hidCustomerKey'] = $rsHeader[0]['customerkey'];
        $arrParamItemIn['hidEmployeeKey'] = 0;

        $arrParamItemIn['reftabletype'] = $rsObjKey['key'];
        $arrParamItemIn['islinked'] = 1;
        $arrParamItemIn['trDesc'] = $movementNote;
        $arrParamItemIn['overwriteGL'] = 1; // anggap gk ad nilai utk barang bekas

        $arrParamItemIn['hidDetailKey'] = array();
        $arrParamItemIn['hidItemKey'] = array();
        $arrParamItemIn['qty'] = array();
        $arrParamItemIn['selUnit'] = array();
        $arrParamItemIn['qtyInBaseUnit'] = array();
        $arrParamItemIn['snList'] = array();
        $arrParamItemIn['COGS'] = array();

        for($i=0;$i<count($rsDetail);$i++){ 
            
            if ($rsDetail[$i]['needsn'] == 1) {
                
                   // utk data2 baru, ad kemungkinan SN lama kosong
                if (!empty($rsDetail[$i]['lastsn'])){
                    
                    array_push($arrParamItemIn['hidDetailKey'], 0);
                    array_push($arrParamItemIn['hidItemKey'], $rsDetail[$i]['itemkey']);
                    array_push($arrParamItemIn['qty'], $rsDetail[$i]['qty']);
                    array_push($arrParamItemIn['selUnit'], $rsDetail[$i]['unitkey']);
                    array_push($arrParamItemIn['qtyInBaseUnit'], $rsDetail[$i]['qty']);
                    array_push($arrParamItemIn['COGS'], 1); //dianggap tdk ad harga karena barang second
                    array_push($arrParamItemIn['snList'], $rsDetail[$i]['lastsn']);
                }
                
                $rsItemSN = $itemMovement->searchItemSerialNumber($rsDetail[$i]['itemkey'], $rsDetail[$i]['newsn']); 

                $rsObjKey = $this->getTableKeyAndObj($this->tableName); 
                $arrParam = array(
                    'refkey' => $rsDetail[$i]['pkey'],
                    'refheaderkey' => $rsHeader[0]['pkey'],
                    'itemkey' => $rsDetail[$i]['itemkey'],
                    'sn' => $rsDetail[$i]['newsn'],
                    'qtyinbaseunit' => -1,
                    'costinbaseunit' => $rsDetail[$i]['costinbaseunit'], 
                    'warehousekey' => $rsHeader[0]['warehousekey'],
                    'note' => $movementNote,
                    'trdate' => $rsHeader[0]['trdate'],
                    'reftabletype' => $rsObjKey['key']
                    );
    
                $result = $itemMovement->updateItemSNMovement($arrParam);
                 
                
                // update sn key 
    
                $sql = 'update '.$this->tableNameDetail.' set newsnkey = '.$rsItemSN[0]['pkey'].' where pkey = ' . $this->oDbCon->paramString($rsDetail[$i]['pkey']) ;   
                $this->oDbCon->execute($sql); 

                
            }
        }

        
            //throw new Exception('<strong>'.$rsHeader[0]['code'] . '</strong>.'); 
        
        if (!empty($arrParamItemIn['hidDetailKey'])) { 
            $arrayToJs = $itemIn->addData($arrParamItemIn);
            
            if (!$arrayToJs[0]['valid'])
                throw new Exception('<strong>'.$rsHeader[0]['code'] . '</strong>. '.$arrayToJs[0]['message']); 
        }
    }


    function updateGL($rs,$rsPayment){
        if (!USE_GL) return;
        
        $warehouse = new Warehouse();
        $coaLink = new COALink();
        $generalJournal = new GeneralJournal();
        $supplier = new Supplier();
        $item = new Item();
        
        $warehousekey = $rs[0]['warehousekey'];

        $rsKey = $this->getTableKeyAndObj($this->tableName);
        $arr = array();
        $arr['pkey'] = $generalJournal->getNextKey($generalJournal->tableName);
        $arr['code'] = 'xxxxx';
        $arr['refkey'] = $rs[0]['pkey'];
        $arr['refTableType'] = $rsKey['key'];
        $arr['trDate'] = $this->formatDBDate($rs[0]['trdate'],'d / m / Y');  
        $arr['createdBy'] = 0;
        $arr['selWarehouseKey'] = $rs[0]['warehousekey'];
        
        $rsMaintenanceCategory  = $this->getMaintenanceCategory($rs[0]['categorykey']);
        $categoryName  = $rsMaintenanceCategory[0]['name'];
               
        switch($rs[0]['typekey']){
            case '1' :  $car = new Car();
                        $rsCar = $car->getDataRowById($rs[0]['carkey']);
                        $arr['trDesc'] = $this->ucFirst($categoryName).' '.$rsCar[0]['policenumber'].', KM ' . $rs[0]['mileage'].'.'; 
                        break;
            case '2' :  $chassis = new Chassis();
                        $rsChassis = $chassis->getDataRowById($rs[0]['chassiskey']);
                        $arr['trDesc'] =  $this->ucFirst($categoryName).' ' .$rsChassis[0]['code'];
                        break;
        }

       

        $temp = -1; 
        
        //HPP
        //$totalHPP = 0 ;
        $totalDisc = 0 ;

        $rsDetail = $this->getDetailById($rs[0]['pkey']); 
        for($i=0;$i<count($rsDetail);$i++){
            //$totalHPP += ($rsDetail[$i]['costinbaseunit'] * $rsDetail[$i]['qtyinbaseunit']);
            
           // $cost = ($rsDetail[$i]['priceinbaseunit'] * $rsDetail[$i]['qtyinbaseunit']);
            $cost = $rsDetail[$i]['priceinunitafterdiscount'] * $rsDetail[$i]['qty'];
            //$this->setLog($cost);
            
            $temp++;
            $arr['hidCOAKey'][$temp] = $item->getCostCOAKey($rsDetail[$i]['itemkey'],$warehousekey,'maintenancecost');
            $arr['debit'][$temp] = $cost; 
            $arr['credit'][$temp] = 0;    
            $arr['refCashBankKey'][$temp] = '';          

           if ($rs[0]['isoutsource'] == 0){
                $temp++;
                $arr['hidCOAKey'][$temp] = $item->getInventoryCOAKey($rsDetail[$i]['itemkey'],$warehousekey);
                $arr['debit'][$temp] = 0; 
                $arr['credit'][$temp] = $cost; 
                $arr['refCashBankKey'][$temp] = '';  
            }
        }
        
         // kalo outsource, creditnya dr kas / AP
         if ($rs[0]['isoutsource'] == 1){
                $termOfPayment = new TermOfPayment();
                $rsTOP = $termOfPayment->getDataRowById($rs[0]['termofpaymentkey']); 
                $isCash = ($rsTOP[0]['duedays'] == 0) ? true : false; 

             

                $rsCOA = $coaLink->getCOALink ('taxin', $warehouse->tableName,$warehousekey, 0); 
                $temp++;
                $arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];
                $arr['debit'][$temp] =  $rs[0]['taxvalue']; 
                $arr['credit'][$temp] = 0; 
                $arr['refCashBankKey'][$temp] = '';

                if ($isCash) {
                    //$rsPayment = $this->getPaymentMethodDetail($rs[0]['pkey']);  
                    for($i=0;$i<count($rsPayment); $i++){ 
                         $rsCOA = $coaLink->getCOALink ('payment', $warehouse->tableName,$warehousekey, $rsPayment[$i]['paymentkey']);
                         $temp++;
                         $arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];
                         $arr['debit'][$temp] = 0; 
                         $arr['credit'][$temp] =  $rsPayment[$i]['amount'];  
                         $arr['refCashBankKey'][$temp] = $rsPayment[$i]['cashBankKey']; 
                    }

                    //selisih pembayaran   
                    if($rs[0]['balance'] != 0){ 
                        $temp++; 
                        if ($rs[0]['balance'] < 0){ 
                            $rsCOA = $coaLink->getCOALink ('otherrevenue', $warehouse->tableName,$warehousekey, 0); 
                            $arr['debit'][$temp] = 0; 
                            $arr['credit'][$temp] = abs($rs[0]['balance']);  
                        }else { 
                            $rsCOA = $coaLink->getCOALink ('othercost', $warehouse->tableName,$warehousekey, 0); 
                            $arr['debit'][$temp] = abs($rs[0]['balance']);  
                            $arr['credit'][$temp] = 0; 
                        }

                        $arr['refCashBankKey'][$temp] = ''; 
                        $arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];
                    }

                }else {  
                        $temp++;
                        $arr['hidCOAKey'][$temp] =  $supplier->getAPCOAKey($rs[0]['supplierkey'],$warehousekey);
                        $arr['debit'][$temp] = 0; 
                        $arr['credit'][$temp] = $rs[0]['grandtotal'];  
                        $arr['refCashBankKey'][$temp] = '';  
                } 
        }
        
        $arrayToJs = $generalJournal->addData($arr);

        if (!$arrayToJs[0]['valid'])
                throw new Exception('<strong>'.$rs[0]['code'] . '</strong>. '.$this->errorMsg[504].' '.$arrayToJs[0]['message']);    
    }

    function cancelTrans($rsHeader,$copy){  

        $id = $rsHeader[0]['pkey'];
        $ap = new AP();
        $car = new Car();
        $itemIn = new ItemIn();
        
        $rsObjKey = $this->getTableKeyAndObj($this->tableName);
        
        if ($rsHeader[0]['isoutsource']){
            //$cashMovement = new CashMovement();   
            //$cashMovement->cancelMovement($id,$this->tableName);
            
            $rsAPKey = $ap->getTableKeyAndObj($this->tableName); 
            $rsAP = $ap->searchData('','',true,' and '.$ap->tableName.'.reftabletype = '.$this->oDbCon->paramString($rsAPKey['key']).' and  '.$ap->tableName.'.refheaderkey = '.$this->oDbCon->paramString($id).' and '.$ap->tableName.'.statuskey = 1');
            for($i=0;$i<count($rsAP);$i++) {
                $arrayToJs = $ap->changeStatus($rsAP[$i]['pkey'],4,'',false,true);
                if (!$arrayToJs[0]['valid'])
                    throw new Exception('<strong>'.$rsHeader[0]['code'] . '</strong>. '.  $arrayToJs[0]['message']);    
            }
        }else{
            $itemMovement = new ItemMovement();  
            $itemMovement->cancelMovement($id,$this->tableName);    
            $itemMovement->cancelSNMovement($id,$this->tableName);
        }
        
         
        $rsCar = $car->getDataRowById($rsHeader[0]['carkey']);
        if(!empty($rsCar[0]['supplierkey'])){
            $rsAPVendor = $ap->searchData('','',true,' and '.$ap->tableName.'.reftabletype = '.$this->oDbCon->paramString($rsObjKey['key']).' and '.$ap->tableName.'.refkey = '.$this->oDbCon->paramString($id).' and '.$ap->tableName.'.statuskey = 1');
            for($i=0;$i<count($rsAPVendor);$i++) { 
                $arrayToJs = $ap->changeStatus($rsAPVendor[$i]['pkey'],4,'',false, true);
                if (!$arrayToJs[0]['valid'])
                    throw new Exception('<strong>'.$rsHeader[0]['code'] . '</strong>. '.  $arrayToJs[0]['message']);    
            }
        }
        
        $carTurnover = new CarTurnover();
        if ($rsHeader[0]['typekey']==1)
            $carTurnover->cancelMovement($id,$rsObjKey['key']);

        
        $cashBank = new CashBank();
        $cashBank->cancelCashBank($rsHeader,$this->tableName);
       
        
        // jangan dulu, ad kemungkinan 1 request bisa beberapa kali pengerjaan
        //update status ke konfirmasi carServiceMaintenanceRequest
//        if(!empty($rsHeader[0]['refkey']))
//        {
//            $carServiceMaintenanceRequest = new CarServiceMaintenanceRequest();
//            $carServiceMaintenanceRequest->changeStatus($rsHeader[0]['refkey'], 2, '', false, true);
//        }
//           
        $rsItemIn = $itemIn->searchDataRow( array( $itemIn->tableName.'.pkey', $itemIn->tableName.'.code'  ) , 
                                ' and  '.$itemIn->tableName.'.refkey = '.$this->oDbCon->paramString($rsHeader[0]['pkey']).'  and reftabletype = '.$rsObjKey['key'].' and '.$itemIn->tableName.'.statuskey = 1'  
                    );
        $totalItem = count($rsItemIn);
        for($i=0;$i<$totalItem;$i++) { 
            $itemIn->changeStatus($rsItemIn[$i]['pkey'],4,'',false, true);  
        }

        if ($copy)
            $this->copyDataOnCancel($id);	  

        $this->cancelGLByRefkey($rsHeader[0]['pkey'],$this->tableName);

    }  

  function getMaintenanceCategory($categorykey = ''){
        
        $sql = 'select * from '.$this->tableCategory.' where 1=1'; 
      
        if(!empty($categorykey))
            $sql .= ' and pkey = ' . $this->oDbCon->paramString($categorykey);
      
        return $this->oDbCon->doQuery($sql);
        
    }
    
    
   function getMaintenanceType($typekey = ''){
        
        $sql = 'select * from '.$this->tableMaintenanceType.' where 1 = 1'; 
        
        if(!empty($typekey))
            $sql .= ' and '.$this->tableMaintenanceType .' .pkey in ('.$typekey.') ' ;
        
        return $this->oDbCon->doQuery($sql);
    }
    
    function validateCancel($rsHeader,$autoChangeStatus=false){ 
 
        $ap = new AP();
        $car = new Car();
        $itemIn = new ItemIn();
        
        $rsObjKey = $this->getTableKeyAndObj($this->tableName,array('key')); 
        
        if ($rsHeader[0]['isoutsource']){ 
            $rsAP = $ap->searchData('','',true,' and '.$ap->tableName.'.reftabletype = '.$this->oDbCon->paramString($rsObjKey['key']).' and '.$ap->tableName.'.refheaderkey = '.$this->oDbCon->paramString($rsHeader[0]['pkey']).' and ('.$ap->tableName.'.statuskey in (2,3) )');
       	    if(!empty($rsAP)){  
                $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' . $this->errorMsg[201].' ' .$this->errorMsg['ap'][2]);
            } 
        } 
        
        
        // pake gk pake outsource, hutang tetap terjadi
        // hanya utk mobil titipan 
        $rsCar = $car->getDataRowById($rsHeader[0]['carkey']);
        if(!empty($rsCar[0]['supplierkey'])){
               $rsAP = $ap->searchData('','',true,' and '.$ap->tableName.'.reftabletype = '.$this->oDbCon->paramString($rsObjKey['key']).' and '.$ap->tableName.'.refkey = '.$this->oDbCon->paramString($rsHeader[0]['pkey']).' and ('.$ap->tableName.'.statuskey in (2,3) )');
                if(!empty($rsAP)) 
                    $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' . $this->errorMsg[201].' ' .$this->errorMsg['ap'][2]);
        }
        
        // cari ad gk barang bekas yg sudah masuk ke gudang lg
         $rsItemIn = $itemIn->searchDataRow( array( $itemIn->tableName.'.pkey', $itemIn->tableName.'.code'  ) , 
                                ' and  '.$itemIn->tableName.'.refkey = '.$this->oDbCon->paramString($rsHeader[0]['pkey']).'  and reftabletype = '.$rsObjKey['key'].' and '.$itemIn->tableName.'.statuskey in (2,3)'  
                    );
       
        if(!empty($rsItemIn))
              $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' . $this->errorMsg[201].' ' .$this->errorMsg['carServiceMaintenance'][4]);

 
      } 

    function getDetailWithRelatedInformation($pkey,$criteria=''){


        $sql = 'select
                '.$this->tableNameDetail .'.*, 
                '.$this->tableItem.'.name as itemname, 
                '.$this->tableItem.'.code as itemcode, 
                '.$this->tableItem.'.itemtype,
                '.$this->tableBrand.'.name as brandname ,
                '.$this->tableItem.'.deftransunitkey,
                '.$this->tableItem.'.needsn,
                '.$this->tableItem.'.categorykey as itemcategorykey,
                '.$this->tableItemCategory.'.spareparttypekey,
                '.$this->tableItemUnit.'.name as unitname,
                '.$this->tableItemSN.'.serialnumber as newitemsn,
                baseunit.name as baseunitname,
                lastitem.name as lastitemname
            from
                '.$this->tableNameDetail .'
                    left join '.$this->tableItemSN.' on ' . $this->tableNameDetail .'.newsnkey = '.$this->tableItemSN.'.pkey
                    left join '.$this->tableItem.' lastitem on '.$this->tableNameDetail.'.lastitemkey = lastitem.pkey,
                '.$this->tableItemUnit.',
                '.$this->tableItemUnit.' baseunit,
                '.$this->tableItem.'
                    left join '.$this->tableBrand.' on 	' . $this->tableItem .'.brandkey = '.$this->tableBrand.'.pkey 
                    left join '.$this->tableItemCategory.' on '.$this->tableItem.'.categorykey = '.$this->tableItemCategory.'.pkey
            where
                '.$this->tableNameDetail .'.itemkey = '.$this->tableItem.'.pkey and
                '.$this->tableNameDetail.'.unitkey = '.$this->tableItemUnit.'.pkey and
                '.$this->tableItem.'.baseunitkey = baseunit.pkey and
                '.$this->tableNameDetail .'.refkey in ('.$this->oDbCon->paramString($pkey,',') . ') ';

        $sql .= $criteria;
  
        return $this->oDbCon->doQuery($sql);

    }  

    function updateCostAndPrice($id){
        //$this->setLog("harusnya kalo harga berubah, user save ulang saja, keluar warning");
        return;
        /*
        // sementara blm support paket, atau blm dicek
        
        $rs = $this->getDataRowById($id);
        $rsDetail = $this->getDetailById($id);
        $item = new Item();
        
        $isOutsource = $rs[0]['isoutsource'];
            
        $total = 0; 
  
        for($i=0;$i<count($rsDetail);$i++){
             $rsItem = $item->getDataRowById($rsDetail[$i]['itemkey']);
            
             if (!$isOutsource && $rsItem[0]['itemtype'] == ITEM){
                  // khusus jenis item dan inhouse
                  $rsDetail[$i]['priceInUnit'] = $rsItem[0]['cogs']; 
                  $rsDetail[$i]['priceInBaseUnit'] = $rsItem[0]['cogs']; 


                 //$cogs = $rsItem[0]['cogs'];

                 $qtyinbaseunit = $rsDetail[$i]['qtyinbaseunit'];
                 $priceinbaseunit = $cogs;
                 $subtotal = $qtyinbaseunit * $priceinbaseunit;
                 $total += $subtotal;

                 $sql = 'update 
                            '. $this->tableNameDetail .' 
                        set 
                            priceinunit = '.$rsDetail[$i]['priceInUnit'].', 
                            priceinbaseunit = '.$rsDetail[$i]['priceInBaseUnit'].', 
                            costinbaseunit = '.$rsDetail[$i]['priceInBaseUnit'].' ,
                            total = '.$subtotal.' 
                        where pkey = ' . $rsDetail[$i]['pkey'];
                $this->oDbCon->execute($sql);
            }
        } 
                 
        $total += $subtotal;
        
        $sql = 'update 
                    '. $this->tableName.' 
                set 
                    subtotal = '.$total.', 
                    beforetaxtotal = '.$total.', 
                    grandtotal = '.$total.' ,
                    balance = 0
                where pkey = ' . $id;
        
        $this->oDbCon->execute($sql);*/

    }  
  
function reCountSubtotal($arrParam){

				$isPriceIncludeTax = (isset($arrParam['chkIncludeTax'])) ? $arrParam['chkIncludeTax'] : 0; 
			
				$subtotal = 0 ;
				$grandtotal = 0;
				 
                $isOutsource = $arrParam['chkIsOutsource'];
				$arrItemKey = $arrParam['hidItemKey'];
				$taxValue = $this->unFormatNumber($arrParam['taxValue']); 
				 
				$finalDiscount = $this->unFormatNumber($arrParam['finalDiscount']); 
				$finalDiscountType = $arrParam['selFinalDiscountType']; 
				$taxPercentage = $this->unFormatNumber($arrParam['taxPercentage']); 
				$taxValue = $this->unFormatNumber($arrParam['taxValue']);  
				$etcCost = $this->unFormatNumber($arrParam['etcCost']);  
				 
				$arrQty = $arrParam['qty']; 
				$arrPriceinunit = $arrParam['priceInUnit']; 
				$arrDiscountValueInUnit = $arrParam['discountValueInUnit']; 
				$arrDiscountType = $arrParam['selDiscountType']; 
				$arrTransUnitKey = $arrParam['selUnit']; 
        
				 
				$arrItemDetail = array();
				$item = new Item(); 

                $rsItemCOGS = $item->getCOGS($arrItemKey, ' and trdate <= '.$this->oDbCon->paramDate($arrParam['trDate'], ' / ')); 
                $rsItemCOGS = array_column($rsItemCOGS, null,'itemkey'); 
    
    
        //
				
				for ($i=0;$i<count($arrItemKey);$i++){
                    
					if (empty($arrItemKey[$i]))  
						continue; 
                    
                        $rsItem = $item->getDataRowById($arrItemKey[$i]);
                     
                        $transactionUnitKey = $arrTransUnitKey[$i];
                        $baseunitkey = $rsItem[0]['baseunitkey']; 
                        $itemkey = $arrItemKey[$i];
						$qty =  $this->unFormatNumber($arrQty[$i]);
                        $conversionMultiplier = $item->getConvMultiplier($itemkey,$transactionUnitKey,$baseunitkey); 
                        $qtyinbaseunit = $qty * $conversionMultiplier;
						$priceInUnit = $this->unFormatNumber($arrPriceinunit[$i]);
						$discount = $this->unFormatNumber($arrDiscountValueInUnit[$i]);
						$discountType =  $this->unFormatNumber($arrDiscountType[$i]);
					 
					 	if ($discount != 0 && $discountType == 2){
							$discount = $discount/100 * $priceInUnit;
						}
						
                        $arrItemDetail[$i]['priceInUnit'] = $priceInUnit;
                    
				        $arrItemDetail[$i]['cogs'] = 0;
 			            if (!$isOutsource && $rsItem[0]['itemtype'] == ITEM){
                          // khusus jenis item dan inhouse
                          $itemCOGS = $rsItemCOGS[$itemkey];
                          $COGS = (!empty($itemCOGS)) ? $itemCOGS['cogs'] : 0;
						  $arrItemDetail[$i]['cogs'] = $COGS;
						  $arrItemDetail[$i]['priceInUnit'] = $conversionMultiplier * $COGS;
                          $priceInUnit =  $arrItemDetail[$i]['priceInUnit']; 
                            
                         // overwrite 
                          $arrPriceinunit[$i] = $priceInUnit;
                        }
                    
                        $priceInBaseUnit = $priceInUnit/$conversionMultiplier;

                    
						$detailSubtotal = $qty * ($priceInUnit - $discount);
                        //$this->setLog($qty.' * '.'('.$priceInUnit.' - '.$discount.')');
                        //$this->setLog('subtotal = '.$detailSubtotal);
                        $arrItemDetail[$i]['baseUnitKey'] = $baseunitkey;
						$arrItemDetail[$i]['isPackage'] = $rsItem[0]['ispackage'];
						$arrItemDetail[$i]['itemType'] = $rsItem[0]['itemtype'];
                        $arrItemDetail[$i]['unitConvMultiplier'] = $conversionMultiplier;
                        $arrItemDetail[$i]['qtyInBaseUnit'] = $qtyinbaseunit; 
                        $arrItemDetail[$i]['priceInBaseUnit'] = $priceInBaseUnit; 
						$arrItemDetail[$i]['unitDiscountValue'] = $discount;
						$arrItemDetail[$i]['detailSubtotal'] = $detailSubtotal;
                        
						
						$subtotal += $detailSubtotal ; 
				} 
				  
				$grandtotal = $subtotal;
				
				if ($finalDiscount != 0 && $finalDiscountType == 2)
						$finalDiscount = $finalDiscount/100 * $grandtotal;
			 
				 
                $totalFinalDiscount = $finalDiscount;
                
               /* for ($i=0;$i<count($arrItemKey);$i++){
					
					if (empty($arrItemKey[$i]))  
						continue;
					  
                        //$qty =  $this->unFormatNumber($arrQty[$i]);
                        $qtyinbaseunit = $arrItemDetail[$i]['qtyInBaseUnit'];
						$priceInUnit = $this->unFormatNumber($arrPriceinunit[$i]); 
                    
                        $unitDiscountedValue = $priceInUnit - $arrItemDetail[$i]['unitDiscountValue'] ;
						$priceInUnitBeforeTax = $unitDiscountedValue - (($unitDiscountedValue/$subtotal) * $totalFinalDiscount);
					
						if ($isPriceIncludeTax == true) { 
								$taxValue = ($taxPercentage/(100 + $taxPercentage)) * $priceInUnitBeforeTax;   
								$priceInUnitBeforeTax = $priceInUnitBeforeTax - $taxValue ;
						}   
                     
				} */ 
        
				$beforeTaxTotal = $subtotal - $totalFinalDiscount;
				$grandtotal = $beforeTaxTotal;
					 
 				if ($isPriceIncludeTax == false) {
						$taxValue = $beforeTaxTotal * $taxPercentage / 100;
						$grandtotal += $taxValue;
				}else{
						$taxValue = ($taxPercentage/(100 + $taxPercentage)) * $grandtotal;   
				 		$beforeTaxTotal = $grandtotal - $taxValue ;
				}
				 
				$grandtotal +=  $etcCost;
			 
			 	
				$balance = 0;
				$totalPayment = 0; 
         
                $termOfPayment = new TermOfPayment();
                $rsTOP = $termOfPayment->getDataRowById($arrParam['selTermOfPaymentKey']);  
                if ($rsTOP[0]['duedays'] == 0){ 
                    $payment = $arrParam['paymentMethodValue'] ?? [];
                    for($i=0;$i<count($payment);$i++){
                        $totalPayment += $this->unFormatNumber($payment[$i]);
                    } 
                }
         
                // calculate priceInUnitAfterDiscount
                for ($i=0; $i<count($arrItemKey); $i++) {
                    $priceInUnit = $this->unFormatNumber($arrPriceinunit[$i]);
					$discount = $this->unFormatNumber($arrDiscountValueInUnit[$i]);
					$discountType =  $this->unFormatNumber($arrDiscountType[$i]);
                    $qty =  $this->unFormatNumber($arrQty[$i]);
                    
                    if ($discount != 0 && $discountType == 2) 
                        $discount = $discount/100 * $priceInUnit;
                    
                    $detailAfterDiscount = $priceInUnit - $discount;
                    
                    //calculate total discount
                    $totaldiscount = 0;
                    if($subtotal !=0)
                        $totaldiscount = ($detailAfterDiscount / $subtotal) * $finalDiscount;
                    
                    $priceDetailAfterDiscount = $detailAfterDiscount - $totaldiscount;
                    $totalDetailAfterDiscount = $qty * $priceDetailAfterDiscount;
                    $arrItemDetail[$i]['priceInUnitAfterDiscount'] = $priceDetailAfterDiscount; 
                    
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
	        
    function normalizeParameter($arrParam, $trim = false){
            $item = new Item();
            $termOfPayment = new TermOfPayment();
         
            
            $arrParam = parent::normalizeParameter($arrParam);  

        
            $typekey = $arrParam['selType'];
            switch($typekey){
                case '1' : $arrParam['hidChassisKey'] = 0;
                          break;
                case '2' : $arrParam['hidCarKey'] = 0;
                          break;
                default : $arrParam['hidChassisKey'] = 0;  
                          $arrParam['hidCarKey'] = 0;
                          break;
            }
        
            $arrParam['recipientName'] = (empty($arrParam['recipientName'])) ? '' : $arrParam['recipientName'];
            $arrParam['recipientPhone'] = (empty($arrParam['recipientPhone'])) ? '' : $arrParam['recipientPhone'];
            $arrParam['recipientEmail'] = (empty($arrParam['recipientEmail'])) ? '' : $arrParam['recipientEmail'];
            $arrParam['recipientAddress'] = (empty($arrParam['recipientAddress'])) ? '' : $arrParam['recipientAddress']; 
            $arrParam['refCode'] = (empty($arrParam['refCode'])) ? '' : $arrParam['refCode']; 
    
            $arrItemkey = $arrParam['hidItemKey'];
            $arrQty = $arrParam['qty'];  
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
            $arrParam['subtotal'] = $reCountResult['subtotal'];
            $arrParam['beforeTaxTotal'] = $reCountResult['beforeTaxTotal'];
            $arrParam['chkIncludeTax'] = $reCountResult['isPriceIncludeTax'];
            $arrParam['grandtotal'] = $reCountResult['grandtotal'];
            $arrParam['totalPayment'] = $reCountResult['totalPayment'];
            $arrParam['balance'] = $reCountResult['balance']; 

             for ($i=0;$i<count($arrItemkey);$i++){ 
                $arrParam['qtyInBaseUnit'][$i] = $arrParam['detailCOGS'][$i]['qtyInBaseUnit'];
                $arrParam['unitConvMultiplier'][$i] = $arrParam['detailCOGS'][$i]['unitConvMultiplier'];
                $arrParam['priceInUnit'][$i] = $arrParam['detailCOGS'][$i]['priceInUnit']; 
                $arrParam['priceInBaseUnit'][$i] = $arrParam['detailCOGS'][$i]['priceInBaseUnit']; 
                $arrParam['cogs'][$i] = $arrParam['detailCOGS'][$i]['cogs']; 
                $arrParam['detailSubtotal'][$i] = $arrParam['detailCOGS'][$i]['detailSubtotal']; 
                $arrParam['priceInUnitAfterDiscount'][$i] = $arrParam['detailCOGS'][$i]['priceInUnitAfterDiscount']; 
  
                $arrParam['isPackage'][$i] = $arrParam['detailCOGS'][$i]['isPackage'];
                $arrParam['itemType'][$i] = $arrParam['detailCOGS'][$i]['itemType'];
                 
                 
//                $arrParam['taxValue'][$i] = $arrParam['detailCOGS'][$i]['taxValue'];
//                $arrParam['headerDiscount'][$i] = $arrParam['detailCOGS'][$i]['headerDiscount'];
//                $arrParam['headerEtcCost'][$i] = $arrParam['detailCOGS'][$i]['itemType'];
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
    
    function getCarMaintenanceHistory($carkey='',$criteria='',$order=''){ 

           $sql = 'select
                    '.$this->tableName .'.code as salescode, 
                    '.$this->tableName .'.trdate as salesdate, 
                    '.$this->tableName .'.executedate, 
                    '.$this->tableWarehouse .'.name as warehousename, 
                    '.$this->tableName .'.mileage, 
                    '.$this->tableCar .'.policenumber, 
                    '.$this->tableSupplier .'.name as suppliername, 
                    '.$this->tableNameDetail .'.qty, 
                    '.$this->tableNameDetail .'.qty * '.$this->tableNameDetail .'.priceinunit as total, 
                    '.$this->tableNameDetail .'.priceinunit, 
                    '.$this->tableNameDetail .'.newsnkey, 
                    '.$this->tableNameDetail .'.lastsn,
                    '.$this->tableNameDetail .'.newsn,
                    '.$this->tableNameDetail .'.priceinunitafterdiscount,  
                    '.$this->tableNameDetail .'.trdesc,  
                    '.$this->tableNameDetail .'.priceinunitafterdiscount * '.$this->tableNameDetail .'.qty  as totaldetailafterdiscount,  
                    '.$this->tableItemPosition .'.name as itemposition,  
                    '.$this->tableItemSN .'.serialnumber,
                    '.$this->tableItem.'.name as itemname, 
                    '.$this->tableItem.'.code as itemcode, 
                    '.$this->tableItem.'.itemtype,
                    '.$this->tableItem.'.deftransunitkey,
                    '.$this->tableItem.'.needsn,
                    '.$this->tableItemUnit.'.name as unitname ,
                    '.$this->tableEmployee.'.name as technicianname,
                    driver.name as drivername
                  from
                    '.$this->tableName .'
                        left join '.$this->tableSupplier.' on 	' . $this->tableName .'.supplierkey = '.$this->tableSupplier.'.pkey 
                        left join '.$this->tableEmployee.' on 	' . $this->tableName .'.techniciankey = '.$this->tableEmployee.'.pkey 
                        left join '.$this->tableEmployee.' driver on 	' . $this->tableName .'.driverkey = driver.pkey ,
                    '.$this->tableCar.',
                    '.$this->tableWarehouse.',
                    '.$this->tableNameDetail .'
                        left join '.$this->tableItemPosition.' on 	' . $this->tableNameDetail .'.itemposition = '.$this->tableItemPosition.'.pkey
                        left join '.$this->tableItemSN.' on 	' . $this->tableNameDetail .'.newsnkey = '.$this->tableItemSN.'.pkey,
                    '.$this->tableItemUnit.', 
                    '.$this->tableItem.'

                  where
				  	'.$this->tableName .'.typekey = 1 and
                    '.$this->tableNameDetail .'.itemkey = '.$this->tableItem.'.pkey and
                    '.$this->tableName .'.pkey = '.$this->tableNameDetail.'.refkey and
                    '.$this->tableName .'.carkey = '.$this->tableCar.'.pkey and
                    '.$this->tableName .'.warehousekey = '.$this->tableWarehouse.'.pkey and
                    '.$this->tableNameDetail.'.unitkey = '.$this->tableItemUnit.'.pkey and
                    '.$this->tableName .'.statuskey in(2,3)';
            
            
            if (!empty($carkey))
                  $sql .= ' and '.$this->tableCar.'.pkey = '. $this->oDbCon->paramString($carkey);
            
            if (!empty($criteria))
                  $sql .= $criteria;
            
            $sql .=' ' . $criteria;
            $sql .=' ' .$order;
            //$this->setLog($sql);

            return $this->oDbCon->doQuery($sql);

        }
     
function generateCarMaintenanceReport($criteria='',$order='',$pkey='',$itemkey = ''){ 
     
	   $sql =  '
			SELECT '.$this->tableName.'.pkey,
                   '.$this->tableName.'.code, 
                   '.$this->tableName.'.code, 
                   '.$this->tableName.'.grandtotal,
                   '.$this->tableName.'.mileage,
                   '.$this->tableName.'.taxvalue,
                   '.$this->tableName.'.trnotes,
                   '.$this->tableName.'.termofpaymentkey,
                   '.$this->tableName.'.beforetaxtotal,
                   '.$this->tableSupplier.'.name as suppliername,  
                   '.$this->tableName.'.trdate, 
                   '.$this->tableName.'.estdate, 
                    '.$this->tableEmployee.'.name as technicianname,
                   CONCAT_WS(\'\', '.$this->tableCar.'.code , '.$this->tableChassis.'.code) as vehiclecode ,
                   CONCAT_WS(\'\', '.$this->tableCar.'.policenumber , '.$this->tableChassis.'.chassisnumber) as policenumber,
                   '.$this->tableNameDetail.'.refkey,
                   '.$this->tableNameDetail.'.itemkey,
                   '.$this->tableNameDetail.'.trdesc as detaildesc,
                   '.$this->tableNameDetail.'.qtyinbaseunit,
                   '.$this->tableNameDetail.'.discount,
                   '.$this->tableNameDetail.'.priceinunit,
                   '.$this->tableNameDetail.'.total,
                   '.$this->tableUnit.'.name as unitname,
                   '.$this->tableItem.'.name as itemname,
                   '.$this->tableStatus.'.status as statusname , 
                   '.$this->tableWarehouse.'.name as warehousename  
			FROM 
                '.$this->tableStatus.',  
                 '.$this->tableItem.',
                 '.$this->tableNameDetail.'
                    left join '.$this->tableUnit.' on '.$this->tableNameDetail.'.unitkey = '.$this->tableUnit.'.pkey,
                '.$this->tableName.' 
                    left join '.$this->tableEmployee.' on  '.$this->tableName.'.techniciankey = '.$this->tableEmployee.'.pkey
                    left join '.$this->tableCar.' on '.$this->tableName.'.carkey = '.$this->tableCar.'.pkey
                    left join '.$this->tableChassis.' on '.$this->tableName.'.chassiskey = '.$this->tableChassis.'.pkey 
                    left join '.$this->tableSupplier.' on 	' . $this->tableName .'.supplierkey = '.$this->tableSupplier.'.pkey ,
                '.$this->tableWarehouse.',
                '.$this->tableItemCategory.'
			WHERE     
                '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey and  
                '.$this->tableNameDetail.'.itemkey = '.$this->tableItem.'.pkey and 
                '. $this->tableItem .'.categorykey = '. $this->tableItemCategory .'.pkey and
                '.$this->tableNameDetail.'.refkey = '.$this->tableName.'.pkey and
                '.$this->tableName.'.warehousekey = '.$this->tableWarehouse.'.pkey
 		'; 
        
        if (!empty($criteria))  
            $sql .=  ' ' .$criteria; 
        
        if (!empty($pkey))  
            $sql .=  '  and '.$this->tableName.'.pkey = ' .$this->oDbCon->paramString($pkey);

        
        if (!empty($order))  
            $sql .=  ' ' .$order;  
        
        
           
        return $this->oDbCon->doQuery($sql);
		 
    }

    function getChassisMaintenanceHistory($carkey='',$criteria='',$order=''){ 

           $sql = 'select
                    '.$this->tableName .'.code as salescode, 
                    '.$this->tableName .'.trdate as salesdate, 
                    '.$this->tableName .'.executedate,
                    '.$this->tableWarehouse .'.name as warehousename, 
                    '.$this->tableName .'.mileage, 
                    '.$this->tableChassis .'.chassisnumber, 
                    '.$this->tableSupplier .'.name as suppliername, 
                    '.$this->tableNameDetail .'.qty, 
                    '.$this->tableNameDetail .'.qty * '.$this->tableNameDetail .'.priceinunit as total, 
                    '.$this->tableNameDetail .'.priceinunit, 
                    '.$this->tableNameDetail .'.priceinunitafterdiscount,  
                    '.$this->tableNameDetail .'.priceinunitafterdiscount * '.$this->tableNameDetail .'.qty  as totaldetailafterdiscount,  
                    '.$this->tableItem.'.name as itemname, 
                    '.$this->tableItem.'.code as itemcode, 
                    '.$this->tableItem.'.itemtype,
                    '.$this->tableItem.'.deftransunitkey,
                    '.$this->tableItemUnit.'.name as unitname,
                    '.$this->tableEmployee.'.name as technicianname
                  from
                    '.$this->tableName .'
                        left join '.$this->tableEmployee.' on 	' . $this->tableName .'.techniciankey = '.$this->tableEmployee.'.pkey 
                        left join '.$this->tableSupplier.' on 	' . $this->tableName .'.supplierkey = '.$this->tableSupplier.'.pkey ,
                    '.$this->tableChassis.',
                    '.$this->tableWarehouse.',
                    '.$this->tableNameDetail .',
                    '.$this->tableItemUnit.', 
                    '.$this->tableItem.'

                  where
				  	'.$this->tableName .'.typekey = 2 and
                    '.$this->tableNameDetail .'.itemkey = '.$this->tableItem.'.pkey and
                    '.$this->tableName .'.pkey = '.$this->tableNameDetail.'.refkey and
                    '.$this->tableName .'.chassiskey = '.$this->tableChassis.'.pkey and
                    '.$this->tableName .'.warehousekey = '.$this->tableWarehouse.'.pkey and
                    '.$this->tableNameDetail.'.unitkey = '.$this->tableItemUnit.'.pkey and
                    '.$this->tableName .'.statuskey in(2,3)';
            
            
            if (!empty($carkey))
                  $sql .= ' and '.$this->tableChassis.'.pkey = '. $this->oDbCon->paramString($carkey);
            
            if (!empty($criteria))
                  $sql .= $criteria;
            
            $sql .=' ' . $criteria;
            $sql .=' ' .$order;

            return $this->oDbCon->doQuery($sql);

        }
    
          
    function getRelatedDataForCashBankReport($pkey){
        $arrReturn = array();
        
        $sql = 'select 
                    '. $this->tableName.'.pkey, 
                    '. $this->tableName.'.code as refcode,
                    '.$this->tableWarehouse.'.name as warehousename, 
                    CONCAT_WS(\'\', '.$this->tableCar.'.policenumber , '.$this->tableChassis.'.code) as policenumber ,
                    '.$this->tableSupplier.'.name as suppliername
                from 
                    '. $this->tableName.'
                        left join '.$this->tableCar.' on '. $this->tableName.'.carkey = '.$this->tableCar.'.pkey
                        left join '.$this->tableChassis.' on '.$this->tableName.'.chassiskey = '.$this->tableChassis.'.pkey
                        left join '.$this->tableSupplier.' on '. $this->tableName.'.supplierkey = '.$this->tableSupplier.'.pkey,
                    '.$this->tableWarehouse.' 
                where 
                    '. $this->tableName.'.pkey in ('.$this->oDbCon->paramString($pkey,',').') and 
                    '. $this->tableName.'.warehousekey = '. $this->tableWarehouse.'.pkey';
        
        
        $rs = $this->oDbCon->doQuery($sql); 
        $rs = array_column($rs, null,'pkey');
          
        return $rs;
    }
    
    function getMaintenanceSummaryByItem($criteria='', $carCriteria = '', $chassisCriteria = ''){
        
        $arrSQL = array();
         
        // mobil
        $sql = 'select   
                        '.$this->tableName.'.typekey,
                        '.$this->tableCar.'.pkey as carkey,
                        '.$this->tableCar.'.policenumber,
                        sum( ('.$this->tableNameDetail.'.priceinunitafterdiscount * '.$this->tableNameDetail.'.qty )  ) as total,
                        '.$this->tableItem.'.pkey as itemkey,
                        '.$this->tableItem.'.name as itemname,
                        '.$this->tableItem.'.categorykey as itemcategorykey,
                        '.$this->tableItem.'.itemtype
                        
                from
                        '.$this->tableName.',
                        '.$this->tableNameDetail.',
                        '.$this->tableItem.', 
                        '.$this->tableCar.'
                where
                         '.$this->tableName.'.pkey = '.$this->tableNameDetail.'.refkey and
                         '.$this->tableNameDetail.'.itemkey = '.$this->tableItem.'.pkey and
                         '.$this->tableName.'.carkey = '.$this->tableCar.'.pkey and 
                         '.$this->tableName.'.statuskey in (2,3)
                         '; 
        
        if (!empty($carCriteria)) $sql .= ' ' . $carCriteria; 
        if (!empty($criteria)) $sql .= ' ' . $criteria; 
        $sql .=' group by '.$this->tableName.'.typekey, '.$this->tableCar.'.pkey, '.$this->tableItem.'.pkey  ';
        array_push($arrSQL,$sql);
        
        // chassis
        $sql = 'select  
                        '.$this->tableName.'.typekey,
                        '.$this->tableChassis.'.pkey as carkey,
                        '.$this->tableChassis.'.chassisnumber as policenumber,
                        sum( ('.$this->tableNameDetail.'.priceinunitafterdiscount * '.$this->tableNameDetail.'.qty )  ) as total,
                        '.$this->tableItem.'.pkey as itemkey,
                        '.$this->tableItem.'.name as itemname,
                        '.$this->tableItem.'.categorykey as itemcategorykey,
                        '.$this->tableItem.'.itemtype
                        
                from
                        '.$this->tableName.',
                        '.$this->tableNameDetail.',
                        '.$this->tableItem.', 
                        '.$this->tableChassis.'
                where
                         '.$this->tableName.'.pkey = '.$this->tableNameDetail.'.refkey and
                         '.$this->tableNameDetail.'.itemkey = '.$this->tableItem.'.pkey and
                         '.$this->tableName.'.chassiskey = '.$this->tableChassis.'.pkey and 
                         '.$this->tableName.'.statuskey in (2,3)
                         '; 
        
        if (!empty($chassisCriteria)) $sql .= ' ' . $chassisCriteria;
        if (!empty($criteria)) $sql .= ' ' . $criteria;
        $sql .=' group by '.$this->tableName.'.typekey, '.$this->tableChassis.'.pkey, '.$this->tableItem.'.pkey  ';
        array_push($arrSQL,$sql);
        
        $sql = implode(' UNION ALL ' , $arrSQL); 
        
        $rs = $this->oDbCon->doQuery($sql);  
        return $rs;
        
    }



    function updateExecuteDate($pkey, $executeDate) 
    {
        	
		try{		
	  	
			if (!$this->oDbCon->startTrans())
                throw new Exception($this->errorMsg[100]);
            
			    $sql = 'update '.$this->tableName.' set executedate = '.$this->oDbCon->paramDate($executeDate).' where pkey = ' . $this->oDbCon->paramString($pkey);
                
                $this->oDbCon->execute($sql);                 
				$this->oDbCon->endTrans();  
                $this->addErrorLog(false, $this->lang['dataHasBeenSuccessfullyUpdated']);
			
		}catch(Exception $e){
			$this->oDbCon->rollback();
            $this->addErrorLog(false, $e->getMessage());
		}			
			
        return $this->getErrorLog();
    }



    function getServiceMaintenanceByMonth($startPeriod, $endPeriod, $carcategorykey, $warehousekey = '', $groupBy = '') 
    {
        $sql = '
            select
                '.$this->tableCar .'.categorykey,
                month('. $this->tableName .'.trdate) as month,
                DATE_FORMAT('. $this->tableName .'.trdate, \'%b\') as monthname,
                year('. $this->tableName .'.trdate) as year,
                sum('. $this->tableName .'.grandtotal) as total
            from
                '. $this->tableName .',
                '. $this->tableCar .'
            where
                '. $this->tableName .'.carkey = '. $this->tableCar .'.pkey and
                ('. $this->tableName .'.statuskey >= 2 and '. $this->tableName .'.statuskey <=3) and
                '. $this->tableName .'.trdate between \''. date("Y-m-d", strtotime($startPeriod)) .'\' and LAST_DAY(\''.date("Y-m-d 23:59", strtotime($endPeriod)).'\')
        ';
        
        if(!empty($carcategorykey)) 
            $sql .=' and '. $this->tableCar .'.categorykey in ('. $this->oDbCon->paramString($carcategorykey,',').' )'; 
        

        // khusus kalo user pilih warehouse
		if (!empty($warehousekey))
            $sql .= ' and ' . $this->tableName . '.warehousekey in ('. $this->oDbCon->paramString($warehousekey,',').' )';
		
        $sql .=  $this->getWarehouseCriteria() ;
		
        if(!empty($groupBy))
            $sql .= ' ' .$groupBy;
        else
            $sql .= ' group by categorykey,year(trdate),month(trdate)';

        $result = $this->oDbCon->doQuery($sql);
    
        return $result;
    }

    function getServiceCostMaintenance($startPeriod = '', $endPeriod = '', $warehousekey = '') 
    {
        $sql = '
            select 
                sum('. $this->tableName .'.grandtotal) as total
            from
                '. $this->tableName .'
            where
                ('. $this->tableName .'.statuskey >= 2 and '. $this->tableName .'.statuskey <=3) and
                '. $this->tableName .'.trdate between \''. date("Y-m-d", strtotime($startPeriod)) .'\' and LAST_DAY(\''.date("Y-m-d 23:59", strtotime($endPeriod)).'\')
        ';
		
        if (!empty($warehousekey))
			$sql .= ' and ' . $this->tableName . '.warehousekey in ('. $this->oDbCon->paramString($warehousekey,',').' )';

        $sql .=  $this->getWarehouseCriteria() ;
		

        $result = $this->oDbCon->doQuery($sql);
    
        return $result;
    }

    function getCostByCategoryKey($startPeriod = '', $endPeriod = '',$warehousekey='', $categorykey='', $groupby=''){
        // Sales Amount
        
        $sql = 'select 
                    DATE_FORMAT('. $this->tableName .'.trdate, "%Y-%m") as period,
                  sum('.$this->tableNameDetail.'.total)  as costamount,
                  '. $this->tableItem .'.name as itemname,
                  '. $this->tableItemCategory .'.pkey as categorykey,
                  '. $this->tableItemCategory .'.name as categoryname
                from 
                    '.$this->tableName.'
                    left join '. $this->tableNameDetail .' on '. $this->tableNameDetail .'.refkey = '. $this->tableName .'.pkey
                    left join '. $this->tableItem .' on '. $this->tableItem .'.pkey = '. $this->tableNameDetail .'.itemkey
                    left join '. $this->tableItemCategory .' on '. $this->tableItem .'.categorykey = '. $this->tableItemCategory .'.pkey
                where 
                    '.$this->tableName.'.statuskey in (2,3) and
                     trdate between \''. date("Y-m-01 00:00", strtotime($startPeriod)) .'\' and LAST_DAY(\''. date("Y-m-d 23:59", strtotime($endPeriod)) .'\')';    
       
		
		 if (!empty($warehousekey))
				$sql .= ' and warehousekey in ('. $this->oDbCon->paramString($warehousekey,',').' )';

		 if (!empty($categorykey))
				$sql .= ' and '. $this->tableItemCategory .'.pkey in ('. $this->oDbCon->paramString($categorykey,',').' )';

            
        $sql .=  $this->getWarehouseCriteria() ;

        if (!empty($groupby))
               $sql .=' group by ' .$groupby.', period';
        
        return $this->oDbCon->doQuery($sql); 
    }  
    
    }
?>
