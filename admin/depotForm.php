<?php 

include '../_config.php'; 
include '../_include-v2.php'; 

includeClass('Depot.class.php');
$depot = createObjAndAddToCol(new Depot()); 
$location = createObjAndAddToCol(new Location());  
$truckingServiceOrderCategory = createObjAndAddToCol(new TruckingServiceOrderCategory()); 
$truckingService = createObjAndAddToCol( new Service());    

$obj= $depot;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true));

$formAction = 'depotList';  
 
$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false;

$rs = prepareOnLoadData($obj); 
$rsCost = array();
$rsDetail = array();

if (!empty($_GET['id'])){ 
    $id = $_GET['id'];	 
	$rsDetail = $obj->getCost($id);
   
	if (!empty($rs[0]['locationkey'])){
		$rsLocation = $location->getDataRowById($rs[0]['locationkey']);
		$_POST['locationName'] = $rsLocation[0]['name'] ;
	} 
    
}else{
    $_POST['chkIsPrivate'] = 1;
}

$arrStatus = $obj->convertForCombobox($obj->getAllStatus(),'pkey','status'); 
$arrJobCategory = $obj->convertForCombobox($truckingServiceOrderCategory->searchData($truckingServiceOrderCategory->tableName.'.statuskey',1),'pkey','name',$obj->lang['allCategories']);   
$arrService = $obj->convertForCombobox($truckingService->searchData($truckingService->tableName.'.statuskey',1),'pkey','name',$obj->lang['allServices']);     

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title> 


