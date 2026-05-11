<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass(array('MedicalRecord.class.php','Customer.class.php','Warehouse.class.php','Employee.class.php'));
$medicalRecord = createObjAndAddToCol(new MedicalRecord()); 
$customer = createObjAndAddToCol(new Customer()); 
$warehouse = createObjAndAddToCol(new Warehouse()); 
$employee = createObjAndAddToCol(new Employee()); 

$obj = $medicalRecord;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true)); 

$formAction = 'medicalRecordList';

$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false;

$editWarehouseInactiveCriteria = ''; 
  
$rsDetail = array();


$defaultDate = date('d / m / Y 00:00');


$rs = prepareOnLoadData($obj);  

if (!empty($_GET['id'])){ 
	$id = $_GET['id'];	
	 
	$rsDetail = $obj->getDetailWithRelatedInformation($id);
    $_POST['name'] = $rs[0]['name'];
    
    $_POST['hidCustomerKey'] = $rs[0]['customerkey']; 
    if (!empty($rs[0]['customerkey'])){
		$rsCustomer= $customer->getDataRowById($rs[0]['customerkey']);
        $age = $customer->getCustomersAge($rs[0]['customerkey']);
            
        $rsEmployee = $employee->getDataRowById($rsCustomer[0]['saleskey']);
        $_POST['hidEmployeeKey'] = $rsCustomer[0]['saleskey'];
        
		$_POST['customerCode'] = $rsCustomer[0]['code'];
		$_POST['customerName'] = $rsCustomer[0]['name'];
		$_POST['employeeName'] = $rsEmployee[0]['name'];
		$_POST['age'] = $age;
		$_POST['address'] = $rsCustomer[0]['address'];
		$_POST['medicineAllergy'] = $rsCustomer[0]['description'];
	}

    $_POST['note'] = $rs[0]['note'];
	$_POST['selWarehouseKey'] = $rs[0]['warehousekey'];
  
        
	$editWarehouseInactiveCriteria = ' or  '.$warehouse->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['warehousekey']);  
    
}

