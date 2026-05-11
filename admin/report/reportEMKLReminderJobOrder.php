<?php

include '../../_config.php';
include '../../_include-v2.php';

includeClass(array('EMKLReminderJobOrder.class.php'));
$eMKLReminderJobOrder = createObjAndAddToCol(new EMKLReminderJobOrder());
$warehouse = createObjAndAddToCol(new Warehouse());
$customer = createObjAndAddToCol(new Customer());
$employee = createObjAndAddToCol(new Employee());
$container = createObjAndAddToCol(new Container());
$supplier = createObjAndAddToCol(new Supplier());
$agent = $supplier;
$carrier = $supplier;

include '_global.php';

$obj = $eMKLReminderJobOrder;
$securityObject = 'EMKLReminderJobOrder'; // the value of security object is manually inserted to handle 
// some modules that have different security object from that of their class


if (!$security->isAdminLogin($securityObject, 10, true));
$_POST['selStatus[]'] = array(1,2,3);

if (!isset($_POST['selDateType']) || empty($_POST['selDateType']))
    $_POST['selDateType'] = 1;

$arrDateType = array(
    '1' => $obj->lang['transactionDate'],
    '2' => 'ETD',
    '3' => 'ETA'
);


//ikut dari form
$arrServiceType = array();
$arrServiceType[0] = '-------';
$arrServiceType[1] = 'CY-CY';
$arrServiceType[2] = 'CY-DOOR';
$arrServiceType[3] = 'CFS-CFS';
$arrServiceType[4] = 'CFS-CY';

$arrHBLType = array();
$arrHBLType[0] = '-------';
$arrHBLType[1] = 'ORI';
$arrHBLType[2] = 'TLX';

$arrMBLType = array();
$arrMBLType[0] = '-------';
$arrMBLType[1] = 'ORI';
$arrMBLType[2] = 'WB';
$arrMBLType[3] = 'EXP ';


$arrFilterInformation = array();

// ===== FOR EXPORT SECTION
$dataToExport = array();

/* data structure */
$arrTemplate = array();

