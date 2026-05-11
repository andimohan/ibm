<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass('SalesOrderSubscription.class.php');   
$salesOrderSubscription = createObjAndAddToCol( new SalesOrderSubscription()); 
$installationWorkOrder = createObjAndAddToCol( new InstallationWorkOrder());
$warehouse = createObjAndAddToCol( new Warehouse()); 
$customer = createObjAndAddToCol( new Customer()); 
$employee = createObjAndAddToCol( new Employee()); 
$location = createObjAndAddToCol( new Location()); 
$jobDetails = createObjAndAddToCol( new JobDetails()); 
$itemUnit = createObjAndAddToCol( new ItemUnit()); 
$media = createObjAndAddToCol( new Media()); 
$stagesProcess = createObjAndAddToCol( new StagesProcess()); 
$item = createObjAndAddToCol( new Item()); 
$invoicePeriod = createObjAndAddToCol( new InvoicePeriod()); 


$obj = $salesOrderSubscription;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true)); 

$formAction = 'salesOrderSubscriptionList';

$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false;

$editWarehouseInactiveCriteria = ''; 
$editJobDetailsInactiveCriteria = ''; 
$editInvoicePeriodInactiveCriteria = ''; 
 
$rsSalesDetail = array();
$rsMonthly = array();
$rsWOExternal = array();

$_POST['trDate'] = date('d / m / Y');
//$_POST['invoiceDueDate'] = date('d / m / Y');

$rs = prepareOnLoadData($obj);  

if (!empty($_GET['id'])){ 
	$id = $_GET['id'];	
	 
	$rsSalesDetail = $obj->getDetailWithRelatedInformation($id);
	$rsMonthly = $obj->getMonthlyDetailRelatedInformation($id);
	$rsWOExternal = $installationWorkOrder->searchData ('salesorderkey',$id,true,' and ('.$installationWorkOrder->tableName.'.statuskey in (1,2,3) )',' order by '.$installationWorkOrder->tableName.'.stagekey desc limit 1');
    if(!empty($rsWOExternal))
        $_POST['selStageKey'] = $rsWOExternal[0]['stagekey'];
    
	$_POST['trDate'] = $obj->formatDBDate($rs[0]['trdate'],'d / m / Y');
	$_POST['invoiceDueDate'] = $obj->formatDBDate($rs[0]['invoiceduedate'],'d / m / Y');
	$_POST['selWarehouseKey'] =$rs[0]['warehousekey']; 
	$_POST['selInvoiceRecurring'] =$rs[0]['periodekey']; 
	$rsCustomer = $customer->getDataRowById($rs[0]['customerkey']);
	$_POST['customerName'] = $rsCustomer[0]['name'];
	$_POST['hidCustomerKey'] = $rsCustomer[0]['pkey']; 
    if(!empty($rsCustomer[0]['pkey'])){
        $_POST['phone'] = $rsCustomer[0]['phone'];
        $_POST['address'] = $rsCustomer[0]['address'];
        $_POST['selMedia'] = $rsCustomer[0]['mediakey'];
        $_POST['attention'] = $rsCustomer[0]['attention'];
        $rsLocation = $location->getDataRowById($rsCustomer[0]['locationkey']);
        $_POST['locationName'] = $rsLocation[0]['name'];  
    }
    
    if (!empty($rs[0]['employeekey'])){ 
        $_POST['hidEmployeeKey'] =  $rs[0]['employeekey'] ;
        $rsEmployee = $employee->getDataRowById($rs[0]['employeekey']);
        $_POST['employeeName'] = $rsEmployee[0]['name'];
    }
	
	$_POST['product'] = $rs[0]['product'];
	$_POST['selJobDetails'] = $rs[0]['jobdetailskey'];
	$_POST['isPostPaid'] = $rs[0]['ispostpaid'];
	
	$_POST['trDesc'] = $rs[0]['trdesc'];
	$_POST['subtotal'] = $obj->formatNumber($rs[0]['subtotal']);  
	$_POST['subtotalMonthly'] = $obj->formatNumber($rs[0]['subtotalmonthly']);  

	$_POST['total'] = $obj->formatNumber($rs[0]['grandtotal']);
	$_POST['totalMonthly'] = $obj->formatNumber($rs[0]['grandtotalmonthly']);
	$_POST['beforeTaxTotal'] =  $obj->formatNumber($rs[0]['beforetaxtotal']);
    $_POST['chkIncludeTax'] = $rs[0]['ispriceincludetax'];
	$_POST['taxPercentage'] = $obj->formatNumber($rs[0]['taxpercentage'],2);
	$_POST['taxValue'] = $obj->formatNumber($rs[0]['taxvalue']);
    
    $_POST['beforeTaxTotalMonthly'] =  $obj->formatNumber($rs[0]['beforetaxtotalmonthly']);
    $_POST['chkIncludeTaxMonthly'] = $rs[0]['ispriceincludetaxmonthly'];
	$_POST['taxPercentageMonthly'] = $obj->formatNumber($rs[0]['taxpercentagemonthly'],2);
	$_POST['taxValueMonthly'] = $obj->formatNumber($rs[0]['taxvaluemonthly']);
	$_POST['selInvoicePeriodeTime'] = $rs[0]['invoiceperiodkey'];
    
	$editWarehouseInactiveCriteria = ' or  '.$warehouse->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['warehousekey']);  
    $editJobDetailsInactiveCriteria = ' or  '.$jobDetails->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['jobdetailskey']);  
    $editInvoicePeriodInactiveCriteria = ' or  '.$invoicePeriod->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['invoiceperiodkey']);  

}
$arrStatus = $obj->convertForCombobox($obj->getAllStatus(),'pkey','status');   
$arrJobDetails = $obj->convertForCombobox($jobDetails->searchData('','',true,' and ('.$jobDetails->tableName.'.statuskey = 1' .$editJobDetailsInactiveCriteria.')',' order by '.$jobDetails->tableName.'.name asc'),'pkey','name');  
$arrWarehouse = $obj->convertForCombobox($warehouse->searchData('','',true,' and ('.$warehouse->tableName.'.statuskey = 1' .$editWarehouseInactiveCriteria.')'),'pkey','name');  
$arrPeriod = $obj->convertForCombobox($invoicePeriod->searchData('','',true,' and ('.$invoicePeriod->tableName.'.statuskey = 1' .$editInvoicePeriodInactiveCriteria.')'),'pkey','name');  
$arrDefaultUnit = $obj->convertForCombobox($itemUnit->searchData('','',true, ' and ('.$itemUnit->tableName.'.statuskey = 1 )'),'pkey','name'); 
$arrMedia = $class->convertForCombobox($media->searchData ('','',true,' and ('.$media->tableName.'.statuskey = 1 )'),'pkey','name');    
$arrStageProcess = $class->convertForCombobox($stagesProcess->searchData ('','',true,' and ('.$stagesProcess->tableName.'.statuskey = 1 )',' order by '.$stagesProcess->tableName.'.orderlist asc'),'pkey','name'); 
$arrPeriodeInvoice = $class->convertForCombobox($obj->getInvoicePeriode(),'pkey','name');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title> 
 
