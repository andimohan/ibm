<?php

class ItemReceiving extends BaseClass
{

    function __construct()
    {
        parent::__construct();

        $this->tableName = 'item_receiving_header';
        $this->tableNameDetail = 'item_receiving_detail';
        $this->tableCustomer = 'customer';
        $this->tableSupplier = 'supplier';
        $this->tableWarehouse = 'warehouse';
        $this->tableWarehouseLayout = 'warehouse_layout';
        $this->tableStatus = 'transaction_status';
        $this->tableBrand = 'brand';
        $this->tableItemCategory = 'item_category';  
        $this->tableCountry = 'country';  
        $this->tableItemReceivingPlanHeader = 'item_receiving_plan_header';  

        $this->isTransaction = true;
        $this->securityObject = 'ItemReceiving';

        $this->uploadFileFolder = 'item-receiving/';
        $this->useStorage  =  array(); //$this->useStorage('S3');

        $this->arrDetail = array();
        $this->arrDetail['pkey'] = array('hidDetailKey');
        $this->arrDetail['refkey'] = array('pkey', 'ref');
        $this->arrDetail['itembarcode'] = array('itemDetailBarcode');
        $this->arrDetail['itemcode'] = array('itemDetailCode');
        $this->arrDetail['itemname'] = array('itemDetailName');
        $this->arrDetail['hs'] = array('hs');
        $this->arrDetail['countrykey'] = array('hidDetailCountryKey');
        $this->arrDetail['countryoforiginid'] = array('countryOfOriginId');
        $this->arrDetail['itemcategory'] = array('itemCategoryName');
        $this->arrDetail['packaging'] = array('packagingName');
        $this->arrDetail['facility'] = array('facility');
        $this->arrDetail['orderlist'] = array('orderList');
        $this->arrDetail['qty'] = array('qty', 'number');
        $this->arrDetail['unit'] = array('itemUnit');
        $this->arrDetail['category'] = array('category');
        $this->arrDetail['alcoholcontent'] = array('alcoholContent', 'number');
        $this->arrDetail['mililiter'] = array('mililiter', 'number');
        $this->arrDetail['qtycarton'] = array('qtyCarton', 'number');
        $this->arrDetail['qtypackage'] = array('qtyPackage', 'number');
        $this->arrDetail['amount'] = array('amount', 'number');
        $this->arrDetail['brandkey'] = array('hidDetailBrandKey');
        $this->arrDetail['typekey'] = array('hidDetailTypeKey');
        $this->arrDetail['label'] = array('label');
        $this->arrDetail['transactiontypekey'] = array('selTransactionType');
        $this->arrDetail['containernumber'] = array('containerNumber');
        $this->arrDetail['containertype'] = array('containerType');
        $this->arrDetail['containersize'] = array('containerSize');
        $this->arrDetail['containerkind'] = array('containerKind');

        $arrDetails = array();
        array_push($arrDetails, array('dataset' => $this->arrDetail, 'tableName' => $this->tableNameDetail));

        $this->arrData = array();
        $this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails));
        $this->arrData['code'] = array('code');
        $this->arrData['trdate'] = array('trDate', 'date');
        $this->arrData['refkey'] = array('hidItemReceivingPlanKey');
        $this->arrData['warehousekey'] = array('selWarehouseKey');
        $this->arrData['warehouselayoutkey'] = array('selWarehouseLayoutKey');
        $this->arrData['customerkey'] = array('hidCustomerKey');
        $this->arrData['supplierkey'] = array('hidSupplierKey');
        $this->arrData['shipperkey'] = array('hidShipperKey');
        $this->arrData['receiveddate'] = array('trReceivedDate', 'date');
        $this->arrData['documenttype'] = array('selDocumentType');
        $this->arrData['submissionnumber'] = array('submissionNumber');
        $this->arrData['submissiondate'] = array('submissionDate', 'date');
        $this->arrData['invoicenumber'] = array('invoiceNumber');
        $this->arrData['invoicedate'] = array('invoiceDate', 'date');
        $this->arrData['blnumber'] = array('blNumber');
        $this->arrData['bldate'] = array('blDate', 'date');
        $this->arrData['registrationnumber'] = array('registrationNumber');
        $this->arrData['registrationdate'] = array('registrationDate', 'date');
        $this->arrData['currencykey'] = array('selCurrency');
        $this->arrData['valuetype'] = array('valueType');
        $this->arrData['trdesc'] = array('trDesc');
        $this->arrData['statuskey'] = array('selStatus');

        $this->importUrl = 'import/itemReceiving';

        $this->arrData['file'] = array('item-file-uploader', array('datatype' => 'file', 'uploadFolder' => $this->uploadFileFolder,  'token' => 'token-item-file-uploader', 'fileName' => 'item-file-uploader'));

        $this->arrDataListAvailableColumn = array();
        array_push($this->arrDataListAvailableColumn, array('code' => 'code', 'title' => 'code', 'dbfield' => 'code', 'default' => true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'trdate', 'title' => 'date', 'dbfield' => 'trdate', 'align' => 'center', 'format' => 'date', 'default' => true, 'width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'refCode', 'title' => 'refCode', 'dbfield' => 'itemreceivingplanheadercode', 'default' => true, 'width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'warehouse', 'title' => 'warehouse', 'dbfield' => 'warehousename', 'default' => true, 'width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'warehouselayout', 'title' => 'warehouseLayout', 'dbfield' => 'warehouselayoutname', 'default' => true, 'width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'customer', 'title' => 'customer', 'dbfield' => 'customername', 'default' => true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'supplier', 'title' => 'supplier', 'dbfield' => 'suppliername', 'default' => true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'shipper', 'title' => 'shipper', 'dbfield' => 'shippername', 'default' => true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status', 'title' => 'status', 'dbfield' => 'statusname', 'default' => true, 'width' => 70));


        $this->arrSearchColumn = array();
        array_push($this->arrSearchColumn, array('Kode', $this->tableName . '.code'));
        array_push($this->arrSearchColumn, array('No. Receiving Plan', $this->tableItemReceivingPlanHeader . '.code'));
        array_push($this->arrSearchColumn, array('Gudang', $this->tableWarehouse . '.name'));
        array_push($this->arrSearchColumn, array('Tata Letak Gudang', $this->tableWarehouseLayout . '.name'));
        array_push($this->arrSearchColumn, array('Tgl', $this->tableName . '.trdate'));
        array_push($this->arrSearchColumn, array('Pelanggan', $this->tableCustomer . '.name'));
        array_push($this->arrSearchColumn, array('Pemasok', $this->tableSupplier . '.name'));
        array_push($this->arrSearchColumn, array('Pengirim', 'shipper.name'));
        array_push($this->arrSearchColumn, array('Status', $this->tableStatus . '.status'));


        $this->includeClassDependencies(
            array(
                'Customer.class.php',
                'Supplier.class.php',
                'Currency.class.php',
                'Warehouse.class.php',
                'ItemCategory.class.php',
                'Item.class.php',
                'ItemMovement.class.php',
                'WarehouseLayout.class.php',
                'TransactionType.class.php',
                'DocumentType.class.php',
                'ItemUnit.class.php',
                'PutAway.class.php',
                'ItemReceivingPlan.class.php'
            )
        );

        $this->overwriteConfig();
    }


    function getQuery()
    {

        return '
				select
					' . $this->tableName . '.*,
					' . $this->tableCustomer . '.name as customername,
                    ' . $this->tableSupplier . '.name as suppliername,
                    shipper.name as shippername,
                    ' . $this->tableWarehouse . '.name as warehousename,
					' . $this->tableStatus . '.status as statusname,
                    ' . $this->tableWarehouseLayout . '.name as warehouselayoutname,
                    ' . $this->tableItemReceivingPlanHeader . '.code as itemreceivingplanheadercode
				from 
					' . $this->tableName . '
                           left join ' . $this->tableItemReceivingPlanHeader . ' on ' . $this->tableName . '.refkey = ' . $this->tableItemReceivingPlanHeader . '.pkey
                           left join ' . $this->tableCustomer . '  on ' . $this->tableName . '.customerkey = ' . $this->tableCustomer . '.pkey
                           left join ' . $this->tableSupplier . '  on ' . $this->tableName . '.supplierkey = ' . $this->tableSupplier . '.pkey
                           left join ' . $this->tableSupplier . ' shipper  on ' . $this->tableName . '.shipperkey = shipper.pkey
                           left join ' . $this->tableWarehouseLayout . ' on ' . $this->tableName . '.warehouselayoutkey = ' . $this->tableWarehouseLayout . '.pkey,
					' . $this->tableStatus . ',
                    ' . $this->tableWarehouse . '
				where  		
                    ' . $this->tableName . '.warehousekey = ' . $this->tableWarehouse . '.pkey and
					' . $this->tableName . '.statuskey = ' . $this->tableStatus . '.pkey  
 		' . $this->criteria;
    }

    function validateForm($arr, $pkey = '')
    {
        $arrayToJs = parent::validateForm($arr, $pkey);

        $warehouseLayout = new WarehouseLayout();

        $customerkey = $arr['hidCustomerKey'];
        $shipperkey = $arr['hidShipperKey'];
        $supplierkey = $arr['hidSupplierKey'];
        $warehousekey = $arr['selWarehouseKey'];
        $warehouseLayoutkey = $arr['selWarehouseLayoutKey'];

        $arrItemCode = $arr['itemDetailCode'];
        $arrItemName = $arr['itemDetailName'];
        
        if (empty($customerkey)) {
            $this->addErrorList($arrayToJs, false, $this->errorMsg['customer'][1]);
        }

        if (empty($shipperkey)) {
            $this->addErrorList($arrayToJs, false, $this->errorMsg['shipper'][1]);
        }

        if (empty($supplierkey)) {
            $this->addErrorList($arrayToJs, false, $this->errorMsg['supplier'][1]);
        }
        $this->setLog('testing', true);

        // if(empty($warehouseLayoutkey)){
        //     // $this->addErrorList($arrayToJs, false, $this->errorMsg['warehouseLayout'][1]);
        // } else {
        //     //cek apakah warehouselayout ada di warehouse
        //     $rsWarehouseLayout = $warehouseLayout->getDataByWarehouse($warehousekey, ' and ' . $warehouseLayout->tableName . '.pkey = ' . $this->oDbCon->paramString($warehouseLayoutkey));
        //     $this->setLog($rsWarehouseLayout, true);
        //     if (empty($rsWarehouseLayout)) {
        //         $this->addErrorList($arrayToJs, false, $this->errorMsg['warehouseLayout'][2]);
        //     }
        // }

        if (empty($arrItemName[0])) {
            $this->addErrorList($arrayToJs, false, $this->errorMsg[501]);
        } else {
            for ($i=0; $i < count($arrItemName); $i++) {
                if (empty($arrItemName[$i])) {
                    $this->addErrorList($arrayToJs, false, $this->errorMsg['item'][1]);
                }
            }
        }
        $this->setLog($arrayToJs, true);
        $this->setLog('testing', true);



        return $arrayToJs;
    }


    function getDetailWithRelatedInformation($pkey, $criteria = '')
    {
        $sql = 'select
	   			' . $this->tableNameDetail . '.*,
                '.$this->tableBrand.'.name as brandname,
                '.$this->tableItemCategory.'.name as typename,
                '.$this->tableCountry.'.name as countryname
			  from
			  	' . $this->tableNameDetail . '
                left join  '.$this->tableBrand.' on '. $this->tableNameDetail .'.brandkey = '.$this->tableBrand.'.pkey
                left join  '.$this->tableItemCategory.' on '. $this->tableNameDetail .'.typekey = '.$this->tableItemCategory.'.pkey
                left join  '.$this->tableCountry.' on '. $this->tableNameDetail .'.countrykey = '.$this->tableCountry.'.pkey
			  where
			  	' . $this->tableNameDetail . '.refkey in (' . $this->oDbCon->paramString($pkey, ',') . ') ';

        $sql .= $criteria;

        return $this->oDbCon->doQuery($sql);
    }

    function validateConfirm($rsHeader){

        $item = new Item();
        $itemReceivingPlan = new ItemReceivingPlan();

        $id = $rsHeader[0]['pkey'];

        $arrErrMsg = array();

        if(!empty($rsHeader[0]['refkey'])) {
            $rsItemReceivingPlan = $itemReceivingPlan->getDataRowById($rsHeader[0]['refkey']);

            if(empty($rsItemReceivingPlan)) {
                $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '. $this->errorMsg[201].' '.$this->errorMsg['itemReceiving'][1]);
            } else {

                if(in_array($rsItemReceivingPlan[0]['statuskey'], [1,4])) {
                    array_push($arrErrMsg, $this->lang['itemReceivingPlan'].' '.$this->errorMsg[228]);
                }

                if($rsHeader[0]['warehousekey'] <> $rsItemReceivingPlan[0]['warehousekey']) {
                    array_push($arrErrMsg, '<strong>'.$rsItemReceivingPlan[0]['code'].' - </strong>'.$this->lang['warehouse'].' '.$this->errorMsg[905]);
                }

                if($rsHeader[0]['warehouselayoutkey'] <> $rsItemReceivingPlan[0]['warehouselayoutkey']) {
                    array_push($arrErrMsg, '<strong>'.$rsItemReceivingPlan[0]['code'].' - </strong>'.$this->lang['warehouseLayout'].' '.$this->errorMsg[905]);
                }

                if($rsHeader[0]['customerkey'] <> $rsItemReceivingPlan[0]['customerkey']) {
                    array_push($arrErrMsg, '<strong>'.$rsItemReceivingPlan[0]['code'].' - </strong>'.$this->errorMsg['customer'][3]);
                }

                if($rsHeader[0]['supplierkey'] <> $rsItemReceivingPlan[0]['supplierkey']) {
                    array_push($arrErrMsg, '<strong>'.$rsItemReceivingPlan[0]['code'].' - </strong>'.$this->errorMsg['supplier'][3]);
                }

                if($rsHeader[0]['shipperkey'] <> $rsItemReceivingPlan[0]['shipperkey']) {
                    array_push($arrErrMsg, '<strong>'.$rsItemReceivingPlan[0]['code'].' - </strong>'.$this->errorMsg['shipper'][3]);
                }
                
            }
        }
        
    
        //cek apakah ada kode barang yang sama, kalau ada tidak boleh
        $rsDetail = $this->getDetailWithRelatedInformation($id);
        $rsItem = $item->searchData('','',true);
        
        $rsItemCodes = array_column($rsItem, 'itemcode');

        for($i=0; $i<count($rsDetail); $i++){
            
            $detailItemCode = $rsDetail[$i]['itemcode'];

            if(empty($detailItemCode)) continue;

            if(in_array($detailItemCode, $rsItemCodes)){
                array_push($arrErrMsg, '<strong>'.$detailItemCode.'.</strong> '.$this->errorMsg['itemReceiving'][1]);
            }
        }

        if(!empty($arrErrMsg)){
            $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '. $this->errorMsg[201] .'<br>'.implode('<br>', $arrErrMsg));
        }
		  
	}


    function confirmTrans($rsHeader)
    {
        $id = $rsHeader[0]['pkey'];
        
		$itemMovement = new ItemMovement();  

        $this->addItem($rsHeader);
        $this->addItemMovement($rsHeader);
    }

    function validateCancel($rsHeader,$autoChangeStatus=false){  
		
	 }
    
     function cancelTrans($rsHeader,$copy){  
         
        $pkey = $rsHeader[0]['pkey'];

        // $this->cancelItem($rsHeader);
        $this->cancelItemMovement($rsHeader);
		
		if ($copy){ 			
			$this->copyDataOnCancel($pkey);
        }
	}    

    function addItem($rsHeader)
    {
        $item = new Item();
        $itemCategory = new ItemCategory();

        $id = $rsHeader[0]['pkey'];

        $rsDetail = $this->getDetailWithRelatedInformation($id);

        if(empty($rsDetail)) return;

        // $rsItemCategory = $itemCategory->searchDataRow(array(
        //     $itemCategory->tableName.'.pkey',
        //     $itemCategory->tableName.'.code',
        //     'lower('.$itemCategory->tableName.'.name) as name',
        //     $itemCategory->tableName.'.statuskey'
        // ), 'and ' . $itemCategory->tableName . '.statuskey = 1');
        // $rsItemCategoryCol = array_column($rsItemCategory,null,'name');
            
    
        for($i=0; $i<count($rsDetail); $i++){

            if(empty($rsDetail[$i]['itemname'])) continue;

            // $rsItem = $item->searchDataRow(array($item->tableName.'.pkey',$item->tableName.'.code',
            // 'lower('.$item->tableName.'.name) as name',
            // $item->tableName.'.statuskey'), ' and ' . $item->tableName . '.code = ' . $this->oDbCon->paramString($rsDetail[$i]['itemcode']));
            $rsItem = $item->cekDuplicateData($rsDetail[$i]['itemname'], $rsDetail[$i]['brandkey'], $rsDetail[$i]['mililiter'], $rsDetail[$i]['typekey'], $rsDetail[$i]['alcoholcontent']);

            if (!empty($rsItem)) {

                $sql = 'update '.$this->tableNameDetail.' set itemkey = '.$this->oDbCon->paramString($rsItem[0]['pkey']).' where pkey  = '.$this->oDbCon->paramString($rsDetail[$i]['pkey']);
                $this->oDbCon->doQuery($sql);
                
                continue;
            }
            
            // $rsCategoryItem = $rsItemCategoryCol[strtolower($rsDetail[$i]['typename'])];
            // if (empty($rsCategoryItem)) {
            //     $arrParam = array();            
            //     $arrParam['code'] = 'xxxx';
            //     $arrParam['name']  = $rsDetail[$i]['itemcategory'];
            //     $arrParam['selCategory'] = 0;

            //     $arrayToJs = $itemCategory->addData($arrParam);
            // }

    
            $arrParam = array();            
            $arrParam['code'] = $rsDetail[$i]['itemcode'];
            $arrParam['name']  = $rsDetail[$i]['itemname'];
            $arrParam['itemCode'] = $rsDetail[$i]['itemcode'];
            $arrParam['barcode'] = $rsDetail[$i]['itembarcode'];
            $arrParam['mililiter'] = $rsDetail[$i]['mililiter'];
            $arrParam['hidBrandKey'] = $rsDetail[$i]['brandkey'];
            $arrParam['hidCategoryKey'] = $rsDetail[$i]['typekey'];
            $arrParam['alcoholContent'] = $rsDetail[$i]['alcoholcontent'];

            $arrayToJs = $item->addData($arrParam);
            
            if(!$arrayToJs[0]['valid']) {
                throw new Exception('<strong>' . $rsHeader[0]['code'] . '</strong>. ' . $this->errorMsg[201] . ' ' . $arrayToJs[0]['message']);
            } else {
                $itemKey =  $arrayToJs[0]['data']['pkey'];
                $sql = 'update '.$this->tableNameDetail.' set itemkey = '.$this->oDbCon->paramString($itemKey).' where pkey  = '.$this->oDbCon->paramString($rsDetail[$i]['pkey']);
                $this->oDbCon->doQuery($sql);
                
            }

        }

    }

    function cancelItem($rsHeader)
    {
        $id = $rsHeader[0]['pkey'];

        $rsDetail = $this->getDetailWithRelatedInformation($id);

        if(empty($rsDetail)) return;

        $arrItemKey = array_column($rsDetail,'itemkey');

        if(empty($arrItemKey)) return;

        $item = new Item();

        // $sql  = '
        //     delete from '.$item->tableName.' where '.$item->tableName.'.pkey in ('. $this->oDbCon->paramString($arrItemKey,',') .')
        // ';

        $sql = 'update 
                            ' . $item->tableName.' 
                        set  
                            statuskey = 2
                        where 
                            pkey in ('. $this->oDbCon->paramString($arrItemKey,',') .')';
        $this->oDbCon->execute($sql);
    }

    function addItemMovement($rsHeader)
    {
        $itemMovement = new ItemMovement();
        
        $id = $rsHeader[0]['pkey'];
        
        $rsData = $this->searchData('','',true, '  and ' . $this->tableName.'.pkey = '.$this->oDbCon->paramString($id));
        
        $rsDetail = $this->getDetailWithRelatedInformation($id);
        $note = $rsHeader[0]['code'] . '. ' . ucfirst($this->lang['itemReceiving']) . ' ' . $this->lang['from'] . ' ' . $rsData[0]['suppliername'];
        for($i=0; $i<count($rsDetail); $i++) {
            $itemMovement->updateItemMovement($id,$rsDetail[$i]['itemkey'],$rsDetail[$i]['qty'],0,$this->tableName, array('warehousekey' => $rsHeader[0]['warehousekey'], 'warehouselayoutkey' => $rsHeader[0]['warehouselayoutkey']), $note,$rsHeader[0]['trdate']);
        }
    }

    function cancelItemMovement($rsHeader)
    {
        $itemMovement = new ItemMovement();

        $id = $rsHeader[0]['pkey'];

        $itemMovement->cancelMovement($id,$this->tableName);
    }

    function getDataForPutAway($pkey, $criteria = ''){

        $itemMovement = new ItemMovement();

        $sql = '
            select
                '.$this->tableName.'.code,
                '.$this->tableName.'.trdate,
                '.$this->tableName.'.warehousekey,
                '.$this->tableName.'.warehouselayoutkey,
                '.$this->tableName.'.customerkey,
                '.$this->tableName.'.supplierkey,
                '.$this->tableName.'.submissionnumber,
                '.$this->tableNameDetail.'.*
            from
                '.$this->tableNameDetail.',
                '.$this->tableName.'
            where
                '.$this->tableNameDetail.'.refkey = '.$this->tableName.'.pkey and
                '.$this->tableName.'.pkey = '.$this->oDbCon->paramString($pkey).' and
                '.$this->tableName.'.statuskey = 2 and '.$this->tableNameDetail.'.putawayqty < '.$this->tableNameDetail.'.qty
        ';

        if(!empty($criteria)){
            $sql .= ' ' . $criteria;
        }
        $this->setLog($sql, true);
        
        $rs = $this->oDbCon->doQuery($sql);
        $this->setLog($rs, true);

        if(empty($rs)) return;

        $arrWarehouse = array_column($rs,'warehousekey');
        $arrWarehouseLayout = array_column($rs,'warehouselayoutkey');   

        $arrResult = array();   
        foreach($rs as $row)
        {
            $rsItemQOH = $itemMovement->getItemQOH($row['itemkey'], $arrWarehouse, $arrWarehouseLayout);

            $row['qtyinbaseunit'] = $rsItemQOH;

            $arrResult[] = $row;
        }

        return $arrResult;
    }

    function normalizeParameter($arrParam, $trim = false)
    {
        $arrParam = parent::normalizeParameter($arrParam, true);
        $this->setLog($arrParam, true);

        return $arrParam;
    }

    function updateQtyPutAway($pkey)
    {

        $rsDetail = $this->getDetailById($pkey);

        $putAway = new PutAway();
        
        for($i=0; $i<count($rsDetail); $i++){
            $sql = 'select 
                        coalesce(sum(qty),0) as totalputawayqty
                    from 
                        '. $putAway->tableName . ', '. $putAway->tableNameDetail . '
                    where 
                         '. $putAway->tableName . '.pkey = '. $putAway->tableNameDetail . '.refkey and
                         '. $putAway->tableName . '.refkey = '. $putAway->oDbCon->paramString($pkey) .' and
                         '. $putAway->tableNameDetail . '.itemkey = ' . $rsDetail[$i]['itemkey'] .' and 
                         '. $putAway->tableNameDetail . '.itemreceivingdetailkey = ' . $rsDetail[$i]['pkey'] .' and 
                         (statuskey = 2 or statuskey = 3)';
 
                $rsTotal = $this->oDbCon->doQuery($sql);


            $sql = 'update 
                            ' . $this->tableNameDetail.' 
                        set  
                            putawayqty = '. $rsTotal[0]['totalputawayqty'] .'
                        where 
                            refkey = '.$pkey.' and 
                            pkey = '.$rsDetail[$i]['pkey'].' and 
                            itemkey = ' . $rsDetail[$i]['itemkey'];
                 
                $this->oDbCon->execute($sql); 
        }


        $rsDetail = $this->getDetailById($pkey);

        for($i=0; $i<count($rsDetail); $i++){

            if ($rsDetail[$i]['qty'] - $rsDetail[$i]['putawayqty'] == 0) {
                $statusKey = 3;
            } else {
                $statusKey = 2;
            }
            $test = $rsDetail[$i]['qty'] - $rsDetail[$i]['putawayqty'];
        }

        // $this->changeStatus($pkey, $statusKey,'',false,true); 
    }

    
}
