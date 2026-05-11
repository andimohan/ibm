<?php

require_once '../../../_config.php';
require_once '../../../_include-v2.php';

includeClass(array('Service.class.php', 'Item.class.php', 'CityCategory.class.php'));
$service = createObjAndAddToCol(new Service(SERVICE));
$item = createObjAndAddToCol(new Item());
$itemUnit = createObjAndAddToCol(new ItemUnit());
$cityCategory = createObjAndAddToCol(new CityCategory());
$serviceCategory =  createObjAndAddToCol(new ServiceCategory());
$waste =  createObjAndAddToCol(new Waste());
$chartOfAccount =  createObjAndAddToCol(new ChartOfAccount());
    
$obj = $service;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
// some modules that have different security object from that of their class

if (!$security->isAdminLogin($securityObject, 10, true));
$overwriteContractAllowed = $security->isAdminLogin($service->overwriteContractSecurityObject, 10);

$formAction = 'serviceList';

$isQuickAdd = (isset($_GET) && !empty($_GET['quickadd'])) ? true : false;

$rs = prepareOnLoadData($obj);

$arrType = array();
$arrType[1] = $obj->lang['yearly'];
$arrType[2] = $obj->lang['monthly'];

$rsArea = $cityCategory->searchDataRow(
    array($cityCategory->tableName . '.pkey as citycategorykey', $cityCategory->tableName . '.name as citycategoryname'),
    ' and ' . $cityCategory->tableName . '.statuskey = 1'
);

$rsWaste = $waste->searchDataRow(
    array($waste->tableName . '.pkey as wastekey', $waste->tableName . '.name as wastename', $waste->tableName . '.code', $waste->tableName . '.categorykey'),
    ' and ' . $waste->tableName . '.statuskey = 1'
);
$rsWasteCategory = $waste->getWasteCategory();
$rsDetailAreaCol = array();
$rsDetailWasteCol = array();
$rsDetailWaste = array();
$rsItemDetail = array();
$rsAssetDetail = array();

if (!empty($_GET['id'])) {

    $id = $_GET['id'];

    $rsAssetDetail = $obj->getAssetGroupDetail($id);
    $rsItemDetail = $obj->getItemDetail($id);
    $rsDetailAreaCol = $obj->getDetailArea($id);
    $rsDetailAreaCol = array_column($rsDetailAreaCol, null, 'citycategorykey');
    $rsDetailWasteCol = $obj->getDetailWaste($id);
    $rsDetailWasteCol = $obj->reindexDetailCollections($rsDetailWasteCol,'refkey');

    $_POST['hidCategoryKey'] = $rs[0]['categorykey'];
    if (!empty($rs[0]['categorykey'])) {
        $rsCategory = $serviceCategory->getDataRowById($rs[0]['categorykey']);
        $categoryName =  $serviceCategory->getPath($rsCategory[0]['pkey']);
        $_POST['categoryName'] = $categoryName[0]['path'];
    }
    
		$_POST['hidRevenueCOAKey'] = $rs[0]['revenuecoakey']; 
		if (!empty($rs[0]['revenuecoakey'])){
			$rsCoa = $chartOfAccount->getDataRowById($rs[0]['revenuecoakey']);
			$_POST['revenueCOALink'] = $rsCoa[0]['code'] . ' - ' . $rsCoa[0]['name'];
		}

    $_POST['qtyService'] = $obj->formatNumber($rs[0]['qtyservice']);
    $_POST['duration'] = $obj->formatNumber($rs[0]['duration']);
    $_POST['maximumWeight'] = $obj->formatNumber($rs[0]['maximumweight'], 2);
    $_POST['driverCommission'] = $obj->formatNumber($rs[0]['drivercommission']);
}

