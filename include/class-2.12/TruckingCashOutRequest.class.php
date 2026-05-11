<?php  
class TruckingCashOutRequest extends BaseClass{

    function __construct()
    {
        // untuk mencatat TCO yg butuh pengajuan, sepertinya...

        parent::__construct();

        $this->tableName = 'trucking_cash_out_request_header';
        $this->tableNameDetail = 'trucking_cash_out_request_detail';
        // $this->tableCategory = 'waste_category';
        $this->tableStatus = 'transaction_status';
        $this->tableEmployee = 'employee';
        $this->tableTruckingCostCashOutHeader = 'trucking_cost_cash_out_header';

        $this->securityObject = 'TruckingCashOutRequest';
        // $this->importUrl = 'import/waste';
        $this->isTransaction = true;
        $this->newLoad = true;

        $this->arrDataDetail = array();
        $this->arrDataDetail['pkey'] = array('hidDetailKey');
        $this->arrDataDetail['refkey'] = array('pkey', 'ref'); 
        $this->arrDataDetail['costcashoutkey'] = array('hidCostCashOutKey'); 
        $this->arrDataDetail['employeekey'] = array('hidEmployeeKey'); 
//        $this->arrDataDetail['submissiondate'] = array('submissionDate', 'date');
        $this->arrDataDetail['amount'] = array('amount', 'number');

        $this->arrData = array();
        $this->arrData['pkey'] = array('pkey', array('dataDetail' => array('dataset' => $this->arrDataDetail)));
        $this->arrData['code'] = array('code');
        $this->arrData['trdate'] = array('trDate', 'date');
        $this->arrData['recipientkey'] = array('hidRecipientKey');
        $this->arrData['notes'] = array('notes');
        $this->arrData['statuskey'] = array('selStatus');
        $this->arrData['grandtotal'] = array('total', 'number');
        $this->arrData['startdateperiod'] = array('trStartDatePeriod', 'date');
        $this->arrData['enddateperiod'] = array('trEndDatePeriod', 'date');

        $this->printMenu = array();
        array_push($this->printMenu, array('code' => 'printTransaction', 'name' => $this->lang['printTransaction'],  'icon' => 'print', 'url' => 'print/truckingCashOutRequest'));
        

        // $this->arrLockedTable = array();
        // $defaultFieldName = 'disposalkey';
        // array_push($this->arrLockedTable, array('table' => 'disposal_work_order_detail', 'field' => $defaultFieldName));

        $this->arrDataListAvailableColumn = array();
        array_push($this->arrDataListAvailableColumn, array('code' => 'code', 'title' => 'code', 'dbfield' => 'code', 'default' => true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'date', 'title' => 'date', 'dbfield' => 'trdate', 'align' => 'center', 'format' => 'date', 'default' => true, 'width' => 150, 'format' => 'datetime'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'recipient', 'title' => 'recipient', 'dbfield' => 'recipientname', 'default' => true, 'width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'notes', 'title' => 'note', 'dbfield' => 'notes', 'default' => true, 'width' => 400));
        array_push($this->arrDataListAvailableColumn, array('code' => 'total', 'title' => 'total', 'dbfield' => 'grandtotal','default' => true, 'align' =>'right',  'format' => 'integer', 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status', 'title' => 'status', 'dbfield' => 'statusname', 'default' => true, 'width' => 150));


        $this->arrSearchColumn = array();
        array_push($this->arrSearchColumn, array('Kode', $this->tableName . '.code'));
        array_push($this->arrSearchColumn, array('Tanggal', $this->tableName . '.trdate'));
        array_push($this->arrSearchColumn, array('Penerima', $this->tableEmployee . '.name'));
        array_push($this->arrSearchColumn, array('Catatan', $this->tableName . '.notes'));
        array_push($this->arrSearchColumn, array('Total', $this->tableName . '.grandtotal'));
        array_push($this->arrSearchColumn, array('Status', $this->tableStatus . '.status'));


        $this->includeClassDependencies(array(
            'TruckingCostCashOut.class.php'
        ));

        $this->overwriteConfig();
    }



    function getQuery()
    {

        return '
				select
					' . $this->tableName . '.*,
                   	' . $this->tableStatus . '.status as statusname,
                    ' . $this->tableEmployee . '.name as recipientname
				from 
                    ' . $this->tableStatus . ', 
                    ' . $this->tableName . '
                left join '. $this->tableEmployee .' on '. $this->tableName .' . recipientkey =' . $this->tableEmployee . '.pkey
				where  		
					' . $this->tableName . '.statuskey = ' . $this->tableStatus . '.pkey  
 		'. $this->criteria;
    }


    // function generateDefaultQueryForAutoComplete($returnField)
    // {

    //     $sql = 'select
    //               ' . $returnField['key'] . ',
    //               concat(' . $this->tableName . '.code' . '," - ",' . $returnField['value'] . ') as value 
    //           from 
    //               ' . $this->tableName . ',
    //               ' . $this->tableStatus . '
    //           where  		
    //               ' . $this->tableName . '.statuskey = ' . $this->tableStatus . '.pkey
    //       ';
    //     return $sql;
    // }

    function getDetailWithRelatedInformation($pkey, $criteria = '')
    {
        $sql = 'select
	   			' . $this->tableNameDetail . '.*,
                '. $this->tableTruckingCostCashOutHeader .'.code as refcode,
                '. $this->tableTruckingCostCashOutHeader .'.trdate as refdate,
                '. $this->tableTruckingCostCashOutHeader .'.statuskey as refstatuskey,
                '. $this->tableEmployee .'.name as employeename             
			  from
			  	' . $this->tableNameDetail . ' 
                left join '. $this->tableTruckingCostCashOutHeader .' on '. $this->tableNameDetail .'.costcashoutkey =' . $this->tableTruckingCostCashOutHeader . '.pkey
                left join '. $this->tableEmployee .' on '. $this->tableTruckingCostCashOutHeader .'.employeekey =' . $this->tableEmployee .'.pkey
			  where
			  	' . $this->tableNameDetail . '.refkey = ' . $this->oDbCon->paramString($pkey);

        $sql .= $criteria;
 
        return $this->oDbCon->doQuery($sql);
    }

    function validateConfirm($rsHeader){
        $truckingCashOut = new TruckingCostCashOut();

        $rsDetails = $this->getDetailWithRelatedInformation($rsHeader[0]['pkey']);

        $arrCashOutKey = array_column($rsDetails,'costcashoutkey');
         
        $rsTruckingCashOutCol = $truckingCashOut->searchDataRow(array($truckingCashOut->tableName.'.pkey',$truckingCashOut->tableName.'.code', $truckingCashOut->tableName.'.employeekey',$truckingCashOut->tableName.'.statuskey'),
                                                                      ' and ' .  $truckingCashOut->tableName.'.pkey in ('.$this->oDbCon->paramString($arrCashOutKey,',').')');
        
        $rsTruckingCashOutCol = array_column($rsTruckingCashOutCol,null,'pkey');
        
        
        $recipientkey = $rsHeader[0]['recipientkey'];
        
        foreach($rsDetails as $rsDetail){
            
            $cashOutKey = $rsDetail['costcashoutkey'];
            
            $rsTruckingCashOut = $rsTruckingCashOutCol[$cashOutKey]; 
            
            if($rsTruckingCashOut['statuskey'] <> 1)
                $this->addErrorLog(false, '<strong>'.$rsHeader[0]['code'].'</strong>. ' . $this->errorMsg[201].'<br><strong>'.$rsTruckingCashOut['code'].'</strong>, '.$this->errorMsg[203]);
             
            if($recipientkey <> $rsTruckingCashOut['employeekey'])
                $this->addErrorLog(false, '<strong>'.$rsHeader[0]['code'].'</strong>. ' . $this->errorMsg[201].'<br><strong>'.$rsTruckingCashOut['code'].'</strong>, '.$this->errorMsg['truckingCashOutRequest'][2]); 
             
        }

    }

    function confirmTrans($rsHeader){

        $id = $rsHeader[0]['pkey'];
        
        $truckingCostCashOut = new TruckingCostCashOut();
        $rsDetails = $this->getDetailWithRelatedInformation($id);

        foreach($rsDetails as $rsDetail) { 
            $truckingCostCashOut->changeStatus($rsDetail['costcashoutkey'], 2,'',false,true);  
            $truckingCostCashOut->updateRequestkey($rsDetail['costcashoutkey'], $id);
        }
    }


    function validateCancel($rsHeader,$autoChangeStatus=false){ 
        
//         kalo TCO sudah konfirmasi (duit sudah ditransfer), request gk boleh dicancel
        
         $id =  $rsHeader[0]['pkey'];
        
         $truckingCostCashOut = new TruckingCostCashOut(); 
         $rsDetail = $this->getDetailWithRelatedInformation($id);
        
         foreach($rsDetail as $row) {  
             if( $row['refstatuskey'] > 2) // statuss TCO baatal jg request gk boleh cancel, utk history. kalo mau revisi, buat request baru
                $this->addErrorLog(false, '<strong>'.$rsHeader[0]['code'].'</strong>. ' . $this->errorMsg[225]); 
         }
                
   	 } 

    
    
    function cancelTrans($rsHeader, $copy){
        $id =  $rsHeader[0]['pkey'];
        
        $truckingCostCashOut = new TruckingCostCashOut(); 
        $rsDetails = $this->getDetailWithRelatedInformation($id);

//        foreach ($rsDetails as $rsDetail)  
            // gk ush dicancel, TCO nya dicancel manual sj. jgn sampe karena 1 TCO yg salah, semua kecancel
//            $truckingCashOutRequest->changeStatus($rsDetail['costcashoutkey'], 5,"",false,true);  
        	 
        foreach($rsDetails as $rsDetail) {  
            $truckingCostCashOut->updateRequestkey($rsDetail['costcashoutkey'], 0);
        }
        
		if ($copy)
			$this->copyDataOnCancel($id);	
        
    }   

    
      
    function afterStatusChanged($rsHeader){   
        // retrieve latest status
        $id = $rsHeader[0]['pkey'];
        
        $rsHeader = $this->getDataRowById($id);
        
        $ubmissionDate = '';
        $statusCriteria = '';
        
        if ($rsHeader[0]['statuskey'] == 2)
            $ubmissionDate = $rsHeader[0]['trdate'];
        if ($rsHeader[0]['statuskey'] == 4){
            $ubmissionDate = '0000-00-00 00:00';
            $statusCriteria = ', statuskey = 1'; // ini gkboleh kalo statusny sudah batal
        }
            
 
        if($ubmissionDate <> ''){
            
            $rsDetail = $this->getDetailById($id);
             
            $sql = 'update ' . $this->tableTruckingCostCashOutHeader.' 
                    set trsubmissiondate = '. $this->oDbCon->paramString($ubmissionDate).$statusCriteria.'
                    where 
                        '.$this->tableTruckingCostCashOutHeader.'.statuskey in (1,2) and
                        '.$this->tableTruckingCostCashOutHeader.'.pkey in (' . $this->oDbCon->paramString( array_column($rsDetail, 'costcashoutkey'), ',') .')
                    ';
              
            $this->oDbCon->execute($sql);
        }
     
        
    }
    
    
    function validateForm($arr, $pkey = ''){

        $arrayToJs = parent::validateForm($arr, $pkey);

        $truckingCostCashOut = new TruckingCostCashOut();
        
        $startdate = $arr['trStartDatePeriod'];
        $enddate = $arr['trEndDatePeriod'];   
        $recipientKey = $arr['hidRecipientKey'];
        $arrCashOutKey = $arr['hidCostCashOutKey']; 
     
        // ambil ulang informasi ks keluarnya 
        $rsTCO = $truckingCostCashOut->searchDataRow(array($truckingCostCashOut->tableName.'.pkey',$truckingCostCashOut->tableName.'.code',$truckingCostCashOut->tableName.'.trdate', $truckingCostCashOut->tableName.'.employeekey', $truckingCostCashOut->tableName.'.statuskey'),
                                                  ' and ' .$truckingCostCashOut->tableName.'.pkey in ('.$this->oDbCon->paramString( $arrCashOutKey, ',').')');
        $rsTCO = array_column($rsTCO, null,'pkey' );
        
        
        if(empty($recipientKey))  
            $this->addErrorList($arrayToJs, false, $this->errorMsg['recipient'][1]);
       
         
        $dateDiff = $this->dateDiff($startdate, $enddate);
        if($dateDiff < 0){
            $this->addErrorList($arrayToJs, false, $this->errorMsg['date'][3]);
        }
        
        $arrDetailKey = array();
        $hasCashOut = false; 
        for($i=0;$i<count($arrCashOutKey);$i++) { 
            $cashOutKey = $arrCashOutKey[$i];
            
           if (!empty($arrCashOutKey[$i])) {
                $hasCashOut = true;  
               
                if($recipientKey <> $rsTCO[$cashOutKey]['employeekey']) 
                    $this->addErrorList($arrayToJs, false, $rsTCO[$cashOutKey]['code'].'. '.$this->errorMsg['truckingCashOutRequest'][2]); 
                
                if($rsTCO[$cashOutKey]['statuskey'] <> 1) 
                   $this->addErrorList($arrayToJs, false, $rsTCO[$cashOutKey]['code'].'. '.$this->errorMsg[202]); 
             
                $cashOutDate = $this->formatDBDate($rsTCO[$cashOutKey]['trdate'],' d / m / Y H:i');
               
                $dateDiffStart = $this->dateDiff($startdate,$cashOutDate);
                $dateDiffEnd = $this->dateDiff($cashOutDate,$enddate);
 
                if ($dateDiffStart < 0)  
                    $this->addErrorList($arrayToJs, false, $rsTCO[$cashOutKey]['code'].'. '.$this->errorMsg['truckingCashOutRequest'][3]);
     
                if($dateDiffEnd < 0) 
                    $this->addErrorList($arrayToJs, false, $rsTCO[$cashOutKey]['code'].'. '.$this->errorMsg['truckingCashOutRequest'][3]);

           }
               
 
            if (in_array($arrCashOutKey[$i],$arrDetailKey)){   
                $this->addErrorList($arrayToJs,false,$rsTCO[$cashOutKey]['code'].'. '.$this->errorMsg[215]); 	 
            }else{ 
                array_push($arrDetailKey, $arrCashOutKey[$i]); 
            }

            
            
        }
          
        if (!$hasCashOut)
            $this->addErrorList($arrayToJs,false, $this->errorMsg['truckingCostCashOut'][1]); 	

        
  

        return $arrayToJs;
    }


    function normalizeParameter($arrParam, $trim = false)
    {
        $arrParam = parent::normalizeParameter($arrParam, true);
        // $this->setLog($arrParam, true);
        return $arrParam;
    } 

    
  }
