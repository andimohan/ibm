<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php';
includeClass('RentalTimeSheet.class.php');
$rentalTimeSheet = createObjAndAddToCol( new RentalTimeSheet());

$salesOrderRental = createObjAndAddToCol( new SalesOrderRental());
$car = createObjAndAddToCol( new Car());
$customer = createObjAndAddToCol( new Customer());
$employee = createObjAndAddToCol( new Employee()); 
$location = createObjAndAddToCol( new Location()); 
$city = createObjAndAddToCol( new City()); 
$warehouse = createObjAndAddToCol( new Warehouse()); 
$itemUnit = createObjAndAddToCol( new ItemUnit()); 
$timeUnit = createObjAndAddToCol( new TimeUnit()); 
$item = createObjAndAddToCol( new Item()); 

$obj = $rentalTimeSheet;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true)); 

$formAction = 'rentalTimeSheetList';

$parentFileName = $_GET['fileName'];
$parentPanelId = $_GET['selectedPanelId'];
$parentTitle = $_GET['title'];

$editWarehouseInactiveCriteria = '';  
$editSalesInactiveCriteria = ''; 
$editLocationInactiveCriteria = ''; 
$editCityInactiveCriteria = ''; 
$editCustomCodeInactiveCriteria = '';
 
$rsTimeDetail = array();
$rsSODetail = array(); 

$_POST['trDate'] = date('d / m / Y');
$defaultWorkDate = date('d / m / Y 00:00');
$_POST['chkIsUnlimited[]'] = 0;

$saleskey = base64_decode($_SESSION[$obj->loginAdminSession]['id']); 
$_POST['selSalesKey'] = $saleskey;
 
$finalDiscDecimal = 0;
$finalDiscDecimalType = 'inputnumber';

$totalWeight = 0;
 
$rs = prepareOnLoadData($obj);  

if (!empty($_GET['id'])){ 
	$id = $_GET['id'];	
	 
	$rsTimeDetail = $obj->getDetailById($id); 
	
	$rsSO = $salesOrderRental->getDataRowById($rs[0]['refkey']);
	$rsSODetail = $salesOrderRental->getDetailWithRelatedInformation($rsSO[0]['pkey']);
	 
    //$_POST['selCustomCode'] = $rs[0]['customcodekey']; 
	$_POST['trDate'] = $obj->formatDBDate($rs[0]['trdate'],'d / m / Y');
	$_POST['selWarehouseKey'] = $rs[0]['warehousekey']; 
	$_POST['selJODetailKey'] = $rs[0]['refsodetailkey']; 
	$_POST['trDesc'] = $rs[0]['trdesc'];
	$_POST['hidEmployeeKey'] = $rs[0]['employeekey'];
	if(!empty($rs[0]['employeekey'])){ 
   	    $rsEmployee = $employee->getDataRowById($rs[0]['employeekey']);
	   	$_POST['employeeName'] = $rsEmployee[0]['name'] ; 
    }
	
	$_POST['hidCarKey'] = $rs[0]['carkey']; 
	if (!empty($rs[0]['carkey'])){
		$rsCar = $car->getDataRowById($rs[0]['carkey']);
		$_POST['policeNumber'] = $rsCar[0]['code']. ' - ' . $rsCar[0]['policenumber'];
	}
    
	$_POST['hidRefkey'] = $rs[0]['refkey'] ; 
    if(!empty($rs[0]['refkey'])){
		$rsCustomer = $customer->getDataRowById($rsSO[0]['customerkey']);
		$_POST['refCode'] = $rsSO[0]['code'] ;
        $_POST['recipientName'] = $rsCustomer[0]['name'] ;
        $_POST['hidRecipientKey'] = $rsCustomer[0]['pkey'] ;  
        $_POST['salesOrderCode'] = $rsSO[0]['code'] ;
        $_POST['recipientPhone'] = $rsSO[0]['recipientphone'];
        $_POST['recipientEmail'] = $rsSO[0]['recipientemail'];
        $_POST['recipientAddress'] = $rsSO[0]['recipientaddress'];
        $_POST['hidRecipientCityKey'] = $rsSO[0]['recipientcitykey'];
        if(!empty($rsSO[0]['recipientcitykey'])){ 
            $rsCity = $city->searchData($city->tableName.'.pkey',$rsSO[0]['recipientcitykey'],true);
            $_POST['recipientCityName'] = $rsCity[0]['citycategoryname']; 
        }
    } 
     
  
	$editWarehouseInactiveCriteria = ' or  '.$warehouse->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['warehousekey']);  
	$editSalesInactiveCriteria = 'or '.$employee->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['saleskey']);
    //$editCustomCodeInactiveCriteria = ' or  '.$customCode->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['customcodekey']); 
}
  

