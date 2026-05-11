<?php
  
class SalesCarServiceReturn extends BaseClass{ 
  
 
   function __construct(){
		
		parent::__construct();
       
		$this->tableName = 'sales_car_service_return_header';
		$this->tableNameDetail = 'sales_car_service_return_detail';
        $this->tablePayment = 'sales_car_service_return_payment'; 
        $this->tableSalesOrderHeader = 'sales_order_car_service_header';
		$this->tableWarehouse = 'warehouse';
		$this->tableStatus = 'transaction_status'; 
		$this->tableCustomer = 'customer';
        $this->tableSalesCarService = 'sales_order_car_service_header';
        $this->tableSalesCarServiceDetail = 'sales_order_car_service_detail';
		$this->tableItemUnit = 'item_unit'; 
        $this->tableItem = 'item'; 	
        $this->tableNeedToBeCopyOnCancel = array($this->tableNameDetail);
       
		$this->securityObject = 'SalesCarServiceReturn'; 
        $this->isTransaction = true;
       
        $this->arrDataDetail = array(); 
        $this->arrDataDetail['pkey'] = array('hidDetailKey');
        $this->arrDataDetail['refsodetailkey'] = array('hidSODetailKey');   
        $this->arrDataDetail['refkey'] = array('pkey','ref');
        $this->arrDataDetail['itemkey'] = array('hidItemKey');
        $this->arrDataDetail['qty'] = array('qty','number'); 
        $this->arrDataDetail['qtyinbaseunit'] = array('qty','number');
        $this->arrDataDetail['unitkey'] = array('selUnit');
        $this->arrDataDetail['unitconvmultiplier'] = array('unitConvMultiplier','number');
        $this->arrDataDetail['priceinunit'] = array('priceInUnit','number');  
        $this->arrDataDetail['total'] = array('detailSubtotal','number');
        
        $this->arrPaymentDetail = array(); 
        $this->arrPaymentDetail['pkey'] = array('hidDetailPaymentKey');
        $this->arrPaymentDetail['refkey'] = array('pkey', 'ref');
        $this->arrPaymentDetail['amount'] = array('paymentMethodValue',array('datatype' => 'number','mandatory'=>true));
        $this->arrPaymentDetail['paymentkey'] = array('selPaymentMethod',array('mandatory'=>true)); 

        $arrDetails = array();
        array_push($arrDetails, array('dataset' => $this->arrDataDetail));
        array_push($arrDetails, array('dataset' => $this->arrPaymentDetail, 'tableName' => $this->tablePayment));

        $this->arrData = array(); 
        $this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails));
        $this->arrData['refkey'] = array('hidRefKey'); 
        $this->arrData['code'] = array('code');
        $this->arrData['trdate'] = array('trDate','date');
        $this->arrData['customerkey'] = array('hidCustomerKey'); 
        $this->arrData['trdesc'] = array('trDesc');
        $this->arrData['statuskey'] = array('selStatus');   
        $this->arrData['warehousekey'] = array('selWarehouseKey');
        $this->arrData['totalpayment'] = array('totalPayment','number');
        $this->arrData['grandtotal'] = array('total','number');
        $this->arrData['balance'] = array('balance','number'); 
        $this->arrData['termofpaymentkey'] = array('selTermOfPaymentKey');
		 
   }
    
   function getQuery(){
	   
	   return '
			SELECT '.$this->tableName.'.* ,
			   '.$this->tableWarehouse.'.name as warehousename,
			   '.$this->tableStatus.'.status as statusname,
               '.$this->tableCustomer.'.name as customername,
               '.$this->tableSalesCarService.'.code as refcode
			FROM '.$this->tableStatus.', 
                 '.$this->tableWarehouse.'  , 
                 '.$this->tableSalesCarService.',
                 '.$this->tableName.' 
                    left join '.$this->tableCustomer.' on '.$this->tableName.'.customerkey = '.$this->tableCustomer.' .pkey
			WHERE '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey 
            and  '.$this->tableName.'.warehousekey = '.$this->tableWarehouse.'.pkey
            and  '.$this->tableName.'.refkey = '.$this->tableSalesCarService.'.pkey
 		' .$this->criteria ; 
		 
    }
	     
     function validateForm($arr,$pkey = ''){
		  
		$item = new Item();   
        $salesOrderCarService = new SalesOrderCarService();
		  
		$arrayToJs = parent::validateForm($arr,$pkey); 
         
        $refkey = $arr['hidRefKey']; 
		$arrItemkey = $arr['hidItemKey']; 
		$arrDetailSOkey = $arr['hidSODetailKey']; 
		$arrQty = $arr['qty'];  
		 
        if(empty($refkey)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['reference'][1]);
		} 
		  
        $rsSODetail = $salesOrderCarService->getDetailById($refkey);
        $rsSODetail = array_column($rsSODetail,null,'pkey');
            
		for($i=0;$i<count($arrItemkey);$i++) {
            
            $rsItem = $item->getDataRowById($arrItemkey[$i]);
            
			if (empty($arrItemkey[$i]) ){ 
				$this->addErrorList($arrayToJs,false, $this->errorMsg['item'][1]); 	
			} 
			if (!empty($arrItemkey[$i]) && $this->unFormatNumber($arrQty[$i]) <= 0){
				$this->addErrorList($arrayToJs,false,$rsItem[0]['name']. '. ' . $this->errorMsg[500]); 
			}
            
			if ( $this->unFormatNumber($arrQty[$i]) > $rsSODetail[$arrDetailSOkey[$i]]['qty'] ){ 
				$this->addErrorList($arrayToJs,false,$rsItem[0]['name']. '. Jumlah retur tidak boleh lebih besar dari penjualan' ); 
			}
		}
		  
		
		return $arrayToJs;
	 }

    function validateConfirm($rsHeader){
        $id = $rsHeader[0]['pkey'];
        
        $warehouse = new Warehouse();  
        $coaLink = new COALink();
        $salesOrder = new SalesOrderCarService();
 
        $rsDetail = $this->getDetailById($id);
        $rsPayment = $this->getPaymentMethodDetail($id); 

        $termOfPayment = new TermOfPayment();
        $rsTOP = $termOfPayment->getDataRowById($rsHeader[0]['termofpaymentkey']);  
        $isCash = ($rsTOP[0]['duedays'] == 0) ? true : false;  
 
        $totalPayment = 0; 
        for($i=0;$i<count($rsPayment); $i++)
            $totalPayment += $rsPayment[$i]['amount'];

        $balance = $totalPayment - $rsHeader[0]['grandtotal'];   
 
        // cek status SO harus konfirmasi atau selesai
        $rsSalesOrder = $salesOrder->searchData($salesOrder->tableName.'.pkey', $rsHeader[0]['refkey'],true,' and '.$salesOrder->tableName.'.statuskey in (2,3)');
        if (empty($rsSalesOrder)){
            $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. Penjualan tidak ditemukan'); 
        }
             
        if ($isCash){ 
            $thresholdDiscount = abs($this->loadSetting('roundedPaymentThreshold'));
            if($balance < ($thresholdDiscount * -1)) 
                $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg[502]);
            else if ($balance > $thresholdDiscount)
                $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg[509]); 
        }
 
 
    }	

