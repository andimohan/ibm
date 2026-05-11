<?php
	 
include '../../_config.php';  
include '../../_include-v2.php'; 

includeClass('Car.class.php');
$car = createObjAndAddToCol( new Car());
$carCategory = createObjAndAddToCol( new CarCategory());
$brand = createObjAndAddToCol( new Brand());
$warehouse = createObjAndAddToCol( new Warehouse());
    
include '_global.php';

$obj= $car;
$securityObject = 'reportCar'; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true)); 

$arrFilterInformation = array();    

// ===== FOR EXPORT SECTION
$dataToExport = array();

/* data structure */
$arrTemplate = array();
$_POST['module'] = IMPORT_TEMPLATE['car'];
$arrDataStructure = array();

switch($EXPORT_TYPE){
    case 2 :
            $arrDataStructure['code'] = array('title'=>ucwords($obj->lang['code']),'dbfield' => 'code', 'width'=>"100px");
            $arrDataStructure['noPolisi'] = array('title'=>ucwords($obj->lang['carRegistrationNumber']),'dbfield' => 'policenumber','width'=>"90px");
            $arrDataStructure['brand'] = array('title'=>ucwords($obj->lang['brand']),'dbfield' => 'brandname', 'width'=>"150px");
            $arrDataStructure['category'] = array('title'=>ucwords($obj->lang['category']),'dbfield' => 'categoryname', 'width'=>"100px");
            $arrDataStructure['year'] = array('title'=>ucwords($obj->lang['year']),'dbfield' => 'year', 'width'=>"70px", 'align' => 'center');
            $arrDataStructure['bpkbname'] = array('title'=>ucwords($obj->lang['bpkbRegisteredName']),'dbfield' => 'bpkbname', 'width'=>"200px");
            $arrDataStructure['bpkbnumber'] = array('title'=>ucwords($obj->lang['bpkbRegisteredNumber']),'dbfield' => 'bpkbnumber', 'width'=>"130px");
            $arrDataStructure['licensenumber'] = array('title'=>ucwords($obj->lang['stnkNumber']),'dbfield' => 'licensenumber', 'width'=>"130px");
            $arrDataStructure['licenseperiod'] = array('title'=>ucwords($obj->lang['stnkExpiredDate']),'dbfield' => 'licenseexpirydate', 'width'=>"120px", 'format' => 'date');
            $arrDataStructure['licensetaxexpperiod'] = array('title'=>ucwords($obj->lang['licenseTaxExpiryDate']),'dbfield' => 'licensetaxexpirydate', 'width'=>"120px", 'format' => 'date');
            $arrDataStructure['kirnumber'] = array('title'=>ucwords($obj->lang['kirNumber']),'dbfield' => 'kir', 'width'=>"70px",'width'=>"130px");
            $arrDataStructure['kirperiod'] = array('title'=>ucwords($obj->lang['kirExpiredDate']),'dbfield' => 'kirexpirydate', 'width'=>"120px", 'format' => 'date', 'text-align' =>'center');
            //$arrDataStructure['tidnumber'] = array('title'=>ucwords($obj->lang['tidNumber']),'dbfield' => 'tid', 'width'=>"130px");
            //$arrDataStructure['tidperiod'] = array('title'=>ucwords($obj->lang['tidExpiredDate']),'dbfield' => 'tidexpirydate', 'width'=>"120px", 'format' => 'date');
            $arrDataStructure['machinenumber'] = array('title'=>ucwords($obj->lang['machineNumber']),'dbfield' => 'machinenumber', 'width'=>"100px");
            $arrDataStructure['chasisnumber'] = array('title'=>ucwords($obj->lang['chassisNumber']),'dbfield' => 'chassisnumber', 'width'=>"150px");
            $arrDataStructure['status'] = array('title'=>ucwords($obj->lang['status']),'dbfield' => 'statusname', 'width'=>"100px");

            break;
        
    default :
            $arrDataStructure['rowNumber'] = array('title'=>'#', 'align'=>'right', 'width'=>"40px", 'autoNumber' => true, "sortable" => false);
            $arrDataStructure['code'] = array('title'=>ucwords($obj->lang['code']),'dbfield' => 'code' , 'width'=>"100px");
            $arrDataStructure['warehouse'] = array('title'=>ucwords($obj->lang['warehouse']),'dbfield' => 'warehousename', 'width'=>"100px");
            $arrDataStructure['noPolisi'] = array('title'=>ucwords($obj->lang['carRegistrationNumber']),'dbfield' => 'policenumber','width'=>"90px");
            $arrDataStructure['year'] = array('title'=>ucwords($obj->lang['year']),'dbfield' => 'year', 'width'=>"70px", 'align' => 'center');
            $arrDataStructure['brand'] = array('title'=>ucwords($obj->lang['brand']),'dbfield' => 'brandname', 'width'=>"150px");
            $arrDataStructure['category'] = array('title'=>ucwords($obj->lang['category']),'dbfield' => 'categoryname', 'width'=>"100px");
            $arrDataStructure['bpkbnumber'] = array('title'=>ucwords($obj->lang['bpkbRegisteredNumber']),'dbfield' => 'bpkbnumber', 'width'=>"130px");
            $arrDataStructure['bpkbname'] = array('title'=>ucwords($obj->lang['bpkbRegisteredName']),'dbfield' => 'bpkbname', 'width'=>"200px");
            $arrDataStructure['kirnumber'] = array('title'=>ucwords($obj->lang['kirNumber']),'dbfield' => 'kir', 'width'=>"70px",'width'=>"130px");
            $arrDataStructure['kirperiod'] = array('title'=>ucwords($obj->lang['kirExpiredDate']),'dbfield' => 'kirexpirydate', 'width'=>"120px", 'format' => 'date', 'text-align' =>'center');
            $arrDataStructure['licensenumber'] = array('title'=>ucwords($obj->lang['stnkNumber']),'dbfield' => 'licensenumber', 'width'=>"130px");
            $arrDataStructure['licenseperiod'] = array('title'=>ucwords($obj->lang['stnkExpiredDate']),'dbfield' => 'licenseexpirydate', 'width'=>"120px", 'format' => 'date');
            $arrDataStructure['licensetaxexpperiod'] = array('title'=>ucwords($obj->lang['licenseTaxExpiryDate']),'dbfield' => 'licensetaxexpirydate', 'width'=>"120px", 'format' => 'date');
            $arrDataStructure['tidnumber'] = array('title'=>ucwords($obj->lang['tidNumber']),'dbfield' => 'tid', 'width'=>"130px");
            $arrDataStructure['tidperiod'] = array('title'=>ucwords($obj->lang['tidExpiredDate']),'dbfield' => 'tidexpirydate', 'width'=>"120px", 'format' => 'date');
            $arrDataStructure['machinenumber'] = array('title'=>ucwords($obj->lang['machineNumber']),'dbfield' => 'machinenumber', 'width'=>"100px");
            $arrDataStructure['chasisnumber'] = array('title'=>ucwords($obj->lang['chassisNumber']),'dbfield' => 'chassisnumber', 'width'=>"150px");
            $arrDataStructure['status'] = array('title'=>ucwords($obj->lang['status']),'dbfield' => 'statusname', 'width'=>"100px");
}





