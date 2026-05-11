<?php
  
class PurchaseReceive extends BaseClass{  
 
    function __construct(){

        parent::__construct(); 

        $this->tableName = 'purchase_receive_header';
        $this->tableNameDetail = 'purchase_receive_detail';
        $this->tablePurchaseOrderHeader = 'purchase_order_header';   
        $this->tablePurchaseOrderDetail = 'purchase_order_detail';
        $this->tableStatus = 'transaction_status';
        $this->tableMovement = 'item_movement'; 
        $this->tableSupplier = 'supplier';
        $this->tableHistory = 'history'; 
        $this->tablePayment= 'purchase_receive_payment';
        $this->tableItem = 'item'; 	
        $this->tableItemUnit = 'item_unit'; 	
        $this->tableWarehouse = 'warehouse'; 	
		$this->isTransaction = true; 	
 

        $this->securityObject = 'PurchaseReceive'; 
        
        $this->arrDataDetail = array();  
        $this->arrDataDetail['pkey'] = array('hidDetailKey');
        $this->arrDataDetail['refkey'] = array('pkey','ref');
        $this->arrDataDetail['refpodetailkey'] = array('hidPODetailKey');
        $this->arrDataDetail['itemkey'] = array('hidItemKey');
        $this->arrDataDetail['orderedqtyinbaseunit'] = array('orderedQtyInBaseUnit','number'); 
        $this->arrDataDetail['baseunitkey'] = array('baseunitkey'); 
        $this->arrDataDetail['qtyminusinbaseunit'] = array('qtyMinusInBaseUnit','number');
        $this->arrDataDetail['receivedqtyinbaseunit'] = array('receivedQtyInBaseUnit','number');
        $this->arrDataDetail['costinbaseunit'] = array('costinbaseunit','number');
 
        $this->arrPaymentDetail = array(); 
        $this->arrPaymentDetail['pkey'] = array('hidDetailPaymentKey');
        $this->arrPaymentDetail['refkey'] = array('pkey', 'ref');
        $this->arrPaymentDetail['amount'] = array('paymentMethodValue',array('datatype' => 'number','mandatory'=>true));
        $this->arrPaymentDetail['paymentkey'] = array('selPaymentMethod',array('mandatory'=>true)); 

        $arrDetails = array();
        array_push($arrDetails, array('dataset' => $this->arrDataDetail));
        array_push($arrDetails, array('dataset' => $this->arrPaymentDetail, 'tableName' => $this->tablePayment));
       
        $this->arrData = array(); 
        $this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails));
        $this->arrData['code'] = array('code');
        $this->arrData['trdate'] = array('trDate','date');
        $this->arrData['refkey'] = array('hidPurchaseOrderKey');
        $this->arrData['warehousekey'] = array('selWarehouse');
        $this->arrData['supplierkey'] = array('hidSupplierKey');
        $this->arrData['trdesc'] = array('trDesc');
        $this->arrData['shipmentfee'] = array('shipmentFee','number');
        $this->arrData['termofpaymentkey'] = array('selTermOfPaymentKey');
        $this->arrData['balance'] = array('balance','number'); 
        $this->arrData['totalpayment'] = array('totalPayment','number'); 
        $this->arrData['statuskey'] = array('selStatus');
               
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'date','title' => 'date','dbfield' => 'trdate','default'=>true, 'width' => 100, 'align' =>'center','format' => 'date'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'poCode','title' => 'poCode','dbfield' => 'purchaseordercode','default'=>true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'supplier','title' => 'supplier','dbfield' => 'suppliername','default'=>true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'warehouse','title' => 'warehouse','dbfield' => 'warehousename','default'=>true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
        array_push($this->arrDataListAvailableColumn, array('code' => 'desc','title' => 'note','dbfield' => 'trdesc',  'width' => 200));
      
        $this->printMenu = array();
        array_push($this->printMenu,array('code' => 'printTransaction', 'name' => $this->lang['printTransaction'],  'icon' => 'print', 'url' => 'print/purchaseReceive'));
        
		$this->includeClassDependencies(array(

              'AP.class.php',
              'CashBank.class.php',
              'COALink.class.php',
              'GeneralJournal.class.php',
              'Item.class.php',
              'ItemUnit.class.php',
              'ItemMovement.class.php',
              'Marketplace.class.php',
              'PaymentMethod.class.php', 
              'CashMovement.class.php',
              'Supplier.class.php',
              'TermOfPayment.class.php',
              'PurchaseOrder.class.php',

        ));         
       
		
        $this->overwriteConfig();
    }

    function getQuery(){

       return '
            SELECT 
                 '.$this->tableName.'.* , 
                 '.$this->tablePurchaseOrderHeader.'.code as purchaseordercode, 
                 '.$this->tableSupplier.'.name as suppliername,
                 '.$this->tableStatus.'.status as statusname,
                 '.$this->tableWarehouse.'.name as warehousename
            FROM 
                '.$this->tableStatus.', 
                '.$this->tableName.', 
                '.$this->tablePurchaseOrderHeader.',
                '.$this->tableSupplier.',
                '.$this->tableWarehouse.'
            WHERE 
                  '.$this->tableName.'.refkey = '.$this->tablePurchaseOrderHeader.'.pkey and
                  '.$this->tableName.'.warehousekey = '.$this->tableWarehouse.'.pkey and
                  '.$this->tablePurchaseOrderHeader.'.supplierkey = '.$this->tableSupplier.'.pkey and
                  '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey  
        ' .$this->criteria ;  

    }

    
     function validateForm($arr,$pkey = ''){
        $item = new Item();  
        $purchaseOrder = new PurchaseOrder();

        $arrayToJs = parent::validateForm($arr,$pkey); 

        $purchaseOrderKey = $arr['hidPurchaseOrderKey']; 
        $arrItemkey = $arr['hidItemKey'];  
        $arrQtyMinusInBaseUnit = $arr['qtyMinusInBaseUnit']; 
        $arrReceivedQtyInBaseUnit = $arr['receivedQtyInBaseUnit']; 
        $shipmentFee =  $arr['shipmentFee']; 
            
        //validasi kalo status gk menunggu gk bisa edit 
        if (!empty($pkey)){
            $rs = $this->getDataRowById($pkey);
            if ($rs[0]['statuskey'] <> 1){
                $this->addErrorList($arrayToJs,false,$this->errorMsg[212]);
            }
        } 
 
        // kurir harus diisi kalo ad ongkir
        if($shipmentFee > 0){ 
		  $supplierkey = $arr['hidSupplierKey']; 
          if(empty($supplierkey)) 
			$this->addErrorList($arrayToJs,false,$this->errorMsg['supplier'][1]); 
        }
            
        if(empty($purchaseOrderKey)){
            $this->addErrorList($arrayToJs,false,$this->errorMsg['purchaseOrder'][1]);
        }else{
             $rsPurchaseOrder = $purchaseOrder->getDataRowById($purchaseOrderKey); 
             $receivedate = strtotime(str_replace('\'','',$this->oDbCon->paramDate($_POST['trDate'],' / ','Y-m-d')));
             $podate =  strtotime($rsPurchaseOrder[0]['trdate']);

            if ($receivedate < $podate)
             $this->addErrorList($arrayToJs,false, $this->errorMsg['purchaseReceive'][2]);

        } 

        for($i=0;$i<count($arrItemkey);$i++) { 
            if (empty($arrItemkey[$i]) ){ 
                $this->addErrorList($arrayToJs,false, $this->errorMsg['item'][1]); 	
            }
            if (!empty($arrItemkey[$i]) &&  $this->unFormatNumber($arrReceivedQtyInBaseUnit[$i]) > $this->unFormatNumber($arrQtyMinusInBaseUnit[$i]) ){
                $rsItem = $item->getDataRowById($arrItemkey[$i]);
                $this->addErrorList($arrayToJs,false,$rsItem[0]['name']. '. ' . $this->errorMsg['purchaseReceive'][1]); 
            }
        }
 
        return $arrayToJs;
     }
 

    function afterStatusChanged($rsHeader){ 
        $purchaseOrder = new PurchaseOrder();
        $purchaseOrder->updatePurchaseOrderReceivedItem($rsHeader[0]['refkey']);
         
        // retrieve latest status
        $rsHeader = $this->getDataRowById($rsHeader[0]['pkey']);
        if ($rsHeader[0]['statuskey'] == 2)
            $this->changeStatus($rsHeader[0]['pkey'],3,'',false,true);
    }
      
    function validateConfirm($rsHeader){
        $id = $rsHeader[0]['pkey'];
        
        $purchaseOrder = new purchaseOrder();
        $warehouse = new Warehouse();
        $coaLink = new COALink();
        $termOfPayment = new TermOfPayment();
 
        $rsDetail = $this->getDetailById($id);

        $rsPayment = $this->getPaymentMethodDetail($id);  
        $rsTOP = $termOfPayment->getDataRowById($rsHeader[0]['termofpaymentkey']); 
        $isCash = ($rsTOP[0]['duedays'] == 0) ? true : false; 
  
        $totalPayment = 0; 
        for($i=0;$i<count($rsPayment); $i++)
            $totalPayment += $rsPayment[$i]['amount']; 
        
        $balance = $totalPayment - $rsHeader[0]['shipmentfee']; 
        
         if (USE_GL){
                $arrCOA = array();
                array_push($arrCOA, 'inventorytemp' , 'inventory' ); 
                for ($i=0;$i<count($arrCOA);$i++){
                    $rsCOA = $coaLink->getCOALink ($arrCOA[$i], $warehouse->tableName,$rsHeader[0]['warehousekey'], 0); 
                    if (empty($rsCOA))	
                        $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '. $this->errorMsg['coa'][3]);
                }  
             
                if ($isCash){
                    $rsPayment = $this->getPaymentMethodDetail($id);  
                    for($i=0;$i<count($rsPayment); $i++){ 
                        if ($rsPayment[$i]['amount'] > 0 ){ 
                            $rsCOA = $coaLink->getCOALink ('payment', $warehouse->tableName,$rsHeader[0]['warehousekey'], $rsPayment[$i]['paymentkey']); 
                            if (empty($rsCOA))	
                                $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg['coa'][3]);
                        }
                    }      
                }else{ 

                     $rsCOA = $coaLink->getCOALink ('ap', $warehouse->tableName,$rsHeader[0]['warehousekey'],0); 
                     if (empty($rsCOA))	
                        $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg['coa'][3]);

                } 
        }
 
        if ($isCash){ 
            $thresholdDiscount = abs($this->loadSetting('roundedPaymentThreshold'));
            if($balance < ($thresholdDiscount * -1)) 
                $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg[502]);
            else if ($balance > $thresholdDiscount)
                $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg[509]); 
        }
  
        // cek sudah diterima full blm
        for($i=0;$i<count($rsDetail);$i++){ 
            $qtyMinusInBaseUnit = $rsDetail[$i]['qtyminusinbaseunit']; 

            $rsPO = $purchaseOrder->getDetailById($rsHeader[0]['refkey'],' and itemkey = ' . $this->oDbCon->paramString($rsDetail[$i]['itemkey']) );

            if ($rsPO[0]['qtyinbaseunit'] - $rsPO[0]['receivedqtyinbaseunit'] <> $qtyMinusInBaseUnit ) 
                   $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg['purchaseReceive'][3]);

        }
         
     }
 
    function confirmTrans($rsHeader){
        $id = $rsHeader[0]['pkey'];
         
        $warehouse = new Warehouse(); 
        $purchaseOrder = new PurchaseOrder();
        $coaLink = new COALink(); 
        $supplier = new Supplier();
        
        $rsPurchaseOrderHeader = $purchaseOrder->getDataRowById($rsHeader[0]['refkey']); 

        $rsWarehouse = $warehouse->getDataRowById($rsHeader[0]['warehousekey']);
 
        $rsSupplier = $supplier->getDataRowById($rsPurchaseOrderHeader[0]['supplierkey']); 
        $note = $rsHeader[0]['code'].'. '.$this->ucFirst($this->lang['purchaseReceive']. ' ' .  $this->lang['from']) . ' '.$rsPurchaseOrderHeader[0]['code'].', '. $rsSupplier[0]['name'].'.'; 
        
        $notecash = $rsHeader[0]['code'].'. Kas Keluar dari '.$rsWarehouse[0]['name'].' untuk pemasukan barang';
        $rsDetail = $this->getDetailById($rsHeader[0]['pkey']);

        $termOfPayment = new TermOfPayment();
        $rsTOP = $termOfPayment->getDataRowById($rsHeader[0]['termofpaymentkey']); 
        $isCash = ($rsTOP[0]['duedays'] == 0) ? true : false; 

        // MENGHITUNG PAYMENT
        if($rsHeader[0]['shipmentfee'] > 0){
            
            if ($isCash){
                $rsPayment = $this->getPaymentMethodDetail($id);  

                $cashMovement = new CashMovement();  

                for($i=0;$i<count($rsPayment); $i++){  
                   $rsCOA = $coaLink->getCOALink ('payment', $warehouse->tableName,$rsHeader[0]['warehousekey'],$rsPayment[$i]['paymentkey']); 
                   $cashMovement->updateCashMovement($id, $rsCOA[0]['coakey'],-$rsPayment[$i]['amount'], $this->tableName, $rsHeader[0]['warehousekey'], $notecash,$rsHeader[0]['trdate']);
                }                  
            }
            else{
                //update AP
                $ap = new AP();

                $arrParam = array();	

                $rsAPKey = $ap->getTableKeyAndObj($this->tableName); 
                $arrParam['code'] = 'xxxxxx';
                $arrParam['hidSupplierKey'] =$rsHeader[0]['supplierkey'];
                $arrParam['hidRefKey'] = $id;
                $arrParam['hidRefCode'] =  $rsHeader[0]['code'];
                $arrParam['hidRefTable'] = $rsAPKey['key'];
                $arrParam['amount'] = abs($rsHeader[0]['balance']);
                $arrParam['selWarehouse'] = $rsHeader[0]['warehousekey'];
				$arrParam['trDesc'] = $this->lang['shippingFee'] . ' ' . $rsHeader[0]['code'];
                $arrParam['trDate'] =  $this->formatDBDate($rsHeader[0]['trdate'],'d / m / Y');  
                $arrParam['dueDate'] = date ('d / m / Y', mktime(0, 0, 0, date("m")  , date("d")+$rsTOP[0]['duedays'], date("Y")));
                $arrParam['createdBy'] = 0;
                $arrParam['selAPType'] = 1;
                $arrParam['islinked'] = 1;
                $arrParam['overwriteGL'] = 1;
                
				$arrayToJs = $ap->addData($arrParam);  
                if (!$arrayToJs[0]['valid'])
                    throw new Exception('<strong>'.$rsHeader[0]['code'] . '</strong>. '.$this->errorMsg[201].' '.$arrayToJs[0]['message']);  
            }
        }
 
       
        $itemMovement = new ItemMovement();  
        for($i=0;$i<count($rsDetail); $i++){	 
           if ($rsDetail[$i]['receivedqtyinbaseunit'] != 0)
            $itemMovement->updateItemMovement($id,$rsDetail[$i]['itemkey'],$rsDetail[$i]['receivedqtyinbaseunit'],$rsDetail[$i]['costinbaseunit'],$this->tableName, $rsHeader[0]['warehousekey'], $note,$rsHeader[0]['trdate']);
        }	 


        //update jurnal umum 
        $this->updateGL($rsHeader);
    } 

   
    function updateGL($rs){
        if (!USE_GL) return; 

        $purchaseOrder = new PurchaseOrder();
        $warehouse = new Warehouse();
        $coaLink = new COALink();
        $generalJournal = new GeneralJournal();
        $supplier = new Supplier();
        $item = new Item();
        
        $warehousekey = $rs[0]['warehousekey'];
        
        $rsKey = $generalJournal->getTableKeyAndObj($this->tableName);
        $arr = array();
        $arr['pkey'] = $generalJournal->getNextKey($generalJournal->tableName);
        $arr['code'] = 'xxxxx';
        $arr['refkey'] = $rs[0]['pkey'];
		$arr['refTableType'] = $rsKey['key'];
        $arr['trDate'] =  $this->formatDBDate($rs[0]['trdate'],'d / m / Y');  
        $arr['createdBy'] = 0;
		$arr['selWarehouseKey'] = $rs[0]['warehousekey'];
                 
        $rsPO = $purchaseOrder->getDataRowById($rs[0]['refkey']); 
        $rsSupplier = $supplier->getDataRowById($rsPO[0]['supplierkey']);
		$arr['trDesc'] = $this->ucFirst($this->lang['purchaseReceive']. ' ' .  $this->lang['from']) . ' '.$rsPO[0]['code'].', '. $rsSupplier[0]['name'].'.'; 

        $temp = -1;
        $totalPayment = 0; 

        $rsDetail = $this->getDetailById($rs[0]['pkey']);
 
        $arrItemCOA = array();
        $arrItemTempCOA = array();
            
        for ($i=0;$i<count($rsDetail);$i++) { 
            
            $itemCOAKey = $item->getInventoryCOAKey($rsDetail[$i]['itemkey'],$warehousekey);
            $itemTempCOAKey = $item->getInventoryTempCOAKey($rsDetail[$i]['itemkey'],$warehousekey);
            
/*            $rsPODetail = $purchaseOrder->getDetailByColumn('pkey',$rsDetail[$i]['refpodetailkey']);
            
            $totalItemValue = $rsDetail[$i]['receivedqtyinbaseunit'] * $rsPODetail[0]['priceinbaseunit'];*/
                
            $totalItemValue = $rsDetail[$i]['receivedqtyinbaseunit']  * $rsDetail[$i]['costinbaseunit'];
            
            $arrItemCOA[$itemCOAKey] = (!isset($arrItemCOA[$itemCOAKey])) ? $totalItemValue : $arrItemCOA[$itemCOAKey] + $totalItemValue; 
            $arrItemTempCOA[$itemTempCOAKey] = (!isset($arrItemTempCOA[$itemTempCOAKey])) ? $totalItemValue : $arrItemTempCOA[$itemTempCOAKey] + $totalItemValue; 
        }
        
        foreach ($arrItemCOA as $coakey => $coaValue){   
            $temp++;
            $arr['hidCOAKey'][$temp] = $coakey; 
            $arr['debit'][$temp] = $coaValue; 
            $arr['credit'][$temp] = 0;    
        }
         
        
        foreach ($arrItemTempCOA as $coakey => $coaValue){    
            $temp++;
            $arr['hidCOAKey'][$temp] = $coakey; 
            $arr['debit'][$temp] = 0;
            $arr['credit'][$temp] =  $coaValue; 
        }
         
        
        if (!empty($rs[0]['shipmentfee'])){ 
            $rsCOA = $coaLink->getCOALink ('othercost', $warehouse->tableName,$warehousekey, 0); 
            $temp++;
            $arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];
            $arr['debit'][$temp] =  $rs[0]['shipmentfee']; 
            $arr['credit'][$temp] = 0; 
            
            $termOfPayment = new TermOfPayment();
            $rsTOP = $termOfPayment->getDataRowById($rs[0]['termofpaymentkey']); 
            $isCash = ($rsTOP[0]['duedays'] == 0) ? true : false; 

            if ($isCash) {
                $rsPayment = $this->getPaymentMethodDetail($rs[0]['pkey']);  
                for($i=0;$i<count($rsPayment); $i++){ 
                     $rsCOA = $coaLink->getCOALink ('payment', $warehouse->tableName,$warehousekey,$rsPayment[$i]['paymentkey']); 
                     $temp++;
                     $arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];
                     $arr['debit'][$temp] = 0;
                     $arr['credit'][$temp] = $rsPayment[$i]['amount'];  

                     $totalPayment +=  $arr['credit'][$temp]; 
                }
 
                //selisih pembayaran   
                $temp++; 
                if ($rs[0]['balance'] < 0){ 
                    $rsCOA = $coaLink->getCOALink ('otherrevenue', $warehouse->tableName,$warehousekey, 0); 
                    $arr['debit'][$temp] = 0; 
                    $arr['credit'][$temp] = abs($rs[0]['balance']); 
                }else{ 
                    $rsCOA = $coaLink->getCOALink ('othercost', $warehouse->tableName,$warehousekey, 0); 
                    $arr['debit'][$temp] = abs($rs[0]['balance']);  
                    $arr['credit'][$temp] = 0;
                }
                $arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];
                
            }else {  
                    $temp++;
                    $arr['hidCOAKey'][$temp] =  $supplier->getAPCOAKey($rs[0]['supplierkey'],$warehousekey);
                    $arr['debit'][$temp] = 0; 
                    $arr['credit'][$temp] =  $rs[0]['shipmentfee']; 

                    $totalPayment +=  $arr['credit'][$temp]; 
            } 
        } 
 
        $arrayToJs = $generalJournal->addData($arr); 
        if (!$arrayToJs[0]['valid'])
                throw new Exception('<strong>'.$rs[0]['code'] . '</strong>. '.$this->errorMsg[504].' '.$arrayToJs[0]['message']);    
    }
 
    function cancelTrans($rsHeader,$copy){  
        $id = $rsHeader[0]['pkey'];
        
        $cashMovement = new CashMovement();   
        $cashMovement->cancelMovement($id,$this->tableName);

        $itemMovement = new ItemMovement();  
        $itemMovement->cancelMovement($id,$this->tableName);  

        $ap = new AP();
        $rsAPKey = $ap->getTableKeyAndObj($this->tableName);  
        $rsAP = $ap->searchData('','',true,' and refkey = '.$this->oDbCon->paramString($id).' and '.$ap->tableName.'.statuskey = 1 and reftabletype = '.$this->oDbCon->paramString($rsAPKey['key']));
        for($i=0;$i<count($rsAP);$i++) 
            $ap->changeStatus($rsAP[$i]['pkey'],4,'',false,true);

        if ($copy)
            $this->copyDataOnCancel($id);	  

        $this->cancelGLByRefkey($rsHeader[0]['pkey'],$this->tableName);
    } 

    function validateCancel($rsHeader,$autoChangeStatus=false){ 
        $id = $rsHeader[0]['pkey']; 
        
        //cek ad AP terbayar
        $ap = new AP();
        $rsAPKey = $ap->getTableKeyAndObj($this->tableName);  
        $rsAP = $ap->searchData('','',true,' and refkey = '.$this->oDbCon->paramString($id).' and reftabletype = '.$rsAPKey['key'].' and ('.$ap->tableName.'.statuskey = 2 or '.$ap->tableName.'.statuskey = 3)');
        if(!empty($rsAP)) 
            $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' . $this->errorMsg[201].' ' .$this->errorMsg['ap'][2]);
     
     }

    function reCalculateDetail($arrParam){

                $item = new Item();
                $purchaseOrder = new PurchaseOrder();

                // default, ongkir dan cost dibagi berdasarkan proporsional gramasi/kubikasi
                $useGramasi = $this->loadSetting('costProportionalType');

                $shipmentFee = $this->unFormatNumber($arrParam['shipmentFee']);    
                $rsPurchaseOrderHeader = $purchaseOrder->getDataRowById($arrParam['hidPurchaseOrderKey']);

                $arrReceivedQtyInBaseUnit = $arrParam['receivedQtyInBaseUnit'];  
                $arrItem = $arrParam['hidItemKey'];  
                $arrPODetailKey = $arrParam['hidPODetailKey']; 

                $gramasi = 0;
                $subtotal = 0 ;

                $arrItemDetail = array();
                for($i=0;$i<count($arrItem);$i++){ 
                    $rsPurchaseOrderDetail =  $purchaseOrder->getDetailByColumn('pkey',$arrPODetailKey[$i]);  
                    $rsItem = $item->getDataRowById($rsPurchaseOrderDetail[0]['itemkey']);
                    
                    $receivedQtyInBaseUnit =  $this->unFormatNumber($arrReceivedQtyInBaseUnit[$i]);
                    
                    $itemkey = $arrItem[$i]; 

                    $arrItemDetail[$i]['baseunitkey'] = $rsItem[0]['baseunitkey']; 
                    $arrItemDetail[$i]['costinbaseunit'] = $rsPurchaseOrderDetail[0]['costinbaseunit'];  
                    
                    // after detail discount, so we need to recalculate
                    $arrItemDetail[$i]['priceinbaseunit'] = $rsPurchaseOrderDetail[0]['total'] /  $rsPurchaseOrderDetail[0]['qtyinbaseunit'];  

                    $arrItemDetail[$i]['gramasi'] = $rsItem[0]['gramasi'];

                    $gramasi += ($rsItem[0]['gramasi'] * $receivedQtyInBaseUnit) ;  
                    $subtotal +=  $arrItemDetail[$i]['priceinbaseunit'] * $receivedQtyInBaseUnit; 
                }
 

                 for($i=0;$i<count($arrItem);$i++){
                    if ($useGramasi == 1){
                        $itemProportion = $arrItemDetail[$i]['gramasi'];
                        $totalProportion = $gramasi;
                    }else{
                        $itemProportion =  $arrItemDetail[$i]['priceinbaseunit'];
                        $totalProportion = $subtotal;
                    }  
                    $proportion = $itemProportion / $totalProportion;
                     
                    if (!USE_GL) { 
                        $percentageCost = ($totalProportion == 0 ) ? 0 :  $proportion * $shipmentFee;  
                        $arrItemDetail[$i]['costinbaseunit'] += $percentageCost; 					 
                    }
                } 

                $balance = 0;
                $totalPayment = 0; 

                $termOfPayment = new TermOfPayment();
                $rsTOP = $termOfPayment->getDataRowById($arrParam['selTermOfPaymentKey']);  
                if ($rsTOP[0]['duedays'] == 0){ 
                    $payment = $arrParam['paymentMethodValue'];
                    for($i=0;$i<count($payment);$i++){
                        $totalPayment += $this->unFormatNumber($payment[$i]);
                    }
                }
 
        
                $balance = $totalPayment - $shipmentFee; 
 
                $reCalculateResult = array(); 
                $reCalculateResult['detailItem'] = $arrItemDetail; 
                $reCalculateResult['totalPayment'] = $totalPayment; 
                $reCalculateResult['balance'] = $balance;

                return $reCalculateResult;

    }

    function getDetailWithRelatedInformation($pkey,$criteria = ''){

      $sql = 'select
                '.$this->tableNameDetail .'.*,
                '.$this->tableItem.'.name as itemname, 
                '.$this->tableItem.'.code as itemcode,
                '.$this->tableItemUnit.'.name as baseunitname 
              from
                '. $this->tableNameDetail .',
                '.$this->tableItem.',
                '.$this->tableItemUnit.' 
              where
                ' . $this->tableNameDetail .'.itemkey = '.$this->tableItem.'.pkey and
                ' . $this->tableNameDetail .'.baseunitkey = '.$this->tableItemUnit.'.pkey  and
                refkey = '.$this->oDbCon->paramString($pkey);

        $sql .= $criteria;
         
        return $this->oDbCon->doQuery($sql);

    }
    
    function normalizeParameter($arrParam, $trim = false){
            $termOfPayment = new TermOfPayment();
            $purchaseOrder = new PurchaseOrder();
        
            $arrParam = parent::normalizeParameter($arrParam); 
         
            $arrItemkey = $arrParam['hidItemKey'];
            
            $rsPO = $purchaseOrder->getDataRowById($arrParam['hidPurchaseOrderKey']);
            $reCalculateResult = $this->reCalculateDetail($arrParam);  
            $arrParam['selWarehouse'] = $rsPO[0]['warehousekey'];
            $arrParam['balance'] = $reCalculateResult['balance'];
            $arrParam['totalPayment'] = $reCalculateResult['totalPayment'];
         
            $rsTOP = $termOfPayment->getDataRowById($arrParam['selTermOfPaymentKey']);  
            if ($rsTOP[0]['duedays'] != 0){   
                for($i=0;$i<count( $arrParam['paymentMethodValue']);$i++){ 
                    $arrParam['paymentMethodValue'][$i] = 0; 
                    $arrParam['hidDetailPaymentKey'][$i] = 0;
                }
            }
        
 
            for ($i=0;$i<count($arrItemkey);$i++){    
               $arrParam['baseunitkey'][$i] = $reCalculateResult['detailItem'][$i]['baseunitkey']; 
               $arrParam['costinbaseunit'][$i] = $reCalculateResult['detailItem'][$i]['costinbaseunit'];
            }
 
            return $arrParam;
    }
     
}
?>