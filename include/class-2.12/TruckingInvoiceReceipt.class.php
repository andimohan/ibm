<?php

class TruckingInvoiceReceipt extends SalesOrderInvoiceReceipt{
	
    function __construct(){

        parent::__construct();
 
        $this->tableInvoice = 'trucking_service_order_invoice_header';  
        
        $this->isTransaction = true;
        $this->securityObject = 'SalesOrderInvoiceReceipt'; // perlu diganti nanti
        $this->autoPrintURL = 'print/salesOrderInvoiceReceipt';
 
        $this->printMenu = array();  
        array_push($this->printMenu,array('code' => 'printTransaction', 'name' => $this->lang['printTransaction'],  'icon' => 'print', 'url' => 'print/truckingInvoiceReceipt'));

    }
  
    function normalizeParameter($arrParam, $trim=false){ 
        $arrParam = parent::normalizeParameter($arrParam,true);  
        return $arrParam;
    } 
    
    function getTransactionObj(){
        return new TruckingServiceOrderInvoice();
    }
     
}

?>
