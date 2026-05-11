<?php
 
includeClass(array('AP.class.php','APPayment.class.php','APEmployeeCommission.class.php','APEmployeeCommissionPayment.class.php','Warehouse.class.php','Employee.class.php','Location.class.php','DisposalContract.class.php'));
$apEmployeeCommission = createObjAndAddToCol( new APEmployeeCommission()); 
$warehouse = createObjAndAddToCol( new Warehouse()); 
$employee = createObjAndAddToCol( new Employee());
$location = new Location();
$disposalContract = new DisposalContract();
 
$obj= $apEmployeeCommission;
$apEmployeeCommissionPayment = $obj->getPaymentObj();
$securityObject = 'reportAPEmployeeCommission'; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
if(!$security->isAdminLogin($securityObject,10,true));

$_POST['selStatus[]'] = array(1,2);

$arrFilterInformation = array();   

// ===== FOR EXPORT SECTION
$dataToExport = array();

/* data structure */
$arrTemplate = array();

$arrDataStructure = array();
$arrDataStructure['rowNumber'] = array('title'=>'#', 'align'=>'right', 'width'=>"40px", 'autoNumber' => true, "sortable" => false);
$arrDataStructure['code'] = array('title'=>ucwords($obj->lang['code']), 'width'=>"100px", 'dbfield'=>'code');
$arrDataStructure['date'] = array('title'=>ucwords($obj->lang['date']), 'align'=>'center', 'width'=>"120px", 'dbfield'=>'trdate','format'=>'date');
//$arrDataStructure['duedate'] = array('title'=>ucwords($obj->lang['duedate']), 'align'=>'center', 'width'=>"120px", 'dbfield'=>'duedate','format'=>'date');
$arrDataStructure['reference'] = array('title'=>ucwords($obj->lang['reference']), 'width'=>"120px", 'dbfield'=>'refcode');
$arrDataStructure['refcode2'] = array('title'=>ucwords($obj->lang['reference']. ' 2'), 'width'=>"180px", 'dbfield'=>'refcode2');
$arrDataStructure['refdate'] = array('title'=>ucwords($obj->lang['jobsDate']), 'align'=>'center', 'width'=>"120px", 'dbfield'=>'refdate','format'=>'date');
$arrDataStructure['employee'] = array('title'=>ucwords($obj->lang['employee']), 'width'=>"160px", 'dbfield'=>'employeename');
$arrDataStructure['customer'] = array('title'=>ucwords($obj->lang['customer']), 'width'=>"250px", 'dbfield'=>'customername', "sortable" => false);
//$arrDataStructure['warehouse'] = array('title'=>ucwords($obj->lang['warehouse']), 'width'=>"120px", 'dbfield'=>'warehousename');
$arrDataStructure['currency'] = array('title'=>ucwords($obj->lang['currencyShort']), 'align'=>'center', 'width'=>"50px", 'dbfield'=>'currencyname');
$arrDataStructure['amount'] = array('title'=>ucwords($obj->lang['amount']), 'width'=>"110px",'format'=>'number', 'dbfield'=>'amount', 'calculateTotal' => true);
$arrDataStructure['outstanding'] = array('title'=>ucwords($obj->lang['outstanding']), 'width'=>"110px",'format'=>'number', 'dbfield'=>'outstanding', 'calculateTotal' => true);
$arrDataStructure['arstatus'] = array('title'=>ucwords($obj->lang['ARStatus']), 'width'=>"100px", 'dbfield'=>'arstatusname');
$arrDataStructure['status'] = array('title'=>ucwords($obj->lang['status']), 'width'=>"100px", 'dbfield'=>'statusname');

$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $obj->lang['apEmployeeCommissionReport']; 
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();

