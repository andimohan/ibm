<?php

//array_push($this->printMenu,array('code' => 'printAttachmentInvoice', 'name' => $this->lang['print'].' ' .$this->lang['attachment'],  'icon' => 'print', 'url' => 'print/attachmentTruckingServiceOrderInvoice'));


array_push($this->printMenu,array('code' => 'printInvoice', 'name' => $this->lang['printInvoice']. ' 1',  'icon' => 'print', 'url' => 'print/truckingServiceOrderInvoice?type=1'));
array_push($this->printMenu,array('code' => 'printInvoice2', 'name' => $this->lang['printInvoice']. ' 2',  'icon' => 'print', 'url' => 'print/truckingServiceOrderInvoice?type=2'));
array_push($this->printMenu,array('code' => 'printInvoice3', 'name' => $this->lang['printInvoice']. ' 3',  'icon' => 'print', 'url' => 'print/truckingServiceOrderInvoice?type=3'));

array_push($this->printMenu, array('code' => 'printInvoiceAttachmentSPK', 'name' => 'Cetak Lamp. SPK (Tgl. JO)', 'icon' => 'print', 'url' => 'print/attachmentTruckingServiceOrderInvoice?groupby=workOrder&datetype=1'));
array_push($this->printMenu, array('code' => 'printInvoiceAttachmentSPK2', 'name' => 'Cetak IJ', 'icon' => 'print', 'url' => 'print/attachmentTruckingServiceOrderInvoice?groupby=workOrder'));
array_push($this->printMenu, array('code' => 'printInvoiceAttachmentJO', 'name' => 'Cetak Lampiran JO', 'icon' => 'print', 'url' => 'print/attachmentTruckingServiceOrderInvoice?groupby=jobOrder'));
array_push($this->printMenu, array('code' => 'printDebitNoteTigaRaksa', 'name' => 'Cetak DN Tiga Raksa', 'icon' => 'print', 'url' => 'print/debitNoteTigaRaksa'));
array_push($this->printMenu, array('code' => 'printInvoiceAttachmentExcel', 'name' => 'Cetak Lampiran Tiga Raksa (Kontrak)', 'icon' => 'print', 'url' => 'print/truckingServiceOrderInvoiceExcel'));

    
?>