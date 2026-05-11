<?php
  
class TruckingCostCashIn extends BaseClass{ 
   
   function __construct(){
		
		parent::__construct();
       
		$this->tableName = 'trucking_cost_cash_in_header';
		$this->tableNameDetail = 'trucking_cost_cash_in_detail';
		$this->tableSalesWorkOrder = 'trucking_service_work_order';
		$this->tableSalesOrder = 'trucking_service_order_header';
		$this->tableCar = 'car';
        $this->tableStatus = 'transaction_status';
	    $this->tableItem = 'item';
        $this->tableEmployee = 'employee';
        $this->tableCOA = 'chart_of_account';
        $this->securityObject = 'truckingCostCashOut';
        
        $this->arrDataDetail = array(); 
        $this->arrDataDetail['pkey'] = array('hidDetailKey');
        $this->arrDataDetail['refkey'] = array('pkey','ref');
        $this->arrDataDetail['refheadercostkey'] = array('refheadercostkey');
        $this->arrDataDetail['costkey'] = array('hidCostKey');
        $this->arrDataDetail['coakey'] = array('hidCOAKey');
        $this->arrDataDetail['price'] = array('amount','number');
        $this->arrDataDetail['description'] = array('detailDesc');
       
        $this->arrData = array(); 
        $this->arrData['pkey'] = array('pkey', array('dataset' => $this->arrDataDetail, 'tablename' => $this->tableNameDetail, 'reffield' => 'refkey') );
        $this->arrData['code'] = array('code');
        $this->arrData['refkey'] = array('hidWorkOrderKey');
        $this->arrData['trdate'] = array('trDate','date');
        $this->arrData['driverkey'] = array('hidDriverKey');
        $this->arrData['plannerkey'] = array('hidPlannerKey');
        $this->arrData['trdesc'] = array('trDesc');
        $this->arrData['total'] = array('total','number');
        $this->arrData['islinked'] = array('islinked'); 
        $this->arrData['statuskey'] = array('selStatus');   
        $this->arrData['reftabletype'] = array('hidRefTable');
       
        $this->tableNeedToBeCopyOnCancel = array($this->tableNameDetail);
         
        //$this->arrLinkedTable = array();  
       
   
   }
   
 
   function getQuery(){
	   
	   $sql =  '
			SELECT '.$this->tableName.'.* , 
			   '.$this->tableStatus.'.status as statusname ,
               '.$this->tableSalesWorkOrder.'.code as refcode,
               '.$this->tableSalesOrder.'.code as socode,
               '.$this->tableCar.'.policenumber,
			   '.$this->tableEmployee.'.name as drivername,
               tablePlanner.name as plannername
			FROM 
                '.$this->tableStatus.',
                '.$this->tableSalesWorkOrder.'
                    left join '.$this->tableCar.' on  '.$this->tableSalesWorkOrder.'.carkey = '.$this->tableCar.'.pkey ,
                '.$this->tableSalesOrder.',
                '.$this->tableName.'    
                    left join '.$this->tableEmployee.' on  '.$this->tableName.'.driverkey = '.$this->tableEmployee.'.pkey 
                    left join '.$this->tableEmployee.' as tablePlanner on  '.$this->tableName.'.plannerkey = tablePlanner.pkey 
			WHERE    
                '.$this->tableName.'.refkey = '.$this->tableSalesWorkOrder.'.pkey and
                '.$this->tableSalesWorkOrder.'.refkey = '.$this->tableSalesOrder.'.pkey and
                '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey 
 		' .$this->criteria ; 
         
       return $sql;
		 
    }  
	
      function addData($arrParam){ 
        $arrParam['selStatus'] = 1;
        return parent::addData($arrParam);
	}
	
	function editData($arrParam){
		unset( $this->arrData['statuskey']); 
		unset( $this->arrData['reftabletype']);  
        return parent::editData($arrParam);
	}
        
     function validateForm($arr,$pkey = ''){ 
		  
		$arrayToJs = parent::validateForm($arr,$pkey); 
		  
		$item = new item();   
		$arrCostKey = $arr['hidCostKey']; 
		$arrAmount = $arr['amount'];  
         
		//validasi kalo status gk menunggu gk bisa edit 
		if (!empty($pkey)){
			$rs = $this->getDataRowById($pkey);  
            
			if ($rs[0]['statuskey'] <> 1) 
				$this->addErrorList($arrayToJs,false,$this->errorMsg[212]); 
		}  
			 
         if(empty($arr['hidWorkOrderKey'])){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['truckingServiceWorkOrder'][1]);
		} 
         
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
	  
