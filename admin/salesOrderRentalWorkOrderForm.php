<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php';
 
includeClass('SalesOrderRentalWorkOrder.class.php');
$salesOrderRentalWorkOrder = createObjAndAddToCol( new SalesOrderRentalWorkOrder()); 
$salesOrderRental = createObjAndAddToCol( new SalesOrderRental()); 
$warehouse = createObjAndAddToCol( new Warehouse()); 
$customer = createObjAndAddToCol( new Customer()); 
$location = createObjAndAddToCol( new Location()); 
$city = createObjAndAddToCol( new City()); 
$itemUnit = createObjAndAddToCol( new ItemUnit()); 
$item = createObjAndAddToCol( new Item()); 

$obj = $salesOrderRentalWorkOrder;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true)); 

$formAction = 'salesOrderRentalWorkOrderList';


$editWarehouseInactiveCriteria = ''; 
$editCityInactiveCriteria = ''; 
 
$rsSalesDetail = array();

$_POST['trDate'] = date('d / m / Y');
$_POST['invoiceDate'] = date('d / m / Y');
 
$rs = prepareOnLoadData($obj);  

if (!empty($_GET['id'])){ 
	$id = $_GET['id'];	
	 
	$rsSalesDetail = $obj->getDetailWithRelatedInformation($id);
	 
	$_POST['trDate'] = $obj->formatDBDate($rs[0]['trdate'],'d / m / Y');
	$_POST['invoiceDate'] = $obj->formatDBDate($rs[0]['invoicedate'],'d / m / Y');
    $_POST['selWarehouseKey'] =$rs[0]['warehousekey']; 
    $_POST['hidSalesOrderKey'] =$rs[0]['refkey']; 

    if(!empty($rs[0]['customerkey'])){
        $rsCustomer = $customer->getDataRowById($rs[0]['customerkey']);
        $_POST['customerName'] = $rsCustomer[0]['name'] ;
        $_POST['hidRecipientKey'] = $rsCustomer[0]['pkey'] ;  
        $_POST['trDesc'] = $rs[0]['trdesc'];
    }
    
    $_POST['hidLocationKey'] = $rs[0]['locationkey'];
	if(!empty($rs[0]['locationkey'])){ 
   	   $rsLocation = $location->getDataRowById($rs[0]['locationkey']);
	   $_POST['locationName'] = $rsLocation[0]['name'] ; 
    }

    if(!empty($rs[0]['refkey'])){
        $rsSO = $salesOrderRental->getDataRowById($rs[0]['refkey']);
        $_POST['salesOrderCode'] = $rsSO[0]['code'] ;
        $_POST['recipientName'] = $rsSO[0]['recipientname'];
        $_POST['recipientPhone'] = $rsSO[0]['recipientphone'];
        $_POST['recipientEmail'] = $rsSO[0]['recipientemail'];
        $_POST['recipientAddress'] = $rsSO[0]['recipientaddress'];
        $_POST['hidRecipientCityKey'] = $rsSO[0]['recipientcitykey'];
        if(!empty($rsSO[0]['recipientcitykey'])){ 
            $rsCity = $city->searchData($city->tableName.'.pkey',$rsSO[0]['recipientcitykey'],true);
            $_POST['recipientCityName'] = $rsCity[0]['citycategoryname']; 
        }
    }
    
	
    
    $rsKey = $obj->getTableKeyAndObj($obj->tableName);
    
	$editWarehouseInactiveCriteria = ' or  '.$warehouse->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['warehousekey']);  
}


