<?php
	 
include '../../_config.php';  
include '../../_include-v2.php';

includeClass(array('Warehouse.class.php','CarCategory.class.php','TruckingServiceWorkOrder.class.php'));
$truckingServiceWorkOrder =  createObjAndAddToCol(new TruckingServiceWorkOrder());

$customer = createObjAndAddToCol( new Customer()); 
$warehouse =  createObjAndAddToCol(new Warehouse());
$truckingJob =  createObjAndAddToCol(new TruckingJob());
$carCategory =  createObjAndAddToCol(new CarCategory());
$car =  createObjAndAddToCol(new Car());
//$supplier = createObjAndAddToCol(new Supplier());

include '_global.php';

$obj= $truckingServiceWorkOrder;
$securityObject = 'reportTruckingServiceWorkOrder'; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true));
   
$arrDateType= array(
    '1' => $obj->lang['jobOrderDate'], 
    '2' => $obj->lang['serviceWorkOrderDate'], 
    '3' => $obj->lang['stuffingAndDestuffingDateTime'], 
);

$arrFilterInformation = array(); 
//$detailCriteria = 'and refcashoutkey is not null and requestamount is not null';
$_POST['selStatus[]'] = array(2,3);  

// ===== FOR EXPORT SECTION
$dataToExport = array();

/* data structure */
$arrTemplate = array();
$isShowDetail = (isset($_POST['isShowDetail']) && !empty($_POST['isShowDetail'])) ? true : false;

$arrDataStructure = array();
$arrDataStructure['rowNumber'] = array('title'=>'#', 'align'=>'right', 'width'=>"40px", 'autoNumber' => true, "sortable" => false);
$arrDataStructure['code'] = array('title'=>ucwords($obj->lang['code']),  'width'=>"100px", 'dbfield' => 'code');
$arrDataStructure['date'] = array('title'=>ucwords($obj->lang['date']),'dbfield' => 'trdate', 'width'=>"90px",'format'=>'date');
$arrDataStructure['stuffingAndDestuffingDateTime'] = array('title'=>ucwords($obj->lang['stuffingAndDestuffingDateTime']),'dbfield' => 'stuffingdatetime', 'width'=>"130px",'format'=>'date'); //seharusnya dari field stuffingdatetime
$arrDataStructure['jobOrderCode'] = array('title'=>ucwords($obj->lang['jobOrderCode']),'dbfield' => 'serviceordercode', 'width'=>"120px" );
$arrDataStructure['jobOrderDate'] = array('title'=>ucwords($obj->lang['jobOrderDate']),'dbfield' => 'serviceorderdate', 'width'=>"100px",'format'=>'date');
$arrDataStructure['cargoType'] = array('title'=>ucwords($obj->lang['cargoType']),'dbfield' => 'cargotype', 'width'=>"90px");
$arrDataStructure['jobType'] = array('title'=>ucwords($obj->lang['jobType']),'dbfield' => 'jobtypename', 'width'=>"120px");
$arrDataStructure['service'] = array('title'=>ucwords($obj->lang['service']),'dbfield' => 'containername', 'width'=>"100px");
$arrDataStructure['si'] = array('title'=>ucwords($obj->lang['si']),'dbfield' => 'donumber', 'width'=>"120px");
$arrDataStructure['carrierBookingNumber'] = array('title'=>ucwords($obj->lang['carrierBookingNumber']),'dbfield' => 'shipmentnumber', 'width'=>"150px");
$arrDataStructure['containerNumber'] = array('title'=>ucwords($obj->lang['containerNumber']),'dbfield' => 'containernumber', 'width'=>"100px");
$arrDataStructure['sealNumber'] = array('title'=>ucwords($obj->lang['sealNumber']),'dbfield' => 'sealnumber', 'width'=>"100px"); 
$arrDataStructure['customer'] = array('title'=>ucwords($obj->lang['customer']),'dbfield' => 'customername', 'width'=>"200px");
$arrDataStructure['consignee'] = array('title'=>ucwords($obj->lang['consignee']),'dbfield' => 'consigneename', 'width'=>"200px");
$arrDataStructure['driver'] = array('title'=>ucwords($obj->lang['driver']),'dbfield' => 'drivername', 'width'=>"150px");
$arrDataStructure['carCategory'] = array('title'=>ucwords($obj->lang['carCategory']),'dbfield' => 'carcategoryname', 'width'=>"150px"); 
$arrDataStructure['car'] = array('title'=>ucwords($obj->lang['car']),'dbfield' => 'policenumber', 'width'=>"100px"); 
$arrDataStructure['tl'] = array('title'=>'TL','dbfield' => 'TL', 'width'=>"50px",'align'=>'center');
$arrDataStructure['route'] = array('title'=>ucwords($obj->lang['route']),'dbfield' => 'route', 'width'=>"150px", "sortable" => false);
$arrDataStructure['note'] = array('title'=>ucwords($obj->lang['note']), 'width'=>"300px",'dbfield' => 'trdesc');