$arrDataDetailStructure = array();
$arrDataDetailStructure['apcode'] = array('title'=>ucwords($obj->lang['apCode']),  'dbfield' => 'code', 'width'=>'100px', 'format' => 'string' ); 
$arrDataDetailStructure['appaymentdate'] = array('title'=>ucwords($obj->lang['date']),  'dbfield' => 'trdate', 'format' => 'date', 'width'=>'100px'); 
$arrDataDetailStructure['amount'] = array('title'=>ucwords($obj->lang['amount']),  'dbfield' => 'amount', 'width'=>"100px", 'format' => 'number' , 'width'=>'50px','calculateTotal' => true);

$arrDetailTemplate = array();
$arrDetailTemplate['reportWidth'] = "400px";
$arrDetailTemplate['dataStructure'] = $arrDataDetailStructure;
$arrDetailTemplate['total'] = array();

array_push($arrTemplate, $arrHeaderTemplate);


/* search*/
if (isset($_POST) && !empty($_POST['hidAction'])){   
	
	$criteria = '';
	
	if(isset($_POST) && !empty($_POST['code'])) {
		$criteria .= ' AND '.$obj->tableName.'.code LIKE ('.$class->oDbCon->paramString('%'.$_POST['code'].'%').')';
		array_push($arrFilterInformation,array("label" => 'Kode', 'filter' => $_POST['code']));
	}
	
	if(isset($_POST) && !empty($_POST['trStartDate'])){
        
        $dateField =  ($_POST['selDateType'][0] == 1) ? 'trdate' : 'refdate';
		$criteria .= ' AND '.$obj->tableName.'.'.$dateField.' between '.$class->oDbCon->paramDate( $_POST['trStartDate'],' / ').' AND '.$class->oDbCon->paramDate( $_POST['trEndDate'],' / ','Y-m-d 23:59'); 
		
        array_push($arrFilterInformation,array("label" => ($_POST['selDateType'][0] == 1) ? $obj->lang['transactionDate'] : $obj->lang['jobsDate'] , 'filter' => $_POST['trStartDate'] . ' - ' .$_POST['trEndDate'] ));
	}
    
 
	if(isset($_POST) && !empty($_POST['selWarehouse'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selWarehouse']));   
        
       	$criteria .= ' AND  '.$obj->tableName.'.warehousekey in('.$key.')';  

        $rsCriteria = $warehouse->searchData('','',true, ' and '.$warehouse->tableName.'.pkey in ('.$key.')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$statusName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => 'Gudang', 'filter' => $statusName ));
        
	}
    
    if(isset($_POST) && !empty($_POST['selEmployee'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selEmployee']));   
        
       	$criteria .= ' AND '.$obj->tableName.'.employeekey in('.$key.')';  

        $rsCriteria =  $employee->searchData('','',true, ' and '.$employee->tableName.'.pkey in ('.$key.')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$employeeName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => 'Karyawan', 'filter' => $employeeName));
        
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
       
    
	if(isset($_POST) && !empty($_POST['selARStatus'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selARStatus']));   
        
       	$criteria .= ' AND '.$obj->tableName.'.arstatuskey in('.$key.')';  

        $rsCriteria =  $obj->getStatusById ($key);
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['status']);
			
		$statusName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => $obj->lang['ARStatus'], 'filter' => $statusName));
        
	} 
    
	if(isset($_POST) && !empty($_POST['chkDueDate'])){ 
        $criteria .= ' having datediff > 0';
        array_push($arrFilterInformation,array("label" => 'Aging', 'filter' => 'Tampilkan hanya yang jatuh tempo'));
    }
    
    $orderBy = (!empty($_POST['hidOrderBy'])) ? $obj->oDbCon->paramOrder($_POST['hidOrderBy']) : 'pkey'; // order by harus dr kolom yg terdaftar saja
    $orderType = (isset($_POST['hidOrderType']) && !empty($_POST['hidOrderType']) && $_POST['hidOrderType'] == 1) ? 'desc' : 'asc';
 
	 
	$order = 'order by '.$orderBy.' ' .$orderType; 
	   
    $rs = $obj->searchData('','',true,$criteria,$order); 
    $arrContractKey = array_column($rs, 'refkey2');
    $rsContract = $disposalContract->searchData('','',true,' and ' . $disposalContract->tableName . '.pkey in (' . $disposalContract->oDbCon->paramString($arrContractKey, ',') . ')');
    $rsContract = array_column($rsContract, null, 'pkey');
    $tempreport = '';  
	 
		for( $i=0;$i<count($rs);$i++) { 
             $arrHeaderStyle = array();
            $rsPayment = $apEmployeeCommissionPayment->getDetailPaymentByAPKey($rs[$i]['pkey']);  
            if ($rs[$i]['datediff']  > 0 ){
                foreach($arrTemplate[0]['dataStructure'] as $key=>$el) 
                    if (isset($el['dbfield']))
                        $arrHeaderStyle[$el['dbfield']]['textColor'] = 'C41E3A';   
            }else{
                $arrHeaderStyle['outstanding']['textColor'] = '0093AF';  
            }
            $rs[$i]['customername'] = $rsContract[$rs[$i]['refkey2']]['customername'];
            $rsDetail = array();    /* detail per primary key */
            for ($j=0;$j<count($rsPayment);$j++){ 

                $rsApPayment= $apEmployeeCommissionPayment->getDataRowById($rsPayment[$j]['refkey']);

                $arrTemp = array();
                $arrTemp['code'] = $rsApPayment[0]['code'];
                $arrTemp['trdate'] = $rsApPayment[0]['trdate'];
                $arrTemp['amount'] = $rsPayment[$j]['amount']; 

                array_push($rsDetail, $arrTemp);
            }
            
            // has detail
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
   	$_POST['trStartDate'] = date('d / m / Y');
	$_POST['trEndDate'] = date('d / m / Y');  
    $_POST['selDateType'] = 2;
}

$arrStatus = $class->convertForCombobox($obj->getAllStatus(),'pkey','status');
$arrDateType = array('1' =>  $obj->lang['transactionDate'], '2' =>  $obj->lang['jobsDate']);
$arrWarehouse = $class->convertForCombobox($warehouse->searchData($warehouse->tableName.'.statuskey',1,true,'','order by name asc'),'pkey','name');
$arrLocation = $class->convertForCombobox($location->searchData($location->tableName.'.statuskey',1,true,'','order by name asc'),'pkey','name');
$arrEmployee = $class->convertForCombobox($employee->searchData($employee->tableName.'.statuskey',2,true,'','order by name asc'),'pkey','name');

$arrTwigVar['inputCode'] =  $class->inputText('code');
$arrTwigVar['inputHidEmployeeKey'] = $class->inputHidden('hidEmployeeKey');
$arrTwigVar['inputConsigneeName'] =  $class->inputText('consigneeName'); 
$arrTwigVar['inputStartDate'] = $class->inputDate('trStartDate',array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputEndDate'] = $class->inputDate('trEndDate',array('etc' => 'style="text-align:center"'));  
$arrTwigVar['inputSelStatus'] =  $class->inputSelect('selStatus[]', $arrStatus, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox')); 
$arrTwigVar['inputSelARStatus'] =  $class->inputSelect('selARStatus[]', $arrStatus, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox')); 
$arrTwigVar['inputSelDateType'] =  $class->inputSelect('selDateType', $arrDateType ); 
$arrTwigVar['inputSelEmployee'] =  $class->inputSelect('selEmployee[]', $arrEmployee, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox')); 
$arrTwigVar['inputChkDueDate'] =  $class->inputCheckBox('chkDueDate',array('overwritePost' => false, 'value' => 0, 'class' => 'no-class'));  
$arrTwigVar['inputSelWarehouse'] =  $class->inputSelect('selWarehouse[]', $arrWarehouse, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
      
$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate; 

echo $twig->render('@custom/reportAPEmployeeCommission.html', $arrTwigVar);   
?>
