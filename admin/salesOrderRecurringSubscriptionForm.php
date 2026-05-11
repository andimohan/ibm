<?php
require_once '../_config.php';
require_once '../_include-v2.php';

includeClass('SalesOrderRecurringSubscription.class.php');
$salesOrderRecurringSubscription = createObjAndAddToCol(new SalesOrderRecurringSubscription());

$itemUnit = createObjAndAddToCol(new ItemUnit());
$item = createObjAndAddToCol(new Item());
$warehouse = createObjAndAddToCol(new Warehouse());
$city = createObjAndAddToCol(new City());
$customer = createObjAndAddToCol(new Customer());
$recurringPeriod = createObjAndAddToCol(new RecurringPeriod());

$obj = $salesOrderRecurringSubscription;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
// some modules that have different security object from that of their class

if (!$security->isAdminLogin($securityObject, 10, true));

$formAction = 'salesOrderRecurringSubscriptionList';

$parentFileName = $_GET['fileName'];
$parentPanelId = $_GET['selectedPanelId'];
$parentTitle = $_GET['title'];

$editRecurringPeriodInactiveCriteria = '';
$editWarehouseInactiveCriteria = '';
$editTermOfPaymentInactiveCriteria = '';
$editPaymentMethodInactiveCriteria = '';
$editSalesInactiveCriteria = '';
$editCityInactiveCriteria = '';
$editCustomCodeInactiveCriteria = '';

$rsSalesDetail = array();
$rsPaymentMethodDetail = array(); 

$_POST['trDate'] = date('d / m / Y H:i');

$saleskey = base64_decode($_SESSION[$obj->loginAdminSession]['id']);
$_POST['selSalesKey'] = $saleskey;

$finalDiscDecimal = 0;
$finalDiscDecimalType = 'inputnumber';

$finalDiscDecimal2 = 0;
$finalDiscDecimalType2 = 'inputnumber';

$totalWeight = 0;

$rs = prepareOnLoadData($obj); 

$rsKey = $obj->getTableKeyAndObj($obj->tableName, array('key'));

if (!empty($_GET['id'])) {
   $id = $_GET['id'];

    $rsSalesDetail = $obj->getDetailWithRelatedInformation($id);
 
	$rsCustomer = $customer->getDataRowById($rs[0]['customerkey']);
	$_POST['customerName'] = $rsCustomer[0]['code'] .' - '.$rsCustomer[0]['name'] ;
	
   $_POST['hidSalesKey'] = $rs[0]['saleskey'];
   if (!empty($rs[0]['saleskey'])) {
      $rsSales = $employee->getDataRowById($rs[0]['saleskey']);
      $_POST['salesName'] = $rsSales[0]['name'];
   }

   $_POST['lastRecurringDate'] = ($obj->isEmptyDate($rs[0]['lastrecurringdate'])) ? '00 / 00 / 0000' : $obj->formatDBDate($rs[0]['lastrecurringdate']);
   $_POST['nextRecurringDate'] = ($obj->isEmptyDate($rs[0]['nextrecurringdate'])) ? '00 / 00 / 0000' : $obj->formatDBDate($rs[0]['nextrecurringdate']); 
	   
   $editRecurringPeriodInactiveCriteria = ' or  ' . $recurringPeriod->tableName . '.pkey = ' . $obj->oDbCon->paramString($rs[0]['recurringperiodkey']);
   $editWarehouseInactiveCriteria = ' or  ' . $warehouse->tableName . '.pkey = ' . $obj->oDbCon->paramString($rs[0]['warehousekey']); 
   $editSalesInactiveCriteria = 'or ' . $employee->tableName . '.pkey = ' . $obj->oDbCon->paramString($rs[0]['saleskey']);

}



$arrStatus = $obj->generateComboboxOpt(array('data' => $obj->getAllStatus(), 'label' => 'status'));
$arrWarehouse = $warehouse->generateComboboxOpt(null, array('criteria' => ' and (' . $warehouse->tableName . '.statuskey = 1' . $editWarehouseInactiveCriteria . ')'));
$arrDefaultUnit = $itemUnit->generateComboboxOpt(null, array('criteria' => ' and (' . $itemUnit->tableName . '.statuskey = 1 )'));
$arrRecurringPeriod = $recurringPeriod->generateComboboxOpt(null, array('criteria' => ' and (' . $recurringPeriod->tableName . '.statuskey = 1' . $editRecurringPeriodInactiveCriteria . ')'));
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title> 
 
