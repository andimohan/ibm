<?php

require_once '../../_config.php';
require_once '../../_include-v2.php';

includeClass('MedicalRequestClaim.class.php');
$medicalRequestClaim = createObjAndAddToCol(new MedicalRequestClaim());
$medicalJobOrder = createObjAndAddToCol(new MedicalJobOrder());
$employee = createObjAndAddToCol(new Employee());


include '_global.php';
$obj = $medicalRequestClaim;

$securityObject = 'reportActivityTransactionLog'; // the value of security object is manually inserted to handle 
// some modules that have different security object from that of their class

if (!$security->isAdminLogin($securityObject, 10, true));

$arrFilterInformation = array();

// ===== FOR EXPORT SECTION
$dataToExport = array();

/* data structure */
$arrTemplate = array();

$arrDataStructure = array();
$arrDataStructure['rowNumber'] = array('title' => '#', 'align' => 'right', 'sortable' => false, 'width' => "40px", 'autoNumber' => true, "sortable" => false);
$arrDataStructure['trdate'] = array('title' => ucwords($obj->lang['actionTime']), 'dbfield' => 'createdon', 'sortable' => false, 'width' => "150px", 'format' => 'datetime');
$arrDataStructure['code'] = array('title' => ucwords($obj->lang['transactionCode']), 'dbfield' => 'code', 'sortable' => false,'width' => "200px");
$arrDataStructure['employee'] = array('title' => ucwords($obj->lang['employee']), 'dbfield' => 'employeename', 'sortable' => false,'width' => "200px");
$arrDataStructure['actionName'] = array('title' => ucwords($obj->lang['activity']), 'dbfield' => 'actionname', 'sortable' => false,'width' => "150px");
$arrDataStructure['description'] = array('title' => ucwords($obj->lang['note']), 'dbfield' => 'trdesc', 'sortable' => false,'width' => "300px");
$arrDataStructure['attachment'] = array('title' => ucwords($obj->lang['attachment']), 'dbfield' => 'attachment', 'sortable' => false,'width' => "300px");

$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $obj->lang['transactionLogReport'];
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();

array_push($arrTemplate, $arrHeaderTemplate);

