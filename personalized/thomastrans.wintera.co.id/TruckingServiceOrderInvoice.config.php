<?php
$this->printMenu = array();  
array_push($this->printMenu,array('code' => 'printInvoice', 'name' => $this->lang['printInvoice'],  'icon' => 'print', 'url' => 'print/truckingServiceOrderInvoice'));
array_push($this->printMenu,array('code' => 'printInvoiceWithSiganture', 'name' => $this->lang['print'] . ' ' .$this->lang['invoice']. ' (TTD) ',  'icon' => 'print', 'url' => 'print/truckingServiceOrderInvoice?sign=1'));
//array_push($this->printMenu,array('code' => 'printConsignee', 'name' => $this->lang['print'] . ' ' .$this->lang['consignee'],  'icon' => 'print', 'url' => 'print/truckingServiceOrderInvoice?consignee=1'));
//array_push($this->printMenu,array('code' => 'printConsigneeWithSignature', 'name' => $this->lang['print'] . ' ' .$this->lang['consignee']. ' (TTD) ',  'icon' => 'print', 'url' => 'print/truckingServiceOrderInvoice?consignee=1&sign=1'));
?>