if(in_array(DOMAIN_NAME, array('yellowegg.wintera.co.id','mandy.wintera.co.id')))
    $arrDataStructure['sellingPrice'] = array('title'=>ucwords($obj->lang['sellingPrice']),'dbfield' => 'sellingprice','align'=>'right', 'width'=>"90px",'format'=>'number', 'calculateTotal' => true);

$arrDataStructure['amount'] = array('title'=>ucwords($obj->lang['costAmount']),'dbfield' => 'total','align'=>'right', 'width'=>"90px",'format'=>'number','sortable' => false,'calculateTotal' => true);
$arrDataStructure['status'] = array('title'=>ucwords($obj->lang['status']),'dbfield' => 'statusname', 'width'=>"80px");
		   
$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $obj->lang['workOrderReport']; 
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();

array_push($arrTemplate, $arrHeaderTemplate);

if ($isShowDetail){ 
// detail ...
$arrDataDetailStructure = array();
$arrDataDetailStructure['cost'] = array('title'=>ucwords($obj->lang['cost']),  'dbfield' => 'name');  
$arrDataDetailStructure['amount'] = array('title'=>ucwords($obj->lang['amount']),  'dbfield' => 'requestamount', 'width'=>"100px" , 'format' => 'number' ,'calculateTotal' => true ); 
$arrDataDetailStructure['realization'] = array('title'=>ucwords($obj->lang['realization']),  'dbfield' => 'amount', 'width'=>"100px" , 'format' => 'number' ,'calculateTotal' => true );  
$arrDataDetailStructure['refcode'] = array('title'=>ucwords($obj->lang['refCode']),  'dbfield' => 'refcashoutcode');  

$arrDetailTemplate = array();
$arrDetailTemplate['reportWidth'] = "500px";
$arrDetailTemplate['dataStructure'] = $arrDataDetailStructure;
$arrDetailTemplate['total'] = array();

array_push($arrTemplate, $arrDetailTemplate); 
}

