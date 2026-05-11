<?php	 
include '../../_config.php';  
include '../../_include.php';
include '_global.php';

$obj = $itemMovement;
$securityObject = 'reportItemMovementSN';
 
if(!$security->isAdminLogin($securityObject,10,true)); 

$arrFilterInformation = array();    

$itemIn = new ItemIn();
$itemInTableKey = $obj->getTableKeyAndObj($itemIn->tableName);
$itemInTableKey = $itemInTableKey['key'];

$itemInReceive = new ItemInReceive();
$itemInReceiveTableKey = $obj->getTableKeyAndObj($itemInReceive->tableName);
$itemInReceiveTableKey = $itemInReceiveTableKey['key']; 

$itemOut = new ItemOut();
$itemOutTableKey = $obj->getTableKeyAndObj($itemOut->tableName);
$itemOutTableKey = $itemOutTableKey['key'];

$itemOutDelivery = new ItemOutDelivery();
$itemOutDeliveryTableKey = $obj->getTableKeyAndObj($itemOutDelivery->tableName);
$itemOutDeliveryTableKey = $itemOutDeliveryTableKey['key'];

$warrantyClaim = new WarrantyClaim();
$warrantyClaimTableKey = $obj->getTableKeyAndObj($warrantyClaim->tableName);
$warrantyClaimTableKey = $warrantyClaimTableKey['key'];
 
$warrantyClaimProgress = new WarrantyClaimProgress();
$warrantyClaimProgressTableKey = $obj->getTableKeyAndObj($warrantyClaimProgress->tableName);
$warrantyClaimProgressTableKey = $warrantyClaimProgressTableKey['key'];
/* 
$itemReturnVendor = new ItemReturnVendor();
$itemReturnVendorTableKey = $obj->getTableKeyAndObj($itemReturnVendor->tableName);
$itemReturnVendorTableKey = $itemReturnVendorTableKey['key'];*/
 
//$arrMovementType['in']  = array( $itemInTableKey, $itemInReceiveTableKey, $warrantyClaimTableKey,$itemReturnVendorTableKey);
    
// ===== FOR EXPORT SECTION
$dataToExport = array();

/* data structure */
$arrTemplate = array();

$arrDataStructure = array();
$arrDataStructure['rowNumber'] = array('title'=>'#', 'align'=>'right', 'width'=>"40px", 'autoNumber' => true, "sortable" => false);
$arrDataStructure['serialNumber'] = array('title'=>ucwords($obj->lang['serialNumber']),'dbfield' => 'serialnumber', 'width'=>"150px" );
/*$arrDataStructure['warrantyExipred'] = array('title'=>'','dbfield' => 'warrantyExpired', 'width'=>"20px", 'align'=> 'center', "sortable" => false );*/
$arrDataStructure['refCode'] = array('title'=>ucwords($obj->lang['refCode']),'dbfield' => 'refcode', 'width'=>"120px" );
$arrDataStructure['customer'] = array('title'=>ucwords($obj->lang['customer']),'dbfield' => 'customername', 'width'=>"250px" );
$arrDataStructure['date'] = array('title'=>ucwords($obj->lang['date']),'dbfield' => 'trdate', 'width'=>"100px",'align' => 'center','format'=>'date');
$arrDataStructure['warehouse'] = array('title'=>ucwords($obj->lang['warehouse']),'dbfield' => 'warehousename', 'width'=>"150px" );
$arrDataStructure['reference'] = array('title'=>'In / Out','dbfield' => 'reference', 'width'=>"80px" ,"align" => 'center', "sortable" => false);
$arrDataStructure['itemCode'] = array('title'=>ucwords($obj->lang['itemCode']),'dbfield' => 'itemcode', 'width'=>"150px" );
$arrDataStructure['vendorPartNumber'] = array('title'=>ucwords($obj->lang['vendorPartNumber']),'dbfield' => 'partnumber', 'width'=>"180px" );
$arrDataStructure['itemName'] = array('title'=>ucwords($obj->lang['item']),'dbfield' => 'itemname', 'width'=>"300px" );
$arrDataStructure['warrantyVendorPeriod'] = array('title'=>ucwords($obj->lang['warrantyPeriod']). ' ('.ucwords($obj->lang['supplier']).')',  'dbfield' => 'warrantyvendorperiod', 'width'=>"120px", 'align' => 'center' );
$arrDataStructure['warrantyVendorPeriodEndDate'] = array('title'=>ucwords($obj->lang['warrantyExpiredDate']). ' ('.ucwords($obj->lang['supplier']).')','dbfield' => 'warrantyvendorperiodexpireddate', 'returnOnEmpty' => array('returnOnEmpty' => true, 'value' => ''),  'format' => 'date', 'width'=>"130px" );
$arrDataStructure['warrantyPeriod'] = array('title'=>ucwords($obj->lang['warrantyPeriod']). ' ('.ucwords($obj->lang['customer']).')',  'dbfield' => 'warrantyperiod', 'width'=>"120px" , 'align' => 'center');
$arrDataStructure['warrantyPeriodEndDate'] = array('title'=>ucwords($obj->lang['warrantyExpiredDate']). ' ('.ucwords($obj->lang['customer']).')','dbfield' => 'warrantyperiodexpireddate', 'returnOnEmpty' => array('returnOnEmpty' => true, 'value' => ''),  'format' => 'date', 'width'=>"130px" );
$arrDataStructure['itemName'] = array('title'=>ucwords($obj->lang['item']),'dbfield' => 'itemname', 'width'=>"300px" );
$arrDataStructure['timeLog'] = array('title'=>ucwords($obj->lang['timeLog']), 'dbfield' => 'createdon', 'format' =>'datetime', 'width'=>"150px" );
//$arrDataStructure['timeLogDiff'] = array('title'=>ucwords('Interval'), 'dbfield' => 'timelogdiff',  'width'=>"150px", "sortable" => false ); 

		  
$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $obj->lang['snMovementReport'];
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();

