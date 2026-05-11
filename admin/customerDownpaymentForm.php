<?php
require_once '../_config.php';
require_once '../_include-v2.php';
includeClass(array('Downpayment.class.php', 'CustomerDownpayment.class.php'));
$customerDownpayment = createObjAndAddToCol(new CustomerDownpayment());
$currency = createObjAndAddToCol(new Currency());
$warehouse = createObjAndAddToCol(new Warehouse());
$paymentMethod = createObjAndAddToCol(new PaymentMethod());
$customer = createObjAndAddToCol(new Customer());
$salesOrder = createObjAndAddToCol(new SalesOrder());
$truckingServiceOrder = createObjAndAddToCol(new TruckingServiceOrder());
$coaLink = createObjAndAddToCol(new COALink());
$cashBank = createObjAndAddToCol(new CashBank());
$termOfPayment = createObjAndAddToCol(new TermOfPayment());

$obj = $customerDownpayment;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
// some modules that have different security object from that of their class

if (!$security->isAdminLogin($securityObject, 10, true));

$formAction = 'customerDownpaymentList';

$isQuickAdd = (isset($_GET) && !empty($_GET['quickadd'])) ? true : false;

$_POST['trDate'] = date('d / m / Y');

$editWarehouseInactiveCriteria = '';
$editPaymentMethodInactiveCriteria = '';
$editCurrencyInactiveCriteria = '';
$editTermOfPaymentInactiveCriteria = '';

$rs = prepareOnLoadData($obj);
$rsPaymentMethodDetail = array();
$arrAvailableVoucher = array();

$readonylyFromInvoice = false;

if (!empty($_GET['id'])) {
    $id = $_GET['id'];
    
  	if(ADV_FINANCE && TEST_VOUCHER){ 
		$rsPaymentMethodDetail = $obj->getPaymentVoucherDetail($id);  
		$arrAvailableVoucher = $class->convertForCombobox($rsPaymentMethodDetail,'cashbankvoucherkey','voucherlabel');  

		$existingVoucherKey = array_column($rsPaymentMethodDetail,'cashbankvoucherkey');
		$otherVoucher = $cashBank->getAvailableVoucher($rs[0]['customerkey'],' and '.$cashBank->tableName.'.pkey not in ('.$obj->oDbCon->paramString($existingVoucherKey,',').')');

		foreach($otherVoucher as $voucherItem){ 
			$arrAvailableVoucher[$voucherItem['pkey']]['label'] = $voucherItem['voucherlabel'];
			$arrAvailableVoucher[$voucherItem['pkey']]['rel'] = array('rel-amount' => $voucherItem['outstanding']); 
		}  
	}else{
		$rsPaymentMethodDetail = $obj->getPaymentMethodDetail($id); 
	} 
	 
    $rsDownpaymentHistory = $obj->getUsedDPList($id);
    $rsDPSettlementHistory = $obj->getDPSettlementList($id);
    $rsDownpaymentHistory = $rsDownpaymentHistory['history'];
    $rsDPSettlementHistory = $rsDPSettlementHistory['history'];
    $rsDownpaymentAllHistory = array_merge($rsDownpaymentHistory, $rsDPSettlementHistory);

    $rsCustomer = $customer->getDataRowById($rs[0]['customerkey']);
	
    $_POST['hidCurrentCustomerKey'] = $rsCustomer[0]['pkey'] ;   
	$_POST['hidCurrentCustomerName'] = $rsCustomer[0]['name'] ; 
	$_POST['customerName'] = $rsCustomer[0]['name'];
    $_POST['hidCustomerKey'] = $rsCustomer[0]['pkey'];
    $_POST['trDesc'] = $rs[0]['trdesc'];
    $_POST['trDate'] = $obj->formatDBDate($rs[0]['trdate'], 'd / m / Y');
    $_POST['payment'] = $obj->formatNumber($rs[0]['payment']);
    $_POST['taxPercentage'] = $obj->formatNumber($rs[0]['taxpercentage'], 2);
    $_POST['taxValue'] = $obj->formatNumber($rs[0]['taxvalue']);
    $_POST['amount'] = $obj->formatNumber($rs[0]['amount']);
    $_POST['beforeTaxTotal'] = $obj->formatNumber($rs[0]['beforetaxtotal']);
    $_POST['outstanding'] = $obj->formatNumber($rs[0]['outstanding']);
    $_POST['payment'] = $obj->formatNumber($rs[0]['payment']);
    $_POST['hidRefKey'] =  $rs[0]['refkey'];
    $_POST['chkIncludeTax'] =  $rs[0]['ispriceincludetax'];
    $_POST['refCode'] =  $rs[0]['refcode'];
    $_POST['selTermOfPaymentKey'] =  $rs[0]['termofpaymentkey'];
    $_POST['selWarehouse'] = $rs[0]['warehousekey'];
    $_POST['selDPType'] = $rs[0]['reftabletype'];
    $_POST['subtotal'] = $obj->formatNumber($rs[0]['subtotal']);
    $_POST['prepaidTax23Percentage'] = $obj->formatNumber($rs[0]['prepaidtax23percentage'], 2);
    $_POST['prepaidTax23'] = $obj->formatNumber($rs[0]['prepaidtax23']);
    $_POST['selCurrency'] = $rs[0]['currencykey'];
    $_POST['currencyRate'] = $obj->formatNumber($rs[0]['rate'], -2);

    $editCurrencyInactiveCriteria = ' or ' . $currency->tableName . '.pkey = ' . $obj->oDbCon->paramString($rs[0]['currencykey']);
    $editWarehouseInactiveCriteria = ' or ' . $warehouse->tableName . '.pkey = ' . $obj->oDbCon->paramString($rs[0]['warehousekey']);
    $editTermOfPaymentInactiveCriteria = ' or ' . $termOfPayment->tableName . '.pkey = ' . $obj->oDbCon->paramString($rs[0]['termofpaymentkey']);
    
    $editPaymentMethodInactiveCriteria = ' or ' . $paymentMethod->tableName . '.pkey in (select paymentkey from ' . $obj->tablePayment . ' where refkey = ' . $obj->oDbCon->paramString($rs[0]['pkey']) . ')';

    if (!empty($rs[0]['refheaderkey']))
        $readonylyFromInvoice = true;
	
}
 
