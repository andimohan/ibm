<?php
require_once '../_config.php';
require_once '../_include-v2.php';

includeClass(array('MedicalJobOrder.class.php'));
$medicalJobOrder = createObjAndAddToCol(new MedicalJobOrder());
$medicalRequestClaim = createObjAndAddToCol(new MedicalRequestClaim());
$city = createObjAndAddToCol(new City());
$country = createObjAndAddToCol(new Country()); 
$diagnose = createObjAndAddToCol(new Diagnose()); 

$obj = $medicalJobOrder;
$securityObject = $obj->securityObject; 

if (!$security->isAdminLogin($securityObject, 10, true));

$formAction = 'medicalJobOrderList';
$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false;

$finalDiscDecimal = 0;

$editTermOfPaymentInactiveCriteria = '';
$editPaymentMethodInactiveCriteria = '';


$rsPaymentMethodDetail = array();
$finalDiscDecimalType = 'inputnumber';

$_POST['trDate'] = date('d / m / Y');
$_POST['dateOfBirth'] = '01 / 01 / 2010';
$rs = prepareOnLoadData($obj);
$rsDetail = array();
$rsDiagnoseDetail = array();


if (!empty($_GET['id'])) {
	// kalo ad job baru dr case lama, oba kita ambil referensiny tetep dr case aj
	
    $id = $_GET['id'];
    $_POST['hidId'] = $_GET['id'];

    $rsDetail = $obj->getDetailWithRelatedInformation($id); 
    $rsDiagnoseDetail = $obj->getDetailDiagnose($id); 
	$rsMedicalRequestClaim = $medicalRequestClaim->searchData($medicalRequestClaim->tableName.'.pkey', $rs[0]['refkey']);
	$rsRequestFile = $medicalRequestClaim->getFileDetail($rs[0]['refkey']);
		
	$_POST['hidMedicalRequestClaimKey'] = $rs[0]['refkey'];
	
	$_POST['hidCurrentMedicalRequestClaimKey'] =  $rs[0]['refkey'];
	$_POST['hidCustomerKey'] =  $rs[0]['customerkey'];
	$_POST['hidCurrentMedicalRequestClaimCode'] = $rsMedicalRequestClaim[0]['code'];
	
	$_POST['callerName'] = $rsMedicalRequestClaim[0]['callername'];
	$_POST['relationToInsured'] = $rsMedicalRequestClaim[0]['relationtoinsured'];
	$_POST['mobile'] = $rsMedicalRequestClaim[0]['mobile'];
	$_POST['email'] = $rsMedicalRequestClaim[0]['email'];
	
	$_POST['insuredName'] = $rsMedicalRequestClaim[0]['insuredname'];
	$_POST['insuredID'] = $rsMedicalRequestClaim[0]['insuredid'];
	$_POST['policyNumber'] = $rsMedicalRequestClaim[0]['policynumber'];
	$_POST['companyName'] = $rsMedicalRequestClaim[0]['customername'];
	$_POST['categoryName'] = $rsMedicalRequestClaim[0]['categoryname'];
	$_POST['insuranceCompanyName'] = $rsMedicalRequestClaim[0]['insurancecompanyname'];
	$_POST['countryName'] = $rsMedicalRequestClaim[0]['countryname'];
	$_POST['dateOfBirth'] = $obj->formatDBDate($rsMedicalRequestClaim[0]['dateofbirth']);
	$_POST['age'] = $rsMedicalRequestClaim[0]['age'];
	$_POST['insuredMobile'] = $rsMedicalRequestClaim[0]['insuredmobile'];
	$_POST['insuredPhone'] = $rsMedicalRequestClaim[0]['insuredphone'];
	$_POST['insuredEmail'] = $rsMedicalRequestClaim[0]['insuredemail']; 
	$_POST['medicalRequestClaimCode'] = $rsMedicalRequestClaim[0]['code'];
	
    if (!empty($rs[0]['citykey'])) {
        $rsCity = $city->searchData($city->tableName . '.pkey', $rs[0]['citykey'], true);
        $_POST['cityName'] = $rsCity[0]['name'] . ', ' . $rsCity[0]['categoryname'];
    }
 
 
    //update file 
    $rsItemFile = $obj->getFileDetail($id);
 	$obj->prepareLoadedFile($id,array('file' => $rsItemFile ));

}

