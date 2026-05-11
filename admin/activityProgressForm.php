<?php
require_once '../_config.php';
require_once '../_include-v2.php';

includeClass(array('ActivityProgress.class.php'));
$activityProgress = new ActivityProgress();
$emklJobOrder = new EMKLJobOrder();
$obj = $activityProgress;
$securityObject = $obj->securityObject;

if (!$security->isAdminLogin($securityObject, 10, true));

$formAction = 'activityProgressList';

$isQuickAdd = (isset($_GET) && !empty($_GET['quickadd'])) ? true : false;

$_POST['trDate'] = date('d / m / Y');
$detailDate = date('d / m / Y');

$rs = prepareOnLoadData($obj);

if (!empty($_GET['id'])) {

    $rsDetail = $obj->getDetailWithRelatedInformation($rs[0]['pkey']);

    $_POST['code'] = $rs[0]['code'];
    $_POST['trDate'] = $obj->formatDBDate($rs[0]['trdate'], 'd / m / Y');
    $_POST['hidJobOrderKey'] = $rs[0]['joborderkey'];
    if (!empty($rs[0]['joborderkey'])) {
        // $rsJO = $emklJobOrder->getDataRowById($rs[0]['joborderkey']);
        $rsJO = $emklJobOrder->searchData('','',true, ' and ' .  $emklJobOrder->tableName.'.pkey = ('. $obj->oDbCon->paramString($rs[0]['joborderkey']) .') ');
        
        $_POST['jobOrderCode'] = $rsJO[0]['code'];

        $_POST['selTypeOfJob'] = $rsJO[0]['jobtypekey'];
        $_POST['selAirSea'] = $rsJO[0]['transportationtypekey'];
        $_POST['selContainerType'] = $rsJO[0]['loadcontainertypekey'];
        $_POST['containerName'] = $rsJO[0]['containername'];

        $_POST['poNumber'] = $rsJO[0]['ponumber'];
        $_POST['bookingNumber'] = $rsJO[0]['jbookingnumber'];
        $_POST['shipperName'] = $rsJO[0]['customername'];
        $_POST['mblNumber'] = $rsJO[0]['mblnumber'];

        $_POST['pol'] = $rsJO[0]['polname'];
        $_POST['pod'] = $rsJO[0]['podname'];

        $_POST['etdPol'] = $obj->formatDBDate($rsJO[0]['etdpol'], 'd / m / Y');
        $_POST['etaPod'] = $obj->formatDBDate($rsJO[0]['etapol'], 'd / m / Y');

        $_POST['terminal'] = $rsJO[0]['terminalname'];
        $_POST['depot'] = $rsJO[0]['depotname'];

        $_POST['location'] = $rsJO[0]['stuffinglocation'];
        $_POST['containerNumber'] = $rsJO[0]['containernumber'];

    }
}

