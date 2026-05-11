<?php 
include '../_config.php'; 
include '../_include-v2.php'; 

includeClass(array('SalesOrderInvoiceReceipt.class.php','Warehouse.class.php'));
$salesOrderInvoiceReceipt = new SalesOrderInvoiceReceipt();
$warehouse = new Warehouse();
$customer = new Customer();

$obj= $salesOrderInvoiceReceipt;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true));
     
$formAction = 'salesOrderInvoiceReceiptList';

$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false; 

$editWarehouseInactiveCriteria = ''; 
$_POST['trDate'] = date('d / m / Y');
$_POST['trReceivedDate'] = date('d / m / Y');
 
$rsDetail = array(); 

$rs = prepareOnLoadData($obj);  
 

if (!empty($_GET['id'])){ 
	$id = $_GET['id'];	
	  
    $rsDetail = $obj->getDetailWithRelatedInformation($id);  
	$rsCustomer = $customer->getDataRowById($rs[0]['customerkey']);
	$_POST['customerName'] = $rsCustomer[0]['name'] ;
	$_POST['hidCurrentCustomerName'] = $rsCustomer[0]['name'] ; 
	$_POST['hidCustomerKey'] = $rsCustomer[0]['pkey'] ;  
	$_POST['hidCurrentCustomerKey'] = $rsCustomer[0]['pkey'] ;   

	$_POST['grandTotal'] = $obj->formatNumber($rs[0]['grandtotal']);
	 
	$editWarehouseInactiveCriteria = ' or '.$warehouse->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['warehousekey']);   
}

$arrStatus = $obj->convertForCombobox($obj->getAllStatus(),'pkey','status');   
$arrWarehouse = $class->convertForCombobox($warehouse->searchData('','',true,' and ('.$warehouse->tableName.'.statuskey = 1' .$editWarehouseInactiveCriteria.')'),'pkey','name');  
 


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title> 
 
