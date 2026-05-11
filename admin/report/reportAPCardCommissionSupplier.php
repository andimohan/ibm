<?php	
include '../../_config.php'; 
include '../../_include-v2.php';

includeClass(array('APPayment.class.php','APCommissionPayment.class.php'));
$apPayment = createObjAndAddToCol( new APCommissionPayment()); 
$ap = createObjAndAddToCol( new APCommission()); 
$supplier = createObjAndAddToCol( new Supplier()); 
$warehouse = createObjAndAddToCol( new Warehouse()); 
$currency = createObjAndAddToCol( new Currency());

include '_global.php';  

$obj= $ap; 
$securityObject = 'reportAPCommission'; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true));


if (!isset($_POST['isShowDetail']))
    $_POST['isShowDetail'] = 0;

$arrFilterInformation = array();
$detailCriteria = '';

// $_POST['selStatus[]'] = array(1,2,3);

// ====================== must be set before TWIG
if (!isset($_POST['trEndDate']) || empty($_POST['trEndDate'])) {
    $_POST['trEndDate'] = date('d / m / Y');
}

$orderCriteria = array();
$orderCriteria['orderBy'] = (isset($_POST) && !empty($_POST['hidOrderBy'])) ? $obj->oDbCon->paramOrder($_POST['hidOrderBy']) : 'pkey'; //$obj->tableName.'.
$orderCriteria['orderType'] = (isset($_POST) && !empty($_POST['hidOrderType'])) ? $_POST['hidOrderType'] : -1;

// ====================== must be set before TWIG

// ===== FOR EXPORT SECTION
$dataToExport = array();

/* data structure */
$arrTemplate = array();

// kalo grouping diaktifkan, harus ganti gk boleh pake generateAPReport, karena kena limit group_concat
$isGrouping = false; //(isset($_POST['isGrouping']) && $_POST['isGrouping'] == 1) ? true : false;
$isShowDetail = (isset($_POST['isShowDetail']) && $_POST['isShowDetail'] == 1) ? true : false;
$rsCurrency = $currency->searchData($currency->tableName.'.statuskey',1,true);

/* data structure */
$arrTemplate = array();
$arrAPType = array();
$arrAPType['1'] = 'Pembelian Barang';
$arrAPType['2'] = 'Outsource Jasa';
$arrAPType['3'] = 'Komisi Ritase';
$arrAPType['4'] = 'Komisi Penjualan';
$arrAPType['5'] = 'Biaya Maintenance (DN)';
$arrAPType['6'] = 'Biaya Lain (DN)';
$arrAPType['7'] = 'Uang Muka';

define('AP_IMPORT_TYPE', $arrAPType);

$arrDataStructure = array();

