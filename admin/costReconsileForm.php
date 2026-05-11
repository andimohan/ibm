<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass('CostReconsile.class.php');
$costReconsile = createObjAndAddToCol( new CostReconsile()); 
$warehouse = createObjAndAddToCol( new Warehouse()); 
$currency = createObjAndAddToCol( new Currency()); 
$paymentMethod = createObjAndAddToCol( new PaymentMethod()); 
$emklOrderInvoice = createObjAndAddToCol( new EMKLOrderInvoice()); 
$prepaidExpense = createObjAndAddToCol( new PrepaidExpense()); 
$service = createObjAndAddToCol( new Service(SERVICE)); 
$customer = createObjAndAddToCol( new Customer()); 

$obj= $costReconsile; 
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true)); 

$formAction = 'costReconsileList';

$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false;

$editPaymentMethodInactiveCriteria = '';
$editWarehouseInactiveCriteria = '';
$editCurrencyInactiveCriteria = '';

$rsARPaymentDetail = array();
$rsARPaymentMethodDetail = array();
$rsARDP = array();
$rsARCost = array();
$arrAvailableVoucher = array();
$rsDetailItemInvoice = array();
$rsCostReconsileDetail = array();

$_POST['trDate'] = date('d / m / Y');
$_POST['trStartDate'] = date('d / m / Y');
$_POST['trEndDate'] = date('d / m / Y'); 
$_POST['hidCurrentCurrencyKey'] = 1;  // default IDR

$rs = prepareOnLoadData($obj);  

//$decimalPrice = (empty($rs[0]['currencykey']) || $rs[0]['currencykey'] == CURRENCY['idr'] ) ? 0 : 2;   

if (!empty($_GET['id'])){ 
	$id = $_GET['id'];	 
    
	$rsCostReconsileDetail = $obj->getDetailById($id); 
 	$rsInvoice = $emklOrderInvoice->getDataRowById($rs[0]['refkey']);
    $rsDetailItemInvoice = $emklOrderInvoice->getItemDetail($rs[0]['refkey'],'refheaderkey');
    
    $rsCustomer = $customer->getDataRowById($rsInvoice[0]['customerkey'] );

	$_POST['invoiceCode'] = $rsInvoice[0]['code'] ;
	$_POST['hidInvoiceKey'] = $rsInvoice[0]['pkey'] ;   
    $_POST['hidCurrentInvoiceCode'] = $rsInvoice[0]['code'] ;
	$_POST['hidCurrentInvoiceKey'] = $rsInvoice[0]['pkey'] ; 
	$_POST['customerName'] = $rsCustomer[0]['name'];   
        
 	$editCurrencyInactiveCriteria = ' or  '.$currency->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['currencykey']);  
 	$editWarehouseInactiveCriteria = ' or  '.$warehouse->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['warehousekey']);  
//	$editPaymentMethodInactiveCriteria = ' or '.$paymentMethod->tableName.'.pkey in (select paymentkey from '.$obj->tablePayment.' where refkey = '. $obj->oDbCon->paramString($rs[0]['pkey']).')';
  
} 



$arrWarehouse = $class->convertForCombobox($warehouse->searchData('','',true,' and ('.$warehouse->tableName.'.statuskey = 1' .$editWarehouseInactiveCriteria.')'),'pkey','name');  
$arrStatus = $obj->convertForCombobox($obj->getAllStatus(),'pkey','status');    

$rsPaymentMethod = (empty($rs[0]['nettingkey'])) ? $paymentMethod->searchData ('','',true,' and ('.$paymentMethod->tableName.'.statuskey = 1' . $editPaymentMethodInactiveCriteria.')') : NETTING_PAYMENT;
$arrPaymentMethod = $obj->convertForCombobox($rsPaymentMethod,'pkey','name');    
    
$arrCurrency = $obj->convertForCombobox($currency->searchData('','',true,' and ('.$currency->tableName.'.statuskey = 1' . $editCurrencyInactiveCriteria.')'),'pkey','name');

$rsService = $service->searchData();
$rsService = array_column($rsService,'name','pkey');


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title> 
 
