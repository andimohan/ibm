<?php 
require_once '../_config.php'; 
require_once '../_include.php'; 

$obj = $projectDumper;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true)); 

$formAction = 'projectDumperList';
 
$editWarehouseInactiveCriteria = '';  
 
$rsSalesDetail = array(); 

$_POST['trDate'] = date('d / m / Y');
   
 
$rs = prepareOnLoadData($obj);  

if (!empty($_GET['id'])){ 
	$id = $_GET['id'];	 
     
    $rsSalesDetail = $obj->getDetailWithRelatedInformation($id);
    //$rsTest = $obj->getJobInvoice($id);
    
    $rsCustomer = $customer->getDataRowById($rs[0]['customerkey']);
	$_POST['customerName'] = $rsCustomer[0]['name'] ; 
    
    if (!empty($rs[0]['locationkey'])){
        $rsLocation = $location->getDataRowById($rs[0]['locationkey']); 
        $_POST['locationName'] = $rsLocation[0]['name'] ;  
    }
    
    
	$editWarehouseInactiveCriteria = ' or  '.$warehouse->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['warehousekey']);
}

$rsKey = $obj->getTableKeyAndObj($obj->tableName);

$arrStatus = $obj->convertForCombobox($obj->getAllStatus(),'pkey','status');    
$arrWarehouse = $obj->convertForCombobox($warehouse->searchData('','',true,' and ('.$warehouse->tableName.'.statuskey = 1' .$editWarehouseInactiveCriteria.')'),'pkey','name');  
$arrDefaultUnit = $obj->convertForCombobox($itemUnit->searchData('','',true, ' and ('.$itemUnit->tableName.'.statuskey = 1 )'),'pkey','name');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title> 
 
