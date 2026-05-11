<?php
  
class PutAway extends BaseClass{  
 
   function __construct(){
		
		parent::__construct();
       
		$this->tableName = 'put_away_header';
		$this->tableNameDetail = 'put_away_detail';
		$this->tableWarehouse = 'warehouse';
        $this->tableItemReceiving = 'item_receiving_header';
        $this->tablePallet = 'pallet';
        $this->tableWarehouseLayout = 'warehouse_layout';
        $this->tableItem = 'item';
		$this->tableStatus = 'transaction_status';

        $this->isTransaction = true; 	
       
		$this->securityObject = 'PutAway'; 

        $this->arrDataDetail = array();  
        $this->arrDataDetail['pkey'] = array('hidDetailKey');
        $this->arrDataDetail['refkey'] = array('pkey','ref');
        $this->arrDataDetail['itemreceivingdetailkey'] = array('hidItemReceivingDetailKey');
        $this->arrDataDetail['itemkey'] = array('hidItemKey');
        $this->arrDataDetail['receivingqty'] = array('receivingQty','number');
        $this->arrDataDetail['putawayqty'] = array('putAwayQty','number');
        $this->arrDataDetail['qty'] = array('qty','number');
        

        $arrDetails = array();
        array_push($arrDetails, array('dataset' => $this->arrDataDetail));
       
        $this->arrData = array(); 
        $this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails));
        $this->arrData['code'] = array('code');
        $this->arrData['trdate'] = array('trDate','date');
        $this->arrData['warehousekey'] = array('selWarehouseKey');
        $this->arrData['putawaydate'] = array('trPutAwayDate','date');
        $this->arrData['warehouselayoutkey'] = array('hidWarehouseLayoutKey');
        $this->arrData['warehouselayoutoriginkey'] = array('hidWarehouseLayoutOriginKey');
        $this->arrData['palletkey'] = array('hidPalletKey');
        $this->arrData['submissionnumber'] = array('submissionNumber');
        $this->arrData['refkey'] = array('hidRefKey');
        $this->arrData['trdesc'] = array('trDesc');
        $this->arrData['statuskey'] = array('selStatus');

        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 90));
        array_push($this->arrDataListAvailableColumn, array('code' => 'date','title' => 'date','dbfield' => 'trdate','default'=>true, 'width' => 100, 'align' =>'center','format' => 'date'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'warehouse','title' => 'warehouse','dbfield' => 'warehousename','default'=>true, 'width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'location','title' => 'location','dbfield' => 'warehouselayoutname','default'=>true, 'width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'pallet','title' => 'pallet','dbfield' => 'palletname','default'=>true, 'width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'itemReceiving','title' => 'itemReceiving','dbfield' => 'refcode','default'=>true, 'width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 100));

         $this->includeClassDependencies(array(
              'Warehouse.class.php',
              'WarehouseLayout.class.php',
              'Pallet.class.php',
              'ItemMovement.class.php',
              'ItemReceiving.class.php'
        ));  
         
        $this->overwriteConfig();
		 
   }


    function getQuery(){
        $sql = '
			SELECT '.$this->tableName.'.* ,
                '.$this->tableWarehouse.'.name as warehousename,
                '.$this->tableWarehouseLayout.'.name as warehouselayoutname,
                '.$this->tablePallet.'.name as palletname,
                '.$this->tableItemReceiving.'.code as refcode,
			    '.$this->tableStatus.'.status as statusname 
			FROM 
                '.$this->tableName.'
                        left join '.$this->tableWarehouseLayout.' on '.$this->tableName.'.warehouselayoutkey = '.$this->tableWarehouseLayout.'.pkey
                        left join '.$this->tablePallet.' on '.$this->tableName.'.palletkey = '.$this->tablePallet.'.pkey,
                '.$this->tableItemReceiving.',
                '.$this->tableWarehouse.',
                '.$this->tableStatus.'
			WHERE 
                '.$this->tableName.'.warehousekey = '.$this->tableWarehouse.'.pkey and
                '.$this->tableName.'.refkey = '.$this->tableItemReceiving.'.pkey and
                '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey
 		' .$this->criteria ;  
		  
        $sql .=  $this->getCompanyCriteria() ;
      
      return $sql;
    }

    function getDetailWithRelatedInformation($pkey,$criteria = ''){
        
	   $sql = 'select
	   			'.$this->tableNameDetail.'.*,
                '.$this->tableItem.'.name as itemname
			  from
			  	'.$this->tableNameDetail.',
                '.$this->tableItem.'
			  where
                '.$this->tableNameDetail.'.itemkey = '.$this->tableItem.'.pkey and
			  	'.$this->tableNameDetail.'.refkey = '.$this->oDbCon->paramString($pkey);
        
        $sql .= $criteria;
              
		return $this->oDbCon->doQuery($sql);
	
    }

    function closeTrans($rsHeader)
    {
        $pkey = $rsHeader[0]['pkey'];
        

        $id = $rsHeader[0]['pkey']; 

        $rsDetail = $this->getDetailWithRelatedInformation($id);

        $itemMovement = new ItemMovement();  
        $note = $rsHeader[0]['code'].'. '.$this->ucFirst($this->lang['putAway']); 
        for($i=0;$i<count($rsDetail); $i++){	 
           if ($rsDetail[$i]['qty'] != 0)
            $itemMovement->updateItemMovement($id,$rsDetail[$i]['itemkey'],-$rsDetail[$i]['qty'],0,$this->tableName, array('warehouselayoutkey'=> $rsHeader[0]['warehouselayoutoriginkey'],'warehousekey' => $rsHeader[0]['warehousekey']), $note,$rsHeader[0]['trdate']);
            $itemMovement->updateItemMovement($id,$rsDetail[$i]['itemkey'],$rsDetail[$i]['qty'],0,$this->tableName, array('warehouselayoutkey'=> $rsHeader[0]['warehouselayoutkey'],'warehousekey' => $rsHeader[0]['warehousekey']), $note,$rsHeader[0]['trdate']);
        }	


    }

    function validateForm($arr,$pkey = ''){
		$arrayToJs = parent::validateForm($arr,$pkey); 

        $warehousekey = $arr['selWarehouseKey'];
        $warehouselayoutkey = $arr['hidWarehouseLayoutKey'];
        $palletkey = $arr['hidPalletKey'];
        $refkey = $arr['hidRefKey'];

        $arrItemKey = $arr['hidItemKey'];
        $arrReceivingQty = $arr['receivingQty'];
        $arrPutAwayQty = $arr['qty'];
        $arrItemName = $arr['itemName'];

        if(empty($warehousekey)) {
            $this->addErrorList($arrayToJs,false,$this->errorMsg['warehouse'][1]); 
        }

        // if(empty($warehouselayoutkey)) {
        //     $this->addErrorList($arrayToJs,false,$this->errorMsg['warehouseLayout'][1]); 
        // }

        // if(empty($palletkey)) {
        //     $this->addErrorList($arrayToJs,false,$this->errorMsg['pallet'][1]); 
        // }

        // if(empty($refkey)) {
        //     $this->addErrorList($arrayToJs,false,$this->errorMsg['putAway'][1]); 
        // }

        if(empty($arrItemKey[0])) {
            $this->addErrorList($arrayToJs,false,$this->errorMsg[501]); 
        } else {
            for($i=0; $i<count($arrItemKey); $i++) {
                $putAwayQty = $this->unFormatNumber($arrPutAwayQty[$i]);
                $receivingQty = $this->unFormatNumber($arrReceivingQty[$i]);

                $itemName = $arr['itemName'][$i];
                if(empty($arrItemKey[$i])) {
                    $this->addErrorList($arrayToJs,false,$this->errorMsg['item'][1]); 
                }

                if($receivingQty <= 0) {
                    $this->addErrorList($arrayToJs,false,'<strong>'.$itemName.'.</strong> '.$this->errorMsg['putAway'][2]);
                }

                if($putAwayQty <= 0) {
                    $this->addErrorList($arrayToJs,false,'<strong>'.$itemName.'.</strong> '.$this->errorMsg[510]); 
                } else {

                    if($putAwayQty > $receivingQty) {
                        $this->addErrorList($arrayToJs,false,'<strong>'.$itemName.'.</strong> '.$this->errorMsg['putAway'][3]); 
                    }

                }

            }
        }
 		  
		return $arrayToJs;
    }
    
    function validateConfirm($rsHeader){

        $id = $rsHeader[0]['pkey'];

        $rsDetail = $this->getDetailWithRelatedInformation($id);

        // if(empty($rsDetail)) {
        //     $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '. $this->errorMsg[501]);
        // } else {
        //     for($i=0; $i<count($rsDetail); $i++) {
        //         if($rsDetail[$i]['receivingqty'] <= 0) {
        //             $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '. $rsDetail[$i]['itemname'] .'. '. $this->errorMsg['putAway'][2]);    
        //         }
        //         if($rsDetail[$i]['qty'] <=0){
        //             $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '. $rsDetail[$i]['itemname'] .'. '. $this->errorMsg['putAway'][2]); 
        //         }else {
        //             if($rsDetail[$i]['qty'] > $rsDetail[$i]['receivingqty']) {
        //                 $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '. $rsDetail[$i]['itemname'] .'. '. $this->errorMsg['putAway'][3]); 
        //             }
        //         }
        //     }
        // }

    }

    function confirmTrans($rsHeader){  
        
        $id = $rsHeader[0]['pkey']; 

        $rsDetail = $this->getDetailWithRelatedInformation($id);

        // $itemMovement = new ItemMovement();  
        // $note = $rsHeader[0]['code'].'. '.$this->ucFirst($this->lang['putAway']. ' ' .  $this->lang['from']) . ' '.$rsHeader[0]['itemReceiving']; 
        // for($i=0;$i<count($rsDetail); $i++){	 
        //    if ($rsDetail[$i]['qty'] != 0)
        //     $itemMovement->updateItemMovement($id,$rsDetail[$i]['itemkey'],-$rsDetail[$i]['qty'],0,$this->tableName, array('warehouselayoutkey'=> $rsHeader[0]['warehouselayoutkey'],'warehousekey' => $rsHeader[0]['warehousekey']), $note,$rsHeader[0]['trdate']);
        // }	

    } 

    function validateCancel($rsHeader,$autoChangeStatus=false){ 
        $id = $rsHeader[0]['pkey'];
    } 



    function cancelTrans($rsHeader,$copy){ 
        $id = $rsHeader[0]['pkey']; 

        $itemMovement = new ItemMovement();  
        $itemMovement->cancelMovement($id,$this->tableName);  

        if ($copy)
            $this->copyDataOnCancel($id);	  


    }  

    function backConfirmTrans($rsHeader)
    {

        $id = $rsHeader[0]['pkey']; 

        $itemMovement = new ItemMovement();  
        $itemMovement->cancelMovement($id,$this->tableName);  

    }

    function afterStatusChanged($rsHeader){  
        $itemReceiving = new ItemReceiving();
        $itemReceiving->updateQtyPutAway($rsHeader[0]['refkey']); 
    }

    function normalizeParameter($arrParam, $trim = false){ 
         
        $arrParam = parent::normalizeParameter($arrParam); 
       
        return $arrParam;
        
    }

    

}
?>