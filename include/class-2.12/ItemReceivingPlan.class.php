<?php

class ItemReceivingPlan extends BaseClass
{

    function __construct()
    {
        parent::__construct();

        $this->tableName = 'item_receiving_plan_header';
        $this->tableNameDetail = 'item_receiving_plan_detail';
        $this->tableCustomer = 'customer';
        $this->tableSupplier = 'supplier';
        $this->tableWarehouse = 'warehouse';
        $this->tableWarehouseLayout = 'warehouse_layout';
        $this->tableStatus = 'transaction_status';
        $this->tableBrand = 'brand';
        $this->tableItemCategory = 'item_category';  
        $this->tableCountry = 'country';  

        $this->isTransaction = true;
        $this->securityObject = 'ItemReceivingPlan';

        $this->uploadFileFolder = 'item-receiving-plan/';
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
        $this->arrDetail['unit'] = array('selUnit');
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
        array_push($this->arrDataListAvailableColumn, array('code' => 'warehouse', 'title' => 'warehouse', 'dbfield' => 'warehousename', 'default' => true, 'width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'warehouselayout', 'title' => 'warehouseLayout', 'dbfield' => 'warehouselayoutname', 'default' => true, 'width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'customer', 'title' => 'customer', 'dbfield' => 'customername', 'default' => true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'supplier', 'title' => 'supplier', 'dbfield' => 'suppliername', 'default' => true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'shipper', 'title' => 'shipper', 'dbfield' => 'shippername', 'default' => true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status', 'title' => 'status', 'dbfield' => 'statusname', 'default' => true, 'width' => 70));


        $this->arrSearchColumn = array();
        array_push($this->arrSearchColumn, array('Kode', $this->tableName . '.code'));
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
                'WarehouseLayout.class.php',
                'TransactionType.class.php',
                'DocumentType.class.php',
                'ItemUnit.class.php'
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
                    ' . $this->tableWarehouseLayout . '.name as warehouselayoutname
				from 
					' . $this->tableName . '
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

        if (empty($arrItemName[0])) {
            $this->addErrorList($arrayToJs, false, $this->errorMsg[501]);
        } else {
            for ($i=0; $i < count($arrItemName); $i++) {
                if (empty($arrItemName[$i])) {
                    $this->addErrorList($arrayToJs, false, $this->errorMsg['item'][1]);
                }
            }
        }
        
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

        $id = $rsHeader[0]['pkey'];
        
    
        //cek apakah ada kode barang yang sama, kalau ada tidak boleh
        $rsDetail = $this->getDetailWithRelatedInformation($id);
        $rsItem = $item->searchData('','',true);
        
        $rsItemCodes = array_column($rsItem, 'itemcode');

        $arrErrMsg = array();
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

    }

    function validateCancel($rsHeader,$autoChangeStatus=false){  
		
	 }
    
     function cancelTrans($rsHeader,$copy){  
         
        $pkey = $rsHeader[0]['pkey'];

		
		if ($copy){ 			
			$this->copyDataOnCancel($pkey);
        }
	}    


    function normalizeParameter($arrParam, $trim = false)
    {
        $arrParam = parent::normalizeParameter($arrParam, true);

        return $arrParam;
    }
    
}
