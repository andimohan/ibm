<?php 
require_once '../_config.php';  
require_once '../_include.php'; 

$obj= $itemDepot;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true));
      
$showItemImage = false;
$showMultiUnit = $item->loadSetting('showMultiUnit');  

$formAction = 'itemDepotList';

$parentFileName = $_GET['fileName'];
$parentPanelId = $_GET['selectedPanelId'];
$parentTitle = $_GET['title'];
 
$editUnitInactiveCriteria = ''; 
 
$rsItemDescription = array();
$rsItemUnitConversion = array();
  
$allowChangeUnit = '';

$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false;

$rs = prepareOnLoadData($obj); 
$finalDiscDecimal = 0;
$finalDiscDecimalType = 'inputnumber'; 
 

if (!empty($_GET['id'])){ 
	 
    $id = $_GET['id']; 
        
    $obj->lockUsedUnit($id);
	  
	$_POST['name'] = $rs[0]['name'];
	$_POST['tag'] = $rs[0]['tag'];
	$_POST['hidParentKey'] = $rs[0]['parentkey'];  
	
	$rsItem = $item->getDataRowById($rs[0]['parentkey']);
	if (!empty($rsItem)) 
		$_POST['itemParent'] = $rsItem[0]['name'];  
	 
	$_POST['sellingPrice'] = $obj->formatNumber($rs[0]['sellingprice']); 
	
	$hasCOGSAccess = $security->isAdminLogin($item->cogsSecurityObject,10);  
    $_POST['cogs'] = ($hasCOGSAccess) ?  $obj->formatNumber($rs[0]['cogs']) : 0 ;
     
	$_POST['selWeightUnit'] = $rs[0]['weightunitkey'];
	$_POST['length'] = $obj->formatNumber($rs[0]['length'],2);
	$_POST['width'] = $obj->formatNumber($rs[0]['width'],2);
	$_POST['height'] = $obj->formatNumber($rs[0]['height'],2);
    
	$_POST['minStockQty'] = $obj->formatNumber($rs[0]['minstockqty']);
	$_POST['maxStockQty'] = $obj->formatNumber($rs[0]['maxstockqty']); 
	$_POST['qtyOnHand'] = $obj->formatNumber($itemDepotMovement->sumItemMovement($rs[0]['pkey'])); 
	$_POST['shortdescription'] = $rs[0]['shortdescription']; 
	$_POST['selBaseUnitKey'] = $rs[0]['baseunitkey']; 
	$_POST['selDefaultTransUnitKey'] = $rs[0]['deftransunitkey'];  
  
	$_POST['hidCategoryKey'] = $rs[0]['categorykey']; 
    if (!empty($rs[0]['categorykey'])){
		$rsCategory = $itemCategory->getDataRowById($rs[0]['categorykey']);
        $categoryName =  $itemCategory->getPath($rsCategory[0]['pkey']);
		$_POST['categoryName'] = $categoryName[0]['path'];
	} 
	      
    $_POST['gramasi'] = $obj->formatNumber($rs[0]['gramasi'],2);   
     
	$editUnitInactiveCriteria = ' or '.$itemUnit->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['baseunitkey']) .' or '.$itemUnit->tableName.'.pkey = '  . $obj->oDbCon->paramString($rs[0]['deftransunitkey']); 
 
    if ($showMultiUnit) { 
        $rsItemUnitConversion = $obj->getItemUnitConversion($id,'','',' order by islocked desc, conversionmultiplier asc ');	

        // lock by conversion 
        $lockedByConversion = false;
        for ($i=0;$i<count($rsItemUnitConversion); $i++){   
                if ($rsItemUnitConversion[$i]['islocked'] == 1){
                    $lockedByConversion = true;
                    break;
                } 
        }

        $rsMovement = $itemMovement->searchData('itemkey', $id, true,'', 'limit 1');
        $allowChangeUnit = (empty($rsMovement) && !$lockedByConversion) ? '' : 'disabled="disabled"'; 
    }
    
    if ($showItemImage){
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


        //update file 
        $rsItemFile = $obj->getItemFile($id);

        if(count($rsItemFile) > 0){
            $sourcePath = $obj->defaultDocUploadPath.$obj->uploadFileFolder.$id;
            $destinationPath = $obj->uploadTempDoc.$obj->uploadFileFolder.$id; 
            $obj->deleteAll($destinationPath); 

            if(!is_dir($destinationPath)) 
                mkdir ($destinationPath,  0755, true);

            $obj->fullCopy($sourcePath,$destinationPath);  
        } 
    }
	
			 
} 

