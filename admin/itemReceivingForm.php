<?php
require_once '../_config.php';
require_once '../_include-v2.php';

includeClass(array('ItemReceiving.class.php','WarehouseLayout.class.php'));
$itemReceiving = createObjAndAddToCol(new ItemReceiving());
//$item = createObjAndAddToCol(new Item());
// $itemUnit = createObjAndAddToCol(new ItemUnit());
$warehouse = createObjAndAddToCol(new Warehouse());
$customer = createObjAndAddToCol(new Customer());
$supplier = createObjAndAddToCol(new Supplier());
$currency = createObjAndAddToCol(new Currency());
$warehouseLayout = createObjAndAddToCol(new WarehouseLayout());
$transactionType = createObjAndAddToCol(new TransactionType());
$documentType = createObjAndAddToCol(new DocumentType());
$itemUnit = createObjAndAddToCol(new ItemUnit());

$obj = $itemReceiving;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
// some modules that have different security object from that of their class

if (!$security->isAdminLogin($securityObject, 10, true));

$formAction = 'itemReceivingList';

$isQuickAdd = (isset($_GET) && !empty($_GET['quickadd'])) ? true : false;

$editWarehouseInactiveCriteria = '';
$editCurrencyInactiveCriteria = '';
$editUnitInactiveCriteria = '';

$rsItemFile = array();
$rsDetail = array();
$rsWarehouseLayout = array();

$_POST['trDate'] = date('d / m / Y');
$_POST['trReceivedDate'] = date('d / m / Y');
$_POST['submissionDate'] = date('d / m / Y');
$_POST['invoiceDate'] = date('d / m / Y');
$_POST['blDate'] = date('d / m / Y');
$_POST['registrationDate'] = date('d / m / Y');

$rs = prepareOnLoadData($obj);


if (!empty($_GET['id'])) {
    $id = $_GET['id'];

    // $rsWarehouseLayout = $warehouseLayout->getDataByWarehouse($rs[0]['warehousekey']);
    
    $rsDetail = $obj->getDetailWithRelatedInformation($id);

    $_POST['trDate'] = $obj->formatDBDate($rs[0]['trdate'], 'd / m / Y');
    $_POST['trReceivedDate'] = $obj->formatDBDate($rs[0]['receiveddate'], 'd / m / Y');

    if (!empty($rs[0]['customerkey'])) {
        $rsCustomer = $customer->getDataRowById($rs[0]['customerkey']);
        $_POST['hidCustomerKey'] = $rsCustomer[0]['pkey'];
        $_POST['customerName'] = $rsCustomer[0]['name'];
    }

    if (!empty($rs[0]['supplierkey'])) {
        $rsSupplier = $supplier->getDataRowById($rs[0]['supplierkey']);
        $_POST['hidSupplierKey'] = $rsSupplier[0]['pkey'];
        $_POST['supplierName'] = $rsSupplier[0]['name'];
    }

    if (!empty($rs[0]['shipperkey'])) {
        $rsShipper = $supplier->getDataRowById($rs[0]['shipperkey']);
        $_POST['hidShipperKey'] = $rsShipper[0]['pkey'];
        $_POST['shipperName'] = $rsShipper[0]['name'];
    }

    $_POST['selWarehouseKey'] = $rs[0]['warehousekey'];
    $_POST['selWarehouseLayoutKey'] = $rs[0]['warehouselayoutkey'];
    $_POST['selCurrentWarehouseLayoutKey'] = $rs[0]['warehouselayoutkey'];
    $_POST['trDesc'] = $rs[0]['trdesc'];
    $_POST['selDocumentType'] = $rs[0]['documenttype'];
    $_POST['submissionNumber'] = $rs[0]['submissionnumber'];
    $_POST['submissionDate'] = $obj->formatDBDate($rs[0]['submissiondate'], 'd / m / Y');
    $_POST['invoiceNumber'] = $rs[0]['invoicenumber'];
    $_POST['invoiceDate'] = $obj->formatDBDate($rs[0]['invoicedate'], 'd / m / Y');
    $_POST['blNumber'] = $rs[0]['blnumber'];
    $_POST['blDate'] = $obj->formatDBDate($rs[0]['bldate'], 'd / m / Y');
    $_POST['registrationNumber'] = $rs[0]['registrationnumber'];
    $_POST['registrationDate'] = $obj->formatDBDate($rs[0]['registrationdate'], 'd / m / Y');
    $_POST['valueType'] = $rs[0]['valuetype'];

    if ($obj->useStorage) {
        $rsFileDetail = $obj->getFileDetail($id);
    } else {
        $rsItemFile = array();
        if (!empty($rs[0]['file'])) {
            $rsItemFile[0]['file'] =  $rs[0]['file'];

            $sourcePath = $obj->defaultDocUploadPath . $obj->uploadFileFolder . $id;
            $destinationPath = $obj->uploadTempDoc . $obj->uploadFileFolder . $id;
            $obj->deleteAll($destinationPath);

            if (!is_dir($destinationPath))
                mkdir($destinationPath,  0755, true);

            $obj->fullCopy($sourcePath, $destinationPath);
        }
    }

    $editWarehouseInactiveCriteria = ' or ' . $warehouse->tableName . '.pkey = ' . $obj->oDbCon->paramString($rs[0]['warehousekey']);
    
    $editCurrencyInactiveCriteria = ' or ' . $currency->tableName . '.pkey = ' . $obj->oDbCon->paramString($rs[0]['currencykey']);
}
$rsWarehouse = $warehouse->searchData('','',true,' and ('.$warehouse->tableName.'.statuskey = 1' .$editWarehouseInactiveCriteria.')');
$editWarehouseLayoutInactiveCriteria = '';
$obj->setLog($rs, true);
if (!empty($rs[0]['warehouselayoutkey'])) {
    $editWarehouseLayoutInactiveCriteria = ' and ' . $warehouseLayout->tableName . '.warehousekey = ' . $obj->oDbCon->paramString($rs[0]['warehousekey']);
} else {
    $editWarehouseLayoutInactiveCriteria = ' and ' . $warehouseLayout->tableName . '.warehousekey = ' . $obj->oDbCon->paramString($rsWarehouse[0]['pkey']);    
}

