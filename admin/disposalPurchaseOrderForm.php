<?php

require_once '../_config.php';
require_once '../_include-v2.php';

includeClass('DisposalPurchaseOrder.class.php');
$disposalPurchaseOrder = createObjAndAddToCol(new DisposalPurchaseOrder());
$warehouse = createObjAndAddToCol(new Warehouse());
$supplier = createObjAndAddToCol(new Supplier());
$termOfPayment = createObjAndAddToCol(new TermOfPayment());
$paymentMethod = createObjAndAddToCol(new PaymentMethod());
$disposalWorkOrderdispatcher = createObjAndAddToCol(new DisposalWorkOrderdispatcher());
$disposalWorkOrder = createObjAndAddToCol(new DisposalWorkOrder());

$obj = $disposalPurchaseOrder;

$securityObject = $obj->securityObject;

if (!$security->isAdminLogin($securityObject, 10, true));

$formAction = 'disposalPurchaseOrderList';

$isQuickAdd = (isset($_GET) && !empty($_GET['quickadd'])) ? true : false;

$_POST['trDate'] = date('d / m / Y');

$rsWorkListDetail = array();
$rs = prepareOnLoadData($obj);
$rsItemFile = array();
$rsAssetGroup = array();
$rsItemDetail = array();
$rsTax = $obj->getTax();
$rsTax = array_column($rsTax, null, 'pkey');


$editWarehouseInactiveCriteria = '';
$editTermOfPaymentInactiveCriteria = '';
$editPaymentMethodInactiveCriteria = '';

if (!empty($_GET['id'])) {
    $id = $_GET['id'];

    $rsDetail = $obj->getDetailWithRelatedInformation($id);
    $rsPaymentMethodDetail = $obj->getPaymentMethodDetail($id);
    $rsDetailWO = $disposalWorkOrder->getInformationforPurchase($rs[0]['dispatchkey']);

    $_POST['beforeTaxTotal'] =  $obj->formatNumber($rs[0]['beforetaxtotal']);

    $_POST['trDate'] = $obj->formatDBDate($rs[0]['trdate'], 'd / m / Y');
    $_POST['totalWeight'] = $obj->formatNumber($rs[0]['totalweight'], 2);

    // $_POST['hidWorkListKey'] = $rs[0]['refkey'];
    // $_POST['JOCode'] = $rsJobOrder[0]['code'];
    // $_POST['hidCustomerKey'] = $rs[0]['customerkey'];
    // $_POST['workOrderCode'] = $rs[0]['workordercode'];
    // $_POST['workListCode'] = $rsWorkList[0]['code'];
    //$_POST['prorateWeight'] = $obj->formatNumber($rs[0]['prorateweight'], 2);

    if (!empty($rs[0]['supplierkey'])) {
        $rsSupplier = $supplier->getDataRowById($rs[0]['supplierkey']);
        $_POST['hidSupplierKey'] = $rsSupplier[0]['pkey'];
        $_POST['supplierName'] = $rsSupplier[0]['name'];
    }

    if (!empty($rs[0]['dispatchkey'])) {
        $rsDisposalWODispatcher = $disposalWorkOrderdispatcher->getDataRowById($rs[0]['dispatchkey']);
        $_POST['hidDispatcKey'] = $rsDisposalWODispatcher[0]['pkey'];
        $_POST['dispatchCode'] = $rsDisposalWODispatcher[0]['code'];
        $_POST['hidCurrentDispatchKey'] = $rsDisposalWODispatcher[0]['pkey'];
        $_POST['hidCurrentDispatchCode'] = $rsDisposalWODispatcher[0]['code'];
    }

    $_POST['selWarehouseKey'] = $rs[0]['warehousekey'];

    $editWarehouseInactiveCriteria = ' or ' . $warehouse->tableName . '.pkey = ' . $obj->oDbCon->paramString($rs[0]['warehousekey']);

    $rsItemFile = $obj->getFileDetail($id);
    $obj->prepareLoadedFile($id, array('file' => $rsItemFile));
}