$arrStatus = $obj->convertForCombobox($obj->getAllStatus(),'pkey','status');    
$arrWarehouse = $obj->convertForCombobox($warehouse->searchData('','',true,' and ('.$warehouse->tableName.'.statuskey = 1' .$editWarehouseInactiveCriteria.')'),'pkey','name');  
$arrSales = $obj->convertForCombobox($employee->searchData('','',true, ' and ('.$employee->tableName.'.statuskey = 2 ' .$editSalesInactiveCriteria.')'),'pkey','name'); 
$arrDefaultUnit = $obj->convertForCombobox($itemUnit->searchData('','',true, ' and ('.$itemUnit->tableName.'.statuskey = 1 )'),'pkey','name'); 
$arrTimeUnit = $timeUnit->searchData('','',true, ' and ('.$timeUnit->tableName.'.statuskey = 1 )');
$arrDefaultTimeUnit = $obj->convertForCombobox($arrTimeUnit,'pkey','name');  
$arrJO = $obj->convertForCombobox($rsSODetail,'pkey','label');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title> 
 
<script type="text/javascript">  
   
	jQuery(document).ready(function(){  
        var tabID = selectedTab.newPanel[0].id;
        var arrDetails = {};
        rentalTimeSheet = new RentalTimeSheet(tabID,<?php echo json_encode($rs); ?>);
        prepareHandler(rentalTimeSheet); 
        
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
                                                message:  phpErrorMsg.name[1]
                                            }
                                        } 
                                    },
            
                                   refCode: { 
                                        validators: {
                                            notEmpty: {
                                                message:  phpErrorMsg.salesOrderRental[1]
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
    <?php echo $obj->inputHidden('hidSendEmail'); ?>  
     
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
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['JOCode']); ?></label> 
                                        <div class="col-xs-9"> 
                                              <?php  echo $obj->inputAutoComplete(array( 
                                                                                'objRefer' => $salesOrderRental,
                                                                                'revalidateField' => true,
                                                                                'element' => array('value' => 'refCode',
                                                                                                   'key' => 'hidRefkey'),
                                                                                'source' =>array(
                                                                                                    'url' => 'ajax-sales-order-rental.php',
                                                                                                    'data' => array(  'action' =>'searchData' )
                                                                                                ),
                                                                                'callbackFunction' => 'getTabObj().updateSalesOrderInformation();'
                                                                              )
                                                                        );  
                                            ?> 
                                        </div> 
                                    </div> 
								 	<div class="form-group">                                    
				                <label class="col-xs-3 control-label"><?php echo $obj->lang['car']; ?></label> 
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
                                                                                    'allowedStatusForEdit' => array(1,2),
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
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['employee']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php                
                                                    echo $obj->inputAutoComplete(array(
                                                                                        'objRefer'=>$employee,
                                                                                        'revalidateField' => false, 
                                                                                        'element' => array('value' => 'employeeName',
                                                                                                           'key' => 'hidEmployeeKey'),
                                                                                        'source' =>array(
                                                                                            'url' => 'ajax-employee.php',
                                                                                            'data' => array(  'action' =>'searchData' )
                                                                                        )  
                                                                                      )
                                                                                );  
                                            ?>  
                                        </div> 
                                    </div>
								 
								 	<div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['occupation']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo  $obj->inputSelect('selJODetailKey', $arrJO); ?>
                                        </div> 
                                    </div> 
								 
								 <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['note']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo  $obj->inputTextArea('trDesc', array('etc' => 'style="height:10em;"')); ?>
                                        </div> 
                                    </div>  
                                 
                             </div>
                         
                    </div>
                     <div class="div-table-col">   
                                               <div class="div-tab-panel"> 
                            <div class="div-table-caption border-blue"><?php echo ucwords($obj->lang['customerInformation']); ?></div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['name']); ?></label> 
                                <div class="col-xs-9"> 
                                <?php  echo $obj->inputAutoComplete(array(
                                                                                'readonly' => true,
                                                                                'element' => array('value' => 'recipientName',
                                                                                                   'key' => 'hidRecipientKey')
                                                                              )
                                                                        );  
                                            ?> 
                                </div> 
                            </div> 
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['phone']); ?></label> 
                                <div class="col-xs-9"> 
                                    <?php echo $obj->inputText('recipientPhone',array('readonly' => true)); ?> 
                                </div> 
                            </div> 
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['email']); ?></label> 
                                <div class="col-xs-9"> 
                                    <?php echo $obj->inputText('recipientEmail',array('readonly' => true)); ?>  
                                </div> 
                            </div> 
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['city']); ?></label> 
                                <div class="col-xs-9"> 
                                     <?php  echo $obj->inputAutoComplete(array(
                                                                'objRefer' => $city,
                                                                'revalidateField' => false, 
                                                                'readonly' => true,
                                                                'element' => array('value' => 'recipientCityName',
                                                                                   'key' => 'hidRecipientCityKey'),
                                                                'source' =>array(
                                                                                    'url' => 'ajax-city.php',
                                                                                    'data' => array(  'action' =>'searchData' )
                                                                                )                                                               
                                                                )
                                                        );  
                                    ?> 
                                </div> 
                            </div> 
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['address']); ?></label> 
                                <div class="col-xs-9"> 
                                    <?php echo  $obj->inputTextArea('recipientAddress', array('etc' => 'style="height:10em;"', 'readonly' => true)); ?> 
                                </div> 
                            </div>  
                        </div>       
                    </div>
           </div>
      </div> 
      
        <div class="div-table mnv-transaction transaction-detail" style="width:100%; border-bottom:1px solid #333; ">
                <div class="div-table-row"> 
                    <div class="div-table-col detail-col-header" style="text-align:center;"><?php echo ucwords($obj->lang['start']); ?></div>
                    <div class="div-table-col detail-col-header" style="width:130px; text-align:center;"><?php echo ucwords($obj->lang['break']); ?></div>
                    <div class="div-table-col detail-col-header" style="width:130px; text-align:center;"><?php echo ucwords($obj->lang['start']); ?></div>
                    <div class="div-table-col detail-col-header" style="width:130px; text-align:center;"><?php echo ucwords($obj->lang['break']); ?></div>
                    <div class="div-table-col detail-col-header" style="width:130px; text-align:center;"><?php echo ucwords($obj->lang['end']); ?></div>
					<div class="div-table-col detail-col-header" style="width:90px; text-align:right;"><?php echo ucwords($obj->lang['workingTime']); ?></div>
					<div class="div-table-col detail-col-header" style="width:80px; text-align:right;"><?php echo ucwords($obj->lang['workHour']); ?></div>
					<div class="div-table-col detail-col-header" style="width:80px; text-align:right;"><?php echo ucwords($obj->lang['overTime']); ?></div>
                    <div class="div-table-col detail-col-header  <?php echo $obj->hideOnDisabled(); ?> icon-col"></div>
                </div>
                
				<?php 
                    $totalRows = count($rsTimeDetail); 
            
                    for ($i=0;$i<=$totalRows; $i++){  
						$readonlyServices =1;	
                        $class =  'transaction-detail-row';
                        $overwrite = true;
                        $disabled = false; 
                        $disabledDays = false; 
                         
                        if ($i == $totalRows ){
                            $class = 'detail-row-template';
                            $overwrite = false;
                            $disabled = true;  
                        } else {  
                            $decimal = 0;
                            $inputnumber = 'inputnumber';
  
                            $_POST['hidDetailKey[]'] =  $rsTimeDetail[$i]['pkey'];
							$_POST['workTime[]'] =   $obj->formatNumber($rsTimeDetail[$i]['worktime']); 
							$_POST['workHour[]'] =   $obj->formatNumber($rsTimeDetail[$i]['workhour']); 
							$_POST['overTime[]'] =   $obj->formatNumber($rsTimeDetail[$i]['overtime']); 
							$_POST['trStartDate[]'] =   $obj->formatDBDate($rsTimeDetail[$i]['startdate'],'d / m / Y H:i');
							$_POST['trStartDate2[]'] =   $obj->formatDBDate($rsTimeDetail[$i]['startdate2'],'d / m / Y H:i');
							$_POST['trRestDate[]'] =   $obj->formatDBDate($rsTimeDetail[$i]['restdate'],'d / m / Y H:i');
							$_POST['trRestDate2[]'] =   $obj->formatDBDate($rsTimeDetail[$i]['restdate2'],'d / m / Y H:i');
							$_POST['trEndDate[]'] =   $obj->formatDBDate($rsTimeDetail[$i]['endate'],'d / m / Y H:i');
                        } 
				 
                ?>
            
                
                <div class="div-table-row <?php echo $class; ?>">
                    <div class="div-table-col detail-col-detail">
						<?php echo $obj->inputDateTime('trStartDate[]', array('value' => $defaultWorkDate,'overwritePost' => $overwrite, 'disabled' => $disabled,  'etc' => 'style="text-align:center;" ' )); ?>
                        <?php echo $obj->inputHidden('hidDetailKey[]',array('overwritePost' => $overwrite, 'disabled' => $disabled)); ?>
                    </div> 
                    <div class="div-table-col detail-col-detail"><?php echo $obj->inputDateTime('trRestDate[]', array('value' => $defaultWorkDate,'overwritePost' => $overwrite, 'disabled' => $disabled, 'etc' => 'style="text-align:center;" ' )); ?></div>
                    <div class="div-table-col detail-col-detail"><?php echo $obj->inputDateTime('trStartDate2[]', array('value' => DEFAULT_EMPTY_DATE_Time,'overwritePost' => $overwrite, 'disabled' => $disabled,   'etc' => 'style="text-align:center;" ' )); ?></div>
                    <div class="div-table-col detail-col-detail"><?php echo $obj->inputDateTime('trRestDate2[]', array('value' => DEFAULT_EMPTY_DATE_Time,'overwritePost' => $overwrite, 'disabled' => $disabled,  'etc' => 'style="text-align:center;" ' )); ?></div>
                    <div class="div-table-col detail-col-detail"><?php echo $obj->inputDateTime('trEndDate[]', array('value' => $defaultWorkDate,'overwritePost' => $overwrite, 'disabled' => $disabled, 'etc' => 'style="text-align:center;" ' )); ?></div>
                    <div class="div-table-col detail-col-detail"><?php echo $obj->inputNumber('workTime[]', array('overwritePost' => $overwrite, 'etc' => 'style="text-align:right;"' , 'disabled' => $disabled)); ?></div>
                    <div class="div-table-col detail-col-detail"><?php echo $obj->inputNumber('workHour[]', array('overwritePost' => $overwrite, 'etc' => 'style="text-align:right;"' , 'disabled' => $disabled)); ?></div>
                    <div class="div-table-col detail-col-detail"><?php echo $obj->inputNumber('overTime[]', array('overwritePost' => $overwrite, 'etc' => 'style="text-align:right;"' , 'disabled' => $disabled)); ?></div>
                    <div class="div-table-col detail-col-detail <?php echo $obj->hideOnDisabled(); ?>"><?php echo $obj->inputLinkButton('btnDeleteRows' , '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button', 'etc' => 'tabIndex="-1"')); ?></div>
                </div>
                 
            <?php } ?> 
                   
         </div>           
          
          <div style="clear:both; height:1em;"></div> 
          <div style="float:left; display:inline-block;"><?php echo $obj->inputButton('btnAddRows', $obj->lang['addRows'], array('class' => 'btn btn-primary btn-second-tone')); ?></div>
       
         
      
         <div class="form-button-margin"></div>
         <div class="form-button-panel" >  
         <?php  echo $obj->generateSaveButton(array(),true);?> 
        </div> 
        
    </form>  

     <?php echo $obj->showDataHistory(); ?>
</div> 
</body>

</html>
