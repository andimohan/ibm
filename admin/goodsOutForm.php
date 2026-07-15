<?php
require_once '../_config.php';
require_once '../_include-v2.php';

includeClass(array('GoodsOut.class.php', 'ItemReceiving.class.php', 'Currency.class.php', 'ItemUnit.class.php', 'Customer.class.php', 'DocumentType.class.php'));
$goodsOut = createObjAndAddToCol(new GoodsOut());
$customer = createObjAndAddToCol(new Customer());
$itemReceiving = createObjAndAddToCol(new ItemReceiving());
$documentType = createObjAndAddToCol(new DocumentType());
$currency = createObjAndAddToCol(new Currency());


$obj = $goodsOut;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
// some modules that have different security object from that of their class

if (!$security->isAdminLogin($securityObject, 10, true))
    ;

$formAction = 'goodsOutList';

$isQuickAdd = (isset($_GET) && !empty($_GET['quickadd'])) ? true : false;

$rsDetail = array();

$_POST['trDate'] = date('d / m / Y');
$_POST['submissionDate'] = date('d / m / Y');
$_POST['registrationDate'] = date('d / m / Y');

$rs = prepareOnLoadData($obj);

$arrPalletDetail = array();
if (!empty($_GET['id'])) {
    $id = $_GET['id'];

    $rsDetail = $obj->getDetailWithRelatedInformation($id);



    $_POST['trDate'] = $obj->formatDBDate($rs[0]['trdate'], 'd / m / Y');

    if (!empty($rs[0]['customerkey'])) {
        $rsCustomer = $customer->getDataRowById($rs[0]['customerkey']);

        $_POST['hidCustomerKey'] = $rsCustomer[0]['pkey'];
        $_POST['customerName'] = $rsCustomer[0]['name'];
    }

    $_POST['recipient'] = $rs[0]['recipient'];
    $_POST['recipientAddress'] = $rs[0]['recipientaddress'];

    $_POST['selDocumentType'] = $rs[0]['documenttypekey'];
    $_POST['submissionNumber'] = $rs[0]['submissionnumber'];
    $_POST['submissionDate'] = $obj->formatDBDate($rs[0]['submissiondate'], 'd / m / Y');
    $_POST['registrationNumber'] = $rs[0]['registrationnumber'];
    $_POST['registrationDate'] = $obj->formatDBDate($rs[0]['registrationdate'], 'd / m / Y');

    $_POST['trDesc'] = $rs[0]['trdesc'];
    $_POST['selStatus'] = $rs[0]['statuskey'];

}

$arrCurrency = $currency->generateComboboxOpt(null, array('criteria' => ' and (' . $currency->tableName . '.statuskey = 1' . $editCurrencyInactiveCriteria . ')'));
$arrDocumentType = $documentType->generateComboboxOpt(null, array('criteria' => ' and (' . $documentType->tableName . '.statuskey = 1 )'));
$arrStatus = $obj->generateComboboxOpt(array('data' => $obj->getAllStatus(), 'label' => 'status'));

?>

