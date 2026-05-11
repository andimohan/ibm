<?php
$this->printMenu = array();  

$printUrl = ($this->jobType == EMKL['jobType']['import']) ? 'print/emklJobOrderImport' : 'print/emklJobOrderExport';
array_push($this->printMenu,array('code' => 'printTransaction', 'name' => $this->lang['printTransaction'],  'icon' => 'print', 'url' => $printUrl)); 
array_push($this->printMenu,array('code' => 'performaShippingInstruction', 'name' => 'Performa Shipping Instruction',  'icon' => 'print', 'url' => 'print/performaShippingInstruction')); 
 

$this->domainConfig['cif'] = array(
                                    'manualCode' => true, // gk kepake jg gpp yang penting ad isi arraynya
                                        
                                );
    
?>