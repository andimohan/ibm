<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass('ARDiscountApproval.class.php');
$arDiscountApproval = createObjAndAddToCol( new ARDiscountApproval()); 
$arPayment = createObjAndAddToCol( new ARPayment()); 
$customer = createObjAndAddToCol( new Customer()); 
$warehouse = createObjAndAddToCol( new Warehouse()); 
$currency = createObjAndAddToCol( new Currency()); 
//$cashBank = createObjAndAddToCol( new CashBank()); 

$obj= $arDiscountApproval;
$ar = $obj->getARObj();
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true)); 

$formAction = 'arDiscountApprovalList';

$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false;

$editPaymentMethodInactiveCriteria = '';
$editWarehouseInactiveCriteria = '';
$editCurrencyInactiveCriteria = '';

$rsDetail = array();
$rsCost = array();

$_POST['trDate'] = date('d / m / Y');
$_POST['hidCurrentCurrencyKey'] = 1;  // default IDR
$_POST['chkIsDiscountOnly'] = true; 

$rs = prepareOnLoadData($obj);  

if (!empty($_GET['id'])){ 
	$id = $_GET['id'];	 
    
	$rsDetail = $obj->getDetailWithRelatedInformation($id); 
    $rsCost = $obj->getCostDetail($id);  
	  
	$_POST['trDate'] = $obj->formatDBDate($rs[0]['trdate'],'d / m / Y');
	$rsCustomer = $customer->getDataRowById($rs[0]['customerkey']);
	$_POST['customerName'] = $rsCustomer[0]['name'] ;
	$_POST['hidCustomerKey'] = $rsCustomer[0]['pkey'] ;  
	$_POST['trDesc'] = $rs[0]['trnotes'];
	$_POST['totalCost'] = $obj->formatNumber($rs[0]['totalcost']); 
	$_POST['totalDiscount'] = $obj->formatNumber($rs[0]['totaldiscount']); 
   	$_POST['selWarehouseKey'] = $rs[0]['warehousekey'];  
   	$_POST['hidARPaymentKey'] = $rs[0]['refkey'];  
	$rsARPayment = $arPayment->getDataRowById($rs[0]['refkey']);
   	$_POST['arPaymentCode'] = $rsARPayment[0]['code'];  
	 
    $_POST['selCurrency'] = $rs[0]['currencykey'];
    
 	$editCurrencyInactiveCriteria = ' or  '.$currency->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['currencykey']);  
 	$editWarehouseInactiveCriteria = ' or  '.$warehouse->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['warehousekey']);  
  
} 

$arrWarehouse = $class->convertForCombobox($warehouse->searchData('','',true,' and ('.$warehouse->tableName.'.statuskey = 1' .$editWarehouseInactiveCriteria.')'),'pkey','name');  
$arrStatus = $obj->convertForCombobox($obj->getAllStatus(),'pkey','status');      
    
$arrCurrency = $obj->convertForCombobox($currency->searchData('','',true,' and ('.$currency->tableName.'.statuskey = 1' . $editCurrencyInactiveCriteria.')'),'pkey','name');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title> 
 
