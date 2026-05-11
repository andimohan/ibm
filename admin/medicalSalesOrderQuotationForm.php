<?php
require_once '../_config.php';
require_once '../_include-v2.php';

includeClass('MedicalSalesOrderQuotation.class.php');
$medicalSalesOrderQuotation = createObjAndAddToCol(new MedicalSalesOrderQuotation());

$itemUnit = createObjAndAddToCol(new ItemUnit());
$item = createObjAndAddToCol(new Item());
$medicalJobOrder = createObjAndAddToCol(new MedicalJobOrder());
$medicalRequestClaim = createObjAndAddToCol(new MedicalRequestClaim()); 
$warehouse = createObjAndAddToCol(new Warehouse());

$obj = $medicalSalesOrderQuotation;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
// some modules that have different security object from that of their class

if (!$security->isAdminLogin($securityObject, 10, true));

$formAction = 'medicalSalesOrderQuotationList';

$parentFileName = $_GET['fileName'];
$parentPanelId = $_GET['selectedPanelId'];
$parentTitle = $_GET['title'];

$editWarehouseInactiveCriteria = '';

$_POST['trDate'] = date('d / m / Y');

$finalDiscDecimal = 0;
$finalDiscDecimalType = 'inputnumber';

$rs = prepareOnLoadData($obj);
$rsDetail = array();

$rsMedicalRequestClaim = $obj->getTableKeyAndObj($medicalRequestClaim->tableName, array('key'));
$rsMedicalJobOrder = $obj->getTableKeyAndObj($medicalJobOrder->tableName, array('key'));

$arrType = array();
$arrType[$rsMedicalRequestClaim['key']] = $obj->lang['newRequest'];
$arrType[$rsMedicalJobOrder['key']] = $obj->lang['jobOrder'];

$rsDiagnoseDetail = array();

$rsKey = $obj->getTableKeyAndObj($obj->tableName, array('key'));
if (!empty($_GET['id'])) {
    $id = $_GET['id'];
    $_POST['hidId'] = $_GET['id'];

    $rsDetail = $obj->getDetailWithRelatedInformation($id);
    $rsMedicalJobOrderType = $medicalJobOrder->getTableKeyAndObj($medicalJobOrder->tableName, array('key'));
	
    if ($rs[0]['reftabletype'] == $rsMedicalJobOrderType['key']) {
        $rsJob = $medicalJobOrder->searchData($medicalJobOrder->tableName . '.pkey', $rs[0]['refkey']);
        $rsDiagnoseDetail = $medicalJobOrder->getDetailDiagnose($rs[0]['refkey']);
    } else {
        $rsJob = $medicalRequestClaim->searchData($medicalRequestClaim->tableName . '.pkey', $rs[0]['refrequestkey']);
        $rsDiagnoseDetail = $medicalRequestClaim->getDetailDiagnose($rs[0]['refrequestkey']);
       
    }
	
	$_POST['policyNumber'] = $rsJob[0]['policynumber'];
	$_POST['categoryName'] = $rsJob[0]['categoryname']; 
	$_POST['insuredName'] = $rsJob[0]['insuredname'];
	$_POST['companyName'] = $rsJob[0]['customername'];
	$_POST['insuranceCompanyName'] = $rsJob[0]['insurancecompanyname'];
	
	$_POST['insuredID'] = $rsJob[0]['insuredid'];
	$_POST['countryName'] = $rsJob[0]['countryname'];
	$_POST['dateOfBirth'] = $obj->formatDBDate($rsJob[0]['dateofbirth']);
	$_POST['age'] = $rsJob[0]['age'];
	$_POST['insuredPhone'] = $rsJob[0]['insuredphone'];
	$_POST['insuredMobile'] = $rsJob[0]['insuredmobile'];
	$_POST['insuredEmail'] = $rsJob[0]['insuredemail'];
	
	$_POST['medicalRequestClaimCode'] = $rsJob[0]['code'];
	$_POST['codeLog'] = $rsJob[0]['codelog'];
	
	$_POST['caseAddress'] = $rsJob[0]['address'];
	$_POST['casePhone'] = $rsJob[0]['casephone'];
	$_POST['caseCityName'] = $rsJob[0]['cityandcategoryname'];
	$_POST['caseDesc'] = $rsJob[0]['trdesc'];
 
 
    //ini sementara gk diupdate, akan di unset
    // hanya utk validasi di validate form

    $_POST['finalDiscount'] = $obj->formatNumber($rs[0]['finaldiscount'], $finalDiscDecimal);
    $_POST['taxPercentage'] = $obj->formatNumber($rs[0]['taxpercentage'], 2);
   
    $editWarehouseInactiveCriteria = ' or  ' . $warehouse->tableName . '.pkey = ' . $obj->oDbCon->paramString($rs[0]['warehousekey']);

}


