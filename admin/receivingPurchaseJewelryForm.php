<?php
require_once '../_config.php';
require_once '../_include-v2.php';

includeClass(array('PurchaseOrderJewelry.class.php', 'ReceivingPurchaseJewelry.class.php', 'Supplier.class.php', 'TermOfPayment.class.php', 'PaymentMethod.class.php', 'ItemUnit.class.php'));
$receivingPurchaseJewelry = createObjAndAddToCol(new ReceivingPurchaseJewelry());
$itemUnit = createObjAndAddToCol(new ItemUnit());
$purchaseOrderJewelry = createObjAndAddToCol(new PurchaseOrderJewelry());
$supplier = createObjAndAddToCol(new Supplier());

$obj = $receivingPurchaseJewelry;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
// some modules that have different security object from that of their class

if (!$security->isAdminLogin($securityObject, 10, true));

$formAction = 'receivingPurchaseJewelryList';

$parentFileName = $_GET['fileName'];
$parentPanelId = $_GET['selectedPanelId'];
$parentTitle = $_GET['title'];

$rsReceivingPurchaseDetail = array();

$_POST['trDate'] = date('d / m / Y');

$rs = prepareOnLoadData($obj);

$arrSelItemPurchaseOrder = array();
$rsPOJewelryDetail = array();

$arrItemPO = array();

if (!empty($_GET['id'])) {
    $id = $_GET['id'];

    $rsReceivingPurchaseDetail = $obj->getDetailWithRelatedInformation($id);

    $rsPOJewelryDetail = $purchaseOrderJewelry->getDetailWithRelatedInformation($rs[0]['refkey']);

    $_POST['trDate'] = $obj->formatDBDate($rs[0]['trdate'], 'd / m / Y');
    $_POST['trDesc'] = $rs[0]['trdesc'];

    if (!empty($rs[0]['supplierkey'])) {
        $rsSupplier = $supplier->getDataRowById($rs[0]['supplierkey']);
        $_POST['supplierName'] = $rsSupplier[0]['name'];
        $_POST['hidSupplierKey'] = $rsSupplier[0]['pkey'];
    }

    $rsPurchaseOderJewelry = $purchaseOrderJewelry->getDataRowById($rs[0]['refkey']);
    $_POST['hidPurchaseOrderKey'] = $rsPurchaseOderJewelry[0]['pkey'];
    $rsSupplier = $supplier->getDataRowById($rsPurchaseOderJewelry[0]['supplierkey']);
    $_POST['purchaseOrder'] = $rsPurchaseOderJewelry[0]['code'] . ' - ' . $rsSupplier[0]['code'];
    $_POST['shipmentFee'] = $obj->formatNumber($rs[0]['shipmentfee']);
    $_POST['balance'] = $obj->formatNumber($rs[0]['balance']);
    $_POST['totalPayment'] = $obj->formatNumber($rs[0]['totalpayment']);

    
}

$arrStatus = $obj->convertForCombobox($obj->getAllStatus(), 'pkey', 'status');
$arrUnit = $class->convertForCombobox($itemUnit->searchData('', '', true, ' and (' . $itemUnit->tableName . '.statuskey = 1 )'), 'pkey', 'name');

for($i=0; $i<count($rsPOJewelryDetail); $i++) 
{
    $rsPOJewelryDetail[$i]['itemnamenumber'] = $obj->formatNumber($rsPOJewelryDetail[$i]['number'],0) . ' - ' . $rsPOJewelryDetail[$i]['itemname'];
}

$arrSelItemPurchaseOrder = $class->convertForCombobox($rsPOJewelryDetail, 'pkey', 'itemnamenumber');

?>

