<?php
  
class SalesRentalQuotation extends BaseClass{ 
    
    function __construct(){

            parent::__construct();

            $this->tableName = 'sales_rental_quotation_header';
            $this->tableNameDetail = 'sales_rental_quotation_detail';
            $this->tableCustomer = 'customer';
            $this->tableCity = 'city';
            $this->tableEmployee = 'employee';
            $this->tableWarehouse = 'warehouse'; 
            $this->tableStatus = 'transaction_status';
            $this->tableMovement = 'item_movement'; 
            $this->tableHistory = 'history'; 
            $this->tableLocation = 'location'; 
            $this->tableItem = 'item'; 	
            $this->tableItemCategory = 'item_category'; 	
            $this->tableItemUnit = 'item_unit'; 	
            $this->tableTimeUnit = 'time_unit';
            $this->tableBrand = 'brand'; 	
            $this->tableItemCategory = 'item_category'; 	
            $this->tableCartTemp = 'cart_temp';  
            $this->isTransaction = true; 		

            $this->autoPrintURL = 'print/salesRentalQuotation';
         
            $this->securityObject = 'SalesRentalQuotation';   

            $this->arrLinkedTable = array(); 
            $defaultFieldName = 'refkey';
            array_push($this->arrLinkedTable, array('table'=>'sales_delivery_header','field'=>$defaultFieldName));  
            array_push($this->arrLinkedTable, array('table'=>'ar','field'=>$defaultFieldName));  

            $this->arrDataDetail = array();  
            $this->arrDataDetail['pkey'] = array('hidDetailKey');
            $this->arrDataDetail['refkey'] = array('pkey','ref');
            $this->arrDataDetail['itemkey'] = array('hidItemKey'); 
            $this->arrDataDetail['qty'] = array('qty','number'); 
            $this->arrDataDetail['qtyinbaseunit'] = array('qtyInBaseUnit','number');
            $this->arrDataDetail['unitkey'] = array('selUnit');
            $this->arrDataDetail['timeunitkey'] = array('selTimeUnit');
            $this->arrDataDetail['priceinunit'] = array('priceInUnit','number'); 
            $this->arrDataDetail['totaldays'] = array('totalDays','number'); 
            $this->arrDataDetail['priceinbaseunit'] = array('priceInBaseUnit','number'); 
            $this->arrDataDetail['unitconvmultiplier'] = array('unitConvMultiplier','number');
            $this->arrDataDetail['total'] = array('detailSubtotal','number');
            $this->arrDataDetail['itemtype'] = array('itemType');
 
            $arrDetails = array();
            array_push($arrDetails, array('dataset' => $this->arrDataDetail)); 

            $this->arrData = array(); 
            $this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails)); 
            $this->arrData['code'] = array('code');
            $this->arrData['name'] = array('name');
            $this->arrData['trdate'] = array('trDate','date');
            $this->arrData['warehousekey'] = array('selWarehouseKey');
            $this->arrData['customerkey'] = array('hidCustomerKey'); 
            $this->arrData['jobname'] = array('jobName'); 
            $this->arrData['trdesc'] = array('trDesc'); 
            $this->arrData['grandtotal'] = array('grandtotal','number'); 
            $this->arrData['statuskey'] = array('selStatus');
            $this->arrData['saleskey'] = array('hidSalesKey');
            $this->arrData['locationkey'] = array('hidLocationKey');
            $this->arrData['recipientname'] = array('recipientName');
            $this->arrData['recipientphone'] = array('recipientPhone');
            $this->arrData['recipientemail'] = array('recipientEmail');
            $this->arrData['recipientaddress'] = array('recipientAddress');
            $this->arrData['recipientcitykey'] = array('hidRecipientCityKey');    
          
            $this->arrDataListAvailableColumn = array(); 
            array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 100));
            array_push($this->arrDataListAvailableColumn, array('code' => 'date','title' => 'date','dbfield' => 'trdate','default'=>true, 'width' => 100, 'align'=>'center', 'format' => 'date'));
            array_push($this->arrDataListAvailableColumn, array('code' => 'name','title' => 'name','dbfield' => 'name','default'=>true, 'width' => 250));
            array_push($this->arrDataListAvailableColumn, array('code' => 'warehouse','title' => 'warehouse','dbfield' => 'warehousename','default'=>true, 'width' => 100));
            array_push($this->arrDataListAvailableColumn, array('code' => 'customer','title' => 'customer','dbfield' => 'customername','default'=>true, 'width' => 200));
            array_push($this->arrDataListAvailableColumn, array('code' => 'total','title' => 'total','dbfield' => 'grandtotal','default'=>true, 'width' => 100, 'align' => 'right', 'format'=>'number'));
            array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
            array_push($this->arrDataListAvailableColumn, array('code' => 'desc','title' => 'note','dbfield' => 'trdesc', 'width' => 200));
            array_push($this->arrDataListAvailableColumn, array('code' => 'salesman','title' => 'salesman','dbfield' => 'salesname', 'width' => 150));
            array_push($this->arrDataListAvailableColumn, array('code' => 'location','title' => 'location','dbfield' => 'locationname', 'width' => 150));
      		
			$this->includeClassDependencies(array(
                   'Warehouse.class.php',   
                   'City.class.php', 
                   'Customer.class.php', 
                   'Item.class.php', 
                   'Service.class.php', 
                   'ItemUnit.class.php',
                   'TimeUnit.class.php',
                   'Location.class.php',
                   'GeneralJournal.class.php',
                   'SalesOrderRental.class.php',
                   'Employee.class.php' 
            )); 
		
            $this->printMenu = array();
            array_push($this->printMenu,array('code' => 'printInvoice', 'name' => $this->lang['print']  ,  'icon' => 'print', 'url' => 'print/salesRentalQuotation'));
  
            array_push($this->filterCriteria, array('title' => $this->lang['warehouse'], 'field' => 'warehousekey'));
        
    }
 
            
    
    function getQuery(){

        $sql = '
            SELECT '.$this->tableName.'.* ,
               '.$this->tableCustomer.'.name as customername,
               '.$this->tableLocation.'.name as locationname ,
               '.$this->tableWarehouse.'.name as warehousename,
               '.$this->tableStatus.'.status as statusname ,
               '.$this->tableEmployee.'.name as salesname 
            FROM 
                '.$this->tableStatus.', 
                '.$this->tableCustomer.' left join '.$this->tableCity.' on  
                     '.$this->tableCustomer.'.citykey = '.$this->tableCity.'.pkey,
                '.$this->tableWarehouse.',
                '.$this->tableName.' 
			         left join '.$this->tableEmployee.' on   '.$this->tableName.'.saleskey = '.$this->tableEmployee.'.pkey 
                     left join '.$this->tableLocation.' on '.$this->tableName.'.locationkey = '.$this->tableLocation.'.pkey 
            WHERE '.$this->tableName.'.customerkey = '.$this->tableCustomer.'.pkey and
                     '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey and
                     '.$this->tableName.'.warehousekey = '.$this->tableWarehouse.'.pkey 
        ' .$this->criteria ; 


        $sql .=  $this->getWarehouseCriteria() ;
        $sql .=  $this->getCompanyCriteria() ;

        return $sql;
    }  
 
       

    function reCountSubtotal($arrParam){
  
            $subtotal = 0 ;
          
            $arrItemKey = $arrParam['hidItemKey'];
 
            $arrQty = $arrParam['qty']; 
            $arrPriceinunit = $arrParam['priceInUnit']; 
            $arrTotalDays = $arrParam['totalDays']; 
            $arrTransUnitKey = $arrParam['selUnit']; 
            $arrTimeUnitKey = $arrParam['selTimeUnit']; 

            $arrItemDetail = array();
            $item = new Item();
        
            for ($i=0;$i<count($arrItemKey);$i++){

                if (empty($arrItemKey[$i]) || empty($arrTimeUnitKey[$i]))   continue; 

                    $rsItem = $item->getDataRowById($arrItemKey[$i]);
 
                    $itemkey = $arrItemKey[$i];
                    $transactionUnitKey = $arrTransUnitKey[$i];
                    $baseunitkey = $rsItem[0]['baseunitkey']; 
                    $qty =  $this->unFormatNumber($arrQty[$i]);
                    $conversionMultiplier = $item->getConvMultiplier($itemkey,$transactionUnitKey,$baseunitkey); 
                    $qtyinbaseunit = $qty * $conversionMultiplier;
                    $priceInUnit = $this->unFormatNumber($arrPriceinunit[$i]);
                    $totalDays = $this->unFormatNumber($arrTotalDays[$i]);
 
                    $arrItemDetail[$i]['baseUnitKey'] = $baseunitkey;
                    $arrItemDetail[$i]['unitConvMultiplier'] = $conversionMultiplier;
                    $arrItemDetail[$i]['qtyInBaseUnit'] = $qtyinbaseunit ; 
                    $arrItemDetail[$i]['priceInBaseUnit'] = $priceInUnit / $conversionMultiplier ;
   
                    $detailSubtotal = $qty * $priceInUnit * $totalDays;
                    $arrItemDetail[$i]['detailSubtotal'] = $detailSubtotal; 
				    $arrItemDetail[$i]['itemType'] = $rsItem[0]['itemtype']; 

                    $subtotal += $detailSubtotal ; 
                
            } 

            $grandtotal = $subtotal;
       
 
            $reCountResult = array(); 
            $reCountResult['subtotal'] = $grandtotal;  
            $reCountResult['grandtotal'] = $grandtotal;  
            $reCountResult['detailCOGS'] = $arrItemDetail; 

            return $reCountResult;

    } 
   

    function validateForm($arr,$pkey = ''){
            $item = new Item();   

            $arrayToJs = parent::validateForm($arr,$pkey); 

            $customerkey = $arr['hidCustomerKey'];  
            $arrItemkey = $arr['hidItemKey']; 
            $arrQty = $arr['qty']; 
            $arrPriceinunit = $arr['priceInUnit'];
            $arrTotalDays = $arr['totalDays'];
            $email = $arr['recipientEmail'];
            $arrSelUnit = $arr['selUnit']; 
 

            if (PLAN_TYPE['maxsalesorder'] >= 0){ 
                $month = str_replace('\'','',$this->oDbCon->paramDate($arr['trDate'],' / ','m'));
                $year = str_replace('\'','',$this->oDbCon->paramDate($arr['trDate'],' / ','Y'));

                $sql = 'select
                            count(pkey) as total 
                        from 
                            ' .$this->tableName.'
                        where 
                            month(trdate) = '.$this->oDbCon->paramString($month).' and year(trdate) = '. $this->oDbCon->paramString($year);

                if (!empty($pkey))
                    $sql .= ' and pkey <> ' . $pkey;

                $rs = $this->oDbCon->doQuery($sql);

                if($rs[0]['total'] >= PLAN_TYPE['maxsalesorder'])   
                  $this->addErrorList($arrayToJs,false,$this->errorMsg['limit'][1]);   
            }


            //validasi kalo status gk menunggu gk bisa edit 
            if (!empty($pkey)){
                $rs = $this->getDataRowById($pkey);
                if ($rs[0]['statuskey'] <> 1){
                    $this->addErrorList($arrayToJs,false,$this->errorMsg[212]);
                }
            }  

            if(empty($customerkey)){
                $this->addErrorList($arrayToJs,false,$this->errorMsg['customer'][1]);
            }


            if(!empty($email)){
                if(!filter_var($email, FILTER_VALIDATE_EMAIL)) 
                    $this->addErrorList($arrayToJs,false,$this->errorMsg['email'][3]); 
            }  

            if(empty($arrItemkey)) 
                 $this->addErrorList($arrayToJs,false,  $this->errorMsg[501]);  


            $arrDetailKeys = array(); 

            for($i=0;$i<count($arrItemkey);$i++) { 
                if (empty($arrItemkey[$i]) ){ 
                    $this->addErrorList($arrayToJs,false, $this->errorMsg['item'][1]); 	
                } 

                if (!empty($arrItemkey[$i])){
                    $rsItem = $item->getDataRowById($arrItemkey[$i]);
                    if ($this->unFormatNumber($arrQty[$i]) <= 0){ 
                        $this->addErrorList($arrayToJs,false,$rsItem[0]['name']. '. ' . $this->errorMsg[510]);  
                    }
                    
                    if ($this->unFormatNumber($arrTotalDays[$i]) <= 0){ 
                        $this->addErrorList($arrayToJs,false,$rsItem[0]['name']. '. ' . $this->errorMsg[510]);  
                    }

//                    $priceMandatory = $this->loadSetting('priceMandatory');
//                    if ($priceMandatory == 1 && $this->unFormatNumber($arrPriceinunit[$i]) <= 0){  
//                        $this->addErrorList($arrayToJs,false,$rsItem[0]['name']. '. ' . $this->errorMsg[511]);  
//                    }  

                    // cek punya konversi unit utk satuan yg dipilih gk  
                    $conv = $item->getConvMultiplier($arrItemkey[$i],$arrSelUnit[$i]);
                    if (empty($conv)){
                        $rsItem = $item->getDataRowById($arrItemkey[$i]);
                        $this->addErrorList($arrayToJs,false,$rsItem[0]['name']. '. ' . $this->errorMsg['itemUnitConversion'][3]); 
                    }  
                }

                // cek ada detail double gk   
                if (in_array($arrItemkey[$i],$arrDetailKeys)){  
                    $rsItem = $item->getDataRowById($arrItemkey[$i]);
                    $this->addErrorList($arrayToJs,false, $rsItem[0]['name'].'. '.$this->errorMsg[215]); 	 
                }else{ 
                    array_push($arrDetailKeys, $arrItemkey[$i]);
                } 
                

            }
  
            return $arrayToJs;
    }
 

    function validateConfirm($rsHeader){
        $id = $rsHeader[0]['pkey'];  
    }


    function confirmTrans($rsHeader){ 
         $id = $rsHeader[0]['pkey'];
        $salesOrderRental = new SalesOrderRental();
            $customer = new Customer();  
            $rsCustomer = $customer->getDataRowById($rsHeader[0]['customerkey']);
            $rsDetail = $this->getDetailWithRelatedInformation($id); 

            $arrParam = array();
            for($i=0;$i<count($rsDetail);$i++){ 
                $arrParam['hidDetailKey'][$i] = 0;
                $arrParam['hidItemKey'][$i] = $rsDetail[$i]['itemkey'];
                $arrParam['qty'][$i] = $rsDetail[$i]['qty'];
                $arrParam['selUnit'][$i] = $rsDetail[$i]['unitkey'];
                $arrParam['selTimeUnit'][$i] = $rsDetail[$i]['timeunitkey'];
                $arrParam['priceInUnit'][$i] = $rsDetail[$i]['priceinunit'];
                $arrParam['totalDays'][$i] = $rsDetail[$i]['totaldays'];
                $arrParam['detailSubtotal'][$i] = $rsDetail[$i]['total'];
            }

            $arrParam['code'] = 'xxxxxx';
            $arrParam['hidSalesQuotationKey'] = $rsHeader[0]['pkey'];
            $arrParam['trDate'] = $this->formatDBDate($rsHeader[0]['trdate'],'d / m / Y');
            $arrParam['hidCustomerKey'] = $rsHeader[0]['customerkey'];
            $arrParam['customerName'] = $rsCustomer[0]['name'];
            $arrParam['hidSalesKey'] = $rsHeader[0]['saleskey'];
            $arrParam['trDesc'] = '';
            $arrParam['selWarehouseKey'] = $rsHeader[0]['warehousekey'];
            $arrParam['hidLocationKey'] = $rsHeader[0]['locationkey'];
            $arrParam['recipientName'] = $rsHeader[0]['recipientname'];
            $arrParam['recipientPhone'] = $rsHeader[0]['recipientphone'];
            $arrParam['recipientEmail'] = $rsHeader[0]['recipientemail'];
            $arrParam['recipientAddress'] = $rsHeader[0]['recipientaddress'];
            $arrParam['hidRecipientCityKey'] = $rsHeader[0]['recipientcitykey'];

            $arrayToJs = $salesOrderRental->addData($arrParam); 

            if (!$arrayToJs[0]['valid'])
                $this->addErrorLog(false, '<strong>'.$rsHeader[0]['code'] . '</strong>. '.$this->errorMsg[201].' '.$arrayToJs[0]['message'], true); 
        
    } 
       
    function validateCancel($rsHeader,$autoChangeStatus=false){ 
        $id = $rsHeader[0]['pkey'];
   
        $salesOrderRental = new SalesOrderRental();
        $rsSO = $salesOrderRental->searchData('','',true,' and refkey = '.$this->oDbCon->paramString($id).' and ('.$salesOrderRental->tableName.'.statuskey  in (2,3))');
        if(!empty($rsSO)) 
            $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' . $this->errorMsg[201].' <strong>'.$rsSO[0]['code'].'</strong>. ' .$this->errorMsg[203]);
    } 



   function cancelTrans($rsHeader,$copy){ 
        $id = $rsHeader[0]['pkey']; 
       
        $salesOrderRental = new SalesOrderRental();
		$rsSO = $salesOrderRental->searchData('','',true,' and '.$salesOrderRental->tableName.'.refkey = '.$this->oDbCon->paramString($rsHeader[0]['pkey']).' and '.$salesOrderRental->tableName.'.statuskey = 1');
		for($i=0;$i<count($rsSO);$i++) 
          $salesOrderRental->changeStatus($rsSO[$i]['pkey'],4,'',false,true); 
       
        if ($copy)
            $this->copyDataOnCancel($id);	  

//        $this->cancelGLByRefkey($rsHeader[0]['pkey'],$this->tableName);

    }  
   

    function getDetailWithRelatedInformation($pkey,$criteria=''){
        
      $sql = 'select
            '.$this->tableNameDetail.'.*,
            '.$this->tableItem.'.name as itemname,
            '.$this->tableItem.'.code as itemcode,
            '.$this->tableTimeUnit.'.name as timename,
            '.$this->tableItemUnit.'.name as unitname,
            baseunit.name as baseunitname
        from
            '.$this->tableNameDetail.',
            '.$this->tableItem.',
            '.$this->tableTimeUnit.',
            '.$this->tableItemUnit.',
            '.$this->tableItemUnit.' baseunit
        where  
            '.$this->tableNameDetail .'.itemkey = '.$this->tableItem.'.pkey and
            '.$this->tableNameDetail.'.unitkey = '.$this->tableItemUnit.'.pkey and
            '.$this->tableNameDetail.'.timeunitkey = '.$this->tableTimeUnit.'.pkey and
            '.$this->tableItem.'.baseunitkey = baseunit.pkey and
            '. $this->tableNameDetail.'.refkey in  ('.$this->oDbCon->paramString($pkey,',') . ') ' ;

        $sql .= $criteria;
  
        return $this->oDbCon->doQuery($sql);

    }
   
        
     function normalizeParameter($arrParam, $trim = false){ 
          
            $arrItemkey = $arrParam['hidItemKey'];
            $arrQty = $arrParam['qty']; 
            $arrPriceinunit = $arrParam['priceInUnit'];   
            $arrTotalDays = $arrParam['totalDays'];   
            $arrUnitKey = $arrParam['selUnit'];  
  
            $reCountResult = $this->reCountSubtotal($arrParam); 
            $arrParam['detailCOGS'] = $reCountResult['detailCOGS']; 
          
            $arrParam['grandtotal'] = $reCountResult['grandtotal']; 
           
             for ($i=0;$i<count($arrItemkey);$i++){ 
  
                $qtyinbaseunit = $arrParam['detailCOGS'][$i]['qtyInBaseUnit'];  
                $arrParam['qtyInBaseUnit'][$i] = $qtyinbaseunit;
                $arrParam['unitConvMultiplier'][$i] = $arrParam['detailCOGS'][$i]['unitConvMultiplier']; 
                $arrParam['priceInBaseUnit'][$i] = $arrParam['detailCOGS'][$i]['priceInBaseUnit']; 
                $arrParam['detailSubtotal'][$i] = $arrParam['detailCOGS'][$i]['detailSubtotal'];
                $arrParam['itemType'][$i] = $arrParam['detailCOGS'][$i]['itemType'];
            
            }
 
            $arrParam = parent::normalizeParameter($arrParam,true); 

            
        return $arrParam;
    }
     
}
?>
