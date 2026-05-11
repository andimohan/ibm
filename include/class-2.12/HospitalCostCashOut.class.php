<?php
  
class HospitalCostCashOut extends BaseClass{ 
   
   function __construct(){
		
		parent::__construct();
       
		$this->tableName = 'hospital_cost_cash_out_header';
		$this->tableNameDetail = 'hospital_cost_cash_out_detail';
		$this->tableSalesWorkOrder = 'hospital_work_order';
		$this->tableSalesOrder = 'hospital_job_order_header';
		$this->tableCar = 'car';
        $this->tableStatus = 'trucking_cost_cash_out_status';
	    $this->tableItem = 'item';
        $this->tableEmployee = 'employee'; 
        $this->tableCustomer = 'customer';
        $this->tableConsignee = 'consignee';
        $this->tableWarehouse = 'warehouse';
        $this->tableCOA = 'chart_of_account';
        $this->tableLocation = 'location';
		$this->tableCategory = 'trucking_service_order_category'; 
        $this->tableCargoType = 'cargo_type'; 
        $this->securityObject = 'HospitalCostCashOut';
        $this->changeTimeStampSecurityObject = 'changeCashOutTimestamp';
        $this->tableFile = 'hospital_cost_cash_out_file';
        $this->uploadFileFolder = 'hospital-cost-cash-out/';
        $this->isTransaction = true;
        $this->allowedStatusForEdit = array(1,2);
        
        $this->arrDataDetail = array(); 
        $this->arrDataDetail['pkey'] = array('hidDetailKey');
        $this->arrDataDetail['refkey'] = array('pkey','ref');
        $this->arrDataDetail['refheadercostkey'] = array('refheadercostkey');
        $this->arrDataDetail['costkey'] = array('hidCostKey');
        $this->arrDataDetail['coakey'] = array('hidCOAKey');
        $this->arrDataDetail['qty'] = array('qty','number');
        $this->arrDataDetail['costvalue'] = array('costValue','number');
        $this->arrDataDetail['amount'] = array('amount','number');
        $this->arrDataDetail['description'] = array('detailDesc');
       
        $this->arrData = array(); 
        $this->arrData['pkey'] = array('pkey', array('dataDetail' => array('dataset' => $this->arrDataDetail)));
        $this->arrData['code'] = array('code');
        $this->arrData['refkey'] = array('hidRefKey');
        $this->arrData['refkey2'] = array('hidRefKey2');
        $this->arrData['refcode'] = array('refCode');
        $this->arrData['refcode2'] = array('refCode2');
        $this->arrData['trdate'] = array('trDate','date');
        $this->arrData['employeekey'] = array('hidEmployeeKey'); 
        $this->arrData['trdesc'] = array('trDesc');
        $this->arrData['subtotal'] = array('subtotal','number');
        $this->arrData['total'] = array('total','number');
        $this->arrData['islinked'] = array('islinked'); 
        $this->arrData['statuskey'] = array('selStatus');   
        $this->arrData['reftabletype'] = array('hidRefTable'); 
        $this->arrData['warehousekey'] = array('selWarehouse'); 
        $this->arrData['recipientbankname'] = array('recipientBankName');
        $this->arrData['recipientbankaccountname'] = array('recipientBankAccountName');
        $this->arrData['recipientbankaccountnumber'] = array('recipientBankAccountNumber');
        $this->arrData['jobdescription'] = array('jobDescription');
        $this->arrData['aremployee'] = array('arEmployee','number');
        $this->arrData['jokey'] = array('hidJOKey');
        $this->arrData['wokey'] = array('hidWOKey');
        $this->arrData['customerkey'] = array('hidCustomerKey');
        $this->arrData['consigneekey'] = array('hidConsigneeKey');
        $this->arrData['timestamptype'] = array('selTimeStampType');
        //$this->arrData['trcashbankdate'] = array('trCashBankDate');
        
         
        //$this->arrLinkedTable = array();
       
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'date','title' => 'JOWODate','dbfield' => 'trdate','default'=>true, 'width' => 100, 'align' =>'center', 'format' => 'date'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'createddate','title' => 'submissionDate','default'=>true,'dbfield' => 'createdon', 'width' => 100, 'align' =>'center', 'format' => 'date'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'refCode','title' => 'refCode','dbfield' => 'refcode','default'=>true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'refCode2','title' => 'refCode','dbfield' => 'refcode2','default'=>true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'recipient','title' => 'recipient','dbfield' => 'employeename','default'=>true, 'width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'amount','title' => 'amount','dbfield' => 'total','default'=>true, 'width' => 100, 'align' =>'right', 'format' => 'integer'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'jobdescription','title' => 'jobDescription','dbfield' => 'jobdescription','default'=>true, 'width' => 200)); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
        array_push($this->arrDataListAvailableColumn, array('code' => 'desc','title' => 'note','dbfield' => 'trdesc',  'width' => 250));
        // array_push($this->arrDataListAvailableColumn, array('code' => 'location','title' => 'location','dbfield' => 'warehousename',  'width' => 100)); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'SI','title' => 'si','dbfield' => 'donumber',  'width' => 130)); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'customer','title' => 'customer','dbfield' => 'customername',  'width' => 150)); 
        // array_push($this->arrDataListAvailableColumn, array('code' => 'consignee','title' => 'consignee','dbfield' => 'consigneename',  'width' => 150)); 
   
        $this->includeClassDependencies(array(
              'Item.class.php',
              'Warehouse.class.php',
              'CashBank.class.php', 
              'ChartOfAccount.class.php',
              'AR.class.php',
              'Service.class.php',
              'COALink.class.php',
              'GeneralJournal.class.php',
              'CashBankRealization.class.php',
              'AREmployee.class.php',
              'ARPayment.class.php',
              'AREmployeePayment.class.php',
              'Customer.class.php',
              'Downpayment.class.php',
              'CustomerDownpayment.class.php',
              'ARPrepaidTax23.class.php',
              'HospitalJobOrder.class.php',
              'HospitalWorkOrder.class.php',
        ));
       
        $this->printMenu = array();
        array_push($this->printMenu,array('code' => 'printTransaction', 'name' => $this->lang['printTransaction'],  'icon' => 'print', 'url' => 'print/hospitalCostCashOut'));
          
        array_push($this->filterCriteria, array('title' => $this->lang['warehouse'], 'field' => 'warehousekey'));
   }
   
 
   function getQuery(){
      
       $sql =  '
			SELECT '.$this->tableName.'.* , 
			   '.$this->tableStatus.'.status as statusname , 
			   '.$this->tableWarehouse.'.name as warehousename , 
               '.$this->tableSalesOrder.'.donumber,
               '.$this->tableSalesOrder.'.customerkey,
               '.$this->tableSalesOrder.'.lastwodate,
               '.$this->tableCustomer.'.name as customername,    
               '.$this->tableConsignee.'.name as consigneename,     
			   '.$this->tableEmployee.'.name as employeename 
			FROM 
                '.$this->tableStatus.',  
                '.$this->tableName.'    
                    left join '.$this->tableSalesOrder.' on '.$this->tableName.'.jokey =  '.$this->tableSalesOrder.'.pkey   
                    left join '.$this->tableCustomer.' on '.$this->tableSalesOrder.'.customerkey = '.$this->tableCustomer.'.pkey
                    left join '.$this->tableConsignee.' on '.$this->tableSalesOrder.'.consigneekey = '.$this->tableConsignee.'.pkey
                    left join '.$this->tableEmployee.' on  '.$this->tableName.'.employeekey = '.$this->tableEmployee.'.pkey,
                '.$this->tableWarehouse.'
			WHERE     
                '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey and 
                '.$this->tableName.'.warehousekey = '.$this->tableWarehouse.'.pkey
 		' .$this->criteria ; 
         
     
       $sql .=  $this->getWarehouseCriteria() ;
       
       //$this->setLog($sql,true);
       
       return $sql;
		 
    }  
	 
    		
     function afterStatusChanged($rsHeader){   
        // retrieve latest status
        /*$rsHeader = $this->getDataRowById($rsHeader[0]['pkey']);
        if ($rsHeader[0]['statuskey'] == 2)
            $this->changeStatus($rsHeader[0]['pkey'],3); */
    }
    
    function afterUpdateData($arrParam, $action){    
        // hanya boleh kalo statusnya blm selesai
        $rsHeader = $this->getDataRowById($arrParam['pkey']); 
        if($rsHeader[0]['statuskey'] < 3)
            $this->updateFile($arrParam['pkey'], $arrParam['token-item-file-uploader'], $arrParam['item-file-uploader']);
    }

	function editData($arrParam){ 
        // biar gk berubah tipenya 
        // kalo kas keluar narik sendiri manual, harusnya gk masalah jg karena gk bisa edit lg setelah narik
        
		unset( $this->arrData['reftabletype']);  
        return parent::editData($arrParam);
	}
            
    function sumArrayColumn($arrDetail,$index, $value){ 
        
        $newArray = array();
        foreach($arrDetail as $row){
            if(!isset($newArray[$row[$index]]))
               $newArray[$row[$index]] = $row[$value];
            else
                  $newArray[$row[$index]] += $row[$value];
                
        }
        
        return $newArray;
     
    }
    
   

