<?php 
require_once '../_config.php'; 
require_once '../_include.php'; 

$obj= $itemInDepot;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true)); 

$formAction = 'itemInDepotList';
 
$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false; 

$editDepotInactiveCriteria = ''; 

$showVendorPartNumber = false;// $item->loadSetting('showVendorPartNumber');
$rsDetail = array(); 

$_POST['trDate'] = date('d / m / Y 00:00');

$rs = prepareOnLoadData($obj);  

$fileType = $obj->fileType;  

if (!empty($_GET['id'])){ 
	$id = $_GET['id'];	
	 
    $rsDetail = $obj->getDetailWithRelatedInformation($id);
   
    foreach($obj->fileType as $key=>$row){
        $rsItemFile[$key] = $obj->getItemFile($id,$key);
        if(count($rsItemFile[$key]) > 0){
            $sourcePath = $obj->defaultDocUploadPath.$row['uploadFileFolder'].$id;
            $destinationPath = $obj->uploadTempDoc.$row['uploadFileFolder'].$id; 
            $obj->deleteAll($destinationPath); 

            if(!is_dir($destinationPath)) 
                mkdir ($destinationPath,  0755, true);

            $obj->fullCopy($sourcePath,$destinationPath);  
        }
    }
     
    
	$_POST['trDate'] = $obj->formatDBDate($rs[0]['trdate'],'d / m / Y H:i'); 
	$_POST['selDepotKey'] =$rs[0]['depotkey']; 
    if (!empty($rs[0]['customerkey'])){
        $_POST['hidCustomerKey'] = $rs[0]['customerkey'] ;  
        $rsCustomer = $customer->getDataRowById($rs[0]['customerkey']); 
        $_POST['customerName'] = $rsCustomer[0]['name'] ;
    } 
    if (!empty($rs[0]['truckingvendorkey'])){
        $_POST['hidTruckingVendorKey'] = $rs[0]['truckingvendorkey'] ;  
        $rsTruckingVendor = $supplier->getDataRowById($rs[0]['truckingvendorkey']); 
        $_POST['truckingVendorName'] = $rsTruckingVendor[0]['name'] ;
    } 	
    
    $_POST['doCode'] =$rs[0]['docode']; 
	$_POST['policeNumber'] =$rs[0]['policenumber']; 
	$_POST['trDesc'] = $rs[0]['trdesc'];
     
	  
	$editDepotInactiveCriteria = ' or '.$depot->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['depotkey']);   
}

