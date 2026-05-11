<?php
	 
include '../../_config.php';  
include '../../_include.php';
include '_global.php';

$obj= $itemMovement;
$securityObject = 'reportItem'; // the value of security object is manually inserted to handle 
									 // some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true)); 
$hasCOGSAccess = $security->isAdminLogin($item->cogsSecurityObject,10);  
 
$arrFilterInformation = array();  
 
// ===== FOR EXPORT SECTION
$dataToExport = array();

/* data structure */
$arrTemplate = array();
$detailCriteria = '';

$arrDataStructure = array();
$arrDataStructure['rowNumber'] = array('title'=>'#', 'align'=>'right', 'width'=>"40px", 'autoNumber' => true, "sortable" => false);
$arrDataStructure['code'] = array('title'=>ucwords($obj->lang['code']),  'width'=>"150px", 'dbfield' => 'code'); 
$arrDataStructure['name'] = array('title'=>ucwords($obj->lang['itemName']),'dbfield' => 'name', 'width'=>"300px", 'mergeExcelCell' => 3); 
$arrDataStructure['itemCategory'] = array('title'=>ucwords($obj->lang['itemCategory']),  'width'=>"180px", 'dbfield' => 'categoryname', 'mergeExcelCell' => 3);
$arrDataStructure['brand'] = array('title'=>ucwords($obj->lang['brand']),  'width'=>"140px", 'dbfield' => 'brandname');
$arrDataStructure['qoh'] = array('title'=>ucwords($obj->lang['qoh']),'dbfield' => 'qtyonhand', 'width'=>"100px",'format'=>'number', "sortable" => false); 
$arrDataStructure['cogs'] = array('title'=>ucwords($obj->lang['cogs']),'dbfield' => 'cogs', 'width'=>"100px",'format'=>'number'); 
$arrDataStructure['totalcogs'] = array('title'=>ucwords($obj->lang['totalCOGS']), 'dbfield' => 'totalcogs', 'width'=>"95px" ,'format'=>'number', "sortable" => false, 'calculateTotal' => true);
$arrDataStructure['status'] = array('title'=>ucwords($obj->lang['status']),'dbfield' => 'statusname', 'width'=>"100px");
		   
$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $obj->lang['stockCardSNReport']; 
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();

array_push($arrTemplate, $arrHeaderTemplate);

// detail ...
$arrDataDetailStructure = array();  
$arrDataDetailStructure['serialNumber'] = array('title'=>ucwords($obj->lang['serialNumber']),'dbfield' => 'serialnumber', 'width'=>"120px"); 
$arrDataDetailStructure['vendorPartNumber'] = array('title'=>ucwords($obj->lang['vendorPartNumber']),'dbfield' => 'partnumber', 'width'=>"120px" );
$arrDataDetailStructure['warrantyVendorPeriodEndDate'] = array('title'=>ucwords($obj->lang['warrantyExpiredDate']). ' ('.ucwords($obj->lang['supplier']).')','dbfield' => 'warrantyvendorperiodexpireddate', 'returnOnEmpty' => array('returnOnEmpty' => true, 'value' => ''), 'width'=>"160px",'format'=>'date'); 
$arrDataDetailStructure['warrantyPeriodEndDate'] = array('title'=>ucwords($obj->lang['warrantyExpiredDate']). ' ('.ucwords($obj->lang['customer']).')','dbfield' => 'warrantyperiodexpireddate', 'returnOnEmpty' => array('returnOnEmpty' => true, 'value' => ''), 'width'=>"160px",'format'=>'date');  
//$arrDataDetailStructure['note'] = array('title'=>ucwords($obj->lang['note']),'dbfield' => 'note', 'width'=>"350px", 'mergeExcelCell' => 3);
  
$arrDetailTemplate = array(); 
$arrDetailTemplate['dataStructure'] = $arrDataDetailStructure;
$arrDetailTemplate['total'] = array();

array_push($arrTemplate, $arrDetailTemplate); 
	