$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $obj->lang['carReport']; 
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();

array_push($arrTemplate, $arrHeaderTemplate);
	
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
	    array_push($arrFilterInformation,array("label" => 'Gudang', 'filter' => $statusName ));
        
	}
    
    // untuk pencarian berdasarkan kode
	if(isset($_POST) && !empty($_POST['carCode'])) {
		$criteria .= ' AND '.$obj->tableName.'.code LIKE ('.$class->oDbCon->paramString('%'.$_POST['carCode'].'%').')';
		array_push($arrFilterInformation,array("label" => 'Kode', 'filter' => $_POST['carCode']));
	}

    // untuk pencarian berdasarkan nama
    if(isset($_POST) && !empty($_POST['carNumber'])) {
		$criteria .= ' AND '.$obj->tableName.'.policenumber LIKE  ('.$class->oDbCon->paramString('%'.$_POST['carNumber'].'%').')'; 
		array_push($arrFilterInformation,array("label" => 'No. Polisi', 'filter' =>  $_POST['carNumber']));
	} 
	
    if(isset($_POST) && !empty($_POST['selBrand'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selBrand']));   
        
       	$criteria .= ' AND '.$brand->tableName.'.pkey in('.$key.')';  

        $rsCriteria =  $brand->searchData('','',true, ' AND '.$brand->tableName.'.pkey in('.$key.')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$statusName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => 'Merk', 'filter' => $statusName));
        
	}  
    
	
    if(isset($_POST) && !empty($_POST['selCategory'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selCategory']));   
         
       	$criteria .= ' AND '.$obj->tableName.'.categorykey in('.$key.')';  
		
        $rsCriteria =  $carCategory->searchData('','',true,' AND '.$carCategory->tableName.'.pkey in('.$key.')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$statusName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => 'Kategori', 'filter' => $statusName));
        
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
      
    // select data
    $rs = $obj->searchData('','',true,$criteria,$order);
    $tempreport = ''; 

    for( $i=0;$i<count($rs);$i++) {     
        $return = $obj->formatReportRows(array('data' => $rs[$i]),$arrTemplate);
        // ===== FOR EXPORT SECTION 
        array_push($dataToExport, $return['data']);  
        // ===== END FOR EXPORT SECTION
        $tempreport .= $return['html'];  
    }	  
    $obj->generateReport($_POST, $tempreport, $arrTemplate,$dataToExport,$arrFilterInformation);
}

$arrStatus = $class->convertForCombobox($obj->getAllStatus(),'pkey','status');    
$arrCategory = $class->convertForCombobox($carCategory->searchData($carCategory->tableName.'.statuskey',1,true, ' and '.$carCategory->tableName.'.isleaf = 1', ' order by name asc'),'pkey','name');   
$arrBrand = $class->convertForCombobox($brand->searchData('','',true, ' and '.$brand->tableName.'.statuskey = 1','order by name asc'),'pkey','name'); 
$arrWarehouse = $class->convertForCombobox($warehouse->searchData($warehouse->tableName.'.statuskey',1,true,'','order by name asc'),'pkey','name');

$arrTwigVar['importUrl'] = $obj->importUrl;  
$arrTwigVar['inputCarCode'] =  $class->inputText('carCode');  
$arrTwigVar['inputCarNumber'] =  $class->inputText('carNumber');   
$arrTwigVar['inputSelCategory'] =  $class->inputSelect('selCategory[]', $arrCategory, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox')); 
$arrTwigVar['inputSelWarehouse'] =  $class->inputSelect('selWarehouse[]', $arrWarehouse, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));  
$arrTwigVar['inputSelBrand'] =  $class->inputSelect('selBrand[]', $arrBrand, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox')); 
$arrTwigVar['inputSelStatus'] =  $class->inputSelect('selStatus[]', $arrStatus, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox')); 
      
$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate;   

echo $twig->render('reportCar.html', $arrTwigVar);  
    
?>