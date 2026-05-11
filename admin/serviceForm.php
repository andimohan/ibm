<?php 
require_once '../_config.php';  
require_once '../_include-v2.php'; 

includeClass(array('Service.class.php','VatOut.class.php'));
$service = createObjAndAddToCol(new Service(SERVICE)); 
$serviceCategory = createObjAndAddToCol(new ServiceCategory()); 
$timeUnit = createObjAndAddToCol(new TimeUnit()); 
$vatOut = createObjAndAddToCol(new VatOut()); 
    
$isActiveModule = $class->isActiveModule(array('ChartOfAccount','TruckingServiceOrderCategory','Amortization'));

if($isActiveModule['chartofaccount']) $chartOfAccount = createObjAndAddToCol(new ChartOfAccount()); 
 
$hasEMKLModule=false;
$showEximServices= 2;
if($class->isActiveModule('EMKLJobOrder')){
	$hasEMKLModule=true;
	includeClass(array('EMKLJobOrder.class.php'));
	$emklJobOrder = createObjAndAddToCol(new EMKLJobOrder());	
	$showEximServices = $class->loadSetting('splitCOAByJobCategory');
}

$obj= $service;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true));
    

$formAction = 'serviceList';
 
$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false;

$showItemImage = $obj->loadSetting('showItemImage');
$metaOptions = $obj->loadSetting('metaOptions');
$rsItemDescription = array();  
$rsIconImage = array();   
$rsItemImage = array();
$rsTimeConversion = array();   

$rs = prepareOnLoadData($obj); 

$editUnitInactiveCriteria = '';
	
$allowChangeUnit = ''; 
$showMultiTimeUnit = $class->loadSetting('showMultiTimeUnit');

$_POST['chkAllowMultiplePurchase']  = 1;

