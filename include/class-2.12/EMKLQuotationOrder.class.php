<?php
  
class EMKLQuotationOrder extends BaseClass{ 
 
    function __construct($jobType = ''){
		
		parent::__construct();
       
		$this->tableName = 'emkl_quotation_order_header'; 
        $this->tableNameDetailItem = 'emkl_quotation_order_detail_item';
        $this->tableNameDetailOrigin = 'emkl_quotation_order_detail_origin';
        $this->tableNameDetailFreight = 'emkl_quotation_order_detail_freight'; 
        $this->tablePriceDetailFreight = 'emkl_quotation_order_price_detail_freight';
        $this->tablePriceDetailOrigin = 'emkl_quotation_order_price_detail_origin';
        $this->tablePriceDetailDestination = 'emkl_quotation_order_price_detail_destination';
		$this->tableTermsandConditionDetail = 'emkl_quotation_order_terms_and_condition_detail'; 
		$this->tableStatus = 'emkl_quotation_order_status';
		$this->tableItem = 'item';
		$this->tableItemUnit = 'item_unit';
		$this->tableCustomer = 'customer';
        $this->tableVolumeDetail = 'emkl_quotation_order_detail_volume';
//        $this->tableEmklType = 'emkl_job_type'; 
//        $this->tableTermOfShipment = 'term_of_shipment'; 
        $this->tableTermsAndCondition = 'terms_and_conditions'; 
        $this->tableEmployee = 'employee';
        $this->tableSupplier = 'supplier';
        $this->tableCategory = 'service_category';
        $this->tableCurrency = 'currency';  
        $this->tableContainer = 'container';
        $this->tableContainerType = 'container_type';
        $this->tablePort = 'port';
        $this->tableLocation = 'location';
//        $this->tableShipmentTerm = 'shipment_term';
        
		$this->newLoad = true;        
        $this->tableContact = 'contact_person';
        $this->tableJobType = 'emkl_import_export';
        $this->tableTransportationType = 'emkl_air_sea'; 
        $this->tableLoadContainer = 'emkl_job_type';
        $this->tableVolumeUnit = 'emkl_volume_unit';
        $this->tableFreightTerm = 'emkl_freight_term';
        $this->tableWarehouse = 'warehouse';
        $this->tableBusinessUnit = 'business_unit';
	    $this->tableFile = 'emkl_quotation_order_file';

		$this->securityObject = 'EMKLQuotationOrder';
          
        $this->uploadFileFolder = 'emkl-quotation-order/';
        $this->isTransaction = true;
        $this->jobType = $jobType;  
        
        $this->tableNeedToBeCopyOnCancel = array($this->tableNameDetailItem, $this->tableNameDetailOrigin,$this->tableNameDetailFreight,
												$this->tablePriceDetailFreight,$this->tablePriceDetailOrigin,$this->tablePriceDetailDestination);
          
   
   		// FREIGHT
		$this->arrPriceDetailFreight = array();   
		$this->arrPriceDetailFreight['pkey'] = array('hidPriceDetailFreightKey');
		$this->arrPriceDetailFreight['refkey'] = array('hidDetailItemFreightKey','ref');
		$this->arrPriceDetailFreight['refheaderkey'] = array('pkey','ref');
		$this->arrPriceDetailFreight['containerkey'] = array('hidPriceDetailFreightContainerKey',array('mandatory' => true));  
		$this->arrPriceDetailFreight['price'] = array('priceFreight','number');  
		$this->arrPriceDetailFreight['cost'] = array('costCarrier','number');  


		$arrDetailPriceFreight = array(); 
		array_push($arrDetailPriceFreight, array('dataset' => $this->arrPriceDetailFreight, 'tableName' => $this->tablePriceDetailFreight)); 

		$this->arrDataItemDetailFreight = array();   
		$this->arrDataItemDetailFreight['pkey'] = array('hidDetailItemFreightKey', array('dataDetail' => $arrDetailPriceFreight)); 
        $this->arrDataItemDetailFreight['refkey'] = array('pkey','ref');  
        $this->arrDataItemDetailFreight['servicekey'] = array('hidServiceFreightKey',array('mandatory' => true)); 
        $this->arrDataItemDetailFreight['unitkey'] = array('hidUnitFreightDetailKey');  
        $this->arrDataItemDetailFreight['polkey'] = array('hidDetailFreightPOLKey');  
        $this->arrDataItemDetailFreight['podkey'] = array('hidDetailFreightPODKey');  
        $this->arrDataItemDetailFreight['carrierkey'] = array('hidCarrierDetailKey');  
 	    $this->arrDataItemDetailFreight['currencykey'] = array('selCurrencyDetail');  
        $this->arrDataItemDetailFreight['isperreciept'] = array('chkIsReimburseFreight'); 
 	    $this->arrDataItemDetailFreight['taxpercentage'] = array('taxPercentageCarrier','number');  
 	    $this->arrDataItemDetailFreight['ispriceincludetax'] = array('chkIncludeTaxCarrierDetail');  
        $this->arrDataItemDetailFreight['remarks'] = array('carrierRemarks');  
        $this->arrDataItemDetailFreight['alias'] = array('aliasCarrier'); 
        $this->arrDataItemDetailFreight['rateprice'] = array('rateFreight','number');  
     	$this->arrDataItemDetailFreight['orderlist'] = array('hidOrderListFreight'); 
      
		// ORIGIN       
		$this->arrPriceDetailOrigin = array();   
        $this->arrPriceDetailOrigin['pkey'] = array('hidPriceDetailOriginKey');
        $this->arrPriceDetailOrigin['refkey'] = array('hidDetailItemOriginKey','ref');
        $this->arrPriceDetailOrigin['refheaderkey'] = array('pkey','ref');
        $this->arrPriceDetailOrigin['containerkey'] = array('hidPriceDetailOriginContainerKey',array('mandatory' => true));  
        $this->arrPriceDetailOrigin['price'] = array('priceOrigin','number',array('mandatory' => true));   
 
        
        $arrDetailPriceOrigin = array(); 
        array_push($arrDetailPriceOrigin, array('dataset' => $this->arrPriceDetailOrigin, 'tableName' => $this->tablePriceDetailOrigin)); 

        $this->arrDataDetailOrigin = array();   
        $this->arrDataDetailOrigin['pkey'] = array('hidDetailItemOriginKey', array('dataDetail' => $arrDetailPriceOrigin)); 
        $this->arrDataDetailOrigin['refkey'] = array('pkey','ref'); 
        $this->arrDataDetailOrigin['servicekey'] = array('hidServiceOriginKey',array('mandatory' => true)); 
        $this->arrDataDetailOrigin['remarks'] = array('serviceOriginRemarks'); 
        $this->arrDataDetailOrigin['locationzonekey'] = array('hidDetailZoneKey'); 
        $this->arrDataDetailOrigin['locationpickupkey'] = array('hidDetailPickupZoneKey'); 
        $this->arrDataDetailOrigin['currencykey'] = array('selCurrencyOriginDetail'); 
        $this->arrDataDetailOrigin['unitkey'] = array('hidUnitOriginDetailKey'); 
 	    $this->arrDataDetailOrigin['isperreciept'] = array('chkIsReimburseOrigin'); 
        $this->arrDataDetailOrigin['taxpercentage'] = array('taxPercentageOrigin','number');  
 	    $this->arrDataDetailOrigin['ispriceincludetax'] = array('chkIncludeTaxOriginDetail');  
        $this->arrDataDetailOrigin['remarks'] = array('serviceOriginRemarks');  
        $this->arrDataDetailOrigin['alias'] = array('aliasOrigin'); 
        $this->arrDataDetailOrigin['rateprice'] = array('ratePriceOrigin','number'); 
     	$this->arrDataDetailOrigin['orderlist'] = array('hidOrderListOrigin'); 
        
                 
		// DESTINATION
		          
        $this->arrPriceDetailDestination = array();   
        $this->arrPriceDetailDestination['pkey'] = array('hidPriceDetailDestinationKey');
        $this->arrPriceDetailDestination['refkey'] = array('hidDetailItemDestinationKey','ref');
        $this->arrPriceDetailDestination['refheaderkey'] = array('pkey','ref');
        $this->arrPriceDetailDestination['containerkey'] = array('hidPriceDetailDestinationContainerKey',array('mandatory' => true));  
        $this->arrPriceDetailDestination['price'] = array('priceDestination','number',array('mandatory' => true));  
        
        $arrDetailPriceDestination = array(); 
        array_push($arrDetailPriceDestination, array('dataset' => $this->arrPriceDetailDestination, 'tableName' => $this->tablePriceDetailDestination)); 
        
        $this->arrDataDetailDestination = array(); 
        $this->arrDataDetailDestination['pkey'] = array('hidDetailItemDestinationKey', array('dataDetail' => $arrDetailPriceDestination));
      	$this->arrDataDetailDestination['refkey'] = array('pkey', 'ref');
        $this->arrDataDetailDestination['servicekey'] = array('hidServiceDestinationKey',array('mandatory' => true)); 
 	    $this->arrDataDetailDestination['unitkey'] = array('hidUnitItemDetailKey'); 
 	    $this->arrDataDetailDestination['currencykey'] = array('selCurrencyItemDetail');  
        $this->arrDataDetailDestination['locationzonekey'] = array('hidDetailLocationZoneKey'); 
        $this->arrDataDetailDestination['locationpickupkey'] = array('hidDetailLocationPickupKey'); 
 	    $this->arrDataDetailDestination['isperreciept'] = array('chkIsReimburse');  
 	    $this->arrDataDetailDestination['remarks'] = array('serviceDestinationRemarks');
        $this->arrDataDetailDestination['taxpercentage'] = array('taxPercentageService','number');  
 	    $this->arrDataDetailDestination['ispriceincludetax'] = array('chkIncludeTaxServiceDetail');  
        $this->arrDataDetailDestination['alias'] = array('aliasService'); 
        $this->arrDataDetailDestination['rateprice'] = array('ratePriceDestination','number'); 
     	$this->arrDataDetailDestination['orderlist'] = array('hidOrderListDestination'); 
		
		$this->arrTermsConditionDetail = array(); 
        $this->arrTermsConditionDetail['pkey'] = array('hidDetailTermsConditionKey');
        $this->arrTermsConditionDetail['refkey'] = array('pkey', 'ref');
        $this->arrTermsConditionDetail['termsconditionkey'] = array('selTermsConditionKey');
        $this->arrVolumeDetail = array();
        $this->arrVolumeDetail['pkey'] = array('hidDetailVolumeKey');
        $this->arrVolumeDetail['refkey'] = array('pkey', 'ref');
        $this->arrVolumeDetail['itemkey'] = array('selContainerDetailVolumeKey');
        $this->arrVolumeDetail['qty'] = array('qtyVolume', 'number');

        $arrDetails = array();  
        array_push($arrDetails, array('dataset' => $this->arrDataItemDetailFreight, 'tableName' => $this->tableNameDetailFreight));    
        array_push($arrDetails, array('dataset' => $this->arrVolumeDetail, 'tableName' => $this->tableVolumeDetail));   
        array_push($arrDetails, array('dataset' => $this->arrDataDetailDestination, 'tableName' => $this->tableNameDetailItem));
        array_push($arrDetails, array('dataset' => $this->arrDataDetailOrigin, 'tableName' => $this->tableNameDetailOrigin)); 
        array_push($arrDetails, array('dataset' => $this->arrTermsConditionDetail, 'tableName' => $this->tableTermsandConditionDetail));
        array_push($arrDetails, array('dataset' => $this->arrDataFile, 'tableName' => $this->tableFile, 
									  'datatype' => 'file', 'uploadFolder' => $this->uploadFileFolder,
									  'token' => 'token-item-file-uploader', 'fileName' => 'item-file-uploader')); 
        
        $this->arrData = array(); 
        $this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails));
        $this->arrData['code'] = array('code');
        $this->arrData['trdate'] = array('trDate','date');   
        $this->arrData['expdate'] = array('validDate','date');   
        $this->arrData['customerkey'] = array('hidCustomerKey');
        $this->arrData['warehousekey'] = array('selWarehouseKey');       
        $this->arrData['quotationkey'] = array('hidQuotationKey');
        $this->arrData['polkey'] = array('hidPOLKey');
        $this->arrData['podkey'] = array('hidPODKey'); 
        $this->arrData['finalpodkey'] = array('hidFinalPODKey'); 
        $this->arrData['saleskey'] = array('hidSalesKey'); 
        $this->arrData['termofshipmentkey'] = array('selTermOfShipment');
        $this->arrData['isshowcurrency'] = array('chkIsShowCurrency');
        $this->arrData['transportationtypekey'] = array('selAirSea');
        $this->arrData['pickey'] = array('selPIC');
        $this->arrData['loadcontainertypekey'] = array('selContainerType');
        $this->arrData['containertypekey'] = array('hidCargoType');
        $this->arrData['trdesc'] = array('trDesc');
        $this->arrData['subtotal'] = array('subtotal','number');
        $this->arrData['statuskey'] = array('selStatus');
        $this->arrData['jobtypekey'] = array('selTypeOfJob');
        $this->arrData['currencykey'] = array('selCurrency');
        $this->arrData['itemdescription'] = array('itemDescription');
        $this->arrData['grandtotal'] = array('total','number'); 
        $this->arrData['rate'] = array('currencyRate','number');
        $this->arrData['totalselling'] = array('totalSelling','number');
        $this->arrData['totalbuying'] = array('totalBuying','number');
        $this->arrData['totalmargin'] = array('totalMargin','number');
        $this->arrData['totalpercentagemargin'] = array('totalPercentageMargin','number');
        $this->arrData['totalsellingcurrency'] = array('totalSellingCurrency','number');
        $this->arrData['totalbuyingcurrency'] = array('totalBuyingCurrency','number');
        $this->arrData['totalmargincurrency'] = array('totalMarginCurrency','number');
