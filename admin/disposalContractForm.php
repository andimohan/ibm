<?php
require_once '../_config.php';
require_once '../_include-v2.php';

includeClass(array('DisposalContract.class.php'));
$disposalContract = createObjAndAddToCol(new DisposalContract());
$customer = createObjAndAddToCol(new Customer());
$service = createObjAndAddToCol(new Service());
$city = createObjAndAddToCol(new City());
$employee = createObjAndAddToCol(new Employee());
$waste = createObjAndAddToCol(new Waste());
$itemUnit = createObjAndAddToCol(new ItemUnit());
$item = createObjAndAddToCol(new Item());

$obj = $disposalContract;
$securityObject = $obj->securityObject;

if (!$security->isAdminLogin($securityObject, 10, true));

$overwriteContractAllowed = $security->isAdminLogin($disposalContract->overwriteContractSecurityObject, 10);
$editSales = array(1);
if ($overwriteContractAllowed)  
    $editSales = array(1,2);

$formAction = 'disposalContractList';
$isQuickAdd = (isset($_GET) && !empty($_GET['quickadd'])) ? true : false;

$finalDiscDecimal = 0;

$editTermOfPaymentInactiveCriteria = '';
$editPaymentMethodInactiveCriteria = '';


$rsPaymentMethodDetail = array();
$rsItemFile = array();
$finalDiscDecimalType = 'inputnumber';

$_POST['trDate'] = date('d / m / Y');
$_POST['startingDate'] = date('d / m / Y');

$lockTransactionDate = TABLENAME_SETTINGS[$obj->tableName]['locktransactiondate'];

$rs = prepareOnLoadData($obj);

$rsItemDetail = array();
$rsAssetDetail= array();
$rsWasteDetail= array();

$useStorage = $obj->useStorage;

if (!empty($_GET['id'])) {

    $id = $_GET['id'];

    $rsAssetDetail = $obj->getAssetGroupDetail($id);
    $rsItemDetail = $obj->getItemDetail($id);
    $rsWasteDetail = $obj->getWasteDetail($id);
    $_POST['maximumWeight'] =  $obj->formatNumber($rs[0]['maximumweight'], 2);

    if (!empty($rs[0]['servicekey'])) {
        $rsService = $service->getDataRowById($rs[0]['servicekey']);
        $_POST['serviceName'] = $rsService[0]['name'];
        $_POST['hidServiceKey'] = $rsService[0]['pkey'];
    }

    if (!empty($rs[0]['citykey'])) {
        $_POST['hidAreaKey'] = $rs[0]['areakey'];
        $rsCity = $city->searchData($city->tableName . '.pkey', $rs[0]['citykey'], true);
        $_POST['area'] = $rsCity[0]['citycategoryname'];
        $_POST['hidCityKey'] = $rsCity[0]['pkey'];
    }

    if (!empty($rs[0]['saleskey'])) {
        $_POST['hidSalesKey'] = $rs[0]['saleskey'];
        $rsSales = $employee->getDataRowById($rs[0]['saleskey']);
        $_POST['salesName'] = $rsSales[0]['name'];
    }

    if (!empty($rs[0]['customerkey'])) {
        $rsCustomer = $customer->getDataRowById($rs[0]['customerkey']);
        $_POST['hidCustomerKey'] = $rsCustomer[0]['pkey'];
        $_POST['customerName'] = $rsCustomer[0]['name'];
    }

    if (!empty($rs[0]['wastecategorykey'])) {
        $rsWasteCategory = $waste->getWasteCategory($rs[0]['wastecategorykey']);
        $_POST['hidWasteCategoryKey'] = $rsWasteCategory[0]['pkey'];
        $_POST['wasteCategoryName'] = $rsWasteCategory[0]['name'];
    }
    $_POST['selWasteCategory'] = $rs[0]['wastecategorykey'];

     //update file 
    if($useStorage){ 
        $rsFileDetail = $obj->getFileDetail($id);
    }else{  
        $rsItemFile = $obj->getFileDetail($id);
        $obj->prepareLoadedFile($id,array('file' => $rsItemFile ));
    }
}

