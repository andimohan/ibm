<?php

require_once '../_config.php';
require_once '../_include-v2.php';

includeClass('DisposalWorkOrderDispatcher.class.php');
$disposalWorkOrderDispatcher = createObjAndAddToCol(new DisposalWorkOrderDispatcher());
$warehouse = createObjAndAddToCol(new Warehouse());
$customer = createObjAndAddToCol(new Customer());
$city = createObjAndAddToCol(new City());
$location = createObjAndAddToCol(new Location());
$car = createObjAndAddToCol(new Car());
$disposalJobOrder = createObjAndAddToCol(new DisposalJobOrder());
$disposalWorkOrder = createObjAndAddToCol(new DisposalWorkOrder());

$obj = $disposalWorkOrderDispatcher;

$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
// some modules that have different security object from that of their class

if (!$security->isAdminLogin($securityObject, 10, true));

$formAction = 'disposalWorkOrderDispatcherList';

$isQuickAdd = (isset($_GET) && !empty($_GET['quickadd'])) ? true : false;

$_POST['trDate'] = date('d / m / Y');

$rs = prepareOnLoadData($obj);

$editWarehouseInactiveCriteria = '';
$rsSupplierDetail = array();
$rsDisposalWorkOrder = array();

if (!empty($_GET['id'])) {
    $id = $_GET['id'];

    $rsJobOrder = $disposalJobOrder->searchData($disposalJobOrder->tableName . '.pkey', $rs[0]['refkey'], true);
    $rsSupplierDetail = $obj->getSupplierDetail($id);
    $rsDetail = $obj->getDetailWithRelatedInformation($rs[0]['pkey']);

    $arrDetailKey = array_column($rsDetail, 'pkey');
    $rsDisposalWorkOrder = $disposalWorkOrder->getDisposalWorkOrderByDispatcher($id, $arrDetailKey);
    $rsDisposalWorkOrder = $obj->reindexDetailCollections($rsDisposalWorkOrder, 'refdetailkey');

    $_POST['totalSupplierWeight'] = $obj->formatNumber($rs[0]['totalsupplierweight'], 2);
    $_POST['totalCustomerWeight'] = $obj->formatNumber($rs[0]['totalcustomerweight'], 2);
    $_POST['totalProRate'] = $obj->formatNumber($rs[0]['totalprorate'], 2);
    if ($rs[0]['statuskey'] == 2)
        $statusConfirmed = array('status' => true, 'readonly' => 'readonly="readonly"',  'disabled' =>  'disabled="disabled"');


    // $_POST['trDate'] = $obj->formatDBDate($rs[0]['trdate'], 'd / m / Y', array('returnOnEmpty' => true, 'value' => ''));
    $_POST['trDate'] = $obj->formatDBDate($rs[0]['trdate'], 'd / m / Y');


    $_POST['hidDriverKey'] = $rs[0]['driverkey'];

    if (!empty($rs[0]['driverkey'])) {
        $rsEmployee = $employee->getDataRowById($rs[0]['driverkey']);
        $_POST['driverName'] = $rsEmployee[0]['name'];
    }

    $_POST['hidCarKey'] = $rs[0]['carkey'];
    if (!empty($rs[0]['carkey'])) {
        $rsCar = $car->getDataRowById($rs[0]['carkey']);
        $_POST['policeNumber'] = $rsCar[0]['code'] . ' - ' . $rsCar[0]['policenumber'];
    }

    $editWarehouseInactiveCriteria = ' or ' . $warehouse->tableName . '.pkey = ' . $obj->oDbCon->paramString($rs[0]['warehousekey']);
}

$arrStatus = $obj->convertForCombobox($obj->getAllStatus(), 'pkey', 'status');
$arrWarehouse = $class->convertForCombobox($warehouse->searchData('', '', true, ' and (' . $warehouse->tableName . '.statuskey = 1' . $editWarehouseInactiveCriteria . ')'), 'pkey', 'name');

