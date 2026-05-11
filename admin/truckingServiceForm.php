<?php 
require_once '../_config.php';  
require_once '../_include-v2.php'; 

includeClass(array('Service.class.php','VatOut.class.php'));
$truckingService = createObjAndAddToCol( new Service());  
$chartOfAccount = createObjAndAddToCol( new ChartOfAccount());  
$serviceCategory = createObjAndAddToCol( new ServiceCategory()); 
$truckingServiceOrderCategory = createObjAndAddToCol( new TruckingServiceOrderCategory());  
$vatOut = createObjAndAddToCol(new VatOut()); 

$obj= $truckingService;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true));
     
$formAction = 'truckingServiceList';
 
$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false;

$truckingCostCOAType = $obj->loadSetting('truckingCostCOAType');

$rsItemDescription = array();  
$rsCostCOAKey = array();

$rs = prepareOnLoadData($obj); 

$allowChangeUnit = '';

$usePPNDetail = $obj->loadSetting('usePPNDetail');
 
if (!empty($_GET['id'])){ 
	$id = $_GET['id'];	
   
 	$_POST['sellingPrice'] = $obj->formatNumber($rs[0]['sellingprice']);
	$_POST['shortdescription'] = $rs[0]['shortdescription'];
	$_POST['volume'] = $obj->formatNumber($rs[0]['volume'],-2);
 	$_POST['qtyCombo'] = $obj->formatNumber($rs[0]['qty'],-2);
 	$_POST['taxPercentage'] = $obj->formatNumber($rs[0]['taxpercentage']);
 
    $rsItemDescription = $obj->getItemDescription($id);
     
     if($truckingCostCOAType == 2) { 
        $rsCostCOAKey = $obj->getCostCOADetail($id); 
    }else{
         
        $_POST['hidRevenueCOAKey'] = $rs[0]['revenuecoakey']; 
          if (!empty($rs[0]['revenuecoakey'])){
            $rsCoa = $chartOfAccount->getDataRowById($rs[0]['revenuecoakey']);
            $_POST['revenueCOALink'] = $rsCoa[0]['code'] . ' - ' . $rsCoa[0]['name'];
        } 
    }
     
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
    
	//update image 
	$rsItemImage = $obj->getItemImage($id);
		
	if(count($rsItemImage) > 0){
		$sourcePath = $obj->defaultDocUploadPath.$obj->uploadFolder.$id;
		$destinationPath = $obj->uploadTempDoc.$obj->uploadFolder.$id; 
		$obj->deleteAll($destinationPath); 
	
		if(!is_dir($destinationPath)) 
			mkdir ($destinationPath,  0755, true);
				
		$obj->fullCopy($sourcePath,$destinationPath);  
	}
	   
} 

$arrStatus = $obj->convertForCombobox($obj->getAllStatus(),'pkey','status');      
  
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title>  

