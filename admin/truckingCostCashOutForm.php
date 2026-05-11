<?php  
require_once '../_config.php'; 
require_once '../_include-v2.php'; 
 
includeClass('TruckingCostCashOut.class.php'); 
$truckingCostCashOut = createObjAndAddToCol(new TruckingCostCashOut());

$warehouse = createObjAndAddToCol(new Warehouse());
$truckingServiceOrder =  createObjAndAddToCol(new TruckingServiceOrder());
$truckingServiceWorkOrder =  createObjAndAddToCol(new TruckingServiceWorkOrder());
$cashBank =  createObjAndAddToCol(new CashBank());
$customer =  createObjAndAddToCol(new Customer());
$consignee =  createObjAndAddToCol(new Consignee());
    
$obj=  $truckingCostCashOut;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true)); 
$changeCashOutTimestamp = $security->isAdminLogin($truckingCostCashOut->changeTimeStampSecurityObject,10);

$formAction = 'truckingCostCashOutList';
 
$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false;
 
$rsTruckingCost = array();
$rsItemFile = array();

$_POST['trDate'] = date('d / m / Y H:i');
$_POST['trSubmissionDate'] = '00 / 00 / 0000 00:00';

$editWarehouseInactiveCriteria = '';

$rs = prepareOnLoadData($obj);   

//$timestampArr = $obj->getDateUsedForTimestamp($obj->tableName, array());
//$showVoucherDate = ($timestampArr['timestampType'] == 2) ? true : false;

$useStorage = $obj->useStorage;

if (!empty($_GET['id'])){ 
	$id = $_GET['id'];	 
    
	$rsTruckingCost = $obj->getDetailWithRelatedInformation($id); 
    $_POST['trDate'] = $obj->formatDBDate($rs[0]['trdate'],'d / m / Y H:i');
    $_POST['trSubmissionDate'] = $obj->formatDBDate($rs[0]['trsubmissiondate'],'d / m / Y H:i',array('returnOnEmpty'=>true, 'value' => '00 / 00 / 0000 00:00'));
     
    //$_POST['trCashBankDate'] = $obj->formatDBDate($rs[0]['trcashbankdate'],'d / m / Y', array('returnOnEmpty'=>true, 'value' => '')); 
    $_POST['trDesc'] = $rs[0]['trdesc'];
    $_POST['subtotal'] = $obj->formatNumber($rs[0]['subtotal']);
    $_POST['total'] = $obj->formatNumber($rs[0]['total']);
    $_POST['arEmployee'] = $obj->formatNumber($rs[0]['aremployee']);  
    $_POST['refCode'] = $rs[0]['refcode'];
    $_POST['hidRefKey'] = $rs[0]['refkey'];
    $_POST['refCode2'] = $rs[0]['refcode2'];
    $_POST['hidRefKey2'] = $rs[0]['refkey2'];
    $_POST['hidRefTable'] = $rs[0]['reftabletype'];   
    $_POST['jobDescription'] = $rs[0]['jobdescription'];   
    
    $_POST['hidEmployeeKey'] = $rs[0]['employeekey']; 
	if (!empty($rs[0]['employeekey'])){
		$rsEmployee = $employee->getDataRowById($rs[0]['employeekey']);
		$_POST['employeeName'] = $rsEmployee[0]['name'];
		$_POST['recipientMobile'] = $rsEmployee[0]['mobile'];
		$_POST['recipientBankName'] = $rs[0]['recipientbankname'];
		$_POST['recipientBankAccountName'] = $rs[0]['recipientbankaccountname'];
		$_POST['recipientBankAccountNumber'] = $rs[0]['recipientbankaccountnumber'];
	}  
     
	if (!empty($rs[0]['customerkey'])){
		$rsCustomer = $customer->getDataRowById($rs[0]['customerkey']);
		$_POST['customerName'] = $rsCustomer[0]['name'];
	}
    
   if (!empty($rs[0]['consigneekey'])){
		$rsConsignee = $consignee->getDataRowById($rs[0]['consigneekey']);
		$_POST['consigneeName'] = $rsConsignee[0]['name'];
	}
    
    $_POST['selWarehouse'] = $rs[0]['warehousekey'];
    
    if($changeCashOutTimestamp){
        $_POST['selTimeStampType'] = $rs[0]['timestamptype'];
    }
    $editWarehouseInactiveCriteria = ' or '.$warehouse->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['warehousekey']); 
  
     
    //update file 
    if($useStorage){ 
        $rsFileDetail = $obj->getFileDetail($id);
    }else{ 
        $rsItemFile = $obj->getItemFile($id);
		
        if(count($rsItemFile) > 0){
            $sourcePath = $obj->defaultDocUploadPath.$obj->uploadFileFolder.$id;
            $destinationPath = $obj->uploadTempDoc.$obj->uploadFileFolder.$id; 
            $obj->deleteAll($destinationPath); 

            if(!is_dir($destinationPath)) 
                mkdir ($destinationPath,  0755, true);

            $obj->fullCopy($sourcePath,$destinationPath);  
        }

    }
    
} 