if (!empty($_GET['id'])){ 
	$id = $_GET['id'];	
   
 
    $rsItemDescription = $obj->getItemDescription($id);	
    $rsTimeConversion = $obj->getTimeDetail($id);
    $_POST['hidCategoryKey'] = $rs[0]['categorykey']; 
    if (!empty($rs[0]['categorykey'])){
		$rsCategory = $serviceCategory->getDataRowById($rs[0]['categorykey']);
        $categoryName =  $serviceCategory->getPath($rsCategory[0]['pkey']);
		$_POST['categoryName'] = $categoryName[0]['path'];
	}
    
    $rsServiceCode = $vatOut->getTaxServiceCode(' and pkey = ' . $obj->oDbCon->paramString($rs[0]['taxservicecodekey']));
    $_POST['taxServiceCode'] = $rsServiceCode[0]['code'];    
    
    $rsServiceUnit = $vatOut->getTaxServiceUnit(' and pkey = ' . $obj->oDbCon->paramString($rs[0]['taxserviceunitkey']));
    $_POST['taxServiceUnit'] = $rsServiceUnit[0]['code'];   
    
 
    if($isActiveModule['chartofaccount']){
        $_POST['hidRevenueCOAKey'] = $rs[0]['revenuecoakey']; 
        if (!empty($rs[0]['revenuecoakey'])){
            $rsCoa = $chartOfAccount->getDataRowById($rs[0]['revenuecoakey']);
            $_POST['revenueCOALink'] = $rsCoa[0]['code'] . ' - ' . $rsCoa[0]['name'];
        }

        $_POST['hidCostCOAKey'] = $rs[0]['costcoakey']; 
        if (!empty($rs[0]['costcoakey'])){
            $rsCoa = $chartOfAccount->getDataRowById($rs[0]['costcoakey']);
            $_POST['costCOALink'] = $rsCoa[0]['code'] . ' - ' . $rsCoa[0]['name'];
        }

        $_POST['hidPrepaidExpenseCOAKey'] = $rs[0]['prepaidexpensecoakey']; 
        if (!empty($rs[0]['prepaidexpensecoakey'])){
            $rsCoa = $chartOfAccount->getDataRowById($rs[0]['prepaidexpensecoakey']);
            $_POST['prepaidExpenseCOALink'] = $rsCoa[0]['code'] . ' - ' . $rsCoa[0]['name'];
        }

        $_POST['hidARAPReimburseCOAKey'] = $rs[0]['arapreimbursecoakey']; 
        if (!empty($rs[0]['arapreimbursecoakey'])){
            $rsCoa = $chartOfAccount->getDataRowById($rs[0]['arapreimbursecoakey']);
            $_POST['ARAPReimburseCOALink'] = $rsCoa[0]['code'] . ' - ' . $rsCoa[0]['name'];
        }
    } 


    if($hasEMKLModule && $showEximServices == 1)
        $rsCostCOAKey = $obj->getCostCOADetail($id);   
    
	$_POST['chkAllowMultiplePurchase'] = $rs[0]['allowmultiplepurchase'];
 
	$_POST['metaTitle'] = $rs[0]['metatitle'];
	$_POST['metaDescription'] = $rs[0]['metadescription'];
	
    $_POST['txtDetail'] = $obj->HTMLSpecialCharacterForEditor($_POST['txtDetail']);
        
	if($showItemImage){ 
        //update image 
        $rsItemImage = $obj->getItemImage($id);

        for($i=0; $i< count($rsItemImage); $i++)  
            $rsItemImage[$i]['phpthumbhash'] = getPHPThumbHash($rsItemImage[$i]['file']);
 
        if(count($rsItemImage) > 0){
            $sourcePath = $obj->defaultDocUploadPath.$obj->uploadFolder.$id;
            $destinationPath = $obj->uploadTempDoc.$obj->uploadFolder.$id; 
            $obj->deleteAll($destinationPath); 

            if(!is_dir($destinationPath)) 
                mkdir ($destinationPath,  0755, true);

            $obj->fullCopy($sourcePath,$destinationPath);  
        }

        if( !empty($rs[0]['iconimage'])){
            $rsIconImage[0]['file'] =  $rs[0]['iconimage'];
            $rsIconImage[0]['phpthumbhash'] = getPHPThumbHash($rsIconImage[0]['file']);

            $sourcePath = $obj->defaultDocUploadPath.$obj->uploadIconFolder.$id;
            $destinationPath = $obj->uploadTempDoc.$obj->uploadIconFolder.$id; 
            $obj->deleteAll($destinationPath); 

            if(!is_dir($destinationPath)) 
                mkdir ($destinationPath,  0755, true);

            $obj->fullCopy($sourcePath,$destinationPath); 
        }

    }
	   
} 

$arrStatus = $obj->convertForCombobox($obj->getAllStatus(),'pkey','status');      
$arrUnit = $class->convertForCombobox($timeUnit->searchData('','',true, ' and ('.$timeUnit->tableName.'.statuskey = 1 ' . $editUnitInactiveCriteria. ')'),'pkey','name'); 
$arrJobType = array();
$arrJobType[1] = 'Import';
$arrJobType[2] = 'Export';
$arrJobType[3] = 'Domestic';
    
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!--<style>
    .item-icon-uploader .image-list {padding: 0; margin: 0; list-style: none; border:1px solid #dedede}
</style>-->
<title></title>  

<script type="text/javascript"> 
      var tabID = <?php echo ($isQuickAdd) ?  $_GET['tabID'] :  'selectedTab.newPanel[0].id';  ?>  
              
        var opt = {};
    
        opt.showItemImage = <?php echo ($showItemImage) ? 'true' : 'false';?>;
        
        opt.uploadImageFolder = "<?php echo $obj->uploadFolder; ?>";
        opt.imageUploaderTarget = "image-uploader"; 
        opt.arrImage = <?php echo json_encode(array_column($rsItemImage,'file')); ?>;  
        opt.arrPHPThumbHash = <?php echo json_encode(array_column($rsItemImage,'phpthumbhash')); ?>;  

        opt.uploadIconFolder = "<?php echo $obj->uploadIconFolder; ?>";
        opt.iconUploaderTarget = "icon-uploader"; 
        opt.arrIcon = <?php echo json_encode( (!empty($rsIconImage)) ? array_column($rsIconImage,'file') : array() ); ?>;  
        opt.arrIconPHPThumbHash =<?php echo json_encode( (!empty($rsIconImage)) ? array_column($rsIconImage,'phpthumbhash')  : array() ); ?>;  

        var service = new Service(tabID,opt);
    
        prepareHandler(service);   
        
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

                                    categoryName: { 
                                        validators: {
                                            notEmpty: {
                                                message: phpErrorMsg.category[1]
                                            },  
                                        }
                                    }, 

                                    sellingPrice: {
                                        validators: { 
                                            greaterThan: {
                                                value: -1,
                                                inclusive: false,
                                                separator: ',', 
                                                message: phpErrorMsg.sellingPrice[2]
                                            }, 
                                        }
                                    }, 
                                } ; 
 
        setFormValidation(getTabObj(tabID), $('#defaultForm-' + tabID), fieldValidation, <?php echo json_encode($obj->validationFormSubmitParam()); ?>  );
  	 
