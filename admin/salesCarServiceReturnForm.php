<?php
require_once '../_config.php'; 
require_once '../_include.php'; 


$obj= $salesCarServiceReturn;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true));

$formAction = 'salesCarServiceReturnList';

$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false;

$editWarehouseInactiveCriteria = '';
$editTermOfPaymentInactiveCriteria = '';
$editPaymentMethodInactiveCriteria = '';

$rsDetail = array();
$rsPaymentMethodDetail = array();

$rs = prepareOnLoadData($obj);

$_POST['trDate'] = date('d / m / Y');

if (!empty($_GET['id'])){
    $id = $_GET['id'];
	$rsDetail = $obj->getDetailById($id);
    $rsPaymentMethodDetail = $obj->getPaymentMethodDetail($id); 
    
	$_POST['trDate'] = $obj->formatDBDate($rs[0]['trdate'],'d / m / Y');
	$_POST['selWarehouseKey'] = $rs[0]['warehousekey'];
	$rsCustomer = $customer->getDataRowById($rs[0]['customerkey']);
	$_POST['customerName'] = $rsCustomer[0]['name'] ;
	$_POST['hidCustomerKey'] = $rsCustomer[0]['pkey'] ;
	$_POST['trDesc'] = $rs[0]['trdesc']; 
     
	$_POST['total'] = $obj->formatNumber($rs[0]['grandtotal']);
    $_POST['totalPayment'] =  $obj->formatNumber($rs[0]['totalpayment']);
    $_POST['balance'] =  $obj->formatNumber($rs[0]['balance']);
        
    if(!empty($rs[0]['refkey'])){
        $rsSales = $salesOrderCarService->getDataRowById($rs[0]['refkey']);
        $_POST['hidRefKey'] = $rsSales[0]['pkey'] ;
	    $_POST['refCode'] = $rsSales[0]['code'] ; 
    }
    
	$editWarehouseInactiveCriteria = ' or '.$warehouse->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['warehousekey']);

}

$rsTOP = $termOfPayment->searchData('','',true, ' and duedays <= 0 and ('.$termOfPayment->tableName.'.statuskey = 1' .$editTermOfPaymentInactiveCriteria.')', ' order by duedays asc');
$arrStatus = $obj->convertForCombobox($obj->getAllStatus(),'pkey','status');
$arrWarehouse = $class->convertForCombobox($warehouse->searchData('','',true,' and ('.$warehouse->tableName.'.statuskey = 1' .$editWarehouseInactiveCriteria.')'),'pkey','name');
$arrDefaultUnit = $obj->convertForCombobox($itemUnit->searchData('','',true, ' and ('.$itemUnit->tableName.'.statuskey = 1 )'),'pkey','name'); 
$arrPaymentMethod = $obj->convertForCombobox($paymentMethod->searchData ('','',true,' and ('.$paymentMethod->tableName.'.statuskey = 1 ' . $editPaymentMethodInactiveCriteria.')'),'pkey','name');    
$arrTOP = $obj->convertForCombobox($rsTOP,'pkey','name'); 

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title>

