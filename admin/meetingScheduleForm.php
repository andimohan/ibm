<?php
require_once '../_config.php';
require_once '../_include-v2.php';

includeClass(array('MeetingSchedule.class.php', 'MeetingPoint.class.php', 'Customer.class.php','OnlineChannel.class.php','PaymentType.class.php'));
$meetingSchedule = createObjAndAddToCol(new MeetingSchedule());
$meetingPoint = createObjAndAddToCol(new MeetingPoint());
$customer = createObjAndAddToCol(new Customer());
$onlineChannel = new OnlineChannel();
$paymetType = new PaymentType();

$obj = $meetingSchedule;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
// some modules that have different security object from that of their class

if (!$security->isAdminLogin($securityObject, 10, true));

$formAction = 'meetingScheduleList';

$isQuickAdd = (isset($_GET) && !empty($_GET['quickadd'])) ? true : false;

$_POST['trDate'] = date('d / m / Y H:i', strtotime('+1 day'));
$rs = prepareOnLoadData($obj);
$rsDetail = array();

if (!empty($_GET['id'])) { // jika edit form 
    $id = $_GET['id'];
    $_POST['trDate'] = $obj->formatDBDate($rs[0]['trdate'], 'd / m / Y H:i');
    $rsDetail = $obj->getDetailWithRelatedInformation($id);
	
	if(!empty($rs[0]['locationkey'])){
		$meetingPoint = new MeetingPoint();
		$rsMeetingPoint = $meetingPoint->getDataRowById($rs[0]['locationkey']);
		$_POST['address'] = $rsMeetingPoint[0]['name'];
	}

   if (!empty($rs[0]['hostkey'])) {
        $rsCustomer = $customer->getDataRowById($rs[0]['hostkey']);
        $_POST['hostName'] = $rsCustomer[0]['code']. ' - '.$rsCustomer[0]['name'];
    }
    
    $_POST['selPaymentType'] = $rs[0]['paymenttypekey'];
} 
 
$arrStatus = $obj->generateComboboxOpt(array('data' => $obj->getAllStatus(), 'label' => 'status'));
$arrLanguage = $obj->generateComboboxOpt(array('data' => $meetingSchedule->getLanguage(), 'label' => 'language'));
$arrPaymentType = $paymetType->generateComboboxOpt(null,array('criteria' =>' and ('.$paymetType->tableName.'.statuskey=1)'));
$arrOnlineOffline = $obj->generateComboboxOpt(array('data' => $meetingSchedule->getOnlineOffline(), 'label' => 'name'));
$arrMeetingPoint = $obj->generateComboboxOpt(array('data' => $meetingPoint->getQuery(), 'label' => 'name'));
$arrOnlineChannel = $onlineChannel->generateComboboxOpt(null,array('criteria' =>' and ('.$onlineChannel->tableName.'.statuskey=1)')); 

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title></title>

    <script type="text/javascript">
        jQuery(document).ready(function() {


            var tabID = <?php echo ($isQuickAdd) ?  $_GET['tabID'] :  'selectedTab.newPanel[0].id'; ?>;
            var meetingSchedule = new MeetingSchedule(tabID);
            if ("<?= $id ?>" !== "") {
                meetingSchedule.getMeetingPoint();
            }
            prepareHandler(meetingSchedule);

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
                            message: phpErrorMsg.meetingSchedule[1]
                        },
                    }
                }
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
                           <div class="div-table-caption border-orange"><?php echo ucwords($obj->lang['generalInformation']); ?></div>

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
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['date']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo  $obj->inputDateTime('trDate');  ?>
                                </div>
                            </div>
							<div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['host']); ?></label>
                                <div class="col-xs-9">
                                    <?php
                                    echo $obj->inputAutoComplete(
                                        array( 
                                            'element' => array(
                                                'value' => 'hostName',
                                                'key' => 'hidHostKey'
                                            ),
                                            'source' => array(
                                                'url' => 'ajax-customer.php',
                                                'data' => array('action' => 'searchData', 'searchField' => 'code,name')
                                            )
                                        )
                                    );

                                    ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['topic']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputText('name'); ?>
                                </div>
                            </div> 
<!--
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['language']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo  $obj->inputSelect('selLanguage', $arrLanguage);  ?>
                                </div>
                            </div>
