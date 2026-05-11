<?php
  
class EMKLReminderJobOrder extends BaseClass{ 
 
    function __construct($jobType = ''){
		
		parent::__construct();
       
		$this->tableName = 'emkl_reminder_job_order_header';
		$this->tableNameDetail = 'emkl_reminder_job_order_detail';
		$this->tableVolumeDetail = 'emkl_reminder_job_order_detail_volume';
		$this->tableContainerDetail = 'emkl_reminder_job_order_detail_container';
		$this->tableStatus = 'transaction_status';
		$this->tableSupplier = 'supplier';
		$this->tablePort = 'port';
        $this->tableLocation = 'location'; 
		$this->tableItem = 'item';
		$this->tableItemUnit = 'item_unit';
        $this->tableConsignee = 'consignee';
        $this->tableCustomer = 'customer';
        $this->tableContainerType = 'container_type';
        $this->tableContact = 'contact_person';
        $this->tableJobType = 'emkl_import_export';
        $this->tableTransportationType = 'emkl_air_sea';
        $this->tableLoadContainer = 'emkl_fcl_lcl';
        $this->tableVolumeUnit = 'emkl_volume_unit';
        $this->tableFreightTerm = 'emkl_freight_term';
        $this->tableWarehouse = 'warehouse';
        $this->tableDepot = 'depot';
	    $this->tableTerminal = 'terminal'; 
        $this->tableContainer = 'container';
        $this->tableCurrency = 'currency';
        $this->tableCity = 'city';
        $this->tableEmployee = 'employee'; 
	    $this->tableVessel = 'vessel';
		$this->securityObject = 'EMKLReminderJobOrder';
        $this->uploadFileFolder = 'emkl-reminder-job-order/';
        $this->isTransaction = true;
        $this->jobType = $jobType;
        $this->allowedStatusForEdit = array(1,2);

        $this->arrDataDetail = array();   
        $this->arrDataDetail['pkey'] = array('hidDetailKey');
        $this->arrDataDetail['refkey'] = array('pkey','ref'); 
        $this->arrDataDetail['itemkey'] = array('hidServiceDetailKey'); 
        $this->arrDataDetail['trdate'] = array('dateDetail','date');
        $this->arrDataDetail['trdesc'] = array('descDetail');
        $this->arrDataDetail['isservice'] = array('chkIsService'); 

                  
        $this->arrVolumeDetail = array(); 
        $this->arrVolumeDetail['pkey'] = array('hidDetailVolumeKey');
        $this->arrVolumeDetail['refkey'] = array('pkey','ref');
        $this->arrVolumeDetail['itemkey'] = array('selContainerDetailVolumeKey');
        $this->arrVolumeDetail['qty'] = array('qtyVolume','number');
        

        $this->arrContainerDetail = array(); 
        $this->arrContainerDetail['pkey'] = array('hidDetailContainerKey');
        $this->arrContainerDetail['refkey'] = array('pkey','ref');
        $this->arrContainerDetail['containerno'] = array('containerNo',array('mandatory'=>true));
        $this->arrContainerDetail['sealno'] = array('sealNo');
        
        $arrDetails = array(); 
        array_push($arrDetails, array('dataset' => $this->arrDataDetail, 'tableName' => $this->tableNameDetail)); 
        array_push($arrDetails, array('dataset' => $this->arrVolumeDetail, 'tableName' => $this->tableVolumeDetail));     
        array_push($arrDetails, array('dataset' => $this->arrContainerDetail, 'tableName' => $this->tableContainerDetail));   
        
        $this->arrData = array();
        $this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails));
        $this->arrData['code'] = array('code');
        $this->arrData['trdate'] = array('trDate','date');  
        $this->arrData['consigneekey'] = array('hidConsigneeKey');
        $this->arrData['carrierkey'] = array('hidCarrierKey');
        $this->arrData['warehousekey'] = array('selWarehouseKey');
        $this->arrData['jobtypekey'] = array('selTypeOfJob');
        $this->arrData['transportationtypekey'] = array('selAirSea');
        $this->arrData['loadcontainertypekey'] = array('selContainerType');
        $this->arrData['itemkey'] = array('hidContainerKey');
        $this->arrData['volume'] = array('volume', 'number');
        $this->arrData['weight'] = array('weight','number');     
        $this->arrData['volumetype'] = array('selVolumeType');
        $this->arrData['agentkey'] = array('hidAgentKey');
        $this->arrData['mblnumber'] = array('mblNumber'); 
        $this->arrData['polkey'] = array('hidPOLKey');
        $this->arrData['podkey'] = array('hidPODKey');
        $this->arrData['etdpol'] = array('etdPol','date');
        $this->arrData['etapod'] = array('etaPod','date');
        $this->arrData['trdesc'] = array('trDesc');
        $this->arrData['containernumber'] = array('containerNumber');
        $this->arrData['vesselkey'] = array('hidVesselKey');
        $this->arrData['vesselnumber'] = array('vesselNumber');
        $this->arrData['statuskey'] = array('selStatus');
  
        $this->arrData['customerkey'] = array('hidCustomerKey');
        $this->arrData['containertypekey'] = array('hidCargoType');
        $this->arrData['itemdescription'] = array('itemDescription');
        
        $this->arrData['consigneename'] = array('consigneeName');
        
        $this->arrData['datedoc'] = array('dateDoc','date'); 

        $this->arrData['depotkey'] = array('hidDepotKey');
        $this->arrData['terminalkey'] = array('hidTerminalKey');

        $this->arrData['docdate'] = array('docDate','date');
        $this->arrData['isdocdate'] = array('chkIsDocDate');
        $this->arrData['transferdate'] = array('transferDate','date');
        $this->arrData['istransferdate'] = array('chkIsTransferDate');
        $this->arrData['profitlossdate'] = array('profitLossDate','date');
        $this->arrData['isprofitlossdate'] = array('chkIsPLDate');
        $this->arrData['voucherdate'] = array('voucherDate','date');
        $this->arrData['isvoucherdate'] = array('chkIsVoucherDate');
        $this->arrData['paymentcarrierdate'] = array('paymentCarrierDate','date');
        $this->arrData['ispaymentcarrierdate'] = array('chkIsPaymentCarrierDate');
        $this->arrData['amsdate'] = array('amsDate','date');
        $this->arrData['isamsdate'] = array('chkIsAMSDate');
        $this->arrData['isfdate'] = array('isfDate','date');
        $this->arrData['isisfdate'] = array('chkIsISFDate');
        $this->arrData['emanifestdate'] = array('emanifestDate','date');
        $this->arrData['isemanifestdate'] = array('chkIsEmanifestDate');
        $this->arrData['ehbldate'] = array('ehblDate','date');
        $this->arrData['isehbldate'] = array('chkIsEHBLDate');
        $this->arrData['trizdate'] = array('trizDate','date');
        $this->arrData['istrizdate'] = array('chkIsTrizDate');
        $this->arrData['telexdate'] = array('telexDate','date');
        $this->arrData['telextype'] = array('selTelexType');
        $this->arrData['porkey'] = array('hidPORKey');
        $this->arrData['poikey'] = array('hidPOIKey');
        $this->arrData['servicetypekey'] = array('selServiceType');
        $this->arrData['mbltypekey'] = array('selMBLType');
        $this->arrData['hbltypekey'] = array('selHBLType');
        $this->arrData['hbldate'] = array('hblDate','date');
        $this->arrData['mbldate'] = array('mblDate','date');
        $this->arrData['hblnumber'] = array('hblNumber');
        $this->arrData['ismbltype'] = array('chkIsMBLType');
        $this->arrData['ishbltype'] = array('chkIsHBLType');
        
            
        $this->arrDataListAvailableColumn = array();
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code', 'default'=>true, 'width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'date','title' => 'date','dbfield' => 'trdate','default'=>true, 'width' => 80, 'align' =>'center', 'format' => 'date'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'warehouse','title' => 'warehouse','dbfield' => 'warehousename', 'width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'containertype','title' => 'type','dbfield' => 'containertype', 'default'=>true,'width' => 60));
        array_push($this->arrDataListAvailableColumn, array('code' => 'etdpol','title' => 'etd','dbfield' => 'etdpol','default'=>true, 'width' => 80,'align' =>'center', 'format' => 'date'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'etapod','title' => 'eta','dbfield' => 'etapod','default'=>true, 'width' => 80,'align' =>'center', 'format' => 'date'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'shipper','title' => 'shipper','dbfield' => 'customername','default'=>true,'width' => 250)); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'pod','title' => 'pod','dbfield' => 'podname','default'=>true,'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'pol','title' => 'pol','dbfield' => 'polname','default'=>true,'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'jobType','title' => 'jobType','dbfield' => 'jobtypeunion','default'=>true,'width' => 150));        
        array_push($this->arrDataListAvailableColumn, array('code' => 'note','title' => 'note','dbfield' => 'trdesc','width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 80));
         
        array_push($this->filterCriteria, array('title' => $this->lang['warehouse'], 'field' => 'warehousekey'));
        array_push($this->filterCriteria, array('title' => $this->lang['containerType'], 'field' => 'containertypekey', 'table' => $this->tableContainerType));
        
//        $this->printMenu = array();  
//        array_push($this->printMenu,array('code' => 'printTransaction', 'name' => $this->lang['printTransaction'],  'icon' => 'print', 'url' => 'print/emklJobOrderExport')); 
//           
        $this->includeClassDependencies(array(
              'Port.class.php',
              'Container.class.php',
              'Customer.class.php',
              'Warehouse.class.php',
              'Item.class.php', 
              'Consignee.class.php',
              'Vessel.class.php',
              'Terminal.class.php',
              'Location.class.php',
              'Supplier.class.php',
              'Currency.class.php',
              'Service.class.php',
              'Depot.class.php',
              'Consignee.class.php',
              'GeneralJournal.class.php',

        ));

        $this->overwriteConfig();
            
   }
   
    function getQuery(){
	   
        $sql = '
			SELECT
              '.$this->tableName.'.* ,
              '.$this->tableCustomer.'.name as customername, 
              '.$this->tableWarehouse.'.name as warehousename,
			  '.$this->tableStatus.'.status as statusname ,
			  '.$this->tableTransportationType.'.name as transportationtype,
              '.$this->tableLoadContainer.'.name as loadcontainertype,
			  '.$this->tableJobType.'.name as jobtype ,
			  '.$this->tableContainer.'.name as containername ,
			  '.$this->tableVessel.'.name as vesselname ,
              pol.name as polname,
              pod.name as podname,
              carrier.name as carriername, 
              agent.name as agentname,
              '.$this->tableDepot.'.name as depotname,
              concat_ws(", ",'.$this->tableJobType.'.name,'.$this->tableTransportationType.'.name,'.$this->tableLoadContainer.'.name) as jobtypeunion,
              '.$this->tableTerminal.'.name as terminalname,
              '.$this->tableVolumeUnit.'.name as volumeunit,
              '.$this->tableContainerType.'.name as containertype, 
			  por.name as porname,
			  poi.name as poiname
			FROM '.$this->tableStatus.',
                 '.$this->tableName.' 
                    left join '.$this->tableContainer.' on  '.$this->tableName.'.itemkey = '.$this->tableContainer.'.pkey 
                    left join '.$this->tablePort.' pol on  '.$this->tableName.'.polkey = pol.pkey 
                    left join '.$this->tablePort.' pod on  '.$this->tableName.'.podkey = pod.pkey
                    left join '.$this->tableLocation.' por on  '.$this->tableName.'.porkey = por.pkey 
                    left join '.$this->tableLocation.' poi on  '.$this->tableName.'.poikey = poi.pkey
                    left join '.$this->tableSupplier.' carrier on  '.$this->tableName.'.carrierkey = carrier.pkey
                    left join '.$this->tableVessel.' on  '.$this->tableName.'.vesselkey = '.$this->tableVessel.'.pkey 
                    left join '.$this->tableSupplier.' agent on  '.$this->tableName.'.agentkey = agent.pkey 
                    left join '.$this->tableDepot.' on  '.$this->tableName.'.depotkey = '.$this->tableDepot.'.pkey 
                    left join '.$this->tableTerminal.' on  '.$this->tableName.'.terminalkey = '.$this->tableTerminal.'.pkey 
                    left join '.$this->tableCustomer.'  on '.$this->tableName.'.customerkey = '.$this->tableCustomer.'.pkey
                    left join '.$this->tableContainerType.' on  '.$this->tableName.'.containertypekey = '.$this->tableContainerType.'.pkey  ,
                 '.$this->tableWarehouse.',  
                 '.$this->tableJobType.',
                 '.$this->tableTransportationType.',
                 '.$this->tableLoadContainer.',
                 '.$this->tableVolumeUnit.'
			WHERE 
                '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey and 
                '.$this->tableName.'.warehousekey = '.$this->tableWarehouse.'.pkey and 
                '.$this->tableName.'.jobtypekey = '.$this->tableJobType.'.pkey and 
                '.$this->tableName.'.transportationtypekey = '.$this->tableTransportationType.'.pkey and
                '.$this->tableName.'.loadcontainertypekey = '.$this->tableLoadContainer.'.pkey and
                '.$this->tableName.'.volumetype = '.$this->tableVolumeUnit.'.pkey';
        
        
        if (!empty($this->jobType))
            $sql .= ' and jobtypekey in ('.$this->jobType.')  ';

            
 		$sql .= $this->criteria ;
        $sql .= $this->getWarehouseCriteria() ;
        $sql .= $this->getCustomerCriteria() ;
        
        return $sql;
    }
        
    function validateForm($arr,$pkey = ''){     
        
	    $customer = new Customer();
        $consignee = new Consignee();
        $item = new Item();
        $carrier= new Supplier();
          
		$arrayToJs = parent::validateForm($arr,$pkey); 
        
		$carrierkey = $arr['hidCarrierKey'];  
        $containerkey = $arr['hidContainerKey'];
	    $customerkey = $arr['hidCustomerKey'];   
        $cargoType = $arr['hidCargoType'];      
        

        $serviceDetailKey = $arr['hidServiceKey'];
        $selContainerType = $arr['selContainerType'];
        $shipmentType = $arr['selAirSea']; 
		$warehousekey = $arr['selWarehouseKey'];

         
		//$customersellingkey = $arr['hidCustomerDetailKey'];   
        $rs = (!empty($pkey)) ? $this->getDataRowById($pkey) : array() ;
         
        //validasi kalo status gk menunggu / konfirmasi gk bisa edit 
		if (!empty($rs)){ 
			if ($rs[0]['statuskey'] > 5){
				$this->addErrorList($arrayToJs,false,$this->errorMsg[212]);
			}
		} 
         
        if (empty($warehousekey))  
            $this->addErrorList($arrayToJs,false,$this->errorMsg['warehouse'][1]); 
       
        if(empty($customerkey))
            $this->addErrorList($arrayToJs,false, $this->errorMsg['shipper'][1]); 
 
        $totalDetail = count($arr['hidDetailKey']);  
        for($i=0;$i<$totalDetail;$i++){ 
            $detailkey = $arr['hidDetailKey'][$i];

        }



        $containerDetail = array();

        if($selContainerType == EMKL['container']['lcl']){
            $containerDetail = array($containerkey);
            
            // KALO LCL 
            if(empty($containerkey)) 
                $this->addErrorList($arrayToJs,false,$this->errorMsg['container'][1]); 
            
            if(!$isMaster){
                $rsRef = $this->getDataRowById($refkey);
                if(empty($rsRef))
                        $this->addErrorList($arrayToJs,false, $this->errorMsg['reference'][1]);
            }      
 
        }else if($selContainerType == EMKL['container']['document']){ 
            // document gk perlu validasi
            
        }else{  
            $containerDetail = $arr['selContainerDetailVolumeKey'];  
             if ( $shipmentType == EMKL['shipping']['sea'] ){  
                // KALO FCL dan tipenya SEA
                foreach($containerDetailKey as $row){    
                    foreach($row as $value)   
                       if(empty($value))
                           $this->addErrorList($arrayToJs,false,$this->errorMsg['container'][1]);

                }  
            } 
        }
        
        if(!empty($containerDetail)){
                // cek jenis cargo
                $container = new Container();
                $rsContainer = $container->searchDataRow( array($container->tableName.'.pkey', $container->tableName.'.containertypekey'),
                                                            ' and '.$container->tableName.'.pkey in ('.$this->oDbCon->paramString($containerDetail,',').')' 
                                                        );

    
        }
        

		
		return $arrayToJs;
	 }

    
    
    function generateDefaultQueryForAutoComplete($returnField){ 
        
        $sql = 'select
					'.$returnField['key'].',
					'.$returnField['value'].' as value
				from 
					'.$this->tableName . ', 
                    '.$this->tableStatus.' 
				where  		 
					'.$this->tableName . '.statuskey = '.$this->tableStatus.'.pkey  
			';
           
         return $sql;
     }


     
     
 	function validateCancel($rsHeader,$autoChangeStatus=false){  

		$pkey = $rsHeader[0]['pkey'];

	 }
    
     function cancelTrans($rsHeader,$copy){  
         
        $pkey = $rsHeader[0]['pkey'];

		if ($copy)
			$this->copyDataOnCancel($rsHeader[0]['pkey']);	  
        
	}    
    
    	 
	function validateConfirm($rsHeader){
        
        $id = $rsHeader[0]['pkey'];
        $rsDetail = $this->getDetailById($id);
         
         // validasi nilai rate gk boleh satu kalo bukan IDR
        $totalDetail = count($rsDetail);
        for($i=0;$i<$totalDetail;$i++){ 
   
             
        
        }  
        
    }		
	     	 
	function validateClose($rsHeader){
        
        parent::validateClose($rsHeader);
        
        $id = $rsHeader[0]['pkey'];
  
    }		
	  
    
	function confirmTrans($id){
		 
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
        
        
       $sql .=' order by name asc';
         
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
        
       $sql .=' order by name asc';
         
       return $this->oDbCon->doQuery($sql);
	
   }
    
    function getLoadContainer($pkey=''){ 
       
	   $sql = 'select
	   			'.$this->tableLoadContainer .'.pkey, 
	   			'.$this->tableLoadContainer .'.name 
              from
			  	'.$this->tableLoadContainer .' 
			  where
			  	'.$this->tableLoadContainer .'.statuskey = 1 order by orderlist asc, pkey asc';
        
       if(!empty($pkey))
            $sql .= ' and pkey = '.$this->oDbCon->paramString($pkey);
        
       //$sql .=' order by name asc';
         
       return $this->oDbCon->doQuery($sql);
	
   }
    
    function getVolumeUnit($pkey=''){ 
       
	   $sql = 'select
	   			'.$this->tableVolumeUnit .'.pkey, 
	   			'.$this->tableVolumeUnit .'.name 
              from
			  	'.$this->tableVolumeUnit .' 
			  where
			  	'.$this->tableVolumeUnit .'.statuskey = 1';
       
       if(!empty($pkey))
            $sql .= ' and pkey = '.$this->oDbCon->paramString($pkey);
        
       $sql .=' order by name asc';
         
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
    
    function getDetailWithRelatedInformation($pkey,$criteria=''){ 
       
	   $sql = 'select
	   			'.$this->tableNameDetail .'.*, 
                 '.$this->tableItem.'.name as servicename

              from
			  	'.$this->tableNameDetail .'
				   left join ' .$this->tableItem.' on   ' .$this->tableNameDetail.'.itemkey = '.$this->tableItem.'.pkey
			  where refkey in ('.$this->oDbCon->paramString($pkey,',') . ') 
              order by '.$this->tableNameDetail .'.pkey asc 
             ';
       
         $sql .= $criteria;
           
		return $this->oDbCon->doQuery($sql);
	
   }
	  
    function normalizeParameter($arrParam, $trim=false){
        

		$arrParam['selVolumeType'] = ($arrParam['selAirSea'] == EMKL['shipping']['air']) ?  EMKL['volume']['kg'] : EMKL['volume']['cbm'];
        
        $arrParam['selTypeOfJob'] = $this->jobType; 

     
        if ( in_array($arrParam['selContainerType'], array(EMKL['container']['lcl'],EMKL['container']['lclnc']))){
                // kalo LCL 
                $arrParam['hidConsigneeKey'] = 0; 
  
        }else{
            // kalo FCL 
            $arrParam['hidContainerKey'] = 0; // karena 1 form bisa lebih dr 1 jenis container
            $arrParam['volume'] = 0; 
            $arrParam['weight'] = 0; 

        }
        
        if($arrParam['chkIsPaymentCarrierDate'] == 0)
            $arrParam['paymentCarrierDate'] = DEFAULT_EMPTY_DATE;
        
        if($arrParam['chkIsVoucherDate'] == 0)
            $arrParam['voucherDate'] = DEFAULT_EMPTY_DATE;    
        
        if($arrParam['chkIsTransferDate'] == 0)
            $arrParam['transferDate'] = DEFAULT_EMPTY_DATE;    
        
        if($arrParam['chkIsTransferDate'] == 0)
            $arrParam['transferDate'] = DEFAULT_EMPTY_DATE;    
   
        if($arrParam['chkIsDocDate'] == 0)
            $arrParam['docDate'] = DEFAULT_EMPTY_DATE;    

        if($arrParam['chkIsPLDate'] == 0)
            $arrParam['profitLossDate'] = DEFAULT_EMPTY_DATE;    
        
        if($arrParam['chkIsAMSDate'] == 0)
            $arrParam['amsDate'] = DEFAULT_EMPTY_DATE;    

        if($arrParam['chkIsISFDate'] == 0)
            $arrParam['isfDate'] = DEFAULT_EMPTY_DATE;    
    
        if($arrParam['chkIsEmanifestDate'] == 0)
            $arrParam['emanifestDate'] = DEFAULT_EMPTY_DATE;    

        if($arrParam['chkIsTrizDate'] == 0)
            $arrParam['trizDate'] = DEFAULT_EMPTY_DATE;    
        
        if($arrParam['chkIsEHBLDate'] == 0)
            $arrParam['ehblDate'] = DEFAULT_EMPTY_DATE;    
             
        if($arrParam['selTelexType'] <> 2)
            $arrParam['telexDate'] = DEFAULT_EMPTY_DATE;    
             
		
			
        if($arrParam['chkIsMBLType'] == 0)
            $arrParam['selMBLType'] = 0;
        
        if($arrParam['chkIsHBLType'] == 0)
            $arrParam['selHBLType'] = 0;
         
        // kalo bkn fcl & trucking
        if($arrParam['selContainerType'] != EMKL['emklType']['fcl'] && $arrParam['selContainerType'] != EMKL['emklType']['trucking']){
            $arrParam['selContainerDetailVolumeKey'] = array();
            $arrParam['qtyVolume'] = array(); 
        }         

        $arrParam = parent::normalizeParameter($arrParam,true);
           
        
        return $arrParam;
    }

    
    function getDetailVolume($pkey,$criteria=''){
        
            $sql = 'select
	   			'.$this->tableVolumeDetail .'.*,
                '.$this->tableContainer.'.name as itemname,
                '.$this->tableContainer.'.volume
			  from
			  	'. $this->tableContainer .', 
			  	'. $this->tableVolumeDetail .' 
			  where  
                '.$this->tableVolumeDetail .'.itemkey = '.$this->tableContainer .'.pkey and
			  	'.$this->tableVolumeDetail .'.refkey in ('.$this->oDbCon->paramString($pkey,',').')';
         

        $sql .= $criteria;
        
        //$this->setLog($sql,true);
		return $this->oDbCon->doQuery($sql);
    }
    
    
     function getDetailContainer($pkey,$criteria=''){
        
            $sql = 'select
	   			'.$this->tableContainerDetail .'.*
			  from
			  	'. $this->tableContainerDetail .' 
			  where  
			  	'.$this->tableContainerDetail .'.refkey in ('.$this->oDbCon->paramString($pkey,',').')';

                
        $sql .= $criteria;
		return $this->oDbCon->doQuery($sql);
    }

    function afterUpdateData($arrParam,$action){
         $pkey = $arrParam['pkey']; 
    }
   

}
?>