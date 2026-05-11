<?php
require_once '../_config.php';
require_once '../_include-v2.php';

includeClass(array('TruckingQuotation.class.php','Service.class.php'));
$truckingQuotation = createObjAndAddToCol(new TruckingQuotation()); 
//$customer = createObjAndAddToCol(new Customer());
$location = createObjAndAddToCol(new Location());
//$supplier = createObjAndAddToCol(new Supplier());
$truckingCost = createObjAndAddToCol(new Service(TRUCKING_SERVICE, 1));
$warehouse = createObjAndAddToCol(new Warehouse());
$truckingServiceOrderCategory = createObjAndAddToCol(new TruckingServiceOrderCategory());

$obj = $truckingQuotation;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
// some modules that have different security object from that of their class

if (!$security->isAdminLogin($securityObject, 10, true));

$formAction = 'truckingQuotationList';

$isQuickAdd = (isset($_GET) && !empty($_GET['quickadd'])) ? true : false;

$rsSalesHeaderCost = array();
$rsSalesDetail = array(); 
$arrShowInvoiced = array(5);
$showInvoicedQty = false; 

//$arrContract = array();

$_POST['trDate'] = date('d / m / Y');

$rs = prepareOnLoadData($obj);
$rsContactPerson = array(); 
$rsDetail = array();

$editWarehouseInactiveCriteria = '';  

if (!empty($_GET['id'])) {
    $id = $_GET['id'];
    $_POST['trDate'] = $obj->formatDBDate($rs[0]['trdate'], 'd / m / Y ');
    $rsDetail = $obj->getDetailWithRelatedInformation($id);

}

//if (!empty($rs[0]['customerkey'])) {
//    $_POST['hidCustomerKey'] = $rs[0]['customerkey'];
//    $rsCustomer = $customer->getDataRowById($rs[0]['customerkey']);
//    $_POST['customerName'] = $rsCustomer[0]['name'];
//}

if (!empty($rs[0]['saleskey'])) {
    $_POST['hidSalesKey'] = $rs[0]['saleskey'];
    $rsEmployee = $employee->getDataRowById($rs[0]['saleskey']);
    $_POST['salesName'] = $rsEmployee[0]['name'];
}
if (!empty($rs[0]['categorykey'])) {
    $_POST['hidCategoryKey'] = $rs[0]['categorykey'];
    $rsTruckingServiceOrderCategory = $truckingServiceOrderCategory->getDataRowById($rs[0]['categorykey']);
    $_POST['categoryName'] = $rsTruckingServiceOrderCategory[0]['name'];
}

$stuffingLocationKey = $rs[0]['stuffinglocationkey'];
if (!empty($stuffingLocationKey)){ 
    $_POST['hidStuffingLocationKey'] = $stuffingLocationKey;    
    $rsLocation = $location->searchData($location->tableName.'.pkey',$stuffingLocationKey,true);
    $_POST['stuffingLocationName'] = $rsLocation[0]['name'] ;
}

//$consigneeLocationKey = $rs[0]['consigneelocationkey'];
//if (!empty($consigneeLocationKey)){ 
//    $_POST['hidConsgineeLocationKey'] = $consigneeLocationKey;    
//    $rsLocation = $location->searchData($location->tableName.'.pkey',$consgineeLocationKey,true);
//    $_POST['consigneeLocationName'] = $rsLocation[0]['name'] ;
//}
//
//if (!empty($rs[0]['consigneekey'])) {
//    $_POST['hidConsigneeKey'] = $rs[0]['consigneekey'];
//    $rsConsignee = $consignee->getDataRowById($rs[0]['consigneekey']);
//    $_POST['consigneeName'] = $rsConsignee[0]['name'];
//}
//
//$address = $rs[0]['consigneeaddress'];

 // $editWarehouseInactiveCriteria = ' or ' . $warehouse->tableName . '.pkey = ' . $obj->oDbCon->paramString($rs[0]['warehousekey']);


