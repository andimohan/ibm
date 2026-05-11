<?php 
include '../_config.php'; 
include '../_include.php'; 


$obj= $campaign;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true));
     
$formAction = 'campaignList';

$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false; 

$_POST['startDate'] = date('d / m / Y');
$_POST['endDate'] = date('d / m / Y'); 

$rsDetail = array(); 
$rsItemCategoryDetail = array();  
$rsItemDetail = array(); 
$rsBrandDetail = array(); 
$rsMarketplaceDetail = array();

$editWarehouseInactiveCriteria = ''; 

$finalDiscDecimal = 0;
$finalDiscDecimalType = 'inputnumber';

/*
$priceAdjustmentDecimal = 0;
$priceAdjustmentDecimalType = 'inputnumber';
*/

$rs = prepareOnLoadData($obj);  

if (!empty($_GET['id'])){ 
	$id = $_GET['id'];	
    $rsItemCategoryDetail = $obj->getItemCategory($id);  
    $rsItemDetail = $obj->getItem($id);  
    $rsBrandDetail = $obj->getBrand($id); 
    $rsMarketplaceDetail = $obj->getMarketplace($id); 
    
    if ($rs[0]['discounttype']  == 2){ 
        $finalDiscDecimal = 2;
        $finalDiscDecimalType = 'inputdecimal';
    } 
 
    /*if ($rs[0]['priceadjustmenttype']  == 2){ 
        $priceAdjustmentDecimal = 2;
        $priceAdjustmentDecimalType = 'inputdecimal';
    }*/
 
    $editWarehouseInactiveCriteria = ' or  '.$warehouse->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['warehousekey']);  
 
}

$arrStatus = $obj->convertForCombobox($obj->getAllStatus(),'pkey','status');   
$arrWarehouse = $obj->convertForCombobox($warehouse->searchData('','',true,' and ('.$warehouse->tableName.'.statuskey = 1' .$editWarehouseInactiveCriteria.')'),'pkey','name');  
 
$arrFinalPrice = array();
$arrFinalPrice[1] = $obj->lang['normalPrice'];    
$arrFinalPrice[2] = $obj->lang['discount'];    

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title> 
 
