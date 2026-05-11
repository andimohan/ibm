<?php
require_once '../_config.php';
require_once '../_include-v2.php';

includeClass('MedicalPurchaseOrder.class.php');
$medicalPurchaseOrder = createObjAndAddToCol(new MedicalPurchaseOrder());
$customer = createObjAndAddToCol(new Customer());
$city = createObjAndAddToCol(new City());
$paymentMethod = createObjAndAddToCol(new PaymentMethod());
$medicalJobOrder = createObjAndAddToCol(new MedicalJobOrder());
$medicalRequestClaim = createObjAndAddToCol(new MedicalRequestClaim());
$supplier = createObjAndAddToCol(new Supplier());
$itemUnit = createObjAndAddToCol(new ItemUnit());
$item = createObjAndAddToCol(new Item());
$termOfPayment = createObjAndAddToCol(new TermOfPayment());
$warehouse = createObjAndAddToCol(new Warehouse());
$country = createObjAndAddToCol(new Country());
$customerInsurancePolicy = createObjAndAddToCol(new CustomerInsurancePolicy());

$obj = $medicalPurchaseOrder;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
// some modules that have different security object from that of their class

if (!$security->isAdminLogin($securityObject, 10, true));

$formAction = 'medicalPurchaseOrderList';

$_POST['trDate'] = date('d / m / Y');
$finalDiscDecimal = 0;

$editTermOfPaymentInactiveCriteria = '';
$editPaymentMethodInactiveCriteria = '';
$editWarehouseInactiveCriteria = '';


$rsPaymentMethodDetail = array();


$finalDiscDecimalType = 'inputnumber';
$rs = prepareOnLoadData($obj);
$rsDetail = array();

$arrGuaranteeType = array();
$arrGuaranteeType[1] = 'Initial Guarantee';
$arrGuaranteeType[2] = 'Final Guarantee';

$rsDiagnoseDetail = array();


if (!empty($_GET['id'])) {
    $id = $_GET['id'];
    $_POST['hidId'] = $_GET['id'];
    // dilakukan pada saat edit
    $rsDetail = $obj->getDetailWithRelatedInformation($id);
    $rsPaymentMethodDetail = $obj->getPaymentMethodDetail($id);
    $rsDiagnoseDetail = $medicalJobOrder->getDetailDiagnose($rs[0]['refkey']);

    $_POST['selWarehouseKey'] = $rs[0]['warehousekey'];
    $_POST['hidSupplierKey'] = $rs[0]['supplierkey'];
    $_POST['hidMedicalRequestClaimKey'] = $rs[0]['refrequestkey'];
    $_POST['total'] = $obj->formatNumber($rs[0]['grandtotal']);
    $_POST['beforeTaxTotal'] =  $obj->formatNumber($rs[0]['beforetaxtotal']);
    $_POST['excessFee'] =  $obj->formatNumber($rs[0]['excessfee']);
    if ($rs[0]['finaldiscounttype']  == 2) {
        $finalDiscDecimal = 2;
        $finalDiscDecimalType = 'inputdecimal';
    }

    $_POST['finalDiscount'] = $obj->formatNumber($rs[0]['finaldiscount'], $finalDiscDecimal);
    $_POST['beforeTaxTotal'] =  $obj->formatNumber($rs[0]['beforetaxtotal']);
    $_POST['shipmentFee'] = $obj->formatNumber($rs[0]['shipmentfee']);
    $_POST['etcCost'] = $obj->formatNumber($rs[0]['etccost']);
    $_POST['chkIncludeTax'] = $rs[0]['ispriceincludetax'];
    $_POST['taxValue'] = $obj->formatNumber($rs[0]['taxvalue']);
    $_POST['taxPercentage'] = $obj->formatNumber($rs[0]['taxpercentage'], 2);
    $_POST['balance'] =  $obj->formatNumber($rs[0]['balance']);


    if (!empty($rs[0]['supplierkey'])) {
        $rsSupplier = $supplier->getDataRowById($rs[0]['supplierkey']);
        $_POST['supplierName'] = $rsSupplier[0]['name'];
    }

    $_POST['hidMedicalJobOrderkey'] = $rs[0]['refkey'];
    if (!empty($rs[0]['refkey'])) {
        $rsMedicalJobOrder = $medicalJobOrder->searchData($medicalJobOrder->tableName . '.pkey', $rs[0]['refkey']);
        $rsMedicalRequestClaim = $medicalRequestClaim->searchData($medicalRequestClaim->tableName . '.pkey', $rs[0]['refrequestkey']);

        $_POST['medicalJobOrderCode'] = $rsMedicalJobOrder[0]['code'];
        $rsDiagnoseDetail = $medicalJobOrder->getDetailDiagnose($rs[0]['refkey']);
        $_POST['caseAddress'] = $rsMedicalJobOrder[0]['address'];
        $_POST['casePhone'] = $rsMedicalJobOrder[0]['casephone'];
        $_POST['codeLog'] = $rsMedicalJobOrder[0]['codelog'];
        $_POST['phoneCase'] = $rsMedicalJobOrder[0]['phonecase'];
        $_POST['caseDesc'] = $rsMedicalJobOrder[0]['trdesc'];
        $_POST['caseCityName'] = $rsMedicalJobOrder[0]['cityname'] . ', ' . $rsMedicalJobOrder[0]['citycategoryname'];

        $_POST['insuredName'] = $rsMedicalRequestClaim[0]['insuredname'];
        $_POST['insuredID'] = $rsMedicalRequestClaim[0]['insuredid'];
        $_POST['policyNumber'] = $rsMedicalRequestClaim[0]['policynumber'];
        $_POST['companyName'] = $rsMedicalRequestClaim[0]['customername'];
        $_POST['categoryName'] = $rsMedicalRequestClaim[0]['categoryname'];
        $_POST['insuranceCompanyName'] = $rsMedicalRequestClaim[0]['insurancecompanyname'];
        $_POST['countryName'] = $rsMedicalRequestClaim[0]['countryname'];
        $_POST['dateOfBirth'] = $obj->formatDBDate($rsMedicalRequestClaim[0]['dateofbirth']);
        $_POST['age'] = $rsMedicalRequestClaim[0]['age'];
        $_POST['insuredMobile'] = $rsMedicalRequestClaim[0]['insuredmobile'];
        $_POST['insuredPhone'] = $rsMedicalRequestClaim[0]['insuredphone'];
        $_POST['insuredEmail'] = $rsMedicalRequestClaim[0]['insuredemail'];
    }

    //update file 
    $rsItemFile = $obj->getFileDetail($id);
 	$obj->prepareLoadedFile($id,array('file' => $rsItemFile ));

    $editWarehouseInactiveCriteria = ' or ' . $warehouse->tableName . '.pkey = ' . $obj->oDbCon->paramString($rs[0]['warehousekey']);
}

