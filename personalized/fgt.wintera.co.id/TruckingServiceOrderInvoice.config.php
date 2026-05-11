<?php
$this->printMenu = array();
array_push($this->printMenu,array('code' => 'printInvoice', 'name' => $this->lang['printInvoice'],  'icon' => 'print', 'url' => 'print/truckingServiceOrderInvoice'));
array_push($this->printMenu,array('code' => 'printInvoiceRounded', 'name' => $this->lang['printInvoice'].' (Pembulatan)',  'icon' => 'print', 'url' => 'print/truckingServiceOrderInvoice?rounding=1'));

?>