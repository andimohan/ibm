<?php
require_once '../_config.php';
require_once '../_include-v2.php';

includeClass(array('PutAway.class.php','WarehouseLayout.class.php','Pallet.class.php','ItemReceiving.class.php','Pallet.class.php'));
$putAway = createObjAndAddToCol(new PutAway());
$warehouse = createObjAndAddToCol(new Warehouse());
$warehouseLayout = createObjAndAddToCol(new WarehouseLayout());
$pallet = createObjAndAddToCol(new Pallet());
$itemReceiving = createObjAndAddToCol(new ItemReceiving());


$obj = $putAway;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
// some modules that have different security object from that of their class

if (!$security->isAdminLogin($securityObject, 10, true));

$formAction = 'putAwayList';

$isQuickAdd = (isset($_GET) && !empty($_GET['quickadd'])) ? true : false;

$editWarehouseInactiveCriteria = '';
$editCurrencyInactiveCriteria = '';

$rsItemFile = array();
$rsDetail = array();

$_POST['trDate'] = date('d / m / Y');
$_POST['trPutAwayDate'] = date('d / m / Y');

$rs = prepareOnLoadData($obj);


if (!empty($_GET['id'])) {
    $id = $_GET['id'];

    $rsWarehouseLayout = $warehouseLayout->getDataByWarehouse($rs[0]['warehousekey']);
    $rsDetail = $obj->getDetailWithRelatedInformation($id);

    $_POST['trDate'] = $obj->formatDBDate($rs[0]['trdate'], 'd / m / Y');
    $_POST['trPutAwayDate'] = $obj->formatDBDate($rs[0]['putawaydate'], 'd / m / Y');

    if(!empty($rs[0]['warehouselayoutkey'])) {

        $rsLocation = $warehouseLayout->getDataRowById($rs[0]['warehouselayoutkey']);
        $_POST['hidWarehouseLayoutKey'] = $rsLocation[0]['pkey'];
        $_POST['warehouseLayoutName'] = $rsLocation[0]['name'];
    }
    
    if(!empty($rs[0]['warehouselayoutoriginkey'])) {

        $rsLocation = $warehouseLayout->getDataRowById($rs[0]['warehouselayoutoriginkey']);
        $_POST['hidWarehouseLayoutOriginKey'] = $rsLocation[0]['pkey'];
        $_POST['warehouseLayoutOriginName'] = $rsLocation[0]['name'];
    }

    $_POST['selWarehouseKey'] = $rs[0]['warehousekey'];

    if(!empty($rs[0]['palletkey'])) {
        $rsPallet = $pallet->getDataRowById($rs[0]['palletkey']);
        $_POST['hidPalletKey'] = $rsPallet[0]['pkey'];
        $_POST['palletName'] = $rsPallet[0]['name'];
    }

    if(!empty($rs[0]['refkey'])) {
        $rsRef = $itemReceiving->getDataRowById($rs[0]['refkey']);
        $_POST['hidRefKey'] = $rsRef[0]['pkey'];
        $_POST['refCode'] = $rsRef[0]['code'];
    }

    $_POST['submissionNumber'] = $rs[0]['submissionnumber'];
    
}

