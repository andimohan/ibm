<?php
// gk pake kode transaksi, biar gk bingung kebykan kode

require_once '../_config.php';
require_once '../_include-v2.php';

includeClass(array('MedicalRequestClaim.class.php'));
$medicalRequestClaim = createObjAndAddToCol(new MedicalRequestClaim()); 
$city = createObjAndAddToCol(new City()); 
$customerInsurancePolicy = createObjAndAddToCol(new CustomerInsurancePolicy());
$diagnose = createObjAndAddToCol(new Diagnose());

$obj = $medicalRequestClaim;
$securityObject = $obj->securityObject;

if (!$security->isAdminLogin($securityObject, 10, true));

$formAction = 'medicalRequestClaimList';
$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false;

$_POST['trDate'] = date('d / m / Y');
$_POST['dateOfBirth'] = '01 / 01 / 2010';
$rs = prepareOnLoadData($obj);
$rsDetail = array();
$rsDiagnoseDetail = array();

if (!empty($_GET['id'])) {
    $id = $_GET['id'];
	
    $rsDetail = $obj->getDetailWithRelatedInformation($id);
    $rsDiagnoseDetail = $obj->getDetailDiagnose($id); 
	$rsCustomerInsurancePolicy = $customerInsurancePolicy->getDataRowById($rs[0]['customerinsurancepolicykey']);
	
	  
	$_POST['age'] = ($rs[0]['statuskey'] == 1) ? $customerInsurancePolicy->calculateAge($rsCustomerInsurancePolicy[0]['dateofbirth']) : $rs[0]['age'];
	
	$_POST['insuredName'] = $rsCustomerInsurancePolicy[0]['name']; 
	$_POST['policyNumber'] = $rsCustomerInsurancePolicy[0]['policynumber']; 
	
	// utk history
	// drpd query satu2 class
	$rsHeaderInformation = $obj->searchData($obj->tableName.'.pkey',$id);
	$_POST['countryName'] = $rsHeaderInformation[0]['countryname'];
	$_POST['categoryName'] = $rsHeaderInformation[0]['categoryname'];
	$_POST['companyName'] = $rsHeaderInformation[0]['customername'];
	$_POST['insuranceCompanyName'] = $rsHeaderInformation[0]['insurancecompanyname'];
	 
	
    $_POST['hidCityKey'] = $rs[0]['citykey'];
    if (!empty($rs[0]['citykey'])) {
        $rsCity = $city->searchData('city.pkey', $rs[0]['citykey'], true);
        $_POST['cityName'] = $rsCity[0]['name'] . ', ' . $rsCity[0]['categoryname'];
    }
 
    //update file 
    $rsItemFile = $obj->getFileDetail($id);
 	$obj->prepareLoadedFile($id,array('file' => $rsItemFile ));
	 
}

