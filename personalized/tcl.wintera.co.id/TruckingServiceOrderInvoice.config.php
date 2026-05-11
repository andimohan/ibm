<?php

$this->printMenu = array();
array_push($this->printMenu,array('code' => 'printInvoice', 'name' => $this->lang['printInvoice'],  'icon' => 'print', 'url' => 'print/truckingServiceOrderInvoice?datetype=invoicedate'));
array_push($this->printMenu,array('code' => 'printInvoicePeriod', 'name' => $this->lang['printInvoice'] .' (SR Periode)',  'icon' => 'print', 'url' => 'print/truckingServiceOrderInvoicePeriodeTCL'));

?>