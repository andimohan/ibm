<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass(array("CustomerIssue.class.php", "SalesOrder.class.php"));
$customerIssue = new CustomerIssue();
$salesOrder = new SalesOrder();
$obj= $customerIssue;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true));
     
$formAction = 'customerIssueList';

$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false;
$editCategoryInactiveCriteria = '';
 
$_POST['postdate'] = date('d / m / Y');

$rs = prepareOnLoadData($obj);  
$rsSalesDetail = array();

if (!empty($_GET['id'])){ 
	$id = $_GET['id']; 
    $editCategoryInactiveCriteria = ' or '.$contactCategory->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['categorykey']);

    $rsSalesOrder = $salesOrder->searchData($salesOrder->tableName . '.pkey', $rs[0]['sokey'], true);
    $rsSalesDetail = $salesOrder->getDetailWithRelatedInformation($rsSalesOrder[0]['pkey']);
    $_POST['customer'] = $rsSalesOrder[0]['customername']; 
    $_POST['salesOrderCode'] = $rsSalesOrder[0]['code']; 
    $_POST['contact'] = $rsSalesOrder[0]['recipientphone']; 
    $_POST['address'] = $rsSalesOrder[0]['recipientaddress']; 

} 

$arrStatus = $obj->convertForCombobox($obj->getAllStatus(),'pkey','status');   
// $arrCategory = $class->convertForCombobox($contactCategory->searchData('','',true, ' and ('.$contactCategory->tableName.'.statuskey = 1'. $editCategoryInactiveCriteria.')'),'pkey','name');  
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
         
        
		 $('#defaultForm-' + tabID.newPanel[0].id )
			.bootstrapValidator({ 
				feedbackIcons: {
					valid: 'glyphicon glyphicon-ok',
					invalid: 'glyphicon glyphicon-remove',
					validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
                name: { 
                    validators: {
                        notEmpty: {
                            message: 'Nama harus diisi.'
                        }, 
                    }
                }
			 
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
                            <label class="col-xs-3 control-label">Status</label> 
                            <div class="col-xs-9"> 
                                 <?php echo  $obj->inputSelect('selStatus', $arrStatus, array('readonly' => true)); ?>
                            </div> 
                        </div>    
                        <div class="form-group">
                            <label class="col-xs-3 control-label">Kode</label> 
                            <div class="col-xs-9"> 
                                <?php echo $obj->inputAutoCode('code', array('readonly' => true)); ?>
                            </div> 
                        </div> 
                        <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['soCode']); ?></label> 
                            <div class="col-xs-9"> 
                                  <?php echo $obj->inputText('salesOrderCode', array('readonly' => true)); ?>
                            </div> 
                        </div> 
                        <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['customer']); ?></label> 
                            <div class="col-xs-9"> 
                                  <?php echo $obj->inputText('customer', array('readonly' => true)); ?> 
                            </div> 
                        </div> 
                        <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['contact']); ?></label> 
                            <div class="col-xs-9"> 
                                  <?php echo $obj->inputText('contact', array('readonly' => true)); ?> 
                            </div> 
                        </div> 
                        <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['address']); ?></label> 
                            <div class="col-xs-9"> 
                                <?php echo  $obj->inputTextArea('address', array('etc' => 'style="height:10em;"',  'readonly' => true)); ?> 
                            </div> 
                        </div> 
                        <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['subject']); ?></label> 
                            <div class="col-xs-9"> 
                                  <?php echo $obj->inputText('subject', array('readonly' => true)); ?> 
                            </div> 
                        </div> 
                        <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['issue']); ?></label> 
                            <div class="col-xs-9"> 
                                <?php echo  $obj->inputTextArea('issue', array('etc' => 'style="height:10em;"',  'readonly' => true)); ?> 
                            </div> 
                        </div> 
                    </div>   
                </div>  
                <div class="div-table-col">
                    <div class="div-tab-panel">
                        <div class="div-table-caption border-green"><?php echo ucwords($obj->lang['pickUpList']); ?></div>
                        <div class="div-table sales-detail transaction-detail" style="width:100%">
                            <div class="div-table-row">
                                <div class="div-table-col-5" style="border-top:1px solid #666;border-bottom:1px solid #666; width:50px;text-align:left;">
                                    <strong><?php echo ucwords($obj->lang['qty']); ?></strong>
                                </div>
                                <div class="div-table-col-5" style="border-top:1px solid #666;border-bottom:1px solid #666;text-align:left">
                                    <strong><?php echo ucwords($obj->lang['description']); ?></strong>
                                </div>
                                <!-- <div class="div-table-col-5" style="border-top:1px solid #666;border-bottom:1px solid #666; width:100px;text-align:left;">
                                    <strong><?php echo ucwords($obj->lang['description']); ?></strong>
                                </div> -->
                                <div class="div-table-col-5" style="border-top:1px solid #666;border-bottom:1px solid #666;  width:70px;text-align:right">
                                    <strong><?php echo ucwords($obj->lang['total']); ?></strong>
                                </div>
                            </div>
                                <?php

                                $totalRows = count($rsSalesDetail);
                                $totalDispatchWeight = 0;
                                for ($j = 0; $j <= $totalRows; $j++) {
                                    $class =  'transaction-detail-row sales-order-row';
                                    $overwrite = true;
                                    $disabled = false;
                                    $display = '';
                                    $customerName = '';
                                    $customerWeight = '';

                                    $itemName = '';
                                    $qtySO = '';
                                    $subtotal = 0;

                                    if ($j == $totalRows) {
                                        $class = 'sales-order-template';
                                        $overwrite = false;
                                        $disabled = true;
                                        $display = 'style="display:none"';
                                    } else {
                                        $qtySO = $obj->formatNumber($rsSalesDetail[$j]['qty'], 2);
                                        $itemName = $rsSalesDetail[$j]['itemname'];
                                        $subtotal = $obj->formatNumber($rsSalesDetail[$j]['total'], 2);

                                        $customerName = $rsDetailWO[$j]['customername'];
                                        $WOCode = $rsDetailWO[$j]['workordercode'];
                                    }

                                ?>
                                    <div class="div-table-row  <?php echo $class; ?>" <?php echo $display; ?>>
                                        <div class="div-table-col-5 detail-col-detail so-code" style="border-bottom:1px solid #dedede; text-align:left; vertical-align:top"><?php echo $qtySO; ?></div>
                                        <div class="div-table-col-5 detail-col-detail waste" style="border-bottom:1px solid #dedede; text-align:left;vertical-align:top"> <?php echo $itemName; ?></div>
                                        <div class="div-table-col-5 detail-col-detail total-detail" style="border-bottom:1px solid #dedede; text-align:right;vertical-align:top"><?php echo $subtotal; ?> </div>
                                    </div>
                                <?php  } ?>
                        </div> 
                    </div>
                </div>   
             </div>
        </div>        
       
    </form>   
     <?php echo $obj->showDataHistory(); ?>
</div> 
</body>

</html>
