<?php

class CashAdvanceRealization extends BaseClass{
	
    function __construct(){

        parent::__construct();

        $this->tableName = 'cash_advance_realization_header';
        $this->tableNameDetail = 'cash_advance_realization_detail';
        $this->tableNameDetailAdvance = 'cash_advance_realization_advance';
        $this->tableCashAdvance = 'cash_advance';
        $this->tableCost = 'cash_advance_realization_cost';      
        $this->tableWarehouse = 'warehouse';   
        $this->tableEmployee = 'employee';   
        $this->tableJobOrder = 'emkl_job_order_header';   
        $this->tableJobOrderHeader = 'emkl_order_header';   
        $this->tableItem = 'item';
        $this->tableCostCashOut = 'cost_cash_out';
        $this->tableStatus = 'transaction_status';
        $this->tableSupplier = 'supplier';
        $this->tableContainer = 'container';
        $this->tableCOA = 'chart_of_account';
        $this->isTransaction = true;    

	    $this->allowedStatusForEdit = array(1,2);
        $this->securityObject = 'CashAdvanceRealization';

		$this->arrCashDetail = array(); 
    	$this->arrCashDetail['pkey'] = array('hidDetailItemKey');
    	$this->arrCashDetail['refkey'] = array('pkey', 'ref');
		$this->arrCashDetail['cashadvancekey'] = array('hidCashAdvanceKey',array('mandatory'=>true));
    	$this->arrCashDetail['amount'] = array('cashAdvanceAmount',array('datatype' => 'number','mandatory'=>true));        
	        
		$this->arrCostDetail = array(); 
        $this->arrCostDetail['pkey'] = array('hidDetailCostKey');
        $this->arrCostDetail['refkey'] = array('pkey', 'ref');
        $this->arrCostDetail['amount'] = array('costAmount',array('datatype' => 'number','mandatory'=>true));
        $this->arrCostDetail['costkey'] = array('hidCostKey',array('mandatory'=>true));         
			
		$this->arrDataDetail = array();  
		$this->arrDataDetail['pkey'] = array('hidDetailKey');
        $this->arrDataDetail['refkey'] = array('pkey','ref'); 
        $this->arrDataDetail['servicekey'] = array('hidServiceKey'); 
        $this->arrDataDetail['joborderkey'] = array('hidJobOrderKey');
        $this->arrDataDetail['amount'] = array('amountDetail','number'); 
        $this->arrDataDetail['qty'] = array('qty','number'); 
        $this->arrDataDetail['ispriceincludetax'] = array('chkIncludeTax'); 
        $this->arrDataDetail['taxpercentage'] = array('taxPercentage','number'); 
        $this->arrDataDetail['taxvalue'] = array('taxValueDetail','number'); 
        $this->arrDataDetail['beforetaxtotal'] = array('beforeTaxTotalDetail','number'); 
        $this->arrDataDetail['subtotal'] = array('subtotal',array('datatype' => 'number','mandatory'=>true));        
        $this->arrDataDetail['itemkey'] = array('hidContainerDetailKey'); 
        $this->arrDataDetail['supplierkey'] = array('hidSupplierKey');
        $this->arrDataDetail['cashtypekey'] = array('selJobType');
        $this->arrDataDetail['downpaymentkey'] = array('hidDownpaymentKey');
        $this->arrDataDetail['coakey'] = array('hidCOAKey');
        $this->arrDataDetail['refcode'] = array('refCode');
        $this->arrDataDetail['description'] = array('description');
        $this->arrDataDetail['jobheaderkey'] = array('hidJobHeaderKey'); 
        $this->arrDataDetail['isreimburse'] = array('chkIsReimburse');
        $this->arrDataDetail['pphamount'] = array('pphAmount','number');
        $this->arrDataDetail['pphtype'] = array('selPPhType');

        $arrDetails = array();
        array_push($arrDetails, array('dataset' => $this->arrDataDetail));
		array_push($arrDetails, array('dataset' => $this->arrCashDetail, 'tableName' => $this->tableNameDetailAdvance));
		array_push($arrDetails, array('dataset' => $this->arrCostDetail, 'tableName' => $this->tableCost));

        $this->arrData = array(); 
        $this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails));  
        $this->arrData['code'] = array('code');
        //$this->arrData['refkey'] = array('hidCashAdvanceKey');
        $this->arrData['warehousekey'] = array('selWarehouseKey');
        $this->arrData['amount'] = array('amount','number');
        $this->arrData['total'] = array('total','number');
        $this->arrData['totalcost'] = array('totalCost','number');
        $this->arrData['balance'] = array('balance','number');
        $this->arrData['trdate'] = array('trDate','date');
        $this->arrData['trdesc'] = array('note');
        $this->arrData['statuskey'] = array('selStatus');
        $this->arrData['cashadvancecoakey'] = array('cashadvancecoakey');
        $this->arrData['coakey'] = array('hidCOAClosingKey');
        $this->arrData['cashadvancecache'] = array('cashadvancecache');
        $this->arrData['totalpph'] = array('totalPPH','number');


        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'date','title' => 'date','dbfield' => 'trdate','default'=>true, 'width' => 100, 'align' =>'center','format' => 'date'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'cashAdvanceCode','title' => 'reference','dbfield' => 'cashadvancecache','default'=>true, 'width' => 120)); 
       // array_push($this->arrDataListAvailableColumn, array('code' => 'recipient','title' => 'recipient','dbfield' => 'employeename','default'=>true, 'width' => 180)); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'warehouse','title' => 'warehouse','dbfield' => 'warehousename','default'=>true, 'width' => 120));    
        array_push($this->arrDataListAvailableColumn, array('code' => 'desc','title' => 'note','dbfield' => 'trdesc','default'=>true, 'width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'cashAdvance','title' => 'cashAdvance','dbfield' => 'amount','default'=>true,  'width' => 100,'align' => 'right', 'format' => 'number'));    
	    array_push($this->arrDataListAvailableColumn, array('code' => 'total','title' => 'settlement','dbfield' => 'total', 'width' => 90,'align' => 'right', 'format' => 'number'));    
        array_push($this->arrDataListAvailableColumn, array('code' => 'balance','title' => 'balance','dbfield' => 'balance', 'default'=>true,  'width' => 90,'align' => 'right', 'format' => 'number'));    
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));

        array_push($this->filterCriteria, array('title' => $this->lang['warehouse'], 'field' => 'warehousekey'));
        
        $this->activeModule = $this->isActiveModule(array('APPayableTax23', 'CashBank'));
            
        $this->printMenu = array();
        array_push($this->printMenu,array('code' => 'printTransaction', 'name' => $this->lang['printTransaction'],  'icon' => 'print', 'url' => 'print/cashAdvanceRealization'));


        $this->includeClassDependencies(array(
                'Warehouse.class.php',
                'CashAdvance.class.php',  
                'CashBank.class.php',  
                'CostCashOut.class.php', 
                'ChartOfAccount.class.php', 
                'EMKLJobOrder.class.php',  
                'EMKLJobOrderHeader.class.php',  
                'EMKLPurchaseOrder.class.php',  
                'Employee.class.php',  
                'Item.class.php' , 
                'Downpayment.class.php',  
                'SupplierDownpayment.class.php',
                'ChartOfAccount.class.php',
                'Customer.class.php',
                'AP.class.php',
                'APPayableTax23.class.php'
        ));  

        $this->overwriteConfig();

    }

      function getQuery(){
         
        $sql = '
            SELECT
                '.$this->tableName.'.* , 
                '.$this->tableWarehouse.'.name as warehousename,
				coaclosing.name as coaname,
                coaclosing.code as coacode, 
                concat(coaclosing.code," - " ,coaclosing.name ) as coacodename,
				coaadvance.name as coaadvancename,
                coaadvance.code as coaadvancecode, 
                concat(coaadvance.code," - " ,coaadvance.name ) as coaadvancecodename,
                '.$this->tableStatus.'.status as statusname
                
            FROM '.$this->tableStatus.',
                 '.$this->tableWarehouse.', 
                 '.$this->tableName.'
				 	left join '.$this->tableCOA.' coaadvance on  '.$this->tableName.'.cashadvancecoakey = coaadvance.pkey 
				 	left join '.$this->tableCOA.' coaclosing on  '.$this->tableName.'.coakey = coaclosing.pkey 
            WHERE   
                  '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey and 
                  '.$this->tableName.'.warehousekey = '.$this->tableWarehouse.'.pkey 

            ' .$this->criteria ;
                                         
            $sql .=  $this->getWarehouseCriteria() ;
        
        return $sql;
    }
    
    function afterStatusChanged($rsHeader){   
        $rsHeader = $this->getDataRowById($rsHeader[0]['pkey']);
        $rsDetailCash = $this->getDetailCashAdvance($rsHeader[0]['pkey']);
        // closing cash advance
		$cashAdvance = new CashAdvance();
		for($i=0;$i<count($rsDetailCash);$i++)
			$cashAdvance->updateCashAdvance($rsDetailCash[$i]['cashadvancekey']);
        
       
      /* // gk bisa ubah otomatis, karena boleh ubah manual backconfirm ut kupdaate cost
       if ($rsHeader[0]['statuskey'] == TRANSACTION_STATUS['konfirmasi'])
            $this->reupdateStatus($rsHeader[0]['pkey']);*/
            
    }

    function afterUpdateData($arrParam, $action){
        
        // cuma add yg blm ad saja
        
        $pkey = $arrParam['pkey'];
        $rsHeader = $this->getDataRowById($pkey);
        
		if($rsHeader[0]['statuskey']==2){
            // kalo cuma cost yg berubah, gk keupdate jadiny
            
            $arrExceptionType = array(3);
            
			// mau cek balancenya jg gkbisa, ad kemungkinan jumlah sama, jenis biaya berbeda
			// gk bisa, karena ad kemungkinan baris barunya dicancel dan baris2 lainnya sudah diproses, jadinya rsDetailnya kosong
			$rsDetail = $this->getDetailWithRelatedInformation($rsHeader[0]['pkey'],' and ( reftranskey = 0 or cashtypekey in ('.$this->oDbCon->paramString($arrExceptionType,',').') )');
			
			// kalo nilainya berubah
			// kalo yg jenisnya cost langsung bagaimana ?? 
			$this->cancelGLByRefkey($rsHeader[0]['pkey'],$this->tableName);
			$this->updateTransDetail($rsHeader,$rsDetail);
			
//			if(!empty($rsDetail))
//				$this->updateTransDetail($rsHeader,$rsDetail);  
	 		
		}
      	      

    }
    
    function getCostDetail($pkey){
		$sql = 'select 
					'. $this->tableCost.'.* ,
					'. $this->tableCostCashOut.'.name as costname
				from 
					'. $this->tableCost.',
                    '.$this->tableCostCashOut.', 
                    '. $this->tableName.'  
				where 
					'. $this->tableCost.'.refkey = '. $this->tableName.'  .pkey and
                     '.$this->tableCost.'.costkey = '.$this->tableCostCashOut.'.pkey  and
					'. $this->tableName.'.pkey = ' .$this->oDbCon->paramString($pkey).'
				order by  pkey asc'; 
        
     	return $this->oDbCon->doQuery($sql);
	}   
    

     function reCountGrandtotal($arrParam){

        $balance = 0;
        $total = 0;
        $taxValue = 0;

//        $arrAmount= $this->unFormatNumber($arrParam['amount']); 
        $arrAmount= 0; 
        $arrAmountDetail = $arrParam['amountDetail']; 
        $arrQty = $arrParam['qty']; 
        $arrTaxPercentage = $arrParam['taxPercentage']; 
        $arrIncludeTax = $arrParam['chkIncludeTax']; 
        $arrCashAdvance = $arrParam['hidCashAdvanceKey']; 
        $arrCashAmount = $arrParam['cashAdvanceAmount']; 
        $arrPPHAmount = $arrParam['pphAmount']; 

        $arrTotalDetail = array();
		
		for($j=0;$j<count($arrCashAdvance);$j++){
			$cashAmount = $this->unFormatNumber($arrCashAmount[$j]);
			if($cashAmount<=0 || empty($arrCashAdvance[$j]))
				continue;
			
			$arrAmount += $cashAmount;
			
		}

        $totalPPH = 0;
        for ($i=0;$i<count($arrAmountDetail);$i++){

            $amountDetail = $this->unFormatNumber($arrAmountDetail[$i]);
            $qtyDetail = $this->unFormatNumber($arrQty[$i]);
            $taxPercentageDetail = $this->unFormatNumber($arrTaxPercentage[$i]);
            $pphAmount = $this->unFormatNumber($arrPPHAmount[$i]);
            
            $includeTaxDetail = $arrIncludeTax[$i];
            if (empty($amountDetail) || empty($qtyDetail)) 
                continue;
            
            $beforeTaxTotal = $amountDetail * $qtyDetail;
            $grandtotal = $beforeTaxTotal;
            if ($includeTaxDetail == false) {
                $taxValue = $beforeTaxTotal * $taxPercentageDetail / 100;
                $grandtotal += $taxValue;
            }else{
                $taxValue = ($taxPercentageDetail/(100 + $taxPercentageDetail)) * $grandtotal;   
                $beforeTaxTotal = $grandtotal - $taxValue ;
            }
            $arrTotalDetail[$i]['subtotalDetail'] = $grandtotal;    
            $arrTotalDetail[$i]['beforeTaxTotalDetail'] = $beforeTaxTotal;    
            $arrTotalDetail[$i]['taxValueDetail'] = $taxValue;    
            
            $total += $grandtotal;
            $totalPPH += $pphAmount;
        } 
        
         
		$costCashOut= new CostCashOut();
		$totalCost = 0; 
		$costAmount = $arrParam['costAmount'];
		$costKey = $arrParam['hidCostKey'];
		 	
		$rsItem = $costCashOut->searchDataRow(array($costCashOut->tableName.'.pkey'), ' and '.$costCashOut->tableName.'.pkey in ('.$this->oDbCon->paramString($costKey,',').')'); 
		$rsItem = array_column($rsItem,'pkey');
		 
		for($i=0;$i<count($costAmount);$i++){
			if(!in_array($costKey[$i],$rsItem)) continue;  
			$totalCost += $this->unFormatNumber($costAmount[$i]); 
		}  

		$total += $totalCost;
        $balance = $arrAmount - $total; 
        
        $reCountResult = array();
        $reCountResult['amount'] = $arrAmount; 
        $reCountResult['total'] = $total; 
        $reCountResult['balance'] = $balance + $totalPPH; 
        $reCountResult['detailSubtotal'] = $arrTotalDetail; 
        $reCountResult['totalCost'] = $totalCost;         
        $reCountResult['totalPPH'] = $totalPPH;         
        return $reCountResult;
				
	}
    
    function validateForm($arr,$pkey = ''){  
        
        // VALIDASI jumlah harus sesuai kalo sudah dikonfirmasi
        
        $cashAdvance = new CashAdvance(); 
        
        $arrayToJs = parent::validateForm($arr,$pkey);  
 		//$cashAdvanceKey = $arr['hidCashAdvanceKey'];
 		$arrServiceKey = $arr['hidServiceKey'];
 		$arrAmountDetail = $arr['amountDetail']; 
 		$arrQty = $arr['qty'];
 		$cashAmount = $this->unFormatNumber($arr['amount']);
		$coaClosingKey = $arr['hidCOAClosingKey'];
		$arrJob = $arr['selJobType'];
		$arrSupplier = $arr['hidSupplierKey'];
		$arrJO = $arr['hidJobOrderKey'];
		$arrJOHeader = $arr['hidJobHeaderKey'];
		$arrCostCoa = $arr['hidCOAKey'];
		$arrCashAdvance = $arr['hidCashAdvanceKey'];
		$warehousekey = $arr['selWarehouseKey'];
		$arrSubtotal = $arr['subtotal'];
        $arrIsReimburse = $arr['chkIsReimburse'];
        $arrTaxPercentage = $arr['taxPercentage'];

        $item = new Item();
   
        if(empty($arrJob)) 
            $this->addErrorList($arrayToJs,false,  $this->errorMsg[501]);
        
		if(empty($coaClosingKey)) 
            $this->addErrorList($arrayToJs,false,  $this->errorMsg['cashAdvanceRealization'][5]);  
        
      	 
		if(empty($arrCashAdvance)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['cashAdvance'][1]); 
		}else{
            
            // cek ad yg double gk
            $arrCashBankKey = array();
            foreach($arrCashAdvance as $row){ 
                if(in_array($row,$arrCashBankKey)){ 
                    $this->addErrorList($arrayToJs,false,$this->errorMsg[215]);
                    break;
                }
                array_push($arrCashBankKey,$row); 
            }
            
            
            // cek semua dr coa dan supplier yg sama gk
            // cek statusnya masih available gk
            $rsCA = $cashAdvance->searchDataRow( array($cashAdvance->tableName.'.pkey', $cashAdvance->tableName.'.cashadvancecoakey',  $cashAdvance->tableName.'.employeekey',  $cashAdvance->tableName.'.warehousekey'),
                                                 'and '.$cashAdvance->tableName.'.pkey in (' .$this->oDbCon->paramString($arrCashAdvance,',').')'  
                                                ); 
            
            $employeekey = $rsCA[0]['employeekey'];
            foreach($rsCA as $row){
                if($row['employeekey'] <> $employeekey){ 
                    $this->addErrorList($arrayToJs,false,$this->errorMsg['recipient'][2]);
                    break;
                }
                
		        if($row['warehousekey'] <> $warehousekey){ 
                    $this->addErrorList($arrayToJs,false,$this->errorMsg['cashAdvanceRealization'][7]);
                    break;
                }
            }
            
            // sementara
            if(!in_array(DOMAIN_NAME, array('cif.wintera.co.id'))){ 
                $coakey = $rsCA[0]['cashadvancecoakey'];
                foreach($rsCA as $row){
                    if($row['cashadvancecoakey'] <> $coakey){ 
                        $this->addErrorList($arrayToJs,false,$this->errorMsg['coa'][5]);
                        break;
                    }
                }
            }
            
            // cari sudah ad tutupan yg sama blm
            $sql = 'select  
					'.$this->tableName.'.pkey  
				from 
					'.$this->tableName.','.$this->tableNameDetailAdvance.'
				where 
					'.$this->tableNameDetailAdvance.'.refkey = '.$this->tableName.'.pkey and
					'.$this->tableName.'.pkey <> ' .$this->oDbCon->paramString($pkey).'and 
					'.$this->tableName.'.statuskey <> '.$this->oDbCon->paramString(TRANSACTION_STATUS['batal']).' and
					'.$this->tableNameDetailAdvance.'.cashadvancekey in  ('.$this->oDbCon->paramString($arrCashAdvance,',') . ')
					';
		    $rsCA = $this->oDbCon->doQuery($sql);
            
            if(!empty($rsCA))            
                $this->addErrorList($arrayToJs,false,$this->errorMsg['cashAdvanceRealization'][4]);
		}
         
		$totalRows = count($arrJob);
		if($totalRows == 1 && $arrSubtotal[0] == 0 ){
			// kalo kosong, gk kepake duitnya gpp
			
		}else{
			
			$totalAmount =0;
			for($i=0;$i<$totalRows;$i++){

				// kalo tipenya job order
				if($arrJob[$i]==1 || $arrJob[$i]==4){
					if(empty($arrServiceKey[$i])) $this->addErrorList($arrayToJs,false,$this->errorMsg['service'][1]);
					if((empty($arrJO[$i]) && $arrJob[$i]==1) || (empty($arrJOHeader[$i]) && $arrJob[$i]==4)) $this->addErrorList($arrayToJs,false,$this->errorMsg['jobOrder'][1]);
				}

				// utk tipe job order dan DP, harus ad supplier
				if(($arrJob[$i]==1 || $arrJob[$i]==2) && empty($arrSupplier[$i]))
					$this->addErrorList($arrayToJs,false, $this->errorMsg['supplier'][1]);

				// utk tipe cost langsung
				if($arrJob[$i]==3 && empty($arrCostCoa[$i]))
					$this->addErrorList($arrayToJs,false,$this->errorMsg['coa'][1]);


				$rsItem = $item->getDataRowById($arrServiceKey[$i]);

				$amount = $this->unFormatNumber($arrAmountDetail[$i]);
				$qty = $this->unFormatNumber($arrQty[$i]);

				if($amount<=0) $this->addErrorList($arrayToJs,false,$rsItem[0]['name']. '. '.$this->errorMsg['amount'][1]);  
				if($qty <= 0)  $this->addErrorList($arrayToJs,false,$rsItem[0]['name']. '. ' . $this->errorMsg[510]);  
                
                if($arrIsReimburse[$i] < 0) {
                    $this->addErrorList($arrayToJs, false, '<strong>'. $rsItem[0]['name'] .'. </strong>'. $this->errorMsg['purchaseOrder'][6]);
                }  else if ($arrIsReimburse[$i] == 1 && $arrTaxPercentage[$i] > 0) { 
                        $this->addErrorList($arrayToJs, false, '<strong>'. $rsItem[0]['name'] .'. </strong>' . $this->errorMsg['purchaseOrder'][5]); 
                }


			}

		}
		
        
         
        // cek status JO dan Header
        // JO & Header gk boleh status cancel
		// kalo status konfirmasi saja, agar lebih mudah ambil transction detail. karena kalo menunggu sudah divalidasi ketika validate confirm
         
        if(!empty($pkey)){ 
            
            $rsHeader = $this->searchDataRow( array($this->tableName.'.pkey',$this->tableName.'.code',$this->tableName.'.statuskey'),
                                                 'and '.$this->tableName.'.pkey = '.$this->oDbCon->paramString($pkey)
                                                );
            
            if($rsHeader[0]['statuskey'] == 2){
                

                $emklJobOrder = new EMKLJobOrder(); 
                $emklJobOrderHeader = new EMKLJobOrderHeader(); 
 
                $id = $pkey;
                
                // job order
                $rsJobExisted = $emklJobOrder->searchDataRow( array($emklJobOrder->tableName.'.pkey',$emklJobOrder->tableName.'.code',$emklJobOrder->tableName.'.statuskey'),
                                                     'and '.$emklJobOrder->tableName.'.pkey in ('.$this->oDbCon->paramString(array_unique($arrJO),',').')'
                                                      );
                $arrJobExisted = array_column($rsJobExisted,null,'pkey'); 
                $arrJobKey = array_column($rsJobExisted,'pkey');
 
                // header
                $rsJobHeaderExisted = $emklJobOrderHeader->searchDataRow( array($emklJobOrderHeader->tableName.'.pkey',$emklJobOrderHeader->tableName.'.code',$emklJobOrderHeader->tableName.'.statuskey'),
                                                     'and '.$emklJobOrderHeader->tableName.'.pkey in ('.$this->oDbCon->paramString(array_unique($arrJOHeader),',').')'
                                                      );
                $arrJobHeader = array_column($rsJobHeaderExisted,null,'pkey'); 
                $arrJobHeaderKey = array_column($rsJobHeaderExisted,'pkey');
 
                for($i=0;$i<$totalRows;$i++){
                    if($arrJob[$i]==1){
                        // cek ulang job order nya masih valid gk 
                        if(!in_array($arrJO[$i],$arrJobKey)){
                            $this->addErrorList($arrayToJs,false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg['jobOrder'][1]);
                        }else if( $arrJobExisted[$arrJO[$i]]['statuskey'] ==  TRANSACTION_STATUS['batal'] ){ 
                            $this->addErrorList($arrayToJs,false,'<strong>'.$rsHeader[0]['code'].'</strong>. <strong>'.$arrJobExisted[$arrJO[$i]]['code'].'</strong>, '.$this->errorMsg['jobOrder'][2]);
                        }

                    }else if($arrJob[$i]==4){ 
                        if(!in_array($arrJOHeader[$i],$arrJobHeaderKey)){
                            $this->addErrorList($arrayToJs,false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg['jobOrderHeader'][1]);
                        }else if( $arrJobHeader[$arrJOHeader[$i]]['statuskey'] ==  TRANSACTION_STATUS['batal'] ){ 
                            // status beda dengan pas konfirmasi, karena header bisa sj sdh selesai
                            $this->addErrorList($arrayToJs,false,'<strong>'.$rsHeader[0]['code'].'</strong>. <strong>'.$arrJobHeader[$arrJOHeader[$i]]['code'].'</strong>, '.$this->errorMsg['jobOrderHeader'][2]);
                        }				 
                    }		
                }
            } 

        }
        // akhir cek status

        return $arrayToJs;
    }
    
    function validateConfirm($rsHeader){
        
        $cashAdvance = new CashAdvance(); 
        $emklJobOrder = new EMKLJobOrder(); 
        $emklJobOrderHeader = new EMKLJobOrderHeader(); 
        $coaLink = new COALink();
        $warehouse = new Warehouse();
        
        $id = $rsHeader[0]['pkey'];
         
        
		$rsDetail = $this->getDetailById($id);
		$rsDetailCash = $this->getDetailCashAdvance($id);
        $jobOrderKey = array_column($rsDetail,'joborderkey');
        $jobHeaderKey = array_column($rsDetail,'jobheaderkey');

        // job order
        $rsJobExisted = $emklJobOrder->searchDataRow( array($emklJobOrder->tableName.'.pkey',$emklJobOrder->tableName.'.code',$emklJobOrder->tableName.'.statuskey'),
                                             'and '.$emklJobOrder->tableName.'.pkey in ('.$this->oDbCon->paramString($jobOrderKey,',').')'
                                              );
        $arrJob = array_column($rsJobExisted,null,'pkey'); 
        $arrJobKey = array_column($rsJobExisted,'pkey');
         
        
        // header
        $rsJobHeaderExisted = $emklJobOrderHeader->searchDataRow( array($emklJobOrderHeader->tableName.'.pkey',$emklJobOrderHeader->tableName.'.code',$emklJobOrderHeader->tableName.'.statuskey'),
                                             'and '.$emklJobOrderHeader->tableName.'.pkey in ('.$this->oDbCon->paramString($jobHeaderKey,',').')'
                                              );
        $arrJobHeader = array_column($rsJobHeaderExisted,null,'pkey'); 
        $arrJobHeaderKey = array_column($rsJobHeaderExisted,'pkey');
         
        $totalRsDetail = count($rsDetail);
		for($i=0;$i<$totalRsDetail;$i++){
			if($rsDetail[$i]['cashtypekey']==1){
                // cek ulang job order nya masih valid gk 
				if(!in_array($rsDetail[$i]['joborderkey'],$arrJobKey)){
					$this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg['jobOrder'][1]);
                }else if( $arrJob[$rsDetail[$i]['joborderkey']]['statuskey'] ==  TRANSACTION_STATUS['batal'] ){ 
					$this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. <strong>'.$arrJob[$rsDetail[$i]['joborderkey']]['code'].'</strong>, '.$this->errorMsg['jobOrder'][2]);
                }
				 
			}else if($rsDetail[$i]['cashtypekey']==4){ 
                if(!in_array($rsDetail[$i]['jobheaderkey'],$arrJobHeaderKey)){
					$this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg['jobOrderHeader'][1]);
                }else if( !in_array( $arrJobHeader[$rsDetail[$i]['jobheaderkey']]['statuskey'], array(TRANSACTION_STATUS['menunggu']))){ 
					$this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. <strong>'.$arrJobHeader[$rsDetail[$i]['jobheaderkey']]['code'].'</strong>, '.$this->errorMsg['jobOrderHeader'][2]);
                }				 
			}		
        }
        
        $totalCash = count($rsDetailCash);
		for($i=0;$i<$totalCash;$i++){
			$rsCashAdvance = $cashAdvance->getDataRowById($rsDetailCash[$i]['cashadvancekey']);
			 // cuma boleh konfirmasi kasbon yg masih menunggu
        	if(empty($rsCashAdvance) || $rsCashAdvance[0]['statuskey'] != TRANSACTION_STATUS['konfirmasi'])
            	$this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg['cashAdvanceRealization'][2]);

		}
        if (USE_GL){ 
            $arrCOA = array();
            array_push($arrCOA, 'taxin'); 
            for ($i=0;$i<count($arrCOA);$i++){
                $rsCOA = $coaLink->getCOALink ($arrCOA[$i], $warehouse->tableName,$rsHeader[0]['warehousekey'], 0); 
                if (empty($rsCOA))	
                    $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$arrCOA[$i]. ' ' .$this->errorMsg['coa'][3]);
            }   
 
        }
    }		
    
    function validateCancel($rsHeader, $autoChangeStatus = false){ 
         
        $emklPurchaseOrder = new EMKLPurchaseOrder();
        $supplierDownpayment = new SupplierDownpayment();

        $id = $rsHeader[0]['pkey'];
        $rsPurchase = $emklPurchaseOrder-> searchDataRow( array(  $emklPurchaseOrder->tableName.'.pkey', $emklPurchaseOrder->tableName.'.code'  ) , 
                                '   and '.$emklPurchaseOrder->tableName.'.refcashadvancekey = '.$this->oDbCon->paramString($id).'
                                    and '.$emklPurchaseOrder->tableName.'.statuskey in ('.TRANSACTION_STATUS['konfirmasi'].','.TRANSACTION_STATUS['selesai'].')'  
                       ); 
       
        if (!empty($rsPurchase)) 
           $this->addErrorLog( false, '<strong>'.$rsPurchase[0]['code'].'</strong> ' .$this->errorMsg[201].'<br><strong>'.$rsPurchase[0]['code'].'</strong>, ' .$this->errorMsg[225] );
 
   
        $rsDownpayment = $supplierDownpayment-> searchDataRow( array(  $supplierDownpayment->tableName.'.pkey', $supplierDownpayment->tableName.'.code'  ) , 
                                '   and '.$supplierDownpayment->tableName.'.refcashadvancekey = '.$this->oDbCon->paramString($id).'
                                    and '.$supplierDownpayment->tableName.'.statuskey in ('.TRANSACTION_STATUS['konfirmasi'].','.TRANSACTION_STATUS['selesai'].')'  
                       ); 
       
        if (!empty($rsDownpayment)) 
           $this->addErrorLog( false, '<strong>'.$rsDownpayment[0]['code'].'</strong> ' .$this->errorMsg[201].'<br><strong>'.$rsDownpayment[0]['code'].'</strong>, ' .$this->errorMsg[225] );
 

   
        if( $this->activeModule['appayabletax23'] ){
            //cek ad Prepaid yg ad bukti potongnya blm 
            $apPayableTax = new APPayableTax23();
            $rsKey = $this->getTableKeyAndObj($this->tableName,array('key'));                  
            $rsAP = $apPayableTax->searchData('','',true,' and refheaderkey = '.$this->oDbCon->paramString($id).' and '.$apPayableTax->tableName.'.reftabletype = '.$rsKey['key'].' and ('.$apPayableTax->tableName.'.statuskey in (2,3) )');
        
            if(!empty($rsAP)) {
                $arrAP = array_column($rsAP,'code');
                $this->addErrorLog( false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' . $this->errorMsg[201].' Bukti bayar sudah diinput.<br>' . implode(', ', $arrAP ).'.');
            }
        }
    } 
	 
	function validateBackConfirm($rsHeader){ 
        // harus bisa balikin ke backconfirm, karena mungkin perlu revisi cost 
         
    }		
     
	function validateClose($rsHeader){
        
        $emklJobOrder = new EMKLJobOrder(); 
        $emklJobOrderHeader = new EMKLJobOrderHeader(); 
            
        $id = $rsHeader[0]['pkey'];
        
        // boleh selesai, tp harus cek ulagn dulu
        // kecuali yg tipenya cost
        $arrExceptionType = array(3);
        
        $rsDetail = $this->getDetailByColumn($this->tableNameDetail.'.refkey',$rsHeader[0]['pkey'],true,' and reftranskey = 0 and cashtypekey not in ('.$this->oDbCon->paramString($arrExceptionType,',').')');   
        if(!empty($rsDetail))
           $this->addErrorLog( false, '<strong>'.$rsHeader[0]['code'].'</strong> ' .$this->errorMsg['cashAdvanceRealization'][6]);
            
        
        // validasi status
		$rsDetail = $this->getDetailById($id);
        $jobOrderKey = array_column($rsDetail,'joborderkey');
        $jobHeaderKey = array_column($rsDetail,'jobheaderkey');
        
        
       // job order
        $rsJobExisted = $emklJobOrder->searchDataRow( array($emklJobOrder->tableName.'.pkey',$emklJobOrder->tableName.'.code',$emklJobOrder->tableName.'.statuskey'),
                                             'and '.$emklJobOrder->tableName.'.pkey in ('.$this->oDbCon->paramString($jobOrderKey,',').')'
                                              );
        $arrJob = array_column($rsJobExisted,null,'pkey'); 
        $arrJobKey = array_column($rsJobExisted,'pkey');
         
        
        // header
        $rsJobHeaderExisted = $emklJobOrderHeader->searchDataRow( array($emklJobOrderHeader->tableName.'.pkey',$emklJobOrderHeader->tableName.'.code',$emklJobOrderHeader->tableName.'.statuskey'),
                                             'and '.$emklJobOrderHeader->tableName.'.pkey in ('.$this->oDbCon->paramString($jobHeaderKey,',').')'
                                              );
        $arrJobHeader = array_column($rsJobHeaderExisted,null,'pkey'); 
        $arrJobHeaderKey = array_column($rsJobHeaderExisted,'pkey');
         
        $totalRsDetail = count($rsDetail);
		for($i=0;$i<$totalRsDetail;$i++){
			if($rsDetail[$i]['cashtypekey']==1){
                // cek ulang job order nya masih valid gk 
				if(!in_array($rsDetail[$i]['joborderkey'],$arrJobKey)){
					$this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg['jobOrder'][1]);
                }else if( $arrJob[$rsDetail[$i]['joborderkey']]['statuskey'] ==  TRANSACTION_STATUS['batal'] ){ 
					$this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. <strong>'.$arrJob[$rsDetail[$i]['joborderkey']]['code'].'</strong>, '.$this->errorMsg['jobOrder'][2]);
                }
				 
			}else if($rsDetail[$i]['cashtypekey']==4){ 
                if(!in_array($rsDetail[$i]['jobheaderkey'],$arrJobHeaderKey)){
					$this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg['jobOrderHeader'][1]);
                }else if( $arrJobHeader[$rsDetail[$i]['jobheaderkey']]['statuskey'] ==  TRANSACTION_STATUS['batal'] ){ 
					// status beda dengan pas konfirmasi, karena header bisa sj sdh selesai
                    $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. <strong>'.$arrJobHeader[$rsDetail[$i]['jobheaderkey']]['code'].'</strong>, '.$this->errorMsg['jobOrderHeader'][2]);
                }				 
			}		
        }
        
        
	 }	 
    
    function addPO($rsHeader,$rsDetail,$cashType){
        
        //$autoSellingReimburse = (in_array($this->loadSetting('autoSellingReimburse'),array(1,2)))  ?  true : false;
        
        $emklJobOrder = new EMKLJobOrder();
        $emklJobOrderHeader = new EMKLJobOrderHeader();
        $emklPurchaseOrder = new EMKLPurchaseOrder();
             
        $useJobOrderDate = $this->loadSetting('useJobOrderDateForPurchaseOrderOnCashAdvanceClosing');
        if($cashType==1){
            // tipe Job Order
            $objJobOrder = $emklJobOrder;
            $joIndexKey =  'joborderkey';
            $hidJOIndexKey =  'hidJobOrderKey';
        }else{
            // tipe Job Order Header
            $objJobOrder = $emklJobOrderHeader ; 
            $joIndexKey =  'jobheaderkey';
            $hidJOIndexKey =  'hidJobHeaderKey';
        }   
         
        $rsJobCol =  $objJobOrder->searchDataRow( array($objJobOrder->tableName.'.pkey', $objJobOrder->tableName.'.trdate', $objJobOrder->tableName.'.jobtypekey'),
                                                 ' and '.$objJobOrder->tableName.'.pkey in ('.$this->oDbCon->paramString( array_column($rsDetail,$joIndexKey) ,',').') ' 
                                                );
        $rsJobCol = array_column($rsJobCol,null,'pkey'); // ganti index berdasarkan pkey
        //$this->setLog($rsJobCol,true);
        
        // ambil detail JO utk auto add selling
        //if($autoSellingReimburse){ 
        //    $rsJobDetail = $objJobOrder->getDetailByColumn('refkey',array_column($rsJobCol,'pkey')); 
        //    $rsJobDetailCols = $this->reindexDetailCollections($rsJobDetail,'refkey');    
        //}
            
        $arrPurchase = array();
        for($i=0;$i<count($rsDetail);$i++){  
            $rsDetail[$i]['refcode'] = trim($rsDetail[$i]['refcode']);
                
            // harus dipisah export atau import 
            // tambahin refcode, karena gk boleh pake refcode yg sama
            // dipisah reimburse atau reguler
            $uniqueIndex = $rsDetail[$i][$joIndexKey].'-'.$rsJobCol[$rsDetail[$i][$joIndexKey]]['jobtypekey'].'-'.$rsDetail[$i]['supplierkey'].'-'.$this->unformatNumber($rsDetail[$i]['taxpercentage']).'-'.$rsDetail[$i]['ispriceincludetax'].'-'.$rsDetail[$i]['refcode'].'-'.$rsDetail[$i]['isreimburse']; 
            
            if(!isset($arrPurchase[$uniqueIndex]))
                $arrPurchase[$uniqueIndex] = array();

            array_push($arrPurchase[$uniqueIndex],$rsDetail[$i]);
        }
        $rsJOType =  $this->getTableKeyAndObj($objJobOrder->tableName,array('key'));
        $rsPurchaseKey = $emklPurchaseOrder->getTableKeyAndObj($emklPurchaseOrder->tableName,array('key')); 
        
        //$this->setLog($arrPurchase,true);
        
        foreach($arrPurchase as $index=>$row){
            $arrParam = array();
            $taxValue = 0;
			$invCode = '';
			$detailDesc = array();
             
            for($i=0;$i<count($row);$i++){ 

                $jobDetailKey = 0;
                
                // dari tutupan kasbon tidak auto add
                //if($autoSellingReimburse){ 
                //    $rsJobDetailCol = $rsJobDetailCols[$row[$i][$joIndexKey]]; 
                //    $jobDetailKey = !empty($rsJobDetailCol) ? $rsJobDetailCol[0]['pkey'] : 0; //ambil detail JO pertama, kalau tidak ada detail kosongkan
                //}
                
                $arrParam['hidDetailKey'][$i] = 0;
                $arrParam['hidContainerDetailKey'][$i] = $row[$i]['itemkey'];
                $arrParam['selJobOrderDetailKey'][$i] = $jobDetailKey;
                $arrParam['hidServiceKey'][$i] = $row[$i]['servicekey'];
                $arrParam['qty'][$i] =  $row[$i]['qty']; 
                $arrParam['priceInUnit'][$i] =  $row[$i]['amount'];
                $arrParam['selCurrencyDetail'][$i] = CURRENCY['idr'];
				$arrParam['description'][$i] =  $row[$i]['description'];
				$arrParam['detailPPHAmount'][$i] =  $row[$i]['pphamount'];
				$arrParam['selPPhType'][$i] =  $row[$i]['pphtype'];
                
                $taxValue += $row[$i]['taxvalue'];

                $row[$i]['refcode'] = $row[$i]['refcode']; // sudh ditrim diatas
                
				if(!empty($row[$i]['description'])  && !in_array($row[$i]['description'],$detailDesc)) array_push($detailDesc,$row[$i]['description']);
				//if(!empty($row[$i]['refcode']) && !in_array($row[$i]['refcode'],$invCode)) array_push($invCode,$row[$i]['refcode']); // harusnya gk mungkin double lg, karena refcode sudah masuk jd unique index
                
                if(!empty($row[$i]['refcode']) && !empty($invCode) && $row[$i]['refcode'] != $invCode)
                    throw new Exception('<strong>'.$rsHeader[0]['code'] . '</strong>. '.$this->errorMsg[201].' '.$invCode. ' '.$this->errorMsg['invoice'][5]); 
                else
                    $invCode = $row[$i]['refcode'];
            }
 
            $rsJob = $rsJobCol[$row[0][$joIndexKey]] ; // $objJobOrder->getDataRowById($row[0][$joIndexKey]);
            

            if($useJobOrderDate == 2) {
                //pakai tgl Job Order
                $poDate = $rsJob['trdate'];
            } else  {
                //pakai tgl cash advance realization
                $poDate = $rsHeader[0]['trdate'];
            }
                  
            $arrParam['code'] = 'xxxxxx';
            $arrParam[$hidJOIndexKey] = $rsJob['pkey']; 
            
            $arrParam['selTypeOfJob'] = $rsJob['jobtypekey'];
            $arrParam['trDate'] = $this->formatDBDate($poDate,'d / m / Y');
            $arrParam['selWarehouseKey'] = $rsHeader[0]['warehousekey'];
            $arrParam['hidSupplierKey'] = $row[0]['supplierkey'];
            $arrParam['selCurrency'] = CURRENCY['idr'];
            $arrParam['currencyRate'] = 1;
            $arrParam['selJOType'] = $rsJOType['key'];
            $arrParam['islinked'] = 1;
            $arrParam['refCashAdvanceKey'] = $rsHeader[0]['pkey'];
            $arrParam['refCashAdvanceDetailKey'] = implode(',',array_column($row,'pkey'));
            $arrParam['cashAdvanceCOAKey'] = $rsHeader[0]['cashadvancecoakey'];
            $arrParam['chkIncludeTax'] = $row[0]['ispriceincludetax'];
            $arrParam['taxPercentage'] = $row[0]['taxpercentage'];
            $arrParam['taxValue'] = $taxValue;
            $arrParam['trDesc'] = implode(chr(13),$detailDesc);
            $arrParam['refInvoiceCode'] = $invCode; //implode(', ',$invCode); 
            //$arrParam['top'] = -1; // gk bisa kirim -1, ad Purchase yg mungkin pake inner join dengan TOP atau error ketika ambil hauth temponya
	        $arrParam['chkIsReimburse'] = $row[0]['isreimburse'];
            
            $arrayToJs = $emklPurchaseOrder->addData($arrParam);

            if (!$arrayToJs[0]['valid'])
                throw new Exception('<strong>'.$rsHeader[0]['code'] . '</strong>. '.$this->errorMsg[201].' '.$arrayToJs[0]['message']); 
            else{
                $transKey = $arrayToJs[0]['data']['pkey']; 
               
				$arrayToJs = $emklPurchaseOrder->changeStatus($transKey,TRANSACTION_STATUS['konfirmasi'],'',false,true); 
                $purchaseStatusChanged = true;
                for($z=0;$z<count($arrayToJs);$z++){   
                    if (!$arrayToJs[$z]['valid']) {
                        $purchaseStatusChanged = false;
                        break;
                    }
                }

                if ($purchaseStatusChanged) {
                    // diupdate ketika add PO / DP
                     $sql = ' update '.$this->tableNameDetail.' set 
                            reftranskey = '.$this->oDbCon->paramString($transKey).', 
                            reftabletype = '.$this->oDbCon->paramString($rsPurchaseKey['key']).' 
                         where
                            '.$this->tableNameDetail.'.pkey in ('.$this->oDbCon->paramString(array_column($row,'pkey'),',').')';
					$this->oDbCon->execute($sql);

                }
            }
            
        }
        
    }
    
    
    function addDownpayment($rsHeader,$rsDetail){
        
        $arrDownpayment = array();
        for($i=0;$i<count($rsDetail);$i++){
            $uniqueIndex = $rsDetail[$i]['joborderkey'].'-'.$rsDetail[$i]['supplierkey'].'-'.$this->formatNumber($rsDetail[$i]['taxpercentage']).'-'.$rsDetail[$i]['ispriceincludetax'];
            if(!isset($arrDownpayment[$uniqueIndex]))
                $arrDownpayment[$uniqueIndex] = array();

            array_push($arrDownpayment[$uniqueIndex],$rsDetail[$i]);
        }
        
        $supplierDownpayment = new supplierDownpayment();
        $rsDownpaymentType = $this->getTableKeyAndObj($supplierDownpayment->tableName,array('key')); 
        
        foreach($arrDownpayment as $index=>$row){
            $arrParam = array();
            $total = 0;

			$desc = array();
            for($i=0;$i<count($row);$i++){
				$total += $row[$i]['subtotal'];
				if(!empty($row[$i]['description'])) array_push($desc,$row[$i]['description']);
			}            
            
            $arrParam['code'] = 'xxxxxx'; 
            $arrParam['trDate'] = $this->formatDBDate($rsHeader[0]['trdate'],'d / m / Y');
            $arrParam['selWarehouse'] = $rsHeader[0]['warehousekey'];
            $arrParam['hidSupplierKey'] = $row[0]['supplierkey'];
            $arrParam['islinked'] = 1;
            $arrParam['refCashAdvanceKey'] = $rsHeader[0]['pkey'];
            $arrParam['refCashAdvanceDetailKey'] = implode(',',array_column($row,'pkey'));
            $arrParam['cashAdvanceCOAKey'] = $rsHeader[0]['cashadvancecoakey'];
            $arrParam['amount'] = $total; 
            $arrParam['trDesc'] = implode(chr(13),$desc);     
       
            //$this->setLog($arrParam,true);
            
            $arrayToJs = $supplierDownpayment->addData($arrParam); 

            if (!$arrayToJs[0]['valid'])
                throw new Exception('<strong>'.$rsHeader[0]['code'] . '</strong>. '.$this->errorMsg[201].' '.$arrayToJs[0]['message']); 
            else{
                $transKey = $arrayToJs[0]['data']['pkey'];
                $supplierDownpayment->changeStatus($transKey,TRANSACTION_STATUS['konfirmasi'],'',false,true); 
                 
                $sql = ' update '.$this->tableNameDetail.' set 
                            reftranskey = '.$this->oDbCon->paramString($transKey).', 
                            reftabletype = '.$this->oDbCon->paramString($rsDownpaymentType['key']).' 
                         where
                            '.$this->tableNameDetail.'.pkey in ('.$this->oDbCon->paramString(array_column($row,'pkey'),',').')';
				$this->oDbCon->execute($sql);
            } 
        }
    }
    
	 
    function updateGL($rs,$rsDetail, $arrCashBank){
		
        if (!USE_GL) return;
        
        $warehouse = new Warehouse();
        $generalJournal = new GeneralJournal();
        $cashAdvance = new CashAdvance(); 
        $employee = new Employee(); 
        $coaLink = new COALink();
        $costCashOut = new CostCashOut();
        $chartOfAccount = new ChartOfAccount();
        
		$rsDetailCash = $this->getDetailCashAdvance($rs[0]['pkey']); 
        $rsCashAdvance = $cashAdvance->getDataRowById($rsDetailCash[0]['cashadvancekey']);
        $rsEmployee = $employee->getDataRowById($rsCashAdvance[0]['employeekey']);

        $desc = array();
        if (!empty($rsEmployee))
            array_push($desc, $rsEmployee[0]['name']);
		 
        
        if (!empty($rs[0]['trdesc']))
            array_push($desc, $rs[0]['trdesc']);
        
        $desc = implode(chr(13),$desc);
        $rsKey = $generalJournal->getTableKeyAndObj($this->tableName);
        $warehousekey = $rs[0]['warehousekey'];
        $timestampArr = $this->getDateUsedForTimestamp($this->tableName, $rs);
        
		$arr = array();
		$arr['pkey'] = $generalJournal->getNextKey($generalJournal->tableName);
		$arr['code'] = 'xxxxx';
		$arr['refkey'] = $rs[0]['pkey'];
		$arr['refTableType'] = $rsKey['key'];
		$arr['trDate'] = $this->formatDBDate($timestampArr['timestamp'],'d / m / Y'); 
		$arr['trDesc'] = $desc;
		$arr['createdBy'] = 0; 
		$arr['selWarehouseKey'] = $rs[0]['warehousekey'];
        
        $temp = -1;
        $totalAmount = 0 ;
        $totalTax = 0 ;
        
		for($i=0;$i<count($rsDetail);$i++){
            $totalAmount += $rsDetail[$i]['subtotal']; 
            $totalTax += $rsDetail[$i]['taxvalue']; 
            
            $temp++;
            $arr['hidCOAKey'][$temp] = $rsDetail[$i]['coakey'];
            $arr['debit'][$temp] =  $rsDetail[$i]['beforetaxtotal'] ;  
            $arr['credit'][$temp] = 0;
            $arr['trdescDetail'][$temp] = $rs[0]['trdesc'];	
			 
			if(!empty($arrCashBank)) $arr['refCashBankKey'][$temp] =  ''; 
        }
	
		// cost
		$rsCost = $this->getCostDetail($rs[0]['pkey']);  
		 
		if(!empty($rsCost)){
     		$rsCOAOperationalCost = $coaLink->getCOALink ('operationalcost', $warehouse->tableName, $warehousekey); 
			
			$rsItemCol = $costCashOut->searchDataRow(array($costCashOut->tableName.'.pkey',$costCashOut->tableName.'.coakey'), ' and '.$costCashOut->tableName.'.pkey in ('.$this->oDbCon->paramString(array_column($rsCost,'costkey'),',').')'); 
			$rsItemCol = array_column($rsItemCol,null,'pkey');

			for($i=0;$i<count($rsCost); $i++){   

				 $rsItem = $rsItemCol[$rsCost[$i]['costkey']];
				 $coakey = (!empty($rsItem['coakey'])) ? $rsItem['coakey'] : $rsCOAOperationalCost[0]['coakey']; 

				 $costAmount = $rsCost[$i]['amount'] ;

				 $temp++;
				 $arr['hidCOAKey'][$temp] = $coakey ;
				 $arr['debit'][$temp] = $costAmount; 
				 $arr['credit'][$temp] = 0;  
				 if(!empty($arrCashBank)) 
					$arr['refCashBankKey'][$temp] =  ''; 

				 $totalAmount += $costAmount;
			}        
		}
		
		$temp++; 
		if ($rs[0]['balance'] > 0){ 
			$arr['debit'][$temp] = abs($rs[0]['balance']); 
			$arr['credit'][$temp] = 0; 
			$arr['hidCOAKey'][$temp] = $rs[0]['coakey'];
			if(!empty($arrCashBank))
        		$arr['refCashBankKey'][$temp] =  $arrCashBank['cashToKey']; 
		}else{ 
			$arr['debit'][$temp] = 0;
			$arr['credit'][$temp] = abs($rs[0]['balance']); 
			$arr['hidCOAKey'][$temp] = $rs[0]['coakey'];
			if(!empty($arrCashBank))
        		$arr['refCashBankKey'][$temp] =  $arrCashBank['cashToKey']; 
		}

        $temp++;
        $arr['hidCOAKey'][$temp] = $rs[0]['cashadvancecoakey'];
        $arr['debit'][$temp] = 0;  
        $arr['credit'][$temp] = $totalAmount + $rs[0]['balance']; 
        $arr['trdescDetail'][$temp] = $rs[0]['trdesc'];	
		if(!empty($arrCashBank)) $arr['refCashBankKey'][$temp] =  ''; 

        //totalTax hanya utk yg costing saja
        if($totalTax>0){
            $rsCOA = $coaLink->getCOALink ('taxin', $warehouse->tableName,$warehousekey, 0);
            $temp++;
            $arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];
            $arr['debit'][$temp] = $totalTax;
            $arr['credit'][$temp] = 0; 	
			if(!empty($arrCashBank)) $arr['refCashBankKey'][$temp] =  ''; 
        }
 
        // kalo gk ad transaksi  
        if($generalJournal->isEmptyTrans($arr)) return; 
            
		$arrayToJs = $generalJournal->addData($arr);
        
		if (!$arrayToJs[0]['valid'])
                throw new Exception('<strong>'.$rs[0]['code'] . '</strong>. '.$this->errorMsg[504].' '.$arrayToJs[0]['message']);    
    }


    function confirmTrans($rsHeader){
        $id = $rsHeader[0]['pkey'];
        $rsDetail = $this->getDetailById($id);
        
        // akan diupdate ketika save jg jika status sudah dikonfirmasi
		$this->updateTransDetail($rsHeader,$rsDetail);  

        if( $this->activeModule['appayabletax23'] ){
            $this->updateAPPrepaid($rsHeader,$rsDetail);
        }
	}
        
	function updateTransDetail($rsHeader,$rsDetail){ 
        $arrDetailByType = array();
        for($i=0;$i<count($rsDetail);$i++){
            $uniqueIndex = $rsDetail[$i]['cashtypekey'];
            if(!isset($arrDetailByType[$uniqueIndex])) $arrDetailByType[$uniqueIndex] = array(); 
            array_push($arrDetailByType[$uniqueIndex],$rsDetail[$i]); 
        }
         
        $arrCostRows = array();
        foreach($arrDetailByType as $index=>$row){
            //cashtypenya jo.saat ini ditembak 1
            if($index==1) $this->addPO($rsHeader,$row,$index);
            else if($index==2) $this->addDownpayment($rsHeader,$row);
            else if($index==3) $arrCostRows = $row;
	        else if($index==4) $this->addPO($rsHeader,$row,$index); 
        }
		 
		
		// cancel voucher cashbank yg sudah ada, JIKA balancenya berbeda
		$arrCashBank = array();
		if( $this->activeModule['cashbank'] ){
			$cashBank = new CashBank(); 
			
			$tablekey =  $this->getTableKeyAndObj($this->tableName,array('key'))['key'];
			$currBalance = $rsHeader[0]['balance'];
			$rsCashBank = $cashBank->searchDataRow(array($cashBank->tableName.'.pkey', $cashBank->tableName.'.amount', $cashBank->tableName.'.coakey', $cashBank->tableName.'.trdate'),
												  ' and '.$cashBank->tableName.'.reftabletype = '.$this->oDbCon->paramString($tablekey).' 
												    and '.$cashBank->tableName.'.refkey = '.$this->oDbCon->paramString($rsHeader[0]['pkey']).'
                                                    and '.$cashBank->tableName.'.statuskey <> 4');
			
            // kalo beda total nya atau akun nya berubah
			if($rsCashBank[0]['amount'] != $rsHeader[0]['balance'] || 
               $rsCashBank[0]['coakey'] != $rsHeader[0]['coakey']  || 
               $rsCashBank[0]['trdate'] != $rsHeader[0]['trdate'] 
              ){
               
				//cancel cashbank
				$cashBank->cancelCashBank($rsHeader,$this->tableName);
					 
				// add ulang
        		$rsCashAdvance = $this->getDetailCashAdvance($rsHeader[0]['pkey']);

				$employeeKey = $rsCashAdvance[0]['employeekey'];
				$employeeName = $rsCashAdvance[0]['employeename'];
 
				$rsCashBank = $cashBank->addCashBank($rsHeader,$this->tableName, array('employeekey' => $employeeKey, 'desc' => $this->lang['cashAdvanceRealization'].', ' . $employeeName, 'amount' =>  $rsHeader[0]['balance'])); 
				$arrCashBank['cashToKey'] = $rsCashBank['pkey'];  
			}else{ 
               
				$arrCashBank['cashToKey'] = $rsCashBank[0]['pkey'];  
			}
				
            // akun asal kasbon nya juga perlu buat voucher
            
            
				
		}
		
		// buat voucher cashbank baru
		
		 
        // kalo balance gk nol, atau ad costing
        // harusnya dicek nya kalo gk ad detail GL, karena bisa saja ad biaya tabmahan (kanan bawah) tp balance 0
//        if($rsHeader[0]['balance']<>0 || !empty($arrCostRows)){  
			$this->updateGL($rsHeader,$arrCostRows,$arrCashBank); 
//		}
        
        

	} 



    function updateAPPrepaid($rsHeader,$rsDetail){
            $apPayableTax23 = new APPayableTax23();  
            
            $rate = (isset($rsHeader[0]['rate']) && $rsHeader[0]['rate'] > 0) ? $rsHeader[0]['rate'] : 1;
            
            $tax = new Tax();
        
            $rsTax = $tax->searchDataRow(array( $tax->tableName.'.pkey', $tax->tableName. '.name', $tax->tableName. '.haswithholding' ), 
                                        ' and ' . $tax->tableName.'.typekey = '. $this->oDbCon->paramString(TAX_TYPE['PPH']) .' and '. $tax->tableName .'.statuskey = 1');
            $rsTax = $this->reindexDetailCollections($rsTax, 'pkey');
        
            for ($i=0;$i<count($rsDetail);$i++){ 
                
                if ($rsDetail[$i]['pphamount'] == 0) continue;
                
                $pphTypeKey = 0; // reset ulang
                
                // hanya jika detail pph ada isinya (backcompability)
                if(!empty($rsDetail[$i]['pphtype'])){ 
                    $pphTypeKey = $rsDetail[$i]['pphtype']; 
                    $hasWithholding = $rsTax[$pphTypeKey][0]['haswithholding']; 
                    if($hasWithholding != 1) continue;
                }
                
                    
                $arrParam = array();
                $rsKey =  $this->getTableKeyAndObj($this->tableName);                  
                $arrParam['code'] = 'xxxxxx';
                $arrParam['hidSupplierKey'] = $rsDetail[$i]['supplierkey']; 
                $arrParam['hidRefKey'] = $rsDetail[$i]['pkey']; 
                $arrParam['hidRefHeaderKey'] = $rsHeader[0]['pkey'];
                $arrParam['hidRefCode'] =  $rsHeader[0]['code'];
                $arrParam['hidRefDate'] =  $this->formatDBDate($rsHeader[0]['trdate'],'d / m / Y');  
                $arrParam['hidRefTable'] = $rsKey['key'];
                $arrParam['amount'] = $rsDetail[$i]['pphamount'] * $rate;
                $arrParam['trDesc'] = '';
                $arrParam['trDate'] =  $this->formatDBDate($rsHeader[0]['trdate'],'d / m / Y');  
                $arrParam['dueDate'] =  $this->formatDBDate($rsHeader[0]['trdate'],'d / m / Y');  
                $arrParam['createdBy'] = 0; 
                $arrParam['islinked'] = 1;
                $arrParam['selAPType'] = 1;
                $arrParam['overwriteGL'] = 1;
                $arrParam['selWarehouse'] = $rsHeader[0]['warehousekey'];
                $arrParam['selPPhType'] = $pphTypeKey;
                
                $returnVal = $apPayableTax23->addData($arrParam,false);  

                if (!$returnVal[0]['valid'])
                    throw new Exception('<strong>'.$rsHeader[0]['code'] . '</strong>. '.$this->errorMsg[201].' '.$returnVal[0]['message']);    
                
 
            }  
    }

    function deleteAPPrepaidTax($id){ 
          
        $apPayableTax23 = new APPayableTax23(); 
		
        $rsHeader = $this->getDataRowById($id);

        $rsKey = $this->getTableKeyAndObj($this->tableName, array('key')); 
        $rsAP = $apPayableTax23->searchData('', '', true, ' and refheaderkey = ' . $this->oDbCon->paramString($id) . ' and ' . $apPayableTax23->tableName . '.reftabletype = ' . $rsKey['key'] . ' and ' . $apPayableTax23->tableName . '.statuskey = 1');
    
        for($i=0;$i<count($rsAP);$i++) { 
            $arrayToJs = $apPayableTax23->changeStatus($rsAP[$i]['pkey'],4,'',false, true);
            if (!$arrayToJs[0]['valid'])
                throw new Exception('<strong>'.$rsHeader[0]['code'] . '</strong>. '.  $arrayToJs[0]['message']);    
        }  
          
      }
 
 
    function normalizeParameter($arrParam, $trim=false){
        $cashAdvance = new CashAdvance(); 

        $rsCash = $cashAdvance->searchData('','',true,' and '.$cashAdvance->tableName.'.pkey in ('.$this->oDbCon->paramString($arrParam['hidCashAdvanceKey'],',') . ')');
        $arrParam['cashadvancecoakey'] = $rsCash[0]['cashadvancecoakey'];
		$arrCashCode = array_column($rsCash,'code');
		$arrParam['cashadvancecache'] = implode(', ',$arrCashCode);
        $arrServicekey = $arrParam['hidServiceKey'];
        $arrJobType = $arrParam['selJobType'];
           
		$pkey = 0;
		$rsHeader = array();
		if(isset($arrParam['hidId']) && !empty($arrParam['hidId'])){ 
			$pkey = $arrParam['hidId'];  
			$rsHeader = $this->getDataRowById($pkey); 
		}
		
        // jika transaksi di konfirmasi dan selesai
		if (in_array($rsHeader[0]['statuskey'], array(2,3))){
			unset($this->arrData['code']);
			unset($this->arrData['warehousekey']);
			unset($this->arrData['refkey']);
			unset($this->arrData['cashadvancecoakey']);
//			unset($this->arrData['coakey']); 
			// pas konfirmasi masih boleh revisi, harusnya aman karena akun ini dipake utk catat tampungan setelah closing
			// pindah pas selsesai saja diunset
		 
            //  detail yang sudah ad transaksinya gk boleh dihapus
            // kalo yg jenisnya cost langsung bagaimana ??
            $rsDetail = $this->getDetailByColumn($this->tableNameDetail.'.refkey',$pkey,true,' and reftranskey <> 0');  
            $this->retrieveReadonlyDataRow($arrParam, $rsDetail, $this->arrDataDetail,'reftranskey'); 
        }
		
		if (in_array($rsHeader[0]['statuskey'], array(3))){
			unset($this->arrData['coakey']);
			unset($this->arrData['trdate']);
		}
		
    
        $reCountResult = $this->reCountGrandtotal($arrParam); 
        $arrParam['amount'] = $reCountResult['amount']; 
        $arrParam['total'] = $reCountResult['total'];
        $arrParam['totalCost'] = $reCountResult['totalCost'];
        $arrParam['balance'] = $reCountResult['balance'];
        $arrParam['totalPPH'] = $reCountResult['totalPPH'];
        
        for ($i=0;$i<count($arrJobType);$i++){
            $arrParam['subtotal'][$i] = $reCountResult['detailSubtotal'][$i]['subtotalDetail'];
            $arrParam['taxValueDetail'][$i] = $reCountResult['detailSubtotal'][$i]['taxValueDetail'];
            $arrParam['beforeTaxTotalDetail'][$i] = $reCountResult['detailSubtotal'][$i]['beforeTaxTotalDetail'];
			 
            //$this->arrDataDetail['itemkey'] = array('hidContainerHeaderDetailKey');
            
			if($arrJobType[$i]==1){   
                // JO
                $arrParam['hidCOAKey'][$i]=0; 
                $arrParam['hidJobHeaderKey'][$i]=0; 
                $arrParam['hidContainerHeaderDetailKey'][$i]=0; 
            }else if($arrJobType[$i]==4){   
                // JO Header
                $arrParam['hidCOAKey'][$i]=0; 
                $arrParam['hidJobOrderKey'][$i]=0; 
                $arrParam['hidContainerDetailKey'][$i]= $arrParam['hidContainerHeaderDetailKey'][$i]; // overwrite
                $arrParam['hidContainerHeaderDetailKey'][$i]=0; 
            }else if($arrJobType[$i]==2){
                // DP
				$arrParam['hidServiceKey'][$i]=0;
				$arrParam['hidCOAKey'][$i]=0;
				$arrParam['hidContainerDetailKey'][$i]=0;
				$arrParam['hidJobOrderKey'][$i]=0;
				$arrParam['taxPercentage'][$i]=0;
				$arrParam['chkIncludeTax'][$i]=0;
				$arrParam['refCode'][$i]='';
				$arrParam['hidJobHeaderKey'][$i]=0; 
                $arrParam['hidContainerHeaderDetailKey'][$i]=0; 
			}else if($arrJobType[$i]==3){
			     // COST
				$arrParam['hidServiceKey'][$i]=0;
				$arrParam['hidContainerDetailKey'][$i]=0;
				$arrParam['hidJobOrderKey'][$i]=0;
				$arrParam['hidSupplierKey'][$i]=0;
				$arrParam['refCode'][$i]='';
				$arrParam['hidJobHeaderKey'][$i]=0; 
                $arrParam['hidContainerHeaderDetailKey'][$i]=0; 
			}
        }
        $arrParam = parent::normalizeParameter($arrParam,true); 
        
        return $arrParam; 
    }
    
    function cancelTrans($rsHeader,$copy){ 
		$id = $rsHeader[0]['pkey']; 
        
        $emklPurchaseOrder = new EMKLPurchaseOrder();
        $supplierDownpayment = new SupplierDownpayment();
        $rsPurchase = $emklPurchaseOrder-> searchDataRow( array(  $emklPurchaseOrder->tableName.'.pkey', $emklPurchaseOrder->tableName.'.code'  ) , 
                                '   and '.$emklPurchaseOrder->tableName.'.refcashadvancekey = '.$this->oDbCon->paramString($id).'
                                    and '.$emklPurchaseOrder->tableName.'.statuskey in ('.TRANSACTION_STATUS['menunggu'].')'  
                       );  
        
		for($i=0;$i<count($rsPurchase);$i++) 
          $emklPurchaseOrder->changeStatus($rsPurchase[$i]['pkey'],4,'',false,true); 
         
        $rsDownpayment = $supplierDownpayment-> searchDataRow( array(  $supplierDownpayment->tableName.'.pkey', $supplierDownpayment->tableName.'.code'  ) , 
                                '   and '.$supplierDownpayment->tableName.'.refcashadvancekey = '.$this->oDbCon->paramString($id).'
                                    and '.$supplierDownpayment->tableName.'.statuskey in ('.TRANSACTION_STATUS['menunggu'].')'  
                       );  
        
		for($i=0;$i<count($rsDownpayment);$i++) 
          $supplierDownpayment->changeStatus($rsDownpayment[$i]['pkey'],4,'',false,true); 
        
		if( $this->activeModule['cashbank'] ){
			$cashBank = new CashBank();
			$cashBank->cancelCashBank($rsHeader,$this->tableName);
		}
			
        if( $this->activeModule['appayabletax23'] ){
            $this->deleteAPPrepaidTax($id);
        }
		
        $this->cancelGLByRefkey($rsHeader[0]['pkey'],$this->tableName);
 
		if ($copy)
			$this->copyDataOnCancel($id);	 

	} 
    
    function getDetailWithRelatedInformation($pkey,$criteria=''){
        $sql = 'select
            '.$this->tableNameDetail.'.*,
            ('.$this->tableNameDetail.'.beforetaxtotal / '.$this->tableNameDetail.'.qty) as beforetaxtotalinunit,  
            ('.$this->tableNameDetail.'.taxvalue / '.$this->tableNameDetail.'.qty) as taxvalueinunit,  
            concat(' . $this->tableCOA .'.code, " - " , ' . $this->tableCOA .'.name ) as coaname,
	        '.$this->tableItem.'.name as servicename,
            '.$this->tableContainer.'.name as containername,
            '.$this->tableSupplier.'.name as suppliername,
	        '.$this->tableJobOrder.'.code as jobordercode,
	        '.$this->tableJobOrderHeader.'.code as jobheadercode
          from
            '.$this->tableNameDetail.'
                left join '.$this->tableItem.' on '. $this->tableNameDetail.'.servicekey = '.$this->tableItem.'.pkey 
                left join '.$this->tableSupplier.' on '. $this->tableNameDetail.'.supplierkey = '.$this->tableSupplier.'.pkey 
                left join '.$this->tableContainer.' on '. $this->tableNameDetail.'.itemkey = '.$this->tableContainer.'.pkey 
                left join '.$this->tableJobOrder.' on '. $this->tableNameDetail.'.joborderkey = '.$this->tableJobOrder.'.pkey 
                left join '.$this->tableJobOrderHeader.' on '. $this->tableNameDetail.'.jobheaderkey = '.$this->tableJobOrderHeader.'.pkey 
                left join '.$this->tableCOA.' on '. $this->tableNameDetail.'.coakey = '.$this->tableCOA.'.pkey 
          where   
            '. $this->tableNameDetail.'.refkey in  ('.$this->oDbCon->paramString($pkey,',') . ') ' ;

         $sql .= $criteria;
         return $this->oDbCon->doQuery($sql);

    }
 
    function removeTransactionLink($arrDetailKey, $headerkey = '', $transkey = ''){
        // kalo kirim pkey, gk perlu query ulang
        // $transkey => buat jaga2 kalo copyAndDuplicate, memastikan yg dihapus hanya yg cancel saja, bukan duplicate
        
        if(empty($arrDetailKey)) return;
        
        $sql = 'update '.$this->tableNameDetail.' set reftranskey = 0, reftabletype = 0  where   '.$this->tableNameDetail.'.pkey in ('.$this->oDbCon->paramString($arrDetailKey,',').')'; 
        
        // gk perlu reftabletype, karena sudah unique ad pkey detail
        if(!empty($transkey))
            $sql .= ' and reftranskey = ' . $this->oDbCon->paramString($transkey);
        
        $this->oDbCon->execute($sql);

        if(empty($headerkey)){ 
            $rsHeader = $this->getDetailByColumn($this->tableNameDetail.'.pkey',$arrDetailKey[0]);  
            $headerkey = $rsHeader[0]['pkey'];
        }
        
        $this->reupdateStatus($headerkey);
    }
    
    function reupdateStatus($pkey){
        // cari ad gk detail yg masih kosong reftranskeynya
        
        // kecuali yg tipenya cost
        $arrExceptionType = array(3);
        
        $rsDetail = $this->getDetailByColumn($this->tableNameDetail.'.refkey',$pkey,true,' and reftranskey = 0 and cashtypekey not in ('.$this->oDbCon->paramString($arrExceptionType,',').')');  
        $rsHeader = $this->getDataRowById($pkey);
        
        $status = (!empty($rsDetail)) ? TRANSACTION_STATUS['konfirmasi'] : TRANSACTION_STATUS['selesai'];
        
        if($rsHeader[0]['statuskey'] <> $status)
            $this->changeStatus($rsHeader[0]['pkey'],$status,'',false,true); // jgn by pass parameter validasi, karena perlu cek ulang status realisasi sudah boleh selesai atau blm
        
    }
    
    function updateTransactionLink($arrDetailKey,$transkey){
        $sql = 'update '.$this->tableNameDetail.' set reftranskey = '.$this->oDbCon->paramString($transkey).'  where  '.$this->tableNameDetail.'.pkey in ('.$this->oDbCon->paramString($arrDetailKey,',').')';
        $this->oDbCon->execute($sql);
    }
    
    function getDetailCashAdvance($pkey,$criteria=''){
        
        $sql = 'select
              '.$this->tableNameDetailAdvance.'.*,
              '.$this->tableCashAdvance.'.code as cashadvancecode,
              '.$this->tableEmployee.'.pkey as employeekey,
              '.$this->tableEmployee.'.name as employeename
          from
              '.$this->tableNameDetailAdvance.',
              '.$this->tableCashAdvance.'
			  	left join '.$this->tableEmployee.' on  '.$this->tableCashAdvance.'.employeekey = '.$this->tableEmployee.'.pkey
          where  
              '.$this->tableNameDetailAdvance .'.cashadvancekey = '.$this->tableCashAdvance.'.pkey and
              '. $this->tableNameDetailAdvance.'.refkey in  ('.$this->oDbCon->paramString($pkey,',') . ') ' ;

          $sql .= $criteria;

          return $this->oDbCon->doQuery($sql);

  }
    
    function getJobInformation($arrPkey){
        // untuk laporan buku besar
         
        $sql = 'select distinct
                 '.$this->tableJobOrder.'.pkey as jokey,
                 '.$this->tableJobOrder.'.code as jocode,
                 '.$this->tableName.'.pkey as reftablekey
                from  
                 '.$this->tableJobOrder.',
                 '.$this->tableName.',
                 '.$this->tableNameDetail.'
                where    
                '.$this->tableName.'.pkey = '.$this->tableNameDetail.'.refkey and 
                '.$this->tableNameDetail.'.joborderkey = '.$this->tableJobOrder.'.pkey and    
                '.$this->tableName.'.pkey in ('.$this->oDbCon->paramString($arrPkey,',').') 
                    
              ';
        
        //$this->setLog($sql,true);
         
        $rs = $this->oDbCon->doQuery($sql);
        
        return $rs;
    }


}

?>
