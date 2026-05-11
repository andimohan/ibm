<?php

require_once  $_SERVER ['DOCUMENT_ROOT'].'/assets/vendor/autoload.php';   
use PhpOffice\PhpSpreadsheet\Spreadsheet; 
use PhpOffice\PhpSpreadsheet\Spreadsheet\Cell;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;  

class VatOut extends BaseClass {

    function __construct()
    {
        parent::__construct();

        $this->tableName = 'vat_out_header';
        $this->tableNameDetail = 'vat_out_detail';
        $this->tableWarehouse = 'warehouse'; 
        $this->tableStatus = 'transaction_status';
        $this->tableCurrency = 'currency';
        $this->tableTaxType = 'vat_out_tax_type';
        $this->tableVatOutType = 'vat_out_type';
        //$this->tableInvoice = 'emkl_order_invoice_header';
        $this->tableCustomer = 'customer';
        $this->tableCustomCode = 'custom_code';
        $this->uploadFileFolder = 'vat-out/';  
        $this->tableItem = 'item';
        $this->tableTaxServiceCode = 'tax_service_code';
        $this->tableTaxServiceUnit = 'tax_service_unit';

        
        $invoiceObj = $this->getInvoiceObj();
        $this->tableInvoice = $invoiceObj->tableName;
            
        //if(in_array(DOMAIN_NAME, array('trioeaglelogistic.wintera.co.id'))){
        //     $this->tableInvoice = 'emkl_order_invoice_header';
        //}else{
        //      if (PLAN_TYPE['categorykey'] == COMPANY_TYPE['trucking']) {	
        //            //Trucking
        //            $this->tableInvoice = 'trucking_service_order_invoice_header';
        //        } else {
        //            $this->tableInvoice = 'emkl_order_invoice_header';
        //        }
        //}
        

        $this->securityObject = 'VatOut';

        $this->isTransaction = true;
        $this->newLoad = true;

        $this->arrDataDetail = array();
        $this->arrDataDetail['pkey'] = array('hidDetailKey');
        $this->arrDataDetail['refkey'] = array('pkey', 'ref');
        $this->arrDataDetail['invoicekey'] = array('hidInvoiceKey',array('mandatory'=>true));
        // $this->arrDataDetail['invoicenumber'] = array('invoiceNumber');
        // $this->arrDataDetail['transactiontype'] = array('transactionType');
        // $this->arrDataDetail['currencykey'] = array('selCurrency');
        // $this->arrDataDetail['invoicedate'] = array('invoiceDate', 'date');
        $this->arrDataDetail['npwp'] = array('npwp');
        $this->arrDataDetail['customername'] = array('customerName');
        $this->arrDataDetail['address'] = array('address'); 
        $this->arrDataDetail['total'] = array('total', 'number');
        $this->arrDataDetail['beforetaxtotal'] = array('beforeTaxTotal', 'number');
        $this->arrDataDetail['taxvalue'] = array('taxValue', 'number');
        $this->arrDataDetail['isrevision'] = array('chkIsRevision');
        $this->arrDataDetail['isvoid'] = array('chkIsVoid');
        $this->arrDataDetail['taxinvoicekey'] = array('hidTaxInvoiceKey');
        $this->arrDataDetail['transactiontypekey'] = array('selDetailTransactionTypeCodeKey');
        $this->arrDataDetail['additionalinfokey'] = array('selAdditionalInfo');
        $this->arrDataDetail['facilitystampkey'] = array('selFacilityStamp');
        

        $this->arrData = array();
        $this->arrData['pkey'] = array('pkey', array('dataDetail' => array('dataset' => $this->arrDataDetail)));
        $this->arrData['code'] = array('code');
        $this->arrData['trdate'] = array('trDate', 'date');
		$this->arrData['vatouttypekey'] = array('selVatOutType'); 
        $this->arrData['warehousekey'] = array('selWarehouseKey'); 
        $this->arrData['taxperiod'] = array('taxPeriod','date'); 
        $this->arrData['transactiontypekey'] = array('selTransactionTypeCodeKey');
        $this->arrData['taxpercentage'] = array('taxPercentage', 'decimal');
        $this->arrData['document'] = array('document');
        $this->arrData['offtax'] = array('selOffTax');
        $this->arrData['statuskey'] = array('selStatus');
        $this->arrData['additionalid'] = array('additionalId');
        $this->arrData['trdesc'] = array('trDesc');
        $this->arrData['uploadfilename'] = array('fileName');
        $this->arrData['uploadfiledate'] = array('uploadDate', 'date'); 
        
            
		$this->arrData['file'] = array('item-file-uploader',array('datatype' => 'file', 'uploadFolder' => $this->uploadFileFolder,  'token' => 'token-item-file-uploader', 'fileName' => 'item-file-uploader'));
   
		
        $this->allowedStatusForEdit = array(1,2);
	
		
          $this->actionMenu = array();  
            $function = '  
                    var phpDataListFile = tabParam[selectedTabId].phpDataListFile; 

                    if (selectedPkey.length == 0){
                        showMsgDialog ("Anda belum memilih data yang hendak di generate."); 
                        break ;
                    }

                    var msg =  "Anda yakin akan melakukan generate ulang XML ?";

                    $( "#dialog-message" ).html(msg);
                    $( "#dialog-message" ).dialog({
                      width: 300,
                      modal: true,
                      title:"Konfirmasi Generate XML", 
                      open: function() {
                          $(this).closest(\'.ui-dialog\').find(\'.ui-dialog-buttonpane button:last\').focus();
                      },
                      buttons : {
                          OK : function (){
                                     
                                     $.ajax({
                                        type: "POST",
                                        url:  phpDataListFile,
                                        data:{action:"regenerateXML", 
                                            selectedPkey:selectedPkey
                                        },
                                    }).done(function( data ) {   
										 alert("XML telah berhasil digenerate ulang");
                                    });  

                                    $( this ).dialog( "close" );
                          },
                          Cancel : function (){ 
                            $( this ).dialog( "close" );
                          }
                      },
                      });
            ';

            
		array_push($this->actionMenu,array('code' => 'regenerateXML', 'name' => 'Regenerate XML',  'icon' => 'resync', 'function' => $function)); 

			
		
        $this->printMenu = array();
        array_push($this->printMenu, array('code' => 'printTransaction', 'name' => $this->lang['printTransaction'],  'icon' => 'print', 'url' => 'print/vatOut'));
	 	array_push($this->printMenu, array('code' => 'downloadXML', 'name' => 'Download XML',  'icon' => 'download', 'url' => 'downloadVatOut'));

        $this->arrDataListAvailableColumn = array();
        array_push($this->arrDataListAvailableColumn, array('code' => 'code', 'title' => 'code', 'dbfield' => 'code', 'default' => true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'trdate', 'title' => 'date', 'dbfield' => 'trdate', 'align' => 'center', 'format' => 'date', 'default' => true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'warehouse', 'title' => 'warehouse', 'dbfield' => 'warehousename', 'default' => true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'taxperiod', 'title' => 'taxPeriod', 'dbfield' => 'taxperiod', 'default' => true, 'width' => 200,'format' => 'monthPeriod')); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'vatouttypename', 'title' => 'transactionType', 'dbfield' => 'vatouttypename', 'default' => true, 'width' => 200)); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'status', 'title' => 'status', 'dbfield' => 'statusname', 'default' => true, 'width' => 150));


        $this->arrSearchColumn = array();
        array_push($this->arrSearchColumn, array('Kode', $this->tableName . '.code'));
        array_push($this->arrSearchColumn, array('Tanggal', $this->tableName . '.trdate'));
        array_push($this->arrSearchColumn, array('Gudang', $this->tableWarehouse . '.name'));
        array_push($this->arrSearchColumn, array('Tahun Pajak', $this->tableName . '.taxperiod'));
        array_push($this->arrSearchColumn, array('Status', $this->tableStatus . '.status'));
        array_push($this->arrSearchColumn, array('Vat Out Type', $this->tableVatOutType . '.name'));


        $this->includeClassDependencies(array(
            'SalesOrder.class.php',
            'Warehouse.class.php',
            'Currency.class.php',
            'Tax.class.php',
            'TruckingServiceOrderInvoice.class.php',
            'EMKLOrderInvoice.class.php',
            'Customer.class.php',
			'ChartOfAccount.class.php'
        ));

        $this->overwriteConfig();
    
    }


    function getQuery()
    {
        return '
				select
					' . $this->tableName . '.*,
                   	' . $this->tableStatus . '.status as statusname,
                    ' . $this->tableWarehouse . '.name as warehousename, 
					'. $this->tableVatOutType . '.name as vatouttypename
				from 
					' . $this->tableStatus . ', 
					' . $this->tableName . '
						left join ' . $this->tableWarehouse . ' on ' . $this->tableName . ' .warehousekey =' . $this->tableWarehouse . '.pkey
						left join ' . $this->tableVatOutType . ' on ' . $this->tableName . '.vatouttypekey =' . $this->tableVatOutType . '.pkey
				where  		
					' . $this->tableName . '.statuskey = ' . $this->tableStatus . '.pkey and
                    ' . $this->tableName . '. warehousekey =' . $this->tableWarehouse . '.pkey  
 		' . $this->criteria;
    }

    function getDetailWithRelatedInformation($pkey, $criteria = '')
    {
        $sql = 'select
	   			' . $this->tableNameDetail . '.*, 
                ' . $this->tableInvoice .'.code as invoicecode,
                ' . $this->tableInvoice .'.currencykey as currencykey,
                ' . $this->tableInvoice .'.trdate as invoicedate,
                ' . $this->tableInvoice .'.customerkey as customerkey, 
                ' . $this->tableInvoice . '.customcodekey as customcodekey, 
                ' . $this->tableCustomCode . '.name as transactiontype 
			  from
			  	' . $this->tableNameDetail . '  
                left join ' . $this->tableInvoice . ' on ' . $this->tableNameDetail . '.invoicekey =' . $this->tableInvoice . '.pkey 
                left join ' . $this->tableCustomCode . ' on ' . $this->tableInvoice . '.customcodekey =' . $this->tableCustomCode . '.pkey 
			  where
			  	' . $this->tableNameDetail . '.refkey in (' . $this->oDbCon->paramString($pkey, ',') . ') ';

        $sql .= $criteria;
 
        return $this->oDbCon->doQuery($sql);
    }

    function getTaxType($id = ''){
        $sql = 'select   '. $this->tableTaxType .'.* from '. $this->tableTaxType;
		
		if($id <> '')
			$sql .= ' where '. $this->tableTaxType . '.pkey = '.$this->oDbCon->paramString($id);
			
        return $this->oDbCon->doQuery($sql);
    }

	function getVatOutType($id = ''){
        $sql = 'select   '. $this->tableVatOutType .'.* from '. $this->tableVatOutType;
		
		if($id <> '')
			$sql .= ' where '. $this->tableVatOutType . '.pkey = '.$this->oDbCon->paramString($id);
			
        return $this->oDbCon->doQuery($sql);
    }

 
    function validateConfirm($rsHeader) {
 
        $warehouse = new Warehouse();
        
        $id = $rsHeader[0]['pkey'];
     
          
        $truckingServiceOrderInvoice = new TruckingServiceOrderInvoice();
		$emklOrderInvoice = new EMKLOrderInvoice();
  		$rsDetail = $this->getDetailById($id);
      
        $arrInvoiceKey = array_column($rsDetail,'invoicekey');
        $arrTaxInvoiceDetailKey = array_column($rsDetail,'taxinvoicekey');
 
		$objInvoice = $this->getInvoiceObj(); // agar nanti bisa di switch
		$tablekey = $this->getTableKeyAndObj($objInvoice->tableName)['key'];
		
        $taxPeriod = $rsHeader[0]['taxperiod'];  
        $warehousekey = $rsHeader[0]['warehousekey'];    
 
		$rsInvoice = $objInvoice->searchDataRow(array($objInvoice->tableName.'.pkey', $objInvoice->tableName.'.code', $objInvoice->tableName.'.trdate',
															$objInvoice->tableName.'.warehousekey', $objInvoice->tableName.'.statuskey'
														), ' and '.$objInvoice->tableName.'.pkey in (' . $this->oDbCon->paramString(array_unique($arrInvoiceKey),',').')'
													);
		$rsInvoice = array_column($rsInvoice,null,'pkey');
	
        // validasi Vat Out
        $rsVatOut = $this->getVatOutByInvoiceKey(array_column($rsInvoice,'pkey'), array(1,2,3), $id);
        if(!empty($rsVatOut)){
            $invoiceCode =  array();
            foreach($rsVatOut as $row){
                $index = $row['invoicecode'].' - '. $row['code'];
                $invoiceCode[$index] = $row['invoicecode'].' - '. $row['code'];
            }
//            implode(', ', array_unique(array_column($rsVatOut, 'invoicecode')));
            $this->addErrorLog(false,'<b>'.$rsHeader[0]['code'].'.</b><br>'. $this->errorMsg['vatOut'][7].'<br>'.implode(', ',$invoiceCode));
        }
         
		// cek period harus sama
        $monthPeriod = $this->formatDBDate($taxPeriod,'m');
        $yearPeriod = $this->formatDBDate($taxPeriod,'Y');
            
 
        $trTaxPeriodDate =  $this->formatDBDate($taxPeriod,'Ym01');
  
		// nanti perlu tambahan informasi no inv yg mana
		if ( $rsHeader[0]['vatouttypekey'] == VAT_OUT_TYPE['ASSIGN']){
			if (!$this->transactionTaxTypeAllowed($tablekey,$rsHeader[0]['transactiontypekey'], $arrInvoiceKey))
					$this->addErrorLog(false, '<b>'.$rsHeader[0]['code'].'.</b> '.$this->errorMsg['vatOut'][5]);
		}
		
		$arrInvoice = array();
          
		foreach($rsDetail as $key=>$row){
		
			$invoiceRow = $rsInvoice[$row['invoicekey']];
                

			if(!in_array($row['invoicekey'],$arrInvoice )) 
				array_push($arrInvoice, $row['invoicekey']);
			 else
				$this->addErrorLog(false, '<b>'.$rsHeader[0]['code'].', '.$invoiceRow['code'].'</b>. '.$this->errorMsg[215]);
			 
			
			$rowMonth = date("m",strtotime($invoiceRow['trdate']));
			$rowYear = date("Y",strtotime($invoiceRow['trdate']));
			             
            
			$hidTaxInvoiceKey = $row['taxinvoicekey']; 
            $trInvoiceDate =  $this->formatDBDate($invoiceRow['trdate'],'Ymd');
 
		
			// validasi gudang
			if ($warehousekey <> $invoiceRow['warehousekey'] )
			     $this->addErrorLog(false,'<b>'.$rsHeader[0]['code'].', '.$invoiceRow['code'].'</b>. '.$this->errorMsg['vatOut'][3]);
	        
			// validasi statuskey dalam (2,3)
			if(!in_array($invoiceRow['statuskey'], array(2,3)))
				$this->addErrorLog(false,'<b>'.$rsHeader[0]['code'].', '.$invoiceRow['code'].'</b>. '.$this->errorMsg[228]);

 
			// validasi periode COA
		}
		
		
		// cek periode jurnal
		$coa = new ChartOfAccount(); 
        $rsRunningPeriod = $coa->rsRunningPeriod;
		$runningDate =  $this->formatDBDate($rsRunningPeriod[0]['runningmonth'],'Ym01');  
        $trdateDBFormat =  $this->formatDBDate($taxPeriod,'Y-m-01');
           
        if ($trTaxPeriodDate < $runningDate)
            $this->addErrorLog(false,'<b>'.$rsHeader[0]['code'].'</b>. '.$this->errorMsg['generalJournal'][6]);

        //validasi period di kunci
//         if($coa->inLockedPeriod($trdateDBFormat))
//            $this->addErrorLog(false,'<b>'.$rsHeader[0]['code'].'</b>. '.$this->errorMsg['generalJournal'][7]);
          

    }


    function getCompanyType(){
        
        // gk bisa pake yg bawah, karena pakenya koma
        if(in_array(DOMAIN_NAME, array('trioeaglelogistic.wintera.co.id','airtel.wintera.co.id','marvel.wintera.co.id')))
            return   COMPANY_TYPE['forwarding'];
        
        if (PLAN_TYPE['categorykey'] == COMPANY_TYPE['trucking'] && PLAN_TYPE['subcategorykey'] == COMPANY_TYPE['trucking']) { 
			return  COMPANY_TYPE['trucking'];
		} else if (PLAN_TYPE['categorykey'] == COMPANY_TYPE['forwarding'] || PLAN_TYPE['subcategorykey'] == COMPANY_TYPE['forwarding']) { 
            // pake atau subcategory, utk support model lama seperti TEL
			return   COMPANY_TYPE['forwarding'];
		}else{
            return  COMPANY_TYPE['retail'];
        }
    }
    
	function getInvoiceObj(){

        // karena ketika init, blm include, error
        $this->includeClassDependencies(array(
            'SalesOrder.class.php', 
            'TruckingServiceOrderInvoice.class.php',
            'EMKLOrderInvoice.class.php'
        ));
        
        
        $companyType = $this->getCompanyType();
        switch($companyType){
            case     COMPANY_TYPE['trucking'] : return new TruckingServiceOrderInvoice();
                                                break;
            case     COMPANY_TYPE['forwarding'] : return new EMKLOrderInvoice();
                                                break;
            default :   return new SalesOrder();
                        break;
        }
      
	}
    
    
    
    function getReference($rsInvoice){
        
         
        $companyType = $this->getCompanyType();
        
        $arrInvoiceKey = array_column($rsInvoice,'pkey');
        
         switch($companyType){
            case     COMPANY_TYPE['trucking'] : $objRef = new TruckingServiceOrderInvoice();
                                                $rsDetail = $objRef->getJODetail($arrInvoiceKey); 
                                                break;
            case     COMPANY_TYPE['forwarding'] : $objRef = new EMKLOrderInvoice();
                                                $rsDetail = $objRef->getJODetail($arrInvoiceKey); 
                                                break;
            default :   $objRef = new SalesOrder();
                                  $invoiceRefColumn = 'pkey'; // nanti di sesuaikan lg
                                  $rsDetail = array();
                        break;
        }
        
        
        
        
//        $rs = $objRef->searchDataRow(array( $objRef->tableName.'.pkey',$objRef->tableName.'.code'),
//                                            ' and '. $objRef->tableName.'.pkey in ('. $this->oDbCon->paramString($arrJOKey,',') .')');
//        
//        $this->setLog( ' and '. $objRef->tableName.'.pkey in '.$this->oDbCon->paramString($arrJOKey,','),true);
//        $this->setLog($rs,true);
        
        
        return $rsDetail;
    }
	 
	
    function confirmTrans($rsHeader)  {
		  
		$pkey = $rsHeader[0]['pkey']; 
        $this->printData($pkey);		 
 
    }
 
    
     function afterStatusChanged($rsHeader){    
        // retrieve latest status
        $rsHeader = $this->getDataRowById($rsHeader[0]['pkey']);
        $pkey = $rsHeader[0]['pkey'];
          
         
        if(in_array($rsHeader[0]['statuskey'], array(2,4))){
            
            $rsDetail = $this->getDetailById($pkey);
            $invoicekey = array_column($rsDetail,'invoicekey');
            
            if($rsHeader[0]['statuskey'] == TRANSACTION_STATUS['konfirmasi']){
                // perlu cek kalo backconfirmed masalah tdk nanti
                $vatOutKey = $pkey; 
            }else if($rsHeader[0]['statuskey'] == TRANSACTION_STATUS['batal']){
                $vatOutKey = 0;
            }

            
            $sql = 'update '.$this->tableInvoice.' set refvatoutkey = '.$this->oDbCon->paramString($vatOutKey).' 
                    where pkey in ('.$this->oDbCon->paramString($invoicekey,',').')';
            
            
            $this->oDbCon->execute($sql);
        } 
            
             
        
    }
    
    function afterUpdateData($arrParam,$action){
		$pkey = $arrParam['pkey']; 
        $this->printData($pkey);
    }
	
    
    function cancelTrans($rsHeader, $copy, $GLCancelDate = '00 / 00 / 0000'){
		
		$id = $rsHeader[0]['pkey'];

		if ($copy)
			$this->copyDataOnCancel($id);	 
    }

    function getVatOutByInvoiceKey($arrInvoiceKey, $arrStatusKey = array(2,3), $pkeyExclude=0){
        $sql = 'select 
                    '.$this->tableName.'.pkey,
                    '.$this->tableName.'.code,
                    '.$this->tableName.'.trdate,
                    '.$this->tableInvoice.'.code as invoicecode
                from 
                    '.$this->tableName.','.$this->tableNameDetail.', '.$this->tableInvoice.'
                where
                    '.$this->tableName.'.pkey = '.$this->tableNameDetail.'.refkey and
                    '.$this->tableName.'.statuskey in ('.$this->oDbCon->paramString($arrStatusKey,',').')  and
                    '.$this->tableNameDetail.'.invoicekey =  '.$this->tableInvoice.'.pkey and
                    '.$this->tableNameDetail.'.invoicekey in  ('.$this->oDbCon->paramString($arrInvoiceKey,',').') 
                ';
        
        if(!empty($pkeyExclude))
            $sql .=' and '.$this->tableName.'.pkey <> '.$this->oDbCon->paramString($pkeyExclude);
        
        $rs = $this->oDbCon->doQuery($sql);
        
        return $rs;
    }
	
    function validateForm($arr, $pkey = '') {
        $arrayToJs = parent::validateForm($arr, $pkey);
		 
		// khusus status menunggu
//		if(!isset($arr['selStatus'])) return $arrayToJs;
		
		// hanya validasi jika add atau status 1
	 
//		if(!empty($arr['hidId'])){
//			$rs = $this->searchDataRow(array($this->tableName.'.statuskey'),
//									   		' and '.$this->tableName.'.pkey in (' . $this->oDbCon->paramString($arr['hidId'],',').')'
//									 );
//			if (!empty($rs) && $rs[0]['statuskey'] > 1) return $arrayToJs;
//		}
		
        $truckingServiceOrderInvoice = new TruckingServiceOrderInvoice();
		$emklOrderInvoice = new EMKLOrderInvoice(); 
  
		// biar nanti bisa di switch
		$objInvoice = $this->getInvoiceObj();
		$tablekey = $this->getTableKeyAndObj($objInvoice->tableName)['key'];
		
        $taxPeriod = $arr['taxPeriod'];  
        $warehousekey = $arr['selWarehouseKey'];   
 
	    $rsInvoice = $objInvoice->searchDataRow(array($objInvoice->tableName.'.pkey', $objInvoice->tableName.'.code', $objInvoice->tableName.'.trdate',
														$objInvoice->tableName.'.warehousekey',  $objInvoice->tableName.'.statuskey'
													), ' and '.$objInvoice->tableName.'.pkey in (' . $this->oDbCon->paramString(array_unique($arr['hidInvoiceKey']),',').')'
												);
		 
		$rsInvoice = array_column($rsInvoice,null,'pkey');
 
		// cek period harus sama
		$monthPeriod = str_replace('\'','', $this->oDbCon->paramDate($arr['taxPeriod'], ' / ', 'm'));
		$yearPeriod = str_replace('\'','', $this->oDbCon->paramDate($arr['taxPeriod'], ' / ', 'Y'));
 
        $trTaxPeriodDate =  str_replace('\'','', $this->oDbCon->paramDate($taxPeriod, ' / ', 'Ym01'));
		  
		
		// validasi jenis tax
		// hanya utk assign
		if ( $arr['selVatOutType'] == VAT_OUT_TYPE['ASSIGN']){
			if (!$this->transactionTaxTypeAllowed($tablekey,$arr['selTransactionTypeCodeKey'], $arr['hidInvoiceKey']))
				$this->addErrorList($arrayToJs, false, $this->errorMsg['vatOut'][5]);
		}

		$arrInvoice = array();
		foreach($arr['hidInvoiceKey'] as $key=>$row){
		
			$invoiceRow = $rsInvoice[$row]; 
			
			if(!in_array($row,$arrInvoice )) 
				array_push($arrInvoice, $row);
			 else
				$this->addErrorList($arrayToJs, false, '<b>'.$invoiceRow['code'].'</b>. '.$this->errorMsg[215]);
			 
			
			$rowMonth = date("m",strtotime($invoiceRow['trdate']));
			$rowYear = date("Y",strtotime($invoiceRow['trdate']));

			$hidTaxInvoiceKey = $arr['hidTaxInvoiceKey'][$key]; 
			 
//            $trInvoiceDate =  $this->formatDBDate($invoiceRow['trdate'],'Ymd');
 
			// validasi gudang
			if ($warehousekey <> $invoiceRow['warehousekey'] )
				  $this->addErrorList($arrayToJs, false, '<b>'.$invoiceRow['code'].'</b>. '.$this->errorMsg['vatOut'][3]);
 
			// validasi statuskey dalam (2,3)
			if(!in_array($invoiceRow['statuskey'], array(2,3)))
				  $this->addErrorList($arrayToJs, false, '<b>'.$invoiceRow['code'].'</b>. '.$this->errorMsg[228]);
  
		}
		
		
		// cek periode jurnal
		$coa = new ChartOfAccount(); 
        $rsRunningPeriod = $coa->rsRunningPeriod;
		$runningDate =  $this->formatDBDate($rsRunningPeriod[0]['runningmonth'],'Ym01');   
        $trdateDBFormat =  str_replace('\'','', $this->oDbCon->paramDate($arr['taxPeriod'], ' / ', 'Y-m-01'));
           
        if ($trTaxPeriodDate < $runningDate)
            $this->addErrorList($arrayToJs,false,$this->errorMsg['generalJournal'][6]);
        
        //validasi period di kunci
//         if($coa->inLockedPeriod($trdateDBFormat))
//			$this->addErrorList($arrayToJs,false,$this->errorMsg['generalJournal'][7]);
         

	  
        return $arrayToJs;
    }
    
    
	function transactionTaxTypeAllowed($tablekey, $transactionTypeKey, $arrInvoiceKey){
		$taxPercentage = $this->getTaxType($transactionTypeKey)[0]['taxpercentage']; 
		
		
		if($taxPercentage < 0) return true;
		 
		 // search semua detail dr invoicekey, lalu distinct / unique
		$objRef = $this->getInvoiceObj();
		$arrTaxPercentage = $objRef->getTaxPercentageTypeForVatOut($arrInvoiceKey);
		 
		
		foreach($arrTaxPercentage as $row)
			if ($row <> $taxPercentage) return false;
		 
		return true;
	}
	
    function normalizeParameter($arrParam, $trim = false)  {
		 
		$arrParam['taxPeriod'] =  date("01 / m / Y",strtotime($arrParam['taxPeriod']));
			   
        $arrParam = parent::normalizeParameter($arrParam, true);
 
        return $arrParam;
    } 
     
	
    function printData($pkey) {
		 
        $vatOutRoundType = $this->loadSetting('vatOutRoundType');
        $vatOutGroupType = $this->loadSetting('vatOutGroupType');
        
        if(empty($vatOutGroupType)) $vatOutGroupType = 0; // buat validasi kalo ''
        
        // 0. Normal
        // 1. group header langsung, nama layanan diambil dari item pertama saja
        // 2. group per nama alias (next feaatures)
        
        $useDetail = (in_array($vatOutGroupType, array(0,2))) ? true : false;
            
         
        $customer = new Customer();
		
		$objRef = $this->getInvoiceObj();
		$rsHeader = $this->getDataRowById($pkey);
		
		// utk PPN dibebaskan
		$addTax = (in_array($rsHeader[0]['transactiontypekey'],array(7,8))) ? true : false;
		 
		$rsDetail = $this->getDetailById($pkey);
		
		$arrInvoiceKey = array_column($rsDetail, 'invoicekey');
		   
		$rsInvoice = $objRef->searchData('','',true,' and '.$objRef->tableName.'.pkey  in ('.$this->oDbCon->paramString($arrInvoiceKey,',').')');
        
        $rsCustomer = $customer->searchData('','',true,' and ' . $customer->tableName.'.pkey in ('.$this->oDbCon->paramString(array_column($rsInvoice,'customerkey'),',').') ');
        $rsCustomer = array_column($rsCustomer,null,'pkey');
        
        foreach($rsInvoice as $key=>$invoiceRow){
            $arrCustomer = $rsCustomer[$invoiceRow['customerkey']];
            $rsInvoice[$key]['tintypekey'] = $arrCustomer['tintypekey'];
            $rsInvoice[$key]['tintypename'] = $arrCustomer['tintypename'];
            $rsInvoice[$key]['tku'] = $arrCustomer['tku'];
            $rsInvoice[$key]['nik'] = $arrCustomer['nik'];
            $rsInvoice[$key]['passport'] = $arrCustomer['passport'];
            $rsInvoice[$key]['countrycode'] = (!empty($arrCustomer['countrycode'])) ? $arrCustomer['countrycode'] : 'IDN';
            $rsInvoice[$key]['otherdocuments'] = $arrCustomer['otherdocuments'];
            $rsInvoice[$key]['taxid'] = $arrCustomer['taxid'];
            $rsInvoice[$key]['taxregistrationname'] = $arrCustomer['taxregistrationname'];
            $rsInvoice[$key]['taxregistrationaddress'] = $arrCustomer['taxregistrationaddress'];
            $rsInvoice[$key]['customeremail'] = $arrCustomer['email'];
            $rsInvoice[$key]['customername'] = $arrCustomer['taxregistrationname'];
            $rsInvoice[$key]['customeraddress'] = $arrCustomer['taxregistrationaddress'];
        }
        
        
		$rsInvoice = array_column($rsInvoice,null,'pkey');
          
        if($useDetail){ 
            $rsInvoiceItemDetail = $objRef->getItemDetailByHeaderKey($arrInvoiceKey);
            $rsInvoiceItemDetail = $objRef->reindexDetailCollections($rsInvoiceItemDetail,'refheaderkey'); 	
        }else{
            // gk perlu, sudah ad diatas
            //$rsInvoice = $objRef->searchDataRow(array($objRef->tableName.'.pkey',
            //                                       $objRef->tableName.'.beforetaxtotal',
            //                                       $objRef->tableName.'.ispriceincludetax',
            //                                       $objRef->tableName.'.taxpercentage',
            //                                       $objRef->tableName.'.taxvalue',
            //                                       $objRef->tableName.'.grandtotal'
            //                                       ), ' and '.$objRef->tableName.'.pkey  in ('.$this->oDbCon->paramString($arrInvoiceKey, ',') .' )');
            //
            //$rsInvoice = array_column($rsInvoice,null,'pkey');
        }
		
	  	//dummy data
		$companyNPWP = $this->loadSetting('companyTaxRegistrationNumber');
        $companyNPWP = str_replace('-','',$companyNPWP);
        $companyNPWP = str_replace('.','',$companyNPWP);
        $companyNPWP = str_replace(' ','',$companyNPWP); 
        $companyNPWP = sprintf('%016d',$companyNPWP);
        
        $companyTKU = sprintf('%06d',$this->loadSetting('companyTKU'));
  
        $arrData = array();
        $arrData['TIN'] = $companyNPWP;
        $arrData['ListOfTaxInvoice'] = array();  
        
        $rsAdditionalInfo = $this->getTaxAdditionalInfo();
        $rsAdditionalInfo = array_column($rsAdditionalInfo,'name','pkey');
        $rsFacilityStamp = $this->getTaxFacilityStamp();
        $rsFacilityStamp = array_column($rsFacilityStamp,'name','pkey');

        if($useDetail){ 
            // cari service code
            $arrItemKey = array();
            foreach($rsDetail as $row){ 
                   $invoiceRow = $rsInvoice[$row['invoicekey']];
                   $itemDetailRow = $rsInvoiceItemDetail[$invoiceRow['pkey']];
                   $arrItemKey = array_merge($arrItemKey, array_column($itemDetailRow,'itemkey')); 
            }
            $arrItemKey  = array_unique($arrItemKey);
            $rsServiceCode = $this->getItemServiceCode($arrItemKey);
            $rsServiceCode = array_column($rsServiceCode,'taxservicecode','pkey');


            // cari service unit 
            $rsServiceUnit = $this->getItemServiceUnit($arrItemKey);
            $rsServiceUnit = array_column($rsServiceUnit,'taxserviceunit','pkey');
        }
        
             
        $refDescPattern = $this->loadSetting('TaxDescriptionPattern');
        if (empty($refDescPattern)) $refDescPattern = '{{INV_CODE}}';

        $customDocPattern = $this->loadSetting('TaxCustomDocPattern');
        if (empty($customDocPattern)) $customDocPattern = '-';
        
        // kalo ad pattern JO_CODE
        $hasJOCode = (strpos($refDescPattern, '{{JO_CODE}}') === false) ? false : true;
        
        if ($hasJOCode) {  
            $rsJODetail = $this->getReference($rsInvoice);
            $rsJODetail = $this->reindexDetailCollections($rsJODetail,'refkey'); // bisa saja satu invoice lebih dari 1 JO
        }
        
        foreach($rsDetail as $row){ 
             
			$invoiceRow = $rsInvoice[$row['invoicekey']];
             
            $customerTIN = '0000000000000000';
            $customerTINTKU = '000000';
            $customerOtherDocs = '-';
            if (in_array($invoiceRow['tintypekey'], array(TIN_TYPE['TIN'], TIN_TYPE['NIK']))){
                  
                    $npwp = $invoiceRow['taxid'];
                    $npwp = str_replace('-','',$npwp);
                    $npwp = str_replace('.','',$npwp);
                    $npwp = sprintf('%016d',$npwp);
                
                    $invoiceRow['tku'] = sprintf('%06d',$invoiceRow['tku']);
                    $customerTIN = $npwp;
                    $customerTINTKU = $npwp.$invoiceRow['tku'];
                
                    $customerOtherDocs = $invoiceRow['nik'];
            }else{
                 
                $customerOtherDocs = (!empty($invoiceRow['passport'])) ? $invoiceRow['passport'] : $invoiceRow['otherdocuments'] ;
                if(empty($customerOtherDocs)) $customerOtherDocs = '-';
            }
            
            $refCode = $refDescPattern;
            
            $refCode = str_replace ('{{INV_CODE}}',$invoiceRow['code'],$refCode );
            
            if($hasJOCode){ 
                $joRow = $rsJODetail[$row['invoicekey']];  
                $joCode = array_unique(array_column($joRow,'jocode'));
                
                $refCode = str_replace ('{{JO_CODE}}',implode(', ',$joCode),$refCode );  
            }
            
            $customCode=  str_replace ('{{INV_CODE}}',$invoiceRow['code'],$customDocPattern); 
            
            $arrRow = array();
            $arrRow['TaxInvoiceDate'] =  $invoiceRow['trdate'];
            $arrRow['TaxInvoiceOpt'] =  'Normal';
            $arrRow['TrxCode'] =  '0'.$rsHeader[0]['transactiontypekey'];
            $arrRow['AddInfo'] = (isset($rsAdditionalInfo[$row['additionalinfokey']])) ? $rsAdditionalInfo[$row['additionalinfokey']] : '';
		    $arrRow['CustomDoc'] = $customCode; // nanti diudpate belakangan
            $arrRow['RefDesc'] = $refCode; // nanti diudpate belakangan
            $arrRow['FacilityStamp'] =(isset($rsFacilityStamp[$row['facilitystampkey']])) ? $rsFacilityStamp[$row['facilitystampkey']] : '';
            $arrRow['SellerIDTKU'] = $companyNPWP . $companyTKU;
            $arrRow['BuyerTin'] = $customerTIN;
            $arrRow['BuyerDocument'] = $invoiceRow['tintypename'];
            $arrRow['BuyerCountry'] = $invoiceRow['countrycode'];
            $arrRow['BuyerDocumentNumber'] = $customerOtherDocs;
            $arrRow['BuyerName'] = str_replace(',',' ',htmlspecialchars_decode($invoiceRow['customername']));
            $arrRow['BuyerAdress'] = str_replace(',',' ',htmlspecialchars_decode($invoiceRow['customeraddress']));
            $arrRow['BuyerEmail'] = (!empty($invoiceRow['customeremail'])) ? $invoiceRow['customeremail'] : '-';
            $arrRow['BuyerIDTKU'] = $customerTINTKU;
        
             
			// hitung total dulu
            
            $arrRow['ListOfGoodService'] = array();
            
            if($useDetail){ 
                // detail item 
                $itemDetailRow = $rsInvoiceItemDetail[$invoiceRow['pkey']];
                foreach($itemDetailRow as $key=>$itemRow){ 

                    if ($itemRow['isreimburse'] == 1) continue; 
                    if(empty($itemRow['currencykey'])) $itemRow['currencykey'] = CURRENCY['idr']; // perlu default karena di TMS tdk ad currency

                    // tembak mati khusus pajak dibebaskan
                    if($addTax && $itemRow['taxdetail'] <= 0) 
                        $itemRow['taxdetail'] = 11;


                    $itemName = (!empty($itemRow['aliasname'])) ? $itemRow['aliasname'] : $itemRow['itemname'];

                    // baseDPP di sistem tergantugn dari currency invoice header.
                    // kalo IDR, maka DPP nya sudah IDR, 
                    // kalo USD, maka DPP nya masih USD
                    // berarti kalo IDR, dicuekin saja, kalo USD maka * dengan rate invoice header
                    $baseDPP = $itemRow['beforetaxdetailvalue'];// * $itemRow['qtyinbaseunit']; // gk perlu kali qty lg
                    if ($invoiceRow['currencykey'] != CURRENCY['idr']) $baseDPP *= $invoiceRow['rate']; // pake currency dan rate invoice header

    //                $dpp = ($itemRow['taxdetail'] == 11) ? (11/12 * $baseDPP) : $baseDPP; 

                    $dpp = $baseDPP;
                    $otherTaxBase = $dpp; // dipiah karena 1.1 beda perhitungannya

                    if($itemRow['taxdetail'] == 11){
                        $dpp = (11/12 * $baseDPP);
                        $otherTaxBase = $dpp;
                        $itemRow['taxdetail'] = 12;
                    }else if($itemRow['taxdetail'] == 1.1){
                        $otherTaxBase = $dpp;
                        $dpp = (11/12 * $baseDPP * 0.1);
                        $itemRow['taxdetail'] = 12;
                    }

                    $arrDetail = array();
                    $arrDetail['Opt'] = 'B';
                    //$arrDetail['Code'] = '000000'; //($rsHeader[0]['transactiontypekey'] == 5) ? '060000' : '080000';

                    $arrDetail['Code'] = (isset($rsServiceCode[$itemRow['itemkey']])) ? $rsServiceCode[$itemRow['itemkey']] : '000000'; //($rsHeader[0]['transactiontypekey'] == 5) ? '060000' : '080000';

                    $arrDetail['Name'] = $itemName;
                   // $arrDetail['Unit'] = 'UM.0033';
                    $arrDetail['Unit'] = (isset($rsServiceUnit[$itemRow['itemkey']])) ? $rsServiceUnit[$itemRow['itemkey']] : 'UM.0033'; //($rsHeader[0]['transactiontypekey'] == 5) ? '060000' : '080000';

                    // pricing selalu dalam currency, jd kalo non IDR harus dikalikan dengan ?? detail atau header

                    $arrDetail['Price'] = $itemRow['priceinunit'] * $itemRow['qtyinbaseunit'];
                    if ($itemRow['currencykey'] != CURRENCY['idr']){ 
                        // harus diuabh kayanya, di trucking blm tentu ad itemRow['rate']
                        $rate = ($invoiceRow['currencykey'] != CURRENCY['idr']) ? $invoiceRow['rate']  : $itemRow['rate'];  
                        $arrDetail['Price'] *= $rate;
                    } 

                    $arrDetail['Price']  = $this->formatNumber( $arrDetail['Price'],2,'','.');

                    $arrDetail['Qty'] = 1;
                    $arrDetail['TotalDiscount'] =0;
                    $arrDetail['TaxBase'] = $this->formatNumber($baseDPP,2,'','.');  
                    $arrDetail['OtherTaxBase'] = $this->formatNumber($otherTaxBase,2,'','.');
                    $arrDetail['VATRate'] = $itemRow['taxdetail'] * 100 / 100;

                    // di round dulu
                    $vatValue =  $itemRow['taxdetail']/100*$dpp;
                    if($vatOutRoundType == 1){  
                        $vatValue = round($vatValue); // normalnya
                    }else{ 
                        // CIF, TWJ $vatOutRoundType == 2 atau blm di set, tetep mau keriting
                        //$arrDetail['VAT'] = $this->formatNumber($itemRow['taxdetail']/100*$dpp,2,'','.');
                    }

                    $arrDetail['VAT'] = $this->formatNumber($vatValue,2,'','.');

                    $arrDetail['STLGRate'] = 0;
                    $arrDetail['STLG'] = 0; 

                    array_push($arrRow['ListOfGoodService'] , array('GoodService' => $arrDetail));

                }
            }else{
                
                // baru buat Praja 
                    $arrDetail = array();
                    $arrDetail['Opt'] = 'B';  
                    $arrDetail['Code'] = '060000';   // tembak dulu biar cepet
                    $arrDetail['Name'] = 'Jasa Angkutan Barang'; // tembak dulu biar cepet
                    $arrDetail['Unit'] = 'UM.0033'; // tembak dulu biar cepet

                    $baseDPP = $invoiceRow['beforetaxtotal'];
                    $dpp = $baseDPP;
                    $otherTaxBase = $dpp; // dipiah karena 1.1 beda perhitungannya

                    if($invoiceRow['taxpercentage'] == 11){
                        $dpp = (11/12 * $baseDPP);
                        $otherTaxBase = $dpp;
                        $invoiceRow['taxpercentage'] = 12;
                    }else if($invoiceRow['taxpercentage'] == 1.1){
                        $otherTaxBase = $dpp;
                        $dpp = (11/12 * $baseDPP * 0.1);
                        $invoiceRow['taxpercentage'] = 12;
                    }
                    
                    $arrDetail['Price'] = $baseDPP;
                    if ($invoiceRow['currencykey'] != CURRENCY['idr']){  
                        $arrDetail['Price'] *= $invoiceRow['rate'];  
                    } 

                    $arrDetail['Price']  = $this->formatNumber( $arrDetail['Price'],2,'','.');

                    $arrDetail['Qty'] = 1;
                    $arrDetail['TotalDiscount'] =0;
                    $arrDetail['TaxBase'] = $this->formatNumber($baseDPP,2,'','.');  
                    $arrDetail['OtherTaxBase'] = $this->formatNumber($otherTaxBase,2,'','.');
                    $arrDetail['VATRate'] = $invoiceRow['taxpercentage'] * 100 / 100;

                    // di round dulu
                    $vatValue =  $invoiceRow['taxpercentage']/100*$dpp;
                    if($vatOutRoundType == 1){  
                        $vatValue = round($vatValue); // normalnya
                    }else{ 
                        // CIF, TWJ $vatOutRoundType == 2 atau blm di set, tetep mau keriting
                        //$arrDetail['VAT'] = $this->formatNumber($itemRow['taxdetail']/100*$dpp,2,'','.');
                    }

                    $arrDetail['VAT'] = $this->formatNumber($vatValue,2,'','.');

                    $arrDetail['STLGRate'] = 0;
                    $arrDetail['STLG'] = 0; 

                    array_push($arrRow['ListOfGoodService'] , array('GoodService' => $arrDetail));
               
            }
             
            array_push($arrData['ListOfTaxInvoice'] , array('TaxInvoice' => $arrRow));
       }
        
        $xmlData = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><TaxInvoiceBulk xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"></TaxInvoiceBulk>');
        $this->arrayToXML($arrData,$xmlData);

        $xmlContent = $xmlData->asXML();
        
//        $this->setLog($xmlContent,true);
        $this->printToXML($xmlContent, 'FP-'.$pkey.'.xml');
//        $this->printToXLS($arrData, 'FP-'.$pkey.'.xlsx');
         
    }
    
    function printToXML($dataToWrite, $fileName){
		
        //path simpan file
        $path = DEFAULT_DOC_UPLOAD_PATH.'FP/';   
        if (!is_dir($path))  mkdir($path, 0755, true); 

        
//        $this->setLog($path.$fileName,true);
		file_put_contents($path.$fileName,$dataToWrite); 
    }
    
    
    function printToXLS($arrData, $fileName){ 
		
        //path simpan file
        $path = DEFAULT_DOC_UPLOAD_PATH.'FP/';  
        if (!is_dir($path))  mkdir($path, 0755, true); 
         
        
        $spreadsheetRespon = new Spreadsheet();
        $writerRespon = new Xlsx($spreadsheetRespon);
        $activeSheet = $spreadsheetRespon->getActiveSheet();
        
        $activeSheet->setTitle("Faktur");
        $activeSheet->setCellValueByColumnAndRow(1, 1,'NPWP Penjual');  
        $activeSheet->setCellValueByColumnAndRow(3, 1, $arrData['TIN']);  

        $row=3;
        $col=1;
        $activeSheet->setCellValueByColumnAndRow($col++, $row, 'BARIS');  
        $activeSheet->setCellValueByColumnAndRow($col++, $row, 'TANGGAL FAKTUR');
        $activeSheet->setCellValueByColumnAndRow($col++, $row, 'JENIS FAKTUR');  
        $activeSheet->setCellValueByColumnAndRow($col++, $row, 'KODE TRANSAKSI');  
        $activeSheet->setCellValueByColumnAndRow($col++, $row, 'KETERANGAN TAMBAHAN');  
        $activeSheet->setCellValueByColumnAndRow($col++, $row, 'DOKUMEN PENDUKUNG');  
        $activeSheet->setCellValueByColumnAndRow($col++, $row, 'REFERENSI');  
        $activeSheet->setCellValueByColumnAndRow($col++, $row, 'CAP FASILITAS');  
        $activeSheet->setCellValueByColumnAndRow($col++, $row, 'ID TKU PENJUAL');  
        $activeSheet->setCellValueByColumnAndRow($col++, $row, 'NPWP/NIK PEMBELI');  
        $activeSheet->setCellValueByColumnAndRow($col++, $row, 'JENIS ID PEMBELI');  
        $activeSheet->setCellValueByColumnAndRow($col++, $row, 'NEGARA PEMBELI');  
        $activeSheet->setCellValueByColumnAndRow($col++, $row, 'NOMOR DOKUMEN PEMBELI'); 
        $activeSheet->setCellValueByColumnAndRow($col++, $row, 'NAMA PEMBELI');  
        $activeSheet->setCellValueByColumnAndRow($col++, $row, 'ALAMAT PEMBELI');  
        $activeSheet->setCellValueByColumnAndRow($col++, $row, 'EMAIL PEMBELI');  
        $activeSheet->setCellValueByColumnAndRow($col++, $row, 'ID TKU PEMBELI');  
             
        $rowNumber=1;
        
        foreach($arrData['ListOfTaxInvoice'] as $dataRow){ 
            $row++;
            $col=1;
            
            $activeSheet->setCellValueByColumnAndRow($col++, $row, $rowNumber);  
            $activeSheet->setCellValueByColumnAndRow($col++, $row, $this->formatDBDate($dataRow['TaxInvoice']['TaxInvoiceDate'],'d/m/Y'));  
            $activeSheet->setCellValueByColumnAndRow($col++, $row, $dataRow['TaxInvoice']['TaxInvoiceOpt']);  
            $activeSheet->setCellValueByColumnAndRow($col++, $row, $dataRow['TaxInvoice']['TrxCode']);  
            $activeSheet->setCellValueByColumnAndRow($col++, $row, $dataRow['TaxInvoice']['AddInfo']);  
            $activeSheet->setCellValueByColumnAndRow($col++, $row, $dataRow['TaxInvoice']['CustomDoc']);  
            $activeSheet->setCellValueByColumnAndRow($col++, $row, $dataRow['TaxInvoice']['RefDesc']);  
            $activeSheet->setCellValueByColumnAndRow($col++, $row, $dataRow['TaxInvoice']['FacilityStamp']);  
            $activeSheet->setCellValueByColumnAndRow($col++, $row, $dataRow['TaxInvoice']['SellerIDTKU']);  
            $activeSheet->setCellValueByColumnAndRow($col++, $row, $dataRow['TaxInvoice']['BuyerTin']);  
            $activeSheet->setCellValueByColumnAndRow($col++, $row, $dataRow['TaxInvoice']['BuyerDocument']);  
            $activeSheet->setCellValueByColumnAndRow($col++, $row, $dataRow['TaxInvoice']['BuyerCountry']);  
            $activeSheet->setCellValueByColumnAndRow($col++, $row, $dataRow['TaxInvoice']['BuyerDocumentNumber']);  
            $activeSheet->setCellValueByColumnAndRow($col++, $row, $dataRow['TaxInvoice']['BuyerName']);  
            $activeSheet->setCellValueByColumnAndRow($col++, $row, $dataRow['TaxInvoice']['BuyerAdress']);  
            $activeSheet->setCellValueByColumnAndRow($col++, $row, $dataRow['TaxInvoice']['BuyerEmail']);  
            $activeSheet->setCellValueByColumnAndRow($col++, $row, $dataRow['TaxInvoice']['BuyerIDTKU']);  
            
            $rowNumber++;
        }
        
        $row++;
        $activeSheet->setCellValueByColumnAndRow(1, $row,'END');  
        
        // sheet 2
        // Zero based, so set the second tab as active sheet
        $spreadsheetRespon->createSheet();
        $spreadsheetRespon->setActiveSheetIndex(1);
        $activeSheet = $spreadsheetRespon->getActiveSheet();
        $activeSheet->setTitle('DetailFaktur');
         
        
        $row=1;
        $col=1;
        $activeSheet->setCellValueByColumnAndRow($col++, $row, 'BARIS');  
        $activeSheet->setCellValueByColumnAndRow($col++, $row, 'BARANG/JASA');
        $activeSheet->setCellValueByColumnAndRow($col++, $row, 'KODE BARANG/JASA');  
        $activeSheet->setCellValueByColumnAndRow($col++, $row, 'NAMA BARANG/JASA');  
        $activeSheet->setCellValueByColumnAndRow($col++, $row, 'SATUAN UKUR');  
        $activeSheet->setCellValueByColumnAndRow($col++, $row, 'HARGA SATUAN');  
        $activeSheet->setCellValueByColumnAndRow($col++, $row, 'QTY');  
        $activeSheet->setCellValueByColumnAndRow($col++, $row, 'DISKON');  
        $activeSheet->setCellValueByColumnAndRow($col++, $row, 'DPP');  
        $activeSheet->setCellValueByColumnAndRow($col++, $row, 'DPP NILAI LAIN');  
        $activeSheet->setCellValueByColumnAndRow($col++, $row, 'TARIF PPN');  
        $activeSheet->setCellValueByColumnAndRow($col++, $row, 'PPN');  
        $activeSheet->setCellValueByColumnAndRow($col++, $row, 'TARIF PPNBM'); 
        $activeSheet->setCellValueByColumnAndRow($col++, $row, 'PPNBM');
        												
            
        $rowNumber=1; 
        foreach($arrData['ListOfTaxInvoice'] as $dataRow){  
            
            foreach($dataRow['TaxInvoice']['ListOfGoodService'] as $itemRow){ 
                    $row++;
                    $col=1;

                    $activeSheet->setCellValueByColumnAndRow($col++, $row, $rowNumber);  
                    $activeSheet->setCellValueByColumnAndRow($col++, $row, $itemRow['GoodService']['Opt']);   
                    $activeSheet->setCellValueByColumnAndRow($col++, $row, $itemRow['GoodService']['Code']);   
                    $activeSheet->setCellValueByColumnAndRow($col++, $row, html_entity_decode($itemRow['GoodService']['Name']));   
                    $activeSheet->setCellValueByColumnAndRow($col++, $row, $itemRow['GoodService']['Unit']);   
                    $activeSheet->setCellValueByColumnAndRow($col++, $row, $itemRow['GoodService']['Price']);   
                    $activeSheet->setCellValueByColumnAndRow($col++, $row, $itemRow['GoodService']['Qty']);   
                    $activeSheet->setCellValueByColumnAndRow($col++, $row, $itemRow['GoodService']['TotalDiscount']);   
                    $activeSheet->setCellValueByColumnAndRow($col++, $row, $itemRow['GoodService']['TaxBase']);   
                    $activeSheet->setCellValueByColumnAndRow($col++, $row, $itemRow['GoodService']['OtherTaxBase']);   
                    $activeSheet->setCellValueByColumnAndRow($col++, $row, $itemRow['GoodService']['VATRate']);   
                    $activeSheet->setCellValueByColumnAndRow($col++, $row, $itemRow['GoodService']['VAT']);   
                    $activeSheet->setCellValueByColumnAndRow($col++, $row, $itemRow['GoodService']['STLGRate']);   
                    $activeSheet->setCellValueByColumnAndRow($col++, $row, $itemRow['GoodService']['STLG']);   
            }
  
            $rowNumber++;
        }
                     
        $row++;
        $activeSheet->setCellValueByColumnAndRow(1, $row,'END');  
        
        $writerRespon->save($path.$fileName); 
         
    }
    
    
	function getDataGroupingForReport($criteria, $order)
	{
		$sql = 'select
	   			' . $this->tableNameDetail . '.*, 
               ' . $this->tableInvoice . '.code as invoicecode,
               ' . $this->tableInvoice . '.currencykey as currencykey,
               ' . $this->tableInvoice . '.trdate as invoicedate,
               ' . $this->tableInvoice . '.customerkey as customerkey, 
               ' . $this->tableInvoice . '.customcodekey as customcodekey, 
               ' . $this->tableCustomCode . '.name as transactiontype,
               ' . $this->tableCurrency . '.name as currencyname,
					'. $this->tableName .'.*,
					' . $this->tableStatus . '.status as statusname,
               ' . $this->tableWarehouse . '.name as warehousename, 
					' . $this->tableVatOutType . '.name as vatouttypename
			from
				' . $this->tableNameDetail . '  
               left join ' . $this->tableInvoice . ' on ' . $this->tableNameDetail . '.invoicekey =' . $this->tableInvoice . '.pkey 
               left join ' . $this->tableCurrency . ' on ' . $this->tableInvoice . '.currencykey =' . $this->tableCurrency . '.pkey 
               left join ' . $this->tableCustomCode . ' on ' . $this->tableInvoice . '.customcodekey =' . $this->tableCustomCode . '.pkey, 
				'. $this->tableName .'
						left join ' . $this->tableWarehouse . ' on ' . $this->tableName . ' .warehousekey =' . $this->tableWarehouse . '.pkey 
						left join ' . $this->tableVatOutType . ' on ' . $this->tableName . '.vatouttypekey =' . $this->tableVatOutType . '.pkey
						left join ' . $this->tableStatus . ' on ' . $this->tableName . '.statuskey =' . $this->tableStatus . '.pkey
				where 
					' . $this->tableNameDetail . '.refkey = ' . $this->tableName . '.pkey
			';

		$sql .= $criteria;
		$sql .= $order;

		return $this->oDbCon->doQuery($sql);
	}
    
    function getTaxAdditionalInfo(){
        $sql = 'select pkey,name, trdesc,isdefault from tax_additional_info where statuskey = 1';
        return $this->oDbCon->doQuery($sql);
    }

    function getTaxFacilityStamp(){
        $sql = 'select pkey,name, trdesc,isdefault from tax_facility_stamp where statuskey = 1';
        return $this->oDbCon->doQuery($sql);
    }
    
   function getTaxServiceCode($criteria='')
	{
        // as value sekalian buat autcomplete
		$sql = 'select pkey,name,code, code as value, trdesc,isdefault from '.$this->tableTaxServiceCode.'  where statuskey = 1 ';
        
        if(!empty($criteria)) $sql .= ' '.$criteria;
        
		return $this->oDbCon->doQuery($sql);
	}
     
    function getItemServiceCode($pkey){
        
        if(!is_array($pkey))  $pkey = array($pkey);
        
        $sql = 'select 
                        '.$this->tableItem.'.pkey,
                        '.$this->tableItem.'.taxservicecodekey,
                        '.$this->tableTaxServiceCode.'.code as taxservicecode
                from 
                        '.$this->tableItem.','.$this->tableTaxServiceCode.'
                where 
                        '.$this->tableItem.'.pkey in ('.$this->oDbCon->paramString($pkey, ',').') and
                        '.$this->tableItem.'.taxservicecodekey = '.$this->tableTaxServiceCode.'.pkey ';
        
        return $this->oDbCon->doQuery($sql);
        
    }
    
    function getTaxServiceUnit($criteria='')
	{
        // as value sekalian buat autcomplete
		$sql = 'select pkey,name,code, code as value, trdesc,isdefault from '.$this->tableTaxServiceUnit.'  where statuskey = 1 ';
        
        if(!empty($criteria)) $sql .= ' '.$criteria;
        
		return $this->oDbCon->doQuery($sql);
	}
    
    function getItemServiceUnit($pkey){
        
        if(!is_array($pkey))  $pkey = array($pkey);
        
        $sql = 'select 
                        '.$this->tableItem.'.pkey,
                        '.$this->tableItem.'.taxserviceunitkey,
                        '.$this->tableTaxServiceUnit.'.code as taxserviceunit
                from 
                        '.$this->tableItem.','.$this->tableTaxServiceUnit.'
                where 
                        '.$this->tableItem.'.pkey in ('.$this->oDbCon->paramString($pkey, ',').') and
                        '.$this->tableItem.'.taxserviceunitkey = '.$this->tableTaxServiceUnit.'.pkey ';
        
        return $this->oDbCon->doQuery($sql);
        
    }

}
?>
