<?php
include '../../_config.php';
include '../../_include-v2.php';

includeClass('BuildingUnit.class.php');
$bulding = createObjAndAddToCol(new BuildingUnit());
$customer = createObjAndAddToCol( new Customer()); 

include '_global.php';

$obj = $bulding;

$securityObject = 'reportBuildingUnit'; // the value of security object is manually inserted to handle 
// some modules that have different security object from that of their class

if (!$security->isAdminLogin($securityObject, 10, true));

$_POST['selStatus[]'] = array(2, 3);
$arrFilterInformation = array();
$detailCriteria = '';

// ===== FOR EXPORT SECTION
$dataToExport = array();

/* data structure */
$arrTemplate = array();

$arrDataStructure = array();
$_POST['module'] = IMPORT_TEMPLATE['buildingUnit'];

switch($EXPORT_TYPE){
    case 2 :
            $arrDataStructure['code'] = array('title'=>ucwords($obj->lang['code']),'dbfield' => 'code', 'width'=>"100px");
            $arrDataStructure['block'] = array('title'=>ucwords($obj->lang['block']),'dbfield' => 'block', 'width'=>"80px", "align"=>"center");
            $arrDataStructure['unit'] = array('title'=>ucwords($obj->lang['unit']),'dbfield' => 'unit', 'width'=>"80px", "align"=>"center"); 
            $arrDataStructure['ownername'] = array('title'=>ucwords($obj->lang['owner']),'dbfield' => 'ownername', 'width'=>"250px");
            $arrDataStructure['tenantname'] = array('title'=>ucwords($obj->lang['tenant']),'dbfield' => 'tenantname', 'width'=>"250px");
            $arrDataStructure['vanumber'] = array('title'=>ucwords($obj->lang['virtualAccount']),'dbfield' => 'vanumber', 'width'=>"100px"); 
            $arrDataStructure['unitSize'] = array('title'=>ucwords($obj->lang['unitSize']),'dbfield' => 'unitsize', 'format' => 'number','width'=>"100px"); 
            $arrDataStructure['status'] = array('title'=>ucwords($obj->lang['status']),'dbfield' => 'statusname', 'width'=>"100px", 'validation' => array_column($arrStatus,'status'));

            break;
        
    default :
			$arrDataStructure['rowNumber'] = array('title' => '#', 'align' => 'right', 'width' => "40px", 'autoNumber' => true, "sortable" => false);
            $arrDataStructure['code'] = array('title'=>ucwords($obj->lang['code']),'dbfield' => 'code', 'width'=>"100px");
            $arrDataStructure['block'] = array('title'=>ucwords($obj->lang['block']),'dbfield' => 'block', 'width'=>"80px", "align"=>"center");
            $arrDataStructure['unit'] = array('title'=>ucwords($obj->lang['unit']),'dbfield' => 'unit', 'width'=>"80px", "align"=>"center"); 
            $arrDataStructure['ownername'] = array('title'=>ucwords($obj->lang['owner']),'dbfield' => 'ownername', 'width'=>"250px");
            $arrDataStructure['tenantname'] = array('title'=>ucwords($obj->lang['tenant']),'dbfield' => 'tenantname', 'width'=>"250px");
            $arrDataStructure['totalResidents'] = array('title'=>ucwords($obj->lang['totalResidents']),'dbfield' => 'totalresidents','format'=>'number', 'width'=>"120px", "align" => 'right', "calculateTotal"=>true);
            $arrDataStructure['vanumber'] = array('title'=>ucwords($obj->lang['virtualAccount']),'dbfield' => 'vanumber', 'width'=>"150px"); 
            $arrDataStructure['unitSize'] = array('title'=>ucwords($obj->lang['unitSize']),'dbfield' => 'unitsize', 'format' => 'number', 'width'=>"100px", "align" => 'right'); 
            $arrDataStructure['aroutstanding'] = array('title'=>ucwords($obj->lang['outstanding']),'dbfield' => 'aroutstanding', 'width'=>"100px", 'format'=>'number','calculateTotal' => true);        
            $arrDataStructure['status'] = array('title'=>ucwords($obj->lang['status']),'dbfield' => 'statusname', 'width'=>"100px", 'validation' => array_column($arrStatus,'status'));
}

$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $obj->lang['buildingUnitReport'];
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();

array_push($arrTemplate, $arrHeaderTemplate);


if (isset($_POST) && !empty($_POST['hidAction'])) {

   $criteria = '';
   $criteriaArr = array();

   // untuk pencarian berdasarkan kode
   array_push( $criteriaArr, array( 'postVariable' => 'buildingUnitCode', 'fieldName' => $obj->tableName . '.code', 'label' => $obj->lang['code'] )  );

   $obj->createReportCriteria($criteria, $arrFilterInformation, $criteriaArr);

   $orderBy = (!empty($_POST['hidOrderBy'])) ? $obj->oDbCon->paramOrder($_POST['hidOrderBy']) : 'pkey'; // order by harus dr kolom yg terdaftar saja
   $orderType = (isset($_POST['hidOrderType']) && !empty($_POST['hidOrderType']) && $_POST['hidOrderType'] == 1) ? 'desc' : 'asc';

   $order = 'order by ' . $orderBy . ' ' . $orderType;

   // select data
   $rs = $obj->searchData('', '', true, $criteria, $order);
 
   $tempreport = '';

   for ($i = 0; $i < count($rs); $i++) {
   
      $return = $obj->formatReportRows(array('data' => $rs[$i]), $arrTemplate);
      // ===== FOR EXPORT SECTION 
      array_push($dataToExport, $return['data']);
      // ===== END FOR EXPORT SECTION
      $tempreport .= $return['html'];

      $arrTemplate[0]['total'] = $obj->arraySum($arrTemplate[0]['total'], $return['subtotal'][0]);
   }
	
   $obj->generateReport($_POST, $tempreport, $arrTemplate, $dataToExport, $arrFilterInformation);
}


$arrStatus = $class->convertForCombobox($obj->getAllStatus(), 'pkey', 'status');
$arrCustomer = $class->convertForCombobox($customer->getQuery(), 'pkey', 'name');

$arrTwigVar['importUrl'] = $obj->importUrl;
$arrTwigVar['inputCode'] =  $class->inputText('code');  
$arrTwigVar['inputBlock'] =  $class->inputText('block');  
$arrTwigVar['inputUnit'] =  $class->inputText('unit');  
$arrTwigVar['inputSelOwner'] = $class->inputSelect('ownerName[]', $arrCustomer, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
// $arrTwigVar['inputSelOwner'] = $class->inputSelect('selStatus[]', $arrStatus, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['arrTemplate'] = $arrHeaderTemplate;
echo $twig->render('reportBuildingUnit.html', $arrTwigVar);

?>