$arrStatus = $obj->generateComboboxOpt(array('data' => $obj->getAllStatus(), 'label' => 'status'));
$arrWarehouse = $warehouse->generateComboboxOpt(null, array('criteria' => ' and (' . $warehouse->tableName . '.statuskey = 1)'));

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

    
            var putAway = new PutAway(tabID);

            prepareHandler(putAway);

            var fieldValidation = {
                code: {
                    validators: {
                        notEmpty: {
                            message: phpErrorMsg.code[1]
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
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['putAwayDate']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputDate('trPutAwayDate'); ?>
                                </div>
                            </div>
                            <div class="form-group coa-link">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['itemReceiving']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputAutoComplete(
                                        array(
                                            'objRefer' => $itemReceiving,
                                            'element' => array(
                                                'value' => 'refCode',
                                                'key' => 'hidRefKey'
                                            ),
                                            'source' => array(
                                                'url' => 'ajax-item-receiving.php',
                                                'data' => array('action' => 'searchData', 'statuskey' => '(2)', 'searchField'=> 'code,submissionnumber')
                                            ),
                                            'callbackFunction' => 'getTabObj().importData()'
                                        )
                                    );
                                    ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['submissionNumber']); ?></label>
                                <div class="col-xs-9">
                                        <?php echo $obj->inputText('submissionNumber', array('readonly' => true)); ?>
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
                                    <div class="flex">
                                        <div class="consume">
                                            <?php echo $obj->inputAutoComplete(
                                                array(
                                                    'objRefer' => $warehouseLayout,
                                                    'element' => array(
                                                        'value' => 'warehouseLayoutOriginName',
                                                        'key' => 'hidWarehouseLayoutOriginKey'
                                                    ),
                                                    'source' => array(
                                                        'url' => 'ajax-warehouse-layout.php',
                                                        'data' => array('action' => 'searchData', 'statuskey' => '(1)', 'searchField'=> 'code,name')
                                                    ),
                                                    'readonly' => true
                                                )
                                            );
                                            ?>
                                        </div>
                                        <div>-</div>
                                        <div class="consume">
                                            <?php echo $obj->inputAutoComplete(
                                                array(
                                                    'objRefer' => $warehouseLayout,
                                                    'element' => array(
                                                        'value' => 'warehouseLayoutName',
                                                        'key' => 'hidWarehouseLayoutKey'
                                                    ),
                                                    'source' => array(
                                                        'url' => 'ajax-warehouse-layout.php',
                                                        'data' => array('action' => 'searchData', 'statuskey' => '(1)', 'searchField'=> 'code,name')
                                                    )
                                                )
                                            );
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- <div class="form-group coa-link">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['pallet']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputAutoComplete(
                                        array(
                                            'objRefer' => $pallet,
                                            'element' => array(
                                                'value' => 'palletName',
                                                'key' => 'hidPalletKey'
                                            ),
                                            'source' => array(
                                                'url' => 'ajax-pallet.php',
                                                'data' => array('action' => 'searchData', 'statuskey' => '(1)', 'searchField'=> 'code,name')
                                            )
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
                                    <?php echo  $obj->inputTextArea('trDesc', array('etc' => 'style="height:10em;"')); ?>
                                </div>
                            </div>
                            

                        </div>

                    </div>
                </div>
            </div>
            <div class="div-table mnv-transaction transaction-detail purchase-receive-detail" style="width:100%; border-bottom:1px solid #333; ">
            
                <div class="div-table-row">
                    <div class=" div-table-col detail-col-header"><?php echo ucwords($obj->lang['itemName']); ?></div>
                    <!-- <div class=" div-table-col detail-col-header"  style="width:150px;"><?php echo ucwords($obj->lang['containerNumber']); ?></div> -->
                    <div class="div-table-col detail-col-header" style="width:150px; text-align:right;"><?php echo ucwords($obj->lang['receivedQty']); ?></div>
                    <div class="div-table-col detail-col-header" style="width:150px; text-align:right;"><?php echo ucwords($obj->lang['afterPutAwayQty']); ?></div>
                    <div class="div-table-col detail-col-header" style="width:150px; text-align:right;"><?php echo ucwords($obj->lang['qty']); ?></div>
                    <div class="div-table-col detail-col-header  icon-col <?php echo $obj->hideOnDisabled(); ?>"></div>
                </div>

                <?php
                $totalRows = count($rsDetail);

                for ($i = 0; $i <= $totalRows; $i++) {

                    $class =  'transaction-detail-row';
                    $overwrite = true;
                    $etc = '';
                    $txtSN = '';
                    $showOptions = false;

                    if ($i == $totalRows) {
                        $class = 'detail-row-template';
                        $overwrite = false;
                        $etc = 'disabled="disabled"';
                    } else {

                        $baseunitname = $rsDetail[$i]['baseunitname'];

                        $_POST['hidDetailKey[]'] =  $rsDetail[$i]['pkey'];
                        $_POST['hidItemReceivingDetailKey[]']=  $rsDetail[$i]['itemreceivingdetailkey'];
                        $_POST['hidItemKey[]'] = $rsDetail[$i]['itemkey'];
                        $_POST['itemName[]'] =  $rsDetail[$i]['itemname'];
                        $_POST['containerNumber[]'] =  $rsDetail[$i]['containernumber'];
                        $_POST['receivingQty[]'] =  $obj->formatNumber($rsDetail[$i]['receivingqty']);
                        $_POST['putAwayQty[]'] =  $obj->formatNumber($rsDetail[$i]['putawayqty']);
                        $_POST['qty[]'] =  $obj->formatNumber($rsDetail[$i]['qty']);
                
                    }


                ?>
                    <div class="div-table-row  <?php echo $class; ?>">
                        <div class="div-table-col detail-col-detail" style="vertical-align:top;">
                            <?php echo $obj->inputHidden('hidDetailKey[]', array('overwritePost' => $overwrite, 'etc' => $etc,)); ?>
                            <?php echo $obj->inputHidden('hidItemReceivingDetailKey[]', array('overwritePost' => $overwrite, 'etc' => $etc,)); ?>
                            <?php echo $obj->inputHidden('hidItemKey[]', array('overwritePost' => $overwrite, 'etc' => $etc,)); ?>
                            <?php echo $obj->inputText('itemName[]', array('overwritePost' => $overwrite, 'readonly' => true, 'etc' => $etc,  'class' => 'form-control mnv-barcode-input')); ?>
                        </div>
                            <!-- <div class="div-table-col detail-col-detail"><?php echo $obj->inputText('containerNumber[]', array('overwritePost' => $overwrite, 'etc' => 'style="text-align:right;" ' . $etc)); ?></div> -->
                        <div class="div-table-col detail-col-detail"><?php echo $obj->inputNumber('receivingQty[]', array('overwritePost' => $overwrite, 'readonly' => true, 'etc' => 'style="text-align:right;" ' . $etc)); ?></div>
                        <div class="div-table-col detail-col-detail"><?php echo $obj->inputNumber('putAwayQty[]', array('overwritePost' => $overwrite, 'readonly' => true, 'etc' => 'style="text-align:right;" ' . $etc)); ?></div>
                        <div class="div-table-col detail-col-detail"><?php echo $obj->inputNumber('qty[]', array('allowedStatusForEdit' => array(1,2),'overwritePost' => $overwrite, 'etc' => 'style="text-align:right;" ' . $etc)); ?></div>
                        
                        <div class="div-table-col detail-col-detail icon-col <?php echo $obj->hideOnDisabled(); ?>"><?php echo $obj->inputLinkButton('btnDeleteRows', '<i class="fas fa-times"></i>', array('etc' => 'tabIndex="-1"', 'class' => 'btn btn-link remove-button')); ?></div>
                    </div>

                <?php  } ?>

            </div>
            <div style=" clear:both; height:1em;"></div>
            <div style="float:left; display:inline-block;"><?php echo $obj->inputButton('btnAddRows', $obj->lang['addRows'], array('class' => 'btn btn-primary btn-second-tone')); ?></div>

            <div class="form-button-margin"></div>
            <div class="form-button-panel">
                <?php echo $obj->generateSaveButton(array(1,2), true); ?>
            </div>

        </form>
        <?php echo $obj->showDataHistory(); ?>
    </div>
</body>

</html>