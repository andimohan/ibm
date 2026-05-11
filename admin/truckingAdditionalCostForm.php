<?php
require_once '../_config.php';
require_once '../_include-v2.php';

includeClass('TruckingAdditionalCost.class.php');
$truckingAdditionalCost = createObjAndAddToCol(new TruckingAdditionalCost());
$warehouse = createObjAndAddToCol(new Warehouse());
$truckingServiceWorkOrder = createObjAndAddToCol(new TruckingServiceWorkOrder());
$truckingCost = createObjAndAddToCol(new Service(TRUCKING_SERVICE, 1));
$truckingServiceOrder = createObjAndAddToCol(new TruckingServiceOrder());

$obj = $truckingAdditionalCost;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
// some modules that have different security object from that of their class

if (!$security->isAdminLogin($securityObject, 10, true));

$formAction = 'truckingAdditionalCostList';

$isQuickAdd = (isset($_GET) && !empty($_GET['quickadd'])) ? true : false;

$rsDetail = array();

$_POST['trDate'] = date('d / m / Y');

$rs = prepareOnLoadData($obj);

$editWarehouseInactiveCriteria = '';

if (!empty($_GET['id'])) {
    $id = $_GET['id'];
 
    if(!empty($rs[0]['refworkorderkey'])) {
        $rsWO = $truckingServiceWorkOrder->searchData('','',true, ' and ' . $truckingServiceWorkOrder->tableName.'.pkey = '.$obj->oDbCon->paramString($rs[0]['refworkorderkey']) .' ');
        $_POST['hidWorkOrderKey'] = $rs[0]['refworkorderkey'];
        $_POST['workOrderCode'] = $rsWO[0]['code'];
        $_POST['hidJobOrderKey'] = $rs[0]['refjoborderkey'];
        $_POST['jobOrderCode'] = $rsWO[0]['serviceordercode'];
        $_POST['hidEmployeeKey'] = $rs[0]['employeekey'];
        $_POST['driverName'] = $rsWO[0]['drivername'];
        $_POST['policeNumber'] =  $rsWO[0]['policenumber'];
    }

    if(!empty($rs[0]['servicekey'])) {
        $rsService = $truckingCost->searchData('','',true, ' and ' . $truckingCost->tableName.'.pkey = '.$obj->oDbCon->paramString($rs[0]['servicekey']) .' ');
        $_POST['hidServiceKey'] = $rs[0]['servicekey'];
        $_POST['serviceName'] =  $rsService[0]['name']; 
    }
        
	if (!empty($rs[0]['employeekey'])){
		$rsEmployee = $employee->getDataRowById($rs[0]['employeekey']);
		$_POST['driverName'] = $rsEmployee[0]['name'];
	}
    
     
    $editWarehouseInactiveCriteria = ' or ' . $warehouse->tableName . '.pkey = ' . $obj->oDbCon->paramString($rs[0]['warehousekey']);

}


$arrStatus = $obj->generateComboboxOpt(array('data' => $obj->getAllStatus(), 'label' => 'status'));
$arrWarehouse = $obj->convertForCombobox($warehouse->searchData('', '', true, ' and (' . $warehouse->tableName . '.statuskey = 1' . $editWarehouseInactiveCriteria . ')'), 'pkey', 'name');

$arrChargedTo[0]['pkey'] = 1;
$arrChargedTo[0]['name'] = $obj->lang['employee'];
//$arrChargedTo[1]['pkey'] = 2;
//$arrChargedTo[1]['name'] = $obj->lang['supplier'];

$arrCharged = $obj->generateComboboxOpt(array('data' => $arrChargedTo, 'label' => 'name'));


