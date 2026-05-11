<?php

include '../../_config.php';
include '../../_include-v2.php';

includeClass(array('PackagingCode.class.php','Packaging.class.php','Item.class.php','Supplier.class.php'));

$packagingCode = createObjAndAddToCol(new PackagingCode());
$packaging = createObjAndAddToCol(new Packaging());
$item = createObjAndAddToCol(new Item());
$supplier = createObjAndAddToCol(new Supplier());

include '_global.php';

$obj = $packagingCode;
$securityObject = 'ReportPackagingCode'; // the value of security object is manually inserted to handle 
// some modules that have different security object from that of their class

if (!$security->isAdminLogin($securityObject, 10, true));

$arrFilterInformation = array();

$_POST['selStatus[]'] = array(1);

// ===== FOR EXPORT SECTION
$dataToExport = array();
/* data structure */
$arrTemplate = array();

$arrDataStructure = array();
$arrDataStructure['rowNumber'] = array('title' => '#', 'align' => 'right', 'width' => "40px", 'autoNumber' => true, "sortable" => false);
$arrDataStructure['code'] = array('title' => ucwords($obj->lang['code']), 'width' => "150px", 'dbfield' => 'code');
$arrDataStructure['date'] = array('title' => ucwords($obj->lang['date']), 'dbfield' => 'trdate', 'width' => "120px", 'format' => 'date');
$arrDataStructure['packaging'] = array('title'=>ucwords($obj->lang['packaging']),'dbfield' => 'packagingname', 'width'=>"120px");
$arrDataStructure['itemCode'] = array('title' => ucwords($obj->lang['itemCode']), 'dbfield' => 'itemcode', 'width' => "150px");
$arrDataStructure['item'] = array('title' => ucwords($obj->lang['itemName']), 'dbfield' => 'itemname', 'width' => "150px");
$arrDataStructure['itemAlias'] = array('title' => ucwords($obj->lang['alias']), 'dbfield' => 'itemalias', 'width' => "130px");
$arrDataStructure['supplier'] = array('title' => ucwords($obj->lang['supplier']), 'dbfield' => 'suppliername', 'width' => "150px");
$arrDataStructure['qty'] = array('title' => ucwords($obj->lang['qty']), 'dbfield' => 'qtyinbaseunit', 'width' => "70px", 'format' => 'number', 'calculateTotal' => true);
$arrDataStructure['baseUnit'] = array('title' => ucwords($obj->lang['unit']), 'dbfield' => 'baseunitname', 'width' => "60px");
$arrDataStructure['qtyInPcs'] = array('title' => ucwords($obj->lang['qty'] . ' Gr'), 'dbfield' => 'qtyinpcs', 'width' => "70px", 'format' => 'number', 'calculateTotal' => true);
//$arrDataStructure['cost'] = array('title' => ucwords($obj->lang['cost']), 'dbfield' => 'costinbaseunit', 'width' => "100px", 'format' => 'number', 'calculateTotal' => true);
//$arrDataStructure['costInPcs'] = array('title' => ucwords($obj->lang['cost'] . ' (Gr)'), 'dbfield' => 'costinpcs', 'width' => "100px", 'format' => 'number', 'calculateTotal' => true);
$arrDataStructure['description'] = array('title' => ucwords($obj->lang['description']), 'dbfield' => 'trdesc', 'width' => "200px");
$arrDataStructure['status'] = array('title' => ucwords($obj->lang['status']), 'dbfield' => 'statusname', 'width' => "100px");

$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $obj->lang['packagingCodeReport'];
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();

array_push($arrTemplate, $arrHeaderTemplate);