    function validateForm($arr,$pkey = ''){ 
		  
		$arrayToJs = parent::validateForm($arr,$pkey); 
		  
		$item = new item();   
		$employee = new Employee();   
		$arrCostKey = $arr['hidCostKey']; 
		$arrAmount = $arr['costValue'];  
		$arrEmployeeAmount = $this->unFormatNumber($arr['arEmployee']);  
		$employeekey = $arr['hidEmployeeKey'];  
		//$driverkey = $arr['hidDriverKey'];  
		//$plannerkey = $arr['hidPlannerKey'];  
         
		//validasi kalo status gk menunggu gk bisa edit 
/*		if (!empty($pkey)){
			$rs = $this->getDataRowById($pkey);  
            
			if ($rs[0]['statuskey'] <> 1) 
				$this->addErrorList($arrayToJs,false,$this->errorMsg[212]); 
		}  */
			 
        $rsEmployee = $employee->getDataRowById($employeekey);
        if($arrEmployeeAmount > $rsEmployee[0]['aroutstanding'])
            $this->addErrorList($arrayToJs,false,$this->errorMsg['ar'][8]);
			 
        if(empty($arr['hidRefKey'])){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['truckingServiceWorkOrder'][1]);
		} 
         
         	 
       /* if(empty($driverkey) && empty($plannerkey)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['recipient'][1]);
		} */
         
