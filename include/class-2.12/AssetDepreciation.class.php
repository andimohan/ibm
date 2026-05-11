<?php

class AssetDepreciation extends BaseClass{

    function __construct()
    {

        parent::__construct();

        $this->tableName = 'asset_depreciation_header'; 
        $this->tableNameDetail = 'asset_depreciation_detail'; 
        $this->tableAsset = 'asset'; 
        $this->tableWarehouse = 'warehouse'; 
		$this->tableStatus = 'transaction_status';
		
		$this->isTransaction = true;
        $this->newLoad = true;

        $this->securityObject = 'AssetDepreciation'; 

		$this->arrDataDetail = array(); 
        $this->arrDataDetail['pkey'] = array('hidDetailKey');
        $this->arrDataDetail['refkey'] = array('pkey','ref');
        $this->arrDataDetail['assetkey'] = array('hidAssetKey', array('mandatory' => true)); 
        $this->arrDataDetail['value'] = array('depreciationValue','number');
		
        $arrDetails = array();
        array_push($arrDetails, array('dataset' => $this->arrDataDetail)); 
		
        $this->arrData = array();
        $this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails));
        $this->arrData['code'] = array('code');
        $this->arrData['trdate'] = array('trDate', 'date');
        $this->arrData['warehousekey'] = array('selWarehouseKey'); 
        $this->arrData['trdesc'] = array('trDesc'); 
        $this->arrData['grandtotal'] = array('grandtotal','number'); 
 
        $this->arrDataListAvailableColumn = array();
        array_push($this->arrDataListAvailableColumn, array('code' => 'code', 'title' => 'code', 'dbfield' => 'code', 'default' => true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'date', 'title' => 'date', 'dbfield' => 'trdate', 'default' => true, 'width' => 100, 'align' => 'center', 'format' => 'date'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'warehouse', 'title' => 'warehouse', 'dbfield' => 'warehousename', 'default' => true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'total', 'title' => 'total', 'dbfield' => 'grandtotal', 'default' => true, 'width' => 150, 'align' => 'right', 'format' => 'number'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status', 'title' => 'status', 'dbfield' => 'statusname', 'default' => true, 'width' => 70));
 

        $this->arrSearchColumn = array();
        array_push($this->arrSearchColumn, array('Kode', $this->tableName . '.code'));
        array_push($this->arrSearchColumn, array('Nama Gudang ', $this->tableWarehouse . '.name'));

        $this->includeClassDependencies(array( 
            'Asset.class.php', 
			'GeneralJournal.class.php'
        ));
 
        $this->overwriteConfig();
    }

    function getQuery(){

        $sql = '
			SELECT ' . $this->tableName . '.* ,
			   ' . $this->tableWarehouse . '.name as warehousename,
			   ' . $this->tableStatus . '.status as statusname
			FROM 
                 ' . $this->tableName . '
                 left join ' . $this->tableWarehouse . ' on ' . $this->tableName . '.warehousekey = ' . $this->tableWarehouse . '.pkey, 
                 ' . $this->tableStatus . '
			WHERE '
            . $this->tableName . '.statuskey = ' . $this->tableStatus . '.pkey
 		' . $this->criteria;
		
        $sql .=  $this->getCompanyCriteria();
        $sql .=  $this->getWarehouseCriteria();

        return $sql;
    }
 
    function normalizeParameter($arrParam, $trim = false) { 

		
		// update ulang nilai depresiasi
		$assetKey = $arrParam['hidAssetKey'];
		
		$asset = new Asset();
		$rsAsset = $asset->searchDataRow( array($asset->tableName.'.pkey',$asset->tableName.'.depreciationvalue',$asset->tableName.'.bookvalue'),
									   ' and ' . $asset->tableName.'.pkey in ('.$this->oDbCon->paramString($assetKey,',').') ');
		
		$rsAsset = array_column($rsAsset,null,'pkey');
		
		$totalDetail = count($assetKey);  
		for($i=0;$i<$totalDetail;$i++){
            // cek kalo nilai buku lebih kecil, pake nilai buku
			$arrParam['depreciationValue'][$i] = ($rsAsset[$assetKey[$i]]['depreciationvalue'] > $rsAsset[$assetKey[$i]]['bookvalue']) ? $rsAsset[$assetKey[$i]]['bookvalue'] : $rsAsset[$assetKey[$i]]['depreciationvalue'];
		}
		
		$reCountResult = $this->reCountSubtotal($arrParam); 
        $arrParam['grandtotal'] = $reCountResult['grandtotal'];
		
        $arrParam = parent::normalizeParameter($arrParam,true);

        return $arrParam;
    }

    function validateForm($arr, $pkey = '')  {

        $arrayToJs = parent::validateForm($arr, $pkey); 

        return $arrayToJs;
    }
 
  
    function validateConfirm($rsHeader)  {

        // kalo sudah 0 tdk boleh didepresiaasi lagi
        
        $asset = new Asset();
        
        $rsDetail = $this->getDetailById($rsHeader[0]['pkey']);
        $rsAssets = $asset->searchDataRow(array($asset->tableName.'.pkey',$asset->tableName.'.code',$asset->tableName.'.name', $asset->tableName.'.bookvalue'),
                                          ' and '.$asset->tableName.'.pkey in ('. $this->oDbCon->paramString(array_column($rsDetail,'assetkey'), ',').')'
                                         );
        
        foreach($rsAssets as $row){
            if(round($row['bookvalue']) <=0 )
                $this->addErrorLog(false, '<b>'.$row['code'].' - ' .$row['name'].'</b>. '.$this->errorMsg['asset'][5]);      
        }
                                           
            
    }

	function updateTotalDepreciatedCtr($assetkey){
		
		if(!is_array($assetkey)) $assetkey = array($assetkey);
		
		// update sudah berapa kali disusutkna
		$sql = 'select 
					count('.$this->tableName.'.pkey) as total,
					coalesce(sum('.$this->tableNameDetail.'.value),0) as depreciatedvalue,
					'.$this->tableNameDetail.'.assetkey from 
					'.$this->tableName.','.$this->tableNameDetail.'
				where
					'.$this->tableName.'.pkey = '.$this->tableNameDetail.'.refkey and
					'.$this->tableName.'.statuskey in (2,3) and
					'.$this->tableNameDetail.'.assetkey in (' . $this->oDbCon->paramString($assetkey, ',') .') 
				group by '.$this->tableNameDetail.'.assetkey
				';
		  
		$rs = $this->oDbCon->doQuery($sql);
		$rs = array_column($rs,null,'assetkey');
		
		foreach($assetkey as $assetPkey){
			
			$ctr = (isset($rs[$assetPkey])) ? $rs[$assetPkey]['total'] : 0;
			$depreciatedValue = (isset($rs[$assetPkey])) ? $rs[$assetPkey]['depreciatedvalue'] : 0;
			
			$sql = 'update '.$this->tableAsset.' set 
						totaldepreciatedctr = '. $this->oDbCon->paramString($ctr) .', 
						bookvalue = acquisitionvalue - initdepreciationvalue - '. $this->oDbCon->paramString($depreciatedValue) .'
					where '.$this->tableAsset.'.pkey = '. $this->oDbCon->paramString($assetPkey) ;
			   
			$this->oDbCon->execute($sql);
		}
		
	}
		
    function confirmTrans($rsHeader){

        $id = $rsHeader[0]['pkey'];
        //update jurnal umum 
        $this->updateGL($rsHeader);
    }

	
	function cancelTrans($rsHeader,$copy){  
		
		$id = $rsHeader[0]['pkey'];  
		
		if ($copy) $this->copyDataOnCancel($id);	  
		
        $this->cancelGLByRefkey($rsHeader[0]['pkey'],$this->tableName);
	} 
	
	
	function afterStatusChanged($rsHeader){   
        // retrieve latest status
        $rsHeader = $this->getDataRowById($rsHeader[0]['pkey']);
		
        if ($rsHeader[0]['statuskey'] == 2 || $rsHeader[0]['statuskey'] == 4){ 
			$rsDetail = $this->getDetailById($rsHeader[0]['pkey']);
			$this->updateTotalDepreciatedCtr(array_column($rsDetail,'assetkey'));
		}
		
        if ($rsHeader[0]['statuskey'] == 2){ 
            $this->changeStatus($rsHeader[0]['pkey'],3,'',false,true); 
		} 
		 
    }
	
	
	 
    function validateCancel($rsHeader,$autoChangeStatus=false){ 
		$id = $rsHeader[0]['pkey'];
  
		
 	 }
	
	 function getDetailWithRelatedInformation($pkey, $criteria = ''){

        $sql = 'select
	   			' . $this->tableNameDetail . '.*,
				'.$this->tableAsset.'.name as assetname,
				'.$this->tableAsset.'.code as assetcode
			  from
			  	' . $this->tableNameDetail . ',
				'.$this->tableAsset.'
			  where
			  ' . $this->tableNameDetail . '.assetkey = '.$this->tableAsset.'.pkey and
                ' . $this->tableNameDetail . '.refkey in (' . $this->oDbCon->paramString($pkey, ',') . ') ';

        $sql .= $criteria;

        return $this->oDbCon->doQuery($sql);
    }
	
	
	function getDetailDepreciationByAsset($pkey,$criteria = ''){
		
		$sql = 'select
					'.$this->tableNameDetail.'.*,
					'.$this->tableName.'.code,
					'.$this->tableName.'.trdate
				from
					'.$this->tableNameDetail.',
					'.$this->tableName.'
				where
					'.$this->tableNameDetail.'.refkey = '.$this->tableName.'.pkey and
					'.$this->tableNameDetail.'.assetkey in ('. $this->oDbCon->paramString($pkey, ',') . ') and
					('. $this->tableName.'.statuskey = 2 or '. $this->tableName.'.statuskey = 3) ';

		if(!empty($criteria))
            $sql .= $criteria;   
        
        $sql .= ' order by  pkey asc'; 
		
		return $this->oDbCon->doQuery($sql);
		
		
	}
	
	
	function getDetailDepreciationCollections($rs,$indexField,$criteria=''){ 
        $rsAllDetail = $this->getDetailDepreciationByAsset(array_column($rs,'pkey'),$criteria);    
        return $this->reindexDetailCollections($rsAllDetail,$indexField);
    }	
    function reCountSubtotal($arrParam)
    {  
        $subtotal = 0;
        
		for ($i = 0; $i < count($arrParam['hidDetailKey']); $i++) { 
			if(empty($arrParam['hidAssetKey'][$i])) continue;
			
            $detailSubtotal = $this->unFormatNumber($arrParam['depreciationValue'][$i]); 
            $subtotal += $detailSubtotal;
        }

		$grandtotal = $subtotal;
        
        $reCountResult = array(); 
        $reCountResult['grandtotal'] = $grandtotal;

        return $reCountResult;
    }

	

    function updateGL($rs){
        if (!USE_GL) return;

        $warehouse = new Warehouse();
        $generalJournal = new GeneralJournal();
        $coaLink = new COALink(); 
		$asset = new Asset();

        $warehousekey = $rs[0]['warehousekey'];

        $rsKey = $generalJournal->getTableKeyAndObj($this->tableName,array('key'));
        $arr = array();
        $arr['pkey'] = $generalJournal->getNextKey($generalJournal->tableName);
        $arr['code'] = 'xxxxx';
        $arr['refkey'] = $rs[0]['pkey'];
        $arr['refTableType'] = $rsKey['key'];
        $arr['trDate'] =  $this->formatDBDate($rs[0]['trdate'], 'd / m / Y');
        $arr['createdBy'] = 0;
        $arr['selWarehouseKey'] = $rs[0]['warehousekey'];
 
		$desc = $this->ucFirst($this->lang['depreciation']) . ' ' . $arr['trDate'];
        $arr['trDesc'] = $desc;

        $temp = -1;

        $rsDetail = $this->getDetailById($rs[0]['pkey']);  
		$rsCOA = $asset->getAssetCOAKey(array_column($rsDetail,'assetkey'),$warehousekey);
		$rsCOA = array_column($rsCOA,null,'pkey');
		
		// gabungin dulu per COA
		
		$arrCOACol = array();
		
        foreach ($rsDetail as $detail) { 
			$arrCOAkey = $rsCOA[$detail['assetkey']];
		
			$coakey = $arrCOAkey['coadepreciationkey'];
			if(!isset($arrCOACol[$coakey])){ 
				$arrCOACol[$coakey]['debit'] = 0;
				$arrCOACol[$coakey]['credit'] = 0;
			}
			
			$arrCOACol[$coakey]['debit'] += $detail['value'];

			
			$coakey = $arrCOAkey['coaaccumulatedkey'];
			if(!isset($arrCOACol[$coakey])){ 
				$arrCOACol[$coakey]['debit'] = 0;
				$arrCOACol[$coakey]['credit'] = 0;
			}
			
			$arrCOACol[$coakey]['credit'] += $detail['value'];
 
        }
		
		
        foreach ($arrCOACol as $coakey=>$detail) {   
            $temp++;
            $arr['hidCOAKey'][$temp] = $coakey;
            $arr['debit'][$temp] = $detail['debit'];
            $arr['credit'][$temp] = $detail['credit'];
			$arr['trdescDetail'][$temp] = $desc;

        }
 
        $arrayToJs = $generalJournal->addData($arr);

        if (!$arrayToJs[0]['valid'])
            throw new Exception('<strong>' . $rs[0]['code'] . '</strong>. ' . $this->errorMsg[504] . ' ' . $arrayToJs[0]['message']);
    }
 
     
}
