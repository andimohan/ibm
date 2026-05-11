<?php
	 
include '../../_config.php';  
include '../../_include.php';
include '_global.php';

$obj= $itemPackage;
$securityObject = 'reportItem'; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true));
$hasCOGSAccess = $security->isAdminLogin($item->cogsSecurityObject,10);  

$arrFilterInformation = array();
$detailCriteria = ''; 

// ===== FOR EXPORT SECTION
$dataToExport = array();

/* data structure */
$arrTemplate = array();

$arrDataStructure = array();
$arrDataStructure['rowNumber'] = array('title'=>'#', 'align'=>'right', 'width'=>"40px", 'autoNumber' => true, "sortable" => false);
$arrDataStructure['code'] = array('title'=>ucwords($obj->lang['code']),  'width'=>"100px", 'dbfield' => 'code');
$arrDataStructure['name'] = array('title'=>ucwords($obj->lang['name']),  'width'=>"350px", 'dbfield' => 'name');
$arrDataStructure['itemCategory'] = array('title'=>ucwords($obj->lang['itemCategory']),  'width'=>"180px", 'dbfield' => 'categoryname','mergeExcelCell' => 2); 
$arrDataStructure['sellingPrice'] = array('title'=>ucwords($obj->lang['sellingPrice']),'dbfield' => 'sellingprice', 'width'=>"90px" ,'format'=>'number');
$arrDataStructure['commission'] = array('title'=>ucwords($obj->lang['commission']),'dbfield' => 'commission', 'width'=>"95px" ,'format'=>'number');
$arrDataStructure['commissionType'] = array('title'=>'','width'=>"60px", 'dbfield' => 'commissiontype', 'class' => 'text-muted');
$arrDataStructure['status'] = array('title'=>ucwords($obj->lang['status']),'dbfield' => 'statusname', 'width'=>"100px");
		  
$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $obj->lang['itemPackage'];
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();

array_push($arrTemplate, $arrHeaderTemplate);

// detail ...
$arrDataDetailStructure = array();
$arrDataDetailStructure['itemcode'] = array('title'=>ucwords($obj->lang['itemCode']),  'dbfield' => 'itemcode', 'width'=>'100px', 'format' => 'string'  ); 
$arrDataDetailStructure['itemname'] = array('title'=>ucwords($obj->lang['itemName']),  'dbfield' => 'itemname', 'width'=>'200px'); 
$arrDataDetailStructure['qty'] = array('title'=>ucwords($obj->lang['qty']),  'dbfield' => 'qty', 'width'=>"60px", 'format' => 'number' ); 
$arrDataDetailStructure['unit'] = array('title'=>ucwords($obj->lang['unit']),  'dbfield' => 'unitname', 'width'=>"60px"  ); 
$arrDataDetailStructure['priceinbaseunit'] = array('title'=>ucwords($obj->lang['price']),  'dbfield' => 'priceinunit', 'width'=>"80px", 'format' => 'number' ); 
$arrDataDetailStructure['discount'] = array('title'=>ucwords($obj->lang['discount']),'width'=>"80px", 'dbfield' => 'discount','format' => 'number'); 
$arrDataDetailStructure['discountType'] = array('title'=>'','width'=>"60px", 'dbfield' => 'discounttype', 'class' => 'text-muted'); 
$arrDataDetailStructure['subtotal'] = array('title'=>ucwords($obj->lang['subtotal']), 'dbfield' => 'total', 'width'=>"95px" ,'format'=>'number', 'calculateTotal' => true);

$arrDetailTemplate = array();
$arrDetailTemplate['reportWidth'] = "800px";
$arrDetailTemplate['dataStructure'] = $arrDataDetailStructure;
$arrDetailTemplate['total'] = array();

array_push($arrTemplate, $arrDetailTemplate);
	