        if(empty($arrCostKey)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg[501]); 
		}	
		
            
		for($i=0;$i<count($arrCostKey);$i++) { 
			
			if (empty($arrCostKey[$i]) ){ 
				$this->addErrorList($arrayToJs,false, $this->errorMsg['cost'][1]); 	
			} 
            
			if (!empty($arrCostKey[$i]) && $this->unFormatNumber($arrAmount[$i]) <= 0){
				$rsItem = $item->getDataRowById($arrCostKey[$i]); 
				$this->addErrorList($arrayToJs,false,$rsItem[0]['code'] . ' - ' .$rsItem[0]['name']. '. ' . $this->errorMsg[503]); 
			}
		} 
		
		return $arrayToJs;
	 }
	   
    
    function reCountGrandtotal($arrParam){
 
				$grandtotal = 0;
				$amount = 0;
         
				$arEmployee = $this->unFormatNumber($arrParam['arEmployee']); 
				$arrCostKey = $arrParam['hidCostKey'];
				$arrAmount = $arrParam['amount']; 
				
				$arrARDetail = array();
				$aR = new AR();
				
				for ($i=0;$i<count($arrCostKey);$i++){
					
				    $arrAmount[$i] = $this->unFormatNumber($arrAmount[$i]);
					if ( empty($arrCostKey[$i]) || empty($arrAmount[$i]) )  
						continue;
					
					$amount += $this->unFormatNumber($arrAmount[$i]);
				} 
				
				$grandtotal = $amount; 

				$reCountResult = array();
				$reCountResult['subtotal'] = $grandtotal; 
         
				$reCountResult['total'] = $grandtotal - $arEmployee; 
				
				return $reCountResult;
				
	}
     
    function normalizeParameter($arrParam, $trim=false){ 
        $warehouse = new Warehouse();
        $security = new Security();
        $hospitalWorkOrder = new HospitalWorkOrder();
        $hospitalJobOrder = new HospitalJobOrder();
        
        $arrParam['islinked'] = (isset($arrParam['islinked'])) ? $arrParam['islinked'] : 0;  
        $arrParam['selWarehouse'] = (isset($arrParam['selWarehouse'])) ? $arrParam['selWarehouse'] : $warehouse->getDefaultData(); 
        
        $reCountResult = $this->reCountGrandtotal($arrParam);   
        $arrParam['total'] = $reCountResult['total'];
        
        // kalo konfirmasi cuma boleh upload file
        $rs = $this->getDataRowById($arrParam['pkey']); 
        if (!empty($rs) && $rs[0]['statuskey'] == 2){
            $this->arrData = array(); 
            $this->arrData['pkey'] = array('pkey', 'pkey');
        }
        
        $changeCashOutTimestamp = $security->isAdminLogin($this->changeTimeStampSecurityObject,10);         
        if(!$changeCashOutTimestamp)
           $arrParam['selTimeStampType'] = 0; // reset mengikuti settingan default
        
        $arrTrdesc = array();
        $itemname = array();
        $spktablekey = $this->getTableKeyAndObj($hospitalWorkOrder->tableName,array('key'));
        $spktablekey =  $spktablekey['key'];
      
        if($arrParam['hidRefTable']==$spktablekey){
            $rsWO = $hospitalWorkOrder->searchData($hospitalWorkOrder->tableName.'.pkey',$arrParam['hidRefKey'],true);
            array_push($arrTrdesc, $rsWO[0]['code'].'. '.$rsWO[0]['containername'].'.');
            $joHeaderKey = $arrParam['hidRefKey2']; 
            $arrParam['hidJOKey'] = $arrParam['hidRefKey2']; 
            $arrParam['hidWOKey'] = $arrParam['hidRefKey']; 
        }else{
            $joHeaderKey = $arrParam['hidRefKey']; 
            $arrParam['hidJOKey'] = $arrParam['hidRefKey']; 
        }

            
        $rsJO = $hospitalJobOrder->searchData($hospitalJobOrder->tableName.'.pkey',$joHeaderKey,true);
        $rsJODetail = $hospitalJobOrder->getDetailWithRelatedInformation($joHeaderKey);
        for($i=0;$i<count($rsJODetail);$i++)
            array_push($itemname, $rsJODetail[$i]['qtyinbaseunit'].'x '.$rsJODetail[$i]['itemname']);

        array_push($arrTrdesc, $rsJO[0]['code'].chr(13).implode(chr(13),$itemname)); 
        
        if(!empty($rsJO[0]['stuffinglocationkey']))
            array_push($arrTrdesc,$rsJO[0]['locationname']);
        
        if(!empty($rsJO[0]['routefrom']) && !empty($rsJO[0]['routeto']))
            array_push($arrTrdesc,'Rute :'.$rsJO[0]['routefrom'].'-'.$rsJO[0]['routeto']);
         
        $arrParam['jobDescription'] = implode(chr(13),$arrTrdesc) ;
        
        $arrParam['hidCustomerKey'] = $rsJO[0]['customerkey'];
        $arrParam['hidConsigneeKey'] = $rsJO[0]['consigneekey'];
            
        $arrParam = parent::normalizeParameter($arrParam,true); 
            
        return $arrParam;
    }
       
    function getDetailWithRelatedInformation($pkey,$criteria=''){
        $sql = 'select
	   			'.$this->tableNameDetail .'.*, 
                concat('.$this->tableCOA. '.code,\' - \','.$this->tableCOA. '.name) as coaname, 
                '.$this->tableItem.'.name as costname
			  from
			  	'. $this->tableNameDetail .'
                left join '.$this->tableCOA.' on  '.$this->tableNameDetail.'.coakey = '.$this->tableCOA.'.pkey ,
                '.$this->tableItem.'
			  where
			  	'. $this->tableNameDetail .'.costkey = '.$this->tableItem.'.pkey and
			  	'.$this->tableNameDetail .'.refkey in ('.$this->oDbCon->paramString($pkey,',').')';
        
        $sql .= $criteria; 
         
		return $this->oDbCon->doQuery($sql);
    }
    /*
    function getTransactionType($tableName){ 
         
        $truckingServiceWorkOrder = new TruckingServiceWorkOrder();  
        $truckingServiceOrder = new TruckingServiceOrder();   
        $cashBankRealization = new CashBankRealization();   
        
        $arr = array();
        
        switch ($tableName){ 
  	        case $truckingServiceOrder->tableName : $arr = array('key' => 1,  
                                                                'obj' =>  $truckingServiceOrder,
                                                      );
                                                break; 
  	        case $truckingServiceWorkOrder->tableName : $arr = array('key' => 2,  
                                                                'obj' =>  $truckingServiceWorkOrder,
                                                      );
                                                break; 
  	      
            default : $arr = array('key' => 99,  
                                    'obj' => $cashBankRealization 
                          );
        }
        
        return $arr;
        
    }*/ 
    
    function updateGL($rsHeader,$rsDetail,$arrCashBank){  
        if (!USE_GL) return;
        
        $pkey = $rsHeader[0]['pkey'];
        $warehouse = new Warehouse();
        $employee = new Employee();
        $generalJournal = new GeneralJournal();
        $hospitalJobOrder = new HospitalJobOrder();
        $coaLink = new COALink();
        $cost = new Service(TRUCKING_SERVICE,1);
        $security = new Security();
        
        $warehousekey = $rsHeader[0]['warehousekey']; 
        $rsEmployee = $employee->getDataRowById($rsHeader[0]['employeekey']); 
        $employeeNeedRealization = (!empty($rsEmployee)) ? $rsEmployee[0]['needrealization'] : 1;
             
        $arrDesc = $this->getTransactionDescription($pkey);
        
        if(isset($arrDesc[$pkey])){
            $desc = $arrDesc[$pkey];
        }else{ 
            $desc = array();
            $employeCOAKey = 0;
            if(!empty($rsHeader[0]['employeekey'])){ 
                $employeCOAKey = $rsEmployee[0]['cashbankcoakey'];
                array_push($desc,  $this->lang['recipient'].': '.$rsEmployee[0]['name']).'.';
            }

            if (!empty($rsHeader[0]['jobdescription'])) array_push($desc, $rsHeader[0]['jobdescription']); 
            if (!empty($rsHeader[0]['trdesc'])) array_push($desc, (empty($desc)) ? $rsHeader[0]['trdesc'] : chr(13). $rsHeader[0]['trdesc']); 
            $desc = implode(chr(13),$desc);
        }
        
        
        // detail  desc
        $arrDetailDesc = array();
        if(!empty($rsHeader[0]['refcode'])) array_push($arrDetailDesc,$rsHeader[0]['refcode']);
        if(!empty($rsHeader[0]['refcode2'])) array_push($arrDetailDesc,$rsHeader[0]['refcode2']);
        
        $timestampArr = $this->getDateUsedForTimestamp($this->tableName, $rsHeader);
        
        $changeCashOutTimestamp = $security->isAdminLogin($this->changeTimeStampSecurityObject,10);         
  
        if(!$changeCashOutTimestamp){
            $trDate = $timestampArr['timestamp'];
        }else{
            switch($rsHeader[0]['timestamptype']){ 
                case '1' : $trDate = $rsHeader[0]['trdate'];  break;
                case '2' : $trDate = date('Y-m-d');  break;
                default : $trDate = $timestampArr['timestamp'];  break; 
            }     
        }
            
          
        //$this->setLog( $rsHeader[0]['confirmedon'] ,true);
            
        $rsKey = $this->getTableKeyAndObj($this->tableName,array('key'))['key'];  
		$arr = array();
		$arr['pkey'] = $generalJournal->getNextKey($generalJournal->tableName);
		$arr['code'] = 'xxxxx';
		$arr['refkey'] = $pkey;
		$arr['refTableType'] = $rsKey;
		$arr['trDate'] = $this->formatDBDate($trDate,'d / m / Y'); 
		$arr['trDesc'] = $desc;  
		$arr['selWarehouseKey'] = $rsHeader[0]['warehousekey'];
        
        $temp = -1;
        $totalAmount = 0 ;
        
        // kalo gk pake realisasi, langsung anggap sebagai biaya
        $useRealization = $this->useRealization();
        
        //$rsCOAOperationalCost = $coaLink->getCOALink ('operationalcost', $warehouse->tableName, $warehousekey); 
        $rsCOACashBank = $coaLink->getCOALink ('cashbankdriver', $warehouse->tableName, $warehousekey, 0);  
        
        $rsJO = $hospitalJobOrder->getDataRowById($rsHeader[0]['jokey']);
        
		for($i=0;$i<count($rsDetail);$i++){
            // biaya atau kas gantung
            
            $rsItem = $cost->getDataRowById($rsDetail[$i]['costkey']);  
            
            // kalo pake realisasi dan karyawan perlu realisasi
            if ($useRealization == 1 && $employeeNeedRealization == 1) { 
                $coakey = (!empty($employeCOAKey)) ? $employeCOAKey : $rsCOACashBank[0]['coakey']; 
            }else{  
                //sementara, kalo DP - Logol gk ad DP, jd harusny aman
                if($rsDetail[$i]['costkey'] == 1)
                    $coakey = $coaLink->getCOALink ('supplierdownpayment', $warehouse->tableName, $rsHeader[0]['warehousekey'], 0)[0]['coakey'];  
                else    
                    $coakey = $cost->getCostCOAKeyByJobCategory($rsDetail[$i]['costkey'],$rsJO[0]['categorykey'],$warehousekey) ; // (!empty($rsItem[0]['costcoakey'])) ? $rsItem[0]['costcoakey'] : $rsCOAOperationalCost[0]['coakey']; 
            } 
            
            $detailDesc = implode('. ',$arrDetailDesc);
            if(!empty($rsItem)) $detailDesc .= '. '. $rsItem[0]['name'];
            if(!empty($rsDetail[$i]['description'])) $detailDesc .= '. '. $rsDetail[$i]['description'];
            
            if(!empty($detailDesc)) $detailDesc.='.';
            
            $temp++;
            $arr['hidDetailKey'][$temp] = 0;
            $arr['hidCOAKey'][$temp] = $coakey; 
            $arr['debit'][$temp] = $rsDetail[$i]['amount']; 
            $arr['credit'][$temp] = 0;
            $arr['trdescDetail'][$temp] = $detailDesc;
            $arr['refCashBankKey'][$temp] = '';
            
            // kas keluar
            $temp++;
            $arr['hidDetailKey'][$temp] = 0;
            $arr['hidCOAKey'][$temp] = $rsDetail[$i]['coakey'];
            $arr['debit'][$temp] = 0;  
            $arr['credit'][$temp] = $rsDetail[$i]['amount']; 
            $arr['trdescDetail'][$temp] = $detailDesc;
            $arr['refCashBankKey'][$temp] =  $arrCashBank['cashFromKey'][$rsDetail[$i]['coakey']] ; 
           
                
        }
        
        // kalo ad AR Employee
        $arEmployee = $rsHeader[0]['aremployee'];
        $arNettingNote = $rsHeader[0]['code'].'. '.$this->lang['netting'].'.';
        if (!empty($arEmployee)){

            // kas keluar
            $temp++;
            $arr['hidDetailKey'][$temp] = 0;
            $arr['hidCOAKey'][$temp] = $rsDetail[0]['coakey']; // sementara
            $arr['debit'][$temp] = $arEmployee;  
            $arr['credit'][$temp] = 0; 
            $arr['trdescDetail'][$temp] = $arNettingNote;

            $temp++;
            $arr['hidDetailKey'][$temp] = 0;
            $arr['hidCOAKey'][$temp] = $employee->getARCOAKey( $rsHeader[0]['employeekey'] ,$warehousekey);
            $arr['debit'][$temp] = 0;  
            $arr['credit'][$temp] = $arEmployee; 
            $arr['trdescDetail'][$temp] = $arNettingNote;
        }
         
 
		$arrayToJs = $generalJournal->addData($arr);
        
		if (!$arrayToJs[0]['valid'])
                throw new Exception('<strong>'.$rsHeader[0]['code'] . '</strong>. '.$this->errorMsg[504].' '.$arrayToJs[0]['message']);    
    }    
    
    function confirmTrans($rsHeader){   
        $hospitalWorkOrder = new HospitalWorkOrder();
        $hospitalJobOrder = new HospitalJobOrder();    
        
        $id = $rsHeader[0]['pkey'];
        $rsDetail = $this->getDetailById($id); 
           
        $objKey = $this->getTableNameAndObjById($rsHeader[0]['reftabletype']);
        $tableName = $objKey['tableName']; 
        $updateEmployeeKey = '';
        if ($tableName == $hospitalWorkOrder->tableName){
            $tableName = $hospitalWorkOrder->tableCost;
            $updateEmployeeKey = ', employeekey = '.$rsHeader[0]['employeekey'].' '; 
        }else if ($tableName == $hospitalJobOrder->tableName){ 
            $tableName = $hospitalJobOrder->tableHeaderCost;
            $updateEmployeeKey = ', employeekey = '.$rsHeader[0]['employeekey'].' '; 
        } else{ 
             $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' . $this->errorMsg[201]); // harusnya di validasi confirm
        }
        
        for($i=0;$i<count($rsDetail);$i++){
            
            if ( $rsDetail[$i]['costkey'] == DEFAULT_COST['outsourceDownpayment']) {
               $sql = 'update '.$hospitalWorkOrder->tableName.' set refcashoutdownpaymentkey = ' . $rsDetail[$i]['refkey'] .', downpaymentemployeekey = '.$rsHeader[0]['employeekey'].' where pkey = ' . $rsHeader[0]['refkey']; 
            }else{
               $sql = 'update '.$tableName.' set refcashoutkey = ' . $rsDetail[$i]['refkey'] .' '.$updateEmployeeKey.' where pkey = ' . $rsDetail[$i]['refheadercostkey']; 
            }
              
            $this->oDbCon->execute($sql); 
        }
 

        $this->addAREmployeePayment($rsHeader);

        $arrCashBank = array();
        if(ADV_FINANCE){ 
            $cashBank = new CashBank();
            $employee = new Employee();
            $coaLink = new COALink();
            $warehouse = new Warehouse();
            $cost = new Service(TRUCKING_SERVICE,1);
            
            // kas asal
            $rsEmployee = $employee->getDataRowById($rsHeader[0]['employeekey']);
            $employeCOAKey = $rsEmployee[0]['cashbankcoakey'];
            $rsCOACashBank = $coaLink->getCOALink ('cashbankdriver', $warehouse->tableName, $rsHeader[0]['warehousekey'], 0);  
            $coakey = (!empty($employeCOAKey)) ? $employeCOAKey : $rsCOACashBank[0]['coakey']; 

            
            $arrCoa = $this->sumArrayColumnGroup($rsDetail,'coakey', array('amount'));
            foreach($arrCoa as $key=>$row){ 
                // gk bisa pake detailkey karena digabung
                
                // cari biaya utk setiap coa
                $arrItem = array();
                foreach($rsDetail as $detailRow)
                    if($detailRow['coakey'] == $key)
                        array_push($arrItem, $detailRow['costkey']);
                
                $rsItem = $cost->searchDataRow( array($cost->tableName.'.pkey', $cost->tableName.'.name'),
                                                ' and '.$cost->tableName.'.pkey in ('.$this->oDbCon->paramString( $arrItem ,',').') '                
                ); 
                $rsItem = array_column($rsItem,'name');
                
                // khusus modify tgl voucher dan jurnal
                //$voucherDate = (!empty($rsHeader[0]['trcashbankdate']) && $rsHeader[0]['trcashbankdate'] <> '0000-00-00' ) ? $rsHeader[0]['trcashbankdate'] : '';
                
                $rsCashBank = $cashBank->addCashBank($rsHeader,$this->tableName, array('employeekey' => $rsHeader[0]['employeekey'],'coakey' => $key, 'desc' => implode(', ', $rsItem),  'amount' => -$row['amount'])); 
                $arrCashBank['cashFromKey'][$key] = $rsCashBank['pkey']; 
                
                // kalo ad realiasi, baru ad kas tujuan, jika tdk, langsung jadi biaya
                // ini blm ad voucher kas bank ke kas tujuan nya kalo ad realisasi
            }
            
            
        }

       
        $this->addCashBankRealization($rsHeader,$rsDetail,$tableName);
	   
        //update jurnal umum 
        $this->updateGL($rsHeader,$rsDetail, $arrCashBank);
	} 
    
	function validateClose($rsHeader){   
        
        if(!$this->useRealization()){ 
            // validasi harus di konfirmasi dulu
            if($rsHeader[0]['statuskey'] <> 2)
                $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'.</strong> '.$this->errorMsg[204],true);   
        } else{
            // kalo pake realisasi, gk boleh ubah langsung
            $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'.</strong> '.$this->errorMsg[201],true);       
        }
        
    }
    
    
    function validateBackConfirm($rsHeader){  
        
        if(!$this->useRealization()) return;
        
        $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'.</strong> '.$this->errorMsg[201],true);   
    } 
     
    
	function validateConfirm($rsHeader){
        
		$cost = new Service(TRUCKING_SERVICE,1);
		$hospitalWorkOrder = new HospitalWorkOrder();  
		$hospitalJobOrder = new HospitalJobOrder();   
        $coaLink = new COALink();
        $warehouse = new Warehouse();
        $employee = new Employee();
        $chartOfAccount = new ChartOfAccount();
        
        parent::validateConfirm($rsHeader);
           
        $id = $rsHeader[0]['pkey']; 
        $rsDetail = $this->getDetailById($id);
        
        
        if (USE_GL){
            $arrCOA = array();
            array_push($arrCOA, 'operationalcost','cashbankdriver' ); 
            for ($i=0;$i<count($arrCOA);$i++){
                $rsCOA = $coaLink->getCOALink ($arrCOA[$i], $warehouse->tableName,$rsHeader[0]['warehousekey'], 0); 
                if (empty($rsCOA))	
                    $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '. $this->errorMsg['coa'][3]);
            } 
            
            // validasi akun kas keluar harus diisi
            // gk divalidasi di validate form karena khawatir pas generate kas keluar otomatis gagal kalo gk ad akunnya
            foreach($rsDetail as $detailRow){
                // sementara, karena pernah kejadian coakeynya ada tp sudah dihapus
                 
                $rsCOA = $chartOfAccount->getDataRowById($detailRow['coakey']);
                
                if ( empty($detailRow['coakey']) || empty($rsCOA) ){
                    $rsCost = $cost->getDataRowById($detailRow['costkey']);
                    $this->addErrorLog(false, '<strong>'.$rsCost[0]['name'].'</strong>. '.$this->errorMsg['coa'][1]);
                }
            }
            
         }
        
        
        /*
        gk perlu validasi lg, kalo gk ad, dia ambil cost perational dr gudang
        for($i=0;$i<count($rsDetail);$i++) { 
            $rsCostList = $cost->getDataRowById($rsDetail[$i]['costkey']);
			if ( empty($rsCostList[0]['costcoakey']) || empty($rsCostList[0]['revenuecoakey'])){  
				$this->addErrorLog(false, '<strong>'.$rsCostList[0]['name'].'</strong>. ' .$this->errorMsg['coa'][3]); 	
			}  
		}*/
		  
        if(empty($rsHeader[0]['employeekey'])){ 
			$this->addErrorLog(false,$this->errorMsg['recipient'][1]);
        }else{
          $rsEmployee = $employee->getDataRowById($rsHeader[0]['employeekey']);
          if($rsHeader[0]['aremployee'] > $rsEmployee[0]['aroutstanding'])
			$this->addErrorLog(false,$this->errorMsg['ar'][8]);	
        }
        
        // TESTING: PERLU TESTING ULANG
        // VALIDASI SPK 
         
        //$rsCashOutKey = TABLENAME_SETTINGS[$truckingServiceWorkOrder->tableName]['pkey']; 
        $rsCashOutKey = $this->getTableKeyAndObj($hospitalWorkOrder->tableName,array('key'));  
          
        if ($rsHeader[0]['reftabletype'] == $rsCashOutKey['key']){    
            $rsSPK = $hospitalWorkOrder->getDataRowById($rsHeader[0]['refkey']);
            // SPK sudah selesai boleh diproses Kas Keluar, utk uang susulan
            if ($rsSPK[0]['statuskey'] < 2 || $rsSPK[0]['statuskey'] > 3){ 
                $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' . $this->errorMsg[201].'<br><strong>'.$rsSPK[0]['code'] .'</strong>, '.  $this->errorMsg[204]); 
            }else{
                //cek semua status di SPK, sama gk semua detailnya   
                $rsSPKCost = $hospitalWorkOrder->getCostDetail($rsHeader[0]['refkey']); 
                for($i=0;$i<count($rsDetail);$i++){
                    
                    //khusus DP gk perlu validasi
                    if($rsDetail[$i]['costkey'] == 1) continue;
                    
                    
                    $foundTheCost = false;
                    
                    // perlu cek tambahan, karena pernah ad case ntah knp di SPK detailnya sudah gk ada
                    for($j=0;$j<count($rsSPKCost);$j++){
                        
                        if ($rsDetail[$i]['refheadercostkey'] == $rsSPKCost[$j]['pkey']){ 
                            $foundTheCost = true;
                            
                            if(!empty($rsSPKCost[$j]['refcashoutkey'])) 
                                $this->addErrorLog(false,'<strong>'.$rsSPKCost[$j]['name'].'</strong>. ' . $this->errorMsg['truckingServiceWorkOrder'][3]); 
                            else if ($rsDetail[$i]['costkey'] != $rsSPKCost[$j]['costkey'] || $rsDetail[$i]['amount'] != $rsSPKCost[$j]['total'])  // <-- dulu $rsSPKCost[$j]['requestamount']
                                $this->addErrorLog(false,'<strong>'.$rsSPKCost[$j]['name'].'</strong>. ' . $this->errorMsg[223]);   
                            
                            break;
                        }
                        
                    }
                    
                    // kecuali dr DP
                    if (!$foundTheCost)
                        $this->addErrorLog(false, $this->errorMsg[213]);   
                    
                } 
            } 
        }
        
        // dr JO 
        //$rsCashOutKey = TABLENAME_SETTINGS[$truckingServiceOrder->tableName]['pkey'];
        $rsCashOutKey = $this->getTableKeyAndObj($hospitalJobOrder->tableName,array('key')); 
        
        if ($rsHeader[0]['reftabletype'] == $rsCashOutKey){   
            $rsJobOrder = $hospitalJobOrder->getDataRowById($rsHeader[0]['refkey']); 
            if ($rsJobOrder[0]['statuskey'] < 2 || $rsJobOrder[0]['statuskey'] > 6){ 
                $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' . $this->errorMsg[201].'<br><strong>'.$rsJobOrder[0]['code'] .'</strong>, '.  $this->errorMsg[204]); 
            }else{
                $rsJobOrderCost = $hospitalJobOrder->getHeaderCost($rsHeader[0]['refkey']); 
                for($i=0;$i<count($rsDetail);$i++){
                    for($j=0;$j<count($rsJobOrderCost);$j++){
                        if ($rsDetail[$i]['refheadercostkey'] == $rsJobOrderCost[$j]['pkey']){ 
                            if(!empty($rsJobOrderCost[$j]['refcashoutkey'])) 
                                $this->addErrorLog(false,'<strong>'.$rsJobOrderCost[$j]['itemname'].'</strong>. ' . $this->errorMsg['truckingServiceOrder'][3]); 
                            else if ($rsDetail[$i]['costkey'] != $rsJobOrderCost[$j]['costkey'] || $rsDetail[$i]['amount'] != ($rsJobOrderCost[$j]['qty'] * $rsJobOrderCost[$j]['requestamount']))   
                                $this->addErrorLog(false,'<strong>'.$rsJobOrderCost[$j]['itemname'].'</strong>. ' . $this->errorMsg[223]);  
                        }
                    }
                }

            } 
        }
        
	 }		
     
    function cancelTrans($rsHeader,$copy){   
        
        if ($rsHeader[0]['statuskey'] == 1) return; 
        
        $hospitalWorkOrder = new HospitalWorkOrder(); 
        $hospitalJobOrder = new HospitalJobOrder(); 
        $arEmployeePayment = new AREmployeePayment();
		  	
        $id = $rsHeader[0]['pkey'];
         
        $tableName = '';
        
        $objKey = $this->getTableNameAndObjById($rsHeader[0]['reftabletype']);
        $tableName = $objKey['tableName'];
         
        if ($tableName == $hospitalWorkOrder->tableName)
            $tableName = $hospitalWorkOrder->tableCost;
        else if ($tableName == $hospitalJobOrder->tableName)
            $tableName = $hospitalJobOrder->tableHeaderCost;
        else
             $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' . $this->errorMsg[201]); 
          
        //$this->setLog($tableName);
        $rsDetail = $this->getDetailById($id); 
        for($i=0;$i<count($rsDetail);$i++){
            
             if ( $rsDetail[$i]['costkey'] == DEFAULT_COST['outsourceDownpayment']) {
               $sql = 'update '.$hospitalWorkOrder->tableName.' set refcashoutdownpaymentkey = 0 where pkey = ' . $rsHeader[0]['refkey']; 
            }else{ 
                $sql = 'update '.$tableName.' set refcashoutkey = 0 where pkey = ' . $rsDetail[$i]['refheadercostkey'];
            } 
            
            $this->oDbCon->execute($sql); 
        } 
         
        
        $rsEmployeePayment = $arEmployeePayment->searchData('','',true,' and '.$arEmployeePayment->tableName.'.nettingkey = ' . $this->oDbCon->paramString($id) .' and '.$arEmployeePayment->tableName.'.statuskey in ('.TRANSACTION_STATUS['konfirmasi'].','.TRANSACTION_STATUS['selesai'].') ');
        //$this->setLog($rsEmployeePayment,true);
        for($i=0;$i<count($rsEmployeePayment);$i++) {
            $arrayToJs = $arEmployeePayment->changeStatus($rsEmployeePayment[$i]['pkey'],TRANSACTION_STATUS['batal'],'',false, true);
            if (!$arrayToJs[0]['valid'])
                throw new Exception('<strong>'.$rsHeader[0]['code'] . '</strong>. '.  $arrayToJs[0]['message']);    
        }
		
        /*$cashMovement = new CashMovement();  
		$cashMovement->cancelMovement($id,$this->tableName);*/
        
        $cashBank = new CashBank();
        $cashBank->cancelCashBank($rsHeader,$this->tableName);
        
        $this->cancelCashBankRealization($rsHeader,$tableName);
		 
		if ($copy)
			$this->copyDataOnCancel($id);	
        
        $this->cancelGLByRefkey($rsHeader[0]['pkey'],$this->tableName);
	} 
    
    function cancelCashBankRealization($rsHeader,$tableName){
        $id = $rsHeader[0]['pkey'];  
        
        $cashBankRealization = new CashBankRealization();
        $employee = new Employee();
        $rsEmployee = $employee->getDataRowById($rsHeader[0]['employeekey']);
        
        // khusus akun yg tdk perlu realisasi
        if($rsEmployee[0]['needrealization'] <> 1) { 
            $rsDetail = $this->getDetailById($id); 
            
            // perlu ccek nanti kalo tipe DP bagaimana ??
            
            // update status realisasi
             for($i=0;$i<count($rsDetail);$i++){ 
                
                  if ($rsDetail[$i]['costkey'] == DEFAULT_COST['outsourceDownpayment']) {
                      
                  }else{ 
                      $sql = 'update '.$tableName.' set amount = 0, isrealization = 0 where pkey = ' . $rsDetail[$i]['refheadercostkey']; 
                      $this->oDbCon->execute($sql); 
                  } 
                 
             } 

            return; // harus update nilai dan GL nya jg
        }
        
        
        // update ulang amount dan realisasi
        
		$rsRealization = $cashBankRealization->searchData('','',true,' and '.$cashBankRealization->tableName.'.refkey = '.$this->oDbCon->paramString($id).'  and '.$cashBankRealization->tableName.'.statuskey = 1 ');
		for($i=0;$i<count($rsRealization);$i++) {  
            $arrayToJs = $cashBankRealization->changeStatus($rsRealization[$i]['pkey'],5,'',false,true);
            if (!$arrayToJs[0]['valid'])
                throw new Exception('<strong>'.$rsHeader[0]['code'] . '</strong>. '.  $arrayToJs[0]['message']);    
        }  
    
    }    
     
    function validateCancel($rsHeader,$autoChangeStatus=false){ 
        
        $hospitalWorkOrder = new HospitalWorkOrder();
        $cashBankRealization = new CashBankRealization();
         
        parent:: validateCancel($rsHeader,$autoChangeStatus);
           
        $id = $rsHeader[0]['pkey'];
             
        if(!$this->validateAutoReverseGL($id))
            $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' . $this->errorMsg[201].' ' .$this->errorMsg['generalJournal'][6],true);
       
        
        // KALO SPK SUDAH CLOSING, GK BOLEH CANCEL LG
        if($rsHeader[0]['statuskey'] == 2 || $rsHeader[0]['statuskey'] == 3){  
            
            // UTK JO sepertinya boleh dicancel kapan saja
          
            $rsCashOutKey = $this->getTableKeyAndObj($hospitalWorkOrder->tableName,array('key')); 
            if ($rsHeader[0]['reftabletype'] == $rsCashOutKey['key']){ 
                $rsSPK = $hospitalWorkOrder->getDataRowById($rsHeader[0]['refkey']);
                if ($rsSPK[0]['statuskey'] == 3) 
                    $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' . $this->errorMsg[201].'<br><strong>'.$rsSPK[0]['code'] .'</strong>, '.  $this->errorMsg[204]); 
              
            }
            
            $rsRealization = $cashBankRealization->searchData('','',true,' and '.$cashBankRealization->tableName.'.refkey = ' . $this->oDbCon->paramString($id) .' and '.$cashBankRealization->tableName.'.statuskey in (2,3,4)');
            if(!empty($rsRealization)){
                $this->addErrorLog(false, '<strong>'.$rsHeader[0]['code'].'</strong> ' .$this->errorMsg[201].'<br><strong>'.$rsRealization[0]['code'].'</strong>, ' .$this->errorMsg[203]);
            }
		} 
             
	 }
    
  
    function generateCostReport($criteria='',$order='',$pkey='',$costkey = ''){ 
        
	   $sql =  '
			SELECT '.$this->tableName.'.code,
                   '.$this->tableName.'.refkey, 
                   '.$this->tableName.'.refkey2, 
                   '.$this->tableName.'.refcode, 
                   '.$this->tableName.'.refcode2, 
                   '.$this->tableName.'.trdate, 
                   '.$this->tableName.'.reftabletype,  
                   '.$this->tableName.'.confirmedon, 
                   '.$this->tableNameDetail.'.amount, 
                   '.$this->tableItem.'.name as costname, 
                   '.$this->tableStatus.'.status as statusname , 
                   '.$this->tableWarehouse.'.name as warehousename , 
                   '.$this->tableCOA.'.name as coaname ,
                   '.$this->tableSalesOrder.'.donumber,
                   '.$this->tableSalesOrder.'.customerkey,
                   '.$this->tableSalesOrder.'.lastwodate,  
                   '.$this->tableCustomer.'.name as customername,     
                   '.$this->tableEmployee.'.name as employeename  
			FROM 
                '.$this->tableStatus.',  
                '.$this->tableItem.', 
                '.$this->tableNameDetail.'
                    left join '.$this->tableCOA.' on '.$this->tableNameDetail.'.coakey = '.$this->tableCOA.'.pkey,
                '.$this->tableName.' 
                    left join '.$this->tableSalesOrder.' on '.$this->tableName.'.jokey =  '.$this->tableSalesOrder.'.pkey   
                    left join '.$this->tableCustomer.' on '.$this->tableSalesOrder.'.customerkey = '.$this->tableCustomer.'.pkey 
                    left join '.$this->tableEmployee.' on '.$this->tableName.'.employeekey = '.$this->tableEmployee.'.pkey,
                '.$this->tableWarehouse.'
			WHERE     
                '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey and 
                '.$this->tableNameDetail.'.refkey = '.$this->tableName.'.pkey and 
                '.$this->tableNameDetail.'.costkey = '.$this->tableItem.'.pkey and  
                '.$this->tableName.'.warehousekey = '.$this->tableWarehouse.'.pkey
 		'; 
        
        if (!empty($criteria))  
            $sql .=  ' ' .$criteria; 
        
        if (!empty($pkey))  
            $sql .=  '  and '.$this->tableName.'.pkey = ' .$this->oDbCon->paramString($pkey);
        
        if (!empty($costkey))  
            $sql .=  '  and '.$this->tableNameDetail.'.costkey = ' .$this->oDbCon->paramString($costkey); 
         
        if (!empty($order))  
            $sql .=  ' ' .$order; 
          
       return $this->oDbCon->doQuery($sql);
		 
    } 
    
    function generateCostFlowReport($criteria='',$order='',$pkey=''){
	   // gk bisa join langsung dengan Job Order atau SPK, karean tergantung tabletype

        $rsJOKey =  $this->getTableKeyAndObj($this->tableSalesOrder,array('key')); 
        $joTableTypeKey = $rsJOKey['key'];
        $rsWOKey =  $this->getTableKeyAndObj($this->tableSalesWorkOrder,array('key')); 
        $woTableTypeKey = $rsWOKey['key'];

	   $sql =  '
			SELECT '.$this->tableName.'.code, 
                    CONCAT_WS(\'\','.$this->tableSalesOrder.'.code,jo.code) as jobordercode,
                   '.$this->tableSalesWorkOrder.'.code as workordercode, 
                   '.$this->tableName.'.refkey,  
                   '.$this->tableName.'.trdate,  
                    CONCAT_WS(\'\','.$this->tableSalesOrder.'.trdate, '.$this->tableSalesWorkOrder.'.trdate) as refdate,
                    CONCAT_WS(\'\','.$this->tableCustomer.'.name, wocustomer.name) as customername,
                    CONCAT_WS(\'\','.$this->tableLocation.'.name, wolocation.name) as locationname,
                    CONCAT_WS(\'\','.$this->tableCargoType.'.name, wocargotype.name) as cargotype,
                    CONCAT_WS(\'\','.$this->tableCategory.'.name, wocategory.name) as jobtypename,
                    '.$this->tableSalesWorkOrder.'.containernumber,
                    '.$this->tableSalesWorkOrder.'.container2number, 
                    '.$this->tableCar .'.policenumber as policenumber,
                   '.$this->tableName.'.reftabletype,  
                   '.$this->tableNameDetail.'.amount, 
                   '.$this->tableItem.'.name as costname, 
                   '.$this->tableCOA.'.code as coacode,
                   '.$this->tableCOA.'.name as coaname,
                   concat( '.$this->tableCOA.'.code,\' - \',  '.$this->tableCOA.'.name) as coacodename,
                   concat( 
                        CONCAT_WS(\'\','.$this->tableSalesOrder.'.routefrom,'.$this->tableSalesWorkOrder.'.routefrom),
                        \' - \',
                        CONCAT_WS(\'\','.$this->tableSalesOrder.'.routeto,'.$this->tableSalesWorkOrder.'.routeto)
                    ) as route,  
                   service.name as servicename,
                   '.$this->tableStatus.'.status as statusname , 
                   '.$this->tableWarehouse.'.name as warehousename ,  
                   '.$this->tableEmployee.'.name as employeename  
		    FROM 
                '.$this->tableStatus.',  
                '.$this->tableItem.', 
                '.$this->tableNameDetail.',
                '.$this->tableCOA.',
                '.$this->tableName.'    
                    left join '.$this->tableEmployee.' on '.$this->tableName.'.employeekey = '.$this->tableEmployee.'.pkey
                    left join '.$this->tableSalesWorkOrder.' on '.$this->tableName.'.refkey = '.$this->tableSalesWorkOrder.'.pkey and '.$this->tableName.'.reftabletype = '.$woTableTypeKey.' 
                    left join '.$this->tableSalesOrder.' on '.$this->tableName.'.refkey = '.$this->tableSalesOrder.'.pkey and '.$this->tableName.'.reftabletype = '.$joTableTypeKey.' 
                    left join '.$this->tableSalesOrder.' jo on '.$this->tableSalesWorkOrder.'.refkey = jo.pkey
                    left join '.$this->tableCar .' on '.$this->tableSalesWorkOrder.'.carkey  = '.$this->tableCar .'.pkey
                    left join '.$this->tableItem.' service on '.$this->tableSalesWorkOrder.'.itemkey  = service.pkey
                    left join '.$this->tableLocation.' on  '.$this->tableSalesOrder.'.stuffinglocationkey = '.$this->tableLocation.'.pkey 
                    left join '.$this->tableLocation.' wolocation on  '.$this->tableSalesWorkOrder.'.locationkey = wolocation.pkey 
                    left join '.$this->tableCustomer.' on  '.$this->tableSalesOrder.'.customerkey = '.$this->tableCustomer.'.pkey 
                    left join '.$this->tableCustomer.' wocustomer on  jo.customerkey =  wocustomer.pkey
                    left join '.$this->tableCargoType.' on  '.$this->tableSalesOrder.'.cargotypekey =  '.$this->tableCargoType.'.pkey 
                    left join '.$this->tableCargoType.' wocargotype on  '.$this->tableSalesWorkOrder.'.cargotypekey = wocargotype.pkey 
                    left join '.$this->tableCategory.' on  '.$this->tableSalesOrder.'.categorykey =  '.$this->tableCategory.'.pkey 
                    left join '.$this->tableCategory.' wocategory on  '.$this->tableSalesWorkOrder.'.categorykey = wocategory.pkey , 
                '.$this->tableWarehouse.'
			WHERE     
                '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey and 
                '.$this->tableNameDetail.'.refkey = '.$this->tableName.'.pkey and 
                '.$this->tableNameDetail.'.costkey = '.$this->tableItem.'.pkey and  
                '.$this->tableNameDetail.'.coakey = '.$this->tableCOA.'.pkey and   
                '.$this->tableName.'.warehousekey = '.$this->tableWarehouse.'.pkey
 		'; 
        
        if (!empty($criteria))  
            $sql .=  ' ' .$criteria; 
        
        if (!empty($pkey))  
            $sql .=  '  and '.$this->tableName.'.pkey = ' .$this->oDbCon->paramString($pkey);
         
         
        if (!empty($order))  
            $sql .=  ' ' .$order; 
         
       //$this->setLog($sql);
       return $this->oDbCon->doQuery($sql);
		 
    } 

 
    function searchAvailableReference($searchCriteria=''){
                
        $rsSPK = $this->getTableKeyAndObj($this->tableSalesWorkOrder,array('key')); 
        $spkTableKey = $rsSPK['key'];
        
	    $rsSO = $this->getTableKeyAndObj($this->tableSalesOrder,array('key')); 
        $soTableKey = $rsSO['key'];
        
		$sql = 'select 
                    '.$this->tableAlias. '.*, 
                    '.$this->tableCustomer. '.name as customername 
                from (select
					'.$this->tableAlias. '.pkey,  
					'.$this->tableAlias. '.trdate,  
					'.$this->tableAlias. '.warehousekey, 
					'.$this->tableAlias. '.customerkey,   
                    '.$this->tableAlias. '.code as value,
                    0 as refkey2,
                    '.$soTableKey.' as reftabletype,
                    \'\' as refcode2,
                    '.$this->tableEmployee . '.pkey as employeekey,
                    '.$this->tableEmployee . '.name as employeename 
				from 
					'.$this->tableSalesOrder . ' '. $this->tableAlias.' 
                        left join '.$this->tableEmployee. ' on  '.$this->tableAlias. '.plannerkey = '.$this->tableEmployee. '.pkey 
				where 
					'.$this->tableAlias. '.statuskey in (2,3,4,5,6) '.$searchCriteria.' 
                    '.$this->getWarehouseCriteria($this->tableAlias).'
                UNION ALL
                
                select
					'.$this->tableAlias. '.pkey,  
					'.$this->tableAlias. '.trdate,  
					'.$this->tableAlias. '.warehousekey,  
					'.$this->tableAlias. '.customerkey, 
                    '.$this->tableAlias. '.code as value,
                    '.$this->tableAlias. '.refkey as refkey2,
                    '.$spkTableKey.' as reftabletype,
                    '.$this->tableSalesOrder. '.code as refcode2, 
                    '.$this->tableEmployee . '.pkey as employeekey,
                    '.$this->tableEmployee . '.name as employeename 
				from 
					'.$this->tableSalesWorkOrder . '  '. $this->tableAlias.' 
                        left join  '.$this->tableEmployee . ' on '. $this->tableAlias.'.driverkey = '.$this->tableEmployee . '.pkey,
                    '.$this->tableSalesOrder. ' 
				where  	
                    '.$this->tableAlias. '.refkey = '.$this->tableSalesOrder. '.pkey and 
					'.$this->tableAlias. '.statuskey in (2,3) '.$searchCriteria.'
                    '.$this->getWarehouseCriteria($this->tableAlias).'
                ) '.$this->tableAlias. ' 
                        left join '.$this->tableCustomer.' on  '.$this->tableAlias. '.customerkey = '.$this->tableCustomer.'.pkey
                order by trdate desc
			';
        
		return $this->oDbCon->doQuery($sql);	
	}
    
    function getItemFile($pkey){
		$sql = 'select * from '.$this->tableFile.' where refkey = '.$this->oDbCon->paramString($pkey).' order by pkey asc';	
		return $this->oDbCon->doQuery($sql);
    }
    
    function updateFile($pkey,$token,$arrFile){		
		  
        if(!empty($arrFile)) 
            $this->validateDiskUsage(); 
        
		$sourcePath = $this->uploadTempDoc.$this->uploadFileFolder.$token;
		$destinationPath = $this->defaultDocUploadPath.$this->uploadFileFolder;
		
			
		if(!is_dir($destinationPath)) 
			mkdir ($destinationPath,  0755, true);
			
		$destinationPath .= $pkey;  
		 
		
		//delete previous files	    
		$this->deleteAll($destinationPath);  
		$sql = 'delete from '.$this->tableFile.' where refkey = '. $this->oDbCon->paramString($pkey);
		$this->oDbCon->execute($sql); 
		
		 
		if(!is_dir($sourcePath)) 
			return;
	
		if (!empty($arrFile))	{
			$arrFile = explode(",",$arrFile);
			for ($i=0;$i<count($arrFile);$i++){   
				$this->uploadImage($sourcePath, $destinationPath,$arrFile[$i]);
				
				$imagekey = $this->getNextKey($this->tableFile);  
				
				$sql = 'insert into '.$this->tableFile.' (pkey,refkey,file) values ('.$this->oDbCon->paramString($imagekey).','.$this->oDbCon->paramString($pkey).','.$this->oDbCon->paramString($arrFile[$i]).')';	
				$this->oDbCon->execute($sql);	 
				 
			}		
		} 
					
	}

  
    function addCashBankRealization($rsHeader,$rsDetail,$tableName){
         
        // $tableName utk tau dr JO / SPK
        
        if(!$this->useRealization()) return; 
        if (empty($rsDetail)) return;
        
	    $employee = new Employee();
        $rsEmployee = $employee->getDataRowById($rsHeader[0]['employeekey']);
        
        // khusus akun yg tdk perlu realisasi
        if($rsEmployee[0]['needrealization'] <> 1) { 
            
            // perlu ccek nanti kalo tipe DP bagaimana ??
            
            // update status realisasi
             for($i=0;$i<count($rsDetail);$i++){ 
                  if ($rsDetail[$i]['costkey'] == DEFAULT_COST['outsourceDownpayment']) {

                  }else{ 
                    $sql = 'update '.$tableName.' set amount = requestamount, isrealization = 1 where pkey = ' . $rsDetail[$i]['refheadercostkey'];  
                    $this->oDbCon->execute($sql); 
                  }  
             } 

            return; // harus update nilai dan GL nya jg
        }
        
        $cashBankRealization = new CashBankRealization();
        $hospitalJobOrder = new HospitalJobOrder();
        $hospitalWorkOrder = new HospitalWorkOrder();
        $warehouse = new Warehouse();
        $coaLink = new COALink();
        
        $arrParam = array();	
        $totalCashOut = 0; 
        for($i=0;$i<count($rsDetail);$i++){ 
            $arrParam['hidDetailKey'][$i] = 0;
            $arrParam['hidCostKey'][$i] = $rsDetail[$i]['costkey'];
            $arrParam['amount'][$i] = $rsDetail[$i]['amount'];
            $arrParam['qty'][$i] = $rsDetail[$i]['qty'];
            $arrParam['costValue'][$i] = $rsDetail[$i]['costvalue']; 
            $arrParam['realCostValue'][$i] = $rsDetail[$i]['costvalue']; 
            $arrParam['amount'][$i] = $rsDetail[$i]['amount'];
            $arrParam['refheadercostkey'][$i] = $rsDetail[$i]['refheadercostkey'];
            $arrParam['hidSettlementType'][$i] = 1;
            $arrParam['detailDesc'][$i] = '';
            $totalCashOut = $totalCashOut+$rsDetail[$i]['amount']; 
        }
        
        $rsWorkOrderKey = $hospitalWorkOrder->getTableKeyAndObj($hospitalWorkOrder->tableName,array('key')); 
        
        $refCode3 ='';
        $hidRefKey3 = '';
        if($rsHeader[0]['reftabletype']==$rsWorkOrderKey['key']){
            $refCode3 = $rsHeader[0]['refcode2'];
            $hidRefKey3 = $rsHeader[0]['refkey2'];
        }
        $user = base64_decode($_SESSION[$this->loginAdminSession]['id']);
        $rsTableKey = $this->getTableKeyAndObj($this->tableName,array('key'));
        $arrParam['code'] = 'xxxxxx';
        $arrParam['hidRefKey'] = $rsHeader[0]['pkey'];
        $arrParam['refCode'] = $rsHeader[0]['code'];
        $arrParam['hidRefKey2'] = $rsHeader[0]['refkey'];
        $arrParam['refCode2'] = $rsHeader[0]['refcode'];
        $arrParam['hidRefKey3'] = $hidRefKey3;
        $arrParam['refCode3'] = $refCode3;
        $arrParam['trDate'] = $this->formatDBDate($rsHeader[0]['trdate'],'d / m / Y');
        $arrParam['hidEmployeeKey'] = $rsHeader[0]['employeekey']; 
        $arrParam['hidConsigneeKey'] = $rsHeader[0]['consigneekey']; 
        $arrParam['hidCustomerKey'] = $rsHeader[0]['customerkey'];  // harusnya ini udah gk perlu, karena sudah ad di normalizenya cash bank realization
        $arrParam['trDesc'] = '';
        $arrParam['selWarehouse'] = $rsHeader[0]['warehousekey'];
        $arrParam['islinked'] = 1;
        $arrParam['total'] = $totalCashOut; 
        $arrParam['totalRealization'] = $totalCashOut; 
        
        $arrParam['hidRefTable'] = $rsTableKey['key'];
        $arrParam['hidRefTable2'] = $rsHeader[0]['reftabletype'];
        
        //$this->setLog($arrParam);
        $arrayToJs = $cashBankRealization->addData($arrParam); 

        if (!$arrayToJs[0]['valid'])
            $this->addErrorLog(false, '<strong>'.$rsHeader[0]['code'] . '</strong>. '.$this->errorMsg[201].' '.$arrayToJs[0]['message'], true); 
     
}

/*
 function addCashBank($rsHeader,$rsDetail){
        if(!ADV_FINANCE) return;
        if(empty($rsDetail)) return;
        
        $cashBank = new CashBank(); 
        $warehouse = new Warehouse();
        $coaLink = new COALink();
	    $employee = new Employee(); 
     
        $employeCOAKey = 0;
        $totalCashOut = 0; 
        
        $rsEmployee = $employee->getDataRowById($rsHeader[0]['employeekey']);
        $employeCOAKey = $rsEmployee[0]['cashbankcoakey'];
        $rsCOACashBank = $coaLink->getCOALink ('cashbankdriver', $warehouse->tableName, $rsHeader[0]['warehousekey'], 0); 

        $rsTableKey = $this->getTableKeyAndObj($this->tableName);
        $coakey = (!empty($employeCOAKey)) ? $employeCOAKey : $rsCOACashBank[0]['coakey']; 
        
        $arrCoa = $this->sumArrayColumnGroup($rsDetail,'coakey', array('amount'));
     
        $timestampArr = $this->getDateUsedForTimestamp($this->tableName, $rsHeader);
     
        //$this->setLog($arrCoa,true);
        foreach ($arrCoa as $key => $row) { 
            if($row['amount'] <= 0) continue;
             
            $arrParam = array();
            $arrParam['code'] = 'xxxxxx';
            $arrParam['hidRefKey'] = $rsHeader[0]['pkey'];
            $arrParam['refCode'] = $rsHeader[0]['code'];
            $arrParam['trDate'] = $this->formatDBDate($timestampArr['timestamp'],'d / m / Y'); 
            $arrParam['trDesc'] = '';
            $arrParam['selWarehouseKey'] = $rsHeader[0]['warehousekey']; 
            $arrParam['reftable'] = $rsTableKey['key'];
            $arrParam['selTransactionTypeKey'] = $rsTableKey['key'];
            $arrParam['hidCOAHeaderKey'] = $key;
            $arrParam['amount'] =  -$row['amount'];
            $arrParam['outstanding'] =  -$row['amount'];
            $arrParam['detailKey'] = $row['pkey'];
            $arrParam['overwriteGL'] = 1;
            $arrParam['islinked'] = 1;
            
            // kas asal
            $arrayToJs = $cashBank->addData($arrParam);
             
            if (!$arrayToJs[0]['valid'])
                $this->addErrorLog(false, '<strong>'.$rsHeader[0]['code'] . '</strong>. '.$this->errorMsg[201].' '.$arrayToJs[0]['message'], true); 
            
            $cashBank->changeStatus($arrayToJs[0]['data']['pkey'],2); 
            
             
            $arrParam['amount'] = $this->formatNumber($row['amount']);
            $arrParam['outstanding'] = $this->formatNumber($row['amount']);
            $arrParam['hidCOAHeaderKey'] = $coakey;
      
            // kas tujuan
            $arrayToJs = $cashBank->addData($arrParam);
            if (!$arrayToJs[0]['valid'])
                $this->addErrorLog(false, '<strong>'.$rsHeader[0]['code'] . '</strong>. '.$this->errorMsg[201].' '.$arrayToJs[0]['message'], true); 

            
            $cashBank->changeStatus($arrayToJs[0]['data']['pkey'],2); 

            
        }
        

    }*/
    
    function addAREmployeePayment($rsHeader){
         
        $employeeAR = $rsHeader[0]['aremployee']; 
        if ($employeeAR == 0) return;
        
        $arEmployee = new AREmployee(); 
        $arEmployeePayment = new AREmployeePayment(); 
	    $employee = new Employee(); 
        $arrParam = array();
    
        
        $rsAREmployee = $arEmployee->searchData('','',true,' and '.$arEmployee->tableName.'.customerkey = '.$this->oDbCon->paramString($rsHeader[0]['employeekey']).' and '.$arEmployee->tableName.'.statuskey in (1,2) order by '.$arEmployee->tableName.'.trdate asc');
        
        //$rsTableKey = $arEmployee->getTableKeyAndObj($arEmployee->tableName);
        for($i=0;$i<count($rsAREmployee);$i++){
            if($employeeAR<=0) continue;
             
            $amount = (($employeeAR - $rsAREmployee[$i]['outstanding']) >= 0) ? $rsAREmployee[$i]['outstanding'] : $employeeAR;
 
            $arrParam['hidARKey'][$i] = $rsAREmployee[$i]['pkey'];
            $arrParam['hidDetailKey'][$i] = 0;
            $arrParam['chkPick'][$i] = 1;
            $arrParam['outstanding'][$i] = $amount;
            $arrParam['amount'][$i] = $amount;
            
            $employeeAR -= $amount;
            
        }
        
        $arrParam['code'] = 'xxxxxx'; 
        $arrParam['trDate'] = $this->formatDBDate($rsHeader[0]['trdate'],'d / m / Y');
        $arrParam['hidEmployeeKey'] = $rsHeader[0]['employeekey'];
        $arrParam['nettingkey'] = $rsHeader[0]['pkey'];
        $arrParam['islinked'] = 1;
        $arrParam['overwriteGL'] = 1;
        $arrParam['hidSaveAndProceed'] = 1;
        
        $arrParam['trDesc'] = $rsHeader[0]['code'];
        $arrParam['selWarehouseKey'] = $rsHeader[0]['warehousekey'];
         
        $arrayToJs = $arEmployeePayment->addData($arrParam);
        if (!$arrayToJs[0]['valid'])
            $this->addErrorLog(false, '<strong>'.$rsHeader[0]['code'] . '</strong>. '.$this->errorMsg[201].' '.$arrayToJs[0]['message'], true); 


    }
    
    function getRelatedDataForCashBankReport($pkey){
        $arrReturn = array();
               
        $woTableKey = $this->getTableKeyAndObj($this->tableSalesWorkOrder,array('key'))['key']; 
        $joTableKey = $this->getTableKeyAndObj($this->tableSalesOrder,array('key'))['key'];  
        
        
        $sql = 'select 
                    '. $this->tableName.'.pkey, 
                    '. $this->tableName.'.code as refcode , 
                    '. $this->tableEmployee.'.name as recipient,
                    wo_table.code as wocode, 
                    CONCAT_WS(\'\',wo_jo_table.code, jo_table.code) as jocode , 
                    CONCAT_WS(\'\',wo_location.name, jo_location.name) as location , 
                    CONCAT_WS(\'\',wo_customer.name, jo_customer.name) as customername  , 
                    CONCAT_WS(\'\',wo_consignee.name, jo_consignee.name) as consigneename 
                from 
                    '.$this->tableName.' 
                        left join '. $this->tableEmployee.'  on  '.$this->tableName.'.employeekey = '. $this->tableEmployee.'.pkey
                        
                        left join '.$this->tableSalesWorkOrder.' wo_table on '.$this->tableName.'.refkey = wo_table.pkey and '. $this->tableName.'.reftabletype = '.$this->oDbCon->paramString($woTableKey).' 
                        left join '.$this->tableSalesOrder.' wo_jo_table on '.$this->tableName.'.refkey2 = wo_jo_table.pkey and '. $this->tableName.'.reftabletype = '.$this->oDbCon->paramString($woTableKey).' 
                        left join '.$this->tableLocation.' wo_location on wo_table.locationkey = wo_location.pkey and '. $this->tableName.'.reftabletype = '.$this->oDbCon->paramString($woTableKey).' 
                        left join '.$this->tableCustomer.' wo_customer on wo_jo_table.customerkey = wo_customer.pkey and '. $this->tableName.'.reftabletype = '.$this->oDbCon->paramString($woTableKey).' 
                        left join '.$this->tableConsignee.' wo_consignee on wo_jo_table.consigneekey = wo_consignee.pkey and '. $this->tableName.'.reftabletype = '.$this->oDbCon->paramString($woTableKey).' 
                      
                        left join '.$this->tableSalesOrder.' jo_table on '.$this->tableName.'.refkey  = jo_table.pkey and '. $this->tableName.'.reftabletype = '.$this->oDbCon->paramString($joTableKey).' 
                        left join '.$this->tableLocation.' jo_location on jo_table.stuffinglocationkey  = jo_location.pkey and '. $this->tableName.'.reftabletype = '.$this->oDbCon->paramString($joTableKey).' 
                        left join '.$this->tableCustomer.' jo_customer on jo_table.customerkey  = jo_customer.pkey and '. $this->tableName.'.reftabletype = '.$this->oDbCon->paramString($joTableKey).' 
                        left join '.$this->tableConsignee.' jo_consignee  on jo_table.consigneekey  = jo_consignee.pkey and '. $this->tableName.'.reftabletype = '.$this->oDbCon->paramString($joTableKey).' 
                where 
                    '. $this->tableName.'.pkey in ('.$this->oDbCon->paramString($pkey,',').') ';
        
        //$this->setLog($sql,true);
        
        $rs = $this->oDbCon->doQuery($sql); 
        $rs = array_column($rs, null,'pkey');
           
        return $rs;
    }
    
    function getTransactionDescription($pkey,$userkey= ''){
                  
        if (empty($userkey))
            $userkey = $this->userkey;
        
        $tablekey = $this->getTableKeyAndObj($this->tableName,array('key'))['key'];  
		$rsSettings = $this->getReportSettings($tablekey);  
        
        if(empty($rsSettings)) return;
        
        $reportPattern = $rsSettings[0]['value']; 
        
        // yg boleh diakses
        $arrAvailableField = array( 
                                    array('code' => 'jocode', 'param' => 'JO_CODE', 'field' => $this->tableSalesOrder.'.code'),
                                    array('code' => 'spkcode', 'param' => 'WO_CODE', 'field' => $this->tableSalesWorkOrder.'.code'),
                                    array('code' => 'employeename', 'param' => 'EMPLOYEE_NAME', 'field' => $this->tableEmployee.'.name'), 
                                    array('code' => 'trdesc', 'param' => 'DESCRIPTION', 'field' => $this->tableName.'.trdesc'), 
                                    array('code' => 'customername', 'param' => 'CUSTOMER_NAME', 'field' => $this->tableCustomer.'.name'), 
                                    array('code' => 'location', 'param' => 'LOCATION', 'field' => $this->tableLocation.'.name'),  
                                    array('code' => 'consigneename', 'param' => 'CONSIGNEE_NAME', 'field' => $this->tableConsignee.'.name'),  
                                    array('code' => 'route', 'param' => 'ROUTE', 'field' => 'concat_ws(\'-\','.$this->tableSalesWorkOrder.'.routefrom,'.$this->tableSalesWorkOrder.'.routeto)'), 
                                    array('code' => 'costdetails', 'param' => 'COST_DETAILS', 'dataset' => 'costDetails'),  
        );
        
        $usedParam = array(); 
        foreach($arrAvailableField as $row) 
             if(strpos($reportPattern, '{{'.$row['param'].'}}') !== false) 
                 array_push($usedParam,$row); 
        
        $arrDataSet = array();
        $queryselect = array();
        foreach($usedParam as $row) { 
            if (isset($row['field']))
                array_push($queryselect, $row['field'] .' as '. $row['code']); 
            else
                array_push($arrDataSet, $row['code']); 
        }
         
        if (empty($queryselect))  return array();
       

        $sql = 'select
                    '.$this->tableName.'.pkey,
                    '.implode(',',$queryselect).'
                from
                    '.$this->tableName.'
                        left join '.$this->tableSalesOrder.' on '.$this->tableName.'.jokey = '.$this->tableSalesOrder.'.pkey 
                        left join '.$this->tableSalesWorkOrder.' on '.$this->tableName.'.wokey = '.$this->tableSalesWorkOrder.'.pkey 
                        left join '.$this->tableCustomer.'  on '.$this->tableSalesOrder.'.customerkey = '.$this->tableCustomer.'.pkey
                        left join '. $this->tableEmployee.' on '.$this->tableName.'.employeekey = '. $this->tableEmployee.'.pkey
                        left join '. $this->tableLocation.' on '.$this->tableSalesOrder.'.stuffinglocationkey = '. $this->tableLocation.'.pkey
                        left join '. $this->tableConsignee.' on '.$this->tableSalesOrder.'.consigneekey = '. $this->tableConsignee.'.pkey
                where '.$this->tableName.'.pkey in ('.$this->oDbCon->paramString($pkey,',').')
                ';
        
        $rs = $this->oDbCon->doQuery($sql);
        
        $totalRs =  count($rs);
        
        // utk cost detail
        if(!empty($arrDataSet)){ 
                if(in_array('costdetails',$arrDataSet)){ 
                    $rsCostDetail = $this->getDetailWithRelatedInformation($pkey);
                    $rsCostDetail = $this->reindexDetailCollections($rsCostDetail,'refkey');
                     
                    $costDetails = array();
                    foreach($rsCostDetail as $key=>$costRow){ 
                        if (!isset($costDetails[$key])) $costDetails[$key] = array(); 
                        
                        foreach($costRow as $itemCost) 
                            array_push($costDetails[$key],$this->formatNumber($itemCost['qty']).'x '. $itemCost['costname']. ' = ' .$this->formatNumber($itemCost['amount'])); 
                    }
                    
                    for($i=0;$i<$totalRs;$i++)
                        $rs[$i]['costdetails'] = implode('<br>',$costDetails[$rs[$i]['pkey']]);
                    
                }
        }
         
        
        
        $returnArr = array();
        
        for($i=0;$i<$totalRs;$i++){
            
            $arrNeedle = array(PHP_EOL); 
            $arrReplacement = array(chr(13));

            foreach($usedParam as $row){  
                if(!isset($rs[$i][$row['code']])) continue;
                
                array_push ($arrNeedle,'{{'.$row['param'].'}}');
                array_push ($arrReplacement,$rs[$i][$row['code']]); 
            }

            $returnArr[$rs[$i]['pkey']] = str_replace($arrNeedle,$arrReplacement,$reportPattern); 
           
        }
        
        return $returnArr;
        
    }
    
}
?>
