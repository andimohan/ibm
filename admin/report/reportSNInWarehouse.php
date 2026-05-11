<?php
include '../../_config.php';
include '../../_include.php';
include '_global.php';

$obj = $item;
$securityObject = 'reportItem'; // the value of security object is manually inserted to handle 
								  // some modules that have different security object from that of their class
								  
if(!$security->isAdminLogin($securityObject,10,true)); 

$arrFilterInformation = array(); 
$detailCriteria = ''; 
$_POST['selStatus[]'] = array(1);

// ===== FOR EXPORT SECTION
$dataToExport = array();

/* data structure */
$arrTemplate = array();

$arrDataStructure = array();
$arrDataStructure['rowNumber'] = array('title'=>'#', 'align'=>'right', 'width'=>"40px", 'autoNumber' => true, "sortable" => false);
$arrDataStructure['code'] = array('title'=>ucwords($obj->lang['code']),  'width'=>"150px", 'dbfield' => 'code');
$arrDataStructure['name'] = array('title'=>ucwords($obj->lang['warehouse']),'dbfield' => 'name',  'width'=>"250px" );
$arrDataStructure['status'] = array('title'=>ucwords($obj->lang['status']),'dbfield' => 'statusname',  'width'=>"300px" ); 
		  
$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $obj->lang['serialNumberReport'];
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();

array_push($arrTemplate, $arrHeaderTemplate);

// detail ...
$arrDataDetailStructure = array();
$arrDataDetailStructure['serialNumber'] = array('title'=>ucwords($obj->lang['serialNumber']),  'dbfield' => 'serialnumber', 'width'=>'150px', 'format' => 'string'  ); 
$arrDataDetailStructure['vendorPartNumber'] = array('title'=>ucwords($obj->lang['vendorPartNumber']),  'dbfield' => 'partnumber', 'width'=>"150px"  ); 
$arrDataDetailStructure['itemname'] = array('title'=>ucwords($obj->lang['itemName']),  'dbfield' => 'itemname',  'width'=>"300px"); 
$arrDataDetailStructure['brandname'] = array('title'=>ucwords($obj->lang['brand']),  'dbfield' => 'brandname',  'width'=>"80px"); 

$arrDetailTemplate = array(); 
$arrDetailTemplate['dataStructure'] = $arrDataDetailStructure;
$arrDetailTemplate['total'] = array();

array_push($arrTemplate, $arrDetailTemplate);

