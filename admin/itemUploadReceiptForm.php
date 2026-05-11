<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass('ItemUploadReceipt.class.php');   
$itemUploadReceipt = createObjAndAddToCol( new ItemUploadReceipt()); 
$customer = createObjAndAddToCol( new Customer()); 
$city = createObjAndAddToCol( new City()); 
$warehouse = createObjAndAddToCol( new Warehouse()); 
$item= createObjAndAddToCol( new Item()); 
$cancelReason =  createObjAndAddToCol( new CancelReason()); 
    
$obj = $itemUploadReceipt;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true)); 

$formAction = 'itemUploadReceiptList';

$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false;

$editWarehouseInactiveCriteria = '';  
$editCancelReasonInactiveCriteria = '';
     
$rsDetail = array();

$_POST['trDate'] = date('d / m / Y H:i');

$rs = prepareOnLoadData($obj);  

if (!empty($_GET['id'])){ 
	$id = $_GET['id'];	
	 
	$rsDetail = $obj->getDetailWithRelatedInformation($id);
	   
	$rsCustomer = $customer->getDataRowById($rs[0]['customerkey']);
	$_POST['customerName'] = $rsCustomer[0]['name'] ; 
    
    $rsCity = $city->getDataRowById($rs[0]['citykey']);
	$_POST['cityName'] = $rsCity[0]['name'] ; 
    $_POST['txtCancelReason'] = $rs[0]['cancelreason'];
    
	$editWarehouseInactiveCriteria = ' or  '.$warehouse->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['warehousekey']);   
	//$editCancelReasonInactiveCriteria = ' or  '.$cancelReason->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['cancelreasonkey']);   

    $rsImage = array(); 
    if(!empty($rs[0]['filename'])){ 
        $rs[0]['phpthumbhash'] = getPHPThumbHash($rs[0]['filename']);
	
		$sourcePath = $obj->defaultDocUploadPath.$obj->uploadFolder.$id;
		$destinationPath = $obj->uploadTempDoc.$obj->uploadFolder.$id; 
		$obj->deleteAll($destinationPath); 
	
		if(!is_dir($destinationPath))  mkdir ($destinationPath,  0755, true);
				
		$obj->fullCopy($sourcePath,$destinationPath); 
	} 

}

$arrStatus = $obj->convertForCombobox($obj->getAllStatus(),'pkey','status');    
$arrWarehouse = $obj->convertForCombobox($warehouse->searchData('','',true,' and ('.$warehouse->tableName.'.statuskey = 1' .$editWarehouseInactiveCriteria.')'),'pkey','name');  
$arrCancelReason = $obj->convertForCombobox($cancelReason->searchData('','',true,' and ('.$cancelReason->tableName.'.statuskey = 1' .$editCancelReasonInactiveCriteria.')'),'pkey','reason',$obj->noCancelReason);  

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title> 
 