$obj->setLog($editWarehouseLayoutInactiveCriteria, true);

$rsWarehouseLayout = $warehouseLayout->searchData('','',true,' and ('.$warehouseLayout->tableName.'.statuskey = 1 and '.$warehouseLayout->tableName.'.istransit = 1'.$editWarehouseLayoutInactiveCriteria.')');

$arrStatus = $obj->generateComboboxOpt(array('data' => $obj->getAllStatus(), 'label' => 'status'));
$arrWarehouse = $warehouse->generateComboboxOpt(null, array('criteria' => ' and (' . $warehouse->tableName . '.statuskey = 1' . $editWarehouseInactiveCriteria . ')'));
$arrWarehouse = $class->convertForCombobox($rsWarehouse,'pkey','name'); 
$arrTransactionType = $transactionType->generateComboboxOpt(null,array('criteria' =>' and ('.$transactionType->tableName.'.statuskey = 1 )')); 
$arrDocumentType = $documentType->generateComboboxOpt(null,array('criteria' =>' and ('.$documentType->tableName.'.statuskey = 1 )')); 
$arrUnit = $itemUnit->generateComboboxOpt(null, array('criteria' => ' and (' . $itemUnit->tableName . '.statuskey = 1 ' . $editUnitInactiveCriteria . ')'));

$arrCurrency = $currency->generateComboboxOpt(null, array('criteria' => ' and (' . $currency->tableName . '.statuskey = 1' . $editCurrencyInactiveCriteria . ')'));
$arrWarehouseLayout = $obj->convertForCombobox($rsWarehouseLayout, 'pkey', 'name');
// $obj->setLog($rsWarehouseLayout, true);

//$arrDefaultUnit = $itemUnit->generateComboboxOpt(null, array('criteria' => ' and (' . $itemUnit->tableName . '.statuskey = 1 )'));

