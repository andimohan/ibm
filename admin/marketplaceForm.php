<?php 
require_once '../_config.php';  
require_once '../_include-v2.php';

includeClass(array('Marketplace.class.php','PaymentMethod.class.php','Customer.class.php')); 
$marketplace = createObjAndAddToCol( new Marketplace()); 
$customer = createObjAndAddToCol( new Customer()); 
$paymentMethod = createObjAndAddToCol( new PaymentMethod()); 

$obj= $marketplace;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true));
    

$formAction = 'marketplaceList';
 
$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false;


$rs = prepareOnLoadData($obj); 
 
$finalMarginDecimalType = 'inputnumber';
$finalMarginDecimal = 0;
$finalDiscDecimalType = 'inputnumber';
$finalDiscDecimal = 0;
$_POST['campaignStartDate'] = date('d / m / Y');
$_POST['campaignEndDate'] = date('d / m / Y');  

$editPaymentMethodInactiveCriteria = '';

if (!empty($_GET['id'])){ 
	$id = $_GET['id'];	
  
	$_POST['name'] = $rs[0]['name']; 
	$_POST['shopId'] = $rs[0]['shopid']; 
	$_POST['trDesc'] = $rs[0]['trdesc'];
     
    $_POST['hidCustomerKey'] = $rs[0]['customerkey']; 
    if (!empty($rs[0]['customerkey'])){
		$rsCustomer = $customer->getDataRowById($rs[0]['customerkey']);
		$_POST['customerName'] = $rsCustomer[0]['name'];
	}
     
    if ($rs[0]['margintype']  == 2){ 
        $finalMarginDecimal = 2;
        $finalMarginDecimalType = 'inputdecimal';
    }
    
    if ($rs[0]['discounttype']  == 2){ 
        $finalDiscDecimal = 2;
        $finalDiscDecimalType = 'inputdecimal';
    } 
    

    $_POST['selFinalPriceType'] = $rs[0]['finalpricetype'];
    
    $_POST['selPriceAdjustmentType'] = $rs[0]['priceadjustmenttype'];
	$_POST['priceAdjustment'] = $obj->formatNumber($rs[0]['priceadjustment'],-2);
    
    $_POST['selMarginType'] = $rs[0]['margintype'];
	$_POST['marginValue'] = $obj->formatNumber($rs[0]['margin'],$finalMarginDecimal);
    $_POST['accessToken'] = $rs[0]['accesstoken'] ;
    $_POST['selDiscountType'] = $rs[0]['discounttype'] ;
    $_POST['discountValue'] = $obj->formatNumber($rs[0]['discount'],$finalDiscDecimal);
    $_POST['campaignStartDate'] = $obj->formatDBDate($rs[0]['campaignstartdate'],'d / m / Y');
    $_POST['campaignEndDate'] = $obj->formatDBDate($rs[0]['campaignenddate'],'d / m / Y');
    $_POST['selPaymentMethod'] = $rs[0]['paymentmethodkey'] ;
        
    $editPaymentMethodInactiveCriteria = ' or '.$paymentMethod->tableName.'.pkey = '. $obj->oDbCon->paramString($rs[0]['pkey']);
} 

$arrStatus = $obj->convertForCombobox($obj->getAllStatus(),'pkey','status');   
$arrFinalPrice = array();
$arrFinalPrice[1] = $obj->lang['normalPrice'];    
$arrFinalPrice[2] = $obj->lang['discount'];    

$arrPaymentMethod = $obj->convertForCombobox($paymentMethod->searchData ('','',true,' and ('.$paymentMethod->tableName.'.statuskey = 1' . $editPaymentMethodInactiveCriteria.')'),'pkey','name');    

  
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title>  

