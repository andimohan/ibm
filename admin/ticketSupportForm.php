<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass('TicketSupport.class.php');  
$ticketSupport = createObjAndAddToCol( new TicketSupport()); 
$warehouse = createObjAndAddToCol( new Warehouse()); 
$customer = createObjAndAddToCol( new Customer());  
$city = createObjAndAddToCol( new City());  
$employeeCategory = createObjAndAddToCol( new EmployeeCategory());  

$obj= $ticketSupport;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true));
     
$formAction = 'ticketSupportList';
  
$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false;
 
$_POST['trDate'] = date('d / m / Y');
$_POST['startTime'] = date('d / m / Y 00:00');
$_POST['endTime'] = date('d / m / Y 00:00');

$editCategoryInactiveCriteria= '';
$editWarehouseInactiveCriteria = ''; 
$rs = prepareOnLoadData($obj); 
$rsItemImage = array();
if (!empty($_GET['id'])){ 
    $id = $_GET['id'];	  

    $_POST['trDate'] = $obj->formatDBDate($rs[0]['trdate'],'d / m / Y');
    $_POST['subject'] = $rs[0]['subject'];
    $_POST['selUrgency'] = $rs[0]['urgencykey'];
    $_POST['message'] = $rs[0]['message'];
    $_POST['selDivision'] = $rs[0]['divisionkey'];
    $_POST['selWarehouseKey'] =$rs[0]['warehousekey'];
    $_POST['startTime'] = $obj->formatDBDate($rs[0]['starttime'],'d / m / Y H:i');
	$_POST['endTime'] = $obj->formatDBDate($rs[0]['endtime'],'d / m / Y H:i');
    $rsCustomer = $customer->getDataRowById($rs[0]['customerkey']);
    if(!empty($rsCustomer)){
        $rsCity = $city->getDataRowById($rsCustomer[0]['citykey']); 
        $_POST['customerName'] = $rsCustomer[0]['name'] ;
        $_POST['hidCustomerKey'] = $rsCustomer[0]['pkey'] ; 
        $_POST['sid'] = $rsCustomer[0]['sid'] ; 
        $_POST['phone'] = $rsCustomer[0]['phone'] ;
        $_POST['attention'] = $rsCustomer[0]['attention'] ;
        $_POST['email'] = $rsCustomer[0]['email'] ;
        $_POST['address'] = $rsCustomer[0]['address'] ;
        $_POST['cityName'] = $rsCity[0]['name'] ;
        
    }
	

    $editWarehouseInactiveCriteria = ' or  '.$warehouse->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['warehousekey']);  
    //get item
    $rsItemImage = $obj->getItemImages($id);

        if(count($rsItemImage) > 0){
            $sourcePath = $obj->defaultDocUploadPath.$obj->uploadFolder.$id;
            $destinationPath = $obj->uploadTempDoc.$obj->uploadFolder.$id; 
            $obj->deleteAll($destinationPath); 

            if(!is_dir($destinationPath)) 
                mkdir ($destinationPath,  0755, true);

            $obj->fullCopy($sourcePath,$destinationPath);  
            
            for($i=0;$i<count($rsItemImage);$i++)
                $rsItemImage[$i]['phpThumbHash'] = getPHPThumbHash($rsItemImage[$i]['file']);	
        }
} 

