<?php
	 
include '../../_config.php';  
include '../../_include-v2.php';

includeClass('Employee.class.php');
$employee = createObjAndAddToCol(new Employee());
$ar = createObjAndAddToCol(new AR());
$warehouse = createObjAndAddToCol(new Warehouse());

include '_global.php';

$obj= $employee;
$securityObject = 'ReportEmployee'; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true));  
$hasARAccess = $security->isAdminLogin($ar->securityObject,10);  
 

$arrFilterInformation = array();     
 
// ===== FOR EXPORT SECTION
$dataToExport = array();

/* data structure */
$arrTemplate = array();

$arrDataStructure = array();
$_POST['module'] = IMPORT_TEMPLATE['employee'];

switch($EXPORT_TYPE){
    case 2 :
            $arrDataStructure['code'] = array('title'=>ucwords($obj->lang['code']),'dbfield' => 'code', 'width'=>"100px");
            $arrDataStructure['name'] = array('title'=>ucwords($obj->lang['name']),'dbfield' => 'name', 'width'=>"200px");
            $arrDataStructure['warehouseName'] = array('title'=>ucwords($obj->lang['warehouse']),'dbfield' => 'warehousename', 'width'=>"100px");
            $arrDataStructure['division'] = array('title'=>ucwords($obj->lang['division']),'dbfield' => 'categoryname', 'width'=>"150px");
            $arrDataStructure['driver'] = array('title'=>ucwords($obj->lang['driver']),'dbfield' => 'isdriver', 'width'=>"60px");
            $arrDataStructure['salesman'] = array('title'=>ucwords($obj->lang['salesman']),'dbfield' => 'issales', 'width'=>"60px");
            $arrDataStructure['placeOfBirth'] = array('title'=>ucwords($obj->lang['placeOfBirth']),'dbfield' => 'placeofbirth', 'width'=>"150px");
            $arrDataStructure['dateOfBirth'] = array('title'=>ucwords($obj->lang['dateOfBirth']),'dbfield' => 'dateofbirth', 'width'=>"120px",'format'=>'date');
            $arrDataStructure['livingAddress'] = array('title'=>ucwords($obj->lang['livingAddress']),'dbfield' => 'livingaddress', 'width'=>"250px");
            $arrDataStructure['address'] = array('title'=>ucwords($obj->lang['address']),'dbfield' => 'address', 'width'=>"250px");
            $arrDataStructure['city'] = array('title'=>ucwords($obj->lang['city']),'dbfield' => 'city', 'width'=>"150px");
            $arrDataStructure['phone'] = array('title'=>ucwords($obj->lang['phone']),'dbfield' => 'phone', 'width'=>"120px");
            $arrDataStructure['drivingLicense'] = array('title'=>ucwords($obj->lang['drivingLicense']),'dbfield' => 'drivinglicense', 'width'=>"150px");
            $arrDataStructure['drivingLicenseExpirationDate'] = array('title'=>ucwords($obj->lang['drivingLicenseExpirationDate']),'dbfield' => 'drivinglicenseexpdate', 'width'=>"120px",'format'=>'date');
            $arrDataStructure['IDNumber'] = array('title'=>ucwords($obj->lang['IDNumber']),'dbfield' => 'idnumber', 'width'=>"150px");

            break;
        
    default :
            $arrDataStructure['rowNumber'] = array('title'=>'#', 'align'=>'right', 'width'=>"40px", 'autoNumber' => true, "sortable" => false);
            $arrDataStructure['code'] = array('title'=>ucwords($obj->lang['code']),'dbfield' => 'code', 'width'=>"100px");
            $arrDataStructure['name'] = array('title'=>ucwords($obj->lang['name']),'dbfield' => 'name', 'width'=>"200px");
            $arrDataStructure['warehouseName'] = array('title'=>ucwords($obj->lang['warehouse']),'dbfield' => 'warehousename', 'width'=>"100px");
           /*  $arrDataStructure['driver'] = array('title'=>ucwords($obj->lang['driver']),'dbfield' => 'isdriver', 'width'=>"60px");
            $arrDataStructure['salesman'] = array('title'=>ucwords($obj->lang['salesman']),'dbfield' => 'issales', 'width'=>"60px");*/
            $arrDataStructure['livingAddress'] = array('title'=>ucwords($obj->lang['livingAddress']),'dbfield' => 'livingaddress', 'width'=>"250px", "sortable" => false);
            $arrDataStructure['address'] = array('title'=>ucwords($obj->lang['address']) . ' KTP','dbfield' => 'address', 'width'=>"250px", "sortable" => false);
            $arrDataStructure['phone'] = array('title'=>ucwords($obj->lang['phone']),'dbfield' => 'phone', 'width'=>"150px", "sortable" => false);
            $arrDataStructure['email'] = array('title'=>ucwords($obj->lang['email']),'dbfield' => 'email', 'width'=>"150px");
            $arrDataStructure['status'] = array('title'=>ucwords($obj->lang['status']),'dbfield' => 'statusname', 'width'=>"100px");
}
  