</script>

</head> 

<body> 

<div style="width:100%; margin:auto; " class="tab-panel-form">   
  <div class="notification-msg"></div>
  
  <form id="defaultForm" method="post" class="form-horizontal" action="<?php echo $formAction; ?>">
     <?php prepareOnLoadDataForm($obj); ?>   
     <?php echo $obj->generateLangOptions(); ?> 
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
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['serviceName']); ?></label> 
                                        <div class="col-xs-9"> 
                                             <?php echo $obj->inputText('name', array('multilang' => true)); ?>
                                        </div> 
                                    </div>
									<?php if ($metaOptions == 1){ ?> 
										<div class="form-group text-muted">
											<label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['metaTitle']); ?></label> 
											<div class="col-xs-9"> 
												 <?php echo $obj->inputText('metaTitle'); ?>
											</div> 
										</div>
									<?php } ?> 
								
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['alias']); ?></label> 
                                        <div class="col-xs-9"> 
                                             <?php echo $obj->inputText('aliasName', array('multilang' => true)); ?>
                                        </div> 
                                    </div>   
                                      <div class="form-group">
                                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['category']); ?></label> 
                                             <div class="col-xs-9">  
                                               <?php    
                                                echo $obj->inputAutoComplete(array(
                                                                                    'objRefer' => $serviceCategory,
                                                                                    'revalidateField' => true, 
                                                                                    'element' => array('value' => 'categoryName',
                                                                                                       'key' => 'hidCategoryKey'),
                                                                                    'source' =>array(
                                                                                                        'url' => 'ajax-service-category.php',
                                                                                                        'data' => array(  'action' =>'searchData', 'isleaf' => 1 )
                                                                                                    ) ,
                                                                                    'popupForm' => array(
                                                                                                            'url' => 'serviceCategoryForm.php',
                                                                                                            'element' => array('value' => 'categoryName',
                                                                                                                   'key' => 'hidCategoryKey'),
                                                                                                            'width' => '600px',
                                                                                                            'title' => ucwords($obj->lang['add'] . ' - ' . $obj->lang['serviceCategory'])
                                                                                                        )
                                                                                  )
                                                                            );  
                                                ?> 
                                            </div> 
                                        </div>
                                
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['order']); ?></label> 
                                        <div class="col-xs-9"> 
                                             <?php echo $obj->inputNumber('orderList'); ?>
                                        </div> 
                                    </div>   
                              
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['sellingPrice']); ?></label> 
                                        <div class="col-xs-9"> 
                                                 <?php echo $obj->inputNumber('sellingPrice'); ?>
                                        </div> 
                                    </div> 

	                           <?php if ($showMultiTimeUnit) { ?>
									 <div class="form-group">
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['rentPrice']); ?></label> 
                                    <div class="col-xs-9">  
                                            <div class="div-table mnv-transaction transaction-detail">
                                        <?php 
                                            $totalRows = count($rsTimeConversion);
                                            for ($i=0;$i<=$totalRows; $i++){ 
                                                
                                                $class =  'transaction-detail-row';
                                                $overwrite = true;
                                                $readonly = false;
                                                $disabled = false; 
                                                //$style = '';

                                                if ($i == $totalRows ){
                                                    $class = 'detail-row-template row-template';
                                                    $overwrite = false;
                                                    $disabled = true; 
                                                    $isLocked = false;
                                                    //$style = 'style="display:none"';
                                                } else{ 
//                                                    $_POST['hidDetailKey[]'] =  $rsTimeConversion[$i]['pkey'];
                                                    $_POST['hidTimeDetailKey[]'] =  $rsTimeConversion[$i]['pkey'];
                                                    $_POST['selTimeUnitKey[]'] =  $rsTimeConversion[$i]['timeunitkey'];
                                                    $_POST['unitSellingPrice[]'] =  $obj->formatNumber($rsTimeConversion[$i]['sellingprice']);

                                                
                                                }
                                                $hideDeleteIcon = '';  
                                            ?>
                                            <div class="div-table-row <?php echo $class; ?> odd-style-adjustment"> 
                                                <div class="div-table-col"> 
                                                    <div class="flex">     
                                                        <div class="consume">
                                                            <?php echo $obj->inputHidden('hidTimeDetailKey[]',array('overwritePost' => $overwrite, 'readonly' => $readonly, 'disabled' => $disabled)); ?>
                                                            <?php echo $obj->inputNumber('unitSellingPrice[]', array('overwritePost' => $overwrite ,'readonly' => $readonly, 'disabled' => $disabled )); ?>
                                                        </div>
                                                        <div style="width:150px;">
                                                            <?php echo $obj->inputSelect('selTimeUnitKey[]', $arrUnit, array('overwritePost' => $overwrite, 'readonly' => $readonly, 'disabled' => $disabled )); ?>
                                                        </div>
                                                        <div class="icon-col <?php echo $obj->hideOnDisabled(); ?>"><?php echo $obj->inputLinkButton('btnAddDetailRow' , '<i class="fas fa-plus-circle"></i>', array('class' => 'btn btn-link add-row-button', 'etc' => 'attr-template="detail-row-template"')); ?></div>
                                                        <div class="icon-col <?php echo $obj->hideOnDisabled(); ?>"><?php echo $obj->inputLinkButton('btnDeleteRows' , '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button', 'etc' =>  'tabIndex="-1" style="padding:6px 0; '.$hideDeleteIcon.'"')); ?></div>
 
                                                    </div> 
                                                </div> 
                                            </div>   
                                        <?php }	 ?>  
                                        
                                    </div>
                                    </div> 
                                </div>
								<?php } ?>
                                  
                          
                                    <?php  if ( in_array(PLAN_TYPE['categorykey'], array(COMPANY_TYPE['trucking'],COMPANY_TYPE['forwarding'])) ) {  ?> 
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['container']); ?></label> 
                                        <div class="col-xs-1"> 
                                                 <?php echo $obj->inputCheckBox('chkIsContainer'); ?>
                                        </div>   
                                    </div>   

                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['reimburse']); ?></label>
                                        <div class="col-xs-1">
                                            <?php echo $obj->inputCheckBox('chkIsReimburse'); ?>
                                        </div>
                                    </div>
								
                                   <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['multiplePurchase']); ?></label> 
                                        <div class="col-xs-9" > 
                                             <?php echo $obj->inputCheckBox('chkAllowMultiplePurchase'); ?>  
                                        </div>  
                                    </div>
                                    <?php } ?>

						</div> 
                       
                    </div>  
                    <div class="div-table-col">  
                            <div class="div-tab-panel"> 
								<div class="div-table-caption border-green"><?php echo ucwords($obj->lang['shortDescription']); ?></div>  
								<div class="form-group">
									<div class="col-xs-12">  
										   <?php echo  $obj->inputTextArea('shortdescription',array('multilang' => true, 'etc'=>'style="height:10em;"')); ?>
									</div> 
								</div>  
								<?php if ($metaOptions == 1){ ?> 
									<div class="form-group text-muted"> 
										<div class="col-xs-12"> 
											 <div><?php echo ucwords($obj->lang['metaDescription']); ?></div>
											 <?php echo $obj->inputTextArea('metaDescription', array( 'etc' => 'style="height:8em;"')); ?>
										</div> 
									</div>
								<?php } ?> 
                            </div>  


							<?php if ($showItemImage) { ?> 
							 <div class="div-tab-panel"> 
									<div class="div-table-caption border-blue"><?php echo ucwords($obj->lang['icon']); ?></div> 
									 <div class="div-table-row"> 
										<div class="div-table-col-5">
										  <!-- image uploader --> 
											<div class="item-image-uploader icon-uploader">
												<ul class="image-list"></ul>
												<div style="clear:both; height:1em;"></div>
												<div class="file-uploader">	
													<noscript>			
													<p>Please enable JavaScript to use file uploader.</p> 
													</noscript> 
												</div>
											  </div>  
											<!-- image uploader --> 
										</div> 
								   </div> 
							 </div>   
							 <div class="div-tab-panel"> 
									<div class="div-table-caption border-black"><?php echo ucwords($obj->lang['image']); ?></div> 
									 <div class="div-table-row"> 
										<div class="div-table-col-5">
										  <!-- image uploader --> 
											<div class="item-image-uploader  image-uploader">
												<ul class="image-list"></ul>
												<div style="clear:both; height:1em;"></div>
												<div class="file-uploader">	
													<noscript>			
													<p>Please enable JavaScript to use file uploader.</p> 
													</noscript> 
												</div>
											  </div>  
											<!-- image uploader --> 
										</div> 
								   </div> 
							 </div>   

						   <?php } ?>

						
				    </div>
             </div>   
       </div>                  
      
      
        <div class="div-table main-tab-table-2">
            <div class="div-table-row">
            <div class="div-table-col">  
                <div class="div-tab-panel">  
                <div class="div-table-caption border-pink"><?php echo ucwords($obj->lang['finance']); ?></div>
