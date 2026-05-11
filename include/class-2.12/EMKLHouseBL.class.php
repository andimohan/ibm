<?php

class EMKLHouseBL extends BaseClass{
	
    function __construct()
    {

        parent::__construct();

        $this->tableName = 'emkl_hbl';
        $this->tableNameDetailContainer = 'emkl_hbl_detail_container';
        $this->tableJobOrderContainer = 'emkl_job_order_detail_container';
        $this->tableItemUnit = 'item_unit';
        $this->tableContainer = 'container';
        $this->tableCustomer = 'customer';
		$this->tablePort = 'port';
		$this->tableJobOrder = 'emkl_job_order_header';
		$this->tableJobOrderDetail = 'emkl_job_order_detail';
        $this->tableConsignee = 'consignee';
        $this->tableStatus = 'transaction_status';
        $this->tableWarehouse = 'warehouse';
        $this->tableVessel = 'vessel';
        $this->tableShipmentTerm = 'shipment_term';
        $this->tableCity = 'city';
        $this->tableSupplier = 'supplier';

        $this->isTransaction = true;
        $this->newLoad = true;

        $this->securityObject = 'EMKLHouseBL'; 

        $this->arrDataDetailContainer = array();
        $this->arrDataDetailContainer['pkey'] = array('hidDetailContainerKey');
        $this->arrDataDetailContainer['refkey'] = array('pkey', 'ref');
//        $this->arrDataDetailContainer['refjoborderkey'] = array('hidJobOrderDetailKey');
        $this->arrDataDetailContainer['refcontainerkey'] = array('selContainerNo', array('mandatory'=>true)); 
        $this->arrDataDetailContainer['containerno'] = array('containerNo');
        $this->arrDataDetailContainer['sealno'] = array('sealNo');



        $arrDetails = array();
        array_push($arrDetails, array('dataset' => $this->arrDataDetailContainer, 'tableName' => $this->tableNameDetailContainer)); 

        $this->arrData = array();
        $this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails));

        $this->arrData['code'] = array('code');
        $this->arrData['trdate'] = array('trDate','date');
        $this->arrData['telexdate'] = array('telexDate','date');
        $this->arrData['refkey'] = array('hidJobOrderKey');
        $this->arrData['refheaderkey'] = array('hidRefHeaderKey');
        $this->arrData['refcode'] = array('refCode');
        $this->arrData['refheadercode'] = array('refHeaderCode');
        $this->arrData['shipperkey'] = array('hidShipperKey');
        $this->arrData['consigneekey'] = array('hidConsigneeKey');
        $this->arrData['carrierkey'] = array('hidCarrierKey');
        $this->arrData['polkey'] = array('hidPOLKey');
        $this->arrData['podkey'] = array('hidPODKey');
        $this->arrData['description'] = array('trDesc');
        $this->arrData['package'] = array('package');
        $this->arrData['weight'] = array('weight', 'number');
        $this->arrData['volume'] = array('volume', 'number');
        $this->arrData['merchant'] = array('merchant');
        $this->arrData['exportreference'] = array('exportReference');
        $this->arrData['note'] = array('note');
        $this->arrData['shortdescription'] = array('shortDesc');
        $this->arrData['marksnumber'] = array('marksNumber');
        $this->arrData['podeliverykey'] = array('hidPODeliveryKey');
        $this->arrData['isrelease'] = array('chkIsRelease');
        $this->arrData['agentkey'] = array('hidAgentKey');
        $this->arrData['freightcharges'] = array('freightCharges');
        $this->arrData['qty'] = array('qty','number');
        $this->arrData['unitkey'] = array('selUnit');
        $this->arrData['isoverwriteshipper'] = array('chkIsOverwriteShipper');
        $this->arrData['isoverwriteconsignee'] = array('chkIsOverwriteConsignee');
        $this->arrData['shippername'] = array('shipperName1');
        $this->arrData['shipperaddress'] = array('shipperAddress1');
        $this->arrData['consigneename'] = array('consigneeName1');
        $this->arrData['consigneeaddress'] = array('consigneeAddress1');
        $this->arrData['saytotalcontainer'] = array('sayTotalContainer');
        $this->arrData['isoverwritecarrier'] = array('chkIsOverwriteCarrier');
        $this->arrData['carriername'] = array('carrierName1');
        $this->arrData['carrieraddress'] = array('carrierAddress1');
        $this->arrData['vesselkey'] = array('hidVesselKey');
        $this->arrData['vesselnumber'] = array('vesselNumber');
        $this->arrData['feederkey'] = array('hidFeederKey');
        $this->arrData['feedernumber'] = array('feederNumber');

        $this->arrData['shipmenttermkey'] = array('selShipmentTermKey');
        $this->arrData['shipmentterm2key'] = array('selShipmentTerm2Key');
        $this->arrData['freighttermkey'] = array('selFreightTermKey');
        $this->arrData['finaldestinationkey'] = array('hidFinalDestinationKey');
        $this->arrData['prepaidat'] = array('prepaidAt');
        $this->arrData['payableat'] = array('payableAt');
        $this->arrData['byinformation'] = array('byInformation');
        $this->arrData['by2information'] = array('by2Information');
        $this->arrData['numberoforiginal'] = array('numberOfOriginal', 'number');
        $this->arrData['poreceiptkey'] = array('hidPOReceiptKey');

        $this->arrData['grossweight'] = array('grossWeight', 'number');
        $this->arrData['netweight'] = array('netWeight', 'number');

        $this->arrData['sumqty'] = array('sumQty', 'number');
        $this->arrData['sumunitkey'] = array('selSumUnit', 'number');
        $this->arrData['sumgrossweight'] = array('sumGrossWeight', 'number');
        $this->arrData['sumnetweight'] = array('sumNetWeight', 'number');
        $this->arrData['summeas'] = array('sumMeas', 'number');
        $this->arrData['sumchargeweight'] = array('sumChargeWeight', 'number');

        $this->arrData['connectingvesselkey'] = array('hidConnectingVesselKey');
        $this->arrData['connectingvessel2key'] = array('hidConnectingVessel2Key');
        //$this->arrData['connectingvessel3key'] = array('hidConnectingVessel3Key');
        $this->arrData['connectingvessel3number'] = array('connectingVessel3Number');
        $this->arrData['connectingvesselnumber'] = array('connectingVesselNumber');
        $this->arrData['connectingvessel2number'] = array('connectingVessel2Number');
        //$this->arrData['connectingvessel3number'] = array('connectingVessel3Number');
        
        $this->arrData['isshowcontainernumber'] = array('chkIsShowContainerNumber');

        $this->arrData['etdpol'] = array('etdPol', 'date');
        $this->arrData['etapod'] = array('etaPod', 'date');

        $this->arrData['connectingcountrykey'] = array('hidConnectingCountryKey');
        $this->arrData['connectingcountry2key'] = array('hidConnectingCountry2Key');
        $this->arrData['connectingcountry3key'] = array('hidConnectingCountry3Key');

        $this->arrData['shipto'] = array('shipTo');
        $this->arrData['servicecontract'] = array('serviceContract');
        $this->arrData['unitofmeaskey'] = array('selUnitOfMeas');

        $this->arrData['transit1date'] = array('transit1Date', 'date');
        $this->arrData['transit2date'] = array('transit2Date', 'date');
        $this->arrData['shippinglinekey'] = array('hidShippingLineKey'); //Sipping Line
        $this->arrData['isoverwritepol'] = array('chkIsOverwritePOL');
        $this->arrData['isoverwritepod'] = array('chkIsOverwritePOD');
        $this->arrData['placeofreceipt'] = array('placeOfReceipt');
        $this->arrData['placeofdelivery'] = array('placeOfDelivery');
        $this->arrData['portofdischarge'] = array('portOfDischarge');
        $this->arrData['portofloading'] = array('portOfLoading');
        
        $this->arrData['isoverwritefinaldestination'] = array('chkIsOverwriteFinalDestination');
        $this->arrData['finaldestination'] = array('finalDestination');
        $this->arrData['alsonotifyparty'] = array('alsoNotifyParty');
        

        $this->arrData['isoverwriteagent'] = array('chkIsOverwriteAgent');
        $this->arrData['agentname'] = array('agentName1');
        $this->arrData['agentaddress'] = array('agentAddress1');
        $this->arrData['mblnumber'] = array('mblNumber');
        $this->arrDataListAvailableColumn = array();
        array_push($this->arrDataListAvailableColumn, array('code' => 'code', 'title' => 'code', 'dbfield' => 'code', 'default' => true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'trdate', 'title' => 'date', 'dbfield' => 'trdate', 'default' => true, 'format' => 'date', 'align' => 'center','width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'jocode', 'title' => 'jobOrderCode', 'dbfield' => 'refcode', 'default' => true,'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'shipper', 'title' => 'shipper', 'dbfield' => 'customername', 'default' => true,'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'release','title' => 'TLX', 'dbfield' => 'isreleaseicon', 'default'=>true, 'width' => 50, 'align' => 'center'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status', 'title' => 'status', 'dbfield' => 'statusname', 'default' => true, 'width' => 90));


        $this->arrSearchColumn = array();
        array_push($this->arrSearchColumn, array('Kode', $this->tableName . '.code'));
