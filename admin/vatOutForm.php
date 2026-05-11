<?php
require_once '../_config.php';
require_once '../_include-v2.php';

includeClass('VatOut.class.php');

$vatOut = createObjAndAddToCol(new VatOut());
$warehouse = createObjAndAddToCol(new Warehouse());
$currency = createObjAndAddToCol(new Currency()); 

$obj = $vatOut;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
// some modules that have different security object from that of their class

if (!$security->isAdminLogin($securityObject, 10, true));

$formAction = 'vatOutList';

$isQuickAdd = (isset($_GET) && !empty($_GET['quickadd'])) ? true : false;

$rsDetail = array();

$_POST['trDate'] = date('d / m / Y'); 
$_POST['taxPeriod'] = date('F Y');
$_POST['uploadDate'] = date('d / m / Y'); 
	

$_POST['hidCurrentTransactionTypeCodeKey'] = $rsTaxType[0]['pkey'];  
$_POST['hidCurrentTaxPercentage'] =  $rsTaxType[0]['taxpercentage'];  
//$_POST['taxPercentage'] =  $obj->formatnumber($rsTaxType[0]['taxpercentage'],2);  

$rs = prepareOnLoadData($obj);

$editWarehouseInactiveCriteria = '';

$rsTaxType = $obj->getTaxType();
 
$rsAdditionalInfo = array();
$rsAdditionalInfo[0]['pkey'] = 0;
$rsAdditionalInfo[0]['name'] = '-----';
$rsAdditionalInfo[0]['iddefault'] = 0; 
$rsAdditionalInfo = array_merge($rsAdditionalInfo,$obj->getTaxAdditionalInfo());

foreach($rsAdditionalInfo as $row){
    if ($row['isdefault'] == 1)     $_POST['selAdditionalInfo[]'] = $row['pkey']; 
}


$rsStamp = array();
$rsStamp[0]['pkey'] = 0;
$rsStamp[0]['name'] = '-----';
$rsStamp[0]['iddefault'] = 0;
$rsStamp = array_merge($rsStamp,$obj->getTaxFacilityStamp());
//foreach($rsStamp as $row){
//    if ($row['isdefault'] == 1) $_POST['selFacilityStamp[]'] = $row['pkey'];
//}

if (!empty($_GET['id'])) {
    $id = $_GET['id'];

    $rsDetail = $obj->getDetailWithRelatedInformation($id);

    $_POST['trDate'] = $obj->formatDBDate($rs[0]['trdate'], 'd / m / Y'); 
	$_POST['taxPeriod'] = $obj->formatDBDate($rs[0]['taxperiod'], 'F Y');
	
	$_POST['hidCurrentTransactionTypeCodeKey'] = $rs[0]['transactiontypekey'];  
	$_POST['hidCurrentTaxPercentage'] = array_column($rsTaxType,null,'pkey')[$rs[0]['transactiontypekey']]['taxpercentage'];  
	    
// 	$rsFile = array();  
//	if( !empty($rs[0]['file'])){
//		$rsFile[0]['file'] =  $rs[0]['file'];
//	
//		$sourcePath = $obj->defaultDocUploadPath.$obj->uploadFileFolder.$id;
//		$destinationPath = $obj->uploadTempDoc.$obj->uploadFileFolder.$id; 
//		$obj->deleteAll($destinationPath); 
//	
//		if(!is_dir($destinationPath)) 
//			mkdir ($destinationPath,  0755, true);
//				
//		$obj->fullCopy($sourcePath,$destinationPath); 
//	}
}
 
$allowEdit = (empty($rs) || $rs[0]['statuskey'] == 1) ? true : false;

$arrStatus = $obj->convertForCombobox($obj->getAllStatus(), 'pkey', 'status');
$arrWarehouse = $warehouse->generateComboboxOpt(null, array('criteria' => ' and (' . $warehouse->tableName . '.statuskey = 1' . $editWarehouseInactiveCriteria . ')'));
$arrCurrency = $currency->generateComboboxOpt(null, array('criteria' => ' and (' . $currency->tableName . '.statuskey = 1)'));
$arrTransactionTypeCode = $obj->generateComboboxOpt(array('data' => $rsTaxType), array(),'',  array('rel-percentage' => 'taxpercentage'));
$arrVatOutType = $obj->generateComboboxOpt(array('data' => $obj->getVatOutType()));
$arrAdditionalInfo = $obj->generateComboboxOpt(array('data' => $rsAdditionalInfo));
$arrFacilityStamp = $obj->generateComboboxOpt(array('data' => $rsStamp));

