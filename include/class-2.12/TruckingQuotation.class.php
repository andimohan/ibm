<?php

class TruckingQuotation extends BaseClass{ 

    function __construct()
    {

        parent::__construct();

        $this->tableName = 'trucking_quotation_header';
        $this->tableNameDetail = 'trucking_quotation_detail';
        $this->tableStatus = 'trucking_quotation_status';
        $this->tableItem = 'item'; 
        $this->tableEmployee = 'employee';
        $this->tableCategory = 'trucking_service_order_category';
        $this->tableCargoType = 'cargo_type';
        $this->tableWarehouse = 'warehouse'; 
        $this->tableLocation = 'location';

        $this->securityObject = 'TruckingQuotation'; 
        $this->isTransaction = true;
        $this->newLoad = true;

        $this->arrDataDetail = array(); 
        $this->arrDataDetail['pkey'] = array('hidDetailKey');
        $this->arrDataDetail['refkey'] = array('pkey','ref');
//        $this->arrDataDetail['itemkey'] = array('hidItemKey',array('mandatory'=>true));
        $this->arrDataDetail['servicename'] = array('serviceName');
        $this->arrDataDetail['qtyinbaseunit'] = array('qty', array('datatype'=>'number','mandatory'=>true));       
        $this->arrDataDetail['priceinunit'] = array('price','number');
        $this->arrDataDetail['subtotal'] = array('subtotal','number');
        // $this->arrDataDetail['total'] = array('totalDetails','number');
        $this->arrDataDetail['trdesc'] = array('detailNotes');


        $this->arrData = array();
        $this->arrData['pkey'] = array('pkey', array('dataDetail' => array('dataset' => $this->arrDataDetail)));
        $this->arrData['code'] = array('code');
        $this->arrData['name'] = array('name');
        $this->arrData['trdate'] = array('trDate', 'date');
        $this->arrData['warehousekey'] = array('selWarehouseKey');
//        $this->arrData['customerkey'] = array('hidCustomerKey');
        $this->arrData['customername'] = array('customerName');
        $this->arrData['recipientname'] = array('recipientName');
        $this->arrData['categorykey'] = array('hidCategoryKey');
        $this->arrData['cargotypekey'] = array('hidCargoType');
        $this->arrData['consigneekey'] = array('hidConsigneeKey');
        $this->arrData['consigneelocationkey'] = array('hidLocationKey');
        $this->arrData['consigneecontactperson'] = array('contactPerson');
        $this->arrData['consigneewarehousename'] = array('warehouseName');
        $this->arrData['consigneeaddress'] = array('address');
        $this->arrData['locationkey'] = array('hidStuffingLocationKey');
        $this->arrData['stuffinglocationkey'] = array('hidStuffingLocationKey');
        $this->arrData['stuffingaddress'] = array('stuffingAddress');
        $this->arrData['trdesc'] = array('trDesc');
        // $this->arrData['subtotal'] = array('subtotal', 'number');
        $this->arrData['statuskey'] = array('selStatus');
        $this->arrData['routefrom'] = array('routeFrom');
        $this->arrData['routeto'] = array('routeTo');
        $this->arrData['saleskey'] = array('hidSalesKey');
        $this->arrData['total'] = array('total', 'number');

        $this->arrDataListAvailableColumn = array();
        array_push($this->arrDataListAvailableColumn, array('code' => 'code', 'title' => 'code', 'dbfield' => 'code', 'default' => true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'name', 'title' => 'name', 'dbfield' => 'name', 'default' => true, 'width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'date', 'title' => 'date', 'dbfield' => 'trdate', 'default' => true, 'width' => 90, 'align' => 'center', 'format' => 'date'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'customer', 'title' => 'customer', 'dbfield' => 'customername', 'default' => true, 'width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'category', 'title' => 'category', 'dbfield' => 'categoryname', 'default' => true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'location', 'title' => 'location', 'dbfield' => 'locationname', 'default' => true, 'width' => 150));
//        array_push($this->arrDataListAvailableColumn, array('code' => 'routefrom', 'title' => 'from', 'dbfield' => 'routefrom', 'default' => true, 'width' => 150));
//        array_push($this->arrDataListAvailableColumn, array('code' => 'routeto', 'title' => 'to', 'dbfield' => 'routeto', 'default' => true, 'width' => 150));
//        array_push($this->arrDataListAvailableColumn, array('code' => 'total', 'title' => 'total', 'dbfield' => 'total', 'default' => true, 'width' => 80, 'align' => 'right', 'format' => 'number'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status', 'title' => 'status', 'dbfield' => 'statusname', 'default' => true, 'width' => 90));
        array_push($this->arrDataListAvailableColumn, array('code' => 'cargoType', 'title' => 'type', 'dbfield' => 'cargotype', 'width' => 90));
        array_push($this->arrDataListAvailableColumn, array('code' => 'warehouse', 'title' => 'warehouse', 'dbfield' => 'warehousename', 'width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'description', 'title' => 'note', 'dbfield' => 'trdesc', 'width' => 200));

        $this->arrSearchColumn = array ();
        array_push($this->arrSearchColumn, array('Kode',$this->tableName . '.code')); 
        array_push($this->arrSearchColumn, array('Nama',$this->tableName . '.name')); 
        array_push($this->arrSearchColumn, array('Pelanggan',$this->tableName . '.customername'));  
        array_push($this->arrSearchColumn, array('PIC',$this->tableName . '.recipientname'));  
        array_push($this->arrSearchColumn, array('Lokasi',$this->tableLocation . '.name')); 
        array_push($this->arrSearchColumn, array('Rute Asal',$this->tableName . '.routefrom')); 
        array_push($this->arrSearchColumn, array('Rute Tujuan',$this->tableName . '.routeto')); 


        $this->printMenu = array();
        array_push($this->printMenu, array('code' => 'printTransaction', 'name' => $this->lang['printTransaction'], 'icon' => 'print', 'url' => 'print/truckingQuotation'));

        $this->includeClassDependencies(array(
            'Warehouse.class.php',
            'Customer.class.php',
            'Sales.class.php',
            'Item.class.php',
            'GeneralJournal.class.php',
            'Location.class.php'
        ));
        
        
        $this->overwriteConfig();

    }

    function getQuery()
    {

        $sql = '
			SELECT ' . $this->tableName . '.* ,
			    ' . $this->tableStatus . '.status as statusname ,
			    ' . $this->tableStatus . '.textcolor as statuscolor, 
                '.$this->tableEmployee.'.code as salescode,
			    '.$this->tableEmployee.'.name as salesname,
                '.$this->tableCategory.'.name as categoryname, 
			    '.$this->tableCargoType.'.name as cargotype,
                ' . $this->tableWarehouse . '.name as warehousename, 
               ' . $this->tableWarehouse . '.code as warehousecode, 
               ' . $this->tableLocation . '.name as locationname 
			FROM 
                ' . $this->tableName . ' 
                left join ' . $this->tableEmployee . ' on  ' . $this->tableName . '.saleskey = ' . $this->tableEmployee . '.pkey    
                 left join ' . $this->tableLocation . ' on ' . $this->tableName . '.locationkey = ' . $this->tableLocation . '.pkey, 
                '. $this->tableStatus .',
                '.$this->tableCategory.', 
                '.$this->tableCargoType .', 
                '. $this->tableWarehouse .'
			WHERE  
                ' . $this->tableName . '.categorykey = ' . $this->tableCategory . '.pkey and
                ' . $this->tableName . '.cargotypekey = ' . $this->tableCargoType . '.pkey and
				' . $this->tableName . '.warehousekey = ' . $this->tableWarehouse . '.pkey and
				' . $this->tableName . '.statuskey = ' . $this->tableStatus . '.pkey 
 		' . $this->criteria;

        $sql .= $this->getWarehouseCriteria();
        
        return $sql;
    }

    function getDetailWithRelatedInformation($pkey,$criteria=''){ 
        
    	   $sql = 'select
    	   			'.$this->tableNameDetail .'.* 
                  from
    			  	'.$this->tableNameDetail .' 
    			  where 
    			  	refkey in ('.$this->oDbCon->paramString($pkey,',') . ') ';
        
            $sql .= $criteria;
            
            
    		return $this->oDbCon->doQuery($sql);
        
    }
    
    function validateForm($arr, $pkey = '') {
        $arrayToJs = parent::validateForm($arr, $pkey);

        $service = $arr['itemName'];
        $price = $arr['price'];
        $customerName = $arr['customerName'];  
        $categoryKey = $arr['hidCategoryKey'];  
        $locationKey = $arr['hidStuffingLocationKey'];

        if(empty($customerName)) 
            $this->addErrorList($arrayToJs, false, $this->errorMsg['customer'][1]); 
  
        if(empty($categoryKey)) 
            $this->addErrorList($arrayToJs, false, $this->errorMsg['jobType'][1]);  

        if(empty($locationKey)) 
            $this->addErrorList($arrayToJs, false, $this->errorMsg['location'][1]);

        for($i = 0; $i < count($service); $i++) { 
            if(empty($service[$i])) 
                $this->addErrorList($arrayToJs, false, $this->errorMsg['service'][1]); 
        }
 
        return $arrayToJs;
    }
 

    function normalizeParameter($arrParam, $trim = false)  {

        $qty = $arrParam['qty'];
        $price = $arrParam['price'];
        $subtotal = $arrParam['subtotal'];

        $subtotal = 0;
        $total = 0;
        for($i = 0; $i < count($qty); $i++) { 
            $subtotal = $this->unFormatNumber($qty[$i]) * $this->unFormatNumber($price[$i]); 
            $arrParam['subtotal'][$i] = $subtotal; 
            $total += $subtotal; 
        }

        $arrParam['total'] = $total;

        $arrParam = parent::normalizeParameter($arrParam, true);

        return $arrParam;
    }

}

?>
