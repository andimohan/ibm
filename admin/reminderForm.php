<?php
require_once '../_config.php';
require_once '../_include-v2.php';

includeClass('Reminder.class.php');
$reminder = createObjAndAddToCol(new Reminder());
$medicalJobOrder = createObjAndAddToCol(new MedicalJobOrder());
$medicalRequestClaim = createObjAndAddToCol(new MedicalRequestClaim());
$medicalSalesOrderQuotation = createObjAndAddToCol(new MedicalSalesOrderQuotation());
$medicalPurchaseOrder = createObjAndAddToCol(new MedicalPurchaseOrder());
$medicalSalesInvoice = createObjAndAddToCol(new MedicalSalesInvoice());
$employee = createObjAndAddToCol(new Employee());

$obj = $reminder;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
// some modules that have different security object from that of their class

if (!$security->isAdminLogin($securityObject, 10, true));

$rsEmployee = $employee->getDataRowById(base64_decode($_SESSION[$employee->loginAdminSession]['id']));
$_POST['senderName'] = $rsEmployee[0]['name'];
$idSender = $rsEmployee[0]['pkey'];

$readonly = false;

$formAction = 'reminderList';

$isQuickAdd = (isset($_GET) && !empty($_GET['quickadd'])) ? true : false;

$editWarehouseInactiveCriteria = '';
$rs = prepareOnLoadData($obj);

$_POST['trDate'] = date('d / m / Y');

$arrType = array();
$arrType['request'] = $obj->lang['newRequest'];
$arrType['job'] = $obj->lang['jobOrder'];
$arrType['quotation'] = $obj->lang['priceQuotation'];
$arrType['guaranteeLetter'] = $obj->lang['guaranteeLetter'];
$arrType['invoice'] = $obj->lang['salesInvoice'];