<script type="text/javascript">  
   
   jQuery(document).ready(function(){  
        var tabID = selectedTab.newPanel[0].id;
         var tablekey = <?php echo $rsKey['key']; ?>;
         var cashTOP = Array();
   
         <?php
         for ($i = 0; $i < count($rsTOP); $i++) {
            if ($rsTOP[$i]['duedays'] <> 0)
               echo 'cashTOP.push(' . $rsTOP[$i]['pkey'] . ');' . chr(13);
         }
         ?> 
        
      var varConstant = {  
            TABLEKEY : tablekey, 
         };
             
                
        salesOrderRecurringSubscription = new SalesOrderRecurringSubscription(tabID,<?php echo json_encode($rs); ?>,cashTOP ,varConstant);
        prepareHandler(salesOrderRecurringSubscription); 
        
        var fieldValidation =  {
                                 code: { 
                                        validators: {
                                            notEmpty: {
                                                message: phpErrorMsg.code[1]
                                            }, 
                                        }
                                    }, 

                                   customerName: { 
                                        validators: {
                                            notEmpty: {
                                                message:  phpErrorMsg.customer[1]
                                            }
                                        } 
                                    },

                                } ; 
        
        setFormValidation(getTabObj(), $('#defaultForm-' + tabID), fieldValidation, <?php echo json_encode($obj->validationFormSubmitParam()); ?>  );
    });
    
</script>

</head> 