$arrStatus = $obj->convertForCombobox($obj->getAllStatus(),'pkey','status');     
$arrUnit = $class->convertForCombobox($itemUnit->searchData('','',true, ' and ('.$itemUnit->tableName.'.statuskey = 1 ' . $editUnitInactiveCriteria. ')'),'pkey','name'); 
$arrWeight = $class->convertForCombobox($obj->getSystemWeight(),'pkey','name'); 

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>  

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title>  

<script type="text/javascript">   
  function ItemDepot(tabID) {
        this.calculateTotalWeight =  function calculateTotalWeight(){  
                var qty =  unformatCurrency($("#" + tabID + " [name='qtyOnHand']").val());
                var weightPerPcs =  unformatCurrency($("#" + tabID + " [name='gramasi']").val());
                var totalWeight = qty * weightPerPcs;
                $("#" + tabID + " [name='totalWeight']").val(totalWeight).blur();
        }
        this.calculateTotalVolume =  function calculateTotalVolume(){  
                var qty =  unformatCurrency($("#" + tabID + " [name='qtyOnHand']").val());
                var width =  unformatCurrency($("#" + tabID + " [name='width']").val());
                var length =  unformatCurrency($("#" + tabID + " [name='length']").val());
                var height =  unformatCurrency($("#" + tabID + " [name='height']").val());
                var totalVolume = qty * ((width * length * height));
                $("#" + tabID + " [name='totalVolume']").val(totalVolume).blur();
        }
  }    
    
  jQuery(document).ready(function(){  
        
        var tabID = <?php echo ($isQuickAdd) ?  $_GET['tabID'] :  'selectedTab.newPanel[0].id';  ?>  
        itemDepot = new ItemDepot(tabID);
        setOnDocumentReady(tabID);
        
        <?php if ($showItemImage) { ?>
		/// FILE UPLOADER
		var folder = "<?php echo $obj->uploadFolder; ?>";
		var fileFolder = "<?php echo $obj->uploadFileFolder; ?>";
		var imageUploaderTarget = "item-image-uploader"; 
		var fileUploaderTarget = "item-file-uploader";
		var arrImage = Array();
		var arrPHPThumbHash = Array();
		var arrFile = Array();
		 
		<?php   
			if (isset($id) && !empty($id)){  
				for($i=0;$i<count($rsItemImage);$i++) {
					echo 'arrImage.push("'.$rsItemImage[$i]['file'].'"); '; 
					echo 'arrPHPThumbHash.push("'.getPHPThumbHash($rsItemImage[$i]['file']).'"); '; 
                }
                
				for($i=0;$i<count($rsItemFile);$i++) 
					echo 'arrFile.push("'.$rsItemFile[$i]['file'].'"); '; 
					
				echo 'createImageUploader({"tabID":tabID, "name":imageUploaderTarget},{"folder":folder, "token":'.$id.', "arrImage":arrImage,"phpThumbHash":arrPHPThumbHash},true,true);';  
               	echo 'createFileUploader(fileUploaderTarget,fileFolder,'.$id.',arrFile,true);';  
				
			}else{
				echo 'createImageUploader({"tabID":tabID, "name":imageUploaderTarget},{"folder":folder},true,true);';
				echo 'createFileUploader(fileUploaderTarget,fileFolder,"","",true);'; 
			}
		?>
        
                                   

        $( "." + imageUploaderTarget + " .image-list ").sortable({  placeholder: "sortable-placeholder" ,stop: function( event, ui ) { updateItemImageArray({"tabID":tabID, "name":imageUploaderTarget}); }});
        $( "." + imageUploaderTarget + " .image-list"  ).disableSelection();

        $( "." + fileUploaderTarget + " .file-list" ).sortable({  placeholder: "sortable-placeholder" ,stop: function( event, ui ) { updateItemFileArray(fileUploaderTarget); }});
        $( "." + fileUploaderTarget + " .file-list"  ).disableSelection(); 

        <?php } ?>
  
	      
		 $("#" + tabID + " [name=selBaseUnitKey]").change(function() {    
		  	$("#" + tabID + " .baseitemunit").html($(this).find('option:selected').text());
		 });
		 $("#" + tabID + " [name=selBaseUnitKey]").change();
		   
         $("#" + tabID + " [name=selWeightUnit]").change(function() {
         $("#" + tabID + " .weightunit").html($(this).find('option:selected').text());
		  itemDepot.calculateTotalWeight();
		 }); 
		 $("#" + tabID + " [name=selWeightUnit]").change();
		  
        itemDepot.calculateTotalVolume();
      
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
			     
				gramasi: {
					validators: { 
						greaterThan: {
							value: -1,
							inclusive: false,
							separator: ',', 
							message: phpErrorMsg.gramasi[2]
						}
					}
				}, 
            }
        })
        .on('success.form.bv', function(e) {  
                  <?php echo $obj->submitFormScript(); ?>
        }); 
 
	}); 
	 
	 