if($isGrouping){
    $arrDataStructure['rowNumber'] = array('title'=>'#', 'align'=>'right', 'width'=>"40px", 'autoNumber' => true,"sortable" => false);
    $arrDataStructure['supplier'] = array('title'=>ucwords($obj->lang['supplier']),'dbfield' => 'suppliername', 'width'=>"300px" );
    
    if(count($rsCurrency) == 1){
        $arrDataStructure['ammount'] = array('title'=>ucwords($obj->lang['total']),'dbfield' => 'totalamount', 'width'=>"150px" ,'format'=>'number','calculateTotal' => true);       
        $arrDataStructure['outstanding'] = array('title'=>ucwords($obj->lang['outstanding']),'dbfield' => 'totaloutstanding', 'width'=>"150px" ,'format'=>'number','calculateTotal' => true);       
    }else{ 
        foreach($rsCurrency as $currRow){
            $arrDataStructure['ammount'.$currRow['pkey']] = array('title'=>ucwords($obj->lang['amount']). ' ' .$currRow['name'],'dbfield' => 'totalamount'.$currRow['pkey'],"sortable" => false, 'width'=>"150px" ,'format'=>'number','calculateTotal' => true);
            $arrDataStructure['outstanding'.$currRow['pkey']] = array('title'=>ucwords($obj->lang['outstanding']). ' '.$currRow['name'],'dbfield' => 'totaloutstanding'.$currRow['pkey'],"sortable" => false, 'width'=>"150px" ,'format'=>'number','calculateTotal' => true);
        }
    }

    $arrDataStructure['dummy'] = array('title'=>'','dbfield' => '', 'width'=>"900px",'sortable' => false);       
}else{
    switch($EXPORT_TYPE){

        case 2 :
            $arrDataStructure['code'] = array('title'=>ucwords($obj->lang['code']),  'width'=>"120px", 'dbfield' => 'code');
            $arrDataStructure['apType'] = array('title'=>ucwords($obj->lang['type']),  'width'=>"250px", 'dbfield' => 'aptypename');
            $arrDataStructure['supplier'] = array('title'=>ucwords($obj->lang['supplier']),'dbfield' => 'suppliername', 'width'=>"170px" );
            $arrDataStructure['date'] = array('title'=>ucwords($obj->lang['date']),'dbfield' => 'trdate', 'width'=>"120px",'format'=>'date');
            $arrDataStructure['amount'] = array('title'=>ucwords($obj->lang['amount']),'dbfield' => 'amount', 'width'=>"130px" ,'format'=>'number');

            break;

        default :

        $arrDataStructure['rowNumber'] = array('title' => '#', 'align' => 'right', 'width' => "40px", 'autoNumber' => true, "sortable" => false);
        $arrDataStructure['code'] = array('title' => ucwords($obj->lang['code']), 'width' => "120px", 'dbfield' => 'code');
        $arrDataStructure['apType'] = array('title' => ucwords($obj->lang['transactionType']), 'width' => "150px", 'dbfield' => 'aptypename');

        if (PLAN_TYPE['categorykey'] == 2) {
            $arrDataStructure['refcode'] = array('title' => ucwords($obj->lang['refCode']) . ' 1', 'width' => "130px", 'dbfield' => 'refcode', );
            $arrDataStructure['refcode2'] = array('title' => ucwords($obj->lang['refCode']) . ' 2', 'width' => "130px", 'dbfield' => 'refcode2');
            $arrDataStructure['refinvoicecode'] = array('title' => ucwords($obj->lang['invoiceReference']), 'width' => "120px", 'dbfield' => 'refinvoicecode');
        } else {
            $arrDataStructure['refcode'] = array('title' => ucwords($obj->lang['refCode']), 'width' => "100px", 'dbfield' => 'refcode');
        }

        $arrDataStructure['date'] = array('title' => ucwords($obj->lang['date']), 'dbfield' => 'trdate', 'width' => "120px", 'format' => 'date');
        $arrDataStructure['warehouse'] = array('title' => ucwords($obj->lang['warehouse']), 'dbfield' => 'warehousename', 'width' => "100px");
        $arrDataStructure['supplier'] = array('title' => ucwords($obj->lang['supplier']), 'dbfield' => 'suppliername', 'width' => "250px");

        if(count($rsCurrency) == 1){
            $arrDataStructure['ammount'] = array('title'=>ucwords($obj->lang['amount']),'dbfield' => 'amount', 'width'=>"120px" ,'format'=>'number','calculateTotal' => true);
            $arrDataStructure['outstanding'] = array('title'=>ucwords($obj->lang['outstanding']),'dbfield' => 'outstanding', 'width'=>"120px" ,'format'=>'number','calculateTotal' => true); 
        }else{ 
            foreach($rsCurrency as $currRow){
                $arrDataStructure['ammount'.$currRow['pkey']] = array('title'=>ucwords($obj->lang['amount']). ' ' .$currRow['name'],'dbfield' => 'amount'.$currRow['pkey'],"sortable" => false, 'width'=>"120px" ,'format'=>'number','calculateTotal' => true);
                $arrDataStructure['outstanding'.$currRow['pkey']] = array('title'=>ucwords($obj->lang['outstanding']). ' '.$currRow['name'],'dbfield' => 'outstanding'.$currRow['pkey'],"sortable" => false, 'width'=>"120px" ,'format'=>'number','calculateTotal' => true);
            }
        } 

        $arrDataStructure['note'] = array('title'=>ucwords($obj->lang['note']),'dbfield' => 'trdesc','width'=>"300px");

    }
}

