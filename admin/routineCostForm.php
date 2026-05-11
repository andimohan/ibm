<?php 
include '../_config.php'; 
include '../_include.php'; 


$obj= $routineCost;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true));
     
$formAction = 'routineCostList';

$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false; 

$editWarehouseInactiveCriteria = ''; 
//$_POST['trDate'] = date('d / m / Y');
$_POST['trRepeatDate'] = date('d / m / Y');

$rsDetail = array(); 

$rs = prepareOnLoadData($obj);  

if (!empty($_GET['id'])){ 
    
	$id = $_GET['id'];	
	  
    $rsDetail = $obj->getDetailWithRelatedInformation($id);
    $_POST['trRepeatDate'] = $obj->formatDBDate($rs[0]['repeatdate'],'d / m / Y');
    $_POST['trDesc'] = $rs[0]['trdesc'];
    
	$rsSupplier = $supplier->getDataRowById($rs[0]['supplierkey']);
	$_POST['supplierName'] = $rsSupplier[0]['name'] ;
	$_POST['hidSupplierKey'] = $rsSupplier[0]['pkey'] ;  
	$_POST['selWarehouseKey'] =$rs[0]['warehousekey'];  
    
	$editWarehouseInactiveCriteria = ' or '.$warehouse->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['warehousekey']);   
    
	 
}

$arrStatus = $obj->convertForCombobox($obj->getAllStatus(),'pkey','status');    
$arrCharge = $obj->convertForCombobox($obj->getChargeType(),'pkey','name');    
$arrRepeatType = $obj->convertForCombobox($obj->getRepeatPeriod(),'pkey','name');    
$arrAPType =  $class->convertForCombobox($ap->getAPType(),'pkey','name');
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
        
        var routineCost = new RoutineCost(tabID);
        prepareHandler(routineCost);     

        var fieldValidation =  { 
                               code: { 
                                    validators: {
                                        notEmpty: {
                                            message: phpErrorMsg.code[1]
                                        }, 
                                    }
                                }, 
                                supplierName: { 
                                    validators: {
                                        notEmpty: {
                                            message: phpErrorMsg.supplier[1]
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
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['repeatEvery']); ?></label> 
                                <div class="col-xs-9"> 
                                      <div class="flex">
                                        <div class="" style="width:100px"><?php echo $obj->inputSelect('selRepeatType', $arrRepeatType); ?> </div>
                                        <div class="consume"><?php echo $obj->inputDate('trRepeatDate', array('etc' => 'style="text-align:center"')); ?> </div>
                                      </div>
                                </div> 
                            </div> 
                           <!--  <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['date']); ?></label> 
                                <div class="col-xs-9"> 
                                      <?php echo $obj->inputDate('trDate'); ?> 
                                </div> 
                            </div> -->
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['warehouse']); ?></label> 
                                <div class="col-xs-9"> 
                                    <?php echo $obj->inputSelect('selWarehouseKey', $arrWarehouse ); ?>  
                                </div> 
                            </div>   
                           
                             <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['supplier']); ?></label> 
                                <div class="col-xs-9"> 
                                      <?php  echo $obj->inputAutoComplete(array( 
                                                                                'objRefer' => $supplier,
                                                                                'revalidateField' => true,
                                                                                'element' => array('value' => 'supplierName',
                                                                                                   'key' => 'hidSupplierKey'),
                                                                                'source' =>array(
                                                                                                    'url' => 'ajax-supplier.php',
                                                                                                    'data' => array(  'action' =>'searchData' )
                                                                                                ),
                                                                                'popupForm' => array(
                                                                                                    'url' => 'supplierForm.php',
                                                                                                    'element' => array('value' => 'supplierName',
                                                                                                                'key' => 'hidSupplierKey'),
                                                                                                    'width' => '1000px',
                                                                                                    'title' => ucwords($obj->lang['add'] . ' - ' . $obj->lang['supplier'])
                                                                                                ) 
                                                                              )
                                                                        );  
                                            ?>
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
                    <div class="div-table-col detail-col-header"><?php echo ucwords($obj->lang['description']); ?></div> 
                    <div class="div-table-col detail-col-header" style="width:160px;"><?php echo ucwords($obj->lang['transactionType']); ?></div>
                    <div class="div-table-col detail-col-header" style="width:160px;"><?php echo ucwords($obj->lang['forEach']); ?></div>
                    <div class="div-table-col detail-col-header" style="width:130px; text-align:right;"><?php echo ucwords($obj->lang['amount']); ?></div> 
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
                            $_POST['description[]'] =  $rsDetail[$i]['description'];
                            $_POST['selChargeType[]'] = $rsDetail[$i]['chargetype'];
                            $_POST['selTransactionType[]'] = $rsDetail[$i]['aptype'];
                            $_POST['amount[]'] =   $obj->formatNumber($rsDetail[$i]['amount']); 
                        } 
						
                  ?>
                
                <div class="div-table-row <?php echo $class; ?>">  
                     <div class="div-table-col detail-col-detail" >
                        <?php echo $obj->inputText('description[]',array('overwritePost' => $overwrite, 'disabled' => $disabled )); ?>
                        <?php echo $obj->inputHidden('hidDetailKey[]',array('overwritePost' => $overwrite, 'disabled' => $disabled)); ?>
                    </div>    
                    <div class="div-table-col detail-col-detail" ><?php echo $obj->inputSelect('selTransactionType[]', $arrAPType, array('overwritePost' => $overwrite,   'disabled' => $disabled)); ?></div> 
                    <div class="div-table-col detail-col-detail" ><?php echo $obj->inputSelect('selChargeType[]', $arrCharge, array('overwritePost' => $overwrite,   'disabled' => $disabled )); ?></div> 
                    <div class="div-table-col detail-col-detail" ><?php echo $obj->inputNumber('amount[]', array('overwritePost' => $overwrite,  'disabled' => $disabled, 'etc' => 'style="text-align:right;"' )); ?></div>  
                    <div class="div-table-col detail-col-detail icon-col  align-top-adjust <?php echo $obj->hideOnDisabled(); ?>"><?php  echo $obj->inputLinkButton('btnDeleteRows' , '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button', 'etc' => 'tabindex="-1"')); ?></div>
                </div> 
             
                <?php } ?> 
                   
         </div>         
      </div> 
        <div style="clear:both; height:1em;"></div>  
        <div style="float:left; display:inline-block;"><?php echo $obj->inputButton('btnAddRows', $obj->lang['addRows'], array('class' =>'btn btn-primary btn-second-tone')); ?></div>
    
        <div style="clear:both; height:2em;"></div> 
        <div class="form-button-panel" > 
         <?php  echo $obj->generateSaveButton();?>  
        </div> 
        
    </form>   
   
     <?php  echo $obj->showDataHistory(); ?>
</div> 
</body>

</html>