//        array_push($this->arrSearchColumn, array('Kode HBL', $this->tableName . '.refhblcode'));
        array_push($this->arrSearchColumn, array('Pelanggan', $this->tableCustomer . '.name'));;
        array_push($this->arrSearchColumn, array('JO Code', $this->tableName . '.refcode'));


        $this->printMenu = array();  
        array_push($this->printMenu,array('code' => 'printTransaction', 'name' => $this->lang['printTransaction'],  'icon' => 'print', 'url' => 'print/emklHouseBL')); 
        //array_push($this->printMenu,array('code' => 'printTransactionAirWay', 'name' => 'Air Way Bill',  'icon' => 'print', 'url' => 'print/airWayBL?showBorder=1&showTitle=1')); 
        


        $this->includeClassDependencies(array(
            'Customer.class.php',
            'City.class.php',
            'ItemUnit.class.php',
            'Consignee.class.php',
            'EMKLJobOrder.class.php',
            'Port.class.php',
            'Warehouse.class.php',
            'Vessel.class.php',  
            'Country.class.php',
            'Supplier.class.php'     
        ));

        $this->overwriteConfig();
    }




    function getQuery()
    {

        $sql = '
                select
                    ' . $this->tableName . '.*,
                    '.$this->tableCustomer.'.name as customername,
                    pod.name as podname,
                    pol.name as polname,
                    podelivery.name as podeliveryname, 
                    poreceipt.name as poreceiptname, 
                    ' . $this->tableVessel . '.name as vesselname,
			        feeder_vessel.name as feedervesselname,
					IF(isrelease = 1, "<i class=\"fas fa-check text-green-avocado\"></i>", "") as isreleaseicon,
                    ' . $this->tableStatus . '.status as statusname,
                    concat(term1.name,\' - \',term2.name) as shipmenttermname,
                    ' . $this->tableCity . '.name as destinationname,
                    shippingline.name as shippinglinename
                from 
                    ' . $this->tableName . '
                        left join '.$this->tableCustomer.'  on '.$this->tableName.'.shipperkey = '.$this->tableCustomer.'.pkey
                        left join '.$this->tablePort.' pod on  '.$this->tableName.'.podkey = pod.pkey
                        left join '.$this->tablePort.' pol on  '.$this->tableName.'.polkey = pol.pkey
                        left join '.$this->tablePort.' podelivery on  '.$this->tableName.'.podeliverykey = podelivery.pkey 
                        left join '.$this->tablePort.' poreceipt on  '.$this->tableName.'.poreceiptkey = poreceipt.pkey 
                        left join '.$this->tableCustomer.' carrier on  '.$this->tableName.'.carrierkey = carrier.pkey
                        left join ' . $this->tableVessel . ' on  ' . $this->tableName . '.vesselkey = ' . $this->tableVessel . '.pkey
                        left join ' . $this->tableShipmentTerm . ' term1 on  ' . $this->tableName . '.shipmenttermkey = term1.pkey 
                        left join ' . $this->tableShipmentTerm . ' term2 on  ' . $this->tableName . '.shipmentterm2key = term2.pkey 
                        left join ' . $this->tableVessel . ' feeder_vessel on  ' . $this->tableName . '.feederkey = feeder_vessel.pkey
                        left join ' . $this->tableCity . ' on  ' . $this->tableName . '.finaldestinationkey = '. $this->tableCity .'.pkey
                        left join ' . $this->tableSupplier . ' shippingline on ' . $this->tableName . '.shippinglinekey = shippingline.pkey,
                    ' . $this->tableStatus . '
                 where 
                     ' . $this->tableName . '.statuskey = ' . $this->tableStatus . '.pkey '
            . $this->criteria;
        


        return $sql;
    }


    function getDetailHBLContainer($pkey, $criteria = '') 
    {

        $pkey = (!is_array($pkey)) ? array($pkey) : $pkey;

        $sql = '
        
            select 
                '. $this->tableNameDetailContainer .'.*,
                '. $this->tableName .'.code as hblcode,
                '. $this->tableJobOrderContainer .'.refkey as jokey,
                '. $this->tableJobOrderContainer .'.qty,
                '. $this->tableJobOrderContainer .'.unitkey,
                '. $this->tableJobOrderContainer .'.deliveryaddress,
                '. $this->tableJobOrderContainer .'.grossweight,
                '. $this->tableJobOrderContainer .'.chargeweight,
                '. $this->tableJobOrderContainer .'.meas,
                '. $this->tableJobOrderContainer .'.typekey,
                '. $this->tableJobOrderContainer .'.netweight,
                '. $this->tableItemUnit . '.name as unitname,
                '. $this->tableContainer . '.name as containername
            from
                '. $this->tableNameDetailContainer .'
                    left join '. $this->tableJobOrderContainer .' on '. $this->tableNameDetailContainer .'.refcontainerkey = '. $this->tableJobOrderContainer .'.pkey
                    left join ' . $this->tableContainer . ' on ' . $this->tableJobOrderContainer . '.typekey = ' . $this->tableContainer . '.pkey
                    left join ' . $this->tableItemUnit . ' on ' . $this->tableJobOrderContainer . '.unitkey =  ' . $this->tableItemUnit . '.pkey,
                '. $this->tableName .'
            where
                '. $this->tableNameDetailContainer .'.refkey = '. $this->tableName .'.pkey and
                '. $this->tableNameDetailContainer .'.refkey in ('. $this->oDbCon->paramString($pkey,',') .')
                order by ' . $this->tableNameDetailContainer . '.pkey asc 
        ';

        if (!empty($criteria)) {
            $sql .= $criteria;
        }

        $result = $this->oDbCon->doQuery($sql);
       
        return $result;
    }
	
    function afterUpdateData($arrParam,$action){
		$pkey = $arrParam['pkey'];
		$rsHeader = $this->getDataRowById($pkey);

		if(!isset($arrParam['_mnv'])){ 
			$this->updateDetailHBL($rsHeader);
 
            $emklJobOrder = new EMKLJobOrder();
            $emklJobOrder->syncHBLAndJobOrder($pkey, false);
		}
		
    }

    function updateDetailHBL($rsHeader){
		$pkey = $rsHeader[0]['pkey'];
         
         $sql = 'update ' .$this->tableJobOrderDetail.' set
		 		hbl = '.$this->oDbCon->paramString($rsHeader[0]['code']).',
				hblkey = '.$this->oDbCon->paramString($pkey).' 
				where pkey = ' . $this->oDbCon->paramString($rsHeader[0]['refkey']);
		
          $this->oDbCon->execute($sql);
		

		// kalo HBL nya gandi JO, JO yg lama harus direset hblkey nya
		$sql = 'update ' .$this->tableJobOrderDetail.' set hbl = \'\', hblkey = 0
				where pkey <> ' . $this->oDbCon->paramString($rsHeader[0]['refkey']) . ' and ' .$this->tableJobOrderDetail.'.hblkey = ' .  $this->oDbCon->paramString($pkey);

		$this->oDbCon->execute($sql); 


		// perlu update HBL yg lain jg. karena 1 detail JO cuma boleh punya 1 HBL
		// tambahkan di validasi saja
//		$sql = 'update '.$this->tableName.' set refkey = 0, refheaderkey = 0,refcode = \'\', refheadercode = \'\'
//				where '.$this->tableName.'.pkey <> '.$this->oDbCon->paramString($rsHeader[0]['pkey']).' and '.$this->tableName.' .refkey = '.$this->oDbCon->paramString($rsHeader[0]['refkey']);
//		
//         $this->oDbCon->execute($sql);
//        
    }
    
    function validateForm($arr, $pkey = '') {
        $arrayToJs = parent::validateForm($arr, $pkey);

        $jokey = $arr['hidJobOrderKey']; 
        
        if(isset($arr['_mnv'])) return;
        
        $rsHBL = $this->searchDataRow(array($this->tableName.'.refcode'), ' 
                                    and '.$this->tableName.'.pkey <> '.$this->oDbCon->paramString($pkey).'
                                    and '.$this->tableName.'.refkey = '.$this->oDbCon->paramString($jokey).' 
                                    and '.$this->tableName.'.statuskey in(1,2,3)');
        
        if(!empty($rsHBL))
            $this->addErrorList($arrayToJs,false, $rsHBL[0]['refcode'].'. '.$this->errorMsg['hbl'][3]); 	  
        
        if(empty($jokey)) 
            $this->addErrorList($arrayToJs,false,$this->errorMsg['jobOrder'][1]);
		 
        if($this->checkIfJobOrderManually($jokey))
            $this->addErrorList($arrayToJs,false,$this->errorMsg['emklHouseBL'][9]);
        
        return $arrayToJs;
    }
	
	function checkIfJobOrderManually($jokey){
            
        $emklJobOrder = new EMKLJobOrder();  
        $rsDetailJO = $emklJobOrder->getDetailByColumn($emklJobOrder->tableNameDetail.'.pkey',$jokey);
        
        return ($rsDetailJO[0]['ismanual'] == 1) ? true : false;

        
    }


    function validateConfirm($rsHeader){  
        
        $emklJobOrder = new EMKLJobOrder();
        
        $id = $rsHeader[0]['pkey'];
 
        // validasi kalo ad informasi contaimer detailnya
        $rsDetail = $this->getDetailHBLContainer($id); 
        
        $rsJobOrderContainer = $emklJobOrder->getDetailContainer($rsHeader[0]['refheaderkey']);
        $JOContainerDetailKey=array_column($rsJobOrderContainer, 'pkey');
        
          
        $arrErrMsg = array();
        for($i=0; $i<count($rsDetail); $i++) { 
            if (!in_array($rsDetail[$i]['refcontainerkey'], $JOContainerDetailKey))  
                array_push($arrErrMsg,  '<strong>'. $rsDetail[$i]['containerno'] .'. </strong>' . $this->errorMsg['hbl'][4]); 
        }
        
        if(!empty($arrErrMsg)) 
            $this->addErrorLog(false, $this->errorMsg[212] . '<br>' . implode('<br>', $arrErrMsg)); 
           
        
    }

    function confirmTrans($rsHeader){ 
        $id = $rsHeader[0]['pkey'];

      
    }
 
    function validateCancel($rsHeader, $autoChangeStatus = false) {
        $id = $rsHeader[0]['pkey'];

		// jika SO sudah closed, gk boleh cancel 
		$sql = 'select '.$this->tableJobOrder.'.pkey,  '.$this->tableJobOrder.'.code
				from '.$this->tableJobOrder.','.$this->tableJobOrderDetail.'
				where 
					'.$this->tableJobOrder.'.pkey =  '.$this->tableJobOrderDetail.'.refkey and
					'.$this->tableJobOrder.'.statuskey = 3 and
					'.$this->tableJobOrderDetail.'.hblkey  = '.$this->oDbCon->paramString($id);
   
		$rs = $this->oDbCon->doQuery($sql); 
		if (!empty($rs))
            $this->addErrorLog(false, '<strong>' . $rs[0]['code'] . '</strong>. ' . $this->errorMsg[220]);
		
    }

    function cancelTrans($rsHeader, $copy)  {
        $id = $rsHeader[0]['pkey'];
  
		$sql = 'update '.$this->tableJobOrderDetail.' set hbl = \'\', hblkey = 0 where hblkey = ' .$this->oDbCon->paramString($id);
		$this->oDbCon->execute($sql);
		
        if ($copy)
            $this->copyDataOnCancel($id);

    }

 function normalizeParameter($arrParam, $trim = false)  {
              
        
        if(!empty($arrParam['hidJobOrderKey'])){
        	$emklJobOrder = new EMKLJobOrder();
            $rsDetailJO = $emklJobOrder->getDetailByColumn($emklJobOrder->tableNameDetail.'.pkey',$arrParam['hidJobOrderKey'],true); 
            $rsJO = $emklJobOrder->getDataRowById($rsDetailJO[0]['refkey']);
            $arrParam['hidRefHeaderKey'] = $rsDetailJO[0]['refkey'];
            $arrParam['refCode'] = $rsDetailJO[0]['code'];
            $arrParam['refHeaderCode'] = $rsJO[0]['code'];
        }
     
	 	//$this->setLog($arrParam,true);
	 
        if(empty($arrParam['chkIsOverwriteShipper'])){
        	$customer = new Customer();
            $rsCustomer = $customer->getDataRowById($arrParam['hidShipperKey']);
            $arrParam['shipperName1'] = $rsCustomer[0]['name'];
            $arrParam['shipperAddress1'] = $rsCustomer[0]['address'];
            
        }
     
        if(empty($arrParam['chkIsOverwriteConsignee'])){
        	$consignee = new Consignee();
            $rsConsignee = $consignee->getDataRowById($arrParam['hidConsigneeKey']);
            $arrParam['consigneeName1'] = $rsConsignee[0]['name'];
            $arrParam['consigneeAddress1'] = $rsConsignee[0]['address'];
            
        }

        if(empty($arrParam['chkIsOverwriteCarrier'])) {
            $consignee = new Consignee();
            $rsCarrier = $consignee->getDataRowById($arrParam['hidCarrierKey']);
            $arrParam['carrierName1'] = $rsCarrier[0]['name'];
            $arrParam['carrierAddress1'] = $rsCarrier[0]['address'];
        }

        if(empty($arrParam['chkIsOverwriteAgent'])) {
            $customer = new Customer();
            $rsAgent = $customer->getDataRowById($arrParam['hidAgentKey']);
            $arrParam['agentName1'] = $rsAgent[0]['name'];
            $arrParam['agentAddress1'] = $rsAgent[0]['address'];
        }


        $arrContainerKey = $arrParam['selContainerNo'];
        if(!empty($arrContainerKey)) {
            $emklJobOrder = new EMKLJobOrder();
            
            $rsContainer = $emklJobOrder->getDetailContainer($arrParam['hidRefHeaderKey'], ' and ' . $emklJobOrder->tableContainerDetail.'.pkey in ('. $this->oDbCon->paramString($arrContainerKey,',') .')');
            $rsContainerCols = $this->reindexDetailCollections($rsContainer,'pkey');
            
            for($i=0; $i<count($arrContainerKey); $i++) {
                $rsContainerCol = $rsContainerCols[$arrContainerKey[$i]];
                $arrParam['sealNo'][$i] = $rsContainerCol[0]['sealno'];
                $arrParam['containerNo'][$i] = $rsContainerCol[0]['containerno'];
//                $arrParam['hidJobOrderDetailKey'][$i] = $rsContainerCol[0]['refkey']; // gk relate
            }

        }
        $arrParam = parent::normalizeParameter($arrParam, true);
        
        return $arrParam;
    }

}
