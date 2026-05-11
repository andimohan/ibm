<?php
require_once '../_config.php';
require_once '../_include-v2.php';

includeClass('TruckingPurchase.class.php');
$truckingPurchase = createObjAndAddToCol(new TruckingPurchase());
$termOfPayment = createObjAndAddToCol(new TermOfPayment());
$truckingServiceOrder = createObjAndAddToCol(new TruckingServiceOrder());
$supplier = createObjAndAddToCol(new Supplier());
$item = createObjAndAddToCol(new Item());
$warehouse = createObjAndAddToCol(new Warehouse());
$truckingServiceWorkOrder = createObjAndAddToCol(new TruckingServiceWorkOrder());
$cashBank =  createObjAndAddToCol(new CashBank());
$paymentMethod = createObjAndAddToCol(new PaymentMethod());

$obj = $truckingPurchase;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
// some modules that have different security object from that of their class

if (!$security->isAdminLogin($securityObject, 10, true));

$formAction = 'truckingPurchaseList';

$isQuickAdd = (isset($_GET) && !empty($_GET['quickadd'])) ? true : false;

$usePPNDetail = $obj->loadSetting('usePurchasePPNDetail');
$usePPNDetail = ($usePPNDetail == 1) ? true : false;

$editWarehouseInactiveCriteria = '';
$editTermOfPaymentInactiveCriteria = '';
$editPaymentMethodInactiveCriteria = '';

$rsPurchaseDetail = array();
$rsSODetail = array();
$rsPaymentMethodDetail = array();
$rsDP = array();

$_POST['trDate'] = date('d / m / Y');

$finalDiscDecimal = 0;
$finalDiscDecimalType = 'inputnumber';

$rs = prepareOnLoadData($obj);

$rsPurchaseRequestType = $obj->getTableKeyAndObj($purchaseRequest->tableName, array('key'));

if (!empty($_GET['id'])) {
    $id = $_GET['id'];

    $rsPurchaseDetail = $obj->getDetailWithRelatedInformation($id); 
//    $rsDP = $obj->getDownpaymentDetail($id);  
        
    $rsSODetail = $obj->getDetailJobOrder($id);
	
	if(ADV_FINANCE && TEST_VOUCHER){ 
		$rsPaymentMethodDetail = $obj->getPaymentVoucherDetail($id);  
		$arrAvailableVoucher = $class->convertForCombobox($rsPaymentMethodDetail,'cashbankvoucherkey','voucherlabel');  

		$existingVoucherKey = array_column($rsPaymentMethodDetail,'cashbankvoucherkey');
		$otherVoucher = $cashBank->getAvailableVoucher($rs[0]['supplierkey'],' and '.$cashBank->tableName.'.pkey not in ('.$obj->oDbCon->paramString($existingVoucherKey,',').')',false,true);
		foreach($otherVoucher as $voucherItem){ 
			$arrAvailableVoucher[$voucherItem['pkey']]['label'] = $voucherItem['voucherlabel'];
			$arrAvailableVoucher[$voucherItem['pkey']]['rel'] = array('rel-amount' => $voucherItem['outstanding']); 
		}  
	}else{ 
		$rsPaymentMethodDetail = $obj->getPaymentMethodDetail($id); 
	}
 
    if ($rs[0]['finaldiscounttype']  == 2) {
        $finalDiscDecimal = 2;
        $finalDiscDecimalType = 'inputdecimal';
    }
 
    $rsSupplier = $supplier->searchDataRow(array($supplier->tableName.'.name'), ' and ' .$supplier->tableName.'.pkey = '. $obj->oDbCon->paramString($rs[0]['supplierkey']));
    $_POST['supplierName'] = $rsSupplier[0]['name'];
                                     
//    if ($rs[0]['reftabletype'] == $rsPurchaseRequestType['key']) {
//        // PURCHASE REQUEST
//        $rsPurchaseRequest = $purchaseRequest->searchData($purchaseRequest->tableName . '.pkey', $rs[0]['refkey']);
//        $_POST['hidPurchaseRequestKey'] = $rs[0]['refkey'];
//        $_POST['purchaseRequestCode'] = $rsPurchaseRequest[0]['code'];
//    }
 

    $editWarehouseInactiveCriteria = ' or ' . $warehouse->tableName . '.pkey = ' . $obj->oDbCon->paramString($rs[0]['warehousekey']);
    $editTermOfPaymentInactiveCriteria = ' or ' . $termOfPayment->tableName . '.pkey = ' . $obj->oDbCon->paramString($rs[0]['termofpaymentkey']);
    $editPaymentMethodInactiveCriteria = ' or ' . $paymentMethod->tableName . '.pkey in (select paymentkey from ' . $obj->tablePayment . ' where refkey = ' . $obj->oDbCon->paramString($rs[0]['pkey']) . ')';
}

