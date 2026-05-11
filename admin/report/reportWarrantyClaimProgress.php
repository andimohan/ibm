<?php
include '../../_config.php';
include '../../_include.php';
include '_global.php';

$obj = $warrantyClaimProgress;
$securityObject = 'ReportWarrantyClaimProgress'; // the value of security object is manually inserted to handle 
								  // some modules that have different security object from that of their class
								  
if(!$security->isAdminLogin($securityObject,10,true));  
$hasCOGSAccess = $security->isAdminLogin($item->cogsSecurityObject,10);  

$arrFilterInformation = array(); 
$detailCriteria = ''; 
$_POST['selStatus[]'] = array(1,2,3,4,5);

// ===== FOR EXPORT SECTION
$dataToExport = array();

/* data structure */
$arrTemplate = array();

$arrDataStructure = array();
$arrDataStructure['rowNumber'] = array('title'=>'#', 'align'=>'right', 'width'=>"40px", 'autoNumber' => true, "sortable" => false);
$arrDataStructure['code'] = array('title'=>ucwords($obj->lang['code']),  'width'=>"100px", 'dbfield' => 'code');
$arrDataStructure['date'] = array('title'=>ucwords($obj->lang['date']),'dbfield' => 'trdate', 'width'=>"100px",'format'=>'date');
$arrDataStructure['warehouse'] = array('title'=>ucwords($obj->lang['warehouse']),'dbfield' => 'warehousename', 'width'=>"150px" );
$arrDataStructure['customer'] = array('title'=>ucwords($obj->lang['customer']),'dbfield' => 'customername', 'width'=>"250px" );
$arrDataStructure['claim'] = array('title'=>ucwords($obj->lang['claim']),'dbfield' => 'claimresult', 'width'=>"90px" );
$arrDataStructure['serialNumber'] = array('title'=>ucwords($obj->lang['serialNumber']),'dbfield' => 'serialnumber', 'width'=>"150px" );
$arrDataStructure['vendorPartNumber'] = array('title'=>ucwords($obj->lang['vendorPartNumber']),'dbfield' => 'partnumber', 'width'=>"200px" );
$arrDataStructure['itemName'] = array('title'=>ucwords($obj->lang['itemName']),'dbfield' => 'itemname', 'width'=>"250px" ); 
$arrDataStructure['newSerial'] = array('title'=>ucwords($obj->lang['serialNumber']) .' ('.ucwords($obj->lang['replacement']).')' ,'dbfield' => 'newserialnumber', 'width'=>"180px" , 'textColor' => '568203');
$arrDataStructure['newPartNumber'] = array('title'=>ucwords($obj->lang['vendorPartNumber']) .' ('.ucwords($obj->lang['replacement']).')','dbfield' => 'newpartnumber', 'width'=>"200px" , 'textColor' => '568203');
$arrDataStructure['newItem'] = array('title'=>ucwords($obj->lang['itemName']).' ('.ucwords($obj->lang['replacement']).')','dbfield' => 'newitemname', 'width'=>"250px" , 'textColor' => '568203'); 
$arrDataStructure['cost'] = array('title'=>ucwords($obj->lang['cost']),'dbfield' => 'amount','align'=>'right', 'width'=>"90px",'sortable' => false,'format'=>'number','calculateTotal' => true);
$arrDataStructure['note'] = array('title'=>ucwords($obj->lang['note']),'dbfield' => 'trdesc', 'width'=>"350px");
$arrDataStructure['status'] = array('title'=>ucwords($obj->lang['status']),'dbfield' => 'statusname', 'width'=>"100px" );
		  
$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $obj->lang['warrantyClaimProgressReport'];
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();

array_push($arrTemplate, $arrHeaderTemplate);

