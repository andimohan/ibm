<?php
  
class AssetTransfer extends BaseClass{ 
 
   function __construct(){
		
		parent::__construct(); 
       
		$this->tableName = 'asset_transfer_header';
		$this->tableNameDetail = 'asset_transfer_detail'; 
		$this->tableWarehouse = 'warehouse';
		$this->tableAssetCategory = 'asset_category';
        $this->tableAsset = 'asset';
		$this->tableStatus = 'transaction_status';
		$this->isTransaction = true;
         
		$this->securityObject = 'AssetTransfer'; 

        $this->arrDataDetail = array();   
        $this->arrDataDetail['pkey'] = array('hidDetailKey');
	   	$this->arrDataDetail['refkey'] = array('pkey','ref');
        $this->arrDataDetail['assetkey'] = array('hidAssetKey');
        $this->arrDataDetail['quantity'] = array('quantity','number'); 

        $arrDetails = array();
        array_push($arrDetails, array('dataset' => $this->arrDataDetail, 'tableName' => $this->tableNameDetail)); 
       
        $this->arrData = array(); 
        $this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails));
        $this->arrData['code'] = array('code');
        $this->arrData['trdate'] = array('trDate','date');
        $this->arrData['fromwarehousekey'] = array('selWarehouseFromKey');
        $this->arrData['towarehousekey'] = array('selWarehouseToKey');
        $this->arrData['refkey'] = array('refkey');
        $this->arrData['reftabletype'] = array('reftabletype');
        $this->arrData['trdesc'] = array('trDesc');
        $this->arrData['statuskey'] = array('selStatus');
		 
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'date','title' => 'date','dbfield' => 'trdate','default'=>true, 'width' => 100, 'align' =>'center', 'format' => 'date'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'origin','title' => 'origin','dbfield' => 'warehousefromname','default'=>true,'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'destination','title' => 'destination','dbfield' => 'warehousetoname','default'=>true ,'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true));

        $this->arrSearchColumn = array();
        array_push($this->arrSearchColumn, array('Kode', $this->tableName . '.code'));
        array_push($this->arrSearchColumn, array('origin', 'warehousefrom.name'));
        array_push($this->arrSearchColumn, array('destination', 'warehouseto.name'));
        array_push($this->arrSearchColumn, array('status', $this->tableStatus . '.status'));

        $this->printMenu = array();  
        array_push($this->printMenu,array('code' => 'printTransaction', 'name' => $this->lang['printTransaction'],  'icon' => 'print', 'url' => 'print/assetTransfer'));
 
        
       $this->includeClassDependencies(array(  
            'Asset.class.php', 
            'Category.class.php', 
            'AssetCategory.class.php', 
            'Item.class.php', 
            'Warehouse.class.php', 
            'COALink.class.php', 
            'GeneralJournal.class.php', 
        ));
        $this->overwriteConfig();

   }
    
    function getQuery(){
	   
	   $sql = '
			SELECT '.$this->tableName.'.* ,
			   warehousefrom.name as warehousefromname,
			   warehouseto.name as warehousetoname,
			   '.$this->tableStatus.'.status as statusname
			FROM '.$this->tableStatus.', '.$this->tableName.' , '.$this->tableWarehouse.' warehousefrom,  '.$this->tableWarehouse.' warehouseto    
			WHERE '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey and  '.$this->tableName.'.fromwarehousekey = warehousefrom.pkey and  '.$this->tableName.'.towarehousekey = warehouseto.pkey
		' .$this->criteria ; 
		 
        $sql .=  $this->getCompanyCriteria() ;
        return $sql;
    }  
    
    function afterStatusChanged($rsHeader){   
        // retrieve latest status
        $rsHeader = $this->getDataRowById($rsHeader[0]['pkey']);
        if ($rsHeader[0]['statuskey'] == 2)
            $this->changeStatus($rsHeader[0]['pkey'],3); 
    }
    
    function validateForm($arr,$pkey = ''){
		$asset = new Asset();   
		$warehouse = new Warehouse();   
		  
		$arrayToJs = parent::validateForm($arr,$pkey); 
         
		$arrAssetkey = $arr['hidAssetKey'];  
		$quantity = $arr['quantity'];

        if ($arr['selWarehouseFromKey'] == $arr['selWarehouseToKey']){  
            $this->addErrorList($arrayToJs,false,$this->errorMsg['warehouseTransfer'][2]); 
		}  
		
        
		$arrDetailKeys = array(); 
         
		for($i=0;$i<count($arrAssetkey);$i++) {
			if (empty($arrAssetkey[$i]) ){ 
				$this->addErrorList($arrayToJs,false, $this->errorMsg['asset'][1]); 	
			}else{
                    $rsAsset = $asset->getDataRowById($arrAssetkey[$i]); 
                	if ( $this->unFormatNumber($quantity[$i]) <= 0){
                        $this->addErrorList($arrayToJs,false,$rsAsset[0]['name']. '. ' . $this->errorMsg[500]); 
                    }
				
                    // cek ada detail double gk  
                    if (in_array($arrAssetkey[$i],$arrDetailKeys)){  
                        $this->addErrorList($arrayToJs,false, $rsAsset[0]['name'].'. '.$this->errorMsg[215]); 	 
                    }else{ 
                        array_push($arrDetailKeys, $arrItemkey[$i]);
                    }       

                    if ($rsAsset[0]['warehouskey'] != $arr['selWarehouseFromKey']) {
                        $rsWarehouse = $warehouse->getDataRowById($arr['selWarehouseFromKey']);
                        $this->addErrorList($arrayToJs,false,$rsAsset[0]['name']. '. ' . $this->errorMsg['asset'][6].' '. $rsWarehouse[0]['name']); 
                    }
            }
		
		} 
       
         
		return $arrayToJs;
	 }
	 
	function validateConfirm($rsHeader){
        $id = $rsHeader[0]['pkey'];
        $warehouse = new Warehouse();  
        $coaLink = new COALink();

		$rsDetail = $this->getDetailById($id);
        
        
/*         if (USE_GL){ 
            $arrCOA = array();
            array_push($arrCOA, 'hpp' , 'inventory' ); 
            for ($i=0;$i<count($arrCOA);$i++){
                $rsCOA = $coaLink->getCOALink ($arrCOA[$i], $warehouse->tableName,$rsHeader[0]['fromwarehousekey'], 0); 
                if (empty($rsCOA))	
                    $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg['coa'][3]); 

                $rsCOA = $coaLink->getCOALink ($arrCOA[$i], $warehouse->tableName,$rsHeader[0]['towarehousekey'], 0); 
                if (empty($rsCOA))	
                    $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg['coa'][3]);
            }  
         }*/

    
            
	 }	

   
    
	function confirmTrans($rsHeader){
        $rsDetail = $this->getDetailWithRelatedInformation($rsHeader[0]['pkey']);

        $sql = ' update '.$this->tableAsset.' set 
                            warehousekey = '.$this->oDbCon->paramString($rsHeader[0]['towarehousekey']).'
                         where
                            '.$this->tableAsset.'.pkey in ('.$this->oDbCon->paramString(array_column($rsDetail,'assetkey'),',').')';
				$this->oDbCon->execute($sql);
		// $itemMovement = new ItemMovement();  
		// $warehouse = new Warehouse();
        
		// $id = $rsHeader[0]['pkey']; 
        
		// $rsWarehouseFrom = $warehouse->getDataRowById($rsHeader[0]['fromwarehousekey']);
		// $rsWarehouseTo = $warehouse->getDataRowById($rsHeader[0]['towarehousekey']);
	 	// $rsDetail = $this->getDetailById($rsHeader[0]['pkey']);  
		             
		// $note = $rsHeader[0]['code'] .'. Perpindahan Gudang dari '.$rsWarehouseFrom[0]['name'].' ke ' .$rsWarehouseTo[0]['name'];
		
  
		//update jurnal umum 
//        $this->updateGL($rsHeader);
	} 
	
