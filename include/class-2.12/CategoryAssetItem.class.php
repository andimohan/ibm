<?php 

class CategoryAssetItem extends Category {

   function __construct() {

      parent::__construct();

      $this->tableName = 'category_asset_item';
      $this->tableStatus = 'master_status';

      $this->newLoad = true;
      $this->securityObject = 'AssetItem';

      $this->arrData = array();
      $this->arrData['pkey'] = array('pkey');
      $this->arrData['code'] = array('code');
      $this->arrData['name'] = array('name');
      $this->arrData['isleaf'] = array('isLeaf'); 
      $this->arrData['orderlist'] = array('orderList', 'number');
      $this->arrData['parentkey'] = array('selCategory');
      $this->arrData['statuskey'] = array('selStatus');
      $this->arrData['description'] = array('description');
      $this->arrData['coakey'] = array('hidCOAKey');


      $this->arrDataListAvailableColumn = array();
      array_push($this->arrDataListAvailableColumn, array('code' => 'code', 'title' => 'code', 'dbfield' => 'code', 'default' => true, 'width' => 100));
      array_push($this->arrDataListAvailableColumn, array('code' => 'name', 'title' => 'name', 'dbfield' => 'name', 'default' => true, 'width' => 150));
      array_push($this->arrDataListAvailableColumn, array('code' => 'prent', 'title' => 'parent', 'dbfield' => 'parentname', 'default' => true, 'width' => 150));
      array_push($this->arrDataListAvailableColumn, array('code' => 'desc', 'title' => 'note', 'dbfield' => 'description', 'default' => true, 'width' => 200));
      array_push($this->arrDataListAvailableColumn, array('code' => 'status', 'title' => 'status', 'dbfield' => 'statusname', 'default' => true, 'width' => 100));

      $this->arrSearchColumn = array();
      array_push($this->arrSearchColumn, array('Kode', $this->tableName . '.code'));
      array_push($this->arrSearchColumn, array('Nama', $this->tableName . '.name'));

      $this->includeClassDependencies(
         array(
            'ChartOfAccount.class.php',
         )
      );

      $this->overwriteConfig();

   }

   function getQuery()
   {

      $sql = '
			select
					' . $this->tableName . '.*,
					' . $this->tableStatus . '.status as statusname,
					parentcat.name as parentname
				from 
					' . $this->tableName . ' left join ' . $this->tableName . ' parentcat on 	parentcat.pkey = ' . $this->tableName . '.parentkey ,' . $this->tableStatus . ' 
				where  		
					' . $this->tableName . '.statuskey = ' . $this->tableStatus . '.pkey 
 		' . $this->criteria;

      return $sql;
   }

   function validateForm($arr, $pkey = '')
   {

      $arrayToJs = parent::validateForm($arr, $pkey);

      $name = $arr['name'];
      $orderlist = (!empty($arr['orderList'])) ? $this->unformatNumber($arr['orderList']) : 0;

      $pkeyCriteria = (!empty($pkey)) ? ' and ' . $this->tableName . '.pkey <> ' . $this->oDbCon->paramString($pkey) : '';

      $rsCategory = $this->searchData('', '', true, ' and ' . $this->tableName . '.name = ' . $this->oDbCon->paramString($name) . ' and ' . $this->tableName . '.parentkey = ' . $this->oDbCon->paramString($arr['selCategory']) . ' ' . $pkeyCriteria);
      
      if (empty($name)) {
         $this->addErrorList($arrayToJs, false, $this->errorMsg['categoryAssetItem'][1]);
      } else if (count($rsCategory) <> 0) {
         $this->addErrorList($arrayToJs, false, $this->errorMsg['categoryAssetItem'][2]);
      }

      if (!empty($orderlist)) {
         if (!is_numeric($orderlist)) {
            $this->addErrorList($arrayToJs, false, $this->errorMsg['orderList'][2]);
         }
      }


      return $arrayToJs;
   }

   function  afterStatusChanged($rs){
	   $this->updateLeaf(); 
   } 

