<?php
include '../../_config.php';
include '../../_include-v2.php';


includeClass(array('TruckingServiceWorkOrder.class.php'));

$truckingServiceWorkOrder = createObjAndAddToCol(new TruckingServiceWorkOrder());
$warehouse = createObjAndAddToCol(new Warehouse());
$supplier = createObjAndAddToCol(new Supplier());

include '_global.php';

$obj = $truckingServiceWorkOrder;
$securityObject = 'ReportTruckingPurchaseUnInvoiced';
// the value of security object is manually inserted to handle 
// some modules that have different security object from that of their class

if (!$security->isAdminLogin($securityObject, 10, true));

$_POST['selStatus[]'] = array(1, 2);

if (!isset($_POST['trStartDate']) || empty($_POST['trStartDate'])) {
   $_POST['trStartDate'] = date('d / m / Y');
   $_POST['trEndDate'] = date('d / m / Y');
}


$orderCriteria = array();
$orderCriteria = array();
$orderCriteria['orderBy'] = (isset($_POST) && !empty($_POST['hidOrderBy'])) ? $obj->oDbCon->paramOrder($_POST['hidOrderBy']) : 'trdate'; //$obj->tableName.'.
$orderCriteria['orderType'] = (isset($_POST) && !empty($_POST['hidOrderType'])) ? $_POST['hidOrderType'] : -1;

$arrFilterInformation = array();
// ===== FOR EXPORT SECTION
$dataToExport = array();

/* data structure */
$arrTemplate = array();

$arrDataStructure = array();
$arrDataStructure['rowNumber'] = array('title' => '#', 'align' => 'right', 'width' => "40px", 'autoNumber' => true, "sortable" => false);
$arrDataStructure['code'] = array('title' => ucwords($obj->lang['code']), 'width' => "120px", 'dbfield' => 'code');
$arrDataStructure['date'] = array('title' => ucwords($obj->lang['date']), 'dbfield' => 'trdate', 'width' => "120px", 'format' => 'date');
$arrDataStructure['jobOrderCode'] = array('title' => ucwords($obj->lang['jobOrderCode']), 'dbfield' => 'socode', 'width' => "120px");
$arrDataStructure['warehouse'] = array('title' => ucwords($obj->lang['warehouse']), 'dbfield' => 'warehousename', 'width' => "120px");
$arrDataStructure['supplier'] = array('title' => ucwords($obj->lang['supplier']), 'dbfield' => 'suppliername', 'width' => "180px");
$arrDataStructure['service'] = array('title' => ucwords($obj->lang['service']), 'dbfield' => 'itemname', 'width' => "150px");
$arrDataStructure['total'] = array('title' => ucwords($obj->lang['total']), 'dbfield' => 'total', 'width' => "120px", "format" => 'number', "align" => "right");


$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $obj->lang['truckingPurchaseUnInvoicedReport'];
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();

array_push($arrTemplate, $arrHeaderTemplate);


$arrWarehouse = $class->convertForCombobox($warehouse->searchData($warehouse->tableName . '.statuskey', 1, true, '', 'order by name asc'), 'pkey', 'name');
$arrStatus = $class->convertForCombobox($obj->getAllStatus(), 'pkey', 'status');
$arrSupplier = $class->convertForCombobox($supplier->searchData($supplier->tableName . '.statuskey', 1, true, '', 'order by name asc'), 'pkey', 'name');

