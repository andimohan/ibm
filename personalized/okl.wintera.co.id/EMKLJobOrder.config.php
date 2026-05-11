<?php

$this->printMenu = array();
$printUrl = ($this->jobType == EMKL['jobType']['import']) ? 'print/emklOrderSheetImport' : 'print/emklOrderSheetExport';
array_push($this->printMenu,array('code' => 'printOrderSheet', 'name' => $this->lang['printOrderSheet'],  'icon' => 'print', 'url' => $printUrl)); 

$printUrl = ($this->jobType == EMKL['jobType']['import']) ? 'print/emklJobOrderImport' : 'print/emklJobOrderExport';
array_push($this->printMenu,array('code' => 'printTransaction', 'name' => $this->lang['printSummary'],  'icon' => 'print', 'url' => $printUrl)); 
//
//$printUrl = ($this->jobType == EMKL['jobType']['import']) ? 'print/emklJobOrderImport' : 'print/emklJobOrderExport';
//array_push($this->printMenu,array('code' => 'printTransaction', 'name' => $this->lang['printSummary'],  'icon' => 'print', 'url' => $printUrl)); 

?>