$arrStatus = $obj->generateComboboxOpt(array('data' => $obj->getAllStatus(), 'label' => 'status'));
$arrDefaultUnit = $obj->convertForCombobox($itemUnit->searchData('', '', true, ' and (' . $itemUnit->tableName . '.statuskey = 1 )'), 'pkey', 'name');
$arrWasteCategory = $obj->generateComboboxOpt(array('data' => $waste->getWasteCategory()));
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title></title>
    <script type="text/javascript">
        jQuery(document).ready(function() {

            var tabID = <?php echo ($isQuickAdd) ?  $_GET['tabID'] :  'selectedTab.newPanel[0].id';  ?>;
            var opt = Array();
            //  opt.arrFile = ();
            //  opt.uploadFileFolder = false;  <?php echo $obj->uploadFileFolder ? 'true' : 'false'; ?>;
            
            opt.fileFolder = "<?php echo $obj->uploadFileFolder; ?>";
            opt.fileUploaderTarget = "item-file-uploader";
            opt.arrFile = Array(); 
            opt.useStorage = <?php echo ($useStorage) ? "true" : "false"; ?>;

            <?php
            if (isset($id) && !empty($id)) {
                for ($i = 0; $i < count($rsItemFile); $i++) {
                    echo 'opt.arrFile.push("' . $rsItemFile[$i]['file'] . '"); ';
                }
            }
            ?>

            var disposalContract = new DisposalContract(tabID, opt, <?php echo json_encode(
                                                                    array(
                                                                        'assetGroupDetail' => $rsAssetDetail,
                                                                        'itemDetail' => $rsItemDetail,
                                                                        'wasteDetail' => $rsWasteDetail
                                                                    )
                                                                ); ?>);

            prepareHandler(disposalContract);

            var fieldValidation = {
                code: {
                    validators: {
                        notEmpty: {
                            message: phpErrorMsg.code[1]
                        },
                    }
                },
//                name: {
//                    validators: {
//                        notEmpty: {
//                            message: phpErrorMsg.name[1]
//                        },
//                    }
//                },
                customerName: {
                    validators: {
                        notEmpty: {
                            message: phpErrorMsg.customer[1]
                        },
                    }
                },
                serviceName: {
                    validators: {
                        notEmpty: {
                            message: phpErrorMsg.service[1]
                        },
                    }
                },
                contractDuration: {
                    validators: {
                        greaterThan: {
                            value: 0,
                            inclusive: false,
                            separator: ',',
                            message: phpErrorMsg.contract[4]
                        }
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
                            <div class="div-table-caption border-orange"><?php echo ucwords($obj->lang['generalInformation']); ?></div>

                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['status']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo  $obj->inputSelect('selStatus', $arrStatus); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['code']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputAutoCode('code'); ?>
                                </div>
                            </div>
<!--
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['name']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputText('name'); ?>
                                </div>
                            </div>
-->
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['date']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputDate('trDate',array('readonly' => $lockTransactionDate)); ?>
                                </div>
                            </div>
                            <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['startingDate']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputDate('startingDate',array('allowedStatusForEdit' => $editSales)); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['customer']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputAutoComplete(
                                        array(
                                            'objRefer' => $customer,
                                            'revalidateField' => true,
                                            'element' => array(
                                                'value' => 'customerName',
                                                'key' => 'hidCustomerKey'
                                            ),
                                            'source' => array(
                                                'url' => 'ajax-customer.php',
                                                'data' => array(
                                                    'action' => 'searchData'
                                                )
                                            ),
                                            'callbackFunction' => 'getTabObj().updateCustomerInformation()'
                                        )
                                    );
                                    ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['city']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputText('area', array('readonly' => true)); ?>
                                    <?php echo $obj->inputHidden('hidCityKey'); ?>
                                    <?php echo $obj->inputHidden('hidAreaKey'); ?>
                                    <?php echo $obj->inputHidden('hidServiceDetailWasteKey'); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['service']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputAutoComplete(
                                        array(
                                            'revalidateField' => true,
                                            'element' => array(
                                                'value' => 'serviceName',
                                                'key' => 'hidServiceKey'
                                            ),
                                            'source' => array(
                                                'url' => 'ajax-service.php',
                                                'data' => array(
                                                    'action' => 'searchData&itemtype=3'
                                                )
                                            ),
                                            'callbackFunction' => 'getTabObj().updateService()'
                                        )
                                    );
                                    ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['contractDuration']); ?></label>
                                <div class="col-xs-7">
                                    <?php echo $obj->inputNumber('contractDuration'); ?>
                                </div>
                                <div class="col-xs-2">
                                    <div class="control-label service-type-information"><?php echo $obj->lang['month']; ?></div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['salesman']); ?></label>
                                <div class="col-xs-9">
                                    <?php
                                    echo $obj->inputAutoComplete(
                                        array(
                                            'objRefer' => $employee,
                                            'revalidateField' => false,
                                            'element' => array(
                                                'value' => 'salesName',
                                                'key' => 'hidSalesKey'
                                            ),
                                            'source' => array(
                                                'url' => 'ajax-employee.php',
                                                'data' => array(
                                                    'action' => 'searchData',
                                                    'issales' => 1
                                                )
                                            ),
                                            'allowedStatusForEdit' => $editSales
                                        )
                                    );
                                    ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['wasteCategory']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputText('wasteCategoryName', array('readonly' => true)); ?>
                                    <?php echo $obj->inputHidden('hidWasteCategoryKey'); ?>
                                </div>
                            </div> 
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['note']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo  $obj->inputTextArea('trDesc', array('etc' => 'style="height:10em;"')); ?>
                                </div>
                            </div>
                        </div>
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
                        <div class="div-tab-panel">
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

                                <div class="div-table transaction-detail asset-detail package-detail" style="width:100%; border-bottom:1px solid #333; ">
                                    <div class="div-table-row">
                                        <div class="div-table-col detail-col-header"><?php echo ucwords($obj->lang['asset']); ?></div>
                                        <div class="div-table-col detail-col-header" style="width:80px; text-align:right;"><?php echo ucwords($obj->lang['qty']); ?></div>
                                        <div class="div-table-col detail-col-header <?php echo $obj->hideOnDisabled(); ?> icon-col"></div>
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

                                            $_POST['hidAssetGroupDetailKey[]'] =  $rsAssetDetail[$k]['pkey'];
                                            $_POST['hidAssetGroupKey[]'] =  $rsAssetDetail[$k]['assetgroupkey'];
                                            $_POST['assetGroupName[]'] =  $rsAssetDetail[$k]['assetgroupname'];
                                            $_POST['qtyAsset[]'] =   $obj->formatNumber($rsAssetDetail[$k]['qty']);
                                        }

                                    ?>
                                        <div class="div-table-row <?php echo $class; ?>">
                                            <div class="div-table-col detail-col-detail"><?php echo $obj->inputText('assetGroupName[]', array('overwritePost' => $overwrite, 'etc' =>  $etc)); ?><?php echo $obj->inputHidden('hidAssetGroupKey[]', array('overwritePost' => $overwrite, 'etc' => $etc)); ?><?php echo $obj->inputHidden('hidAssetGroupDetailKey[]', array('overwritePost' => $overwrite, 'etc' => $etc)); ?></div>
                                            <div class="div-table-col detail-col-detail"><?php echo $obj->inputNumber('qtyAsset[]', array('overwritePost' => $overwrite, 'etc' => 'style="text-align:right;"' . $etc)); ?></div>
                                            <div class="icon-col div-table-col detail-col-detail <?php echo $obj->hideOnDisabled(); ?>"><?php echo $obj->inputLinkButton('btnAddAssetRows', '<i class="fas fa-plus-circle"></i>', array('class' => 'btn btn-link add-row-button', 'etc' => 'attr-template="asset-row-template"')); ?></div>
                                            <div class="icon-col div-table-col detail-col-detail <?php echo $obj->hideOnDisabled(); ?>"><?php echo $obj->inputLinkButton('btnDeleteRows', '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button', 'etc' =>  'tabIndex="-1" style="padding:6px 0; ' . $hideDeleteIcon . '"')); ?></div>
                                        </div>
                                    <?php  }   ?>

                                </div>
                                <div style="clear:both; height:1em;"></div>

                            </div>
                        </div> 
                        <div class="div-tab-panel">
                            <div class="div-table" style="width:100%">
                                <div class="div-table-caption border-red"><?php echo ucwords($obj->lang['item']); ?></div>

                                <div class="div-table mnv-transaction transaction-detail item-detail package-detail" style="width:100%; border-bottom:1px solid #333; ">
                                    <div class="div-table-row">
                                        <div class="div-table-col detail-col-header"><?php echo ucwords($obj->lang['item']); ?></div>
                                        <div class="div-table-col detail-col-header" style="width:80px; text-align:right;"><?php echo ucwords($obj->lang['qty']); ?></div>
                                        <div class="div-table-col detail-col-header" style="width:80px;"><?php echo ucwords($obj->lang['unit']); ?></div>
                                        <div class="div-table-col detail-col-header <?php echo $obj->hideOnDisabled(); ?> icon-col"></div>
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

                                            $_POST['hidItemDetailKey[]'] =  $rsItemDetail[$k]['pkey'];
                                            $_POST['hidItemKey[]'] =  $rsItemDetail[$k]['itemkey'];
                                            $_POST['itemName[]'] =  $rsItemDetail[$k]['itemname'];
                                            $_POST['qty[]'] =   $obj->formatNumber($rsItemDetail[$k]['qty']);
                                            $_POST['selUnit[]'] =  $rsItemDetail[$k]['unitkey'];

                                            $arrUnit = $obj->convertForCombobox($item->getAvailableUnit($rsItemDetail[$k]['itemkey']), 'conversionunitkey', 'unitname');
                                        }

                                    ?>
                                        <div class="div-table-row <?php echo $class; ?>">
                                            <div class="div-table-col detail-col-detail"><?php echo $obj->inputText('itemName[]', array('overwritePost' => $overwrite, 'etc' =>  $etc)); ?><?php echo $obj->inputHidden('hidItemKey[]', array('overwritePost' => $overwrite, 'etc' => $etc)); ?><?php echo $obj->inputHidden('hidItemDetailKey[]', array('overwritePost' => $overwrite, 'etc' => $etc)); ?></div>
                                            <div class="div-table-col detail-col-detail"><?php echo $obj->inputNumber('qty[]', array('overwritePost' => $overwrite, 'etc' => 'style="text-align:right;"' . $etc)); ?></div>
                                            <div class="div-table-col detail-col-detail"><?php echo $obj->inputSelect('selUnit[]', $arrUnit, array('overwritePost' => $overwrite, 'etc' =>  $etc)); ?></div>
                                            <div class="icon-col div-table-col detail-col-detail <?php echo $obj->hideOnDisabled(); ?>"><?php echo $obj->inputLinkButton('btnAddItemRows', '<i class="fas fa-plus-circle"></i>', array('class' => 'btn btn-link add-row-button', 'etc' => 'attr-template="item-row-template"')); ?></div>
                                            <div class="icon-col div-table-col detail-col-detail <?php echo $obj->hideOnDisabled(); ?>"><?php echo $obj->inputLinkButton('btnDeleteRows', '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button', 'etc' =>  'tabIndex="-1" style="padding:6px 0; ' . $hideDeleteIcon . '"')); ?></div>
                                        </div>
                                    <?php  }   ?>

                                </div>
                                <div style="clear:both; height:1em;"></div>

                            </div>
                        </div>
                    </div>

                    <div class="div-table-col">
                        <div class="div-tab-panel">
                            <div class="div-table-caption border-blue"><?php echo ucwords($obj->lang['serviceInformation']); ?></div>
                            <div class="form-group">
                                <div class="col-xs-12">
                                    <div class="flex">
                                        <div class="consume">
                                            <?php echo ucwords($obj->lang['duration']); ?> (<?php echo $obj->lang['month']; ?>)<br>
                                            <?php echo $obj->inputNumber('duration', array('etc' => 'style="text-align:right;padding-right:12px;"', 'readonly' => !$overwriteContractAllowed)); ?></div>
                                        <div class="consume" style="padding:0 0.5em">
                                            <?php echo ucwords($obj->lang['totalVisit']); ?><br>
                                            <?php echo  $obj->inputNumber('qtyService', array('etc' => 'style="text-align:right;padding-right:12px;"', 'readonly' => !$overwriteContractAllowed)); ?>
                                        </div>
                                        <!-- <div class="consume">
                                            <?php echo ucwords($obj->lang['maxWeight']); ?><br>
                                            <?php echo  $obj->inputDecimal('maximumWeight', array('etc' => 'style="text-align:right;padding-right:12px;"', 'readonly' => !$overwriteContractAllowed)); ?>
                                        </div> -->
                                    </div>
                                </div>
                            </div>
                            <div class="form-group" style="margin-top:1em">
                                <div class="col-xs-12">
                                    <div class="flex">
                                        <div class="consume">
                                            <?php echo ucwords($obj->lang['sellingPrice']); ?><br>
                                            <?php echo $obj->inputNumber('sellingPrice', array('etc' => 'style="text-align:right;padding-right:12px;"', 'readonly' => !$overwriteContractAllowed)); ?></div>
                                        <div class="consume" style="padding:0 0.5em">
                                            <?php echo ucwords($obj->lang['additional']); ?> / <?php echo ucwords($obj->lang['visit']); ?><br>
                                            <?php echo  $obj->inputNumber('exceedSellingPriceArea', array('etc' => 'style="text-align:right;padding-right:12px;"', 'readonly' => !$overwriteContractAllowed)); ?>
                                        </div>
                                        <!-- <div class="consume">
                                            <?php echo ucwords($obj->lang['additional']); ?> / (Kg)<br>
                                            <?php echo  $obj->inputDecimal('exceedWeightPriceArea', array('etc' => 'style="text-align:right;padding-right:12px;"', 'readonly' => !$overwriteContractAllowed)); ?>
                                        </div> -->
                                    </div>
                                </div>
                            </div>