<body>                    
<div style="width:100%; margin:auto; " class="tab-panel-form">   
  <div class="notification-msg"></div>
  
  <form id="defaultForm" method="post" class="form-horizontal" action="<?php echo $formAction; ?>" > 
    <?php prepareOnLoadDataForm($obj); ?>   
    <?php echo $obj->inputHidden('hidSendEmail'); ?>  
     
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
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['refCode']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputText('refCode'); ?>
                                        </div> 
                                    </div> 
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['date']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputDate('trDate'); ?> 
                                        </div> 
                                    </div>    
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['warehouse']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputSelect('selWarehouseKey', $arrWarehouse); ?>
                                        </div> 
                                    </div>    
                                    <?php if (!empty($arrCustomCode)) { ?>
                                       <div class="form-group">
                                           <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['salesType']); ?></label> 
                                           <div class="col-xs-9"> 
                                               <?php echo $obj->inputSelect('selCustomCode', $arrCustomCode); ?>
                                           </div> 
                                       </div>  
                                    <?php } ?>
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
                                                    'popupForm' => array(
                                                       'url' => 'customerForm.php',
                                                       'element' => array(
                                                          'value' => 'customerName',
                                                          'key' => 'hidCustomerKey'
                                                       ),
                                                       'width' => '1000px',
                                                       'title' => ucwords($obj->lang['add'] . ' - ' . $obj->lang['customer'])
                                                    )
                                                 )
                                              );
                                              ?> 
                                        </div> 
                                    </div> 
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['recurringPeriod']); ?></label>
                                        <div class="col-xs-9">
                                            <?php echo $obj->inputSelect('selRecurringPeriod', $arrRecurringPeriod, array('allowedStatusForEdit' => array(1, 2))); ?>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['salesman']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php
                                            echo $obj->inputAutoComplete(
                                               array(
                                                  'element' => array(
                                                     'value' => 'salesName',
                                                     'key' => 'hidSalesKey'
                                                  ),
                                                  'source' => array(
                                                     'url' => 'ajax-employee.php',
                                                     'data' => array(
                                                        'action' => 'searchData',
                                                        'issales' => 1
                                                     )
                                                  )
                                               )
                                            );
                                            ?>  
                                        </div> 
                                    </div>  
							 
							 		<div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['lastInvoiceDate']); ?></label>
                                        <div class="col-xs-9">
                                            <?php echo $obj->inputText('lastRecurringDate', array('readonly' => true)); ?>
                                        </div>
                                    </div> 
							 
							 		<div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['nextInvoiceDate']); ?></label>
                                        <div class="col-xs-9">
                                            <?php echo $obj->inputText('nextRecurringDate', array('readonly' => true)); ?>
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
      
        <div class="div-table mnv-transaction transaction-detail" style="width:100%; border-bottom:1px solid #333; ">
                <div class="div-table-row"> 
                    <div class="div-table-col detail-col-header"><?php echo ucwords($obj->lang['itemName']); ?></div>
                    <div class="div-table-col detail-col-header" style="width:80px; text-align:right;"><?php echo ucwords($obj->lang['qty']); ?></div>
                    <div class="div-table-col detail-col-header" style="width:80px;"><?php echo ucwords($obj->lang['unit']); ?></div>
                    <div class="div-table-col detail-col-header" style="width:100px; text-align:right;"><?php echo ucwords($obj->lang['price']); ?> @</div>
                    <div class="div-table-col detail-col-header" style="width:100px; text-align:right; padding-right:0;"></div>
                    <div class="div-table-col detail-col-header" style="width:80px; text-align:right; padding-left:0.2em;"><?php echo ucwords($obj->lang['discount']); ?> @</div>
                    <div class="div-table-col detail-col-header" style="width:180px; text-align:right;"><?php echo ucwords($obj->lang['subtotal']); ?></div>
                    <div class="div-table-col detail-col-header  <?php echo $obj->hideOnDisabled(); ?> icon-col"></div>
                </div>
                
            <?php
            $totalRows = count($rsSalesDetail);

            for ($i = 0; $i <= $totalRows; $i++) {

               $class = 'transaction-detail-row';
               $overwrite = true;
               $disabled = false;
               $arrUnit = $arrDefaultUnit;

               if ($i == $totalRows) {
                  $class = 'detail-row-template';
                  $overwrite = false;
                  $disabled = true;
                  $unitname = 'Pcs';
               } else {
                  $decimal = 0;
                  $inputnumber = 'inputnumber';

                  if ($rsSalesDetail[$i]['discounttype'] == 2) {
                     $decimal = 2;
                     $inputnumber = 'inputdecimal';
                  }


                  $_POST['hidDetailKey[]'] = $rsSalesDetail[$i]['pkey'];
                  $_POST['hidItemKey[]'] = $rsSalesDetail[$i]['itemkey'];
                  $_POST['itemName[]'] = $rsSalesDetail[$i]['itemname'];
                  $_POST['trDetailDesc[]'] = $rsSalesDetail[$i]['trdesc'];
                  $_POST['qty[]'] = $obj->formatNumber($rsSalesDetail[$i]['qty']);
                  $_POST['priceInUnit[]'] = $obj->formatNumber($rsSalesDetail[$i]['priceinunit']);
                  $_POST['selDiscountType[]'] = $rsSalesDetail[$i]['discounttype'];
                  $_POST['discountValueInUnit[]'] = $obj->formatNumber($rsSalesDetail[$i]['discount'], $decimal);
                  $_POST['detailSubtotal[]'] = $obj->formatNumber($rsSalesDetail[$i]['total']);
                  $_POST['selUnit[]'] = $rsSalesDetail[$i]['unitkey'];
                  $_POST['hidGramasi[]'] = $rsSalesDetail[$i]['weight'];
                  $_POST['hidGramasiSubtotal[]'] = $rsSalesDetail[$i]['weight'] * $rsSalesDetail[$i]['qtyinbaseunit'];

                  $arrUnit = $obj->convertForCombobox($item->getAvailableUnit($rsSalesDetail[$i]['itemkey']), 'conversionunitkey', 'unitname', '', array('relconversionmultiplier' => 'conversionmultiplier'));

               }

               ?>
            
                
                   <div class="div-table-row <?php echo $class; ?>">
                       <div class="div-table-col detail-col-detail" style="vertical-align:top">
                           <?php echo $obj->inputText('itemName[]', array('overwritePost' => $overwrite, 'disabled' => $disabled, 'add-class' => 'mnv-barcode-input')); ?>
						   <?php echo $obj->inputTextArea('trDetailDesc[]', array('overwritePost' => $overwrite, 'disabled' => $disabled,'etc'=>'style="height:10em; margin-top:0.2em"', 'add-class' => 'mnv-barcode-input')); ?> 
                           <?php echo $obj->inputHidden('hidItemKey[]', array('overwritePost' => $overwrite, 'disabled' => $disabled)); ?>
                           <?php echo $obj->inputHidden('hidGramasi[]', array('overwritePost' => $overwrite, 'disabled' => $disabled)); ?>
                           <?php echo $obj->inputHidden('hidGramasiSubtotal[]', array('overwritePost' => $overwrite, 'disabled' => $disabled)); ?>
                           <?php echo $obj->inputHidden('hidDetailKey[]', array('overwritePost' => $overwrite, 'disabled' => $disabled)); ?>
                       </div> 
                       <div class="div-table-col detail-col-detail" style="vertical-align:top"><?php echo $obj->inputNumber('qty[]', array('overwritePost' => $overwrite, 'value' => 1, 'etc' => 'style="text-align:right;"', 'disabled' => $disabled)); ?></div>
                       <div class="div-table-col detail-col-detail" style="vertical-align:top"><?php echo $obj->inputSelect('selUnit[]', $arrUnit, array('overwritePost' => $overwrite, 'disabled' => $disabled)); ?></div>
                       <div class="div-table-col detail-col-detail" style="vertical-align:top"><?php echo $obj->inputNumber('priceInUnit[]', array('overwritePost' => $overwrite, 'etc' => 'style="text-align:right;"', 'disabled' => $disabled)); ?></div>
                       <div class="div-table-col detail-col-detail" style="vertical-align:top"><?php echo $obj->inputNumber('discountValueInUnit[]', array('overwritePost' => $overwrite, 'etc' => 'style="text-align:right;"', 'disabled' => $disabled)); ?></div>
                       <div class="div-table-col detail-col-detail" style="vertical-align:top"><?php echo $obj->inputSelect('selDiscountType[]', $obj->arrDiscountType, array('overwritePost' => $overwrite, 'disabled' => $disabled)); ?></div>
                       <div class="div-table-col detail-col-detail" style="vertical-align:top"><?php echo $obj->inputNumber('detailSubtotal[]', array('overwritePost' => $overwrite, 'etc' => 'style="text-align:right;" ', 'readonly' => true, 'disabled' => $disabled)); ?></div>
                       <div class="div-table-col detail-col-detail <?php echo $obj->hideOnDisabled(); ?>"  style="vertical-align:top"><?php echo $obj->inputLinkButton('btnDeleteRows', '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button', 'etc' => 'tabIndex="-1"')); ?></div>
                   </div>
                 
            <?php } ?> 
                   
         </div>        
          
          <div style="clear:both; height:1em;"></div> 
          <div style="float:left; display:inline-block;"><?php echo $obj->inputButton('btnAddRows', $obj->lang['addRows'], array('class' => 'btn btn-primary btn-second-tone')); ?></div>
       
        <div>   
                  <div class="<?php echo $obj->hideOnDisabled(); ?>" style="float:right; width:30px; height: 1em"></div>  
                    <div class="div-table" style="float:right;">
                     <div class="div-table-row  form-group"> 
                        <div class="div-table-col-3" style="text-align:right;"> 
                              <?php echo ucwords($obj->lang['total']); ?>
                        </div>  
                        <div class="div-table-col-3" style="width:180px"> 
                              <?php echo $obj->inputNumber('grandTotal', array ('readonly' => true, 'etc' => 'style="text-align:right;"')) ;?>    
                        </div>  
                     </div> 
                  </div>   
            </div>


         
      
         <div class="form-button-margin"></div>
         <div class="form-button-panel" >  
         <?php echo $obj->generateSaveButton(array(1,2), true); ?> 
        </div> 
        
    </form>  

     <?php echo $obj->showDataHistory(); ?>
</div> 
</body>

</html>