<script type="text/javascript">  
    jQuery(document).ready(function(){   
         var tabID = <?php echo ($isQuickAdd) ?  $_GET['tabID'] :  'selectedTab.newPanel[0].id';  ?>  
         var marketplace = new Marketplace(tabID);
    
         prepareHandler(marketplace);   
        
         var fieldValidation =  {code: {
                                        validators: {
                                            notEmpty: {  message: phpErrorMsg.code[1] }, 
                                            },
                                        },
                                 name: {
                                        validators: {
                                            notEmpty: {  message: phpErrorMsg.name[1] }, 
                                            },
                                        },
                                 customerName: {
                                        validators: {
                                            notEmpty: {  message: phpErrorMsg.customer[1] }, 
                                        }, 
                                        }
                                }; 
        
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
                                             <?php echo  $obj->inputSelect('selStatus', $arrStatus); ?>
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
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['shopId']); ?></label> 
                                        <div class="col-xs-9"> 
                                             <?php echo $obj->inputText('shopId'); ?>
                                        </div> 
                                    </div>
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['token']); ?></label> 
                                        <div class="col-xs-9"> 
                                             <?php echo $obj->inputText('accessToken'); ?>
                                        </div> 
                                    </div>
                                 
                                      <div class="form-group">
                                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['customer']) .' ('.ucwords($obj->lang['default']).')'; ?></label> 
                                             <div class="col-xs-9">  
                                               <?php    
                                                echo $obj->inputAutoComplete(array(
                                                                'objRefer' => $customer,
                                                                'revalidateField' => true, 
                                                                'element' => array('value' => 'customerName',
                                                                                   'key' => 'hidCustomerKey'),
                                                                'source' =>array(
                                                                                    'url' => 'ajax-customer.php',
                                                                                    'data' => array(  'action' =>'searchData')
                                                                                ) 
                                                              )
                                                        );  
                                                ?> 
                                            </div> 
                                        </div> 
                                        <div class="form-group">
                                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['accountsReceivablePayment']); ?></label> 
                                            <div class="col-xs-9"> 
                                                <?php echo  $obj->inputSelect('selPaymentMethod', $arrPaymentMethod); ?>
                                            </div>  
                                        </div>  
                              
                                
                                        <div class="form-group">
                                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['priceAdjustment']); ?></label> 
                                            <div class="col-xs-9"> 
                                                <div class="flex">
                                                    <div><?php echo  $obj->inputSelect('selPriceAdjustmentType', $obj->arrDiscountType); ?></div>
                                                    <div class="consume"><?php echo $obj->inputNumber('priceAdjustment', array ('class'=> 'form-control ')); ?></div>
                                                </div> 
                                            </div>  
                                        </div>  
                              
                                        <div class="form-group">
                                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['margin']) .' ('.ucwords($obj->lang['default']).')'; ?></label> 
                                            <div class="col-xs-9"> 
                                                <div class="flex">
                                                    <div><?php echo  $obj->inputSelect('selMarginType', $obj->arrDiscountType,   array( 'etc' => 'onChange="getTabObj().updateMarginDecimal(this);"')); ?></div>
                                                    <div class="consume"><?php echo $obj->inputNumber('marginValue', array ('class'=> 'form-control ' . $finalDiscDecimalType)); ?></div>
                                                </div> 
                                            </div>  
                                        </div>  

                                        <div class="form-group">
                                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['finalPrice']); ?></label> 
                                            <div class="col-xs-9"> 
                                                 <?php echo $obj->inputSelect('selFinalPriceType',$arrFinalPrice); ?>
                                            </div> 
                                        </div>

                                        <div class="form-group isDiscount">
                                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['discount']) .' ('.ucwords($obj->lang['default']).')'; ?></label> 
                                            <div class="col-xs-9"> 
                                                <div class="flex">
                                                    <div><?php echo  $obj->inputSelect('selDiscountType', $obj->arrDiscountType,   array( 'etc' => 'onChange="getTabObj().updateDiscDecimal(this);"')); ?></div>
                                                    <div class="consume"><?php echo $obj->inputNumber('discountValue', array ('class'=> 'form-control ' . $finalDiscDecimalType)); ?></div>
                                                </div> 
                                            </div>  
                                        </div>  
                                  
                                        <div class="form-group">
                                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['campaignDate']); ?></label> 
                                            <div class="col-xs-4" style="padding-right:0"> 
                                                    <?php echo $obj->inputDate('campaignStartDate', array('etc' => 'style="text-align:center"')); ?> 
                                            </div> 
                                            <div class="col-xs-1 control-label" style="text-align:center; padding-left:0"> - </div>
                                            <div class="col-xs-4" style="padding-left:0"> 
                                                    <?php echo $obj->inputDate('campaignEndDate', array('etc' => 'style="text-align:center"')); ?> 
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
             
        <div class="form-button-panel" > 
       	 <?php echo $obj->generateSaveButton(); ?> 
        </div>  
    </form>  
     <?php echo $obj->showDataHistory(); ?> 
</div> 
</body>

</html>
