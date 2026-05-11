<?php	 
include '../../_config.php';  
include '../../_include-v2.php'; 

includeClass('AREmployee.class.php');
$arEmployee = createObjAndAddToCol(new AREmployee());
$arPayment = createObjAndAddToCol(new ARPayment());
$customer = createObjAndAddToCol(new Customer());
$warehouse = createObjAndAddToCol(new Warehouse());
$employee = createObjAndAddToCol(new Employee());
include '_global.php';

$obj= $arEmployee;
$arEmployeePayment = $obj->getPaymentObj();
$securityObject = 'reportAREmployee'; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true));

$_POST['selStatus[]'] = array(1,2);

$arrFilterInformation = array();    


// ===== FOR EXPORT SECTION
$dataToExport = array();

/* data structure */
$arrTemplate = array();

$arrDataStructure = array();
$arrDataStructure['rowNumber'] = array('title'=>'#', 'align'=>'right', 'width'=>"40px", 'autoNumber' => true,"sortable" => false);
$arrDataStructure['code'] = array('title'=>ucwords($obj->lang['code']),  'width'=>"100px", 'dbfield' => 'code');
$arrDataStructure['refcode'] = array('title'=>ucwords($obj->lang['refCode']),  'width'=>"120px", 'dbfield' => 'refcode');  
$arrDataStructure['jocode'] = array('title'=>ucwords($obj->lang['JOCode']),  'width'=>"120px", 'dbfield' => 'socode');  
$arrDataStructure['customer'] = array('title'=>ucwords($obj->lang['customer']),  'width'=>"150px", 'dbfield' => 'customername');  
$arrDataStructure['date'] = array('title'=>ucwords($obj->lang['date']),'dbfield' => 'trdate', 'width'=>"120px",'format'=>'date');
$arrDataStructure['duedate'] = array('title'=>ucwords($obj->lang['duedate']),'dbfield' => 'refdate', 'width'=>"120px",'format'=>'date');
$arrDataStructure['datediff'] = array('title'=>ucwords($obj->lang['aging']),'dbfield' => 'datediff', 'width'=>"90px", 'format' => 'number');
$arrDataStructure['warehouse'] = array('title'=>ucwords($obj->lang['warehouse']),'dbfield' => 'warehousename', 'width'=>"100px" );
$arrDataStructure['employee'] = array('title'=>ucwords($obj->lang['employee']),'dbfield' => 'employeename', 'width'=>"170px" );
$arrDataStructure['ammount'] = array('title'=>ucwords($obj->lang['amount']),'dbfield' => 'amount', 'width'=>"100px" ,'format'=>'number','calculateTotal' => true);
$arrDataStructure['outstanding'] = array('title'=>ucwords($obj->lang['outstanding']),'dbfield' => 'outstanding', 'width'=>"100px" ,'format'=>'number','calculateTotal' => true);
$arrDataStructure['note'] = array('title'=>ucwords($obj->lang['note']),'dbfield' => 'trdesc','width'=>"300px");
$arrDataStructure['status'] = array('title'=>ucwords($obj->lang['status']),'dbfield' => 'statusname', 'width'=>"100px" );
		  
$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $obj->lang['employeeAccountsReceivableReport']; 
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();

array_push($arrTemplate, $arrHeaderTemplate);


// detail ...
$arrDataDetailStructure = array();
$arrDataDetailStructure['arcode'] = array('title'=>ucwords($obj->lang['arCode']),  'dbfield' => 'code', 'width'=>'100px', 'format' => 'string' ); 
$arrDataDetailStructure['arpaymentdate'] = array('title'=>ucwords($obj->lang['date']),  'dbfield' => 'trdate', 'format' => 'date', 'width'=>'100px'); 
$arrDataDetailStructure['amount'] = array('title'=>ucwords($obj->lang['amount']),  'dbfield' => 'amount', 'width'=>"100px", 'format' => 'number' , 'width'=>'50px','calculateTotal' => true);

$arrDetailTemplate = array();
$arrDetailTemplate['reportWidth'] = "400px";
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

	/*if(isset($_POST) && !empty($_POST['employeeName'])) {
		$criteria .= ' AND '.$obj->tableEmployee.'.name LIKE ('.$class->oDbCon->paramString('%'.$_POST['employeeName'].'%').')';
		array_push($arrFilterInformation,array("label" => 'Nama Sales', 'filter' =>  $_POST['employeeName']));
	}*/
    
    if(isset($_POST) && !empty($_POST['selEmployee'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selEmployee']));   
        
       	$criteria .= ' AND '.$obj->tableName.'.customerkey in('.$key.')';  

        $rsCriteria = $employee->searchData('','',true, ' and '.$employee->tableName.'.pkey in ('.$key.')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$statusName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => 'Karyawan', 'filter' => $statusName ));
        
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
             $arrHeaderStyle = array();

            $rsPayment = $arEmployeePayment->getDetailPaymentByARKey($rs[$i]['pkey']);  
            if ($rs[$i]['datediff']  > 0 ){
                foreach($arrTemplate[0]['dataStructure'] as $key=>$el) 
                    if (isset($el['dbfield']))
                        $arrHeaderStyle[$el['dbfield']]['textColor'] = 'C41E3A';   
            }else{
                $arrHeaderStyle['outstanding']['textColor'] = '0093AF';  
            }

            $rsDetail = array();
            for ($j=0;$j<count($rsPayment);$j++){ 

                $rsApPayment= $arEmployeePayment->getDataRowById($rsPayment[$j]['refkey']);

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
}

$arrStatus = $class->convertForCombobox($obj->getAllStatus(),'pkey','status');
$arrWarehouse = $class->convertForCombobox($warehouse->searchData($warehouse->tableName.'.statuskey',1,true,'','order by name asc'),'pkey','name');
$arrEmployee = $class->convertForCombobox($employee->searchData($employee->tableName.'.statuskey',2,true,'','order by name asc'),'pkey','name');

$arrTwigVar['inputCode'] =  $class->inputText('code');
//$arrTwigVar['inputHidEmployeeKey'] = $class->inputHidden('hidEmployeeKey');
//$arrTwigVar['inputEmployeeName'] =  $class->inputText('employeeName');
$arrTwigVar['inputSelEmployee'] =  $class->inputSelect('selEmployee[]', $arrEmployee, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputStartDate'] = $class->inputDate('trStartDate',array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputEndDate'] = $class->inputDate('trEndDate',array('etc' => 'style="text-align:center"'));  
$arrTwigVar['inputSelStatus'] =  $class->inputSelect('selStatus[]', $arrStatus, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox')); 
$arrTwigVar['inputChkDueDate'] =  $class->inputCheckBox('chkDueDate',array('overwritePost' => false, 'value' => 0, 'class' => 'no-class'));  
$arrTwigVar['inputSelWarehouse'] =  $class->inputSelect('selWarehouse[]', $arrWarehouse, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
      
$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate; 

echo $twig->render('reportAREmployee.html', $arrTwigVar);   
?>

