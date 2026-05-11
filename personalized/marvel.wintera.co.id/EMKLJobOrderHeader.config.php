<?php

$this->arrDataListAvailableColumn = array(); 
array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 120));
array_push($this->arrDataListAvailableColumn, array('code' => 'date','title' => 'date','dbfield' => 'trdate','default'=>true, 'width' => 80, 'align' =>'center', 'format' => 'date'));
array_push($this->arrDataListAvailableColumn, array('code' => 'warehouse','title' => 'warehouse','dbfield' => 'warehousename', 'width' => 120));
array_push($this->arrDataListAvailableColumn, array('code' => 'containertype','title' => 'type','dbfield' => 'containertype', 'default'=>true,'width' => 60));
array_push($this->arrDataListAvailableColumn, array('code' => 'etdpol','title' => 'etd','dbfield' => 'etdpol','default'=>true, 'width' => 80,'align' =>'center', 'format' => 'date'));
array_push($this->arrDataListAvailableColumn, array('code' => 'etapod','title' => 'eta','dbfield' => 'etapod','default'=>true, 'width' => 80,'align' =>'center', 'format' => 'date'));
array_push($this->arrDataListAvailableColumn, array('code' => 'shipper','title' => 'shipper','dbfield' => 'customername','default'=>true,'width' => 250));
array_push($this->arrDataListAvailableColumn, array('code' => 'shipperPEB','title' => ($this->jobType == EMKL['jobType']['import']) ? 'shipperPIB' : 'shipperPEB','dbfield' => 'customerpebname', 'width' => 250));
array_push($this->arrDataListAvailableColumn, array('code' => 'pod','title' => 'pod','dbfield' => 'podname','default'=>true,'width' => 100));
array_push($this->arrDataListAvailableColumn, array('code' => 'pol','title' => 'pol','dbfield' => 'polname','default'=>true,'width' => 100));
array_push($this->arrDataListAvailableColumn, array('code' => 'jobType','title' => 'jobType','dbfield' => 'jobtypeunion','default'=>true,'width' => 150));
array_push($this->arrDataListAvailableColumn, array('code' => 'note','title' => 'note','dbfield' => 'trdesc','width' => 200));
array_push($this->arrDataListAvailableColumn, array('code' => 'salesman','title' => 'salesman','dbfield' => 'salesname','width' => 150));
array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 80));


array_push($this->printMenu,array('code' => 'printFormJO', 'name' => $this->lang['printFormJO'],  'icon' => 'print', 'url' => 'print/emklFormJobHeader')); 

?>