$rsTOP = $termOfPayment->searchData('', '', true, ' and (' . $termOfPayment->tableName . '.statuskey = 1' . $editTermOfPaymentInactiveCriteria . ')', ' order by duedays asc');
$rsPaymentMethod =  $paymentMethod->searchData('', '', true, ' and (' . $paymentMethod->tableName . '.statuskey = 1' . $editPaymentMethodInactiveCriteria . ')');

$arrStatus = $obj->convertForCombobox($obj->getAllStatus(), 'pkey', 'status');
$arrTOP = $class->convertForCombobox($rsTOP, 'pkey', 'name');
$arrPaymentMethod = $obj->convertForCombobox($rsPaymentMethod, 'pkey', 'name');
$arrTax = $obj->convertForCombobox($rsTax, 'pkey', 'name');
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

            opt.fileFolder = "<?php echo $obj->uploadFileFolder; ?>";
            opt.fileUploaderTarget = "item-file-uploader";
            opt.arrFile = Array();

            <?php
            if (isset($id) && !empty($id)) {
                for ($i = 0; $i < count($rsItemFile); $i++) {
                    echo 'opt.arrFile.push("' . $rsItemFile[$i]['file'] . '"); ';
                }
            }
            ?>

            var cashTOP = Array();
            <?php
            for ($i = 0; $i < count($rsTOP); $i++) {
                if ($rsTOP[$i]['duedays'] <> 0)
                    echo 'cashTOP.push(' . $rsTOP[$i]['pkey'] . ');' . chr(13);
            }
            ?>


            var disposalPurchaseOrder = new DisposalPurchaseOrder(tabID, opt, cashTOP, <?php echo json_encode(
                                                                                            array(
                                                                                                'tax' => $rsTax
                                                                                            )
                                                                                        ); ?>);
            prepareHandler(disposalPurchaseOrder);

            var fieldValidation = {
                code: {
                    validators: {
                        notEmpty: {
                            message: phpErrorMsg.code[1]
                        },
                    }
                },
                supplierName: {
                    validators: {
                        notEmpty: {
                            message: phpErrorMsg.supplier[1]
                        },
                    }
                },
                dispatchCode: {
                    validators: {
                        notEmpty: {
                            message: phpErrorMsg.disposalWorkOrderDispatcher[1]
                        },
                    }
                }
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
            <?php echo $obj->inputHidden('hidCurrentDispatchKey'); ?>
            <?php echo $obj->inputHidden('hidCurrentDispatchCode'); ?>

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
                                <label class="col-xs-3 control-label"><?php echo $obj->lang['date']; ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputDate('trDate', array('allowedStatusForEdit' => array(1))); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['warehouse']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputSelect('selWarehouseKey', $arrWarehouse, array('allowedStatusForEdit' => array(1))); ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo $obj->lang['supplier']; ?></label>
                                <div class="col-xs-9">
                                    <?php
                                    echo $obj->inputAutoComplete(
                                        array(
                                            'revalidateField' => true,
                                            'element' => array(
                                                'value' => 'supplierName',
                                                'key' => 'hidSupplierKey'
                                            ),
                                            'source' => array(
                                                'url' => 'ajax-supplier.php',
                                                'data' => array('action' => 'searchData')
                                            ),
                                            'allowedStatusForEdit' => array(1)
                                        )
                                    );
                                    ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo $obj->lang['workOrderDispatcherCode']; ?></label>
                                <div class="col-xs-9">
                                    <?php
                                    echo $obj->inputAutoComplete(
                                        array(
                                            'revalidateField' => true,
                                            'element' => array(
                                                'value' => 'dispatchCode',
                                                'key' => 'hidDispatchKey'
                                            ),
                                            'source' => array(
                                                'url' => 'ajax-disposal-work-order-dispatcher.php',
                                                'data' => array('action' => 'searchData', 'statuskey' => '(2,3)')
                                            ),
                                            'allowedStatusForEdit' => array(1),
                                            'callbackFunction' => 'getTabObj().updateWorkOrderDispatchInformation(event, ui);'
                                        )
                                    );
                                    ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['invoiceReference']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputText('refInvoiceCode'); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['totalWeight']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputDecimal('totalWeight', array('readonly' => true)); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['note']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo  $obj->inputTextArea('trDesc', array('etc' => 'style="height:10em;"')); ?>
                                </div>
                            </div>
                        </div>
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
                    </div>
                    <div class="div-table-col">
                        <div class="div-tab-panel">
                            <div class="div-table-caption border-green"><?php echo ucwords($obj->lang['pickUpList']); ?></div>
                            <div class="div-table dispatcher-detail transaction-detail" style="width:100%">
                                <div class="div-table-row">
                                    <div class="div-table-col-5" style="border-top:1px solid #666;border-bottom:1px solid #666; width:100px;text-align:left;">
                                        <strong><?php echo ucwords($obj->lang['WOCode']); ?></strong>
                                    </div>
                                    <div class="div-table-col-5" style="border-top:1px solid #666;border-bottom:1px solid #666; width:100px;text-align:left;">
                                        <strong><?php echo ucwords($obj->lang['waste']); ?></strong>
                                    </div>
                                    <div class="div-table-col-5" style="border-top:1px solid #666;border-bottom:1px solid #666;text-align:left">
                                        <strong><?php echo ucwords($obj->lang['customer']); ?></strong>
                                    </div>
                                    <div class="div-table-col-5" style="border-top:1px solid #666;border-bottom:1px solid #666;  width:70px;text-align:right">
                                        <strong><?php echo ucwords($obj->lang['weight']); ?> (Kg)</strong>
                                    </div>

                                </div>
                                <?php

                                $totalRows = count($rsDetailWO);
                                $totalDispatchWeight = 0;
                                for ($j = 0; $j <= $totalRows; $j++) {
                                    $class =  'transaction-detail-row work-order-row';
                                    $overwrite = true;
                                    $disabled = false;
                                    $display = '';
                                    $waste = '';
                                    $customerName = '';
                                    $customerWeight = '';
                                    $WOCode = '';

                                    if ($j == $totalRows) {
                                        $class = 'work-order-template';
                                        $overwrite = false;
                                        $disabled = true;
                                        $display = 'style="display:none"';
                                    } else {
                                        $waste = $rsDetailWO[$j]['waste'];
                                        $customerName = $rsDetailWO[$j]['customername'];
                                        $WOCode = $rsDetailWO[$j]['workordercode'];
                                        $customerWeight = $obj->formatNumber($rsDetailWO[$j]['customerweight'], 2);
                                        $totalDispatchWeight += $obj->formatNumber($rsDetailWO[$j]['customerweight'], 2);
                                    }

                                ?>
                                    <div class="div-table-row  <?php echo $class; ?>" <?php echo $display; ?>>
                                        <div class="div-table-col-5 detail-col-detail wo-code" style="border-bottom:1px solid #dedede; text-align:left; vertical-align:top"><?php echo $WOCode; ?></div>
                                        <div class="div-table-col-5 detail-col-detail waste" style="border-bottom:1px solid #dedede; text-align:left;vertical-align:top"> <?php echo $waste; ?></div>
                                        <div class="div-table-col-5 detail-col-detail customer-name" style="border-bottom:1px solid #dedede; text-align:left; vertical-align:top"><?php echo $customerName; ?></div>
                                        <div class="div-table-col-5 detail-col-detail customer-weight" style="border-bottom:1px solid #dedede; text-align:right;vertical-align:top"><?php echo $customerWeight; ?> </div>
                                    </div>
                                <?php  } ?>
                                
                                    <div class="div-table-row">
                                        <div class="div-table-col-5 detail-col-detail wo-code" style="border-top:1px solid #666;"></div>
                                        <div class="div-table-col-5 detail-col-detail waste" style="border-top:1px solid #666;"></div>
                                        <div class="div-table-col-5 detail-col-detail customer-name" style="border-top:1px solid #666; text-align:right; font-weight:bold"><?php echo $obj->lang['total']; ?></div>
                                        <div class="div-table-col-5 detail-col-detail total-dispatch-weight" style="border-top:1px solid #666; text-align:right;"> <?php echo $obj->formatNumber($totalDispatchWeight, 2); ?> </div>
                                    </div> 
                            </div> 
                        </div>
                    </div>
                </div>
            </div>

            <div style="clear:both; height:2em;"></div>
            <div class="div-table transaction-detail" style="width:100%; ">
                <div class="div-table-row">
                    <div class="div-table-col detail-col-header"><?php echo ucwords($obj->lang['waste']); ?></div>
                    <div class="div-table-col detail-col-header" style="width:120px; text-align:right;"><?php echo ucwords($obj->lang['weight']); ?> (Kg)</div>
                    <div class="div-table-col detail-col-header" style="width:150px; text-align:right;"><?php echo ucwords($obj->lang['price']); ?> / Kg</div>
                    <div class="div-table-col detail-col-header" style="width:120px; text-align:right"><?php echo ucwords($obj->lang['subtotal']); ?></div>
                    <div class="div-table-col detail-col-header" style="width:70px; text-align:right"><?php echo ucwords($obj->lang['PPN']); ?> %</div>
                    <div class="div-table-col detail-col-header" style="width:30px; text-align:center">Inc.</div>
                    <div class="div-table-col detail-col-header" style="width:120px; text-align:right"><?php echo ucwords($obj->lang['total']); ?></div>
                    <div class="div-table-col detail-col-header icon-col <?php echo $obj->hideOnDisabled(); ?>"></div>
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
                        $_POST['hidWasteKey[]'] =  $rsDetail[$i]['wastekey'];
                        $_POST['waste[]'] =  $rsDetail[$i]['waste'];
                        $_POST['weightDetail[]'] = $obj->formatNumber($rsDetail[$i]['weightdetail'], 2);
                        $_POST['priceInUnit[]'] = $obj->formatNumber($rsDetail[$i]['priceinunit']);
                        $_POST['subTotalDetailBeforeTax[]'] = $obj->formatNumber($rsDetail[$i]['subtotaldetailbeforetax']);
                        $_POST['taxpercentage[]'] = $obj->formatNumber($rsDetail[$i]['taxpercentage'], 2);
                        $_POST['chkIncludeTaxDetail[]'] =  $rsDetail[$i]['ispriceincludetax'];
                        $_POST['taxDetailKey[]'] =  $rsDetail[$i]['taxkey'];
                        $_POST['subTotalDetail[]'] = $obj->formatNumber($rsDetail[$i]['total']);
                        $_POST['taxValueDetail[]'] = $obj->formatNumber($rsDetail[$i]['taxvaluedetail']);
                    }
                ?>

                    <div class="div-table-row <?php echo $class; ?>">
                        <div class="div-table-col detail-col-detail" style="padding:0">
                            <div class="div-table" style="width:100%">
                                <div class="div-table-row">
                                    <div class="div-table-col detail-col-detail">
                                        <?php echo $obj->inputText('waste[]', array('overwritePost' => $overwrite, 'etc' => $etc)); ?>
                                        <?php echo $obj->inputHidden('hidDetailKey[]', array('overwritePost' => $overwrite, 'etc' => $etc)); ?>
                                        <?php echo $obj->inputHidden('hidWasteKey[]', array('overwritePost' => $overwrite, 'etc' => $etc)); ?>
                                    </div>
                                    <div class="div-table-col detail-col-detail" style="width:120px;">
                                        <?php echo $obj->inputDecimal('weightDetail[]', array('overwritePost' => $overwrite, 'etc' => 'style="text-align:right;" ')); ?>
                                    </div>
                                    <div class="div-table-col detail-col-detail" style="width:150px;">
                                        <?php echo $obj->inputDecimal('priceInUnit[]', array('overwritePost' => $overwrite, 'etc' => 'style="text-align:right;" ')); ?>
                                    </div>
                                    <div class="div-table-col detail-col-detail" style="width:120px;">
                                        <?php echo $obj->inputDecimal('subTotalDetailBeforeTax[]', array('overwritePost' => $overwrite, 'etc' => 'style="text-align:right;" ')); ?>
                                    </div>
                                    <div class="div-table-col detail-col-detail" style="width:70px;">
                                        <?php echo $obj->inputSelect('taxDetailKey[]', $arrTax, array('overwritePost' => $overwrite)); ?>
                                    </div>
                                    <div class="div-table-col detail-col-detail" style="width:30px;text-align:center">
                                        <?php echo $obj->inputCheckBox('chkIncludeTaxDetail[]', array('overwritePost' => $overwrite)); ?>
                                        <?php echo $obj->inputHidden('taxValueDetail[]', array('overwritePost' => $overwrite)); ?>
                                        <?php echo $obj->inputHidden('beforeTaxDetail[]', array('overwritePost' => $overwrite)); ?>
                                    </div>
                                    <div class="div-table-col detail-col-detail" style="width:120px;">
                                        <?php echo $obj->inputDecimal('subTotalDetail[]', array('overwritePost' => $overwrite, 'etc' => 'style="text-align:right;" ')); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="div-table-col detail-col-detail icon-col align-top-adjust  <?php echo $obj->hideOnDisabled(); ?>">
                            <?php echo $obj->inputLinkButton('btnDeleteRows', '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button', 'etc' => 'tabIndex="- 1"')); ?>
                        </div>
                    </div>
                <?php }   ?>

            </div>

            <div style="clear:both; height:1em;"></div>
            <div style="float:left; display:inline-block;"><?php echo $obj->inputButton('btnAddRows', $obj->lang['addRows'], array('class' => 'btn btn-primary btn-second-tone')); ?></div>

            <div>
                <div style="width:350px; margin-left:2em; float:right;">
                    <!-- <div class="<?php echo $obj->hideOnDisabled(); ?>" style="float:right; width:50px; height: 1em"></div>-->
                    <div class="div-table" style="width:100%">
                        <div class="div-table-row  form-group">
                            <div class="div-table-col-3" style="text-align:right;">
                                <?php echo ucwords($obj->lang['payment']); ?>
                            </div>
                            <div class="div-table-col-3" style="width:180px;">
                                <?php echo  $obj->inputSelect('selTermOfPayment', $arrTOP); ?>
                            </div>
                            <div class="div-table-col-3 <?php echo $obj->hideOnDisabled(); ?> icon-col"></div>
                        </div>
                    </div>
                    <div class="mnv-total-group mnv-payment-method cashTOP">
                        <div class="div-table" style="width: 100%">
                            <div class="div-table-row  form-group">
                                <div class="div-table-col-3" style="text-align:right;">
                                    <?php echo $obj->lang['totalPayment']; ?>
                                </div>
                                <div class="div-table-col-3" style="width:180px">
                                    <?php echo $obj->inputCollapsibleNumber('totalPayment', array('readonly' => true, 'etc' => 'style="text-align:right;" ')); ?>
                                </div>
                                <div class="div-table-col-3  <?php echo $obj->hideOnDisabled(); ?> icon-col"></div>
                            </div>
                        </div>

                        <div class="mnv-total-group-detail">
                            <div class="div-table transaction-detail" style="width: 100%">
                                <?php

                                $totalRows = count($rsPaymentMethodDetail);
                                for ($i = 0; $i <= $totalRows; $i++) {
                                    $class =  'transaction-detail-row';
                                    $overwrite = true;
                                    $disabled = false;

                                    if ($i == $totalRows) {
                                        $class = 'payment-method-row-template row-template';
                                        $overwrite = false;
                                        $disabled = true;
                                    } else {
                                        $_POST['hidDetailPaymentKey[]'] = $rsPaymentMethodDetail[$i]['pkey'];
                                        $_POST['selPaymentMethod[]'] = $rsPaymentMethodDetail[$i]['paymentkey'];
                                        $_POST['paymentMethodValue[]'] = $obj->formatNumber($rsPaymentMethodDetail[$i]['amount']);
                                    }
                                ?>

                                    <div class="div-table-row form-group <?php echo $class; ?>">
                                        <div class="div-table-col-3" style="text-align:right;">
                                            <?php echo $obj->inputHidden('hidDetailPaymentKey[]', array('overwritePost' => $overwrite,  'disabled' => $disabled)); ?>
                                            <?php echo  $obj->inputSelect('selPaymentMethod[]', $arrPaymentMethod, array('overwritePost' => $overwrite, 'disabled' => $disabled)); ?>
                                        </div>
                                        <div class="div-table-col-3" style="width:180px">
                                            <?php echo $obj->inputNumber('paymentMethodValue[]', array('overwritePost' => $overwrite, 'disabled' => $disabled, 'class' => 'form-control inputnumber mnv-detail-field', 'etc' => 'style="text-align:right;"')); ?>
                                        </div>
                                        <div class="div-table-col-3 <?php echo $obj->hideOnDisabled(); ?> icon-col">
                                            <?php echo $obj->inputLinkButton('btnDeleteRows', '<i class="fas fa-times"></i>', array('etc' => 'tabIndex="-1"', 'class' => 'btn btn-link remove-button')); ?>
                                        </div>
                                    </div>

                                <?php } ?>

                                <div class="div-table-row form-group ">
                                    <div class="div-table-col-3"></div>
                                    <div class="div-table-col-3">
                                        <div class="form-detail-button mnv-total-group-hide-detail" style="float:right; text-align:right;"><?php echo ucwords($obj->lang['hideDetail']); ?> </div>
                                    </div>
                                    <div class="div-table-col-3 <?php echo $obj->hideOnDisabled(); ?>"></div>
                                </div>
                                <div class="div-table-row form-group ">
                                    <div class="div-table-col-3 " style="height:1em"></div>
                                    <div class="div-table-col-3 "></div>
                                    <div class="div-table-col-3  <?php echo $obj->hideOnDisabled(); ?> "></div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="div-table" style="width:100%;">
                        <div class="div-table-row  form-group">
                            <div class="div-table-col-3" style="text-align:right;">
                                <?php echo ucwords($obj->lang['balance']); ?>
                            </div>
                            <div class="div-table-col-3" style="width:180px;">
                                <?php echo $obj->inputNumber('balance', array('readonly' => true, 'etc' => 'style="text-align:right;"')); ?>
                            </div>
                            <div class="div-table-col-3 <?php echo $obj->hideOnDisabled(); ?> icon-col"></div>
                        </div>
                    </div>

                </div>
                <div class="div-table" style="float:right;">
                    <div class="div-table-row  form-group not-downpayment-field" style="<?php echo $notDownpaymentField; ?>">
                        <div class="div-table-col-5" style="text-align:right;">
                            <?php echo ucwords($obj->lang['subtotal']); ?>
                        </div>
                        <div class="div-table-col-5" style="width:200px;">
                            <?php echo $obj->inputNumber('subtotal', array('readonly' => true, 'etc' => 'style="text-align:right;"')); ?>
                        </div>

                    </div>
                    <div class="div-table-row  form-group not-downpayment-field" style="<?php echo $notDownpaymentField; ?>">
                        <div class="div-table-col-5"></div>
                        <div class="div-table-col-5"></div>
                    </div>


                    <div class="div-table-row  form-group">
                        <div class="div-table-col-5" style="text-align:right;">
                            <?php echo ucwords($obj->lang['beforeTax']); ?>
                        </div>
                        <div class="div-table-col-5" style="width:200px;">
                            <?php echo $obj->inputNumber('beforeTaxTotal', array('readonly' => true, 'etc' => 'style="text-align:right;"')); ?>
                        </div>

                    </div>

                    <div class="div-table-row  form-group">
                        <div class="div-table-col-5" style="text-align:right;">
                            <?php echo strtoupper($obj->lang['PPN']); ?>
                        </div>
                        <div class="div-table-col-5" style="width:200px;">
                            <?php echo $obj->inputNumber('taxValue', array('readonly' => true, 'etc' => 'style="text-align:right;"')); ?>
                        </div>

                    </div>
                    <div class="div-table-row  form-group">
                        <div class="div-table-col-5" style="text-align:right;">
                            <?php echo ucwords($obj->lang['total']); ?>
                        </div>
                        <div class="div-table-col-5">
                            <?php echo  $obj->inputNumber('total', array('readonly' => true, 'etc' => 'style="text-align:right"')); ?>
                        </div>
                    </div>

                </div>

            </div>

            <div class="form-button-margin"></div>
            <div class="form-button-panel">
                <?php echo $obj->generateSaveButton(array(), true);   ?>
            </div>

        </form>

        <?php echo $obj->showDataHistory(); ?>

    </div>
</body>

</html>