   function compileChildArray($showWebstore = false){
		 
		$arrResult = array();
	 
        $orderby = 'order by orderlist asc, name asc';
             
        $webstoreCriteria = ($showWebstore) ? ' and '.$this->tableName . '.isshow = 1 '  : '';
        
		$rsChild = $this->searchData($this->tableName . '.statuskey',1,true,' and '.$this->tableName . '.parentkey = 0 ' .$webstoreCriteria,$orderby);
		$arrResult[0]['parentnode'] = -1; 
		$arrResult[0]['node'] = 0; 
		$arrResult[0]['childnode'] = $rsChild; 
		
		$rs = $this->searchData( $this->tableName . '.statuskey',1,true,$webstoreCriteria,$orderby);
		 
		for($i=0;$i<count($rs);$i++){ 
			$rsChild = $this->searchData($this->tableName . '.statuskey',1,true,' and '.$this->tableName . '.parentkey = ' . $this->oDbCon->paramString($rs[$i]['pkey']) .$webstoreCriteria,$orderby);
			$arrResult[$rs[$i]['pkey']]['parentnode'] = $rs[$i]['parentkey']; 
			$arrResult[$rs[$i]['pkey']]['node'] = $rs[$i]['pkey']; 
			$arrResult[$rs[$i]['pkey']]['childnode'] = $rsChild; 
		}
		
		return $arrResult;
	}

   function getLeafNodeWithPath($parentkey = 0, $pathSeparator = ' / ', $arrTemp = array(), &$arrPath = array())
   {
      $sql = 'select * from ' . $this->tableName . ' where ' . $this->tableName . '.parentkey = ' . $this->oDbCon->paramString($parentkey) . ' and statuskey = 1 order by name asc';
      $rs = $this->oDbCon->doQuery($sql);

      for ($i = 0; $i < count($rs); $i++) {

         if ($rs[$i]['parentkey'] == 0) {
            $arrTemp[$rs[$i]['pkey']] = array();
         } else {
            $arrTemp[$rs[$i]['pkey']] = $arrTemp[$rs[$i]['parentkey']];
         }

         array_push($arrTemp[$rs[$i]['pkey']], $rs[$i]['name']);

         if ($rs[$i]['isleaf'] == 1) {
            $arrResult = array();
            $arrResult['path'] = implode($pathSeparator, $arrTemp[$rs[$i]['pkey']]);
            $arrResult['pkey'] = $rs[$i]['pkey'];
            array_push($arrPath, $arrResult);
         } else {
            $this->getLeafNodeWithPath($rs[$i]['pkey'], $pathSeparator, $arrTemp, $arrPath);
         }

      }

      return $arrPath;

   }  


   function getCategoryTree($parentkey = 0, $pathSeparator = ' / ', $leafOnly = true){
		$sql = 'select * from '.$this->tableName.' where '.$this->tableName . '.parentkey = ' . $this->oDbCon->paramString($parentkey) .' and statuskey = 1 order by name asc';
		$rs = $this->oDbCon->doQuery($sql);  
		 
        $arrPath = array();
        
		for ($i=0;$i<count($rs);$i++){  
			
            $arrResult = array();
            $arrTemp = $this->getPath($rs[$i]['pkey'], $pathSeparator);
            $arrResult['path'] = $arrTemp[0]['path'];
            $arrResult['pkey'] = $rs[$i]['pkey']; 
            $arrResult['name'] = $rs[$i]['name'];  
            array_push($arrPath,$arrResult);

            $arr = $this->getCategoryTree($rs[$i]['pkey'], $pathSeparator,$leafOnly);
            $arrPath = array_merge($arrPath,$arr);
		}
		
		return $arrPath;
		
	}

   function getChildren($parentkey = 0, &$arrChild = array())
   {
      // utk mencari semua node dibawah node $parentkey
      $rs = $this->searchData($this->tableName . '.statuskey', 1, true, ' and ' . $this->tableName . '.parentkey = ' . $this->oDbCon->paramString($parentkey));

      for ($i = 0; $i < count($rs); $i++) {
         array_push($arrChild, $rs[$i]['pkey']);
         if ($rs[$i]['isleaf'] == 0)
            $this->getChildren($rs[$i]['pkey'], $arrChild);
      }

      return $arrChild;

   }