if (isset($_POST) && !empty($_POST['hidAction'])){
	
	$criteria = '';
	if(isset($_POST) && !empty($_POST['code'])) {
		$detailCriteria .= ' AND '.$obj->tableName.'.code LIKE ('.$class->oDbCon->paramString('%'.$_POST['code'].'%').')';
		array_push($arrFilterInformation,array("label" => 'Kode', 'filter' => $_POST['code']));
	}
	/*if(isset($_POST) && !empty($_POST['trStartDate'])){
		$detailCriteria .= ' and trdate between '.$class->oDbCon->paramDate( $_POST['trStartDate'],' / ').' AND '.$class->oDbCon->paramDate( $_POST['trEndDate'],' / '); 
		array_push($arrFilterInformation,array("label" => 'Tanggal', 'filter' => $_POST['trStartDate'] . ' - ' .$_POST['trEndDate'] ));
	} */
	if(isset($_POST) && !empty($_POST['itemName'])) { 
        $detailCriteria .= ' AND '.$obj->tableName.'.name LIKE ('.$class->oDbCon->paramString('%'.$_POST['itemName'].'%').')';
	    array_push($arrFilterInformation,array("label" => 'Item', 'filter' => $_POST['itemName']));
	}
 
    
    if(isset($_POST) && !empty($_POST['serialNumber'])) { 
        $detailCriteria .= ' AND '.$obj->tableSerialNumber.'.serialnumber LIKE ('.$class->oDbCon->paramString('%'.$_POST['serialNumber'].'%').')';
	    array_push($arrFilterInformation,array("label" => 'Serial Number', 'filter' => $_POST['serialNumber']));
	}
    
    if(isset($_POST) && !empty($_POST['vendorPartNumber'])) { 
        $detailCriteria .= ' AND '.$obj->tableVendorPartNumber.'.partnumber LIKE ('.$class->oDbCon->paramString('%'.$_POST['vendorPartNumber'].'%').')';
	    array_push($arrFilterInformation,array("label" => 'Vendor Part Number', 'filter' => $_POST['vendorPartNumber']));
	}
    
    
	
	if(isset($_POST) && !empty($_POST['selBrand'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selBrand']));    
        $detailCriteria .= ' AND brandkey in('.$key.')';

        $rsCriteria = $brand->searchData('','',true, ' and '.$brand->tableName.'.pkey in ('.$key.')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$brandName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => 'Merk', 'filter' => $brandName ));
        
	}
     
    
	
	if(isset($_POST) && !empty($_POST['selWarehouse'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selWarehouse']));   
        
        // ini kepake gk kepake, karena gudangnya selalu 1
       	$criteria .= ' AND '.$warehouse->tableName.'.pkey in('.$key.')';  
        
        $detailCriteria .= ' AND warehousekey in('.$key.')';

        $rsCriteria = $warehouse->searchData('','',true, ' and '.$warehouse->tableName.'.pkey in ('.$key.')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$warehouseName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => 'Gudang', 'filter' => $warehouseName ));
        
	}
     
    
	if(isset($_POST) && !empty($_POST['selStatus'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selStatus']));   
        
       	$criteria .= ' AND '.$warehouse->tableName.'.statuskey in('.$key.')';  

        $rsCriteria =  $obj->getStatusById ($key);
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['status']);
			
		$statusName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => 'Status', 'filter' => $statusName));
        
	}
	
    $orderBy = (!empty($_POST['hidOrderBy'])) ? $obj->oDbCon->paramOrder($_POST['hidOrderBy']) : 'pkey'; // order by harus dr kolom yg terdaftar saja
    $orderType = (isset($_POST['hidOrderType']) && !empty($_POST['hidOrderType']) && $_POST['hidOrderType'] == 1) ? 'desc' : 'asc';
    
	 
	$order = 'order by '.$orderBy.' ' .$orderType; 
	 
	$rs = $warehouse->searchData('','',true,$criteria,$order);
		
	$tempreport = ''; 
		
    // ============================= GENERATE DATA ============================= 
 
		for( $i=0;$i<count($rs);$i++) {   
		
			$rsDetail = $obj->searchSerialNumber('','','',$rs[$i]['pkey'],$detailCriteria); 
            if (empty($rsDetail)) continue;
            
            // has detail
            $rs[$i]['_detail_'] = array('arrTemplate'=>$arrDetailTemplate,'data' => $rsDetail);
                  
            $return = $obj->formatReportRows(array('data' => $rs[$i]),$arrTemplate); 
            
            // ===== FOR EXPORT SECTION 
            array_push($dataToExport, $return['data']);  
            // ===== END FOR EXPORT SECTION
            
            $tempreport .= $return['html'];  
            
		}

        $obj->generateReport($_POST, $tempreport, $arrTemplate,$dataToExport,$arrFilterInformation);

}
else{ 
	/*$_POST['trStartDate'] = date('d / m / Y');
	$_POST['trEndDate'] = date('d / m / Y');*/
}  

$arrWarehouse = $class->convertForCombobox($warehouse->searchData($warehouse->tableName.'.statuskey',1,true,'','order by name asc'),'pkey','name');
$arrStatus = $class->convertForCombobox($obj->getAllStatus(),'pkey','status');
$arrBrand = $class->convertForCombobox($brand->searchData('','',true, ' and '.$brand->tableName.'.statuskey = 1','order by name asc'),'pkey','name'); 

$arrTwigVar['inputCode'] =  $class->inputText('code');  
$arrTwigVar['inputSelWarehouse'] =  $class->inputSelect('selWarehouse[]', $arrWarehouse, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));  
$arrTwigVar['inputSelBrand'] =  $class->inputSelect('selBrand[]', $arrBrand, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox')); 
$arrTwigVar['inputItemName'] =  $class->inputText('itemName'); 
$arrTwigVar['inputSerialNumber'] =  $class->inputText('serialNumber'); 
$arrTwigVar['inputVendorPartNumber'] =  $class->inputText('vendorPartNumber'); 
$arrTwigVar['inputSelStatus'] =  $class->inputSelect('selStatus[]', $arrStatus, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));   
$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate;  
$arrTwigVar['autoLoad'] =  0; 

echo $twig->render('reportSNInWarehouse.html', $arrTwigVar);   

?>
