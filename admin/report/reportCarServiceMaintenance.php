<?php
	 
include '../../_config.php';  
include '../../_include-v2.php';
include '_global.php';

includeClass('CarServiceMaintenance.class.php');
$carServiceMaintenance = createObjAndAddToCol(new CarServiceMaintenance()); 
$brand = createObjAndAddToCol(new Brand()); 
$car = createObjAndAddToCol(new Car()); 
$carCategory = createObjAndAddToCol(new CarCategory()); 
$chassis = createObjAndAddToCol(new Chassis()); 
$itemUnit = createObjAndAddToCol(new ItemUnit()); 
$termOfPayment = createObjAndAddToCol(new TermOfPayment()); 
$warehouse = createObjAndAddToCol(new Warehouse()); 
$paymentMethod = createObjAndAddToCol(new PaymentMethod()); 
$shipment = createObjAndAddToCol(new Shipment()); 
$supplier = createObjAndAddToCol(new Supplier()); 
$item = createObjAndAddToCol(new Item()); 
$itemCategory = createObjAndAddToCol(new ItemCategory());


$obj= $carServiceMaintenance;
$securityObject = 'reportCarServiceMaintenance'; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true));   
   
$arrFilterInformation = array(); 
//$detailCriteria = 'and refcashoutkey is not null and requestamount is not null';
$_POST['selStatus[]'] = array(2,3);  
// ====================== must be set before TWIG
if (!isset($_POST['trStartDate']) || empty($_POST['trStartDate'])){ 
	$_POST['trStartDate'] = date('d / m / Y');
	$_POST['trEndDate'] = date('d / m / Y');
}   

$orderCriteria = array(); 
$orderCriteria['orderBy'] =  (isset ($_POST) && !empty($_POST['hidOrderBy']) ) ?  $obj->oDbCon->paramOrder($_POST['hidOrderBy']) : 'trdate'; //$obj->tableName.'.
$orderCriteria['orderType'] = (isset ($_POST) && !empty($_POST['hidOrderType'])) ?   $_POST['hidOrderType'] : -1; 
// ====================== must be set before TWIG 


// ===== FOR EXPORT SECTION
$dataToExport = array();
$detailCriteria = '';

/* data structure */
$arrTemplate = array();
//$isShowDetail = (isset($_POST['isShowDetail']) && !empty($_POST['isShowDetail'])) ? true : false;
$isGrouping = (isset($_POST['isGrouping']) && !empty($_POST['isGrouping'])) ? true : false;

$arrDataStructure = array();
$arrDataStructure['rowNumber'] = array('title'=>'#', 'align'=>'right', 'width'=>"40px", 'autoNumber' => true, "sortable" => false);
$arrDataStructure['code'] = array('title'=>ucwords($obj->lang['code']),  'width'=>"100px", 'dbfield' => 'code');
$arrDataStructure['refcode'] = array('title'=>ucwords($obj->lang['refCode']),  'width'=>"100px", 'dbfield' => 'refcode');
$arrDataStructure['date'] = array('title'=>ucwords($obj->lang['date']),'dbfield' => 'trdate', 'width'=>"90px",'format'=>'date');
$arrDataStructure['executeDate'] = array('title'=>ucwords($obj->lang['executeDate']),'dbfield' => 'executedate', 'width'=>"110px",'format'=>'date');
$arrDataStructure['warehouse'] = array('title'=>ucwords($obj->lang['warehouse']),'dbfield' => 'warehousename', 'width'=>"90px");
$arrDataStructure['technician'] = array('title'=>ucwords($obj->lang['technician']),'dbfield' => 'technicianname', 'width'=>"150px");
$arrDataStructure['supplier'] = array('title'=>ucwords($obj->lang['supplier']),'dbfield' => 'suppliername', 'width'=>"150px");
$arrDataStructure['car'] = array('title'=>ucwords($obj->lang['vehicleCode']),'dbfield' => 'vehiclecode', 'width'=>"150px"); 
$arrDataStructure['chassis'] = array('title'=>ucwords($obj->lang['carRegistrationNumber']),'dbfield' => 'policenumber', 'width'=>"150px"); 
$arrDataStructure['mileage'] = array('title'=>ucwords($obj->lang['mileage']),'dbfield' => 'mileage', 'width'=>"100px",'format'=>'number'); 