if (isset($_POST) && !empty($_POST['hidAction'])) {
     

    $criteria = '';

    if (isset($_POST) && !empty($_POST['selModule'])) {
        $objRef = $obj->getObjMapping(null,$_POST['selModule']);
        $criteria .= ' AND ' . $obj->transactionLog . '.tablekey in (' . $class->oDbCon->paramString($_POST['selModule'], ',') . ')';
        // array_push($arrFilterInformation, array("label" => $obj->lang['employee'], 'filter' => $_POST['employeeName']));
    }
    if (isset($_POST) && !empty($_POST['employeeName'])) {
        $criteria .= ' AND ' . $obj->tableEmployee . '.name LIKE (' . $class->oDbCon->paramString('%' . $_POST['employeeName'] . '%') . ')';
        array_push($arrFilterInformation, array("label" => $obj->lang['employee'], 'filter' => $_POST['employeeName']));
    }
    if (isset($_POST) && !empty($_POST['code'])) {
        $criteria .= ' AND ' . $objRef->tableName . '.code = ' . $obj->oDbCon->paramString($_POST['code']);
        array_push($arrFilterInformation, array("label" => $obj->lang['code'], 'filter' => $_POST['code']));
    }


    $orderBy = (!empty($_POST['hidOrderBy'])) ? $obj->oDbCon->paramOrder($_POST['hidOrderBy']) : 'pkey'; // order by harus dr kolom yg terdaftar saja
    $orderType = (isset($_POST['hidOrderType']) && !empty($_POST['hidOrderType']) && $_POST['hidOrderType'] == 1) ? 'desc' : 'asc';


    $order = 'order by ' . $orderBy . ' ' . $orderType;

    $tempreport = '';

    $rs = array();
    if (!empty($_POST['code'])) {
        $rs = $obj->getActivityLog($objRef, $criteria);
    
            
            $rsReindex = $obj->reindexDetailCollections($rs,'tablekey');
            
            foreach ($rsReindex as $index){
                $tablekey = $index[0]['tablekey'];
                $refkey = array_column($index, 'refkey');
                $objRef = $obj->getObjMapping(null, $tablekey);
                
                $rsObjRef = $objRef->searchDataRow( array($objRef->tableName . '.pkey', $objRef->tableName . '.code', $objRef->tableName . '.statuskey'), ' and ' . $objRef->tableName . '.pkey in (' . $objRef->oDbCon->paramString($refkey, ',') . ')');
                $rsCode = array_column($rsObjRef, null, 'pkey');

                if (isset( $objRef->tableFile ) ) {
                    for ($i=0; $i<count($rsObjRef); $i++) {
                        $pkey = $rsObjRef[$i]['pkey'];
                        $rsItemFile = $objRef->getFileDetail($pkey);
                        $arrItemFile = array();
                        if (!empty($rsItemFile)) {
                            for ($j=0; $j<count($rsItemFile); $j++) {
                                array_push($arrItemFile, '<a href="/download.php?filename='.$objRef->uploadFileFolder.$pkey.'/'.$rsItemFile[$j]['file'].'" target="_blank">'.$rsItemFile[$j]['file'].'</a>');
                            }
                        }
                        $arrItemFile = implode(", ",$arrItemFile);
                        $rsCode[$pkey]['attachment'] = $arrItemFile;
                    }
                }    
                $arrStatus = $objRef->getAllStatus();
                $arrStatus = array_column($arrStatus, null, 'pkey');
 
                $rsReindex[$tablekey]['data'] = $rsCode;
                $rsReindex[$tablekey]['status'] = $arrStatus;
            }
    }


    for ($i = 0; $i < count($rs); $i++) {

        $tablekey = $rs[$i]['tablekey'];
        $transactionkey = $rs[$i]['refkey'];

        $rs[$i]['code'] = $rsReindex[$tablekey]['data'][$transactionkey]['code'];
        
        $rs[$i]['actionname'] = ucwords($obj->lang[$rs[$i]['actionname']]);
        if ($rs[$i]['actionkey'] <= 10)
            $rs[$i]['actionname'] .= ': ' . $rsReindex[$tablekey]['status'][$rs[$i]['actionkey']]['status'];

        if (!empty($rs[$i]['reason']))
            $rs[$i]['actionname'] .= '<br><span class="text-muted"><i>' . $obj->replaceNewLine($rs[$i]['reason']) . '</i></span>';


        $rs[$i]['trdesc'] = $rsReindex[$tablekey]['data'][$transactionkey]['trdesc'];
        $rs[$i]['attachment']  = $rsReindex[$tablekey]['data'][$transactionkey]['attachment'];
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

$rsMedicalRequestClaim = $obj->getTableKeyAndObj($medicalRequestClaim->tableName, array('key'));
$rsMedicalJobOrder = $obj->getTableKeyAndObj($medicalJobOrder->tableName, array('key'));

$arrType = array();
$arrType[$rsMedicalRequestClaim['key']] = $obj->lang['newRequest'];
$arrType[$rsMedicalJobOrder['key']] = $obj->lang['jobOrder'];

$arrTwigVar['inputCode'] =  $class->inputText('code');
$arrTwigVar['inputSelModule'] =  $class->inputSelect('selModule', $arrType);
// $arrTwigVar['inputStartDate'] = $class->inputDate('trStartDate', array('etc' => 'style="text-align:center"'));
// $arrTwigVar['inputEndDate'] = $class->inputDate('trEndDate', array('etc' => 'style="text-align:center"'));
$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate;

echo $twig->render('reportMedicalRequestClaimHistory.html', $arrTwigVar);
?>