$arrDataStructure = array();
$arrDataStructure['rowNumber'] = array('title' => '#', 'align' => 'right', 'width' => "40px", 'autoNumber' => true, "sortable" => false);
$arrDataStructure['code'] = array('title' => ucwords($obj->lang['code']), 'dbfield' => 'code', 'width' => "150px"); 
$arrDataStructure['warehouse'] = array('title' => ucwords($obj->lang['warehouse']), 'dbfield' => 'warehousename', 'width' => "150px"); 
$arrDataStructure['trDate'] = array('title' => ucwords($obj->lang['date']), 'dbfield' => 'trdate',  'width' => "100px", 'format' => 'date'); 
$arrDataStructure['shipper'] = array('title' => ucwords($obj->lang['exportir'] . ' / ' . $obj->lang['shipper']), 'dbfield' => 'customername', 'width' => "250px"); 
$arrDataStructure['consignee'] = array('title' => ucwords($obj->lang['consignee']), 'dbfield' => 'consigneename', 'width' => "150px"); // done knepa gak keluar da
$arrDataStructure['typeOfJob'] = array('title' => ucwords($obj->lang['typeOfJob']), 'dbfield' => 'jobtypeunion', 'width' => "150px"); 
$arrDataStructure['carrier'] = array('title' => ucwords($obj->lang['carrier']), 'dbfield' => 'carriername', 'width' => "200px");  
$arrDataStructure['agent'] = array('title' => ucwords($obj->lang['agent']), 'dbfield' => 'agentname', 'width' => "200px"); 
$arrDataStructure['vessel'] = array('title' => ucwords($obj->lang['vessel']), 'dbfield' => 'vesselname', 'width' => "100px"); 
$arrDataStructure['vesselnumber'] = array('title' => ucwords($obj->lang['voyage']), 'dbfield' => 'vesselnumber', 'width' => "100px"); 
$arrDataStructure['etdPol'] = array('title' => ucwords($obj->lang['etd']), 'dbfield' => 'etdpol', 'width' => "100px", 'format' => 'date'); 
$arrDataStructure['etapod'] = array('title' => ucwords($obj->lang['eta']), 'dbfield' => 'etapod', 'width' => "100px", 'format' => 'date'); 
$arrDataStructure['pol'] = array('title' => 'POL', 'dbfield' => 'polname', 'width' => "100px"); 
$arrDataStructure['pod'] = array('title' => 'POD', 'dbfield' => 'podname', 'width' => "100px"); 
$arrDataStructure['20'] = array('title'=>'20\'','dbfield' => 'volume20', 'align'=>'right', 'width'=>"60px", 'format'=>'number', 'calculateTotal' => true, "sortable" => false);
$arrDataStructure['40'] = array('title'=>'40\'','dbfield' => 'volume40', 'align'=>'right',  'width'=>"60px" ,'format'=>'number','calculateTotal' => true, "sortable" => false);
$arrDataStructure['45'] = array('title'=>'45\'','dbfield' => 'volume45', 'align'=>'right',  'width'=>"60px" ,'format'=>'number','calculateTotal' => true, "sortable" => false);
$arrDataStructure['cbm'] = array('title'=>'CBM','dbfield' => 'volume', 'width'=>"100px", 'align' =>'right', 'format' => 'decimal','calculateTotal' => true );
$arrDataStructure['description'] = array('title' => ucwords($obj->lang['goodsDescription']), 'dbfield' => 'itemdescription', 'width' => "250px"); 
$arrDataStructure['por'] = array('title' => ucwords($obj->lang['placeOfReciept']), 'dbfield' => 'porname', 'width' => "100px"); 
$arrDataStructure['poiName'] = array('title' => ucwords($obj->lang['placeOfIssued']), 'dbfield' => 'poiname', 'width' => "150px"); 
$arrDataStructure['mblType'] = array('title' => ucwords($obj->lang['mblType']), 'dbfield' => 'mbltypename', 'width' => "80px");
$arrDataStructure['mblDate'] = array('title' => ucwords($obj->lang['mblDate']), 'dbfield' => 'mbldate', 'width' => "100px", 'format' => 'date'); 
$arrDataStructure['mblNumber'] = array('title' => ucwords($obj->lang['mblNumber']), 'dbfield' => 'mblnumber', 'width' => "150px"); 
$arrDataStructure['hblType'] = array('title' => ucwords($obj->lang['hblType']), 'dbfield' => 'hbltypename', 'width' => "80px" ); 
$arrDataStructure['hbldate'] = array('title' => ucwords($obj->lang['hblDate']), 'dbfield' => 'hbldate', 'width' => "100px", 'format' => 'date'); 
$arrDataStructure['hblNumber'] = array('title' => ucwords($obj->lang['hblNumber']), 'dbfield' => 'hblnumber', 'width' => "100px"); 
$arrDataStructure['optionType'] = array('title' => ucwords($obj->lang['optionType']), 'dbfield' => 'telextypename', 'width' => "150px"); 
$arrDataStructure['optionDate'] = array('title' => ucwords($obj->lang['optionDate']), 'dbfield' => 'telexdate', 'width' => "100px", 'format' => 'date', 'returnOnEmpty' => array('returnOnEmpty' => true, 'value' => '')); 
$arrDataStructure['paymentCarrierDate'] = array('title' => ucwords($obj->lang['proposeDate']), 'dbfield' => 'paymentcarrierdate', 'width' => "100px", 'format' => 'date','returnOnEmpty' => array('returnOnEmpty' => true, 'value' => '')); 
$arrDataStructure['voucherDate'] = array('title' => ucwords($obj->lang['voucherDate']), 'dbfield' => 'voucherdate', 'width' => "100px", 'format' => 'date', 'returnOnEmpty' => array('returnOnEmpty' => true, 'value' => ''));
$arrDataStructure['transferDate'] = array('title' => ucwords($obj->lang['transferFtp']), 'dbfield' => 'transferdate', 'width' => "100px", 'format' => 'date', 'returnOnEmpty' => array('returnOnEmpty' => true, 'value' => '')); 
$arrDataStructure['paDate'] = array('title' => ucwords($obj->lang['PADate']), 'dbfield' => 'docdate', 'width' => "100px", 'format' => 'date', 'returnOnEmpty' => array('returnOnEmpty' => true, 'value' => '')); 
$arrDataStructure['profitLossDate'] = array('title' => ucwords($obj->lang['PLDate']), 'dbfield' => 'profitlossdate', 'width' => "100px", 'format' => 'date', 'returnOnEmpty' => array('returnOnEmpty' => true, 'value' => '')); 
$arrDataStructure['amsdate'] = array('title' => 'AMS', 'dbfield' => 'amsdate', 'width' => "100px", 'format' => 'date','returnOnEmpty' => array('returnOnEmpty' => true, 'value' => '')); 
$arrDataStructure['isfDate'] = array('title' => 'ISF', 'dbfield' => 'isfdate', 'width' => "100px", 'format' => 'date','returnOnEmpty' => array('returnOnEmpty' => true, 'value' => '')); 
$arrDataStructure['emanifestDate'] = array('title' => ucwords($obj->lang['eManifestDate']), 'dbfield' => 'emanifestdate', 'width' => "100px", 'format' => 'date','returnOnEmpty' => array('returnOnEmpty' => true, 'value' => ''));
$arrDataStructure['trizDate'] = array('title' => '3Z', 'dbfield' => 'trizdate', 'width' => "100px", 'format' => 'date','returnOnEmpty' => array('returnOnEmpty' => true, 'value' => '')); 
$arrDataStructure['ehblDate'] = array('title' => 'e-HBL', 'dbfield' => 'ehbldate', 'width' => "100px", 'format' => 'date','returnOnEmpty' => array('returnOnEmpty' => true, 'value' => '')); 
$arrDataStructure['serviceType'] = array('title' => ucwords($obj->lang['service']), 'dbfield' => 'servicetypename', 'width' => "150px"); 
$arrDataStructure['containerno'] = array('title' => ucwords($obj->lang['containerNumber']), 'dbfield' => 'containerno', 'width' => "150px","sortable" => false); 
$arrDataStructure['note'] = array('title' => ucwords($obj->lang['note']), 'dbfield' => 'trdesc', 'width' => "250px");
$arrDataStructure['statusname'] = array('title' => ucwords($obj->lang['status']), 'dbfield' => 'statusname', 'width' => "70px"); 

