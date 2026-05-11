<?php
require_once '../_config.php';
require_once '../_include-v2.php';

includeClass('TruckingPurchaseRefund.class.php');
$truckingPurchaseRefund = createObjAndAddToCol(new TruckingPurchaseRefund());
$supplier               = createObjAndAddToCol(new Supplier());
$customer               = createObjAndAddToCol(new Customer());
$warehouse              = createObjAndAddToCol(new Warehouse());
$truckingServiceOrder               = createObjAndAddToCol(new TruckingServiceOrder());

$obj                    = $truckingPurchaseRefund;
$securityObject         = $obj->securityObject; // the value of security object is manually inserted to handle 
// some modules that have different security object from that of their class

if (!$security->isAdminLogin($securityObject, 10, true));

$formAction = 'truckingPurchaseRefundList';
$isQuickAdd = (isset($_GET) && !empty($_GET['quickadd'])) ? true : false;

$_POST['trDate'] = date('d / m / Y');

$editWarehouseInactiveCriteria = '';

$rs = prepareOnLoadData($obj);

if (!empty($_GET['id'])) {
   $id = $_GET['id'];

   $_POST['trDate'] = $obj->formatDbDate($rs[0]['trdate'], 'd / m / Y');

   if(!empty($rs[0]['supplierkey']))
   {
      $rsSupplier = $supplier->getDataRowById($rs[0]['supplierkey']);
      $_POST['supplierName'] = $rsSupplier[0]['name'];
   }
   
   if(!empty($rs[0]['customerkey']))
   {
      $rsCustomer = $customer->getDataRowById($rs[0]['customerkey']);
      $_POST['customerName'] = $rsCustomer[0]['name'];
   }

    $_POST['selWarehouseKey'] = $rs[0]['warehousekey'];

   if(!empty($rs[0]['refjoborderkey']))
   {
      $rsSalesOrder = $truckingServiceOrder->getDataRowById($rs[0]['refjoborderkey']);
      $_POST['soNumber'] = $rsSalesOrder[0]['code'];
   }

   $_POST['total'] = $obj->formatNumber($rs[0]['total']);

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

            var tabID = <?php echo ($isQuickAdd) ? $_GET['tabID'] : 'selectedTab.newPanel[0].id'; ?>;

            var truckingPurchaseRefund = new TruckingPurchaseRefund(tabID);
            prepareHandler(truckingPurchaseRefund);
            var fieldValidation = {
                code: {
                    validators: {
                        notEmpty: {
                            message: phpErrorMsg.code[1]
                        },
                    }
                },

                supplierName: {
                    validators: {
                        notEmpty: {
                            message: phpErrorMsg.supplier[1]
                        },
                    }
                },
               //  customerName: {
               //      validators: {
               //          notEmpty: {
               //              message: phpErrorMsg.customer[1]
               //          },
               //      }
               //  },
                soNumber: { 
                    validators: {
                        notEmpty: {
                            message:  phpErrorMsg.jobOrder[1]
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
            <?php echo $obj->inputHidden('hidCurrentSupplierKey'); ?>
            <?php echo $obj->inputHidden('hidCurrentSupplierName'); ?>

            <div class="div-table main-tab-table-2">
                <div class="div-table-row">
                    <div class="div-table-col">
                        <div class="div-tab-panel">
                            <div class="div-table-caption border-orange"><?php echo ucwords($obj->lang['generalInformation']); ?></div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['status']); ?></label>
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
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['warehouse']); ?>
                                </label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputSelect('selWarehouseKey', $arrWarehouse); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['supplier']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputAutoComplete(
                                       array(
                                          'objRefer'         => $supplier,
                                          'revalidateField'  => true,
                                          'element'          => array(
                                             'value' => 'supplierName',
                                             'key'   => 'hidSupplierKey'
                                          ),
                                          'source'           => array(
                                             'url'  => 'ajax-supplier.php',
                                             'data' => array('action' => 'searchData')
                                          ),
                                          'callbackFunction' => 'getTabObj().updateSupplierInformation(this,event, ui)'

                                       )
                                    );
                                    ?>
                                </div>
                            </div>

                           <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo $obj->lang['jobOrder']; ?></label> 
                                        <div class="col-xs-9"> 
                                             <?php     
                                                   echo $obj->inputAutoComplete(array(
                                                                             'objRefer' => $truckingServiceOrder,
                                                                             'revalidateField' => true, 
                                                                             'element' => array('value' => 'soNumber',
                                                                                                'key' => 'hidSOKey'),
                                                                             'source' => array(
                                                                                                 'url' => 'ajax-trucking-service-order.php',
                                                                                                 'data' => array(  'action' =>'searchData', 'statuskey' => '(2,3,4,5)' )
                                                                                             ) , 
                                                                             'allowedStatusForEdit' => array (1),
                                                                             'callbackFunction' => 'getTabObj().updateSOInformation()'
                                                                           )
                                                                     );  
                                                 
                                                       
                                                ?> 
                                        </div> 
                                    </div> 
 
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['customer']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputText('customerName', array('readonly' => true)); ?>
                                    <?php echo $obj->inputHidden('hidCustomerKey', array('readonly' => true)); ?>
                                </div>
                            </div> 

                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['jobInformation']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputText('jobInformation', array('readonly' => true)); ?>
                                </div>
                            </div> 

                            <div class="form-group">
                              <label class="col-xs-3 control-label"><?php echo $obj->lang['total']; ?></label>
                              <div class="col-xs-9">
                                 <?php echo $obj->inputNumber('total'); ?>
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