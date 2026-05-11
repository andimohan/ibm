<?php
require_once '../_config.php';
require_once '../_include-v2.php';

includeClass('BankReconsiliation.class.php');

$bankReconsiliaton = createObjAndAddToCol(new BankReconsiliation());
$warehouse = createObjAndAddToCol(new Warehouse());
$chartOfAccount = createObjAndAddToCol(new ChartOfAccount());
$currency = createObjAndAddToCol(new Currency());

$obj = $bankReconsiliaton;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
// some modules that have different security object from that of their class

if (!$security->isAdminLogin($securityObject, 10, true));

$formAction = 'bankReconsiliationList';

$isQuickAdd = (isset($_GET) && !empty($_GET['quickadd'])) ? true : false;

$rsDetail = array();

$_POST['trDate'] = date('d / m / Y');
$_POST['trStartDatePriode'] = date('F Y');
//$_POST['trStartDatePriode'] = date('d / m / Y');
//$_POST['trEndDatePriode'] = date('d / m / Y');
$_POST['trDetailDate'] = date('d / m / Y');

$rs = prepareOnLoadData($obj);

$editWarehouseInactiveCriteria = '';

if (!empty($_GET['id'])) {
    $id = $_GET['id'];

    $rsDetail = $obj->getDetailWithRelatedInformation($id);

    //$_POST['trDate'] = $obj->formatDBDate($rs[0]['date']);
    
    $_POST['beginingBalance'] = $obj->formatNumber($rs[0]['beginingbalance']);
    $_POST['endingBalance'] = $obj->formatNumber($rs[0]['endingbalance']);
    $_POST['trStartDatePriode'] = $obj->formatDBDate($rs[0]['startdatepriode'], 'F Y');
//    $_POST['trEndDatePriode'] = $obj->formatDBDate($rs[0]['enddatepriode'], 'd / m / Y'); 
    
    if (!empty($rs[0]['coakey'])) {
        $rsChartOfAccount = $chartOfAccount->getDataRowById($rs[0]['coakey']);
        $_POST['coaName'] = $rsChartOfAccount[0]['code'] . " - " . $rsChartOfAccount[0]['name'];
    }

}



