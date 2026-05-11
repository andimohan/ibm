<?php  
include '../../_config.php';  
include '../../_include.php'; 
include '_global.php';
  

$obj= $itemMovement;
$securityObject = 'reportItem'; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true));
$_POST['selStatus[]'] = array(1);

 
$arrFilterInformation = array();
$detailCriteria = '';  
//$warehouseCriteria = '';

$dt = date('d / m / Y');

// ====================== must be set before TWIG 

$orderCriteria = array(); 
$orderCriteria['orderBy'] =  (isset ($_POST) && !empty($_POST['hidOrderBy']) ) ? $obj->oDbCon->paramOrder($_POST['hidOrderBy']) : $obj->tableSNMovement.'.pkey';
$orderCriteria['orderType'] = (isset ($_POST) && !empty($_POST['hidOrderType'])) ?   $_POST['hidOrderType'] : 1;

// ====================== must be set before TWIG

// ===== FOR EXPORT SECTION
$dataToExport = array();

/* data structure */
$arrTemplate = array();
$warehousekey = (isset($_POST['selWarehouse']) && !empty($_POST['selWarehouse'])) ? $_POST['selWarehouse'] : 0;

$dateMethod = $class->loadSetting('movementDateMethod');  
$datefield = ($dateMethod == 2)  ? 'trdate' : 'createdon'; 

$arrDataStructure = array();
$_POST['module'] = IMPORT_TEMPLATE['item'];

$arrDataStructure['rowNumber'] = array('title'=>'#', 'align'=>'right', 'width'=>"40px", 'autoNumber' => true, "sortable" => false);
$arrDataStructure['serialNumber'] = array('title'=>ucwords($obj->lang['serialNumber']),  'width'=>"120px", 'dbfield' => 'serialnumber');
$arrDataStructure['itemCode'] = array('title'=>ucwords($obj->lang['internalPartNumber']),  'width'=>"180px", 'dbfield' => 'itemcode');
$arrDataStructure['name'] = array('title'=>ucwords($obj->lang['description']),  'width'=>"300px", 'dbfield' => 'itemname');

switch($warehousekey){
    case 2 : $refname = ucwords($obj->lang['customer']); 
             $refcode = 'RMA #'; 
             $refdate = 'Tgl. Terima'; 
            break;
    case 5: $refname = ucwords($obj->lang['supplier']);  
            $refcode = 'RMA Vendor #'; 
            $refdate =  'Tgl. Kirim'; 
            break;
    default : $refname = ''; $refcode = ''; $refdate = ''; break;    
}

if(!empty($refname)) $arrDataStructure['refname'] = array('title'=>$refname,  'width'=>"250px", "sortable" => false, 'dbfield' => 'refname');
if(!empty($refcode)) $arrDataStructure['refcode'] = array('title'=>$refcode,  'width'=>"100px", "sortable" => false, 'dbfield' => 'refcode');
if(!empty($refdate)) $arrDataStructure['refdate'] = array('title'=>$refdate,  'width'=>"100px", "sortable" => false,'align' =>'center', 'format' => 'date', 'dbfield' => 'refdate');

$arrDataStructure['aging'] = array('title'=>$dt,  'width'=>"100px",'align'=>'center', 'sortable' => false, 'dbfield' =>'aging'); 
 
$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $obj->lang['SNAgingReport'];
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();

array_push($arrTemplate, $arrHeaderTemplate);

$arrWarehouse = $class->convertForCombobox($warehouse->searchData($warehouse->tableName.'.statuskey',1,true,'','order by name asc'),'pkey','name');
$arrStatus = $class->convertForCombobox($item->getAllStatus(),'pkey','status');  
$arrCategory = $class->convertForCombobox($itemCategory->searchData($itemCategory->tableName.'.statuskey',1,true, ' and '.$itemCategory->tableName.'.isleaf = 1', ' order by name asc'),'pkey','name');   
$arrBrand = $class->convertForCombobox($brand->searchData('','',true, ' and '.$brand->tableName.'.statuskey = 1','order by name asc'),'pkey','name'); 

$arrTwigVar['inputItemCode'] =  $class->inputText('itemCode');   
$arrTwigVar['inputItemName'] =  $class->inputText('itemName');
$arrTwigVar['inputCustomerName'] =  $class->inputText('customerName');
$arrTwigVar['inputSupplierName'] =  $class->inputText('supplierName');
$arrTwigVar['inputSerialNumber'] =  $class->inputText('serialNumber');

