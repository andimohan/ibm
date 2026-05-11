<?php
$printWorkOrderUrl = ($this->jobType == EMKL['jobType']['import']) ? 'print/emklWorkOrderImport' : 'print/emklWorkOrderExport';
array_push($this->printMenu,array('code' => 'printTransactionWorkOrder', 'name' => $this->lang['printWorkOrder'],  'icon' => 'print', 'url' => $printWorkOrderUrl));       
?>