if(!$isGrouping){ 
    $arrDataStructure['itemOrService'] = array('title'=>ucwords($obj->lang['itemOrService']),'dbfield' => 'itemname', 'width'=>"200px"); 
    $arrDataStructure['description'] = array('title'=>ucwords($obj->lang['description']),'dbfield' => 'detaildesc', 'width'=>"150px");
    $arrDataStructure['qty'] = array('title'=>ucwords($obj->lang['qty']),  'width'=>"80px", 'dbfield' => 'qtyinbaseunit','format'=>'number');
    $arrDataStructure['item_unit'] = array('title'=>ucwords($obj->lang['unit']),'dbfield' => 'unitname', 'width'=>"120px"); 
    $arrDataStructure['price'] = array('title'=>ucwords($obj->lang['price']),'dbfield' => 'priceinunit', 'width'=>"150px",'format'=>'number'); 
    $arrDataStructure['discount'] = array('title'=>ucwords($obj->lang['discount']),'dbfield' => 'discount', 'width'=>"150px",'format'=>'number'); 
    $arrDataStructure['subtotal'] = array('title'=>ucwords($obj->lang['subtotal']),'dbfield' => 'total','align'=>'right', 'width'=>"120px",'format'=>'number','calculateTotal' => true);        

}else{
    $arrDataStructure['subtotal'] = array('title'=>ucwords($obj->lang['subtotal']),'dbfield' => 'subtotal','align'=>'right', 'width'=>"120px",'format'=>'number','calculateTotal' => true);
    $arrDataStructure['discount'] = array('title'=>ucwords($obj->lang['discount']),'dbfield' => 'finaldiscount','align'=>'right', 'width'=>"90px",'format'=>'number','calculateTotal' => true);
        //$arrDataStructure['ppn'] = array('title'=>ucwords($obj->lang['PPN']),'dbfield' => 'discount','align'=>'right', 'width'=>"90px",'format'=>'number','sortable' => false,'calculateTotal' => true);
    $arrDataStructure['total'] = array('title'=>ucwords($obj->lang['total']),'dbfield' => 'grandtotal','align'=>'right', 'width'=>"120px",'format'=>'number','calculateTotal' => true);        
    $arrDataStructure['totalPayment'] = array('title'=>ucwords($obj->lang['totalPayment']),'dbfield' => 'totalpayment','align'=>'right', 'width'=>"150px",'format'=>'number');

}
            
$arrDataStructure['payment'] = array('title'=>ucwords($obj->lang['payment']),'dbfield' => 'top', 'width'=>"150px");
$arrDataStructure['note'] = array('title'=>ucwords($obj->lang['note']), 'width'=>"300px",'dbfield' => 'trnotes');
$arrDataStructure['status'] = array('title'=>ucwords($obj->lang['status']),'dbfield' => 'statusname', 'width'=>"80px");
		   
$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $obj->lang['carMaintenanceReport']; 
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();

array_push($arrTemplate, $arrHeaderTemplate);