if (isset($_POST) && !empty($_POST['hidAction'])){
	
	$criteria = '';
	if(isset($_POST) && !empty($_POST['code'])) {
		$criteria .= ' AND '.$obj->tableName.'.code LIKE ('.$class->oDbCon->paramString('%'.$_POST['code'].'%').')';
		array_push($arrFilterInformation,array("label" => 'Kode', 'filter' => $_POST['code']));
	}
	if(isset($_POST) && !empty($_POST['trStartDate'])){
		$criteria .= ' and '.$obj->tableName.'.trdate between '.$class->oDbCon->paramDate( $_POST['trStartDate'],' / ').' AND '.$class->oDbCon->paramDate( $_POST['trEndDate'],' / '); 
		array_push($arrFilterInformation,array("label" => 'Tanggal', 'filter' => $_POST['trStartDate'] . ' - ' .$_POST['trEndDate'] ));
	} 
	if(isset($_POST) && !empty($_POST['itemName'])) { 
        $criteria .= ' AND  claimitem.name LIKE ('.$class->oDbCon->paramString('%'.$_POST['itemName'].'%').')';
	    array_push($arrFilterInformation,array("label" => 'Item', 'filter' => $_POST['itemName']));
	}
    
    if(isset($_POST) && !empty($_POST['serialNumber'])) { 
        $criteria .= ' AND '.$obj->tableName.'.serialnumber LIKE ('.$class->oDbCon->paramString('%'.$_POST['serialNumber'].'%').')';
	    array_push($arrFilterInformation,array("label" => 'Serial Number', 'filter' => $_POST['serialNumber']));
	}
    if(isset($_POST) && !empty($_POST['partNumber'])) { 
        $criteria .= ' AND  claimpart.partnumber LIKE ('.$class->oDbCon->paramString('%'.$_POST['partNumber'].'%').')';
	    array_push($arrFilterInformation,array("label" => 'Part Number', 'filter' => $_POST['partNumber']));
	}
    
    
    
    if(isset($_POST) && !empty($_POST['newItemName'])) { 
        $criteria .= ' AND  newitem.name LIKE ('.$class->oDbCon->paramString('%'.$_POST['newItemName'].'%').')';
	    array_push($arrFilterInformation,array("label" => 'Item Peganti', 'filter' => $_POST['newItemName']));
	}
    
    if(isset($_POST) && !empty($_POST['newSerialNumber'])) { 
        $criteria .= ' AND '.$obj->tableName.'.newserialnumber LIKE ('.$class->oDbCon->paramString('%'.$_POST['newSerialNumber'].'%').')';
	    array_push($arrFilterInformation,array("label" => 'Serial Number Penganti', 'filter' => $_POST['newSerialNumber']));
	}
    
	 if(isset($_POST) && !empty($_POST['newPartNumber'])) { 
        $criteria .= ' AND  newpart.partnumber LIKE ('.$class->oDbCon->paramString('%'.$_POST['newPartNumber'].'%').')';
	    array_push($arrFilterInformation,array("label" => 'Part Number', 'filter' => $_POST['newPartNumber']));
	}
    
	if(isset($_POST) && !empty($_POST['selWarehouse'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selWarehouse']));   
        
       	$criteria .= ' AND warehousekey in('.$key.')';  

        $rsCriteria = $warehouse->searchData('','',true, ' and '.$warehouse->tableName.'.pkey in ('.$key.')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$warehouseName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => 'Gudang', 'filter' => $warehouseName ));
        
	}
    
    if(isset($_POST) && !empty($_POST['customerName'])) {
		$criteria .= ' AND '.$obj->tableCustomer.'.name LIKE ('.$class->oDbCon->paramString('%'.$_POST['customerName'].'%').')';
	 	array_push($arrFilterInformation,array("label" => 'Pelanggan', 'filter' =>  $_POST['customerName']));
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
    
     if(isset($_POST) && !empty($_POST['selClaimResult'])) {
        $key = implode(",", $class->oDbCon->paramString($_POST['selClaimResult'])); 
        $criteria .= ' AND '.$obj->tableName.'.claimresultkey in('.$key.')';
		//$criteria .= ' AND '.$obj->tableSNMovement.'.reftabletype LIKE ('.$class->oDbCon->paramString('%'.$_POST['selTrans'].'%').')';
        
        
       
            
		//array_push($arrFilterInformation,array("label" => 'Transaksi', 'filter' => $table));
	}
    
    $orderBy = (!empty($_POST['hidOrderBy'])) ? $obj->oDbCon->paramOrder($_POST['hidOrderBy']) : 'pkey'; // order by harus dr kolom yg terdaftar saja
    $orderType = (isset($_POST['hidOrderType']) && !empty($_POST['hidOrderType']) && $_POST['hidOrderType'] == 1) ? 'desc' : 'asc';
    
	 
	$order = 'order by '.$orderBy.' ' .$orderType; 
	 
	$rs = $warrantyClaimProgress->searchData('','',true,$criteria,$order);
		
	$tempreport = ''; 
		
    // ============================= GENERATE DATA ============================= 
    $item = new Item();
 
		for( $i=0;$i<count($rs);$i++) {   
		 
            
            //$rsClaim = $item->searchVendorPartNumberForAutoComplete('',' and '.$item->tableVendorPartNumber.'.pkey = '.$rs[$i]['vendorpartnumberkey']);
            $rs[$i]['claimPartNumber'] = '';
            //$rs[$i]['claimItem'] = $rsClaim[0]['itemname'];
            
            //$rsNew = $item->searchVendorPartNumberForAutoComplete('',' and '.$item->tableVendorPartNumber.'.pkey = '.$rs[$i]['newvendorpartnumberkey']);
            $rs[$i]['newPartNumber'] = '';
            //$rs[$i]['newItem'] = $rsNew[0]['itemname'];
                  
            $return = $obj->formatReportRows(array('data' => $rs[$i]),$arrTemplate); 
            
            // ===== FOR EXPORT SECTION 
            array_push($dataToExport, $return['data']);  
            // ===== END FOR EXPORT SECTION
            
            $tempreport .= $return['html'];  
            
		}

        $obj->generateReport($_POST, $tempreport, $arrTemplate,$dataToExport,$arrFilterInformation);

}
else{ 
	$_POST['trStartDate'] = date('d / m / Y');
	$_POST['trEndDate'] = date('d / m / Y');
}  

$arrWarehouse = $class->convertForCombobox($warehouse->searchData($warehouse->tableName.'.statuskey',1,true,'','order by name asc'),'pkey','name');
$arrStatus = $class->convertForCombobox($obj->getAllStatus(),'pkey','status');
$arrClaimResult = $class->convertForCombobox($warrantyClaim->getClaimResult(),'pkey','name');

$arrTwigVar['inputCode'] =  $class->inputText('code'); 
$arrTwigVar['inputStartDate'] = $class->inputDate('trStartDate', array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputEndDate'] = $class->inputDate('trEndDate', array('etc' => 'style="text-align:center"'));  
$arrTwigVar['inputSelWarehouse'] =  $class->inputSelect('selWarehouse[]', $arrWarehouse, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
  
//$arrTwigVar['inputHidItemKey'] = $class->inputHidden('hidItemKey');
$arrTwigVar['inputItemName'] =  $class->inputText('itemName'); 
$arrTwigVar['inputSerialNumber'] =  $class->inputText('serialNumber');
$arrTwigVar['inputPartNumber'] =  $class->inputText('partNumber');
$arrTwigVar['inputSelClaimResult'] =  $class->inputSelect('selClaimResult[]', $arrClaimResult, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputNewItemName'] =  $class->inputText('newItemName'); 
$arrTwigVar['inputNewSerialNumber'] =  $class->inputText('newSerialNumber'); 
$arrTwigVar['inputNewPartNumber'] =  $class->inputText('newPartNumber');
$arrTwigVar['inputSelStatus'] =  $class->inputSelect('selStatus[]', $arrStatus, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));  
$arrTwigVar['inputCustomerName'] =  $class->inputText('customerName');  
$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate;  

echo $twig->render('reportWarrantyClaimProgress.html', $arrTwigVar);   

?>