<script type="text/javascript"> 
    function Service(tabID) { 
       
    }
    
	jQuery(document).ready(function(){  
        var tabID = <?php echo ($isQuickAdd) ?  $_GET['tabID'] :  'selectedTab.newPanel[0].id';  ?>  
        itemObj = new Service(tabID);
        setOnDocumentReady(tabID);
        
		/// FILE UPLOADER
		var folder = "<?php echo $obj->uploadFolder; ?>"; 
		var imageUploaderTarget = "item-image-uploader";  
		var arrImage = Array();
		var arrPHPThumbHash = Array(); 
   
		<?php   
			if (isset($id) && !empty($id)){ 
			
				for($i=0;$i<count($rsItemImage);$i++) {
					echo 'arrImage.push("'.$rsItemImage[$i]['file'].'"); '; 
					echo 'arrPHPThumbHash.push("'.getPHPThumbHash($rsItemImage[$i]['file']).'"); '; 
                }
                  	
				echo 'createImageUploader({"tabID":tabID, "name":imageUploaderTarget},{"folder":folder, "token":'.$id.', "arrImage":arrImage,"phpThumbHash":arrPHPThumbHash},true,true);';  
               	 
			}else{
				echo 'createImageUploader({"tabID":tabID, "name":imageUploaderTarget},{"folder":folder},true,true);';
 			}
		?>
        
        
		$( "." + imageUploaderTarget + " .image-list ").sortable({  placeholder: "sortable-placeholder" ,stop: function( event, ui ) { updateItemImageArray({"tabID":tabID, "name":imageUploaderTarget}); }});
		$( "." + imageUploaderTarget + " .image-list"  ).disableSelection();
	 	 
		// DESC FIELD CLONE
		 $("#" + tabID + " [name=btnAddDescription]").on('click', function() { addNewTemplateRow("item-description-row-template"); }); 
 
		// BOOTSTRAP VALIDATOR
		 $('#defaultForm-' + tabID )
			.bootstrapValidator({ 
				feedbackIcons: {
					valid: 'glyphicon glyphicon-ok',
					invalid: 'glyphicon glyphicon-remove',
					validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
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
	           qtyCombo: {
					validators: { 
						greaterThan: {
							value: 0,
							inclusive: false,
							separator: ',', 
							message: phpErrorMsg.qty[1]
						}, 
					}
				},   
                
                 <?php if ($truckingCostCOAType == 2){} else {?> 
                    revenueCOALink: { 
                        validators: {
                            notEmpty: {
                                message: phpErrorMsg.coa[1]
                            },  
                        }
                    },    
                <?php }?>
            }
        })
        .on('success.form.bv', function(e) {   
                 <?php echo $obj->submitFormScript(); ?>
        }); 
         
        <?php if (!isset($rsItemDescription) || empty($rsItemDescription)) {  ?> 
            addNewTemplateRow("item-description-row-template");  
        <?php } ?>
        
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
                                             <?php echo $obj->inputText('name'); ?>
                                        </div> 
                                    </div>
                                    
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['alias']); ?></label> 
                                        <div class="col-xs-9"> 
                                             <?php echo $obj->inputText('aliasName'); ?>
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
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['volume']); ?> / <?php echo ucwords($obj->lang['combo']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <div class="flex">
                                              <div class="consume"><?php echo  $obj->inputNumber('volume'); ?></div>
                                               <div>/</div>
                                              <div class="consume"><?php echo  $obj->inputNumber('qtyCombo',array('value' => 1)); ?></div>
                                            </div>
                                        </div> 
                                    </div>  
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['shortDescription']); ?></label> 
                                        <div class="col-xs-9"> 
                                               <?php echo  $obj->inputTextArea('shortdescription',array('etc'=>'style="height:8em;"')); ?>
                                        </div> 
                                    </div>  
                           </div>
                  
                            <div class="div-tab-panel"> 
                                    <div class="div-table-caption border-pink"><?php echo ucwords($obj->lang['financialInformation']); ?></div>  
                                     <?php if (USE_GL && ( empty($rs[0]['pkey'])  || $security->isAdminLogin('ChartOfAccount',10) )) { ?>
                                    
                                     <?php if ($truckingCostCOAType == 2){
                                        // kalo dipecah per kategori (seperti logol)
                                        $rsCategory = $truckingServiceOrderCategory->searchDataRow( array('pkey','name'), ' and '.$truckingServiceOrderCategory->tableName.'.statuskey = 1');

                                        $arrCostType = array(
                                            //'1' => $obj->lang['costAccount'], // di layanan cuma ad akun pendapatan
                                            '2' => $obj->lang['revenueAccount'], 
                                        );

                                        $costCOAByType = array();
                                        $arrTempCOA = array(); 

                                        if(!empty($rsCostCOAKey)){
                                            $costCOAByType = $obj->reindexDetailCollections($rsCostCOAKey,'typekey'); 
                                            $arrTempCOA = $chartOfAccount->searchDataRow(array('pkey','code','name'),' and '.$chartOfAccount->tableName.'.pkey in ('.$obj->oDbCon->paramString(array_column($rsCostCOAKey,'coakey'),',').') ');
                                            $arrTempCOA = array_column($arrTempCOA,null,'pkey');
                                        }

                                        foreach($arrCostType as $typekey => $typeRow){
                                            $arrCostCOAKey = array();
                                            if(!empty($rsCostCOAKey)){  
                                                $arrCostCOAKey = $costCOAByType[$typekey];
                                                $arrCostCOAKey = array_column($arrCostCOAKey,null,'categorykey');
                                            }

                                            echo '<div class="col-xs-12 section-title">'.ucwords($typeRow).'</div>'; 
                                            foreach($rsCategory as $categoryRow){ 
                                                $categorykey = $categoryRow['pkey'];
                                                if(!empty($arrCostCOAKey[$categorykey])){ 
                                                    $_POST['hidCostCOADetailKey[]'] = $arrCostCOAKey[$categorykey]['pkey']; 
                                                    $_POST['hidCostCOAKeyDetail[]'] = $arrCostCOAKey[$categorykey]['coakey'];
                                                    $_POST['costCOALinkDetail[]'] = (!empty($arrTempCOA[$arrCostCOAKey[$categorykey]['coakey']])) ? $arrTempCOA[$arrCostCOAKey[$categorykey]['coakey']]['code'] .' - '. $arrTempCOA[$arrCostCOAKey[$categorykey]['coakey']]['name'] : '' ;
                                                }else{
                                                    $_POST['hidCostCOADetailKey[]']  = '';
                                                    $_POST['hidCostCOAKeyDetail[]']  = '';
                                                    $_POST['costCOALinkDetail[]']  = '';
                                                }

                                                $hidDetailKey = $obj->inputHidden('hidCostCOADetailKey[]'); 
                                                $typeKeyInput = $obj->inputHidden('typeKeyCOA[]',array( 'value' => $typekey)); 
                                                $categoryKeyInput = $obj->inputHidden('categoryKeyCOA[]',array( 'value' => $categoryRow['pkey'])); 

                                                $coaLinkInput = $obj->inputAutoComplete(array(  
                                                                                        'element' => array('value' => 'costCOALinkDetail[]', 'key' => 'hidCostCOAKeyDetail[]'),
                                                                                        'source' =>array(  'url' => 'ajax-coa.php', 'data' => array(  'action' =>'searchData' ) )  
                                                                                        )); 

                                                echo '     
                                                <div class="form-group">
                                                    <label class="col-xs-3 control-label">'.$hidDetailKey.$typeKeyInput.$categoryKeyInput.$categoryRow['name'].'</label> 
                                                    <div class="col-xs-9">'.$coaLinkInput.'</div> 
                                                </div>';

                                            }        
                                        } 
                                        
                                        echo  '<div style="clear:both; height: 2em"></div>';
    
                                    }else{ // normal ?> 
                                     <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['revenueAccount']); ?></label> 
                                        <div class="col-xs-9">  
                                            <?php 
                                                       echo $obj->inputAutoComplete(array( 
                                                                            'revalidateField' => true, 
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
                                    <?php } ?>
                                 <?php } ?>
 
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
                                        <div class="form-group" >
                                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['tax']); ?></label> 
                                            <div class="col-xs-9">
                                                <div class="flex" <?php if (!$usePPNDetail) { echo 'style="padding-top:5px"';} ?> >
                                                    <div ><?php echo $obj->inputCheckBox('chkIsTax23'); ?></div>
                                                    <div ><?php echo ucwords($obj->lang['tax23']); ?></div>
                                                    <?php if ($usePPNDetail) { ?> 
                                                    <div style="margin-left:5em"><?php echo ucwords($obj->lang['PPN']); ?></div>
                                                    <div style="width:7em"><?php echo $obj->inputNumber('taxPercentage'); ?></div>
                                                    <div>%</div>
                                                    <div style="margin-left:1em"><?php echo $obj->inputCheckBox('chkIsPriceIncludeTax'); ?></div>
                                                    <div><?php echo ucwords('Include'); ?></div>
                                                    <?php } ?>
                                                </div>  
                                            </div> 
                                        </div> 
                            </div>
                     
                         <div class="div-tab-panel"> 
                          		<div class="div-table-caption border-green"><?php echo ucwords($obj->lang['image']); ?></div> 
                                 <div class="div-table-row"> 
                                    <div class="div-table-col-5">
                                      <!-- image uploader --> 
                                      	<div class="item-image-uploader">
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
                    </div>  
                    <div class="div-table-col">  
                                <div class="div-tab-panel transaction-detail" style="margin-bottom:3em; "> 
                          		<div class="div-table-caption border-blue"><?php echo ucwords($obj->lang['description']); ?></div>
                                    
                                  <?php
								    $totalRows = count($rsItemDescription); 
                  
                                    for ($i=0;$i<=$totalRows; $i++){   
                                        $class =  'transaction-detail-row'; 
                                        $overwrite = true;
                                        $etc = ''; 
                                        $style = '';
                                        $editor = '';

                                        if ($i == $totalRows ){
                                            $class = 'item-description-row-template'; 
                                            $overwrite = false;
                                            $etc = 'disabled="disabled"'; 
                                            $style  = 'style="display:none"';
                                            $editor =  $obj->inputTextArea('txtDescription[]', array('overwritePost' => $overwrite, 'class' => 'ckeditor'));
                                        } else {  
                                            $_POST['txtDescriptionLabel[]'] =  $rsItemDescription[$i]['label'];
                                            $_POST['txtDescription[]'] =  $rsItemDescription[$i]['value']; 
                                            $editor =  $obj->inputEditor('txtDescription[]', array('overwritePost' => $overwrite));
                                        }
                                    ?>
 
                                        <div class="form-group <?php echo $class; ?>" <?php echo $style; ?>>
                                            <div class="col-xs-12"> 
                                                <?php echo $obj->inputText('txtDescriptionLabel[]',array('value' => 'Deskripsi Produk', 'overwritePost' => $overwrite, 'etc' => $etc)); ?> 
                                            </div>  
                                            <div class="col-xs-12"  style="margin-top:1em">  
                                                <?php echo  $editor; ?>  
                                            </div> 
                                            <div class="col-xs-12" style="text-align:right">
                                                 <?php echo $obj->inputLinkButton('btnDeleteDescription', $obj->lang['delete'],array('class' => 'btn btn-link remove-button')); ?> 
                                            </div>
                                        </div>   

 
								<?php } ?> 
                                <div class="col-xs-12" style="text-align:center">  <?php echo $obj->inputButton('btnAddDescription',$obj->lang['add'], array( 'class' =>'btn btn-primary btn-second-tone')); ?>   </div>
                                     
                         	</div>    
                </div>
             </div>   
       </div>
             
        <div class="form-button-panel" > 
       	 <?php echo $obj->generateSaveButton(); ?> 
        </div>  
    </form>  
     <?php echo $obj->showDataHistory(); ?> 
</div> 
</body>

</html>
