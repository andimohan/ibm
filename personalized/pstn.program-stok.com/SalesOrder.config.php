<?php
$this->printMenu = array();  
array_push($this->printMenu,array('code' => 'printInvoice', 'name' => $this->lang['print'] . ' ' .$this->lang['invoice'],  'icon' => 'print', 'url' => 'print/salesOrder'));
//array_push($this->printMenu,array('code' => 'printInvoiceNonPPN', 'name' => $this->lang['print'] .$this->lang['invoice']. ' (Non PKP)',  'icon' => 'print', 'url' => 'print/salesOrder?invoiceType=1'));
array_push($this->printMenu,array('code' => 'printInvoiceSJ', 'name' => $this->lang['print'].' SJ2',  'icon' => 'print', 'url' => 'print/salesOrder?invoiceType=1'));
//array_push($this->printMenu,array('code' => 'printInvoiceSubtotal', 'name' => $this->lang['print'] . ' ' .$this->lang['invoice']. ' (Subtotal)',  'icon' => 'print', 'url' => 'print/salesOrderSubtotal'));
array_push($this->printMenu,array('code' => 'printDeliveryNotes', 'name' => $this->lang['print'] . ' ' .$this->lang['deliveryNotes'],  'icon' => 'print', 'url' => 'print/salesOrderDelivery'));
array_push($this->printMenu,array('code' => 'printShippingLabel', 'name' => $this->lang['print'] . ' ' .$this->lang['shippingLabel'],  'icon' => 'print', 'url' => 'print/salesLabel'));
	
array_push($this->printMenu,array('code' => 'sendAttachment', 'name' => 'email invoice',  'icon' => 'print', 'url' => 'mail-invoice'));
        
if($this->isActiveModule('marketplace')) {
    array_push($this->printMenu,array('code' => 'printSeparator', 'name' => '-'));
    array_push($this->printMenu,array('code' => 'printAirwayBill', 'name' => $this->lang['print'] . ' ' .$this->lang['airwayBill'],  'icon' => 'print', 'url' => 'print/airwayBill'));
    array_push($this->printMenu,array('code' => 'printInvoiceMarketplace', 'name' => $this->lang['print'] . ' ' .$this->lang['invoice'] .' (Marketplace)',  'icon' => 'print', 'url' => 'print/marketplaceInvoice'));
}

array_push($this->printMenu,array('code' => 'sendAttachment', 'name' => 'Email Invoice',  'icon' => 'print', 'url' => 'mail-invoice'));

?>