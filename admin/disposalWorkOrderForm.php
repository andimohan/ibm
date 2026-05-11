<?php

require_once '../_config.php';
require_once '../_include-v2.php';

includeClass('DisposalWorkOrder.class.php');
$disposalWorkOrder = createObjAndAddToCol(new DisposalWorkOrder());
$warehouse = createObjAndAddToCol(new Warehouse());
$disposalWorkOrderDispatcher = createObjAndAddToCol(new DisposalWorkOrderDispatcher());
$disposalJobOrder = createObjAndAddToCol(new DisposalJobOrder());
$customer = createObjAndAddToCol(new Customer());
$employee = createObjAndAddToCol(new Employee());
$city = createObjAndAddToCol(new City());
$car = createObjAndAddToCol(new Car());
$service = createObjAndAddToCol(new Service());

$obj = $disposalWorkOrder;

$securityObject = $obj->securityObject;

if (!$security->isAdminLogin($securityObject, 10, true));

$formAction = 'disposalWorkOrderList';

$isQuickAdd = (isset($_GET) && !empty($_GET['quickadd'])) ? true : false;

$_POST['trDate'] = date('d / m / Y');

$rsWorkListDetail = array();
$rs = prepareOnLoadData($obj);
$rsItemFile = array();
$rsAssetGroup = array();
$rsItemDetail = array();

$useStorage = $obj->useStorage;

$editWarehouseInactiveCriteria = '';

if (!empty($_GET['id'])) {
    $id = $_GET['id'];
    $rsWorkList = $disposalWorkOrderDispatcher->searchData($disposalWorkOrderDispatcher->tableName . '.pkey', $rs[0]['refkey'], true);
    $rsWorkListDetail = $disposalWorkOrderDispatcher->getDetailWithRelatedInformation($rs[0]['refkey']);
    $rsDetail = $obj->getDetailWithRelatedInformation($rs[0]['pkey']);
    $rsJobOrder = $disposalJobOrder->searchData($disposalJobOrder->tableName . '.pkey', $rs[0]['jokey'], true);

    $_POST['trDate'] = $obj->formatDBDate($rs[0]['trdate'], 'd / m / Y');

    $_POST['hidWorkListKey'] = $rs[0]['refkey'];
    $_POST['JOCode'] = $rsJobOrder[0]['code'];
    $_POST['hidCustomerKey'] = $rs[0]['customerkey'];
    $_POST['workOrderCode'] = $rs[0]['workordercode'];
    $_POST['hidJobOrderKey'] = $rs[0]['jokey'];
    $_POST['hidWorkListDetailKey'] = $rs[0]['refdetailkey'];
    $_POST['workListCode'] = $rsWorkList[0]['code'];
    $_POST['disposalWeight'] = $obj->formatNumber($rs[0]['disposalweight'], 2);
    //$_POST['prorateWeight'] = $obj->formatNumber($rs[0]['prorateweight'], 2);

    if (!empty($rsJobOrder[0]['customerkey'])) {
        $rsCustomer = $customer->getDataRowById($rsJobOrder[0]['customerkey']);
        $_POST['customerName'] = $rsCustomer[0]['name'];
    }

    if (!empty($rsJobOrder[0]['citykey'])) {
        $rsCity = $city->searchData('city.pkey', $rsCustomer[0]['citykey'], true);
        $_POST['cityName'] = $rsCity[0]['name'] . ', ' . $rsCity[0]['categoryname'];
    }

    if (!empty($rsJobOrder[0]['servicekey'])) {
        $rsService = $service->getDataRowById($rsJobOrder[0]['servicekey']);
        $_POST['serviceName'] = $rsService[0]['name'];
        $_POST['maximumWeight'] = $obj->formatNumber($rsJobOrder[0]['maximumweight'], 2);
    }
    $_POST['totalDisposalWeight'] = $obj->formatNumber($rs[0]['totaldisposalweight'], 2);

    $_POST['selWarehouseKey'] = $rs[0]['warehousekey'];

    if (!empty($rsWorkList[0]['driverkey'])) {
        $rsEmployee = $employee->getDataRowById($rsWorkList[0]['driverkey']);
        $_POST['driverName'] = $rsEmployee[0]['name'];
    }

    if (!empty($rsWorkList[0]['carkey'])) {
        $rsCar = $car->getDataRowById($rsWorkList[0]['carkey']);
        $_POST['policeNumber'] = $rsCar[0]['code'] . ' - ' . $rsCar[0]['policenumber'];
    }

    $editWarehouseInactiveCriteria = ' or ' . $warehouse->tableName . '.pkey = ' . $obj->oDbCon->paramString($rs[0]['warehousekey']);
    
         //update file 
    if($useStorage){ 
        $rsFileDetail = $obj->getFileDetail($id);
    }else{  
        $rsItemFile = $obj->getFileDetail($id);
        $obj->prepareLoadedFile($id,array('file' => $rsItemFile ));
    }
}