$planType = PLAN_TYPE;
$planType['categorykey'] = $obj->getCompanyType();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title></title>
    <script type="text/javascript">
        jQuery(document).ready(function() {
            var tabID = <?php echo ($isQuickAdd) ?  $_GET['tabID'] :  'selectedTab.newPanel[0].id';  ?>;

            var varConstant = {  
				PLAN_TYPE : <?php echo json_encode($planType); ?>,
                COMPANY_TYPE: <?php echo json_encode(COMPANY_TYPE); ?>
            };

            var vatOut = new VatOut(tabID, <?php echo json_encode(array( 'rsDetail' => $rsDetail )); ?>,"<?php echo $obj->uploadFileFolder; ?>",<?php echo json_encode($rsFile); ?>, varConstant);
 
            prepareHandler(vatOut);

            var fieldValidation = {
                code: {
                    validators: {
                        notEmpty: {
                            message: phpErrorMsg.code[1]
                        },
                    }
                }
            };
            setFormValidation(getTabObj(), $('#defaultForm-' + tabID), fieldValidation, <?php echo json_encode($obj->validationFormSubmitParam()); ?>);

        });
    </script>

</head>

<body>
    <div style="width:100%; margin:auto; " class="tab-panel-form">
        <div class="notification-msg"></div>

        <form id="defaultForm" method="post" class="form-horizontal" action="<?php echo $formAction; ?>">
            <?php prepareOnLoadDataForm($obj); ?>

    		<?php echo $obj->inputHidden('hidCurrentTransactionTypeCodeKey'); ?>
    		<?php echo $obj->inputHidden('hidCurrentTaxPercentage'); ?>
			
            <div class="div-table main-tab-table-2">
                <div class="div-table-row">
                    <div class="div-table-col">
                        <div class="div-tab-panel">
                            <div class="div-table-caption border-orange"><?php echo ucwords($obj->lang['generalInformation']); ?></div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['status']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputSelect('selStatus', $arrStatus, array('disabled' => true)) ?>
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
                                    <?php echo $obj->inputDate('trDate', array( 'allowedStatusForEdit' => array (1))); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['warehouse']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputSelect('selWarehouseKey', $arrWarehouse, array( 'allowedStatusForEdit' => array (1))); ?>
                                </div>
                            </div>
    
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['transactionType']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputSelect('selVatOutType', $arrVatOutType, array( 'allowedStatusForEdit' => array (1))); ?>
                                </div>
                            </div>
 
                            <div class="form-group type-filter type-1">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['taxPeriod']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputMonth('taxPeriod', array( 'allowedStatusForEdit' => array (1))); ?>
                                </div>
                            </div> 
                            <div class="form-group type-filter type-1">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['transactionTypeCode']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputSelect('selTransactionTypeCodeKey', $arrTransactionTypeCode, array( 'allowedStatusForEdit' => array (1))); ?>
                                </div>
                            </div>
                            <div class="form-group type-filter type-1">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['taxPercentage']); ?></label>
                                 <div class="col-xs-9">
                                    <?php echo $obj->inputDecimal('taxPercentage', array('readonly' => true)); ?>
                                </div>
                            </div>
<!--
                            <div class="form-group type-filter type-1">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['document']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputText('document', array( 'allowedStatusForEdit' => array (1))); ?>
                                </div>
                            </div>
                            <div class="form-group type-filter type-1">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['additionalId']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputText('additionalId', array( 'allowedStatusForEdit' => array (1))); ?>
                                </div>
                            </div>
-->
							<?php if ($allowEdit) { ?>
                            <div class="form-group  type-filter type-1">
                                <div class="col-xs-3"></div>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputButton('btnImport', $obj->lang['import'], array('class' => 'btn btn-primary btn-second-tone')); ?>
                                </div>
                            </div>
							<?php } ?>
                        </div>
                    </div>
                    <div class="div-table-col">
                        <div class="div-tab-panel">
                            <div class="div-table-caption border-blue"><?php echo ucwords($obj->lang['note']); ?></div>
                            <div class="form-group">
                                <div class="col-xs-12">
                                    <?php echo $obj->inputTextArea('trDesc', array('etc' => 'style="height:10em;"', 'allowedStatusForEdit' => array(1)));
                                    ?>
                                </div>
                            </div>
                        </div>
<!--
                        <div class="div-tab-panel">
                            <div class="div-table-caption border-red"><?php echo ucwords($obj->lang['uploadInformation']); ?></div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['uploadDate']); ?></label>
                                <div class="col-xs-9">
                                   <?php echo $obj->inputDate('uploadDate'); ?>
                                </div>
                            </div>
							<div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['fileName']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputText('fileName'); ?>
                                </div>
                            </div>
							 <div class="form-group">
									<label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['file']); ?></label> 
									<div class="col-xs-9">  
										<div class="item-file-uploader">
											<ul class="file-list" ></ul>
											<div style="clear:both; height:1em; "></div>
											<div class="file-uploader">	
												<noscript><p>Please enable JavaScript to use file uploader.</p></noscript> 
											</div>
										  </div>   
									</div> 
								</div>   
                        </div>