<script type="text/javascript">  
   
	jQuery(document).ready(function(){  
        var tabID = <?php echo ($isQuickAdd) ?  $_GET['tabID'] :  'selectedTab.newPanel[0].id';  ?>  
        
         var itemUploadReciept = new ItemUploadReciept(tabID, <?php echo json_encode($obj->validationFormSubmitParam()); ?>); 
         prepareHandler(itemUploadReciept);
 
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
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['code']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputAutoCode('code'); ?>
                                        </div> 
                                    </div>   
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['uploadDate']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputText('trDate', array('readonly'=>true)); ?> 
                                        </div> 
                                    </div>    
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['receiptDate']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputText('receiptDate', array('readonly'=>true)); ?> 
                                        </div> 
                                    </div>    
                                    <div class="form-group" style="display:none">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['warehouse']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo  $obj->inputSelect('selWarehouseKey', $arrWarehouse, array('readonly'=>true)); ?> 
                                        </div> 
                                    </div>    
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['customer']); ?></label> 
                                        <div class="col-xs-9"> 
                                              <?php  echo $obj->inputAutoComplete(array( 
                                                                                'objRefer' => $customer,
                                                                                'readonly' => true,
                                                                                'revalidateField' => true,
                                                                                'element' => array('value' => 'customerName',
                                                                                                   'key' => 'hidCustomerKey'),
                                                                                'source' =>array(
                                                                                                    'url' => 'ajax-customer.php',
                                                                                                    'data' => array('action' =>'searchData')
                                                                                                ) 
                                                                                )
                                                                        );  
                                            ?> 
                                        </div> 
                                    </div> 
                                      <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['city']); ?></label> 
                                        <div class="col-xs-9"> 
                                              <?php  echo $obj->inputAutoComplete(array( 
                                                                                'objRefer' => $city,
                                                                                'readonly' => true,
                                                                                'revalidateField' => true,
                                                                                'element' => array('value' => 'cityName',
                                                                                                   'key' => 'hidCityKey'),
                                                                                'source' =>array(
                                                                                                    'url' => 'ajax-city.php',
                                                                                                    'data' => array('action' =>'searchData')
                                                                                                ) 
                                                                                )
                                                                        );  
                                            ?> 
                                        </div> 
                                    </div> 
                                 
                                  <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['store']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo  $obj->inputText('storeName', array('readonly'=>true)); ?>
                                        </div> 
                                    </div>  
                                   <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['invoiceNumber']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo  $obj->inputText('invoiceNumber', array('readonly'=>true)); ?>
                                        </div> 
                                    </div>   
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['status']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo  $obj->inputSelect('selStatus', $arrStatus); ?>
                                        </div> 
                                    </div> 
                                    <div class="form-group"  >
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['cancelReason']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo ($rs[0]['statuskey'] <> 1) ? $obj->inputText('txtCancelReason') : $obj->inputSelect('selCancelReasonKey', $arrCancelReason); ?> 
                                        </div> 
                                    </div>   
                                 
                             </div>
                             <div class="div-tab-panel"> 
                                <?php
                                    if(!empty($rs[0]['pkey'])){ 
                                                echo '<div class="div-table odd-even-style" style="width: 100%">';

                                                echo '<div class="div-table-row">
                                                                <div class="div-table-col-5" style="border-top:1px solid #666;border-bottom:1px solid #666; font-weight:bold">'.$obj->lang['item'].'</div> 
                                                                <div class="div-table-col-5" style="border-top:1px solid #666;border-bottom:1px solid #666; font-weight:bold; text-align:right">'.$obj->lang['price'].'</div>
                                                                <div class="div-table-col-5" style="border-top:1px solid #666;border-bottom:1px solid #666; font-weight:bold; text-align:right">'.$obj->lang['qty'].'</div>
                                                                <div class="div-table-col-5" style="border-top:1px solid #666;border-bottom:1px solid #666; font-weight:bold; text-align:right">'.$obj->lang['point'].'</div>
                                                                <div class="div-table-col-5" style="border-top:1px solid #666;border-bottom:1px solid #666; font-weight:bold; text-align:right">'.$obj->lang['total'].'</div>
                                                          </div>
                                                          <div></div>
                                                          ' 
                                                    ;

                                                foreach($rsDetail as $row){
                                                    $classMuted = ($row['qty'] <=0 ) ? 'text-muted' : '';
                                                    echo '<div class="div-table-row '.$classMuted.'">
                                                                <div class="div-table-col-5" style="border-bottom:1px solid #dedede; ">'.$row['itemname'].'</div>
                                                                <div class="div-table-col-5" style="border-bottom:1px solid #dedede; text-align:right">'.$obj->formatNumber($row['sellingprice']).'</div>
                                                                <div class="div-table-col-5" style="border-bottom:1px solid #dedede; text-align:right">'.$obj->formatNumber($row['qty']).'</div>
                                                                <div class="div-table-col-5" style="border-bottom:1px solid #dedede; text-align:right">'.$obj->formatNumber($row['point']).'</div>
                                                                <div class="div-table-col-5" style="border-bottom:1px solid #dedede; text-align:right">'.$obj->formatNumber($row['qty'] * $row['point']).'</div>
                                                          </div>'; 
                                                } 
                                                echo '</div>';
                                        }

                                ?>
                            </div>
                    </div>
                    <div class="div-table-col">
                        <div class="div-tab-panel"> 
                            <div style="border:1px solid #dedede; border-radius: 0.5em; width:100%; min-height:45em; padding: 1em; text-align:center">
                                <!--<img src="/download.php?filename=<?php echo $class->phpThumbURLSrc.$obj->uploadFolder.$rs[0]['pkey'].'/'.$rs[0]['filename']; ?>">-->
                                 <img  style="max-height:100%; max-width: 100%" src="../phpthumb/phpThumb.php?src=<?php echo $class->phpThumbURLSrc.$obj->uploadFolder.$rs[0]['pkey'].'/'.$rs[0]['filename']; ?>&far=C&hash=<?php echo $rs[0]['phpthumbhash']; ?>">
                            </div>
                        </div>    
                     </div>
                     
           </div>
      </div> 
     
         <div class="form-button-margin"></div>
         <div class="form-button-panel" >  
            <?php   echo $obj->generateSaveButton(array(1),false);   ?> 
         </div> 
        
    </form>  

     <?php echo $obj->showDataHistory(); ?>
</div> 
</body>

</html>
