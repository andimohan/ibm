<?php 

include '../../_config.php';  
include '../../_include-v2.php';

includeClass(array('TruckingServiceOrder.class.php'));
$truckingServiceOrder = createObjAndAddToCol(new TruckingServiceOrder());
$truckingServiceWorkOrder = createObjAndAddToCol(new TruckingServiceWorkOrder());
$customer = createObjAndAddToCol(new Customer());
$warehouse = createObjAndAddToCol(new Warehouse());
$consignee = createObjAndAddToCol(new Consignee());

include '_global.php';

$obj = $truckingServiceOrder;
$securityObject = 'reportDaily'; // the value of security object is manually inserted to handle 
// some modules that have different security object from that of their class

if (!$security->isAdminLogin($securityObject, 10, true));

$arrFilterInformation = array();
$detailCriteria = '';
$_POST['selStatus[]'] = array(2,3,4,5,6);

if (!isset($_POST['isShowDetail']))
    $_POST['isShowDetail'] = 1;


// ===== FOR EXPORT SECTION
$dataToExport = array();

/* data structure */
$arrTemplate = array();

$isShowDetail = (isset($_POST['isShowDetail']) && !empty($_POST['isShowDetail'])) ? true : false;

$arrDataStructure = array();
$arrDataStructure['rowNumber'] = array('title' => '#', 'align' => 'right', 'width' => "40px", 'autoNumber' => true, "sortable" => false);
$arrDataStructure['service'] = array('title' => ucwords($obj->lang['service']), 'width' => "200px", 'dbfield' => 'servicename', 'sortable' => false);
$arrDataStructure['route'] = array('title' => ucwords($obj->lang['route']), 'width' => "250px", 'dbfield' => 'route', 'sortable' => false);
$arrDataStructure['customer'] = array('title' => ucwords($obj->lang['customer']), 'width' => "250px", 'dbfield' => 'customername', 'sortable' => false);
$arrDataStructure['consignee'] = array('title' => ucwords($obj->lang['consignee']), 'width' => "250px", 'dbfield' => 'consigneename', 'sortable' => false);

$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $obj->lang['dailyReport'];
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();

array_push($arrTemplate, $arrHeaderTemplate);

if ($isShowDetail) {

    // detail ...
    $arrDataDetailStructure = array();
    $arrDataDetailStructure['rowNumber'] = array('title' => '#', 'width' => "35px", 'dbfield' => 'number', 'format' => 'number', 'align' => 'right');
    $arrDataDetailStructure['code'] = array('title' => ucwords($obj->lang['code']), 'dbfield' => 'serviceordercode', 'width' => "100px");
    $arrDataDetailStructure['policeNumber'] = array('title' => ucwords('Nomor Polisi'), 'dbfield' => 'policenumber', 'width' => "120px");
    $arrDataDetailStructure['containerNumber'] = array('title' => ucwords($obj->lang['container']), 'dbfield' => 'containernumber', 'width' => "150px");
    $arrDataDetailStructure['cost'] = array('title' => ucwords($obj->lang['cost']), 'dbfield' => 'cost', 'width' => "250px");
    // $arrDataDetailStructure['itemName'] = array('title' => ucwords($obj->lang['itemName']), 'dbfield' => 'itemname', 'width' => "150px");
    $arrDataDetailStructure['aju'] = array('title' => ucwords('NO SJ'), 'dbfield' => 'aju', 'width' => "150px");


    $arrDetailTemplate = array();
    $arrDetailTemplate['reportWidth'] = "700px";
    $arrDetailTemplate['dataStructure'] = $arrDataDetailStructure;
    $arrDetailTemplate['total'] = array();

    array_push($arrTemplate, $arrDetailTemplate);

}

