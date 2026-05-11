<?php 
class ChangeItemSN extends BaseClass{ 
 
    function __construct(){
		
		parent::__construct();
       
		$this->tableName = 'change_item_sn_header';
        $this->tableStatus = 'transaction_status';
		$this->tableItem = 'item';  
        $this->tableSN = 'item_sn'; 
        $this->tableItemMovementSN = 'item_sn_movement'; 
        $this->tableItemInDetailSN = 'item_in_detail_sn'; 
        $this->tableItemOutDetailSN = 'item_out_detail_sn'; 
        $this->tableItemInReceiveDetailSN = 'item_in_receive_detail_sn'; 
        $this->tableItemOutDeliveryDetailSN = 'item_out_delivery_detail_sn'; 
        $this->tableVendorWarrantyClaimDetailSN = 'vendor_warranty_claim_detail_sn'; 
        $this->tableVendorWarrantyClaimReturnDetailSN = 'vendor_warranty_claim_return_detail_sn'; 
        $this->tableWarrantyClaimProgress = 'warranty_claim_progress_header'; 
        $this->tableWarrantyClaim= 'warranty_claim_detail'; 
        $this->securityObject = 'ChangeItemSN';
        $this->tableVendorPartNumber = 'item_vendor_part_number';
        $this->tableWarehouse = 'warehouse';
        $this->isTransaction = true;
        
        $this->arrData = array();
        $this->arrData['pkey'] = array('pkey');
        $this->arrData['code'] = array('code');
        $this->arrData['trdate'] = array('trDate','date');
        $this->arrData['itemkey'] = array('hidItemKey');
        $this->arrData['serialnumber'] = array('serialNumber');
        $this->arrData['vendorpartnumberkey'] = array('hidVendorPartNumberKey');
        $this->arrData['trdesc'] = array('trDesc');
        $this->arrData['newserialnumber'] = array('newSerialNumber');
        $this->arrData['statuskey'] = array('selStatus');
        
        $this->arrTableTrans = array( 
                $this->tableSN,
                $this->tableItemMovementSN,
                $this->tableItemInDetailSN,
                $this->tableItemOutDetailSN,
                $this->tableItemInReceiveDetailSN,
                $this->tableItemOutDeliveryDetailSN,
                $this->tableVendorWarrantyClaimDetailSN,
                $this->tableVendorWarrantyClaimReturnDetailSN,
                $this->tableWarrantyClaimProgress,
                $this->tableWarrantyClaim
        );
        
                
        $this->arrDataListAvailableColumn = array();
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code', 'defaut'=>true, 'width' => 80));
        array_push($this->arrDataListAvailableColumn, array('code' => 'date','title' => 'date','dbfield' => 'trdate','default'=>true, 'width' => 100, 'align' =>'center', 'format' => 'date'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'itemName','title' => 'itemName','dbfield' => 'itemname','default'=>true,'width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'vendorPartNumber','title' => 'vendorPartNumber','dbfield' => 'partnumber','default'=>true,'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'serialNumber','title' => 'serialNumber','dbfield' => 'serialnumber','default'=>true,'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'newSerialNumber','title' => 'serialNumber','dbfield' => 'newserialnumber','default'=>true,'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'note','title' => 'note','dbfield' => 'trdesc','width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 80));

        
		$this->overwriteConfig();
   }
   
    function getQuery(){
	   
        return '
			SELECT
                '.$this->tableName.'.*,
                '.$this->tableVendorPartNumber.'.partnumber,
                '.$this->tableItem.'.name as itemname, 
                '.$this->tableStatus.'.status as statusname

			FROM 
                '.$this->tableName.',
                '.$this->tableVendorPartNumber.',
                '.$this->tableItem.', 
                '.$this->tableStatus.'
            WHERE
                '.$this->tableName.'.itemkey ='.$this->tableItem.'.pkey and 
                '.$this->tableName.'.vendorpartnumberkey ='.$this->tableVendorPartNumber.'.pkey and 
                '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey   

 		' .$this->criteria ; 
		 
    }  
     
    function validateForm($arr,$pkey = ''){ 
        $item = new Item();
        
		$arrayToJs = parent::validateForm($arr,$pkey);
        
		$arrItemkey = $arr['hidItemKey']; 
        
        $serialNumber = trim($arr['serialNumber']);
        $newSerialNumber = trim($arr['newSerialNumber']); 
        
            
        if(empty($newSerialNumber))
            $this->addErrorList($arrayToJs,false,$this->errorMsg['serialnumber'][1]);
            
        $rsSN = $this->searchSN($serialNumber);  
        if(empty($serialNumber)){
            $this->addErrorList($arrayToJs,false,$this->errorMsg['serialnumber'][1]);
        }else if(empty($rsSN)){
            $this->addErrorList($arrayToJs,false,'<strong>'.$serialNumber.'</strong>. '.$this->errorMsg['serialnumber'][4]); 
        }
        
         
        // cek SN baru blm boleh terdaftar 
        $rsNewSN =  $this->searchSN($newSerialNumber);
        if(!empty($rsNewSN)){
            $this->addErrorList($arrayToJs,false,'<strong>'.$newSerialNumber.'</strong>. '.$this->errorMsg['serialnumber'][3]); 
        }
         
		return $arrayToJs;
	 }

    
    function confirmTrans($rsHeader){
        $id = $rsHeader[0]['pkey'];
        $oldSN = $rsHeader[0]['serialnumber'];
        $newSN = $rsHeader[0]['newserialnumber'];
         
        $arrTable = array();
        foreach($this->arrTableTrans as $row=>$value){
            
                $sql = 'update
                        '.$value.'
                    SET
                        serialnumber =  '.$this->oDbCon->paramString($newSN).'
                    WHERE
                        serialnumber = '.$this->oDbCon->paramString($oldSN).'
                ';
            
                $this->oDbCon->execute($sql);  

        } 
	}
    
    
    function cancelTrans($rsHeader,$copy){
		        
		$id = $rsHeader[0]['pkey'];
        $oldSN = $rsHeader[0]['serialnumber'];
        $newSN = $rsHeader[0]['newserialnumber'];
          
        $arrTable = array();
        foreach($this->arrTableTrans as $row=>$value){
            
                $sql = 'update
                        '.$value.'
                    SET
                        serialnumber =  '.$this->oDbCon->paramString($oldSN).'
                    WHERE
                        serialnumber = '.$this->oDbCon->paramString($newSN).'
                ';
            
                $this->oDbCon->execute($sql);  

        } 
        
        $this->copyDataOnCancel($id); 
	}  
    
    
    function validateConfirm($rsHeader){   
           
        $serialNumber = $rsHeader[0]['serialnumber'];
        
        $rsSN = $this->searchSN($serialNumber);  
        if(empty($rsSN)){ 
            $this->addErrorLog(false,'<strong>'.$serialNumber.'</strong>. '.$this->errorMsg['serialnumber'][4]); 
        }
        
    }
    
    function validateCancel($rsHeader,$autoChangeStatus=false){ 
        $arrayToJs = array(); 
        return $arrayToJs;    
    }
    

  	
    function normalizeParameter($arrParam, $trim=false){
        
        $item = new Item();
        
        $arrParam = parent::normalizeParameter($arrParam);  
        
    
        return $arrParam;
    }
    
    
    function searchSN($sn,$criteria=''){
                
            $sql = 'select 
                        '.$this->tableSN.'.*,
                        '.$this->tableItem.'.pkey as itemkey,
                        '.$this->tableItem.'.name as itemname,
                        '.$this->tableVendorPartNumber.'.partnumber,
                        '.$this->tableVendorPartNumber.'.pkey as vendorpartnumberkey
                    from
                        '.$this->tableSN.',
                        '.$this->tableItem.',
                        '.$this->tableVendorPartNumber.'
                    where
                        '.$this->tableSN.'.itemkey ='.$this->tableItem.'.pkey and
                        '.$this->tableSN.'.vendorpartnumberkey ='.$this->tableVendorPartNumber.'.pkey and
                        '.$this->tableSN.'.serialnumber = '.$this->oDbCon->paramString($sn).'
            ';

            if (!empty($criteria))  
                $sql .=  ' ' .$criteria; 
             
            $rs = $this->oDbCon->doQuery($sql);

            return $rs;
 
    }
    
    
}
?>