   function changeStatus($id,$status,$reason='',$copy=false, $autoChangeStatus = false, $ignoreValidation = false){
		   
		   
		$arrayToJs = array();
		
		try{
			 	 
			switch ($status){
				case 1 :  $arrayToJs = $this->validateInput($id);
						 if (!empty($arrayToJs)) 
								return $arrayToJs; 
						  break;
				case 2 : $arrayToJs = $this->validateConfirm($id);
						 if (!empty($arrayToJs)) 
								return $arrayToJs;
						break;
				case 3 : $arrayToJs = $this->validateClose($id);
						 if (!empty($arrayToJs)) 
								return $arrayToJs; 
						  break; 
				case 4 : $arrayToJs = $this->validateCancel($id, $autoChangeStatus);
						 if (!empty($arrayToJs)) 
								return $arrayToJs;
						 break;  
			}
		 
		 
		 
			if(!$this->oDbCon->startTrans())
				throw new Exception($this->errorMsg[100]);
					 
			switch ($status){
              case 2 : $this->confirmTrans($id); 
                         $this->afterConfirmTrans($id);
                         break; 
				case 4 :  $this->cancelTrans($id,$copy);
                          $this->afterCancelTrans($id);
                          break;  
			}
		 
		 
			$sql = 'update '.$this->tableName.' set statuskey = '.$this->oDbCon->paramString($status).' where pkey = ' . $this->oDbCon->paramString($id);
			$this->oDbCon->execute($sql);
			
            $rsStatus = $this->getStatusById ($status); 
            $this->setTransactionLog($rsStatus[0]['pkey'],$id);
            
			$this->oDbCon->endTrans();
			$this->addErrorList($arrayToJs,true,$this->lang['dataHasBeenSuccessfullyUpdated']); 
		
	    } catch(Exception $e){
			$this->oDbCon->rollback();
			$this->addErrorList($arrayToJs,false,$e->getMessage()); 
		}		
				 
 		return $arrayToJs; 
 	}
	  
    function confirmTrans($id){  
         
        
	} 
    

	function validateConfirm($id){
        
		$cost = new Service(TRUCKING_SERVICE,1);
		$truckingServiceWorkOrder = new TruckingServiceWorkOrder();
        
        $arrayToJs = array();
        
		$rs = $this->getDataRowById($id);
        $rsDetail = $this->getDetailById($id);
        
        for($i=0;$i<count($rsDetail);$i++) { 
            $rsCostList = $cost->getDataRowById($rsDetail[$i]['costkey']);
			if ( empty($rsCostList[0]['costcoakey']) || empty($rsCostList[0]['revenuecoakey'])){ 
				$this->addErrorList($arrayToJs,false, $this->errorMsg['coa'][3]); 	
			}  
		}
		
		if($rs[0]['statuskey'] <> 1){  
			$this->addErrorList($arrayToJs,false,'<strong>'.$rs[0]['code'].'</strong>. ' . $this->errorMsg[203]);
		} 
		 
	 	return $arrayToJs; 
	 }		
     
     
    function updateGL($rs){
          
    }
     
    function cancelTrans($id,$copy){  
        
        $truckingServiceWorkOrder = new TruckingServiceWorkOrder();
		$rsHeader = $this->getDataRowById($id);
		  	
		if ($rsHeader[0]['statuskey'] == 1)
			return;
	 
        
		$cashMovement = new CashMovement();  
		$cashMovement->cancelMovement($id,$this->tableName);
		 
		if ($copy)
			$this->copyDataOnCancel($id);	
        
        $this->cancelGLByRefkey($rsHeader[0]['pkey'],$this->tableName);
	} 
    
    function reCountGrandtotal($arrParam){

				$grandtotal = 0;
				$amount = 0;
				
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
				$reCountResult['total'] = $grandtotal; 
				
				return $reCountResult;
				
	}
    
    
    
    
     
    function normalizeParameter($arrParam, $trim=false){
        $arrParam['islinked'] = (isset($arrParam['islinked'])) ? $arrParam['islinked'] : 0; 
        $arrParam['trDesc'] = (isset($arrParam['trDesc'])) ? $arrParam['trDesc'] : ''; 
        $arrParam['hidDriverKey'] = (isset($arrParam['hidDriverKey'])) ? $arrParam['hidDriverKey'] : '';
        $arrParam['hidPlannerKey'] = (isset($arrParam['hidPlannerKey'])) ? $arrParam['hidPlannerKey'] : ''; 
        //$arrParam['detailDesc'] = (isset($arrParam['detailDesc'])) ? $arrParam['detailDesc'] : array(); 
        
        $reCountResult = $this->reCountGrandtotal($arrParam);   
        $arrParam['total'] = $reCountResult['total'];
            
        return $arrParam;
    }
    
     function validateCancel($pkey,$autoChangeStatus=false){ 
         
		$rs = $this->getDataRowById($pkey);
		  
		$arrayToJs = array();
		
		if($rs[0]['statuskey'] == 4){  
			$this->addErrorList($arrayToJs,false,'<strong>'.$rs[0]['code'].'</strong>. ' . $this->errorMsg[201]);
            return $arrayToJs;
		} 
           
        if ( !$autoChangeStatus ) {
            if(isset($rs[0]['islinked']) && !empty($rs[0]['islinked'])){  
                $this->addErrorList($arrayToJs,false,'<strong>'.$rs[0]['code'].'</strong>. ' . $this->errorMsg[900]);  
                return $arrayToJs;  
            }
        }  
         
        // KALO SPK SUDAH CLOSING, GK BOLEH CANCEL LG
         
         
          
       
	 	return $arrayToJs;
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
			  	' . $this->tableNameDetail .'.costkey = '.$this->tableItem.'.pkey and
			  	'.$this->tableNameDetail .'.refkey = '.$this->oDbCon->paramString($pkey);
        
        $sql .= $criteria;
        //$this->setLog($sql);
		return $this->oDbCon->doQuery($sql);
    }
    
    function getTransactionType($tableName){ 
         
        $truckingServiceWorkOrder = new TruckingServiceWorkOrder();  
        $cashBankRealization = new CashBankRealization();  
          
        $arr = array();
        
        switch ($tableName){ 
  	        case $cashBankRealization->tableName : $arr = array('key' => 2,  
                                                       'obj' => $cashBankRealization 
                                                      );
                                                break; 
  	      
            default : $arr = array('key' => 1,  
                           'obj' => $truckingServiceWorkOrder 
                          );
        }
        
        return $arr;
        
    }
    
}
?>