?>
<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title></title>


    <script type="text/javascript">

        jQuery(document).ready(function() {
            var tabID = <?php echo ($isQuickAdd) ?  $_GET['tabID'] : 'selectedTab.newPanel[0].id';?>;
            var truckingAdditionalCost = new TruckingAdditionalCost(tabID);

            prepareHandler(truckingAdditionalCost);

            var fieldValidation = {
                code: {
                    validators: {
                        notEmpty: {
                            message: phpErrorMsg.code[1]
                        },
                    }
                },
              
                serviceName: {
                    validators: {
                        notEmpty: {
                            message: phpErrorMsg.service[1]
                        },
                    }
                },
                 amount: {
                                            validators: { 
                                                greaterThan: {
                                                    value: 0,
                                                    inclusive: false,
                                                    separator: ',', 
                                                    message: phpErrorMsg.amount[2]
                                                }
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
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['code']); ?></label>
                                    <div class="col-xs-9">
                                        <?php echo $obj->inputAutoCode('code'); ?>
                                    </div>
                                </div>

                            <div class="form-group">
                                <label
                                    class="col-xs-3 control-label"><?php echo ucwords($obj->lang['status']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputSelect('selStatus', $arrStatus, array('disabled' => true)); ?>
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
                                    class="col-xs-3 control-label"><?php echo ucwords($obj->lang['warehouse']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputSelect('selWarehouse', $arrWarehouse, array('readonly' => true)); ?>
                                </div>
                            </div> 
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo $obj->lang['serviceWorkOrder']; ?></label>  
                                <div class="col-xs-9"> 
                                    <?php    
                                        echo $obj->inputAutoComplete(array(
                                                        'objRefer' => $truckingServiceWorkOrder,
                                                        'readonly' => false, 
                                                        'element' => array('value' => 'workOrderCode',
                                                                            'key' => 'hidWorkOrderKey'),
                                                        'source' =>array(
                                                                            'url' => 'ajax-trucking-service-work-order.php',
                                                                            'data' => array(  'action' =>'searchData', 'statuskey' => '(2)')
                                                                    ),
                                                                    'callbackFunction' => 'getTabObj().getWorkOrderData(1)'
                                                                ),
                                                        );  
                                    ?> 
                                </div> 
                            </div>  
                            
                            <div class="form-group">
                                <label
                                    class="col-xs-3 control-label"><?php echo ucwords($obj->lang['containerNumber']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputText('containerNumber'); ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo $obj->lang['jobOrder']; ?></label>  
                                <div class="col-xs-9"> 
                                    <?php    
                                        echo $obj->inputAutoComplete(array(
                                                        'objRefer' => $truckingServiceOrder,
                                                        'readonly' => true, 
                                                        'element' => array('value' => 'jobOrderCode',
                                                                            'key' => 'hidJobOrderKey'),
                                                        'source' =>array(
                                                                            'url' => 'ajax-trucking-service-order.php',
                                                                            'data' => array(  'action' =>'searchData' )
                                                                        )
                                                                    )
                                                        );  
                                    ?> 
                                </div> 
                            </div>  
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['paidTo']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputSelect('paidTo', $arrCharged); ?>
                                </div>
                            </div>

 
                              <div class="form-group inhouse">
                                        <label class="col-xs-3 control-label"><?php echo $obj->lang['driver']; ?></label> 
                                        <div class="col-xs-9"> 
                                             <?php                 
                                                    echo $obj->inputAutoComplete(array( 
                                                                                        'element' => array('value' => 'driverName',
                                                                                                           'key' => 'hidEmployeeKey'),
                                                                                        'source' =>array(
                                                                                                            'url' => 'ajax-employee.php',
                                                                                                            'data' => array(  'action' =>'searchData' , 
                                                                                                                              'isdriver' => 1 )
                                                                                                        ) , 
                                                                                      )
                                                                                );  
                                            ?> 
                                        </div> 
                                    </div>  

                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['car']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputText('policeNumber', array('readonly' => true)); ?>
                                </div>
                            </div>

                       
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo $obj->lang['service']; ?></label>  
                                <div class="col-xs-9"> 
                                    <?php    
                                        echo $obj->inputAutoComplete(array( 
                                                        'element' => array('value' => 'serviceName',
                                                                            'key' => 'hidServiceKey'),
                                                        'source' => array(
                                                                            'url' => 'ajax-service.php',
                                                                            'data' => array(  'action' =>'searchData', 'itemtype' => 2)
                                                                    )
                                                    ));  
                                            ?> 
                                </div> 
                            </div>  

                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['amount']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputNumber('amount', array('value' => 0)); ?>
                                </div>
                            </div>

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

            <div class="form-button-margin"></div>
            <div class="form-button-panel">
                <?php echo $obj->generateSaveButton(array(), true); ?>  
            </div>

        </form>

        <?php echo $obj->showDataHistory(); ?>
    </div>
</body>

</html>