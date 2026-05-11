<?php 
require_once '../_config.php'; 
require_once '../_include.php'; 

$obj= $warrantyClaimProgress;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true)); 

$formAction = 'warrantyClaimProgressList';
 
$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false;
 
$rsDetail = array();
$arrProgress = array();

$editWarehouseInactiveCriteria = '';
$editBUSWarehouseInactiveCriteria = '';
$_POST['trDate'] = date('d / m / Y');
$_POST['progressDate[]'] = date('d / m / Y');

$rs = prepareOnLoadData($obj);   

if (!empty($_GET['id'])){ 
	$id = $_GET['id'];	 
    
    $rsDetail = $obj->getDetailWithRelatedInformation($id);

    //$_POST['refcode'] = $obj->getRefCode($id, $rs[0]['reftable']); 
    $_POST['trDate'] = $obj->formatDBDate($rs[0]['trdate'],'d / m / Y');
    $_POST['warrantyDate'] = $obj->formatDBDate($rs[0]['warrantydate'],'d / m / Y');
    $_POST['newWarrantyDate'] = $obj->formatDBDate($rs[0]['newwarrantydate'],'d / m / Y');

    $_POST['hidRefKey'] = $rs[0]['refkey'] ; 
    $_POST['hidRefHeaderKey'] = $rs[0]['refheaderkey'] ; 
    $_POST['amount'] = $obj->formatNumber($rs[0]['amount']); 

	$rsWarranty = $warrantyClaim->getDataRowById($rs[0]['refheaderkey']);
    if(!empty($rsWarranty)){
       $_POST['refCode'] = $rsWarranty[0]['code'] ;
	   $_POST['hidCustomerKey'] = $rsWarranty[0]['customerkey'] ; 
       $rsCustomer = $customer->getDataRowById($rsWarranty[0]['customerkey']);
	   $_POST['customerName'] = $rsCustomer[0]['name'] ; 
    }
	
    $_POST['selClaimResult'] = $rs[0]['claimresultkey']; 
    $_POST['newSerialNumber'] = $rs[0]['newserialnumber'];
    $_POST['hidNewVendorPartNumberKey'] = $rs[0]['newvendorpartnumberkey']; 
    $_POST['hidNewItemKey'] = $rs[0]['itemkey'];
    
    $rsItem = $item->searchVendorPartNumberForAutoComplete('',' and '.$item->tableVendorPartNumber.'.pkey = '.$rs[0]['newvendorpartnumberkey']);
    if(!empty($rsItem)){
        $_POST['newVendorPartNumber'] = $rsItem[0]['value'];
        $_POST['newItemName'] = $rsItem[0]['itemname'];  
    }

    
	$_POST['trDesc'] = $rs[0]['trdesc'];
	$_POST['selWarehouseKey'] = $rs[0]['warehousekey']; 
    $_POST['selBUSWarehouseKey'] = $rs[0]['buswarehousekey']; 
    $_POST['hidItemKey'] = $rs[0]['itemkey'] ;
	$_POST['serialNumber'] = $rs[0]['serialnumber'];  
	$_POST['hidVendorPartNumberKey'] = $rs[0]['vendorpartnumberkey'] ; 
    
    $rsItem = $item->searchVendorPartNumberForAutoComplete('',' and '.$item->tableVendorPartNumber.'.pkey = '.$rs[0]['vendorpartnumberkey']);
    if(!empty($rsItem)){  
       $_POST['vendorPartNumber'] = $rsItem[0]['value'] ;
       $_POST['itemName'] = $rsItem[0]['itemname'] ;
    }
    
	$editWarehouseInactiveCriteria = ' or  '.$warehouse->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['warehousekey']);  
	$editBUSWarehouseInactiveCriteria = ' or  '.$warehouse->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['buswarehousekey']);  
	
} 

