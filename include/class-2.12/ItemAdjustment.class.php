<?php
  
class ItemAdjustment extends BaseClass{ 
 
   function __construct(){
		
		parent::__construct();
       
		$this->tableName = 'item_adjustment_header';
		$this->tableNameDetail = 'item_adjustment_detail';
		$this->tableWarehouse = 'warehouse'; 
		$this->tableItem = 'item';
		$this->tableItemUnit = 'item_unit';
		$this->tableStatus = 'transaction_status';
        $this->isTransaction = true;
		    
		$this->securityObject = 'ItemAdjustment'; 
       
        $this->arrDataDetail = array();  
        $this->arrDataDetail['pkey'] = array('hidDetailKey');
        $this->arrDataDetail['refkey'] = array('pkey','ref');
        $this->arrDataDetail['itemkey'] = array('hidItemKey');
        $this->arrDataDetail['qtybefore'] = array('qtyBefore','number');
        $this->arrDataDetail['qtyafter'] = array('qtyAfter','number');
        $this->arrDataDetail['qtyadjust'] = array('qtyAdjust','number');
        $this->arrDataDetail['qtyinbaseunit'] = array('qtyAdjust','number');
        $this->arrDataDetail['unitkey'] = array('baseUnitKey');
        $this->arrDataDetail['unitconvmultiplier'] = array('unitConvMultiplier','number');
        $this->arrDataDetail['costinbaseunit'] = array('COGS','number');
         
        $this->arrDataDetail['qtybeforeinpcs'] = array('qtyBeforeInPcs','number');
        $this->arrDataDetail['qtyafterinpcs'] = array('qtyAfterInPcs','number');
        $this->arrDataDetail['qtyadjustinpcs'] = array('qtyAdjustInPcs','number');
        
        $this->arrDataDetail['gramasi'] = array('hidGramasi',array('datatype' => 'number'));
        $this->arrDataDetail['isweightfixed'] = array('hidIsWeightFixed');
        $this->arrDataDetail['iscostinpcs'] = array('chkCostInPcs');
        $this->arrDataDetail['costinpcs'] = array('COGSInPcs','number');
       
        $this->arrData = array();        
        $this->arrData['pkey'] = array('pkey', array('dataDetail' => array('dataset' => $this->arrDataDetail)));
        $this->arrData['code'] = array('code');
        $this->arrData['trdate'] = array('trDate','date');
        $this->arrData['warehousekey'] = array('selWarehouseKey');
        $this->arrData['trdesc'] = array('trDesc');
        $this->arrData['statuskey'] = array('selStatus');
	          
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'date','title' => 'date','dbfield' => 'trdate','default'=>true, 'width' => 100, 'align' =>'center','format' => 'date', 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'warehouse','title' => 'warehouse','dbfield' => 'warehousename','default'=>true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'description','title' => 'note','dbfield' => 'trdesc',  'width' => 250));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
        
        $this->printMenu = array();  
        array_push($this->printMenu,array('code' => 'printTransaction', 'name' => $this->lang['printTransaction'],  'icon' => 'print', 'url' => 'print/itemAdjustment'));
 
        $this->includeClassDependencies(array( 
              'COALink.class.php',
              'GeneralJournal.class.php',
              'Item.class.php', 
              'ItemCategory.class.php',
              'ItemUnit.class.php',
              'ItemMovement.class.php', 
              'Marketplace.class.php',
              'PackagingCode.class.php'
        ));
 
        $this->overwriteConfig();
   }
   
    function getQuery(){
	   
	   $sql =  '
			SELECT '.$this->tableName.'.* ,
			   '.$this->tableWarehouse.'.name as warehousename,
			   '.$this->tableStatus.'.status as statusname
			FROM '.$this->tableStatus.', '.$this->tableName.' , '.$this->tableWarehouse.'  
			WHERE '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey and  '.$this->tableName.'.warehousekey = '.$this->tableWarehouse.'.pkey
 	' .$this->criteria ;  
        
        $sql .=  $this->getWarehouseCriteria() ;
        $sql .=  $this->getCompanyCriteria() ;
        return $sql;
    }
	  
    function afterStatusChanged($rsHeader){   
        // retrieve latest status
        $rsHeader = $this->getDataRowById($rsHeader[0]['pkey']);
        if ($rsHeader[0]['statuskey'] == 2)
            $this->changeStatus($rsHeader[0]['pkey'],3); 
        
		   if($this->isActiveModule('marketplace')){
			 if ($rsHeader[0]['statuskey'] == 2 || $rsHeader[0]['statuskey'] == 4){  
				$marketplace = new Marketplace();
				$rsDetail = $this->getDetailById($rsHeader[0]['pkey']);
				$arrItemKey = array_column($rsDetail,'itemkey'); 
				$marketplace->updateProductsQOHInAllMarketplace($arrItemKey); 
			}
		   }
         
    }
     
    
    function validateForm($arr,$pkey = ''){
		  
		$arrayToJs = parent::validateForm($arr,$pkey); 
         
        $item = new Item();
         
        $warehouseke = $arr['selWarehouseKey'];
		$arrItemkey = $arr['hidItemKey'];  
		$arrQtyBefore = $arr['qtyBefore'];  
		
		//validasi kalo status gk menunggu gk bisa edit 
		if (!empty($pkey)){
			$rs = $this->getDataRowById($pkey);
			if ($rs[0]['statuskey'] <> 1){
				$this->addErrorList($arrayToJs,false,$this->errorMsg[212]);
			}
		} 
	  
        $arrDetailKeys = array(); 
		for($i=0;$i<count($arrItemkey);$i++) {
		 	if (empty($arrItemkey[$i]) ){ 
				$this->addErrorList($arrayToJs,false, $this->errorMsg['item'][1]); 	
			}else{
                // cek ada detail double gk 
                if (in_array($arrItemkey[$i],$arrDetailKeys)){  
                    $rsItem = $item->getDataRowById($arrItemkey[$i]);
                    $this->addErrorList($arrayToJs,false, $rsItem[0]['name'].'. '.$this->errorMsg[215]); 	 
                }else{ 
                    array_push($arrDetailKeys, $arrItemkey[$i]);
                }
            }   
		}
		 


		return $arrayToJs;
	 } 

	function validateConfirm($rsHeader){
		$id = $rsHeader[0]['pkey'];
        
        $warehouse = new Warehouse();  
        $coaLink = new COALink(); 
        $security = new Security();
		
        
		$purchaseOverThreshold = $this->loadSetting('purchaseOverThreshold');
		$purchaseOverThreshold = ($purchaseOverThreshold == '') ? 0 : $purchaseOverThreshold; 
        
        $hasPurchaseOverAccess = $security->isAdminLogin('PurchaseOverThreshold',10);
        
        if (USE_GL){

            $arrCOA = array();
            array_push($arrCOA, 'hpp' , 'inventory' ); 
            for ($i=0;$i<count($arrCOA);$i++){
                $rsCOA = $coaLink->getCOALink ($arrCOA[$i], $warehouse->tableName,$rsHeader[0]['warehousekey'], 0); 
                if (empty($rsCOA))	
                     $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' .$this->errorMsg['coa'][3]);
            }  

        }
            
        // cek qohBefore sudah berubah apa blm
        $rsDetail = $this->getDetailById($id);
        $itemMovement = new ItemMovement();
        $item = new Item();

        $rsItemCol = $item->searchDataRow(array($item->tableName.'.pkey',$item->tableName.'.code',$item->tableName.'.name',$item->tableName.'.cogs'),
                                         ' and '.$item->tableName.'.pkey in ('.$this->oDbCon->paramString(array_column($rsDetail,'itemkey'),',').')'
                                         );
        $rsItemCol = array_column($rsItemCol,null,'pkey');
            
        $arrZeroCOGS = array();
        
        for($i=0;$i<count($rsDetail);$i++){

            $itemkey = $rsDetail[$i]['itemkey'];
                
            $qoh = $itemMovement->sumItemMovement($itemkey,$rsHeader[0]['warehousekey'],$this->formatDBDate($rsHeader[0]['trdate']));
            $qtyBefore = $rsDetail[$i]['qtybefore'];
            $qtyAfter = $rsDetail[$i]['qtyafter'];

            $arrItem = $rsItemCol[$itemkey];
            
            if ($qtyBefore <> $qoh){  
                $this->addErrorLog(false, '<strong>'.$arrItem['name'].'</strong>. '. $this->errorMsg['itemAdjustment'][1].' '.$this->lang['pleaseReopenAndSaveTheData']); 	
            }

            if ($qtyAfter < 0){   
                $this->addErrorLog(false,'<strong>'.$arrItem['name'].'</strong>. '.$this->errorMsg[402]);
            }else{
                
                $latestPrice = $arrItem['cogs'];
                $itemUnitPrice = $rsDetail[$i]['costinbaseunit'];

                 if($latestPrice == 0){
                     array_push($arrZeroCOGS,array('itemkey' => $itemkey, 'cogs' => $itemUnitPrice )); 
                } else{ 
                      
                    if(!$hasPurchaseOverAccess && $purchaseOverThreshold > 0){ 
                        if ($itemUnitPrice > $latestPrice &&  (($itemUnitPrice-$latestPrice) / $latestPrice * 100) > $purchaseOverThreshold ) {  
                            $this->addErrorLog(false,'<strong>'.$arrItem['name'].'</strong>. '.$this->errorMsg['item'][8]);
                        } 
                    }
                }
                 
            }

            if (in_array(PLAN_TYPE['categorykey'], array(COMPANY_TYPE['jewelry']))) {
                $qohinpcs = $itemMovement->sumItemMovementInPcs($itemkey, $rsHeader[0]['warehousekey'], $this->formatDBDate($rsHeader[0]['trdate']));
                $qtyBeforeInPcs = $rsDetail[$i]['qtybeforeinpcs'];
                $qtyAfterInPcs = $rsDetail[$i]['qtyafterinpcs'];

                if ($qtyBeforeInPcs <> $qohinpcs) {
                    $this->addErrorLog(false, '<strong>' . $arrItem['name'] . '</strong>. ' . $this->errorMsg['itemAdjustment'][1] . ' ( Gr). ' . $this->lang['pleaseReopenAndSaveTheData']);
                }

                if ($qtyAfterInPcs < 0) {
                    $this->addErrorLog(false, '<strong>' . $arrItem['name'] . '</strong>. ' . $this->errorMsg[402] . ' ( Gr).');
                }
            }
        } 
        
        
     if(!$hasPurchaseOverAccess && $purchaseOverThreshold > 0){ 
         if(!empty($arrZeroCOGS)){
                        includeClass('PurchaseOrder.class.php');
                        $purchaseOrder = new PurchaseOrder();

                        $rsPurchase = $purchaseOrder->getLatestPurchase('', array_column($arrZeroCOGS,'itemkey'));

                       if(!empty($rsPurchase)){
                             $rsPurchase = array_column($rsPurchase,null,'itemkey');

                             for($i=0;$i<count($arrZeroCOGS);$i++){
                                $itemkey = $arrZeroCOGS[$i]['itemkey'];
                                $cogs = $arrZeroCOGS[$i]['cogs'];
                                 
                                $arrItem = $rsItemCol[$itemkey];
                                $latestPrice = $rsPurchase[$itemkey]['total'] / $rsPurchase[$itemkey]['qtyinbaseunit']; // biar kehitung jg discountnya
                                $itemUnitPrice = $cogs; // biar kehitung jg discuntnya


                                if($itemUnitPrice <= $latestPrice) continue;

                                if ( (($itemUnitPrice-$latestPrice) / $latestPrice * 100) > $purchaseOverThreshold) { 
//                                    $this->setLog( 'PO '.$itemUnitPrice . '  - ' .$latestPrice .'  > ' . $purchaseOverThreshold,true);
                                    $this->addErrorLog(false,'<strong>'.$arrItem['name'].'</strong>. '.$this->errorMsg['purchaseOrder'][4]);
                                }
                                    
                            }
                        }

                 }
     }
            
	 }	
    
    function updateCOGS($id){
        $item = new Item();
        $rsDetail = $this->getDetailById($id); 
        for($i=0;$i<count($rsDetail); $i++){
            
            if ($rsDetail[$i]['qtyadjust'] > 0)
                continue;

            $rsItem = $item->getDataRowById($rsDetail[$i]['itemkey']); 
            $sql = 'update '. $this->tableNameDetail .' set costinbaseunit = '.$this->oDbCon->paramString($rsItem[0]['cogs']).' where pkey = ' .$this->oDbCon->paramString($rsDetail[$i]['pkey']);
            $this->oDbCon->execute($sql);   

        }
    }

	function confirmTrans($rsHeader){
		$id = $rsHeader[0]['pkey'];
        $this->updateCOGS($id);
        
		$itemMovement = new ItemMovement();   
		
		$note = $rsHeader[0]['code'] .'. Penyesuaian Stok Barang'; 
	 	$rsDetail = $this->getDetailById($id); 
		
		for($i=0;$i<count($rsDetail); $i++){		 
           if ($rsDetail[$i]['qtyadjust'] == 0)
               continue; 
            
            $cogs = $rsDetail[$i]['costinbaseunit']; 
           $itemMovement->updateItemMovement($id,$rsDetail[$i]['itemkey'], array('qtyinbaseunit' => $rsDetail[$i]['qtyadjust'], 'qtyinpcs' => $rsDetail[$i]['qtyadjustinpcs']),array('costinbaseunit' => $cogs, 'costinpcs' => $rsDetail[$i]['costinpcs']),$this->tableName, $rsHeader[0]['warehousekey'], $note, $rsHeader[0]['trdate']);
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
		 $rsKey = $generalJournal->getTableKeyAndObj($this->tableName);
		 $arr = array();
        
		 $arr['pkey'] = $generalJournal->getNextKey($generalJournal->tableName);
		 $arr['code'] = 'xxxxx';
		 $arr['refkey'] = $rsHeader[0]['pkey'];
		 $arr['refTableType'] = $rsKey['key'];
		 $arr['trDate'] =  $this->formatDBDate($rsHeader[0]['trdate'],'d / m / Y'); 
		 $arr['createdBy'] = 0; 
		 $arr['selWarehouseKey'] = $rsHeader[0]['warehousekey'];
		  
/*		$totalHPP = 0 ;
        $rsDetail = $this->getDetailById($rsHeader[0]['pkey']); 
        for($i=0;$i<count($rsDetail);$i++){
            $totalHPP += ($rsDetail[$i]['costinbaseunit']*$rsDetail[$i]['qtyadjust']);
        }*/
        
        $temp = -1;
      
        $rsDetail = $this->getDetailById($rsHeader[0]['pkey']); 
         
        $totalHPP = 0;
        $arrItemCOA = array(); 
        foreach($rsDetail as $detail){
            $itemCOAKey = $item->getInventoryCOAKey($detail['itemkey'],$rsHeader[0]['warehousekey']); 
            $totalItemValue =  $detail['costinbaseunit']*$detail['qtyadjust']; 
            $arrItemCOA[$itemCOAKey] = (!isset($arrItemCOA[$itemCOAKey])) ? $totalItemValue : $arrItemCOA[$itemCOAKey] + $totalItemValue; 
        }
        
        foreach ($arrItemCOA as $coakey => $coaValue){ 
            $temp++;
            $arr['hidCOAKey'][$temp] = $coakey;
            $arr['debit'][$temp] = $coaValue; 
            $arr['credit'][$temp] = 0; 
            $arr['refCashBankKey'][$temp] = '';
            
            $totalHPP += $coaValue;
        }        
        
        
/*        $rsCOA = $coaLink->getCOALink ('inventory', $warehouse->tableName,$rsHeader[0]['warehousekey'], 0); 
        $temp++;
        $arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];
        $arr['debit'][$temp] = $totalHPP; 
        $arr['credit'][$temp] = 0;  */
        
        $rsCOA = $coaLink->getCOALink ('hpp', $warehouse->tableName,$rsHeader[0]['warehousekey'], 0); 
        $temp++;
        $arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];
        $arr['debit'][$temp] = 0; 
        $arr['credit'][$temp] = $totalHPP;  


		$arrayToJs = $generalJournal->addData($arr);
        
		if (!$arrayToJs[0]['valid'])
                throw new Exception('<strong>'.$rsHeader[0]['code'] . '</strong>. '.$this->errorMsg[504].' '.$arrayToJs[0]['message']); 
	 }
     
	 
	function cancelTrans($rsHeader,$copy){ 
		
		$id = $rsHeader[0]['pkey'];;
		  	 
		$itemMovement = new ItemMovement();  
		$itemMovement->cancelMovement($id,$this->tableName);
		 
		if ($copy)
			$this->copyDataOnCancel($id);	  
        
        $this->cancelGLByRefkey($rsHeader[0]['pkey'],$this->tableName);
	} 
    
  
    function getDetailWithRelatedInformation($pkey,$criteria = ''){
	   $sql = 'select
	   			'.$this->tableNameDetail .'.*,
                '.$this->tableItem.'.name as itemname, 
                '.$this->tableItem.'.code as itemcode,
                '.$this->tableItem.'.sellingprice as itemsellingprice,
                '.$this->tableItemUnit.'.name as unitname,
                baseunit.name as baseunitname
			  from
			  	'. $this->tableNameDetail .',
                '.$this->tableItem.',
                '.$this->tableItemUnit.',
                '.$this->tableItemUnit.' baseunit
			  where
			  	' . $this->tableNameDetail .'.itemkey = '.$this->tableItem.'.pkey and
			  	' . $this->tableNameDetail .'.unitkey = '.$this->tableItemUnit.'.pkey and
			  	' . $this->tableItem .'.baseunitkey = baseunit.pkey and
			  	refkey in ('.$this->oDbCon->paramString($pkey,',').')';
        
        $sql .= $criteria;
        
		return $this->oDbCon->doQuery($sql);
	
   }
    
    function normalizeParameter($arrParam, $trim = false){
        $item = new Item();
        
        $itemkey = $arrParam['hidItemKey'];
        
        $rsItemCol = $item->searchDataRow(array($item->tableName.'.pkey',
                                             $item->tableName.'.baseunitkey',
                                             $item->tableName.'.isweightfixed',
                                             $item->tableName.'.gramasi'), 
                                        ' and '.$item->tableName.'.pkey in ('.$this->oDbCon->paramString($itemkey,', ').')');
        $rsItemCol = array_column($rsItemCol,null,'pkey');
            
        for($i=0;$i<count($itemkey); $i++) {
            $qtyBefore = $this->unFormatNumber($arrParam['qtyBefore'][$i]);
            $qtyAfter = $this->unFormatNumber($arrParam['qtyAfter'][$i]);
            
            $qtyAdjust = $qtyAfter - $qtyBefore;
            
            $rsItem = $rsItemCol[$itemkey[$i]];
            
            $arrParam['baseUnitKey'][$i]  =  $rsItem['baseunitkey']; 
            $arrParam['unitConvMultiplier'][$i] = 1;//$reCountResult['unitConvMultiplier']; 
            $arrParam['qtyAdjust'][$i] = $qtyAdjust;
            $arrParam['qtyInBaseUnit'][$i] = $qtyAdjust;
            
            //if(isset($arrParam['qtyBeforeInPcs'])) { 
            
            if(DOMAIN_NAME == 'hans.wintera.co.id') { 

                $arrParam['hidIsWeightFixed'][$i]= $rsItem['isweightfixed'];
                $arrParam['hidGramasi'][$i] = $rsItem['gramasi'];
                 
                if($rsItem['isweightfixed'] == 1) {
                    $arrParam['qtyAfterInPcs'][$i] = $qtyAfter * $rsItem['gramasi'];
                }

                $qtyBeforeInPcs = $this->unFormatNumber($arrParam['qtyBeforeInPcs'][$i]);
                $qtyAfterInPcs = $this->unFormatNumber($arrParam['qtyAfterInPcs'][$i]);
                
                $qtyAdjustInPcs = $qtyAfterInPcs - $qtyBeforeInPcs;
                
                $arrParam['qtyAdjustInPcs'][$i] = $qtyAdjustInPcs;

                $COGSBaseUnit = 0;
                $COGSInPcsVal = 0;
                if($arrParam['chkCostInPcs'][$i]  == 1 ) {
                    if($qtyAfter > 0) {
                        $COGSBaseUnit = ($qtyAfterInPcs * $this->unFormatNumber($arrParam['COGSInPcs'][$i])) / $qtyAfter;
                    }
                    $arrParam['COGS'][$i] = $COGSBaseUnit;
                } else {
                    if($qtyAfterInPcs > 0) {
                        $COGSInPcsVal = ($qtyAfter * $this->unFormatNumber($arrParam['COGS'][$i])) / $qtyAfterInPcs;
                    }
                    $arrParam['COGSInPcs'][$i] = $COGSInPcsVal;
                }

            }
            
        }
     
        
        $arrParam = parent::normalizeParameter($arrParam,true); 
   
        //$this->setLog($arrParam,true);
        return $arrParam;
    }
    
}
?>
