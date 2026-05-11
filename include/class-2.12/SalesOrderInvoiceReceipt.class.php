<?php

class SalesOrderInvoiceReceipt extends BaseClass{
	
    function __construct(){

        parent::__construct();

        $this->tableName = 'sales_order_invoice_receipt_header';
        $this->tableNameDetail = 'sales_order_invoice_receipt_detail';
        $this->tableCustomer = 'customer'; 
        $this->tableAR = 'ar'; 
        $this->tableStatus = 'transaction_status';
        $this->tableWarehouse = 'warehouse';   
        
        $this->tableInvoice = 'trucking_service_order_invoice_header';  
        
        $this->isTransaction = true;
        $this->newLoad = true;
        $this->securityObject = 'SalesOrderInvoiceReceipt'; 
        $this->autoPrintURL = 'print/salesOrderInvoiceReceipt';


        $this->arrDataDetail = array(); 
        $this->arrDataDetail['pkey'] = array('hidDetailKey');
        $this->arrDataDetail['refkey'] = array('pkey','ref'); 
        $this->arrDataDetail['invoicekey'] = array('hidInvoiceKey' ,array('mandatory'=>true));
        //$this->arrDataDetail['description'] = array('detailNote');
        $this->arrDataDetail['amount'] = array('invoiceTotal','number');


        $arrDetails = array(); 
        array_push($arrDetails, array('dataset' => $this->arrDataDetail, 'tableName' => $this->tableNameDetail));

        $this->arrData = array(); 
        $this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails));  
        $this->arrData['code'] = array('code');
        $this->arrData['trdate'] = array('trDate','date');
        $this->arrData['receiveddate'] = array('trReceivedDate','date');
        $this->arrData['customerkey'] = array('hidCustomerKey');
        $this->arrData['warehousekey'] = array('selWarehouseKey');
        $this->arrData['trdesc'] = array('trDesc');
        $this->arrData['statuskey'] = array('selStatus');
        $this->arrData['grandtotal'] = array('grandTotal','number');   
        $this->arrData['recipientname'] = array('recipientName');
        $this->arrData['picname'] = array('picName');
        $this->arrData['tablekey'] = array('hidTableType');
        $this->arrData['invoicecodecache'] = array('invoicecodecache');
 
        array_push($this->filterCriteria, array('title' => $this->lang['warehouse'], 'field' => 'warehousekey'));
        
	 
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'date','title' => 'dateSent','dbfield' => 'trdate','default'=>true, 'width' => 100, 'align' =>'center', 'format' => 'date'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'receiveddate','title' => 'dateReceived','dbfield' => 'receiveddate','default'=>true, 'width' => 100, 'align' =>'center', 'format' => 'date'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'customer','title' => 'customer','dbfield' => 'customername','default'=>true,'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'recipient','title' => 'recipient','dbfield' => 'recipientname','default'=>true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'total','title' => 'total','dbfield' => 'grandtotal','default'=>true ,'width' => 100, 'align' => 'right', 'format' => 'integer'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'invoicecode','title' => 'invoiceCode','dbfield' => 'invoicecodecache', 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'note','title' => 'note','dbfield' => 'trdesc','default'=>true ,'width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname',  'width' => 70));
        array_push($this->arrDataListAvailableColumn, array('code' => 'warehouse','title' => 'warehouse','dbfield' => 'warehousename', 'width' => 100));
    
        $this->arrSearchColumn = array ();
        array_push($this->arrSearchColumn, array('Kode', $this->tableName . '.code'));
        array_push($this->arrSearchColumn, array('Tanggal', $this->tableName . '.trdate'));
        array_push($this->arrSearchColumn, array('Pelanggan', $this->tableCustomer . '.name')); 
        array_push($this->arrSearchColumn, array('Invoice Code', $this->tableName . '.invoicecodecache')); 
        array_push($this->arrSearchColumn, array('Catatan', $this->tableName . '.trdesc')); 


        $this->printMenu = array();  
        array_push($this->printMenu,array('code' => 'printTransaction', 'name' => $this->lang['printTransaction'],  'icon' => 'print', 'url' => 'print/salesOrderInvoiceReceipt'));
   		array_push($this->printMenu,array('code' => 'printTransactionWithoutPrice', 'name' => $this->lang['printTransaction'] .' ('.$this->lang['withoutAmount'].')',  'icon' => 'print', 'url' => 'print/salesOrderInvoiceReceipt?hideAmount=1'));
   
        $this->includeClassDependencies(array(
            'TruckingServiceOrderInvoice.class.php',
            'EMKLOrderInvoice.class.php',
            'Customer.class.php'
        ));  
            
    }

    function getQuery(){

        $sql = '
            SELECT
                '.$this->tableName.'.* ,   
			    '.$this->tableWarehouse.'.name as warehousename,
                '.$this->tableCustomer.'.name as customername, 
                '.$this->tableStatus.'.status as statusname
            FROM '.$this->tableStatus.',
                 '.$this->tableCustomer.',
                 '.$this->tableName.',
                 '.$this->tableWarehouse.'  
            WHERE   
                  '.$this->tableName.'.customerkey = '.$this->tableCustomer.'.pkey and
                  '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey and 
                  '.$this->tableName.'.warehousekey = '.$this->tableWarehouse.'.pkey
            ' .$this->criteria ;
            
        $sql .=  $this->getWarehouseCriteria() ;
          
        return $sql;
    }

 
    function reCountGrandtotal($arrParam){

        $transactionObj = $this->getTransactionObj();
        
        $grandTotal = 0;
        $amount = 0; 

        $arrInvoicekey = $arrParam['hidInvoiceKey']; 
        $arrAmount = $arrParam['invoiceTotal']; 
        $arrInvoiceTotal = array();
        
        for ($i=0;$i<count($arrInvoicekey);$i++){

            $arrAmount[$i] = $this->unFormatNumber($arrAmount[$i]);
            if (empty($arrInvoicekey[$i]) || empty($arrAmount[$i]))   
                continue; 

            $rsSI = $transactionObj->getDataRowById($arrInvoicekey[$i]);
            $arrInvoiceTotal[$i] = $rsSI[0]['grandtotal'];
            
            $amount += $this->unFormatNumber($arrInvoiceTotal[$i]); 
        }  

        $grandTotal = $amount  ;

        $reCountResult = array(); 
        $reCountResult['grandTotal'] = $grandTotal; 
        $reCountResult['invoiceTotal'] = $arrInvoiceTotal; 

        return $reCountResult;
				
	}

    function validateForm($arr,$pkey = ''){ 

        $transactionObj = $this->getTransactionObj();
        
        $arrayToJs = parent::validateForm($arr,$pkey); 
 
        $customerkey = $arr['hidCustomerKey'];  
        $arrInvoiceKey = $arr['hidInvoiceKey']; 
        $arrPick = $arr['chkPick'];  
 
        $arrDetailKey = array();
          
        if(empty($customerkey)) 
            $this->addErrorList($arrayToJs,false,$this->errorMsg['customer'][1]);  
  
        
        $hasSOI = false; 
        // cek ad duplikasi gk, dan cek customernya sesuai gk
        for($i=0;$i<count($arrInvoiceKey);$i++) {   

            if ( (!empty($arrInvoiceKey[$i]) ) && !empty($arrPick[$i]) )  {
                $hasSOI = true;   
                
                $rsSOI = $transactionObj->getDataRowById($arrInvoiceKey[$i]);

                if (in_array($arrInvoiceKey[$i],$arrDetailKey)){  
                    $this->addErrorList($arrayToJs,false, $rsSOI[0]['code'].'. '.$this->errorMsg[215]); 	 
                }else{ 
                    if (!empty($arrInvoiceKey[$i]))  
                        array_push($arrDetailKey, $arrInvoiceKey[$i]);
                }

                if ($rsSOI[0]['customerkey'] <> $customerkey)
                    $this->addErrorList($arrayToJs,false, $rsSOI[0]['code'].'. '.$this->errorMsg['invoice'][2]); 	

				if ($rsSOI[0]['statuskey'] <> TRANSACTION_STATUS['konfirmasi'])
                    $this->addErrorList($arrayToJs,false, $rsSOI[0]['code'].'. '.$this->errorMsg[204]); 
            }
  
             
        } 
 
        return $arrayToJs;
    }
    
    

    function getDetailWithRelatedInformation($pkey,$criteria=''){
        $sql = 'select
            '.$this->tableNameDetail.'.*,  
            '.$this->tableInvoice.'.code as invoicecode,  
            '.$this->tableInvoice.'.trdate as invoicedate 
          from
            '.$this->tableNameDetail.',
            '.$this->tableInvoice.' 
          where  
            '. $this->tableNameDetail.'.refkey  = '.$this->oDbCon->paramString($pkey) . ' and
            '. $this->tableNameDetail.'.invoicekey = '.$this->tableInvoice.'.pkey ' ;

        $sql .= $criteria;
 
        return $this->oDbCon->doQuery($sql);

    } 
   
    function normalizeParameter($arrParam, $trim=false){
        
        
        $detail = $arrParam['hidDetailKey'];  
           
        // remove uncheck 
        $this->removeUnCheckRows($arrParam,$this->arrDataDetail);
        
        $reCountResult = $this->reCountGrandtotal($arrParam); 
        $arrParam['grandTotal'] = $reCountResult['grandTotal']; 
        
        for ($i=0;$i<count($reCountResult['invoiceTotal']);$i++){ 
            $arrParam['invoiceTotal'][$i] =  $reCountResult['invoiceTotal'][$i];
        }
 
        
        $truckingServiceOrderInvoice = new TruckingServiceOrderInvoice();
        $rsInvoice = $truckingServiceOrderInvoice->searchDataRow(array($truckingServiceOrderInvoice->tableName.'.code'),
                                                     ' and '.$truckingServiceOrderInvoice->tableName.'.pkey in ('.$this->oDbCon->paramString($arrParam['hidInvoiceKey'],',').')');
        
        $arrParam['invoicecodecache'] = implode(', ', array_column($rsInvoice,'code'));
        
        
        $arrParam = parent::normalizeParameter($arrParam,true); 
         
        return $arrParam;
    } 
  
    function validateConfirm($rsHeader){  
        $id = $rsHeader[0]['pkey'];
        $customerkey = $rsHeader[0]['customerkey'];  
         
        $rsDetail = $this->getDetailById($id);
         
        $transactionObj = $this->getTransactionObj(); 
        
        foreach($rsDetail as $row){
            $invoicekey = $row['invoicekey'];
            
            $rsInvoice = $transactionObj->getDataRowById($invoicekey);
            if ($rsInvoice[0]['statuskey'] != 2) 
              $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' . $this->errorMsg[201].'<br><strong>'.$rsInvoice[0]['code'].'</strong>, ' .$this->errorMsg[204],true);
        
      
        }
    } 

    function confirmTrans($rsHeader){
        $transactionObj = $this->getTransactionObj();
        
        $rsDetail = $this->getDetailById($rsHeader[0]['pkey']);
        
        foreach($rsDetail as $row){
            $invoicekey = $row['invoicekey'];
            $sql = 'update '.$this->tableInvoice.' set 
                    receiptdt = '.$this->oDbCon->paramString($rsHeader[0]['trdate']).' ,
                    receiveddate = '.$this->oDbCon->paramString($rsHeader[0]['receiveddate']).' 
                    where pkey = ' . $this->oDbCon->paramString($invoicekey);
            
            $this->oDbCon->execute($sql); 
              
            $transactionObj->changeStatus($invoicekey,3,'',false,true);
        }
        
    } 
    
       
    function validateCancel($rsHeader, $autoChangeStatus = false){
        $id = $rsHeader[0]['pkey'];
 
    } 
     
    function cancelTrans($rsHeader,$copy){  
        
        $transactionObj = $this->getTransactionObj(); 
		$id = $rsHeader[0]['pkey'];
		  	    
        $rsDetail = $this->getDetailById($id);
        
        foreach($rsDetail as $row){
            $invoicekey = $row['invoicekey'];
            $sql = 'update '.$this->tableInvoice.' set receiptdt = \'0000-00-00\', receiveddate =  \'0000-00-00\' where pkey = ' . $this->oDbCon->paramString($invoicekey);
            
            $this->oDbCon->execute($sql); 
              
            $rsInvoice = $transactionObj->getDataRowById($invoicekey);
            if ($rsInvoice[0]['statuskey'] == 3)
                $transactionObj->changeStatus($invoicekey,2,'',false,true,true); // abaikan validasi   
      
        }
        
		if ($copy)
			$this->copyDataOnCancel($id);	  
         
	} 
    
    function getInvoiceReceipt($sokey, $criteria = ''){
        
        // TODO: nanti perlu ditambahkan informasi tablekey disini
        
		if(!is_array($sokey)) $sokey = array($sokey);
		
		// idealnya cuma 1 tanda terima utk 1 invoice
        $sql = 'select
                    '.$this->tableName.'.pkey,
                    '.$this->tableName.'.code,
                    '.$this->tableName.'.trdate ,
                    '.$this->tableName.'.receiveddate ,
                    '.$this->tableName.'.recipientname ,
                    '.$this->tableName.'.statuskey  ,
                    '.$this->tableNameDetail.'.invoicekey 
                from 
                    '.$this->tableName.',
                    '.$this->tableNameDetail.'
                where 
                    '.$this->tableName.'.pkey = '.$this->tableNameDetail.'.refkey and
                    '.$this->tableNameDetail.'.invoicekey in ('.$this->oDbCon->paramString($sokey,',').')';
        
        
        if(!empty($criteria))
            $sql .=  $criteria;
		
        return  $this->oDbCon->doQuery($sql);
        
    } 
    
    function getTransactionObj(){
        return new TruckingServiceOrderInvoice();
    }
     
}

?>