$rsTOP = $termOfPayment->searchData('', '', true, ' and (' . $termOfPayment->tableName . '.statuskey = 1' . $editTermOfPaymentInactiveCriteria . ')', ' order by duedays asc');
$arrWarehouse = $class->convertForCombobox($warehouse->searchData('', '', true, ' and (' . $warehouse->tableName . '.statuskey = 1' . $editWarehouseInactiveCriteria . ')'), 'pkey', 'name');
$arrPaymentMethod = $obj->convertForCombobox($paymentMethod->searchData('', '', true, ' and (' . $paymentMethod->tableName . '.statuskey = 1' . $editPaymentMethodInactiveCriteria . ')'), 'pkey', 'name');

$arrTOP = $class->convertForCombobox($rsTOP, 'pkey', 'name');
$arrStatus = $obj->generateComboboxOpt(array('data' => $obj->getAllStatus(), 'label' => 'status'));
$arrDefaultUnit = $class->convertForCombobox($itemUnit->searchData('', '', true, ' and (' . $itemUnit->tableName . '.statuskey = 1 )'), 'pkey', 'name');
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
            var opt = Array();
            //  opt.arrFile = ();
            //  opt.uploadFileFolder = false;  <?php echo $obj->uploadFileFolder ? 'true' : 'false'; ?>;

            opt.fileFolder = "<?php echo $obj->uploadFileFolder; ?>";
            opt.fileUploaderTarget = "item-file-uploader";
            opt.arrFile = Array();
            opt.initialDiagnose = Array();
            
            var cashTOP = Array();

            <?php
            for ($i = 0; $i < count($rsTOP); $i++) {
                if ($rsTOP[$i]['duedays'] <> 0)
                    echo 'cashTOP.push(' . $rsTOP[$i]['pkey'] . ');' . chr(13);
            }

            if (isset($id) && !empty($id)) {
                for ($i = 0; $i < count($rsItemFile); $i++) {
                    echo 'opt.arrFile.push("' . $rsItemFile[$i]['file'] . '"); ';
                }
            }
            ?>

            var medicalPurchaseOrder = new MedicalPurchaseOrder(tabID, cashTOP, <?php echo json_encode(
                                                                                    array(
                                                                                        'rs' => $rs,
                                                                                        'rsDetail' => $rsDetail,
                                                                                        'initialDiagnoseDetail' => $rsDiagnoseDetail
                                                                                    )
                                                                                ); ?>, opt);
            prepareHandler(medicalPurchaseOrder);

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
                        }
                    }
                },
                medicalJobOrderCode: {
                    validators: {
                        notEmpty: {
                            message: phpErrorMsg.code[1]
                        }
                    }
                }
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
            <?php echo $obj->inputHidden('hidMedicalRequestClaimKey'); ?>

            <div class="div-table main-tab-table-2">
                <div class="div-table-row">
                    <div class="div-table-col">
                        <div class="div-tab-panel">
                            <div class="div-table-caption border-orange"><?php echo ucwords($obj->lang['generalInformation']); ?></div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['status']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo  $obj->inputSelect('selStatus', $arrStatus, array('value' => 2)); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['code'] . ' / ' . $obj->lang['log']); ?></label>
                                <div class="col-xs-9">
                                    <div class="flex">
                                        <div class="consume"> <?php echo $obj->inputAutoCode('code'); ?></div>
                                        <div>/</div>
                                        <div class="consume"> <?php echo $obj->inputText('codeLog', array('readonly' => true)); ?></div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['date']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputDate('trDate'); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['reference']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputAutoComplete(
                                        array(
                                            'objRefer' => $medicalJobOrder,
                                            'revalidateField' => true,
                                            'element' => array(
                                                'value' => 'medicalJobOrderCode',
                                                'key' => 'hidMedicalJobOrderkey'
                                            ),
                                            'source' => array(
                                                'url' => 'ajax-medical-job-order.php',
                                                'data' => array(
                                                    'action' => 'searchData',
                                                    'statuskey' => '(2,3)'
                                                )
                                            ),
                                            'callbackFunction' => 'getTabObj().updateMedicalJobOrder()'
                                        )
                                    );
                                    ?>

                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['warehouse']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputSelect('selWarehouseKey', $arrWarehouse); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['type']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputSelect('selGuaranteeType', $arrGuaranteeType); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['supplier']); ?></label>
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
                                                'data' => array(
                                                    'action' => 'searchData'
                                                )
                                            )
                                        )
                                    );
                                    ?>
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
                            <div class="div-table-caption border-green"><?php echo ucwords($obj->lang['caseInformation']); ?></div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['diagnose']); ?></label>
                                <div class="col-xs-9">
                                    <div class="div-table mnv-transaction diagnose-detail transaction-detail" style="width:100%">
                                        <?php
                                        $totalRows = count($rsDiagnoseDetail);
                                        for ($j = 0; $j <= $totalRows; $j++) {

                                            $class =  'transaction-detail-row';
                                            $overwrite = true;
                                            $readonly = true;
                                            $disabled = false;

                                            if ($j == $totalRows) {
                                                $class = 'diagnose-row-template row-template';
                                                $overwrite = false;
                                                $etc = 'disabled="disabled"';
                                            } else {
                                                $_POST['initialDiagnose[]'] = $rsDiagnoseDetail[$j]['codenameinitialdiagnose'];
                                            }

                                        ?>
                                            <div class="div-table-row <?php echo $class; ?>  odd-style-adjustment">
                                                <div class="div-table-col" style="padding-left:0">
                                                    <div class="consume">
                                                        <?php echo $obj->inputText('initialDiagnose[]', array('overwritePost' => $overwrite, 'readonly' => $readonly, 'disabled' => $disabled)); ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php }  ?>

                                    </div>
                                </div>
                            </div>

                            <div style="clear:both; height: 1em"></div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['address']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputTextArea('caseAddress', array('etc' => 'style="height:8em;"', 'readonly' => true)); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['phone']); ?> </label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputText('casePhone', array('readonly' => true)); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['city']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputText('caseCityName', array('readonly' => true)); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['note']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputTextArea('caseDesc', array('etc' => 'style="height:8em;"', 'readonly' => true)); ?>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="div-table-col">
                        <div class="div-tab-panel">
                            <div class="div-table-caption border-blue"><?php echo ucwords($obj->lang['insuredInformation']); ?></div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['policyNumber']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo  $obj->inputText('policyNumber', array('readonly' => true)); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['category']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo  $obj->inputText('categoryName', array('readonly' => true)); ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['insuredName'] . ' / ' . $obj->lang['company']); ?></label>
                                <div class="col-xs-9">
                                    <div class="flex">
                                        <div class="consume"><?php echo  $obj->inputText('insuredName', array('readonly' => true)); ?></div>
                                        <div>/</div>
                                        <div class="consume"><?php echo  $obj->inputText('companyName', array('readonly' => true)); ?></div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['insuranceCompany']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo  $obj->inputText('insuranceCompanyName', array('readonly' => true)); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['excess']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo  $obj->inputNumber('excessFee'); ?>
                                </div>
                            </div>
                            <div style="clear:both; height:1em;"></div>

                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['IDNumber']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo  $obj->inputText('insuredID', array('readonly' => true)); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['country']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputText('countryName', array('readonly' => true)); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['dateOfBirth']); ?> / <?php echo ucwords($obj->lang['age']); ?></label>
                                <div class="col-xs-9">
                                    <div class="flex">
                                        <div class="consume"><?php echo $obj->inputDate('dateOfBirth', array('readonly' => true,  'etc' => 'style="text-align:center;"')); ?></div>
                                        <div>/</div>
                                        <div><?php echo $obj->inputNumber('age', array('readonly' => true, 'etc' => 'style="text-align:center; width:6em"')); ?></div>
                                        <div><?php echo $obj->lang['year']; ?></div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['phone']); ?> / <?php echo ucwords($obj->lang['mobilePhone']); ?></label>
                                <div class="col-xs-9">
                                    <div class="flex">
                                        <div class="consume"><?php echo $obj->inputText('insuredPhone', array('readonly' => true)); ?></div>
                                        <div>/</div>
                                        <div class="consume"> <?php echo $obj->inputText('insuredMobile', array('readonly' => true)); ?></div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['email']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputText('insuredEmail', array('readonly' => true)); ?>
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

                </div>
            </div>

            <!-- Form Detail -->
            <div style="clear:both; height:2em;"></div>
            <div class="div-table mnv-transaction transaction-detail" style="width:100%;   ">
                <div class="div-table-row">
                    <div class="div-table-col detail-col-header">
                        <?php echo ucwords($obj->lang['service']); ?>
                    </div>
                    <div class="div-table-col detail-col-header" style="width:80px; text-align:right;">
                        <?php echo ucwords($obj->lang['amount']); ?>
                    </div>
                    <div class="div-table-col detail-col-header" style="width:80px;">
                        <?php echo ucwords($obj->lang['unit']); ?>
                    </div>
                    <div class="div-table-col detail-col-header" style="width:130px; text-align:right;">
                        <?php echo ucwords($obj->lang['price']); ?>
                    </div>
                    <div class="div-table-col detail-col-header" style="width:150px; text-align:right;">
                        <?php echo ucwords($obj->lang['subtotal']); ?>
                    </div>
                    <div class="div-table-col detail-col-header  icon-col <?php echo $obj->hideOnDisabled(); ?>"></div>
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
                    $arrUnit = $arrDefaultUnit;

                    if ($i == $totalRows) {
                        $class = 'detail-row-template';
                        $overwrite = false;
                        $etc = 'disabled="disabled"';
                    } else {
                        $decimal = 0;
                        $inputnumber = 'inputnumber';

                        $_POST['hidDetailKey[]'] =  $rsDetail[$i]['pkey'];
                        $_POST['hidItemKey[]'] =  $rsDetail[$i]['itemkey'];
                        $_POST['itemName[]'] =  $rsDetail[$i]['itemname'];
                        $_POST['quantityValue[]'] =   $obj->formatNumber($rsDetail[$i]['qty']);
                        $_POST['priceValue[]'] =   $obj->formatNumber($rsDetail[$i]['priceinunit']);
                        $_POST['selUnit[]'] =  $rsDetail[$i]['unitkey'];
                        $_POST['detailSubtotal[]'] =   $obj->formatNumber($rsDetail[$i]['total']);
                        // $arrUnit = $obj->convertForCombobox($itemUnit->searchData('','',true, ' and ('.$itemUnit->tableName.'.statuskey = 1 )'),'pkey','name'); 
                        $arrUnit = $obj->convertForCombobox($item->getAvailableUnit($rsDetail[$i]['itemkey']), 'conversionunitkey', 'unitname');
                    }
                ?>

                    <div class="div-table-row <?php echo $class; ?>">
                        <div class="div-table-col detail-col-detail" style="padding:0">
                            <div class="div-table" style="width:100%">
                                <div class="div-table-row">
                                    <div class="div-table-col detail-col-detail">
                                        <?php echo $obj->inputText('itemName[]', array('overwritePost' => $overwrite, 'etc' => $etc)); ?>
                                        <?php echo $obj->inputHidden('hidItemKey[]', array('overwritePost' => $overwrite, 'etc' => $etc)); ?>
                                        <?php echo $obj->inputHidden('hidDetailKey[]', array('overwritePost' => $overwrite, 'etc' => $etc)); ?>
                                    </div>
                                    <div class="div-table-col detail-col-detail" style="width:80px;">
                                        <?php echo $obj->inputNumber('quantityValue[]', array('overwritePost' => $overwrite, 'etc' => 'style="text-align:right;" ' . $etc)); ?>
                                    </div>
                                    <div class="div-table-col detail-col-detail" style="width:80px;">
                                        <?php echo $obj->inputSelect('selUnit[]', $arrUnit, array('overwritePost' => $overwrite, 'etc' =>  $etc)); ?>
                                    </div>
                                    <div class="div-table-col detail-col-detail" style="width:130px;">
                                        <?php echo $obj->inputNumber('priceValue[]', array('overwritePost' => $overwrite, 'etc' => 'style="text-align:right;" ' . $etc)); ?>
                                    </div>
                                    <div class="div-table-col detail-col-detail" style="width:150px;">
                                        <?php echo $obj->inputNumber('detailSubtotal[]', array('overwritePost' => $overwrite, 'readonly' => true, 'etc' => 'style="text-align:right;" ' . $etc)); ?>
                                    </div>
                                </div>

                            </div>
                            <div class="div-table" style="width:100%;">
                                <div class="div-table-row">
                                    <div class="div-table-col detail-col-detail">
                                        <?php echo $obj->inputTextArea('detailDescription[]', array('overwritePost' => $overwrite, 'etc' => 'style="height:8em" placeholder="' . $obj->lang['description'] . '"')); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="div-table-col detail-col-detail icon-col align-top-adjust  <?php echo $obj->hideOnDisabled(); ?>">
                            <?php echo $obj->inputLinkButton('btnDeleteRows', '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button', 'etc' => 'tabIndex="- 1"')); ?>
                        </div>
                        <!--onClick="itemAdj.calculateTotal()"-->
                    </div>

                <?php }      ?>

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
                    <div class="div-table-row  form-group">
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

                    <div class="div-table-row  form-group   form-detail-field">
                        <div class="div-table-col-5" style="text-align:right; padding-top:2em;">
                            <?php echo ucwords($obj->lang['shippingFee']); ?>
                        </div>
                        <div class="div-table-col-5" style=" padding-top:2em;">
                            <?php echo $obj->inputNumber('shipmentFee', array('etc' => 'style="text-align:right;" ')); ?>
                        </div>
                        <div class="div-table-col"> </div>
                    </div>

                    <div class="div-table-row  form-group   form-detail-field">
                        <div class="div-table-col-5" style="text-align:right;">
                            <?php echo ucwords($obj->lang['others']); ?>
                        </div>
                        <div class="div-table-col-5">
                            <?php echo $obj->inputNumber('etcCost', array('etc' => 'style="text-align:right;"')); ?>
                        </div>
                        <div class="div-table-col"> </div>
                    </div>
                    <div class="div-table-row  form-group">
                        <div class="div-table-col-5" style="text-align:right;">
                            <?php echo ucwords($obj->lang['total']); ?>
                        </div>
                        <div class="div-table-col-5">
                            <?php echo $obj->inputNumber('total', array('readonly' => true, 'etc' => 'style="text-align:right;"')); ?>
                        </div>
                        <div class="div-table-col"> </div>
                    </div>
                    <div class="div-table-row  form-group">
                        <div class="div-table-col-5" style="text-align:right;"> </div>
                        <div class="div-table-col-5">
                            <div class="form-detail-button" style="float:right; text-align:right; padding-right:0; padding-top:0; " relalt="<?php echo ucwords($obj->lang['hideDetail']); ?>"> <?php echo ucwords($obj->lang['showDetail']); ?> </div>
                        </div>
                        <div class="div-table-col"> </div>
                    </div>

                </div>
                <div style="clear:both"></div>
            </div>


            <div class="form-button-margin"></div>
            <div class="form-button-panel"> <?php echo $obj->generateSaveButton(array(), true); ?> </div>
        </form>

        <?php echo $obj->showDataHistory(); ?>
    </div>
</body>

</html>