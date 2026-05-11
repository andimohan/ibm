<?php
  
class SalesOrderProperty extends BaseClass{ 
  

    function __construct(){

		parent::__construct();

		$this->tableName = 'sales_order_property_header';
		$this->tableNameDetail = 'sales_order_property_detail';
		$this->tableType = 'sales_order_property_type';
		$this->tableWarehouse = 'warehouse';  
		$this->tableCustomer = 'customer';  
		$this->tableSupplier = 'supplier';  
		$this->tableEmployee = 'employee';  
		$this->tableStatus = 'transaction_status';
		$this->tableDownpayment = 'customer_downpayment';    
		$this->tableTermOfPayment = 'term_of_payment'; 
		$this->tableDownpaymentDetail = 'sales_order_property_downpayment'; 
		$this->isTransaction = true; 		
		$this->newLoad  = true;

		$this->securityObject = 'SalesOrderProperty';   

//            $this->arrLinkedTable = array(); 
//            $defaultFieldName = 'refkey';
//            array_push($this->arrLinkedTable, array('table'=>'ar','field'=>$defaultFieldName));  

		$arrDownpaymentDetail = array(); 
		$arrDownpaymentDetail['pkey'] = array('hidDetailDownpaymentKey');
		$arrDownpaymentDetail['refkey'] = array('pkey', 'ref');
		$arrDownpaymentDetail['amount'] = array('downpaymentAmount',array('datatype' => 'number','mandatory'=>true));
		$arrDownpaymentDetail['downpaymentkey'] = array('hidDownpaymentKey',array('mandatory'=>true));

 
        $arrAgentFeeDetail = array();
        $arrAgentFeeDetail['pkey'] = array('hidDetailKey');
        $arrAgentFeeDetail['refkey'] = array('pkey', 'ref');
        $arrAgentFeeDetail['agentkey'] = array('hidAgentKey');
        $arrAgentFeeDetail['cobrokefee'] = array('cobrokeFee', 'number');
        $arrAgentFeeDetail['totalfee'] = array('agentTotal', 'number');
        $arrAgentFeeDetail['cobrokepercentage'] = array('cobrokePercentage', 'number');
        $arrAgentFeeDetail['commissionfee'] = array('commissionFee', 'number');
        $arrAgentFeeDetail['commissionpercentage'] = array('commissionPercentage', 'number');
        $arrAgentFeeDetail['bankprovision'] = array('agentBankProvision', 'number');
        $arrAgentFeeDetail['closingfee'] = array('agentClosingFee', 'number');
        $arrAgentFeeDetail['bankprovisionpercentage'] = array('provisionPercentage', 'number');

		$arrDetails = array();
		array_push($arrDetails, array('dataset' => $arrDownpaymentDetail, 'tableName' => $this->tableDownpaymentDetail));    
		array_push($arrDetails, array('dataset' => $arrAgentFeeDetail, 'tableName' => $this->tableNameDetail));

		$this->arrData = array(); 
		$this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails)); 
		$this->arrData['code'] = array('code');
		$this->arrData['trdate'] = array('trDate','datetime');
		$this->arrData['sellerkey'] = array('hidSellerKey');
		$this->arrData['buyerkey'] = array('hidBuyerKey');
		$this->arrData['agentkey'] = array('hidEmployeeKey');
		$this->arrData['warehousekey'] = array('selWarehouseKey');
		$this->arrData['trdesc'] = array('trDesc');
		$this->arrData['subtotal'] = array('subtotal','number');
		$this->arrData['agencyfee'] = array('agencyFee','number');
		$this->arrData['officefee'] = array('officeFee','number');
		$this->arrData['commissiontype'] = array('selCommissionType', 'number');
		$this->arrData['provisiontype'] = array('selProvisionType', 'number');
		$this->arrData['agentfee'] = array('agentFee','number');
		$this->arrData['adminfee'] = array('adminFee','number');
		$this->arrData['taxfee'] = array('taxFee','number');
		$this->arrData['orlead'] = array('orLead','number');
		$this->arrData['bankprovision'] = array('bankProvision','number');
		$this->arrData['transactiontotal'] = array('transactionTotal','number');
		$this->arrData['agencypercentage'] = array('agencyPercentage','number');
		$this->arrData['officepercentage'] = array('officeFeePercentage','number');
		$this->arrData['agentpercentage'] = array('agentFeePercentage','number');
		$this->arrData['adminpercentage'] = array('adminFeePercentage','number');
		$this->arrData['orleadpercentage'] = array('orLeadPercentage','number');
		$this->arrData['bankprovisionpercentage'] = array('bankProvisionPercentage','number');
		$this->arrData['taxpercentage'] = array('taxFeePercentage','number');
		$this->arrData['totaldownpayment'] = array('totalDownpayment','number');
		$this->arrData['closingfeetotal'] = array('closingFeeTotal','number');
		$this->arrData['balance'] = array('balance','number'); 
		$this->arrData['statuskey'] = array('selStatus');
		$this->arrData['propertyinformation'] = array('propertyInformation');
		$this->arrData['bankkey'] = array('hidBankKey');
		$this->arrData['termofpaymentkey'] = array('selTermOfPaymentKey');
		$this->arrData['refundcoakey'] = array('hidRefundCOAKey');
		$this->arrData['downpaymentsettlement'] = array('downpaymentSettlement','number');
		
		$this->arrData['banktotal'] = array('bankTotal','number');
		$this->arrData['officebankpercentage'] = array('officeFeeBankPercentage','number');
		$this->arrData['agentbankpercentage'] = array('agentFeeBankPercentage','number');
		$this->arrData['officefeebank'] = array('officeFeeBank','number');
		$this->arrData['agentfeebank'] = array('agentFeeBank','number');
		$this->arrData['totalcompanyrevenue'] = array('totalCompanyRevenue','number');
		
		
		$this->arrData['typekey'] = array('selType');

		$this->arrDataListAvailableColumn = array(); 
		array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 150));
		array_push($this->arrDataListAvailableColumn, array('code' => 'date','title' => 'date','dbfield' => 'trdate','default'=>true, 'width' => 100, 'align'=>'center', 'format' => 'date'));
		array_push($this->arrDataListAvailableColumn, array('code' => 'warehouse','title' => 'warehouse','dbfield' => 'warehousename','default'=>true, 'width' => 100));
		array_push($this->arrDataListAvailableColumn, array('code' => 'seller','title' => 'seller','dbfield' => 'sellername','default'=>true, 'width' => 150));
		array_push($this->arrDataListAvailableColumn, array('code' => 'buyer','title' => 'buyer','dbfield' => 'buyername', 'width' => 100));
		//array_push($this->arrDataListAvailableColumn, array('code' => 'agent','title' => 'agent','dbfield' => 'employeename','default'=>true, 'width' => 150));
		array_push($this->arrDataListAvailableColumn, array('code' => 'propertyInformation','title' => 'propertyInformation','dbfield' => 'propertyinformation','default'=>true,  'width' => 200));
		array_push($this->arrDataListAvailableColumn, array('code' => 'desc','title' => 'note','dbfield' => 'trdesc', 'width' => 200));
		array_push($this->arrDataListAvailableColumn, array('code' => 'total','title' => 'total','dbfield' => 'transactiontotal','default'=>true, 'width' => 100, 'align' => 'right', 'format'=>'number'));
		array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));


		$this->arrSearchColumn = array(); 
		array_push($this->arrSearchColumn, array('Kode', $this->tableName . '.code'));
		array_push($this->arrSearchColumn, array('Tanggal', $this->tableName . '.trdate')); 
		array_push($this->arrSearchColumn, array('Pembeli', 'buyer.name'));
		array_push($this->arrSearchColumn, array('Penjual', $this->tableCustomer. '.name')); 
		array_push($this->arrSearchColumn, array('Bank', $this->tableEmployee. '.name'));
		array_push($this->arrSearchColumn, array('Gudang', $this->tableWarehouse. '.name'));
		array_push($this->arrSearchColumn, array('Informasi Properti', $this->tableName. '.propertyinformation'));
		array_push($this->arrSearchColumn, array('Total', $this->tableName. '.transactiontotal'));
		array_push($this->arrSearchColumn, array('Catatan', $this->tableName. '.trdesc'));


		$this->printMenu = array();  
		array_push($this->printMenu,array('code' => 'printInvoice', 'name' => $this->lang['print'] . ' ' .$this->lang['invoice'],  'icon' => 'print', 'url' => 'print/salesOrderProperty'));
		array_push($this->printMenu,array('code' => 'printComplete', 'name' => $this->lang['printSummary'],  'icon' => 'print', 'url' => 'print/salesOrderPropertyComplete'));

		$this->includeClassDependencies(array(
		   'Warehouse.class.php',  
		   'Customer.class.php', 
		   'APEmployeeCommission.class.php', 
		   'Supplier.class.php', 
		   'Downpayment.class.php', 
		   'Employee.class.php', 
		   'CustomerDownpayment.class.php',
		   'COALink.class.php',
		   'AR.class.php',
		   'ARPayment.class.php',
		   'ChartOfAccount.class.php',
		   'GeneralJournal.class.php',
		   'SalesOrderPropertyType.class.php',
		   'TermOfPayment.class.php' 
		));  


		$this->overwriteConfig();
    }
 
            
    
    function getQuery(){

        $sql = '
            SELECT '.$this->tableName.'.* ,
               '.$this->tableCustomer.'.name as sellername,  
               buyer.name as buyername,   
               '.$this->tableWarehouse.'.code as warehousecode,
               '.$this->tableWarehouse.'.name as warehousename,
               '.$this->tableStatus.'.status as statusname
            FROM 
                '.$this->tableName.', 
                '.$this->tableStatus.', 
                '.$this->tableWarehouse.',
                '.$this->tableCustomer.',
                '.$this->tableCustomer.' buyer
            WHERE
				 '.$this->tableName.'.sellerkey = '.$this->tableCustomer.'.pkey and
				 '.$this->tableName.'.buyerkey = buyer.pkey and
				 '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey and
				 '.$this->tableName.'.warehousekey = '.$this->tableWarehouse.'.pkey 
        ' .$this->criteria ; 


        $sql .=  $this->getWarehouseCriteria() ;
        $sql .=  $this->getCompanyCriteria() ;
 
        return $sql;
    }  
 

    function reCountSubtotal($arrParam){
            
//           $basicAgentAdminFee = 0;
//		
//		
//		
//           $transactionTotal = $this->unFormatNumber($arrParam['transactionTotal']);
//           $bankTotal = $this->unFormatNumber($arrParam['bankTotal']);
//
//          
//           $officePercentage = $this->unFormatNumber($arrParam['officeFeePercentage']);
//           $agentPercentage = $this->unFormatNumber($arrParam['agentFeePercentage']);
//           $adminPercentage = $this->unFormatNumber($arrParam['adminFeePercentage']);
//           $taxPercentage = $this->unFormatNumber($arrParam['taxFeePercentage']);
//           $orLeadPercentage = $this->unFormatNumber($arrParam['orLeadPercentage']);
//           $bankProvisionPercentage = $this->unFormatNumber($arrParam['bankProvisionPercentage']);
//           $officeFeeBankPercentage = $this->unFormatNumber($arrParam['officeFeeBankPercentage']);
//           $agentFeeBankPercentage = $this->unFormatNumber($arrParam['agentFeeBankPercentage']);
//           $closingFeeTotal = $this->unFormatNumber($arrParam['closingFee']);
//           $cashRewardTotal = $this->unFormatNumber($arrParam['cashReward']);
//       
// 
// 			$agencyFeeTotal = $transactionTotal * ($agencyPercentage / 100);
//			$orLeadTotal = $transactionTotal * ($orLeadPercentage / 100);
//				
//			$basicAgentTotal = $agencyFeeTotal ;
//                    
//        	$officeFeeCommTotal = $basicAgentTotal * ($officePercentage / 100);
//			$agentFeeCommTotal = $basicAgentTotal * ($agentPercentage / 100); 
//            
//            $bankProvisionTotal = $bankTotal * ($bankProvisionPercentage / 100);    
//            $officeFeeBankTotal = $bankProvisionTotal * ($officeFeeBankPercentage / 100);
//			$agentFeeBankTotal = $bankProvisionTotal * ($agentFeeBankPercentage / 100); 
//
//			$taxFeeTotal = $officeFeeCommTotal * ($taxPercentage / 100) ;
//
//			$basicAgentAdminFee = $agentFeeCommTotal; // + $agentFeeBankTotal;
//            $adminFeeTotal = $basicAgentAdminFee * ($adminPercentage / 100) ;
//				
//            $totalCompanyRevenue =   $officeFeeCommTotal + $officeFeeBankTotal + $adminFeeTotal + $orLeadTotal;
//            $totalAgentRevenue =   $agentFeeCommTotal + $agentFeeBankTotal + $closingFeeTotal + $cashRewardTotal;
// 
//            $totalDowpayment = 0; 
//            $downpayment = $arrParam['downpaymentAmount'];
//            $downpaymentKey = $arrParam['hidDownpaymentKey'];
//            for($i=0;$i<count($downpayment);$i++){
//                if(empty($downpaymentKey[$i]))
//                    continue;
//                $totalDowpayment += $this->unFormatNumber($downpayment[$i]);
//            }  
//		
//			$downpaymentSettlement = ($totalDowpayment > 0) ? $totalDowpayment - ($agencyFeeTotal+$orLeadTotal) : 0;
//		
//			// gk dipotong adminFeeTotal, karena termasuk utk perhitungan pph23
//			$agentTakeHomeCommission = $agentFeeCommTotal + $agentFeeBankTotal + $closingFeeTotal + $cashRewardTotal;// - $adminFeeTotal;
//		
//            $balance = $transactionTotal - $totalDowpayment;  
//            $reCountResult = array();
//            $reCountResult['agencyFee'] = $agencyFeeTotal;
//            $reCountResult['officeFee'] = $officeFeeCommTotal;
//            $reCountResult['agentFee'] = $agentFeeCommTotal;
//            $reCountResult['adminFee'] = $adminFeeTotal;
//            $reCountResult['taxFee'] = $taxFeeTotal;
//		    $reCountResult['orLead'] = $orLeadTotal;
//		    $reCountResult['officeFeeBank'] = $officeFeeBankTotal;
//		    $reCountResult['agentFeeBank'] = $agentFeeBankTotal; 
//		    $reCountResult['totalCompanyRevenue'] = $totalCompanyRevenue; 
//		    $reCountResult['totalAgentRevenue'] = $totalAgentRevenue;
//		    $reCountResult['bankProvision'] = $bankProvisionTotal;
//            $reCountResult['totalDownpayment'] = $totalDowpayment;
//            $reCountResult['downpaymentSettlement'] = $downpaymentSettlement;
//            $reCountResult['balance'] = $balance;
		
		
		
            $transactionTotal = $this->unFormatNumber($arrParam['transactionTotal']);
		
			$commissionType = $this->unFormatNumber($arrParam['selCommissionType']);
		 	$agencyPercentage = $this->unFormatNumber($arrParam['agencyPercentage']);
        	$agencyFeeTotal = $this->unFormatNumber($arrParam['agencyFee']);
		
			$provisionType = $this->unFormatNumber($arrParam['selProvisionType']);
			$bankTotal = $this->unFormatNumber($arrParam['bankTotal']);
        	$bankProvisionPercentage = $this->unFormatNumber($arrParam['bankProvisionPercentage']);
        	$bankProvisionTotal = $this->unFormatNumber($arrParam['bankProvision']);
		
			$orLeadPercentage = $this->unFormatNumber($arrParam['orLeadPercentage']);
		
			$cobrokePercentage = $arrParam['cobrokePercentage'];
		
			if($commissionType == 2) $agencyFeeTotal = $transactionTotal * ($agencyPercentage / 100);
			if($provisionType == 2) $bankProvisionTotal = $bankTotal * ($bankProvisionPercentage / 100);
		
			$orLeadTotal = $transactionTotal * ($orLeadPercentage / 100);
		 
			// loop setiap detail agent 
			$totalAgentCommission =0;
			$totalAgentProvision =0;
			$totalClosingFee =0;
			
			for ($i=0;$i<count($cobrokePercentage); $i++) { 

				$percentCobroke = $this->unFormatNumber($cobrokePercentage[$i]) / 100;
				$cobrokeFee = $agencyFeeTotal * $percentCobroke;
				$arrParam['cobrokeFee'][$i] = $cobrokeFee;

				$percentCommission = $this->unFormatNumber($arrParam['commissionPercentage'][$i]) / 100;
				$arrParam['commissionFee'][$i] = $cobrokeFee * $percentCommission;

				$cobrokeProvision = $bankProvisionTotal * $percentCobroke;
				$percentProvision = $this->unFormatNumber($arrParam['provisionPercentage'][$i]) / 100;
				$arrParam['agenBankProvision'][$i] = $cobrokeProvision * $percentProvision;
 
				$arrParam['agentClosingFee'][$i] = $this->unFormatNumber($arrParam['agentClosingFee'][$i]); 
				$arrParam['agentBankProvision'][$i]  =  $this->unFormatNumber($arrParam['agentBankProvision'][$i]); 
				$arrParam['agentTotal'][$i] =  $arrParam['commissionFee'][$i] +  $arrParam['agentBankProvision'][$i] + $arrParam['agentClosingFee'][$i];
				 
				$totalAgentCommission += $arrParam['commissionFee'][$i];
				$totalAgentProvision += $arrParam['agenBankProvision'][$i];
				$totalClosingFee += $arrParam['agentClosingFee'][$i];
			}
		
			$officeFeeCommTotal = $agencyFeeTotal - $totalAgentCommission;
			$officeFeeBankTotal = $bankProvisionTotal - $totalAgentProvision;
		
			$totalCompanyRevenue =  $officeFeeCommTotal + $officeFeeBankTotal + $orLeadTotal;
		 
            $totalDowpayment = 0; 
            $downpayment = $arrParam['downpaymentAmount'];
            $downpaymentKey = $arrParam['hidDownpaymentKey'];
            for($i=0;$i<count($downpayment);$i++){
                if(empty($downpaymentKey[$i])) continue;
                $totalDowpayment += $this->unFormatNumber($downpayment[$i]);
            }  
		
			$downpaymentSettlement = ($totalDowpayment > 0) ? $totalDowpayment - ($agencyFeeTotal+ $totalClosingFee +$orLeadTotal) : 0;
		  	//if ($downpaymentSettlement < 0 ) $downpaymentSettlement = 0;
			
		  	$reCountResult = array();
		 	$reCountResult['agencyFee'] = $agencyFeeTotal;
		 	$reCountResult['bankProvision'] = $bankProvisionTotal;
		 	$reCountResult['officeFee'] = $officeFeeCommTotal ; 
		 	$reCountResult['agentFee'] = $totalAgentCommission;
		
		 	$reCountResult['officeFeeBank'] = $officeFeeBankTotal; 
		 	$reCountResult['agentFeeBank'] = $totalAgentProvision;
		 	$reCountResult['closingFeeTotal'] = $totalClosingFee;
		
		  	$reCountResult['orLead'] = $orLeadTotal;
			$reCountResult['totalCompanyRevenue'] = $totalCompanyRevenue;  
			$reCountResult['downpaymentSettlement'] = $downpaymentSettlement;
		 
		
			// detail 
        	$reCountResult['cobrokeFee'] = $arrParam['cobrokeFee'];
        	$reCountResult['commissionFee'] = $arrParam['commissionFee'];
        	$reCountResult['agenBankProvision'] = $arrParam['agenBankProvision'];
        	$reCountResult['agentTotal'] = $arrParam['agentTotal'];
			
		    $reCountResult['totalDownpayment'] = $totalDowpayment;
            $reCountResult['downpaymentSettlement'] = $downpaymentSettlement;
            $reCountResult['balance'] = $balance;
		
            return $reCountResult;
        
    } 
   

    function validateForm($arr,$pkey = ''){

			$arrayToJs = parent::validateForm($arr,$pkey); 
			$downpayment = new CustomerDownpayment();  

			$sellerkey = $arr['hidSellerKey'];
			$buyerkey = $arr['hidBuyerKey'];
        
           $transactionTotal = $arr['transactionTotal'];   
           $bankTotal = $this->unformatNumber($arr['bankTotal']); 
           $arrDownpaymentKey = $arr['hidDownpaymentKey'];
		   $arrDownpaymentAmount = $arr['downpaymentAmount'];
		   $arrDownpaymentCode = $arr['downpaymentCode'];
          
        	if(empty($sellerkey))
				$this->addErrorList($arrayToJs,false,$this->errorMsg['salesOrderProperty'][2]);
        
            if(empty($buyerkey))
				$this->addErrorList($arrayToJs,false,$this->errorMsg['salesOrderProperty'][3]);
        
			if(empty($transactionTotal))
				$this->addErrorList($arrayToJs,false,$this->errorMsg['salesOrderProperty'][1]);

            if( $bankTotal > 0 && empty($arr['hidBankKey']))
				$this->addErrorList($arrayToJs,false,$this->errorMsg['salesOrderProperty'][4]);
            
			$arrDownpaymentExistKey = array();
			for($i=0;$i<count($arrDownpaymentKey);$i++) {  
				if(empty($arrDownpaymentKey[$i])) 	continue;
            
				// validasi DP masi available gk
				$rsDP = $downpayment->searchData($downpayment->tableName.'.pkey',$arrDownpaymentKey[$i],true, ' and '.$downpayment->tableName.'.statuskey in (2) ');

				if(empty($rsDP)){ 
					$this->addErrorList($arrayToJs,false,$arrDownpaymentCode[$i]. '. ' . $this->errorMsg['downpayment'][9]);
				}else{

					if ($buyerkey <> $rsDP[0]['customerkey'])
						$this->addErrorList($arrayToJs,false,$arrDownpaymentCode[$i]. '. ' . $this->errorMsg['downpayment'][6]); 

					if (in_array($arrDownpaymentKey[$i],$arrDownpaymentExistKey)){  
						$this->addErrorList($arrayToJs,false, $rsDP[0]['code'].'. '.$this->errorMsg[215]); 	 
					}else{ 
						if (!empty($arrDownpaymentKey[$i])) {  
							array_push($arrDownpaymentExistKey, $arrDownpaymentKey[$i]);
						}
					}

					// validasi nilai DP masi mencukupi gk
					$amount = $this->unformatNumber($arrDownpaymentAmount[$i]);
					if ($amount > $rsDP[0]['outstanding'] )
						$this->addErrorList($arrayToJs,false,$arrDownpaymentCode[$i]. '. ' . $this->errorMsg['downpayment'][8].' ('.$this->lang['outstanding']. ': ' .$this->formatNumber($rsDP[0]['outstanding']).')');  
				}

			}
		
 		//hitung semua jumlah detail persentasi
        $agenPercentage = 0;
        $statusAgenName = false;
        for ($i = 0; $i < count($arr['cobrokePercentage']); $i++) {
              
			//$commissionPercentage = $this->unFormatNumber($arr['commissionPercent'][$i]);
			$cobrokePercentage = $this->unFormatNumber($arr['cobrokePercentage'][$i]);
			
            if (empty($arr['hidAgentKey'][$i])) 
				$this->addErrorList($arrayToJs, false, $this->errorMsg['agent'][1]);
 
//            if ( $commissionPercentage > 100 || $commissionPercentage < 0) 
//                $this->addErrorList($arrayToJs, false, $this->errorMsg['commissionPercentage'][1]); 
//			
         	$agenPercentage = $agenPercentage + $cobrokePercentage;

        }
		
		 
        if ($agenPercentage <> 100) 
            $this->addErrorList($arrayToJs, false, $this->errorMsg['cobrokePercentage'][1]);
      
		
        
            return $arrayToJs;
    }
 
 
    function confirmTrans($rsHeader){  
        
        $id = $rsHeader[0]['pkey']; 
        $apEmployeeCommission = new APEmployeeCommission();
		$termOfPayment = new TermOfPayment();
		
        $refTableKey = $this->getTableKeyAndObj($this->tableName,array('key'))['key'];

		// Add Commission
		$rsDetail = $this->getDetailById($id);
		foreach($rsDetail as $row){ 
			$arrParam = array();	 
			$arrParam['code'] = 'xxxxxx';
			$arrParam['hidEmployeeKey'] = $row['agentkey'];
			$arrParam['hidRefKey'] = $id;
			$arrParam['hidRefHeaderKey'] = $id;
			$arrParam['hidRefCode'] =  $rsHeader[0]['code']; 
			$arrParam['hidRefCode2'] =  $rsHeader[0]['refcode'];  
			$arrParam['hidRefTable'] = $refTableKey;
			$arrParam['hidRefDate'] =   $this->formatDBDate($rsHeader[0]['trdate']); 
			$arrParam['selWarehouse'] = $rsHeader[0]['warehousekey'];
			$arrParam['selAPType'] = 1; 
			$arrParam['amount'] =  $row['totalfee'];
			$arrParam['trDesc'] = '';
			$arrParam['trDate'] =  $this->formatDBDate($rsHeader[0]['trdate']);  
			$arrParam['dueDate'] = $this->formatDBDate($rsHeader[0]['trdate']);// date ('d / m / Y', mktime(0, 0, 0, date("m")  , date("d")+$rsTOP[0]['duedays'], date("Y")));
			$arrParam['createdBy'] = 0;
			$arrParam['islinked'] = 1; 
			$arrParam['overwriteGL'] = 1;

			$arrayToJs = $apEmployeeCommission->addData($arrParam); 
			if (!$arrayToJs[0]['valid'])
				throw new Exception('<strong>'.$rsHeader[0]['code'] . '</strong>. '.$this->errorMsg[201].' '.$arrayToJs[0]['message']);    

	 
		}
		 
	
		// so far sementara semua pasti TOP dulu
		$rsTOP = $termOfPayment->searchDataRow( array($termOfPayment->tableName.'.pkey',$termOfPayment->tableName.'.duedays'),
												   ' and '.$termOfPayment->tableName.'.pkey = ' . $this->oDbCon->paramString($rsHeader[0]['termofpaymentkey'])
												  );
		
		// Add AR sales
		// jika ad kekurangan dr DP yg gk kepotong
		
		$totalAR = $rsHeader[0]['agencyfee'] + $rsHeader[0]['orlead'] - $rsHeader[0]['totaldownpayment'] + $rsHeader[0]['closingfeetotal'] + $rsHeader[0]['cashrewardtotal'] ;
		
		//$this->setLog($rsHeader[0]['agencyfee'] .'-'. $rsHeader[0]['totaldownpayment'],true);
		
		$duedays = $rsTOP[0]['duedays'];
		if($totalAR > 0 && $duedays > 0){
			$ar = new AR();
			$arrParam = array();	 
			$arrParam['code'] = 'xxxxxx';
            $arrParam['hidCustomerKey'] = $rsHeader[0]['buyerkey'];
            //$arrParam['hidSalesKey'] = $rsHeader[0]['agentkey'];
            $arrParam['hidRefKey'] = $id;
            $arrParam['hidRefHeaderKey'] = $id;
            $arrParam['hidRefCode'] =  $rsHeader[0]['code']; 
            $arrParam['hidRefCode2'] =  '';  
            $arrParam['hidRefTable'] = $refTableKey;
            $arrParam['hidRefDate'] =   $this->formatDBDate($rsHeader[0]['trdate']); 
            $arrParam['selWarehouse'] = $rsHeader[0]['warehousekey'];
            $arrParam['selARType'] = 1; 
            $arrParam['amount'] = $totalAR;
            $arrParam['trDesc'] = '';
            $arrParam['trDate'] =  $this->formatDBDate($rsHeader[0]['trdate']);  
            $date = new DateTime($rsHeader[0]['trdate']);
            $date->add(new DateInterval('P'.$duedays.'D'));
            $arrParam['dueDate'] = $date->format('d / m / Y');// date ('d / m / Y', mktime(0, 0, 0, date("m")  , date("d")+$rsTOP[0]['duedays'], date("Y")));
            $arrParam['createdBy'] = 0;
            $arrParam['islinked'] = 1; 
            $arrParam['overwriteGL'] = 1;

			$arrayToJs = $ar->addData($arrParam);  
			if (!$arrayToJs[0]['valid'])
				throw new Exception('<strong>'.$rsHeader[0]['code'] . '</strong>. '.$this->errorMsg[201].' '.$arrayToJs[0]['message']);    

		}
		
		// Add AR Provision
		if($rsHeader[0]['bankprovision'] > 0 ){ 
			$ar = new AR();
			$arrParam = array();	 
			$arrParam['code'] = 'xxxxxx';
            $arrParam['hidCustomerKey'] = $rsHeader[0]['bankkey'];
            //$arrParam['hidSalesKey'] = $rsHeader[0]['agentkey'];
            $arrParam['hidRefKey'] = $id;
            $arrParam['hidRefHeaderKey'] = $id;
            $arrParam['hidRefCode'] =  $rsHeader[0]['code']; 
            $arrParam['hidRefCode2'] =  '';  
            $arrParam['hidRefTable'] = $refTableKey;
            $arrParam['hidRefDate'] =   $this->formatDBDate($rsHeader[0]['trdate']); 
            $arrParam['selWarehouse'] = $rsHeader[0]['warehousekey'];
            $arrParam['selARType'] = 1; 
            $arrParam['amount'] =  $rsHeader[0]['bankprovision'] ;
            $arrParam['trDesc'] = '';
            $arrParam['trDate'] =  $this->formatDBDate($rsHeader[0]['trdate'],'d / m / Y');  
            $date = new DateTime($rsHeader[0]['trdate']);
            $date->add(new DateInterval('P'.$duedays.'D'));
            $arrParam['dueDate'] = $date->format('d / m / Y');// date ('d / m / Y', mktime(0, 0, 0, date("m")  , date("d")+$rsTOP[0]['duedays'], date("Y")));
            $arrParam['createdBy'] = 0;
            $arrParam['islinked'] = 1; 
            $arrParam['overwriteGL'] = 1;

			$arrayToJs = $ar->addData($arrParam);  
			if (!$arrayToJs[0]['valid'])
				throw new Exception('<strong>'.$rsHeader[0]['code'] . '</strong>. '.$this->errorMsg[201].' '.$arrayToJs[0]['message']);    
		}
		 
        //update jurnal umum 
        $this->updateGL($rsHeader);
   
    } 
 
	 function validateCancel($rsHeader,$autoChangeStatus=false){
        $id = $rsHeader[0]['pkey'];
         
        if(!$this->validateAutoReverseGL($id))
            $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' . $this->errorMsg[201].' ' .$this->errorMsg['generalJournal'][6],true);
            
        $apEmployeeCommission = new APEmployeeCommission();
        $ar = new AR(); 
        $rsARKey = $this->getTableKeyAndObj($this->tableName,array('key'))['key']; 
          
		//cek ad AR Service terbayar
         $rsAR = $ar->searchDataRow(array($ar->tableName.'.pkey') ,
													 ' and reftabletype = '.$this->oDbCon->paramString($rsARKey).' and refkey = '.$this->oDbCon->paramString($id).' 
													   and '.$ar->tableName.'.statuskey  in(2,3)');
		if(!empty($rsAR)) 
			$this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' . $this->errorMsg[201].' ' .$this->errorMsg['ar'][2],true);
 
         
		$rsAP = $apEmployeeCommission->searchDataRow(array($apEmployeeCommission->tableName.'.pkey') ,
													 ' and reftabletype = '.$this->oDbCon->paramString($rsARKey).' and refkey = '.$this->oDbCon->paramString($id).' 
													   and '.$apEmployeeCommission->tableName.'.statuskey  in(2,3)');
		if(!empty($rsAP)) 
			$this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' . $this->errorMsg[201].' ' .$this->errorMsg['apEmployee'][2],true);
 
	 } 
	
	
 	function afterStatusChanged($rsHeader){ 
          
        // hanya berlaku utk transaksi, untuk karyawan harusnya gk berlaku
        $customerDownpayment = new CustomerDownpayment();
        $rsDownpayment = $this->getDownpaymentDetail($rsHeader[0]['pkey']);
        for($i=0;$i<count($rsDownpayment); $i++){  
           $customerDownpayment->updateOutstanding($rsDownpayment[$i]['downpaymentkey']); 
        }
         
    }

    function cancelTrans($rsHeader,$copy){ 
        $id = $rsHeader[0]['pkey'];

        if ($copy)
            $this->copyDataOnCancel($id);	  

		
		//cancel komisi agent 
        $apEmployeeCommission = new APEmployeeCommission();
        $rsAPKey = $this->getTableKeyAndObj($this->tableName,array('key'))['key']; 
        $rsAP = $apEmployeeCommission->searchDataRow(array($apEmployeeCommission->tableName.'.pkey',$apEmployeeCommission->tableName.'.code') ,
													 ' and reftabletype = '.$this->oDbCon->paramString($rsAPKey).' and refkey = '.$this->oDbCon->paramString($id).' and '.$apEmployeeCommission->tableName.'.statuskey = 1');
        for($i=0;$i<count($rsAP);$i++) {
            $arrayToJs = $apEmployeeCommission->changeStatus($rsAP[$i]['pkey'],TRANSACTION_STATUS['batal'],'',false,true);
            if (!$arrayToJs[0]['valid'])
                throw new Exception('<strong>'.$rsHeader[0]['code'] . '</strong>. '.  $arrayToJs[0]['message']);    
        }
		
		$ar = new AR();  
		$rsAR = $ar->searchDataRow(array($ar->tableName.'.pkey',$ar->tableName.'.code') ,
													 ' and reftabletype = '.$this->oDbCon->paramString($rsAPKey).' and refkey = '.$this->oDbCon->paramString($id).' and '.$ar->tableName.'.statuskey = 1');
        for($i=0;$i<count($rsAR);$i++) {
            $arrayToJs = $ar->changeStatus($rsAR[$i]['pkey'],TRANSACTION_STATUS['batal'],'',false,true);
            if (!$arrayToJs[0]['valid'])
                throw new Exception('<strong>'.$rsHeader[0]['code'] . '</strong>. '.  $arrayToJs[0]['message']);    
        }
		
		
        $this->cancelGLByRefkey($id,$this->tableName);

    }  
 

    function updateGL($rs){
            if (!USE_GL) return;

            $warehouse = new Warehouse();
            $coaLink = new COALink();
            $generalJournal = new GeneralJournal();
            $customer = new Customer();
			$employee = new Employee();
        
			$rsDetail = $this->getDetailById($rs[0]['pkey']);
		
            $warehousekey = $rs[0]['warehousekey'];
  
            $rsKey = $generalJournal->getTableKeyAndObj($this->tableName);
            $arr = array();
            $arr['pkey'] = $generalJournal->getNextKey($generalJournal->tableName);
            $arr['code'] = 'xxxxx';
            $arr['refkey'] = $rs[0]['pkey'];
		    $arr['refTableType'] = $rsKey['key'];
            $arr['trDate'] = $this->formatDBDate($rs[0]['trdate'],'d / m / Y');  
            $arr['createdBy'] = 0;
		    $arr['selWarehouseKey'] = $warehousekey;
        

            $rsSeller = $customer->getDataRowById($rs[0]['sellerkey']);
            $rsBuyer = $customer->getDataRowById($rs[0]['buyerkey']);
			$arrDesc = array();
			array_push($arrDesc,$this->ucFirst($this->lang['seller']) . ' '. $rsSeller[0]['name'].'.');
			array_push($arrDesc,$this->ucFirst($this->lang['buyer']) . ' '. $rsBuyer[0]['name'].'.');
			array_push($arrDesc,$rs[0]['propertyinformation'].'.');
		
            $arr['trDesc'] = implode(chr(13),$arrDesc); 

            $temp = -1;
            
            //downpayment 
            $rsDownpayment = $this->getDownpaymentDetail($rs[0]['pkey']);  
             
			// gk dipotong DP, karena jurnal DP dipisah
			$totalARFee =  $rs[0]['agencyfee'] + $rs[0]['orlead'] + $rs[0]['closingfeetotal'] + $rs[0]['cashrewardtotal'] ;  
		
            if(!empty($rsDownpayment)){
                $totalDownpayment = 0;
                for($i=0;$i<count($rsDownpayment); $i++) 
                     $totalDownpayment += $rsDownpayment[$i]['amount'];
                     
				 $temp++;
				 $arr['hidCOAKey'][$temp] = $customer->getDownpaymentCOAKey($rs[0]['customerkey'],$warehousekey);   
				 $arr['debit'][$temp] = $totalDownpayment; 
				 $arr['credit'][$temp] = 0;  
					
				$totalARFee -= $totalDownpayment; 
            }
		
			if($totalARFee > 0){
				$temp++;
                $arr['hidCOAKey'][$temp] = $customer->getARCOAKey($rs[0]['customerkey'],$warehousekey);   
                $arr['debit'][$temp] = $totalARFee; 
                $arr['credit'][$temp] = 0;    
			}else if($totalARFee < 0){
				// sisa pengembalian duit 
				$temp++;
                $arr['hidCOAKey'][$temp] = $rs[0]['refundcoakey'];   
                $arr['debit'][$temp] = $totalARFee; 
                $arr['credit'][$temp] = 0;   
			}
            
			/* bank provision */
			$rsCOA = $coaLink->getCOALink ('bankprovision', $warehouse->tableName,$warehousekey, 0);
			$temp++;
			$arr['hidCOAKey'][$temp] = $customer->getARCOAKey($rs[0]['bankkey'],$warehousekey);   
			$arr['debit'][$temp] =   $rs[0]['bankprovision'];
			$arr['credit'][$temp] = 0 ;  

		
			$rsCOA = $coaLink->getCOALink ('salesservice', $warehouse->tableName,$warehousekey, 0);
			$temp++;
			$arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];
			$arr['debit'][$temp] = 0;
			$arr['credit'][$temp] = $rs[0]['officefee'] ;  
 
			
			$rsCOA = $coaLink->getCOALink ('orlead', $warehouse->tableName,$warehousekey, 0);
			$temp++;
			$arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];
			$arr['debit'][$temp] = 0;
			$arr['credit'][$temp] = $rs[0]['orlead'] ;   
		
		
			$rsCOA = $coaLink->getCOALink ('bankprovision', $warehouse->tableName,$warehousekey, 0);
			$temp++;
			$arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];
			$arr['debit'][$temp] = 0;
			$arr['credit'][$temp] = $rs[0]['officefeebank'] ;   
		
			// admin fee tdk diakui dulu karema nanti utk potong PPH komisi agent
