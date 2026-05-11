<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass('CashAdvanceRealization.class.php');
$cashAdvanceRealization = createObjAndAddToCol( new CashAdvanceRealization()); 
$cashAdvance = createObjAndAddToCol( new CashAdvance()); 
$employee = createObjAndAddToCol( new Employee()); 
$warehouse = createObjAndAddToCol( new Warehouse()); 
$chartOfAccount = createObjAndAddToCol( new ChartOfAccount()); 
$obj = $cashAdvanceRealization;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true)); 

$formAction = 'cashAdvanceRealizationList';

$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false;

$editWarehouseInactiveCriteria = ''; 
  
$rsDetail = array();
$rsDetailCash = array();
$rsCARCost = array();

$_POST['trDate'] = date('d / m / Y');

$rs = prepareOnLoadData($obj);  

if (!empty($_GET['id'])){ 
	$id = $_GET['id'];	
	 
	$rsDetail = $obj->getDetailWithRelatedInformation($id);
	$rsDetailCash = $obj->getDetailCashAdvance($id);
    $rsCARCost = $obj->getCostDetail($id);  
    $_POST['trDate'] = $obj->formatDBDate($rs[0]['trdate'],'d / m / Y');
    $_POST['note'] = $rs[0]['trdesc'];
	$_POST['selWarehouseKey'] = $rs[0]['warehousekey'];
    
    if(!empty($rs[0]['refkey'])){
        $rsCashAdvance = $cashAdvance->getDataRowById($rs[0]['refkey']);
        $_POST['cashAdvanceCode'] = $rsCashAdvance[0]['code'];
        $_POST['hidCashAdvanceKey'] = $rsCashAdvance[0]['pkey'] ;
        
        $rsRecipient = $employee->getDataRowById($rsCashAdvance[0]['employeekey']);
        
        $_POST['recipient'] = $rsRecipient[0]['name'] ;
        $_POST['amountCash'] = $obj->formatNumber($rsCashAdvance[0]['amount']) ;
    }
    
    $_POST['amount'] = $obj->formatNumber($rs[0]['amount']); 
    $_POST['total'] = $obj->formatNumber($rs[0]['total']); 
    $_POST['totalCost'] = $obj->formatNumber($rs[0]['totalcost']); 
    $_POST['balance'] = $obj->formatNumber($rs[0]['balance']); 
    
	$rsCOA = $chartOfAccount->getDataRowById($rs[0]['coakey']);
	$_POST['COAClosingName'] = $rsCOA[0]['code'].' - '.$rsCOA[0]['name'] ;
	$_POST['hidCOAClosingKey'] = $rs[0]['coakey'] ;
	$editWarehouseInactiveCriteria = ' or  '.$warehouse->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['warehousekey']);  
    
}

