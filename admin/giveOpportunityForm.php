<?php 
include '../_config.php'; 
include '../_include-v2.php'; 

includeClass(array('GiveOpportunity.class.php','Warehouse.class.php'));
$giveOpportunity = createObjAndAddToCol(new GiveOpportunity());
$warehouse = createObjAndAddToCol(new Warehouse());
$customer = createObjAndAddToCol(new Customer());


$obj= $giveOpportunity;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true));
     
$formAction = 'giveOpportunityList';

$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false; 

$editWarehouseInactiveCriteria = ''; 
$_POST['trDate'] = date('d / m / Y');
 
$rsDetail = array(); 

$rs = prepareOnLoadData($obj);  
 

if (!empty($_GET['id'])){ 
	$id = $_GET['id'];	
    
    
    if(!empty($rs[0]['torecipientkey'])){
        $rsCustomer = $customer->getDataRowById($rs[0]['torecipientkey']);
        $_POST['recipientName'] = $rsCustomer[0]['code'].' - '.$rsCustomer[0]['name'];
    }
	
    
    if(!empty($rs[0]['refkey'])){
        $rsCustomer = $customer->getDataRowById($rs[0]['refkey']);
        $_POST['customerName'] = $rsCustomer[0]['code'].' - '.$rsCustomer[0]['name'];
    }
    
	$editWarehouseInactiveCriteria = ' or '.$warehouse->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['warehousekey']);   
}

$arrStatus = $obj->convertForCombobox($obj->getAllStatus(),'pkey','status'); 
$arrCategory = $obj->generateComboboxOpt(array('data' => $obj->getCategoryType(), 'label' => 'name'));

$arrType = array();
$arrType[0]= 'Semua Member';
$arrType[3]= 'Pro Member';

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title> 
 
<script type="text/javascript">  
 
     
	jQuery(document).ready(function(){  
	 	 
        var tabID = selectedTab.newPanel[0].id;
        
        var giveOpportunity = new GiveOpportunity(tabID);
        prepareHandler(giveOpportunity);     

        var fieldValidation =  { 
                               code: { 
                                    validators: {
                                        notEmpty: {
                                            message: phpErrorMsg.code[1]
                                        }, 
                                    }
                                }, 
                                name: { 
                                    validators: {
                                        notEmpty: {
                                            message: phpErrorMsg.name[1]
                                        }, 
                                    }
                                }, 
                                phone: { 
                                    validators: {
                                        notEmpty: {
                                            message: phpErrorMsg.phone[1]
                                        }, 
                                    }
                                }, 
                                description: { 
                                    validators: {
                                        notEmpty: {
                                            message: phpErrorMsg.description[1]
                                        }, 
                                    }
                                },
                               recipientName: { 
                                    validators: {
                                        notEmpty: {
                                            message: phpErrorMsg.customer[1]
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
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['type']); ?></label> 
                                <div class="col-xs-9"> 
                                    <?php echo  $obj->inputSelect('selType', $arrType); ?>
                                </div> 
                            </div> 
                             
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['from']); ?></label> 
                                <div class="col-xs-9"> 
                                      <?php  echo $obj->inputAutoComplete(array(  
                                                                                'revalidateField' => true,
                                                                                'element' => array('value' => 'customerName',
                                                                                                   'key' => 'refkey'),
                                                                                'source' =>array(
                                                                                                    'url' => 'ajax-customer.php',
                                                                                                    'data' => array('action' => 'searchData', 'membershiplevel' => 3, 'searchField' => 'code,name')
                                                                                                ) ,

                                                                              )
                                                                        );  
                                            ?>
                                </div> 
                            </div>    
                            <div class="form-group opportunity">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['customer']); ?></label> 
                                <div class="col-xs-9"> 
                                      <?php  echo $obj->inputAutoComplete(array(  
                                                                                'revalidateField' => true,
                                                                                'element' => array('value' => 'recipientName',
                                                                                                   'key' => 'hidRecipientKey'),
                                                                                'source' =>array(
                                                                                                    'url' => 'ajax-customer.php',
                                                                                                    'data' => array('action' => 'searchData', 'membershiplevel' => 3, 'searchField' => 'code,name')
                                                                                                ) ,

                                                                              )
                                                                        );  
                                            ?>
                                </div> 
                            </div> 
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['category']); ?></label> 
                                <div class="col-xs-9"> 
                                    <?php echo  $obj->inputSelect('selCategoryKey', $arrCategory); ?>
                                </div> 
                            </div> 
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['name']); ?> <?php echo ucwords($obj->lang['PIC']); ?></label> 
                                <div class="col-xs-9"> 
                                     <?php echo $obj->inputText('name'); ?>
                                </div> 
                            </div>  
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['phone']); ?> <?php echo ucwords($obj->lang['PIC']); ?></label> 
                                <div class="col-xs-9"> 
                                     <?php echo $obj->inputText('phone'); ?>
                                </div> 
                            </div> 

                             
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['description']); ?></label> 
                                <div class="col-xs-9"> 
                                         <?php echo  $obj->inputTextArea('description', array('etc' => 'style="height:10em;"')); ?>
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