if (!empty($_GET['id'])) {
    $id = $_GET['id'];

    $_POST['name'] = $rs[0]['name'];
    $_POST['selModule'] = $rs[0]['module'];
    $_POST['trDesc'] = $rs[0]['trdesc'];

    if ($rs[0]['createdby'] != $idSender) {
        $readonly = true;
    }

    $_POST['hidEmployeeKey'] = $rs[0]['employeekey'];
    if (!empty($rs[0]['employeekey'])) {
        $rsEmployee = $employee->getDataRowById($rs[0]['employeekey']);
        $_POST['employeeName'] = $rsEmployee[0]['name'];
    }

    if (!empty($rs[0]['createdby'])) {
        $rsEmployee = $employee->getDataRowById($rs[0]['createdby']);
        $_POST['senderName'] = $rsEmployee[0]['name'];
    }

    switch ($rs[0]['module']) {
        case 'request':
            $_POST['hidMedicalRequestClaimKey'] = $rs[0]['refrequestkey'];
            $rsMedicalRequestClaim = $medicalRequestClaim->getDataRowById($rs[0]['refrequestkey']);
            $_POST['medicalRequestClaimCode'] = $rsMedicalRequestClaim[0]['code'];
            break;
        case 'job':
            $_POST['hidMedicalJobOrderKey'] = $rs[0]['refjobkey'];
            $rsMedicalJobOrder = $medicalJobOrder->getDataRowById($rs[0]['refjobkey']);
            $_POST['medicalJobOOrderCode'] = $rsMedicalJobOrder[0]['code'];
            break;
        case 'quotation':
            $_POST['hidMedicalSalesOrderQuotationKey'] = $rs[0]['refquotationkey'];
            $rsMedicalSalesOrderQuotation = $medicalSalesOrderQuotation->getDataRowById($rs[0]['refquotationkey']);
            $_POST['medicalSalesOrderQuotationCode'] = $rsMedicalSalesOrderQuotation[0]['code'];
            break;
        case 'guaranteeLetter':
            $_POST['hidMedicalPurchaseKey'] = $rs[0]['refpurchasekey'];
            $rsMedicalPurchaseOrder = $medicalPurchaseOrder->getDataRowById($rs[0]['refpurchasekey']);
            $_POST['medicalPurchaseCode'] = $rsMedicalPurchaseOrder[0]['code'];
            break;
        case 'invoice':
            $_POST['hidMedicalSalesInvoiceKey'] = $rs[0]['refinvoicekey'];
            $rsMedicalSalesInvoice = $medicalSalesInvoice->getDataRowById($rs[0]['refinvoicekey']);
            $_POST['medicalSalesInvoiceCode'] = $rsMedicalSalesInvoice[0]['code'];
            break;
    }

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
            var tabID = <?php echo ($isQuickAdd) ?  $_GET['tabID'] :  'selectedTab.newPanel[0].id';  ?>

            var reminder = new Reminder(tabID);
            prepareHandler(reminder);

            var fieldValidation = {
                code: {
                    validators: {
                        notEmpty: {
                            message: phpErrorMsg.code[1]
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
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['sender']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputAutoCode('senderName', array('readonly' => true)); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['date']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputDate('trDate', array('readonly' => $readonly)); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['toAccount']); ?></label>
                                <div class="col-xs-9">
                                    <?php
                                    echo $obj->inputAutoComplete(
                                        array(
                                            'element' => array(
                                                'value' => 'employeeName',
                                                'key' => 'hidEmployeeKey'
                                            ),
                                            'source' => array(
                                                'url' => 'ajax-employee.php',
                                                'data' => array('action' => 'searchData')
                                            ),
                                            'readonly' => $readonly
                                        )
                                    );
                                    ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['module']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputSelect('selModule', $arrType, array('readonly' => $readonly)); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['transactionCode']); ?></label>
                                <div class="col-xs-9">
                                    <div class="flex">
                                        <div class="consume">
                                            <div class="isrequest" style="margin-right:0">
                                                <?php
                                                echo $obj->inputAutoComplete(
                                                    array( 
                                                        'element' => array(
                                                            'value' => 'medicalRequestClaimCode',
                                                            'key' => 'hidMedicalRequestClaimKey'
                                                        ),
                                                        'source' => array(
                                                            'url' => 'ajax-medical-request-claim.php',
                                                            'data' => array(
                                                                'action' => 'searchData'
                                                            )
                                                        ),
                                                        'readonly' => $readonly
                                                    )
                                                );
                                                ?>
                                            </div>
                                            <div class="isjob">
                                                <?php
                                                echo $obj->inputAutoComplete(
                                                    array(
                                                        'revalidateField' => false,
                                                        'element' => array(
                                                            'value' => 'medicalJobOOrderCode',
                                                            'key' => 'hidMedicalJobOrderKey'
                                                        ),
                                                        'source' => array(
                                                            'url' => 'ajax-medical-job-order.php',
                                                            'data' => array(
                                                                'action' => 'searchData'
                                                            )
                                                        ),
                                                        'readonly' => $readonly
                                                    )
                                                );
                                                ?>
                                            </div>
                                            <div class="ispurchase">
                                                <?php
                                                echo $obj->inputAutoComplete(
                                                    array(
                                                        'revalidateField' => false,
                                                        'element' => array(
                                                            'value' => 'medicalPurchaseCode',
                                                            'key' => 'hidMedicalPurchaseKey'
                                                        ),
                                                        'source' => array(
                                                            'url' => 'ajax-medical-purchase-order.php',
                                                            'data' => array(
                                                                'action' => 'searchData'
                                                            )
                                                        ),
                                                        'readonly' => $readonly
                                                    )
                                                );
                                                ?>
                                            </div>
                                            <div class="isquotation">
                                                <?php
                                                echo $obj->inputAutoComplete(
                                                    array(
                                                        'revalidateField' => false,
                                                        'element' => array(
                                                            'value' => 'medicalSalesOrderQuotationCode',
                                                            'key' => 'hidMedicalSalesOrderQuotationKey'
                                                        ),
                                                        'source' => array(
                                                            'url' => 'ajax-medical-sales-order-quotation.php',
                                                            'data' => array(
                                                                'action' => 'searchData'
                                                            )
                                                        ),
                                                        'readonly' => $readonly
                                                    )
                                                );
                                                ?>
                                            </div>
                                            <div class="isinvoice">
                                                <?php
                                                echo $obj->inputAutoComplete(
                                                    array(
                                                        'revalidateField' => false,
                                                        'element' => array(
                                                            'value' => 'medicalSalesInvoiceCode',
                                                            'key' => 'hidMedicalSalesInvoiceKey'
                                                        ),
                                                        'source' => array(
                                                            'url' => 'ajax-medical-sales-invoice.php',
                                                            'data' => array(
                                                                'action' => 'searchData'
                                                            )
                                                        ),
                                                        'readonly' => $readonly
                                                    )
                                                );
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div> 
                        </div>
                    </div>
                    <div class="div-table-col">
                        <div class="div-tab-panel">
                            <div class="div-table-caption border-blue"><?php echo ucwords($obj->lang['note']); ?></div>
                            <div class="form-group">
                                <div class="col-xs-12">
                                    <?php echo  $obj->inputTextArea('trDesc', array('etc' => 'style="height:10em;"', 'readonly' => $readonly)); ?>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div class="form-button-margin"></div>
            <div class="form-button-panel">
                <?php echo $obj->generateSaveButton(array(), true);  ?>
            </div>

        </form>
        <?php echo $obj->showDataHistory(); ?>
    </div>
</body>

</html>