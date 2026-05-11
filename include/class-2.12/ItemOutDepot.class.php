<?php
  
class ItemOutDepot extends BaseClass{ 
 
   function __construct(){
		
		parent::__construct();
       
		$this->tableName = 'item_out_depot_header';
		$this->tableNameDetail = 'item_out_depot_detail';
		$this->tableItemUnit = 'item_unit';
        $this->tableItem = 'item'; 
		$this->tableDepot = 'depot';
		$this->tableStatus = 'transaction_status';
		$this->tableCustomer = 'customer'; 
        $this->tableFile = 'item_out_depot_file';   
        $this->tableTruckingVendor = 'supplier';
        $this->isTransaction = true; 
		  
		$this->tableNeedToBeCopyOnCancel = array($this->tableNameDetail,$this->tableFile);
       
		$this->securityObject = 'ItemOutDepot';  
                 
        $this->arrDataDetail = array();  
        $this->arrDataDetail['pkey'] = array('hidDetailKey');
        $this->arrDataDetail['refkey'] = array('pkey','ref');
        $this->arrDataDetail['itemkey'] = array('hidItemKey'); 
        $this->arrDataDetail['qty'] = array('qty','number');
        $this->arrDataDetail['qtyinbaseunit'] = array('qty','number');
        $this->arrDataDetail['unitkey'] = array('selUnit');
        $this->arrDataDetail['baseunitkey'] = array('selUnit');
        $this->arrDataDetail['unitconvmultiplier'] = array('unitConvMultiplier','number'); 
       
        $this->arrData = array();  
        $this->arrData['pkey'] = array('pkey', array('dataDetail' => array('dataset' => $this->arrDataDetail)));
        $this->arrData['code'] = array('code');
        $this->arrData['trdate'] = array('trDate','date');
        $this->arrData['depotkey'] = array('selDepotKey');
        $this->arrData['customerkey'] = array('hidCustomerKey');
        $this->arrData['truckingvendorkey'] = array('hidTruckingVendorKey');
        $this->arrData['policenumber'] = array('policeNumber');
        $this->arrData['docode'] = array('doCode');
        $this->arrData['trdesc'] = array('trDesc');    
        $this->arrData['statuskey'] = array('selStatus'); 
         
        $this->fileType = array();
        $this->fileType[1] = array('title' => $this->lang['memoDocuments'], 'uploadFileFolder' => 'item-out-depot-memo/','mandatory'=>true);
        $this->fileType[2] = array('title' => $this->lang['spkDocuments'], 'uploadFileFolder' => 'item-out-depot-spk/','mandatory'=>true);
        $this->fileType[3] = array('title' => $this->lang['otherDocuments'], 'uploadFileFolder' => 'item-out-depot-others/' );
              
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'date','title' => 'date','dbfield' => 'trdate','default'=>true, 'width' => 150, 'align' =>'center','format' => 'datetime'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'customer','title' => 'customer','dbfield' => 'customername','default'=>true));
        array_push($this->arrDataListAvailableColumn, array('code' => 'carRegistrationNumber','title' => 'carRegistrationNumber','dbfield' => 'policenumber','default'=>true, 'width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'doCode','title' => 'doCode','dbfield' => 'docode','default'=>true, 'width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'depot','title' => 'depot','dbfield' => 'depotname','default'=>true, 'width' => 170));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
        array_push($this->arrDataListAvailableColumn, array('code' => 'desc','title' => 'note','dbfield' => 'trdesc',  'width' => 200));
 
        $this->printMenu = array();  
        array_push($this->printMenu,array('code' => 'printTransaction', 'name' => $this->lang['printTransaction'],  'icon' => 'print', 'url' => 'print/itemOutDepot'));    
       
   }
   
   function getQuery(){
	   
	   $sql = '
			SELECT '.$this->tableName.'.* ,
			   '.$this->tableDepot.'.name as depotname,
			   '.$this->tableCustomer.'.name as customername,
               '.$this->tableTruckingVendor.'.name as truckingvendorname,
			   '.$this->tableStatus.'.status as statusname
			FROM '.$this->tableStatus.', 
                 '.$this->tableName.'
                 left join 
                    '.$this->tableTruckingVendor.' on  '.$this->tableName.'.truckingvendorkey = '.$this->tableTruckingVendor.'.pkey, 
                 '.$this->tableDepot.',
                 '.$this->tableCustomer.'
			WHERE '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey and  '.$this->tableName.'.depotkey = '.$this->tableDepot.'.pkey
                  and  '.$this->tableName.'.customerkey = '.$this->tableCustomer.'.pkey
 	  ' .$this->criteria ; 
		 
        $sql .=  $this->getCompanyCriteria() ;
        return $sql;
    }
	  
	  
    function afterUpdateData($arrParam, $action){
        foreach($this->fileType as $key=>$row){ 
            $this->updateFile($arrParam['pkey'], $arrParam['token-item-file-uploader-'.$key], $arrParam['item-file-uploader-'.$key], $key);
        }
    }
    
    function afterStatusChanged($rsHeader){ 
        // retrieve latest status
        $rsHeader = $this->getDataRowById($rsHeader[0]['pkey']);
        if ($rsHeader[0]['statuskey'] == 2)
            $this->changeStatus($rsHeader[0]['pkey'],3); 
    }
      
        
    function validateForm($arr,$pkey = ''){
        
		$item = new Item();   
		$customer = new Customer();   
		  
		$arrayToJs = parent::validateForm($arr,$pkey); 
         
		$arrItemkey = $arr['hidItemKey']; 
		$arrSelUnit = $arr['selUnit']; 
		$arrQty = $arr['qty'];  
        $customerkey = $arr['hidCustomerKey'];
          
		
		//validasi kalo status gk menunggu gk bisa edit 
		if (!empty($pkey)){
			$rs = $this->getDataRowById($pkey);
			if ($rs[0]['statuskey'] <> 1){
				$this->addErrorList($arrayToJs,false,$this->errorMsg[212]);
			}
		} 
         
        $rsCustomer = $customer->getDataRowById($customerkey); 
        if(empty($rsCustomer)) 
			$this->addErrorList($arrayToJs,false,$this->errorMsg['customer'][1]);
 
		
        $arrDetailKeys = array(); 
         
		for($i=0;$i<count($arrItemkey);$i++) {
            $rsItem = $item->getDataRowById($arrItemkey[$i]);
            
		 	if (empty($arrItemkey[$i]) ){ 
				$this->addErrorList($arrayToJs,false, $this->errorMsg['item'][1]); 	
			} else{
                
                if ($this->unFormatNumber($arrQty[$i]) <= 0){
                    $this->addErrorList($arrayToJs,false,$rsItem[0]['name']. '. ' . $this->errorMsg[500]); 
                } 

                // cek punya konversi unit utk satuan yg dipilih gk  
                $conv = $item->getConvMultiplier($arrItemkey[$i],$arrSelUnit[$i]);
                if (empty($conv)){
                    $this->addErrorList($arrayToJs,false,$rsItem[0]['name']. '. ' . $this->errorMsg['itemUnitConversion'][3]); 
                }  

                // cek double atau tdk
                if (in_array($arrItemkey[$i],$arrDetailKeys)){  
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
         
        foreach($this->fileType as $key=>$row){ 
                 
            $mandatory = (isset($row['mandatory']) && !empty($row['mandatory'])) ? $row['mandatory'] : false;
             
            if($mandatory){
                $rsFile = $this->getItemFile($id,$key);
                if(empty($rsFile))
                    $this->addErrorLog(false,'<strong>'.$this->fileType[$key]['title'].'</strong>. '.$this->errorMsg[216]);
            }
            
        }
        
        $itemDepotMovement = new ItemDepotMovement();
        $rsDetail = $this->getDetailById($id);
        for($i=0;$i<count($rsDetail);$i++){
             $saldoakhir = $itemDepotMovement->getItemQOH($rsDetail[$i]['itemkey'],$rsHeader[0]['depotkey'],$rsHeader[0]['customerkey']);  
             $totalqty = $saldoakhir - $rsDetail[$i]['qtyinbaseunit'];  
             if($totalqty<0){
                $item = new Item();
                $rsItem = $item->getDataRowById($rsDetail[$i]['itemkey']);

                $this->addErrorLog(false,'<strong>'.$rsItem[0]['name'].'</strong>. '.$this->errorMsg[402]);
            }
        }
        
	 }
	  
 
	function confirmTrans($rsHeader){
        $id = $rsHeader[0]['pkey'];
		  
		$itemDepotMovement = new ItemDepotMovement();   
		 
	 	$rsDetail = $this->getDetailById($rsHeader[0]['pkey']); 
		
        $rsKey = $this->getTableKeyAndObj($this->tableName);
        
		for($i=0;$i<count($rsDetail); $i++){		
            
            $arrOptions = array( 'refkey' => $id, 
                                 'itemkey' => $rsDetail[$i]['itemkey'],
                                 'qtyinbaseunit' => -$rsDetail[$i]['qtyinbaseunit'],
                                 'tableType' => $rsKey['key'],
                                 'depotkey' => $rsHeader[0]['depotkey'],
                                 'customerkey' => $rsHeader[0]['customerkey'],
                                 'note' => '',
                                 'trdate' => $rsHeader[0]['trdate']
                               ); 
		   $itemDepotMovement->updateItemMovement($arrOptions);
		    
       }	 
        
		//update jurnal umum 
        //$this->updateGL($rsHeader); 
	} 

     
    function updateGL($rsHeader){
        
	 }
     
	 
	function cancelTrans($rsHeader,$copy){  
     
		$id = $rsHeader[0]['pkey'];
		  	 
		$itemDepotMovement = new ItemDepotMovement();  
        
        $rsKey = $this->getTableKeyAndObj($this->tableName);
		$itemDepotMovement->cancelMovement($id,$rsKey['key']); 
		 
		if ($copy)
			$this->copyDataOnCancel($id);	  
        
        //$this->cancelGLByRefkey($rsHeader[0]['pkey'],$this->tableName);
	} 
    
    function getDetailWithRelatedInformation($pkey,$criteria=''){
	   $sql = 'select
	   			'.$this->tableNameDetail .'.*,
                '.$this->tableItem.'.name as itemname, 
                '.$this->tableItem.'.code as itemcode, 
                '.$this->tableItem.'.width * '.$this->tableItem.'.length * '.$this->tableItem.'.height  * '.$this->tableNameDetail .'.qtyinbaseunit  as totalvolume, 
                '.$this->tableItem.'.gramasi * '.$this->tableNameDetail .'.qtyinbaseunit as totalweight, 
                '.$this->tableItemUnit.'.name as unitname,
                baseunit.name as baseunitname,
                weightunit.name as weightunitname
			  from
			  	'. $this->tableNameDetail .',
                '.$this->tableItem.',
                '.$this->tableItemUnit.',
                '.$this->tableItemUnit.' as weightunit,
                '.$this->tableItemUnit.' baseunit
			  where
			  	' . $this->tableNameDetail .'.itemkey = '.$this->tableItem.'.pkey and
			  	' . $this->tableNameDetail .'.unitkey = '.$this->tableItemUnit.'.pkey and
			  	' . $this->tableItem .'.baseunitkey = baseunit.pkey and
			  	' . $this->tableItem .'.weightunitkey = weightunit.pkey and
			  	'.$this->tableNameDetail .'.refkey = '.$this->oDbCon->paramString($pkey);
        
        $sql .= $criteria;
       
        
		return $this->oDbCon->doQuery($sql);
	
   }
    
    function getItemFile($pkey,$typekey){
		$sql = 'select * from '.$this->tableFile.' where refkey = '.$this->oDbCon->paramString($pkey);
        
        if(!empty($typekey)) 
            $sql .= 'and typekey = '.$this->oDbCon->paramString($typekey);
        
        $sql .= ' order by pkey asc';
		return $this->oDbCon->doQuery($sql);
    }
    
    function updateFile($pkey,$token,$arrFile,$typekey=1){		
		 
        if(!empty($arrFile)) 
            $this->validateDiskUsage(); 
        
        $uploadFileFolder = $this->fileType[$typekey]['uploadFileFolder'];
        
		$sourcePath = $this->uploadTempDoc.$uploadFileFolder.$token;
		$destinationPath = $this->defaultDocUploadPath.$uploadFileFolder;
			
		if(!is_dir($destinationPath)) 
			mkdir ($destinationPath,  0755, true);
			
		$destinationPath .= $pkey;  
		  
		//delete previous files	    
		$this->deleteAll($destinationPath);  
		$sql = 'delete from '.$this->tableFile.' where refkey = '. $this->oDbCon->paramString($pkey).'and typekey = '. $this->oDbCon->paramString($typekey); 
		$this->oDbCon->execute($sql); 
		 
		if(!is_dir($sourcePath)) 
			return;
	
		if (!empty($arrFile))	{
			$arrFile = explode(",",$arrFile);
			for ($i=0;$i<count($arrFile);$i++){   
				$this->uploadImage($sourcePath, $destinationPath,$arrFile[$i]);
				
				$imagekey = $this->getNextKey($this->tableFile);  
				
				$sql = 'insert into '.$this->tableFile.' (pkey,refkey,file,typekey) values ('.$this->oDbCon->paramString($imagekey).','.$this->oDbCon->paramString($pkey).','.$this->oDbCon->paramString($arrFile[$i]).','.$this->oDbCon->paramString($typekey).')';	
				$this->oDbCon->execute($sql);	 
				 
			}		
		} 
					
	}
    
    
    
    function normalizeParameter($arrParam, $trim=false){
        $car = new Car();
        
        $arrParam = parent::normalizeParameter($arrParam); 
          
        $arrParam['policeNumber'] = $car->normalizePoliceNumber($arrParam['policeNumber']);
        $arrParam['hidTruckingVendorKey'] = (empty($arrParam['hidTruckingVendorKey'])) ? '' : $arrParam['hidTruckingVendorKey'];
        
        for($i=0;$i<count($arrParam['hidDetailKey']);$i++)
            $arrParam['unitConvMultiplier'][$i] = 1;
         
        return $arrParam;
    }
     
  
}
?>
