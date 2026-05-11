<?php
  
class Assembly extends BaseClass{ 
 
   function __construct(){
		
		parent::__construct();
       
		$this->tableName = 'assembly_header';
		$this->tableNameDetail = 'assembly_detail';
		$this->tableStatus = 'transaction_status';
		$this->tableItem = 'item';
		$this->tableUnit = 'item_unit';
		$this->tableBOM = 'bill_of_materials_header';
		$this->tableWarehouse = 'warehouse';  

	   	$this->securityObject = 'Assembly';
	   
	    $this->isTransaction = true; 
	   	$this->newLoad = true;
	   
	    $this->arrDataDetail = array();   
        $this->arrDataDetail['pkey'] = array('hidDetailKey');
	   	$this->arrDataDetail['refkey'] = array('pkey','ref');
        $this->arrDataDetail['itemkey'] = array('hidItemDetailKey');
        $this->arrDataDetail['qtybom'] = array('qtyDetail','number'); 
        $this->arrDataDetail['qtyused'] = array('qtyUsed','number'); 
	   
     	$arrDetails = array();
        array_push($arrDetails, array('dataset' => $this->arrDataDetail, 'tableName' => $this->tableNameDetail)); 
     
        $this->arrData = array();
        $this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails));
        $this->arrData['code'] = array('code');
        $this->arrData['trdate'] = array('trDate','date');
        $this->arrData['bomkey'] = array('selBOM');
        $this->arrData['itemkey'] = array('hidItemKey');
        $this->arrData['qty'] = array('qty','number');
        $this->arrData['cost'] = array('cost','number');
        $this->arrData['warehousefromkey'] = array('selWarehouseFromKey');
        $this->arrData['warehousetokey'] = array('selWarehouseToKey'); 
        $this->arrData['trdesc'] = array('trDesc'); 
		  
        $this->tableNeedToBeCopyOnCancel = array($this->tableNameDetail);
	   
	    $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'date','title' => 'date','dbfield' => 'trdate','default'=>true, 'width' => 100, 'align'=>'center', 'format' => 'date'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'billOfMaterials','title' => 'billOfMaterials','dbfield' => 'bomname','default'=>true));
        array_push($this->arrDataListAvailableColumn, array('code' => 'item','title' => 'itemName','dbfield' => 'itemname','default'=>true,'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'sourceWarehouse','title' => 'sourceWarehouse','dbfield' => 'warehousefromname','default'=>true,'width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'destinationWarehouse','title' => 'destinationWarehouse','dbfield' => 'warehousetoname','default'=>true,'width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
 			
	   
	   	$this->arrSearchColumn = array();
	    array_push($this->arrSearchColumn, array('Kode', $this->tableName . '.code'));
		array_push($this->arrSearchColumn, array('Tanggal', $this->tableName . '.trdate')); 
		array_push($this->arrSearchColumn, array('BOM', $this->tableBOM . '.name') );
		array_push($this->arrSearchColumn, array('Item', $this->tableItem . '.name') );
	   
	   $this->includeClassDependencies(array(
                   'BillOfMaterials.class.php', 
                   'COALink.class.php', 
                   'Warehouse.class.php',
                   'Item.class.php',
                   'ItemMovement.class.php',
                   'GeneralJournal.class.php',
                  
        ));  
	   
        $this->overwriteConfig();
	   
		 
   }
   
   function getQuery(){
	   
	   return '
			SELECT 
              '.$this->tableName.'.* ,
              '.$this->tableBOM.'.name as bomname,
              '.$this->tableItem.'.name as itemname,
              '.$this->tableWarehouse.'.name as warehousefromname,
              warehouseto.name as warehousetoname,
			   '.$this->tableStatus.'.status as statusname 
			FROM 
                '.$this->tableStatus.',
                '.$this->tableName.',
                '.$this->tableBOM.',
                '.$this->tableItem.',
                '.$this->tableWarehouse.',
                '.$this->tableWarehouse.' as warehouseto
			WHERE '.$this->tableBOM.'.pkey = '.$this->tableName.'.bomkey  and
                '.$this->tableName.'.itemkey = '.$this->tableItem.'.pkey and
                '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey and
                '.$this->tableName.'.warehousefromkey = '.$this->tableWarehouse.'.pkey and
                '.$this->tableName.'.warehousetokey = warehouseto.pkey
 		' .$this->criteria ; 
		 
    }  

  
	
     function validateForm($arr,$pkey = ''){

        $item = new Item();   
        $billOfMaterials = new BillOfMaterials();
		  
		$arrayToJs = parent::validateForm($arr,$pkey); 
         
		$arrItemkey = $arr['hidItemDetailKey']; 
        $arrItemHeaderKey = $arr['hidItemKey'];
		$arrQty = $arr['qtyUsed']; 
		 
	 	//validasi kalo status gk menunggu gk bisa edit 
		if (!empty($pkey)){
			$rs = $this->getDataRowById($pkey);
			if ($rs[0]['statuskey'] <> 1){
				$this->addErrorList($arrayToJs,false,$this->errorMsg[212]);
			}
		} 
        if(empty($arrItemHeaderKey)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['item'][1]);
		} 
	
		if(empty($arr['selBOM'])){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['billofmaterials'][1]);
		}else{
			$rsBom = $billOfMaterials->getDataRowById($arr['selBOM']);
			if ($rsBom[0]['itemkey'] <> $arrItemHeaderKey ){
				$this->addErrorList($arrayToJs,false,$this->errorMsg['assembly'][1]);
			}
		}
         
		if(empty($arr['qty'])){
			$this->addErrorList($arrayToJs,false,$this->errorMsg[503]);
		}
		 
        $arrDetailKeys = array(); 
         
		for($i=0;$i<count($arrItemkey);$i++) {
		 	if (empty($arrItemkey[$i]) ){ 
				$this->addErrorList($arrayToJs,false, $this->errorMsg['item'][1]); 	
			} else{
                
                if ($this->unFormatNumber($arrQty[$i]) <= 0){
                    $rsItem = $item->getDataRowById($arrItemkey[$i]);
                    $this->addErrorList($arrayToJs,false,$rsItem[0]['name']. '. ' . $this->errorMsg[500]); 
                } 
 
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
				case 4 : $arrayToJs = $this->validateCancel($id);
						 if (!empty($arrayToJs)) 
								return $arrayToJs; 
						  break; 
			} 


			if(!$this->oDbCon->startTrans())
				throw new Exception($this->errorMsg[100]);
					 
			switch ($status){ 
				case 2 : $this->confirmTrans($id); break; 
				case 4 : $this->cancelTrans($id,$copy);
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
	 
	function validateConfirm($id){
		$warehouse = new Warehouse();  
        
		$rs = $this->getDataRowById($id);
        $rsDetail = $this->getDetailById($id);
        
        $arrayToJs = array();
        
        //validasi stock
        $itemMovement = new itemMovement();
        for($i=0;$i<count($rsDetail);$i++){
             $saldoakhir = $itemMovement->getItemQOH($rsDetail[$i]['itemkey'], $rs[0]['warehousefromkey']);  
             $totalqty = $saldoakhir - $rsDetail[$i]['qtyused'];  
            if($totalqty<0){
                $item = new Item();
                $rsItem = $item->getDataRowById($rsDetail[$i]['itemkey']);

                $this->addErrorList($arrayToJs,false,'<strong>'.$rsItem[0]['name'].'</strong>. '.$this->errorMsg[402]);
            }
        }
		

	 	return $arrayToJs;
	 }
	
	 function validateClose($rsHeader){ 
           
        $id = $rsHeader[0]['pkey'];
	
    } 
	 
	 
	function confirmTrans($id){
		
		$rsHeader = $this->getDataRowById($id);
		$rsDetail = $this->getDetailById($id);

		$item = new Item();
		$note = 'Perakitan '.$rsHeader[0]['code'];
         			
		//update cogs;
		$arrReturnCOGS = $this->updateCOGS($id);	 
		$materialsCOGS = array_column($arrReturnCOGS['materials'],'cogs','itemkey');
		
        $totalCOGS = 0; 
	 
        $itemMovement = new ItemMovement();  
        for($i=0;$i<count($rsDetail); $i++){	 
            $cogs = $materialsCOGS[$rsDetail[$i]['itemkey']];	
            $totalCOGS += ($cogs * $rsDetail[$i]['qtybom']); //jgn pake qtyused, karena perlunya cogs per unit jadi
			
            $itemMovement->updateItemMovement($id,$rsDetail[$i]['itemkey'],-$rsDetail[$i]['qtyused'],$cogs,$this->tableName, $rsHeader[0]['warehousefromkey'], $note,$rsHeader[0]['trdate']); 
		}
		
		//hitung ulang untuk cogs nya  
        $itemMovement->updateItemMovement($id,$rsHeader[0]['itemkey'],$rsHeader[0]['qty'],$totalCOGS,$this->tableName, $rsHeader[0]['warehousetokey'], $note,$rsHeader[0]['trdate']);
		
		
		//update GL
		$this->updateGL($rsHeader);
		
	}

    function cancelTrans($id,$copy){ 
		 
		
		$rsHeader = $this->getDataRowById($id); 
		
		if ($rsHeader[0]['statuskey'] == 1)
			return;
			 
        $itemMovement = new ItemMovement();  
        $itemMovement->cancelMovement($id,$this->tableName); 

		if ($copy)
			$this->copyDataOnCancel($id);	  
	    
        $this->cancelGLByRefkey($rsHeader[0]['pkey'],$this->tableName);
        
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
        
        $temp = -1;
      
        $rsDetail = $this->getDetailById($rsHeader[0]['pkey']); 
		
		$totalItemValue = 0;
		
		// grouping coa bahan
		$arrMaterialCOGS = array();
		foreach($rsDetail as $row){
			$itemCOAKey = $item->getInventoryCOAKey($row['itemkey'],$rsHeader[0]['warehousetokey']); 
			$cogsValue =  $row['cogs']*$row['qtyused']; 
			$totalItemValue += $cogsValue; 
			$arrMaterialCOGS[$itemCOAKey] = (!isset($arrMaterialCOGS[$itemCOAKey])) ? $cogsValue : $arrMaterialCOGS[$itemCOAKey] + $cogsValue; 
		}
		
		foreach($arrMaterialCOGS as $key=>$row){
			$temp++;
			$arr['hidCOAKey'][$temp] = $key;
			$arr['debit'][$temp] = $row; 
			$arr['credit'][$temp] = 0; 
		}
		
		// to warehousekey
		$itemCOAKey = $item->getInventoryCOAKey($rsHeader[0]['itemkey'],$rsHeader[0]['warehousefromkey']); 
		$temp++;
		$arr['hidCOAKey'][$temp] = $itemCOAKey;
		$arr['debit'][$temp] = 0; 
		$arr['credit'][$temp] = $totalItemValue; 
        
		$arrayToJs = $generalJournal->addData($arr);
        
		if (!$arrayToJs[0]['valid'])
                throw new Exception('<strong>'.$rsHeader[0]['code'] . '</strong>. '.$this->errorMsg[504].' '.$arrayToJs[0]['message']); 
	 }
	
	function updateCOGS($pkey){
		$item = new Item();
		$rsDetail = $this->getDetailById($pkey); 
		$arrItemKey = array_column($rsDetail,'itemkey');
		
		$rsItemCol = $item->searchDataRow(array($item->tableName.'.pkey',$item->tableName.'.name',$item->tableName.'.cogs'),
										   	' and '.$item->tableName.'.pkey in('.$this->oDbCon->paramString($arrItemKey,',').')');
		
		$rsItemCol = array_column($rsItemCol,null,'pkey');	
		
		$arrReturn = array();
		$arrReturn['materials'] = array();
		$arrReturn['finishGood'] = array(); // nanti ditambahkan jika perlu
		
        for($i=0;$i<count($rsDetail); $i++){ 
			$rsItem = $rsItemCol[$rsDetail[$i]['itemkey']]; 
			
			array_push($arrReturn['materials'], array('itemkey' => $rsItem['pkey'], 'cogs' => $rsItem['cogs']));
			
			$sql = 'UPDATE '.$this->tableNameDetail.' SET cogs = '.$this->oDbCon->paramString($rsItem['cogs']).'  WHERE pkey ='.$this->oDbCon->paramString($rsDetail[$i]['pkey']);
			$this->oDbCon->execute($sql); 
        }
		
		return $arrReturn;
	}
	
	function getDetailWithRelatedInformation($pkey,$criteria=''){
	   $sql = 'select
	   			'. $this->tableNameDetail .'.*, 
                '.$this->tableItem.'.name as itemname, 
                '.$this->tableItem.'.code as itemcode, 
                '.$this->tableUnit.'.name as baseunitname 
			  from
			  	'. $this->tableNameDetail.',
                '.$this->tableItem.',
                '.$this->tableUnit.'
			  where
			  	' . $this->tableNameDetail .'.itemkey = '.$this->tableItem.'.pkey and 
			  	' . $this->tableItem .'.baseunitkey = '.$this->tableUnit.'.pkey and 
			  	refkey in ('.$this->oDbCon->paramString($pkey,',') . ') ';
       
        $sql .= $criteria; 
		return $this->oDbCon->doQuery($sql); 
   }
		
}
?>