//     function updateGL($rsHeader){
//         if (!USE_GL) return;
        
// 		 $warehouse = new Warehouse();
//          $coaLink = new COALink();
//          $item = new Item();
// 		 $generalJournal = new GeneralJournal();
//          $rsKey = $generalJournal->getTableKeyAndObj($this->tableName);
		 
// 		 $arr = array();
// 		 $arr['pkey'] = $generalJournal->getNextKey($generalJournal->tableName);
// 		 $arr['code'] = 'xxxxx';
// 		 $arr['refkey'] = $rsHeader[0]['pkey'];
// 		 $arr['refTableType'] = $rsKey['key'];
// 		 $arr['trDate'] =  $this->formatDBDate($rsHeader[0]['trdate'],'d / m / Y'); 
// 		 $arr['createdBy'] = 0; 
		  
// 		/*$totalHPP = 0 ;
//         $rsDetail = $this->getDetailById($rsHeader[0]['pkey']); 
//         for($i=0;$i<count($rsDetail);$i++){
//             $rsItem = $item->getDataRowById($rsDetail[$i]['itemkey']);
//             $totalHPP += ($rsItem[0]['cogs'] * $rsDetail[$i]['qtyinbaseunit']);
//         }*/
        
//         $temp = -1;
      
//         $rsDetail = $this->getDetailById($rsHeader[0]['pkey']); 
        
