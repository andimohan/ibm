<?php
   
$this->printMenu = array();  

  switch($this->jobType){
      case EMKL['jobType']['import'] : $printUrl = 'print/emklJobOrderImport'; $printNOAUrl = 'print/arrivalNoticeImport'; break;
      case EMKL['jobType']['export'] : $printUrl = 'print/emklJobOrderExport'; $printNOAUrl = 'print/arrivalNoticeExport'; break;
      case EMKL['jobType']['domestic'] : $printUrl = 'print/emklJobOrderDomestic'; $printNOAUrl = 'print/arrivalNoticeDomestic'; break;
      default : $printUrl =  'print/emklJobOrderImport';
  }

array_push($this->printMenu,array('code' => 'printTransaction', 'name' => $this->lang['printTransaction'],  'icon' => 'print', 'url' => $printUrl)); 
array_push($this->printMenu,array('code' => 'printNOA', 'name' => $this->lang['printNOA'],  'icon' => 'print', 'url' => $printNOAUrl));


?>