$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $obj->lang['emklReminderJobOrderReport'];
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();
array_push($arrTemplate, $arrHeaderTemplate);

if (isset($_POST) && !empty($_POST['hidAction'])) {

    $criteria = '';


    if (isset($_POST) && !empty($_POST['code'])) {
        $criteria .= ' AND ' . $obj->tableName . '.code=' . $class->oDbCon->paramString($_POST['code']);
        array_push($arrFilterInformation, array("label" => $class->lang['code'], 'filter' =>  $_POST['code']));
    }

    if (isset($_POST) && !empty($_POST['selStatus'])) {

        $key = implode(",", $class->oDbCon->paramString($_POST['selStatus']));

        $criteria .= ' AND ' . $obj->tableName . '.statuskey in(' . $key . ')';

        $rsCriteria =  $obj->getStatusById($key);
        $arrTempStatus = array();
        for ($k = 0; $k < count($rsCriteria); $k++)
            array_push($arrTempStatus, $rsCriteria[$k]['status']);

        $statusName = implode(", ", $arrTempStatus);
        array_push($arrFilterInformation, array("label" => 'Status', 'filter' => $statusName));
    }

    if (isset($_POST) && !empty($_POST['trStartDate'])) {
        switch ($_POST['selDateType']) {
            case '1':
                $fieldName = $obj->tableName . '.trdate';
                break;
            case '2':
                $fieldName = $obj->tableName . '.etdpol';
                break;
            case '3':
                $fieldName = $obj->tableName . '.etapod';
                break;
            default:
                $fieldName = $obj->tableName . '.trdate';
                break;
        }

        $criteria .= ' and ' . $fieldName . ' between ' . $class->oDbCon->paramDate($_POST['trStartDate'], ' / ') . ' AND ' . $class->oDbCon->paramDate($_POST['trEndDate'], ' / ');
        array_push($arrFilterInformation, array("label" => $arrDateType[$_POST['selDateType']], 'filter' => $_POST['trStartDate'] . ' - ' . $_POST['trEndDate']));
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
    if (isset($_POST) && !empty($_POST['selAgent'])) {

        $key = implode(",", $class->oDbCon->paramString($_POST['selAgent']));

        $criteria .= ' AND ' . $obj->tableName . '.agentkey in(' . $key . ')';

        $rsCriteria = $agent->searchData('', '', true, ' and ' . $agent->tableName . '.pkey in (' . $key . ')');

        $arrTempStatus = array();
        for ($k = 0; $k < count($rsCriteria); $k++)
            array_push($arrTempStatus, $rsCriteria[$k]['name']);

        $statusName = implode(", ", $arrTempStatus);
        array_push($arrFilterInformation, array("label" => $obj->lang['agent'], 'filter' => $statusName));
    }
    if (isset($_POST) && !empty($_POST['selCarrier'])) {
        //carrierkey
        $key = implode(",", $class->oDbCon->paramString($_POST['selCarrier']));

        $criteria .= ' AND ' . $obj->tableName . '.carrierkey in(' . $key . ')';

        $rsCriteria = $carrier->searchData('', '', true, ' and ' . $carrier->tableName . '.pkey in (' . $key . ')');

        $arrTempStatus = array();
        for ($k = 0; $k < count($rsCriteria); $k++)
            array_push($arrTempStatus, $rsCriteria[$k]['name']);

        $statusName = implode(", ", $arrTempStatus);
        array_push($arrFilterInformation, array("label" => $obj->lang['carrier'], 'filter' => $statusName));
    }

    if (isset($_POST) && !empty($_POST['selCustomer'])) {

        $key = implode(",", $class->oDbCon->paramString($_POST['selCustomer']));
        $criteria .= ' AND ' . $obj->tableName . '.customerkey in(' . $key . ')';

        $rsCriteria = $customer->searchDataRow(array($customer->tableName . '.name'), ' and ' . $customer->tableName . '.pkey in (' . $key . ')');;

        $arrTempStatus = array();
        for ($k = 0; $k < count($rsCriteria); $k++)
            array_push($arrTempStatus, $rsCriteria[$k]['name']);

        $customerName = implode(", ", $arrTempStatus);
        array_push($arrFilterInformation, array("label" => $obj->lang['exportir'] . ' / ' . $obj->lang['shipper'], 'filter' => $customerName));
    }

    if (isset($_POST) && !empty($_POST['selContainer'])) {

        $key = $_POST['selContainer'];
        $criteria .= ' AND ' . $obj->tableName . '.containertypekey in (' . $class->oDbCon->paramString($key, ',') . ')';

        $rsCriteria = $container->getContainerType($key);

        $arrTempStatus = array();
        for ($k = 0; $k < count($rsCriteria); $k++)
            array_push($arrTempStatus, $rsCriteria[$k]['name']);

        $statusName = implode(", ", $arrTempStatus);
        array_push($arrFilterInformation, array("label" => $obj->lang['containerType'], 'filter' => $statusName));
    }

    if (isset($_POST) && !empty($_POST['mblNumber'])) {
        $criteria .= ' AND ' . $obj->tableName . '.mblnumber LIKE (' . $class->oDbCon->paramString('%' . $_POST['mblNumber'] . '%') . ')';
        array_push($arrFilterInformation, array("label" => 'MBL', 'filter' =>  $_POST['mblNumber']));
    }

    if (isset($_POST) && !empty($_POST['hblNumber'])) {
        $criteria .= ' AND ' . $obj->tableName . '.hblnumber LIKE (' . $class->oDbCon->paramString('%' . $_POST['hblNumber'] . '%') . ')';
        array_push($arrFilterInformation, array("label" => 'HBL', 'filter' =>  $_POST['hblNumber']));
    }

    $orderBy = (!empty($_POST['hidOrderBy'])) ? $obj->oDbCon->paramOrder($_POST['hidOrderBy']) : 'pkey'; // order by harus dr kolom yg terdaftar saja
    $orderType = (isset($_POST['hidOrderType']) && !empty($_POST['hidOrderType']) && $_POST['hidOrderType'] == 1) ? 'desc' : 'asc';

    $order = 'order by ' . $orderBy . ' ' . $orderType;
    $rs = $obj->searchData('', '', true, $criteria, $order);
    
    
    $arrKeys = array_column($rs,'pkey');
    
    $tempreport = '';

    // count container for each row
    $rsContainerQty = $obj->getDetailVolume($arrKeys);
  

    $arrContainerCol = array();
    $totalContainerRows = count($rsContainerQty); 
    for( $i=0;$i<$totalContainerRows;$i++) {  
        $sokey = $rsContainerQty[$i]['refkey'];
        $vol = $rsContainerQty[$i]['volume'];
        $qty = $rsContainerQty[$i]['qty'];
        if(!isset($arrContainerCol[$sokey])) $arrContainerCol[$sokey] = array();
        
        $arrContainerCol[$sokey][strval(intval($vol))] += $qty; 
    }
    
    // utk LCL
    $containerLCLKey = array_unique(array_column($rs,'itemkey'));
    $rsContainerCol = $container->searchDataRow(array($container->tableName.'.pkey', $container->tableName.'.volume'),
                                             ' and '.$container->tableName.'.pkey in ('.$class->oDbCon->paramString($containerLCLKey,',').')'
                                            );
    $rsContainerCol = array_column($rsContainerCol,null,'pkey');

    $rsContainerNumber = $obj->getDetailContainer($arrKeys);
    $rsContainerInformation = $obj->reindexDetailCollections($rsContainerNumber,'refkey');      
    
    for ($i = 0; $i < count($rs); $i++) {

        $containertype = $rs[$i]['loadcontainertypekey'];
        
        $arrContainerQty = $rsContainerQty[$rs[$i]['pkey']];
        $arrContainerNumber = array();
        
        if(isset($rsContainerInformation[$rs[$i]['pkey']])){
            
            foreach($rsContainerInformation[$rs[$i]['pkey']] as $containerRow){ 
                
                if(!empty($containerRow['containerno']))
                    array_push($arrContainerNumber, $containerRow['containerno']); 
            }

        }
        
        $rs[$i]['containerno'] = implode('<br>',$arrContainerNumber);   
        // kalo lcl dan bukan master
        if($containertype == 2){ 
            if($rs[$i]['ismaster']){
                $volLCL = strval(intval($rsContainerCol[$rs[$i]['itemkey']]['volume']));
                $rs[$i]['volume'.$volLCL] = 1;     
            }else{ 
                $rs[$i]['volume20'] =  0;
                $rs[$i]['volume40'] =  0;
                $rs[$i]['volume45'] =  0;
            }
        }else{ 
            $rs[$i]['volume20'] =  $arrContainerCol[$sokey]['20'];
            $rs[$i]['volume40'] =  $arrContainerCol[$sokey]['40'];
            $rs[$i]['volume45'] =  $arrContainerCol[$sokey]['45'];
        }        
        
        $rs[$i]['servicetypename'] = $arrServiceType[$rs[$i]['servicetypekey']];
        $rs[$i]['telextypename'] = $arrHBLType[$rs[$i]['telextype']];
        $rs[$i]['mbltypename'] = $arrMBLType[$rs[$i]['mbltypekey']];
        $rs[$i]['hbltypename'] = $arrHBLType[$rs[$i]['hbltypekey']];

        
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


$arrCarrier = $class->convertForCombobox($carrier->searchData($carrier->tableName . '.statuskey', 1, true, '', 'order by name asc'), 'pkey', 'name');
$arrAgent = $class->convertForCombobox($agent->searchData($agent->tableName . '.statuskey', 1, true, '', 'order by name asc'), 'pkey', 'name');
$arrWarehouse = $class->convertForCombobox($warehouse->searchData($warehouse->tableName . '.statuskey', 1, true, '', 'order by name asc'), 'pkey', 'name');
$arrStatus = $class->convertForCombobox($obj->getAllStatus(), 'pkey', 'status');
$arrContainer = $class->convertForCombobox($container->getContainerType(), 'pkey', 'name');
$arrCustomer = $class->convertForCombobox($customer->searchDataRow(array($customer->tableName . '.pkey', $customer->tableName . '.name',), ' and ' . $customer->tableName . '.statuskey=2' . ' ORDER BY ' . $customer->tableName . '.name ASC'), 'pkey', 'name');

$arrTwigVar['inputSalesCode'] =  $class->inputText('code');
$arrTwigVar['inputSelCarrier'] =  $class->inputSelect('selCarrier[]', $arrCarrier, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputSelAgent'] =  $class->inputSelect('selAgent[]', $arrAgent, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputSelWarehouse'] =  $class->inputSelect('selWarehouse[]', $arrWarehouse, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputSelCustomer'] =  $class->inputSelect('selCustomer[]', $arrCustomer, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));

$arrTwigVar['inputSelDateType'] =  $class->inputSelect('selDateType', $arrDateType);
$arrTwigVar['inputSelStatus'] =  $class->inputSelect('selStatus[]', $arrStatus, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputStartDate'] = $class->inputDate('trStartDate', array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputEndDate'] = $class->inputDate('trEndDate', array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputBookingNumber'] =  $class->inputText('bookingNumber');
$arrTwigVar['inputHblNumber'] =  $class->inputText('hblNumber');
$arrTwigVar['inputMblNumber'] =  $class->inputText('mblNumber');
$arrTwigVar['inputSelContainer'] =  $class->inputSelect('selContainer[]', $arrContainer, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputIsShowDetail'] =  $class->inputCheckBox('isShowDetail');

$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate;

echo $twig->render('reportEMKLReminderJobOrder.html', $arrTwigVar);
