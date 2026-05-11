<?php
require_once '../_config.php';
require_once '../_include-v2.php';

includeClass(array('Asset.class.php','AssetDepreciation.class.php'));
$assetDepreciation = createObjAndAddToCol(new AssetDepreciation()); 
$warehouse = createObjAndAddToCol(new Warehouse());

$obj = $assetDepreciation;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
// some modules that have different security object from that of their class

if (!$security->isAdminLogin($securityObject, 10, true));

$formAction = 'assetDepreciationList';

$isQuickAdd = (isset($_GET) && !empty($_GET['quickadd'])) ? true : false;

$editWarehouseInactiveCriteria = '';

$rsAssetDetail = array(); 

$_POST['trDate'] = date('d / m / Y');
 
$rs = prepareOnLoadData($obj);

if (!empty($_GET['id'])) {
    $id = $_GET['id'];

    $rsAssetDetail = $obj->getDetailWithRelatedInformation($id);
     
    $editWarehouseInactiveCriteria = ' or ' . $warehouse->tableName . '.pkey = ' . $obj->oDbCon->paramString($rs[0]['warehousekey']);
}

$arrStatus = $obj->convertForCombobox($obj->getAllStatus(), 'pkey', 'status');
$arrWarehouse = $class->convertForCombobox($warehouse->searchData('', '', true, ' and (' . $warehouse->tableName . '.statuskey = 1' . $editWarehouseInactiveCriteria . ')'), 'pkey', 'name');

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

            var varConstant = { };  
 
            var assetDepreciation = new AssetDepreciation(tabID, tablekey, varConstant);
            prepareHandler(assetDepreciation);
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
                                    <?php echo $obj->inputSelect('selWarehouseKey', $arrWarehouse); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['date']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputDate('trDate'); ?>
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

            <div class="div-table mnv-transaction transaction-detail" style="width:100%; border-bottom:1px solid #333; ">
                <div class="div-table-row">
                    <div class="div-table-col detail-col-header" style="width:180px;"><?php echo ucwords($obj->lang['code']); ?></div> 
                    <div class="div-table-col detail-col-header"><?php echo ucwords($obj->lang['asset']); ?></div> 
                    <div class="div-table-col detail-col-header" style="width:150px; text-align:right;"><?php echo ucwords($obj->lang['amount']); ?></div>
                    <div class="div-table-col detail-col-header <?php echo $obj->hideOnDisabled(); ?> icon-col"></div>
                </div>

                <?php
                $totalRows = count($rsAssetDetail);

                for ($i = 0; $i <= $totalRows; $i++) {

                    $class =  'transaction-detail-row';
                    $overwrite = true;
                    $etc = '';

                    if ($i == $totalRows) {
                        $class = 'detail-row-template';
                        $overwrite = false;
                        $etc = 'disabled="disabled"';
                    } else {
                        $decimal = 0;
                        $inputnumber = 'inputnumber';
						
                        $_POST['hidDetailKey[]'] =  $rsAssetDetail[$i]['pkey'];
                        $_POST['hidAssetKey[]'] =  $rsAssetDetail[$i]['assetkey']; 
                        $_POST['assetName[]'] =  $rsAssetDetail[$i]['assetname']; 
                        $_POST['assetCode[]'] =  $rsAssetDetail[$i]['assetcode']; 
                        $_POST['depreciationValue[]'] =   $obj->formatNumber($rsAssetDetail[$i]['value']); 
                    }

                ?>
                    <div class="div-table-row <?php echo $class; ?>">
                        <div class="div-table-col detail-col-detail"><?php echo $obj->inputText('assetCode[]', array('overwritePost' => $overwrite, 'etc' =>  $etc)); ?><?php echo $obj->inputHidden('hidDetailKey[]', array('overwritePost' => $overwrite, 'etc' => $etc)); ?></div>
                        <div class="div-table-col detail-col-detail"><?php echo $obj->inputText('assetName[]', array('overwritePost' => $overwrite,'readonly'=>true, 'etc' =>  $etc)); ?><?php echo $obj->inputHidden('hidAssetKey[]', array('overwritePost' => $overwrite, 'etc' => $etc)); ?></div>
                        <div class="div-table-col detail-col-detail"><?php echo $obj->inputNumber('depreciationValue[]', array('overwritePost' => $overwrite,'readonly'=>true, 'etc' => 'style="text-align:right;"' . $etc)); ?></div>
                        <div class="div-table-col detail-col-detail  <?php echo $obj->hideOnDisabled(); ?> icon-col"><?php echo $obj->inputLinkButton('btnDeleteRows', '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button', 'etc' => 'tabIndex="-1"')); ?></div>
                    </div>
                <?php  }   ?>

            </div>

            <div style="clear:both; height:1em;"></div>
            <div style="float:left; display:inline-block;"><?php echo $obj->inputButton('btnAddRows', $obj->lang['addRows'], array('class' => 'btn btn-primary btn-second-tone')); ?></div>

            <div>
                <div style="width:350px; float:right; ">
                  
                    <div class="div-table" style="width:100%;">

                        <div class="div-table-row  form-group">
                            <div class="div-table-col-3" style="text-align:right;">
                                <?php echo ucwords($obj->lang['total']); ?>
                            </div>
                            <div class="div-table-col-3" style="width:150px;">
                                 <?php echo $obj->inputNumber('grandtotal', array('readonly' => true, 'etc' => 'style="text-align:right;"')); ?>
                            </div>
                            <div class="div-table-col-3 <?php echo $obj->hideOnDisabled(); ?> icon-col"></div>
                        </div>
                    </div>
                </div>
 
                <div style="clear:both"></div>
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
