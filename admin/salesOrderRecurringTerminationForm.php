<?php
require_once '../_config.php';
require_once '../_include-v2.php';

includeClass('SalesOrderRecurringTermination.class.php');
$salesOrderRecurringTermination = createObjAndAddToCol(new SalesOrderRecurringTermination());

$customer               = createObjAndAddToCol(new Customer());
$warehouse              = createObjAndAddToCol(new Warehouse());
$salesOrderRecurringSubscription = createObjAndAddToCol(new SalesOrderRecurringSubscription());

$obj                    = $salesOrderRecurringTermination;
$securityObject         = $obj->securityObject; 
// the value of security object is manually inserted to handle 
// some modules that have different security object from that of their class

if (!$security->isAdminLogin($securityObject, 10, true));

$formAction = 'salesOrderRecurringTerminationList';
$isQuickAdd = (isset($_GET) && !empty($_GET['quickadd'])) ? true : false;

$_POST['trDate'] = date('d / m / Y');

$editWarehouseInactiveCriteria = '';

$rs = prepareOnLoadData($obj);

if (!empty($_GET['id'])) {
   $id = $_GET['id'];

   $_POST['trDate'] = $obj->formatDBDate($rs[0]['trdate'], 'd / m / Y');
   $_POST['selWarehouse'] = $rs[0]['warehousekey'];

   $_POST['hidSubscriptionKey'] = $rs[0]['refsubscriptionkey'];

   if(!empty($rs[0]['refsubscriptionkey']))
   {
      $rsSOSubscription = $salesOrderRecurringSubscription->getDataRowById($rs[0]['refsubscriptionkey']);

      $_POST['hidSubscriptionKey'] = $rsSOSubscription[0]['pkey'];
      $_POST['subscriptionCode'] = $rsSOSubscription[0]['code'];

      if(!empty($rsSOSubscription[0]['customerkey']))
      {
         $rsCustomer = $customer->getDataRowById($rsSOSubscription[0]['customerkey']);
   
         $_POST['hidCustomerKey'] = $rsCustomer[0]['pkey'];
         $_POST['customerName'] = $rsCustomer[0]['name'];
      }
   }


   $_POST['trDesc'] = $rs[0]['trdesc'];

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

            var salesOrderRecurringTermination = new SalesOrderRecurringTermination(tabID);
            prepareHandler(salesOrderRecurringTermination);
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
            <?php echo $obj->inputHidden('hidCurrentSupplierKey'); ?>
            <?php echo $obj->inputHidden('hidCurrentSupplierName'); ?>

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
                           <?php echo $obj->inputSelect('selStatus', $arrStatus, array('disabled' => true)); ?>
                        </div>
                     </div>
                     <div class="form-group">
                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['code']); ?>
                        </label>
                        <div class="col-xs-9">
                          <?php echo $obj->inputAutoCode('code'); ?>
                        </div>
                     </div>

                  
                     <div class="form-group">
                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['date']); ?>
                        </label>
                        <div class="col-xs-9">
                          <?php echo $obj->inputDate('trDate'); ?>
                        </div>
                     </div>

                     <div class="form-group">
                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['warehouse']); ?>
                        </label>
                        <div class="col-xs-9">
                           <?php echo $obj->inputSelect('selWarehouse', $arrWarehouse); ?>
                        </div>
                     </div>

                     <div class="form-group">
                           <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['subscriptionCode']); ?></label> 
                           <div class="col-xs-9"> 
                                 <?php echo $obj->inputAutoComplete(
                                    array(
                                       'objRefer' => $salesOrderRecurringSubscription,
                                       'revalidateField' => true,
                                       'element' => array(
                                          'value' => 'subscriptionCode',
                                          'key' => 'hidSubscriptionKey'
                                       ),
                                       'source' => array(
                                          'url' => 'ajax-sales-order-recurring-subscription.php',
                                          'data' => array('action' => 'searchData', 'searchField' => 'code')
                                       ),
                                       'callbackFunction' => 'getTabObj().updateCustomerInformation()'
                                    )
                                 );
                                 ?> 
                           </div> 
                     </div>

                     <div class="form-group">
                           <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['customer']); ?></label> 
                           <div class="col-xs-9"> 
                                 <?php echo $obj->inputAutoComplete(
                                    array(
                                       'objRefer' => $customer,
                                       'revalidateField' => true,
                                       'element' => array(
                                          'value' => 'customerName',
                                          'key' => 'hidCustomerKey'
                                       ),
                                       'source' => array(
                                          'url' => 'ajax-customer.php',
                                          'data' => array('action' => 'searchData', 'searchField' => 'code,name')
                                       ),
                                       'readonly' => true
                                    )
                                 );
                                 ?> 
                           </div> 
                     </div>

                  </div>
               </div>

               <div class="div-table-col">
                  <div class="div-tab-panel">
                     <div class="div-table-caption border-blue">
                        <?php echo ucwords($obj->lang['note']); ?>
                     </div>
                     <div class="div-table" style="width:100%">
                        <div class="form-group"> 
                           <div class="col-xs-12"> 
                              <?php echo  $obj->inputTextArea('trDesc', array('etc' => 'style="height:10em;"')); ?>
                           </div> 
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