</script> 
</head> 

<body> 

<div style="width:100%; margin:auto; " class="tab-panel-form">   
  <div class="notification-msg"></div>
  
  <form id="defaultForm" method="post" class="form-horizontal" action="<?php echo $formAction; ?>">
    <?php prepareOnLoadDataForm($obj); ?>  
    <?php echo $obj->inputHidden('hidParentKey'); ?>
      
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
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['itemName']); ?></label> 
                                        <div class="col-xs-9"> 
                                             <?php echo $obj->inputText('name'); ?>
                                        </div> 
                                    </div>
                                
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['category']); ?></label> 
                                         <div class="col-xs-9">  
                                           <?php    
                                            echo $obj->inputAutoComplete(array(
                                                                                'objRefer' => $itemCategory,
                                                                                'revalidateField' => true, 
                                                                                'element' => array('value' => 'categoryName',
                                                                                                   'key' => 'hidCategoryKey'),
                                                                                'source' =>array(
                                                                                                    'url' => 'ajax-item-category.php',
                                                                                                    'data' => array(  'action' =>'searchData', 'isleaf' => 1 )
                                                                                                ) ,
                                                                                'popupForm' => array(
                                                                                                        'url' => 'itemCategoryForm.php',
                                                                                                        'element' => array('value' => 'categoryName',
                                                                                                               'key' => 'hidCategoryKey'),
                                                                                                        'width' => '600px',
                                                                                                        'title' => ucwords($obj->lang['add'] . ' - ' . $obj->lang['itemCategory'])
                                                                                                    )
                                                                              )
                                                                        );  
                                            ?> 
                                        </div> 
                                    </div>  
                                      
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['baseunit']); ?></label> 
                                        <div class="col-xs-9"> 
                                           <?php echo  $obj->inputSelect('selBaseUnitKey', $arrUnit); ?>
                                        </div> 
                                    </div>
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['weight']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <div class="flex">          
                                                <div><?php echo  $obj->inputSelect('selWeightUnit', $arrWeight); ?></div>
                                                <div class="consume"><?php echo $obj->inputDecimal('gramasi',array('etc' => "onChange=\"itemDepot.calculateTotalWeight()\"")); ?></div>
                                             </div> 
                                        </div> 
                                    </div>  
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label" style="margin-top:15px"><?php echo ucwords($obj->lang['size']); ?></label> 
                                        <div class="col-xs-3" style="padding-right:5px;">     
                                            <div class="text-muted"><?php echo ucwords($obj->lang['length']); ?></div>       
                                           <div class="flex">
                                               <div><?php echo $obj->inputDecimal('length',array('etc' => "onChange=\"itemDepot.calculateTotalVolume()\"")); ?></div>
                                               <div class="text-muted">CM</div>
                                            </div>   
                                        </div> 
                                        <div class="col-xs-3"  style="padding-right:10px; padding-left:10px">   
                                            <div class="text-muted"><?php echo ucwords($obj->lang['width']); ?></div>       
                                           <div class="flex">            
                                               <div><?php echo $obj->inputDecimal('width',array('etc' => "onChange=\"itemDepot.calculateTotalVolume()\"")); ?></div>
                                               <div class="text-muted">CM</div>
                                            </div>   
                                        </div> 
                                        <div class="col-xs-3"  style="padding-left:5px;">         
                                            <div class="text-muted"><?php echo ucwords($obj->lang['height']); ?></div>     
                                           <div class="flex">        
                                               <div><?php echo $obj->inputDecimal('height',array('etc' => "onChange=\"itemDepot.calculateTotalVolume()\"")); ?></div>
                                               <div class="text-muted">CM</div>
                                            </div>   
                                        </div> 
                                    </div>  
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['shortDescription']); ?></label> 
                                        <div class="col-xs-9"> 
                                               <?php echo  $obj->inputTextArea('shortdescription', array( 'etc' => 'style="height:8em;"')); ?>
                                        </div> 
                                    </div>  
                           </div> 
                             
                        
                      <?php if ($showItemImage) { ?>
                         <div class="div-tab-panel"> 
                             <div class="div-table" style="width:100%">
                          		<div class="div-table-caption border-pink"><?php echo ucwords($obj->lang['image']); ?></div> 
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
                         <div class="div-tab-panel">  
                             <div class="div-table" style="width:100%"> 
                          		<div class="div-table-caption border-black"><?php echo ucwords($obj->lang['file']); ?></div> 
                                 <div class="div-table-row"> 
                                    <div class="div-table-col-5">
                                      <!-- file uploader --> 
                                      	<div class="item-file-uploader">
                                      		<ul class="file-list"></ul>
                                            <div style="clear:both; height:1em;"></div>
                                            <div class="file-uploader">	
                                                <noscript>			
                                                <p>Please enable JavaScript to use file uploader.</p> 
                                                </noscript> 
                                            </div>
                                          </div>  
                                        <!-- file uploader --> 
                                    </div> 
                               </div> 
                             </div> 
                         </div> 
                      <?php } ?>
                        
                    </div>  
                    <div class="div-table-col"> 
  
                   			<div class="div-tab-panel"> 
                                <div class="div-table-caption border-green"><?php echo ucwords($obj->lang['stockInformation']); ?></div>  
                                    <div class="col-xs-4">
                                        <div class="form-group">   
                                            <label class="col-xs-12 control-label"><?php echo ucwords($obj->lang['qoh']); ?> <span class="text-muted">(<span class="baseitemunit"></span>)</span></label>    
                                            <div class="col-xs-12"><?php echo $obj->inputNumber('qtyOnHand', array('readonly' => true)); ?></div>  
                                        </div>   
                                    </div>  
                                    <div class="col-xs-4">
                                        <div class="form-group">   
                                            <label class="col-xs-12 control-label"><?php echo ucwords($obj->lang['totalWeight']); ?> <span class="text-muted">(<span class="weightunit"></span>)</span></label>    
                                            <div class="col-xs-12"><?php echo $obj->inputDecimal('totalWeight', array('readonly' => true)); ?></div>  
                                        </div>   
                                    </div>  
                                    <div class="col-xs-4">
                                        <div class="form-group">   
                                            <label class="col-xs-12 control-label"><?php echo ucwords($obj->lang['totalVolume']); ?> <span class="text-muted">(CM<sup>3</sup>)</span></label>    
                                            <div class="col-xs-12"><?php echo $obj->inputDecimal('totalVolume', array('readonly' => true)); ?></div>  
                                        </div>   
                                    </div>  
                                  <div style="clear:both; height: 1em"></div>
                                  <div class="div-table" style="margin:auto;  width:95%; "> 
                                     <div class="div-table-row"> 
                                         <div class="div-table-col-5" style="border-top:1px solid #666;border-bottom:1px solid #666;" > 
                                            <strong><?php echo ucwords($obj->lang['depot']); ?></strong>
                                         </div> 
                                         <div class="div-table-col-5" style="border-top:1px solid #666;border-bottom:1px solid #666; text-align:right;" > 
                                            <strong><?php echo ucwords($obj->lang['qty']); ?></strong>
                                         </div> 
                                     </div> 
									 <?php
                                     $rsDepot = $depot->searchData($depot->tableName.'.statuskey',1,true, ' and isprivate = 1 ' );
                                     for($i=0;$i<count($rsDepot);$i++){
                                         
                                         $qoh = 0;
                                         $colorClass ="";
                                         
                                         if (!empty($id)) 
                                            $qoh = $obj->formatNumber($itemDepotMovement->getItemQOH($id, $rsDepot[$i]['pkey']));
                                    
                                         if ($qoh == 0)
                                            $colorClass="text-red-cardinal";
                                         
                                         echo '
                                         <div class="div-table-row"> 
                                             <div class="div-table-col-5 '.$colorClass.'" style="border-bottom:1px solid #dedede;" > 
                                                '.$rsDepot[$i]['name'].'
                                             </div> 
                                             <div class="div-table-col-5 '.$colorClass.'" style="border-bottom:1px solid #dedede; text-align:right;" > 
                                                '.$qoh.'
                                             </div> 
                                         </div> 
                                         ';
                                     }
                                    ?>
                            </div> 
                            </div>                
                 </div>
             </div>   
       </div> 
   
      <div class="form-button-margin"></div>
        <div class="form-button-panel" > 
       	 <?php echo $obj->generateSaveButton(); ?> 
        </div>  
    </form>   
    <?php  echo $obj->showDataHistory(); ?> 
</div> 
</body>

</html>
