<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass(array('TemplateEMKLPurchaseItem.class.php','Service.class.php'));
$templateEMKLPurchaseItem = new TemplateEMKLPurchaseItem();
$service = createObjAndAddToCol(new Service(SERVICE)); 

$obj= $templateEMKLPurchaseItem;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true));
     
$formAction = 'templateEMKLPurchaseItemList';

$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false; 

$rsDetail = array(); 

$rs = prepareOnLoadData($obj);  

if (!empty($_GET['id'])){ 
	$id = $_GET['id'];	
	  
    $rsDetail = $obj->getDetailWithRelatedInformation($id);
	
    $_POST['name'] = $rs[0]['name'];  
    $_POST['trDesc'] = $rs[0]['trdesc'];  
	 
}

$arrStatus = $obj->convertForCombobox($obj->getAllStatus(),'pkey','status');
$arrCost = $obj->convertForCombobox($service->searchData('','',true),'pkey','name'); 

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title> 
 
<script type="text/javascript">  
 
     
	jQuery(document).ready(function(){  
	 	 
        var tabID = selectedTab.newPanel[0].id;
        
        var templateEMKLPurchaseItem = new TemplateEMKLPurchaseItem(tabID);
        prepareHandler(templateEMKLPurchaseItem);     

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
											message:  phpErrorMsg.name[1]
										}, 
									}
								},
                            } ; 

        
        setFormValidation(getTabObj(), $('#defaultForm-' + tabID), fieldValidation, <?php echo json_encode($obj->validationFormSubmitParam()); ?>  );
 
         
    }); 
	 
</script>

<style>
    .invoice-detail > .transaction-detail-row > .div-table-col {padding: 1em 0em !important /*background-color: transparent!important*/}    
    .invoice-detail .icon-col.align-top-adjust {padding-top: 1.6em !important}
</style>  
 
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
                    <div class="div-table-col" style="padding:0">
                        <div class="div-table" style="width:100%">
                            <div class="div-table-row">  
                                <div class="div-table-col detail-col-header" ><?php echo ucwords($obj->lang['service']); ?></div> 
                            </div>
                        </div>
                    </div>  
                    <div class="div-table-col detail-col-header <?php echo $obj->hideOnDisabled(); ?>  icon-col"></div>
                </div>
    
				<?php 
                           
                    $totalRows = count($rsDetail); 
            
                    for ($i=0;$i<=$totalRows; $i++){  

                        $class =  'transaction-detail-row';
                        $overwrite = true;
                        $readonly = false;
                        $disabled = false;
                        $optionRows = 'display:none';
                        $totalDetailRows = 0 ;
                        
                        if ($i == $totalRows ){
                            $class = 'detail-row-template row-template';
                            $overwrite = false;
                            $disabled = true;

                        } else {  

                            $readonly = false;
                            $_POST['hidCostKey[]'] =  $rsDetail[$i]['itemkey'];
                            $_POST['costName[]'] =  $rsDetail[$i]['costname'];

                            $_POST['hidDetailKey[]'] = $rsDetail[$i]['pkey'];

                        } 
						
                  ?>
                
                <div class="div-table-row <?php echo $class; ?>"> 
                     <div class="div-table-col"> 
                        <?php echo $obj->inputText('costName[]',array('overwritePost' => $overwrite, 'disabled' => $disabled )); ?>
                        <?php echo $obj->inputHidden('hidCostKey[]',array('overwritePost' => $overwrite,  'disabled' => $disabled)); ?> 
                        <?php echo $obj->inputHidden('hidDetailKey[]',array('overwritePost' => $overwrite, 'disabled' => $disabled)); ?> 
                    </div>
                    <div class="div-table-col detail-col-detail icon-col  align-top-adjust <?php echo $obj->hideOnDisabled(); ?>"><?php  echo $obj->inputLinkButton('btnDeleteRows' , '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button', 'etc' => 'tabindex="-1"')); ?></div>
                </div> 
             
                <?php } ?> 
                   
         </div>         
      </div>
      
        <div style="clear:both; height:1em;"></div> 
        <div style="float:left; display:inline-block;"><?php echo $obj->inputButton('btnAddRows', $obj->lang['addRows'], array('class' =>'btn btn-primary btn-second-tone')); ?></div>
       
      
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
