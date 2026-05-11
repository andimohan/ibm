<?php
require_once '../_config.php';
require_once '../_include-v2.php';

includeClass(array('AssetPurchase.class.php'));
$assetPurchase = createObjAndAddToCol(new AssetPurchase());
$paymentMethod = createObjAndAddToCol(new PaymentMethod()); 
$supplier = createObjAndAddToCol(new Supplier());
$termOfPayment = createObjAndAddToCol(new TermOfPayment());
$warehouse = createObjAndAddToCol(new Warehouse());
$assetCategory = createObjAndAddToCol(new AssetCategory());


$obj = $assetPurchase;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
// some modules that have different security object from that of their class

if (!$security->isAdminLogin($securityObject, 10, true));

$formAction = 'assetPurchaseList';

$isQuickAdd = (isset($_GET) && !empty($_GET['quickadd'])) ? true : false;

$editWarehouseInactiveCriteria = '';
$editTermOfPaymentInactiveCriteria = '';
$editPaymentMethodInactiveCriteria = '';

$rsPurchaseDetail = array();
$rsPaymentMethodDetail = array();

$_POST['trDate'] = date('d / m / Y');

$finalDiscDecimal = 0;
$finalDiscDecimalType = 'inputnumber';

$rs = prepareOnLoadData($obj);
$arrCategory = $obj->generateComboboxOpt(array('data' => $assetCategory->searchDataRow(array($assetCategory->tableName . '.pkey', $assetCategory->tableName . '.name')), 'label' => 'name'));

if (!empty($_GET['id'])) {
    $id = $_GET['id'];

    $rsPurchaseDetail = $obj->getDetailWithRelatedInformation($id);
    $rsPaymentMethodDetail = $obj->getPaymentMethodDetail($id);

    // $_POST['trDate'] = $obj->formatDBDate($rs[0]['trdate'], 'd / m / Y');
    $rsSupplier = $supplier->getDataRowById($rs[0]['supplierkey']);
    $_POST['supplierName'] = $rsSupplier[0]['name'];
    $_POST['hidSupplierKey'] = $rsSupplier[0]['pkey'];
    $_POST['trDesc'] = $rs[0]['trdesc'];
//
//    if ($rs[0]['finaldiscounttype']  == 2) {
//        $finalDiscDecimal = 2;
//        $finalDiscDecimalType = 'inputdecimal';
//    } 
//    $_POST['selFinalDiscountType'] = $rs[0]['finaldiscounttype'];
//    $_POST['finalDiscount'] = $obj->formatNumber($rs[0]['finaldiscount'], $finalDiscDecimal);
  
	$_POST['total'] = $obj->formatNumber($rs[0]['grandtotal']);
    $_POST['beforeTaxTotal'] =  $obj->formatNumber($rs[0]['beforetaxtotal']);

    $_POST['chkIncludeTax'] = $rs[0]['ispriceincludetax'];
    $_POST['chkIsFullReceive'] = $rs[0]['isfullreceive'];
    $_POST['taxPercentage'] = $obj->formatNumber($rs[0]['taxpercentage'], 2);
    $_POST['taxValue'] = $obj->formatNumber($rs[0]['taxvalue']);
    //$_POST['shipmentFee'] = $obj->formatNumber($rs[0]['shipmentfee']);
    $_POST['etcCost'] = $obj->formatNumber($rs[0]['etccost']);
    $_POST['selTermOfPaymentKey'] = $rs[0]['termofpaymentkey'];
    $_POST['balance'] =  $obj->formatNumber($rs[0]['balance']);
    $_POST['refInvoiceCode'] =  $rs[0]['refinvoicecode'];
    $_POST['totalPayment'] = $obj->formatNumber($rs[0]['totalpayment']);

    $editWarehouseInactiveCriteria = ' or ' . $warehouse->tableName . '.pkey = ' . $obj->oDbCon->paramString($rs[0]['warehousekey']);
    $editTermOfPaymentInactiveCriteria = ' or ' . $termOfPayment->tableName . '.pkey = ' . $obj->oDbCon->paramString($rs[0]['termofpaymentkey']);
    $editPaymentMethodInactiveCriteria = ' or ' . $paymentMethod->tableName . '.pkey in (select paymentkey from ' . $obj->tablePayment . ' where refkey = ' . $obj->oDbCon->paramString($rs[0]['pkey']) . ')';
}