$arrStatus = $obj->convertForCombobox($obj->getAllStatus(), 'pkey', 'status');
$arrJO = $obj->convertForCombobox($rsWorkListDetail, 'pkey', 'label');
$arrCustomer = $obj->convertForCombobox($rsWorkListDetail, 'customerkey', 'customername');
$arrWarehouse = $class->convertForCombobox($warehouse->searchData('', '', true, ' and (' . $warehouse->tableName . '.statuskey = 1' . $editWarehouseInactiveCriteria . ')'), 'pkey', 'name');

// $arrJobType = $obj->convertForCombobox($rsCategory, 'pkey', 'name');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title></title>

    <script type="text/javascript">
        jQuery(document).ready(function() {
            var tabID = <?php echo ($isQuickAdd) ?  $_GET['tabID'] :  'selectedTab.newPanel[0].id';  ?>;
            var opt = {};
            //  opt.arrFile = ();
            //  opt.uploadFileFolder = false;  <?php echo $obj->uploadFileFolder ? 'true' : 'false'; ?>;

            opt.fileFolder = "<?php echo $obj->uploadFileFolder; ?>";
            opt.fileUploaderTarget = "item-file-uploader";
            opt.arrFile = Array();
            opt.useStorage = <?php echo ($useStorage) ? "true" : "false"; ?>;
            
            var arrDetails = {};

            <?php 
//            if (!empty($_GET['id'])) {
//                for ($i = 0; $i < count($rsSODetail); $i++) {
//                    echo 'arrTemp = {};';
//                    echo 'arrTemp[\'customerkey\'] = "' . $rsSODetail[$i]['customerkey'] . '";';
//                    echo 'arrTemp[\'customername\'] = "' . $rsSODetail[$i]['customername'] . '";';
//                    echo 'arrDetails[' . $rsWorkListDetail[$i]['pkey'] . '] = arrTemp; ';
//                }
//            } 

            if (isset($id) && !empty($id)) {
                for ($i = 0; $i < count($rsItemFile); $i++) {
                    echo 'opt.arrFile.push("' . $rsItemFile[$i]['file'] . '"); ';
                }
            }
            ?>


            var disposalWorkOrder = new DisposalWorkOrder(tabID, opt, arrDetails);
            prepareHandler(disposalWorkOrder);

            var fieldValidation = {
                code: {
                    validators: {
                        notEmpty: {
                            message: phpErrorMsg.code[1]
                        },
                    }
                },
                workListCode: {
                    validators: {
                        notEmpty: {
                            message: phpErrorMsg.reference[1]
                        },
                    }
                },
            };
            setFormValidation(getTabObj(), $('#defaultForm-' + tabID), fieldValidation, <?php echo json_encode($obj->validationFormSubmitParam()); ?>);

        });
    </script>

</head>