$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $obj->lang['APCommissionCardReport'];
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();

array_push($arrTemplate, $arrHeaderTemplate);

if(!$isGrouping && $isShowDetail){
    // detail ...
    $arrDataDetailStructure = array();
    $arrDataDetailStructure['apcode'] = array('title'=>ucwords($obj->lang['paymentCode']),  'dbfield' => 'code', 'width'=>'100px', 'format' => 'string' );
    $arrDataDetailStructure['appaymentdate'] = array('title'=>ucwords($obj->lang['date']),  'dbfield' => 'trdate', 'format' => 'date', 'width'=>'100px'); 
    $arrDataDetailStructure['currency'] = array('title'=>ucwords($obj->lang['curr']),  'dbfield' => 'currencyname', 'width'=>"60px",   'align'=>'center');
    $arrDataDetailStructure['amount'] = array('title'=>ucwords($obj->lang['amount']),  'dbfield' => 'amount', 'width'=>"110px", 'format' => 'number' , 'calculateTotal' => true);

    $arrDetailTemplate = array();
    $arrDetailTemplate['reportWidth'] = "680px";
    $arrDetailTemplate['dataStructure'] = $arrDataDetailStructure;
    $arrDetailTemplate['total'] = array();

    array_push($arrTemplate, $arrDetailTemplate);
}

$arrWarehouse = $class->convertForCombobox($warehouse->searchData($warehouse->tableName . '.statuskey', 1, true, '', 'order by name asc'), 'pkey', 'name');
$arrSupplier = $class->convertForCombobox($supplier->searchData($supplier->tableName . '.statuskey', 1, true, '', 'order by name asc'), 'pkey', 'name');
$arrCurrency = $class->convertForCombobox($currency->searchData($currency->tableName . '.statuskey', 1, true, '', 'order by name asc'), 'pkey', 'name');
$arrStatus = $class->convertForCombobox($obj->getAllStatus(), 'pkey', 'status');
$arrAPType = $class->convertForCombobox($obj->getAPType(), 'pkey', 'name');