$rsTOP = $termOfPayment->searchData('', '', true, ' and (' . $termOfPayment->tableName . '.statuskey = 1' . $editTermOfPaymentInactiveCriteria . ')', ' order by duedays asc');
$arrTOP = $class->convertForCombobox($rsTOP, 'pkey', 'name');$arrStatus = $obj->generateComboboxOpt(array('data' => $obj->getAllStatus(), 'label' => 'status'));
$arrWarehouse = $warehouse->generateComboboxOpt(null, array('criteria' => ' and (' . $warehouse->tableName . '.statuskey = 1' . $editWarehouseInactiveCriteria . ')'));
$arrPaymentMethod = $paymentMethod->generateComboboxOpt(null, array('criteria' => ' and (' . $paymentMethod->tableName . '.statuskey = 1' . $editPaymentMethodInactiveCriteria . ')'));
$arrCurrency = $currency->generateComboboxOpt(null, array('criteria' => ' and (' . $currency->tableName . '.statuskey = 1)'));

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title></title>


    <script type="text/javascript">
        jQuery(document).ready(function() {

            var tabID = <?php echo ($isQuickAdd) ?  $_GET['tabID'] :  'selectedTab.newPanel[0].id';  ?>
            
            var cashTOP = Array();

            var varConstant = {
                CURRENCY: <?php echo json_encode(CURRENCY); ?>,
                ADV_FINANCE : <?php echo (ADV_FINANCE) ? "true" : "false"; ?>
            };

            <?php
            for ($i = 0; $i < count($rsTOP); $i++) {
                if ($rsTOP[$i]['duedays'] <> 0)
                    echo 'cashTOP.push(' . $rsTOP[$i]['pkey'] . ');' . chr(13);
            }
            ?>

            var customerDownpayment = new CustomerDownpayment(tabID, varConstant, cashTOP);
            prepareHandler(customerDownpayment);


            var fieldValidation = {
                code: {
                    validators: {
                        notEmpty: {
                            message: phpErrorMsg.code[1]
                        },
                    }
                },
                customerName: {
                    validators: {
                        notEmpty: {
                            message: phpErrorMsg.customer[1]
                        },
                    }
                },
                amount: {
                    validators: {
                        greaterThan: {
                            value: 0,
                            inclusive: false,
                            separator: ',',
                            message: phpErrorMsg.amount[2]
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
			<?php echo $obj->inputHidden('selDPType'); ?>
			<?php echo $obj->inputHidden('hidCurrentCustomerKey'); ?>
			<?php echo $obj->inputHidden('hidCurrentCustomerName'); ?>

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
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['warehouse']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo  $obj->inputSelect('selWarehouse', $arrWarehouse); ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['date']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputDate('trDate'); ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['note']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo  $obj->inputTextArea('trDesc', array('etc' => 'style="height:10em;"')); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="div-table-col">
                        <div class="div-tab-panel">
                            <div class="div-table-caption border-red"><?php echo ucwords($obj->lang['transactionInformation']); ?></div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['customer']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputAutoComplete(array(
                                        'readonly' => $readonylyFromInvoice,
                                        'objRefer' => $customer,
                                        'revalidateField' => true,
                                        'element' => array(
                                            'value' => 'customerName',
                                            'key' => 'hidCustomerKey'
                                        ),
                                        'source' => array(
                                            'url' => 'ajax-customer.php',
                                            'data' => array('action' => 'searchData')
                                        ), 
										'callbackFunction' => 'getTabObj().updateCustomerInformation(event, ui)'
                                    ));
                                    ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['reference']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputAutoComplete(array(
                                        'readonly' => $readonylyFromInvoice,
                                        'objRefer' => $salesOrder,
                                        'revalidateField' => false,
                                        'element' => array(
                                            'value' => 'refCode',
                                            'key' => 'hidRefKey'
                                        ),
                                        'source' => array(
                                            'url' => 'ajax-downpayment.php',
                                            'data' => array('action' => 'getSalesOrder')
                                        ),
                                        'callbackFunction' => 'getTabObj().updateTypeKey(ui)'

                                    ));
                                    ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['currency']); ?> / <?php echo ucwords($obj->lang['currencyRate']); ?></label>
                                <div class="col-xs-9  mnv-currency">
                                    <div class="flex">
                                        <div><?php echo $obj->inputSelect('selCurrency', $arrCurrency, array('class' => 'form-control input-currency')); ?></div>
                                        <div class="consume"><?php echo $obj->inputDecimal('currencyRate', array('class' => 'form-control inputnumber input-currency-rate')); ?></div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['downpayment']); ?></label>
                                <div class="col-xs-5">
                                    <?php echo $obj->inputDecimal('amount', array('readonly' => $readonylyFromInvoice)); ?>
                                </div>
                                <div class="col-xs-4" style="padding-left:0">
                                    <?php echo $obj->inputDecimal('outstanding', array('readonly' => true)); ?>
                                </div>
                            </div>

                            <!--<div style="clear:both; height: 1em"></div>
                                   <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['beforeTax']); ?></label> 
                                        <div class="col-xs-9"> 
                                           <?php echo $obj->inputNumber('beforeTaxTotal', array('readonly' => true)); ?>
                                        </div> 
                                   </div> 
                                   
                                   <div class="form-group"> 
                                      <div class="col-xs-3 control-label"> <?php echo strtoupper($obj->lang['PPN']); ?> [Include]</div>   
                                     <div class="col-xs-9"> 
                                         <div class="flex">    
                                            <div><?php echo $obj->inputCheckBox('chkIncludeTax', array('readonly' => $readonylyFromInvoice)); ?></div>  
                                            <div class="percentage-col"><?php echo $obj->inputDecimal('taxPercentage', array('readonly' => $readonylyFromInvoice)); ?></div> 
                                            <div>%</div>
                                            <div class="consume"><?php echo $obj->inputNumber('taxValue', array('readonly' => true)); ?></div>
                                          </div> 
                                    </div> 
                                 </div> 
                                 
                                   <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['subtotal']); ?></label> 
                                        <div class="col-xs-9"> 
                                           <?php echo $obj->inputNumber('subtotal', array('readonly' => true)); ?>
                                        </div> 
                                   </div> 
                                   
                                   <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['tax23']); ?></label> 
                                        <div class="col-xs-9"> 
                                           <div class="flex">  
                                            <div class="percentage-col"><?php echo $obj->inputDecimal('prepaidTax23Percentage', array('readonly' => $readonylyFromInvoice)); ?></div> 
                                            <div>%</div>
                                            <div class="consume"><?php echo $obj->inputNumber('prepaidTax23', array('readonly' => true)); ?></div>
                                        </div>
                                        </div> 
                                   </div>   
                                   
                                   
                                   
                                   <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['totalPayment']); ?></label> 
                                        <div class="col-xs-9"> 
                                           <?php echo $obj->inputNumber('payment', array('readonly' => true)); ?>
                                        </div> 
                                   </div> -->
                            <?php if (!empty($rsDownpaymentAllHistory)) { ?>
                                <div class="div-table" style="width:100%; margin-top:20px">
                                    <div class="div-table-row">
                                        <div class="div-table-col-5" style="border-top:1px solid #666;border-bottom:1px solid #666; width:100px;">
                                            <strong><?php echo ucwords($obj->lang['reference']); ?></strong>
                                        </div>
                                        <div class="div-table-col-5" style="border-top:1px solid #666;border-bottom:1px solid #666; text-align:center">
                                            <strong><?php echo ucwords($obj->lang['date']); ?></strong>
                                        </div>
                                        <div class="div-table-col-5" style="border-top:1px solid #666;border-bottom:1px solid #666; text-align:right;">
                                            <strong><?php echo ucwords($obj->lang['amount']); ?></strong>
                                        </div>

                                    </div>
                                    <?php
                                    for ($i = 0; $i < count($rsDownpaymentAllHistory); $i++) {
                                        echo '
                                                             <div class="div-table-row"> 
                                                                 <div class="div-table-col-5" style="border-bottom:1px solid #dedede;" > 
                                                                    ' . $rsDownpaymentAllHistory[$i]['code'] . '
                                                                 </div> 
                                                                 <div class="div-table-col-5" style="border-bottom:1px solid #dedede; text-align:center" > 
                                                                    ' . $obj->formatDBDate($rsDownpaymentAllHistory[$i]['trdate']) . '
                                                                 </div> 
                                                                 <div class="div-table-col-5" style="border-bottom:1px solid #dedede; text-align:right;" > 
                                                                    ' . $obj->formatNumber($rsDownpaymentAllHistory[$i]['amount']) . '
                                                                 </div>
                                                             </div> 
                                                         ';
                                    }
                                    ?>
                                </div>
                            <?php } ?>
                        </div>
                        <div class="div-tab-panel">
                            <div class="div-table-caption border-blue"><?php echo ucwords($obj->lang['payment']); ?></div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['payment']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo  $obj->inputSelect('selTermOfPaymentKey', $arrTOP); ?>
                                </div>
                            </div>
							<div class="form-group">
                                <label class="col-xs-3 control-label"></label>
                                <div class="col-xs-9">
                                     <div class="cashTOP transaction-detail" style="width:100%">
                                <?php

                                $totalRows = count($rsPaymentMethodDetail);
                                $numberClass = (!empty($_POST['selCurrency']) && $_POST['selCurrency'] == CURRENCY['idr']) ? 'inputnumber' : 'inputdecimal';
                                for ($i = 0; $i <= $totalRows; $i++) {
                                    $class =  'transaction-detail-row';
                                    $overwrite = true;
                                    $style = 'style="margin-bottom:0.3em"';
                                    $disabled = false;

                                    if ($i == $totalRows) {
                                        $class = 'payment-method-row-template clone-detail';
                                        $overwrite = false;
                                        $style = 'style="margin-bottom:0.3em; display:none !important"';
                                        $disabled = true;
                                    } else {

                                        $_POST['hidDetailPaymentKey[]'] = $rsPaymentMethodDetail[$i]['pkey'];
                                        $_POST['selPaymentMethod[]'] = $rsPaymentMethodDetail[$i]['paymentkey'];
                                        $_POST['selVoucher[]'] = $rsPaymentMethodDetail[$i]['cashbankvoucherkey'];
                                        $_POST['paymentMethodValue[]'] = $obj->formatNumber($rsPaymentMethodDetail[$i]['amount']);

                                        if (ADV_FINANCE) {
                                            if (USE_GL) {
                                                $rsPaymentCOA = $coaLink->getCOALink('payment', $warehouse->tableName, $rs[0]['warehousekey'], $rsPaymentMethodDetail[$i]['paymentkey']);
                                                $coakey = $rsPaymentCOA[0]['coakey'];
                                            } else {
                                                $coakey = $rsPaymentMethodDetail[$i]['paymentkey'];
                                            }

                                            $_POST['cashBankRefCode[]'] = $cashBank->getCashBankRef($id, $obj->tableName, $coakey)['code'];
                                        }
                                    }
                                ?>

                                    <div class="<?php echo $class; ?>" <?php echo $style; ?>>
									 		<div class="flex" style="width: 100%">
												<div class="consume">
													 <?php echo $obj->inputHidden('hidDetailPaymentKey[]', array('overwritePost' => $overwrite,  'disabled' => $disabled)); ?>
													 <?php echo  (ADV_FINANCE && TEST_VOUCHER) ? $obj->inputSelect('selVoucher[]', $arrAvailableVoucher, array('overwritePost' => $overwrite, 'disabled' => $disabled))
																		: $obj->inputSelect('selPaymentMethod[]', $arrPaymentMethod, array('overwritePost' => $overwrite, 'disabled' => $disabled)) 
													?>
												</div>
												<div>
													<?php echo $obj->inputDecimal('paymentMethodValue[]', array('overwritePost' => $overwrite, 'disabled' => $disabled, 'etc' => 'style="text-align:right;" ', 'class' => 'form-control ' . $numberClass)); ?>
												</div>
												<!--
harusnya ikut modul voucher
                                        <?php if (ADV_FINANCE) { ?>
                                            <div><?php echo $obj->inputText('cashBankRefCode[]', array('overwritePost' => $overwrite, 'readonly' => true)); ?></div>
                                        <?php } ?>
-->											<div>
												<?php echo $obj->inputLinkButton('btnDeleteRows', '<i class="fas fa-times"></i>', array('etc' => 'tabIndex="-1"', 'class' => 'btn btn-link remove-button')); ?>	
											</div>
											</div>  
                                    </div>

                                <?php } ?>

								<div class="flex">
										<div class="consume" style="text-align:right"> <?php echo $obj->inputLinkButton('btnAddPayment', $obj->lang['addRows'], array('class' => 'btn btn-link', 'etc' => 'style="padding-right:0; padding-top:0px"')); ?></div>
										<div class="<?php echo $obj->hideOnDisabled(); ?> icon-col"></div>
								</div>
<!--
                                <div class="div-table-row form-group">
                                    <div class="div-table-col-3"></div>
                                    <?php if (ADV_FINANCE) { ?>
                                        <div class="div-table-col-3"></div>
                                    <?php } ?>
                                    <div class="div-table-col-3" style="text-align:right"><?php echo $obj->inputLinkButton('btnAddPayment', $obj->lang['addRows'], array('class' => 'btn btn-link', 'etc' => 'style="padding-right:0; padding-top:0px"')); ?></div>
                                    <div class="div-table-col-3 <?php echo $obj->hideOnDisabled(); ?> icon-col" style="width:30px;"></div>
                                </div>
-->

                            </div>
                                </div>
                            </div>
                         

                        </div>
                    </div>
                </div>
            </div>

            <div class="form-button-panel">
                <?php echo $obj->generateSaveButton(array(), true); ?>
            </div>

        </form>

        <?php echo $obj->showDataHistory(); ?>
    </div>
</body>

</html>