<script type="text/javascript">
    
    jQuery(document).ready(function(){  
        var tabID = selectedTab.newPanel[0].id;
         
        salesReturn = new SalesReturn(tabID);
        prepareHandler(salesReturn); 
        
        var fieldValidation =  {
                                    code: {
                                        validators: {
                                            notEmpty: {
                                                message: phpErrorMsg.code[1]
                                            },
                                        }
                                    }, 
                                    refCode: {
                                       validators: {
                                           notEmpty: {
                                               message:  phpErrorMsg.reference[1]
                                           },
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
                                        <?php echo $obj->inputDate('trDate'); ?> 
                                    </div> 
                                </div>    
                                <div class="form-group">
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['warehouse']); ?></label> 
                                    <div class="col-xs-9"> 
                                         <?php echo $obj->inputSelect('selWarehouseKey', $arrWarehouse ); ?>  
                                    </div> 
                                </div>    
                                <!--<div class="form-group">
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['reference']); ?></label> 
                                    <div class="col-xs-9"> 
                                        <?php echo $obj->inputText('refCode'); ?>
                                    </div> 
                                </div>--> 
                                <div class="form-group">
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['reference']); ?></label> 
                                    <div class="col-xs-9"> 
                                       <?php  echo $obj->inputAutoComplete(array( 
                                                                                'objRefer' => $salesOrderCarService,
                                                                                'revalidateField' => true,
                                                                                'element' => array('value' => 'refCode',
                                                                                                   'key' => 'hidRefKey'),
                                                                                'source' =>array(
                                                                                                    'url' => 'ajax-sales-order-car-service.php',
                                                                                                    'data' => array(  'action' =>'searchData','statuskey' => '(2,3)')
                                                                                                ) ,
                                                                                'callbackFunction' =>  'getTabObj().updateInformation()'
                                                                              )
                                                                        );  
                                            ?>
                                    </div> 
                                </div> 
                                <div class="form-group">
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['customer']); ?></label> 
                                    <div class="col-xs-9"> 
                                        <?php echo $obj->inputText('customerName', array('readonly' => true)); ?>
                                    </div> 
                                </div>       
                             </div>
         			</div> 
                    <div class="div-table-col">   
                        <div class="div-tab-panel"> 
                            <div class="div-table-caption border-blue"><?php echo ucwords($obj->lang['note']); ?></div>
                            <div class="form-group"> 
                                <div class="col-xs-12"> 
                                    <?php echo  $obj->inputTextArea('trDesc', array('etc' => 'style="height:10em;"')); ?>
                                </div> 
                            </div>   
                        </div>
                    </div>
                </div>
                    
         </div>


        <div class="div-table mnv-transaction transaction-detail sales-return-detail" style="width:100%; border-bottom:1px solid #333; ">
                <div class="div-table-row">
                    <div class="div-table-col detail-col-header" ><?php echo ucwords($obj->lang['itemName']); ?></div>
                    <div class="div-table-col detail-col-header" style="width:80px; text-align:right;"><?php echo ucwords($obj->lang['qty']); ?></div> 
                    <div class="div-table-col detail-col-header" style="width:80px; text-align:right;"><?php echo ucwords($obj->lang['unit']); ?></div> 
                    <div class="div-table-col detail-col-header" style="width:100px; text-align:right;"><?php echo ucwords($obj->lang['price']); ?></div> 
                    <div class="div-table-col detail-col-header" style="width:180px; text-align:right;"><?php echo ucwords($obj->lang['subtotal']); ?></div> 
                    <div class="div-table-col detail-col-header <?php echo $obj->hideOnDisabled(); ?>  icon-col"></div>
                </div>

				<?php 
                    $totalRows = count($rsDetail);
                     for ($i=0;$i<=$totalRows; $i++){  
                        
                        $class =  'transaction-detail-row';
                        $overwrite = true;
                        $etc = '';
                        
                        if ($i == $totalRows ){
                            $class = 'detail-row-template';
                            $overwrite = false;
                            $etc = 'disabled="disabled"'; 
                        }

                        $rsItem = $item->getDataRowById($rsDetail[$i]['itemkey']);
                        $_POST['hidSODetailKey[]'] = $rsDetail[$i]['refsodetailkey'];
                        $_POST['hidDetailKey[]'] =  $rsDetail[$i]['pkey'];
                        $_POST['hidItemKey[]'] =  $rsDetail[$i]['itemkey'];
                        $_POST['itemName[]'] =  $rsItem[0]['name'];
                        $_POST['qty[]'] =   $obj->formatNumber($rsDetail[$i]['qty']);
                        $_POST['selUnit[]'] =   $rsDetail[$i]['unitkey'];
                        $_POST['priceInUnit[]'] =   $obj->formatNumber($rsDetail[$i]['priceinunit']);
                        $_POST['detailSubtotal[]'] =   $obj->formatNumber($rsDetail[$i]['total']);
                        $_POST['description[]'] =  $rsDetail[$i]['description'];
                  ?>    
            
                 <div class="div-table-row <?php echo $class; ?>">
                    <div class="div-table-col detail-col-detail"><?php echo $obj->inputText('itemName[]',array('overwritePost' => $overwrite,'readonly' =>true, 'etc' =>  $etc)); ?><?php echo $obj->inputHidden('hidItemKey[]',array('overwritePost' => $overwrite, 'etc' => $etc)); ?><?php echo $obj->inputHidden('hidSODetailKey[]',array('overwritePost' => $overwrite, 'etc' => $etc)); ?><?php echo $obj->inputHidden('hidDetailKey[]',array('overwritePost' => $overwrite, 'etc' => $etc)); ?><?php echo $obj->inputHidden('isPackage[]',array('overwritePost' => $overwrite, 'etc' => $etc)); ?></div> 
                    <div class="div-table-col detail-col-detail" style="width:80px;"><?php echo $obj->inputNumber('qty[]', array('overwritePost' => $overwrite, 'etc' => 'style="text-align:right;" ' .$etc)); ?></div>
                    <div class="div-table-col detail-col-detail" style="width:80px;"><?php echo $obj->inputSelect('selUnit[]',$arrDefaultUnit, array('overwritePost' => $overwrite,'readonly' =>true, 'etc' => $etc)); ?></div>
                    <div class="div-table-col detail-col-detail" style="width:100px;"><?php echo $obj->inputNumber('priceInUnit[]', array('overwritePost' => $overwrite, 'readonly' =>true, 'etc' => 'style="text-align:right;"' .$etc)); ?></div>
                    <div class="div-table-col detail-col-detail" style="width:180px;"><?php echo $obj->inputNumber('detailSubtotal[]', array('overwritePost' => $overwrite, 'readonly' =>true, 'etc' => 'style="text-align:right;" ' .$etc)); ?></div> 
                    <div class="div-table-col detail-col-detail  <?php echo $obj->hideOnDisabled(); ?>"><?php echo $obj->inputLinkButton('btnDeleteRows' , '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button', 'etc' => 'tabIndex="-1"')); ?></div>
                </div>
               <?php } ?>
         </div>
      
         <div> 
                <div style="width:350px; float:right; ">
                        <div class="div-table" style="width:100%; margin-top:1em" >
                               <div class="div-table-row  form-group"> 
                                    <div class="div-table-col-3" style="text-align:right;">
                                        <?php echo ucwords($obj->lang['total']); ?> 
                                    </div>  
                                    <div class="div-table-col-3" style="width:180px;"> 
                                        <?php echo $obj->inputNumber('total', array ('readonly' => true, 'etc' => 'style="text-align:right;"')) ;?>   
                                    </div> 
                                </div>  
                                <div class="div-table-row  form-group"> 
                                    <div class="div-table-col-3" style="text-align:right;">
                                        <?php echo ucwords($obj->lang['payment']); ?> 
                                    </div>  
                                    <div class="div-table-col-3" style="width:180px;"> 
                                         <?php echo  $obj->inputSelect('selTermOfPaymentKey', $arrTOP); ?>
                                    </div> 
                                    <div class="div-table-col-3 <?php echo $obj->hideOnDisabled(); ?> icon-col"></div>
                                </div> 
                        </div>    
                    
                    <div class="mnv-total-group mnv-payment-method cashTOP "  >  
                        <div class="div-table" style="width: 100%">
                              <div class="div-table-row  form-group"> 
                                    <div class="div-table-col-3" style="text-align:right;"> 
                                           <?php echo $obj->lang['totalPayment']; ?>
                                    </div>  
                                    <div class="div-table-col-3"  style="width:180px"> 
                                            <?php echo $obj->inputCollapsibleNumber('totalPayment', array('readonly' => true, 'etc' => 'style="text-align:right;" ')); ?> 
                                    </div> 
                                    <div class="div-table-col-3  <?php echo $obj->hideOnDisabled(); ?> icon-col"></div> 
                              </div>
                        </div>

                        <div class="mnv-total-group-detail">
                            <div class="div-table  transaction-detail" style="width: 100%">
                                <?php 

                                    $totalRows = count($rsPaymentMethodDetail); 

                                    for($i=0;$i<=$totalRows;$i++) {
                                        $class =  'transaction-detail-row';
                                        $overwrite = true; 
                                        $disabled = false; 

                                        if ($i == $totalRows ){
                                            $class = 'payment-method-row-template row-template'; 
                                            $overwrite = false; 
                                            $disabled = true; 
                                        } else {   
                                            $_POST['hidDetailPaymentKey[]'] = $rsPaymentMethodDetail[$i]['pkey'];
                                            $_POST['selPaymentMethod[]'] = $rsPaymentMethodDetail[$i]['paymentkey'];
                                            $_POST['paymentMethodValue[]'] = $obj->formatNumber($rsPaymentMethodDetail[$i]['amount']); 
                                        }
                                ?> 

                                <div class="div-table-row form-group payment-detail-row <?php echo $class; ?>">
                                    <div class="div-table-col-3" style="text-align:right;">  
                                            <?php echo $obj->inputHidden('hidDetailPaymentKey[]',array('overwritePost' => $overwrite,  'disabled' => $disabled)); ?>
                                            <?php echo $obj->inputSelect('selPaymentMethod[]', $arrPaymentMethod, array('overwritePost' => $overwrite, 'disabled' => $disabled)); ?>
                                    </div>  
                                    <div class="div-table-col-3" style="width:180px"> 
                                           <?php echo $obj->inputNumber('paymentMethodValue[]', array('overwritePost' => $overwrite, 'disabled' => $disabled,'class'=>'form-control inputnumber mnv-detail-field', 'etc' => 'style="text-align:right;" ')); ?>
                                    </div>  
                                    <div class="div-table-col-3 <?php echo $obj->hideOnDisabled(); ?> icon-col">
                                        <?php echo $obj->inputLinkButton('btnDeleteRows', '<i class="fas fa-times"></i>',array('etc' => 'tabIndex="-1"', 'class' =>'btn btn-link remove-button' )); ?>
                                    </div>
                                </div> 

                                <?php } ?> 

                                <div class="div-table-row form-group ">
                                    <div class="div-table-col-3"></div>   
                                    <div class="div-table-col-3">
                                        <div class="text-link-01 mnv-total-group-hide-detail" style="float:right; text-align:right;" ><?php echo ucwords($obj->lang['hideDetail']); ?> </div> 
                                    </div>
                                    <div class="div-table-col-3 <?php echo $obj->hideOnDisabled(); ?>"></div>
                                </div>  
                                <div class="div-table-row form-group ">
                                    <div class="div-table-col-3 " style="height:1em"></div> <div class="div-table-col-3 "></div> <div class="div-table-col-3  <?php echo $obj->hideOnDisabled(); ?> "></div>
                                </div>  

                           </div>   
                        </div>
                    </div> 

                    <div class="div-table" style="width:100%; margin-top:1em"> 
                            <div class="div-table-row  form-group"> 
                                <div class="div-table-col-3" style="text-align:right;">
                                    <?php echo ucwords($obj->lang['balance']); ?> 
                                </div>  
                                <div class="div-table-col-3" style="width:180px;"> 
                                    <?php echo $obj->inputNumber('balance', array ( 'readonly' => true, 'etc' => 'style="text-align:right;"')) ;?>  
                                </div> 
                                <div class="div-table-col-3 <?php echo $obj->hideOnDisabled(); ?> icon-col"></div>
                            </div>  
                    </div>
                </div>     
                 <div style="clear:both"></div> 
          </div> 
      
        <div class="form-button-margin"></div>
        <div class="form-button-panel" > 
         <?php  echo $obj->generateSaveButton(array(),true);   ?>  
        </div>

    </form>
    <?php  echo $obj->showDataHistory(); ?>
</div>
</body>

</html>
