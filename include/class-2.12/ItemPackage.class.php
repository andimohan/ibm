<?php 
class ItemPackage extends BaseClass{
 
   function __construct(){
		
		parent::__construct();
       
		$this->tableName = 'item';  
		$this->tableNameDetail = 'item_package_detail';     
		$this->tableCategory = 'item_category';  
		$this->tableStatus = 'master_status';  
		$this->tableDescription = 'item_description';
		$this->tableImage = 'item_image'; 
        $this->tableItemUnit = 'item_unit';
		$this->tableUnitConversion = 'item_unit_conversion';
        $this->tableItemGroup = 'item_category_group';
		$this->uploadFolder = 'item/';
		$this->securityObject = 'Item';  
        $this->itemType = 3; 
       
        $this->arrData = array(); 
        $this->arrData['pkey'] = array('pkey');
        $this->arrData['code'] = array('code');
        $this->arrData['name'] = array('name');
        $this->arrData['ispackage'] = array('ispackage');
        $this->arrData['categorykey'] = array('hidCategoryKey');
        $this->arrData['itemtype'] = array('itemtype');  
        $this->arrData['revenuecoakey'] = array('hidRevenueCOAKey');  
        $this->arrData['sellingprice'] = array('sellingPrice','number');  
        $this->arrData['shortdescription'] = array('shortdescription');  
        $this->arrData['statuskey'] = array('selStatus');
        $this->arrData['commissiontype'] = array('selCommissionType');
        $this->arrData['commission'] = array('commissionValue','number');
         
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'name','title' => 'name','dbfield' => 'name','default'=>true,'width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'category','title' => 'category','dbfield' => 'categoryname','default'=>true,'width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'price','title' => 'price','dbfield' => 'sellingprice','default'=>true,'width' => 100, 'align'=>'right','format'=>'number'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'note','title' => 'note','dbfield' => 'trdesc','width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
    
		$this->overwriteConfig();
   }
   
   function getQuery(){ 
	   
	   return '
				select
					'.$this->tableName. '.*,
					'.$this->tableCategory. '.name as categoryname,
					'.$this->tableStatus.'.status as statusname  
				from 
					'.$this->tableName . ',
                    '.$this->tableStatus.' ,
                    '.$this->tableCategory. '
				where  		
                    itemtype = '.$this->itemType.' and
                    ispackage = 1 and
					'.$this->tableName . '.statuskey = '.$this->tableStatus.'.pkey and
					'.$this->tableName . '.categorykey = '.$this->tableCategory.'.pkey
                    
 		' .$this->criteria ; 
		 
   }
    
 
    function afterUpdateData($arrParam, $action){ 
        $pkey = $arrParam['pkey'];
        $this->updateDetail($pkey, $arrParam);
        //$this->updateDescription($pkey, $arrParam); 
        //$this->updateImage($pkey,$arrParam['token-item-image-uploader'], $arrParam['item-image-uploader']);
        //$this->updateUnitConversion($pkey,$arrParam);   
    }

   function addData($arrParam){ 
        $arrParam['ispackage'] = 1; 
        $arrParam['itemtype'] = $this->itemType;
		return parent::addData($arrParam); 	
	}
    
	
        
    function editData($arrParam){ 
        $arrParam['ispackage'] = 1; 
        $arrParam['itemtype'] = $this->itemType;  
        return parent::editData($arrParam);
	}    
     
