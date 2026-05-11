<?php

class AccountStatementImport extends BaseClass{
	
    function __construct(){
 
    parent::__construct();

    $this->tableName = 'account_statement_import_header';
    $this->tableNameDetail = 'account_statement_import_detail';
    $this->tableWarehouse = 'warehouse';   
    $this->tableCOA = 'chart_of_account';    
    $this->tableStatus = 'transaction_status';
	$this->uploadFolder = 'account-statement/'; 
    $this->isTransaction = true;    
    $this->newLoad = true;

    $this->securityObject = 'AccountStatementImport';

    $this->arrData = array(); 
    $this->arrData['pkey'] = array('pkey');  
    $this->arrData['code'] = array('code');
    $this->arrData['warehousekey'] = array('selWarehouse');
    $this->arrData['virtualaccount'] = array('virtualAccount');
    $this->arrData['coakey'] = array('hidCOAKey');
    $this->arrData['paymentmethodkey'] = array('selPaymentMethod');
    $this->arrData['trdate'] = array('trDate','date'); 
    $this->arrData['grandtotal'] = array('grandtotal');
	$this->arrData['trdesc'] = array('trDesc'); 
	$this->arrData['file'] = array('item-file-uploader',array('datatype' => 'file', 'uploadFolder' => $this->uploadFolder,  'token' => 'token-item-file-uploader', 'fileName' => 'item-file-uploader')); 
    $this->arrData['statuskey'] = array('selStatus');
 
    $this->arrDataListAvailableColumn = array(); 
    array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 100));
    array_push($this->arrDataListAvailableColumn, array('code' => 'date','title' => 'date','dbfield' => 'trdate','default'=>true, 'width' => 100, 'align' =>'center','format' => 'date'));
    array_push($this->arrDataListAvailableColumn, array('code' => 'desc','title' => 'description','dbfield' => 'trdesc','default'=>true, 'width' => 200 ));
    array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 90));
   	 
    $this->arrSearchColumn = array(); 
	array_push($this->arrSearchColumn, array('Kode', $this->tableName . '.code'));
	array_push($this->arrSearchColumn, array('Tanggal', $this->tableName . '.trdate'));  

		
	array_push($this->filterCriteria, array('title' => $this->lang['warehouse'], 'field' => 'warehousekey'));
        
	$this->printMenu = array();
    array_push($this->printMenu,array('code' => 'printTransaction', 'name' => $this->lang['printTransaction'],  'icon' => 'print', 'url' => 'print/cashAdvance'));
         
    $this->includeClassDependencies(array(
           'Warehouse.class.php',
           'ChartOfAccount.class.php',
           'COALink.class.php',
           'CashOut.class.php',
           'AR.class.php',
           'ARPayment.class.php',
           'PaymentMethod.class.php',
           'CashIn.class.php',
           'GeneralJournal.class.php',
    ));  

    $this->overwriteConfig();

    }

    function getQuery(){
         
        $sql = '
            SELECT
                '.$this->tableName.'.* ,  
                '.$this->tableWarehouse.'.name as warehousename, 
                '.$this->tableStatus.'.status as statusname
                
            FROM '.$this->tableStatus.',
                 '.$this->tableWarehouse.', 
                 '.$this->tableName.'
      	        WHERE   
                  '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey and 
                  '.$this->tableName.'.warehousekey = '.$this->tableWarehouse.'.pkey 

            ' .$this->criteria ;       
        
        $sql .=  $this->getWarehouseCriteria() ;
                                
        return $sql;
    }
    
   
    
    function validateForm($arr,$pkey = ''){  
        $arrayToJs = parent::validateForm($arr,$pkey);  
 		 
        return $arrayToJs;
    }
      
	function closeTrans($rsHeader){
		
		// sementara disini 
		$id = $rsHeader[0]['pkey'];
		$this->addCashIn($id);
		$this->addCashOut($id);   
		
	}
	
	function getDetailWithRelatedInformation($pkey,$criteria){

	
        $sql = 'select
	   			'.$this->tableNameDetail .'.*
			  from
			  	'. $this->tableNameDetail .'
			  where
			  	'.$this->tableNameDetail .'.refkey = '.$this->oDbCon->paramString($pkey);
        
        $sql .= $criteria;
                    

        
		return $this->oDbCon->doQuery($sql);

	}
    
	
	function contains($str, array $arr){
		foreach($arr as $a) {
			if (stripos(strtolower($str),strtolower($a)) !== false) return true;
		}
		return false;
	}
	
	
    function getJournalInformation($keyword, $warehousekey, $isDebit = 0){
	
		$coaLink = new COALink(); 
        $warehouse = new Warehouse();
		
		$coakey = 0;
		$desc = $keyword;
		 
		
		if ($isDebit == 1){
			 //pengeluaran
			
			 // admin VA
			 if($this->contains($keyword,array('TRF DEST VA'))){ 
				 $rsCOA = $coaLink->getCOALink('adminfeecost', $warehouse->tableName,$warehousekey, 0);
				 
				 $coakey = $rsCOA[0]['coakey'];
				 $desc = 'Admin VA';
			 }else if($this->contains($keyword,array('FEE','ADMINISTRASI'))){ 
				 $rsCOA = $coaLink->getCOALink('adminfeecost', $warehouse->tableName,$warehousekey, 0);
				 
				 $coakey = $rsCOA[0]['coakey'];
				 $desc = 'Admin Penarikan Tunai';
			 }else if($this->contains($keyword,array('PLN'))){ 
				 // tembak dulu
				 $coakey = 8025; 
			 }else if($this->contains($keyword,array('PALYJA'))){ 
				 // tembak dulu
				 $coakey = 8026; 
			 }
			
			if (empty($coakey)){
				 $rsCOA = $coaLink->getCOALink('othercost', $warehouse->tableName,$warehousekey, 0); 
				 $coakey = $rsCOA[0]['coakey']; 
			}
		}else{
			 // pendapatan
			 if($this->contains($keyword,array('TRF DEST VA','VA CLOSE','IPKL'))){ 
				 $rsCOA = $coaLink->getCOALink('salesservice', $warehouse->tableName,$warehousekey, 0);
				 
				 $coakey = $rsCOA[0]['coakey'];
				 $desc = '';
			 }
			
			
			// default set nya ke pendapatan lai2 buat jaga2
			if (empty($coakey)){
				 $rsCOA = $coaLink->getCOALink('otherrevenue', $warehouse->tableName,$warehousekey, 0); 
				 $coakey = $rsCOA[0]['coakey']; 
				
			}
		}
		
	
		
		return array('coakey' => $coakey, 'description' => $desc);
	}
	
    function addCashIn($id){
            $cashIn = new CashIn();
            $warehouse = new Warehouse();
            $coaLink = new COALink();
    		    
            $rsHeader = $this->getDataRowById($id);
            $warehousekey = $rsHeader[0]['warehousekey'];

			$arrParam = array(); 
        
            $criteria = ' and '.$this->tableNameDetail.'.credit > 0';

            $rsDetail = $this->getDetailWithRelatedInformation($id,$criteria);
                
            $rsPaymentCOA =  $coaLink->getCOALink ('payment', $warehouse->tableName, $warehousekey,$rsHeader[0]['paymentmethodkey']);
 
 
			for($i=0;$i<count($rsDetail);$i++){
				$arrParam = array(); 

				$coaInformation = $this->getJournalInformation($rsDetail[$i]['trdesc'], $warehousekey, 0);
				
				$arrParam['code'] = 'xxxx';
				$arrParam['selWarehouse'] =  $rsHeader[0]['warehousekey'];
				$arrParam['bankRefCode'] =  $rsDetail[$i]['reference'];
				$arrParam['trDate'] =  $this->formatDBDate($rsDetail[$i]['postingdate']);
				$arrParam['hidCOAHeaderKey'] =  $rsPaymentCOA[0]['coakey'];
	//			$arrParam['recipientName'] =  $rsHeader[0]['virtualaccount'];

				$arrParam['selStatus'] = 1;



				$arrParam['hidDetailKey'] = array(0);
				$arrParam['hidCOAKey'] = array($coaInformation['coakey']);
				$arrParam['trdesc'] = array($coaInformation['description']);
				$arrParam['amount'] = array($rsDetail[$i]['credit']);

				$response = $cashIn->addData($arrParam);
				
			}
    }
    
    
    function addCashOut($id){
            $cashOut = new CashOut();
            $warehouse = new Warehouse();        
    		$coaLink = new COALink();

            $rsHeader = $this->getDataRowById($id);
            $warehousekey = $rsHeader[0]['warehousekey'];

		    $rsPaymentCOA =  $coaLink->getCOALink ('payment', $warehouse->tableName, $warehousekey,$rsHeader[0]['paymentmethodkey']);

		
            $criteria = ' and '.$this->tableNameDetail.'.debit > 0';
			$rsDetail = $this->getDetailWithRelatedInformation($id,$criteria);
		 
		 
			for($i=0;$i<count($rsDetail);$i++){
				$arrParam = array(); 

				$coaInformation = $this->getJournalInformation($rsDetail[$i]['trdesc'], $warehousekey,1);

				$arrParam['code'] = 'xxxx';
				$arrParam['selWarehouse'] =  $rsHeader[0]['warehousekey'];
				$arrParam['bankRefCode'] =  $rsDetail[$i]['reference'];
				$arrParam['trDate'] =  $this->formatDBDate($rsDetail[$i]['postingdate']);
				$arrParam['hidCOAHeaderKey'] =  $rsPaymentCOA[0]['coakey'];
	//			$arrParam['recipientName'] =  $rsHeader[0]['virtualaccount'];

				$arrParam['selStatus'] = 1;
 
				$arrParam['hidDetailKey'] = array(0);
				$arrParam['hidCOAKey'] = array($coaInformation['coakey']);
				$arrParam['trdesc'] = array($coaInformation['description']);
				$arrParam['amount'] = array($rsDetail[$i]['debit']);
				
				$response = $cashOut->addData($arrParam);
				
			}
	 
    }

	
	function confirmTrans($rsHeader){
		 
		require_once DOC_ROOT.'assets/vendor/autoload.php';  
		
		
		$id = $rsHeader[0]['pkey'];
         
		
		// ambil file, baca per baris
		// example
		
		$inputFileType = 'Xlsx';   
		$inputFileName = $rsHeader[0]['file'];
		$uploadPath = DEFAULT_DOC_UPLOAD_PATH.$this->uploadFolder.$id.'/';
			
         
		$reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType); 
		$reader->setReadDataOnly(true); 

		$spreadsheet = $reader->load($uploadPath.$inputFileName);
		$worksheet = $spreadsheet->getActiveSheet(); 
      
		$coaLink = new COALink();
        $warehouse = new Warehouse();      
		$warehousekey = $rsHeader[0]['warehousekey'];
		
        $rsPaymentCOA =  $coaLink->getCOALink ('payment', $warehouse->tableName, $warehousekey,$rsHeader[0]['paymentmethodkey']);
  
		$arrStatement = $this->importFromExcel($worksheet,$rsPaymentCOA[0]['coakey']);
            
        foreach($arrStatement as $row){
			
					$sql = 'insert into '.$this->tableNameDetail.' 
								(refkey,postingdate,trdesc,debit,credit,reference) 
							values (
								'.$this->oDbCon->paramString($id).',
								'.$this->oDbCon->paramString($row['date']).',
								'.$this->oDbCon->paramString($row['remarks']).',
								'.$this->oDbCon->paramString($row['debit']).',
								'.$this->oDbCon->paramString($row['credit']).',
								'.$this->oDbCon->paramString($row['reference']).'
								
							) ';

					$this->oDbCon->execute($sql);
        }
	} 
	
	  
	function validateConfirm($rsHeader){
		
		$id = $rsHeader[0]['pkey']; 
		 
		$inputFileName = $rsHeader[0]['file'];
		$uploadPath = DEFAULT_DOC_UPLOAD_PATH.$this->uploadFolder.$id.'/';
		
//	    if(!is_file($uploadPath.$inputFileName))
//			$this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg[216]);
             
 	 }
    
	
	function importFromExcel($worksheet,$coakey){
		// nanti bisa dioverwrite

		$arrStatement = array();
		 
		// Get the highest row and column numbers referenced in the worksheet
		$highestRow = $worksheet->getHighestRow(); // e.g. 10
		$highestColumn = $worksheet->getHighestColumn(); // e.g 'F'
		$highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn); // e.g. 5

		
		for ($row = 14; $row <= $highestRow; ++$row) { 

			$trdate = $worksheet->getCellByColumnAndRow(3, $row)->getValue(); 
			$remarks = $worksheet->getCellByColumnAndRow(7, $row)->getValue();     
			$reference = $worksheet->getCellByColumnAndRow(12, $row)->getValue();  
			$debit = $worksheet->getCellByColumnAndRow(14, $row)->getValue();    
			$credit = $worksheet->getCellByColumnAndRow(18, $row)->getValue();   

                
//			if(!isset($arrStatement[$reference])) $arrStatement[$reference] = array();
				 
            if(empty($reference)) continue;
            
			array_push($arrStatement, array( 
				'date' => date('Y-m-d',strtotime($trdate)),
				'remarks' =>  $remarks,
				'reference' => $reference,
				'debit' => $this->unFormatNumber($debit),
				'credit' => $this->unFormatNumber($credit)
			)); 


		}
		
		// cari dulu ad referensi yg sudah terdaftar blm
		$arrReference = array_column($arrStatement,'reference'); 
		$arrCashObj = array(new CashIn(), new CashOut()); 
		foreach($arrCashObj as $objRow){
		  
			
			$rsCashBank = $objRow->searchDataRow(array($objRow->tableName.'.pkey',$objRow->tableName.'.bankrefcode'), 
											   ' and '.$objRow->tableName.'.statuskey <> 4 
												 and '.$objRow->tableName.'.coakey = '.$this->oDbCon->paramString($coakey).' 
												 and '.$objRow->tableName.'.bankrefcode in ('.$this->oDbCon->paramString($arrReference,',').') 
											   '
											 );
 
			foreach($rsCashBank as $cashBankRow){
				foreach($arrStatement as $key=>$row){
					if ($row['reference'] == $cashBankRow['bankrefcode'] ){
						unset($arrStatement[$key]);
						break; // idealnya asatu importan semua referensi unik
					}
				}
			}
		}
	 
        
		return $arrStatement;
		
	}
  
	
    function normalizeParameter($arrParam, $trim=false){
        
        $arrParam = parent::normalizeParameter($arrParam,true); 
        
        return $arrParam; 
    }
     
}

?>