if (isset($_POST) && !empty($_POST['hidAction'])){  
		
	$criteria = '';
	if(isset($_POST) && !empty($_POST['workOrderCode'])) {
		$criteria .= ' AND '.$obj->tableName.'.code LIKE ('.$class->oDbCon->paramString('%'.$_POST['workOrderCode'].'%').')';
		array_push($arrFilterInformation,array("label" => 'Kode', 'filter' => $_POST['workOrderCode']));
	}
    
	if(isset($_POST) && !empty($_POST['trStartDateTime'])){
        
        switch($_POST['selDateType']){
            case '1' : $fieldName = $obj->tableServiceOrderHeader.'.trdate'; break;
            case '2' : $fieldName = $obj->tableName.'.trdate';  break;
            case '3' : $fieldName = $obj->tableName.'.stuffingdatetime';  break;
            default : $fieldName = $obj->tableName.'.trdate';  break;
                
        }
		$criteria .= ' and '.$fieldName.' between '.$class->oDbCon->paramDate( $_POST['trStartDateTime'],' / ').' AND '.$class->oDbCon->paramDate( $_POST['trEndDateTime'],' / ', 'Y-m-d 23:59'); 
		array_push($arrFilterInformation,array("label" => $arrDateType[$_POST['selDateType']], 'filter' => $_POST['trStartDateTime'] . ' - ' .$_POST['trEndDateTime'] ));
	}
    
    if(isset($_POST) && !empty($_POST['selWarehouse'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selWarehouse']));   
        
       	$criteria .= ' AND '.$obj->tableName.'.warehousekey in('.$key.')';  

        $rsCriteria = $warehouse->searchData('','',true, ' and '.$warehouse->tableName.'.pkey in ('.$key.')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$statusName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => $obj->lang['warehouse'], 'filter' => $statusName ));
        
	}
    
	
	if(isset($_POST) && !empty($_POST['salesCode'])) {
		$criteria .= ' AND '.$obj->tableServiceOrderHeader.'.code LIKE ('.$class->oDbCon->paramString('%'.$_POST['salesCode'].'%').')';
		array_push($arrFilterInformation,array("label" => 'Kode', 'filter' => $_POST['salesCode']));
	}
    
    	   
	if(isset($_POST) && !empty($_POST['doNumber'])) {
		$criteria .= ' AND '.$obj->tableServiceOrderHeader.'.donumber LIKE ('.$class->oDbCon->paramString('%'.$_POST['doNumber'].'%').')';
	 	array_push($arrFilterInformation,array("label" => 'DO Pelanggan', 'filter' =>  $_POST['doNumber']));
	} 
    
	if(isset($_POST) && !empty($_POST['containerNumber'])) {
		$criteria .= ' AND ('.$obj->tableName.'.containernumber LIKE ('.$class->oDbCon->paramString('%'.$_POST['containerNumber'].'%').') or  '.$obj->tableName.'.container2number LIKE ('.$class->oDbCon->paramString('%'.$_POST['containerNumber'].'%').'))';
	 	array_push($arrFilterInformation,array("label" => 'No. Container', 'filter' =>  $_POST['containerNumber']));
	} 
    
    
	if(isset($_POST) && !empty($_POST['shipmentNumber'])) {
		$criteria .= ' AND '.$obj->tableServiceOrderHeader.'.shipmentnumber LIKE ('.$class->oDbCon->paramString('%'.$_POST['shipmentNumber'].'%').')';
	 	array_push($arrFilterInformation,array("label" => 'Booking Pelayaran', 'filter' =>  $_POST['shipmentNumber']));
	} 
    
        
	if(isset($_POST) && !empty($_POST['selCustomer'])) {
		$criteria .= ' AND '.$obj->tableServiceOrderHeader.'.customerkey in ('.$class->oDbCon->paramString($_POST['selCustomer'],',').')';
        $rsCust = $customer->searchDataRow(array($customer->tableName.'.name'),' and '.$customer->tableName.'.pkey in ('.$class->oDbCon->paramString($_POST['selCustomer'],',').')' );
	 	array_push($arrFilterInformation,array("label" => 'Pelanggan', 'filter' =>  array_column($rsCust,'name')));
	}  
    
	if(isset($_POST) && !empty($_POST['consigneeName'])) {
		$criteria .= ' AND '.$obj->tableConsignee.'.name LIKE ('.$class->oDbCon->paramString('%'.$_POST['consigneeName'].'%').')';
	 	array_push($arrFilterInformation,array("label" => 'Consignee', 'filter' =>  $_POST['consigneeName']));
	}  
    
	if(isset($_POST) && !empty($_POST['selJobType'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selJobType']));   
        
       	$criteria .= ' AND '.$obj->tableName.'.jobtypekey in('.$key.')';  

        $rsCriteria =  $truckingJob->searchData('','',true, ' and '.$truckingJob->tableName.'.pkey in ('.$key.')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$statusName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => 'Jenis Pekerjaan', 'filter' => $statusName));
        
	}
    
	if(isset($_POST) && !empty($_POST['selCargoType'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selCargoType']));   
        
       	$criteria .= ' AND '.$obj->tableServiceOrderHeader.'.cargotypekey in('.$key.')';  

        $rsCriteria =  $obj->getCargoType(1);
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$statusName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => 'Jenis Kargo', 'filter' => $statusName));
        
	}
    
    if(isset($_POST) && !empty($_POST['selCarKey'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selCarKey']));   
        
       	$criteria .= ' AND '.$obj->tableName.'.carkey in('.$key.')';  

        $rsCriteria =  $car->searchData('','',true, ' and '.$car->tableName.'.statuskey = 1  and '.$car->tableName.'.pkey in ('.$key.')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['policenumber']);
			
		$statusName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => 'No Polisi', 'filter' => $statusName));
        
	}
    
//    if(isset($_POST) && !empty($_POST['selSupplier'])) { 
//        
//        $key = implode(",", $class->oDbCon->paramString($_POST['selSupplier']));   
//        
//       	$criteria .= ' AND '.$supplier->tableName.'.pkey in('.$key.')';  
//
//        $rsCriteria =  $supplier->searchData('','',true, ' and '.$supplier->tableName.'.statuskey = 1  and '.$supplier->tableName.'.pkey in ('.$key.')');
//	 
//        $arrTempStatus = array();
//		for ($k=0;$k<count($rsCriteria);$k++)
//		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
//			
//		$supplierName = implode(", ",$arrTempStatus); 
//	    array_push($arrFilterInformation,array("label" => 'Pemasok', 'filter' => $supplierName));
//        
//	}

        
    if(isset($_POST) && !empty($_POST['outsourceRegistrationNumber'])) {   
       	$criteria .= ' AND '.$obj->tableName.'.outsourcecarregistrationnumber = '. $class->oDbCon->paramString($_POST['outsourceRegistrationNumber']);  
        array_push($arrFilterInformation,array("label" => 'No Polisi Outsource', 'filter' => $_POST['outsourceRegistrationNumber'])); 
	}
    
    
    if(isset($_POST) && !empty($_POST['selCarCategory'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selCarCategory']));   
        
       	$criteria .= ' AND '.$carCategory->tableName.'.pkey in('.$key.')';  

        $rsCriteria =  $carCategory->searchData('','',true, ' and '.$carCategory->tableName.'.statuskey = 1  and '.$carCategory->tableName.'.pkey in ('.$key.')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$statusName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => 'Jenis Mobil', 'filter' => $statusName));
        
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
    
    $orderBy = (!empty($_POST['hidOrderBy'])) ? $obj->oDbCon->paramOrder($_POST['hidOrderBy']) : 'pkey'; // order by harus dr kolom yg terdaftar saja
    $orderType = (isset($_POST['hidOrderType']) && !empty($_POST['hidOrderType']) && $_POST['hidOrderType'] == 1) ? 'desc' : 'asc';
      
	$order = 'order by '.$orderBy.' ' .$orderType; 
	$rs = $obj->searchData('','',true,$criteria,$order);
	$arrPkey = array_column($rs, 'pkey');
	
	$rsCargoDetail = $obj->getCargoDetail($arrPkey);

	$totalCargoCost = array();
	foreach($rsCargoDetail as $key => $row) {
		$refkey = $row['refkey'];
		if(!isset($totalCargoCost[$refkey])) $totalCargoCost[$refkey]['totalcargocost'] = 0;
		$totalCargoCost[$refkey]['totalcargocost'] += $row['amount']; 

	}
    $tempreport = ''; 
     
    if (empty($rs)) 
			$tempreport .= '<tr class="report-row rewrite-row"><td colspan="'.count($arrHeaderTemplate['dataStructure']).'"></td></tr>';
    
    for( $i=0;$i<count($rs);$i++) {   
		    $arrHeaderStyle = array();

			$pkey = $rs[$i]['pkey'];

			$totalCargo = (isset($totalCargoCost[$pkey])) ? $totalCargoCost[$pkey]['totalcargocost'] : 0;
            
            // detail tetep perlu digenerate krena perlu hitung total
			$rsDetail = $obj->getCostDetail($rs[$i]['pkey']);
			
            if(!empty($rs[$i]['outsourcecost'])){
                $newrow = array ("qty" => 1, "amount" => $rs[$i]['outsourcecost'],"requestamount" => $rs[$i]['outsourcecost'], "name" => $obj->lang['truckingFee']);
                array_push($rsDetail,$newrow);
            }
        
            if(!empty($rs[$i]['TL'])){
                $arrHeaderStyle['drivername']['textColor'] = '0093af'; 
                $arrHeaderStyle['policenumber']['textColor'] = '0093af'; 
                $rs[$i]['policenumber'] =  $rs[$i]['outsourcecarregistrationnumber'];
                $rs[$i]['drivername'] =  $rs[$i]['outsourcename']; 
            }
        
            $total = 0;
            for($j=0;$j<count($rsDetail);$j++)
                $total += ($rsDetail[$j]['qty'] * $rsDetail[$j]['amount']); 


			//total cost di tambah total cargo
			$total += $totalCargo;
            
            $rs[$i]['total'] = $total;
            
            $arrContainer = array();
            if(!empty($rs[$i]['containernumber']))  array_push($arrContainer, $rs[$i]['containernumber']);
            if(!empty($rs[$i]['container2number'])) array_push($arrContainer, $rs[$i]['container2number']);
            
            $arrSeal = array();
            if(!empty($rs[$i]['sealnumber'])) array_push($arrSeal, $rs[$i]['sealnumber']);
            if(!empty($rs[$i]['seal2number'])) array_push($arrSeal, $rs[$i]['seal2number']);
            
            $rs[$i]['containernumber'] = implode('<br>', $arrContainer);
            $rs[$i]['sealnumber'] = implode('<br>', $arrSeal);
        
        
            $arrRoute = array();
            if(!empty($rs[$i]['routefrom'])) array_push($arrRoute, $rs[$i]['routefrom']);
            if(!empty($rs[$i]['routeto'])) array_push($arrRoute, $rs[$i]['routeto']);
            $rs[$i]['route'] = implode(' - ', $arrRoute);
         
            // has detail
            if ($isShowDetail)
                $rs[$i]['_detail_'] = array('arrTemplate'=>$arrDetailTemplate,'data' => $rsDetail); 
                  
            $return = $obj->formatReportRows(array('data' => $rs[$i], 'style' => $arrHeaderStyle),$arrTemplate); 
            
            // ===== FOR EXPORT SECTION 
            array_push($dataToExport, $return['data']);  
            // ===== END FOR EXPORT SECTION
            
            $tempreport .= $return['html'];
            $arrTemplate[0]['total'] = $obj->arraySum($arrTemplate[0]['total'], $return['subtotal'][0]);
            
		} 
    
        $obj->generateReport($_POST, $tempreport, $arrTemplate,$dataToExport,$arrFilterInformation);
}
else{
   	$_POST['trStartDateTime'] = date('d / m / Y');
	$_POST['trEndDateTime'] = date('d / m / Y'); 
}
 

$rsJobType = $truckingJob->searchData($truckingJob->tableName.'.statuskey',1,true); 
//$rsSupplier = $supplier->searchData($supplier->tableName.'.statuskey',1,true); 
    
$arrStatus = $class->convertForCombobox($obj->getAllStatus(),'pkey','status');   
$arrJobType = $class->convertForCombobox($rsJobType,'pkey','name'); 
//$arrSupplier = $class->convertForCombobox($rsSupplier,'pkey','name'); 
$arrCargoType = $obj->convertForCombobox($obj->getCargoType(),'pkey','name');
$arrWarehouse = $class->convertForCombobox($warehouse->searchData($warehouse->tableName.'.statuskey',1,true,'','order by name asc'),'pkey','name');
$arrCar = $class->convertForCombobox($car->searchData($car->tableName.'.statuskey',1,true,'','order by policenumber asc'),'pkey','policenumber');
$arrCarCategory = $class->convertForCombobox($carCategory->searchData($carCategory->tableName.'.statuskey',1,true,'','order by name asc'),'pkey','name');
$arrCustomer = $class->convertForCombobox($customer->searchData($customer->tableName.'.statuskey',2,true,'','order by name asc'),'pkey','name');

$arrTwigVar['inputSelDateType'] =  $class->inputSelect('selDateType', $arrDateType);  
$arrTwigVar['inputWorkOrderCode'] =  $class->inputText('workOrderCode');
$arrTwigVar['inputSalesCode'] =  $class->inputText('salesCode'); 
//$arrTwigVar['inputHidCustomerKey'] =  $class->inputHidden('hidCustomerKey');
//$arrTwigVar['inputCustomerName'] =  $class->inputText('customerName');
$arrTwigVar['inputSelCustomer'] =  $class->inputSelect('selCustomer[]', $arrCustomer, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));

$arrTwigVar['inputOutsourceRegistrationNumber'] =  $class->inputText('outsourceRegistrationNumber'); 
$arrTwigVar['inputHidConsigneeKey'] =  $class->inputHidden('hidConsigneeKey');
$arrTwigVar['inputConsigneeName'] =  $class->inputText('consigneeName');
$arrTwigVar['inputSelWarehouse'] =  $class->inputSelect('selWarehouse[]', $arrWarehouse, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));  
$arrTwigVar['inputSelStatus'] =  $class->inputSelect('selStatus[]', $arrStatus, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputStartDateTime'] = $class->inputDate('trStartDateTime', array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputEndDateTime'] = $class->inputDate('trEndDateTime', array('etc' => 'style="text-align:center"'));   
$arrTwigVar['inputDONumber'] =  $class->inputText('doNumber');
$arrTwigVar['inputContainerNumber'] =  $class->inputText('containerNumber');
$arrTwigVar['inputShipmentNumber'] =  $class->inputText('shipmentNumber'); 
//$arrTwigVar['inputSelJobType'] =  $class->inputSelect('selSupplier[]', $arrSupplier, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox')); 
$arrTwigVar['inputSelJobType'] =  $class->inputSelect('selJobType[]', $arrJobType, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox')); 
$arrTwigVar['inputSelCargoType'] =  $class->inputSelect('selCargoType[]', $arrCargoType, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));   
$arrTwigVar['inputSelCarKey'] =  $class->inputSelect('selCarKey[]', $arrCar, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));   
$arrTwigVar['inputSelCarCategory'] =  $class->inputSelect('selCarCategory[]', $arrCarCategory, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));   
$arrTwigVar['inputIsShowDetail'] =  $class->inputCheckBox('isShowDetail');
$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate;   
echo $twig->render('reportTruckingServiceWorkOrder.html', $arrTwigVar);  
 
?>