if (!empty($_GET['id']) && ($_POST['selStatus']==2 || $_POST['selStatus']==3 )){ 
    $_POST['action'] = 'resendEmail';
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
 
        salesOrderRentalWorkOrder = new SalesOrderRentalWorkOrder(tabID,<?php echo json_encode($rs); ?>);
        prepareHandler(salesOrderRentalWorkOrder); 
        
        var fieldValidation =  {
                                 code: { 
                                        validators: {
                                            notEmpty: {
                                                message: phpErrorMsg.code[1]
                                            }, 
                                        }
                                    }, 

                                    salesOrderCode: { 
                                        validators: {
                                            notEmpty: {
                                                message:  phpErrorMsg.salesOrder[1]
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
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['invoiceDate']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputDate('invoiceDate'); ?> 
                                        </div> 
                                    </div>
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['warehouse']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo  $obj->inputSelect('selWarehouseKey', $arrWarehouse); ?>
                                        </div> 
                                    </div> 
                                 
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['salesOrderRental']); ?></label> 
                                        <div class="col-xs-9"> 
                                              <?php  echo $obj->inputAutoComplete(array(
                                                                                'element' => array('value' => 'salesOrderCode',
                                                                                                   'key' => 'hidSalesOrderKey'),
                                                                                'source' =>array(
                                                                                                    'url' => 'ajax-sales-order-rental.php',
                                                                                                    'data' => array('action' =>'searchData')
                                                                                                ) ,
                                                                                'callbackFunction' => 'getTabObj().updateSalesOrderInformation();'                                                                          
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
                                                                                        'objRefer'=>$location,
                                                                                        'revalidateField' => false, 
                                                                                        'readonly' => true, 
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
                            <div class="div-table-caption border-blue"><?php echo ucwords($obj->lang['customerInformation']); ?></div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['name']); ?></label> 
                                <div class="col-xs-9"> 
                                <?php  echo $obj->inputAutoComplete(array(
                                                                                'readonly' => true,
                                                                                'element' => array('value' => 'recipientName',
                                                                                                   'key' => 'hidRecipientKey')
                                                                              )
                                                                        );  
                                            ?> 
                                </div> 
                            </div> 
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['phone']); ?></label> 
                                <div class="col-xs-9"> 
                                    <?php echo $obj->inputText('recipientPhone',array('readonly' => true)); ?> 
                                </div> 
                            </div> 
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['email']); ?></label> 
                                <div class="col-xs-9"> 
                                    <?php echo $obj->inputText('recipientEmail',array('readonly' => true)); ?>  
                                </div> 
                            </div> 
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['city']); ?></label> 
                                <div class="col-xs-9"> 
                                     <?php  echo $obj->inputAutoComplete(array(
                                                                'objRefer' => $city,
                                                                'revalidateField' => false, 
                                                                'readonly' => true,
                                                                'element' => array('value' => 'recipientCityName',
                                                                                   'key' => 'hidRecipientCityKey'),
                                                                'source' =>array(
                                                                                    'url' => 'ajax-city.php',
                                                                                    'data' => array(  'action' =>'searchData' )
                                                                                )                                                               
                                                                )
                                                        );  
                                    ?> 
                                </div> 
                            </div> 
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['address']); ?></label> 
                                <div class="col-xs-9"> 
                                    <?php echo  $obj->inputTextArea('recipientAddress', array('etc' => 'style="height:10em;"', 'readonly' => true)); ?> 
                                </div> 
                            </div>  
                        </div>    
                    </div>
           </div>
      </div> 
      
        <div class="div-table mnv-transaction transaction-detail" style="width:100%; border-bottom:1px solid #333; ">
                <div class="div-table-row"> 
                    <div class="div-table-col detail-col-header"><?php echo ucwords($obj->lang['itemName']); ?></div>
                    <div class="div-table-col detail-col-header" style="width:80px; text-align:right;"><?php echo ucwords($obj->lang['qty']); ?></div>
                    <div class="div-table-col detail-col-header" style="width:80px;"><?php echo ucwords($obj->lang['unit']); ?></div> 
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
                        } else {  
                            $decimal = 0;
                            $inputnumber = 'inputnumber';
 
                            
                            $_POST['hidDetailKey[]'] =  $rsSalesDetail[$i]['pkey'];
                            $_POST['hidItemKey[]'] =  $rsSalesDetail[$i]['itemkey']; 
                            $_POST['itemName[]'] =  $rsSalesDetail[$i]['itemname']; 
                            $_POST['qty[]'] =   $obj->formatNumber($rsSalesDetail[$i]['qty']); 
                            $_POST['selUnit[]'] =  $rsSalesDetail[$i]['unitkey']; 
                            $_POST['hidRefSODetailKey[]'] =  $rsSalesDetail[$i]['refsodetailkey']; 
                                      
                            $arrUnit = $obj->convertForCombobox($item->getAvailableUnit($rsSalesDetail[$i]['itemkey']),'conversionunitkey','unitname','',array('relconversionmultiplier' => 'conversionmultiplier')); 
                        } 
				 
                ?>
            
                
                <div class="div-table-row <?php echo $class; ?>">
                    <div class="div-table-col detail-col-detail">
                        <?php echo $obj->inputText('itemName[]',array('overwritePost' => $overwrite, 'disabled' => $disabled)); ?>
                        <?php echo $obj->inputHidden('hidItemKey[]',array('overwritePost' => $overwrite,'disabled' => $disabled)); ?>
                        <?php echo $obj->inputHidden('hidGramasi[]',array('overwritePost' => $overwrite,'disabled' => $disabled)); ?>
                        <?php echo $obj->inputHidden('hidDetailKey[]',array('overwritePost' => $overwrite, 'disabled' => $disabled)); ?>
                        <?php echo $obj->inputHidden('hidRefSODetailKey[]',array('overwritePost' => $overwrite, 'disabled' => $disabled)); ?>
                    </div> 
                    <div class="div-table-col detail-col-detail"><?php echo $obj->inputNumber('qty[]', array('overwritePost' => $overwrite,'value' => 1, 'etc' => 'style="text-align:right;"', 'disabled' => $disabled)); ?></div>
                    <div class="div-table-col detail-col-detail"><?php echo $obj->inputSelect('selUnit[]',$arrUnit, array('overwritePost' => $overwrite,  'disabled' => $disabled)); ?></div>
                   <div class="div-table-col detail-col-detail <?php echo $obj->hideOnDisabled(); ?>"><?php echo $obj->inputLinkButton('btnDeleteRows' , '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button', 'etc' => 'tabIndex="-1"')); ?></div>
                </div>
                 
            <?php } ?> 
                   
         </div>        
          
          <div style="clear:both; height:1em;"></div> 
          <div style="float:left; display:inline-block;"><?php echo $obj->inputButton('btnAddRows', $obj->lang['addRows'], array('class' => 'btn btn-primary btn-second-tone')); ?></div>
       
         
      
         <div class="form-button-margin"></div>
         <div class="form-button-panel" >  
         <?php  echo $obj->generateSaveButton(array(),true);?> 
        </div> 
        
    </form>  

     <?php echo $obj->showDataHistory(); ?>
</div> 
</body>

</html>
