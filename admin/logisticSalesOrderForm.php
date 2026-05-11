<?php
require_once '../_config.php';
require_once '../_include-v2.php';

includeClass(array('LogisticSalesOrder.class.php', 'Customer.class.php', 'TermOfPayment.class.php', 'PaymentMethod.class.php'));
$logisticSalesOrder = createObjAndAddToCol(new LogisticSalesOrder());
$customer = createObjAndAddToCol(new Customer());
$city = createObjAndAddToCol(new City());
$paymentMethod = createObjAndAddToCol(new PaymentMethod());
$termOfPayment = createObjAndAddToCol(new TermOfPayment());
$warehouse = createObjAndAddToCol(new Warehouse());
$security = createObjAndAddToCol(new Security());


$obj = $logisticSalesOrder;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
// some modules that have different security object from that of their class

if (!$security->isAdminLogin($securityObject, 10, true));


$overwriteSellingPriceAllowed = $security->isAdminLogin($logisticSalesOrder->overwriteContractSecurityObject,10); 
$formAction = 'logisticSalesOrderList';

$isQuickAdd = (isset($_GET) && !empty($_GET['quickadd'])) ? true : false;

$_POST['trDate'] = date('d / m / Y');
$rs = prepareOnLoadData($obj);

$rsDetail = array();
$rsPaymentMethodDetail = array();

$finalDiscDecimalType = 'inputnumber';

$editWarehouseInactiveCriteria = '';
$editTermOfPaymentInactiveCriteria = '';
$editPaymentMethodInactiveCriteria = '';

$useVoucherPoint = $obj->loadSetting('transactionVoucherPoint');

if (!empty($_GET['id'])) {
	$id = $_GET['id'];

	$rsPaymentMethodDetail = $obj->getPaymentMethodDetail($id);
	$rsDetail = $obj->getDetailWithRelatedInformation($id);

	//Sender
	$rsSender = $customer->getDataRowById($rs[0]['senderkey']);
	$_POST['senderName'] = $rsSender[0]['name'];
	$_POST['hidSenderKey'] = $rsSender[0]['pkey'];
	$_POST['senderPhone'] = $rsSender[0]['phone'];

	if (!empty($rs[0]['sendercitykey'])) {
		$rsCitySender = $city->searchData($city->tableName . '.pkey', $rs[0]['sendercitykey'], true);
		$_POST['senderCity'] = $rsCitySender[0]['citycategoryname'];
	}

	//Recipient
	$rsRecipient = $customer->getDataRowById($rs[0]['recipientkey']);
	$_POST['recipientName'] = $rsRecipient[0]['name'];
	$_POST['recipientPhone'] = $rsRecipient[0]['phone'];
	$_POST['hidRecipientKey'] = $rsRecipient[0]['pkey'];
	
	if (!empty($rs[0]['recipientcitykey'])) {
		$rsCityRecipient = $city->searchData($city->tableName . '.pkey', $rs[0]['recipientcitykey'], true);
		$_POST['recipientCity'] = $rsCityRecipient[0]['citycategoryname'];
	}

	$_POST['trDate'] = $obj->formatDBDate($rs[0]['trdate'], 'd / m / Y');
	$_POST['totalWeight'] =   $obj->formatNumber($rs[0]['totalweight']);
	$_POST['taxPercentage'] = $obj->formatNumber($rs[0]['taxpercentage'], 2);


	if ($rs[0]['finaldiscounttype']  == 2) {
		$finalDiscDecimal = 2;
		$finalDiscDecimalType = 'inputdecimal';
	}

	$_POST['selFinalDiscountType'] = $rs[0]['finaldiscounttype'];
	$_POST['finalDiscount'] = $obj->formatNumber($rs[0]['finaldiscount'], $finalDiscDecimal);
	$_POST['packingFee'] = $obj->formatNumber($rs[0]['packingfee']);
	$_POST['grandTotal'] = $obj->formatNumber($rs[0]['grandtotal']);


	$editTermOfPaymentInactiveCriteria = ' or ' . $termOfPayment->tableName . '.pkey = ' . $obj->oDbCon->paramString($rs[0]['termofpaymentkey']);
} 
	
