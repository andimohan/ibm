<?php

class APPayableTax23 extends AP{
  
   function __construct(){
		
		parent::__construct();
		
       // yg ada pph 23 hanya jasa 
		$this->tableName = 'ap_payable_23';      
		$this->tableTax = 'tax';
	    $this->tableAP = 'ap';
	   
	    // utk ini dipisahkan, karena takut ganggu kalo pake inherit
	    $this->tableAPPaymentHeader = 'ap_payment_header';
	    $this->tableAPPaymentDetail = 'ap_payment_detail';
	    $this->tablePaymentDetail = 'ap_payable_23_payment_detail';
		$this->tableCashAdvanceRealizationHeader = 'cash_advance_realization_header';
		$this->tableCashAdvanceRealizationDetail = 'cash_advance_realization_detail';
		$this->tableCashOutHeader = 'cash_out_header';
		$this->tableCashOutDetail = 'cash_out_detail';
        //$this->tablePurchase = 'trucking_service_order_invoice_header'; 
		
		$this->securityObject = 'APPayableTax23';
         
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'date','title' => 'date','dbfield' => 'trdate','default'=>true, 'width' => 100, 'align' =>'center', 'format' => 'date'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'reference','title' => 'reference','dbfield' => 'refcode','default'=>true, 'width' => 120 ));
        array_push($this->arrDataListAvailableColumn, array('code' => 'type','title' => 'type','dbfield' => 'pphtypename',  'width' => 100 ));
        array_push($this->arrDataListAvailableColumn, array('code' => 'supplier','title' => 'supplier','dbfield' => 'suppliername','default'=>true, 'width' => 200 ));
        array_push($this->arrDataListAvailableColumn, array('code' => 'amount','title' => 'amount','dbfield' => 'amount','default'=>true, 'width' => 100, 'align' => 'right', 'format' => 'number'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'outstanding','title' => 'outstanding','dbfield' => 'outstanding','default'=>true, 'width' => 100, 'align' => 'right', 'format' => 'number'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
        array_push($this->arrDataListAvailableColumn, array('code' => 'warehouse','title' => 'warehouse','dbfield' => 'warehousename',  'width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'description','title' => 'note','dbfield' => 'trdesc',  'width' => 200));
        
        
        $this->includeClassDependencies(array( 
            'AP.class.php',
            'APPayment.class.php',
            'APPayableTax23Payment.class.php'
        ));

        $this->overwriteConfig();
	}  
    
    function getQuery(){
	   
		   $sql = '
                    select
                        '.$this->tableName. '.*,
                        if('.$this->tableName. '.statuskey = 1 or '.$this->tableName. '.statuskey = 2, datediff(now(),duedate) , 0)  as datediff,
                        '.$this->tableSupplier.'.name as suppliername,
                        '.$this->tableStatus.'.status as statusname,
                        '.$this->tableWarehouse.'.name as warehousename, 
                        '.$this->tableCurrency.'.name as currencyname ,
                        '.$this->tableType .'.name as aptypename, 
                        '.$this->tableTax .'.name as pphtypename
                    from 
                        '.$this->tableName . ' 
                            left join ' . $this->tableCurrency .' on  '.$this->tableName.'.currencykey = ' . $this->tableCurrency .'.pkey
                            left join ' .  $this->tableType .' on  '.$this->tableName.'.aptype = ' . $this->tableType .'.pkey
                            left join ' .  $this->tableTax .' on  '.$this->tableName.'.pphtype = ' . $this->tableTax .'.pkey,
                        '.$this->tableStatus.',
                        '.$this->tableSupplier.',
                        '.$this->tableWarehouse.' 
                    where  		
                        '.$this->tableName . '.statuskey = '.$this->tableStatus.'.pkey and 
                        '.$this->tableName . '.warehousekey = '.$this->tableWarehouse.'.pkey and 
                        '.$this->tableName.'.supplierkey = '.$this->tableSupplier.'.pkey
		' .$this->criteria ; 
        
        $sql .=  $this->getWarehouseCriteria() ;
          
        return $sql;
	}
  
    function getPaymentObj(){
        return  new APPayableTax23Payment();
    }

function getJobInformation($arrPkey){
		 // ini utk GL (mungkin nanti)
		 // tp dipake jg ketika narik AP Payment 23 di CIF
		 
		 // coba liat nanti perlu dipisah gk karena ada jenis table
		$apPaymentKey = $this->getTableKeyAndObj($this->tableAPPaymentHeader,array('key'))['key'];
		$emklPOKey = $this->getTableKeyAndObj($this->tableEMKLPurchaseOrder,array('key'))['key'];

		$cashAdvanceKey = $this->getTableKeyAndObj($this->tableCashAdvanceRealizationHeader,array('key'))['key'];
		$cashOutKey = $this->getTableKeyAndObj($this->tableCashOutHeader,array('key'))['key'];

		   
			//AP Payment
			$sql = 'select 
						'.$this->tableName.'.pkey as apkey,
						'.$this->tableEMKLSalesOrder.'.pkey as jokey, 
						'.$this->tableEMKLSalesOrder.'.code as jocode, 
						'.$this->tableEMKLSalesOrder.'.trdate as jodate
				from
						'.$this->tableName.'
						
							left join '.$this->tableAPPaymentDetail.' on '.$this->tableName.'.refkey = '.$this->tableAPPaymentDetail.'.pkey
								and '.$this->tableName.'.reftabletype = '.$this->oDbCon->paramString($apPaymentKey).'
							left join '.$this->tableAP.' on '.$this->tableAPPaymentDetail.'.apkey = '.$this->tableAP.'.pkey 
								and '.$this->tableAP.'.reftabletype =  '.$this->oDbCon->paramString($emklPOKey).'
							left join '.$this->tableEMKLPurchaseOrder.' on '.$this->tableAP.'.refkey = '.$this->tableEMKLPurchaseOrder.'.pkey 
							left join '.$this->tableEMKLSalesOrder.' on '.$this->tableEMKLPurchaseOrder.'.refkey = '.$this->tableEMKLSalesOrder.'.pkey					
				where 
					'.$this->tableName.'.pkey in ('.$this->oDbCon->paramString($arrPkey,',').')
			';

			$result = $this->oDbCon->doQuery($sql);	

			//Cash Advance
			$sql = 'select 
						'.$this->tableName.'.pkey as apkey,
						'.$this->tableEMKLSalesOrder.'.pkey as jokey, 
						'.$this->tableEMKLSalesOrder.'.code as jocode, 
						'.$this->tableEMKLSalesOrder.'.trdate as jodate
				from
						'.$this->tableName.'
							left join '.$this->tableCashAdvanceRealizationDetail.' on '.$this->tableName.'.refkey = '.$this->tableCashAdvanceRealizationDetail.'.pkey
								and '.$this->tableName.'.reftabletype = '.$this->oDbCon->paramString($cashAdvanceKey).'
							left join '.$this->tableEMKLSalesOrder.' on '.$this->tableCashAdvanceRealizationDetail.'.joborderkey = '.$this->tableEMKLSalesOrder.'.pkey
				where 
					'.$this->tableName.'.pkey in ('.$this->oDbCon->paramString($arrPkey,',').')
			';

		   $result2 =  $this->oDbCon->doQuery($sql);	

		$rs = [];

		$defaultJO = [
			'jokey'  => 0,
			'jocode' => null,
			'jodate' => null,
		];

		// AP Payment 
		foreach ($result as $row) {
			$apkey = $row['apkey'];
			$rs[$apkey] = array_replace($defaultJO, $row);
		}

		// Cash Advance 
		foreach ($result2 as $row) {
			$apkey = $row['apkey'];

			if (
				!isset($rs[$apkey]) ||
				empty($rs[$apkey]['jokey'])
			) {
			if (!empty($row['jokey'])) {
					$rs[$apkey] = array_replace(
						$rs[$apkey] ?? ['apkey' => $apkey] + $defaultJO,
						$row
					);
				}
			}
		}

		$rs = array_values($rs);
		
		return $rs;
	}

  function searchDataForAutoComplete($fieldname='',$searchkey='',$mustmatch=false,$searchCriteria='',$orderCriteria='', $limit=''){
         
		$sql = 'select
					'.$this->tableName. '.pkey,     
                    '.$this->tableName.'.code as value , 
                    '.$this->tableName. '.code as code , 
                    '.$this->tableName.'.refcode, 
                    '.$this->tableName.'.trdate, 
                    '.$this->tableName.'.duedate, 
                    '.$this->tableName.'.refcode2,
                    '.$this->tableName.'.refinvoicecode,
                    '.$this->tableName.'.refkey,
                    '.$this->tableName.'.refdate, 
                    '.$this->tableName. '.amount,  
                    '.$this->tableName. '.currencykey,  
                    '.$this->tableName. '.autotax,  
                    '.$this->tableName. '.outstanding,
					'.$this->tableSupplier.'.name as suppliername
				from 
					'.$this->tableName . '
					left join '.$this->tableSupplier.' on '.$this->tableName.'.supplierkey = '.$this->tableSupplier.'.pkey, 
                    '.$this->tableStatus.'
				where  		
					'.$this->tableName . '.statuskey = '.$this->tableStatus.'.pkey 
			';
	
		if(!empty($fieldname)){
			
			$sql .= ' and ' ;
			
			if($mustmatch)
				$sql .=  $fieldname .' = '. $this->oDbCon->paramString($searchkey);
			else
				$sql .=  '('.$fieldname .' like '. $this->oDbCon->paramString('%'.$searchkey.'%') .' || '. $this->tableName .'.refcode like '. $this->oDbCon->paramString('%'.$searchkey.'%').')';
		}
				
		if($searchCriteria <> '')
			$sql .= ' ' .$searchCriteria;
	
		if($orderCriteria <> '')
			$sql .= ' ' .$orderCriteria;  
			
		if($limit <> '') $sql .= ' ' .$limit;
		
	  
		$rs =  $this->oDbCon->doQuery($sql);	
	  	
	   
	  // update informasi JO
	  // kalo jenis nya EMKL, join ke job order jika diperlukan
	  // utk tipe TEL atau yg lama, nanti tambahin manual pengecualiannya
	   if ( in_array(PLAN_TYPE['categorykey'], array(COMPANY_TYPE['forwarding']))) { 
		   
		   $rsJob = $this->getJobInformation(array_column($rs,'pkey'));
		   $rsJob = array_column($rsJob,null,'apkey');
		   
		   foreach($rs as $key=>$row){
			   $apPkey = $row['pkey'];
			   $rs[$key]['jocode'] = $rsJob[$apPkey]['jocode'];
			   $rs[$key]['jodate'] = $rsJob[$apPkey]['jodate'];
		   }
		   
	   } 
	  return $rs;
	}
    
    
}
?>