$arrTwigVar['inputCode'] = $class->inputText('code');
$arrTwigVar['inputSelWarehouse'] = $class->inputSelect('selWarehouse[]', $arrWarehouse, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputSelSupplier'] = $class->inputSelect('selSupplier[]', $arrSupplier, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputSelCurrency'] = $class->inputSelect('selCurrency[]', $arrCurrency, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputStartDate'] = $class->inputDate('trStartDate', array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputEndDate'] = $class->inputDate('trEndDate', array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputSelTransactionType'] = $class->inputSelect('selTransactionType', $arrAPType, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputIsShowDetail'] = $class->inputCheckBox('isShowDetail');
$arrTwigVar['inputSelStatus'] = $class->inputSelect('selStatus[]', $arrStatus, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputIsGrouping'] =  $class->inputCheckBox('isGrouping'); 

$arrTwigVar['inputTemplateSupplier'] = $class->inputAutoComplete(array(
                                            'element' => array(
                                                'value' => 'selTemplateSupplier',
                                                'key' => 'hidTemplateSupplierKey'
                                            ),
                                            'source' => array(
                                                'url' => '../ajax-template-supplier.php',
                                                'data' => array('action' => 'searchData')
                                            ),
                                            'placeholder' => $obj->lang['searchTemplate'] . '...',
                                            'callbackFunction' => 'updateSupplier(this)'
                                        ));

$arrTwigVar['order'] = $orderCriteria;
$arrTwigVar['arrTemplate'] = $arrHeaderTemplate;

if (isset($_POST) && !empty($_POST['hidAction'])) {

    $criteria = '';
    $criteriaArr = array();

    // untuk pencarian berdasarkan kode
    array_push($criteriaArr, array(
        'postVariable' => 'code',
        'fieldName' => $obj->tableName . '.code',
        'label' => $obj->lang['code']
    ));


    if (isset($_POST) && !empty($_POST['trEndDate'])) {

        if ($isGrouping) {
            $criteria .= ' AND ' . $ap->tableName . '.trdate <= ' . $class->oDbCon->paramDate($_POST['trEndDate'], ' / ', 'Y-m-d 23:59');
        }
        if ($isShowDetail)
            $detailCriteria .= ' AND ' . $apPayment->tableName . '.trdate <= ' . $class->oDbCon->paramDate($_POST['trEndDate'], ' / ', 'Y-m-d 23:59');

        array_push($arrFilterInformation, array("label" => 'Periode', 'filter' => $_POST['trEndDate']));

    }

    array_push($criteriaArr, array(
        'postVariable' => 'selWarehouse',
        'fieldName' => $obj->tableName . '.warehousekey',
        'label' => $obj->lang['warehouse'],
        'useArrayKey' => array('obj' => $warehouse)
    ));

    array_push($criteriaArr, array(
        'postVariable' => 'selSupplier',
        'fieldName' => $obj->tableName . '.supplierkey',
        'label' => $obj->lang['supplier'],
        'useArrayKey' => array('obj' => $supplier)
    ));

    array_push($criteriaArr, array(
        'postVariable' => 'selCurrency',
        'fieldName' => $obj->tableName . '.currencykey',
        'label' => $obj->lang['currency'],
        'useArrayKey' => array('obj' => $currency)
    ));

    if(isset($_POST) && !empty($_POST['selTransactionType'])) {  
        
        $criteria .= ' AND '.$obj->tableName.'.aptype in('.$class->oDbCon->paramString($_POST['selTransactionType'],',').')';  

        $rsCriteria = $obj->getAPTypeName($_POST['selTransactionType']);
    
        $arrTempStatus = array();
        for ($k=0;$k<count($rsCriteria);$k++)
            array_push($arrTempStatus,$rsCriteria[$k]['name']);
            
        $statusName = implode(", ",$arrTempStatus); 
        array_push($arrFilterInformation,array("label" => $obj->lang['transactionType'], 'filter' => $statusName ));
        
    }    


    // array_push($criteriaArr, array(
    //     'postVariable' => 'selStatus[]',
    //     'type' => 'status'
    // ));



    $obj->createReportCriteria($criteria, $arrFilterInformation, $criteriaArr);


    $orderBy = (!empty($_POST['hidOrderBy'])) ? $obj->oDbCon->paramOrder($_POST['hidOrderBy']) : $obj->tableName . '.pkey'; // order by harus dr kolom yg terdaftar saja
    $orderType = (isset($_POST['hidOrderType']) && !empty($_POST['hidOrderType']) && $_POST['hidOrderType'] == 1) ? 'desc' : 'asc';

    $order = 'order by ' . $orderBy . ' ' . $orderType;
    ;
    $rs = (!$isGrouping) ? $obj->searchAPCard($_POST['trEndDate'],$criteria,$order) :  $obj->generateAPReport($criteria,$order);

    if($isShowDetail) $rsPaymentDetail = $apPayment->getDetailPaymentCollections($rs, 'apkey', $detailCriteria);

    $tempreport = '';

    $totalRs = count($rs);
    for ($i = 0; $i < $totalRs; $i++) {


        $arrHeaderStyle = array();

        if ($isGrouping) {

            $apPkey = explode(",", $rs[$i]['pkey']);
            $rsDetail = $obj->searchData('', '', true, ' and ' . $obj->tableName . '.pkey in (' . implode(',', $apPkey) . ') ' . $criteria);
            $arrDetailStyle = array();

            if (count($rsCurrency) >= 1) {
                foreach ($rsCurrency as $currRow) {
                    $rs[$i]['totalamount' . $currRow['pkey']] = 0;
                    $rs[$i]['totaloutstanding' . $currRow['pkey']] = 0;

                    $currencykey = $rs[$i]['currencykey'];
                    $rs[$i]['totalamount' . $currencykey] = $rs[$i]['totalamount'];
                    $rs[$i]['totaloutstanding' . $currencykey] = $rs[$i]['totaloutstanding'];
                }
            }

            for ($j = 0; $j < count($rsDetail); $j++) {
                $rsDetail[$j]['datediff'] = ($rsDetail[$j]['datediff'] > 0) ? $rsDetail[$j]['datediff'] : 0;

                if ($rsDetail[$j]['datediff'] > 0) {

                    foreach ($arrDataDetailStructure as $key => $detailStructure)
                        $arrDetailStyle[$j][$detailStructure['dbfield']]['textColor'] = 'C41E3A';

                } else {
                    $arrDetailStyle[$j]['outstanding']['textColor'] = '0093AF';

                }
            }
            $rs[$i]['_detail_'] = array('arrTemplate' => $arrDetailTemplate, 'data' => $rsDetail, 'style' => $arrDetailStyle);

        } else {


            if (count($rsCurrency) >= 1) {
                foreach ($rsCurrency as $currRow) {
                    $rs[$i]['amount' . $currRow['pkey']] = 0;
                    $rs[$i]['outstanding' . $currRow['pkey']] = 0;

                    $currencykey = $rs[$i]['currencykey'];
                    $rs[$i]['amount' . $currencykey] = $rs[$i]['amount'];
                    $rs[$i]['outstanding' . $currencykey] = $rs[$i]['outstanding'];
                }
            }

            $arrHeaderStyle['outstanding']['textColor'] = '0093AF';

            if (count($rsCurrency) > 1) {
                foreach ($rsCurrency as $currRow)
                    $arrHeaderStyle['outstanding' . $currRow['pkey']]['textColor'] = '0093AF';
            }

            if ($isShowDetail) {

                $rsPayment = (isset($rsPaymentDetail[$rs[$i]['pkey']])) ? $rsPaymentDetail[$rs[$i]['pkey']] : array();

                $rsDetail = array();
                for ($j = 0; $j < count($rsPayment); $j++) {

                    $rsAPPayment = $apPayment->getDataRowById($rsPayment[$j]['refkey']);

                    $arrTemp = array();
                    $arrTemp['code'] = $rsAPPayment[0]['code'];
                    $arrTemp['trdate'] = $rsAPPayment[0]['trdate'];
                    $arrTemp['currencyname'] = $rsPayment[$j]['currencyname'];
                    $arrTemp['amount'] = $rsPayment[$j]['amount'];

                    array_push($rsDetail, $arrTemp);
                }

                // has detail
                $rs[$i]['_detail_'] = array('arrTemplate' => $arrDetailTemplate, 'data' => $rsDetail);
            }

        }
        
        $return = $obj->formatReportRows(array('data' => $rs[$i], 'style' => $arrHeaderStyle), $arrTemplate);

        // ===== FOR EXPORT SECTION 
        array_push($dataToExport, $return['data']);
        // ===== END FOR EXPORT SECTION

        $tempreport .= $return['html'];
        $arrTemplate[0]['total'] = $obj->arraySum($arrTemplate[0]['total'], $return['subtotal'][0]);

    }

    $tableHeader = $twig->render('template-header.html', $arrTwigVar);

    $obj->generateReport($_POST, $tempreport, $arrTemplate, $dataToExport, $arrFilterInformation, $tableHeader);

} 

echo $twig->render('reportAPCardCommissionSupplier.html', $arrTwigVar);


?>