$arrTwigVar['inputWorkOrderCode'] = $class->inputText('workOrderCode');
$arrTwigVar['inputStartDate'] = $class->inputDate('trStartDate', array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputEndDate'] = $class->inputDate('trEndDate', array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputSelWarehouse'] = $class->inputSelect('selWarehouse[]', $arrWarehouse, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputSelStatus'] = $class->inputSelect('selStatus[]', $arrStatus, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputSelSupplier'] = $class->inputSelect('selSupplier[]', $arrSupplier, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['order'] = $orderCriteria;
$arrTwigVar['arrTemplate'] = $arrHeaderTemplate;

if (isset($_POST) && !empty($_POST['hidAction'])) {

   $criteria = '';
   if (isset($_POST) && !empty($_POST['workOrderCode'])) {
      $criteria .= ' AND ' . $obj->tableName . '.code LIKE (' . $class->oDbCon->paramString('%' . $_POST['workOrderCode'] . '%') . ')';
      array_push($arrFilterInformation, array("label" => 'Kode', 'filter' => $_POST['workOrderCode']));
   }

   if (isset($_POST) && !empty($_POST['trStartDate'])) {
      $criteria .= ' and ' . $obj->tableName . '.trdate between ' . $class->oDbCon->paramDate($_POST['trStartDate'], ' / ') . ' AND ' . $class->oDbCon->paramDate($_POST['trEndDate'], ' / ');
      array_push($arrFilterInformation, array("label" => 'Tanggal', 'filter' => $_POST['trStartDate'] . ' - ' . $_POST['trEndDate']));
   }

   if (isset($_POST) && !empty($_POST['selWarehouse'])) {

      $key = implode(",", $class->oDbCon->paramString($_POST['selWarehouse']));

      $criteria .= ' AND ' . $obj->tableName . '.warehousekey in(' . $key . ')';

      $rsCriteria = $warehouse->searchData('', '', true, ' and ' . $warehouse->tableName . '.pkey in (' . $key . ')');

      $arrTempStatus = array();
      for ($k = 0; $k < count($rsCriteria); $k++)
         array_push($arrTempStatus, $rsCriteria[$k]['name']);

      $statusName = implode(", ", $arrTempStatus);
      array_push($arrFilterInformation, array("label" => $obj->lang['warehouse'], 'filter' => $statusName));

   }

   if (isset($_POST) && !empty($_POST['selSupplier'])) {
      $criteria .= ' AND ' . $obj->tableName . '.supplierkey in (' . $class->oDbCon->paramString($_POST['selSupplier'], ',') . ')';
      $rsSupplier = $supplier->searchDataRow(array($supplier->tableName . '.name'), ' and ' . $supplier->tableName . '.pkey in (' . $class->oDbCon->paramString($_POST['selSupplier'], ',') . ')');
      array_push($arrFilterInformation, array("label" => 'Pelanggan', 'filter' => array_column($rsSupplier, 'name')));
   }

   if (isset($_POST) && !empty($_POST['selStatus'])) {

      $key = implode(",", $class->oDbCon->paramString($_POST['selStatus']));

      $criteria .= ' AND ' . $obj->tableName . '.statuskey in(' . $key . ')';

      $rsCriteria = $obj->getStatusById($key);

      $arrTempStatus = array();
      for ($k = 0; $k < count($rsCriteria); $k++)
         array_push($arrTempStatus, $rsCriteria[$k]['status']);

      $statusName = implode(", ", $arrTempStatus);
      array_push($arrFilterInformation, array("label" => 'Status', 'filter' => $statusName));

   }

   $order = ' order by ' . $orderCriteria['orderBy'] . ' ' . (($orderCriteria['orderType'] == 1) ? 'desc' : 'asc');

   $rs = $obj->getDataForUnInvoicedReport($criteria, $order);

   $tempreport = '';

   if (empty($rs))
      $tempreport .= '<tr class="report-row rewrite-row"><td colspan="' . count($arrHeaderTemplate['dataStructure']) . '"></td></tr>';


   for ($i = 0; $i < count($rs); $i++) {

      $return = $obj->formatReportRows(array('data' => $rs[$i]), $arrTemplate);

      // ===== FOR EXPORT SECTION 
      array_push($dataToExport, $return['data']);
      // ===== END FOR EXPORT SECTION

      $tempreport .= $return['html'];
   }

   $obj->generateReport($_POST, $tempreport, $arrTemplate, $dataToExport, $arrFilterInformation);


} else {
   $_POST['trStartDate'] = date('d / m / Y');
   $_POST['trEndDate'] = date('d / m / Y');
}

echo $twig->render('reportTruckingPurchaseUnInvoiced.html', $arrTwigVar);

?>