<?php 
require_once '../_config.php'; 
require_once '../_include.php';  

$obj= $carRevenue;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true)); 

$formAction = 'carRevenueList'; 

$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false;

$_POST['dueDate'] = date('d / m / Y');
$_POST['trDate'] = date('d / m / Y'); 

$editWarehouseInactiveCriteria = '';

$rs = prepareOnLoadData($obj);  

if (!empty($_GET['id'])){    
    
    $_POST['hidCarKey'] = $rs[0]['carkey']; 
	if (!empty($rs[0]['carkey'])){
		$rsCar = $car->getDataRowById($rs[0]['carkey']);
		$_POST['policeNumber'] = $rsCar[0]['policenumber'];
	}
	 
	$_POST['trDesc'] = $rs[0]['trdesc']; 
	$_POST['trDate'] = $obj->formatDBDate($rs[0]['trdate'],'d / m / Y');
	$_POST['amount'] = $obj->formatNumber($rs[0]['amount']); 

    $_POST['refCode'] = $rs[0]['refcode'];
    $_POST['selWarehouse'] = $rs[0]['warehousekey'];
    $_POST['hidDriverKey'] = $rs[0]['driverkey'];  
	if (!empty($rs[0]['driverkey'])){
		$rsEmployee = $employee->getDataRowById($rs[0]['driverkey']);
		$_POST['driverName'] = $rsEmployee[0]['name'];
	}
    
    if (!empty($rs[0]['customerkey'])){
        $_POST['hidCustomerKey'] = $rs[0]['customerkey'] ;  
        $rsCustomer = $customer->getDataRowById($rs[0]['customerkey']); 
        $_POST['customerName'] = $rsCustomer[0]['name'] ;
    } 
    
	$editWarehouseInactiveCriteria = ' or '.$warehouse->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['warehousekey']);
	 
}

$arrStatus = $obj->convertForCombobox($obj->getAllStatus(),'pkey','status');   
$arrWarehouse = $class->convertForCombobox($warehouse->searchData('','',true,' and ('.$warehouse->tableName.'.statuskey = 1' .$editWarehouseInactiveCriteria.')'),'pkey','name');  

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title> 


<script type="text/javascript"> 
	
    function carIncome(tabID) { 
        
    }
    
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
                policeNumber: {  
                    validators: {
                        notEmpty: {
                            message:  phpErrorMsg.car[1]
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
                                    <div class="form-group" >
                                        <label class="col-xs-3 control-label"><?php echo $obj->lang['date']; ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputDate('trDate'); ?>   
                                        </div> 
                                    </div> 
                                   <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['warehouse']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo  $obj->inputSelect('selWarehouse', $arrWarehouse); ?> 
                                        </div> 
                                    </div>
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['reference']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputText('refCode'); ?> 
                                        </div> 
                                    </div>  
                                   
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['customer']); ?></label> 
                                        <div class="col-xs-9"> 
                                              <?php     
                                                    echo $obj->inputAutoComplete(array(
                                                                                        'objRefer' => $customer, 
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
                                                                                                        )
                                                                                      )
                                                                                );  
                                            ?>  
                                        </div> 
                                    </div> 
                                    <div class="form-group inhouse">                                    
				                        <label class="col-xs-3 control-label"><?php echo $obj->lang['car']; ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php    
                                                echo $obj->inputAutoComplete(array(
                                                                                    'objRefer' => $car,
                                                                                    'revalidateField' => true, 
                                                                                    'element' => array('value' => 'policeNumber',
                                                                                                       'key' => 'hidCarKey', 
                                                                                                      ),
                                                                                    'source' =>array(
                                                                                                        'url' => 'ajax-car.php',
                                                                                                        'data' => array(  'action' =>'searchData',
                                                                                                                           'searchField' => 'policenumber')
                                                                                                    ) , 
                                                                                    'popupForm' => array(
                                                                                                        'url' => 'carForm.php',
                                                                                                        'element' => array( 'value' => 'policeNumber', 
                                                                                                                            'valueDBField' => 'policenumber',
                                                                                                                            'key' => 'hidCarKey'),
                                                                                                        'width' => '1000px',
                                                                                                        'title' => ucwords($obj->lang['add'] . ' - ' . $obj->lang['car'])
                                                                                                    )  
                                                                                  )
                                                                            );  
                                            ?> 
                                        </div> 
                                    </div> 
                                   <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo $obj->lang['driver']; ?></label> 
                                        <div class="col-xs-9"> 
                                             <?php                 
                                                    echo $obj->inputAutoComplete(array(
                                                                                        'objRefer'=>$employee, 
                                                                                        'element' => array('value' => 'driverName',
                                                                                                           'key' => 'hidDriverKey'),
                                                                                        'source' =>array(
                                                                                                            'url' => 'ajax-employee.php',
                                                                                                            'data' => array(  'action' =>'searchData' , 
                                                                                                                              'isdriver' => 1 )
                                                                                                        ) , 
                                                                                        'popupForm' => array(
                                                                                                'url' => 'employeeForm.php',
                                                                                                'element' => array('value' => 'driverName',
                                                                                                       'key' => 'hidDriverKey'),
                                                                                                'width' => '1000px',
                                                                                                'title' => ucwords($obj->lang['add'] . ' - ' . $obj->lang['employee'])
                                                                                            ) 
                                                                                      )
                                                                                );  
                                            ?> 
                                        </div> 
                                    </div>   
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['amount']); ?></label> 
                                        <div class="col-xs-9"> 
                                           <?php echo $obj->inputNumber('amount'); ?>
                                        </div> 
                                    </div> 
                                   
                                </div>   
                    </div>   
                    
                    <div class="div-table-col"> 
                        <div class="div-tab-panel"> 
                          <div class="div-table-caption border-blue"><?php echo ucwords($obj->lang['note']); ?></div> 
                           <?php echo  $obj->inputTextArea('trDesc', array('etc' => 'style="height:10em;"')); ?> 
                        </div> 
                    </div>    
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