<script type="text/javascript">  
 
     
	jQuery(document).ready(function(){  
	 	 
        var tabID = selectedTab.newPanel[0].id;
        
        var salesOrderInvoiceReceipt = new SalesOrderInvoiceReceipt(tabID);
        prepareHandler(salesOrderInvoiceReceipt);     

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
                                            message: phpErrorMsg.customer[1]
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
  
  <form id="defaultForm" method="post" class="form-horizontal" action="<?php echo $formAction; ?>" >
     <?php prepareOnLoadDataForm($obj); ?>   
    <?php echo $obj->inputHidden('hidCurrentCustomerKey'); ?>
    <?php echo $obj->inputHidden('hidCurrentCustomerName'); ?> 

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
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['dateSent']); ?></label> 
                                <div class="col-xs-9"> 
                                      <?php echo $obj->inputDate('trDate'); ?> 
                                </div> 
                            </div> 
                             <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['dateReceived']); ?></label> 
                                <div class="col-xs-9"> 
                                      <?php echo $obj->inputDate('trReceivedDate'); ?> 
                                </div> 
                            </div> 
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['warehouse']); ?></label> 
                                <div class="col-xs-9"> 
                                    <?php echo $obj->inputSelect('selWarehouseKey', $arrWarehouse ); ?>  
                                </div> 
                            </div> 
                             <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['customer']); ?></label> 
                                <div class="col-xs-9"> 
                                      <?php  echo $obj->inputAutoComplete(array( 
                                                                                'objRefer' => $customer,
                                                                                'revalidateField' => true,
                                                                                'element' => array('value' => 'customerName',
                                                                                                   'key' => 'hidCustomerKey'),
                                                                                'source' =>array(
                                                                                                    'url' => 'ajax-customer.php',
                                                                                                    'data' => array(  'action' =>'searchData' )
                                                                                                ) ,
                                                                                'callbackFunction' => 'getTabObj().updateCustomerInformation(this,event, ui)'
                                                                              )
                                                                        );  
                                            ?>
                                </div> 
                            </div> 
                             <div class="form-group <?php echo $obj->hideOnDisabled(); ?>">
                                <label class="col-xs-3 control-label"></label> 
                                <div class="col-xs-9"> 
                                         <?php echo $obj->inputButton('btnImport',$obj->lang['showAll'],array('class' =>'btn btn-primary btn-second-tone')); ?>
                                </div> 
                            </div>   
                             <div class="form-group">
                                <label class="col-xs-12"></label>  
                            </div> 
                             
                             <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['attention']); ?></label> 
                                <div class="col-xs-9"> 
                                      <?php echo $obj->inputText('picName'); ?> 
                                </div> 
                            </div> 
                             <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['recipient']); ?></label> 
                                <div class="col-xs-9"> 
                                      <?php echo $obj->inputText('recipientName'); ?> 
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
       
      <div class="mnv-checkbox-group">
        <div class="div-table mnv-transaction transaction-detail" style="width:100%; border-bottom:1px solid #333; "  attr-level="0">
                <div class="div-table-row">  
                    <div class="div-table-col detail-col-header"><?php echo ucwords($obj->lang['invoiceCode']); ?></div> 
                    <!--<div class="div-table-col detail-col-header"><?php echo ucwords($obj->lang['description']); ?></div>-->
                    <div class="div-table-col detail-col-header" style="width:130px; text-align:center;"><?php echo ucwords($obj->lang['date']); ?></div>
                    <div class="div-table-col detail-col-header" style="width:150px; text-align:right;"><?php echo ucwords($obj->lang['total']); ?></div>
                    <div class="div-table-col detail-col-header <?php echo $obj->hideOnDisabled(); ?>  icon-col" style="width: 25px" > <?php echo $obj->inputCheckBox('chkPick-master', array('etc' => '')); ?></div> 
                    <div class="div-table-col detail-col-header <?php echo $obj->hideOnDisabled(); ?>  icon-col"></div>
                </div>
    
				<?php 
                           
                    $totalRows = count($rsDetail); 
            
                    for ($i=0;$i<=$totalRows; $i++){  

                        $class =  'transaction-detail-row';
                        $overwrite = true; 
                        $disabled = false;
                        $invoicekey = '';
                        $optionRows = 'display:none';
                        $totalDetailRows = 0 ;
                        
                        if ($i == $totalRows ){
                            $class = 'detail-row-template row-template';
                            $overwrite = false;
                            $disabled = true; 
                        } else {   
                            $_POST['hidInvoiceKey[]'] =  $rsDetail[$i]['invoicekey']; 
                            $_POST['invoiceCode[]'] =  $rsDetail[$i]['invoicecode'];
                            $_POST['invoiceDate[]'] =  $obj->formatDBDate($rsDetail[$i]['invoicedate']);  
                            $_POST['invoiceTotal[]'] =   $obj->formatNumber($rsDetail[$i]['amount']);
                            $_POST['hidDetailKey[]'] = $rsDetail[$i]['pkey'];
                            //$_POST['detailNote[]'] =  $rsSalesOrderInvoiceDetail[$i]['description'];    
                
                        } 
						
                  ?>
                 
             
            <div class="div-table-row <?php echo $class; ?>"> 
                <div class="div-table-col detail-col-detail" >
                    <?php echo $obj->inputText('invoiceCode[]',array('overwritePost' => $overwrite, 'disabled' => $disabled )); ?>
                    <?php echo $obj->inputHidden('hidInvoiceKey[]',array('overwritePost' => $overwrite, 'disabled' => $disabled)); ?> 
                    <?php echo $obj->inputHidden('hidDetailKey[]',array('overwritePost' => $overwrite,  'disabled' => $disabled)); ?>
                </div>    
                <div class="div-table-col detail-col-detail" style="width:130px; text-align:right;"><?php echo $obj->inputText('invoiceDate[]', array('overwritePost' => $overwrite, 'readonly'=>true,  'disabled' => $disabled, 'etc' => 'style="text-align:center;"  placeholder="'.$obj->lang['invoiceDate'].'"' )); ?></div> 
                <div class="div-table-col detail-col-detail" style="width:150px; text-align:right;"><?php echo $obj->inputNumber('invoiceTotal[]', array('overwritePost' => $overwrite, 'readonly'=>true,  'disabled' => $disabled, 'etc' => 'style="text-align:right;"' )); ?></div> 
                <div class="div-table-col detail-col-detail icon-col  <?php echo $obj->hideOnDisabled(); ?>"><?php echo $obj->inputCheckBox('chkPick[]', array('value' => 1, 'disabled' => $disabled)); ?></div>
                <div class="div-table-col detail-col-detail icon-col  align-top-adjust <?php echo $obj->hideOnDisabled(); ?>"><?php  echo $obj->inputLinkButton('btnDeleteRows' , '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button', 'etc' => 'tabindex="-1"')); ?></div>
            </div>      

                <?php } ?> 
                   
         </div>         
      </div>
      
        <div style="clear:both; height:1em;"></div> 
        <div style="float:left; display:inline-block;"><?php echo $obj->inputButton('btnAddRows', $obj->lang['addRows'], array('class' =>'btn btn-primary btn-second-tone')); ?></div>
       
        <div>  
            <div style="float:right; ">
                <div class="div-table icon-col  <?php echo $obj->hideOnDisabled(array(1)); ?>" style="float:right;">&nbsp;</div>  
                <div class="div-table icon-col  <?php echo $obj->hideOnDisabled(array(1)); ?>" style="float:right;">&nbsp;</div>   
                <div class="div-table" style="width:250px;float:right">
                    <div class="div-table-row  form-group"> 
                        <div class="div-table-col-3" style="text-align:right;">
                            <?php echo ucwords($obj->lang['total']); ?> 
                        </div>  
                        <div class="div-table-col-3" style="width:150px;"> 
                            <?php echo $obj->inputNumber('grandTotal', array ('readonly' => true, 'etc' => 'style="text-align:right;"')) ;?>   
                        </div>
                    </div>
                    
                </div>    
            </div>   
       </div>     
      
        <div style="clear:both"></div>
       
        <div class="form-button-margin"></div>
        <div class="form-button-panel" > 
         <?php  echo $obj->generateSaveButton(array(),true);   ?>  
        </div> 
        
    </form>   
   
     <?php  echo $obj->showDataHistory(); ?>
</div> 
</body>

</html>
