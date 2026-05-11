<?php
require_once '../_config.php';
require_once '../_include-v2.php';

includeClass(array('BuildingUnit.class.php'));
$buildingUnit = createObjAndAddToCol(new BuildingUnit());
$customer = createObjAndAddToCol( new Customer()); 
$buildingUnitCategory = createObjAndAddToCol( new BuildingUnitCategory());

$obj = $buildingUnit;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
// some modules that have different security object from that of their class

if (!$security->isAdminLogin($securityObject, 10, true))
    ;

$formAction = 'buildingUnitList';

$isQuickAdd = (isset($_GET) && !empty($_GET['quickadd'])) ? true : false;

$rs = prepareOnLoadData($obj);

$rsDetailOwner = array();
$rsDetailTenant = array();

$_POST['itemDateHomeOwner[]'] = date('d / m / Y');
$_POST['itemDateHouseTenant[]'] = date('d / m / Y');


if (!empty($_GET['id'])) {
    $id = $_GET['id'];
     
	$rsOwnerDetail = $customer->getDataRowById($rs[0]['ownerkey']);
    $_POST['ownerName'] = $rsOwnerDetail[0]['name'];
    $rsTenantDetail = $customer->getDataRowById($rs[0]['tenantkey']);
    $_POST['tenantName'] = $rsTenantDetail[0]['name'];

 
    $_POST['aroutstanding'] = $obj->formatNumber($rs[0]['aroutstanding']);
	$_POST['totalResidentsHeader'] =$obj->formatNumber($rs[0]['totalresidents']);
   
    $rsDetailOwner = $obj->getOwnerDetail($id);
    $rsDetailTenant = $obj->getTenantDetail($id);
}

