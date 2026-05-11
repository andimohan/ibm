<?php
class InvoiceTax extends BaseClass{
    
   function __construct(){
		
		parent::__construct();
		
		$this->tableName = 'invoice_tax';
		$this->tableEMKLInvoice = 'emkl_order_invoice_header';
		$this->tableTruckingInvoice = 'trucking_service_order_invoice_header';
        $this->tableFile = 'invoice_tax_file';
        $this->tableCustomer = 'customer';
        $this->tableEmployee = 'employee';
        $this->tableWarehouse = 'warehouse';
        
		$this->tableStatus = 'transaction_status'; 
		$this->securityObject = 'InvoiceTax'; 
        $this->newLoad = true;
        $this->isTransaction = true;
	    $this->activeModule = $this->isActiveModule(array('EMKLOrderInvoice','TruckingServiceOrderInvoice'));
        $this->uploadFileFolder = 'invoice-tax/';
       
        $this->useStorage = $this->useStorage('S3');		
 
       if($this->useStorage){ 
            $this->arrDataFileDetail = array();  
            $this->arrDataFileDetail['pkey'] = array('hidDetailFileKey');
            $this->arrDataFileDetail['refkey'] = array('pkey','ref');
            $this->arrDataFileDetail['file'] = array('fileDetail',array('datatype' => 'file','uploadFolder' => $this->uploadFileFolder));
       }
       
        $arrDetails = array(); 
       
        if($this->useStorage){ 
            array_push($arrDetails, array('dataset' => $this->arrDataFileDetail, 'tableName' => $this->tableFile));
        }else{ 
            array_push($arrDetails, array('dataset' => $this->arrDataFile, 'tableName' => $this->tableFile, 
                                          'datatype' => 'file', 'uploadFolder' => $this->uploadFileFolder,
                                          'token' => 'token-item-file-uploader', 'fileName' => 'item-file-uploader')); 
        }
       
        $this->arrData = array(); 
        $this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails));
        $this->arrData['code'] = array('code'); 
        $this->arrData['trdate'] = array('trDate','date');
        $this->arrData['refkey'] = array('hidRefHeaderKey');
        $this->arrData['reftabletype'] = array('selType');
        $this->arrData['invoicetaxnumber'] = array('invoiceTaxNumber');
        $this->arrData['trdesc'] = array('trDesc');
        $this->arrData['warehousekey'] = array('selWarehouse');
        $this->arrData['taxpercentage'] = array('selTaxPercentage');
        $this->arrData['statuskey'] = array('selStatus');  

         
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 90));
        array_push($this->arrDataListAvailableColumn, array('code' => 'date','title' => 'date','dbfield' => 'trdate','default'=>true, 'width' => 90,  'align' => 'center', 'format' => 'date'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'warehouse','title' => 'warehouse','dbfield' => 'warehousename', 'default'=>true,'width' => 150));             
        array_push($this->arrDataListAvailableColumn, array('code' => 'refcode','title' => 'refCode','dbfield' => 'refcode', 'default'=>true,'width' => 160));            
        array_push($this->arrDataListAvailableColumn, array('code' => 'invoicetaxnumber','title' => 'invoiceTaxNumber','dbfield' => 'invoicetaxnumber','default'=>true, 'width' => 170));
        array_push($this->arrDataListAvailableColumn, array('code' => 'taxpercentage','title' => 'tax','dbfield' => 'taxpercentage', 'width' => 60,'align' =>'right', 'format' => 'number'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
		 

         
        $this->arrSearchColumn = array ();
        array_push($this->arrSearchColumn, array('Kode', $this->tableName . '.code')); 
        array_push($this->arrSearchColumn, array('Lokasi Usaha', $this->tableWarehouse . '.name')); 
        array_push($this->arrSearchColumn, array('No. Faktur Pajak', $this->tableName . '.invoicetaxnumber'));   
        
       if($this->activeModule['emklorderinvoice'])       
            array_push($this->arrSearchColumn, array('Kode Ref.', $this->tableEMKLInvoice . '.code')); 
              
        if($this->activeModule['truckingserviceorderinvoice'])       
            array_push($this->arrSearchColumn, array('Kode Ref.', $this->tableTruckingInvoice . '.code')); 
       	    $this->includeClassDependencies(array(
              'EMKLOrderInvoice.class.php',
              'TruckingServiceOrderInvoice.class.php'
        ));
	   
	    $this->arrTablekeys = array(
			'truckingInvoiceKey' =>  $this->getTableKeyAndObj($this->tableTruckingInvoice,array('key'))['key'],
			'emklInvoiceKey' =>  $this->getTableKeyAndObj($this->tableEMKLInvoice,array('key'))['key'],
	   );
	    

        $this->overwriteConfig();
	}
	
	 function getQuery(){
	  
		   $sql = '
			select
					'.$this->tableName. '.*,
                    CONCAT_WS(\'\','.$this->tableTruckingInvoice.'.code, '.$this->tableEMKLInvoice.'.code) as refcode,                                   
					'.$this->tableWarehouse.'.name as warehousename, 
					'.$this->tableStatus.'.status as statusname 
				from
					'.$this->tableName.'
                        left join '.$this->tableTruckingInvoice.' on '.$this->tableName.'.refkey = '.$this->tableTruckingInvoice.'.pkey and '.$this->tableName.'.reftabletype = '.$this->arrTablekeys['truckingInvoiceKey'].' 
                        left join '.$this->tableEMKLInvoice.' on '.$this->tableName.'.refkey = '.$this->tableEMKLInvoice.'.pkey and '.$this->tableName.'.reftabletype = '.$this->arrTablekeys['emklInvoiceKey'].',         
                    '.$this->tableWarehouse.',
                    '.$this->tableStatus.' 
                where
                    '.$this->tableName.'.warehousekey = '.$this->tableWarehouse.'.pkey and
                    '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey 
 		' .$this->criteria ;
		 
         return $sql;
    }
        
	function  afterStatusChanged($rsHeader){
       
            
        $invoicetaxnumber = $rsHeader[0]['invoicetaxnumber'];
        $refkey = $rsHeader[0]['refkey'];
        $reftabletype = $rsHeader[0]['reftabletype'];
          
        $rs = $this->searchDataRow(array($this->tableName.'.pkey',$this->tableName.'.invoicetaxnumber'),
                                    ' 
                                      and '.$this->tableName.'.refkey = '.$this->oDbCon->paramString($refkey).'
                                      and '.$this->tableName.'.reftabletype = '.$this->oDbCon->paramString($reftabletype).'
                                      and '.$this->tableName.'.statuskey in (2,3)' 
                                  );   
        
        $invoicetaxnumber = (!empty($rs)) ? implode(', ' ,array_column($rs,'invoicetaxnumber')) : '';
			
        $this->updateInvoiceTaxNumber($invoicetaxnumber,$refkey,$reftabletype); 
        
    }    
    
 
    function validateForm($arr,$pkey = ''){ 
		$arrayToJs = parent::validateForm($arr,$pkey); 
		
		// cari yg berbeda,  tp statusnya batal gk termasuk
		if(!empty($arr['invoiceTaxNumber'])){ 
			$rs = $this->isValueExisted($pkey,'invoicetaxnumber',$arr['invoiceTaxNumber'],4);
			if(count($rs) <> 0) 
				$this->addErrorList($arrayToJs,false,$this->errorMsg['invoiceTaxNumber'][2]); 
		}
		 

		// kadang empty sam 0 nth kenapa bisa beda
        if(empty($arr['selTaxPercentage']) || $arr['selTaxPercentage'] <= 0)
		 	$this->addErrorList($arrayToJs,false,$this->errorMsg['invoiceTaxNumber'][3]); 
		
		return $arrayToJs;
	 }
     
    function normalizeParameter($arrParam, $trim=false){
		
		// validasi kalo dr form
		// salah satu saja cukup menandakan dari form
		 
		if(isset($arrParam['refTruckingInvoiceCode'])){
			switch($arrParam['selType']){
				case $this->arrTablekeys['truckingInvoiceKey'] : $arrParam['hidRefHeaderKey'] = $arrParam['hidRefTruckingInvoiceHeaderKey'] ; break;
				case $this->arrTablekeys['emklInvoiceKey'] : $arrParam['hidRefHeaderKey'] = $arrParam['hidRefEMKLInvoiceHeaderKey'] ; break;
			}
		}
		
        $arrParam = parent::normalizeParameter($arrParam,true); 
        
        return $arrParam;
    }

        
    function validateConfirm($rsHeader){  
        
        $id = $rsHeader[0]['pkey'];
        $refkey = $rsHeader[0]['refkey'];
        $reftabletype = $rsHeader[0]['reftabletype'];
        $invoiceTaxNumber = $rsHeader[0]['invoicetaxnumber'];
        $taxPercentage = $rsHeader[0]['taxpercentage'];

        if(empty($invoiceTaxNumber)) 
            $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '  .$this->errorMsg['invoiceTaxNumber'][1]);
      
      
		// validasi sudah ada no faktur pajak blm utk invoice yg sama
		// NANTI perlu ad settingan, utk invoice yg ad beberapa nilai pajak, tdk perlu validasi
		// nanti cek berdasarkan nilai pajaknya jg 
		
	    $rs = $this->searchDataRow(array($this->tableName.'.pkey',$this->tableName.'.refkey',$this->tableName.'.reftabletype'),
                                  'and '.$this->tableName.'.refkey = '.$this->oDbCon->paramString($refkey).' 
                                   and '.$this->tableName.'.reftabletype = '.$this->oDbCon->paramString($reftabletype).' 
                                   and '.$this->tableName.'.taxpercentage = '.$this->oDbCon->paramString($taxPercentage).' 
                                   and '.$this->tableName.'.statuskey in(2,3) ');

        if(!empty($rs)){
        	$objRef = $this->getObjMapping( '', $reftabletype); 
            $rsInvoice = $objRef->searchDataRow(array($objRef->tableName.'.pkey',$objRef->tableName.'.code'),
														'and '.$objRef->tableName.'.pkey = '.$this->oDbCon->paramString($rs[0]['refkey'])
													 );
            $this->addErrorLog(false,'<strong>'.$rsInvoice[0]['code'].'</strong>. '  .$this->errorMsg['invoiceTaxNumber'][4]);
        }
		 
    } 
 
    
     function updateInvoiceTaxNumber($invoiceTaxNumber,$refkey,$reftablekey){
  
        $objRef = $this->getObjMapping( '', $reftablekey);
        
        //update invoice tax number
        $sql = 'update '.$objRef->tableName.' 
				set invoicetaxnumber = '.$this->oDbCon->paramString($invoiceTaxNumber).' 
				where pkey = '.$this->oDbCon->paramString($refkey);
        $this->oDbCon->execute($sql);  
  
    }
	 
    
	function cancelTrans($rsHeader,$copy){
        if ($rsHeader[0]['statuskey'] == 1) return; 
		$id = $rsHeader[0]['pkey']; 
           
		if ($copy)
			$this->copyDataOnCancel($id);	 
      
    }
 
}

?>