$arrStatus = $obj->convertForCombobox($obj->getAllStatus(),'pkey','status'); 
$arrUrgency = $obj->convertForCombobox($obj->getUrgency(),'pkey','name');  
$arrCategory = $class->convertForCombobox($employeeCategory->searchData('','',true, ' and ('.$employeeCategory->tableName.'.statuskey'. $editCategoryInactiveCriteria.')'),'pkey','name');    
$arrWarehouse = $obj->convertForCombobox($warehouse->searchData('','',true,' and ('.$warehouse->tableName.'.statuskey = 1' .$editWarehouseInactiveCriteria.')'),'pkey','name');  

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title>
<script type="text/javascript">
   
       jQuery(document).ready(function(){   
         var tabID = <?php echo ($isQuickAdd) ?  $_GET['tabID'] :  'selectedTab.newPanel[0].id';  ?>  
         var ticketSupport = new TicketSupport(tabID,"<?php echo $obj->uploadFolder; ?>",<?php echo json_encode($rsItemImage); ?>);

         prepareHandler(ticketSupport);   
        
         var fieldValidation =  {
                code: { 
                    validators: {
                        notEmpty: {
                            message: phpErrorMsg.code[1]
                        }, 
                    }
                }, 
				
                subject: { 
                    validators: {
                        notEmpty: {
                            message: phpErrorMsg.ticketSupport[1]
                        }, 
                    }
                }, 

                message: { 
                    validators: {
                        notEmpty: {
                            message: phpErrorMsg.ticketSupport[2]
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
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['date']); ?></label>
                                        <div class="col-xs-9">  
                                            <?php echo $obj->inputDate('trDate'); ?>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['warehouse']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo  $obj->inputSelect('selWarehouseKey', $arrWarehouse, array('allowedStatusForEdit' => array (1))); ?>
                                        </div> 
                                    </div>
                                    <div class="form-group">
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['division']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputSelect('selDivision',$arrCategory); ?>
                                        </div> 
                                    </div>
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['startTime']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputDateTime('startTime', array('allowedStatusForEdit' => array (1))); ?> 
                                        </div> 
                                    </div>
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['endTime']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputDateTime('endTime',array('allowedStatusForEdit' => array(1,2))); ?> 
                                        </div> 
                                    </div>
                                    <div class="form-group">
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['urgency']); ?></label> 
                                        <div class="col-xs-9"> 
                                                <?php echo $obj->inputSelect('selUrgency',$arrUrgency); ?>
                                        </div> 
                                    </div> 
                                    
                                    <div class="form-group">
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['subject']); ?></label> 
                                        <div class="col-xs-9"> 
                                                <?php echo $obj->inputText('subject'); ?>
                                        </div> 
                                    </div>
                                    <div class="form-group">
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['issue']); ?></label> 
                                        <div class="col-xs-9"> 
                                                <?php echo $obj->inputTextArea('message',array('etc' => 'style="height:10em;"')); ?>
                                        </div> 
                                    </div> 
                                     
                                       
                        </div>      
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
                    </div> 
                    <!-- collom spk   start-->
                    <div class="div-table-col">  
                        <div class="div-tab-panel">  
                            <div class="div-table-caption border-orange"><?php echo ucwords($obj->lang['customer']); ?></div>
                            
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['sid']); ?></label> 
                                <div class="col-xs-9"> 
                                      <?php  echo $obj->inputAutoComplete(array( 
                                                                        'objRefer' => $customer,
                                                                        'revalidateField' => true,
                                                                        'element' => array('value' => 'sid',
                                                                                           'key' => 'hidCustomerKey'),
                                                                        'source' =>array(
                                                                                            'url' => 'ajax-customer.php',
                                                                                            'data' => array(  'action' =>'searchData', 'searchField' => 'sid' )
                                                                                        ),
                                                                        'callbackFunction' => 'getTabObj().importData()' 
                                                                      )
                                                                );  
                                    ?> 
                                </div> 
                            </div>

                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['name']); ?></label> 
                                <div class="col-xs-9"> 
                                    <?php echo $obj->inputText('customerName', array('readonly' => true)); ?> 
                                </div> 
                            </div> 

                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['attention']); ?></label> 
                                <div class="col-xs-9"> 
                                    <?php echo $obj->inputText('attention', array('readonly' => true)); ?> 
                                </div> 
                            </div> 

                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['phone']); ?></label> 
                                <div class="col-xs-9"> 
                                    <?php echo $obj->inputText('phone', array('readonly' => true)); ?> 
                                </div> 
                            </div> 
                            
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['email']); ?></label> 
                                <div class="col-xs-9"> 
                                    <?php echo $obj->inputText('email', array('readonly' => true)); ?> 
                                </div> 
                            </div> 

                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['city']); ?></label> 
                                <div class="col-xs-9"> 
                                    <?php echo $obj->inputText('cityName', array('readonly' => true)); ?> 
                                </div> 
                            </div> 

                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['address']); ?></label> 
                                <div class="col-xs-9"> 
                                    <?php echo $obj->inputTextArea('address', array('readonly' => true,'etc' => 'style="height:10em;"')); ?> 
                                </div> 
                            </div> 

                        </div>
                    </div>
                    <!-- collom spk   end-->
            </div>
       </div>      
      
        <div class="form-button-panel" > 
       	 <?php  echo $obj->generateSaveButton(); ?> 
        </div> 
        
    </form>   
   
     <?php  echo $obj->showDataHistory(); ?>
</div> 
</body>

</html>
