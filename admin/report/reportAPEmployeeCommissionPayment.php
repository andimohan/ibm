<?php   

include '../../_config.php';  
include '../../_include-v2.php';

includeClass(array('APEmployeeCommissionPayment.class.php','Warehouse.class.php','Employee.class.php'));
$apEmployeeCommissionPayment = createObjAndAddToCol( new APEmployeeCommissionPayment()); 
$warehouse = createObjAndAddToCol( new Warehouse()); 
$employee = createObjAndAddToCol( new Employee());

include '_global.php';

$obj= $apEmployeeCommissionPayment;
$ap = $obj->getAPObj();
$securityObject = 'reportAPEmployeeCommissionPayment'; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
if(!$security->isAdminLogin($securityObject,10,true));
$_POST['selStatus[]'] = array(2,3);

$arrFilterInformation = array();   

// ===== FOR EXPORT SECTION
$dataToExport = array();

/* data structure */
$arrTemplate = array();

$arrDataStructure = array();
$arrDataStructure['rowNumber'] = array('title'=>'#', 'align'=>'right', 'width'=>"40px", 'autoNumber' => true, "sortable" => false);
$arrDataStructure['code'] = array('title'=>ucwords($obj->lang['code']), 'width'=>"100px", 'dbfield'=>'code');
$arrDataStructure['date'] = array('title'=>ucwords($obj->lang['date']), 'align'=>'center', 'width'=>"120px", 'dbfield'=>'trdate','format'=>'date');
$arrDataStructure['warehousename'] = array('title'=>ucwords($obj->lang['warehouse']), 'width'=>"120px", 'dbfield'=>'warehousename');
$arrDataStructure['employeename'] = array('title'=>ucwords($obj->lang['employee']), 'width'=>"160px", 'dbfield'=>'employeename');
$arrDataStructure['total'] = array('title'=>ucwords($obj->lang['total']), 'width'=>"120px", 'format'=>'number', 'dbfield'=>'totalpaid', 'calculateTotal' => true);
$arrDataStructure['tax'] = array('title'=>ucwords($obj->lang['tax23']), 'width'=>"100px", 'format'=>'number', 'dbfield'=>'payabletax23', 'calculateTotal' => true);
$arrDataStructure['status'] = array('title'=>ucwords($obj->lang['status']), 'width'=>"100px", 'dbfield'=>'statusname');

$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $obj->lang['employeeCommissionPaymentReport']; 
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();

array_push($arrTemplate, $arrHeaderTemplate);

$arrDataDetailStructure = array();
$arrDataDetailStructure['apcode'] = array('title'=>ucwords($obj->lang['apCode']),  'dbfield' => 'apcode', 'width'=>'100px', 'format' => 'string' ); 
$arrDataDetailStructure['trdate'] = array('title'=>ucwords($obj->lang['date']),  'dbfield' => 'trdate', 'width'=>'100px', 'format' => 'date' ); 
$arrDataDetailStructure['refcode'] = array('title'=>ucwords($obj->lang['refCode']),  'dbfield' => 'refcode', 'width'=>'100px', 'format' => 'string' ); 
$arrDataDetailStructure['refDate'] = array('title'=>ucwords($obj->lang['jobsDate']),'dbfield' => 'refdate', 'width'=>"120px",'format'=>'date');
$arrDataDetailStructure['amount'] = array('title'=>ucwords($obj->lang['amount']),  'dbfield' => 'amountap', 'width'=>"120px", 'format' => 'number' ,'calculateTotal' => true);
$arrDataDetailStructure['outstanding'] = array('title'=>ucwords($obj->lang['outstanding']),  'dbfield' => 'outstanding', 'width'=>"120px", 'format' => 'number' ,'calculateTotal' => true);
$arrDataDetailStructure['payment'] =    array('title'=>ucwords($obj->lang['payment']),  'dbfield' => 'amount', 'width'=>"120px", 'format' => 'number' ,'calculateTotal' => true); 

$arrDetailTemplate = array();
$arrDetailTemplate['reportWidth'] = "900px";
$arrDetailTemplate['dataStructure'] = $arrDataDetailStructure;
$arrDetailTemplate['total'] = array();

array_push($arrTemplate, $arrDetailTemplate);


