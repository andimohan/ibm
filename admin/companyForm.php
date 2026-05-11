<?php 

include '../_config.php'; 
include '../_include.php';  

$obj= $company;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true));

$formAction = 'companyList';  
 
$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false;

$rs = prepareOnLoadData($obj); 

if (!empty($_GET['id'])){ 
    $id = $_GET['id'];	 
    $_POST['address'] = $rs[0]['address'];
    $_POST['name'] = $rs[0]['name'];    
    $_POST['hidCityKey'] = $rs[0]['citykey'];
    $_POST['isService'] = $rs[0]['isservice'];
    $_POST['isRetail'] = $rs[0]['isretail'];
	
	if (!empty($rs[0]['citykey'])){
		$rsCity = $city->searchData($city->tableName.'.pkey',$rs[0]['citykey'],true);
		$_POST['cityName'] = $rsCity[0]['name'] .', ' . $rsCity[0]['categoryname'];
	}
    
    $_POST['hidEmployeeKey'] = $rs[0]['employeekey'];
	if (!empty($rs[0]['employeekey'])){
		$rsEmployee = $employee->getDataRowById($rs[0]['employeekey']);
		$_POST['employeeName'] = $rsEmployee[0]['name'];
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
	
	jQuery(document).ready(function(){  
		
        var tabID = <?php echo ($isQuickAdd) ?  $_GET['tabID'] :  'selectedTab.newPanel[0].id';  ?>  
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
				
				name: { 
                    validators: {
                        notEmpty: {
                            message: phpErrorMsg.depot[1]
                        }, 
                    }
                },  		
				
                employeeName: { 
                    validators: {
                        notEmpty: {
                            message: phpErrorMsg.businessPartner[1]
                        }, 
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
        <?php prepareOnLoadDataForm($obj,false); ?> 
        <div class="div-table main-tab-table-1">
              <div class="div-table-row">
                    <div class="div-table-col">  
                  		   	<div class="div-tab-panel">     
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
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['businessPartner']); ?></label> 
                                        <div class="col-xs-9"> 
                                             <?php  
                                                    $popupOpt = (!$isQuickAdd) ? array(
                                                                        'url' => 'employeeForm.php',
                                                                        'element' => array('value' => 'employeeName',
                                                                               'key' => 'hidEmployeeKey'),
                                                                        'width' => '1000px',
                                                                        'title' => ucwords($obj->lang['add'] . ' - ' .  $obj->lang['employee'])
                                                                    )  : '';

                                                    echo $obj->inputAutoComplete(array(
                                                                        'objRefer' => $city,
                                                                        'revalidateField' => false, 
                                                                        'element' => array('value' => 'employeeName',
                                                                                           'key' => 'hidEmployeeKey'),
                                                                        'source' =>array(
                                                                                            'url' => 'ajax-employee.php',
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
                                     <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['city']); ?></label> 
                                        <div class="col-xs-9"> 
                                             <?php  
                                                    $popupOpt = (!$isQuickAdd) ? array(
                                                                        'url' => 'cityForm.php',
                                                                        'element' => array('value' => 'cityName',
                                                                               'key' => 'hidCityKey'),
                                                                        'width' => '600px',
                                                                        'title' => ucwords($obj->lang['add'] . ' - ' .  $obj->lang['city'])
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
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['companyType']); ?></label>  
                                        <div class="col-xs-1" style="padding-right:0"> 
                                             <?php echo $obj->inputCheckBox('isService'); ?>
                                        </div>  
                                          <label class="col-xs-2 control-label" style="padding-left:0"> 
                                             <?php echo ucwords($obj->lang['service']); ?>
                                        </label>  
                                         <div class="col-xs-1" style="padding-right:0" > 
                                             <?php echo $obj->inputCheckBox('isRetail'); ?>  
                                        </div>  
                                        <label class="col-xs-2 control-label"  style="padding-left:0">  <?php echo ucwords($obj->lang['retail']); ?> </label>  
                                    </div>  
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