if (isset($_POST) && !empty($_POST['hidAction'])){  
		
    	
	$criteria = '';
	$criteriaArr = array();

    array_push($criteriaArr, array('postVariable' => array('trStartDate', 'trEndDate'), 
								   'fieldName' => $obj->tableName.'.trdate', 
								   'label' =>  $obj->lang['period'], 
								   'type' => 'daterange'));

    array_push($criteriaArr, array('postVariable' => 'packagingCode', 
				'fieldName' => $obj->tableName.'.code', 
				'label' => $obj->lang['code']));
    array_push($criteriaArr, array('postVariable' => 'itemCode', 
				'fieldName' => $obj->tableItem.'.code', 
				'label' => $obj->lang['itemCode']));

    array_push($criteriaArr, array(
        'postVariable' => 'selSupplier',
        'fieldName' => $obj->tableName . '.supplierkey',
        'label' => $obj->lang['supplier'],
        'useArrayKey' => array('obj' => $supplier)
    ));

    array_push($criteriaArr, array(
        'postVariable' => 'selItem',
        'fieldName' => $obj->tableName . '.itemkey',
        'label' => $obj->lang['item'],
        'useArrayKey' => array('obj' => $item)
    ));

    array_push($criteriaArr, array(
        'postVariable' => 'selPackaging',
        'fieldName' => $obj->tableName . '.packagingkey',
        'label' => $obj->lang['packaging'],
        'useArrayKey' => array('obj' => $packaging)
    ));


    array_push($criteriaArr, array(
                'postVariable' => 'selStatus',
                'type' => 'status'
            ));

    $obj->createReportCriteria($criteria,$arrFilterInformation,$criteriaArr);

    $orderBy = (!empty($_POST['hidOrderBy'])) ? $obj->oDbCon->paramOrder($_POST['hidOrderBy']) : 'pkey'; // order by harus dr kolom yg terdaftar saja
    $orderType = (isset($_POST['hidOrderType']) && !empty($_POST['hidOrderType']) && $_POST['hidOrderType'] == 1) ? 'desc' : 'asc';


    $order = 'order by ' . $orderBy . ' ' . $orderType;

    $rs = $obj->searchData('', '', true, $criteria, $order);
    
    $tempreport = ''; 

    for( $i=0;$i<count($rs);$i++) { 
        
        $return = $obj->formatReportRows(array('data' => $rs[$i]),$arrTemplate); 

        // ===== FOR EXPORT SECTION 
        array_push($dataToExport, $return['data']);  
        // ===== END FOR EXPORT SECTION

        $tempreport .= $return['html'];  
        $arrTemplate[0]['total'] = $obj->arraySum($arrTemplate[0]['total'], $return['subtotal'][0]);

    }

    $obj->generateReport($_POST, $tempreport, $arrTemplate, $dataToExport, $arrFilterInformation);

} else {
    $_POST['trStartDate'] = date('d / m / Y');
    $_POST['trEndDate'] = date('d / m / Y');
}

$arrPackaging = $class->convertForCombobox($packaging->searchData($packaging->tableName.'.statuskey',1,true,'','order by name asc'),'pkey','name');
$arrSupplier = $class->convertForCombobox($supplier->searchData($supplier->tableName.'.statuskey',1,true,'','order by name asc'),'pkey','name');
$arrItem = $class->convertForCombobox($item->searchData($item->tableName.'.statuskey',1,true,'','order by name asc'),'pkey','name');
$arrStatus = $class->convertForCombobox($obj->getAllStatus(),'pkey','status');

$arrTwigVar['inputStartDate'] = $class->inputDate('trStartDate', array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputEndDate'] = $class->inputDate('trEndDate', array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputPackagingCode'] = $class->inputText('packagingCode');
$arrTwigVar['inputItemCode'] = $class->inputText('itemCode');
$arrTwigVar['inputSelPackaging'] = $class->inputSelect('selPackaging[]', $arrPackaging, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputSelItem'] = $class->inputSelect('selItem[]', $arrItem, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputSelSupplier'] = $class->inputSelect('selSupplier[]', $arrSupplier, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputSelStatus'] =  $class->inputSelect('selStatus[]', $arrStatus, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox')); 

$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate;

echo $twig->render('reportPackagingCode.html', $arrTwigVar);

?>