$rsMedicalRequestClaimType = $obj->getTableKeyAndObj($obj->tableName, array('key'));
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
 
            var medicalRequestClaim = new MedicalRequestClaim(tabID, <?php echo json_encode(
                                                                            array(
                                                                                'rs' => $rs,
                                                                                'rsDetail' => $rsDetail,
                                                                                'initialDiagnoseDetail' => $rsDiagnoseDetail
                                                                            )
                                                                        ); ?>, opt);

            prepareHandler(medicalRequestClaim);

            var fieldValidation = {
                code: {
                    validators: {
                        notEmpty: {
                            message: phpErrorMsg.code[1]
                        },
                    }
                },
                callerName: {
                    validators: {
                        notEmpty: {
                            message: phpErrorMsg.customer[1]
                        },
                    }
                },
                policyNumber: {
                    validators: {
                        notEmpty: {
                            message: phpErrorMsg.policyNumber[1]
                        }
                    }
                },
               
                mobile: {
                    validators: {
                        notEmpty: {
                            message: phpErrorMsg.phone[1]
                        }
                    }
                },
            };


            setFormValidation(getTabObj(tabID), $('#defaultForm-' + tabID), fieldValidation, <?php echo json_encode($obj->validationFormSubmitParam()); ?>);

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
                            <div class="div-table-caption border-orange"><?php echo ucwords($obj->lang['callerInformation']); ?></div>

                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['status']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo  $obj->inputSelect('selStatus', $arrStatus, array('value' => 2)); ?>
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
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['name']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputText('callerName'); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['relationshipToInsured']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputText('relationToInsured'); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['mobilePhone']); ?> </label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputText('mobile'); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['email']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputText('email'); ?>
                                </div>
                            </div>
                        </div>

                        <div class="div-tab-panel">
                            <div class="div-table-caption border-green"><?php echo ucwords($obj->lang['caseInformation']); ?></div>
							
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['diagnose']); ?></label>
                                <div class="col-xs-9">
                                    <div class="div-table mnv-transaction transaction-detail" style="width:100%">
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
                                        <?php } ?>

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
                                    <?php
                                    echo $obj->inputAutoComplete(
                                        array( 
                                            'element' => array(
                                                'value' => 'policyNumber',
                                                'key' => 'hidCustomerInsurancePolicyKey'
                                            ),
                                            'source' => array(
                                                'url' => 'ajax-customer-insurance-policy.php',
                                                'data' => array(
                                                    'action' => 'searchData',
                                                    'searchField' => 'code,policynumber,name',
                                                    'returnField' => 'policynumber,name'
                                                )
                                            ),
                                            'allowedStatusForEdit' => array(1),
                                            'callbackFunction' => 'getTabObj().updateCustomerInsurancePolicy()'
                                        )
                                    );
                                    ?> 
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
                    <div class="div-table-col detail-col-header">  <?php echo ucwords($obj->lang['service']); ?> </div>
                    <div class="div-table-col detail-col-header" style="width:80px; text-align:right;"> <?php echo ucwords($obj->lang['amount']); ?></div>
                    <div class="div-table-col detail-col-header" style="width:150px; text-align:right;"><?php echo ucwords($obj->lang['price']); ?></div>
                    <div class="div-table-col detail-col-header" style="width:150px; text-align:right;"> <?php echo ucwords($obj->lang['subtotal']); ?></div> 
                    <div class="div-table-col detail-col-header  icon-col <?php echo $obj->hideOnDisabled(); ?>"></div>
                </div>
            </div>
            <div class="div-table mnv-transaction transaction-detail" style="width:100%; border-bottom:1px solid #333; ">
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
                        $_POST['detailDescription[]'] =  $rsDetail[$i]['trdesc'];
                        $_POST['qty[]'] =   $obj->formatNumber($rsDetail[$i]['qty']);
                        $_POST['priceInUnit[]'] =   $obj->formatNumber($rsDetail[$i]['priceinunit']);
                        $_POST['detailSubtotal[]'] =   $obj->formatNumber($rsDetail[$i]['total']);
 
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
                                    </div>
                                    <div class="div-table-col detail-col-detail" style="width:80px;">
                                        <?php echo $obj->inputNumber('qty[]', array('overwritePost' => $overwrite, 'etc' => 'style="text-align:right;" ' . $etc)); ?>
                                    </div>
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
										<label class="<?php echo $statusStyle; ?>"><?php echo $statusName; ?></label>
										<?php echo $quotationLink; ?>
									</div>
                                </div>
                            </div>
                        </div> 
                        <div class="div-table-col detail-col-detail icon-col align-top-adjust  <?php echo $obj->hideOnDisabled(); ?>">
                            <?php echo $obj->inputLinkButton('btnDeleteRows', '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button', 'etc' => 'tabIndex="- 1"')); ?>
                        </div>
                    </div>

                <?php }      ?>

            </div>

            <div style="clear:both; height:1em;"></div>
            <div style="float:left; display:inline-block;"><?php echo $obj->inputButton('btnAddRows', $obj->lang['addRows'], array('class' => 'btn btn-primary btn-second-tone')); ?></div>
 
			<div class="div-table" style="float:right">
				<div class="div-table-row">
					<div class="div-table-col">
							<div class="div-table" style="float:right;"> 
								<div class="div-table-row  form-group">
									<div class="div-table-col-3" style="text-align:right;">
										<?php echo ucwords($obj->lang['total']); ?>
									</div>
									<div class="div-table-col-3" style="width:150px">
										<?php echo $obj->inputNumber('grandtotal', array('readonly' => true, 'etc' => 'style="text-align:right;"')); ?>
									</div>
								</div> 
							</div> 
					</div> 
					<div class="div-table-col icon-col <?php echo $obj->hideOnDisabled(); ?>"></div>
				</div>
			</div>	
						 
			<div style="clear:both"></div>  
            <div class="form-button-margin"></div>
            <div class="form-button-panel"> <?php echo $obj->generateSaveButton(array(), true); ?> </div>
        </form>

        <?php echo $obj->showDataHistory(); ?>
    </div>
</body>

</html>