$arrStatus = $obj->convertForCombobox($obj->getAllStatus(), 'pkey', 'status');
$arrWarehouse = $warehouse->generateComboboxOpt(null, array('criteria' => ' and (' . $warehouse->tableName . '.statuskey = 1' . $editWarehouseInactiveCriteria . ')'));
$arrCurrency = $currency->generateComboboxOpt(null, array('criteria' => ' and (' . $currency->tableName . '.statuskey = 1)'));

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title></title>
    <script type="text/javascript">
        jQuery(document).ready(function() {
            var tabID = <?php echo ($isQuickAdd) ?  $_GET['tabID'] :  'selectedTab.newPanel[0].id';  ?>;
            var tablekey = <?php echo $obj->getTableKeyAndObj($obj->tableName ,array('key'))['key']; ?>;  
        
        var varConstant = {  
            TABLEKEY : tablekey
        };
            var bankReconsiliation = new BankReconsiliation(tabID, <?php echo json_encode(array(
                                                                        'rsDetail' => $rsDetail
                                                                    )); ?>,varConstant);

            prepareHandler(bankReconsiliation);

            var fieldValidation = {
                code: {
                    validators: {
                        notEmpty: {
                            message: phpErrorMsg.code[1]
                        },
                    }
                },
                  coaName: {
                    validators: {
                        notEmpty: {
                            message: phpErrorMsg.coa[1]
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
                                    <?php echo $obj->inputSelect('selStatus', $arrStatus, array('disabled' => true)) ?>
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
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['account']); ?></label>
                                <div class="col-xs-9">
                                    <?php
                                    echo  $obj->inputAutoComplete(array(
                                        'objRefer' => $chartOfAccount,
                                        'element' => array(
                                            'value' => 'coaName',
                                            'key' => 'hidCoaKey'
                                        ),
                                        'source' => array(
                                            'url' => 'ajax-coa.php',
                                            'data' => array('action' => 'searchData','iscashbank'=>true)
                                        ),
                                        'callbackFunction' => 'getTabObj().onChangeBeginingBalance()'
                                       
                                    ));
                                    ?>
                                </div>
                            </div>
                            <div class="form-group mnv-date-range">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['period']); ?></label>
                                <div class="col-xs-9">
                                    <div class="flex">
                                        <div class="consume"><?php echo $obj->inputMonth('trStartDatePriode', array('etc' => 'style="text-align:center"')); ?></div>
<!--                                        <div class="consume"><?php /*echo $obj->inputDate('trEndDatePriode', array('etc' => 'style="text-align:center"')); */?></div>-->
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['openingBalance']); ?></label>
                                <div class="col-xs-9  mnv-currency">
                                    <div class="flex">
                                        <div><?php echo $obj->inputSelect('selCurrency', $arrCurrency, array('class' => 'form-control input-currency')); ?></div>
                                        <div class="consume"><?php echo $obj->inputDecimal('beginingBalance', array('class' => 'form-control inputnumber', 'readonly' => true)); ?></div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-xs-3"></div>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputButton('btnImport', $obj->lang['import'], array('class' => 'btn btn-primary btn-second-tone')); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="div-table-col">
                        <div class="div-tab-panel">
                            <div class="div-table-caption border-blue"><?php echo ucwords($obj->lang['note']); ?></div>
                            <div class="form-group">
                                <div class="col-xs-12">
                                    <?php echo $obj->inputTextArea('trDesc', array('etc' => 'style="height:10em;"', 'allowedStatusForEdit' => array(1)));
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="div-table mnv-transaction transaction-detail mnv-checkbox-group" style="width:100%; border-bottom:1px solid #333; ">
                <div class="div-table-row">
                    <div class="div-table-col detail-col-header" style="width:140px;"><?php echo ucwords($obj->lang['voucherCode']); ?></div>
                    <div class="div-table-col detail-col-header" style="width:140px;"><?php echo ucwords($obj->lang['refCode']); ?></div>
                    <div class="div-table-col detail-col-header" style="width:110px; text-align:center;"><?php echo ucwords($obj->lang['date']); ?></div>
                    <div class="div-table-col detail-col-header" tyle="width:200px;"><?php echo ucwords($obj->lang['note']); ?></div>
                    <div class="div-table-col detail-col-header" style="width:60px; text-align:center;"><?php echo ucwords($obj->lang['curr']); ?></div>
                    <div class="div-table-col detail-col-header" style="width:110px; text-align:right;"><?php echo ucwords($obj->lang['debit']); ?></div>
                    <div class="div-table-col detail-col-header" style="width:110px; text-align:right;"><?php echo ucwords($obj->lang['credit']); ?></div>
                    <div class="div-table-col detail-col-header" style="width:125px; text-align:right;"><?php echo ucwords($obj->lang['balance']); ?></div>
                    <div class="div-table-col detail-col-header  <?php echo $obj->hideOnDisabled(); ?> icon-col" style="text-align:center; width: 35px;"><?php echo $obj->inputCheckBox('chkPick-master', array('etc' => 'style="margin-top:0"')); ?></div>
                    <div class="div-table-col detail-col-header icon-col <?php echo $obj->hideOnDisabled(); ?>" style="width:35px;"></div>
                    <div class="div-table-col detail-col-header icon-col <?php echo $obj->hideOnDisabled(); ?>" style="width:35px;"></div>
                </div>

                <?php
                $totalRows = count($rsDetail);
                for ($i = 0; $i <= $totalRows; $i++) {

                    $class =  'transaction-detail-row';
                    $overwrite = true;
                    $etc = '';

                    if ($i == $totalRows) {
                        $class = 'bank-reconsiliation-row-template row-template';
                        $overwrite = false;
                        $etc = 'disabled="disabled"';
                    } else {
                        $_POST['hidDetailKey[]'] =  $rsDetail[$i]['pkey'];
                        $_POST['hidVoucherKey[]'] =  $rsDetail[$i]['voucherkey'];
                        $_POST['voucherCode[]'] =  $rsDetail[$i]['vouchercode'];
                        $_POST['refCode[]'] =  $rsDetail[$i]['refcode'];
                        $_POST['trDetailDate[]'] =  $obj->formatDBDate($rsDetail[$i]['date'], 'd / m / Y');
                        $_POST['trDetailDesc[]'] =  $rsDetail[$i]['trdesc'];
                        $_POST['currency[]'] =  $rsDetail[$i]['currencyname'];
                        $_POST['debit[]'] =   $obj->formatNumber($rsDetail[$i]['debit']);
                        $_POST['credit[]'] =   $obj->formatNumber($rsDetail[$i]['credit']);
                        $_POST['chkPick[]'] =  1;
                    }
                ?>


                    <div class="div-table-row <?php echo $class; ?>">
                        <div class="div-table-col detail-col-detail">
                            <?php echo $obj->inputHidden('hidDetailKey[]', array('overwritePost' => $overwrite, 'etc' => $etc)); ?>
                            <?php echo $obj->inputHidden('hidVoucherKey[]', array('overwritePost' => $overwrite, 'etc' => $etc, 'readonly' => true)); ?>
                            <?php echo $obj->inputHidden('hidCurrencyKey[]', array('overwritePost' => $overwrite, 'etc' => $etc, 'readonly' => true)); ?>
                            <?php
                            echo $obj->inputText('voucherCode[]', array('overwritePost' => $overwrite));
                            ?>
                        </div>
                        <div class="div-table-col detail-col-detail">
                            <?php
                            echo $obj->inputText('refCode[]', array('overwritePost' => $overwrite, 'readonly' => true));
                            ?>
                        </div>
                        <div class="div-table-col detail-col-detail"><?php echo $obj->inputDate('trDetailDate[]', array('overwritePost' => $overwrite, 'etc' => 'style="text-align:center"' . $etc, 'readonly' => true)); ?></div>
                        <div class="div-table-col detail-col-detail"><?php echo $obj->inputText('trDetailDesc[]', array('overwritePost' => $overwrite, 'etc' => $etc, 'readonly' => true)); ?></div>
                        <div class="div-table-col detail-col-detail"><?php echo $obj->inputText('currency[]', array('overwritePost' => $overwrite, 'etc' => 'style="text-align:center"'.$etc, 'readonly' => true)); ?></div>
                        <div class="div-table-col detail-col-detail"><?php echo $obj->inputNumber('debit[]', array('overwritePost' => $overwrite, 'etc' => 'style="text-align:right" ' . $etc, 'readonly' => true)); ?></div>
                        <div class="div-table-col detail-col-detail"><?php echo $obj->inputNumber('credit[]', array('overwritePost' => $overwrite, 'etc' => 'style="text-align:right" ' . $etc, 'readonly' => true)); ?></div>
                        <div class="div-table-col detail-col-detail"><?php echo $obj->inputNumber('detailBalance[]', array('overwritePost' => $overwrite, 'etc' => 'style="text-align:right" ' . $etc, 'readonly' => true)); ?></div>
                        <div class="div-table-col detail-col-detail  <?php echo $obj->hideOnDisabled(); ?> icon-col" style="text-align:center; width: 35px;"><?php echo $obj->inputCheckBox('chkPick[]',  array('value' => 1, 'etc' => $etc)); ?></div>
                        <div class="div-table-col detail-col-detail  <?php echo $obj->hideOnDisabled(); ?>  icon-col"><?php echo $obj->inputLinkButton('btnAddDetailRow', '<i class="fas fa-plus-circle"></i>', array('class' => 'btn btn-link add-row-button', 'etc' => 'attr-template="bank-reconsiliation-row-template"')); ?></div>
                        <div class="div-table-col detail-col-detail <?php echo $obj->hideOnDisabled(); ?>"><?php echo $obj->inputLinkButton('btnDeleteRows', '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button', 'etc' =>  'tabIndex="-1" style="padding:6px 0"')); ?></div>
                    </div>
                <?php } ?>

            </div>

            <div style="clear:both; height:1em;"></div>

            <div>
                <div class="<?php echo $obj->hideOnDisabled(); ?>" style="float:right; width:105px; height: 1em"></div>
                <div class="div-table" style="float:right;">
                    <div class="div-table-row  form-group">
                        <div class="div-table-col-3" style="width:125px">
                            <?php echo $obj->inputNumber('debitAmount', array('readonly' => true, 'etc' => 'style="text-align:right;"')); ?>
                        </div>
                        <div class="div-table-col-3" style="width:125px">
                            <?php echo $obj->inputNumber('creditAmount', array('readonly' => true, 'etc' => 'style="text-align:right;"')); ?>
                        </div>
                    </div>
                </div>
            </div>

            <div style="clear:both; height:1em;"></div>

            <div>
                <div class="<?php echo $obj->hideOnDisabled(); ?>" style="float:right; width:105px; height: 1em"></div>
                <div class="div-table" style="float:right;">
                    <div class="div-table-row  form-group">
                        <div class="div-table-col-3" style="text-align:right;">
                            <?php echo ucwords($obj->lang['endingBalance']); ?>
                        </div>
                        <div class="div-table-col-3" style="width:250px">
                            <?php echo $obj->inputNumber('endingBalance', array('readonly' => true, 'etc' => 'style="text-align:right;"')); ?>
                        </div>
                    </div>
                </div>
            </div>

            <div style="clear:both; height:1em;"></div>
            <div class="form-button-panel">
                <?php echo $obj->generateSaveButton(array(), true); ?>
            </div>

        </form>
        <?php echo $obj->showDataHistory(); ?>
    </div>
</body>

</html>
