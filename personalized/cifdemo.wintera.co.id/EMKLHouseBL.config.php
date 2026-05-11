<?php
$this->printMenu = array();  
array_push($this->printMenu,array('code' => 'printTransaction', 'name' => $this->lang['printTransaction'],  'icon' => 'print', 'url' => 'print/emklHouseBL')); 
array_push($this->printMenu,array('code' => 'printTransactionBlank', 'name' => $this->lang['printTransaction'] .' (Blank)',  'icon' => 'print', 'url' => 'print/emklHouseBL/?showBorder=0&showTitle=0')); 
array_push($this->printMenu,array('code' => 'printSI', 'name' => $this->lang['print'] .' SI',  'icon' => 'print', 'url' => 'print/shippingInstructionHBL'));  
array_push($this->printMenu,array('code' => 'printPreAlert', 'name' => $this->lang['print'] .' Pre-Alert Notice',  'icon' => 'print', 'url' => 'print/preAlertNotice')); 
array_push($this->printMenu,array('code' => 'printCargoReleased', 'name' => $this->lang['print'] .' Cargo Released',  'icon' => 'print', 'url' => 'print/cargoReleased')); 
?>