$rsTransportationType = $obj->getTransportationType(); 
$arrTransportation = $obj->generateComboboxOpt(array('data' =>$rsTransportationType, 'label' => 'name'));
$arrWarehouse = $warehouse->generateComboboxOpt(null, array('criteria' => ' and (' . $warehouse->tableName . '.statuskey = 1' . $editWarehouseInactiveCriteria . ')'));
$arrStatus = $obj->generateComboboxOpt(array('data' => $obj->getAllStatus(), 'label' => 'status'));
$arrPaymentMethod = $paymentMethod->generateComboboxOpt(null, array('criteria' => ' and (' . $paymentMethod->tableName . '.statuskey = 1' . $editPaymentMethodInactiveCriteria . ')'));
$rsTOP = $termOfPayment->searchDataRow(
	array($termOfPayment->tableName . '.pkey', $termOfPayment->tableName . '.name', $termOfPayment->tableName . '.duedays'),
	' and (' . $termOfPayment->tableName . '.statuskey = 1' . $editTermOfPaymentInactiveCriteria . ')',
	' order by duedays asc'
);
$arrTOP = $obj->generateComboboxOpt(array('data' => $rsTOP));

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title></title>

	<script type="text/javascript">
		jQuery(document).ready(function() {
			var tabID = selectedTab.newPanel[0].id;
			var cashTOP = Array();

			<?php
			for ($i = 0; $i < count($rsTOP); $i++) {
				if ($rsTOP[$i]['duedays'] <> 0)
					echo 'cashTOP.push(' . $rsTOP[$i]['pkey'] . ');' . chr(13);
			}
			?>
			
			 var opt = new Array();
			 opt['transportationType'] = <?php echo json_encode(array_column($rsTransportationType,null,'pkey')); ?>;
			 opt['sellingPriceAllowed'] = <?php echo json_encode($overwriteSellingPriceAllowed); ?>;

			logisticSalesOrder = new LogisticSalesOrder(tabID, <?php echo json_encode($rs); ?>, cashTOP, opt);

			var tabID = <?php echo ($isQuickAdd) ?  $_GET['tabID'] :  'selectedTab.newPanel[0].id'; ?>;

			prepareHandler(logisticSalesOrder);

			var fieldValidation = {
				code: {
					validators: {
						notEmpty: {
							message: phpErrorMsg.code[1]
						},
					}
				},
				recipientName: {
					validators: {
						notEmpty: {
							message: phpErrorMsg.name[1]
						},
					}
				},
				senderName: {
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

			<div class="div-table main-tab-table-2">
				<div class="div-table-row">

					<div class="div-table-col">
						<div class="div-tab-panel">
							<div class="div-table-caption border-purple"><?php echo ucwords($obj->lang['generalInformation']); ?></div>

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
									<?php echo  $obj->inputDate('trDate');  ?>
								</div>
							</div>
							<div class="form-group">
								<label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['warehouse']); ?></label>
								<div class="col-xs-9">
									<?php echo  $obj->inputSelect('selWarehouseKey', $arrWarehouse); ?>
								</div>
							</div>
							<div class="form-group">
								<label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['transportation']); ?></label>
								<div class="col-xs-9">
									<?php echo  $obj->inputSelect('selTransportation', $arrTransportation); ?>
								</div>
							</div>
							<div class="form-group">
								<label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['total']); ?></label>
								<div class="col-xs-9">
									<div class="flex">
										<div><?php echo $obj->inputNumber('totalQty', array('readonly' => true, 'etc' => 'style="text-align:right; width:80px"')); ?></div>
										<div><?php echo ucwords($obj->lang['bale']); ?> </div>
										<div><?php echo $obj->inputNumber('totalWeight', array('readonly' => true, 'etc' => 'style="text-align:right; width:80px"')); ?></div>
										<div>Kg </div>
										<div class="consume"><?php echo $obj->inputNumber('grandTotal', array('readonly' => true, 'etc' => 'style="text-align:right;"')); ?></div>
										<div>IDR</div>
									</div>
								</div> 
							</div>
        			         <!-- <div class="form-group">
								<label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['goodsDescription']); ?></label>
								<div class="col-xs-9">
									<?php echo  $obj->inputText('goodsDescription'); ?>
								</div>
							</div> -->
						</div>
					</div>

					<div class="div-table-col">
						<div class="div-tab-panel">
							<div class="div-table-caption border-red"><?php echo ucwords($obj->lang['note']); ?></div>
							<div class="form-group">
								<div class="col-xs-12">
									<?php echo  $obj->inputTextArea('trDesc', array('etc' => 'style="height:10em;"')); ?>
								</div>
							</div>
						</div>

					</div>

				</div>
			</div>
			<div class="div-table main-tab-table-2">
				<div class="div-table-row">
					<div class="div-table-col">
						<div class="div-tab-panel">
							<div class="div-table-caption border-blue"><?php echo ucwords($obj->lang['sender']); ?></div>

							<div class="form-group">
								<label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['senderName']); ?></label>
								<div class="col-xs-9">
									<?php
									echo $obj->inputAutoComplete(
										array(
											'element' => array(
												'value' => 'senderName',
												'key' => 'hidSenderKey'
											),
											'source' => array(
												'url' => 'ajax-customer.php',
												'data' => array('action' => 'searchData', 'searchField' => 'phone,name', 'returnField' => 'name')
											), 
											'clearOnNotFound' => false,
											'callbackFunction' => 'getTabObj().updateSender();'
										)
									);
									?>
								</div>
							</div>
							<div class="form-group">								
								<label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['phone']); ?></label>
								<div class="col-xs-9">
									<?php echo $obj->inputText('senderPhone'); ?>
								</div>
							</div>
							
							<div class="form-group">
								<label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['senderCity']); ?></label>
								<div class="col-xs-9">
									<?php
									echo $obj->inputAutoComplete(
										array(
											'element' => array(
												'value' => 'senderCity',
												'key' => 'hidSenderCityKey'
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
								<label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['address']); ?></label>
								<div class="col-xs-9">
									<?php echo $obj->inputTextArea('senderAddress', array( 'etc' => 'style="height:10em;"')); ?>
								</div>
							</div>
							
							
							<div class="form-group">
								<label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['messenger']); ?></label>
								<div class="col-xs-9">
									<?php echo $obj->inputText('courier'); ?>
								</div>
							</div>

						</div>
					</div>
					<div class="div-table-col">
						<div class="div-tab-panel">
							<div class="div-table-caption border-orange"><?php echo ucwords($obj->lang['recipient']); ?></div>
							<div class="form-group">
								<label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['recipientName']); ?></label>
								<div class="col-xs-9">
									<?php
									echo $obj->inputAutoComplete(
										array(
											'element' => array(
												'value' => 'recipientName',
												'key' => 'hidRecipientKey'
											),
											'source' => array(
												'url' => 'ajax-customer.php',
												'data' => array('action' => 'searchData', 'searchField' => 'phone,name', 'returnField' => 'name')
											), 	
											'clearOnNotFound' => false,
											'callbackFunction' => 'getTabObj().updateRecipient();'

										)
									);
									?>
								</div>
							</div>
							<div class="form-group">
								<label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['phone']); ?></label>
								<div class="col-xs-9">
									<?php echo $obj->inputText('recipientPhone'); ?>
								</div>
							</div>
							<div class="form-group">
								<label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['recipientCity']); ?></label>
								<div class="col-xs-9">
									<?php
									echo $obj->inputAutoComplete(
										array(
											'element' => array(
												'value' => 'recipientCity',
												'key' => 'hidRecipientCityKey'
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
								<label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['address']); ?></label>
								<div class="col-xs-9">
									<?php echo $obj->inputTextArea('recipientAddress', array( 'etc' => 'style="height:10em;"')); ?>
								</div>
							</div>
						</div>
					</div>



				</div>
			</div>

			<div class="div-table mnv-transaction transaction-detail" style="width:100%; border-bottom:1px solid #333; ">
				<div class="div-table-row">
					<div class="div-table-col detail-col-header"><?php echo ucwords($obj->lang['description']); ?> </div>
					<div class="div-table-col detail-col-header" style="width:80px;text-align:right;"><?php echo ucwords($obj->lang['weight']); ?> (Kg)</div>
					<div class="div-table-col detail-col-header" style="width:70px;text-align:center;"><?php echo ucwords($obj->lang['lengthShort']); ?></div>
					<div class="div-table-col detail-col-header" style="width:70px;text-align:center;"><?php echo ucwords($obj->lang['widthShort']); ?></div>
					<div class="div-table-col detail-col-header" style="width:70px;text-align:center;"><?php echo ucwords($obj->lang['heightShort']); ?></div>
					<div class="div-table-col detail-col-header" style="width:100px;text-align:right;"><?php echo ucwords($obj->lang['weight']); ?> (Vol)</div>
					<div class="div-table-col detail-col-header" style="width:100px;text-align:right;"><?php echo ucwords($obj->lang['weight']); ?></div>
					<div class="div-table-col detail-col-header" style="width:100px;text-align:right;"><?php echo ucwords($obj->lang['price']); ?></div>
					<div class="div-table-col detail-col-header  <?php echo $obj->hideOnDisabled(); ?> icon-col"></div>
				</div>

				<?php
				$totalRows = count($rsDetail);
				for ($i = 0; $i <= $totalRows; $i++) {

					$class =  'transaction-detail-row';
					$overwrite = true;
					$disabled = false;

                    $readonly = !$overwriteSellingPriceAllowed;
					if ($i == $totalRows) {
						$class = 'detail-row-template';
						$overwrite = false;
						$disabled = true;
					} else {
						$_POST['hidDetailKey[]'] =  $rsDetail[$i]['pkey'];
						$_POST['detailDescription[]'] =  $rsDetail[$i]['description'];
						$_POST['detailWeight[]'] =   $obj->formatNumber($rsDetail[$i]['weight']);
						$_POST['detailLength[]'] =   $obj->formatNumber($rsDetail[$i]['length']);
						$_POST['detailWidth[]'] =   $obj->formatNumber($rsDetail[$i]['width']);
						$_POST['detailHeight[]'] =   $obj->formatNumber($rsDetail[$i]['height']);
						$_POST['detailFinalWeight[]'] = $obj->formatNumber($rsDetail[$i]['finalweight']);
						$_POST['detailCBMWeight[]'] = $obj->formatNumber($rsDetail[$i]['cbmweight']);
						$_POST['detailSubtotal[]'] = $obj->formatNumber($rsDetail[$i]['subtotal']);
						$_POST['priceInUnit[]'] = $obj->formatNumber($rsDetail[$i]['priceinunit']);
					}

				?>
					<div class="div-table-row <?php echo $class; ?>">
						<div class="div-table-col detail-col-detail">
							<?php echo $obj->inputHidden('hidDetailKey[]', array('overwritePost' => $overwrite, 'disabled' => $disabled)); ?>
							<?php echo $obj->inputHidden('priceInUnit[]', array('overwritePost' => $overwrite, 'disabled' => $disabled)); ?>
							<?php echo $obj->inputText('detailDescription[]', array('overwritePost' => $overwrite, 'disabled' => $disabled)); ?>
						</div>
						<div class="div-table-col detail-col-detail"><?php echo $obj->inputNumber('detailWeight[]', array('overwritePost' => $overwrite, 'etc' => 'style="text-align:right;"', 'disabled' => $disabled)); ?></div>
						<div class="div-table-col detail-col-detail"><?php echo $obj->inputNumber('detailLength[]', array('overwritePost' => $overwrite, 'etc' => 'style="text-align:right;"', 'disabled' => $disabled)); ?></div>
						<div class="div-table-col detail-col-detail"><?php echo $obj->inputNumber('detailWidth[]', array('overwritePost' => $overwrite, 'etc' => 'style="text-align:right;"', 'disabled' => $disabled)); ?></div>
						<div class="div-table-col detail-col-detail"><?php echo $obj->inputNumber('detailHeight[]', array('overwritePost' => $overwrite, 'etc' => 'style="text-align:right;"', 'disabled' => $disabled)); ?></div>
						<div class="div-table-col detail-col-detail"><?php echo $obj->inputNumber('detailCBMWeight[]', array('readonly' => true, 'overwritePost' => $overwrite, 'etc' => 'style="text-align:right;"', 'disabled' => $disabled)); ?></div>
						<div class="div-table-col detail-col-detail"><?php echo $obj->inputNumber('detailFinalWeight[]', array('readonly' => true, 'overwritePost' => $overwrite, 'etc' => 'style="text-align:right;"', 'disabled' => $disabled)); ?></div>
						<div class="div-table-col detail-col-detail"><?php echo $obj->inputNumber('detailSubtotal[]', array('readonly' => $readonly, 'overwritePost' => $overwrite, 'etc' => 'style="text-align:right;"', 'disabled' => $disabled)); ?></div>
						<div class="div-table-col detail-col-detail <?php echo $obj->hideOnDisabled(); ?>"><?php echo $obj->inputLinkButton('btnDeleteRows', '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button', 'etc' => 'tabIndex="-1"')); ?></div>
					</div>

				<?php } ?>

			</div>

			<div style="clear:both; height:1em;"></div>
			<div style="float:left; display:inline-block;"><?php echo $obj->inputButton('btnAddRows', $obj->lang['addRows'], array('class' => 'btn btn-primary btn-second-tone')); ?></div>

			<div>
				<div style="width:350px; float:right; ">
					<div class="div-table" style="width:100%">
						<div class="div-table-row  form-group">
							<div class="div-table-col-3" style="text-align:right;">
								<?php echo ucwords($obj->lang['payment']); ?>
							</div>
							<div class="div-table-col-3" style="width:180px;">
								<?php echo  $obj->inputSelect('selTermOfPaymentKey', $arrTOP); ?>
							</div>
							<div class="div-table-col-3 <?php echo $obj->hideOnDisabled(); ?> icon-col"></div>
						</div>
					</div>

					<div class="mnv-total-group mnv-payment-method cashTOP">
						<div class="div-table" style="width: 100%">
							<div class="div-table-row  form-group">
								<div class="div-table-col-3" style="text-align:right;">
									<?php echo $obj->lang['totalPayment']; ?>
								</div>
								<div class="div-table-col-3" style="width:180px">
									<?php echo $obj->inputCollapsibleNumber('totalPayment', array('readonly' => true, 'etc' => 'style="text-align:right;" ')); ?>
								</div>
								<div class="div-table-col-3  <?php echo $obj->hideOnDisabled(); ?> icon-col"></div>
							</div>
						</div>

						<div class="mnv-total-group-detail">
							<div class="div-table  transaction-detail" style="width: 100%">
								<?php

								$totalRows = count($rsPaymentMethodDetail);

								for ($i = 0; $i <= $totalRows; $i++) {
									$class =  'transaction-detail-row';
									$overwrite = true;
									$disabled = false;

									if ($i == $totalRows) {
										$class = 'payment-method-row-template row-template';
										$overwrite = false;
										$disabled = true;
									} else {
										$_POST['hidDetailPaymentKey[]'] = $rsPaymentMethodDetail[$i]['pkey'];
										$_POST['selPaymentMethod[]'] = $rsPaymentMethodDetail[$i]['paymentkey'];
										$_POST['paymentMethodValue[]'] = $obj->formatNumber($rsPaymentMethodDetail[$i]['amount']);
									}
								?>

									<div class="div-table-row form-group payment-detail-row <?php echo $class; ?>">
										<div class="div-table-col-3" style="text-align:right;">
											<?php echo $obj->inputHidden('hidDetailPaymentKey[]', array('overwritePost' => $overwrite,  'disabled' => $disabled)); ?>
											<?php echo  $obj->inputSelect('selPaymentMethod[]', $arrPaymentMethod, array('overwritePost' => $overwrite, 'disabled' => $disabled)); ?>
										</div>
										<div class="div-table-col-3" style="width:180px">
											<?php echo $obj->inputNumber('paymentMethodValue[]', array('overwritePost' => $overwrite, 'disabled' => $disabled, 'class' => 'form-control inputnumber mnv-detail-field', 'etc' => 'style="text-align:right;" ')); ?>
										</div>
										<div class="div-table-col-3 <?php echo $obj->hideOnDisabled(); ?> icon-col">
											<?php echo $obj->inputLinkButton('btnDeleteRows', '<i class="fas fa-times"></i>', array('etc' => 'tabIndex="-1"  attrhandler="getTabObj().calculateTotal()"', 'class' => 'btn btn-link remove-button')); ?>
										</div>
									</div>

								<?php } ?>

								<div class="div-table-row form-group ">
									<div class="div-table-col-3"></div>
									<div class="div-table-col-3">
										<div class="text-link-01 mnv-total-group-hide-detail" style="float:right; text-align:right;"><?php echo ucwords($obj->lang['hideDetail']); ?> </div>
									</div>
									<div class="div-table-col-3 <?php echo $obj->hideOnDisabled(); ?>"></div>
								</div>
								<div class="div-table-row form-group ">
									<div class="div-table-col-3 " style="height:1em"></div>
									<div class="div-table-col-3 "></div>
									<div class="div-table-col-3  <?php echo $obj->hideOnDisabled(); ?> "></div>
								</div>

							</div>
						</div>
					</div>

					<div class="div-table" style="width:100%; margin-top:1em">
						<div class="div-table-row  form-group">
							<div class="div-table-col-3" style="text-align:right;">
								<?php echo ucwords($obj->lang['balance']); ?>
							</div>
							<div class="div-table-col-3" style="width:180px;">
								<?php echo $obj->inputNumber('balance', array('readonly' => true, 'etc' => 'style="text-align:right;"')); ?>
							</div>
							<div class="div-table-col-3 <?php echo $obj->hideOnDisabled(); ?> icon-col"></div>
						</div>


						<div class="div-table-row  form-group">
							<div class="div-table-col-3"></div>
							<div class="div-table-col-3"> </div>
						</div>
					</div>

				</div>
				<div class="div-table" style="float:right; margin-right:4em">
					<div class="div-table-row  form-group">
						<div class="div-table-col-3" style="text-align:right;">
							<?php echo ucwords($obj->lang['subtotal']); ?>
						</div>
						<div class="div-table-col-3" style="width:200px;">
							<?php echo $obj->inputNumber('subtotal', array('readonly' => true, 'etc' => 'style="text-align:right;"')); ?>
						</div>

                    </div>

<!--
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
-->
<!--
					<div class="div-table-row  form-group   form-detail-field">
						<div class="div-table-col-3" style="text-align:right; padding-top:2em;">
							<?php echo ucwords($obj->lang['beforeTax']); ?>
						</div>
						<div class="div-table-col-3" style="padding-top:2em;">
							<?php echo $obj->inputNumber('beforeTaxTotal', array('disabled' => true, 'etc' => 'style="text-align:right;"')); ?>
						</div>

					</div>
-->
					<div class="div-table-row  form-group" style="display: none;">
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
					<?php if ($useVoucherPoint == 1) { ?>
						<?php
						$totalRows = count($rsVoucher);
						for ($i = 0; $i <= $totalRows; $i++) {
							$class =  'transaction-detail-row';
							$overwrite = true;
							$disabled = false;

							if ($i == $totalRows) {
								$class = 'voucher-row-template row-template';
								$overwrite = false;
								$disabled = true;
							} else {
								$_POST['hidDetailVoucherKey[]'] = $rsVoucher[$i]['pkey'];
								$_POST['hidVoucherKey[]'] = $rsVoucher[$i]['voucherkey'];
								$_POST['voucherCode[]'] = $rsVoucher[$i]['code'];
								$_POST['voucherAmount[]'] = $obj->formatNumber($rsVoucher[$i]['amount']);
							}
						?>

							<div class="div-table-row form-group voucher-row <?php echo $class; ?>">
								<div class="div-table-col-3" style="text-align:right;">
									<?php echo $obj->inputHidden('hidDetailVoucherKey[]', array('overwritePost' => $overwrite,  'disabled' => $disabled)); ?>
									<?php echo $obj->inputHidden('hidVoucherKey[]', array('overwritePost' => $overwrite,  'disabled' => $disabled)); ?>
									<?php echo $obj->inputText('voucherCode[]', array('overwritePost' => $overwrite, 'disabled' => $disabled, 'etc' => 'placeholder="' . $obj->lang['voucherCode'] . '"')); ?>
								</div>
								<div class="div-table-col-3" style="width:180px">
									<?php echo $obj->inputNumber('voucherAmount[]', array('overwritePost' => $overwrite, 'class' => 'form-control inputnumber mnv-detail-field', 'disabled' => $disabled, 'readonly' => true, 'etc' => 'style="text-align:right;"')); ?>
								</div>
								<div class="div-table-col-3 <?php echo $obj->hideOnDisabled(); ?> icon-col">
									<?php echo $obj->inputLinkButton('btnDeleteRows', '<i class="fas fa-times"></i>', array('etc' => 'tabIndex="-1"', 'class' => 'btn btn-link remove-button')); ?>
								</div>
							</div>
						<?php } ?>
						<div style="clear:both; height: 1em"></div>
					<?php } ?>

					<?php if ($useVoucherPoint == 2) { ?>
						<div class="div-table-row  form-group form-detail-field">
							<div class="div-table-col-3" style="text-align:right;">
								<?php echo ucwords($obj->lang['point']); ?>
							</div>
							<div class="div-table-col-3">
								<?php echo $obj->inputText('pointValue', array('etc' => 'style="text-align:right;" ')); ?>
							</div>
						</div>
					<?php } ?>



<!--
					<div class="div-table-row  form-group   form-detail-field">
						<div class="div-table-col-3" style="text-align:right;">
							<?php echo ucwords($obj->lang['insurance']); ?>
						</div>
						<div class="div-table-col-3">
							<?php echo  $obj->inputCheckBox('useInsurance', array('etc' => 'style="margin-top:0;"')) ?>
						</div>
					</div>
-->
					<!--name =  shipmentFee di hapus -->
	               <div class="div-table-row  form-group ">
						<div class="div-table-col-3" style="text-align:right;">
							<?php echo ucwords($obj->lang['packingFee']); ?>
						</div>
						<div class="div-table-col-3" style="width:200px;">
							<?php echo $obj->inputNumber('packingFee', array('readonly' => false, 'etc' => 'style="text-align:right;"')); ?>
						</div>

					</div>
					<div class="div-table-row  form-group ">
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
							<?php echo $obj->inputNumber('grandTotal', array('readonly' => true, 'etc' => 'style="text-align:right;"')); ?>
						</div>
					</div>
<!--
					<div class="div-table-row  form-group">
						<div class="div-table-col-3" style="text-align:right;"> </div>
						<div class="div-table-col-3">
							<div class="form-detail-button" style="float:right; text-align:right;" relalt="<?php echo ucwords($obj->lang['hideDetail']); ?> "><?php echo ucwords($obj->lang['showDetail']); ?> </div>
						</div>
					</div>
-->
				</div>
				<div style="clear:both"></div>
			</div>


			<div class="form-button-margin"></div>
			<div class="form-button-panel">
				<?php echo $obj->generateSaveButton(array(), true); ?>
			</div>

		</form>
		<?php echo $obj->showDataHistory(); ?>
	</div>

</body>

</html>
