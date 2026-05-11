<?php
$this->printMenu = array();  
array_push($this->printMenu,array('code' => 'printQuotation', 'name' => $this->lang['print'],  'icon' => 'print', 'url' => 'print/truckingQuotation'));
array_push($this->printMenu,array('code' => 'printQuotationWithSiganture', 'name' => $this->lang['print'] . ' (TTD) ',  'icon' => 'print', 'url' => 'print/truckingQuotation?sign=1'));
array_push($this->printMenu,array('code' => 'printQuotationApprovedBy', 'name' => $this->lang['print'].' ' .$this->lang['approval'],  'icon' => 'print', 'url' => 'print/truckingQuotation?approvedBy=1'));
array_push($this->printMenu,array('code' => 'printQuotationApprovedByWithSiganture', 'name' => $this->lang['print'] . ' ' .$this->lang['approval']. ' (TTD) ',  'icon' => 'print', 'url' => 'print/truckingQuotation?approvedBy=1&sign=1'));

?>