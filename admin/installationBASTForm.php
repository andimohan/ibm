<?php
require_once '../_config.php';
require_once '../_include-v2.php'; 

includeClass('InstallationBAST.class.php');   
$installationBAST = createObjAndAddToCol( new InstallationBAST()); 
$salesOrderSubscription = createObjAndAddToCol( new SalesOrderSubscription()); 
$warehouse = createObjAndAddToCol( new Warehouse()); 
$customer = createObjAndAddToCol( new Customer()); 
$location = createObjAndAddToCol( new Location()); 
$employee = createObjAndAddToCol( new Employee());
$jobDetails = createObjAndAddToCol( new JobDetails());
$media = createObjAndAddToCol( new Media());

$obj= $installationBAST;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true));

$formAction = 'installationBASTList';

$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false;

$_POST['trDate'] = date('d / m / Y');
$_POST['activationDate'] = date('d / m / Y');
$_POST['invoiceDueDate'] = date('d / m / Y');
$editJobDetailsInactiveCriteria = '';

$rs = prepareOnLoadData($obj);

if (!empty($_GET['id'])){
    $id = $_GET['id'];

	$_POST['selWarehouseKey'] =$rs[0]['warehousekey'];
    $_POST['note'] = $rs[0]['trdesc'];
    $_POST['hidSalesOrderSubsKey'] = $rs[0]['refkey'];
    $_POST['sid'] = $rs[0]['sid'];
    $_POST['position'] = $rs[0]['position'];
    $_POST['capacity'] = $rs[0]['capacity'];
    $_POST['trDate'] = $obj->formatDBDate($rs[0]['trdate'],'d / m / Y');
    $_POST['activationDate'] = $obj->formatDBDate($rs[0]['activationdate'],'d / m / Y');
    if(!empty($rs[0]['refkey'])){
        $rsSOB = $salesOrderSubscription->getDataRowById($rs[0]['refkey']);
		$rsPIC = $employee->getDataRowById($rsSOB[0]['employeekey']);
        $_POST['SalesOrderSubsCode'] = $rsSOB[0]['code'] ;
        $_POST['selWarehouseKey'] = $rsSOB[0]['warehousekey'] ;
        $_POST['PICName'] = $rsPIC[0]['name'] ;
        $_POST['products'] = $rsSOB[0]['product'] ;
        $_POST['selJobDetails'] = $rsSOB[0]['jobdetailskey'] ;
        $editJobDetailsInactiveCriteria = ' or  '.$jobDetails->tableName.'.pkey = ' . $obj->oDbCon->paramString($rsSOB[0]['jobdetailskey']); 
    }
	$_POST['invoiceDueDate'] = $obj->formatDBDate($rs[0]['invoiceduedate'],'d / m / Y');
    $_POST['hidEmployeeKey'] = $rs[0]['employeekey'] ; 
    if(!empty($rs[0]['employeekey'])){ 
   	    $rsSales = $employee->getDataRowById($rs[0]['employeekey']);
	    $_POST['employeeName'] = $rsSales[0]['name'] ; 
	    
    }

    if(!empty($rsSOB[0]['customerkey'])){
        $rsCustomer = $customer->getDataRowById($rsSOB[0]['customerkey']);
        $_POST['customerName'] = $rsCustomer[0]['name'] ;
        $_POST['hidCustomerKey'] = $rsCustomer[0]['pkey'] ;
        $_POST['phone'] = $rsCustomer[0]['phone'] ;
        $_POST['attention'] = $rsCustomer[0]['attention'] ;
        $_POST['email'] = $rsCustomer[0]['email'] ;
        $_POST['address'] = $rsCustomer[0]['address'] ;
        
        if(!empty($rsCustomer[0]['mediakey'])){
            $rsMedia = $media->getDataRowById($rsCustomer[0]['mediakey']);
            $_POST['media'] = $rsMedia[0]['name'] ;
        }

        if(!empty($rsCustomer[0]['locationkey'])){
            $rsLocation = $location->getDataRowById($rsCustomer[0]['locationkey']);
            $_POST['locationName'] = $rsLocation[0]['name'] ;
        }
    }
}