if (isset($_POST) && !empty($_POST['hidAction'])){  
		
	$criteria = '';
	if(isset($_POST) && !empty($_POST['itemCode'])) {
		$criteria .= ' AND '.$obj->tableName.'.code LIKE ('.$class->oDbCon->paramString('%'.$_POST['itemCode'].'%').')';
		array_push($arrFilterInformation,array("label" => 'Kode', 'filter' => $_POST['itemCode']));
	}
	if(isset($_POST) && !empty($_POST['itemName'])) {
		$criteria .= ' AND '.$obj->tableName.'.name LIKE ('.$class->oDbCon->paramString('%'.$_POST['itemName'].'%').')'; 
		array_push($arrFilterInformation,array("label" => 'Nama', 'filter' => $_POST['itemName']));
	}
	if(isset($_POST) && !empty($_POST['selCategory'])) { 
         
        $key = implode(",", $class->oDbCon->paramString($_POST['selCategory']));   
        
        $criteria .= ' AND categorykey in('.$key.')';  

        $rsCriteria = $itemCategory->searchData('','',true, ' and '.$itemCategory->tableName.'.pkey in ('.$key.')');
	 
        $arrTempCategory = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempCategory,$rsCriteria[$k]['name']);
			
		$statusName = implode(", ",$arrTempCategory); 
	    array_push($arrFilterInformation,array("label" => 'Kategori', 'filter' => $statusName));
	}
    
    if(isset($_POST) && !empty($_POST['selBrand'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selBrand']));   
        
       	$criteria .= ' AND '.$obj->tableBrand.'.pkey in('.$key.')';  

        $rsCriteria = $brand->searchData('','',true, ' and '.$brand->tableName.'.pkey in ('.$key.')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$statusName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => 'Merk', 'filter' => $statusName));
        
	}
    
	if(isset($_POST) && !empty($_POST['selWarehouse'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selWarehouse']));   
        
       	$criteria .= ' AND warehousekey in('.$key.')';  

        $rsCriteria = $warehouse->searchData('','',true, ' and '.$warehouse->tableName.'.pkey in ('.$key.')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$statusName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => 'Gudang', 'filter' => $statusName ));
        
	}	
    
    if (PLAN_TYPE['usefrontend'] == 1){
        if(isset($_POST) && !empty($_POST['selFilter'])) { 

            $key = implode(",", $class->oDbCon->paramString($_POST['selFilter']));     

            $rsFilterCategory = $filterCategory->searchData($filterCategory->tableName.'.statuskey',1);

            $arrIntersect = array();
            for ($i=0;$i<count($rsFilterCategory);$i++){
                $rsFilter = $itemFilter->searchData($itemFilter->tableName.'.statuskey',1,true, ' and categorykey = ' . $obj->oDbCon->paramString($rsFilterCategory[$i]['pkey']));
                $rsFilterKey = array_column($rsFilter, 'pkey');
                $rsIntersectFilterKey = array_intersect($rsFilterKey,$_POST['selFilter']);

                if(empty($rsIntersectFilterKey))
                    continue;

                $rsFilteredItem = $itemFilter->getItemInFilter($rsIntersectFilterKey); 
                $rsFilteredItemKey = array_column($rsFilteredItem, 'itemkey'); 


                if (empty($arrIntersect)) 
                    $arrIntersect = $rsFilteredItemKey;
                 else 
                    $arrIntersect = array_intersect($arrIntersect,$rsFilteredItemKey);

            }

            $criteria .=  ' and '.$obj->tableName.'.pkey in ('.implode(',',$arrIntersect).')'; 

            $rsCriteria = $itemFilter->searchData('','',true, ' and '.$itemFilter->tableName.'.pkey in ('.$key.')');

            $arrTempStatus = array();
            for ($k=0;$k<count($rsCriteria);$k++)
                array_push($arrTempStatus,$rsCriteria[$k]['name']);

            $statusName = implode(", ",$arrTempStatus); 
            array_push($arrFilterInformation,array("label" => 'Filter', 'filter' => $statusName ));

        }	
    }
	
 
	if(isset($_POST) && !empty($_POST['selStatus'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selStatus']));   
        
       	$criteria .= ' AND '.$obj->tableName.'.statuskey in('.$key.')';  

        $rsCriteria =  $obj->getStatusById ($key);
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['status']);
			
		$statusName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => 'Status', 'filter' => $statusName));
        
	}
    
    /*$groupCriteria = ''; 
	if(isset($_POST) && !empty($_POST['chkAvailable'])){ 
        $groupCriteria = 'having qtyonhand > 0';
    }*/
     
		 
  
    $orderBy = (!empty($_POST['hidOrderBy'])) ? $obj->oDbCon->paramOrder($_POST['hidOrderBy']) : 'pkey'; // order by harus dr kolom yg terdaftar saja
    $orderType = (isset($_POST['hidOrderType']) && !empty($_POST['hidOrderType']) && $_POST['hidOrderType'] == 1) ? 'desc' : 'asc';

	
	 
	$order = 'order by '.$orderBy.' ' .$orderType; 
      
	$rs = $obj->searchData('','',true,$criteria,$order,'',$groupCriteria);
     
		$temp = 1;
		$tempreport = ''; 
    
		if (empty($rs))
            $tempreport .= '<tr class="report-row rewrite-row"><td colspan="'.count($arrHeaderTemplate['dataStructure']).'"></td></tr>';
    
		for( $i=0;$i<count($rs);$i++) {   
		
			if(!$hasCOGSAccess) 
				$rs[$i]['cogs']  = 0;  
			
            $rs[$i]['totalcogs'] = $rs[$i]['qtyonhand'] * $rs[$i]['cogs'];
            
            if($rs[$i]['commissiontype']==1)
                    $rs[$i]['commissiontype'] = 'IDR';
                else
                    $rs[$i]['commissiontype'] = '%';
                
            // has detail
            $rsDetail = $obj->getDetailWithRelatedInformation($rs[$i]['pkey'],$detailCriteria); 
           
            for ($j=0;$j<count($rsDetail);$j++){     
                if($rsDetail[$j]['discounttype']==1)
                    $rsDetail[$j]['discounttype'] = 'IDR';
                else
                    $rsDetail[$j]['discounttype'] = '%';
            }
            
            // has detail
            $rs[$i]['_detail_'] = array('arrTemplate'=>$arrDetailTemplate,'data' => $rsDetail);
                  
            $return = $obj->formatReportRows(array('data' => $rs[$i]),$arrTemplate); 
   
            array_push($dataToExport, $return['data']);  
            
            $tempreport .= $return['html'];
            $arrTemplate[0]['total'] = $obj->arraySum($arrTemplate[0]['total'], $return['subtotal'][0]);
		}
		
		$obj->generateReport($_POST, $tempreport, $arrTemplate,$dataToExport,$arrFilterInformation);
}

$arrWarehouse = $class->convertForCombobox($warehouse->searchData($warehouse->tableName.'.statuskey',1,true,'','order by name asc'),'pkey','name');
$arrStatus = $class->convertForCombobox($obj->getAllStatus(),'pkey','status');  
$arrCategory = $class->convertForCombobox($itemCategory->searchData($itemCategory->tableName.'.statuskey',1,true, ' and '.$itemCategory->tableName.'.isleaf = 1', ' order by name asc'),'pkey','name');   
$arrBrand = $class->convertForCombobox($brand->searchData('','',true, ' and '.$brand->tableName.'.statuskey = 1','order by name asc'),'pkey','name'); 

 
if (PLAN_TYPE['usefrontend'] == 1){
    $arrFilter = array();
    $rsItemFilterCategory = $filterCategory->searchData($filterCategory->tableName.'.statuskey','1',true);

    for($i=0;$i<count($rsItemFilterCategory);$i++){
         $rsFilter = $itemFilter->searchData('categorykey',$rsItemFilterCategory[$i]['pkey'],true, ' and '.$itemFilter->tableName.'.statuskey = 1'); 
         $arrFilter[$rsItemFilterCategory[$i]['name']] = $class->convertForCombobox($rsFilter,'pkey','name') ;  
    }  
    
    $arrTwigVar['inputSelFilter'] =  $class->inputSelect('selFilter[]', $arrFilter , array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
}


$arrTwigVar['inputItemCode'] =  $class->inputText('itemCode');  
$arrTwigVar['inputHidItemKey'] = $class->inputHidden('hidItemKey');
$arrTwigVar['inputItemName'] =  $class->inputText('itemName'); 
$arrTwigVar['inputSelCategory'] =  $class->inputSelect('selCategory[]', $arrCategory, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox')); 
/*$arrTwigVar['inputSelBrand'] =  $class->inputSelect('selBrand[]', $arrBrand, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox')); 
$arrTwigVar['inputSelWarehouse'] =  $class->inputSelect('selWarehouse[]', $arrWarehouse, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));  
$arrTwigVar['inputChkAvailable'] =  $class->inputCheckBox('chkAvailable',array('overwritePost' => false, 'value' => 1, 'class' => 'no-class'));  */
$arrTwigVar['inputSelStatus'] =  $class->inputSelect('selStatus[]', $arrStatus, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox')); 
$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate;   
echo $twig->render('reportItemPackage.html', $arrTwigVar);  
 
?>

