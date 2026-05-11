<?php
	 
include '../../_config.php'; 
include '../../_include-v2.php'; 

includeClass('Supplier.class.php');
$supplier = createObjAndAddToCol( new Supplier()); 
$supplierCategory = createObjAndAddToCol( new SupplierCategory()); 
$ap = createObjAndAddToCol( new AP()); 

include '_global.php';

$obj= $supplier;
$securityObject = 'reportSupplier'; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true));  
$hasAPAccess = $security->isAdminLogin($ap->securityObject,10);  

$arrFilterInformation = array();    

// ===== FOR EXPORT SECTION
$dataToExport = array();

/* data structure */
$arrTemplate = array();

$arrDataStructure = array();

$_POST['module'] = IMPORT_TEMPLATE['supplier'];

$arrCategory = $supplierCategory->searchData($supplierCategory->tableName.'.statuskey',1);

switch($EXPORT_TYPE){
    case 2 :
            $arrDataStructure['code'] = array('title'=>ucwords($obj->lang['code']),'dbfield' => 'code', 'width'=>"100px");
            $arrDataStructure['name'] = array('title'=>ucwords($obj->lang['name']),'dbfield' => 'name', 'width'=>"200px");
            $arrDataStructure['category'] = array('title'=>ucwords($obj->lang['category']),'dbfield' => 'categoryname', 'width'=>"100px", 'validation' => array_column($arrCategory,'name'));
            $arrDataStructure['address'] = array('title'=>ucwords($obj->lang['address']),'dbfield' => 'address', 'width'=>"250px");
            $arrDataStructure['city'] = array('title'=>ucwords($obj->lang['city']),'dbfield' => 'city', 'width'=>"100px");
            $arrDataStructure['zipcode'] = array('title'=>ucwords($obj->lang['zipcode']),'dbfield' => 'zipcode', 'width'=>"100px"); 
            $arrDataStructure['phone'] = array('title'=>ucwords($obj->lang['phone']),'dbfield' => 'phone', 'width'=>"100px");
            $arrDataStructure['mobile'] = array('title'=>ucwords($obj->lang['mobilePhone']),'dbfield' => 'mobile', 'width'=>"100px");
            $arrDataStructure['fax'] = array('title'=>ucwords($obj->lang['fax']),'dbfield' => 'fax', 'width'=>"100px");
            $arrDataStructure['email'] = array('title'=>ucwords($obj->lang['email']),'dbfield' => 'email', 'width'=>"150px");
    		$arrDataStructure['status'] = array('title'=>ucwords($obj->lang['status']),'dbfield' => 'statusname', 'width'=>"100px" );
		
            break;
        
    default : 
        $arrDataStructure['rowNumber'] = array('title'=>'#', 'align'=>'right', 'width'=>"40px", 'autoNumber' => true, "sortable" => false);
        $arrDataStructure['code'] = array('title'=>ucwords($obj->lang['code']),'dbfield' => 'code', 'width'=>"100px");
        $arrDataStructure['name'] = array('title'=>ucwords($obj->lang['name']),'dbfield' => 'name', 'width'=>"300px");
        $arrDataStructure['categoryname'] = array('title'=>ucwords($obj->lang['category']),'dbfield' => 'categoryname', 'width'=>"200px");
        $arrDataStructure['taxid'] = array('title'=>ucwords($obj->lang['taxIdentificationNumber']),'dbfield' => 'taxid', 'width'=>"200px");
        $arrDataStructure['address'] = array('title'=>ucwords($obj->lang['address']),'dbfield' => 'address', 'width'=>"250px", "sortable" => false);
        $arrDataStructure['phone'] = array('title'=>ucwords($obj->lang['phone']),'dbfield' => 'phone', 'width'=>"150px", "sortable" => false);
        $arrDataStructure['email'] = array('title'=>ucwords($obj->lang['email']),'dbfield' => 'email', 'width'=>"180px");
        $arrDataStructure['apoutstanding'] = array('title'=>ucwords($obj->lang['outstanding']),'dbfield' => 'apoutstanding', 'width'=>"100px", 'format'=>'number','calculateTotal' => true);
        $arrDataStructure['status'] = array('title'=>ucwords($obj->lang['status']),'dbfield' => 'statusname', 'width'=>"100px" );
}
  

$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $obj->lang['supplierReport']; 
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();
 
array_push($arrTemplate, $arrHeaderTemplate);

// ===== END FOR EXPORT SECTION

if (isset($_POST) && !empty($_POST['hidAction'])){  
		  
	$criteria = '';
	if(isset($_POST) && !empty($_POST['supplierCode'])) {
		$criteria .= ' AND '.$obj->tableName.'.code LIKE ('.$class->oDbCon->paramString('%'.$_POST['supplierCode'].'%').')';
		array_push($arrFilterInformation,array("label" => 'Kode', 'filter' => $_POST['supplierCode']));
	}
	if(isset($_POST) && !empty($_POST['supplierName'])) {
		$criteria .= ' AND '.$obj->tableName.'.name LIKE  ('.$class->oDbCon->paramString('%'.$_POST['supplierName'].'%').')'; 
		array_push($arrFilterInformation,array("label" => 'Nama', 'filter' =>  $_POST['supplierName']));
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
 
    //rows
    if (empty($rs)) 
        $tempreport .= '<tr class="report-row rewrite-row"><td colspan="'.count($arrHeaderTemplate['dataStructure']).'"></td></tr>';	
   
    for( $i=0;$i<count($rs);$i++) {     
        
        $arrAddress = array();
        if(!empty($rs[$i]['address1']))  array_push($arrAddress,$rs[$i]['address1']);
        if(!empty($rs[$i]['address2']))  array_push($arrAddress,$rs[$i]['address2']);

        $arrCity = array();
        if(!empty($rs[$i]['cityname']))  array_push($arrCity,$rs[$i]['cityname']);
        if(!empty($rs[$i]['citycategoryname']))  array_push($arrCity,$rs[$i]['citycategoryname']);

        $city = implode(', ', $arrCity); 
        
         
        switch($EXPORT_TYPE){
            case 2 :  
                $rs[$i]['city']  = $city;
                break;
            default :
  
                if(!empty($city))  array_push($arrAddress,$city);   
                
                $arrPhone = array();
                if(!empty($rs[$i]['phone']))  array_push($arrPhone,$rs[$i]['phone']);
                if(!empty($rs[$i]['mobile']))  array_push($arrPhone,$rs[$i]['mobile']);
                $rs[$i]['phone'] = implode('<br>',$arrPhone); 
        }
 
        $rs[$i]['address'] = implode('<br>',$arrAddress); 
        
        $apoustanding = ($hasAPAccess) ? $rs[$i]['apoutstanding'] : 0;
   
        $rs[$i]['apoutstanding'] = $apoustanding; 

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

$arrTwigVar['importUrl'] = $obj->importUrl; 
$arrTwigVar['inputSupplierCode'] =  $class->inputText('supplierCode');  
$arrTwigVar['inputSupplierName'] =  $class->inputText('supplierName');   
$arrTwigVar['inputSelStatus'] =  $class->inputSelect('selStatus[]', $arrStatus, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox')); 
$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate;   
  

echo $twig->render('reportSupplier.html', $arrTwigVar);
  
?>
