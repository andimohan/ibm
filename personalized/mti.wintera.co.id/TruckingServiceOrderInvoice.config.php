<?php

$this->printMenu = array();
array_push($this->printMenu,array('code' => 'printInvoice', 'name' => $this->lang['printInvoice'],  'icon' => 'print', 'url' => 'print/truckingServiceOrderInvoice?datetype=invoicedate'));
array_push($this->printMenu,array('code' => 'printInvoicePeriod', 'name' => $this->lang['printInvoice'] .' (SR Periode)',  'icon' => 'print', 'url' => 'print/truckingServiceOrderInvoice?datetype=perioddate'));
array_push($this->printMenu,array('code' => 'printInvoiceMaersk', 'name' => $this->lang['printInvoice'] .' (Maersk)',  'icon' => 'print', 'url' => 'print/truckingServiceOrderInvoiceMaersk'));
array_push($this->printMenu,array('code' => 'printInvoiceLSI', 'name' => $this->lang['printInvoice'] .' (LSI)',  'icon' => 'print', 'url' => 'print/truckingServiceOrderInvoiceLSI'));
array_push($this->printMenu,array('code' => 'printInvoiceSariRoti', 'name' => $this->lang['printInvoice'] .' (Sari Roti)',  'icon' => 'print', 'url' => 'print/truckingServiceOrderInvoiceSariRoti'));


?>