// $arrDocumentType = array(array('pkey' => 1, 'name' => 'BC 1.6'));
// $arrDocumentType = $obj->generateComboboxOpt(array('data' => $arrDocumentType, 'label' => 'name'));
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

    <title></title>

    <?php  //include_once $class->defaultDocJsPath.'test.js.php'; 
    ?>

    <script type="text/javascript">
        jQuery(document).ready(function() {
            var tabID = <?php echo ($isQuickAdd) ?  $_GET['tabID'] :  'selectedTab.newPanel[0].id';  ?>;
            var tablekey = <?php echo $obj->getTableKeyAndObj($obj->tableName, array('key'))['key']; ?>;

            var fileUpload = {
                uploadFolder: "<?php echo $obj->uploadFileFolder; ?>",
                uploaderTarget: "item-file-uploader",
                rsFile: <?php echo json_encode($rsItemFile); ?>,
            };

            var itemReceiving = new ItemReceiving(tabID, fileUpload);

            prepareHandler(itemReceiving);

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
                supplierName: {
                    validators: {
                        notEmpty: {
                            message: phpErrorMsg.supplier[1]
                        },
                    }
                },
                shipperName: {
                    validators: {
                        notEmpty: {
                            message: phpErrorMsg.shipper[1]
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
            <?php echo $obj->inputHidden('selCurrentWarehouseLayoutKey'); ?>

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
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['warehouseLayout']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputSelect('selWarehouseLayoutKey', $arrWarehouseLayout); ?>
                                </div>
                            </div>
                            

                            <!-- <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['receivedDate']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputDate('trReceivedDate'); ?>
                                </div>
                            </div> -->

                            <div class="form-group coa-link">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['customer']); ?></label>
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
                                                'data' => array('action' => 'searchData')
                                            )
                                        )
                                    );
                                    ?>
                                </div>
                            </div>

                            <div class="form-group coa-link">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['supplier']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputAutoComplete(
                                        array(
                                            'objRefer' => $supplier,
                                            'element' => array(
                                                'value' => 'supplierName',
                                                'key' => 'hidSupplierKey'
                                            ),
                                            'source' => array(
                                                'url' => 'ajax-supplier.php',
                                                'data' => array('action' => 'searchData')
                                            )
                                        )
                                    );
                                    ?>
                                </div>
                            </div>

                            <div class="form-group coa-link">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['shipper']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputAutoComplete(
                                        array(
                                            'objRefer' => $supplier,
                                            'element' => array(
                                                'value' => 'shipperName',
                                                'key' => 'hidShipperKey'
                                            ),
                                            'source' => array(
                                                'url' => 'ajax-supplier.php',
                                                'data' => array('action' => 'searchData')
                                            )
                                        )
                                    );
                                    ?>
                                </div>
                            </div>


                        </div>
                    </div>
                    <div class="div-table-col">
                        <div class="div-tab-panel">
                            <div class="div-table-caption border-blue"><?php echo ucwords($obj->lang['document']); ?></div>

                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['documentType']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputSelect('selDocumentType', $arrDocumentType); ?>
                                    <!-- <?php echo $obj->inputText('documentType'); ?> -->
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['submissionNumber'] . ' / ' . $obj->lang['submissionDate']); ?></label>
                                <div class="col-xs-9">
                                    <div class="flex">
                                        <div class="consume"><?php echo $obj->inputText('submissionNumber'); ?></div>
                                        <div>/</div>
                                        <div class="consume"><?php echo $obj->inputDate('submissionDate', array('etc' => 'style="text-align:center;"')); ?></div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['invoiceNumber'] . ' / ' . $obj->lang['invoiceDate']); ?></label>
                                <div class="col-xs-9">
                                    <div class="flex">
                                        <div class="consume"><?php echo $obj->inputText('invoiceNumber'); ?></div>
                                        <div>/</div>
                                        <div class="consume"><?php echo $obj->inputDate('invoiceDate', array('etc' => 'style="text-align:center;"')); ?></div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['blNumber'] . ' / ' . $obj->lang['blDate']); ?></label>
                                <div class="col-xs-9">
                                    <div class="flex">
                                        <div class="consume"><?php echo $obj->inputText('blNumber'); ?></div>
                                        <div>/</div>
                                        <div class="consume"><?php echo $obj->inputDate('blDate', array('etc' => 'style="text-align:center;"')); ?></div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['registerNumber'] . ' / ' . $obj->lang['registerDate']); ?></label>
                                <div class="col-xs-9">
                                    <div class="flex">
                                        <div class="consume"><?php echo $obj->inputText('registrationNumber'); ?></div>
                                        <div>/</div>
                                        <div class="consume"><?php echo $obj->inputDate('registrationDate', array('etc' => 'style="text-align:center;"')); ?></div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['currency']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputSelect('selCurrency', $arrCurrency); ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['valueType']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputText('valueType'); ?>
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
                            <div class="div-table-caption border-red"><?php echo ucwords($obj->lang['files']); ?></div>

                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['documentFiles']); ?></label>
                                <div class="col-xs-9">
                                    <!-- file uploader -->
                                    <div class="item-file-uploader">
                                        <ul class="file-list"></ul>
                                        <div style="clear:both; height:1em; "></div>
                                        <div class="file-uploader">
                                            <noscript>
                                                <p>Please enable JavaScript to use file uploader.</p>
                                            </noscript>
                                        </div>
                                    </div>
                                    <!-- file uploader -->
                                    <?php if (!empty($rs) && in_array($rs[0]['statuskey'], array(2, 3))) {
                                        echo $obj->inputButton('btnUpdateFile', $obj->lang['update'], array('allowedStatusForEdit' => array(1, 2, 3), 'class' => 'btn btn-primary btn-second-tone'));
                                    } ?>
                                </div>
                            </div>

                        </div>


                    </div>
                </div>
            </div>
            <div class="div-table mnv-transaction mnv-job transaction-detail" style="width:100%; border-bottom:1px solid #333; ">
                <div class="div-table-row"> 
                    <div class="div-table-col" style="padding:0">
                        <div class="div-table" style="width:100%">
                <div class="div-table-row">
                    <!-- <div class="div-table-col detail-col-header" style="width:170px;"><?php echo ucwords($obj->lang['itemBarcode']); ?></div> -->
                    <div class=" div-table-col detail-col-header" style="width:180px;"><?php echo ucwords($obj->lang['itemCode']); ?></div>
                    <div class=" div-table-col detail-col-header"><?php echo ucwords($obj->lang['itemName']); ?></div>
                    <div class=" div-table-col detail-col-header" style="width:60px;text-align:right;"><?php echo ucwords($obj->lang['mililiter']); ?></div>
                    <div class=" div-table-col detail-col-header" style="width:170px;"><?php echo ucwords($obj->lang['brand']); ?></div>
                    <div class=" div-table-col detail-col-header" style="width:120px;"><?php echo ucwords($obj->lang['kind']); ?></div>
                    <div class=" div-table-col detail-col-header" style="width:70px;text-align:right;"><?php echo ucwords($obj->lang['qtyCarton']); ?></div>
                    <div class=" div-table-col detail-col-header" style="width:100px;text-align:right;"><?php echo ucwords($obj->lang['qtyPackage']); ?></div>
                    <div class=" div-table-col detail-col-header" style="width:80px; text-align:right;"><?php echo ucwords($obj->lang['qty']); ?></div>
                    <div class=" div-table-col detail-col-header" style="width:80px;text-align:right;"><?php echo ucwords($obj->lang['alcohol'] . ' %'); ?></div>
                    <!-- <div class=" div-table-col detail-col-header" style="width:120px;"><?php echo ucwords($obj->lang['country']); ?></div> -->
                    <!-- <div class=" div-table-col detail-col-header" style="width:100px;"><?php echo ucwords($obj->lang['unit']); ?></div> -->
                    <div class="div-table-col detail-col-header" style="width:100px;text-align:right;"><?php echo ucwords($obj->lang['value']); ?></div>
                    <div class="div-table-col detail-col-header  icon-col <?php echo $obj->hideOnDisabled(); ?>"></div>
                        </div>
                        </div>
                    </div>
                </div>
        
                <?php
                $totalRows = count($rsDetail);
                $obj->setLog($rsDetail, true);

                for ($i = 0; $i <= $totalRows; $i++) {

                    $class =  'transaction-detail-row';
                    $overwrite = true;
                    $etc = '';
                    $txtSN = '';
                    $disable = '';
                //    $arrUnit = $arrDefaultUnit;

                    if ($i == $totalRows) {
                        $class = 'detail-row-template';
                        $overwrite = false;
                        $etc = 'disabled="disabled"';
                        $disable = 'disabled="disabled"';
                        $baseunitname = 'Pcs';
                    } else {

                        $baseunitname = $rsDetail[$i]['baseunitname'];

                        $_POST['hidDetailKey[]'] =  $rsDetail[$i]['pkey'];
                        $_POST['itemDetailBarcode[]'] = $rsDetail[$i]['itembarcode'];
                        $_POST['itemDetailCode[]'] = $rsDetail[$i]['itemcode'];
                        $_POST['itemDetailName[]'] = $rsDetail[$i]['itemname'];
                        $_POST['selUnit[]'] =  $rsDetail[$i]['unit'];
                        $_POST['hs[]'] = $rsDetail[$i]['hs'];
                        $_POST['countryOfOriginId[]'] = $rsDetail[$i]['countryoforiginid'];
                        $_POST['itemCategoryName[]'] = $rsDetail[$i]['itemcategory'];
                        $_POST['packagingName[]'] = $rsDetail[$i]['packaging'];
                        $_POST['facility[]'] = $rsDetail[$i]['facility'];
                        $_POST['orderList[]'] = $rsDetail[$i]['orderlist'];
                        $_POST['qty[]'] =   $obj->formatNumber($rsDetail[$i]['qty']);
                        $_POST['category[]'] = $rsDetail[$i]['category'];
                        $_POST['alcoholContent[]'] =   $obj->formatNumber($rsDetail[$i]['alcoholcontent'], 2);
                        $_POST['mililiter[]'] =   $obj->formatNumber($rsDetail[$i]['mililiter']);
                        $_POST['qtyCarton[]'] =   $obj->formatNumber($rsDetail[$i]['qtycarton']);
                        $_POST['qtyPackage[]'] =   $obj->formatNumber($rsDetail[$i]['qtypackage']);
                        $_POST['amount[]'] =   $obj->formatNumber($rsDetail[$i]['amount']);
                        $_POST['hidDetailTypeKey[]'] = $rsDetail[$i]['typekey'];
                        $_POST['detailType[]'] = $rsDetail[$i]['typename'];
                        $_POST['hidDetailBrandKey[]'] = $rsDetail[$i]['brandkey'];
                        $_POST['brandName[]'] = $rsDetail[$i]['brandname'];
                        $_POST['label[]'] = $rsDetail[$i]['label'];
                        $_POST['selTransactionType[]'] = $rsDetail[$i]['transactiontypekey'];
                        $_POST['containerNumber[]'] = $rsDetail[$i]['containernumber'];
                        $_POST['containerType[]'] = $rsDetail[$i]['containertype'];
                        $_POST['containerSize[]'] = $rsDetail[$i]['containersize'];
                        $_POST['containerKind[]'] = $rsDetail[$i]['containerkind'];

                        //$arrUnit = $obj->convertForCombobox($item->getAvailableUnit($rsDetail[$i]['itemkey']), 'conversionunitkey', 'unitname');
                    }


                ?>

                    <div class="div-table-row odd-style-adjustment <?php echo $class; ?> ">
                        <div class="div-table-col" style="padding:0">
                            <div class="div-table" style="width:100%">
                                <div class="div-table-row">
                                    <div class="div-table-col" style="padding:0">
                                        <div class="div-table" style="width: 100%">
                                            <div class="div-table-row">
                                                <!-- <div class="div-table-col detail-col-detail" style="width:170px;" style="vertical-align:top;">
                                                    <?php echo $obj->inputText('itemDetailBarcode[]', array('overwritePost' => $overwrite, 'etc' => $etc,  'class' => 'form-control mnv-barcode-input', 'disabled' =>  $disable)); ?>
                                                </div> -->
                                                <div class="div-table-col detail-col-detail" style="width:180px;" style="vertical-align:top;">
                                                    <?php echo $obj->inputHidden('hidDetailKey[]', array('overwritePost' => $overwrite, 'etc' => $etc, 'disabled' => $disabled)); ?>
                                                    <?php echo $obj->inputText('itemDetailCode[]', array('overwritePost' => $overwrite, 'etc' => $etc,  'class' => 'form-control mnv-barcode-input', 'disabled' =>  $disable)); ?>
                                                </div>
                                                <div class="div-table-col detail-col-detail" style="vertical-align:top;">
                                                    <?php echo $obj->inputHidden('hidItemDetailKey[]', array('overwritePost' => $overwrite, 'etc' => $etc, 'disabled' => $disabled)); ?>
                                                    <?php echo $obj->inputText('itemDetailName[]', array('overwritePost' => $overwrite, 'etc' => $etc,  'class' => 'form-control mnv-barcode-input', 'disabled' =>  $disable)); ?>
                                                </div>
                                                <div class="div-table-col detail-col-detail" style="vertical-align:top; width:60px; "><?php echo $obj->inputNumber('mililiter[]', array('overwritePost' => $overwrite, 'etc' => 'style="text-align:right;" ' . $etc, 'disabled' =>  $disable)); ?></div>
                                                <div class="div-table-col detail-col-detail" style="width:170px;" style="vertical-align:top;">
                                                    <?php echo $obj->inputHidden('hidDetailBrandKey[]', array('overwritePost' => $overwrite, 'etc' => $etc, 'disabled' => $disabled)); ?>
                                                    <?php echo $obj->inputText('brandName[]', array('overwritePost' => $overwrite, 'etc' => $etc,  'class' => 'form-control', 'disabled' =>  $disable)); ?>
                                                </div>
                                                <div class="div-table-col detail-col-detail" style="width:120px;" style="vertical-align:top;">
                                                    <!-- <?php echo $obj->inputText('detailType[]', array('overwritePost' => $overwrite, 'etc' => $etc,  'class' => 'form-control', 'disabled' =>  $disable)); ?> -->
                                                    <?php echo $obj->inputHidden('hidDetailTypeKey[]', array('overwritePost' => $overwrite, 'etc' => $etc, 'disabled' => $disabled)); ?>
                                                    <?php echo $obj->inputText('detailType[]', array('overwritePost' => $overwrite, 'etc' => $etc,  'class' => 'form-control', 'disabled' =>  $disable)); ?>
                                                </div>
                                                <div class="div-table-col detail-col-detail" style="vertical-align:top; width:70px; "><?php echo $obj->inputNumber('qtyCarton[]', array('overwritePost' => $overwrite, 'etc' => 'style="text-align:right;" ' . $etc, 'disabled' =>  $disable)); ?></div>
                                                <div class="div-table-col detail-col-detail" style="vertical-align:top; width:100px; "><?php echo $obj->inputNumber('qtyPackage[]', array('overwritePost' => $overwrite, 'etc' => 'style="text-align:right;" ' . $etc, 'disabled' =>  $disable)); ?></div>
                                                <div class="div-table-col detail-col-detail" style="vertical-align:top; width:80px; "><?php echo $obj->inputNumber('qty[]', array('readonly' => true,'overwritePost' => $overwrite, 'etc' => 'style="text-align:right;" ' . $etc, 'disabled' =>  $disable)); ?></div>
                                                <div class="div-table-col detail-col-detail" style="vertical-align:top; width:80px; "><?php echo $obj->inputDecimal('alcoholContent[]', array('overwritePost' => $overwrite, 'etc' => 'style="text-align:right;" ' . $etc, 'disabled' =>  $disable)); ?></div>
                                                <div class="div-table-col detail-col-detail" style="vertical-align:top; width:100px;"><?php echo $obj->inputDecimal('amount[]', array('overwritePost' => $overwrite, 'etc' => 'style="text-align:right;" ' . $etc, 'disabled' =>  $disable)); ?></div>
                                                <!-- <div class="div-table-col detail-col-detail" style="width:120px;" style="vertical-align:top;">
                                                    <?php echo $obj->inputHidden('hidDetailCountryKey[]', array('overwritePost' => $overwrite, 'etc' => $etc, 'disabled' => $disabled)); ?>
                                                    <?php echo $obj->inputText('countryOfOriginId[]', array('overwritePost' => $overwrite, 'etc' => $etc,  'class' => 'form-control', 'disabled' =>  $disable)); ?>
                                                </div> -->
                                            </div>
                                        </div>
                                    </div>
                                    <div class="div-table-col detail-col-detail icon-col <?php echo $obj->hideOnDisabled(); ?>"><?php echo $obj->inputLinkButton('btnDeleteRows', '<i class="fas fa-times"></i>', array('etc' => 'tabIndex="-1"', 'class' => 'btn btn-link remove-button')); ?></div>
                                </div>
                            </div>
                            <div class="div-table" style="width:100%"> 
                                 <div class="div-table-row">
                                    <div class="div-table-col" style="padding:0">
                                         <?php echo $obj->inputText('label[]', array('readonly' => true, 'overwritePost' => $overwrite, 'etc' => $etc . ' placeholder="' . $obj->lang['label'] . '" ', 'add-class' => 'label-style', 'disabled' =>  $disable)); ?>
                                        <div class="flex" style="margin-top: 1em;">
                                            <div>
                                                <div style="font-weight:bold; padding-left: 0.5em;"><?php echo $obj->lang['hs']; ?></div>
                                                <div>
                                                    <?php echo $obj->inputText('hs[]', array('overwritePost' => $overwrite, 'etc' => $etc . ' placeholder="' . $obj->lang['hs'] . '" ', 'add-class' => 'label-style', 'disabled' =>  $disable)); ?>
                                                </div>
                                            </div>
                                            <div>
                                                <div style="font-weight:bold; padding-left: 0.5em;"><?php echo $obj->lang['transactionType']; ?></div>
                                                <div>
                                                    <?php echo $obj->inputSelect('selTransactionType[]', $arrTransactionType, array('overwritePost' => $overwrite, 'etc' => $etc . ' placeholder="' . $obj->lang['transactionType'] . '" ', 'add-class' => 'label-style', 'disabled' =>  $disable)); ?>
                                                </div>
                                            </div>
                                            <div style="width:70px">
                                                <div style="font-weight:bold; padding-left: 0.5em;"><?php echo $obj->lang['gol']; ?></div>
                                                <div>
                                                    <?php echo $obj->inputText('category[]', array('overwritePost' => $overwrite, 'etc' => $etc . ' placeholder="' . $obj->lang['gol'] . '" ', 'add-class' => 'label-style', 'disabled' =>  $disable)); ?>
                                                </div>
                                            </div>
                                            <div>
                                                <div style="font-weight:bold; padding-left: 0.5em;"><?php echo $obj->lang['unit']; ?></div>
                                                <div>
                                                    <?php echo $obj->inputSelect('selUnit[]', $arrUnit, array('overwritePost' => $overwrite, 'etc' => $etc . ' placeholder="' . $obj->lang['unit'] . '" ', 'add-class' => 'label-style', 'disabled' =>  $disable)); ?>
                                                </div>
                                            </div>
                                            <div>
                                                <div style="font-weight:bold; padding-left: 0.5em;"><?php echo $obj->lang['packaging']; ?></div>
                                                <div>
                                                    <?php echo $obj->inputText('packagingName[]', array('overwritePost' => $overwrite, 'etc' => $etc . ' placeholder="' . $obj->lang['packaging'] . '" ', 'add-class' => 'label-style', 'disabled' =>  $disable)); ?>
                                                </div>
                                            </div>
                                            <div>
                                                <div style="font-weight:bold; padding-left: 0.5em;"><?php echo $obj->lang['country']; ?></div>
                                                <div>
                                                    <?php echo $obj->inputHidden('hidDetailCountryKey[]', array('overwritePost' => $overwrite, 'etc' => $etc, 'add-class' => 'label-style', 'disabled' =>  $disable)); ?>
                                                    <?php echo $obj->inputText('countryOfOriginId[]', array('overwritePost' => $overwrite, 'etc' => $etc . ' placeholder="' . $obj->lang['country'] . '" ', 'add-class' => 'label-style', 'disabled' =>  $disable)); ?>
                                                </div>
                                            </div>
                                        </div>  
                                    </div>
                                    <div class="div-table-col" style="padding:0; width:1em;">
                                    </div>
                                    <div class="div-table-col" style="padding:0; width:20em;">
                                        <b><?php echo $obj->lang['container']; ?></b><br>
                                        <div class="flex">
                                            <div class="consume">
                                                <?php echo $obj->inputText('containerNumber[]', array('overwritePost' => $overwrite, 'etc' => $etc . ' placeholder="' . $obj->lang['containerNumber'] . '" ', 'add-class' => 'label-style', 'disabled' =>  $disable)); ?>
                                            </div>
                                        </div>
                                         <div class="flex">
                                            <div class="consume">
                                                <?php echo $obj->inputText('containerSize[]', array('overwritePost' => $overwrite, 'etc' => $etc . ' placeholder="' . $obj->lang['size'] . '" ', 'add-class' => 'label-style', 'disabled' =>  $disable)); ?>
                                            </div>
                                            <div class="consume">
                                                <?php echo $obj->inputText('containerType[]', array('overwritePost' => $overwrite, 'etc' => $etc . ' placeholder="' . $obj->lang['containerType'] . '" ', 'add-class' => 'label-style', 'disabled' =>  $disable)); ?>

                                            </div>
                                        </div>
                                    </div>
                                 </div>
                            </div>
                        </div>
                    </div>

                <?php  } ?>

             </div>
            
            <div style=" clear:both; height:1em;"></div>
            <div style="float:left; display:inline-block;"><?php echo $obj->inputButton('btnAddRow', $obj->lang['addRows'], array('class' => 'btn btn-primary btn-second-tone')); ?></div>

            <div class="form-button-margin"></div>
            <div class="form-button-panel">
                <?php echo $obj->generateSaveButton(array(), true); ?>
            </div>

        </form>
        <?php echo $obj->showDataHistory(); ?>
    </div>
</body>

</html>