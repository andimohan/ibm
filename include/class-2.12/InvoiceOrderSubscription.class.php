<?php

class InvoiceOrderSubscription extends BaseClass{
	
    function __construct(){

        parent::__construct();

        $this->tableName = 'invoice_order_subscription_header';
        $this->tableNameDetail = 'invoice_order_subscription_detail';
        $this->tableSalesOrder = 'sales_order_subscription_header';
        $this->tablePayment = 'invoice_order_subscription_payment'; 
        $this->tableCustomer = 'customer';
        $this->tableEmployee = 'employee';
        $this->tableStatus = 'transaction_status';
        $this->tableWarehouse = 'warehouse';  
        $this->tableItem = 'item'; 	
        $this->tableMedia = 'media'; 	
        $this->tableLocation = 'location'; 	
        $this->tableItemUnit = 'item_unit'; 	   
        $this->tableJobDetails = 'job_details'; 	   
        $this->isTransaction = true;
        $this->securityObject = 'InvoiceOrderSubscription';

        $this->arrDataDetail = array();  
        $this->arrDataDetail['pkey'] = array('hidDetailKey');
        $this->arrDataDetail['refkey'] = array('pkey','ref');  
        $this->arrDataDetail['refsodetailkey'] = array('hidRefSODetailKey'); 
        $this->arrDataDetail['itemkey'] = array('hidItemKey', array('mandatory'=>true)); 
        $this->arrDataDetail['qty'] = array('qty','number', array('mandatory'=>true)); 
        $this->arrDataDetail['priceinunit'] = array('priceInUnit','number', array('mandatory'=>true));      
        $this->arrDataDetail['total'] = array('detailSubtotal','number'); 
		$this->arrDataDetail['description'] = array('description');

        $this->arrPaymentDetail = array(); 
        $this->arrPaymentDetail['pkey'] = array('hidDetailPaymentKey');
        $this->arrPaymentDetail['refkey'] = array('pkey', 'ref');
        $this->arrPaymentDetail['amount'] = array('paymentMethodValue',array('datatype' => 'number','mandatory'=>true));
        $this->arrPaymentDetail['paymentkey'] = array('selPaymentMethod',array('mandatory'=>true)); 


        $arrDetails = array(); 
        array_push($arrDetails, array('dataset' => $this->arrDataDetail, 'tableName' => $this->tableNameDetail));
        array_push($arrDetails, array('dataset' => $this->arrPaymentDetail, 'tableName' => $this->tablePayment));    

        $this->arrData = array(); 
        $this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails));  
        $this->arrData['code'] = array('code');
        $this->arrData['trdate'] = array('trDate','date');
        $this->arrData['customerkey'] = array('hidCustomerKey');
        $this->arrData['warehousekey'] = array('selWarehouseKey');
        //$this->arrData['employeekey'] = array('hidEmployeeKey');
        //$this->arrData['product'] = array('product');
        //$this->arrData['attention'] = array('attention');
        //$this->arrData['phone'] = array('phone');
        //$this->arrData['location'] = array('locationName');
        $this->arrData['refkey'] = array('hidSoKey');
        //$this->arrData['address'] = array('address');
        $this->arrData['trdesc'] = array('trDesc');
        $this->arrData['statuskey'] = array('selStatus');        
        $this->arrData['grandtotal'] = array('grandtotal','number');

        $this->arrData['subtotal'] = array('subtotal','number'); 
        $this->arrData['beforetaxtotal'] = array('beforeTaxTotal','number'); 
        $this->arrData['taxpercentage'] = array('taxPercentage','number');
        $this->arrData['taxvalue'] = array('taxValue','number'); 

        $this->arrData['ispriceincludetax'] = array('chkIncludeTax');
        $this->arrData['totalpayment'] = array('totalPayment','number'); 
        $this->arrData['balance'] = array('balance','number'); 
        $this->arrData['finaldiscounttype'] = array('selFinalDiscountType'); 
        $this->arrData['finaldiscount'] = array('finalDiscount','number'); 
        $this->arrData['termofpaymentkey'] = array('selTermOfPayment');   
        $this->arrData['companybankkey'] = array('selBank');   
		$this->arrData['revctr'] = array('revctr');
        //$this->arrData['jobdetailskey'] = array('selJobDetails');


        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 90));
        array_push($this->arrDataListAvailableColumn, array('code' => 'date','title' => 'date','dbfield' => 'trdate','default'=>true, 'width' => 90,  'align' => 'center', 'format' => 'date'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'salesOrder','title' => 'salesOrder','dbfield' => 'socode','default'=>true ));
        array_push($this->arrDataListAvailableColumn, array('code' => 'client','title' => 'customer','dbfield' => 'customername','default'=>true,'width' => 250));
        array_push($this->arrDataListAvailableColumn, array('code' => 'sid','title' => 'sid','dbfield' => 'sid','default'=>true, 'width' =>120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'total','title' => 'invoiceAmount','dbfield' => 'grandtotal','default'=>true, 'default'=>true,'align'=>'right','format'=>'integer', 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));

        $this->printMenu = array();
        array_push($this->printMenu,array('code' => 'printTransaction', 'name' => $this->lang['printTransaction'],  'icon' => 'print', 'url' => 'print/invoiceOrderSubscription'));
        $this->overwriteConfig();
        $this->includeClassDependencies(array(
			'SalesOrderSubscription.class.php',  
			'Warehouse.class.php',  
			'Customer.class.php', 
			'Location.class.php', 
			'Employee.class.php',
			'TermOfPayment.class.php',
			'PaymentMethod.class.php',
			'AR.class.php',
			'COALink.class.php',
			'GeneralJournal.class.php',
			'Item.class.php'
		));
    }

    function getQuery(){
        $sql = '
            SELECT
                '.$this->tableName.'.* ,  
                '.$this->tableSalesOrder.'.code as socode ,  
                '.$this->tableWarehouse.'.name as warehousename, 
                '.$this->tableCustomer.'.name as customername, 
                '.$this->tableCustomer.'.mediakey, 
                '.$this->tableCustomer.'.locationkey, 
                '.$this->tableCustomer.'.sid, 
                '.$this->tableCustomer.'.phone, 
                '.$this->tableCustomer.'.address, 
                '.$this->tableCustomer.'.attention, 
                '.$this->tableMedia.'.name as medianame, 
                '.$this->tableLocation.'.name as locationname, 
                '.$this->tableStatus.'.status as statusname
            FROM '.$this->tableStatus.',
                 '.$this->tableName.',
                 '.$this->tableSalesOrder.',
                 '.$this->tableCustomer.'
                    left join '. $this->tableEmployee.' employeesales on ' . $this->tableCustomer .'.saleskey = employeesales.pkey     
                    left join '.$this->tableMedia.' on  '.$this->tableCustomer.'.mediakey = '.$this->tableMedia.'.pkey 
                    left join '.$this->tableLocation.' on '.$this->tableCustomer.'.locationkey = '.$this->tableLocation.'.pkey,  
                 '.$this->tableWarehouse.' 
            WHERE 
                  '.$this->tableName.'.refkey = '.$this->tableSalesOrder.'.pkey and  
                  '.$this->tableName.'.customerkey = '.$this->tableCustomer.'.pkey and
                  '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey and
                  '.$this->tableName.'.warehousekey = '.$this->tableWarehouse.'.pkey 
            ' .$this->criteria ;
            
        $sql .=  $this->getWarehouseCriteria() ;
                             
        return $sql;
    }

 
    function reCountGrandTotal($arrParam){
        
        
            $isPriceIncludeTax = (!empty($arrParam['chkIncludeTax'])) ? 1 : 0; 

            $subtotal = 0 ;
            $grandtotal = 0;


            $arrItemKey = $arrParam['hidItemKey'];

            $taxPercentage = $this->unFormatNumber($arrParam['taxPercentage']); 
            $taxValue = $this->unFormatNumber($arrParam['taxValue']);  
            $finalDiscount = $this->unFormatNumber($arrParam['finalDiscount']); 
            $finalDiscountType = $arrParam['selFinalDiscountType']; 
        
            $arrQty = $arrParam['qty']; 
            $arrPriceinunit = $arrParam['priceInUnit']; 

            $arrItemDetail = array();
            $item = new Item();
            $totalProfit = 0;
        
            for ($i=0;$i<count($arrItemKey);$i++){

                if (empty($arrItemKey[$i])) continue; 

				$rsItem = $item->getDataRowById($arrItemKey[$i]);
				$qty =  $this->unFormatNumber($arrQty[$i]);
				$priceInUnit = $this->unFormatNumber($arrPriceinunit[$i]);
				$detailSubtotal = $qty * $priceInUnit ;
				$arrItemDetail[$i]['detailSubtotal'] = $detailSubtotal;
				$subtotal += $detailSubtotal ; 
            } 
        
            if ($finalDiscount != 0){
                if ($finalDiscountType == 2)
                    $finalDiscount = $finalDiscount/100 * $grandtotal;
            } 


            $beforeTaxTotal = $subtotal - $finalDiscount;
            $grandtotal = $beforeTaxTotal;
        
        
            if ($isPriceIncludeTax == false) {
                    $taxValue = $beforeTaxTotal * $taxPercentage / 100;
                    $grandtotal += $taxValue;
            }else{
                    $taxValue = ($taxPercentage/(100 + $taxPercentage)) * $grandtotal;   
                    $beforeTaxTotal = $grandtotal - $taxValue ;
            }
        
            $balance = 0;
            $totalPayment = 0; 

            $termOfPayment = new TermOfPayment();
            $rsTOP = $termOfPayment->getDataRowById($arrParam['selTermOfPayment']);  
            if ($rsTOP[0]['duedays'] == 0){ 
                $payment = $arrParam['paymentMethodValue'];
                for($i=0;$i<count($payment);$i++){
                    $totalPayment += $this->unFormatNumber($payment[$i]);
                } 
            } 
        
            $outstanding = $grandtotal;
            $balance = $totalPayment - $outstanding; 
        
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

    function validateForm($arr,$pkey = ''){ 
        $item = new Item();   
		$salesOrderSubscription = new SalesOrderSubscription(); 
        $arrayToJs = parent::validateForm($arr,$pkey); 
            
        $arrItemkey = $arr['hidItemKey']; 
        $arrQty = $arr['qty']; 
        $arrPriceinunit = $arr['priceInUnit'];
        $sokey = $arr['hidSoKey'];
        $customerkey = $arr['hidCustomerKey']; 
        
        $arrDetailKey = array();
        //validasi kalo status gk menunggu gk bisa edit 
        if (!empty($pkey)){
            $rs = $this->getDataRowById($pkey);
            if ($rs[0]['statuskey'] <> 1){
                $this->addErrorList($arrayToJs,false,$this->errorMsg[212]);
            }
        }
		
		if(empty($sokey)) 
            $this->addErrorList($arrayToJs,false,$this->errorMsg['salesOrderSubscription'][1]);
        else{
            $rsSO = $salesOrderSubscription->getDataRowById($sokey);
            if($rsSO[0]['statuskey']<>3)
                $this->addErrorList($arrayToJs,false,$this->errorMsg['salesOrderSubscription'][2]);
        }
        
        if(empty($customerkey)){ 
            $this->addErrorList($arrayToJs,false,$this->errorMsg['customer'][1]);
        }else{
            $customer = new Customer();
            $rsCustomer = $customer->getDataRowById($customerkey);
            if (empty($rsCustomer[0]['locationkey']))
                 $this->addErrorList($arrayToJs,false,$this->errorMsg['location'][1]);
        }
               
        if(empty($arrItemkey)) 
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
        }
        return $arrayToJs;
    }
  

    function getDetailWithRelatedInformation($pkey,$criteria=''){      
      $sql = 'select
            '.$this->tableNameDetail.'.*,
            '.$this->tableItem.'.name as itemname,
            '.$this->tableItemUnit.'.name as unitname,
            '.$this->tableItem.'.code as itemcode
        from
            '.$this->tableNameDetail.'
			left join '.$this->tableItemUnit.' on '.$this->tableNameDetail.'.unitkey = '.$this->tableItemUnit.'.pkey ,
            '.$this->tableItem.'
        where  
            '.$this->tableNameDetail .'.itemkey = '.$this->tableItem.'.pkey and
            '. $this->tableNameDetail.'.refkey in  ('.$this->oDbCon->paramString($pkey,',') . ') ' ;

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
            '.$this->tableNameDetail.',
            '.$this->tableItemUnit.',
            '.$this->tableItem.'
          where  
            '.$this->tableNameDetail .'.itemkey = '.$this->tableItem.'.pkey and
            '.$this->tableNameDetail .'.unitkey = '.$this->tableItemUnit.'.pkey and
            '. $this->tableNameDetail.'.refkey in  ('.$this->oDbCon->paramString($pkey,',') . ') ' ;

        $sql .= $criteria;
  
        return $this->oDbCon->doQuery($sql);

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
					'.$this->tableName . '.statuskey = '.$this->tableStatus.'.pkey  and '.$this->tableName.'.statuskey = 2
			';
        
          
         return $sql;
     }
    

    
    function normalizeParameter($arrParam, $trim=false){
        $arrParam['taxValue'] = (isset($arrParam['taxValue'])) ? $arrParam['taxValue'] : 0;
        $arrParam['taxPercentage'] = (isset($arrParam['taxPercentage'])) ? $arrParam['taxPercentage'] : 0;
        
        $termOfPayment = new TermOfPayment();
        $rsTOP = $termOfPayment->getDataRowById($arrParam['selTermOfPayment']);  
        if ($rsTOP[0]['duedays'] != 0){   
            for($i=0;$i<count( $arrParam['paymentMethodValue']);$i++){ 
                $arrParam['paymentMethodValue'][$i] = 0; 
                $arrParam['hidDetailPaymentKey'][$i] = 0;
            }
        }
        
        $arrItemkey = $arrParam['hidItemKey'];
        $reCountResult = $this->reCountGrandTotal($arrParam); 
        $arrParam['detailCOGS'] = $reCountResult['detailCOGS'];
        //$arrParam['profit'] =$reCountResult['profit'];
        $arrParam['subtotal'] = $reCountResult['subtotal'];
        $arrParam['beforeTaxTotal'] = $reCountResult['beforeTaxTotal'];
        $arrParam['isPriceIncludeTax'] = $reCountResult['isPriceIncludeTax'];
        $arrParam['grandtotal'] = $reCountResult['grandtotal'];
        $arrParam['totalPayment'] = $reCountResult['totalPayment'];
        $arrParam['balance'] = $reCountResult['balance'];
        
         for ($i=0;$i<count($arrItemkey);$i++)
  			$arrParam['detailSubtotal'][$i] = $arrParam['detailCOGS'][$i]['detailSubtotal'];
        
        $arrParam = parent::normalizeParameter($arrParam,true); 
        
        return $arrParam;
    }


    function afterStatusChanged($rsHeader){
		$rsHeader = $this->getDataRowById($rsHeader[0]['pkey']);
		if($rsHeader[0]['statuskey'] == 2)
			$this->changeStatus($rsHeader[0]['pkey'],3);
	}
    
        
    function validateConfirm($rsHeader){ 
        $id = $rsHeader[0]['pkey'];
		$salesOrderSubscription = new SalesOrderSubscription();
        $rsSO = $salesOrderSubscription->searchData($salesOrderSubscription->tableName.'.pkey',$rsHeader[0]['refkey'],true,' and '.$salesOrderSubscription->tableName.'.statuskey in(3) ');
		if(empty($rsSO))
		$this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg['salesOrderSubscription'][4]);

       
    } 

    function confirmTrans($rsHeader){
        $termOfPayment = new TermOfPayment();
        $warehouse = new Warehouse();
        $salesOrderSubscription = new SalesOrderSubscription();
		$rsSO = $salesOrderSubscription->searchData($salesOrderSubscription->tableName.'.pkey',$rsHeader[0]['refkey'],true,' and '.$salesOrderSubscription->tableName.'.statuskey in(3) ');
		$monthPeriod = 1;
		//$trDate = new DateTime($rsHeader[0]['trdate']);
		$trDate = $this->formatDBDate($rsHeader[0]['trdate'],'d / m / Y');
		if(strtotime($rsHeader[0]['trdate']) > strtotime($rsSO[0]['invoiceduedate'])){
			$sql = 'update '.$salesOrderSubscription->tableName.' set invoiceduedate = '.$this->oDbCon->paramDate($trDate,' / ').' where '.$salesOrderSubscription->tableName.'.pkey = ' . $this->oDbCon->paramString($rsHeader[0]['refkey']) ;   
			$this->oDbCon->execute($sql); 
		}
			

        $rsTOP = $termOfPayment->getDataRowById($rsHeader[0]['termofpaymentkey']);  
        $isCash = ($rsTOP[0]['duedays'] == 0) ? true : false;  
        if (!$isCash && $rsHeader[0]['grandtotal'] > 0){ 
            $ar = new AR(); 
            $customer = new Customer();

            $topkey = $rsHeader[0]['termofpaymentkey']; 
            $rsTOP = $termOfPayment->getDataRowById($topkey);    
            $top = (empty($rsTOP)) ? 0 : $rsTOP[0]['duedays'];

            $rsARKey = $ar->getTableKeyAndObj($this->tableName);  
            $arrParam = array();	

            $arrParam['code'] = 'xxxxxx';
            $arrParam['hidCustomerKey'] = $rsHeader[0]['customerkey']; 
            $arrParam['hidSalesKey'] = $rsHeader[0]['saleskey']; 
            $arrParam['hidRefKey'] = $rsHeader[0]['pkey'];
            $arrParam['hidRefHeaderKey'] = $rsHeader[0]['pkey'];
            $arrParam['hidRefCode'] =  $rsHeader[0]['code'];
            $arrParam['hidRefCode2'] =  $rsHeader[0]['donumber'];
            $arrParam['hidRefDate'] =   $this->formatDBDate($rsHeader[0]['trdate'],'d / m / Y'); 
            $arrParam['hidRefTable'] = $rsARKey['key'];
            $arrParam['amount'] = $rsHeader[0]['grandtotal'];
            $arrParam['trDesc'] = $rsHeader[0]['code'];
            $arrParam['trDate'] =  $this->formatDBDate($rsHeader[0]['trdate'],'d / m / Y');  
            $date = new DateTime($rsHeader[0]['trdate']);
            $date->add(new DateInterval('P'.$top.'D'));
            $arrParam['dueDate'] = $date->format('d / m / Y');// date ('d / m / Y', mktime(0, 0, 0, date("m")  , date("d")+$rsTOP[0]['duedays'], date("Y")));
            $arrParam['createdBy'] = 0;
            $arrParam['overwriteGL'] = 1;
            $arrParam['islinked'] = 1;
            $arrParam['selARType'] = AR_TYPE['serviceOrder'];
            $arrParam['selWarehouse'] = $rsHeader[0]['warehousekey'];//$warehouse->getDefaultData();

            $returnVal = $ar->addData($arrParam,false); 
            $rsHeader[0]['arKey'] = $returnVal[0]['data']['pkey'];
            $rsHeader[0]['arCode'] = $returnVal[0]['data']['code'];
        } 
            
    } 
    

    function validateCancel($rsHeader,$autoChangeStatus=false){
        $id = $rsHeader[0]['pkey'];
        $ar = new AR();
        $rsARKey = $ar->getTableKeyAndObj($this->tableName); 
        $rsAR = $ar->searchData('','',true,' and reftabletype = '.$this->oDbCon->paramString($rsARKey['key']).' and refkey = '.$this->oDbCon->paramString($id).' and ('.$ar->tableName.'.statuskey = 2 or '.$ar->tableName.'.statuskey = 3)');
		if(!empty($rsAR)) 
			$this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' . $this->errorMsg[201].' ' .$this->errorMsg['ar'][2],true);

    
    } 
     
	function cancelTrans($rsHeader,$copy){
		$id = $rsHeader[0]['pkey'];
        $rsDetail = $this->getDetailById($id);
        
        $ar = new AR();
        $rsObjKey = $this->getTableKeyAndObj($this->tableName); 
        
        $rsAR = $ar->searchData('','',true,' and reftabletype = '.$this->oDbCon->paramString($rsObjKey['key']).' and refkey = '.$this->oDbCon->paramString($id).' and '.$ar->tableName.'.statuskey = 1');
        for($i=0;$i<count($rsAR);$i++) { 
			$arrayToJs = $ar->changeStatus($rsAR[$i]['pkey'],4,'',false, true);
            if (!$arrayToJs[0]['valid'])
                throw new Exception('<strong>'.$rsHeader[0]['code'] . '</strong>. '.  $arrayToJs[0]['message']);    
        }
		
		$salesOrderSubscription = new SalesOrderSubscription();
		$rsSO = $salesOrderSubscription->searchData($salesOrderSubscription->tableName.'.pkey',$rsHeader[0]['refkey'],true,' and '.$salesOrderSubscription->tableName.'.statuskey in(3) ');
		$rsInvoice = $this->searchData($this->tableName.'.refkey',$rsHeader[0]['refkey'],true,' and '.$this->tableName.'.pkey <> '.$rsHeader[0]['pkey'].' and '.$this->tableName.'.statuskey in(2,3) order by '.$this->tableName.'.trdate desc limit 1');
		if($rsSO[0]['periodekey']==1){
			if(!empty($rsInvoice)){
				$invoiceDate = $this->formatDBDate($rsInvoice[0]['trdate'],'d / m / Y');
				$sql = 'update '.$salesOrderSubscription->tableName.' set invoiceduedate = '.$this->oDbCon->paramDate($invoiceDate,' / ').' where '.$salesOrderSubscription->tableName.'.pkey = ' . $this->oDbCon->paramString($rsHeader[0]['refkey']) ;   
				
				$this->oDbCon->execute($sql); 	
			}else{
				$sql = 'update '.$salesOrderSubscription->tableName.' set invoiceduedate = '.$this->oDbCon->paramDate(DEFAULT_EMPTY_DATE).' where '.$salesOrderSubscription->tableName.'.pkey = ' . $this->oDbCon->paramString($rsHeader[0]['refkey']) ;   
				$this->oDbCon->execute($sql);
			}
		}
			
		 
		if ($copy)
			$this->copyDataOnCancel($id);	  
		   
//        $this->cancelGLByRefkey($rsHeader[0]['pkey'],$this->tableName);
      
	}
    

}

?>