<script type="text/javascript"> 
	
	jQuery(document).ready(function(){  
	 jQuery(document).ready(function(){  
		
        var tabID = <?php echo ($isQuickAdd) ?  $_GET['tabID'] :  'selectedTab.newPanel[0].id';  ?>  
        var depot = new Depot(tabID);
         prepareHandler(depot);   
        
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
                                                message:  phpErrorMsg.depot[1]
                                            }
                                        } 
                                    },

                                } ; 
        
        setFormValidation(getTabObj(), $('#defaultForm-' + tabID), fieldValidation, <?php echo json_encode($obj->validationFormSubmitParam()); ?>  ); 
  	  
        setAutoComplete(tabID, {objName:'cityName', objValue :'hidCityKey', url : 'ajax-city.php?action=searchData' });

   
	});
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
                    <div class="div-table-col" >  
                  		   	<div class="div-tab-panel">     
                                    <div class="div-table-caption border-orange"><?php echo ucwords($obj->lang['generalInformation']); ?></div>
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
                                        <label class="col-xs-3 control-label">Nama</label> 
                                        <div class="col-xs-9"> 
                                              <?php echo $obj->inputText('name'); ?>
                                        </div> 
                                     </div>  
                                     <div class="form-group">
                                        <label class="col-xs-3 control-label">Private Depot</label> 
                                        <div class="col-xs-9"> 
                                              <?php echo  $obj->inputCheckBox('chkIsPrivate'); ?>  
                                        </div> 
                                     </div>  
                                     <div class="form-group">
                                        <label class="col-xs-3 control-label">Lokasi</label> 
                                        <div class="col-xs-9"> 
                                                <?php  
                                                        $popupOpt = (!$isQuickAdd) ? array(
                                                                            'url' => 'locationForm.php',
                                                                            'element' => array('value' => 'locationName',
                                                                                   'key' => 'hidLocationKey'),
                                                                            'width' => '600px',
                                                                            'title' => $obj->lang['add'] . ' - ' . $obj->lang['location']
                                                                        )  : '';

                                                        echo $obj->inputAutoComplete(array(
                                                                                            'objRefer' => $location,
                                                                                            'revalidateField' => false, 
                                                                                            'element' => array('value' => 'locationName',
                                                                                                               'key' => 'hidLocationKey'),
                                                                                            'source' =>array(
                                                                                                                'url' => 'ajax-location.php',
                                                                                                                'data' => array(  'action' =>'searchData' )
                                                                                                            ) ,
                                                                                            'popupForm' => $popupOpt
                                                                                          )
                                                                                    );  
                                                ?> 
                                        </div> 
                                     </div>   
                                     <div class="form-group">
                                        <label class="col-xs-3 control-label">Alamat</label> 
                                        <div class="col-xs-9"> 
                                             <?php echo  $obj->inputTextArea('address', array('etc' => 'style="height:10em;"')); ?>
                                        </div> 
                                     </div>  
								
									<?php if(!empty(PARTNER_ACCOUNT['TMS'])){ ?> 
										<div class="form-group">
											<label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['partnerID']); ?></label> 
											<div class="col-xs-9"> 
												  <?php echo  $obj->inputText('partnerID'); ?> 
											</div> 
										</div>
									<?php } ?>
                            </div>   
                  </div> 
                   
                    <?php if (!$isQuickAdd) { ?> 
                  		   	 <div class="div-tab-panel">    
                                  <div class="div-table-caption border-green"><?php echo ucwords($obj->lang['costInformation']); ?></div>
                                  <div class="div-table mnv-transaction transaction-detail" style="width:100%;">
                                        <div class="div-table-row"> 
                                            <div class="div-table-col detail-col-header" style="width:110px; border:0"><?php echo ucwords($obj->lang['jobType']); ?></div>
                                            <div class="div-table-col detail-col-header" style="border:0"><?php echo ucwords($obj->lang['service']); ?></div>
											<div class="div-table-col detail-col-header" style="width:120px; border:0"><?php echo ucwords($obj->lang['cost']); ?></div>
                                            <div class="div-table-col detail-col-header" style="width:80px;border:0;text-align:right;"><?php echo ucwords($obj->lang['price']); ?></div>
                                            <div class="div-table-col detail-col-header  <?php echo $obj->hideOnDisabled(); ?> icon-col" style="border:0"></div>
                                            <div class="div-table-col detail-col-header  <?php echo $obj->hideOnDisabled(); ?> icon-col" style="border:0"></div> 
                                        </div>
 
                                        <?php 
                                            $totalDetail = count($rsDetail); 

                                            for ($i=0;$i<=$totalDetail; $i++){  

                                                $class =  'transaction-detail-row';
                                                $overwrite = true;
                                                $disabled = false; 
                                                if ($i == $totalDetail ){
                                                    $class = 'detail-row-template';
                                                    $overwrite = false;
                                                    $disabled = true; 
                                                } else {    
                                                    $_POST['hidDetailItemKey[]'] =  $rsDetail[$i]['pkey'];
                                                    $_POST['selJobCategory[]'] =  $rsDetail[$i]['jobcategorykey']; 
                                                    $_POST['hidItemKey[]'] =  $rsDetail[$i]['costkey']; 
                                                    $_POST['itemName[]'] =  $rsDetail[$i]['costname']; 
													$_POST['selService[]'] =  $rsDetail[$i]['servicekey']; 
                                                    $_POST['price[]'] =  $obj->formatNumber($rsDetail[$i]['price']);  
                                                }  

                                        ?>


                                        <div class="div-table-row <?php echo $class; ?>" style=""> 
                                            <div class="div-table-col detail-col-detail">                      
                                                <?php echo $obj->inputHidden('hidDetailItemKey[]',array('overwritePost' => $overwrite, 'disabled' => $disabled)); ?> 
                                                <?php echo $obj->inputSelect('selJobCategory[]',$arrJobCategory); ?>
                                            </div> 
                                            <div class="div-table-col detail-col-detail">
												<?php echo $obj->inputSelect('selService[]',$arrService); ?>
                                             </div>
                                            <div class="div-table-col detail-col-detail">
                                                <?php echo $obj->inputText('itemName[]',array('overwritePost' => $overwrite)); ?>
                                                <?php echo $obj->inputHidden('hidItemKey[]',array('overwritePost' => $overwrite,'disabled' => $disabled)); ?>
                                             </div> 
                                            <div class="div-table-col detail-col-detail">
                                                <?php echo $obj->inputNumber('price[]',array('overwritePost' => $overwrite,'etc' =>  'style="text-align:right"')); ?>
                                            </div> 
                                            <div class="div-table-col detail-col-detail  <?php echo $obj->hideOnDisabled(); ?>  icon-col"><?php echo $obj->inputLinkButton('btnAddDetailRow' , '<i class="fas fa-plus-circle"></i>', array('class' => 'btn btn-link add-row-button', 'etc' => 'attr-template="detail-row-template"')); ?></div>
                                            <div class="div-table-col detail-col-detail <?php echo $obj->hideOnDisabled(); ?>"><?php echo $obj->inputLinkButton('btnDeleteRows' , '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button', 'etc' =>  'tabIndex="-1" style="padding:6px 0"')); ?></div>
                                        </div>

                                    <?php } ?> 

                                 </div>        
                            </div> 
                    <?php } ?> 
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