<script type="text/javascript">  
   
	jQuery(document).ready(function(){  
        var tabID = selectedTab.newPanel[0].id; 
        
        projectDumper = new ProjectDumper(tabID);
        prepareHandler(projectDumper); 
        
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
                                                message:  phpErrorMsg.customer[1]
                                            }
                                        } 
                                    },

                                   locationName: { 
                                        validators: {
                                            notEmpty: {
                                                message:  phpErrorMsg.location[1]
                                            }
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
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['date']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputDate('trDate'); ?> 
                                        </div> 
                                    </div>    
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['warehouse']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo  $obj->inputSelect('selWarehouseKey', $arrWarehouse); ?>
                                        </div> 
                                    </div>    
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['name']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputText('projectName'); ?>
                                        </div> 
                                    </div>  
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['customer']); ?></label> 
                                        <div class="col-xs-9"> 
                                              <?php  echo $obj->inputAutoComplete(array( 
                                                                                'element' => array('value' => 'customerName',
                                                                                                   'key' => 'hidCustomerKey'),
                                                                                'source' =>array(
                                                                                                    'url' => 'ajax-customer.php',
                                                                                                    'data' => array(  'action' =>'searchData' )
                                                                                                ) 
                                                                              )
                                                                        );  
                                            ?> 
                                        </div> 
                                    </div> 
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['location']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php                
                                                    echo $obj->inputAutoComplete(array(
                                                                                        'element' => array('value' => 'locationName',
                                                                                                           'key' => 'hidLocationKey'),
                                                                                        'source' =>array(
                                                                                            'url' => 'ajax-location.php',
                                                                                            'data' => array(  'action' =>'searchData' )
                                                                                        )  
                                                                                      )
                                                                                );  
                                            ?>  
                                        </div> 
                                    </div>  
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['note']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo  $obj->inputTextArea('trDesc', array('etc' => 'style="height:10em;"')); ?>
                                        </div> 
                                    </div>    
                                 
                             </div>
                         
                    </div>
                    <div class="div-table-col"> 
                        
                    <div class="div-tab-panel"> 
                    <div class="div-table-caption border-green"><?php echo ucwords($obj->lang['price']); ?></div> 
                    <div class="div-table mnv-transaction transaction-detail" style="width:100%; border-bottom:1px solid #333; ">
                        <div class="div-table-row"> 
                            <div class="div-table-col detail-col-header"><?php echo ucwords($obj->lang['destinationSite']); ?></div>
                            <div class="div-table-col detail-col-header" style="width:90px; text-align:right;"><?php echo ucwords($obj->lang['distance']); ?> <span class="text-muted">(KM)</span> </div>
                            <div class="div-table-col detail-col-header" style="width:90px; text-align:right;"><?php echo ucwords($obj->lang['price']); ?> <span class="text-muted">/ Kg</span></div>
                            <div class="div-table-col detail-col-header  <?php echo $obj->hideOnDisabled(); ?> icon-col"></div>
                            <div class="div-table-col detail-col-header  <?php echo $obj->hideOnDisabled(); ?> icon-col"></div>
                        </div>

                            <?php 
                                $totalRows = count($rsSalesDetail); 

                                for ($i=0;$i<=$totalRows; $i++){  

                                    $class =  'transaction-detail-row';
                                    $overwrite = true;
                                    $disabled = false; 
                                    $arrUnit = $arrDefaultUnit;

                                    if ($i == $totalRows ){
                                        $class = 'detail-row-template';
                                        $overwrite = false;
                                        $disabled = true; 
                                        $unitname = 'Pcs';
                                    } else {  
                                        $decimal = 0;
                                        $inputnumber = 'inputnumber';
 
                                        $_POST['hidDetailKey[]'] =  $rsSalesDetail[$i]['pkey'];
                                        $_POST['hidLocationDetailKey[]'] =  $rsSalesDetail[$i]['locationkey']; 
                                        $_POST['locationDetailName[]'] =  $rsSalesDetail[$i]['locationname']; 
                                        $_POST['pricePerDistance[]'] =  $obj->formatNumber($rsSalesDetail[$i]['priceperdistance']);
                                        $_POST['qty[]'] =   $obj->formatNumber($rsSalesDetail[$i]['qty']);

                                    } 

                            ?>


                            <div class="div-table-row <?php echo $class; ?>">
                                <div class="div-table-col detail-col-detail">
                                    <?php echo $obj->inputText('locationDetailName[]',array('overwritePost' => $overwrite, 'disabled' => $disabled)); ?>
                                    <?php echo $obj->inputHidden('hidLocationDetailKey[]',array('overwritePost' => $overwrite,'disabled' => $disabled)); ?>
                                    <?php echo $obj->inputHidden('hidDetailKey[]',array('overwritePost' => $overwrite, 'disabled' => $disabled)); ?>
                                </div> 
                                <div class="div-table-col detail-col-detail"><?php echo $obj->inputNumber('qty[]', array('overwritePost' => $overwrite,'value' => 1, 'etc' => 'style="text-align:right;"', 'disabled' => $disabled)); ?></div>
                                <div class="div-table-col detail-col-detail"><?php echo $obj->inputNumber('pricePerDistance[]', array('overwritePost' => $overwrite, 'etc' => 'style="text-align:right;"' , 'disabled' => $disabled)); ?></div>
                                <div class="div-table-col detail-col-detail <?php echo $obj->hideOnDisabled(); ?>"><?php echo $obj->inputLinkButton('btnAddRows' , '<i class="fas fa-plus-circle"></i>', array('class' => 'btn btn-link add-row-button', 'etc' => 'attr-template="detail-row-template"')); ?></div>
                                <div class="div-table-col detail-col-detail <?php echo $obj->hideOnDisabled(); ?>"><?php echo $obj->inputLinkButton('btnDeleteRows' , '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button', 'etc' => 'tabIndex="-1"')); ?></div>
                                
                            </div>

                        <?php } ?> 

                     </div>    
                        <div style="clear:both; height:1em;"></div> 
                        <!--<div style="float:left; display:inline-block;"><?php echo $obj->inputButton('btnAddRows', $obj->lang['addRows'], array('class' => 'btn btn-primary btn-second-tone')); ?></div>
                    --></div>
                    </div>
           </div>
      </div> 
          
          
        
         <div class="form-button-margin"></div>
         <div class="form-button-panel" >  
         <?php  echo $obj->generateSaveButton(array(),true);?>
        </div> 
        
    </form>  

     <?php echo $obj->showDataHistory(); ?>
</div> 
</body>

</html>