$arrStatus = $obj->convertForCombobox($obj->getAllStatus(),'pkey','status');    
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
        
         var medicalRecord = new MedicalRecord(tabID,<?php echo json_encode($rs); ?>);
    
         prepareHandler(medicalRecord);

        
        var fieldValidation =  {
                                 code: { 
                                        validators: {
                                            notEmpty: {
                                                message: phpErrorMsg.code[1]
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
   

                                } ; 
            
        setFormValidation(getTabObj(), $('#defaultForm-' + tabID), fieldValidation, <?php echo json_encode($obj->validationFormSubmitParam()); ?>  );
        
  	  
    }); 

</script>

</head> 

<body>                    
<div style="width:100%; margin:auto; " class="tab-panel-form">   
  <div class="notification-msg"></div>
  
  <form id="defaultForm" method="post" class="form-horizontal" action="<?php echo $formAction; ?>" >
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
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['warehouse']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo  $obj->inputSelect('selWarehouseKey', $arrWarehouse); ?>
                                        </div> 
                                    </div>
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['customer']); ?> / <?php echo ucwords($obj->lang['codeMR']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <div class="flex">
                                                <div class="consume"> 
                                                           <?php    
                                                            echo $obj->inputAutoComplete(array(
                                                                                                'objRefer' => $customer,
                                                                                                'revalidateField' => true, 
                                                                                                'element' => array('value' => 'customerName',
                                                                                                                   'key' => 'hidCustomerKey'),
                                                                                                'source' =>array(
                                                                                                                    'url' => 'ajax-customer.php',
                                                                                                                    'data' => array(  'action' =>'searchData')
                                                                                                                ) ,
                                                                                                'callbackFunction' => 'getTabObj().updateSalesMan(); getTabObj().updateAgeCustomer(); getTabObj().updateCustomerInformation()'                                                                          

                                                                                              )
                                                                                        );  
                                                            ?>  
                                                </div>
                                                <div style="width:10em"><?php echo $obj->inputText('customerCode', array('readonly'=>true)); ?> </div>
                                            </div> 
                                        </div> 
                                    </div>
                                    
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['age']); ?></label> 
                                        <div class="col-xs-3"> 
                                            <?php echo $obj->inputText('age', array('readonly'=>true, 'etc'=>'style="text-align:right"' )); ?> 
                                        </div> 
                                        <div class="col-xs-6" style="padding-left:2px"> 
                                              <label class="control-label">  <?php echo ucwords($obj->lang['year']); ?> </label>                                   
                                        </div> 
                                    </div>
                                  <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['DPJP']); ?></label> 
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
                                                                                                        ),
                                                                                        'readonly'=>true 
                                                                                      )
                                                                                );  
                                            ?>  
                                        </div> 
                                    </div>
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['address']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo  $obj->inputTextArea('address', array('readonly'=>true, 'etc' => 'style="height:10em;"')); ?>
                                        </div> 
                                    </div>   
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['medicineAllergy']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo  $obj->inputTextArea('medicineAllergy', array('readonly'=>true, 'etc' => 'style="height:10em;"')); ?>
                                        </div> 
                                    </div>   
                            
                             </div>
                         
                    </div>     
                 <div class="div-table-col">
                     <div class="div-tab-panel">
                            <div class="div-table-caption border-blue"><?php echo ucwords($obj->lang['note']); ?></div>
                            <div class="form-group">
                                        <div class="col-xs-12"> 
                                            <?php echo  $obj->inputTextArea('note', array('etc' => 'style="height:10em;"')); ?>                                         </div> 
                            </div>
                     </div>
                </div>
            </div>
      </div> 

        <div class="div-table mnv-transaction transaction-detail" style="width:100%; border-bottom:1px solid #333; ">
              <div class="div-table-row">  
                    <div class="div-table-col detail-col-header" style="width:120px;text-align:center"><?php echo ucwords($obj->lang['date']); ?></div>
                    <div class="div-table-col detail-col-header" style="width:120px;"><?php echo ucwords($obj->lang['profession']); ?></div>
                    <div class="div-table-col detail-col-header" style="width:200px;"><?php echo ucwords($obj->lang['soap']); ?></div>
                    <div class="div-table-col detail-col-header" style="width:200px;"><?php echo ucwords($obj->lang['theraphy']); ?></div>
                    <div class="div-table-col detail-col-header icon-col <?php echo $obj->hideOnDisabled(); ?>"></div>
                </div>
            
				<?php  
                    $totalRows = count($rsDetail); 
            
                    for ($i=0;$i<=$totalRows; $i++){  
					 
                        $class =  'transaction-detail-row';
                        $overwrite = true;
                        $etc = '';  
                        $deleteIcon = $obj->inputLinkButton('btnDeleteRows' , '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button', 'etc' =>  'tabIndex="-1" style="padding:6px 0"'));

						if ($i == $totalRows ){
                            $class = 'medical-row-template row-template';
                            $overwrite = false;
                            $etc = 'disabled="disabled"'; 

                        } else {  
    
                            $_POST['hidDetailKey[]'] =  $rsDetail[$i]['pkey']; 
                            $_POST['trDate[]'] =  $obj->formatDBDate($rsDetail[$i]['date'],'d / m / Y H:i'); 
                            $_POST['hidEmployeeKey[]'] =  $rsDetail[$i]['employeekey']; 
                            $_POST['employeeName[]'] =  $rsDetail[$i]['employeename']; 
                            $_POST['soapDesc[]'] =  $rsDetail[$i]['soapdescription']; 
                            $_POST['theraphyDesc[]'] =  $rsDetail[$i]['therapydescription']; 
                             
                        }
				 
                ?>
            
                
                <div class="div-table-row <?php echo $class; ?>">
       
                                <div class="div-table-col detail-col-detail"  style="vertical-align:top">
                                    <?php echo $obj->inputDateTime('trDate[]',array('value' => $defaultDate,'overwritePost' => $overwrite, 'etc' => $etc.'style=" text-align:center"')); ?>
                                    <?php echo $obj->inputHidden('hidDetailKey[]',array('overwritePost' => $overwrite, 'etc' => $etc)); ?>
                                </div> 
                                <div class="div-table-col detail-col-detail"  style="vertical-align:top">
                                    <?php echo $obj->inputText('employeeName[]',array('overwritePost' => $overwrite, 'etc' => $etc)); ?>
                                    <?php echo $obj->inputHidden('hidEmployeeKey[]',array('overwritePost' => $overwrite, 'etc' => $etc)); ?>
                                </div> 
                                <div class="div-table-col detail-col-detail" ><?php echo $obj->inputTextArea('soapDesc[]',array('overwritePost' => $overwrite, 'etc' =>  $etc.'style="height:10em" placeholder="'.$obj->lang['soap'].'"')); ?></div> 
                                <div class="div-table-col detail-col-detail" ><?php echo $obj->inputTextArea('theraphyDesc[]',array('overwritePost' => $overwrite, 'etc' => 'style="height:10em" placeholder="'.$obj->lang['theraphy'].'"')); ?></div> 
                                <div class="div-table-col detail-col-detail <?php echo $obj->hideOnDisabled(); ?>"><?php echo $deleteIcon; ?></div>
         
                </div> 
 
            <?php } ?> 
                   
         </div>        
        
          <div style="clear:both; height:1em;"></div> 
          <div style="float:left; display:inline-block;"><?php echo $obj->inputButton('btnAddMedical', $obj->lang['addRows'],array('class' => 'btn btn-primary btn-second-tone')); ?></div>
       
         <div class="form-button-margin"></div>
         <div class="form-button-panel" >  
         <?php  echo $obj->generateSaveButton(array(),true);?>
        </div> 
        
    </form>  

     <?php echo $obj->showDataHistory(); ?>
</div> 
</body>

</html>