//         // from warehousekey
//         $totalHPP = 0;
//         $arrItemCOA = array(); 
//         foreach($rsDetail as $detail){
//             $itemCOAKey = $item->getInventoryCOAKey($detail['itemkey'],$rsHeader[0]['fromwarehousekey']); 
//             $totalItemValue =  $detail['costinbaseunit']*$detail['qtyinbaseunit']; 
//             $arrItemCOA[$itemCOAKey] = (!isset($arrItemCOA[$itemCOAKey])) ? $totalItemValue : $arrItemCOA[$itemCOAKey] + $totalItemValue; 
//         }
        
//         foreach ($arrItemCOA as $coakey => $coaValue){ 
//             $temp++;
//             $arr['hidCOAKey'][$temp] = $coakey;
//             $arr['debit'][$temp] = 0; 
//             $arr['credit'][$temp] = $coaValue; 
//             $arr['refCashBankKey'][$temp] = '';
            
//             $totalHPP += $coaValue;
//         }        
        
//         // to warehousekey
//         $totalHPP = 0;
//         $arrItemCOA = array(); 
//         foreach($rsDetail as $detail){
//             $itemCOAKey = $item->getInventoryCOAKey($detail['itemkey'],$rsHeader[0]['towarehousekey']); 
//             $totalItemValue =  $detail['costinbaseunit']*$detail['qtyinbaseunit']; 
//             $arrItemCOA[$itemCOAKey] = (!isset($arrItemCOA[$itemCOAKey])) ? $totalItemValue : $arrItemCOA[$itemCOAKey] + $totalItemValue; 
//         }
        
//         foreach ($arrItemCOA as $coakey => $coaValue){ 
//             $temp++;
//             $arr['hidCOAKey'][$temp] = $coakey;
//             $arr['debit'][$temp] = $coaValue; 
//             $arr['credit'][$temp] = 0; 
//             $arr['refCashBankKey'][$temp] = '';
            
//             $totalHPP += $coaValue;
//         }        
        
        
// /*       // hpp gk ikut pindah

//         $rsCOA = $coaLink->getCOALink ('hpp', $warehouse->tableName,$rsHeader[0]['fromwarehousekey'], 0);  
//         $temp++;
//         $arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];
//         $arr['debit'][$temp] = $totalHPP; 
//         $arr['credit'][$temp] = 0;  */
         
// /*        $rsCOA = $coaLink->getCOALink ('inventory', $warehouse->tableName,$rsHeader[0]['towarehousekey'], 0);  
//         $temp++;
//         $arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];
//         $arr['debit'][$temp] = $totalHPP; 
//         $arr['credit'][$temp] = 0;  */
        
         
// /*        $rsCOA = $coaLink->getCOALink ('inventory', $warehouse->tableName,$rsHeader[0]['fromwarehousekey'], 0);  
//         $temp++;
//         $arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];
//         $arr['debit'][$temp] = 0; 
//         $arr['credit'][$temp] = $totalHPP; */
 
// /*        $rsCOA = $coaLink->getCOALink ('hpp', $warehouse->tableName,$rsHeader[0]['towarehousekey'], 0);  
//         $temp++;
//         $arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];
//         $arr['debit'][$temp] = 0; 
//         $arr['credit'][$temp] = $totalHPP;  */
        
//         $arr['_mnv_ungroup'] = 1;
//         //$this->setLog($arr,true);
        
// 		$arrayToJs = $generalJournal->addData($arr);
        