$arrJob = $class->convertForCombobox($emklJobOrder->getJobType(), 'pkey', 'name');
$arrContainer = $class->convertForCombobox($emklJobOrder->getLoadContainer(), 'pkey', 'name');
$arrTransportType = $class->convertForCombobox($emklJobOrder->getTransportationType(), 'pkey', 'name');
$arrStatus = $obj->convertForCombobox($obj->getAllStatus(), 'pkey', 'status');

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
                            EMKL : <?php echo json_encode(EMKL); ?>,  
                            };

            var activityProgress = new ActivityProgress(tabID, varConstant);

            prepareHandler(activityProgress);

            var fieldValidation = {
                code: {
                    validators: {
                        notEmpty: {
                            message: phpErrorMsg.code[1]
                        },
                    }
                },
                jobOrderCode: {
                    validators: {
                        notEmpty: {
                            message: phpErrorMsg.jobOrder[1]
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

                <div class="div-table main-tab-table-2">
                    
                    <div class="div-table-row">
                        
                        <div class="div-table-col">
                            <div class="div-tab-panel">
                                <div class="div-table-caption border-orange"><?php echo ucwords($obj->lang['generalInformation']); ?></div>
                                <div class="form-group">
                                    <label
                                        class="col-xs-3 control-label"><?php echo ucwords($obj->lang['status']); ?></label>
                                    <div class="col-xs-9">
                                        <?php echo $obj->inputSelect('selStatus', $arrStatus, array('readonly' => true)); ?>
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
                                        <label class="col-xs-3 control-label"><?php echo $obj->lang['jobOrder']; ?></label>
                                    <div class="col-xs-9">
                                        <?php
                                        echo $obj->inputAutoComplete(
                                            array(
                                                'objRefer' => $truckingServiceOrder,
                                                'revalidateField' => true,
                                                'element' => array(
                                                    'value' => 'jobOrderCode',
                                                    'key' => 'hidJobOrderKey'
                                                ),
                                                'source' => array(
                                                    'url' => 'ajax-emkl-job-order.php',
                                                    'data' => array('action' => 'searchData', 'statuskey' => '(1,2)')
                                                ),
                                                // 'allowedStatusForEdit' => array(1),
                                                'callbackFunction' => 'getTabObj().updateFromJobOrder()'
                                            )
                                        );


                                        ?>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['note']); ?></label>
                                    <div class="col-xs-9">
                                        <?php echo $obj->inputTextArea('trDesc', array('etc' => 'style="height:10em;"')); ?>
                                    </div>
                                </div>

                                <div class="form-group">
                                        <div class="col-xs-3"></div>
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputButton('btnImport', $obj->lang['showAll'], array('class' => 'btn btn-primary btn-second-tone')); ?>
                                    </div>
                                </div>

                            </div>
                        </div>
 
                        <div class="div-table-col">
                            <div class="div-tab-panel">
                                <div class="div-table-caption border-blue"><?php echo ucwords($obj->lang['jobInformation']); ?></div>
                                
                                <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['typeOfJob']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <div class="flex">
                                            <div class="consume" ><?php echo $obj->inputSelect('selTypeOfJob', $arrJob, array('readonly' => true)); ?></div>
                                            <div ><?php echo $obj->inputSelect('selAirSea', $arrTransportType, array('readonly' => true)); ?></div>
                                            <div ><?php echo $obj->inputSelect('selContainerType', $arrContainer, array('readonly' => true)); ?></div>
                                            <div class="lcl-only"><?php echo $obj->inputText('containerName', array('readonly' => true)); ?></div> 
                                            </div>
                                        </div>  
                                    </div>  
                                     <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['poReference']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputText('poNumber', array('readonly' => true)); ?>
                                        </div> 
                                    </div> 
                                      <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['bookingNumber']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputText('bookingNumber', array('readonly' => true)); ?>
                                        </div> 
                                    </div> 
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['shipper']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputText('shipperName', array('readonly' => true)); ?>  
                                        </div> 
                                    </div>
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['mbl']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputText('mblNumber', array('readonly' => true)); ?>  
                                        </div> 
                                    </div> 
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label">POL / POD</label> 
                                        <div class="col-xs-9"> 
                                            <div class="flex">
                                                <div class="consume"><?php echo $obj->inputText('pol', array('readonly' => true)); ?>  </div>
                                                <div> / </div>
                                                <div class="consume"><?php echo $obj->inputText('pod', array('readonly' => true)); ?></div>
                                            </div>   
                                        </div> 
                                    </div> 
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo strtoupper($obj->lang['etd']); ?> / <?php echo strtoupper($obj->lang['eta']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <div class="flex">
                                                <div class="consume"><?php echo $obj->inputDate('etdPol', array('etc' => 'style="text-align:center"', 'readonly' => true)); ?></div>
                                                <div> / </div>
                                                <div class="consume"><?php echo $obj->inputDate('etaPod', array('etc' => 'style="text-align:center"', 'readonly' => true)); ?></div>
                                            </div>   
                                        </div> 
                                    </div>  
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo $obj->lang['terminal']; ?> / <?php echo $obj->lang['depot']; ?></label> 
                                        <div class="col-xs-9"> 
                                            <div class="flex">
                                                <div class="consume"><?php echo $obj->inputText('terminal', array('readonly' => true)); ?></div>
                                                <div> / </div>
                                                <div class="consume"><?php echo $obj->inputText('depot', array('readonly' => true)); ?></div>
                                            </div>   
                                        </div> 
                                    </div>  
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label" style="padding-top:0"><?php echo $obj->lang['stuffingDestuffingLocation']; ?></label> 
                                        <div class="col-xs-9"> <?php echo $obj->inputHidden('hidLocationKey'); ?>
                                                                <?php echo $obj->inputText('location', array('readonly' => true)); ?></div>  
                                   </div>  
                                    <!--<div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['customer']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputTextArea('customerName', array('etc' => 'style="height:8em;"', 'readonly' => true)); ?>  
                                        </div> 
                                    </div>-->
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['containerType']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputTextArea('containerNumber', array('etc' => 'style="height:8em;"', 'readonly' => true)); ?>  
                                        </div> 
                                    </div>

                            </div>
                        </div>
                    </div>
                </div>

                <div class="div-table mnv-transaction transaction-detail mnv-checkbox-group" style="width:100%; border-bottom:1px solid #333; ">
                    <div class="div-table-row"> 
                        <div class="div-table-col detail-col-header" style="width:130px;text-align:center;"><?php echo ucwords($obj->lang['date']); ?></div>
                        <div class="div-table-col detail-col-header"><?php echo ucwords($obj->lang['activity']); ?></div>
                        <div class="div-table-col detail-col-header" style="width:250px"><?php echo ucwords($obj->lang['response']); ?></div>
                        <div class="div-table-col detail-col-header" style="width:280px"><?php echo ucwords($obj->lang['note']); ?></div>
                        <div class="div-table-col detail-col-header <?php echo $obj->hideOnDisabled(); ?> icon-col"></div>
                    </div>

                    <?php
                
                        $totalRows = count($rsDetail);

                        for ($i = 0; $i <= $totalRows; $i++) {
                            $class = 'transaction-detail-row';
                            $overwrite = true;
                            $disabled = false;

                            if ($i == $totalRows ){
                            
                                $class = 'detail-row-template';
                                $overwrite = false;
                                $disabled = true;
                            
                            } else {
                                $_POST['hidDetailKey[]'] = $rsDetail[$i]['pkey'];
                                $_POST['detailDate[]'] = $obj->formatDBDate($rsDetail[$i]['date'], 'd / m / Y');
                                $_POST['hidActivityKey[]'] = $rsDetail[$i]['activitykey'];
                                $_POST['activityName[]'] = $rsDetail[$i]['activityname'];
                                $_POST['response[]'] = $rsDetail[$i]['response'];
                                $_POST['detailNote[]'] = $rsDetail[$i]['trdesc'];
                            }

                        ?>

                        <div class="div-table-row <?php echo $class; ?>">
                            <div class="div-table-col detail-col-detail" style="text-align:center; vertical-align:top">
                                <?php echo $obj->inputHidden('hidDetailKey[]', array('disabled' => $disabled, 'overwritePost' => $overwrite)); ?>
                                <?php echo $obj->inputDate('detailDate[]', array('value' => $detailDate,'disabled' => $disabled, 'overwritePost' => $overwrite, 'etc' => 'style="text-align:center;" ')); ?>
                            </div>
                            <div class="div-table-col detail-col-detail" style="text-align:center; vertical-align:top">
                                <?php echo $obj->inputHidden('hidActivityKey[]', array('disabled' => $disabled, 'overwritePost' => $overwrite)); ?>
                                <?php echo $obj->inputText('activityName[]', array('disabled' => $disabled, 'overwritePost' => $overwrite)); ?>
                            </div>
                            <div class="div-table-col detail-col-detail" style="text-align:center; vertical-align:top">
                                <?php echo $obj->inputText('response[]', array('disabled' => $disabled, 'overwritePost' => $overwrite)); ?>
                            </div>
                            <div class="div-table-col detail-col-detail" style="text-align:center; vertical-align:top">
                                <?php echo $obj->inputTextArea('detailNote[]', array('overwritePost' => $overwrite, 'etc' => 'style="height:6em" placeholder="' . $obj->lang['note'] . '"')); ?>
                            </div>
                            <div class="div-table-col detail-col-detail  <?php echo $obj->hideOnDisabled(); ?> icon-col" style="vertical-align:top">
                                <?php echo $obj->inputLinkButton('btnDeleteRows', '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button', 'etc' => 'tabIndex="-1"')); ?>
                            </div>
                        </div>

                    <?php } ?>

                </div>

                <div style="clear:both; height:1em;"></div>
                <div style="float:left; display:inline-block;"><?php echo $obj->inputButton('btnAddRows', $obj->lang['addRows'], array('class' => 'btn btn-primary btn-second-tone')); ?></div>

                <div class="form-button-margin"></div>
                <div class="form-button-panel">
                    <?php echo $obj->generateSaveButton(array(), true); ?>
                </div>

            </form>
                <?php echo $obj->showDataHistory(); ?>
        </div>
</body>
</html>