<script type="text/javascript">  
   
	jQuery(document).ready(function(){  
        var tabID = <?php echo ($isQuickAdd) ?  $_GET['tabID'] :  'selectedTab.newPanel[0].id';  ?>  
        
         var salesOrderSubscription = new SalesOrderSubscription(tabID, <?php echo json_encode($rs); ?>);
    
         prepareHandler(salesOrderSubscription);
        
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
                                   /*locationName: { 
                                        validators: {
                                            notEmpty: {
                                                message:  phpErrorMsg.location[1]
                                            }
                                        } 
                                    },*/

                                } ; 
        
        setFormValidation(getTabObj(), $('#defaultForm-' + tabID), fieldValidation, <?php echo json_encode($obj->validationFormSubmitParam()); ?>  );
        <?php if (!empty($rs) && empty($rsSalesDetail) ){ ?>     
        	var newRow = addNewTemplateRow("detail-row-template"); 
    	<?php } ?> 
  	  
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
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['invoiceRecurring']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo  $obj->inputSelect('selInvoiceRecurring', $arrPeriodeInvoice); ?>
                                        </div> 
                                    </div>
								 	<div class="form-group recurring">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['billingDate']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputDate('invoiceDueDate', array('readonly' => true)); ?> 
                                        </div> 
                                    </div> 
								 	
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['PIC']); ?></label>  
                                        <div class="col-xs-9"> 
                                         <?php    
                                                echo $obj->inputAutoComplete(array(
                                                                                'objRefer' => $employee,
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
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['products']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputText('product'); ?> 
                                        </div> 
                                    </div>
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['jobDetails']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo  $obj->inputSelect('selJobDetails', $arrJobDetails); ?>
                                        </div> 
                                    </div>
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['stagesProcess']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo  $obj->inputSelect('selStageKey', $arrStageProcess, array('readonly' => true)); ?>
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
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['customer']); ?></label> 
                                        <div class="col-xs-9"> 
                                              <?php  echo $obj->inputAutoComplete(array( 
                                                                                'objRefer' => $customer,
                                                                                'revalidateField' => true,
                                                                                'element' => array('value' => 'customerName',
                                                                                                   'key' => 'hidCustomerKey'),
                                                                                'source' =>array(
                                                                                                    'url' => 'ajax-customer.php',
                                                                                                    'data' => array(  'action' =>'searchData', 
                                                                                                                              'statuskey' => 2  )
                                                                                                ),
                                                                                'callbackFunction' => 'getTabObj().updateCustomerInformation()'                                                                           
                                                                                )
                                                                        );  
                                            ?> 
                                        </div> 
                                    </div>
 
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['media']); ?></label> 
                                        <div class="col-xs-9"> 
                                           <?php echo  $obj->inputSelect('selMedia', $arrMedia, array('readonly' => true)); ?> 
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
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['location']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputText('locationName', array('readonly' => true)); ?> 
                                        </div> 
                                    </div>  
                                      
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['address']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo  $obj->inputTextArea('address', array('readonly' => true,'etc' => 'style="height:10em;"')); ?>
                                        </div> 
                                    </div>  
                            </div>   
                    </div>
           </div>
      </div>   
        <div class="div-table mnv-transaction transaction-detail" style="width:100%; border-bottom:1px solid #333; ">
                <div class="div-table-caption"><h2><?php echo ucwords($obj->lang['initialCost']); ?></h2></div>
                <div class="div-table-row"> 
                    <div class="div-table-col detail-col-header" style="width:30px; text-align:right;">#</div>
                    <div class="div-table-col detail-col-header"><?php echo ucwords($obj->lang['description']); ?></div>
                    <div class="div-table-col detail-col-header" style="width:80px; text-align:right;"><?php echo ucwords($obj->lang['qty']); ?></div>
                    <!--<div class="div-table-col detail-col-header" style="width:80px;"><?php echo ucwords($obj->lang['unit']); ?></div>-->
                    <div class="div-table-col detail-col-header" style="width:120px; text-align:right;"><?php echo ucwords($obj->lang['price']); ?> @</div>
                    <div class="div-table-col detail-col-header" style="width:180px; text-align:right;"><?php echo ucwords($obj->lang['subtotal']); ?></div>
                    <div class="div-table-col detail-col-header  <?php echo $obj->hideOnDisabled(); ?> icon-col"></div>
                </div>
                
				<?php 
                    $totalRows = count($rsSalesDetail); 
            
                    for ($i=0;$i<=$totalRows; $i++){  
							
                        $class =  'transaction-detail-row';
                        $overwrite = true;
                        $disabled = false; 
                        $arrUnit = $arrDefaultUnit;

                        if ($i == $totalRows ){
                            $class = 'detail-row-template';
                            $overwrite = false;
                            $disabled = true; 
                            $unitname = 'Pcs';
                        } else {  
                            $decimal = 0;
                            $inputnumber = 'inputnumber';
                            
                            $_POST['hidDetailKey[]'] =  $rsSalesDetail[$i]['pkey'];
                            $_POST['hidItemKey[]'] =  $rsSalesDetail[$i]['itemkey']; 
                            $_POST['itemName[]'] =  $rsSalesDetail[$i]['itemname']; 
                            $_POST['qty[]'] =   $obj->formatNumber($rsSalesDetail[$i]['qty']); 
                            $_POST['priceInUnit[]'] =   $obj->formatNumber($rsSalesDetail[$i]['priceinunit']); 
                            $_POST['detailSubtotal[]'] =   $obj->formatNumber($rsSalesDetail[$i]['total']);  
                            //$_POST['selUnit[]'] =  $rsSalesDetail[$i]['unitkey']; 
                            //$arrUnit = $obj->convertForCombobox($item->getAvailableUnit($rsSalesDetail[$i]['itemkey']),'conversionunitkey','unitname','',array('relconversionmultiplier' => 'conversionmultiplier')); 
                 
                        } 
				 
                ?>
            
                
                <div class="div-table-row <?php echo $class; ?>">
                    <div class="div-table-col detail-col-detail" style="text-align:right;"><div class="row-number"></div></div>
                    <div class="div-table-col detail-col-detail">
                        <?php echo $obj->inputText('itemName[]',array('overwritePost' => $overwrite, 'disabled' => $disabled)); ?>
                        <?php echo $obj->inputHidden('hidItemKey[]',array('overwritePost' => $overwrite,'disabled' => $disabled)); ?>
                        <?php echo $obj->inputHidden('hidDetailKey[]',array('overwritePost' => $overwrite, 'disabled' => $disabled)); ?>
                    </div> 
                    <div class="div-table-col detail-col-detail"><?php echo $obj->inputNumber('qty[]', array('overwritePost' => $overwrite, 'etc' => 'style="text-align:right;"', 'disabled' => $disabled)); ?></div>
                    <!--<div class="div-table-col detail-col-detail"><?php echo $obj->inputSelect('selUnit[]',$arrUnit, array('overwritePost' => $overwrite,  'disabled' => $disabled)); ?></div> -->              
                    <div class="div-table-col detail-col-detail"><?php echo $obj->inputNumber('priceInUnit[]', array('overwritePost' => $overwrite, 'etc' => 'style="text-align:right;"' , 'disabled' => $disabled)); ?></div>
                    <div class="div-table-col detail-col-detail"><?php echo $obj->inputNumber('detailSubtotal[]', array('overwritePost' => $overwrite, 'readonly'=>true, 'etc' => 'style="text-align:right;" ' , 'disabled' => $disabled)); ?></div>
                    <div class="div-table-col detail-col-detail <?php echo $obj->hideOnDisabled(); ?>"><?php echo $obj->inputLinkButton('btnDeleteRows' , '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button', 'etc' => 'tabIndex="-1"')); ?></div>
                </div>
                 
            <?php } ?> 
                   
         </div>        
          
          <div style="clear:both; height:1em;"></div> 
          <div style="float:left; display:inline-block;"><?php echo $obj->inputButton('btnAddRows', $obj->lang['addRows'], array('class' => 'btn btn-primary btn-second-tone')); ?></div>
       
          <div> 
                <div class="div-table  transaction-detail" style="float:right;">
                          
                        <div class="div-table" style="width: 100%">
                              <div class="div-table-row  form-group"> 
                                    <div class="div-table-col-3" style="text-align:right;"> 
                                           <?php echo $obj->lang['subtotal']; ?>
                                    </div>  
                                    <div class="div-table-col-3" style="width:180px;"> 
                                            <?php echo $obj->inputNumber('subtotal', array('readonly' => true, 'etc' => 'style="text-align:right;" ' )); ?> 
                                    </div> 
                                    <div class="div-table-col-3  <?php echo $obj->hideOnDisabled(); ?> icon-col"></div> 
                              </div>
                        </div>   
                       <div class="div-table" style="width: 100%">
                              <div class="div-table-row  form-group"> 
                                    <div class="div-table-col-3" style="text-align:right;"> 
                                           <?php echo $obj->lang['beforeTax']; ?>
                                    </div>  
                                    <div class="div-table-col-3" style="width:180px;"> 
                                            <?php echo $obj->inputNumber('beforeTaxTotal', array('readonly' => true, 'etc' => 'style="text-align:right;" ' )); ?> 
                                    </div> 
                                    <div class="div-table-col-3  <?php echo $obj->hideOnDisabled(); ?> icon-col"></div> 
                              </div>
                        </div>   
                        <div class="div-table" style="width: 100%">
                              <div class="div-table-row  form-group"> 
                                    <div class="div-table-col-3" style="text-align:right;"> 
                                          <?php echo strtoupper($obj->lang['PPN']); ?> [Include]
                                    </div>  
                                    <div class="div-table-col-3" style="width:180px;"> 
                                        <div class="flex">    
                                            <div><?php echo $obj->inputCheckBox('chkIncludeTax'); ?></div>  
                                            <div class="percentage-col"><?php echo $obj->inputDecimal('taxPercentage', array('etc' => 'style="text-align:right;"')); ?></div> 
                                            <div>%</div>
                                            <div class="consume"><?php echo $obj->inputNumber('taxValue', array('readonly' => true, 'etc' => 'style="text-align:right;"')); ?></div>
                                          </div>                                     </div> 
                                    <div class="div-table-col-3  <?php echo $obj->hideOnDisabled(); ?> icon-col"></div> 
                              </div>
                        </div>   

                    <div class="div-table"  style="width: 100%">
                            <div class="div-table-row  form-group"> 
                                <div class="div-table-col-3" style="text-align:right;">
                                       <?php echo $obj->lang['total']; ?>  
                                </div>  
                                <div class="div-table-col-3" style="width:180px;"> 
   									    <?php echo $obj->inputNumber('total', array( 'readonly' => true, 'etc' => 'style="text-align:right;"' )); ?> 
                                </div>  
                                  <div class="div-table-col-3  <?php echo $obj->hideOnDisabled(); ?> icon-col" ></div>
                            </div>
                          </div>  
                      </div>    