-->
                    </div>
                </div>
            </div>
 
            
            <div class="div-table mnv-transaction transaction-detail mnv-checkbox-group" style="width:100%; ">
                <div class="div-table-row">
							<div class="div-table-col detail-col-header" ><?php echo ucwords($obj->lang['invoiceNumber']); ?></div>
                            <div class="div-table-col detail-col-header" style="width:130px;"><?php echo ucwords($obj->lang['additionalInfo']); ?></div>
                            <div class="div-table-col detail-col-header" style="width:130px;"><?php echo ucwords($obj->lang['facilityStamp']); ?></div>
							<div class="div-table-col detail-col-header  type-filter type-2" style="width:40px; text-align:center;"><?php echo ucwords($obj->lang['void']); ?></div> 
							<div class="div-table-col detail-col-header  type-filter type-3" style="width:200px; "><?php echo ucwords($obj->lang['transactionTypeCode']); ?></div> 
							<div class="div-table-col detail-col-header" style="width:120px; text-align:center;"><?php echo ucwords($obj->lang['invoiceDate']); ?></div> 
							<div class="div-table-col detail-col-header" style="width:80px;"><?php echo ucwords($obj->lang['curr']); ?></div>
							<div class="div-table-col detail-col-header icon-col <?php echo $obj->hideOnDisabled(); ?>"  style="<?php if (!$allowEdit) {?>display:none <?php } ?>"></div> 
				</div> 
			</div> 
            <div class="div-table mnv-transaction transaction-detail" style="width:100%; border-bottom:1px solid #333; ">
				  <?php
					$totalRows = count($rsDetail); 

					for ($i = 0; $i <= $totalRows; $i++) {

						$class =  'transaction-detail-row';
						$overwrite = true;
						$etc = '';
						$optionRows = 'display:none';
	//					$readonly = ($rs[0]['statuskey'] == 1) ? false : true;

						$_POST['chkIsRevision[]'] = 0;
						
						if ($i == $totalRows) {
							$class = 'detail-row-template';
							$overwrite = false;
							$etc = 'disabled="disabled"';
						} else {
							$_POST['hidDetailKey[]'] =  $rsDetail[$i]['pkey'];
							$_POST['hidInvoiceKey[]'] =  $rsDetail[$i]['invoicekey'];
							$_POST['hidTaxInvoiceKey[]'] =  $rsDetail[$i]['taxinvoicekey'];
							$_POST['taxInvoiceNumber[]'] =  $rsDetail[$i]['taxinvoicenumber'];
							$_POST['chkIsRevision[]'] =  $rsDetail[$i]['isrevision'];
							$_POST['chkIsVoid[]'] =  $rsDetail[$i]['isvoid'];
							$_POST['selCurrency[]'] =  $rsDetail[$i]['currencykey'];
							$_POST['invoiceNumber[]'] =  $rsDetail[$i]['invoicecode'];
							$_POST['transactionType[]'] =  $rsDetail[$i]['transactiontype']; 
							$_POST['invoiceDate[]'] =  $obj->formatDBDate($rsDetail[$i]['invoicedate'], 'd / m / Y');
							$_POST['npwp[]'] =  $rsDetail[$i]['npwp'];
							$_POST['customerName[]'] =  $rsDetail[$i]['customername'];
							$_POST['address[]'] =  $rsDetail[$i]['address'];
							$_POST['total[]'] =   $obj->formatNumber($rsDetail[$i]['total']);
							$_POST['beforeTaxTotal[]'] =   $obj->formatNumber($rsDetail[$i]['beforetaxtotal']);
							$_POST['taxValue[]'] =   $obj->formatNumber($rsDetail[$i]['taxvalue']);
							$_POST['selDetailTransactionTypeCodeKey[]'] =   $rsDetail[$i]['transactiontypekey'];
							$_POST['selAdditionalInfo[]'] = $rsDetail[$i]['additionalinfokey'];
							$_POST['selFacilityStamp[]'] = $rsDetail[$i]['facilitystampkey'];
//							$_POST['chkPick[]'] =  1;
						}
					?> 
                    <div class="div-table-row <?php echo $class; ?>">
                        <div class="div-table-col"  style="padding: 0.3em 0">

                            <div class="div-table" style="width:100%">
                                <div class="div-table-row"> 

                                    <div class="div-table-col detail-col-detail">
                                        <?php echo $obj->inputHidden('hidDetailKey[]', array('overwritePost' => $overwrite, 'etc' => $etc)); ?> 
                                        <?php echo $obj->inputHidden('hidInvoiceKey[]', array('overwritePost' => $overwrite, 'etc' => $etc)); ?>
                                        <?php
                                        echo $obj->inputText('invoiceNumber[]', array('overwritePost' => $overwrite, 'allowedStatusForEdit' => array (1)));
                                        ?>
                                    </div>
                                    
									 <div class="div-table-col detail-col-detail" style="text-align:center;width:130px;"> 
                                       <?php echo $obj->inputSelect('selAdditionalInfo[]', $arrAdditionalInfo, array( 'allowedStatusForEdit' => array (1))); ?>
                                    </div>  
									 <div class="div-table-col detail-col-detail" style="text-align:center;width:130px;"> 
                                        <?php echo $obj->inputSelect('selFacilityStamp[]', $arrFacilityStamp, array( 'allowedStatusForEdit' => array (1))); ?>
                                    </div>  

									 <div class="div-table-col detail-col-detail type-filter type-2" style="text-align:center;width:40px;"> 
                                        <?php echo $obj->inputCheckBox('chkIsVoid[]', array('overwritePost' => $overwrite, 'etc' => $etc));  ?>
                                    </div>  
									 <div class="div-table-col detail-col-detail type-filter type-3" style="text-align:center;width:200px;"> 
                                        <?php echo $obj->inputSelect('selDetailTransactionTypeCodeKey[]', $arrTransactionTypeCode, array('overwritePost' => $overwrite, 'allowedStatusForEdit' => array (1),  'etc' => $etc));  ?>
                                    </div>  
									<div class="div-table-col detail-col-detail" style="width:120px;"><?php echo $obj->inputDate('invoiceDate[]', array('overwritePost' => $overwrite, 'etc' => 'style="text-align:center"' . $etc, 'readonly' => true)); ?></div>
                                    <div class="div-table-col detail-col-detail" style="width:80px;"><?php echo $obj->inputSelect('selCurrency', $arrCurrency, array('class' => 'form-control input-currency', 'readonly' => true)); ?></div>
                                    <div class="div-table-col detail-col-detail <?php echo $obj->hideOnDisabled(); ?> icon-col"  style="<?php if (!$allowEdit) {?>display:none <?php } ?>"><?php echo $obj->inputLinkButton('btnDeleteRows', '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button', 'etc' =>  'tabIndex="-1" style="padding:6px 0"')); ?></div>

                                </div> 
                            </div> 
                            <div class="div-table options-row" style="width: 100%">
                                <div class="div-table-row">
                                    <div class="div-table-col detail-col-detail row-header" style="width: 40px">
                                        <?php echo $obj->lang['customer']; ?>
                                    </div> 
                                    <div class="div-table-col detail-col-detail" style="width: 450px">
                                        <?php echo $obj->inputText('customerName[]',array('overwritePost' => $overwrite, 'readonly' => true, 'class' => 'form-control label-style')); ?>
                                    </div> 
                                    <div class="div-table-col detail-col-detail row-header" style="width: 40px">
                                        <?php echo $obj->lang['taxIdentificationNumber']; ?>
                                    </div> 
                                    <div class="div-table-col detail-col-detail" style="width: 400px">
                                        <?php echo $obj->inputText('npwp[]',array('overwritePost' => $overwrite, 'readonly' => true, 'class' => 'form-control label-style')); ?>
                                    </div> 
                                    <div class="div-table-col detail-col-detail row-header" style="width: 40px">
                                        <?php echo $obj->lang['address']; ?>
                                    </div> 
                                    <div class="div-table-col detail-col-detail" style="width: 800px">
                                        <?php echo $obj->inputText('address[]',array('overwritePost' => $overwrite, 'readonly' => true, 'class' => 'form-control label-style')); ?>
                                    </div> 
                                </div>
                            </div>

                        </div>
                    </div>
                <?php } ?> 
            </div>
			
			<div style="clear:both; height:1em;"></div> 
          <?php if ($rs[0]['statuskey']==1 || empty($rs)) { ?> 
            <div style="float:left; display:inline-block;"><?php echo $obj->inputButton('btnAddRows', $obj->lang['addRows'],array('class' => 'btn btn-primary btn-second-tone')); ?></div>
          <?php } ?>   
			
			<div class="form-button-margin"></div>
            <div class="form-button-panel">
                <?php echo $obj->generateSaveButton(array(), true); ?>
            </div>
 
        </form>
        <?php echo $obj->showDataHistory(); ?>
    </div>
</body>

</html>