if ($isGrouping){ 
	// detail ...
	$arrDataDetailStructure = array();
	$arrDataDetailStructure['itemOrService'] = array('title'=>ucwords($obj->lang['itemOrService']), 'dbfield' => 'itemname','width'=>"200px");  
    $arrDataDetailStructure['description'] = array('title'=>ucwords($obj->lang['description']),'dbfield' => 'trdesc', 'width'=>"150px"); 
	$arrDataDetailStructure['qty'] = array('title'=>ucwords($obj->lang['qty']),  'dbfield' => 'qtyinbaseunit', 'width'=>"80px" , 'format' => 'number'); 
	$arrDataDetailStructure['unit'] = array('title'=>ucwords($obj->lang['unit']),  'dbfield' => 'unitname', 'width'=>"50px" ,'calculateTotal' => true ); 
	$arrDataDetailStructure['price'] = array('title'=>ucwords($obj->lang['price']),  'dbfield' => 'priceinunit', 'width'=>"80px" , 'format' => 'number' ,'calculateTotal' => true ); 
	$arrDataDetailStructure['subamount'] = array('title'=>ucwords($obj->lang['subtotal']),  'dbfield' => 'subtotal', 'width'=>"100px" , 'format' => 'number' ,'calculateTotal' => true );  
	$arrDataDetailStructure['discount'] = array('title'=>ucwords($obj->lang['discount']),  'dbfield' => 'discount', 'width'=>"80px" , 'format' => 'number' ,'calculateTotal' => true );  
	$arrDataDetailStructure['amount'] = array('title'=>ucwords($obj->lang['total']),  'dbfield' => 'total', 'width'=>"100px" , 'format' => 'number' ,'calculateTotal' => true );  

	$arrDetailTemplate = array();
	$arrDetailTemplate['reportWidth'] = "500px";
	$arrDetailTemplate['dataStructure'] = $arrDataDetailStructure;
	$arrDetailTemplate['total'] = array();

	array_push($arrTemplate, $arrDetailTemplate); 
}

 

    
$arrStatus = $class->convertForCombobox($obj->getAllStatus(),'pkey','status');   
$arrWarehouse = $class->convertForCombobox($warehouse->searchData($warehouse->tableName.'.statuskey',1,true,'','order by name asc'),'pkey','name');
$arrCar = $class->convertForCombobox($car->searchData($car->tableName.'.statuskey',1,true,'','order by policenumber asc'),'pkey','policenumber');
$arrChassis = $class->convertForCombobox($chassis->searchData($chassis->tableName.'.statuskey',1,true,'','order by chassisnumber asc'),'pkey','chassisnumber');
$arrTechnician = $class->convertForCombobox($employee->searchData($employee->tableName.'.statuskey',2,true,'','order by name asc'),'pkey','name');
$arrSupplier = $class->convertForCombobox($supplier->searchData($supplier->tableName.'.statuskey',1,true,'','order by name asc'),'pkey','name');
$arrType = $obj->convertForCombobox($obj->getMaintenanceType(),'pkey','name');    
$arrItem = $class->convertForCombobox($item->searchData($item->tableName.'.statuskey',1,true,'', 'order by name asc'), 'pkey', 'name');
$arrItemCategory = $class->convertForCombobox($itemCategory->searchData($itemCategory->tableName . '.statuskey', 1, true, '', 'order by name asc'), 'pkey', 'name');

$arrTwigVar['inputCode'] =  $class->inputText('carMaintenanceCode'); ;
//$arrTwigVar['inputHidTechnicianKey'] =  $class->inputHidden('hidTechnicianKey');
//$arrTwigVar['inputTechnicianName'] =  $class->inputText('technicianName');
//$arrTwigVar['inputHidSupplierKey'] =  $class->inputHidden('hidSupplierKey');
//$arrTwigVar['inputSupplierName'] =  $class->inputText('supplierName');
$arrTwigVar['inputSelType'] =  $class->inputSelect('selType', $arrType);
$arrTwigVar['inputSelItem'] =  $class->inputSelect('selItem[]', $arrItem, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));  
$arrTwigVar['inputSelItemCategory'] =  $class->inputSelect('selItemCategory[]', $arrItemCategory, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox')); 
$arrTwigVar['inputSelTechnician'] =  $class->inputSelect('selTechnician[]', $arrTechnician, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));  
$arrTwigVar['inputSelSupplier'] =  $class->inputSelect('selSupplier[]', $arrSupplier, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));  
$arrTwigVar['inputSelWarehouse'] =  $class->inputSelect('selWarehouse[]', $arrWarehouse, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));  
$arrTwigVar['inputSelCar'] =  $class->inputSelect('selCarKey[]', $arrCar, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));  
$arrTwigVar['inputSelChassis'] =  $class->inputSelect('selChassisKey[]', $arrChassis, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));  
$arrTwigVar['inputSelStatus'] =  $class->inputSelect('selStatus[]', $arrStatus, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputStartDate'] = $class->inputDate('trStartDate', array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputEndDate'] = $class->inputDate('trEndDate', array('etc' => 'style="text-align:center"'));   
$arrTwigVar['inputSelCarKey'] =  $class->inputSelect('selCarKey[]', $arrCar, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));   
$arrTwigVar['inputIsGrouping'] =  $class->inputCheckBox('isGrouping', array('value'=> 1));
$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate;   

