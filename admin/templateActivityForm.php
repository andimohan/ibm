<?php
require_once '../_config.php';
require_once '../_include-v2.php';

includeClass(array('TemplateActivity.class.php'));
$templateActivity = new TemplateActivity();
$obj = $templateActivity;
$securityObject = $obj->securityObject;

if (!$security->isAdminLogin($securityObject, 10, true));

$formAction = 'templateActivityList';

$isQuickAdd = (isset($_GET) && !empty($_GET['quickadd'])) ? true : false;

$rs = prepareOnLoadData($obj);

$rsDataTypeDetail = array();
if (!empty($_GET['id'])) {

    $rsDataTypeDetail = $obj->getDataTypeDetail($rs[0]['pkey']);

    $_POST['name'] = $rs[0]['name'];
    $_POST['selDataType'] = $rs[0]['typekey'];
    $_POST['chkIsNotification'] = $rs[0]['notification'];
    $_POST['orderList'] = $rs[0]['orderlist'];

}

$arrStatus = $obj->generateComboboxOpt(array('data' => $obj->getAllStatus(), 'label' => 'status'));
$arrDataType = $obj->generateComboboxOpt(array('data' => $obj->getDataType()));

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>

    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title></title>

    <script type="text/javascript">
        
        jQuery(document).ready(function(){  
            var tabID = <?php echo ($isQuickAdd) ? $_GET['tabID'] : 'selectedTab.newPanel[0].id'; ?>

            var varConstant = { 
                            INPUT_TYPE : <?php echo json_encode(INPUT_TYPE); ?>
                        }

            var templateActivity = new TemplateActivity(tabID, <?php echo json_encode(
                                                                array(
                                                                    'dataTypeDetail' => $rsDataTypeDetail
                                                                ) 
                                                            ); ?>,varConstant);

            prepareHandler(templateActivity);

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
                            message: phpErrorMsg.name[1]
                        },
                    }
                },
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
            <div class="div-table main-tab-table-1">
                <div class="div-table-row">
                    <div class="div-table-col">
                        <div class="div-tab-panel">
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
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['name']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputText('name'); ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['type']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputSelect('selDataType', $arrDataType); ?>
                                </div>
                            </div>

                                <div class="form-group data-type-detail" style="display:none">
                                    <label class="col-xs-3 control-label"> </label> 
                                    <div class="col-xs-9">
                                        <div class="div-table transaction-detail" style="width:100%">
                                        <?php 
                                            $totalRows = count($rsDataTypeDetail);
                                            for ($j=0;$j<=$totalRows; $j++){ 
                                                
                                                $class =  'transaction-detail-row';
                                                $overwrite = true;
                                                $readonly = false;
                                                $disabled = false;
                                                $show = 'display:none';

                                                if ($j == $totalRows ){
                                                    $class = 'data-type-row-template row-template';
                                                    $overwrite = false;
                                                    $disabled = true; 
                                                    $isLocked = false; 
                                                } else{ 
                                                    $_POST['hidDetailDataTypeKey[]'] =  $rsDataTypeDetail[$j]['pkey'];
                                                    $_POST['dataTypeDetailName[]'] =  $rsDataTypeDetail[$j]['name'];
                                                
                                                }
                                                $hideDeleteIcon = '';  
                                            ?>
                                            <div class="div-table-row <?php echo $class; ?>  odd-style-adjustment" > 
                                                <div class="div-table-col"> 
													  <div class="flex" style="width:100%">     
															<div style="width:100%;">
															    <?php echo $obj->inputHidden('hidDetailDataTypeKey[]',array('overwritePost' => $overwrite, 'readonly' => $readonly, 'disabled' => $disabled)); ?>
																<?php echo $obj->inputText('dataTypeDetailName[]', array('overwritePost' => $overwrite ,'readonly' => $readonly, 'disabled' => $disabled )); ?>
															</div>
                                                            
															<div class="icon-col <?php echo $obj->hideOnDisabled(); ?>"><?php echo $obj->inputLinkButton('btnAddDetailRow' , '<i class="fas fa-plus-circle"></i>', array('class' => 'btn btn-link add-row-button', 'etc' => 'attr-template="data-type-row-template"')); ?></div>
															<div class="icon-col <?php echo $obj->hideOnDisabled(); ?>"><?php echo $obj->inputLinkButton('btnDeleteRows' , '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button', 'etc' =>  'tabIndex="-1" style="padding:6px 0; '.$hideDeleteIcon.'"')); ?></div>

														</div>  
												
                                                </div> 
                                            </div>   
                                        <?php }	 ?>  
                                        
                                    </div>
                                    </div> 
                                </div>

                            <div class="form-group" >
                                <label class="col-xs-3 control-label"><?php echo $obj->lang['notification']; ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputCheckBox('chkIsNotification'); ?>
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
                </div>
            </div>

            <div class="form-button-panel">
                <?php echo $obj->generateSaveButton(); ?>
            </div>
        </form>
        <?php echo $obj->showDataHistory(); ?>
    </div>
</body>

</html>
