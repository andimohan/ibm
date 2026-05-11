<?php

class ARDiscountApproval extends BaseClass{
  
   function __construct(){
		
		parent::__construct();
		
		$this->tableName = 'ar_discount_approval_header';
		$this->tableNameDetail = 'ar_discount_approval_detail';
	    $this->tableCost = 'ar_credit_note_cost';
		$this->tableCustomer = 'customer';
		$this->tableStatus = 'transaction_status';
		$this->tableWarehouse = 'warehouse'; 
		$this->tableARPayment= 'ar_payment_header';
        $this->tableItem = 'cost_cash_out';
		$this->tableAR = 'ar';
        $this->tableCurrency = 'currency';
        $this->isTransaction = true;
       
        $this->tableNeedToBeCopyOnCancel = array($this->tableNameDetail,$this->tableCost);
		 
		$this->securityObject = 'ARDiscountApproval';
       
        $this->arrDataDetail = array(); 
        $this->arrDataDetail['pkey'] = array('hidDetailKey');
        $this->arrDataDetail['refkey'] = array('pkey','ref');
        $this->arrDataDetail['arkey'] = array('hidARKey');
        $this->arrDataDetail['refdetailkey'] = array('hidARPaymentDetailKey');
        $this->arrDataDetail['outstanding'] = array('outstanding','number');
        $this->arrDataDetail['amount'] = array('amount', 'number');
        $this->arrDataDetail['discount'] = array('discount',array('datatype' => 'number','mandatory'=>true));
       
        $arrCostDetail = array(); 
        $arrCostDetail['pkey'] = array('hidDetailCostKey');
        $arrCostDetail['refkey'] = array('pkey', 'ref');
        $arrCostDetail['refdetailkey'] = array('hidCostARDetailKey');
        $arrCostDetail['amount'] = array('costAmount',array('datatype' => 'number','mandatory'=>true));
        $arrCostDetail['costkey'] = array('hidCostKey',array('mandatory'=>true)); 
	   
        $arrDetails = array();
        array_push($arrDetails, array('dataset' => $this->arrDataDetail));
     	array_push($arrDetails, array('dataset' => $arrCostDetail, 'tableName' => $this->tableCost));
          
        $this->arrData = array(); 
        $this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails));
        $this->arrData['code'] = array('code');
        $this->arrData['refcode'] = array('refHeaderCode');
        $this->arrData['trdate'] = array('trDate','date');
        $this->arrData['customerkey'] = array('hidCustomerKey');
        $this->arrData['currencykey'] = array('selCurrency');
        $this->arrData['warehousekey'] = array('selWarehouseKey');
        $this->arrData['trnotes'] = array('trDesc');
        $this->arrData['statuskey'] = array('selStatus');
        $this->arrData['islinked'] = array('islinked');
        $this->arrData['refkey'] = array('hidARPaymentKey');
        $this->arrData['totalcost'] = array('totalCost','number');
        $this->arrData['totaldiscount'] = array('totalDiscount','number');
       
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'date','title' => 'date','dbfield' => 'trdate','default'=>true, 'width' => 100, 'align' =>'center', 'format' => 'date'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'warehouse','title' => 'warehouse','dbfield' => 'warehousename', 'default'=>true, 'width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'customer','title' => 'customer','dbfield' => 'customername', 'default'=>true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'paymentCode','title' => 'paymentCode','dbfield' => 'arpaymentcode', 'default'=>true, 'width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'currency','title' => 'curr','dbfield' => 'currencyname', 'default'=>true, 'width' => 60,  'align' =>'center'));
	   	array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
        array_push($this->arrDataListAvailableColumn, array('code' => 'desc','title' => 'note','dbfield' => 'trnotes',  'width' => 250)); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'refCode','title' => 'refCode','dbfield' => 'refcode', 'width' => 100));    
 
       
        array_push($this->filterCriteria, array('title' => $this->lang['warehouse'], 'field' => 'warehousekey'));
       
        $this->printMenu = array();
        array_push($this->printMenu,array('code' => 'printTransaction', 'name' => $this->lang['printTransaction'],  'icon' => 'print', 'url' => 'print/arDiscountApproval'));
          
        $this->includeClassDependencies(array(
                  'AR.class.php', 
                  'ARPayment.class.php', 
                  'Currency.class.php', 
                  'Customer.class.php', 
                  'Item.class.php', 
                  'CostCashOut.class.php', 
                  'Service.class.php', 
                  'Warehouse.class.php'
            ));  
       
        $this->overwriteConfig();
	}
	
	function getQuery(){
		
		$sql = '
			SELECT '.$this->tableName.'.* ,
			   '.$this->tableCustomer.'.name as customername,
			   '.$this->tableCustomer.'.taxid as customertaxid,
			   '.$this->tableWarehouse.'.name as warehousename,
			   '.$this->tableStatus.'.status as statusname,
			   '.$this->tableARPayment.'.code as arpaymentcode,
               '.$this->tableCurrency.'.name as currencyname
			FROM '.$this->tableStatus.',
                 '.$this->tableCustomer.', 
                  '.$this->tableName.', 
                  '.$this->tableWarehouse.',
                  '.$this->tableARPayment.',
                  '.$this->tableCurrency.'
			WHERE '.$this->tableName.'.customerkey = '.$this->tableCustomer.'.pkey and
				  '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey and
                  '.$this->tableName.'.currencykey = '.$this->tableCurrency.'.pkey   and
                  '.$this->tableName.'.refkey = '.$this->tableARPayment.'.pkey   and
				  '.$this->tableName.'.warehousekey = '.$this->tableWarehouse.'.pkey  
		' .$this->criteria ;
        
        $sql .=  $this->getWarehouseCriteria() ;
        $sql .=  $this->getCustomerCriteria() ;
        
        return $sql;
	}
	 
	function validateForm($arr,$pkey = ''){
          
        $ARObj = $this->getARObj();
		$arPayment = new ARPayment();
		$arrayToJs = parent::validateForm($arr,$pkey);  
		$arPaymentkey = $arr['hidARPaymentKey'];  

        $rsPayment = $arPayment->getDataRowById($arPaymentkey);
        if(empty($rsPayment))
			$this->addErrorList($arrayToJs,false,$this->errorMsg['arPayment'][1]);
		 
		
		return $arrayToJs;
    }
	     
    function afterStatusChanged($rsHeader){ 
        $rsHeader = $this->getDataRowById($rsHeader[0]['pkey']);
        if ($rsHeader[0]['statuskey'] == 2)
            $this->changeStatus($rsHeader[0]['pkey'],3); 
    }
    
	/*function validateConfirm($rsHeader){
		$id = $rsHeader[0]['pkey'];
        $ar = $this->getARObj();
        
    }
	 
	function confirmTrans($rsHeader){
		$id = $rsHeader[0]['pkey']; 
		
	}

    function validateCancel($rsHeader,$autoChangeStatus = false){ 
            if ( !$autoChangeStatus ) {
                if(isset($rsHeader[0]['islinked']) && !empty($rsHeader[0]['islinked']))
                    $this->addErrorLog(false, '<strong>'.$rsHeader[0]['code'].'.</strong> '.$this->errorMsg[900],true);  
            }  
            $id = $rsHeader[0]['pkey'];

    } 

    function cancelTrans($rsHeader,$copy){ 

        $id = $rsHeader[0]['pkey'];   

        $rsARKey = $this->getTableKeyAndObj($this->tableName,array('key')); 
 
        if ($copy)
            $this->copyDataOnCancel($id);	  

    }
      */
    
	function getDetailPaymentByARKey($arkey,$criteria = ''){
		$sql = 'select 
                    '. $this->tableName.'.code,  
                    '. $this->tableName.'.refcode,  
                    '. $this->tableName.'.trdate,   
					'. $this->tableNameDetail.'.*,
					'. $this->tableCurrency.'.name as currencyname
				from 
					'. $this->tableNameDetail.',
					'. $this->tableCurrency.',
                    '. $this->tableName.'  
				where 
					'. $this->tableNameDetail.'.refkey = '. $this->tableName.'  .pkey and
					'. $this->tableCurrency.'.pkey = '. $this->tableName.'  .currencykey and
					'. $this->tableNameDetail.'.arkey in (' .$this->oDbCon->paramString($arkey,',').') and
				    ('. $this->tableName.'.statuskey = 2 or '. $this->tableName.'.statuskey = 3) ';
        
        if(!empty($criteria))
            $sql .= $criteria;   
        
        $sql .= ' order by  pkey asc'; 
					  
		return $this->oDbCon->doQuery($sql);
	} 

    function getARObj(){
        return new AR();
    }
	
	function reCountGrandtotal($arrParam){
		$arrARkey = $arrParam['hidARKey'];
		$arrDiscount = $arrParam['discount'];
		$costAmount = $arrParam['costAmount'];
		
		$totalDiscount = 0;
		for ($i=0;$i<count($arrARkey);$i++){ 
			$totalDiscount += $this->unFormatNumber($arrDiscount[$i]);    
		}  
		
		$totalCost = 0;  
		for($i=0;$i<count($costAmount);$i++){
			$totalCost += $this->unFormatNumber($costAmount[$i]);
		} 

		$reCountResult = array();
		$reCountResult['totalDiscount'] = $totalDiscount;
		$reCountResult['totalCost'] = $totalCost;
 
		return $reCountResult;
				
	}
    
    function normalizeParameter($arrParam, $trim = false){   
         
		
		$arrParam['costAmount'] = (!empty($arrParam['costAmount'])) ? $arrParam['costAmount'] : array();
  	    $arrParam['hidCostKey'] = (!empty($arrParam['hidCostKey'])) ? $arrParam['hidCostKey'] : array();
        $arrParam['selCurrency'] = (!empty($arrParam['selCurrency'])) ? $arrParam['selCurrency'] : CURRENCY['idr'];
        $arrParam['islinked'] = (!empty($arrParam['islinked'])) ? $arrParam['islinked'] : 0;
		
		$reCountResult = $this->reCountGrandtotal($arrParam);
		$arrParam['totalDiscount'] = $reCountResult['totalDiscount'];
        $arrParam['totalCost'] = $reCountResult['totalCost'];
		
        $arrParam = parent::normalizeParameter($arrParam,true);
        
        return $arrParam;
    }
	
    function getCostDetail($pkey){
		$sql = 'select 
					'. $this->tableCost.'.* ,
					'. $this->tableItem.'.name as costname
				from 
					'. $this->tableCost.',
                    '.$this->tableItem.', 
                    '. $this->tableName.'  
				where 
					'. $this->tableCost.'.refkey = '. $this->tableName.'  .pkey and
                     '.$this->tableCost.'.costkey = '.$this->tableItem.'.pkey  and
					'. $this->tableName.'.pkey = ' .$this->oDbCon->paramString($pkey).'
				order by  pkey asc'; 
        
     	return $this->oDbCon->doQuery($sql);
	}    
    
    
    function getDetailWithRelatedInformation($pkey,$criteria=''){
      $arObj = $this->getARObj();
        
      $sql = 'select
	   			'.$this->tableNameDetail .'.*,
                '.$arObj->tableName.'.code as arcode ,
                '.$arObj->tableName.'.refcode  ,
                '.$arObj->tableName.'.refdate
			  from
			  	'. $this->tableNameDetail .',
                '.$arObj->tableName.' 
			  where
			  	'. $this->tableNameDetail .'.arkey = '.$arObj->tableName.'.pkey and
			  	'. $this->tableNameDetail .'.refkey in('.$this->oDbCon->paramString($pkey,',').') ';
         
       
        $sql .= $criteria; 
   
        return $this->oDbCon->doQuery($sql);
   } 
     
    /*function getDetailPaymentCollections($rs,$indexField,$criteria=''){ 
        $rsAllDetail = $this->getDetailPaymentByARKey(array_column($rs,'pkey'),$criteria);    
        return $this->reindexDetailCollections($rsAllDetail,$indexField);
    }
*/     
}
?>
