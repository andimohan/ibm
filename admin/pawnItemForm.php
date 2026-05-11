<?php 
require_once '../_config.php'; 
require_once '../_include.php'; 

$obj= $pawnItem;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true));
    
$formAction = 'pawnItemList';

$parentFileName = $_GET['fileName'];
$parentPanelId = $_GET['selectedPanelId'];
$parentTitle = $_GET['title'];
 
$editUnitInactiveCriteria = ''; 
 
$rsItemDescription = array();
$rsItemUnitConversion = array();  
$rsItemFilter = array();
  
$allowChangeUnit = '';  

$rs = prepareOnLoadData($obj); 
 
if (!empty($_GET['id'])){ 
	 
    $id = $_GET['id'];
     
	$rsCategory = $itemCategory->getDataRowById($rs[0]['categorykey']);
    
	$_POST['name'] = $rs[0]['name'];
	$_POST['tag'] = $rs[0]['tag']; 
	 
	$_POST['sellingPrice'] = $obj->formatNumber($rs[0]['sellingprice']);
	$_POST['secondPrice'] = $obj->formatNumber($rs[0]['secondprice']);
	$_POST['hidSecondPercentage'] = $rsCategory[0]['secondpercentage'];
	$_POST['hidSellPercentage'] = $rsCategory[0]['sellpercentage'];
        
	$hasCOGSAccess = $security->isAdminLogin($item->cogsSecurityObject,10);  
    $_POST['cogs'] = ($hasCOGSAccess) ?  $obj->formatNumber($rs[0]['cogs']) : 0 ;
	  
	$_POST['qtyOnHand'] = $obj->formatNumber($itemMovement->sumItemMovement($rs[0]['pkey'])); 
	$_POST['shortdescription'] = $rs[0]['shortdescription']; 
  
	$_POST['hidCategoryKey'] = $rs[0]['categorykey']; 
    if (!empty($rs[0]['categorykey'])){
		$rsCategory = $itemCategory->getDataRowById($rs[0]['categorykey']);
        $categoryName =  $itemCategory->getPath($rsCategory[0]['pkey']);
		$_POST['categoryName'] = $categoryName[0]['path'];
	}
    
	$_POST['hidBrandKey'] = $rs[0]['brandkey']; 
    if (!empty($rs[0]['brandkey'])){
		$rsBrand = $brand->getDataRowById($rs[0]['brandkey']);
		$_POST['brandName'] = $rsBrand[0]['name'];
	}
     
	 
	$editUnitInactiveCriteria = ' or '.$itemUnit->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['baseunitkey']) .' or '.$itemUnit->tableName.'.pkey = '  . $obj->oDbCon->paramString($rs[0]['deftransunitkey']); 
 
	$rsItemDescription = $obj->getItemDescription($id);	
   
    
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

$arrStatus = $obj->convertForCombobox($obj->getAllStatus(),'pkey','status');     
 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head> 
 
