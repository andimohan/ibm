<?php 
require_once '../../../_config.php';
require_once '../../../_include-v2.php';
includeClass(array('APPayment.class.php','APPayableTax23Payment.class.php','AP.class.php','APPayableTax23.class.php'));
$apPayableTax23 = createObjAndAddToCol(new APPayableTax23());
$apPayableTax23Payment = createObjAndAddToCol(new APPayableTax23Payment());
$warehouse = createObjAndAddToCol(new Warehouse());
$paymentMethod = createObjAndAddToCol(new PaymentMethod());
$supplier = createObjAndAddToCol(new Supplier());


$obj= $apPayableTax23Payment; 
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true)); 

$useStorage = $obj->useStorage;

$formAction = 'apPayableTax23PaymentList';

$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false;

$editPaymentMethodInactiveCriteria = '';
$editWarehouseInactiveCriteria = '';

$rsAPPaymentDetail = array(); 

$_POST['trDate'] = date('d / m / Y');
$_POST['taxPeriod'] = date('F Y');

$_POST['trStartDate'] = date('d / m / Y');
$_POST['trEndDate'] = date('d / m / Y');

$rs = prepareOnLoadData($obj);  

$hasEditAccess = ($security->isAdminLogin($securityObject,11,false)) ? true:false;

$btnUpdateNTPN = '';
$allowedNTPN = array();
$rsItemFile = array();
$rsAPPaymentMethodDetail = array();



if (!empty($_GET['id'])){ 
	$id = $_GET['id'];	 
    
	$rsAPPaymentDetail = $obj->getDetailById($id); 

    if(ADV_FINANCE && TEST_VOUCHER){ 
            $rsAPPaymentMethodDetail = $obj->getPaymentVoucherDetail($id);  
            $arrAvailableVoucher = $class->convertForCombobox($rsAPPaymentMethodDetail,'cashbankvoucherkey','voucherlabel');  
                        
            $existingVoucherKey = array_column($rsAPPaymentMethodDetail,'cashbankvoucherkey');
            $otherVoucher = $cashBank->getAvailableVoucher($rs[0]['supplierkey'],' and  '.$cashBank->tableName.'.credittype = -1 and '.$cashBank->tableName.'.pkey not in ('.$obj->oDbCon->paramString($existingVoucherKey,',').')',true,2);
            foreach($otherVoucher as $voucherItem){ 
                $arrAvailableVoucher[$voucherItem['pkey']]['label'] = $voucherItem['voucherlabel'];
                $arrAvailableVoucher[$voucherItem['pkey']]['rel'] = array('rel-amount' => $voucherItem['outstanding']); 
            }  
    }else{ 
    	$rsAPPaymentMethodDetail = $obj->getPaymentMethodDetail($id); 
	}

    
	$_POST['trDate'] = $obj->formatDBDate($rs[0]['trdate'],'d / m / Y');
	$rsSupplier = $supplier->getDataRowById($rs[0]['supplierkey']);
	$_POST['supplierName'] = $rsSupplier[0]['name'] ;
	$_POST['taxObjectCode'] = $rs[0]['taxobjectcode'] ;
	$_POST['taxPeriod'] = $obj->formatDBDate($rs[0]['taxperiod'],'F Y');
	$_POST['hidCurrentSupplierName'] = $rsSupplier[0]['name'] ; 
	$_POST['hidSupplierKey'] = $rsSupplier[0]['pkey'] ;  
	$_POST['hidCurrentSupplierKey'] = $rsSupplier[0]['pkey'] ; 
	$_POST['trDesc'] = $rs[0]['trnotes'];
    $_POST['refHeaderCode'] = $rs[0]['refcode'];
    $_POST['ntpn'] = $rs[0]['ntpn'];
	$_POST['total'] = $obj->formatNumber($rs[0]['grandtotal']); 
    $_POST['selWarehouseKey'] = $rs[0]['warehousekey']; 

    $_POST['chkDatePeriod'] = $rs[0]['usedateperiod'];   
	$_POST['trStartDate'] = $obj->formatDBDate($rs[0]['startdateperiod'],'d / m / Y', array('returnOnEmpty' => true, 'value' => '00 / 00 / 0000'));
	$_POST['trEndDate'] = $obj->formatDBDate($rs[0]['enddateperiod'],'d / m / Y', array('returnOnEmpty' => true, 'value' => '00 / 00 / 0000'));
	 
    $_POST['totalPayment'] = $obj->formatNumber($rs[0]['totalpayment']);
    $_POST['balance'] = $obj->formatNumber($rs[0]['balance']);

    if(($rs[0]['statuskey'] == 2 || $rs[0]['statuskey'] == 3) && $hasEditAccess){
        $btnUpdateNTPN = '<div>'.$obj->inputButton('btnUpdateNTPN', $obj->lang['update'],array( 'allowedStatusForEdit' => array(2,3), 'class' =>'btn btn-primary btn-second-tone')).'</div>';
        $allowedNTPN = array( 'allowedStatusForEdit' => array(2,3) );
    }
        
	
    if($useStorage){ 
        $rsFileDetail = $obj->getFileDetail($id);
    }else{  
        
        $rsItemFile = $obj->getItemFile($id);

        if (count($rsItemFile) > 0) {
            $sourcePath = $obj->defaultDocUploadPath . $obj->uploadFileFolder . $id;
            $destinationPath = $obj->uploadTempDoc . $obj->uploadFileFolder . $id;
            $obj->deleteAll($destinationPath);

            if (!is_dir($destinationPath))
                mkdir($destinationPath, 0755, true);

            $obj->fullCopy($sourcePath, $destinationPath);
        }

    }
    
   
    
	$editWarehouseInactiveCriteria = ' or  '.$warehouse->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['warehousekey']);
	$editPaymentMethodInactiveCriteria = ' or '.$paymentMethod->tableName.'.pkey in (select paymentkey from '.$obj->tablePayment.' where refkey = '. $obj->oDbCon->paramString($rs[0]['pkey']).')';
  
} 