//			$rsCOA = $coaLink->getCOALink ('adminrevenue', $warehouse->tableName,$warehousekey, 0);
//			$temp++;
//			$arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];
//			$arr['debit'][$temp] = 0;
//			$arr['credit'][$temp] = $rs[0]['adminfee'] ;  

			foreach($rsDetail as $row){ 
				$temp++;
				$arr['hidCOAKey'][$temp] =  $employee->getAPCommissionCOAKey( $row['agentkey'] , $warehousekey);
				$arr['debit'][$temp] = 0;
				$arr['credit'][$temp] = $row['totalfee'];  
			}
		
		
            $arrayToJs = $generalJournal->addData($arr);

            if (!$arrayToJs[0]['valid'])
                    throw new Exception('<strong>'.$rs[0]['code'] . '</strong>. '.$this->errorMsg[504].' '.$arrayToJs[0]['message']);    
     
    
    }
    
 
    
     function normalizeParameter($arrParam, $trim = false){ 

		if ($arrParam['selCommissionType'] == 1)    $arrParam['agencyPercentage'] = 0;
		if ($arrParam['selProvisionType'] == 1)    $arrParam['bankProvisionPercentage'] = 0;

      
        $reCountResult = $this->reCountSubtotal($arrParam);
		
        $arrParam['agencyFee'] = $reCountResult['agencyFee'];
        $arrParam['bankProvision'] = $reCountResult['bankProvision']; 
        $arrParam['officeFee'] = $reCountResult['officeFee']; 
        $arrParam['agentFee'] = $reCountResult['agentFee'];
        $arrParam['officeFeeBank'] = $reCountResult['officeFeeBank']; 
        $arrParam['agentFeeBank'] = $reCountResult['agentFeeBank']; 
        $arrParam['orLead'] = $reCountResult['orLead'];
        $arrParam['totalCompanyRevenue'] = $reCountResult['totalCompanyRevenue'];
		 
		// detail
        $arrParam['cobrokeFee'] = $reCountResult['cobrokeFee'];
        $arrParam['commissionFee'] = $reCountResult['commissionFee'];
        $arrParam['agenBankProvision'] = $reCountResult['agenBankProvision'];
        $arrParam['agentTotal'] = $reCountResult['agentTotal'];
		
		 
        $arrParam['closingFeeTotal'] = $reCountResult['closingFeeTotal']; 
        $arrParam['totalDownpayment'] = $reCountResult['totalDownpayment'];
        $arrParam['downpaymentSettlement'] = $reCountResult['downpaymentSettlement']; 
        $arrParam['balance'] = $reCountResult['balance'];
		
        $arrParam = parent::normalizeParameter($arrParam,true); 
		 
        return $arrParam;
    }
    
    
    function getDetailWithRelatedInformation($pkey)  {
        $sql = '
				select
					' . $this->tableNameDetail . '.*,
					' . $this->tableEmployee . '.name as employeename
				from 
					' . $this->tableNameDetail . '
					left join ' . $this->tableEmployee . ' on ' . $this->tableNameDetail . '.agentkey = ' . $this->tableEmployee . '.pkey
				where  		 
					' . $this->tableNameDetail . '.refkey=' . $this->oDbCon->paramString($pkey);
		
        return $this->oDbCon->doQuery($sql);
    }
        
}
?>