array_push($arrTemplate, $arrHeaderTemplate);


$criteria = ' and '.$obj->tableSNMovement.'.statuskey = 1';

if (isset($_POST) && !empty($_POST['hidAction'])){
	
	/*if(isset($_POST) && !empty($_POST['refCode'])) {
		$criteria .= ' AND '.$obj->tableName.'.refcode LIKE ('.$class->oDbCon->paramString('%'.$_POST['refCode'].'%').')';
		array_push($arrFilterInformation,array("label" => 'Kode Ref', 'filter' => $_POST['refCode']));
	}*/
	
	if(isset($_POST) && !empty($_POST['trStartDate'])){
		$criteria .= ' and '.$obj->tableSNMovement.'.trdate between '.$class->oDbCon->paramDate( $_POST['trStartDate'],' / ').' AND '.$class->oDbCon->paramDate( $_POST['trEndDate'] . ' 23:59:59',' / '); 
		array_push($arrFilterInformation,array("label" => 'Tanggal', 'filter' => $_POST['trStartDate'] . ' - ' .$_POST['trEndDate'] ));
	}
    
	if(isset($_POST) && !empty($_POST['itemName'])) { 
        $criteria .= ' AND '.$obj->tableItem.'.name LIKE ('.$class->oDbCon->paramString('%'.$_POST['itemName'].'%').')';
	    array_push($arrFilterInformation,array("label" => 'Item', 'filter' => $_POST['itemName']));
	}
    
    if(isset($_POST) && !empty($_POST['itemCode'])) { 
        $criteria .= ' AND '.$obj->tableItem.'.code LIKE ('.$class->oDbCon->paramString('%'.$_POST['itemCode'].'%').')';
	    array_push($arrFilterInformation,array("label" => 'Item', 'filter' => $_POST['itemCode']));
	}
    
    if(isset($_POST) && !empty($_POST['vendorPartNumber'])) { 
        $criteria .= ' AND '.$obj->tableItemVendorPartNumber.'.partnumber LIKE ('.$class->oDbCon->paramString('%'.$_POST['vendorPartNumber'].'%').')';
	    array_push($arrFilterInformation,array("label" => 'Vendor Part Number', 'filter' => $_POST['vendorPartNumber']));
	}
    if(isset($_POST) && !empty($_POST['serialNumber'])) { 
        $criteria .= ' AND '.$obj->tableSNMovement.'.serialnumber LIKE ('.$class->oDbCon->paramString('%'.$_POST['serialNumber'].'%').')';
	    array_push($arrFilterInformation,array("label" => 'Serial Number', 'filter' => $_POST['serialNumber']));
	}
   
    if(isset($_POST) && !empty($_POST['selTrans'])) {
        $key = implode(",", $class->oDbCon->paramString($_POST['selTrans'])); 
        $criteria .= ' AND '.$obj->tableSNMovement.'.reftabletype in('.$key.')';
		//$criteria .= ' AND '.$obj->tableSNMovement.'.reftabletype LIKE ('.$class->oDbCon->paramString('%'.$_POST['selTrans'].'%').')';
        
        
        $table = '';
        if($_POST['selTrans'] == $itemInTableKey || $_POST['selTrans'] == $itemInReceiveTableKey)
            $table = ucwords($obj->lang['itemIn']);
        else
          $table = ucwords($obj->lang['itemOut']);
            
		array_push($arrFilterInformation,array("label" => 'Transaksi', 'filter' => $table));
	}
	 
	if(isset($_POST) && !empty($_POST['selWarehouse'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selWarehouse']));   
        
       	$criteria .= ' AND '.$obj->tableSNMovement.'.warehousekey in('.$key.')';  

        $rsCriteria = $warehouse->searchData('','',true, ' and '.$warehouse->tableName.'.pkey in ('.$key.')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$warehouseName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => 'Gudang', 'filter' => $warehouseName ));
        
	}
	
    //$orderBy = (!empty($_POST['hidOrderBy'])) ? $obj->oDbCon->paramOrder($_POST['hidOrderBy']) : $obj->tableSNMovement.'.pkey'; // order by harus dr kolom yg terdaftar saja
    $orderType = (isset($_POST['hidOrderType']) && !empty($_POST['hidOrderType']) && $_POST['hidOrderType'] == 1) ? 'desc' : 'asc';
    
    $dateMethod = $class->loadSetting('movementDateMethod');
    $orderBy = ($dateMethod == 2) ? 'trdate' : 'createdon'; 
    
	$order = 'order by '.$orderBy.' ' .$orderType; 
     
	$rs = $obj->searchSNMovement('','','','',$criteria,$order);
     
    $tempreport = ''; 
 
    
    $prevDate = '';
    $arrDateDiff = array('%y', '%m', '%d', '%h', '%i');
    
    for($i=0;$i<count($rs);$i++) {
        $arrStyle = array();
        $tableName ='';
        //if(in_array($rs[$i]['reftabletype'], $arrMovementType['in'])){
        if($rs[$i]['qtyinbaseunit'] > 0){
             $rs[$i]['reference'] = 'In'; 
        } else{
            $rs[$i]['reference'] = 'Out';
            //$arrStyle['dbfield']['textColor'] = 'C41E3A';

            foreach($arrTemplate[0]['dataStructure'] as $key=>$el) 
            if (isset($el['dbfield']))
                $arrStyle[$el['dbfield']]['textColor'] = 'C41E3A';      

        }
 
        
        // $rs[$i]['serialnumber'] .= ($rs[$i]['warrantyvendordatediff'] > 0) ? ' <i class="fas fa-clock text-blue-munsell"></i>' : '';
        
        $emptyDate = $obj->formatDBDate($rs[$i]['warrantyperiodexpireddate']) == DEFAULT_EMPTY_DATE;
        if(!$emptyDate) { 
            $rs[$i]['serialnumber'] .= ($rs[$i]['warrantydatediff'] > 0 ) ? ' <i class="fas fa-clock text-red-cardinal"></i>' : '';
        }else{  
           // $rs[$i]['warrantyperiodexpireddate'] = '';
        }
        
        $rs[$i]['warrantyperiod'] = $obj->convertMonthToYear($rs[$i]['warrantyperiod']); 
        $rs[$i]['warrantyvendorperiod'] = $obj->convertMonthToYear($rs[$i]['warrantyvendorperiod']); 
        //$rs[$i]['timelogdiff'] = (empty($prevDate)) ? '-' : $obj->getDateDifference($prevDate,$rs[$i]['createdon'], $arrDateDiff);

        $prevDate = $rs[$i]['createdon'];
            
        $return = $obj->formatReportRows(array('data' => $rs[$i], 'style' => $arrStyle),$arrTemplate); 

        // ===== FOR EXPORT SECTION 
        array_push($dataToExport, $return['data']);  
        // ===== END FOR EXPORT SECTION

        $tempreport .= $return['html'];  

    }

    $obj->generateReport($_POST, $tempreport, $arrTemplate,$dataToExport,$arrFilterInformation);


}else{ 
	$_POST['trStartDate'] = date('d / m / Y');
	$_POST['trEndDate'] = date('d / m / Y');
}

$arrWarehouse = $class->convertForCombobox($warehouse->searchData($warehouse->tableName.'.statuskey',1,true,'','order by name asc'),'pkey','name');
$arrMovement = array();
$arrMovement[$itemInTableKey] = 'Pemasukan Barang';
$arrMovement[$itemInReceiveTableKey] = 'Penerimaan Barang';
$arrMovement[$itemOutTableKey] = 'Pengeluaran Barang';
$arrMovement[$itemOutDeliveryTableKey] = 'Pengiriman Barang';
$arrMovement[$warrantyClaimTableKey] = 'Penerimaan Klaim Garansi';
$arrMovement[$warrantyClaimProgressTableKey] = 'Klaim Garansi';


//$arrTwigVar['inputRefCode'] =  $class->inputText('refCode'); 
$arrTwigVar['inputStartDate'] = $class->inputDate('trStartDate', array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputEndDate'] = $class->inputDate('trEndDate', array('etc' => 'style="text-align:center"'));  
$arrTwigVar['inputSelWarehouse'] =  $class->inputSelect('selWarehouse[]', $arrWarehouse, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));  
$arrTwigVar['inputSelTrans'] =  $class->inputSelect('selTrans[]', $arrMovement, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));  
//$arrTwigVar['inputHidItemKey'] = $class->inputHidden('hidItemKey');
$arrTwigVar['inputItemName'] =  $class->inputText('itemName'); 
$arrTwigVar['inputItemCode'] =  $class->inputText('itemCode'); 
$arrTwigVar['inputVendorPartNumber'] =  $class->inputText('vendorPartNumber'); 
$arrTwigVar['inputSerialNumber'] =  $class->inputText('serialNumber'); 
$arrTwigVar['autoLoad'] =  0; 
$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate;  

echo $twig->render('reportItemMovementSN.html', $arrTwigVar);      
?>