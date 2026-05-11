<?php
$this->printMenu = array();  
array_push($this->printMenu,array('code' => 'printInvoice', 'name' => $this->lang['print'] . ' ' .$this->lang['invoice'],  'icon' => 'print', 'url' => 'print/salesOrder'));
array_push($this->printMenu,array('code' => 'printInvoiceWithSiganture', 'name' => $this->lang['print'] . ' ' .$this->lang['invoice']. ' (TTD) ',  'icon' => 'print', 'url' => 'print/salesLabel'));
array_push($this->printMenu,array('code' => 'sendAttachment', 'name' => 'Email Invoice',  'icon' => 'print', 'url' => 'mail-invoice'));
?>