$arrStatus = $obj->convertForCombobox($obj->getAllStatus(),'pkey','status');
$arrClaimResult = $obj->convertForCombobox($warrantyClaim->getClaimResult(),'pkey','name');
$arrWarehouse = $class->convertForCombobox($warehouse->searchData('','',true,' and (isrma = 1 and '.$warehouse->tableName.'.statuskey = 1' .$editWarehouseInactiveCriteria.')'),'pkey','name'); 
$arrBUSWarehouse = $class->convertForCombobox($warehouse->searchData('','',true,' and (isbus = 1 and '.$warehouse->tableName.'.statuskey = 1' .$editBUSWarehouseInactiveCriteria.')'),'pkey','name'); 
 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title> 
 
<script type="text/javascript">    
	jQuery(document).ready(function(){  
	 	    
        var tabID = selectedTab.newPanel[0].id;
        var varConstant = {  CLAIM_TYPE : <?php echo json_encode(CLAIM_TYPE); ?> };
        
         var warrantyClaimProgress = new WarrantyClaimProgress(tabID,varConstant); 
         prepareHandler(warrantyClaimProgress); 
		 

         var fieldValidation =  { 
            code: { 
                validators: {
                    notEmpty: {
                        message: phpErrorMsg.code[1]
                    }, 
                }
            },
            
            refCode: { 
                validators: {
                    notEmpty: {
                        message: phpErrorMsg.reference[1]
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
                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['warehouse']); ?> (RMA)</label> 
                            <div class="col-xs-9"> 
                                <?php echo $obj->inputSelect('selWarehouseKey', $arrWarehouse, array('readonly' => true)); ?>  
                            </div> 
                        </div>
                        <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['date']); ?></label> 
                            <div class="col-xs-9"> 
                                    <?php echo $obj->inputText('trDate', array('readonly' => true)); ?> 
                            </div> 
                        </div>
                        <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['reference']); ?></label> 
                            <div class="col-xs-9"> 
                                 <?php  
                                        echo $obj->inputHidden('hidRefKey');  
                                        echo  $obj->inputAutoComplete( array(
                                                                    'objRefer' => $warrantyClaim, 
                                                                    'element' => array('value' => 'refCode',
                                                                                       'key' => 'hidRefHeaderKey'),
                                                                    'readonly' => true,
                                                                    'source' =>array(
                                                                                        'url' => 'ajax-warranty-claim.php',
                                                                                        'data' => array(  'action' =>'searchData' )
                                                                                    ) 
                                                        ));
                                 ?> 
                            </div> 
                        </div>   
                        <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['customer']); ?></label> 
                            <div class="col-xs-9"> 
                                 <?php  
                                        echo  $obj->inputAutoComplete( array(
                                                                    'objRefer' => $customer,
                                                                    'readonly' => true,
                                                                    'element' => array('value' => 'customerName',
                                                                                       'key' => 'hidCustomerKey'),
                                                                    'source' =>array(
                                                                                        'url' => 'ajax-customer.php',
                                                                                        'data' => array(  'action' =>'searchData' )
                                                                                    ) 
                                                        ));
                                 ?>
                            </div> 
                        </div> 
                         
                        <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['claim']); ?></label> 
                            <div class="col-xs-9"> 
                                 <?php echo $obj->inputSelect('selClaimResult',$arrClaimResult, array('allowedStatusForEdit' => array(1,2))); ?>  
                            </div> 
                        </div>
                          
                        <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['cost']); ?></label> 
                            <div class="col-xs-9"> 
                                 <?php echo $obj->inputNumber('amount',array('allowedStatusForEdit' => array(1,2))); ?> 
                            </div> 
                        </div>  
                        <div class="form-group"> 
                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['note']); ?></label> 
                            <div class="col-xs-9"> 
                                    <?php echo  $obj->inputTextArea('trDesc', array('etc' => 'style="height:10em;"')); ?>
                            </div> 
                        </div>    
                    </div>
                    
                    <div class="div-tab-panel"> 
                            <div class="div-table-caption border-purple"><?php echo $obj->lang['progressInformation']; ?></div>
                                     <div class="div-table mnv-transaction transaction-detail" style="width:100%"> 
                                        <div class="div-table-row"> 
                                             
                                            <div class="div-table-col detail-col-header" style="width:120px; text-align:center;border:0"><?php echo ucwords($obj->lang['date']); ?></div>  
                                            <div class="div-table-col detail-col-header" style="border:0"><?php echo ucwords($obj->lang['description']); ?></div>
                                            <div class="div-table-col detail-col-header  <?php echo $obj->hideOnDisabled(); ?>" style="width:25px;border:0"></div>   
                                        </div> 
                                          <?php  
                                              $totalRows = count($rsDetail); 
                                               
                                              for ($i=0;$i<=$totalRows; $i++){  

                                                    $class =  'transaction-detail-row';
                                                    $style = '';
                                                    $overwrite = true;
                                                    $etc = ''; 

                                                    $statusStyle = '';
                                                    $detail = '';
                                                  
                                                        
                                                    if ($i == $totalRows ){
                                                        $class = 'detail-row-template';
                                                        $style = 'style="display:none"';
                                                        $overwrite = false;
                                                        $etc = 'disabled="disabled"';  
                                                    } else {      
                                                        $_POST['hidDetailKey[]'] = $rsDetail[$i]['pkey']; 
                                                        $_POST['description[]'] = $rsDetail[$i]['description'];  
                                                        $_POST['progressDate[]'] =  $obj->formatDBDate($rsDetail[$i]['trdate'],'d / m / Y');
                                                     } 
                                            ?>

                                            <div class="div-table-row  <?php echo $class; ?>" <?php echo $style; ?> >
                                                <div class="div-table-col detail-col-detail"><?php echo $obj->inputHidden('hidDetailKey[]',array('overwritePost' => $overwrite, 'etc' => $etc)); ?><?php echo $obj->inputDate('progressDate[]',array('overwritePost' => $overwrite ,'value' => date('d / m / Y'), 'etc' => 'style="text-align:center;"' .$etc )); ?></div> 
                                                <!--<div class="div-table-col detail-col-detail"><?php echo $obj->inputSelect('progressName[]',$arrProgress,array('overwritePost' => $overwrite, 'etc' => $etc) ); ?> <?php echo $obj->inputHidden('hidProgressKey[]',array('overwritePost' => $overwrite, 'etc' => $etc)); ?></div>-->
                                    
                                                <div class="div-table-col detail-col-detail"><?php echo $obj->inputText('description[]',array('overwritePost' => $overwrite, 'etc' => $etc)); ?></div>
                                                <div class="div-table-col detail-col-detail  <?php echo $obj->hideOnDisabled(); ?>"><?php echo   $obj->inputLinkButton('btnDeleteRows' , '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button', 'etc' =>  'style="padding:6px 0"')); ?></div>
                                            </div> 

                                            <?php } ?>
                                    </div>  
                         
                                    <div style="clear:both; height:1em;"></div>  
                                    <div class="div-table transaction-detail" style="width:100%;">
                                        <div class="div-table-row">
                                            <div class="div-table-col detail-col-detail">
                                                <?php echo $obj->inputButton('btnAddRows',ucwords($obj->lang['addRows']), array('class' =>'btn btn-primary btn-second-tone')); ?>
                                            </div> 
                                        </div>     
                                    </div>  
                          </div> 
                </div> 
                <div class="div-table-col">   
                    <div class="div-tab-panel"> 
                         <div class="div-table-caption border-blue"><?php echo ucwords($obj->lang['claimSettlement']); ?></div>
                        <div class="col-xs-12 section-title"><?php echo strtoupper($obj->lang['claimedItem']); ?></div>  
                            <div class="form-group">
                                <label class="col-xs-3 control-label" ><?php echo ucwords($obj->lang['serialNumber']); ?></label> 
                                <div class="col-xs-9"> 
                                     <div class="flex">
                                        <div  class="consume" style="margin-right:0" ><?php echo $obj->inputText('serialNumber', array('readonly' => true)); ?> </div>
                                        <div class="newsn" style="display:none; padding:0">
                                             <div style="padding-left:0.5em; padding-right:0.5em">/</div>
                                             <div>
                                               <?php echo $obj->inputText('changeSerialNumber'); ?>  
                                             </div>
                                         </div>
                                     </div> 
                                </div> 
                            </div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['vendorPartNumber']); ?></label> 
                                <div class="col-xs-9"> 
                                    <?php echo $obj->inputText('vendorPartNumber', array('readonly' => true)); ?> 
                                    <?php echo $obj->inputHidden('hidVendorPartNumberKey', array('readonly' => true)); ?> 
                                </div> 
                            </div> 
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['itemName']); ?></label> 
                                <div class="col-xs-9"> 
                                     <?php echo $obj->inputText('itemName', array('readonly' => true)); ?> 
                                     <?php echo $obj->inputHidden('hidItemKey', array('readonly' => true)); ?> 
                                </div> 
                            </div>    
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['warrantyExpiredDate']); ?></label> 
                                <div class="col-xs-9"> 
                                     <?php echo $obj->inputText('warrantyDate', array('readonly' => true)); ?>  
                                </div> 
                            </div>    
                            <div class="replacement-panel" <?php if (isset($rs[0]['claimresultkey']) && $rs[0]['claimresultkey'] == CLAIM_TYPE['CN']) echo 'style="display:none;"'; ?>>
                            <div class="col-xs-12 section-title"><?php echo strtoupper($obj->lang['replacementItem']); ?></div>  

                            <div class="form-group isSNReplace">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['warehouse']); ?> (BUS)</label> 
                                <div class="col-xs-9"> 
                                    <?php echo $obj->inputSelect('selBUSWarehouseKey', $arrBUSWarehouse,array('allowedStatusForEdit' => array(1))); ?>  
                                </div> 
                            </div>
                            <div class="form-group isSNReplace">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['serialNumber']); ?></label> 
                                <div class="col-xs-9"> 
                                     <?php echo $obj->inputText('newSerialNumber',array('allowedStatusForEdit' => array(1))); ?> 
                                </div> 
                            </div> 

                            <div class="form-group isUpgrade">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['vendorPartNumber']); ?></label> 
                                <!--<div class="col-xs-9"> 
                                    <?php  
                                            echo  $obj->inputAutoComplete( array(
                                                                        'objRefer' => $item, 
                                                                        'element' => array('value' => 'newVendorPartNumber',
                                                                                           'key' => 'hidNewVendorPartNumberKey'),
                                                                        'source' =>array(
                                                                                            'url' => 'ajax-item.php',
                                                                                            'data' => array(  'action' =>'searchVendorPartNumber' )
                                                                                        ), 
                                                                        'allowedStatusForEdit' => array(1)
                                                                        //'callbackFunction' => 'getTabObj().updateNewPartNumber()' 
                                                            ));
                                     ?>
                                </div>-->
                                <div class="col-xs-9"> 
                                     <?php echo $obj->inputText('newVendorPartNumber', array('readonly' => true)); ?> 
                                     <?php echo $obj->inputHidden('hidNewVendorPartNumberKey', array('readonly' => true)); ?>  
                                </div> 
                                
                            </div> 
                            <div class="form-group isUpgrade">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['itemName']); ?></label> 
                                <div class="col-xs-9"> 
                                     <?php echo $obj->inputText('newItemName', array('readonly' => true)); ?> 
                                     <?php echo $obj->inputHidden('hidNewItemKey', array('readonly' => true)); ?>  
                                </div> 
                            </div>   
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['warrantyExpiredDate']); ?></label> 
                                <div class="col-xs-9"> 
                                     <?php echo $obj->inputText('newWarrantyDate', array('readonly' => true)); ?>  
                                </div> 
                            </div>  
                            </div>
                    </div> 
                </div> 
            </div>
      </div>       
        <div class="form-button-panel" > 
         <?php  
            $hasFinancialAccess = $security->isAdminLogin($warrantyClaimProgress->financialAccess,10);  
            if ( !(!empty($rs) && $rs[0]['statuskey'] == 2 && !$hasFinancialAccess)){
                 echo $obj->generateSaveButton(array(),true);   
            }    
         ?>  
        </div> 
        
    </form>   
   
     <?php  echo $obj->showDataHistory(); ?>
</div> 
</body>

</html>