<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

    <title></title>

    <script type="text/javascript">
        jQuery(document).ready(function () {
            var tabID = <?php echo ($isQuickAdd) ? $_GET['tabID'] : 'selectedTab.newPanel[0].id'; ?>;

            var goodsOut = new GoodsOut(tabID);

            prepareHandler(goodsOut);

            var fieldValidation = {
                code: {
                    validators: {
                        notEmpty: {
                            message: phpErrorMsg.code[1]
                        },
                    }
                },
                recipient: {
                    validators: {
                        notEmpty: {
                            message: phpErrorMsg.recipient[1]
                        },
                    }
                },
                customerName: {
                    validators: {
                        notEmpty: {
                            message: phpErrorMsg.customer[1]
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
                            <div class="div-table-caption border-orange">
                                <?php echo ucwords($obj->lang['generalInformation']); ?>
                            </div>
                            <div class="form-group">
                                <label
                                    class="col-xs-3 control-label"><?php echo ucwords($obj->lang['status']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputSelect('selStatus', $arrStatus, array('disabled' => true)); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['code']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputAutoCode('code'); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['date']); ?>
                                </label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputDate('trDate'); ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-xs-3 control-label">
                                    <?php echo ucwords($obj->lang['customer']); ?>
                                </label>
                                <div class="col-xs-9">

                                    <?php echo $obj->inputAutoComplete(
                                        array(
                                            'objRefer' => $customer,
                                            'element' => array(
                                                'value' => 'customerName',
                                                'key' => 'hidCustomerKey'
                                            ),
                                            'source' => array(
                                                'url' => 'ajax-customer.php',
                                                'data' => array('action' => 'searchData', 'statuskey' => '(2)')
                                            )
                                        )
                                    );
                                    ?>
                                </div>

                            </div>

                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['recipient']); ?>
                                </label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputText('recipient'); ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-xs-3 control-label">
                                    <?php echo ucwords($obj->lang['recipientAddress']); ?>
                                </label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputTextArea('recipientAddress', array('etc' => 'style="height:6em;"')); ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-xs-3 control-label">
                                    <?php echo ucwords($obj->lang['itemReceiving']); ?>
                                </label>
                                <div class="col-xs-9">

                                    <div class="flex">
                                        <div class="consume">
                                            <?php echo $obj->inputAutoComplete(
                                                array(
                                                    'objRefer' => $itemReceiving,
                                                    'element' => array(
                                                        'value' => 'itemReceivingCode',
                                                        'key' => 'hidItemReceivingKey'
                                                    ),
                                                    'source' => array(
                                                        'url' => 'ajax-item-receiving.php',
                                                        'data' => array('action' => 'searchData', 'statuskey' => '(2)')
                                                    )
                                                )
                                            );
                                            ?>
                                        </div>
                                        <div>
                                            <?php echo $obj->inputButton('btnImport', $obj->lang['import']); ?>
                                        </div>
                                    </div>

                                </div>
                            </div>



                        </div>
                    </div>
                    <div class="div-table-col">
                        <div class="div-tab-panel">

                            <div class="div-table-caption border-green"><?php echo ucwords($obj->lang['document']); ?>
                            </div>

                            <div class="form-group">
                                <label class="col-xs-3 control-label">
                                    <?php echo ucwords($obj->lang['documentType']); ?>
                                </label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputSelect('selDocumentType', $arrDocumentType); ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-xs-3 control-label">
                                    <?php echo ucwords($obj->lang['submissionNumber'] . ' / ' . $obj->lang['submissionDate']); ?>
                                </label>
                                <div class="col-xs-9">

                                    <div class="flex">
                                        <div class="consume">
                                            <?php echo $obj->inputText('submissionNumber'); ?>
                                        </div>
                                        <div>/</div>
                                        <div class="consume">
                                            <?php echo $obj->inputDate('submissionDate', array('etc' => 'style="text-align:center;"')); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-xs-3 control-label">
                                    <?php echo ucwords($obj->lang['registerNumber'] . ' / ' . $obj->lang['registerDate']); ?>
                                </label>
                                <div class="col-xs-9">
                                    <div class="flex">
                                        <div class="consume">
                                            <?php echo $obj->inputText('registrationNumber'); ?>
                                        </div>
                                        <div>/</div>
                                        <div class="consume">
                                            <?php echo $obj->inputDate('registrationDate', array('etc' => 'style="text-align:center;"')); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label
                                    class="col-xs-3 control-label"><?php echo ucwords($obj->lang['currency']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputSelect('selCurrency', $arrCurrency); ?>
                                </div>
                            </div>

                            <div class="div-table-caption border-blue"><?php echo ucwords($obj->lang['note']); ?>
                            </div>

                            <div class="form-group">
                                <div class="col-xs-12">
                                    <?php echo $obj->inputTextArea('trDesc', array('etc' => 'style="height:10em;"')); ?>
                                </div>
                            </div>


                        </div>

                    </div>
                </div>
            </div>
            <div class="div-table mnv-transaction transaction-detail labeling-detail"
                style="width:100%; border-bottom:1px solid #333; ">

                <div class="div-table-row">
                    <div class=" div-table-col detail-col-header" style="width:150px;">
                        <?php echo ucwords($obj->lang['itemReceiving']); ?>
                    </div>
                    <div class=" div-table-col detail-col-header" style="width:150px;">
                        <?php echo ucwords($obj->lang['submissionNumber']); ?>
                    </div>
                    <div class=" div-table-col detail-col-header" style="width:150px;">
                        <?php echo ucwords($obj->lang['itemCode']); ?>
                    </div>
                    <div class=" div-table-col detail-col-header">
                        <?php echo ucwords($obj->lang['itemName']); ?>
                    </div>
                    <div class="div-table-col detail-col-header" style="width:120px; text-align:right;">
                        <?php echo ucwords($obj->lang['qtyItem']); ?>
                    </div>
                    <div class="div-table-col detail-col-header" style="width:120px; text-align:right;">
                        <?php echo ucwords($obj->lang['qtyIssued']); ?>
                    </div>
                    <div class="div-table-col detail-col-header" style="width:120px; text-align:right;">
                        <?php echo ucwords($obj->lang['qty']); ?>
                    </div>
                    <div class="div-table-col detail-col-header  icon-col <?php echo $obj->hideOnDisabled(); ?>">
                    </div>
                </div>

                <?php
                $totalRows = count($rsDetail);

                for ($i = 0; $i <= $totalRows; $i++) {

                    $class = 'transaction-detail-row';
                    $overwrite = true;
                    $readonly = true;
                    $etc = '';
                    $txtSN = '';
                    $showOptions = false;

                    if ($i == $totalRows) {
                        $class = 'detail-row-template';
                        $overwrite = false;
                        $readonly = false;
                        $etc = 'disabled="disabled"';
                    } else {

                        $baseunitname = $rsDetail[$i]['baseunitname'];

                        $_POST['hidDetailKey[]'] = $rsDetail[$i]['pkey'];
                        $_POST['hidRefReceivingHeaderKey[]'] = $rsDetail[$i]['refreceivingheaderkey'];
                        $_POST['hidRefReceivingDetailKey[]'] = $rsDetail[$i]['refreceivingdetailkey'];
                        $_POST['itemReceiving[]'] = $rsDetail[$i]['receivingcode'];
                        $_POST['submissionDetailNumber[]'] = $rsDetail[$i]['submissionnumber'];
                        $_POST['hidItemKey[]'] = $rsDetail[$i]['itemkey'];
                        $_POST['itemCode[]'] = $rsDetail[$i]['itemcode'];
                        $_POST['itemName[]'] = $rsDetail[$i]['itemname'];
                        $_POST['itemQty[]'] = $obj->formatNumber($rsDetail[$i]['itemqty']);
                        $_POST['issuedQty[]'] = $obj->formatNumber($rsDetail[$i]['qtyissued']);
                        $_POST['qty[]'] = $obj->formatNumber($rsDetail[$i]['qty']);

                    }


                    ?>
                    <div class="div-table-row  <?php echo $class; ?>">
                        <div class="div-table-col detail-col-detail" style="vertical-align:top;">
                            <?php echo $obj->inputHidden('hidDetailKey[]', array('overwritePost' => $overwrite, 'etc' => $etc, )); ?>
                            <?php echo $obj->inputHidden('hidRefReceivingHeaderKey[]', array('overwritePost' => $overwrite, 'etc' => $etc, )); ?>
                            <?php echo $obj->inputHidden('hidRefReceivingDetailKey[]', array('overwritePost' => $overwrite, 'etc' => $etc, )); ?>
                            <?php echo $obj->inputText('itemReceiving[]', array('overwritePost' => $overwrite, 'readonly' => true, 'etc' => $etc, 'class' => 'form-control mnv-barcode-input')); ?>
                        </div>
                        <div class="div-table-col detail-col-detail">
                            <?php echo $obj->inputText('submissionDetailNumber[]', array('overwritePost' => $overwrite, 'readonly' => true, 'etc' => $etc, 'class' => 'form-control mnv-barcode-input')); ?>
                        </div>
                        <div class="div-table-col detail-col-detail">
                            <?php echo $obj->inputHidden('hidItemKey[]', array('overwritePost' => $overwrite, 'etc' => $etc, )); ?>
                            <?php echo $obj->inputText('itemCode[]', array('overwritePost' => $overwrite, 'readonly' => true, 'etc' => $etc, 'class' => 'form-control mnv-barcode-input')); ?>
                        </div>
                        <div class="div-table-col detail-col-detail">
                            <?php echo $obj->inputText('itemName[]', array('overwritePost' => $overwrite, 'readonly' => true, 'etc' => $etc, 'class' => 'form-control mnv-barcode-input')); ?>
                        </div>
                        <div class="div-table-col detail-col-detail">
                            <?php echo $obj->inputNumber('itemQty[]', array('readonly' => true, 'overwritePost' => $overwrite, 'etc' => 'style="text-align:right;" ' . $etc)); ?>
                        </div>
                        <div class="div-table-col detail-col-detail">
                            <?php echo $obj->inputNumber('issuedQty[]', array('readonly' => true, 'overwritePost' => $overwrite, 'etc' => 'style="text-align:right;" ' . $etc)); ?>
                        </div>
                        <div class="div-table-col detail-col-detail">
                            <?php echo $obj->inputNumber('qty[]', array('readonly' => false, 'overwritePost' => $overwrite, 'etc' => 'style="text-align:right;" ' . $etc)); ?>
                        </div>
                        <div class="div-table-col detail-col-detail icon-col <?php echo $obj->hideOnDisabled(); ?>">
                            <?php echo $obj->inputLinkButton('btnDeleteRows', '<i class="fas fa-times"></i>', array('etc' => 'tabIndex="-1"', 'class' => 'btn btn-link remove-button')); ?>
                        </div>
                    </div>

                <?php } ?>

            </div>
            <div style=" clear:both; height:1em;"></div>
            <!-- <div style="float:left; display:inline-block;">
                <?php echo $obj->inputButton('btnAddRows', $obj->lang['addRows'], array('class' => 'btn btn-primary btn-second-tone')); ?>
            </div> -->

            <div class="form-button-margin"></div>
            <div class="form-button-panel">
                <?php echo $obj->generateSaveButton(array(1, 2), true); ?>
            </div>

        </form>
        <?php echo $obj->showDataHistory(); ?>
    </div>
</body>

</html>