<!--
                            <div class="form-group" style="margin-top:1em">
                                <div class="col-xs-12">
                                    <?php echo ucwords($obj->lang['serviceFacilities']); ?> <br>
                                    <?php echo  $obj->inputTextArea('serviceFacilities', array('etc' => 'style="height:10em;"')); ?>
                                </div>
                            </div>
-->
                        </div>

                        <div class="div-tab-panel">
                            <div class="div-table" style="width:100%">
                                <div class="div-table-caption border-red"><?php echo ucwords($obj->lang['waste']); ?></div>

                                <div class="div-table mnv-transaction transaction-detail waste-detail package-detail" style="width:100%; border-bottom:1px solid #333; ">
                                    <div class="div-table-row">
                                        <div class="div-table-col detail-col-header"><?php echo ucwords($obj->lang['waste']); ?></div>
                                        <div class="div-table-col detail-col-header" style="width:100px; text-align:right;"><?php echo ucwords($obj->lang['sellingPrice']); ?> / kg</div>
                                        <div class="div-table-col detail-col-header" style="width:100px; text-align:right;"><?php echo ucwords($obj->lang['minWeight']); ?></div>
                                        <div class="div-table-col detail-col-header" style="width:100px; text-align:right;"><?php echo ucwords($obj->lang['maxWeight']); ?></div>
                                        <div class="div-table-col detail-col-header <?php echo $obj->hideOnDisabled(); ?> icon-col"></div>
                                        <div class="div-table-col detail-col-header <?php echo $obj->hideOnDisabled(); ?> icon-col"></div>
                                    </div>

                                    <?php
                                    $totalRows = count($rsWasteDetail);

                                    for ($k = 0; $k <= $totalRows; $k++) {

                                        $class =  'transaction-detail-row';
                                        $overwrite = true;
                                        $etc = '';
                                        $arrUnit = $arrDefaultUnit;

                                        if ($k == $totalRows) {
                                            $class = 'waste-row-template row-template';
                                            $overwrite = false;
                                            $etc = 'disabled="disabled"';
                                        } else {
                                            $decimal = 0;
                                            $inputnumber = 'inputnumber';

                                            $_POST['hidWasteDetailKey[]'] =  $rsWasteDetail[$k]['pkey'];
                                            $_POST['hidWasteKey[]'] =  $rsWasteDetail[$k]['wastekey'];
                                            $_POST['wasteName[]'] =  $rsWasteDetail[$k]['wastecodename'];
                                            $_POST['weightPrice[]'] =   $obj->formatNumber($rsWasteDetail[$k]['weightprice']);
                                            $_POST['minWeight[]'] =   $obj->formatNumber($rsWasteDetail[$k]['minweight'], 2);
                                            $_POST['maxWeight[]'] =   $obj->formatNumber($rsWasteDetail[$k]['maxweight'], 2);
                                        }

                                    ?>
                                        <div class="div-table-row <?php echo $class; ?>">
                                            <div class="div-table-col detail-col-detail">
                                                <?php echo $obj->inputText('wasteName[]', array('overwritePost' => $overwrite, 'etc' =>  $etc)); ?>
                                                <?php echo $obj->inputHidden('hidWasteKey[]', array('overwritePost' => $overwrite, 'etc' => $etc)); ?>
                                                <?php echo $obj->inputHidden('hidWasteDetailKey[]', array('overwritePost' => $overwrite, 'etc' => $etc)); ?></div>
                                            <div class="div-table-col detail-col-detail"><?php echo $obj->inputNumber('weightPrice[]', array('overwritePost' => $overwrite, 'etc' => 'style="text-align:right;"' . $etc)); ?></div>
                                            <div class="div-table-col detail-col-detail"><?php echo $obj->inputDecimal('minWeight[]', array('overwritePost' => $overwrite, 'etc' => 'style="text-align:right;"' . $etc)); ?></div>
                                            <div class="div-table-col detail-col-detail"><?php echo $obj->inputDecimal('maxWeight[]', array('overwritePost' => $overwrite, 'etc' => 'style="text-align:right;"' . $etc)); ?></div>
                                            <div class="icon-col div-table-col detail-col-detail <?php echo $obj->hideOnDisabled(); ?>"><?php echo $obj->inputLinkButton('btnAddWasteRows', '<i class="fas fa-plus-circle"></i>', array('class' => 'btn btn-link add-row-button', 'etc' => 'attr-template="waste-row-template"')); ?></div>
                                            <div class="icon-col div-table-col detail-col-detail <?php echo $obj->hideOnDisabled(); ?>"><?php echo $obj->inputLinkButton('btnDeleteRows', '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button', 'etc' =>  'tabIndex="-1" style="padding:6px 0; ' . $hideDeleteIcon . '"')); ?></div>
                                        </div>
                                    <?php  }   ?>

                                </div>
                                <div style="clear:both; height:1em;"></div>

                            </div>
                        </div>
                        <div class="div-tab-panel">
                            <div class="div-table-caption border-blue"><?php echo ucwords($obj->lang['PIC']); ?> &amp; <?php echo ucwords($obj->lang['correspondentInformation']); ?></div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['PIC']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputText('pic'); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['jobPosition']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputText('jobPosition'); ?>
                                </div>
                            </div>
                            <div class="form-group" style="margin-top:2em">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['name']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputText('correspondentName'); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['jobPosition']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputText('correspondentJobPosition'); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['address']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo  $obj->inputTextArea('correspondentAddress', array('etc' => 'style="height:10em;"')); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['phone']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputText('correspondentPhone'); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['email']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputText('correspondentEmail'); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div style="clear:both"></div>
            <div class="form-button-margin"></div>
            <div class="form-button-panel">
                <?php echo $obj->generateSaveButton(array(1,2), true);   ?>
            </div>
        </form>

        <?php echo $obj->showDataHistory(); ?>
    </div>
</body>

</html>