// 		if (!$arrayToJs[0]['valid'])
//                 throw new Exception('<strong>'.$rsHeader[0]['code'] . '</strong>. '.$this->errorMsg[504].' '.$arrayToJs[0]['message']); 
// 	 }
    
    
	function cancelTrans($rsHeader,$copy){ 
		  
		// $id = $rsHeader[0]['pkey'];
		 	 
		// $itemMovement = new ItemMovement();  
		// $itemMovement->cancelMovement($id,$this->tableName);
		// $itemMovement->cancelSNMovement($id,$this->tableName);

        $rsDetail = $this->getDetailWithRelatedInformation($rsHeader[0]['pkey']);

        $sql = ' update '.$this->tableAsset.' set 
                            movewarehousekey = null
                         where
                            '.$this->tableAsset.'.pkey in ('.$this->oDbCon->paramString(array_column($rsDetail,'assetkey'),',').')';
		$this->oDbCon->execute($sql);
		
		
		if ($copy)
			$this->copyDataOnCancel($id);	 
        
        $this->cancelGLByRefkey($rsHeader[0]['pkey'],$this->tableName);
	} 
    
    function getDetailWithRelatedInformation($pkey, $criteria=''){
	   $sql = 'select
	   			'.$this->tableNameDetail .'.*,
                '.$this->tableAsset.'.name as assetname, 
                '.$this->tableAsset.'.code as assetcode
			  from
			  	'. $this->tableNameDetail .',
                '.$this->tableAsset.'
			  where
			  	' . $this->tableNameDetail .'.assetkey = '.$this->tableAsset.'.pkey and
			  	refkey = '.$this->oDbCon->paramString($pkey);
        
        $sql .= $criteria;
        
		return $this->oDbCon->doQuery($sql);
	
   }
    
  
   

    function normalizeParameter($arrParam, $trim = false){
          
        $item = new Item();
         
        $arrParam = parent::normalizeParameter($arrParam); 
    
        return $arrParam;
    }
      
	/*function generateWarehouseTransferReport($criteria='',$detailCriteria='',$order='',$pkey=''){
        
	   $sql =  '
			SELECT '.$this->tableName.'.code,
                    warehousefrom.name as warehousefromname,
			        warehouseto.name as warehousetoname,
                   '.$this->tableName.'.trdate, 
                   '.$this->tableName.'.trdesc, 
                   '.$this->tableItem.'.code as itemcode,
                   '.$this->tableItem.'.name as itemname,
                   '.$this->tableNameDetail.'.qty, 
                   '.$this->tableNameDetail.'.costinbaseunit, 
                   '.$this->tableNameDetail.'.qty * '.$this->tableNameDetail.'.costinbaseunit as totalcogs, 
                   '.$this->tableStatus.'.status as statusname , 
                   '.$this->tableItemUnit.'.name as unitname,
                   concat(\' / \',baseunit.name) as cogsunit 
			FROM 
                '.$this->tableStatus.',  
                '.$this->tableItem.', 
                '.$this->tableNameDetail.',
                '.$this->tableName.',
                '.$this->tableWarehouse.' warehousefrom, 
                '.$this->tableWarehouse.' warehouseto,
                '.$this->tableItemUnit.',
                '.$this->tableItemUnit.' baseunit
			WHERE     
                '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey and 
                '.$this->tableName.'.fromwarehousekey = warehousefrom.pkey and  
                '.$this->tableName.'.towarehousekey = warehouseto.pkey and
                '.$this->tableNameDetail.'.refkey = '.$this->tableName.'.pkey and 
                '.$this->tableNameDetail .'.itemkey = '.$this->tableItem.'.pkey and 
                '.$this->tableNameDetail .'.unitkey = '.$this->tableItemUnit.'.pkey and
			  	'.$this->tableItem .'.baseunitkey = baseunit.pkey  
                
 		'; 
        
        if (!empty($criteria))  
            $sql .=  ' ' .$criteria;
        
        if (!empty($detailCriteria))  
            $sql .=  ' ' .$detailCriteria; 
        
        if (!empty($pkey))  
            $sql .=  '  and '.$this->tableName.'.pkey = ' .$this->oDbCon->paramString($pkey);

        if (!empty($order))  
            $sql .=  ' ' .$order; 
          
       return $this->oDbCon->doQuery($sql);
		 
    }      */
    
  
    
}
?>