$arrStatus = $obj->convertForCombobox($obj->getAllStatus(), 'pkey', 'status');
$arrCargoType = $obj->convertForCombobox($obj->getCargoType(), 'pkey', 'name');
$arrWarehouse = $class->convertForCombobox($warehouse->searchData('', '', true, ' and (' . $warehouse->tableName . '.statuskey = 1' . $editWarehouseInactiveCriteria . ')'), 'pkey', 'name');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 
<title></title> 
 
<script type="text/javascript">  
   
    jQuery(document).ready(function() {
            var tabID = <?php echo ($isQuickAdd) ?  $_GET['tabID'] :  'selectedTab.newPanel[0].id';  ?>;

            var truckingQuotation = new TruckingQuotation(tabID, <?php echo json_encode(array(
                                                            'rsDetail' => $rsDetail
                                                        )); ?>);

            prepareHandler(truckingQuotation);

           
            var fieldValidation = {
                name: {
                    validators: {
                        notEmpty: {
                            message: phpErrorMsg.name[1]
                        },
                    }
                },
                customerName: {
                    validators: {
                        notEmpty: {
                            message: phpErrorMsg.customer[1]
                        },
                    }
                },
                categoryName: {
                    validators: {
                        notEmpty: {
                            message: phpErrorMsg.jobType[1]
                        },
                    }
                }, 
                stuffingLocationName: {
                    validators: {
                        notEmpty: {
                            message: phpErrorMsg.location[1]
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
  <form id="defaultForm" method="post" class="form-horizontal" action="<?php echo $formAction; ?>" >
        <?php prepareOnLoadDataForm($obj); ?>
     
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
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['name']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputText('name'); ?>
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
                                
<!--
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['customer']); ?></label> 
                                        <div class="col-xs-9"> 
                                              <?php
                                              echo $obj->inputAutoComplete(
                                                  array(  
                                                      'element' => array(
                                                          'value' => 'customerName',
                                                          'key' => 'hidCustomerKey'
                                                      ),
                                                      'source' => array(
                                                          'url' => 'ajax-customer.php',
                                                          'data' => array('action' => 'searchData')
                                                      )
                                                  )
                                              );
                                              ?>  
                                        </div> 
                                    </div> 
-->                                  <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['customer']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputText('customerName'); ?>  
                                        </div> 
                                    </div> 
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['PIC']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputText('recipientName'); ?>  
                                        </div> 
                                    </div> 
                                 
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['salesman']); ?></label> 
                                        <div class="col-xs-9"> 
                                              <?php
                                              echo $obj->inputAutoComplete(
                                                  array(
                                                      'objRefer' => $employee,
                                                      'revalidateField' => true,
                                                      'element' => array(
                                                          'value' => 'salesName',
                                                          'key' => 'hidSalesKey'
                                                      ),
                                                      'source' => array(
                                                          'url' => 'ajax-employee.php',
                                                          'data' => array('action' => 'searchData', 'issales' => 1)
                                                      ),
                                                      'allowedStatusForEdit' => array(1)
                                                  )
                                              );
                                              ?>  
                                        </div> 
                                    </div> 
                                
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['typeOfJob']); ?></label> 
                                        <div class="col-xs-3">  
                                            <?php echo $obj->inputSelect('hidCargoType', $arrCargoType, array('etc' => 'style="padding-right:0"', 'allowedStatusForEdit' => array(1))); ?> 
                                        </div> 
                                        <div class="col-xs-6" style="padding-left:0"> 
                                         <?php
                                         echo $obj->inputAutoComplete(
                                             array( 
                                                 'revalidateField' => true,
                                                 'element' => array(
                                                     'value' => 'categoryName',
                                                     'key' => 'hidCategoryKey'
                                                 ), 
                                                 
                                                'source' =>array(
                                                                    'url' => 'ajax-trucking-service-order-category.php',
                                                                    'data' => array(  'action' =>'searchData' )
                                                                ) ,
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
                                 
                             </div>
                    
                    

                    </div>
                     <div class="div-table-col">   
                        <div class="div-tab-panel"> 
                            <div class="div-table-caption border-green"><?php echo ucwords($obj->lang['location']); ?></div>
                             <div class="form-group">
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['route']); ?>
                                        </label>
                                        <div class="col-xs-9">
                                            <div class="flex">
                                                <div class="consume">
                                                    <?php echo $obj->inputText('routeFrom'); ?>
                                                </div>
                                                <div> - </div>
                                                <div class="consume">
                                                    <?php echo $obj->inputText('routeTo'); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label">
                                            <?php echo ucwords($obj->lang['location']); ?>
                                        </label>
                                        <div class="col-xs-9">
                                            <?php
                                            echo $obj->inputAutoComplete(
                                                array( 
                                                    'element' => array(
                                                        'value' => 'stuffingLocationName',
                                                        'key' => 'hidStuffingLocationKey'
                                                    ),
                                                    'source' => array(
                                                        'url' => 'ajax-location.php',
                                                        'data' => array('action' => 'searchData')
                                                    )
                                                )
                                            );
                                            ?>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label">
                                            <?php echo ucwords($obj->lang['address']); ?>
                                        </label>
                                        <div class="col-xs-9">
                                            <?php echo $obj->inputTextArea('stuffingAddress', array('etc' => 'style="height:10em;"', 'allowedStatusForEdit' => array(1))); ?>
                                        </div>
                                    </div>
                            
                         </div>  
                    </div>
           </div>
      </div>      
      

        <div class="div-table mnv-transaction transaction-detail" style="width:100%; border-bottom:1px solid #333; ">
                <div class="div-table-row">  
                    <div class="div-table-col" style="padding:0">
                        <div class="div-table" style="width:100%">
                        <div class="div-table-row"> 
                                    <div class="div-table-col detail-col-header"  style="width:30px; text-align:right;">#</div>
                                    <div class="div-table-col detail-col-header"  style="width:80px; text-align:right;"><?php echo ucwords($obj->lang['party']); ?></div>
                                    <div class="div-table-col detail-col-header"  ><?php echo ucwords($obj->lang['service']); ?></div>
                                    <div class="div-table-col detail-col-header" style="width:180px; text-align:right; "><?php echo ucwords($obj->lang['price']); ?></div>
                                    <div class="div-table-col detail-col-header" style="width:180px; text-align:right; "><?php echo ucwords($obj->lang['subtotal']); ?></div>
                                    <div class="div-table-col detail-col-header <?php echo $obj->hideOnDisabled(); ?> icon-col"  ></div> 
                            </div>
                        </div>    
                    </div>  
                </div>

            <?php 
            
                    $totalRows = count($rsDetail); 
                  
                    for ($i=0;$i<=$totalRows; $i++){  
                        
                        $class =  'transaction-detail-row';
                        $overwrite = true;
                        $disabled = false;  
                         
                        $etc = ''; 
                        
                        if ($i == $totalRows ){
                            $class = 'quotation-row-template row-template';
                            $overwrite = false;
                            $disabled = true;
                            $etc = 'disabled="disabled"';    
                        } else {   
                               
                            $qty = $rsDetail[$i]['qtyinbaseunit']; 
                            $_POST['serviceName[]'] = $rsDetail[$i]['servicename'];
                            $_POST['qty[]'] = $rsDetail[$i]['qtyinbaseunit']; 
                            $_POST['price[]'] = $obj->formatNumber($rsDetail[$i]['priceinunit']);
                            $_POST['subtotal[]'] = $obj->formatNumber($rsDetail[$i]['subtotal']);
                            $_POST['detailNotes[]'] =  $rsDetail[$i]['trdesc']; 

                        }  
                         
                         
                ?>

            <div class="div-table-row <?php echo $class; ?>">
                    <div class="div-table-col" style="padding: 0.3em 0">
                        <div class="div-table" style="width:100%">
                            <div class="div-table-row">
                                <div class="div-table-col detail-col-detail" style="width:30px; text-align:right">
                                    <div class="row-number"></div>
                                </div>
                                <div class="div-table-col detail-col-detail" style="width:80px;">
                                    <?php echo $obj->inputNumber('qty[]', array('overwritePost' => $overwrite, 'value' => 1, 'disabled' => $disabled, 'etc' => 'style="text-align:right; "')); ?>
                                </div>
                                <div class="div-table-col detail-col-detail" >
                                    <?php echo $obj->inputText('serviceName[]', array('overwritePost' => $overwrite, 'disabled' => $disabled,  'etc' => 'style="text-align:left;"' . $etc)); ?>
                                    <?php echo $obj->inputHidden('hidDetailKey[]', array('disabled' => $disabled, 'overwritePost' => $overwrite)); ?>
                                </div>
                                <div class="div-table-col detail-col-detail" style="width:180px;">
                                    <?php echo $obj->inputNumber('price[]', array('overwritePost' => $overwrite, 'disabled' => $disabled, 'etc' => 'style="text-align:right;"' . $etc)); ?>
                                </div>
                                <div class="div-table-col detail-col-detail" style="width:180px;">
                                    <?php echo $obj->inputNumber('subtotal[]', array('overwritePost' => $overwrite, 'readonly' => true, 'disabled' => $disabled, 'etc' => 'style="text-align:right;"' . $etc)); ?>
                                </div>
                                <div class="div-table-col detail-col-detail  icon-col <?php echo $obj->hideOnDisabled(); ?>"><?php echo $obj->inputLinkButton('btnDeleteRows', '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button', 'etc' => 'tabIndex="-1" style="padding:6px 0"')); ?></div>
                                </div>
                         </div>
                            <div class="div-table" style="width:100%">
                                <div class="div-table-row">
                                    <div class="div-table-col detail-col-detail" style="width:30px;"></div>
                                    <div class="div-table-col detail-col-detail"> 
                                    <?php echo $obj->inputTextArea('detailNotes[]', array('overwritePost' => $overwrite, 'disabled' => $disabled, 'etc' => 'style="height:10em; placeholder="' . $obj->lang['note'] . '"')); ?>
                                </div> 
                                <div class="div-table-col detail-col-detail  <?php echo $obj->hideOnDisabled(); ?> icon-col">
                                </div>
                            </div>
                        </div> 
            
                    </div>
                </div>
            
            <?php } ?>        


        </div>
      
        <div style="clear:both; height:1em;"></div> 
        <div style="float:left; display:inline-block;">
            <?php echo $obj->inputButton('btnAddRows', ucwords($obj->lang['addRows']), array('class' => 'btn btn-primary btn-second-tone')); ?>
        </div>

        <div>  
            <div style="float:right; ">
                <div class="div-table icon-col  <?php echo $obj->hideOnDisabled(); ?>" style="float:right;">&nbsp;</div>
                <div class="div-table" style="width:280px;float:right">
                    <div class="div-table-row  form-group">
                        <div class="div-table-col-3" style="text-align:right;">
                            <?php echo ucwords($obj->lang['total']); ?>
                        </div>
                        <div class="div-table-col-3" style="width:180px">
                            <?php echo $obj->inputNumber('total', array('readonly' => true, 'etc' => 'style="text-align:right;"')); ?>
                        </div> 
                    </div>       
                </div>
            </div>
        </div>


        <div class="form-button-margin"></div>
        <div class="form-button-panel" >  
         <?php echo $obj->generateSaveButton(array(), true); ?>  
        </div> 
        
    </form>  
   
     <?php echo $obj->showDataHistory(); ?>
    
</div> 
</body>

</html>
