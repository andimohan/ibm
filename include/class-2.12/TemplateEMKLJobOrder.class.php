<?php 

class TemplateEMKLJobOrder extends BaseClass 
{

    function __construct($jobType = '')
    {

        parent::__construct();

        $this->tableName = 'template_emkl_job_order';
        $this->tableVolumeDetail = 'template_emkl_job_order_detail_volume';
        $this->tableCommodityDetail = 'template_emkl_job_order_detail_commodity';
        $this->tableCommodity = 'commodity';
        $this->tableWarehouse = 'warehouse';
        $this->tableSupplier = 'supplier';
        $this->tableCustomer = 'customer';
        $this->tableStatus = 'master_status';
        $this->tablePort = 'port';
        $this->tableBillType = 'emkl_bill_type';
        $this->tableLocation = 'location';
        $this->tableItem = 'item';
        $this->tableItemUnit = 'item_unit';
        $this->tableConsignee = 'consignee';
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
        $this->tableShipmentTerm = 'shipment_term';
        $this->tableShipmentType = 'shipment_type';
        $this->tableCountry = 'country';
        $this->tableServiceCategory = 'service_category';
        $this->tableContinent = 'continent'; 

        $this->jobType = $jobType;

        $this->securityObject = 'TemplateEMKLJobOrder';

        $this->arrVolumeDetail = array(); 
        $this->arrVolumeDetail['pkey'] = array('hidDetailVolumeKey');
        $this->arrVolumeDetail['refkey'] = array('pkey','ref');
        $this->arrVolumeDetail['itemkey'] = array('selContainerDetailVolumeKey');
        $this->arrVolumeDetail['qty'] = array('qtyVolume','number');


        $this->arrCommodityDetail = array(); 
        $this->arrCommodityDetail['pkey'] = array('hidDetailCommodityKey');
        $this->arrCommodityDetail['refkey'] = array('pkey', 'ref');
        $this->arrCommodityDetail['commoditykey'] = array('hidCommodityKey'); 

        $arrDetails = array(); 
        array_push($arrDetails, array('dataset' => $this->arrVolumeDetail, 'tableName' => $this->tableVolumeDetail));  
        array_push($arrDetails, array('dataset' => $this->arrCommodityDetail, 'tableName' => $this->tableCommodityDetail));    
        $this->arrData = array();
        $this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails));
        $this->arrData['code'] = array('code');
        $this->arrData['warehousekey'] = array('selWarehouseKey');
        $this->arrData['trdate'] = array('trDate','date'); 
        $this->arrData['name'] = array('name');
        $this->arrData['consigneekey'] = array('hidConsigneeKey');
        $this->arrData['supplierkey'] = array('hidSupplierKey');
        $this->arrData['carrierkey'] = array('hidCarrierKey');
        $this->arrData['customerkey'] = array('hidCustomerKey');
        $this->arrData['terminalkey'] = array('hidTerminalKey');
        $this->arrData['terminalkey'] = array('hidTerminalKey');
        $this->arrData['jobtypekey'] = array('selTypeOfJob');
        $this->arrData['transportationtypekey'] = array('selAirSea');
        $this->arrData['loadcontainertypekey'] = array('selContainerType');
        $this->arrData['itemkey'] = array('hidContainerKey');
        $this->arrData['volume'] = array('volume', 'number');
        $this->arrData['weight'] = array('weight', 'number');
        $this->arrData['volumetype'] = array('selVolumeType');
        $this->arrData['saleskey'] = array('hidSalesKey');
        $this->arrData['agentkey'] = array('hidAgentKey');
        $this->arrData['mblnumber'] = array('mblNumber');
        $this->arrData['polkey'] = array('hidPOLKey');
        $this->arrData['podkey'] = array('hidPODKey');
        $this->arrData['etdpol'] = array('etdPol', 'date');
        $this->arrData['etapod'] = array('etaPod', 'date');
        //$this->arrData['closingdate'] = array('closingDate', 'date');
        $this->arrData['trdesc'] = array('trDesc');
        $this->arrData['bookingnumber'] = array('bookingNumber');
        $this->arrData['containernumber'] = array('containerNumber');
        $this->arrData['depotkey'] = array('hidDepotKey');
        $this->arrData['vesselkey'] = array('hidVesselKey');
        $this->arrData['vesselnumber'] = array('vesselNumber');
        $this->arrData['feederkey'] = array('hidFeederKey');
        $this->arrData['feedernumber'] = array('feederNumber');
        $this->arrData['statuskey'] = array('selStatus');

        $this->arrData['aju'] = array('aju');    
        $this->arrData['peb'] = array('peb');    
        $this->arrData['stuffinglocation'] = array('stuffingLocation');
        $this->arrData['stuffingin'] = array('stuffingIn','date');
        $this->arrData['stuffingout'] = array('stuffingOut','date');
        
        $this->arrData['customerkey'] = array('hidCustomerKey');
        $this->arrData['containertypekey'] = array('hidCargoType');

        $this->arrData['consigneename'] = array('consigneeName');
        $this->arrData['placeofdeliverykey'] = array('hidPlaceOfDeliveryKey');
        $this->arrData['placeofreceiptkey'] = array('hidPlaceOfReceiptKey');


        $this->arrData['shipmenttermkey'] = array('selShipmentTerm');
        $this->arrData['shipmentterm2key'] = array('selShipmentTerm2');
        $this->arrData['finaldestinationkey'] = array('hidFinalDestinationKey');
        $this->arrData['connectingvesselkey'] = array('hidConnectingVesselKey');
        $this->arrData['connectingvessel2key'] = array('hidConnectingVessel2Key');
        $this->arrData['connectingvessel3key'] = array('hidConnectingVessel3Key');
        $this->arrData['connectingvesselnumber'] = array('connectingVesselNumber');
        $this->arrData['connectingvessel2number'] = array('connectingVessel2Number');
        $this->arrData['connectingvessel3number'] = array('connectingVessel3Number');
        $this->arrData['connectingvessel3number'] = array('connectingVessel3Number');
        $this->arrData['freighttermkey'] = array('selFreightTerm');
        $this->arrData['freightterm2key'] = array('selFreightTerm2');
        $this->arrData['shipmenttypekey'] = array('selShipmentType');
    
        $this->arrData['servicecontract'] = array('serviceContract');

        $this->arrData['isoverwritenotifyparty'] = array('chkIsOverwriteNotifyParty');
        $this->arrData['notifypartykey'] = array('hidNotifyPartyKey');
        $this->arrData['notifypartyname'] = array('notifyPartyName1');
        $this->arrData['notifypartyaddress'] = array('notifyPartyAddress1');
        $this->arrData['alsonotifyparty'] = array('alsoNotifyParty');

        $this->arrData['qty'] = array('qtyHeader', 'number');
        $this->arrData['unitkey'] = array('selUnitKey');
        $this->arrData['weightqty'] = array('weightQty', 'number');
        $this->arrData['measurement'] = array('measurement', 'number');

        $this->arrDataListAvailableColumn = array();
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code', 'default'=>true, 'width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'name','title' => 'name','dbfield' => 'name', 'default'=>true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'date','title' => 'date','dbfield' => 'trdate','default'=>true, 'width' => 80, 'align' =>'center', 'format' => 'date'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'name','title' => 'name','dbfield' => 'name', 'width' => 180));
        array_push($this->arrDataListAvailableColumn, array('code' => 'warehouse','title' => 'warehouse','dbfield' => 'warehousename', 'width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'containertype','title' => 'type','dbfield' => 'containertype', 'default'=>true,'width' => 60));
        array_push($this->arrDataListAvailableColumn, array('code' => 'etdpol','title' => 'etd','dbfield' => 'etdpol','default'=>true, 'width' => 80,'align' =>'center', 'format' => 'date'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'etapod','title' => 'eta','dbfield' => 'etapod','default'=>true, 'width' => 80,'align' =>'center', 'format' => 'date'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'customer','title' => 'invoiceTo','dbfield' => 'customerinvoicename','width' => 250));

        // kalo export / import beda
        if(EMKL['jobType']['import'] == $this->jobType) {
            array_push($this->arrDataListAvailableColumn, array('code' => 'shipper','title' => 'consignee','dbfield' => 'customername','default'=>true,'width' => 250));
        } else {
            array_push($this->arrDataListAvailableColumn, array('code' => 'shipper', 'title' => 'shipper', 'dbfield' => 'customername', 'default' => true, 'width' => 250));
        }

        array_push($this->arrDataListAvailableColumn, array('code' => 'carrier','title' => 'carrier','dbfield' => 'carriername', 'width' => 200)); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'pod','title' => 'pod','dbfield' => 'podname','default'=>true,'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'pol','title' => 'pol','dbfield' => 'polname','default'=>true,'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'finaldestinationname','title' => 'finalDestination','dbfield' => 'finaldestinationname', 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'jobType','title' => 'jobType','dbfield' => 'jobtypeunion','default'=>true,'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'note','title' => 'note','dbfield' => 'trdesc','width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'salesman','title' => 'salesman','dbfield' => 'salesname','width' => 150));
    	array_push($this->arrDataListAvailableColumn, array('code' => 'mbl','title' => 'mbl','dbfield' => 'mblnumber','width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 80));
        array_push($this->arrDataListAvailableColumn, array('code' => 'createdBy','title' => 'createdBy','dbfield' => 'createdbyname','width' => 150));

        $this->arrSearchColumn = array();
        array_push($this->arrSearchColumn, array('Kode', $this->tableName . '.code'));
        array_push($this->arrSearchColumn, array('Tanggal', $this->tableName . '.trdate'));  
        array_push($this->arrSearchColumn, array('Pelanggan', $this->tableCustomer.'.name'));  
        array_push($this->arrSearchColumn, array(ucwords($this->lang['jobType']), $this->tableJobType. '.name')); 
        array_push($this->arrSearchColumn, array(ucwords($this->lang['jobType']), $this->tableTransportationType. '.name')); 
        array_push($this->arrSearchColumn, array(ucwords($this->lang['jobType']), $this->tableLoadContainer. '.name')); 
        array_push($this->arrSearchColumn, array(ucwords($this->lang['volume']), $this->tableName. '.volume')); 
        array_push($this->arrSearchColumn, array(ucwords($this->lang['unit']), $this->tableVolumeUnit. '.name')); 
        array_push($this->arrSearchColumn, array('Sales', $this->tableEmployee. '.name'));
        array_push($this->arrSearchColumn, array('Catatan', $this->tableName. '.trdesc'));
        array_push($this->arrSearchColumn, array('POL', 'pol.name'));
        array_push($this->arrSearchColumn, array('POD', 'pod.name'));
        array_push($this->arrSearchColumn, array('MBL', $this->tableName.'.mblnumber'));
        array_push($this->arrSearchColumn, array('AJU',  $this->tableName. '.aju'));
        array_push($this->arrSearchColumn, array('PEB',  $this->tableName. '.peb'));
        array_push($this->arrSearchColumn, array('Container',  $this->tableName. '.containernumber'));
        array_push($this->arrSearchColumn, array('Container Type',  $this->tableContainerType. '.name'));
        array_push($this->arrSearchColumn, array('Carrier', 'carrier.name'));  


        $this->includeClassDependencies(array(
            'Port.class.php',
            'Container.class.php',
            'Customer.class.php',
            'Warehouse.class.php',
            'ItemUnit.class.php',
            'Item.class.php', 
            'City.class.php',
            'Consignee.class.php',
            'Vessel.class.php',
            'Terminal.class.php',
            'Supplier.class.php',
            'Service.class.php',
            'Depot.class.php',
            'Consignee.class.php',
            'ServiceCategory.class.php',
            'EMKLJobOrder.class.php' 
        ));

        $this->overwriteConfig();

    }


    function getQuery() {
        $sql = '
            SELECT
              ' . $this->tableName . '.* ,
              ' . $this->tableCustomer . '.name as customername,
              ' . $this->tableCustomer . '.address as customeraddress,
              ' . $this->tableName . '.customercache as customercachename, 
              ' . $this->tableWarehouse . '.name as warehousename,
              ' . $this->tableEmployee . '.name as salesname,
			  ' . $this->tableStatus . '.status as statusname ,
			  ' . $this->tableJobType . '.name as jobtype ,
			  ' . $this->tableTransportationType . '.name as transportationtype,
              ' . $this->tableLoadContainer . '.name as loadcontainertype,
			  ' . $this->tableContainer . '.name as containername ,
			  ' . $this->tableVessel . '.name as vesselname ,
			  feeder_vessel.name as feedervesselname ,
			  ' . $this->tableLocation . '.name as locationname ,
              pol.name as polname,
              pod.name as podname,  
              podelivery.name as placeofdeliveryname,
              poreceipt.name as placeofreceiptname,
              carrier.name as carriername, 
              carrier.alias as carrieraliasname, 
              agent.name as agentname,
              agent.address as agentaddress,
              ' . $this->tableDepot . '.name as depotname,
              ' . $this->tableTerminal . '.name as terminalname,
              ' . $this->tableVolumeUnit . '.name as volumeunit,
             ' . $this->tableContainerType . '.name as containertype,
             ' . $this->tableCity . '.name as finaldestinationname,
             connectingvessel.name as connectingvesselname,
             connectingvessel2.name as connectingvessel2name
			FROM ' . $this->tableStatus . ',
                 ' . $this->tableName . '
                    left join ' . $this->tableEmployee . ' on  ' . $this->tableName . '.saleskey = ' . $this->tableEmployee . '.pkey   
                    left join ' . $this->tableContainer . ' on  ' . $this->tableName . '.itemkey = ' . $this->tableContainer . '.pkey 
                    left join ' . $this->tablePort . ' pol on  ' . $this->tableName . '.polkey = pol.pkey 
                    left join ' . $this->tablePort . ' pod on  ' . $this->tableName . '.podkey = pod.pkey
                    left join ' . $this->tablePort . ' podelivery on  ' . $this->tableName . '.placeofdeliverykey = podelivery.pkey
                    left join ' . $this->tablePort . ' poreceipt on  ' . $this->tableName . '.placeofreceiptkey = poreceipt.pkey
                    left join ' . $this->tableSupplier . ' carrier on  ' . $this->tableName . '.carrierkey = carrier.pkey
                    left join ' . $this->tableVessel . ' on  ' . $this->tableName . '.vesselkey = ' . $this->tableVessel . '.pkey 
                    left join ' . $this->tableVessel . ' feeder_vessel on  ' . $this->tableName . '.feederkey = feeder_vessel.pkey 
                    left join ' . $this->tableCustomer . ' agent on  ' . $this->tableName . '.agentkey = agent.pkey 
                    left join ' . $this->tableDepot . ' on  ' . $this->tableName . '.depotkey = ' . $this->tableDepot . '.pkey 
                    left join ' . $this->tableTerminal . ' on  ' . $this->tableName . '.terminalkey = ' . $this->tableTerminal . '.pkey 
                    left join ' . $this->tableLocation . ' on  ' . $this->tableName . '.locationkey = ' . $this->tableLocation . '.pkey 
                    left join ' . $this->tableCustomer . '  on ' . $this->tableName . '.customerkey = ' . $this->tableCustomer . '.pkey
                    left join ' . $this->tableContainerType . ' on  ' . $this->tableName . '.containertypekey = ' . $this->tableContainerType . '.pkey  
                    left join ' . $this->tableCity . ' on ' . $this->tableName . '.finaldestinationkey = ' . $this->tableCity . '.pkey 
                    left join '. $this->tableVessel .' connectingvessel on '. $this->tableName .'.connectingvesselkey = connectingvessel.pkey
                    left join '. $this->tableVessel .' connectingvessel2 on '. $this->tableName .'.connectingvessel2key = connectingvessel2.pkey, 
                 ' . $this->tableWarehouse . ',  
                 ' . $this->tableJobType . ',
                 ' . $this->tableTransportationType . ',
                 ' . $this->tableLoadContainer . ',
                 ' . $this->tableVolumeUnit . '
			WHERE 
                ' . $this->tableName . '.statuskey = ' . $this->tableStatus . '.pkey and 
                ' . $this->tableName . '.warehousekey = ' . $this->tableWarehouse . '.pkey and  
                ' . $this->tableName . '.jobtypekey = ' . $this->tableJobType . '.pkey and 
                ' . $this->tableName . '.transportationtypekey = ' . $this->tableTransportationType . '.pkey and
                ' . $this->tableName . '.loadcontainertypekey = ' . $this->tableLoadContainer . '.pkey and
                ' . $this->tableName . '.volumetype = ' . $this->tableVolumeUnit . '.pkey';


        if (!empty($this->jobType))
            $sql .= ' and jobtypekey in (' . $this->jobType . ')  ';

        $sql .= $this->criteria;
        $sql .= $this->getWarehouseCriteria();
        $sql .= $this->getCustomerCriteria();
        $sql .= $this->getSalesCriteria();

        return $sql;

    }

    function validateForm($arr, $pkey = '') 
    {

        $arrayToJs = parent::validateForm($arr, $pkey);

        $container = new Container();

        $name = $arr['name'];
        $customerkey = $arr['hidCustomerKey'];
        $warehousekey = $arr['selWarehouseKey'];
        $saleskey = $arr['hidSalesKey'];

        $arrContainerDetailVolumeKey = $arr['selContainerDetailVolumeKey']; 

        $rs = $this->isValueExisted($pkey,'name',$name);

        $rs = $this->searchDataRow(array($this->tableName.'.pkey',$this->tableName.'.name'), ' and ' . $this->tableName . '.pkey <> ' . $this->oDbCon->paramString($pkey) . ' and ' . $this->tableName . '.name = ' . $this->oDbCon->paramString($name) . ' and ' . $this->tableName.'.jobtypekey = '. $this->oDbCon->paramString($this->jobType));

        $this->setLog($rs, true);

        if(empty($name)) {
            $this->addErrorList($arrayToJs,false,$this->errorMsg['name'][1]);
        } else {
            if (count($rs) <> 0) {
                $this->addErrorList($arrayToJs, false, $this->errorMsg['name'][2]);
            }
        }

        if (empty($warehousekey)){  
            $this->addErrorList($arrayToJs,false,$this->errorMsg['warehouse'][1]); 
        }

        if(empty($customerkey)){
            $this->addErrorList($arrayToJs,false, $this->errorMsg['shipper'][1]); 
        }


        if(!empty($arrContainerDetailVolumeKey)) {

            $rsContainer = $container->searchData();
            $rsContainer = $this->reindexDetailCollections($rsContainer,'pkey');

            $arrDetailVolume = array();
            for($i=0; $i<count($arrContainerDetailVolumeKey); $i++) {
                if(in_array($arrContainerDetailVolumeKey[$i], $arrDetailVolume)) {
                    $this->addErrorList($arrayToJs,false, '<strong>'. $rsContainer[$arrContainerDetailVolumeKey[$i]][0]['name'] .'.</strong> ' . $this->errorMsg[215]);
                } else {
                    $arrDetailVolume[] = $arrContainerDetailVolumeKey[$i];
                }
            }

        }

        return $arrayToJs;

    }

    function getDetailVolume($pkey,$criteria=''){
        
            $sql = 'select
	   			'.$this->tableVolumeDetail .'.*,
                '.$this->tableContainer.'.name as itemname,
                '.$this->tableContainer.'.volume,
                '.$this->tableContainer.'.groupvolume
			  from
			  	'. $this->tableContainer .', 
			  	'. $this->tableVolumeDetail .' 
			  where  
                '.$this->tableVolumeDetail .'.itemkey = '.$this->tableContainer .'.pkey and
			  	'.$this->tableVolumeDetail .'.refkey in ('.$this->oDbCon->paramString($pkey,',').')';
         

        $sql .= $criteria;
        
		return $this->oDbCon->doQuery($sql);
    }


    function getDetailCommodity($pkey,$criteria = ''){ 
        
        $sql = 'select
                    '.$this->tableCommodityDetail .'.*, 
                    '.$this->tableCommodity.'.name as commodityname                
                from
                    '.$this->tableCommodityDetail .' 
                    left join ' .$this->tableCommodity.' on   ' .$this->tableCommodityDetail.'.commoditykey = '.$this->tableCommodity.'.pkey
                where 
                    '.$this->tableCommodityDetail .'.refkey in ('.$this->oDbCon->paramString($pkey,',') . ')  '; 
        
            $sql .= $criteria;
            
            return $this->oDbCon->doQuery($sql);
        
    }

     function normalizeParameter($arrParam, $trim=false){

        $vessel = new Vessel();
        $vesselkey = $arrParam['hidVesselKey'];
        $rsVessel = $vessel->getDataRowById($vesselkey);

        $arrParam['flag'] = $rsVessel[0]['flag'];


         if(empty($arrParam['chkIsOverwriteNotifyParty'])) {
            $consignee = new Consignee();
            $rsNotifyParty = $consignee->getDataRowById($arrParam['hidNotifyPartyKey']);

        if(!empty($rsNotifyParty)){

            $arrParam['notifyPartyName1'] = $rsNotifyParty[0]['name'];
            $arrParam['notifyPartyAddress1'] = $rsNotifyParty[0]['address'];
        }else{

            $arrParam['notifyPartyName1'] ='';
            $arrParam['notifyPartyAddress1'] ='';
        }

        }

        $arrParam['selVolumeType'] = ($arrParam['selAirSea'] == EMKL['shipping']['air']) ? EMKL['volume']['kg'] : EMKL['volume']['cbm'];
        $arrParam['selTypeOfJob'] = $this->jobType;

        if ( in_array($arrParam['selContainerType'], 
                      array(EMKL['container']['lcl'],
                            EMKL['container']['lclnc'],
                            EMKL['container']['freightcustomlcl'],
                            EMKL['container']['customlcl'] 
                           ))){
            // kalo LCL 
            $arrParam['hidConsigneeKey'] = 0; 

        } else{
            // kalo FCL 
            $arrParam['hidContainerKey'] = 0; // karena 1 form bisa lebih dr 1 jenis container
            $arrParam['volume'] = 0; 
            $arrParam['weight'] = 0; 
            
            for($i=0;$i<count($arrParam['hidContainerDetailKey']);$i++){ 
                $arrParam['hidSalesDetailKey'][$i] =  0;
                
                if ($arrParam['selAirSea'] == EMKL['shipping']['air'])
                    $arrParam['hidContainerDetailKey'][$i] = 0;
            } 
       
        }

         // kalo bkn fcl & trucking
        if($arrParam['selContainerType'] != EMKL['container']['fcl'] && 
           $arrParam['selContainerType'] != EMKL['container']['trucking'] && 
           $arrParam['selContainerType'] != EMKL['container']['freightcustomfcl'] && 
           $arrParam['selContainerType'] != EMKL['container']['customfcl']
          
          ){
            $arrParam['selContainerDetailVolumeKey'] = array();
            $arrParam['qtyVolume'] = array(); 
        }   

        $arrParam = parent::normalizeParameter($arrParam,true);
        
        return $arrParam;
    }

    

}

?>