<!--                    <div class="col-xs-12 section-title-h2" style="border:0"><?php echo ucwords($obj->lang['generalTransaction']); ?></div>    -->
                      
                        <div class="form-group" >
                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['taxServiceCode']); ?></label> 
                                <div class="col-xs-9"> 
                                        <?php 
                                               echo $obj->inputAutoComplete(array( 
                                                                    'element' => array('value' => 'taxServiceCode',
                                                                                       'key' => 'hidTaxServiceCodeKey'),
                                                                    'source' =>array(
                                                                                        'url' => 'ajax-vat-out.php',
                                                                                        'data' => array(  'action' =>'getTaxServiceCode' )
                                                                                    )  
                                                                    )); 
                                     ?>    


                                </div> 
				            </div>
                            <div class="form-group" >
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['taxServiceUnit']); ?></label> 
                                            <div class="col-xs-9"> 
                                                    <?php 
                                                           echo $obj->inputAutoComplete(array( 
                                                                                'element' => array('value' => 'taxServiceUnit',
                                                                                                   'key' => 'hidTaxServiceUnitKey'),
                                                                                'source' =>array(
                                                                                                    'url' => 'ajax-vat-out.php',
                                                                                                    'data' => array(  'action' =>'getTaxServiceUnit' )
                                                                                                )  
                                                                                )); 
                                                 ?>    


                                            </div> 
				            </div> 
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['tax23']); ?></label> 
                                <div class="col-xs-9" > 
                                     <?php echo $obj->inputCheckBox('chkIsTax23'); ?>  
                                </div>  
                            </div>

                        <?php if ($isActiveModule['amortization']){ ?> 
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['amortization']); ?></label> 
                                <div class="col-xs-9" > 
                                    <div class="flex">
                                        <div><?php echo $obj->inputCheckBox('chkIsAmortized'); ?>  </div>
                                        <div style="padding-left: 2em; "><?php echo $obj->inputNumber('amortizationAging', array('etc' => 'style="width: 8em; text-align:right"')); ?>  </div>
                                        <div class="consume"><?php echo $obj->lang['month']; ?>  </div>
                                    </div> 
                                </div>  
                            </div>

                        <?php } ?>
                    
                    
                        <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['revenueAccount']); ?></label> 
                            <div class="col-xs-9">  
                                <?php 
                                           echo $obj->inputAutoComplete(array( 
                                                                'element' => array('value' => 'revenueCOALink',
                                                                                   'key' => 'hidRevenueCOAKey'),
                                                                'source' =>array(
                                                                                    'url' => 'ajax-coa.php',
                                                                                    'data' => array(  'action' =>'searchData' )
                                                                                )  
                                                                )); 
                                 ?>    
                            </div> 
                        </div>
                    <!-- kepake utk amortisasi jg -->
                    <div class="form-group">
                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['prepaidCost']); ?></label> 
                        <div class="col-xs-9">  
                            <?php 
                                       echo $obj->inputAutoComplete(array( 
                                                            'element' => array('value' => 'prepaidExpenseCOALink',
                                                                               'key' => 'hidPrepaidExpenseCOAKey'),
                                                            'source' =>array(
                                                                                'url' => 'ajax-coa.php',
                                                                                'data' => array(  'action' =>'searchData' )
                                                                            )  
                                                            )); 
                             ?>    
                        </div> 
                    </div>