if (isset($_POST) && !empty($_POST['hidAction'])){  
		
	$criteria = '';
	if(isset($_POST) && !empty($_POST['carMaintenanceCode'])) {
		$criteria .= ' AND '.$obj->tableName.'.code LIKE ('.$class->oDbCon->paramString('%'.$_POST['carMaintenanceCode'].'%').')';
		array_push($arrFilterInformation,array("label" => 'Kode', 'filter' => $_POST['carMaintenanceCode']));
	}
    
	if(isset($_POST) && !empty($_POST['trStartDate'])){
		$criteria .= ' and '.$obj->tableName.'.trdate between '.$class->oDbCon->paramDate( $_POST['trStartDate'],' / ').' AND '.$class->oDbCon->paramDate( $_POST['trEndDate'] .'',' / '); 
		array_push($arrFilterInformation,array("label" => 'Tanggal', 'filter' => $_POST['trStartDate'] . ' - ' .$_POST['trEndDate'] ));
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
    
    if(isset($_POST) && !empty($_POST['selType'])) { 
        
        $key = $class->oDbCon->paramString($_POST['selType']);   
        
       	$criteria .= ' AND '.$obj->tableName.'.typekey in('.$key.')'; 
        $rsCriteria = $obj->getMaintenanceType($key);
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$typeName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => $obj->lang['type'], 'filter' => $typeName ));
         
        switch ($_POST['selType']) {
				
				
            case 1 :
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
  				break;
            case 2 :
                if(isset($_POST) && !empty($_POST['selChassisKey'])) { 
                    
                    $key = implode(",", $class->oDbCon->paramString($_POST['selChassisKey']));   
                    
                    $criteria .= ' AND '.$obj->tableName.'.chassiskey in('.$key.')';  
            
                    $rsCriteria =  $chassis->searchData('','',true, ' and '.$chassis->tableName.'.statuskey = 1  and '.$chassis->tableName.'.pkey in ('.$key.')');
                
                    $arrTempStatus = array();
                    for ($k=0;$k<count($rsCriteria);$k++)
                        array_push($arrTempStatus,$rsCriteria[$k]['chassisnumber']);
                        
                    $statusName = implode(", ",$arrTempStatus); 
                    array_push($arrFilterInformation,array("label" => 'No Chassis', 'filter' => $statusName));
                    
                } 
				break;
        }
	}


	
    if(isset($_POST) && !empty($_POST['selTechnician'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selTechnician']));   
        
       	$criteria .= ' AND '.$obj->tableName.'.techniciankey in('.$key.')';  

        $rsCriteria =  $employee->searchData('','',true, ' and '.$employee->tableName.'.statuskey = 2  and '.$employee->tableName.'.pkey in ('.$key.')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$statusName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => 'Nama Teknisi', 'filter' => $statusName));
        
	}
    
    if(isset($_POST) && !empty($_POST['selSupplier'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selSupplier']));   
        
       	$criteria .= ' AND '.$obj->tableName.'.supplierkey in('.$key.')';  

        $rsCriteria =  $supplier->searchData('','',true, ' and '.$supplier->tableName.'.statuskey = 1  and '.$supplier->tableName.'.pkey in ('.$key.')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$statusName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => 'Nama Supplier', 'filter' => $statusName));
        
	}
    if(isset($_POST) && !empty($_POST['selItemCategory'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selItemCategory']));   

        if(!$isGrouping) {
            $criteria .= ' AND '.$item->tableName.'.categorykey in ('.$key.')';  
        } else {
            $detailCriteria .= ' AND ' . $item->tableName . '.categorykey in (' . $key . ')';
        }
        
        $rsCriteria = $itemCategory->searchData('','',true, ' and '.$itemCategory->tableName.'.statuskey = 1  and '.$itemCategory->tableName.'.pkey in ('.$key.')');
	
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		    array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$statusName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => 'Kategori Barang', 'filter' => $statusName));
        
	}

    if(isset($_POST) && !empty($_POST['selItem'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selItem']));

        if (!$isGrouping) {
            $criteria .= ' AND ' . $obj->tableNameDetail . '.itemkey in (' . $key . ')';
        } else {
            $detailCriteria .= ' AND ' . $obj->tableNameDetail . '.itemkey in (' . $key . ')';
        }

        $rsCriteria =  $item->searchData('','',true, ' and '.$item->tableName.'.statuskey = 1  and '.$item->tableName.'.pkey in ('.$key.')');
	
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		    array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$statusName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => 'Nama Barang', 'filter' => $statusName));
        
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
	$rs =  (!$isGrouping) ? $obj->generateCarMaintenanceReport($criteria,$order) : $obj->searchData('','',true,$criteria,$order); 
    

    
    $tempreport = ''; 
     
    if (empty($rs)) 
			$tempreport .= '<tr class="report-row rewrite-row"><td colspan="'.count($arrHeaderTemplate['dataStructure']).'"></td></tr>';
    
    $rsDetailCol = $obj->getDetailCollections($rs,'refkey', $detailCriteria);


    
    $totalRs = count($rs);
    for( $i=0;$i<$totalRs;$i++) {   
          $rsDetail = $rsDetailCol[$rs[$i]['pkey']];  
        $totalRsDetail = count($rsDetail);
           
            $rsTOP = $termOfPayment->getDataRowById($rs[$i]['termofpaymentkey']);
        
            if(!empty($rsTOP)){
                
                $rs[$i]['top'] = $rsTOP[0]['name'];
            }
        
                $subtotal = $rs[$i]['subtotal'];
         
            
            $finalDiscount = $rs[$i]['finaldiscount'];
            $finalDiscountType = $rs[$i]['finaldiscounttype'];
        
            if ($finalDiscount != 0){
                if ($finalDiscountType == 2) 
                    $rs[$i]['finaldiscount'] = $finalDiscount/100 * $subtotal;
            }
            
        
            if($isGrouping){ 
            // model normal
            
                if (empty($rsDetail))
                    continue;
    
        
                
                for($j=0;$j<count($rsDetail);$j++){

                    $price = $rsDetail[$j]['priceinunit'];
                    $qty = $rsDetail[$j]['qtyinbaseunit'];
                    $total = $qty * $price;

                    $discount = $rsDetail[$j]['discount'];
                    $discounttype = $rsDetail[$j]['discounttype'];

                    if ($discount != 0){
                        if ($discounttype == 2) 
                            $rsDetail[$j]['discount'] = $discount/100 * $total;
                    }

                    $rsDetail[$j]['subtotal'] = $total;


                    $rs[$i]['_detail_'] = array('arrTemplate'=>$arrDetailTemplate,'data' => $rsDetail); 
            }

        }
        
         
            $return = $obj->formatReportRows(array('data' => $rs[$i]),$arrTemplate); 
            
            // ===== FOR EXPORT SECTION 
            array_push($dataToExport, $return['data']);  
            // ===== END FOR EXPORT SECTION
            
            $tempreport .= $return['html'];
            $arrTemplate[0]['total'] = $obj->arraySum($arrTemplate[0]['total'], $return['subtotal'][0]);
            
		} 
    
   $tableHeader = $twig->render('template-header.html', $arrTwigVar);
    $obj->generateReport($_POST, $tempreport, $arrTemplate,$dataToExport,$arrFilterInformation,$tableHeader);
}

echo $twig->render('reportCarServiceMaintenance.html', $arrTwigVar);  
 
?>
