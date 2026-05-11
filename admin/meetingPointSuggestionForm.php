<?php
require_once '../_config.php';
require_once '../_include-v2.php';

includeClass(array('Country.class.php','MeetingPointSuggestion.class.php','Customer.class.php'));
$meetingPointSuggestion = createObjAndAddToCol(new MeetingPointSuggestion());
$city = createObjAndAddToCol(new City());
$cityCategory = createObjAndAddToCol(new CityCategory());
$customer = createObjAndAddToCol(new Customer());
$country = new Country();

$obj = $meetingPointSuggestion;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
// some modules that have different security object from that of their class

if (!$security->isAdminLogin($securityObject, 10, true));

$formAction = 'meetingPointSuggestionList';

$isQuickAdd = (isset($_GET) && !empty($_GET['quickadd'])) ? true : false;

$rs = prepareOnLoadData($obj);

$rsDetail = array();
if (!empty($_GET['id'])) { // jika edit form 
    $id = $_GET['id'];
   
	
    if (!empty($rs[0]['citykey'])) {
		$rsCity = $city->searchData('city.pkey',$rs[0]['citykey'],true);
		$_POST['cityName'] = $rsCity[0]['name'] .', ' . $rsCity[0]['categoryname']; 
        $_POST['hidCityKey'] = $rs[0]['citykey'];
    }
    if (!empty($rs[0]['customerkey'])) {
        $rsCustomer = $customer->getDataRowById($rs[0]['customerkey']);
        $_POST['customerName'] = $rsCustomer[0]['code']. ' - '.$rsCustomer[0]['name'];
        $_POST['hidCustomerKey'] = $rs[0]['customerkey'];
    }
}

$arrCountry =  $country->generateComboboxOpt(null,array('criteria' =>' and ('.$country->tableName.'.statuskey = 1)'));  
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
            var meetingPointSuggestion = new MeetingPointSuggestion(tabID);
            prepareHandler(meetingPointSuggestion);

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
                customerName: {
                    validators: {
                        notEmpty: {
                            message: phpErrorMsg.customer[1]
                        },
                    }
                },
                address: {
                    validators: {
                        notEmpty: {
                            message: phpErrorMsg.address[1]
                        },
                    }
                }, 
                phone: {
                    validators: {
                        notEmpty: {
                            message: phpErrorMsg.phone[1]
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
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['customer']); ?></label>
                                <div class="col-xs-9">
                                    <?php
                                    echo $obj->inputAutoComplete(
                                        array( 
                                            'element' => array(
                                                'value' => 'customerName',
                                                'key' => 'hidCustomerKey'
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
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['name']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputText('name'); ?>

                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['address']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputTextArea('address', array('etc' => 'style="height:8em"'));  ?>

                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['country']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputSelect('selCountry',$arrCountry); ?>

                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['city']); ?></label>
                                <div class="col-xs-9">
                                    <?php
                                    echo $obj->inputAutoComplete(
                                        array(
                                            'objRefer' => $city,
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
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['description']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputTextArea('descriptionPoint', array('etc' => 'style="height:8em"')); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['phone']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputText('phone'); ?>
                                </div>
                            </div>
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