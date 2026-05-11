<?php 
require_once '../_config.php'; 
require_once '../_include.php'; 

$obj= $salesOrderDumper;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true));
     
$formAction = 'salesOrderDumperList';
  
$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false;
$_POST['trDate'] = date('d / m / Y');
$rs = prepareOnLoadData($obj); 
$arrDestination = array();

if (!empty($_GET['id'])){ 
	$id = $_GET['id'];
  
    $_POST['weight'] = $obj->formatNumber($rs[0]['weight'],2);
    $_POST['distance'] = $obj->formatNumber($rs[0]['distance']); 
    $_POST['pricePerDistance'] = $obj->formatNumber($rs[0]['price']); 
    $_POST['totalPrice'] = $obj->formatNumber($rs[0]['total']);
    
    $_POST['trDesc'] = $rs[0]['trdesc']; 
    $_POST['trDate'] = $obj->formatDBDate($rs[0]['trdate'],'d / m / Y');
    
    $_POST['hidDriverKey'] = $rs[0]['driverkey']; 
    if (!empty($rs[0]['driverkey'])){
		$rsEmployee = $employee->getDataRowById($rs[0]['driverkey']);
		$_POST['driverName'] = $rsEmployee[0]['name'];
	}
     
    if (!empty($rs[0]['carkey'])){
        $rsCar = $car->getDataRowById($rs[0]['carkey']);
        $_POST['policeNumber'] = $rsCar[0]['code']. ' - '.$rsCar[0]['policenumber']; 
    }
    
    if (!empty($rs[0]['refkey'])){
        $rsProject = $projectDumper->getDataRowById($rs[0]['refkey']); 
        $_POST['hidProjectKey'] = $rsProject[0]['pkey'];
        $_POST['projectName'] = $rsProject[0]['code']. ' - '.$rsProject[0]['name'];
        if(!empty($rsProject[0]['locationkey'])){
            $rsLocation = $location->getDataRowById($rsProject[0]['locationkey']);
            $_POST['location'] = $rsLocation[0]['name'];
        }
    }
      
    $arrDestination = $class->convertForCombobox($projectDumper->getDetailWithRelatedInformation($rs[0]['refkey']),'locationkey','locationname');
} 
$arrWarehouse = $obj->convertForCombobox($warehouse->searchData('','',true,' and ('.$warehouse->tableName.'.statuskey = 1' .$editWarehouseInactiveCriteria.')'),'pkey','name');  
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
        var salesOrderDumper  = new SalesOrderDumper(tabID);
    
         prepareHandler(salesOrderDumper);
			 
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
				projectName: { 
                    validators: {
                        notEmpty: {
                            message: phpErrorMsg.project[1]
                        }, 
                    }
				},
 
                policeNumber: { 
                    validators: {
                        notEmpty: {
                            message:  phpErrorMsg.car[1]
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
                                            <?php echo  $obj->inputSelect('selWarehouseKey', $arrWarehouse); ?>
                                        </div> 
                                    </div>
                                    
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['project']); ?></label>  
                                        <div class="col-xs-9"> 
                                         <?php    
                                                echo $obj->inputAutoComplete(array(
                                                                                'objRefer' => $projectDumper,
                                                                                'element' => array('value' => 'projectName',
                                                                                                   'key' => 'hidProjectKey'),
                                                                                'source' =>array(
                                                                                                    'url' => 'ajax-project-dumper.php',
                                                                                                    'data' => array(  'action' =>'searchData', 'statuskey' => "(2)", 'searchField' => 'code,name')
                                                                                ),
                                                                                'callbackFunction' => 'getTabObj().updateProjectInformation()'   
                                                                              )
                                                                        );  
                                            ?> 
                                        </div> 
                                    </div> 
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['location']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo  $obj->inputText('location', array('readonly' => true)); ?> 
                                        </div> 
                                    </div>
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['destination']); ?></label> 
                                        <div class="col-xs-9"> 
                                           <?php echo  $obj->inputSelect('selDestination', $arrDestination); ?> 
                                        </div> 
                                    </div>
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['carRegistrationNumber']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php    
                                                echo $obj->inputAutoComplete(array(
                                                                                    'objRefer' => $car,
                                                                                    'revalidateField' => false, 
                                                                                    'element' => array('value' => 'policeNumber',
                                                                                                       'key' => 'hidCarKey', 
                                                                                                      ),
                                                                                    'source' =>array(
                                                                                                        'url' => 'ajax-car.php',
                                                                                                        'data' => array(  'action' =>'searchData', 'searchField' => 'code,policenumber')
                                                                                                    ) , 
                                                                                    'popupForm' => array(
                                                                                                        'url' => 'carForm.php',
                                                                                                        'element' => array( 'value' => 'policeNumber', 'valueDBField' => 'codepolicenumber',
                                                                                                                            'key' => 'hidCarKey'),
                                                                                                        'width' => '1000px',
                                                                                                        'title' => ucwords($obj->lang['add'] . ' - ' . $obj->lang['car'])
                                                                                                    )  
                                                                                  )
                                                                            );  
                                            ?> 
                                        </div> 
                                    </div>
                                    <div class="form-group inhouse">
                                        <label class="col-xs-3 control-label"><?php echo $obj->lang['driver']; ?></label> 
                                        <div class="col-xs-9"> 
                                             <?php                
                                                    echo $obj->inputHidden('hidBeforeDriverKey');
                                                    echo $obj->inputAutoComplete(array(
                                                                                        'objRefer'=>$employee,
                                                                                        'revalidateField' => false, 
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
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['weight']). ' <span class="text-muted">(Kg)</span>'; ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo  $obj->inputDecimal('weight'); ?> 
                                        </div> 
                                    </div>
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['distance']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo  $obj->inputNumber('distance', array('readonly' => true)); ?> 
                                        </div> 
                                    </div>
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['price']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo  $obj->inputNumber('pricePerDistance', array('readonly' => true)); ?> 
                                        </div> 
                                    </div>
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['total']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo  $obj->inputNumber('totalPrice', array('readonly' => true)); ?> 
                                        </div> 
                                    </div>
                        
                            </div>   
                  </div> 
                  
                    <div class="div-table-col"> 
                        <div class="div-tab-panel"> 
                            <div class="div-table-caption border-blue"><?php echo ucwords($obj->lang['note']); ?></div>
                            <div class="form-group"> 
                                <div class="col-xs-12"> 
                                    <?php echo  $obj->inputTextArea('trDesc',array('etc' => 'style="height:10em;"' )); ?>
                                </div> 
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