$arrStatus = $obj->convertForCombobox($obj->getAllStatus(),'pkey','status');    
$arrDepot = $class->convertForCombobox($depot->searchData('','',true,' and ('.$depot->tableName.'.isprivate = 1 and '.$depot->tableName.'.statuskey = 1' .$editDepotInactiveCriteria.')'),'pkey','name');  
$arrUnit = $class->convertForCombobox($itemUnit->searchData('','',true, ' and ('.$itemUnit->tableName.'.statuskey = 1 )'),'pkey','name'); 

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<style> 
    .total-sn-label {font-size: 0.9em; color:#999; font-style: italic}
    .tag-list li {height: 2em; text-align: center;}
    .transaction-detail>.div-table-row:nth-child(2n+3) .tag-list li {background-color: #dedede !important}
</style>
<title></title> 
 
<script type="text/javascript">   
    var objAndValueForVendorDetailAutoComplete = [];
    function ItemInDepot(tabID) { 
         
        this.updateDetail = function updateDetail(target,objAndValue,ui){   
            var detailRow = $(target).closest(".transaction-detail-row");
  
            for(i=0;i<objAndValue.length;i++){   
                detailRow.find("[name='" + objAndValue[i].object +"']").first().val(ui.item[objAndValue[i].value]).change().blur();  
            } 

            // harus handle manual utk obj autosearch
            detailRow.find("[name=\"itemName[]\"]").first().val(ui.item['value']);  
 
        }  
        
        this.updateItem = function updateItem (target,objAndValue,ui){
                var detailRow = $(target).closest(".transaction-detail-row"); 
 
                for(i=0;i<objAndValue.length;i++){    
                    detailRow.find("[name='" + objAndValue[i].object +"']").first().val(ui.item[objAndValue[i].value]); //.change().blur();  
                } 
            
                detailRow.find(".inputnumber").change().blur();  
              
        }
         
     }
    
	jQuery(document).ready(function(){  
	 	 var tabID = <?php echo ($isQuickAdd) ?  $_GET['tabID'] :  'selectedTab.newPanel[0].id';  ?>  
         itemInDepot = new ItemInDepot(tabID);
        
         setOnDocumentReady(tabID);   
	
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
                 policeNumber: { 
                    validators: {
                        notEmpty: {
                            message: phpErrorMsg.car[1]
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
                
                doCode: { 
                    validators: {
                        notEmpty: {
                            message: phpErrorMsg.depot[3]
                        }, 
                    }
				},
                
                truckingVendorName: { 
                    validators: {
                        notEmpty: {
                            message: phpErrorMsg.trucking[1]
                        }, 
                    }
				},
			 
            }
        })
        .on('success.form.bv', function(e) {
               <?php echo $obj->submitFormScript(); ?>
        });
        
        <?php   
            foreach ($fileType as $key=>$row) { 

                /// FILE UPLOADER 
                echo 'var fileFolder'.$key.' = "'.$row['uploadFileFolder'].'";'; 
                echo 'var fileUploaderTarget'.$key.' = "item-file-uploader-'.$key.'";'; 
                echo 'var arrFile'.$key.' = Array();'; 
  
                if (isset($id) && !empty($id)){   
                    for($i=0;$i<count($rsItemFile[$key]);$i++) 
                        echo 'arrFile'.$key.'.push("'.$rsItemFile[$key][$i]['file'].'"); '; 

                    echo 'createFileUploader(fileUploaderTarget'.$key.',fileFolder'.$key.','.$id.',arrFile'.$key.',true);';  

                }else{ 
                    echo 'createFileUploader(fileUploaderTarget'.$key.',fileFolder'.$key.',"","",true);'; 
                }
            }
		?>
          
	 
	
		objAndValue = new Array;
		objAndValue.push({object:'hidItemKey[]', value :'pkey'});   
		objAndValue.push({object:'selUnit[]', value :'deftransunitkey'}); 
		objAndValue.push({object:'COGS[]', value :'cogs'});   
        objAndValueForDetailAutoComplete[tabID] = objAndValue;
    
		// DETAIL CLONE
		 $("#" + tabID + " [name=btnAddRows]").on('click', function() { 
          	addNewTemplateRow("detail-row-template");
			bindAutoCompleteForTransactionDetail('itemName[]',objAndValueForDetailAutoComplete[tabID],'ajax-item.php?action=searchData&itemtype=4','itemInDepot.updateDetail');
          });
	 
        <?php if (empty($_GET['id'])){ ?> 
            addNewTemplateRow("detail-row-template");
        <?php } ?>
        bindAutoCompleteForTransactionDetail('itemName[]',objAndValueForDetailAutoComplete[tabID],'ajax-item.php?action=searchData&itemtype=4','itemInDepot.updateDetail');
    
        
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
                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['date']); ?></label> 
                            <div class="col-xs-9"> 
                                <?php echo $obj->inputDateTime('trDate'); ?>
                            </div> 
                        </div> 
                        <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['depot']); ?></label> 
                            <div class="col-xs-9"> 
                                <?php echo $obj->inputSelect('selDepotKey', $arrDepot ); ?>  
                            </div> 
                        </div>  
                        <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['doCode']); ?></label> 
                            <div class="col-xs-9"> 
                                <?php echo  $obj->inputText('doCode'); ?>
                            </div> 
                        </div>  
                        <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['customer']); ?></label> 
                            <div class="col-xs-9"> 
                                  <?php     
                                        echo $obj->inputAutoComplete(array(
                                                                            'objRefer' => $customer,
                                                                            'revalidateField' => true, 
                                                                            'element' => array('value' => 'customerName',
                                                                                               'key' => 'hidCustomerKey'),
                                                                            'source' =>array(
                                                                                                'url' => 'ajax-customer.php',
                                                                                                'data' => array(  'action' =>'searchData' )
                                                                                            ) ,
                                                                            'popupForm' => array(
                                                                                                'url' => 'customerForm.php',
                                                                                                'element' => array('value' => 'customerName',
                                                                                                       'key' => 'hidCustomerKey'),
                                                                                                'width' => '1000px',
                                                                                                'title' => ucwords($obj->lang['add'] . ' - ' . $obj->lang['customer'])
                                                                                            ), 
                                                                          )
                                                                    ); 
                                ?>  
                            </div> 
                        </div> 
                        <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['trucking']); ?></label> 
                            <div class="col-xs-9"> 
                                  <?php     
                                        echo $obj->inputAutoComplete(array(
                                                                            'objRefer' => $supplier,
                                                                            'revalidateField' => true, 
                                                                            'element' => array('value' => 'truckingVendorName',
                                                                                               'key' => 'hidTruckingVendorKey'),
                                                                            'source' =>array(
                                                                                                'url' => 'ajax-supplier.php',
                                                                                                'data' => array(  'action' =>'searchData' )
                                                                                            ) ,
                                                                            'popupForm' => array(
                                                                                                'url' => 'supplierForm.php',
                                                                                                'element' => array('value' => 'truckingVendorName',
                                                                                                       'key' => 'hidTruckingVendorKey'),
                                                                                                'width' => '1000px',
                                                                                                'title' => ucwords($obj->lang['add'] . ' - ' . $obj->lang['supplier'])
                                                                                            ),
                                                                            'allowedStatusForEdit' => array(1)
                                                                          )
                                                                    ); 
                                ?>  
                            </div> 
                        </div>
                                
                        <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['carRegistrationNumber']); ?></label> 
                            <div class="col-xs-9"> 
                                <?php echo  $obj->inputText('policeNumber'); ?>
                            </div> 
                        </div>   
                        <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['note']); ?></label> 
                            <div class="col-xs-9"> 
                            <?php echo  $obj->inputTextArea('trDesc',array('etc' => 'style="height:10em;"' )); ?>
                            </div> 
                        </div>  
                            
                    </div> 
                </div> 
                <div class="div-table-col">   
                    <?php foreach ($fileType as $key=>$row) {  ?>
                            <div class="div-tab-panel">  
                                 <div class="div-table" style="width:100%"> 
                                    <div class="div-table-caption border-purple"><?php echo ucwords($row['title']); ?></div> 
                                     <div class="div-table-row"> 
                                        <div class="div-table-col-5">
                                          <!-- file uploader --> 
                                            <div class="item-file-uploader item-file-uploader-<?php echo $key; ?>">
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
             </div>
        </div>        
        <div class="div-table transaction-detail" style="width:100%; border-bottom:1px solid #333; ">
                <div class="div-table-row">
                    <?php if ($showVendorPartNumber) { ?> 
                        <div class="div-table-col detail-col-header" style="width:150px;"><?php echo ucwords($obj->lang['vendorPartNumber']); ?></div>
                    <?php } ?>
                    <div class="div-table-col detail-col-header"><?php echo ucwords($obj->lang['itemName']); ?></div>
                    <div class="div-table-col detail-col-header" style="width:60px; text-align:right;"><?php echo ucwords($obj->lang['qty']); ?></div>
                    <div class="div-table-col detail-col-header" style="width:100px;"><?php echo ucwords($obj->lang['unit']); ?></div> 
                     <?php if ($showVendorPartNumber) { ?> 
                    <div class="div-table-col detail-col-header  icon-col <?php echo $obj->hideOnDisabled(); ?>"></div>
                    <?php } ?>
                    <div class="div-table-col detail-col-header  icon-col <?php echo $obj->hideOnDisabled(); ?>"></div>
                </div>
                
				<?php 
                    $totalRows = count($rsDetail);
            
                    for ($i=0;$i<=$totalRows; $i++){  
                        
                        $class =  'transaction-detail-row';
                        $overwrite = true;
                        $etc = '';  
                        
                        if ($i == $totalRows ){
                            $class = 'detail-row-template';
                            $overwrite = false;
                            $etc = 'disabled="disabled"';
                            $baseunitname = 'Pcs';
                        } else{ 
                              
                            $_POST['hidDetailKey[]'] =  $rsDetail[$i]['pkey']; 
                            $_POST['hidItemKey[]'] =  $rsDetail[$i]['itemkey']; 
                            $_POST['selUnit[]'] =  $rsDetail[$i]['unitkey']; 
                            $_POST['itemName[]'] =  $rsDetail[$i]['itemname']; 
                            $_POST['qty[]'] =   $obj->formatNumber($rsDetail[$i]['qty']);     
                        }

                         
                 ?>
            
                   <div class="div-table-row <?php echo $class; ?>">  
                    <div class="div-table-col detail-col-detail" style="vertical-align:top;">
                         <?php echo $obj->inputText('itemName[]',array('overwritePost' => $overwrite, 'etc' => $etc)); ?>
                         <?php echo $obj->inputHidden('hidItemKey[]',array('overwritePost' => $overwrite, 'etc' => $etc)); ?>
                         <?php echo $obj->inputHidden('hidDetailKey[]',array('overwritePost' => $overwrite, 'etc' => $etc)); ?>
                    </div> 
                    <div class="div-table-col detail-col-detail" style="vertical-align:top;"><?php echo $obj->inputNumber('qty[]', array('overwritePost' => $overwrite, 'etc' => 'style="text-align:right;" ' .$etc)); ?></div>
                    <div class="div-table-col detail-col-detail" style="vertical-align:top;"><?php echo $obj->inputSelect('selUnit[]',$arrUnit, array('overwritePost' => $overwrite, 'etc' => $etc)); ?></div>
                    <div class="div-table-col detail-col-detail icon-col <?php echo $obj->hideOnDisabled(); ?>" style="vertical-align:top;"><?php echo $obj->inputLinkButton('btnDeleteRows' , '<i class="fas fa-times"></i>', array('etc'=>'tabIndex="-1"','class' => 'btn btn-link remove-button')); ?></div>
               </div> 
                         
                <?php  } ?> 
                   
         </div>        
          
          <div style="clear:both; height:1em;"></div> 
          <div style="float:left; display:inline-block;"><?php echo $obj->inputButton('btnAddRows', $obj->lang['addRows'], array('class' => 'btn btn-primary btn-second-tone')); ?></div>
    
        <div class="form-button-margin"></div>
         <div class="form-button-panel" > 
       	    <?php  echo $obj->generateSaveButton(array(),true);?>
        </div> 
        
    </form>  
     <?php  echo $obj->showDataHistory(); ?> 
</div> 
</body>

</html>