<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title></title>

    <script type="text/javascript">


        jQuery(document).ready(function () {
            var tabID = selectedTab.newPanel[0].id;
	 	    var tablekey = <?php echo $obj->getTableKeyAndObj($obj->tableName ,array('key'))['key']; ?>;  
	 	    var labelWeight = <?php $labelWeight = $obj->loadSetting('labelWeight'); echo (empty($labelWeight)) ? '0' : $labelWeight; ?>;  
       
              var varConstant = {   
                            TABLEKEY : tablekey,
                            LABEL_WEIGHT : labelWeight
                            };
            
            receivingPurchaseJewelry = new ReceivingPurchaseJewelry(tabID, <?php echo json_encode($rs); ?>,varConstant);
            prepareHandler(receivingPurchaseJewelry);

            var fieldValidation = {
                code: {
                    validators: {
                        notEmpty: {
                            message: phpErrorMsg.code[1]
                        },
                    }
                },

                purchaseOrder: {
                    validators: {
                        notEmpty: {
                            message: phpErrorMsg.purchaseOrder[1]
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
                            <div class="div-table-caption border-orange">
                                <?php echo ucwords($obj->lang['generalInformation']); ?></div>
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
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['date']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputDate('trDate'); ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <label
                                    class="col-xs-3 control-label"><?php echo ucwords($obj->lang['poCode']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputAutoComplete(
                                        array( 
                                            'revalidateField' => true,
                                            'element' => array(
                                                'value' => 'purchaseOrder',
                                                'key' => 'hidPurchaseOrderKey'
                                            ),
                                            'source' => array(
                                                'url' => 'ajax-purchase-order-jewelry.php',
                                                'data' => array('action' => 'searchData','isfullreceive' => 0)
                                            ),
                                            'callbackFunction' => 'getTabObj().importData()'
                                        )
                                    );
                                    ?>

                                </div>
                            </div>
                            <!-- <div class="form-group">
                                <label
                                    class="col-xs-3 control-label"><?php echo ucwords($obj->lang['shippingCourier']); ?></label>
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
                                            'popupForm' => array(
                                                'url' => 'supplierForm.php',
                                                'element' => array(
                                                    'value' => 'supplierName',
                                                    'key' => 'hidSupplierKey'
                                                ),
                                                'width' => '1000px',
                                                'title' => ucwords($obj->lang['add'] . ' - ' . $obj->lang['supplier'])
                                            ),
                                            'callbackFunction' => 'getTabObj().updateTOP()'
                                        )
                                    );
                                    ?>
                                </div>
                            </div> -->
                        </div>
                    </div>

                    <div class="div-table-col">
                        <div class="div-tab-panel">
                            <div class="div-table-caption border-blue"><?php echo ucwords($obj->lang['note']); ?></div>
                            <div class="form-group">
                                <div class="col-xs-12">
                                    <?php echo $obj->inputTextArea('trDesc', array('etc' => 'style="height:10em;"')); ?>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <div class="div-table mnv-transaction transaction-detail purchase-receive-detail" style="width:100%; border-bottom:1px solid #333; ">
            
                <div class="div-table-row">
                    <div class="div-table-col detail-col-header"><?php echo ucwords($obj->lang['itemType']); ?></div>
                    <div class="div-table-col detail-col-header"><?php echo ucwords($obj->lang['itemName']); ?></div>
                    <div class="div-table-col detail-col-header" style="width:90px; text-align:right;">
                        <?php echo ucwords($obj->lang['orderedQty']); ?></div>
                    <!-- <div class="div-table-col detail-col-header" style="width:80px; text-align:right;">
                        <?php echo ucwords($obj->lang['outstanding']); ?></div> -->
                        <div class="div-table-col detail-col-header" style="width:90px; text-align:right;">
                            <?php echo ucwords($obj->lang['received']); ?></div>
                    <div class="div-table-col detail-col-header" style="width:50px;"></div> 
                     <div class="div-table-col detail-col-header" style="width:90px; text-align:right;">
                        <?php echo ucwords($obj->lang['orderedQty']); ?></div>
                    <!-- <div class="div-table-col detail-col-header" style="width:120px; text-align:right;">
                        <?php echo ucwords($obj->lang['outstanding']); ?></div> -->
                    <div class="div-table-col detail-col-header" style="width:90px; text-align:right;">
                        <?php echo ucwords($obj->lang['received']); ?>
                    </div>
                    <div class="div-table-col detail-col-header" style="width:50px;"></div> 
                    <div class="div-table-col detail-col-header" style="width:90px; text-align:right;"><?php echo ucwords('GW (Gr)'); ?></div>
                    <div class="div-table-col detail-col-header" style="width:90px; text-align:right;"><?php echo ucwords('Total GW (Gr)'); ?></div>
                    <div class="div-table-col detail-col-header" style="width:130px;">
                        <?php echo ucwords($obj->lang['packaging']); ?>
                    </div>
                    <!-- <div class="div-table-col detail-col-header" style="width:80px;"></div> -->
                    <div class="div-table-col detail-col-header  <?php echo $obj->hideOnDisabled(); ?>  icon-col"></div>
                </div>

                <?php

                $totalRows = count($rsReceivingPurchaseDetail);

                for ($i = 0; $i <= $totalRows; $i++) {

                    $class = 'transaction-detail-row';
                    $overwrite = true;
                    $etc = '';

                    $optionRows = '';

                    $rsItemDetail = array();
                    $totalDetailRows = 0;

                    if ($i == $totalRows) {
                        $class = 'detail-row-template';
                        $overwrite = false;
                        $etc = 'disabled="disabled"';
                        $unitname = '';
                    } else {
                        $decimal = 0;
                        $inputnumber = 'inputnumber';

                        $unitname = $rsReceivingPurchaseDetail[$i]['baseunitname'];
                        $_POST['hidDetailKey[]'] = $rsReceivingPurchaseDetail[$i]['pkey'];
                        $_POST['hidBaseUnitKey[]'] = $rsReceivingPurchaseDetail[$i]['baseunitkey'];
                        $_POST['selItemPurchaseOrder[]'] = $rsReceivingPurchaseDetail[$i]['refpodetailkey'];
                        $_POST['hidItemKey[]'] = $rsReceivingPurchaseDetail[$i]['itemkey'];
                        $_POST['itemName[]'] = $rsReceivingPurchaseDetail[$i]['itemname'];
                        $_POST['orderedQtyInBaseUnit[]'] = $obj->formatNumber($rsReceivingPurchaseDetail[$i]['orderedqtyinbaseunit']);
                        $_POST['qtyMinusInBaseUnit[]'] = $obj->formatNumber($rsReceivingPurchaseDetail[$i]['qtyminusinbaseunit']);
                        $_POST['receivedQtyInBaseUnit[]'] = $obj->formatNumber($rsReceivingPurchaseDetail[$i]['receivedqtyinbaseunit']);
                        $_POST['orderedQtyInPcs[]'] = $obj->formatNumber($rsReceivingPurchaseDetail[$i]['orderedqtyinpcs']);
                        $_POST['qtyMinusInPcs[]'] = $obj->formatNumber($rsReceivingPurchaseDetail[$i]['qtyminusinpcs']);
                        $_POST['receivedQtyInPcs[]'] = $obj->formatNumber($rsReceivingPurchaseDetail[$i]['receivedqtyinpcs']);
                        $_POST['hidPackagingKey[]'] = $rsReceivingPurchaseDetail[$i]['packagingkey'];
                        $_POST['packagingName[]'] = $rsReceivingPurchaseDetail[$i]['packagingname'];
                        $_POST['grossWeight[]'] = $obj->formatNumber($rsReceivingPurchaseDetail[$i]['grossweight'],2);
                        $_POST['trDetailDesc[]'] = $rsReceivingPurchaseDetail[$i]['trdesc'];
                        $_POST['beforeGrossWeight[]'] = $obj->formatNumber($rsReceivingPurchaseDetail[$i]['beforegrossweight'],2);
                    
                    }

                    ?>

                    <div class="div-table-row  <?php echo $class; ?>">

                        <div class="div-table-col detail-col-detail" style="vertical-align:top">
                            <?php echo $obj->inputSelect('selItemPurchaseOrder[]',$arrSelItemPurchaseOrder, array('overwritePost' => $overwrite, 'readonly' => false, 'etc' => $etc)); ?>    
                            <?php echo $obj->inputHidden('hidDetailKey[]', array('overwritePost' => $overwrite, 'etc' => $etc)); ?>
                            <?php echo $obj->inputHidden('hidBaseUnitKey[]', array('overwritePost' => $overwrite, 'etc' => $etc)); ?>
                        </div>
                        <div class="div-table-col detail-col-detail" style="vertical-align:top">
                            <?php echo $obj->inputText('itemName[]', array('overwritePost' => $overwrite, 'readonly' => false, 'etc' => $etc)); ?>    
                            <?php echo $obj->inputHidden('hidItemKey[]', array('overwritePost' => $overwrite, 'etc' => $etc)); ?>
                            <?php echo $obj->inputText('trDetailDesc[]', array('overwritePost' => $overwrite, 'readonly' => false, 'etc' => $etc. ' style="margin-top:0.5em" placeholder="'.$obj->lang['description'].'"')); ?>     
                        </div>
                        <div class="div-table-col detail-col-detail" style="vertical-align:top">
                            <?php echo $obj->inputDecimal('orderedQtyInBaseUnit[]', array('overwritePost' => $overwrite, 'readonly' => true, 'etc' => 'style="text-align:right;" ' . $etc)); ?>
                            <?php echo $obj->inputHidden('qtyMinusInBaseUnit[]', array('overwritePost' => $overwrite,'etc' => $etc)); ?>
                        </div>
                        <!-- <div class="div-table-col detail-col-detail">
                            <?php //echo $obj->inputNumber('qtyMinusInBaseUnit[]', array('overwritePost' => $overwrite, 'readonly' => true, 'etc' => 'style="text-align:right;" ' . $etc)); ?>
                        </div> -->
                        <div class="div-table-col detail-col-detail" style="vertical-align:top">
                            <?php echo $obj->inputDecimal('receivedQtyInBaseUnit[]', array('overwritePost' => $overwrite, 'etc' => 'style="text-align:right;" ' . $etc)); ?>
                        </div>
                        <div class="div-table-col detail-col-detail" style="vertical-align:top; padding-top:1.2em"><div class="text-muted"><span class="baseitemunit"><?php echo $unitname;?></span></div></div>
                        <div class="div-table-col detail-col-detail" style="vertical-align:top">
                            <?php echo $obj->inputDecimal('orderedQtyInPcs[]', array('overwritePost' => $overwrite, 'readonly' => true, 'etc' => 'style="text-align:right;" ' . $etc)); ?>
                            <?php echo $obj->inputHidden('qtyMinusInPcs[]', array('overwritePost' => $overwrite, 'etc' =>  $etc)); ?>
                        </div>
                        <!-- <div class="div-table-col detail-col-detail">
                            <?php //echo $obj->inputNumber('qtyMinusInPcs[]', array('overwritePost' => $overwrite, 'readonly' => true, 'etc' => 'style="text-align:right;" ' . $etc)); ?>
                        </div> -->
                        <div class="div-table-col detail-col-detail" style="vertical-align:top">
                            <?php echo $obj->inputDecimal('receivedQtyInPcs[]', array('overwritePost' => $overwrite, 'etc' => 'style="text-align:right;" ' . $etc)); ?>
                        </div>
                          <div class="div-table-col detail-col-detail" style="vertical-align:top; padding-top:1.2em"><div class="text-muted"><span class="gram">Gr</span></div></div>
                        
                        <div class="div-table-col detail-col-detail" style="vertical-align:top">
                            <?php echo $obj->inputDecimal('beforeGrossWeight[]', array('overwritePost' => $overwrite, 'etc' => 'style="text-align:right;" ' . $etc)); ?>
                        </div>
                        <div class="div-table-col detail-col-detail" style="vertical-align:top">
                            <?php echo $obj->inputDecimal('grossWeight[]', array('overwritePost' => $overwrite,'readonly' => true, 'etc' => 'style="text-align:right;" ' . $etc)); ?>
                        </div>
                        <div class="div-table-col detail-col-detail" style="vertical-align:top">
                            <?php echo $obj->inputText('packagingName[]', array('overwritePost' => $overwrite, 'readonly' => false, 'etc' => $etc)); ?>    
                            <?php echo $obj->inputHidden('hidPackagingKey[]', array('overwritePost' => $overwrite, 'etc' => $etc)); ?>   
                        </div>
                        <!-- <div class="div-table-col detail-col-detail">
                            <div class="text-muted"><span class="baseitemunit"><?php echo $unitname; ?></span></div>
                        </div> -->
                        <div class="div-table-col detail-col-detail  <?php echo $obj->hideOnDisabled(); ?> icon-col"  style="vertical-align:top;">
                            <?php echo $obj->inputLinkButton('btnDeleteRows', '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button', 'etc' => 'tabIndex="-1" style="margin-top:.7em"')); ?>
                        </div>
      
                </div>

                <?php } ?>
            </div>
            <div style="clear:both; height:1em;"></div> 
          <div style="float:left; display:inline-block;"><?php echo $obj->inputButton('btnAddRows', $obj->lang['addRows'], array('class' => 'btn btn-primary btn-second-tone')); ?>
            </div>
 

            <div class="form-button-margin"></div>
            <div class="form-button-panel">
                <?php echo $obj->generateSaveButton(array(), true); ?>
            </div>

        </form>

        <?php echo $obj->showDataHistory(); ?>
    </div>
</body>

</html>