<script type="text/javascript">  
  
	jQuery(document).ready(function(){  
	 	 
        var tabID = <?php echo ($isQuickAdd) ?  $_GET['tabID'] :  'selectedTab.newPanel[0].id';  ?>  
        var tablekey = <?php echo $obj->getTableKeyAndObj($obj->tableName ,array('key'))['key']; ?>;   
        
         var varConstant = {  
                        TABLEKEY : tablekey,
                        CURRENCY : <?php echo json_encode(CURRENCY); ?>,
                        };
        
         var costReconsile = new CostReconsile(tabID,<?php echo json_encode($rs); ?>,varConstant);
    
         prepareHandler(costReconsile);

          var fieldValidation =  {
                                    
                                    code: {
                                        validators: {
                                                notEmpty: {  message: phpErrorMsg.code[1] }, 
                                        }
                                    },
                                    invoiceCode: {
                                        validators: {
                                                notEmpty: {  message: phpErrorMsg.invoice[1] }, 
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
    <?php echo $obj->inputHidden('hidCurrentInvoiceKey'); ?>
    <?php echo $obj->inputHidden('hidCurrentInvoiceCode'); ?>
    <?php echo $obj->inputHidden('hidCurrentCurrencyKey'); ?>
    
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
                                            <?php echo $obj->inputDate('trDate',array('etc' => 'max-days=14')); ?>  
                                        </div> 
                                    </div>    
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['warehouse']); ?></label> 
                                        <div class="col-xs-9"> 
                                           <?php echo  $obj->inputSelect('selWarehouseKey', $arrWarehouse); ?>
                                        </div> 
                                    </div> 
                                   <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['invoiceNumber']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php  echo $obj->inputAutoComplete(array( 
                                                                                'objRefer' => $emklOrderInvoice,
                                                                                'revalidateField' => true,
                                                                                'element' => array('value' => 'invoiceCode',
                                                                                                   'key' => 'hidInvoiceKey'),
                                                                                'source' =>array(
                                                                                                    'url' => 'ajax-emkl-order-invoice.php',
                                                                                                    'data' => array(  'action' =>'searchData' , 'statuskey' => '(2,3)' )
                                                                                                ) ,
                                                                                'callbackFunction' => 'getTabObj().updateInvoiceInformation(event, ui);'

                                                                              )
                                                                        );  
                                            ?>
                                        </div> 
                                    </div>  
             			            <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['customer']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo  $obj->inputText('customerName', array('readonly' => true)); ?> 
                                        </div> 
                                    </div>  
             			            <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['currency']); ?> / <?php echo ucwords($obj->lang['currencyRate']); ?></label> 
                                        <div class="col-xs-9  mnv-currency"> 
                                           <div class="flex">
                                               <div><?php  echo $obj->inputSelect('selCurrency', $arrCurrency, array('class' => 'form-control input-currency')); ?></div>
                                               <div style="display:none" class="consume"><?php echo $obj->inputDecimal('currencyRate', array('class'=>'form-control inputnumber input-currency-rate')); ?></div>
                                           </div>
                                        </div> 
                                    </div>
<!--
                                   <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['period']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <div class="flex">
                                                <div><?php echo $obj->inputCheckBox('chkDatePeriod'); ?></div>  
                                                <div class="consume"><?php echo $obj->inputDate('trStartDate',array( 'etc' => 'style="text-align:center"')); ?></div>  
                                                <div class="consume"><?php echo $obj->inputDate('trEndDate',array(  'etc' => 'style="text-align:center"')); ?></div>  
                                            </div> 
                                        </div> 
                                   </div>
-->
             			            <!--
                            <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['margin']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo  $obj->inputNumber('profitLoss', array('readonly' => true)); ?> 
                                            <div class="asterix-label" style="font-size:0.9em; margin-top:0.5em">Margin dihitung dari DPP, dan hanya berfungsi sebagai nilai perbandingan.</div>
                                        </div> 
                                    </div>
                                    -->
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['note']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo  $obj->inputTextArea('trDesc', array('etc' => 'style="height:10em;"')); ?> 
                                        </div> 
                                    </div>   
                                   <div class="form-group">
                                        <div class="col-xs-3"></div>
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputButton('btnImport', $obj->lang['showAll'],array('class' =>'btn btn-primary btn-second-tone')); ?>
                                        </div> 
                                    </div>    
                                </div>
                    </div>
                    <div class="div-table-col"> 
                           <div class="div-tab-panel"> 
                              <div class="div-table-caption border-green"><?php echo ucwords($obj->lang['invoiceDetail']); ?></div> 
                                <div class="div-table transaction-detail" style="width:100%">
                                        <div class="div-table-row"> 
                                             <div class="div-table-col-5" style="border-top:1px solid #666;border-bottom:1px solid #666; width:60px;text-align:right;" > 
                                                <strong><?php echo ucwords($obj->lang['qty']); ?></strong>
                                             </div>
                                            <div class="div-table-col-5" style="border-top:1px solid #666;border-bottom:1px solid #666;text-align:left" > 
                                                <strong><?php echo ucwords($obj->lang['service']); ?></strong>
                                             </div>
                                            <div class="div-table-col-5" style="border-top:1px solid #666;border-bottom:1px solid #666;  width:60px;text-align:center" > 
                                                <strong><?php echo ucwords($obj->lang['curr']); ?></strong>
                                             </div>
  											<div class="div-table-col-5" style="border-top:1px solid #666;border-bottom:1px solid #666;  width:80px;text-align:right" > 
                                                <strong><?php echo ucwords($obj->lang['total']); ?></strong>
                                             </div>

                                        </div> 
                                       		<?php
                  	                                $totalInvoice = 0;
                  	  
                                                    $totalRows = count($rsDetailItemInvoice);
                                                    for ($j=0;$j<=$totalRows; $j++){   
                                                        
                                                        $roundDecimal = ($rsDetailItemInvoice[0]['headercurrencykey'] == 1 ) ? 0 : 2;
                                                        
                                                        $class =  'transaction-detail-row invoice-row';
                                                        $overwrite = true;
                                                        $disabled = false;  
                                                        $display = '';
                                                        $qty = '';    
                                                        $services = '';    
                                                        $amount = '';    
                                                        $currencyName = '';

                                                        if ($j == $totalRows ){
                                                            $class = 'invoice-row-template';
                                                            $overwrite = false;
                                                            $disabled = true; 
                                                            $display = 'style="display:none"';
                                                        } else {  
                                                            $qty = $obj->formatNumber($rsDetailItemInvoice[$j]['qtyinbaseunit'],2);
                                                            $services = $rsDetailItemInvoice[$j]['itemname'];
                                                            $currencyName = $rsDetailItemInvoice[$j]['headercurrencyname'];
                                                            $amount = $obj->formatNumber($rsDetailItemInvoice[$j]['total'],$roundDecimal);
                                                            $totalInvoice += $rsDetailItemInvoice[$j]['total'];
                                                        }

                                                 ?> 
                                        <div class="div-table-row  <?php echo $class; ?>"  <?php echo $display; ?>> 
                                                <div class="div-table-col-5 detail-col-detail qty" style="border-bottom:1px solid #dedede; text-align:right;vertical-align:top" > <?php echo $qty; ?></div> 
                                                <div class="div-table-col-5 detail-col-detail services" style="border-bottom:1px solid #dedede; text-align:left; vertical-align:top" ><?php echo $services; ?></div>
                                                <div class="div-table-col-5 detail-col-detail currency" style="border-bottom:1px solid #dedede; text-align:center; vertical-align:top" ><?php echo $currencyName; ?></div>
                                                <div class="div-table-col-5 detail-col-detail amount" style="border-bottom:1px solid #dedede; text-align:right;vertical-align:top" ><?php echo $amount; ?> </div>             
                                        </div> 
                                        <?php  } ?>   
    
                                   </div>    
                                        <div class="div-table transaction-detail total-table" style="margin-top:0.5em; width:100%; font-weight:bold; <?php echo $displayTotal; ?>">
                                             <div class="div-table-row" > 
                                                <div class="div-table-col-3 "  style=" width:60px;"> </div>
                                                <div class="div-table-col-3 " > </div> 
                                                <div class="div-table-col-3" style=" width:100px; text-align:right" ><?php echo ucwords($obj->lang['total']); ?> <?php echo ucwords($obj->lang['invoice']); ?></div>
                                                <div class="div-table-col-3 total" style=" width:80px;text-align:right;font-weight:bold;"><?php echo $obj->formatNumber($totalInvoice,$roundDecimal); ?></div> 
                                            </div> 
                                             <!--
                                             <div class="div-table-row" > 
                                                <div class="div-table-col-3 "> </div>
                                                <div class="div-table-col-3 " > </div> 
                                                <div class="div-table-col-3" style=" width:100px; text-align:right" ><?php echo ucwords($obj->lang['tax']); ?></div>
                                                <div class="div-table-col-3 tax" style=" width:80px;text-align:right;font-weight:bold;"><?php echo $obj->formatNumber($rsInvoice[0]['taxvalue'],$roundDecimal); ?></div> 
                                            </div> 
                                             <div class="div-table-row" > 
                                                <div class="div-table-col-3 "  style=" width:60px;"> </div>
                                                <div class="div-table-col-3 " > </div> 
                                                <div class="div-table-col-3" style=" width:100px; text-align:right" ><?php echo ucwords($obj->lang['total']); ?> <?php echo ucwords($obj->lang['invoice']); ?> </div>
                                                <div class="div-table-col-3 grandtotal" style=" width:80px;text-align:right;font-weight:bold;"><?php echo $obj->formatNumber($rsInvoice[0]['grandtotal'],$roundDecimal); ?></div> 
                                            </div> 
                                            -->
                                   </div>
                            </div>   
                    </div>
                </div>    
        </div>    
                        
        <div class="div-table mnv-transaction transaction-detail mnv-checkbox-group" style="width:100%; border-bottom:1px solid #333; ">
                <div class="div-table-row"> 

                            <div class="div-table-col detail-col-header" style="width:120px;"><?php echo ucwords($obj->lang['code']); ?></div>
                            <div class="div-table-col detail-col-header" style="width:120px;"><?php echo ucwords($obj->lang['reference']); ?></div>
                            <div class="div-table-col detail-col-header" ><?php echo ucwords($obj->lang['service']); ?></div>
                            <div class="div-table-col detail-col-header" style="width:100px; text-align:right;"><?php echo ucwords($obj->lang['amount']); ?></div>
                            <div class="div-table-col detail-col-header" style="width:100px; text-align:right;"><?php echo ucwords($obj->lang['outstanding']); ?></div>
                            <div class="div-table-col detail-col-header" style="width:120px; text-align:right;"><?php echo ucwords($obj->lang['reconsiliation']); ?></div>
                            <div class="div-table-col detail-col-header  <?php echo $obj->hideOnDisabled(); ?> icon-col" style="text-align:center; width: 35px;"><?php echo $obj->inputCheckBox('chkPick-master', array('etc' => 'style="margin-top:0"')); ?></div>
                            <div class="div-table-col detail-col-header  <?php echo $obj->hideOnDisabled(); ?> icon-col"></div>
                </div>
                
				<?php
                  	  
                    $totalRows = count($rsCostReconsileDetail);
                    for ($i=0;$i<=$totalRows; $i++){  
					    $class =  'transaction-detail-row';
                        $overwrite = true;
                        $disabled = false;  
                        
                        $_POST['refCode[]']  = '';
                        
                        if ($i == $totalRows ){
                            $class = 'detail-row-template';
                            $overwrite = false;
                            $disabled = true; 
                        } else {  
						    $rsCostReconsile = $prepaidExpense->getDataRowById($rsCostReconsileDetail[$i]['refreconsilekey']);  
                            $_POST['hidDetailKey[]'] =  $rsCostReconsileDetail[$i]['pkey'];
                            $_POST['hidReconsileKey[]'] =  $rsCostReconsileDetail[$i]['refreconsilekey']; 
                            $_POST['reconsileCode[]'] =  $rsCostReconsile[0]['code'];
                            $_POST['refCode[]'] =  $rsCostReconsile[0]['refcode'];
                            $_POST['hidServiceKey[]'] =  $rsCostReconsile[0]['costkey']; 
                            $_POST['serviceName[]'] =  $rsService[$rsCostReconsile[0]['costkey']];
                      	    $_POST['reconsileAmount[]'] =  $obj->formatNumber($rsCostReconsile[0]['amount']);
		   	                $_POST['outstanding[]'] =   $obj->formatNumber($rsCostReconsileDetail[$i]['outstanding']); 
                            $_POST['amount[]'] =   $obj->formatNumber($rsCostReconsileDetail[$i]['amount']); 
                            $_POST['chkPick[]'] =  1;
                             
                        }
                       
                 ?>        
                 
              <div class="div-table-row <?php echo $class; ?>">

                                    <div class="div-table-col detail-col-detail" >
                                        <?php echo $obj->inputHidden('hidDetailKey[]',array('disabled' => $disabled, 'overwritePost' => $overwrite)); ?>
                                        <?php echo $obj->inputText('reconsileCode[]',array('disabled' => $disabled, 'overwritePost' => $overwrite)); ?>
                                        <?php echo $obj->inputHidden('hidReconsileKey[]',array('overwritePost' => $overwrite,'disabled' => $disabled)); ?>
                                    </div> 
                                    <div class="div-table-col detail-col-detail" ><?php echo $obj->inputText('refCode[]',array('overwritePost' => $overwrite, 'readonly' => true,'disabled' => $disabled )); ?></div> 
                                    <div class="div-table-col detail-col-detail" >
                                        <?php echo $obj->inputHidden('hidServiceKey[]',array('overwritePost' => $overwrite,'readonly' => true, 'disabled' =>  $disabled)); ?>
                                        <?php echo $obj->inputText('serviceName[]',array('overwritePost' => $overwrite,'readonly' => true, 'disabled' =>  $disabled)); ?>
                                    </div> 
                                    <div class="div-table-col detail-col-detail" ><?php echo $obj->inputNumber('reconsileAmount[]',array('overwritePost' => $overwrite, 'readonly' => true,'disabled' => $disabled,  'etc' => 'style="text-align:right"' )); ?></div> 
                                    <div class="div-table-col detail-col-detail" ><?php echo $obj->inputNumber('outstanding[]',array('overwritePost' => $overwrite,'readonly' => true, 'disabled' => $disabled,  'etc' => 'style="text-align:right"' )); ?></div> 
                                    <div class="div-table-col detail-col-detail" ><?php echo $obj->inputNumber('amount[]',array('overwritePost' => $overwrite, 'disabled' => $disabled, 'etc' => 'style="text-align:right";')); ?></div> 
                                    <div class="div-table-col detail-col-detail  <?php echo $obj->hideOnDisabled(); ?> icon-col" ><?php echo $obj->inputCheckBox('chkPick[]',  array('value'=> 1, 'disabled' => $disabled) ); ?></div>
                                    <div class="div-table-col detail-col-detail  <?php echo $obj->hideOnDisabled(); ?> icon-col"><?php echo $obj->inputLinkButton('btnDeleteRows' , '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button')); ?> </div>

                </div> 
                
                <?php  } ?>   
                   
         </div>        
                   
          <div style="clear:both; height:1em;"></div> 
          <div style="float:left; display:inline-block;"><?php echo $obj->inputButton('btnAddRows', $obj->lang['addRows'],array('class' =>'btn btn-primary btn-second-tone')); ?></div>
              
         <div>   
            <div class="<?php echo $obj->hideOnDisabled(); ?>" style="float:right; width:65px; height: 1em"></div>  
            <div class="div-table" style="float:right;">
               <div class="div-table-row  form-group"> 
                    <div class="div-table-col-3" style="text-align:right;"> 
                        <?php echo ucwords($obj->lang['total']); ?> 
                    </div>  
                    <div class="div-table-col-3" style="width:120px"> 
                         <?php echo $obj->inputNumber('total', array ('readonly' => true, 'etc' => 'style="text-align:right;"')) ;?>    
                    </div>  
                </div> 
            </div>   
        </div>          
      
        <div class="form-button-margin"></div>
        <div class="form-button-panel" > 
         <?php  echo $obj->generateSaveButton(array(),true);?>
        </div> 
        
    </form>   
   
     <?php  echo $obj->showDataHistory(); ?>
</div> 
</body>

</html>
