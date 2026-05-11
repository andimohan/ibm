<?php
require_once '../_config.php';
require_once '../_include-v2.php';

includeClass(array('MembershipLevel.class.php', 'CustomerFeatures.class.php'));
$membershipLevel = createObjAndAddToCol(new MembershipLevel());
$customerFeatures = createObjAndAddToCol(new CustomerFeatures());

$obj = $membershipLevel;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
// some modules that have different security object from that of their class

if (!$security->isAdminLogin($securityObject, 10, true));

$formAction = 'membershipLevelList';

$isQuickAdd = (isset($_GET) && !empty($_GET['quickadd'])) ? true : false;

$rs = prepareOnLoadData($obj);
 
$dataFeatures = $customerFeatures->searchDataRow(array(  $customerFeatures->tableName . '.name',   $customerFeatures->tableName . '.pkey'),
												' and '.$customerFeatures->tableName . '.statuskey  = 1'
												);

$rsDetailFeatures = array();                              
if (!empty($_GET['id'])) { // jika edit form 
	$id = $_GET['id'];
 	$rsDetailFeatures = $obj->getFeaturesDetail($id);
	
	$_POST['commissionTotal'] = $obj->formatNumber($rs[0]['commissiontotal'],-2);
		
	$rsItemImage = array();  
	if( !empty($rs[0]['file'])){
		$rsItemImage[0]['file'] =  $rs[0]['file'];
        $rsItemImage[0]['phpthumbhash'] = getPHPThumbHash($rsItemImage[0]['file']);
	
		$sourcePath = $obj->defaultDocUploadPath.$obj->uploadFolder.$id;
		$destinationPath = $obj->uploadTempDoc.$obj->uploadFolder.$id; 
		$obj->deleteAll($destinationPath); 
	
		if(!is_dir($destinationPath)) 
			mkdir ($destinationPath,  0755, true);
				
		$obj->fullCopy($sourcePath,$destinationPath); 
	}
	 
}

$arrStatus = $obj->generateComboboxOpt(array('data' => $obj->getAllStatus(), 'label' => 'status'));

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"> 
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title></title>

    <script type="text/javascript">
        jQuery(document).ready(function() {


            var tabID = <?php echo ($isQuickAdd) ?  $_GET['tabID'] :  'selectedTab.newPanel[0].id'; ?>;
			var membershipLevel = new MembershipLevel(tabID,"<?php echo $obj->uploadFolder; ?>",<?php echo json_encode($rsItemImage); ?>);

            prepareHandler(membershipLevel);

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
                            message: phpErrorMsg.name[1]
                        },
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

            <div class="div-table main-tab-table-1">
                <div class="div-table-row">
                    <div class="div-table-col">
                        <div class="div-tab-panel">
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['status']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo  $obj->inputSelect('selStatus', $arrStatus);  ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['code']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputAutoCode('code'); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['name']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputText('name'); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['price']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo  $obj->inputNumber('sellingprice'); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['activePeriod']); ?></label>
                                <div class="col-xs-9">
								 <div class="flex">
									 <div><?php echo  $obj->inputNumber('activePeriod'); ?></div>
									 <div class="consume text-muted"><?php echo $obj->lang['month']; ?></div>
								</div>	
                                </div>
                            </div>  
							<div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['commission']); ?></label>
                                <div class="col-xs-9">
                                    <div class="flex">
                                        <div class="flex">
                                            <div class="consume" ><?php echo $obj->inputSelect('commissionType', $obj->arrDiscountType) ?></div>
                                            <div style="width:290px"><?php echo  $obj->inputDecimal('commissionTotal'); ?></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
							<div class="form-group">
								<label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['image']); ?></label> 
								<div class="col-xs-9"> 

									 <!-- image uploader --> 
									<div class="item-image-uploader">
										<ul class="image-list" ></ul>
										<div style="clear:both; height:1em; "></div>
										<div class="file-uploader">	
											<noscript><p>Please enable JavaScript to use file uploader.</p></noscript> 
										</div>
									  </div>  
									<!-- image uploader --> 

								</div> 
							</div>   
							<div style="clear:both; height:1em"></div>
							<?php
								// harus pake model ini karena pertama kali add blm ad detail
								$arrFeaturesDetail = array_column($rsDetailFeatures, null, 'featurekey');
								$dataFeatures = $customerFeatures->searchDataRow( array($customerFeatures->tableName . '.name',$customerFeatures->tableName . '.pkey'), ' and '.$customerFeatures->tableName.'.statuskey = 1');
								foreach ($dataFeatures as $field) {
									$indexkey =  $field['pkey'];
									// pkey detail
									$_POST['hidFeaturesDetailKey[]'] = $arrFeaturesDetail[$indexkey]['pkey'];
									$_POST['hidFeatureKey[]'] = $indexkey;
									$_POST['quota[]'] = $arrFeaturesDetail[$indexkey]['quota'];
							  ?>
                                <div class="form-group">
                                    <label class="col-xs-3 control-label"><?php echo $field['name'] ?></label>
                                    <div class="col-xs-9">
                                        <?php echo $obj->inputHidden('hidFeaturesDetailKey[]'); ?>
                                        <?php echo $obj->inputHidden('hidFeatureKey[]', array('value' => $field['pkey'])); ?>
                                        <?php echo $obj->inputNumber('quota[]'); ?>
                                    </div>
                                </div>

                            <?php } ?>
                        </div>
                    </div>
                </div>
      </div>

    <div class="form-button-panel">
        <?php echo $obj->generateSaveButton(); ?>
    </div>

    </form>
    <?php echo $obj->showDataHistory(); ?>
    </div>
</body>

</html>