$arrTwigVar['inputSelCategory'] =  $class->inputSelect('selCategory[]', $arrCategory, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox')); 
$arrTwigVar['inputSelBrand'] =  $class->inputSelect('selBrand[]', $arrBrand, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox')); 
$arrTwigVar['inputSelWarehouse'] =  $class->inputSelect('selWarehouse', $arrWarehouse);  
//$arrTwigVar['inputChkQOH'] =  $class->inputCheckBox('chkQOH',array('overwritePost' => false, 'value' => 1, 'class' => 'no-class'));  
$arrTwigVar['inputSelStatus'] =  $class->inputSelect('selStatus[]', $arrStatus, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox')); 
$arrTwigVar['autoLoad'] =  0; 
$arrTwigVar['order'] =  $orderCriteria;
$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate;   
	
if (isset($_POST) && !empty($_POST['hidAction'])){  
		
	$criteria = '';
       
    array_push($arrFilterInformation,array("label" => 'Tanggal', 'filter' => $dt));
	
    if(isset($_POST) && !empty($_POST['selWarehouse'])) { 
          
        // aging gk bisa beda gudang
        $warehousekey = $_POST['selWarehouse'];  
        
       	$criteria .= ' AND '.$obj->tableSNMovement.'.warehousekey in ('.$warehousekey.')';  

        $rsCriteria = $warehouse->searchData('','',true, ' and '.$warehouse->tableName.'.pkey in ('.$warehousekey.')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$statusName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => 'Gudang', 'filter' => $statusName ));
            
	}	
	    
    if(isset($_POST) && !empty($_POST['customerName'])) {
		$criteria .= ' AND '.$obj->tableCustomer.'.name LIKE ('.$class->oDbCon->paramString('%'.$_POST['customerName'].'%').')'; 
		array_push($arrFilterInformation,array("label" => 'Pelanggan', 'filter' => $_POST['customerName']));
	}
    
    if(isset($_POST) && !empty($_POST['supplierName'])) {
		$criteria .= ' AND '.$obj->tableSuppier.'.name LIKE ('.$class->oDbCon->paramString('%'.$_POST['supplierName'].'%').')'; 
		array_push($arrFilterInformation,array("label" => 'Pemasok', 'filter' => $_POST['supplierName']));
	}
    
    
	if(isset($_POST) && !empty($_POST['itemCode'])) {
		$criteria .= ' AND '.$obj->tableName.'.code LIKE ('.$class->oDbCon->paramString('%'.$_POST['itemCode'].'%').')';
		array_push($arrFilterInformation,array("label" => 'Internal Part Number', 'filter' => $_POST['itemCode']));
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
        
       	$criteria .= ' AND '.$obj->tableName.'.brandkey in('.$key.')';  

        $rsCriteria = $brand->searchData('','',true, ' and '.$brand->tableName.'.pkey in ('.$key.')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$statusName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => 'Merk', 'filter' => $statusName));
        
	}
    
    
    if(isset($_POST) && !empty($_POST['serialNumber'])) {
		$criteria .= ' AND '.$obj->tableSerialNumberMovement.'.serialnumber LIKE ('.$class->oDbCon->paramString('%'.$_POST['serialNumber'].'%').')'; 
		array_push($arrFilterInformation,array("label" => 'Serial Number', 'filter' => $_POST['serialNumber']));
	}
    
 
	if(isset($_POST) && !empty($_POST['selStatus'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selStatus']));   
        
       	$criteria .= ' AND '.$item->tableName.'.statuskey in('.$key.')';  

        $rsCriteria =  $item->getStatusById ($key);
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['status']);
			
		$statusName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => 'Status', 'filter' => $statusName));
        
	}
      

	$order = 'order by '.$orderCriteria['orderBy'].' ' . (($orderCriteria['orderType'] == 1) ? 'desc' : 'asc');  
	$rs = $itemMovement->generateItemSNAgingReport('','',true,$criteria,$order); 
         
    $temp = 1;
    $tempreport = ''; 

    if (empty($rs))
        $tempreport .= '<tr class="report-row rewrite-row"><td colspan="'.count($arrHeaderTemplate['dataStructure']).'"></td></tr>';

    $totalAging = 0;
    
    for($i=0;$i<count($rs);$i++) {     
        $totalAging += $rs[$i]['aging'];
        
        $return = $obj->formatReportRows(array('data' => $rs[$i]),$arrTemplate);  
        array_push($dataToExport, $return['data']);   
        $tempreport .= $return['html']; 
    }

    $totalQty = count($rs);

    // overwrite header
    $arrDataStructure['aging']['title'] = $obj->lang['aging'].'<br><div class="col-header-tag">'.$obj->formatNumber($totalQty).' Pcs, '.$obj->formatNumber(($totalAging / $totalQty)). ' ' .$obj->lang['days'].'</div>'; 
    $arrHeaderTemplate['dataStructure'] = $arrDataStructure; // utk excel
    $arrTemplate[0]['dataStructure'] = $arrDataStructure;  


    $arrTwigVar['arrTemplate'] =  $arrHeaderTemplate;   

    $tableHeader = $twig->render('template-header.html', $arrTwigVar);
    $obj->generateReport($_POST, $tempreport, $arrTemplate,$dataToExport,$arrFilterInformation,$tableHeader);
}
 
echo $twig->render('reportItemSNAging.html', $arrTwigVar);  
 
?>