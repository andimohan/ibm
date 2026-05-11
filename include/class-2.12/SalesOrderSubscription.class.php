<?php

class SalesOrderSubscription extends BaseClass{
	
    function __construct(){

    parent::__construct();

    $this->tableName = 'sales_order_subscription_header';
    $this->tableNameDetail = 'sales_order_subscription_detail';
    $this->tableNameDetailInitial = 'sales_order_subscription_detail_initial';
    $this->tableInvoicePeriode = 'invoice_period'; 
    $this->tableInvoiceRecurring = 'invoice_order_subscription_periode'; 
    $this->tableCustomer = 'customer';
    $this->tableEmployee = 'employee';
    $this->tableStatus = 'sales_order_subscription_status';
    $this->tableWarehouse = 'warehouse';  
    $this->tableItem = 'item'; 	
    $this->tableMedia = 'media'; 	
    $this->tableLocation = 'location'; 	
    $this->tableItemUnit = 'item_unit'; 	   
    $this->tableJobDetails = 'job_details'; 	   
    $this->isTransaction = true;
    $this->securityObject = 'SalesOrderSubscription';
        
    $this->importUrl = 'import/salesOrderSubscription';
        
    $this->arrDataDetail = array();  
    $this->arrDataDetail['pkey'] = array('hidDetailKey');
    $this->arrDataDetail['refkey'] = array('pkey','ref');  
    $this->arrDataDetail['itemkey'] = array('hidItemKey'); 
    $this->arrDataDetail['qty'] = array('qty','number'); 
	$this->arrDataDetail['priceinunit'] = array('priceInUnit','number', array('mandatory'=>true)); 
	$this->arrDataDetail['total'] = array('detailSubtotal','number'); 
        
    $this->arrMonthly = array();  
    $this->arrMonthly['pkey'] = array('hidDetailMonthlyKey');
    $this->arrMonthly['refkey'] = array('pkey','ref');  
    $this->arrMonthly['itemkey'] = array('hidItemMonthlyKey'); 
    $this->arrMonthly['qty'] = array('qtyMonthly','number'); 
    $this->arrMonthly['qtyinbaseunit'] = array('qtyMonthly','number', array('mandatory'=>true));
    $this->arrMonthly['priceinunit'] = array('priceInUnitMonthly','number', array('mandatory'=>true)); 
	$this->arrMonthly['total'] = array('detailSubtotalMonthly','number');
        
    $arrDetails = array(); 
    array_push($arrDetails, array('dataset' => $this->arrMonthly, 'tableName' => $this->tableNameDetail));
    array_push($arrDetails, array('dataset' => $this->arrDataDetail, 'tableName' => $this->tableNameDetailInitial));
        
    $this->arrData = array(); 
    $this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails));  
    $this->arrData['code'] = array('code');
    $this->arrData['trdate'] = array('trDate','date');
    $this->arrData['customerkey'] = array('hidCustomerKey');
    $this->arrData['warehousekey'] = array('selWarehouseKey');
    //$this->arrData['pic'] = array('pic');
    $this->arrData['employeekey'] = array('hidEmployeeKey');
