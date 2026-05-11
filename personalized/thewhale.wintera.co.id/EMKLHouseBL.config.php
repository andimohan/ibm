<?php
array_push($this->printMenu,array('code' => 'printWithoutLogo', 'name' => $this->lang['printTransaction'].' ('.$this->lang['preprinted'].')' ,  'icon' => 'print', 'url' => 'print/emklHouseBL/?logo=0'));
array_push($this->printMenu,array('code' => 'printWSI', 'name' => $this->lang['printTransaction'].' WSI' ,  'icon' => 'print', 'url' => 'print/emklHouseBLWSI'));
?>