   function updateLeaf(){
        // bisa update khusus leaf tertentu, agar kalo tree nya byk, gk lemot
		// gk bisa karena kalo dia pindah lokasi tree, harus cek sebelum dan sesudah.. 
		// dan mungkin gk byk kategori sampe puluhan ribu
		
		$sql = 'update ' . $this->tableName . ' set isleaf =  0';
		$this->oDbCon->execute($sql);
			
		$rs = array ();
		
		$sql = 'select pkey from ' . $this->tableName . '
				where '.$this->tableName . '.parentkey =  0 and  
				' . $this->tableName . '.statuskey = 1  
			   order by orderlist asc';
		$rsTree = $this->oDbCon->doQuery($sql);	
		$this->updateLeafChild (array_column($rsTree,'pkey'),$rs); 
		 
	}

   function updateLeafChild ($arrChildKey,&$rs) {
		 		
		$rsCol = $this->searchDataRow( array('pkey','code','parentkey'),
									 ' and parentkey in (' .$this->oDbCon->paramString($arrChildKey,',').')  
									 and  ' . $this->tableName . '.statuskey = 1'); 
		$rsCol = $this->reindexDetailCollections($rsCol,'parentkey');
		
		for ($i=0;$i<count($arrChildKey);$i++) {   
			$childkey = $arrChildKey[$i];
			$rsTemp = (isset($rsCol[$childkey])) ? $rsCol[$childkey] : array();
				
			if (empty($rsTemp)){
				$sql = 'update ' . $this->tableName . ' set isleaf =  1 where pkey = ' .$this->oDbCon->paramString($childkey)   ; 
				$this->oDbCon->execute($sql);	
			}else{	
				$this->updateLeafChild (array_column($rsTemp,'pkey'),$rs);
			}
		}
	
	}

   


   function getPath($categorykey, $pathSeparator = ' / '){
        $arrPath = array();
        $arrTempPath = array();
         
        
        $rsCat = $this->getDataRowById($categorykey);  
        array_unshift($arrTempPath, $rsCat[0]['name']);  
        
        $arrResult = array();
        $arrResult['name'] = $rsCat[0]['name'];
        $arrResult['pkey'] = $rsCat[0]['pkey'];
        $arrResult['path'] = implode($pathSeparator,$arrTempPath); 
        array_unshift($arrPath, $arrResult);
        
        while($rsCat[0]['parentkey'] <> 0) { 
            $rsCat = $this->getDataRowById($rsCat[0]['parentkey']);  
            
            array_unshift($arrTempPath, $rsCat[0]['name']);  
            
            $arrResult = array();
            $arrResult['name'] = $rsCat[0]['name'];
            $arrResult['pkey'] = $rsCat[0]['pkey'];
            $arrResult['path'] = implode($pathSeparator,$arrTempPath);  
            array_unshift($arrPath, $arrResult); 
        }
        
        // update level (reverse)  
        $level = (empty($rsCat)) ? 0 : 1;
        for($i=count($arrPath)-1;$i>=0;$i--){
            $arrPath[$i]['level'] = $level++;
        }
 
        return $arrPath ;
    }

    function searchDataForAutoComplete($returnField, $searchOptions,$orderCriteria=''){ 
         
        $fieldname = $searchOptions['field'];
        $searchkey = $searchOptions['key'];
        $searchCriteria = (isset($searchOptions['criteria'])) ? $searchOptions['criteria'] : '';
            
		$sql = $this->generateDefaultQueryForAutoComplete($returnField);
	
		if(!empty($fieldname)){ 
			$sql .= ' and ' ; 
	        $sql .=  $fieldname .' like '. $this->oDbCon->paramString('%'.$searchkey.'%');
		}
				
		if($searchCriteria <> '')
			$sql .= ' ' .$searchCriteria;
	
		if($orderCriteria <> '') 
			$sql .= ' ' .$orderCriteria;
	     
         $rs = $this->oDbCon->doQuery($sql);	
         for($i=0;$i<count($rs);$i++) { 
            $rsPath = $this->getPath($rs[$i]['pkey']);
            $rs[$i]['name'] = $rs[$i]['value'] ; 
            $rs[$i]['value'] = htmlspecialchars_decode($rsPath[0]['path']); 
         }
        
         return $rs;
	} 

   function normalizeParameter($arrParam, $trim = false)
   {
      $arrParam = parent::normalizeParameter($arrParam, true);
      return $arrParam;
   }

}

?>