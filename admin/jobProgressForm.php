<?php
require_once '../_config.php';
require_once '../_include-v2.php';

includeClass('JobProgress.class.php','TruckingServiceOrderCategory.class.php');
$jobProgress = createObjAndAddToCol(new JobProgress());
$truckingServiceOrderCategory = createObjAndAddToCol(new TruckingServiceOrderCategory());

$obj = $jobProgress;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
// some modules that have different security object from that of their class

if (!$security->isAdminLogin($securityObject, 10, true))
    ;

$formAction = 'jobProgressList';

$isQuickAdd = (isset($_GET) && !empty($_GET['quickadd'])) ? true : false;

$rs = prepareOnLoadData($obj);

$rsDetail = array();
if (!empty($_GET['id'])) {
    $id = $_GET['id'];

    $rsDetail = $obj->getDetailWithRelatedInformation($id);

    $_POST['code'] = $rs[0]['code'];
    $_POST['hidCategoryKey'] = $rs[0]['categorykey'];

    if(!empty($rs[0]['categorykey'])) {
        $rsCategory = $truckingServiceOrderCategory->getDataRowById($rs[0]['categorykey']);
        $_POST['categoryName'] = $rsCategory[0]['name'];
    }

    $_POST['trDesc'] = $rs[0]['trdesc'];

}

$arrStatus = $obj->convertForCombobox($obj->getAllStatus(), 'pkey', 'status');

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

            var jobProgress = new JobProgress(tabID,<?php echo json_encode(
                array(
                    'detail' => $rsDetail
                )
            ); ?>);

            prepareHandler(jobProgress);

            var fieldValidation = {
                code: {
                    validators: {
                        notEmpty: {
                            message: phpErrorMsg.code[1]
                        },
                    }
                },
                name: {
                    validators: {
                        notEmpty: {
                            message: phpErrorMsg.jobProgress[1]
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
                                <?php echo ucwords($obj->lang['generalInformation']); ?></div>
                            <div class="form-group">
                                <label
                                    class="col-xs-3 control-label"><?php echo ucwords($obj->lang['status']); ?></label>
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
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['category']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputAutoComplete(
                                        array(
                                            'objRefer' => $truckingServiceOrderCategory,
                                            'revalidateField' => true,
                                            'element' => array(
                                                'value' => 'categoryName',
                                                'key' => 'hidCategoryKey'
                                            ),
                                            'source' => array(
                                                'url' => 'ajax-trucking-service-order-category.php',
                                                'data' => array('action' => 'searchData')
                                            ),
                                        )
                                    ); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="div-table-col">
                        <div class="div-tab-panel">
                            <div class="div-table-caption border-blue"><?php echo ucwords($obj->lang['note']); ?></div>
                            <div class="form-group">
                                <div class="col-xs-12">
                                    <?php echo $obj->inputTextArea('trDesc', array('etc' => 'style="height:10em;"', 'allowedStatusForEdit' => array(1))); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="div-table transaction-detail" style="width:100%; border-bottom:1px solid #333; ">
                <div class="div-table-row"> 
                    <div class="div-table-col detail-col-header" style="width:60px; text-align:right;"><?php echo ucwords($obj->lang['number']); ?></div>
                    <div class="div-table-col detail-col-header" ><?php echo ucwords($obj->lang['jobProgress']); ?></div>
                    <div class="div-table-col detail-col-header ">POD</div>
                    <div class="div-table-col detail-col-header icon-col"></div>
                </div>
            
                <?php

                $totalRows = count($rsDetail);
                for ($i = 0; $i <= count($rsDetail); $i++) {

                    $class = 'transaction-detail-row';
                    $overwrite = true;
                    $etc = '';

                    if ($i == $totalRows) {
                        $class = 'detail-row-template';
                        $overwrite = false;
                        $etc = 'disabled="disabled"';
                    } else {
                        $_POST['hidDetailKey[]'] = $rsDetail[$i]['pkey'];
                        $_POST['numberDetail[]'] = $rsDetail[$i]['number'];
                        $_POST['name[]'] = $rsDetail[$i]['name'];
                        $_POST['chkNeedPOD[]'] = $rsDetail[$i]['needpod'];
                    }
                    ?>
            
            
                    <div class="div-table-row  <?php echo $class; ?>">
                        <div class="div-table-col detail-col-detail" style="width:60px; text-align:right;">
                            <?php echo $obj->inputInteger('numberDetail[]', array('overwritePost' => $overwrite, 'readonly'=>true, 'etc' => 'style="text-align:right;" ' . $etc)); ?>
                        </div>
                        <div class="div-table-col detail-col-detail">
                            <?php echo $obj->inputText('name[]', array('overwritePost' => $overwrite, 'etc' => $etc));  ?>
                            <?php echo $obj->inputHidden('hidDetailKey[]', array('overwritePost' => $overwrite, 'etc' => $etc)); ?>
                        </div>
                        <div class="div-table-col detail-col-detail <?php echo $obj->hideOnDisabled(); ?> icon-col"  style="vertical-align:top; padding-top:7px !important">
                                        <?php echo $obj->inputCheckBox('chkNeedPOD[]',array('disabled' => $disabled )); ?>
                                    </div>
                        <div class="div-table-col detail-col-detail icon-col" >
                            <?php echo $obj->inputLinkButton('btnDeleteRows', '<i class="fas fa-times"></i>', array('etc' => 'tabIndex="-1" ', 'class' => 'btn btn-link remove-button')); ?>
                        </div>
                    </div>
            
                <?php } ?>
            
            </div>
            
            <div style="clear:both; height:1em;"></div>
            <div style="float:left; display:inline-block;">
                <?php echo $obj->inputButton('btnAddRows', ucwords($obj->lang['addRows']), array('class' => 'btn btn-primary btn-second-tone')); ?>
            </div>
            
            <div class="form-button-margin"></div>

            <div style="clear:both; height:1em;"></div>
            <div class="form-button-panel">
                <?php echo $obj->generateSaveButton(); ?>
            </div>

        </form>
        <?php echo $obj->showDataHistory(); ?>
    </div>
</body>

</html>