$arrHeaderTemplate = array();  
$arrHeaderTemplate['reportTitle'] = $obj->lang['employeeReport']; 
$arrHeaderTemplate['dataStructure'] = $arrDataStructure; 
$arrHeaderTemplate['total'] = array();
 
array_push($arrTemplate, $arrHeaderTemplate);

// ===== END FOR EXPORT SECTION

if (isset($_POST) && !empty($_POST['hidAction'])){  
		
	$criteria = '';
    
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
    
	if(isset($_POST) && !empty($_POST['employeeCode'])) {
		$criteria .= ' AND '.$obj->tableName.'.code LIKE ('.$class->oDbCon->paramString('%'.$_POST['employeeCode'].'%').')';
		array_push($arrFilterInformation,array("label" => 'Kode', 'filter' => $_POST['employeeCode']));
	}
    
	if(isset($_POST) && !empty($_POST['employeeName'])) {
		$criteria .= ' AND '.$obj->tableName.'.name LIKE  ('.$class->oDbCon->paramString('%'.$_POST['employeeName'].'%').')'; 
		array_push($arrFilterInformation,array("label" => 'Nama', 'filter' =>  $_POST['employeeName']));
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
    
    // ============================= GENERATE DATA ============================= 
 
    for( $i=0;$i<count($rs);$i++) {      
   
        $rs[$i]['issales'] = ($rs[$i]['issales']==1) ? 'Ya' : '';
        $rs[$i]['isdriver'] = ($rs[$i]['isdriver']==1) ? 'Ya' : '';
        
        $arrLivingAddress = array();
        if(!empty($rs[$i]['livingaddress1']))  array_push($arrLivingAddress,$rs[$i]['livingaddress1']);
        if(!empty($rs[$i]['livingaddress2']))  array_push($arrLivingAddress,$rs[$i]['livingaddress2']); 
        $rs[$i]['livingaddress'] = implode('<br>',$arrLivingAddress); 
        
        $arrAddress = array();
        if(!empty($rs[$i]['address1']))  array_push($arrAddress,$rs[$i]['address1']);
        if(!empty($rs[$i]['address2']))  array_push($arrAddress,$rs[$i]['address2']); 
         
        $arrCity = array(); 
        if(!empty($rs[$i]['cityname']))  array_push($arrCity,$rs[$i]['cityname']);
        if(!empty($rs[$i]['citycategoryname']))  array_push($arrCity,$rs[$i]['citycategoryname']);
 
        $cityName = implode(', ', $arrCity); 
         
        switch($EXPORT_TYPE){
            case 2 :  
                
                $rs[$i]['city']  = $cityName;
                
                $rsPOB = $city->getDataRowById($rs[$i]['placeofbirth']);
                $rsCategoryPOB = $cityCategory->getDataRowById($rsPOB[0]['categorykey']);
                
                $arrPobCity = array(); 
                if(!empty($rsPOB[0]['name']))  array_push($arrPobCity,$rsPOB[0]['name']);
                if(!empty($rsCategoryPOB[0]['name']))  array_push($arrPobCity,$rsCategoryPOB[0]['name']);
                $rs[$i]['placeofbirth']  = implode(', ', $arrPobCity);
                break;
                
             default :
                
                
                if(!empty($cityName))  array_push($arrAddress,$cityName);   
                
                $arrPhone = array();
                if(!empty($rs[$i]['phone']))  array_push($arrPhone,$rs[$i]['phone']);
                if(!empty($rs[$i]['mobile']))  array_push($arrPhone,$rs[$i]['mobile']);
                $rs[$i]['phone'] = implode('<br>',$arrPhone); 
        }
         
        $rs[$i]['address'] = implode('<br>',$arrAddress); 


        
        $return = $obj->formatReportRows(array('data' => $rs[$i]),$arrTemplate);
        
        // ===== FOR EXPORT SECTION 
        array_push($dataToExport, $return['data']); 
        // ===== END FOR EXPORT SECTION
        
        $tempreport .= $return['html']; 
         
        // count subtotal for each col
        $arrTemplate[0]['total'] = $obj->arraySum($arrTemplate[0]['total'], $return['subtotal'][0]); 
         
    }
		 
    $obj->generateReport($_POST, $tempreport, $arrTemplate,$dataToExport,$arrFilterInformation);
}

$arrStatus = $class->convertForCombobox($obj->getAllStatus(),'pkey','status');   
$arrWarehouse = $class->convertForCombobox($warehouse->searchData($warehouse->tableName.'.statuskey',1,true,'','order by name asc'),'pkey','name');


$arrTwigVar['inputSelWarehouse'] =  $class->inputSelect('selWarehouse[]', $arrWarehouse, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));  
$arrTwigVar['inputEmployeeCode'] =  $class->inputText('employeeCode');  
$arrTwigVar['inputEmployeeName'] =  $class->inputText('employeeName');   
$arrTwigVar['inputSelStatus'] =  $class->inputSelect('selStatus[]', $arrStatus, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox')); 
$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate;   
      
echo $twig->render('reportEmployee.html', $arrTwigVar);  
 
?>