<!--nanti baru diupdate
                    <div class="form-group">
                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['ARAPReimburse']); ?></label> 
                        <div class="col-xs-9">  
                            <?php 
                                       echo $obj->inputAutoComplete(array( 
                                                            'element' => array('value' => 'ARAPReimburseCOALink',
                                                                               'key' => 'hidARAPReimburseCOAKey'),
                                                            'source' =>array(
                                                                                'url' => 'ajax-coa.php',
                                                                                'data' => array(  'action' =>'searchData' )
                                                                            )  
                                                            )); 
                             ?>    
                        </div> 
                    </div>
--> 
                     <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['costAccount']); ?></label> 
                            <div class="col-xs-9">  
                                <?php 
                                           echo $obj->inputAutoComplete(array( 
                                                                'element' => array('value' => 'costCOALink',
                                                                                   'key' => 'hidCostCOAKey'),
                                                                'source' =>array(
                                                                                    'url' => 'ajax-coa.php',
                                                                                    'data' => array(  'action' =>'searchData' )
                                                                                )  
                                                                )); 
                                 ?>    
                            </div> 
                        </div>
                    </div>
            </div>
             <div class="div-table-col">  

            </div>
        </div>
        </div>
      
      
              <?php if (USE_GL) { ?>
							<?php if ($hasEMKLModule && $showEximServices == 1) { ?> 
							<div class="div-table main-tab-table-3" style="width:100%"> 
								 
									<div class="div-table-row">
								  
									<?php 
										   $rsContainer = $emklJobOrder->getLoadContainer();

										   $arrCostType = array(
												'1' => $obj->lang['revenueAccount'],
												'2' => $obj->lang['prepaidCost'], 
												'3' => $obj->lang['costAccount'], 
												'4' => $obj->lang['ARAPReimburse'], 
											);

											$costCOAByType = array();
											$arrTempCOA = array(); 

											if($isActiveModule['chartofaccount'] && !empty($rsCostCOAKey)){
												$costCOAByType = $obj->reindexDetailCollections($rsCostCOAKey,'eximkey'); 
												$arrTempCOA = $chartOfAccount->searchDataRow(array('pkey','code','name'),' and '.$chartOfAccount->tableName.'.pkey in ('.$obj->oDbCon->paramString(array_column($rsCostCOAKey,'coakey'),',').') ');
												$arrTempCOA = array_column($arrTempCOA,null,'pkey');
											} 

											foreach($arrJobType as $jobtypekey => $jobtyperow){

												echo '
												<div class="div-table-col" style="padding-right:10px">  
												<div class="div-tab-panel" style="min-width:330px">';
												echo '<div class="col-xs-12 section-title-h2" style="border:0">'.ucwords($jobtyperow).'</div>'; 

												foreach($rsContainer as $containerRow){

													$hidJobType = $obj->inputHidden('hidEximKey[]', array('value' => $jobtypekey)); 

													$containerkey = $containerRow['pkey'];

													echo '<div class="col-xs-12 section-title">'.ucwords($containerRow['name']).'</div>'; 


													foreach($arrCostType as $typekey => $typeRow){
 															$COAIndexKey = $containerkey.'-'.$typekey;
														
															$arrCostCOAKey = array();
															if(!empty($rsCostCOAKey)){  
																$arrCostCOAKey = $costCOAByType[$jobtypekey];
																$arrCostCOAKey = array_column($arrCostCOAKey,null,'categoryandtypekey');
															}

															$_POST['hidCostCOADetailKey[]']  = '';
															$_POST['hidCostCOAKeyDetail[]']  = '';
															$_POST['costCOALinkDetail[]']  = '';

															if(!empty($arrCostCOAKey[$COAIndexKey]) ){

																$_POST['hidCostCOADetailKey[]'] = $arrCostCOAKey[$COAIndexKey]['pkey']; 
																$_POST['hidCostCOAKeyDetail[]'] = $arrCostCOAKey[$COAIndexKey]['coakey'];
																$_POST['costCOALinkDetail[]'] = (!empty($arrTempCOA[$arrCostCOAKey[$COAIndexKey]['coakey']]) ) ? $arrTempCOA[$arrCostCOAKey[$COAIndexKey]['coakey']]['code'] .' - '. $arrTempCOA[$arrCostCOAKey[$COAIndexKey]['coakey']]['name'] : '' ;
															} 

															$hidDetailKey = $obj->inputHidden('hidCostCOADetailKey[]'); 
															$typeKeyInput = $obj->inputHidden('typeKeyCOA[]',array( 'value' => $typekey)); 
															$hidContainer = $obj->inputHidden('categoryKeyCOA[]', array('value' => $containerkey)); 

															$coaLinkInput = $obj->inputAutoComplete(array(  
																									'element' => array('value' => 'costCOALinkDetail[]', 'key' => 'hidCostCOAKeyDetail[]'),
																									'source' =>array(  'url' => 'ajax-coa.php', 'data' => array(  'action' =>'searchData' ) )  
																									)); 

													echo '     
														<div class="form-group">
															<label class="col-xs-3 control-label">'.$hidDetailKey.$hidJobType.$hidContainer.$typeKeyInput.$typeRow.'</label> 
															<div class="col-xs-9">'.$coaLinkInput.'</div> 
														</div>';
													  }

												}

												echo '</div>';
												echo '</div>';
											}

										?>
									</div> 
	  						</div> 
							<?php }  ?> 
		      <?php } ?>
      
      
        <?php if (PLAN_TYPE['usefrontend'] == 1) {  ?>    
        <div class="div-table main-tab-table-1" style="width:100%;">
              <div class="div-table-row">
                    <div class="div-table-col">  
                         <div class="div-tab-panel">  
                             <div class="div-table-caption border-orange"><?php echo ucwords($obj->lang['description']); ?></div>
                            <div class="form-group">
                                 <div class="col-xs-12">  
                                     <?php echo  $obj->inputEditor('txtDetail',array('multilang' => true )); ?> 
                                 </div>
                            </div>
                        </div>
                  </div>
            </div>
        </div>  
        <?php } ?>
	  
        <div class="form-button-panel" > 
       	 <?php echo $obj->generateSaveButton(); ?> 
        </div>  
    </form>  
     <?php echo $obj->showDataHistory(); ?> 
</div> 
</body>

</html>