if (isset($_POST) && !empty($_POST['hidAction'])){   
	
	$criteria = '';
	
	if(isset($_POST) && !empty($_POST['code'])) {
		$criteria .= ' AND '.$obj->tableName.'.code LIKE ('.$class->oDbCon->paramString('%'.$_POST['code'].'%').')';
		array_push($arrFilterInformation,array("label" => 'Kode', 'filter' => $_POST['code']));
	}
	
	if(isset($_POST) && !empty($_POST['trStartDate'])){
		$criteria .= ' AND '.$obj->tableName.'.trdate between '.$class->oDbCon->paramDate( $_POST['trStartDate'],' / ').' AND '.$class->oDbCon->paramDate( $_POST['trEndDate'],' / ','Y-m-d 23:59'); 
		array_push($arrFilterInformation,array("label" => 'Tanggal', 'filter' => $_POST['trStartDate'] . ' - ' .$_POST['trEndDate'] ));
	}
    
	if(isset($_POST) && !empty($_POST['chkDueDate'])){ 
        $criteria .= ' having datediff > 0';
        array_push($arrFilterInformation,array("label" => 'Aging', 'filter' => 'Tampilkan hanya yang jatuh tempo'));
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
    
    $orderBy = (!empty($_POST['hidOrderBy'])) ? $obj->oDbCon->paramOrder($_POST['hidOrderBy']) : 'pkey'; // order by harus dr kolom yg terdaftar saja
    $orderType = (isset($_POST['hidOrderType']) && !empty($_POST['hidOrderType']) && $_POST['hidOrderType'] == 1) ? 'desc' : 'asc';
 
	 
	$order = 'order by '.$orderBy.' ' .$orderType; 
	   
    $rs = $obj->searchData('','',true,$criteria,$order); 
    $tempreport = '';  
	 
		for( $i=0;$i<count($rs);$i++) {   
            $rsDetail = $obj->getDetailById($rs[$i]['pkey']); 
        if (empty($rsDetail))
            continue;

        for ($j=0;$j<count($rsDetail);$j++){   
            $rsAP = $ap->getDataRowById($rsDetail[$j]['apkey']);
            $rsDetail[$j]['apcode'] =  $rsAP[0]['code'];
            $rsDetail[$j]['refcode'] =  $rsAP[0]['refcode']; 
            $rsDetail[$j]['trdate'] =  $rsAP[0]['trdate'];
            $rsDetail[$j]['refdate'] =  $rsAP[0]['refdate'];
            $rsDetail[$j]['amountap'] =  $rsAP[0]['amount']; 
        }

        // has detail
        $rs[$i]['_detail_'] = array('arrTemplate'=>$arrDetailTemplate,'data' => $rsDetail);

        $return = $obj->formatReportRows(array('data' => $rs[$i]),$arrTemplate); 

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
}

$arrStatus = $class->convertForCombobox($obj->getAllStatus(),'pkey','status');
$arrWarehouse = $class->convertForCombobox($warehouse->searchData($warehouse->tableName.'.statuskey',1,true,'','order by name asc'),'pkey','name');
$arrEmployee = $class->convertForCombobox($employee->searchData($employee->tableName.'.statuskey',2,true,'','order by name asc'),'pkey','name');

$arrTwigVar['inputCode'] =  $class->inputText('code');
$arrTwigVar['inputHidEmployeeKey'] = $class->inputHidden('hidEmployeeKey');
$arrTwigVar['inputStartDate'] = $class->inputDate('trStartDate',array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputEndDate'] = $class->inputDate('trEndDate',array('etc' => 'style="text-align:center"'));  
$arrTwigVar['inputSelEmployee'] =  $class->inputSelect('selEmployee[]', $arrEmployee, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox')); 
$arrTwigVar['inputSelStatus'] =  $class->inputSelect('selStatus[]', $arrStatus, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox')); 
$arrTwigVar['inputChkDueDate'] =  $class->inputCheckBox('chkDueDate',array('overwritePost' => false, 'value' => 0, 'class' => 'no-class'));  
$arrTwigVar['inputSelWarehouse'] =  $class->inputSelect('selWarehouse[]', $arrWarehouse, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
      
$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate; 

echo $twig->render('reportAPEmployeeCommissionPayment.html', $arrTwigVar);   


?>
