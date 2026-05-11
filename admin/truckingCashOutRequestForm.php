<?php
require_once '../_config.php';
require_once '../_include-v2.php';

includeClass('TruckingCashOutRequest.class.php');
$truckingCashOutRequest = createObjAndAddToCol(new TruckingCashOutRequest());

$obj = $truckingCashOutRequest;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
// some modules that have different security object from that of their class

if (!$security->isAdminLogin($securityObject, 10, true));

$formAction = 'truckingCashOutRequestList';

$isQuickAdd = (isset($_GET) && !empty($_GET['quickadd'])) ? true : false;

$rsDetail = array();

$_POST['trDate'] = date('d / m / Y 00:00');
$_POST['trStartDatePeriod'] = date('d / m / Y 00:00');
$_POST['trEndDatePeriod'] = date('d / m / Y 00:00');
$cashOutDate = date('d / m / Y 00:00');

$rs = prepareOnLoadData($obj);

$editWarehouseInactiveCriteria = '';

if (!empty($_GET['id'])) {
    $id = $_GET['id'];

    $rsDetail = $obj->getDetailWithRelatedInformation($id);
 
    $_POST['trDate'] = $obj->formatDBDate($rs[0]['trdate'], 'd / m / Y H:i');
    $_POST['trStartDatePeriod'] = $obj->formatDBDate($rs[0]['startdateperiod'], 'd / m / Y H:i');
    $_POST['trEndDatePeriod'] = $obj->formatDBDate($rs[0]['enddateperiod'], 'd / m / Y H:i');
}

if (!empty($rs[0]['recipientkey'])) {
    $rsEmployee = $employee->getDataRowById($rs[0]['recipientkey']);
    $_POST['recipientName'] = $rsEmployee[0]['name'];
}


