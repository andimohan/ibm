<?php 
include '../_config.php'; 
include '../_include-v2.php'; 

includeClass(array('TemplateSupplier.class.php'));
$templateSupplier = new TemplateSupplier();

$obj= $templateSupplier;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true));
     
$formAction = 'templateSupplierList';

$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false; 

$_POST['trDate'] = date('d / m / Y');

$rsDetail = array(); 
//$rsCustomer = array();
$rs = prepareOnLoadData($obj);  

if (!empty($_GET['id'])){     
    
	$id = $_GET['id'];
    
    $rsDetail = $obj->getDetailWithRelatedInformation($id);

    $_POST['trDesc'] = $rs[0]['trdesc'];
    $_POST['name'] = $rs[0]['name'];

	 
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
	 	 
        var tabID = selectedTab.newPanel[0].id;
        
        var templateSupplier = new TemplateSupplier(tabID);
        prepareHandler(templateSupplier);     

        var fieldValidation =  { 
                               code: { 
                                    validators: {
                                        notEmpty: {
                                            message: phpErrorMsg.code[1]
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
<!--
                             <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['customer']); ?></label> 
                                <div class="col-xs-9"> 
                                    <?php echo  $obj->inputSelect('selSupplier[]',$arrSupplier, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox') ); ?>
                                </div> 
                            </div>  
-->


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
                    <div class="div-table-col detail-col-header"><?php echo ucwords($obj->lang['supplier']); ?></div> 
                    <div class="div-table-col detail-col-header <?php echo $obj->hideOnDisabled(); ?>  icon-col"></div>
                </div>
    
				<?php 
                           
                    $totalRows = count($rsDetail); 
            
                    for ($i=0;$i<=$totalRows; $i++){  

                        $class =  'transaction-detail-row';
                        $overwrite = true; 
                        $disabled = false;
                        $optionRows = 'display:none';
                        $totalDetailRows = 0 ;
                        
                        if ($i == $totalRows ){
                            $class = 'detail-row-template row-template';
                            $overwrite = false;
                            $disabled = true;

                        } else {  
                            $_POST['hidDetailKey[]'] = $rsDetail[$i]['pkey'];
                            $_POST['supplierName[]'] =  $rsDetail[$i]['suppliername'];
                            $_POST['hidSupplierKey[]'] =  $rsDetail[$i]['supplierkey']; 

                        } 
						
                  ?>
                
                <div class="div-table-row <?php echo $class; ?>">  
                     <div class="div-table-col detail-col-detail" >
                        <?php echo $obj->inputText('supplierName[]', array('overwritePost' => $overwrite, 'disabled' => $disabled )); ?>
                        <?php echo $obj->inputHidden('hidSupplierKey[]',array('overwritePost' => $overwrite, 'disabled' => $disabled)); ?>
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
       
        <div class="form-button-panel" > 
         <?php  echo $obj->generateSaveButton();?>  
        </div> 
        
    </form>   
   
     <?php  echo $obj->showDataHistory(); ?>
</div> 
</body>

</html>