$arrStatus = $obj->generateComboboxOpt(array('data' => $obj->getAllStatus(),'label' => 'status'));  
$arrWarehouse = $warehouse->generateComboboxOpt(null,array('criteria' => ' and ('.$warehouse->tableName.'.statuskey = 1' .$editWarehouseInactiveCriteria.')')); 

$rsPaymentMethod =$paymentMethod->getDataForCommboboxWithPrivileges($editPaymentMethodInactiveCriteria);
$arrPaymentMethod = $obj->convertForCombobox($rsPaymentMethod,'pkey','name');    


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title> 
 
<script type="text/javascript">  
 
		jQuery(document).ready(function(){  
	 	 
     	var tabID = <?php echo ($isQuickAdd) ?  $_GET['tabID'] :  'selectedTab.newPanel[0].id';  ?>  
        var tablekey = <?php echo $obj->getTableKeyAndObj($obj->tableName ,array('key'))['key']; ?>;  
          
        var varConstant = {  simpleForm : true,
                            useStorage : <?php echo ($useStorage) ? "true" : "false"; ?>
                        };
        
        var fileUpload = {  
				uploadFolder : "<?php echo $obj->uploadFileFolder; ?>",
				uploaderTarget: "item-file-uploader",
				rsFile :<?php echo json_encode($rsItemFile); ?>, 
		};
        
        var apPayableTax23Payment = new APPayableTax23Payment(tabID,varConstant,fileUpload);
        prepareHandler(apPayableTax23Payment);     

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
  
  <form id="defaultForm" method="post" class="form-horizontal" action="<?php echo $formAction; ?>" >
    <?php prepareOnLoadDataForm($obj); ?>     
    <?php echo $obj->inputHidden('hidCurrentSupplierKey'); ?>
    <?php echo $obj->inputHidden('hidCurrentSupplierName'); ?>
    
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
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['taxPeriod']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputMonth('taxPeriod'); ?>
                                        </div> 
                                    </div>
                                    <div class="form-group">
                		                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['withholdingNo']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputText('refHeaderCode'); ?>
                                        </div> 
                                    </div>
<!--
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['taxObjectCode']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputText('taxObjectCode'); ?>
                                        </div> 
                                    </div>
-->
                                    <div class="form-group">
  				                        <label class="col-xs-3 control-label">NTPN</label> 
                                        <div class="col-xs-9"> 
                                            <div class="flex">
                                                <div class="consume"> <?php echo $obj->inputText('ntpn', $allowedNTPN); ?> </div>
                                                <?php echo $btnUpdateNTPN; ?>
                                            </div>
                                        </div> 
                                    </div>
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['warehouse']); ?></label> 
                                        <div class="col-xs-9"> 
                                           <?php echo  $obj->inputSelect('selWarehouseKey', $arrWarehouse); ?>
                                        </div> 
                                    </div>    
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['supplier']); ?></label> 
                                        <div class="col-xs-9"> 
                                           <?php  echo $obj->inputAutoComplete(array( 
                                                                                'objRefer' => $supplier,
                                                                                'revalidateField' => true,
                                                                                'element' => array('value' => 'supplierName',
                                                                                                   'key' => 'hidSupplierKey'),
                                                                                'source' =>array(
                                                                                                    'url' => 'ajax-supplier.php',
                                                                                                    'data' => array(  'action' =>'searchData' )
                                                                                                ) , 
                                                                                'callbackFunction' => 'getTabObj().updateSupplierInformation(event, ui)'
                                                                              )
                                                                        );  
                                            ?>
                                        </div> 
                                    </div>   
                                </div>
                    </div>
                    <div class="div-table-col"> 
                           <div class="div-tab-panel"> 
                              <div class="div-table-caption border-blue"><?php echo ucwords($obj->lang['note']); ?></div> 
                               <?php echo  $obj->inputTextArea('trDesc', array('etc' => 'style="height:10em;"')); ?> 
                            </div>  
						
                        <?php if($useStorage) {  ?>
                             <div id="file-update-ajax" class="div-tab-panel">
                                 <div class="div-table" style="width:100%"> 
                                    <div class="div-table-caption border-purple"><?php echo ucwords($obj->lang['file']); ?></div> 
                                    <?php echo $obj->inputUploadFilePlugin($rs,$rsFileDetail); ?> 
                                 </div>
                            </div>     
                        <?php }else { ?> 
						<div class="div-tab-panel"> 
                            <div class="div-table-caption border-purple"><?php echo ucwords($obj->lang['file']); ?></div> 
							 <div class="form-group"> 
								 <div class="col-xs-12"> 
                                     <!-- file uploader --> 
										<div class="item-file-uploader">
											<ul class="file-list" ></ul>
											<div style="clear:both; height:1em; "></div>
											<div class="file-uploader">	
												<noscript><p>Please enable JavaScript to use file uploader.</p></noscript> 
											</div>
										  </div>  
										<!-- file uploader -->  
									 	
								 </div> 
                              </div>    
                         </div> 
                        <?php } ?> 
                    </div>
                </div>    
        </div>   
                                    

        <div class="form-group <?php echo $obj->hideOnDisabled(); ?>" style="margin-bottom:1em" > 
            <div class="col-xs-12"> 
                <div class="flex mnv-date-range">
                    <div><?php echo $obj->inputCheckBox('chkDatePeriod'); ?></div>
                    <div><?php echo $obj->inputDate('trStartDate',array('add-class' => 'import-date-period')); ?></div>
                    <div>-</div>
                    <div><?php echo $obj->inputDate('trEndDate',array('add-class' => 'import-date-period')); ?></div>
                    <div style="margin-left:1em"><?php echo $obj->inputButton('btnImport',$obj->lang['showAll'],array('class' =>'btn btn-primary btn-second-tone')); ?></div>
                </div> 
            </div> 
        </div>  
        
        <div class="div-table mnv-transaction transaction-detail mnv-checkbox-group" style="width:100%; border-bottom:1px solid #333; ">
                <div class="div-table-row"> 
                    <div class="div-table-col detail-col-header"><?php echo ucwords($obj->lang['code']); ?></div>
                    <div class="div-table-col detail-col-header" style="width:120px; text-align:center;"><?php echo ucwords($obj->lang['date']); ?></div>
                    <div class="div-table-col detail-col-header" style="width:250px;"><?php echo ucwords($obj->lang['supplier']); ?></div>
                    <div class="div-table-col detail-col-header" style="width:160px;"><?php echo ucwords($obj->lang['reference']); ?> PO</div>
                    <div class="div-table-col detail-col-header" style="width:160px;"><?php echo ucwords($obj->lang['reference']); ?> JO</div>
                    <div class="div-table-col detail-col-header" style="width:120px; text-align:center;"><?php echo ucwords($obj->lang['jobDate']); ?></div>
                    <div class="div-table-col detail-col-header" style="width:130px; text-align:right;"><?php echo ucwords($obj->lang['amount']); ?></div>
                    <div class="div-table-col detail-col-header" style="width:130px; text-align:right;"><?php echo ucwords($obj->lang['outstanding']); ?></div>
                    <div class="div-table-col detail-col-header" style="width:130px; text-align:right;"><?php echo ucwords($obj->lang['paymentAmount']); ?></div>
                    <div class="div-table-col detail-col-header  <?php echo $obj->hideOnDisabled(); ?> icon-col" style="text-align:center; width: 35px;"><?php echo $obj->inputCheckBox('chkPick-master', array('etc' => 'style="margin-top:0"')); ?></div>
                    <div class="div-table-col detail-col-header <?php echo $obj->hideOnDisabled(); ?> icon-col"></div>
                      
                </div>
                
				<?php 
                    $objAP = $obj->getAPObj(); 
                    $totalRows = count($rsAPPaymentDetail);

                    $arrAPKey = array_column($rsAPPaymentDetail,'apkey');
                    $rsAPCol = $objAP->searchDataRow(array($objAP->tableName.'.pkey',
                                                        $objAP->tableName.'.code',
                                                      $objAP->tableName.'.trdate',
                                                        $objAP->tableName.'.supplierkey',
                                                      $objAP->tableName.'.refcode',
                                                       $objAP->tableName.'.amount'), 
                                                ' and ' .$objAP->tableName.'.pkey in ('. $obj->oDbCon->paramString($arrAPKey,',').')'
                                                     ); 
                    $arrSupplierKey = array_column($rsAPCol, 'supplierkey');
                    $rsAPCol = array_column($rsAPCol,null,'pkey');
                    $rsSupplierCol = $supplier->searchDataRow(array($supplier->tableName.'.pkey',
                                                    $supplier->tableName.'.code',
                                                    $supplier->tableName.'.name'), 
                                            ' and ' .$supplier->tableName.'.pkey in ('. $obj->oDbCon->paramString($arrSupplierKey,',').')'
                                            ); 
                    $rsSupplierCol = array_column($rsSupplierCol,null,'pkey');
            
                    if(!empty($rs)){
                        $rsJob = $apPayableTax23->getJobInformation($arrAPKey);
                        $rsJob = array_column($rsJob,null,'apkey');  
                    }
                

                    for ($i=0;$i<=$totalRows; $i++){  
                        
					    $class =  'transaction-detail-row';
                        $overwrite = true;
                        $disabled = false;  
                        if ($i == $totalRows ){
                            $class = 'detail-row-template';
                            $overwrite = false;
                            $disabled = true; 
                        } else {  
                            $apPkey = $rsAPPaymentDetail[$i]['apkey'];
                            $rsAP = $rsAPCol[$apPkey];
                            $rsSupplier = $rsSupplierCol[$rsAP['supplierkey']];
                            //$rsAP = $objAP->getDataRowById($rsAPPaymentDetail[$i]['apkey']); 
                            $_POST['hidDetailKey[]'] =  $rsAPPaymentDetail[$i]['pkey'];
                            $_POST['hidAPKey[]'] =  $rsAPPaymentDetail[$i]['apkey'];  
                            $_POST['outstanding[]'] =  $obj->formatNumber($rsAPPaymentDetail[$i]['outstanding']); 
                            $_POST['amount[]'] =   $obj->formatNumber($rsAPPaymentDetail[$i]['amount']);  
                            $_POST['chkPick[]'] =  1;

                            
                            $rsAP['jocode'] = $rsJob[$apPkey]['jocode'];
                            $rsAP['jodate'] = $rsJob[$apPkey]['jodate']; 
                            
                        }
                 ?>
            
                  <div class="div-table-row <?php echo $class; ?>"> 
                        <div class="div-table-col detail-col-detail" style="font-weight:bold;height:2.6em;">
                            <span class="apcode"><?php echo $rsAP['code']; ?></span>
                            <?php //echo $obj->inputText('apCode[]',array('disabled' => $disabled, 'overwritePost' => $overwrite )); ?>
                            <?php echo $obj->inputHidden('hidAPKey[]',array('disabled' => $disabled, 'overwritePost' => $overwrite )); ?>
                            <?php echo $obj->inputHidden('hidDetailKey[]',array('disabled' => $disabled,'overwritePost' => $overwrite)); ?>
                        </div> 
                        <div class="div-table-col detail-col-detail" style="text-align:center;">
                            <div class="trdate" ><?php echo $obj->formatDBDate($rsAP['trdate'],'d / m / Y'); ?></div>
                            <?php //echo $obj->inputText('refDate[]',array('overwritePost' => $overwrite, 'readonly' => true, 'etc' => 'style="text-align:center"')); ?>
                        </div> 
                        <div class="div-table-col detail-col-detail">
                            <span class="suppliername"><?php echo $rsSupplier['name']; ?></span>
                        </div>
                        <div class="div-table-col detail-col-detail">
                            <span class="refcode"><?php echo $rsAP['refcode']; ?></span>
                            <?php //echo $obj->inputText('refCode[]',array('overwriteP  ost' => $overwrite, 'readonly' => true)); ?>
                        </div>
                        <div class="div-table-col detail-col-detail">
                            <span class="jocode"><?php echo (empty($rsAP['jocode'])) ? '-' : $rsAP['jocode']; ?></span>
                            <?php //echo $obj->inputText('refCode[]',array('overwriteP  ost' => $overwrite, 'readonly' => true)); ?>
                        </div> 
                        <div class="div-table-col detail-col-detail" style="text-align:center;">
                            <div class="jodate" ><?php echo (empty($rsAP['jodate'])) ? '-' : $obj->formatDBDate($rsAP['jodate'],'d / m / Y'); ?></div>
                            <?php //echo $obj->inputText('refDate[]',array('overwritePost' => $overwrite, 'readonly' => true, 'etc' => 'style="text-align:center"')); ?>
                        </div> 
                        <div class="div-table-col detail-col-detail" style="text-align:right;">
                            <span class="apamount"><?php echo $obj->formatNumber($rsAP['amount']); ?></span>
                            <?php //echo $obj->inputNumber('apAmount[]',array('overwritePost' => $overwrite, 'readonly' => true, 'disabled' => $disabled,'etc' => 'style="text-align:right"' )); ?></div> 
                        <div class="div-table-col detail-col-detail" style="text-align:right;">
                            <span class="outstanding"><?php echo $obj->formatNumber($rsAPPaymentDetail[$i]['outstanding']); ?></span>
                            <?php echo $obj->inputHidden('outstanding[]',array('disabled' => $disabled, 'overwritePost' => $overwrite)); ?>
                            <?php //echo $obj->inputNumber('outstanding[]',array('overwritePost' => $overwrite,'readonly' => true,  'disabled' => $disabled,'etc' => 'style="text-align:right"')); ?>
                        </div> 
                        <div class="div-table-col detail-col-detail" style="text-align:right;">
                            <span class="amount"><?php echo $obj->formatNumber($rsAPPaymentDetail[$i]['amount']); ?></span>
                            <?php echo $obj->inputHidden('amount[]',array('disabled' => $disabled, 'overwritePost' => $overwrite)); ?>
                            <?php //echo $obj->inputNumber('amount[]',array('overwritePost' => $overwrite, 'disabled' => $disabled, 'etc' => 'style="text-align:right"; ')); ?>
                        </div> 
                        <div class="div-table-col detail-col-detail <?php echo $obj->hideOnDisabled(); ?> icon-col" style=" width:30px; text-align:center"><?php echo $obj->inputCheckBox('chkPick[]',  array('value'=> 1, 'disabled' => $disabled) ); ?></div>
                        <div class="div-table-col detail-col-detail <?php echo $obj->hideOnDisabled(); ?> icon-col"><?php echo $obj->inputLinkButton('btnDeleteRows' , '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button', 'etc' => 'tabIndex="-1"')); ?> </div>
                   </div>

                 <?php }   ?> 
                   
         </div>        
          
          <div style="clear:both; height:1em;"></div> 
          <!-- <div style="float:left; display:inline-block;"><?php echo $obj->inputButton('btnAddRows', $obj->lang['addRows'],array('class' =>'btn btn-primary btn-second-tone')); ?></div> -->
        <div>     
    
            <div class="div-table transaction-detail" style="float:right;">
                    
                    <div class="div-table" style="width:100%; margin-top:1em"> 
                        <div class="div-table-row  form-group"> 
                            <div class="div-table-col-5" style="text-align:right;">Total</div>  
                            <div class="div-table-col-3" style="width:180px; text-align:right;"> 
                                <!-- <span class="total"><?php //echo $obj->formatNumber($rs[0]['grandtotal']); ?></span> -->
                                <?php echo $obj->inputNumber('total', array('readonly' => true, 'etc' => 'style="text-align:right;" ')); ?> 
                            </div> 
                            <div class="div-table-col-3 <?php echo $obj->hideOnDisabled(); ?> icon-col" style="width:35px;"></div> 
                            <div class="div-table-col-3 <?php echo $obj->hideOnDisabled(); ?> icon-col"></div> 
                        </div>    
                    </div>


                    <div class="mnv-total-group mnv-payment-method" style="margin-top:1em">  
                        <div class="div-table" style="width: 100%">
                              <div class="div-table-row  form-group"> 
                                    <div class="div-table-col-3" style="text-align:right;"> 
                                           <?php echo $obj->lang['totalPayment']; ?>
                                    </div>  
                                    <div class="div-table-col-3"  style="width:180px"> 
                                            <?php echo $obj->inputCollapsibleNumber('totalPayment', array('readonly' => true, 'etc' => 'style="text-align:right;" ')); ?> 
                                    </div> 
                                    <div class="div-table-col-3 <?php echo $obj->hideOnDisabled(); ?> icon-col" style="width:35px;"></div> 
                                    <div class="div-table-col-3  <?php echo $obj->hideOnDisabled(); ?> icon-col"></div> 
                              </div>
                        </div>
                         
                        <div class="mnv-total-group-detail ">
                            <div class="div-table transaction-detail" style="width: 100%;">
                                <?php 

                                    $totalRows = count($rsAPPaymentMethodDetail);
                                    for($i=0;$i<=$totalRows;$i++) {
                                            $class =  'transaction-detail-row';
                                            $overwrite = true; 
                                            $disabled = false; 

                                            if ($i == $totalRows ){
                                                $class = 'payment-method-row-template row-template'; 
                                                $overwrite = false; 
                                                $disabled = true; 
                                            } else {   
                                                $_POST['hidDetailPaymentKey[]'] = $rsAPPaymentMethodDetail[$i]['pkey'];
                                                $_POST['selPaymentMethod[]'] = $rsAPPaymentMethodDetail[$i]['paymentkey'];
                                                $_POST['selVoucher[]'] = $rsAPPaymentMethodDetail[$i]['cashbankvoucherkey'];
                                                $_POST['paymentMethodValue[]'] = $obj->formatNumber($rsAPPaymentMethodDetail[$i]['amount']); 
                                            }
                                ?> 

                                <div class="div-table-row form-group <?php echo $class; ?>">
                                    <div class="div-table-col-3" style="text-align:right;">  
                                            <?php echo $obj->inputHidden('hidDetailPaymentKey[]',array('overwritePost' => $overwrite,  'disabled' => $disabled)); ?>
                                            <?php echo  (ADV_FINANCE && TEST_VOUCHER) ? $obj->inputSelect('selVoucher[]', $arrAvailableVoucher, array('overwritePost' => $overwrite, 'disabled' => $disabled))
                                                                        : $obj->inputSelect('selPaymentMethod[]', $arrPaymentMethod, array('overwritePost' => $overwrite, 'disabled' => $disabled)) 
                                            ?>                                </div>  
                                    <div class="div-table-col-3" style="width:180px"> 
                                        <?php echo $obj->inputNumber('paymentMethodValue[]', array('overwritePost' => $overwrite, 'disabled' => $disabled, 'class'=>'form-control inputnumber mnv-detail-field','etc' => 'style="text-align:right;"')); ?>
                                    </div>  
                                    <div class="div-table-col-3 <?php echo $obj->hideOnDisabled(); ?> icon-col">
                                        <?php echo $obj->inputLinkButton('btnDeleteRows', '<i class="fas fa-times"></i>',array('etc' => 'tabIndex="-1"', 'class' =>'btn btn-link remove-button' )); ?>
                                    </div>
                                    <div class="div-table-col-3 <?php echo $obj->hideOnDisabled(); ?> icon-col" style="width:30px;"></div> 
                                </div> 

                                <?php } ?> 

                                <div class="div-table-row form-group ">
                                    <div class="div-table-col-3"></div>   
                                <div class="div-table-col-3">
                                        <div class="form-detail-button mnv-total-group-hide-detail" style="float:right; text-align:right;" ><?php echo ucwords($obj->lang['hideDetail']); ?> </div> 
                                    </div>
                                    <div class="div-table-col-3 <?php echo $obj->hideOnDisabled(); ?> icon-col" style="width:35px;"></div> 
                                    <div class="div-table-col-3 <?php echo $obj->hideOnDisabled(); ?>"></div>
                                </div>  
                                <div class="div-table-row form-group ">
                                    <div class="div-table-col-3 " style="height:1em"></div> 
                                    <div class="div-table-col-3 "></div> 
                                    <div class="div-table-col-3 <?php echo $obj->hideOnDisabled(); ?> icon-col" style="width:35px;"></div> 
                                    <div class="div-table-col-3  <?php echo $obj->hideOnDisabled(); ?> icon-col"></div>
                                </div>  
                            
                            </div>   
                        </div>
                    </div> 


                    <div class="div-table"  style="width: 100%;margin-top:1em">
                        <div class="div-table-row  form-group"> 
                            <div class="div-table-col-3" style="text-align:right;">
                                   <?php echo $obj->lang['balance']; ?>  
                            </div>  
                            <div class="div-table-col-3" style="width:180px;"> 
   								<?php echo $obj->inputNumber('balance', array( 'readonly' => true, 'etc' => 'style="text-align:right;"' )); ?> 
                            </div>  
                            <div class="div-table-col-3 <?php echo $obj->hideOnDisabled(); ?> icon-col" style="width:35px;"></div> 
                            <div class="div-table-col-3  <?php echo $obj->hideOnDisabled(); ?> icon-col"></div>
                        </div>
                    </div> 

                </div>

        </div>
         
        <div class="form-button-margin"></div>
        <div class="form-button-panel" > 
         <?php  echo $obj->generateSaveButton(array(),true);?>
        </div> 
        
    </form>   
   
     <?php  echo $obj->showDataHistory(); ?>
</div> 
</body>

</html>