if (isset($_POST) && !empty($_POST['hidAction'])) {

    $criteria = '';
    $criteriaArr = array();

    array_push($criteriaArr, array(
        'postVariable' => 'code',
        'fieldName' => $obj->tableName . '.code',
        'label' => $obj->lang['code']
    ));

    array_push($criteriaArr, array(
        'postVariable' => array('trStartDate', 'trEndDate'),
        'fieldName' => $obj->tableName . '.trdate',
        'label' => $obj->lang['date'],
        'type' => 'daterange'
    ));

    array_push($criteriaArr, array(
        'postVariable' => 'selWarehouse',
        'fieldName' => $obj->tableName . '.warehousekey',
        'label' => $obj->lang['warehouse'],
        'useArrayKey' => array('obj' => $warehouse)
    ));

    array_push($criteriaArr, array(
        'postVariable' => 'selCustomer',
        'fieldName' => $obj->tableName . '.customerkey',
        'label' => $obj->lang['customer'],
        'useArrayKey' => array('obj' => $customer)
    ));

    array_push($criteriaArr, array(
        'postVariable' => 'selConsignee',
        'fieldName' => $obj->tableName . '.consigneekey',
        'label' => $obj->lang['consignee'],
        'useArrayKey' => array('obj' => $consignee)
    ));     

    array_push($criteriaArr, array(
        'postVariable' => 'selStatus',
        'type' => 'status'
    ));

    $obj->createReportCriteria($criteria, $arrFilterInformation, $criteriaArr);

    $orderBy = (!empty($_POST['hidOrderBy'])) ? $obj->oDbCon->paramOrder($_POST['hidOrderBy']) : 'pkey'; // order by harus dr kolom yg terdaftar saja
    $orderType = (isset($_POST['hidOrderType']) && !empty($_POST['hidOrderType']) && $_POST['hidOrderType'] == 1) ? 'desc' : 'asc';

    $order = 'order by ' . $orderBy . ' ' . $orderType;
    $rs = $obj->searchData('', '', true, $criteria, $order);

    //$obj->setLog($rs, true);

    $arrPkey = array_column($rs, 'pkey');

    $rsWorkOrder = $truckingServiceWorkOrder->searchData('','',true, 'and ' . $truckingServiceWorkOrder->tableName.'.statuskey in (1,2,3) and '. $truckingServiceWorkOrder->tableName.'.refkey in ('. $obj->oDbCon->paramString($arrPkey,',') .') ');
    $rsWorkOrderCol = $obj->reindexDetailCollections($rsWorkOrder, 'refkey');

    $rsJOSelling = $obj->getSellingCostDetail($arrPkey);
    // $rsJOSellingCol = $obj->reindexDetailCollections($rsJOSelling, 'refkey');

    $rsJODetailCol = $obj->getDetailCollections($rs, 'refkey');
    
    $arrJOSelling = [];
    foreach($rsJOSelling as $sellingRow) {
        $key = $sellingRow['refkey'] . '-' . $sellingRow['costkey'] . '-' . $sellingRow['price'];
        
        if (!isset($arrJOSelling[$key])) {
            $arrJOSelling[$key] = [
                'refkey' => $sellingRow['refkey'],
                'costkey' => $sellingRow['costkey'],
                'itemname' => $sellingRow['itemname'],
                'qty' => $sellingRow['qty'],
                'price' => $sellingRow['price']
            ];
        } else {
            $arrJOSelling[$key]['qty'] += $sellingRow['qty'];
        }
    }
    
    $resultJOSelling = array_values($arrJOSelling);
    $resultJOSellingCol = $obj->reindexDetailCollections($resultJOSelling, 'refkey');
    
    //$obj->setLog($resultJOSellingCol, true);
    // $obj->setLog($rsWorkOrderCol, true);

    $arrDetail = array();
    foreach ($rsWorkOrderCol as $refkey => $workOrders) {
        $sellingItems = $resultJOSellingCol[$refkey] ?? [];

        $workOrders = array_values($workOrders);
        $sellingItems = array_values($sellingItems);

        $maxCount = max(count($workOrders), count($sellingItems));

        for ($i = 0; $i < $maxCount; $i++) {
            $wo = $workOrders[$i] ?? null;
            $sell = $sellingItems[$i] ?? null;
    
            $arrDetail[] = [
                'refkey' => $refkey,
                'containernumber' => $wo['containernumber'] ?? '',
                'container2number' => $wo['container2number'] ?? '',
                'policenumber' => $wo['policenumber'] ?? '',
                'aju' => $wo['aju'] ?? '', 
                'itemname' => $sell['itemname'] ?? '',
                'qty' => $sell['qty'] ?? '',
                'price' => $sell['price'] ?? '',
                'costkey' => $sell['costkey'] ?? '',
            ];
        }

    }
    // $obj->setLog($arrDetail, true);

    $rsDetailsCols = $obj->reindexDetailCollections($arrDetail, 'refkey');

    $temp = 1;  
    $tempreport = '';

    $rsDetailCol = ($isShowDetail) ? $rsDetailsCols : array();

    $totalRs = count($rs);
    for ($i = 0; $i < $totalRs; $i++) {
        
        $rsJODetail = $rsJODetailCol[$rs[$i]['pkey']];
        // $obj->setLog($rs, true);

        $arrParty = array();
        foreach($rsJODetail as $detailRow) {
            $qty = $obj->formatNumber($detailRow['qtyinbaseunit']);
            $item = $detailRow['itemname'];
            $qtyParty = $qty .' X ' . $item;
            array_push($arrParty, $qtyParty);
        }

        $party = implode(', ', $arrParty);

        

        $route = $rs[$i]['routefrom'] . ' - ' . $rs[$i]['routeto'];
        $consigneeName = (!empty($rs[$i]['consigneename'])) ? ' ( '. $rs[$i]['consigneename'] .' )' : '';
        $customerName = ' O/'.$rs[$i]['customername'];

        //$rs[$i]['route_service_customer_consignee'] = $party .' ' . $route . $customerName . $consigneeName;
        $rs[$i]['servicename'] = $party;
        $rs[$i]['route'] = $route;
        $rs[$i]['customername'] = $customerName;
        $rs[$i]['consignee'] = $consigneeName;


        if ($isShowDetail) {

            if (!isset($rsDetailCol[$rs[$i]['pkey']]))
                continue;

            
            $rsDetail = $rsDetailCol[$rs[$i]['pkey']];

            $no = 1;
            for($j=0; $j<count($rsDetail); $j++) {
                
                $rsDetail[$j]['number'] = $no;
                $rsDetail[$j]['serviceordercode'] = $rs[$i]['code'];

                $containerNumber = $rsDetail[$j]['containernumber'];
                $container2Number = (!empty($rsDetail[$j]['container2number'])) ? ', '.$rsDetail[$j]['container2number'] : '';

                $rsDetail[$j]['containernumber'] = $containerNumber . $container2Number;

                $itemName = (!empty($rsDetail[$j]['itemname'])) ? '/'.$rsDetail[$j]['itemname'] : '';
                $qty = $obj->formatNumber($rsDetail[$j]['qty']);
                $price = $obj->formatNumber($rsDetail[$j]['price']);

                $qtyPrice = ((empty($qty) && empty($price)) ? '' : $qty . ' X ' . $price);

                $rsDetail[$j]['cost'] = $qtyPrice . ' ' . $itemName;

                $no++;
            }


            $rs[$i]['_detail_'] = array('arrTemplate' => $arrDetailTemplate, 'data' => $rsDetail);
        }

        $return = $obj->formatReportRows(array('data' => $rs[$i]), $arrTemplate);

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

$arrWarehouse = $class->convertForCombobox($warehouse->searchData($warehouse->tableName . '.statuskey', 1, true, '', 'order by name asc'), 'pkey', 'name');
$arrStatus = $class->convertForCombobox($obj->getAllStatus(), 'pkey', 'status');
$arrCustomer = $class->convertForCombobox($customer->searchData($customer->tableName.'.statuskey',2,true,'','order by name asc'),'pkey','name');
$arrConsignee = $class->convertForCombobox($consignee->searchData($consignee->tableName.'.statuskey',1,true,'','order by name asc'),'pkey','name');

$arrTwigVar['inputCode'] = $class->input('code');
$arrTwigVar['inputStartDate'] = $class->inputDate('trStartDate', array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputEndDate'] = $class->inputDate('trEndDate', array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputSelWarehouse'] = $class->inputSelect('selWarehouse[]', $arrWarehouse, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputSelCustomer'] = $class->inputSelect('selCustomer[]', $arrCustomer, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputSelConsignee'] = $class->inputSelect('selConsignee[]', $arrConsignee, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputSelStatus'] = $class->inputSelect('selStatus[]', $arrStatus, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));

$arrTwigVar['inputShowDetail'] = $class->inputCheckBox('isShowDetail');
$arrTwigVar['arrTemplate'] = $arrHeaderTemplate;
echo $twig->render('reportDaily.html', $arrTwigVar);


?>