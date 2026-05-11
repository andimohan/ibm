<?php
require_once '../_config.php';
require_once '../_include-v2.php';

includeClass(array('ShippingRate.class.php'));
$shippingRate = createObjAndAddToCol(new ShippingRate());
$city = createObjAndAddToCol(new City());
$cityCategory = createObjAndAddToCol(new CityCategory());
$logisticSalesOrder = createObjAndAddToCol(new LogisticSalesOrder());

$obj = $shippingRate;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
// some modules that have different security object from that of their class

if (!$security->isAdminLogin($securityObject, 10, true));

$formAction = 'shippingRateList';

$rsTransportation = $logisticSalesOrder->getTransportationType();
$arrTransportation = $obj->generateComboboxOpt(array('data' => $rsTransportation, 'label' => 'name'));
$rsCostTransportationKey = array();

$isQuickAdd = (isset($_GET) && !empty($_GET['quickadd'])) ? true : false;
$rs = prepareOnLoadData($obj);
$rsDetail = array();

if (!empty($_GET['id'])) {
	$id = $_GET['id'];

	if (!empty($rs[0]['fromcitykey'])) {
		$rsFromCity = $city->searchData('city.pkey', $rs[0]['fromcitykey'], true);
		$_POST['fromCityName'] = $rsFromCity[0]['name'] . ', ' . $rsFromCity[0]['categoryname'];
	}

	if (!empty($rs[0]['destinationcitykey'])) {
		$rsDestinationCity = $city->searchData('city.pkey', $rs[0]['destinationcitykey'], true);
		$_POST['destinationCityName'] = $rsDestinationCity[0]['name'] . ', ' . $rsDestinationCity[0]['categoryname'];
	}
	$rsCostTransportationKey = $obj->getDetailWithRelatedInformation($id);
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
			var shippingRate = new ShippingRate(tabID);
			prepareHandler(shippingRate);

			var fieldValidation = {
				code: {
					validators: {
						notEmpty: {
							message: phpErrorMsg.code[1]
						},
					}
				},
				fromCityName: {
					validators: {
						notEmpty: {
							message: phpErrorMsg.city[1]
						},
					}
				},
				destinationCityName: {
					validators: {
						notEmpty: {
							message: phpErrorMsg.city[1]
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

			<div class="div-table main-tab-table-2">
				<div class="div-table-row">
					<div class="div-table-col">
						<div class="div-tab-panel">
							<div class="div-table-caption border-purple"><?php echo ucwords($obj->lang['generalInformation']); ?></div>
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
								<label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['fromCity']); ?></label>
								<div class="col-xs-9">
									<?php echo $obj->inputAutoComplete(
										array(
											'objRefer' => $city,
											'element' => array(
												'value' => 'fromCityName',
												'key' => 'hidFromCityKey'
											),
											'source' => array(
												'url' => 'ajax-city.php',
												'data' => array('action' => 'searchData')
											),
											'popupForm' => array(
												'url' => 'cityForm.php',
												'element' => array(
													'value' => 'fromCityName',
													'key' => 'hidFromCityKey'
												),
												'width' => '600px',
												'title' => ucwords($obj->lang['add'] . ' - ' . $obj->lang['city'])
											),
										)
									);
									?>
								</div>
							</div>
							<div class="form-group">
								<label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['destinationCity']); ?></label>
								<div class="col-xs-9">
									<?php echo $obj->inputAutoComplete(
										array(
											'objRefer' => $city,
											'element' => array(
												'value' => 'destinationCityName',
												'key' => 'hidDestinationCityKey'
											),
											'source' => array(
												'url' => 'ajax-city.php',
												'data' => array('action' => 'searchData')
											),
											'popupForm' => array(
												'url' => 'cityForm.php',
												'element' => array(
													'value' => 'destinationCityName',
													'key' => 'hidDestinationCityKey'
												),
												'width' => '600px',
												'title' => ucwords($obj->lang['add'] . ' - ' . $obj->lang['city'])
											),
										)
									);
									?>
								</div>
							</div>
						</div>
					</div>
					<div class="div-table-col">
						<div class="div-tab-panel">
							<div class="div-table-caption border-red"><?php echo ucwords($obj->lang['shippingFee']); ?></div>
							<?php 
							
								if(!empty($rsCostTransportationKey)) 
									$costByTransportation = array_column($rsCostTransportationKey,null,'transportationkey');
							
								foreach ($rsTransportation as $transportationKey => $transportation) {

									echo '<div class="col-xs-12 section-title">'.$transportation['name'].'</div>';
								
									$arrCostTransportationKey = array();
								  	$arrCostTransportationKey = (isset($costByTransportation[$transportation['pkey']])) ? $costByTransportation[$transportation['pkey']] : array();
 
										
									$_POST['hidDetailKey[]'] = '';
									$_POST['firstFee[]'] = '';
									$_POST['nextFee[]'] = '';
								 
									if(!empty($arrCostTransportationKey)){ 
										$_POST['hidDetailKey[]'] = $arrCostTransportationKey['pkey'];
										$_POST['firstFee[]'] = $obj->formatNumber($arrCostTransportationKey['firstfee']);
										$_POST['nextFee[]'] = $obj->formatNumber($arrCostTransportationKey['nextfee']);
									}
									
									$unitName = $transportation['unitname']; 
									
									$hidDetailKey = $obj->inputHidden('hidDetailKey[]'); 
									$firstFeeInput = $obj->inputNumber('firstFee[]',array('etc' => 'style="text-align:right;"')); 
									$nextFeeInput = $obj->inputNumber('nextFee[]', array('etc' => 'style="text-align:right;"')); 
									$hidTransportationKey = $obj->inputHidden('hidTransportationKey[]', array('value' => $transportation['pkey'])); 

										echo '     
												<div class="form-group">
													<div class="col-xs-12">
														'.$hidDetailKey.'
														'.$hidTransportationKey.'
														<div class="flex">
															<div>'.$firstFeeInput.'</div><div class="text-muted">/ '.$unitName.'</div>
															<div>&nbsp;&nbsp;</div>
															<div>'.$nextFeeInput.'</div><div class="text-muted">/ '.$unitName.' '.$obj->lang['next'].'</div>

														</div>	
													</div>
												</div>';
								}
							?>
						</div>
					</div>
				</div>
			</div>


			<div class="form-button-margin"></div>
			<div class="form-button-panel">
				<?php echo $obj->generateSaveButton(); ?>
			</div>

		</form>
		<?php echo $obj->showDataHistory(); ?>
	</div>

</body>

</html>
