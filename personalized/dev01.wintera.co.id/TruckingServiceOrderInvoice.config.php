<?php
 $this->printMenu = array();
 array_push($this->printMenu,array('code' => 'printInvoice', 'name' => $this->lang['printInvoice'],  'icon' => 'print', 'url' => 'print/truckingServiceOrderInvoice'));
 array_push($this->printMenu,array('code' => 'printInvoiceType1', 'name' => $this->lang['printInvoice'] . ' type 1',  'icon' => 'print', 'url' => 'print/truckingServiceOrderInvoice?type=1'));
 array_push($this->printMenu,array('code' => 'printInvoiceSelling', 'name' => $this->lang['printInvoice'] .' (non reim.)',  'icon' => 'print', 'url' => 'print/truckingServiceOrderInvoice?selling=1'));
 array_push($this->printMenu,array('code' => 'printInvoiceAndtax', 'name' => $this->lang['printInvoice'] .' &amp; ' . $this->lang['tax'],  'icon' => 'print', 'url' => 'print/truckingServiceOrderInvoice?attachment=1'));
?>
