<?php
require_once '../_config.php'; 
require_once '../_include.php'; 
 
$obj=  $downPayment;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true)); 

$formAction = 'downPaymentList';
 
$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false;

$_POST['trDate'] = date('d / m / Y');

$rs = prepareOnLoadData($obj);   

if (!empty($_GET['id'])){ 
	$id = $_GET['id'];	 
 
    $_POST['trDate'] = $obj->formatDBDate($rs[0]['trdate'],'d / m / Y');
    $_POST['trDesc'] = $rs[0]['trdesc'];
    $_POST['amount'] = $obj->formatNumber($rs[0]['amount']);
	
    $_POST['hidCustomerKey'] = $rs[0]['customerkey']; 
	if (!empty($rs[0]['customerkey'])){
		$rsCustomer = $customer->getDataRowById($rs[0]['customerkey']);
		$_POST['customerName'] = $rsCustomer[0]['name'];
	}  
    
    $_POST['hidRefKey'] = $rs[0]['refkey']; 
	if (!empty($rs[0]['refkey'])){
		$rsJobOrder = $truckingServiceOrder->getDataRowById($rs[0]['refkey']);
		$_POST['refCode'] = $rsJobOrder[0]['code'];
	}  
   
} 

$arrStatus = $obj->convertForCombobox($obj->getAllStatus(),'pkey','status');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title>

<script type="text/javascript"> 
	
	jQuery(document).ready(function(){  
        var tabID = <?php echo ($isQuickAdd) ?  $_GET['tabID'] :  'selectedTab.newPanel[0].id';  ?>  
        setOnDocumentReady(tabID);  
          
		 $('#defaultForm-' + tabID )
			.bootstrapValidator({ 
				feedbackIcons: {
					valid: 'glyphicon glyphicon-ok',
					invalid: 'glyphicon glyphicon-remove',
					validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
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
				
				
            }
        })
        .on('success.form.bv', function(e) {
               <?php echo $obj->submitFormScript(); ?> 
        });
		  
		
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
                                        <label class="col-xs-3 control-label"><?php echo $obj->lang['customer']; ?></label> 
                                        <div class="col-xs-9"> 
                                             <?php     
                                                   echo $obj->inputAutoComplete(array(
                                                                                            'objRefer' => $customer,
                                                                                            'revalidateField' => true, 
                                                                                            'element' => array('value' => 'customerName',
                                                                                                               'key' => 'hidCustomerKey'),
                                                                                            'source' => array(
                                                                                                                'url' => 'ajax-customer.php',
                                                                                                                'data' => array(  'action' =>'searchData', 'statuskey' => '2' )
                                                                                                            )  
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
                                                                                            'element' => array('value' => 'refCode',
                                                                                                               'key' => 'hidRefKey'),
                                                                                            'source' => array(
                                                                                                                'url' => 'ajax-trucking-service-order.php',
                                                                                                                'data' => array(  'action' =>'searchData', 'statuskey' => '2' )
                                                                                                            )  
                                                                                          )
                                                                                    );  
                                                 
                                                       
                                                ?> 
                                        </div> 
                                    </div> 
                        
                        <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['amount']); ?></label> 
                            <div class="col-xs-9"> 
                                    <?php echo $obj->inputNumber('amount'); ?> 
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
       
     
        <div class="form-button-margin"></div>
        <div class="form-button-panel" > 
       	 <?php  echo $obj->generateSaveButton(); ?> 
        </div> 
        
    </form>   
   
     <?php  echo $obj->showDataHistory(); ?>
</div> 
</body>

</html>