$arrStatus = $obj->convertForCombobox($obj->getAllStatus(),'pkey','status');
$editWarehouseInactiveCriteria = ' or '.$warehouse->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['warehousekey']);
$arrWarehouse = $class->convertForCombobox($warehouse->searchData('','',true,' and ('.$warehouse->tableName.'.statuskey = 1' .$editWarehouseInactiveCriteria.')'),'pkey','name');
$arrJobDetails = $obj->convertForCombobox($jobDetails->searchData('','',true,' and ('.$jobDetails->tableName.'.statuskey = 1' .$editJobDetailsInactiveCriteria.')',' order by '.$jobDetails->tableName.'.name asc'),'pkey','name');  

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title>
<script type="text/javascript">

       jQuery(document).ready(function(){
         var tabID = <?php echo ($isQuickAdd) ?  $_GET['tabID'] :  'selectedTab.newPanel[0].id';  ?>;
         var installationBAST = new InstallationBAST(tabID);
         prepareHandler(installationBAST);

         var fieldValidation =  {
                code: {
                    validators: {
                        notEmpty: {
                            message: phpErrorMsg.code[1]
                        },
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
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['sid']); ?></label>
                                        <div class="col-xs-9">
                                            <?php echo $obj->inputText('sid'); ?>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['personincharge']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php                
                                                    echo $obj->inputAutoComplete(array(
                                                                                        'objRefer'=>$employee,
                                                                                        'revalidateField' => false, 
                                                                                        'element' => array('value' => 'employeeName',
                                                                                                           'key' => 'hidEmployeeKey'),
                                                                                        'source' =>array(
                                                                                            'url' => 'ajax-employee.php',
                                                                                            'data' => array(  'action' =>'searchData')
                                                                                        )  
                                                                                      )
                                                                                );  
                                            ?>  
                                        </div> 
                                    </div>
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['position']); ?></label>
                                        <div class="col-xs-9">
                                            <?php echo $obj->inputText('position'); ?>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['salesOrder']); ?></label>
                                        <div class="col-xs-9">
                                        <?php  echo $obj->inputAutoComplete(array(

                                                                            'element' => array('value' => 'SalesOrderSubsCode',
                                                                                               'key' => 'hidSalesOrderSubsKey'),
                                                                            'source' =>array(
                                                                                                'url' => 'ajax-sales-order-subscription.php',
                                                                                                'data' => array(  'action' =>'searchData', 'statuskey' => "(2)" )
                                                                                            ),
                                                                            'callbackFunction' => 'getTabObj().importData()'
                                                                          )
                                                                    );
                                        ?>
                                        </div>
                                     </div>
									<div class="form-group">
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['activationDate']); ?></label>
                                        <div class="col-xs-9">
                                            <?php echo $obj->inputDate('activationDate'); ?>
                                        </div>
                                    </div>
									<div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['billingDate']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputDate('invoiceDueDate'); ?> 
                                        </div> 
                                    </div> 

                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['warehouse']); ?></label>
                                        <div class="col-xs-9">
                                            <?php echo $obj->inputSelect('selWarehouseKey', $arrWarehouse ,array('readonly' => true)); ?>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['PIC']); ?></label>
                                        <div class="col-xs-9">
                                            <?php echo $obj->inputText('PICName', array('readonly' => true)); ?>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['products']); ?></label>
                                        <div class="col-xs-9">
                                            <?php echo $obj->inputText('products', array('readonly' => true)); ?>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['jobDetails']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo  $obj->inputSelect('selJobDetails', $arrJobDetails, array('readonly' => true)); ?>
                                        </div> 
                                    </div>
                                     <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['capacity']); ?></label>
                                        <div class="col-xs-9">
                                            <?php echo $obj->inputText('capacity'); ?>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['note']); ?></label>
                                        <div class="col-xs-9">
                                                <?php echo $obj->inputTextArea('note',array('etc' => 'style="height:10em;"')); ?>
                                        </div>
                                    </div>


                        </div>
                    </div>
                    <!-- collom customer   start-->
                    <div class="div-table-col">
                        <div class="div-tab-panel">
                            <div class="div-table-caption border-orange"><?php echo ucwords($obj->lang['customer']); ?></div>

                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['name']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputText('customerName', array('readonly' => true)); ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['media']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputText('media', array('readonly' => true)); ?>
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
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['location']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputText('locationName', array('readonly' => true)); ?>
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
                    <!-- collom customer   end-->
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