/*	function confirmTrans($rsHeader){
		$itemMovement = new ItemMovement();  
		$item = new Item();
		
		$note = $rsHeader[0]['code'] .'. Retur Penjualan';
		$warehouse = new Warehouse();
		$rsWarehouse = $warehouse->getDataRowById($rsHeader[0]['warehousekey']);
	 	$rsDetail = $this->getDetailById($rsHeader[0]['pkey']); 
		
		for($i=0;$i<count($rsDetail); $i++){	 
		   $rsItem = $item->getDataRowById($rsDetail[$i]['itemkey']);
		   $itemMovement->updateItemMovement($id,$rsDetail[$i]['itemkey'],$rsDetail[$i]['qtyinbaseunit'], $rsItem[0]['cogs'] ,$this->tableName, $rsHeader[0]['warehousekey'], $note,$rsHeader[0]['trdate']);
		}	 
		
	} */
	
	/*function cancelTrans($rsHeader,$copy){   	
        $id = $rsHeader[0]['pkey'];
		$itemMovement = new ItemMovement();  
		$itemMovement->cancelMovement($id,$this->tableName);
		 
		if ($copy)
			$this->copyDataOnCancel($id);	  
		
	} */
    
    function normalizeParameter($arrParam, $trim = false){ 
          
            $item = new Item();
            $salesOrder = new SalesOrderCarService();
                
            $arrParam = parent::normalizeParameter($arrParam); 

            $arrParam['hidRefKey'] = (isset($arrParam['hidRefKey'])) ? $arrParam['hidRefKey'] : '';
            $sokey = $arrParam['hidRefKey'];
        
            $arrItemkey = $arrParam['hidItemKey'];
        
            $rsSalesOrder = $salesOrder->getDataRowById($sokey); 
        
            // customerkey
            $arrParam['hidCustomerKey'] = $rsSalesOrder[0]['customerkey'];
    

        return $arrParam;
    }
    
   function getDetailWithRelatedInformation($pkey,$criteria=''){ 

        $sql = 'select
                '.$this->tableNameDetail .'.*, 
                '.$this->tableItem.'.name as itemname, 
                '.$this->tableItem.'.code as itemcode,   
                '.$this->tableSalesCarServiceDetail.'.qty as orderedqty,   
                '.$this->tableItemUnit.'.name as unitname 
              from
                '.$this->tableNameDetail .',
                '.$this->tableItemUnit.', 
                '.$this->tableSalesCarServiceDetail.', 
                '.$this->tableItem.' 
              where
                '.$this->tableNameDetail.'.itemkey = '.$this->tableItem.'.pkey and
                '.$this->tableNameDetail.'.unitkey = '.$this->tableItemUnit.'.pkey and 
                '.$this->tableNameDetail.'.refsodetailkey = '.$this->tableSalesCarServiceDetail.'.pkey and 
                '.$this->tableNameDetail.'.refkey = '.$this->oDbCon->paramString($pkey) . ' ';

        $sql .= $criteria;

        return $this->oDbCon->doQuery($sql);

    } 
}
?>