<!--
          </div>
          </div>
-->
        </div>    
      <div style="clear:both; height:4em;"></div> 
      
          <div class="div-table mnv-transaction mnv-monthly transaction-detail" style="width:100%; border-bottom:1px solid #333; ">
                <div class="div-table-caption"><h2><?php echo ucwords($obj->lang['monthlyCost']); ?></h2></div>
                <div class="div-table-row"> 
                    <div class="div-table-col detail-col-header" style="width:30px; text-align:right;">#</div>
                    <div class="div-table-col detail-col-header"><?php echo ucwords($obj->lang['description']); ?></div>
                    <div class="div-table-col detail-col-header" style="width:80px; text-align:right;"><?php echo ucwords($obj->lang['qty']); ?></div>
                    <!--<div class="div-table-col detail-col-header" style="width:80px;"><?php echo ucwords($obj->lang['unit']); ?></div>-->
                    <div class="div-table-col detail-col-header" style="width:120px; text-align:right;"><?php echo ucwords($obj->lang['price']); ?> @</div>
                    <div class="div-table-col detail-col-header" style="width:180px; text-align:right;"><?php echo ucwords($obj->lang['subtotal']); ?></div>
                    <div class="div-table-col detail-col-header  <?php echo $obj->hideOnDisabled(); ?> icon-col"></div>
                </div>
                
				<?php 
                    $totalMonthly = count($rsMonthly); 

                    for ($j=0;$j<=$totalMonthly; $j++){  
							
                        $class2 = 'transaction-detail-row';
                        $overwrite = true;
                        $disabled = false; 
                        $style = '';
                        $arrUnit = $arrDefaultUnit;

                        if ($j == $totalMonthly ){
                            $class2 = 'monthly-row-template';
                            $overwrite = false;
                            $disabled = true; 
                            $style = 'display: none !important';
                            $unitname = 'Pcs';

                        } else {  
                            
                            $_POST['hidDetailMonthlyKey[]'] =  $rsMonthly[$j]['pkey'];
                            $_POST['hidItemMonthlyKey[]'] =  $rsMonthly[$j]['itemkey']; 
                            $_POST['itemMonthlyName[]'] =  $rsMonthly[$j]['itemname']; 
                            $_POST['qtyMonthly[]'] =   $obj->formatNumber($rsMonthly[$j]['qty']); 
                            $_POST['priceInUnitMonthly[]'] =   $obj->formatNumber($rsMonthly[$j]['priceinunit']); 
                            $_POST['detailSubtotalMonthly[]'] =   $obj->formatNumber($rsMonthly[$j]['total']);  
                            //$_POST['selUnitMonthly[]'] =  $rsMonthly[$j]['unitkey']; 
                            //$arrUnit = $obj->convertForCombobox($item->getAvailableUnit($rsMonthly[$j]['itemkey']),'conversionunitkey','unitname','',array('relconversionmultiplier' => 'conversionmultiplier')); 
                 
                        } 
                    
                ?>
            
                
                <div class="div-table-row <?php echo $class2 ; ?>" style="<?php echo $style ; ?>">
                    <div class="div-table-col detail-col-detail row-number" style="text-align:right;"></div>
                    <div class="div-table-col detail-col-detail">
                        <?php echo $obj->inputText('itemMonthlyName[]',array('overwritePost' => $overwrite, 'disabled' => $disabled)); ?>
                        <?php echo $obj->inputHidden('hidItemMonthlyKey[]',array('overwritePost' => $overwrite,'disabled' => $disabled)); ?>
                        <?php echo $obj->inputHidden('hidDetailMonthlyKey[]',array('overwritePost' => $overwrite, 'disabled' => $disabled)); ?>
                    </div> 
                    <div class="div-table-col detail-col-detail"><?php echo $obj->inputNumber('qtyMonthly[]', array('overwritePost' => $overwrite, 'etc' => 'style="text-align:right;"', 'disabled' => $disabled)); ?></div>
                    <!--<div class="div-table-col detail-col-detail"><?php echo $obj->inputSelect('selUnitMonthly[]',$arrUnit, array('overwritePost' => $overwrite,  'disabled' => $disabled)); ?></div> -->                                   
                    <div class="div-table-col detail-col-detail"><?php echo $obj->inputNumber('priceInUnitMonthly[]', array('overwritePost' => $overwrite, 'etc' => 'style="text-align:right;"' , 'disabled' => $disabled)); ?></div>
                    <div class="div-table-col detail-col-detail"><?php echo $obj->inputNumber('detailSubtotalMonthly[]', array('overwritePost' => $overwrite,'readonly'=>true, 'etc' => 'style="text-align:right;" ' , 'disabled' => $disabled)); ?></div>
                    <div class="div-table-col detail-col-detail <?php echo $obj->hideOnDisabled(); ?>"><?php echo $obj->inputLinkButton('btnDeleteRows' , '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button', 'etc' => 'tabIndex="-1"')); ?></div>
                </div>
                 
            <?php } ?> 
                   
         </div>        
          
          <div style="clear:both; height:1em;"></div> 
          <div style="float:left; display:inline-block;"><?php echo $obj->inputButton('btnAddRows2', $obj->lang['addRows'], array('class' => 'btn btn-primary btn-second-tone')); ?></div>
       
          <div> 
                <div class="div-table transaction-detail" style="float:right;">
                          
                        <div class="div-table" style="width: 100%">
                              <div class="div-table-row  form-group"> 
                                    <div class="div-table-col-3" style="text-align:right;"> 
                                           <?php echo $obj->lang['subtotal']; ?>
                                    </div>  
                                    <div class="div-table-col-3" style="width:180px;"> 
                                            <?php echo $obj->inputNumber('subtotalMonthly', array('readonly' => true, 'etc' => 'style="text-align:right;" ' )); ?> 
                                    </div> 
                                    <div class="div-table-col-3  <?php echo $obj->hideOnDisabled(); ?> icon-col"></div> 
                              </div>
                        </div>   
                        <div class="div-table" style="width: 100%">
                              <div class="div-table-row  form-group"> 
                                    <div class="div-table-col-3" style="text-align:right;"> 
                                           <?php echo $obj->lang['beforeTax']; ?>
                                    </div>  
                                    <div class="div-table-col-3" style="width:180px;"> 
                                            <?php echo $obj->inputNumber('beforeTaxTotalMonthly', array('readonly' => true, 'etc' => 'style="text-align:right;" ' )); ?> 
                                    </div> 
                                    <div class="div-table-col-3  <?php echo $obj->hideOnDisabled(); ?> icon-col"></div> 
                              </div>
                        </div>   
                        <div class="div-table" style="width: 100%">
                              <div class="div-table-row  form-group"> 
                                    <div class="div-table-col-3" style="text-align:right;"> 
                                          <?php echo strtoupper($obj->lang['PPN']); ?> [Include]
                                    </div>  
                                    <div class="div-table-col-3" style="width:180px;"> 
                                        <div class="flex">    
                                            <div><?php echo $obj->inputCheckBox('chkIncludeTaxMonthly'); ?></div>  
                                            <div class="percentage-col"><?php echo $obj->inputDecimal('taxPercentageMonthly', array('etc' => 'style="text-align:right;"')); ?></div> 
                                            <div>%</div>
                                            <div class="consume"><?php echo $obj->inputNumber('taxValueMonthly', array('readonly' => true, 'etc' => 'style="text-align:right;"')); ?></div>
                                          </div>                                     </div> 
                                    <div class="div-table-col-3  <?php echo $obj->hideOnDisabled(); ?> icon-col"></div> 
                              </div>
                        </div>   

                    <div class="div-table"  style="width: 100%">
                            <div class="div-table-row  form-group"> 
                                <div class="div-table-col-3" style="text-align:right;">
                                       <?php echo $obj->lang['total']; ?>  
                                </div>  
                                <div class="div-table-col-3" style="width:180px;"> 
   									    <?php echo $obj->inputNumber('totalMonthly', array( 'readonly' => true, 'etc' => 'style="text-align:right;"' )); ?> 
                                </div>  
                                  <div class="div-table-col-3  <?php echo $obj->hideOnDisabled(); ?> icon-col" ></div>
                            </div>
                          </div>  
                      </div>    
          </div>
         <div class="form-button-margin"></div>
         <div class="form-button-panel" >  
         <?php  echo $obj->generateSaveButton(array(),true);?>
        </div> 
        
    </form>  

     <?php echo $obj->showDataHistory(); ?>
</div> 
</body>

</html>
