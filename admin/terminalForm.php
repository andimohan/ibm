<?php 

include '../_config.php'; 
include '../_include-v2.php';  

includeClass('Terminal.class.php');
$terminal = createObjAndAddToCol(new Terminal()); 
$city = createObjAndAddToCol(new City()); 
$service = createObjAndAddToCol(new Service()); 
$location = createObjAndAddToCol(new Location());  
$truckingServiceOrderCategory = createObjAndAddToCol(new TruckingServiceOrderCategory());  
    
$obj= $terminal;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true));
 
$formAction = 'terminalList';  
 
$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false;
$rsDetail = array();

$rs = prepareOnLoadData($obj); 
$rsCost = array();

if (!empty($_GET['id'])){ 
    $id = $_GET['id'];	  
	$rsDetail = $obj->getCost($id);
  
	if (!empty($rs[0]['citykey'])){
		$rsCity = $city->searchData('city.pkey',$rs[0]['citykey'],true);
		$_POST['cityName'] = $rsCity[0]['name'] .', ' . $rsCity[0]['categoryname'];
	} 
    
} 

$arrStatus = $obj->convertForCombobox($obj->getAllStatus(),'pkey','status');    
$arrJobCategory = $obj->convertForCombobox($truckingServiceOrderCategory->searchData($truckingServiceOrderCategory->tableName.'.statuskey',1),'pkey','name',$obj->lang['allCategories']);    
$arrService = $obj->convertForCombobox($service->searchData($service->tableName.'.statuskey',1),'pkey','name',$obj->lang['allServices']);        
    
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title> 


<script type="text/javascript"> 
	
	jQuery(document).ready(function(){  
		
        var tabID = <?php echo ($isQuickAdd) ?  $_GET['tabID'] :  'selectedTab.newPanel[0].id';  ?>  
        var terminal = new Terminal(tabID);
         prepareHandler(terminal);   
        
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
                                                message:  phpErrorMsg.terminal[1]
                                            }
                                        } 
                                    },

                                } ; 
        
        setFormValidation(getTabObj(tabID), $('#defaultForm-' + tabID), fieldValidation, <?php echo json_encode($obj->validationFormSubmitParam()); ?>  );
        
  	  
        setAutoComplete(tabID, {objName:'cityName', objValue :'hidCityKey', url : 'ajax-city.php?action=searchData' });

   
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
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['name']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputText('name'); ?> 
                                        </div> 
                                     </div>  
                                     <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['city']); ?></label> 
                                        <div class="col-xs-9"> 
                                                <?php  
                                                        $popupOpt = (!$isQuickAdd) ? array(
                                                                            'url' => 'cityForm.php',
                                                                            'element' => array('value' => 'cityName',
                                                                                   'key' => 'hidCityKey'),
                                                                            'width' => '600px',
                                                                            'title' => ucwords($obj->lang['add'] . ' - ' . $obj->lang['city'])
                                                                        )  : '';

                                                        echo $obj->inputAutoComplete(array(
                                                                                            'objRefer' => $city,
                                                                                            'revalidateField' => false, 
                                                                                            'element' => array('value' => 'cityName',
                                                                                                               'key' => 'hidCityKey'),
                                                                                            'source' =>array(
                                                                                                                'url' => 'ajax-city.php',
                                                                                                                'data' => array(  'action' =>'searchData' )
                                                                                                            ) ,
                                                                                            'popupForm' => $popupOpt
                                                                                          )
                                                                                    );  
                                                ?> 
                                        </div> 
                                     </div>   
                                     <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['address']); ?></label> 
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
                  <div class="div-table-col">   
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
													$_POST['selService[]'] =  $rsDetail[$i]['servicekey'];                                                     $_POST['price[]'] =  $obj->formatNumber($rsDetail[$i]['price']);  
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
                                            <div class="div-table-col detail-col-detail  <?php echo $obj->hideOnDisabled(); ?>  icon-col"><?php echo (!$readonly) ? $obj->inputLinkButton('btnAddDetailRow' , '<i class="fas fa-plus-circle"></i>', array('class' => 'btn btn-link add-row-button', 'etc' => 'attr-template="detail-row-template"')) : ''; ?></div>
                                            <div class="div-table-col detail-col-detail <?php echo $obj->hideOnDisabled(); ?>"><?php echo $obj->inputLinkButton('btnDeleteRows' , '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button', 'etc' =>  'tabIndex="-1" style="padding:6px 0"')); ?></div>
                                        </div>

                                    <?php } ?> 

                                 </div>        
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