$arrStatus = $obj->convertForCombobox($obj->getAllStatus(),'pkey','status');   

$arrCashOutType = array(); 
$tableKey = $obj->getTableKeyAndObj($truckingServiceOrder->tableName);
$arrCashOutType[$tableKey['key']] = 'Job Order'; 
$tableKey = $obj->getTableKeyAndObj($truckingServiceWorkOrder->tableName);
$arrCashOutType[$tableKey['key']] = 'Surat Perintah Kerja';

$arrWarehouse = $class->convertForCombobox($warehouse->searchData('','',true,' and ('.$warehouse->tableName.'.statuskey = 1' .$editWarehouseInactiveCriteria.')'),'pkey','name'); 
$arrDateType = array();
$arrDateType[0] = '------';
$arrDateType[1] = ucwords($obj->lang['transactionDate']);
$arrDateType[2] = ucwords($obj->lang['confirmedDate']);


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title> 
 
<script type="text/javascript">  
 
   jQuery(document).ready(function(){  
	 	 var tabID = <?php echo ($isQuickAdd) ?  $_GET['tabID'] :  'selectedTab.newPanel[0].id';  ?>  
             
         var varConstant = {     
            USE_STORAGE : <?php echo ($useStorage) ? "true" : "false"; ?>
        };

       
         var truckingCostCashOut = new TruckingCostCashOut(tabID, 
                                                           varConstant,
                                                           "<?php echo $obj->uploadFileFolder; ?>",
                                                           <?php echo json_encode($rsItemFile); ?>,
                                                           <?php echo $obj->loadSetting('autoDeductAREmployeeOnCashOut'); ?>,
                                                          );
    
         prepareHandler(truckingCostCashOut);   
       
         var fieldValidation =  {
                                 code: { 
                                        validators: {
                                            notEmpty: {
                                                message: phpErrorMsg.code[1]
                                            }, 
                                        }
                                    },
                                    refCode: { 
                                        validators: {
                                            notEmpty: {
                                                message: phpErrorMsg.reference[1]
                                            }, 
                                        }
                                    }, 
                                    refCode: { 
                                        validators: {
                                            notEmpty: {
                                                message: phpErrorMsg.reference[1]
                                            }, 
                                        }
                                    }, 
                                    employeeName: { 
                                        validators: {
                                            notEmpty: {
                                                message: phpErrorMsg.employee[1]
                                            }, 
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
  
  <form id="defaultForm" method="post" class="form-horizontal" action="<?php echo $formAction; ?>">
      <?php prepareOnLoadDataForm($obj); ?>  
      <?php // echo $obj->inputHidden('hidRefTable'); ?>
      
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
                            <div class="col-xs-9">  <?php echo $obj->inputAutoCode('code'); ?></div> 
                        </div>   
                        
                        <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['date']); ?></label> 
                            <div class="col-xs-9"> 
                                    <?php echo $obj->inputDateTime('trDate',array('allowedStatusForEdit' => array (1))); ?> 
                            </div> 
                        </div>  
                        <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['submissionDate']); ?></label> 
                            <div class="col-xs-9"> 
                                    <?php echo $obj->inputDate('trSubmissionDate',array('readonly' => true)); ?> 
                            </div> 
                        </div>  
                         <?php if($changeCashOutTimestamp) { ?> 
                        <div class="form-group" > 
                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['journalDate']); ?></label> 
                            <div class="col-xs-9"> 
                                    <?php echo $obj->inputSelect('selTimeStampType',$arrDateType,array('allowedStatusForEdit' => array (1))); ?> 
                            </div> 
                        </div>
                        <?php } ?>
                        
                       <!-- <?php if ($showVoucherDate) { ?> 
                        <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['date']); ?> (<?php echo ucwords($obj->lang['voucher']); ?>)</label> 
                            <div class="col-xs-9"> 
                                    <?php echo $obj->inputDate('trCashBankDate',array('allowedStatusForEdit' => array (1))); ?> 
                            </div> 
                        </div>  
                        <?php } ?>-->
                        
                        <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['warehouse']); ?></label> 
                            <div class="col-xs-9"> 
                                <?php echo $obj->inputSelect('selWarehouse', $arrWarehouse, array('readonly' => true)); ?>  
                            </div> 
                        </div>
                        <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['transactionType']); ?></label> 
                            <div class="col-xs-9"> 
                                 <?php echo $obj->inputSelect('hidRefTable', $arrCashOutType, array('readonly' => true)); ?>
                            </div> 
                        </div>  
                        <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo $obj->lang['refCode']; ?></label> 
                            <div class="col-xs-9"> 
                                <div class="flex"> 
                                     <div  class="consume">
                                          <?php     
                                               echo $obj->inputAutoComplete(array(  
                                                                                    'readonly' => (empty($rs)) ? false : true,
                                                                                    'revalidateField' => true, 
                                                   
                                                                                    'element' => array('value' => 'refCode',
                                                                                                       'key' => 'hidRefKey'),
                                                                                    'source' => array(
                                                                                                        'url' => 'ajax-trucking-cost-cash-out.php',
                                                                                                        'data' => array(  'action' =>'searchAvailableReference')
                                                                                                    ) , 
                                                                                    'callbackFunction' => 'getTabObj().updateReference()'
                                                                                  )
                                                                            );  


                                            ?> 
                                    </div>
                                    <div  class="consume">
                                          <?php     
                                               echo $obj->inputHidden('hidRefKey2'); 
                                               echo $obj->inputText('refCode2', array('readonly' => true));    
                                            ?> 
                                    </div>
                               </div> 
                            </div>  
                        </div> 
                        
                        <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo $obj->lang['customer']; ?></label>  
                            <div class="col-xs-9"> <?php echo $obj->inputText('customerName',array('readonly' => true));   ?>  </div> 
                        </div>  
                        
                        <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo $obj->lang['consignee']; ?></label>  
                            <div class="col-xs-9"> <?php echo $obj->inputText('consigneeName',array('readonly' => true));   ?>  </div> 
                        </div>  
                        
                         <div class="form-group">
                                <label class="col-xs-3 control-label"></label> 
                                <div class="col-xs-9"></div> 
                         </div> 
                        
                       
                        <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo $obj->lang['recipient']; ?></label>  
                                        <div class="col-xs-9"> 
                                         <?php    
                                                echo $obj->inputAutoComplete(array(
                                                                                'allowedStatusForEdit' => array (1),
                                                                                'revalidateField' => true, 
                                                                                'objRefer' => $employee,
                                                                                'element' => array('value' => 'employeeName',
                                                                                                   'key' => 'hidEmployeeKey'),
                                                                                'source' =>array(
                                                                                                    'url' => 'ajax-employee.php',
                                                                                                    'data' => array(  'action' =>'searchData' )
                                                                                                ) , 
                                                                                'callbackFunction' => 'getTabObj().updateEmployeeInformation()'
                                                                              )
                                                                        );  
                                            ?> 
                                        </div> 
                          </div>  
                          <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['mobilePhone']); ?></label> 
                                <div class="col-xs-9"> 
                                    <div class="flex">
                                        <div class="consume"><?php echo $obj->inputText('recipientMobile', array('readonly' => true)); ?></div>
                                        <div class="wa-button"><li class="fab fa-whatsapp"></li></div>
                                    </div> 
                                </div> 
                            </div>   
                          <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['bankName']); ?></label> 
                                <div class="col-xs-9"> 
                                      <?php echo $obj->inputText('recipientBankName',array('allowedStatusForEdit' => array (1))); ?>
                                </div> 
                            </div>   
                             <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['bankAccountName']); ?></label> 
                                <div class="col-xs-9"> 
                                      <?php echo $obj->inputText('recipientBankAccountName',array('allowedStatusForEdit' => array (1))); ?>
                                </div> 
                            </div>   
                             <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['bankAccountNumber']); ?></label> 
                                <div class="col-xs-9"> 
                                      <?php echo $obj->inputText('recipientBankAccountNumber',array('allowedStatusForEdit' => array (1))); ?>
                                </div> 
                            </div>   
                            <?php if (empty($rs)){ ?> 
                            <div class="form-group"> 
                                <div class="col-xs-3"></div>
                                <div class="col-xs-9"><?php echo $obj->inputButton('btnImport',$obj->lang['update'],array( 'class' => 'btn btn-primary semi-fixed btn-second-tone')); ?></div>
                            </div>  
                            <?php } ?>  
                    </div>
                </div>
                
                <div class="div-table-col">   
                    <div class="div-tab-panel"> 
                        <div class="div-table-caption border-blue"><?php echo ucwords($obj->lang['note']); ?></div>
                        <div class="form-group"> 
                            <div class="col-xs-12"> 
                                    <?php echo  $obj->inputTextArea('trDesc', array('allowedStatusForEdit' => array (1), 'etc' => 'style="height:10em;"')); ?>
                            </div> 
                        </div>   
                    </div> 
                    <div class="div-tab-panel"> 
                        <div class="div-table-caption border-green"><?php echo ucwords($obj->lang['jobDescription']); ?></div>
                        <div class="form-group"> 
                            <div class="col-xs-12"> 
                                    <?php echo  $obj->inputTextArea('jobDescription', array('disabled'=>true, 'etc' => 'style="height:10em;"')); ?>
                            </div> 
                        </div>   
                    </div> 
                    
                    
                        <?php if($useStorage) {  ?>
                             <div id="file-update-ajax" class="div-tab-panel">
                                 <div class="div-table" style="width:100%"> 
                                    <div class="div-table-caption border-purple"><?php echo ucwords($obj->lang['file']); ?></div> 
                                    <?php echo $obj->inputUploadFilePlugin($rs,$rsFileDetail, array('allowedStatusForEdit' => array(1,2,3,4))); ?> 
                                 </div>
                            </div>     
                        <?php }else { ?> 
                    
                        <div class="div-tab-panel"> 
                         <div class="div-table-caption border-purple"><?php echo ucwords($obj->lang['files']); ?></div> 
                         <div class="form-group"> 
                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['documentFiles']); ?></label> 
                            <div class="col-xs-9"> 
                                    <!-- file uploader --> 
                                    <div class="item-file-uploader">
                                        <ul class="file-list" ></ul>
                                        <div style="clear:both; height:1em; "></div>
                                        <div class="file-uploader">	
                                            <noscript><p>Please enable JavaScript to use file uploader.</p></noscript> 
                                        </div>
                                      </div>  
                                    <!-- file uploader -->
                                    <?php if (!empty($rs) && in_array($rs[0]['statuskey'], array(2,3)) ) {
                                         echo $obj->inputButton('btnUpdateFile', $obj->lang['update'], array('allowedStatusForEdit' => array(1,2,3),'class' =>'btn btn-primary btn-second-tone'));
                                    } ?>
                            </div>  
                          </div>  
                          </div>  
                    
                        <?php }  ?> 
                </div>
				
                
            </div>
      </div>   
       
        <div class="div-table transaction-detail" style="width:100%; border-bottom:1px solid #333; ">
                <div class="div-table-row"> 
                    <div class="div-table-col detail-col-header" style="width:60px; text-align:right;"><?php echo ucwords($obj->lang['qty']); ?></div>
                    <div class="div-table-col detail-col-header"><?php echo ucwords($obj->lang['costName']); ?></div>
                    <div class="div-table-col detail-col-header" style="width:200px;"><?php echo ucwords($obj->lang['fromAccount']); ?></div>
                    <?php if(ADV_FINANCE){ ?>
                    <div class="div-table-col detail-col-header" style="width:160px;"><?php echo ucwords($obj->lang['cashBankNumber']); ?></div>
                    <?php } ?> 
                    <div class="div-table-col detail-col-header" style="width:200px;"><?php echo ucwords($obj->lang['note']); ?></div> 
                    <div class="div-table-col detail-col-header" style="width:100px; text-align:right;"><?php echo ucwords( ($obj->isActiveModule('CashBankRealization')) ? $obj->lang['request'] : $obj->lang['cost']); ?></div> 
                    <div class="div-table-col detail-col-header" style="width:120px; text-align:right;"><?php echo ucwords($obj->lang['subtotal']); ?></div> 
                    <!-- <div class="div-table-col detail-col-header  <?php echo $obj->hideOnDisabled(); ?>" style="width:45px"></div>  -->
                </div>
                
				<?php 
                            
                    $totalRows = count($rsTruckingCost);
             
            
                    for ($i=0;$i<=$totalRows; $i++){  
                                
                        $class =  'transaction-detail-row';
                        $overwrite = true;
                        $etc = ''; 
                        
                        if ($i == $totalRows ){
                            $class = 'detail-row-template';
                            $overwrite = false;
                            $etc = 'disabled="disabled"'; 
                        } else { 
                            $_POST['hidDetailKey[]'] =  $rsTruckingCost[$i]['pkey'];  
                             
                            $_POST['hidCostKey[]'] = $rsTruckingCost[$i]['costkey'];
                            $_POST['costName[]'] =  $rsTruckingCost[$i]['costname'];
                            $_POST['hidCOAKey[]'] = $rsTruckingCost[$i]['coakey'];
                            $_POST['COAName[]'] =  $rsTruckingCost[$i]['coaname'];  
                            $_POST['qty[]'] =   $obj->formatNumber($rsTruckingCost[$i]['qty']);  
                            $_POST['costValue[]'] =   $obj->formatNumber($rsTruckingCost[$i]['costvalue']);  
                            $_POST['amount[]'] =   $obj->formatNumber($rsTruckingCost[$i]['amount']);  
                            $_POST['detailDesc[]'] =  $rsTruckingCost[$i]['description'];  
                            $_POST['refheadercostkey[]'] = $rsTruckingCost[$i]['refheadercostkey'];  
                             
                            if(ADV_FINANCE)
                                $_POST['cashBankRefCode[]'] = $cashBank->getCashBankRef($id,$obj->tableName,$rsTruckingCost[$i]['coakey'])['code'];
                            
                        }
                    ?>
            
                    <div class="div-table-row <?php echo $class; ?>"> 
                    <div class="div-table-col detail-col-detail"><?php echo $obj->inputHidden('hidDetailKey[]',array('overwritePost' => $overwrite, 'etc' => $etc)); ?><?php echo $obj->inputHidden('refheadercostkey[]',array('overwritePost' => $overwrite, 'etc' => $etc)); ?><?php echo $obj->inputNumber('qty[]',array('overwritePost' => $overwrite, 'readonly' => true, 'etc' =>  'style="text-align:right"' . $etc)); ?></div> 
                    <div class="div-table-col detail-col-detail"><?php echo $obj->inputText('costName[]',array('overwritePost' => $overwrite, 'readonly' => true, 'etc' => $etc)); ?><?php echo $obj->inputHidden('hidCostKey[]',array('overwritePost' => $overwrite, 'etc' => $etc)); ?></div> 
                    <div class="div-table-col detail-col-detail"><?php echo $obj->inputHidden('hidCOAKey[]',array('overwritePost' => $overwrite, 'readonly' => true )); ?><?php echo $obj->inputText('COAName[]',array('overwritePost' => $overwrite, 'allowedStatusForEdit' => array (1), 'etc' => $etc )); ?></div>
                    <?php if(ADV_FINANCE){ ?>
                        <div class="div-table-col detail-col-detail"><?php echo $obj->inputText('cashBankRefCode[]',array('overwritePost' => $overwrite, 'readonly' => true )); ?></div> 
                    <?php } ?>    
                    <div class="div-table-col detail-col-detail"><?php echo $obj->inputText('detailDesc[]',array('overwritePost' => $overwrite, 'etc' => $etc, 'allowedStatusForEdit' => array (1) )); ?></div> 
                    <div class="div-table-col detail-col-detail"><?php echo $obj->inputNumber('costValue[]',array('overwritePost' => $overwrite,'readonly' => true,  'etc' => 'style="text-align:right"' .$etc)); ?></div> 
                    <div class="div-table-col detail-col-detail"><?php echo $obj->inputNumber('amount[]',array('overwritePost' => $overwrite,'readonly' => true,  'etc' => 'style="text-align:right"' .$etc)); ?></div> 
                    <div class="div-table-col detail-col-detail  <?php echo $obj->hideOnDisabled(); ?>" style="display:none;"><?php echo $obj->inputLinkButton('btnDeleteRows' , '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button', 'etc' => 'tabIndex="-1"')); ?></div> 
                    </div>
                         
                <?php  } ?>   
                   
         </div>     
      
          <div style="clear:both; height:1em;"></div> 
          <!-- 
          <div style="float:left; display:inline-block;"><?php echo $obj->inputButton('btnAddRows', $obj->lang['addRows']); ?></div>
         -->
        <div>    
            <div class="div-table" style="float:right;" >
               <div class="div-table-row  form-group" > 
                    <div class="div-table-col-3" style="text-align:right;"> 
                        <?php echo ucwords($obj->lang['subtotal']); ?>
                    </div>  
                    <div class="div-table-col-3" style="width:120px"> 
                         <?php echo $obj->inputNumber('subtotal', array ('readonly' => true, 'etc' => 'style="text-align:right;"')) ;?>    
                    </div>  
                </div> 
                <div class="div-table-row  form-group" > 
                    <div class="div-table-col-3" style="text-align:right;"> 
                        <?php echo ucwords($obj->lang['employeeAR']); ?><br>
                        <span class="ar-employee" style="color:#666">0</span>
                    </div>  
                    <div class="div-table-col-3" style="width:120px"> 
                         <?php echo $obj->inputNumber('arEmployee', array ( 'allowedStatusForEdit' => array (1), 'etc' => 'style="text-align:right;"')) ;?>    
                    </div>  
                </div> 
                 <div class="div-table-row  form-group"> 
                    <div class="div-table-col-3" style="text-align:right;"> 
                        <?php echo ucwords($obj->lang['total']); ?>
                    </div>  
                    <div class="div-table-col-3" style="width:120px"> 
                         <?php echo $obj->inputNumber('total', array ('readonly' => true, 'etc' => 'style="text-align:right;"')) ;?>    
                    </div>  
                </div> 
            </div>   
        </div>        
      
        <div class="form-button-margin"></div>
        <div class="form-button-panel" > 
         <?php  echo $obj->generateSaveButton(array(),true);   ?>  
        </div> 
        
    </form>   
   
     <?php  echo $obj->showDataHistory(); ?>
</div> 
</body>

</html>
