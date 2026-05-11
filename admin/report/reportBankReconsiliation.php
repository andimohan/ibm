<?php
include '../../_config.php';
include '../../_include-v2.php';

includeClass('BankReconsiliation.class.php');
$bankReconsiliation  = createObjAndAddToCol(new BankReconsiliation());
$chartOfAccount  = createObjAndAddToCol(new ChartOfAccount());

$obj            = $bankReconsiliation;
$securityObject = 'reportBankReconsiliation';

include '_global.php';

if(!$security->isAdminLogin($securityObject,10,true));
$_POST['selStatus[]'] = array(2, 3);
$isShowDetail         = (isset($_POST['isShowDetail']) && !empty($_POST['isShowDetail'])) ? true : false;
$arrFilterInformation = array();
$detailCriteria       = '';

// ====================== must be set before TWIG
if (!isset($_POST['trStartDate']) || empty($_POST['trStartDate'])) {
   $_POST['trStartDate'] = date('d / m / Y');
   $_POST['trEndDate']   = date('d / m / Y');
}

// $orderCriteria  = array();

// ===== FOR EXPORT SECTION
$dataToExport = array();

/* data structure */
$arrTemplate = array();

$arrDataStructure                  = array();
$arrDataStructure['rowNumber']     = array('title' => '#', 'align' => 'right', 'width' => "40px", 'autoNumber' => true, "sortable" => false);
$arrDataStructure['code']          = array('title' => ucwords($obj->lang['code']), 'width' => "120px", 'dbfield' => 'code');
$arrDataStructure['date']          = array('title' => ucwords($obj->lang['date']), 'dbfield' => 'trdate', 'width' => "120px", 'format' => 'date');
$arrDataStructure['warehouse']        = array('title' => ucwords($obj->lang['warehouse']), 'width' => "120px", 'dbfield' => 'warehousename');
$arrDataStructure['coa'] = array('title' => ucwords($obj->lang['account']), 'width' => "200px", 'dbfield' => 'codename');
$arrDataStructure['period']      = array('title' => ucwords($obj->lang['period']), 'width' => "120px", 'dbfield' => 'startdatepriode', 'align' => 'center', 'format' => 'monthYear');
$arrDataStructure['currency']      = array('title' => ucwords($obj->lang['currency']), 'width' => "80px", 'dbfield' => 'currencyname');
$arrDataStructure['openingBalance']        = array('title' => ucwords($obj->lang['openingBalance']), 'dbfield' => 'beginingbalance', 'format' => 'number', 'width' => "150px", 'calculateTotal' => true);
$arrDataStructure['endingBalance']        = array('title' => ucwords($obj->lang['endingBalance']), 'dbfield' => 'endingbalance', 'format' => 'number', 'width' => "150px", 'calculateTotal' => true);
$arrDataStructure['totalDebit']        = array('title' => ucwords($obj->lang['totalDebit']), 'dbfield' => 'totaldebit', 'format' => 'number', 'width' => "150px", 'calculateTotal' => true);
$arrDataStructure['totalCredit']        = array('title' => ucwords($obj->lang['totalCredit']), 'dbfield' => 'totalcredit', 'format' => 'number', 'width' => "150px", 'calculateTotal' => true);
$arrDataStructure['status']      = array('title' => ucwords($obj->lang['status']), 'width' => "100px", 'dbfield' => 'statusname');

$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $obj->lang['bankReconsiliationReport'];
$arrHeaderTemplate['reportWidth'] = "1100px;";
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();

array_push($arrTemplate, $arrHeaderTemplate);

if ($isShowDetail) {
   $arrDataDetailStructure                      = array();
   $arrDataDetailStructure['voucherCode']              = array('title' => ucwords($obj->lang['voucherCode']), 'dbfield' => 'vouchercode', 'width' => "150px", 'mergeExcelCell' => 2);
   $arrDataDetailStructure['refCode']            = array('title' => ucwords($obj->lang['refCode']), 'dbfield' => 'refcode', 'width' => "100px");
   $arrDataDetailStructure['date'] = array('title' => ucwords($obj->lang['date']), 'dbfield' => 'date', 'width' => "100px", 'format' => 'date');
   $arrDataDetailStructure['trDesc']            = array('title' => ucwords($obj->lang['note']), 'dbfield' => 'trdesc', 'width' => "200px", 'mergeExcelCell' => 4);
   $arrDataDetailStructure['currency']       = array('title' => ucwords($obj->lang['currency']), 'dbfield' => 'currencyname', 'width' => "80px");
   $arrDataDetailStructure['debit']           = array('title' => ucwords($obj->lang['debit']), 'dbfield' => 'debit', 'width' => "120px", 'format' => 'number', 'calculateTotal' => true);
   $arrDataDetailStructure['credit']           = array('title' => ucwords($obj->lang['credit']), 'dbfield' => 'credit', 'width' => "120px", 'format' => 'number', 'calculateTotal' => true);

   $arrDetailTemplate                  = array();
   $arrDetailTemplate['reportWidth']   = "800px";
   $arrDetailTemplate['dataStructure'] = $arrDataDetailStructure;
   $arrDetailTemplate['total']         = array();

   array_push($arrTemplate, $arrDetailTemplate);
}