// $arrJobType = $obj->convertForCombobox($rsCategory, 'pkey', 'name');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title></title>

    <script type="text/javascript">
        jQuery(document).ready(function() {
            var tabID = <?php echo ($isQuickAdd) ?  $_GET['tabID'] :  'selectedTab.newPanel[0].id';  ?>;

            var disposalWorkOrderDispatcher = new DisposalWorkOrderDispatcher(tabID, <?php echo json_encode(
                                                                    array(
                                                                        'supplierDetail' => $rsSupplierDetail
                                                                    )
                                                                ); ?>);
            prepareHandler(disposalWorkOrderDispatcher);

            var fieldValidation = {
                code: {
                    validators: {
                        notEmpty: {
                            message: phpErrorMsg.code[1]
                        },
                    }
                },
                policeNumber: {
                    validators: {
                        notEmpty: {
                            message: phpErrorMsg.car[1]
                        },
                    }
                },
                driverName: {
                    validators: {
                        notEmpty: {
                            message: phpErrorMsg.driver[1]
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
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['date']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputDate('trDate', array('allowedStatusForEdit' => array(1))); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['warehouse']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputSelect('selWarehouseKey', $arrWarehouse, array('allowedStatusForEdit' => array(1))); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo $obj->lang['car']; ?></label>
                                <div class="col-xs-9">
                                    <?php
                                    echo $obj->inputAutoComplete(
                                        array(
                                            'objRefer' => $car,
                                            'revalidateField' => false,
                                            'element' => array(
                                                'value' => 'policeNumber',
                                                'key' => 'hidCarKey',
                                            ),
                                            'source' => array(
                                                'url' => 'ajax-car.php',
                                                'data' => array('action' => 'searchData', 'searchField' => 'code,policenumber')
                                            ),
                                            'popupForm' => array(
                                                'url' => 'carForm.php',
                                                'element' => array(
                                                    'value' => 'policeNumber', 'valueDBField' => 'codepolicenumber',
                                                    'key' => 'hidCarKey'
                                                ),
                                                'width' => '1000px',
                                                'title' => ucwords($obj->lang['add'] . ' - ' . $obj->lang['car'])
                                            ),
                                            'callbackFunction' => 'getTabObj().updateDriver()',
                                            'allowedStatusForEdit' => array(1),
                                        )
                                    );
                                    ?>
                                </div>
                            </div>
                            <div class="form-group inhouse">
                                <label class="col-xs-3 control-label"><?php echo $obj->lang['driver']; ?></label>
                                <div class="col-xs-9">
                                    <?php
                                    echo $obj->inputAutoComplete(
                                        array(
                                            'element' => array(
                                                'value' => 'driverName',
                                                'key' => 'hidDriverKey'
                                            ),
                                            'source' => array(
                                                'url' => 'ajax-employee.php',
                                                'data' => array(
                                                    'action' => 'searchData',
                                                    'isdriver' => 1
                                                )
                                            ),
                                            'allowedStatusForEdit' => array(1,2)
                                        )
                                    );
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="div-table-col">
                        <div class="div-tab-panel">
                            <div class="div-table-caption border-blue"><?php echo ucwords($obj->lang['note']); ?></div>
                            <div class="form-group">
                                <div class="col-xs-12">
                                    <?php echo  $obj->inputTextArea('trDesc', array('etc' => 'style="height:10em;"', 'allowedStatusForEdit' => array(1))); ?>
                                </div>
                            </div>
                        </div>
                        <!-- <div class="div-tab-panel">
                            <div class="div-table-caption border-red"><?php echo ucwords($obj->lang['supplierInformation']); ?></div>
                            <div class="div-table mnv-transaction transaction-detail supplier-detail" style="width:100%; border-bottom:1px solid #333; ">
                                <div class="div-table-row">
                                    <div class="div-table-col detail-col-header"><?php echo ucwords($obj->lang['supplier']); ?></div>
                                    <div class="div-table-col detail-col-header"><?php echo ucwords($obj->lang['refCode']); ?></div>
                                    <div class="div-table-col detail-col-header" style="width:80px; text-align:right;"><?php echo ucwords($obj->lang['weight']); ?> (Kg)</div>
                                    <div class="div-table-col detail-col-header <?php echo $obj->hideOnDisabled(); ?> icon-col"></div>
                                    <div class="div-table-col detail-col-header <?php echo $obj->hideOnDisabled(); ?> icon-col"></div>
                                </div>

                                <?php
                                $totalSupplierRows = count($rsSupplierDetail);


                                for ($k = 0; $k <= $totalSupplierRows; $k++) {

                                    $class =  'transaction-detail-row';
                                    $overwrite = true;
                               
                                    $etc = '';

                                    if ($k == $totalSupplierRows) {
                                        $class = 'supplier-row-template row-template';
                                        $overwrite = false;
                                        $etc = 'disabled="disabled"';
                                    } else {
                                        $decimal = 0;

                                        $_POST['hidSupplierDetailKey[]'] =  $rsSupplierDetail[$k]['pkey'];
                                        $_POST['hidSupplierKey[]'] =  $rsSupplierDetail[$k]['supplierkey'];
                                        $_POST['supplierName[]'] =  $rsSupplierDetail[$k]['suppliername'];
                                        $_POST['refSupplierCode[]'] =  $rsSupplierDetail[$k]['refsuppliercode'];
                                        $_POST['disposalSupplierWeight[]'] =   $obj->formatNumber($rsSupplierDetail[$k]['disposalsupplierweight'], 2);
                                    }

                                ?>
                                    <div class="div-table-row <?php echo $class; ?>">
                                        <div class="div-table-col detail-col-detail">
                                            <?php echo $obj->inputText('supplierName[]', array('overwritePost' => $overwrite, 'etc' =>  $etc, 'allowedStatusForEdit' => array(1,2))); ?>
                                            <?php echo $obj->inputHidden('hidSupplierDetailKey[]', array('overwritePost' => $overwrite, 'etc' => $etc, 'allowedStatusForEdit' => array(1,2))); ?>
                                            <?php echo $obj->inputHidden('hidSupplierKey[]', array('overwritePost' => $overwrite, 'etc' => $etc, 'allowedStatusForEdit' => array(1,2))); ?>
                                        </div>
                                        <div class="div-table-col detail-col-detail">
                                            <?php echo $obj->inputText('refSupplierCode[]', array('overwritePost' => $overwrite, 'etc' =>  $etc, 'allowedStatusForEdit' => array(1,2))); ?>
                                        </div>
                                        <div class="div-table-col detail-col-detail">
                                            <?php echo $obj->inputDecimal('disposalSupplierWeight[]', array('overwritePost' => $overwrite, 'etc' => 'style="text-align:right;"' . $etc, 'allowedStatusForEdit' => array(1,2))); ?>
                                        </div>
                                        <div class="icon-col div-table-col detail-col-detail <?php echo $obj->hideOnDisabled(); ?>"><?php echo $obj->inputLinkButton('btnAddSupplierRows', '<i class="fas fa-plus-circle"></i>', array('class' => 'btn btn-link add-row-button', 'etc' => 'attr-template="supplier-row-template"')); ?></div>
                                        <div class="icon-col div-table-col detail-col-detail <?php echo $obj->hideOnDisabled(); ?>"><?php echo $obj->inputLinkButton('btnDeleteRows', '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button', 'etc' =>  'tabIndex="-1" style="padding:6px 0; ' . $hideDeleteIcon . '"')); ?></div>
                                    </div>
                                <?php  }   ?>

                            </div>
                            <div style="clear:both; height:1em;"></div>
                            <div class="div-table" style="float:right">
                                <div class="div-table-row form-group">
                                    <div class="div-table-col-3" style="text-align:right;">
                                        <?php echo ucwords($obj->lang['total']); ?>
                                    </div>
                                    <div class="div-table-col-3">
                                        <?php echo $obj->inputDecimal('totalSupplierWeight', array('readonly' => true, 'etc' => 'style="text-align:right; width:120px;"')); ?>
                                    </div>
                                </div>

                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['total']); ?> <?php echo ucwords($obj->lang['customerWeight']); ?>  / <?php echo ucwords($obj->lang['total']); ?> <?php echo ucwords($obj->lang['supplierWeight']); ?></label>
                                <div class="col-xs-9">
                                  <div class="flex">
                                        <div class="consume"><?php echo  $obj->inputDecimal('totalSupplierWeight', array('readonly' => true)) ; ?></div>
                                        <div>Kg</div> 
                                        <div class="consume"><?php echo  $obj->inputDecimal('totalCustomerWeight', array('readonly' => true)); ?></div>  
                                        <div>Kg</div>  
                                    </div>  
                                </div>
                            </div> 
                            </div>
                        </div> -->
                    </div>
                </div>
            </div>

            <div style="clear:both; height:2em;"></div>
            <div class="div-table transaction-detail" style="width:100%; ">
                <div class="div-table-row">
                    <div class="div-table-col detail-col-header" style="width:200px; text-align:left;"><?php echo ucwords($obj->lang['JOCode']); ?></div>
                    <!-- <div class="div-table-col detail-col-header" style="width:200px; text-align:left;"><?php echo ucwords($obj->lang['contract']); ?></div> -->
                    <div class="div-table-col detail-col-header"><?php echo ucwords($obj->lang['customer']); ?></div>
                    <div class="div-table-col detail-col-header" style="width:250px; text-align:left;"><?php echo ucwords($obj->lang['workOrderCode']); ?></div>
                    <!-- <div class="div-table-col detail-col-header" style="width:120px; text-align:right;"><?php echo ucwords($obj->lang['customerWeight']); ?> (Kg)</div>
                    <div class="div-table-col detail-col-header" style="width:120px; text-align:right;"><?php echo ucwords($obj->lang['supplierWeight']); ?> (Kg)</div> -->
                    <!-- <div class="div-table-col detail-col-header" style="width:120px; text-align:right;"><?php echo ucwords($obj->lang['maxWeight']); ?> (Kg)</div> -->
                    <!--                    <div class="div-table-col detail-col-header" style="width:130px; text-align:center;"><?php echo ucwords($obj->lang['status']); ?></div> -->
                    <div class="div-table-col detail-col-header icon-col <?php echo $obj->hideOnDisabled(); ?>"></div>
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
                    $readonlyJOCode = false;
                    $allowEditStatus = array(1,2);
                    if ($i == $totalRows) {
                        $class = 'detail-row-template';
                        $overwrite = false;
                        $etc = 'disabled="disabled"';
                        $readonlyDetail = false;
                        $allowEditStatus = array(1,2);
                    } else {
                        $decimal = 0;
                        $inputnumber = 'inputnumber';

                        $_POST['hidDetailKey[]'] =  $rsDetail[$i]['pkey'];
                        $_POST['hidJobOrderKey[]'] =  $rsDetail[$i]['joborderkey'];
                        $_POST['jobOrderCode[]'] =  $rsDetail[$i]['jobordercode'];
                        $_POST['serviceName[]'] =  $rsDetail[$i]['servicename'];
                        $_POST['customerName[]'] =  $rsDetail[$i]['customername'];
                        $_POST['contractName[]'] =  $rsDetail[$i]['contractname'];
                        $_POST['hidCustomerKey[]'] =  $rsDetail[$i]['customerkey'];
                        $_POST['hidServiceKey[]'] =  $rsDetail[$i]['servicekey'];
                        $_POST['workOrderCode[]'] =  $rsDetail[$i]['workordercode'];
                        $statusDetailName =  $rsDetail[$i]['statusname'];
                        $statusStyle =  $rsDetail[$i]['statuscolor'];
                        $_POST['quota[]'] =   $obj->formatNumber($rsDetail[$i]['quota'], 2);
                        $_POST['supplierWeight[]'] =   $obj->formatNumber($rsDetail[$i]['supplierweight'], 2);
                        $_POST['customerWeight[]'] =   $obj->formatNumber($rsDetail[$i]['customerweight'], 2);

                        $rsDisposalWorkOrderCol = $rsDisposalWorkOrder[$rsDetail[$i]['pkey']];
                
                        if (!empty($rsDisposalWorkOrderCol) && $rs[0]['statuskey'] > TRANSACTION_STATUS['menunggu']) {

                            if($rsDisposalWorkOrderCol[0]['refdetailkey'] == $rsDetail[$i]['pkey']) {
                                if($rsDisposalWorkOrderCol[0]['statuskey'] > TRANSACTION_STATUS['menunggu']) {
                                    // Jika key sama dan status > menunggu
                                    $readonlyDetail = true;
                                    $allowEditStatus = array(1);
                                } else if ($rsDisposalWorkOrderCol[0]['statuskey'] == TRANSACTION_STATUS['menunggu']) {
                                    // Jika key sama dan status == menunggu
                                    $readonlyDetail = false;
                                    $allowEditStatus = array(1, 2);
                                }
                            }else{
                                // Jika key tidak sama
                                $readonlyDetail = false;
                                $allowEditStatus = array(1, 2);
                            }
                        }

                    }
                    //$obj->setLog($allowEditStatus, true);
                ?>

                    <div class="div-table-row <?php echo $class; ?>">
                        <div class="div-table-col detail-col-detail" style="padding:0">
                            <div class="div-table" style="width:100%">
                                <div class="div-table-row">
                                    <div class="div-table-col detail-col-detail" style="width:200px;">
                                        <?php //echo $obj->inputText('jobOrderCode[]', array('overwritePost' => $overwrite, 'etc' => $etc, 'allowedStatusForEdit' => array(1))); ?>
                                        <?php echo $obj->inputText('jobOrderCode[]', array('overwritePost' => $overwrite, 'etc' => $etc, 'readonly' => $readonlyDetail)); ?>
                                        <?php echo $obj->inputHidden('hidDetailKey[]', array('overwritePost' => $overwrite, 'etc' => $etc)); ?>
                                        <?php echo $obj->inputHidden('hidJobOrderKey[]', array('overwritePost' => $overwrite, 'etc' => $etc)); ?>
                                        <?php echo $obj->inputHidden('hidServiceKey[]', array('overwritePost' => $overwrite, 'etc' => $etc)); ?>
                                    </div>
                                    <!-- <div class="div-table-col detail-col-detail" style="width:200px;">
                                        <?php echo $obj->inputText('contractName[]', array('readonly' => true, 'overwritePost' => $overwrite, 'etc' => 'style="text-align:left;" ' . $etc)); ?>
                                    </div> -->
                                    <div class="div-table-col detail-col-detail">
                                        <?php echo $obj->inputHidden('hidCustomerKey[]', array('overwritePost' => $overwrite, 'etc' => $etc)); ?>
                                        <?php echo $obj->inputText('customerName[]', array('readonly' => true, 'overwritePost' => $overwrite, 'etc' => 'style="text-align:left;" ' . $etc)); ?>
                                    </div>
                                    <div class="div-table-col detail-col-detail" style="width:250px;">
                                        <?php echo $obj->inputText('workOrderCode[]', array('overwritePost' => $overwrite, 'etc' => 'style="text-align:left;" ' . $etc, 'readonly' => $readonlyDetail)); ?>
                                    </div>
                                    <!-- <div class="div-table-col detail-col-detail" style="width:120px;">
                                        <?php echo $obj->inputDecimal('customerWeight[]', array('overwritePost' => $overwrite, 'etc' => 'style="text-align:right;" ' . $etc, 'allowedStatusForEdit' => array(1))); ?>
                                    </div>
                                    <div class="div-table-col detail-col-detail" style="width:120px;">
                                        <?php echo $obj->inputDecimal('supplierWeight[]', array('readonly' => true,'overwritePost' => $overwrite, 'etc' => 'style="text-align:right;" ' . $etc)); ?>
                                    </div> -->
                                    <!-- <div class="div-table-col detail-col-detail" style="width:120px;">
                                        <?php echo $obj->inputDecimal('quota[]', array('readonly' => true, 'overwritePost' => $overwrite, 'etc' => 'style="text-align:right;" ' . $etc)); ?>
                                    </div> -->
                                    <!--                                    <div class="div-table-col detail-col-detail status-label " style="text-align: center; width:130px; "><label class=" <?php echo $statusStyle; ?>"><?php echo $statusDetailName; ?></label></div> -->
                                </div>
                            </div>
                        </div>
                        <div class="div-table-col detail-col-detail icon-col align-top-adjust  <?php echo $obj->hideOnDisabled(); ?>">
                            <?php echo $obj->inputLinkButton('btnDeleteRows', '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button', 'etc' => 'tabIndex="- 1"', 'allowedStatusForEdit' => $allowEditStatus)); ?>
                        </div>
                    </div>
                <?php }   ?>

            </div>

            <div style="clear:both; height:1em;"></div>
            <div style="float:left; display:inline-block;"><?php echo $obj->inputButton('btnAddRows', $obj->lang['addRows'], array('class' => 'btn btn-primary btn-second-tone', 'allowedStatusForEdit' => array(1,2))); ?></div>

            <!-- <div class="div-table" style="float:right">
				<div class="div-table-row">
					<div class="div-table-col">
						<div class="div-table" style="float:right;">
							<div class="div-table-row form-group">
								<div class="div-table-col-3" style="text-align:right;">
									<?php echo ucwords($obj->lang['total']); ?>
								</div>
								<div class="div-table-col-3">
									<div class="flex">
										<div class="consume" style="width:120px;">
                                            <?php echo $obj->inputDecimal('totalCustomerWeight', array('readonly' => true, 'etc' => 'style="text-align:right;"')); ?>
                                        </div>
										<div class="consume" style="width:110px;">
                                            <?php echo $obj->inputDecimal('totalProRate', array('readonly' => true, 'etc' => 'style="text-align:right;"')); ?>
                                        </div>
									</div>
								</div>
							</div>
						</div>
					</div>
					 <div class="div-table-col icon-col <?php echo $obj->hideOnDisabled(); ?>"></div>
				</div> 
            </div> -->

            <div class="form-button-margin"></div>
            <div class="form-button-panel">
                <?php echo $obj->generateSaveButton(array(), true);   ?>
            </div>

        </form>
        <?php echo $obj->showDataHistory(); ?>

    </div>
</body>

</html>