//    $this->arrData['attention'] = array('attention');
    //$this->arrData['phone'] = array('phone');
    $this->arrData['product'] = array('product');
    $this->arrData['address'] = array('address');
    $this->arrData['trdesc'] = array('trDesc');
    $this->arrData['statuskey'] = array('selStatus');        
    $this->arrData['grandtotal'] = array('total','number');
    $this->arrData['grandtotalmonthly'] = array('totalMonthly','number');
    
    $this->arrData['subtotal'] = array('subtotal','number'); 
    $this->arrData['beforetaxtotal'] = array('beforeTaxTotal','number'); 
    $this->arrData['taxpercentage'] = array('taxPercentage','number');
    $this->arrData['taxvalue'] = array('taxValue','number'); 
    $this->arrData['ispriceincludetax'] = array('chkIncludeTax');
        
    $this->arrData['subtotalmonthly'] = array('subtotalMonthly','number'); 
    $this->arrData['beforetaxtotalmonthly'] = array('beforeTaxTotalMonthly','number'); 
    $this->arrData['taxpercentagemonthly'] = array('taxPercentageMonthly','number');
    $this->arrData['taxvaluemonthly'] = array('taxValueMonthly','number'); 
    $this->arrData['ispriceincludetaxmonthly'] = array('chkIncludeTaxMonthly');
    $this->arrData['jobdetailskey'] = array('selJobDetails');
    $this->arrData['periodekey'] = array('selInvoiceRecurring');
    $this->arrData['invoiceperiodkey'] = array('selInvoicePeriodeTime');
    //$this->arrData['ispostpaid'] = array('isPostPaid');
    $this->arrData['invoiceduedate'] = array('invoiceDueDate','date');

    $this->arrDataListAvailableColumn = array(); 
    array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 100));
    array_push($this->arrDataListAvailableColumn, array('code' => 'date','title' => 'date','dbfield' => 'trdate','default'=>true, 'width' => 90,  'align' => 'center', 'format' => 'date'));
    array_push($this->arrDataListAvailableColumn, array('code' => 'client','title' => 'customer','dbfield' => 'customername','default'=>true, 'width' => 150));
    array_push($this->arrDataListAvailableColumn, array('code' => 'media','title' => 'media','dbfield' => 'medianame','default'=>true, 'width' => 80));
    array_push($this->arrDataListAvailableColumn, array('code' => 'products','title' => 'products','dbfield' => 'product','default'=>true, 'width' => 150));
    array_push($this->arrDataListAvailableColumn, array('code' => 'jobDetails','title' => 'jobDetails','dbfield' => 'jobdetailname','default'=>true, 'width' => 120));
    array_push($this->arrDataListAvailableColumn, array('code' => 'initialCost','title' => 'initialCost','dbfield' => 'grandtotal','default'=>true,'align'=>'right','format'=>'integer', 'width' => 100));
    array_push($this->arrDataListAvailableColumn, array('code' => 'monthlyCost','title' => 'monthlyCost','dbfield' => 'grandtotalmonthly','default'=>true, 'align'=>'right','format'=>'integer', 'width' => 90));
    array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
    array_push($this->arrDataListAvailableColumn, array('code' => 'warehouse','title' => 'warehouse','dbfield' => 'warehousename', 'width' => 120));
     
    $this->printMenu = array();
    array_push($this->printMenu,array('code' => 'printTransaction', 'name' => $this->lang['printTransaction'],  'icon' => 'print', 'url' => 'print/salesOrderSubscription'));
		
	$this->includeClassDependencies(array(
		'Warehouse.class.php',  
		'Customer.class.php', 
		'Location.class.php', 
		'Employee.class.php', 
		'InstallationWorkOrder.class.php', 
		'ItemUnit.class.php', 
		'JobDetails.class.php', 
		'Media.class.php', 
		'StagesProcess.class.php', 
		'Item.class.php', 
		'InstallationBAST.class.php', 
		'Termination.class.php', 
		'InvoiceOrderSubscription.class.php', 
		'GeneralJournal.class.php', 
		'InvoicePeriod.class.php' 
	
	)); 

        
    }

    function getQuery(){
        $sql = '
            SELECT
                '.$this->tableName.'.* ,  
                '.$this->tableWarehouse.'.name as warehousename, 
                '.$this->tableCustomer.'.name as customername, 
                employeepic.name as employeename, 
                employeesales.name as salesname, 
                '.$this->tableCustomer.'.mediakey, 
                '.$this->tableCustomer.'.locationkey, 
                '.$this->tableCustomer.'.phone, 
                '.$this->tableCustomer.'.address, 
                '.$this->tableCustomer.'.attention, 
                '.$this->tableCustomer.'.sid, 
                '.$this->tableMedia.'.name as medianame, 
                '.$this->tableLocation.'.name as locationname, 
                '.$this->tableJobDetails.'.name as jobdetailname, 
                '.$this->tableStatus.'.status as statusname,
                '.$this->tableInvoicePeriode.'.months,
				IF(invoiceduedate="1970-01-01" || invoiceduedate IS NULL, 0, 1) AS isinvoiced
            FROM '.$this->tableStatus.',
                 '.$this->tableName.'
                    left join '.$this->tableInvoicePeriode.' on  '.$this->tableName.'.invoiceperiodkey = '.$this->tableInvoicePeriode.'.pkey
                    left join '. $this->tableEmployee.' employeepic on ' . $this->tableName .'.employeekey = employeepic.pkey, 
                 '.$this->tableCustomer.'
                    left join '. $this->tableEmployee.' employeesales on ' . $this->tableCustomer .'.saleskey = employeesales.pkey     
                    left join '.$this->tableMedia.' on  '.$this->tableCustomer.'.mediakey = '.$this->tableMedia.'.pkey 
                    left join '.$this->tableLocation.' on '.$this->tableCustomer.'.locationkey = '.$this->tableLocation.'.pkey,  
                 '.$this->tableJobDetails.', 
                 '.$this->tableWarehouse.' 
            WHERE   
                  '.$this->tableName.'.customerkey = '.$this->tableCustomer.'.pkey and
                  '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey and
                  '.$this->tableName.'.jobdetailskey = '.$this->tableJobDetails.'.pkey and
                  '.$this->tableName.'.warehousekey = '.$this->tableWarehouse.'.pkey 
            ' .$this->criteria ;
            
        $sql .=  $this->getWarehouseCriteria() ;  
//		$this->setLog($sql,true);
        return $sql;
    }

 
    function reCountGrandTotal($arrParam){  
            $isPriceIncludeTax = (!empty($arrParam['chkIncludeTax'])) ? 1 : 0; 
            $isPriceIncludeTaxMonthly = (!empty($arrParam['chkIncludeTaxMonthly'])) ? 1 : 0; 
            $subtotal = 0 ;
            $grandtotal = 0;

            $grandtotalmonthly = 0;
            $subtotalmonthly = 0 ;

            $arrItemKey = $arrParam['hidItemKey'];
            $arrItemMonthlyKey = $arrParam['hidItemMonthlyKey'];
            $taxPercentage = $this->unFormatNumber($arrParam['taxPercentage']); 
            $taxValue = $this->unFormatNumber($arrParam['taxValue']);        
            $taxPercentageMonthly = $this->unFormatNumber($arrParam['taxPercentageMonthly']); 
            $taxValueMonthly = $this->unFormatNumber($arrParam['taxValueMonthly']); 
            $arrQtyMonthly = $arrParam['qtyMonthly']; 
            $arrPriceinunitMonthly = $arrParam['priceInUnitMonthly']; 
            $arrQty = $arrParam['qty']; 
            $arrPriceinunit = $arrParam['priceInUnit']; 

            $arrItemDetail = array();
            $arrItemMonthlyDetail = array();
            $item = new Item();
            $totalProfit = 0;
            $totalGramasi = 0;
            $totalGramasiMonthly = 0;
        
            for ($j=0;$j<count($arrItemMonthlyKey);$j++){
				$rsItem = $item->getDataRowById($arrItemMonthlyKey[$j]);
                if (empty($arrItemMonthlyKey[$j]) || empty($rsItem))  
                    continue; 

                    $itemmonthlykey = $arrItemMonthlyKey[$j];
                    $qtyMonthly =  $this->unFormatNumber($arrQtyMonthly[$j]);
                    $priceInUnitMonthly = $this->unFormatNumber($arrPriceinunitMonthly[$j]);
                    $detailMonthlySubtotal = $qtyMonthly * $priceInUnitMonthly ;
                    $arrItemMonthlyDetail[$j]['detailSubtotalMonthly'] = $detailMonthlySubtotal; 

                    $subtotalmonthly += $detailMonthlySubtotal ; 
            } 
        
            for ($i=0;$i<count($arrItemKey);$i++){
				$rsItem = $item->getDataRowById($arrItemKey[$i]);
                if (empty($arrItemKey[$i]) || empty($rsItem))  
                    continue; 
				
                    $qty =  $this->unFormatNumber($arrQty[$i]);
                    $priceInUnit = $this->unFormatNumber($arrPriceinunit[$i]);
                    $detailSubtotal = $qty * $priceInUnit ;
                    $arrItemDetail[$i]['detailSubtotal'] = $detailSubtotal; 
                    $subtotal += $detailSubtotal ; 
            } 
        
            $beforeTaxTotal = $subtotal;
            $grandtotal = $beforeTaxTotal;
        
            $beforeTaxTotalMonthly = $subtotalmonthly;
            $grandtotalmonthly = $beforeTaxTotalMonthly;
        
            if ($isPriceIncludeTax == false) {
                    $taxValue = $beforeTaxTotal * $taxPercentage / 100;
                    $grandtotal += $taxValue;
            }else{
                    $taxValue = ($taxPercentage/(100 + $taxPercentage)) * $grandtotal;   
                    $beforeTaxTotal = $grandtotal - $taxValue ;
            }
        
            if ($isPriceIncludeTaxMonthly == false) {
                    $taxValueMonthly  = $beforeTaxTotalMonthly * $taxPercentageMonthly  / 100;
                    $grandtotalmonthly  += $taxValueMonthly ;
            }else{
                    $taxValueMonthly = ($taxPercentageMonthly/(100 + $taxPercentageMonthly)) * $grandtotalmonthly;   
                    $beforeTaxTotalMonthly = $grandtotalmonthly - $taxValueMonthly ;
            }
        
            $reCountResult = array();
            $reCountResult['subtotal'] = $subtotal;
            $reCountResult['beforeTaxTotal'] = $beforeTaxTotal;
            $reCountResult['isPriceIncludeTax'] = $isPriceIncludeTax;
            $reCountResult['total'] = $grandtotal;
            
            $reCountResult['subtotalMonthly'] = $subtotalmonthly;
            $reCountResult['beforeTaxTotalMonthly'] = $beforeTaxTotalMonthly;
            $reCountResult['isPriceIncludeTaxMonthly'] = $isPriceIncludeTaxMonthly;
            $reCountResult['totalMonthly'] = $grandtotalmonthly;
        
            $reCountResult['detailCOGS'] = $arrItemDetail;
            $reCountResult['detailMonthlyCOGS'] = $arrItemMonthlyDetail;
        
            return $reCountResult;

    } 

    function validateForm($arr,$pkey = ''){ 
        $item = new Item();   
        $arrayToJs = parent::validateForm($arr,$pkey); 
        $arrItemkey = $arr['hidItemKey']; 
        $arrQty = $arr['qty']; 
        $arrPriceinunit = $arr['priceInUnit'];
        $arrItemMonthlykey = $arr['hidItemMonthlyKey']; 
        $arrQtyMonthly = $arr['qtyMonthly']; 
        $arrPriceinunitMonthly = $arr['priceInUnitMonthly'];
        $customerkey = $arr['hidCustomerKey']; 
		$subtotal = $arr['subtotal'];
		$subtotalMonthly = $arr['subtotalMonthly'];
        
        $arrDetailKey = array();
        
        //validasi kalo status gk menunggu gk bisa edit 
        if (!empty($pkey)){
            $rs = $this->getDataRowById($pkey);
            if ($rs[0]['statuskey'] <> 1){
                $this->addErrorList($arrayToJs,false,$this->errorMsg[212]);
            }
        }  
        
        if(empty($customerkey)){ 
            $this->addErrorList($arrayToJs,false,$this->errorMsg['customer'][1]);
        }else{
            // nanti perlu cek lg, bisa gk kalo diaplikasikan di tmp lain
            $customer = new Customer();
            $rsCustomer = $customer->getDataRowById($customerkey);
            if (empty($rsCustomer[0]['locationkey']))
                 $this->addErrorList($arrayToJs,false,$this->errorMsg['location'][1]);
        }
        
        if(empty($arrItemMonthlykey)) 
            $this->addErrorList($arrayToJs,false,  $this->errorMsg[501]);  
        
        $arrDetailKeys = array(); 

        for($i=0;$i<count($arrItemkey);$i++) { 
            if (!empty($arrItemkey[$i])){
                $rsItem = $item->getDataRowById($arrItemkey[$i]);
                if ($this->unFormatNumber($arrQty[$i]) <= 0){ 
                    $this->addErrorList($arrayToJs,false,$rsItem[0]['name']. '. ' . $this->errorMsg[510]);  
                }

                $priceMandatory = $this->loadSetting('priceMandatory');
                if ($priceMandatory == 1 && $this->unFormatNumber($arrPriceinunit[$i]) <= 0){  
                    $this->addErrorList($arrayToJs,false,$rsItem[0]['name']. '. ' . $this->errorMsg[511]);  
                }  

            }

            // cek ada detail double gk  

        }

        for($j=0;$j<count($arrItemMonthlykey);$j++) { 
			if (empty($arrItemMonthlykey[$j]) ){ 
				$this->addErrorList($arrayToJs,false, $this->errorMsg['item'][1]); 	
			} 

			if (!empty($arrItemkey[$j])){
				$rsItemMonthly = $item->getDataRowById($arrItemMonthlykey[$j]);
				if ($this->unFormatNumber($arrQtyMonthly[$j]) <= 0){ 
					$this->addErrorList($arrayToJs,false,$rsItemMonthly[0]['name']. '. ' . $this->errorMsg[510]);  
				}

				$priceMandatoryMonthly = $this->loadSetting('priceMandatory');
				if ($priceMandatoryMonthly == 1 && $this->unFormatNumber($arrPriceinunitMonthly[$j]) <= 0){  
					$this->addErrorList($arrayToJs,false,$rsItemMonthly[0]['name']. '. ' . $this->errorMsg[511]);  
				}  
			}
		}
        
        return $arrayToJs;
    }
      
    function getDetailWithRelatedInformation($pkey,$criteria=''){
        
      $sql = 'select
            '.$this->tableNameDetailInitial.'.*,
            '.$this->tableItem.'.name as itemname,
            '.$this->tableItemUnit.'.name as unitname,
            '.$this->tableItem.'.code as itemcode
        from
            '.$this->tableNameDetailInitial.'
				left join '.$this->tableItemUnit.' on '.$this->tableNameDetailInitial.'.unitkey = '.$this->tableItemUnit.'.pkey  ,
            '.$this->tableItem.'
        where  
            '.$this->tableNameDetailInitial .'.itemkey = '.$this->tableItem.'.pkey and
            '. $this->tableNameDetailInitial.'.refkey in  ('.$this->oDbCon->paramString($pkey,',') . ') ' ;

        $sql .= $criteria;
  
        return $this->oDbCon->doQuery($sql);

    }
 
    function getAllDetailRelatedInformation($pkey,$criteria=''){
        
        $sql = 'select "Biaya Bulanan" as type, 
            '.$this->tableNameDetail.'.*,
            '.$this->tableItem.'.name as itemname,
            '.$this->tableItemUnit.'.name as unitname,
            '.$this->tableItem.'.code as itemcode
        from
            '.$this->tableNameDetail.'
				left join '.$this->tableItemUnit.' on '.$this->tableNameDetail.'.unitkey = '.$this->tableItemUnit.'.pkey  ,
            '.$this->tableItem.'
          where  
            '.$this->tableNameDetail .'.itemkey = '.$this->tableItem.'.pkey and
            '. $this->tableNameDetail.'.refkey in  ('.$this->oDbCon->paramString($pkey,',') . ') ' ;
        
        $sql .= ' UNION ';
        
        $sql .=  'select "Biaya Pertama",
            '.$this->tableNameDetailInitial.'.*,
            '.$this->tableItem.'.name as itemname,
            '.$this->tableItemUnit.'.name as unitname,
            '.$this->tableItem.'.code as itemcode
        from
            '.$this->tableNameDetailInitial.'
				left join '.$this->tableItemUnit.' on '.$this->tableNameDetailInitial.'.unitkey = '.$this->tableItemUnit.'.pkey  ,
            '.$this->tableItem.'
        where  
            '.$this->tableNameDetailInitial .'.itemkey = '.$this->tableItem.'.pkey and
            '. $this->tableNameDetailInitial.'.refkey in  ('.$this->oDbCon->paramString($pkey,',') . ') ' ;
        
        
        $sql .= $criteria;
        
        return $this->oDbCon->doQuery($sql);

        
    }
    
      function getMonthlyDetailRelatedInformation($pkey,$criteria=''){
        $sql = 'select
            '.$this->tableNameDetail.'.*,
            '.$this->tableItem.'.name as itemname,
            '.$this->tableItemUnit.'.name as unitname,
            '.$this->tableItem.'.code as itemcode

        from
            '.$this->tableNameDetail.'
				left join '.$this->tableItemUnit.' on '.$this->tableNameDetail.'.unitkey = '.$this->tableItemUnit.'.pkey  ,
            '.$this->tableItem.'
          where  
            '.$this->tableNameDetail .'.itemkey = '.$this->tableItem.'.pkey and
            '. $this->tableNameDetail.'.refkey in  ('.$this->oDbCon->paramString($pkey,',') . ') ' ;

        $sql .= $criteria;
        //$this->setLog($sql,true);
        return $this->oDbCon->doQuery($sql);

    }
	
	function getInvoiceDate($trDate,$period=0){
		$billingDate = new DateTime($trDate);
		$billingDate -> modify($period.' month');
		return $billingDate -> format('Y-m-d');
	}
	
	function addMonths($date,$months) {
	  $orig_day = $date->format("d");
	  $date->modify("+".$months." months");
	  while ($date->format("d")<$orig_day && $date->format("d")<5) {
		$date->modify("-1 day");
	  }
	}
	
	function calculateProrate($rsSO,$startDate,$endDate){
        
		$rsMonthProrate = $this->getMonthlyDetailRelatedInformation($rsSO[0]['pkey']);
		$dateSubs = new DateTime($startDate);
		$EODateSubs = date('Y-m-t', strtotime($startDate));
		$Lastdate = new DateTime($endDate);
		$dayOfSubscription = $Lastdate->diff($dateSubs);
        $days = $dayOfSubscription->d;
        $days += 1;  // agar hari yg sama sudah dihitung
        
//        $this->setLog('$startDate => '.$startDate,true);
//        $this->setLog('$endDate => '.$endDate,true);
//        $this->setLog('$EODateSubs => '.$EODateSubs,true);
//        $this->setLog('$days => '.$days,true);
//        $this->setLog('$prorate => '.$days .'/ '.date('d', strtotime($EODateSubs)),true);
        
		$prorate = $days / date('d', strtotime($EODateSubs)); 
//        $this->setLog('$prorate => '. $prorate,true);
       
        
		if($prorate<=0)
			return array();
		
		for($i=0;$i<count($rsMonthProrate);$i++){
			if($rsMonthProrate[$i]['priceinunit']<=0 || $rsMonthProrate[$i]['qty']<=0)
				continue;

			$price = $prorate * $rsMonthProrate[$i]['priceinunit'];
			$total = $price * $rsMonthProrate[$i]['qty'];
			$rsMonthProrate[$i]['priceinunit'] = $price;
			$rsMonthProrate[$i]['total'] = $total;

		}
		return $rsMonthProrate;
	}
	
	function getItemForInvoice($pkey){
		$invoiceOrderSubscription = new InvoiceOrderSubscription();
		$installationBAST = new InstallationBAST();
		$customer = new Customer();
		$invoicePeriod = new InvoicePeriod();
		$rsProrate = array();
		$rsFirst = array();
		$rsMonthly = array();
		$rsDetailMonthly = $this->getMonthlyDetailRelatedInformation($pkey);
		$rsSO = $this->getDataRowById($pkey);
		$isRecurring = ($rsSO[0]['periodekey']==1) ? true : false;
		$rsInvoicePeriod = $invoicePeriod->getDataRowById($rsSO[0]['invoiceperiodkey']);
		$monthsPeriode = (!empty($rsInvoicePeriod)) ? $rsInvoicePeriod[0]['months'] : 1;
		$rsCustomer = $customer->getDataRowById($rsSO[0]['customerkey']);
		$rsInvoice = $invoiceOrderSubscription->searchData ('','',true,' and '.$invoiceOrderSubscription->tableName.'.refkey = '.$this->oDbCon->paramString($pkey).' and ('.$invoiceOrderSubscription->tableName.'.statuskey in (2,3))  limit 1'); 
        $rsBast = $installationBAST->searchData('','',true,' and '.$installationBAST->tableName.'.refkey = '.$this->oDbCon->paramString($pkey).' and ('.$installationBAST->tableName.'.statuskey  in (2,3))');
		$rsMonthly = $rsDetailMonthly;
		
		if(empty($rsInvoice)){
			if(!empty($rsBast)) $rsFirst = $this->getDetailWithRelatedInformation($pkey);
            $EODateSubs = date('Y-m-t', strtotime($rsCustomer[0]['subscriptionactivationdate']));
			$rsProrate = $this->calculateProrate($rsSO,$rsSO[0]['invoiceduedate'],$EODateSubs); 
		}
		
		$arrItemInvoice = array_merge($rsFirst,$rsProrate,$rsMonthly);

        return $arrItemInvoice;
		
	}
    
    function generateDefaultQueryForAutoComplete($returnField){ 
        
        $sql = 'select
					'.$this->tableName . '.pkey,
                    '.$this->tableName . '.code as value, 
                    trdate,
					grandtotal 
				from 
					'.$this->tableName . ', 
                    '.$this->tableStatus.' 
				where  		 
					'.$this->tableName . '.statuskey = '.$this->tableStatus.'.pkey
			';
        
          
         return $sql;
     }
    

    function normalizeParameter($arrParam, $trim=false){
        $arrParam['taxPercentage'] = (isset($arrParam['taxPercentage'])) ? $arrParam['taxPercentage'] : 0;
        $arrParam['taxValueMonthly'] = (isset($arrParam['taxValueMonthly'])) ? $arrParam['taxValueMonthly'] : 0;
        $arrParam['taxPercentageMonthly'] = (isset($arrParam['taxPercentageMonthly'])) ? $arrParam['taxPercentageMonthly'] : 0;
        
        $arrItemkey = $arrParam['hidItemKey'];
        $arrItemMonthlykey = $arrParam['hidItemMonthlyKey'];
		
        $reCountResult = $this->reCountGrandTotal($arrParam); 
        $arrParam['detailCOGS'] = $reCountResult['detailCOGS'];
        $arrParam['subtotal'] = $reCountResult['subtotal'];
        $arrParam['beforeTaxTotal'] = $reCountResult['beforeTaxTotal'];
        $arrParam['isPriceIncludeTax'] = $reCountResult['isPriceIncludeTax'];
        $arrParam['total'] = $reCountResult['total'];
        
        for ($i=0;$i<count($arrItemkey);$i++){ 
            $arrParam['detailSubtotal'][$i] = $arrParam['detailCOGS'][$i]['detailSubtotal'];
        }
        
        $arrParam['detailMonthlyCOGS'] = $reCountResult['detailMonthlyCOGS'];
        $arrParam['subtotalMonthly'] = $reCountResult['subtotalMonthly'];
        $arrParam['beforeTaxTotalMonthly'] = $reCountResult['beforeTaxTotalMonthly'];
        $arrParam['isPriceIncludeTaxMonthly'] = $reCountResult['isPriceIncludeTaxMonthly'];
        $arrParam['totalMonthly'] = $reCountResult['totalMonthly'];
           
        for ($j=0;$j<count($arrItemMonthlykey);$j++){ 
            $arrParam['detailSubtotalMonthly'][$j] = $arrParam['detailMonthlyCOGS'][$j]['detailSubtotalMonthly'];
        }
        
        $arrParam = parent::normalizeParameter($arrParam,true); 
        
        return $arrParam;
    }


    function  afterStatusChanged($rsHeader){
    
         
    }
    
        
    function validateConfirm($rsHeader){ 

       
    } 
	
	function validateOnHold($rsHeader){ 
       
    } 
	
	function validateTerminated($rsHeader,$autoChangeStatus){
		if ($autoChangeStatus)  return;
		
		$this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' . $this->errorMsg[201].' '.$this->errorMsg['salesOrderSubscription'][3]); 
    } 

    function confirmTrans($rsHeader){

       // $warehouse = new Warehouse();
        
        //update jurnal umum 
//        $this->updateGL($rsHeader); 
            
    } 
	
	function validateReturnToConfirm($rsHeader,$autoChangeStatus){
		if($autoChangeStatus)  return;
    	$id = $rsHeader[0]['pkey'];
        $insatallationBAST = new InstallationBAST();  
		$rsBast = $insatallationBAST->searchData('','',true,' and '.$insatallationBAST->tableName.'.refkey = '.$this->oDbCon->paramString($id).' and ('.$insatallationBAST->tableName.'.statuskey  in (2,3))');
    	if(!empty($rsBast)) 
            $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' . $this->errorMsg[201].'<br> <strong>' .$rsBast[0]['code'].'</strong>, '.$this->errorMsg[225]);
	} 
	
	function onHoldTrans($rsHeader){

            
    } 
	
	function terminatedTrans($rsHeader){
            
    } 
    

    function validateCancel($rsHeader,$autoChangeStatus=false){
        $id = $rsHeader[0]['pkey'];
        $insatallationBAST = new InstallationBAST();
        $installationWorkOrder = new InstallationWorkOrder();
        $termination = new Termination();
        $rsBast = $insatallationBAST->searchData('','',true,' and '.$insatallationBAST->tableName.'.refkey = '.$this->oDbCon->paramString($id).' and ('.$insatallationBAST->tableName.'.statuskey  in (2,3))');
        if(!empty($rsBast)) 
            $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' . $this->errorMsg[201].'<br> <strong>' .$rsBast[0]['code'].'</strong>, '.$this->errorMsg[225]);
	    
        $rsWO = $installationWorkOrder->searchData('','',true,' and '.$installationWorkOrder->tableName.'.salesorderkey = '.$this->oDbCon->paramString($id).' and '. $installationWorkOrder->tableName.'.statuskey in (2,3)');
        if (!empty($rsWO)) 
           $this->addErrorLog( false, '<strong>'.$rsHeader[0]['code'].'</strong> ' .$this->errorMsg[201].'<br><strong>'.$rsWO[0]['code'].'</strong>, ' .$this->errorMsg[225] );
        
        $rsTermination = $termination->searchData('','',true,' and '.$termination->tableName.'.salesorderkey = '.$this->oDbCon->paramString($id).' and '. $termination->tableName.'.statuskey in (2,3)');
        if (!empty($rsTermination)) 
           $this->addErrorLog( false, '<strong>'.$rsHeader[0]['code'].'</strong> ' .$this->errorMsg[201].'<br><strong>'.$rsTermination[0]['code'].'</strong>, ' .$this->errorMsg[225] );

        $rsInvoiced = $this->getInvoiceInformation($id);
        if (!empty($rsInvoiced)) 
           $this->addErrorLog( false, '<strong>'.$rsHeader[0]['code'].'</strong> ' .$this->errorMsg[201].'<br><strong>'.$rsInvoiced[0]['code'].'</strong>, ' .$this->errorMsg[225] );


    } 
     
	function cancelTrans($rsHeader,$copy){
        
		$id = $rsHeader[0]['pkey'];
        $rsDetail = $this->getDetailById($id);
        $insatallationBAST = new InstallationBAST();
        $installationWorkOrder = new InstallationWorkOrder();
        $termination = new Termination();
        $rsBast = $insatallationBAST->searchData('','',true,' and '.$insatallationBAST->tableName.'.refkey = '.$this->oDbCon->paramString($id).' and ('.$insatallationBAST->tableName.'.statuskey = 1)');
        for($j=0;$j<count($rsBast);$j++) 
          $insatallationBAST->changeStatus($rsBast[$j]['pkey'],4,'',false,true);
		
	   	$rsWorkOrder = $installationWorkOrder->searchData('','',true,' and '.$installationWorkOrder->tableName.'.salesorderkey = '.$this->oDbCon->paramString($id).' and '. $installationWorkOrder->tableName.'.statuskey = 1');
        for($i=0;$i<count($rsWorkOrder);$i++) 
          $installationWorkOrder->changeStatus($rsWorkOrder[$i]['pkey'],4,'',false,true); 
        
        $rsTermination = $termination->searchData('','',true,' and '.$termination->tableName.'.salesorderkey = '.$this->oDbCon->paramString($id).' and '. $termination->tableName.'.statuskey = 1');
        for($j=0;$j<count($rsTermination);$j++) 
          $termination->changeStatus($rsTermination[$j]['pkey'],4,'',false,true); 
         
		if ($copy)
			$this->copyDataOnCancel($id);	  
		   
      
	}
	
	function changeTransactionStatus($id,$status,$reason='',$copy=false, $autoChangeStatus = false, $ignoreValidation = false){
             
        if (empty($_SESSION[$this->loginAdminSession]['id']))
            die;
          
        $rsHeader = $this->getDataRowById($id); 


      	try{ 
            // jika status bkn status sendiri dan bukan status terakhir (status cancel)
            //$this->setLog($this->tableName.' -> ' . $rsHeader[0]['statuskey'] .'=='. $status);
              
            if(!$autoChangeStatus){  
                $security = new Security();
                if(!$security->isAdminLogin($this->securityObject,$status,false))  
                    $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'.</strong> '.$this->errorMsg[252],true);   
            }
            
            if ($rsHeader[0]['statuskey'] == count($this->getAllStatus())) 
                $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'.</strong> '.$this->errorMsg[221],true);   
        
            if ($rsHeader[0]['statuskey'] == $status) 
                $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'.</strong> '.$this->errorMsg[224],true);   
   
        }catch(Exception $e){ 
 		     return $this->getErrorLog(); 
			//$this->addErrorList($arrayToJs,false,$e->getMessage());
		}		
				 

		try{ 
            
            // ================== VALIDATION
		  	switch ($status){
				case 1 : $this->validateInput($rsHeader); 
						  break;
                case 2 : if ($rsHeader[0]['statuskey'] < $status )
                            $this->validateConfirm($rsHeader); 
						 else
							 $this->validateReturnToConfirm($rsHeader,$autoChangeStatus);
						  break;
                case 3 : if ($rsHeader[0]['statuskey'] < $status )
                            $this->validateClose($rsHeader);
						  break;
				case 4 : $this->validateOnHold($rsHeader); 
						  break;
				case 5 : $this->validateTerminated($rsHeader, $autoChangeStatus);
						  break; 
				case 6 : $this->validateCancel($rsHeader, $autoChangeStatus);
						  break; 
			} 
             
             
            //make sure we throw error 
            $this->throwIfHasErrorLog();  
             
            
            // ================== VALIDATION OK !
            
			if(!$this->oDbCon->startTrans())
				throw new Exception($this->errorMsg[100]);
					  
            
			switch ($status){ 
				case 2 : if ($rsHeader[0]['statuskey'] < $status ){ 
                            $this->confirmTrans($rsHeader); 
                            $this->afterConfirmTrans($rsHeader);
                        }else{ 
                            $this->backConfirmTrans($rsHeader); 
                            $this->afterBackConfirmTrans($rsHeader);  
                        }
                         break; 
				case 3 : $this->closeTrans($rsHeader); 
                         $this->afterCloseTrans($rsHeader); 
                         break; 
				/*case 4 : $this->onHoldTrans($rsHeader);
                         break; 
				case 5 : $this->terminatedTrans($rsHeader);
                         break; */
				case 6 : $this->cancelTrans($rsHeader,$copy);
                         $this->afterCancelTrans($rsHeader);
                         break;  
					
			}
			
			$sql = 'update '.$this->tableName.' set statuskey = '.$this->oDbCon->paramString($status).' where pkey = ' . $this->oDbCon->paramString($id); 
            $this->oDbCon->execute($sql);  
             
            $this->setTransactionLog($status,$id,'',$reason);
            
            $this->afterStatusChanged($rsHeader);
                
			$this->oDbCon->endTrans();  
			$this->addErrorLog(true,$this->lang['dataHasBeenSuccessfullyUpdated']);   
		
	    } catch(Exception $e){ 
             
            $this->oDbCon->rollback(); 
            
            if (!empty($e->getMessage()))
                $this->addErrorLog(false,$e->getMessage());
			//$this->addErrorList($arrayToJs,false,$e->getMessage());
		}		
				 
        return $this->getErrorLog(); 
  }
    
    
    function getInvoicePeriode($pkey=''){ 
       
	   $sql = 'select
	   			'.$this->tableInvoiceRecurring .'.pkey, 
	   			'.$this->tableInvoiceRecurring .'.name
              from
			  	'.$this->tableInvoiceRecurring .' 
			  where
			  	'.$this->tableInvoiceRecurring .'.statuskey = 1';
                
        if(!empty($pkey))
            $sql .= ' and pkey = '.$this->oDbCon->paramString($pkey);
        
        
       $sql .=' order by name asc';
         
		return $this->oDbCon->doQuery($sql);
	
   }
    
    
    function getInvoiceInformation($pkey){
        $invoiceOrderSubscription = new InvoiceOrderSubscription();
      
        $sql = 'select
            '.$invoiceOrderSubscription->tableName.'.code,    
            '.$invoiceOrderSubscription->tableName.'.trdate,
            '.$invoiceOrderSubscription->tableName.'.isdownpayment,
            '.$invoiceOrderSubscription->tableName.'.customerkey,
            '.$invoiceOrderSubscription->tableName.'.pkey,
            '.$invoiceOrderSubscription->tableName.'.grandtotal
          from 
            '.$invoiceOrderSubscription->tableName.'
          where  
            '. $invoiceOrderSubscription->tableName.'.refkey = '.$this->oDbCon->paramString($pkey) .' and   
            '. $invoiceOrderSubscription->tableName.'.statuskey in (2,3) ';
 
        //$this->setLog($sql,true);
        return $this->oDbCon->doQuery($sql);

    }
    

}

?>
