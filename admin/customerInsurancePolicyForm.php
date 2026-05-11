<?php
require_once '../_config.php';
require_once '../_include-v2.php';

includeClass('CustomerInsurancePolicy.class.php');
$customerInsurancePolicy = createObjAndAddToCol(new CustomerInsurancePolicy());
$customer = createObjAndAddToCol(new Customer());
$supplier = createObjAndAddToCol(new Supplier());
$country = createObjAndAddToCol(new Country());
$customerCategory = createObjAndAddToCol(new CustomerCategory());

$obj = $customerInsurancePolicy;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
// some modules that have different security object from that of their class

if (!$security->isAdminLogin($securityObject, 10, true));

$formAction = 'customerInsurancePolicyList';

$isQuickAdd = (isset($_GET) && !empty($_GET['quickadd'])) ? true : false;

$rs = prepareOnLoadData($obj);
$_POST['dateOfBirth'] = '01 / 01 / 2000';
$_POST['expiredDate'] = date('d / m / Y'); 

$rsInsuranceCompany = array();
 
$editCountryInactiveCriteria = '';
 
if (!empty($_GET['id'])) {

    $id = $_GET['id'];
	$rsCustomer = $customer->searchData($customer->tableName.'.pkey',$rs[0]['refkey']);
	$rsInsuranceCompany = $customer->getInsuranceCompanyDetail($rs[0]['refkey']);
		
    $_POST['name'] = $rs[0]['name'];
    $_POST['hidId'] = $id;
	$_POST['hidRefKey'] = $rs[0]['refkey'];
    $_POST['phone'] = $rs[0]['phone'];
    $_POST['mobile'] = $rs[0]['mobile'];
    $_POST['policyNumber'] = $rs[0]['policynumber'];
    $_POST['email'] = $rs[0]['email'];
    $_POST['trDesc'] = $rs[0]['trdesc'];
    $_POST['IDNumber'] = $rs[0]['idnumber'];
    $_POST['dateOfBirth'] = $obj->formatDBDate($rs[0]['dateofbirth'], 'd / m / Y');
    $_POST['expiredDate'] = $obj->formatDBDate($rs[0]['expireddate'], 'd / m / Y'); 
    $_POST['excessFee'] =$obj->formatNumber($rs[0]['excessfee']);
	$_POST['selCountry'] = $rs[0]['countrykey'];
	$_POST['selInsuranceCompany'] = $rs[0]['supplierkey'];
 	
	$_POST['customerName'] = $rsCustomer[0]['name'];
	$_POST['categoryName'] = $rsCustomer[0]['categoryname'];
	
    
    $editCountryInactiveCriteria = ' or '.$country->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['countrykey']);
}

$arrCountry = $country->generateComboboxOpt(null,array('criteria' =>' and ('.$country->tableName.'.statuskey = 1 '. $editCountryInactiveCriteria.')'));
$arrInsuranceCompany = $obj->generateComboboxOpt(array('data' => $rsInsuranceCompany,'label' => 'suppliername','value'=>'supplierkey'));
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
            var opt = {};
            opt.showItemImage = false;
            <?php  //echo ($showItemImage) ? 'true' : 'false';
            ?>;
            var customerInsurancePolicy = new CustomerInsurancePolicy(tabID, opt);
            prepareHandler(customerInsurancePolicy);

            var fieldValidation = {
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
                            message: phpErrorMsg.customer[1]
                        },
                    }
                },
                email: {
                    validators: {
                        emailAddress: {
                            message: phpErrorMsg.email[3]
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
            <?php echo $obj->inputHidden('hidLatLng'); ?>
            <?php echo $obj->inputHidden('hidId'); ?>
            <div class="div-table main-tab-table-2">
                <div class="div-table-row">
                    <div class="div-table-col">
                        <div class="div-tab-panel">
                            <div class="div-table-caption border-orange"><?php echo ucwords($obj->lang['generalInformation']); ?></div>

                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['status']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo  $obj->inputSelect('selStatus', $arrStatus); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['code'] .' / '. $obj->lang['policyNumber']); ?></label>
                                <div class="col-xs-9">
                                 <div class="flex">
								  <div class="consume"><?php echo  $obj->inputAutoCode('code'); ?>	</div>
								  <div>/</div>
								  <div class="consume"><?php echo  $obj->inputText('policyNumber'); ?>	</div> 
                                 </div>
								</div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['name'].' / ' .$obj->lang['company']); ?></label>
                                <div class="col-xs-9">
								 <div class="flex">
								  <div class="consume"><?php echo  $obj->inputText('name'); ?>	</div>
								  <div>/</div>
								  <div class="consume"> <?php
                                    echo $obj->inputAutoComplete(
                                        array( 
                                            'element' => array(
                                                'value' => 'customerName',
                                                'key' => 'hidRefKey'
                                            ),
                                            'source' => array(
                                                'url' => 'ajax-customer.php',
                                                'data' => array(
                                                    'action' => 'searchData'
                                                )
                                            ),
                                            'callbackFunction' => 'getTabObj().updateCustomer()'
                                        )
                                    );
                                    ?></div> 
                                 </div>
									
                                </div>
                            </div>   
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['category']); ?></label> 
                                <div class="col-xs-9"> 
                                       <?php echo  $obj->inputText('categoryName', array('readonly' => true)); ?>  
                                </div> 
                            </div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['IDNumber']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo  $obj->inputText('IDNumber'); ?>
                                </div>
                            </div>
                           <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['country']); ?></label>
                                <div class="col-xs-9">
                                    <?php
                                    	echo $obj->inputSelect('selCountry', $arrCountry); 
                                    ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['dateOfBirth']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputDate('dateOfBirth'); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['phone']); ?> / <?php echo ucwords($obj->lang['mobilePhone']); ?></label>
                                <div class="col-xs-4" style="padding-right:0">
                                    <?php echo $obj->inputText('phone'); ?>
                                </div>
                                <div class="col-xs-5" style="padding-left:5px">
                                    <?php echo $obj->inputText('mobile'); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['email']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputText('email'); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['insuranceCompany']); ?></label>
                                <div class="col-xs-9">
                                    <?php 
                                    	echo $obj->inputSelect('selInsuranceCompany', $arrInsuranceCompany); 
                                    ?>
                                </div>
                            </div> 
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['excess']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputNumber('excessFee'); ?>
                                </div>
                            </div> 
                        </div>
                    </div>
                    <div class="div-table-col">
                        <div class="div-tab-panel">
                            <div class="div-table-caption border-blue"><?php echo ucwords($obj->lang['note']); ?></div>
                            <div class="form-group">
                                <div class="col-xs-12">
                                    <?php echo  $obj->inputTextArea('trDesc', array('etc' => 'style="height:10em;"')); ?>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="form-button-panel"> <?php echo $obj->generateSaveButton(); ?> </div>
        </form>

        <?php echo $obj->showDataHistory(); ?>
    </div>
</body>

</html>