$arrChartOfAccount = $class->convertForCombobox($chartOfAccount->searchData($chartOfAccount->tableName . '.statuskey', 1, true, ' and '.$chartOfAccount->tableName . '.iscashbank=1', 'order by name asc'), 'pkey', 'coaname');
$arrStatus  = $class->convertForCombobox($obj->getAllStatus(), 'pkey', 'status');

$arrTwigVar['inputSelStatus'] = $class->inputSelect('selStatus[]', $arrStatus, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputStartDate']         = $class->inputDate('trStartDate', array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputEndDate']           = $class->inputDate('trEndDate', array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputIsShowDetail']      = $class->inputCheckBox('isShowDetail');
$arrTwigVar['inputSelChartOfAccount'] = $class->inputSelect('selChartOfAccount[]', $arrChartOfAccount, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputBankReconsiliationCode'] =  $class->inputText('bankReconsiliationCode');
$arrTwigVar['arrTemplate']            = $arrHeaderTemplate;

if (isset($_POST) && !empty($_POST['hidAction'])) {

    $criteria = '';
    $arrFilterInformation = array();
    $criteriaArr = array();

    array_push($criteriaArr, array(
        'postVariable' => 'bankReconsiliationCode',
        'fieldName' => $obj->tableName . '.code',
        'label' => $obj->lang['code']
    ));
       
    array_push($criteriaArr, array(
        'postVariable' => 'selChartOfAccount',
        'fieldName' => $obj->tableName . '.coakey',
        'label' => $obj->lang['chartOfAccount'],
        'useArrayKey' => array('obj' => $chartOfAccount)
    ));
       
    array_push($criteriaArr, array(
        'postVariable' => array('trStartDate', 'trEndDate'),
        'fieldName' => $obj->tableName . '.trdate',
        'label' =>  $obj->lang['date'],
        'type' => 'daterange'
    ));
        
    array_push($criteriaArr, array(
        'postVariable' => 'selStatus',
        'type' => 'status'
    ));
    
    $obj->createReportCriteria($criteria, $arrFilterInformation, $criteriaArr);
 
   $orderBy   = (!empty($_POST['hidOrderBy'])) ? $obj->oDbCon->paramOrder($_POST['hidOrderBy']) : 'pkey'; // order by harus dr kolom yg terdaftar saja
   $orderType = (isset($_POST['hidOrderType']) && !empty($_POST['hidOrderType']) && $_POST['hidOrderType'] == 1) ? 'desc' : 'asc';

   $order = 'order by ' . $orderBy . ' ' . $orderType;

   $rs = $obj->searchData('', '', true, $criteria, $order);
   $rsDetailCol = $obj->getDetailCollections($rs, 'refkey');

   $tempreport  = '';
   // $rsDetailCol = ($isShowDetail) ? $obj->getDetailCollections($rs, 'refkey', $detailCriteria) : array();

   for ($i = 0; $i < count($rs); $i++) {

      if ($isShowDetail) {
         if (!isset($rsDetailCol[$rs[$i]['pkey']]))
            continue;

         $rsDetail = $rsDetailCol[$rs[$i]['pkey']];

         $rs[$i]['_detail_'] = array('arrTemplate' => $arrDetailTemplate, 'data' => $rsDetail);

      }

      $return = $obj->formatReportRows(array('data' => $rs[$i]), $arrTemplate);


      // ===== FOR EXPORT SECTION 
      array_push($dataToExport, $return['data']);
      // ===== END FOR EXPORT SECTION

      $tempreport .= $return['html'];
      $arrTemplate[0]['total'] = $obj->arraySum($arrTemplate[0]['total'], $return['subtotal'][0]);

   }

   $tableHeader = $twig->render('template-header.html', $arrTwigVar);
   $obj->generateReport($_POST, $tempreport, $arrTemplate, $dataToExport, $arrFilterInformation, $tableHeader);


}


echo $twig->render('reportBankReconsiliation.html', $arrTwigVar);



?>