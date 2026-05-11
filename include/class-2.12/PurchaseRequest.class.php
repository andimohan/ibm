<?php
  
class PurchaseRequest extends BaseClass{ 
  
 
   function __construct(){
		
		parent::__construct();
       
		$this->tableName = 'purchase_request_header';
		$this->tableNameDetail = 'purchase_request_detail';
		$this->tableWarehouse = 'warehouse';
		$this->tableSupplier = 'supplier';
		$this->tableStatus = 'transaction_status';
		$this->tableItemUnit = 'item_unit'; 
		$this->tableHistory = 'history';
		$this->tableItem = 'item'; 	
		$this->isTransaction = true; 	
		   
		$this->securityObject = 'PurchaseRequest'; 
    
       
        $this->arrDataDetail = array();  
        $this->arrDataDetail['pkey'] = array('hidDetailKey');
        $this->arrDataDetail['refkey'] = array('pkey','ref');
        $this->arrDataDetail['itemkey'] = array('hidItemKey');
        $this->arrDataDetail['qty'] = array('qty','number');
        $this->arrDataDetail['priceinunit'] = array('priceInUnit','number');
        $this->arrDataDetail['subtotal'] = array('detailSubtotal','number');
        $this->arrDataDetail['unitkey'] = array('selUnit');
    
        $arrDetails = array();
        array_push($arrDetails, array('dataset' => $this->arrDataDetail));
       
        $this->arrData = array(); 
        $this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails));
        $this->arrData['code'] = array('code');
        $this->arrData['trdate'] = array('trDate','date');
        $this->arrData['warehousekey'] = array('selWarehouseKey');
        $this->arrData['supplierkey '] = array('hidSupplierKey');
        $this->arrData['trdesc'] = array('trDesc');
        $this->arrData['statuskey'] = array('selStatus');

        $this->printMenu = array();  
        array_push($this->printMenu,array('code' => 'printTransaction', 'name' => $this->lang['printTransaction'],  'icon' => 'print', 'url' => 'print/purchaseOrder'));
        array_push($this->printMenu,array('code' => 'printReceipt', 'name' => $this->lang['printReceipt'],  'icon' => 'print', 'url' => 'print/purchaseOrderDelivery'));
        
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 90));
        array_push($this->arrDataListAvailableColumn, array('code' => 'date','title' => 'date','dbfield' => 'trdate','default'=>true, 'width' => 100, 'align' =>'center','format' => 'date'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'warehouse','title' => 'warehouse','dbfield' => 'warehousename','default'=>true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'supplier','title' => 'supplier','dbfield' => 'suppliername','default'=>true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
        array_push($this->arrDataListAvailableColumn, array('code' => 'desc','title' => 'note','dbfield' => 'trdesc',  'width' => 200));
                
        $this->printMenu = array();
        array_push($this->printMenu,array('code' => 'printTransaction', 'name' => $this->lang['printTransaction'],  'icon' => 'print', 'url' => 'print/purchaseRequest'));
        
       
       $this->includeClassDependencies(array(

              'Item.class.php',
              'ItemUnit.class.php',
              'Supplier.class.php',
              'PurchaseOrder.class.php',

        ));  
         
        $this->overwriteConfig();
       
   }
    
    function getQuery(){
	   
	   $sql = '
			SELECT '.$this->tableName.'.* ,
			   '.$this->tableWarehouse.'.name as warehousename,
               '.$this->tableSupplier.'.name as suppliername,
			   '.$this->tableStatus.'.status as statusname 
			FROM 
                '.$this->tableStatus.', 
                '.$this->tableName.'
                    left join '.$this->tableSupplier.' on '.$this->tableName.'.supplierkey = '.$this->tableSupplier.'.pkey, 
                '.$this->tableWarehouse.'  
			WHERE '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey and 
				  '.$this->tableName.'.warehousekey = '.$this->tableWarehouse.'.pkey  
 		' .$this->criteria ;  
		  
        $sql .=  $this->getCompanyCriteria() ;
      
      return $sql;
    }
 
     function validateForm($arr,$pkey = ''){
		$item = new Item();  
		
		$arrayToJs = parent::validateForm($arr,$pkey); 
        
		$arrItemkey = $arr['hidItemKey']; 
		$arrQty = $arr['qty']; 
		$arrPriceinunit = $arr['priceInUnit'];
		 
		//validasi kalo status gk menunggu gk bisa edit 
		if (!empty($pkey)){
			$rs = $this->getDataRowById($pkey);
			if ($rs[0]['statuskey'] <> 1){
				$this->addErrorList($arrayToJs,false,$this->errorMsg[212]);
			}
		} 
        
        if(empty($arrItemkey)) 
            $this->addErrorList($arrayToJs,false,  $this->errorMsg[501]);  
 
        $arrDetailKeys = array(); 
         
		for($i=0;$i<count($arrItemkey);$i++) { 
			if (empty($arrItemkey[$i]) ){ 
				$this->addErrorList($arrayToJs,false, $this->errorMsg['item'][1]); 	
			} else{
       
                
                // cek ada detail double gk  
                if (in_array($arrItemkey[$i],$arrDetailKeys)){  
                    $rsItem = $item->getDataRowById($arrItemkey[$i]);
                    $this->addErrorList($arrayToJs,false, $rsItem[0]['name'].'. '.$this->errorMsg[215]); 	 
                }else{ 
                    array_push($arrDetailKeys, $arrItemkey[$i]);
                } 
            } 
             
		} 
 		  
		return $arrayToJs;
     }
	   
        
    function cancelTrans($rsHeader,$copy){  
		
		$id = $rsHeader[0]['pkey']; 
        
		$purchaseOrder = new PurchaseOrder();
		$rsPurchase= $purchaseOrder->searchData('','',true,' and '.$purchaseOrder->tableName.'.refkey = '.$this->oDbCon->paramString($id).' and '.$purchaseOrder->tableName.'.statuskey = 1');
		for($i=0;$i<count($rsPurchase);$i++) {
			$arrayToJs = $purchaseOrder->changeStatus($rsPurchase[$i]['pkey'],4,'',false,true);
            if (!$arrayToJs[0]['valid'])
                throw new Exception('<strong>'.$rsHeader[0]['code'] . '</strong>. '.  $arrayToJs[0]['message']);    
        }

		if ($copy)
			$this->copyDataOnCancel($id);	  
	 
	} 
 
    function validateCancel($rsHeader,$autoChangeStatus=false){ 
		$id = $rsHeader[0]['pkey'];
 
         
		$purchaseOrder = new PurchaseOrder();
		$rsPurchase = $purchaseOrder->searchData('','',true,' and '.$purchaseOrder->tableName.'.refkey = '.$this->oDbCon->paramString($id).' and ('.$purchaseOrder->tableName.'.statuskey in (2,3))');
		if(!empty($rsPurchase))  
			$this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' . $this->errorMsg[201].' ' .$this->errorMsg['purchaseOrder'][2]);

		 
	 }
    
	 
	function getDetailWithRelatedInformation($pkey,$criteria = ''){
        
	   $sql = 'select
	   			'.$this->tableNameDetail.'.*, 
                '.$this->tableItem.'.name as itemname, 
                '.$this->tableItem.'.code as itemcode,
                '.$this->tableItem.'.deftransunitkey,
                '.$this->tableItemUnit.'.name as unitname,
                 baseunit.name as baseunitname
			  from
			  	'.$this->tableNameDetail.',
                '.$this->tableItem.',
                '.$this->tableItemUnit.',
                '.$this->tableItemUnit.' baseunit
			  where
			  	'.$this->tableNameDetail.'.itemkey = '.$this->tableItem.'.pkey and
			  	'.$this->tableNameDetail.'.unitkey = '.$this->tableItemUnit.'.pkey and
			  	'.$this->tableItem.'.baseunitkey = baseunit.pkey and
			  	refkey = '.$this->oDbCon->paramString($pkey);
        
        $sql .= $criteria;
              
		return $this->oDbCon->doQuery($sql);
	
   }
     
}
?>