//$arrDefaultUnit = $class->convertForCombobox($itemUnit->searchData('', '', true, ' and (' . $itemUnit->tableName . '.statuskey = 1 )'), 'pkey', 'name');

$arrStatus = $obj->generateComboboxOpt(array('data' => $obj->getAllStatus(), 'label' => 'status'));
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title></title>
    <script type="text/javascript">
        jQuery(document).ready(function() {

            var tabID = <?php echo ($isQuickAdd) ?  $_GET['tabID'] :  'selectedTab.newPanel[0].id';  ?>;
    		var opt = Array(); 
            opt.fileFolder = "<?php echo $obj->uploadFileFolder; ?>";
            opt.fileUploaderTarget = "item-file-uploader";
            opt.arrFile = Array();

            <?php
            if (isset($id) && !empty($id)) {
                for ($i = 0; $i < count($rsItemFile); $i++) {
                    echo 'opt.arrFile.push("' . $rsItemFile[$i]['file'] . '"); ';
                }
            }
            ?>
 
            var medicalJobOrder = new MedicalJobOrder(tabID, <?php echo json_encode(
                                                                            array(
                                                                                'rs' => $rs,
                                                                                'rsDetail' => $rsDetail,
                                                                                'initialDiagnoseDetail' => $rsDiagnoseDetail
                                                                            )
                                                                        ); ?>, opt);
 
           

            prepareHandler(medicalJobOrder);

            var fieldValidation = {
                code: {
                    validators: {
                        notEmpty: {
                            message: phpErrorMsg.code[1]
                        },
                    }
                },
				medicalRequestClaimCode: {
                    validators: {
                        notEmpty: {
                            message: phpErrorMsg.reference[1]
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
			<?php echo $obj->inputHidden('hidCurrentMedicalRequestClaimKey'); ?>
			<?php echo $obj->inputHidden('hidCustomerKey'); ?>
			<?php echo $obj->inputHidden('hidCurrentMedicalRequestClaimCode'); ?>

            <div class="div-table main-tab-table-2">
                <div class="div-table-row">
                    <div class="div-table-col">
                        <div class="div-tab-panel">
                            <div class="div-table-caption border-orange"><?php echo ucwords($obj->lang['callerInformation']); ?></div>

                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['status']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo  $obj->inputSelect('selStatus', $arrStatus, array('value' => 2)); ?>
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
                                    <?php echo $obj->inputDate('trDate'); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['reference']); ?></label>
                                <div class="col-xs-9">
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
                                                                'statuskey' => 2
                                                            )
                                                        ),
                                                        'allowedStatusForEdit' => array(1),
                                                        'callbackFunction' => 'getTabObj().updateMedicalRequestClaim(event, ui)'
                                                    )
                                                );
                                                ?>
                                    </div> 
                            </div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['callerName']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputText('callerName', array('readonly' => true)); ?> 
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['relationshipToInsured']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputText('relationToInsured', array('readonly' => true)); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['phone']); ?> </label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputText('mobile', array('readonly' => true)); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['email']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputText('email', array('readonly' => true)); ?>
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
                                            $readonly = false;
                                            $disabled = false;

                                            if ($j == $totalRows) {
                                                $class = 'diagnose-row-template row-template';
                                                $overwrite = false;
                                                $disabled = true;
                                                $isLocked = false;
                                            } else {
                                                $_POST['hidInitialDiagnoseDetailKey[]'] =  $rsDiagnoseDetail[$j]['pkey'];
                                                $_POST['hidInitialDiagnoseKey[]'] =  $rsDiagnoseDetail[$j]['initialdiagnosekey'];
                                                $_POST['initialDiagnose[]'] = $rsDiagnoseDetail[$j]['codenameinitialdiagnose'];
                                            }
                                            $hideDeleteIcon = '';
                                        ?>
                                            <div class="div-table-row <?php echo $class; ?>  odd-style-adjustment">
                                                <div class="div-table-col">
                                                    <div class="flex">
                                                        <div class="consume">
                                                            <?php echo $obj->inputHidden('hidInitialDiagnoseDetailKey[]', array('overwritePost' => $overwrite, 'readonly' => $readonly, 'disabled' => $disabled)); ?>
                                                            <?php echo $obj->inputHidden('hidInitialDiagnoseKey[]', array('overwritePost' => $overwrite, 'readonly' => $readonly, 'disabled' => $disabled)); ?>
                                                            <?php echo $obj->inputText('initialDiagnose[]', array('overwritePost' => $overwrite, 'readonly' => $readonly, 'disabled' => $disabled)); ?>
                                                        </div>
                                                        <div class="icon-col <?php echo $obj->hideOnDisabled(); ?>"><?php echo $obj->inputLinkButton('btnAddDetailRow', '<i class="fas fa-plus-circle"></i>', array('class' => 'btn btn-link add-row-button', 'etc' => 'attr-template="diagnose-row-template"')); ?></div>
                                                        <div class="icon-col <?php echo $obj->hideOnDisabled(); ?>"><?php echo $obj->inputLinkButton('btnDeleteRows', '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button', 'etc' =>  'tabIndex="-1" style="padding:6px 0; ' . $hideDeleteIcon . '"')); ?></div>

                                                    </div>
                                                </div>
                                            </div>
                                        <?php }     ?>

                                    </div>
                                </div>
                            </div>
 							<div style="clear:both; height: 1em"></div>							
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['address']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputTextArea('address', array('etc' => 'style="height:8em;"')); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['phone']); ?> </label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputText('casePhone'); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['city']); ?></label>
                                <div class="col-xs-9">
                                    <?php
                                    echo $obj->inputAutoComplete(
                                        array(
                                            'objRefer' => $city,
                                            'revalidateField' => false,
                                            'element' => array(
                                                'value' => 'cityName',
                                                'key' => 'hidCityKey'
                                            ),
                                            'source' => array(
                                                'url' => 'ajax-city.php',
                                                'data' => array('action' => 'searchData')
                                            )
                                        )
                                    );
                                    ?>
                                </div>
                            </div> 
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['note']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputTextArea('trDesc', array('etc' => 'style="height:8em;"')); ?>
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
                                    <?php echo  $obj->inputText('policyNumber', array('readonly' => true)); ?> 
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
                        <div class="div-tab-panel">
                            <div class="div-table" style="width:100%">
                                <div class="div-table-caption border-purple"><?php echo ucwords($obj->lang['file']); ?></div>  
								<?php if (count($rsRequestFile) > 0) { ?>
                                <div class="div-table-row">
                                    <div class="div-table-col-5" style="padding-bottom:0"> 
                                        <div class="file-uploader">
											<ul class="file-list"> 
											 <?php for($i=0;$i<count($rsRequestFile);$i++) {
												echo '<li><div class="panel"><div class="file-uploader-description"><a href="/download.php?filename=request-claim-file/'.$rsRequestFile[$i]['refkey'].'/'.$rsRequestFile[$i]['file'].'" target="_blank" title="'.$rsRequestFile[$i]['file'].'">'.$rsRequestFile[$i]['file'].'</a></div></div></li>';
											} ?>     
											</ul>
										</div> 
									</div>
								</div>
								<?php } ?>
                                <div class="div-table-row">
                                    <div class="div-table-col-5"> 
                                        <!-- file uploader -->
                                        <div class="item-file-uploader">
                                            <ul class="file-list"></ul>
                                            <div style="clear:both; height:1em;"></div>
                                            <div class="file-uploader">
                                                <noscript>
                                                    <p>Please enable JavaScript to use file uploader.</p>
                                                </noscript>
                                            </div>
                                        </div>
                                        <!-- file uploader -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <!-- Form Detail -->
            <div style="clear:both; height:2em;"></div>
            <div class="div-table transaction-detail" style="width:100%; ">
               <div class="div-table-row">
                    <div class="div-table-col detail-col-header"><?php echo ucwords($obj->lang['service']); ?></div>
                    <div class="div-table-col detail-col-header" style="width:80px; text-align:right;"><?php echo ucwords($obj->lang['amount']); ?></div>
				   	<?php if (!empty($_GET['id'])) {
						if (($rs[0]['statuskey'] == 2) || ($rs[0]['statuskey'] == 3)) { ?>
                            <div class="div-table-col detail-col-header" style="width:40px;"></div>
                    <?php
                        }
                    }
                    ?> 
                    <div class="div-table-col detail-col-header" style="width:150px; text-align:right;"><?php echo ucwords($obj->lang['price']); ?></div>
                    <div class="div-table-col detail-col-header" style="width:150px; text-align:right;"> <?php echo ucwords($obj->lang['subtotal']); ?></div>
                    <div class="div-table-col detail-col-header icon-col <?php echo $obj->hideOnDisabled(); ?>"></div>
                </div>
            </div>
            <div class="div-table mnv-transaction service-detail transaction-detail" style="width:100%; border-bottom:1px solid #333; ">
                <div class="div-table-row" style="display:none">
                    <div class="div-table-col"></div>
                    <div class="div-table-col"></div>
                    <div class="div-table-col"></div>
                </div>

                <?php 
                $totalRows = count($rsDetail);
                for ($i = 0; $i <= $totalRows; $i++) {

                    $class =  'transaction-detail-row';
                    $overwrite = true;
                    $etc = '';
                    //$arrUnit = $arrDefaultUnit;
					$statusName =  '';
					$statusStyle =  ''; //bg-blue-munsell
					$quotationLink = '';
					$readonly = false;

                    if ($i == $totalRows) {
                        $class = 'detail-row-template';
                        $overwrite = false;
                        $etc = 'disabled="disabled"';
                    } else {
                        $decimal = 0;
                        $inputnumber = 'inputnumber';
                        
						if($rsDetail[$i]['isquotation']){
							if($rsDetail[$i]['statuskey'] == 2){
								$statusName = $obj->lang['approved'];
                                $readonly = true;
								$statusStyle = 'bg-green-avocado';
								$quotationLink = '<div><a href="/admin/print/medicalSalesOrderQuotation/'.$rsDetail[$i]['refquotationkey'].'" target="_blank">'.strtolower($obj->lang['showQuotation']).'</a></div>';
							}else{ 
								$statusName = $obj->lang['needQuotation'];
								$statusStyle = 'bg-red-cardinal';
							}
						}

                        $_POST['hidDetailKey[]'] =  $rsDetail[$i]['pkey'];
                        $_POST['hidItemKey[]'] =  $rsDetail[$i]['itemkey'];
                        $_POST['itemName[]'] =  $rsDetail[$i]['itemname'];
                        $_POST['hidStatusKey[]'] =  $rsDetail[$i]['statuskey'];
                        $_POST['qty[]'] =   $obj->formatNumber($rsDetail[$i]['qty']);
                        $_POST['qtyInvoiced[]'] =   $obj->formatNumber($rsDetail[$i]['qtyinvoiced']);
                        $_POST['priceInUnit[]'] =   $obj->formatNumber($rsDetail[$i]['priceinunit']);
                        //$_POST['selUnit[]'] =  $rsDetail[$i]['unitkey'];
                        $_POST['detailSubtotal[]'] =   $obj->formatNumber($rsDetail[$i]['total']);
                        $_POST['detailDescription[]'] =  $rsDetail[$i]['trdesc'];
                        $_POST['hidQuotationKey[]'] =  $rsDetail[$i]['refquotationkey'];
                        //$arrUnit = $obj->convertForCombobox($item->getAvailableUnit($rsDetail[$i]['itemkey']), 'conversionunitkey', 'unitname');
 
                    }
                ?>

                    <div class="div-table-row <?php echo $class; ?>">
                        <div class="div-table-col detail-col-detail" style="padding:0">
                            <div class="div-table" style="width:100%">
                                <div class="div-table-row">
                                    <div class="div-table-col detail-col-detail">
                                        <?php echo $obj->inputText('itemName[]', array('overwritePost' => $overwrite, 'etc' => $etc, 'readonly' => $readonly)); ?> 
										<?php echo $obj->inputHidden('hidItemKey[]', array('overwritePost' => $overwrite, 'etc' => $etc)); ?> 
										<?php echo $obj->inputHidden('hidDetailKey[]', array('overwritePost' => $overwrite, 'etc' => $etc)); ?>
										<?php echo $obj->inputHidden('hidStatusKey[]', array('overwritePost' => $overwrite, 'etc' => $etc)); ?> 
										<?php echo $obj->inputHidden('hidQuotationKey[]', array('overwritePost' => $overwrite, 'etc' => $etc)); ?> 
                                    </div>
                                    <div class="div-table-col detail-col-detail" style="width:80px;">
                                        <?php echo $obj->inputNumber('qty[]', array('overwritePost' => $overwrite, 'etc' => 'style="text-align:right;" ' . $etc)); ?>
                                    </div>
                                    <?php if (!empty($_GET['id'])) {
                                        if (($rs[0]['statuskey'] == 2) || ($rs[0]['statuskey'] == 3)) {
                                    ?>
                                            <div class="div-table-col detail-col-detail text-muted" style="width:40px;">
                                                / <span style="text-align:right; width: 35px">
                                                    <?php echo $obj->formatNumber($rsDetail[$i]['qtyinvoiced']); ?></span>
                                            </div>
                                    <?php
                                        }
                                    }
                                    ?> 
                                    <div class="div-table-col detail-col-detail" style="width:150px;">
                                        <?php echo $obj->inputNumber('priceInUnit[]', array('overwritePost' => $overwrite, 'etc' => 'style="text-align:right;" ' . $etc)); ?>
                                    </div>
                                    <div class="div-table-col detail-col-detail" style="width:150px;">
                                        <?php echo $obj->inputNumber('detailSubtotal[]', array('overwritePost' => $overwrite, 'readonly' => true, 'etc' => 'style="text-align:right;" ' . $etc)); ?>
                                    </div>
                                </div>

                            </div>
                            <div class="div-table" style="width:100%;">
                                <div class="div-table-row">
                                    <div class="div-table-col detail-col-detail">
                                        <?php echo $obj->inputTextArea('detailDescription[]', array('overwritePost' => $overwrite, 'etc' => 'style="height:8em" placeholder="' . $obj->lang['description'] . '"')); ?>
                                    </div>
                                    <div class="div-table-col detail-col-detail status-label " style="width:130px; text-align: center">
									 	<label class=" <?php echo $statusStyle; ?>"><?php echo $statusName; ?></label>
										<?php echo $quotationLink; ?>
									</div>
                                </div>
                            </div>
                        </div>
                        <div class="div-table-col detail-col-detail icon-col align-top-adjust  <?php echo $obj->hideOnDisabled(); ?>">
                            <?php echo $obj->inputLinkButton('btnDeleteRows', '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button', 'etc' => 'tabIndex="- 1"')); ?>
                        </div>
                        <!--onClick="itemAdj.calculateTotal()"-->
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
									<div class="div-table-col-5" style="text-align:right;">
										<?php echo ucwords($obj->lang['total']); ?>
									</div>
									<div class="div-table-col-5" style="width:150px;">
										<?php echo $obj->inputNumber('subtotal', array('readonly' => true, 'etc' => 'style="text-align:right;"')); ?>
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
				<?php
				echo $obj->generateSaveButton(array(), true);
				?>
			</div>
        </form>

        <?php echo $obj->showDataHistory(); ?>
    </div>
</body>

</html>