<script type="text/javascript">  
  
	jQuery(document).ready(function(){  
	 	 
        var tabID = <?php echo ($isQuickAdd) ?  $_GET['tabID'] :  'selectedTab.newPanel[0].id';  ?>  
        
         var arDiscountApproval = new ARDiscountApproval(tabID);
    
         prepareHandler(arDiscountApproval);

          var fieldValidation =  {code: {
                                        validators: {
                                                notEmpty: {  message: phpErrorMsg.code[1] }, 
                                        }
                                    },
                                  customerName: { 
                                        validators: {
                                            notEmpty: {
                                                message:  phpErrorMsg.customer[1]
                                            }, 
                                        }
                                    }    
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
    <?php echo $obj->inputHidden('hidCustomerKey'); ?>
    
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
                                            <?php echo $obj->inputDate('trDate', array('readonly' => true )); ?>  
                                        </div> 
                                    </div>    
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['warehouse']); ?></label> 
                                        <div class="col-xs-9"> 
                                           <?php echo  $obj->inputSelect('selWarehouseKey', $arrWarehouse, array('readonly' => true )); ?>
                                        </div> 
                                    </div>    
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['paymentCode']); ?></label> 
                                        <div class="col-xs-9"> 
                                              <?php  echo $obj->inputAutoComplete(array( 
                                                                                'objRefer' => $arPayment,
                                                                                'readonly' => true,
                                                                                'revalidateField' => true,
                                                                                'element' => array('value' => 'arPaymentCode',
                                                                                                   'key' => 'hidARPaymentKey'),
                                                                                'source' =>array(
                                                                                                    'url' => 'ajax-ar-payment.php',
                                                                                                    'data' => array(  'action' =>'searchData','statuskey'=>(1) )
                                                                                                ) 
                                                                              )
                                                                        );  
                                            ?>
                                        </div> 
                                    </div>
             			            <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['customer']); ?> </label> 
                                        <div class="col-xs-9"> 
                                               <?php  echo $obj->inputText('customerName', array('readonly' => true )); ?>
                                        </div>
                                    </div>
								   <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['currency']); ?> </label> 
                                        <div class="col-xs-9  mnv-currency"> 
                                               <?php  echo $obj->inputSelect('selCurrency', $arrCurrency, array('class' => 'form-control input-currency','readonly' => true)); ?>
                                        </div>
                                    </div>
								   	<div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['totalDiscount']); ?> </label> 
                                        <div class="col-xs-9"> 
                                               <?php  echo $obj->inputAutoDecimal('totalDiscount', array('readonly' => true )); ?>
                                        </div>
                                    </div>
								   	<div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['totalCost']); ?> </label> 
                                        <div class="col-xs-9"> 
                                               <?php  echo $obj->inputAutoDecimal('totalCost', array('readonly' => true )); ?>
                                        </div>
                                    </div> 
                                </div>
                    </div>
                    <div class="div-table-col"> 
                           <div class="div-tab-panel"> 
                              <div class="div-table-caption border-orange"><?php echo ucwords($obj->lang['note']); ?></div> 
                               <?php echo  $obj->inputTextArea('trDesc', array('etc' => 'style="height:10em;"','readonly' => true )); ?> 
                            </div>   
                    </div>
                </div>    
        </div>    
                        
        <div class="div-table mnv-arpayment transaction-detail mnv-checkbox-group" style="width:100%; border-bottom:1px solid #333; ">
                <div class="div-table-row"> 
					<div class="div-table-col detail-col-header"><?php echo ucwords($obj->lang['arCode']); ?></div>
					<div class="div-table-col detail-col-header" style="width:150px;"><?php echo ucwords($obj->lang['reference']); ?></div>
					<?php 	if((in_array(PLAN_TYPE['categorykey'], array(COMPANY_TYPE['trucking'],COMPANY_TYPE['forwarding'])))) { ?>
						<div class="div-table-col detail-col-header" style="width:150px;"><?php echo ucwords($obj->lang['si']); ?></div>
					<?php } ?> 
					<div class="div-table-col detail-col-header" style="width:120px; text-align:right;"><?php echo ucwords($obj->lang['amount']); ?></div>
					<div class="div-table-col detail-col-header" style="width:120px; text-align:right;"><?php echo ucwords($obj->lang['outstanding']); ?></div>
					<div class="div-table-col detail-col-header" style="width:120px; text-align:right;"><?php echo ucwords($obj->lang['discount']); ?></div>
					<!--<div class="div-table-col detail-col-header" style="width:120px; text-align:right;"><?php echo ucwords($obj->lang['approved']); ?></div>-->     
                </div>
                
				<?php
                  	  
                    $totalRows = count($rsDetail);
                    for ($i=0;$i<=$totalRows; $i++){  
					    $class =  'transaction-detail-row';
                        $overwrite = true;
                        $disabled = false;  
                        
                        $_POST['refCode[]']  = '';
                        $_POST['doNumber[]']  = '';
                        
                        if ($i == $totalRows ){
                            $class = 'detail-row-template';
                            $overwrite = false;
                            $disabled = true; 
                        } else {  
						    $rsAR = $ar->getDataRowById($rsDetail[$i]['arkey']);  
                            $_POST['hidDetailKey[]'] =  $rsDetail[$i]['pkey'];
                            $_POST['hidARKey[]'] =  $rsDetail[$i]['arkey']; 
                            $_POST['hidARPaymentDetailKey[]'] =  $rsDetail[$i]['refdetailkey']; 
                            $_POST['arCode[]'] =  $rsAR[0]['code'];
                            $_POST['refCode[]'] =  $rsAR[0]['refcode'];
                            $_POST['refCode2[]'] =  $rsAR[0]['refcode2'];
                            //$doNumber = $ar->getDoNumber($rsAR[0]['refheaderkey']);
                      	    $_POST['arAmount[]'] =  $obj->formatNumber($rsAR[0]['amount']);
                            $_POST['doNumber[]'] =  $rsAR[0]['refcode2'];
		   	                $_POST['outstanding[]'] =   $obj->formatNumber($rsDetail[$i]['outstanding']); 
                           // $_POST['amount[]'] =   $obj->formatNumber($rsDetail[$i]['amount']); 
                            $_POST['discount[]'] =   $obj->formatNumber($rsDetail[$i]['discount']); 
                            //$_POST['taxPPH[]'] =   $obj->formatNumber($rsDetail[$i]['taxamount']); 
                            //$_POST['chkPick[]'] =  1;
                             
                        }
                       
                 ?>        
                 
              <div class="div-table-row <?php echo $class; ?>">
					<div class="div-table-col detail-col-detail">
						<?php echo $obj->inputHidden('hidDetailKey[]',array('disabled' => $disabled, 'overwritePost' => $overwrite)); ?>
						<?php echo $obj->inputText('arCode[]',array('readonly' => true,'disabled' => $disabled, 'overwritePost' => $overwrite)); ?>
						<?php echo $obj->inputHidden('hidARKey[]',array('overwritePost' => $overwrite,'disabled' => $disabled)); ?>
						<?php echo $obj->inputHidden('hidARPaymentDetailKey[]',array('overwritePost' => $overwrite,'disabled' => $disabled)); ?>
					</div> 
					<div class="div-table-col detail-col-detail">
						<?php echo $obj->inputText('refCode[]',array('readonly' => true,'disabled' => $disabled, 'overwritePost' => $overwrite)); ?>
					</div>
					<?php 	if((in_array(PLAN_TYPE['categorykey'], array(COMPANY_TYPE['trucking'],COMPANY_TYPE['forwarding'])))) { ?> 
						<div class="div-table-col detail-col-detail">
							<?php echo $obj->inputText('doNumber[]',array('readonly' => true,'disabled' => $disabled, 'overwritePost' => $overwrite)); ?>
						</div>
					<?php } ?> 
					<div class="div-table-col detail-col-detail"><?php echo $obj->inputNumber('arAmount[]',array('overwritePost' => $overwrite, 'readonly' => true,'disabled' => $disabled,  'etc' => 'style="text-align:right"' )); ?></div> 
					<div class="div-table-col detail-col-detail" ><?php echo $obj->inputNumber('outstanding[]',array('overwritePost' => $overwrite,'readonly' => true, 'disabled' => $disabled,  'etc' => 'style="text-align:right"' )); ?></div> 
					<div class="div-table-col detail-col-detail" ><?php echo $obj->inputNumber('discount[]',array('overwritePost' => $overwrite,'readonly' => true, 'disabled' => $disabled, 'etc' => 'style="text-align:right";')); ?></div> 
					<!--<div class="div-table-col detail-col-detail" style="width:100px;"><?php echo $obj->inputNumber('amount[]',array('overwritePost' => $overwrite, 'disabled' => $disabled, 'etc' => 'style="text-align:right";')); ?></div>-->
                </div> 
                
                <?php  } ?>   
                   
         </div>        
                   
          <div style="clear:both; height:3em;"></div> 
          <!--<div style="float:left; display:inline-block;"><?php echo $obj->inputButton('btnAddRows', $obj->lang['addRows'],array('class' =>'btn btn-primary btn-second-tone')); ?></div>-->
	  
        <div class="div-table mnv-cost transaction-detail" style="width:100%; border-bottom:1px solid #333; ">
                <div class="div-table-row">  
                    <div class="div-table-col detail-col-header" style=""><?php echo ucwords($obj->lang['costName']); ?></div>
                    <div class="div-table-col detail-col-header" style="width:120px;text-align:right;"><?php echo ucwords($obj->lang['amount']); ?></div> 
                    <!--<div class="div-table-col detail-col-header" style="width:120px;text-align:right;"><?php echo ucwords($obj->lang['approved']); ?></div>--> 
                </div>
      
            
				<?php  
                    $totalRows = count($rsCost);
                    $overwrite = true;

                    for ($i=0;$i<=$totalRows; $i++){  
					 
                        $class =  'transaction-detail-row';
                        $readOnly = false;
                        $disable = '';  
                        $etc = '';  
                        
						if ($i == $totalRows ){
                            $class = 'cost-row-template row-template';
                            $overwrite = false;
                            $disable = 'disabled="disabled"';
                        } else {
							$obj->setLog($rsCost[$i]);
                            $_POST['hidCostDetailKey[]'] =  $rsCost[$i]['pkey']; 
                            $_POST['hidCostKey[]'] =  $rsCost[$i]['costkey']; 
                            $_POST['costName[]'] =  $rsCost[$i]['costname']; 
                            $_POST['costAmount[]'] =  $obj->formatNumber($rsCost[$i]['amount']);
                            //$_POST['costAmountConfirmed[]'] =  $obj->formatNumber($rsCost[$i]['amount']);
							$_POST['hidCostARDetailKey[]'] =  $rsCost[$i]['refdetailkey'];
                        }
				 
                ?>
                 
                
                <div class="div-table-row <?php echo $class; ?>"> 
                    <div class="div-table-col detail-col-detail">
                        <?php echo $obj->inputHidden('hidCostDetailKey[]',array('overwritePost' => $overwrite, 'disabled' =>  $disable)); ?>
                        <?php echo $obj->inputText('costName[]',array('readonly' => true,'overwritePost' => $overwrite, 'disabled' =>  $disable)); ?>
                        <?php echo $obj->inputHidden('hidCostKey[]',array('overwritePost' => $overwrite, 'disabled' =>  $disable)); ?>
						<?php echo $obj->inputHidden('hidCostARDetailKey[]',array('overwritePost' => $overwrite, 'disabled' =>  $disable)); ?>
                    </div> 

					<div class="div-table-col detail-col-detail">
                        <?php echo $obj->inputNumber('costAmount[]',array('overwritePost' => $overwrite, 'readonly' => true, 'disabled' =>  $disable,  'etc' => 'style="text-align:right;" ' .$etc)); ?>
                    </div>
					<!--<div class="div-table-col detail-col-detail">
                        <?php echo $obj->inputNumber('costAmountConfirmed[]',array('overwritePost' => $overwrite, 'disabled' =>  $disable,  'etc' => 'style="text-align:right;" ' .$etc)); ?>
                    </div>-->
                    
                </div> 
 
            <?php } ?> 
                   
         </div>
	  	
	  	<div style="clear:both; height:1em;"></div>
      
        <div class="form-button-margin"></div>
        <div class="form-button-panel" > 
         <?php  echo $obj->generateSaveButton(array(),true);?>
        </div> 
        
    </form>   
   
     <?php  echo $obj->showDataHistory(); ?>
</div> 
</body>

</html>
