<?php

class LogisticSalesOrderManifest extends BaseClass{
	
    function __construct(){

        parent::__construct();

        $this->tableName = 'logistic_sales_order_manifest_header';
        $this->tableNameDetail = 'logistic_sales_order_manifest_detail';
        $this->tableCity = 'city'; 
        $this->tableTransportation = 'transportation'; 
        $this->tableStatus = 'transaction_status';
        $this->tableWarehouse = 'warehouse';   
        
        $this->tableSalesOrder = 'logistic_sales_order_header';  
        
        $this->isTransaction = true;
        $this->newLoad = true;

        $this->securityObject = 'LogisticSalesOrderManifest'; 
//        $this->autoPrintURL = 'print/logisticSalesOrderManifest';


        $this->arrDataDetail = array(); 
        $this->arrDataDetail['pkey'] = array('hidDetailKey');
        $this->arrDataDetail['refkey'] = array('pkey','ref'); 
        $this->arrDataDetail['sokey'] = array('hidLogisticKey' ,array('mandatory'=>true));
        $this->arrDataDetail['amount'] = array('logisticTotal','number');


        $arrDetails = array(); 
        array_push($arrDetails, array('dataset' => $this->arrDataDetail, 'tableName' => $this->tableNameDetail));

        $this->arrData = array(); 
        $this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails));  
        $this->arrData['code'] = array('code');
        $this->arrData['trdate'] = array('trDate','date');
        $this->arrData['transportationkey'] = array('selTransportation');
        $this->arrData['recipientcitykey'] = array('hidCityKey');
        $this->arrData['warehousekey'] = array('selWarehouseKey');
        $this->arrData['trdesc'] = array('trDesc');
        $this->arrData['statuskey'] = array('selStatus');
        $this->arrData['grandtotal'] = array('grandTotal','number');   
        $this->arrData['tablekey'] = array('hidTableType');
        $this->arrData['usedateperiod'] = array('chkDatePeriod');
        $this->arrData['startdateperiod'] = array('trStartDate','date');
        $this->arrData['enddateperiod'] = array('trEndDate','date');
 
        array_push($this->filterCriteria, array('title' => $this->lang['warehouse'], 'field' => 'warehousekey'));
        
	 
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'date','title' => 'date','dbfield' => 'trdate','default'=>true, 'width' => 100, 'align' =>'center', 'format' => 'date'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'recipientCity','title' => 'recipientCity','dbfield' => 'recipientcityname','default'=>true,'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'transportation','title' => 'transportation','dbfield' => 'transportationname','default'=>true,'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'total','title' => 'total','dbfield' => 'grandtotal','default'=>true ,'width' => 100, 'align' => 'right', 'format' => 'integer'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
        array_push($this->arrDataListAvailableColumn, array('code' => 'warehouse','title' => 'warehouse','dbfield' => 'warehousename', 'width' => 100));
    
        $this->printMenu = array();  
        array_push($this->printMenu,array('code' => 'printTransaction', 'name' => $this->lang['printTransaction'],  'icon' => 'print', 'url' => 'print/logisticSalesOrderManifest'));
   

        $this->arrSearchColumn = array();
        array_push($this->arrSearchColumn, array('Kode', $this->tableName . '.code'));
        array_push($this->arrSearchColumn, array('Tanggal', $this->tableName . '.trdate'));
        array_push($this->arrSearchColumn, array('Tujuan', $this->tableCity . '.name')); 
        array_push($this->arrSearchColumn, array('Transportasi', $this->tableTransportation . '.name')); 
          
		$this->includeClassDependencies(array(
            'LogisticSalesOrder.class.php',
            'City.class.php',
        ));  
            
    }

    function getQuery(){

        $sql = '
            SELECT
                '.$this->tableName.'.* ,   
			    '.$this->tableWarehouse.'.name as warehousename,
                '.$this->tableCity.'.name as recipientcityname, 
                '.$this->tableTransportation.'.name as transportationname, 
                '.$this->tableStatus.'.status as statusname
            FROM '.$this->tableStatus.',
                 '.$this->tableCity.',
                 '.$this->tableTransportation.',
                 '.$this->tableName.',
                 '.$this->tableWarehouse.'  
            WHERE   
                  '.$this->tableName.'.recipientcitykey = '.$this->tableCity.'.pkey and
                  '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey and 
                  '.$this->tableName.'.transportationkey = '.$this->tableTransportation.'.pkey and 
                  '.$this->tableName.'.warehousekey = '.$this->tableWarehouse.'.pkey
            ' .$this->criteria ;
            
        $sql .=  $this->getWarehouseCriteria() ;
          
        return $sql;
    }

 
    function reCountGrandtotal($arrParam){

        $transactionObj = $this->getTransactionObj();
        
        $grandTotal = 0;
        $amount = 0; 

        $arrInvoicekey = $arrParam['hidLogisticKey']; 
        $arrAmount = $arrParam['logisticTotal']; 
        $arrInvoiceTotal = array();
        
        for ($i=0;$i<count($arrInvoicekey);$i++){

            $arrAmount[$i] = $this->unFormatNumber($arrAmount[$i]);
            if (empty($arrInvoicekey[$i]) || empty($arrAmount[$i]))   
                continue; 

            $rsSI = $transactionObj->getDataRowById($arrInvoicekey[$i]);
            $arrInvoiceTotal[$i] = $rsSI[0]['grandtotal'];
            
            $amount += $this->unFormatNumber($arrInvoiceTotal[$i]); 
        }  

        $grandTotal = $amount  ;

        $reCountResult = array(); 
        $reCountResult['grandTotal'] = $grandTotal; 
        $reCountResult['logisticTotal'] = $arrInvoiceTotal; 

        return $reCountResult;
				
	}

    function validateForm($arr,$pkey = ''){ 

        $transactionObj = $this->getTransactionObj();
        
        $arrayToJs = parent::validateForm($arr,$pkey); 
 
        $recipientcitykey = $arr['hidCityKey']; 
        $arrSOKey = $arr['hidLogisticKey']; 
        $arrPick = $arr['chkPick'];  
 
        $arrDetailKey = array();
        
        if(empty($recipientcitykey)) 
            $this->addErrorList($arrayToJs,false,$this->errorMsg['city'][1]);  
  
        $hasSOI = false; 
        // cek ad duplikasi gk, dan cek customernya sesuai gk
        for($i=0;$i<count($arrSOKey);$i++) {   

            if ( (!empty($arrSOKey[$i]) ) && !empty($arrPick[$i]) )  {
                $hasSOI = true;   
                
                $rsSOI = $transactionObj->getDataRowById($arrSOKey[$i]);

                if (in_array($arrSOKey[$i],$arrDetailKey)){  
                    $this->addErrorList($arrayToJs,false, $rsSOI[0]['code'].'. '.$this->errorMsg[215]); 	 
                }else{ 
                    if (!empty($arrSOKey[$i]))  
                        array_push($arrDetailKey, $arrSOKey[$i]);
                }

				if ($rsSOI[0]['statuskey'] <> TRANSACTION_STATUS['konfirmasi'])
                    $this->addErrorList($arrayToJs,false, $rsSOI[0]['code'].'. '.$this->errorMsg[204]); 
            }
  
             
        } 
 
        return $arrayToJs;
    }
    
    

    function getDetailWithRelatedInformation($pkey,$criteria=''){
        $sql = 'select
            '.$this->tableNameDetail.'.*,  
            '.$this->tableSalesOrder.'.code as socode,  
            '.$this->tableSalesOrder.'.trdate as sodate,
            '.$this->tableSalesOrder.'.termofpaymentkey
          from
            '.$this->tableNameDetail.',
            '.$this->tableSalesOrder.' 
          where  
            '. $this->tableNameDetail.'.refkey  = '.$this->oDbCon->paramString($pkey) . ' and
            '. $this->tableNameDetail.'.sokey = '.$this->tableSalesOrder.'.pkey ' ;

        $sql .= $criteria;
 
        return $this->oDbCon->doQuery($sql);

    } 
   
    function normalizeParameter($arrParam, $trim=false){
        
        
        $detail = $arrParam['hidDetailKey'];  
           
        // remove uncheck 
        $this->removeUnCheckRows($arrParam,$this->arrDataDetail);
        
        $arrParam['trStartDate'] = (!empty($arrParam['trStartDate'])) ? $arrParam['trStartDate'] : DEFAULT_EMPTY_DATE;  
        $arrParam['trEndDate'] = (!empty($arrParam['trEndDate'])) ? $arrParam['trEndDate'] : DEFAULT_EMPTY_DATE;        
        
        $reCountResult = $this->reCountGrandtotal($arrParam); 
        $arrParam['grandTotal'] = $reCountResult['grandTotal']; 
        
        for ($i=0;$i<count($reCountResult['invoiceTotal']);$i++){ 
            $arrParam['logisticTotal'][$i] =  $reCountResult['invoiceTotal'][$i];
        }
 
        $arrParam = parent::normalizeParameter($arrParam,true); 
         
        return $arrParam;
    } 
  
    function validateConfirm($rsHeader){  
        $id = $rsHeader[0]['pkey'];
         
        $rsDetail = $this->getDetailById($id); 
        $transactionObj = $this->getTransactionObj(); 
		
		$arrSOKeys = array_column($rsDetail,'sokey');
		
		// cari jo yang statusnya gk sama dengan konfirmasi, kalo ad, return error
		$rsSO = $transactionObj->searchDataRow(array($transactionObj->tableName.'.pkey',$transactionObj->tableName.'.code'),
											   ' and '.$transactionObj->tableName.'.pkey in ('.$this->oDbCon->paramString($arrSOKeys,',').') 
											     and '.$transactionObj->tableName.'.statuskey <> 2' 
											   );
        if(!empty($rsSO))
			$this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' . $this->errorMsg[201].'<br><strong>'.implode(',',array_column($rsSO,'code')).'</strong>. ' .$this->errorMsg[204],true);
         
    } 

 	function confirmTrans($rsHeader){
        $id = $rsHeader[0]['pkey'];

        $transactionObj = $this->getTransactionObj(); 
        $rsDetail = $this->getDetailById($id);

        foreach($rsDetail as $row) 
            $transactionObj->changeStatus($row['sokey'],3,'',false,true, true);  // hati2 kalo copas ini ke tmp lain, karena ad parameter ignoreValidation
 
    } 

    
       
    function validateCancel($rsHeader, $autoChangeStatus = false){
        $id = $rsHeader[0]['pkey'];
 
    } 
     
    function cancelTrans($rsHeader,$copy){  
        
        $transactionObj = $this->getTransactionObj(); 
		$id = $rsHeader[0]['pkey'];
		
        $rsDetail = $this->getDetailById($id);

        foreach($rsDetail as $row) 
            $transactionObj->changeStatus($row['sokey'],2,'',false,true, true);  // hati2 kalo copas ini ke tmp lain, karena ad parameter ignoreValidation
 
		if ($copy)
			$this->copyDataOnCancel($id);	  
         
	} 

    
    function getManifest($sokey, $criteria = ''){
        
        // TODO: nanti perlu ditambahkan informasi tablekey disini
        
        $sql = 'select
                    '.$this->tableName.'.pkey,
                    '.$this->tableName.'.code,
                    '.$this->tableName.'.trdate ,
                    '.$this->tableName.'.statuskey 
                from 
                    '.$this->tableName.',
                    '.$this->tableNameDetail.'
                where 
                    '.$this->tableName.'.pkey = '.$this->tableNameDetail.'.refkey and
                    '.$this->tableNameDetail.'.sokey = '.$this->oDbCon->paramString($sokey);
        
        
        if(!empty($criteria))
            $sql .=  $criteria;
                        
        return  $this->oDbCon->doQuery($sql);
        
    } 
    
    function getTransactionObj(){
        return new LogisticSalesOrder();
    }
	
	function countTotalRowsByTOP($pkey){ 
		
		$pkey = explode(',',$pkey);
		
		// utk printan, biar lebih efisien
		$sql = 'select '.$this->tableNameDetail.'.refkey, count('. $this->tableSalesOrder.'.pkey) as total,  '. $this->tableSalesOrder.'.termofpaymentkey
				from '.$this->tableNameDetail.', '. $this->tableSalesOrder.'
				where '.$this->tableNameDetail.'.refkey in ('.$this->oDbCon->paramString($pkey,',').') and '.$this->tableNameDetail.'.sokey = '. $this->tableSalesOrder.'.pkey
				group by  '.$this->tableNameDetail.'.refkey ,'. $this->tableSalesOrder.'.termofpaymentkey ';
		
		$this->setLog($sql,true);
		
		return  $this->oDbCon->doQuery($sql);
	}
     
}

?>