if (isset($_POST) && !empty($_POST['hidAction'])){   
	
	$criteria = '';
	
	/*if(isset($_POST) && !empty($_POST['trStartDate'])){
		//$criteria .= ' and trdate between '.$class->oDbCon->paramDate( $_POST['trStartDate'],' / ').' AND '.$class->oDbCon->paramDate( $_POST['trEndDate'],' / '); 
		array_push($arrFilterInformation,array("label" => 'Tanggal', 'filter' => $_POST['trStartDate'] . ' - ' .$_POST['trEndDate'] ));
	}*/
	if(isset($_POST) && !empty($_POST['itemCode'])) {
		$criteria .= ' AND '.$obj->tableItem.'.code LIKE ('.$class->oDbCon->paramString('%'.$_POST['itemCode'].'%').')';
		array_push($arrFilterInformation,array("label" => 'Kode', 'filter' => $_POST['itemCode']));
	}
  
    
	$itemkey = '';
    if(isset($_POST) && !empty($_POST['selItem'])) { 
        
        $itemkey = implode(",", $class->oDbCon->paramString($_POST['selItem']));   
         
       	$criteria .= ' AND '.$item->tableName.'.pkey in('.$itemkey.')';  

        $rsCriteria = $item->searchData('','',true, ' and '.$item->tableName.'.pkey in ('.$itemkey.')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$itemName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => 'Barang', 'filter' => $itemName ));
        
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
 
 	$warehouseCriteria = '';
	$warehousekey = '';  

	if(isset($_POST) && !empty($_POST['selWarehouse'])) { 
        
        $warehousekey = implode(",", $class->oDbCon->paramString($_POST['selWarehouse']));   
        
       	$warehouseCriteria .= ' AND warehousekey in('.$warehousekey.')';  
       	$detailCriteria .= ' AND warehousekey in('.$warehousekey.')';  

        $rsCriteria = $warehouse->searchData('','',true, ' and '.$warehouse->tableName.'.pkey in ('.$warehousekey.')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$warehouseName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => 'Gudang', 'filter' => $warehouseName ));
        
	}
    
    if(isset($_POST) && !empty($_POST['serialNumber'])) { 
        $detailCriteria .= ' AND '.$obj->tableSerialNumber.'.serialnumber = ('.$class->oDbCon->paramString('%'.$_POST['serialNumber'].'%').')';
	    array_push($arrFilterInformation,array("label" => 'Serial Number', 'filter' => $_POST['serialNumber']));
	}
    
    if(isset($_POST) && !empty($_POST['vendorPartNumber'])) { 
        $detailCriteria .= ' AND '.$obj->tableItemVendorPartNumber.'.partnumber LIKE ('.$class->oDbCon->paramString('%'.$_POST['vendorPartNumber'].'%').')';
	    array_push($arrFilterInformation,array("label" => 'Vendor Part Number', 'filter' => $_POST['vendorPartNumber']));
	}
	 
    $orderBy = (!empty($_POST['hidOrderBy'])) ? $obj->oDbCon->paramOrder($_POST['hidOrderBy']) : 'pkey'; // order by harus dr kolom yg terdaftar saja
    $orderType = (isset($_POST['hidOrderType']) && !empty($_POST['hidOrderType']) && $_POST['hidOrderType'] == 1) ? 'desc' : 'asc';
 
	 
	$order = 'order by '.$orderBy.' ' .$orderType; 
	 
    // oerwrite jenis item
    $item->itemType = "1,3";
    $groupCriteria = '';
	$rsItem = $item->searchData('','',true,$criteria,$order,'',$groupCriteria);
    
    //$date = date('d / m / Y',strtotime(str_replace('\'','',$obj->oDbCon->paramDate($_POST['trStartDate'],' / ','Y-m-d')).' -1 day'));
    $warehousekey =  (isset($_POST['selWarehouse'])) ? $_POST['selWarehouse']  : '';
	
	$tempreport = '';

    /*if (empty($rsItem))
         $tempreport .= '<tr class="report-row rewrite-row"><td colspan="'.count($arrHeaderTemplate['dataStructure']).'"></td></tr>';*/
    for($i=0;$i<count($rsItem);$i++){ 
        $itemkey = $rsItem[$i]['pkey']; 
        //$rsDetail = $item->searchSerialNumber($itemkey,'','','',' and warehousekey <> 0 '.$detailCriteria);
        //if(empty($rsDetail))
                //continue;
              
        // has detail
        $rsItem[$i]['_detail_'] = array('arrTemplate'=>$arrDetailTemplate,'data' => $rsDetail);
                  
        $return = $obj->formatReportRows(array('data' => $rsItem[$i]),$arrTemplate); 
            
        // ===== FOR EXPORT SECTION 
        array_push($dataToExport, $return['data']);  
        // ===== END FOR EXPORT SECTION

        $tempreport .= $return['html'];

        //$arrTemplate[0]['total'] = $obj->arraySum($arrTemplate[0]['total'], $return['subtotal'][0]);
            
		}  
    
        $obj->generateReport($_POST, $tempreport, $arrTemplate,$dataToExport,$arrFilterInformation);
}
else{ 
	$_POST['trStartDate'] = date('d / m / Y');
	$_POST['trEndDate'] = date('d / m / Y');
} 


$arrWarehouse = $class->convertForCombobox($warehouse->searchData($warehouse->tableName.'.statuskey',1,true,'','order by name asc'),'pkey','name'); 
$arrItem = $class->convertForCombobox($item->searchData($item->tableName.'.statuskey',1,true,' and itemtype in (1,3) ','order by name asc'),'pkey','name'); 
$arrCategory = $class->convertForCombobox($itemCategory->searchData($itemCategory->tableName.'.statuskey',1,true, ' and '.$itemCategory->tableName.'.isleaf = 1', ' order by name asc'),'pkey','name');   


//$arrTwigVar['inputItemCode'] =  $class->inputText('itemCode');
//$arrTwigVar['inputHidItemKey'] = $class->inputHidden('hidItemKey');
//$arrTwigVar['inputItemName'] =  $class->inputText('itemName'); 
$arrTwigVar['inputSelCategory'] =  $class->inputSelect('selCategory[]', $arrCategory, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));  
//$arrTwigVar['inputStartDate'] = $class->inputDate('trStartDate', array('etc' => 'style="text-align:center"'));
//$arrTwigVar['inputEndDate'] = $class->inputDate('trEndDate', array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputVendorPartNumber'] =  $class->inputText('vendorPartNumber'); 
$arrTwigVar['inputSerialNumber'] =  $class->inputText('serialNumber'); 
$arrTwigVar['inputSelWarehouse'] =  $class->inputSelect('selWarehouse[]', $arrWarehouse, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));  
$arrTwigVar['inputSelItem'] =  $class->inputSelect('selItem[]', $arrItem, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));   
$arrTwigVar['autoLoad'] =  0; 
$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate;       
echo $twig->render('reportItemSN.html', $arrTwigVar);   
?>