$arrStatus = $obj->convertForCombobox($obj->getAllStatus(), 'pkey', 'status');


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title></title>
    <script type="text/javascript">
        jQuery(document).ready(function() {
            var tabID = <?php echo ($isQuickAdd) ?  $_GET['tabID'] :  'selectedTab.newPanel[0].id';  ?>;

            var truckingCashOutRequest = new TruckingCashOutRequest(tabID);

            prepareHandler(truckingCashOutRequest);

            var fieldValidation = {
                code: {
                    validators: {
                        notEmpty: {
                            message: phpErrorMsg.code[1]
                        },
                    }
                },
                recipientName: {
                    validators: {
                        notEmpty: {
                            message: phpErrorMsg.recipient[1]
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
                                    <?php echo $obj->inputSelect('selStatus', $arrStatus); ?>
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
                                    <?php echo $obj->inputDateTime('trDate'); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['recipient']); ?></label>
                                <div class="col-xs-9">
                                    <?php
                                    echo $obj->inputAutoComplete(
                                        array(
                                            'objRefer' => $employee,
                                            'element' => array(
                                                'value' => 'recipientName',
                                                'key' => 'hidRecipientKey'
                                            ),
                                            'source' => array(
                                                'url' => 'ajax-employee.php',
                                                'data' => array(
                                                    'action' => 'searchData'
                                                )
                                            ),
                                            'callbackFunction' => 'getTabObj().updateEmployeeInformation(event, ui)'
                                        )
                                    );
                                    ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['period']); ?></label>
                                <div class="col-xs-9">
                                    <div class="flex">
                                        <div class="consume"><?php echo $obj->inputDateTime('trStartDatePeriod', array('etc' => 'style="text-align:center"')); ?></div>
                                        <div class="consume"><?php echo $obj->inputDateTime('trEndDatePeriod', array('etc' => 'style="text-align:center"')); ?></div>
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
                                    <?php echo $obj->inputTextArea('notes', array('etc' => 'style="height:10em;"', 'allowedStatusForEdit' => array(1))); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="div-table mnv-transaction transaction-detail" style="width:100%; border-bottom:1px solid #333; ">
                <div class="div-table-row">
                    <div class="div-table-col detail-col-header"><?php echo ucwords($obj->lang['code']); ?></div>
                    <div class="div-table-col detail-col-header" style="width:200px; text-align:center;"><?php echo ucwords($obj->lang['date']); ?></div>
                    <div class="div-table-col detail-col-header"><?php echo ucwords($obj->lang['recipient']); ?></div>
                    <div class="div-table-col detail-col-header" style="width:120px; text-align:right;"><?php echo ucwords($obj->lang['amount']); ?></div>
                    <div class="div-table-col detail-col-header <?php echo $obj->hideOnDisabled(); ?>" style="width:45px"></div>
                </div>

                <?php
                $totalRows = count($rsDetail);
                for ($i = 0; $i <= $totalRows; $i++) {

                    $class =  'transaction-detail-row';
                    $overwrite = true;
                    $etc = '';
                        
                    $_POST['refDate[]'] =  date('d / m / Y 00:00');

                    if ($i == $totalRows) {
                        $class = 'detail-row-template';
                        $overwrite = false;
                        $etc = 'disabled="disabled"';
                    } else {
                        $_POST['hidDetailKey[]'] =  $rsDetail[$i]['pkey'];
                        $_POST['hidCostCashOutKey[]'] =  $rsDetail[$i]['costcashoutkey']; 
                        $_POST['amount[]'] =   $obj->formatNumber($rsDetail[$i]['amount']);
                        $_POST['cashOutCode[]'] =  $rsDetail[$i]['refcode'];
                        $_POST['recipientNameDetail[]'] =  $rsDetail[$i]['employeename'];
                        $_POST['refDate[]'] = $obj->formatDBDate($rsDetail[$i]['refdate'],'d / m / Y H:i'); 
                    }
                ?>

                    <div class="div-table-row <?php echo $class; ?>">
                        <div class="div-table-col detail-col-detail">
                            <?php echo $obj->inputHidden('hidCostCashOutKey[]', array('overwritePost' => $overwrite, 'etc' => $etc)); ?> 
                            <?php echo $obj->inputHidden('hidDetailKey[]', array('overwritePost' => $overwrite, 'etc' => $etc)); ?>
                            <?php echo $obj->inputText('cashOutCode[]', array('overwritePost' => $overwrite, 'etc' => $etc)); ?>
                        </div>
                        <div class="div-table-col detail-col-detail"><?php echo $obj->inputText('refDate[]', array('value' => $cashOutDate, 'etc' => 'style="text-align:center"'. $etc, 'readonly' => true)); ?></div>
                        <div class="div-table-col detail-col-detail"><?php echo $obj->inputText('recipientNameDetail[]', array('overwritePost' => $overwrite, 'etc' => $etc, 'readonly' => true)); ?></div>
                        <div class="div-table-col detail-col-detail"><?php echo $obj->inputNumber('amount[]', array('overwritePost' => $overwrite, 'etc' => 'style="text-align:right" ' . $etc, 'readonly' => true)); ?></div>
                        <div class="div-table-col detail-col-detail  <?php echo $obj->hideOnDisabled(); ?>"><?php echo $obj->inputLinkButton('btnDeleteRows', '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button', 'etc' => 'tabIndex="-1" ')); ?></div>
                    </div>
                <?php } ?>

            </div>

            <div style="clear:both; height:1em;"></div>
            <div style="float:left; display:inline-block;"><?php echo $obj->inputButton('btnAddRows', $obj->lang['addRows'], array('class' => 'btn btn-primary btn-second-tone')); ?></div>

            <div>
                <div class="<?php echo $obj->hideOnDisabled(); ?>" style="float:right; width:45px; height: 1em"></div>
                <div class="div-table" style="float:right;">
                    <div class="div-table-row  form-group">
                        <div class="div-table-col-3" style="text-align:right;">
                            <?php echo ucwords($obj->lang['total']); ?>
                        </div>
                        <div class="div-table-col-3" style="width:120px">
                            <?php echo $obj->inputNumber('total', array('readonly' => true, 'etc' => 'style="text-align:right;"')); ?>
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