$arrStatus = $obj->generateComboboxOpt(array('data' => $obj->getAllStatus(), 'label' => 'status'));
$arrWarehouse = $warehouse->generateComboboxOpt(null, array('criteria' => ' and (' . $warehouse->tableName . '.statuskey = 1' . $editWarehouseInactiveCriteria . ')'));
$arrDefaultUnit = $itemUnit->generateComboboxOpt(null, array('criteria' => ' and (' . $itemUnit->tableName . '.statuskey = 1 )'));

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title></title>

    <script type="text/javascript">
        jQuery(document).ready(function() {
            var tabID = selectedTab.newPanel[0].id;
			 
            var varConstant = {
                JOBTYPE: {'job': <?php echo $rsMedicalJobOrder['key']; ?>,
						  'request':<?php echo $rsMedicalRequestClaim['key']; ?>
						 }
            };

			var opt = Array(); 
			opt.constant = varConstant;
            opt.fileUploaderTarget = "item-file-uploader";
            opt.arrFile = Array();

            var medicalSalesOrderQuotation = new MedicalSalesOrderQuotation(tabID, <?php echo json_encode(
                                                                            array(
                                                                                'rs' => $rs, 
                                                                                'rsDetail' => $rsDetail,
                                                                                'initialDiagnoseDetail' => $rsDiagnoseDetail
                                                                            )
                                                                        ); ?>, opt);
            prepareHandler(medicalSalesOrderQuotation);
                                                                        
            var fieldValidation = {
                medicalJobOrderCode: {
                    validators: {
                        notEmpty: {
                            message: phpErrorMsg.code[1]
                        },
                    }
                }, 

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
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['code'].' / '.$obj->lang['log']); ?></label>
                                <div class="col-xs-9">
									<div class="flex">
										<div class="consume"> <?php echo $obj->inputAutoCode('code'); ?></div>
										<div>/</div>
										<div class="consume"> <?php echo $obj->inputText('codeLog', array('readonly' => true)); ?></div>
									</div> 
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['date']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputDateTime('trDate'); ?>
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
                                    <div class="flex">
                                        <div><?php echo $obj->inputSelect('selJOType', $arrType); ?> </div>
                                        <div class="consume">
                                            <div class="isrequest" style="margin-right:0">
                                                <?php
                                                echo $obj->inputAutoComplete(
                                                    array( 
                                                        'element' => array(
                                                            'value' => 'medicalRequestClaimCode',
                                                            'key' => 'hidMedicalRequestClaimKey'
                                                        ),
                                                        'source' => array(
                                                            'url' => 'ajax-medical-request-claim.php',
                                                            'data' => array(
                                                                'action' => 'searchData',
                                                                'statuskey' => '1'
                                                            )
                                                        ), 
                                                        'callbackFunction' => 'getTabObj().updateMedicalRequestClaim()'
                                                    )
                                                );
                                                ?>
                                            </div>
                                            <div class="isjob">
                                                <?php
                                                echo $obj->inputAutoComplete(
                                                    array(
                                                        'revalidateField' => false,
                                                        'element' => array(
                                                            'value' => 'medicalJobOrderCode',
                                                            'key' => 'hidMedicalJobOrderkey'
                                                        ),
                                                        'source' => array(
                                                            'url' => 'ajax-medical-job-order.php',
                                                            'data' => array(
                                                                'action' => 'searchData',
                                                                'statuskey' => '(1,2)'
                                                            )
                                                        ),
                                                        'callbackFunction' => 'getTabObj().updateMedicalJobOrder()'
                                                    )
                                                );
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div> 
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['attention']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo  $obj->inputText('attention'); ?>
                                </div>
                            </div>  
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['note']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo  $obj->inputTextArea('trDesc', array('etc' => 'style="height:10em;"')); ?>
                                </div>
                            </div> 
                        </div> 

                        <div class="div-tab-panel">
                            <div class="div-table-caption border-green"><?php echo ucwords($obj->lang['caseInformation']); ?></div>
							 <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['diagnose']); ?></label>
                                <div class="col-xs-9">
                                    <div class="div-table mnv-transaction diagnose-detail transaction-detail" style="width:100%">
                                        <?php
                                        $totalRows = count($rsDiagnoseDetail);
                                        for ($j = 0; $j <= $totalRows; $j++) { 
 
                                            $class =  'transaction-detail-row';
                                            $overwrite = true;
                                            $readonly = true;
											$disabled = false;
											
											if ($j == $totalRows) {
                                                $class = 'diagnose-row-template row-template';
												$overwrite = false;
												$etc = 'disabled="disabled"';
											} else {
												$_POST['initialDiagnose[]'] = $rsDiagnoseDetail[$j]['codenameinitialdiagnose']; 
											}
 
                                        ?>
                                            <div class="div-table-row <?php echo $class; ?>  odd-style-adjustment">
                                                <div class="div-table-col"> 
                                                        <div class="consume">
                                                            <?php echo $obj->inputText('initialDiagnose[]', array('overwritePost' => $overwrite ,'readonly' => $readonly, 'disabled' => $disabled )); ?>
                                                        </div> 
                                                </div>
                                            </div>
                                        <?php }  ?>

                                    </div>
                                </div>
                            </div>
							
 							<div style="clear:both; height: 1em"></div>		
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['address']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputTextArea('caseAddress', array('etc' => 'style="height:8em;"', 'readonly' => true)); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['phone']); ?> </label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputText('casePhone', array('readonly' => true)); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['city']); ?></label>
                                <div class="col-xs-9">
                                      <?php echo $obj->inputText('caseCityName', array('readonly' => true)); ?>
                                </div>
                            </div>  
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['note']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputTextArea('caseDesc', array('etc' => 'style="height:8em;"', 'readonly' => true)); ?>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="div-table-col">
                        <div class="div-tab-panel">
                            <div class="div-table-caption border-blue"><?php echo ucwords($obj->lang['insuredInformation']); ?></div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['policyNumber']); ?></label>
                                <div class="col-xs-9">  
                                     <?php echo  $obj->inputText('policyNumber', array('readonly'=>true)); ?>
                                </div>
                            </div>
                             <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['category']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo  $obj->inputText('categoryName', array('readonly' => true)); ?> 
                                </div>
                            </div>
							
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['insuredName'] .' / '.$obj->lang['company']); ?></label>
                                <div class="col-xs-9">
									<div class="flex"> 
										<div class="consume"><?php echo  $obj->inputText('insuredName', array('readonly' => true)); ?></div>
										<div>/</div>
										<div class="consume"><?php echo  $obj->inputText('companyName', array('readonly' => true)); ?></div>
									</div> 
                                </div>
                            </div> 
                          <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['insuranceCompany']); ?></label>
                                <div class="col-xs-9">
									<?php echo  $obj->inputText('insuranceCompanyName', array('readonly' => true)); ?>
                                </div>
                            </div> 
							<div style="clear:both; height:1em;"></div>
							
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['IDNumber']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo  $obj->inputText('insuredID', array('readonly' => true)); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['country']); ?></label>
                                <div class="col-xs-9"> 
                                    <?php echo $obj->inputText('countryName', array('readonly' => true)); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['dateOfBirth']); ?> / <?php echo ucwords($obj->lang['age']); ?></label>
                                <div class="col-xs-9">
									<div class="flex">
										<div class="consume"><?php echo $obj->inputDate('dateOfBirth', array('readonly' => true,  'etc' => 'style="text-align:center;"')); ?></div>
										<div>/</div>
										<div><?php echo $obj->inputNumber('age', array('readonly' => true, 'etc' => 'style="text-align:center; width:6em"')); ?></div>
										<div><?php echo $obj->lang['year']; ?></div>
									</div>
                                </div>  
                            </div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['phone']); ?> / <?php echo ucwords($obj->lang['mobilePhone']); ?></label>
                                <div class="col-xs-9">
                                    <div class="flex">
										<div class="consume"><?php echo $obj->inputText('insuredPhone', array('readonly' => true)); ?></div>
										<div>/</div>
										<div class="consume"> <?php echo $obj->inputText('insuredMobile', array('readonly' => true)); ?></div>
									</div> 
                                </div> 
                            </div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['email']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputText('insuredEmail', array('readonly' => true)); ?>
                                </div>
                            </div>


                        </div>
                    </div>
                </div>
            </div>


            <div class="div-table transaction-detail" style="width:100%; "> 
                <div class="div-table-row">
                    <div class="div-table-col detail-col-header"><?php echo ucwords($obj->lang['service']); ?></div>
                    <div class="div-table-col detail-col-header" style="width:80px; text-align:right;"><?php echo ucwords($obj->lang['amount']); ?></div>
                    <div class="div-table-col detail-col-header" style="width:150px; text-align:right;"><?php echo ucwords($obj->lang['price']); ?></div>
                    <div class="div-table-col detail-col-header" style="width:200px; text-align:right;"><?php echo ucwords($obj->lang['subtotal']); ?></div>
                    <div class="div-table-col detail-col-header icon-col <?php echo $obj->hideOnDisabled(); ?>"></div>
                </div>
            </div>
            <div class="div-table mnv-transaction service-detail transaction-detail" style="width:100%; border-bottom:1px solid #333; ">
             
                <?php
                $totalRows = count($rsDetail);
                for ($i = 0; $i <= $totalRows; $i++) {

                    $class =  'transaction-detail-row';
                    $overwrite = true;
                    $etc = '';
                    $arrUnit = $arrDefaultUnit;

                    if ($i == $totalRows) {
                        $class = 'detail-row-template';
                        $overwrite = false;
                        $etc = 'disabled="disabled"';
                    } else {
                        $decimal = 0;
                        $inputnumber = 'inputnumber';

                        $_POST['hidDetailKey[]'] =  $rsDetail[$i]['pkey'];
                        $_POST['hidItemKey[]'] =  $rsDetail[$i]['itemkey'];
                        $_POST['itemName[]'] =  $rsDetail[$i]['itemname'];
                        $_POST['qty[]'] =   $obj->formatNumber($rsDetail[$i]['qty']);
                        $_POST['priceInUnit[]'] =   $obj->formatNumber($rsDetail[$i]['priceinunit']);
                        $_POST['selUnit[]'] =  $rsDetail[$i]['unitkey'];
                        $_POST['detailDescription[]'] =  $rsDetail[$i]['trdesc'];
                        $_POST['detailSubtotal[]'] =   $obj->formatNumber($rsDetail[$i]['total']);
                        $arrUnit = $obj->convertForCombobox($item->getAvailableUnit($rsDetail[$i]['itemkey']), 'conversionunitkey', 'unitname');
                    }
                ?>

                    <div class="div-table-row <?php echo $class; ?>">
                        <div class="div-table-col detail-col-detail" style="padding:0">
                            <div class="div-table" style="width:100%">
                                <div class="div-table-row">
                                    <div class="div-table-col detail-col-detail">
                                        <?php echo $obj->inputText('itemName[]', array('overwritePost' => $overwrite, 'etc' => $etc)); ?><?php echo $obj->inputHidden('hidItemKey[]', array('overwritePost' => $overwrite, 'etc' => $etc)); ?><?php echo $obj->inputHidden('hidDetailKey[]', array('overwritePost' => $overwrite, 'etc' => $etc)); ?>
                                    </div>
                                    <div class="div-table-col detail-col-detail" style="width:80px;">
                                        <?php echo $obj->inputNumber('qty[]', array('overwritePost' => $overwrite, 'etc' => 'style="text-align:right;" ' . $etc)); ?>
                                    </div>
                                    <div class="div-table-col detail-col-detail" style="width:80px; display:none">
                                        <?php echo $obj->inputSelect('selUnit[]', $arrUnit, array('overwritePost' => $overwrite, 'etc' =>  $etc)); ?>
                                    </div>
                                    <div class="div-table-col detail-col-detail" style="width:150px;">
                                        <?php echo $obj->inputNumber('priceInUnit[]', array('overwritePost' => $overwrite, 'etc' => 'style="text-align:right;" ' . $etc)); ?>
                                    </div>
                                    <div class="div-table-col detail-col-detail" style="width:200px;">
                                        <?php echo $obj->inputNumber('detailSubtotal[]', array('overwritePost' => $overwrite, 'readonly' => true, 'etc' => 'style="text-align:right;" ' . $etc)); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="div-table" style="width:100%;">
                                <div class="div-table-row">
                                    <div class="div-table-col detail-col-detail">
                                        <?php echo $obj->inputTextArea('detailDescription[]', array('overwritePost' => $overwrite, 'etc' => 'style="height:10em" placeholder="' . $obj->lang['note'] . '"')); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="div-table-col detail-col-detail icon-col align-top-adjust  <?php echo $obj->hideOnDisabled(); ?>">
                            <?php echo $obj->inputLinkButton('btnDeleteRows', '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button', 'etc' => 'tabIndex="- 1"')); ?>
                        </div> 
                    </div>
                <?php }   ?>

            </div>

            <div style="clear:both; height:1em;"></div>
            <div style="float:left; display:inline-block;"><?php echo $obj->inputButton('btnAddRows', $obj->lang['addRows'], array('class' => 'btn btn-primary btn-second-tone')); ?></div>

            <div class="div-table" style="float:right">
				<div class="div-table-row">
					<div class="div-table-col">

						<div class="div-table" style="float:right;">
							<div class="div-table-row  form-group">
								<div class="div-table-col-3" style="text-align:right;">
									<?php echo ucwords($obj->lang['subtotal']); ?>
								</div>
								<div class="div-table-col-3" style="width:200px;">
									<?php echo $obj->inputNumber('subtotal', array('readonly' => true, 'etc' => 'style="text-align:right;"')); ?>
								</div>

							</div>
							<div class="div-table-row  form-group">
								<div class="div-table-col-3" style="text-align:right;">
									<?php echo ucwords($obj->lang['discount']); ?>
								</div>
								<div class="div-table-col-3">
									<div class="flex">
										<div><?php echo $obj->inputSelect('selFinalDiscountType', $obj->arrDiscountType); ?> </div>
										<div class="consume"> <?php echo $obj->inputNumber('finalDiscount', array('class' => 'form-control ' . $finalDiscDecimalType, 'etc' => 'style="text-align:right;"')); ?> </div>
									</div>
								</div>
							</div>
							<div class="div-table-row  form-group   form-detail-field">
								<div class="div-table-col-3" style="text-align:right; padding-top:2em;">
									<?php echo ucwords($obj->lang['beforeTax']); ?>
								</div>
								<div class="div-table-col-3" style="padding-top:2em;">
									<?php echo $obj->inputNumber('beforeTaxTotal', array('readonly' => true, 'etc' => 'style="text-align:right;"')); ?>
								</div>

							</div>

							<div class="div-table-row  form-group">
								<div class="div-table-col-3" style="text-align:right;">
									<?php echo strtoupper($obj->lang['PPN']); ?> [Include]
								</div>
								<div class="div-table-col-3">
									<div class="flex">
										<div><?php echo $obj->inputCheckBox('chkIncludeTax'); ?></div>
										<div class="percentage-col"><?php echo $obj->inputDecimal('taxPercentage', array('etc' => 'style="text-align:right;"')); ?></div>
										<div>%</div>
										<div class="consume"><?php echo $obj->inputNumber('taxValue', array('readonly' => true, 'etc' => 'style="text-align:right;"')); ?></div>
									</div>
								</div>
							</div>

							<div class="div-table-row  form-group   form-detail-field">
								<div class="div-table-col-3" style="text-align:right;">
									<?php echo ucwords($obj->lang['others']); ?>
								</div>
								<div class="div-table-col-3">
									<?php echo $obj->inputNumber('etcCost', array('etc' => 'style="text-align:right;"')); ?>
								</div>
							</div>
							<div class="div-table-row  form-group">
								<div class="div-table-col-3" style="text-align:right;">
									<?php echo ucwords($obj->lang['total']); ?>
								</div>
								<div class="div-table-col-3">
									<?php echo $obj->inputNumber('total', array('readonly' => true, 'etc' => 'style="text-align:right;"')); ?>
								</div>
							</div>
							<div class="div-table-row  form-group">
								<div class="div-table-col-3" style="text-align:right;"> </div>
								<div class="div-table-col-3">
									<div class="form-detail-button" style="float:right; text-align:right;" relalt="<?php echo ucwords($obj->lang['hideDetail']); ?> "><?php echo ucwords($obj->lang['showDetail']); ?> </div>
								</div>
							</div>
						</div>
					</div>
					 <div class="div-table-col icon-col <?php echo $obj->hideOnDisabled(); ?>"></div>
				</div> 
            </div>
 
			<div style="clear:both"></div>  
            <div class="form-button-margin"></div>
            <div class="form-button-panel">
                <?php echo $obj->generateSaveButton(array(), true); ?> 
            </div>

        </form>

        <?php echo $obj->showDataHistory(); ?>
    </div>
</body>

</html>