$rsTOP = $termOfPayment->searchData('', '', true, ' and (' . $termOfPayment->tableName . '.statuskey = 1' . $editTermOfPaymentInactiveCriteria . ')', ' order by duedays asc');

$arrStatus = $obj->convertForCombobox($obj->getAllStatus(), 'pkey', 'status');
$arrWarehouse = $class->convertForCombobox($warehouse->searchData('', '', true, ' and (' . $warehouse->tableName . '.statuskey = 1' . $editWarehouseInactiveCriteria . ')'), 'pkey', 'name');
$arrTOP = $class->convertForCombobox($rsTOP, 'pkey', 'name');
$arrPaymentMethod = $obj->convertForCombobox($paymentMethod->searchData('', '', true, ' and (' . $paymentMethod->tableName . '.statuskey = 1' . $editPaymentMethodInactiveCriteria . ')'), 'pkey', 'name');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title></title>

    <script type="text/javascript">
        jQuery(document).ready(function() {

            var tabID = <?php echo ($isQuickAdd) ?  $_GET['tabID'] :  'selectedTab.newPanel[0].id';  ?>;
            var tablekey = <?php echo $obj->getTableKeyAndObj($obj->tableName, array('key'))['key']; ?>;


            var varConstant = { };
			
            var cashTOP = Array();

            <?php
            for ($i = 0; $i < count($rsTOP); $i++) {
                if ($rsTOP[$i]['duedays'] <> 0)
                    echo 'cashTOP.push(' . $rsTOP[$i]['pkey'] . ');' . chr(13);
            }
            ?>

            var assetPurchase = new AssetPurchase(tabID, cashTOP, tablekey, varConstant);
            prepareHandler(assetPurchase);
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

            <div class="div-table main-tab-table-2">
                <div class="div-table-row">
                    <div class="div-table-col">
                        <div class="div-tab-panel">
                            <div class="div-table-caption border-orange"><?php echo ucwords($obj->lang['generalInformation']); ?></div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['status']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo  $obj->inputSelect('selStatus', $arrStatus, array('disabled' => true)); ?>
                                </div>
                            </div>
							
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['warehouse']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputSelect('selWarehouseKey', $arrWarehouse); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['code']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputAutoCode('code'); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['date']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputDate('trDate'); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['supplier']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputAutoComplete(
                                        array( 
                                            'element' => array(
                                                'value' => 'supplierName',
                                                'key' => 'hidSupplierKey'
                                            ),
                                            'source' => array(
                                                'url' => 'ajax-supplier.php',
                                                'data' => array('action' => 'searchData')
                                            ), 
                                            'callbackFunction' => 'getTabObj().updateTOP()'
                                        )
                                    );
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="div-table-col">
                        <div class="div-tab-panel">
                            <div class="div-table-caption border-blue"><?php echo ucwords($obj->lang['note']); ?></div>
                            <div class="form-group">
                                <div class="col-xs-12">
                                    <?php echo  $obj->inputTextArea('trDesc', array('etc' => 'style="height:10em;"')); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="div-table mnv-transaction transaction-detail" style="width:100%; border-bottom:1px solid #333; ">
                <div class="div-table-row">
                    <div class="div-table-col detail-col-header"><?php echo ucwords($obj->lang['name']); ?></div>
                    <div class="div-table-col detail-col-header" style="width:150px; "><?php echo ucwords($obj->lang['category']); ?></div> 
                    <div class="div-table-col detail-col-header" style="width:150px; text-align:right;"><?php echo ucwords($obj->lang['price']); ?> @</div>
                    <div class="div-table-col detail-col-header" style="width:150px; text-align:right;"><?php echo ucwords($obj->lang['subtotal']); ?></div>
                    <div class="div-table-col detail-col-header <?php echo $obj->hideOnDisabled(); ?> icon-col"></div>
                </div>

                <?php
                $totalRows = count($rsPurchaseDetail);

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
//
//                        if ($rsPurchaseDetail[$i]['discounttype']  == 2) {
//                            $decimal = 2;
//                            $inputnumber = 'inputdecimal';
//                        }
//						
                        $_POST['hidDetailKey[]'] =  $rsPurchaseDetail[$i]['pkey'];
                        $_POST['detailName[]'] =  $rsPurchaseDetail[$i]['name']; 
                        $_POST['priceInUnit[]'] =   $obj->formatNumber($rsPurchaseDetail[$i]['priceinunit']);
                        $_POST['selCategoryKey[]'] =   $rsPurchaseDetail[$i]['categorykey'];
                        $_POST['detailSubtotal[]'] =   $obj->formatNumber($rsPurchaseDetail[$i]['subtotal']);
                    }

                ?>
                    <div class="div-table-row <?php echo $class; ?>">
                        <div class="div-table-col detail-col-detail"><?php echo $obj->inputText('detailName[]', array('overwritePost' => $overwrite, 'etc' =>  $etc)); ?><?php echo $obj->inputHidden('hidDetailKey[]', array('overwritePost' => $overwrite, 'etc' => $etc)); ?></div>
                        <div class="div-table-col detail-col-detail"><?php echo $obj->inputSelect('selCategoryKey[]', $arrCategory, array('overwritePost' => $overwrite, 'etc' =>  $etc)); ?></div>
                        <div class="div-table-col detail-col-detail"><?php echo $obj->inputNumber('priceInUnit[]', array('overwritePost' => $overwrite, 'etc' => 'style="text-align:right;"' . $etc)); ?></div>
                        <div class="div-table-col detail-col-detail"><?php echo $obj->inputNumber('detailSubtotal[]', array('overwritePost' => $overwrite, 'readonly' => true, 'etc' => 'style="text-align:right;" ' . $etc)); ?></div>
                        <div class="div-table-col detail-col-detail  <?php echo $obj->hideOnDisabled(); ?> icon-col"><?php echo $obj->inputLinkButton('btnDeleteRows', '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button', 'etc' => 'tabIndex="-1"')); ?></div>
                    </div>
                <?php  }   ?>

            </div>

            <div style="clear:both; height:1em;"></div>
            <div style="float:left; display:inline-block;"><?php echo $obj->inputButton('btnAddRows', $obj->lang['addRows'], array('class' => 'btn btn-primary btn-second-tone')); ?></div>

            <div>
                <div style="width:350px; float:right; ">
                    <div class="div-table" style="width:100%">
                        <div class="div-table-row  form-group">
                            <div class="div-table-col-3" style="text-align:right;">
                                <?php echo ucwords($obj->lang['payment']); ?>
                            </div>
                            <div class="div-table-col-3" style="width:180px;">
                                <?php echo  $obj->inputSelect('selTermOfPaymentKey', $arrTOP); ?>
                            </div>
                            <div class="div-table-col-3 <?php echo $obj->hideOnDisabled(); ?> icon-col"></div>
                        </div>
                    </div>

                    <div class="mnv-total-group mnv-payment-method cashTOP ">
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
                            <div class="div-table  transaction-detail" style="width: 100%">
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

                                    <div class="div-table-row form-group payment-detail-row <?php echo $class; ?>">
                                        <div class="div-table-col-3" style="text-align:right;">
                                            <?php echo $obj->inputHidden('hidDetailPaymentKey[]', array('overwritePost' => $overwrite,  'disabled' => $disabled)); ?>
                                            <?php echo  $obj->inputSelect('selPaymentMethod[]', $arrPaymentMethod, array('overwritePost' => $overwrite, 'disabled' => $disabled)); ?>
                                        </div>
                                        <div class="div-table-col-3" style="width:180px">
                                            <?php echo $obj->inputNumber('paymentMethodValue[]', array('overwritePost' => $overwrite, 'disabled' => $disabled, 'class' => 'form-control inputnumber mnv-detail-field', 'etc' => 'style="text-align:right;" ')); ?>
                                        </div>
                                        <div class="div-table-col-3 <?php echo $obj->hideOnDisabled(); ?> icon-col">
                                            <?php echo $obj->inputLinkButton('btnDeleteRows', '<i class="fas fa-times"></i>', array('etc' => 'tabIndex="-1"  attrhandler="getTabObj().calculateTotal()"', 'class' => 'btn btn-link remove-button')); ?>
                                        </div>
                                    </div>

                                <?php } ?>

                                <div class="div-table-row form-group ">
                                    <div class="div-table-col-3"></div>
                                    <div class="div-table-col-3">
                                        <div class="text-link-01 mnv-total-group-hide-detail" style="float:right; text-align:right;"><?php echo ucwords($obj->lang['hideDetail']); ?> </div>
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

                    <div class="div-table" style="width:100%; margin-top:1em">

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

                <div class="div-table" style="float:right; margin-right:4em">
                    <div class="div-table-row  form-group">
                        <div class="div-table-col-5" style="text-align:right;">
                            <?php echo ucwords($obj->lang['subtotal']); ?>
                        </div>
                        <div class="div-table-col-5" style="width:200px;">
                            <?php echo $obj->inputNumber('subtotal', array('readonly' => true, 'etc' => 'style="text-align:right;"')); ?>
                        </div>

                    </div>