$arrStatus = $obj->generateComboboxOpt(array('data' => $obj->getAllStatus(), 'label' => 'status'));
$arrDefaultUnit = $obj->convertForCombobox($itemUnit->searchData('', '', true, ' and (' . $itemUnit->tableName . '.statuskey = 1 )'), 'pkey', 'name');
$arrWasteCategory = $obj->generateComboboxOpt(array('data' => $rsWasteCategory));
$rsWasteCategory = array_column($rsWasteCategory,null, 'pkey');

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
            opt.year = "<?php echo $obj->lang['year']; ?>";
            opt.month = "<?php echo $obj->lang['month']; ?>";
            var service = new Service(tabID, opt, <?php echo json_encode(
                                                        array(
                                                            'assetGroupDetail' => $rsAssetDetail,
                                                            'itemDetail' => $rsItemDetail,
                                                            'wasteCategory' => $rsWasteCategory
                                                        )
                                                    ); ?>);
            prepareHandler(service);

            var fieldValidation = {
                code: {
                    validators: {
                        notEmpty: {
                            message: phpErrorMsg.code[1]
                        },
                    }
                },
                name: {
                    validators: {
                        notEmpty: {
                            message: phpErrorMsg.customer[1]
                        },
                    }
                },

                categoryName: {
                    validators: {
                        notEmpty: {
                            message: phpErrorMsg.category[1]
                        },
                    }
                },

            };


            setFormValidation(getTabObj(tabID), $('#defaultForm-' + tabID), fieldValidation, <?php echo json_encode($obj->validationFormSubmitParam()); ?>);

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
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['name']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo  $obj->inputText('name'); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['category']); ?></label>
                                <div class="col-xs-9">
                                    <?php
                                    echo $obj->inputAutoComplete(
                                        array(
                                            'objRefer' => $serviceCategory,
                                            'revalidateField' => true,
                                            'element' => array(
                                                'value' => 'categoryName',
                                                'key' => 'hidCategoryKey'
                                            ),
                                            'source' => array(
                                                'url' => 'ajax-service-category.php',
                                                'data' => array('action' => 'searchData', 'isleaf' => 1)
                                            ),
                                            'popupForm' => array(
                                                'url' => 'serviceCategoryForm.php',
                                                'element' => array(
                                                    'value' => 'categoryName',
                                                    'key' => 'hidCategoryKey'
                                                ),
                                                'width' => '600px',
                                                'title' => ucwords($obj->lang['add'] . ' - ' . $obj->lang['serviceCategory'])
                                            )
                                        )
                                    );
                                    ?>
                                </div>
                            </div>
                            <!-- <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['duration']); ?></label>
                                <div class="col-xs-9">
                                    <div class="flex">
                                        <div class="consume"> <?php echo $obj->inputNumber('duration'); ?></div>
                                        <div style="width: 4em"><?php echo $obj->lang['month']; ?></div>
                                    </div>
                                </div>
                            </div> -->
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['wasteCategory']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo  $obj->inputSelect('hidWasteCategoryKey', $arrWasteCategory); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['totalVisit']); ?> / <?php echo ucwords($obj->lang['duration']); ?></label>
                                <div class="col-xs-9">
                                    <div class="flex">
                                        <div class="consume"><?php echo  $obj->inputNumber('qtyService'); ?></div>
                                        <div style="padding-right:1em"><?php echo ucwords($obj->lang['visit']); ?></div>
                                        <div class="consume"><?php echo  $obj->inputNumber('duration'); ?></div>
                                        <div style="width: 4em"><?php echo $obj->lang['month']; ?></div>
                                    </div>
                                </div>
                            </div>
                            <?php if (!empty($overwriteContractAllowed)) { ?>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['salesmanCommission']); ?> 
                                    <div class="field-information"><?php echo ucwords($obj->lang['visit']) .' / '. ucwords($obj->lang['weight']); ?></div>
                                </label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputCheckBox('chkIsCommissionPerVisit'); ?>
                                </div>
                            </div>
                            
                                <div class="form-group">
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['salesmanCommission']); ?></label>
                                    <div class="col-xs-9">
                                        <div class="flex">
                                            <div class="consume"><?php echo  $obj->inputNumber('commissionPerVisit'); ?></div>
                                            <div style="padding-right:1em"> / <?php echo ucwords($obj->lang['visit']); ?></div>
                                            <div class="consume"><?php echo  $obj->inputNumber('firstEmployeeCommission'); ?></div>
                                            <div style="width: 4em"><?php echo ucwords($obj->lang['initialCost']); ?></div>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['driverCommission']); ?> / <?php echo ucwords($obj->lang['customer']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputNumber('driverCommission'); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['note']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo  $obj->inputTextArea('shortdescription', array('etc' => 'style="height:10em;"')); ?>
                                </div>
                            </div>
                           
                        </div>
                          <div class="div-tab-panel">
                                <div class="div-table-caption border-pink"><?php echo ucwords($obj->lang['finance']); ?></div>
                               <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['revenueAccount']); ?></label>
                                <div class="col-xs-9">
                                    <?php
                                    echo $obj->inputAutoComplete(
                                        array(
                                        'objRefer' => $chartOfAccount,
                                        'revalidateField' => false, 
                                        'element' => array('value' => 'revenueCOALink',
                                                           'key' => 'hidRevenueCOAKey'),
                                        'source' =>array(
                                                            'url' => 'ajax-coa.php',
                                                            'data' => array(  'action' =>'searchData' )
                                                        ) 
                                        )
                                    );
                                    ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['paymentUpfront']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputCheckBox('chkIsPrePaid'); ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="div-tab-panel">
                        <div class="div-table-caption border-red"><?php echo ucwords($obj->lang['asset']); ?></div>
                        <div class="div-table mnv-transaction transaction-detail asset-detail" style="width:100%; border-bottom:1px solid #333; ">
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
                    </div>
                    <div class="div-tab-panel">
                        <div class="div-table-caption border-blue"><?php echo ucwords($obj->lang['item']); ?></div>
                        <div class="div-table mnv-transaction transaction-detail item-detail" style="width:100%; border-bottom:1px solid #333; ">
                            <div class="div-table-row">
                                <div class="div-table-col detail-col-header"><?php echo ucwords($obj->lang['item']); ?></div>
                                <div class="div-table-col detail-col-header" style="width:80px; text-align:right;"><?php echo ucwords($obj->lang['qty']); ?></div>
                                <div class="div-table-col detail-col-header" style="width:80px;"><?php echo ucwords($obj->lang['unit']); ?></div>
                                <div class="div-table-col detail-col-header <?php echo $obj->hideOnDisabled(); ?> icon-col"></div>
                                <div class="div-table-col detail-col-header <?php echo $obj->hideOnDisabled(); ?> icon-col"></div>
                            </div>

                            <?php
                            $totalItemRows = count($rsItemDetail);


                            for ($i = 0; $i <= $totalItemRows; $i++) {

                                $class =  'transaction-detail-row';
                                $overwrite = true;
                                $etc = '';
                                $arrUnit = $arrDefaultUnit;

                                if ($i == $totalItemRows) {
                                    $class = 'item-row-template row-template';
                                    $overwrite = false;
                                    $etc = 'disabled="disabled"';
                                } else {
                                    $decimal = 0;

                                    $_POST['hidItemDetailKey[]'] =  $rsItemDetail[$i]['pkey'];
                                    $_POST['hidItemKey[]'] =  $rsItemDetail[$i]['itemkey'];
                                    $_POST['itemName[]'] =  $rsItemDetail[$i]['itemname'];
                                    $_POST['qty[]'] =   $obj->formatNumber($rsItemDetail[$i]['qty']);
                                    $_POST['priceInUnit[]'] =   $obj->formatNumber($rsItemDetail[$i]['priceinunit']);
                                    $_POST['selDiscountType[]'] =  $rsItemDetail[$i]['discounttype'];
                                    $_POST['discountValueInUnit[]'] =   $obj->formatNumber($rsItemDetail[$i]['discount'], $decimal);
                                    $_POST['detailSubtotal[]'] =   $obj->formatNumber($rsItemDetail[$i]['total']);
                                    $_POST['selUnit[]'] =  $rsItemDetail[$i]['unitkey'];

                                    $arrUnit = $obj->convertForCombobox($item->getAvailableUnit($rsItemDetail[$i]['itemkey']), 'conversionunitkey', 'unitname');
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
                    </div>
                </div>
            </div>

            <div class="mnv-checkbox-group">
                <div class="div-table mnv-transaction invoice-detail transaction-detail" style="width:100%; border-bottom:1px solid #333; " attr-level="0">
                    <div class="div-table-row">
                        <div class="div-table-col" style="padding:0">
                            <div class="div-table" style="width:100%">
                                <div class="div-table-row">
                                    <div class="div-table-col detail-col-header"><?php echo ucwords($obj->lang['costInformation']); ?> / <?php echo ucwords($obj->lang['cityCategory']); ?></div>
                                    <div class="div-table-col detail-col-header" style="width:200px; text-align:right;"><?php echo ucwords($obj->lang['sellingPrice']); ?></div>
                                    <div class="div-table-col detail-col-header" style="width:200px; text-align:right;"><?php echo ucwords($obj->lang['additional']); ?> / <?php echo ucwords($obj->lang['visit']); ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="div-table-col detail-col-header <?php echo $obj->hideOnDisabled(); ?>  icon-col"></div>
                    </div>

                    <?php

                    $totalRows = count($rsArea);

                    for ($i = 0; $i <= $totalRows; $i++) {

                        $class =  'transaction-detail-row';
                        $overwrite = true;
                        $readonly = true;
                        $disabled = false;
                        $sokey = '';
                        $showSO = '';
                        $showCost = 'display:none;';
                        $soDisable = false;

                        $rsServiceDetail = array();
                        $totalWasteRows = 0;
                        $optionRows = '';

                        if ($i == $totalRows) {
                            $class = 'detail-row-template row-template';
                            $overwrite = false;
                            $disabled = true;
                        } else {

                            $rsDetailArea =  $rsDetailAreaCol[$rsArea[$i]['citycategorykey']];
                            $rsDetailWaste =  $rsDetailWasteCol[$rsDetailArea['pkey']];
                            $rsDetailWaste = array_column($rsDetailWaste, null, 'wastekey');
//                            $_POST['hidDetailKey[]']  =  $rsDetailArea['pkey'];
                            $_POST['hidDetailKey[]'] =  $rsDetailArea['pkey'];
                            $_POST['cityCategoryName[]'] =  $rsArea[$i]['citycategoryname'];
                            $_POST['hidCityCategoryKey[]'] =  $rsArea[$i]['citycategorykey'];
                            $_POST['sellingPriceArea[]'] = $obj->formatNumber($rsDetailArea['sellingprice']);
                            $_POST['exceedSellingPriceArea[]'] = $obj->formatNumber($rsDetailArea['exceedsellingpricearea']);

                            $readonly = false;

                            $showSO = 'display:none;';
                            $showCost = '';
                        }

                    ?>

                        <div class="div-table-row <?php echo $class; ?>">
                            <div class="div-table-col detail-col-detail" style="padding:0">
                                <!-- <div style="background-color:#dedede; border-radius:0.5em; padding: 0.5em">-->
                                <div class="div-table" style="width:100%">
                                    <div class="div-table-row">
                                        <div class="div-table-col detail-col-detail">
                                            <?php echo $obj->inputHidden('hidDetailKey[]', array('overwritePost' => $overwrite, 'disabled' => $disabled)); ?>
                                            <?php echo $obj->inputHidden('hidCityCategoryKey[]', array('overwritePost' => $overwrite, 'disabled' => $disabled)); ?>
                                            <?php echo $obj->inputText('cityCategoryName[]', array('overwritePost' => $overwrite, 'disabled' => $disabled, 'readonly' => true)); ?>
                                        </div>
                                        <div class="div-table-col detail-col-detail" style="width:200px;"><?php echo $obj->inputNumber('sellingPriceArea[]', array('overwritePost' => $overwrite, 'disabled' => $disabled, 'etc' => 'style="text-align:right;" ')); ?></div>
                                        <div class="div-table-col detail-col-detail" style="width:200px;"><?php echo $obj->inputNumber('exceedSellingPriceArea[]', array('overwritePost' => $overwrite, 'disabled' => $disabled, 'etc' => 'style="text-align:right;" ')); ?></div>
                                    </div>
                                </div>
                                <div class="options-row" style="<?php echo $optionRows ?>">
                                    <div style="clear:both; height:1em"></div>
                                    <div class="div-table mnv-transaction transaction-detail" style="width: 100%;" attr-level="1" attr-group="hidDetailWasteKey">
                                        <div class="div-table-row">
                                            <div class="div-table-col detail-col-detail col-header no-border"><?php echo ucwords($obj->lang['waste']); ?></div>
                                            <div class="div-table-col detail-col-detail col-header no-border" style="width:200px; text-align:right"><?php echo ucwords($obj->lang['sellingPrice']); ?> / (Kg)</div>
                                            <div class="div-table-col detail-col-detail col-header no-border" style="width:200px; text-align:right"><?php echo ucwords($obj->lang['minWeight']); ?> </div>
                                            <div class="div-table-col detail-col-detail col-header no-border" style="width:200px; text-align:right"><?php echo ucwords($obj->lang['maxWeight']); ?> </div>
                                            <div class="div-table-col detail-col-detail col-header no-border" style="width:200px; text-align:right"><?php echo ucwords($obj->lang['salesmanCommission']); ?> </div>
                                        </div>

                                        <?php

                                        $totalWasteRows = count($rsWaste);
                                        for ($j = 0; $j <= $totalWasteRows; $j++) {

                                            $classDetail =  'transaction-detail-row';
                                            $overwriteDetail = true;
                                            $disabledDetail = false;

                                            if ($j == $totalWasteRows) {
                                                $classDetail = 'service-row-template row-template';
                                                $overwriteDetail = false;
                                                $disabledDetail = true;
                                            } else {

                                                $classDetail = 'service-detail-row ' . $classDetail;
                                                $wasteCategoryClass = 'medis-'.$rsWasteCategory[$rsWaste[$j]['categorykey']]['ismedis']. '-waste-detail-row ' ;
                                                $wasteKey = $rsWaste[$j]['wastekey'];
                                                $_POST['hidDetailWasteKey[]'] =  $rsDetailWaste[$wasteKey]['pkey'];
                                                $_POST['wasteName[]'] =  $rsWaste[$j]['code'].' - '.$rsWaste[$j]['wastename'];
                                                $_POST['hidWasteKey[]'] =  $rsWaste[$j]['wastekey'];
                                                $_POST['wasteSellingPrice[]'] = $obj->formatNumber($rsDetailWaste[$wasteKey]['sellingprice']);
                                                $_POST['minWeight[]'] = $obj->formatNumber($rsDetailWaste[$wasteKey]['minweight'], 2);
                                                $_POST['maxWeight[]'] = $obj->formatNumber($rsDetailWaste[$wasteKey]['maxweight'], 2);
                                                $_POST['salesCommission[]'] = $obj->formatNumber($rsDetailWaste[$wasteKey]['salescommission'], 2);
                                                $_POST['salesCommissionType[]'] = $rsDetailWaste[$wasteKey]['salescommissiontype'];
                                            }

                                        ?>
                                            <div class="div-table-row <?php echo $wasteCategoryClass; ?> <?php echo $classDetail; ?>">
                                                <div class="div-table-col-3" style="vertical-align:top">
                                                    <?php echo $obj->inputHidden('hidDetailWasteKey[]', array('overwritePost' => $overwriteDetail, 'disabled' => $disabledDetail)); ?>
                                                    <?php echo $obj->inputHidden('hidWasteKey[]', array('overwritePost' => $overwriteDetail, 'disabled' => $disabledDetail)); ?>
                                                    <?php echo $obj->inputText('wasteName[]', array('readonly' => true, 'overwritePost' => $overwriteDetail, 'add-class' => 'label-style', 'disabled' => $disabledDetail, 'etc' => 'style="text-align:left;" ')); ?>
                                                </div>
                                                <div class="div-table-col-3" style="vertical-align:top">
                                                    <?php echo $obj->inputNumber('wasteSellingPrice[]', array('overwritePost' => $overwriteDetail, 'class' => 'form-control inputnumber label-style',   'disabled' => $disabledDetail, 'etc' => ' style="text-align:right;"  ')); ?>
                                                </div>
                                                <div class="div-table-col-3" style="vertical-align:top">
                                                    <?php echo $obj->inputDecimal('minWeight[]', array('overwritePost' => $overwriteDetail, 'class' => 'form-control inputnumber label-style',   'disabled' => $disabledDetail, 'etc' => ' style="text-align:right;"  ')); ?>
                                                </div>
                                                <div class="div-table-col-3" style="vertical-align:top">
                                                    <?php echo $obj->inputDecimal('maxWeight[]', array('overwritePost' => $overwriteDetail, 'class' => 'form-control inputnumber label-style',   'disabled' => $disabledDetail, 'etc' => ' style="text-align:right;"  ')); ?>
                                                </div>
                                                <div class="div-table-col-3" style="vertical-align:top">
                                                    <div class="flex">
                                                        <div><?php echo $obj->inputSelect('salesCommissionType[]', $obj->arrDiscountType, array('overwritePost' => $overwriteDetail, 'class' => 'form-control label-style',   'disabled' => $disabledDetail)); ?> </div>
                                                        <div class="consume"> <?php echo $obj->inputNumber('salesCommission[]', array('overwritePost' => $overwriteDetail, 'class' => 'form-control inputnumber label-style', 'etc' => 'style="text-align:right;"', 'disabled' => $disabledDetail)); ?> </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                    <?php } ?>

                </div>
            </div>

            <div class="form-button-panel"> <?php echo $obj->generateSaveButton(); ?> </div>
        </form>

        <?php echo $obj->showDataHistory(); ?>
    </div>
</body>

</html>