<style>
.item-filter  {list-style:none; padding:0; margin:0}
.item-filter li{float:left; margin: 0.2em 0.2em 0em 0.2em; display:inline-block; }    
.item-filter li label{width:150px; cursor:pointer;background-color:#dedede; padding:0.7em 1em 1em 1em;}
.item-filter li input {font-size:1.5em;margin-right:0.2em; }
</style>  

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title>  

<script type="text/javascript">   
 
    function ItemPawn(tabID) {  

         this.updatePercentageLabel = function updatePercentageLabel(){
                        var categorykey = $( "#" + tabID + " [name=hidCategoryKey]" ).val();  
                       
                        if(!categorykey)
                            return;
 
                        $.ajax({
                            type: "GET",
                            url:  'ajax-item-category.php',
                            async: false,
                            data: "action=getDataRowById&pkey=" + categorykey ,  
                        }).done(function( data ) { 
                          
                                data = JSON.parse(data) ; 
                                data = data[0];
                                   
                                $("#" + tabID + " [name=hidSecondPercentage]").val(data.secondpercentage);
                                $("#" + tabID + " [name=hidSellPercentage]").val(data.sellpercentage);
                            
                                $("#" + tabID + " .secondPercentange").html(data.secondpercentage).formatCurrency({roundToDecimalPlace: 2 });
                                $("#" + tabID + " .sellPercentange").html(data.sellpercentage).formatCurrency({roundToDecimalPlace: 2 });
                                
                                itemPawn.updateValue();
                        });
         }  
        
         this.updateValue = function updateValue(){ 
             
             var value = unformatCurrency($( "#" + tabID + " [name=cogs]" ).val());
            
             secondPercentage = $("#" + tabID + " [name=hidSecondPercentage]").val();
             sellPercentage = $("#" + tabID + " [name=hidSellPercentage]").val();
             
             $("#" + tabID + " [name=secondPrice]").val(value * secondPercentage / 100).blur();
             $("#" + tabID + " [name=sellingPrice]").val(value * sellPercentage / 100).blur();
         }

    }

	jQuery(document).ready(function(){  
        
        var tabID = selectedTab.newPanel[0].id;   
        itemPawn = new ItemPawn(tabID);
        setOnDocumentReady(tabID);
        
		/// FILE UPLOADER
		var folder = "<?php echo $obj->uploadFolder; ?>";
		var fileFolder = "<?php echo $obj->uploadFileFolder; ?>";
		var imageUploaderTarget = "item-image-uploader";  
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
				
				
				cogs: {
					validators: { 
				 		greaterThan: {
							value: -1,
							inclusive: false,
							separator: ',', 
							message: phpErrorMsg.sellingPrice[2]
						}, 
					}
				},
                
				secondPrice: {
					validators: { 
				 		greaterThan: {
							value: -1,
							inclusive: false,
							separator: ',', 
							message: phpErrorMsg.sellingPrice[2]
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
                 
            }
        })
        .on('success.form.bv', function(e) {  
                  <?php echo $obj->submitFormScript(); ?>
        }); 
        
        <?php if (!isset($rsItemDescription) || empty($rsItemDescription)) {  ?> 
            addNewTemplateRow("item-description-row-template");  
        <?php } ?>
        <?php if (!isset($rsItemUnitConversion) || empty($rsItemUnitConversion)) { ?> 
            addNewTemplateRow("unit-conversion-row-template");
        <?php } ?> 
      
	}); 
	 
	 
</script>

</head> 

<body> 

<div style="width:100%; margin:auto; " class="tab-panel-form">   
  <div class="notification-msg"></div>
  
  <form id="defaultForm" method="post" class="form-horizontal" action="<?php echo $formAction; ?>">
    <?php prepareOnLoadDataForm($obj); ?>   
    <?php echo  $obj->inputHidden('hidSecondPercentage'); ?>
    <?php echo  $obj->inputHidden('hidSellPercentage'); ?>
      
     <div class="div-table main-tab-table-2">
              <div class="div-table-row">
                    <div class="div-table-col">  
                  		   	<div class="div-tab-panel">  
                                    <div class="div-table-caption border-orange">Data Barang</div>
                                 
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label">Status</label> 
                                        <div class="col-xs-9"> 
                                             <?php echo  $obj->inputSelect('selStatus', $arrStatus); ?>
                                        </div> 
                                    </div> 
                              
                                     <div class="form-group">
                                        <label class="col-xs-3 control-label">Kode</label> 
                                        <div class="col-xs-9"> 
                                           <?php echo $obj->inputAutoCode('code'); ?>
                                        </div> 
                                     </div>
                                    
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label">Nama Barang</label> 
                                        <div class="col-xs-9"> 
                                             <?php echo $obj->inputText('name'); ?>
                                        </div> 
                                    </div>
                                
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label">Kategori</label> 
                                         <div class="col-xs-9">  
                                           <?php    
                                            echo $obj->inputAutoComplete(array(
                                                                                'objRefer' => $itemCategory,
                                                                                'revalidateField' => true, 
                                                                                'element' => array('value' => 'categoryName',
                                                                                                   'key' => 'hidCategoryKey'),
                                                                                'source' =>array(
                                                                                                    'url' => 'ajax-item-category.php',
                                                                                                    'data' => array(  'action' =>'searchData' )
                                                                                                ) ,
                                                                                'popupForm' => array(
                                                                                                        'url' => 'itemCategoryForm.php',
                                                                                                        'element' => array('value' => 'categoryName',
                                                                                                               'key' => 'hidCategoryKey'),
                                                                                                        'width' => '600px',
                                                                                                        'title' => $obj->lang['add'] . ' - ' . $obj->lang['itemCategory']
                                                                                                    ),
                                                                                'callbackFunction' => 'itemPawn.updatePercentageLabel()',
                                                                              )
                                                                        );  
                                            ?> 
                                        </div> 
                                    </div>
                                
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label">Merk</label> 
                                        <div class="col-xs-9">  
                                           <?php    
                                            echo $obj->inputAutoComplete(array( 
                                                                                'objRefer' => $brand,
                                                                                'element' => array('value' => 'brandName',
                                                                                                   'key' => 'hidBrandKey'),
                                                                                'source' =>array(
                                                                                                    'url' => 'ajax-brand.php',
                                                                                                    'data' => array(  'action' =>'searchData' )
                                                                                                ) ,
                                                                                'popupForm' => array(
                                                                                                        'url' => 'brandForm.php',
                                                                                                        'element' => array('value' => 'brandName',
                                                                                                               'key' => 'hidBrandKey'),
                                                                                                        'width' => '600px',
                                                                                                        'title' => $obj->lang['add'] . ' - ' . $obj->lang['brand']
                                                                                                    )
                                                                              )
                                                                        );  
                                            ?> 
                                        </div> 
                                    </div> 
                                      
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label">Deskripsi Singkat</label> 
                                        <div class="col-xs-9"> 
                                               <?php echo  $obj->inputTextArea('shortdescription', array( 'etc' => 'style="height:8em;"')); ?>
                                        </div> 
                                    </div> 
                                      
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label">Tag</label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputText('tag'); ?>
                                        </div> 
                                    </div>  
                                     
                           </div> 
                            
                        <div class="div-tab-panel"> 
                          	<div class="div-table-caption border-purple">Informasi Transaksi</div>
                         	 
                                <div class="col-xs-4"  style="padding:15px 5px"> 
                                    <div class="form-group">
                                        <label class="col-xs-12 control-label">Harga Baru</label> 
                                        <div class="col-xs-12"> 
                                            <?php echo $obj->inputNumber('cogs', array('etc' => 'onChange="itemPawn.updateValue()"')); ?>
                                        </div> 
                                    </div>  
                                </div> 
                                <div class="col-xs-4"  style="padding:15px 5px"> 
                                    <div class="form-group">
                                        <label class="col-xs-12 control-label">Harga Second <span class="text-muted">(<span class="secondPercentange"><?php echo $obj->formatNumber($rsCategory[0]['secondpercentage'],2); ?></span> %)</span></label> 
                                        <div class="col-xs-12"> 
                                            <?php echo $obj->inputNumber('secondPrice'); ?>
                                        </div> 
                                    </div>  
                                </div>
                                <div class="col-xs-4"  style="padding:15px 5px"> 
                                    <div class="form-group">
                                        <label class="col-xs-12 control-label">Nilai Gadai <span class="text-muted">(<span class="sellPercentange"><?php echo  $obj->formatNumber($rsCategory[0]['sellpercentage'],2); ?></span> %)</span></label> 
                                        <div class="col-xs-12"> 
                                            <?php echo $obj->inputNumber('sellingPrice'); ?>
                                        </div> 
                                    </div>  
                                </div> 
                            
                         </div>   
                        
                    </div>  
                    <div class="div-table-col"> 
                        
                   			<div class="div-tab-panel"> 
                                <div class="div-table-caption border-green"><?php echo ucwords($obj->lang['stockInformation']); ?></div>  
                                    <div class="col-xs-4">
                                        <div class="form-group">   
                                            <label class="col-xs-12 control-label"><?php echo ucwords($obj->lang['qoh']); ?></label>    
                                            <div class="col-xs-12"><?php echo $obj->inputNumber('qtyOnHand', array('readonly' => true)); ?></div>  
                                        </div>   
                                    </div> 
                                 
                            </div> 
                        
                         <div class="div-tab-panel"> 
                          		<div class="div-table-caption border-pink">Gambar</div> 
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
                        
                        
                            <?php if (PLAN_TYPE['usefrontend']) { ?> 
                                <div class="div-tab-panel transaction-detail" style="margin-bottom:3em; "> 
                          		<div class="div-table-caption border-blue">Deskripsi</div>
                                  <?php
								    $totalRows = count($rsItemDescription); 
                  
                                    for ($i=0;$i<=$totalRows; $i++){   
                                        $class =  'transaction-detail-row'; 
                                        $overwrite = true;
                                        $etc = ''; 
                                        $style = '';

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
                                 
                                <div class="col-xs-12" style="text-align:center">  <?php echo $obj->inputButton('btnAddDescription',$obj->lang['add']); ?>   </div>
                                     
                              </div> 
                            <?php }  ?>   
                  </div>
             </div>   
       </div>
             
        <div class="form-button-panel" > 
       	 <?php echo $obj->generateSaveButton(); ?> 
        </div>  
    </form>   
    <?php  echo $obj->showDataHistory(); ?> 
</div> 
</body>

</html>