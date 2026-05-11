<?php
  
class VendorWarrantyClaimReturn extends BaseClass{ 
 
function __construct(){

    parent::__construct();

    $this->tableName = 'vendor_warranty_claim_return_header';
    $this->tableNameDetail = 'vendor_warranty_claim_return_detail';
    $this->tableSerial = 'vendor_warranty_claim_return_detail_sn';
    $this->tableClaim = 'vendor_warranty_claim_header';
    $this->tableClaimDetail = 'vendor_warranty_claim_detail';
    $this->tableItemUnit = 'item_unit';
    $this->tableItem = 'item';
    $this->tableVendorPartNumber = 'item_vendor_part_number';
    
    $this->tableItemSN = 'item_sn';
    $this->tableWarehouse = 'warehouse';
    $this->tableSupplier = 'supplier';
    $this->tableEmployee = 'employee';
    $this->tableStatus = 'transaction_status';
    $this->isTransaction = true; 
    
    $this->allowedStatusForEdit = array(1,2);

    $this->tableNeedToBeCopyOnCancel = array($this->tableNameDetail);

    $this->securityObject = 'ItemIn';  

    $this->arrData = array();  
    $this->arrData['pkey'] = array('pkey');
    $this->arrData['code'] = array('code');
    $this->arrData['trdate'] = array('trDate','date');
    $this->arrData['fromwarehousekey'] = array('selFromWarehouseKey');
    $this->arrData['towarehousekey'] = array('selToWarehouseKey');
    $this->arrData['trdesc'] = array('trDesc'); 
    $this->arrData['refkey'] = array('hidRefKey');
    $this->arrData['reftabletype'] = array('reftabletype'); 
    $this->arrData['statuskey'] = array('selStatus');
    $this->arrData['isfullreceive'] = array('chkIsFullReceive');
    $this->arrData['supplierkey'] = array('hidSupplierKey');
    $this->arrData['refcode'] = array('refCode');
    $this->arrData['recipientname'] = array('recipientName');
    $this->arrData['grandtotal'] = array('grandtotal','number');
    $this->arrData['currencykey'] = array('selCurrency');
    $this->arrData['rate'] = array('currencyRate','number');
    $this->arrData['cnref'] = array('cnRef');
    
    
    $this->arrDataListAvailableColumn = array();
    array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 120));
    array_push($this->arrDataListAvailableColumn, array('code' => 'date','title' => 'date','dbfield' => 'trdate','default'=>true, 'width' => 100, 'align' =>'center', 'format' => 'date'));
    array_push($this->arrDataListAvailableColumn, array('code' => 'warehouse','title' => 'warehouse','dbfield' => 'warehousename','default'=>true,'width' => 150));
    array_push($this->arrDataListAvailableColumn, array('code' => 'RMANumber','title' => 'RMANumber','dbfield' => 'rmacode','default'=>true,'width' => 100));
    array_push($this->arrDataListAvailableColumn, array('code' => 'cnref','title' => 'cn/dn','dbfield' => 'cnref','default'=>true,'width' => 100));
    array_push($this->arrDataListAvailableColumn, array('code' => 'supplier','title' => 'supplier','dbfield' => 'suppliername','default'=>true,'width' => 250));
    array_push($this->arrDataListAvailableColumn, array('code' => 'note','title' => 'note','dbfield' => 'trdesc','width' => 200));
    array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
        
    $this->overwriteConfig();
     
}
 
    function getQuery(){

        $sql = '
            SELECT '.$this->tableName.'.* ,
               warehousefrom.name as warehousename,
			   warehouseto.name as warehousebusname,
               '.$this->tableSupplier.'.name as suppliername, 
               '.$this->tableClaim.'.code as claimcode, 
               '.$this->tableClaim.'.refcode as rmacode, 
               '.$this->tableStatus.'.status as statusname
            FROM 
               '.$this->tableStatus.', 
               '.$this->tableClaim.', 
               '.$this->tableName.'
                    left join ' . $this->tableSupplier .' on  '.$this->tableName.'.supplierkey = ' . $this->tableSupplier .'.pkey,
               '.$this->tableWarehouse.' warehousefrom,  '.$this->tableWarehouse.' warehouseto      
            WHERE '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey and  
                  '.$this->tableName.'.refkey = '.$this->tableClaim.'.pkey and  
                  '.$this->tableName.'.fromwarehousekey = warehousefrom.pkey and  
                  '.$this->tableName.'.towarehousekey = warehouseto.pkey
                  ' .$this->criteria ; 

        $sql .=  $this->getCompanyCriteria() ;
          
        return $sql;

    }
    

    function afterUpdateData($arrParam, $action){  
        $this->updateDetail($arrParam); 
    }
    	
    function afterStatusChanged($rsHeader){ 
        $rsHeader = $this->getDataRowById($rsHeader[0]['pkey']);
        $vendorWarrantyClaim = new VendorWarrantyClaim();
        $vendorWarrantyClaim->updateItemVendorClaim($rsHeader[0]['refkey']);
         
        // retrieve latest status
        $rsHeader = $this->getDataRowById($rsHeader[0]['pkey']);
        if ($rsHeader[0]['statuskey'] == 2)
            $this->changeStatus($rsHeader[0]['pkey'],3); 
        
        /*if ($rsHeader[0]['isfullreceive'] == 1 && $rsHeader[0]['statuskey'] == 2){  
            $sql = 'update '.$this->tableNameDetail.' set receivedqtyinbaseunit = qtyinbaseunit where refkey  = '.$this->oDbCon->paramString($rsHeader[0]['pkey']);
            $this->oDbCon->execute($sql); 
             
//            $this->changeStatus($rsHeader[0]['pkey'],3); 
        }*/ 
    }

    function updateDetail($arrParam){
        $pkey = $arrParam['pkey'];

        // recount convmultiplier
        $reCountResult = $this->reCalculateConversionDetail($arrParam); 
        $arrBaseUnitKey =  $reCountResult['baseUnitKey']; 
        $arrConvMultiplier = $reCountResult['unitConvMultiplier']; 
        $arrQtyInBaseUnit = $reCountResult['qtyInBaseUnit']; 

        $sql = 'delete from '.$this->tableNameDetail.' where refkey = '. $this->oDbCon->paramString($pkey);
        $this->oDbCon->execute($sql);

        $sql = 'delete from '.$this->tableSerial.' where refheaderkey = '. $this->oDbCon->paramString($pkey);
        $this->oDbCon->execute($sql);

        $arrClaimDetailkey = $arrParam['hidClaimDetailKey']; 
        $arrItemkey = $arrParam['hidItemKey']; 
	    $arrOldItemkey = $arrParam['hidOldItemKey']; 
        //$arrRefDetailkey = $arrParam['hidRefDetailkey'];
        $arrUnitKey = $arrParam['selUnit']; 
        $arrQty = $arrParam['qty'];    
        $arrPrice = $arrParam['priceInUnit'];    
        $arrTotal = $arrParam['subtotal'];    
        $arrSerialNumber = $arrParam['snList'];
        $arrSerialNumberReplacement = $arrParam['snReplacementList'];

        $arrVendorPartNumberKey = (isset($arrParam['hidVendorPartNumberKey'])) ? $arrParam['hidVendorPartNumberKey'] : array();  

        $item = new Item();

        for ($i=0;$i<count($arrItemkey);$i++){

            if (empty($arrOldItemkey[$i]))
                continue; 

            $detailkey = $this->getNextKey($this->tableNameDetail);        
            $arrSNItem =  preg_split('/[\n, ]+/', $arrSerialNumber[$i]); 
            $arrSNItemReplacement =  preg_split('/[\n, ]+/', $arrSerialNumberReplacement[$i]); 
            $qty =  $this->unFormatNumber($arrQty[$i]); 
            $priceInUnit =  $this->unFormatNumber($arrPrice[$i]); 
            $total =  $this->unFormatNumber($arrTotal[$i]); 
            $qtyinbaseunit = $this->unFormatNumber($arrQtyInBaseUnit[$i]); 
            $vendorPartNumber = (isset($arrVendorPartNumberKey[$i]) && !empty($arrVendorPartNumberKey[$i])) ? $arrVendorPartNumberKey[$i] : 0;
            $rsItem = $item->getDataRowById($arrOldItemkey[$i]);
            $costItem = $rsItem[0]['cogs'];

            $sql = 'insert into '.$this->tableNameDetail.' (
                        pkey,
                        refkey,
                        claimdetailkey,
                        itemkey,
			            olditemkey,
                        qty,  
                        priceinunit,  
                        unitkey,
                        unitconvmultiplier, 
                        qtyinbaseunit,
                        qtyoutstanding,
                        costinbaseunit,
                        vendorpartnumberkey,
                        total
                     ) values ( 
                        '.$this->oDbCon->paramString($detailkey).',
                        '.$this->oDbCon->paramString($pkey).',
                        '.$this->oDbCon->paramString($arrClaimDetailkey[$i]).',
                        '.$this->oDbCon->paramString($arrItemkey[$i]).',
                        '.$this->oDbCon->paramString($arrOldItemkey[$i]).',                
                        '.$this->oDbCon->paramString($qty).',
                        '.$this->oDbCon->paramString($priceInUnit).',
                        '.$this->oDbCon->paramString($arrUnitKey[$i]).',
                        '.$this->oDbCon->paramString($arrConvMultiplier[$i]).', 
                        '.$this->oDbCon->paramString($qtyinbaseunit).' , 
                        '.$this->oDbCon->paramString($qtyinbaseunit).' , 
                        '.$this->oDbCon->paramString($costItem).' , 
                        '.$this->oDbCon->paramString($vendorPartNumber).',
                        '.$this->oDbCon->paramString($total).'
                    )';	 
                
                $this->oDbCon->execute($sql);

              for($j=0;$j<count($arrSNItem);$j++){
                                  
                      $sn = preg_replace("/[^A-Za-z0-9]/", '', $arrSNItem[$j]); 
                      $snReplacement = preg_replace("/[^A-Za-z0-9]/", '', $arrSNItemReplacement[$j]); 
                      if (empty($sn)) continue;

                    $sql = 'insert into '.$this->tableSerial.' (
                            refkey,
                            refheaderkey,
                            serialnumber, 
                            serialnumberreplacement 
                         ) values (
                            '.$this->oDbCon->paramString($detailkey).',
                            '.$this->oDbCon->paramString($pkey).',
                            '.$this->oDbCon->paramString($sn).',
                            '.$this->oDbCon->paramString($snReplacement).'
                        )';
                        $this->oDbCon->execute($sql);
                }
        } 

    }

    function getSerialNumber($refkey){
        $sql = 'select 
                    '.$this->tableSerial.'.* ,
                    '.$this->tableItemSN.'.vendorpartnumberkey
                from 
                    '.$this->tableSerial.' 
                    left join '.$this->tableItemSN.' on '.$this->tableSerial.'.serialnumber = '.$this->tableItemSN.'.serialnumber
                where 
                    '.$this->tableSerial.'.refkey = ' . $this->oDbCon->paramString($refkey);
        return $this->oDbCon->doQuery($sql);
    }

    function validateForm($arr,$pkey = ''){

     $showVendorPartNumber = $this->loadSetting('showVendorPartNumber'); 
        
     // validasi kalo pake parts number, yg gk boleh sama item dan parts number
     // validasi parts number harus sesuai dengan itemnya

    $item = new Item(); 
    $vendorWarrantyClaim = new VendorWarrantyClaim();

    $arrayToJs = parent::validateForm($arr,$pkey); 

    $arrItemkey = $arr['hidItemKey'];
    $arrOldItemkey = $arr['hidOldItemKey']; 
    $arrSubtotal = $arr['subtotal']; 
    $arrSelUnit = $arr['selUnit']; 
    $arrQty = $arr['qty'];  
    $supplierkey = $arr['hidSupplierKey']; 
    $refkey = $arr['hidRefKey']; 
    $refcode = $arr['refCode']; 
    $arrSerialNumber = $arr['snList'];
    $arrSerialNumberReplacement = $arr['snReplacementList'];
    $arrSN = array();
    $arrItemVendor = array();

    if($showVendorPartNumber)
        $arrVendorPartNumberKey = $arr['hidVendorPartNumberKey'];
        
    //validasi kalo status gk menunggu gk bisa edit 
    if (!empty($pkey)){
        $rs = $this->getDataRowById($pkey);
        if ($rs[0]['statuskey'] == 3 || $rs[0]['statuskey'] == 4){
            $this->addErrorList($arrayToJs,false,$this->errorMsg[212]);
        }
    }  
        
    //$arrSN = array_column($rsSN, 'serialnumber');    

    if(empty($supplierkey)){
        $this->addErrorList($arrayToJs,false,$this->errorMsg['supplier'][1]);
    }
        
    if(empty($refkey)){
        $this->addErrorList($arrayToJs,false,$this->errorMsg['reference'][1]);
    }else{
        $rsVendorClaimDetail = $vendorWarrantyClaim->getDetailById($refkey);
        for($i=0;$i<count($rsVendorClaimDetail);$i++){
            
            array_push($arrItemVendor,$rsVendorClaimDetail[$i]['itemkey']); 
            $rsSNVendor = $vendorWarrantyClaim->getSerialNumber($rsVendorClaimDetail[$i]['pkey']);
            if(empty($rsSNVendor))
                continue;
            if(USE_SN){
                for($j=0;$j<count($rsSNVendor);$j++){
                    if(empty($rsSNVendor[$j]['serialnumber']))
                        continue;
 
                    array_push($arrSN,$rsSNVendor[$j]['serialnumber']);  
                }
            }
        }
        
    }
	
    if ($arr['selFromWarehouseKey'] == $arr['selToWarehouseKey']){  
        $this->addErrorList($arrayToJs,false,$this->errorMsg['warehouseTransfer'][2]); 
    }

    

    $arrDetailKeys = array(); 

    for($i=0;$i<count($arrOldItemkey);$i++) {
        if (empty($arrOldItemkey[$i]) ){ 
            $this->addErrorList($arrayToJs,false, $this->errorMsg['item'][1]); 	
        } else{
            
            //if((empty($arrItemkey[$i]) || empty($arrVendorPartNumberKey[$i])) && $this->unFormatNumber($arrSubtotal[$i]) <= 0)
                //$this->addErrorList($arrayToJs,false, $this->errorMsg['item'][1]); 
            if($this->unFormatNumber($arrSubtotal[$i]) <= 0){
                if(empty($arrItemkey[$i]))
                    $this->addErrorList($arrayToJs,false, $this->errorMsg['item'][1]); 
                
                if(empty($arrVendorPartNumberKey[$i]))
                    $this->addErrorList($arrayToJs,false, $this->errorMsg['vendorPartNumber'][1]); 
                
            }

            $rsItem = $item->getDataRowById($arrItemkey[$i]);
            $rsOldItem = $item->getDataRowById($arrOldItemkey[$i]);

            if ($this->unFormatNumber($arrQty[$i]) <= 0) 
                $this->addErrorList($arrayToJs,false,$rsItem[0]['name']. '. ' . $this->errorMsg[500]); 
            
            if(!in_array($arrOldItemkey[$i],$arrItemVendor))
                $this->addErrorList($arrayToJs,false,$rsOldItem[0]['name']. '. ' .$this->errorMsg['vendorWarrantyClaim'][3]); 
            
            
            if(USE_SN){
                $arrOLDSN = array(); 
                $arrSNItem =  preg_split('/[\n, ]+/', $arrSerialNumber[$i]);
                $arrSNItemReplacement =  preg_split('/[\n, ]+/', $arrSerialNumberReplacement[$i]);
                if(count($arrSNItem)<>count($arrSNItemReplacement) && $this->unFormatNumber($arrSubtotal[$i]) <= 0)
                    $this->addErrorList($arrayToJs,false,$rsItem[0]['name']. '. ' . $this->errorMsg['serialnumber'][2]); 
                
                
                // SN REPLACEMENT ARUS ADA KALO BKN CN
                if($arrSubtotal[$i]<=0 && empty($arrSerialNumberReplacement[$i]))
                    $this->addErrorList($arrayToJs,false,$this->errorMsg['vendorWarrantyClaimReturn'][2]);
       
                // SN ASAL
                for($j=0;$j<count($arrSNItem);$j++){
                    if(empty($arrSNItem[$j]))
                        $this->addErrorList($arrayToJs,false,$this->errorMsg['serialnumber'][2]);
 
                    $sn = preg_replace("/[^A-Za-z0-9]/", '', $arrSNItem[$j]); 
                    
                    if(!in_array($sn,$arrSN))
                        $this->addErrorList($arrayToJs,false,$sn. '. ' .$this->errorMsg['vendorWarrantyClaim'][4]); 

                    // cek ada yg double gk
                    if (in_array($sn,$arrOLDSN)) 
                        $this->addErrorList($arrayToJs,false, $sn.'. '.$this->errorMsg['serialnumber'][3]); 	 
                     else  
                        array_push($arrOLDSN, $sn);
                      
                }
                 
                // SN REPLACEMENT
                // cek ada yg double gk SN Replacement
                $arrSNReplace = array(); 
                if($arrSubtotal[$i]<=0){ 
                    for($j=0;$j<count($arrSNItemReplacement);$j++){
                        $snReplacement = preg_replace("/[^A-Za-z0-9]/", '', $arrSNItemReplacement[$j]); 
                        if (in_array($snReplacement,$arrSNReplace)) 
                           $this->addErrorList($arrayToJs,false, $snReplacement.'. '.$this->errorMsg['serialnumber'][3]); 	 
                        else  
                           array_push($arrSNReplace, $snReplacement); 
                    } 
                } 
            }
            
            

            // cek punya konversi unit utk satuan yg dipilih gk  
            $conv = $item->getConvMultiplier($arrItemkey[$i],$arrSelUnit[$i]);
            /*iif (empty($conv)) 
                $this->addErrorList($arrayToJs,false,$rsItem[0]['name']. '. ' . $this->errorMsg['itemUnitConversion'][3]); */ 

            // cek ada detail double gk
            if($arrSubtotal[$i]<=0){
                if($showVendorPartNumber && !empty($arrVendorPartNumberKey[$i])){
                    if (in_array( array($arrItemkey[$i], $arrVendorPartNumberKey[$i]),$arrDetailKeys)) { 
                        //$this->addErrorList($arrayToJs,false, $rsItem[0]['name'].'. '.$this->errorMsg[215]); 	 
                    }else { 
                        array_push($arrDetailKeys, array($arrItemkey[$i], $arrVendorPartNumberKey[$i]));
                    }
                    
                    // cek itemkey sesuai gk dengan partnumber
                    if(!empty($arrVendorPartNumberKey[$i])){ 
                        $rsVendor = $item->getVendorPartNumber($arrItemkey[$i], 'and '.$this->tableVendorPartNumber.'.pkey ='.$this->oDbCon->paramString($arrVendorPartNumberKey[$i]));
                        if(empty($rsVendor))
                            $this->addErrorList($arrayToJs,false,$rsItem[0]['name']. '. ' . $this->errorMsg['vendorPartNumber'][3]); 
                    }


                }else{
                    if (in_array($arrItemkey[$i],$arrDetailKeys)) { 
                        //$this->addErrorList($arrayToJs,false, $rsItem[0]['name'].'. '.$this->errorMsg[215]); 	 
                    }else  { 
                        array_push($arrDetailKeys, $arrItemkey[$i]);
                    }
                    
                }

            }

        } 
 
    } 

    return $arrayToJs;
    }
    
     
    function validateCancel($rsHeader,$autoChangeStatus=false){ 
        $id = $rsHeader[0]['pkey']; 
     }
    
 
    function validateConfirm($rsHeader){
  
        $id = $rsHeader[0]['pkey'];
        $warehouse = new Warehouse();  
        $coaLink = new COALink();

        $rsDetail = $this->getDetailById($id);

        //validasi stock
        $itemMovement = new itemMovement();
        for($i=0;$i<count($rsDetail);$i++){
             $saldoakhir = $itemMovement->getItemQOH($rsDetail[$i]['olditemkey'], $rsHeader[0]['fromwarehousekey']);  
             $totalqty = $saldoakhir - $rsDetail[$i]['qtyinbaseunit'];  

            $item = new Item();

            if($totalqty < 0){ 
                $rsItem = $item->getDataRowById($rsDetail[$i]['olditemkey']);
                $this->addErrorLog(false,'<strong>'.$rsItem[0]['name'].'</strong>. '.$this->errorMsg[402]);
            }else{ 
  
                if(USE_SN && $rsHeader[0]['isfullreceive'] == 1){ 
                    $rsItem = $item->getDataRowById($rsDetail[$i]['itemkey']); 
                    if(!$rsItem[0]['needsn'])
                        continue;
                    
                    $rsSN = $this->getSerialNumber($rsDetail[$i]['pkey']); 

                    if($rsDetail[$i]['qtyinbaseunit'] <> count($rsSN))
                        $this->addErrorLog(false, $this->errorMsg['serialnumber'][2]); 

                    //validasi SN
                    for($j=0;$j<count($rsSN); $j++){
                        $saldoakhir = $itemMovement->getItemSNQOH($rsDetail[$i]['olditemkey'], $rsSN[$j]['serialnumber'], $rsHeader[0]['fromwarehousekey']); 
                        $totalqty = $saldoakhir - 1;
                        if($totalqty<0) 
                            $this->addErrorLog(false,'<strong>'.$rsItem[0]['name'].', '. $rsSN[$j]['serialnumber'].'</strong>. '.$this->errorMsg[402]);
                        
                        if($rsDetail[$i]['total']>0 && !empty($rsSN[$j]['serialnumberreplacement']))
                            $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '. $this->errorMsg['serialnumber'][7]);

                    } 
                } 
            }
        } 
        
         if (USE_GL){
            $arrCOA = array();
            array_push($arrCOA, 'hpp' , 'inventory' ); 
            for ($i=0;$i<count($arrCOA);$i++){
                $rsCOA = $coaLink->getCOALink ($arrCOA[$i], $warehouse->tableName,$rsHeader[0]['fromwarehousekey'], 0); 
                if (empty($rsCOA))	
                    $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '. $this->errorMsg['coa'][3]);
            } 
         }


    }
    
    
    function confirmTrans($rsHeader){ 
        $id = $rsHeader[0]['pkey'];  
        
        $item = new Item();
        $vendorWarrantyClaim = new VendorWarrantyClaim();
        $warrantyPeriod = new WarrantyPeriod();
        $itemMovement = new ItemMovement();  
        $employee = new Employee();
        $customer = new Customer();
        $supplier = new Supplier();
        $arrNote = array();
        array_push($arrNote, $rsHeader[0]['code']); 
          
        
        $recipientName = '';
        if(!empty($rsHeader[0]['recipientname']) || !empty($rsHeader[0]['employeekey'])){  
            
            if(!empty($rsHeader[0]['recipientname'])){
               $recipientName = $rsHeader[0]['recipientname']; 
            }else{
                $rsSupplier = $supplier->getDataRowById($rsHeader[0]['supplierkey']);
                $recipientName = $rsSupplier[0]['name'];  
            }
                
        }
        $recipient = (!empty($recipientName)) ? ' ' .$this->lang['for'] . ' ' .$recipientName : ''; 
        
        //IF HAS CUSTOMER
        $customerName = '';
        /*if(!empty($rsHeader[0]['supplierkey']) || !empty($rsHeader[0]['supplierkey'])){   
            $rsCustomer = $customer->getDataRowById($rsHeader[0]['supplierkey']);
            $customerName = $rsCustomer[0]['name'];   
        }
        $customer = (!empty($customerName)) ? ' ' .$this->lang['for'] . ' ' .$customerName : ''; */
        $customer = '';
        
        array_push($arrNote, $this->ucFirst($this->lang['reclaimVendor']).$recipient);
        
        
        if(!empty($rsHeader[0]['refcode']))
            array_push($arrNote, $rsHeader[0]['refcode']); 
        
        $note = implode('. ', $arrNote).'.';
        
        $this->updateConvMultiplier($id); 
        $this->updateCOGS($id);

        $rsDetail = $this->getDetailById($id);  
        
        // kalo pake warranty
         if(USE_SN){
            $rsWarranty = $warrantyPeriod->searchData();
            $rsWarranty = array_column($rsWarranty,'period', 'pkey');
            $rsWarranty[0] = 0; // default kalo gk ad warranty
         }

        for($i=0;$i<count($rsDetail); $i++){	 
           $rsItem = $item->getDataRowById($rsDetail[$i]['itemkey']);
            
           $itemMovement->updateItemMovement($id,$rsDetail[$i]['olditemkey'],-$rsDetail[$i]['qtyinbaseunit'], $rsDetail[$i]['costinbaseunit'] ,$this->tableName, $rsHeader[0]['fromwarehousekey'], $note, $rsHeader[0]['trdate']);
	       if($rsDetail[$i]['total']<=0)
           	$itemMovement->updateItemMovement($id,$rsDetail[$i]['itemkey'],$rsDetail[$i]['qtyinbaseunit'], $rsDetail[$i]['costinbaseunit'] ,$this->tableName, $rsHeader[0]['towarehousekey'], $note, $rsHeader[0]['trdate']);

            if(USE_SN){
                // set warranty enddate
                $warrantyMonth = $rsWarranty[$rsItem[0]['warrantyperiodkey']];

                $date = new DateTime($rsHeader[0]['trdate']);
                $date->add(new DateInterval('P'.$warrantyMonth.'M'));
                $warrantyEndDate = $date->format('d / m / Y'); 


               $rsSN = $this->getSerialNumber($rsDetail[$i]['pkey']); 
               
                
               for($j=0;$j<count($rsSN); $j++){ 
                   
                    $rsSNINfo = $item->getSNInformation($rsSN[$j]['serialnumber']);
                    //$rsDetail[$i]['vendorpartnumberkey'] = $rsSN[$j]['vendorpartnumberkey'];
                     
                    $arrMovementParam =  array(
                                            'refkey' => $rsDetail[$i]['pkey'],
                                            'refheaderkey' => $id,
                                            'itemkey' => $rsDetail[$i]['olditemkey'],
                                            'vendorpartnumberkey' =>  $rsSN[$j]['vendorpartnumberkey'], 
                                            'sn' => $rsSN[$j]['serialnumber'],
                                            'qtyinbaseunit' => -1,
                                            'costinbaseunit' => $rsDetail[$i]['costinbaseunit'],
                                            'reftable' => $this->tableName,
                                            'warehousekey' => $rsHeader[0]['fromwarehousekey'],
                                            'note' => $note,
                                            'trdate' => $rsHeader[0]['trdate'],
                                            'warrantyvendorperiodkey' => $rsSNINfo[0]['warrantyvendorperiodkey'],
                                            'warrantyvendorperiodtime' => $rsSNINfo[0]['warrantyvendorperiod'],
                                            'warrantyvendorperiodexpireddate' => $this->formatDBDate($rsSNINfo[0]['warrantyvendorperiodexpireddate']),
                                            'warrantyperiodkey' =>  $rsSNINfo[0]['warrantyperiodkey'],
                                            'warrantyperiodtime' =>  $rsSNINfo[0]['warrantyperiod'],
                                            'warrantyperiodexpireddate' =>  $this->formatDBDate($rsSNINfo[0]['warrantyperiodexpireddate']), 
                                        ) ;
                    
                    $itemMovement->updateItemSNMovement($arrMovementParam);
                    
                   
                    // kalo gk ad nilainya. berarti ganti item
                    if($rsDetail[$i]['total']<=0){
                        $arrMovementParam['qtyinbaseunit'] = 1;
                        $arrMovementParam['warehousekey'] =  $rsHeader[0]['towarehousekey']; 
                        $arrMovementParam['sn'] =  $rsSN[$j]['serialnumberreplacement'];
                        $arrMovementParam['itemkey'] =  $rsDetail[$i]['itemkey'];
                        $arrMovementParam['vendorpartnumberkey'] = $rsDetail[$i]['vendorpartnumberkey'];  

                        $itemMovement->updateItemSNMovement($arrMovementParam); 
                    }
                     
               }

            }

        }	

        //update jurnal umum 
        $this->updateGL($rsHeader);
    } 
    

    function updateGL($rsHeader){
        if (!USE_GL) return;

         $warehouse = new Warehouse();
         $coaLink = new COALink();
         $item = new Item();
         $generalJournal = new GeneralJournal();
         $employee = new Employee();
         $rsKey = $generalJournal->getTableKeyAndObj($this->tableName);

         $arr = array();
         $arr['pkey'] = $generalJournal->getNextKey($generalJournal->tableName);
         $arr['code'] = 'xxxxx';
         $arr['refkey'] = $rsHeader[0]['pkey'];
         $arr['refTableType'] = $rsKey['key'];
         $arr['trDate'] =  $this->formatDBDate($rsHeader[0]['trdate'],'d / m / Y'); 
         $arr['createdBy'] = 0; 
         
        // ============= DESCRIPTION 
         $desc = array(); 
                 
         $recipientName = '';
         if (!empty($rsHeader[0]['recipientname'])){
            $recipientName = $rsHeader[0]['recipientname'];
         }else{
            $rsEmployee = $employee->getDataRowById($rsHeader[0]['employeekey']);
            $recipientName = $rsEmployee[0]['name'];
         }
         
         $recipient = (!empty($recipientName)) ? ' ' .$this->lang['for'] . ' ' .$recipientName : '';
        
         array_push($desc, $this->ucFirst($this->lang['itemOut']).$recipient);
        
         // refcode
         if (!empty($rsHeader[0]['refcode'])) array_push($desc, $rsHeader[0]['refcode']);
          
		 $arr['trDesc'] = implode('. ',$desc) .'.'; 

        // ============= DESCRIPTION 
        
        
        $totalHPP = 0 ;
        $rsDetail = $this->getDetailById($rsHeader[0]['pkey']); 
        for($i=0;$i<count($rsDetail);$i++)
            $totalHPP += ( $rsDetail[$i]['costinbaseunit']  * $rsDetail[$i]['qtyinbaseunit']);

        $temp = -1;

        $rsCOA = $coaLink->getCOALink ('hpp', $warehouse->tableName,$rsHeader[0]['warehousekey'], 0);
        $temp++;
        $arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];
        $arr['debit'][$temp] = $totalHPP; 
        $arr['credit'][$temp] = 0;  

        $rsCOA = $coaLink->getCOALink ('inventory', $warehouse->tableName,$rsHeader[0]['warehousekey'], 0);
        $temp++;
        $arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];
        $arr['debit'][$temp] = 0; 
        $arr['credit'][$temp] = $totalHPP;  

        $arrayToJs = $generalJournal->addData($arr);

        if (!$arrayToJs[0]['valid'])
                throw new Exception('<strong>'.$rsHeader[0]['code'] . '</strong>. '.$this->errorMsg[504].' '.$arrayToJs[0]['message']); 
    }

    function cancelTrans($rsHeader,$copy){ 
        $id = $rsHeader[0]['pkey'];

        $itemMovement = new ItemMovement();  
        $itemMovement->cancelMovement($id,$this->tableName); 
        $itemMovement->cancelSNMovement($id,$this->tableName);


        if ($copy)
            $this->copyDataOnCancel($id);	  

        $this->cancelGLByRefkey($rsHeader[0]['pkey'],$this->tableName);
    } 
    

    function getDetailWithRelatedInformation($pkey,$criteria=''){
        $sql = 'select
                '.$this->tableNameDetail .'.*,
                '.$this->tableItem.'.name as itemname, 
                '.$this->tableItem.'.code as itemcode,
                '.$this->tableVendorPartNumber.'.pkey as vendorpartnumberkey,
                '.$this->tableVendorPartNumber.'.partnumber,
                '.$this->tableItem.'.needsn,
                '.$this->tableItemUnit.'.name as unitname,
                baseunit.name as baseunitname 
              from
                '. $this->tableNameDetail .'
                    left join '.$this->tableVendorPartNumber.' on 
                    '.$this->tableNameDetail .'.vendorpartnumberkey =  '.$this->tableVendorPartNumber.'.pkey ,
                '.$this->tableItem.',
                '.$this->tableItemUnit.',
                '.$this->tableItemUnit.' baseunit
              where
                ' . $this->tableNameDetail .'.olditemkey = '.$this->tableItem.'.pkey and
                ' . $this->tableNameDetail .'.unitkey = '.$this->tableItemUnit.'.pkey and
			  	' . $this->tableItem .'.baseunitkey = baseunit.pkey and
                '.$this->tableNameDetail .'.refkey = '.$this->oDbCon->paramString($pkey);

        $sql .= $criteria; 

        return $this->oDbCon->doQuery($sql); 
    }

    
    
    function generateDefaultQueryForAutoComplete($returnField){ 
        $sql = 'select
                '.$returnField['key'].',
                '.$returnField['value'].' as value  
            from 
                '.$this->tableName . ',
                '.$this->tableStatus.'  
            where  		
                '.$this->tableName . '.statuskey = '.$this->tableStatus.'.pkey and
                '.$this->tableName . '.statuskey = 2
        ';
        
        $sql .=  $this->getCompanyCriteria() ;
        return $sql;
        
    }
    
    function normalizeParameter($arrParam, $trim = false){ 
        $item = new Item();
        
        $arrParam = parent::normalizeParameter($arrParam); 
 
        $arrParam['hidRefKey'] = (isset($arrParam['hidRefKey']) && !empty($arrParam['hidRefKey'])) ?  $arrParam['hidRefKey'] : 0;
        $arrParam['hidSupplierKey'] = (isset( $arrParam['hidSupplierKey'])) ?  $arrParam['hidSupplierKey'] : 0;
        $arrParam['hidEmployeeKey'] = (isset( $arrParam['hidEmployeeKey'])) ?  $arrParam['hidEmployeeKey'] : 0;
        $arrParam['recipientName'] = (isset( $arrParam['recipientName'])) ?  $arrParam['recipientName'] : '';
        
        $arrParam['chkIsInternal'] = (isset($arrParam['chkIsInternal'])) ? $arrParam['chkIsInternal'] : 0; 
        if($arrParam['chkIsInternal'] == 0)
            $arrParam['hidEmployeeKey'] = 0; 
        else
            $arrParam['recipientName'] = '';
      
        $arrParam['refCode'] = (isset($arrParam['refCode'])) ?  $arrParam['refCode'] : '';
        $arrParam['reftabletype'] = (isset($arrParam['reftabletype']) && !empty($arrParam['reftabletype'])) ? $arrParam['reftabletype'] : 0;
	   
        $arrParam['chkIsFullReceive'] = (isset($arrParam['chkIsFullReceive'])) ? $arrParam['chkIsFullReceive'] : 1;

        if( $arrParam['chkIsFullReceive'] == 0){
            for($i=0;$i<count($arrParam['snList']);$i++)
                $arrParam['snList'][$i] = ''; 
        }
        
        for($i=0;$i<count($arrParam['hidItemKey']);$i++){
            $arrParam['snList'][$i] = (isset($arrParam['snList'][$i]) && !empty($arrParam['snList'][$i])) ?  $arrParam['snList'][$i]: '';
        }
        
        
        // remove SN yg baris kosong 
        foreach($arrParam['snReplacementList'] as $key=>$row)
            $arrParam['snReplacementList'][$key] = implode(',',$item->normalizeSNList($row));
         
        foreach($arrParam['snList'] as $key=>$row)
            $arrParam['snList'][$key] = implode(',',$item->normalizeSNList($row));
     
        return $arrParam; 
    }
 
    function updateDetailTablesOnCopy($id,$newPkey, $arrTableDetail){ 
         
        for($k=0;$k<count($arrTableDetail);$k++){
            $rsDetail = $this->getDetailById($id,'','',$arrTableDetail[$k]);

            $sql = 'show columns from ' . $arrTableDetail[$k] ;   
            $rsColumnsName = $this->oDbCon->doQuery ($sql); 

            for ($j=0;$j<count($rsDetail);$j++){
                $fields = '';
                $data = ''; 
                $oldDetailKey = $rsDetail[$j]['pkey'];
                
                if ($arrTableDetail[$k] == $this->tableNameDetail)  
                     $rsDetail[$j]['pkey'] = $this->getNextKey($this->tableNameDetail);  
              
                $rsDetail[$j]['refkey'] = $newPkey; 
                
                for ($i=1;$i<count($rsColumnsName);$i++){

                    $fields .= $rsColumnsName[$i]['Field'];  
                    $data .=   $this->oDbCon->paramString($rsDetail[$j][$rsColumnsName[$i]['Field']]);

                    if ($i <> count($rsColumnsName) - 1){
                      $data .= ',';   
                      $fields.= ',';    
                    }

                }

                $sql = 'insert into ' .$arrTableDetail[$k].'  ('.$fields.') values ('.$data.')'; 
                $this->oDbCon->execute ($sql);	
                
  
                // ============= update detail Package
                
                $rsItemDetail = $this->getSerialNumber($oldDetailKey);
                $sql = 'show columns from ' . $this->tableSerial;   
                $rsDetailsColumnsName = $this->oDbCon->doQuery($sql); 
                 
               for ($z=0;$z<count($rsItemDetail);$z++){
                    $fields = '';
                    $data = ''; 

                    for ($i=1;$i<count($rsDetailsColumnsName);$i++){

                        $fields .= $rsDetailsColumnsName[$i]['Field'];

                        $rsItemDetail[$z]['refheaderkey'] = $newPkey;
                        $rsItemDetail[$z]['refkey'] = $rsDetail[$j]['pkey']; 

                        $data .= $this->oDbCon->paramString($rsItemDetail[$z][$rsDetailsColumnsName[$i]['Field']]);

                        if ($i <> count($rsDetailsColumnsName) - 1){
                          $data .= ',';   
                          $fields.= ',';    
                        }

                    }

                    $sql = 'insert into ' .$this->tableSerial.'  ('.$fields.') values ('.$data.')';  
                    $this->oDbCon->execute ($sql);	 
               }
                
                
                // ============= end update detail Package
                
            }  
        }  
        
    }
    
    
   
    function updateCOGS($id){
        $item = new Item();
        $rsDetail = $this->getDetailById($id); 
        for($i=0;$i<count($rsDetail); $i++){
            $rsItem = $item->getDataRowById($rsDetail[$i]['itemkey']); 
            $sql = 'update '. $this->tableNameDetail .' set costinbaseunit = '.$this->oDbCon->paramString($rsItem[0]['cogs']).' where pkey = ' .$this->oDbCon->paramString($rsDetail[$i]['pkey']);
            $this->oDbCon->execute($sql); 
        }
    }

  
    function updateItemFromReclaim($pkey){ 
            $itemReclaimVendor = new ItemReclaimVendor();
            $rsHeader = $this->getDataRowById($pkey);  
            $rsDetail = $this->getDetailById($pkey); 

            for($i=0;$i<count($rsDetail); $i++){	
                $sql = 'select 
                        coalesce(sum(qtyinbaseunit),0) as totalqty
                    from 
                        '. $itemReclaimVendor->tableName . ', '. $itemReclaimVendor->tableNameDetail . '
                    where 
                         '. $itemReclaimVendor->tableName . '.pkey = '. $itemReclaimVendor->tableNameDetail . '.refkey and
                         '. $itemReclaimVendor->tableName . '.refkey = '. $this->oDbCon->paramString($pkey) .' and
                         '. $itemReclaimVendor->tableNameDetail . '.itemkey = ' . $rsDetail[$i]['itemkey'] .' and 
                         '. $itemReclaimVendor->tableNameDetail . '.refdetailkey = ' . $rsDetail[$i]['pkey'] .' and 
                         (statuskey = 2 or statuskey = 3)';
                
//                $this->setLog($sql);
                $rsTotal = $this->oDbCon->doQuery($sql);

                $sql = 'update 
                            ' . $this->tableNameDetail.' 
                        set  
                            qtyclaimed = '. $rsTotal[0]['totalqty'].' ,
                            qtyoutstanding = (' . $this->tableNameDetail.'.qtyinbaseunit - '. $rsTotal[0]['totalqty'].') 
                        where 
                            refkey = '.$pkey.' and 
                            pkey = '.$rsDetail[$i]['pkey'].' and 
                            itemkey = ' . $rsDetail[$i]['itemkey'];
                $this->oDbCon->execute($sql); 
//                $this->setLog($sql);
            }

            
            $sql = 'select * from ' . $this->tableNameDetail.' where refkey = '.$this->oDbCon->paramString($pkey).' and  qtyclaimed < qtyinbaseunit';
            $rs = $this->oDbCon->doQuery($sql);

            $statuskey = (empty($rs)) ? 3 : 2;
 
            if ($rsHeader[0]['statuskey'] <> $statuskey)
                $this->changeStatus($pkey,$statuskey);
 
      
    }
    
  
}
?>