    function updateDetail($pkey,$arrParam){
		
	 	$sql = 'delete from '.$this->tableNameDetail.' where refkey = '. $this->oDbCon->paramString($pkey);
		$this->oDbCon->execute($sql);
		 
		$arrItemkey = $arrParam['hidItemKey']; 
		$arrQty = $arrParam['qty']; 
		$arrPriceinunit = $arrParam['priceInUnit']; 
		$arrDiscountValueInUnit = $arrParam['discountValueInUnit']; 
		$arrDiscountType = $arrParam['selDiscountType']; 
		$arrUnitKey = $arrParam['selUnit']; 
		
        $item = new Item();
		        
     	for ($i=0;$i<count($arrItemkey);$i++){
			
			$rsItem = $item->getDataRowById($arrItemkey[$i]); 
			$baseunitkey = $rsItem[0]['baseunitkey'];
			$unitconvmultiplier = 1;
			
			 
		 	$qty =  $this->unFormatNumber($arrQty[$i]);
            $qtyinbaseunit = $arrParam['detailCOGS'][$i]['qtyInBaseUnit'];
            $unitconversionmultiplier = $arrParam['detailCOGS'][$i]['unitConvMultiplier'];
			$priceInUnit = $this->unFormatNumber($arrPriceinunit[$i]);
		 	$discount = $this->unFormatNumber($arrDiscountValueInUnit[$i]);
			$discountType =  $this->unFormatNumber($arrDiscountType[$i]);
		  
			$discountValue = $discount;
		 
			if ($discount != 0){
				if ($discountType == 2)
					$discountValue = $discount/100 * $priceInUnit;
			}
			
			
			$subtotal = $qtyinbaseunit * ($priceInUnit - $discountValue);  
			 
			$sql = 'insert into '.$this->tableNameDetail.' (
						refkey,
						itemkey,
						qty,  
						qtyinbaseunit,  
						unitkey,
						priceinunit, 
						priceinbaseunit, 
						unitconvmultiplier, 
						discounttype,
						discount,
						total 
					 ) values (
						'.$this->oDbCon->paramString($pkey).',
						'.$this->oDbCon->paramString($arrItemkey[$i]).',
						'.$this->oDbCon->paramString($qty).',
						'.$this->oDbCon->paramString($qtyinbaseunit).',
						'.$this->oDbCon->paramString($arrUnitKey[$i]).',
						'.$this->oDbCon->paramString($priceInUnit).',
						'.$this->oDbCon->paramString($priceInUnit).',
						'.$this->oDbCon->paramString($unitconversionmultiplier).', 
						'.$this->oDbCon->paramString($discountType).',
						'.$this->oDbCon->paramString($discount).', 
						'.$this->oDbCon->paramString($subtotal).' 
					)';	 
			$this->oDbCon->execute($sql);
                                        
		}
		 
					
	}
        
    
	function updateImage($pkey,$token,$arrImage){		
         
		$sourcePath = $this->uploadTempDoc.$this->uploadFolder.$token;
		$destinationPath = $this->defaultDocUploadPath.$this->uploadFolder;
		
		 
		if(!is_dir($sourcePath)) 
			return;
			
		if(!is_dir($destinationPath)) 
			mkdir ($destinationPath,  0755, true);
			
		$destinationPath .= $pkey;  
		  
		//delete previous images	    
		$this->deleteAll($destinationPath);  
		$sql = 'delete from '.$this->tableImage.' where refkey = '. $this->oDbCon->paramString($pkey);
		$this->oDbCon->execute($sql);
		  
		if (!empty($arrImage))	{
			$arrImage = explode(",",$arrImage);
			for ($i=0;$i<count($arrImage);$i++){   
                
                if ($i >= PLAN_TYPE['maxproductimage'])
                    continue;
				
				$newFileName = $this->hashFileName($sourcePath.$arrImage[$i]);
				$imagekey = $this->getNextKey($this->tableImage);  
				$this->uploadImage($sourcePath, $destinationPath,$arrImage[$i],$newFileName);
				
				$sql = 'insert into '.$this->tableImage.' (pkey,refkey,file) values ('.$this->oDbCon->paramString($imagekey).','.$this->oDbCon->paramString($pkey).', '.$this->oDbCon->paramString($newFileName).')';	
				$this->oDbCon->execute($sql);	 
				 
			}		
		}  
					
	} 
	 
	
	function validateForm($arr,$pkey = ''){
        $item = new Item(array(ITEM,SERVICE));
        
		$arrayToJs = parent::validateForm($arr,$pkey);  
		 
		$name = $arr['name'];   
		$sellingPrice = $this->unFormatNumber($arr['sellingPrice']); 
		$categorykey = $arr['hidCategoryKey'];
        $coaKey = $arr['hidCostCOAKey'];
        $arrItemKey = $arr['hidItemKey'];
        $arrUnit = $arr['selUnit'];
        $itemName = $arr['itemName'];
        
	  
        if($this->checkTotalItemLimitation($this->tableName,PLAN_TYPE['maxproduct'],$pkey))
            $this->addErrorList($arrayToJs,false,$this->errorMsg['limit'][1] . ' ('.$this->lang['max'].' '. $this->formatNumber(PLAN_TYPE['maxproduct']). ' '. strtolower($this->lang['items']).')');  
        
        if (!empty($arr['item-image-uploader'])){
            $arrImage = explode(",",$arr['item-image-uploader']);
            if(count($arrImage) > PLAN_TYPE['maxproductimage'])
                $this->addErrorList($arrayToJs,false,$this->errorMsg['limit'][2] .' ('.$this->lang['max'].' '. $this->formatNumber(PLAN_TYPE['maxproductimage']). ' '. strtolower($this->lang['images']).')' );

            for($i=0;$i<count($arrImage);$i++){
                $path = $this->uploadTempDoc.$this->uploadFolder.$arr['token-item-image-uploader'];  
                if (filesize($path.'/'.$arrImage[$i]) >  (pow(1024,2) * PLAN_TYPE['maximagesize'])  )
                    $this->addErrorList($arrayToJs,false,$this->errorMsg['limit'][4] .' ('.$this->lang['max'].' '. $this->formatNumber(PLAN_TYPE['maximagesize']). ' MB)' );
            }
            
        } 
        
        if(!empty($arrItemKey)){ 
            $rsItem = $item->searchData('','',true,' and '.$this->tableName.'.pkey in ('.$this->oDbCon->paramString($arrItemKey,',').')');
            $arrBaseUnitKey = array_column($rsItem, null, 'pkey'); 
            
            for($i=0;$i<count($arrItemKey);$i++){
                
                $itemkey = $arrItemKey[$i];
                
                if(!isset($arrBaseUnitKey[$itemkey])){ 
                     $this->addErrorList($arrayToJs,false,$itemName[$i]. '. '.$this->errorMsg[200]);  
                }else{  
                    if ($arrUnit[$i] <> $arrBaseUnitKey[$itemkey]['baseunitkey'])
                        $this->addErrorList($arrayToJs,false,$arrBaseUnitKey[$itemkey]['name']. '. '.$this->errorMsg['item'][4]);  
                }
            }
        }
        
		$rsItem = $this->isValueExisted($pkey,'name',$name);	 
		if(empty($name)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['service'][1]);
		}else if(count($rsItem) <> 0){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['service'][2]);
		}
		 
		if (empty($isVariant) && empty($categorykey)){ 
				$this->addErrorList($arrayToJs,false,$this->errorMsg['category'][1]); 
		}
         
		if (!is_numeric($sellingPrice) || $sellingPrice < 0){ 
			$this->addErrorList($arrayToJs,false, $this->errorMsg['sellingPrice'][2]);
		}
		   
		return $arrayToJs;
	 } 
	  
	function getItemDescription($pkey){
		$sql = 'select * from  '.$this->tableDescription.' where refkey = '.$this->oDbCon->paramString($pkey).' order by pkey asc';	
		return $this->oDbCon->doQuery($sql); 
	} 
	function getItemImage($pkey ){  
		$sql = 'select * from '.$this->tableImage.' where refkey = '.$this->oDbCon->paramString($pkey).' order by  pkey asc';	
	 	return $this->oDbCon->doQuery($sql);
    }  
	 
	  
	function updateDescription($pkey,$arrParam){
		
        if (!isset($arrParam['txtDescriptionLabel']))
            return;
        
	 	$itemDescriptionLabel = $arrParam['txtDescriptionLabel'];
		$itemDescription = $arrParam['txtDescription'];
		 
			$sql = 'delete from '.$this->tableDescription.' where refkey = '. $this->oDbCon->paramString($pkey);
			$this->oDbCon->execute($sql);
			
			for ($i=0;$i<count($itemDescription);$i++){
				
                if(!isset($itemDescriptionLabel[$i]))
                    continue;
                
				$label = trim($itemDescriptionLabel[$i]);
				$value = trim($itemDescription[$i]);
				 
			 	if (empty($label) && empty($value))  
					continue; 
			   
				$sql = 'insert into  '.$this->tableDescription.' (refkey,label,value) values ('.$this->oDbCon->paramString($pkey).','.$this->oDbCon->paramString($label).',  \''.addslashes($value).'\' )';	
		 
				$this->oDbCon->execute($sql);
				 
			}
					 
	}
	  
    
	 function updateUnitConversion($itemkey,$arrParam){ 
			$baseUnitKey = 1;
			$conversionUnitKey = 1;
			$conversionMultiplier = 1;
		    
            // untuk konversi default 1 pcs
            // cek utk base unit dan konversi unit yg sama udah ada blm (yg autoinsert sama islock)
            // kalo blm ad baru add
         
            $rsItemUnit = $this->getItemUnitConversion($itemkey);
         
            $found = false;
            for ($i=0;$i<count($rsItemUnit);$i++){
                if ($rsItemUnit[$i]['baseunitkey'] == $baseUnitKey  && $rsItemUnit[$i]['conversionunitkey'] == $baseUnitKey){
                    $found = true;
                    break;
                }
            } 
            if (!$found){ 
                $sql = 'insert into  '.$this->tableUnitConversion.' (refkey,baseunitkey,conversionunitkey,conversionmultiplier,isautoinsert) values ('.$this->oDbCon->paramString($itemkey).','.$this->oDbCon->paramString($baseUnitKey).','.$this->oDbCon->paramString($baseUnitKey).',1,1)';	
                $this->oDbCon->execute($sql);
            } 
			 
	 }
	  
    function getItemUnitConversion($pkey,$toUnitKey = '', $criteria = '', $orderby = ''){
     
		$sql = 'select 
                    '.$this->tableUnitConversion.'.* 
                from 
                    '.$this->tableUnitConversion.' 
                where  
                    refkey = '.$this->oDbCon->paramString($pkey);
        
        if (!empty($toUnitKey))
            $sql .= ' and conversionunitkey = ' .$this->oDbCon->paramString($toUnitKey); 
        
        $sql .= (!empty($criteria)) ? ' ' . $criteria : '';
        $sql .= (!empty($orderby)) ? ' ' . $orderby : ''; 
            
        $rs  = $this->oDbCon->doQuery($sql);  
          
        return $rs;
	}
    
	function delete($id, $forceDelete = false,$reason = ''){ 
		$arrayToJs =  array();
		 
		try{			 
				 
				$arrayToJs = $this->validateDelete($id);
				if (!empty($arrayToJs)) 
					return $arrayToJs;
		 		
				if (!$this->oDbCon->startTrans())
					throw new Exception($this->errorMsg[100]);
			 
				
				$sql = 'delete from  '.$this->tableName.' where pkey = ' . $this->oDbCon->paramString($id);
				$this->oDbCon->execute($sql);
				
				$sql = 'delete from  '.$this->tableDescription.' where refkey = '. $this->oDbCon->paramString($id);
				$this->oDbCon->execute($sql);
				 
				$sql = 'delete from '.$this->tableImage.' where refkey = '. $this->oDbCon->paramString($id);
				$this->oDbCon->execute($sql);
				$this->deleteAll($this->defaultDocUploadPath.$this->uploadFolder.$id);
			 	
                $sql = 'delete from '.$this->tableUnitConversion.' where refkey = '. $this->oDbCon->paramString($id);
				$this->oDbCon->execute($sql);	 
            
                $this->setTransactionLog(DELETE_DATA,$id);
            
				$this->oDbCon->endTrans();
										 
				$this->addErrorList($arrayToJs,true,$this->lang['dataHasBeenSuccessfullyUpdated']);    
			 
				
			}catch(Exception $e){
				$this->oDbCon->rollback();
				$this->addErrorList($arrayToJs,false, $e->getMessage()); 
		}			
			
		return $arrayToJs;	
	}
	 	  
	 function searchDataForAutoComplete($fieldname='',$searchkey='',$mustmatch=false,$searchCriteria='',$orderCriteria='', $limit=''){
		$sql = 'select
					'.$this->tableName. '.pkey,
                    '.$this->tableName. '.name as value, 
                    '.$this->tableName. '.code as code,
                    '.$this->tableName. '.sellingprice 
				from 
					'.$this->tableName . ', 
                    '.$this->tableStatus.'
				where  		 
					'.$this->tableName . '.statuskey = '.$this->tableStatus.'.pkey 
			';
	
		if(!empty($fieldname)){
			
			$sql .= ' and ' ;
			
			if($mustmatch)
				$sql .=  $fieldname .' = '. $this->oDbCon->paramString($searchkey);
			else
				$sql .=  $fieldname .' like '. $this->oDbCon->paramString('%'.$searchkey.'%');
		}
				
		if($searchCriteria <> '')
			$sql .= ' ' .$searchCriteria;
	
		if($orderCriteria <> ''){
			$sql .= ' ' .$orderCriteria;
	 
	 	}
			
		if($limit <> '')
			$sql .= ' ' .$limit;
		    
		return $this->oDbCon->doQuery($sql);	
	} 
 
    
     function normalizeParameter($arrParam, $trim = false){ 
         
        $arrParam['chkIsFixedCost'] = (isset($arrParam['chkIsFixedCost'])) ?  1 : 0 ;
        $arrParam['chkIsReimburse'] = (isset($arrParam['chkIsReimburse'])) ?  1 : 0 ;
        $arrParam['chkShowInTrucking'] = (isset($arrParam['chkShowInTrucking'])) ?  1 : 0 ;
        $arrParam['chkShowInCostRate'] = (isset($arrParam['chkShowInCostRate'])) ?  1 : 0 ;
        $arrParam['chkShowInDepot'] = (isset($arrParam['chkShowInDepot'])) ?  1 : 0 ;
        $arrParam['chkShowInTerminal'] = (isset($arrParam['chkShowInTerminal'])) ?  1 : 0 ;
        $arrParam['chkShowInShippingCompany'] = (isset($arrParam['chkShowInShippingCompany'])) ?  1 : 0 ;
        $arrParam['rdbChargeType'] = (isset($arrParam['rdbChargeType'])) ?  $arrParam['rdbChargeType'] : 0 ;
             
        $arrParam['hidCategoryKey'] = (isset($arrParam['hidCategoryKey'])) ? $arrParam['hidCategoryKey'] : 0 ; 
        $arrParam['hidCostCOAKey'] = (isset($arrParam['hidCostCOAKey'])) ? $arrParam['hidCostCOAKey'] : 0 ; 
        $arrParam['hidRevenueCOAKey'] = (isset($arrParam['hidRevenueCOAKey'])) ? $arrParam['hidRevenueCOAKey'] : 0 ; 
        $arrParam['finalDiscount'] = (isset($arrParam['finalDiscount'])) ? $arrParam['finalDiscount'] : 0 ; 
        $arrParam['selFinalDiscountType'] = (isset($arrParam['selFinalDiscountType'])) ? $arrParam['selFinalDiscountType'] : 1 ;  
        $arrParam['shipmentFee'] = (isset($arrParam['shipmentFee'])) ? $arrParam['shipmentFee'] : 0 ; 
        $arrParam['etcCost'] = (isset($arrParam['etcCost'])) ? $arrParam['etcCost'] : 0 ; 
              
        $reCountResult = $this->reCountSubtotal($arrParam);
        $arrParam['detailCOGS'] = $reCountResult['detailCOGS']; 
         
        return $arrParam; 
    }
    
    
    function getDetailWithRelatedInformation($pkey,$criteria=''){
    
	   $sql = 'select
	   			'.$this->tableNameDetail .'.*, 
                '.$this->tableName.'.name as itemname, 
                '.$this->tableName.'.cogs, 
                '.$this->tableName.'.code as itemcode,   
                '.$this->tableItemUnit.'.name as unitname 
			  from  
                '.$this->tableName.' 
                    left join '.$this->tableNameDetail .' on
			  	      '.$this->tableNameDetail .'.itemkey = '.$this->tableName.'.pkey   
                    left join '.$this->tableItemUnit .' on
			  	      '.$this->tableNameDetail.'.unitkey = '.$this->tableItemUnit.'.pkey  
			  where 
			  	refkey = '.$this->oDbCon->paramString($pkey) . ' ';
       
        $sql .= $criteria;
        
		return $this->oDbCon->doQuery($sql);
	
   }
    
    
	function reCountSubtotal($arrParam){
 
               
				$subtotal = 0 ;
				$grandtotal = 0;
				
				$arrItemKey = $arrParam['hidItemKey'];
				 
				$finalDiscount = $this->unFormatNumber($arrParam['finalDiscount']); 
				$finalDiscountType = $arrParam['selFinalDiscountType'];
				 
				$arrQty = $arrParam['qty']; 
				$arrPriceinunit = $arrParam['priceInUnit']; 
				$arrDiscountValueInUnit = $arrParam['discountValueInUnit']; 
				$arrDiscountType = $arrParam['selDiscountType']; 
				$arrTransUnitKey = $arrParam['selUnit']; 
				 
				$arrItemDetail = array();
				$item = new Item(); 
				
				for ($i=0;$i<count($arrItemKey);$i++){
					
					if (empty($arrItemKey[$i]))  
						continue; 
                    
                        $rsItem = $item->getDataRowById($arrItemKey[$i]);

                        $transactionUnitKey = $arrTransUnitKey[$i];
                        $baseunitkey = $rsItem[0]['baseunitkey']; 
                        $itemkey = $arrItemKey[$i];
						$qty =  $this->unFormatNumber($arrQty[$i]);
                        $conversionMultiplier = $item->getConvMultiplier($itemkey,$transactionUnitKey,$baseunitkey); 
                        $qtyinbaseunit = $qty * $conversionMultiplier;
						$priceInUnit = $this->unFormatNumber($arrPriceinunit[$i]);
						$discount = $this->unFormatNumber($arrDiscountValueInUnit[$i]);
						$discountType =  $this->unFormatNumber($arrDiscountType[$i]);
					 
					 	if ($discount != 0 && $discountType == 2){
							$discount = $discount/100 * $priceInUnit;
						}
						
						$detailSubtotal = $qtyinbaseunit * ($priceInUnit - $discount);

                        $arrItemDetail[$i]['baseUnitKey'] = $baseunitkey;
                        $arrItemDetail[$i]['unitConvMultiplier'] = $conversionMultiplier;
                        $arrItemDetail[$i]['qtyInBaseUnit'] = $qtyinbaseunit ; 
						$arrItemDetail[$i]['unitDiscountValue'] = $discount;
						$arrItemDetail[$i]['detailSubtotal'] = $detailSubtotal;
						
						$subtotal += $detailSubtotal ; 
				} 
				  
				$grandtotal = $subtotal;
				
				if ($finalDiscount != 0){
					if ($finalDiscountType == 2)
						$finalDiscount = $finalDiscount/100 * $grandtotal;
				} 
				 
                $totalFinalDiscountAndPointValue = $finalDiscount;
                
                for ($i=0;$i<count($arrItemKey);$i++){
					
					if (empty($arrItemKey[$i]))  
						continue;

                    $qtyinbaseunit = $arrItemDetail[$i]['qtyInBaseUnit'];
                    $priceInUnit = $this->unFormatNumber($arrPriceinunit[$i]); 

                    $unitDiscountedValue = $priceInUnit - $arrItemDetail[$i]['unitDiscountValue'] ;
                    //$priceInUnitBeforeTax = $unitDiscountedValue - (($unitDiscountedValue/$subtotal) * $totalFinalDiscountAndPointValue);

                    $rsItem = $item->getDataRowById($arrItemKey[$i]);
                    $arrItemDetail[$i]['cogs'] = $rsItem[0]['cogs'];	
                    $arrItemDetail[$i]['profit'] = 0; //$priceInUnitBeforeTax - $rsItem[0]['cogs'];
 
				}
        
        
				$beforeTaxTotal = $subtotal - $totalFinalDiscountAndPointValue;
				$grandtotal = $beforeTaxTotal;
 			 
				//$grandtotal +=  $shipmentFee + $etcCost;
				
				$reCountResult = array();
				$reCountResult['subtotal'] = $subtotal; 
				$reCountResult['grandtotal'] = $grandtotal; 
				$reCountResult['detailCOGS'] = $arrItemDetail;
				
				return $reCountResult;
				
	}
       
    function searchCategoryGroup($refname = '',$criteria = ''){
        
        $sql = 'select 
                    '.$this->tableItemGroup.'.* 
                from
                    '.$this->tableItemGroup.' 
                where
                    1=1'
            ;
        
        if (!empty($refname))
            $sql .= ' and ref = ' . $this->oDbCon->paramString($refname);
         
        
        if (!empty($criteria))
            $sql .= $criteria;
          
        return $this->oDbCon->doQuery($sql);
    }
    
    
     function searchItemByGroupCategory($ref){
        $rs = $this->searchCategoryGroup($ref);
        $arrCat = array_column($rs,'categorykey');
        
        $rs = array();
        
        if (!empty($arrCat)){ 
            $arrCat = implode(',',$arrCat);   
            $rs = $this->searchData( $this->tableName.'.statuskey',1,true,' and '.$this->tableName.'.categorykey in ('.$arrCat.')');
        }
            
        return  $rs;
    }
    
    
  } 

?>