-->
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['meetingType']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo  $obj->inputSelect('selOnlineOffline', $arrOnlineOffline);  ?>
                                </div>
                            </div>
                              <div class="form-group offline">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['meal']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo  $obj->inputSelect('selPaymentType', $arrPaymentType);  ?>
                                </div>
                            </div>
                            <div class="form-group offline">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['meetingPoint']); ?></label>
                                <div class="col-xs-9">
                                    <?php
                                    echo $obj->inputAutoComplete(
                                        array(
                                            'objRefer' => $meetingPoint,
                                            'element' => array(
                                                'value' => 'address',
                                                'key' => 'hidMeetingPointKey'
                                            ),
                                            'source' => array(
                                                'url' => 'ajax-meeting-point.php',
                                                'data' => array('action' => 'searchData')
                                            )
                                        )
                                    );

                                    ?>
                                </div>
                            </div>
                            <div class="form-group online">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['media']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo  $obj->inputSelect('selOnlineChannel',$arrOnlineChannel);  ?>
                                </div>
                            </div>
                            <div class="form-group online">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['meetingLink']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo  $obj->inputTextArea('meetingLink', array('etc' => 'style="height:10em;"'));  ?>
                                </div>
                            </div>
							 
                        </div>
                    </div>
					
                    <div class="div-table-col">
						
                        <div class="div-tab-panel">
                           <div class="div-table-caption border-blue"><?php echo ucwords($obj->lang['participants']); ?></div>
							<div class="div-table  mnv-transaction transaction-detail" style="width:100%;">
								<div class="div-table-row"> 
									<div class="div-table-col detail-col-header"><?php echo ucwords($obj->lang['customer']); ?></div>
									<div class="div-table-col detail-col-header" style="width:250px;"><?php echo ucwords($obj->lang['businessCategory']); ?></div>
									<div class="div-table-col detail-col-header  icon-col <?php echo $obj->hideOnDisabled(); ?>"></div>
								</div>
								
								<?php
									$totalRows = count($rsDetail);
									for ($i = 0; $i <= $totalRows; $i++) {

										$class =  'transaction-detail-row';
										$overwrite = true;
										$etc = ''; 
										$showOptions = false; 

										if ($i == $totalRows) {
											$class = 'detail-row-template';
											$overwrite = false;
											$etc = 'disabled="disabled"'; 
										} else { 
											$_POST['hidDetailKey[]'] =  $rsDetail[$i]['pkey']; // save
											$_POST['hidCustomerKey[]'] =  $rsDetail[$i]['customerkey']; // save
											$_POST['customerName[]'] =  $rsDetail[$i]['customername']; // tdk disave
											$_POST['hidBusinessKey[]'] =  $rsDetail[$i]['businesscategorykey']; //save
											$_POST['businessName[]'] =  $rsDetail[$i]['businesscategoryname']; // tdk disave

										}
 
									?>
										<div class="div-table-row  <?php echo $class; ?>"> 
											<div class="div-table-col detail-col-detail">
												<?php echo $obj->inputText('customerName[]', array('overwritePost' => $overwrite, 'etc' => $etc,  'class' => 'form-control')); ?>
												<?php echo $obj->inputHidden('hidCustomerKey[]', array('overwritePost' => $overwrite, 'etc' => $etc)); ?>
												<?php echo $obj->inputHidden('hidDetailKey[]', array('overwritePost' => $overwrite, 'etc' => $etc)); ?> 
											</div>
											<div class="div-table-col detail-col-detail">
													<?php echo $obj->inputHidden('hidBusinessKey[]', array('overwritePost' => $overwrite, 'etc' => $etc)); ?>
													<?php echo $obj->inputText('businessName[]', array('readonly' => true, 'overwritePost' => $overwrite, 'etc' => $etc,  'class' => 'form-control mnv-barcode-input')); ?>
											</div>
											<div class="div-table-col detail-col-detail icon-col <?php echo $obj->hideOnDisabled(); ?>"><?php echo $obj->inputLinkButton('btnDeleteRows', '<i class="fas fa-times"></i>', array('etc' => 'tabIndex="-1"', 'class' => 'btn btn-link remove-button')); ?></div>
										</div>
 
									<?php  } ?>
							</div>
					 
						<div style="clear:both; height:1em;"></div>
						<div style="float:left; display:inline-block;"><?php echo $obj->inputButton('btnAddRows', $obj->lang['addRows'], array('class' => 'btn btn-primary btn-second-tone')); ?></div>

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
