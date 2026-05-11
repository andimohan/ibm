<?php
require_once '../_config.php';
require_once '../_include-v2.php';

includeClass('CostGrouping.class.php');
$costGrouping = createObjAndAddToCol(new CostGrouping());

$obj = $costGrouping;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
// some modules that have different security object from that of their class

if (!$security->isAdminLogin($securityObject, 10, true));

$formAction = 'costGroupingList';

$isQuickAdd = (isset($_GET) && !empty($_GET['quickadd'])) ? true : false;

$rsDetail = array();

$rs = prepareOnLoadData($obj);

$editWarehouseInactiveCriteria = '';

if (!empty($_GET['id'])) {
    $id = $_GET['id'];

    $rsDetail = $obj->getDetailWithRelatedInformation($id);
    $rsCostGroupingHeader = $obj->getDataRowById($rs[0]['parentkey']);
    $_POST['parentName'] = $rsCostGroupingHeader[0]['name'];
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

            var costGrouping = new CostGrouping(tabID, <?php echo json_encode(
                                                            array(
                                                                'rs' => $rs,
                                                                'rsDetail' => $rsDetail
                                                            )
                                                        ); ?>);
            prepareHandler(costGrouping);

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
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['name']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputText('name'); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['parent']); ?></label>
                                <div class="col-xs-9">
                                    <?php
                                    echo $obj->inputAutoComplete(
                                        array(
                                            'objRefer' => $costGrouping,
                                            'element' => array(
                                                'value' => 'parentName',
                                                'key' => 'hidParentKey'
                                            ),
                                            'source' => array(
                                                'url' => 'ajax-cost-grouping.php',
                                                'data' => array(
                                                    'action' => 'searchData'
                                                )
                                            ), 
                                        )
                                    );
                                    ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['orderList']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputNumber('orderList'); ?>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="div-table-col">
                        <div class="div-tab-panel">
                            <div class="div-table-caption border-blue"><?php echo ucwords($obj->lang['chartOfAccount']); ?></div>

                            <div class="form-group"> 
                                <div class="col-xs-12">
                                    <div class="div-table mnv-transaction diagnose-detail transaction-detail" style="width:100%">
                                        <?php
                                        $totalRows = count($rsDetail);
                                        for ($j = 0; $j <= $totalRows; $j++) {
                                            $class =  'transaction-detail-row';
                                            $overwrite = true;
                                            $readonly = false;
                                            $disabled = false;

                                            if ($j == $totalRows) {
                                                $class = 'cost-grouping-row-template row-template';
                                                $overwrite = false;
                                                $disabled = true;
                                                $isLocked = false;
                                            } else {
                                                $_POST['hidCostGroupDetailKey[]'] =  $rsDetail[$j]['pkey'];
                                                $_POST['hidCoaKey[]'] =  $rsDetail[$j]['coakey'];
                                                $_POST['coaCode[]'] = $rsDetail[$j]['coacode'];
                                            }
                                            $hideDeleteIcon = '';
                                        ?>
                                            <div class="div-table-row <?php echo $class; ?>  odd-style-adjustment">
                                                <div class="div-table-col">
                                                    <div class="flex">
                                                        <div class="consume">
                                                            <?php echo $obj->inputHidden('hidDetailKey[]', array('overwritePost' => $overwrite, 'etc' => $etc, 'readonly' => $readonly, 'disabled' => $disabled)); ?>
                                                            <?php echo $obj->inputHidden('hidCoaKey[]', array('overwritePost' => $overwrite, 'readonly' => $readonly, 'disabled' => $disabled)); ?>
                                                            <?php echo $obj->inputText('coaCode[]', array('overwritePost' => $overwrite, 'readonly' => $readonly, 'disabled' => $disabled)); ?>
                                                        </div>
                                                        <div class="icon-col <?php echo $obj->hideOnDisabled(); ?>"><?php echo $obj->inputLinkButton('btnAddDetailRow', '<i class="fas fa-plus-circle"></i>', array('class' => 'btn btn-link add-row-button', 'etc' => 'attr-template="cost-grouping-row-template"')); ?></div>
                                                        <div class="icon-col <?php echo $obj->hideOnDisabled(); ?>"><?php echo $obj->inputLinkButton('btnDeleteRows', '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button', 'etc' =>  'tabIndex="-1" style="padding:6px 0; ' . $hideDeleteIcon . '"')); ?></div>

                                                    </div>
                                                </div>
                                            </div>
                                        <?php }     ?>

                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <div style="clear:both; height:1em;"></div>
            <div class="form-button-panel">
                <?php echo $obj->generateSaveButton(); ?>
            </div>

        </form>
        <?php echo $obj->showDataHistory(); ?>
    </div>
</body>

</html>