$rsTOP = $termOfPayment->searchData('', '', true, ' and (' . $termOfPayment->tableName . '.statuskey = 1' . $editTermOfPaymentInactiveCriteria . ')', ' order by duedays asc');
$arrStatus = $obj->convertForCombobox($obj->getAllStatus(), 'pkey', 'status');
$arrWarehouse = $class->convertForCombobox($warehouse->searchData('', '', true, ' and (' . $warehouse->tableName . '.statuskey = 1' . $editWarehouseInactiveCriteria . ')'), 'pkey', 'name');
$arrTOP = $class->convertForCombobox($rsTOP, 'pkey', 'name');
$arrPaymentMethod = $obj->convertForCombobox($paymentMethod->searchData('', '', true, ' and (' . $paymentMethod->tableName . '.statuskey = 1' . $editPaymentMethodInactiveCriteria . ')'), 'pkey', 'name');


//$arrType = array();
//$arrType[$rsPurchaseRequestType['key']] = 'Request';
//
//if ($obj->isActiveModule('SalesOrderCarService')) {
//    $salesOrderCarService = createObjAndAddToCol(new SalesOrderCarService());
//    $rsServiceType = $obj->getTableKeyAndObj($salesOrderCarService->tableName, array('key'));
//    $arrType[$rsServiceType['key']] = 'Services';
//
//    if (!empty($rs[0]['pkey'])) {
//        // SERVICE
//        $rsSOCarService = $salesOrderCarService->searchData($salesOrderCarService->tableName . '.pkey', $rs[0]['refservicekey']);
//        $_POST['hidJobHeaderKey'] = $rs[0]['refservicekey'];
//        $_POST['serviceCode'] = $rsSOCarService[0]['code'];
//    }
//}

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

            var varConstant = {
                usePPNDetail: <?php echo json_encode($usePPNDetail); ?>,
                tablekey: tablekey,
				ADV_FINANCE : <?php echo (ADV_FINANCE) ? "true" : "false"; ?>
            };

            var cashTOP = Array();

            <?php
            for ($i = 0; $i < count($rsTOP); $i++) {
                if ($rsTOP[$i]['duedays'] <> 0)
                    echo 'cashTOP.push(' . $rsTOP[$i]['pkey'] . ');' . chr(13);
            }
            ?>

            var truckingPurchase = new TruckingPurchase(tabID, cashTOP, <?php echo json_encode(
                                                                            array(
                                                                                'rsSODetail' => $rsSODetail
                                                                            )
                                                                        ); ?>, varConstant);
            prepareHandler(truckingPurchase);
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
                refInvoiceCode: {
                    validators: {
                        notEmpty: {
                            message: phpErrorMsg.invoice[1]
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
            <?php echo $obj->inputHidden('hidCurrentSupplierKey'); ?>
            <?php echo $obj->inputHidden('hidCurrentSupplierName'); ?>
            <?php echo $obj->inputHidden('hidTotalBeforeTaxPPH23'); ?>

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
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['warehouse']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputSelect('selWarehouseKey', $arrWarehouse); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['supplier']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputAutoComplete(
                                        array(
                                            'objRefer' => $supplier,
                                            'revalidateField' => true,
                                            'element' => array(
                                                'value' => 'supplierName',
                                                'key' => 'hidSupplierKey'
                                            ),
                                            'source' => array(
                                                'url' => 'ajax-supplier.php',
                                                'data' => array('action' => 'searchData')
                                            ),
                                            'callbackFunction' => 'getTabObj().updateSupplierInformation(this,event, ui)'

                                        )
                                    );
                                    ?>
                                </div>
                            </div>
                            <!-- detail job order -->
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['jobOrder']); ?></label>
                                <div class="col-xs-9">

                                    <div class="div-table mnv-transaction transaction-detail" style="width:100%">
                                        <?php
                                        $totalRows = count($rsSODetail);
                                        for ($i = 0; $i <= $totalRows; $i++) {

                                            $class =  'transaction-detail-row';
                                            $overwrite = true;
                                            $readonly = false;
                                            $disabled = false;

                                            if ($i == $totalRows) {
                                                $class = 'job-order-row-template row-template';
                                                $overwrite = false;
                                                $disabled = true;
                                                $isLocked = false;
                                            } else {
                                                $_POST['hidDetailJobOrderKey[]'] =  $rsSODetail[$i]['pkey'];
                                                $_POST['SOCode[]'] =  $rsSODetail[$i]['socode'];
                                                $_POST['hidSOKey[]'] =  $rsSODetail[$i]['sokey'];
                                            }

                                        ?>
                                            <div class="div-table-row odd-style-adjustment <?php echo $class; ?> ">
                                                <div class="div-table-col" style="padding-left:0;">
                                                    <div class="flex">
                                                        <div class="consume" style="width:270px">
                                                            <?php echo $obj->inputText('SOCode[]', array('overwritePost' => $overwrite, 'disabled' => $disabled)); ?>
                                                            <?php echo $obj->inputHidden('hidSOKey[]', array('overwritePost' => $overwrite, 'disabled' => $disabled)); ?>
                                                            <?php echo $obj->inputHidden('hidDetailJobOrderKey[]', array('overwritePost' => $overwrite, 'disabled' => $disabled)); ?>
                                                        </div>
                                                        <div class="icon-col <?php echo $obj->hideOnDisabled(); ?>"><?php echo $obj->inputLinkButton('btnAddDetailRow', '<i class="fas fa-plus-circle"></i>', array('class' => 'btn btn-link add-row-button', 'etc' => 'attr-template="job-order-row-template"')); ?></div>
                                                        <div class="icon-col <?php echo $obj->hideOnDisabled(); ?>"><?php echo $obj->inputLinkButton('btnDeleteRows', '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button', 'etc' =>  'tabIndex="-1" style="padding:6px 0;  "')); ?></div>

                                                    </div>
                                                </div>
                                            </div>
                                        <?php }     ?>

                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['invoiceReference']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputText('refInvoiceCode'); ?>
                                </div>
                            </div>
                            <div class="form-group <?php echo $obj->hideOnDisabled(); ?>">
                                <label class="col-xs-3 control-label"></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputButton('btnImport', $obj->lang['showAll'], array('class' => 'btn btn-primary btn-second-tone')); ?>
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

            <div class="mnv-checkbox-group">
                <div class="div-table mnv-transaction invoice-detail transaction-detail" style="width:100%; border-bottom:1px solid #333; " attr-level="0">
                    <div class="div-table-row">
                        <div class="div-table-col" style="padding:0">
                            <div class="div-table" style="width:100%">
                                <div class="div-table-row">
                                    <div class="div-table-col detail-col-header"  style="width:200px;">
                                        <?php echo ucwords($obj->lang['WOCode']); ?> 
                                    </div> 
                                    <div class="div-table-col detail-col-header" style="width:110px;  text-align:center;">
                                       <?php echo ucwords($obj->lang['serviceWorkOrderDate']); ?>
                                    </div> 
                                    <div class="div-table-col detail-col-header">
                                       <?php echo ucwords($obj->lang['jobOrderCode']); ?>
                                    </div>
                                    <div class="div-table-col detail-col-header" style="width:110px; text-align:right;">
                                        <?php echo ucwords($obj->lang['subtotal']); ?>
                                    </div> 
                                    <div class="div-table-col detail-col-header" style="width:110px; text-align:right;">
                                        <?php echo ucwords($obj->lang['total']); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="div-table-col detail-col-header <?php echo $obj->hideOnDisabled(); ?>  icon-col" style="width: 25px"> <?php echo $obj->inputCheckBox('chkPick-master', array('etc' => 'style="margin-top:0"')); ?></div>
                        <div class="div-table-col detail-col-header <?php echo $obj->hideOnDisabled(); ?>  icon-col"></div>
                    </div>

                    <?php
 
                    $totalRows = count($rsPurchaseDetail);

                    for ($i = 0; $i <= $totalRows; $i++) {

                        $class =  'transaction-detail-row';
                        $overwrite = true;
                        $readonly = true;
                        $disabled = false;
                        $sokey = '';   

                        $rsServiceDetail = array();
                        $totalDetailRows = 0;
                        $optionRows = 'display:none';

                        if ($i == $totalRows) {
                            $class = 'detail-row-template row-template';
                            $overwrite = false;
                            $disabled = true;
                        } else {

                            $readonly = false;  
                            $readonly = (!$downpaymentType) ? true : false;  
 
                            $sokey = $rsPurchaseDetail[$i]['sokey'];
                            $_POST['hidSODetailKey[]'] =  $sokey;
                            $_POST['hidWOKey[]'] =  $rsPurchaseDetail[$i]['wokey'];
                            $_POST['WOCode[]'] =  $rsPurchaseDetail[$i]['wocode'];
                            $_POST['salesOrderDetailCode[]'] =  $rsPurchaseDetail[$i]['socode'];
                            $_POST['workOrderDate[]'] =  $obj->formatDBDate($rsPurchaseDetail[$i]['wodate']);
                            $_POST['detailSubtotal[]'] =   $obj->formatNumber($rsPurchaseDetail[$i]['total']); 

                            $_POST['hidDetailKey[]'] = $rsPurchaseDetail[$i]['pkey'];
                            $_POST['amount[]'] =   $obj->formatNumber($rsPurchaseDetail[$i]['amount']);


                            // Detail Service 
                            if (!empty($sokey) && !$downpaymentType) {
                                $rsServiceDetail = $obj->getItemDetail($rsPurchaseDetail[$i]['pkey']); 
                                $totalDetailRows = count($rsServiceDetail);
                                $optionRows = '';
                            }
                        }

                    ?>

                        <div class="div-table-row <?php echo $class; ?>">
                            <div class="div-table-col detail-col-detail" style="padding:0"> 
                                <div class="div-table" style="width:100%">
                                    <div class="div-table-row">
                                        <div class="div-table-col detail-col-detail" style="width:200px;">
                                            <?php echo $obj->inputHidden('hidDetailKey[]', array('overwritePost' => $overwrite, 'disabled' => $disabled)); ?>
                                            <?php echo $obj->inputHidden('hidWOKey[]', array('overwritePost' => $overwrite, 'disabled' => $disabled)); ?>
                                            <?php echo $obj->inputText('WOCode[]', array('overwritePost' => $overwrite,  'add-class' => 'mnv-barcode-input')); ?>
                                        </div> 
                                        <div class="div-table-col detail-col-detail" style="width:110px;"> 
                                                <?php echo $obj->inputText('workOrderDate[]', array('overwritePost' => $overwrite, 'readonly' => true,  'disabled' => $disabled, 'etc' => 'style="text-align:center;"')); ?>
                                         </div>
                                         <div class="div-table-col detail-col-detail"> 
                                            <?php echo $obj->inputText('salesOrderDetailCode[]', array('overwritePost' => $overwrite, 'readonly' => true,   'disabled' => $disabled)); ?>
                                            <?php echo $obj->inputHidden('hidSODetailKey[]', array('overwritePost' => $overwrite, 'readonly' => true,  'disabled' => $disabled)); ?>
                                        </div>
                                        <div class="div-table-col detail-col-detail" style="width:110px; text-align:right;">
                                            <?php echo $obj->inputNumber('detailSubtotal[]', array('overwritePost' => $overwrite, 'readonly' => true,  'disabled' => $disabled, 'etc' => 'style="text-align:right;" ')); ?>
                                        </div> 
                                        <div class="div-table-col detail-col-detail" style="width:110px; text-align:right;">
                                            <?php echo $obj->inputNumber('amount[]', array('overwritePost' => $overwrite, 'readonly' => $readonly,  'disabled' => $disabled, 'etc' => 'style="text-align:right;"')); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="options-row" style="<?php echo $optionRows ?>">
                                    <div style="clear:both; height:1em"></div>
                                    <div class="div-table mnv-transaction transaction-detail" style="width: 100%;" attr-level="1" attr-group="hidDetailItemKey">
                                        <div class="div-table-row">
                                            <div class="div-table-col detail-col-detail col-header no-border <?php echo $obj->hideOnDisabled(); ?> " style="width: 3em; "></div>
                                            <div class="div-table-col detail-col-detail col-header no-border" style="width:4em; text-align:right">
                                                <?php echo ucwords($obj->lang['party']); ?>
                                            </div>
                                            <div class="div-table-col detail-col-detail col-header no-border"  style="width:20em;">
                                                <?php echo ucwords($obj->lang['services']); ?>
                                            </div>
                                            <div class="div-table-col detail-col-detail col-header no-border">
                                                <?php echo ucwords($obj->lang['description']); ?>
                                            </div>
                                            <div class="div-table-col detail-col-detail col-header no-border" style="width:8em; text-align:right">
                                                <?php echo ucwords($obj->lang['price']); ?>
                                            </div>
                                            <div class="div-table-col detail-col-detail col-header no-border" style="width:10em; text-align:right">
                                                <?php echo ucwords($obj->lang['subtotal']); ?>
                                            </div>
 
                                            <?php if ($usePPNDetail) { ?>
                                                <div class="div-table-col detail-col-detail col-header no-border" style="width:5em; text-align:right"><?php echo ucwords($obj->lang['PPN']); ?> %</div>
                                                 <div class="div-table-col detail-col-detail col-header no-border" style="width:1em; text-align:center">Inc.</div>
                                                <div class="div-table-col detail-col-detail col-header no-border" style="width:5em; text-align:right"><?php echo ucwords($obj->lang['tax23']); ?></div>
                                                <div class="div-table-col detail-col-detail col-header no-border" style="width:1em; text-align:center">R</div>
                                                <div class="div-table-col detail-col-detail col-header no-border" style="width:9em; text-align:right"><?php echo ucwords($obj->lang['total']); ?></div>
                                            <?php } ?> 
                                        </div> 

                                        <?php

                                        for ($j = 0; $j <= $totalDetailRows; $j++) {

                                            $classDetail =  'transaction-detail-row';
                                            $overwriteDetail = true;
                                            $disabledDetail = false;

                                            if ($j == $totalDetailRows) {
                                                $classDetail = 'service-row-template row-template';
                                                $overwriteDetail = false;
                                                $disabledDetail = true;
                                            } else {

                                                $classDetail = 'service-detail-row ' . $classDetail;

                                                $_POST['hidDetailItemKey[]'] =  $rsServiceDetail[$j]['pkey']; 
                                                $_POST['hidWODetailKey[]'] = $rsServiceDetail[$j]['wodetailkey'];
                                                $_POST['qtyDetail[]'] =  $obj->formatNumber($rsServiceDetail[$j]['qty'], 0);
                                                $_POST['hidItemDetailKey[]'] =  $rsServiceDetail[$j]['itemkey'];
                                                $_POST['itemNameDetail[]'] =   $rsServiceDetail[$j]['itemname'];
                                                $_POST['remarkDetail[]'] =   $rsServiceDetail[$j]['remark'];
                                                $_POST['priceInUnitDetail[]'] = $obj->formatNumber($rsServiceDetail[$j]['priceinunit']);
                                                $_POST['subtotalDetail[]'] = $obj->formatNumber($rsServiceDetail[$j]['total']);
                                                $_POST['taxDetail[]'] = $obj->formatNumber($rsServiceDetail[$j]['taxdetail'], 2);
                                                $_POST['taxValueDetail[]'] = $obj->formatNumber($rsServiceDetail[$j]['taxdetailvalue']);
                                                $_POST['beforeTaxDetail[]'] = $obj->formatNumber($rsServiceDetail[$j]['beforetaxdetailvalue']);
                                                $_POST['afterTaxDetail[]'] = $obj->formatNumber($rsServiceDetail[$j]['aftertaxdetailvalue']);
                                                $_POST['tax23PercentageDetail[]'] = $obj->formatNumber($rsServiceDetail[$j]['tax23percentagedetail'], 2);
                                                $_POST['chkService[]'] =  1;
                                                $_POST['chkIsTax23[]'] = $rsServiceDetail[$j]['istax23'];
                                                $_POST['detailType[]'] = $rsServiceDetail[$j]['detailtype'];
                                                $_POST['chkIsReimburse[]'] = $rsServiceDetail[$j]['isreimburse'];
                                                $_POST['chkIncludeTaxDetail[]'] = $rsServiceDetail[$j]['ispriceincludetax'];
                                            }

                                        ?>
                                            <div class="div-table-row <?php echo $classDetail; ?>">
                                                <div class="div-table-col-3 <?php echo $obj->hideOnDisabled(); ?> " style="text-align:center">
                                                    <?php echo $obj->inputHidden('hidDetailItemKey[]', array('overwritePost' => $overwriteDetail, 'disabled' => $disabledDetail)); ?> 
                                                    <?php echo $obj->inputHidden('hidWODetailKey[]', array('overwritePost' => $overwriteDetail, 'disabled' => $disabledDetail)); ?>
                                                    <?php echo $obj->inputHidden('detailType[]', array('overwritePost' => $overwriteDetail, 'disabled' => $disabledDetail)); ?>
                                                    <?php echo $obj->inputCheckBox('chkService[]', array('disabled' => $disabledDetail)); ?>
                                                </div>
                                                <div class="div-table-col-3">
                                                    <?php echo $obj->inputInteger('qtyDetail[]', array('overwritePost' => $overwriteDetail, 'add-class' => 'label-style', 'disabled' => $disabledDetail, 'etc' => 'style="text-align:right;" ')); ?>
                                                </div>
                                                <div class="div-table-col-3">
                                                    <?php echo $obj->inputText('itemNameDetail[]', array('readonly' => true, 'overwritePost' => $overwriteDetail, 'class' => 'form-control label-style',  'disabled' => $disabledDetail)); ?>
                                                    <?php echo $obj->inputHidden('hidItemDetailKey[]', array('overwritePost' => $overwriteDetail,  'disabled' => $disabledDetail)); ?>
                                                </div>
                                                <div class="div-table-col-3">
                                                    <?php echo $obj->inputText('remarkDetail[]', array('readonly' => true, 'overwritePost' => $overwriteDetail, 'class' => 'form-control label-style',  'disabled' => $disabledDetail)); ?>
                                                </div>
                                                <div class="div-table-col-3">
                                                    <?php echo $obj->inputNumber('priceInUnitDetail[]', array('readonly' => true, 'overwritePost' => $overwriteDetail, 'class' => 'form-control inputnumber label-style',   'disabled' => $disabledDetail, 'etc' => ' style="text-align:right;"  ')); ?>
                                                </div>
                                                <div class="div-table-col-3">
                                                    <?php echo $obj->inputNumber('subtotalDetail[]', array('readonly' => true, 'overwritePost' => $overwriteDetail, 'class' => 'form-control inputnumber label-style',   'disabled' => $disabledDetail, 'etc' => ' style="text-align:right;"  ')); ?>
                                                </div> 
                                                <?php if ($usePPNDetail) { ?>
                                                    <div class="div-table-col-3">
                                                        <?php echo $obj->inputDecimal('taxDetail[]', array('readonly' => true, 'overwritePost' => $overwriteDetail, 'add-class' => 'label-style', 'disabled' => $disabledDetail, 'etc' => 'style="text-align:right;" ')); ?>
                                                        <?php echo $obj->inputHidden('taxValueDetail[]', array('readonly' => true, 'overwritePost' => $overwriteDetail, 'class' => 'form-control inputnumber label-style', 'disabled' => $disabledDetail, 'etc' => 'style="text-align:right;" ')); ?>
                                                    </div>
                                                    <div class="div-table-col-3" style="text-align:center">
                                                        <?php echo $obj->inputCheckBox('chkIncludeTaxDetail[]', array('readonly' => true, 'overwritePost' => $overwriteDetail, 'disabled' => $disabledDetail)); ?>
                                                    </div> 
                                                    <div class="div-table-col-3">
                                                        <?php echo $obj->inputDecimal('tax23PercentageDetail[]', array('readonly' => true, 'overwritePost' => $overwriteDetail, 'add-class' => 'label-style', 'disabled' => $disabledDetail, 'etc' => 'style="text-align:right;" ')); ?>
                                                    </div>
                                             
                                                    <div class="div-table-col-3">
                                                        <?php echo $obj->inputCheckBox('chkIsReimburse[]', array('readonly' => true, 'overwritePost' => $overwriteDetail, 'disabled' => $disabledDetail)); ?>
                                                    </div>   
                                                    <div class="div-table-col-3">
                                                        <?php echo $obj->inputNumber('afterTaxDetail[]', array('readonly' => true, 'overwritePost' => $overwriteDetail, 'class' => 'form-control inputnumber label-style',   'disabled' => $disabledDetail, 'etc' => ' style="text-align:right;"  ')); ?>
                                                        <?php echo $obj->inputHidden('beforeTaxDetail[]', array('readonly' => true, 'overwritePost' => $overwriteDetail, 'class' => 'form-control inputnumber label-style',   'disabled' => $disabledDetail, 'etc' => ' style="text-align:right;"  ')); ?>
                                                    </div> 
                                                    <?php } ?> 
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                                <!--</div> -->
                            </div>
                            <div class="div-table-col detail-col-detail icon-col  align-top-adjust <?php echo $obj->hideOnDisabled(); ?>"><?php echo $obj->inputCheckBox('chkPick[]', array('value' => 1, 'disabled' => $disabled)); ?></div>
                            <div class="div-table-col detail-col-detail icon-col  align-top-adjust <?php echo $obj->hideOnDisabled(); ?>"><?php echo $obj->inputLinkButton('btnDeleteRows', '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button', 'etc' => 'tabindex="-1"')); ?></div>
                        </div>

                    <?php } ?>

                </div>
            </div>

            <div style="clear:both; height:1em;"></div>
            <div style="float:left; display:inline-block;"><?php echo $obj->inputButton('btnAddRows', $obj->lang['addRows'], array('class' => 'btn btn-primary btn-second-tone')); ?></div>

            <div>
                <div style="width:350px; margin-left:2em; float:right;">
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
                    <div class="mnv-total-group mnv-downpayment" style="display:none">
                        <div class="div-table" style="width: 100%">
                            <div class="div-table-row  form-group">
                                <div class="div-table-col-3" style="text-align:right;">
                                    <?php echo $obj->lang['downpayment']; ?>
                                </div>
                                <div class="div-table-col-3" style="width:180px">
                                    <?php echo $obj->inputCollapsibleNumber('totalDownpayment', array('readonly' => true, 'etc' => 'style="text-align:right;" ')); ?>
                                </div>
                                <div class="div-table-col-3  <?php echo $obj->hideOnDisabled(); ?> icon-col"></div>
                            </div>
                        </div>

                        <div class="mnv-total-group-detail">
                            <div class="div-table transaction-detail" style="width: 100%">
                                <?php
                                $totalRows = count($rsDP);
                                for ($i = 0; $i <= $totalRows; $i++) {
                                    $class =  'transaction-detail-row';
                                    $overwrite = true;
                                    $disabled = false;

                                    if ($i == $totalRows) {
                                        $class = 'downpayment-row-template row-template';
                                        $overwrite = false;
                                        $disabled = true;
                                    } else {
                                        $_POST['hidDetailDownpaymentKey[]'] = $rsDP[$i]['pkey'];
                                        $_POST['hidDownpaymentKey[]'] = $rsDP[$i]['downpaymentkey'];
                                        $_POST['downpaymentCode[]'] = $rsDP[$i]['refcode'];
                                        $_POST['downpaymentAmount[]'] = $obj->formatNumber($rsDP[$i]['amount']);
                                    }
                                ?>

                                    <div class="div-table-row form-group <?php echo $class; ?>">
                                        <div class="div-table-col-3" style="text-align:right;">
                                            <?php echo $obj->inputHidden('hidDetailDownpaymentKey[]', array('overwritePost' => $overwrite,  'disabled' => $disabled)); ?>
                                            <?php echo $obj->inputHidden('hidDownpaymentKey[]', array('overwritePost' => $overwrite,  'disabled' => $disabled)); ?>
                                            <?php echo  $obj->inputText('downpaymentCode[]', array('overwritePost' => $overwrite, 'disabled' => $disabled)); ?>
                                        </div>
                                        <div class="div-table-col-3" style="width:180px">
                                            <?php echo $obj->inputNumber('downpaymentAmount[]', array('overwritePost' => $overwrite, 'class' => 'form-control inputnumber mnv-detail-field', 'disabled' => $disabled, 'etc' => 'style="text-align:right;"')); ?>
                                        </div>
                                        <div class="div-table-col-3 <?php echo $obj->hideOnDisabled(); ?> icon-col">
                                            <?php echo $obj->inputLinkButton('btnDeleteRows', '<i class="fas fa-times"></i>', array('etc' => 'tabIndex="-1"', 'class' => 'btn btn-link remove-button')); ?>
                                        </div>
                                    </div>

                                <?php } ?>

                                <div class="div-table-row form-group ">
                                    <div class="div-table-col-3"></div>
                                    <div class="div-table-col-3">
                                        <div class="form-detail-button mnv-total-group-hide-detail" style="float:right; text-align:right;"><?php echo ucwords($obj->lang['hideDetail']); ?></div>
                                    </div>
                                    <div class="div-table-col-3 icon-col <?php echo $obj->hideOnDisabled(); ?>"></div>
                                </div>
                                <div class="div-table-row form-group ">
                                    <div class="div-table-col-3 " style="height:1em"></div>
                                    <div class="div-table-col-3 "></div>
                                    <div class="div-table-col-3  <?php echo $obj->hideOnDisabled(); ?> "></div>
                                </div>

                            </div>
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
                                         <?php echo $obj->inputHidden('hidDetailPaymentKey[]',array('overwritePost' => $overwrite,  'disabled' => $disabled)); ?>
                                         <?php echo  (ADV_FINANCE && TEST_VOUCHER) ? $obj->inputSelect('selVoucher[]', $arrAvailableVoucher, array('overwritePost' => $overwrite, 'disabled' => $disabled))
                                                                    : $obj->inputSelect('selPaymentMethod[]', $arrPaymentMethod, array('overwritePost' => $overwrite, 'disabled' => $disabled)) 
                                         ?>                                
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
                    <?php if (!$usePPNDetail) { ?>
                        <div class="div-table-row  form-group not-downpayment-field" style="<?php echo $notDownpaymentField; ?>">
                            <div class="div-table-col-5" style="text-align:right;">
                                <?php echo ucwords($obj->lang['subtotal']); ?>
                            </div>
                            <div class="div-table-col-5" style="width:200px;">
                                <?php echo $obj->inputNumber('subtotal', array('readonly' => true, 'etc' => 'style="text-align:right;"')); ?>
                            </div>

                        </div>

                        <div class="div-table-row  form-group not-downpayment-field" style="<?php echo $notDownpaymentField; ?>">
                            <div class="div-table-col-5" style="text-align:right;">
                                <?php echo ucwords($obj->lang['discount']); ?>
                            </div>
                            <div class="div-table-col-5">
                                <div class="flex">
                                    <div><?php echo $obj->inputSelect('selFinalDiscountType', $obj->arrDiscountType); ?> </div>
                                    <div class="consume"> <?php echo $obj->inputNumber('finalDiscount', array('class' => 'form-control ' . $finalDiscDecimalType, 'etc' => 'style="text-align:right;"')); ?> </div>
                                </div>
                            </div>
                        </div>
                      <div class="div-table-row  form-group not-downpayment-field" style="<?php echo $notDownpaymentField; ?>">
                            <div class="div-table-col-5"></div>
                            <div class="div-table-col-5"></div>
                        </div>
                    <?php } ?>



                    <div class="div-table-row  form-group">
                        <div class="div-table-col-5" style="text-align:right;">
                            <?php echo ucwords($obj->lang['beforeTax']); ?>
                        </div>
                        <div class="div-table-col-5" style="width:200px;">
                            <?php echo $obj->inputNumber('beforeTaxTotal', array('disabled' => true, 'etc' => 'style="text-align:right;"')); ?>
                        </div>

                    </div>

                    <?php if ($usePPNDetail) { ?>
                        <div class="div-table-row  form-group">
                            <div class="div-table-col-5" style="text-align:right;">
                                <?php echo strtoupper($obj->lang['PPN']); ?>
                            </div>
                            <div class="div-table-col-5" style="width:200px;">
                                <?php echo $obj->inputNumber('taxValue', array('readonly' => true, 'etc' => 'style="text-align:right;"')); ?>
                            </div>

                        </div>
                    <?php } else { ?>
                        <div class="div-table-row  form-group">
                            <div class="div-table-col-5" style="text-align:right;">
                                <?php echo strtoupper($obj->lang['PPN']); ?> [Include]
                            </div>
                            <div class="div-table-col-5">
                                <div class="flex">
                                    <div><?php echo $obj->inputCheckBox('chkIncludeTax'); ?></div>
                                    <div class="percentage-col">
                                        <?php
                                        if ($obj->loadSetting('inputTaxValueType') == 2)
                                            echo $obj->inputSelect('taxPercentage', TAX_VALUE, array('etc' => 'style="text-align:right;"', 'add-class' => 'no-padding'));
                                        else
                                            echo $obj->inputDecimal('taxPercentage', array('etc' => 'style="text-align:right;"'));
                                        ?>
                                    </div>
                                    <div>%</div>
                                    <div class="consume"><?php echo $obj->inputNumber('taxValue', array('readonly' => true, 'etc' => 'style="text-align:right;"')); ?></div>
                                </div>
                            </div>
                        </div>

                    <?php } ?>

                    <div class="div-table-row  form-group">
                        <div class="div-table-col-5" style="text-align:right;">
                            <?php echo ucwords($obj->lang['stampFee']); ?>
                        </div>
                        <div class="div-table-col-5">
                            <?php echo  $obj->inputNumber('stampFee', array('etc' => 'style="text-align:right"')); ?>
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
                    <div style="clear:both; height: 2em"></div>


                    <div class="div-table-row  form-group">
                        <div class="div-table-col-5" style="text-align:right;">
                            <?php echo ucwords($obj->lang['tax23']); ?>
                        </div>
                        <div class="div-table-col-5">
                            <div class="flex"> 
                                <div class="consume"><?php echo $obj->inputNumber('tax23Value', array('readonly' => true, 'etc' => 'style="text-align:right;"')); ?></div>
                            </div>
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