<script type="text/javascript">  
  
	jQuery(document).ready(function(){  
	 	 
        var tabID = <?php echo ($isQuickAdd) ?  $_GET['tabID'] :  'selectedTab.newPanel[0].id';  ?>  
        var arrCampaign = {};
        
        arrCampaign.rsItemCategoryDetail = <?php echo json_encode($rsItemCategoryDetail); ?>;
        arrCampaign.rsBrandDetail = <?php echo json_encode($rsBrandDetail); ?>;
        arrCampaign.rsItemDetail = <?php echo json_encode($rsItemDetail); ?>;
        arrCampaign.rsMarketplaceDetail = <?php echo json_encode($rsMarketplaceDetail); ?>;
        
        var campaign = new Campaign(tabID,arrCampaign);
        prepareHandler(campaign);     

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
                                value: {
                                    validators: { 
                                        greaterThan: {
                                            value: 0,
                                            inclusive: false,
                                            separator: ',', 
                                            message: phpErrorMsg.amount[2]
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
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['name']); ?></label> 
                                <div class="col-xs-9"> 
                                      <?php echo $obj->inputText('name'); ?> 
                                </div> 
                            </div>    
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['period']); ?></label> 
                                    <div class="col-xs-9">
                                            <div class="flex">
                                                <div class="consume"><?php echo $obj->inputDate('startDate',array('etc' => 'style="text-align:center;"')); ?></div>
                                                <div> / </div>
                                                <div class="consume"><?php echo $obj->inputDate('endDate',array('etc' => 'style="text-align:center;"')); ?> </div>
                                            </div>   
                                    </div>                                    
                            </div> 
                             
                            <!--<div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['priceAdjustment']); ?></label> 
                                <div class="col-xs-9"> 
                                    <div class="flex">
                                        <div><?php echo  $obj->inputSelect('selPriceAdjustmentType', $obj->arrDiscountType); ?></div>
                                        <div class="consume"><?php echo $obj->inputNumber('priceAdjustment', array ('class'=> 'form-control  ' . $priceAdjustmentDecimalType)); ?></div>
                                    </div> 
                                </div>  
                            </div>  -->

                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['margin']) .' ('.ucwords($obj->lang['default']).')'; ?></label> 
                                <div class="col-xs-9"> 
                                    <div class="flex">
                                        <div><?php echo  $obj->inputSelect('selMarginType', $obj->arrDiscountType,   array( 'etc' => 'onChange="getTabObj().updateMarginDecimal(this);"')); ?></div>
                                        <div class="consume"><?php echo $obj->inputNumber('marginValue', array ('class'=> 'form-control ' . $finalDiscDecimalType)); ?></div>
                                    </div> 
                                </div>  
                            </div>  

                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['finalPrice']); ?></label> 
                                <div class="col-xs-9"> 
                                     <?php echo $obj->inputSelect('selFinalPriceType',$arrFinalPrice); ?>
                                </div> 
                            </div>

                            <div class="form-group isDiscount">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['discount']) .' ('.ucwords($obj->lang['default']).')'; ?></label> 
                                <div class="col-xs-9"> 
                                    <div class="flex">
                                        <div><?php echo  $obj->inputSelect('selDiscountType', $obj->arrDiscountType,   array( 'etc' => 'onChange="getTabObj().updateDiscDecimal(this);"')); ?></div>
                                        <div class="consume"><?php echo $obj->inputNumber('discountValue', array ('class'=> 'form-control ' . $finalDiscDecimalType)); ?></div>
                                    </div> 
                                </div>  
                            </div>  

                             
                        </div>
                       
                </div>
                <div class="div-table-col"> 
                    <div class="div-tab-panel"> 
                            <div class="div-table-caption border-blue"><?php echo ucwords($obj->lang['note']); ?></div> 
                            <div class="form-group">
                                <div class="col-xs-12"> <?php echo  $obj->inputTextArea('trDesc', array('etc' => 'style="height:10em;"')); ?></div>
                            </div> 
                    </div> 
                </div>             
             </div>
      </div>
       
        <div style="clear:both; height: 2em"></div>
        <div class="div-tab-panel">  
            <div class="div-table-caption border-green"><?php echo ucwords($obj->lang['criteria']); ?></div>  
             
            <div style="clear:both; height: 1em "></div> 
            <div class="div-table" style="width: 100%">
            <div class="div-table-row">
                
                <div class="div-table-col-5" style="vertical-align:top; width: 33.33%;  padding-bottom:1.5em">  
                    <div class="div-table marketplace-criteria mnv-transaction transaction-detail" style="width:100%;">
                        <div class="div-table-caption"  style="border:0; font-size: 1.2em;font-weight:bold">
                            <?php echo strtoupper($obj->lang['marketplace']); ?> 
                            <div style="font-weight:normal"><?php echo $obj->inputCheckBox('chkAllMarketplace') . ' ' . $obj->lang['allMarketplace']; ?></div>
                        </div> 
                        
                         <?php  
                            $totalRows = count($rsMarketplaceDetail); 

                            for ($i=0;$i<=$totalRows; $i++){  

                                $class =  'transaction-detail-row';
                                $overwrite = true;
                                $disable = '';  
                                $readonly = false;

                                if ($i == $totalRows ){
                                    $class = 'marketplace-row-template row-template';
                                    $overwrite = false; 
                                    $disable = 'disabled="disabled"';
                                } else {  

                                    $_POST['hidDetailMarketplaceKey[]'] =  $rsMarketplaceDetail[$i]['pkey'];
                                    $_POST['hidMarketplaceKey[]'] =  $rsMarketplaceDetail[$i]['marketplacekey']; 
                                    $_POST['marketplaceName[]'] =  $rsMarketplaceDetail[$i]['marketplacename']; 
                                } 

                        ?>
 
                        <div class="div-table-row <?php echo $class; ?>"> 
                            <div class="div-table-col detail-col-detail">
                                <?php echo $obj->inputHidden('hidDetailMarketplaceKey[]',array('overwritePost' => $overwrite, 'disabled' =>  $disable)); ?>
                                <?php echo $obj->inputHidden('hidMarketplaceKey[]',array('overwritePost' => $overwrite, 'disabled' =>  $disable)); ?>
                                <?php echo $obj->inputText('marketplaceName[]',array('overwritePost' => $overwrite, 'disabled' =>  $disable)); ?>
                            </div>   
                            
                            <div class="div-table-col detail-col-detail  <?php echo $obj->hideOnDisabled(); ?>  icon-col"><?php echo (!$readonly) ? $obj->inputLinkButton('btnAddDetailRow' , '<i class="fas fa-plus-circle"></i>', array('class' => 'btn btn-link add-row-button', 'etc' => 'attr-template="marketplace-row-template"')) : ''; ?></div>
                            <div class="div-table-col detail-col-detail  <?php echo $obj->hideOnDisabled(); ?>  icon-col"><?php echo (!$readonly) ? $obj->inputLinkButton('btnDeleteSalesRows' , '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button', 'etc' => 'tabIndex="-1"')) : ''; ?></div>
                      </div>
                    <?php } ?>
 
                        
                    </div>  
                </div> 
                
                <div class="div-table-col-5" style="vertical-align:top; width: 33.33%;  padding-bottom:1.5em">  </div>
                <div class="div-table-col-5" style="vertical-align:top; width: 33.33%;  padding-bottom:1.5em">  </div>
            </div>
            <div class="div-table-row">
                <div class="div-table-col-5" style="vertical-align:top; width: 33.33%;">  
                    <div class="div-table brand-criteria mnv-transaction transaction-detail" style="width:100%;">
                        <div class="div-table-caption" style="border:0;  font-size: 1.2em; font-weight:bold">
                            <?php echo strtoupper($obj->lang['brand']); ?> 
                            <div style="font-weight:normal"><?php echo $obj->inputCheckBox('chkAllBrand') . ' ' . $obj->lang['allBrands']; ?></div>
                        </div>  
                        
                         <?php  
                            $totalRows = count($rsBrandDetail); 

                            for ($i=0;$i<=$totalRows; $i++){  

                                $class =  'transaction-detail-row';
                                $overwrite = true;
                                $disable = '';  
                                $readonly = false;

                                if ($i == $totalRows ){
                                    $class = 'brand-row-template row-template';
                                    $overwrite = false; 
                                    $disable = 'disabled="disabled"';
                                } else {  

                                    $_POST['hidDetailBrandKey[]'] =  $rsBrandDetail[$i]['pkey'];
                                    $_POST['hidBrandKey[]'] =  $rsBrandDetail[$i]['brandkey']; 
                                    $_POST['brandName[]'] =  $rsBrandDetail[$i]['brandname']; 
                                } 

                        ?>

 
                        <div class="div-table-row <?php echo $class; ?>"> 
                            <div class="div-table-col detail-col-detail">
                                <?php echo $obj->inputHidden('hidDetailBrandKey[]',array('overwritePost' => $overwrite, 'disabled' =>  $disable)); ?>
                                <?php echo $obj->inputHidden('hidBrandKey[]',array('overwritePost' => $overwrite, 'disabled' =>  $disable)); ?>
                                <?php echo $obj->inputText('brandName[]',array('overwritePost' => $overwrite, 'disabled' =>  $disable)); ?>
                            </div>   
                            
                            <div class="div-table-col detail-col-detail  <?php echo $obj->hideOnDisabled(); ?>  icon-col"><?php echo (!$readonly) ? $obj->inputLinkButton('btnAddDetailRow' , '<i class="fas fa-plus-circle"></i>', array('class' => 'btn btn-link add-row-button', 'etc' => 'attr-template="brand-row-template"')) : ''; ?></div>
                            <div class="div-table-col detail-col-detail  <?php echo $obj->hideOnDisabled(); ?>  icon-col"><?php echo (!$readonly) ? $obj->inputLinkButton('btnDeleteSalesRows' , '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button', 'etc' => 'tabIndex="-1"')) : ''; ?></div>
                      </div>
                    <?php } ?> 
                        
                    </div>  
                </div> 
                <div class="div-table-col-5" style="vertical-align:top;  width: 33.33%"> 
                    <div class="div-table item-category-criteria mnv-transaction transaction-detail" style="width:100%;">
                        <div class="div-table-caption"  style="border:0; font-size: 1.2em; font-weight:bold">
                            <?php echo strtoupper($obj->lang['itemCategory']); ?> 
                            <div style="font-weight:normal"><?php echo $obj->inputCheckBox('chkAllItemCategory') . ' ' . $obj->lang['allCategories']; ?></div>
                        </div> 
                        
                         <?php  
                            $totalRows = count($rsItemCategoryDetail); 

                            for ($i=0;$i<=$totalRows; $i++){  

                                $class =  'transaction-detail-row';
                                $overwrite = true;
                                $disable = '';  
                                $readonly = false;

                                if ($i == $totalRows ){
                                    $class = 'category-row-template row-template';
                                    $overwrite = false; 
                                    $disable = 'disabled="disabled"';
                                } else {  

                                    $_POST['hidDetailCategoryKey[]'] =  $rsItemCategoryDetail[$i]['pkey'];
                                    $_POST['hidCategoryKey[]'] =  $rsItemCategoryDetail[$i]['categorykey']; 
                                    $_POST['categoryName[]'] =  $rsItemCategoryDetail[$i]['categoryname'];

                                } 

                        ?>
 
                        <div class="div-table-row <?php echo $class; ?>"> 
                            <div class="div-table-col detail-col-detail">
                                <?php echo $obj->inputHidden('hidDetailCategoryKey[]',array('overwritePost' => $overwrite, 'disabled' =>  $disable)); ?>
                                <?php echo $obj->inputHidden('hidCategoryKey[]',array('overwritePost' => $overwrite, 'disabled' =>  $disable)); ?>
                                <?php echo $obj->inputText('categoryName[]',array('overwritePost' => $overwrite, 'disabled' =>  $disable)); ?>
                            </div>   
                            <div class="div-table-col detail-col-detail  <?php echo $obj->hideOnDisabled(); ?>  icon-col"><?php echo (!$readonly) ? $obj->inputLinkButton('btnAddDetailRow' , '<i class="fas fa-plus-circle"></i>', array('class' => 'btn btn-link add-row-button', 'etc' => 'attr-template="category-row-template"')) : ''; ?></div>
                            <div class="div-table-col detail-col-detail  <?php echo $obj->hideOnDisabled(); ?>  icon-col"><?php echo (!$readonly) ? $obj->inputLinkButton('btnDeleteSalesRows' , '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button', 'etc' => 'tabIndex="-1"')) : ''; ?></div>
                       </div>
                        <?php } ?> 
 
                    </div>    
                </div> 
                <div class="div-table-col-5" style="vertical-align:top;  width: 33.33%"> 
                    <div class="div-table item-criteria mnv-transaction transaction-detail" style="width:100%;">
                        <div class="div-table-caption"  style="border:0; font-size: 1.2em;font-weight:bold">
                            <?php echo strtoupper($obj->lang['item']); ?> 
                            <div style="font-weight:normal"><?php echo $obj->inputCheckBox('chkAllItem') . ' ' . $obj->lang['allProducts']; ?></div>
                        </div> 
                        
                         <?php  
                            $totalRows = count($rsItemDetail); 
 
                            for ($i=0;$i<=$totalRows; $i++){  

                                $class =  'transaction-detail-row';
                                $overwrite = true;
                                $disable = '';  
                                $readonly = false;

                                if ($i == $totalRows ){
                                    $class = 'item-row-template row-template';
                                    $overwrite = false; 
                                    $disable = 'disabled="disabled"';
                                } else {  

                                    $_POST['hidDetailItemKey[]'] =  $rsItemDetail[$i]['pkey'];
                                    $_POST['hidItemKey[]'] =  $rsItemDetail[$i]['itemkey']; 
                                    $_POST['itemName[]'] =  $rsItemDetail[$i]['itemname'];

                                } 

                        ?>
 
                        <div class="div-table-row <?php echo $class; ?>"> 
                            <div class="div-table-col detail-col-detail">
                                <?php echo $obj->inputHidden('hidDetailItemKey[]',array('overwritePost' => $overwrite, 'disabled' =>  $disable)); ?>
                                <?php echo $obj->inputHidden('hidItemKey[]',array('overwritePost' => $overwrite, 'disabled' =>  $disable)); ?>
                                <?php echo $obj->inputText('itemName[]',array('overwritePost' => $overwrite, 'disabled' =>  $disable)); ?>
                            </div>   
                            <div class="div-table-col detail-col-detail  <?php echo $obj->hideOnDisabled(); ?>  icon-col"><?php echo (!$readonly) ? $obj->inputLinkButton('btnAddDetailRow' , '<i class="fas fa-plus-circle"></i>', array('class' => 'btn btn-link add-row-button', 'etc' => 'attr-template="item-row-template"')) : ''; ?></div>
                            <div class="div-table-col detail-col-detail  <?php echo $obj->hideOnDisabled(); ?>  icon-col"><?php echo (!$readonly) ? $obj->inputLinkButton('btnDeleteSalesRows' , '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button', 'etc' => 'tabIndex="-1"')) : ''; ?></div>
                       </div>
                       <?php } ?> 
                        
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