$arrStatus = $obj->convertForCombobox($obj->getAllStatus(), 'pkey', 'status');
$arrUnitCategory = $obj->convertForCombobox($buildingUnitCategory->getLeafNodeWithPath(),'pkey','path');

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
            var building = new BuildingUnit(tabID, <?php echo json_encode(
                array(
                    'rsTenant' => $rsDetailTenant,
                    'rsOwner' => $rsDetailOwner
                )
            ); ?>);

            prepareHandler(building);

            var fieldValidation = {
                code: {
                    validators: {
                        notEmpty: {
                            message: phpErrorMsg.code[1]
                        },
                    }
                },
                // format erro ngebug
                //                block: {
                //                    validators: {
                //                        notEmpty: {
                //                            message: phpErrorMsg.buildingUnit[2]
                //                        },
                //                    }
                //                },
                //                unit: {
                //                    validators: {
                //                        notEmpty: {
                //                            message: phpErrorMsg.buildingUnit[3]
                //                        },
                //                    }
                //                }
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
                                <label class="col-xs-3 control-label">
                                    <?php echo ucwords($obj->lang['status']); ?>
                                </label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputSelect('selStatus', $arrStatus); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label">
                                    <?php echo ucwords($obj->lang['code']); ?>
                                </label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputAutoCode('code'); ?>
                                </div>
                            </div>
							
                            <div class="form-group">
                                <label class="col-xs-3 control-label">
                                    <?php echo $obj->lang['block'] . ' / ' . $obj->lang['unit'] . ' / ' . $obj->lang['unitSize'] ?>
                                </label>
                                <div class="col-xs-9">
                                    <div class="flex">
                                        <div class="consume">
                                            <?php echo $obj->inputText('block', array('etc' => 'style="text-align:center;"')); ?>
                                        </div>
                                        <div>
                                            /
                                        </div>
                                        <div class="consume">
                                            <?php echo $obj->inputText('unit', array('etc' => 'style="text-align:center;"')); ?>
                                        </div>
                                        <div>
                                            /
                                        </div>
                                        <div class="consume">
                                            <?php echo $obj->inputNumber('unitSize', array('etc' => 'style="text-align:right;" mnv-attr-decimal="2" ')); ?>
                                        </div>
                                        <div>
                                            m<sup>2</sup>
                                        </div>
                                    </div>
                                </div>
                            </div>
							
						   <div class="form-group">
							<label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['category']); ?></label> 
							<div class="col-xs-9"> 
								<?php echo $obj->inputSelect('hidCategoryKey', $arrUnitCategory); ?>
							</div> 
							</div> 
							<div class="form-group">
                                <label class="col-xs-3 control-label">
                                    <?php echo ucwords($obj->lang['price']); ?> / m<sup>2</sup>
                                </label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputNumber('pricePerSquare', array('etc' => 'style="text-align:right;"')); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label">
                                    <?php echo $obj->lang['owner'] . ' / ' . $obj->lang['tenant'] . ' / '.  $obj->lang['totalResidents'] ?>
                                </label>
                                <div class="col-xs-9">
                                    <div class="flex">
                                        <div class="consume">
                                            <?php echo $obj->inputText('ownerName', array('etc' => 'readonly="readonly"')); ?>
                                        </div>
                                        <div>
                                            /
                                        </div>
                                        <div class="consume">
                                            <?php echo $obj->inputText('tenantName', array('etc' => 'readonly="readonly"')); ?>
                                        </div>
										<div>
                                            /
                                        </div>
                                        <div style="width:7em">
                                            <?php echo $obj->inputNumber('totalResidentsHeader', array('etc' => 'readonly="readonly" style="text-align:right"')); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-xs-3 control-label">
                                    <?php echo ucwords($obj->lang['virtualAccount']); ?>
                                </label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputText('vaNumber'); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label">
                                    <?php echo ucwords($obj->lang['outstanding']); ?>
                                </label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputNumber('aroutstanding', array('etc' => 'readonly="readonly"')); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="div-table-col">
                        <div class="div-tab-panel">

                            <div class="div-table-caption border-purple">
                                <?php echo ucwords($obj->lang['note']); ?>
                            </div>
                            <?php echo $obj->inputTextArea('trDesc', array('multilang' => true, 'etc' => 'style="height:10em;"')); ?>
                        </div>
                    </div>
                </div>


                <div class="div-table-row">

                    <div class="div-table-col">
                        <div class="div-tab-panel">
                            <div class="div-table-caption border-blue">
                                <?php echo ucwords($obj->lang['ownerDetail']); ?>
                            </div>
                            <div class="div-table mnv-transaction transaction-detail" style="width:100%;  ">
                                <div class="div-table-row">
                                    <div class="div-table-col detail-col-header" align="center" style="border:0">
                                        <?php echo ucwords($obj->lang['date']); ?>
                                    </div>
                                    <div class="div-table-col detail-col-header" style="text-align:left;border:0">
                                        <?php echo ucwords($obj->lang['owner']) ?>
                                    </div>
                                    <div class="div-table-col detail-col-header <?php echo $obj->hideOnDisabled(); ?>"
                                        style="width:45px; border:0"></div>
                                </div>

                                <?php

                                $totalDetailHomeowner = count($rsDetailOwner);

                                for ($i = 0; $i <= $totalDetailHomeowner; $i++) {

                                    $class = 'transaction-detail-row';
                                    $overwrite = true;
                                    $disabled = false;
                                    $optionRows = 'display:none';
                                    $totalDetailRows = 0;

                                    if ($i == $totalDetailHomeowner) {
                                        $class = 'detail-row-template row-template';
                                        $overwrite = false;
                                        $disabled = true;

                                    } else {
                                        $_POST['hidDetailOwnerKey[]'] = $rsDetailOwner[$i]['pkey'];
                                        $_POST['itemDateHomeOwner[]'] = $obj->formatDBDate($rsDetailOwner[$i]['trdate'], 'd / m / Y');
                                        $_POST['hidOwnerCustomerKey[]'] = $rsDetailOwner[$i]['customerkey'];
                                        $_POST['ownerName[]'] = $rsDetailOwner[$i]['customername'];

                                    }

                                    ?>

                                    <div class="div-table-row <?php echo $class; ?>">
                                        <div class="div-table-col detail-col-detail" style="width:11em">
                                            <?php echo $obj->inputHidden('hidDetailOwnerKey[]', array('overwritePost' => $overwrite, 'disabled' => $disabled)); ?>
                                            <?php echo $obj->inputDate('itemDateHomeOwner[]', array('etc' => 'style="text-align:center"')); ?>
                                        </div>
                                        <div class="div-table-col detail-col-detail">
                                            <?php echo $obj->inputText('ownerName[]', array('overwritePost' => $overwrite, 'disabled' => $disabled)); ?>
                                            <?php echo $obj->inputHidden('hidOwnerCustomerKey[]', array('overwritePost' => $overwrite, 'disabled' => $disabled)); ?>
                                        </div>
                                        <div
                                            class="div-table-col detail-col-detail icon-col  align-top-adjust <?php echo $obj->hideOnDisabled(); ?>">
                                            <?php echo $obj->inputLinkButton('btnDeleteRows', '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button', 'etc' => 'tabindex="-1"')); ?>
                                        </div>
                                    </div>

                                <?php } ?>


                            </div>

                            <div style="clear:both; height:1em;"></div>
                            <div style="float:left; display:inline-block;">
                                <?php echo $obj->inputButton('btnAddRows', $obj->lang['addRows'], array('class' => 'btn btn-primary btn-second-tone')); ?>
                            </div>

                        </div>
                    </div>

                    <div class="div-table-col">
                        <div class="div-tab-panel">
                            <div class="div-table-caption border-green">
                                <?php echo ucwords($obj->lang['tenantDetail']); ?>
                            </div>
                            <div class="div-table mnv-transaction transaction-detail" style="width:100%;  ">
                                <div class="div-table-row">
                                    <div class="div-table-col detail-col-header" align="center" style="border:0">
                                        <?php echo ucwords($obj->lang['date']); ?>
                                    </div>
                                    <div class="div-table-col detail-col-header" style="border:0">
                                        <?php echo ucwords($obj->lang['tenant']) ?>
                                    </div>
                                    <div class="div-table-col detail-col-header" style="text-align:right;border:0; width: 10em">
                                        <?php echo ucwords($obj->lang['totalResidents']) ?>
                                    </div>
                                    <div class="div-table-col detail-col-header <?php echo $obj->hideOnDisabled(); ?>"
                                        style="width:45px; border:0"></div>
                                </div>

                                <?php
                                $totalDetailHouseTenant = count($rsDetailTenant);

                                for ($i = 0; $i <= $totalDetailHouseTenant; $i++) {


                                    $class = 'transaction-detail-row';
                                    $overwrite = true;
                                    $disabled = false;

                                    if ($i == $totalDetailHouseTenant) {
                                        $class = 'house-tenant-detail-row-template row-template';
                                        $overwrite = false;
                                        $disabled = true;
                                    } else {
                                        $_POST['hidDetailTenantKey[]'] = $rsDetailTenant[$i]['pkey'];
                                        $_POST['itemDateHouseTenant[]'] = $obj->formatDBDate($rsDetailTenant[$i]['trdate'], 'd / m / Y');
                                        $_POST['totalResidents[]'] = $obj->formatNumber($rsDetailTenant[$i]['totalresidents']);
                                        $_POST['hidTenantCustomerKey[]'] = $rsDetailTenant[$i]['customerkey'];
                                        $_POST['tenantName[]'] = $rsDetailTenant[$i]['customername'];
                                    }
                                    ?>

                                    <div class="div-table-row <?php echo $class; ?>" style="">
                                        <div class="div-table-col detail-col-detail" style="width:11em">
                                            <?php echo $obj->inputHidden('hidDetailTenantKey[]', array('overwritePost' => $overwrite, 'disabled' => $disabled)); ?>
                                            <?php echo $obj->inputDate('itemDateHouseTenant[]', array('etc' => 'style="text-align:center"')); ?>
                                        </div>
                                        <div class="div-table-col detail-col-detail">
                                            <?php echo $obj->inputText('tenantName[]', array('overwritePost' => $overwrite, 'disabled' => $disabled)); ?>
                                            <?php echo $obj->inputHidden('hidTenantCustomerKey[]', array('overwritePost' => $overwrite, 'disabled' => $disabled)); ?>
                                        </div>
                                        <div class="div-table-col detail-col-detail">
                                            <?php echo $obj->inputNumber('totalResidents[]', array('etc' => 'style="text-align:right;" ')); ?>
                                        </div>
                                        <div
                                            class="div-table-col detail-col-detail icon-col  align-top-adjust <?php echo $obj->hideOnDisabled(); ?>">
                                            <?php echo $obj->inputLinkButton('btnDeleteRows', '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button', 'etc' => 'tabindex="-1"')); ?>
                                        </div>
                                    </div>

                                <?php } ?>


                            </div>

                            <div style="clear:both; height:1em;"></div>
                            <div style="float:left; display:inline-block;">
                                <?php echo $obj->inputButton('btnAddRowsHouseTenant', $obj->lang['addRows'], array('class' => 'btn btn-primary btn-second-tone')); ?>
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
