<?php

class EMKLInvoiceReceipt extends SalesOrderInvoiceReceipt{
	
    function __construct(){

        parent::__construct();
 
        $this->tableName = 'emkl_invoice_receipt_header';  
        $this->tableNameDetail = 'emkl_invoice_receipt_detail';  
        $this->tableInvoice = 'emkl_order_invoice_header';  
        $this->tableCurrency = 'currency';
        
        $this->securityObject = 'EMKLInvoiceReceipt'; // perlu diganti nanti
        $this->autoPrintURL = 'print/emklInvoiceReceipt';
  
        
        $this->arrDataDetail = array(); 
        $this->arrDataDetail['pkey'] = array('hidDetailKey');
        $this->arrDataDetail['refkey'] = array('pkey','ref'); 
        $this->arrDataDetail['invoicekey'] = array('hidInvoiceKey' ,array('mandatory'=>true));
        $this->arrDataDetail['description'] = array('detailNote');
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
        $this->arrData['tablekey'] = array('hidTableType');
        $this->arrData['invoicecodecache'] = array('invoicecodecache');
 
        $this->arrSearchColumn = array ();
        array_push($this->arrSearchColumn, array('Kode', $this->tableName . '.code'));
        array_push($this->arrSearchColumn, array('Tanggal', $this->tableName . '.trdate'));
        array_push($this->arrSearchColumn, array('Pelanggan', $this->tableCustomer . '.name')); 
        array_push($this->arrSearchColumn, array('Catatan', $this->tableName . '.trdesc')); 
        array_push($this->arrSearchColumn, array('Invoice Code', $this->tableName . '.invoicecodecache')); 
        
        $this->printMenu = array();  
        array_push($this->printMenu,array('code' => 'printTransaction', 'name' => $this->lang['printTransaction'],  'icon' => 'print', 'url' => 'print/emklInvoiceReceipt'));
      
        $this->includeClassDependencies(array(
              'EMKLJobOrder.class.php', 
              'EMKLOrderInvoice.class.php', 
              'Customer.class.php', 
        )); 

        $this->overwriteConfig();
    }
    
    function getDetailWithRelatedInformation($pkey,$criteria=''){
        $sql = 'select
            '.$this->tableNameDetail.'.*,  
            '.$this->tableInvoice.'.code as invoicecode,  
            '.$this->tableInvoice.'.totaldownpayment,  
            '.$this->tableInvoice.'.trdate as invoicedate ,
            '.$this->tableCurrency.'.name as currencyname
          from
            '.$this->tableNameDetail.',
            '.$this->tableInvoice.' 
            left join  '.$this->tableCurrency.' on '.$this->tableInvoice.'.currencykey = '.$this->tableCurrency.'.pkey
          where  
            '. $this->tableNameDetail.'.refkey  in ('.$this->oDbCon->paramString($pkey,',') . ') and
            '. $this->tableNameDetail.'.invoicekey = '.$this->tableInvoice.'.pkey ' ;

        $sql .= $criteria;
 
        return $this->oDbCon->doQuery($sql);

    }
  
    function normalizeParameter($arrParam, $trim=false){  
        
        $emklOrderInvoice = new EMKLOrderInvoice();
        
        $rsInvoice = $emklOrderInvoice->searchDataRow(array($emklOrderInvoice->tableName.'.code'),
                                                     ' and '.$emklOrderInvoice->tableName.'.pkey in ('.$this->oDbCon->paramString($arrParam['hidInvoiceKey'],',').')');
        
        
        $arrParam['invoicecodecache'] = implode(', ', array_column($rsInvoice,'code'));
        
        // gk bisa pake parent:: karena salah turunan, turunan dari salesOrderInvoiceReceipt yg isinya tentang Trucking
        $class = new BaseClass();
        $arrParam = $class->normalizeParameter($arrParam,true);   
         
        return $arrParam;
    } 
     
    
    
    function getTransactionObj(){
        return new EMKLOrderInvoice();
    }
  
}

?>