//        $this->arrData['shipmenttermkey'] = array('shipmentTermKey');
//        $this->arrData['shipmentterm2key'] = array('shipmentTerm2Key'); 
        $this->arrData['locationcache'] = array('locationCache');   
        $this->arrData['showtop'] = array('chkIsShowTOP');    
        $this->arrData['headertext'] = array('txtHeader');   
        $this->arrData['termsandconditions'] = array('txtTermsAndConditions');    

        $this->arrData['itemkey'] = array('hidContainerKey');
        $this->arrData['totalvolume'] = array('volume', 'number');

        $this->arrDataListAvailableColumn = array();
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code', 'default'=>true, 'width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'date','title' => 'date','dbfield' => 'trdate','default'=>true, 'width' => 80, 'align' =>'center', 'format' => 'date'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'warehouse','title' => 'warehouse','dbfield' => 'warehousename', 'width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'expdate','title' => 'expDate','dbfield' => 'expdate', 'width' => 80, 'align' =>'center', 'format' => 'date'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'costumer','title' => 'customer','dbfield' => 'customername','default'=>true,'width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'jobType','title' => 'jobType','dbfield' => 'jobtypeunion', 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'salesman','title' => 'salesman','dbfield' => 'salesname','default'=>true,'width' => 200));
    	array_push($this->arrDataListAvailableColumn, array('code' => 'location','title' => 'location','dbfield' => 'locationcache','default'=>true,'width' => 350));
        array_push($this->arrDataListAvailableColumn, array('code' => 'note','title' => 'note','dbfield' => 'trdesc','width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 80));
 
		 
        array_push($this->filterCriteria, array('title' => $this->lang['warehouse'], 'field' => 'warehousekey'));
        
        $this->arrSearchColumn = array ();
        array_push($this->arrSearchColumn, array('Kode', $this->tableName . '.code'));
        array_push($this->arrSearchColumn, array('Tanggal', $this->tableName . '.trdate'));   
        array_push($this->arrSearchColumn, array('Pelanggan', $this->tableCustomer.'.name')); 
        array_push($this->arrSearchColumn, array(ucwords($this->lang['jobType']), $this->tableJobType. '.name')); 
        array_push($this->arrSearchColumn, array(ucwords($this->lang['jobType']), $this->tableTransportationType. '.name')); 
        array_push($this->arrSearchColumn, array(ucwords($this->lang['jobType']), $this->tableLoadContainer. '.name')); 
        array_push($this->arrSearchColumn, array('Sales', $this->tableEmployee. '.name'));
        array_push($this->arrSearchColumn, array('Catatan', $this->tableName. '.trdesc')); 
        array_push($this->arrSearchColumn, array('Container Type',  $this->tableContainerType. '.name')); 
        array_push($this->arrSearchColumn, array('Location',  $this->tableName. '.locationcache'));
        array_push($this->arrSearchColumn, array('Status',  $this->tableStatus. '.status'));
        
        $this->printMenu = array();  
        array_push($this->printMenu,array('code' => 'printTransaction', 'name' => $this->lang['printTransaction'],  'icon' => 'print', 'url' => 'print/emklQuotationOrder'));
 
        
        // shortcut
        $this->actionMenu = array();  
        
        switch($jobType){
            case EMKL['jobType']['import'] : $form = 'emklJobOrderImportForm';
                                                 $label = $this->lang['importOrderSheet'];
                                                 break;
            case EMKL['jobType']['export'] : $form = 'emklJobOrderExportForm';
                                                 $label = $this->lang['exportOrderSheet'];
                                                 break;  
            case EMKL['jobType']['domestic'] : $form = 'emklJobOrderDomesticForm';
                                                 $label = $this->lang['domesticOrderSheet'];
                                                 break;  
			default :  $form = 'emklJobOrderImportForm';
                                                 $label = $this->lang['importOrderSheet'];
                                                 break;
        }
        
		
		// update path personalized 
		$form = $this->getPersonalizedFiles($form);
 
        array_push($this->actionMenu,array('code' => 'duplicate', 'name' => $this->lang['duplicateData'],  'icon' => 'duplicate')); 
 
        $function = ' openTabForShortCutAdd("'.$form.'",{"title" : "'.$label.'"});';
        array_push($this->actionMenu,array('code' => 'jobOrder', 'name' => $label,  'icon' => 'resync', 'function' => $function)); 
 
        
        $this->includeClassDependencies(array(
              'Port.class.php',
              'Container.class.php',
              'Currency.class.php',
              'CurrencyRate.class.php',
              'Employee.class.php',
              'ServiceCategory.class.php',
              'Customer.class.php',
              'Warehouse.class.php',
              'ItemUnit.class.php',
              'Item.class.php', 
              'City.class.php',
              'Supplier.class.php',
              'Location.class.php',
              'EMKLJobOrder.class.php',
              'TermOfPayment.class.php',
              'Currency.class.php', 
              'Service.class.php',
              'Depot.class.php',
              'Consignee.class.php',
              'ItemUnit.class.php',
              'BusinessUnit.class.php', 
			  'TermsAndConditions.class.php'
        ));
        
        $this->overwriteConfig();
        
   }
   
  function getQuery(){
	   
	  
//                concat(term1.name,\' - \',term2.name) as shipmenttermname, 
//                    left join '.$this->tableShipmentTerm.' term1 on  '.$this->tableName.'.shipmenttermkey = term1.pkey 
//                    left join '.$this->tableShipmentTerm.' term2 on  '.$this->tableName.'.shipmentterm2key = term2.pkey
						
        $sql = '
			SELECT '.$this->tableName.'.* ,
              '.$this->tableCustomer.'.name as customername, 
              '.$this->tableEmployee.'.name as salesname,
              '.$this->tableWarehouse.'.name as warehousename, 
		      '.$this->tableJobType.'.name as jobtype ,
			  '.$this->tableCurrency.'.name as currencyname, 
			  '.$this->tableTransportationType.'.name as transportationtype,
              '.$this->tableLoadContainer.'.name as loadcontainertype,
               pol.name as polname,
               pod.name as podname,
               finalpod.name as finalpodname,
              concat_ws(", ",'.$this->tableJobType.'.name,'.$this->tableTransportationType.'.name,'.$this->tableLoadContainer.'.name) as jobtypeunion,
              '.$this->tableContainerType.'.name as containertype ,
			  '.$this->tableStatus.'.status as statusname
			FROM 
                 '.$this->tableName.'
                    left join '.$this->tableCurrency.' on  '.$this->tableName.'.currencykey = '.$this->tableCurrency.'.pkey 
                    left join '.$this->tableCustomer.' on '.$this->tableName.'.customerkey = '.$this->tableCustomer.'.pkey 
                    left join '.$this->tableEmployee.' on '.$this->tableName.'.saleskey = '.$this->tableEmployee.'.pkey
                    left join '.$this->tablePort.' pol on  '.$this->tableName.'.polkey = pol.pkey 
                    left join '.$this->tablePort.' pod on  '.$this->tableName.'.podkey = pod.pkey
                    left join '.$this->tablePort.' finalpod on  '.$this->tableName.'.finalpodkey = finalpod.pkey
                    left join '.$this->tableContainerType.' on  '.$this->tableName.'.containertypekey = '.$this->tableContainerType.'.pkey  ,
                 '.$this->tableWarehouse.',
                 '.$this->tableJobType.',
                 '.$this->tableTransportationType.',
                 '.$this->tableLoadContainer.', 
                 '.$this->tableStatus.'
			WHERE 
                '.$this->tableName.'.jobtypekey = '.$this->tableJobType.'.pkey and 
                '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey and 
                '.$this->tableName.'.transportationtypekey = '.$this->tableTransportationType.'.pkey and
                '.$this->tableName.'.loadcontainertypekey = '.$this->tableLoadContainer.'.pkey and 
                '.$this->tableName.'.warehousekey = '.$this->tableWarehouse.'.pkey'
            . $this->criteria;

        if (!empty($this->jobType))
            $sql .= ' and '.$this->tableName.'.jobtypekey in ('.$this->jobType.')  ';
        
      
        $sql .= $this->getWarehouseCriteria() ;

        return $sql;
    } 
    
    
    
     function getJobType($pkey=''){ 
       
	   $sql = 'select
	   			'.$this->tableJobType .'.pkey, 
	   			'.$this->tableJobType .'.name 
              from
			  	'.$this->tableJobType .' 
			  where
			  	'.$this->tableJobType .'.statuskey = 1';
                
        if(!empty($pkey))
            $sql .= ' and pkey = '.$this->oDbCon->paramString($pkey);
         
		return $this->oDbCon->doQuery($sql);
	
   }
     
    
     function getFreightTerm($pkey=''){ 

           $sql = 'select
                    '.$this->tableFreightTerm .'.pkey, 
                    '.$this->tableFreightTerm .'.name 
                  from
                    '.$this->tableFreightTerm .' 
                  where
                    '.$this->tableFreightTerm .'.statuskey = 1';

            if(!empty($pkey))
                $sql .= ' and pkey = '.$this->oDbCon->paramString($pkey);

            $sql .=' order by pkey asc';

            return $this->oDbCon->doQuery($sql);

       }
    
    function getTransportationType($pkey=''){ 
       
	   $sql = 'select
	   			'.$this->tableTransportationType .'.pkey, 
	   			'.$this->tableTransportationType .'.name 
              from
			  	'.$this->tableTransportationType .' 
			  where
			  	'.$this->tableTransportationType .'.statuskey = 1';
       if(!empty($pkey))
            $sql .= ' and pkey = '.$this->oDbCon->paramString($pkey);
         
       $sql .=' order by orderlist asc';
         
       return $this->oDbCon->doQuery($sql);
	
   }
   
    function getEmklType($pkey='',$criteria = ''){ 
       
	   $sql = 'select
	   			'.$this->tableLoadContainer .'.pkey, 
	   			'.$this->tableLoadContainer .'.name 
              from
			  	'.$this->tableLoadContainer .' 
			  where
			  	'.$this->tableLoadContainer .'.statuskey = 1';
                
        if(!empty($pkey))
            $sql .= ' and pkey in ( '.$this->oDbCon->paramString($pkey,',').')';
                
        if(!empty($criteria))
            $sql .= $criteria;
        
        
        $sql .=' order by orderlist asc, pkey asc';
        
         
		return $this->oDbCon->doQuery($sql);
	
   }
    
//  function getTermOfShipment($pkey=''){ 
//       
//	   $sql = 'select
//	   			'.$this->tableTermOfShipment .'.pkey, 
//	   			'.$this->tableTermOfShipment .'.name 
//              from
//			  	'.$this->tableTermOfShipment .' 
//			  where
//			  	'.$this->tableTermOfShipment .'.statuskey = 1';
//      
//       if(!empty($pkey))
//            $sql .= ' and pkey = '.$this->oDbCon->paramString($pkey);
//        
//       $sql .=' order by name asc';
//         
//       return $this->oDbCon->doQuery($sql);
//	
//   }
      
    function validateForm($arr,$pkey = ''){
        $port = new Port();
        $termsAndConditions = new TermsAndConditions();
        $container = new Container();
        $service = new Service(SERVICE);
        $employee = new Employee();

		$arrayToJs = parent::validateForm($arr,$pkey); 
		        
		$transportationtypekey = $arr['selAirSea'];  
		$jobtypekey = $arr['selTypeOfJob'];  
		$containertypekey = $arr['selContainerType'];  
		$arrTACKey = $arr['selTermsConditionKey'];   
        
        
        $arrPOLKey= $arr['hidDetailPOLKey'];  
		$arrPODKey= $arr['hidDetailPODKey'];  
        
        $arrPickupKey= $arr['hidPickupDetailKey'];  
		$arrPickupZoneKey= $arr['hidPickupZoneDetailKey'];         
        
        $arrLocationPickupKey= $arr['hidLocationPickupDetailKey'];  
		$arrLocationZoneKey= $arr['hidLocationZoneDetailKey'];  
        
        $arrDetailOriginKey = $arr['hidDetailItemOriginKey'];
        $arrDetailDestinationKey = $arr['hidDetailItemDestinationKey'];
        $arrDetailCarrierKey = $arr['hidDetailItemFreightKey'];
        
        $arrServiceDetailOriginKey = $arr['hidServiceOriginKey'];
        $arrServiceDetailFreightKey = $arr['hidServiceFreightKey'];
        $arrServiceDetailDestinationKey = $arr['hidServiceDestinationKey'];
        
//		$this->setLog($arrServiceDetailFreightKey,true);
		
        $arrchkIsReimburseDestination = $arr['chkIsReimburse'];
        $arrchkIsReimburseOrigin = $arr['chkIsReimburseOrigin'];
        $arrchkIsReimburseFreight = $arr['chkIsReimburseFreight'];
        
        $transcontainerkey = $transportationtypekey.'-'.$containertypekey;
        $criteriaTransportation = '';
        $criteriaContainer = '';

		
		// sementara tidka perlu divalidasi dulu
		
//		if($transportationtypekey == EMKL['shipping']['sea']){
//			$criteriaTransportation .= ' and '.$port->tableName.'.issea = 1';
//			$criteriaContainer .= ' and '.$container->tableName.'.issea = 1';
//		}else if($transportationtypekey == EMKL['shipping']['air']){
//			$criteriaTransportation .= ' and '.$port->tableName.'.isair = 1';
//			$criteriaContainer .= ' and '.$container->tableName.'.isair = 1';
//
//		}else if($transportationtypekey == EMKL['shipping']['land']){
//			$criteriaTransportation .= ' and '.$port->tableName.'.island = 1';
//			$criteriaContainer .= ' and '.$container->tableName.'.island = 1';
//		}


        //search pol 
        $rsPOLCol = $port->searchDataRow(array($port->tableName.'.pkey', $port->tableName.'.name'), 
                                         ' and '.$port->tableName.'.pkey in ('.$this->oDbCon->paramString($arrPOLKey,',').')');

        $rsPOLCol = array_column($rsPOLCol,null,'pkey');

        //search pol by criteria
        $rsPOLCriteria = $port->searchDataRow(array($port->tableName.'.pkey', $port->tableName.'.name'), 
                                         ' and '.$port->tableName.'.pkey in ('.$this->oDbCon->paramString($arrPOLKey,',').')'.$criteriaTransportation );

        $arrPOL = array_column($rsPOLCriteria,'pkey');
            
        
        //validate detail harus ada
       if (empty($arrServiceDetailOriginKey[0]) && empty($arrServiceDetailDestinationKey[0]) && empty($arrServiceDetailFreightKey[0])) 
            $this->addErrorList($arrayToJs,false,'<strong>' . $arr['code'] . '</strong>. ' . $this->errorMsg[501]); 
         
        
        foreach($arrPOLKey as $polkey){
            $rsPOL = $rsPOLCol[$polkey];
 
            if(!empty($polkey)){
                if(!in_array($polkey,$arrPOL))
                    $this->addErrorList($arrayToJs,false,'<b>'.$rsPOL['name'].'</b>. '.$this->errorMsg['port'][5]);  
            }
        }
        
        //validasi komiditi 
//        $arrCommodity = $arr['commodityName'];
//        for($j=0;$j<count($arrCommodity);$j++){
//             if(empty($arr['commodityName'][$j]))
//                    $this->addErrorList($arrayToJs,false,$this->errorMsg['commodity'][1]);
//        }
            
//        //search pod
//        $rsPODCol = $port->searchDataRow(array($port->tableName.'.pkey',$port->tableName.'.name'), 'and '.$port->tableName.'.pkey in ('.$this->oDbCon->paramString($arrPODKey,',').')' );
//        $rsPODCol = array_column($rsPODCol,null,'pkey');
//        
//        //search pod by criteria
//        $rsDestination = $port->searchDataRow(array($port->tableName.'.pkey',$port->tableName.'.name'), 'and '.$port->tableName.'.pkey in ('.$this->oDbCon->paramString($arrPODKey,',').')'.$criteriaTransportation );
//        $arrPOD = array_column($rsDestination,'pkey');
//        
//        foreach($arrPODKey as $podkey){
//            $rsPOD = $rsPODCol[$podkey]; 
//            if(!empty($podkey)){
//                if(!in_array($podkey,$arrPOD))
//                    $this->addErrorList($arrayToJs,false,'<b>'.$rsPOD['name'].'</b>. '.$this->errorMsg['port'][5]);  
//            }
//        }
        
       $rsJobType = $this->getJobType($jobtypekey); 
		
       $criteriaJobType = ' and '.$termsAndConditions->tableName.'.categorykey = '.$this->oDbCon->paramString($rsJobType[0]['pkey']);
        
       $rsTACCol = $termsAndConditions->searchDataRow(array($termsAndConditions->tableName.'.pkey', $termsAndConditions->tableName.'.name'), 
                                                     ' and '.$termsAndConditions->tableName.'.pkey in ('.$this->oDbCon->paramString($arrTACKey,',').')'.$criteriaJobType );
       $rsTACCol = array_column($rsTACCol,null,'pkey');
       $arrTAC = array_column($rsTACCol,'pkey');

        foreach($arrTACKey as $tackey){
            $rsTAC = $rsTACCol[$tackey]; 
            if(!empty($tackey)){
                if(!in_array($tackey,$arrTAC))
                    $this->addErrorList($arrayToJs,false,'<b>'.$rsTAC['name'].'</b>. '.$this->errorMsg['termsAndCondition'][5]); 
            }
        }
        
            
         if(!in_array($arr['validDate'],ARR_DB_EMPTY_DATE)){

            $trDate = str_replace('\'','',$this->oDbCon->paramDate($arr['trDate'],' / ','Y-m-d'));
            $validDate = str_replace('\'','',$this->oDbCon->paramDate($arr['validDate'],' / ','Y-m-d'));
 
             //tanggal valid tidak boleh kecil dari tanggal quotation;
            $quotationDate = strtotime($trDate);
            $expDate = strtotime($validDate);

                     
           if($expDate <= $quotationDate)
              $this->addErrorList($arrayToJs,false,$this->errorMsg['emklQuotation'][6]);   
        }
        
        $rsContainer = $container->searchData($container->tableName.'.statuskey',1,true,$criteriaContainer);
        
        $arrContainerOriginList = array();
        $arrContainerFreightList = array();
        $arrContainerDestinationList = array();
        for ($k=0;$k<count($rsContainer);$k++){
            
            array_push($arrContainerOriginList,$arr['chkContainerOrigin'.$rsContainer[$k]['pkey']][0]);
            array_push($arrContainerFreightList,$arr['chkContainerFreight'.$rsContainer[$k]['pkey']][0]);
            array_push($arrContainerDestinationList,$arr['chkContainerDestination'.$rsContainer[$k]['pkey']][0]);
            
        }
        
        
        //VALIDASI CONTAINER HARUS CHECKLIST SALAH SATU
        $arrChkContainerFreight = array($arr['chkContainerFreight-1'][0],$arr['chkContainerFreight-2'][0],$arr['chkContainerFreight-4'][0]);
        $arrChkContainerOrigin = array($arr['chkContainerOrigin-1'][0],$arr['chkContainerOrigin-2'][0],$arr['chkContainerOrigin-4'][0]);
        $arrChkContainerDestination = array($arr['chkContainerDestination-1'][0],$arr['chkContainerDestination-2'][0],$arr['chkContainerDestination-4'][0]);
        
        $arrContainerOrigin = array_merge($arrContainerOriginList,$arrChkContainerOrigin);
        $arrContainerFreight = array_merge($arrContainerFreightList,$arrChkContainerFreight);
        $arrContainerDestination = array_merge($arrContainerDestinationList,$arrChkContainerDestination);
        
        
        //salah satu ada yang checklist origin dan harus ada service nya
        if(!in_array(1,$arrContainerOrigin) && !empty($arr['hidServiceOriginKey'][0]))
              $this->addErrorList($arrayToJs,false,$this->errorMsg['emklQuotation'][17]); 

        if(!in_array(1,$arrContainerFreight) && !empty($arr['hidServiceFreightKey'][0]))
              $this->addErrorList($arrayToJs,false,$this->errorMsg['emklQuotation'][17]); 
        
        if(!in_array(1,$arrContainerDestination) && !empty($arr['hidServiceDestinationKey'][0]))
              $this->addErrorList($arrayToJs,false,$this->errorMsg['emklQuotation'][17]); 
         
            
        //VALIDASI UNTUK SETIAP COST DAN SELLING TIDAK BOLEH 0 
        //freight
		
        for($j=0;$j<count($arrDetailCarrierKey);$j++){
                    

				if(empty($arr['hidServiceFreightKey'][$j])) continue;
 
				$arrContainerLCL = $this->unFormatNumber($arr['rateFreight'][$j]); 
                  
				if($arr['chkContainerFreight-1'][0] == 1 && $arrContainerLCL <= 0){ 
					 $this->addErrorList($arrayToJs,false,'<b>Freight  - Rate</b>. '.$this->errorMsg['emklQuotation'][9]); 
				}
                
                for ($k=0;$k<count($rsContainer);$k++){ 
					$arrContainerFreight = $this->unFormatNumber($arr['containerFreight_'.$rsContainer[$k]['pkey']][$j]); 
					
					if($arr['chkContainerFreight'.$rsContainer[$k]['pkey']][0] == 1){ 
						if($arrchkIsReimburseFreight[$j] <> 1 && $arrContainerFreight <= 0){  
								 $this->addErrorList($arrayToJs,false,'<b>Freight - '.$rsContainer[$k]['name'].'</b>. '.$this->errorMsg['emklQuotation'][9]);  
						} 
				   }  
                }   
                
            }
        
        //origin
        for($i=0;$i<count($arrDetailOriginKey);$i++){
            
            if(empty($arr['hidServiceOriginKey'][$i])) continue;
             
            $arrContainerOriginLCL = $this->unFormatNumber($arr['ratePriceOrigin'][$i]);  
              
			if($arr['chkContainerOrigin-1'][0] == 1){ 
				if($arrchkIsReimburseOrigin[$i] <> 1 && $arrContainerOriginLCL <= 0){ 
					 $this->addErrorList($arrayToJs,false,'<b>Origin Charge - Rate</b>. '.$this->errorMsg['emklQuotation'][9]);  
				}
			}

			for ($k=0;$k<count($rsContainer);$k++){
					$arrContainerOrigin = $this->unFormatNumber($arr['containerOrigin_'.$rsContainer[$k]['pkey']][$i]); 
				 
					if($arr['chkContainerOrigin'.$rsContainer[$k]['pkey']][0] == 1){ 
						if($arrchkIsReimburseOrigin[$i] <> 1 && $arrContainerOrigin <= 0){  
								 $this->addErrorList($arrayToJs,false,'<b>Origin Charge - '.$rsContainer[$k]['name'].'</b>. '.$this->errorMsg['emklQuotation'][9]);  
						} 
				   } 
			}   
            
        }
        
        //destination
        for($i=0;$i<count($arrDetailDestinationKey);$i++){
            
            if(empty($arr['hidDestinationServiceKey'][$i])) continue;
             
            $arrContainerItemLCL = $this->unFormatNumber($arr['ratePriceDestination'][$i]);  
                       
			if($arr['chkContainerDestination-1'][0] == 1){ 
				if($arrchkIsReimburseDestination[$i] <> 1 && $arrContainerItemLCL <= 0){ 
					 $this->addErrorList($arrayToJs,false,'<b>Destination Charge - Rate</b>. '.$this->errorMsg['emklQuotation'][9]);   
				}
			}

			for ($k=0;$k<count($rsContainer);$k++){ 
				    $arrContainerItem = $this->unFormatNumber($arr['containerDestinaton_'.$rsContainer[$k]['pkey']][$i]);  
					if($arr['chkContainerDestination'.$rsContainer[$k]['pkey']][0] == 1){ 
						if($arrchkIsReimburseDestination[$i] <> 1 && $arrContainerItem <= 0){  
								 $this->addErrorList($arrayToJs,false,'<b>Destination Charge - '.$rsContainer[$k]['name'].'</b>. '.$this->errorMsg['emklQuotation'][9]); 
						}
					}
			}   
            
        }
        
   
        
		return $arrayToJs;
	 }
 
   function addErrorMsgArray(&$arr,$content){
        if(!in_array($content,$arr ))
             array_push($arr,$content);   
    }
    
 
    
    function getDataAllServices($pkey,$table,$opt,$locTypeKey){
 
		// selain fregiht, gk ad polpod
		if ($locTypeKey <> LOC_TYPE['freight']){
			unset($opt['polkey']);
			unset($opt['podkey']); 
		}
		
        $rsDataContainer = $this->generateQueryWithContainer($pkey,$table,$opt,$locTypeKey);
 
        $rsDataRate = $this->generateQueryWithoutContainer($pkey,$table,$opt,$locTypeKey,RATE_TYPE['rate']);
        $rsDataMinimum = $this->generateQueryWithoutContainer($pkey,$table,$opt,$locTypeKey,RATE_TYPE['minimum']);
        $rsDataNormal = $this->generateQueryWithoutContainer($pkey,$table,$opt,$locTypeKey,RATE_TYPE['normal']);
            
            
        $rsData = array_merge($rsDataContainer,$rsDataRate,$rsDataMinimum,$rsDataNormal);


        return $rsData;
        
        
    }
    

    
     function getQuotationPriceAndCost($pkey,$opt = array()){
  
	   $rsDataOrigin = $this->getDataAllServices($pkey,array('tableDetail' => $this->tableNameDetailOrigin,'tableCostDetail' => $this->tablePriceDetailOrigin),$opt,LOC_TYPE['origin']);
       $rsDataDestination = $this->getDataAllServices($pkey,array('tableDetail' => $this->tableNameDetailItem,'tableCostDetail' => $this->tablePriceDetailDestination),$opt,LOC_TYPE['destination']);
       $rsDataFreight = $this->getDataAllServices($pkey,array('tableDetail' => $this->tableNameDetailFreight,'tableCostDetail' => $this->tablePriceDetailFreight),$opt,LOC_TYPE['freight']);

 
       //setelah itu di merge dijadikan satu lalu di lempar setiap item yang ada.
       $rsAllCostAndPrice = array_merge($rsDataOrigin,$rsDataDestination,$rsDataFreight);
        for($i=0;$i<count($rsAllCostAndPrice);$i++){
            $rsAllCostAndPrice[$i]['taxpercentage'] =  (float)$rsAllCostAndPrice[$i]['taxpercentage'];
        }

         
       return $rsAllCostAndPrice;
       
   }
    
    
    function getDetailPriceAndCost($pkey,$opt = array(),$loctypekey){
         
            switch($loctypekey){
                case LOC_TYPE['origin']: 
                    $arrTable = array('tableDetail' => $this->tableNameDetailOrigin,'tableCostDetail' => $this->tablePriceDetailOrigin);
                    $locTypeKey = LOC_TYPE['origin'];   
                break;
               case LOC_TYPE['destination']: 
                    $arrTable = array('tableDetail' => $this->tableNameDetailItem,'tableCostDetail' => $this->tablePriceDetailDestination);
                    $locTypeKey = LOC_TYPE['destination'];    
                break;
                case LOC_TYPE['freight']: 
                    $arrTable = array('tableDetail' => $this->tableNameDetailFreight,'tableCostDetail' => $this->tablePriceDetailFreight);
                    $locTypeKey = LOC_TYPE['freight'];    
                break;
            } 
               
               
        
        $rsData = $this->getDataAllServices($pkey,$arrTable,$opt,$loctypekey);
        
        
        return $rsData;
        
        
    }
    
      
    
     function generateDefaultQueryForAutoComplete($returnField){ 
 
        $sql = 'select
					'.$returnField['key'].',
					'.$returnField['value'].' as value,
                    '.$this->tableName . '.customerkey,
                    '.$this->tableName . '.saleskey,
                    '.$this->tableName . '.transportationtypekey,
                    '.$this->tableName . '.loadcontainertypekey, 
                    '.$this->tableName . '.isshowcurrency,
                    '.$this->tableName . '.warehousekey, 
                    '.$this->tableName . '.trdesc,
                    '.$this->tableName . '.currencykey,
                    '.$this->tableName . '.trdesc,
                    '.$this->tableCustomer . '.name as customername,
                    '.$this->tableEmployee . '.name as salesname
				from 
					'.$this->tableName . '
                    left join '.$this->tableCustomer.' on '.$this->tableName.'.customerkey = '.$this->tableCustomer.'.pkey
                    left join '.$this->tableEmployee.' on '.$this->tableName.'.saleskey = '.$this->tableEmployee.'.pkey,
                    '.$this->tableStatus.' 
				where  		 
					'.$this->tableName . '.statuskey = '.$this->tableStatus.'.pkey  
			';
        
           
            $sql .=  $this->getWarehouseCriteria() ; 
		 
         return $sql;
     }
  
    
    function generateQueryWithContainer($pkey,$table,$opt,$locTypeKey){
        
        
        $criteria = $this->filterCriteriaQuery($opt,$table,$locTypeKey);
 
           $sql = 'select 
                    '.$table['tableDetail'].'.pkey,
                    '.$table['tableDetail'].'.servicekey,
                    '.$table['tableDetail'].'.remarks,
                    '.$table['tableDetail'].'.alias,
                    '.$table['tableDetail'].'.isperreciept,
                    '.$table['tableDetail'].'.unitkey,
                    '.$table['tableDetail'].'.currencykey,
                    '.$table['tableDetail'].'.costcurrencykey,
                    '.$table['tableDetail'].'.taxpercentage,
                    '.$table['tableDetail'].'.taxpercentagecost,
                    '.$table['tableDetail'].'.ispriceincludetax,
                    '.$table['tableDetail'].'.ispriceincludetaxcost,
                    '.$table['tableCostDetail'].'.containerkey,
                    '.$table['tableCostDetail'].'.price,
                    '.$table['tableCostDetail'].'.cost,
                    '.$this->tableContainer.'.name as containername,  
                    '.$this->tableItem.'.name as servicename,   
                    '.$this->tableItemUnit.'.name as unitname   
                from
                    '.$table['tableDetail'].'
                        left join '.$this->tableItem.' on  '.$table['tableDetail'].'.servicekey = '.$this->tableItem.'.pkey
                        left join '.$this->tableItemUnit.' on  '.$table['tableDetail'].'.unitkey = '.$this->tableItemUnit.'.pkey, 
                    '.$table['tableCostDetail'].'
                        left join '.$this->tableContainer.' on  '.$table['tableCostDetail'].'.containerkey = '.$this->tableContainer.'.pkey
                where
                    '.$table['tableDetail'].'.refkey =  '. $this->oDbCon->paramString($pkey).' and    
                    '.$table['tableCostDetail'].'.refkey = '. $table['tableDetail'].'.pkey';
           
           $sql .= $criteria;
       
        return $this->oDbCon->doQuery($sql);
    
        
    }
    
    function generateQueryWithoutContainer($pkey,$table,$opt,$locTypeKey,$rateType){

     
        $criteria = $this->filterCriteriaQuery($opt,$table,$locTypeKey,$rateType);
    
        $selectField = '';
   
            switch($rateType){
                case RATE_TYPE['rate'] :  
                    $price = 'rateprice as price';
                    $cost = 'cost as cost';
                break;
                case RATE_TYPE['minimum'] :  
                    $price = 'minimumprice as price';
                    $cost = 'minimumcost as cost';
                break;
                case RATE_TYPE['normal'] :  
                    $price = 'normalprice as price';
                    $cost = 'normalcost as cost';
                break;
                          
                
            }
            
     

         $sql = 'select 
                    '.$table['tableDetail'].'.'.$price.',
                    '.$table['tableDetail'].'.'.$cost.',
                    '.$table['tableDetail'].'.servicekey,
                    '.$table['tableDetail'].'.alias,
                    '.$table['tableDetail'].'.remarks,
                    '.$table['tableDetail'].'.isperreciept,
                    '.$table['tableDetail'].'.currencykey,
                    '.$table['tableDetail'].'.unitkey,
                    '.$table['tableDetail'].'.costcurrencykey,
                    '.$table['tableDetail'].'.taxpercentage,
                    '.$table['tableDetail'].'.taxpercentagecost,
                    '.$table['tableDetail'].'.ispriceincludetax,
                    '.$table['tableDetail'].'.ispriceincludetaxcost,
                    '.$this->tableItem.'.name as servicename,
                    '.$this->tableItemUnit.'.name as unitname   
                from
                    '.$table['tableDetail'].'
                        left join '.$this->tableItem.' on  '.$table['tableDetail'].'.servicekey = '.$this->tableItem.'.pkey
                        left join '.$this->tableItemUnit.' on  '.$table['tableDetail'].'.unitkey = '.$this->tableItemUnit.'.pkey
                where
                    '.$table['tableDetail'].'.refkey =  '. $this->oDbCon->paramString($pkey);
            
        $sql .= $criteria;
         
        $rs = $this->oDbCon->doQuery($sql); 

        $rsData = array();
        for($i=0;$i<count($rs);$i++){
            
            if($rs[$i]['price'] == 0 && $rs[$i]['cost'] == 0) continue;
            $rsData[$i] = $rs[$i];
        }
        
   
        
        return $rsData;
        
    }
    
    function filterCriteriaQuery($opt,$table,$locTypeKey,$rateType = ''){
                   
        $port = new Port();

        $arrCriteria = array();
        
                            
          if(!empty($opt['containerkey']) && empty($rateType)) 
            array_push ($arrCriteria,' and '.$table['tableCostDetail'].'.containerkey in ('. $this->oDbCon->paramString($opt['containerkey'],',').')' );
 
          if (!empty($opt['servicekey'])) 
            array_push ($arrCriteria,' and '. $table['tableDetail'].'.servicekey = '. $this->oDbCon->paramString($opt['servicekey']) );
         
		
        if(!in_array($locTypeKey,NO_FREIGHT_TYPE)){
          	
            if (!empty($opt['carrierkey'])) 
                array_push ($arrCriteria,' and '. $table['tableDetail'].'.carrierkey = '. $this->oDbCon->paramString($opt['carrierkey']) );


             if (!empty($opt['podkey']) && !empty($opt['polkey']))
                array_push ($arrCriteria,' and '. $table['tableDetail'].'.podkey = '. $this->oDbCon->paramString($opt['podkey']).' and '.$table['tableDetail'].'.polkey = '. $this->oDbCon->paramString($opt['polkey']) );           

        }else{ 
        
              if($locTypeKey == LOC_TYPE['origin']){
                    $rsPOLLocation = $port->getDataRowById($opt['polkey']);
                  
                    $locationpickupkey = $opt['placeofreceiptkey'];
                    $locationzonekey = (empty($rsPOLLocation[0]['locationkey'])) ? 0 : $rsPOLLocation[0]['locationkey'];

              }else{

                  $rsPODLocation = $port->getDataRowById($opt['podkey']);
                  
                  $locationpickupkey = (empty($rsPODLocation[0]['locationkey'])) ? 0 : $rsPODLocation[0]['locationkey'];    
                  $locationzonekey =  $opt['placeofdeliverykey'];
              }

            
            if (!empty($locationpickupkey) || !empty($locationzonekey))
               array_push ($arrCriteria,' and '. $table['tableDetail'].'.locationpickupkey = '. $this->oDbCon->paramString($locationpickupkey).' and '.$table['tableDetail'].'.locationzonekey = '. $this->oDbCon->paramString($locationzonekey) );           

            
            
        }

        $criteria = '';
         $criteria .= implode(' ', $arrCriteria);  
               
        return $criteria;
    }

	function validateConfirm($rsHeader){

        $container = new Container();
        $employee = new Employee();
        $id = $rsHeader[0]['pkey'];

        $rsDetailDestination = $this->getDetailDestinationInformation($id);
        $rsHeader = $this->getDataRowById($id);
        
        $rsDetailCarrier = $this->getDetailFreight($id);
        $rsDetailOrigin = $this->getDetailOriginInformation($id);

        if (empty($rsDetailOrigin) && empty($rsDetailCarrier) && empty($rsDetailDestination)) 
                $this->addErrorLog(false,'<strong>' . $rsHeader[0]['code'] . '</strong>. ' . $this->errorMsg[501]); 
                
        
//        $arrOrigin = $this->getDataServiceAndTax($id,$rsDetailOrigin,LOC_TYPE['origin']);
//        $arrDestination = $this->getDataServiceAndTax($id,$rsDetailDestination,LOC_TYPE['destination']);
//        $arrCarrier = $this->getDataServiceAndTax($id,$rsDetailCarrier,LOC_TYPE['freight']);

        
        
//        $this->validateTaxServiceDetail($arrOrigin);
//        $this->validateTaxServiceDetail($arrDestination);
//        $this->validateTaxServiceDetail($arrCarrier);
 
         
	 }

    
//    function validateTaxServiceDetail($arr,$group = false){
//
//
//        $arrayToJs = array();
//        $arrTemp = array();
//
//
//        for($i=0;$i<count($arr);$i++){
//                                
//            
//            $indexKey = strtolower($arr[$i]['alias']);
//            
//
//               if(!isset($arrTemp[$indexKey])){
//                   $arrTemp[$indexKey] = $arr[$i]['taxpercentage'];
//               }else{
//                   
//                   if($arrTemp[$indexKey] <> $arr[$i]['taxpercentage']){
//                       
//                          array_push($arrayToJs,array('valid'=> 'false', 'message' => '<strong>'. $indexKey .'</strong>. '.$this->errorMsg['emklQuotation'][10]) );
//                    }
//               }
//            
//        }
//
//        if(!empty($arrayToJs)){
//            
//            for($i=0;$i<count($arrayToJs);$i++){
//                 if($arrayToJs[$i]['valid']  == 'false'){
//                    $this->addErrorLog(false,$arrayToJs[$i]['message']);
//
//                }
//            }
//        }
//
//        
//    }
    
    
    function getContainerDetail($pkey,$loctypekey){
        
            
			switch($loctypekey){
                case LOC_TYPE['origin']: 
                    $arrTable = array('tableDetail' => $this->tableNameDetailOrigin,'tableCostDetail' => $this->tablePriceDetailOrigin);
                    $loctypekey = LOC_TYPE['origin'];
                break;
               case LOC_TYPE['destination']: 
                    $arrTable = array('tableDetail' => $this->tableNameDetailItem,'tableCostDetail' => $this->tablePriceDetailDestination);
                    $loctypekey = LOC_TYPE['destination'];    
                break;
                case LOC_TYPE['freight']: 
                    $arrTable = array('tableDetail' => $this->tableNameDetailFreight,'tableCostDetail' => $this->tablePriceDetailFreight);
                    $loctypekey = LOC_TYPE['freight'];    
                break;
            } 
               
        $rsData = $this->getDataAllServices($pkey,$arrTable,$opt,$loctypekey);

//        $arrContainerKey = array();
        /*for($i=0;$i<count($rsData);$i++){
    
            array_push($arrContainerKey,array( 'containerkey' => $rsData[$i]['containerkey']));
        }*/
        $arrContainerKey = array_column($rsData,'containerkey');
        
        $arrContainerKey = array_unique($arrContainerKey);

        
        return $arrContainerKey;
        
        
        
    }
    
    
    
    function getContainerQuotation($pkey){
        
        
        $rsPriceAndCost =  $this->getQuotationPriceAndCost($pkey,$opt);
        $arrContainerKey = array_column($rsPriceAndCost,'containerkey');

        $containerKey = array_unique($arrContainerKey);
        
        return array_values($containerKey) ;
        
        
        
    }
    
//    function getDataServiceAndTax($id,$rsDetail,$groupBy){
//           
//        
//        $arrData = array();
//        
//                
//            switch($groupBy){
//                case LOC_TYPE['origin']: 
//                    $arrTable = array('tableDetail' => $this->tableNameDetailOrigin,'tableCostDetail' => $this->tablePriceDetailOrigin);
//                    $locTypeKey = LOC_TYPE['origin'];   
//                break;
//               case LOC_TYPE['destination']: 
//                    $arrTable = array('tableDetail' => $this->tableNameDetailItem,'tableCostDetail' => $this->tablePriceDetailDestination);
//                    $locTypeKey = LOC_TYPE['destination'];    
//                break;
//                case LOC_TYPE['freight']: 
//                    $arrTable = array('tableDetail' => $this->tableNameDetailFreight,'tableCostDetail' => $this->tablePriceDetailFreight);
//                    $locTypeKey = LOC_TYPE['freight'];    
//                break;
//            } 
//
//       
//        $rsData = $this->getDataAllServices($id,$arrTable,$opt,$locTypeKey);    
//
//        $arrData = array();
//
//        for($i=0;$i<count($rsData);$i++){
//            
//            $rsData[$i]['alias'] = (empty($rsData[$i]['alias'])) ? $rsData[$i]['servicename'] : $rsData[$i]['alias'];
//                
//                    
//            if($rsData[$i]['price'] > 0 && $rsData[$i]['isperreciept'] == 0)
//                array_push($arrData,$rsData[$i]);
//            
//        } 
//        
//
//        return $arrData;
//    }

//    function getDataServiceByTax($rsDetail,$groupby){
//        
//         //1. validasi berdasarkan PPN (berarti harus setiap service di check)
//        //- Jika Item sama ppn beda = Error
//        //- Jika Alias sama ppn beda = Error
//        
//        
//        
//        $service = new Service(SERVICE);
//        
//        $rsService = $service->searchDataRow(array($service->tableName.'.pkey',$service->tableName.'.name',$service->tableName.'.aliasname'), 
//                                               ' and '.$service->tableName.'.statuskey = 1');  
//            
//        $rsService = array_column($rsService,null,'pkey');
//        
//        
//
//        $arrTempService = array();
//        for($i=0;$i<count($rsDetail);$i++){
//            
//            //jika alias di detail  kosong maka akan menggunakan alias di master service kalau di master service kosong maka akan menggunakan item name
//            $itemname = (!empty($rsService[$rsDetail[$i]['servicekey']]['aliasname'])) ? $rsService[$rsDetail[$i]['servicekey']]['aliasname'] : $rsService[$rsDetail[$i]['servicekey']]['name'];
//
//            $servicename = (!empty($rsDetail[$i]['alias'])) ?  $rsDetail[$i]['alias'] : $itemname;
//            $indexKey = strtolower($servicename);
//                        
//            if(!isset($arrTempService[$groupby][$indexKey]))  
//                    $arrTempService[$groupby][$indexKey] = array();
//
//                array_push($arrTempService[$groupby][$indexKey],$rsDetail[$i]['taxpercentage']);
//
//            
//        }
//        
//        
//        return $arrTempService;
//        
//    }
	
	 
	function confirmTrans($rsHeader){ 
            $id = $rsHeader[0]['pkey'];
        
            
	}
    
    function validateJobOrder($rsHeader){
                     
        $id = $rsHeader[0]['pkey'];

        $emklJobOrder = new EMKLJobOrder();
        
        $rsJO = $emklJobOrder->searchDataRow( array(  $emklJobOrder->tableName.'.pkey', $emklJobOrder->tableName.'.code'  ) , 
                                '   and '.$emklJobOrder->tableName.'.quotationkey = '.$this->oDbCon->paramString($id).'
                                    and '.$emklJobOrder->tableName.'.statuskey in (2,3,4,5,6)'  
                       ); 

         if (!empty($rsJO)){
            $errorMsg = '';
            foreach($rsJO as $row)
                $errorMsg .= '<br><strong>'.$row['code'].'</strong>, ' .$this->errorMsg[203];
                
            
            $this->addErrorLog( false, '<strong>'.$rsHeader[0]['code'].'</strong> ' .$this->errorMsg[201].$errorMsg);
        }
    }
    
    
    function validateBackConfirm($rsHeader){
        $id = $rsHeader[0]['pkey'];
        
        $this->validateJobOrder($rsHeader);


    }
    
     function validateCancel($rsHeader,$autoChangeStatus=false){
        $id = $rsHeader[0]['pkey'];
         
        $this->validateJobOrder($rsHeader);

        
    /*    $emklJobOrder = new EMKLJobOrder();
        
        $rsJO = $emklJobOrder->searchDataRow( array(  $emklJobOrder->tableName.'.pkey', $emklJobOrder->tableName.'.code'  ) , 
                                '   and '.$emklJobOrder->tableName.'.quotationkey = '.$this->oDbCon->paramString($id).'
                                    and '.$emklJobOrder->tableName.'.statuskey in (2,3,4,5,6)'  
                       ); 
       
        if (!empty($rsJO)){
            $errorMsg = '';
            foreach($rsJO as $row)
                $errorMsg .= '<br><strong>'.$row['code'].'</strong>, ' .$this->errorMsg[225];
                
            
            $this->addErrorLog( false, '<strong>'.$rsHeader[0]['code'].'</strong> ' .$this->errorMsg[201].$errorMsg);
        }*/

    }

   function cancelTrans($rsHeader, $copy, $GLCancelDate = '00 / 00 / 0000'){
        $id = $rsHeader[0]['pkey']; 
	
		
		if ($copy) $this->copyDataOnCancel($id);
	}  
 
  
    function getDetailLocation($pkey,$locType){
          switch($locType){
                case LOC_TYPE['origin'] : 
                    $tableNameDetail = $this->tableNameDetailOrigin;
                    $tableLocation = $this->tableLocation;
                    $polkey = 'locationpickupkey';
                    $podkey = 'locationzonekey';
                    break;
                case LOC_TYPE['destination'] :
                    $tableNameDetail = $this->tableNameDetailItem;
                    $tableLocation = $this->tableLocation;

                    $polkey = 'locationpickupkey';
                    $podkey = 'locationzonekey';
                    break;
               case LOC_TYPE['freight'] :
                    $tableNameDetail = $this->tableNameDetailFreight;
                    $tableLocation = $this->tablePort;
                    $polkey = 'polkey';
                    $podkey = 'podkey';
                    break;

            }
        
        $sql = 'select
	   			'.$tableNameDetail .'.'.$polkey.' as polkey, 
	   			'.$tableNameDetail .'.'.$podkey.' as podkey, 
                concat('.$tableNameDetail. '.'.$polkey.',\' - \','.$tableNameDetail.'.'.$podkey.') as polpodkey, 
                pol.name as polname,
                pol.code as polcode,
                pod.name as podname,
                pod.code as podcode,
                concat(pol.name,\' - \',pod.name) as polpodname                
              from
			  	'.$tableNameDetail .' 
                    left join '.$tableLocation.' pol on  '.$tableNameDetail.'.'.$polkey.' = pol.pkey 
                    left join '.$tableLocation.' pod on  '.$tableNameDetail.'.'.$podkey.' = pod.pkey
			  where 
			  	'.$tableNameDetail .'.refkey in ('.$this->oDbCon->paramString($pkey,',') . ')  '; 
        

        $rs = $this->oDbCon->doQuery($sql);
        $rs = $this->reindexDetailCollections($rs,'polpodkey');
        $rs = array_values($rs);
		return $rs;        
        
    }
    
    
    function getLocationQuotation($pkey,$locType){
        
            switch($locType){
                case LOC_TYPE['origin'] : 
                    $locationkey = 'locationpickupkey';
                    $locationname = 'polcode';
                    $rs = $this->getDetailOriginInformation($pkey);
                    break;
                case LOC_TYPE['destination'] :
                    $locationkey = 'locationzonekey';
                    $locationname = 'podcode';
                    $rs =  $this->getDetailDestinationInformation($pkey); 
                    break;
               

            }
                    
        $rsPickupKey = array_column($rs,$locationname,$locationkey);

        $rs = array_unique($rsPickupKey);

        
        $arrLocationKey = array();

        $arrLocationKey[0]['pkey'] = 0;
        $arrLocationKey[0]['name'] = '----------';

        
        foreach($rs as $row => $value){
            
            if (empty($value) ) continue;
               
            array_push($arrLocationKey, array('pkey'=> $row,'name'=>$value));
           
        }
        
        
        return $arrLocationKey;
        
    }
    
    function getDetailVolume($pkey, $criteria = '')
    {

        $sql = 'select
	   			' . $this->tableVolumeDetail . '.*,
                ' . $this->tableContainer . '.name as itemname,
                ' . $this->tableContainer . '.volume
			  from
			  	' . $this->tableContainer . ', 
			  	' . $this->tableVolumeDetail . ' 
			  where  
                ' . $this->tableVolumeDetail . '.itemkey = ' . $this->tableContainer . '.pkey and
			  	' . $this->tableVolumeDetail . '.refkey in (' . $this->oDbCon->paramString($pkey, ',') . ')';


        $sql .= $criteria;

        return $this->oDbCon->doQuery($sql);
    }
    
    function getDetailFreight($pkey, $polkey = '', $podkey =''){ 
       
	   $sql = 'select
	   			'.$this->tableNameDetailFreight .'.*, 
                concat('.$this->tableNameDetailFreight. '.polkey,\' - \','.$this->tableNameDetailFreight. '.podkey) as polpodkey, 
                  pol.name as polname,
                pol.code as polcode,
                pod.code as podcode,
                pod.name as podname,
                concat(pol.name,\' - \',pod.name) as polpodname, 
                '.$this->tableSupplier.'.name as carriername,
                '.$this->tableItem.'.name as servicename,
                '.$this->tableItemUnit.'.name as unitname,
	   			'.$this->tableCurrency.'.name as currencyname 
              from
			  	'.$this->tableNameDetailFreight .' 
                    left join '.$this->tablePort.' pol on  '.$this->tableNameDetailFreight.'.polkey = pol.pkey 
                    left join '.$this->tablePort.' pod on  '.$this->tableNameDetailFreight.'.podkey = pod.pkey
				    left join ' .$this->tableItem.' on   ' .$this->tableNameDetailFreight.'.servicekey = '.$this->tableItem.'.pkey
                    left join '.$this->tableSupplier.' on  '.$this->tableNameDetailFreight.'.carrierkey = '.$this->tableSupplier.'.pkey
                    left join '.$this->tableItemUnit.' on '.$this->tableNameDetailFreight .'.unitkey =  '.$this->tableItemUnit.'.pkey, 
			  	'.$this->tableCurrency .'   
			  where 
			  	'.$this->tableNameDetailFreight .'.currencykey = '.$this->tableCurrency .'.pkey and  
			  	'.$this->tableNameDetailFreight .'.refkey in ('.$this->oDbCon->paramString($pkey,',') . ')  '; 
       
        //$sql .= $criteria;
    	if (!empty($podkey) && !empty($polkey))
            $sql .= ' and '.$this->tableNameDetailFreight.'.polkey = '. $this->oDbCon->paramString($polkey).' and '.$this->tableNameDetailFreight.'.podkey = '. $this->oDbCon->paramString($podkey);
		
		$sql .= 'order by '.$this->tableNameDetailFreight .'.orderlist asc';
         
		return $this->oDbCon->doQuery($sql);
	
   }  
    
    
    function getDetailDestinationInformation($pkey,$criteria = ''){ 
       
	   $sql = 'select
	   			'.$this->tableNameDetailItem .'.*, 
                concat('.$this->tableNameDetailItem. '.locationpickupkey,\' - \','.$this->tableNameDetailItem. '.locationzonekey) as polpodkey, 
                pod.name as podname,
                pod.code as podcode,
                pol.name as polname,
                pol.code as polcode,
                concat(pol.name,\' - \',pod.name) as polpodname, 
                '.$this->tableItem.'.name as servicename,
                '.$this->tableItem.'.categorykey,
                '.$this->tableItemUnit.'.name as unitname,
	   			'.$this->tableCurrency.'.name as currencyname
                
              from
			  	'.$this->tableNameDetailItem .' 
                    left join '.$this->tableLocation.' pol on  '.$this->tableNameDetailItem.'.locationpickupkey = pol.pkey 
                    left join '.$this->tableLocation.' pod on  '.$this->tableNameDetailItem.'.locationzonekey = pod.pkey
				   left join ' .$this->tableItem.' on   ' .$this->tableNameDetailItem.'.servicekey = '.$this->tableItem.'.pkey
                    left join '.$this->tableItemUnit.' on '.$this->tableNameDetailItem .'.unitkey =  '.$this->tableItemUnit.'.pkey, 
			  	'.$this->tableCurrency .'   
			  where 
			  	'.$this->tableNameDetailItem .'.currencykey = '.$this->tableCurrency .'.pkey and  
			  	'.$this->tableNameDetailItem .'.refkey in ('.$this->oDbCon->paramString($pkey,',') . ')  '; 
       
        if(!empty($criteria))
            $sql .= $criteria;
        
		$sql .= 'order by '.$this->tableNameDetailItem .'.orderlist asc';
		return $this->oDbCon->doQuery($sql);
	
   }
    
    function getDetailOriginInformation($pkey,$criteria = ''){ 
 
	   $sql = 'select
	   			'.$this->tableNameDetailOrigin .'.*, 
                concat('.$this->tableNameDetailOrigin. '.locationpickupkey,\' - \','.$this->tableNameDetailOrigin. '.locationzonekey) as polpodkey, 
                pol.name as polname,
                pol.code as polcode,
                pod.name as podname,
                pod.code as podcode,
                concat(pol.name,\' - \',pod.name) as polpodname, 
                '.$this->tableItem.'.name as servicename,
                '.$this->tableItem.'.categorykey,
                '.$this->tableItemUnit.'.name as unitname,
	   			'.$this->tableCurrency.'.name as currencyname
                
              from
			  	'.$this->tableNameDetailOrigin .' 
                    left join '.$this->tableLocation.' pol on  '.$this->tableNameDetailOrigin.'.locationpickupkey = pol.pkey 
                    left join '.$this->tableLocation.' pod on  '.$this->tableNameDetailOrigin.'.locationzonekey = pod.pkey
				    left join ' .$this->tableItem.' on   ' .$this->tableNameDetailOrigin.'.servicekey = '.$this->tableItem.'.pkey
                    left join '.$this->tableItemUnit.' on '.$this->tableNameDetailOrigin .'.unitkey =  '.$this->tableItemUnit.'.pkey, 
                '.$this->tableCurrency .'   
			  where 
			  	'.$this->tableNameDetailOrigin .'.currencykey = '.$this->tableCurrency .'.pkey and  
			  	'.$this->tableNameDetailOrigin .'.refkey in ('.$this->oDbCon->paramString($pkey,',') . ')  '; 
       
        if(!empty($criteria))
            $sql .= $criteria;
        
		$sql .= 'order by '.$this->tableNameDetailOrigin .'.orderlist asc';
		 
		return $this->oDbCon->doQuery($sql);
	
   }
    
//    function getDetailCommodity($pkey){ 
//       
//	   $sql = 'select
//	   			'.$this->tableCommodityDetail .'.*, 
//                '.$this->tableCommodity.'.name as commodityname                
//              from
//			  	'.$this->tableCommodityDetail .' 
//				   left join ' .$this->tableCommodity.' on   ' .$this->tableCommodityDetail.'.commoditykey = '.$this->tableCommodity.'.pkey
//			  where 
//			  	'.$this->tableCommodityDetail .'.refkey in ('.$this->oDbCon->paramString($pkey,',') . ')  '; 
//       
//        //$sql .= $criteria;
//        
//		return $this->oDbCon->doQuery($sql);
//	
//   }
	  
	function getDetailTermAndCondition($pkey){ 
       
	   $sql = 'select
	   			'.$this->tableTermsandConditionDetail .'.*,
	   			'.$this->tableTermsAndCondition .'.name 
              from
			  	'.$this->tableTermsandConditionDetail .' 
                    left join '.$this->tableTermsAndCondition.' on '.$this->tableTermsandConditionDetail .'.termsconditionkey =  '.$this->tableTermsAndCondition.'.pkey 
			  where 
			  	'.$this->tableTermsandConditionDetail .'.refkey in ('.$this->oDbCon->paramString($pkey,',') . ')  '; 
       
        //$sql .= $criteria;
        
		return $this->oDbCon->doQuery($sql);
	
   }

    function afterAddDataOnCopy($pkey, $pkeyBefore){     
        $rsHeaderBefore = $this->getDataRowById($pkeyBefore);
        if ($rsHeaderBefore[0]['statuskey'] == 3) {
            $revision =  $rsHeaderBefore[0]['revision'] + 1 ;
            $sql = 'update ' .$this->tableName.' set revision = '.$revision.' where pkey = ' . $this->oDbCon->paramString($pkey);    
            $this->oDbCon->execute($sql); 
        }
    }
    
    function getComboLocation($arrParam){
                
        
        $arrPickupDetailKey = $arrParam['hidPickupDetailKey'];
        $arrLocationPickupDetailKey = $arrParam['hidLocationPickupDetailKey'];
        $arrLocationZoneDetailKey = $arrParam['hidLocationZoneDetailKey'];
        $arrPickupDetailName = $arrParam['pickupDetailName'];
        $arrPickupZoneDetailName = $arrParam['pickupZoneDetailName'];
        $arrPickupLocationName = $arrParam['pickupLocationDetailName'];
        $arrZoneLocationName = $arrParam['zoneLocationDetailName'];
        $arrPOLKey = $arrParam['hidDetailPOLKey'];
        $arrPOLName = $arrParam['detailPOLName'];
        $arrPODName = $arrParam['detailPODName'];


		$arrZone = array();
		
        $arrPickup = array();
        for($i=0;$i<count($arrPickupDetailKey);$i++){
			if(!empty($arrPickupDetailName[$i])) array_push($arrPickup,$arrPickupDetailName[$i]);
			if(!empty($arrPickupZoneDetailName[$i])) array_push($arrPickup,$arrPickupZoneDetailName[$i]);
			
			if(!empty($arrPickup)) 
				array_push($arrZone, implode(' - ',$arrPickup));
		 
		}
                
 		$arrPickup = array();
        for($i=0;$i<count($arrLocationPickupDetailKey);$i++){
			if(!empty($arrPickupLocationName[$i])) array_push($arrPickup,$arrPickupLocationName[$i]);
			if(!empty($arrZoneLocationName[$i])) array_push($arrPickup,$arrZoneLocationName[$i]);
			
			if(!empty($arrPickup)) 
				array_push($arrZone, implode(' - ',$arrPickup));
		 
		}
                 	
		$arrPickup = array();
        for($i=0;$i<count($arrPOLKey);$i++){
			if(!empty($arrPOLName[$i])) array_push($arrPickup,$arrPOLName[$i]);
			if(!empty($arrPODName[$i])) array_push($arrPickup,$arrPODName[$i]);
			
			if(!empty($arrPickup)) 
				array_push($arrZone, implode(' - ',$arrPickup));
		 
		}
                 
		 
        return $arrZone; 
        
    }
	
	  function getContainerPrice($pkey,$type,$containerkey=''){ 
		   
		switch($type){
			case  LOC_TYPE['origin']: 	$tablePrice = $this->tablePriceDetailOrigin;
							$tableDetail = $this->tableNameDetailOrigin;
							break;
				
			case  LOC_TYPE['destination']: 	$tablePrice = $this->tablePriceDetailDestination;
									$tableDetail = $this->tableNameDetailItem;
									break;
				
			case  LOC_TYPE['freight']: 	$tablePrice = $this->tablePriceDetailFreight;
									$tableDetail = $this->tableNameDetailFreight;
									break;
				
			default : $tablePrice = $this->tablePriceDetailOrigin;
					  $tableDetail = $this->tableNameDetailOrigin;
					  break;
		}  
		  
        $sql = 'select 
                    '.$tablePrice.'.containerkey,
                    '.$tablePrice.'.price,
                    '.$tablePrice.'.cost
                from
                    '.$this->tableName.', 
                    '.$tableDetail.', 
                    '.$tablePrice.'
                where
                    '.$this->tableName.'.pkey = '.$tableDetail.'.refkey and
                    '.$tableDetail.'.pkey = '.$tablePrice.'.refkey and
                    '.$tablePrice.'.refkey in ('.$this->oDbCon->paramString($pkey,',').')';

        if (!empty($containerkey))
            $sql .= ' and '.$tablePrice.'.containerkey = '. $this->oDbCon->paramString($containerkey);
           
         return $this->oDbCon->doQuery($sql);
    }
    

//    function getShipmentTerm($pkey=''){ 
//       
//        $sql = 'select
//                    '.$this->tableShipmentTerm .'.*
//               from
//                   '.$this->tableShipmentTerm ;
//                 
//         if(!empty($pkey))
//             $sql .= ' where  		
//             '.$this->tableShipmentTerm . '.pkey = '.$this->oDbCon->paramString($pkey);
//         
//         
//        $sql .=' order by name asc';
//          
//        return $this->oDbCon->doQuery($sql);
//     
//    }
    
    function normalizeParameter($arrParam, $trim=false){
  
        $arrParam['selTypeOfJob'] = $this->jobType; 
        
        
//        $arrCommodityCache = array();
//        for($i=0;$i<count($arrParam['hidCommodityKey']);$i++){
//            
//            if(empty($arrParam['hidCommodityKey'][$i])) continue;
//    
//            array_push($arrCommodityCache,$arrParam['commodityName'][$i]);
//                
//        }
//        
//        $commodityCache = (!empty($arrCommodityCache)) ? implode(', ',$arrCommodityCache) : '';
//        
//        $arrParam['commodityCache'] = $commodityCache;
        
        $arrLocationCache = $this->getComboLocation($arrParam);
        $locationCache =  (!empty($arrLocationCache)) ? implode(chr(13),$arrLocationCache) : '';
        
        $arrParam['locationCache'] = $locationCache;
       
        
        $container = new Container();
        $rsContainer = $container->searchData($container->tableName.'.statuskey',1,true);

     
        $totalContainer = count($arrParam['hidPriceDetailOriginContainerKey'] ?? []);
        $totalContainerDestination = count($arrParam['hidPriceDetailDestinationContainerKey'] ?? []);
        $totalContainerFreight = count($arrParam['hidPriceDetailFreightContainerKey'] ?? []);
        $arrParam['hidPriceDetailOriginKeyTotalRows'] = array('1' => array('0' => $totalContainer));
        $arrParam['hidPriceDetailDestinationKeyTotalRows'] = array('1' => array('0' => $totalContainerDestination));
        $arrParam['hidPriceDetailFreightContainerKeyTotalRows'] = array('1' => array('0' => $totalContainerFreight));

        $containerType = $arrParam['selContainerType'];

        if(($containerType == EMKL['emklType']['fcl']) || 
            ($containerType == EMKL['emklType']['trucking'])) {
            $arrParam['volume'] = 0;
        } else if($containerType == EMKL['emklType']['lclnc'] || $containerType == EMKL['emklType']['lcl']) { 
			unset($arrParam['hidDetailVolumeKey']);
			unset($arrParam['qtyVolume']);
			unset($arrParam['selContainerDetailVolumeKey']); 
        }        
		
		// ORIGIN
         for($i=0;$i<count($arrParam['hidDetailItemOriginKey']);$i++){
 
//				if($arrParam['chkIsReimburseOrigin'][$i] == 1){ 
//					$arrParam['chkIncludeTaxOriginDetail'][$i] = 0; 
//					$arrParam['taxPercentageOrigin'][$i] = 0; 
//				}
                
			 	//KHUSUS NON CONTAINER (RATE,MINIMUM,NORMAL)
                if($arrParam['chkContainerOrigin-1'][0] != 1){
                    $arrParam['ratePriceOrigin'][$i] = 0;  
                }
 

                $arrParam['hidPriceDetailOriginContainerKey'][$i] = array();
                $arrParam['hidPriceDetailOriginKey'][$i] = array();
                $arrParam['priceOrigin'][$i] = array(); 

                 //KHUSUS CONTAINER 

                for ($k=0;$k<count($rsContainer);$k++){
                    

                    if( $arrParam['chkContainerOrigin'.$rsContainer[$k]['pkey']][0] == 1){ 
                        
                        if($arrParam['containerOrigin_'.$rsContainer[$k]['pkey']][$i] <= 0) continue;
                        
						array_push($arrParam['hidPriceDetailOriginContainerKey'][$i], $rsContainer[$k]['pkey']);
						array_push($arrParam['hidPriceDetailOriginKey'][$i], 0);
						array_push($arrParam['priceOrigin'][$i], $arrParam['containerOrigin_'.$rsContainer[$k]['pkey']][$i]); 
						 
                    }
                }

            }

		// FREIGHT 
        for($i=0;$i<count($arrParam['hidDetailItemFreightKey']);$i++){
             
			
//				if($arrParam['chkIsReimburseFreight'][$i] == 1){ 
//					$arrParam['chkIncludeTaxOriginDetail'][$i] = 0; 
//					$arrParam['taxPercentageOrigin'][$i] = 0; 
//				}
                
			
                if($arrParam['chkContainerFreight-1'][0] != 1){
                    $arrParam['ratePriceFreight'][$i] = 0;  
                }
 

                $arrParam['hidPriceDetailFreightContainerKey'][$i] = array();
                $arrParam['hidPriceDetailFreightKey'][$i] = array();
                $arrParam['priceFreight'][$i] = array(); 

                 //KHUSUS CONTAINER 

                for ($k=0;$k<count($rsContainer);$k++){

                    if( $arrParam['chkContainerFreight'.$rsContainer[$k]['pkey']][0] == 1){ 
                        
                     if($arrParam['containerFreight_'.$rsContainer[$k]['pkey']][$i] <= 0) continue;

					array_push($arrParam['hidPriceDetailFreightContainerKey'][$i], $rsContainer[$k]['pkey']);
					array_push($arrParam['hidPriceDetailFreightKey'][$i], 0);
					array_push($arrParam['priceFreight'][$i], $arrParam['containerFreight_'.$rsContainer[$k]['pkey']][$i]);   

                    }
                }

            }
		
		
		 // DESTINATION
          for($i=0;$i<count($arrParam['hidDetailItemDestinationKey']);$i++){
 
//				if($arrParam['chkIsReimburseDestination'][$i] == 1){ 
//					$arrParam['chkIncludeTaxDestinationDetail'][$i] = 0; 
//					$arrParam['taxPercentageDestination'][$i] = 0; 
//				}
                
			 	//KHUSUS NON CONTAINER (RATE,MINIMUM,NORMAL)
                if($arrParam['chkContainerDestination-1'][0] != 1){
                    $arrParam['ratePriceDestination'][$i] = 0;  
                }
 

                $arrParam['hidPriceDetailDestinationContainerKey'][$i] = array();
                $arrParam['hidPriceDetailDestinationKey'][$i] = array();
                $arrParam['priceDestination'][$i] = array(); 

                 //KHUSUS CONTAINER 

                for ($k=0;$k<count($rsContainer);$k++){
                    

                    if( $arrParam['chkContainerDestination'.$rsContainer[$k]['pkey']][0] == 1){ 
                        
                        if($arrParam['containerDestination_'.$rsContainer[$k]['pkey']][$i] <= 0) continue;
                        
						array_push($arrParam['hidPriceDetailDestinationContainerKey'][$i], $rsContainer[$k]['pkey']);
						array_push($arrParam['hidPriceDetailDestinationKey'][$i], 0);
						array_push($arrParam['priceDestination'][$i], $arrParam['containerDestination_'.$rsContainer[$k]['pkey']][$i]); 
						 
                    }
                }

            }
		
		 
//        
		// gk valid kalo ad margin
		
//        $reCountResult = $this->reCountTotalSellingAndCost($arrParam); 
//        $arrParam['totalSelling'] = $reCountResult['totalSelling'];
//        $arrParam['totalBuying'] = $reCountResult['totalBuying'];
//        $arrParam['totalMargin'] = $reCountResult['totalMargin'];
//        $arrParam['totalPercentageMargin'] = $reCountResult['totalPercentageMargin'];

        $arrParam = parent::normalizeParameter($arrParam,true);
		 
			
        return $arrParam;
    }
	
	function getQuotationInformation($pkey){
		
		$criteria = array();
		
		array_push($criteria,' and '.$this->tableName.'.statuskey in (3)');
		
        $rs = $this->searchData($this->tableName.'.pkey',$pkey,true,implode(' and ',$criteria));
	 
		// sementara asumsi ambil yg pertama saja dulu
		$rs[0]['freightdetail'] = $this->getDetailFreight($pkey);
		$rs[0]['containerfreightdetail'] = $this->getDataAllServices($pkey,array('tableDetail' => $this->tableNameDetailFreight,'tableCostDetail' => $this->tablePriceDetailFreight),array(),LOC_TYPE['freight']);
		
		return $rs;
		 
	}
    
  public function getTotalContainerJobOrderSummary($pkey, $containerType = '') 
    {
        $emklJobOrder = new EMKLJobOrder();
        
        $rsResult = array();

        //get container detail volume (FCL, Trucking)
        $sql = '
            SELECT
                '. $emklJobOrder->tableName.'.code,
                '. $emklJobOrder->tableVolumeDetail .'.itemkey,
                '. $emklJobOrder->tableVolumeDetail . '.qty,
                '. $emklJobOrder->tableContainer .'.name as containername
            FROM
                '. $emklJobOrder->tableVolumeDetail .',
                '. $emklJobOrder->tableContainer .',
                '. $emklJobOrder->tableName.'
            WHERE
                '. $emklJobOrder->tableVolumeDetail .'.itemkey = '. $emklJobOrder->tableContainer .'.pkey and
                ' . $emklJobOrder->tableName . '.pkey = '. $emklJobOrder->tableVolumeDetail .'.refkey  and
                '. $emklJobOrder->tableName .'.statuskey in (2,3) and
                '. $emklJobOrder->tableName .'.quotationkey in ('. $this->oDbCon->paramString($pkey,',') .') 
        ';

        $rsVolume = $this->oDbCon->doQuery($sql);

        //get volume satuan cbm (LCL)
        $sqlVolumeCBM = '
            SELECT 
                '. $emklJobOrder->tableName.'.code,
                ' . $emklJobOrder->tableName . '.volume as qty,
                    0 as itemkey,
                    0 as containername
                FROM
                    '. $emklJobOrder->tableName .'
                WHERE
                    '. $emklJobOrder->tableName .'.statuskey in (2,3) and
                    '. $emklJobOrder->tableName .'.quotationkey in ('. $this->oDbCon->paramString($pkey,',') .')
            ';
        
        $rsVolumeCBM = $this->oDbCon->doQuery($sqlVolumeCBM);
        
        if(empty($containerType)) {
            
            $rsResult = array_merge($rsVolume, $rsVolumeCBM);

        } else {
            if(($containerType == EMKL['emklType']['fcl']) || ($containerType == EMKL['emklType']['trucking'])) {


                $rsResult = $rsVolume;

            } else if($containerType == EMKL['emklType']['lclnc']) {
                $rsResult = $rsVolumeCBM;
            }
        }

        return $rsResult;
    } 

    public function generateDataForRealizationQuotaReport($criteria,$order) {
        $sql = '
            select 
                '. $this->tableName .'.pkey,
                '. $this->tableName .'.code,
                '. $this->tableName .'.trdate,
                '. $this->tableName .'.customerkey,
                '. $this->tableName .'.saleskey,
                '. $this->tableName .'.locationcache,
                '. $this->tableStatus .'.status as statusname,
                '. $this->tableCustomer .'.name as customername,
                '. $this->tableEmployee .'.name as salesname,
                '. $this->tableName .'.totalvolume as quotacbm,
                '. $this->tableVolumeDetail .'.qty as quotavolume,
                '. $this->tableVolumeDetail .'.itemkey,
                '. $this->tableContainer .'.name as containername
            from
                '. $this->tableName .'
                    left join '. $this->tableEmployee .' on '. $this->tableName .'.saleskey = '. $this->tableEmployee .'.pkey
                    left join '. $this->tableVolumeDetail .' on '. $this->tableName .'.pkey = '. $this->tableVolumeDetail .'.refkey
                    left join '. $this->tableContainer .' on '. $this->tableVolumeDetail .'.itemkey = '. $this->tableContainer .'.pkey,
                '. $this->tableCustomer .',
                '. $this->tableStatus .'
            where
                '. $this->tableName .'.statuskey = '. $this->tableStatus .'.pkey and
                '. $this->tableName .'.customerkey = '. $this->tableCustomer .'.pkey 
        ';

        if (!empty($this->jobType))
            $sql .= ' and '.$this->tableName.'.jobtypekey in ('.$this->jobType.')  ';

        if (!empty($criteria)) {
            $sql .= ' ' . $criteria;
        }

        $sql .= ' ' . $order;

        $rs = $this->oDbCon->doQuery($sql);
    
        return $rs;
    }

    function getRealizationQuotationJobOrderReport($quotationkey) 
    {
        $emklJobOrder = new EMKLJobOrder();

        $sql = '
            select
                '. $emklJobOrder->tableName .'.pkey,
                '. $emklJobOrder->tableName .'.code,
                '. $emklJobOrder->tableName .'.quotationkey,
                '. $emklJobOrder->tableName .'.trdate,
                '. $this->tableName .'.code as quotationcode,
                '. $emklJobOrder->tableName .'.volume as totalrealizationcbm,
                '. $emklJobOrder->tableVolumeDetail .'.qty as totalrealizationvolume,
                '. $emklJobOrder->tableVolumeDetail .'.itemkey,
                '. $emklJobOrder->tableContainer . '.name as containername,
                CONCAT('. $emklJobOrder->tableName .'.quotationkey, \'-\', COALESCE(' . $emklJobOrder->tableVolumeDetail . '.itemkey, 0)) as indexkey
            from
                '. $emklJobOrder->tableName .'
                    left join '. $emklJobOrder->tableVolumeDetail .' on '. $emklJobOrder->tableName .'.pkey = '. $emklJobOrder->tableVolumeDetail .'.refkey
                    left join '. $this->tableName .' on '. $emklJobOrder->tableName .'.quotationkey = '. $this->tableName .'.pkey
                    left join ' . $emklJobOrder->tableContainer . ' on ' . $emklJobOrder->tableVolumeDetail . '.itemkey = ' . $emklJobOrder->tableContainer . '.pkey,
                '. $emklJobOrder->tableStatus .'
            where
                '. $emklJobOrder->tableName .'.statuskey = '. $emklJobOrder->tableStatus .'.pkey and
                '. $emklJobOrder->tableName .'.statuskey in (2,3) and
                '. $emklJobOrder->tableName .'.quotationkey in ('. $this->oDbCon->paramString($quotationkey,',') .')
        ';

        $rs = $this->oDbCon->doQuery($sql);
        
        return $rs;
    }
}
?>