<body>
    <div style="width:100%; margin:auto; " class="tab-panel-form">
        <div class="notification-msg"></div>

        <form id="defaultForm" method="post" class="form-horizontal" action="<?php echo $formAction; ?>">

            <?php prepareOnLoadDataForm($obj); ?>

            <div class="div-table main-tab-table-2">
                <div class="div-table-row">
                    <div class="div-table-col">
                        <div class="div-tab-panel">
                            <div class="div-table-caption border-orange"><?php echo $obj->lang['generalInformation']; ?></div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo $obj->lang['status']; ?></label>
                                <div class="col-xs-9">
                                    <?php echo  $obj->inputSelect('selStatus', $arrStatus, array('disabled' => true)); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo $obj->lang['code']; ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputAutoCode('code'); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['workOrderCode']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo  $obj->inputText('workOrderCode', array('readonly' => true)); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo $obj->lang['date']; ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputDateTime('trDate', array('allowedStatusForEdit' => array(1))); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['warehouse']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputSelect('selWarehouseKey', $arrWarehouse, array('allowedStatusForEdit' => array(1))); ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo $obj->lang['reference']; ?></label>
                                <div class="col-xs-9">
                                    <?php
                                    echo $obj->inputAutoComplete(
                                        array(
                                            'revalidateField' => true,
                                            'element' => array(
                                                'value' => 'workListCode',
                                                'key' => 'hidWorkListKey'
                                            ),
                                            'source' => array(
                                                'url' => 'ajax-disposal-work-order-dispatcher.php',
                                                'data' => array('action' => 'searchData', 'statuskey' => '(2)')
                                            ),
                                            'allowedStatusForEdit' => array(1),
                                            'callbackFunction' => 'getTabObj().updateWorkList()'
                                        )
                                    );
                                    ?>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['jobOrder']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputText('JOCode', array('readonly' => true)); ?>
                                    <?php echo $obj->inputHidden('hidJobOrderKey'); ?>
                                    <?php echo $obj->inputHidden('hidWorkListDetailKey'); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo $obj->lang['customer']; ?></label>
                                <div class="col-xs-9">
                                    <?php echo  $obj->inputSelect('hidCustomerKey', $arrCustomer,  array('allowedStatusForEdit' => array(1))); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['city']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo  $obj->inputText('cityName', array('readonly' => true)); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['car']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputText('policeNumber', array('readonly' => true)); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['driver']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputText('driverName', array('readonly' => true)); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['services']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputText('serviceName', array('readonly' => true)); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['total']); ?> <?php echo ucwords($obj->lang['waste']); ?> (Kg)</label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputDecimal('totalDisposalWeight', array('readonly' => true)); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="div-table-col">
                        <!-- <div class="div-tab-panel">
                            <div class="div-table" style="width:100%">
                                <div class="div-table-caption border-red"><?php echo ucwords($obj->lang['inventory']); ?></div>

                                <div class="div-table mnv-transaction transaction-detail package-detail" style="width:100%; border-bottom:1px solid #333; ">
                                    <div class="div-table-row">
                                        <div class="div-table-col detail-col-header"><?php echo ucwords($obj->lang['item']); ?></div>
                                        <div class="div-table-col detail-col-header" style="width:80px; text-align:right;"><?php echo ucwords($obj->lang['qty']); ?></div>
                                        <div class="div-table-col detail-col-header" style="width:80px;"><?php echo ucwords($obj->lang['unit']); ?></div>
                                        <div class="div-table-col detail-col-header <?php echo $obj->hideOnDisabled(); ?> icon-col"></div>
                                    </div>

                                    <?php
                                    $totalRows = count($rsItemDetail);

                                    for ($k = 0; $k <= $totalRows; $k++) {

                                        $class =  'transaction-detail-row';
                                        $overwrite = true;
                                        $etc = '';
                                        $arrUnit = $arrDefaultUnit;

                                        if ($k == $totalRows) {
                                            $class = 'item-row-template row-template';
                                            $overwrite = false;
                                            $etc = 'disabled="disabled"';
                                        } else {
                                            $decimal = 0;
                                            $inputnumber = 'inputnumber';

                                            $_POST['hidDetailItemKey[]'] =  $rsItemDetail[$k]['pkey'];
                                            $_POST['hidItemDetailKey[]'] =  $rsItemDetail[$k]['itemkey'];
                                            $_POST['itemDetailName[]'] =  $rsItemDetail[$k]['itemname'];
                                            $_POST['qty[]'] =   $obj->formatNumber($rsItemDetail[$k]['qty']);
                                            $_POST['selUnit[]'] =  $rsItemDetail[$k]['unitkey'];

                                            $arrUnit = $obj->convertForCombobox($item->getAvailableUnit($rsItemDetail[$k]['itemkey']), 'conversionunitkey', 'unitname');
                                        }

                                    ?>
                                        <div class="div-table-row <?php echo $class; ?>">
                                            <div class="div-table-col detail-col-detail"><?php echo $obj->inputText('itemDetailName[]', array('overwritePost' => $overwrite, 'etc' =>  $etc)); ?><?php echo $obj->inputHidden('hidItemDetailKey[]', array('overwritePost' => $overwrite, 'etc' => $etc)); ?><?php echo $obj->inputHidden('hidDetailItemKey[]', array('overwritePost' => $overwrite, 'etc' => $etc)); ?></div>
                                            <div class="div-table-col detail-col-detail"><?php echo $obj->inputNumber('qty[]', array('overwritePost' => $overwrite, 'etc' => 'style="text-align:right;"' . $etc)); ?></div>
                                            <div class="div-table-col detail-col-detail"><?php echo $obj->inputSelect('selUnit[]', $arrUnit, array('overwritePost' => $overwrite, 'etc' =>  $etc)); ?></div>
                                            <div class="div-table-col detail-col-detail  <?php echo $obj->hideOnDisabled(); ?> icon-col"><?php echo $obj->inputLinkButton('btnDeleteRows', '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button', 'etc' => 'tabIndex="-1"')); ?></div>
                                        </div>
                                    <?php  }   ?>

                                </div>
                                <div style="clear:both; height:1em;"></div>
                                <div class="package-detail" style="float:left; display:inline-block;"><?php echo $obj->inputButton('btnAddItemRows', $obj->lang['addRows'], array('class' => 'btn btn-primary btn-second-tone')); ?></div>

                            </div>
                        </div> -->
                        <!-- <div class="div-tab-panel">
                            <div class="div-table" style="width:100%">
                                <div class="div-table-caption border-purple"><?php echo ucwords($obj->lang['asset']); ?></div>

                                <div class="asset-group">
                                    <div class="div-table transaction-detail" style="width:100%">
                                        <?php

                                        $totalRows = count($rsAssetGroup);
                                        for ($j = 0; $j <= $totalRows; $j++) {
                                            $class =  'transaction-detail-row asset-group-row';
                                            $overwrite = true;
                                            $disabled = false;
                                            $display = '';
                                            $qty = '';
                                            $assetGroup = '';

                                            if ($j == $totalRows) {
                                                $class = 'asset-group-row-template';
                                                $overwrite = false;
                                                $disabled = true;
                                                $display = 'style="display:none"';
                                            } else {
                                                $qty = $obj->formatNumber($rsAssetGroup[$j]['qty']);
                                                $assetGroup = $rsAssetGroup[$j]['assetgroupname'];
                                            }

                                        ?>
                                            <div class="div-table-row  <?php echo $class; ?>" <?php echo $display; ?>>
                                                <div class="div-table-col  qty" style="width:2%;text-align:left;"><?php echo $qty; ?></div>
                                                <div class="div-table-col  " style="width:2%; text-align:left;">x</div>
                                                <div class="div-table-col  assetgroup" style=" text-align:left;"><?php echo $assetGroup; ?></div>
                                            </div>
                                        <?php  } ?>

                                    </div>

                                </div>
                                <div style="clear:both; height:1em;"></div>

                                <div class="div-table transaction-detail package-detail" style="width:100%; border-bottom:1px solid #333; ">
                                    <div class="div-table-row">
                                        <div class="div-table-col detail-col-header" style="width:120px;"><?php echo ucwords($obj->lang['code']); ?></div>
                                        <div class="div-table-col detail-col-header"><?php echo ucwords($obj->lang['asset']); ?></div>
                                        <div class="div-table-col detail-col-header" style="width:120px;"><?php echo ucwords($obj->lang['expirationDate']); ?></div>
                                        <div class="div-table-col detail-col-header <?php echo $obj->hideOnDisabled(); ?> icon-col"></div>
                                    </div>

                                    <?php
                                    $totalAssetRows = count($rsAssetDetail);


                                    for ($k = 0; $k <= $totalAssetRows; $k++) {

                                        $class =  'transaction-detail-row';
                                        $overwrite = true;
                                        $etc = '';

                                        if ($k == $totalAssetRows) {
                                            $class = 'asset-row-template row-template';
                                            $overwrite = false;
                                            $etc = 'disabled="disabled"';
                                        } else {
                                            $decimal = 0;

                                            $_POST['hidAssetDetailKey[]'] =  $rsAssetDetail[$k]['pkey'];
                                            $_POST['hidAssetKey[]'] =  $rsAssetDetail[$k]['assetkey'];
                                            $_POST['assetName[]'] =  $rsAssetDetail[$k]['assetname'];
                                            $_POST['assetCode[]'] =  $rsAssetDetail[$k]['assetcode'];
                                            $_POST['assetExpired[]'] =  $obj->formatDBDate($rsAssetDetail[$k]['explicensedate'], 'd / m / Y', array('returnOnEmpty' => true, 'value' => '00 / 00 / 0000'));
                                        }

                                    ?>
                                        <div class="div-table-row <?php echo $class; ?>">
                                            <div class="div-table-col detail-col-detail"><?php echo $obj->inputText('assetCode[]', array('overwritePost' => $overwrite, 'etc' =>  $etc)); ?><?php echo $obj->inputHidden('hidAssetDetailKey[]', array('overwritePost' => $overwrite, 'etc' => $etc)); ?></div>
                                            <div class="div-table-col detail-col-detail"><?php echo $obj->inputText('assetName[]', array('overwritePost' => $overwrite, 'readonly' => true, 'etc' =>  $etc)); ?><?php echo $obj->inputHidden('hidAssetKey[]', array('overwritePost' => $overwrite, 'etc' => $etc)); ?></div>
                                            <div class="div-table-col detail-col-detail"><?php echo $obj->inputDate('assetExpired[]', array('overwritePost' => $overwrite, 'readonly' => true, 'etc' =>  $etc . 'style="text-align:center;"', 'allowEmpty' => true)); ?></div>
                                            <div class="div-table-col detail-col-detail  <?php echo $obj->hideOnDisabled(); ?> icon-col"><?php echo $obj->inputLinkButton('btnDeleteRows', '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button', 'etc' => 'tabIndex="-1"')); ?></div>
                                        </div>
                                    <?php  }   ?>

                                </div>
                                <div style="clear:both; height:1em;"></div>
                                <div class="package-detail" style="float:left; display:inline-block;"><?php echo $obj->inputButton('btnAddAssetRows', $obj->lang['addRows'], array('class' => 'btn btn-primary btn-second-tone')); ?></div>
                                <div style="clear:both; height:1em;"></div>

                            </div>
                        </div> -->
                        
                          <?php if($useStorage) {  ?>
                             <div id="file-update-ajax" class="div-tab-panel">
                                 <div class="div-table" style="width:100%"> 
                                    <div class="div-table-caption border-purple"><?php echo ucwords($obj->lang['file']); ?></div> 
                                    <?php echo $obj->inputUploadFilePlugin($rs,$rsFileDetail); ?> 
                                 </div>
                            </div>     
                        <?php }else { ?> 
                              <div class="div-tab-panel">
                                <div class="div-table" style="width:100%">
                                    <div class="div-table-caption border-purple"><?php echo ucwords($obj->lang['file']); ?></div>
                                    <div class="div-table-row">
                                        <div class="div-table-col-5">
                                            <!-- file uploader -->
                                            <div class="item-file-uploader">
                                                <ul class="file-list"></ul>
                                                <div style="clear:both; height:1em;"></div>
                                                <div class="file-uploader">
                                                    <noscript>
                                                        <p>Please enable JavaScript to use file uploader.</p>
                                                    </noscript>
                                                </div>
                                            </div>
                                            <!-- file uploader -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        
                        <?php } ?>
                    </div>
                </div>
            </div>

            <div style="clear:both; height:2em;"></div>
            <div class="div-table transaction-detail" style="width:100%; ">
                <div class="div-table-row">
                    <div class="div-table-col detail-col-header" style="width:400px; text-align:left;"><?php echo ucwords($obj->lang['waste']); ?></div>
                    <div class="div-table-col detail-col-header"><?php echo ucwords($obj->lang['manifestCode']); ?></div>
                    <div class="div-table-col detail-col-header" style="width:120px; text-align:right;"><?php echo ucwords($obj->lang['weight']); ?> (Kg)</div>
                    <!-- <div class="div-table-col detail-col-header" style="width:250px; text-align:left;"><?php echo ucwords($obj->lang['supplierName']); ?></div>
                    <div class="div-table-col detail-col-header" style="width:120px; text-align:left;"><?php echo ucwords($obj->lang['ticketNumber']); ?></div>
                    <div class="div-table-col detail-col-header" style="width:120px; text-align:right;"><?php echo ucwords($obj->lang['supplier']); ?> (Kg)</div> -->
                    <!-- <div class="div-table-col detail-col-header" style="width:120px; text-align:right;"><?php echo ucwords($obj->lang['maxWeight']); ?> (Kg)</div> -->
                    <!--                    <div class="div-table-col detail-col-header" style="width:130px; text-align:center;"><?php echo ucwords($obj->lang['status']); ?></div> -->
                    <!-- <div class="div-table-col detail-col-header icon-col <?php echo $obj->hideOnDisabled(); ?>"></div> -->
                </div>
            </div>
            <div class="div-table mnv-transaction service-detail transaction-detail" style="width:100%; border-bottom:1px solid #333; ">
                <div class="div-table-row" style="display:none">
                    <div class="div-table-col"></div>
                    <div class="div-table-col"></div>
                    <div class="div-table-col"></div>
                </div>

                <?php
                $totalRows = count($rsDetail);
                for ($i = 0; $i <= $totalRows; $i++) {

                    $class =  'transaction-detail-row';
                    $overwrite = true;
                    $etc = '';

                    if ($i == $totalRows) {
                        $class = 'detail-row-template';
                        $overwrite = false;
                        $etc = 'disabled="disabled"';
                    } else {
                        $decimal = 0;
                        $inputnumber = 'inputnumber';

                        $_POST['hidDetailKey[]'] =  $rsDetail[$i]['pkey'];
                        $_POST['hidSupplierKey[]'] =  $rsDetail[$i]['supplierkey'];
                        $_POST['supplierName[]'] =  $rsDetail[$i]['suppliername'];
                        $_POST['manifestCode[]'] =  $rsDetail[$i]['manifestcode'];
                        $_POST['ticketNumber[]'] =  $rsDetail[$i]['ticketnumber'];
                        $_POST['hidWasteKey[]'] =  $rsDetail[$i]['wastekey'];
                        $_POST['waste[]'] =  $rsDetail[$i]['waste'];
                        $_POST['manifest[]'] =  $rsDetail[$i]['manifest'];

                        $_POST['customerName[]'] =  $rsDetail[$i]['manifest'];
                        $_POST['contractName[]'] =  $rsDetail[$i]['contractname'];
                        $_POST['hidCustomerKey[]'] =  $rsDetail[$i]['customerkey'];
                        $_POST['hidServiceKey[]'] =  $rsDetail[$i]['servicekey'];
                        $statusDetailName =  $rsDetail[$i]['statusname'];
                        $statusStyle =  $rsDetail[$i]['statuscolor'];
                        $_POST['quota[]'] =   $obj->formatNumber($rsDetail[$i]['quota'], 2);
                        $_POST['supplierWeight[]'] =   $obj->formatNumber($rsDetail[$i]['supplierweight'], 2);
                        $_POST['customerWeight[]'] =   $obj->formatNumber($rsDetail[$i]['customerweight'], 2);
                    }
                ?>

                    <div class="div-table-row <?php echo $class; ?>">
                        <div class="div-table-col detail-col-detail" style="padding:0">
                            <div class="div-table" style="width:100%">
                                <div class="div-table-row">
                                    <div class="div-table-col detail-col-detail" style="width:400px;">
                                        <?php echo $obj->inputText('waste[]', array('overwritePost' => $overwrite, 'etc' => $etc)); ?>
                                        <?php echo $obj->inputHidden('hidDetailKey[]', array('overwritePost' => $overwrite, 'etc' => $etc)); ?>
                                        <?php echo $obj->inputHidden('hidWasteKey[]', array('overwritePost' => $overwrite, 'etc' => $etc)); ?>
                                    </div>
                                    <div class="div-table-col detail-col-detail">
                                        <?php echo $obj->inputText('manifestCode[]', array('overwritePost' => $overwrite, 'etc' => 'style="text-align:left;" ' . $etc)); ?>
                                    </div>
                                    <div class="div-table-col detail-col-detail" style="width:120px;">
                                        <?php echo $obj->inputDecimal('customerWeight[]', array('overwritePost' => $overwrite, 'etc' => 'style="text-align:right;" ' . $etc, 'allowedStatusForEdit' => array(1, 2))); ?>
                                    </div>
                                    <!-- <div class="div-table-col detail-col-detail" style="width:250px;">
                                        <?php echo $obj->inputHidden('hidSupplierKey[]', array('overwritePost' => $overwrite, 'etc' => $etc)); ?>
                                        <?php echo $obj->inputText('supplierName[]', array('overwritePost' => $overwrite, 'etc' => 'style="text-align:left;" ' . $etc)); ?>
                                    </div>
                                    <div class="div-table-col detail-col-detail" style="width:120px;">
                                        <?php echo $obj->inputText('ticketNumber[]', array('overwritePost' => $overwrite, 'etc' => 'style="text-align:left;" ' . $etc)); ?>
                                    </div>
                                    <div class="div-table-col detail-col-detail" style="width:120px;">
                                        <?php echo $obj->inputDecimal('supplierWeight[]', array('overwritePost' => $overwrite, 'etc' => 'style="text-align:right;" ' . $etc)); ?>
                                    </div> -->
                                    <!--                                    <div class="div-table-col detail-col-detail status-label " style="text-align: center; width:130px; "><label class=" <?php echo $statusStyle; ?>"><?php echo $statusDetailName; ?></label></div> -->
                                </div>
                            </div>
                        </div>
                        <!-- <div class="div-table-col detail-col-detail icon-col align-top-adjust  <?php echo $obj->hideOnDisabled(); ?>">
                            <?php echo $obj->inputLinkButton('btnDeleteRows', '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button', 'etc' => 'tabIndex="- 1"')); ?>
                        </div> -->
                    </div>
                <?php }   ?>

            </div>

            <div style="clear:both; height:1em;"></div>
            <!-- <div style="float:left; display:inline-block;"><?php echo $obj->inputButton('btnAddRows', $obj->lang['addRows'], array('class' => 'btn btn-primary btn-second-tone')); ?></div> -->

            <!-- <div class="div-table" style="float:right">
                <div class="div-table-row">
                    <div class="div-table-col">
                        <div class="div-table" style="float:right;">
                            <div class="div-table-row form-group">
                                <div class="div-table-col-3" style="text-align:right;">
                                    <?php echo ucwords($obj->lang['total']); ?>
                                </div>
                                <div class="div-table-col-3">
                                    <div class="flex">
                                        <div class="consume" style="width:120px;">
                                            <?php echo $obj->inputDecimal('totalCustomerWeight', array('readonly' => true, 'etc' => 'style="text-align:right;"')); ?>
                                        </div>
                                        <div class="consume" style="width:110px;">
                                            <?php echo $obj->inputDecimal('totalProRate', array('readonly' => true, 'etc' => 'style="text-align:right;"')); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="div-table-col icon-col <?php echo $obj->hideOnDisabled(); ?>"></div>
                </div>
            </div> -->

            <div class="form-button-margin"></div>
            <div class="form-button-panel">
                <?php echo $obj->generateSaveButton(array(), true);   ?>
            </div>

        </form>

        <?php echo $obj->showDataHistory(); ?>

    </div>
</body>

</html>