<!--
                    <div class="div-table-row  form-group" >
                        <div class="div-table-col-5" style="text-align:right;">
                            <?php echo ucwords($obj->lang['discount']); ?>
                        </div>
                        <div class="div-table-col-5">
                            <div class="flex">
                                <div><?php echo $obj->inputSelect('selFinalDiscountType', $obj->arrDiscountType); ?> </div>
                                <div class="consume"> <?php echo $obj->inputNumber('finalDiscount', array('class' => 'form-control ' . $finalDiscDecimalType, 'etc' => 'style="text-align:right;" ')); ?> </div>
                            </div>
                        </div>
                    </div>
-->

                    <div class="div-table-row  form-group   form-detail-field">
                        <div class="div-table-col-5" style="text-align:right; padding-top:2em;">
                            <?php echo ucwords($obj->lang['beforeTax']); ?>
                        </div>
                        <div class="div-table-col-5" style="padding-top:2em;">
                            <?php echo $obj->inputNumber('beforeTaxTotal', array('readonly' => true,  'etc' => 'style="text-align:right;')); ?>
                        </div>

                    </div>

                    <div class="div-table-row  form-group">
                        <div class="div-table-col-5" style="text-align:right;">
                            <?php echo strtoupper($obj->lang['PPN']); ?> [Include]
                        </div>
                        <div class="div-table-col-5">
                            <div class="flex">
                                <div><?php echo $obj->inputCheckBox('chkIncludeTax'); ?></div>
                                <div class="percentage-col"><?php echo $obj->inputDecimal('taxPercentage', array('etc' => 'style="text-align:right;"')); ?></div>
                                <div>%</div>
                                <div class="consume"><?php echo $obj->inputNumber('taxValue', array('readonly' => true, 'etc' => 'style="text-align:right;"')); ?></div>
                            </div>
                        </div>
                    </div>
<!--

                    <div class="div-table-row  form-group   form-detail-field"  >
                        <div class="div-table-col-5" style="text-align:right;">
                            <?php echo ucwords($obj->lang['others']); ?>
                        </div>
                        <div class="div-table-col-5">
                            <?php echo $obj->inputNumber('etcCost', array('etc' => 'style="text-align:right;"')); ?>
                        </div>
                        <div class="div-table-col"> </div>
                    </div>
-->
                    <div class="div-table-row  form-group">
                        <div class="div-table-col-5" style="text-align:right;">
                            <?php echo ucwords($obj->lang['total']); ?>
                        </div>
                        <div class="div-table-col-5">
                            <?php echo $obj->inputNumber('total', array('readonly' => true, 'etc' => 'style="text-align:right;"')); ?>
                        </div>
                        <div class="div-table-col"> </div>
                    </div> 

                </div>
                <div style="clear:both"></div>
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