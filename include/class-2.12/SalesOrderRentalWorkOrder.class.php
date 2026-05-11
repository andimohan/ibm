<?php
  
class SalesOrderRentalWorkOrder extends BaseClass{ 
  

    function __construct(){

            parent::__construct();

            $this->tableName = 'sales_order_rental_work_order_header';
            $this->tableNameDetail = 'sales_order_rental_work_order_detail';
            $this->tableSalesOrder = 'sales_order_rental_header';
            $this->tableCustomer = 'customer';
            $this->tableCity = 'city';
            $this->tableEmployee = 'employee';
            $this->tableWarehouse = 'warehouse'; 
            $this->tableStatus = 'transaction_status';
            $this->tableMovement = 'item_movement'; 
            $this->tableHistory = 'history';
            $this->tableItem = 'item'; 		
            $this->tableItemUnit = 'item_unit'; 	
            $this->isTransaction = true; 		

            //$this->autoPrintURL = 'print/salesOrder';
         
            $this->securityObject = 'SalesOrderRentalWorkOrder';   

            $this->arrDataDetail = array();  
            $this->arrDataDetail['pkey'] = array('hidDetailKey');
            $this->arrDataDetail['refkey'] = array('pkey','ref'); 
            $this->arrDataDetail['refsodetailkey'] = array('hidRefSODetailKey'); 
            $this->arrDataDetail['itemkey'] = array('hidItemKey'); 
            $this->arrDataDetail['qty'] = array('qty','number'); 
            $this->arrDataDetail['qtyinbaseunit'] = array('qtyInBaseUnit','number');
            $this->arrDataDetail['unitkey'] = array('selUnit');
            $this->arrDataDetail['baseunitkey'] = array('baseUnitKey');
            $this->arrDataDetail['unitconvmultiplier'] = array('unitConvMultiplier','number'); 

            $arrDetails = array();
            array_push($arrDetails, array('dataset' => $this->arrDataDetail));

            $this->arrData = array(); 
            $this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails)); 
            $this->arrData['code'] = array('code');
            $this->arrData['locationkey'] = array('hidLocationKey');  
            $this->arrData['trdate'] = array('trDate','date');
            $this->arrData['invoicedate'] = array('invoiceDate','date');
            $this->arrData['warehousekey'] = array('selWarehouseKey');
            $this->arrData['refkey'] = array('hidSalesOrderKey');  
            $this->arrData['customerkey'] = array('hidRecipientKey');  
            $this->arrData['trdesc'] = array('trDesc'); 
            $this->arrData['statuskey'] = array('selStatus'); 
          
            $this->arrDataListAvailableColumn = array(); 
            array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 100));
            array_push($this->arrDataListAvailableColumn, array('code' => 'date','title' => 'date','dbfield' => 'trdate','default'=>true, 'width' => 100, 'align'=>'center', 'format' => 'date'));
            array_push($this->arrDataListAvailableColumn, array('code' => 'warehouse','title' => 'warehouse','dbfield' => 'warehousename','default'=>true, 'width' => 100));
            array_push($this->arrDataListAvailableColumn, array('code' => 'refcode','title' => 'refCode','dbfield' => 'refcode','default'=>true, 'width' => 100));
            array_push($this->arrDataListAvailableColumn, array('code' => 'recipient','title' => 'name','dbfield' => 'recipientname','default'=>true, 'width' => 200));
            array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
            array_push($this->arrDataListAvailableColumn, array('code' => 'desc','title' => 'note','dbfield' => 'trdesc', 'width' => 200));
     
			$this->includeClassDependencies(array(
                   'Warehouse.class.php',  
                   'City.class.php', 
                   'Customer.class.php', 
                   'Item.class.php', 
                   'Service.class.php', 
				   'SalesOrderRental.class.php',
                   'ItemMovement.class.php',  
                   'ItemUnit.class.php',
                   'Location.class.php',
                   'GeneralJournal.class.php' ,
                   'ItemIn.class.php' 
            )); 

            array_push($this->filterCriteria, array('title' => $this->lang['warehouse'], 'field' => 'warehousekey'));
        
    }
 
            
    
    function getQuery(){

        $sql = '
            SELECT '.$this->tableName.'.* ,
               '.$this->tableCustomer.'.name as customername,
               '.$this->tableWarehouse.'.name as warehousename,
               '.$this->tableSalesOrder.'.code as refcode ,
               '.$this->tableSalesOrder.'.recipientname ,
               '.$this->tableSalesOrder.'.recipientphone ,
               '.$this->tableSalesOrder.'.recipientemail ,
               '.$this->tableSalesOrder.'.recipientaddress ,
               '.$this->tableStatus.'.status as statusname 
            FROM 
                '.$this->tableStatus.', 
                '.$this->tableSalesOrder.' 
                left join '.$this->tableCustomer.' on  
                    '.$this->tableSalesOrder.'.customerkey = '.$this->tableCustomer.'.pkey,
                '.$this->tableWarehouse.',
                '.$this->tableName.' 
            WHERE '.$this->tableName.'.refkey = '.$this->tableSalesOrder.'.pkey and
                     '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey and
                     '.$this->tableName.'.warehousekey = '.$this->tableWarehouse.'.pkey 
        ' .$this->criteria ; 


        $sql .=  $this->getWarehouseCriteria() ;
        $sql .=  $this->getCompanyCriteria() ;

        return $sql;
    }  
 
      
    function editData($arrParam){ 
        // kalo edit, marketplacenya jgn diupdate, kalo gk jd 0
        
		unset($this->arrData['marketplacekey']);  
        return parent::editData($arrParam);
	}
     
    function afterStatusChanged($rsHeader){  
        $salesOrderRental = new SalesOrderRental();
        $salesOrderRental->updateSalesOrderDeliveredItem($rsHeader[0]['refkey']);
        
        // retrieve latest status
        $rsHeader = $this->getDataRowById($rsHeader[0]['pkey']);
        if ($rsHeader[0]['statuskey'] == 2)
            $this->changeStatus($rsHeader[0]['pkey'],3); 
    }    

    function reCountSubtotal($arrParam){
 
          
            $arrItemKey = $arrParam['hidItemKey'];  
 
            $arrQty = $arrParam['qty']; 
            $arrTransUnitKey = $arrParam['selUnit']; 

            $arrItemDetail = array();
            $item = new Item();
            $totalGramasi = 0;
        
            for ($i=0;$i<count($arrItemKey);$i++){

                if (empty($arrItemKey[$i]))   continue; 

                    $rsItem = $item->getDataRowById($arrItemKey[$i]);
 
                    $itemkey = $arrItemKey[$i];
                    $transactionUnitKey = $arrTransUnitKey[$i];
                    $baseunitkey = $rsItem[0]['baseunitkey']; 
                    $qty =  $this->unFormatNumber($arrQty[$i]);
                    $conversionMultiplier = $item->getConvMultiplier($itemkey,$transactionUnitKey,$baseunitkey); 
                    $qtyinbaseunit = $qty * $conversionMultiplier;
 
                    /*$gramasi = $rsItem[0]['gramasi'];
                    if ($rsItem[0]['weightunitkey'] == UNIT['kg'])
                        $gramasi *= 1000;*/
                
                    $arrItemDetail[$i]['baseUnitKey'] = $baseunitkey;
                    $arrItemDetail[$i]['unitConvMultiplier'] = $conversionMultiplier;
                    $arrItemDetail[$i]['qtyInBaseUnit'] = $qtyinbaseunit ; 
                    //$arrItemDetail[$i]['weight'] = $gramasi ; 
   
				    //$arrItemDetail[$i]['itemType'] = $rsItem[0]['itemtype']; 

                
                
                   // $totalGramasi += ($qty * $gramasi);
            } 

       
 
            $reCountResult = array(); 
            $reCountResult['detailCOGS'] = $arrItemDetail; 

            return $reCountResult;

    } 
   

    function validateForm($arr,$pkey = ''){
            $item = new Item();   
            $salesOrderRental = new SalesOrderRental();   

            $arrayToJs = parent::validateForm($arr,$pkey); 
 
            $arrItemkey = $arr['hidItemKey']; 
            $arrQty = $arr['qty']; 
            $arrQtyInBaseUnit = $this->unFormatNumber($arr['qtyInBaseUnit']); 
            $arrSelUnit = $arr['selUnit']; 
            $salesOrderKey = $arr['hidSalesOrderKey']; 

            //validasi kalo status gk menunggu gk bisa edit 
            if (!empty($pkey)){
                $rs = $this->getDataRowById($pkey);
                if ($rs[0]['statuskey'] <> 1){
                    $this->addErrorList($arrayToJs,false,$this->errorMsg[212]);
                }
            } 
        
           
            $rsSO = $salesOrderRental->getDataRowById($salesOrderKey);
            if(empty($rsSO)){
                $this->addErrorList($arrayToJs,false,$this->errorMsg['salesOrder'][1]);
            }else{
                $salesOrderRental = new SalesOrderRental();
                $rsRental = $salesOrderRental->searchData('','',true,' and '.$salesOrderRental->tableName.'.pkey = '.$this->oDbCon->paramString($salesOrderKey));
                
                $rsRentalDetail = $salesOrderRental->getDetailWithRelatedInformation($rsRental[0]['pkey']);
                $arrRentalItemKey = array_column($rsRentalDetail, 'itemkey');
                $arrRentalItemQty = array_column($rsRentalDetail,'qtyinbaseunit','itemkey');
                $arrRentalItemQtyDelivery = array_column($rsRentalDetail,'deliveredqtyinbaseunit','itemkey');
                
                for($i=0;$i<count($arrItemkey);$i++){
                    $rsItem = $item->getDataRowById($arrItemkey[$i]);
                    //$this->setLog(($arrQtyInBaseUnit[$i]+$arrRentalItemQtyDelivery[$arrItemkey[$i]])  .' > '. $arrRentalItemQty[$arrItemkey[$i]],true);
                    
                    if(($arrQtyInBaseUnit[$i]+$arrRentalItemQtyDelivery[$arrItemkey[$i]]) > $arrRentalItemQty[$arrItemkey[$i]])  
                        $this->addErrorList($arrayToJs,false, $rsItem[0]['name'].'. '.$this->errorMsg[223]); 
                    
                    if(!in_array($arrItemkey[$i],$arrRentalItemKey)) 
                        $this->addErrorList($arrayToJs,false, $rsItem[0]['name'].'. '.$this->errorMsg[213]);
                 
                }
            }


            $arrDetailKeys = array(); 

            for($i=0;$i<count($arrItemkey);$i++) { 
                if (empty($arrItemkey[$i]) ){ 
                    $this->addErrorList($arrayToJs,false, $this->errorMsg['item'][1]); 	
                } else {
                    $rsItem = $item->getDataRowById($arrItemkey[$i]);

                    if (in_array($arrItemkey[$i],$arrDetailKeys))   
                       $this->addErrorList($arrayToJs,false, $rsItem[0]['name'].'. '.$this->errorMsg[215]); 	 
                    else 
                        array_push($arrDetailKeys, $arrItemkey[$i]);
                     

                    // cek punya konversi unit utk satuan yg dipilih gk  
                    $conv = $item->getConvMultiplier($arrItemkey[$i],$arrSelUnit[$i]);
                    if (empty($conv)) 
                        $this->addErrorList($arrayToJs,false,$rsItem[0]['name']. '. ' . $this->errorMsg['itemUnitConversion'][3]); 
                   
                }

            }



            return $arrayToJs;
    }
 

    function validateConfirm($rsHeader){
        $id = $rsHeader[0]['pkey']; 
        $customerkey = $rsHeader[0]['customerkey']; 
        $quotationkey = $rsHeader[0]['refkey']; 
        
        $warehouse = new Warehouse();    
        $item = new Item();    
        $salesOrderRental = new SalesOrderRental();    
 
        $rsDetail = $this->getDetailWithRelatedInformation($id);
        $rsSOHeader = $salesOrderRental->getDataRowById($rsHeader[0]['refkey']);
        $rsSODetail = $salesOrderRental->getDetailWithRelatedInformation($rsSOHeader[0]['pkey']);
        $arrSOItemKey = array_column($rsSODetail, 'itemkey');
        $arrSOItemQty = array_column($rsSODetail,'qtyinbaseunit','itemkey');
  
        //validasi stock
        $itemMovement = new itemMovement();
        $arrItem = array();
        for($i=0;$i<count($rsDetail);$i++){
            
            if(empty($rsDetail[$i]['itemkey'])) continue;

             $saldoakhir = $itemMovement->getItemQOH($rsDetail[$i]['itemkey'], $rsHeader[0]['warehousekey']);  
             $totalqty = $saldoakhir - $rsDetail[$i]['qtyinbaseunit'];  
            
            if($totalqty<0){
            	$rsItem = $item->getDataRowById($rsDetail[$i]['itemkey']); 
                $this->addErrorLog(false,'<strong>'.$rsItem[0]['name'].'</strong>. '.$this->errorMsg[402]);
            }
        }
         

    }
  

    function confirmTrans($rsHeader){  
        
        $id = $rsHeader[0]['pkey']; 
       
        $item = new Item();
        $customer = new Customer(); 
        $salesOrderRental = new SalesOrderRental(); 
        $salesOrderRental->updateDetailDelivery($id);

        /*$rsCustomer = $customer->getDataRowById($rsHeader[0]['customerkey']);
         

        if ($rsHeader[0]['isfulldeliver']){
            $itemMovement = new ItemMovement();  
            for($i=0;$i<count($rsDetail); $i++){	
               
                if(empty($rsDetail[$i]['itemkey']) || $rsDetail[$i]['itemtype']==SERVICE)
                    continue;
                
               $itemMovement->updateItemMovement($id,$rsDetail[$i]['itemkey'],-$rsDetail[$i]['qtyinbaseunit'],$this->tableName, $rsHeader[0]['warehousekey'], $note,$rsHeader[0]['trdate']);
            }	 
        }
 
        //update jurnal umum 
        $this->updateGL($rsHeader);*/
        $note = $rsHeader[0]['code']; 
        $itemMovement = new ItemMovement(); 
        $rsSalesDetail = $this->getDetailWithRelatedInformation($id);
        for($i=0;$i<count($rsSalesDetail);$i++){
            if(empty($rsSalesDetail[$i]['itemkey']) || $rsSalesDetail[$i]['qtyinbaseunit'] < 1)
                continue;
            
            $itemMovement->updateItemMovement($id,$rsSalesDetail[$i]['itemkey'],-$rsSalesDetail[$i]['qtyinbaseunit'], 0 ,$this->tableName, $rsHeader[0]['warehousekey'], $note,$rsHeader[0]['trdate']);
            $itemMovement->updateItemMovementRental($id,$rsSalesDetail[$i]['itemkey'],-$rsSalesDetail[$i]['qtyinbaseunit'], 0 ,$this->tableName, $rsHeader[0]['warehousekey'], $note,$rsHeader[0]['trdate'],$rsHeader[0]['customerkey']);
            $itemMovement->updateQORRental($rsSalesDetail[$i]['itemkey'],$rsHeader[0]['warehousekey'],-$rsSalesDetail[$i]['qtyinbaseunit']);  
            
        }
                  

    } 


    function validateCancel($rsHeader,$autoChangeStatus=false){ 
        $id = $rsHeader[0]['pkey'];
        $itemIn = new ItemIn();
        $tableKey = $this->getTableKeyAndObj($this->tableName,array('key'));    
        $tableKey = $tableKey['key'];
        
        $rsItemIn = $itemIn-> searchDataRow( array(  $itemIn->tableName.'.pkey', $itemIn->tableName.'.code'  ) , 
                                ' and  '.$itemIn->tableName.'.refkey = '.$this->oDbCon->paramString($rsHeader[0]['pkey']).' and reftabletype = '.$tableKey.' and '.$itemIn->tableName.'.statuskey in(2,3)'  
                       );
        
        if (!empty($rsItemIn))
                 $this->addErrorLog( false, '<strong>'.$rsHeader[0]['code'].'</strong> ' .$this->errorMsg[201].'<br><strong>'.$rsItemIn[0]['code'].'</strong>, ' .$this->errorMsg[225] );
  
        //cek apakah sudah ad penerimaan PO

        /*if (!$rsHeader[0]['isfulldeliver']) {
            $salesDelivery = new SalesDelivery();
            $rsSalesDelivery = $salesDelivery->searchData('','',true,' and '.$salesDelivery->tableName.'.refkey = '.$this->oDbCon->paramString($id).' and ('.$salesDelivery->tableName.'.statuskey in  (2,3) )');

            if (!empty($rsSalesDelivery))
                 $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' . $this->errorMsg[201].' ' .$this->errorMsg['salesOrder'][2]);
        }*/
 
    } 



     function cancelTrans($rsHeader,$copy){ 
        $id = $rsHeader[0]['pkey']; 
        $rsSalesDetail = $this->getDetailWithRelatedInformation($id);
        $itemMovement = new ItemMovement();  
        $salesOrderRental = new SalesOrderRental();
        $itemMovement->cancelMovement($id,$this->tableName); 
        $itemMovement->cancelMovementRental($id,$this->tableName); 
        
        for($i=0;$i<count($rsSalesDetail);$i++){
            if(empty($rsSalesDetail[$i]['itemkey']) || $rsSalesDetail[$i]['qtyinbaseunit'] < 1)
                continue;
            
            $itemMovement->updateQORRental($rsSalesDetail[$i]['itemkey'],$rsHeader[0]['warehousekey'],$rsSalesDetail[$i]['qtyinbaseunit']);  
            
        }        

	   $salesOrderRental->deleteDetailDelivery($id);
         
        $itemIn = new ItemIn();
        $tableKey = $this->getTableKeyAndObj($this->tableName,array('key'));    
        $tableKey = $tableKey['key'];
        
        $rsItemIn = $itemIn-> searchDataRow( array(  $itemIn->tableName.'.pkey', $itemIn->tableName.'.code'  ) , 
                                ' and  '.$itemIn->tableName.'.refkey = '.$this->oDbCon->paramString($rsHeader[0]['pkey']).' and reftabletype = '.$tableKey.' and '.$itemIn->tableName.'.statuskey =1'  
                       );
         
         if(!empty($rsItemIn)){
             for($i=0;$i<count($rsItemIn);$i++)
                 $itemIn->changeStatus($rsItemIn[$i]['pkey'],4,'',false, true); 
         }
   
        if ($copy)
            $this->copyDataOnCancel($id);	  

        $this->cancelGLByRefkey($rsHeader[0]['pkey'],$this->tableName);

    }  

    function updateGL($rs){
       
    }
    

    function getDetailWithRelatedInformation($pkey,$criteria=''){
        
      $sql = 'select
            '.$this->tableNameDetail.'.*,
            '.$this->tableItem.'.name as itemname,
            '.$this->tableItem.'.code as itemcode,
            '.$this->tableItemUnit.'.name as unitname,
            baseunit.name as baseunitname
        from
            '.$this->tableNameDetail.',
            '.$this->tableItem.',
            '.$this->tableItemUnit.',
            '.$this->tableItemUnit.' baseunit
        where  
            '.$this->tableNameDetail .'.itemkey = '.$this->tableItem.'.pkey and
            '.$this->tableNameDetail.'.unitkey = '.$this->tableItemUnit.'.pkey and
            '.$this->tableItem.'.baseunitkey = baseunit.pkey and
            '. $this->tableNameDetail.'.refkey in  ('.$this->oDbCon->paramString($pkey,',') . ') ' ;
 
        $sql .= $criteria;
        return $this->oDbCon->doQuery($sql);
    }
    
    /*function searchDataForAutoComplete($fieldname='',$searchkey='',$mustmatch=false,$searchCriteria='',$orderCriteria='', $limit=''){

     $sql = 'select
                '.$this->tableName. '.pkey,  concat('.$this->tableName. '.code,\' - \', '.$this->tableCustomer.'.name) as value,  grandtotal
            from 
                '.$this->tableName . ','.$this->tableCustomer.','.$this->tableStatus.'
            where  		
                '.$this->tableName . '.customerkey = '.$this->tableCustomer.'.pkey and
                '.$this->tableName . '.statuskey = '.$this->tableStatus.'.pkey 
        ';


    if(!empty($fieldname)){

        $sql .= ' and ' ;

        if($mustmatch)
            $sql .=  $fieldname .' = '. $this->oDbCon->paramString($searchkey);
        else
            $sql .=  $fieldname .' like '. $this->oDbCon->paramString('%'.$searchkey.'%');
    }

    if($searchCriteria <> '')
        $sql .= ' ' .$searchCriteria;

    if($orderCriteria <> ''){
        $sql .= ' ' .$orderCriteria;

    }

    if($limit <> '')
        $sql .= ' ' .$limit;

    return $this->oDbCon->doQuery($sql);	
    } */
 
 
    function generateInvoice($pkey){   
        $rsHeader = $this->getDataRowById($pkey);   

        $file=  HTTP_HOST . 'invoice/'.$pkey.'/'.md5($pkey . $rsHeader[0]['grandtotal'] . $this->secretKey).'/1';   
        $invoice =  file_get_contents($file);
        
        return $invoice;
    }
 
        
     function normalizeParameter($arrParam, $trim = false){          
            $arrParam = parent::normalizeParameter($arrParam); 

            $arrItemkey = $arrParam['hidItemKey'];
            $arrQty = $arrParam['qty'];  
            $arrUnitKey = $arrParam['selUnit']; 

            $reCountResult = $this->reCountSubtotal($arrParam); 
            $arrParam['detailCOGS'] = $reCountResult['detailCOGS']; 
          
            //$arrParam['totalWeight'] = $reCountResult['totalWeight'];
           
             for ($i=0;$i<count($arrItemkey);$i++){ 
  
                $qtyinbaseunit = $arrParam['detailCOGS'][$i]['qtyInBaseUnit'];  
                $arrParam['qtyInBaseUnit'][$i] = $qtyinbaseunit;
                $arrParam['unitConvMultiplier'][$i] = $arrParam['detailCOGS'][$i]['unitConvMultiplier'];
                $arrParam['baseUnitKey'][$i] = $arrParam['detailCOGS'][$i]['baseUnitKey']; 
                // $arrParam['cogs'][$i] = $arrParam['detailCOGS'][$i]['cogs']; 
                //$arrParam['itemType'][$i] = $arrParam['detailCOGS'][$i]['itemType'];
                //$arrParam['itemWeight'][$i] = $arrParam['detailCOGS'][$i]['weight']; 
           
                // set default jadi 0 lg, utk handle copy on cancel
                //$arrParam['deliveredQtyInBaseUnit'][$i] = 0;
            }
 
       
        return $arrParam;
    }
     
}
?>