$arrStatus = $obj->convertForCombobox($obj->getAllStatus(),'pkey','status');    
$arrWarehouse = $obj->convertForCombobox($warehouse->searchData('','',true,' and ('.$warehouse->tableName.'.statuskey = 1' .$editWarehouseInactiveCriteria.')'),'pkey','name');  
$arrCashType = array();
$arrCashType[1] = $obj->lang['jobOrder'];
$arrCashType[4]= $obj->lang['jobHeader'];
$arrCashType[2] = $obj->lang['downpayment'];
$arrCashType[3] = $obj->lang['cost'];

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title> 
<script type="text/javascript">  
   
	jQuery(document).ready(function(){  
        var tabID = <?php echo ($isQuickAdd) ?  $_GET['tabID'] :  'selectedTab.newPanel[0].id';  ?>  
			
        
         var cashAdvanceRealization = new CashAdvanceRealization(tabID, <?php echo json_encode($rs); ?>);
    
         prepareHandler(cashAdvanceRealization);
        
        
        var fieldValidation =  {
                                 code: { 
                                        validators: {
                                            notEmpty: {
                                                message: phpErrorMsg.code[1]
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
        <?php echo $obj->inputHidden('amountCash'); ?>

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
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['date']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputDate('trDate',array('etc' => 'max-days=14')); ?>  
                                        </div> 
                                    </div> 
								 <div class="form-group">
									<label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['settlementAccount']); ?></label> 
									<div class="col-xs-9"> 
										 <?php 
											   echo  $obj->inputAutoComplete( array(
																		'objRefer' => $chartOfAccount,
																		'revalidateField' => true, 
																		'element' => array('value' => 'COAClosingName',
																						   'key' => 'hidCOAClosingKey'),
																		'source' =>array(
																							'url' => 'ajax-coa.php',
																							'data' => array(  'action' =>'searchData', 'iscashbank' => '1' )
																						)  
															));
											?>
									</div> 
								</div>                                  
                                <div class="form-group">
									<label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['note']); ?></label> 
									<div class="col-xs-9"> 
										<?php echo  $obj->inputTextArea('note', array('etc' => 'style="height:10em;"','allowedStatusForEdit' => array (1,2))); ?>                                           
									</div> 
								</div>  
                             </div>
                         
                    </div>     
              <div class="div-table-col">
                     <div class="div-tab-panel">    
						  <div class="div-table-caption border-green"><?php echo ucwords($obj->lang['cashAdvance']); ?></div>
						  <div class="div-table mnv-transaction transaction-detail" style="width:100%;">
								<div class="div-table-row"> 
									<div class="div-table-col detail-col-header" style="border:0"><?php echo ucwords($obj->lang['cashAdvance']); ?></div>
									<div class="div-table-col detail-col-header" style="width:160px;border:0"><?php echo ucwords($obj->lang['recipient']); ?></div>
									<div class="div-table-col detail-col-header" style="width:110px;border:0;text-align:right;"><?php echo ucwords($obj->lang['amount']); ?></div>
									<div class="div-table-col detail-col-header  <?php echo $obj->hideOnDisabled(); ?> icon-col" style="border:0"></div>
									<div class="div-table-col detail-col-header  <?php echo $obj->hideOnDisabled(); ?> icon-col" style="border:0"></div> 
								</div>

								<?php 
									$totalDetail = count($rsDetailCash); 

									for ($i=0;$i<=$totalDetail; $i++){  

										$class =  'transaction-detail-row';
										$overwrite = true;
										$disabled = false; 
										
										if ($i == $totalDetail ){
											$class = 'detail-row-template';
											$overwrite = false;
											$disabled = true; 
										} else {    
											$_POST['hidDetailItemKey[]'] =  $rsDetailCash[$i]['pkey'];
											$_POST['hidCashAdvanceKey[]'] =  $rsDetailCash[$i]['cashadvancekey']; 
											$_POST['cashAdvanceCode[]'] =  $rsDetailCash[$i]['cashadvancecode']; 
											$_POST['cashAdvanceRecipient[]'] =  $rsDetailCash[$i]['employeename']; 
											$_POST['cashAdvanceAmount[]'] =  $obj->formatNumber($rsDetailCash[$i]['amount']);  
										}  

								?>
 
								<div class="div-table-row <?php echo $class; ?>" style=""> 
									<div class="div-table-col detail-col-detail">
										<?php echo $obj->inputHidden('hidDetailItemKey[]',array('overwritePost' => $overwrite, 'disabled' => $disabled)); ?> 
										<?php echo $obj->inputText('cashAdvanceCode[]',array('overwritePost' => $overwrite, 'allowedStatusForEdit' => array (1))); ?>
										<?php echo $obj->inputHidden('hidCashAdvanceKey[]',array('overwritePost' => $overwrite,'disabled' => $disabled)); ?>
									 </div> 
									<div class="div-table-col detail-col-detail">
										<?php echo $obj->inputText('cashAdvanceRecipient[]',array('overwritePost' => $overwrite,'readonly'=>true)); ?>
									</div> 
									<div class="div-table-col detail-col-detail">
										<?php echo $obj->inputNumber('cashAdvanceAmount[]',array('overwritePost' => $overwrite,'readonly'=>true, 'etc' => 'style="text-align:right"')); ?>
									</div>
									<div class="div-table-col detail-col-detail  <?php echo $obj->hideOnDisabled(); ?>  icon-col"><?php echo $obj->inputLinkButton('btnAddCashAadvanceRow' , '<i class="fas fa-plus-circle"></i>', array('class' => 'btn btn-link add-row-button', 'etc' => 'attr-template="detail-row-template"')); ?></div>
                                    <div class="div-table-col detail-col-detail <?php echo $obj->hideOnDisabled(); ?>"><?php echo $obj->inputLinkButton('btnDeleteRowsCash' , '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button', 'etc' =>  'tabIndex="-1" style="padding:6px 0"')); ?></div>
								</div>

							<?php } ?> 

						 </div> 
						 <div style="clear:both; height:0.5em;"></div>  
						 <div class="div-table transaction-detail" style="width:100%;">
							<div class="div-table-row">
								<div class="div-table-col detail-col-detail"></div>
								<div class="div-table-col detail-col-detail"  style="width:110px;"><?php echo $obj->inputNumber('amount',array('readonly' => true, 'etc' => 'style="text-align:right;')); ?></div>
							   <div class="div-table-col detail-col-detail  <?php echo $obj->hideOnDisabled(); ?>  icon-col"></div>
							   <div class="div-table-col detail-col-detail  <?php echo $obj->hideOnDisabled(); ?>  icon-col"></div>
							</div>     
						 </div>
					</div> 
                </div>
            </div>
      </div> 
        <div class="div-table mnv-transaction mnv-job transaction-detail" style="width:100%; border-bottom:1px solid #333; ">
                <div class="div-table-row"> 
                    <div class="div-table-col" style="padding:0">
                        <div class="div-table" style="width:100%">
                            <div class="div-table-row">
                                <div class="div-table-col detail-col-header" style="width:100px;"><?php echo ucwords($obj->lang['jobType']); ?></div> 
								<div class="div-table-col detail-col-header" style="width:250px;"><?php echo ucwords($obj->lang['description']); ?></div>  
								<!--<div class="div-table-col detail-col-header" style="width:180px;"><?php echo ucwords($obj->lang['supplier']); ?></div>-->  
								<div class="div-table-col detail-col-header" ><?php echo ucwords($obj->lang['service']); ?></div>
								<div class="div-table-col detail-col-header" style="width:60px;text-align:right;"><?php echo ucwords($obj->lang['qty']); ?></div> 
								<div class="div-table-col detail-col-header" style="width:100px;text-align:right;"><?php echo ucwords($obj->lang['amount']); ?></div>
                                <div class="div-table-col detail-col-header" style="width:40px; text-align:center"><?php echo ucwords($obj->lang['reim']); ?></div>   
								<div class="div-table-col detail-col-header" style="width:70px;text-align:right;"><?php echo ucwords($obj->lang['PPN']); ?> %</div> 
								<div class="div-table-col detail-col-header" style="width:30px;text-align:center;">Incl</div> 
								<div class="div-table-col detail-col-header" style="width:120px;text-align:right;"><?php echo ucwords($obj->lang['subtotal']); ?></div> 
								<div class="div-table-col detail-col-header icon-col <?php echo $obj->hideOnDisabled(); ?>" style="width:35px;"></div>
                            </div>
                        </div>
                    </div>
                </div>
      
            
				<?php  
                    $totalRows = count($rsDetail);      
		            $deleteBtn = $obj->inputLinkButton('btnDeleteRows' , '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button', 'etc' => 'tabIndex="-1" ')); 
              
                    // kalo cost nanti diproses setelah diselesaikan
                    $arrException = array(3);
            
                    for ($i=0;$i<=$totalRows; $i++){  
			            $class =  'transaction-detail-row';
                        $overwrite = true;
                        
                        $allowUpdate = (empty($rsDetail[$i]['reftranskey']) || in_array($rsDetail[$i]['cashtypekey'],$arrException)) ? true : false; 
                        $readonly = !$allowUpdate;
                        
                        $disable = '';  
                        $etc = '';  
                        $showJO = '';
                        $showCost = 'display:none;';
                        $showJOHeader = 'display:none;';
                        $style = '';
			             if ($i == $totalRows ){
                            $class = 'job-row-template row-template';
                            $overwrite = false;
                            $disable = 'disabled="disabled"';
							$style = 'display: none !important';
                        } else {  
                               
                            $showJO = 'display:none;';
                            $showJOHeader = 'display:none;';
                            $showCost = 'display:none;';
                
                            if($rsDetail[$i]['cashtypekey']==1 || $rsDetail[$i]['cashtypekey']==2){
                                // JO & DP
                                $_POST['hidJobOrderKey[]'] =  $rsDetail[$i]['joborderkey'];
                                $_POST['jobOrderCode[]'] =  $rsDetail[$i]['jobordercode'];
                                $_POST['hidContainerDetailKey[]'] =  $rsDetail[$i]['itemkey'];
                                $_POST['containerDetailName[]'] =  $rsDetail[$i]['containername'];
                            }elseif($rsDetail[$i]['cashtypekey']==3){ 
                                // biaya
                                $showCost = '';
                                $_POST['hidCOAKey[]'] =  $rsDetail[$i]['coakey'];
                                $_POST['COAName[]'] =  $rsDetail[$i]['coaname'];   
                            }elseif($rsDetail[$i]['cashtypekey']==4){ 
                                // JO Header
                                $showJOHeader = '';
                                $_POST['hidJobHeaderKey[]'] =  $rsDetail[$i]['jobheaderkey'];
                                $_POST['jobHeaderCode[]'] =  $rsDetail[$i]['jobheadercode'];
                                $_POST['hidContainerHeaderDetailKey[]'] =  $rsDetail[$i]['itemkey'];
                                $_POST['containerHeaderDetailName[]'] =  $rsDetail[$i]['containername']; 
							}
  
                            $_POST['hidDetailKey[]'] =  $rsDetail[$i]['pkey']; 
                            $_POST['hidServiceKey[]'] =  $rsDetail[$i]['servicekey']; 
                            $_POST['serviceName[]'] =  $rsDetail[$i]['servicename']; 
                            
                            $_POST['amountDetail[]'] =  $obj->formatNumber($rsDetail[$i]['amount']); 
                            $_POST['qty[]'] =  $obj->formatNumber($rsDetail[$i]['qty']); 
                            $_POST['subtotal[]'] =  $obj->formatNumber($rsDetail[$i]['subtotal']); 
                            $_POST['hidSupplierKey[]'] =  $rsDetail[$i]['supplierkey'];
                            $_POST['supplierName[]'] =  $rsDetail[$i]['suppliername'];
                            
                            $_POST['chkIncludeTax[]'] =  $rsDetail[$i]['ispriceincludetax'];
                            $_POST['taxPercentage[]'] =  $obj->formatNumber($rsDetail[$i]['taxpercentage']);
                            $_POST['selJobType[]'] =  $rsDetail[$i]['cashtypekey'];
                            $_POST['refCode[]'] =  $rsDetail[$i]['refcode'];
                            $_POST['description[]'] =  $rsDetail[$i]['description'];
                            $_POST['hidRefTransKey[]'] =  $rsDetail[$i]['reftranskey'];
                            $_POST['chkIsReimburse[]'] = $rsDetail[$i]['isreimburse'];
               
                        }
				 
                ?>
			
				<div class="div-table-row <?php echo $class; ?>" style="<?php echo $style ; ?>">
                    <div class="div-table-col" style="padding:5px 0">
                        <div class="div-table" style="width:100%">
                            <div class="div-table-row">
                                <div class="div-table-col detail-col-detail" style="width:100px;">
									<?php echo $obj->inputSelect('selJobType[]',$arrCashType,array('overwritePost' => $overwrite,'readonly' => $readonly, 'disabled' =>  $disable)); ?>
									<?php echo $obj->inputHidden('hidDetailKey[]',array('overwritePost' => $overwrite, 'disabled' =>  $disable)); ?>
									<?php echo $obj->inputHidden('hidRefTransKey[]'); ?>
								</div> 

								<div class="div-table-col detail-col-detail" style="width:250px;">
										<div class="type-1 type-2" style="<?php echo $showJO ; ?>"> 
											<div class="flex">
												<div><?php echo $obj->inputText('jobOrderCode[]',array('overwritePost' => $overwrite,'readonly' => $readonly,'etc' => 'placeholder="'.$obj->lang['pleasestarttyping'].'"', 'disabled' => $disable )); ?>
													 <?php echo $obj->inputHidden('hidJobOrderKey[]',array('overwritePost' => $overwrite, 'readonly' => $readonly, 'disabled' => $disable)); ?>
												</div>
												<div class="consume" style="width:60px;">
													<?php echo $obj->inputHidden('hidContainerDetailKey[]',array('overwritePost' => $overwrite, 'readonly' => $readonly,'disabled' =>  $disable)); ?>
													<?php echo $obj->inputText('containerDetailName[]',array('overwritePost' => $overwrite,'readonly' => $readonly, 'disabled' =>  $disable,'etc' => 'placeholder="'.$obj->lang['container'].'"')); ?>
												</div>
											</div>
									</div>
									<div class="type-3" style="<?php echo $showCost ; ?>">
										<?php echo $obj->inputText('COAName[]',array('overwritePost' => $overwrite,'readonly' => $readonly, 'disabled' =>  $disable)); ?>
										<?php echo $obj->inputHidden('hidCOAKey[]',array('overwritePost' => $overwrite, 'readonly' => $readonly,'disabled' =>  $disable)); ?>                        
									</div>
									<div class="type-4" style="<?php echo $showJOHeader ; ?>">
										<div class="flex">
											<div><?php echo $obj->inputText('jobHeaderCode[]',array('overwritePost' => $overwrite,'readonly' => $readonly,'etc' => 'placeholder="'.$obj->lang['pleasestarttyping'].'"', 'disabled' => $disable )); ?>
												 <?php echo $obj->inputHidden('hidJobHeaderKey[]',array('overwritePost' => $overwrite, 'readonly' => $readonly, 'disabled' => $disable)); ?>
											</div>
											<div class="consume" style="width:60px;">
												<?php echo $obj->inputHidden('hidContainerHeaderDetailKey[]',array('overwritePost' => $overwrite, 'readonly' => $readonly,'disabled' =>  $disable)); ?>
												<?php echo $obj->inputText('containerHeaderDetailName[]',array('overwritePost' => $overwrite, 'readonly' => $readonly,'disabled' =>  $disable,'etc' => 'placeholder="'.$obj->lang['container'].'"')); ?>
											</div>
										</div>                        
									</div>
								</div>

								<!--<div class="div-table-col detail-col-detail" style="width:180px;">
									<?php echo $obj->inputText('supplierName[]',array('overwritePost' => $overwrite, 'readonly' => $readonly,'disabled' =>  $disable)); ?>
									<?php echo $obj->inputHidden('hidSupplierKey[]',array('overwritePost' => $overwrite, 'readonly' => $readonly,'disabled' =>  $disable)); ?>
								</div>-->
								<div class="div-table-col detail-col-detail">
									<?php echo $obj->inputText('serviceName[]',array('overwritePost' => $overwrite, 'readonly' => $readonly,'disabled' =>  $disable)); ?>
									<?php echo $obj->inputHidden('hidServiceKey[]',array('overwritePost' => $overwrite, 'readonly' => $readonly,'disabled' =>  $disable)); ?>
								</div>
								<div class="div-table-col detail-col-detail" style="width:60px;text-align:right;">
									<?php echo $obj->inputNumber('qty[]',array('overwritePost' => $overwrite, 'readonly' => $readonly, 'disabled' =>  $disable,  'etc' => 'style="text-align:right;" ' .$etc)); ?>
								</div> 
								<div class="div-table-col detail-col-detail" style="width:100px;text-align:right;">
									<?php echo $obj->inputNumber('amountDetail[]',array('overwritePost' => $overwrite, 'readonly' => $readonly,'disabled' =>  $disable,  'etc' => 'style="text-align:right;" ' .$etc)); ?>
								</div> 
                                
								<div class="div-table-col detail-col-detail" style="width:40px;text-align:center;">
									 <?php echo $obj->inputCheckBox('chkIsReimburse[]'); ?>
								</div>  
								<div class="div-table-col detail-col-detail" style="width:70px;text-align:right;">
									<?php echo $obj->inputNumber('taxPercentage[]',array('overwritePost' => $overwrite, 'readonly' => $readonly,'disabled' =>  $disable,  'etc' => 'style="text-align:right;" ' .$etc)); ?>
								</div>   
								<div class="div-table-col detail-col-detail" style="width:30px;text-align:center">
									<?php echo $obj->inputCheckBox('chkIncludeTax[]',array('overwritePost' => $overwrite, 'readonly' => $readonly, 'disabled' =>  $disable,  'etc' => 'style="text-align:right;" ' .$etc)); ?>
								</div> 
								<div class="div-table-col detail-col-detail" style="width:120px;text-align:center;">
									<?php echo $obj->inputNumber('subtotal[]',array('overwritePost' => $overwrite, 'readonly' => true, 'readonly' => 'true', 'disabled' =>  $disable ,  'etc' => 'style="text-align:right;" ' .$etc)); ?>
								 </div>  

								<div class="div-table-col detail-col-detail  <?php echo $obj->hideOnDisabled(); ?>  icon-col" style="width:35px;"><?php if(!$readonly) echo $obj->inputLinkButton('btnDeleteSalesRows' , '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button', 'etc' => 'tabIndex="-1"')); ?></div>

							</div>
                        </div> 
                        <div class="div-table" style="width:100%">
                            <div class="div-table-row">
                                <div class="div-table-col detail-col-detail" style="width:200px;vertical-align:top">
									<?php echo $obj->inputText('supplierName[]',array('overwritePost' => $overwrite, 'readonly' => $readonly,'disabled' =>  $disable, 'class' => 'form-control label-style', 'etc' => 'placeholder="'.$obj->lang['supplier'].'"')); ?>
									<?php echo $obj->inputHidden('hidSupplierKey[]',array('overwritePost' => $overwrite, 'readonly' => $readonly,'disabled' =>  $disable)); ?>
								</div>
								<div class="div-table-col detail-col-detail" style="width:150px;vertical-align:top">
									<?php echo $obj->inputText('refCode[]',array('overwritePost' => $overwrite, 'readonly' => $readonly,'disabled' =>  $disable, 'class' => 'form-control label-style', 'etc' => 'placeholder="'.$obj->lang['reference'].'"')); ?>
								</div>
                                <div class="div-table-col detail-col-detail"  style="vertical-align:top">
									<?php echo $obj->inputText('description[]',array('overwritePost' => $overwrite,'readonly' => $readonly, 'class' => 'form-control label-style', 'etc' => 'placeholder="'.$obj->lang['note'].'"')); ?>
								</div> 
                                <div class="div-table-col detail-col-detail" style="width:120px;"></div>
                                <div class="div-table-col detail-col-detail icon-col <?php echo $obj->hideOnDisabled(); ?>" style="width:30px;vertical-align:top;"></div>
                            </div>
                        </div> 
                    </div>
                </div>
 
            <?php } ?> 
                   
         </div>        
        
          <div style="clear:both; height:1em;"></div> 
          <?php if (in_array($rs[0]['statuskey'],array(1,2)) || empty($rs)) { ?> 
            <div style="float:left; display:inline-block;"><?php echo $obj->inputButton('btnAddDetailRow', $obj->lang['addRows'],array('class' => 'btn btn-primary btn-second-tone')); ?></div>
          <?php } ?>   
        <div>   
        <div class="div-table transaction-detail" style="float:right;">
                <div class="mnv-total-group mnv-cost" style="margin-top:1em">  
                            <div class="div-table" style="width: 100%">
                                  <div class="div-table-row  form-group"> 
                                        <div class="div-table-col-3" style="text-align:right;"> 
                                               <?php echo $obj->lang['totalCost']; ?>
                                        </div>  
                                        <div class="div-table-col-3"  style="width:120px"> 
                                                <?php echo $obj->inputCollapsibleNumber('totalCost', array('readonly' => true, 'etc' => 'style="text-align:right;" ')); ?> 
                                        </div> 
                                        <div class="div-table-col-3  <?php echo $obj->hideOnDisabled(); ?> icon-col"></div> 
                                  </div>
                            </div>

                            <div class="mnv-total-group-detail ">
                            <div class="div-table transaction-detail" style="width: 100%">
                                <?php 

                                    $totalRows = count($rsCARCost);
                                    for($i=0;$i<=$totalRows;$i++) {
                                            $class =  'transaction-detail-row';
                                            $overwrite = true; 
                                            $disabled = false; 

                                            if ($i == $totalRows ){
                                                $class = 'cost-row-template row-template'; 
                                                $overwrite = false; 
                                                $disabled = true; 
                                            } else {   
                                                $_POST['hidDetailCostKey[]'] = $rsCARCost[$i]['pkey'];
                                                $_POST['hidCostKey[]'] = $rsCARCost[$i]['costkey'];
                                                $_POST['costName[]'] = $rsCARCost[$i]['costname'];
                                                $_POST['costAmount[]'] = $obj->formatNumber($rsCARCost[$i]['amount']); 
                                            }
                                ?> 

                                <div class="div-table-row form-group <?php echo $class; ?>">
                                    <div class="div-table-col-3" style="text-align:right;">  
                                            <?php echo $obj->inputHidden('hidDetailCostKey[]',array('overwritePost' => $overwrite,  'disabled' => $disabled)); ?>
                                            <?php echo  $obj->inputText('costName[]', array('overwritePost' => $overwrite, 'disabled' => $disabled)); ?>
                                            <?php echo $obj->inputHidden('hidCostKey[]',array('overwritePost' => $overwrite,  'disabled' => $disabled)); ?>
                                    </div>  
                                    <div class="div-table-col-3" style="width:120px"> 
                                           <?php echo $obj->inputNumber('costAmount[]', array('overwritePost' => $overwrite, 'disabled' => $disabled,'add-class'=>'mnv-detail-field', 'etc' => 'style="text-align:right;"')); ?>
                                    </div>  
                                    <div class="div-table-col-3 <?php echo $obj->hideOnDisabled(); ?> icon-col">
                                        <?php echo $obj->inputLinkButton('btnDeleteRows', '<i class="fas fa-times"></i>',array('etc' => 'tabIndex="-1"', 'class' =>'btn btn-link remove-button' )); ?>
                                    </div>
                                </div> 

                                <?php } ?> 

                                <div class="div-table-row form-group ">
                                    <div class="div-table-col-3"></div>   
                                    <div class="div-table-col-3">
                                        <div class="form-detail-button mnv-total-group-hide-detail" style="float:right; text-align:right;" ><?php echo ucwords($obj->lang['hideDetail']); ?> </div> 
                                    </div>
                                    <div class="div-table-col-3 <?php echo $obj->hideOnDisabled(); ?>"></div>
                                </div>  
                                <div class="div-table-row form-group ">
                                    <div class="div-table-col-3 " style="height:1em"></div> <div class="div-table-col-3 "></div> <div class="div-table-col-3  <?php echo $obj->hideOnDisabled(); ?> "></div>
                                </div>  

                           </div>   
                            </div>
                        </div> 
                    <div class="div-table" style="width: 100%">
                              <div class="div-table-row  form-group"> 
                                    <div class="div-table-col-3" style="text-align:right;"> 
                                           <?php echo $obj->lang['total']; ?>
                                    </div>  
                                    <div class="div-table-col-3" style="width:120px;"> 
                                            <?php echo $obj->inputNumber('total', array('readonly' => true, 'etc' => 'style="text-align:right;" ' )); ?> 
                                    </div> 
                                    <div class="div-table-col-3  <?php echo $obj->hideOnDisabled(); ?> icon-col"></div> 
                              </div>
                        </div>
                    <div class="div-table"  style="width: 100%">
                      <div class="div-table-row  form-group"> 
                            <div class="div-table-col-3" style="text-align:right;">
                                   <?php echo $obj->lang['balance']; ?>  
                            </div>  
                            <div class="div-table-col-3" style="width:120px"> 
                                <?php echo $obj->inputNumber('balance', array( 'readonly' => true, 'etc' => 'style="text-align:right;"' )); ?> 
                            </div>  
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

     <?php echo $obj->showDataHistory(); ?>
</div> 
</body>

</html>
