<?php
  
class EMKLJobOrderHeader extends BaseClass{ 
 
   function __construct($jobType = ''){
		
		parent::__construct();
         
		$this->tableName = 'emkl_order_header';
		$this->tableNameDetail = 'emkl_order_detail'; 
		$this->tableStatus = 'transaction_status';
        $this->tableSupplier = 'supplier';
		$this->tablePort = 'port'; 
        $this->tableCustomer = 'customer';
        $this->tableContainerType = 'container_type';
        $this->tableJobType = 'emkl_import_export';
        $this->tableWarehouse = 'warehouse';
        //$this->tableBillType = 'emkl_bill_type';
        $this->tableContainer = 'container'; 
        $this->tableContainerDetail = 'emkl_order_header_container_detail'; 
        $this->tableEmployee = 'employee'; 
        $this->tableEmklType = 'emkl_job_type'; 
        $this->isTransaction = true; 
		$this->securityObject = 'EMKLOrder'; 
		$this->tableItemUnit = 'item_unit';
        $this->tableVessel = 'vessel';
        $this->tableTerminal = 'terminal'; 
        $this->tableTransportationType = 'emkl_air_sea';
        $this->jobType = $jobType;
       
        $this->arrDataDetail = array(); 
        $this->arrDataDetail['pkey'] = array('hidDetailKey');
        $this->arrDataDetail['refkey'] = array('pkey','ref');
        $this->arrDataDetail['itemkey'] = array('selContainerDetailKey');
        $this->arrDataDetail['qty'] = array('qty',array('datatype' => 'number','mandatory'=>true));
        //$this->arrDataDetail['trdesc'] = array('trdesc');
    
        $this->arrContainerDetail = array(); 
        $this->arrContainerDetail['pkey'] = array('hidDetailContainerKey');
        $this->arrContainerDetail['refkey'] = array('pkey','ref');
        $this->arrContainerDetail['containerno'] = array('containerNo',array('mandatory'=>true));
        $this->arrContainerDetail['sealno'] = array('sealNo');
        $this->arrContainerDetail['unitkey'] = array('selUnit');
        $this->arrContainerDetail['qty'] = array('qtyContainer',array('datatype' => 'number'));
        $this->arrContainerDetail['weight'] = array('weightContainer',array('datatype' => 'number'));
        $this->arrContainerDetail['volume'] = array('volumeContainer',array('datatype' => 'number'));

        $arrDetails = array(); 
        array_push($arrDetails, array('dataset' => $this->arrDataDetail, 'tableName' => $this->tableNameDetail)); 
        array_push($arrDetails, array('dataset' => $this->arrContainerDetail, 'tableName' => $this->tableContainerDetail)); 
    
        $this->arrData = array(); 
        $this->arrData['pkey'] = array('pkey',array('dataDetail' =>  $arrDetails));
        $this->arrData['code'] = array('code');
        $this->arrData['codectr'] = array('codectr');
        $this->arrData['warehousekey'] = array('selWarehouseKey');
        //$this->arrData['reftable'] = array('reftable');
        $this->arrData['trdate'] = array('trDate','date');
        $this->arrData['trdesc'] = array('trDesc');
        $this->arrData['statuskey'] = array('selStatus');
        $this->arrData['customerkey'] = array('hidCustomerKey');
        $this->arrData['billtokey'] = array('hidBillToKey');
        $this->arrData['customerpebkey'] = array('hidCustomerPEBKey');
        $this->arrData['carrierkey'] = array('hidCarrierKey');
        $this->arrData['agentkey'] = array('hidAgentKey');
        $this->arrData['truckingkey'] = array('hidVendorKey');
        $this->arrData['polkey'] = array('hidPOLKey');
        $this->arrData['podkey'] = array('hidPODKey');
        $this->arrData['etdpol'] = array('etdPol','date');
        $this->arrData['etapod'] = array('etaPod','date');
        $this->arrData['closingdate'] = array('closingDate','date'); 
        $this->arrData['trdesc'] = array('trDesc');
        $this->arrData['bookingnumber'] = array('bookingNumber');
        //$this->arrData['refnumber'] = array('refNumber');
        $this->arrData['containernumber'] = array('containerNumber');
        $this->arrData['invoicenumber'] = array('invoiceNumber');
        $this->arrData['terminalkey'] = array('hidTerminalKey');    
        $this->arrData['depotkey'] = array('hidDepotKey');    
        $this->arrData['aju'] = array('aju');    
        $this->arrData['peb'] = array('peb');    
        $this->arrData['stuffing'] = array('stuffing');     
        $this->arrData['temperature'] = array('temperature','number');     
        $this->arrData['volume'] = array('volume','number');
        $this->arrData['weight'] = array('weight','number');          
        $this->arrData['vesselkey'] = array('hidVesselKey');
        $this->arrData['vesselnumber'] = array('vesselNumber');
        $this->arrData['feederkey'] = array('hidFeederKey');
        $this->arrData['feedernumber'] = array('feederNumber');  
        $this->arrData['jobtypekey'] = array('selTypeOfJob');
        $this->arrData['transportationtypekey'] = array('selAirSea');
        $this->arrData['loadcontainertypekey'] = array('selContainerType');     
        $this->arrData['stuffingin'] = array('stuffingIn','date');     
        $this->arrData['stuffingout'] = array('stuffingOut','date');   
        $this->arrData['saleskey'] = array('hidSalesKey');
        $this->arrData['itemkey'] = array('hidContainerKey');
        $this->arrData['containertypecache'] = array('containertypecache');
        $this->arrData['containertypekey'] = array('hidCargoType');
        $this->arrData['truckingplanningdate'] = array('truckingPlanningDate','date');  
        $this->arrData['incotermskey'] = array('selIncoterms');  
        $this->arrData['quotationnumber'] = array('quotationNumber');  
       
       
        //FROM BARU UPDATE
        $this->arrData['portkey'] = array('hidPortKey');
        $this->arrData['locationkey'] = array('hidLocationKey');
        $this->arrData['consigneename'] = array('consigneeName');
        $this->arrData['itemdescription'] = array('itemDescription');
        $this->arrData['mbl'] = array('mblNumber');
	   
        $this->arrData['placeofdeliverykey'] = array('hidPlaceOfDeliveryKey');
        $this->arrData['placeofreceiptkey'] = array('hidPlaceOfReceiptKey');

     
        $this->arrData['attachment'] = array('attachment');

	   // 7seas
        $this->arrData['placeofissuekey'] = array('hidPlaceOfIssueKey');
        $this->arrData['sishipperkey'] = array('hidSIShipperKey');
        $this->arrData['siconsigneename'] = array('siConsigneeName');
        $this->arrData['siconsigneeaddress'] = array('siConsigneeAddress');
	    $this->arrData['notifykey'] = array('hidNotifyKey');
	    $this->arrData['notifykey2'] = array('hidNotifyKey2');
	    $this->arrData['billtypekey'] = array('selBillType');
	    $this->arrData['freighttermkey'] = array('selSellingFreightTerm');
	    $this->arrData['hscode'] = array('hsCode');
	    $this->arrData['kpbc'] = array('kpbc');
	    $this->arrData['pebdate'] = array('pebDate','date');
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'date','title' => 'date','dbfield' => 'trdate','default'=>true, 'width' => 80, 'align' =>'center', 'format' => 'date'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'warehouse','title' => 'warehouse','dbfield' => 'warehousename', 'width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'containertype','title' => 'type','dbfield' => 'containertype', 'default'=>true,'width' => 60));
        array_push($this->arrDataListAvailableColumn, array('code' => 'etdpol','title' => 'etd','dbfield' => 'etdpol','default'=>true, 'width' => 80,'align' =>'center', 'format' => 'date'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'etapod','title' => 'eta','dbfield' => 'etapod','default'=>true, 'width' => 80,'align' =>'center', 'format' => 'date'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'shipper','title' => ($this->jobType == EMKL['jobType']['import']) ? 'consignee' : 'shipper','dbfield' => 'customername','default'=>true,'width' => 250));
        //array_push($this->arrDataListAvailableColumn, array('code' => 'shipperPEB','title' => ($this->jobType == EMKL['jobType']['import']) ? 'shipperPIB' : 'shipperPEB','dbfield' => 'customerpebname', 'width' => 250));
		array_push($this->arrDataListAvailableColumn, array('code' => 'pod','title' => 'pod','dbfield' => 'podname','default'=>true,'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'pol','title' => 'pol','dbfield' => 'polname','default'=>true,'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'jobType','title' => 'jobType','dbfield' => 'jobtypeunion','default'=>true,'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'note','title' => 'note','dbfield' => 'trdesc','width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'salesman','title' => 'salesman','dbfield' => 'salesname','width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'aju','title' => ($this->jobType == EMKL['jobType']['import']) ? 'ajuPIB' : 'ajuPEB','dbfield' => 'aju', 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 80));
        array_push($this->arrDataListAvailableColumn, array('code' => 'mbl','title' => 'mbl','dbfield' => 'mbl','width' => 100));
        
        array_push($this->filterCriteria, array('title' => $this->lang['warehouse'], 'field' => 'warehousekey'));        
        array_push($this->filterCriteria, array('title' => $this->lang['containerType'], 'field' => 'containertypekey', 'table' => $this->tableContainerType));
       

		$this->arrSearchColumn = array ();
		array_push($this->arrSearchColumn, array('Kode', $this->tableName . '.code'));
		array_push($this->arrSearchColumn, array('Tanggal', $this->tableName . '.trdate'));  
		array_push($this->arrSearchColumn, array('Pelanggan', $this->tableCustomer. '.name')); 
		array_push($this->arrSearchColumn, array('Sales', $this->tableEmployee. '.name'));
		array_push($this->arrSearchColumn, array('Catatan', $this->tableName. '.trdesc'));
		array_push($this->arrSearchColumn, array('POL', 'pol.name'));
		array_push($this->arrSearchColumn, array('POD', 'pod.name'));
		array_push($this->arrSearchColumn, array('AJU',  $this->tableName. '.aju'));
		array_push($this->arrSearchColumn, array('PEB',  $this->tableName. '.peb'));
		array_push($this->arrSearchColumn, array('Container',  $this->tableName. '.containernumber'));
		array_push($this->arrSearchColumn, array('Booking Number',  $this->tableName. '.bookingnumber'));
		array_push($this->arrSearchColumn, array('Container Type',  $this->tableContainerType. '.name')); 
		array_push($this->arrSearchColumn, array(ucwords($this->lang['jobType']), $this->tableJobType. '.name')); 
		array_push($this->arrSearchColumn, array(ucwords($this->lang['jobType']), $this->tableTransportationType. '.name')); 
		array_push($this->arrSearchColumn, array(ucwords($this->lang['jobType']), $this->tableEmklType. '.name')); 
		array_push($this->arrSearchColumn, array($this->lang['mbl'],  $this->tableName.'.mbl')); 
		array_push($this->arrSearchColumn, array($this->lang['quotation'],  $this->tableName.'.quotationnumber')); 


        $this->printMenu = array();
        array_push($this->printMenu,array('code' => 'print', 'name' => $this->lang['printTransaction'],  'icon' => 'print', 'url' => 'print/emklJobHeader'));
        //array_push($this->printMenu,array('code' => 'printSI', 'name' => 'Shipping Instruction',  'icon' => 'print', 'url' => 'print/shippingInstruction')); 
        
	   //array_push($this->filterCriteria, array('title' => $this->lang['warehouse'], 'field' => 'warehousekey'));
             
       
        $this->includeClassDependencies(array(
                  'Port.class.php',
                  'Container.class.php',
                  'Customer.class.php',
                  'City.class.php',
                  'Warehouse.class.php',
                  'ItemUnit.class.php',
                  'Item.class.php',
                  'EMKLJobOrder.class.php',
                  'EMKLPurchaseOrder.class.php',
                  'Consignee.class.php',
                  'Vessel.class.php',
                  'Terminal.class.php',
                  'Supplier.class.php',
                  'Service.class.php',
                  'ItemChecklist.class.php',
                  'Depot.class.php'
            ));
                                 
        $this->overwriteConfig();
   }
   
   function getQuery(){
	  
	   $sql = '
			SELECT '.$this->tableName.'.* , 
               '.$this->tableCustomer.'.name as customername,
               customer_peb.name as customerpebname,
               '.$this->tableWarehouse.'.name as warehousename,
               '.$this->tableEmployee.'.name as salesname, 
               '.$this->tableVessel.'.name as vesselname ,
               '.$this->tableEmklType.'.name as emkltypename ,
               CONCAT_WS(" ",'.$this->tableVessel.'.name,'.$this->tableName.'.vesselnumber) as vesselvoyage , 
               CONCAT_WS(", ",'.$this->tableJobType.'.name,'.$this->tableTransportationType.'.name,'.$this->tableEmklType.'.name) as jobtypeunion,
               '.$this->tableTerminal.'.name as terminalname,
               pol.name as polname,
               pod.name as podname,
               carrier.name as carriername, 
               vendor.name as vendorname,
			   '.$this->tableStatus.'.status as statusname,
               '.$this->tableContainerType.'.name as containertype  
			FROM '.$this->tableStatus.',
                 '.$this->tableWarehouse.',
                 '.$this->tableName.' 
                    left join '.$this->tableCustomer.' on '.$this->tableName.'.customerkey = '.$this->tableCustomer.'.pkey
                    left join '.$this->tableCustomer.' customer_peb on '.$this->tableName.'.customerpebkey = customer_peb.pkey 
                    left join '.$this->tableEmployee.' on  '.$this->tableName.'.saleskey = '.$this->tableEmployee.'.pkey 
                    left join '.$this->tableVessel.' on  '.$this->tableName.'.vesselkey = '.$this->tableVessel.'.pkey
                    left join '.$this->tableEmklType.' on  '.$this->tableName.'.loadcontainertypekey = '.$this->tableEmklType.'.pkey
                    left join '.$this->tablePort.' pol on  '.$this->tableName.'.polkey = pol.pkey 
                    left join '.$this->tablePort.' pod on  '.$this->tableName.'.podkey = pod.pkey
                    left join '.$this->tableSupplier.' carrier on  '.$this->tableName.'.carrierkey = carrier.pkey
                    left join '.$this->tableTerminal.' on  '.$this->tableName.'.terminalkey = '.$this->tableTerminal.'.pkey 
                    left join '.$this->tableSupplier.' vendor on  '.$this->tableName.'.truckingkey = vendor.pkey  
                    left join '.$this->tableContainerType.' on  '.$this->tableName.'.containertypekey = '.$this->tableContainerType.'.pkey  
                    left join '.$this->tableJobType.' on '.$this->tableName.'.jobtypekey = '.$this->tableJobType.'.pkey
                    left join '.$this->tableTransportationType.' on '.$this->tableName.'.transportationtypekey = '.$this->tableTransportationType.'.pkey
			WHERE '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey and 
                  '.$this->tableName.'.warehousekey = '.$this->tableWarehouse.'.pkey
 	  	' .$this->criteria ; 
	 	         
        if (!empty($this->jobType))
            $sql .= ' and jobtypekey in ('.$this->jobType.')  ';
       
        $sql .= $this->getWarehouseCriteria();
        $sql .= $this->getSalesCriteria() ;
       
       return $sql;
    }    
    
    function afterStatusChanged($rsHeader){   
        // retrieve latest status
        $rsHeader = $this->getDataRowById($rsHeader[0]['pkey']);
        if ($rsHeader[0]['statuskey'] == 2)
            $this->changeStatus($rsHeader[0]['pkey'],3); 
    }
    
        
     function validateForm($arr,$pkey = ''){ 
		  
		$arrayToJs = parent::validateForm($arr,$pkey);  
		
        $cargoType = $arr['hidCargoType']; 
         
        $containerDetail = array();
        if($arr['selContainerType']==EMKL['emklType']['fcl'] || $arr['selContainerType']==EMKL['emklType']['trucking']){
            $containerDetail = $arr['selContainerDetailKey'];  
        }else if( in_array($arr['selContainerType'], array(EMKL['emklType']['lcl'], EMKL['emklType']['lclnc'])) ){ 
             $containerDetail = array($arr['hidContainerKey']);
        }
         
        // cek jenis cargo
        $container = new Container();
        $rsContainer = $container->searchDataRow( array($container->tableName.'.pkey', $container->tableName.'.containertypekey'),
                                                    ' and '.$container->tableName.'.pkey in ('.$this->oDbCon->paramString($containerDetail,',').')' 
                                                );
         
        //$this->setLog(' and '.$container->tableName.'.pkey in ('.$this->oDbCon->paramString($containerDetail,',').')',true);
         
        foreach($rsContainer as $row){
            if ($row['containertypekey'] <> $cargoType){ 
                $this->addErrorList($arrayToJs,false,$this->errorMsg['container'][3]);
                break;
            }
        } 
         
       
		return $arrayToJs;
	 }

	function validateConfirm($rsHeader){
         
        parent::validateConfirm($rsHeader);
           
        $id = $rsHeader[0]['pkey']; 
      
        
        $cargoType = $rsHeader[0]['containertypekey']; 
        $loadType = $rsHeader[0]['loadcontainertypekey']; 
         
        $containerDetail = array();
        if($loadType==EMKL['emklType']['fcl'] || $loadType==EMKL['emklType']['trucking']){ 
            $rsDetail = $this->getDetailById($id);
            $containerDetail = array_column($rsDetail,'itemkey');
        }else if(in_array($loadType, array(EMKL['emklType']['lcl'],EMKL['emklType']['lclnc']))){ 
            $containerDetail = array($rsHeader[0]['itemkey']);
        }
         
        // cek jenis cargo
        $container = new Container();
        $rsContainer = $container->searchDataRow( array($container->tableName.'.pkey', $container->tableName.'.containertypekey'),
                                                    ' and '.$container->tableName.'.pkey in ('.$this->oDbCon->paramString($containerDetail,',').')' 
                                                );
         
            
        foreach($rsContainer as $row){
            if ($row['containertypekey'] <> $cargoType){ 
                $this->addErrorLog(false,$this->errorMsg['container'][3]);
                break;
            }
        } 
          
        
    }		
    
    function validateCancel($rsHeader,$autoChangeStatus=false){  
       
	    $emklJobOrder = new EMKLJobOrder();
        $emklPurchaseOrder = new EMKLPurchaseOrder($this->jobType);
	
        $id = $rsHeader[0]['pkey'];
	
    	$rsJobOrder = $emklJobOrder-> searchDataRow( array(  $emklJobOrder->tableName.'.pkey', $emklJobOrder->tableName.'.code'  ) , 
                                '   and '.$emklJobOrder->tableName.'.headerorderkey = '.$this->oDbCon->paramString($id).'
                                    and '.$emklJobOrder->tableName.'.statuskey in ('.TRANSACTION_STATUS['konfirmasi'].','.TRANSACTION_STATUS['selesai'].')'  
                       ); 
       
        if (!empty($rsJobOrder)) 
           $this->addErrorLog( false, '<strong>'.$rsJobOrder[0]['code'].'</strong> ' .$this->errorMsg[201].'<br><strong>'.$rsJobOrder[0]['code'].'</strong>, ' .$this->errorMsg[225] );
  
        $rsPurchase = $emklPurchaseOrder-> searchDataRow( array(  $emklPurchaseOrder->tableName.'.pkey', $emklPurchaseOrder->tableName.'.code'  ) , 
                                '   and '.$emklPurchaseOrder->tableName.'.refjoheaderkey = '.$this->oDbCon->paramString($id).'
                                    and '.$emklPurchaseOrder->tableName.'.statuskey in ('.TRANSACTION_STATUS['konfirmasi'].','.TRANSACTION_STATUS['selesai'].')'  
                       ); 
       
        if (!empty($rsPurchase)) 
           $this->addErrorLog( false, '<strong>'.$rsPurchase[0]['code'].'</strong> ' .$this->errorMsg[201].'<br><strong>'.$rsPurchase[0]['code'].'</strong>, ' .$this->errorMsg[225] );
  	 }

	function confirmTrans($rsHeader){
        $id = $rsHeader[0]['pkey'];
        
        $emklJobOrder = new EMKLJobOrder($this->jobType);
        $emklPurchaseOrder = new EMKLPurchaseOrder($this->jobType);
        $prepaidExpense = new PrepaidExpense();
        
        $arrParam = array();
        $rsDetail = array();
        
        $arrParam['hidCustomerDetailKey'][0] = $rsHeader[0]['customerkey'];
        $arrParam['hidDetailKey'][0] = 0;
        $arrParam['selSellingCurrency'][0] = CURRENCY['idr']; 
        $arrParam['sellingCurrencyRate'][0] = 1; 

        $arrParam['hidServiceKey'] = array();
        $arrParam['selCurrencyDetail'] = array();
        $arrParam['hidDetailItemKey'] = array();
        $arrParam['hidDetailVolumeKey'] = array();
        $arrParam['hidDetailContainerKey'] = array();
        
        if($rsHeader[0]['loadcontainertypekey']==EMKL['emklType']['fcl'] || $rsHeader[0]['loadcontainertypekey']==EMKL['emklType']['trucking']){
            $rsDetail = $this->getDetailWithRelatedInformation($id);
            $rsDetailContainer = $this->getDetailContainer($id);
            for($i=0;$i<count($rsDetail);$i++){ 
                $arrParam['hidDetailItemKey'][$i] = 0;
                $arrParam['hidContainerDetailKey'][$i] = $rsDetail[$i]['itemkey'];
                $arrParam['qty'][$i] = $rsDetail[$i]['qty'];
                $arrParam['hidServiceKey'][$i] = 4; // nembak OF
                $arrParam['selCurrencyDetail'][$i] = $arrParam['selSellingCurrency'][0];
                $arrParam['detailHBL'][$i] = $rsHeader[0]['bookingnumber'];
            }
            for($j=0;$j<count($rsDetail);$j++){
                $arrParam['hidDetailVolumeKey'][$j] = 0;
                $arrParam['selContainerDetailVolumeKey'][$j] = $rsDetail[$j]['itemkey'];
                $arrParam['qtyVolume'][$j] = $rsDetail[$j]['qty'];
                
            }
           
            //update container number
            for($k=0;$k<count($rsDetailContainer);$k++){
                $arrParam['hidDetailContainerKey'][$k] = 0;
                $arrParam['containerNo'][$k] = $rsDetailContainer[$k]['containerno'];
                $arrParam['sealNo'][$k] = $rsDetailContainer[$k]['sealno'];
                $arrParam['selUnit'][$k] = $rsDetailContainer[$k]['unitkey'];
                $arrParam['qtyContainer'][$k] = $rsDetailContainer[$k]['qty'];
                $arrParam['weightContainer'][$k] = $rsDetailContainer[$k]['weight'];
                $arrParam['volumeContainer'][$k] = $rsDetailContainer[$k]['volume'];
                
            }
            
            $arrParam['hidTotalRows'] = array(array(1)); 
            $arrParam['hidDetailItemKeyTotalRows'] = array();
            $arrParam['hidDetailItemKeyTotalRows'][1][0]= count($rsDetail);
        }else if($rsHeader[0]['loadcontainertypekey']==EMKL['emklType']['document']){
            
            $arrParam['hidDetailItemKey'][$i] = 0;
            $arrParam['hidContainerDetailKey'][$i] = 0;
            $arrParam['qty'][$i] = 1;
            $arrParam['hidServiceKey'][$i] = 16; // nembak PEB
            $arrParam['selCurrencyDetail'][$i] = $arrParam['selSellingCurrency'][0];
            $arrParam['detailHBL'][$i] = $rsHeader[0]['bookingnumber'];
            
             /*
            $arrParam['hidDetailVolumeKey'] = array();
            $arrParam['selContainerDetailVolumeKey'] = array();
            $arrParam['qtyVolume'] = array();
            $arrParam['hidDetailContainerKey'] = array();
            $arrParam['containerNo'] = array();
            $arrParam['sealNo'] = array();*/

            $arrParam['hidTotalRows'] = array(array(1)); 
            $arrParam['hidDetailItemKeyTotalRows'] = array();
            $arrParam['hidDetailItemKeyTotalRows'][1][0]= 1;
        }
        
        
        if(in_array($rsHeader[0]['loadcontainertypekey'], array(EMKL['emklType']['lcl'],EMKL['emklType']['lclnc']))){ 
			$arrParam['chkIsMaster'] = ($rsHeader[0]['loadcontainertypekey']==EMKL['emklType']['lcl']) ? 1 : 0; // jenis NC gk ad detail
            $arrParam['volume'] = $rsHeader[0]['volume']; 
            $arrParam['weight'] = $rsHeader[0]['weight']; 
            $arrParam['hidContainerKey'] = $rsHeader[0]['itemkey'];  
        } 
        
        $arrParam['hidCargoType'] = $rsHeader[0]['containertypekey']; 
        $arrParam['code'] = $rsHeader[0]['code'];
        $arrParam['hidHeaderOrderKey'] = $rsHeader[0]['pkey'];
        $arrParam['trDate'] = $this->formatDBDate($rsHeader[0]['trdate'],'d / m / Y');
        $arrParam['hidCarrierKey'] = $rsHeader[0]['carrierkey'];
        $arrParam['hidCustomerKey'] = $rsHeader[0]['customerkey'];
        $arrParam['selWarehouseKey'] = $rsHeader[0]['warehousekey'];
        $arrParam['poNumber'] = $rsHeader[0]['invoicenumber'];   
        $arrParam['selContainerType'] = $rsHeader[0]['loadcontainertypekey'];
        $arrParam['hidVesselKey'] = $rsHeader[0]['vesselkey'];
        $arrParam['vesselNumber'] = $rsHeader[0]['vesselnumber'];
        $arrParam['hidFeederKey'] = $rsHeader[0]['feederkey'];
        $arrParam['feederNumber'] = $rsHeader[0]['feedernumber'];
        $arrParam['selAirSea'] = $rsHeader[0]['transportationtypekey'];
         
        $arrParam['hidSalesKey'] = $rsHeader[0]['saleskey'];  
        $arrParam['hidPOLKey'] = $rsHeader[0]['polkey'];  
        $arrParam['hidPODKey'] = $rsHeader[0]['podkey'];
        $arrParam['hidPlaceOfDeliveryKey'] = $rsHeader[0]['placeofdeliverykey'];  
        $arrParam['hidPlaceOfReceiptKey'] = $rsHeader[0]['placeofreceiptkey'];  
        $arrParam['hidPODKey'] = $rsHeader[0]['podkey'];
        $arrParam['hidTerminalKey'] = $rsHeader[0]['terminalkey'];
        $arrParam['hidDepotKey'] = $rsHeader[0]['depotkey'];

        $arrParam['etdPol'] = $this->formatDBDate($rsHeader[0]['etdpol'],'d / m / Y',array('returnOnEmpty'=>true));
        $arrParam['etaPod'] = $this->formatDBDate($rsHeader[0]['etapod'],'d / m / Y',array('returnOnEmpty'=>true));
        $arrParam['closingDate'] = $this->formatDBDate($rsHeader[0]['closingdate'],'d / m / Y H:i'); 
        $arrParam['bookingNumber'] = $rsHeader[0]['bookingnumber'];
        $arrParam['mblNumber'] = (!empty($rsHeader[0]['mbl'])) ? $rsHeader[0]['mbl'] : $rsHeader[0]['bookingnumber'];
        
        $arrParam['containerNumber'] = $rsHeader[0]['containernumber'];
        $arrParam['trDesc'] =  $rsHeader[0]['trdesc'];
        $arrParam['aju'] = $rsHeader[0]['aju'];
        $arrParam['peb'] = $rsHeader[0]['peb'];
        $arrParam['stuffingLocation'] = $rsHeader[0]['stuffing'];
        $arrParam['stuffingIn'] = $this->formatDBDate($rsHeader[0]['stuffingin'],'d / m / Y',array('returnOnEmpty'=>true));
        $arrParam['stuffingOut'] = $this->formatDBDate($rsHeader[0]['stuffingout'],'d / m / Y');
        $arrParam['hidTruckingSupplierKey'] = $rsHeader[0]['truckingkey'];
        $arrParam['trDesc'] = $rsHeader[0]['trdesc'];
        $arrParam['itemDescription'] = $rsHeader[0]['itemdescription'];
        $arrParam['consigneeName'] = $rsHeader[0]['consigneename'];
        $arrParam['hidAgentKey'] = $rsHeader[0]['agentkey']; 
        $arrParam['attachment'] = $rsHeader[0]['attachment'];   

		$arrParam['siConsigneeName'] = $rsHeader[0]['siconsigneename'];
		$arrParam['siConsigneeAddress'] = $rsHeader[0]['siconsigneeaddress'];
		$arrParam['hidPlaceOfIssueKey'] = $rsHeader[0]['placeofissuekey'];  
		$arrParam['hidSIShipperKey'] = $rsHeader[0]['sishipperkey'];  
		$arrParam['hidNotifyKey'] = $rsHeader[0]['notifykey'];  
		$arrParam['hidNotifyKey2'] = $rsHeader[0]['notifykey2'];  
		$arrParam['selBillType'] = $rsHeader[0]['billtypekey'];  
		$arrParam['selFreightTerm'] = $rsHeader[0]['freighttermkey'];  
		$arrParam['hsCode'] = $rsHeader[0]['hscode'];  
		$arrParam['kpbc'] = $rsHeader[0]['kpbc'];  
		$arrParam['isFromJobHeader'] = 1;
		$arrParam['selIncoterms'] = $rsHeader[0]['incotermskey'];  
		$arrParam['quotationNumber'] = $rsHeader[0]['quotationnumber'];  
        
        $arrParam['sppbDate'] = $this->formatDBDate($rsHeader[0]['truckingplanningdate'],'d / m / Y');
        
		
		if(!empty(PARTNER_ACCOUNT['TMS']))	$arrParam['chkIsTrucking'] = 1;
		
        $arrayToJs = $emklJobOrder->addData($arrParam); 
        
		 
        if (!$arrayToJs[0]['valid']){ 
            $this->addErrorLog(false, '<strong>'.$rsHeader[0]['code'] . '</strong>. '.$this->errorMsg[201].' '.$arrayToJs[0]['message'], true); 
        }else{ 
            
            $JOKey = $arrayToJs[0]['data']['pkey'];
            $JOHeaderKey = $arrayToJs[0]['data']['headerorderkey'];
            
			$usePrepaidExpense = $this->loadSetting('usePrepaidExpense');
			
            //$emklJobOrder->changeStatus($JOKey,TRANSACTION_STATUS['konfirmasi']); 
 	        $rsPurchase = $emklPurchaseOrder-> searchDataRow( array(  $emklPurchaseOrder->tableName.'.pkey') , 
                                '   and '.$emklPurchaseOrder->tableName.'.refjoheaderkey = '.$this->oDbCon->paramString($id).'
                                    and '.$emklPurchaseOrder->tableName.'.statuskey in ('.TRANSACTION_STATUS['menunggu'].','.TRANSACTION_STATUS['konfirmasi'].','.TRANSACTION_STATUS['selesai'].')'  
                       ); 
             
            $countRs = count($rsPurchase);
            for($i=0;$i<$countRs;$i++){
                $emklPurchaseOrder->updateRefkey($rsPurchase[$i]['pkey'],$JOHeaderKey);
                $prepaidExpense->updateRefkey($rsPurchase[$i]['pkey'],$JOHeaderKey);
				
                //$sql = 'update ' . $emklPurchaseOrder->tableName.' set  refkey = '.$this->oDbCon->paramString($arrayToJs[0]['data']['pkey']).'  where  pkey = '.$this->oDbCon->paramString($rsPurchase[$i]['pkey']);
                //$this->setLog($sql,true);
                //$this->oDbCon->execute($sql);
            }  
        }

        
	} 
	 
	function cancelTrans($rsHeader,$copy){  
        
		$id = $rsHeader[0]['pkey'];
        
        $emklJobOrder = new EMKLJobOrder();
		$rsJobOrder = $emklJobOrder-> searchDataRow( array(  $emklJobOrder->tableName.'.pkey'  ) , 
                                '   and '.$emklJobOrder->tableName.'.headerorderkey = '.$this->oDbCon->paramString($id).'
                                    and '.$emklJobOrder->tableName.'.statuskey in ('.TRANSACTION_STATUS['menunggu'].')'  
                       );  
        
		for($i=0;$i<count($rsJobOrder);$i++) 
          $emklJobOrder->changeStatus($rsJobOrder[$i]['pkey'],4,'',false,true); 
        
        
        $emklPurchaseOrder = new EMKLPurchaseOrder();
        $rsPurchase = $emklPurchaseOrder-> searchDataRow( array(  $emklPurchaseOrder->tableName.'.pkey', $emklPurchaseOrder->tableName.'.code'  ) , 
                                '   and '.$emklPurchaseOrder->tableName.'.refjoheaderkey = '.$this->oDbCon->paramString($id).'
                                    and '.$emklPurchaseOrder->tableName.'.statuskey in ('.TRANSACTION_STATUS['menunggu'].')'  
                       );  
        
		for($i=0;$i<count($rsPurchase);$i++) 
          $emklPurchaseOrder->changeStatus($rsPurchase[$i]['pkey'],4,'',false,true); 
        
    
		if ($copy)
			$this->copyDataOnCancel($id);	
         
	} 
     
  /* function getBillType($pkey=''){ 
       
	   $sql = 'select
	   			'.$this->tableBillType .'.pkey, 
	   			'.$this->tableBillType .'.name 
              from
			  	'.$this->tableBillType .' 
			  where
			  	'.$this->tableBillType .'.statuskey = 1';
       if(!empty($pkey))
            $sql .= ' and pkey = '.$this->oDbCon->paramString($pkey);
        
       $sql .=' order by name asc';
         
       return $this->oDbCon->doQuery($sql);
	
   }*/

    function getDetailWithRelatedInformation($pkey,$criteria=''){
        
            $sql = 'select
	   			'.$this->tableNameDetail .'.*,
                '.$this->tableContainer.'.name as itemname,
                '.$this->tableContainer.'.volume
			  from
			  	'. $this->tableContainer .', 
			  	'. $this->tableNameDetail .' 
			  where  
                '.$this->tableNameDetail .'.itemkey = '.$this->tableContainer .'.pkey and
			  	'.$this->tableNameDetail .'.refkey = '.$this->oDbCon->paramString($pkey);
         

        $sql .= $criteria;
		return $this->oDbCon->doQuery($sql);
    }
     
   function getDetailContainer($pkey,$criteria=''){
        
            $sql = 'select
	   			'.$this->tableContainerDetail .'.*,
                '.$this->tableItemUnit.'.name as unitname
			  from
			  	'. $this->tableContainerDetail .' 
            left join '.$this->tableItemUnit.' on 
                    '.$this->tableContainerDetail .'.unitkey =  '.$this->tableItemUnit.'.pkey
			  where  
			  	'.$this->tableContainerDetail .'.refkey = '.$this->oDbCon->paramString($pkey);
         
                
        $sql .= $criteria;
		return $this->oDbCon->doQuery($sql);
    }
     
     
    
    function normalizeParameter($arrParam, $trim = false){ 
        
        $arrParam['selTypeOfJob'] = $this->jobType;  
        
        // kalo sdh add, jgn ubah tgl
        // nanti kode nya lompat
        
        if(isset($arrParam['hidId']) && !empty($arrParam['hidId'])){
           unset($this->arrData['trdate']);
        }
        
            
        // kalo bkn fcl & trucking
        if($arrParam['selContainerType'] != EMKL['emklType']['fcl'] && $arrParam['selContainerType'] != EMKL['emklType']['trucking']){
            $arrParam['selContainerDetailKey'] = array();
            $arrParam['qty'] = array(); 
        }
        
        
        // kalo bkn lcl
        if(!in_array($arrParam['selContainerType'], array(EMKL['emklType']['lcl'],EMKL['emklType']['lclnc']))){
           $arrParam['volume'] = 0; 
           $arrParam['weight'] = 0; 
           $arrParam['hidContainerKey'] = 0; 
        }
        
        // update cache container number, di TEL masih pake model textarea
        if(isset($arrParam['containerNo'])){
            $containerNoCache = array();
            
            $totalContainerNumber = count($arrParam['containerNo']);
            for($i=0;$i<$totalContainerNumber;$i++){ 
                if(empty( $arrParam['containerNo'][$i] )) continue;
                array_push($containerNoCache, $arrParam['containerNo'][$i] . ' / ' . $arrParam['sealNo'][$i]); 
            }
            $arrParam['containerNumber'] = implode(chr(13), $containerNoCache);
        }
                 
        // gk perlu validasi, kebykan validasi bingung ntar
        // diatas sudah di 0 kan
        
        $arrContainerType = array();
        if(!empty($arrParam['hidContainerKey']))
            array_push($arrContainerType, $arrParam['hidContainerKey']);
        
        foreach($arrParam['selContainerDetailKey'] as $value)
            if(!empty($value))
                array_push($arrContainerType,$value);
         
        $containercache = '';
        if(!empty($arrContainerType)){ 
            $container = new Container();
            $rsContainer = $container->searchDataRow( array(  $container->tableName.'.containertypekey') , 
                                                    ' and '.$container->tableName.'.pkey in('.$this->oDbCon->paramString($arrContainerType,',').')'
                                                    ); 


            $arrContainerType = array_column($rsContainer,'containertypekey');
            $containercache = $this->createFieldCache($arrContainerType);
        } 
        
        $arrParam['containertypecache'] = $containercache;
         
        $arrParam = parent::normalizeParameter($arrParam,true); 
         
        return $arrParam;
    }

    
    function getEmklType($pkey=''){ 
       
	   $sql = 'select
	   			'.$this->tableEmklType .'.pkey, 
	   			'.$this->tableEmklType .'.name 
              from
			  	'.$this->tableEmklType .' 
			  where
			  	'.$this->tableEmklType .'.statuskey = 1';
                
        if(!empty($pkey))
            $sql .= ' and pkey in ( '.$this->oDbCon->paramString($pkey,',').')';
        
        
        $sql .=' order by orderlist asc, pkey asc';
         
		return $this->oDbCon->doQuery($sql);
	
   }
    
    function updateTotalDebitNote($arrKey){
            // harus update juga ke job order
            $emklJobOrder = new EMKLJobOrder();
            $emklJobOrder->updateTotalDebitNote($arrKey);
    }  
    
  function manipulateDataBeforeUpdateData($arrParam){
//        if(isset($this->domainConfig) && !empty($this->domainConfig['fortis'])){
//              
//             if(EMKL['jobType']['domestic'] == $this->jobType){
//                $arrParam['paramPrefixCode'] = '60';
//             }else if ($arrParam['selContainerType']  == EMKL['container']['trucking']){ 
//                 $arrParam['paramPrefixCode'] = '42'; 
//             }elseif ($arrParam['selAirSea'] == EMKL['shipping']['sea'] &&  $this->jobType == EMKL['jobType']['export']){
//                 $arrParam['paramPrefixCode'] = '28';  
//             }elseif ($arrParam['selAirSea'] == EMKL['shipping']['sea'] &&  $this->jobType == EMKL['jobType']['import']){
//                 $arrParam['paramPrefixCode'] = '46';  
//             }elseif ($arrParam['selAirSea'] == EMKL['shipping']['air'] &&  $this->jobType == EMKL['jobType']['export']){
//                 $arrParam['paramPrefixCode'] = '64';  
//             }elseif ($arrParam['selAirSea'] == EMKL['shipping']['air'] &&  $this->jobType == EMKL['jobType']['import']){
//                 $arrParam['paramPrefixCode'] = '82';  
//             }else{
//                  $arrParam['paramPrefixCode'] = '00';   // incase gk ketemu
//             }
//              
//        }
         
        return $arrParam;
    }
    
    function getIncoterms($pkey=''){ 
        
        $emklJobOrder = new EMKLJobOrder();
        return $emklJobOrder->getIncoterms($pkey); 
	
   }
    
}
?>